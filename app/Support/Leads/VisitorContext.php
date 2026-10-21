<?php

namespace App\Support\Leads;

use App\Http\Requests\StoreLeadRequest;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Throwable;

/**
 * What we know about the visitor who sent the contact form: device (from the
 * User-Agent) plus the "visitor" JSON that public/assets/js/10xcart.js sends:
 * traffic source, sections viewed, time on page, timezone, screen size …
 *
 * Browser data is untrusted, so only known keys are kept and every value is
 * type-checked and length-limited. Bad data is dropped, never rejected.
 */
final class VisitorContext
{
    /** Home page sections (element id => name shown in the email) */
    public const SECTIONS = [
        'services' => 'Services',
        'solutions' => 'E-commerce platform',
        'process' => 'How it works',
        'why' => 'Why 10xCart',
        'features' => 'Features',
        'admin' => 'Admin panel',
        'showcase' => 'App designs',
        'pricing' => 'Pricing',
        'faq' => 'FAQ',
        'contact' => 'Contact form',
    ];

    /** Ad-click URL parameters (Google, Facebook/Instagram, Microsoft) */
    private const CLICK_IDS = ['gclid', 'gbraid', 'wbraid', 'fbclid', 'msclkid'];

    public static function fromRequest(Request $request): array
    {
        $json = (string) $request->input('visitor', '');
        $client = strlen($json) <= 10000 ? json_decode($json, true) : null;
        $client = is_array($client) ? $client : [];
        $userAgent = (string) $request->userAgent();

        return self::filled([
            ...UserAgent::parse($userAgent, self::deviceHints($client['device'] ?? null)),
            'user_agent' => Str::limit($userAgent, 300, ''),
            'language' => self::text($client['language'] ?? null, 20)
                ?? self::text(Str::before(Str::before((string) $request->header('Accept-Language'), ','), ';'), 20),
            'timezone' => self::timezone($client['timezone'] ?? null),
            'screen' => preg_match('/^(\d{2,5})x(\d{2,5})$/', (string) ($client['screen'] ?? ''), $m) ? "{$m[1]} × {$m[2]}" : null,
            'page' => self::url($client['page'] ?? null),
            'time_on_page' => self::int($client['time_on_page'] ?? null),
            'sections' => self::sections($client['sections'] ?? null),
            'pricing_tab' => match ($client['pricing_mode'] ?? null) {
                'single' => 'Single vendor',
                'multi' => 'Multi-vendor',
                default => null,
            },
            'quote_plan' => in_array($client['quote_plan'] ?? null, StoreLeadRequest::PLANS, true) ? $client['quote_plan'] : null,
            'visits' => self::int($client['visits'] ?? null),
            'first_seen' => self::date($client['first_seen'] ?? null),
            'first_touch' => self::touch($client['first_touch'] ?? null),
            'last_touch' => self::touch($client['last_touch'] ?? null),
        ]);
    }

    /** One visit's traffic source: referrer, UTM tags, ad click id and landing page */
    private static function touch(mixed $touch): ?array
    {
        if (! is_array($touch)) {
            return null;
        }

        $clean = self::filled([
            'at' => self::date($touch['at'] ?? null),
            'landing' => self::text($touch['landing'] ?? null, 200),
            'referrer' => self::url($touch['referrer'] ?? null),
            'utm_source' => self::text($touch['utm_source'] ?? null, 100),
            'utm_medium' => self::text($touch['utm_medium'] ?? null, 100),
            'utm_campaign' => self::text($touch['utm_campaign'] ?? null, 100),
            'utm_term' => self::text($touch['utm_term'] ?? null, 100),
            'utm_content' => self::text($touch['utm_content'] ?? null, 100),
            'click' => in_array($touch['click'] ?? null, self::CLICK_IDS, true) ? $touch['click'] : null,
        ]);

        return $clean ? $clean + ['source' => TrafficSource::describe($clean)] : null;
    }

    /** Touch screen flag + Chromium client hints (real OS version, phone model) */
    private static function deviceHints(mixed $device): array
    {
        $device = is_array($device) ? $device : [];

        return [
            'touch' => ($device['touch'] ?? false) === true,
            'platform' => self::text($device['platform'] ?? null, 20),
            'platform_version' => preg_match('/^\d{1,3}(\.\d{1,5}){0,3}$/', (string) ($device['platform_version'] ?? '')) ? $device['platform_version'] : null,
            'model' => preg_match('/^[\w\s\-+().,]{1,40}$/u', (string) ($device['model'] ?? '')) ? trim($device['model']) : null,
        ];
    }

    /** Section ids → names, in viewing order, known sections only */
    private static function sections(mixed $ids): array
    {
        if (! is_array($ids)) {
            return [];
        }

        $names = array_map(fn ($id) => is_string($id) ? (self::SECTIONS[$id] ?? null) : null, array_slice($ids, 0, 30));

        return array_values(array_unique(array_filter($names)));
    }

    private static function text(mixed $value, int $max): ?string
    {
        if (! is_string($value) && ! is_int($value)) {
            return null;
        }

        $value = trim(preg_replace('/[\x00-\x1F\x7F]+/u', ' ', (string) $value) ?? '');

        return $value === '' ? null : Str::limit($value, $max, '…');
    }

    private static function url(mixed $value): ?string
    {
        if (! is_string($value) || strlen($value) > 2000 || ! preg_match('~^https?://~i', $value) || ! filter_var($value, FILTER_VALIDATE_URL)) {
            return null;
        }

        return Str::limit($value, 300, '…');
    }

    private static function int(mixed $value, int $max = 1_000_000): ?int
    {
        return is_numeric($value) && $value >= 0 ? (int) min($value, $max) : null;
    }

    /** ISO date from the browser; impossible dates (future, > 2 years old) are ignored */
    private static function date(mixed $value): ?string
    {
        if (! is_string($value) || ! preg_match('/^\d{4}-\d{2}-\d{2}T[\d:.]+(Z|[+-]\d{2}:?\d{2})?$/', $value)) {
            return null;
        }

        try {
            $date = Carbon::parse($value);
        } catch (Throwable) {
            return null;
        }

        return $date->between(now()->subYears(2), now()->addDay()) ? $date->toIso8601String() : null;
    }

    private static function timezone(mixed $value): ?string
    {
        // ALL_WITH_BC also accepts old names browsers still send, e.g. "Asia/Calcutta"
        return is_string($value) && in_array($value, DateTimeZone::listIdentifiers(DateTimeZone::ALL_WITH_BC), true) ? $value : null;
    }

    /** Drop empty values so the email only shows rows we actually know */
    private static function filled(array $values): array
    {
        return array_filter($values, fn ($value) => $value !== null && $value !== '' && $value !== []);
    }
}
