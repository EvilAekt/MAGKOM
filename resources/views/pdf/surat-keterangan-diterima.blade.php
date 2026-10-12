<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan Diterima Magang - {{ $peserta->nama }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 25mm 20mm 20mm 20mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #1a202c;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #2d3748;
            padding-bottom: 12px;
            margin-bottom: 24px;
        }
        .header h2 {
            margin: 0;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header h3 {
            margin: 4px 0 0 0;
            font-size: 14pt;
            font-weight: bold;
            color: #1a365d;
        }
        .header p {
            margin: 4px 0 0 0;
            font-size: 9pt;
            color: #4a5568;
        }
        .title-doc {
            text-align: center;
            margin: 20px 0 25px 0;
        }
        .title-doc h4 {
            margin: 0;
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
        }
        .title-doc .nomor-surat {
            margin-top: 4px;
            font-size: 10pt;
            color: #4a5568;
        }
        .content-body {
            text-align: justify;
            margin-bottom: 20px;
        }
        .info-table {
            width: 100%;
            margin: 15px 0 20px 15px;
            font-size: 10.5pt;
        }
        .info-table td {
            padding: 4px 6px;
            vertical-align: top;
        }
        .info-table td.label {
            width: 30%;
            font-weight: bold;
            color: #2d3748;
        }
        .info-table td.colon {
            width: 3%;
        }
        .statement-box {
            background-color: #f7fafc;
            border-left: 4px solid #2b6cb0;
            padding: 12px 16px;
            margin: 20px 0;
            font-size: 10.5pt;
        }
        .footer-container {
            margin-top: 30px;
            width: 100%;
        }
        .footer-qr {
            width: 50%;
            vertical-align: top;
            font-size: 8pt;
            color: #4a5568;
            line-height: 1.3;
        }
        .footer-qr .qr-wrapper {
            margin-bottom: 6px;
        }
        .footer-qr .qr-code-text {
            font-family: monospace;
            font-weight: bold;
            color: #1a365d;
            font-size: 8.5pt;
        }
        .footer-sig {
            width: 50%;
            vertical-align: top;
            text-align: center;
        }
        .footer-sig .date {
            margin-bottom: 55px;
        }
        .footer-sig .name {
            font-weight: bold;
            text-decoration: underline;
        }
        .footer-sig .nip {
            font-size: 9.5pt;
            color: #4a5568;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>PEMERINTAH KABUPATEN</h2>
        <h3>DINAS KOMUNIKASI DAN INFORMATIKA</h3>
        <p>Jalan Parasamya No. 1, Kompleks Kantor Pemda | Email: kominfo@pemda.go.id | Telp: (0274) 868405</p>
    </div>

    <div class="title-doc">
        <h4>SURAT KETERANGAN PENERIMAAN MAGANG</h4>
        <div class="nomor-surat">Nomor: {{ $nomorSurat }}</div>
    </div>

    <div class="content-body">
        <p>
            Yang bertanda tangan di bawah ini, Kepala Dinas Komunikasi dan Informatika menerangkan dengan sebenarnya bahwa calon peserta magang berikut:
        </p>

        <table class="info-table">
            <tr>
                <td class="label">Nama Lengkap</td>
                <td class="colon">:</td>
                <td><strong>{{ $peserta->nama }}</strong></td>
            </tr>
            <tr>
                <td class="label">NIM / NISN</td>
                <td class="colon">:</td>
                <td>{{ $peserta->profil?->nim_nisn ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Institusi Asal</td>
                <td class="colon">:</td>
                <td>{{ $peserta->profil?->institusi ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Jurusan / Program Studi</td>
                <td class="colon">:</td>
                <td>{{ $penempatan->kuota?->jurusan?->nama_jurusan ?? $peserta->profil?->jurusan?->nama_jurusan ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Jalur Pendaftaran</td>
                <td class="colon">:</td>
                <td>{{ $penempatan->jalur?->label() ?? 'Umum' }}</td>
            </tr>
        </table>

        <div class="statement-box">
            Dinyatakan secara sah <strong>DITERIMA</strong> untuk melaksanakan Praktik Kerja Lapangan / Magang Mandiri di lingkungan Dinas Komunikasi dan Informatika dengan rincian penempatan sebagai berikut:
        </div>

        <table class="info-table">
            <tr>
                <td class="label">Periode Magang</td>
                <td class="colon">:</td>
                <td>
                    <strong>{{ \Carbon\Carbon::parse($penempatan->tgl_mulai)->translatedFormat('d F Y') }}</strong>
                    sampai dengan
                    <strong>{{ \Carbon\Carbon::parse($penempatan->tgl_selesai)->translatedFormat('d F Y') }}</strong>
                </td>
            </tr>
            <tr>
                <td class="label">Unit Bidang Kerja</td>
                <td class="colon">:</td>
                <td>{{ $penempatan->bidang?->nama_bidang ?? 'Layanan Komunikasi dan Informatika' }}</td>
            </tr>
            <tr>
                <td class="label">Pembimbing Lapangan</td>
                <td class="colon">:</td>
                <td>{{ $penempatan->pembimbing?->nama ?? 'Tim Pembimbing Dinas Kominfo' }}</td>
            </tr>
        </table>

        <p>
            Demikian Surat Keterangan ini diterbitkan untuk dipergunakan sebagaimana mestinya sebagai bukti resmi penerimaan kegiatan magang di Dinas Komunikasi dan Informatika.
        </p>
    </div>

    <table class="footer-container">
        <tr>
            <td class="footer-qr">
                @if(isset($qrCodeSvg))
                    <div class="qr-wrapper">
                        {!! $qrCodeSvg !!}
                    </div>
                @endif
                <div>
                    <strong>Verifikasi Dokumen Elektronik</strong><br>
                    Kode: <span class="qr-code-text">{{ $dokumenVerifikasi->kode_unik ?? '-' }}</span><br>
                    <span>Pindai QR Code untuk memvalidasi keaslian dokumen pada portal resmi MagKom Diskominfo.</span>
                </div>
            </td>
            <td class="footer-sig">
                <div class="date">
                    Ditetapkan pada: {{ $tglTerbit }}<br>
                    Kepala Dinas Komunikasi dan Informatika
                </div>
                <div class="name">Drs. Pengelola Magang, M.Eng.</div>
                <div class="nip">NIP. 19780512 200212 1 003</div>
            </td>
        </tr>
    </table>
</body>
</html>
