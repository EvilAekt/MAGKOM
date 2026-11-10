<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $modul->nama_modul }} - Portal Informasi MagKom Sleman</title>
    <meta name="description" content="{{ $modul->deskripsi ?: 'Katalog informasi resmi Magang Kominfo Kabupaten Sleman.' }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-body: #F8FAFC;
            --bg-card: #FFFFFF;
            --border-color: #E2E8F0;
            --text-main: #0F172A;
            --text-muted: #64748B;
            --primary: #0751FF;
            --primary-hover: #003ECC;
            --primary-subtle: rgba(7, 81, 255, 0.08);
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --bg-body: #0F172A;
                --bg-card: #182234;
                --border-color: #26354D;
                --text-main: #F8FAFC;
                --text-muted: #94A3B8;
                --primary: #38BDF8;
                --primary-hover: #0EA5E9;
                --primary-subtle: rgba(56, 189, 248, 0.12);
            }
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Public Sans', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background-color: var(--bg-card);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: var(--text-main);
            font-weight: 800;
            font-size: 1.125rem;
        }

        .container {
            max-width: 1100px;
            margin: 2rem auto;
            padding: 0 1.5rem;
            width: 100%;
            flex: 1;
        }

        .header-section {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .badge-public {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.75rem;
            font-weight: 700;
            background: var(--primary-subtle);
            color: var(--primary);
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            margin-bottom: 0.75rem;
        }

        .search-box {
            display: flex;
            gap: 0.75rem;
            margin-top: 1.5rem;
        }

        .search-input {
            flex: 1;
            height: 44px;
            border: 1px solid var(--border-color);
            background: var(--bg-body);
            color: var(--text-main);
            border-radius: 10px;
            padding: 0 1rem;
            font-size: 0.875rem;
            font-family: inherit;
        }

        .btn-search {
            height: 44px;
            padding: 0 1.5rem;
            background: var(--primary);
            color: #FFFFFF;
            font-weight: 700;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 0.875rem;
        }

        .data-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.03);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
            text-align: left;
        }

        th {
            background: var(--bg-body);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            color: var(--text-muted);
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            vertical-align: top;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .btn-detail {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.4rem 0.85rem;
            background: var(--primary-subtle);
            color: var(--primary);
            text-decoration: none;
            border-radius: 6px;
            font-weight: 700;
            font-size: 0.75rem;
            transition: all 0.15s ease;
        }

        .btn-detail:hover {
            background: var(--primary);
            color: #FFFFFF;
        }

        footer {
            background: var(--bg-card);
            border-top: 1px solid var(--border-color);
            padding: 1.5rem;
            text-align: center;
            font-size: 0.8125rem;
            color: var(--text-muted);
            margin-top: 3rem;
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="{{ route('landing') }}" class="brand">
            <img src="{{ asset('images/logo-sleman.png') }}" alt="Logo Sleman" width="28" height="34" onerror="this.style.display='none'">
            <span>Portal MagKom Sleman</span>
        </a>
        <div>
            <a href="{{ route('landing') }}" style="color: var(--text-muted); font-size: 0.875rem; text-decoration: none; font-weight: 600;">
                ← Kembali ke Beranda
            </a>
        </div>
    </nav>

    <main class="container">
        <div class="header-section">
            <span class="badge-public">🌐 Katalog Publik Read-Only</span>
            <h1 style="font-size: 1.75rem; font-weight: 900; margin-bottom: 0.5rem; letter-spacing: -0.02em;">
                {{ $modul->nama_modul }}
            </h1>
            <p style="color: var(--text-muted); font-size: 0.9375rem; max-width: 680px;">
                {{ $modul->deskripsi ?: 'Katalog data publik resmi yang diterbitkan oleh Dinas Komunikasi dan Informatika Kabupaten Sleman.' }}
            </p>

            <form method="GET" action="{{ route('public.modul.index', ['slug' => $modul->slug]) }}" class="search-box">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari dalam katalog {{ $modul->nama_modul }}..." class="search-input">
                <button type="submit" class="btn-search">Cari Data</button>
                @if(request('q'))
                    <a href="{{ route('public.modul.index', ['slug' => $modul->slug]) }}" style="display: flex; align-items: center; padding: 0 1rem; color: var(--text-muted); text-decoration: none; font-size: 0.875rem;">Reset</a>
                @endif
            </form>
        </div>

        <div class="data-card">
            @if($records->isEmpty())
                <div style="padding: 4rem 1.5rem; text-align: center; color: var(--text-muted);">
                    <p style="font-weight: 600; font-size: 1rem;">Tidak ada data yang ditemukan.</p>
                </div>
            @else
                @php
                    $visibleFields = $modul->fields->where('tampil_di_tabel', true);
                @endphp
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 50px; text-align: center;">No</th>
                                @foreach($visibleFields as $f)
                                    <th>{{ $f->label }}</th>
                                @endforeach
                                <th style="text-align: right; width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $idx => $row)
                                @php
                                    $rowId = $modul->tipe_sumber === 'physical_table' ? ($row->{$modul->primary_key} ?? $row->id ?? '') : $row->id;
                                    $rowData = $modul->tipe_sumber === 'physical_table' ? (array) $row : ($row->data ?? []);
                                @endphp
                                <tr>
                                    <td style="text-align: center; color: var(--text-muted); font-weight: 700;">
                                        {{ $records->firstItem() + $idx }}
                                    </td>
                                    @foreach($visibleFields as $f)
                                        @php
                                            $val = $rowData[$f->nama_kolom] ?? null;
                                        @endphp
                                        <td>
                                            @if($f->tipe_data === 'boolean')
                                                <span style="font-weight: 700; color: {{ $val ? '#059669' : '#64748B' }};">
                                                    {{ $val ? 'Ya' : 'Tidak' }}
                                                </span>
                                            @elseif($f->tipe_data === 'file' && !empty($val))
                                                <a href="{{ $val }}" target="_blank" style="color: var(--primary); font-weight: 600; text-decoration: none;">
                                                    Buka Berkas
                                                </a>
                                            @elseif(is_array($val))
                                                {{ implode(', ', $val) }}
                                            @else
                                                {{ $val !== null && $val !== '' ? $val : '-' }}
                                            @endif
                                        </td>
                                    @endforeach
                                    <td style="text-align: right;">
                                        <a href="{{ route('public.modul.show', ['slug' => $modul->slug, 'id' => $rowId]) }}" class="btn-detail">
                                            Detail →
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($records->hasPages())
                    <div style="padding: 1.25rem; border-top: 1px solid var(--border-color);">
                        {{ $records->links() }}
                    </div>
                @endif
            @endif
        </div>
    </main>

    <footer>
        <p>&copy; {{ date('Y') }} Dinas Komunikasi dan Informatika Kabupaten Sleman. Hak Cipta Dilindungi.</p>
    </footer>

</body>
</html>
