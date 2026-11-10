@extends('layouts.dashboard')

@section('title', 'Detail Arsip Peserta ' . ($penempatan->peserta?->nama ?? 'Alumni') . ' - SuperAdmin MagKom')
@section('topbar_title', 'Detail Arsip Alumni Magang')

@section('breadcrumbs')
    <a href="{{ route('superadmin.dashboard') }}">SuperAdmin</a>
    <span class="breadcrumb-separator">/</span>
    <a href="{{ route('superadmin.arsip.index') }}">Arsip Peserta</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">{{ $penempatan->peserta?->nama }}</span>
@endsection

@push('styles')
    @vite(['resources/css/pages/superadmin/superadmin-common.css'])
@endpush

@section('content')
@php
    $peserta = $penempatan->peserta;
    $profil = $peserta?->profil;
    $pembimbing = $penempatan->pembimbing;
    $penilaian = $penempatan->penilaian;
    $namaPeserta = $peserta?->nama ?? 'Alumni';
    $initials = collect(explode(' ', $namaPeserta))->take(2)->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('');
    $pembimbingInitials = collect(explode(' ', $pembimbing?->nama ?? 'Pembimbing'))->take(2)->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('');
@endphp

<div class="sa-fluid-container" style="display: flex; flex-direction: column; gap: 1.5rem;">

    {{-- 1. HERO BANNER ALUMNI KHAS SLEMAN MAGKOM --}}
    <div class="sa-hero-banner">
        <div class="banner-top-row">
            <div style="display: flex; align-items: center; gap: 1.25rem; flex-wrap: wrap;">
                <div style="width: 62px; height: 62px; min-width: 62px; border-radius: 12px; background: #ffffff; color: #01347C; font-weight: 800; font-size: 1.5rem; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                    @if($profil?->foto_url)
                        <img src="{{ $profil->foto_url }}" alt="{{ $namaPeserta }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px;">
                    @else
                        {{ $initials ?: 'AL' }}
                    @endif
                </div>
                <div>
                    <div style="display: flex; align-items: center; gap: 0.65rem; flex-wrap: wrap;">
                        <h1 class="banner-title" style="margin: 0; font-size: 1.375rem;">{{ $namaPeserta }}</h1>
                        @if($penempatan->status?->value === 'selesai')
                            <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.65rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: rgba(147, 197, 253, 0.2); color: #BFDBFE; border: 1px solid rgba(147, 197, 253, 0.4);">
                                Selesai Magang
                            </span>
                        @elseif($penempatan->status?->value === 'mengundurkan_diri')
                            <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.65rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: rgba(239, 68, 68, 0.2); color: #FECACA; border: 1px solid rgba(239, 68, 68, 0.4);">
                                Mengundurkan Diri
                            </span>
                        @else
                            <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.65rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: rgba(255, 255, 255, 0.2); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.4);">
                                Nonaktif (Arsip)
                            </span>
                        @endif
                    </div>
                    <div class="banner-subtitle" style="margin-top: 0.35rem; font-size: 0.875rem;">
                        <span>Email: <strong>{{ $peserta?->email }}</strong></span>
                        <span class="bullet-separator">&bull;</span>
                        <span>NIM/NISN: <strong>{{ $profil?->nim_nisn ?? '-' }}</strong></span>
                        <span class="bullet-separator">&bull;</span>
                        <span>Institusi: <strong>{{ $profil?->institusi ?? '-' }}</strong></span>
                    </div>
                </div>
            </div>

            {{-- Tombol Aksi Kanan --}}
            <div class="banner-actions-group" style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                <a href="{{ route('admin.penempatan.surat-diterima', $penempatan) }}" class="btn-banner-action--secondary" target="_blank">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                    </svg>
                    <span>Surat Diterima PDF</span>
                </a>
                <a href="{{ route('superadmin.arsip.index') }}" class="btn-banner-action">
                    <span>&larr; Kembali ke Arsip</span>
                </a>
            </div>
        </div>
    </div>

    @php
        $jenjangPeserta = $profil?->jenjang ?? $penempatan->pendaftaran?->user?->profil?->jenjang;
        $rawDokumen = $penempatan->pendaftaran?->dokumen ?? collect();
        $filteredDokumen = $rawDokumen->filter(function ($dok) use ($jenjangPeserta) {
            if (! $jenjangPeserta || ! $dok->jenisDokumen) {
                return true;
            }
            return ! $dok->jenisDokumen->jenjang || $dok->jenisDokumen->jenjang === $jenjangPeserta;
        });
        $dokumenList = $filteredDokumen->isNotEmpty() ? $filteredDokumen : $rawDokumen;
    @endphp

    {{-- 2. GRID 4 KARTU STATISTIK TILE-STAT (PARITAS DENGAN MANAJEMEN STAF & ARSIP PESERTA) --}}
    <div class="tiles-grid">
        <!-- 1. Total Penugasan -->
        <x-gentelella.tile-stat
            title="TOTAL PENUGASAN"
            value="{{ $penempatan->tugas->count() }}"
            color="blue"
            trendType="info"
            trendDirection="up"
            trendText="Tugas"
            subText="Beban Kerja Selama Dinas"
            :sparkBars="[20, 35, 50, 70, 85, 95]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M9 11l3 3L22 4"></path>
                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <!-- 2. Tugas Selesai -->
        <x-gentelella.tile-stat
            title="TUGAS SELESAI"
            value="{{ $penempatan->tugas->where('status.value', 'selesai')->count() }}"
            color="teal"
            trendType="success"
            trendDirection="up"
            trendText="Tuntas"
            subText="Pekerjaan Berstatus Selesai"
            :sparkBars="[25, 40, 55, 75, 90, 100]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <!-- 3. Logbook Harian -->
        <x-gentelella.tile-stat
            title="LOGBOOK HARIAN"
            value="{{ $penempatan->logbooks->count() }}"
            color="amber"
            trendType="warning"
            trendDirection="up"
            trendText="Presensi"
            subText="Aktivitas Terbimbing Dinas"
            :sparkBars="[15, 30, 50, 70, 85, 95]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <!-- 4. Berkas Persyaratan -->
        <x-gentelella.tile-stat
            title="BERKAS PERSYARATAN"
            value="{{ $dokumenList->count() }}"
            color="purple"
            trendType="info"
            trendDirection="up"
            trendText="Berkas"
            subText="Kelengkapan Syarat Pendaftaran"
            :sparkBars="[30, 45, 60, 75, 90, 100]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>
    </div>

    {{-- 3. SEGMENTED PILL TAB CONTROLLER (DS-MAGKOM PARITY) --}}
    <div class="arsip-segmented-tabs" role="tablist">
        <button type="button" class="arsip-tab-pill active" data-tab-target="tab-ringkasan">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
            <span>Ringkasan &amp; Biodata</span>
        </button>
        <button type="button" class="arsip-tab-pill" data-tab-target="tab-tugas">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 11l3 3L22 4"></path>
                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
            </svg>
            <span>Riwayat Tugas</span>
            <span class="pill-counter">({{ (string) $penempatan->tugas->count() }})</span>
        </button>
        <button type="button" class="arsip-tab-pill" data-tab-target="tab-logbook">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
            </svg>
            <span>Logbook Harian</span>
            <span class="pill-counter">({{ (string) $penempatan->logbooks->count() }})</span>
        </button>
        <button type="button" class="arsip-tab-pill" data-tab-target="tab-administrasi">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
            </svg>
            <span>Dokumen Pendaftaran</span>
            <span class="pill-counter">({{ (string) $dokumenList->count() }})</span>
        </button>
    </div>

    {{-- TAB 1: RINGKASAN & BIODATA --}}
    <div class="arsip-tab-pane active" id="tab-ringkasan">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 1.5rem;">
            
            {{-- Kartu A: Data Penempatan & Institusi --}}
            <div class="gentelella-card" style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-top: 3.5px solid var(--magkom-primary-blue, #01347C) !important; border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
                <h3 style="margin: 0 0 1.25rem 0; font-size: 1rem; font-weight: 800; color: var(--neutral-800); border-bottom: 1px solid var(--neutral-300); padding-bottom: 0.75rem;">
                    Data Penempatan &amp; Institusi
                </h3>
                <div style="display: flex; flex-direction: column; gap: 1rem; font-size: 0.875rem;">
                    <div>
                        <span style="color: var(--neutral-500); display: block; font-size: 0.725rem; font-weight: 700; text-transform: uppercase;">Bidang Dinas</span>
                        <strong style="color: var(--neutral-900); font-size: 0.9375rem;">{{ $penempatan->bidang?->nama_bidang ?? 'Dinas Kominfo Sleman' }}</strong>
                    </div>
                    <div>
                        <span style="color: var(--neutral-500); display: block; font-size: 0.725rem; font-weight: 700; text-transform: uppercase;">Jurusan Terdaftar</span>
                        <strong style="color: var(--neutral-900); font-size: 0.9375rem;">{{ $penempatan->kuota?->jurusan?->nama_jurusan ?? ($profil?->jurusan?->nama_jurusan ?? '-') }}</strong>
                    </div>
                    <div class="arsip-info-box" style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div>
                            <span style="color: var(--neutral-500); display: block; font-size: 0.6875rem; font-weight: 700;">TANGGAL MULAI</span>
                            <strong style="color: var(--neutral-900);">{{ $penempatan->tgl_mulai?->translatedFormat('d M Y') ?? '-' }}</strong>
                        </div>
                        <div>
                            <span style="color: var(--neutral-500); display: block; font-size: 0.6875rem; font-weight: 700;">TANGGAL SELESAI</span>
                            <strong style="color: var(--neutral-900);">{{ $penempatan->tgl_selesai?->translatedFormat('d M Y') ?? '-' }}</strong>
                        </div>
                    </div>
                    <div>
                        <span style="color: var(--neutral-500); display: block; font-size: 0.725rem; font-weight: 700; text-transform: uppercase;">Jalur &amp; Sumber</span>
                        <span style="color: var(--neutral-800); font-weight: 600;">{{ $penempatan->jalur?->label() ?? 'Reguler' }} &bull; {{ $penempatan->sumber?->label() ?? 'Portal' }}</span>
                    </div>
                </div>
            </div>

            {{-- Kartu B: Pembimbing Lapangan --}}
            <div class="gentelella-card" style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-top: 3.5px solid var(--magkom-primary-blue, #01347C) !important; border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
                <h3 style="margin: 0 0 1.25rem 0; font-size: 1rem; font-weight: 800; color: var(--neutral-800); border-bottom: 1px solid var(--neutral-300); padding-bottom: 0.75rem;">
                    Pembimbing Lapangan
                </h3>
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.25rem;">
                    <div style="width: 48px; height: 48px; min-width: 48px; border-radius: 10px; background: linear-gradient(135deg, #01347C 0%, #003566 100%); color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem; box-shadow: 0 2px 6px rgba(1, 52, 124, 0.2);">
                        {{ $pembimbingInitials ?: 'PL' }}
                    </div>
                    <div>
                        <div style="font-size: 0.9375rem; font-weight: 800; color: var(--neutral-900);">{{ $pembimbing?->nama ?? 'Tidak Ada Pembimbing' }}</div>
                        <div style="font-size: 0.8125rem; color: var(--neutral-600);">{{ $pembimbing?->email ?? '-' }}</div>
                    </div>
                </div>

                <div class="arsip-info-box" style="display: flex; flex-direction: column; gap: 0.65rem; font-size: 0.8125rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: var(--neutral-600);">Total Tugas Diberikan:</span>
                        <strong style="color: var(--neutral-900);">{{ (string) $penempatan->tugas->count() }} Tugas</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: var(--neutral-600);">Tugas Selesai:</span>
                        <strong style="color: #01347C;">{{ (string) $penempatan->tugas->where('status.value', 'selesai')->count() }} Selesai</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: var(--neutral-600);">Total Logbook Terisi:</span>
                        <strong style="color: var(--blue-600);">{{ (string) $penempatan->logbooks->count() }} Entri</strong>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- TAB 2: RIWAYAT TUGAS --}}
    <div class="arsip-tab-pane" id="tab-tugas">
        <div class="gentelella-card" style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-top: 3.5px solid var(--magkom-primary-blue, #01347C) !important; border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
                <div>
                    <h3 style="margin: 0; font-size: 1rem; font-weight: 800; color: var(--neutral-800);">
                        Daftar Tugas &amp; Penugasan Magang (Read-Only)
                    </h3>
                </div>
                <span style="font-size: 0.75rem; font-weight: 700; padding: 0.3rem 0.75rem; border-radius: 9999px; background: #EFF6FF; color: #01347C; border: 1px solid #BFDBFE;">
                    Total: {{ $penempatan->tugas->count() }} Penugasan
                </span>
            </div>

            @if($penempatan->tugas->isNotEmpty())
                <div style="display: flex; flex-direction: column; gap: 1rem;" id="arsipTugasList">
                    @foreach($penempatan->tugas as $t)
                        <div class="arsip-task-card">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; flex-wrap: wrap;">
                                <div style="flex: 1; min-width: 250px;">
                                    <h4 style="margin: 0; font-size: 0.9375rem; font-weight: 800; color: var(--neutral-900);">
                                        {{ $t->judul }}
                                    </h4>
                                    <p style="margin: 0.35rem 0 0 0; font-size: 0.8125rem; color: var(--neutral-600); line-height: 1.45;">
                                        {{ $t->deskripsi ?: 'Tidak ada rincian deskripsi tugas.' }}
                                    </p>
                                </div>
                                <span class="badge-status-blue">
                                    Selesai
                                </span>
                            </div>

                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; margin-top: 0.25rem; font-size: 0.75rem; color: var(--neutral-600);">
                                <div style="display: flex; align-items: center; gap: 1.25rem; flex-wrap: wrap;">
                                    <span>Tenggat Waktu: <strong>{{ $t->deadline ? $t->deadline->translatedFormat('d M Y') : '-' }}</strong></span>
                                    @if($t->link_referensi)
                                        <span>&bull;</span>
                                        <a href="{{ $t->link_referensi }}" target="_blank" style="color: #01347C; font-weight: 700; text-decoration: underline; display: inline-flex; align-items: center; gap: 0.25rem;">
                                            <span>Tautan Repositori / Referensi</span>
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                        </a>
                                    @endif
                                </div>
                            </div>

                            @if($t->progresLogs->isNotEmpty())
                                <div style="padding: 0.65rem 0.85rem; border-radius: 6px; background: rgba(16, 185, 129, 0.08); border-left: 3px solid #10B981; font-size: 0.78125rem; color: var(--neutral-800);">
                                    <strong style="color: #065F46;">Progres Terakhir:</strong> {{ $t->progresLogs->first()->catatan ?? 'Pekerjaan proyek selesai 100%.' }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div id="arsipTugasPagination" style="margin-top: 1.25rem;"></div>
            @else
                <div style="text-align: center; padding: 3rem 1.5rem; color: var(--neutral-600);">
                    <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--neutral-200); display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem auto;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"></path><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                    </div>
                    <div style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-800);">Tidak Ada Catatan Tugas</div>
                    <p style="font-size: 0.8125rem; margin: 0.25rem 0 0 0;">Tidak ada catatan tugas yang terdaftar untuk alumni ini.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- TAB 3: REKAPITULASI LOGBOOK --}}
    <div class="arsip-tab-pane" id="tab-logbook">
        <div class="gentelella-card" style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-top: 3.5px solid var(--magkom-primary-blue, #01347C) !important; border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
                <div>
                    <h3 style="margin: 0; font-size: 1rem; font-weight: 800; color: var(--neutral-800);">
                        Rekam Jejak Logbook Harian Magang (Read-Only)
                    </h3>
                </div>
                <span class="badge-counter-blue">
                    Total: {{ $penempatan->logbooks->count() }} Entri Terisi
                </span>
            </div>

            @if($penempatan->logbooks->isNotEmpty())
                <div class="table-responsive-gentelella" style="overflow-x: auto;">
                    <table class="table jambo_table bulk_action" style="width: 100%; border-collapse: collapse; font-size: 0.8125rem;">
                        <thead>
                            <tr class="headings">
                                <th style="text-align: left; padding: 0.75rem; width: 130px;">Tanggal</th>
                                <th style="text-align: left; padding: 0.75rem; width: 110px;">Minggu</th>
                                <th style="text-align: left; padding: 0.75rem;">Aktivitas Magang</th>
                                <th style="text-align: center; padding: 0.75rem; width: 110px;">Status</th>
                            </tr>
                        </thead>
                        <tbody id="arsipLogbookTableBody">
                            @foreach($penempatan->logbooks as $lb)
                                <tr class="arsip-logbook-row" style="border-bottom: 1px solid var(--neutral-200);">
                                    <td style="padding: 0.75rem; font-weight: 600; white-space: nowrap;">
                                        {{ \Carbon\Carbon::parse($lb->tanggal)->translatedFormat('d M Y') }}
                                    </td>
                                    <td style="padding: 0.75rem; white-space: nowrap;">
                                        <span style="font-weight: 600; color: var(--neutral-700);">Minggu ke-{{ $lb->minggu_ke ?? 1 }}</span>
                                    </td>
                                    <td style="padding: 0.75rem; line-height: 1.45; color: var(--neutral-800);">
                                        {{ $lb->kegiatan ?: '-' }}
                                        @if($lb->link_laporan)
                                            <div style="margin-top: 0.25rem;">
                                                <a href="{{ $lb->link_laporan }}" target="_blank" style="color: #01347C; font-size: 0.75rem; text-decoration: underline;">
                                                    &rarr; Tautan Lampiran
                                                </a>
                                            </div>
                                        @endif
                                    </td>
                                    <td style="padding: 0.75rem; text-align: center; white-space: nowrap;">
                                        <span class="badge-status-blue">
                                            Selesai
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div id="arsipLogbookPagination" style="margin-top: 1.25rem;"></div>
            @else
                <div style="text-align: center; padding: 3rem 1.5rem; color: var(--neutral-600);">
                    <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--neutral-200); display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem auto;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                    </div>
                    <div style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-800);">Tidak Ada Catatan Logbook</div>
                    <p style="font-size: 0.8125rem; margin: 0.25rem 0 0 0;">Belum ada catatan aktivitas harian yang ditemukan untuk peserta ini.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- TAB 4: DOKUMEN PENDAFTARAN --}}
    <div class="arsip-tab-pane" id="tab-administrasi">
        <div class="gentelella-card" style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-top: 3.5px solid var(--magkom-primary-blue, #01347C) !important; border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
                <div>
                    <h3 style="margin: 0; font-size: 1rem; font-weight: 800; color: var(--neutral-800);">
                        Berkas Administrasi &amp; Dokumen Terverifikasi
                    </h3>
                </div>
                <span class="badge-counter-blue">
                    Total: {{ $dokumenList->count() }} Berkas Terdata
                </span>
            </div>

            @if($dokumenList->isNotEmpty())
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1rem;">
                    @foreach($dokumenList as $dok)
                        @php
                            $namaJenis = $dok->jenisDokumen?->nama ?? 'Dokumen Pendaftaran';
                            $ukuranKb = $dok->ukuran ? number_format($dok->ukuran / 1024, 0) . ' KB' : null;
                        @endphp
                        <div class="arsip-doc-card">
                            <div class="arsip-doc-header">
                                <div class="arsip-doc-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                    </svg>
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0.5rem;">
                                        <div class="arsip-doc-title">{{ $namaJenis }}</div>
                                        <span class="badge-status-blue">
                                            Valid
                                        </span>
                                    </div>
                                    <div class="arsip-doc-meta">
                                        <span>File: <strong>{{ $dok->nama_asli ?: basename($dok->path) }}</strong></span>
                                        @if($ukuranKb)
                                            <span>&bull; {{ $ukuranKb }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="arsip-doc-actions">
                                @if($dok->path)
                                    <a href="{{ $dok->getSignedPreviewUrl(15) }}" target="_blank" class="btn-doc-action btn-doc-action--preview" title="Buka pratinjau dokumen">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                        <span>Lihat Dokumen</span>
                                    </a>
                                    <a href="{{ $dok->getSignedDownloadUrl(15) }}" class="btn-doc-action btn-doc-action--download" title="Unduh berkas asli">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                            <polyline points="7 10 12 15 17 10"></polyline>
                                            <line x1="12" y1="15" x2="12" y2="3"></line>
                                        </svg>
                                        <span>Unduh</span>
                                    </a>
                                @else
                                    <span style="font-size: 0.75rem; color: var(--neutral-500); font-style: italic;">
                                        Berkas fisik tidak ditemukan di penyimpanan.
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 3rem 1.5rem; color: var(--neutral-600);">
                    <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--neutral-200); display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem auto;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                    </div>
                    <div style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-800);">Tidak Ada Berkas Unggahan</div>
                    <p style="font-size: 0.8125rem; margin: 0.25rem 0 0 0;">Tidak ada berkas unggahan administrasi yang terdata.</p>
                </div>
            @endif
        </div>
    </div>

</div>

@push('scripts')
    @vite(['resources/js/pages/superadmin/arsip-show.js'])
@endpush
@endsection
