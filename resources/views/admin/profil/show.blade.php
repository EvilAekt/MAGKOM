@extends('layouts.dashboard')

@section('title', 'Profil Saya - Administrasi MagKom')

@section('content')
<div class="staff-profile-wrapper">



    {{-- Header Section --}}
    <x-gentelella.page-title title="Profil & Data Staf Administrasi" description="Kelola informasi identitas, foto profil, dan kontak operasional dinas Anda.">
        <x-slot:actions>
            <a href="{{ route('admin.pengaturan.index') }}" class="btn-secondary-link">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span>Pengaturan Akun & Preferensi</span>
            </a>
        </x-slot:actions>
    </x-gentelella.page-title>

    {{-- Main Form Container --}}
    <form action="{{ route('admin.profil.update') }}" method="POST" enctype="multipart/form-data" id="formProfilStaff">
        @csrf
        @method('PUT')

        {{-- Hidden File Input --}}
        <input type="file" id="inputFotoProfil" name="foto" accept="image/jpeg,image/png,image/webp" style="display: none;">
        <input type="hidden" id="inputHapusFoto" name="hapus_foto" value="0">

        <div class="profile-two-columns">
            
            {{-- KOLOM KIRI: IDENTITY CARD GENTELELLA --}}
            <x-gentelella.card class="identity-card" :hoverable="false">
                <div class="identity-card-cover"></div>
                <div class="identity-card-body">
                    
                    {{-- Avatar & Quick Actions --}}
                    <div class="avatar-interactive-wrapper">
                        <div class="avatar-preview-box">
                            <img id="avatarPreviewImg" 
                                 src="{{ $user->foto_url ?? '' }}" 
                                 alt="{{ $user->nama }}" 
                                 class="avatar-preview-img"
                                 style="{{ $user->foto_url ? 'display: block;' : 'display: none;' }}">
                            <span id="avatarFallbackIcon" 
                                  class="avatar-initial-icon" 
                                  style="{{ $user->foto_url ? 'display: none;' : 'display: flex;' }}">
                                {{ strtoupper(substr($user->nama ?? 'A', 0, 2)) }}
                            </span>
                        </div>
                        <button type="button" class="avatar-btn-overlay" id="btnPilihFotoOverlay" title="Pilih Pasfoto">
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <circle cx="12" cy="13" r="4"></circle>
                            </svg>
                        </button>
                    </div>

                    <h2 class="identity-user-name">{{ $user->nama }}</h2>
                    <span class="identity-role-badge">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        <span>Administrator Dinas</span>
                    </span>

                    <div class="avatar-controls">
                        <button type="button" class="btn-avatar-action" id="btnPilihFoto">
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                            <span>Ganti Pasfoto</span>
                        </button>
                        @if($user->foto)
                            <button type="button" class="btn-avatar-action danger" id="btnHapusFoto">
                                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                <span>Hapus</span>
                            </button>
                        @endif
                    </div>
                    <div id="selectedFileName" class="avatar-hint-text">Format JPG/PNG/WEBP maks 2 MB</div>

                    {{-- Metrics Widget --}}
                    <div class="metrics-summary-card">
                        <div class="metrics-row">
                            <div class="metric-item">
                                <span class="metric-num">{{ number_format($totalPendaftaran) }}</span>
                                <span class="metric-lbl">Pendaftaran Masuk</span>
                            </div>
                            <div class="metric-divider"></div>
                            <div class="metric-item">
                                <span class="metric-num text-success">{{ number_format($totalPenempatan) }}</span>
                                <span class="metric-lbl">Penempatan Disetujui</span>
                            </div>
                        </div>
                    </div>

                    {{-- Identity Details Table --}}
                    <div class="mini-detail-list">
                        <div class="mini-detail-item">
                            <span class="detail-label">Email Kedinasan</span>
                            <span class="detail-value">{{ $user->email }}</span>
                        </div>
                        <div class="mini-detail-item">
                            <span class="detail-label">Instansi / Unit</span>
                            <span class="detail-value">Diskominfo Sleman</span>
                        </div>
                        <div class="mini-detail-item">
                            <span class="detail-label">Hak Akses</span>
                            <span class="detail-value">Verifikasi & Penempatan</span>
                        </div>
                    </div>

                </div>
            </x-gentelella.card>

            {{-- KOLOM KANAN: FORM DETAIL PROFIL GENTELELLA --}}
            <x-gentelella.card class="form-card-container" :hoverable="false">

                {{-- SEKSI 1: IDENTITAS UTAMA --}}
                <div class="form-section-title-wrap">
                    <div class="form-section-icon-badge blue">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="form-section-heading">Informasi Kedinasan & Identitas</h3>
                        <p class="form-section-desc">Identitas resmi yang ditampilkan pada sistem tata kelola magang MagKom.</p>
                    </div>
                </div>

                <div class="field-row">
                    <div class="form-field-group">
                        <label for="nama" class="field-label">
                            <span>Nama Lengkap Staf <span class="required-dot">*</span></span>
                        </label>
                        <div class="field-input-wrapper">
                            <span class="field-leading-icon">
                                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </span>
                            <input type="text" id="nama" name="nama"
                                   class="field-input @error('nama') is-invalid @enderror"
                                   value="{{ old('nama', $user->nama) }}"
                                   placeholder="Contoh: Nama Lengkap beserta Gelar" required>
                        </div>
                        @error('nama')
                            <span class="field-error-text">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-field-group">
                        <label for="email" class="field-label">
                            <span>Email Resmi (Akun)</span>
                            <span class="readonly-badge">Terkunci</span>
                        </label>
                        <div class="field-input-wrapper readonly">
                            <span class="field-leading-icon">
                                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                </svg>
                            </span>
                            <input type="email" id="email" class="field-input readonly" value="{{ $user->email }}" readonly disabled>
                        </div>
                        <span class="field-helper-text">Perubahan email staf dikelola oleh Super Administrator.</span>
                    </div>
                </div>

                <div class="field-row">
                    <div class="form-field-group">
                        <label class="field-label">
                            <span>Instansi / Unit Kerja</span>
                            <span class="readonly-badge">Sistem</span>
                        </label>
                        <div class="field-input-wrapper readonly">
                            <span class="field-leading-icon">
                                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </span>
                            <input type="text" class="field-input readonly" value="Dinas Komunikasi dan Informatika Kabupaten Sleman" readonly disabled>
                        </div>
                    </div>

                    <div class="form-field-group">
                        <label class="field-label">
                            <span>Peran Akses</span>
                            <span class="readonly-badge">Sistem</span>
                        </label>
                        <div class="field-input-wrapper readonly">
                            <span class="field-leading-icon">
                                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </span>
                            <input type="text" class="field-input readonly" value="Administrasi Operasional" readonly disabled>
                        </div>
                    </div>
                </div>

                {{-- SEKSI 2: KONTAK OPERASIONAL --}}
                <div class="form-section-title-wrap" style="margin-top: 14px;">
                    <div class="form-section-icon-badge emerald">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="form-section-heading">Kontak Operasional Staf</h3>
                        <p class="form-section-desc">Nomor komunikasi dinas aktif untuk koordinasi dengan tim dan pembimbing lapangan.</p>
                    </div>
                </div>

                <div class="field-row full">
                    <div class="form-field-group">
                        <label for="no_telp" class="field-label">
                            <span>Nomor WhatsApp / Telepon Aktif</span>
                        </label>
                        <div class="field-input-wrapper">
                            <span class="field-leading-icon">
                                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                            </span>
                            <input type="tel" id="no_telp" name="no_telp"
                                   class="field-input @error('no_telp') is-invalid @enderror"
                                   value="{{ old('no_telp', $user->no_telp) }}"
                                   placeholder="Contoh: 081234567890">
                        </div>
                        @error('no_telp')
                            <span class="field-error-text">{{ $message }}</span>
                        @enderror
                        <span class="field-helper-text">Digunakan untuk pemberitahuan internal dan koordinasi administrasi penempatan magang.</span>
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
                            <span>Simpan Perubahan Profil</span>
                        </button>
                    </div>
                </div>

            </x-gentelella.card>

        </div>

    </form>

