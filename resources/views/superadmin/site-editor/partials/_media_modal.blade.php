{{-- MEDIA LIBRARY MODAL --}}
<div id="wp-media-modal" class="wp-modal-backdrop" style="display: none;" role="dialog" aria-modal="true" aria-labelledby="media-modal-title">
    <div class="wp-modal-dialog wp-modal-lg">
        <div class="wp-modal-header">
            <div class="wp-modal-tabs" role="tablist">
                <button type="button" class="wp-modal-tab is-active" data-media-tab="browse" role="tab">Pustaka Media</button>
                <button type="button" class="wp-modal-tab" data-media-tab="upload" role="tab">Unggah Berkas Baru</button>
            </div>
            <button type="button" id="btn-close-media-modal" class="wp-icon-btn wp-modal-close-btn" title="Tutup Modal">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        <div class="wp-modal-body">
            {{-- TAB 1: BROWSE MEDIA --}}
            <div id="wp-media-pane-browse" class="wp-modal-pane is-active">
                {{-- FILTER BAR --}}
                <div class="wp-media-filter-bar">
                    <div class="wp-media-type-filter">
                        <button type="button" class="wp-filter-chip is-active" data-media-type-filter="">Semua Media</button>
                        <button type="button" class="wp-filter-chip" data-media-type-filter="image">Foto / Gambar</button>
                        <button type="button" class="wp-filter-chip" data-media-type-filter="pdf">Dokumen PDF</button>
                    </div>
                    <div class="wp-media-search-box">
                        <input type="text" id="wp-media-search-input" class="wp-text-input wp-input-sm" placeholder="Cari media..." autocomplete="off">
                    </div>
                </div>

                <div class="wp-media-grid-wrap">
                    <div class="wp-media-grid" id="wp-media-library-grid">
                        {{-- Diisi secara dinamis oleh JavaScript --}}
                    </div>
                </div>
            </div>

            {{-- TAB 2: UPLOAD BERKAS BARU --}}
            <div id="wp-media-pane-upload" class="wp-modal-pane" style="display: none;">
                <div class="wp-upload-dropzone" id="wp-upload-dropzone">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                    <h4>Tarik Berkas ke Sini untuk Mengunggah</h4>
                    <p>atau klik tombol di bawah untuk memilih file dari komputer Anda</p>
                    <input type="file" id="wp-media-file-input" class="wp-sr-only" accept="image/jpeg,image/png,image/webp,image/svg+xml,application/pdf">
                    <button type="button" id="btn-browse-file" class="wp-btn wp-btn-primary">
                        Pilih Berkas
                    </button>
                    <small class="wp-upload-hint">Format yang didukung: JPG, PNG, WEBP, SVG, PDF. Ukuran berkas maksimal 5 MB.</small>
                    <div id="wp-upload-spinner" class="wp-upload-progress" style="display: none;">
                        <span class="btn-spinner"></span>
                        <span>Sedang mengunggah berkas...</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="wp-modal-footer">
            <span id="wp-media-modal-status" class="wp-modal-status-text">Pilih berkas yang ingin digunakan.</span>
            <div class="wp-modal-btn-row">
                <button type="button" id="btn-delete-selected-media" class="wp-btn wp-btn-danger" style="display: none;">Hapus Berkas</button>
                <button type="button" id="btn-cancel-media-modal" class="wp-btn wp-btn-secondary">Batal</button>
                <button type="button" id="btn-select-media-action" class="wp-btn wp-btn-primary" disabled>
                    Gunakan Berkas Ini
                </button>
            </div>
        </div>
    </div>
</div>

