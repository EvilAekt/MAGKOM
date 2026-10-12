@extends('layouts.peserta')

@section('title', 'Biodata & Profil Peserta')
@section('page_title', 'Biodata & Profil Peserta')

@push('styles')
<style>
    .profile-container {
        max-width: 1140px;
        margin: 0 auto;
    }

    /* Profile Hero Header */
    .profile-hero {
        background: linear-gradient(95deg, rgba(6, 65, 204, 0.95) 0%, rgba(6, 65, 204, 0.88) 50%, rgba(6, 65, 204, 0.4) 100%),
                    url('{{ asset('images/dashboard_hero_bg.jpg') }}') right center / cover no-repeat;
        border-radius: var(--radius-lg, 12px);
        padding: 32px 36px;
        color: #ffffff;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        box-shadow: 0 6px 20px rgba(6, 65, 204, 0.16);
        position: relative;
        overflow: hidden;
    }

    .profile-hero::after {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 85% 50%, rgba(255, 255, 255, 0.08) 0%, transparent 60%);
        pointer-events: none;
    }

    .hero-avatar-group {
        display: flex;
        align-items: center;
        gap: 20px;
        position: relative;
        z-index: 2;
    }

    .hero-avatar {
        width: 84px;
        height: 84px;
        border-radius: 50%;
        background: #ffffff;
        color: var(--blue-700);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: 800;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
        border: 4px solid rgba(255, 255, 255, 0.35);
        flex-shrink: 0;
    }

    .hero-meta h1 {
        font-size: var(--text-2xl);
        font-weight: var(--fw-bold);
        margin: 0 0 6px 0;
        line-height: 1.25;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }

    .hero-meta-details {
        font-size: var(--text-sm);
        color: rgba(255, 255, 255, 0.92);
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        line-height: 1.4;
    }

    .hero-completeness-box {
        position: relative;
        z-index: 2;
        background: rgba(15, 23, 42, 0.45);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        padding: 16px 24px;
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.25);
        min-width: 220px;
        text-align: right;
    }

    /* 2 Kolom Asimetris (35% : 65%) */
    .profile-grid-layout {
        display: grid;
        grid-template-columns: 360px 1fr;
        gap: 24px;
        align-items: start;
    }

    /* Card Modern Base */
    .card-modern {
        background: #ffffff;
        border: 1px solid var(--neutral-300);
        border-radius: var(--radius-lg, 12px);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        padding: 24px;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
    }

    .card-modern:last-child {
        margin-bottom: 0;
    }

    /* Student ID Card Simulation (Kolom Kiri) */
    .student-id-card {
        background: linear-gradient(135deg, #FAFBFD 0%, #EFF6FF 100%);
        border: 1px solid #BFDBFE;
        border-radius: var(--radius-lg, 12px);
        padding: 24px;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(7, 81, 255, 0.05);
    }

    .id-card-watermark {
        position: absolute;
        right: -20px;
        bottom: -20px;
        font-size: 110px;
        font-weight: 900;
        color: rgba(7, 81, 255, 0.04);
        pointer-events: none;
        user-select: none;
        line-height: 1;
    }

    .id-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px dashed #BFDBFE;
    }

    .id-card-title {
        font-size: var(--text-xs);
        font-weight: var(--fw-bold);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--blue-700);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .id-row {
        margin-bottom: 12px;
    }

    .id-row:last-child {
        margin-bottom: 0;
    }

    .id-label {
        font-size: 0.6875rem;
        font-weight: 600;
        text-transform: uppercase;
        color: var(--neutral-500);
        letter-spacing: 0.04em;
    }

    .id-value {
        font-size: var(--text-sm);
        font-weight: var(--fw-bold);
        color: var(--neutral-900);
        margin-top: 2px;
        word-break: break-word;
    }

    /* Checklist Card */
    .checklist-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 0;
        border-bottom: 1px solid var(--neutral-200);
        font-size: var(--text-sm);
        color: var(--neutral-700);
    }

    .checklist-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .check-icon-circle {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        flex-shrink: 0;
    }

    .check-icon-circle.is-done {
        background: #ECFDF5;
        color: #059669;
        border: 1px solid #A7F3D0;
    }

    .check-icon-circle.is-pending {
        background: #FEF3C7;
        color: #D97706;
        border: 1px solid #FDE68A;
    }

    /* Card Section Header */
    .section-card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
        padding-bottom: 14px;
        border-bottom: 1px solid var(--neutral-200);
    }

    .section-icon-badge {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .section-title {
        font-size: var(--text-base);
        font-weight: var(--fw-bold);
        color: var(--neutral-900);
        margin: 0;
        line-height: 1.3;
    }

    .section-desc {
        font-size: var(--text-xs);
        color: var(--neutral-600);
        margin: 2px 0 0 0;
    }

    /* Input with Leading Icon */
    .form-group-clean {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 18px;
    }

    .form-label-clean {
        font-size: var(--text-sm);
        font-weight: var(--fw-semibold);
        color: var(--neutral-800);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .input-with-icon {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-leading-icon {
        position: absolute;
        left: 14px;
        color: var(--neutral-500);
        pointer-events: none;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .form-control-custom {
        width: 100%;
        height: 44px;
        padding: 0 14px 0 42px;
        border: 1.5px solid #CBD5E1;
        border-radius: 8px;
        font-size: var(--text-sm);
        color: var(--neutral-900);
        background: #ffffff;
        font-family: inherit;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
        box-sizing: border-box;
    }

    .form-control-custom:focus {
        outline: none;
        border-color: var(--blue-600);
        box-shadow: 0 0 0 3px rgba(7, 81, 255, 0.16);
    }

    .form-control-custom:disabled,
    .form-control-custom[readonly] {
        background: #F8FAFC;
        color: var(--neutral-600);
        border-color: #E2E8F0;
        cursor: not-allowed;
    }

    .form-control-custom.is-invalid {
        border-color: var(--red-600);
        background: #FFF8F8;
    }

    .invalid-text {
        font-size: 0.75rem;
        color: var(--red-600);
        margin-top: 2px;
    }

    /* Emergency Contact Card (Highlighted with Shield) */
    .emergency-card-container {
        background: #FFFBEB;
        border: 1.5px solid #FDE68A;
        border-radius: var(--radius-lg, 12px);
        padding: 20px 22px;
        margin-bottom: 0;
        min-height: 215px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }



    /* Responsif */
    @media (max-width: 1024px) {
        .profile-grid-layout {
            grid-template-columns: 1fr;
        }

        .student-id-card {
            margin-bottom: 20px;
        }
    }

    /* Dark Mode Harmonization ala Menu Tugas */
    [data-theme="dark"] .identity-card,
    [data-theme="dark"] .form-unified-card {
        background: #182234 !important;
        border-color: #26354D !important;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.25) !important;
    }

    [data-theme="dark"] .avatar-preview-box {
        border-color: #182234 !important;
        background: rgba(6, 65, 204, 0.35) !important;
        color: #60A5FA !important;
    }

    [data-theme="dark"] .privacy-notice-box {
        background: #0F172A !important;
        border-color: #26354D !important;
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .field-input {
        background: #0F172A !important;
        border-color: #3E516E !important;
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .field-input:focus {
        border-color: #2563EB !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.25) !important;
    }

    [data-theme="dark"] .field-input:disabled,
    [data-theme="dark"] .field-input[readonly] {
        background: #111C2E !important;
        border-color: #26354D !important;
        color: #8FA2BF !important;
    }

    [data-theme="dark"] .checklist-mini-item {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .checklist-mini-item.done {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .checklist-mini-icon.done {
        background: rgba(6, 78, 59, 0.45) !important;
        border-color: rgba(52, 211, 153, 0.4) !important;
        color: #6EE7B7 !important;
    }

    [data-theme="dark"] .checklist-mini-icon.pending {
        background: #26354D !important;
        color: #8FA2BF !important;
    }

    [data-theme="dark"] .form-section-title-wrap {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .form-action-bar {
        border-top-color: #26354D !important;
    }

    [data-theme="dark"] .profile-header-title {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .profile-header-subtitle {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .field-label {
        color: #E2E8F0 !important;
    }
</style>
@endpush

@section('content')
<div class="profile-container">

    {{-- 1. Profile Hero Header Banner --}}
    <div class="profile-hero">
        <div class="hero-avatar-group">
            <div class="hero-avatar" style="overflow: hidden; padding: 0;">
                @if($profil?->foto_url)
                    <img src="{{ $profil->foto_url }}" alt="Foto Profil" style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    {{ strtoupper(substr($user->nama ?? $user->name ?? 'P', 0, 1)) }}
                @endif
            </div>
            <div class="hero-meta">
                <div style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; background: rgba(15, 23, 42, 0.4); border-radius: 20px; font-size: var(--text-2xs); font-weight: 700; text-transform: uppercase; margin-bottom: 6px; border: 1px solid rgba(255,255,255,0.2);">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #4ADE80;"></span>
                    Akun Peserta Magang
                </div>
                <h1>{{ $user->nama ?? $user->name }}</h1>
                <div class="hero-meta-details">
                    <span>✉ {{ $user->email }}</span>
                    <span>&bull;</span>
                    <span>🏫 {{ $profil->institusi ?? 'Institusi Belum Diisi' }}</span>
                    @if($profil?->nim_nisn)
                        <span>&bull;</span>
                        <span>ID: {{ $profil->nim_nisn }}</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="hero-completeness-box">
            <div style="font-size: var(--text-2xs); text-transform: uppercase; font-weight: 700; color: rgba(255,255,255,0.85); letter-spacing: 0.04em;">
                Kelengkapan Biodata
            </div>
            <div style="font-size: var(--text-2xl); font-weight: 800; margin: 2px 0;">
                {{ $persentaseLengkap }}%
            </div>
            <div style="width: 100%; height: 6px; background: rgba(255,255,255,0.25); border-radius: 999px; overflow: hidden; margin-top: 6px;">
                <div style="height: 100%; width: {{ max(6, $persentaseLengkap) }}%; background: #4ADE80; border-radius: 999px;"></div>
            </div>
        </div>
    </div>

    {{-- 2. Formulir Biodata Utama Peserta --}}
    <form action="{{ route('peserta.profil.simpan') }}" method="POST" id="formBiodataPeserta" enctype="multipart/form-data">
        @csrf

        {{-- Grid 2 Kolom Asimetris (35% : 65%) --}}
        <div class="profile-grid-layout">

            {{-- ========================================================
                 KOLOM KIRI (35%): IDENTITAS PESERTA & STATUS DATA
                 ======================================================== --}}
            <div>
            {{-- Kartu Simulasi Identitas Peserta (Student ID Card) --}}
            <div class="student-id-card">
                <div class="id-card-watermark">MAGKOM</div>

                <div class="id-card-header">
                    <div class="id-card-title">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                        </svg>
                        Identitas Magang
                    </div>
                    <span class="badge {{ $persentaseLengkap >= 100 ? 'badge-success' : 'badge-warning' }}" style="font-size: 0.6875rem;">
                        {{ $persentaseLengkap >= 100 ? '✓ Siap' : 'Belum Lengkap' }}
                    </span>
                </div>

                {{-- Pasfoto Peserta pada ID Card --}}
                <div style="display: flex; gap: 14px; align-items: center; margin-bottom: 16px; padding: 10px 14px; background: #ffffff; border-radius: 10px; border: 1px solid #DBEAFE;">
                    <div style="width: 52px; height: 52px; border-radius: 50%; overflow: hidden; border: 2px solid #3B82F6; background: #EFF6FF; flex-shrink: 0; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(7, 81, 255, 0.1);">
                        @if($profil?->foto_url)
                            <img src="{{ $profil->foto_url }}" alt="Pasfoto" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <span style="font-size: 1.25rem; font-weight: 800; color: var(--blue-700);">{{ strtoupper(substr($user->nama ?? $user->name ?? 'P', 0, 1)) }}</span>
                        @endif
                    </div>
                    <div>
                        <div style="font-size: 0.6875rem; text-transform: uppercase; font-weight: 700; color: var(--blue-700); letter-spacing: 0.05em;">Pasfoto Peserta</div>
                        <div style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-top: 2px;">{{ $profil?->foto ? '✓ Pasfoto Resmi Terdaftar' : 'Belum Ada Foto Profil' }}</div>
                    </div>
                </div>

                <div class="id-row">
                    <div class="id-label">Nama Lengkap Peserta</div>
                    <div class="id-value">{{ $user->nama ?? $user->name }}</div>
                </div>

                <div class="id-row">
                    <div class="id-label">NIM / NISN Identitas</div>
                    <div class="id-value">{{ $profil?->nim_nisn ?? '— Belum Dicatat —' }}</div>
                </div>

                <div class="id-row">
                    <div class="id-label">Sekolah / Perguruan Tinggi</div>
                    <div class="id-value">{{ $profil?->institusi ?? '— Belum Dicatat —' }}</div>
                </div>

                <div class="id-row">
                    <div class="id-label">Program Studi / Jurusan</div>
                    <div class="id-value">
                        {{ $profil?->jurusan?->nama_jurusan ?? ($jurusanList->firstWhere('id', $profil?->jurusan_id)?->nama_jurusan ?? '— Belum Dipilih —') }}
                    </div>
                </div>

                <div class="id-row" style="padding-top: 10px; border-top: 1px dashed #BFDBFE;">
                    <div class="id-label">Status Penempatan</div>
                    <div class="id-value" style="color: var(--blue-700);">
                        @if($penempatan)
                            {{ $penempatan->bidang?->nama_bidang ?? 'Dinas Komunikasi dan Informatika' }}
                        @else
                            Calon Peserta Magang
                        @endif
                    </div>
                </div>
            </div>

            {{-- Kartu Checklist Kelengkapan Profil --}}
            <div class="card-modern">
                <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-900); margin: 0 0 14px 0; display: flex; align-items: center; gap: 8px;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--blue-600);">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    Checklist Kelengkapan
                </h3>

                <div class="checklist-item">
                    <div class="check-icon-circle is-done">✓</div>
                    <div>
                        <div style="font-weight: 600; color: var(--neutral-800);">Akun & Data Diri</div>
                        <div style="font-size: 0.75rem; color: var(--neutral-500);">Nama & Email Terverifikasi</div>
                    </div>
                </div>

                <div class="checklist-item">
                    <div class="check-icon-circle {{ $checklist['foto'] ? 'is-done' : 'is-pending' }}">
                        {{ $checklist['foto'] ? '✓' : '•' }}
                    </div>
                    <div>
                        <div style="font-weight: 600; color: var(--neutral-800);">Pasfoto Resmi</div>
                        <div style="font-size: 0.75rem; color: var(--neutral-500);">Pasfoto Formal Peserta</div>
                    </div>
                </div>

                <div class="checklist-item">
                    <div class="check-icon-circle {{ $checklist['akademik'] ? 'is-done' : 'is-pending' }}">
                        {{ $checklist['akademik'] ? '✓' : '•' }}
                    </div>
                    <div>
                        <div style="font-weight: 600; color: var(--neutral-800);">Informasi Akademik</div>
                        <div style="font-size: 0.75rem; color: var(--neutral-500);">Asal Kampus, NIM/NISN, Jurusan</div>
                    </div>
                </div>

                <div class="checklist-item">
                    <div class="check-icon-circle {{ $checklist['kontak'] ? 'is-done' : 'is-pending' }}">
                        {{ $checklist['kontak'] ? '✓' : '•' }}
                    </div>
                    <div>
                        <div style="font-weight: 600; color: var(--neutral-800);">Kontak Utama</div>
                        <div style="font-size: 0.75rem; color: var(--neutral-500);">Nomor WhatsApp Aktif</div>
                    </div>
                </div>

                <div class="checklist-item">
                    <div class="check-icon-circle {{ $checklist['darurat'] ? 'is-done' : 'is-pending' }}">
                        {{ $checklist['darurat'] ? '✓' : '•' }}
                    </div>
                    <div>
                        <div style="font-weight: 600; color: var(--neutral-800);">Kontak Darurat / Wali</div>
                        <div style="font-size: 0.75rem; color: var(--neutral-500);">Nama & Nomor Wali</div>
                    </div>
                </div>
            </div>

            {{-- Kartu Kontak Darurat & Wali (Ditempatkan di atas Privasi & Keamanan Data) --}}
            <div class="emergency-card-container">
                <div class="section-card-header" style="border-bottom-color: #FDE68A; margin-bottom: 16px; padding-bottom: 8px;">
                    <div class="section-icon-badge" style="background: #FEF3C7; color: #D97706; width: 32px; height: 32px; border-radius: 8px;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="section-title" style="color: #92400E; font-size: 0.9375rem;">Kontak Darurat & Wali</h2>
                        <p class="section-desc" style="color: #B45309; font-size: 0.75rem;">Diperlukan untuk perlindungan & situasi mendesak</p>
                    </div>
                </div>

                <div class="form-group-clean" style="margin-bottom: 12px;">
                    <label for="nama_kontak_darurat" class="form-label-clean" style="font-size: 0.8125rem;">
                        <span>Nama Kontak Darurat</span>
                    </label>
                    <div class="input-with-icon">
                        <span class="input-leading-icon">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </span>
                        <input type="text" id="nama_kontak_darurat" name="nama_kontak_darurat"
                               class="form-control-custom @error('nama_kontak_darurat') is-invalid @enderror"
                               value="{{ old('nama_kontak_darurat', $profil?->nama_kontak_darurat) }}"
                               placeholder="Nama Orang Tua / Wali" style="height: 40px; font-size: 0.8125rem;">
                    </div>
                    @error('nama_kontak_darurat')
                        <span class="invalid-text">{{ $message }}</span>
                    @enderror
                </div>



                <div class="form-group-clean" style="margin-bottom: 0;">
                    <label for="no_kontak_darurat" class="form-label-clean" style="font-size: 0.8125rem;">
                        <span>Nomor Telepon Darurat</span>
                    </label>
                    <div class="input-with-icon">
                        <span class="input-leading-icon">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </span>
                        <input type="tel" id="no_kontak_darurat" name="no_kontak_darurat"
                               class="form-control-custom @error('no_kontak_darurat') is-invalid @enderror"
                               value="{{ old('no_kontak_darurat', $profil?->no_kontak_darurat) }}"
                               placeholder="Contoh: 089876543210" style="height: 40px; font-size: 0.8125rem;">
                    </div>
                    @error('no_kontak_darurat')
                        <span class="invalid-text">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Kartu Informasi Keamanan Data (Minimalis & Bersih) --}}
            <div class="card-modern" style="background: #F8FAFC; border: 1px solid #E2E8F0; padding: 14px 18px; min-height: 54px; display: flex; align-items: center; gap: 10px; margin-top: 16px; margin-bottom: 0;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--blue-600); flex-shrink: 0;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                <div style="font-size: 0.75rem; color: var(--neutral-600); line-height: 1.4;">
                    <strong style="color: var(--neutral-800);">Privasi Data:</strong> Data Anda terenkripsi dan terlindungi untuk verifikasi magang.
                </div>
            </div>
        </div>

        {{-- ========================================================
             KOLOM KANAN (65%): FORMULIR INTERAKTIF TEMATIK
             ======================================================== --}}
        <div>

                {{-- Seksi 1: Pasfoto & Foto Profil Resmi (Compact) --}}
                <div class="card-modern" style="border-left: 4px solid var(--blue-600); padding: 16px 20px; margin-bottom: 20px;">
                    <div class="section-card-header" style="margin-bottom: 12px; padding-bottom: 8px;">
                        <div class="section-icon-badge" style="background: var(--blue-tint); color: var(--blue-600); width: 32px; height: 32px; border-radius: 8px;">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="section-title" style="font-size: 0.9375rem;">Pasfoto & Foto Profil Resmi</h2>
                            <p class="section-desc" style="font-size: 0.75rem;">Format JPG/PNG/WEBP maks 2 MB. Disarankan rasio 1:1 atau 3:4.</p>
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                        {{-- Preview Box Ramping --}}
                        <div style="position: relative; width: 64px; height: 64px; border-radius: 50%; border: 2.5px solid #BFDBFE; box-shadow: 0 2px 8px rgba(6, 65, 204, 0.12); overflow: hidden; background: #F8FAFC; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
                            <img id="avatarPreviewImg" src="{{ $profil?->foto_url ?? '' }}" alt="Preview Foto" style="width: 100%; height: 100%; object-fit: cover; display: {{ $profil?->foto_url ? 'block' : 'none' }};">
                            <div id="avatarFallbackIcon" style="display: {{ $profil?->foto_url ? 'none' : 'flex' }}; align-items: center; justify-content: center; width: 100%; height: 100%; color: var(--blue-700); font-size: 1.5rem; font-weight: 800;">
                                {{ strtoupper(substr($user->nama ?? $user->name ?? 'P', 0, 1)) }}
                            </div>
                        </div>

                        {{-- Action Buttons & Status --}}
                        <div style="flex: 1; min-width: 220px;">
                            <input type="file" id="inputFotoProfil" name="foto" accept="image/jpeg,image/png,image/jpg,image/webp" style="display: none;">
                            <input type="hidden" id="inputHapusFoto" name="hapus_foto" value="0">

                            <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                                <button type="button" class="btn btn-secondary" id="btnPilihFoto" style="height: 36px; padding: 0 14px; font-size: 0.8125rem; font-weight: 600; border-radius: 8px; display: inline-flex; align-items: center; gap: 6px;">
                                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                    </svg>
                                    <span>Pilih Foto</span>
                                </button>
                                @if($profil?->foto)
                                    <button type="button" class="btn" id="btnHapusFoto" style="height: 36px; padding: 0 12px; font-size: 0.8125rem; background: #FEE2E2; color: #DC2626; border: 1px solid #FECACA; font-weight: 600; border-radius: 8px; display: inline-flex; align-items: center; gap: 6px;">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        <span>Hapus</span>
                                    </button>
                                @endif
                                <span id="selectedFileName" style="font-size: 0.8125rem; color: var(--blue-700); font-weight: 600;"></span>
                            </div>
                            @error('foto')
                                <span class="invalid-text" style="display: block; margin-top: 4px;">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Seksi 2: Data Akun & Identitas Dasar (Readonly) --}}
                <div class="card-modern">
                    <div class="section-card-header">
                        <div class="section-icon-badge" style="background: var(--blue-tint); color: var(--blue-600);">
                            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="section-title">Data Akun Terdaftar</h2>
                            <p class="section-desc">Informasi akun login MagKom Anda</p>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group-clean">
                            <label class="form-label-clean">Nama Lengkap</label>
                            <div class="input-with-icon">
                                <span class="input-leading-icon">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </span>
                                <input type="text" class="form-control-custom" value="{{ $user->nama ?? $user->name }}" readonly disabled>
                            </div>
                        </div>

                        <div class="form-group-clean">
                            <label class="form-label-clean">Alamat Email</label>
                            <div class="input-with-icon">
                                <span class="input-leading-icon">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </span>
                                <input type="email" class="form-control-custom" value="{{ $user->email }}" readonly disabled>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Seksi 2: Informasi Akademik & Pendidikan (Graduate Card) --}}
                <div class="card-modern">
                    <div class="section-card-header">
                        <div class="section-icon-badge" style="background: #EFF6FF; color: #1E40AF;">
                            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="section-title">Informasi Akademik & Pendidikan</h2>
                            <p class="section-desc">Data sekolah atau perguruan tinggi asal peserta magang</p>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group-clean" style="grid-column: span 2;">
                            <label for="institusi" class="form-label-clean">
                                <span>Nama Sekolah / Kampus <span style="color: var(--red-600);">*</span></span>
                            </label>
                            <div class="input-with-icon">
                                <span class="input-leading-icon">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </span>
                                <input type="text" id="institusi" name="institusi"
                                       class="form-control-custom @error('institusi') is-invalid @enderror"
                                       value="{{ old('institusi', $profil?->institusi) }}"
                                       placeholder="Contoh: Universitas Gadjah Mada / SMKN 2 Depok Sleman" required>
                            </div>
                            @error('institusi')
                                <span class="invalid-text">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group-clean">
                            <label for="nim_nisn" class="form-label-clean">
                                <span>NISN / NIM <span style="color: var(--red-600);">*</span></span>
                            </label>
                            <div class="input-with-icon">
                                <span class="input-leading-icon">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                                    </svg>
                                </span>
                                <input type="text" id="nim_nisn" name="nim_nisn"
                                       class="form-control-custom @error('nim_nisn') is-invalid @enderror"
                                       value="{{ old('nim_nisn', $profil?->nim_nisn) }}"
                                       placeholder="Nomor identitas siswa/mahasiswa" required>
                            </div>
                            @error('nim_nisn')
                                <span class="invalid-text">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group-clean">
                            <label for="jenjang" class="form-label-clean">
                                <span>Jenjang Pendidikan <span style="color: var(--red-600);">*</span></span>
                            </label>
                            <div class="input-with-icon">
                                <span class="input-leading-icon">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </span>
                                <select id="jenjang" name="jenjang" class="form-control-custom @error('jenjang') is-invalid @enderror" required>
                                    <option value="">-- Pilih Jenjang --</option>
                                    <option value="smk" {{ old('jenjang', $profil?->jenjang?->value ?? $profil?->jenjang) === 'smk' ? 'selected' : '' }}>SMK (Siswa)</option>
                                    <option value="d3" {{ old('jenjang', $profil?->jenjang?->value ?? $profil?->jenjang) === 'd3' ? 'selected' : '' }}>D3 (Diploma Tiga)</option>
                                    <option value="s1" {{ old('jenjang', $profil?->jenjang?->value ?? $profil?->jenjang) === 's1' ? 'selected' : '' }}>S1 / D4 (Sarjana)</option>
                                </select>
                            </div>
                            @error('jenjang')
                                <span class="invalid-text">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group-clean" style="grid-column: span 2;">
                            <label for="jurusan_id" class="form-label-clean">
                                <span>Jurusan / Program Studi <span style="color: var(--red-600);">*</span></span>
                            </label>
                            <div class="input-with-icon">
                                <span class="input-leading-icon">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                </span>
                                <select id="jurusan_id" name="jurusan_id" class="form-control-custom @error('jurusan_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Jurusan / Program Studi --</option>
                                    @foreach($jurusanList as $jurusan)
                                        <option value="{{ $jurusan->id }}" {{ old('jurusan_id', $profil?->jurusan_id) == $jurusan->id ? 'selected' : '' }}>
                                            {{ $jurusan->nama_jurusan }} ({{ strtoupper($jurusan->kategori) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('jurusan_id')
                                <span class="invalid-text">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Seksi 4: Kontak Utama & Komunikasi (WhatsApp) --}}
                <div class="card-modern" style="min-height: 215px; margin-bottom: 0; display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <div class="section-card-header" style="margin-bottom: 14px;">
                            <div class="section-icon-badge" style="background: #ECFDF5; color: #059669;">
                                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="section-title">Kontak Utama & WhatsApp</h2>
                                <p class="section-desc">Nomor kontak aktif untuk koordinasi pembimbingan magang</p>
                            </div>
                        </div>

                        <div class="form-group-clean" style="margin-bottom: 0;">
                            <label for="no_hp" class="form-label-clean">
                                <span>Nomor WhatsApp / Telepon Aktif <span style="color: var(--red-600);">*</span></span>
                            </label>
                            <div class="input-with-icon">
                                <span class="input-leading-icon">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                </span>
                                <input type="tel" id="no_hp" name="no_hp"
                                       class="form-control-custom @error('no_hp') is-invalid @enderror"
                                       value="{{ old('no_hp', $profil?->no_hp ?? $user->no_telp) }}"
                                       placeholder="Contoh: 081234567890" required>
                            </div>
                            @error('no_hp')
                                <span class="invalid-text">{{ $message }}</span>
                            @enderror
                            <span style="font-size: 0.75rem; color: var(--neutral-500); margin-top: 6px; display: block;">
                                Nomor aktif terhubung WhatsApp untuk koordinasi pembimbing.
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Action Bar Simpan (Sejajar dengan Privasi Data di Kolom Kiri) --}}
                <div style="min-height: 54px; display: flex; align-items: center; justify-content: flex-end; margin-top: 16px; margin-bottom: 0;">
                    <button type="submit" class="btn btn-primary" id="btnSimpanProfil" style="height: 46px; padding: 0 28px; font-size: 0.9375rem; font-weight: 600; box-shadow: 0 4px 14px rgba(7, 81, 255, 0.25); display: inline-flex; align-items: center; gap: 8px;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan Biodata Profil
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>

@push('scripts')
<script>
    // Live preview dan handling foto profil
    const inputFoto = document.getElementById('inputFotoProfil');
    const btnPilihFoto = document.getElementById('btnPilihFoto');
    const btnHapusFoto = document.getElementById('btnHapusFoto');
    const inputHapusFoto = document.getElementById('inputHapusFoto');
    const previewImg = document.getElementById('avatarPreviewImg');
    const fallbackIcon = document.getElementById('avatarFallbackIcon');
    const selectedFileName = document.getElementById('selectedFileName');

    btnPilihFoto?.addEventListener('click', () => {
        inputFoto?.click();
    });

    inputFoto?.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran berkas melebihi batas maksimal 2 MB.');
                this.value = '';
                return;
            }
            if (inputHapusFoto) inputHapusFoto.value = '0';
            if (selectedFileName) selectedFileName.textContent = '✓ ' + file.name;
            const reader = new FileReader();
            reader.onload = function(e) {
                if (previewImg) {
                    previewImg.src = e.target.result;
                    previewImg.style.display = 'block';
                }
                if (fallbackIcon) {
                    fallbackIcon.style.display = 'none';
                }
            };
            reader.readAsDataURL(file);
        }
    });

    btnHapusFoto?.addEventListener('click', function() {
        if (confirm('Apakah Anda yakin ingin menghapus foto profil dan kembali menggunakan avatar inisial bawaan?')) {
            if (inputHapusFoto) inputHapusFoto.value = '1';
            if (inputFoto) inputFoto.value = '';
            if (previewImg) {
                previewImg.src = '';
                previewImg.style.display = 'none';
            }
            if (fallbackIcon) {
                fallbackIcon.style.display = 'flex';
            }
            if (selectedFileName) selectedFileName.textContent = '(Foto akan dihapus setelah Anda menekan tombol simpan)';
        }
    });

    // Anti double submit
    document.getElementById('formBiodataPeserta')?.addEventListener('submit', function() {
        const btn = document.getElementById('btnSimpanProfil');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="animation: spin 1s linear infinite;">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" style="opacity: 0.25;"></circle>
                    <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" style="opacity: 0.75;"></path>
                </svg>
                Menyimpan Data...
            `;
        }
    });
</script>
<style>
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>
@endpush
@endsection
