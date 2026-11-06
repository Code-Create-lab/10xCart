@extends('layouts.admin')

@section('title', 'Dashboard')

@php($tz = config('site.timezone'))

@section('content')
<div class="page-head">
    <div>
        <h1>Dashboard</h1>
        <span class="live">{{ $liveCount }} on the site now</span>
        <span class="muted small">· visitors who accepted cookies only</span>
    </div>
    <nav class="segmented" aria-label="Date range">
        @foreach (\App\Http\Controllers\Admin\DashboardController::RANGES as $key => [$label])
            <a href="{{ route('admin.dashboard', ['range' => $key]) }}" aria-current="{{ $range === $key ? 'true' : 'false' }}">{{ $label }}</a>
        @endforeach
    </nav>
</div>

<div class="grid kpis">
    @foreach ($stats as $label => $value)
        <div class="card kpi"><div class="label">{{ $label }}</div><div class="value">{{ $value }}</div></div>
    @endforeach
    <div class="card kpi"><div class="label">Form starts</div><div class="value">{{ number_format($formStarts) }}</div></div>
</div>

@php($max = max(1, max(array_column($chart, 'value') ?: [0])))
<div class="card" style="margin-bottom:1rem">
    <h2>Visits {{ $range === 'today' ? 'per hour' : 'per day' }}</h2>
    <div class="chart" role="img" aria-label="Visits {{ $range === 'today' ? 'per hour' : 'per day' }}">
        @foreach ($chart as $bar)
            <div class="bar" tabindex="0" data-tip="{{ $bar['label'] }}: {{ $bar['value'] }}">
                <span style="height: {{ round($bar['value'] / $max * 100, 1) }}%"></span>
            </div>
        @endforeach
    </div>
    <div class="chart-axis"><span>{{ $chart[0]['label'] ?? '' }}</span><span>Peak {{ $max }}</span><span>{{ end($chart)['label'] ?? '' }}</span></div>
</div>

<div class="grid grid-2">
    <div class="card">
        <h2>On the site now</h2>
        @forelse ($liveVisits as $visit)
            <div class="small" style="padding:.3rem 0">
                <a href="{{ route('admin.visitors.show', $visit->visitor) }}">{{ $visit->visitor->place() ?? $visit->ip }}</a>
                <span class="muted">· {{ $visit->visitor->device }} · {{ $visit->source }} · {{ $visit->page_views }} pages · active {{ $visit->last_activity_at->diffForHumans() }}</span>
            </div>
        @empty
            <p class="muted small">Nobody right now.</p>
        @endforelse
    </div>
    <div class="card">
        <h2>Latest leads</h2>
        @forelse ($latestLeads as $lead)
            <div class="small" style="padding:.3rem 0">
                <a href="{{ route('admin.leads.show', $lead) }}">{{ $lead->name }}</a>
                <span class="muted">· {{ $lead->interest }} · {{ $lead->created_at->timezone($tz)->format('j M, g:i A') }}</span>
            </div>
        @empty
            <p class="muted small">No leads yet.</p>
        @endforelse
    </div>
</div>

<div class="grid grid-3">
    @foreach ($tops as $title => $rows)
        @php($topMax = max($rows ?: [1]))
        <div class="card">
            <h2>{{ $title }}</h2>
            @if ($rows)
                <ul class="rank">
                    @foreach ($rows as $label => $count)
                        <li>
                            <span class="fill" style="width: {{ round($count / $topMax * 100) }}%"></span>
                            <span class="name" title="{{ $label }}">{{ $label }}</span>
                            <span class="count">{{ number_format($count) }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="muted small">No data yet.</p>
            @endif
        </div>
    @endforeach
</div>
@endsection
