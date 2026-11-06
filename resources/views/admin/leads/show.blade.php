@extends('layouts.admin')

@section('title', $lead->name)

@section('content')
<div class="page-head">
    <div>
        <h1>{{ $presenter->name() }}</h1>
        <span class="muted small">{{ $presenter->ref() }} · {{ implode(' · ', $presenter->tags()) }} · {{ $presenter->receivedAt() }}</span>
    </div>
    <div class="filters">
        @foreach ($presenter->actions() as $action)
            <a class="btn" href="{{ $action['url'] }}" @if (str_starts_with($action['url'], 'http')) target="_blank" rel="noopener" @endif>{{ $action['label'] }}</a>
        @endforeach
    </div>
</div>

<div class="grid grid-2">
    <div class="card">
        <h2>Contact details</h2>
        @include('admin.leads.rows', ['rows' => $presenter->contactRows()])
        @if ($presenter->message())
            <h2 style="margin-top:1rem">Message</h2>
            <p style="white-space:pre-line;margin:0">{{ $presenter->message() }}</p>
        @endif
        @if ($lead->visitor)
            <p style="margin-bottom:0"><a href="{{ route('admin.visitors.show', $lead->visitor) }}">See this visitor's full browsing history →</a></p>
        @else
            <p class="muted small" style="margin-bottom:0">No browsing history: the visitor did not accept analytics cookies.</p>
        @endif
    </div>

    @foreach ($presenter->sections() as $section)
        <div class="card">
            <h2>{{ $section['title'] }} @if ($section['note'])<span class="muted small">({{ $section['note'] }})</span>@endif</h2>
            @include('admin.leads.rows', ['rows' => $section['rows']])
        </div>
    @endforeach
</div>
@endsection
