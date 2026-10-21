<?php

namespace App\Support\Leads;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Turns a lead (form fields + visitor context + IP location) into what the
 * admin email shows: headline, action buttons and titled sections of
 * label/value rows. Both the HTML and the plain-text email use this class,
 * so they always contain the same information.
 *
 * To add a detail to the email: add a row to one of the *Rows() methods.
 * Rows with an empty value are skipped automatically.
 */
final class LeadPresenter
{
    private CarbonInterface $submittedAt;

    private array $visitor;

    private array $location;

    public function __construct(private array $lead)
    {
        $this->submittedAt = Carbon::parse($lead['submitted_at'] ?? 'now');
        $this->visitor = $lead['visitor'] ?? [];
        $this->location = $lead['location'] ?? ['status' => 'unavailable'];
    }

    // ---- Headline ---------------------------------------------------------

    public function subject(): string
    {
        $city = $this->location['city'] ?? null;

        return 'New demo request: '.$this->lead['interest'].' – '.$this->lead['name'].($city ? ', '.$city : '');
    }

    /** Inbox preview line shown after the subject */
    public function preheader(): string
    {
        return implode(' · ', array_filter([
            $this->lead['phone'],
            $this->place(),
            $this->visitor['last_touch']['source'] ?? null,
        ]));
    }

    public function ref(): string
    {
        return $this->lead['ref'] ?? '';
    }

    public function name(): string
    {
        return $this->lead['name'];
    }

    /** Chips under the name: what they want and which plan */
    public function tags(): array
    {
        return array_values(array_filter([$this->lead['interest'], $this->lead['plan'] ?? null]));
    }

    /** "Nagpur, Maharashtra, India" */
    public function place(): ?string
    {
        if (($this->location['status'] ?? null) !== 'ok') {
            return null;
        }

        $parts = array_unique(array_filter([
            $this->location['city'] ?? null,
            $this->location['region'] ?? null,
            $this->location['country'] ?? null,
        ]));

        return $parts ? implode(', ', $parts) : null;
    }

    public function receivedAt(): string
    {
        return $this->time($this->submittedAt);
    }

    /** Buttons under the headline; the first one is the main action */
    public function actions(): array
    {
        $phone = $this->phoneDigits();
        $email = $this->lead['email'] ?? null;

        return array_values(array_filter([
            ['label' => 'Call '.$this->lead['phone'], 'url' => 'tel:+'.$phone],
            ['label' => 'WhatsApp', 'url' => 'https://wa.me/'.$phone],
            $email ? ['label' => 'Reply by email', 'url' => 'mailto:'.$email.'?subject='.rawurlencode('Your '.config('site.name').' demo request')] : null,
        ]));
    }

    public function message(): ?string
    {
        return ($this->lead['message'] ?? null) ?: null;
    }

    public function footer(): string
    {
        $site = parse_url((string) config('app.url'), PHP_URL_HOST) ?: config('site.name');
        $reply = empty($this->lead['email'])
            ? 'No email was given, so call or WhatsApp the visitor.'
            : 'Reply to this email to answer '.Str::before($this->lead['name'], ' ').' directly.';

        return "Lead {$this->ref()} · Sent automatically by the contact form on {$site}. {$reply}";
    }

    // ---- Sections -----------------------------------------------------------

    /** @return list<array{label: string, value: string, url: ?string, hint: ?string}> */
    public function contactRows(): array
    {
        $email = $this->lead['email'] ?? null;

        return $this->rows([
            ['Phone', $this->lead['phone'], 'tel:+'.$this->phoneDigits()],
            ['Email', $email, $email ? 'mailto:'.$email : null],
            ['Interested in', $this->lead['interest']],
            ['Plan', ($this->lead['plan'] ?? null) ?: 'Help me choose'],
        ]);
    }

    /** @return list<array{title: string, note: ?string, rows: list<array>}> */
    public function sections(): array
    {
        return array_values(array_filter([
            $this->section('Location', 'Approximate, based on the IP address', $this->locationRows()),
            $this->section('How they found you', null, $this->sourceRows()),
            $this->section('On the website', null, $this->activityRows()),
            $this->section('Device', null, $this->deviceRows()),
        ]));
    }

