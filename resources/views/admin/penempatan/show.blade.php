@extends('layouts.dashboard')

@php
    $peserta = $penempatan->peserta;
    $profil = $peserta?->profil;
    $pembimbing = $penempatan->pembimbing;
    $statusObj = $penempatan->status;
    $statusVal = $statusObj instanceof \App\Enums\StatusPenempatanEnum ? $statusObj->value : (string) $statusObj;

    $ref = request('ref');
    $tab = request('tab');
    $masukKelompok = request('masuk_kelompok');
    $masukIndividu = request('masuk_individu');

    $isFromSuratSelesai = $ref === 'surat-selesai' 
        || !empty($masukKelompok) 
        || !empty($masukIndividu);

    if ($isFromSuratSelesai) {
        if ($tab === 'kelompok' || !empty($masukKelompok) || $penempatan->isKelompok()) {
            $kelId = $masukKelompok ?: $penempatan->pendaftaran_id;
            $backUrl = route('admin.surat-selesai.index', ['tab' => 'kelompok', 'masuk_kelompok' => $kelId]);
            $backLabel = __('Kembali ke Surat Selesai Tim');
        } elseif ($tab === 'individu' || !empty($masukIndividu)) {
            $indId = $masukIndividu ?: $penempatan->id;
            $backUrl = route('admin.surat-selesai.index', ['tab' => 'individu', 'masuk_individu' => $indId]);
            $backLabel = __('Kembali ke Surat Selesai Peserta');
        } else {
            $backUrl = route('admin.surat-selesai.index');
            $backLabel = __('Kembali ke Surat Selesai');
        }
    } else {
        $backUrl = route('admin.penempatan.index');
        $backLabel = __('Kembali');
    }
@endphp

@section('title', ($isFromSuratSelesai ? __('Detail Surat Selesai: ') : __('Detail Penempatan: ')) . ($peserta?->nama ?? __('Peserta')) . ' - ' . __('Admin MagKom'))
@section('topbar_title', $isFromSuratSelesai ? __('Surat Selesai Magang') : __('Detail Penempatan Peserta'))



@section('content')
@php
    $peserta = $penempatan->peserta;
    $profil = $peserta?->profil;
    $pembimbing = $penempatan->pembimbing;
    $statusObj = $penempatan->status;
    $statusVal = $statusObj instanceof \App\Enums\StatusPenempatanEnum ? $statusObj->value : (string) $statusObj;

    $badgeStyle = match($statusVal) {
        'aktif' => ['bg' => '#ECFDF5', 'color' => '#065F46', 'border' => '#A7F3D0', 'dot' => '#10B981'],
        'selesai' => ['bg' => '#EFF6FF', 'color' => '#1E40AF', 'border' => '#BFDBFE', 'dot' => '#3B82F6'],
        'batal' => ['bg' => '#FEF2F2', 'color' => '#991B1B', 'border' => '#FECACA', 'dot' => '#EF4444'],
        default => ['bg' => '#F1F5F9', 'color' => '#475569', 'border' => '#CBD5E1', 'dot' => '#94A3B8'],
    };

    $noSuratResmi = $penempatan->no_surat ?? ('500.12/' . str_pad((string) $penempatan->id, 4, '0', STR_PAD_LEFT) . '/DISKOMINFO/' . date('Y'));

    $tugasPersen = $tugasCount > 0 ? min(100, round(($tugasSelesaiCount / $tugasCount) * 100)) : 0;
    $startDate = $penempatan->tgl_mulai ? \Carbon\Carbon::parse($penempatan->tgl_mulai) : null;
    $endDate = $penempatan->tgl_selesai ? \Carbon\Carbon::parse($penempatan->tgl_selesai) : null;
    $today = today('Asia/Jakarta');
    $timelinePersen = 0;
    if ($startDate && $endDate && $endDate->gt($startDate)) {
        if ($today->lt($startDate)) {
            $timelinePersen = 0;
        } elseif ($today->gt($endDate)) {
            $timelinePersen = 100;
        } else {
            $totalDays = $startDate->diffInDays($endDate);
            $passedDays = $startDate->diffInDays($today);
            $timelinePersen = $totalDays > 0 ? min(100, round(($passedDays / $totalDays) * 100)) : 0;
        }
    }
