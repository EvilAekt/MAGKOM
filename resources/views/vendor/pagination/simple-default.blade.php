@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi Halaman Sederhana" class="magkom-pagination-nav">
        <ul class="magkom-pagination-list" style="width: 100%; justify-content: space-between;">
            @if ($paginator->onFirstPage())
                <li class="magkom-page-item disabled" aria-disabled="true">
                    <span class="magkom-page-link">&laquo; Sebelumnya</span>
                </li>
            @else
                <li class="magkom-page-item">
                    <a class="magkom-page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo; Sebelumnya</a>
                </li>
            @endif

            @if ($paginator->hasMorePages())
                <li class="magkom-page-item">
                    <a class="magkom-page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">Berikutnya &raquo;</a>
                </li>
            @else
                <li class="magkom-page-item disabled" aria-disabled="true">
                    <span class="magkom-page-link">Berikutnya &raquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
