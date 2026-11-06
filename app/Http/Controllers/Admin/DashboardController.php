<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Visit;
use App\Models\Visitor;
use App\Models\VisitorEvent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /** Range key => [label, days back (0 = since midnight)] */
    public const RANGES = [
        'today' => ['Today', 0],
        '7d' => ['7 days', 6],
        '30d' => ['30 days', 29],
        '90d' => ['90 days', 89],
    ];

    public function __invoke(Request $request): View
    {
        $range = array_key_exists($request->query('range'), self::RANGES) ? $request->query('range') : '7d';
        $timezone = config('site.timezone');
        $from = now($timezone)->subDays(self::RANGES[$range][1])->startOfDay()->utc();

        $visits = Visit::where('started_at', '>=', $from);
        $visitorsInRange = Visitor::where('last_seen_at', '>=', $from);
        $visitCount = (clone $visits)->count();
        $visitorCount = (clone $visits)->distinct()->count('visitor_id');
        $leadCount = Lead::where('created_at', '>=', $from)->count();

        return view('admin.dashboard', [
            'range' => $range,
            'stats' => [
                'Visitors' => number_format($visitorCount),
                'New visitors' => number_format(Visitor::where('first_seen_at', '>=', $from)->count()),
                'Visits' => number_format($visitCount),
                'Page views' => number_format((clone $visits)->sum('page_views')),
                'Avg. engaged time' => $this->duration($visitCount ? intdiv((int) (clone $visits)->sum('engaged_seconds'), $visitCount) : 0),
                'Leads' => number_format($leadCount),
                'Conversion' => $visitorCount ? round($leadCount / $visitorCount * 100, 1).'%' : '–',
            ],
            'liveCount' => Visit::where('last_activity_at', '>=', now()->subMinutes(5))->count(),
            'chart' => $this->chart($range, $from, $timezone),
            'tops' => [
                'Sources' => $this->top(clone $visits, 'source'),
                'Campaigns' => $this->top((clone $visits)->whereNotNull('utm_campaign'), 'utm_campaign'),
                'Cities' => $this->top((clone $visitorsInRange)->whereNotNull('city'), 'city'),
                'Countries' => $this->top((clone $visitorsInRange)->whereNotNull('country'), 'country'),
                'Devices' => $this->top((clone $visitorsInRange)->whereNotNull('device'), 'device'),
                'Browsers' => $this->top((clone $visitorsInRange)->whereNotNull('browser'), 'browser'),
                'Operating systems' => $this->top((clone $visitorsInRange)->whereNotNull('os'), 'os'),
                'Networks (ISP)' => $this->top((clone $visitorsInRange)->whereNotNull('network'), 'network'),
                'Sections reached' => $this->top(VisitorEvent::where('type', 'section')->where('created_at', '>=', $from), 'name', 12),
                'Clicks' => $this->top(VisitorEvent::where('type', 'click')->where('created_at', '>=', $from), 'name', 12),
            ],
            'formStarts' => VisitorEvent::where('type', 'form_start')->where('created_at', '>=', $from)->count(),
            'latestLeads' => Lead::latest()->limit(6)->get(),
            'liveVisits' => Visit::with('visitor')->where('last_activity_at', '>=', now()->subMinutes(5))->latest('last_activity_at')->limit(10)->get(),
        ]);
    }

    /**
     * Visits per hour (today) or per day.
     *
     * @return list<array{label: string, value: int}>
     */
    private function chart(string $range, Carbon $from, string $timezone): array
    {
        $hourly = $range === 'today';
        $format = $hourly ? 'H' : 'Y-m-d';
        $counts = Visit::where('started_at', '>=', $from)
            ->pluck('started_at')
            ->countBy(fn (Carbon $startedAt) => $startedAt->setTimezone($timezone)->format($format));

        $bars = [];
        $cursor = $from->copy()->setTimezone($timezone);
        $end = now($timezone);

        while ($cursor <= $end) {
            $bars[] = [
                'label' => $hourly ? $cursor->format('ga') : $cursor->format('D j M'),
                'value' => $counts[$cursor->format($format)] ?? 0,
            ];
            $hourly ? $cursor->addHour() : $cursor->addDay();
        }

        return $bars;
    }

    /** @return array<string, int> label => count, most common first */
    private function top(Builder $query, string $column, int $limit = 8): array
    {
        return $query->toBase()
            ->selectRaw("{$column} as label, count(*) as total")
            ->groupBy($column)
            ->orderByDesc('total')
            ->limit($limit)
            ->pluck('total', 'label')
            ->map(fn ($total) => (int) $total)
            ->all();
    }

    private function duration(int $seconds): string
    {
        return $seconds < 60 ? "{$seconds}s" : intdiv($seconds, 60).'m '.($seconds % 60).'s';
    }
}
