# Plan: Implementasi Lengkap Portal Peserta MagKom (/peserta/*)

| Metadata | Keterangan |
|---|---|
| **Tanggal Pembuatan** | 9 Oktober 2026 |
| **Status Dokumen** | Menunggu Persetujuan Pengguna (Draft Plan) |
| **Target Area** | Portal Peserta (`/peserta/*`) |
| **Referensi Utama** | `docs/01-PRD-MagKom.md` (F-02, F-03, F-08, F-09, F-10, F-12), `docs/07-DESIGN-SYSTEM-MAGKOM.md` (Bab 2, 3, 4) |

---

## 1. Tujuan
Membangun antarmuka terintegrasi dan logika server-side untuk seluruh alur **Portal Peserta** (`/peserta/*`) dengan desain premium sesuai Design System MagKom (Open Sans, palet resmi Blue 700/600, Netral 0-900, badge status WCAG AA).

Fokus utama mencakup:
1. **Layout Shell Dashboard Peserta**: Sidebar biru 256px (`#0641CC`), topbar putih 64px, drawer responsif ponsel (<1024px), status banner, dan navigasi peran.
2. **Profil & Biodata (`peserta.profil.show`)**: Formulir biodata lengkap (asal sekolah/kampus, NISN/NIM, jurusan, jenjang, kontak WA, kontak darurat).
3. **Pendaftaran Magang (`peserta.pendaftaran.index` & `detail`)**: Riwayat & pembuatan pengajuan baru, pemilihan periode bulan & kuota aktif, timeline visual status pendaftaran (diajukan, waiting list, revisi, diterima), serta antarmuka unggah berkas (surat pengantar, proposal, transkrip) dengan validasi instan & signed URL.
4. **Logbook Harian (`peserta.logbook.index`)**: Form input jurnal aktivitas kerja, upload lampiran PDF / link HTTPS, status reviu pembimbing, dan batasan rentang tanggal magang (AC-09).
5. **Tugas & Progres (`peserta.tugas.index` & `show`)**: Daftar tugas bimbingan, indikator tenggat waktu/keterlambatan, dan formulir unggah progres pengerjaan (AC-08).
6. **Lembar Nilai (`peserta.nilai.show`)**: Rincian 5 rubrik nilai (bobot snapshot, skor, nilai terbobot), predikat huruf (A–E), dan tombol cetak/unduh Lembar Nilai resmi PDF (F-10, F-12).

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi:
- **A-1**: Aplikasi MagKom menggunakan rendering server-side berbasis Blade view. Endpoint controller di `LogbookController`, `TugasController`, dan `NilaiController` yang sebelumnya mengembalikan `JsonResponse` disesuaikan agar me-return Blade view secara default untuk request browser web, dan JSON jika diminta (AJAX).
- **A-2**: Pengguna yang mengakses rute `/peserta/*` telah melewati middleware `auth`, `verified`, `must_change_password`, dan `role:peserta`.
- **A-3**: Jenjang pendidikan terbagi menjadi `smk`, `d3`, dan `s1` (`JenjangEnum`). Syarat dokumen wajib mengacu pada tabel `jenis_dokumen` yang aktif untuk jenjang terkait.
- **A-4**: Desain UI sepenuhnya patuh pada `docs/07-DESIGN-SYSTEM-MAGKOM.md` (WCAG 2.1 AA kontras teks, tombol tinggi 44px, radius 8px, tanpa inline style).

### Pertanyaan Terbuka:
- Kontak darurat di profil peserta: Kami mengusulkan penambahan kolom opsional/wajib `nama_kontak_darurat`, `no_kontak_darurat`, dan `hubungan_kontak_darurat` melalui migration non-destruktif pada tabel `peserta_profil`.

---

## 3. Evaluasi Opsi Desain

### 3.1 Struktur Layout Dashboard
- **Opsi 1: Satu Layout Bersama `layouts.dashboard` untuk Seluruh Role dengan Kondisional Menu.**
  - *Kelebihan*: Satu file CSS/struktur sidebar.
  - *Kekurangan*: Percabangan `if($role === 'peserta')` yang rumit, mudah membocorkan menu admin atau pembimbing jika terjadi kesalahan logika.
- **Opsi 2: Layout Khusus `layouts.peserta` Khusus untuk Peserta Magang (Terpilih).**
  - *Kelebihan*: Terisolasi dengan aman (zero leakage risiko privilege escalation), bersih, navigasi fixed untuk siklus peserta (Profil -> Pendaftaran -> Logbook -> Tugas -> Nilai), mudah disesuaikan dengan status penempatan peserta.
  - *Rekomendasi*: **Opsi 2** sesuai prinsip arsitektur modular MagKom.

### 3.2 Penambahan Kolom Kontak Darurat
- **Opsi 1: Menambahkan Kolom Terpisah di Tabel `peserta_profil` (Terpilih).**
  - *Kelebihan*: Data relasional murni, tipe data jelas, validasi regex nomor HP dapat ditegakkan dengan ketat di `PesertaProfilRequest`.
  - *Kekurangan*: Membutuhkan migration baru (`add_kontak_darurat_to_peserta_profil_table`).
