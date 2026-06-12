@if ($paginator && $paginator->hasPages())
    <!-- Nút Previous -->
    @if (!$paginator->onFirstPage())
        <button class="pagination-btn prev" onclick="location.href='{{ $paginator->previousPageUrl() }}'">
            <i class="ti ti-arrow-left" aria-hidden="true"></i>
        </button>
    @endif

    <!-- Nút phân trang -->
    @if ($paginator->currentPage() > 3)
        <button class="pagination-btn" onclick="location.href='{{ $paginator->url(1) }}'">1</button>
        @if ($paginator->currentPage() > 4)
            <span class="pagination-ellipsis">...</span>
        @endif
    @endif

    @foreach (range(1, $paginator->lastPage()) as $i)
        @if ($i >= $paginator->currentPage() - 2 && $i <= $paginator->currentPage() + 2)
            <button onclick="location.href='{{ $paginator->url($i) }}'"
                class="pagination-btn @if ($i == $paginator->currentPage()) active @endif">
                {{ $i }}
            </button>
        @endif
    @endforeach

    @if ($paginator->currentPage() < $paginator->lastPage() - 2)
        @if ($paginator->currentPage() < $paginator->lastPage() - 3)
            <span class="pagination-ellipsis">...</span>
        @endif
        <button class="pagination-btn"
            onclick="location.href='{{ $paginator->url($paginator->lastPage()) }}'">{{ $paginator->lastPage() }}</button>
    @endif

    <!-- Nút Next -->
    @if ($paginator->hasMorePages())
        <button class="pagination-btn next" onclick="location.href='{{ $paginator->nextPageUrl() }}'">
            <i class="ti ti-arrow-right" aria-hidden="true"></i>
        </button>
    @endif
@endif
