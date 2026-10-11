<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat Kelulusan Magang - {{ $peserta->nama }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #1A202C;
            margin: 0;
            padding: 0;
            background: #ffffff;
        }
        .cert-outer-border {
            border: 4px double #0641CC;
            padding: 8px;
            height: 94%;
            box-sizing: border-box;
        }
        .cert-inner-border {
            border: 1.5px solid #D97706;
            padding: 24px 32px;
            height: 95%;
            box-sizing: border-box;
            text-align: center;
            background: radial-gradient(circle at center, #FFFFFF 0%, #F8FAFC 100%);
        }
        .kop-instansi {
            font-family: Arial, Helvetica, sans-serif;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-size: 11pt;
            font-weight: bold;
            color: #475569;
            margin-bottom: 2px;
        }
        .kop-dinas {
            font-family: Arial, Helvetica, sans-serif;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 15pt;
            font-weight: 800;
            color: #0641CC;
            margin-bottom: 6px;
        }
        .kop-alamat {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8pt;
            color: #64748B;
            border-bottom: 2px solid #0641CC;
            padding-bottom: 8px;
            margin-bottom: 16px;
        }
        .cert-title {
            font-family: 'Georgia', serif;
            font-size: 26pt;
            font-weight: bold;
            color: #0F172A;
            letter-spacing: 3px;
            margin: 8px 0 2px 0;
            text-transform: uppercase;
        }
        .cert-number {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9.5pt;
            color: #475569;
            margin-bottom: 16px;
            font-weight: 600;
        }
        .cert-presented {
            font-style: italic;
            font-size: 11pt;
            color: #64748B;
            margin-bottom: 8px;
        }
        .recipient-name {
            font-family: 'Georgia', serif;
            font-size: 22pt;
            font-weight: bold;
            color: #0641CC;
            text-decoration: underline;
            margin-bottom: 4px;
        }
        .recipient-meta {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            color: #334155;
            margin-bottom: 14px;
        }
        .cert-statement {
            font-size: 11pt;
            line-height: 1.5;
            color: #334155;
            max-width: 820px;
            margin: 0 auto 16px auto;
        }
        .cert-grade-box {
            display: inline-block;
            border: 1px solid #CBD5E1;
            background: #FFFFFF;
            padding: 6px 24px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-family: Arial, Helvetica, sans-serif;
        }
        .cert-grade-box .grade-label {
            font-size: 8.5pt;
            color: #64748B;
            text-transform: uppercase;
            font-weight: bold;
        }
        .cert-grade-box .grade-val {
            font-size: 13pt;
            font-weight: 800;
            color: #0641CC;
            margin-left: 8px;
        }
        .signature-table {
            width: 100%;
            margin-top: 10px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
        }
        .signature-table td {
            vertical-align: top;
            text-align: center;
            width: 50%;
        }
        .sign-title {
            color: #475569;
            margin-bottom: 45px;
        }
        .sign-name {
            font-weight: bold;
            text-decoration: underline;
            color: #0F172A;
            font-size: 10pt;
        }
        .sign-nip {
            color: #64748B;
            font-size: 8pt;
        }
    </style>
</head>
<body>
    <div class="cert-outer-border">
        <div class="cert-inner-border">
            <div class="kop-instansi">Pemerintah Kabupaten Sleman</div>
            <div class="kop-dinas">Dinas Komunikasi dan Informatika</div>
            <div class="kop-alamat">Jl. Parasamya No. 1, Beran, Tridadi, Sleman, D.I. Yogyakarta 55511 &bull; Portal MagKom</div>

            <div class="cert-title">Sertifikat Kelulusan</div>
            <div class="cert-number">Nomor Seri: {{ $nomorSertifikat }}</div>

            <div class="cert-presented">Diberikan sebagai pengakuan atas integritas dan dedikasi kepada:</div>
            <div class="recipient-name">{{ $peserta->nama }}</div>
            <div class="recipient-meta">
                {{ $profil?->institusi ?? 'Institusi Pendidikan' }} &bull; {{ $profil?->jurusan ?? 'Mahasiswa/Siswa' }}
                @if($profil?->nim_nisn)
                    &bull; NIM/NISN: {{ $profil->nim_nisn }}
                @endif
            </div>

            <div class="cert-statement">
                Telah menyelesaikan program Praktik Kerja Lapangan / Magang Mandiri secara penuh pada Bidang <strong>{{ $penempatan->bidang?->nama_bidang ?? 'Teknologi Informasi & Komunikasi' }}</strong> Dinas Komunikasi dan Informatika Kabupaten Sleman terhitung mulai tanggal <strong>{{ $penempatan->tgl_mulai?->translatedFormat('d F Y') }}</strong> sampai dengan <strong>{{ $penempatan->tgl_selesai?->translatedFormat('d F Y') }}</strong>.
            </div>

            @if($penilaian)
                <div class="cert-grade-box">
                    <span class="grade-label">Nilai Akhir:</span>
                    <span class="grade-val">{{ number_format((float) $penilaian->nilai_akhir, 2) }} (Predikat {{ $penilaian->predikat }})</span>
                </div>
            @endif

            <table class="signature-table">
                <tr>
                    <td>
                        <div class="sign-title">
                            Pembimbing Lapangan,<br>
                            Bidang {{ $penempatan->bidang?->nama_bidang ?? 'Dinas Kominfo' }}
                        </div>
                        <div class="sign-name">{{ $penempatan->pembimbing?->nama ?? 'Pembimbing MagKom' }}</div>
                        <div class="sign-nip">NIP. {{ $penempatan->pembimbing?->profil?->nomor_identitas ?? $penempatan->pembimbing?->email ?? '-' }}</div>
                    </td>
                    <td>
                        <div class="sign-title">
                            Sleman, {{ $penempatan->tgl_selesai?->translatedFormat('d F Y') ?? now()->translatedFormat('d F Y') }}<br>
                            Kepala Dinas Komunikasi dan Informatika
                        </div>
                        <div class="sign-name">Budi Santosa, S.Kom., M.Eng.</div>
                        <div class="sign-nip">NIP. 19780512 200501 1 008</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