    private function locationRows(): array
    {
        $l = $this->location;
        $ip = $this->lead['ip'] ?? null;

        if (($l['status'] ?? null) !== 'ok') {
            $why = ($l['status'] ?? null) === 'private'
                ? 'Local or private network, so no location is available'
                : 'The location service did not respond';

            return $this->rows([['IP address', $ip, null, $why]]);
        }

        $hasMap = isset($l['latitude'], $l['longitude']);
        $area = implode(', ', array_unique(array_filter([$l['city'] ?? null, $l['region'] ?? null])));

        return $this->rows([
            ['City / region', trim($area.' '.($l['postal'] ?? ''))],
            ['Country', isset($l['country']) ? $l['country'].(isset($l['country_code']) ? " ({$l['country_code']})" : '') : null],
            ['Timezone', $l['timezone'] ?? null, null, $this->timezoneMismatch()],
            ['Network', implode(' · ', array_filter([$l['network'] ?? null, $l['asn'] ?? null]))],
            ['Map', $hasMap ? 'View approximate area' : null, $hasMap ? 'https://www.google.com/maps?q='.(float) $l['latitude'].','.(float) $l['longitude'] : null],
            ['IP address', $ip],
        ]);
    }

    private function sourceRows(): array
    {
        $v = $this->visitor;
        $last = $v['last_touch'] ?? [];
        $first = $v['first_touch'] ?? [];
        $returning = ($v['visits'] ?? 1) > 1;
        $firstSource = $first['source'] ?? null;

        return $this->rows([
            ['Source', $last['source'] ?? null, null, ($last['source'] ?? null) === 'Direct'
                ? 'Typed the address, used a bookmark, or came from an app that hides where links come from'
                : null],
            ['Campaign', $last['utm_campaign'] ?? null],
            ['Keyword', $last['utm_term'] ?? null],
            ['Ad / content', $last['utm_content'] ?? null],
            ['Referrer', $last['referrer'] ?? null], // plain text: never link to visitor-supplied URLs
            ['Landing page', isset($last['landing']) ? $this->pageName($last['landing']) : null],
            ['Visits', isset($v['visits']) ? ($returning ? $v['visits'].' visits (returning visitor)' : 'First visit') : null],
            ['First visit', $returning && isset($v['first_seen']) ? $this->time($v['first_seen']).' ('.$this->before($v['first_seen']).')' : null, null,
                $returning && $firstSource && $firstSource !== ($last['source'] ?? null) ? 'First came from: '.$firstSource : null],
        ]);
    }

    private function activityRows(): array
    {
        $v = $this->visitor;
        $sections = $v['sections'] ?? [];
        $seconds = $v['time_on_page'] ?? null;

        return $this->rows([
            ['Time on page', $seconds !== null ? $this->duration($seconds) : null, null,
                $seconds !== null && $seconds < 10 ? 'Very quick: could be autofill or a bot' : null],
            ['Sections viewed', $sections ? implode(' → ', $sections) : null],
            ['Pricing viewed', in_array('Pricing', $sections, true) ? ($v['pricing_tab'] ?? 'Yes').' plans' : null],
            ['Clicked "Get a quote"', $v['quote_plan'] ?? null],
            ['Submitted from', isset($v['page']) ? $this->withoutQuery($v['page']) : null],
        ]);
    }

    private function deviceRows(): array
    {
        $v = $this->visitor;
        $tz = $v['timezone'] ?? null;

        return $this->rows([
            ['Device', implode(' · ', array_filter([$v['device'] ?? null, $v['model'] ?? null]))],
            ['Operating system', $v['os'] ?? null],
            ['Browser', $v['browser'] ?? null],
            ['Screen', $v['screen'] ?? null],
            ['Language', $v['language'] ?? null],
            ['Visitor\'s local time', $tz ? $this->submittedAt->copy()->setTimezone($tz)->format('g:i A, D j M') : null, null, $tz],
        ]);
    }

    // ---- Plain-text version ---------------------------------------------------

