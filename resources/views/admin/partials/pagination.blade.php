{{-- Previous / next links for a LengthAwarePaginator ($paginator) --}}
@if ($paginator->hasPages())
<nav class="pagination" aria-label="Pages">
    <span>@if ($paginator->onFirstPage())<span class="muted">← Newer</span>@else<a href="{{ $paginator->previousPageUrl() }}" rel="prev">← Newer</a>@endif</span>
    <span class="muted">Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }} · {{ number_format($paginator->total()) }} total</span>
    <span>@if ($paginator->hasMorePages())<a href="{{ $paginator->nextPageUrl() }}" rel="next">Older →</a>@else<span class="muted">Older →</span>@endif</span>
</nav>
@endif
