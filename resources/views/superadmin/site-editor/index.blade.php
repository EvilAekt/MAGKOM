<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="wp-site-editor-html">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Canvas Editor – Landing Page &amp; Konten Publik MAGKOM</title>

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <!-- Google Fonts untuk Editor UI -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Stylesheet Scoped WordPress Site Editor -->
    @vite(['resources/css/pages/superadmin/site-editor.css'])
</head>
<body class="wp-site-editor-body">

    {{-- 1. TOP BAR EDITOR KHAS WP-ADMIN --}}
    @include('superadmin.site-editor.partials._topbar')

    {{-- 2. WORKSPACE CONTAINER (DRAWER + KANVAS TENGAH + RIGHT INSPECTOR) --}}
    <main class="wp-editor-workspace">

        {{-- DRAWER INSERTER (KIRI) --}}
        @include('superadmin.site-editor.partials._inserter')

        {{-- DRAWER LIST VIEW TERPISAH (KIRI) --}}
        @include('superadmin.site-editor.partials._list_view')

        {{-- AREA KANVAS TENGAH (THE WYSIWYG CANVAS) --}}
        <section class="wp-canvas-area" id="wp-canvas-area">
            {{-- FLOATING TOOLBAR MELAYANG DI ATAS BLOK AKTIF (GUTENBERG PARITY) --}}
            <div id="wp-floating-toolbar" class="wp-floating-toolbar" style="display: none;" role="toolbar" aria-label="Aksi Blok">
                <span id="wp-toolbar-block-name" class="wp-toolbar-chip">Blok: Hero</span>
                <div class="wp-toolbar-divider"></div>

                {{-- 1. Drag Handle --}}
                <button type="button" id="btn-toolbar-drag" class="wp-toolbar-btn" title="Tarik untuk mengatur posisi">
                    <span style="font-size: 14px;">⠿</span>
                </button>

                {{-- 2. Tambah Blok (+) --}}
                <button type="button" id="btn-toolbar-add" class="wp-toolbar-btn" title="Tambah Blok di Bawah">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                </button>

                {{-- 3. Buka List View --}}
                <button type="button" id="btn-toolbar-list" class="wp-toolbar-btn" title="Buka Struktur Halaman (List View)">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                </button>

                {{-- 4. Duplikasi Blok --}}
                <button type="button" id="btn-toolbar-duplicate" class="wp-toolbar-btn" title="Duplikasi Blok">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                </button>

                {{-- 5. Hapus Blok --}}
                <button type="button" id="btn-toolbar-delete" class="wp-toolbar-btn wp-btn-danger" title="Hapus Blok">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                </button>

                <div class="wp-toolbar-divider"></div>

                {{-- 6. Pindah Atas & Bawah --}}
                <button type="button" id="btn-block-up" class="wp-toolbar-btn" title="Pindah ke Atas">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m18 15-6-6-6 6"/></svg>
                </button>
                <button type="button" id="btn-block-down" class="wp-toolbar-btn" title="Pindah ke Bawah">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m6 9 6 6 6-6"/></svg>
                </button>

                {{-- 7. Reset Style --}}
                <button type="button" id="btn-block-reset" class="wp-toolbar-btn" title="Kembalikan Pengaturan Gaya Blok Ini">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><path d="M3 3v5h5"></path></svg>
                </button>

                <div class="wp-toolbar-divider"></div>

                {{-- 8. Jadikan Reusable Block (Task 7) --}}
                <button type="button" id="btn-toolbar-make-reusable" class="wp-toolbar-btn" title="Jadikan Reusable Block (Tersinkronisasi)">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="3" width="8" height="8" rx="1.5"></rect><rect x="13" y="13" width="8" height="8" rx="1.5"></rect><path d="M17 7h-4V3"></path><path d="M7 17h4v4"></path></svg>
                </button>

                {{-- 9. Pisahkan Reusable Block (Detach / Convert to Regular) --}}
                <button type="button" id="btn-toolbar-detach-reusable" class="wp-toolbar-btn" style="display: none;" title="Pisahkan dari Reusable Block (Jadikan Blok Reguler Mandiri)">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path><line x1="2" y1="2" x2="22" y2="22"></line></svg>
                </button>
            </div>

            {{-- BINGKAI KANVAS RESPONSIVE (DESKTOP 100% / TABLET 768px / MOBILE 375px) --}}
            <div id="wp-canvas-frame-container" class="wp-canvas-frame-container device-desktop">
                <iframe id="wp-preview-iframe" class="wp-preview-iframe" 
                        src="{{ route('superadmin.landing.preview', ['editor_mode' => 1]) }}" 
                        title="Kanvas Pratinjau Interaktif Canvas Editor"
                        loading="eager">
                </iframe>
            </div>
        </section>

        {{-- RIGHT INSPECTOR PANEL (KANAN) --}}
        @include('superadmin.site-editor.partials._inspector')

    </main>

    {{-- MODAL MEDIA LIBRARY --}}
    @include('superadmin.site-editor.partials._media_modal')

    {{-- MODAL RIWAYAT REVISI --}}
    @include('superadmin.site-editor.partials._revisions_modal')

    {{-- MODAL PEMULIHAN DRAF (RECOVERY) --}}
    @include('superadmin.site-editor.partials._recovery_modal')

    {{-- MODAL PINTASAN PAPAN KETIK (SHORTCUTS) --}}
    @include('superadmin.site-editor.partials._shortcuts_modal')

    {{-- MODAL PRATINJAU PATTERN (TASK 7) --}}
    @include('superadmin.site-editor.partials._pattern_preview_modal')

    {{-- MODAL REUSABLE BLOCKS (TASK 7) --}}
    @include('superadmin.site-editor.partials._reusable_modal')

    {{-- TOAST NOTIFIKASI --}}
    <div id="wp-toast" class="wp-toast" style="display: none;" role="alert">
        <span id="wp-toast-icon" class="wp-toast-icon">✓</span>
        <span id="wp-toast-msg" class="wp-toast-msg">Draf berhasil disimpan</span>
    </div>

    {{-- HIDDEN INITIAL STATE (PASS DATA TO ES MODULE TANPA INLINE SCRIPT DI BLADE) --}}
    <div id="wp-editor-initial-data" 
         data-layout='@json($blocksLayout)' 
         data-document='@json($pageDocument ?? [])'
         data-registry='@json($blockRegistry)' 
         data-patterns='@json($patterns)' 
         data-categories='@json($patternCategories ?? [])'
         data-reusable-blocks='@json($reusableBlocks ?? [])'
         data-revisions='@json($revisions)' 
         data-media='@json($mediaLibrary)' 
         data-responsive-styles='@json($responsiveStyles)'
         data-version="{{ $documentVersion ?? 1 }}"
         data-save-status="{{ $draftMeta['active'] ? 'draft' : 'clean' }}"
         data-author-name="{{ $draftMeta['author_name'] ?? 'Super Admin' }}"
         data-updated-at="{{ $draftMeta['updated_at'] ?? '' }}"
         hidden>
    </div>

    {{-- ES MODULE ENGINE VITE --}}
    @vite(['resources/js/pages/superadmin/site-editor/editor-app.js'])
</body>
</html>
