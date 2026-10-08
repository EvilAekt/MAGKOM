<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Master Rubrik Penilaian - SuperAdmin MagKom</title>
</head>
<body>
    <h1>Master Rubrik Penilaian</h1>
    <p>Total Bobot Aktif: {{ $totalBobotAktif }}%</p>
    <ul>
        @foreach ($rubrikList as $rubrik)
            <li>{{ $rubrik->kriteria }} (Bobot: {{ $rubrik->bobot }}%) - {{ $rubrik->aktif ? 'Aktif' : 'Nonaktif' }}</li>
        @endforeach
    </ul>
</body>
</html>
