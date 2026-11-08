{{-- REVISION HISTORY MODAL --}}
<div id="wp-revisions-modal" class="wp-modal-backdrop" style="display: none;" role="dialog" aria-modal="true" aria-labelledby="revisions-modal-title">
    <div class="wp-modal-dialog">
        <div class="wp-modal-header">
            <h3 id="revisions-modal-title" class="wp-modal-title">Riwayat Versi Halaman (Revision History)</h3>
            <button type="button" id="btn-close-revisions-modal" class="wp-icon-btn wp-modal-close-btn" title="Tutup Modal">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        <div class="wp-modal-body">
            <p class="wp-modal-desc">Setiap kali draf disimpan atau diterbitkan, sistem mencatat snapshot kondisi visual dan konten secara otomatis. Milestone publikasi dan rollback diarsipkan permanen, sedangkan riwayat draf disimpan hingga 30 versi terakhir:</p>
            
            <div class="wp-revisions-list" id="wp-revisions-list">
                {{-- Diisi secara dinamis oleh JavaScript --}}
            </div>
        </div>

        <div class="wp-modal-footer">
            <span class="wp-modal-status-text">Memulihkan versi lama akan menimpa draf saat ini dan membuka kanvas dalam kondisi revisi tersebut.</span>
            <button type="button" id="btn-cancel-revisions-modal" class="wp-btn wp-btn-secondary">Tutup</button>
        </div>
    </div>
</div>

