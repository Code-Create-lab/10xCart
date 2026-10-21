<!DOCTYPE html>
<html lang="en-IN">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1" name="viewport"/>
@include('partials.analytics')
{{-- Page SEO meta (title, description, canonical, OG, Twitter) --}}
@yield('seo')
<link href="{{ asset('favicon.ico') }}" rel="icon" sizes="48x48"/>
<link href="{{ asset('assets/img/brand/favicon-32.png') }}" rel="icon" sizes="32x32" type="image/png"/>
<link href="{{ asset('assets/img/brand/favicon-192.png') }}" rel="icon" sizes="192x192" type="image/png"/>
<link href="{{ asset('assets/img/brand/apple-touch-icon.png') }}" rel="apple-touch-icon" sizes="180x180"/>
<link href="{{ asset('site.webmanifest') }}" rel="manifest"/>
<!-- Fonts: self-hosted Rubik (headings) + Nunito Sans (body), both used above the fold -->
<link as="font" crossorigin="" href="{{ asset('assets/fonts/rubik.woff2') }}" rel="preload" type="font/woff2"/>
<link as="font" crossorigin="" href="{{ asset('assets/fonts/nunito-sans.woff2') }}" rel="preload" type="font/woff2"/>
{{-- Page-specific <head> tags (e.g. LCP image preload) --}}
@stack('head')
<!-- Styles: edit public/assets/css/10xcart.css; it is minified and inlined here (cached until the file changes) -->
<style>{!! \App\Support\Assets::inlineCss('assets/css/10xcart.css') !!}</style>
<script>document.documentElement.classList.add("js");</script>
</head>
<body>
@include('partials.icons')

<a class="skip-link" href="#main">Skip to main content</a>

<x-header />

<main id="main">
@yield('content')
</main>

<x-footer />

<x-mobile-cta />

<script defer src="{{ \App\Support\Assets::versioned('assets/js/10xcart.js') }}"></script>
{{-- Structured data for search engines (schema.org) --}}
@stack('schema')
</body>
</html>
