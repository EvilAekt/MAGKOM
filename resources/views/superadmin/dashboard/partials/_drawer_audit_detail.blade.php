{{-- ==========================================================================
   Slide-Over Drawer: Rincian Jejak Rekam Audit Log - SuperAdmin
   Standar Visual Gentelella Theme (MagKom Sleman)
   ========================================================================== --}}
<div class="cv-slide-drawer gentelella-drawer" id="drawerAuditDetail" role="dialog" aria-modal="true" aria-labelledby="drawerAuditDetailTitle">
    <div class="cv-drawer-header" style="background: var(--blue-700, #0641CC); color: #ffffff; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h3 class="cv-drawer-title" id="drawerAuditDetailTitle" style="color: #ffffff; font-size: 1.1rem; font-weight: 700; margin: 0;">Inspeksi Jejak Audit</h3>
            <span class="cv-dyn-waktu" style="font-size: 0.8rem; color: rgba(255, 255, 255, 0.85); font-family: monospace;">Waktu Aktivitas</span>
        </div>
        <button type="button" class="cv-drawer-close" data-action="close-drawer" aria-label="Tutup inspeksi" style="background: rgba(255, 255, 255, 0.15); border: none; color: #ffffff; width: 32px; height: 32px; border-radius: 50%; font-size: 1.25rem; cursor: pointer; display: flex; align-items: center; justify-content: center;">
            &times;
        </button>
    </div>

    <div class="cv-drawer-body" style="padding: 1.5rem;">
        <div style="background: var(--cv-paper, #F8FAFC); border: 1px solid var(--cv-line, #E2E8F0); border-radius: 10px; padding: 1.25rem; margin-bottom: 1.5rem;">
            <div style="font-size: 1.05rem; font-weight: 700; color: var(--cv-blue-900, #061E5B);" class="cv-dyn-action">
                Mutasi / Aktivitas Sistem
            </div>
            <div style="font-size: 0.85rem; color: #475569; margin-top: 0.35rem;">
                Aktor Pelaksana: <strong class="cv-dyn-user" style="color: #0F172A;">sistem</strong>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <div style="background: #ffffff; border: 1px solid var(--cv-line, #E2E8F0); border-radius: 8px; padding: 1rem; font-size: 0.85rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; border-bottom: 1px dashed var(--cv-line, #E2E8F0); padding-bottom: 0.4rem;">
                    <span style="color: #64748B;">Alamat IP:</span>
                    <code class="cv-dyn-ip" style="color: #2563EB; font-weight: 700;">127.0.0.1</code>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #64748B;">Integritas Log:</span>
                    <span style="font-weight: 700; color: #16A34A;">Append-Only &bull; Imutabel (AC-15)</span>
                </div>
            </div>

            <x-gentelella.callout type="info" title="Kanal Pengawasan Multi-Channel">
                Seluruh mutasi data kuota, status pendaftaran, rubrik nilai, dan perubahan kredensial staf dicatat secara otomatis dalam 15 channel audit log terisolasi.
            </x-gentelella.callout>
        </div>
    </div>

    <div class="cv-drawer-footer" style="padding: 1.25rem 1.5rem; border-top: 1px solid var(--cv-line, #E2E8F0); display: flex; justify-content: flex-end; gap: 0.75rem; background: #ffffff;">
        <button type="button" class="btn btn-default" data-action="close-drawer">
            Tutup
        </button>
        <a href="{{ route('superadmin.audit-log.index') }}" class="btn btn-primary" style="background: var(--blue-700, #0641CC); border-color: var(--blue-700, #0641CC); font-weight: 600;">
            Buka Arsip Audit Lengkap &rarr;
        </a>
    </div>
</div>
