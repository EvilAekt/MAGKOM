@extends('layouts.auth')

@section('title', 'Lupa Kata Sandi - Portal MagKom Sleman')
@section('meta_description', 'Pemulihan kata sandi akun peserta magang Dinas Komunikasi dan Informatika Kabupaten Sleman.')

@section('content')


    <!-- Header Lupa Kata Sandi -->
    <div class="step-badge">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 2l-2 2m-1.5 1.5L14 9l-1.5-1.5-3 3 1.5 1.5-5 5H2v-4l5-5 1.5 1.5 3-3L12 5.5 13.5 4 15.5 2z"></path>
        </svg>
        <span>Pemulihan Akun</span>
    </div>
    <h1 class="auth-title">Lupa Kata Sandi?</h1>
    <p class="auth-subtitle">
        Masukkan alamat email yang terdaftar di akun Anda untuk menerima tautan instruksi pemulihan kata sandi.
    </p>

    <!-- Form Pengiriman Tautan Reset Password -->
    <form action="{{ route('password.email') }}" method="POST" novalidate>
        @csrf

        <!-- Field Email -->
        <div class="form-group">
            <label for="email" class="form-label sr-only">Alamat Email</label>
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
                    placeholder="nama@email.com"
                    autocomplete="email"
                >
            </div>
        </div>

        <button type="submit" class="btn-primary" style="margin-top: 1.5rem;">
            Kirim Tautan Pemulihan
        </button>
    </form>

    <p class="auth-footer-text">
        Ingat kata sandi Anda? <a href="{{ route('login') }}">Masuk kembali</a>
    </p>
@endsection
