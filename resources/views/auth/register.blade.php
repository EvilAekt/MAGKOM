@extends('layouts.app')

@section('title', 'Daftar Calon Peserta - Portal MagKom')

@section('content')
<div class="container" style="max-width: 480px; margin: 4rem auto;">
    <div style="background: #ffffff; padding: 2.5rem; border-radius: 1rem; border: 1px solid var(--gray-200); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
        <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--gray-900); margin-bottom: 0.5rem; text-align: center;">Registrasi Akun Baru</h2>
        <p style="font-size: 0.875rem; color: var(--gray-600); margin-bottom: 2rem; text-align: center;">Khusus calon peserta magang / PKL Dinas Kominfo.</p>

        @if($errors->any())
            <div style="background: #fee2e2; border-left: 4px solid #ef4444; padding: 0.75rem 1rem; border-radius: 0.375rem; margin-bottom: 1.5rem; color: #991b1b; font-size: 0.875rem;">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf

            <div style="margin-bottom: 1.25rem;">
                <label for="nama" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--gray-700); margin-bottom: 0.5rem;">Nama Lengkap</label>
                <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required autofocus style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--gray-300); border-radius: 0.5rem; font-size: 0.95rem;">
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label for="email" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--gray-700); margin-bottom: 0.5rem;">Alamat Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--gray-300); border-radius: 0.5rem; font-size: 0.95rem;">
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label for="password" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--gray-700); margin-bottom: 0.5rem;">Kata Sandi (Min. 8 karakter)</label>
                <input type="password" id="password" name="password" required style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--gray-300); border-radius: 0.5rem; font-size: 0.95rem;">
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label for="password_confirmation" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--gray-700); margin-bottom: 0.5rem;">Konfirmasi Kata Sandi</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--gray-300); border-radius: 0.5rem; font-size: 0.95rem;">
            </div>

            @if(config('services.turnstile.enabled'))
                <div style="margin-bottom: 1.25rem;">
                    <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}"></div>
                    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
                </div>
            @endif

            <button type="submit" style="width: 100%; background: #2563eb; color: #ffffff; padding: 0.75rem; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 1rem; cursor: pointer; transition: background 0.2s;">
                Daftar Akun
            </button>
        </form>

        <p style="text-align: center; font-size: 0.875rem; color: var(--gray-600); margin-top: 1.5rem;">
            Sudah memiliki akun? <a href="{{ route('login') }}" style="color: #2563eb; text-decoration: none; font-weight: 600;">Masuk di sini</a>
        </p>
    </div>
</div>
@endsection
