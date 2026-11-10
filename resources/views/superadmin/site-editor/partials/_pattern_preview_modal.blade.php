{{-- MODAL PRATINJAU PATTERN (TASK 7 - PATTERN PREVIEW) --}}
<div id="wp-pattern-preview-modal" class="wp-modal-backdrop" style="display: none;" role="dialog" aria-modal="true" aria-labelledby="pattern-preview-modal-title">
    <div class="wp-modal-dialog wp-modal-lg">
        <div class="wp-modal-header">
            <div>
                <h3 id="pattern-preview-modal-title" class="wp-modal-title">Pratinjau Pola Desain</h3>
                <span id="pattern-preview-badge" class="wp-pattern-badge">Hero</span>
            </div>
            <button type="button" id="btn-close-pattern-preview" class="wp-icon-btn wp-modal-close-btn" title="Tutup Modal Pratinjau">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        <div class="wp-modal-body">
            <div class="wp-pattern-preview-content">
                <div class="wp-pattern-preview-header">
                    <h4 id="pattern-preview-name" class="wp-pattern-preview-name">-</h4>
                    <p id="pattern-preview-desc" class="wp-pattern-preview-desc">-</p>
                </div>
                <div class="wp-pattern-preview-blocks-section">
                    <span class="wp-preview-subheading">Daftar Blok yang Akan Disisipkan:</span>
                    <div id="pattern-preview-blocks-list" class="wp-pattern-preview-blocks-list">
                        {{-- Diisi secara dinamis oleh JavaScript --}}
                    </div>
                </div>
                <div class="wp-pattern-preview-notice">
                    <span class="wp-notice-icon">ℹ️</span>
                    <span>Setiap blok dalam pola akan diinstansiasi sebagai blok independen dengan ID unik. Anda dapat mengubah konten dan gayanya sesuka hati setelah disisipkan tanpa mengubah pola asli.</span>
                </div>
            </div>
        </div>

        <div class="wp-modal-footer">
            <button type="button" id="btn-cancel-pattern-preview" class="wp-btn wp-btn-secondary">
                Batal
            </button>
            <button type="button" id="btn-confirm-insert-pattern" class="wp-btn wp-btn-primary" data-pattern-key="">
                + Sisipkan Pola ke Halaman
            </button>
        </div>
    </div>
</div>

