<?php

namespace Tests\Feature;

use App\Jobs\SendLeadNotification;
use App\Mail\NewLeadMail;
use App\Support\Leads\IpLocation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class LeadFormTest extends TestCase
{
    private const CHROME_ANDROID = 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Mobile Safari/537.36';

    private array $form = [
        'name' => 'Priya Mehta',
        'phone' => '91234 56789',
        'email' => 'priya@example.com',
        'interest' => 'Multi-vendor marketplace',
        'plan' => 'All in One',
        'message' => "We sell cakes in Nagpur.\nNeed a delivery app.",
    ];

    private function visitorJson(array $overrides = []): string
    {
        return json_encode($overrides + [
            'page' => 'https://10xcart.snehal.info/#contact',
            'time_on_page' => 194,
            'sections' => ['services', 'pricing', 'top', 'unknown', 'faq', 'contact'],
            'pricing_mode' => 'multi',
            'quote_plan' => 'All in One',
            'timezone' => 'Asia/Calcutta',
            'language' => 'en-IN',
            'screen' => '412x915',
            'device' => ['touch' => true, 'platform' => 'Android', 'platform_version' => '14.0.0', 'model' => 'SM-S911B'],
            'visits' => 3,
            'first_seen' => now()->subDays(5)->toIso8601String(),
            'first_touch' => ['at' => now()->subDays(5)->toIso8601String(), 'landing' => '/', 'referrer' => 'https://www.google.co.in/'],
            'last_touch' => ['at' => now()->subMinutes(4)->toIso8601String(), 'landing' => '/?utm_source=facebook', 'utm_source' => 'facebook', 'utm_medium' => 'cpc', 'utm_campaign' => 'diwali-sale', 'click' => 'fbclid'],
        ]);
    }

    private function lead(array $overrides = []): array
    {
        return $overrides + $this->form + [
            'ref' => 'L261017-TEST',
            'submitted_at' => now()->toIso8601String(),
            'ip' => '49.36.10.20',
            'visitor' => [],
        ];
    }

    public function test_submission_queues_email_with_cleaned_visitor_context(): void
    {
        Queue::fake();

        $this->withHeader('User-Agent', self::CHROME_ANDROID)
            ->postJson(route('lead.store'), $this->form + ['visitor' => $this->visitorJson()])
            ->assertOk();

        Queue::assertPushed(SendLeadNotification::class, function ($job) {
            $v = $job->lead['visitor'];

            return $job->lead['name'] === 'Priya Mehta'
                && str_starts_with($job->lead['ref'], 'L')
                && $v['device'] === 'Mobile'
                && $v['os'] === 'Android 14'          // from client hints, not the frozen "Android 10"
                && $v['model'] === 'SM-S911B'
                && $v['browser'] === 'Chrome 129'
                && $v['sections'] === ['Services', 'Pricing', 'FAQ', 'Contact form'] // unknown ids dropped
                && $v['last_touch']['source'] === 'facebook / cpc'
                && $v['first_touch']['source'] === 'Google search'
                && $v['screen'] === '412 × 915';
        });
    }

    public function test_bad_visitor_data_never_blocks_a_lead(): void
    {
        Queue::fake();

        $this->postJson(route('lead.store'), $this->form + ['visitor' => '{not json'])->assertOk();
        $this->postJson(route('lead.store'), $this->form + ['visitor' => json_encode([
            'page' => 'javascript:alert(1)',
            'timezone' => 'Mars/Olympus',
            'sections' => 'pricing',
            'first_seen' => '1999-01-01T00:00:00Z',
            'quote_plan' => '<script>',
            'last_touch' => ['referrer' => 'not a url', 'click' => 'evil'],
        ])])->assertOk();

        Queue::assertPushed(SendLeadNotification::class, 2);

        // Only the server-side User-Agent details survive; every junk value is dropped
        foreach (Queue::pushed(SendLeadNotification::class) as $job) {
            $this->assertSame([], array_diff(array_keys($job->lead['visitor']), ['device', 'os', 'browser', 'user_agent', 'language']));
        }
    }

    public function test_invalid_submission_is_rejected(): void
    {
        Queue::fake();

        $this->postJson(route('lead.store'), ['name' => ''])->assertUnprocessable();

        Queue::assertNothingPushed();
    }

    public function test_job_adds_location_and_mails_admins_with_cc(): void
    {
        Mail::fake();
        Http::fake(['ipapi.co/*' => Http::response([
            'city' => 'Nagpur', 'region' => 'Maharashtra', 'postal' => '440001', 'country_name' => 'India',
            'country_code' => 'IN', 'timezone' => 'Asia/Kolkata', 'latitude' => 21.1458, 'longitude' => 79.0882,
            'org' => 'Reliance Jio Infocomm Limited', 'asn' => 'AS55836',
        ])]);

        app()->call([new SendLeadNotification($this->lead()), 'handle']);

        Mail::assertSent(NewLeadMail::class, function (NewLeadMail $mail) {
            return $mail->hasTo('shreyadiwivedi@gmail.com')
                && $mail->hasTo('shreya@snehal.info')
                && $mail->hasCc('sr.snehal369@gmail.com')
                && $mail->hasReplyTo('priya@example.com')
                && $mail->lead['location']['city'] === 'Nagpur'
                && $mail->envelope()->subject === 'New demo request: Multi-vendor marketplace – Priya Mehta, Nagpur';
        });
    }

    public function test_ip_location_falls_back_to_second_provider_and_skips_private_ips(): void
    {
        Http::fake([
            'ipapi.co/*' => Http::response(['error' => true, 'reason' => 'RateLimited'], 429),
            'ipwho.is/*' => Http::response([
                'success' => true, 'city' => 'Pune', 'region' => 'Maharashtra', 'country' => 'India', 'country_code' => 'IN',
                'timezone' => ['id' => 'Asia/Kolkata'], 'connection' => ['asn' => 24560, 'isp' => 'Bharti Airtel'],
            ]),
        ]);
        $locator = app(IpLocation::class);

        $this->assertSame(['status' => 'private'], $locator->lookup('127.0.0.1'));
        $this->assertSame(['status' => 'private'], $locator->lookup('192.168.1.5'));

        $location = $locator->lookup('49.36.10.21');
        $this->assertSame('ok', $location['status']);
        $this->assertSame('ipwhois', $location['provider']);
        $this->assertSame('Pune', $location['city']);
        $this->assertSame('AS24560', $location['asn']);
    }

    public function test_built_email_embeds_logo_and_has_text_part(): void
    {
        config(['mail.default' => 'array']);

        Mail::to('admin@example.com')->send(new NewLeadMail($this->lead() + ['location' => ['status' => 'private']]));

        $sent = app('mail.manager')->mailer('array')->getSymfonyTransport()->messages()->first()->getOriginalMessage();
        $this->assertStringContainsString('src="cid:', $sent->getHtmlBody());
        $this->assertCount(1, $sent->getAttachments()); // the inline logo
        $this->assertStringContainsString('NEW DEMO REQUEST', $sent->getTextBody());
        $this->assertStringContainsString('Local or private network', $sent->getTextBody());
    }

    public function test_email_renders_all_sections_in_html_and_text(): void
    {
        $this->withHeader('User-Agent', self::CHROME_ANDROID);
        Queue::fake();
        $this->postJson(route('lead.store'), $this->form + ['visitor' => $this->visitorJson()]);
        $lead = Queue::pushed(SendLeadNotification::class)->first()->lead + ['location' => [
            'status' => 'ok', 'city' => 'Nagpur', 'region' => 'Maharashtra', 'country' => 'India', 'country_code' => 'IN',
            'timezone' => 'America/New_York', 'latitude' => 21.14, 'longitude' => 79.08, 'network' => 'Jio', 'asn' => 'AS55836',
        ]];

        $mail = new NewLeadMail($lead);
        $html = $mail->render();
        $text = $mail->summary->toText();

        foreach (['Priya Mehta', 'Nagpur, Maharashtra, India', 'How they found you', 'facebook / cpc', 'diwali-sale',
            'Services → Pricing → FAQ → Contact form', 'Multi-vendor plans', '3 min 14 sec', 'SM-S911B',
            'https://wa.me/919123456789', 'tel:+919123456789', 'may be using a VPN'] as $expected) {
            $this->assertStringContainsString(e($expected), $html, "HTML is missing: {$expected}");
        }

        $this->assertStringContainsString('We sell cakes in Nagpur.<br />', $html);
        $this->assertStringContainsString('LOCATION (Approximate, based on the IP address)', $text);
        $this->assertStringContainsString('Source: facebook / cpc', $text);
        $this->assertStringNotContainsString('&amp;', $text);
    }
}
