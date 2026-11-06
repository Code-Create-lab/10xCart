{{-- Site footer. Links, socials and contact details: config/site.php --}}
@php($home = request()->routeIs('home') ? '' : route('home'))
<!-- ============ Footer ============ -->
<footer class="site-footer">
<div class="container">
<div class="footer-grid">
<div class="footer-about">
<a class="logo" href="{{ $home ?: '#top' }}"><img sizes="198px" srcset="{{ asset('assets/img/brand/logo-horizontal-light-198.webp') }} 198w, {{ asset('assets/img/brand/logo-horizontal-light-395.webp') }} 395w" alt="10xCart – Ecommerce web development" height="54" loading="lazy" src="{{ asset('assets/img/brand/logo-horizontal-light-198.webp') }}" width="198"/></a>
<p>10xCart is an ecommerce website and app development company in India. We build and manage online stores, Android &amp; iOS apps, multi-vendor marketplaces, SaaS products and custom software for small businesses.</p>
<x-socials />
</div>
@foreach (config('site.footer_menus') as $heading => $links)
<div>
<h3>{{ $heading }}</h3>
<ul>
@foreach ($links as $label => $url)
<li><a href="{{ str_starts_with($url, '#') ? $home.$url : $url }}">{{ $label }}</a></li>
@endforeach
</ul>
</div>
@endforeach
<div>
<h3>Contact</h3>
<address>
<ul>
<li><a href="tel:{{ config('site.phone') }}">{{ config('site.phone_display') }} (Sales)</a></li>
<li><a href="mailto:{{ config('site.email') }}">{{ config('site.email') }}</a></li>
{{-- Office address, hidden for now: <li><span>{{ config('site.address') }}</span></li> --}}
</ul>
</address>
</div>
</div>
<div class="footer-bottom">
<p>© <span id="year">{{ date('Y') }}</span> 10xCart. All rights reserved.</p>
<nav aria-label="Legal">
@foreach (config('site.legal') as $label => $url)
<a href="{{ $url }}">{{ $label }}</a>
@endforeach
<a href="#cookie-consent" id="cookie-settings">Cookie settings</a>
</nav>
</div>
</div>
</footer>
