<aside id="wp-list-view-drawer" class="wp-list-view-drawer wp-sidebar-drawer" aria-label="Pohon Struktur Halaman" style="display: none;">
    {{-- HEADER DRAWER LIST VIEW --}}
    <div class="wp-drawer-header">
        <div class="wp-drawer-title-group">
            <span class="wp-drawer-title">Struktur Halaman</span>
            <small class="wp-listview-count" id="wp-listview-count">Memuat...</small>
        </div>
        <button type="button" id="btn-close-list-view" class="wp-icon-btn" title="Tutup Panel List View" aria-label="Tutup List View">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    {{-- KETERANGAN & PETUNJUK --}}
    <div class="wp-listview-intro">
        <p class="wp-inserter-desc">
            Kelola hierarki dan susunan blok. Tarik handle untuk mengubah urutan atau gunakan tombol aksi.
        </p>
    </div>

    {{-- POHON STRUKTUR DOKUMEN (LIST VIEW TREE) --}}
    <div class="wp-listview-scroll-content">
        <div class="wp-listview-root-item">
            <span class="wp-root-icon">📄</span>
            <span class="wp-root-title">Landing Page MAGKOM</span>
        </div>

        <ul class="wp-listview-tree" id="wp-listview-tree" role="tree" aria-label="Daftar blok berurutan">
            {{-- Dirender secara dinamis oleh BlockManager ES Module dari Page Document --}}
        </ul>
    </div>

    {{-- FOOTER LIST VIEW --}}
    <div class="wp-listview-footer">
        <button type="button" id="btn-listview-add-block" class="wp-btn wp-btn-secondary wp-btn-sm" style="width: 100%;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            <span>Tambah Blok Baru</span>
        </button>
    </div>
</aside>

