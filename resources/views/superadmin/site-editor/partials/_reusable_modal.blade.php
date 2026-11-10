{{-- MODAL PEMBUATAN REUSABLE BLOCK (TASK 7 - CREATE REUSABLE BLOCK) --}}
<div id="wp-create-reusable-modal" class="wp-modal-backdrop" style="display: none;" role="dialog" aria-modal="true" aria-labelledby="create-reusable-modal-title">
    <div class="wp-modal-dialog wp-modal-md">
        <div class="wp-modal-header">
            <h3 id="create-reusable-modal-title" class="wp-modal-title">🧩 Buat Reusable Block</h3>
            <button type="button" id="btn-close-create-reusable" class="wp-icon-btn wp-modal-close-btn" title="Tutup Modal">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        <div class="wp-modal-body">
            <form id="wp-create-reusable-form" onsubmit="return false;">
                <div class="wp-form-group">
                    <label for="reusable-title-input" class="wp-form-label">Nama Reusable Block <span class="wp-required">*</span></label>
                    <input type="text" id="reusable-title-input" class="wp-input" placeholder="Misal: CTA Pendaftaran Seleksi 2026" required maxlength="100">
                    <small class="wp-help-text">Nama unik untuk memudahkan pencarian di daftar Reusable Block.</small>
                </div>

                <div class="wp-form-group">
                    <label for="reusable-category-input" class="wp-form-label">Kategori</label>
                    <input type="text" id="reusable-category-input" class="wp-input" placeholder="Misal: CTA, Informasi, dll" value="Umum" maxlength="50">
                </div>

                <div class="wp-reusable-source-info">
                    <span class="wp-info-label">Blok Sumber:</span>
                    <span id="reusable-source-block-type" class="wp-badge-block-type">CTA Banner</span>
                </div>

                <div class="wp-pattern-preview-notice">
                    <span class="wp-notice-icon">🔄</span>
                    <span>Reusable Block akan tersimpan di pustaka terpusat. Mengubah konten blok ini di masa depan akan otomatis memperbarui seluruh instance di halaman mana pun yang menggunakannya.</span>
                </div>
            </form>
        </div>

        <div class="wp-modal-footer">
            <button type="button" id="btn-cancel-create-reusable" class="wp-btn wp-btn-secondary">
                Batal
            </button>
            <button type="button" id="btn-confirm-create-reusable" class="wp-btn wp-btn-primary">
                Simpan Reusable Block
            </button>
        </div>
    </div>
</div>

{{-- MODAL INFO PENGGUNAAN REUSABLE BLOCK (USAGE TRACKING & DELETE PROTECTION) --}}
<div id="wp-reusable-usage-modal" class="wp-modal-backdrop" style="display: none;" role="dialog" aria-modal="true" aria-labelledby="reusable-usage-modal-title">
    <div class="wp-modal-dialog wp-modal-md">
        <div class="wp-modal-header">
            <h3 id="reusable-usage-modal-title" class="wp-modal-title">📍 Lokasi Penggunaan Reusable Block</h3>
            <button type="button" id="btn-close-reusable-usage" class="wp-icon-btn wp-modal-close-btn" title="Tutup Modal">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        <div class="wp-modal-body">
            <div id="wp-reusable-usage-content">
                <p id="reusable-usage-title" class="wp-pattern-preview-name">-</p>
                <div class="wp-reusable-usage-list" id="reusable-usage-items">
                    {{-- Diisi secara dinamis --}}
                </div>
            </div>
        </div>

        <div class="wp-modal-footer">
            <button type="button" id="btn-close-reusable-usage-footer" class="wp-btn wp-btn-secondary">
                Tutup
            </button>
        </div>
    </div>
</div>

