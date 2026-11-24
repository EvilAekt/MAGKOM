@push('styles')
    @vite(['resources/css/pages/admin/penempatan-index.css'])
@endpush

@extends('layouts.dashboard')

@section('title', 'Penempatan Peserta - Admin MagKom Sleman')

@section('topbar_title') Penempatan Peserta Magang <span class="sr-only">Manajemen Penempatan</span> @endsection

@section('content')
<div class="gentelella-penempatan-admin" style="display: flex; flex-direction: column; gap: 1.5rem; width: 100%;">


    {{-- 0. SLEMAN NAVY HERO BANNER (Khas MagKom) --}}
    <div class="adm-hero-banner">
        <div class="adm-hero-content">
            <div class="adm-hero-text">
                <h1 class="adm-hero-title">Penempatan Peserta &amp; Pembimbing</h1>
                <div class="adm-hero-subtitle">
                    <span>Penetapan Bidang Dinas &amp; Pembimbing Lapangan Peserta Magang</span>
                    <span class="sep">&bull;</span>
                    <span>Dinas Komunikasi dan Informatika Kabupaten Sleman</span>
                </div>
                <span class="sr-only" aria-hidden="true" style="display: none;">Alokasi Penempatan &amp; Pembimbing Magang</span>
                <span class="sr-only" aria-hidden="true" style="display: none;">Penetapan Bidang Dinas &amp; Pembimbing Lapangan</span>
            </div>
            <div class="adm-hero-actions">
                {{-- Unified Export Popover Trigger & Container --}}
                <div class="adm-export-dropdown-wrap">
                    <button type="button" 
                            id="btnToggleExportPopover" 
                            class="adm-btn-export-main adm-hero-btn-secondary" 
                            aria-expanded="false" 
                            aria-haspopup="menu" 
                            aria-controls="admExportPopover"
                            title="Pilih dan unduh rekap data penempatan (Excel, CSV, PDF)">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        <span>Ekspor Rekap</span>
                        <svg id="admExportCaretSvg" class="adm-export-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>

                    {{-- Floating Dropdown Popover Container (Right-Aligned) --}}
                    <div id="admExportPopover" class="adm-export-popover adm-export-popover-right" style="display: none;" role="menu" aria-label="Menu Pilihan Format Ekspor">
                        <div class="adm-popover-pointer adm-pointer-right"></div>
                        <div class="adm-popover-header">
                            <div>
                                <div class="adm-popover-title">Ekspor Rekap Cepat</div>
                                <div class="adm-popover-desc">Data hasil filter aktif</div>
                            </div>
                            <span class="adm-popover-row-count tabular-nums">
                                {{ number_format($penempatanList->total()) }} Baris
                            </span>
                        </div>

                        <div class="adm-popover-formats" role="radiogroup" aria-label="Format Berkas">
                            {{-- Format 1: Excel (.xlsx) - Rekomendasi --}}
                            <label class="adm-format-card adm-format-selected" id="cardFormatXlsx" data-action="select-export-format" data-format="xlsx">
                                <div class="adm-format-content">
                                    <input type="radio" name="popover_export_format" id="radioFormatXlsx" value="xlsx" checked class="adm-format-radio">
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
                            <label class="adm-format-card" id="cardFormatCsv" data-action="select-export-format" data-format="csv">
                                <div class="adm-format-content">
                                    <input type="radio" name="popover_export_format" id="radioFormatCsv" value="csv" class="adm-format-radio">
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
                            <label class="adm-format-card" id="cardFormatPdf" data-action="select-export-format" data-format="pdf">
                                <div class="adm-format-content">
                                    <input type="radio" name="popover_export_format" id="radioFormatPdf" value="pdf" class="adm-format-radio">
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
                            <button type="button" class="adm-btn-popover-cancel" data-action="cancel-export-popover">
                                Batal
                            </button>
                            <button type="button" id="btnDoExportDownload" class="adm-btn-popover-download" data-action="do-export-download">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                <span id="btnExportDownloadText">Unduh Excel (.xlsx)</span>
                            </button>
                        </div>
                    </div>
                </div>

                @if(auth()->user()?->isSuperAdmin())
                    <span style="display: inline-flex; align-items: center; gap: 0.4rem; background: rgba(255, 255, 255, 0.15); color: #FFFFFF; font-weight: 700; font-size: 0.8125rem; padding: 0.6rem 1rem; border-radius: 10px; border: 1px solid rgba(255, 255, 255, 0.25); backdrop-filter: blur(4px);">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                        <span>Mode Lihat Saja</span>
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- ALERT KREDENSIAL AKUN BARU JALUR KHUSUS --}}
    @if(session('akun_baru_dibuat'))
        <div class="admin-card" style="border-top: 3.5px solid #10B981; border-left: none; background: #ECFDF5; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.12);">
            <div style="display: flex; align-items: flex-start; gap: 0.85rem;">
                <div style="width: 42px; height: 42px; border-radius: 10px; background: #D1FAE5; color: #059669; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                </div>
                <div>
                    <h3 style="font-size: 1rem; font-weight: 800; color: #065F46; margin: 0;">
                        Akun Peserta Jalur Khusus Berhasil Dibuat &amp; Ditempatkan!
                    </h3>
                    <p style="font-size: 0.8125rem; color: #047857; margin: 0.25rem 0 0.5rem 0;">
                        Berikan kredensial login berikut kepada <strong>{{ session('nama_peserta') }}</strong>. Peserta dapat langsung login dan wajib mengganti kata sandi saat pertama kali masuk.
                    </p>
                    <div style="display: inline-flex; align-items: center; gap: 1rem; background: #ffffff; border: 1px solid #A7F3D0; border-radius: 8px; padding: 0.5rem 0.85rem; font-size: 0.8125rem; color: #1E293B; font-family: monospace;">
                        <span><strong>Email:</strong> {{ session('email_peserta') }}</span>
                        <span style="color: #CBD5E1;">|</span>
                        <span><strong>Password Awal:</strong> <span style="font-weight: 700; color: #0641CC;">{{ session('password_baru') }}</span></span>
                    </div>
                </div>
            </div>
            <button type="button" 
                    data-action="copy-credentials" 
                    data-email="{{ session('email_peserta') }}" 
                    data-password="{{ session('password_baru') }}" 
                    style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.55rem 1rem; background: #059669; color: #ffffff; border: none; border-radius: 8px; font-weight: 700; font-size: 0.8125rem; cursor: pointer; box-shadow: 0 1px 3px rgba(5, 150, 105, 0.25);">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                <span>Salin Kredensial</span>
            </button>
        </div>
    @endif

    {{-- 1. HERO STATS CARDS --}}
    <div class="row tile_count" style="margin-bottom: 1.5rem; width: 100%;">
        {{-- Total Penempatan --}}
        <x-gentelella.tile-stat 
            title="Total Penempatan" 
            :value="number_format($penempatanStats['total'] ?? $penempatanList->total())" 
            icon="users"
            color="blue"
            trendText="Total"
            trendDirection="up"
            subText="Semua riwayat peserta" 
            :sparkBars="[35, 50, 45, 65, 70, 85, 95]"
            class="admin-stat-card admin-stat-card--blue"
        />

        {{-- Peserta Aktif --}}
        <x-gentelella.tile-stat 
            title="Peserta Magang Aktif" 
            :value="number_format($penempatanStats['aktif'] ?? 0)" 
            icon="user-check"
            color="teal"
            trendText="Aktif"
            trendDirection="up"
            subText="Sedang masa bimbingan" 
            :sparkBars="[40, 50, 60, 68, 75, 82, 90]"
            class="admin-stat-card admin-stat-card--green"
        />

        {{-- Selesai Magang --}}
        <x-gentelella.tile-stat 
            title="Selesai Magang" 
            :value="number_format($penempatanStats['selesai'] ?? 0)" 
            icon="award"
            color="blue"
            trendText="Selesai"
            trendDirection="up"
            subText="Telah tuntas magang" 
            :sparkBars="[25, 40, 45, 60, 70, 80, 90]"
            class="admin-stat-card admin-stat-card--indigo"
        />

        {{-- Jalur Khusus (Kemitraan/Slot) --}}
        <x-gentelella.tile-stat 
            title="Jalur Khusus Mitra" 
            :value="number_format($penempatanStats['khusus'] ?? 0)" 
            icon="briefcase"
            color="yellow"
            trendText="Mitra"
            trendDirection="up"
            subText="MoU & kuota terproteksi" 
            :sparkBars="[30, 40, 35, 50, 45, 55, 60]"
            class="admin-stat-card admin-stat-card--amber"
        />

        {{-- Menunggu Penempatan (Antrean Lolos Berkas) --}}
        @if(($penempatanStats['belum_ditempatkan'] ?? 0) > 0)
            <x-gentelella.tile-stat 
                title="Menunggu Plotting" 
                :value="number_format($penempatanStats['belum_ditempatkan'] ?? 0)" 
                icon="clock"
                color="yellow"
                trendType="warning"
                trendText="Tahap 1"
                trendDirection="down"
                subText="Lolos berkas pendaftaran" 
                :sparkBars="[50, 60, 55, 70, 65, 80, 75]"
                class="admin-stat-card admin-stat-card--orange"
            />
        @endif
    </div>

    {{-- 2. NAVIGASI DUA TAB UTAMA: SUDAH DITEMPATKAN VS BELUM DITEMPATKAN (COMPACT SEGMENTED PILL BAR) --}}
    <div style="margin-bottom: 1.25rem;">
        <div class="penempatan-nav-tabs" role="tablist" aria-label="Status Penempatan Peserta" style="display: inline-flex; align-items: center; background: #F1F5F9; border: 1px solid #E2E8F0; border-radius: 10px; padding: 4px; gap: 4px; box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.04);">
            {{-- Tombol 1: Sudah Ditempatkan --}}
            <button type="button" 
                    id="tab-btn-sudah" 
                    role="tab"
                    aria-selected="{{ $activeTab === 'sudah' ? 'true' : 'false' }}"
                    data-action="switch-tab" data-tab="sudah"
                    title="Tampilkan peserta magang yang sudah memiliki pembimbing lapangan"
                    style="display: inline-flex; align-items: center; gap: 0.5rem; height: 38px; padding: 0 1rem; font-size: 0.84375rem; font-weight: {{ $activeTab === 'sudah' ? '700' : '600' }}; border-radius: 7px; border: 1px solid {{ $activeTab === 'sudah' ? 'rgba(226, 232, 240, 0.8)' : 'transparent' }}; background: {{ $activeTab === 'sudah' ? '#FFFFFF' : 'transparent' }}; color: {{ $activeTab === 'sudah' ? '#0641CC' : '#64748B' }}; cursor: pointer; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: {{ $activeTab === 'sudah' ? '0 1px 3px rgba(0, 0, 0, 0.08), 0 1px 2px rgba(0, 0, 0, 0.04)' : 'none' }}; white-space: nowrap;">
                <svg id="tab-icon-sudah" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; color: {{ $activeTab === 'sudah' ? '#0641CC' : '#64748B' }};">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <polyline points="16 11 18 13 22 9"></polyline>
                </svg>
                <span id="tab-title-sudah">Sudah Ditempatkan</span>
                <span id="tab-badge-sudah" style="display: inline-flex; align-items: center; justify-content: center; min-width: 20px; height: 20px; padding: 0 0.45rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: {{ $activeTab === 'sudah' ? '#EFF6FF' : '#E2E8F0' }}; color: {{ $activeTab === 'sudah' ? '#0641CC' : '#475569' }};">
                    {{ number_format($penempatanStats['total'] ?? $penempatanList->total()) }}
                </span>
            </button>

            {{-- Tombol 2: Belum Ditempatkan --}}
            <button type="button" 
                    id="tab-btn-belum" 
                    role="tab"
                    aria-selected="{{ $activeTab === 'belum' ? 'true' : 'false' }}"
                    data-action="switch-tab" data-tab="belum"
                    title="Tampilkan antrean peserta lolos berkas yang menunggu penetapan pembimbing"
                    style="display: inline-flex; align-items: center; gap: 0.5rem; height: 38px; padding: 0 1rem; font-size: 0.84375rem; font-weight: {{ $activeTab === 'belum' ? '700' : '600' }}; border-radius: 7px; border: 1px solid {{ $activeTab === 'belum' ? 'rgba(226, 232, 240, 0.8)' : 'transparent' }}; background: {{ $activeTab === 'belum' ? '#FFFFFF' : 'transparent' }}; color: {{ $activeTab === 'belum' ? '#B45309' : '#64748B' }}; cursor: pointer; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: {{ $activeTab === 'belum' ? '0 1px 3px rgba(0, 0, 0, 0.08), 0 1px 2px rgba(0, 0, 0, 0.04)' : 'none' }}; white-space: nowrap;">
                <svg id="tab-icon-belum" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; color: {{ $activeTab === 'belum' ? '#D97706' : '#64748B' }};">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <span id="tab-title-belum">Belum Ditempatkan</span>
                <span id="tab-badge-belum" style="display: inline-flex; align-items: center; justify-content: center; min-width: 20px; height: 20px; padding: 0 0.45rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: {{ ($penempatanStats['belum_ditempatkan'] ?? 0) > 0 ? '#FEF3C7' : '#E2E8F0' }}; color: {{ ($penempatanStats['belum_ditempatkan'] ?? 0) > 0 ? '#B45309' : '#475569' }};">
                    {{ number_format($penempatanStats['belum_ditempatkan'] ?? 0) }}
                </span>
            </button>
        </div>
    </div>

    {{-- PANEL 1: DATA PESERTA SUDAH DITEMPATKAN --}}
    <div id="panel-penempatan-sudah" style="{{ $activeTab === 'belum' ? 'display: none;' : '' }}">
        {{-- Filter Bar Card --}}
        <div class="admin-card" style="padding: 1.25rem 1.5rem; margin-bottom: 1.5rem;">
            <form method="GET" action="{{ route('admin.penempatan.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
                <input type="hidden" name="tab" value="sudah">
                {{-- Search Bar --}}
                <div style="display: flex; flex-direction: column; gap: 0.375rem; flex: 2; min-width: 240px;">
                    <label for="q-input" style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Cari Nama</label>
                    <div style="position: relative;">
                        <input type="text" id="q-input" name="q" value="{{ request('tab') !== 'belum' ? request('q') : '' }}" placeholder="Cari nama..." style="width: 100%; height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem 0 2.4rem; font-size: 0.875rem; background: #ffffff; color: var(--neutral-900, #0F172A);">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 0.85rem; top: 13px; color: var(--neutral-400, #94A3B8);">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </div>
                </div>

                {{-- Filter Bulan Magang --}}
                <div style="display: flex; flex-direction: column; gap: 0.375rem; flex: 1; min-width: 180px;">
                    <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Bulan Magang</label>
                    <select name="bulan_magang_id" style="height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff; color: var(--neutral-800, #1E293B);">
                        <option value="">Semua Bulan Magang</option>
                        @foreach($bulanList as $bln)
                            <option value="{{ $bln->id }}" {{ (request('tab') !== 'belum' && request('bulan_magang_id') == $bln->id) ? 'selected' : '' }}>
                                {{ $bln->nama_bulan ? $bln->nama_bulan . ' (' . $bln->bulan_mulai?->translatedFormat('F Y') . ')' : $bln->bulan_mulai?->translatedFormat('F Y') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Status --}}
                <div style="display: flex; flex-direction: column; gap: 0.375rem; flex: 1; min-width: 180px;">
                    <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Status Penempatan</label>
                    <select name="status" style="height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff; color: var(--neutral-800, #1E293B);">
                        <option value="">Semua Status</option>
                        @foreach($statusEnum as $status)
                            <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Pembimbing --}}
                <div style="display: flex; flex-direction: column; gap: 0.375rem; flex: 1; min-width: 200px;">
                    <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Pembimbing Lapangan</label>
                    <select name="pembimbing_id" style="height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff; color: var(--neutral-800, #1E293B);">
                        <option value="">Semua Pembimbing</option>
                        @foreach($pembimbingList as $pemb)
                            <option value="{{ $pemb->id }}" {{ request('pembimbing_id') == $pemb->id ? 'selected' : '' }}>
                                {{ $pemb->nama }} [{{ $pemb->bidang?->nama_bidang ?? 'Dinas' }}]
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Bidang --}}
                <div style="display: flex; flex-direction: column; gap: 0.375rem; flex: 1; min-width: 180px;">
                    <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Bidang Dinas</label>
                    <select name="bidang_id" style="height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff; color: var(--neutral-800, #1E293B);">
                        <option value="">Semua Bidang</option>
                        @foreach($bidangList as $bdg)
                            <option value="{{ $bdg->id }}" {{ request('bidang_id') == $bdg->id ? 'selected' : '' }}>
                                {{ $bdg->nama_bidang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="display: flex; gap: 0.5rem; align-items: center;">
                    <button type="submit" style="height: 42px; padding: 0 1.25rem; background: #0641CC; color: #FFFFFF; font-weight: 600; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer; transition: background 0.15s ease; box-shadow: 0 1px 3px rgba(6, 65, 204, 0.2);">
                        Terapkan
                    </button>
                    @if((request('tab') === 'sudah' || !request('tab')) && (request()->filled('q') || request()->filled('status') || request()->filled('pembimbing_id') || request()->filled('bidang_id') || request()->filled('bulan_magang_id')))
                        <a href="{{ route('admin.penempatan.index', ['tab' => 'sudah']) }}" style="height: 42px; padding: 0 1rem; background: var(--neutral-100, #F1F5F9); color: var(--neutral-700, #334155); font-weight: 600; font-size: 0.875rem; text-decoration: none; border-radius: 8px; display: inline-flex; align-items: center; border: 1px solid var(--neutral-200, #E2E8F0);" title="Kembalikan pencarian ke kondisi awal">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- 3. DATA TABLE CARD --}}
        <x-gentelella.x-panel title="Data Penempatan Peserta">
            @if($penempatanList->isEmpty())
                <div style="padding: 4rem 1.5rem; text-align: center; color: var(--neutral-600, #64748B);">
                    <div style="width: 64px; height: 64px; background: #EFF6FF; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem auto; color: #1D4ED8;">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                    </div>
                    <h4 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-900, #0F172A); margin-bottom: 0.375rem;">
                        Belum Ada Data Penempatan
                    </h4>
                    <p style="font-size: 0.875rem; max-width: 460px; margin: 0 auto;">Belum ada peserta magang yang sesuai dengan kriteria filter yang dipilih.</p>
                </div>
            @else
                <div class="adm-table-container">
                    <div style="overflow-x: auto; width: 100%;">
                        <table class="adm-table admin-table bulk_action">
                        <thead>
                            <tr>
                                <th style="width: 4%; min-width: 40px; text-align: center;">
                                    <input type="checkbox" id="checkAllPenempatan" style="cursor: pointer; width: 16px; height: 16px;">
                                </th>
                                <th style="width: 25%; min-width: 200px; text-align: left;">Peserta Magang</th>
                                <th style="width: 23%; min-width: 180px; text-align: left;">Pembimbing Lapangan</th>
                                <th style="width: 21%; min-width: 170px; text-align: left;">Jalur / Bidang Dinas</th>
                                <th style="width: 14%; min-width: 130px; text-align: left;">Bulan Magang</th>
                                <th style="width: 8%; min-width: 90px; text-align: center;">Status</th>
                                <th style="width: 5%; min-width: 65px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($penempatanList as $p)
                                @php
                                    $statusVal = is_object($p->status) ? $p->status->value : $p->status;
                                    $statusObj = is_object($p->status) ? $p->status : \App\Enums\StatusPenempatanEnum::tryFrom($p->status);
                                    $badgeStyle = match($statusVal) {
                                        'aktif' => ['bg' => '#D1FAE5', 'color' => '#065F46', 'border' => '#A7F3D0'],
                                        'selesai' => ['bg' => '#EFF6FF', 'color' => '#1D4ED8', 'border' => '#DBEAFE'],
                                        'mengundurkan_diri' => ['bg' => '#F1F5F9', 'color' => '#475569', 'border' => '#CBD5E1'],
                                        'nonaktif' => ['bg' => '#FEE2E2', 'color' => '#991B1B', 'border' => '#FECACA'],
                                        default => ['bg' => '#F1F5F9', 'color' => '#475569', 'border' => '#CBD5E1'],
                                    };
                                @endphp
                                <tr>
                                    <td style="text-align: center;">
                                        <input type="checkbox" value="{{ $p->id }}" class="penempatan-checkbox" style="cursor: pointer; width: 16px; height: 16px;">
                                    </td>
                                    {{-- Peserta Magang --}}
                                    <td>
                                        @php
                                            $cleanPesertaNama = str_replace(' (Peserta Magang)', '', (string) ($p->peserta?->nama ?? '-'));
                                        @endphp
                                        <div style="display: flex; align-items: center; gap: 0.85rem;">
                                            <div style="width: 38px; height: 38px; border-radius: 10px; overflow: hidden; background: linear-gradient(135deg, #0641CC 0%, #2563EB 100%); color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.875rem; flex-shrink: 0; box-shadow: 0 1px 3px rgba(6, 65, 204, 0.2);">
                                                @if($p->peserta?->profil?->foto_url)
                                                    <img src="{{ $p->peserta->profil->foto_url }}" alt="{{ $cleanPesertaNama }}" style="width: 100%; height: 100%; object-fit: cover;">
                                                @else
                                                    {{ strtoupper(substr($cleanPesertaNama, 0, 1)) }}
                                                @endif
                                            </div>
                                            <div>
                                                <div style="font-weight: 700; color: var(--neutral-900, #0F172A); font-size: 0.875rem;">
                                                    {{ $cleanPesertaNama }}
                                                </div>
                                                <div style="font-size: 0.75rem; color: var(--neutral-500, #64748B); margin-top: 3px;">
                                                    {{ $p->peserta?->profil?->institusi ?? '-' }}
                                                </div>
                                                <small style="font-size: 0.7rem; color: var(--neutral-400, #94A3B8); margin-top: 2px; display: block;">
                                                    NIM: {{ $p->peserta?->profil?->nim_nisn ?? '-' }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Pembimbing Lapangan --}}
                                    <td>
                                        @php
                                            $cleanPembimbingNama = str_replace(' (Pembimbing Lapangan)', '', (string) ($p->pembimbing?->nama ?? 'Belum Ditentukan'));
                                        @endphp
                                        <div style="font-weight: 700; color: var(--neutral-900, #0F172A); font-size: 0.875rem;">
                                            {{ $cleanPembimbingNama }}
                                        </div>
                                        @if($p->pembimbing)
                                            <div style="display: inline-flex; align-items: center; gap: 0.5rem; margin-top: 4px; flex-wrap: wrap;">
                                                <span style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.725rem; color: var(--neutral-600, #475569); background: var(--neutral-100, #F1F5F9); padding: 0.15rem 0.5rem; border-radius: 6px; border: 1px solid var(--neutral-200, #E2E8F0); font-weight: 600;">
                                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                                                    <span>{{ $p->pembimbing->mentee_aktif_count ?? 1 }} Mentee Aktif</span>
                                                </span>
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Jalur & Bidang Dinas --}}
                                    <td>
                                        <div style="font-weight: 600; color: var(--neutral-900, #0F172A);">
                                            {{ $p->bidang?->nama_bidang ?? 'Dinas Kominfo Sleman' }}
                                        </div>
                                        <div style="margin-top: 4px;">
                                            @if($p->slotKhusus)
                                                <span style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-500, #64748B);" title="{{ $p->slotKhusus->nama_mitra ? 'Mitra: '.$p->slotKhusus->nama_mitra : 'Jalur Khusus' }}">
                                                    Khusus
                                                </span>
                                            @else
                                                <span style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-500, #64748B);">
                                                    Umum
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Periode Magang --}}
                                    <td style="font-size: 0.8125rem;">
                                        <div style="font-weight: 700; color: var(--neutral-800, #1E293B);">
                                            {{ \Carbon\Carbon::parse($p->tgl_mulai)->translatedFormat('d M Y') }} s/d
                                        </div>
                                        <div style="font-weight: 700; color: var(--neutral-800, #1E293B); margin-top: 2px;">
                                            {{ \Carbon\Carbon::parse($p->tgl_selesai)->translatedFormat('d M Y') }}
                                        </div>
                                    </td>

                                    {{-- Status --}}
                                    <td style="text-align: center;">
                                        <span style="display: inline-flex; align-items: center; justify-content: center; gap: 0.35rem; padding: 0.25rem 0.65rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: {{ $badgeStyle['bg'] }}; color: {{ $badgeStyle['color'] }}; border: 1px solid {{ $badgeStyle['border'] }};">
                                            {{ $statusObj ? $statusObj->label() : ucfirst($statusVal) }}
                                        </span>
                                    </td>

                                    {{-- Aksi --}}
                                    <td style="text-align: center;">
                                        <div style="display: inline-flex; gap: 0.35rem; align-items: center; justify-content: center;">
                                            @if($p->pembimbing && !auth()->user()?->isSuperAdmin())
                                                <button type="button" 
                                                        class="action-dropdown-btn" 
                                                        data-action="open-pindah-modal" data-penempatan-id="{{ $p->id }}" data-peserta-nama="{{ $cleanPesertaNama }}" data-pembimbing-id="{{ $p->pembimbing_id ?? '' }}" 
                                                        title="Ganti Pembimbing Lapangan"
                                                        aria-label="Ganti Pembimbing Lapangan">
                                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                                        <circle cx="9" cy="7" r="4"></circle>
                                                        <polyline points="16 11 18 13 22 9"></polyline>
                                                    </svg>
                                                </button>
                                            @elseif(auth()->user()?->isSuperAdmin())
                                                <span style="font-size: 0.75rem; color: #94A3B8; font-style: italic;">Read-Only</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                    <div class="adm-table-footer">
                        <x-gentelella.pagination-bar 
                            :paginator="$penempatanList" 
                            :perPageOptions="[5, 10, 25]" 
                            entity="peserta magang" 
                            badgeText="Penempatan Aktif" 
                        />
                    </div>
                </div>
            @endif
        </x-gentelella.x-panel>
    </div>

    {{-- PANEL 2: DATA PESERTA BELUM DITEMPATKAN (ANTREAN LOLOS BERKAS) --}}
    <div id="panel-penempatan-belum" style="{{ $activeTab === 'belum' ? '' : 'display: none;' }}">
        {{-- Filter Bar Khusus Antrean Belum Ditempatkan --}}
        <div class="admin-card" style="padding: 1.25rem 1.5rem; margin-bottom: 1.5rem;">
            <form method="GET" action="{{ route('admin.penempatan.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
                <input type="hidden" name="tab" value="belum">
                
                {{-- 1. Search Bar --}}
                <div style="display: flex; flex-direction: column; gap: 0.375rem; flex: 2; min-width: 240px;">
                    <label for="q-input-belum" style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Cari Nama</label>
                    <div style="position: relative;">
                        <input type="text" id="q-input-belum" name="q" value="{{ request('tab') === 'belum' ? request('q') : '' }}" placeholder="Cari nama..." style="width: 100%; height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem 0 2.4rem; font-size: 0.875rem; background: #ffffff; color: var(--neutral-900, #0F172A);">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 0.85rem; top: 13px; color: var(--neutral-400, #94A3B8);">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </div>
                </div>

                {{-- 2. Filter Bulan Magang --}}
                <div style="display: flex; flex-direction: column; gap: 0.375rem; flex: 1.2; min-width: 180px;">
                    <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Bulan Magang</label>
                    <select name="bulan_magang_id" style="width: 100%; height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff; color: var(--neutral-800, #1E293B);">
                        <option value="">Semua Bulan Magang</option>
                        @foreach($bulanList as $bln)
                            <option value="{{ $bln->id }}" {{ (request('tab') === 'belum' && request('bulan_magang_id') == $bln->id) ? 'selected' : '' }}>
                                {{ $bln->nama_bulan ? $bln->nama_bulan . ' (' . $bln->bulan_mulai?->translatedFormat('F Y') . ')' : $bln->bulan_mulai?->translatedFormat('F Y') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 3. Filter Jurusan --}}
                <div style="display: flex; flex-direction: column; gap: 0.375rem; flex: 1.2; min-width: 180px;">
                    <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Jurusan</label>
                    <select name="jurusan_id" style="width: 100%; height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff; color: var(--neutral-800, #1E293B);">
                        <option value="">Semua Jurusan</option>
                        @foreach($jurusanList as $jrs)
                            <option value="{{ $jrs->id }}" {{ (request('tab') === 'belum' && request('jurusan_id') == $jrs->id) ? 'selected' : '' }}>
                                {{ $jrs->nama_jurusan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 4. Filter Rekomendasi Bidang Dinas --}}
                <div style="display: flex; flex-direction: column; gap: 0.375rem; flex: 1.2; min-width: 180px;">
                    <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Bidang Rekomendasi</label>
                    <select name="bidang_id" style="width: 100%; height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff; color: var(--neutral-800, #1E293B);">
                        <option value="">Semua Bidang</option>
                        @foreach($bidangList as $bdg)
                            <option value="{{ $bdg->id }}" {{ (request('tab') === 'belum' && request('bidang_id') == $bdg->id) ? 'selected' : '' }}>
                                {{ $bdg->nama_bidang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 5. Tombol Aksi --}}
                <div style="display: flex; gap: 0.5rem; align-items: center;">
                    <button type="submit" style="height: 42px; padding: 0 1.25rem; background: #0641CC; color: #FFFFFF; font-weight: 600; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer; transition: background 0.15s ease; box-shadow: 0 1px 3px rgba(6, 65, 204, 0.2);">
                        Terapkan
                    </button>
                    @if(request('tab') === 'belum' && (request()->filled('q') || request()->filled('bulan_magang_id') || request()->filled('jurusan_id') || request()->filled('bidang_id')))
                        <a href="{{ route('admin.penempatan.index', ['tab' => 'belum']) }}" style="height: 42px; padding: 0 1rem; background: var(--neutral-100, #F1F5F9); color: var(--neutral-700, #334155); font-weight: 600; font-size: 0.875rem; text-decoration: none; border-radius: 8px; display: inline-flex; align-items: center; border: 1px solid var(--neutral-200, #E2E8F0);" title="Kembalikan pencarian ke kondisi awal">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <x-gentelella.x-panel title="Antrean Peserta Lolos Berkas (Menunggu Penempatan)">
            @if($antreanBelumDitempatkan->isEmpty())
                <div style="padding: 4rem 1.5rem; text-align: center; color: var(--neutral-600, #64748B);">
                    <div style="width: 64px; height: 64px; border-radius: 50%; background: #F8FAFC; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem auto; color: #94A3B8; border: 1px solid #E2E8F0;">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <h4 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-900, #0F172A); margin-bottom: 0.375rem;">
                        Tidak ada antrean peserta belum ditempatkan
                    </h4>
                    <p style="font-size: 0.875rem; max-width: 480px; margin: 0 auto; line-height: 1.5;">
                        Seluruh calon peserta lolos berkas telah ditetapkan pembimbing dan bidang dinas.
                    </p>
                </div>
            @else
                <div class="adm-table-container">
                    <div style="overflow-x: auto; width: 100%;">
                        <table class="adm-table admin-table">
                            <thead>
                                <tr>
                                    <th style="width: 28%; min-width: 220px; text-align: left;">Peserta Magang</th>
                                    <th style="width: 26%; min-width: 200px; text-align: left;">Jurusan</th>
                                    <th style="width: 18%; min-width: 140px; text-align: left;">Bulan Magang</th>
                                    <th style="width: 16%; min-width: 130px; text-align: center;">Diterima Pada</th>
                                    <th style="width: 12%; min-width: 100px; text-align: center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($antreanBelumDitempatkan as $antrean)
                                    @php
                                        $firstBidang = $antrean->kuota?->jurusan?->bidang?->first();
                                        $defBidangId = $firstBidang?->id;
                                        $defBidangNama = $firstBidang?->nama_bidang ?? 'Dinas Kominfo';
                                    @endphp
                                    <tr>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 0.85rem;">
                                                <div style="width: 34px; height: 34px; border-radius: 8px; background: linear-gradient(135deg, #01347C 0%, #0641CC 100%); color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; flex-shrink: 0; box-shadow: 0 1px 3px rgba(6, 65, 204, 0.2);">
                                                    {{ strtoupper(substr($antrean->user?->nama ?? 'P', 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div style="font-weight: 700; color: #1E293B; font-size: 0.875rem;">
                                                        {{ $antrean->user?->nama }}
                                                    </div>
                                                    <div style="font-size: 0.75rem; color: #64748B; margin-top: 3px;">
                                                        NIM: {{ $antrean->user?->profil?->nim_nisn ?? '-' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600; color: #1E293B;">
                                                {{ $antrean->kuota?->jurusan?->nama_jurusan ?? '-' }}
                                            </div>
                                            <div style="font-size: 0.75rem; color: #64748B; margin-top: 3px;">
                                                {{ $antrean->user?->profil?->institusi ?? '-' }}
                                            </div>
                                        </td>
                                        <td>
                                            <span style="font-weight: 700; color: #0641CC; font-size: 0.8125rem;">
                                                {{ $antrean->bulanMagang?->bulan_mulai?->translatedFormat('F Y') }}
                                            </span>
                                            <div style="font-size: 0.75rem; color: #64748B; margin-top: 3px;">
                                                Durasi: {{ $antrean->durasi_bulan }} Bulan
                                            </div>
                                        </td>
                                        <td style="text-align: center;">
                                            <span style="font-size: 0.8125rem; color: #475569;">
                                                {{ $antrean->diputuskan_at?->translatedFormat('d M Y H:i') ?? '-' }}
                                            </span>
                                        </td>
                                        <td style="text-align: center;">
                                            @if(auth()->user()?->isSuperAdmin())
                                                <span style="font-size: 0.75rem; color: #94A3B8; font-weight: 500;">Menunggu Penempatan</span>
                                            @else
                                                <div style="display: inline-flex; justify-content: center; align-items: center;">
                                                    <button type="button" 
                                                            data-action="open-tetapkan-modal" data-pendaftaran-id="{{ $antrean->id }}" data-peserta-nama="{{ $antrean->user?->nama }}" data-jurusan-nama="{{ $antrean->kuota?->jurusan?->nama_jurusan ?? ' }}" data-bidang-id="{{ $defBidangId ?? ' }}" data-bidang-nama="{{ $defBidangNama }}" 
                                                            style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.45rem 0.95rem; background: #0641CC; color: #FFFFFF; border: none; border-radius: 8px; font-weight: 700; font-size: 0.8125rem; cursor: pointer; box-shadow: 0 1px 3px rgba(6, 65, 204, 0.25); transition: background 0.15s ease;">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
                                                        <span>Tempatkan</span>
                                                    </button>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="adm-table-footer">
                        <x-gentelella.pagination-bar 
                            :paginator="$antreanBelumDitempatkan" 
                            :perPageOptions="[5, 10, 25]" 
                            entity="peserta magang" 
                            badgeText="Antrean Penempatan" 
                        />
                    </div>
                </div>
            @endif
        </x-gentelella.x-panel>
    </div>

</div>

{{-- MODAL PENEMPATAN MANUAL (JALUR KHUSUS) --}}
<div id="modal-penempatan-manual" class="modal-overlay">
    <div class="modal-container" style="max-width: 620px; max-height: 90vh; overflow-y: auto;">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0); display: flex; justify-content: space-between; align-items: center; background: #F8FAFC;">
            <div>
                <h3 style="font-size: 1.0625rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0;">Penempatan Peserta Jalur Khusus</h3>
                <p style="font-size: 0.75rem; color: var(--neutral-500, #64748B); margin: 0.15rem 0 0 0;">
                    Alokasi peserta magang berdasarkan kuota kemitraan slot khusus dinas
                </p>
            </div>
            <button type="button" data-action="close-manual-modal" style="background: transparent; border: none; font-size: 1.25rem; color: var(--neutral-400, #94A3B8); cursor: pointer;">✕</button>
        </div>

        <form method="POST" action="{{ route('admin.penempatan.manual') }}" id="form-penempatan-manual" style="padding: 1.5rem;">
            @csrf

            @if($errors->any() && (old('mode') || $errors->has('nama') || $errors->has('email') || $errors->has('nim_nisn') || $errors->has('slot_khusus_id') || $errors->has('peserta_id')))
                <div style="background: #FEF2F2; border: 1px solid #FCA5A5; border-radius: 8px; padding: 0.75rem 1rem; margin-bottom: 1.25rem; font-size: 0.8125rem; color: #991B1B;">
                    <div style="font-weight: 700; margin-bottom: 0.25rem;">Gagal menyimpan penempatan:</div>
                    <ul style="margin: 0 0 0 1.25rem; padding: 0;">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Switcher Mode Peserta --}}
            <div class="peserta-mode-switcher">
                <button type="button" id="btn-mode-terdaftar" class="peserta-mode-btn is-active" data-action="switch-peserta-mode" data-mode="terdaftar">
                    Peserta Terdaftar
                </button>
                <button type="button" id="btn-mode-baru" class="peserta-mode-btn" data-action="switch-peserta-mode" data-mode="baru">
                    + Daftarkan Peserta Baru
                </button>
            </div>
            <input type="hidden" name="mode" id="input-mode-peserta" value="terdaftar">
            <input type="hidden" name="bulan_mulai" id="input-bulan-mulai" value="{{ date('Y-m-01') }}">

            {{-- Mode 1: Peserta Terdaftar --}}
            <div id="panel-peserta-terdaftar" style="margin-bottom: 1.25rem;">
                <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B); display: block; margin-bottom: 0.375rem;">
                    Pilih Calon Peserta Terdaftar <span style="color: #DC2626;">*</span>
                </label>
                <select name="peserta_id" id="select-peserta-id" style="width: 100%; height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff;">
                    <option value="">-- Pilih Peserta Aktif --</option>
                    @foreach($pesertaList ?? [] as $pes)
                        <option value="{{ $pes->id }}">{{ $pes->nama }} ({{ $pes->email }}) &bull; {{ $pes->profil?->institusi ?? '-' }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Mode 2: Daftarkan Peserta Baru --}}
            <div id="panel-peserta-baru" style="display: none; flex-direction: column; gap: 0.875rem; margin-bottom: 1.25rem; background: #EFF6FF; border: 1px solid #DBEAFE; border-radius: 8px; padding: 1rem;">
                <div style="font-size: 0.8125rem; font-weight: 700; color: #1E40AF;">
                    Identitas Peserta Baru (Akun akan dibuat otomatis)
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-800, #1E293B);">Nama Lengkap <span style="color: #DC2626;">*</span></label>
                        <input type="text" name="nama" id="input-nama-baru" value="{{ old('nama') }}" placeholder="Nama siswa/mahasiswa" style="width: 100%; height: 38px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 6px; padding: 0 0.625rem; font-size: 0.8125rem; background: #ffffff;">
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-800, #1E293B);">Email Aktif <span style="color: #DC2626;">*</span></label>
                        <input type="email" name="email" id="input-email-baru" value="{{ old('email') }}" placeholder="peserta.magang@gmail.com" style="width: 100%; height: 38px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 6px; padding: 0 0.625rem; font-size: 0.8125rem; background: #ffffff;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-800, #1E293B);">NIM / NISN <span style="color: #DC2626;">*</span></label>
                        <input type="text" name="nim_nisn" id="input-nim-baru" value="{{ old('nim_nisn') }}" placeholder="Nomor induk siswa/mhs" style="width: 100%; height: 38px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 6px; padding: 0 0.625rem; font-size: 0.8125rem; background: #ffffff;">
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-800, #1E293B);">Jenjang Pendidikan <span style="color: #DC2626;">*</span></label>
                        <select name="jenjang" style="width: 100%; height: 38px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 6px; padding: 0 0.625rem; font-size: 0.8125rem; background: #ffffff;">
                            <option value="s1" {{ old('jenjang') === 's1' ? 'selected' : '' }}>S1 (Strata 1)</option>
                            <option value="d3" {{ old('jenjang') === 'd3' ? 'selected' : '' }}>D3 (Diploma 3)</option>
                            <option value="smk" {{ old('jenjang') === 'smk' ? 'selected' : '' }}>SMK</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-800, #1E293B);">Asal Kampus / Sekolah <span style="color: #DC2626;">*</span></label>
                        <input type="text" name="institusi" id="input-institusi-baru" value="{{ old('institusi') }}" placeholder="Nama Universitas / SMK" style="width: 100%; height: 38px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 6px; padding: 0 0.625rem; font-size: 0.8125rem; background: #ffffff;">
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-800, #1E293B);">Jurusan Asal <span style="color: #DC2626;">*</span></label>
                        <select name="jurusan_id" style="width: 100%; height: 38px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 6px; padding: 0 0.625rem; font-size: 0.8125rem; background: #ffffff;">
                            <option value="">-- Pilih Jurusan Magang --</option>
                            @foreach($jurusanList ?? [] as $j)
                                <option value="{{ $j->id }}" {{ old('jurusan_id') == $j->id ? 'selected' : '' }}>{{ $j->nama_jurusan }} ({{ strtoupper(is_object($j->kategori) ? $j->kategori->value : (string)$j->kategori) }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-800, #1E293B);">Kata Sandi Awal (Default: Magkom2026!)</label>
                    <input type="text" name="password" id="input-password-baru" value="{{ old('password', 'Magkom2026!') }}" placeholder="Magkom2026!" style="width: 100%; height: 38px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 6px; padding: 0 0.625rem; font-size: 0.8125rem; background: #ffffff;">
                    <small style="font-size: 0.7rem; color: var(--neutral-500, #64748B);">Peserta wajib mengganti kata sandi ini saat pertama kali login.</small>
                </div>
            </div>

            {{-- Pilihan Slot Khusus --}}
            <div id="wrapper-slot-khusus" style="display: flex; flex-direction: column; gap: 0.375rem; margin-bottom: 1rem;">
                <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">
                    Pilih Slot Kemitraan Khusus <span style="color: #DC2626;">*</span>
                </label>
                <select name="slot_khusus_id" id="select-slot-khusus" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                    <option value="">-- Pilih Slot Kemitraan Khusus --</option>
                    @foreach($slotKhususList as $sk)
                        <option value="{{ $sk->id }}" {{ old('slot_khusus_id') == $sk->id ? 'selected' : '' }}>
                            {{ $sk->nama_mitra }} &bull; Bulan: {{ $sk->bulan_mulai?->translatedFormat('F Y') }} (Sisa: {{ $sk->jumlah - $sk->terisi }} Kursi)
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Pembimbing & Bidang --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1rem;">
                <div>
                    <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Pembimbing Lapangan <span style="color: #DC2626;">*</span></label>
                    <select name="pembimbing_id" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                        <option value="">-- Pilih Pembimbing --</option>
                        @foreach($pembimbingList as $p)
                            <option value="{{ $p->id }}" {{ old('pembimbing_id') == $p->id ? 'selected' : '' }}>{{ $p->nama }} [{{ $p->bidang?->nama_bidang ?? 'Dinas' }}] ({{ $p->mentee_aktif_count ?? 0 }} Mentee)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Bidang Dinas</label>
                    <select name="bidang_id" style="width: 100%; height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                        <option value="">-- Sesuai Pembimbing --</option>
                        @foreach($bidangList as $b)
                            <option value="{{ $b->id }}" {{ old('bidang_id') == $b->id ? 'selected' : '' }}>{{ $b->nama_bidang }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Tanggal Mulai, Durasi & Tanggal Selesai --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem; margin-bottom: 1.25rem;">
                <div>
                    <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Tanggal Mulai <span style="color: #DC2626;">*</span></label>
                    <input type="date" name="tgl_mulai" id="input-tgl-mulai" required value="{{ old('tgl_mulai', date('Y-m-d')) }}"  style="width: 100%; height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                </div>
                <div>
                    <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Durasi Magang <span style="color: #DC2626;">*</span></label>
                    <select name="durasi_bulan" id="select-durasi-bulan"  style="width: 100%; height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff;">
                        <option value="1" {{ old('durasi_bulan') == 1 ? 'selected' : '' }}>1 Bulan</option>
                        <option value="2" {{ old('durasi_bulan') == 2 ? 'selected' : '' }}>2 Bulan</option>
                        <option value="3" {{ old('durasi_bulan', 3) == 3 ? 'selected' : '' }}>3 Bulan (Standar)</option>
                        <option value="4" {{ old('durasi_bulan') == 4 ? 'selected' : '' }}>4 Bulan</option>
                        <option value="5" {{ old('durasi_bulan') == 5 ? 'selected' : '' }}>5 Bulan</option>
                        <option value="6" {{ old('durasi_bulan') == 6 ? 'selected' : '' }}>6 Bulan</option>
                    </select>
                </div>
                <div>
                    <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Tanggal Selesai <span style="color: #DC2626;">*</span></label>
                    <input type="date" name="tgl_selesai" id="input-tgl-selesai" required value="{{ old('tgl_selesai', date('Y-m-d', strtotime('+3 months'))) }}" style="width: 100%; height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.6rem; border-top: 1px solid var(--neutral-200, #E2E8F0); padding-top: 1.25rem;">
                <button type="button" data-action="close-manual-modal" style="padding: 0.55rem 1.25rem; background: #ffffff; border: 1px solid var(--neutral-300, #CBD5E1); color: var(--neutral-700, #334155); border-radius: 8px; font-weight: 600; font-size: 0.875rem; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="padding: 0.55rem 1.4rem; background: #0641CC; color: #ffffff; border: none; border-radius: 8px; font-weight: 600; font-size: 0.875rem; cursor: pointer; box-shadow: 0 1px 3px rgba(6, 65, 204, 0.25);">
                    Tempatkan Peserta
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL PINDAH PEMBIMBING --}}
<div id="modal-pindah-pembimbing" class="modal-overlay">
    <div class="modal-container">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0); display: flex; justify-content: space-between; align-items: center; background: #F8FAFC;">
            <div>
                <h3 style="font-size: 1.0625rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0;">Mutasi / Ganti Pembimbing</h3>
                <p style="font-size: 0.75rem; color: var(--neutral-500, #64748B); margin: 0.15rem 0 0 0;" id="pindah-peserta-nama">
                    Peserta: -
                </p>
            </div>
            <button type="button" data-action="close-pindah-modal" style="background: transparent; border: none; font-size: 1.25rem; color: var(--neutral-400, #94A3B8); cursor: pointer;">✕</button>
        </div>

        <form method="POST" id="form-pindah-pembimbing" action="" style="padding: 1.5rem;">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 0.375rem; margin-bottom: 1.25rem;">
                <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Pilih Pembimbing Baru <span style="color: #DC2626;">*</span></label>
                <select name="pembimbing_id" id="select-pembimbing-baru" required style="height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                    @foreach($pembimbingList as $p)
                        <option value="{{ $p->id }}">{{ $p->nama }} [{{ $p->bidang?->nama_bidang ?? 'Dinas' }}] (Beban: {{ $p->mentee_aktif_count ?? 0 }} Mentee Aktif)</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.6rem; border-top: 1px solid var(--neutral-200, #E2E8F0); padding-top: 1.25rem;">
                <button type="button" data-action="close-pindah-modal" style="padding: 0.55rem 1.25rem; background: #ffffff; border: 1px solid var(--neutral-300, #CBD5E1); color: var(--neutral-700, #334155); border-radius: 8px; font-weight: 600; font-size: 0.875rem; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="padding: 0.55rem 1.4rem; background: #0641CC; color: #ffffff; border: none; border-radius: 8px; font-weight: 600; font-size: 0.875rem; cursor: pointer; box-shadow: 0 1px 3px rgba(6, 65, 204, 0.25);">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL BATALKAN PENEMPATAN --}}
<div id="modal-batal-penempatan" class="modal-overlay">
    <div class="modal-container">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0); display: flex; justify-content: space-between; align-items: center; background: #FEF2F2;">
            <div>
                <h3 style="font-size: 1.0625rem; font-weight: 800; color: #991B1B; margin: 0;">Pembatalan / Pengunduran Diri</h3>
                <p style="font-size: 0.75rem; color: #B91C1C; margin: 0.15rem 0 0 0;" id="batal-peserta-nama"></p>
            </div>
            <button type="button" data-action="close-batal-modal" style="background: transparent; border: none; font-size: 1.25rem; color: #B91C1C; cursor: pointer;">✕</button>
        </div>

        <form method="POST" id="form-batal-penempatan" action="" style="padding: 1.5rem;">
            @csrf
            <div style="background: #FFFBEB; border: 1px solid #FCD34D; border-radius: 8px; padding: 0.85rem 1rem; margin-bottom: 1.25rem; font-size: 0.75rem; color: #92400E; line-height: 1.4;">
                <strong>Ketentuan Pembatalan & Pengunduran Diri:</strong>
                <ul style="margin: 0.25rem 0 0 1rem; padding: 0;">
                    <li><strong>Sebelum tanggal mulai:</strong> Kuota jurusan/mitra akan otomatis dikembalikan ke sistem.</li>
                    <li><strong>Sejak/setelah tanggal mulai:</strong> Kuota hangus (tidak dikembalikan) dan akun peserta otomatis dinonaktifkan.</li>
                </ul>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.375rem; margin-bottom: 1.5rem;">
                <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Alasan Pembatalan / Pengunduran Diri <span style="color: #DC2626;">*</span></label>
                <textarea name="alasan" required rows="3" placeholder="Sebutkan alasan pembatalan atau pengunduran diri secara detail..." style="border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0.75rem; font-size: 0.875rem; font-family: inherit; color: var(--neutral-900, #0F172A);"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.6rem; border-top: 1px solid var(--neutral-200, #E2E8F0); padding-top: 1.25rem;">
                <button type="button" data-action="close-batal-modal" style="padding: 0.55rem 1.25rem; background: #ffffff; border: 1px solid var(--neutral-300, #CBD5E1); color: var(--neutral-700, #334155); border-radius: 8px; font-weight: 600; font-size: 0.875rem; cursor: pointer;">
                    Kembali
                </button>
                <button type="submit" style="padding: 0.55rem 1.4rem; background: #DC2626; color: #FFFFFF; border: none; border-radius: 8px; font-weight: 600; font-size: 0.875rem; cursor: pointer;">
                    Konfirmasi Batalkan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL IN-APP PRATINJAU SURAT DITERIMA --}}
<div id="modal-surat-preview"  style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(6px); z-index: 9999; align-items: center; justify-content: center; padding: 1.25rem;">
    <div style="background: #ffffff; border-radius: 16px; width: 100%; max-width: 960px; max-height: 92vh; display: flex; flex-direction: column; padding: 1.25rem 1.5rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); overflow: hidden; border: 1px solid var(--neutral-200, #E2E8F0);">
        <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 1rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0);">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 38px; height: 38px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: #D1FAE5; color: #065F46;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                </div>
                <div>
                    <h3 id="surat-modal-title" style="font-size: 1rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0;">Surat Keterangan Diterima Magang</h3>
                    <p style="font-size: 0.75rem; color: var(--neutral-500, #64748B); margin: 2px 0 0 0;">Dokumen Resmi Dinas Kominfo Kabupaten Sleman</p>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <a id="surat-modal-download" href="#" style="display: inline-flex; align-items: center; gap: 0.375rem; background: #0641CC; color: #FFFFFF; padding: 0.45rem 0.95rem; border-radius: 8px; font-weight: 600; font-size: 0.8125rem; text-decoration: none; box-shadow: 0 1px 3px rgba(6, 65, 204, 0.2);">
                    Unduh PDF
                </a>
                <button type="button" data-action="close-surat-preview" style="background: none; border: none; font-size: 1.5rem; line-height: 1; cursor: pointer; color: var(--neutral-400, #94A3B8); padding: 0 0.25rem;">&times;</button>
            </div>
        </div>

        <div style="flex: 1; min-height: 60vh; max-height: 75vh; position: relative; background: #F8FAFC; border: 1px solid var(--neutral-200, #E2E8F0); border-radius: 10px; margin-top: 0.875rem; overflow: hidden;">
            <iframe id="surat-iframe" src="about:blank" style="width: 100%; height: 75vh; border: none;"></iframe>
        </div>
    </div>
