{{-- ==========================================================================
   Slide-Over Drawer: Rincian Jejak Rekam Audit Log - SuperAdmin
   ========================================================================== --}}
<div class="cv-slide-drawer" id="drawerAuditDetail" role="dialog" aria-modal="true" aria-labelledby="drawerAuditDetailTitle">
    <div class="cv-drawer-header">
        <div>
            <h3 class="cv-drawer-title" id="drawerAuditDetailTitle">Inspeksi Jejak Audit</h3>
            <span class="cv-dyn-waktu" style="font-size: 0.8rem; color: #64748B; font-family: monospace;">Waktu Aktivitas</span>
        </div>
        <button type="button" class="cv-drawer-close" data-action="close-drawer" aria-label="Tutup inspeksi">
            &times;
        </button>
    </div>

    <div class="cv-drawer-body">
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
                    <code class="cv-dyn-ip" style="color: #2563EB;">127.0.0.1</code>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #64748B;">Integritas Log:</span>
                    <span style="font-weight: 700; color: #16A34A;">Append-Only &bull; Imutabel (AC-15)</span>
                </div>
            </div>

            <div style="background: rgba(15, 23, 42, 0.04); border-radius: 8px; padding: 1rem;">
                <div style="font-weight: 700; font-size: 0.8rem; color: #475569; text-transform: uppercase; margin-bottom: 0.5rem;">
                    Kanal Pengawasan Multi-Channel:
                </div>
                <p style="font-size: 0.8rem; color: #64748B; margin: 0; line-height: 1.5;">
                    Seluruh mutasi data kuota, status pendaftaran, rubrik nilai, dan perubahan kredensial staf dicatat secara otomatis dalam 15 channel audit log terisolasi.
                </p>
            </div>
        </div>
    </div>

    <div class="cv-drawer-footer">
        <button type="button" class="btn btn-default" data-action="close-drawer">
            Tutup
        </button>
        <a href="{{ route('superadmin.audit-log.index') }}" class="btn btn-primary" style="background: var(--cv-blue-800, #06318C); border-color: var(--cv-blue-800, #06318C); font-weight: 600;">
            Buka Arsip Audit Lengkap &rarr;
        </a>
    </div>
</div>
