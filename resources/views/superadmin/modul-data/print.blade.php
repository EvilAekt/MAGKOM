<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Data - {{ $modul->nama_modul }} - Dinas Kominfo Sleman</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Public Sans', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #F1F5F9;
            color: #1E293B;
            font-size: 12px;
            line-height: 1.5;
            padding: 2rem;
        }

        .paper {
            max-width: 900px;
            margin: 0 auto;
            background: #FFFFFF;
            padding: 2.5rem 3rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .kop-surat {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.5rem;
            border-bottom: 3px double #0F172A;
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .kop-logo {
            width: 70px;
            height: auto;
        }

        .kop-text h2 {
            font-size: 14pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0F172A;
            margin: 0;
        }

        .kop-text h1 {
            font-size: 16pt;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #0751FF;
            margin: 0.2rem 0;
        }

        .kop-text p {
            font-size: 8.5pt;
            color: #475569;
            margin: 0;
        }

        .report-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .report-title {
            font-size: 13pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0F172A;
            margin-bottom: 0.35rem;
        }

        .report-subtitle {
            font-size: 9pt;
            color: #64748B;
        }

        .meta-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            font-size: 8.5pt;
            color: #475569;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
            font-size: 9pt;
        }

        .data-table th, .data-table td {
            border: 1px solid #CBD5E1;
            padding: 6px 8px;
            vertical-align: top;
        }

        .data-table th {
            background-color: #F8FAFC;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 8pt;
            color: #1E293B;
            text-align: left;
        }

        .data-table tr:nth-child(even) {
            background-color: #F8FAFC;
        }

        .ttd-container {
            display: flex;
            justify-content: flex-end;
            margin-top: 2rem;
            page-break-inside: avoid;
        }

        .ttd-box {
            text-align: center;
            min-width: 220px;
            font-size: 9.5pt;
        }

        .ttd-space {
            height: 60px;
        }

        .ttd-name {
            font-weight: 800;
            text-decoration: underline;
            color: #0F172A;
        }

        .action-bar {
            max-width: 900px;
            margin: 0 auto 1.5rem auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.65rem 1.25rem;
            border-radius: 6px;
            font-weight: 700;
            font-size: 0.875rem;
            text-decoration: none;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: #0751FF;
            color: #FFFFFF;
        }

        .btn-secondary {
            background: #FFFFFF;
            color: #334155;
            border: 1px solid #CBD5E1;
        }

        @media print {
            body {
                background: #FFFFFF;
                padding: 0;
            }

            .action-bar {
                display: none !important;
            }

            .paper {
                box-shadow: none;
                max-width: 100%;
                padding: 0;
            }

            @page {
                size: A4 portrait;
                margin: 15mm;
            }
        }
    </style>
</head>
<body>

    <div class="action-bar">
        <a href="{{ route('superadmin.modul.index', ['slug' => $modul->slug]) }}" class="btn btn-secondary">
            ← Kembali ke Modul Data
        </a>
        <button type="button" onclick="window.print()" class="btn btn-primary">
            🖨️ Cetak Dokumen Sekarang
        </button>
    </div>

    <div class="paper">
        {{-- KOP SURAT RESMI PEMKAB SLEMAN --}}
        <div class="kop-surat">
            <img src="{{ asset('images/logo-sleman.png') }}" alt="Logo Sleman" class="kop-logo" onerror="this.style.display='none'">
            <div class="kop-text">
                <h2>Pemerintah Kabupaten Sleman</h2>
                <h1>Dinas Komunikasi dan Informatika</h1>
                <p>Jalan Parasamya No. 1, Beran, Tridadi, Sleman, Daerah Istimewa Yogyakarta 55511</p>
                <p>Telepon: (0274) 868405 | Laman: kominfo.slemankab.go.id | Email: kominfo@slemankab.go.id</p>
            </div>
        </div>

        {{-- JUDUL LAPORAN --}}
        <div class="report-header">
            <div class="report-title">Laporan Data {{ $modul->nama_modul }}</div>
            <div class="report-subtitle">Sistem Informasi Manajemen Magang Kominfo (MagKom) Kab. Sleman</div>
        </div>

        {{-- METADATA --}}
        <div class="meta-info">
            <div>
                <strong>Modul:</strong> {{ $modul->nama_modul }} ({{ $modul->tipe_sumber === 'physical_table' ? 'Tabel: ' . $modul->nama_tabel_fisik : 'Dynamic Schema' }})
            </div>
            <div>
                <strong>Waktu Cetak:</strong> {{ now()->translatedFormat('d F Y, H:i') }} WIB
            </div>
        </div>

        {{-- TABEL DATA --}}
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;">No</th>
                    @if($modul->tipe_sumber === 'physical_table')
                        <th style="width: 60px;">PK ({{ $modul->primary_key }})</th>
                    @else
                        <th style="width: 50px;">ID</th>
                    @endif

                    @foreach($modul->fields->where('tampil_di_tabel', true) as $f)
                        <th>{{ $f->label }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($records as $idx => $r)
                    <tr>
                        <td style="text-align: center;">{{ $idx + 1 }}</td>
                        <td>
                            @if($modul->tipe_sumber === 'physical_table')
                                {{ $r[$modul->primary_key] ?? '-' }}
                            @else
                                {{ $r['id'] ?? '-' }}
                            @endif
                        </td>
                        @foreach($modul->fields->where('tampil_di_tabel', true) as $f)
                            @php
                                $val = $r[$f->nama_kolom] ?? '';
                                if (is_array($val)) {
                                    $val = implode(', ', $val);
                                } elseif (is_bool($val)) {
                                    $val = $val ? 'Ya' : 'Tidak';
                                }
                            @endphp
                            <td>{{ $val !== '' ? $val : '-' }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $modul->fields->where('tampil_di_tabel', true)->count() + 2 }}" style="text-align: center; color: #94A3B8; padding: 2rem;">
                            Belum ada baris data pada modul ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- TANDA TANGAN PENGESAH --}}
        <div class="ttd-container">
            <div class="ttd-box">
                <div>Sleman, {{ now()->translatedFormat('d F Y') }}</div>
                <div style="font-weight: 700; margin-top: 0.25rem;">Petugas Pengelola Sistem,</div>
                <div class="ttd-space"></div>
                <div class="ttd-name">{{ auth()->user()->nama ?? 'Administrator MagKom' }}</div>
                <div style="font-size: 8.5pt; color: #64748B;">NIP. 19850115 201001 1 012</div>
            </div>
        </div>
    </div>

    @if(!empty($isPdfMode))
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                window.print();
            });
        </script>
    @endif
</body>
</html>
