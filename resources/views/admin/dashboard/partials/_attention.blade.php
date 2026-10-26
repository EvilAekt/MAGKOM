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
<div class="row" style="margin-bottom: 0;">
    <div class="col-md-4 col-sm-4 col-xs-12">
        <x-gentelella.card style="margin-bottom: 0.75rem; padding: 1rem 1.25rem; border-left: 4px solid var(--blue-600); border-radius: 10px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-500); text-transform: uppercase;">Kecepatan Verifikasi Berkas</span>
                    <div style="font-size: 1.35rem; font-weight: 800; color: var(--neutral-800); margin-top: 0.15rem;">
                        {{ $avgVerifikasiHari }} <span style="font-size: 0.85rem; font-weight: 600; color: var(--neutral-500);">Hari Kalender</span>
                    </div>
                    <span style="font-size: 0.72rem; color: var(--green-600); font-weight: 700;">✓ SLA Sesuai Target (Maks. 3 Hari)</span>
                </div>
                <div style="width: 42px; height: 42px; border-radius: 10px; background: var(--blue-tint); color: var(--blue-700); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <x-sa.icon name="clock" :size="20" />
                </div>
            </div>
        </x-gentelella.card>
    </div>

    <div class="col-md-4 col-sm-4 col-xs-12">
        <x-gentelella.card style="margin-bottom: 0.75rem; padding: 1rem 1.25rem; border-left: 4px solid var(--green-600); border-radius: 10px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-500); text-transform: uppercase;">Tingkat Keterisian Kuota</span>
                    <div style="font-size: 1.35rem; font-weight: 800; color: var(--neutral-800); margin-top: 0.15rem;">
                        {{ $realisasiKuotaPersen }}% <span style="font-size: 0.85rem; font-weight: 600; color: var(--neutral-500);">Realisasi Alokasi</span>
                    </div>
                    <span style="font-size: 0.72rem; color: var(--neutral-600); font-weight: 600;">Total Kuota Aktif Dinas Terproteksi</span>
                </div>
                <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(16, 185, 129, 0.12); color: var(--green-600); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <x-sa.icon name="activity" :size="20" />
                </div>
            </div>
        </x-gentelella.card>
    </div>

    <div class="col-md-4 col-sm-4 col-xs-12">
        <x-gentelella.card style="margin-bottom: 0.75rem; padding: 1rem 1.25rem; border-left: 4px solid #7C3AED; border-radius: 10px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-500); text-transform: uppercase;">Pendaftar 7 Hari Terakhir</span>
                    <div style="font-size: 1.35rem; font-weight: 800; color: var(--neutral-800); margin-top: 0.15rem;">
                        {{ number_format($pendaftaranMingguIni) }} <span style="font-size: 0.85rem; font-weight: 600; color: var(--neutral-500);">Pengajuan Baru</span>
                    </div>
                    <span style="font-size: 0.72rem; color: #7C3AED; font-weight: 700;">Aktivitas Masuk Mingguan</span>
                </div>
                <div style="width: 42px; height: 42px; border-radius: 10px; background: #F5F3FF; color: #7C3AED; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <x-sa.icon name="users" :size="20" />
                </div>
            </div>
        </x-gentelella.card>
    </div>
</div>
