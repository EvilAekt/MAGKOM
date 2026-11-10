@extends('layouts.dashboard')

@section('title', 'Profil & Data Pembimbing Lapangan - MagKom')
@section('page_title', 'Profil & Data Pembimbing')

@push('styles')
    @vite(['resources/css/pages/pembimbing/profil-show.css'])
@endpush

@push('scripts')
    @vite(['resources/js/pages/pembimbing/profil-show.js'])
@endpush

@section('content')
<div class="profil-page-wrapper">
    <div class="profil-main-card">

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="profil-alert profil-alert-success" role="alert">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="profil-alert profil-alert-danger" role="alert">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Terdapat kesalahan pada input Anda. Silakan periksa kolom yang ditandai merah.</span>
            </div>
        @endif

        {{-- Form Profil Pembimbing --}}
        <form action="{{ route('pembimbing.profil.update') }}" method="POST" id="formProfilStaff" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <input type="file" id="inputFotoProfil" name="foto" accept="image/jpeg,image/png,image/webp" style="display: none;">
            <input type="hidden" id="inputHapusFoto" name="hapus_foto" value="0">

            {{-- ========================================================
                 1. HERO IDENTITY BANNER (NAVY BANNER)
                 ======================================================== --}}
            <div class="profil-identity-banner">
                <div class="profil-identity-left">
                    <div class="profil-avatar-box">
                        <div class="profil-avatar-img-wrap">
                            <img id="avatarPreviewImg"
                                 src="{{ $user->foto_url ?? '' }}"
                                 alt="Pasfoto {{ $user->nama }}"
                                 class="profil-avatar-img"
                                 style="{{ $user->foto_url ? 'display: block;' : 'display: none;' }}">
                            <span id="avatarFallbackIcon"
                                  class="profil-avatar-initial"
                                  style="{{ $user->foto_url ? 'display: none;' : 'display: block;' }}">
                                {{ strtoupper(substr($user->nama ?? 'P', 0, 1)) }}
                            </span>
                        </div>
                        <button type="button" class="profil-camera-badge" id="btnPilihFotoOverlay" title="Ganti pasfoto" aria-label="Ganti pasfoto">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="profil-user-info">
                        <div class="profil-name-row">
                            <h2 class="profil-user-name">{{ $user->nama }}</h2>
                            <span class="profil-badge-role">
                                {{ is_object($bidang ?? null) ? ($bidang->nama ?? 'BIDANG DINAS') : ($bidang ?? $user->bidang?->nama ?? 'PEMBIMBING LAPANGAN') }}
                            </span>
                        </div>
                        <p class="profil-user-email">{{ $user->email }}</p>
                    </div>
                </div>

                <div class="profil-identity-right">
                    <button type="button" class="profil-btn-upload-photo" id="btnPilihFoto">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        <span>Pilih Pasfoto</span>
                    </button>
                    @if($user->foto)
                        <button type="button" id="btnHapusFoto" class="profil-btn-remove-photo">Hapus Pasfoto</button>
                    @endif
                    <span id="selectedFileName" class="profil-photo-subtext">Maksimal 2 MB (JPG, PNG, WEBP)</span>
                </div>
            </div>

            {{-- ========================================================
                 2. STATUS & KAPASITAS BIMBINGAN
                 ======================================================== --}}
            <div class="profil-completeness-card">
                <div class="profil-completeness-header">
                    <span class="profil-completeness-title">KAPASITAS & STATISTIK BIMBINGAN</span>
                    <span class="profil-completeness-percent">{{ $menteeAktifCount }} / {{ $maksPeserta }} Mentee Aktif</span>
                </div>
                <div class="profil-progress-track">
                    <div class="profil-progress-fill" style="width: {{ min(100, round(($menteeAktifCount / max(1, $maksPeserta)) * 100)) }}%;"></div>
                </div>
                <div class="profil-checklist-grid">
                    <div class="profil-checklist-item">
                        <span class="profil-checklist-icon done">✓</span>
                        <span>Akun Terverifikasi</span>
                    </div>
                    <div class="profil-checklist-item">
                        <span class="profil-checklist-icon {{ $user->foto ? 'done' : 'pending' }}">{{ $user->foto ? '✓' : '○' }}</span>
                        <span>Pasfoto Resmi</span>
                    </div>
                    <div class="profil-checklist-item">
                        <span class="profil-checklist-icon done">✓</span>
                        <span>Bidang Penugasan</span>
                    </div>
                    <div class="profil-checklist-item">
                        <span class="profil-checklist-icon {{ $user->no_telp ? 'done' : 'pending' }}">{{ $user->no_telp ? '✓' : '○' }}</span>
                        <span>WhatsApp Aktif</span>
                    </div>
                    <div class="profil-checklist-item">
                        <span class="profil-checklist-icon done">✓</span>
                        <span>Sisa Kuota: {{ max(0, $maksPeserta - $menteeAktifCount) }} Slot</span>
                    </div>
                </div>
            </div>

            {{-- ========================================================
                 3. SEKSI 1: IDENTITAS & PENUGASAN DINAS
                 ======================================================== --}}
            <div class="profil-section-card" id="sectionIdentitas">
                <div class="profil-section-header">
                    <div class="profil-section-title-wrap">
                        <div class="profil-section-icon-badge">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="profil-section-heading">Informasi Identitas & Penugasan</h3>
                            <p class="profil-section-subheading">Identitas resmi pembimbing lapangan bagi mahasiswa/siswa magang.</p>
                        </div>
                    </div>
                    <button type="button" class="profil-btn-edit" id="btnEditIdentitas" data-focus-target="nama">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                        <span>Edit</span>
                    </button>
                </div>

                <div class="profil-fields-grid">
                    <div class="profil-form-group profil-field-col-full">
                        <label for="nama" class="profil-label">
                            <span>Nama Lengkap Pembimbing <span class="profil-required">*</span></span>
                        </label>
                        <input type="text" id="nama" name="nama" class="profil-input @error('nama') is-invalid @enderror" value="{{ old('nama', $user->nama) }}" placeholder="Contoh: Ir. Nama Lengkap, M.Eng" required>
                        @error('nama') <span class="profil-field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="profil-form-group">
                        <label for="email" class="profil-label">
                            <span>Email Resmi (Akun)</span>
                            <svg class="profil-lock-icon" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        </label>
                        <input type="email" id="email" class="profil-input" value="{{ $user->email }}" readonly disabled>
                        <span class="profil-helper-text">Perubahan email dikelola oleh Super Administrator.</span>
                    </div>

                    <div class="profil-form-group">
                        <label class="profil-label">
                            <span>Bidang Penugasan Dinas</span>
                            <svg class="profil-lock-icon" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        </label>
                        <input type="text" class="profil-input" value="{{ is_object($bidang ?? null) ? ($bidang->nama ?? 'Aptika/IKP') : ($bidang ?? $user->bidang?->nama ?? 'Bidang Penugasan MagKom') }}" readonly disabled>
                    </div>

                    <div class="profil-form-group profil-field-col-full">
                        <label class="profil-label">
                            <span>Peran Penugasan</span>
                            <svg class="profil-lock-icon" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        </label>
                        <input type="text" class="profil-input" value="Pembimbing Lapangan Magang" readonly disabled>
                    </div>
                </div>
            </div>

            {{-- ========================================================
                 4. SEKSI 2: KONTAK OPERASIONAL BIMBINGAN
                 ======================================================== --}}
            <div class="profil-section-card" id="sectionKontak">
                <div class="profil-section-header">
                    <div class="profil-section-title-wrap">
                        <div class="profil-section-icon-badge">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="profil-section-heading">Kontak Bimbingan & Komunikasi</h3>
                            <p class="profil-section-subheading">Nomor kontak aktif untuk memudahkan koordinasi bimbingan tugas peserta magang.</p>
                        </div>
                    </div>
                    <button type="button" class="profil-btn-edit" id="btnEditKontak" data-focus-target="no_telp">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                        <span>Edit</span>
                    </button>
                </div>

                <div class="profil-fields-grid">
                    <div class="profil-form-group profil-field-col-full">
                        <label for="no_telp" class="profil-label">
                            <span>Nomor WhatsApp / Telepon Aktif</span>
                        </label>
                        <input type="tel" id="no_telp" name="no_telp" class="profil-input @error('no_telp') is-invalid @enderror" value="{{ old('no_telp', $user->no_telp) }}" placeholder="Contoh: 081234567890">
                        @error('no_telp') <span class="profil-field-error">{{ $message }}</span> @enderror
                        <span class="profil-helper-text">Digunakan oleh peserta magang untuk menghubungi pembimbing saat konfirmasi tugas dan agenda harian.</span>
                    </div>
                </div>
            </div>

            {{-- ========================================================
                 5. BOTTOM ACTION BAR
                 ======================================================== --}}
            <div class="profil-bottom-actions">
                <label class="profil-agreement-label">
                    <input type="checkbox" id="checkPersetujuan" class="profil-checkbox">
                    <span>Saya menyatakan bahwa data profil dan kontak bimbingan yang saya perbarui adalah benar dan siap digunakan untuk koordinasi magang.</span>
                </label>
                <button type="submit" class="profil-btn-submit" id="btnSubmitProfil" disabled>
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
