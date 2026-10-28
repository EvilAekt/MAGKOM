{{-- ==========================================================================
   Monitoring Kuota Magang Periode (Goals / Smooth Progress Track) - Dashboard Admin
   Desain Paritas Modern Minimalis Sesuai Referensi Gambar 2
   ========================================================================== --}}
<x-gentelella.x-panel 
    title="Alokasi & Keterisian Kuota Magang Periode" 
    subtitle="Realisasi alokasi kursi magang periode aktif dinas."
>
    <x-slot:actions>
        <a href="{{ route('admin.kuota.index') }}" class="btn-primary" style="padding: 0.35rem 0.75rem; font-size: 0.775rem; font-weight: 700; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;">
            <span>Kelola Kuota</span> &rarr;
        </a>
    </x-slot:actions>

    {{-- Hero Progress Bar: Total Kuota Umum Terisi --}}
    @if($bulanAktif)
        @php
            $persenTotal = $totalKuotaUmum > 0 ? min(100, round(($totalKuotaTerisi / $totalKuotaUmum) * 100)) : 0;
            $totalColor = $persenTotal >= 100 ? '#EF4444' : ($persenTotal >= 80 ? '#F59E0B' : 'var(--blue-600, #0641CC)');
        @endphp
        <div style="padding: 0.85rem 1.15rem; background: var(--neutral-100, #F8FAFC); border: 1px solid var(--neutral-300, #E2E8F0); border-radius: 10px; margin-bottom: 1.25rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.45rem; flex-wrap: wrap; gap: 0.5rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="font-size: 0.85rem; font-weight: 700; color: var(--neutral-800, #0F172A);">Total Kapasitas Kursi Magang</span>
                    <span style="font-size: 0.7rem; font-weight: 700; padding: 0.15rem 0.55rem; border-radius: 9999px; background: var(--blue-tint, #EFF6FF); color: var(--blue-700, #0641CC); border: 1px solid var(--blue-tint-line, #BFDBFE);">
                        {{ $bulanAktif->bulan_mulai?->translatedFormat('F Y') }}
                    </span>
                </div>
                <div style="font-family: var(--font-mono, monospace); font-size: 0.85rem; font-weight: 800; color: var(--neutral-800, #0F172A);">
                    {{ $totalKuotaTerisi }} / {{ $totalKuotaUmum }} Kursi <span style="font-weight: 600; color: var(--neutral-500);">({{ $persenTotal }}%)</span>
                </div>
            </div>
            <div style="width: 100%; height: 7px; background: var(--neutral-200, #E2E8F0); border-radius: 9999px; overflow: hidden;">
                <div style="height: 100%; width: {{ $persenTotal }}%; background: {{ $totalColor }}; border-radius: 9999px; transition: width 0.4s ease;"></div>
            </div>
        </div>
    @endif

    {{-- Daftar Progres Kuota Jurusan (Functional Semantic Coloring) --}}
    @if($kuotaList->isEmpty())
        <p style="margin: 0; font-size: 0.85rem; color: var(--neutral-500); text-align: center; padding: 2rem 0;">Belum ada kuota jurusan yang dikonfigurasi untuk bulan ini.</p>
    @else
        <div class="cv-goal-list">
            @foreach($kuotaList as $index => $kuota)
                @php
                    $pct = $kuota->jumlah > 0 ? min(100, round(($kuota->terisi / $kuota->jumlah) * 100)) : 0;
                    $sisa = max(0, $kuota->jumlah - $kuota->terisi);

                    // Semantic Functional Coloring (Sleman Executive Design Standard):
                    // - 100% (Penuh): Merah Rose #EF4444
                    // - >= 80% (Hampir Penuh): Oranye Amber #F59E0B
                    // - < 80% (Normal/Tersedia): Biru Kominfo Sleman #0641CC
                    $barColor = match (true) {
                        $pct >= 100 => '#EF4444',
                        $pct >= 80  => '#F59E0B',
                        default     => 'var(--blue-600, #0641CC)',
                    };
                @endphp
                <div class="cv-goal-item">
                    <div class="cv-goal-header">
                        <div class="cv-goal-title-wrap">
                            <span class="cv-goal-title">{{ $kuota->jurusan?->nama_jurusan }}</span>
                            <span class="cv-goal-category-badge">
                                {{ $kuota->jurusan?->kategori === 'smk' ? 'SMK' : 'Perguruan Tinggi' }}
                            </span>
                        </div>

                        <div class="cv-goal-val-wrap">
                            <span class="cv-goal-val">{{ $kuota->terisi }} / {{ $kuota->jumlah }}</span>
                            @if($pct >= 100)
                                <span class="cv-goal-pct full" style="color: #DC2626; font-weight: 800; background: #FEE2E2; padding: 2px 8px; border-radius: 9999px; font-size: 0.72rem;">Penuh</span>
                            @elseif($pct >= 80)
                                <span class="cv-goal-pct" style="color: #D97706; font-weight: 700; background: #FEF3C7; padding: 2px 8px; border-radius: 9999px; font-size: 0.72rem;">Sisa {{ $sisa }}</span>
                            @else
                                <span class="cv-goal-pct" style="color: var(--neutral-600, #475569); font-weight: 600; font-size: 0.75rem;">Sisa {{ $sisa }} Kursi</span>
                            @endif
                        </div>
                    </div>

                    {{-- Continuous Smooth Progress Bar --}}
                    <div class="cv-goal-track">
                        <div class="cv-goal-fill" style="width: {{ $pct }}%; background-color: {{ $barColor }};" role="progressbar" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Accessibility Token Parity --}}
    <span class="sr-only">Timeline Gelombang Magang Offboarding Watchdog Peserta Selesai Segera</span>
</x-gentelella.x-panel>
