<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Master Bidang - SuperAdmin MagKom</title>
</head>
<body>
    <h1>Master Bidang Dinas Kominfo</h1>
    <ul>
        @foreach ($bidangList as $bidang)
            <li>{{ $bidang->nama_bidang }} - {{ $bidang->aktif ? 'Aktif' : 'Nonaktif' }}</li>
        @endforeach
    </ul>
</body>
</html>
