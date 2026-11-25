@php
    $user = auth()->user();
    $isPeserta = $user?->isPeserta();
    $layout = $isPeserta ? 'layouts.peserta' : 'layouts.dashboard';
    $roleName = $user?->role?->label() ?? __('Akun');
    $dashboardUrl = match($user?->role) {
        \App\Enums\RoleEnum::SuperAdmin => route('superadmin.dashboard'),
        \App\Enums\RoleEnum::Admin => route('admin.dashboard'),
        \App\Enums\RoleEnum::Pembimbing => route('pembimbing.dashboard'),
        default => route('peserta.dashboard'),
    };
    $settingsUrl = match($user?->role) {
        \App\Enums\RoleEnum::SuperAdmin => route('superadmin.akun.index'),
        \App\Enums\RoleEnum::Admin => route('admin.pengaturan.index'),
        \App\Enums\RoleEnum::Pembimbing => route('pembimbing.pengaturan.index'),
        default => route('peserta.pengaturan.index'),
    };
    $backUrl = $backUrl ?? $settingsUrl;
@endphp

@extends($layout)

@section('title', __('Aktivasi 2FA - MagKom'))
@section('topbar_title', __('Langkah Aktivasi Autentikasi Dua Faktor'))

@section('breadcrumbs')
    <a href="{{ $dashboardUrl }}">{{ $roleName }}</a>
    <span class="breadcrumb-separator">/</span>
    <a href="{{ $settingsUrl }}">{{ __('Pengaturan Akun') }}</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">{{ __('Aktivasi 2FA') }}</span>
@endsection

@section('content')
<div class="saas-container" style="max-width: 1140px; width: 100%; margin: 0 auto; padding: 1.5rem 0;">
    <div class="card" style="background: #ffffff; border-radius: 16px; border: 1px solid #E2E8F0; padding: 2.25rem 2.5rem; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem; border-bottom: 1px solid #F1F5F9; padding-bottom: 1.5rem;">
            <div>
                <h2 style="font-size: 1.35rem; font-weight: 800; color: #003566; margin: 0 0 0.5rem 0;">{{ __('Hubungkan Aplikasi Authenticator') }}</h2>
                <p style="font-size: 0.9rem; color: #64748B; margin: 0;">{{ __('Ikuti 2 langkah mudah di bawah ini untuk menyelesaikan aktivasi 2FA pada akun :role Anda.', ['role' => $roleName]) }}</p>
            </div>
            <a href="{{ $backUrl }}" class="btn-action-outline" style="text-decoration: none; padding: 0.5rem 1rem; border-radius: 8px; font-weight: 600; font-size: 0.875rem;">&larr; {{ __('Batal / Kembali') }}</a>
        </div>

        @if($errors->any())
            <div class="app-alert alert-error" style="background: #FEF2F2; border-left: 4px solid #EF4444; padding: 0.875rem 1.25rem; border-radius: 8px; margin-bottom: 2rem; color: #991B1B; font-size: 0.875rem;">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Langkah 1: Pindai Kode / Input Manual --}}
        <div style="display: flex; gap: 2.5rem; align-items: center; background: #F8FAFC; border-radius: 14px; padding: 2rem 2.25rem; border: 1px solid #E2E8F0; margin-bottom: 2rem; flex-wrap: wrap;">
            <div style="text-align: center; background: #ffffff; padding: 1.25rem; border-radius: 12px; border: 1px solid #CBD5E1; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($qrCodeUrl) }}" alt="QR Code 2FA" style="width: 180px; height: 180px; display: block;">
                <span style="display: block; font-size: 0.75rem; font-weight: 600; color: #64748B; margin-top: 0.75rem;">{{ __('Pindai via Google Authenticator') }}</span>
            </div>
            <div style="flex: 1; min-width: 300px;">
                <h3 style="font-size: 1.1rem; font-weight: 700; color: #1E293B; margin: 0 0 0.5rem 0;">{{ __('Langkah 1: Pindai QR Code atau Input Kunci Rahasia') }}</h3>
                <p style="font-size: 0.875rem; color: #475569; line-height: 1.6; margin: 0 0 1.25rem 0;">
                    {{ __('Buka Google Authenticator, Microsoft Authenticator, atau Authy di smartphone Anda. Pilih Scan a QR Code, atau masukkan kunci manual di bawah ini jika kamera mengalami kendala.') }}
                </p>
                <div style="background: #ffffff; padding: 1rem 1.25rem; border-radius: 8px; border: 1.5px dashed #94A3B8;">
                    <div style="font-size: 0.6875rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">{{ __('Kunci Rahasia Manual:') }}</div>
                    <code style="font-size: 1.125rem; font-weight: 800; color: #003566; word-break: break-all; letter-spacing: 0.08em; font-family: monospace;">{{ $secret }}</code>
                </div>
            </div>
        </div>

        {{-- Langkah 2: Konfirmasi Kode --}}
        <div style="background: #F8FAFC; border-radius: 14px; padding: 2rem 2.25rem; border: 1px solid #E2E8F0;">
            <h3 style="font-size: 1.1rem; font-weight: 700; color: #1E293B; margin: 0 0 0.5rem 0;">{{ __('Langkah 2: Masukkan Kode Konfirmasi 6-Digit') }}</h3>
            <p style="font-size: 0.875rem; color: #64748B; line-height: 1.5; margin: 0 0 1.25rem 0;">
                {{ __('Ketikkan kode 6 angka yang saat ini muncul pada aplikasi authenticator Anda untuk mengonfirmasi bahwa sinkronisasi waktu berhasil.') }}
            </p>

            <form action="{{ route('2fa.confirm') }}" method="POST">
                @csrf
                <div style="display: flex; gap: 0.75rem; align-items: center; max-width: 480px; flex-wrap: wrap;">
                    <input type="text" name="code" id="code" required inputmode="numeric" pattern="[0-9]*" maxlength="6" autocomplete="one-time-code" autofocus
                           placeholder="Contoh: 123456"
                           class="fld otp-code-input"
                           style="width: 200px; text-align: center; font-size: 1.15rem; letter-spacing: 0.2em; font-weight: 700; padding: 0.5rem 0.75rem; border: 2px solid #003566; border-radius: 8px; height: 46px; box-sizing: border-box;">
                    <button type="submit" class="btn" style="height: 46px; background: #003566; color: #ffffff; padding: 0 1.25rem; border-radius: 8px; font-weight: 600; font-size: 0.84rem; border: none; cursor: pointer; white-space: nowrap; letter-spacing: 0.01em; transition: background 0.15s ease;">
                        {{ __('Konfirmasi & Aktifkan') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