@endphp

    @if($isFromSuratSelesai)
        {{-- =========================================================================
             0. HERO BANNER BIRU SLEMAN NAVY (KONTEKS SURAT SELESAI MAGANG)
             Paritas Visual Persis Gambar 2 Referensi Pengguna
             ========================================================================= --}}
        <div class="adm-hero-banner" style="margin-bottom: 1.25rem;">
            <div class="adm-hero-content">
                <div class="adm-hero-text">
                    <h1 class="adm-hero-title">{{ __('Unggah Surat Selesai: :nama', ['nama' => $peserta?->nama ?? 'Peserta']) }}</h1>
                    <div class="adm-hero-subtitle">
                        @if($penempatan->isKelompok())
                            <span>{{ $penempatan->pendaftaran?->kelompokMagang?->nama_kelompok ?? __('Rombongan Tim') }}</span>
                            <span class="sep">&bull;</span>
                        @endif
                        <span>{{ __('Penerbitan Berkas Resmi') }}</span>
                        <span class="sep">&bull;</span>
                        <span>{{ __('Dinas Komunikasi dan Informatika Kabupaten Sleman') }}</span>
                    </div>
                </div>
                <div class="adm-hero-actions">
                    <a href="{{ $backUrl }}" class="adm-hero-btn-secondary" title="{{ $backLabel }}">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        <span>{{ $backLabel }}</span>
                    </a>
                </div>
            </div>
        </div>
    @else
        {{-- Header Putih Standar Penempatan --}}
        <div class="page-header-row" style="margin-bottom: 1.25rem;">
            <div>
                <h1 class="page-title-text">{{ __('Detail Penempatan Peserta') }}</h1>
                <p class="page-desc-text">{{ __('Rincian data alokasi penempatan peserta, pembimbing lapangan, rekapitulasi progres magang, dan dokumen resmi.') }}</p>
            </div>
            <div class="page-actions-wrap">
                <div class="penempatan-header-actions">
                    <a href="{{ route('admin.penempatan.index') }}" class="btn-action-back" title="{{ __('Kembali ke halaman daftar penempatan') }}">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        <span>{{ __('Kembali') }}</span>
                    </a>

                    <a href="{{ route('admin.penempatan.surat-diterima.pratinjau', $penempatan) }}" target="_blank" class="btn-action-secondary" title="{{ __('Buka pratinjau surat keputusan resmi di tab baru') }}">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="3" r="3"></circle></svg>
                        <span>{{ __('Pratinjau Surat') }}</span>
                    </a>

                    @if(auth()->user()?->isSuperAdmin())
                        <span style="display: inline-flex; align-items: center; gap: 0.4rem; background: #EFF6FF; color: #1D4ED8; font-weight: 700; font-size: 0.8125rem; padding: 0.45rem 0.85rem; border-radius: 8px; border: 1px solid #BFDBFE;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                            <span>{{ __('Mode Lihat Saja') }}</span>
                        </span>
                    @endif

                    @if($statusVal === 'aktif' && !auth()->user()?->isSuperAdmin())
                        <div class="penempatan-dropdown-wrap">
                            <button type="button" class="btn-action-dropdown-trigger" id="btnPenempatanDropdown" data-action="toggle-dropdown" title="{{ __('Opsi aksi penempatan lanjutan') }}">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                                <span>{{ __('Aksi Penempatan') }}</span>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </button>
                            <div class="penempatan-dropdown-menu" id="menuPenempatanDropdown">
                                <button type="button" class="dropdown-action-item" data-action="open-pindah-modal">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><polyline points="17 11 19 13 23 9"></polyline></svg>
                                    <span>{{ __('Pindah Pembimbing') }}</span>
                                </button>
                                <button type="button" class="dropdown-action-item is-danger" data-action="open-batal-modal">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                    <span>{{ __('Batalkan Penempatan') }}</span>
                                </button>
                            </div>
                        </div>
                    @endif

                    <a href="{{ route('admin.penempatan.surat-diterima', $penempatan) }}" class="btn-action-primary" title="{{ __('Unduh berkas resmi Surat Keterangan Diterima Magang') }}">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        <span>{{ __('Unduh Surat Diterima PDF') }}</span>
                    </a>
                </div>
            </div>
        </div>
    @endif

