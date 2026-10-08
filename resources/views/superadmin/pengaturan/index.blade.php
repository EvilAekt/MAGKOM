<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengaturan Sistem - SuperAdmin MagKom</title>
</head>
<body>
    <h1>Pengaturan Sistem</h1>
    <form action="{{ route('superadmin.pengaturan.update') }}" method="POST">
        @csrf
        @method('PUT')
        @foreach ($pengaturan as $kunci => $nilai)
            <div>
                <label>{{ $kunci }}</label>
                <input type="text" name="{{ $kunci }}" value="{{ $nilai }}">
            </div>
        @endforeach
        <button type="submit">Simpan</button>
    </form>
</body>
</html>