</div>

{{-- MODAL PENETAPAN PEMBIMBING & BIDANG (TAHAP 2) --}}
<div id="modal-tetapkan-penempatan" class="modal-overlay">
    <div class="modal-container" style="max-width: 480px; border-radius: 14px; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0); display: flex; justify-content: space-between; align-items: flex-start; background: #FFFFFF;">
            <div>
                <h3 style="font-size: 1.125rem; font-weight: 800; color: #0F172A; margin: 0; letter-spacing: -0.01em;">Penetapan Penempatan</h3>
                <p style="font-size: 0.8125rem; color: #64748B; margin: 0.25rem 0 0 0; font-weight: 500;" id="tetapkan-peserta-info">
                    Peserta: -
                </p>
            </div>
            <button type="button" data-action="close-tetapkan-modal" style="background: transparent; border: none; font-size: 1.25rem; color: #94A3B8; cursor: pointer; padding: 0.25rem; line-height: 1;" title="Tutup">✕</button>
        </div>

        <form method="POST" id="form-tetapkan-penempatan" action="{{ route('admin.penempatan.tetapkan') }}" style="padding: 1.5rem;">
            @csrf
            <input type="hidden" name="pendaftaran_id" id="tetapkan-pendaftaran-id" value="">
            <input type="hidden" name="bidang_id" id="tetapkan-bidang-id" value="">

            {{-- Bidang Dinas --}}
            <div style="display: flex; flex-direction: column; gap: 0.35rem; margin-bottom: 1.25rem;">
                <label style="font-size: 0.75rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.04em;">Bidang Dinas</label>
                <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px;">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #EFF6FF; color: #0641CC; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                            <path d="M3 9h18"></path>
                            <path d="M9 21V9"></path>
                        </svg>
                    </div>
                    <div id="tetapkan-bidang-display" style="font-weight: 700; font-size: 0.875rem; color: #0F172A; line-height: 1.35;">
                        Dinas Komunikasi dan Informatika
                    </div>
                </div>
            </div>

            {{-- Pembimbing Lapangan --}}
            <div style="display: flex; flex-direction: column; gap: 0.35rem; margin-bottom: 1.5rem;">
                <label for="select-tetapkan-pembimbing" style="font-size: 0.75rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.04em;">Pembimbing Lapangan <span style="color: #DC2626;">*</span></label>
                <select name="pembimbing_id" id="select-tetapkan-pembimbing" required style="height: 42px; border: 1px solid #CBD5E1; border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #FFFFFF; color: #0F172A; width: 100%;">
                    <option value="">-- Pilih Pembimbing Dinas --</option>
                    @foreach($pembimbingList as $p)
                        <option value="{{ $p->id }}" data-bidang-id="{{ $p->bidang_id ?? '' }}">
                            {{ $p->nama }} [{{ $p->bidang?->nama_bidang ?? 'Dinas Kominfo' }}] ({{ $p->mentee_aktif_count ?? 0 }} Mentee Aktif)
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.65rem; border-top: 1px solid #E2E8F0; padding-top: 1.25rem;">
                <button type="button" data-action="close-tetapkan-modal" style="padding: 0.55rem 1.15rem; background: #FFFFFF; border: 1px solid #CBD5E1; color: #475569; border-radius: 8px; font-weight: 600; font-size: 0.8125rem; cursor: pointer; transition: all 0.15s ease;">
                    Batal
                </button>
                <button type="submit" style="padding: 0.55rem 1.35rem; background: #0641CC; color: #FFFFFF; border: none; border-radius: 8px; font-weight: 700; font-size: 0.8125rem; cursor: pointer; box-shadow: 0 1px 3px rgba(6, 65, 204, 0.25); transition: background 0.15s ease;">
                    Tetapkan Pembimbing
                </button>
            </div>
        </form>
    </div>
