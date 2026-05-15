@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-center gap-1">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span
                class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 dark:text-slate-500 cursor-not-allowed">
                <i class="fas fa-chevron-left text-xs"></i>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
                class="dashboard-pg-link w-9 h-9 flex items-center justify-center rounded-lg text-gray-600 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-slate-600 hover:text-blue-600 dark:hover:text-blue-400 transition-colors focus:outline-none">
                <i class="fas fa-chevron-left text-xs"></i>
            </a>
        @endif

        {{-- Pagination Elements --}}
        @php
            $start = max($paginator->currentPage() - 2, 1);
            $end = min($start + 4, $paginator->lastPage());
            if ($end - $start < 4) {
                $start = max($end - 4, 1);
            }
        @endphp

        {{-- First Page + Ellipsis --}}
        @if ($start > 1)
            <a href="{{ $paginator->url(1) }}"
                class="dashboard-pg-link w-9 h-9 flex items-center justify-center rounded-lg text-sm font-medium text-gray-600 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-slate-600 hover:text-blue-600 dark:hover:text-blue-400 transition-colors focus:outline-none">
                1
            </a>
            @if ($start > 2)
                <span class="w-9 h-9 flex items-center justify-center text-gray-400 dark:text-slate-500 text-sm">...</span>
            @endif
        @endif

        {{-- Page Numbers --}}
        @for ($page = $start; $page <= $end; $page++)
            @if ($page == $paginator->currentPage())
                <span
                    class="w-9 h-9 flex items-center justify-center rounded-lg text-sm font-bold bg-blue-600 text-white shadow-sm">
                    {{ $page }}
                </span>
            @else
                <a href="{{ $paginator->url($page) }}"
                    class="dashboard-pg-link w-9 h-9 flex items-center justify-center rounded-lg text-sm font-medium text-gray-600 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-slate-600 hover:text-blue-600 dark:hover:text-blue-400 transition-colors focus:outline-none">
                    {{ $page }}
                </a>
            @endif
        @endfor

        {{-- Last Page + Ellipsis --}}
        @if ($end < $paginator->lastPage())
            @if ($end < $paginator->lastPage() - 1)
                <span class="w-9 h-9 flex items-center justify-center text-gray-400 dark:text-slate-500 text-sm">...</span>
            @endif
            <a href="{{ $paginator->url($paginator->lastPage()) }}"
                class="dashboard-pg-link w-9 h-9 flex items-center justify-center rounded-lg text-sm font-medium text-gray-600 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-slate-600 hover:text-blue-600 dark:hover:text-blue-400 transition-colors focus:outline-none">
                {{ $paginator->lastPage() }}
            </a>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
                class="dashboard-pg-link w-9 h-9 flex items-center justify-center rounded-lg text-gray-600 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-slate-600 hover:text-blue-600 dark:hover:text-blue-400 transition-colors focus:outline-none">
                <i class="fas fa-chevron-right text-xs"></i>
            </a>
        @else
            <span
                class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 dark:text-slate-500 cursor-not-allowed">
                <i class="fas fa-chevron-right text-xs"></i>
            </span>
        @endif
    </nav>
@endif
