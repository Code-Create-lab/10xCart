<!-- ============ Work: projects ============ -->
@php($projects = config('work.projects'))
@php($categories = config('work.categories'))
<section aria-labelledby="projects-title" class="section" id="projects">
<div class="container">
<div class="section-head reveal">
<span class="eyebrow">Selected work</span>
<h2 id="projects-title">Projects our clients run their business on</h2>
<p class="lead">Pick the kind of product you need. Live projects open in a new tab, so you can click through the real thing.</p>
</div>

{{-- Category filter: shown by 10xcart.js ("Work page: project filter"); without JavaScript every project is listed --}}
<div class="work-filters" hidden id="work-filters">
<div aria-label="Filter projects" class="work-chips" role="group">
<button aria-pressed="true" class="work-chip" data-filter="all" type="button">All projects <span class="work-chip-count">{{ count($projects) }}</span></button>
@foreach ($categories as $key => $category)
<button aria-pressed="false" class="work-chip" data-filter="{{ $key }}" type="button"><svg aria-hidden="true" class="i"><use href="#{{ $category['icon'] }}"></use></svg>{{ $category['label'] }} <span class="work-chip-count">{{ collect($projects)->where('category', $key)->count() }}</span></button>
@endforeach
</div>
<p aria-live="polite" class="work-count" id="work-count"></p>
</div>

{{-- "is-even": the one-column projects fill every row of the two-column grid (the featured one spans a row) --}}
@php($oneColumnCount = collect($projects)->reject(fn ($project) => $project['featured'] ?? false)->count())
<div class="work-grid{{ $oneColumnCount % 2 === 0 ? ' is-even' : '' }}" id="work-grid">
@foreach ($projects as $project)
<x-work.project-card :categories="$categories" :project="$project" />
@endforeach

{{-- Fills the free half of the last row in two columns; work.css hides it when the grid is "is-even",
     and the filter in 10xcart.js updates that class for the projects it shows --}}
<div class="work-next reveal" id="work-next">
<span aria-hidden="true" class="work-next-icon"><svg class="i"><use href="#i-rocket"></use></svg></span>
<p class="work-next-label">Your project next</p>
<h3>Need a store, portal or app like these?</h3>
<p>Tell us what you want to build. You get a clear plan, timeline and price before any work starts.</p>
<div class="work-next-actions">
<a class="btn btn-accent" href="{{ $contactUrl }}">Discuss your project <svg aria-hidden="true" class="i i-arrow"><use href="#i-arrow-right"></use></svg></a>
<a aria-label="Chat with us on WhatsApp (opens in a new tab)" class="btn btn-outline-light" href="{{ $whatsappUrl }}" rel="noopener" target="_blank"><svg aria-hidden="true" class="i"><use href="#i-message"></use></svg>WhatsApp us</a>
</div>
</div>
</div>
</div>
</section>
