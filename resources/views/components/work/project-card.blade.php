{{-- One project on the work page. Data: config/work.php → projects (see the notes at the top of that file). --}}
@props(['project', 'categories'])
@php
    $category = $categories[$project['category']];
    $featured = $project['featured'] ?? false;
    $host = $project['url'] ? preg_replace('/^www\./', '', (string) parse_url($project['url'], PHP_URL_HOST)) : null;
    $headingId = 'work-'.$project['slug'];
    $shotSizes = $featured
        ? ['(min-width: 1024px) 560px, calc(100vw - 72px)', '(min-width: 1024px) 360px, 58vw']
        : ['(min-width: 1024px) 520px, (min-width: 768px) calc(50vw - 90px), calc(100vw - 80px)'];
@endphp
<article aria-labelledby="{{ $headingId }}" class="work-card reveal is-{{ $project['category'] }} {{ $featured ? 'is-featured' : '' }}" data-category="{{ $project['category'] }}">
<div class="work-media is-{{ $project['visual'] }}">
@switch($project['visual'])
@case('screens')
@foreach ($project['images'] as $image)
<div class="browser work-shot">
<div aria-hidden="true" class="browser-bar"><span></span><span></span><span></span><div class="browser-url">{{ $host }}</div></div>
<img alt="{{ $image['alt'] }}" decoding="async" height="855" loading="lazy" sizes="{{ $shotSizes[$loop->index] ?? $shotSizes[0] }}" src="{{ asset('media/work/'.$image['name'].'-640.webp') }}" srcset="{{ asset('media/work/'.$image['name'].'-640.webp') }} 640w, {{ asset('media/work/'.$image['name'].'-960.webp') }} 960w, {{ asset('media/work/'.$image['name'].'.webp') }} 1368w" width="1368"/>
</div>
@endforeach
@break

@case('platform')
<div class="browser work-shot">
<div aria-hidden="true" class="browser-bar"><span></span><span></span><span></span><div class="browser-url">admin.yourstore.com</div></div>
<img alt="10xCart admin dashboard with sales, orders and customer totals and a 30-day sales chart" decoding="async" height="810" loading="lazy" sizes="(min-width: 1024px) 520px, (min-width: 768px) calc(50vw - 90px), calc(100vw - 80px)" src="{{ asset('media/10xcart/admin-dashboard-640.webp') }}" srcset="{{ asset('media/10xcart/admin-dashboard-640.webp') }} 640w, {{ asset('media/10xcart/admin-dashboard-960.webp') }} 960w, {{ asset('media/10xcart/admin-dashboard.webp') }} 1440w" width="1440"/>
</div>
<div class="phone work-phone">
<img alt="Grocery store app home screen built on 10xCart" decoding="async" height="1055" loading="lazy" sizes="150px" src="{{ asset('media/10xcart/screen-grocery-300.webp') }}" srcset="{{ asset('media/10xcart/screen-grocery-240.webp') }} 240w, {{ asset('media/10xcart/screen-grocery-300.webp') }} 300w, {{ asset('media/10xcart/screen-grocery-420.webp') }} 420w" width="488"/>
</div>
@break

@case('flow')
<ol aria-label="How {{ $project['name'] }} works" class="work-flow">
@foreach ($project['flow'] as $step)
<li><span aria-hidden="true" class="wf-icon"><svg class="i"><use href="#{{ $step['icon'] }}"></use></svg></span><span>{{ $step['label'] }}</span></li>
@endforeach
</ol>
@break
@endswitch
</div>

<div class="work-body">
<div class="work-meta">
<span class="work-tag"><svg aria-hidden="true" class="i"><use href="#{{ $category['icon'] }}"></use></svg>{{ $category['label'] }}</span>
@if ($featured)<span class="work-tag is-featured">Featured</span>@endif
@if ($project['period'])<span class="work-period">{{ $project['period'] }}</span>@endif
</div>
<h3 id="{{ $headingId }}">{{ $project['name'] }} <span>{{ $project['title'] }}</span></h3>
<p class="work-client">{{ $project['client'] }}</p>
<p class="work-summary">{{ $project['summary'] }}</p>
<ul class="work-points">
@foreach ($project['highlights'] as $highlight)
<li><svg aria-hidden="true" class="i"><use href="#i-check"></use></svg>{{ $highlight }}</li>
@endforeach
</ul>
@if (! empty($project['note']))<p class="work-note">{{ $project['note'] }}</p>@endif

<div class="work-foot">
@if ($project['metric'])
<p class="work-metric"><strong>{{ $project['metric']['value'] }}</strong> {{ $project['metric']['label'] }}</p>
@endif
<ul aria-label="Built with" class="work-stack">
@foreach ($project['stack'] as $tech)
<li>{{ $tech }}</li>
@endforeach
</ul>
</div>

<div class="work-actions">
@if ($project['url'])
<a aria-label="Visit the {{ $project['name'] }} live site (opens in a new tab)" class="btn btn-primary btn-sm" href="{{ $project['url'] }}" rel="noopener" target="_blank">Visit live site <svg aria-hidden="true" class="i"><use href="#i-external"></use></svg></a>
<span class="work-host">{{ $host }}</span>
@elseif (! empty($project['link']))
<a class="card-link" href="{{ route('home').$project['link']['anchor'] }}">{{ $project['link']['label'] }} <svg aria-hidden="true" class="i"><use href="#i-arrow-right"></use></svg></a>
@endif
@if (! empty($project['private']))
<span class="work-private"><svg aria-hidden="true" class="i"><use href="#i-lock"></use></svg>{{ $project['private'] }}</span>
@endif
</div>
</div>
</article>
