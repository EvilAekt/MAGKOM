@extends('layouts.dashboard')

@section('title', __('Surat Selesai Magang - Administrasi MagKom'))
@section('topbar_title', __('Surat Selesai Magang'))

@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}">{{ __('Administrasi') }}</a>
    <span class="breadcrumb-separator">/</span>
    @if(!empty($masukPenempatan))
        <a href="{{ route('admin.surat-selesai.index', ['tab' => 'individu']) }}">{{ __('Surat Selesai Magang') }}</a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">{{ $masukPenempatan->peserta?->nama ?? __('Detail Peserta') }}</span>
    @elseif(!empty($masukPendaftaran))
        <a href="{{ route('admin.surat-selesai.index', ['tab' => 'kelompok']) }}">{{ __('Surat Selesai Magang') }}</a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">{{ $masukPendaftaran->kelompokMagang?->nama_kelompok ?? __('Detail Kelompok') }}</span>
    @else
        <span class="breadcrumb-current">{{ __('Surat Selesai Magang') }}</span>
    @endif
@endsection

@push('styles')
    @vite(['resources/css/pages/admin/surat-selesai-index.css'])
@endpush

@push('scripts')
    @vite(['resources/js/pages/admin/surat-selesai-index.js'])
@endpush

@section('content')
<div class="sa-fluid-container" style="display: flex; flex-direction: column; width: 100%;">

    {{-- =========================================================================
         0. SLEMAN NAVY HERO BANNER
         ========================================================================= --}}
    <div class="adm-hero-banner">
        <div class="adm-hero-content">
            <div class="adm-hero-text">
                @if(!empty($masukPenempatan))
                    <h1 class="adm-hero-title">{{ __('Unggah Surat Selesai: :nama', ['nama' => $masukPenempatan->peserta?->nama ?? 'Peserta']) }}</h1>
                    <div class="adm-hero-subtitle">
                        <span>{{ __('Workspace Penerbitan Berkas Resmi Peserta Individu') }}</span>
                        <span class="sep">&bull;</span>
                        <span>{{ __('Dinas Komunikasi dan Informatika Kabupaten Sleman') }}</span>
                    </div>
                @elseif(!empty($masukPendaftaran))
                    <h1 class="adm-hero-title">{{ __('Unggah Surat Selesai: :nama', ['nama' => $masukPendaftaran->kelompokMagang?->nama_kelompok ?? 'Rombongan Kelompok']) }}</h1>
                    <div class="adm-hero-subtitle">
                        <span>{{ __('Workspace Penerbitan Berkas Resmi Rombongan Kelompok') }}</span>
                        <span class="sep">&bull;</span>
                        <span>{{ __('Dinas Komunikasi dan Informatika Kabupaten Sleman') }}</span>
                    </div>
                @else
                    <h1 class="adm-hero-title">{{ __('Daftar Surat Selesai Magang') }}</h1>
                    <div class="adm-hero-subtitle">
                        <span>{{ __('Penerbitan & Distribusi Berkas Resmi Peserta') }}</span>
                        <span class="sep">&bull;</span>
                        <span>{{ __('Dinas Komunikasi dan Informatika Kabupaten Sleman') }}</span>
                    </div>
                @endif
            </div>
            <div class="adm-hero-actions">
                @if(!empty($masukPenempatan))
                    <a href="{{ route('admin.surat-selesai.index', request()->except('masuk_individu')) }}" class="adm-hero-btn-secondary">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                        <span>{{ __('Kembali ke Daftar Peserta') }}</span>
                    </a>
                @elseif(!empty($masukPendaftaran))
                    <a href="{{ route('admin.surat-selesai.index', request()->except('masuk_kelompok')) }}" class="adm-hero-btn-secondary">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                        <span>{{ __('Kembali ke Daftar Kelompok') }}</span>
                    </a>
                @else
                    <a href="{{ route('admin.penempatan.index') }}" class="adm-hero-btn-secondary">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                        </svg>
                        <span>{{ __('Data Penempatan') }}</span>
                    </a>
                    <a href="{{ route('admin.arsip.index') }}" class="adm-hero-btn-secondary">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="21 8 21 21 3 21 3 8"></polyline>
                            <rect x="1" y="3" width="22" height="5"></rect>
                            <line x1="10" y1="12" x2="14" y2="12"></line>
                        </svg>
                        <span>{{ __('Arsip Alumni') }}</span>
                    </a>
                @endif

                @if(auth()->user()?->isSuperAdmin())
                    <span style="display: inline-flex; align-items: center; gap: 0.4rem; background: rgba(255, 255, 255, 0.15); color: #FFFFFF; font-weight: 700; font-size: 0.8125rem; padding: 0.6rem 1rem; border-radius: 10px; border: 1px solid rgba(255, 255, 255, 0.25); backdrop-filter: blur(4px);">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                        <span>{{ __('Mode Lihat Saja (Read-Only)') }}</span>
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- =========================================================================
         1. SEGMENTED PILL TABS TOOLBAR (Hanya tampil saat di mode daftar utama)
         ========================================================================= --}}
    @if(empty($masukPenempatan) && empty($masukPendaftaran))
        <div class="month-switch-strip kuota-unified-toolbar">
            <div class="kuota-nav-tabs" role="tablist" aria-label="{{ __('Kategori Surat Selesai Magang') }}">
                
                {{-- Tab 1: Peserta Individu --}}
                <a href="{{ route('admin.surat-selesai.index', ['tab' => 'individu']) }}" 
                   class="kuota-tab-btn {{ $activeTab === 'individu' ? 'active' : '' }}" 
                   role="tab" 
                   aria-selected="{{ $activeTab === 'individu' ? 'true' : 'false' }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <span>{{ __('Peserta Individu') }}</span>
                    <span class="kuota-tab-badge">{{ $totalIndividu }}</span>
                </a>

                {{-- Tab 2: Rombongan Kelompok --}}
                <a href="{{ route('admin.surat-selesai.index', ['tab' => 'kelompok']) }}" 
                   class="kuota-tab-btn {{ $activeTab === 'kelompok' ? 'active' : '' }}" 
                   role="tab" 
                   aria-selected="{{ $activeTab === 'kelompok' ? 'true' : 'false' }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <span>{{ __('Rombongan Kelompok') }}</span>
                    <span class="kuota-tab-badge">{{ $totalKelompok }}</span>
                </a>

            </div>
        </div>
    @endif

    @if($activeTab === 'individu')
        {{-- =========================================================================
             2. PANEL TAB: PESERTA INDIVIDU
             ========================================================================= --}}
        
        @if(!empty($masukPenempatan))
            {{-- ---------------------------------------------------------------------
                 2.B TIER 2: FOCUSED WORKSPACE UNGGAH SURAT INDIVIDU (KLIK DULU BARU MASUK)
                 --------------------------------------------------------------------- --}}
            @php
                $peserta = $masukPenempatan->peserta;
                $profil = $peserta?->profil;
                $hasSurat = ! empty($masukPenempatan->file_surat_selesai) && \Illuminate\Support\Facades\Storage::disk('local')->exists($masukPenempatan->file_surat_selesai);
                $fotoProfil = $profil?->foto_profil;
                $avatarUrl = $fotoProfil && \Illuminate\Support\Facades\Storage::disk('public')->exists($fotoProfil)
                    ? asset('storage/'.$fotoProfil)
                    : null;
                $initials = strtoupper(substr($peserta?->nama ?? 'P', 0, 2));
            @endphp

            <div class="focused-workspace-wrapper">
                
                {{-- Baris Identitas Utama Peserta --}}
                <div class="focused-identity-bar">
                    <div class="focused-identity-left">
                        <div class="mentee-avatar-circle" aria-hidden="true">
                            @if($avatarUrl)
                                <img src="{{ $avatarUrl }}" alt="{{ $peserta?->nama }}" class="avatar-img">
                            @else
                                <span>{{ $initials }}</span>
                            @endif
                        </div>
                        <div class="focused-identity-text">
                            <h2 class="focused-identity-name">{{ $peserta?->nama ?? __('Peserta Magang') }}</h2>
                            <p class="focused-identity-sub">
                                {{ $profil?->institusi ?? '-' }} &bull; {{ __('NIM/NISN:') }} {{ $profil?->nim_nisn ?? '-' }} &bull; {{ $profil?->jurusan?->nama_jurusan ?? '-' }}
                            </p>
                        </div>
                    </div>

                    <div class="focused-identity-right">
                        @if($hasSurat)
                            <span class="status-badge-chip success">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <span>{{ __('SURAT SUDAH TERBIT') }}</span>
                            </span>
                        @else
                            <span class="status-badge-chip warning">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                <span>{{ __('BELUM DIUNGGAH') }}</span>
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Grid Konten Terfokus: Info Penempatan & Area Form Unggah --}}
                <div class="focused-content-grid">
                    
                    {{-- Kolom Kiri: Ringkasan Informasi Penempatan --}}
                    <div class="focused-card info-card">
                        <h3 class="focused-card-title">{{ __('Informasi Penempatan Magang') }}</h3>
                        <div class="info-list">
                            <div class="info-item">
                                <span class="info-label">{{ __('Bidang Penempatan') }}</span>
                                <span class="info-value">{{ $masukPenempatan->bidang?->nama_bidang ?? '-' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">{{ __('Pembimbing Lapangan') }}</span>
                                <span class="info-value">{{ $masukPenempatan->pembimbing?->nama ?? '-' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">{{ __('Periode Magang') }}</span>
                                <span class="info-value">{{ $masukPenempatan->tgl_mulai?->translatedFormat('d M Y') ?? '-' }} s/d {{ $masukPenempatan->tgl_selesai?->translatedFormat('d M Y') ?? '-' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">{{ __('Tanggal Selesai') }}</span>
                                <span class="info-value">{{ $masukPenempatan->tgl_selesai?->translatedFormat('d F Y') ?? '-' }}</span>
                            </div>
                        </div>

                        {{-- Status Terbit & Tombol Unduh --}}
                        <div class="published-status-box {{ $hasSurat ? 'is-published' : 'is-pending' }}">
                            <div class="published-box-header">
                                <h4 class="published-box-title">{{ __('Status Surat Selesai Magang') }}</h4>
                                <span class="published-status-text">
                                    {{ $hasSurat ? __('Tersedia di Dashboard Peserta') : __('Menunggu Pengunggahan Berkas') }}
                                </span>
                            </div>
                            @if($hasSurat)
                                <div class="published-meta">
                                    <span>{{ __('Diunggah pada:') }} {{ $masukPenempatan->tgl_upload_surat_selesai?->translatedFormat('d F Y, H:i') ?? '-' }} WIB</span>
                                    <a href="{{ route('admin.penempatan.surat-selesai.unduh', $masukPenempatan) }}" class="btn-download-primary">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                        <span>{{ __('Unduh Surat Resmi (PDF)') }}</span>
                                    </a>
                                </div>
                            @else
                                <p class="published-note">
                                    {{ __('Peserta belum menerima Surat Keterangan Selesai Magang. Silakan unggah berkas bertanda tangan resmi pada formulir di samping.') }}
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Kolom Kanan: Card Unggah Berkas Terdedikasi (Kompak & To-The-Point) --}}
                    @if(!auth()->user()?->isSuperAdmin())
                        <div class="focused-card upload-card">
                            <div class="upload-card-header">
                                <h3 class="focused-card-title">
                                    {{ $hasSurat ? __('Perbarui / Ganti Berkas Surat') : __('Formulir Unggah Surat Selesai') }}
                                </h3>
                                <p class="upload-card-desc">
                                    {{ __('Unggah berkas resmi format PDF (Maks. 2.0 MB).') }}
                                </p>
                            </div>

                            <form method="POST" action="{{ route('admin.penempatan.surat-selesai.upload', $masukPenempatan) }}" enctype="multipart/form-data" class="dedicated-upload-form">
                                @csrf
                                
                                {{-- Area Dropzone: Seluruh Card Merupakan Label yang Dapat Diklik Langsung --}}
                                <label for="file_surat_ind" class="upload-dropzone-panel" tabindex="0" role="button" aria-label="{{ __('Pilih atau seret berkas PDF surat selesai magang') }}">
                                    <div class="dropzone-icon-circle" aria-hidden="true">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                            <polyline points="17 8 12 3 7 8"></polyline>
                                            <line x1="12" y1="3" x2="12" y2="15"></line>
                                        </svg>
                                    </div>
                                    <h4 class="dropzone-prompt-title">{{ __('Pilih Berkas PDF Surat Selesai') }}</h4>
                                    <p class="dropzone-prompt-desc">{{ __('Klik di mana saja atau seret berkas .PDF (Maks. 2.0 MB)') }}</p>

                                    <input type="file" name="file_surat" id="file_surat_ind" accept="application/pdf" required class="visually-hidden-file-input">

                                    {{-- Chip nama file dinamis: hanya muncul saat file dipilih --}}
                                    <div class="dynamic-file-chip" id="previewFileNameInd"></div>
                                </label>

                                <div class="upload-footer-wrap">
                                    <button type="submit" class="btn-compact-submit" style="width: 100%; justify-content: center; padding: 10px 20px; font-size: 0.85rem;">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                            <polyline points="17 8 12 3 7 8"></polyline>
                                            <line x1="12" y1="3" x2="12" y2="15"></line>
                                        </svg>
                                        <span>{{ $hasSurat ? __('Simpan Perubahan Berkas') : __('Unggah Surat Resmi') }}</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        {{-- Mode Pengawasan SuperAdmin (Read-Only) --}}
                        <div class="focused-card upload-card" style="border-top: 3.5px solid var(--blue-600);">
                            <div class="upload-card-header">
                                <div style="display: inline-flex; align-items: center; gap: 0.4rem; color: var(--blue-600); font-weight: 700; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 0.35rem;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                                    <span>{{ __('Mode Pengawasan SuperAdmin (Read-Only)') }}</span>
                                </div>
                                <h3 class="focused-card-title">
                                    {{ __('Informasi Penerbitan Berkas') }}
                                </h3>
                                <p class="upload-card-desc">
                                    {{ __('SuperAdmin memiliki hak akses peninjauan dan audit. Pengunggahan berkas resmi merupakan wewenang Administrator Operasional.') }}
                                </p>
                            </div>
                            <div style="background: var(--neutral-50, #F8FAFC); border: 1px dashed var(--neutral-300, #CBD5E1); border-radius: 10px; padding: 1.5rem; text-align: center;">
                                <div style="width: 44px; height: 44px; border-radius: 50%; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem auto;">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                </div>
                                <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-800); margin: 0 0 0.35rem 0;">{{ __('Akses Unggah Dibatasi') }}</h4>
                                <p style="font-size: 0.8125rem; color: var(--neutral-500); margin: 0; line-height: 1.5;">
                                    {{ $hasSurat ? __('Berkas resmi telah diterbitkan oleh Administrator. Anda dapat mengunduh salinan berkas pada panel informasi peserta.') : __('Peserta belum memiliki berkas surat keterangan selesai. Administrator akan mengunggah berkas bertanda tangan resmi.') }}
                                </p>
                            </div>
                        </div>
                    @endif

                </div>

            </div>

        @else
            {{-- ---------------------------------------------------------------------
                 2.A TIER 1: DAFTAR KARTU PESERTA INDIVIDU (ALA GAMBAR 3)
                 --------------------------------------------------------------------- --}}
            
            {{-- Toolbar Filter Pencarian Individu --}}
            <div class="surat-filter-card">
                <form method="GET" action="{{ route('admin.surat-selesai.index') }}" class="surat-filter-form">
                    <input type="hidden" name="tab" value="individu">

                    <div class="filter-field-search">
                        <label for="filter_q_ind" class="filter-label">
                            {{ __('Cari Peserta Individu') }}
                        </label>
                        <input type="text" name="q" id="filter_q_ind" value="{{ request('q') }}" placeholder="{{ __('Ketik nama peserta, NIM/NISN, atau institusi...') }}" class="filter-input-text">
                    </div>

                    <div class="filter-field-status">
                        <label for="filter_status_surat_ind" class="filter-label">
                            {{ __('Status Berkas Surat') }}
                        </label>
                        <select name="status_surat" id="filter_status_surat_ind" class="filter-select">
                            <option value="">{{ __('Semua Status') }}</option>
                            <option value="sudah" {{ request('status_surat') === 'sudah' ? 'selected' : '' }}>{{ __('Sudah Diterbitkan') }}</option>
                            <option value="belum" {{ request('status_surat') === 'belum' ? 'selected' : '' }}>{{ __('Belum Diunggah') }}</option>
                        </select>
                    </div>

                    <div class="filter-field-actions">
                        <button type="submit" class="btn-filter-submit">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                            <span>{{ __('Cari') }}</span>
                        </button>
                        @if(request()->hasAny(['q', 'status_surat']))
                            <a href="{{ route('admin.surat-selesai.index', ['tab' => 'individu']) }}" class="btn-filter-reset">
                                <span>{{ __('Reset') }}</span>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Container Daftar Kartu Master Peserta Individu --}}
            <div class="surat-panel-container">
                <div class="panel-header-bar">
                    <div>
                        <h3 class="panel-title">{{ __('Daftar Peserta Individu Selesai Magang') }}</h3>
                        <p class="panel-desc">{{ __('Klik baris peserta untuk masuk ke halaman unggah dan kelola surat keterangan selesai magang.') }}</p>
                    </div>
                    <span class="panel-counter-chip">
                        {{ $items->total() }} {{ __('Peserta Terdata') }}
                    </span>
                </div>

                <div class="surat-stream-container">
                    @forelse($items as $item)
                        @php
                            $hasSurat = ! empty($item->file_surat_selesai) && \Illuminate\Support\Facades\Storage::disk('local')->exists($item->file_surat_selesai);
                            $fotoProfil = $item->peserta?->profil?->foto_profil;
                            $avatarUrl = $fotoProfil && \Illuminate\Support\Facades\Storage::disk('public')->exists($fotoProfil)
                                ? asset('storage/'.$fotoProfil)
                                : null;
                            $initials = strtoupper(substr($item->peserta?->nama ?? 'P', 0, 2));
                            $masukUrl = route('admin.surat-selesai.index', array_merge(request()->query(), ['tab' => 'individu', 'masuk_individu' => $item->id]));
                        @endphp

                        <div class="surat-master-card" data-card-id="{{ $item->id }}">
                            <a href="{{ $masukUrl }}" class="surat-card-link" title="{{ __('Masuk ke halaman unggah surat :nama', ['nama' => $item->peserta?->nama]) }}">
                                
                                {{-- Kiri: Avatar & Info Peserta --}}
                                <div class="card-left-section">
                                    <div class="mentee-avatar-circle" aria-hidden="true">
                                        @if($avatarUrl)
                                            <img src="{{ $avatarUrl }}" alt="{{ $item->peserta?->nama }}" class="avatar-img">
                                        @else
                                            <span>{{ $initials }}</span>
                                        @endif
                                    </div>
                                    <div class="card-title-meta">
                                        <h4 class="card-primary-name">{{ $item->peserta?->nama ?? __('Peserta Magang') }}</h4>
                                        <p class="card-sub-inst">
                                            {{ $item->peserta?->profil?->institusi ?? '-' }} &bull; {{ __('NIM/NISN:') }} {{ $item->peserta?->profil?->nim_nisn ?? '-' }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Tengah: Bidang & Pembimbing --}}
                                <div class="card-mid-section">
                                    <div class="card-bidang-name">{{ $item->bidang?->nama_bidang ?? '-' }}</div>
                                    <div class="card-pembimbing-text">{{ __('Pembimbing:') }} {{ $item->pembimbing?->nama ?? '-' }}</div>
                                </div>

                                {{-- Kanan: Tanggal, Status Chip & Ikon Chevron Masuk --}}
                                <div class="card-right-section">
                                    <div class="card-date-meta">
                                        <span class="card-date-label">{{ __('Selesai:') }}</span>
                                        <span class="card-date-value">{{ $item->tgl_selesai?->translatedFormat('d M Y') ?? '-' }}</span>
                                    </div>

                                    <div class="card-status-wrapper">
                                        @if($hasSurat)
                                            <span class="status-badge-chip success">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                <span>{{ __('SUDAH TERBIT') }}</span>
                                            </span>
                                        @else
                                            <span class="status-badge-chip warning">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                                <span>{{ __('BELUM DIUNGGAH') }}</span>
                                            </span>
                                        @endif
                                    </div>

                                    <div class="card-chevron-box" aria-hidden="true">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="9 18 15 12 9 6"></polyline>
                                        </svg>
                                    </div>
                                </div>

                            </a>
                        </div>
                    @empty
                        <div class="empty-state-box">
                            <div class="empty-state-icon">
                                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                            </div>
                            <h4 class="empty-state-title">{{ __('Tidak Ada Data Peserta Individu Selesai Magang') }}</h4>
                            <p class="empty-state-text">{{ __('Peserta perorangan yang telah menyelesaikan masa bimbingan magang akan otomatis muncul di sini.') }}</p>
                        </div>
                    @endforelse
                </div>

                @if($items->hasPages())
                    <div class="panel-pagination-wrapper">
                        {{ $items->links() }}
                    </div>
                @endif
            </div>

        @endif

    @else
        {{-- =========================================================================
             3. PANEL TAB: ROMBONGAN KELOMPOK
             ========================================================================= --}}
        
        @if(!empty($masukPendaftaran))
            {{-- ---------------------------------------------------------------------
                 3.B TIER 2: FOCUSED WORKSPACE UNGGAH TIM & TABEL ANGGOTA MANDIRI
                 --------------------------------------------------------------------- --}}
            @php
                $kelompok = $masukPendaftaran->kelompokMagang;
                $penempatans = $masukPendaftaran->penempatans;
                $primaryPenempatan = $penempatans->first();
                $hasSuratTim = $penempatans->contains(fn($p) => ! empty($p->file_surat_selesai) && \Illuminate\Support\Facades\Storage::disk('local')->exists($p->file_surat_selesai));
                $totalAnggotaTim = $penempatans->count();
            @endphp

            <div class="focused-workspace-wrapper">
                
                {{-- Baris Identitas Rombongan Kelompok --}}
                <div class="focused-identity-bar">
                    <div class="focused-identity-left">
                        <div class="group-icon-box" aria-hidden="true">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                        <div class="focused-identity-text">
                            <h2 class="focused-identity-name">{{ $kelompok?->nama_kelompok ?? __('Kelompok Magang') }}</h2>
                            <p class="focused-identity-sub">
                                {{ $kelompok?->institusi ?? '-' }} &bull; <strong style="color: #1D4ED8;">{{ $totalAnggotaTim }} {{ __('Anggota Terdaftar') }}</strong> &bull; {{ __('Bidang:') }} {{ $primaryPenempatan?->bidang?->nama_bidang ?? '-' }}
                            </p>
                        </div>
                    </div>

                    <div class="focused-identity-right">
                        @if($hasSuratTim)
                            <span class="status-badge-chip success">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <span>{{ __('SURAT TIM TERBIT') }}</span>
                            </span>
                        @else
                            <span class="status-badge-chip warning">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                <span>{{ __('BELUM DIUNGGAH') }}</span>
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Grid Konten Terfokus: Info Penempatan Kelompok & Area Form Unggah (Parity Grid 2-Kolom Ala Individu) --}}
                <div class="focused-content-grid">
                    
                    {{-- Kolom Kiri: Ringkasan Informasi Penempatan Kelompok (Persis Gaya Gambar 2) --}}
                    <div class="focused-card info-card">
                        <h3 class="focused-card-title">{{ __('Informasi Penempatan Kelompok') }}</h3>
                        <div class="info-list">
                            <div class="info-item">
                                <span class="info-label">{{ __('Nama Kelompok') }}</span>
                                <span class="info-value">{{ $kelompok?->nama_kelompok ?? '-' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">{{ __('Bidang Penempatan') }}</span>
                                <span class="info-value">{{ $primaryPenempatan?->bidang?->nama_bidang ?? '-' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">{{ __('Pembimbing Lapangan') }}</span>
                                <span class="info-value">{{ $primaryPenempatan?->pembimbing?->nama ?? '-' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">{{ __('Periode Magang') }}</span>
                                <span class="info-value">{{ $primaryPenempatan?->tgl_mulai?->translatedFormat('d M Y') ?? '-' }} s/d {{ $primaryPenempatan?->tgl_selesai?->translatedFormat('d M Y') ?? '-' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">{{ __('Tanggal Selesai') }}</span>
                                <span class="info-value">{{ $primaryPenempatan?->tgl_selesai?->translatedFormat('d F Y') ?? '-' }}</span>
                            </div>
                        </div>

                        {{-- Status Terbit & Unduh Surat Tim --}}
                        <div class="published-status-box {{ $hasSuratTim ? 'is-published' : 'is-pending' }}">
                            <div class="published-box-header">
                                <h4 class="published-box-title">{{ __('Status Surat Selesai Tim') }}</h4>
                                <span class="published-status-text">
                                    {{ $hasSuratTim ? __('Tersedia di Semua Dashboard Anggota') : __('Menunggu Pengunggahan Berkas') }}
                                </span>
                            </div>
                            @if($hasSuratTim && $primaryPenempatan)
                                <div class="published-meta">
                                    <span>{{ __('Diunggah pada:') }} {{ $primaryPenempatan->tgl_upload_surat_selesai?->translatedFormat('d F Y, H:i') ?? '-' }} WIB</span>
                                    <a href="{{ route('admin.penempatan.surat-selesai.unduh', $primaryPenempatan) }}" class="btn-download-primary">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                        <span>{{ __('Unduh Salinan Surat Tim (PDF)') }}</span>
                                    </a>
                                </div>
                            @else
                                <p class="published-note">
                                    {{ __('Surat keterangan selesai magang tim belum diunggah. 1x unggah di formulir di samping akan otomatis didistribusikan ke seluruh anggota.') }}
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Kolom Kanan: Card Formulir Unggah Surat Selesai Tim (Persis Format Gambar 3) --}}
                    @if(!auth()->user()?->isSuperAdmin())
                        <div class="focused-card upload-card">
                            <div class="upload-card-header">
                                <h3 class="focused-card-title">
                                    {{ $hasSuratTim ? __('Perbarui / Ganti Berkas Surat') : __('Formulir Unggah Surat Selesai') }}
                                </h3>
                                <p class="upload-card-desc">
                                    {{ __('Unggah berkas resmi format PDF (Maks. 2.0 MB).') }}
                                </p>
                            </div>

                            @if($primaryPenempatan)
                                <form method="POST" action="{{ route('admin.penempatan.surat-selesai.upload', $primaryPenempatan) }}" enctype="multipart/form-data" class="dedicated-upload-form">
                                    @csrf
                                    
                                    {{-- Area Dropzone: Seluruh Card Merupakan Label yang Dapat Diklik Langsung --}}
                                    <label for="file_surat_kel" class="upload-dropzone-panel" tabindex="0" role="button" aria-label="{{ __('Pilih atau seret berkas PDF surat selesai tim') }}">
                                        <div class="dropzone-icon-circle" aria-hidden="true">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                <polyline points="17 8 12 3 7 8"></polyline>
                                                <line x1="12" y1="3" x2="12" y2="15"></line>
                                            </svg>
                                        </div>
                                        <h4 class="dropzone-prompt-title">{{ __('Pilih Berkas PDF Surat Selesai Tim') }}</h4>
                                        <p class="dropzone-prompt-desc">{{ __('Klik di mana saja atau seret berkas .PDF (Maks. 2.0 MB)') }}</p>

                                        <input type="file" name="file_surat" id="file_surat_kel" accept="application/pdf" required class="visually-hidden-file-input">

                                        {{-- Chip nama file dinamis: hanya muncul saat file dipilih --}}
                                        <div class="dynamic-file-chip" id="previewFileNameKel"></div>
                                    </label>

                                    <div class="upload-footer-wrap">
                                        <button type="submit" class="btn-compact-submit" style="width: 100%; justify-content: center; padding: 10px 20px; font-size: 0.85rem;">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                <polyline points="17 8 12 3 7 8"></polyline>
                                                <line x1="12" y1="3" x2="12" y2="15"></line>
                                            </svg>
                                            <span>{{ $hasSuratTim ? __('Simpan Perubahan Berkas') : __('Unggah Surat Resmi') }}</span>
                                        </button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    @else
                        {{-- Mode Pengawasan SuperAdmin (Read-Only) Kelompok --}}
                        <div class="focused-card upload-card" style="border-top: 3.5px solid var(--blue-600);">
                            <div class="upload-card-header">
                                <div style="display: inline-flex; align-items: center; gap: 0.4rem; color: var(--blue-600); font-weight: 700; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 0.35rem;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                                    <span>{{ __('Mode Pengawasan SuperAdmin (Read-Only)') }}</span>
                                </div>
                                <h3 class="focused-card-title">
                                    {{ __('Informasi Penerbitan Berkas Tim') }}
                                </h3>
                                <p class="upload-card-desc">
                                    {{ __('SuperAdmin memiliki hak akses peninjauan dan audit. Pengunggahan berkas kolektif merupakan wewenang Administrator Operasional.') }}
                                </p>
                            </div>
                            <div style="background: var(--neutral-50, #F8FAFC); border: 1px dashed var(--neutral-300, #CBD5E1); border-radius: 10px; padding: 1.5rem; text-align: center;">
                                <div style="width: 44px; height: 44px; border-radius: 50%; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem auto;">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                </div>
                                <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-800); margin: 0 0 0.35rem 0;">{{ __('Akses Unggah Dibatasi') }}</h4>
                                <p style="font-size: 0.8125rem; color: var(--neutral-500); margin: 0; line-height: 1.5;">
                                    {{ $hasSuratTim ? __('Berkas resmi kelompok telah diterbitkan oleh Administrator. Anda dapat mengunduh salinan berkas pada panel informasi tim di samping.') : __('Kelompok belum memiliki berkas surat keterangan selesai. Administrator akan mengunggah berkas kolektif resmi.') }}
                                </p>
                            </div>
                        </div>
                    @endif

                </div>

                {{-- =====================================================================
                     TABEL KHUSUS ANGGOTA KELOMPOK (MANDIRI & TERISOLASI)
                     Sama sekali tidak bergabung dengan tabel kelompok lain!
                     ===================================================================== --}}
                <div class="dedicated-members-panel">
                    <div class="panel-header-bar">
                        <div>
                            <h3 class="panel-title">
                                {{ __('Daftar Anggota Kelompok: :nama (:count Orang)', ['nama' => $kelompok?->nama_kelompok ?? 'Tim', 'count' => $totalAnggotaTim]) }}
                            </h3>
                            <p class="panel-desc">
                                {{ __('Tabel khusus rincian anggota kelompok ini — surat selesai yang diunggah di atas otomatis tersinkronisasi ke masing-masing dashboard akun di bawah.') }}
                            </p>
                        </div>
                    </div>

                    <div style="overflow-x: auto;">
                        <table class="dedicated-group-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px; text-align: center;">{{ __('No') }}</th>
                                    <th>{{ __('Nama Anggota') }}</th>
                                    <th>{{ __('Peran Tim') }}</th>
                                    <th>{{ __('NIM / Institusi') }}</th>
                                    <th>{{ __('Status Surat Selesai') }}</th>
                                    <th style="text-align: right;">{{ __('Aksi Individual') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($penempatans as $idxMember => $penempatanAnggota)
                                    @php
                                        $userAnggota = $penempatanAnggota->peserta;
                                        $isKetua = $kelompok && (int) $kelompok->ketua_user_id === (int) $userAnggota?->id;
                                        $fotoAnggota = $userAnggota?->profil?->foto_profil;
                                        $avatarAnggotaUrl = $fotoAnggota && \Illuminate\Support\Facades\Storage::disk('public')->exists($fotoAnggota)
                                            ? asset('storage/'.$fotoAnggota)
                                            : null;
                                        $hasSuratAnggota = ! empty($penempatanAnggota->file_surat_selesai) && \Illuminate\Support\Facades\Storage::disk('local')->exists($penempatanAnggota->file_surat_selesai);
                                    @endphp
                                    <tr>
                                        <td style="text-align: center; font-weight: 700; color: #64748B;">
                                            {{ $idxMember + 1 }}
                                        </td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 10px;">
                                                <div class="mentee-avatar-circle small" aria-hidden="true">
                                                    @if($avatarAnggotaUrl)
                                                        <img src="{{ $avatarAnggotaUrl }}" alt="{{ $userAnggota?->nama }}" class="avatar-img">
                                                    @else
                                                        <span>{{ strtoupper(substr($userAnggota?->nama ?? 'A', 0, 2)) }}</span>
                                                    @endif
                                                </div>
                                                <div>
                                                    <a href="{{ route('admin.penempatan.show', [$penempatanAnggota, 'ref' => 'surat-selesai', 'tab' => 'kelompok', 'masuk_kelompok' => $masukPendaftaran->id]) }}" class="member-name-link">
                                                        {{ $userAnggota?->nama ?? __('Anggota Tim') }}
                                                    </a>
                                                    <div class="member-sub-email">{{ $userAnggota?->email ?? '-' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($isKetua)
                                                <span class="role-badge ketua">{{ __('Ketua Kelompok') }}</span>
                                            @else
                                                <span class="role-badge anggota">{{ __('Anggota') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="member-nim-text">{{ $userAnggota?->profil?->nim_nisn ?? '-' }}</div>
                                            <div class="member-inst-text">{{ $userAnggota?->profil?->institusi ?? '-' }}</div>
                                        </td>
                                        <td>
                                            @if($hasSuratAnggota)
                                                <span class="status-badge-chip success small">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                    <span>{{ __('Tersinkronisasi') }}</span>
                                                </span>
                                            @else
                                                <span class="status-badge-chip warning small">
                                                    <span>{{ __('Belum Diterbitkan') }}</span>
                                                </span>
                                            @endif
                                        </td>
                                        <td style="text-align: right;">
                                            <div style="display: flex; align-items: center; justify-content: flex-end; gap: 6px;">
                                                @if($hasSuratAnggota)
                                                    <a href="{{ route('admin.penempatan.surat-selesai.unduh', $penempatanAnggota) }}" 
                                                       class="btn-action-small download" 
                                                       title="{{ __('Unduh Surat Selesai untuk :nama', ['nama' => $userAnggota?->nama]) }}">
                                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                                        <span>{{ __('Unduh') }}</span>
                                                    </a>
                                                @endif
                                                <a href="{{ route('admin.penempatan.show', [$penempatanAnggota, 'ref' => 'surat-selesai', 'tab' => 'kelompok', 'masuk_kelompok' => $masukPendaftaran->id]) }}" 
                                                   class="btn-action-small detail" 
                                                   title="{{ __('Lihat Detail Penempatan :nama', ['nama' => $userAnggota?->nama]) }}">
                                                    <span>{{ __('Detail') }}</span>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        @else
            {{-- ---------------------------------------------------------------------
                 3.A TIER 1: DAFTAR KARTU ROMBONGAN KELOMPOK (ALA GAMBAR 3)
                 (Tanpa form upload sempit di baris & tanpa accordion dropdown inline)
                 --------------------------------------------------------------------- --}}
            
            {{-- Toolbar Filter Pencarian Kelompok --}}
            <div class="surat-filter-card">
                <form method="GET" action="{{ route('admin.surat-selesai.index') }}" class="surat-filter-form">
                    <input type="hidden" name="tab" value="kelompok">

                    <div class="filter-field-search">
                        <label for="filter_q_kel" class="filter-label">
                            {{ __('Cari Rombongan Kelompok') }}
                        </label>
                        <input type="text" name="q" id="filter_q_kel" value="{{ request('q') }}" placeholder="{{ __('Ketik nama kelompok, institusi tim, atau nama anggota...') }}" class="filter-input-text">
                    </div>

                    <div class="filter-field-status">
                        <label for="filter_status_surat_kel" class="filter-label">
                            {{ __('Status Berkas Tim') }}
                        </label>
                        <select name="status_surat" id="filter_status_surat_kel" class="filter-select">
                            <option value="">{{ __('Semua Status') }}</option>
                            <option value="sudah" {{ request('status_surat') === 'sudah' ? 'selected' : '' }}>{{ __('Sudah Diterbitkan') }}</option>
                            <option value="belum" {{ request('status_surat') === 'belum' ? 'selected' : '' }}>{{ __('Belum Diunggah') }}</option>
                        </select>
                    </div>

                    <div class="filter-field-actions">
                        <button type="submit" class="btn-filter-submit">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                            <span>{{ __('Cari') }}</span>
                        </button>
                        @if(request()->hasAny(['q', 'status_surat']))
                            <a href="{{ route('admin.surat-selesai.index', ['tab' => 'kelompok']) }}" class="btn-filter-reset">
                                <span>{{ __('Reset') }}</span>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Container Daftar Kartu Master Rombongan Kelompok --}}
            <div class="surat-panel-container">
                <div class="panel-header-bar">
                    <div>
                        <h3 class="panel-title">{{ __('Daftar Kelompok Selesai Magang') }}</h3>
                        <p class="panel-desc">{{ __('Klik baris kelompok untuk masuk ke workspace unggah surat tim dan melihat tabel anggota kelompok.') }}</p>
                    </div>
                    <span class="panel-counter-chip">
                        {{ $items->total() }} {{ __('Kelompok Terdata') }}
                    </span>
                </div>

                <div class="surat-stream-container">
                    @forelse($items as $pendaftaran)
                        @php
                            $kelompok = $pendaftaran->kelompokMagang;
                            $penempatans = $pendaftaran->penempatans;
                            $primaryPenempatan = $penempatans->first();
                            $hasSuratTim = $penempatans->contains(fn($p) => ! empty($p->file_surat_selesai) && \Illuminate\Support\Facades\Storage::disk('local')->exists($p->file_surat_selesai));
                            $masukUrl = route('admin.surat-selesai.index', array_merge(request()->query(), ['tab' => 'kelompok', 'masuk_kelompok' => $pendaftaran->id]));
                            $totalAnggotaTim = $penempatans->count();
                        @endphp

                        <div class="surat-master-card" data-card-id="{{ $pendaftaran->id }}">
                            <a href="{{ $masukUrl }}" class="surat-card-link" title="{{ __('Masuk ke halaman pengelolaan berkas tim :nama', ['nama' => $kelompok?->nama_kelompok]) }}">
                                
                                {{-- Kiri: Group Icon & Nama Kelompok --}}
                                <div class="card-left-section">
                                    <div class="group-icon-box" aria-hidden="true">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="9" cy="7" r="4"></circle>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                        </svg>
                                    </div>
                                    <div class="card-title-meta">
                                        <h4 class="card-primary-name">{{ $kelompok?->nama_kelompok ?? __('Kelompok Magang') }}</h4>
                                        <p class="card-sub-inst">
                                            {{ $kelompok?->institusi ?? '-' }} &bull; <strong style="color: #1D4ED8;">{{ $totalAnggotaTim }} {{ __('Anggota Terdaftar') }}</strong>
                                        </p>
                                    </div>
                                </div>

                                {{-- Tengah: Bidang & Pembimbing --}}
                                <div class="card-mid-section">
                                    <div class="card-bidang-name">{{ $primaryPenempatan?->bidang?->nama_bidang ?? '-' }}</div>
                                    <div class="card-pembimbing-text">{{ __('Pembimbing:') }} {{ $primaryPenempatan?->pembimbing?->nama ?? '-' }}</div>
                                </div>

                                {{-- Kanan: Status Berkas Tim & Ikon Chevron Masuk --}}
                                <div class="card-right-section">
                                    <div class="card-status-wrapper">
                                        @if($hasSuratTim)
                                            <span class="status-badge-chip success">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                <span>{{ __('SURAT TIM TERBIT') }}</span>
                                            </span>
                                        @else
                                            <span class="status-badge-chip warning">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                                <span>{{ __('BELUM DIUNGGAH') }}</span>
                                            </span>
                                        @endif
                                    </div>

                                    <div class="card-chevron-box" aria-hidden="true">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="9 18 15 12 9 6"></polyline>
                                        </svg>
                                    </div>
                                </div>

                            </a>
                        </div>
                    @empty
                        <div class="empty-state-box">
                            <div class="empty-state-icon">
                                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                            </div>
                            <h4 class="empty-state-title">{{ __('Tidak Ada Data Kelompok Selesai Magang') }}</h4>
                            <p class="empty-state-text">{{ __('Rombongan kelompok yang telah menyelesaikan masa bimbingan magang akan otomatis muncul di sini.') }}</p>
                        </div>
                    @endforelse
                </div>

                @if($items->hasPages())
                    <div class="panel-pagination-wrapper">
                        {{ $items->links() }}
                    </div>
                @endif
            </div>

        @endif

    @endif

</div>
@endsection
