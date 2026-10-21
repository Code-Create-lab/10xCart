@extends('layouts.app')

{{-- SEO: title, description, canonical, Open Graph, Twitter --}}
@section('seo')
<!-- SEO:META:START -->
<title>Ecommerce Website &amp; App Development in India | 10xCart</title>
<meta content="10xCart builds and maintains ecommerce websites and Android &amp; iOS apps for small businesses in India, plus SaaS and custom software. Get a free demo." name="description"/>
<meta content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1" name="robots"/>
<link href="https://10xcart.snehal.info/" rel="canonical"/>
<meta content="10xCart" name="author"/>
<meta content="10xCart" name="application-name"/>
<meta content="10xCart" name="apple-mobile-web-app-title"/>
<meta content="#09348e" name="theme-color"/>
<!-- Open Graph (Facebook, LinkedIn, WhatsApp) -->
<meta content="website" property="og:type"/>
<meta content="10xCart" property="og:site_name"/>
<meta content="en_IN" property="og:locale"/>
<meta content="https://10xcart.snehal.info/" property="og:url"/>
<meta content="10xCart — Ecommerce websites &amp; apps for small businesses" property="og:title"/>
<meta content="Launch your online store with a website and Android &amp; iOS apps — single-vendor or multi-vendor, set up and maintained for you. SaaS and custom software development too." property="og:description"/>
<meta content="https://10xcart.snehal.info/assets/img/brand/og-image.jpg" property="og:image"/>
<meta content="image/jpeg" property="og:image:type"/>
<meta content="1200" property="og:image:width"/>
<meta content="630" property="og:image:height"/>
<meta content="10xCart — ecommerce websites and apps for small businesses, with an admin dashboard and store app preview" property="og:image:alt"/>
<!-- X / Twitter -->
<meta content="summary_large_image" name="twitter:card"/>
<meta content="10xCart — Ecommerce websites &amp; apps for small businesses" name="twitter:title"/>
<meta content="Launch your online store with a website and Android &amp; iOS apps — single-vendor or multi-vendor, set up and maintained for you. SaaS and custom software development too." name="twitter:description"/>
<meta content="https://10xcart.snehal.info/assets/img/brand/og-image.jpg" name="twitter:image"/>
<meta content="10xCart — ecommerce websites and apps for small businesses, with an admin dashboard and store app preview" name="twitter:image:alt"/>
<!-- AI assistants: Markdown summary of this site (llmstxt.org) -->
<link href="https://10xcart.snehal.info/llms.txt" rel="alternate" title="10xCart — summary for AI assistants" type="text/markdown"/>
<!-- Search Console / Bing Webmaster verification: paste your codes and uncomment -->
<!-- <meta content="GOOGLE_VERIFICATION_CODE" name="google-site-verification"/> -->
<!-- <meta content="BING_VERIFICATION_CODE" name="msvalidate.01"/> -->
<!-- SEO:META:END -->
@endsection

{{-- Hero image (LCP) preload --}}
@push('head')
<link as="image" fetchpriority="high" href="{{ asset('media/10xcart/admin-dashboard-640.webp') }}" imagesizes="(min-width: 1024px) 540px, (min-width: 680px) 612px, calc(100vw - 50px)" imagesrcset="{{ asset('media/10xcart/admin-dashboard-640.webp') }} 640w, {{ asset('media/10xcart/admin-dashboard-960.webp') }} 960w, {{ asset('media/10xcart/admin-dashboard.webp') }} 1440w" rel="preload" type="image/webp"/>
@endpush

@section('content')
    @include('pages.home.sections.hero')
    @include('pages.home.sections.industries')
    @include('pages.home.sections.services')
    @include('pages.home.sections.solutions')
    @include('pages.home.sections.process')
    @include('pages.home.sections.why-choose-us')
    @include('pages.home.sections.features')
    @include('pages.home.sections.admin-tour')
    @include('pages.home.sections.showcase')
    @include('pages.home.sections.pricing')
    @include('pages.home.sections.custom-cta')
    @include('pages.home.sections.faq')
    @include('pages.home.sections.contact')
@endsection

{{-- schema.org structured data --}}
@push('schema')
    @include('pages.home.schema')
@endpush
