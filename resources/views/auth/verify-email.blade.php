<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Alamat Email - MagKom</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #0f172a; color: #f8fafc; min-height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; padding: 20px; }
        .card { background: #1e293b; border: 1px solid #334155; border-radius: 16px; padding: 32px; width: 100%; max-width: 440px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); }
        h1 { font-size: 20px; font-weight: 700; margin: 0 0 8px; color: #38bdf8; }
        p { font-size: 14px; color: #94a3b8; line-height: 1.6; margin: 0 0 20px; }
        .btn { width: 100%; background: #0284c7; color: white; border: none; border-radius: 8px; padding: 12px; font-weight: 600; font-size: 14px; cursor: pointer; transition: background 0.2s; }
        .btn:hover { background: #0369a1; }
        .btn-link { background: none; border: none; color: #94a3b8; font-size: 13px; cursor: pointer; text-decoration: underline; margin-top: 16px; display: block; text-align: center; width: 100%; }
        .btn-link:hover { color: #f8fafc; }
        .alert-success { background: rgba(34, 197, 94, 0.15); border: 1px solid #22c55e; border-radius: 8px; padding: 12px; font-size: 13px; color: #86efac; margin-bottom: 16px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Verifikasi Email Anda</h1>
        <p>Terima kasih telah mendaftar di Portal MagKom. Kami telah mengirimkan tautan verifikasi ke alamat email Anda. Silakan klik tautan tersebut untuk mengaktifkan akun Anda.</p>

        @if (session('status') == 'verification-link-sent')
            <div class="alert-success">
                Tautan verifikasi baru telah dikirimkan ke alamat email Anda.
            </div>
        @endif

        <form action="{{ route('verification.send') }}" method="POST">
            @csrf
            <button type="submit" class="btn">Kirim Ulang Email Verifikasi</button>
        </form>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-link">Keluar dari Akun</button>
        </form>
    </div>
</body>
</html>
