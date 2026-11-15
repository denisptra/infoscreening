@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi Halaman">
        <div class="flex gap-2 items-center justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-slate-400 bg-slate-50 border border-slate-200 cursor-not-allowed rounded-lg">
                    Sebelumnya
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 hover:text-[#3A07BB] transition">
                    Sebelumnya
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 hover:text-[#3A07BB] transition">
                    Berikutnya
                </a>
            @else
                <span class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-slate-400 bg-slate-50 border border-slate-200 cursor-not-allowed rounded-lg">
                    Berikutnya
                </span>
            @endif
        </div>

        <div class="hidden sm:flex sm:flex-wrap sm:gap-3 sm:items-center sm:justify-between">
            <div>
                <p class="text-xs text-slate-600 whitespace-nowrap font-mono tabular-nums">
                    Menampilkan
                    @if ($paginator->firstItem())
                        <span class="font-semibold text-slate-800">{{ number_format($paginator->firstItem(), 0, ',', '.') }}</span>–<span class="font-semibold text-slate-800">{{ number_format($paginator->lastItem(), 0, ',', '.') }}</span>
                    @else
                        <span class="font-semibold text-slate-800">{{ $paginator->count() }}</span>
                    @endif
                    dari
                    <span class="font-semibold text-slate-800">{{ number_format($paginator->total(), 0, ',', '.') }}</span>
                    hasil
                </p>
            </div>

            <div>
                <span class="inline-flex items-center shadow-xs rounded-lg overflow-hidden border border-slate-200 bg-white divide-x divide-slate-200">
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="Sebelumnya" class="inline-flex items-center px-2.5 py-2 text-xs text-slate-300 bg-slate-50 cursor-not-allowed" aria-hidden="true">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-2.5 py-2 text-xs text-slate-600 bg-white hover:bg-slate-50 hover:text-[#3A07BB] transition" aria-label="Sebelumnya">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif

                    @foreach ($elements as $element)
                        @if (is_string($element))
                            <span aria-disabled="true" class="inline-flex items-center px-3 py-2 text-xs font-medium text-slate-400 bg-white cursor-default">{{ $element }}</span>
                        @endif

                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page" class="inline-flex items-center px-3.5 py-2 text-xs font-semibold text-white bg-[#3A07BB] cursor-default">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-slate-700 bg-white hover:bg-slate-50 hover:text-[#3A07BB] transition" aria-label="Halaman {{ $page }}">{{ $page }}</a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-2.5 py-2 text-xs text-slate-600 bg-white hover:bg-slate-50 hover:text-[#3A07BB] transition" aria-label="Berikutnya">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="Berikutnya" class="inline-flex items-center px-2.5 py-2 text-xs text-slate-300 bg-slate-50 cursor-not-allowed" aria-hidden="true">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
