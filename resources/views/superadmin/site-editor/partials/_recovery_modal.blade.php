{{-- DRAFT RECOVERY MODAL (TASK 5) --}}
<div id="wp-recovery-modal" class="wp-modal-backdrop" style="display: none;" role="dialog" aria-modal="true" aria-labelledby="recovery-modal-title">
    <div class="wp-modal-dialog wp-modal-md">
        <div class="wp-modal-header">
            <h3 id="recovery-modal-title" class="wp-modal-title">🔄 Pemulihan Draf Lokal Ditemukan</h3>
            <button type="button" id="btn-close-recovery-modal" class="wp-icon-btn wp-modal-close-btn" title="Tutup Modal">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        <div class="wp-modal-body">
            <div class="wp-recovery-alert">
                <p>Sistem mendeteksi adanya perubahan tata letak dan konten di browser Anda yang belum tersimpan ke server sebelum sesi sebelumnya tertutup.</p>
                <div class="wp-recovery-meta-box">
                    <div><strong>Waktu Perubahan Lokal:</strong> <span id="wp-recovery-time">-</span></div>
                    <div><strong>Status Dokumen Server:</strong> <span id="wp-recovery-server-time">-</span></div>
                </div>
                <p class="wp-recovery-question">Apakah Anda ingin memulihkan perubahan lokal tersebut?</p>
            </div>
        </div>

        <div class="wp-modal-footer">
            <div class="wp-modal-btn-row">
                <button type="button" id="btn-recovery-discard" class="wp-btn wp-btn-ghost wp-btn-danger">
                    Buang Perubahan Lokal
                </button>
                <button type="button" id="btn-recovery-server" class="wp-btn wp-btn-secondary">
                    Gunakan Draf Server
                </button>
                <button type="button" id="btn-recovery-restore" class="wp-btn wp-btn-primary">
                    Pulihkan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>

