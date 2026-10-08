<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Master Jurusan - SuperAdmin MagKom</title>
</head>
<body>
    <h1>Master Data Jurusan</h1>
    <ul>
        @foreach ($jurusanList as $jurusan)
            <li>{{ $jurusan->nama_jurusan }} ({{ $jurusan->kategori }}) - {{ $jurusan->aktif ? 'Aktif' : 'Nonaktif' }}</li>
        @endforeach
    </ul>
</body>
</html>