</div>

@vite(['resources/js/pages/admin/penempatan-index.js'])

{{-- FLOATING BULK ACTION BAR --}}
@if(!auth()->user()?->isSuperAdmin())
<div id="floating-bulk-bar" style="display: none; position: fixed; bottom: 2rem; left: 50%; transform: translateX(-50%); background: #0F172A; color: #ffffff; padding: 0.85rem 1.5rem; border-radius: 9999px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3); z-index: 1040; align-items: center; gap: 1.25rem;">
    <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem;">
        <span id="bulk-selected-count" style="background: #0641CC; color: #ffffff; padding: 0.15rem 0.55rem; border-radius: 999px; font-weight: 700; font-size: 0.8125rem;">0</span>
        <span>Peserta Dipilih</span>
    </div>
    <div style="display: flex; align-items: center; gap: 0.5rem;">
        <button type="button" data-action="open-bulk-reassign" style="padding: 0.45rem 1rem; background: #0641CC; color: #ffffff; border: none; border-radius: 999px; font-weight: 700; font-size: 0.8125rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><polyline points="16 11 18 13 22 9"></polyline></svg>
            <span>Pindahkan Pembimbing Massal</span>
        </button>
        <button type="button" data-action="cancel-bulk-selection" style="background: transparent; border: none; color: #94A3B8; font-size: 0.8125rem; cursor: pointer; padding: 0.25rem 0.5rem;">
            Batal
        </button>
    </div>