- **Opsi 2: Menggabungkan Kontak Darurat ke dalam Kolom JSON.**
  - *Kelebihan*: Tanpa menambah banyak kolom.
  - *Kekurangan*: Query dan validasi Eloquent lebih rentan inkonsistensi.
  - *Rekomendasi*: **Opsi 1**.

---

## 4. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
|---|---|---|
| `database/migrations/2026_10_09_000002_add_kontak_darurat_to_peserta_profil_table.php` | Buat | Migration penambahan `nama_kontak_darurat`, `no_kontak_darurat`, `hubungan_kontak_darurat` ke `peserta_profil`. |
| `app/Models/PesertaProfil.php` | Ubah | Tambahkan kolom kontak darurat ke `$fillable`. |
| `app/Http/Requests/PesertaProfilRequest.php` | Ubah | Validasi kolom kontak darurat (nama, nomor telp regex, relasi). |
| `resources/views/layouts/peserta.blade.php` | Buat | Master layout dashboard peserta: Sidebar 256px Blue 700, topbar 64px, mobile drawer, flash message alert, token CSS DS-MagKom. |
| `resources/views/peserta/profil/show.blade.php` | Buat | Tampilan form pengisian biodata profil lengkap, info akun, jurusan, jenjang, asal institusi, dan kontak darurat. |
| `resources/views/peserta/pendaftaran/index.blade.php` | Buat | Daftar riwayat pendaftaran magang, CTA pembuatan baru, modal/form pemilihan bulan & kuota aktif. |
| `resources/views/peserta/pendaftaran/detail.blade.php` | Buat | Halaman detail pengajuan: Timeline Tracker Status (5 status), formulir upload berkas persyatan (PDF/DOCX), tombol ajukan resmi, tombol unduh SK diterima jika lulus. |
| `app/Http/Controllers/Peserta/LogbookController.php` | Ubah | Perbarui method `index` agar mengembalikan Blade view `peserta.logbook.index` untuk web browser. |
| `resources/views/peserta/logbook/index.blade.php` | Buat | Tampilan logbook harian: form entri kegiatan harian (tanggal, deskripsi, upload lampiran PDF/link HTTPS), tabel riwayat dan catatan reviu pembimbing. |
| `app/Http/Controllers/Peserta/TugasController.php` | Ubah | Perbarui method `index` dan `show` agar mengembalikan Blade view `peserta.tugas.index` & `peserta.tugas.show`. |
| `resources/views/peserta/tugas/index.blade.php` | Buat | Daftar tugas magang aktif, filter status, indikator tenggat waktu, kartu tugas. |
| `resources/views/peserta/tugas/show.blade.php` | Buat | Detail instruksi tugas dari pembimbing, riwayat progres submission, form submit hasil/laporan pengerjaan tugas. |
| `app/Http/Controllers/Peserta/NilaiController.php` | Ubah | Perbarui method `show` agar mengembalikan Blade view `peserta.nilai.show`. |
| `resources/views/peserta/nilai/show.blade.php` | Buat | Lembar evaluasi penilaian peserta: tabel 5 rubrik penilaian (disiplin, inisiatif, kerjasama, teknis, laporan), total skor, predikat huruf A–E, catatan evaluator, dan tombol unduh PDF resmi. |
| `tests/Feature/PesertaPortalWorkflowTest.php` | Buat | Feature test otomatis menguji seluruh alur: pengisian profil, pendaftaran, unggah berkas, pengajuan, pengisian logbook, dan pengecekan nilai. |

---

## 5. Langkah Implementasi

### Tahap 1: Fondasi Skema & Layout Dashboard Peserta
- [x] Buat migration `2026_10_09_000002_add_kontak_darurat_to_peserta_profil_table.php` dan jalankan `php artisan migrate`.
- [x] Update `$fillable` di `app/Models/PesertaProfil.php` dan validasi di `app/Http/Requests/PesertaProfilRequest.php`.
- [x] Buat layout master `resources/views/layouts/peserta.blade.php` lengkap dengan CSS Design System MagKom, sidebar 256px, topbar 64px, Lucide icon SVG inline, flash notification, modal wrapper, dan drawer ponsel.

### Tahap 2: Modul Profil & Biodata Peserta (Prioritas 1)
- [x] Buat view `resources/views/peserta/profil/show.blade.php`.
- [x] Implementasikan input: Nama & Email (read-only dari user), Asal Sekolah/Kampus (`institusi`), NISN/NIM (`nim_nisn`), Jurusan (dropdown aktif), Jenjang (SMK/D3/S1), Nomor WhatsApp (`no_hp`), dan Kontak Darurat (`nama_kontak_darurat`, `no_kontak_darurat`, `hubungan_kontak_darurat`).
- [x] Berikan banner status profil ("Profil belum lengkap" vs "Profil lengkap, siap mendaftar magang").

