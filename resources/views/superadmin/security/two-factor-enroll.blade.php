@extends('layouts.dashboard')

@section('title', 'Aktivasi 2FA - SuperAdmin MagKom')
@section('topbar_title', 'Langkah Aktivasi Autentikasi Dua Faktor')

@section('breadcrumbs')
    <a href="{{ route('superadmin.dashboard') }}">SuperAdmin</a>
    <span class="breadcrumb-separator">/</span>
    <a href="{{ route('superadmin.2fa.settings') }}">Keamanan 2FA</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Enrollment</span>
@endsection

@section('content')
<div class="saas-container" style="max-width: 760px; margin: 0 auto; padding: 1.5rem 0;">
    <div class="card" style="background: #ffffff; border-radius: 12px; border: 1px solid #E2E8F0; padding: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <h2 style="font-size: 1.25rem; font-weight: 700; color: #0F172A; margin: 0 0 0.5rem 0;">Hubungkan Aplikasi Authenticator</h2>
        <p style="font-size: 0.875rem; color: #64748B; margin: 0 0 2rem 0;">Ikuti 2 langkah mudah di bawah ini untuk menyelesaikan aktivasi 2FA pada akun SuperAdmin Anda.</p>

        @if($errors->any())
            <div class="app-alert alert-error" style="background: #FEF2F2; border-left: 4px solid #EF4444; padding: 0.875rem 1rem; border-radius: 6px; margin-bottom: 1.5rem; color: #991B1B; font-size: 0.875rem;">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Langkah 1: Pindai Kode / Input Manual --}}
        <div style="display: flex; gap: 2rem; align-items: center; background: #F8FAFC; border-radius: 10px; padding: 1.5rem; border: 1px solid #E2E8F0; margin-bottom: 2rem; flex-wrap: wrap;">
            <div style="text-align: center; background: #ffffff; padding: 1rem; border-radius: 8px; border: 1px solid #CBD5E1;">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($qrCodeUrl) }}" alt="QR Code 2FA" style="width: 180px; height: 180px; display: block;">
                <span style="display: block; font-size: 0.6875rem; color: #64748B; margin-top: 0.5rem;">Pindai via Google Authenticator</span>
            </div>
            <div style="flex: 1; min-width: 260px;">
                <h3 style="font-size: 1rem; font-weight: 600; color: #1E293B; margin: 0 0 0.5rem 0;">Langkah 1: Pindai QR Code atau Input Kunci Rahasia</h3>
                <p style="font-size: 0.8125rem; color: #64748B; line-height: 1.5; margin: 0 0 1rem 0;">
                    Buka Google Authenticator, Microsoft Authenticator, atau Authy di smartphone Anda. Pilih <em>Scan a QR Code</em>, atau masukkan kunci manual di bawah jika kamera bermasalah.
                </p>
                <div style="background: #ffffff; padding: 0.75rem 1rem; border-radius: 6px; border: 1.5px dashed #94A3B8;">
                    <div style="font-size: 0.6875rem; font-weight: 600; color: #475569; text-transform: uppercase; margin-bottom: 4px;">Kunci Rahasia Manual:</div>
                    <code style="font-size: 1rem; font-weight: 700; color: #1E3A8A; word-break: break-all; letter-spacing: 0.05em;">{{ $secret }}</code>
                </div>
            </div>
        </div>

        {{-- Langkah 2: Konfirmasi Kode --}}
        <div>
            <h3 style="font-size: 1rem; font-weight: 600; color: #1E293B; margin: 0 0 0.5rem 0;">Langkah 2: Masukkan Kode Konfirmasi 6-Digit</h3>
            <p style="font-size: 0.8125rem; color: #64748B; margin: 0 0 1.25rem 0;">
                Ketikkan kode 6 angka yang saat ini muncul pada aplikasi authenticator Anda untuk mengonfirmasi bahwa sinkronisasi waktu berhasil.
            </p>

            <form action="{{ route('superadmin.2fa.confirm') }}" method="POST">
                @csrf
                <div style="display: flex; gap: 1rem; align-items: center; max-width: 400px; margin-bottom: 1.5rem;">
                    <input type="text" name="code" id="code" required inputmode="numeric" pattern="[0-9]*" maxlength="6" autocomplete="one-time-code" autofocus
                           placeholder="000000"
                           style="width: 180px; text-align: center; font-size: 1.5rem; letter-spacing: 0.25em; font-weight: 700; padding: 0.625rem 1rem; border: 2px solid #3B82F6; border-radius: 8px;">
                    <button type="submit" class="btn" style="flex: 1; background: #1E3A8A; color: #ffffff; padding: 0.75rem 1.25rem; border-radius: 8px; font-weight: 600; font-size: 0.875rem; border: none; cursor: pointer;">
                        Konfirmasi & Aktifkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
