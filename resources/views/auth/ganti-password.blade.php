<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Kata Sandi - MagKom</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #0f172a; color: #f8fafc; min-height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; padding: 20px; }
        .card { background: #1e293b; border: 1px solid #334155; border-radius: 16px; padding: 32px; width: 100%; max-width: 440px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); }
        h1 { font-size: 20px; font-weight: 700; margin: 0 0 8px; color: #38bdf8; }
        p { font-size: 14px; color: #94a3b8; margin: 0 0 24px; }
        .form-group { margin-bottom: 16px; }
        label { display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px; color: #cbd5e1; }
        input { width: 100%; box-sizing: border-box; background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 10px 14px; color: #fff; font-size: 14px; }
        input:focus { outline: none; border-color: #38bdf8; }
        .btn { width: 100%; background: #0284c7; color: white; border: none; border-radius: 8px; padding: 12px; font-weight: 600; font-size: 14px; cursor: pointer; transition: background 0.2s; margin-top: 8px; }
        .btn:hover { background: #0369a1; }
        .alert-error { background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; border-radius: 8px; padding: 12px; font-size: 13px; color: #fca5a5; margin-bottom: 16px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Ganti Kata Sandi Wajib</h1>
        <p>Demi standar keamanan instansi, Anda wajib mengganti kata sandi awal sebelum dapat mengakses fitur portal.</p>

        @if ($errors->any())
            <div class="alert-error">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('password.ganti.update') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="current_password">Kata Sandi Saat Ini</label>
                <input type="password" id="current_password" name="current_password" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Kata Sandi Baru (Min. 8 Karakter, Kombinasi Huruf & Angka)</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="password_confirmation">Konfirmasi Kata Sandi Baru</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
            </div>
            <button type="submit" class="btn">Perbarui Kata Sandi & Lanjutkan</button>
        </form>
    </div>
</body>
</html>
