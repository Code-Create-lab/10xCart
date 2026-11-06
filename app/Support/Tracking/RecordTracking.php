<?php

namespace App\Support\Tracking;

use App\Jobs\LocateVisit;
use App\Models\Visit;
use App\Models\Visitor;
use App\Models\VisitorEvent;
use App\Support\Leads\VisitorContext;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Saves one batch from the browser tracker (public/assets/js/10xcart.js → "Visitor tracking"):
 * updates the visitor's device details, opens or continues the visit and stores its events.
 *
 * The batch is untrusted: unknown keys and event types are ignored, every value is
 * cleaned by VisitorContext and length-limited, counters are capped.
 */
final class RecordTracking
{
    /** Most events kept from one batch */
    private const MAX_EVENTS = 50;

    /** Most engaged seconds credited by one batch (the tracker sends at least every 15 s while active) */
    private const MAX_ENGAGED_SECONDS = 900;

    /**
     * @param  array<string, mixed>  $batch
     * @return Visitor|null null when nothing was recorded (bot or missing visit key)
     */
    public function handle(Request $request, ?string $visitorUuid, array $batch): ?Visitor
    {
        $visitKey = is_string($batch['visit'] ?? null) && preg_match('/^[A-Za-z0-9]{8,40}$/', $batch['visit']) ? $batch['visit'] : null;
        $context = VisitorContext::fromData([
            'device' => $batch['device'] ?? null,
            'timezone' => $batch['timezone'] ?? null,
            'language' => $batch['language'] ?? null,
            'screen' => $batch['screen'] ?? null,
            'last_touch' => $batch['touch'] ?? null,
        ], $request);

        if (! $visitKey || $context['device'] === 'Bot / automated') {
            return null;
        }

        $visitor = $this->visitor($visitorUuid, $request, $context, $batch['traits'] ?? null);
        $visit = $this->visit($visitor, $visitKey, $request, $context['last_touch'] ?? []);

        $events = $this->events($batch['events'] ?? null);
        $pageViews = count(array_filter($events, fn (array $event) => $event['type'] === 'page_view'));
        $engaged = VisitorContext::int($batch['engaged'] ?? null, self::MAX_ENGAGED_SECONDS) ?? 0;

        $visit->events()->createMany($events);
        $visit->forceFill([
            'last_activity_at' => now(),
            'page_views' => $visit->page_views + $pageViews,
            'engaged_seconds' => $visit->engaged_seconds + $engaged,
            'max_scroll' => max($visit->max_scroll, VisitorContext::int($batch['scroll'] ?? null, 100) ?? 0),
        ])->save();

        $visitor->forceFill([
            'page_views_count' => $visitor->page_views_count + $pageViews,
            'engaged_seconds' => $visitor->engaged_seconds + $engaged,
        ])->save();

        return $visitor;
    }

    private function visitor(?string $uuid, Request $request, array $context, mixed $traits): Visitor
    {
        $visitor = ($uuid ? Visitor::firstWhere('uuid', $uuid) : null)
            ?? new Visitor(['uuid' => (string) Str::uuid(), 'first_seen_at' => now()]);

        $visitor->fill(array_filter([
            'last_seen_at' => now(),
            'last_ip' => $request->ip(),
            'device' => $context['device'] ?? null,
            'os' => $context['os'] ?? null,
            'browser' => $context['browser'] ?? null,
            'model' => $context['model'] ?? null,
            'screen' => $context['screen'] ?? null,
            'language' => $context['language'] ?? null,
            'timezone' => $context['timezone'] ?? null,
            'user_agent' => $context['user_agent'] ?? null,
            'traits' => $this->traits($traits) ?: null,
        ], fn ($value) => $value !== null))->save();

        return $visitor;
    }

    private function visit(Visitor $visitor, string $key, Request $request, array $touch): Visit
    {
        if ($visit = $visitor->visits()->firstWhere('key', $key)) {
            return $visit;
        }

        $visit = $visitor->visits()->create([
            'key' => $key,
            'started_at' => now(),
            'last_activity_at' => now(),
            'ip' => $request->ip(),
            'source' => $touch['source'] ?? 'Direct',
            'referrer' => $touch['referrer'] ?? null,
            'landing' => $touch['landing'] ?? null,
            'utm_source' => $touch['utm_source'] ?? null,
            'utm_medium' => $touch['utm_medium'] ?? null,
            'utm_campaign' => $touch['utm_campaign'] ?? null,
            'utm_term' => $touch['utm_term'] ?? null,
            'utm_content' => $touch['utm_content'] ?? null,
            'click_id' => $touch['click'] ?? null,
        ]);

        $visitor->forceFill([
            'visits_count' => $visitor->visits_count + 1,
            'first_source' => $visitor->first_source ?? $visit->source,
            'last_source' => $visit->source,
        ])->save();

        LocateVisit::dispatch($visit);

        return $visit;
    }

    /**
     * @return list<array{type: string, name: ?string, path: ?string, created_at: Carbon}>
     */
    private function events(mixed $events): array
    {
        if (! is_array($events)) {
            return [];
        }

        $clean = [];

        foreach (array_slice($events, 0, self::MAX_EVENTS) as $event) {
            $type = is_array($event) ? ($event['type'] ?? null) : null;

            if (! is_string($type) || ! array_key_exists($type, VisitorEvent::TYPES)) {
                continue;
            }

            $name = VisitorContext::text($event['name'] ?? null, 100);

            $clean[] = [
                'type' => $type,
                'name' => $type === 'section' && $name !== null ? (VisitorContext::SECTIONS[$name] ?? $name) : $name,
                'path' => VisitorContext::text($event['path'] ?? null, 200),
                'created_at' => now(),
            ];
        }

        return $clean;
    }

    /**
     * Extra browser details shown on the visitor page.
     *
     * @return array<string, string|int>
     */
    private function traits(mixed $traits): array
    {
        if (! is_array($traits)) {
            return [];
        }

        $languages = is_array($traits['languages'] ?? null)
            ? implode(', ', array_filter(array_slice($traits['languages'], 0, 5), 'is_string'))
            : null;

        return array_filter([
            'Viewport' => preg_match('/^(\d{2,5})x(\d{2,5})$/', (string) ($traits['viewport'] ?? ''), $m) ? "{$m[1]} × {$m[2]}" : null,
            'Pixel ratio' => is_numeric($traits['pixel_ratio'] ?? null) ? (string) round((float) $traits['pixel_ratio'], 2) : null,
            'Colour scheme' => in_array($traits['color_scheme'] ?? null, ['dark', 'light'], true) ? ucfirst($traits['color_scheme']) : null,
            'Connection' => in_array($traits['connection'] ?? null, ['slow-2g', '2g', '3g', '4g'], true) ? Str::upper($traits['connection']) : null,
            'Memory (GB)' => VisitorContext::int($traits['memory'] ?? null, 64),
            'CPU cores' => VisitorContext::int($traits['cores'] ?? null, 256),
            'Languages' => VisitorContext::text($languages, 100),
        ], fn ($value) => $value !== null && $value !== '');
    }
}