</div>

@push('styles')
<style>
    .staff-profile-wrapper {
        max-width: 1240px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 24px;
        padding-bottom: 40px;
    }

    /* Alerts */
    .alert-banner {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 16px 20px;
        border-radius: 12px;
        font-size: 0.875rem;
    }
    .alert-success {
        background: #ECFDF5;
        border: 1px solid #A7F3D0;
        color: #065F46;
    }
    .alert-danger {
        background: #FEF2F2;
        border: 1px solid #FECACA;
        color: #991B1B;
    }
    .alert-title {
        font-weight: 700;
        margin-bottom: 2px;
    }
    .alert-desc {
        color: inherit;
    }
    .alert-list {
        margin: 4px 0 0 16px;
        padding: 0;
    }

    /* Header */
    .profile-header-clean {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }
    .profile-header-title {
        font-size: 1.625rem;
        font-weight: 800;
        color: var(--neutral-900, #0F172A);
        margin: 0;
        line-height: 1.25;
    }
    .profile-header-subtitle {
        font-size: 0.9rem;
        color: var(--neutral-600, #475569);
        margin: 4px 0 0 0;
    }
    .btn-secondary-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 10px;
        background: #ffffff;
        border: 1px solid var(--neutral-300, #CBD5E1);
        color: var(--neutral-700, #334155);
        font-size: 0.875rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .btn-secondary-link:hover {
        background: var(--neutral-50, #F8FAFC);
        border-color: var(--blue-600, #0641CC);
        color: var(--blue-700, #0641CC);
    }

    /* 2 Columns Layout */
    .profile-two-columns {
        display: grid;
        grid-template-columns: 360px 1fr;
        gap: 28px;
        align-items: start;
    }
    @media (max-width: 980px) {
        .profile-two-columns {
            grid-template-columns: 1fr;
        }
    }

    /* Left Card: Identity */
    .identity-card {
        background: #ffffff;
        border: 1px solid var(--neutral-200, #E2E8F0);
        border-radius: 16px;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04);
        overflow: hidden;
        position: sticky;
        top: 24px;
    }
    .identity-card-cover {
        height: 90px;
        background: linear-gradient(135deg, var(--blue-700, #0641CC) 0%, #1E40AF 60%, #3B82F6 100%);
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
        margin-top: -50px;
        margin-bottom: 12px;
    }
    .avatar-preview-box {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        border: 4px solid #ffffff;
        box-shadow: 0 4px 14px rgba(6, 65, 204, 0.2);
        overflow: hidden;
        background: #EFF6FF;
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
        color: var(--blue-700, #0641CC);
        user-select: none;
    }
    .avatar-btn-overlay {
        position: absolute;
        bottom: 2px;
        right: 2px;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: var(--blue-600, #0641CC);
        color: #ffffff;
        border: 2px solid #ffffff;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: transform 0.15s ease, background-color 0.15s ease;
    }
    .avatar-btn-overlay:hover {
        background: var(--blue-700, #0533A3);
        transform: scale(1.08);
    }
    .identity-user-name {
        font-size: 1.2rem;
        font-weight: 800;
        color: var(--neutral-900, #0F172A);
        margin: 0;
        line-height: 1.3;
    }
    .identity-role-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 14px;
        background: #EFF6FF;
        color: var(--blue-700, #0641CC);
        border: 1px solid #BFDBFE;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        margin-top: 8px;
    }
    .avatar-controls {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 14px;
    }
    .btn-avatar-action {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 8px;
        background: #F1F5F9;
        border: 1px solid #CBD5E1;
        color: #334155;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .btn-avatar-action:hover {
        background: #E2E8F0;
        color: #0F172A;
    }
    .btn-avatar-action.danger {
        background: #FEF2F2;
        border-color: #FECACA;
        color: #DC2626;
    }
    .btn-avatar-action.danger:hover {
        background: #FEE2E2;
        color: #B91C1C;
    }
    .avatar-hint-text {
        font-size: 0.75rem;
        color: var(--neutral-500, #64748B);
        margin-top: 6px;
    }

    /* Metrics Card */
    .metrics-summary-card {
        width: 100%;
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        padding: 14px 16px;
        margin-top: 18px;
    }
    .metrics-row {
        display: flex;
        align-items: center;
        justify-content: space-around;
    }
    .metric-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 2px;
    }
    .metric-num {
        font-size: 1.35rem;
        font-weight: 800;
        color: #0F172A;
    }
    .metric-num.text-success {
        color: #059669;
    }
    .metric-lbl {
        font-size: 0.7rem;
        font-weight: 600;
        color: #64748B;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }
    .metric-divider {
        width: 1px;
        height: 34px;
        background: #CBD5E1;
    }

    /* Mini Detail Table */
    .mini-detail-list {
        width: 100%;
        margin-top: 18px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        border-top: 1px solid #E2E8F0;
        padding-top: 14px;
        text-align: left;
    }
    .mini-detail-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.8rem;
    }
    .detail-label {
        color: #64748B;
        font-weight: 500;
    }
    .detail-value {
        color: #0F172A;
        font-weight: 600;
        text-align: right;
    }

    /* Right Card: Form */
    .form-card-container {
        background: #ffffff;
        border: 1px solid var(--neutral-200, #E2E8F0);
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04);
        display: flex;
        flex-direction: column;
        gap: 22px;
    }
    .form-section-title-wrap {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding-bottom: 14px;
        border-bottom: 1px solid #E2E8F0;
    }
    .form-section-icon-badge {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .form-section-icon-badge.blue {
        background: #EFF6FF;
        color: var(--blue-700, #0641CC);
    }
    .form-section-icon-badge.emerald {
        background: #ECFDF5;
        color: #059669;
    }
    .form-section-heading {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--neutral-900, #0F172A);
        margin: 0;
    }
    .form-section-desc {
        font-size: 0.825rem;
        color: var(--neutral-500, #64748B);
        margin: 2px 0 0 0;
    }
    .field-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    .field-row.full {
        grid-template-columns: 1fr;
    }
    @media (max-width: 680px) {
        .field-row {
            grid-template-columns: 1fr;
        }
    }
    .form-field-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .field-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--neutral-800, #1E293B);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .required-dot {
        color: #DC2626;
    }
    .readonly-badge {
        font-size: 0.7rem;
        padding: 2px 8px;
        background: #F1F5F9;
        color: #64748B;
        border-radius: 999px;
        font-weight: 600;
    }
    .field-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }
    .field-leading-icon {
        position: absolute;
        left: 12px;
        color: #94A3B8;
        pointer-events: none;
        display: flex;
        align-items: center;
    }
    .field-input {
        width: 100%;
        padding: 10px 14px 10px 38px;
        border: 1px solid var(--neutral-300, #CBD5E1);
        border-radius: 10px;
        font-size: 0.875rem;
        color: var(--neutral-900, #0F172A);
        background: #ffffff;
        transition: all 0.2s ease;
    }
    .field-input:focus {
        outline: none;
        border-color: var(--blue-600, #0641CC);
        box-shadow: 0 0 0 3px rgba(6, 65, 204, 0.12);
    }
    .field-input.readonly {
        background: #F8FAFC;
        border-color: #E2E8F0;
        color: #64748B;
        cursor: not-allowed;
    }
    .field-input.is-invalid {
        border-color: #DC2626;
        background: #FEF2F2;
    }
    .field-error-text {
        font-size: 0.775rem;
        color: #DC2626;
        font-weight: 500;
    }
    .field-helper-text {
        font-size: 0.775rem;
        color: #64748B;
    }

    /* Action Bar */
    .form-action-bar {
        margin-top: 12px;
        padding-top: 20px;
        border-top: 1px solid #E2E8F0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }
    .form-action-meta {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.825rem;
        color: #64748B;
    }
    .btn-submit-profil {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        border-radius: 10px;
        background: var(--blue-700, #0641CC);
        color: #ffffff;
        border: none;
        font-size: 0.9rem;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(6, 65, 204, 0.25);
        transition: all 0.2s ease;
    }
    .btn-submit-profil:hover {
        background: #0533A3;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(6, 65, 204, 0.35);
    }

    /* Dark Mode Harmonization */
    [data-theme="dark"] .staff-profile-wrapper .profile-header-title {
        color: #F8FAFC !important;
    }
    [data-theme="dark"] .staff-profile-wrapper .profile-header-subtitle {
        color: #94A3B8 !important;
    }
    [data-theme="dark"] .staff-profile-wrapper .btn-secondary-link {
        background: #182234 !important;
        border-color: #3E516E !important;
        color: #E2E8F0 !important;
    }
    [data-theme="dark"] .staff-profile-wrapper .btn-secondary-link:hover {
        background: #1E293B !important;
        color: #60A5FA !important;
        border-color: #60A5FA !important;
    }
    [data-theme="dark"] .staff-profile-wrapper .identity-card,
    [data-theme="dark"] .staff-profile-wrapper .form-card-container {
        background: #182234 !important;
        border-color: #26354D !important;
    }
    [data-theme="dark"] .staff-profile-wrapper .identity-user-name,
    [data-theme="dark"] .staff-profile-wrapper .form-section-heading {
        color: #F8FAFC !important;
    }
    [data-theme="dark"] .staff-profile-wrapper .field-label {
        color: #E2E8F0 !important;
    }
    [data-theme="dark"] .staff-profile-wrapper .field-input {
        background: #0F172A !important;
        border-color: #334155 !important;
        color: #F8FAFC !important;
    }
    [data-theme="dark"] .staff-profile-wrapper .field-input.readonly {
        background: #1E293B !important;
        border-color: #334155 !important;
        color: #94A3B8 !important;
    }
    [data-theme="dark"] .staff-profile-wrapper .metrics-summary-card {
        background: #0F172A !important;
        border-color: #26354D !important;
    }
    [data-theme="dark"] .staff-profile-wrapper .metric-num {
        color: #F8FAFC !important;
    }
    [data-theme="dark"] .staff-profile-wrapper .mini-detail-list {
        border-top-color: #26354D !important;
    }
    [data-theme="dark"] .staff-profile-wrapper .detail-value {
        color: #F8FAFC !important;
    }
    [data-theme="dark"] .staff-profile-wrapper .form-section-title-wrap,
    [data-theme="dark"] .staff-profile-wrapper .form-action-bar {
        border-color: #26354D !important;
    }
    [data-theme="dark"] .staff-profile-wrapper .btn-avatar-action {
        background: #1E293B !important;
        border-color: #3E516E !important;
        color: #E2E8F0 !important;
    }
</style>
@endpush

@push('scripts')
<script>
    const inputFoto = document.getElementById('inputFotoProfil');
    const btnPilihFoto = document.getElementById('btnPilihFoto');
    const btnPilihFotoOverlay = document.getElementById('btnPilihFotoOverlay');
    const btnHapusFoto = document.getElementById('btnHapusFoto');
    const inputHapusFoto = document.getElementById('inputHapusFoto');
    const previewImg = document.getElementById('avatarPreviewImg');
    const fallbackIcon = document.getElementById('avatarFallbackIcon');
    const selectedFileName = document.getElementById('selectedFileName');

    const triggerUpload = () => inputFoto?.click();
    btnPilihFoto?.addEventListener('click', triggerUpload);
    btnPilihFotoOverlay?.addEventListener('click', triggerUpload);

    inputFoto?.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            if (file.size > 2 * 1024 * 1024) {
                if (window.MagKomToast) {
                    window.MagKomToast.warning('Ukuran berkas pasfoto melebihi batas maksimal 2 MB.');
                } else {
                    alert('Ukuran berkas pasfoto melebihi batas maksimal 2 MB.');
                }
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

    document.getElementById('formProfilStaff')?.addEventListener('submit', function() {
        const btn = document.getElementById('btnSimpanProfil');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="animation: spin 1s linear infinite;">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" style="opacity: 0.25;"></circle>
                    <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" style="opacity: 0.75;"></path>
                </svg>
                <span>Menyimpan Data...</span>
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
