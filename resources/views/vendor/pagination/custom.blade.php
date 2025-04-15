@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center space-x-2">
        {{-- 前のページ --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-1 text-gray-400 border rounded">&lsaquo;</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
               class="px-3 py-1 text-gray-700 border rounded hover:bg-gray-200">
                &lsaquo;
            </a>
        @endif

        {{-- ページ番号 --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" の省略記号 --}}
            @if (is_string($element))
                <span class="px-3 py-1 text-gray-400">{{ $element }}</span>
            @endif

            {{-- ページ番号リンク --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="px-3 py-1 bg-blue-500 text-white border rounded">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="px-3 py-1 text-gray-700 border rounded hover:bg-gray-200">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- 次のページ --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next"
               class="px-3 py-1 text-gray-700 border rounded hover:bg-gray-200">
                &rsaquo;
            </a>
        @else
            <span class="px-3 py-1 text-gray-400 border rounded">&rsaquo;</span>
        @endif
    </nav>
@endif
