@extends('layouts.peserta')

@section('title', 'Pendaftaran Magang')
@section('page_title', 'Pendaftaran Magang')

@push('styles')
    @vite(['resources/css/pages/peserta/pendaftaran-index.css'])
@endpush

@section('content')
<div class="pendaftaran-container">

    @php
        $profilLengkap = $profil && $profil->nim_nisn && $profil->institusi && $profil->jurusan_id && $profil->no_hp;
    @endphp

    {{-- ========================================================
         1. HERO HEADER BANNER MAGKOM (GENTELELLA PALETTE)
         ======================================================== --}}
    <div class="pendaftaran-hero">
        <div class="hero-text-content">
            <div class="hero-badge-tag">
                <span class="hero-badge-dot"></span>
                Portal Pendaftaran Magang Diskominfosandi Sleman
            </div>
            <h1 class="hero-title">Pendaftaran Magang Kedinasan</h1>
            <p class="hero-desc">
                Pilih bulan pelaksanaan dan ajukan permohonan magang resmi sesuai kompetensi jurusan Anda di lingkungan Dinas Komunikasi dan Informatika Kabupaten Sleman.
            </p>
            <div class="hero-meta-pills">
                <div class="hero-meta-pill">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <span>{{ $profil?->institusi ?? 'Institusi Belum Dicatat' }}</span>
                </div>
                <div class="hero-meta-pill">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ $profil?->jurusan?->nama_jurusan ?? 'Jurusan Belum Dipilih' }}</span>
                </div>
                @if($profil?->jenjang)
                    <div class="hero-meta-pill">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-5.825-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                        <span>{{ strtoupper($profil->jenjang?->value ?? $profil->jenjang) }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Decorative Concentric Rings (Matching Sleman Mockup) --}}
        <svg class="hero-rings-ornament" viewBox="0 0 300 300" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <circle cx="200" cy="200" r="140" stroke="#FFC300" stroke-width="1.5" stroke-opacity="0.35" />
            <circle cx="200" cy="200" r="100" stroke="#FFFFFF" stroke-width="1.5" stroke-opacity="0.25" />
            <circle cx="200" cy="200" r="60" stroke="#FFC300" stroke-width="1.5" stroke-opacity="0.2" />
        </svg>
    </div>

    {{-- ========================================================
         2. ALERT PANDUAN PROFIL (JIKA BELUM LENGKAP)
         ======================================================== --}}
    @if(!$profilLengkap)
        <x-gentelella.card class="border-warning" style="border-left: 5px solid #F59E0B;">
            <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
                <div style="display: flex; align-items: flex-start; gap: 14px; flex: 1;">
                    <div style="width: 40px; height: 40px; border-radius: 8px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 style="font-size: 1rem; font-weight: 700; color: #92400E; margin: 0 0 4px 0;">
                            Lengkapi Biodata Profil Sebelum Mendaftar
                        </h3>
                        <p style="font-size: 0.8125rem; color: #78350F; margin: 0; line-height: 1.5;">
                            Sesuai ketentuan dinas, data institusi pendidikan, NIM/NISN, jurusan, dan kontak darurat harus terverifikasi sebelum dapat membuat pengajuan baru.
                        </p>
                    </div>
                </div>
                <a href="{{ route('peserta.profil.show') }}" class="btn btn-primary" style="align-self: center;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Lengkapi Profil Sekarang
                </a>
            </div>
        </x-gentelella.card>
    @endif

    {{-- ========================================================
         3. SPOTLIGHT CARD PENGAJUAN AKTIF (TWO-TONE SPLIT)
         ======================================================== --}}
    @if($pendaftaranAktif)
        @php
            $valStatus = $pendaftaranAktif->status->value ?? (string)$pendaftaranAktif->status;
            $namaPenempatan = $pendaftaranAktif->penempatan?->bidang?->nama_bidang;
            $isPlacedOrAccepted = ($valStatus === 'diterima' || !empty($namaPenempatan));
        @endphp
        <div class="pendaftaran-active-card">
            {{-- Top Navy Header Band --}}
            <div class="active-card-topbar">
                <div class="active-card-meta">
                    <div class="active-reg-badge">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>#REG-{{ str_pad((string)$pendaftaranAktif->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <h2 class="active-card-title">
                        Pengajuan Magang Aktif Sedang Diproses
                    </h2>
                    <p class="active-card-date">
                        Diajukan pada {{ $pendaftaranAktif->created_at->translatedFormat('d F Y, H:i') }} WIB
                    </p>
                </div>

                <div>
                    @if($valStatus === 'diterima')
                        <div class="active-badge-gold">
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <span>Selamat! Diterima Magang</span>
                        </div>
                    @elseif($valStatus === 'diajukan')
                        <div class="active-badge-gold" style="background: #FEF3C7; color: #92400E;">
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            <span>Menunggu Verifikasi Admin</span>
                        </div>
                    @elseif($valStatus === 'waiting_list')
                        <div class="active-badge-gold" style="background: #EFF6FF; color: #1E40AF;">
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            <span>Waiting List (Antrean)</span>
                        </div>
                    @elseif($valStatus === 'perlu_perbaikan')
                        <div class="active-badge-gold" style="background: #FEF2F2; color: #B91C1C;">
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                            <span>Perlu Perbaikan Berkas</span>
                        </div>
                    @elseif($valStatus === 'berkas_belum_lengkap')
                        <div class="active-badge-gold" style="background: #F1F5F9; color: #475569;">
                            <span>Berkas Belum Lengkap</span>
                        </div>
                    @else
                        <div class="active-badge-gold">
                            <span>{{ ucfirst($valStatus) }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Crisp White Lower Body --}}
            <div class="active-card-body">
                <div class="active-grid-4">
                    {{-- 1. Bulan Magang --}}
                    <div class="active-metric-box">
                        <div class="metric-icon-box" aria-hidden="true">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <div class="metric-content">
                            <span class="metric-label">Bulan Magang</span>
                            <span class="metric-value">{{ $pendaftaranAktif->bulanMagang?->bulan_mulai?->translatedFormat('F Y') }}</span>
                        </div>
                    </div>

                    {{-- 2. Jurusan Kuota Terdaftar --}}
                    <div class="active-metric-box">
                        <div class="metric-icon-box" aria-hidden="true">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-5.825-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                        </div>
                        <div class="metric-content">
                            <span class="metric-label">Jurusan Kuota Terdaftar</span>
                            <span class="metric-value" title="{{ $pendaftaranAktif->kuota?->jurusan?->nama_jurusan ?? '-' }}">
                                {{ $pendaftaranAktif->kuota?->jurusan?->nama_jurusan ?? '-' }}
                            </span>
                        </div>
                    </div>

                    {{-- 3. Rencana Durasi Magang --}}
                    <div class="active-metric-box">
                        <div class="metric-icon-box" aria-hidden="true">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        </div>
                        <div class="metric-content">
                            <span class="metric-label">Rencana Durasi Magang</span>
                            <span class="metric-value">{{ $pendaftaranAktif->durasi_bulan }} Bulan</span>
                        </div>
                    </div>

                    {{-- 4. Status Penempatan (Highlighted) --}}
                    <div class="active-metric-box {{ $isPlacedOrAccepted ? 'highlight-placement' : '' }}">
                        <div class="metric-icon-box" aria-hidden="true">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <div class="metric-content">
                            <span class="metric-label">Status Penempatan</span>
                            <span class="metric-value" title="{{ $namaPenempatan ?? 'Tahap Seleksi Berkas' }}">
                                {{ $namaPenempatan ?? 'Tahap Seleksi Berkas' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Dashed Separator --}}
                <div class="active-dashed-separator"></div>

                {{-- Golden CTA Button --}}
                <div class="active-card-cta-row">
                    <a href="{{ route('peserta.pendaftaran.detail', $pendaftaranAktif) }}" class="btn-gold-action">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Buka Berkas & Detail Pengajuan</span>
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- ========================================================
         4. KATALOG KUOTA BULAN MAGANG (X-PANEL GENTELELLA)
         ======================================================== --}}
    @if(!$pendaftaranAktif && $profilLengkap)
        @php
            $bidangList = $profil->jurusan?->bidang;
            $namaBidang = ($bidangList && $bidangList->isNotEmpty())
                ? $bidangList->pluck('nama_bidang')->implode(', ')
                : 'Dinas Komunikasi dan Informatika';
        @endphp
        <x-gentelella.x-panel title="Pilihan Bulan & Kuota Magang Tersedia" subtitle="Ketersediaan kuota resmi untuk Bidang {{ $namaBidang }} &bull; Jurusan {{ $profil->jurusan?->nama_jurusan }}">
            @if($kuotaList->isEmpty())
                <div style="text-align: center; padding: 36px 20px; background: #F8FAFC; border: 1px dashed var(--neutral-300, #CBD5E1); border-radius: 8px;">
                    <div style="font-size: 2rem; margin-bottom: 8px;">📅</div>
                    <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-800, #1E293B); margin: 0 0 4px 0;">Belum Ada Kuota Magang Terbuka</h3>
                    <p style="font-size: 0.8125rem; color: var(--neutral-500, #64748B); max-width: 460px; margin: 0 auto;">
                        Saat ini belum ada bulan magang yang dibuka untuk jurusan Anda. Silakan hubungi admin kedinasan atau pantau pengumuman berkala.
                    </p>
                </div>
            @else
                <div class="kuota-grid">
                    @foreach($kuotaList as $kuotaItem)
                        @php
                            $sisaKursi = max(0, $kuotaItem->jumlah - $kuotaItem->terisi);
                            $persenTerisi = $kuotaItem->jumlah > 0 ? min(100, round(($kuotaItem->terisi / $kuotaItem->jumlah) * 100)) : 100;
                            $bulanMulaiFormatted = $kuotaItem->bulanMagang?->bulan_mulai?->translatedFormat('F Y');
                            $itemBidangNama = ($kuotaItem->jurusan?->bidang && $kuotaItem->jurusan->bidang->isNotEmpty())
                                ? $kuotaItem->jurusan->bidang->pluck('nama_bidang')->implode(', ')
                                : $namaBidang;
                        @endphp
                        <div class="kuota-card">
                            <div class="kuota-card-info">
                                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 8px;">
                                    <x-gentelella.badge variant="{{ $sisaKursi > 0 ? 'success' : 'warning' }}" style="font-size: 0.6875rem;">
                                        {{ $sisaKursi > 0 ? '✓ Kuota Tersedia' : 'Penuh (Waiting List)' }}
                                    </x-gentelella.badge>
                                    <div style="display: inline-flex; align-items: center; gap: 5px; font-size: 0.6875rem; font-weight: 700; color: #02275D; background: rgba(2, 39, 93, 0.06); border: 1px solid rgba(2, 39, 93, 0.18); padding: 2px 8px; border-radius: 6px;">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        <span>{{ $itemBidangNama }}</span>
                                    </div>
                                    <span style="font-size: 0.6875rem; font-weight: 700; color: var(--neutral-500, #64748B); margin-left: auto;">
                                        Total Kuota: {{ $kuotaItem->jumlah }}
                                    </span>
                                </div>

                                <h3 class="kuota-month-name">{{ $bulanMulaiFormatted }}</h3>
                                <div class="kuota-dates" style="display: flex; align-items: center; gap: 5px; margin-bottom: 0;">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span>Pelaksanaan mulai: {{ $kuotaItem->bulanMagang?->bulan_mulai?->translatedFormat('d M Y') ?? '-' }}</span>
                                </div>
                            </div>

                            <div class="kuota-card-stats">
                                <div class="kuota-progress-box" style="margin-bottom: 0;">
                                    <div style="display: flex; justify-content: space-between; font-size: 0.75rem; font-weight: 700; margin-bottom: 4px;">
                                        <span style="color: {{ $sisaKursi > 0 ? '#166534' : '#92400E' }};">
                                            {{ $sisaKursi > 0 ? "Tersisa {$sisaKursi} Kursi" : 'Kuota Penuh' }}
                                        </span>
                                        <span style="color: var(--neutral-500, #64748B);">
                                            {{ $kuotaItem->terisi }}/{{ $kuotaItem->jumlah }} Terisi
                                        </span>
                                    </div>
                                    <div class="kuota-progress-bar-bg" style="margin-top: 4px;">
                                        <div class="kuota-progress-bar-fill" style="width: {{ $persenTerisi }}%; background: {{ $sisaKursi > 0 ? '#22C55E' : '#EAB308' }};"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="kuota-card-cta">
                                <button type="button" class="btn btn-primary" onclick="openModalWithKuota('{{ $kuotaItem->id }}')" style="width: 100%; min-width: 170px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; font-weight: 700; padding: 10px 18px;">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    <span>Daftar Sekarang</span>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-gentelella.x-panel>
    @endif

    {{-- ========================================================
         5. TABEL RIWAYAT PENDAFTARAN MAGANG (MODERN WHITE ACCORDION CARD)
         ======================================================== --}}
    <div class="riwayat-card-container">
        <div class="riwayat-header">
            <h2 class="riwayat-title">Riwayat Pendaftaran Magang</h2>
            <button type="button" class="riwayat-chevron-btn" id="btnToggleRiwayat" aria-label="Toggle Riwayat Pendaftaran">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <polyline points="18 15 12 9 6 15"></polyline>
                </svg>
            </button>
        </div>

        <span class="sr-only">Buat Pengajuan Baru</span>

        <div class="riwayat-body" id="riwayatAccordionBody">
            @if($pendaftaranList->isEmpty())
                <div style="text-align: center; padding: 48px 24px;">
                    <div style="width: 56px; height: 56px; margin: 0 auto 16px auto; background: rgba(2, 39, 93, 0.06); color: #02275D; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 style="font-size: 1rem; font-weight: 700; color: #1E293B; margin: 0 0 6px 0;">Belum Ada Pengajuan Magang</h3>
                    <p style="font-size: 0.8125rem; color: #64748B; max-width: 420px; margin: 0 auto; line-height: 1.5;">
                        Anda belum memiliki riwayat pengajuan magang di Dinas Kominfo Sleman.
                    </p>
                </div>
            @else
                <div class="riwayat-stream-list">
                    @foreach($pendaftaranList as $item)
                        @php
                            $val = $item->status->value ?? (string)$item->status;
                        @endphp
                        <div class="riwayat-card-row">
                            <div class="riwayat-row-left">
                                <div class="riwayat-navy-icon-box" aria-hidden="true">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                </div>

                                <div class="riwayat-row-info">
                                    <div class="riwayat-title-row">
                                        <span class="riwayat-reg-code">#REG-{{ str_pad((string)$item->id, 5, '0', STR_PAD_LEFT) }}</span>
                                        <a href="{{ route('peserta.pendaftaran.detail', $item) }}" class="riwayat-title-text">
                                            {{ $item->bulanMagang?->bulan_mulai?->translatedFormat('F Y') }}
                                        </a>
                                    </div>
                                    <div class="riwayat-meta-row">
                                        <strong>{{ $item->kuota?->jurusan?->nama_jurusan ?? '-' }}</strong>
                                        <span class="riwayat-bullet">&bull;</span>
                                        <span>Rencana: <strong>{{ $item->durasi_bulan }} Bulan</strong></span>
                                        <span class="riwayat-bullet">&bull;</span>
                                        <span>Diajukan: {{ $item->created_at->translatedFormat('d M Y, H:i') }} WIB</span>
                                    </div>
                                </div>
                            </div>

                            <div class="riwayat-row-right">
                                <div>
                                    @if($val === 'diterima')
                                        <span class="riwayat-status-pill-soft status-diterima">Diterima</span>
                                    @elseif(in_array($val, ['diajukan', 'waiting_list']))
                                        <span class="riwayat-status-pill-soft status-diajukan">Menunggu Verifikasi</span>
                                    @elseif($val === 'perlu_perbaikan')
                                        <span class="riwayat-status-pill-soft status-perbaikan">Perlu Perbaikan</span>
                                    @elseif($val === 'berkas_belum_lengkap')
                                        <span class="riwayat-status-pill-soft status-default">Belum Lengkap</span>
                                    @elseif($val === 'ditolak')
                                        <span class="riwayat-status-pill-soft status-perbaikan">Ditolak</span>
                                    @elseif($val === 'dibatalkan')
                                        <span class="riwayat-status-pill-soft status-default">Dibatalkan</span>
                                    @else
                                        <span class="riwayat-status-pill-soft status-default">{{ ucfirst($val) }}</span>
                                    @endif
                                </div>

                                <a href="{{ route('peserta.pendaftaran.detail', $item) }}" class="btn-detail-outline" title="Lihat detail pendaftaran">
                                    <span>Detail</span>
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

