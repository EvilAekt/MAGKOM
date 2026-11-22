@extends('layouts.auth-depth', ['variant' => 'two-factor'])

@section('title', __('MagKom - Verifikasi Dua Faktor (2FA)'))

@section('auth-form')
<section style="flex: 1 1 440px; min-height: 560px; position: relative; box-sizing: border-box; padding: 36px 40px 32px; display: flex; flex-direction: column; background: #FFFFFF">
  <div style="flex: 1; display: flex; flex-direction: column; justify-content: center; width: 100%; max-width: 440px; margin: 0 auto; box-sizing: border-box">

    <!-- Header 2FA -->
    <div style="text-align: center; margin-bottom: 24px">
      <div style="display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; border-radius: 16px; background: #EEF3FB; color: #003566; margin-bottom: 16px">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
          <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
        </svg>
      </div>
      <h1 style="margin: 0 0 8px; font-size: 34px; line-height: 1.15; font-weight: 800; letter-spacing: -0.02em; color: #003566">{{ __('Otentikasi Dua Faktor') }}</h1>
      <p style="margin: 0; font-size: 15px; line-height: 1.5; color: #5F6B76">{{ __('Masukkan kode 6-digit dari aplikasi Google Authenticator atau gunakan kode pemulihan darurat Anda.') }}</p>
    </div>

    @if($errors->any())
      <div style="margin-bottom: 20px; padding: 14px 16px; border-radius: 10px; background: #FEE2E2; border: 1px solid #FCA5A5; color: #991B1B; font-size: 14px; line-height: 1.5; display: flex; align-items: flex-start; gap: 10px">
        <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#991B1B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 2px"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        <span>{{ $errors->first() }}</span>
      </div>
    @endif

    <!-- Form Verifikasi 2FA -->
    <form action="{{ route('two-factor.challenge.verify') }}" method="POST" id="twoFactorForm" style="display: flex; flex-direction: column; gap: 18px">
      @csrf

      <div id="totpSection" style="display: flex; flex-direction: column; gap: 8px">
        <label for="code" style="font-size: 14px; font-weight: 600; color: #212529">{{ __('Kode Autentikasi (6 Digit)') }} <span aria-hidden="true" style="color: #C62828">*</span></label>
        <div style="position: relative">
          <input 
            type="text" 
            name="code" 
            id="code" 
            class="fld otp-code-input" 
            inputmode="numeric" 
            pattern="[0-9]*" 
            maxlength="6" 
            autocomplete="one-time-code" 
            autofocus
            placeholder="Contoh: 123456"
            style="width: 100%; box-sizing: border-box; height: 50px; text-align: center; font-size: 20px; letter-spacing: 0.25em; font-weight: 700; font-family: monospace; padding: 0 16px; border: 1.5px solid #CBD5E1; border-radius: 10px; color: #003566; background: #FFFFFF"
          >
        </div>
      </div>

      <!-- Kode Pemulihan Darurat Accordion -->
      <details style="border: 1px dashed #CBD5E1; border-radius: 10px; padding: 12px 16px; background: #F8FAFC">
        <summary style="cursor: pointer; color: #003566; font-size: 13.5px; font-weight: 700; user-select: none">
          {{ __('Kehilangan perangkat? Gunakan Kode Pemulihan') }}
        </summary>
        <div style="margin-top: 12px; display: flex; flex-direction: column; gap: 8px">
          <label for="recovery_code" style="font-size: 13px; font-weight: 600; color: #475569">{{ __('Kode Pemulihan (Recovery Code)') }}</label>
          <input 
            type="text" 
            name="recovery_code" 
            id="recovery_code" 
            class="fld" 
            autocomplete="off"
            placeholder="Contoh: ABCD1234EF"
            style="width: 100%; box-sizing: border-box; height: 46px; text-align: center; font-family: monospace; font-size: 15px; font-weight: 600; padding: 0 14px; border: 1px solid #CBD5E1; border-radius: 8px; color: #212529; background: #FFFFFF"
          >
          <p style="margin: 4px 0 0; font-size: 12px; color: #64748B; line-height: 1.4">{{ __('Kosongkan kolom kode 6 digit di atas jika Anda menggunakan kode pemulihan.') }}</p>
        </div>
      </details>

      <button type="submit" class="btn-main" style="width: 100%; height: 52px; border: none; border-bottom: 3px solid #FFC300; border-radius: 10px; background: #003566; color: #FFFFFF; font-family: inherit; font-size: 16px; font-weight: 700; cursor: pointer; margin-top: 4px">
        {{ __('Verifikasi & Masuk') }}
      </button>
    </form>

    <div style="margin-top: 24px; text-align: center; display: flex; flex-direction: column; gap: 8px">
      <a href="{{ route('login') }}" style="font-size: 14.5px; font-weight: 700; color: #003566; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 6px">
        <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        <span>{{ __('Kembali ke Login') }}</span>
      </a>
      <p style="margin: 6px 0 0; font-size: 12.5px; color: #5F6B76">Magang Kominfo • Dinas Kominfo Sleman • v1.0</p>
    </div>

  </div>
</section>
@endsection
