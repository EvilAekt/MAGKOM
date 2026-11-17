@extends('layouts.dashboard')

@php
    $dashboardUrl = match(auth()->user()?->role) {
        \App\Enums\RoleEnum::SuperAdmin => route('superadmin.dashboard'),
        \App\Enums\RoleEnum::Admin => route('admin.dashboard'),
        \App\Enums\RoleEnum::Pembimbing => route('pembimbing.dashboard'),
        default => route('peserta.dashboard'),
    };
    $roleName = auth()->user()?->role?->label() ?? __('Akun');
@endphp

@section('title', __('Kode Pemulihan 2FA - MagKom'))
@section('topbar_title', __('Kode Pemulihan Darurat (Emergency Recovery Codes)'))

@section('breadcrumbs')
    <a href="{{ $dashboardUrl }}">{{ $roleName }}</a>
    <span class="breadcrumb-separator">/</span>
    <a href="{{ route('2fa.settings') }}">{{ __('Keamanan 2FA') }}</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">{{ __('Recovery Codes') }}</span>
@endsection

@section('content')
<div class="saas-container" style="max-width: 760px; margin: 0 auto; padding: 1.5rem 0;">
    <div class="card" style="background: #ffffff; border-radius: 12px; border: 1px solid #E2E8F0; padding: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <div style="background: #FEF3C7; border-left: 4px solid #F59E0B; padding: 1rem 1.25rem; border-radius: 6px; margin-bottom: 1.5rem;">
            <h3 style="font-size: 0.9375rem; font-weight: 700; color: #92400E; margin: 0 0 0.25rem 0;">{{ __('PENTING: Simpan Kode Pemulihan Ini Sekarang!') }}</h3>
            <p style="font-size: 0.8125rem; color: #B45309; line-height: 1.5; margin: 0;">
                {{ __('Kode pemulihan ini hanya ditampilkan satu kali ini. Jika perangkat authenticator Anda hilang, rusak, atau terhapus, kode-kode ini adalah satu-satunya cara Anda dapat masuk kembali ke sistem. Simpan di tempat yang aman (misalnya Password Manager atau cetak secara fisik).') }}
            </p>
        </div>

        <div style="background: #F8FAFC; border: 1px solid #CBD5E1; border-radius: 8px; padding: 1.5rem; margin-bottom: 2rem;">
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; font-family: monospace; font-size: 1.125rem; font-weight: 700; color: #0F172A;">
                @foreach($recoveryCodes as $code)
                    <div style="background: #ffffff; padding: 0.625rem 1rem; border-radius: 6px; border: 1px solid #E2E8F0; text-align: center;">
                        {{ $code }}
                    </div>
                @endforeach
            </div>
        </div>

        <div style="display: flex; gap: 1rem; align-items: center; justify-content: space-between; flex-wrap: wrap;">
            <span style="font-size: 0.8125rem; color: #64748B;">
                {!! __('Tips: Simpan kode ini ke password manager atau gunakan <strong>Ctrl + P</strong> untuk menyimpan sebagai PDF.') !!}
            </span>
            <a href="{{ route('2fa.settings') }}" class="btn btn-primary" style="background: #1E3A8A; color: #ffffff; padding: 0.625rem 1.5rem; border-radius: 6px; font-weight: 600; font-size: 0.875rem; text-decoration: none;">
                {{ __('Saya Sudah Menyimpan Seluruh Kode') }} &rarr;
            </a>
        </div>
    </div>
</div>
@endsection
