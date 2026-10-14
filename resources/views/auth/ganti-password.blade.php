@extends('layouts.auth')

@section('title', 'Ganti Kata Sandi - Portal MagKom Sleman')
@section('meta_description', 'Ganti kata sandi akun Portal MagKom Dinas Komunikasi dan Informatika Kabupaten Sleman.')

@section('content')


    <!-- Header Form Ganti Password -->
    <div class="step-badge">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
        </svg>
        <span>Keamanan Akun</span>
    </div>
    <h1 class="auth-title">Ganti Kata Sandi</h1>
    <p class="auth-subtitle">Demi standar keamanan instansi, Anda wajib memperbarui kata sandi awal sebelum dapat mengakses fitur portal.</p>

    <!-- Form Ganti Kata Sandi -->
    <form action="{{ route('password.ganti.update') }}" method="POST" novalidate>
        @csrf

        <!-- Field Kata Sandi Saat Ini -->
        <div class="form-group">
            <label for="current_password" class="form-label">Kata Sandi Saat Ini</label>
            <div class="input-wrapper">
                <span class="input-icon-left" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                </span>
                <input 
                    type="password" 
                    id="current_password" 
                    name="current_password" 
                    required 
                    autofocus 
                    class="form-input" 
                    placeholder="Masukkan kata sandi saat ini"
                    autocomplete="current-password"
                    style="padding-right: 2.75rem;"
                >
                <button type="button" class="toggle-password-btn" data-target="current_password" aria-label="Tampilkan atau sembunyikan kata sandi saat ini">
                    <svg class="eye-closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                        <line x1="1" y1="1" x2="23" y2="23"></line>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Field Kata Sandi Baru -->
        <div class="form-group">
            <label for="password" class="form-label">Kata Sandi Baru</label>
            <div class="input-wrapper">
                <span class="input-icon-left" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 2l-2 2m-1.5 1.5L14 9l-1.5-1.5-3 3 1.5 1.5-5 5H2v-4l5-5 1.5 1.5 3-3L12 5.5 13.5 4 15.5 2z"></path>
                    </svg>
                </span>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required 
                    class="form-input" 
                    placeholder="Min. 8 karakter (huruf & angka)"
                    autocomplete="new-password"
                    style="padding-right: 2.75rem;"
                >
                <button type="button" class="toggle-password-btn" data-target="password" aria-label="Tampilkan atau sembunyikan kata sandi baru">
                    <svg class="eye-closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                        <line x1="1" y1="1" x2="23" y2="23"></line>
                    </svg>
                </button>
            </div>
            <span class="form-hint">Minimal 8 karakter dengan kombinasi huruf dan angka.</span>
        </div>

        <!-- Field Konfirmasi Kata Sandi Baru -->
        <div class="form-group">
            <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi Baru</label>
            <div class="input-wrapper">
                <span class="input-icon-left" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </span>
                <input 
                    type="password" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    required 
                    class="form-input" 
                    placeholder="Ulangi kata sandi baru"
                    autocomplete="new-password"
                    style="padding-right: 2.75rem;"
                >
                <button type="button" class="toggle-password-btn" data-target="password_confirmation" aria-label="Tampilkan atau sembunyikan konfirmasi kata sandi baru">
                    <svg class="eye-closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                        <line x1="1" y1="1" x2="23" y2="23"></line>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Tombol Submit -->
        <button type="submit" class="btn-primary" style="margin-top: 1.5rem;">
            Perbarui Kata Sandi & Lanjutkan
        </button>
    </form>
@endsection
