<aside id="wp-inserter-drawer" class="wp-inserter-drawer" aria-label="Daftar Blok, Pola, Media, dan List View" style="display: none;">
    {{-- HEADER DRAWER DENGAN TAB NAVIGASI --}}
    <div class="wp-inserter-top">
        <div class="wp-inserter-nav-tabs" role="tablist">
            <button type="button" class="wp-nav-tab is-active" data-drawer-tab="blocks" role="tab" aria-selected="true">
                Blocks
            </button>
            <button type="button" class="wp-nav-tab" data-drawer-tab="patterns" role="tab" aria-selected="false">
                Patterns
            </button>
            <button type="button" class="wp-nav-tab" data-drawer-tab="reusable" role="tab" aria-selected="false">
                Reusable
            </button>
            <button type="button" class="wp-nav-tab" data-drawer-tab="media" role="tab" aria-selected="false">
                Media
            </button>
        </div>
        <button type="button" id="btn-close-inserter" class="wp-icon-btn" title="Tutup Panel Samping">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    {{-- KOTAK PENCARIAN BLOK --}}
    <div class="wp-inserter-search-wrap">
        <div class="wp-search-input-box">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            <input type="text" id="wp-inserter-search" class="wp-search-input" placeholder="Cari blok..." autocomplete="off">
            <button type="button" id="btn-clear-search" class="wp-clear-search-btn" style="display: none;" title="Hapus pencarian">✕</button>
        </div>
    </div>

    {{-- KONTEN TAB 1: BLOK (BLOCK INSERTER) --}}
    <div id="wp-drawer-pane-blocks" class="wp-drawer-pane is-active" role="tabpanel">
        <div class="wp-inserter-content-scroll">
            {{-- KATEGORI 1: LAYOUT --}}
            <div class="wp-block-category" data-cat="layout">
                <span class="wp-cat-heading">Layout</span>
                <div class="wp-block-grid">
                    <button type="button" class="wp-block-card" data-block-type="hero" draggable="true" title="Tarik atau klik untuk menambahkan Section Hero">
                        <span class="wp-card-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                        </span>
                        <span class="wp-card-title">Section</span>
                    </button>
                    <button type="button" class="wp-block-card" data-block-type="informasi" draggable="true" title="Tarik atau klik untuk menambahkan Kontainer Informasi">
                        <span class="wp-card-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="2"></rect></svg>
                        </span>
                        <span class="wp-card-title">Container</span>
                    </button>
                    <button type="button" class="wp-block-card" data-block-type="tahapan" draggable="true" title="Tarik atau klik untuk menambahkan Kolom Tahapan">
                        <span class="wp-card-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="18" rx="1"></rect><rect x="14" y="3" width="7" height="18" rx="1"></rect></svg>
                        </span>
                        <span class="wp-card-title">Columns</span>
                    </button>
                    <button type="button" class="wp-block-card" data-block-type="footer" draggable="true" title="Tarik atau klik untuk menambahkan Grup Footer">
                        <span class="wp-card-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="8" rx="1"></rect><rect x="2" y="14" width="20" height="8" rx="1"></rect></svg>
                        </span>
                        <span class="wp-card-title">Group</span>
                    </button>
                    <button type="button" class="wp-block-card" data-block-type="spacer" draggable="true" title="Tarik atau klik untuk menambahkan Jarak Kosong (Spacer)">
                        <span class="wp-card-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="7 8 12 3 17 8"></polyline><polyline points="7 16 12 21 17 16"></polyline><line x1="12" y1="3" x2="12" y2="21"></line></svg>
                        </span>
                        <span class="wp-card-title">Spacer</span>
                    </button>
                    <button type="button" class="wp-block-card" data-block-type="spacer" draggable="true" title="Tarik atau klik untuk menambahkan Garis Separator">
                        <span class="wp-card-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line><line x1="12" y1="5" x2="12" y2="19"></line></svg>
                        </span>
                        <span class="wp-card-title">Separator</span>
                    </button>
                </div>
            </div>

            {{-- KATEGORI 2: KONTEN --}}
            <div class="wp-block-category" data-cat="konten">
                <span class="wp-cat-heading">Konten</span>
                <div class="wp-block-grid">
                    <button type="button" class="wp-block-card" data-block-type="cta_banner" draggable="true" title="Tarik atau klik untuk menambahkan Heading CTA">
                        <span class="wp-card-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="4 7 4 4 20 4 20 7"></polyline><line x1="9" y1="20" x2="15" y2="20"></line><line x1="12" y1="4" x2="12" y2="20"></line></svg>
                        </span>
                        <span class="wp-card-title">Heading</span>
                    </button>
                    <button type="button" class="wp-block-card" data-block-type="informasi" draggable="true" title="Tarik atau klik untuk menambahkan Paragraf Persyaratan">
                        <span class="wp-card-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="21" y1="6" x2="3" y2="6"></line><line x1="15" y1="12" x2="3" y2="12"></line><line x1="17" y1="18" x2="3" y2="18"></line></svg>
                        </span>
                        <span class="wp-card-title">Paragraph</span>
                    </button>
                    <button type="button" class="wp-block-card" data-action="open-media" title="Buka Media Library untuk memilih gambar">
                        <span class="wp-card-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                        </span>
                        <span class="wp-card-title">Image</span>
                    </button>
                    <button type="button" class="wp-block-card" data-block-type="cta_banner" draggable="true" title="Tarik atau klik untuk menambahkan Tombol Aksi">
                        <span class="wp-card-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="7" width="18" height="10" rx="3"></rect><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                        </span>
                        <span class="wp-card-title">Button</span>
                    </button>
                    <button type="button" class="wp-block-card" data-block-type="features" draggable="true" title="Tarik atau klik untuk menambahkan List Keunggulan">
                        <span class="wp-card-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                        </span>
                        <span class="wp-card-title">List</span>
                    </button>
                    <button type="button" class="wp-block-card" data-block-type="features" draggable="true" title="Tarik atau klik untuk menambahkan Kartu Ikon">
                        <span class="wp-card-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        </span>
                        <span class="wp-card-title">Icon</span>
                    </button>
                </div>
            </div>

            {{-- KATEGORI 3: LAINNYA --}}
            <div class="wp-block-category" data-cat="lainnya">
                <span class="wp-cat-heading">Lainnya</span>
                <div class="wp-block-grid">
                    <button type="button" class="wp-block-card" data-block-type="faq" draggable="true" title="Tarik atau klik untuk menambahkan FAQ Accordion">
                        <span class="wp-card-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                        </span>
                        <span class="wp-card-title">FAQ</span>
                    </button>
                    <button type="button" class="wp-block-card" data-block-type="features" draggable="true" title="Tarik atau klik untuk menambahkan Testimonial Alumni">
                        <span class="wp-card-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                        </span>
                        <span class="wp-card-title">Testimonial</span>
                    </button>
                    <button type="button" class="wp-block-card" data-action="open-media" title="Buka Galeri Media">
                        <span class="wp-card-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                        </span>
                        <span class="wp-card-title">Gallery</span>
                    </button>
                    <button type="button" class="wp-block-card" data-block-type="cta_banner" draggable="true" title="Tarik atau klik untuk menambahkan Video">
                        <span class="wp-card-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg>
                        </span>
                        <span class="wp-card-title">Video</span>
                    </button>
                    <button type="button" class="wp-block-card" data-action="open-media-pdf" title="Kelola File Panduan PDF">
                        <span class="wp-card-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                        </span>
                        <span class="wp-card-title">File PDF</span>
                    </button>
                    <button type="button" class="wp-block-card" data-block-type="kuota" draggable="true" title="Tarik atau klik untuk menambahkan Tabel Kuota &amp; Form">
                        <span class="wp-card-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"></rect><line x1="7" y1="9" x2="17" y2="9"></line><line x1="7" y1="13" x2="17" y2="13"></line><line x1="7" y1="17" x2="13" y2="17"></line></svg>
                        </span>
                        <span class="wp-card-title">Form</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- FOOTER INSERTER NOTE --}}
        <div class="wp-inserter-footer-tip">
            <span class="wp-tip-icon">ℹ️</span>
            <span>Drag &amp; Drop atau klik untuk menambahkan blok ke halaman</span>
        </div>
    </div>

    {{-- KONTEN TAB 2: PATTERNS (POLA SIAP PAKAI) --}}
    <div id="wp-drawer-pane-patterns" class="wp-drawer-pane" role="tabpanel" style="display: none;">
        <div class="wp-inserter-content-scroll">
            <p class="wp-inserter-desc">Pilih pola section siap pakai yang telah dirancang sesuai standar Pemkab Sleman:</p>

            {{-- FILTER KATEGORI PATTERN --}}
            <div class="wp-pattern-category-filters" role="group" aria-label="Filter Kategori Pola">
                <button type="button" class="wp-cat-filter-btn is-active" data-pattern-filter="all">Semua</button>
                @foreach(($patternCategories ?? ['Hero', 'Informasi', 'FAQ', 'Timeline']) as $cat)
                    <button type="button" class="wp-cat-filter-btn" data-pattern-filter="{{ $cat }}">{{ $cat }}</button>
                @endforeach
            </div>

            <div class="wp-patterns-list" id="wp-patterns-container">
                @foreach($patterns as $patKey => $pat)
                    <div class="wp-pattern-card" data-pattern-id="{{ $pat['id'] }}" data-pattern-key="{{ $pat['key'] ?? $patKey }}" data-category="{{ $pat['category'] }}">
                        <div class="wp-pattern-preview">
                            <div class="wp-pattern-dummy-visual">
                                <span class="wp-pattern-badge">{{ $pat['category'] }}</span>
                                <h5>{{ $pat['name'] }}</h5>
                            </div>
                        </div>
                        <div class="wp-pattern-info">
                            <div class="wp-pattern-header-row">
                                <strong>{{ $pat['name'] }}</strong>
                                @if(!empty($pat['is_system']))
                                    <span class="wp-tag-system" title="Pola resmi sistem">Sistem</span>
                                @else
                                    <span class="wp-tag-custom" title="Pola kustom buatan admin">Kustom</span>
                                @endif
                            </div>
                            <p>{{ $pat['description'] }}</p>
                            <div class="wp-pattern-action-row">
                                <button type="button" class="wp-btn wp-btn-secondary wp-btn-sm btn-preview-pattern" data-pattern-key="{{ $pat['key'] ?? $patKey }}">
                                    Pratinjau
                                </button>
                                <button type="button" class="wp-btn wp-btn-primary wp-btn-sm btn-insert-pattern" data-pattern-key="{{ $pat['key'] ?? $patKey }}">
                                    + Sisipkan
                                </button>
                                @if(empty($pat['is_system']))
                                    <button type="button" class="wp-icon-btn wp-btn-danger wp-btn-sm btn-delete-custom-pattern" data-pattern-id="{{ $pat['id'] }}" title="Hapus Pola Kustom">
                                        🗑️
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- KONTEN TAB 3: REUSABLE BLOCKS (BLOK TERSINKRONISASI) --}}
    <div id="wp-drawer-pane-reusable" class="wp-drawer-pane" role="tabpanel" style="display: none;">
        <div class="wp-inserter-content-scroll">
            <div class="wp-reusable-intro">
                <p class="wp-inserter-desc">Blok yang dapat digunakan di berbagai bagian dan tersinkronisasi secara otomatis saat diedit:</p>
            </div>

            <div class="wp-reusable-list" id="wp-reusable-container">
                @forelse(($reusableBlocks ?? []) as $rBlock)
                    <div class="wp-reusable-card" data-reusable-id="{{ $rBlock['id'] }}">
                        <div class="wp-reusable-info">
                            <div class="wp-reusable-header-row">
                                <span class="wp-reusable-title">{{ $rBlock['title'] }}</span>
                                <span class="wp-badge-block-type">{{ strtoupper($rBlock['block_type']) }}</span>
                            </div>
                            <div class="wp-reusable-meta-row">
                                <span class="wp-reusable-usage-badge {{ ($rBlock['usage_count'] ?? 0) > 0 ? 'is-in-use' : 'is-unused' }}">
                                    📍 Dipakai di {{ $rBlock['usage_count'] ?? 0 }} lokasi
                                </span>
                            </div>
                            <div class="wp-reusable-actions">
                                <button type="button" class="wp-btn wp-btn-primary wp-btn-sm btn-insert-reusable" data-reusable-id="{{ $rBlock['id'] }}">
                                    + Sisipkan Blok
                                </button>
                                <button type="button" class="wp-btn wp-btn-secondary wp-btn-sm btn-view-reusable-usage" data-reusable-id="{{ $rBlock['id'] }}">
                                    Lokasi
                                </button>
                                @if(($rBlock['usage_count'] ?? 0) === 0)
                                    <button type="button" class="wp-icon-btn wp-btn-danger wp-btn-sm btn-delete-reusable" data-reusable-id="{{ $rBlock['id'] }}" title="Hapus Reusable Block">
                                        🗑️
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="wp-empty-reusable-note">
                        <span class="wp-empty-icon">🧩</span>
                        <p>Belum ada Reusable Block tersimpan.</p>
                        <small>Pilih salah satu blok pada kanvas, lalu klik tombol "Jadikan Reusable Block" pada toolbar untuk membuatnya.</small>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- KONTEN TAB 3: MEDIA (QUICK MEDIA PICKER) --}}
    <div id="wp-drawer-pane-media" class="wp-drawer-pane" role="tabpanel" style="display: none;">
        <div class="wp-inserter-content-scroll">
            <div class="wp-media-quick-header">
                <span>Media Terunggah</span>
                <button type="button" class="wp-btn wp-btn-primary wp-btn-sm" id="btn-open-full-media-modal">
                    Buka Media Library
                </button>
            </div>
            <div class="wp-media-quick-grid" id="wp-media-quick-grid">
                @forelse($mediaLibrary as $med)
                    <div class="wp-media-thumb" data-media-id="{{ $med['id'] }}" data-media-url="{{ $med['url'] }}" title="{{ $med['original_name'] }}">
                        @if(str_starts_with($med['mime_type'], 'image/'))
                            <img src="{{ $med['url'] }}" alt="{{ $med['alt_text'] ?: $med['original_name'] }}" loading="lazy">
                        @else
                            <div class="wp-pdf-thumb">📄 PDF</div>
                        @endif
                        <span class="wp-media-thumb-name">{{ Str::limit($med['original_name'], 15) }}</span>
                    </div>
                @empty
                    <div class="wp-empty-media-note">
                        <p>Belum ada media di Media Library.</p>
                        <button type="button" class="wp-btn wp-btn-secondary wp-btn-sm" id="btn-quick-upload">Unggah Berkas</button>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</aside>
