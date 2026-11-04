# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

Empat peran, semuanya dinyatakan sama pentingnya (bukan satu peran prioritas):

- **Peserta magang** — 15 pendaftar baru per bulan (12 jalur umum + 3 slot khusus), durasi 1–6 bulan, sekitar 90 peserta aktif bersamaan. Situasi: sering di ponsel, waktu terbatas di sela jam kerja; pekerjaan utamanya mengerjakan tugas, mengunggah progres, mengisi logbook mingguan, dan memantau status.
- **Pembimbing** — saat ini **satu orang** pegawai Kominfo. Memberi tugas, meninjau progres dan logbook, menilai dengan rubrik, menetapkan nilai akhir dan status selesai. Volume: menangani puluhan peserta aktif sekaligus, kerja di desktop.
- **Admin (Administrasi Kominfo)** — memverifikasi pendaftaran, mengelola kuota bulan, menetapkan dan memindahkan Pembimbing, mengelola Waiting List.
- **SuperAdmin** — mengelola master data dan akun staf, slot khusus, pengaturan sistem, arsip, dan audit log. Pada fungsi Pembimbing (tugas, review, penilaian) hanya **melihat**.

## Product Purpose

Portal magang Dinas Kominfo Sleman yang menangani siklus penuh: pendaftaran → verifikasi dan kuota → penempatan dan penetapan Pembimbing → pelaksanaan (tugas, progres, logbook) → penilaian rubrik → kelulusan, masa tenggang, dan pengarsipan.

Keberhasilan berarti: satu Pembimbing tetap bisa membimbing puluhan peserta tanpa kehilangan jejak, tiap peserta selalu tahu posisinya tanpa perlu bertanya, dan nilai keluar dari rubrik yang konsisten serta dapat diaudit.

## Positioning

Satu-satunya alur magang instansi yang **menjadikan status sebagai antarmuka**. Karena sistem sengaja tidak mengirim notifikasi dan tidak mengirim email undangan, dashboard, badge status, catatan Admin, dan timeline **adalah** kanal informasi utama — bukan pelengkap. Aturan operasional lapangan (kuota atomik, batas beban Pembimbing, masa tenggang, penilaian yang dapat diubah dengan alasan) dikodekan langsung ke dalam tampilan, bukan diserahkan ke kebiasaan pengguna.

## Operating Context

- Instansi pemerintahan daerah (Dinas Komunikasi dan Informatika Kabupaten Sleman); bahasa antarmuka Indonesia formal tetapi tidak birokratis.
- Admin menyerahkan akun dan password awal **secara manual di luar sistem**; staf wajib ganti password saat login pertama.
- Tidak ada notifikasi push/email dan tidak ada login sosial; peserta memakai email terverifikasi.
- Penilaian memakai rubrik berbobot; nilai dapat diubah dengan alasan minimal 10 karakter dan riwayat tersimpan.
- Logbook bersifat mingguan (`minggu_ke`), hanya dalam rentang magang, tidak boleh tanggal masa depan, isi mundur dibatasi pengaturan (default 3 hari).
- Tenggat tugas harus ≥ hari ini dan ≤ tanggal selesai penempatan; label "Terlambat" dan "mendekati deadline" (≤ 3 hari) dihitung saat dibaca.
- Staf bekerja di desktop; peserta kemungkinan besar di ponsel.
- Dokumen sumber: `docs/01-PRD-MagKom.md`, `docs/02-TDD-MagKom.md`, `docs/04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md`, `docs/05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md`, `docs/07-DESIGN-SYSTEM-MAGKOM.md`.

## Capabilities and Constraints

- Laravel + Blade + Tailwind 3 + Vite (server-rendered, CSP produksi hanya `'self'`: font di-host sendiri, tanpa `style=` inline, tanpa CDN).
- View tersusun per peran: `resources/views/{admin,pembimbing,peserta,superadmin,public,auth}`.
- RBAC berjenjang lewat Policy; SuperAdmin hanya-baca pada seluruh fungsi Pembimbing; Pembimbing hanya mengakses peserta bimbingannya (cegah IDOR).
- Penerimaan bersifat atomik dalam satu transaksi (kunci baris kuota, tambah terisi, buat penempatan, ubah status, tulis audit).
- Batas beban Pembimbing dari pengaturan (default 20 peserta aktif; `null` = tanpa batas).
- Penghapusan tugas tidak tersedia pada versi ini; tugas hanya dapat diedit selama belum selesai.
- Tidak ada fitur presensi/kehadiran (di luar scope), tidak ada chat internal.
- Halaman error kustom 403/404/419/429/500 wajib ada.
- Master data yang masih dipakai penempatan aktif tidak boleh dinonaktifkan.

