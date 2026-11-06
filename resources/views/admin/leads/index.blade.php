@extends('layouts.admin')

@section('title', 'Leads')

@php($tz = config('site.timezone'))

@section('content')
<div class="page-head">
    <h1>Leads</h1>
    <form class="filters" method="GET" action="{{ route('admin.leads.index') }}">
        <input type="search" name="q" value="{{ $search }}" placeholder="Name, phone, email or ref" aria-label="Search leads"/>
        <button class="btn" type="submit">Search</button>
    </form>
</div>

<div class="card table-wrap">
    <table>
        <thead>
            <tr><th>Received</th><th>Name</th><th>Contact</th><th>Interested in</th><th>Location</th><th>Source</th></tr>
        </thead>
        <tbody>
            @forelse ($leads as $lead)
                @php($presenter = $lead->presenter())
                <tr>
                    <td class="nowrap"><a href="{{ route('admin.leads.show', $lead) }}">{{ $lead->created_at->timezone($tz)->format('j M, g:i A') }}</a><div class="muted small">{{ $lead->ref }}</div></td>
                    <td><a href="{{ route('admin.leads.show', $lead) }}">{{ $lead->name }}</a></td>
                    <td>{{ $lead->phone }}<div class="muted small">{{ $lead->email }}</div></td>
                    <td>{{ $lead->interest }}<div class="muted small">{{ $lead->plan }}</div></td>
                    <td>{{ $presenter->place() ?? '–' }}</td>
                    <td>{{ $lead->context['last_touch']['source'] ?? $lead->visitor?->last_source ?? '–' }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="muted">No leads yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@include('admin.partials.pagination', ['paginator' => $leads])
@endsection
