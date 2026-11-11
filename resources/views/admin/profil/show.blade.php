@extends('layouts.dashboard')

@section('title', 'Profil Saya - Administrasi MagKom')
@section('topbar_title', 'Profil Saya')

@push('styles')
    @vite(['resources/css/pages/admin/profil-show.css'])
@endpush

@push('scripts')
    @vite(['resources/js/pages/admin/profil-show.js'])
@endpush

@section('content')
<div class="admin-profil-wrapper">

    {{-- Header Section dengan Title Assertion Parity --}}
    <x-gentelella.page-title title="Profil & Data Staf Administrasi" description="Kelola informasi identitas, foto profil, dan kontak operasional dinas Anda.">
        <x-slot:actions>
            <a href="{{ route('admin.pengaturan.index') }}" class="btn-banner-action" style="background: #ffffff; color: #02275D; border: 1px solid #CBD5E1; padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 0.8125rem; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span>Pengaturan Akun</span>
            </a>
        </x-slot:actions>
    </x-gentelella.page-title>

    {{-- Container Kartu Utama Fluid Elevated --}}
    <div class="admin-profil-main-card">

        {{-- Flash Alerts --}}
        @if(session('success'))
            <div class="profil-alert profil-alert-success" role="alert">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink: 0;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="profil-alert profil-alert-danger" role="alert">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink: 0;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Terdapat kesalahan pada data yang Anda masukkan. Silakan periksa kolom yang ditandai merah.</span>
            </div>
        @endif

        {{-- Form Profil Staf Administrasi --}}
        <form action="{{ route('admin.profil.update') }}" method="POST" enctype="multipart/form-data" id="formProfilStaff">
            @csrf
            @method('PUT')

            {{-- Hidden File Input & Status Hapus --}}
            <input type="file" id="inputFotoProfil" name="foto" accept="image/jpeg,image/png,image/jpg,image/webp" style="display: none;">
            <input type="hidden" id="inputHapusFoto" name="hapus_foto" value="0">

            {{-- ========================================================
                 1. BANNER IDENTITAS STAF (NAVY SLEMAN GRADIENT)
                 ======================================================== --}}
            <div class="profil-identity-banner">
                <div class="profil-identity-left">
                    {{-- Avatar Box Lingkaran dengan Badge Kamera Kuning --}}
                    <div class="profil-avatar-box">
                        <div class="profil-avatar-img-wrap">
                            <img id="avatarImg"
                                 src="{{ $user->foto_url ?? '' }}"
                                 data-original-url="{{ $user->foto_url ?? '' }}"
                                 alt="{{ $user->nama }}"
                                 class="profil-avatar-img"
                                 style="display: {{ $user->foto_url ? 'block' : 'none' }};">
                            <span id="avatarInitial"
                                  class="profil-avatar-initial"
                                  style="display: {{ $user->foto_url ? 'none' : 'block' }};">
                                {{ strtoupper(substr($user->nama ?? 'A', 0, 1)) }}
                            </span>
                        </div>

                        {{-- Tombol Overlay Kamera Kuning Sleman --}}
                        <button type="button" class="profil-camera-badge" id="cameraOverlayBtn" title="Ganti pasfoto profil" aria-label="Ganti pasfoto profil">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <circle cx="12" cy="13" r="4"></circle>
                            </svg>
                        </button>
                    </div>

                    {{-- Informasi Nama, Role Badge, dan Email Staf --}}
                    <div class="profil-user-info">
                        <div class="profil-name-row">
                            <h2 class="profil-user-name">{{ $user->nama }}</h2>
                            <span class="profil-badge-role">
                                ADMINISTRATOR DINAS
                            </span>
                        </div>
                        <p class="profil-user-email">{{ $user->email }}</p>
                    </div>
                </div>

                {{-- Kontrol Unggah Pasfoto di Sisi Kanan --}}
                <div class="profil-identity-right">
                    <button type="button" class="profil-btn-upload-photo" id="btnUploadPhoto">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        <span>Pilih Pasfoto</span>
                    </button>

                    @if($user->foto)
                        <button type="button" class="profil-btn-remove-photo" id="btnRemovePhoto">
                            Hapus Pasfoto
                        </button>
                    @endif
                    <span id="photoInfoText" class="profil-photo-subtext">Format JPG/PNG/WEBP maks 2 MB</span>
                </div>
            </div>

            @error('foto')
                <span class="admin-field-error" style="margin-top: 4px; display: block;">{{ $message }}</span>
            @enderror

            {{-- ========================================================
                 2. STATUS OPERASIONAL & KINERJA ADMINISTRASI (METRIC CARD)
                 ======================================================== --}}
            <div class="admin-kpi-card">
                <div class="admin-kpi-header">
                    <span class="admin-kpi-title">STATUS OPERASIONAL & KINERJA ADMINISTRASI</span>
                    <span class="admin-kpi-badge">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>100% Akun Terverifikasi ASN Dinas</span>
                    </span>
                </div>

                {{-- Progress Bar Solid --}}
                <div class="admin-kpi-bar-track">
                    <div class="admin-kpi-bar-fill" style="width: 100%;"></div>
                </div>

                {{-- 4 Checklist Kesiapan Operasional --}}
                <div class="admin-kpi-checklist-grid">
                    <div class="admin-kpi-checklist-item">
                        <span class="admin-kpi-checklist-icon">
                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </span>
                        <span>Akun Resmi Sleman</span>
                    </div>

                    <div class="admin-kpi-checklist-item">
                        <span class="admin-kpi-checklist-icon">
                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </span>
                        <span>Pasfoto Kedinasan</span>
                    </div>

                    <div class="admin-kpi-checklist-item">
                        <span class="admin-kpi-checklist-icon">
                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </span>
                        <span>Nomor WhatsApp Aktif</span>
                    </div>

                    <div class="admin-kpi-checklist-item">
                        <span class="admin-kpi-checklist-icon">
                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </span>
                        <span>Hak Akses Terotorisasi</span>
                    </div>
                </div>

                {{-- Ringkasan Bento Metrik Kinerja --}}
                <div class="admin-kpi-metrics-row">
                    <div class="admin-metric-box">
                        <span class="admin-metric-num">{{ number_format($totalPendaftaran) }}</span>
                        <span class="admin-metric-lbl">Total Pendaftaran Masuk</span>
                    </div>

                    <div class="admin-metric-box">
                        <span class="admin-metric-num" style="color: #059669;">{{ number_format($totalPenempatan) }}</span>
                        <span class="admin-metric-lbl">Penempatan Disetujui</span>
                    </div>

                    <div class="admin-metric-box">
                        <span class="admin-metric-num" style="font-size: 1.05rem; color: #1E293B;">Diskominfo</span>
                        <span class="admin-metric-lbl">Unit Kerja Pemerintah Kab. Sleman</span>
                    </div>
                </div>
            </div>

            {{-- ========================================================
                 3. SEKSI 1: INFORMASI KEDINASAN & IDENTITAS RESMI
                 ======================================================== --}}
            <div class="admin-section-card">
                <div class="admin-section-header">
                    <div class="admin-section-header-left">
                        <div class="admin-section-icon-wrap">
                            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="admin-section-heading">Informasi Kedinasan & Identitas Resmi</h3>
                            <p class="admin-section-desc">Identitas resmi yang ditampilkan pada sistem tata kelola magang MagKom.</p>
                        </div>
                    </div>
                    <span class="admin-section-btn-edit">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                        <span>Edit</span>
                    </span>
                </div>

                <div class="admin-fields-grid">
                    {{-- Nama Lengkap Staf (Editable) --}}
                    <div class="admin-field-group">
                        <label for="nama" class="admin-field-label">
                            <span>Nama Lengkap Staf</span>
                            <span class="admin-required-mark">*</span>
                        </label>
                        <div class="admin-input-wrapper">
                            <span class="admin-input-leading-icon">
                                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </span>
                            <input type="text"
                                   id="nama"
                                   name="nama"
                                   class="admin-input-control @error('nama') is-invalid @enderror"
                                   value="{{ old('nama', $user->nama) }}"
                                   placeholder="Contoh: Nama Lengkap beserta Gelar"
                                   required>
                        </div>
                        @error('nama')
                            <span class="admin-field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Email Resmi Akun (Readonly Terkunci) --}}
                    <div class="admin-field-group">
                        <label for="email" class="admin-field-label">
                            <span>Email Resmi (Akun)</span>
                            <span class="admin-lock-badge">
                                <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                    <path d="M7 11V7a5 5 0 0110 0v4"></path>
                                </svg>
                                <span>Terkunci</span>
                            </span>
                        </label>
                        <div class="admin-input-wrapper">
                            <span class="admin-input-leading-icon">
                                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                </svg>
                            </span>
                            <input type="email"
                                   id="email"
                                   class="admin-input-control readonly"
                                   value="{{ $user->email }}"
                                   readonly
                                   disabled>
                        </div>
                        <span class="admin-field-helper">Perubahan email staf dikelola oleh Super Administrator.</span>
                    </div>

                    {{-- Instansi / Unit Kerja (Readonly Terkunci) --}}
                    <div class="admin-field-group">
                        <label class="admin-field-label">
                            <span>Instansi / Unit Kerja</span>
                            <span class="admin-lock-badge">Sistem</span>
                        </label>
                        <div class="admin-input-wrapper">
                            <span class="admin-input-leading-icon">
                                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </span>
                            <input type="text"
                                   class="admin-input-control readonly"
                                   value="Dinas Komunikasi dan Informatika Kabupaten Sleman"
                                   readonly
                                   disabled>
                        </div>
                    </div>

                    {{-- Peran Akses Sistem (Readonly Terkunci) --}}
                    <div class="admin-field-group">
                        <label class="admin-field-label">
                            <span>Peran Akses Sistem</span>
                            <span class="admin-lock-badge">Sistem</span>
                        </label>
                        <div class="admin-input-wrapper">
                            <span class="admin-input-leading-icon">
                                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </span>
                            <input type="text"
                                   class="admin-input-control readonly"
                                   value="Administrasi Operasional (Verifikasi & Penempatan)"
                                   readonly
                                   disabled>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ========================================================
                 4. SEKSI 2: KONTAK OPERASIONAL & JALUR KOORDINASI
                 ======================================================== --}}
            <div class="admin-section-card">
                <div class="admin-section-header">
                    <div class="admin-section-header-left">
                        <div class="admin-section-icon-wrap">
                            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="admin-section-heading">Kontak Operasional & Jalur Koordinasi</h3>
                            <p class="admin-section-desc">Nomor komunikasi dinas aktif untuk koordinasi dengan tim dan pembimbing lapangan.</p>
                        </div>
                    </div>
                    <span class="admin-section-btn-edit">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                        <span>Edit</span>
                    </span>
                </div>

                <div class="admin-fields-grid">
                    {{-- Nomor WhatsApp / Telepon Aktif (Editable) --}}
                    <div class="admin-field-group">
                        <label for="no_telp" class="admin-field-label">
                            <span>Nomor WhatsApp / Telepon Aktif</span>
                            <span class="admin-required-mark">*</span>
                        </label>
                        <div class="admin-input-wrapper">
                            <span class="admin-input-leading-icon">
                                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                            </span>
                            <input type="tel"
                                   id="no_telp"
                                   name="no_telp"
                                   class="admin-input-control @error('no_telp') is-invalid @enderror"
                                   value="{{ old('no_telp', $user->no_telp) }}"
                                   placeholder="Contoh: 081234567890"
                                   required>
                        </div>
                        @error('no_telp')
                            <span class="admin-field-error">{{ $message }}</span>
                        @enderror
                        <span class="admin-field-helper">Digunakan untuk pemberitahuan internal dan koordinasi administrasi penempatan magang.</span>
                    </div>

                    {{-- Alamat Kantor Kedinasan (Readonly Terkunci) --}}
                    <div class="admin-field-group">
                        <label class="admin-field-label">
                            <span>Alamat Kantor Kedinasan</span>
                            <span class="admin-lock-badge">Sistem</span>
                        </label>
                        <div class="admin-input-wrapper">
                            <span class="admin-input-leading-icon">
                                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </span>
                            <input type="text"
                                   class="admin-input-control readonly"
                                   value="Jl. Parasamya No. 1, Beran, Tridadi, Sleman, D.I. Yogyakarta 55511"
                                   readonly
                                   disabled>
                        </div>
                        <span class="admin-field-helper">Lokasi pusat kantor dinas komunikasi dan informatika pemda sleman.</span>
                    </div>
                </div>
            </div>

            {{-- ========================================================
                 5. BOTTOM ACTIONS: CHECKBOX PERSETUJUAN & TOMBOL SIMPAN
                 ======================================================== --}}
            <div class="profil-bottom-actions">
                <label class="profil-agreement-label">
                    <input type="checkbox" id="checkKonfirmasiProfil" class="profil-checkbox">
                    <span>Data profil kedinasan yang saya isikan sudah benar dan siap digunakan untuk administrasi operasional dinas.</span>
                </label>

                <button type="submit" class="profil-btn-submit" id="btnSimpanProfil" disabled>
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Simpan Perubahan Profil</span>
                </button>
            </div>

        </form>

    </div>

</div>
@endsection
