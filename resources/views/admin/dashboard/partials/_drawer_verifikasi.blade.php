{{-- ==========================================================================
   Slide-Over Drawer: Pratinjau Cepat Berkas Pendaftaran - Admin
   Standar Visual Gentelella Theme (MagKom Sleman)
   ========================================================================== --}}
<div class="cv-slide-drawer gentelella-drawer" id="drawerVerifikasi" role="dialog" aria-modal="true" aria-labelledby="drawerVerifikasiTitle">
    <div class="cv-drawer-header" style="background: var(--blue-700, #0641CC); color: #ffffff; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h3 class="cv-drawer-title" id="drawerVerifikasiTitle" style="color: #ffffff; font-size: 1.1rem; font-weight: 700; margin: 0;">Pratinjau Berkas Pendaftaran</h3>
            <span class="cv-dyn-nomor" style="font-family: monospace; font-size: 0.8rem; color: rgba(255, 255, 255, 0.85);">REG-0000</span>
        </div>
        <button type="button" class="cv-drawer-close" data-action="close-drawer" aria-label="Tutup pratinjau" style="background: rgba(255, 255, 255, 0.15); border: none; color: #ffffff; width: 32px; height: 32px; border-radius: 50%; font-size: 1.25rem; cursor: pointer; display: flex; align-items: center; justify-content: center;">
            &times;
        </button>
    </div>

    <div class="cv-drawer-body" style="padding: 1.5rem;">
        <div style="background: var(--neutral-100, #F8FAFC); border: 1px solid var(--neutral-200, #E2E8F0); border-radius: 10px; padding: 1.25rem; margin-bottom: 1.5rem;">
            <div style="font-size: 1.1rem; font-weight: 700; color: var(--blue-700, #0641CC);" class="cv-dyn-nama">
                Nama Calon Peserta
            </div>
            <div style="font-size: 0.85rem; color: var(--neutral-600, #475569); margin-top: 0.25rem;">
                <span class="cv-dyn-institusi">Institusi</span> &bull; <span class="cv-dyn-jurusan">Jurusan</span>
            </div>
            <div style="margin-top: 0.75rem;">
                <span class="x_badge x_badge_warning cv-dyn-status">Status Berkas</span>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <div style="font-weight: 700; font-size: 0.9rem; color: var(--neutral-900, #0F172A); display: flex; align-items: center; gap: 0.5rem;">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Kelengkapan Berkas Administrasi
            </div>
            <p style="font-size: 0.8125rem; color: var(--neutral-600, #64748B); margin: 0; line-height: 1.5;">
                Gunakan drawer ini untuk memastikan kesesuaian identitas dan jurusan calon peserta sebelum memproses verifikasi penuh.
            </p>

            <x-gentelella.callout type="warning" title="Alur & Batas Waktu Verifikasi (BR-04 FIFO)">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span>SLA Maksimal:</span>
                    <strong style="color: #B45309;">3 Hari Kerja (FIFO)</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span>Aksi Verifikasi:</span>
                    <span style="font-weight: 600; color: #166534;">Terima &bull; Minta Perbaikan &bull; Tolak</span>
                </div>
            </x-gentelella.callout>
        </div>
    </div>

    <div class="cv-drawer-footer" style="padding: 1rem 1.5rem; border-top: 1px solid var(--neutral-200, #E2E8F0); background: var(--neutral-50, #F8FAFC); display: flex; justify-content: flex-end; gap: 0.75rem;">
        <button type="button" class="btn btn-default" data-action="close-drawer" style="padding: 0.5rem 1rem; border-radius: 6px; font-weight: 600;">
            Tutup
        </button>
        <a href="#" class="btn btn-primary cv-dyn-url" style="background: var(--blue-700, #0641CC); border-color: var(--blue-700, #0641CC); font-weight: 600; padding: 0.5rem 1.25rem; border-radius: 6px; color: #ffffff; text-decoration: none;">
            Buka Halaman Verifikasi Penuh &rarr;
        </a>
    </div>
</div>