<div class="penempatan-detail-wrapper">

    @if($penempatan->isKelompok())
        {{-- =====================================================================
             TAMPILAN DETAIL PENEMPATAN PESERTA ANGGOTA KELOMPOK
             (Single-Participant Focus with Team Context - Tanpa Tabel Anggota)
             ===================================================================== --}}
        @include('admin.penempatan.partials._show_kelompok', [
            'penempatan' => $penempatan,
            'statusVal' => $statusVal,
        ])
    @else
        {{-- =====================================================================
             TAMPILAN DETAIL PENEMPATAN INDIVIDU EKSISTING
             ===================================================================== --}}
        {{-- 1. COMPACT 1-LINE PROFILE BANNER --}}
    <div class="penempatan-profile-card">
        <div class="penempatan-profile-main">
            {{-- Avatar Monogram / Foto --}}
            <div class="penempatan-profile-avatar">
                @if($profil?->foto_url)
                    <img src="{{ $profil->foto_url }}" alt="{{ $peserta?->nama }}">
                @else
                    {{ strtoupper(substr($peserta?->nama ?? 'P', 0, 2)) }}
                @endif
            </div>

            {{-- Identitas & Meta Chips --}}
            <div class="penempatan-profile-info">
                <div class="penempatan-profile-name-row">
                    <h2 class="penempatan-profile-name">{{ $peserta?->nama ?? '-' }}</h2>

                    {{-- Dynamic Status Badge --}}
                    <span class="status-badge-dot" style="background: {{ $badgeStyle['bg'] }}; color: {{ $badgeStyle['color'] }}; border: 1px solid {{ $badgeStyle['border'] }};">
                        <span class="status-dot-indicator" style="background: {{ $badgeStyle['dot'] }};"></span>
                        <span>{{ $statusObj ? __($statusObj->label()) : __(ucfirst($statusVal)) }}</span>
                    </span>

                    {{-- Registration Track Badge --}}
                    @if($penempatan->slotKhusus)
                        <span class="jalur-badge-pill" style="background: #FEF3C7; color: #B45309; border: 1px solid #FCD34D;">
                            {{ __('Jalur Khusus') }}: {{ $penempatan->slotKhusus->nama_mitra }}
                        </span>
                    @else
                        <span class="jalur-badge-pill" style="background: #F1F5F9; color: #475569; border: 1px solid #CBD5E1;">
                            {{ __('Jalur Reguler') }}
                        </span>
                    @endif

                    {{-- Kelompok & Role Badge --}}
                    @if($penempatan->isKelompok())
                        @php
                            $kelompokData = $penempatan->pendaftaran?->kelompokMagang;
                            $isKetua = $kelompokData && (int)$kelompokData->ketua_user_id === (int)$peserta?->id;
                        @endphp
                        <span class="kelompok-badge-pill">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                            <span>{{ $kelompokData?->nama_kelompok ?? __('Rombongan Kelompok') }}</span>
                        </span>
                        <span class="role-badge-pill {{ $isKetua ? 'ketua' : 'anggota' }}">
                            {{ $isKetua ? __('Ketua Kelompok') : __('Anggota Tim') }}
                        </span>
                    @endif
                </div>

                {{-- Horizontal Meta Chips (NIM, Institusi, Jurusan) --}}
                <div class="penempatan-profile-meta-row">
                    <span class="meta-chip-item">
                        <span>{{ __('NIM/NISN') }}:</span>
                        <strong>{{ $profil?->nim_nisn ?? '-' }}</strong>
                    </span>
                    <span class="meta-chip-item">
                        <span>{{ __('Institusi') }}:</span>
                        <strong>{{ $profil?->institusi ?? '-' }}</strong>
                    </span>
                    <span class="meta-chip-item">
                        <span>{{ __('Jurusan') }}:</span>
                        <strong>{{ $penempatan->kuota?->jurusan?->nama_jurusan ?? ($profil?->jurusan?->nama_jurusan ?? '-') }}</strong>
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. MINIMAL METRICS SUMMARY (4 STAT CARDS) --}}
    <div class="penempatan-metrics-grid">
        {{-- Stat 1: Tugas Selesai --}}
        <div class="metric-tile-card">
            <div class="metric-tile-top">
                <span class="metric-tile-title">{{ __('Tugas Selesai') }}</span>
                <div class="metric-tile-icon-box" style="background: #EFF6FF; color: #2563EB;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M9 11l3 3L22 4"></path><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                </div>
            </div>
            <div class="metric-tile-value">{{ $tugasSelesaiCount }} / {{ $tugasCount }}</div>
            <div class="metric-progress-track">
                <div class="metric-progress-fill" style="width: {{ $tugasPersen }}%; background: #2563EB;"></div>
            </div>
            <div class="metric-tile-sub">
                <span>{{ $tugasCount > 0 ? $tugasPersen . '% ' . __('tugas magang tuntas') : __('Belum ada tugas diberikan') }}</span>
            </div>
        </div>

        {{-- Stat 2: Total Logbook --}}
        <div class="metric-tile-card">
            <div class="metric-tile-top">
                <span class="metric-tile-title">{{ __('Total Logbook') }}</span>
                <div class="metric-tile-icon-box" style="background: #ECFDF5; color: #059669;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                </div>
            </div>
            <div class="metric-tile-value">{{ $logbookCount }} {{ __('Minggu') }}</div>
            <div class="metric-tile-sub">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span>{{ __('Jurnal aktivitas terverifikasi') }}</span>
            </div>
        </div>

        {{-- Stat 3: Status Magang --}}
        <div class="metric-tile-card">
            <div class="metric-tile-top">
                <span class="metric-tile-title">{{ __('Status Magang') }}</span>
                <div class="metric-tile-icon-box" style="background: #ECFDF5; color: #059669;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
                </div>
            </div>
            <div class="metric-tile-value" style="font-size: 1.125rem;">
                {{ __($penempatan->status->label()) }}
            </div>
            <div class="metric-tile-sub">
                <span style="font-weight: 700; color: {{ $penempatan->status === \App\Enums\StatusPenempatanEnum::Selesai ? '#059669' : '#4F46E5' }};">
                    {{ $penempatan->status === \App\Enums\StatusPenempatanEnum::Selesai ? __('Program Magang Tuntas') : __('Sedang Bimbingan Aktif') }}
                </span>
            </div>
        </div>

        {{-- Stat 4: Durasi Magang --}}
        <div class="metric-tile-card">
            <div class="metric-tile-top">
                <span class="metric-tile-title">{{ __('Durasi Magang') }}</span>
                <div class="metric-tile-icon-box" style="background: #EEF2FF; color: #4F46E5;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                </div>
            </div>
            <div class="metric-tile-value">{{ $penempatan->total_minggu }} {{ __('Minggu') }}</div>
            <div class="metric-tile-sub">
                <span>{{ __('Total periode kedinasan') }}</span>
            </div>
        </div>
    </div>

    {{-- 3. 12-COLUMN MAIN CONTENT GRID (8-COL KIRI : 4-COL KANAN) --}}
    <div class="penempatan-grid-12">

        {{-- ==================== KOLOM UTAMA (8-COL, ~66.67%) ==================== --}}
        <div class="penempatan-col-8">
            <div class="penempatan-section-card">
                <div class="section-card-header">
                    <div class="section-card-title-group">
                        <h3 class="section-card-title">{{ __('Informasi Penempatan & Surat Keputusan') }}</h3>
                        <p class="section-card-subtitle">{{ __('Rincian alokasi kedinasan dan nomor surat resmi') }}</p>
                    </div>
                </div>

                {{-- Banner Surat Keputusan Kedinasan --}}
                <div class="sk-official-banner">
                    <div>
                        <div class="sk-official-label">{{ __('Nomor Surat Keputusan Kedinasan') }}</div>
                        <div class="sk-official-number">{{ $noSuratResmi }}</div>
                    </div>
                    <div class="sk-official-badge">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                        <span>{{ __('Resmi Diskominfo Sleman') }}</span>
                    </div>
                </div>

                {{-- Flat 2x2 Placement Details Grid (Tanpa Redundansi Jurusan/Durasi/Jalur) --}}
                <div class="placement-details-grid">
                    {{-- 1. Bidang Penempatan --}}
                    <div class="detail-field-card">
                        <div class="detail-field-icon-box">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"></rect><path d="M3 9h18"></path><path d="M9 21V9"></path></svg>
                        </div>
                        <div class="detail-field-content">
                            <span class="detail-field-label">{{ __('Bidang Penempatan') }}</span>
                            <span class="detail-field-value">{{ $penempatan->bidang?->nama_bidang ?? __('Dinas Kominfo Sleman') }}</span>
                        </div>
                    </div>

                    {{-- 2. Tanggal Mulai Magang --}}
                    <div class="detail-field-card">
                        <div class="detail-field-icon-box">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        </div>
                        <div class="detail-field-content">
                            <span class="detail-field-label">{{ __('Tanggal Mulai Magang') }}</span>
                            <span class="detail-field-value">{{ $startDate ? $startDate->translatedFormat('d F Y') : '-' }}</span>
                        </div>
                    </div>

                    {{-- 3. Tanggal Selesai Magang --}}
                    <div class="detail-field-card">
                        <div class="detail-field-icon-box">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                        </div>
                        <div class="detail-field-content">
                            <span class="detail-field-label">{{ __('Tanggal Selesai Magang') }}</span>
                            <span class="detail-field-value">{{ $endDate ? $endDate->translatedFormat('d F Y') : '-' }}</span>
                        </div>
                    </div>

                    {{-- 4. Status Akun Sistem Peserta --}}
                    <div class="detail-field-card">
                        <div class="detail-field-icon-box">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        </div>
                        <div class="detail-field-content">
                            <span class="detail-field-label">{{ __('Status Akun Login') }}</span>
                            <span class="detail-field-value" style="color: {{ $peserta?->status_aktif ? '#059669' : '#DC2626' }};">
                                {{ $peserta?->status_aktif ? __('Aktif (Dapat Login)') : __('Nonaktif') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Linear Milestone Timeline Track --}}
                <div class="timeline-card-body">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 0.8125rem; font-weight: 700; color: #1E293B;">{{ __('Rentang Masa Pelaksanaan') }}</span>
                        <span style="font-size: 0.775rem; font-weight: 700; color: #2563EB;">{{ $penempatan->total_minggu }} {{ __('Minggu Periode') }}</span>
                    </div>
                    <div class="timeline-bar-wrap">
                        <div class="timeline-bar-line">
                            <div class="timeline-bar-progress" style="width: {{ $timelinePersen }}%;"></div>
                        </div>
                        <div class="timeline-points-row">
                            <div class="timeline-point-item">
                                <div class="timeline-dot"></div>
                                <span class="timeline-point-label">{{ __('Mulai') }}</span>
                                <span class="timeline-point-date">{{ $startDate ? $startDate->translatedFormat('d M Y') : '-' }}</span>
                            </div>
                            <div class="timeline-point-item">
                                <div class="timeline-dot {{ $timelinePersen > 0 && $timelinePersen < 100 ? 'is-active' : '' }}"></div>
                                <span class="timeline-point-label">{{ $timelinePersen >= 100 ? __('Tuntas') : __('Berjalan') }}</span>
                                <span class="timeline-point-date">{{ $timelinePersen }}%</span>
                            </div>
                            <div class="timeline-point-item">
                                <div class="timeline-dot is-end"></div>
                                <span class="timeline-point-label">{{ __('Selesai') }}</span>
                                <span class="timeline-point-date">{{ $endDate ? $endDate->translatedFormat('d M Y') : '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card Khusus Kelompok: Informasi Rombongan Kelompok & Rekan Tim --}}
            @if($penempatan->isKelompok())
                @php
                    $rekanList = $penempatan->getRekanKelompokPenempatan();
                    $kelompokData = $penempatan->pendaftaran?->kelompokMagang;
                @endphp
                <div class="penempatan-section-card team-rekan-card">
                    <div class="section-card-header">
                        <div class="section-card-title-group">
                            <div class="team-header-title-row">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#003566" stroke-width="2.5">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                                <h3 class="section-card-title">{{ __('Informasi Rombongan Kelompok & Rekan Tim') }}</h3>
                            </div>
                            <p class="section-card-subtitle">
                                <strong>{{ $kelompokData?->nama_kelompok }}</strong> &bull; {{ $kelompokData?->institusi ?? '-' }}
                            </p>
                        </div>
                        <span class="team-total-chip">
                            {{ $rekanList->count() }} {{ __('Total Anggota') }}
                        </span>
                    </div>

                    <div class="team-rekan-list">
                        @foreach($rekanList as $rekan)
                            @php
                                $rekanUser = $rekan->peserta;
                                $isCurrent = (int)$rekan->id === (int)$penempatan->id;
                                $isKetuaRekan = $kelompokData && (int)$kelompokData->ketua_user_id === (int)$rekanUser?->id;
                                $fotoRekan = $rekanUser?->profil?->foto_profil;
                                $avatarRekanUrl = $fotoRekan && \Illuminate\Support\Facades\Storage::disk('public')->exists($fotoRekan)
                                    ? asset('storage/'.$fotoRekan)
                                    : null;
                            @endphp
                            <div class="team-rekan-item {{ $isCurrent ? 'is-current' : '' }}">
                                <div class="team-rekan-left">
                                    <div class="rekan-avatar-circle" aria-hidden="true">
                                        @if($avatarRekanUrl)
                                            <img src="{{ $avatarRekanUrl }}" alt="{{ $rekanUser?->nama }}" class="avatar-img">
                                        @else
                                            <span>{{ strtoupper(substr($rekanUser?->nama ?? 'A', 0, 2)) }}</span>
                                        @endif
                                    </div>
                                    <div class="team-rekan-info">
                                        <div class="team-rekan-name">
                                            {{ $rekanUser?->nama ?? __('Anggota Tim') }}
                                            @if($isCurrent)
                                                <span class="current-indicator-badge">({{ __('Sedang Dilihat') }})</span>
                                            @endif
                                        </div>
                                        <div class="team-rekan-meta">
                                            <span>{{ $rekanUser?->profil?->nim_nisn ?? '-' }}</span>
                                            <span class="sep">&bull;</span>
                                            <span class="rekan-role-text {{ $isKetuaRekan ? 'ketua' : 'anggota' }}">
                                                {{ $isKetuaRekan ? __('Ketua Kelompok') : __('Anggota Tim') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="team-rekan-right">
                                    @if(!$isCurrent)
                                        <a href="{{ route('admin.penempatan.show', $rekan) }}" class="btn-rekan-link" title="{{ __('Lihat Detail Penempatan Rekan') }}">
                                            <span>{{ __('Lihat Detail') }}</span>
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                        </a>
                                    @else
                                        <span class="active-member-pill">{{ __('Halaman Ini') }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- ==================== KOLOM SIDEBAR (4-COL, ~33.33%) ==================== --}}
        <div class="penempatan-col-4">
            {{-- Panel Pembimbing Lapangan --}}
            <div class="penempatan-section-card">
                <div class="section-card-header">
                    <div class="section-card-title-group">
                        <h3 class="section-card-title">{{ __('Pembimbing Lapangan') }}</h3>
                        <p class="section-card-subtitle">{{ __('Staf pembimbing teknis kedinasan') }}</p>
                    </div>
                </div>

                @if($pembimbing)
                    <div class="mentor-profile-card">
                        <div class="mentor-avatar">
                            {{ strtoupper(substr($pembimbing->nama, 0, 2)) }}
                        </div>
                        <div style="min-width: 0;">
                            <div class="mentor-info-name">{{ $pembimbing->nama }}</div>
                            <div class="mentor-info-email">{{ $pembimbing->email }}</div>
                        </div>
                    </div>

                    <div class="mentor-stat-row">
                        <div>
                            <div class="field-label">{{ __('NIP Pembimbing') }}</div>
                            <div class="field-value" style="font-family: ui-monospace, monospace; font-size: 0.8125rem;">
                                {{ $pembimbing->nip ?? '-' }}
                            </div>
                        </div>
                        <div>
                            <div class="field-label">{{ __('Beban Mentee') }}</div>
                            <div>
                                <span class="mentor-capacity-badge">
                                    {{ $pembimbing->mentee_aktif_count ?? 0 }} {{ __('Mentee Aktif') }}
                                </span>
                            </div>
                        </div>
                    </div>
                @else
                    <div style="text-align: center; padding: 1.5rem 1rem; color: #64748B;">
                        <p style="margin: 0; font-size: 0.8125rem;">{{ __('Belum ada pembimbing lapangan yang ditugaskan.') }}</p>
                    </div>
                @endif
            </div>

            {{-- Panel Berkas & Dokumen Magang --}}
            <div class="penempatan-section-card">
                <div class="section-card-header">
                    <div class="section-card-title-group">
                        <h3 class="section-card-title">{{ __('Berkas & Dokumen Magang') }}</h3>
                        <p class="section-card-subtitle">{{ __('Arsip dokumen persyaratan & surat resmi') }}</p>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    {{-- Surat Keterangan Diterima Magang --}}
                    <div class="doc-pdf-card">
                        <div class="doc-pdf-info">
                            <div class="doc-pdf-icon-box">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                            </div>
                            <div style="min-width: 0;">
                                <div class="doc-pdf-title">{{ __('Surat Diterima Magang') }}</div>
                                <div class="doc-pdf-sub">{{ __('PDF Resmi Diskominfo') }}</div>
                            </div>
                        </div>
                        <div class="doc-pdf-actions">
                            <a href="{{ route('admin.penempatan.surat-diterima.pratinjau', $penempatan) }}" target="_blank" class="btn-doc-action btn-doc-view" title="{{ __('Pratinjau berkas') }}">
                                {{ __('Lihat') }}
                            </a>
                            <a href="{{ route('admin.penempatan.surat-diterima', $penempatan) }}" class="btn-doc-action btn-doc-download" title="{{ __('Unduh berkas') }}">
                                {{ __('Unduh') }}
                            </a>
                        </div>
                    </div>

                    {{-- Surat Keterangan Selesai Magang Individual (Kebutuhan 4 PRD MagKom) --}}
                    <div style="border-top: 1px dashed #CBD5E1; padding-top: 0.875rem; margin-top: 0.5rem;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                            <div style="font-size: 0.825rem; font-weight: 800; color: #003566; text-transform: uppercase;">
                                {{ __('Surat Selesai Magang') }}
                            </div>
                            @if($penempatan->file_surat_selesai)
                                <span style="font-size: 0.7rem; font-weight: 800; padding: 2px 8px; border-radius: 9999px; background: #ECFDF5; color: #047857; border: 1px solid #A7F3D0;">
                                    {{ __('Tersedia') }}
                                </span>
                            @else
                                <span style="font-size: 0.7rem; font-weight: 800; padding: 2px 8px; border-radius: 9999px; background: #F1F5F9; color: #64748B;">
                                    {{ __('Belum Ada') }}
                                </span>
                            @endif
                        </div>

                        @if($penempatan->file_surat_selesai)
                            <div class="doc-pdf-card" style="margin-bottom: 8px;">
                                <div class="doc-pdf-info">
                                    <div class="doc-pdf-icon-box" style="background: #ECFDF5; color: #059669;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><polyline points="9 15 12 18 15 15"></polyline></svg>
                                    </div>
                                    <div style="min-width: 0;">
                                        <div class="doc-pdf-title" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ __('Surat Keterangan Selesai') }}</div>
                                        <div class="doc-pdf-sub">{{ $penempatan->tgl_upload_surat_selesai?->translatedFormat('d M Y H:i') ?? __('Dokumen Resmi') }}</div>
                                    </div>
                                </div>
                                <div class="doc-pdf-actions">
                                    <a href="{{ route('admin.penempatan.surat-selesai.unduh', $penempatan) }}" class="btn-doc-action btn-doc-download" title="{{ __('Unduh Surat Selesai') }}">
                                        {{ __('Unduh') }}
                                    </a>
                                </div>
                            </div>
                        @endif

                        @if($statusVal === 'selesai' && !auth()->user()?->isSuperAdmin())
                            <form method="POST" action="{{ route('admin.penempatan.surat-selesai.upload', $penempatan) }}" enctype="multipart/form-data" class="penempatan-upload-form">
                                @csrf
                                <div class="form-upload-group">
                                    <label class="form-upload-label">
                                        {{ $penempatan->file_surat_selesai ? __('Perbarui Berkas Surat (PDF, maks 2MB):') : __('Unggah Surat Selesai (PDF, maks 2MB):') }}
                                    </label>

                                    @if($penempatan->isKelompok())
                                        @php
                                            $rekanList = $penempatan->getRekanKelompokPenempatan();
                                            $namaKelompok = $penempatan->pendaftaran?->kelompokMagang?->nama_kelompok ?? __('Kelompok Magang');
                                        @endphp
                                        <div class="team-upload-notice">
                                            <div class="notice-title">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                                <span>{{ __('Pendaftaran Kelompok: :nama (:count Orang)', ['nama' => $namaKelompok, 'count' => $rekanList->count()]) }}</span>
                                            </div>
                                            <p class="notice-desc">
                                                {{ __('Surat selesai yang diunggah akan otomatis didistribusikan ke seluruh dashboard anggota kelompok ini.') }}
                                            </p>
                                        </div>
                                    @endif

                                    <div class="file-picker-row">
                                        <label for="inputSuratSelesai" class="btn-picker-trigger">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path>
                                            </svg>
                                            <span>{{ __('Pilih PDF') }}</span>
                                        </label>
                                        <input type="file" name="file_surat" id="inputSuratSelesai" accept="application/pdf" required class="visually-hidden-file-input">
                                        <div class="file-picker-preview" id="previewInputSuratSelesai">
                                            <span class="preview-text-placeholder">{{ __('Belum ada file dipilih') }}</span>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn-submit-upload-surat">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                            <polyline points="17 8 12 3 7 8"></polyline>
                                            <line x1="12" y1="3" x2="12" y2="15"></line>
                                        </svg>
                                        <span>{{ $penempatan->file_surat_selesai ? __('Ganti Berkas Surat') : ($penempatan->isKelompok() ? __('Unggah Surat Tim (1x untuk Semua)') : __('Unggah Surat Selesai')) }}</span>
                                    </button>
                                </div>
                            </form>
                        @elseif($statusVal !== 'selesai')
                            <p class="notice-non-selesai">
                                <em>{{ __('Surat selesai magang dapat diunggah setelah status peserta dinyatakan selesai magang.') }}</em>
                            </p>
                        @endif
                    </div>

                    {{-- Berkas Pendaftaran jika ada --}}
                    @if($penempatan->pendaftaran && $penempatan->pendaftaran->dokumen->isNotEmpty())
                        @foreach($penempatan->pendaftaran->dokumen as $dok)
                            <div class="verified-doc-item">
                                <span class="verified-doc-name">{{ ucwords(str_replace('_', ' ', $dok->jenis_dokumen)) }}</span>
                                <x-gentelella.badge variant="success">
                                    {{ __('Terverifikasi') }}
                                </x-gentelella.badge>
                            </div>
                        @endforeach
                    @else
                        <div style="font-size: 0.775rem; color: #64748B; text-align: center; padding: 0.5rem;">
                            {{ __('Tidak ada berkas unggahan tambahan.') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
    @endif

</div>

{{-- MODAL PINDAH PEMBIMBING --}}
<div id="modal-pindah-pembimbing" class="modal-overlay">
    <div class="modal-container" style="max-width: 480px;">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-300, #E2E8F0); display: flex; justify-content: space-between; align-items: center; background: var(--neutral-100, #F8FAFC);">
            <div>
                <h3 style="font-size: 1.0625rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0;">{{ __('Alihkan Pembimbing Lapangan') }}</h3>
                <p style="font-size: 0.75rem; color: var(--neutral-500, #64748B); margin: 0.15rem 0 0 0;">
                    {{ __('Pindahkan alokasi peserta ke staf pembimbing lainnya') }}
                </p>
            </div>
            <button type="button" data-action="close-pindah-modal" style="background: transparent; border: none; font-size: 1.25rem; color: var(--neutral-400, #94A3B8); cursor: pointer;">✕</button>
        </div>

        <form method="POST" action="{{ route('admin.penempatan.pindah-pembimbing', $penempatan) }}" style="padding: 1.5rem;">
            @csrf
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-900, #1E293B); margin-bottom: 0.35rem;">
                    {{ __('Pilih Pembimbing Baru') }} <span style="color: #DC2626;">*</span>
                </label>
                <select name="pembimbing_id" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                    <option value="">-- {{ __('Pilih Pembimbing') }} --</option>
                    @foreach($pembimbingList as $pemb)
                        <option value="{{ $pemb->id }}" {{ $pemb->id === $penempatan->pembimbing_id ? 'selected' : '' }}>
                            {{ $pemb->nama }} ({{ __('Saat ini') }}: {{ $pemb->mentee_aktif_count }} {{ __('mentee') }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                <button type="button" data-action="close-pindah-modal" style="padding: 0.6rem 1rem; background: var(--neutral-100, #F1F5F9); color: var(--neutral-700, #334155); border: 1px solid var(--neutral-300, #E2E8F0); border-radius: 8px; font-weight: 600; font-size: 0.8125rem; cursor: pointer;">
                    {{ __('Batal') }}
                </button>
                <button type="submit" style="padding: 0.6rem 1.25rem; background: #0641CC; color: #FFFFFF; border: none; border-radius: 8px; font-weight: 700; font-size: 0.8125rem; cursor: pointer;">
                    {{ __('Simpan Perubahan') }}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL BATALKAN PENEMPATAN --}}
<div id="modal-batalkan-penempatan" class="modal-overlay">
    <div class="modal-container" style="max-width: 480px;">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-300, #E2E8F0); display: flex; justify-content: space-between; align-items: center; background: #FEF2F2;">
            <div>
                <h3 style="font-size: 1.0625rem; font-weight: 800; color: #991B1B; margin: 0;">{{ __('Batalkan Penempatan Peserta') }}</h3>
                <p style="font-size: 0.75rem; color: #B91C1C; margin: 0.15rem 0 0 0;">
                    {{ __('Catat pembatalan atau pengunduran diri peserta magang') }}
                </p>
            </div>
            <button type="button" data-action="close-batal-modal" style="background: transparent; border: none; font-size: 1.25rem; color: #991B1B; cursor: pointer;">✕</button>
        </div>

        <form method="POST" action="{{ route('admin.penempatan.batalkan', $penempatan) }}" style="padding: 1.5rem;">
            @csrf
            @php
                $hariIni = today('Asia/Jakarta');
                $tglMulaiCarbon = \Carbon\Carbon::parse($penempatan->tgl_mulai);
                $sebelumMulai = $hariIni->lt($tglMulaiCarbon);
            @endphp
            <div style="margin-bottom: 1rem; padding: 0.75rem 1rem; border-radius: 8px; font-size: 0.8125rem; {{ $sebelumMulai ? 'background: var(--blue-tint, #EFF6FF); color: #1E40AF; border: 1px solid var(--blue-tint-line, #BFDBFE);' : 'background: #FFFBEB; color: #92400E; border: 1px solid #FCD34D;' }}">
                @if($sebelumMulai)
                    <strong>{{ __('Informasi (Sebelum Pelaksanaan):') }}</strong> {{ __('Periode magang belum dimulai. Kuota akan dikembalikan ke jurusan dan akun login peserta tetap aktif.') }}
                @else
                    <strong>{{ __('Peringatan (Pengunduran Diri):') }}</strong> {{ __('Periode magang telah berjalan. Kuota TIDAK dikembalikan ke jurusan dan akun login peserta akan dinonaktifkan secara permanen.') }}
                @endif
            </div>
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-900, #1E293B); margin-bottom: 0.35rem;">
                    {{ __('Alasan Pembatalan / Pengunduran Diri') }} <span style="color: #DC2626;">*</span>
                </label>
                <textarea name="alasan" rows="4" required minlength="10" placeholder="{{ __('Tuliskan alasan resmi pembatalan magang peserta (minimal 10 karakter)...') }}" style="width: 100%; border: 1px solid var(--neutral-400, #CBD5E1); border-radius: 8px; padding: 0.75rem; font-size: 0.875rem; font-family: inherit;"></textarea>
                <span style="font-size: 0.75rem; color: var(--neutral-500, #64748B);">{{ __('Alasan akan dicatat secara permanen di audit log sistem.') }}</span>
            </div>

            <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                <button type="button" data-action="close-batal-modal" style="padding: 0.6rem 1rem; background: var(--neutral-100, #F1F5F9); color: var(--neutral-700, #334155); border: 1px solid var(--neutral-300, #E2E8F0); border-radius: 8px; font-weight: 600; font-size: 0.8125rem; cursor: pointer;">
                    {{ __('Kembali') }}
                </button>
                <button type="submit" style="padding: 0.6rem 1.25rem; background: #DC2626; color: #FFFFFF; border: none; border-radius: 8px; font-weight: 700; font-size: 0.8125rem; cursor: pointer;">
                    {{ __('Konfirmasi Pembatalan') }}
                </button>
            </div>
        </form>
    </div>
</div>

@vite(['resources/css/pages/admin/surat-selesai-index.css', 'resources/js/pages/admin/penempatan-show.js'])
@endsection
