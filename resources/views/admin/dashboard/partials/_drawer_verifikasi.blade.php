{{-- ==========================================================================
   Slide-Over Drawer: Pratinjau Cepat Berkas Pendaftaran - Admin
   ========================================================================== --}}
<div class="cv-slide-drawer" id="drawerVerifikasi" role="dialog" aria-modal="true" aria-labelledby="drawerVerifikasiTitle">
    <div class="cv-drawer-header">
        <div>
            <h3 class="cv-drawer-title" id="drawerVerifikasiTitle">Pratinjau Berkas Pendaftaran</h3>
            <span class="cv-dyn-nomor" style="font-family: monospace; font-size: 0.8rem; color: #64748B;">REG-0000</span>
        </div>
        <button type="button" class="cv-drawer-close" data-action="close-drawer" aria-label="Tutup pratinjau">
            &times;
        </button>
    </div>

    <div class="cv-drawer-body">
        <div style="background: var(--cv-paper, #F8FAFC); border: 1px solid var(--cv-line, #E2E8F0); border-radius: 10px; padding: 1.25rem; margin-bottom: 1.5rem;">
            <div style="font-size: 1.1rem; font-weight: 700; color: var(--cv-blue-900, #061E5B);" class="cv-dyn-nama">
                Nama Calon Peserta
            </div>
            <div style="font-size: 0.85rem; color: #475569; margin-top: 0.25rem;">
                <span class="cv-dyn-institusi">Institusi</span> &bull; <span class="cv-dyn-jurusan">Jurusan</span>
            </div>
            <div style="margin-top: 0.75rem;">
                <span class="x_badge x_badge_warning cv-dyn-status">Status Berkas</span>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <div style="font-weight: 700; font-size: 0.9rem; color: var(--cv-ink, #0F172A); display: flex; align-items: center; gap: 0.5rem;">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Kelengkapan Berkas Administrasi
            </div>
            <p style="font-size: 0.8125rem; color: #64748B; margin: 0; line-height: 1.5;">
                Gunakan drawer ini untuk memastikan kesesuaian identitas dan jurusan calon peserta sebelum memproses verifikasi penuh.
            </p>

            <div style="background: #ffffff; border: 1px dashed var(--cv-line, #E2E8F0); border-radius: 8px; padding: 1rem; font-size: 0.85rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span style="color: #64748B;">SLA Maksimal:</span>
                    <strong style="color: #B45309;">3 Hari Kerja (BR-04 FIFO)</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #64748B;">Alur Verifikasi:</span>
                    <span style="font-weight: 600; color: #166534;">Terima &bull; Minta Perbaikan &bull; Tolak</span>
                </div>
            </div>
        </div>
    </div>

    <div class="cv-drawer-footer">
        <button type="button" class="btn btn-default" data-action="close-drawer">
            Tutup
        </button>
        <a href="#" class="btn btn-primary cv-dyn-url" style="background: var(--cv-blue-800, #06318C); border-color: var(--cv-blue-800, #06318C); font-weight: 600;">
            Buka Halaman Verifikasi Penuh &rarr;
        </a>
    </div>
</div>
