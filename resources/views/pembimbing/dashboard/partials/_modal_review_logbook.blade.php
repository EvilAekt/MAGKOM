{{-- ==========================================================================
   Slide-Over Drawer / Modal Cepat: Reviu Logbook Harian Mentee - Pembimbing
   Standar Visual Gentelella Theme (MagKom Sleman)
   ========================================================================== --}}
<div class="cv-slide-drawer gentelella-drawer" id="drawerReviewLogbook" role="dialog" aria-modal="true" aria-labelledby="drawerReviewLogbookTitle">
    <div class="cv-drawer-header" style="background: var(--blue-700, #0641CC); color: #ffffff; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h3 class="cv-drawer-title" id="drawerReviewLogbookTitle" style="color: #ffffff; font-size: 1.1rem; font-weight: 700; margin: 0;">Reviu Logbook Mentee</h3>
            <span class="cv-dyn-tanggal" style="font-size: 0.8rem; color: rgba(255, 255, 255, 0.85);">Tanggal Aktivitas</span>
        </div>
        <button type="button" class="cv-drawer-close" data-action="close-drawer" aria-label="Tutup reviu" style="background: rgba(255, 255, 255, 0.15); border: none; color: #ffffff; width: 32px; height: 32px; border-radius: 50%; font-size: 1.25rem; cursor: pointer; display: flex; align-items: center; justify-content: center;">
            &times;
        </button>
    </div>

    <div class="cv-drawer-body" style="padding: 1.5rem;">
        <div style="background: var(--neutral-100, #F8FAFC); border: 1px solid var(--neutral-200, #E2E8F0); border-radius: 10px; padding: 1.25rem; margin-bottom: 1.5rem;">
            <div style="font-size: 1.1rem; font-weight: 700; color: var(--blue-700, #0641CC);" class="cv-dyn-mentee">
                Nama Peserta Mentee
            </div>
            <div style="font-size: 0.85rem; color: var(--neutral-600, #475569); margin-top: 0.25rem;">
                <span class="cv-dyn-institusi">Institusi Peserta</span>
            </div>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-700, #334155); margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em;">
                Aktivitas / Kegiatan yang Dilaporkan:
            </label>
            <div style="background: var(--neutral-0, #ffffff); border: 1px solid var(--neutral-200, #E2E8F0); border-radius: 8px; padding: 1rem; font-size: 0.875rem; color: var(--neutral-900, #1E293B); line-height: 1.6;" class="cv-dyn-kegiatan">
                Deskripsi aktivitas kerja mentee...
            </div>
        </div>

        <x-gentelella.callout type="info" title="Pedoman Mentoring Diskominfo (BR-09)">
            Logbook harian merupakan instrumen kehadiran dan penilaian sikap kerja. Berikan umpan balik konstruktif atau instruksi perbaikan jika laporan belum memadai.
        </x-gentelella.callout>
    </div>

    <div class="cv-drawer-footer" style="padding: 1rem 1.5rem; border-top: 1px solid var(--neutral-200, #E2E8F0); background: var(--neutral-50, #F8FAFC); display: flex; justify-content: flex-end; gap: 0.75rem;">
        <button type="button" class="btn btn-default" data-action="close-drawer" style="padding: 0.5rem 1rem; border-radius: 6px; font-weight: 600;">
            Tutup
        </button>
        <a href="#" class="btn btn-primary cv-dyn-url" style="background: var(--blue-700, #0641CC); border-color: var(--blue-700, #0641CC); font-weight: 600; padding: 0.5rem 1.25rem; border-radius: 6px; color: #ffffff; text-decoration: none;">
            Buka Form Tanggapan Penuh &rarr;
        </a>
    </div>
</div>
