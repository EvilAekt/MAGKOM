<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Data Penempatan &amp; Surat Diterima</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15mm 15mm 15mm 15mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8.5pt;
            line-height: 1.35;
            color: #1e293b;
        }
        .header {
            text-align: center;
            border-bottom: 2.5px double #0f172a;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header h2 {
            margin: 0;
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0f172a;
        }
        .header h3 {
            margin: 2px 0 0 0;
            font-size: 12pt;
            font-weight: bold;
            color: #0641cc;
        }
        .header p {
            margin: 2px 0 0 0;
            font-size: 7.5pt;
            color: #475569;
        }
        .doc-title {
            text-align: center;
            margin-bottom: 10px;
        }
        .doc-title h4 {
            margin: 0;
            font-size: 10.5pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #0f172a;
        }
        .doc-title .meta-text {
            font-size: 7.5pt;
            color: #64748b;
            margin-top: 2px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        th, td {
            border: 0.75px solid #94a3b8;
            padding: 4.5px 5.5px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: bold;
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }
        tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .text-center {
            text-align: center;
        }
        .text-bold {
            font-weight: bold;
        }
        .badge-status {
            display: inline-block;
            padding: 1.5px 4px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-aktif {
            background-color: #dcfce7;
            color: #166534;
        }
        .badge-selesai {
            background-color: #e0f2fe;
            color: #075985;
        }
        .footer-note {
            margin-top: 15px;
            font-size: 7.5pt;
            color: #64748b;
            display: table;
            width: 100%;
        }
        .footer-left {
            display: table-cell;
            text-align: left;
            vertical-align: top;
        }
        .footer-right {
            display: table-cell;
            text-align: right;
            vertical-align: top;
        }
    </style>
</head>
<body>

    {{-- KOP RESMI DINAS KOMINFO SLEMAN --}}
    <div class="header">
        <h2>Pemerintah Kabupaten Sleman</h2>
        <h3>Dinas Komunikasi dan Informatika</h3>
        <p>Jl. Parasamya No. 1, Beran, Tridadi, Sleman, Daerah Istimewa Yogyakarta 55511 | Telp: (0274) 868405 | Website: slemankab.go.id</p>
    </div>

    {{-- JUDUL DOKUMEN REKAP --}}
    <div class="doc-title">
        <h4>Rekapitulasi Data Penempatan &amp; Surat Diterima Magang</h4>
        <div class="meta-text">
            Dicetak otomatis oleh Sistem MagKom pada: {{ $generatedAt->translatedFormat('d F Y H:i:s') }} WIB | Total: {{ $records->count() }} Data Peserta
        </div>
    </div>

    {{-- TABEL DATA --}}
    <table>
        <thead>
            <tr>
                <th style="width: 25px;" class="text-center">No</th>
                <th style="width: 110px;">No Surat Diterima</th>
                <th style="width: 105px;">Nama Peserta</th>
                <th style="width: 75px;">NIM / NISN</th>
                <th style="width: 110px;">Institusi / Kampus</th>
                <th style="width: 90px;">Jurusan</th>
                <th style="width: 90px;">Bidang Penempatan</th>
                <th style="width: 95px;">Pembimbing</th>
                <th style="width: 65px;" class="text-center">Jalur</th>
                <th style="width: 95px;" class="text-center">Periode Magang</th>
                <th style="width: 50px;" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $index => $item)
                @php
                    $jurusan = $item->kuota?->jurusan?->nama_jurusan ?? ($item->peserta?->profil?->jurusan?->nama_jurusan ?? '-');
                    $jalur = strtoupper($item->jalur instanceof \App\Enums\JalurEnum ? $item->jalur->value : (string) ($item->jalur ?? 'REGULER'));
                    $noSurat = $item->no_surat ?? '500.12/' . str_pad((string) $item->id, 4, '0', STR_PAD_LEFT) . '/DISKOMINFO/' . date('Y');
                    $statusVal = strtolower((string) ($item->status?->value ?? $item->status ?? '-'));
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-bold">{{ $noSurat }}</td>
                    <td class="text-bold">{{ $item->peserta?->nama ?? '-' }}</td>
                    <td>{{ $item->peserta?->profil?->nim_nisn ?? '-' }}</td>
                    <td>{{ $item->peserta?->profil?->institusi ?? '-' }}</td>
                    <td>{{ $jurusan }}</td>
                    <td>{{ $item->bidang?->nama_bidang ?? '-' }}</td>
                    <td>{{ $item->pembimbing?->nama ?? '-' }}</td>
                    <td class="text-center">{{ $jalur }}</td>
                    <td class="text-center">
                        {{ $item->tgl_mulai?->format('d/m/Y') ?? '-' }} s.d. {{ $item->tgl_selesai?->format('d/m/Y') ?? '-' }}
                    </td>
                    <td class="text-center">
                        <span class="badge-status {{ $statusVal === 'aktif' ? 'badge-aktif' : 'badge-selesai' }}">
                            {{ ucfirst($statusVal) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center" style="padding: 20px; color: #64748b;">
                        Tidak ada catatan penempatan yang sesuai dengan parameter filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- FOOTER / PENGESAHAN --}}
    <div class="footer-note">
        <div class="footer-left">
            <div><em>Dokumen ini diterbitkan secara sah dan tervalidasi melalui Platform Terpadu MagKom Sleman.</em></div>
            <div>Dokumen Resmi Arsip Operasional Bidang Informasi &amp; Komunikasi Publik.</div>
        </div>
        <div class="footer-right">
            <div>Sleman, {{ $generatedAt->translatedFormat('d F Y') }}</div>
            <div style="margin-top: 35px; font-weight: bold; text-decoration: underline;">Administrator MagKom</div>
            <div>NIP. 19850315 201101 1 002</div>
        </div>
    </div>

</body>
</html>

