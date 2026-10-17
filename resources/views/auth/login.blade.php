@extends('layouts.auth')

@section('title', 'Login - Magang Kominfo Sleman')
@section('meta_description', 'Masuk ke akun Portal MagKom Dinas Komunikasi dan Informatika Kabupaten Sleman.')

@section('content')
    <!-- Alert Notifikasi Flash -->
    @if(session('success'))
        <div class="auth-alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="auth-alert-error" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Header Form Login -->
    <h1 class="auth-title">Selamat Datang di MagKom</h1>
    <p class="auth-subtitle">Masuk untuk mengakses sistem magang terpadu Kominfo</p>

    <!-- Form Kredensial Email & Password -->
    <form action="{{ route('login') }}" method="POST" novalidate>
        @csrf

        <!-- Field Email -->
        <div class="form-group">
            <label for="email" class="form-label sr-only">Email</label>
            <div class="input-wrapper">
                <span class="input-icon-left" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                </span>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required 
                    autofocus 
                    class="form-input" 
                    placeholder="email"
                    autocomplete="email"
                    aria-label="Email"
                >
            </div>
        </div>

        <!-- Field Kata Sandi -->
        <div class="form-group">
            <label for="password" class="form-label sr-only">Kata Sandi</label>
            <div class="input-wrapper">
                <span class="input-icon-left" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                </span>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required 
                    class="form-input" 
                    placeholder="password"
                    autocomplete="current-password"
                    aria-label="Kata Sandi"
                    style="padding-right: 2.75rem;"
                >
                <button type="button" class="toggle-password-btn" data-target="password" aria-label="Tampilkan atau sembunyikan kata sandi">
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

        <!-- Opsi Ingat Saya & Lupa Kata Sandi -->
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; font-size: 0.875rem;">
            <label style="display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--neutral-700);">
                <input type="checkbox" name="remember" id="remember" style="width: 1rem; height: 1rem; accent-color: var(--blue-600); cursor: pointer;">
                <span>Ingat saya</span>
            </label>
            <a href="{{ route('password.request') }}" style="color: var(--blue-600); font-weight: 600; text-decoration: none;">
                Lupa Kata Sandi?
            </a>
        </div>

        <!-- Cloudflare Turnstile CAPTCHA jika Diaktifkan -->
        @if(config('services.turnstile.enabled'))
            <div style="margin-bottom: 1.25rem;">
                <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}"></div>
                <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
            </div>
        @endif

        <!-- Tombol Submit Login -->
        <button type="submit" class="btn-primary" id="btn-submit-login">
            Login
        </button>
    </form>

    <!-- Divider Pemisah -->
    <div class="auth-divider">
        <span>atau</span>
    </div>

    <!-- Tombol Login dengan Google OAuth -->
    <a href="{{ route('oauth.redirect', 'google') }}" class="btn-google" id="btn-login-google" title="Masuk dengan Google" aria-label="Masuk dengan Google">
        <svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
        </svg>
        <span>Login dengan Google</span>
    </a>

    <!-- Footer Tautan Switch ke Halaman Register -->
    <p class="auth-footer-text">
        Belum punya akun magang? <a href="{{ route('register') }}">Register</a>
    </p>
@endsection
