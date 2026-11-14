@push('styles')
    @vite(['resources/css/pages/admin/pendaftaran-index.css'])
@endpush

@extends('layouts.dashboard')

@section('title', 'Verifikasi Berkas Pendaftaran Peserta - Admin MagKom Sleman')

@section('topbar_title', 'Verifikasi Berkas Pendaftaran Peserta')

@section('content')
<div class="adm-verifikasi-wrapper">


    {{-- 0. SLEMAN NAVY HERO BANNER (Khas MagKom) --}}
    <div class="adm-hero-banner">
        <div class="adm-hero-content">
            <div class="adm-hero-text">
                <h1 class="adm-hero-title">Daftar Pendaftaran Peserta Magang</h1>
                <div class="adm-hero-subtitle">
                    <span>Verifikasi Berkas Pendaftaran Peserta</span>
                    <span class="sep">&bull;</span>
                    <span>Dinas Komunikasi dan Informatika Kabupaten Sleman</span>
                </div>
            </div>
            <div class="adm-hero-actions">
                {{-- Unified Export Popover Trigger & Container --}}
                <div class="adm-export-dropdown-wrap">
                    <button type="button" 
                            id="btnToggleExportPopoverPendaftaran" 
                            class="adm-btn-export-main adm-hero-btn-secondary" 
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
                        <svg id="admExportCaretSvgPendaftaran" class="adm-export-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
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
                                {{ number_format($pendaftaranList->total()) }} Baris
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
            </div>
        </div>
    </div>

    {{-- 1. TOP 4 KPI CARDS ROW (COCKPIT PARITY) --}}
    <div class="row tile_count adm-tile-count-grid">
        <x-gentelella.tile-stat 
            title="Total Pengajuan" 
            :value="number_format($queueStats['total'] ?? $pendaftaranList->total())" 
            icon="file-text"
            color="blue"
            trendText="Total"
            trendDirection="up"
            subText="Semua riwayat pendaftaran" 
            :sparkBars="[35, 50, 45, 60, 65, 80, 95]"
        />
        <x-gentelella.tile-stat 
            title="Menunggu Ditinjau" 
            :value="number_format($queueStats['diajukan'] ?? 0)" 
            icon="clipboard-check"
            color="yellow"
            trendType="warning"
            trendText="Antrean"
            trendDirection="up"
            subText="Prioritas antrean FIFO" 
            :sparkBars="[70, 75, 65, 80, 85, 60, 55]"
        />
        <x-gentelella.tile-stat 
            title="Pendaftaran Diterima" 
            :value="number_format($queueStats['diterima'] ?? 0)" 
            icon="check-circle"
            color="green"
            trendType="success"
            trendText="Diterima"
            trendDirection="up"
            subText="Telah lolos verifikasi berkas" 
            :sparkBars="[25, 35, 45, 50, 65, 80, 95]"
        />
        <x-gentelella.tile-stat 
            title="Tertahan > 3 Hari" 
            :value="number_format($queueStats['sla_warning'] ?? 0)" 
            icon="clock"
            color="red"
            trendType="danger"
            trendText="SLA Target"
            trendDirection="down"
            subText="Batas SLA verifikasi target" 
            :sparkBars="[40, 30, 45, 25, 35, 20, 15]"
        />
    </div>

    {{-- NAVIGASI TAB UTAMA: Kategori Pendaftaran (Berdiri Sendiri di Luar Frame Kartu) --}}
    @php
        $currentStatus = request('status', '');
        $currentTipe = request('tipe', '');
        $makeFilterUrl = function($statusVal = null, $tipeVal = null) {
            $params = request()->except('page');
            if ($statusVal !== null) {
                if ($statusVal === '') {
                    unset($params['status']);
                } else {
                    $params['status'] = $statusVal;
                }
            }
            if ($tipeVal !== null) {
                if ($tipeVal === '') {
                    unset($params['tipe']);
                } else {
                    $params['tipe'] = $tipeVal;
                }
            }
            return route('admin.pendaftaran.index', $params);
        };
        $hasActiveSearch = request()->filled('q') 
            || request()->filled('bulan_magang_id') 
            || request()->filled('jurusan_id') 
            || (request()->filled('sort') && request('sort') !== 'terbaru');
    @endphp

    <div style="margin-bottom: 1.15rem;">
        <div class="adm-category-nav-bar" role="tablist" aria-label="Kategori Pendaftaran Peserta" style="margin-bottom: 0;">
            <a href="{{ $makeFilterUrl(null, '') }}" class="adm-category-nav-item {{ $currentTipe === '' ? 'active' : '' }}" role="tab" aria-selected="{{ $currentTipe === '' ? 'true' : 'false' }}" title="Tampilkan semua kategori pendaftaran">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
                <span>Semua Kategori</span>
                <span class="adm-category-nav-badge">{{ $queueStats['total'] ?? $pendaftaranList->total() }}</span>
            </a>
            <a href="{{ $makeFilterUrl(null, 'individu') }}" class="adm-category-nav-item {{ ($currentTipe === 'individu' || $currentTipe === 'mandiri') ? 'active-individu' : '' }}" role="tab" aria-selected="{{ ($currentTipe === 'individu' || $currentTipe === 'mandiri') ? 'true' : 'false' }}" title="Hanya pendaftaran individu">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <span>Individu</span>
                <span class="adm-category-nav-badge">{{ $queueStats['total_individu'] ?? 0 }}</span>
            </a>
            <a href="{{ $makeFilterUrl(null, 'kelompok') }}" class="adm-category-nav-item {{ $currentTipe === 'kelompok' ? 'active-kelompok' : '' }}" role="tab" aria-selected="{{ $currentTipe === 'kelompok' ? 'true' : 'false' }}" title="Hanya pendaftaran kelompok">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span>Kelompok</span>
                <span class="adm-category-nav-badge">{{ $queueStats['total_kelompok'] ?? 0 }}</span>
            </a>
        </div>
    </div>

    {{-- 2. STANDALONE FILTER CARD (TERPISAH SEPERTI DI PENEMPATAN PESERTA) --}}
    <div class="adm-filter-card">
        {{-- Form Pencarian & Filter Grid Lapang (42px Unified Height - 4 Kolom) --}}
        <form method="GET" action="{{ route('admin.pendaftaran.index') }}" class="adm-filter-grid">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            @if(request('tipe'))
                <input type="hidden" name="tipe" value="{{ request('tipe') }}">
            @endif

            <div class="adm-field-group adm-field-search">
                <label for="search-input">Pencarian Calon Peserta</label>
                <div class="adm-search-input-wrap">
                    <svg class="adm-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" id="search-input" name="q" value="{{ request('q') }}" placeholder="Cari nama..." class="adm-input-search">
                </div>
            </div>

            <div class="adm-field-group">
                <label for="select-bulan">Bulan Magang</label>
                <select name="bulan_magang_id" id="select-bulan" class="adm-select">
                    <option value="">Semua Bulan</option>
                    @foreach($bulanList as $bulan)
                        <option value="{{ $bulan->id }}" {{ request('bulan_magang_id') == $bulan->id ? 'selected' : '' }}>
                            {{ $bulan->bulan_mulai?->translatedFormat('F Y') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="adm-field-group">
                <label for="select-jurusan">Jurusan</label>
                <select name="jurusan_id" id="select-jurusan" class="adm-select">
                    <option value="">Semua Jurusan</option>
                    @foreach($jurusanList as $jur)
                        <option value="{{ $jur->id }}" {{ request('jurusan_id') == $jur->id ? 'selected' : '' }}>
                            {{ $jur->nama_jurusan }} ({{ strtoupper($jur->kategori) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="adm-field-group">
                <label for="select-sort">Urutan Data</label>
                <select name="sort" id="select-sort" class="adm-select">
                    <option value="terbaru" {{ request('sort', 'terbaru') === 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                    <option value="terlama" {{ request('sort') === 'terlama' ? 'selected' : '' }}>Terlama</option>
                </select>
            </div>

            <div class="adm-filter-actions">
                <button type="submit" class="adm-btn-filter-submit">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    <span>Terapkan</span>
                </button>
                @if($hasActiveSearch)
                    <a href="{{ route('admin.pendaftaran.index', array_filter(['status' => request('status'), 'tipe' => request('tipe')])) }}" class="adm-btn-filter-reset" title="Kembalikan pencarian ke kondisi awal">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- 3. MAIN X-PANEL CARD (TABEL CALON PESERTA) --}}
    <x-gentelella.x-panel :toolbox="false">

        {{-- 3A. TABEL DATA CALON PESERTA TERPADU --}}
        <div class="adm-table-container">
            {{-- Toolbar Status Alur Kerja (Tepat Menempel di Atas Tabel) --}}
            <div class="adm-table-meta-bar">
                {{-- Sisi Kiri: Switcher Status Alur Kerja (Workflow State) --}}
                <div class="adm-status-track">
                    <a href="{{ $makeFilterUrl('', null) }}" class="adm-status-tab {{ $currentStatus === '' ? 'active' : '' }}">
                        <span>Semua Status</span>
                        <span class="adm-status-counter">{{ $queueStats['total'] ?? $pendaftaranList->total() }}</span>
                    </a>
                    <a href="{{ $makeFilterUrl('diajukan', null) }}" class="adm-status-tab {{ $currentStatus === 'diajukan' ? 'active' : '' }}">
                        <span>Diajukan</span>
                        <span class="adm-status-counter">{{ $queueStats['diajukan'] ?? 0 }}</span>
                    </a>
                    <a href="{{ $makeFilterUrl('diterima', null) }}" class="adm-status-tab {{ $currentStatus === 'diterima' ? 'active' : '' }}">
                        <span>Diterima</span>
                        <span class="adm-status-counter">{{ $queueStats['diterima'] ?? 0 }}</span>
                    </a>
                    <a href="{{ $makeFilterUrl('ditolak', null) }}" class="adm-status-tab {{ $currentStatus === 'ditolak' ? 'active' : '' }}">
                        <span>Ditolak</span>
                        <span class="adm-status-counter">{{ $queueStats['ditolak'] ?? 0 }}</span>
                    </a>
                </div>
            </div>

            @if($pendaftaranList->isEmpty())
                <div style="padding: 3.5rem 1.5rem; text-align: center; color: #64748B;">
                    <div style="width: 52px; height: 52px; background: #EFF6FF; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto; color: #0641CC;">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </div>
                    <h3 style="font-size: 1.1rem; font-weight: 800; color: #0F172A; margin: 0 0 1.25rem 0;">
                        Tidak Ada Berkas
                    </h3>
                    <a href="{{ route('admin.pendaftaran.index') }}" class="adm-btn-tinjau" style="display: inline-flex;">
                        Reset Semua Filter
                    </a>
                </div>
            @else
                <div style="overflow-x: auto; width: 100%;">
                    <table class="adm-table">
                        <thead>
                            <tr>
                                <th style="width: 14%; min-width: 130px; text-align: left;">No. Registrasi</th>
                                <th style="width: 25%; min-width: 210px; text-align: left;">Calon Peserta</th>
                                <th style="width: 25%; min-width: 210px; text-align: left;">Institusi & Jurusan</th>
                                <th style="width: 16%; min-width: 130px; text-align: left;">Bulan Magang</th>
                                <th style="width: 12%; min-width: 120px; text-align: center;">Status</th>
                                <th style="width: 8%; min-width: 90px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendaftaranList as $item)
                                @php
                                    $val = $item->status->value ?? (string)$item->status;
                                    $statusDotType = match($val) {
                                        'diterima' => 'success',
                                        'diajukan' => 'warning',
                                        'waiting_list' => 'info',
                                        'ditolak' => 'danger',
                                        'perlu_perbaikan' => 'warning',
                                        default => 'neutral',
                                    };

                                    // Dynamic 2-letter uppercase initials with rotating palette
                                    $userName = trim($item->user?->nama ?? 'Peserta');
                                    $nameWords = preg_split('/\s+/', $userName);
                                    if (count($nameWords) >= 2) {
                                        $initials = mb_substr($nameWords[0], 0, 1) . mb_substr($nameWords[1], 0, 1);
                                    } else {
                                        $initials = mb_substr($userName, 0, min(2, mb_strlen($userName)));
                                    }
                                    $initials = strtoupper($initials);
                                    $paletteIdx = (($item->id ?? 1) % 5) + 1;
                                @endphp
                                <tr>
                                    {{-- No. Registrasi --}}
                                    <td>
                                        <span class="adm-reg-badge">
                                            {{ $item->nomor_pendaftaran }}
                                        </span>
                                    </td>

                                    {{-- Calon Peserta --}}
                                    <td>
                                        @php
                                            $isSmk = ($item->user?->profil?->jenjang?->value ?? (string)($item->user?->profil?->jenjang ?? '')) === 'smk'
                                                || ($item->kuota?->jurusan?->kategori ?? '') === 'smk';
                                            $idLabel = $isSmk ? 'NISN' : 'NIM';
                                        @endphp
                                        <div class="adm-user-cell" style="display: flex; align-items: center; gap: 0.85rem;">
                                            <div class="cv-avatar-sm cv-avatar-palette-{{ $paletteIdx }}" style="width: 38px; height: 38px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.825rem; flex-shrink: 0; box-shadow: 0 1px 2px rgba(0,0,0,0.06);">
                                                {{ $initials }}
                                            </div>
                                            <div>
                                                <div style="font-weight: 700; color: #0F172A; font-size: 0.875rem;">
                                                    {{ $item->user?->nama ?? 'Calon Peserta' }}
                                                </div>
                                                @if($item->tipe === 'kelompok' || $item->kelompokMagang)
                                                    <div style="margin-top: 4px;">
                                                        <span class="adm-badge-kelompok" title="{{ $item->kelompokMagang?->nama_kelompok ? 'Tim: ' . $item->kelompokMagang->nama_kelompok : 'Kelompok' }}">
                                                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                                            Kelompok
                                                            @if($item->kelompokMagang)
                                                                <span class="sr-only">Tim: {{ $item->kelompokMagang->nama_kelompok }}</span>
                                                            @endif
                                                        </span>
                                                    </div>
                                                @else
                                                    <div style="margin-top: 4px;">
                                                        <span class="adm-badge-individu">
                                                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                                            Individu
                                                        </span>
                                                    </div>
                                                @endif
                                                <span class="sr-only">{{ $item->user?->email }} {{ $item->user?->profil?->nim_nisn }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Institusi & Jurusan --}}
                                    <td>
                                        <div style="font-weight: 700; color: #1E293B; font-size: 0.875rem;">{{ $item->user?->profil?->institusi ?? '-' }}</div>
                                        <div style="color: #64748B; font-size: 0.8125rem; margin-top: 3px;">{{ $item->kuota?->jurusan?->nama_jurusan ?? ($item->user?->profil?->jurusan ?? '-') }}</div>
                                    </td>

                                    {{-- Periode --}}
                                    <td>
                                        <div style="font-weight: 700; color: #0F172A; font-size: 0.875rem;">
                                            {{ $item->bulanMagang?->bulan_mulai?->translatedFormat('F Y') }}
                                        </div>
                                        <div style="color: #64748B; font-size: 0.8125rem; margin-top: 3px;">Durasi: {{ $item->durasi_bulan }} Bulan</div>
                                    </td>

                                    {{-- Status Permohonan --}}
                                    <td>
                                        <div style="display: flex; align-items: center; justify-content: center;">
                                            <span class="cv-status-dot-badge {{ $statusDotType }}">
                                                <span class="dot"></span>
                                                <span>{{ $item->status->label() }}</span>
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Aksi --}}
                                    <td style="text-align: center;">
                                        <a href="{{ route('admin.pendaftaran.show', $item) }}" class="adm-btn-tinjau">
                                            <span>Tinjau</span>
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                            <span class="sr-only" aria-hidden="true" style="display: none;">Tinjau Berkas</span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="adm-table-footer">
                    <x-gentelella.pagination-bar 
                        :paginator="$pendaftaranList" 
                        :perPageOptions="[5, 10, 25]" 
                        entity="berkas pendaftaran" 
                        badgeText="Pendaftaran Terverifikasi" 
                    />
                </div>
            @endif
        </div>

    </x-gentelella.x-panel>

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
            if (key !== 'page') {
                exportUrl.searchParams.set(key, val);
            }
        });

        exportUrl.searchParams.set('format', format);

        // Tutup popover
        toggleAdmExportPopoverPendaftaran(false);

        // Jalankan download file
        window.location.href = exportUrl.toString();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const trigger = document.getElementById('btnToggleExportPopoverPendaftaran');
        const popover = document.getElementById('admExportPopoverPendaftaran');

        if (trigger) {
            trigger.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleAdmExportPopoverPendaftaran();
            });
        }

        if (popover) {
            popover.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }

        document.addEventListener('click', function(e) {
            if (popover && popover.style.display !== 'none') {
                if (!popover.contains(e.target) && trigger && !trigger.contains(e.target)) {
                    toggleAdmExportPopoverPendaftaran(false);
                }
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && popover && popover.style.display !== 'none') {
                toggleAdmExportPopoverPendaftaran(false);
            }
        });
    });
</script>
@endpush
