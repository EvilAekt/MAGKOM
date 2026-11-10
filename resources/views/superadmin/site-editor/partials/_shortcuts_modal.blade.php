{{-- KEYBOARD SHORTCUTS MODAL (TASK 5) --}}
<div id="wp-shortcuts-modal" class="wp-modal-backdrop" style="display: none;" role="dialog" aria-modal="true" aria-labelledby="shortcuts-modal-title">
    <div class="wp-modal-dialog wp-modal-md">
        <div class="wp-modal-header">
            <h3 id="shortcuts-modal-title" class="wp-modal-title">⌨️ Pintasan Papan Ketik (Keyboard Shortcuts)</h3>
            <button type="button" id="btn-close-shortcuts-modal" class="wp-icon-btn wp-modal-close-btn" title="Tutup Modal">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        <div class="wp-modal-body">
            <div class="wp-shortcuts-list">
                <div class="wp-shortcut-row">
                    <span class="wp-shortcut-label">Simpan Draf (Save Draft)</span>
                    <kbd class="wp-kbd">Ctrl + S</kbd>
                </div>
                <div class="wp-shortcut-row">
                    <span class="wp-shortcut-label">Batalkan Perubahan (Undo)</span>
                    <kbd class="wp-kbd">Ctrl + Z</kbd>
                </div>
                <div class="wp-shortcut-row">
                    <span class="wp-shortcut-label">Ulangi Perubahan (Redo)</span>
                    <div class="wp-kbd-group">
                        <kbd class="wp-kbd">Ctrl + Y</kbd>
                        <span class="wp-kbd-sep">atau</span>
                        <kbd class="wp-kbd">Ctrl + Shift + Z</kbd>
                    </div>
                </div>
                <div class="wp-shortcut-row">
                    <span class="wp-shortcut-label">Hapus Blok Terpilih</span>
                    <kbd class="wp-kbd">Delete / Backspace</kbd>
                </div>
                <div class="wp-shortcut-row">
                    <span class="wp-shortcut-label">Batalkan Seleksi / Tutup Panel</span>
                    <kbd class="wp-kbd">Esc</kbd>
                </div>
                <div class="wp-shortcut-row">
                    <span class="wp-shortcut-label">Buka Bantuan Pintasan</span>
                    <kbd class="wp-kbd">Shift + ?</kbd>
                </div>
            </div>
        </div>

        <div class="wp-modal-footer">
            <button type="button" id="btn-close-shortcuts-footer" class="wp-btn wp-btn-secondary">
                Tutup
            </button>
        </div>
    </div>
</div>

