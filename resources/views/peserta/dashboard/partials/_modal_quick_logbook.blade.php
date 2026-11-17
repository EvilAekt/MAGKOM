{{-- ==========================================================================
   Slide-Over Drawer / Quick Modal: Pengisian Logbook Kilat - Peserta
   ========================================================================== --}}
<div class="cv-slide-drawer" id="drawerQuickLogbook" role="dialog" aria-modal="true" aria-labelledby="drawerQuickLogbookTitle">
    <div class="cv-drawer-header">
        <div>
            <h3 class="cv-drawer-title" id="drawerQuickLogbookTitle">Isi Logbook Harian</h3>
            <span class="quick-logbook-subtitle" style="font-size: 0.8rem; color: #16A34A; font-weight: 600;">Magang Aktif &bull; Diskominfo Sleman</span>
        </div>
        <button type="button" class="cv-drawer-close" data-action="close-drawer" aria-label="Tutup formulir">
            &times;
        </button>
    </div>

    <div class="cv-drawer-body">
        <div class="quick-logbook-alert saas-alert-catatan">
            <div class="quick-logbook-alert-title">
                <span>⚠️</span>
                <span>Aturan Kedisiplinan Pelaporan</span>
            </div>
            <p class="quick-logbook-alert-desc">
                Logbook wajib diisi maksimal <strong>H-1 hari kerja</strong>. Keterlambatan pengisian berakibat status kehadiran tercatat terlambat.
            </p>
        </div>

        <form method="POST" action="{{ route('peserta.logbook.store') }}" enctype="multipart/form-data" id="quickLogbookForm">
            @csrf

            <div class="quick-logbook-group">
                <label for="quick_tgl_logbook" class="quick-logbook-label">
                    Tanggal Kegiatan <span class="quick-logbook-req">*</span>
                </label>
                <input type="date" name="tanggal" id="quick_tgl_logbook" class="quick-logbook-input" value="{{ now()->toDateString() }}" max="{{ now()->toDateString() }}" min="{{ now()->subDays(3)->toDateString() }}" required>
            </div>

            <div class="quick-logbook-group">
                <label for="quick_kegiatan_logbook" class="quick-logbook-label">
                    Uraian Aktivitas & Capaian Pekerjaan <span class="quick-logbook-req">*</span>
                </label>
                <textarea name="kegiatan" id="quick_kegiatan_logbook" class="quick-logbook-textarea" rows="5" required placeholder="Tuliskan secara jelas kegiatan yang Anda kerjakan, koordinasi dengan pembimbing, dan output yang dihasilkan..."></textarea>
            </div>

            <div class="quick-logbook-group">
                <label for="quick_link_logbook" class="quick-logbook-label">
                    Tautan Lampiran Dokumen / Bukti Kerja <span class="quick-logbook-optional">(Opsional)</span>
                </label>
                <input type="url" name="link_laporan" id="quick_link_logbook" class="quick-logbook-input" placeholder="https://drive.google.com/... atau GitHub repo">
            </div>

            <div class="quick-logbook-group">
                <label for="quick_file_logbook" class="quick-logbook-label">
                    Unggah Berkas PDF Bukti <span class="quick-logbook-optional">(Maks 5MB)</span>
                </label>
                <input type="file" name="file_laporan" id="quick_file_logbook" class="quick-logbook-file" accept=".pdf">
            </div>
        </form>
    </div>

    <div class="cv-drawer-footer">
        <button type="button" class="saas-btn-secondary" data-action="close-drawer" style="padding: 8px 16px;">
            Batal
        </button>
        <button type="submit" form="quickLogbookForm" class="saas-btn-primary" style="padding: 8px 18px;">
            Kirim Logbook Harian
        </button>
    </div>
</div>
