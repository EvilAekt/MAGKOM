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

@section('title', __('Autentikasi Dua Faktor (2FA) - MagKom'))
@section('topbar_title', __('Keamanan Akun & Autentikasi Dua Faktor'))

@section('breadcrumbs')
    <a href="{{ $dashboardUrl }}">{{ $roleName }}</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">{{ __('Keamanan 2FA') }}</span>
@endsection

@section('content')
<div class="saas-container" style="max-width: 1140px; width: 100%; margin: 0 auto; padding: 1.5rem 0;">
    @if(session('success'))
        <div class="app-alert alert-success" style="background: #F0FDF4; border-left: 4px solid #22C55E; padding: 1rem 1.25rem; border-radius: 8px; margin-bottom: 1.5rem; color: #15803D;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('info'))
        <div class="app-alert alert-info" style="background: #EFF6FF; border-left: 4px solid #3B82F6; padding: 1rem 1.25rem; border-radius: 8px; margin-bottom: 1.5rem; color: #1D4ED8;">
            {{ session('info') }}
        </div>
    @endif

    <div class="card" style="background: #ffffff; border-radius: 16px; border: 1px solid #E2E8F0; padding: 2.25rem 2.5rem; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);">
        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; border-bottom: 1px solid #F1F5F9; padding-bottom: 1.5rem; margin-bottom: 2rem;">
            <div>
                <h2 style="font-size: 1.35rem; font-weight: 800; color: #003566; margin: 0 0 0.5rem 0;">{{ __('Autentikasi Dua Faktor (TOTP)') }}</h2>
                <p style="font-size: 0.9rem; color: #64748B; margin: 0;">{{ __('Tambahkan lapisan keamanan ekstra pada akun Anda menggunakan aplikasi Google Authenticator, Microsoft Authenticator, atau Authy.') }}</p>
            </div>
            <div>
                @if($enabled)
                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 0.375rem 0.875rem; background: #DCFCE7; color: #166534; font-size: 0.8125rem; font-weight: 600; border-radius: 9999px;">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #16A34A;"></span> {{ __('Aktif') }}
                    </span>
                @else
                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 0.375rem 0.875rem; background: #F1F5F9; color: #64748B; font-size: 0.8125rem; font-weight: 600; border-radius: 9999px;">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #94A3B8;"></span> {{ __('Belum Aktif') }}
                    </span>
                @endif
            </div>
        </div>

        @if(! $enabled)
            <div style="background: #F8FAFC; border-radius: 8px; padding: 1.5rem; border: 1px dashed #CBD5E1; margin-bottom: 1.5rem;">
                <h3 style="font-size: 1rem; font-weight: 600; color: #1E293B; margin: 0 0 0.5rem 0;">{{ __('Mengapa 2FA Sangat Penting untuk Akun Anda?') }}</h3>
                <p style="font-size: 0.875rem; color: #475569; line-height: 1.6; margin: 0 0 1rem 0;">
                    {{ __('Akun staf kedinasan memiliki wewenang terhadap data pendaftaran, kuota magang, penilaian mentee, dan dokumen privat peserta magang. Mengaktifkan 2FA mencegah pengambilalihan akun meskipun kata sandi Anda bocor atau ditebak.') }}
                </p>
                <form action="{{ route('2fa.enable') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="background: #1E3A8A; color: #ffffff; padding: 0.625rem 1.25rem; border-radius: 6px; font-weight: 600; font-size: 0.875rem; border: none; cursor: pointer;">
                        {{ __('Mulai Aktivasi 2FA Sekarang') }} &rarr;
                    </button>
                </form>
            </div>
        @else
            <div style="margin-bottom: 2rem;">
                <p style="font-size: 0.875rem; color: #334155; margin-bottom: 1.5rem;">
                    {{ __('Autentikasi dua faktor aktif sejak :waktu WIB. Setiap login akan meminta kode 6-digit dari aplikasi authenticator Anda.', ['waktu' => $user->two_factor_confirmed_at?->translatedFormat('d F Y H:i')]) }}
                </p>

                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <form action="{{ route('2fa.regenerate-recovery-codes') }}" method="POST">
                        @csrf
                        <div style="margin-bottom: 0.5rem;">
                            <input type="password" name="current_password" required placeholder="{{ __('Masukkan kata sandi saat ini') }}" style="padding: 0.5rem 0.75rem; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 0.875rem;">
                        </div>
                        <button type="submit" class="btn" style="background: #0284C7; color: #ffffff; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.875rem; font-weight: 600; border: none; cursor: pointer;">
                            {{ __('Perbarui Recovery Codes') }}
                        </button>
                    </form>

                    <form action="{{ route('2fa.disable') }}" method="POST">
                        @csrf
                        <div style="margin-bottom: 0.5rem;">
                            <input type="password" name="current_password" required placeholder="{{ __('Masukkan kata sandi saat ini') }}" style="padding: 0.5rem 0.75rem; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 0.875rem;">
                        </div>
                        <button type="submit" class="btn" style="background: #DC2626; color: #ffffff; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.875rem; font-weight: 600; border: none; cursor: pointer;">
                            {{ __('Nonaktifkan 2FA') }}
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
