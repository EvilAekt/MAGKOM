@extends('layouts.peserta')

@section('title', 'Pendaftaran Magang')
@section('page_title', 'Pendaftaran Magang')

@push('styles')
<style>
    /* ========================================================
       MAGKOM GENTELELLA DESIGN SYSTEM: PENDAFTARAN INDEX
       ======================================================== */
    .pendaftaran-container {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    /* Hero Banner MagKom Sleman */
    .pendaftaran-hero {
        background: linear-gradient(135deg, #0641CC 0%, #0751FF 60%, #1E40AF 100%);
        border-radius: var(--radius-lg, 12px);
        padding: 28px 32px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 25px -5px rgba(7, 81, 255, 0.25);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 24px;
        flex-wrap: wrap;
    }

    .pendaftaran-hero::before {
        content: 'MAGKOM';
        position: absolute;
        right: -15px;
        bottom: -25px;
        font-size: 5.5rem;
        font-weight: 900;
        letter-spacing: -0.05em;
        color: rgba(255, 255, 255, 0.06);
        pointer-events: none;
        user-select: none;
    }

    .hero-text-content {
        position: relative;
        z-index: 2;
        max-width: 640px;
    }

    .hero-badge-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 10px;
    }

    .hero-badge-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #4ADE80;
        box-shadow: 0 0 6px #4ADE80;
    }

    .hero-title {
        font-size: 1.5rem;
        font-weight: 800;
        margin: 0 0 8px 0;
        line-height: 1.3;
        letter-spacing: -0.02em;
        color: #ffffff;
    }

    .hero-desc {
        font-size: 0.875rem;
        color: rgba(255, 255, 255, 0.9);
        line-height: 1.55;
        margin: 0 0 14px 0;
    }

    .hero-meta-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .hero-meta-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        background: rgba(0, 0, 0, 0.25);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 500;
        color: #ffffff;
    }

    .hero-cta-box {
        position: relative;
        z-index: 2;
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: var(--radius-lg, 12px);
        padding: 18px 22px;
        display: flex;
        flex-direction: column;
        align-items: stretch;
        gap: 10px;
        min-width: 250px;
    }

    /* Active Application Spotlight Card (Gentelella Standard) */
    .active-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 16px;
        padding-bottom: 14px;
        border-bottom: 1px solid var(--neutral-200, #E2E8F0);
    }

    .active-reg-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        background: #EFF6FF;
        color: #1D4ED8;
        border: 1px solid #BFDBFE;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.02em;
    }

    .active-grid-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }

    .active-info-item {
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        padding: 12px 14px;
    }

    .active-info-label {
        font-size: 0.6875rem;
        text-transform: uppercase;
        font-weight: 700;
        color: var(--neutral-500, #64748B);
        letter-spacing: 0.04em;
        margin-bottom: 4px;
    }

    .active-info-value {
        font-size: 0.9375rem;
        font-weight: 700;
        color: var(--neutral-800, #1E293B);
    }

    .active-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        padding-top: 16px;
        border-top: 1px dashed var(--neutral-200, #E2E8F0);
    }

    .active-card-note {
        font-size: 0.75rem;
        color: var(--neutral-500, #64748B);
        margin: 0;
        line-height: 1.4;
        display: flex;
        align-items: center;
        gap: 8px;
        max-width: 580px;
    }

    /* Kuota Catalog Cards Grid */
    .kuota-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 18px;
    }

    .kuota-card {
        background: #ffffff;
        border: 1px solid var(--neutral-300, #CBD5E1);
        border-radius: var(--radius-lg, 12px);
        padding: 20px;
        transition: all 0.25s ease;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
    }

    .kuota-card:hover {
        border-color: #0751FF;
        box-shadow: 0 8px 24px rgba(7, 81, 255, 0.12);
        transform: translateY(-2px);
    }

    .kuota-month-name {
        font-size: 1.125rem;
        font-weight: 800;
        color: var(--neutral-900, #0F172A);
        margin: 0 0 4px 0;
    }

    .kuota-dates {
        font-size: 0.75rem;
        color: var(--neutral-500, #64748B);
        margin-bottom: 16px;
    }

    .kuota-progress-box {
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        padding: 10px 12px;
        margin-bottom: 16px;
    }

    .kuota-progress-bar-bg {
        width: 100%;
        height: 6px;
        background: #E2E8F0;
        border-radius: 999px;
        overflow: hidden;
        margin-top: 6px;
    }

    .kuota-progress-bar-fill {
        height: 100%;
        border-radius: 999px;
        transition: width 0.3s ease;
    }

    /* Custom Table Styles */
    .table-custom {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .table-custom th {
        background: #F8FAFC;
        padding: 12px 18px;
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--neutral-600, #475569);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid #E2E8F0;
    }

    .table-custom td {
        padding: 14px 18px;
        font-size: 0.875rem;
        border-bottom: 1px solid #F1F5F9;
        color: var(--neutral-800, #1E293B);
        vertical-align: middle;
    }

    .table-custom tr:hover td {
        background: #F8FAFC;
    }

    /* Modal Styling */
    .modal-backdrop-blur {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 1050;
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        align-items: center;
        justify-content: center;
        padding: 20px;
        animation: fadeIn 0.2s ease;
    }

    .modal-backdrop-blur.open {
        display: flex;
    }

    .modal-dialog-modern {
        background: #ffffff;
        border-radius: var(--radius-xl, 16px);
        width: 100%;
        max-width: 600px;
        padding: 28px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        animation: slideUp 0.25s ease;
        max-height: 90vh;
        overflow-y: auto;
    }

    .duration-chips-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin-top: 8px;
    }

    .duration-chip-btn {
        border: 1.5px solid #CBD5E1;
        border-radius: 8px;
        background: #ffffff;
        padding: 8px 10px;
        min-height: 52px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 2px;
        cursor: pointer;
        transition: all 0.2s ease;
        user-select: none;
    }

    .duration-chip-btn:hover {
        border-color: #0751FF;
        background: #EFF6FF;
    }

    .duration-chip-btn.is-active {
        border-color: #0641CC;
        background: #EFF6FF;
        color: #0641CC;
        font-weight: 700;
        box-shadow: 0 0 0 2px rgba(7, 81, 255, 0.2);
    }

    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }

    /* Dark Mode Harmonization */
    [data-theme="dark"] .active-info-item,
    [data-theme="dark"] .kuota-progress-box {
        background: #0F172A !important;
        border-color: #26354D !important;
    }

    [data-theme="dark"] .active-info-label,
    [data-theme="dark"] .kuota-dates,
    [data-theme="dark"] .active-card-note {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .active-info-value,
    [data-theme="dark"] .kuota-month-name {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .active-reg-badge {
        background: #0F172A !important;
        border-color: #26354D !important;
        color: #60A5FA !important;
    }

    [data-theme="dark"] .kuota-card {
        background: #182234 !important;
        border-color: #26354D !important;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25) !important;
    }

    [data-theme="dark"] .kuota-card:hover {
        border-color: #0751FF !important;
        box-shadow: 0 8px 24px rgba(7, 81, 255, 0.2) !important;
    }

    [data-theme="dark"] .kuota-progress-bar-bg {
        background: #26354D !important;
    }

    [data-theme="dark"] .table-custom th {
        background: #0F172A !important;
        color: #94A3B8 !important;
        border-bottom-color: #26354D !important;
    }

    [data-theme="dark"] .table-custom td {
        border-bottom-color: #26354D !important;
        color: #E2E8F0 !important;
    }

    [data-theme="dark"] .table-custom tr:hover td {
        background: rgba(30, 41, 59, 0.5) !important;
    }

    [data-theme="dark"] .modal-dialog-modern {
        background: #182234 !important;
        border: 1px solid #26354D !important;
        color: #F8FAFC !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6) !important;
    }

    [data-theme="dark"] .duration-chip-btn {
        background: #0F172A !important;
        border-color: #3E516E !important;
        color: #E2E8F0 !important;
    }

    [data-theme="dark"] .duration-chip-btn:hover {
        border-color: #60A5FA !important;
        background: #1E293B !important;
    }

    [data-theme="dark"] .duration-chip-btn.is-active {
        border-color: #2563EB !important;
        background: rgba(30, 58, 138, 0.4) !important;
        color: #93C5FD !important;
    }
</style>
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
                Pilih periode bulan pelaksanaan dan ajukan permohonan magang resmi sesuai kompetensi jurusan Anda di lingkungan Dinas Komunikasi dan Informatika Kabupaten Sleman.
            </p>
            <div class="hero-meta-pills">
                <div class="hero-meta-pill">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <span>{{ $profil?->institusi ?? 'Institusi Belum Dicatat' }}</span>
                </div>
                <div class="hero-meta-pill">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ $profil?->jurusan?->nama_jurusan ?? 'Jurusan Belum Dipilih' }}</span>
                </div>
                @if($profil?->jenjang)
                    <div class="hero-meta-pill">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-5.825-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                        <span>{{ strtoupper($profil->jenjang?->value ?? $profil->jenjang) }}</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="hero-cta-box">
            <div style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: rgba(255,255,255,0.85); letter-spacing: 0.04em;">
                Status Pengajuan
            </div>
            @if($pendaftaranAktif)
                <div style="font-size: 0.9375rem; font-weight: 800; color: #4ADE80; display: flex; align-items: center; gap: 6px;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #4ADE80; display: inline-block;"></span>
                    1 Pengajuan Aktif
                </div>
                <a href="{{ route('peserta.pendaftaran.detail', $pendaftaranAktif) }}" class="btn btn-secondary" style="width: 100%;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Buka Berkas #REG-{{ str_pad((string)$pendaftaranAktif->id, 5, '0', STR_PAD_LEFT) }}
                </a>
            @elseif($profilLengkap)
                <div style="font-size: 0.9375rem; font-weight: 800; color: #ffffff;">
                    Siap Mengajukan
                </div>
                <button type="button" class="btn btn-primary" onclick="openModalPendaftaran()" style="width: 100%;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Buat Pengajuan Baru
                </button>
            @else
                <div style="font-size: 0.9375rem; font-weight: 800; color: #FCD34D;">
                    Profil Belum Lengkap
                </div>
                <a href="{{ route('peserta.profil.show') }}" class="btn btn-secondary" style="width: 100%;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Lengkapi Profil Dulu
                </a>
            @endif
        </div>
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
         3. SPOTLIGHT CARD PENGAJUAN AKTIF (GENTELELLA CARD)
         ======================================================== --}}
    @if($pendaftaranAktif)
        <x-gentelella.card style="border-top: 4px solid #0751FF;">
            <div class="active-card-header">
                <div>
                    <div class="active-reg-badge">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        #REG-{{ str_pad((string)$pendaftaranAktif->id, 5, '0', STR_PAD_LEFT) }}
                    </div>
                    <h2 style="font-size: 1.125rem; font-weight: 800; margin: 8px 0 2px 0;">
                        Pengajuan Magang Aktif Sedang Diproses
                    </h2>
                    <p style="font-size: 0.8125rem; color: var(--neutral-500, #64748B); margin: 0;">
                        Diajukan pada {{ $pendaftaranAktif->created_at->translatedFormat('d F Y, H:i') }} WIB
                    </p>
                </div>

                <div>
                    @php
                        $valStatus = $pendaftaranAktif->status->value ?? (string)$pendaftaranAktif->status;
                    @endphp
                    @if($valStatus === 'berkas_belum_lengkap')
                        <x-gentelella.badge variant="secondary" style="font-size: 0.75rem; padding: 6px 12px;">
                            Berkas Belum Lengkap
                        </x-gentelella.badge>
                    @elseif($valStatus === 'diajukan')
                        <x-gentelella.badge variant="warning" style="font-size: 0.75rem; padding: 6px 12px;">
                            Menunggu Verifikasi Admin
                        </x-gentelella.badge>
                    @elseif($valStatus === 'waiting_list')
                        <x-gentelella.badge variant="info" style="font-size: 0.75rem; padding: 6px 12px;">
                            Waiting List (Antrean)
                        </x-gentelella.badge>
                    @elseif($valStatus === 'perlu_perbaikan')
                        <x-gentelella.badge variant="warning" style="font-size: 0.75rem; padding: 6px 12px;">
                            Perlu Perbaikan Berkas
                        </x-gentelella.badge>
                    @elseif($valStatus === 'diterima')
                        <x-gentelella.badge variant="success" style="font-size: 0.75rem; padding: 6px 12px;">
                            Selamat! Diterima Magang
                        </x-gentelella.badge>
                    @else
                        <x-gentelella.badge variant="secondary">{{ $valStatus }}</x-gentelella.badge>
                    @endif
                </div>
            </div>

            <div class="active-grid-info">
                <div class="active-info-item">
                    <div class="active-info-label">Periode Bulan Magang</div>
                    <div class="active-info-value" style="display: flex; align-items: center; gap: 6px; color: #0751FF;">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span>{{ $pendaftaranAktif->bulanMagang?->bulan_mulai?->translatedFormat('F Y') }}</span>
                    </div>
                </div>

                <div class="active-info-item">
                    <div class="active-info-label">Jurusan Kuota Terdaftar</div>
                    <div class="active-info-value" style="display: flex; align-items: center; gap: 6px;">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-5.825-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                        <span>{{ $pendaftaranAktif->kuota?->jurusan?->nama_jurusan ?? '-' }}</span>
                    </div>
                </div>

                <div class="active-info-item">
                    <div class="active-info-label">Rencana Durasi Magang</div>
                    <div class="active-info-value" style="display: flex; align-items: center; gap: 6px;">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        <span>{{ $pendaftaranAktif->durasi_bulan }} Bulan</span>
                    </div>
                </div>

                <div class="active-info-item">
                    <div class="active-info-label">Status Penempatan</div>
                    <div class="active-info-value" style="display: flex; align-items: center; gap: 6px;">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <span>{{ $pendaftaranAktif->penempatan?->bidang?->nama_bidang ?? 'Tahap Seleksi Berkas' }}</span>
                    </div>
                </div>
            </div>

            <div class="active-card-footer">
                <p class="active-card-note">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: #0751FF; flex-shrink: 0;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                    <span>Sesuai ketentuan sistem, pengajuan baru terkunci saat Anda masih memiliki pengajuan aktif yang sedang berjalan.</span>
                </p>

                <div style="margin-left: auto;">
                    <a href="{{ route('peserta.pendaftaran.detail', $pendaftaranAktif) }}" class="btn btn-primary">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Buka Berkas & Detail Pengajuan
                    </a>
                </div>
            </div>
        </x-gentelella.card>
    @endif

    {{-- ========================================================
         4. KATALOG KUOTA BULAN MAGANG (X-PANEL GENTELELLA)
         ======================================================== --}}
    @if(!$pendaftaranAktif && $profilLengkap)
        <x-gentelella.x-panel title="Pilihan Periode & Kuota Magang Tersedia" subtitle="Ketersediaan kuota resmi yang dialokasikan khusus untuk jurusan {{ $profil->jurusan?->nama_jurusan }}">
            @if($kuotaList->isEmpty())
                <div style="text-align: center; padding: 36px 20px; background: #F8FAFC; border: 1px dashed var(--neutral-300, #CBD5E1); border-radius: 8px;">
                    <div style="font-size: 2rem; margin-bottom: 8px;">📅</div>
                    <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-800, #1E293B); margin: 0 0 4px 0;">Belum Ada Kuota Magang Terbuka</h3>
                    <p style="font-size: 0.8125rem; color: var(--neutral-500, #64748B); max-width: 460px; margin: 0 auto;">
                        Saat ini belum ada periode bulan magang yang dibuka untuk jurusan Anda. Silakan hubungi admin kedinasan atau pantau pengumuman berkala.
                    </p>
                </div>
            @else
                <div class="kuota-grid">
                    @foreach($kuotaList as $kuotaItem)
                        @php
                            $sisaKursi = max(0, $kuotaItem->jumlah - $kuotaItem->terisi);
                            $persenTerisi = $kuotaItem->jumlah > 0 ? min(100, round(($kuotaItem->terisi / $kuotaItem->jumlah) * 100)) : 100;
                            $bulanMulaiFormatted = $kuotaItem->bulanMagang?->bulan_mulai?->translatedFormat('F Y');
                        @endphp
                        <div class="kuota-card">
                            <div>
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                    <x-gentelella.badge variant="{{ $sisaKursi > 0 ? 'success' : 'warning' }}" style="font-size: 0.6875rem;">
                                        {{ $sisaKursi > 0 ? '✓ Kuota Tersedia' : 'Penuh (Waiting List)' }}
                                    </x-gentelella.badge>
                                    <span style="font-size: 0.6875rem; font-weight: 700; color: var(--neutral-500, #64748B);">
                                        Total Kuota: {{ $kuotaItem->jumlah }}
                                    </span>
                                </div>

                                <h3 class="kuota-month-name">{{ $bulanMulaiFormatted }}</h3>
                                <div class="kuota-dates" style="display: flex; align-items: center; gap: 5px;">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span>Pelaksanaan mulai: {{ $kuotaItem->bulanMagang?->bulan_mulai?->translatedFormat('d M Y') ?? '-' }}</span>
                                </div>

                                <div class="kuota-progress-box">
                                    <div style="display: flex; justify-content: space-between; font-size: 0.75rem; font-weight: 700;">
                                        <span style="color: {{ $sisaKursi > 0 ? '#166534' : '#92400E' }};">
                                            {{ $sisaKursi > 0 ? "Tersisa {$sisaKursi} Kursi" : 'Kuota Penuh' }}
                                        </span>
                                        <span style="color: var(--neutral-500, #64748B);">
                                            {{ $kuotaItem->terisi }}/{{ $kuotaItem->jumlah }} Terisi
                                        </span>
                                    </div>
                                    <div class="kuota-progress-bar-bg">
                                        <div class="kuota-progress-bar-fill" style="width: {{ $persenTerisi }}%; background: {{ $sisaKursi > 0 ? '#22C55E' : '#EAB308' }};"></div>
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-secondary" onclick="openModalWithKuota('{{ $kuotaItem->id }}')" style="width: 100%;">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Pilih Periode Ini
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-gentelella.x-panel>
    @endif

    {{-- ========================================================
         5. TABEL RIWAYAT PENDAFTARAN MAGANG (X-PANEL GENTELELLA)
         ======================================================== --}}
    <x-gentelella.x-panel title="Riwayat Pendaftaran Magang" subtitle="Daftar pengajuan magang yang telah Anda daftarkan di Dinas Kominfo Sleman.">
        <x-slot:toolbar>
            @if($pendaftaranAktif)
                <button type="button" class="btn btn-secondary" disabled title="Anda masih memiliki pengajuan aktif">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0110 0v4"></path>
                    </svg>
                    Buat Pengajuan Baru (Terkunci)
                </button>
            @elseif($profilLengkap)
                <button type="button" class="btn btn-primary" onclick="openModalPendaftaran()">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Buat Pengajuan Baru
                </button>
            @else
                <button type="button" class="btn btn-secondary" disabled title="Lengkapi profil terlebih dahulu">
                    Buat Pengajuan Baru (Profil Belum Lengkap)
                </button>
            @endif
        </x-slot:toolbar>

        @if($pendaftaranList->isEmpty())
            <div style="text-align: center; padding: 48px 24px;">
                <div style="width: 56px; height: 56px; margin: 0 auto 16px auto; background: #EFF6FF; color: #0751FF; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-800, #1E293B); margin: 0 0 6px 0;">Belum Ada Pengajuan Magang</h3>
                <p style="font-size: 0.8125rem; color: var(--neutral-500, #64748B); max-width: 420px; margin: 0 auto 20px auto; line-height: 1.5;">
                    Anda belum pernah mengajukan permohonan magang. Klik tombol "Buat Pengajuan Baru" di atas untuk memilih periode bulan dan kuota magang.
                </p>
                @if($profilLengkap && !$pendaftaranAktif)
                    <button type="button" class="btn btn-primary" onclick="openModalPendaftaran()">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Daftar Magang Sekarang
                    </button>
                @endif
            </div>
        @else
            <div style="overflow-x: auto;">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>No. Pendaftaran</th>
                            <th>Periode Magang</th>
                            <th>Jurusan & Durasi</th>
                            <th>Status Pengajuan</th>
                            <th>Tanggal Buat</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendaftaranList as $item)
                            <tr>
                                <td>
                                    <strong style="color: #0751FF; font-weight: 800;">
                                        #REG-{{ str_pad((string)$item->id, 5, '0', STR_PAD_LEFT) }}
                                    </strong>
                                </td>
                                <td>
                                    <div style="font-weight: 700; color: var(--neutral-900, #0F172A);">
                                        {{ $item->bulanMagang?->bulan_mulai?->translatedFormat('F Y') }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--neutral-500, #64748B);">
                                        Mulai: {{ $item->bulanMagang?->bulan_mulai ? $item->bulanMagang->bulan_mulai->translatedFormat('d M Y') : '-' }}
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: var(--neutral-800, #1E293B);">
                                        {{ $item->kuota?->jurusan?->nama_jurusan ?? '-' }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--neutral-500, #64748B);">
                                        Rencana: <strong>{{ $item->durasi_bulan }} Bulan</strong>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $val = $item->status->value ?? (string)$item->status;
                                    @endphp
                                    @if($val === 'berkas_belum_lengkap')
                                        <x-gentelella.badge variant="secondary">Berkas Belum Lengkap</x-gentelella.badge>
                                    @elseif($val === 'diajukan')
                                        <x-gentelella.badge variant="warning">Menunggu Verifikasi</x-gentelella.badge>
                                    @elseif($val === 'waiting_list')
                                        <x-gentelella.badge variant="info">Waiting List</x-gentelella.badge>
                                    @elseif($val === 'perlu_perbaikan')
                                        <x-gentelella.badge variant="warning">Perlu Perbaikan</x-gentelella.badge>
                                    @elseif($val === 'diterima')
                                        <x-gentelella.badge variant="success">Diterima</x-gentelella.badge>
                                    @elseif($val === 'ditolak')
                                        <x-gentelella.badge variant="danger">Ditolak</x-gentelella.badge>
                                    @elseif($val === 'dibatalkan')
                                        <x-gentelella.badge variant="secondary">Dibatalkan</x-gentelella.badge>
                                    @else
                                        <x-gentelella.badge variant="secondary">{{ $val }}</x-gentelella.badge>
                                    @endif
                                </td>
                                <td style="font-size: 0.8125rem; color: var(--neutral-600, #475569);">
                                    {{ $item->created_at->translatedFormat('d M Y, H:i') }}
                                </td>
                                <td style="text-align: right; white-space: nowrap;">
                                    <a href="{{ route('peserta.pendaftaran.detail', $item) }}" class="btn btn-secondary" style="height: 34px; padding: 0 14px; font-size: 0.8125rem; display: inline-flex; align-items: center; gap: 6px;">
                                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-gentelella.x-panel>
</div>

{{-- ========================================================
     6. MODAL INTERAKTIF PENGAJUAN MAGANG BARU
     ======================================================== --}}
@if($profilLengkap && !$pendaftaranAktif)
<div class="modal-backdrop-blur" id="pendaftaranModal" onclick="if(event.target === this) closeModalPendaftaran()">
    <div class="modal-dialog-modern">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid var(--neutral-200, #E2E8F0);">
            <div>
                <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0 0 4px 0;">
                    Formulir Pengajuan Magang Baru
                </h3>
                <p style="font-size: 0.8125rem; color: var(--neutral-500, #64748B); margin: 0;">
                    Pilih periode bulan magang dan tentukan rencana durasi magang Anda.
                </p>
            </div>
            <button type="button" onclick="closeModalPendaftaran()" aria-label="Tutup Formulir" style="width: 32px; height: 32px; border-radius: 8px; border: 1px solid #CBD5E1; background: #F8FAFC; color: var(--neutral-500, #64748B); display: inline-flex; align-items: center; justify-content: center; cursor: pointer; padding: 0;">
                &times;
            </button>
        </div>

        <form action="{{ route('peserta.pendaftaran.store') }}" method="POST" id="formPendaftaranBaru" onsubmit="handleFormSubmit(this)">
            @csrf

            <div style="display: flex; flex-direction: column; gap: 18px;">
                {{-- 1. Info Jurusan Profil Peserta --}}
                <div style="padding: 14px 16px; background: #EFF6FF; border-radius: 10px; border: 1px solid #BFDBFE;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                        <span style="font-size: 0.6875rem; color: #1D4ED8; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Jurusan Terdaftar Anda</span>
                        <x-gentelella.badge variant="info" style="font-size: 0.6875rem; padding: 2px 8px;">{{ strtoupper($profil->jenjang?->value ?? $profil->jenjang) }}</x-gentelella.badge>
                    </div>
                    <div style="font-size: 0.9375rem; font-weight: 800; color: var(--neutral-900, #0F172A);">
                        {{ $profil->jurusan?->nama_jurusan }}
                    </div>
                    <div style="font-size: 0.75rem; color: var(--neutral-600, #475569); margin-top: 2px;">
                        Institusi: {{ $profil->institusi }}
                    </div>
                </div>

                {{-- 2. Dropdown Periode Bulan Magang & Kuota Aktif --}}
                <div class="form-group">
                    <label for="kuota_id" class="form-label" style="font-weight: 700;">
                        Pilih Periode Bulan Magang <span style="color: #DC2626;">*</span>
                    </label>
                    <select id="kuota_id" name="kuota_id" class="form-control" required onchange="handleKuotaChange(this)">
                        <option value="">-- Pilih Periode Bulan Magang --</option>
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
                    <span style="font-size: 0.75rem; color: var(--neutral-500, #64748B); margin-top: 4px; display: block;">
                        Hanya menampilkan bulan magang yang berstatus Tersedia untuk jurusan Anda.
                    </span>
                </div>

                {{-- Hidden input bulan_magang_id yang diisi otomatis via JS dari select kuota_id --}}
                <input type="hidden" id="bulan_magang_id" name="bulan_magang_id" value="{{ old('bulan_magang_id') }}">

                {{-- 3. Pilihan Durasi Magang Berbasis Chip/Buttons --}}
                <div class="form-group">
                    <label for="durasi_bulan" class="form-label" style="font-weight: 700;">
                        Rencana Durasi Magang <span style="color: #DC2626;">*</span>
                    </label>

                    <div class="duration-chips-grid">
                        @for($d = 1; $d <= 6; $d++)
                            <div class="duration-chip-btn {{ old('durasi_bulan', 3) == $d ? 'is-active' : '' }}" onclick="selectDurationChip({{ $d }})">
                                <span style="font-size: 0.875rem; font-weight: 700;">{{ $d }} Bulan</span>
                                <span style="font-size: 0.6875rem; color: {{ $d == 3 ? '#0284C7' : ($d == 6 ? '#059669' : '#64748B') }}; font-weight: 600;">
                                    {{ $d == 3 ? 'Standar' : ($d == 6 ? 'Maksimal' : 'Opsi') }}
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

                    <span style="font-size: 0.75rem; color: var(--neutral-500, #64748B); margin-top: 6px; display: block;">
                        Sesuai rekomendasi surat pengantar institusi pendidikan Anda (1 s/d 6 bulan).
                    </span>
                </div>

                {{-- Live Preview Rentang Tanggal --}}
                <div id="previewPeriodeBox" style="display: none; padding: 14px 16px; background: #F8FAFC; border-radius: 10px; border: 1.5px dashed #CBD5E1; font-size: 0.8125rem;">
                    <div style="font-weight: 700; color: var(--neutral-800, #1E293B); margin-bottom: 2px; display: flex; align-items: center; gap: 6px;">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span>Estimasi Rentang Pelaksanaan:</span>
                    </div>
                    <div id="previewPeriodeText" style="color: #0751FF; font-weight: 800; font-size: 0.875rem;"></div>
                </div>
            </div>

            <div style="margin-top: 24px; display: flex; justify-content: flex-end; align-items: center; gap: 12px; padding-top: 16px; border-top: 1px solid var(--neutral-200, #E2E8F0);">
                <button type="button" class="btn btn-secondary" onclick="closeModalPendaftaran()">Batal</button>
                <button type="submit" class="btn btn-primary" id="btnSubmitPendaftaran">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Lanjut ke Pengunggahan Berkas
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
    });
</script>
<style>
@keyframes spin { to { transform: rotate(360deg); } }
</style>
@endpush
@endsection
