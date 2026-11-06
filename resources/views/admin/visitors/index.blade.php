@extends('layouts.admin')

@section('title', 'Visitors')

@php($tz = config('site.timezone'))

@section('content')
<div class="page-head">
    <h1>Visitors</h1>
    <form class="filters" method="GET" action="{{ route('admin.visitors.index') }}">
        <input type="search" name="q" value="{{ $search }}" placeholder="IP, city, country, network, source" aria-label="Search visitors"/>
        <label class="small"><input type="checkbox" name="leads" value="1" @checked($onlyLeads)/> Sent a lead</label>
        <button class="btn" type="submit">Filter</button>
    </form>
</div>

<div class="card table-wrap">
    <table>
        <thead>
            <tr><th>Last seen</th><th>Location</th><th>Device</th><th>Source</th><th>Visits</th><th>Pages</th><th>Time</th><th></th></tr>
        </thead>
        <tbody>
            @forelse ($visitors as $visitor)
                <tr>
                    <td class="nowrap"><a href="{{ route('admin.visitors.show', $visitor) }}">{{ $visitor->last_seen_at->timezone($tz)->format('j M, g:i A') }}</a></td>
                    <td>{{ $visitor->place() ?? '–' }}<div class="muted small">{{ $visitor->last_ip }}{{ $visitor->network ? ' · '.$visitor->network : '' }}</div></td>
                    <td>{{ $visitor->device }}<div class="muted small">{{ implode(' · ', array_filter([$visitor->model, $visitor->os, $visitor->browser])) }}</div></td>
                    <td>{{ $visitor->last_source }}@if ($visitor->first_source && $visitor->first_source !== $visitor->last_source)<div class="muted small">First: {{ $visitor->first_source }}</div>@endif</td>
                    <td>{{ $visitor->visits_count }}</td>
                    <td>{{ $visitor->page_views_count }}</td>
                    <td class="nowrap">{{ gmdate($visitor->engaged_seconds >= 3600 ? 'G\h i\m' : 'i:s', $visitor->engaged_seconds) }}</td>
                    <td>@if ($visitor->leads_count)<span class="badge lead">Lead</span>@endif</td>
                </tr>
            @empty
                <tr><td colspan="8" class="muted">No visitors yet. Visitors appear here after they accept cookies on the site.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@include('admin.partials.pagination', ['paginator' => $visitors])
@endsection
