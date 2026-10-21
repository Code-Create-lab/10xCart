@php
    /*
     | Admin email for a new contact-form lead.
     | Content comes from App\Support\Leads\LeadPresenter ($summary); this file is layout only.
     | Email clients need tables + inline styles. Colours match public/assets/css/10xcart.css.
     */
    $font = "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif";
    $c = [
        'navy' => '#0d2140',     // header band
        'primary' => '#09348e',  // links, section titles
        'accent' => '#c94a0b',   // main button
        'ink' => '#13243d',      // headings
        'text' => '#1e2b3d',     // values
        'muted' => '#475569',    // labels, notes (7:1 on white)
        'page' => '#f1f5f9',
        'card' => '#ffffff',
        'surface' => '#f8fafc',
        'border' => '#e2e8f0',
        'onNavy' => '#c7d2e3',   // secondary text on the header band
    ];
    $logo = 'assets/img/email/logo-light.png';
    // Embedded in the email (no "show images" prompt); a plain URL when previewing in a browser
    $logoSrc = isset($message) ? $message->embed(public_path($logo)) : asset($logo);
    $h2 = "margin:0;font-family:{$font};font-size:12px;line-height:16px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:{$c['primary']};";
@endphp
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="x-apple-disable-message-reformatting">
<meta name="color-scheme" content="light dark">
<meta name="supported-color-schemes" content="light dark">
<title>{{ $summary->subject() }}</title>
<style>
    :root { color-scheme: light dark; supported-color-schemes: light dark; }
    a { text-decoration: underline; }
    @media only screen and (max-width: 520px) {
        .px { padding-left: 20px !important; padding-right: 20px !important; }
        .title { font-size: 22px !important; line-height: 28px !important; }
        .btn-table { width: 100% !important; }
        .btn-cell { display: block !important; width: 100% !important; padding-right: 0 !important; }
        .btn-cell a { display: block !important; text-align: center !important; }
        .kv-label, .kv-value { display: block !important; width: 100% !important; }
        .kv-label { padding-bottom: 0 !important; }
        .kv-value { padding-top: 2px !important; border-top: 0 !important; }
    }
    /* Dark mode (Apple Mail, Outlook.com; Gmail applies its own) */
    @media (prefers-color-scheme: dark) {
        .dm-page { background: #0b1220 !important; }
        .dm-card { background: #111a2e !important; }
        .dm-surface { background: #18233a !important; }
        .dm-border { border-color: #24324a !important; }
        .dm-text { color: #e2e8f0 !important; }
        .dm-muted { color: #a3b1c6 !important; }
        .dm-link, .dm-title { color: #9dbbff !important; }
        .btn-secondary { background: #111a2e !important; color: #9dbbff !important; border-color: #33435f !important; }
    }
    [data-ogsc] .dm-page { background: #0b1220 !important; }
    [data-ogsc] .dm-card { background: #111a2e !important; }
    [data-ogsc] .dm-surface { background: #18233a !important; }
    [data-ogsc] .dm-text { color: #e2e8f0 !important; }
    [data-ogsc] .dm-muted { color: #a3b1c6 !important; }
    [data-ogsc] .dm-link, [data-ogsc] .dm-title { color: #9dbbff !important; }
</style>
</head>
<body class="dm-page" style="margin:0;padding:0;width:100%;background:{{ $c['page'] }};-webkit-text-size-adjust:100%;">
{{-- Inbox preview text (hidden) --}}
<div style="display:none;max-height:0;max-width:0;overflow:hidden;opacity:0;font-size:1px;line-height:1px;color:{{ $c['page'] }};">{{ $summary->preheader() }}{!! str_repeat('&#847;&zwnj;&nbsp;', 60) !!}</div>

<table role="presentation" class="dm-page" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:{{ $c['page'] }};">
<tr>
<td align="center" style="padding:24px 12px 32px;">
<table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:600px;">

{{-- Header: who, what, where, when --}}
<tr>
<td class="px" style="background:{{ $c['navy'] }};border-radius:16px 16px 0 0;padding:28px 32px 26px;">
<img src="{{ $logoSrc }}" width="160" height="44" alt="{{ config('site.name') }}" style="display:block;width:160px;height:auto;border:0;outline:none;text-decoration:none;">
<p style="margin:24px 0 6px;font-family:{{ $font }};font-size:12px;line-height:16px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#ffb27a;">New demo request</p>
<h1 class="title" style="margin:0;font-family:{{ $font }};font-size:26px;line-height:32px;font-weight:700;color:#ffffff;">{{ $summary->name() }}</h1>
<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin-top:12px;">
<tr>
@foreach ($summary->tags() as $tag)
<td style="padding:0 6px 6px 0;">
<span style="display:inline-block;padding:4px 10px;border-radius:999px;border:1px solid #2b4a7d;background:#183563;font-family:{{ $font }};font-size:13px;line-height:18px;font-weight:600;color:#e6edf8;white-space:nowrap;">{{ $tag }}</span>
</td>
@endforeach
</tr>
</table>
<p style="margin:8px 0 0;font-family:{{ $font }};font-size:14px;line-height:20px;color:{{ $c['onNavy'] }};">
@if ($summary->place()){{ $summary->place() }} &middot; @endif{{ $summary->receivedAt() }}
</p>
</td>
</tr>

{{-- Actions, contact details, message --}}
<tr>
<td class="px dm-card" style="background:{{ $c['card'] }};border-radius:0 0 16px 16px;padding:24px 32px 28px;">
<table role="presentation" class="btn-table" cellpadding="0" cellspacing="0" border="0">
<tr>
@foreach ($summary->actions() as $action)
<td class="btn-cell" style="padding:0 8px 8px 0;">
@if ($loop->first)
<a href="{{ $action['url'] }}" style="display:inline-block;padding:12px 18px;border-radius:10px;border:1px solid {{ $c['accent'] }};background:{{ $c['accent'] }};font-family:{{ $font }};font-size:14px;line-height:20px;font-weight:700;color:#ffffff;text-decoration:none;">{{ $action['label'] }}</a>
@else
<a href="{{ $action['url'] }}" class="btn-secondary" style="display:inline-block;padding:12px 18px;border-radius:10px;border:1px solid #cbd5e1;background:#ffffff;font-family:{{ $font }};font-size:14px;line-height:20px;font-weight:700;color:{{ $c['primary'] }};text-decoration:none;">{{ $action['label'] }}</a>
@endif
</td>
@endforeach
</tr>
</table>

<h2 class="dm-title" style="{{ $h2 }}margin-top:20px;">Contact details</h2>
@include('emails.leads.partials.rows', ['rows' => $summary->contactRows()])

@if ($summary->message())
<h2 class="dm-title" style="{{ $h2 }}margin-top:20px;">Message</h2>
<div class="dm-surface dm-text" style="margin-top:12px;padding:14px 16px;border-left:3px solid {{ $c['primary'] }};border-radius:0 10px 10px 0;background:{{ $c['surface'] }};font-family:{{ $font }};font-size:15px;line-height:24px;color:{{ $c['text'] }};">{!! nl2br(e($summary->message())) !!}</div>
@endif
</td>
</tr>

{{-- Location, source, activity, device --}}
@foreach ($summary->sections() as $section)
<tr><td style="height:16px;line-height:16px;font-size:0;">&nbsp;</td></tr>
<tr>
<td class="px dm-card dm-border" style="background:{{ $c['card'] }};border:1px solid {{ $c['border'] }};border-radius:16px;padding:22px 32px 16px;">
<h2 class="dm-title" style="{{ $h2 }}">{{ $section['title'] }}</h2>
@if ($section['note'])
<p class="dm-muted" style="margin:4px 0 0;font-family:{{ $font }};font-size:13px;line-height:18px;color:{{ $c['muted'] }};">{{ $section['note'] }}</p>
@endif
@include('emails.leads.partials.rows', ['rows' => $section['rows']])
</td>
</tr>
@endforeach

{{-- Footer --}}
<tr>
<td class="px dm-muted" style="padding:24px 32px 0;font-family:{{ $font }};font-size:12px;line-height:18px;color:{{ $c['muted'] }};text-align:center;">{{ $summary->footer() }}</td>
</tr>

</table>
</td>
</tr>
</table>
</body>
</html>
