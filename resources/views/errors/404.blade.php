@extends('layouts.app')

{{-- Shown for any unknown URL, with status 404 (Laravel picks up errors/404 automatically).
     Sends visitors back to the home page sections instead of a dead end. --}}
@section('seo')
<title>Page not found | 10xCart</title>
<meta content="noindex, follow" name="robots"/>
<meta content="#09348e" name="theme-color"/>
@endsection

@section('content')
<section aria-labelledby="not-found-title" class="hero">
<div aria-hidden="true" class="blob blob-1"></div>
<div aria-hidden="true" class="blob blob-2"></div>
<div class="container">
<span class="eyebrow">Error 404</span>
<h1 id="not-found-title">This page doesn't exist</h1>
<p class="lead">The link may be old or mistyped. Start from the home page, or jump straight to what you were looking for.</p>
<div class="hero-actions">
<a class="btn btn-accent" href="{{ route('home') }}">Go to the home page <svg aria-hidden="true" class="i i-arrow"><use href="#i-arrow-right"></use></svg></a>
<a class="btn btn-ghost" href="{{ route('home') }}#contact">Contact us</a>
</div>
<h2 class="sr-only" id="not-found-links">Popular sections</h2>
<nav aria-labelledby="not-found-links" class="hero-actions" style="margin-top:16px">
@foreach (config('site.nav') as $item)
<a class="btn btn-ghost btn-sm" href="{{ route('home').$item['url'] }}">{{ $item['mobile'] }}</a>
@endforeach
</nav>
</div>
</section>
@endsection
