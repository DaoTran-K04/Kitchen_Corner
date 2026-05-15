@if ($paginator->hasPages())
    <nav class="flex items-center justify-center gap-1" role="navigation" aria-label="Pagination Navigation">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center justify-center w-10 h-10 text-gray-300 cursor-not-allowed rounded-lg">
                <i class="fas fa-chevron-left text-sm"></i>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" 
               class="inline-flex items-center justify-center w-10 h-10 text-gray-600 hover:text-white hover:bg-brand-green rounded-lg transition-all duration-200 shadow-sm border border-gray-100 hover:border-brand-green bg-white"
               rel="prev" aria-label="@lang('pagination.previous')">
                <i class="fas fa-chevron-left text-sm"></i>
            </a>
        @endif

        {{-- Pagination Elements --}}
        <div class="hidden sm:flex items-center gap-1">
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="inline-flex items-center justify-center w-10 h-10 text-gray-400 text-sm font-medium">
                        {{ $element }}
                    </span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="inline-flex items-center justify-center w-10 h-10 bg-gradient-to-r from-brand-green to-emerald-600 text-white font-bold text-sm rounded-lg shadow-md" aria-current="page">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" 
                               class="inline-flex items-center justify-center w-10 h-10 text-gray-600 hover:text-white hover:bg-brand-green font-medium text-sm rounded-lg transition-all duration-200 shadow-sm border border-gray-100 hover:border-brand-green bg-white"
                               aria-label="@lang('pagination.goto_page', ['page' => $page])">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        {{-- Mobile: Current Page Info --}}
        <div class="sm:hidden px-4 py-2 text-sm text-gray-600 font-medium">
            <span class="text-brand-green font-bold">{{ $paginator->currentPage() }}</span>
            <span class="mx-1">/</span>
            <span>{{ $paginator->lastPage() }}</span>
        </div>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" 
               class="inline-flex items-center justify-center w-10 h-10 text-gray-600 hover:text-white hover:bg-brand-green rounded-lg transition-all duration-200 shadow-sm border border-gray-100 hover:border-brand-green bg-white"
               rel="next" aria-label="@lang('pagination.next')">
                <i class="fas fa-chevron-right text-sm"></i>
            </a>
        @else
            <span class="inline-flex items-center justify-center w-10 h-10 text-gray-300 cursor-not-allowed rounded-lg">
                <i class="fas fa-chevron-right text-sm"></i>
            </span>
        @endif
    </nav>
@endif
