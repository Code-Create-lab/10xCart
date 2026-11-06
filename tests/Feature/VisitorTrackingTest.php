<?php

namespace Tests\Feature;

use App\Jobs\LocateVisit;
use App\Models\Lead;
use App\Models\Visit;
use App\Models\Visitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class VisitorTrackingTest extends TestCase
{
    use RefreshDatabase;

    private const CHROME_ANDROID = 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Mobile Safari/537.36';

    private const LEAD_FORM = [
        'name' => 'Priya Mehta',
        'phone' => '91234 56789',
        'interest' => 'Multi-vendor marketplace',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    private function batch(array $overrides = []): array
    {
        return $overrides + [
            'visit' => 'abc12345visit',
            'touch' => ['landing' => '/?utm_source=facebook', 'utm_source' => 'facebook', 'utm_medium' => 'cpc', 'utm_campaign' => 'diwali'],
            'device' => ['touch' => true, 'platform' => 'Android', 'platform_version' => '14.0.0', 'model' => 'SM-S911B'],
            'timezone' => 'Asia/Kolkata',
            'screen' => '412x915',
            'traits' => ['viewport' => '412x800', 'color_scheme' => 'dark', 'connection' => '4g', 'cores' => 8],
            'events' => [
                ['type' => 'page_view', 'name' => '10xCart', 'path' => '/'],
                ['type' => 'section', 'name' => 'pricing', 'path' => '/'],
                ['type' => 'click', 'name' => 'Get a quote: All in One', 'path' => '/'],
                ['type' => 'evil', 'name' => 'dropped'],
            ],
            'engaged' => 42,
            'scroll' => 63,
        ];
    }

    private function consented(): static
    {
        return $this->withCredentials()->withUnencryptedCookie('10x_consent', 'granted')->withHeader('User-Agent', self::CHROME_ANDROID);
    }

    public function test_nothing_is_stored_without_cookie_consent(): void
    {
        $this->withCredentials()->withUnencryptedCookie('10x_consent', 'denied')
            ->postJson(route('track'), $this->batch())
            ->assertNoContent()
            ->assertCookieMissing('10x_vid');

        $this->assertDatabaseCount('visitors', 0);
        $this->assertDatabaseCount('visits', 0);
    }

    public function test_consented_batch_records_visitor_visit_and_known_events(): void
    {
        $response = $this->consented()->postJson(route('track'), $this->batch())->assertNoContent();

        $visitor = Visitor::sole();
        $response->assertCookie('10x_vid', $visitor->uuid);

        $this->assertSame('Mobile', $visitor->device);
        $this->assertSame('Android 14', $visitor->os);
        $this->assertSame('facebook / cpc', $visitor->first_source);
        $this->assertSame(['Viewport' => '412 × 800', 'Colour scheme' => 'Dark', 'Connection' => '4G', 'CPU cores' => 8], $visitor->traits);
        $this->assertSame(1, $visitor->visits_count);
        $this->assertSame(1, $visitor->page_views_count);
        $this->assertSame(42, $visitor->engaged_seconds);

        $visit = Visit::sole();
        $this->assertSame('diwali', $visit->utm_campaign);
        $this->assertSame(63, $visit->max_scroll);
        $this->assertSame(
            [['page_view', '10xCart'], ['section', 'Pricing'], ['click', 'Get a quote: All in One']],
            $visit->events()->orderBy('id')->get()->map(fn ($event) => [$event->type, $event->name])->all(),
        );
        Queue::assertPushed(LocateVisit::class, fn (LocateVisit $job) => $job->visit->is($visit));
    }

    public function test_later_batches_continue_the_same_visitor_and_visit(): void
    {
        $this->consented()->postJson(route('track'), $this->batch());
        $uuid = Visitor::sole()->uuid;

        $this->consented()->withCookie('10x_vid', $uuid)
            ->postJson(route('track'), $this->batch(['events' => [['type' => 'page_view']], 'engaged' => 99999, 'scroll' => 20]))
            ->assertNoContent();

        $visitor = Visitor::sole();
        $visit = Visit::sole();
        $this->assertSame(1, $visitor->visits_count);
        $this->assertSame(2, $visitor->page_views_count);
        $this->assertSame(42 + 900, $visit->engaged_seconds); // one batch is capped at 15 minutes
        $this->assertSame(63, $visit->max_scroll);              // keeps the deepest scroll

        $this->consented()->withCookie('10x_vid', $uuid)
            ->postJson(route('track'), $this->batch(['visit' => 'secondvisit1', 'touch' => ['referrer' => 'https://www.google.com/']]));

        $this->assertSame(2, $visitor->fresh()->visits_count);
        $this->assertSame('Google search', $visitor->fresh()->last_source);
        $this->assertSame('facebook / cpc', $visitor->fresh()->first_source);
    }

    public function test_first_batch_without_scroll_or_engaged_time_is_recorded(): void
    {
        $batch = $this->batch(['events' => [['type' => 'page_view'], ['type' => 'section', 'name' => 'top']]]);
        unset($batch['scroll'], $batch['engaged']);

        $this->consented()->postJson(route('track'), $batch)->assertNoContent()->assertCookie('10x_vid');

        $visit = Visit::sole();
        $this->assertSame(0, $visit->max_scroll);
        $this->assertSame(1, $visit->page_views);
        $this->assertSame(['page_view'], $visit->events()->pluck('type')->all()); // unknown section ids are dropped
    }

    public function test_bots_and_batches_without_a_visit_key_are_ignored(): void
    {
        $this->consented()->withHeader('User-Agent', 'Googlebot/2.1 (+http://www.google.com/bot.html)')
            ->postJson(route('track'), $this->batch())
            ->assertNoContent();
        $this->consented()->postJson(route('track'), $this->batch(['visit' => 'bad key!']))->assertNoContent();

        $this->assertDatabaseCount('visitors', 0);
    }

    public function test_locate_visit_copies_ip_location_to_the_visitor(): void
    {
        Http::fake(['ipapi.co/*' => Http::response([
            'city' => 'Nagpur', 'region' => 'Maharashtra', 'country_name' => 'India', 'country_code' => 'IN',
            'org' => 'Reliance Jio Infocomm Limited',
        ])]);
        $visit = Visit::factory()->create(['ip' => '49.36.10.20']);

        app()->call([new LocateVisit($visit), 'handle']);

        $this->assertSame('Nagpur', $visit->fresh()->location['city']);
        $this->assertSame('Nagpur, Maharashtra, India', $visit->visitor->fresh()->place());
        $this->assertSame('Reliance Jio Infocomm Limited', $visit->visitor->fresh()->network);
    }

    public function test_lead_is_saved_and_linked_to_the_visitor_only_with_consent(): void
    {
        $visitor = Visitor::factory()->create();

        $this->withCredentials()->withUnencryptedCookie('10x_consent', 'granted')->withCookie('10x_vid', $visitor->uuid)
            ->postJson(route('lead.store'), self::LEAD_FORM)->assertOk();
        $this->withUnencryptedCookie('10x_consent', 'denied')->withCookie('10x_vid', $visitor->uuid)
            ->postJson(route('lead.store'), self::LEAD_FORM)->assertOk();

        $this->assertSame([$visitor->id, null], Lead::orderBy('id')->pluck('visitor_id')->all());
    }

    public function test_inactive_visitors_are_pruned_with_their_visits_but_leads_are_kept(): void
    {
        $old = Visitor::factory()->create(['last_seen_at' => now()->subDays(400)]);
        Visit::factory()->for($old)->create();
        $lead = Lead::factory()->for($old)->create();
        $recent = Visitor::factory()->create(['last_seen_at' => now()->subDays(10)]);

        $this->artisan('model:prune', ['--model' => [Visitor::class]])->assertSuccessful();

        $this->assertModelMissing($old);
        $this->assertModelExists($recent);
        $this->assertDatabaseCount('visits', 0);
        $this->assertNull($lead->fresh()->visitor_id);
    }

    public function test_google_consent_defaults_follow_the_consent_cookie(): void
    {
        config(['site.ga_id' => 'G-TEST123']);

        $this->get('/')->assertSee("analytics_storage: 'denied'", false)->assertSee('id="cookie-consent"', false);
        $this->withUnencryptedCookie('10x_consent', 'granted')->get('/')->assertSee("analytics_storage: 'granted'", false);
    }
}
