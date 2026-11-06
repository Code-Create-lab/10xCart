{{-- Label/value rows from App\Support\Leads\LeadPresenter (same as the lead email) --}}
<dl class="kv">
    @foreach ($rows as $row)
        <dt>{{ $row['label'] }}</dt>
        <dd>
            @if ($row['url'])<a href="{{ $row['url'] }}" @if (str_starts_with($row['url'], 'http')) target="_blank" rel="noopener" @endif>{{ $row['value'] }}</a>@else{{ $row['value'] }}@endif
            @if ($row['hint'])<div class="muted small">{{ $row['hint'] }}</div>@endif
        </dd>
    @endforeach
</dl>
