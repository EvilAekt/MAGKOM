@extends('layouts.auth-depth', ['variant' => 'login'])

@section('title', 'MagKom - Login')

@section('auth-form')
<section style="flex: 1 1 440px; min-height: 560px; position: relative; box-sizing: border-box; padding: 36px 40px 32px; display: flex; flex-direction: column; background: #FFFFFF">
  <div style="flex: 1; display: flex; flex-direction: column; justify-content: center; width: 100%; max-width: 440px; margin: 0 auto; box-sizing: border-box">
    
    <div style="text-align: center; margin-bottom: 26px">
      <h1 style="margin: 0 0 8px; font-size: 38px; line-height: 1.15; font-weight: 800; letter-spacing: -0.02em; color: #003566">Selamat datang</h1>
      <p style="margin: 0; font-size: 15px; line-height: 1.5; color: #5F6B76">Masuk untuk mengakses sistem magang terpadu Kominfo.</p>
    </div>

    @if(session('lockout_seconds') || session('error_lockout'))
      <div id="lockoutBanner" data-lockout-seconds="{{ session('lockout_seconds', 60) }}" style="margin-bottom: 20px; padding: 16px; border-radius: 12px; background: #FEF2F2; border: 1.5px solid #F87171; color: #991B1B; display: flex; flex-direction: column; gap: 8px">
        <div style="display: flex; align-items: center; gap: 8px; font-weight: 800; font-size: 15px">
          <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
          <span>Akses Akun Terkunci Sementara</span>
        </div>
        <p style="margin: 0; font-size: 13.5px; line-height: 1.5; color: #7F1D1D">
          Terlalu banyak percobaan masuk yang gagal. Demi keamanan akun, silakan tunggu <strong id="lockoutTimerDisplay" style="color: #991B1B; font-size: 14px">{{ session('lockout_seconds', 60) }}</strong> detik sebelum mencoba kembali.
        </p>
        <div style="margin-top: 4px; padding-top: 8px; border-top: 1px dashed #FCA5A5; display: flex; align-items: center; justify-content: space-between">
          <span style="font-size: 12.5px; color: #7F1D1D">Lupa kata sandi akun Anda?</span>
          <a href="{{ route('password.request') }}" style="font-size: 13px; font-weight: 700; color: #991B1B; text-decoration: underline">Pulihkan Akun →</a>
        </div>
      </div>
    @elseif($errors->any())
      <div style="margin-bottom: 20px; padding: 12px 16px; border-radius: 10px; background: #FEE2E2; border: 1px solid #FCA5A5; color: #991B1B; font-size: 13.5px; line-height: 1.5">
        {{ $errors->first() }}
      </div>
    @endif

    <form action="{{ route('login') }}" method="POST" novalidate style="display: flex; flex-direction: column; gap: 16px">
      @csrf
      <div style="display: flex; flex-direction: column; gap: 8px">
        <label for="email" style="font-size: 14px; font-weight: 600; color: #212529">Email</label>
        <div style="position: relative">
          <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 16px; top: 16px"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="M3 7l9 6 9-6"></path></svg>
          <input id="email" name="email" class="fld" type="email" autocomplete="email" required autofocus value="{{ old('email') }}" placeholder="Email" style="width: 100%; box-sizing: border-box; height: 50px; padding: 0 16px 0 46px; border: 1px solid #AAAAAA; border-radius: 10px; font-family: inherit; font-size: 15px; color: #212529; background: #FFFFFF">
        </div>
      </div>

      <div style="display: flex; flex-direction: column; gap: 8px">
        <label for="sandi" style="font-size: 14px; font-weight: 600; color: #212529">Kata sandi</label>
        <div style="position: relative">
          <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 16px; top: 16px"><rect x="5" y="11" width="14" height="9" rx="2"></rect><path d="M8 11V8a4 4 0 0 1 8 0v3"></path></svg>
          <input id="sandi" name="password" class="fld" type="password" autocomplete="current-password" required placeholder="Password" style="width: 100%; box-sizing: border-box; height: 50px; padding: 0 54px 0 46px; border: 1px solid #AAAAAA; border-radius: 10px; font-family: inherit; font-size: 15px; color: #212529; background: #FFFFFF">
          <button type="button" class="btn-ico" data-action="toggle-password" data-target="sandi" aria-label="Tampilkan kata sandi" style="position: absolute; right: 3px; top: 3px; width: 44px; height: 44px; border: none; border-radius: 8px; background: transparent; cursor: pointer; display: flex; align-items: center; justify-content: center">
            <svg class="pw-icon-show" aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"></path><circle cx="12" cy="12" r="3"></circle></svg>
            <svg class="pw-icon-hide" aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"></path><circle cx="12" cy="12" r="3"></circle><path d="M4 4l16 16"></path></svg>
          </button>
        </div>
      </div>

      <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap">
        <div style="display: flex; align-items: center; gap: 8px">
          <input id="ingat" name="remember" type="checkbox" style="width: 18px; height: 18px; margin: 0; accent-color: #003566">
          <label for="ingat" style="font-size: 14px; color: #5F6B76">Ingat saya</label>
        </div>
        <a href="{{ route('password.request') }}" style="font-size: 14px; font-weight: 700; color: #003566; text-decoration: none">Lupa kata sandi?</a>
      </div>

      <div style="display: flex; flex-direction: column; gap: 8px">
        <label for="kode" style="font-size: 14px; font-weight: 600; color: #212529">Kode verifikasi keamanan <span aria-hidden="true" style="color: #C62828">*</span></label>
        <div style="display: flex; gap: 10px; align-items: center">
          <div style="width: 150px; height: 46px; box-sizing: border-box; border-radius: 10px; overflow: hidden; display: flex; align-items: center; justify-content: center; background: #F8FAFC; border: 1px solid #CBD5E1">
            <img data-captcha-img src="{{ route('captcha.image') }}" alt="Kode Verifikasi Keamanan" style="width: 150px; height: 46px; display: block; object-fit: cover; user-select: none; cursor: pointer" width="150" height="46" title="Klik untuk memuat ulang kode">
          </div>
          <button type="button" class="btn-ico" data-action="refresh-captcha" aria-label="Ganti kode verifikasi" style="width: 46px; height: 46px; border: 1px solid #AAAAAA; border-radius: 10px; background: #FFFFFF; cursor: pointer; display: flex; align-items: center; justify-content: center">
            <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#003566" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 11a8 8 0 0 0-14.5-4M4 4v4h4"></path><path d="M4 13a8 8 0 0 0 14.5 4M20 20v-4h-4"></path></svg>
          </button>
        </div>
        <div style="position: relative">
          <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 16px; top: 16px"><rect x="5" y="11" width="14" height="9" rx="2"></rect><path d="M8 11V8a4 4 0 0 1 8 0v3"></path></svg>
          <input id="kode" name="captcha" class="fld" type="text" maxlength="4" autocomplete="off" placeholder="4 karakter kode di atas" style="width: 100%; box-sizing: border-box; height: 50px; padding: 0 16px 0 46px; border: 1px solid #AAAAAA; border-radius: 10px; font-family: inherit; font-size: 15px; color: #212529; background: #FFFFFF">
        </div>
      </div>

      <button type="submit" class="btn-main" style="width: 100%; height: 52px; border: none; border-bottom: 3px solid #FFC300; border-radius: 10px; background: #003566; color: #FFFFFF; font-family: inherit; font-size: 16px; font-weight: 700; cursor: pointer">Masuk</button>

      <div style="display: flex; align-items: center; gap: 14px">
        <div style="flex: 1; height: 1px; background: #BBBBBB"></div>
        <span style="font-size: 13px; color: #5F6B76">atau</span>
        <div style="flex: 1; height: 1px; background: #BBBBBB"></div>
      </div>

      <a href="{{ route('oauth.redirect', 'google') }}" class="btn-alt" style="width: 100%; height: 50px; box-sizing: border-box; text-decoration: none; border: 1px solid #AAAAAA; border-radius: 10px; background: #FFFFFF; color: #212529; font-family: inherit; font-size: 15px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px">
        <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24">
          <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"></path>
          <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"></path>
          <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"></path>
          <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"></path>
        </svg>
        <span>Masuk dengan Google</span>
      </a>
    </form>

    <p style="margin: 22px 0 0; text-align: center; font-size: 15px; color: #5F6B76">Belum punya akun magang? <a href="{{ route('register') }}" style="font-weight: 700; color: #003566; text-decoration: none">Daftar</a></p>
    <div style="margin-top: 10px; display: flex; justify-content: center; gap: 18px; flex-wrap: wrap; font-size: 13px"><br>
      <button type="button" data-action="open-modal-privasi" style="background: none; border: none; padding: 0; color: rgb(95, 107, 118); text-decoration: underline; font-family: inherit; font-size: 13px; cursor: pointer">Kebijakan Privasi &amp; Syarat Ketentuan</button>
    </div>

  </div>
</section>

@include('auth.partials._modal_privasi_syarat')
@endsection
