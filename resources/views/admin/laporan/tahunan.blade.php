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
<div style="display: flex; flex-direction: column; gap: 1.5rem;">

    @push('styles')
    <style>
        /* =====================================================================
           DARK MODE OVERRIDES (DS-MagKom Standards)
           ===================================================================== */
        [data-theme="dark"] div[style*="background: var(--neutral-0)"] {
            background: #182234 !important;
            border-color: #26354D !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25) !important;
        }

        [data-theme="dark"] h2,
        [data-theme="dark"] h3,
        [data-theme="dark"] h4 {
            color: #F8FAFC !important;
        }

        [data-theme="dark"] label[style*="color: var(--neutral-800)"],
        [data-theme="dark"] strong[style*="color: var(--neutral-900)"] {
            color: #F8FAFC !important;
        }

        [data-theme="dark"] p[style*="color: var(--neutral-600)"],
        [data-theme="dark"] span[style*="color: var(--neutral-600)"],
        [data-theme="dark"] div[style*="color: var(--neutral-600)"] {
            color: #94A3B8 !important;
        }

        [data-theme="dark"] select#tahun {
            background-color: #0F172A !important;
            border-color: #26354D !important;
            color: #93C5FD !important;
        }

        [data-theme="dark"] select#tahun option {
            background-color: #0F172A !important;
            color: #F8FAFC !important;
        }

        [data-theme="dark"] .btn-outline {
            background: #111C2E !important;
            border-color: #26354D !important;
            color: #CBD5E1 !important;
        }

        [data-theme="dark"] .btn-outline:hover {
            background: #162238 !important;
            border-color: #3B82F6 !important;
            color: #60A5FA !important;
        }

        [data-theme="dark"] table thead tr {
            border-bottom-color: #26354D !important;
            color: #94A3B8 !important;
        }

        [data-theme="dark"] table tbody tr {
            border-bottom-color: #26354D !important;
        }

        [data-theme="dark"] table tbody td {
            color: #CBD5E1 !important;
        }

        [data-theme="dark"] table tbody tr:hover {
            background: #162238 !important;
        }
    </style>
    @endpush

    <!-- TOP TOOLBAR & YEAR SELECTOR -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1rem 1.25rem; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem; box-shadow: var(--shadow-subtle);">
        <form method="GET" action="{{ route('admin.laporan.tahunan') }}" style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            <label for="tahun" style="font-weight: 700; font-size: 0.875rem; color: var(--neutral-800);">Pilih Tahun Kalender:</label>
            <select name="tahun" id="tahun" onchange="this.form.submit()" style="padding: 0.5rem 1rem; border: 1px solid var(--neutral-400); border-radius: 8px; font-weight: 700; color: var(--blue-800); background: var(--neutral-0); font-size: 0.875rem;">
                @foreach($daftarTahun as $t)
                    <option value="{{ $t }}" {{ $t == $tahun ? 'selected' : '' }}>Tahun {{ $t }}</option>
                @endforeach
            </select>
            <span style="font-size: 0.8125rem; color: var(--neutral-600);">Menampilkan seluruh data bulan magang tahun {{ $tahun }}</span>
        </form>

        <div style="display: flex; gap: 0.75rem; align-items: center;">
            <a href="{{ route('admin.laporan.index') }}" class="btn btn-outline" style="font-size: 0.8125rem;">
                &larr; Hub Laporan Utama
            </a>
            <a href="{{ route('admin.laporan.tahunan.export', ['tahun' => $tahun]) }}" class="btn btn-primary" style="font-size: 0.8125rem; display: inline-flex; align-items: center; gap: 0.375rem;">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Unduh Rekap CSV (Sanitized)
            </a>
        </div>
    </div>

    <!-- 4 EXECUTIVE SUMMARY KPI CARDS -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem; box-shadow: var(--shadow-subtle);">
            <div style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-600); text-transform: uppercase;">Total Pendaftar Masuk</div>
            <div style="font-size: 2rem; font-weight: 900; color: var(--blue-700); margin-top: 0.25rem;">
                {{ number_format($totalPendaftaran) }}
            </div>
            <div style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.25rem;">Calon peserta yang mengajukan berkas</div>
        </div>

        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem; box-shadow: var(--shadow-subtle);">
            <div style="font-size: 0.75rem; font-weight: 700; color: var(--green-700); text-transform: uppercase;">Peserta Diterima Magang</div>
            <div style="font-size: 2rem; font-weight: 900; color: var(--green-700); margin-top: 0.25rem;">
                {{ number_format($totalDiterima) }}
            </div>
            <div style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.25rem;">
                Tingkat Penerimaan: <strong>{{ number_format($acceptanceRate, 1) }}%</strong>
            </div>
        </div>

        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem; box-shadow: var(--shadow-subtle);">
            <div style="font-size: 0.75rem; font-weight: 700; color: var(--purple-700, #7E22CE); text-transform: uppercase;">Alumni Magang Lulus</div>
            <div style="font-size: 2rem; font-weight: 900; color: var(--purple-700, #7E22CE); margin-top: 0.25rem;">
                {{ number_format($totalSelesai) }}
            </div>
            <div style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.25rem;">
                Tingkat Kelulusan: <strong>{{ number_format($completionRate, 1) }}%</strong>
            </div>
        </div>

        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem; box-shadow: var(--shadow-subtle);">
            <div style="font-size: 0.75rem; font-weight: 700; color: var(--amber-700); text-transform: uppercase;">Rata-rata Durasi Magang</div>
            <div style="font-size: 2rem; font-weight: 900; color: var(--amber-700); margin-top: 0.25rem;">
                {{ $rataRataDurasi }} <span style="font-size: 1.125rem; font-weight: 600;">Bulan</span>
            </div>
            <div style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.25rem;">Rentang normal 1 sampai 6 bulan</div>
        </div>
    </div>

    <!-- TREN BULANAN TABEL & PROGRESS -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.5rem;">
            <div>
                <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Tren Penyerapan Bulanan (Januari - Desember {{ $tahun }})</h3>
                <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0.25rem 0 0 0;">Perbandingan volume pengajuan berkas, peserta ditempatkan, dan alumni selesai per bulan.</p>
            </div>
        </div>

        <div style="overflow-x: auto;" class="clean-scrollbar">
            <table class="table" style="width: 100%; text-align: left; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--neutral-300); font-size: 0.8125rem; color: var(--neutral-600);">
                        <th style="padding: 0.75rem;">Bulan</th>
                        <th style="padding: 0.75rem; text-align: center;">Pendaftar</th>
                        <th style="padding: 0.75rem; text-align: center;">Diterima</th>
                        <th style="padding: 0.75rem; text-align: center;">Lulus / Selesai</th>
                        <th style="padding: 0.75rem; text-align: center;">Rasio Penerimaan</th>
                        @if($canViewNilai)
                            <th style="padding: 0.75rem; text-align: center;">Rata-rata Nilai</th>
                        @endif
                        <th style="padding: 0.75rem; width: 30%;">Grafik Proporsi</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.875rem;">
                    @foreach($trenBulanan as $b)
                        <tr style="border-bottom: 1px solid var(--neutral-200);">
                            <td style="padding: 0.75rem; font-weight: 700; color: var(--neutral-800);">
                                {{ $b['nama_bulan'] }}
                            </td>
                            <td style="padding: 0.75rem; text-align: center; font-weight: 700; color: var(--blue-700);">
                                {{ $b['pendaftar'] }}
                            </td>
                            <td style="padding: 0.75rem; text-align: center; font-weight: 700; color: var(--green-700);">
                                {{ $b['diterima'] }}
                            </td>
                            <td style="padding: 0.75rem; text-align: center; font-weight: 700; color: var(--purple-700, #7E22CE);">
                                {{ $b['selesai'] }}
                            </td>
                            <td style="padding: 0.75rem; text-align: center;">
                                <span class="badge {{ $b['rasio'] > 50 ? 'badge-success' : ($b['rasio'] > 0 ? 'badge-info' : 'badge-neutral') }}" style="font-size: 0.75rem;">
                                    {{ $b['rasio'] }}%
                                </span>
                            </td>
                            @if($canViewNilai)
                                <td style="padding: 0.75rem; text-align: center; font-weight: 700; color: var(--neutral-800);">
                                    {{ $b['rata_nilai'] !== null ? number_format((float) $b['rata_nilai'], 1) : '-' }}
                                </td>
                            @endif
                            <td style="padding: 0.75rem;">
                                <div style="display: flex; height: 10px; border-radius: 9999px; overflow: hidden; background: var(--neutral-200); width: 100%;">
                                    <div style="width: {{ min(100, $b['rasio']) }}%; background: var(--blue-700);" title="Penerimaan: {{ $b['rasio'] }}%"></div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- 2 COLUMN GRID: TOP INSTITUSI & DISTRIBUSI/MUTU -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 1.5rem;">
        
        <!-- KOLOM KIRI: TOP 10 INSTITUSI PENGIRIM MITRA -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
            <h3 style="font-size: 1.0625rem; font-weight: 800; color: var(--neutral-900); margin: 0 0 0.5rem 0;">Top 10 Institusi Pengirim Mitra</h3>
            <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0 0 1rem 0;">Perguruan tinggi dan SMK dengan kontribusi pemohon magang terbanyak tahun {{ $tahun }}.</p>

            <div class="clean-scrollbar" style="max-height: 420px; overflow-y: auto;">
                <table class="table" style="width: 100%; border-collapse: collapse; font-size: 0.8125rem;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--neutral-300); color: var(--neutral-600);">
                            <th style="padding: 0.5rem;">Institusi</th>
                            <th style="padding: 0.5rem; text-align: center;">Pelamar</th>
                            <th style="padding: 0.5rem; text-align: center;">Lolos</th>
                            <th style="padding: 0.5rem; text-align: right;">% Diterima</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topInstitusi as $inst)
                            @php
                                $rasioInst = $inst->total_pelamar > 0 ? round(($inst->total_diterima / $inst->total_pelamar) * 100, 1) : 0;
                            @endphp
                            <tr style="border-bottom: 1px solid var(--neutral-200);">
                                <td style="padding: 0.625rem; font-weight: 600; color: var(--neutral-900);">
                                    {{ $inst->institusi }}
                                </td>
                                <td style="padding: 0.625rem; text-align: center; font-weight: 700; color: var(--blue-700);">
                                    {{ $inst->total_pelamar }}
                                </td>
                                <td style="padding: 0.625rem; text-align: center; font-weight: 700; color: var(--green-700);">
                                    {{ $inst->total_diterima }}
                                </td>
                                <td style="padding: 0.625rem; text-align: right; font-weight: 700;">
                                    {{ $rasioInst }}%
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
            <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
                <h3 style="font-size: 1.0625rem; font-weight: 800; color: var(--neutral-900); margin: 0 0 0.5rem 0;">Distribusi Jenjang Pendidikan</h3>
                <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0 0 1rem 0;">Proporsi pemohon magang berdasarkan tingkat pendidikan.</p>

                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem;">
                    <div style="background: var(--blue-50); border: 1px solid var(--blue-200); border-radius: 8px; padding: 1rem; text-align: center;">
                        <span style="font-size: 0.75rem; font-weight: 700; color: var(--blue-800); text-transform: uppercase;">SMK</span>
                        <div style="font-size: 1.5rem; font-weight: 900; color: var(--blue-900); margin-top: 0.25rem;">
                            {{ $jenjangDistribusi['smk'] ?? 0 }}
                        </div>
                    </div>
                    <div style="background: var(--emerald-50, #ECFDF5); border: 1px solid var(--emerald-200, #A7F3D0); border-radius: 8px; padding: 1rem; text-align: center;">
                        <span style="font-size: 0.75rem; font-weight: 700; color: var(--emerald-800, #065F46); text-transform: uppercase;">Diploma 3 (D3)</span>
                        <div style="font-size: 1.5rem; font-weight: 900; color: var(--emerald-900, #064E3B); margin-top: 0.25rem;">
                            {{ $jenjangDistribusi['d3'] ?? 0 }}
                        </div>
                    </div>
                    <div style="background: var(--purple-50, #FAF5FF); border: 1px solid var(--purple-200, #E9D5FF); border-radius: 8px; padding: 1rem; text-align: center;">
                        <span style="font-size: 0.75rem; font-weight: 700; color: var(--purple-800, #581C87); text-transform: uppercase;">Sarjana (S1)</span>
                        <div style="font-size: 1.5rem; font-weight: 900; color: var(--purple-900, #3B0764); margin-top: 0.25rem;">
                            {{ $jenjangDistribusi['s1'] ?? 0 }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- ANALISIS MUTU & PREDIKAT KELULUSAN (KHUSUS SUPERADMIN - ATURAN A-20) -->
            @if($canViewNilai)
                <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <h3 style="font-size: 1.0625rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Sebaran Predikat & Mutu Nilai</h3>
                        <span class="badge badge-primary" style="font-size: 0.6875rem;">SuperAdmin Only (A-20)</span>
                    </div>
                    <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0 0 1rem 0;">
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
                <div style="background: var(--neutral-50); border: 1px dashed var(--neutral-400); border-radius: 12px; padding: 1.25rem; text-align: center; color: var(--neutral-600); font-size: 0.8125rem;">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin: 0 auto 0.5rem auto; color: var(--neutral-400);">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <p style="margin: 0; font-weight: 600; color: var(--neutral-700);">Rekapitulasi Mutu Nilai Dilindungi</p>
                    <p style="margin: 0.25rem 0 0 0;">Sesuai Aturan Bisnis PRD Bab 3.3 & Asumsi A-20, rincian skor evaluasi magang hanya dapat diakses oleh peran SuperAdmin.</p>
                </div>
            @endif

        </div>

    </div>

</div>
@endsection
