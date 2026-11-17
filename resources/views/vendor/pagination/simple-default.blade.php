@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Navigasi Halaman Sederhana') }}" class="magkom-pagination-nav">
        <ul class="magkom-pagination-list" style="width: 100%; justify-content: space-between;">
            @if ($paginator->onFirstPage())
                <li class="magkom-page-item disabled" aria-disabled="true">
                    <span class="magkom-page-link">&laquo; {{ __('Sebelumnya') }}</span>
                </li>
            @else
                <li class="magkom-page-item">
                    <a class="magkom-page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo; {{ __('Sebelumnya') }}</a>
                </li>
            @endif

            @if ($paginator->hasMorePages())
                <li class="magkom-page-item">
                    <a class="magkom-page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">{{ __('Berikutnya') }} &raquo;</a>
                </li>
            @else
                <li class="magkom-page-item disabled" aria-disabled="true">
                    <span class="magkom-page-link">{{ __('Berikutnya') }} &raquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
