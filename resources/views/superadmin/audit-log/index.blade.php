<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jejak Audit Trail - SuperAdmin MagKom</title>
</head>
<body>
    <h1>Audit Log Sistem</h1>
    <table>
        <thead>
            <tr><th>Waktu</th><th>Tipe</th><th>Aksi</th><th>Entitas</th><th>Pengguna</th></tr>
        </thead>
        <tbody>
            @foreach ($logs as $log)
                <tr>
                    <td>{{ $log->waktu }}</td>
                    <td>{{ $log->tipe->value }}</td>
                    <td>{{ $log->aksi }}</td>
                    <td>{{ $log->entitas }}</td>
                    <td>{{ $log->user?->nama ?? 'Sistem' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
