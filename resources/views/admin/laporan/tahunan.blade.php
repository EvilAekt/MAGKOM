@extends('layouts.dashboard')

@section('title', "Analitik Tahunan {$tahun} - MagKom Sleman")
@section('page_title', "Dashboard Analitik Rekapitulasi Tahunan {$tahun}")
@section('page_description', "Evaluasi tren pendaftaran, rasio seleksi, profil institusi mitra, dan mutu kelulusan tahun {$tahun} (PRD 4.3 Poin 5).")

@section('breadcrumbs')
    <a href="{{ route('admin.laporan.index') }}" class="breadcrumb-item">Laporan & Ekspor</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Analitik Tahunan {{ $tahun }}</span>
@endsection

@section('content')
<div class="tahunan-container">

    <!-- 1. TOP TOOLBAR & YEAR SELECTOR -->
    <div class="tahunan-toolbar">
        <form method="GET" action="{{ route('admin.laporan.tahunan') }}" class="tahunan-filter-form">
            <label for="tahun" class="tahunan-filter-label">Pilih Tahun Kalender:</label>
            <select name="tahun" id="tahun" onchange="this.form.submit()" class="tahunan-select-tahun">
                @foreach($daftarTahun as $t)
                    <option value="{{ $t }}" {{ $t == $tahun ? 'selected' : '' }}>Tahun {{ $t }}</option>
                @endforeach
            </select>
            <span class="tahunan-filter-desc">Menampilkan seluruh data bulan magang tahun {{ $tahun }}</span>
        </form>

        <div class="tahunan-toolbar-actions">
            <a href="{{ route('admin.laporan.index') }}" class="btn-tahunan-hub" title="Kembali ke Hub Laporan Utama">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Hub Laporan Utama</span>
            </a>
            <a href="{{ route('admin.laporan.tahunan.export', ['tahun' => $tahun]) }}" class="btn-tahunan-unduh" title="Unduh Rekapitulasi Tahunan Format CSV">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                <span>Unduh Rekap CSV</span>
            </a>
        </div>
    </div>

    <!-- 2. 4 EXECUTIVE SUMMARY HERO KPI CARDS -->
    <div class="tahunan-kpi-grid">
        <div class="tahunan-kpi-card card-pendaftar">
            <div class="tahunan-kpi-header">
                <span class="tahunan-kpi-label">Total Pendaftar Masuk</span>
                <span class="badge badge-neutral" style="font-size: 0.6875rem;">Tahunan</span>
            </div>
            <div class="tahunan-kpi-value">{{ number_format($totalPendaftaran) }}</div>
            <div class="tahunan-kpi-sub">Calon peserta yang mengajukan berkas</div>
        </div>

        <div class="tahunan-kpi-card card-diterima">
            <div class="tahunan-kpi-header">
                <span class="tahunan-kpi-label">Peserta Diterima Magang</span>
                <span class="badge badge-success" style="font-size: 0.6875rem;">{{ number_format($acceptanceRate, 1) }}% Lolos</span>
            </div>
            <div class="tahunan-kpi-value">{{ number_format($totalDiterima) }}</div>
            <div class="tahunan-kpi-sub">
                Tingkat Penerimaan: <strong>{{ number_format($acceptanceRate, 1) }}%</strong>
            </div>
        </div>

        <div class="tahunan-kpi-card card-alumni">
            <div class="tahunan-kpi-header">
                <span class="tahunan-kpi-label">Peserta Selesai Magang</span>
                <span class="badge badge-info" style="font-size: 0.6875rem;">{{ number_format($completionRate, 1) }}% Selesai</span>
            </div>
            <div class="tahunan-kpi-value">{{ number_format($totalSelesai) }}</div>
            <div class="tahunan-kpi-sub">
                Tingkat Penyelesaian: <strong>{{ number_format($completionRate, 1) }}%</strong>
            </div>
        </div>

        <div class="tahunan-kpi-card card-durasi">
            <div class="tahunan-kpi-header">
                <span class="tahunan-kpi-label">Rata-rata Durasi Magang</span>
                <span class="badge badge-neutral" style="font-size: 0.6875rem;">Standar</span>
            </div>
            <div class="tahunan-kpi-value">
                {{ $rataRataDurasi }} <span style="font-size: 1.125rem; font-weight: 600;">Bulan</span>
            </div>
            <div class="tahunan-kpi-sub">Rentang normal 1 sampai 6 bulan</div>
        </div>
    </div>

    <!-- 3. 2-COLUMN GRID: TREN PENYERAPAN BULANAN (KIRI) & GRAFIK PLAN GROWTH (KANAN) -->
    <div class="tahunan-mid-grid">

        <!-- KOLOM KIRI: KARTU TABEL TREN PENYERAPAN BULANAN -->
        <div class="tahunan-section-card tahunan-table-card">
            <div class="tahunan-section-header">
                <div>
                    <h3 class="tahunan-section-title">Tren Penyerapan Bulanan (Januari - Desember {{ $tahun }})</h3>
                    <p class="tahunan-section-desc">Perbandingan volume pengajuan berkas, peserta ditempatkan, dan peserta selesai per bulan.</p>
                </div>
                <span class="badge badge-info" style="font-size: 0.75rem;">12 Bulan Terpantau</span>
            </div>

            <!-- Tabel Rincian Data Bulanan (Clean Tabular Layout) -->
            <div class="tahunan-table-wrapper clean-scrollbar">
                <table class="tahunan-table">
                    <thead>
                        <tr>
                            <th>Bulan</th>
                            <th style="text-align: center;">Pendaftar</th>
                            <th style="text-align: center;">Diterima</th>
                            <th style="text-align: center;">Selesai</th>
                            <th style="text-align: center;">Rasio Penerimaan</th>
                            @if($canViewNilai)
                                <th style="text-align: center;">Rata-rata Nilai</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($trenBulanan as $b)
                            <tr>
                                <td style="font-weight: 700; color: var(--neutral-800);">
                                    {{ $b['nama_bulan'] }}
                                </td>
                                <td style="text-align: center; font-weight: 700; color: var(--blue-700);">
                                    {{ $b['pendaftar'] }}
                                </td>
                                <td style="text-align: center; font-weight: 700; color: var(--green-700);">
                                    {{ $b['diterima'] }}
                                </td>
                                <td style="text-align: center; font-weight: 700; color: var(--purple-700, #7E22CE);">
                                    {{ $b['selesai'] }}
                                </td>
                                <td style="text-align: center;">
                                    <span class="badge {{ $b['rasio'] > 50 ? 'badge-success' : ($b['rasio'] > 0 ? 'badge-info' : 'badge-neutral') }}" style="font-size: 0.75rem;">
                                        {{ $b['rasio'] }}%
                                    </span>
                                </td>
                                @if($canViewNilai)
                                    <td style="text-align: center; font-weight: 700; color: var(--neutral-800);">
                                        {{ $b['rata_nilai'] !== null ? number_format((float) $b['rata_nilai'], 1) : '-' }}
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- KOLOM KANAN: KARTU GOALS (TARGET & CAPAIAN KINERJA) -->
        <div class="tahunan-section-card goals-card">
            <div class="goals-header">
                <div>
                    <h3 class="tahunan-section-title">Goals</h3>
                    <p class="tahunan-section-desc">Target &amp; capaian indikator kinerja magang tahun {{ $tahun }}.</p>
                </div>
                <span class="badge badge-info" style="font-size: 0.6875rem;">Target Kinerja</span>
            </div>

            @php
                $targetKuotaVal = max($totalKuota ?? 0, $totalDiterima, 1);
                $pctKuota = min(100, round(($totalDiterima / $targetKuotaVal) * 100, 1));

                $targetPendaftarVal = max($totalPendaftaran, 1);
                $pctSeleksi = min(100, round(($totalDiterima / $targetPendaftarVal) * 100, 1));

                $targetSelesaiVal = max($totalDiterima, 1);
                $pctSelesai = min(100, round(($totalSelesai / $targetSelesaiVal) * 100, 1));

                $mitraAktifCount = count($topInstitusi);
                $targetMitraVal = max(10, $mitraAktifCount);
                $pctMitra = min(100, round(($mitraAktifCount / $targetMitraVal) * 100, 1));

                $bulanAktif = $bulanAktifCount ?? 0;
                $pctBulan = min(100, round(($bulanAktif / 12) * 100, 1));
            @endphp

            <div class="goals-list">
                <!-- Item 1: Penyerapan Kuota Magang (Teal) -->
                <div class="goal-item">
                    <div class="goal-item-top">
                        <span class="goal-label">Penyerapan Kuota Magang</span>
                        <span class="goal-value">{{ number_format($totalDiterima) }} / {{ number_format($targetKuotaVal) }}</span>
                    </div>
                    <div class="goal-bar-track">
                        <div class="goal-bar-fill goal-teal" style="width: {{ $pctKuota }}%;"></div>
                    </div>
                </div>

                <!-- Item 2: Kelulusan Seleksi Pendaftar (Green) -->
                <div class="goal-item">
                    <div class="goal-item-top">
                        <span class="goal-label">Kelulusan Seleksi Pendaftar</span>
                        <span class="goal-value">{{ number_format($totalDiterima) }} / {{ number_format($targetPendaftarVal) }}</span>
                    </div>
                    <div class="goal-bar-track">
                        <div class="goal-bar-fill goal-green" style="width: {{ $pctSeleksi }}%;"></div>
                    </div>
                </div>

                <!-- Item 3: Penyelesaian Masa Magang (Blue) -->
                <div class="goal-item">
                    <div class="goal-item-top">
                        <span class="goal-label">Penyelesaian Masa Magang</span>
                        <span class="goal-value">{{ number_format($totalSelesai) }} / {{ number_format($targetSelesaiVal) }}</span>
                    </div>
                    <div class="goal-bar-track">
                        <div class="goal-bar-fill goal-blue" style="width: {{ $pctSelesai }}%;"></div>
                    </div>
                </div>

                <!-- Item 4: Jangkauan Institusi Mitra (Amber) -->
                <div class="goal-item">
                    <div class="goal-item-top">
                        <span class="goal-label">Jangkauan Institusi Mitra</span>
                        <span class="goal-value">{{ number_format($mitraAktifCount) }} / {{ number_format($targetMitraVal) }}</span>
                    </div>
                    <div class="goal-bar-track">
                        <div class="goal-bar-fill goal-amber" style="width: {{ $pctMitra }}%;"></div>
                    </div>
                </div>

                <!-- Item 5: Cakupan Periode Aktif (Purple) -->
                <div class="goal-item">
                    <div class="goal-item-top">
                        <span class="goal-label">Cakupan Periode Aktif</span>
                        <span class="goal-value">{{ number_format($bulanAktif) }} / 12</span>
                    </div>
                    <div class="goal-bar-track">
                        <div class="goal-bar-fill goal-purple" style="width: {{ $pctBulan }}%;"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- 4. 2-COLUMN GRID: TOP INSTITUSI & DISTRIBUSI/MUTU -->
    <div class="tahunan-bottom-grid">
        
        <!-- KOLOM KIRI: TOP 10 INSTITUSI PENGIRIM MITRA -->
        <div class="tahunan-section-card">
            <h3 class="tahunan-section-title" style="font-size: 1.0625rem; margin-bottom: 0.25rem;">Top 10 Institusi Pengirim Mitra</h3>
            <p class="tahunan-section-desc" style="margin-bottom: 1rem;">Perguruan tinggi dan SMK dengan kontribusi pemohon magang terbanyak tahun {{ $tahun }}.</p>

            <div class="clean-scrollbar" style="max-height: 420px; overflow-y: auto;">
                <table class="tahunan-table" style="font-size: 0.8125rem;">
                    <thead>
                        <tr>
                            <th>Institusi</th>
                            <th style="text-align: center;">Pelamar</th>
                            <th style="text-align: center;">Lolos</th>
                            <th style="text-align: right;">% Diterima</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topInstitusi as $inst)
                            @php
                                $rasioInst = $inst->total_pelamar > 0 ? round(($inst->total_diterima / $inst->total_pelamar) * 100, 1) : 0;
                            @endphp
                            <tr>
                                <td style="font-weight: 600; color: var(--neutral-900);">
                                    {{ $inst->institusi }}
                                </td>
                                <td style="text-align: center; font-weight: 700; color: var(--blue-700);">
                                    {{ $inst->total_pelamar }}
                                </td>
                                <td style="text-align: center; font-weight: 700; color: var(--green-700);">
                                    {{ $inst->total_diterima }}
                                </td>
                                <td style="text-align: right; font-weight: 700; color: var(--neutral-800);">
                                    <span class="badge {{ $rasioInst > 50 ? 'badge-success' : ($rasioInst > 0 ? 'badge-info' : 'badge-neutral') }}" style="font-size: 0.75rem;">
                                        {{ $rasioInst }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 2rem; color: var(--neutral-500);">
                                    Belum ada data pendaftar pada tahun {{ $tahun }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- KOLOM KANAN: DISTRIBUSI JENJANG & MUTU KELULUSAN (A-20 GUARD) -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            
            <!-- DISTRIBUSI JENJANG PENDIDIKAN -->
            <div class="tahunan-section-card">
                <h3 class="tahunan-section-title" style="font-size: 1.0625rem; margin-bottom: 0.25rem;">Distribusi Jenjang Pendidikan</h3>
                <p class="tahunan-section-desc" style="margin-bottom: 1rem;">Proporsi pemohon magang berdasarkan tingkat pendidikan.</p>

                <div class="tahunan-jenjang-grid">
                    <div class="tahunan-jenjang-card smk">
                        <span class="tahunan-jenjang-label">SMK</span>
                        <div class="tahunan-jenjang-value">
                            {{ $jenjangDistribusi['smk'] ?? 0 }}
                        </div>
                    </div>
                    <div class="tahunan-jenjang-card d3">
                        <span class="tahunan-jenjang-label">Diploma 3 (D3)</span>
                        <div class="tahunan-jenjang-value">
                            {{ $jenjangDistribusi['d3'] ?? 0 }}
                        </div>
                    </div>
                    <div class="tahunan-jenjang-card s1">
                        <span class="tahunan-jenjang-label">Sarjana (S1)</span>
                        <div class="tahunan-jenjang-value">
                            {{ $jenjangDistribusi['s1'] ?? 0 }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- ANALISIS MUTU & PREDIKAT KELULUSAN (KHUSUS SUPERADMIN - ATURAN A-20) -->
            @if($canViewNilai)
                <div class="tahunan-section-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <h3 class="tahunan-section-title" style="font-size: 1.0625rem;">Sebaran Predikat & Mutu Nilai</h3>
                        <span class="badge badge-primary" style="font-size: 0.6875rem;">SuperAdmin Only (A-20)</span>
                    </div>
                    <p class="tahunan-section-desc" style="margin-bottom: 1rem;">
                        Rata-rata Skor Kelulusan: <strong style="color: var(--blue-700);">{{ $rataRataNilai !== null ? number_format((float) $rataRataNilai, 2) : 'Belum ada data' }}</strong>
                    </p>

                    <div style="display: flex; flex-direction: column; gap: 0.625rem;">
                        @foreach(['A' => 'Sangat Baik (≥ 85)', 'B' => 'Baik (70 - 84)', 'C' => 'Cukup (55 - 69)', 'D' => 'Kurang (40 - 54)', 'E' => 'Sangat Kurang (< 40)'] as $pred => $lbl)
                            @php
                                $totalNilai = array_sum($sebaranPredikat);
                                $pct = $totalNilai > 0 ? round(($sebaranPredikat[$pred] / $totalNilai) * 100, 1) : 0;
                            @endphp
                            <div>
                                <div style="display: flex; justify-content: space-between; font-size: 0.8125rem; margin-bottom: 0.25rem;">
                                    <span style="font-weight: 700; color: var(--neutral-800);">Predikat {{ $pred }} &bull; <small style="font-weight: normal; color: var(--neutral-600);">{{ $lbl }}</small></span>
                                    <span style="font-weight: 700; color: var(--blue-800);">{{ $sebaranPredikat[$pred] }} siswa ({{ $pct }}%)</span>
                                </div>
                                <div style="height: 8px; border-radius: 9999px; background: var(--neutral-200); overflow: hidden;">
                                    <div style="width: {{ $pct }}%; height: 100%; background: {{ $pred === 'A' ? 'var(--green-600)' : ($pred === 'B' ? 'var(--blue-600)' : ($pred === 'C' ? 'var(--amber-600)' : 'var(--red-600)')) }};"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <!-- INFO RESTRIKSI ADMIN (ATURAN A-20) -->
                <div class="tahunan-a20-banner">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin: 0 auto 0.5rem auto; color: var(--neutral-400);">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <p style="margin: 0; font-weight: 700; color: var(--neutral-700);">Rekapitulasi Mutu Nilai Dilindungi</p>
                    <p style="margin: 0.25rem 0 0 0;">Sesuai Aturan Bisnis PRD Bab 3.3 & Asumsi A-20, rincian skor evaluasi magang hanya dapat diakses oleh peran SuperAdmin.</p>
                </div>
            @endif

        </div>

    </div>

</div>
@endsection
