<!-- ============ Work: hero ============ -->
<section aria-labelledby="work-title" class="hero work-hero" id="top">
<div aria-hidden="true" class="blob blob-1"></div>
<div aria-hidden="true" class="blob blob-2"></div>
<div class="container">
<div class="hero-grid">
<div class="hero-copy">
<span class="badge-pill"><span aria-hidden="true" class="pulse-dot"></span>Portfolio · live projects you can open</span>
<h1 id="work-title">Stores, platforms &amp; apps <span class="text-grad">we've shipped.</span></h1>
<p class="lead">Real projects running in production: fashion and skincare stores, a ride-booking platform, sports federation websites and enterprise back-ends. Open any live site and judge the work for yourself.</p>
<div class="hero-actions">
<a class="btn btn-accent" href="{{ $contactUrl }}">Discuss your project <svg aria-hidden="true" class="i i-arrow"><use href="#i-arrow-right"></use></svg></a>
<a class="btn btn-ghost" href="#projects">See the projects</a>
</div>
<ul class="check-list">
<li><span class="check-dot"><svg aria-hidden="true" class="i"><use href="#i-check"></use></svg></span>Laravel &amp; Node.js experts</li>
<li><span class="check-dot"><svg aria-hidden="true" class="i"><use href="#i-check"></use></svg></span>Payments, shipping &amp; admin panels</li>
<li><span class="check-dot"><svg aria-hidden="true" class="i"><use href="#i-check"></use></svg></span>Daily progress updates</li>
</ul>
</div>

{{-- Three of the projects below, stacked like open browser windows (decorative: the cards describe them) --}}
<div aria-hidden="true" class="work-collage">
<div class="browser wc wc-back">
<div class="browser-bar"><span></span><span></span><span></span><div class="browser-url">yellowrides.com</div></div>
<img alt="" decoding="async" height="855" sizes="(min-width: 1024px) 330px, 55vw" src="{{ asset('media/work/yellow-ride-home-640.webp') }}" srcset="{{ asset('media/work/yellow-ride-home-640.webp') }} 640w, {{ asset('media/work/yellow-ride-home-960.webp') }} 960w" width="1368"/>
</div>
<div class="browser wc wc-main">
<div class="browser-bar"><span></span><span></span><span></span><div class="browser-url">zakh.in</div></div>
<img alt="" fetchpriority="high" height="855" sizes="(min-width: 1024px) 440px, 72vw" src="{{ asset('media/work/zakh-home-640.webp') }}" srcset="{{ asset('media/work/zakh-home-640.webp') }} 640w, {{ asset('media/work/zakh-home-960.webp') }} 960w" width="1368"/>
</div>
<div class="browser wc wc-front">
<div class="browser-bar"><span></span><span></span><span></span><div class="browser-url">internationaljumpballfederation.com</div></div>
<img alt="" decoding="async" height="855" sizes="(min-width: 1024px) 300px, 50vw" src="{{ asset('media/work/ijf-home-640.webp') }}" srcset="{{ asset('media/work/ijf-home-640.webp') }} 640w, {{ asset('media/work/ijf-home-960.webp') }} 960w" width="1368"/>
</div>
<div class="float-card wc-card-1">
<span class="fc-icon"><svg class="i"><use href="#i-cart"></use></svg></span>
<span><strong>600+ products live</strong><small>ZAKH fashion store</small></span>
</div>
<div class="float-card wc-card-2">
<span class="fc-icon is-blue"><svg class="i"><use href="#i-zap"></use></svg></span>
<span><strong>35% faster API</strong><small>Yellow Ride bookings</small></span>
</div>
</div>
</div>

<dl class="stats">
@foreach (config('work.stats') as $stat)
<div class="stat"><dt><strong>{{ $stat['value'] }}</strong></dt><dd style="margin:0"><span>{{ $stat['label'] }}</span></dd></div>
@endforeach
</dl>
</div>
</section>
