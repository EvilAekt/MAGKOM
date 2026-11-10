<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Panduan Pendaftaran Magang Diskominfo Sleman</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 18mm 18mm 18mm 18mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            line-height: 1.5;
            color: #1f2937;
        }
        
        /* Kop Surat Resmi Kedinasan */
        .kop-header {
            text-align: center;
            border-bottom: 3px double #1f2937;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .kop-instansi-atas {
            font-size: 13pt;
            font-weight: bold;
            color: #111827;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
        }
        .kop-instansi-utama {
            font-size: 15pt;
            font-weight: bold;
            color: #1b3860;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin: 3px 0;
        }
        .kop-instansi-alamat {
            font-size: 8.5pt;
            color: #4b5563;
            line-height: 1.4;
            margin: 4px 0 0 0;
        }

        /* Judul Dokumen */
        .doc-title-block {
            text-align: center;
            margin-bottom: 18px;
        }
        .doc-title {
            margin: 0;
            font-size: 12pt;
            font-weight: bold;
            color: #1b3860;
            text-transform: uppercase;
        }
        .doc-subtitle {
            margin: 3px 0 0 0;
            font-size: 10pt;
            font-weight: bold;
            color: #374151;
            text-transform: uppercase;
        }
        .doc-nomor {
            margin-top: 4px;
            font-size: 8.5pt;
            color: #6b7280;
            font-family: monospace;
        }

        /* Heading Bab */
        .section-title {
            font-size: 10.5pt;
            font-weight: bold;
            color: #1b3860;
            border-bottom: 1.5px solid #1b3860;
            padding-bottom: 3px;
            margin-top: 14px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        p {
            margin: 0 0 7px 0;
            text-align: justify;
        }

        /* Tabel Data & Format Berkas */
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0 12px 0;
            font-size: 9pt;
        }
        .content-table th {
            background-color: #1b3860;
            color: #ffffff;
            font-weight: bold;
            padding: 6px 8px;
            text-align: left;
            border: 1px solid #1b3860;
        }
        .content-table td {
            padding: 5px 8px;
            border: 1px solid #d1d5db;
            vertical-align: top;
        }
        .content-table tr:nth-child(even) td {
            background-color: #f9fafb;
        }

        /* Box Informasi & Peringatan Khusus */
        .info-box {
            background-color: #eff6ff;
            border: 1px solid #93c5fd;
            border-left: 4px solid #1b3860;
            border-radius: 4px;
            padding: 8px 12px;
            margin: 10px 0;
            font-size: 9pt;
            color: #1e40af;
        }
        .info-box strong {
            color: #1e3a8a;
        }

        /* Alur 4 Tahap List */
        .step-block {
            margin-bottom: 8px;
            padding-left: 6px;
        }
        .step-header {
            font-weight: bold;
            color: #111827;
            font-size: 9.5pt;
        }
        .step-desc {
            font-size: 9pt;
            color: #4b5563;
            margin: 2px 0 6px 14px;
            text-align: justify;
        }

        /* Footer Pengesahan / Kontak */
        .footer-note {
            margin-top: 18px;
            border-top: 1px dashed #9ca3af;
            padding-top: 8px;
            font-size: 8pt;
            color: #6b7280;
            text-align: center;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

    <!-- KOP SURAT KEDINASAN -->
    <div class="kop-header">
        <div class="kop-instansi-atas">PEMERINTAH KABUPATEN SLEMAN</div>
        <div class="kop-instansi-utama">DINAS KOMUNIKASI DAN INFORMATIKA</div>
        <div class="kop-instansi-alamat">
            Jalan Parasamya No. 1, Kompleks Kantor Pemda Sleman, Beran, Tridadi, Sleman, D.I. Yogyakarta 55511<br>
            Telepon: (0274) 868405 | Pos-el: kominfo@slemankab.go.id | Laman Resmi: <strong>https://magkom.slemankab.go.id</strong>
        </div>
    </div>

    <!-- JUDUL DOKUMEN RESMI -->
    <div class="doc-title-block">
        <div class="doc-title">Petunjuk Teknis & Panduan Pendaftaran Magang</div>
        <div class="doc-subtitle">Praktik Kerja Lapangan (PKL) / Magang Riset Mahasiswa</div>
        <div class="doc-nomor">Nomor Dokumen: {{ $nomorJuknis }}</div>
    </div>

    <!-- BAB I: INFORMASI UMUM -->
    <div class="section-title">I. Ketentuan Umum & Jalur Pendaftaran</div>
    <p>
        Program Magang dan Praktik Kerja Lapangan (PKL) di lingkungan Dinas Komunikasi dan Informatika (Diskominfo) Kabupaten Sleman diselenggarakan sebagai wujud sinergi peningkatan kompetensi digital, wawasan tata kelola pemerintahan berbasis elektronik (SPBE), serta pengalaman kerja riil bagi insan akademis.
    </p>

    <div class="info-box">
        <strong>PENTING:</strong> Pelaksanaan magang di Diskominfo Kabupaten Sleman dilaksanakan secara <strong>luring (WFO) penuh</strong> di kantor dinas, <strong>bebas biaya (gratis)</strong>, dan peserta tidak dipungut biaya apa pun selama proses seleksi hingga penerbitan sertifikat.
    </div>

    <table class="content-table">
        <thead>
            <tr>
                <th style="width: 28%;">Jalur Pendaftaran</th>
                <th style="width: 22%;">Batas Peserta</th>
                <th>Mekanisme Akun & Pengajuan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>1. Jalur Mandiri</strong></td>
                <td>1 Calon Peserta</td>
                <td>Pendaftaran perorangan. Calon peserta membuat 1 akun individu dan melengkapi seluruh biodata serta berkas mandiri.</td>
            </tr>
            <tr>
                <td><strong>2. Jalur Kelompok (Tim)</strong></td>
                <td>2 sampai 4 Orang</td>
                <td>
                    <strong>Cukup 1 Akun Perwakilan/Ketua Tim</strong> yang mendaftar. Rekan tim <em>tidak perlu</em> membuat akun terpisah. Seluruh identitas dan berkas rekan tim dimasukkan bersamaan oleh Ketua di formulir permohonan.
                </td>
            </tr>
        </tbody>
    </table>

    <!-- BAB II: PERSYARATAN BERKAS -->
    <div class="section-title">II. Persyaratan Berkas Administrasi (Format PDF)</div>
    <p>
        Seluruh calon peserta wajib menyiapkan berkas administrasi resmi bertanda tangan dan berstempel institusi dalam format digital <strong>PDF (maksimal 2 MB per berkas)</strong>:
    </p>

    <table class="content-table">
        <thead>
            <tr>
                <th style="width: 25%;">Jenjang Pendidikan</th>
                <th style="width: 35%;">Daftar Berkas Wajib</th>
                <th>Keterangan Teknis Berkas</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Siswa SMK (Kejuruan)</strong></td>
                <td>
                    1. Surat Pengantar Sekolah Asli<br>
                    2. Curriculum Vitae (CV) Siswa<br>
                    3. Salinan Rapor Terakhir
                </td>
                <td>
                    Surat pengantar wajib bertanda tangan Kepala Sekolah/Waka Humas berstempel resmi. Untuk kelompok, nama seluruh anggota dicantumkan dalam 1 surat pengantar sekolah.
                </td>
            </tr>
            <tr>
                <td><strong>Mahasiswa (D3 / D4 / S1)</strong></td>
                <td>
                    1. Surat Pengantar Fakultas/Prodi<br>
                    2. CV & Portofolio Keterampilan<br>
                    3. Transkrip Nilai Kumulatif
                </td>
                <td>
                    Surat pengantar diterbitkan oleh Dekanat/Ketua Program Studi. CV diharapkan memuat keahlian teknis (pemrograman, multimedia, jaringan, atau analisis data).
                </td>
            </tr>
        </tbody>
    </table>

    <!-- BAB III: TAHAPAN ALUR PENDAFTARAN -->
    <div class="section-title">III. Alur Tahapan Pendaftaran (SOP 4 Langkah)</div>
    
    <div class="step-block">
        <div class="step-header">Langkah 1: Eksplorasi Kuota & Pembuatan Akun MAGKOM</div>
        <div class="step-desc">
            Calon peserta memeriksa ketersediaan kursi secara real-time pada menu <strong>Ketersediaan Kuota</strong> di portal MAGKOM. Pilih jenjang pendidikan, periode bulan masuk, dan jalur pendaftaran (Mandiri atau Kelompok). Buat akun login dengan alamat email aktif dan lakukan konfirmasi aktivasi.
        </div>
    </div>

    <div class="step-block">
        <div class="step-header">Langkah 2: Pengajuan Permohonan & Seleksi Berkas Administrasi</div>
        <div class="step-desc">
            Login ke portal peserta, pilih bidang penempatan yang diminati, lengkapi data profil, dan unggah berkas persyaratan yang diminta. Tim administrasi Diskominfo akan menyeleksi berkas dalam rentang <strong>1 hingga 3 hari kerja</strong>. Hasil penerimaan diumumkan langsung di dashboard peserta.
        </div>
    </div>

    <div class="step-block">
        <div class="step-header">Langkah 3: Pelaksanaan Magang & Pengisian Logbook Aktivitas</div>
        <div class="step-desc">
            Peserta yang dinyatakan diterima akan ditetapkan pembimbing lapangan sesuai bidang keahlian. Selama masa magang (durasi 1 s/d 3 bulan), peserta wajib hadir tepat waktu, mengerjakan penugasan dinas, dan mengisi laporan aktivitas harian (logbook) yang diparaf oleh pembimbing.
        </div>
    </div>

    <div class="step-block">
        <div class="step-header">Langkah 4: Evaluasi Akhir, Lembar Nilai & Sertifikat Resmi</div>
        <div class="step-desc">
            Pada akhir periode magang, pembimbing lapangan menginput evaluasi kinerja. Peserta dapat langsung mengunduh <strong>Lembar Nilai Magang Resmi</strong> serta <strong>Sertifikat Magang</strong> bertandatangan digital Kepala Dinas Komunikasi dan Informatika ber-QR Code validasi keaslian.
        </div>
    </div>

    <!-- BAB IV: BIDANG PENEMPATAN & TATA TERTIB -->
    <div class="section-title">IV. Bidang Penempatan & Tata Tertib Kedinasan</div>
    <table class="content-table">
        <thead>
            <tr>
                <th style="width: 32%;">Bidang Penempatan</th>
                <th>Fokus Kompetensi & Jurusan Terkait</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Aplikasi Informatika (Aptika)</strong></td>
                <td>Pengembangan sistem web/mobile, UI/UX design, database management, dan pemeliharaan aplikasi layanan publik Pemkab Sleman (RPL, Informatika, Sistem Informasi).</td>
            </tr>
            <tr>
                <td><strong>Statistik Sektoral</strong></td>
                <td>Pembersihan, analisis data statistik sektoral, penyusunan infografis, dan pengelolaan portal Satu Data Sleman (Statistika, Matematika, Data Science).</td>
            </tr>
            <tr>
                <td><strong>Persandian & Keamanan Siber</strong></td>
                <td>Pengamanan infrastruktur jaringan, konfigurasi server dinas, mitigasi insiden keamanan siber, dan audit jaringan (TKJ, Keamanan Siber, Teknik Komputer).</td>
            </tr>
            <tr>
                <td><strong>Sekretariat Kedinasan</strong></td>
                <td>Administrasi persuratan digital, kearsipan dinas, dokumentasi kehumasan, dan tata kelola operasional (OTKP, Administrasi Perkantoran, Manajemen).</td>
            </tr>
        </tbody>
    </table>

    <p style="font-size: 8.5pt; color: #4b5563; margin-top: 4px;">
        <strong>Jam Pelayanan Kedinasan:</strong> Senin – Kamis: Pukul 07.30 – 15.30 WIB | Jumat: Pukul 07.30 – 14.30 WIB.<br>
        Peserta wajib mengenakan pakaian seragam sekolah / almamater kampus yang sopan dan rapi serta menjunjung tinggi kode etik kedinasan.
    </p>

    <!-- FOOTER RESMI DOKUMEN -->
    <div class="footer-note">
        Dokumen panduan ini diterbitkan secara resmi melalui Sistem Informasi Manajemen Magang (MAGKOM) Dinas Komunikasi dan Informatika Kabupaten Sleman.<br>
        Untuk informasi lebih lanjut, silakan akses portal resmi: <strong>https://magkom.slemankab.go.id</strong>
    </div>

</body>
</html>
