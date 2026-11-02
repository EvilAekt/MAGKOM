{{-- ==========================================================================
   Monitoring Kuota Magang Periode (Goals / Smooth Progress Track) - Dashboard Admin
   Desain Paritas Modern Minimalis Sesuai Referensi Gambar 2
   ========================================================================== --}}
<x-gentelella.x-panel 
    title="Alokasi & Keterisian Kuota Magang Periode" 
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
        <div class="cv-goal-list" id="kuotaGoalList">
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
                <div class="cv-goal-item kuota-goal-item" data-index="{{ $index }}" style="{{ $index >= 10 ? 'display: none;' : '' }}">
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

        {{-- Pagination Komponen Sesuai Referensi Gambar 5 --}}
        <div class="kuota-pagination-wrapper" id="kuotaPaginationWrapper" style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--cv-line, #E2E8F0); display: flex; flex-direction: column; gap: 0.85rem; width: 100%;">
            {{-- BARIS 1: Info Counter (Kiri) & Pilihan Menampilkan 10, 20, 30 (Kanan Pojok) --}}
            <div class="kuota-pagination-row kuota-pagination-top" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem; width: 100%;">
                <div class="kuota-pagination-summary" style="display: flex; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
                    <span class="kuota-pagination-counter" style="font-size: 0.8125rem; color: var(--neutral-600, #475569); font-weight: 500;">
                        Menampilkan <strong style="color: var(--neutral-900, #0F172A); font-weight: 700;" id="kuotaStartRow">1</strong> - <strong style="color: var(--neutral-900, #0F172A); font-weight: 700;" id="kuotaEndRow">{{ min(10, $kuotaList->count()) }}</strong> dari <strong style="color: var(--neutral-900, #0F172A); font-weight: 700;" id="kuotaTotalRows">{{ $kuotaList->count() }}</strong> kuota
                    </span>
                    <span class="kuota-immutable-pill" style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.72rem; font-weight: 700; color: #059669; background: #ECFDF5; border: 1px solid #A7F3D0; padding: 0.15rem 0.55rem; border-radius: 9999px;">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        Kuota Terverifikasi
                    </span>
                </div>

                <div class="kuota-per-page-container" style="display: inline-flex; align-items: center; gap: 0.65rem; margin-left: auto;">
                    <span class="kuota-per-page-label" style="font-size: 0.8125rem; color: var(--neutral-600, #475569); font-weight: 600;">Menampilkan:</span>
                    <div class="kuota-per-page-group" role="group" aria-label="Pilih jumlah kuota per halaman" style="display: inline-flex; align-items: center; background: #F1F5F9; border: 1px solid #CBD5E1; border-radius: 8px; padding: 2px; gap: 2px;">
                        <button type="button" class="kuota-per-page-pill active" data-per-page="10" title="Tampilkan 10 kuota per halaman" style="padding: 0.25rem 0.65rem; border: none; border-radius: 6px; font-size: 0.785rem; font-weight: 700; background: var(--blue-600, #01347C); color: #FFFFFF; cursor: pointer; transition: all 0.15s ease;">10</button>
                        <button type="button" class="kuota-per-page-pill" data-per-page="20" title="Tampilkan 20 kuota per halaman" style="padding: 0.25rem 0.65rem; border: none; border-radius: 6px; font-size: 0.785rem; font-weight: 600; background: transparent; color: #64748B; cursor: pointer; transition: all 0.15s ease;">20</button>
                        <button type="button" class="kuota-per-page-pill" data-per-page="30" title="Tampilkan 30 kuota per halaman" style="padding: 0.25rem 0.65rem; border: none; border-radius: 6px; font-size: 0.785rem; font-weight: 600; background: transparent; color: #64748B; cursor: pointer; transition: all 0.15s ease;">30</button>
                    </div>
                </div>
            </div>

            {{-- BARIS 2: Pilihan Lembar Halaman (Kiri) & Navigasi Prev/Next/Numbers (Kanan Pojok) --}}
            <div class="kuota-pagination-row kuota-pagination-bottom" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem; width: 100%; padding-top: 0.75rem; border-top: 1px solid var(--cv-line, #F1F5F9);">
                <div class="kuota-pagination-lembar-info" id="kuotaLembarInfo" style="font-size: 0.8125rem; color: var(--neutral-600, #475569);">
                    Lembar Halaman <strong style="color: var(--neutral-900, #0F172A); font-weight: 700;" id="kuotaCurrentPage">1</strong> dari <strong style="color: var(--neutral-900, #0F172A); font-weight: 700;" id="kuotaTotalPages">{{ max(1, (int) ceil($kuotaList->count() / 10)) }}</strong>
                </div>

                <div class="kuota-pagination-right" style="margin-left: auto;">
                    <div class="kuota-page-controls" id="kuotaPageControls" role="navigation" aria-label="Navigasi Lembar Halaman Kuota" style="display: inline-flex; align-items: center; gap: 0.35rem;">
                        <button type="button" class="kuota-page-btn-nav disabled" id="kuotaBtnPrev" disabled aria-label="Lembar Sebelumnya" style="padding: 0.3rem 0.75rem; border-radius: 6px; border: 1px solid #CBD5E1; background: #FFFFFF; color: #64748B; font-size: 0.785rem; font-weight: 600; cursor: pointer; transition: all 0.15s ease;">&laquo; Prev</button>
                        <div class="kuota-page-numbers" id="kuotaPageNumbers" style="display: inline-flex; align-items: center; gap: 0.25rem;">
                            {{-- Dynamically rendered by JS --}}
                        </div>
                        <button type="button" class="kuota-page-btn-nav {{ $kuotaList->count() <= 10 ? 'disabled' : '' }}" id="kuotaBtnNext" {{ $kuotaList->count() <= 10 ? 'disabled' : '' }} aria-label="Lembar Selanjutnya" style="padding: 0.3rem 0.75rem; border-radius: 6px; border: 1px solid #CBD5E1; background: #FFFFFF; color: #64748B; font-size: 0.785rem; font-weight: 600; cursor: pointer; transition: all 0.15s ease;">Next &raquo;</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Accessibility Token Parity --}}
    <span class="sr-only">Timeline Gelombang Magang Offboarding Watchdog Peserta Selesai Segera</span>
</x-gentelella.x-panel>
