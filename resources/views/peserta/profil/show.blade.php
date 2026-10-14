@extends('layouts.peserta')

@section('title', 'Biodata & Profil Peserta')
@section('page_title', 'Biodata & Profil Peserta')

@push('styles')
<style>
    .profile-page-wrapper {
        max-width: 1160px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    /* Page Header */
    .profile-header-clean {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        padding-bottom: 4px;
    }

    .profile-header-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--neutral-900);
        margin: 0;
        line-height: 1.25;
    }

    .profile-header-subtitle {
        font-size: 0.875rem;
        color: var(--neutral-600);
        margin: 4px 0 0 0;
    }

    /* 2-Column Responsive Layout */
    .profile-two-columns {
        display: grid;
        grid-template-columns: 340px 1fr;
        gap: 24px;
        align-items: start;
    }

    /* Column Left: Identity Card */
    .identity-card {
        background: #ffffff;
        border: 1px solid var(--neutral-300);
        border-radius: var(--radius-lg, 16px);
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04);
        overflow: hidden;
        position: sticky;
        top: 24px;
    }

    .identity-card-cover {
        height: 84px;
        background: linear-gradient(135deg, var(--blue-700) 0%, var(--blue-600) 60%, #3B82F6 100%);
        position: relative;
    }

    .identity-card-body {
        padding: 0 24px 24px 24px;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .avatar-interactive-wrapper {
        position: relative;
        margin-top: -46px;
        margin-bottom: 14px;
    }

    .avatar-preview-box {
        width: 92px;
        height: 92px;
        border-radius: 50%;
        border: 4px solid #ffffff;
        box-shadow: 0 4px 14px rgba(7, 81, 255, 0.18);
        overflow: hidden;
        background: var(--blue-tint);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .avatar-preview-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .avatar-initial-icon {
        font-size: 2.25rem;
        font-weight: 800;
        color: var(--blue-700);
        user-select: none;
    }

    .avatar-btn-overlay {
        position: absolute;
        bottom: 2px;
        right: 2px;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--blue-600);
        color: #ffffff;
        border: 2px solid #ffffff;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: transform 0.15s ease, background-color 0.15s ease;
    }

    .avatar-btn-overlay:hover {
        background: var(--blue-700);
        transform: scale(1.08);
    }

    .identity-user-name {
        font-size: 1.125rem;
        font-weight: 800;
        color: var(--neutral-900);
        margin: 0;
        line-height: 1.3;
    }

    .identity-role-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        background: #EFF6FF;
        color: var(--blue-700);
        border: 1px solid #BFDBFE;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        margin-top: 8px;
    }

    .identity-user-email {
        font-size: 0.8125rem;
        color: var(--neutral-600);
        margin-top: 6px;
    }

    .photo-actions-row {
        display: flex;
        gap: 8px;
        margin-top: 12px;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn-photo-action {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 6px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        border: none;
        transition: all 0.15s ease;
    }

    .btn-photo-change {
        background: var(--neutral-100);
        color: var(--neutral-700);
        border: 1px solid var(--neutral-300);
    }
    .btn-photo-change:hover {
        background: var(--neutral-200);
    }

    .btn-photo-remove {
        background: #FEE2E2;
        color: #DC2626;
        border: 1px solid #FECACA;
    }
    .btn-photo-remove:hover {
        background: #FCA5A5;
    }

    /* Identity Divider */
    .identity-divider {
        width: 100%;
        height: 1px;
        background: var(--neutral-200);
        margin: 18px 0;
    }

    /* Completeness Mini Card */
    .completeness-widget {
        width: 100%;
        text-align: left;
    }

    .completeness-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .completeness-title {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--neutral-600);
    }

    .completeness-pct {
        font-size: 0.875rem;
        font-weight: 800;
        color: var(--blue-700);
    }

    .completeness-progress-track {
        width: 100%;
        height: 6px;
        background: var(--neutral-200);
        border-radius: 999px;
        overflow: hidden;
        margin-bottom: 12px;
    }

    .completeness-progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #3B82F6, #10B981);
        border-radius: 999px;
        transition: width 0.3s ease;
    }

    .checklist-mini-list {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .checklist-mini-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        font-size: 0.75rem;
        color: var(--neutral-700);
        padding: 2px 0;
    }

    .checklist-mini-item.done {
        color: var(--neutral-900);
        font-weight: 600;
    }

    .checklist-mini-icon {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.625rem;
        flex-shrink: 0;
    }

    .checklist-mini-icon.done {
        background: #DCFCE7;
        color: #166534;
        border: 1px solid #86EFAC;
    }

    .checklist-mini-icon.pending {
        background: var(--neutral-200);
        color: var(--neutral-500);
    }

    /* Privacy Banner in Left Card */
    .privacy-notice-box {
        margin-top: 16px;
        padding: 10px 12px;
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        font-size: 0.72rem;
        color: var(--neutral-600);
        line-height: 1.4;
        display: flex;
        align-items: flex-start;
        gap: 8px;
        text-align: left;
    }

    /* Column Right: Unified Form Card */
    .form-unified-card {
        background: #ffffff;
        border: 1px solid var(--neutral-300);
        border-radius: var(--radius-lg, 16px);
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04);
        padding: 28px 32px;
    }

    .form-section-title-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
    }

    .form-section-icon-badge {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .form-section-icon-badge.blue {
        background: var(--blue-tint);
        color: var(--blue-600);
    }

    .form-section-icon-badge.emerald {
        background: #ECFDF5;
        color: #059669;
    }

    .form-section-heading {
        font-size: 1.0625rem;
        font-weight: 700;
        color: var(--neutral-900);
        margin: 0;
        line-height: 1.25;
    }

    .form-section-desc {
        font-size: 0.8125rem;
        color: var(--neutral-600);
        margin: 2px 0 0 0;
    }

    .form-section-divider {
        height: 1px;
        background: var(--neutral-200);
        margin: 28px 0 24px 0;
    }

    /* Input Field Styling */
    .field-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
        margin-bottom: 18px;
    }

    .field-row.full {
        grid-template-columns: 1fr;
    }

    .form-field-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .field-label {
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--neutral-800);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .field-label .required-dot {
        color: var(--red-600);
        margin-left: 2px;
    }

    .field-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .field-leading-icon {
        position: absolute;
        left: 14px;
        color: var(--neutral-500);
        pointer-events: none;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .field-input {
        width: 100%;
        height: 42px;
        padding: 0 14px 0 42px;
        border: 1.5px solid #CBD5E1;
        border-radius: 8px;
        font-size: 0.875rem;
        color: var(--neutral-900);
        background: #ffffff;
        font-family: inherit;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
        box-sizing: border-box;
    }

    .field-input:focus {
        outline: none;
        border-color: var(--blue-600);
        box-shadow: 0 0 0 3px rgba(7, 81, 255, 0.14);
    }

    .field-input:disabled,
    .field-input[readonly] {
        background: #F8FAFC;
        color: var(--neutral-600);
        border-color: #E2E8F0;
        cursor: not-allowed;
    }

    .field-input.is-invalid {
        border-color: var(--red-600);
        background: #FFF8F8;
    }

    .field-error-text {
        font-size: 0.75rem;
        color: var(--red-600);
        margin-top: 2px;
    }

    .field-helper-text {
        font-size: 0.75rem;
        color: var(--neutral-500);
        margin-top: 2px;
    }

    /* Submit Action Bar */
    .form-action-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        padding-top: 24px;
        border-top: 1px solid var(--neutral-200);
        margin-top: 28px;
    }

    .form-action-meta {
        font-size: 0.8125rem;
        color: var(--neutral-500);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-action-buttons {
        margin-left: auto;
        display: flex;
        align-items: center;
    }

    .btn-submit-profil {
        height: 46px;
        padding: 0 28px;
        background: var(--blue-600);
        color: #ffffff;
        border: none;
        border-radius: 8px;
        font-size: 0.9375rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 14px rgba(7, 81, 255, 0.22);
        transition: background-color 0.15s ease, transform 0.15s ease;
    }

    .btn-submit-profil:hover {
        background: var(--blue-700);
        transform: translateY(-1px);
    }

    .btn-submit-profil:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .profile-two-columns {
            grid-template-columns: 1fr;
        }

        .identity-card {
            position: static;
        }
    }

    @media (max-width: 640px) {
        .form-unified-card {
            padding: 20px 18px;
        }

        .field-row {
            grid-template-columns: 1fr;
            gap: 14px;
        }

        .form-action-bar {
            flex-direction: column;
            align-items: stretch;
            gap: 14px;
        }

        .form-action-buttons {
            margin-left: 0;
            width: 100%;
        }

        .btn-submit-profil {
            width: 100%;
            justify-content: center;
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
<div class="profile-page-wrapper">



    {{-- Clean Page Header --}}
    <div class="profile-header-clean">
        <div>
            <h1 class="profile-header-title">Biodata & Profil Peserta</h1>
            <p class="profile-header-subtitle">
                Kelola pasfoto, data akademik, serta kontak utama dan kontak darurat Anda secara terpadu.
            </p>
        </div>
    </div>

    {{-- Formulir Biodata Utama Peserta (Membungkus Kedua Kolom) --}}
    <form action="{{ route('peserta.profil.simpan') }}" method="POST" id="formBiodataPeserta" enctype="multipart/form-data">
        @csrf

        {{-- Hidden Input untuk Penanganan Foto --}}
        <input type="file" id="inputFotoProfil" name="foto" accept="image/jpeg,image/png,image/jpg,image/webp" style="display: none;">
        <input type="hidden" id="inputHapusFoto" name="hapus_foto" value="0">

        <div class="profile-two-columns">

            {{-- ========================================================
                 KOLOM KIRI: KARTU IDENTITAS PESERTA (PROFILE CARD)
                 ======================================================== --}}
            <div class="identity-card">
                <div class="identity-card-cover"></div>

                <div class="identity-card-body">
                    {{-- Avatar Profil dengan Overlay Tombol Kamera --}}
                    <div class="avatar-interactive-wrapper">
                        <div class="avatar-preview-box">
                            <img id="avatarPreviewImg"
                                 src="{{ $profil?->foto_url ?? '' }}"
                                 alt="Foto Profil"
                                 class="avatar-preview-img"
                                 style="display: {{ $profil?->foto_url ? 'block' : 'none' }};">
                            <div id="avatarFallbackIcon"
                                 class="avatar-initial-icon"
                                 style="display: {{ $profil?->foto_url ? 'none' : 'flex' }};">
                                {{ strtoupper(substr($user->nama ?? $user->name ?? 'P', 0, 1)) }}
                            </div>
                        </div>

                        {{-- Tombol Ikon Kamera untuk Ganti Foto --}}
                        <label for="inputFotoProfil" class="avatar-btn-overlay" title="Klik untuk unggah atau ganti foto profil">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </label>
                    </div>

                    {{-- Nama & Status Penempatan --}}
                    <h2 class="identity-user-name">{{ $user->nama ?? $user->name }}</h2>
                    <span class="identity-role-badge">
                        @if($penempatan)
                            {{ $penempatan->bidang?->nama_bidang ?? 'Peserta Magang Aktif' }}
                        @else
                            Calon Peserta Magang
                        @endif
                    </span>
                    <span class="identity-user-email">{{ $user->email }}</span>

                    {{-- Tombol Aksi Pasfoto (Pilih / Hapus) --}}
                    <div class="photo-actions-row">
                        <button type="button" class="btn-photo-action btn-photo-change" id="btnPilihFoto">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                            <span>Pilih Pasfoto</span>
                        </button>

                        @if($profil?->foto)
                            <button type="button" class="btn-photo-action btn-photo-remove" id="btnHapusFoto">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                <span>Hapus</span>
                            </button>
                        @endif
                    </div>
                    <span id="selectedFileName" style="font-size: 0.75rem; color: var(--blue-700); font-weight: 600; margin-top: 6px;"></span>

                    @error('foto')
                        <span class="field-error-text" style="margin-top: 6px;">{{ $message }}</span>
                    @enderror

                    <div class="identity-divider"></div>

                    {{-- Widget Ringkas Kelengkapan Profil --}}
                    <div class="completeness-widget">
                        <div class="completeness-header">
                            <span class="completeness-title">Kelengkapan Biodata</span>
                            <span class="completeness-pct">{{ $persentaseLengkap }}%</span>
                        </div>
                        <div class="completeness-progress-track">
                            <div class="completeness-progress-bar" style="width: {{ max(6, $persentaseLengkap) }}%;"></div>
                        </div>

                        <div class="checklist-mini-list">
                            <div class="checklist-mini-item {{ $checklist['data_diri'] ? 'done' : '' }}">
                                <span>Akun & Nama Lengkap</span>
                                <span class="checklist-mini-icon {{ $checklist['data_diri'] ? 'done' : 'pending' }}">
                                    {{ $checklist['data_diri'] ? '✓' : '•' }}
                                </span>
                            </div>

                            <div class="checklist-mini-item {{ $checklist['foto'] ? 'done' : '' }}">
                                <span>Pasfoto Resmi</span>
                                <span class="checklist-mini-icon {{ $checklist['foto'] ? 'done' : 'pending' }}">
                                    {{ $checklist['foto'] ? '✓' : '•' }}
                                </span>
                            </div>

                            <div class="checklist-mini-item {{ $checklist['akademik'] ? 'done' : '' }}">
                                <span>Data Akademik & Kampus</span>
                                <span class="checklist-mini-icon {{ $checklist['akademik'] ? 'done' : 'pending' }}">
                                    {{ $checklist['akademik'] ? '✓' : '•' }}
                                </span>
                            </div>

                            <div class="checklist-mini-item {{ $checklist['kontak'] ? 'done' : '' }}">
                                <span>Nomor WhatsApp Aktif</span>
                                <span class="checklist-mini-icon {{ $checklist['kontak'] ? 'done' : 'pending' }}">
                                    {{ $checklist['kontak'] ? '✓' : '•' }}
                                </span>
                            </div>

                            <div class="checklist-mini-item {{ $checklist['darurat'] ? 'done' : '' }}">
                                <span>Kontak Darurat / Wali</span>
                                <span class="checklist-mini-icon {{ $checklist['darurat'] ? 'done' : 'pending' }}">
                                    {{ $checklist['darurat'] ? '✓' : '•' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Catatan Privasi & Keamanan Data --}}
                    <div class="privacy-notice-box">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--blue-600); flex-shrink: 0; margin-top: 1px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <span>Data tersimpan aman dan terenkripsi untuk kebutuhan administrasi magang Dinas Kominfo.</span>
                    </div>

                </div>
            </div>

            {{-- ========================================================
                 KOLOM KANAN: FORMULIR BIODATA BERSATU (UNIFIED FORM CARD)
                 ======================================================== --}}
            <div class="form-unified-card">

                {{-- SEKSI 1: INFORMASI AKADEMIK --}}
                <div class="form-section-title-wrap">
                    <div class="form-section-icon-badge blue">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="form-section-heading">Informasi Akademik & Pendidikan</h3>
                        <p class="form-section-desc">Data institusi pendidikan, nomor identitas, dan program studi Anda</p>
                    </div>
                </div>

                <div class="field-row full">
                    <div class="form-field-group">
                        <label for="institusi" class="field-label">
                            <span>Nama Sekolah / Perguruan Tinggi <span class="required-dot">*</span></span>
                        </label>
                        <div class="field-input-wrapper">
                            <span class="field-leading-icon">
                                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </span>
                            <input type="text" id="institusi" name="institusi"
                                   class="field-input @error('institusi') is-invalid @enderror"
                                   value="{{ old('institusi', $profil?->institusi) }}"
                                   placeholder="Contoh: Universitas Gadjah Mada / SMKN 2 Depok Sleman" required>
                        </div>
                        @error('institusi')
                            <span class="field-error-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="field-row">
                    <div class="form-field-group">
                        <label for="nim_nisn" class="field-label">
                            <span>NIM / NISN <span class="required-dot">*</span></span>
                        </label>
                        <div class="field-input-wrapper">
                            <span class="field-leading-icon">
                                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                                </svg>
                            </span>
                            <input type="text" id="nim_nisn" name="nim_nisn"
                                   class="field-input @error('nim_nisn') is-invalid @enderror"
                                   value="{{ old('nim_nisn', $profil?->nim_nisn) }}"
                                   placeholder="Nomor induk mahasiswa / siswa" required>
                        </div>
                        @error('nim_nisn')
                            <span class="field-error-text">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-field-group">
                        <label for="jenjang" class="field-label">
                            <span>Jenjang Pendidikan <span class="required-dot">*</span></span>
                        </label>
                        <div class="field-input-wrapper">
                            <span class="field-leading-icon">
                                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </span>
                            <select id="jenjang" name="jenjang" class="field-input @error('jenjang') is-invalid @enderror" required>
                                <option value="">-- Pilih Jenjang --</option>
                                <option value="smk" {{ old('jenjang', $profil?->jenjang?->value ?? $profil?->jenjang) === 'smk' ? 'selected' : '' }}>SMK (Siswa)</option>
                                <option value="d3" {{ old('jenjang', $profil?->jenjang?->value ?? $profil?->jenjang) === 'd3' ? 'selected' : '' }}>D3 (Diploma Tiga)</option>
                                <option value="s1" {{ old('jenjang', $profil?->jenjang?->value ?? $profil?->jenjang) === 's1' ? 'selected' : '' }}>S1 / D4 (Sarjana)</option>
                            </select>
                        </div>
                        @error('jenjang')
                            <span class="field-error-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="field-row full">
                    <div class="form-field-group">
                        <label for="jurusan_id" class="field-label">
                            <span>Jurusan / Program Studi <span class="required-dot">*</span></span>
                        </label>
                        <div class="field-input-wrapper">
                            <span class="field-leading-icon">
                                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </span>
                            <select id="jurusan_id" name="jurusan_id" class="field-input @error('jurusan_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Jurusan / Program Studi --</option>
                                @foreach($jurusanList as $jurusan)
                                    <option value="{{ $jurusan->id }}" {{ old('jurusan_id', $profil?->jurusan_id) == $jurusan->id ? 'selected' : '' }}>
                                        {{ $jurusan->nama_jurusan }} ({{ strtoupper($jurusan->kategori) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('jurusan_id')
                            <span class="field-error-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Kepatuhan UU PDP (SEC-41): Persetujuan Orang Tua/Wali Khusus Siswa SMK --}}
                <div id="wrapper-persetujuan-wali" class="field-row full" style="{{ old('jenjang', $profil?->jenjang?->value ?? $profil?->jenjang) === 'smk' || !empty($profil?->persetujuan_wali_at) ? '' : 'display: none;' }}">
                    <div class="form-field-group" style="background: #F8FAFC; border: 1.5px solid #CBD5E1; border-radius: 8px; padding: 14px 16px;">
                        <label class="field-label" style="display: flex; align-items: flex-start; gap: 10px; cursor: pointer; font-weight: normal;">
                            <input type="hidden" name="persetujuan_wali" value="0">
                            <input type="checkbox" id="persetujuan_wali" name="persetujuan_wali" value="1"
                                   {{ old('persetujuan_wali') || !empty($profil?->persetujuan_wali_at) ? 'checked' : '' }}
                                   style="width: 18px; height: 18px; margin-top: 2px; flex-shrink: 0; accent-color: var(--blue-600);">
                            <span style="font-size: 0.8125rem; color: var(--neutral-800); line-height: 1.4;">
                                <strong>Pernyataan Kepatuhan UU PDP No. 27/2022 (Khusus Jenjang SMK):</strong><br>
                                Saya berusia minimal 18 tahun atau telah mendapatkan izin dan persetujuan dari orang tua / wali sah untuk mengikuti kegiatan magang serta pemrosesan data identitas untuk administrasi instansi Dinas Kominfo.
                                @if(!empty($profil?->persetujuan_wali_at))
                                    <span style="display: block; font-size: 0.75rem; color: var(--green-600); margin-top: 4px; font-weight: 600;">
                                        ✓ Persetujuan telah tercatat pada: {{ $profil->persetujuan_wali_at->format('d/m/Y H:i') }} WIB
                                    </span>
                                @endif
                            </span>
                        </label>
                        @error('persetujuan_wali')
                            <span class="field-error-text" style="margin-top: 6px;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-section-divider"></div>

                {{-- SEKSI 2: KONTAK & JALUR DARURAT --}}
                <div class="form-section-title-wrap">
                    <div class="form-section-icon-badge emerald">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="form-section-heading">Kontak Utama & Jalur Darurat</h3>
                        <p class="form-section-desc">Nomor komunikasi aktif dan kontak wali untuk perlindungan selama magang</p>
                    </div>
                </div>

                <div class="field-row full">
                    <div class="form-field-group">
                        <label for="no_hp" class="field-label">
                            <span>Nomor WhatsApp / Telepon Aktif <span class="required-dot">*</span></span>
                        </label>
                        <div class="field-input-wrapper">
                            <span class="field-leading-icon">
                                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                            </span>
                            <input type="tel" id="no_hp" name="no_hp"
                                   class="field-input @error('no_hp') is-invalid @enderror"
                                   value="{{ old('no_hp', $profil?->no_hp ?? $user->no_telp) }}"
                                   placeholder="Contoh: 081234567890" required>
                        </div>
                        @error('no_hp')
                            <span class="field-error-text">{{ $message }}</span>
                        @enderror
                        <span class="field-helper-text">Digunakan oleh pembimbing lapangan untuk koordinasi penugasan harian.</span>
                    </div>
                </div>

                <div class="field-row">
                    <div class="form-field-group">
                        <label for="nama_kontak_darurat" class="field-label">
                            <span>Nama Kontak Darurat / Wali</span>
                        </label>
                        <div class="field-input-wrapper">
                            <span class="field-leading-icon">
                                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </span>
                            <input type="text" id="nama_kontak_darurat" name="nama_kontak_darurat"
                                   class="field-input @error('nama_kontak_darurat') is-invalid @enderror"
                                   value="{{ old('nama_kontak_darurat', $profil?->nama_kontak_darurat) }}"
                                   placeholder="Contoh: Nama Orang Tua / Wali">
                        </div>
                        @error('nama_kontak_darurat')
                            <span class="field-error-text">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-field-group">
                        <label for="no_kontak_darurat" class="field-label">
                            <span>Nomor Telepon Darurat</span>
                        </label>
                        <div class="field-input-wrapper">
                            <span class="field-leading-icon">
                                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </span>
                            <input type="tel" id="no_kontak_darurat" name="no_kontak_darurat"
                                   class="field-input @error('no_kontak_darurat') is-invalid @enderror"
                                   value="{{ old('no_kontak_darurat', $profil?->no_kontak_darurat) }}"
                                   placeholder="Contoh: 089876543210">
                        </div>
                        @error('no_kontak_darurat')
                            <span class="field-error-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Action Bar Submit --}}
                <div class="form-action-bar">
                    <div class="form-action-meta">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--neutral-400); flex-shrink: 0;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                        <span>Periksa kembali data Anda sebelum menekan tombol simpan.</span>
                    </div>

                    <div class="form-action-buttons">
                        <button type="submit" class="btn-submit-profil" id="btnSimpanProfil">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Simpan Biodata Profil</span>
                        </button>
                    </div>
                </div>

            </div>

        </div>

    </form>

</div>

@push('scripts')
<script>
    // Live preview dan handling pasfoto profil
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
        if (confirm('Apakah Anda yakin ingin menghapus foto profil dan kembali menggunakan inisial bawaan?')) {
            if (inputHapusFoto) inputHapusFoto.value = '1';
            if (inputFoto) inputFoto.value = '';
            if (previewImg) {
                previewImg.src = '';
                previewImg.style.display = 'none';
            }
            if (fallbackIcon) {
                fallbackIcon.style.display = 'flex';
            }
            if (selectedFileName) selectedFileName.textContent = '(Foto akan dihapus saat Anda menekan tombol Simpan)';
        }
    });

    // Dynamic toggle persetujuan wali untuk jenjang SMK (SEC-41)
    const jenjangSelect = document.getElementById('jenjang');
    const waliWrapper = document.getElementById('wrapper-persetujuan-wali');
    if (jenjangSelect && waliWrapper) {
        jenjangSelect.addEventListener('change', function() {
            if (this.value === 'smk') {
                waliWrapper.style.display = '';
            } else {
                const checkbox = document.getElementById('persetujuan_wali');
                if (checkbox && !checkbox.checked) {
                    waliWrapper.style.display = 'none';
                }
            }
        });
    }

    // Anti double-submit proteksi
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