### Tahap 3: Modul Pendaftaran Magang & Upload Berkas (Prioritas 1)
- [x] Buat view `resources/views/peserta/pendaftaran/index.blade.php` dengan tabel pendaftaran, status badge warna, tombol detail, dan modal/form buat pendaftaran baru dengan dropdown bulan berstatus Tersedia & kuota aktif.
- [x] Buat view `resources/views/peserta/pendaftaran/detail.blade.php` dengan:
  - Timeline status tracker visual: Draf -> Diajukan / Waiting List -> Perlu Perbaikan / Ditolak / Diterima.
  - Box alert catatan admin jika status `perlu_perbaikan` atau `ditolak`.
  - Formulir unggah berkas per jenis dokumen wajib (Surat Pengantar, Proposal, Transkrip) dengan progress status per file.
  - Tombol aksi "Ajukan Pendaftaran Sekarang" yang hanya aktif saat semua berkas wajib lengkap (dengan proteksi double-submit).
  - Tombol unduh "Surat Keterangan Diterima (PDF)" bila status penempatan diterima.

### Tahap 4: Modul Logbook Harian (Tahap Lanjutan)
- [x] Sesuaikan `app/Http/Controllers/Peserta/LogbookController.php` method `index` agar me-render view `peserta.logbook.index`.
- [x] Buat view `resources/views/peserta/logbook/index.blade.php` dengan pengecekan penempatan aktif, form tambah entri (tanggal tidak mundur melebihi batas pengaturan, kegiatan, lampiran PDF/URL HTTPS), serta tabel riwayat logbook dan feedback revisi pembimbing.

### Tahap 5: Modul Tugas & Progres Bimbingan (Tahap Lanjutan)
- [x] Sesuaikan `app/Http/Controllers/Peserta/TugasController.php` method `index` & `show` agar me-render view Blade.
- [x] Buat view `resources/views/peserta/tugas/index.blade.php` dan `resources/views/peserta/tugas/show.blade.php` yang menampilkan indikator deadline, badge keterlambatan, form upload link/file progres, dan histori review.

### Tahap 6: Modul Lembar Nilai & Unduh PDF
- [x] Sesuaikan `app/Http/Controllers/Peserta/NilaiController.php` method `show` agar me-render view `peserta.nilai.show`.
- [x] Buat view `resources/views/peserta/nilai/show.blade.php` yang menampilkan tabel 5 rubrik nilai, skor terbobot, predikat huruf (A–E), dan tombol direct download PDF `peserta.nilai.unduh`.

### Tahap 7: Testing & Verifikasi Kualitas
- [x] Buat test otomatis `tests/Feature/PesertaPortalWorkflowTest.php` untuk memvalidasi seluruh alur peserta.
- [x] Jalankan test suite PHPUnit / Pest untuk memastikan 100% test lulus tanpa regresi.

---

## 6. Strategi Testing
- **Feature Tests**:
  - `PesertaProfilTest`: Uji validasi simpan profil, kelengkapan form, regex nomor kontak darurat.
  - `PesertaPendaftaranWorkflowTest`: Uji alur pembuatan pendaftaran, validasi upload dokumen wajib, penguncian tombol ajukan saat berkas belum lengkap, dan perubahan status ke `diajukan` atau `waiting_list`.
  - `PesertaLogbookTest`: Uji proteksi pengisian jika penempatan belum aktif, validasi rentang tanggal.
  - `PesertaNilaiTest`: Uji visibilitas lembar nilai (hanya tampil saat status final), unduh PDF lembar nilai.
- **Manual QA**:
  - Verifikasi tampilan desktop 1440px dan mobile 390px.
  - Verifikasi WCAG 2.1 AA kontras warna pada semua badge status.

---

## 7. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Upload file berbahaya pada berkas pendaftaran atau logbook | Eksploitasi RCE atau malware | Dokumen divalidasi MIME type asli di server, disimpan di disk privat dengan nama acak (UUID/hash), diunduh via signed URL sementara. |
| IDOR / Akses pendaftaran atau tugas peserta lain | Kebocoran data pribadi (BOLA/IDOR) | Kebijakan otorisasi (`authorize('view', $pendaftaran)` dan `peserta_id === auth()->id()`) diterapkan ketat di level controller dan policy. |
| Double submission pengajuan berkas pendaftaran | Inkonsistensi audit dan data ganda | JavaScript event handler mendisable tombol submit setelah 1 kali klik + database transaction lock. |

---

## 8. Kriteria Selesai (Definition of Done)
- [x] Skema database `peserta_profil` mendukung kontak darurat dan berhasil dimigrasikan.
- [x] Master layout `layouts.peserta` terpasang rapi sesuai Design System MagKom (Open Sans, palet warna resmi, sidebar responsif).
- [x] Halaman Profil & Biodata (`/peserta/profil`) berfungsi penuh (simpan, baca, notifikasi flash).
- [x] Halaman Pendaftaran Magang (`/peserta/pendaftaran` & detail) berfungsi penuh dengan timeline tracker dan upload berkas.
- [x] Halaman Logbook Harian (`/peserta/logbook`), Tugas (`/peserta/tugas`), dan Nilai (`/peserta/nilai`) terintegrasi dengan layout Blade resmi.
- [x] Seluruh unit dan feature tests lolos hijau (`php artisan test`).
