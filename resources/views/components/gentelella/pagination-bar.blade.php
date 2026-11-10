@props([
    'paginator',
    'perPageOptions' => [5, 10, 25],
    'entity' => 'data',
    'badgeText' => null,
])

@php
    $currentPage = $paginator->currentPage();
    $lastPage = max(1, $paginator->lastPage());
    $currentPerPage = (int) $paginator->perPage();

    // Sliding window hingga 5 nomor halaman
    $startPage = max(1, $currentPage - 2);
    $endPage = min($lastPage, max($startPage + 4, $currentPage + 2));
    if ($endPage - $startPage < 4 && $lastPage >= 5) {
        $startPage = max(1, $endPage - 4);
    }
@endphp

<div {{ $attributes->merge(['class' => 'kuota-pagination-wrapper adm-pagination-wrapper']) }}>
    {{-- BARIS 1: Info Counter (Kiri) & Tombol Pilihan Menampilkan (Kanan) --}}
    <div class="kuota-pagination-row kuota-pagination-top">
        <div class="kuota-pagination-summary">
            <span class="kuota-pagination-counter">
                Menampilkan <strong>{{ $paginator->firstItem() ?? 0 }}</strong> - <strong>{{ $paginator->lastItem() ?? 0 }}</strong> dari <strong>{{ number_format($paginator->total()) }}</strong> {{ $entity }}
            </span>
            @if($badgeText)
                <span class="kuota-immutable-pill">
                    <x-sa.icon name="check-circle" :size="13" />
                    <span>{{ $badgeText }}</span>
                </span>
            @endif
        </div>

        <div class="kuota-per-page-container">
            <span class="kuota-per-page-label">Menampilkan:</span>
            <div class="kuota-per-page-group" role="group" aria-label="Pilih jumlah data per halaman">
                @foreach($perPageOptions as $size)
                    @php
                        $isActive = ($currentPerPage === (int) $size);
                        $url = request()->fullUrlWithQuery(['per_page' => $size, 'page' => 1]);
                    @endphp
                    <a href="{{ $url }}"
                       class="kuota-per-page-pill {{ $isActive ? 'active' : '' }}"
                       title="Tampilkan {{ $size }} {{ $entity }} per halaman"
                       @if($isActive) aria-current="true" @endif>
                        {{ $size }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- BARIS 2: Info Lembar Halaman (Kiri) & Navigasi Prev/Next/Numbers (Kanan) --}}
    <div class="kuota-pagination-row kuota-pagination-bottom">
        <div class="kuota-pagination-lembar-info">
            Lembar Halaman <strong>{{ $currentPage }}</strong> dari <strong>{{ $lastPage }}</strong>
        </div>

        <div class="kuota-pagination-right">
            <div class="kuota-page-controls" role="navigation" aria-label="Navigasi Lembar Halaman">
                {{-- Tombol Sebelumnya --}}
                @if($paginator->onFirstPage())
                    <button type="button" class="kuota-page-btn-nav disabled" disabled aria-label="Lembar Sebelumnya">&laquo; Prev</button>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="kuota-page-btn-nav" aria-label="Lembar Sebelumnya">&laquo; Prev</a>
                @endif

                {{-- Angka Halaman (Sliding Window) --}}
                <div class="kuota-page-numbers" style="display: inline-flex; align-items: center; gap: 0.25rem;">
                    @if($startPage > 1)
                        <a href="{{ $paginator->url(1) }}" class="kuota-page-num" aria-label="Lembar 1">1</a>
                        @if($startPage > 2)
                            <span class="kuota-page-ellipsis" aria-hidden="true">&hellip;</span>
                        @endif
                    @endif

                    @for($page = $startPage; $page <= $endPage; $page++)
                        @if($page == $currentPage)
                            <span class="kuota-page-num active" aria-current="page" aria-label="Lembar {{ $page }}">{{ $page }}</span>
                        @else
                            <a href="{{ $paginator->url($page) }}" class="kuota-page-num" aria-label="Lembar {{ $page }}">{{ $page }}</a>
                        @endif
                    @endfor

                    @if($endPage < $lastPage)
                        @if($endPage < $lastPage - 1)
                            <span class="kuota-page-ellipsis" aria-hidden="true">&hellip;</span>
                        @endif
                        <a href="{{ $paginator->url($lastPage) }}" class="kuota-page-num" aria-label="Lembar {{ $lastPage }}">{{ $lastPage }}</a>
                    @endif
                </div>

                {{-- Tombol Selanjutnya --}}
                @if($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="kuota-page-btn-nav" aria-label="Lembar Selanjutnya">Next &raquo;</a>
                @else
                    <button type="button" class="kuota-page-btn-nav disabled" disabled aria-label="Lembar Selanjutnya">Next &raquo;</button>
                @endif
            </div>
        </div>
    </div>
</div>

