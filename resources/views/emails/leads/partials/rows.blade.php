{{-- Label / value rows (from LeadPresenter). Uses $c and $font from emails/leads/new-lead.blade.php --}}
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:12px;">
@foreach ($rows as $row)
@php($line = $loop->first ? '' : 'border-top:1px solid '.$c['border'].';')
<tr>
<td class="kv-label dm-muted dm-border" width="34%" valign="top" style="{{ $line }}padding:10px 12px 10px 0;font-family:{{ $font }};font-size:13px;line-height:20px;font-weight:600;color:{{ $c['muted'] }};">{{ $row['label'] }}</td>
<td class="kv-value dm-text dm-border" valign="top" style="{{ $line }}padding:10px 0;font-family:{{ $font }};font-size:15px;line-height:22px;color:{{ $c['text'] }};word-break:break-word;">
@if ($row['url'])
<a class="dm-link" href="{{ $row['url'] }}" style="color:{{ $c['primary'] }};text-decoration:underline;">{{ $row['value'] }}</a>
@else
{{ $row['value'] }}
@endif
@if ($row['hint'])
<div class="dm-muted" style="margin-top:2px;font-size:13px;line-height:18px;color:{{ $c['muted'] }};">{{ $row['hint'] }}</div>
@endif
</td>
</tr>
@endforeach
</table>