    public function toText(): string
    {
        $lines = [
            'NEW DEMO REQUEST · '.$this->ref(),
            $this->name().' · '.implode(' · ', $this->tags()),
            implode(' · ', array_filter([$this->place(), $this->receivedAt()])),
            '',
        ];

        foreach ($this->actions() as $action) {
            $lines[] = $action['label'].': '.$action['url'];
        }

        array_push($lines, '', ...$this->textBlock(['title' => 'Contact details', 'note' => null, 'rows' => $this->contactRows()]));

        if ($message = $this->message()) {
            array_push($lines, '', 'MESSAGE', $message);
        }

        foreach ($this->sections() as $section) {
            array_push($lines, '', ...$this->textBlock($section));
        }

        array_push($lines, '', '-- ', $this->footer());

        return implode("\n", $lines);
    }

    private function textBlock(array $block): array
    {
        $lines = [Str::upper($block['title']).($block['note'] ? " ({$block['note']})" : '')];

        foreach ($block['rows'] as $row) {
            $link = str_starts_with((string) $row['url'], 'http') ? " <{$row['url']}>" : '';
            $hint = $row['hint'] ? " — {$row['hint']}" : '';
            $lines[] = "{$row['label']}: {$row['value']}{$link}{$hint}";
        }

        return $lines;
    }

    // ---- Helpers ---------------------------------------------------------------

    private function section(string $title, ?string $note, array $rows): ?array
    {
        return $rows ? ['title' => $title, 'note' => $note, 'rows' => $rows] : null;
    }

    /** [label, value, url?, hint?] → row; rows without a value are dropped */
    private function rows(array $rows): array
    {
        $rows = array_map(fn (array $r) => filled($r[1] ?? null)
            ? ['label' => $r[0], 'value' => (string) $r[1], 'url' => $r[2] ?? null, 'hint' => $r[3] ?? null]
            : null, $rows);

        return array_values(array_filter($rows));
    }

    /** Phone as international digits for tel: / WhatsApp; 10-digit numbers are taken as Indian (+91) */
    private function phoneDigits(): string
    {
        $digits = preg_replace('/\D/', '', $this->lead['phone']);

        return match (true) {
            strlen($digits) === 10 => '91'.$digits,
            strlen($digits) === 11 && str_starts_with($digits, '0') => '91'.substr($digits, 1),
            default => $digits,
        };
    }

    /** "Sat, 17 Oct 2026, 1:07 PM IST" in the site's timezone */
    private function time(CarbonInterface|string $time): string
    {
        return Carbon::parse($time)->setTimezone(config('site.timezone'))->format('D, j M Y, g:i A T');
    }

    /** "5 days before this request" */
    private function before(string $time): string
    {
        return Carbon::parse($time)->diffForHumans($this->submittedAt, [
            'syntax' => CarbonInterface::DIFF_ABSOLUTE,
            'parts' => 1,
        ]).' before this request';
    }

    /** URL or path without "?utm_…" / "#…" (campaign tags have their own rows) */
    private function withoutQuery(string $url): string
    {
        return Str::before(Str::before($url, '#'), '?') ?: '/';
    }

    /** "/" → "Home page", other paths as they are */
    private function pageName(string $path): string
    {
        $path = $this->withoutQuery($path);

        return $path === '/' ? 'Home page' : $path;
    }

    private function duration(int $seconds): string
    {
        return match (true) {
            $seconds < 60 => "{$seconds} sec",
            $seconds < 3600 => intdiv($seconds, 60).' min '.($seconds % 60).' sec',
            default => intdiv($seconds, 3600).' h '.intdiv($seconds % 3600, 60).' min',
        };
    }

    /** Browser timezone vs. IP timezone: different UTC offsets hint at a VPN or travel */
    private function timezoneMismatch(): ?string
    {
        $browser = $this->visitor['timezone'] ?? null;
        $ip = $this->location['timezone'] ?? null;

        if (! $browser || ! $ip) {
            return null;
        }

        $offset = fn (string $tz) => rescue(fn () => $this->submittedAt->copy()->setTimezone($tz)->utcOffset(), null, false);

        return $offset($browser) !== $offset($ip)
            ? "The browser is set to {$browser}, so the visitor may be using a VPN or travelling"
            : null;
    }
}
