{{-- Site header + mobile menu. Menu items: config/site.php → nav --}}
<!-- ============ Header ============ -->
<header class="site-header" id="site-header">
<div class="container nav">
<a class="logo" href="#top"><img sizes="(min-width: 1080px) 176px, (min-width: 480px) 161px, 139px" srcset="{{ asset('assets/img/brand/logo-horizontal-176.webp') }} 176w, {{ asset('assets/img/brand/logo-horizontal-264.webp') }} 264w, {{ asset('assets/img/brand/logo-horizontal-351.webp') }} 351w" alt="10xCart – Ecommerce web development" height="48" src="{{ asset('assets/img/brand/logo-horizontal-176.webp') }}" width="176"/></a>
<nav aria-label="Primary">
<ul class="nav-links">
@foreach (config('site.nav') as $item)
<li><a href="{{ $item['url'] }}">{{ $item['label'] }}</a></li>
@endforeach
</ul>
</nav>
<div class="nav-actions">
<a class="nav-phone" href="tel:{{ config('site.phone') }}"><svg aria-hidden="true" class="i"><use href="#i-phone"></use></svg>{{ config('site.phone_display') }}</a>
<a class="btn btn-accent btn-sm" href="#contact">Get free demo</a>
<button aria-controls="mobile-menu" aria-expanded="false" aria-label="Open menu" class="menu-toggle" id="menu-toggle" type="button">
<svg aria-hidden="true" class="i i-open"><use href="#i-menu"></use></svg>
<svg aria-hidden="true" class="i i-close"><use href="#i-x"></use></svg>
</button>
</div>
</div>
</header>
<div aria-label="Mobile" class="mobile-menu" id="mobile-menu" role="navigation">
<ul>
@foreach (config('site.nav') as $item)
<li><a href="{{ $item['url'] }}">{{ $item['mobile'] }} <svg aria-hidden="true" class="i"><use href="#i-chevron-right"></use></svg></a></li>
@endforeach
</ul>
<a class="btn btn-accent btn-block" href="#contact">Get free demo <svg aria-hidden="true" class="i i-arrow"><use href="#i-arrow-right"></use></svg></a>
<a class="btn btn-ghost btn-block" href="tel:{{ config('site.phone') }}"><svg aria-hidden="true" class="i"><use href="#i-phone"></use></svg>Call sales: {{ config('site.phone_display') }}</a>
</div>
