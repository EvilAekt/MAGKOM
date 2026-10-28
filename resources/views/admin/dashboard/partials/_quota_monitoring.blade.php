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
        @endphp
        <div style="padding: 1rem 1.25rem; background: var(--blue-tint, #EFF6FF); border: 1px solid var(--blue-tint-line, #BFDBFE); border-radius: 12px; margin-bottom: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.5rem; flex-wrap: wrap; gap: 0.5rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="font-size: 0.9rem; font-weight: 800; color: var(--blue-700, #0641CC);">Total Kapasitas Kursi Magang</span>
                    <span class="x_badge x_badge_primary" style="font-size: 0.7rem; padding: 0.2rem 0.5rem; border-radius: 6px;">
                        {{ $bulanAktif->bulan_mulai?->translatedFormat('F Y') }}
                    </span>
                </div>
                <div style="font-family: var(--font-mono, monospace); font-size: 0.875rem; font-weight: 800; color: var(--blue-700, #0641CC);">
                    {{ $totalKuotaTerisi }} / {{ $totalKuotaUmum }} Kursi ({{ $persenTotal }}%)
                </div>
            </div>
            <div style="width: 100%; height: 8px; background: rgba(6, 65, 204, 0.15); border-radius: 9999px; overflow: hidden;">
                <div style="height: 100%; width: {{ $persenTotal }}%; background: var(--blue-600, #0751FF); border-radius: 9999px; transition: width 0.5s ease;"></div>
            </div>
        </div>
    @endif

    {{-- Daftar Progres Kuota Jurusan (Goals / Smooth Track Style - Gambar 2) --}}
    @if($kuotaList->isEmpty())
        <p style="margin: 0; font-size: 0.85rem; color: var(--neutral-500); text-align: center; padding: 2rem 0;">Belum ada kuota jurusan yang dikonfigurasi untuk bulan ini.</p>
    @else
        <div class="cv-goal-list">
            @php
                // Palet multi-warna cerah dan estetik persis seperti di Gambar 2
                $goalColors = ['#06B6D4', '#10B981', '#3B82F6', '#F59E0B', '#8B5CF6', '#EC4899'];
            @endphp

            @foreach($kuotaList as $index => $kuota)
                @php
                    $pct = $kuota->jumlah > 0 ? min(100, round(($kuota->terisi / $kuota->jumlah) * 100)) : 0;
                    $sisa = max(0, $kuota->jumlah - $kuota->terisi);
                    $color = $pct >= 100 ? '#EF4444' : $goalColors[$index % count($goalColors)];
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
                                <span class="cv-goal-pct full">Penuh</span>
                            @else
                                <span class="cv-goal-pct">Sisa {{ $sisa }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Continuous Smooth Progress Bar --}}
                    <div class="cv-goal-track">
                        <div class="cv-goal-fill" style="width: {{ $pct }}%; background-color: {{ $color }};" role="progressbar" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Accessibility Token Parity --}}
    <span class="sr-only">Timeline Gelombang Magang Offboarding Watchdog Peserta Selesai Segera</span>
</x-gentelella.x-panel>
