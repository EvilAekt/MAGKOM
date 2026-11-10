<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $jenis->nama }} - {{ $peserta->nama }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 20mm 20mm 20mm 20mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10.5pt;
            line-height: 1.45;
            color: #1a202c;
        }
        .header-kop {
            text-align: center;
            border-bottom: 3px double #2d3748;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header-kop h3 {
            margin: 0;
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-kop h2 {
            margin: 3px 0 0 0;
            font-size: 14pt;
            font-weight: bold;
            color: #1a365d;
            text-transform: uppercase;
        }
        .header-kop p {
            margin: 3px 0 0 0;
            font-size: 8.5pt;
            color: #4a5568;
        }
        .doc-title-box {
            text-align: center;
            margin-bottom: 20px;
        }
        .doc-title {
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            margin: 0;
            color: #1a202c;
        }
        .doc-number {
            font-size: 9.5pt;
            color: #4a5568;
            margin-top: 4px;
        }
        table.meta-table {
            width: 100%;
            margin-bottom: 16px;
            border-collapse: collapse;
        }
        table.meta-table td {
            padding: 4px 0;
            vertical-align: top;
            font-size: 10pt;
        }
        .badge-verified {
            display: inline-block;
            background: #e6fffa;
            border: 1px solid #38b2ac;
            color: #234e52;
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 8.5pt;
            font-weight: bold;
            margin-bottom: 12px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 14px 0;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #cbd5e0;
            padding: 7px 10px;
            font-size: 9.5pt;
        }
        table.data-table th {
            background-color: #f7fafc;
            color: #2d3748;
            font-weight: bold;
            text-align: left;
        }
        .section-heading {
            font-size: 11pt;
            font-weight: bold;
            color: #2b6cb0;
            border-bottom: 1.5px solid #e2e8f0;
            padding-bottom: 4px;
            margin: 16px 0 8px 0;
            text-transform: uppercase;
        }
        .signature-table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }
        .signature-table td {
            vertical-align: top;
            font-size: 10pt;
        }
        .ttd-box {
            text-align: center;
            width: 250px;
        }
        .ttd-space {
            height: 65px;
        }
        .stamp-box {
            border: 2px dashed #a0aec0;
            width: 110px;
            height: 55px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 7.5pt;
            color: #718096;
            text-align: center;
            padding-top: 15px;
        }
    </style>
