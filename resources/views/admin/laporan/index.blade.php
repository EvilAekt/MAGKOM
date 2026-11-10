@extends('layouts.dashboard')

@section('title', 'Hub Laporan & Ekspor - Admin MagKom')
@section('topbar_title', 'Hub Laporan & Ekspor Data')

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">

    {{-- 0. SLEMAN NAVY HERO BANNER (Signature MagKom) --}}
    <div class="adm-hero-banner">
        <div class="adm-hero-content">
            <div class="adm-hero-text">
                <h1 class="adm-hero-title">Pusat Rekapitulasi Data Magang</h1>
                <div class="adm-hero-subtitle">
                    <span>Rekapitulasi Pendaftaran, Penempatan &amp; Arsip Magang</span>
                    <span class="sep">&bull;</span>
                    <span>Dinas Komunikasi dan Informatika Kabupaten Sleman</span>
                </div>
            </div>
        </div>
    </div>

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

        [data-theme="dark"] div[style*="background: #F0F4FF"] {
            background: rgba(37, 99, 235, 0.15) !important;
            border-color: #26354D !important;
        }
        [data-theme="dark"] div[style*="background: #F0FDF4"] {
            background: rgba(16, 185, 129, 0.15) !important;
            border-color: #26354D !important;
        }
        [data-theme="dark"] div[style*="background: #FAF5FF"] {
            background: rgba(147, 51, 234, 0.15) !important;
            border-color: #26354D !important;
        }

        [data-theme="dark"] h2,
        [data-theme="dark"] h3,
        [data-theme="dark"] h4 {
            color: #F8FAFC !important;
        }

        [data-theme="dark"] span[style*="color: var(--neutral-800)"],
        [data-theme="dark"] strong[style*="color: var(--neutral-900)"],
        [data-theme="dark"] div[style*="color: var(--neutral-800)"],
        [data-theme="dark"] td[style*="color: var(--neutral-800)"] {
            color: #F8FAFC !important;
        }

        [data-theme="dark"] p[style*="color: var(--neutral-600)"],
        [data-theme="dark"] span[style*="color: var(--neutral-600)"],
        [data-theme="dark"] small[style*="color: var(--neutral-600)"],
        [data-theme="dark"] div[style*="color: var(--neutral-600)"] {
            color: #94A3B8 !important;
        }

        [data-theme="dark"] select,
        [data-theme="dark"] input[type="text"],
        [data-theme="dark"] input[type="date"] {
            background-color: #0F172A !important;
            border-color: #26354D !important;
            color: #F8FAFC !important;
        }

        [data-theme="dark"] select option {
            background-color: #0F172A !important;
            color: #F8FAFC !important;
        }

        [data-theme="dark"] table thead tr {
            background: #0E1726 !important;
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

        [data-theme="dark"] a[style*="background: transparent"][style*="color: var(--neutral-700)"] {
            color: #94A3B8 !important;
        }

        [data-theme="dark"] a[style*="background: transparent"][style*="color: var(--neutral-700)"]:hover {
            background: #162238 !important;
            color: #F8FAFC !important;
        }

        [data-theme="dark"] a[style*="background: #ffffff"],
        [data-theme="dark"] a[style*="background: var(--neutral-0)"] {
            background: #111C2E !important;
            border-color: #26354D !important;
            color: #CBD5E1 !important;
        }

        [data-theme="dark"] a[style*="background: #ffffff"]:hover,
        [data-theme="dark"] a[style*="background: var(--neutral-0)"]:hover {
            background: #162238 !important;
            border-color: #3B82F6 !important;
            color: #60A5FA !important;
        }

        [data-theme="dark"] div[style*="border-top: 1px solid var(--neutral-200)"] {
            border-top-color: #26354D !important;
        }
    </style>
    @endpush

    <!-- 1. SUMMARY METRICS CARDS (3x2 BALANCED GRID) -->
    <div class="row tile_count adm-laporan-kpi-grid" role="region" aria-label="Metrik Ringkasan Laporan">
        {{-- Card 1: Total Pengajuan --}}
        <x-gentelella.tile-stat 
            title="Total Pengajuan" 
            :value="number_format($metricCards['total_pendaftaran'])" 
            icon="file-text"
            color="blue"
            trendText="Total"
            trendDirection="up"
            subText="Semua berkas masuk portal" 
            :sparkBars="[35, 50, 45, 65, 70, 85, 95]"
        />

        {{-- Card 2: Peserta Diterima --}}
        <x-gentelella.tile-stat 
            title="Peserta Diterima" 
            :value="number_format($metricCards['total_diterima'])" 
            icon="user-check"
            color="teal"
            trendText="Diterima"
            trendDirection="up"
            subText="Lolos seleksi berkas & kuota" 
            :sparkBars="[25, 40, 50, 65, 75, 85, 95]"
        />

        {{-- Card 3: Penempatan Aktif --}}
        <x-gentelella.tile-stat 
            title="Penempatan Aktif" 
            :value="number_format($metricCards['penempatan_aktif'])" 
            icon="activity"
            color="blue"
            trendText="Aktif"
            trendDirection="up"
            subText="Sedang menjalani masa magang" 
            :sparkBars="[40, 50, 60, 68, 75, 82, 90]"
        />

        {{-- Card 4: Magang Selesai --}}
        <x-gentelella.tile-stat 
            title="Magang Selesai" 
            :value="number_format($metricCards['penempatan_selesai'])" 
            icon="award"
            color="teal"
            trendText="Selesai"
            trendDirection="up"
            subText="Telah tuntas dinas" 
            :sparkBars="[30, 45, 55, 65, 75, 85, 100]"
        />
    </div>

    <!-- 1.5 VISUAL ANALYTICS & INSIGHTS (AC-17-1) -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 1.25rem;">
        <!-- KARTU 1: TINGKAT KETERISIAN KUOTA PER JURUSAN -->
        <x-gentelella.card title="Tingkat Keterisian Kuota per Jurusan"
                           badge="Live Capacity"
                           badgeType="primary"
                           style="display: flex; flex-direction: column;">
            <x-slot:icon>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color: var(--blue-600);"><path d="M18 20V10"></path><path d="M12 20V4"></path><path d="M6 20v-6"></path></svg>
            </x-slot:icon>

            <div style="display: flex; flex-direction: column; gap: 1rem; max-height: 280px; overflow-y: auto; padding-right: 0.25rem;" class="clean-scrollbar">
                @forelse($analyticsKuotaJurusan ?? [] as $item)
                    <div style="display: flex; flex-direction: column; gap: 0.375rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8125rem;">
                            <span style="font-weight: 700; color: var(--neutral-800); max-width: 60%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $item['nama_jurusan'] }}">
                                {{ $item['nama_jurusan'] }}
                            </span>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <span style="color: var(--neutral-600); font-size: 0.75rem;">
                                    <strong>{{ $item['terisi'] }}</strong> / {{ $item['kuota'] }} Kursi
                                </span>
                                @if($item['persen'] >= 100)
                                    <span style="font-size: 0.6875rem; font-weight: 800; background: var(--red-100, #FEE2E2); color: var(--red-700, #B91C1C); padding: 0.15rem 0.5rem; border-radius: 6px;">Penuh</span>
                                @elseif($item['persen'] >= 75)
                                    <span style="font-size: 0.6875rem; font-weight: 800; background: var(--amber-100, #FEF3C7); color: var(--amber-800, #92400E); padding: 0.15rem 0.5rem; border-radius: 6px;">Padat</span>
                                @else
                                    <span style="font-size: 0.6875rem; font-weight: 800; background: var(--green-100, #DCFCE7); color: var(--green-800, #166534); padding: 0.15rem 0.5rem; border-radius: 6px;">Tersedia</span>
                                @endif
                            </div>
                        </div>
                        <div style="width: 100%; height: 8px; background: var(--neutral-200); border-radius: 999px; overflow: hidden; position: relative;">
                            <div style="height: 100%; width: {{ $item['persen'] }}%; border-radius: 999px; background: {{ $item['persen'] >= 100 ? 'var(--red-600)' : ($item['persen'] >= 75 ? 'var(--amber-600)' : 'linear-gradient(90deg, var(--blue-600) 0%, var(--blue-700) 100%)') }}; transition: width 0.4s ease;"></div>
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 2rem 1rem; color: var(--neutral-500); font-size: 0.875rem;">
                        Belum ada data kuota aktif pada sistem.
                    </div>
                @endforelse
            </div>
        </x-gentelella.card>

        <!-- KARTU 2: PROPORSI JENJANG PENDIDIKAN PESERTA -->
        <x-gentelella.card title="Proporsi Jenjang Calon Peserta"
                           badge="Demografi"
                           badgeType="success"
                           style="display: flex; flex-direction: column;">
            <x-slot:icon>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color: var(--green-600);"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </x-slot:icon>

            <!-- STACKED HORIZONTAL BAR -->
            @php
                $totJenjang = $analyticsJenjang['total'] ?? 0;
            @endphp
            <div style="width: 100%; height: 14px; background: var(--neutral-200); border-radius: 999px; overflow: hidden; display: flex; margin-bottom: 1.5rem;">
                @if($totJenjang > 0)
                    <div style="height: 100%; width: {{ $analyticsJenjang['smk']['persen'] ?? 0 }}%; background: #0641CC;" title="SMK: {{ $analyticsJenjang['smk']['persen'] ?? 0 }}%"></div>
                    <div style="height: 100%; width: {{ $analyticsJenjang['d3']['persen'] ?? 0 }}%; background: #16A34A;" title="D3: {{ $analyticsJenjang['d3']['persen'] ?? 0 }}%"></div>
                    <div style="height: 100%; width: {{ $analyticsJenjang['s1']['persen'] ?? 0 }}%; background: #9333EA;" title="S1: {{ $analyticsJenjang['s1']['persen'] ?? 0 }}%"></div>
                @else
                    <div style="height: 100%; width: 100%; background: var(--neutral-300);"></div>
                @endif
            </div>

            <!-- BREAKDOWN CARDS -->
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; margin-top: auto;">
                <!-- SMK -->
                <div style="background: #F0F4FF; border: 1px solid #D6E4FF; border-radius: 10px; padding: 0.875rem 0.75rem; text-align: center;">
                    <div style="display: flex; align-items: center; justify-content: center; gap: 0.35rem; margin-bottom: 0.25rem;">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #0641CC; display: inline-block;"></span>
                        <span style="font-size: 0.75rem; font-weight: 700; color: #1E3A8A;">SMK</span>
                    </div>
                    <div style="font-size: 1.25rem; font-weight: 900; color: #0641CC;">
                        {{ $analyticsJenjang['smk']['count'] ?? 0 }}
                    </div>
                    <small style="font-size: 0.6875rem; color: #3B82F6; font-weight: 700;">
                        {{ $analyticsJenjang['smk']['persen'] ?? 0 }}% Total
                    </small>
                </div>

                <!-- D3 -->
                <div style="background: #F0FDF4; border: 1px solid #DCFCE7; border-radius: 10px; padding: 0.875rem 0.75rem; text-align: center;">
                    <div style="display: flex; align-items: center; justify-content: center; gap: 0.35rem; margin-bottom: 0.25rem;">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #16A34A; display: inline-block;"></span>
                        <span style="font-size: 0.75rem; font-weight: 700; color: #14532D;">Diploma (D3)</span>
                    </div>
                    <div style="font-size: 1.25rem; font-weight: 900; color: #16A34A;">
                        {{ $analyticsJenjang['d3']['count'] ?? 0 }}
                    </div>
                    <small style="font-size: 0.6875rem; color: #22C55E; font-weight: 700;">
                        {{ $analyticsJenjang['d3']['persen'] ?? 0 }}% Total
                    </small>
                </div>

                <!-- S1 -->
                <div style="background: #FAF5FF; border: 1px solid #F3E8FF; border-radius: 10px; padding: 0.875rem 0.75rem; text-align: center;">
                    <div style="display: flex; align-items: center; justify-content: center; gap: 0.35rem; margin-bottom: 0.25rem;">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #9333EA; display: inline-block;"></span>
                        <span style="font-size: 0.75rem; font-weight: 700; color: #581C87;">Sarjana (S1)</span>
                    </div>
                    <div style="font-size: 1.25rem; font-weight: 900; color: #9333EA;">
                        {{ $analyticsJenjang['s1']['count'] ?? 0 }}
                    </div>
                    <small style="font-size: 0.6875rem; color: #A855F7; font-weight: 700;">
                        {{ $analyticsJenjang['s1']['persen'] ?? 0 }}% Total
                    </small>
                </div>
            </div>

            <x-slot:footer>
                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; color: var(--neutral-600); width: 100%;">
                    <span>Total Profil Calon Peserta Terdata</span>
                    <strong style="color: var(--neutral-900); font-weight: 800;">{{ number_format($totJenjang) }} Orang</strong>
                </div>
            </x-slot:footer>
        </x-gentelella.card>
    </div>

    <!-- 2. TAB SWITCHER -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 0.5rem; box-shadow: var(--shadow-subtle); display: flex; gap: 0.5rem; flex-wrap: wrap;">
        <a href="{{ route('admin.laporan.index', ['tab' => 'pendaftaran']) }}"
           style="flex: 1; min-width: 160px; padding: 0.625rem 1rem; border-radius: 8px; text-decoration: none; font-size: 0.875rem; font-weight: 700; text-align: center; transition: all 0.15s;
           background: {{ $tab === 'pendaftaran' ? 'var(--blue-600)' : 'transparent' }};
           color: {{ $tab === 'pendaftaran' ? '#FFFFFF' : 'var(--neutral-700)' }};">
            Rekap Pendaftaran
        </a>

        <a href="{{ route('admin.laporan.index', ['tab' => 'penempatan']) }}"
           style="flex: 1; min-width: 160px; padding: 0.625rem 1rem; border-radius: 8px; text-decoration: none; font-size: 0.875rem; font-weight: 700; text-align: center; transition: all 0.15s;
           background: {{ $tab === 'penempatan' ? 'var(--blue-600)' : 'transparent' }};
           color: {{ $tab === 'penempatan' ? '#FFFFFF' : 'var(--neutral-700)' }};">
            Data Penempatan
        </a>
    </div>

    <!-- 3. TAB 1: REKAP PENDAFTARAN -->
    @if($tab === 'pendaftaran')
        <x-gentelella.x-panel class="has-table" title="Laporan Rekapitulasi Pendaftaran">
            <x-slot:actions>
                {{-- Unified Export Popover Trigger & Container (Pendaftaran) --}}
                <div class="adm-export-dropdown-wrap" style="position: relative;">
                    <button type="button" 
                            id="btnToggleExportPopoverPendaftaran" 
                            class="adm-btn-export-main" 
                            aria-expanded="false" 
                            aria-haspopup="menu" 
                            aria-controls="admExportPopoverPendaftaran"
                            title="Pilih dan unduh rekap data pendaftaran (Excel, CSV, PDF)">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        <span>Ekspor Rekap</span>
                        <svg class="adm-export-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>

                    {{-- Floating Dropdown Popover Container (Right-Aligned) --}}
                    <div id="admExportPopoverPendaftaran" class="adm-export-popover adm-export-popover-right" style="display: none;" role="menu" aria-label="Menu Pilihan Format Ekspor Pendaftaran">
                        <div class="adm-popover-pointer adm-pointer-right"></div>
                        <div class="adm-popover-header">
                            <div>
                                <div class="adm-popover-title">Ekspor Rekap Cepat</div>
                                <div class="adm-popover-desc">Data pendaftaran hasil filter aktif</div>
                            </div>
                            <span class="adm-popover-row-count tabular-nums">
                                {{ number_format($pendaftaranData->total()) }} Baris
                            </span>
                        </div>

                        <div class="adm-popover-formats" role="radiogroup" aria-label="Format Berkas">
                            {{-- Format 1: Excel (.xlsx) - Rekomendasi --}}
                            <label class="adm-format-card adm-format-selected" id="cardFormatPendaftaranXlsx" onclick="handleAdmExportPendaftaranFormatChange('xlsx')">
                                <div class="adm-format-content">
                                    <input type="radio" name="popover_export_pendaftaran_format" id="radioFormatPendaftaranXlsx" value="xlsx" checked class="adm-format-radio" onchange="handleAdmExportPendaftaranFormatChange('xlsx')">
                                    <div class="adm-format-details">
                                        <div class="adm-format-name-wrap">
                                            <span class="adm-format-name">Microsoft Excel</span>
                                            <span class="badge-format-ext badge-ext-xlsx">.xlsx</span>
                                        </div>
                                        <p class="adm-format-desc">Tabel resmi bergaris rapi &amp; filterable</p>
                                    </div>
                                </div>
                                <div class="adm-format-meta">
                                    <span class="adm-badge-recommend">Rekomendasi</span>
                                </div>
                            </label>

                            {{-- Format 2: CSV Tabular (.csv) --}}
                            <label class="adm-format-card" id="cardFormatPendaftaranCsv" onclick="handleAdmExportPendaftaranFormatChange('csv')">
                                <div class="adm-format-content">
                                    <input type="radio" name="popover_export_pendaftaran_format" id="radioFormatPendaftaranCsv" value="csv" class="adm-format-radio" onchange="handleAdmExportPendaftaranFormatChange('csv')">
                                    <div class="adm-format-details">
                                        <div class="adm-format-name-wrap">
                                            <span class="adm-format-name">CSV Tabular</span>
                                            <span class="badge-format-ext badge-ext-csv">.csv</span>
                                        </div>
                                        <p class="adm-format-desc">Data mentah teks pemisah koma terstandarisasi</p>
                                    </div>
                                </div>
                                <div class="adm-format-meta">
                                    <span class="adm-badge-quick">Cepat</span>
                                </div>
                            </label>

                            {{-- Format 3: Dokumen Resmi PDF (.pdf) --}}
                            <label class="adm-format-card" id="cardFormatPendaftaranPdf" onclick="handleAdmExportPendaftaranFormatChange('pdf')">
                                <div class="adm-format-content">
                                    <input type="radio" name="popover_export_pendaftaran_format" id="radioFormatPendaftaranPdf" value="pdf" class="adm-format-radio" onchange="handleAdmExportPendaftaranFormatChange('pdf')">
                                    <div class="adm-format-details">
                                        <div class="adm-format-name-wrap">
                                            <span class="adm-format-name">Dokumen Resmi (.pdf)</span>
                                            <span class="badge-format-ext badge-ext-pdf">.pdf</span>
                                        </div>
                                        <p class="adm-format-desc">Rekapitulasi cetak ber-kop resmi kedinasan</p>
                                    </div>
                                </div>
                                <div class="adm-format-meta">
                                    <span class="adm-badge-legal">Legal Sah</span>
                                </div>
                            </label>
                        </div>

                        <div class="adm-popover-cta">
                            <button type="button" class="adm-btn-popover-cancel" onclick="toggleAdmExportPopoverPendaftaran(false)">
                                Batal
                            </button>
                            <button type="button" id="btnDoExportDownloadPendaftaran" class="adm-btn-popover-download" onclick="triggerPendaftaranExport()">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                <span id="btnExportDownloadTextPendaftaran">Unduh Excel (.xlsx)</span>
                            </button>
                        </div>
                    </div>
                </div>
            </x-slot:actions>

            <!-- Toolbar Filter -->
            <form method="GET" action="{{ route('admin.laporan.index') }}" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; background: var(--neutral-100, #F8FAFC); padding: 1rem 1.25rem; border-radius: 10px; margin-bottom: 1.25rem; border: 1.5px solid #CBD5E1;">
                <input type="hidden" name="tab" value="pendaftaran">

                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <label style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700);">Bulan Magang</label>
                    <select name="bulan_magang_id" style="height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.8125rem; background: #FFFFFF;">
                        <option value="">Semua Bulan Magang</option>
                        @foreach($bulanMagangList as $bm)
                            <option value="{{ $bm->id }}" {{ request('bulan_magang_id') == $bm->id ? 'selected' : '' }}>
                                {{ $bm->bulan_mulai?->translatedFormat('F Y') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <label style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700);">Status Keputusan</label>
                    <select name="status" style="height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.8125rem; background: #FFFFFF;">
                        <option value="">Semua Status</option>
                        @foreach($statusPendaftaran as $sp)
                            <option value="{{ $sp->value }}" {{ request('status') == $sp->value ? 'selected' : '' }}>
                                {{ $sp->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <label style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700);">Jurusan</label>
                    <select name="jurusan_id" style="height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.8125rem; background: #FFFFFF;">
                        <option value="">Semua Jurusan</option>
                        @foreach($jurusanList as $jur)
                            <option value="{{ $jur->id }}" {{ request('jurusan_id') == $jur->id ? 'selected' : '' }}>
                                {{ $jur->nama_jurusan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <label style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700);">Dari Tanggal</label>
                    <input type="date" name="dari_tanggal" value="{{ request('dari_tanggal') }}" style="height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.5rem; font-size: 0.8125rem; background: #FFFFFF;">
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <label style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700);">Sampai Tanggal</label>
                    <input type="date" name="sampai_tanggal" value="{{ request('sampai_tanggal') }}" style="height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.5rem; font-size: 0.8125rem; background: #FFFFFF;">
                </div>

                <div style="display: flex; align-items: flex-end; gap: 0.5rem; margin-top: 1rem;">
                    <button type="submit" style="height: 38px; padding: 0 1.25rem; background: var(--blue-600); color: #FFFFFF; font-weight: 600; font-size: 0.8125rem; border: none; border-radius: 6px; cursor: pointer;">
                        Terapkan
                    </button>
                    @if(request()->hasAny(['bulan_magang_id', 'status', 'jurusan_id', 'dari_tanggal', 'sampai_tanggal']))
                        <a href="{{ route('admin.laporan.index', ['tab' => 'pendaftaran']) }}" style="font-size: 0.8125rem; color: var(--neutral-600); text-decoration: none; padding: 0.5rem;">Reset</a>
                    @endif
                </div>
            </form>

            <!-- Table Preview (Contained & Clean) -->
            <div class="adm-table-container">
                <div style="overflow-x: auto; width: 100%;">
                    <table class="adm-table">
                        <thead>
                            <tr>
                                <th style="width: 15%; min-width: 130px; text-align: left;">No. Registrasi</th>
                                <th style="width: 26%; min-width: 210px; text-align: left;">Nama & Institusi</th>
                                <th style="width: 23%; min-width: 190px; text-align: left;">Jurusan</th>
                                <th style="width: 15%; min-width: 130px; text-align: left;">Bulan Mulai</th>
                                <th style="width: 10%; min-width: 85px; text-align: center;">Durasi</th>
                                <th style="width: 11%; min-width: 105px; text-align: center;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendaftaranData as $p)
                                @php
                                    $val = $p->status?->value ?? (string)$p->status;
                                    $statusDotType = match($val) {
                                        'diterima' => 'success',
                                        'diajukan' => 'warning',
                                        'waiting_list' => 'info',
                                        'ditolak', 'dibatalkan' => 'danger',
                                        'perlu_perbaikan' => 'warning',
                                        default => 'neutral',
                                    };
                                @endphp
                                <tr>
                                    {{-- No. Registrasi --}}
                                    <td>
                                        <span class="adm-reg-pill">
                                            {{ $p->nomor_pendaftaran }}
                                        </span>
                                    </td>

                                    {{-- Nama & Institusi --}}
                                    <td>
                                        <div class="adm-cell-stack">
                                            <div class="adm-cell-title">
                                                {{ $p->user?->nama ?? 'Calon Peserta' }}
                                            </div>
                                            <div class="adm-cell-sub" style="margin-top: 3px;">
                                                {{ $p->asal_instansi ?? ($p->user?->profil?->institusi ?? '-') }}
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Jurusan --}}
                                    <td>
                                        <div style="font-weight: 600; color: #1E293B; font-size: 0.85rem;">
                                            {{ $p->kuota?->jurusan?->nama_jurusan ?? '-' }}
                                        </div>
                                    </td>

                                    {{-- Bulan Mulai --}}
                                    <td>
                                        <div style="font-weight: 600; color: #0F172A; font-size: 0.85rem;">
                                            {{ $p->bulanMagang?->bulan_mulai?->translatedFormat('F Y') ?? '-' }}
                                        </div>
                                    </td>

                                    {{-- Durasi --}}
                                    <td style="text-align: center;">
                                        <span style="font-weight: 600; color: #475569; font-size: 0.85rem;">
                                            {{ $p->durasi_bulan }} Bulan
                                        </span>
                                    </td>

                                    {{-- Status --}}
                                    <td style="text-align: center;">
                                        <div style="display: flex; justify-content: center; align-items: center;">
                                            <span class="cv-status-dot-badge {{ $statusDotType }}">
                                                <span class="dot"></span>
                                                <span>{{ $p->status?->label() ?? ucfirst($val) }}</span>
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="adm-table-empty">
                                        Tidak ada data pendaftaran yang sesuai dengan filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($pendaftaranData->isNotEmpty())
                    <div class="adm-table-footer">
                        <x-gentelella.pagination-bar 
                            :paginator="$pendaftaranData" 
                            :perPageOptions="[5, 10, 25]" 
                            entity="data rekapitulasi" 
                            badgeText="Rekapitulasi Berkas" 
                        />
                    </div>
                @endif
            </div>
        </x-gentelella.x-panel>
    @endif

    <!-- 4. TAB 2: DATA PENEMPATAN -->
    @if($tab === 'penempatan')
        <x-gentelella.x-panel class="has-table" title="Laporan Data Penempatan Magang">
            <x-slot:actions>
                {{-- Unified Export Popover Trigger & Container (Penempatan) --}}
                <div class="adm-export-dropdown-wrap" style="position: relative;">
                    <button type="button" 
                            id="btnToggleExportPopoverPenempatan" 
                            class="adm-btn-export-main" 
                            aria-expanded="false" 
                            aria-haspopup="menu" 
                            aria-controls="admExportPopoverPenempatan"
                            title="Pilih dan unduh rekap data penempatan (Excel, CSV, PDF)">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        <span>Ekspor Rekap</span>
                        <svg class="adm-export-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>

                    {{-- Floating Dropdown Popover Container (Right-Aligned) --}}
                    <div id="admExportPopoverPenempatan" class="adm-export-popover adm-export-popover-right" style="display: none;" role="menu" aria-label="Menu Pilihan Format Ekspor Penempatan">
                        <div class="adm-popover-pointer adm-pointer-right"></div>
                        <div class="adm-popover-header">
                            <div>
                                <div class="adm-popover-title">Ekspor Rekap Cepat</div>
                                <div class="adm-popover-desc">Data penempatan hasil filter aktif</div>
                            </div>
                            <span class="adm-popover-row-count tabular-nums">
                                {{ number_format($penempatanData->total()) }} Baris
                            </span>
                        </div>

                        <div class="adm-popover-formats" role="radiogroup" aria-label="Format Berkas">
                            {{-- Format 1: Excel (.xlsx) - Rekomendasi --}}
                            <label class="adm-format-card adm-format-selected" id="cardFormatPenempatanXlsx" onclick="handleAdmExportPenempatanFormatChange('xlsx')">
                                <div class="adm-format-content">
                                    <input type="radio" name="popover_export_penempatan_format" id="radioFormatPenempatanXlsx" value="xlsx" checked class="adm-format-radio" onchange="handleAdmExportPenempatanFormatChange('xlsx')">
                                    <div class="adm-format-details">
                                        <div class="adm-format-name-wrap">
                                            <span class="adm-format-name">Microsoft Excel</span>
                                            <span class="badge-format-ext badge-ext-xlsx">.xlsx</span>
                                        </div>
                                        <p class="adm-format-desc">Tabel resmi bergaris rapi &amp; filterable</p>
                                    </div>
                                </div>
                                <div class="adm-format-meta">
                                    <span class="adm-badge-recommend">Rekomendasi</span>
                                </div>
                            </label>

                            {{-- Format 2: CSV Tabular (.csv) --}}
                            <label class="adm-format-card" id="cardFormatPenempatanCsv" onclick="handleAdmExportPenempatanFormatChange('csv')">
                                <div class="adm-format-content">
                                    <input type="radio" name="popover_export_penempatan_format" id="radioFormatPenempatanCsv" value="csv" class="adm-format-radio" onchange="handleAdmExportPenempatanFormatChange('csv')">
                                    <div class="adm-format-details">
                                        <div class="adm-format-name-wrap">
                                            <span class="adm-format-name">CSV Tabular</span>
                                            <span class="badge-format-ext badge-ext-csv">.csv</span>
                                        </div>
                                        <p class="adm-format-desc">Data mentah teks pemisah koma terstandarisasi</p>
                                    </div>
                                </div>
                                <div class="adm-format-meta">
                                    <span class="adm-badge-quick">Cepat</span>
                                </div>
                            </label>

                            {{-- Format 3: Dokumen Resmi PDF (.pdf) --}}
                            <label class="adm-format-card" id="cardFormatPenempatanPdf" onclick="handleAdmExportPenempatanFormatChange('pdf')">
                                <div class="adm-format-content">
                                    <input type="radio" name="popover_export_penempatan_format" id="radioFormatPenempatanPdf" value="pdf" class="adm-format-radio" onchange="handleAdmExportPenempatanFormatChange('pdf')">
                                    <div class="adm-format-details">
                                        <div class="adm-format-name-wrap">
                                            <span class="adm-format-name">Dokumen Resmi (.pdf)</span>
                                            <span class="badge-format-ext badge-ext-pdf">.pdf</span>
                                        </div>
                                        <p class="adm-format-desc">Rekapitulasi cetak ber-kop resmi kedinasan</p>
                                    </div>
                                </div>
                                <div class="adm-format-meta">
                                    <span class="adm-badge-legal">Legal Sah</span>
                                </div>
                            </label>
                        </div>

                        <div class="adm-popover-cta">
                            <button type="button" class="adm-btn-popover-cancel" onclick="toggleAdmExportPopoverPenempatan(false)">
                                Batal
                            </button>
                            <button type="button" id="btnDoExportDownloadPenempatan" class="adm-btn-popover-download" onclick="triggerPenempatanExport()">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                <span id="btnExportDownloadTextPenempatan">Unduh Excel (.xlsx)</span>
                            </button>
                        </div>
                    </div>
                </div>
            </x-slot:actions>

            <!-- Toolbar Filter Penempatan -->
            <form method="GET" action="{{ route('admin.laporan.index') }}" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; background: var(--neutral-100, #F8FAFC); padding: 1rem 1.25rem; border-radius: 10px; margin-bottom: 1.25rem; border: 1.5px solid #CBD5E1;">
                <input type="hidden" name="tab" value="penempatan">

                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <label style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700);">Status Penempatan</label>
                    <select name="status" style="height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.8125rem; background: #FFFFFF;">
                        <option value="">Semua Status</option>
                        @foreach($statusPenempatan as $st)
                            <option value="{{ $st->value }}" {{ request('status') == $st->value ? 'selected' : '' }}>
                                {{ $st->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <label style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700);">Jalur</label>
                    <select name="jalur" style="height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.8125rem; background: #FFFFFF;">
                        <option value="">Semua Jalur</option>
                        <option value="umum" {{ request('jalur') == 'umum' ? 'selected' : '' }}>Jalur Umum (Online)</option>
                        <option value="khusus" {{ request('jalur') == 'khusus' ? 'selected' : '' }}>Jalur Khusus (Slot Internal)</option>
                    </select>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <label style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700);">Jurusan</label>
                    <select name="jurusan_id" style="height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.8125rem; background: #FFFFFF;">
                        <option value="">Semua Jurusan</option>
                        @foreach($jurusanList as $jur)
                            <option value="{{ $jur->id }}" {{ request('jurusan_id') == $jur->id ? 'selected' : '' }}>
                                {{ $jur->nama_jurusan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <label style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700);">Mulai Dari Tanggal</label>
                    <input type="date" name="dari_tanggal" value="{{ request('dari_tanggal') }}" style="height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.5rem; font-size: 0.8125rem; background: #FFFFFF;">
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <label style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700);">Sampai Tanggal</label>
                    <input type="date" name="sampai_tanggal" value="{{ request('sampai_tanggal') }}" style="height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.5rem; font-size: 0.8125rem; background: #FFFFFF;">
                </div>

                <div style="display: flex; align-items: flex-end; gap: 0.5rem; margin-top: 1rem;">
                    <button type="submit" style="height: 38px; padding: 0 1.25rem; background: var(--blue-600); color: #FFFFFF; font-weight: 600; font-size: 0.8125rem; border: none; border-radius: 6px; cursor: pointer;">
                        Terapkan
                    </button>
                    @if(request()->hasAny(['status', 'jalur', 'jurusan_id', 'dari_tanggal', 'sampai_tanggal']))
                        <a href="{{ route('admin.laporan.index', ['tab' => 'penempatan']) }}" style="font-size: 0.8125rem; color: var(--neutral-600); text-decoration: none; padding: 0.5rem;">Reset</a>
                    @endif
                </div>
            </form>

            <div class="adm-table-container">
                <div style="overflow-x: auto; width: 100%;">
                    <table class="adm-table">
                        <thead>
                            <tr>
                                <th style="width: 26%; text-align: left;">Peserta Magang</th>
                                <th style="width: 22%; text-align: left;">Pembimbing Lapangan</th>
                                <th style="width: 18%; text-align: left;">Jalur</th>
                                <th style="width: 20%; text-align: left;">Rentang Waktu Magang</th>
                                <th style="width: 14%; text-align: left;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($penempatanData as $pen)
                                @php
                                    $stVal = $pen->status?->value ?? (string)$pen->status;
                                    $penStatusDot = match($stVal) {
                                        'aktif' => 'success',
                                        'selesai' => 'info',
                                        'mengundurkan_diri' => 'danger',
                                        default => 'neutral',
                                    };
                                @endphp
                                <tr>
                                    {{-- Peserta Magang --}}
                                    <td>
                                        <div class="adm-cell-stack">
                                            <div class="adm-cell-title">
                                                {{ $pen->peserta?->nama ?? 'Peserta Magang' }}
                                            </div>
                                            <div class="adm-cell-sub">
                                                {{ $pen->peserta?->profil?->institusi ?? '-' }}
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Pembimbing --}}
                                    <td>
                                        <div style="font-weight: 600; color: #1E293B; font-size: 0.875rem;">
                                            {{ $pen->pembimbing?->nama ?? '-' }}
                                        </div>
                                        @if($pen->bidang)
                                            <div class="adm-cell-sub">{{ $pen->bidang->nama_bidang }}</div>
                                        @endif
                                    </td>

                                    {{-- Jalur --}}
                                    <td>
                                        <span style="font-weight: 700; font-size: 0.8125rem; color: {{ $pen->jalur?->value === 'khusus' ? '#7E22CE' : '#0641CC' }};">
                                            {{ $pen->jalur?->label() }}
                                        </span>
                                        <small style="color: #64748B; display: block;">({{ $pen->sumber?->label() }})</small>
                                    </td>

                                    {{-- Rentang Waktu --}}
                                    <td>
                                        <div style="font-weight: 600; color: #0F172A; font-size: 0.8125rem;">
                                            {{ $pen->tgl_mulai?->format('d M Y') }} s/d {{ $pen->tgl_selesai?->format('d M Y') }}
                                        </div>
                                    </td>

                                    {{-- Status --}}
                                    <td>
                                        <span class="cv-status-dot-badge {{ $penStatusDot }}">
                                            <span class="dot"></span>
                                            <span>{{ $pen->status?->label() ?? ucfirst($stVal) }}</span>
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="adm-table-empty">
                                        Tidak ada data penempatan yang sesuai dengan filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($penempatanData->isNotEmpty())
                    <div class="adm-table-footer">
                        <x-gentelella.pagination-bar 
                            :paginator="$penempatanData" 
                            :perPageOptions="[5, 10, 25]" 
                            entity="data penempatan" 
                            badgeText="Penempatan Magang" 
                        />
                    </div>
                @endif
            </div>
        </x-gentelella.x-panel>
    @endif

    <!-- END OF TABS -->
