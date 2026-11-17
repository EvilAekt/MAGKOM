@push('styles')
    @vite(['resources/css/pages/admin/pendaftaran-index.css'])
@endpush

@extends('layouts.dashboard')

@section('title', __('Verifikasi Berkas Pendaftaran Peserta - Admin MagKom Sleman'))

@section('topbar_title', __('Verifikasi Berkas Pendaftaran Peserta'))

@section('content')
<div class="adm-verifikasi-wrapper">


    {{-- 0. SLEMAN NAVY HERO BANNER (Khas MagKom) --}}
    <div class="adm-hero-banner">
        <div class="adm-hero-content">
            <div class="adm-hero-text">
                <h1 class="adm-hero-title">{{ __('Daftar Pendaftaran Peserta Magang') }}</h1>
                <div class="adm-hero-subtitle">
                    <span>{{ __('Verifikasi Berkas Pendaftaran Peserta') }}</span>
                    <span class="sep">&bull;</span>
                    <span>{{ __('Dinas Komunikasi dan Informatika Kabupaten Sleman') }}</span>
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
                            title="{{ __('Pilih dan unduh rekap data pendaftaran (Excel, CSV, PDF)') }}">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        <span>{{ __('Ekspor Rekap') }}</span>
                        <svg id="admExportCaretSvgPendaftaran" class="adm-export-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>

                    {{-- Floating Dropdown Popover Container (Right-Aligned) --}}
                    <div id="admExportPopoverPendaftaran" class="adm-export-popover adm-export-popover-right" style="display: none;" role="menu" aria-label="{{ __('Menu Pilihan Format Ekspor Pendaftaran') }}">
                        <div class="adm-popover-pointer adm-pointer-right"></div>
                        <div class="adm-popover-header">
                            <div>
                                <div class="adm-popover-title">{{ __('Ekspor Rekap Cepat') }}</div>
                                <div class="adm-popover-desc">{{ __('Data pendaftaran hasil filter aktif') }}</div>
                            </div>
                            <span class="adm-popover-row-count tabular-nums">
                                {{ number_format($pendaftaranList->total()) }} {{ __('Baris') }}
                            </span>
                        </div>

                        <div class="adm-popover-formats" role="radiogroup" aria-label="{{ __('Format Berkas') }}">
                            {{-- Format 1: Excel (.xlsx) - Rekomendasi --}}
                            <label class="adm-format-card adm-format-selected" id="cardFormatPendaftaranXlsx" data-action="select-pendaftaran-export-format" data-format="xlsx">
                                <div class="adm-format-content">
                                    <input type="radio" name="popover_export_pendaftaran_format" id="radioFormatPendaftaranXlsx" value="xlsx" checked class="adm-format-radio" >
                                    <div class="adm-format-details">
                                        <div class="adm-format-name-wrap">
                                            <span class="adm-format-name">{{ __('Microsoft Excel') }}</span>
                                            <span class="badge-format-ext badge-ext-xlsx">{{ __('.xlsx') }}</span>
                                        </div>
                                        <p class="adm-format-desc">{{ __('Tabel resmi bergaris rapi & filterable') }}</p>
                                    </div>
                                </div>
                                <div class="adm-format-meta">
                                    <span class="adm-badge-recommend">{{ __('Rekomendasi') }}</span>
                                </div>
                            </label>

                            {{-- Format 2: CSV Tabular (.csv) --}}
                            <label class="adm-format-card" id="cardFormatPendaftaranCsv" data-action="select-pendaftaran-export-format" data-format="csv">
                                <div class="adm-format-content">
                                    <input type="radio" name="popover_export_pendaftaran_format" id="radioFormatPendaftaranCsv" value="csv" class="adm-format-radio" >
                                    <div class="adm-format-details">
                                        <div class="adm-format-name-wrap">
                                            <span class="adm-format-name">{{ __('CSV Tabular') }}</span>
                                            <span class="badge-format-ext badge-ext-csv">{{ __('.csv') }}</span>
                                        </div>
                                        <p class="adm-format-desc">{{ __('Data mentah teks pemisah koma terstandarisasi') }}</p>
                                    </div>
                                </div>
                                <div class="adm-format-meta">
                                    <span class="adm-badge-quick">{{ __('Cepat') }}</span>
                                </div>
                            </label>

                            {{-- Format 3: Dokumen Resmi PDF (.pdf) --}}
                            <label class="adm-format-card" id="cardFormatPendaftaranPdf" data-action="select-pendaftaran-export-format" data-format="pdf">
                                <div class="adm-format-content">
                                    <input type="radio" name="popover_export_pendaftaran_format" id="radioFormatPendaftaranPdf" value="pdf" class="adm-format-radio" >
                                    <div class="adm-format-details">
                                        <div class="adm-format-name-wrap">
                                            <span class="adm-format-name">{{ __('Dokumen Resmi (.pdf)') }}</span>
                                            <span class="badge-format-ext badge-ext-pdf">{{ __('.pdf') }}</span>
                                        </div>
                                        <p class="adm-format-desc">{{ __('Rekapitulasi cetak ber-kop resmi kedinasan') }}</p>
                                    </div>
                                </div>
                                <div class="adm-format-meta">
                                    <span class="adm-badge-legal">{{ __('Legal Sah') }}</span>
                                </div>
                            </label>
                        </div>

                        <div class="adm-popover-cta">
                            <button type="button" class="adm-btn-popover-cancel" data-action="cancel-pendaftaran-export">
                                {{ __('Batal') }}
                            </button>
                            <button type="button" id="btnDoExportDownloadPendaftaran" class="adm-btn-popover-download" data-action="do-pendaftaran-export">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                <span id="btnExportDownloadTextPendaftaran">{{ __('Unduh Excel (.xlsx)') }}</span>
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
            title="{{ __('Total Pengajuan') }}" 
            :value="number_format($queueStats['total'] ?? $pendaftaranList->total())" 
            icon="file-text"
            color="blue"
            trendText="{{ __('Total') }}"
            trendDirection="up"
            subText="{{ __('Semua riwayat pendaftaran') }}" 
            :sparkBars="[35, 50, 45, 60, 65, 80, 95]"
        />
        <x-gentelella.tile-stat 
            title="{{ __('Menunggu Ditinjau') }}" 
            :value="number_format($queueStats['diajukan'] ?? 0)" 
            icon="clipboard-check"
            color="yellow"
            trendType="warning"
            trendText="{{ __('Antrean') }}"
            trendDirection="up"
            subText="{{ __('Prioritas antrean FIFO') }}" 
            :sparkBars="[70, 75, 65, 80, 85, 60, 55]"
        />
        <x-gentelella.tile-stat 
            title="{{ __('Pendaftaran Diterima') }}" 
            :value="number_format($queueStats['diterima'] ?? 0)" 
            icon="check-circle"
            color="green"
            trendType="success"
            trendText="{{ __('Diterima') }}"
            trendDirection="up"
            subText="{{ __('Telah lolos verifikasi berkas') }}" 
            :sparkBars="[25, 35, 45, 50, 65, 80, 95]"
        />
        <x-gentelella.tile-stat 
            title="{{ __('Tertahan > 3 Hari') }}" 
            :value="number_format($queueStats['sla_warning'] ?? 0)" 
            icon="clock"
            color="red"
            trendType="danger"
            trendText="{{ __('SLA Target') }}"
            trendDirection="down"
            subText="{{ __('Batas SLA verifikasi target') }}" 
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
        <div class="adm-category-nav-bar" role="tablist" aria-label="{{ __('Kategori Pendaftaran Peserta') }}" style="margin-bottom: 0;">
            <a href="{{ $makeFilterUrl(null, '') }}" class="adm-category-nav-item {{ $currentTipe === '' ? 'active' : '' }}" role="tab" aria-selected="{{ $currentTipe === '' ? 'true' : 'false' }}" title="{{ __('Tampilkan semua kategori pendaftaran') }}">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
                <span>{{ __('Semua Kategori') }}</span>
                <span class="adm-category-nav-badge">{{ $queueStats['total'] ?? $pendaftaranList->total() }}</span>
            </a>
            <a href="{{ $makeFilterUrl(null, 'individu') }}" class="adm-category-nav-item {{ ($currentTipe === 'individu' || $currentTipe === 'mandiri') ? 'active-individu' : '' }}" role="tab" aria-selected="{{ ($currentTipe === 'individu' || $currentTipe === 'mandiri') ? 'true' : 'false' }}" title="{{ __('Hanya pendaftaran individu') }}">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <span>{{ __('Individu') }}</span>
                <span class="adm-category-nav-badge">{{ $queueStats['total_individu'] ?? 0 }}</span>
            </a>
            <a href="{{ $makeFilterUrl(null, 'kelompok') }}" class="adm-category-nav-item {{ $currentTipe === 'kelompok' ? 'active-kelompok' : '' }}" role="tab" aria-selected="{{ $currentTipe === 'kelompok' ? 'true' : 'false' }}" title="{{ __('Hanya pendaftaran kelompok') }}">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span>{{ __('Kelompok') }}</span>
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
                <label for="search-input">{{ __('Pencarian Calon Peserta') }}</label>
                <div class="adm-search-input-wrap">
                    <svg class="adm-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" id="search-input" name="q" value="{{ request('q') }}" placeholder="{{ __('Cari nama...') }}" class="adm-input-search">
                </div>
            </div>

            <div class="adm-field-group">
                <label for="select-bulan">{{ __('Bulan Magang') }}</label>
                <select name="bulan_magang_id" id="select-bulan" class="adm-select">
                    <option value="">{{ __('Semua Bulan') }}</option>
                    @foreach($bulanList as $bulan)
                        <option value="{{ $bulan->id }}" {{ request('bulan_magang_id') == $bulan->id ? 'selected' : '' }}>
                            {{ $bulan->bulan_mulai?->translatedFormat('F Y') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="adm-field-group">
                <label for="select-jurusan">{{ __('Jurusan') }}</label>
                <select name="jurusan_id" id="select-jurusan" class="adm-select">
                    <option value="">{{ __('Semua Jurusan') }}</option>
                    @foreach($jurusanList as $jur)
                        <option value="{{ $jur->id }}" {{ request('jurusan_id') == $jur->id ? 'selected' : '' }}>
                            {{ $jur->nama_jurusan }} ({{ strtoupper($jur->kategori) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="adm-field-group">
                <label for="select-sort">{{ __('Urutan Data') }}</label>
                <select name="sort" id="select-sort" class="adm-select">
                    <option value="terbaru" {{ request('sort', 'terbaru') === 'terbaru' ? 'selected' : '' }}>{{ __('Terbaru') }}</option>
                    <option value="terlama" {{ request('sort') === 'terlama' ? 'selected' : '' }}>{{ __('Terlama') }}</option>
                </select>
            </div>

            <div class="adm-filter-actions">
                <button type="submit" class="adm-btn-filter-submit">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    <span>{{ __('Terapkan') }}</span>
                </button>
                @if($hasActiveSearch)
                    <a href="{{ route('admin.pendaftaran.index', array_filter(['status' => request('status'), 'tipe' => request('tipe')])) }}" class="adm-btn-filter-reset" title="{{ __('Kembalikan pencarian ke kondisi awal') }}">
                        {{ __('Reset') }}
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
                        <span>{{ __('Semua Status') }}</span>
                        <span class="adm-status-counter">{{ $queueStats['total'] ?? $pendaftaranList->total() }}</span>
                    </a>
                    <a href="{{ $makeFilterUrl('diajukan', null) }}" class="adm-status-tab {{ $currentStatus === 'diajukan' ? 'active' : '' }}">
                        <span>{{ __('Diajukan') }}</span>
                        <span class="adm-status-counter">{{ $queueStats['diajukan'] ?? 0 }}</span>
                    </a>
                    <a href="{{ $makeFilterUrl('diterima', null) }}" class="adm-status-tab {{ $currentStatus === 'diterima' ? 'active' : '' }}">
                        <span>{{ __('Diterima') }}</span>
                        <span class="adm-status-counter">{{ $queueStats['diterima'] ?? 0 }}</span>
                    </a>
                    <a href="{{ $makeFilterUrl('ditolak', null) }}" class="adm-status-tab {{ $currentStatus === 'ditolak' ? 'active' : '' }}">
                        <span>{{ __('Ditolak') }}</span>
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
                        {{ __('Tidak Ada Berkas') }}
                    </h3>
                    <a href="{{ route('admin.pendaftaran.index') }}" class="adm-btn-tinjau" style="display: inline-flex;">
                        {{ __('Reset Semua Filter') }}
                    </a>
                </div>
            @else
                <div style="overflow-x: auto; width: 100%;">
                    <table class="adm-table">
                        <thead>
                            <tr>
                                <th style="width: 14%; min-width: 130px; text-align: left;">{{ __('No. Registrasi') }}</th>
                                <th style="width: 25%; min-width: 210px; text-align: left;">{{ __('Calon Peserta') }}</th>
                                <th style="width: 25%; min-width: 210px; text-align: left;">{{ __('Institusi & Jurusan') }}</th>
                                <th style="width: 16%; min-width: 130px; text-align: left;">{{ __('Bulan Magang') }}</th>
                                <th style="width: 12%; min-width: 120px; text-align: center;">{{ __('Status') }}</th>
                                <th style="width: 8%; min-width: 90px; text-align: center;">{{ __('Aksi') }}</th>
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
                                    $userName = trim($item->user?->nama ?? __('Peserta'));
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
                                                    {{ $item->user?->nama ?? __('Calon Peserta') }}
                                                </div>
                                                @if($item->tipe === 'kelompok' || $item->kelompokMagang)
                                                    <div style="margin-top: 4px;">
                                                        <span class="adm-badge-kelompok" title="{{ $item->kelompokMagang?->nama_kelompok ? __('Tim') . ': ' . $item->kelompokMagang->nama_kelompok : __('Kelompok') }}">
                                                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                                            {{ __('Kelompok') }}
                                                            @if($item->kelompokMagang)
                                                                <span class="sr-only">{{ __('Tim') }}: {{ $item->kelompokMagang->nama_kelompok }}</span>
                                                            @endif
                                                        </span>
                                                    </div>
                                                @else
                                                    <div style="margin-top: 4px;">
                                                        <span class="adm-badge-individu">
                                                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                                            {{ __('Individu') }}
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
                                        <div style="color: #64748B; font-size: 0.8125rem; margin-top: 3px;">{{ __('Durasi') }}: {{ $item->durasi_bulan }} {{ __('Bulan') }}</div>
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
                                            <span>{{ __('Tinjau') }}</span>
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                            <span class="sr-only" aria-hidden="true" style="display: none;">{{ __('Tinjau Berkas') }}</span>
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
                        entity="{{ __('berkas pendaftaran') }}" 
                        badgeText="{{ __('Pendaftaran Terverifikasi') }}" 
                    />
                </div>
            @endif
        </div>

    </x-gentelella.x-panel>

</div>
@endsection

@vite(['resources/js/pages/admin/pendaftaran-index.js'])
