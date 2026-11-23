@extends('layouts.auth-depth', ['variant' => 'ganti-password'])

@section('title', __('MagKom - Ganti Kata Sandi'))

@section('auth-form')
<section style="flex: 1 1 440px; min-height: 560px; position: relative; box-sizing: border-box; padding: 36px 40px 32px; display: flex; flex-direction: column; background: #FFFFFF">
  <div style="flex: 1; display: flex; flex-direction: column; justify-content: center; width: 100%; max-width: 440px; margin: 0 auto; box-sizing: border-box">

    <!-- Header Ganti Kata Sandi -->
    <div style="text-align: center; margin-bottom: 24px">
      <h1 style="margin: 0 0 8px; font-size: 36px; line-height: 1.15; font-weight: 800; letter-spacing: -0.02em; color: #003566">{{ __('Ganti Kata Sandi') }}</h1>
      <p style="margin: 0; font-size: 15px; line-height: 1.5; color: #5F6B76">{{ __('Demi standar keamanan instansi, Anda wajib memperbarui kata sandi awal sebelum dapat mengakses fitur portal.') }}</p>
    </div>

    @if(session('warning'))
      <div style="margin-bottom: 20px; padding: 12px 16px; border-radius: 10px; background: #FEF3C7; border: 1px solid #FCD34D; color: #92400E; font-size: 13.5px; line-height: 1.5; display: flex; align-items: flex-start; gap: 8px">
        <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 1px"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        <span>{{ session('warning') }}</span>
      </div>
    @endif

    @if($errors->any())
      <div style="margin-bottom: 20px; padding: 14px 16px; border-radius: 10px; background: #FEE2E2; border: 1px solid #FCA5A5; color: #991B1B; font-size: 14px; line-height: 1.5; display: flex; align-items: flex-start; gap: 10px">
        <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#991B1B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 2px"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        <span>{{ $errors->first() }}</span>
      </div>
    @endif

    <!-- Form Ganti Kata Sandi -->
    <form action="{{ route('password.ganti.update') }}" method="POST" novalidate style="display: flex; flex-direction: column; gap: 18px">
      @csrf

      <!-- Field Kata Sandi Saat Ini -->
      <div style="display: flex; flex-direction: column; gap: 8px">
        <label for="current_password" style="font-size: 14px; font-weight: 600; color: #212529">{{ __('Kata Sandi Saat Ini') }} <span aria-hidden="true" style="color: #C62828">*</span></label>
        <div style="position: relative">
          <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 16px; top: 16px">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
          </svg>
          <input 
            id="current_password" 
            name="current_password" 
            class="fld" 
            type="password" 
            required 
            autofocus 
            autocomplete="current-password" 
            placeholder="Masukkan kata sandi saat ini" 
            style="width: 100%; box-sizing: border-box; height: 50px; padding: 0 54px 0 46px; border: 1px solid #AAAAAA; border-radius: 10px; font-family: inherit; font-size: 15px; color: #212529; background: #FFFFFF"
          >
          <button type="button" class="btn-ico" data-action="toggle-password" data-target="current_password" aria-label="Tampilkan kata sandi" style="position: absolute; right: 3px; top: 3px; width: 44px; height: 44px; border: none; border-radius: 8px; background: transparent; cursor: pointer; display: flex; align-items: center; justify-content: center">
            <svg class="pw-icon-show" aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"></path><circle cx="12" cy="12" r="3"></circle></svg>
            <svg class="pw-icon-hide" aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="display: none"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"></path><circle cx="12" cy="12" r="3"></circle><path d="M4 4l16 16"></path></svg>
          </button>
        </div>
      </div>

      <!-- Field Kata Sandi Baru -->
      <div style="display: flex; flex-direction: column; gap: 8px">
        <label for="password" style="font-size: 14px; font-weight: 600; color: #212529">{{ __('Kata Sandi Baru') }} <span aria-hidden="true" style="color: #C62828">*</span></label>
        <div style="position: relative">
          <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 16px; top: 16px">
            <path d="M21 2l-2 2m-1.5 1.5L14 9l-1.5-1.5-3 3 1.5 1.5-5 5H2v-4l5-5 1.5 1.5 3-3L12 5.5 13.5 4 15.5 2z"></path>
          </svg>
          <input 
            id="password" 
            name="password" 
            class="fld" 
            type="password" 
            required 
            autocomplete="new-password" 
            placeholder="Min. 8 karakter (huruf & angka)" 
            style="width: 100%; box-sizing: border-box; height: 50px; padding: 0 54px 0 46px; border: 1px solid #AAAAAA; border-radius: 10px; font-family: inherit; font-size: 15px; color: #212529; background: #FFFFFF"
          >
          <button type="button" class="btn-ico" data-action="toggle-password" data-target="password" aria-label="Tampilkan kata sandi" style="position: absolute; right: 3px; top: 3px; width: 44px; height: 44px; border: none; border-radius: 8px; background: transparent; cursor: pointer; display: flex; align-items: center; justify-content: center">
            <svg class="pw-icon-show" aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"></path><circle cx="12" cy="12" r="3"></circle></svg>
            <svg class="pw-icon-hide" aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="display: none"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"></path><circle cx="12" cy="12" r="3"></circle><path d="M4 4l16 16"></path></svg>
          </button>
        </div>
        <div class="pw-strength-wrapper" data-target-input="password"></div>
        <span style="font-size: 12.5px; color: #5F6B76; line-height: 1.4">{{ __('Minimal 8 karakter dengan kombinasi huruf dan angka.') }}</span>
      </div>

      <!-- Field Konfirmasi Kata Sandi Baru -->
      <div style="display: flex; flex-direction: column; gap: 8px">
        <label for="password_confirmation" style="font-size: 14px; font-weight: 600; color: #212529">Konfirmasi {{ __('Kata Sandi Baru') }} <span aria-hidden="true" style="color: #C62828">*</span></label>
        <div style="position: relative">
          <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 16px; top: 16px">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
          <input 
            id="password_confirmation" 
            name="password_confirmation" 
            class="fld" 
            type="password" 
            required 
            autocomplete="new-password" 
            placeholder="Ulangi kata sandi baru" 
            style="width: 100%; box-sizing: border-box; height: 50px; padding: 0 54px 0 46px; border: 1px solid #AAAAAA; border-radius: 10px; font-family: inherit; font-size: 15px; color: #212529; background: #FFFFFF"
          >
          <button type="button" class="btn-ico" data-action="toggle-password" data-target="password_confirmation" aria-label="Tampilkan konfirmasi kata sandi" style="position: absolute; right: 3px; top: 3px; width: 44px; height: 44px; border: none; border-radius: 8px; background: transparent; cursor: pointer; display: flex; align-items: center; justify-content: center">
            <svg class="pw-icon-show" aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"></path><circle cx="12" cy="12" r="3"></circle></svg>
            <svg class="pw-icon-hide" aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="display: none"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"></path><circle cx="12" cy="12" r="3"></circle><path d="M4 4l16 16"></path></svg>
          </button>
        </div>
      </div>

      <!-- Tombol Aksi Submit -->
      <button type="submit" class="btn-main" style="width: 100%; height: 52px; border: none; border-bottom: 3px solid #FFC300; border-radius: 10px; background: #003566; color: #FFFFFF; font-family: inherit; font-size: 16px; font-weight: 700; cursor: pointer; margin-top: 6px">
        {{ __('Perbarui Kata Sandi & Lanjutkan') }}
      </button>
    </form>

    <!-- Opsi Pembatalan & Logout -->
    <div style="margin-top: 24px; text-align: center; display: flex; flex-direction: column; gap: 8px">
      <form action="{{ route('logout') }}" method="POST" style="margin: 0">
        @csrf
        <button type="submit" style="background: none; border: none; padding: 0; font-size: 14px; font-weight: 600; color: #5F6B76; cursor: pointer; text-decoration: underline">
          {{ __('Batal dan Keluar dari Akun') }}
        </button>
      </form>
      <p style="margin: 6px 0 0; font-size: 12.5px; color: #5F6B76">Magang Kominfo • Dinas Kominfo Sleman • v1.0</p>
    </div>

  </div>
</section>
@endsection
