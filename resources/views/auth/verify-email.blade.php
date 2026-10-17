@extends('layouts.auth')

@section('title', 'Verifikasi Alamat Email - Magang Kominfo Sleman')
@section('meta_description', 'Verifikasi alamat email Anda untuk mengaktifkan akses ke Portal MagKom Dinas Komunikasi dan Informatika Kabupaten Sleman.')

@section('content')
    <!-- Alert Notifikasi Flash -->
    @if (session('status') == 'verification-link-sent')
        <div class="auth-alert-success" role="alert">
            Tautan verifikasi baru telah berhasil dikirimkan ke alamat email Anda. Silakan periksa kotak masuk atau folder spam.
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="auth-alert-error" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Header Verifikasi Email -->
    <div class="step-badge">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
            <polyline points="22,6 12,13 2,6"></polyline>
        </svg>
        <span>Aktivasi Akun</span>
    </div>
    <h1 class="auth-title">Verifikasi Alamat Email</h1>
    <p class="auth-subtitle">
        Terima kasih telah mendaftar di Portal MagKom. Kami telah mengirimkan tautan aktivasi akun ke alamat email Anda. Silakan klik tautan tersebut untuk memverifikasi dan mengaktifkan akun Anda.
    </p>

    <!-- Kotak Saran & Bantuan -->
    <div style="background-color: var(--neutral-50); border: 1px solid var(--neutral-200); border-radius: 0.5rem; padding: 1rem 1.25rem; margin-bottom: 1.5rem; font-size: 0.875rem; color: var(--neutral-600); line-height: 1.5;">
        <div style="display: flex; gap: 0.75rem; align-items: flex-start;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--blue-600)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 0.125rem;">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            <div>
                Belum menerima email? Pastikan untuk memeriksa tab <strong>Spam</strong> atau <strong>Promosi</strong> di akun email Anda, atau klik tombol di bawah untuk meminta tautan baru.
            </div>
        </div>
    </div>

    <!-- Tombol Kirim Ulang Verifikasi -->
    <form action="{{ route('verification.send') }}" method="POST">
        @csrf
        <button type="submit" class="btn-primary" style="margin-bottom: 1rem;">
            Kirim Ulang Email Verifikasi
        </button>
    </form>

    <!-- Opsi Keluar Akun -->
    <form action="{{ route('logout') }}" method="POST" style="text-align: center;">
        @csrf
        <button type="submit" style="background: none; border: none; color: var(--neutral-500); font-size: 0.875rem; font-weight: 600; cursor: pointer; text-decoration: underline; padding: 0.5rem 1rem; transition: color 0.15s ease;">
            Keluar dari Akun
        </button>
    </form>
@endsection
