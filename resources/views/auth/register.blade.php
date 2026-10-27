@extends('layouts.auth')

@section('title', 'Register - Magang Kominfo Sleman')
@section('meta_description', 'Registrasi akun calon peserta magang / PKL Dinas Komunikasi dan Informatika Kabupaten Sleman.')

@section('content')


    <!-- Header Form Register -->
    <h1 class="auth-title">Register Akun MagKom</h1>
    <p class="auth-subtitle">Daftarkan akun Anda untuk memulai proses seleksi magang di Dinas Kominfo.</p>

    @if(isset($selectedJurusan) && $selectedJurusan)
        <div style="background: linear-gradient(135deg, rgba(7, 81, 255, 0.05) 0%, rgba(59, 130, 246, 0.1) 100%); border: 1px solid rgba(7, 81, 255, 0.22); border-radius: 0.875rem; padding: 1rem 1.15rem; margin-bottom: 1.5rem; display: flex; align-items: flex-start; gap: 0.85rem;">
            <div style="width: 2.5rem; height: 2.5rem; border-radius: 0.625rem; background: var(--blue-600); color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 10px rgba(7, 81, 255, 0.25);">
                @if($selectedTipe === 'kelompok')
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                @else
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                @endif
            </div>
            <div style="flex: 1; min-width: 0;">
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 0.25rem;">
                    <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--blue-700);">
                        Pilihan Magang Terpilih
                    </span>
                    <span style="font-size: 0.75rem; font-weight: 700; padding: 0.15rem 0.65rem; border-radius: 9999px; background: {{ $selectedTipe === 'kelompok' ? '#dbeafe' : '#e0e7ff' }}; color: {{ $selectedTipe === 'kelompok' ? '#1e40af' : '#3730a3' }};">
                        {{ $selectedTipe === 'kelompok' ? 'Kelompok (' . $selectedJumlah . ' Orang)' : 'Mandiri (1 Orang)' }}
                    </span>
                </div>
                <div style="font-size: 0.95rem; font-weight: 700; color: var(--neutral-900); margin-bottom: 0.25rem;">
                    {{ $selectedJurusan->nama_jurusan }} ({{ strtoupper($selectedJurusan->kategori) }})
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; margin-top: 0.35rem; flex-wrap: wrap;">
                    @if($selectedBulan)
                        <div style="font-size: 0.8125rem; color: var(--neutral-600);">
                            Periode Mulai: <strong>{{ \Carbon\CarbonImmutable::parse($selectedBulan->bulan_mulai)->translatedFormat('F Y') }}</strong>
                        </div>
                    @endif
                    <a href="{{ route('landing') }}#kuota" style="font-size: 0.75rem; font-weight: 700; color: var(--blue-600); text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem;">
                        <span>&larr; Ganti Pilihan Kuota</span>
                    </a>
                </div>
            </div>
        </div>
    @endif

    @if(($selectedTipe ?? request('tipe')) === 'kelompok')
        <div style="background: #EFF6FF; border: 1.5px solid #93C5FD; border-radius: 0.75rem; padding: 0.875rem 1rem; margin-bottom: 1.5rem; display: flex; align-items: flex-start; gap: 0.75rem;">
            <div style="width: 28px; height: 28px; border-radius: 6px; background: #0751FF; color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
            <div style="font-size: 0.8125rem; color: #1E40AF; line-height: 1.5;">
                <strong style="color: #1E3A8A; display: block; margin-bottom: 2px;">Pendaftaran Akun Ketua / Perwakilan Kelompok:</strong>
                Anda mendaftar sebagai perwakilan kelompok ({{ $selectedJumlah ?? request('jumlah', 2) }} orang). Rekan tim Anda <strong>tidak perlu membuat akun terpisah</strong>. Seluruh identitas & berkas rekan tim akan dimasukkan langsung pada formulir permohonan magang setelah akun ini aktif.
            </div>
        </div>
    @endif

    <!-- Form Registrasi Akun Baru -->
    <form action="{{ route('register') }}" method="POST" novalidate>
        @csrf

        @if(request('bulan'))
            <input type="hidden" name="redirect_bulan" value="{{ request('bulan') }}">
        @endif
        @if(request('jurusan'))
            <input type="hidden" name="redirect_jurusan" value="{{ request('jurusan') }}">
        @endif
        @if(request('tipe'))
            <input type="hidden" name="redirect_tipe" value="{{ request('tipe') }}">
        @endif
        @if(request('jumlah'))
            <input type="hidden" name="redirect_jumlah" value="{{ request('jumlah') }}">
        @endif

        <!-- Field Username -->
        <div class="form-group">
            <label for="username" class="form-label sr-only">Username</label>
            <div class="input-wrapper">
                <span class="input-icon-left" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </span>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    value="{{ old('username', old('nama')) }}" 
                    required 
                    autofocus 
                    class="form-input" 
                    placeholder="username"
                    autocomplete="username"
                    aria-label="Username"
                >
            </div>
        </div>

        <!-- Field Alamat Email Aktif -->
        <div class="form-group">
            <label for="email" class="form-label sr-only">Alamat Email Aktif</label>
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
                    class="form-input" 
                    placeholder="email"
                    autocomplete="email"
                    aria-label="Alamat Email Aktif"
                >
            </div>
            <span class="form-hint">Gunakan email aktif untuk verifikasi berkas dan notifikasi seleksi.</span>
        </div>

        <!-- Field Nomor Telepon -->
        <div class="form-group">
            <label for="no_telp" class="form-label sr-only">Nomor Telepon</label>
            <div class="input-wrapper">
                <span class="input-icon-left" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                    </svg>
                </span>
                <input 
                    type="tel" 
                    id="no_telp" 
                    name="no_telp" 
                    value="{{ old('no_telp') }}" 
                    required 
                    class="form-input" 
                    placeholder="no telp"
                    autocomplete="tel"
                    aria-label="Nomor Telepon"
                >
            </div>
            <span class="form-hint">Nomor WhatsApp aktif untuk koordinasi dan informasi seleksi.</span>
        </div>

        <!-- Field Kata Sandi -->
        <div class="form-group">
            <label for="password" class="form-label sr-only">Kata Sandi / Password</label>
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
                    autocomplete="new-password"
                    aria-label="Kata Sandi"
                    style="padding-right: 2.75rem;"
                >
                <button type="button" class="toggle-password-btn" data-target="password" aria-label="Tampilkan atau sembunyikan kata sandi">
                    <svg class="eye-closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                        <line x1="1" y1="1" x2="23" y2="23"></line>
                    </svg>
                </button>
            </div>
            <span class="form-hint">Minimal 8 karakter kombinasi huruf dan angka.</span>
        </div>

        <!-- Checkbox Persetujuan Kebijakan Privasi -->
        <div style="margin-bottom: 1.5rem; font-size: 0.8125rem;">
            <label style="display: flex; align-items: flex-start; gap: 0.625rem; cursor: pointer; color: var(--neutral-600); line-height: 1.4;">
                <input type="checkbox" name="setuju_privasi" id="setuju_privasi" required checked style="width: 1.1rem; height: 1.1rem; accent-color: var(--blue-600); cursor: pointer; margin-top: 0.125rem;">
                <span>
                    Saya menyetujui <a href="{{ route('kebijakan-privasi') }}" target="_blank" style="color: var(--blue-600); font-weight: 600; text-decoration: underline;">Kebijakan Privasi</a> Dinas Komunikasi dan Informatika Kabupaten Sleman.
                </span>
            </label>
        </div>

        <!-- Cloudflare Turnstile CAPTCHA jika Diaktifkan -->
        @if(config('services.turnstile.enabled'))
            <div style="margin-bottom: 1.25rem;">
                <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}"></div>
                <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
            </div>
        @endif

        <!-- Dynamic SVG Security CAPTCHA (4 Kode) -->
        <x-captcha-field />

        <!-- Tombol Submit Register -->
        <button type="submit" class="btn-primary" id="btn-submit-register">
            Register
        </button>
    </form>

    <!-- Divider Pemisah -->
    <div class="auth-divider">
        <span>atau</span>
    </div>

    <!-- Tombol Daftar dengan Google OAuth -->
    <a href="{{ route('oauth.redirect', 'google') }}" class="btn-google" id="btn-register-google" title="Daftar dengan Google" aria-label="Daftar dengan Google">
        <svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
        </svg>
        <span>Register dengan Google</span>
    </a>

    <!-- Footer Tautan Switch ke Halaman Login -->
    <p class="auth-footer-text">
        Sudah punya akun? <a href="{{ route('login') }}">Login</a>
    </p>
@endsection
