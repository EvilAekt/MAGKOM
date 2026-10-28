{{-- ==========================================================================
   Attention SLA Hazard Card & Efisiensi Operasional - Dashboard Admin
   Standar Visual Gentelella Theme (MagKom Sleman)
   ========================================================================== --}}
@if($pengajuanSlaKritisCount > 0)
    <div style="margin-bottom: 1rem;">
        <x-gentelella.callout type="danger" title="Perhatian: Berkas Mendekati atau Melewati Batas Waktu SLA Verifikasi (BR-04)">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    Terdapat <strong>{{ $pengajuanSlaKritisCount }} berkas</strong> calon peserta yang mendekati atau melewati batas waktu SLA verifikasi (3 hari kerja). Segera lakukan peninjauan berkas pendaftar untuk menjaga mutu pelayanan seleksi.
                </div>
                <a href="{{ route('admin.pendaftaran.index') }}" class="btn btn-sm btn-danger" style="font-weight: 700; border-radius: 6px; text-decoration: none; padding: 0.45rem 1rem;">
                    Buka Antrean Verifikasi &rarr;
                </a>
            </div>
        </x-gentelella.callout>
    </div>
@endif

{{-- 3 Kartu Efisiensi Operasional Gentelella --}}
<div class="adm-kpi-grid" style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 0.85rem; margin-bottom: 0;">
    <div style="min-width: 0;">
        <x-gentelella.card style="margin-bottom: 0; padding: 0.85rem 0.95rem; border-left: 4px solid var(--blue-600); border-radius: 12px; height: 100%;">
            <div style="display: flex; align-items: center; gap: 0.35rem; color: var(--blue-600); margin-bottom: 0.35rem; min-width: 0;">
                <x-sa.icon name="clock" :size="14" />
                <span style="font-size: 0.6875rem; font-weight: 700; color: var(--neutral-500); text-transform: uppercase; letter-spacing: 0.02em; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="Kecepatan Verifikasi Berkas">Kecepatan Verifikasi Berkas</span>
            </div>
            <div style="font-size: 1.35rem; font-weight: 800; color: var(--neutral-800); line-height: 1.2; margin-bottom: 0.2rem;">
                {{ $avgVerifikasiHari }} <span style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-500);">Hari</span>
            </div>
            <div style="font-size: 0.7rem; color: var(--green-600); font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                ✓ SLA Sesuai Target (Maks. 3 Hari)
            </div>
        </x-gentelella.card>
    </div>

    <div style="min-width: 0;">
        <x-gentelella.card style="margin-bottom: 0; padding: 0.85rem 0.95rem; border-left: 4px solid var(--green-600); border-radius: 12px; height: 100%;">
            <div style="display: flex; align-items: center; gap: 0.35rem; color: var(--green-600); margin-bottom: 0.35rem; min-width: 0;">
                <x-sa.icon name="activity" :size="14" />
                <span style="font-size: 0.6875rem; font-weight: 700; color: var(--neutral-500); text-transform: uppercase; letter-spacing: 0.02em; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="Tingkat Keterisian Kuota">Tingkat Keterisian Kuota</span>
            </div>
            <div style="font-size: 1.35rem; font-weight: 800; color: var(--neutral-800); line-height: 1.2; margin-bottom: 0.2rem;">
                {{ $realisasiKuotaPersen }}% <span style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-500);">Realisasi</span>
            </div>
            <div style="font-size: 0.7rem; color: var(--neutral-600); font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                Sisa {{ max(0, $totalKuotaUmum - $totalKuotaTerisi) }} dari {{ $totalKuotaUmum }} Kuota
            </div>
        </x-gentelella.card>
    </div>

    <div style="min-width: 0;">
        <x-gentelella.card style="margin-bottom: 0; padding: 0.85rem 0.95rem; border-left: 4px solid #4F46E5; border-radius: 12px; height: 100%;">
            <div style="display: flex; align-items: center; gap: 0.35rem; color: #4F46E5; margin-bottom: 0.35rem; min-width: 0;">
                <x-sa.icon name="users" :size="14" />
                <span style="font-size: 0.6875rem; font-weight: 700; color: var(--neutral-500); text-transform: uppercase; letter-spacing: 0.02em; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="Pendaftar 7 Hari Terakhir">Pendaftar 7 Hari Terakhir</span>
            </div>
            <div style="font-size: 1.35rem; font-weight: 800; color: var(--neutral-800); line-height: 1.2; margin-bottom: 0.2rem;">
                {{ number_format($pendaftaranMingguIni) }} <span style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-500);">Baru</span>
            </div>
            <div style="font-size: 0.7rem; color: #4F46E5; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                Aktivitas Masuk Mingguan
            </div>
        </x-gentelella.card>
    </div>
</div>
