<!-- ============ Work: how we work ============ -->
<section aria-labelledby="work-process-title" class="section" id="how-we-work">
<div class="container">
<div class="section-head reveal">
<span class="eyebrow">How we work with you</span>
<h2 id="work-process-title">Clear plan, daily updates, no surprises</h2>
<p class="lead">You always know what is being built, when it is ready and what it costs.</p>
</div>
<ol class="steps" style="list-style:none;margin:0;padding:0">
<li class="step reveal">
<div class="step-top"><div class="card-icon" style="margin:0"><svg aria-hidden="true" class="i"><use href="#i-message"></use></svg></div><span aria-hidden="true" class="step-num">01</span></div>
<h3>Share your idea</h3>
<p>Tell us what you need on a call or WhatsApp. No technical brief needed.</p>
</li>
<li class="step reveal" style="--d:1">
<div class="step-top"><div class="card-icon" style="margin:0"><svg aria-hidden="true" class="i"><use href="#i-pen"></use></svg></div><span aria-hidden="true" class="step-num">02</span></div>
<h3>Plan &amp; estimate</h3>
<p>You get a clear plan, timeline and price before any work starts.</p>
</li>
<li class="step reveal" style="--d:2">
<div class="step-top"><div class="card-icon" style="margin:0"><svg aria-hidden="true" class="i"><use href="#i-code"></use></svg></div><span aria-hidden="true" class="step-num">03</span></div>
<h3>Build with daily updates</h3>
<p>We share progress every day, so you always know where things stand.</p>
</li>
<li class="step reveal" style="--d:3">
<div class="step-top"><div class="card-icon" style="margin:0"><svg aria-hidden="true" class="i"><use href="#i-rocket"></use></svg></div><span aria-hidden="true" class="step-num">04</span></div>
<h3>Launch &amp; support</h3>
<p>We deploy to your server, hand over clean code and stay on for fixes and growth.</p>
</li>
</ol>

{{-- "Who builds it" card: hidden for now. Delete this comment line and the closing one below the card to show it again
     (content: config/work.php → lead).
@if ($lead = config('work.lead'))
<div class="work-lead reveal">
<div aria-hidden="true" class="work-avatar">{{ collect(explode(' ', $lead['name']))->map(fn ($part) => mb_substr($part, 0, 1))->join('') }}</div>
<div class="work-lead-copy">
<p class="work-lead-label">Who builds it</p>
<h3>{{ $lead['name'] }} <span>{{ $lead['role'] }}</span></h3>
<p>{{ $lead['bio'] }}</p>
</div>
<ul class="work-lead-links">
@foreach ($lead['links'] as $link)
<li><a aria-label="{{ $lead['name'] }} on {{ $link['label'] }} (opens in a new tab)" class="btn btn-ghost btn-sm" href="{{ $link['url'] }}" rel="noopener" target="_blank"><svg aria-hidden="true" class="i {{ str_starts_with($link['icon'], 'b-') ? 'i-fill' : '' }}"><use href="#{{ $link['icon'] }}"></use></svg>{{ $link['label'] }}</a></li>
@endforeach
</ul>
</div>
@endif
--}}
</div>
</section>