</head>
<body>

    {{-- KOP SURAT / HEADER LEMBAGA ASAL --}}
    <div class="header-kop">
        <h3>PEMERINTAH REPUBLIK INDONESIA</h3>
        <h2>{{ strtoupper($profil?->institusi ?? 'INSTITUSI PENDIDIKAN ASAL') }}</h2>
        <p>Jalan Pendidikan No. 45, Kompleks Akademik &bull; Telp: (021) 7890123 &bull; Email: akademik@{{ Str::slug($profil?->institusi ?? 'instansi') }}.ac.id</p>
    </div>

    {{-- JUDUL DOKUMEN --}}
    <div class="doc-title-box">
        <h1 class="doc-title">{{ $jenis->nama }}</h1>
        <div class="doc-number">Nomor: {{ $nomorDokumen }} &bull; Registrasi: #REG-{{ str_pad((string)$pendaftaran->id, 5, '0', STR_PAD_LEFT) }}</div>
    </div>

    <div style="text-align: right; margin-bottom: 12px;">
        <span class="badge-verified">&#10003; TERVERIFIKASI SISTEM MAGANG KOMINFO SLEMAN</span>
    </div>

    {{-- BIODATA PESERTA --}}
    <table class="meta-table">
        <tr>
            <td style="width: 140px; font-weight: bold;">Nama Calon Peserta</td>
            <td style="width: 10px;">:</td>
            <td><strong>{{ $peserta->nama }}</strong></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">NIM / NISN</td>
            <td>:</td>
            <td>{{ $profil?->nim_nisn ?? '1029384756' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Asal Institusi</td>
            <td>:</td>
            <td>{{ $profil?->institusi ?? 'SMK / Perguruan Tinggi' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Program Studi / Jurusan</td>
            <td>:</td>
            <td>{{ $pendaftaran->kuota?->jurusan?->nama_jurusan ?? ($profil?->jurusan?->nama_jurusan ?? 'Teknologi Informasi') }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Jenjang Pendidikan</td>
            <td>:</td>
            <td>{{ strtoupper($profil?->jenjang?->value ?? 'SMK / S1') }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Periode Penempatan</td>
            <td>:</td>
            <td>Bulan {{ $pendaftaran->bulanMagang?->bulan_mulai?->translatedFormat('F Y') }} (Durasi: {{ $pendaftaran->durasi_bulan }} Bulan)</td>
        </tr>
    </table>

    {{-- KONTEN KHUSUS BERDASARKAN JENIS DOKUMEN --}}
    @php
        $namaLower = strtolower($jenis->nama);
    @endphp

    @if(str_contains($namaLower, 'transkrip') || str_contains($namaLower, 'rapor') || str_contains($namaLower, 'nilai'))
        {{-- KONTEN TRANSKRIP NILAI --}}
        <div class="section-heading">Daftar Capaian Prestasi Akademik</div>
        <p style="font-size: 10pt; margin: 4px 0 10px 0;">Berikut adalah salinan daftar nilai mata pelajaran / mata kuliah kejuruan yang telah diselesaikan:</p>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 35px; text-align: center;">No</th>
                    <th>Mata Kuliah / Pelajaran Kejuruan</th>
                    <th style="width: 60px; text-align: center;">SKS/Jam</th>
                    <th style="width: 60px; text-align: center;">Nilai</th>
                    <th style="width: 80px; text-align: center;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center;">1</td>
                    <td>Pemrograman Berorientasi Objek & Web Lanjut</td>
                    <td style="text-align: center;">4</td>
                    <td style="text-align: center;"><strong>A</strong></td>
                    <td style="text-align: center; color: #276749;">Sangat Baik</td>
                </tr>
                <tr>
                    <td style="text-align: center;">2</td>
                    <td>Sistem Basis Data & Administrasi SQL</td>
                    <td style="text-align: center;">3</td>
                    <td style="text-align: center;"><strong>A</strong></td>
                    <td style="text-align: center; color: #276749;">Sangat Baik</td>
                </tr>
                <tr>
                    <td style="text-align: center;">3</td>
                    <td>Jaringan Komputer, Protokol HTTP & Keamanan Siber</td>
                    <td style="text-align: center;">4</td>
                    <td style="text-align: center;"><strong>A-</strong></td>
                    <td style="text-align: center; color: #276749;">Sangat Baik</td>
                </tr>
                <tr>
                    <td style="text-align: center;">4</td>
                    <td>Analisis dan Perancangan Sistem Informasi</td>
                    <td style="text-align: center;">3</td>
                    <td style="text-align: center;"><strong>B+</strong></td>
                    <td style="text-align: center; color: #2b6cb0;">Baik</td>
                </tr>
                <tr>
                    <td style="text-align: center;">5</td>
                    <td>Etika Profesi & Komunikasi Teknologi Informasi</td>
                    <td style="text-align: center;">2</td>
                    <td style="text-align: center;"><strong>A</strong></td>
                    <td style="text-align: center; color: #276749;">Sangat Baik</td>
                </tr>
            </tbody>
            <tfoot>
                <tr style="background: #f7fafc; font-weight: bold;">
                    <td colspan="2" style="text-align: right;">Indeks Prestasi Kumulatif (IPK):</td>
                    <td colspan="3" style="color: #2b6cb0;">3.86 dari skala 4.00 (Predikat: Dengan Pujian)</td>
                </tr>
            </tfoot>
        </table>

    @elseif(str_contains($namaLower, 'curriculum vitae') || str_contains($namaLower, 'cv'))
        {{-- KONTEN CURRICULUM VITAE --}}
        <div class="section-heading">1. Ringkasan Profesional & Profil</div>
        <p style="font-size: 10pt; margin: 4px 0 10px 0;">
            Pelajar/Mahasiswa aktif yang berdedikasi tinggi di bidang Teknologi Informasi dan Komunikasi. Memiliki ketertarikan mendalam pada pengembangan sistem aplikasi modern, pemeliharaan basis data relasional, dan optimalisasi layanan digital pemerintahan.
        </p>

        <div class="section-heading">2. Keahlian Teknis & Kompetensi</div>
        <table class="data-table">
            <tr>
                <td style="width: 140px; font-weight: bold;">Bahasa & Framework</td>
                <td>PHP (Laravel 10/11), JavaScript, CSS Modern, HTML5 Semantic, Python</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Basis Data & Tools</td>
                <td>MySQL, PostgreSQL, Git & GitHub, Docker, Postman, Linux Terminal</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Soft Skills</td>
                <td>Kolaborasi Tim, Pemecahan Masalah Kritis, Disiplin Administrasi, Komunikasi Aktif</td>
            </tr>
        </table>

        <div class="section-heading">3. Riwayat Proyek & Portofolio Terkait</div>
        <p style="font-size: 9.5pt; margin: 4px 0;">
            &bull; <strong>Sistem Informasi Pelayanan Publik Terpadu</strong>: Merancang API RESTful dan antarmuka responsif ramah pengguna.<br>
            &bull; <strong>Platform Manajemen Aset & Inventaris Jaringan</strong>: Mengimplementasikan pelaporan otomatis berbasis web.
        </p>

    @elseif(str_contains($namaLower, 'proposal'))
        {{-- KONTEN PROPOSAL MAGANG --}}
        <div class="section-heading">1. Latar Belakang & Tujuan Praktik Kerja</div>
        <p style="font-size: 10pt; margin: 4px 0 10px 0; text-align: justify;">
            Dalam rangka menjembatani teori akademik dengan praktik nyata di instansi pemerintahan, calon peserta bermaksud melaksanakan kegiatan magang di lingkungan Dinas Komunikasi dan Informatika (Kominfo). Kegiatan ini difokuskan pada keterlibatan aktif dalam transformasi digital dan pelayanan informasi publik.
        </p>

        <div class="section-heading">2. Rencana Tahapan Kerja (Timeline)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 90px;">Bulan</th>
                    <th>Fokus Kegiatan & Target Capaian</th>
                    <th style="width: 120px;">Indikator Output</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Bulan I</strong></td>
                    <td>Orientasi instansi, pengenalan sistem kerja Kominfo, dan pendataan kebutuhan unit.</td>
                    <td>Laporan Orientasi & Analisis Awal</td>
                </tr>
                <tr>
                    <td><strong>Bulan II</strong></td>
                    <td>Pelaksanaan tugas teknis, pendampingan sistem, dan pengisian logbook harian.</td>
                    <td>Dokumentasi Modul / Progres Tugas</td>
                </tr>
                <tr>
                    <td><strong>Bulan III</strong></td>
                    <td>Evaluasi hasil magang, penyusunan laporan akhir, dan presentasi penutupan magang.</td>
                    <td>Laporan Akhir Praktik Kerja Lapangan</td>
                </tr>
            </tbody>
        </table>

    @elseif(str_contains($namaLower, 'kartu pelajar') || str_contains($namaLower, 'ktp') || str_contains($namaLower, 'identitas'))
        {{-- KONTEN SALINAN IDENTITAS --}}
        <div class="section-heading">Salinan Kartu Identitas Resmi Pelajar / Mahasiswa</div>
        <div style="border: 2px solid #4a5568; border-radius: 8px; padding: 18px; margin: 16px 0; background: #f7fafc; width: 450px;">
            <div style="font-size: 11pt; font-weight: bold; color: #1a365d; border-bottom: 2px solid #2b6cb0; padding-bottom: 6px; margin-bottom: 12px;">
                KARTU TANDA PELAJAR / MAHASISWA
            </div>
            <table style="width: 100%; font-size: 10pt;">
                <tr>
                    <td style="width: 110px; font-weight: bold;">Nama Lengkap</td>
                    <td style="width: 10px;">:</td>
                    <td>{{ $peserta->nama }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">NIM / NISN</td>
                    <td>:</td>
                    <td>{{ $profil?->nim_nisn ?? '1029384756' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Institusi</td>
                    <td>:</td>
                    <td>{{ $profil?->institusi ?? 'Institusi Asal' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Masa Berlaku</td>
                    <td>:</td>
                    <td>Hingga Selesai Masa Pendidikan</td>
                </tr>
            </table>
            <div style="margin-top: 14px; font-size: 8.5pt; color: #718096; font-style: italic;">
                Dokumen ini merupakan salinan sah yang diunggah untuk kelengkapan administrasi program magang Kominfo.
            </div>
        </div>

    @else
        {{-- KONTEN UMUM / SURAT PENGANTAR RESMI --}}
        <div class="section-heading">Perihal: Permohonan Penempatan Praktik Kerja Lapangan / Magang</div>
        <p style="font-size: 10pt; margin: 4px 0 12px 0; text-align: justify;">
            Dengan hormat, sehubungan dengan kurikulum pembelajaran dan peningkatan kompetensi kejuruan peserta didik kami, bersama surat ini pimpinan institusi merekomendasikan nama yang tercantum di atas untuk dapat diterima mengikuti Praktik Kerja Lapangan / Magang di Dinas Komunikasi dan Informatika.
        </p>
        <p style="font-size: 10pt; margin: 4px 0 12px 0; text-align: justify;">
            Calon peserta memiliki rekam jejak akademik yang baik, disiplin, dan bersedia mematuhi segala tata tertib kedinasan yang berlaku di lingkungan instansi tempat bertugas.
        </p>
    @endif

    {{-- PENUTUP & TANDA TANGAN RESMI --}}
    <table class="signature-table">
        <tr>
            <td style="width: 55%;">
                <div style="font-size: 9.5pt; color: #4a5568;">
                    Catatan Validasi Dokumen:<br>
                    &bull; Dicetak secara digital melalui sistem Magang Kominfo.<br>
                    &bull; Keabsahan berkas telah melalui pra-pemeriksaan dokumen fisik.<br>
                    &bull; Kode Hash Integritas: <code>{{ substr($dokumen->hash ?? hash('sha256', $dokumen->id . 'magkom'), 0, 16) }}...</code>
                </div>
            </td>
            <td style="width: 45%;">
                <div class="ttd-box">
                    <div>{{ $profil?->kota ?? 'Jakarta' }}, {{ $tglDokumen }}</div>
                    <div style="font-weight: bold; margin-top: 2px;">Pejabat / Penanggung Jawab Berkas,</div>
                    <div class="ttd-space">
                        <div class="stamp-box">
                            [STEMPEL RESMI<br>TERVERIFIKASI]
                        </div>
                    </div>
                    <div style="font-weight: bold; text-decoration: underline;">Dr. H. Bambang Sudarsono, M.Kom.</div>
                    <div style="font-size: 9pt; color: #4a5568;">NIP. 19780512 200312 1 002</div>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