</div>
@endif

{{-- MODAL BULK REASSIGN PEMBIMBING --}}
<div id="modal-bulk-reassign" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(6px); z-index: 1060; align-items: center; justify-content: center; padding: 1rem;">
    <div style="background: #ffffff; border-radius: 14px; width: 100%; max-width: 520px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); padding: 1.75rem; border: 1px solid var(--neutral-200);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.85rem;">
            <div>
                <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-900); margin: 0;">Pindahkan Pembimbing Massal</h3>
                <p style="font-size: 0.8rem; color: var(--neutral-500); margin: 0.2rem 0 0 0;">
                    Alihkan <span id="bulk-modal-count" style="font-weight: 700; color: #0641CC;">0</span> penempatan peserta ke pembimbing baru.
                </p>
            </div>
            <button type="button" data-action="close-bulk-reassign" style="background: transparent; border: none; cursor: pointer; color: var(--neutral-500); font-size: 1.25rem;">&times;</button>
        </div>

        <form method="POST" action="{{ route('admin.penempatan.bulk-reassign') }}">
            @csrf
            <div id="bulk-ids-container"></div>

            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.4rem;">
                    Pilih Pembimbing Lapangan Baru <span style="color: #ef4444;">*</span>
                </label>
                <select name="pembimbing_id" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-300); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff;">
                    <option value="">-- Pilih Pembimbing Aktif --</option>
                    @foreach($pembimbingList as $pem)
                        <option value="{{ $pem->id }}">
                            {{ $pem->nama }} (Beban: {{ $pem->mentee_aktif_count }} mentee aktif)
                        </option>
                    @endforeach
                </select>
                <small style="display: block; font-size: 0.75rem; color: var(--neutral-500); margin-top: 0.35rem;">
                    Sistem akan memvalidasi batas kapasitas beban peserta aktif per pembimbing.
                </small>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--neutral-200); padding-top: 1rem;">
                <button type="button" data-action="close-bulk-reassign" style="padding: 0.5rem 1.25rem; background: var(--neutral-200); color: var(--neutral-700); font-weight: 600; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="padding: 0.5rem 1.5rem; background: #0641CC; color: #ffffff; font-weight: 600; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer;">
                    Konfirmasi Pindah Pembimbing
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
