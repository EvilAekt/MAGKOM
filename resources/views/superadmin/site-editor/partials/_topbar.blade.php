<header class="wp-editor-header" role="banner">
    {{-- KIRI: NAVIGASI KEMBALI, BRAND MAGKOM, WORKSPACE BADGE, PAGE SELECTOR, TOOLS --}}
    <div class="wp-header-left">
        {{-- Navigasi Kembali ke Dashboard SuperAdmin --}}
        <a href="{{ route('superadmin.dashboard') }}" class="wp-header-back-btn" title="Kembali ke Dashboard SuperAdmin" aria-label="Kembali ke Dashboard SuperAdmin">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
        </a>

        {{-- Brand Identitas Portal --}}
        <div class="wp-header-brand" title="Portal Magang Kominfo Sleman (MAGKOM)">
            @include('partials.logo-sleman', ['class' => 'wp-logo-img', 'width' => '28', 'height' => '28', 'style' => 'width: 28px; height: 28px; object-fit: contain;'])
            <div class="wp-brand-meta">
                <span class="wp-brand-title">MAGKOM</span>
                <span class="wp-brand-sub">Magang Sleman</span>
            </div>
        </div>

        <span class="wp-breadcrumb-slash" aria-hidden="true">/</span>

        {{-- Workspace Badge Identifier --}}
        <div class="wp-workspace-badge" title="Mode Visual Canvas Editor">
            <span class="wp-workspace-text">Canvas Editor</span>
        </div>

        {{-- Dropdown Pemilihan Halaman Publik yang Sedang Diedit --}}
        <div class="canvas-page-selector-wrap" title="Pilih Halaman Publik yang Sedang Diedit">
            <label for="select-canvas-page" class="sr-only">Pilih Halaman</label>
            <div class="canvas-page-select-box">
                <svg class="canvas-page-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                </svg>
                <select id="select-canvas-page" class="canvas-page-select">
                    <option value="landing" selected>Landing Page (/)</option>
                    <option value="peta-alur">Peta Alur (/peta-alur)</option>
                    <option value="kebijakan-privasi">Kebijakan Privasi (/kebijakan-privasi)</option>
                </select>
                <svg class="canvas-page-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="m6 9 6 6 6-6"></path>
                </svg>
            </div>
        </div>

        <div class="wp-header-vseparator" aria-hidden="true"></div>

        {{-- Group Alat Pembuatan Konten (Inserter, Undo/Redo, List View) --}}
        <div class="wp-creation-tools">
            {{-- Tombol Buka Inserter (+) --}}
            <button type="button" id="btn-toggle-inserter" class="wp-icon-btn wp-btn-inserter" title="Tambah Blok &amp; Pola (+)" aria-label="Buka Inserter Blok">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
            </button>

            {{-- Riwayat Undo / Redo --}}
            <div class="wp-history-buttons">
                <button type="button" id="btn-undo" class="wp-icon-btn" title="Urungkan (Ctrl+Z)" disabled>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 14 4 9l5-5"></path>
                        <path d="M4 9h10.5a5.5 5.5 0 0 1 5.5 5.5v0a5.5 5.5 0 0 1-5.5 5.5H11"></path>
                    </svg>
                </button>
                <button type="button" id="btn-redo" class="wp-icon-btn" title="Ulangi (Ctrl+Y)" disabled>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m15 14 5-5-5-5"></path>
                        <path d="M20 9H9.5A5.5 5.5 0 0 0 4 14.5v0A5.5 5.5 0 0 0 9.5 20H13"></path>
                    </svg>
                </button>
            </div>

            {{-- Buka Struktur List View --}}
            <button type="button" id="btn-list-view" class="wp-icon-btn" title="Pohon Struktur Dokumen (List View)" aria-label="Pohon Struktur Dokumen">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="8" y1="6" x2="21" y2="6"></line>
                    <line x1="8" y1="12" x2="21" y2="12"></line>
                    <line x1="8" y1="18" x2="21" y2="18"></line>
                    <line x1="3" y1="6" x2="3.01" y2="6"></line>
                    <line x1="3" y1="12" x2="3.01" y2="12"></line>
                    <line x1="3" y1="18" x2="3.01" y2="18"></line>
                </svg>
            </button>
        </div>
    </div>

    {{-- TENGAH: SEGMENTED PILL VIEWPORT SWITCHER --}}
    <div class="wp-header-center">
        <div class="wp-viewport-switcher" role="group" aria-label="Mode Pratinjau Layar">
            <button type="button" class="wp-viewport-btn is-active" data-device="desktop" title="Pratinjau Layar Penuh (Desktop)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                    <line x1="8" y1="21" x2="16" y2="21"></line>
                    <line x1="12" y1="17" x2="12" y2="21"></line>
                </svg>
                <span class="wp-viewport-label">Desktop</span>
            </button>
            <button type="button" class="wp-viewport-btn" data-device="tablet" title="Pratinjau Layar Tablet (768px)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect>
                    <line x1="12" y1="18" x2="12.01" y2="18"></line>
                </svg>
                <span class="wp-viewport-label">Tablet</span>
            </button>
            <button type="button" class="wp-viewport-btn" data-device="mobile" title="Pratinjau Layar Ponsel (375px)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
                    <line x1="12" y1="18" x2="12.01" y2="18"></line>
                </svg>
                <span class="wp-viewport-label">Mobile</span>
            </button>
        </div>
    </div>

    {{-- KANAN: STATUS, UTILITY, PUBLISHING ACTIONS, TOGGLE INSPECTOR --}}
    <div class="wp-header-right">
        {{-- Status Penyimpanan Draf Kompak --}}
        <div class="wp-status-container">
            <span id="wp-save-status" class="wp-save-status-pill {{ $draftMeta['active'] ? 'status-draft' : 'status-clean' }}" title="{{ $draftMeta['active'] ? 'Ada perubahan draf sandbox yang belum dipublikasikan' : 'Seluruh data tersinkronisasi dengan versi live' }}">
                {{ $draftMeta['active'] ? 'Draf Sandbox Aktif' : 'Tersinkronisasi' }}
            </span>
        </div>

        {{-- Grup Utilitas (Bantuan, Riwayat, Pratinjau) --}}
        <div class="wp-utility-group">
            <button type="button" id="btn-open-shortcuts" class="wp-icon-btn wp-btn-help" title="Pintasan Papan Ketik (Shift + ?)" aria-label="Bantuan Pintasan Papan Ketik">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
            </button>

            <button type="button" id="btn-open-revisions" class="wp-btn wp-btn-ghost" title="Lihat Riwayat Versi &amp; Snapshot">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 14 14"></polyline>
                </svg>
                <span class="wp-btn-label">Riwayat</span>
            </button>

            <a href="{{ route('superadmin.landing.preview') }}" target="_blank" id="btn-preview-external" class="wp-btn wp-btn-ghost" title="Buka Pratinjau Interaktif di Tab Baru">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                    <polyline points="15 3 21 3 21 9"></polyline>
                    <line x1="10" y1="14" x2="21" y2="3"></line>
                </svg>
                <span class="wp-btn-label">Pratinjau</span>
            </a>
        </div>

        {{-- Grup Aksi Penerbitan (Simpan Draf, Terbitkan, Rollback) --}}
        <div class="wp-publish-group">
            <button type="button" id="btn-save-draft" class="wp-btn wp-btn-secondary" title="Simpan sebagai Draf (Ctrl+S)">
                <span class="btn-spinner" style="display: none;"></span>
                <span>Simpan Draf</span>
            </button>

            <button type="button" id="btn-publish" class="wp-btn wp-btn-primary" title="Mempublikasikan perubahan ke versi live publik">
                <span class="btn-spinner" style="display: none;"></span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14"></path>
                    <path d="m12 5 7 7-7 7"></path>
                </svg>
                <span>Terbitkan</span>
            </button>

            <button type="button" id="btn-rollback" class="wp-btn wp-btn-danger-outline" title="Kembalikan Tampilan Publik ke Versi Sebelum Terbit Terakhir">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="1 4 1 10 7 10"></polyline>
                    <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path>
                </svg>
                <span>Rollback</span>
            </button>
        </div>

        <div class="wp-header-vseparator" aria-hidden="true"></div>

        {{-- Toggle Panel Inspector Kanan --}}
        <button type="button" id="btn-toggle-inspector" class="wp-icon-btn wp-btn-inspector is-active" title="Pengaturan Panel Samping (Settings)" aria-label="Toggle Panel Inspector">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="15" y1="3" x2="15" y2="21"></line>
            </svg>
        </button>
    </div>
</header>
