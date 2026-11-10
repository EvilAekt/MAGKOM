@extends('layouts.peserta')

@section('title', 'Akun & Preferensi Saya - Portal Peserta MagKom')
@section('page_title', 'Akun & Preferensi Saya')

@section('content')
<div class="pengaturan-akun-page">

    {{-- Flash Notifications --}}
    @if(session('success'))
        <x-gentelella.callout type="success" :title="__('Berhasil')">
            {{ session('success') }}
        </x-gentelella.callout>
    @endif
    @if(session('error'))
        <x-gentelella.callout type="danger" :title="__('Gagal')">
            {{ session('error') }}
        </x-gentelella.callout>
    @endif
    @if($errors->any())
        <x-gentelella.callout type="danger" :title="__('Perhatian')">
            {{ $errors->first() }}
        </x-gentelella.callout>
    @endif

    {{-- Hero Banner --}}
    <div class="settings-hero-banner">
        <div class="banner-content-left">
            <h1 class="banner-title">Akun & Preferensi Saya</h1>
            <p class="banner-subtitle">Kelola informasi kredensial, keamanan kata sandi, dan tema tampilan portal peserta magang.</p>
            <div class="banner-capsules-row">
                <div class="hero-context-pill">
                    <span class="pill-micro-label">Peserta</span>
                    <span class="pill-strong-value">{{ $user->nama ?? $user->name }}</span>
                </div>
                <div class="hero-context-pill">
                    <span class="pill-micro-label">Penempatan</span>
                    <span class="pill-strong-value">{{ $penempatan->bidang->nama_bidang ?? 'Dinas Komunikasi dan Informatika' }}</span>
                </div>
            </div>
        </div>

        <div class="banner-gold-widget">
            <span class="widget-label">Status Akun</span>
            <span class="widget-status-value">Aktif</span>
            <span class="widget-subtext">Portal peserta magang</span>
            <a href="{{ route('peserta.profil.show') }}" class="btn-gold-action">Lihat Profil Lengkap</a>
        </div>
    </div>

    {{-- Bento Grid 2 Kolom --}}
    <div class="settings-grid-2col">

        {{-- Kolom Kiri: Informasi Akun --}}
        <div class="settings-white-card">
            <h2 class="card-heading-title">Informasi Akun</h2>
            <p class="card-heading-subtitle">Ringkasan kredensial dan data diri peserta magang terdaftar.</p>

            <div class="profile-identity-box">
                <div class="avatar-circle">
                    @if($user->profil?->foto_url)
                        <img src="{{ $user->profil->foto_url }}" alt="{{ $user->nama ?? $user->name }}">
                    @else
                        {{ strtoupper(substr($user->nama ?? $user->name ?? 'P', 0, 2)) }}
                    @endif
                </div>
                <div class="identity-meta">
                    <span class="identity-name">{{ $user->nama ?? $user->name }}</span>
                    <span class="role-chip-pill">Peserta Magang</span>
                </div>
            </div>

            <div class="credential-rows">
                <div class="credential-row">
                    <span class="credential-label">Nama Lengkap</span>
                    <span class="credential-value">{{ $user->nama ?? $user->name }}</span>
                </div>
                <div class="credential-row">
                    <span class="credential-label">Alamat Email</span>
                    <span class="credential-value">{{ $user->email }}</span>
                </div>
                <div class="credential-row">
                    <span class="credential-label">Nomor Kontak</span>
                    <span class="credential-value">{{ $user->no_telp ?? $profil->no_hp ?? 'Belum diisi' }}</span>
                </div>
                <div class="credential-row">
                    <span class="credential-label">Instansi / Kampus</span>
                    <span class="credential-value">{{ $profil->asal_instansi ?? 'Belum diisi' }}</span>
                </div>
                <div class="credential-row">
                    <span class="credential-label">Status Akun</span>
                    <span class="credential-value">
                        <span class="badge-status-gold">Aktif</span>
                    </span>
                </div>
            </div>

            <div class="alert-info-box">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                <span>Biodata lengkap, riwayat pendidikan, dan nomor kontak dapat diperbarui di halaman <a href="{{ route('peserta.profil.show') }}">Profil Saya</a>.</span>
            </div>
        </div>

        {{-- Kolom Kanan: Ganti Kata Sandi --}}
        <div class="settings-white-card">
            <h2 class="card-heading-title">Ganti Kata Sandi</h2>
            <p class="card-heading-subtitle">Gunakan kombinasi sandi yang aman dan belum pernah digunakan di sistem lain.</p>

            <form action="{{ route('peserta.pengaturan.password.update') }}" method="POST" id="passwordForm">
                @csrf
                @method('PUT')

                {{-- Kata Sandi Saat Ini --}}
                <div class="form-group-password">
                    <label for="current_password" class="form-label-password">Kata sandi saat ini</label>
                    <div class="input-password-wrapper">
                        <input type="password" 
                               name="current_password" 
                               id="current_password" 
                               class="input-password-field @error('current_password') is-invalid @enderror" 
                               placeholder="Masukkan kata sandi saat ini" 
                               required>
                        <button type="button" class="toggle-eye-btn" data-toggle-target="current_password" aria-label="Lihat kata sandi">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                    @error('current_password')
                        <div style="font-size: 0.75rem; color: #EF4444; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Kata Sandi Baru --}}
                <div class="form-group-password">
                    <label for="password" class="form-label-password">Kata sandi baru</label>
                    <div class="input-password-wrapper">
                        <input type="password" 
                               name="password" 
                               id="password" 
                               class="input-password-field @error('password') is-invalid @enderror" 
                               placeholder="Masukkan kata sandi baru" 
                               required>
                        <button type="button" class="toggle-eye-btn" data-toggle-target="password" aria-label="Lihat kata sandi">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <div style="font-size: 0.75rem; color: #EF4444; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Konfirmasi Kata Sandi Baru --}}
                <div class="form-group-password">
                    <label for="password_confirmation" class="form-label-password">Konfirmasi kata sandi baru</label>
                    <div class="input-password-wrapper">
                        <input type="password" 
                               name="password_confirmation" 
                               id="password_confirmation" 
                               class="input-password-field @error('password_confirmation') is-invalid @enderror" 
                               placeholder="Ulangi kata sandi baru" 
                               required>
                        <button type="button" class="toggle-eye-btn" data-toggle-target="password_confirmation" aria-label="Lihat kata sandi">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <div style="font-size: 0.75rem; color: #EF4444; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="password-hint-text">Minimal 8 karakter, kombinasi huruf dan angka atau simbol.</div>

                <div class="password-actions-row">
                    <button type="submit" class="btn-action-navy" id="btnSubmitPassword">Perbarui Kata Sandi</button>
                    <button type="button" class="btn-action-outline" onclick="document.getElementById('passwordForm').reset()">Batal</button>
                </div>
            </form>
        </div>

    </div>

    {{-- Kartu Penuh Bawah: Preferensi Antarmuka & Bahasa --}}
    <div class="settings-white-card">
        <h2 class="card-heading-title">{{ __('Preferensi Antarmuka & Bahasa') }}</h2>
        <p class="card-heading-subtitle">{{ __('Sesuaikan kenyamanan visual dan bahasa antarmuka saat mengakses portal peserta.') }}</p>

        <form action="{{ route('peserta.pengaturan.preferensi.update') }}" method="POST">
            @csrf

            <div class="pref-horizontal-grid">
                {{-- Pilihan Bahasa --}}
                <div>
                    <label class="pref-label-title" for="bahasaSelect">{{ __('Bahasa Antarmuka') }}</label>
                    <select name="bahasa" id="bahasaSelect" class="pref-select-field">
                        <option value="id" {{ ($bahasaAktif ?? 'id') === 'id' ? 'selected' : '' }}>{{ __('Indonesia (Default)') }}</option>
                        <option value="en" {{ ($bahasaAktif ?? '') === 'en' ? 'selected' : '' }}>{{ __('English (US)') }}</option>
                    </select>
                    <div class="pref-helper-caption">{{ __('Bahasa yang digunakan untuk menu dan tampilan sistem.') }}</div>
                </div>

                {{-- Pilihan Tema --}}
                <div>
                    <label class="pref-label-title">{{ __('Mode Tampilan Tema') }}</label>
                    <div class="theme-cards-grid">
                        {{-- Terang --}}
                        <label class="theme-preview-card {{ ($temaAktif ?? 'terang') === 'terang' ? 'selected' : '' }}">
                            <input type="radio" name="tema" value="terang" {{ ($temaAktif ?? 'terang') === 'terang' ? 'checked' : '' }}>
                            <div class="theme-mini-box mini-box-light">
                                <span class="preview-dot"></span>
                                <span class="preview-bar"></span>
                            </div>
                            <div>
                                <h3 class="theme-card-title">{{ __('Terang') }}</h3>
                                <p class="theme-card-desc">{{ __('Mode default portal') }}</p>
                            </div>
                        </label>

                        {{-- Gelap --}}
                        <label class="theme-preview-card {{ ($temaAktif ?? '') === 'gelap' ? 'selected' : '' }}">
                            <input type="radio" name="tema" value="gelap" {{ ($temaAktif ?? '') === 'gelap' ? 'checked' : '' }}>
                            <div class="theme-mini-box mini-box-dark">
                                <span class="preview-dot"></span>
                                <span class="preview-bar"></span>
                            </div>
                            <div>
                                <h3 class="theme-card-title">{{ __('Gelap') }}</h3>
                                <p class="theme-card-desc">{{ __('Ramah mata untuk malam hari') }}</p>
                            </div>
                        </label>

                        {{-- Ikuti Sistem --}}
                        <label class="theme-preview-card {{ ($temaAktif ?? '') === 'sistem' ? 'selected' : '' }}">
                            <input type="radio" name="tema" value="sistem" {{ ($temaAktif ?? '') === 'sistem' ? 'checked' : '' }}>
                            <div class="theme-mini-box mini-box-system">
                                <span class="preview-dot"></span>
                                <span class="preview-bar"></span>
                            </div>
                            <div>
                                <h3 class="theme-card-title">{{ __('Ikuti Sistem') }}</h3>
                                <p class="theme-card-desc">{{ __('Menyesuaikan pengaturan OS') }}</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div>
                <button type="submit" class="btn-action-navy">{{ __('Simpan Preferensi Tampilan') }}</button>
            </div>
        </form>
    </div>

</div>
@endsection
