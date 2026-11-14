@extends('layouts.auth-depth', ['variant' => 'reset'])

@section('title', 'MagKom - Buat Password Baru')

@section('auth-form')
<section style="flex: 1 1 440px; min-height: 560px; position: relative; box-sizing: border-box; padding: 36px 40px 32px; display: flex; flex-direction: column; background: #FFFFFF">
  <div style="flex: 1; display: flex; flex-direction: column; justify-content: center; width: 100%; max-width: 440px; margin: 0 auto; box-sizing: border-box">

    <div style="text-align: center; margin-bottom: 24px">
      <h1 style="margin: 0 0 8px; font-size: 36px; line-height: 1.15; font-weight: 800; letter-spacing: -0.02em; color: #003566">Buat Password Baru</h1>
      <p style="margin: 0; font-size: 15px; line-height: 1.5; color: #5F6B76">Silakan tentukan kata sandi baru untuk mengamankan akun magang Anda.</p>
    </div>

    @if($errors->any())
      <div style="margin-bottom: 20px; padding: 14px 16px; border-radius: 10px; background: #FEE2E2; border: 1px solid #FCA5A5; color: #991B1B; font-size: 14px; line-height: 1.5; display: flex; align-items: flex-start; gap: 10px">
        <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#991B1B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 2px"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        <span>{{ $errors->first() }}</span>
      </div>
    @endif

    <form action="{{ route('password.update') }}" method="POST" novalidate style="display: flex; flex-direction: column; gap: 18px">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">
      <input type="hidden" name="email" value="{{ old('email', $email) }}">

      <!-- Field Email (Read-Only Info) -->
      <div style="display: flex; flex-direction: column; gap: 8px">
        <label style="font-size: 14px; font-weight: 600; color: #212529">Email Akun Mahasiswa</label>
        <div style="position: relative">
          <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 16px; top: 16px"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="M3 7l9 6 9-6"></path></svg>
          <input type="text" readonly disabled value="{{ old('email', $email) }}" style="width: 100%; box-sizing: border-box; height: 50px; padding: 0 16px 0 46px; border: 1px solid #D1D5DB; border-radius: 10px; font-family: inherit; font-size: 15px; color: #4B5563; background: #F3F4F6; cursor: not-allowed">
        </div>
      </div>

      <!-- Field Password Baru -->
      <div style="display: flex; flex-direction: column; gap: 8px">
        <label for="password" style="font-size: 14px; font-weight: 600; color: #212529">Password Baru <span aria-hidden="true" style="color: #C62828">*</span></label>
        <div style="position: relative">
          <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 16px; top: 16px"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
          <input id="password" name="password" class="fld" type="password" required autofocus autocomplete="new-password" placeholder="Minimal 8 karakter kombinasi" style="width: 100%; box-sizing: border-box; height: 50px; padding: 0 48px 0 46px; border: 1px solid #AAAAAA; border-radius: 10px; font-family: inherit; font-size: 15px; color: #212529; background: #FFFFFF">
          <button type="button" class="btn-ico" data-action="toggle-password" data-target="password" aria-label="Tampilkan kata sandi" style="position: absolute; right: 10px; top: 10px; width: 30px; height: 30px; border: none; background: transparent; cursor: pointer; display: flex; align-items: center; justify-content: center">
            <svg class="pw-icon-show" aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
            <svg class="pw-icon-hide" aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
          </button>
        </div>
        <span style="font-size: 12.5px; color: #5F6B76; line-height: 1.4">Minimal 8 karakter dengan kombinasi huruf dan angka.</span>
      </div>

      <!-- Field Konfirmasi Password Baru -->
      <div style="display: flex; flex-direction: column; gap: 8px">
        <label for="password_confirmation" style="font-size: 14px; font-weight: 600; color: #212529">Konfirmasi Password Baru <span aria-hidden="true" style="color: #C62828">*</span></label>
        <div style="position: relative">
          <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 16px; top: 16px"><polyline points="20 6 9 17 4 12"></polyline></svg>
          <input id="password_confirmation" name="password_confirmation" class="fld" type="password" required autocomplete="new-password" placeholder="Ulangi kata sandi baru" style="width: 100%; box-sizing: border-box; height: 50px; padding: 0 48px 0 46px; border: 1px solid #AAAAAA; border-radius: 10px; font-family: inherit; font-size: 15px; color: #212529; background: #FFFFFF">
          <button type="button" class="btn-ico" data-action="toggle-password" data-target="password_confirmation" aria-label="Tampilkan konfirmasi kata sandi" style="position: absolute; right: 10px; top: 10px; width: 30px; height: 30px; border: none; background: transparent; cursor: pointer; display: flex; align-items: center; justify-content: center">
            <svg class="pw-icon-show" aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
            <svg class="pw-icon-hide" aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
          </button>
        </div>
      </div>

      <button type="submit" class="btn-main" style="width: 100%; height: 52px; border: none; border-bottom: 3px solid #FFC300; border-radius: 10px; background: #003566; color: #FFFFFF; font-family: inherit; font-size: 16px; font-weight: 700; cursor: pointer; margin-top: 6px">Simpan Password Baru</button>
    </form>

    <div style="margin-top: 24px; text-align: center; display: flex; flex-direction: column; gap: 8px">
      <a href="{{ route('login') }}" style="font-size: 14.5px; font-weight: 700; color: #003566; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 6px">
        <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        <span>Kembali ke Login</span>
      </a>
      <p style="margin: 6px 0 0; font-size: 12.5px; color: #5F6B76">Magang Kominfo • Dinas Kominfo Sleman • v1.0</p>
    </div>

  </div>
</section>
@endsection
