@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between py-4 px-2 border-t border-slate-200">
        <div class="flex-1 flex justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-xs font-bold text-slate-400 bg-slate-100 rounded-xl cursor-not-allowed">
                    &larr; Previous
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-xs font-bold text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition shadow-sm">
                    &larr; Previous
                </a>
            @endif

            <span class="text-xs font-bold text-slate-700 bg-slate-100 px-3 py-2 rounded-xl font-mono self-center">
                {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
            </span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-xs font-bold text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition shadow-sm">
                    Next &rarr;
                </a>
            @else
                <span class="relative inline-flex items-center px-4 py-2 text-xs font-bold text-slate-400 bg-slate-100 rounded-xl cursor-not-allowed">
                    Next &rarr;
                </span>
            @endif
        </div>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-xs text-slate-500 font-medium">
                    Showing
                    <span class="font-bold text-slate-900">{{ $paginator->firstItem() ?? 0 }}</span>
                    to
                    <span class="font-bold text-slate-900">{{ $paginator->lastItem() ?? 0 }}</span>
                    of
                    <span class="font-bold text-slate-900">{{ $paginator->total() }}</span>
                    results
                </p>
            </div>

            <div class="flex items-center gap-3">
                @if ($paginator->onFirstPage())
                    <span class="px-4 py-2 text-xs font-bold text-slate-400 bg-slate-100 border border-slate-200 rounded-xl cursor-not-allowed flex items-center gap-1.5">
                        <i class="fa-solid fa-arrow-left text-[10px]"></i> Previous
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="px-4 py-2 text-xs font-bold text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 hover:text-slate-900 transition shadow-sm flex items-center gap-1.5">
                        <i class="fa-solid fa-arrow-left text-[10px]"></i> Previous
                    </a>
                @endif

                <span class="text-xs font-bold text-slate-700 bg-slate-100 px-3 py-2 rounded-xl font-mono">
                    Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}
                </span>

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="px-4 py-2 text-xs font-bold text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 hover:text-slate-900 transition shadow-sm flex items-center gap-1.5">
                        Next <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                @else
                    <span class="px-4 py-2 text-xs font-bold text-slate-400 bg-slate-100 border border-slate-200 rounded-xl cursor-not-allowed flex items-center gap-1.5">
                        Next <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </span>
                @endif
            </div>
        </div>
    </nav>
@endif
