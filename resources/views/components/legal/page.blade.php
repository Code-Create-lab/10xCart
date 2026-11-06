@props(['title', 'updated', 'toc'])
{{-- Shell of the legal pages (resources/views/pages/legal): page head, contents list, text and a contact card.
     "updated" is a Y-m-d date and "toc" maps the id of each <section> in the text to its heading.
     Styles: public/assets/css/legal.css --}}
@push('styles')
<style>{!! \App\Support\Assets::inlineCss('assets/css/legal.css') !!}</style>
@endpush
@php
    $updatedAt = \Illuminate\Support\Carbon::parse($updated);
    $pageUrl = url()->current();

    // schema.org: this page as part of the site described on the home page (pages/home/schema.blade.php)
    $schema = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'WebPage',
                '@id' => $pageUrl.'#webpage',
                'url' => $pageUrl,
                'name' => $title.' | 10xCart',
                'isPartOf' => ['@id' => route('home').'/#website'],
                'about' => ['@id' => route('home').'/#organization'],
                'inLanguage' => 'en-IN',
                'dateModified' => $updatedAt->toDateString(),
                'breadcrumb' => ['@id' => $pageUrl.'#breadcrumb'],
            ],
            [
                '@type' => 'BreadcrumbList',
                '@id' => $pageUrl.'#breadcrumb',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home').'/'],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => $title],
                ],
            ],
        ],
    ];
@endphp
@push('schema')
<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endpush
<section aria-labelledby="legal-title" class="hero legal-hero">
<div aria-hidden="true" class="blob blob-1"></div>
<div class="container">
<span class="eyebrow">Legal</span>
<h1 id="legal-title">{{ $title }}</h1>
<p class="legal-meta"><svg aria-hidden="true" class="i"><use href="#i-history"></use></svg>Last updated <time datetime="{{ $updatedAt->toDateString() }}">{{ $updatedAt->format('j F Y') }}</time></p>
<p class="lead legal-intro">{{ $intro }}</p>
@isset($summary)
<div class="legal-summary">
<h2>{{ $summary->attributes->get('title', 'In short') }}</h2>
<ul>
{{ $summary }}
</ul>
</div>
@endisset
</div>
</section>

<section class="section legal-section">
<div class="container legal-layout">
{{-- Contents: a collapsible list on phones and tablets, a sticky list beside the text on wide screens --}}
<nav aria-label="On this page" class="legal-toc-mobile">
<details>
<summary>On this page <svg aria-hidden="true" class="i"><use href="#i-chevron-down"></use></svg></summary>
<ol>
@foreach ($toc as $id => $label)
<li><a href="#{{ $id }}">{{ $label }}</a></li>
@endforeach
</ol>
</details>
</nav>
<nav aria-label="On this page" class="legal-toc">
<p class="legal-toc-title">On this page</p>
<ol>
@foreach ($toc as $id => $label)
<li><a href="#{{ $id }}">{{ $label }}</a></li>
@endforeach
</ol>
</nav>

<div class="legal-body">
{{ $slot }}

<div class="legal-contact">
<div>
<h2>Questions about this page?</h2>
<p>Email or call us and we'll explain anything here. See also our @if (request()->routeIs('privacy'))<a href="{{ route('terms') }}">Terms &amp; Conditions</a>@else<a href="{{ route('privacy') }}">Privacy Policy</a>@endif.</p>
</div>
<div class="legal-contact-actions">
<a class="btn btn-primary" href="mailto:{{ config('site.email') }}"><svg aria-hidden="true" class="i"><use href="#i-mail"></use></svg>Email us</a>
<a class="btn btn-ghost" href="tel:{{ config('site.phone') }}"><svg aria-hidden="true" class="i"><use href="#i-phone"></use></svg>{{ config('site.phone_display') }}</a>
</div>
</div>
</div>
</div>
</section>
