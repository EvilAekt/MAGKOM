@extends('layouts.auth-depth', ['variant' => 'verify-email'])

@section('title', __('MagKom - Verifikasi Alamat Email'))

@section('auth-form')
<section style="flex: 1 1 440px; min-height: 560px; position: relative; box-sizing: border-box; padding: 36px 40px 32px; display: flex; flex-direction: column; background: #FFFFFF">
  <div style="flex: 1; display: flex; flex-direction: column; justify-content: center; width: 100%; max-width: 440px; margin: 0 auto; box-sizing: border-box">

    <!-- Header Verifikasi Email -->
    <div style="text-align: center; margin-bottom: 24px">
      <div style="display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; border-radius: 16px; background: #EEF3FB; color: #003566; margin-bottom: 16px">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
          <polyline points="22,6 12,13 2,6"></polyline>
        </svg>
      </div>
      <h1 style="margin: 0 0 8px; font-size: 34px; line-height: 1.15; font-weight: 800; letter-spacing: -0.02em; color: #003566">{{ __('Verifikasi Alamat Email') }}</h1>
      <p style="margin: 0; font-size: 15px; line-height: 1.5; color: #5F6B76">{{ __('Terima kasih telah mendaftar di Portal MagKom. Kami telah mengirimkan tautan aktivasi akun ke alamat email Anda. Silakan klik tautan tersebut untuk memverifikasi akun Anda.') }}</p>
    </div>

    @if (session('status') == 'verification-link-sent')
      <div style="margin-bottom: 20px; padding: 14px 16px; border-radius: 10px; background: #DCFCE7; border: 1px solid #86EFAC; color: #166534; font-size: 14px; line-height: 1.5; display: flex; align-items: flex-start; gap: 10px">
        <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#166534" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 2px"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        <span>{{ __('Tautan verifikasi baru telah berhasil dikirimkan ke alamat email yang Anda daftarkan.') }}</span>
      </div>
    @endif

    <!-- Kotak Saran & Bantuan -->
    <div style="background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 16px; margin-bottom: 24px; font-size: 13.5px; color: #475569; line-height: 1.5; display: flex; gap: 12px; align-items: flex-start">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#003566" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 2px">
        <circle cx="12" cy="12" r="10"></circle>
        <line x1="12" y1="16" x2="12" y2="12"></line>
        <line x1="12" y1="8" x2="12.01" y2="8"></line>
      </svg>
      <div>
        {!! __('Belum menerima email? Pastikan untuk memeriksa tab <strong>Spam</strong> atau <strong>Promosi</strong> di email Anda, atau klik tombol di bawah untuk meminta tautan baru.') !!}
      </div>
    </div>

    <!-- Tombol Kirim Ulang Verifikasi -->
    <form action="{{ route('verification.send') }}" method="POST" style="margin-bottom: 12px">
      @csrf
      <button type="submit" class="btn-main" style="width: 100%; height: 52px; border: none; border-bottom: 3px solid #FFC300; border-radius: 10px; background: #003566; color: #FFFFFF; font-family: inherit; font-size: 16px; font-weight: 700; cursor: pointer">
        {{ __('Kirim Ulang Email Verifikasi') }}
      </button>
    </form>

    <!-- Opsi Keluar Akun -->
    <div style="margin-top: 20px; text-align: center; display: flex; flex-direction: column; gap: 8px">
      <form action="{{ route('logout') }}" method="POST" style="margin: 0">
        @csrf
        <button type="submit" style="background: none; border: none; padding: 0; font-size: 14px; font-weight: 600; color: #5F6B76; cursor: pointer; text-decoration: underline">
          {{ __('Keluar dari Akun') }}
        </button>
      </form>
      <p style="margin: 6px 0 0; font-size: 12.5px; color: #5F6B76">Magang Kominfo • Dinas Kominfo Sleman • v1.0</p>
    </div>

  </div>
</section>
@endsection
