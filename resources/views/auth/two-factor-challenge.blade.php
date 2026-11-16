@extends('layouts.app')

@section('title', __('Verifikasi Dua Faktor (2FA) - MAGKOM'))

@section('content')
<div class="auth-page-wrapper" style="min-height: 80vh; display: flex; align-items: center; justify-content: center; padding: 2rem 1rem;">
    <div class="auth-card" style="max-width: 440px; width: 100%; background: #ffffff; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); padding: 2.5rem 2rem; border-top: 4px solid #1E3A8A;">
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="display: inline-flex; align-items: center; justify-content: center; width: 56px; height: 56px; border-radius: 50%; background: #EFF6FF; color: #1E3A8A; margin-bottom: 1rem;">
                <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
            </div>
            <h2 style="font-size: 1.375rem; font-weight: 700; color: #0F172A; margin: 0 0 0.5rem 0;">{{ __('Otentikasi Dua Faktor') }}</h2>
            <p style="font-size: 0.875rem; color: #64748B; margin: 0;">{{ __('Masukkan kode 6-digit dari aplikasi Google Authenticator atau kode pemulihan darurat Anda.') }}</p>
        </div>

        @if($errors->any())
            <div class="app-alert alert-error" style="background: #FEF2F2; border-left: 4px solid #EF4444; padding: 0.875rem 1rem; border-radius: 6px; margin-bottom: 1.5rem; color: #991B1B; font-size: 0.875rem;">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('two-factor.challenge.verify') }}" method="POST" id="twoFactorForm">
            @csrf

            <div id="totpSection">
                <label for="code" style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.5rem;">{{ __('Kode Autentikasi (6 Digit)') }}</label>
                <input type="text" name="code" id="code" inputmode="numeric" pattern="[0-9]*" maxlength="6" autocomplete="one-time-code" autofocus
                       placeholder="Contoh: 123456"
                       style="width: 100%; text-align: center; font-size: 1.5rem; letter-spacing: 0.35em; font-weight: 700; padding: 0.75rem 1rem; border: 1.5px solid #CBD5E1; border-radius: 8px; margin-bottom: 1.25rem;">
            </div>

            <details style="margin-bottom: 1.5rem; border: 1px dashed #CBD5E1; border-radius: 8px; padding: 0.75rem 1rem; background: #F8FAFC;">
                <summary style="cursor: pointer; color: #2563EB; font-size: 0.875rem; font-weight: 600; user-select: none;">
                    {{ __('Kehilangan perangkat? Gunakan Kode Pemulihan Darurat') }}
                </summary>
                <div style="margin-top: 1rem;">
                    <label for="recovery_code" style="display: block; font-size: 0.8125rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">{{ __('Kode Pemulihan (Recovery Code)') }}</label>
                    <input type="text" name="recovery_code" id="recovery_code" autocomplete="off"
                           placeholder="Contoh: ABCD1234EF"
                           style="width: 100%; text-align: center; font-family: monospace; font-size: 1.125rem; font-weight: 600; padding: 0.625rem 0.875rem; border: 1.5px solid #CBD5E1; border-radius: 6px; box-sizing: border-box;">
                    <p style="font-size: 0.75rem; color: #64748B; margin: 0.5rem 0 0 0;">{{ __('Kosongkan kolom kode 6 digit di atas jika Anda menggunakan kode pemulihan.') }}</p>
                </div>
            </details>

            <button type="submit" style="width: 100%; background: #1E3A8A; color: #ffffff; border: none; padding: 0.875rem 1rem; border-radius: 8px; font-weight: 600; font-size: 0.9375rem; cursor: pointer; transition: background 0.15s ease;">
                {{ __('Verifikasi & Masuk') }}
            </button>
        </form>
    </div>
</div>
@endsection
