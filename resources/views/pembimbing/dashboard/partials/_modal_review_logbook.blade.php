{{-- ==========================================================================
   Slide-Over Drawer / Quick Modal: Reviu Logbook Harian Mentee - Pembimbing
   ========================================================================== --}}
<div class="cv-slide-drawer" id="drawerReviewLogbook" role="dialog" aria-modal="true" aria-labelledby="drawerReviewLogbookTitle">
    <div class="cv-drawer-header">
        <div>
            <h3 class="cv-drawer-title" id="drawerReviewLogbookTitle">Reviu Logbook Mentee</h3>
            <span class="cv-dyn-tanggal" style="font-size: 0.8rem; color: #64748B;">Tanggal Aktivitas</span>
        </div>
        <button type="button" class="cv-drawer-close" data-action="close-drawer" aria-label="Tutup reviu">
            &times;
        </button>
    </div>

    <div class="cv-drawer-body">
        <div style="background: var(--cv-paper, #F8FAFC); border: 1px solid var(--cv-line, #E2E8F0); border-radius: 10px; padding: 1.25rem; margin-bottom: 1.5rem;">
            <div style="font-size: 1.1rem; font-weight: 700; color: var(--cv-blue-900, #061E5B);" class="cv-dyn-mentee">
                Nama Peserta Mentee
            </div>
            <div style="font-size: 0.85rem; color: #475569; margin-top: 0.25rem;">
                <span class="cv-dyn-institusi">Institusi Peserta</span>
            </div>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: #334155; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em;">
                Aktivitas / Kegiatan yang Dilaporkan:
            </label>
            <div style="background: #ffffff; border: 1px solid var(--cv-line, #E2E8F0); border-radius: 8px; padding: 1rem; font-size: 0.875rem; color: #1E293B; line-height: 1.6;" class="cv-dyn-kegiatan">
                Deskripsi aktivitas kerja mentee...
            </div>
        </div>

        <div style="background: rgba(7, 81, 255, 0.04); border: 1px dashed var(--cv-blue-600, #0751FF); border-radius: 8px; padding: 1rem; font-size: 0.8125rem; color: #1E40AF;">
            <strong>Pedoman Mentoring Diskominfo (BR-09):</strong>
            <p style="margin: 0.35rem 0 0 0;">
                Logbook harian merupakan instrumen kehadiran dan penilaian sikap kerja. Berikan umpan balik konstruktif atau instruksi perbaikan jika laporan belum memadai.
            </p>
        </div>
    </div>

    <div class="cv-drawer-footer">
        <button type="button" class="btn btn-default" data-action="close-drawer">
            Tutup
        </button>
        <a href="#" class="btn btn-primary cv-dyn-url" style="background: var(--cv-blue-800, #06318C); border-color: var(--cv-blue-800, #06318C); font-weight: 600;">
            Buka Form Tanggapan Penuh &rarr;
        </a>
    </div>
</div>
