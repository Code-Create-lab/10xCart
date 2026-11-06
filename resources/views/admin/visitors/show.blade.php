@extends('layouts.admin')

@section('title', 'Visitor')

@php($tz = config('site.timezone'))

@section('content')
<div class="page-head">
    <div>
        <h1>{{ $visitor->place() ?? 'Unknown location' }}</h1>
        <span class="muted small">Visitor #{{ $visitor->id }} · first seen {{ $visitor->first_seen_at->timezone($tz)->format('j M Y, g:i A') }} · last seen {{ $visitor->last_seen_at->diffForHumans() }}</span>
    </div>
    <a href="{{ route('admin.visitors.index') }}">← All visitors</a>
</div>

<div class="grid kpis">
    <div class="card kpi"><div class="label">Visits</div><div class="value">{{ $visitor->visits_count }}</div></div>
    <div class="card kpi"><div class="label">Page views</div><div class="value">{{ $visitor->page_views_count }}</div></div>
    <div class="card kpi"><div class="label">Engaged time</div><div class="value">{{ intdiv($visitor->engaged_seconds, 60) }}m {{ $visitor->engaged_seconds % 60 }}s</div></div>
    <div class="card kpi"><div class="label">Leads</div><div class="value">{{ $leads->count() }}</div></div>
</div>

<div class="grid grid-2">
    <div class="card">
        <h2>Location &amp; network</h2>
        <dl class="kv">
            <dt>City</dt><dd>{{ $visitor->city ?? '–' }}</dd>
            <dt>Region</dt><dd>{{ $visitor->region ?? '–' }}</dd>
            <dt>Country</dt><dd>{{ $visitor->country ?? '–' }}</dd>
            <dt>Network (ISP)</dt><dd>{{ $visitor->network ?? '–' }}</dd>
            <dt>Last IP</dt><dd>{{ $visitor->last_ip ?? '–' }}</dd>
            <dt>Browser timezone</dt><dd>{{ $visitor->timezone ?? '–' }}</dd>
            <dt>First source</dt><dd>{{ $visitor->first_source ?? '–' }}</dd>
            <dt>Latest source</dt><dd>{{ $visitor->last_source ?? '–' }}</dd>
        </dl>
    </div>
    <div class="card">
        <h2>Device</h2>
        <dl class="kv">
            <dt>Device</dt><dd>{{ implode(' · ', array_filter([$visitor->device, $visitor->model])) ?: '–' }}</dd>
            <dt>Operating system</dt><dd>{{ $visitor->os ?? '–' }}</dd>
            <dt>Browser</dt><dd>{{ $visitor->browser ?? '–' }}</dd>
            <dt>Screen</dt><dd>{{ $visitor->screen ?? '–' }}</dd>
            <dt>Language</dt><dd>{{ $visitor->language ?? '–' }}</dd>
            @foreach ($visitor->traits ?? [] as $label => $value)
                <dt>{{ $label }}</dt><dd>{{ $value }}</dd>
            @endforeach
            <dt>User agent</dt><dd class="small muted">{{ $visitor->user_agent ?? '–' }}</dd>
        </dl>
    </div>
</div>

@if ($leads->isNotEmpty())
<div class="card" style="margin-bottom:1rem">
    <h2>Leads from this visitor</h2>
    @foreach ($leads as $lead)
        <div class="small" style="padding:.25rem 0">
            <a href="{{ route('admin.leads.show', $lead) }}">{{ $lead->name }} · {{ $lead->phone }}</a>
            <span class="muted">· {{ $lead->interest }} · {{ $lead->created_at->timezone($tz)->format('j M Y, g:i A') }}</span>
        </div>
    @endforeach
</div>
@endif

<div class="card">
    <h2>Visits <span class="muted small">(latest {{ $visits->count() }})</span></h2>
    @foreach ($visits as $visit)
        <section class="visit">
            <div class="visit-head">
                <strong>{{ $visit->started_at->timezone($tz)->format('D j M Y, g:i A') }}</strong>
                <span class="muted small">
                    {{ $visit->source }}
                    @if ($visit->utm_campaign) · campaign “{{ $visit->utm_campaign }}”@endif
                    · {{ $visit->page_views }} {{ Str::plural('page', $visit->page_views) }}
                    · {{ intdiv($visit->engaged_seconds, 60) }}m {{ $visit->engaged_seconds % 60 }}s engaged
                    · scrolled {{ $visit->max_scroll }}%
                    · {{ $visit->ip }}@if (($visit->location['status'] ?? null) === 'ok'), {{ $visit->location['city'] ?? $visit->location['country'] ?? '' }}@endif
                </span>
            </div>
            @if ($visit->referrer)<div class="muted small">Referrer: {{ $visit->referrer }}</div>@endif
            @if ($visit->landing)<div class="muted small" style="margin-bottom:.4rem">Landing page: {{ $visit->landing }}</div>@endif
            <ol class="timeline">
                @forelse ($visit->events as $event)
                    <li><time>{{ $event->created_at->timezone($tz)->format('g:i:s A') }}</time>{{ $event->label() }}@if ($event->name) <strong>{{ $event->name }}</strong>@endif @if ($event->type === 'page_view' && $event->path)<span class="muted">{{ $event->path }}</span>@endif</li>
                @empty
                    <li class="muted">No events recorded.</li>
                @endforelse
            </ol>
        </section>
    @endforeach
</div>
@endsection
