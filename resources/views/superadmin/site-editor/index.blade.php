<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="wp-site-editor-html">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <title>Canvas Editor – Landing Page &amp; Konten Publik MAGKOM</title>

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <!-- Google Fonts untuk Editor UI (DS-MagKom Standar) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Plus+Jakarta+Sans:wght@600;700&display=swap" rel="stylesheet">

    <!-- Stylesheet Scoped WordPress Site Editor -->
    @vite(['resources/css/pages/superadmin/site-editor.css'])
</head>
<body class="wp-site-editor-body">

    {{-- 1. TOP BAR EDITOR KHAS WP-ADMIN --}}
    @include('superadmin.site-editor.partials._topbar')

    {{-- 2. WORKSPACE CONTAINER (LIST VIEW + KANVAS TENGAH + RIGHT INSPECTOR) --}}
    <main class="wp-editor-workspace">

        {{-- DRAWER LIST VIEW TERPISAH (KIRI) --}}
        @include('superadmin.site-editor.partials._list_view')

        {{-- AREA KANVAS TENGAH (THE WYSIWYG CANVAS) --}}
        <section class="wp-canvas-area" id="wp-canvas-area">
            {{-- FLOATING TOOLBAR MELAYANG DI ATAS BLOK AKTIF (SUPER MINIMALIS) --}}
            <div id="wp-floating-toolbar" class="wp-floating-toolbar" style="display: none;" role="toolbar" aria-label="Aksi Blok">
                <span id="wp-toolbar-block-name" class="wp-toolbar-chip">Blok: Hero</span>
                <div class="wp-toolbar-divider"></div>

                {{-- 1. Pindah Atas & Bawah --}}
                <button type="button" id="btn-block-up" class="wp-toolbar-btn" title="Pindah ke Atas">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m18 15-6-6-6 6"/></svg>
                </button>
                <button type="button" id="btn-block-down" class="wp-toolbar-btn" title="Pindah ke Bawah">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m6 9 6 6 6-6"/></svg>
                </button>

                <div class="wp-toolbar-divider"></div>

                {{-- 2. Duplikasi Blok --}}
                <button type="button" id="btn-toolbar-duplicate" class="wp-toolbar-btn" title="Duplikasi Blok">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                </button>

                {{-- 3. Hapus Blok --}}
                <button type="button" id="btn-toolbar-delete" class="wp-toolbar-btn wp-btn-danger" title="Hapus Blok">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
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

    {{-- MODAL AKSI PUBLIKASI DAN ROLLBACK (IN-PAGE DIALOG) --}}
    @include('superadmin.site-editor.partials._action_modals')

    {{-- FLOATING TOP NOTIFICATION BAR (STATUS SIMPAN / PERUBAHAN MELAYANG DI ATAS) --}}
    <div id="wp-top-notification-bar" class="wp-top-notification-bar" style="display: none;" role="status" aria-live="polite">
        <span id="wp-top-notification-icon" class="wp-top-notification-icon">✓</span>
        <span id="wp-top-notification-text" class="wp-top-notification-text">Tersinkronisasi</span>
    </div>

    {{-- SISTEM TOAST NOTIFIKASI MENGAMBANG MODERN (REFERENSI GAMBAR 2 - DS-MAGKOM) --}}
    <div id="wp-toast-container" class="toast-floating-container alerts-container" aria-live="polite" aria-atomic="true"></div>

    {{-- HIDDEN INITIAL STATE (PASS DATA TO ES MODULE TANPA INLINE SCRIPT DI BLADE) --}}
    <div id="wp-editor-initial-data" 
         data-layout='@json($blocksLayout)' 
         data-document='@json($pageDocument ?? [])'
         data-registry='@json($blockRegistry)' 
         data-media='@json($mediaLibrary)' 
         data-faq='@json($cmsData["landing_faq_data"] ?? [])'
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
