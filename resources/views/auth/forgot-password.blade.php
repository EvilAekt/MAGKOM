@extends('layouts.auth-depth', ['variant' => 'forgot'])

@section('title', 'MagKom - Lupa Kata Sandi')

@section('auth-form')
<section style="flex: 1 1 440px; min-height: 560px; position: relative; box-sizing: border-box; padding: 36px 40px 32px; display: flex; flex-direction: column; background: #FFFFFF">
  <div style="flex: 1; display: flex; flex-direction: column; justify-content: center; width: 100%; max-width: 440px; margin: 0 auto; box-sizing: border-box">

    <div style="text-align: center; margin-bottom: 26px">
      <h1 style="margin: 0 0 8px; font-size: 38px; line-height: 1.15; font-weight: 800; letter-spacing: -0.02em; color: #003566">Lupa kata sandi?</h1>
      <p style="margin: 0; font-size: 15px; line-height: 1.5; color: #5F6B76">Masukkan alamat email yang terdaftar untuk menerima tautan pemulihan kata sandi.</p>
    </div>

    @if(session('status'))
      <div style="margin-bottom: 20px; padding: 14px 16px; border-radius: 10px; background: #DCFCE7; border: 1px solid #86EFAC; color: #166534; font-size: 14px; line-height: 1.5; display: flex; align-items: flex-start; gap: 10px">
        <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#166534" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 2px"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        <span>{{ session('status') }}</span>
      </div>
    @endif

    @if($errors->any())
      <div style="margin-bottom: 20px; padding: 14px 16px; border-radius: 10px; background: #FEE2E2; border: 1px solid #FCA5A5; color: #991B1B; font-size: 14px; line-height: 1.5; display: flex; align-items: flex-start; gap: 10px">
        <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#991B1B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 2px"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        <span>{{ $errors->first() }}</span>
      </div>
    @endif

    <form action="{{ route('password.email') }}" method="POST" novalidate style="display: flex; flex-direction: column; gap: 18px">
      @csrf

      <div style="display: flex; flex-direction: column; gap: 8px">
        <label for="email" style="font-size: 14px; font-weight: 600; color: #212529">Email Akun Terdaftar</label>
        <div style="position: relative">
          <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 16px; top: 16px"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="M3 7l9 6 9-6"></path></svg>
          <input id="email" name="email" class="fld" type="email" required autofocus autocomplete="email" placeholder="contoh: nama.peserta@gmail.com" value="{{ old('email') }}" style="width: 100%; box-sizing: border-box; height: 50px; padding: 0 16px 0 46px; border: 1px solid #AAAAAA; border-radius: 10px; font-family: inherit; font-size: 15px; color: #212529; background: #FFFFFF">
        </div>
      </div>

      <div style="display: flex; flex-direction: column; gap: 8px">
        <label for="kode" style="font-size: 14px; font-weight: 600; color: #212529">Kode verifikasi keamanan <span aria-hidden="true" style="color: #C62828">*</span></label>
        <div style="display: flex; gap: 10px; align-items: center">
          <div data-captcha-display style="width: 150px; height: 46px; box-sizing: border-box; border-radius: 10px; background: #E6ECF7; color: #003566; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 800; letter-spacing: 0.32em; padding-left: 0.32em; user-select: none">DE4F</div>
          <button type="button" class="btn-ico" data-action="refresh-captcha" aria-label="Ganti kode verifikasi" style="width: 46px; height: 46px; border: 1px solid #AAAAAA; border-radius: 10px; background: #FFFFFF; cursor: pointer; display: flex; align-items: center; justify-content: center">
            <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#003566" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 11a8 8 0 0 0-14.5-4M4 4v4h4"></path><path d="M4 13a8 8 0 0 0 14.5 4M20 20v-4h-4"></path></svg>
          </button>
        </div>
        <div style="position: relative">
          <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 16px; top: 16px"><rect x="5" y="11" width="14" height="9" rx="2"></rect><path d="M8 11V8a4 4 0 0 1 8 0v3"></path></svg>
          <input id="kode" class="fld" type="text" maxlength="4" autocomplete="off" placeholder="4 karakter kode di atas" style="width: 100%; box-sizing: border-box; height: 50px; padding: 0 16px 0 46px; border: 1px solid #AAAAAA; border-radius: 10px; font-family: inherit; font-size: 15px; color: #212529; background: #FFFFFF">
        </div>
      </div>

      <button type="submit" class="btn-main" style="width: 100%; height: 52px; border: none; border-bottom: 3px solid #FFC300; border-radius: 10px; background: #003566; color: #FFFFFF; font-family: inherit; font-size: 16px; font-weight: 700; cursor: pointer">Kirim Tautan Pemulihan</button>
    </form>

    <p style="margin: 24px 0 0; text-align: center; font-size: 15px; color: #5F6B76">Ingat kata sandi Anda? <a href="{{ route('login') }}" style="font-weight: 700; color: #003566; text-decoration: none">Masuk kembali</a></p>
    <p style="margin: 10px 0 0; text-align: center; font-size: 12.5px; color: #5F6B76">Magang Kominfo • Dinas Kominfo Sleman • v1.0</p>

  </div>
</section>
@endsection
