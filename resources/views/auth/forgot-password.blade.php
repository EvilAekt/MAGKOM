@extends('layouts.auth-depth', ['variant' => 'forgot'])

@section('title', 'MagKom - Lupa Password')

@section('auth-form')
<section style="flex: 1 1 440px; min-height: 560px; position: relative; box-sizing: border-box; padding: 36px 40px 32px; display: flex; flex-direction: column; background: #FFFFFF">
  <div style="flex: 1; display: flex; flex-direction: column; justify-content: center; width: 100%; max-width: 440px; margin: 0 auto; box-sizing: border-box">

    <div style="text-align: center; margin-bottom: 26px">
      <h1 style="margin: 0 0 8px; font-size: 38px; line-height: 1.15; font-weight: 800; letter-spacing: -0.02em; color: #003566">Lupa Password?</h1>
      <p style="margin: 0; font-size: 15px; line-height: 1.5; color: #5F6B76">Masukkan alamat email yang terdaftar pada akun Anda. Kami akan mengirimkan tautan untuk mengatur ulang password.</p>
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

    <form action="{{ route('password.email') }}" method="POST" novalidate style="display: flex; flex-direction: column; gap: 20px">
      @csrf

      <div style="display: flex; flex-direction: column; gap: 8px">
        <label for="email" style="font-size: 14px; font-weight: 600; color: #212529">Email Akun Terdaftar <span aria-hidden="true" style="color: #C62828">*</span></label>
        <div style="position: relative">
          <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 16px; top: 16px"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="M3 7l9 6 9-6"></path></svg>
          <input id="email" name="email" class="fld" type="email" required autofocus autocomplete="email" placeholder="contoh: nama.peserta@gmail.com" value="{{ old('email') }}" style="width: 100%; box-sizing: border-box; height: 50px; padding: 0 16px 0 46px; border: 1px solid #AAAAAA; border-radius: 10px; font-family: inherit; font-size: 15px; color: #212529; background: #FFFFFF">
        </div>
      </div>

      <button type="submit" class="btn-main" style="width: 100%; height: 52px; border: none; border-bottom: 3px solid #FFC300; border-radius: 10px; background: #003566; color: #FFFFFF; font-family: inherit; font-size: 16px; font-weight: 700; cursor: pointer">Kirim Tautan Reset</button>
    </form>

    <div style="margin-top: 26px; text-align: center; display: flex; flex-direction: column; gap: 8px">
      <a href="{{ route('login') }}" style="font-size: 14.5px; font-weight: 700; color: #003566; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 6px">
        <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        <span>Kembali ke Login</span>
      </a>
      <p style="margin: 6px 0 0; font-size: 12.5px; color: #5F6B76">Magang Kominfo • Dinas Kominfo Sleman • v1.0</p>
    </div>

  </div>
</section>
@endsection
