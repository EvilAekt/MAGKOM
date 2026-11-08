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

                <div class="wp-media-split-layout">
                    {{-- GRID MEDIA (KIRI) --}}
                    <div class="wp-media-grid-wrap">
                        <div class="wp-media-grid" id="wp-media-library-grid">
                            {{-- Diisi secara dinamis oleh JavaScript --}}
                        </div>
                    </div>

                    {{-- SIDEBAR DETAIL MEDIA (KANAN) --}}
                    <div class="wp-media-detail-sidebar" id="wp-media-detail-sidebar" style="display: none;">
                        <h5>Detail Lampiran</h5>
                        <div class="wp-media-sidebar-preview">
                            <img id="wp-sidebar-thumb" src="" alt="Pratinjau Media" style="display: none;">
                            <div id="wp-sidebar-pdf-preview" style="display: none;" class="wp-pdf-icon-lg">📄 PDF Dokumen</div>
                        </div>

                        <div class="wp-media-meta-info">
                            <strong id="wp-sidebar-filename">nama-file.jpg</strong>
                            <div class="wp-media-meta-pills">
                                <span id="wp-sidebar-filesize" class="wp-meta-pill">0 KB</span>
                                <span id="wp-sidebar-dimensions" class="wp-meta-pill" style="display: none;">-</span>
                            </div>
                            <span id="wp-sidebar-filedate" class="wp-meta-date">Tanggal</span>
                            <a id="wp-sidebar-download-link" href="#" target="_blank" rel="noopener noreferrer" class="wp-meta-download-link">
                                ⬇️ Unduh Berkas Asli
                            </a>
                        </div>

                        {{-- INFORMASI PENGGUNAAN BERKAS (USAGE TRACKING) --}}
                        <div class="wp-media-usage-box" id="wp-media-usage-box">
                            <span class="wp-usage-title">Penggunaan di Halaman:</span>
                            <div id="wp-sidebar-usage-list" class="wp-usage-list">
                                <span class="wp-usage-empty">Belum digunakan di Landing Page</span>
                            </div>
                        </div>

                        <form id="form-media-meta" class="wp-media-meta-form">
                            <input type="hidden" id="wp-sidebar-media-id">
                            <div class="wp-field-group">
                                <label for="wp-sidebar-alt">Teks Alternatif (Alt Text)</label>
                                <input type="text" id="wp-sidebar-alt" class="wp-text-input wp-input-sm" placeholder="Deskripsi untuk aksesibilitas">
                            </div>
                            <div class="wp-field-group">
                                <label for="wp-sidebar-caption">Keterangan (Caption)</label>
                                <input type="text" id="wp-sidebar-caption" class="wp-text-input wp-input-sm" placeholder="Keterangan singkat">
                            </div>
                            <div class="wp-field-group">
                                <label for="wp-sidebar-title">Judul (Title)</label>
                                <input type="text" id="wp-sidebar-title" class="wp-text-input wp-input-sm" placeholder="Judul berkas atau dokumen">
                            </div>
                            <div class="wp-field-group">
                                <label for="wp-sidebar-description">Deskripsi Lengkap</label>
                                <textarea id="wp-sidebar-description" class="wp-text-input wp-input-sm" rows="2" placeholder="Catatan atau deskripsi berkas"></textarea>
                            </div>
                            <button type="button" id="btn-save-media-meta" class="wp-btn wp-btn-secondary wp-btn-sm">
                                Simpan Keterangan
                            </button>
                        </form>

                        <div class="wp-media-sidebar-actions">
                            <input type="file" id="wp-replace-file-input" class="wp-sr-only" accept="image/jpeg,image/png,image/webp,image/svg+xml,application/pdf">
                            <button type="button" id="btn-replace-media" class="wp-btn wp-btn-secondary wp-btn-sm" title="Ganti berkas ini dengan file baru tanpa merusak referensi">
                                Ganti Berkas
                            </button>
                            <button type="button" id="btn-delete-media" class="wp-btn wp-btn-danger wp-btn-sm" title="Hapus berkas ini jika tidak digunakan">
                                Hapus Berkas
                            </button>
                        </div>
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
            <span id="wp-media-modal-status" class="wp-modal-status-text">Pilih media yang ingin digunakan.</span>
            <div class="wp-modal-btn-row">
                <button type="button" id="btn-cancel-media-modal" class="wp-btn wp-btn-secondary">Batal</button>
                <button type="button" id="btn-select-media-action" class="wp-btn wp-btn-primary" disabled>
                    Gunakan Media Ini
                </button>
            </div>
        </div>
    </div>
</div>

