<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lembar Nilai Magang - {{ $peserta->name }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 20mm 20mm 20mm 20mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #1a202c;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #2d3748;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header h3 {
            margin: 4px 0 0 0;
            font-size: 13pt;
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
            margin: 15px 0 20px 0;
        }
        .title-doc h4 {
            margin: 0;
            font-size: 12pt;
            text-decoration: underline;
            text-transform: uppercase;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
            font-size: 10pt;
        }
        .info-table td {
            padding: 3px 6px;
            vertical-align: top;
        }
        .info-table td.label {
            width: 28%;
            font-weight: bold;
            color: #2d3748;
        }
        .info-table td.colon {
            width: 2%;
        }
        .grade-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10pt;
        }
        .grade-table th, .grade-table td {
            border: 1px solid #718096;
            padding: 8px 10px;
        }
        .grade-table th {
            background-color: #edf2f7;
            font-weight: bold;
            text-align: center;
        }
        .grade-table td.center {
            text-align: center;
        }
        .grade-table td.right {
            text-align: right;
        }
        .grade-table tr.total-row {
            background-color: #f7fafc;
            font-weight: bold;
        }
        .summary-box {
            border: 1px solid #cbd5e0;
            background-color: #f8fafc;
            padding: 10px 14px;
            margin-bottom: 25px;
            border-radius: 4px;
        }
        .summary-box h5 {
            margin: 0 0 6px 0;
            font-size: 10pt;
            font-weight: bold;
        }
        .summary-box p {
            margin: 0;
            font-size: 9.5pt;
            font-style: italic;
            color: #2d3748;
        }
        .signature-table {
            width: 100%;
            margin-top: 30px;
            font-size: 10pt;
        }
        .signature-table td {
            vertical-align: top;
            width: 50%;
        }
        .signature-table .signature-box {
            text-align: center;
            padding-left: 20px;
        }
        .signature-space {
            height: 70px;
        }
        .sign-name {
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>Pemerintah Daerah</h2>
        <h3>Dinas Komunikasi dan Informatika</h3>
        <p>Portal Sistem Informasi Manajemen Magang (MagKom)</p>
    </div>

    <div class="title-doc">
        <h4>LEMBAR EVALUASI DAN NILAI AKHIR MAGANG</h4>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Nama Peserta</td>
            <td class="colon">:</td>
            <td><strong>{{ $peserta->nama ?? $peserta->name }}</strong></td>
            <td class="label">Jalur Pendaftaran</td>
            <td class="colon">:</td>
            <td>{{ strtoupper($penempatan->jalur->value) }}</td>
        </tr>
        <tr>
            <td class="label">Nomor Induk (NISN/NIM)</td>
            <td class="colon">:</td>
            <td>{{ $profil->nim_nisn ?? ($profil->nomor_induk ?? '-') }}</td>
            <td class="label">Bidang Penempatan</td>
            <td class="colon">:</td>
            <td>{{ $penempatan->bidang->nama_bidang ?? 'Umum' }}</td>
        </tr>
        <tr>
            <td class="label">Asal Institusi</td>
            <td class="colon">:</td>
            <td>{{ $profil->institusi ?? ($profil->asal_institusi ?? '-') }}</td>
            <td class="label">Periode Magang</td>
            <td class="colon">:</td>
            <td>{{ $penempatan->tgl_mulai->translatedFormat('d F Y') }} s/d {{ $penempatan->tgl_selesai->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td class="label">Program Studi / Jurusan</td>
            <td class="colon">:</td>
            <td>{{ $profil->jurusan->nama_jurusan ?? ($profil->jurusan_nama ?? '-') }}</td>
            <td class="label">Pembimbing Lapangan</td>
            <td class="colon">:</td>
            <td>{{ $pembimbing->nama ?? $pembimbing->name }}</td>
        </tr>
    </table>

    <table class="grade-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 50%;">Kriteria Penilaian</th>
                <th style="width: 15%;">Bobot (%)</th>
                <th style="width: 15%;">Skor (0-100)</th>
                <th style="width: 15%;">Nilai Terbobot</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($penilaian->details as $detail)
                @php
                    $skorTerbobot = round(($detail->skor * $detail->bobot_snapshot) / 100, 2);
                @endphp
                <tr>
                    <td class="center">{{ $no++ }}</td>
                    <td>{{ $detail->rubrik->kriteria ?? 'Kriteria' }}</td>
                    <td class="center">{{ $detail->bobot_snapshot }}%</td>
                    <td class="center">{{ $detail->skor }}</td>
                    <td class="right">{{ number_format($skorTerbobot, 2) }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" class="right">NILAI AKHIR (SKALA 100) :</td>
                <td class="right">{{ number_format($penilaian->nilai_akhir, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="4" class="right">PREDIKAT MUTU :</td>
                <td class="center" style="font-size: 12pt; color: #1a365d;">{{ $penilaian->predikat }}</td>
            </tr>
        </tbody>
    </table>

    @if($penilaian->catatan)
        <div class="summary-box">
            <h5>Catatan & Evaluasi Pembimbing Lapangan:</h5>
            <p>"{{ $penilaian->catatan }}"</p>
        </div>
    @endif

    <table class="signature-table">
        <tr>
            <td style="vertical-align: top; text-align: left; padding-left: 10px;">
                @if(isset($qrCodeSvg))
                    <div style="font-size: 8pt; color: #4a5568; line-height: 1.35;">
                        <div style="margin-bottom: 5px;">
                            {!! $qrCodeSvg !!}
                        </div>
                        <strong style="color: #1a365d;">Verifikasi Dokumen Elektronik</strong><br>
                        Kode: <span style="font-family: monospace; font-weight: bold; color: #2b6cb0;">{{ $dokumenVerifikasi->kode_unik ?? '-' }}</span><br>
                        <span>Pindai QR Code untuk memvalidasi keaslian nilai resmi pada portal MagKom Diskominfo.</span>
                    </div>
                @endif
            </td>
            <td>
                <div class="signature-box">
                    <p>Ditetapkan pada: {{ ($penempatan->tgl_ditetapkan_selesai ?? now())->translatedFormat('d F Y') }}<br>
                    Pembimbing Lapangan,</p>
                    <div class="signature-space"></div>
                    <p class="sign-name">{{ $pembimbing->nama ?? $pembimbing->name }}</p>
                    <p style="margin: 2px 0 0 0; font-size: 9pt; color: #718096;">NIP / Identitas Pembimbing</p>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
