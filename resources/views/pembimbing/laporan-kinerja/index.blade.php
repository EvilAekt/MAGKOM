@extends('layouts.dashboard')

@section('title', 'Laporan Peserta - Portal Pembimbing')
@section('topbar_title', 'Laporan Peserta')
@section('content_area_class', 'gentelella-fluid-canvas')

@push('styles')
    @vite(['resources/css/pages/pembimbing/laporan-kinerja-index.css'])
@endpush

@push('scripts')
    @vite(['resources/js/pages/pembimbing/laporan-kinerja-index.js'])
@endpush

@section('content')
<div class="classroom-container laporan-kinerja-container">

    {{-- 1. Classroom Hero Banner Khas Sleman MagKom (Gambar 2 Parity) --}}
    @php
        $authUser = auth()->user();
        $bidangNama = $authUser->bidang?->nama_bidang ?? 'Dinas Komunikasi dan Informatika Sleman';
    @endphp

    <div class="classroom-banner laporan-hero-banner sc-reveal-hero" data-scroll-reveal>
        <div class="banner-top-row">
            <div>
                <h1 class="banner-title">Laporan Peserta</h1>
                <div class="banner-subtitle">
                    <span>Bidang: <strong>{{ $bidangNama }}</strong></span>
                    <span>&bull;</span>
                    <span>Pembimbing: <strong>{{ $authUser->nama }}</strong></span>
                    <span>&bull;</span>
                    <span><strong>{{ $countAktif }} Mentee Aktif Bimbingan</strong></span>
                </div>
            </div>
            <div>
                {{-- Dropdown Wrap & Popover Ekspor di dalam Card Banner (Gambar 2 & 5) --}}
                <div id="admExportDropdownWrap" class="adm-export-dropdown-wrap">
                    <button type="button" 
                            id="btnToggleExportPopover" 
                            class="btn-export-rekap-banner"
                            aria-expanded="false" 
                            aria-haspopup="menu" 
                            aria-controls="admExportPopover"
                            title="Pilih dan unduh rekap data peserta (Excel, CSV, PDF)">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        <span>Ekspor Rekap</span>
                        <svg id="admExportCaretSvg" class="adm-export-chevron" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>

                    {{-- Floating Popover Card dengan class anti-clipping .adm-export-popover-right --}}
                    <div id="admExportPopover" 
                         class="adm-export-popover adm-export-popover-right" 
                         style="display: none;" 
                         role="menu" 
                         aria-label="Menu Pilihan Format Ekspor"
                         data-export-url="{{ route('pembimbing.laporan-kinerja.export') }}"
                         data-print-url="{{ route('pembimbing.laporan-kinerja.cetak') }}">
                        <div class="adm-popover-pointer adm-pointer-right"></div>
                        <div class="adm-popover-header">
                            <div>
                                <div class="adm-popover-title">Ekspor Rekap Cepat</div>
                                <div class="adm-popover-desc">Data hasil filter aktif</div>
                            </div>
                            <span class="adm-popover-row-count tabular-nums">
                                {{ number_format($totalMentee) }} Baris
                            </span>
                        </div>

                <div class="adm-popover-formats" role="radiogroup" aria-label="Format Berkas">
                    {{-- Format 1: Excel (.xlsx) - Rekomendasi --}}
                    <label class="adm-format-card adm-format-selected" id="cardFormatXlsx">
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
                    <label class="adm-format-card" id="cardFormatCsv">
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
                    <label class="adm-format-card" id="cardFormatPdf">
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
                    <button type="button" id="btnCancelExportPopover" class="adm-btn-popover-cancel">
                        Batal
                    </button>
                    <button type="button" id="btnDoExportDownload" class="adm-btn-popover-download">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right: 0.35rem;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        <span>Unduh Excel (.xlsx)</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

    {{-- 2. Tile Stats MagKom Gentelella (4 Metrik Selaras Gambar 2) --}}
    <div class="tiles-grid sc-reveal-up" data-scroll-reveal>
        <x-gentelella.tile-stat
            title="TOTAL MENTEE"
            value="{{ $totalMentee }}"
            color="teal"
            trendDirection="up"
            trendText="{{ $totalMentee }} Total"
            subText="Seluruh riwayat penempatan"
            :sparkBars="[35, 45, 55, 65, 75, 82, 88, 92, 100]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <x-gentelella.tile-stat
            title="PESERTA AKTIF"
            value="{{ $countAktif }}"
            color="blue"
            trendType="info"
            trendDirection="up"
            trendText="{{ $persenAktif }}%"
            subText="Sedang menjalankan magang"
            :sparkBars="[40, 50, 65, 55, 70, 60, 80, 75, 70]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <x-gentelella.tile-stat
            title="PERLU PERHATIAN"
            value="{{ $countPerhatian }}"
            color="yellow"
            trendType="warning"
            trendDirection="down"
            trendText="{{ $countPerhatian }} Alert"
            subText="Tugas lewat tenggat / belum tuntas"
            :sparkBars="[70, 85, 90, 65, 50, 60, 45, 30, 20]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <x-gentelella.tile-stat
            title="ALUMNI SELESAI"
            value="{{ $countSelesai }}"
            color="teal"
            trendType="success"
            trendDirection="up"
            trendText="{{ $persenSelesai }}%"
            subText="Magang tuntas & lulus"
            :sparkBars="[15, 25, 40, 50, 65, 75, 85, 95, 100]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>
    </div>

    {{-- 2.5 Filter Bar: Pencarian Teks, Nama Peserta, Status, dan Bulan --}}
    <div class="laporan-filter-card sc-reveal-up sc-delay-1" data-scroll-reveal>
        <form method="GET" action="{{ route('pembimbing.laporan-kinerja.index') }}" class="laporan-filter-form" id="formFilterLaporan">
            <div class="filter-group filter-group-search" style="flex: 1.3;">
                <label for="inputSearchLaporan">Pencarian Peserta</label>
                <div class="peserta-search-input-wrap">
                    <input type="text" 
                           name="q" 
                           id="inputSearchLaporan" 
                           value="{{ request('q') }}" 
                           placeholder="Cari nama, kampus, NIM..." 
                           class="peserta-search-input">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="peserta-search-icon" aria-hidden="true">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </div>
            </div>

            <div class="filter-group">
                <label for="filterPeserta">Filter Nama Peserta</label>
                <select name="penempatan_id" id="filterPeserta" class="filter-control" onchange="this.form.submit()">
                    <option value="">Semua Peserta Bimbingan</option>
                    @foreach($penempatanOptions ?? [] as $pen)
                        <option value="{{ $pen->id }}" {{ request('penempatan_id') == $pen->id ? 'selected' : '' }}>
                            {{ $pen->peserta?->nama }} ({{ $pen->peserta?->profil?->institusi ?? 'Institusi' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label for="filterStatus">Filter Status</label>
                <select name="status" id="filterStatus" class="filter-control" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="tidak_aktif" {{ request('status') === 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif / Selesai</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="filterBulan">Filter Bulan</label>
                <select name="bulan" id="filterBulan" class="filter-control" onchange="this.form.submit()">
                    <option value="">Semua Bulan</option>
                    @foreach($daftarBulan ?? [] as $val => $label)
                        <option value="{{ $val }}" {{ request('bulan') == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-filter-apply">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                    <span>Filter</span>
                </button>
                @if(request()->anyFilled(['q', 'penempatan_id', 'status', 'bulan']))
                    <a href="{{ route('pembimbing.laporan-kinerja.index') }}" class="btn-filter-reset">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- 3. Redesign Tabel Portofolio Bimbingan (Data Penempatan Peserta) --}}
    <x-gentelella.x-panel title="Data Penempatan Peserta">
        @if($rekapMentees->isEmpty())
            <div class="adm-table-empty" style="padding: 3.5rem 1.5rem; text-align: center; color: var(--neutral-600, #64748B);">
                <div style="width: 56px; height: 56px; background: #EFF6FF; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto; color: #1D4ED8;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
                <h4 style="font-size: 1.05rem; font-weight: 700; color: var(--neutral-900, #0F172A); margin-bottom: 0.25rem;">
                    Belum Ada Data Penempatan
                </h4>
                <p style="font-size: 0.85rem; max-width: 440px; margin: 0 auto;">Belum ada catatan rekapitulasi peserta bimbingan yang terdaftar.</p>
            </div>
        @else
            <div class="adm-table-container sc-reveal-up sc-delay-2" data-scroll-reveal>
                <div style="overflow-x: auto; width: 100%;">
                    <table class="adm-table admin-table">
                        <thead>
                            <tr>
                                <th>Nama Mentee</th>
                                <th>Institusi &amp; Jurusan</th>
                                <th>Bidang Magang</th>
                                <th style="text-align: center;">Tugas Selesai</th>
                                <th style="text-align: center;">Logbook</th>
                                <th style="text-align: center;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rekapMentees as $item)
                                @php
                                    $p = $item['penempatan'];
                                    $cleanNama = str_replace([' (Peserta Magang)', ' (Pembimbing Lapangan)'], '', (string) ($p->peserta?->nama ?? '-'));
                                    $initials = collect(explode(' ', $cleanNama))->filter()->map(fn($w) => mb_substr($w, 0, 1))->take(1)->implode('');
                                    if (empty($initials)) {
                                        $initials = 'P';
                                    }
                                    $statusVal = is_object($p->status) ? $p->status->value : $p->status;
                                    $statusClass = match($statusVal) {
                                        'aktif' => 'status-pill-aktif',
                                        'selesai' => 'status-pill-selesai',
                                        'nonaktif' => 'status-pill-nonaktif',
                                        default => 'status-pill-default',
                                    };
                                    $statusLabel = match($statusVal) {
                                        'aktif' => 'Aktif',
                                        'selesai' => 'Selesai',
                                        'nonaktif' => 'Nonaktif',
                                        'mengundurkan_diri' => 'Mengundurkan Diri',
                                        default => ucfirst((string) $statusVal),
                                    };
                                @endphp
                                <tr>
                                    {{-- 1. Nama Mentee dengan Avatar Box Inisial sesuai Gambar 2 --}}
                                    <td>
                                        <div class="mentee-table-cell">
                                            <div class="mentee-avatar-square" aria-hidden="true">
                                                {{ strtoupper($initials) }}
                                            </div>
                                            <div class="mentee-info-col">
                                                <div class="mentee-info-name">
                                                    {{ $cleanNama }}
                                                </div>
                                                <div class="mentee-info-nim">
                                                    NIM/NISN: {{ $p->peserta?->profil?->nim_nisn ?? '-' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- 2. Institusi & Jurusan --}}
                                    <td>
                                        <div class="institusi-col-text">
                                            {{ $p->peserta?->profil?->institusi ?? '-' }}
                                        </div>
                                        <div class="jurusan-subtext">
                                            {{ $p->peserta?->profil?->jurusan?->nama_jurusan ?? '-' }}
                                        </div>
                                    </td>

                                    {{-- 3. Bidang Magang --}}
                                    <td>
                                        <div class="bidang-text">
                                            {{ $p->bidang?->nama_bidang ?? 'Umum' }}
                                        </div>
                                    </td>

                                    {{-- 4. Tugas Selesai --}}
                                    <td style="text-align: center;">
                                        <span class="ratio-highlight-tugas">{{ $item['tugas_selesai'] }}</span>
                                        <span class="ratio-subtext">/ {{ $item['total_tugas'] }}</span>
                                    </td>

                                    {{-- 5. Logbook --}}
                                    <td style="text-align: center;">
                                        <span class="ratio-highlight-logbook">{{ $item['logbook_reviewed'] }}</span>
                                        <span class="ratio-subtext">/ {{ $item['total_logbook'] }}</span>
                                    </td>

                                    {{-- 6. Status --}}
                                    <td style="text-align: center;">
                                        <span class="status-pill-badge {{ $statusClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Footer Paginasi Terstandarisasi Selaras Gambar 2 --}}
                <div class="adm-table-footer">
                    <x-gentelella.pagination-bar 
                        :paginator="$rekapMentees" 
                        :perPageOptions="[5, 10, 25]" 
                        entity="peserta magang"
                        badgeText="Penempatan Aktif"
                    />
                </div>
            </div>
        @endif
    </x-gentelella.x-panel>
</div>
@endsection
