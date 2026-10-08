<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi - MagKom</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #0f172a; color: #f8fafc; min-height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; padding: 20px; }
        .card { background: #1e293b; border: 1px solid #334155; border-radius: 16px; padding: 32px; width: 100%; max-width: 440px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); }
        h1 { font-size: 20px; font-weight: 700; margin: 0 0 8px; color: #38bdf8; }
        p { font-size: 14px; color: #94a3b8; line-height: 1.6; margin: 0 0 20px; }
        .form-group { margin-bottom: 16px; }
        label { display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px; color: #cbd5e1; }
        input { width: 100%; box-sizing: border-box; background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 10px 14px; color: #fff; font-size: 14px; }
        input:focus { outline: none; border-color: #38bdf8; }
        .btn { width: 100%; background: #0284c7; color: white; border: none; border-radius: 8px; padding: 12px; font-weight: 600; font-size: 14px; cursor: pointer; transition: background 0.2s; }
        .btn:hover { background: #0369a1; }
        .alert-status { background: rgba(34, 197, 94, 0.15); border: 1px solid #22c55e; border-radius: 8px; padding: 12px; font-size: 13px; color: #86efac; margin-bottom: 16px; }
        .back-link { display: block; text-align: center; margin-top: 16px; color: #94a3b8; font-size: 13px; text-decoration: none; }
        .back-link:hover { color: #38bdf8; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Pemulihan Kata Sandi</h1>
        <p>Masukkan alamat email Anda yang terdaftar sebagai peserta magang untuk menerima tautan pemulihan kata sandi.</p>

        @if (session('status'))
            <div class="alert-status">
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email">Alamat Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
            </div>
            <button type="submit" class="btn">Kirim Tautan Pemulihan</button>
        </form>

        <a href="{{ route('login') }}" class="back-link">&larr; Kembali ke halaman Masuk</a>
    </div>
</body>
</html>