## Brand Commitments

- Nama produk: **MagKom** (Magang Kominfo). Institusi: Dinas Kominfo Kabupaten Sleman.
- Palet terkunci 11 token CSS Peeper, sudah hidup di `resources/css/base/variables.css`: `#02275D` `#01347C` `#003566` `#3F51B5` `#FFC300` `#FFCB05` `#212529` `#000000` `#AAAAAA` `#BBBBBB` `#FFFFFF`. Tidak ada warna merek lain, tidak ada merah/hijau semantik di luar pemetaan status turunan.
- Tipografi terkunci: **Open Sans** (400/600/700) untuk seluruh antarmuka. Font generasi sebelumnya (Plus Jakarta Sans) tidak dipakai.
- Aset yang disetujui: hanya logo resmi di `G:\MAGKOM\Logo`. Foto di `G:\MAGKOM\Aset Foto` **belum** disetujui untuk desain — gunakan placeholder abu-abu berlabel, jangan stok foto palsu.

## Evidence on Hand

- PRD v1.1 lengkap dengan 30+ acceptance criteria, matriks hak akses, state machine status, dan aturan bisnis BR-01…BR-16.
- TDD, skema database + data dictionary, dokumen keamanan RBAC/audit, dan addendum QA.
- Design system tertulis (`docs/07-DESIGN-SYSTEM-MAGKOM.md`) hasil ekstraksi file Figma "MAGKOM Kominfo Sleman".
- Implementasi Blade + token CSS yang sudah berjalan di repo ini.
- File desain kerja: `Designnnn/MagKom-Design.figcraft` (halaman 01 Design System, 02 Dashboard Pembimbing — Buat Tugas Baru).
- Tidak ada testimoni, studi kasus, angka pencapaian, atau logo mitra yang boleh dikarang.

## Product Principles

1. **Status selalu terlihat.** Setiap status memakai warna **dan** ikon **dan** teks; tidak pernah warna saja.
2. **Netral dulu, biru untuk aksi, kuning untuk perhatian.** Warna hanya bermakna, bukan dekorasi.
3. **Satu layar harus cukup untuk mengambil keputusan.** Ringkasan, aturan yang berlaku, dan aksi utama tampil bersama; pengguna tidak perlu berpindah halaman untuk tahu konsekuensinya.
4. **Aturan bisnis tampil sebagai bantuan kontekstual**, bukan pesan error setelah gagal (contoh: rentang tenggat yang sah ditampilkan di depan field).
5. **Beban satu Pembimbing adalah batas desain.** Layar staf dioptimalkan untuk memproses banyak peserta dengan cepat, tanpa mengorbankan keterbacaan.

## Accessibility & Inclusion

WCAG 2.1 AA untuk halaman publik, auth, dan area Peserta (target PRD 9.5). Cincin fokus 3px terlihat di semua permukaan; target sentuh 44×44 px untuk publik/auth/peserta; area staf boleh memakai tombol tabel 36 px karena dioptimalkan untuk desktop. Teks bantu dan placeholder memakai `#003566` (bukan `#AAAAAA`) agar kontras ≥ 4,5:1; batas kontrol memakai `#BBBBBB`/`#AAAAAA` dan tidak boleh jadi satu-satunya penanda status. Makna tidak boleh bergantung pada warna saja, dan `prefers-reduced-motion` dihormati.

## Open Decisions

- Ilustrasi/imagery untuk halaman publik belum diputuskan (aset foto belum disetujui).
- Breakpoint resmi mengikuti saran Tailwind (640/768/1024/1280); sidebar menjadi drawer di bawah 1024 — belum diuji dengan pengguna.
- Aturan edit tugas setelah peserta mengunggah progres masih menunggu klarifikasi PO (AC-08-7).
