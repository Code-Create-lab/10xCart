@extends('layouts.app')

{{-- Portfolio page, shared with clients by link only: it is not linked from the site and search engines are asked
     not to index it. Projects, stats and categories: config/work.php. Styles: public/assets/css/work.css. --}}
@section('seo')
<title>Our work: e-commerce stores, platforms &amp; apps | 10xCart</title>
<meta content="Live projects by 10xCart: fashion and skincare stores, a ride-booking platform, sports federation websites, enterprise back-ends and AI automation." name="description"/>
<meta content="noindex, nofollow" name="robots"/>
<link href="{{ route('work') }}" rel="canonical"/>
<meta content="#09348e" name="theme-color"/>
<!-- Link previews (WhatsApp, LinkedIn, email) -->
<meta content="website" property="og:type"/>
<meta content="10xCart" property="og:site_name"/>
<meta content="en_IN" property="og:locale"/>
<meta content="{{ route('work') }}" property="og:url"/>
<meta content="Our work: stores, platforms &amp; apps we've shipped | 10xCart" property="og:title"/>
<meta content="Fashion and skincare stores, a ride-booking platform, sports federation websites, enterprise back-ends and AI automation, all live in production." property="og:description"/>
<meta content="{{ asset('media/work/og-work.jpg') }}" property="og:image"/>
<meta content="image/jpeg" property="og:image:type"/>
<meta content="1200" property="og:image:width"/>
<meta content="630" property="og:image:height"/>
<meta content="Screenshots of projects built by 10xCart: ZAKH fashion store, Yellow Ride, Bodhi Bliss and the International JumpBall Federation" property="og:image:alt"/>
<meta content="summary_large_image" name="twitter:card"/>
<meta content="Our work: stores, platforms &amp; apps we've shipped | 10xCart" name="twitter:title"/>
<meta content="Fashion and skincare stores, a ride-booking platform, sports federation websites, enterprise back-ends and AI automation, all live in production." name="twitter:description"/>
<meta content="{{ asset('media/work/og-work.jpg') }}" name="twitter:image"/>
@endsection

{{-- Largest hero image (LCP) --}}
@push('head')
<link as="image" fetchpriority="high" href="{{ asset('media/work/zakh-home-640.webp') }}" imagesizes="(min-width: 1024px) 440px, 72vw" imagesrcset="{{ asset('media/work/zakh-home-640.webp') }} 640w, {{ asset('media/work/zakh-home-960.webp') }} 960w" rel="preload" type="image/webp"/>
@endpush

@push('styles')
<style>{!! \App\Support\Assets::inlineCss('assets/css/work.css') !!}</style>
@endpush

@section('content')
    @php($whatsappUrl = 'https://wa.me/'.ltrim(config('site.phone'), '+').'?text='.rawurlencode('Hi 10xCart, I saw your work and would like to discuss a project.'))
    @php($contactUrl = route('home').'#contact')
    @include('pages.work.icons')
    @include('pages.work.sections.hero')
    @include('pages.work.sections.projects')
    @include('pages.work.sections.capabilities')
    @include('pages.work.sections.process')
    @include('pages.work.sections.cta')
@endsection
