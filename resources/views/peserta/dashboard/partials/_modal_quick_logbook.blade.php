{{-- ==========================================================================
   Slide-Over Drawer / Quick Modal: Pengisian Logbook Kilat - Peserta
   ========================================================================== --}}
<div class="cv-slide-drawer" id="drawerQuickLogbook" role="dialog" aria-modal="true" aria-labelledby="drawerQuickLogbookTitle">
    <div class="cv-drawer-header">
        <div>
            <h3 class="cv-drawer-title" id="drawerQuickLogbookTitle">Isi Logbook Harian</h3>
            <span style="font-size: 0.8rem; color: #16A34A; font-weight: 600;">Magang Aktif &bull; Diskominfo Sleman</span>
        </div>
        <button type="button" class="cv-drawer-close" data-action="close-drawer" aria-label="Tutup formulir">
            &times;
        </button>
    </div>

    <div class="cv-drawer-body">
        <div style="background: rgba(245, 158, 11, 0.08); border: 1px solid #FCD34D; border-radius: 8px; padding: 0.85rem 1rem; margin-bottom: 1.25rem;">
            <div style="font-weight: 700; color: #92400E; font-size: 0.825rem; display: flex; align-items: center; gap: 0.35rem;">
                <span>⚠️</span> Aturan Kedisiplinan Pelaporan (BR-09)
            </div>
            <p style="font-size: 0.775rem; color: #78350F; margin: 0.25rem 0 0 0; line-height: 1.45;">
                Logbook wajib diisi maksimal <strong>H-1 hari kerja</strong>. Keterlambatan pengisian berakibat status kehadiran hangus dan tercatat dalam lembar evaluasi dinas.
            </p>
        </div>

        <form method="POST" action="{{ route('peserta.logbook.store') }}" enctype="multipart/form-data" id="quickLogbookForm">
            @csrf

            <div style="margin-bottom: 1.25rem;">
                <label for="quick_tgl_logbook" style="display: block; font-size: 0.8125rem; font-weight: 700; color: #1E293B; margin-bottom: 0.35rem;">
                    Tanggal Kegiatan <span style="color: #DC2626;">*</span>
                </label>
                <input type="date" name="tanggal" id="quick_tgl_logbook" value="{{ now()->toDateString() }}" max="{{ now()->toDateString() }}" min="{{ now()->subDays(3)->toDateString() }}" required style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid var(--cv-line, #CBD5E1); border-radius: 6px; font-size: 0.875rem;">
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label for="quick_kegiatan_logbook" style="display: block; font-size: 0.8125rem; font-weight: 700; color: #1E293B; margin-bottom: 0.35rem;">
                    Uraian Aktivitas & Capaian Pekerjaan <span style="color: #DC2626;">*</span>
                </label>
                <textarea name="kegiatan" id="quick_kegiatan_logbook" rows="5" required placeholder="Tuliskan secara jelas kegiatan yang Anda kerjakan, koordinasi dengan pembimbing, dan output yang dihasilkan..." style="width: 100%; padding: 0.65rem 0.75rem; border: 1px solid var(--cv-line, #CBD5E1); border-radius: 6px; font-size: 0.875rem; line-height: 1.5; resize: vertical;"></textarea>
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label for="quick_link_logbook" style="display: block; font-size: 0.8125rem; font-weight: 700; color: #1E293B; margin-bottom: 0.35rem;">
                    Tautan Lampiran Dokumen / Bukti Kerja <span style="color: #64748B; font-weight: 400;">(Opsional)</span>
                </label>
                <input type="url" name="link_laporan" id="quick_link_logbook" placeholder="https://drive.google.com/... atau GitHub repo" style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid var(--cv-line, #CBD5E1); border-radius: 6px; font-size: 0.875rem;">
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label for="quick_file_logbook" style="display: block; font-size: 0.8125rem; font-weight: 700; color: #1E293B; margin-bottom: 0.35rem;">
                    Unggah Berkas PDF Bukti <span style="color: #64748B; font-weight: 400;">(Maks 5MB)</span>
                </label>
                <input type="file" name="file_laporan" id="quick_file_logbook" accept=".pdf" style="width: 100%; font-size: 0.8125rem;">
            </div>
        </form>
    </div>

    <div class="cv-drawer-footer">
        <button type="button" class="btn btn-default" data-action="close-drawer">
            Batal
        </button>
        <button type="submit" form="quickLogbookForm" class="btn btn-primary" style="background: var(--cv-blue-800, #06318C); border-color: var(--cv-blue-800, #06318C); font-weight: 700;">
            Kirim Logbook Harian
        </button>
    </div>
</div>
