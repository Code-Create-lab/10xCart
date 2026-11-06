{{-- Social profile links (footer and home contact section). Profiles: config/site.php → socials --}}
<div class="socials">
@foreach (config('site.socials') as $social)
<a aria-label="10xCart on {{ $social['label'] }} (opens in a new tab)" href="{{ $social['url'] }}" rel="noopener" target="_blank"><svg aria-hidden="true" class="i i-fill"><use href="#{{ $social['icon'] }}"></use></svg></a>
@endforeach
</div>