{{-- ========================================================
     6. MODAL INTERAKTIF PENGAJUAN MAGANG BARU
     ======================================================== --}}
@if($profilLengkap && !$pendaftaranAktif)
<div class="pendaftaran-modal-backdrop" id="pendaftaranModal" role="dialog" aria-modal="true" aria-labelledby="modalPendaftaranTitle" onclick="if(event.target === this) closeModalPendaftaran()">
    <div class="pendaftaran-modal-card">
        <div class="pendaftaran-modal-header">
            <div class="pendaftaran-modal-header-main">
                <div class="pendaftaran-modal-icon-badge" aria-hidden="true">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <h3 class="pendaftaran-modal-title" id="modalPendaftaranTitle">
                        Formulir Pengajuan Magang Baru
                    </h3>
                    <p class="pendaftaran-modal-subtitle">
                        Tentukan bulan dan durasi pelaksanaan magang Anda.
                    </p>
                </div>
            </div>
            <button type="button" class="pendaftaran-modal-close" onclick="closeModalPendaftaran()" aria-label="Tutup Formulir">
                &times;
            </button>
        </div>

        <form action="{{ route('peserta.pendaftaran.store') }}" method="POST" id="formPendaftaranBaru" onsubmit="handleFormSubmit(this)">
            @csrf

            <div class="pendaftaran-form-body">
                {{-- 1. Kartu Ringkasan Profil & Jurusan Terdaftar --}}
                <div class="pendaftaran-summary-card">
                    <div class="pendaftaran-summary-row">
                        <span class="pendaftaran-summary-jurusan">
                            {{ $profil->jurusan?->nama_jurusan ?? 'Jurusan Terdaftar' }}
                        </span>
                        <span class="pendaftaran-summary-badge">
                            {{ strtoupper($profil->jenjang?->value ?? $profil->jenjang) }}
                        </span>
                    </div>

                    @php
                        $modalBidangNama = ($profil->jurusan?->bidang && $profil->jurusan->bidang->isNotEmpty())
                            ? $profil->jurusan->bidang->pluck('nama_bidang')->implode(', ')
                            : 'Dinas Komunikasi dan Informatika';
                    @endphp
                    <div class="pendaftaran-summary-sub">
                        <span>Bidang: <strong>{{ $modalBidangNama }}</strong></span>
                        <span class="pendaftaran-summary-bullet">&bull;</span>
                        <span>Kampus: <strong>{{ $profil->institusi }}</strong></span>
                    </div>
                </div>

                {{-- 2. Dropdown Bulan Magang & Kuota Aktif --}}
                <div class="pendaftaran-form-group">
                    <label for="kuota_id" class="pendaftaran-label">
                        <span>Pilih Bulan Magang</span>
                        <span class="req">*</span>
                    </label>
                    <select id="kuota_id" name="kuota_id" class="pendaftaran-select" required onchange="handleKuotaChange(this)">
                        <option value="">-- Pilih Bulan Magang --</option>
                        @foreach($kuotaList as $kuota)
                            @php
                                $sisa = max(0, $kuota->jumlah - $kuota->terisi);
                                $bulanMulaiIso = $kuota->bulanMagang?->bulan_mulai?->format('Y-m-d');
                            @endphp
                            <option value="{{ $kuota->id }}"
                                    data-bulan-id="{{ $kuota->bulan_magang_id }}"
                                    data-bulan-mulai="{{ $bulanMulaiIso }}"
                                    {{ old('kuota_id') == $kuota->id ? 'selected' : '' }}>
                                {{ $kuota->bulanMagang?->bulan_mulai?->translatedFormat('F Y') }}
                                ({{ $sisa > 0 ? "Tersisa {$sisa} kursi" : 'Penuh (Waiting List)' }})
                            </option>
                        @endforeach
                    </select>
                    <p class="pendaftaran-hint">
                        Menampilkan kuota magang yang berstatus tersedia untuk jurusan Anda.
                    </p>
                </div>

                {{-- Hidden input bulan_magang_id yang diisi otomatis via JS dari select kuota_id --}}
                <input type="hidden" id="bulan_magang_id" name="bulan_magang_id" value="{{ old('bulan_magang_id') }}">

                {{-- 3. Pilihan Durasi Magang Berbasis Interactive Chips --}}
                <div class="pendaftaran-form-group">
                    <label for="durasi_bulan" class="pendaftaran-label">
                        <span>Rencana Durasi Magang</span>
                        <span class="req">*</span>
                    </label>

                    <div class="duration-chips-grid">
                        @for($d = 1; $d <= 6; $d++)
                            <div class="duration-chip-btn {{ old('durasi_bulan', 3) == $d ? 'is-active' : '' }}"
                                 onclick="selectDurationChip({{ $d }})"
                                 role="button"
                                 tabindex="0"
                                 aria-pressed="{{ old('durasi_bulan', 3) == $d ? 'true' : 'false' }}">
                                <span class="duration-chip-val">{{ $d }} Bulan</span>
                                <span class="duration-chip-sub">
                                    {{ $d == 3 ? 'Direkomendasikan' : ($d == 6 ? 'Maksimal' : 'Opsi') }}
                                </span>
                            </div>
                        @endfor
                    </div>

                    {{-- Native Select tersembunyi untuk submission POST --}}
                    <select id="durasi_bulan" name="durasi_bulan" style="display: none;" required onchange="updateEstimasiPeriode()">
                        @for($d = 1; $d <= 6; $d++)
                            <option value="{{ $d }}" {{ old('durasi_bulan', 3) == $d ? 'selected' : '' }}>{{ $d }} Bulan</option>
                        @endfor
                    </select>

                    <p class="pendaftaran-hint">
                        Sesuai rekomendasi surat pengantar institusi pendidikan Anda (1 s/d 6 bulan).
                    </p>
                </div>

                {{-- Live Preview Rentang Tanggal --}}
                <div id="previewPeriodeBox" class="pendaftaran-preview-box" style="display: none;">
                    <div class="pendaftaran-preview-icon" aria-hidden="true">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div class="pendaftaran-preview-content">
                        <span class="pendaftaran-preview-title">Estimasi Pelaksanaan:</span>
                        <span id="previewPeriodeText" class="pendaftaran-preview-date"></span>
                    </div>
                </div>
            </div>

            <div class="pendaftaran-modal-footer">
                <button type="button" class="pendaftaran-btn-cancel" onclick="closeModalPendaftaran()">Batal</button>
                <button type="submit" class="pendaftaran-btn-submit" id="btnSubmitPendaftaran">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Daftar Sekarang</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@push('scripts')
