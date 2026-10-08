@extends('layouts.app')

@section('title', 'Masuk Akun - Portal MagKom')

@section('content')
<div class="container" style="max-width: 450px; margin: 4rem auto;">
    <div style="background: #ffffff; padding: 2.5rem; border-radius: 1rem; border: 1px solid var(--gray-200); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
        <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--gray-900); margin-bottom: 0.5rem; text-align: center;">Masuk ke Portal</h2>
        <p style="font-size: 0.875rem; color: var(--gray-600); margin-bottom: 2rem; text-align: center;">Gunakan email dan kata sandi yang telah terdaftar.</p>

        @if(session('success'))
            <div style="background: #dcfce7; border-left: 4px solid #22c55e; padding: 0.75rem 1rem; border-radius: 0.375rem; margin-bottom: 1.5rem; color: #15803d; font-size: 0.875rem;">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background: #fee2e2; border-left: 4px solid #ef4444; padding: 0.75rem 1rem; border-radius: 0.375rem; margin-bottom: 1.5rem; color: #991b1b; font-size: 0.875rem;">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <div style="margin-bottom: 1.25rem;">
                <label for="email" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--gray-700); margin-bottom: 0.5rem;">Alamat Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--gray-300); border-radius: 0.5rem; font-size: 0.95rem;">
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label for="password" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--gray-700); margin-bottom: 0.5rem;">Kata Sandi</label>
                <input type="password" id="password" name="password" required style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--gray-300); border-radius: 0.5rem; font-size: 0.95rem;">
            </div>

            @if(config('services.turnstile.enabled'))
                <div style="margin-bottom: 1.25rem;">
                    <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}"></div>
                    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
                </div>
            @endif

            <button type="submit" style="width: 100%; background: #2563eb; color: #ffffff; padding: 0.75rem; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 1rem; cursor: pointer; transition: background 0.2s;">
                Masuk
            </button>
        </form>

        <div style="display: flex; align-items: center; margin: 1.5rem 0;">
            <div style="flex-grow: 1; height: 1px; background: var(--gray-300);"></div>
            <span style="padding: 0 0.75rem; font-size: 0.8rem; color: var(--gray-500); text-transform: uppercase;">atau</span>
            <div style="flex-grow: 1; height: 1px; background: var(--gray-300);"></div>
        </div>

        <a href="{{ route('oauth.redirect', 'google') }}" style="display: flex; align-items: center; justify-content: center; gap: 0.75rem; width: 100%; padding: 0.75rem; background: #ffffff; color: var(--gray-700); border: 1px solid var(--gray-300); border-radius: 0.5rem; font-weight: 600; font-size: 0.95rem; text-decoration: none; transition: background 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            <svg width="18" height="18" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
            </svg>
            Masuk dengan Google
        </a>

        <p style="text-align: center; font-size: 0.875rem; color: var(--gray-600); margin-top: 1.5rem;">
            Belum memiliki akun? <a href="{{ route('register') }}" style="color: #2563eb; text-decoration: none; font-weight: 600;">Daftar di sini</a>
        </p>
    </div>
</div>
@endsection
