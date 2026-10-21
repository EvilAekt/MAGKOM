<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Kinerja Pembimbing Lapangan - Diskominfo Sleman</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1.5cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000000;
            background: #ffffff;
            margin: 0;
            padding: 0;
        }
        .kop-surat {
            text-align: center;
            border-bottom: 2px solid #000000;
            padding-bottom: 8px;
            margin-bottom: 18px;
        }
        .kop-surat h2 {
            font-size: 13pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        .kop-surat h1 {
            font-size: 14pt;
            font-weight: bold;
            margin: 2px 0;
            text-transform: uppercase;
        }
        .kop-surat p {
            font-size: 9pt;
            margin: 0;
        }
        .judul-laporan {
            text-align: center;
            margin-bottom: 16px;
        }
        .judul-laporan h3 {
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 0 0 4px 0;
            text-transform: uppercase;
        }
        .info-pembimbing {
            width: 100%;
            margin-bottom: 14px;
            font-size: 10.5pt;
        }
        .info-pembimbing td {
            padding: 3px 0;
            vertical-align: top;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10pt;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #000000;
            padding: 6px 8px;
            vertical-align: middle;
        }
        table.data-table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }
        .ttd-box {
            float: right;
            width: 250px;
            text-align: center;
            margin-top: 25px;
        }
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="no-print" style="background: #eff6ff; border: 1px solid #bfdbfe; padding: 10px 15px; margin-bottom: 20px; border-radius: 6px; display: flex; justify-content: space-between; align-items: center;">
        <span style="font-family: sans-serif; font-size: 13px; color: #1e3a8a;">
            Pratinjau Cetak Laporan Portofolio Mentoring ASN. Klik tombol di kanan untuk mencetak atau simpan ke PDF.
        </span>
        <button onclick="window.print()" style="font-family: sans-serif; background: #0641CC; color: #ffffff; border: none; padding: 8px 18px; border-radius: 6px; font-weight: bold; cursor: pointer;">
            Cetak Dokumen
        </button>
    </div>

    <!-- Kop Surat Dinas -->
    <div class="kop-surat">
        <h2>Pemerintah Kabupaten Sleman</h2>
        <h1>Dinas Komunikasi dan Informatika</h1>
        <p>Jalan Parasamya No. 1, Beran, Tridadi, Sleman, D.I. Yogyakarta 55511</p>
        <p>Telepon (0274) 868405 &bull; Pos-el: kominfo@slemankab.go.id &bull; Laman: kominfo.slemankab.go.id</p>
    </div>

    <!-- Judul Dokumen -->
    <div class="judul-laporan">
        <h3>Lembar Rekapitulasi Pembimbingan Magang Mahasiswa / Siswa (LPJ)</h3>
        <span style="font-size: 10pt;">Nomor: 500.12/LPJ-MAGKOM/{{ date('Y') }}</span>
    </div>

    <!-- Identitas Pembimbing Lapangan ASN -->
    <table class="info-pembimbing">
        <tr>
            <td style="width: 200px;">Nama Pembimbing Lapangan</td>
            <td style="width: 15px;">:</td>
            <td><strong>{{ $pembimbingUser->nama }}</strong></td>
        </tr>
        <tr>
            <td>NIP / Identitas Pegawai</td>
            <td>:</td>
            <td>{{ $pembimbingUser->nip ?? '19880512 201101 1 003' }}</td>
        </tr>
        <tr>
            <td>Jabatan Kedinasan</td>
            <td>:</td>
            <td>Pranata Komputer / Pembimbing Lapangan Magang</td>
        </tr>
        <tr>
            <td>Instansi</td>
            <td>:</td>
            <td>Dinas Komunikasi dan Informatika Kabupaten Sleman</td>
        </tr>
    </table>

    <!-- Tabel Rekap Data Mentee -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th>Nama Peserta Magang</th>
                <th>NIM / NISN</th>
                <th>Asal Institusi / Sekolah</th>
                <th>Jurusan</th>
                <th style="width: 70px;">Periode</th>
                <th style="width: 50px;">Tugas Selesai</th>
                <th style="width: 50px;">Logbook</th>
                <th style="width: 50px;">Nilai Akhir</th>
                <th style="width: 40px;">Predikat</th>
            </tr>
        </thead>
        <tbody>
            @forelse($allPenempatans as $index => $p)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td><strong>{{ $p->peserta?->nama }}</strong></td>
                    <td style="text-align: center;">{{ $p->peserta?->profil?->nim_nisn ?? '-' }}</td>
                    <td>{{ $p->peserta?->profil?->institusi ?? '-' }}</td>
                    <td>{{ $p->peserta?->profil?->jurusan?->nama_jurusan ?? '-' }}</td>
                    <td style="text-align: center; font-size: 8.5pt;">
                        {{ $p->tgl_mulai ? $p->tgl_mulai->format('d/m/y') : '-' }} s.d {{ $p->tgl_selesai ? $p->tgl_selesai->format('d/m/y') : '-' }}
                    </td>
                    <td style="text-align: center;">
                        {{ $p->tugas->where('status.value', 'selesai')->count() }} / {{ $p->tugas->count() }}
                    </td>
                    <td style="text-align: center;">
                        {{ $p->logbooks->count() }}
                    </td>
                    <td style="text-align: center; font-weight: bold;">
                        {{ $p->penilaian?->nilai_akhir !== null ? number_format((float) $p->penilaian->nilai_akhir, 2) : '-' }}
                    </td>
                    <td style="text-align: center; font-weight: bold;">
                        {{ $p->penilaian?->predikat ?? '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align: center; padding: 20px;">
                        Tidak ada data bimbingan pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Tanda Tangan -->
    <div class="ttd-box">
        <p style="margin: 0 0 4px 0;">Sleman, {{ $tanggalCetak }}</p>
        <p style="margin: 0 0 55px 0;">Pembimbing Lapangan,</p>
        <p style="margin: 0; font-weight: bold; text-decoration: underline;">{{ $pembimbingUser->nama }}</p>
        <p style="margin: 0; font-size: 9.5pt;">NIP. {{ $pembimbingUser->nip ?? '19880512 201101 1 003' }}</p>
    </div>

</body>
</html>
