<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Pendaftaran Calon Peserta Magang</title>
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
        .badge-diterima {
            background-color: #dcfce7;
            color: #166534;
        }
        .badge-diajukan {
            background-color: #eff6ff;
            color: #1d4ed8;
        }
        .badge-perbaikan {
            background-color: #fef3c7;
            color: #92400e;
        }
        .badge-waiting {
            background-color: #f3e8ff;
            color: #6b21a8;
        }
        .badge-ditolak, .badge-dibatalkan {
            background-color: #fee2e2;
            color: #991b1b;
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
        <h4>Rekapitulasi Pendaftaran Calon Peserta Magang</h4>
        <div class="meta-text">
            Dicetak otomatis oleh Sistem MagKom pada: {{ $generatedAt->translatedFormat('d F Y H:i:s') }} WIB | Total: {{ $records->count() }} Data Pendaftaran
        </div>
    </div>

    {{-- TABEL DATA --}}
    <table>
        <thead>
            <tr>
                <th style="width: 25px;" class="text-center">No</th>
                <th style="width: 85px;">No. Registrasi</th>
                <th style="width: 130px;">Nama Calon Peserta</th>
                <th style="width: 85px;">NIM / NISN</th>
                <th style="width: 140px;">Institusi / Asal Pendidikan</th>
                <th style="width: 110px;">Jurusan</th>
                <th style="width: 85px;" class="text-center">Bulan Mulai</th>
                <th style="width: 55px;" class="text-center">Durasi</th>
                <th style="width: 75px;" class="text-center">Status</th>
                <th style="width: 85px;" class="text-center">Tgl Diajukan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $index => $p)
                @php
                    $jurusanNama = $p->kuota?->jurusan?->nama_jurusan
                        ?? ($p->user?->profil?->jurusan?->nama_jurusan ?? '-');
                    $noReg = '#REG-' . str_pad((string) $p->id, 5, '0', STR_PAD_LEFT);
                    $statusEnum = $p->status;
                    $statusLabel = $statusEnum instanceof \App\Enums\StatusPendaftaranEnum ? $statusEnum->label() : (string) ($statusEnum?->value ?? $statusEnum ?? '-');
                    $statusVal = strtolower((string) ($statusEnum?->value ?? $statusEnum ?? ''));

                    $badgeClass = match ($statusVal) {
                        'diterima' => 'badge-diterima',
                        'diajukan' => 'badge-diajukan',
                        'perlu_perbaikan' => 'badge-perbaikan',
                        'waiting_list' => 'badge-waiting',
                        'ditolak' => 'badge-ditolak',
                        'dibatalkan' => 'badge-dibatalkan',
                        default => 'badge-diajukan',
                    };
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-bold">{{ $noReg }}</td>
                    <td class="text-bold">
                        {{ $p->user?->nama ?? '-' }}
                        @if($p->user?->email)
                            <br><small style="color: #64748b; font-weight: normal;">{{ $p->user->email }}</small>
                        @endif
                    </td>
                    <td>{{ $p->user?->profil?->nim_nisn ?? '-' }}</td>
                    <td>{{ $p->user?->profil?->institusi ?? '-' }}</td>
                    <td>{{ $jurusanNama }}</td>
                    <td class="text-center">
                        {{ $p->bulanMagang?->bulan_mulai?->translatedFormat('M Y') ?? ($p->bulanMagang?->bulan_mulai?->format('M Y') ?? '-') }}
                    </td>
                    <td class="text-center">{{ $p->durasi_bulan ?? '-' }} Bulan</td>
                    <td class="text-center">
                        <span class="badge-status {{ $badgeClass }}">
                            {{ $statusLabel }}
                        </span>
                    </td>
                    <td class="text-center">
                        {{ $p->diajukan_at?->format('d/m/Y') ?? ($p->created_at?->format('d/m/Y') ?? '-') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding: 20px; color: #64748b;">
                        Tidak ada data pendaftaran yang sesuai dengan parameter filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- FOOTER / PENGESAHAN --}}
    <div class="footer-note">
        <div class="footer-left">
            <div><em>Dokumen ini diterbitkan secara sah dan tervalidasi melalui Platform Terpadu MagKom Sleman.</em></div>
            <div>Dokumen Rekapitulasi Berkas Pendaftaran Magang Dinas Kominfo Sleman.</div>
        </div>
        <div class="footer-right">
            <div>Sleman, {{ $generatedAt->translatedFormat('d F Y') }}</div>
            <div style="margin-top: 35px; font-weight: bold; text-decoration: underline;">Administrator MagKom</div>
            <div>NIP. 19850315 201101 1 002</div>
        </div>
    </div>

</body>
</html>

