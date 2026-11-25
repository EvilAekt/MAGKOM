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

@section('title', __('Kode Pemulihan 2FA - MagKom'))
@section('topbar_title', __('Kode Pemulihan Darurat (Emergency Recovery Codes)'))

@section('breadcrumbs')
    <a href="{{ $dashboardUrl }}">{{ $roleName }}</a>
    <span class="breadcrumb-separator">/</span>
    <a href="{{ $settingsUrl }}">{{ __('Pengaturan Akun') }}</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">{{ __('Recovery Codes') }}</span>
@endsection

@section('content')
<div class="saas-container" style="max-width: 1140px; width: 100%; margin: 0 auto; padding: 1.5rem 0;">
    <div class="card" style="background: #ffffff; border-radius: 16px; border: 1px solid #E2E8F0; padding: 2.25rem 2.5rem; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);">
        <div style="background: #FEF3C7; border-left: 4px solid #F59E0B; padding: 1.25rem 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
            <h3 style="font-size: 1rem; font-weight: 700; color: #92400E; margin: 0 0 0.35rem 0;">{{ __('PENTING: Simpan Kode Pemulihan Ini Sekarang!') }}</h3>
            <p style="font-size: 0.875rem; color: #B45309; line-height: 1.6; margin: 0;">
                {{ __('Kode pemulihan ini hanya ditampilkan satu kali ini. Jika perangkat authenticator Anda hilang, rusak, atau terhapus, kode-kode ini adalah satu-satunya cara Anda dapat masuk kembali ke sistem. Simpan di tempat yang aman (misalnya Password Manager atau cetak secara fisik).') }}
            </p>
        </div>

        <div style="background: #F8FAFC; border: 1px solid #CBD5E1; border-radius: 12px; padding: 2rem 2.25rem; margin-bottom: 2rem;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; font-family: monospace; font-size: 1.15rem; font-weight: 700; color: #003566;">
                @foreach($recoveryCodes as $code)
                    <div style="background: #ffffff; padding: 0.875rem 1.25rem; border-radius: 8px; border: 1px solid #E2E8F0; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                        {{ $code }}
                    </div>
                @endforeach
            </div>
        </div>

        <div style="display: flex; gap: 1.5rem; align-items: center; justify-content: space-between; flex-wrap: wrap; border-top: 1px solid #F1F5F9; padding-top: 1.5rem;">
            <span style="font-size: 0.875rem; color: #64748B;">
                {!! __('Tips: Simpan kode ini ke password manager atau gunakan <strong>Ctrl + P</strong> untuk menyimpan sebagai PDF.') !!}
            </span>
            <a href="{{ $backUrl }}" class="btn btn-primary" style="background: #003566; color: #ffffff; padding: 0.75rem 1.75rem; border-radius: 8px; font-weight: 700; font-size: 0.9375rem; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                {{ __('Saya Sudah Menyimpan Seluruh Kode') }} &rarr;
            </a>
        </div>
    </div>
</div>
@endsection