</div>
@endsection

@push('scripts')
<script>
    // =========================================================================
    // EXPORT REKAP PENDAFTARAN POPOVER MANAGEMENT
    // =========================================================================
    function toggleAdmExportPopoverPendaftaran(forceOpen = null) {
        const popover = document.getElementById('admExportPopoverPendaftaran');
        const trigger = document.getElementById('btnToggleExportPopoverPendaftaran');
        const wrap = trigger ? trigger.closest('.adm-export-dropdown-wrap') : null;
        if (!popover) return;

        const shouldOpen = forceOpen !== null ? forceOpen : popover.style.display === 'none';
        if (shouldOpen) {
            popover.style.display = 'flex';
            if (trigger) trigger.setAttribute('aria-expanded', 'true');
            if (wrap) wrap.classList.add('is-open');
        } else {
            popover.style.display = 'none';
            if (trigger) trigger.setAttribute('aria-expanded', 'false');
            if (wrap) wrap.classList.remove('is-open');
        }
    }

    function handleAdmExportPendaftaranFormatChange(format) {
        const cards = document.querySelectorAll('#admExportPopoverPendaftaran .adm-format-card');
        cards.forEach(c => c.classList.remove('adm-format-selected'));

        const radio = document.getElementById('radioFormatPendaftaran' + format.charAt(0).toUpperCase() + format.slice(1));
        if (radio) radio.checked = true;

        const card = document.getElementById('cardFormatPendaftaran' + format.charAt(0).toUpperCase() + format.slice(1));
        if (card) card.classList.add('adm-format-selected');

        const btnText = document.getElementById('btnExportDownloadTextPendaftaran');
        if (btnText) {
            if (format === 'xlsx') {
                btnText.textContent = 'Unduh Excel (.xlsx)';
            } else if (format === 'csv') {
                btnText.textContent = 'Unduh CSV (.csv)';
            } else if (format === 'pdf') {
                btnText.textContent = 'Unduh PDF (.pdf)';
            }
        }
    }

    function triggerPendaftaranExport(forcedFormat = null) {
        let format = forcedFormat;
        if (!format) {
            const selectedRadio = document.querySelector('input[name="popover_export_pendaftaran_format"]:checked');
            format = selectedRadio ? selectedRadio.value : 'xlsx';
        }

        const currentUrl = new URL(window.location.href);
        const exportUrl = new URL("{{ route('admin.laporan.pendaftaran') }}", window.location.origin);

        // Salin seluruh filter query string aktif
        currentUrl.searchParams.forEach((val, key) => {
            if (key !== 'page' && key !== 'tab') {
                exportUrl.searchParams.set(key, val);
            }
        });

        exportUrl.searchParams.set('format', format);

        // Tutup popover
        toggleAdmExportPopoverPendaftaran(false);

        // Jalankan download file
        window.location.href = exportUrl.toString();
    }

    // =========================================================================
    // EXPORT REKAP PENEMPATAN POPOVER MANAGEMENT
    // =========================================================================
    function toggleAdmExportPopoverPenempatan(forceOpen = null) {
        const popover = document.getElementById('admExportPopoverPenempatan');
        const trigger = document.getElementById('btnToggleExportPopoverPenempatan');
        const wrap = trigger ? trigger.closest('.adm-export-dropdown-wrap') : null;
        if (!popover) return;

        const shouldOpen = forceOpen !== null ? forceOpen : popover.style.display === 'none';
        if (shouldOpen) {
            popover.style.display = 'flex';
            if (trigger) trigger.setAttribute('aria-expanded', 'true');
            if (wrap) wrap.classList.add('is-open');
        } else {
            popover.style.display = 'none';
            if (trigger) trigger.setAttribute('aria-expanded', 'false');
            if (wrap) wrap.classList.remove('is-open');
        }
    }

    function handleAdmExportPenempatanFormatChange(format) {
        const cards = document.querySelectorAll('#admExportPopoverPenempatan .adm-format-card');
        cards.forEach(c => c.classList.remove('adm-format-selected'));

        const radio = document.getElementById('radioFormatPenempatan' + format.charAt(0).toUpperCase() + format.slice(1));
        if (radio) radio.checked = true;

        const card = document.getElementById('cardFormatPenempatan' + format.charAt(0).toUpperCase() + format.slice(1));
        if (card) card.classList.add('adm-format-selected');

        const btnText = document.getElementById('btnExportDownloadTextPenempatan');
        if (btnText) {
            if (format === 'xlsx') {
                btnText.textContent = 'Unduh Excel (.xlsx)';
            } else if (format === 'csv') {
                btnText.textContent = 'Unduh CSV (.csv)';
            } else if (format === 'pdf') {
                btnText.textContent = 'Unduh PDF (.pdf)';
            }
        }
    }

    function triggerPenempatanExport(forcedFormat = null) {
        let format = forcedFormat;
        if (!format) {
            const selectedRadio = document.querySelector('input[name="popover_export_penempatan_format"]:checked');
            format = selectedRadio ? selectedRadio.value : 'xlsx';
        }

        const currentUrl = new URL(window.location.href);
        const exportUrl = new URL("{{ route('admin.laporan.penempatan') }}", window.location.origin);

        // Salin seluruh filter query string aktif
        currentUrl.searchParams.forEach((val, key) => {
            if (key !== 'page' && key !== 'tab') {
                exportUrl.searchParams.set(key, val);
            }
        });

        exportUrl.searchParams.set('format', format);

        // Tutup popover
        toggleAdmExportPopoverPenempatan(false);

        // Jalankan download file
        window.location.href = exportUrl.toString();
    }

    // Event listener click outside & Escape key
    document.addEventListener('DOMContentLoaded', function() {
        // Pendaftaran Popover
        const triggerPdt = document.getElementById('btnToggleExportPopoverPendaftaran');
        const popoverPdt = document.getElementById('admExportPopoverPendaftaran');

        if (triggerPdt) {
            triggerPdt.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleAdmExportPopoverPendaftaran();
            });
        }

        if (popoverPdt) {
            popoverPdt.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }

        // Penempatan Popover
        const triggerPen = document.getElementById('btnToggleExportPopoverPenempatan');
        const popoverPen = document.getElementById('admExportPopoverPenempatan');

        if (triggerPen) {
            triggerPen.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleAdmExportPopoverPenempatan();
            });
        }

        if (popoverPen) {
            popoverPen.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }

        document.addEventListener('click', function(e) {
            if (popoverPdt && popoverPdt.style.display !== 'none') {
                if (!popoverPdt.contains(e.target) && triggerPdt && !triggerPdt.contains(e.target)) {
                    toggleAdmExportPopoverPendaftaran(false);
                }
            }
            if (popoverPen && popoverPen.style.display !== 'none') {
                if (!popoverPen.contains(e.target) && triggerPen && !triggerPen.contains(e.target)) {
                    toggleAdmExportPopoverPenempatan(false);
                }
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (popoverPdt && popoverPdt.style.display !== 'none') {
                    toggleAdmExportPopoverPendaftaran(false);
                }
                if (popoverPen && popoverPen.style.display !== 'none') {
                    toggleAdmExportPopoverPenempatan(false);
                }
            }
        });
    });
</script>
@endpush