<script>
    function openModalPendaftaran() {
        const modal = document.getElementById('pendaftaranModal');
        if (modal) {
            modal.classList.add('open');
            updateEstimasiPeriode();
        }
    }

    function closeModalPendaftaran() {
        const modal = document.getElementById('pendaftaranModal');
        if (modal) modal.classList.remove('open');
    }

    function openModalWithKuota(kuotaId) {
        const select = document.getElementById('kuota_id');
        if (select) {
            select.value = kuotaId;
            handleKuotaChange(select);
        }
        openModalPendaftaran();
    }

    function selectDurationChip(months) {
        const select = document.getElementById('durasi_bulan');
        if (select) {
            select.value = months;
            updateEstimasiPeriode();
        }
        const chips = document.querySelectorAll('.duration-chip-btn');
        chips.forEach((chip, index) => {
            if (index + 1 === months) {
                chip.classList.add('is-active');
            } else {
                chip.classList.remove('is-active');
            }
        });
    }

    function handleKuotaChange(select) {
        const selectedOption = select.options[select.selectedIndex];
        const bulanId = selectedOption ? selectedOption.getAttribute('data-bulan-id') : '';
        const inputBulan = document.getElementById('bulan_magang_id');
        if (inputBulan) inputBulan.value = bulanId || '';
        updateEstimasiPeriode();
    }

    function updateEstimasiPeriode() {
        const selectKuota = document.getElementById('kuota_id');
        const selectDurasi = document.getElementById('durasi_bulan');
        const previewBox = document.getElementById('previewPeriodeBox');
        const previewText = document.getElementById('previewPeriodeText');

        if (!selectKuota || !selectDurasi || !previewBox || !previewText) return;

        const selectedOption = selectKuota.options[selectKuota.selectedIndex];
        const bulanMulaiStr = selectedOption ? selectedOption.getAttribute('data-bulan-mulai') : null;
        const durasi = parseInt(selectDurasi.value, 10);

        if (bulanMulaiStr && durasi) {
            const startDate = new Date(bulanMulaiStr);
            const endDate = new Date(startDate.getFullYear(), startDate.getMonth() + durasi, 0);

            const options = { day: 'numeric', month: 'long', year: 'numeric' };
            const startFmt = startDate.toLocaleDateString('id-ID', options);
            const endFmt = endDate.toLocaleDateString('id-ID', options);

            previewText.textContent = `${startFmt} s/d ${endFmt} (${durasi} Bulan)`;
            previewBox.style.display = 'block';
        } else {
            previewBox.style.display = 'none';
        }
    }

    function handleFormSubmit(form) {
        const btn = document.getElementById('btnSubmitPendaftaran');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `<span style="display:inline-block;width:14px;height:14px;border:2px solid #fff;border-top-color:transparent;border-radius:50%;animation:spin 0.6s linear infinite;margin-right:8px;"></span> Menyimpan...`;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const select = document.getElementById('kuota_id');
        if (select && select.value) {
            handleKuotaChange(select);
        }

        const btnToggleRiwayat = document.getElementById('btnToggleRiwayat');
        const riwayatBody = document.getElementById('riwayatAccordionBody');
        if (btnToggleRiwayat && riwayatBody) {
            btnToggleRiwayat.addEventListener('click', () => {
                const isHidden = riwayatBody.style.display === 'none';
                riwayatBody.style.display = isHidden ? 'block' : 'none';
                const svg = btnToggleRiwayat.querySelector('svg');
                if (svg) {
                    svg.style.transform = isHidden ? 'rotate(0deg)' : 'rotate(180deg)';
                }
            });
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeModalPendaftaran();
            }
        });
    });
</script>
@endpush
@endsection
