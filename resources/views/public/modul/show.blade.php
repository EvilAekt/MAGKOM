<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Data #{{ $id }} - {{ $modul->nama_modul }} - Dinas Kominfo Sleman</title>
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
            max-width: 800px;
            margin: 2.5rem auto;
            padding: 0 1.5rem;
            width: 100%;
            flex: 1;
        }

        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .detail-row {
            display: grid;
            grid-template-columns: 200px 1fr;
            padding: 1rem 0;
            border-bottom: 1px solid var(--border-color);
            font-size: 0.9375rem;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }

        .detail-value {
            color: var(--text-main);
            font-weight: 600;
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
            <a href="{{ route('public.modul.index', ['slug' => $modul->slug]) }}" style="color: var(--text-muted); font-size: 0.875rem; text-decoration: none; font-weight: 600;">
                ← Kembali ke Katalog
            </a>
        </div>
    </nav>

    <main class="container">
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1.25rem;">
                <div>
                    <span style="font-size: 0.75rem; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.05em;">
                        Detail Informasi Publik
                    </span>
                    <h1 style="font-size: 1.5rem; font-weight: 900; margin: 0.25rem 0;">
                        {{ $modul->nama_modul }} #{{ $id }}
                    </h1>
                </div>
                <a href="{{ route('public.modul.index', ['slug' => $modul->slug]) }}" style="font-size: 0.8125rem; color: var(--primary); text-decoration: none; font-weight: 700;">
                    Kembali
                </a>
            </div>

            <div>
                @foreach($modul->fields as $f)
                    @php
                        $col = $f->nama_kolom;
                        $val = $data[$col] ?? null;
                    @endphp
                    <div class="detail-row">
                        <div class="detail-label">{{ $f->label }}</div>
                        <div class="detail-value">
                            @if($f->tipe_data === 'boolean')
                                <span style="color: {{ $val ? '#059669' : '#64748B' }}; font-weight: 800;">
                                    {{ $val ? 'Ya' : 'Tidak' }}
                                </span>
                            @elseif($f->tipe_data === 'file' && !empty($val))
                                <a href="{{ $val }}" target="_blank" style="color: var(--primary); text-decoration: none; font-weight: 700;">
                                    Unduh / Lihat Berkas Lampiran ↗
                                </a>
                            @elseif(is_array($val))
                                {{ implode(', ', $val) }}
                            @elseif($f->tipe_data === 'textarea')
                                <div style="white-space: pre-wrap; font-weight: normal; color: var(--text-main);">{{ $val ?: '-' }}</div>
                            @else
                                {{ $val !== null && $val !== '' ? $val : '-' }}
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; {{ date('Y') }} Dinas Komunikasi dan Informatika Kabupaten Sleman. Hak Cipta Dilindungi.</p>
    </footer>

</body>
</html>
