<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Staf - SuperAdmin MagKom</title>
</head>
<body>
    <h1>Manajemen Staf Internal</h1>
    <table>
        <thead>
            <tr><th>Nama</th><th>Email</th><th>Role</th><th>Status</th></tr>
        </thead>
        <tbody>
            @foreach ($stafList as $staf)
                <tr>
                    <td>{{ $staf->nama }}</td>
                    <td>{{ $staf->email }}</td>
                    <td>{{ $staf->role->label() }}</td>
                    <td>{{ $staf->status_aktif ? 'Aktif' : 'Nonaktif' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
