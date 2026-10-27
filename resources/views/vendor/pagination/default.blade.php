@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi Halaman" class="magkom-pagination-nav">
        {{-- Ringkasan Jumlah Data --}}
        <div class="magkom-pagination-summary">
            <span>
                Menampilkan
                <strong>{{ $paginator->firstItem() ?? 0 }}</strong>
                sampai
                <strong>{{ $paginator->lastItem() ?? 0 }}</strong>
                dari
                <strong>{{ $paginator->total() }}</strong>
                entri
            </span>
        </div>

        {{-- Tombol Halaman --}}
        <ul class="magkom-pagination-list">
            {{-- Tombol Sebelumnya --}}
            @if ($paginator->onFirstPage())
                <li class="magkom-page-item disabled" aria-disabled="true">
                    <span class="magkom-page-link">&laquo; Sebelumnya</span>
                </li>
            @else
                <li class="magkom-page-item">
                    <a class="magkom-page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo; Sebelumnya</a>
                </li>
            @endif

            {{-- Elemen Nomor Halaman --}}
            @foreach ($elements as $element)
                {{-- Separator Titik Tiga (...) --}}
                @if (is_string($element))
                    <li class="magkom-page-item disabled" aria-disabled="true">
                        <span class="magkom-page-link">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Tautan Nomor Halaman --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="magkom-page-item active" aria-current="page">
                                <span class="magkom-page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="magkom-page-item">
                                <a class="magkom-page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Tombol Berikutnya --}}
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
