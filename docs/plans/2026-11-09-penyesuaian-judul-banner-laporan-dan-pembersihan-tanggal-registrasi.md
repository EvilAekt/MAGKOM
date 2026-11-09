# Rencana Kerja: Penyesuaian Judul Banner Laporan dan Pembersihan Teks Tanggal Registrasi

Dokumen rencana kerja ini disusun untuk memenuhi 2 permintaan penyesuaian teks pada antarmuka Hub Laporan & Ekspor Admin ([`resources/views/admin/laporan/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/laporan/index.blade.php)) berdasarkan tangkapan layar dan arahan pengguna.

---

## 1. Tujuan

1. **Penggantian Istilah "Kedinasan" pada Judul Hero Banner Laporan (Poin 1 / Gambar 1)**:
   - Judul saat ini: `<h1 class="adm-hero-title">Pusat Rekapitulasi &amp; Ekspor Kedinasan</h1>`.
   - Pengguna memberikan evaluasi yang sangat tepat: halaman ini berfokus pada data rekapitulasi peserta magang, bukan laporan birokrasi kedinasan secara umum.
   - Mengganti penamaan tersebut dengan istilah yang representatif, profesional, dan berfokus langsung pada domain **Magang**:
     - **Rekomendasi Utama**: **`Pusat Rekapitulasi & Ekspor Magang`**
     - **Alternatif (opsional)**: **`Pusat Rekapitulasi Data Magang`**
     - Subjudul tetap konsisten: `Rekapitulasi Pendaftaran, Penempatan & Arsip Magang • Dinas Komunikasi dan Informatika Kabupaten Sleman`.

2. **Pembersihan Teks Tanggal di Bawah Nomor Registrasi (Poin 2 / Gambar 2)**:
   - Pada tabel Rekapitulasi Pendaftaran ([`resources/views/admin/laporan/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/laporan/index.blade.php) baris 513–515), di bawah badge nomor registrasi (`$p->nomor_pendaftaran`) saat ini terdapat teks tanggal pengajuan:
     ```html
     <div style="font-size: 0.725rem; color: #64748B; margin-top: 0.35rem;">
         {{ $p->created_at?->translatedFormat('d M Y') ?? '-' }}
     </div>
     ```
   - Teks tanggal ini membuat sel kolom Nomor Registrasi terlihat penuh dan tidak serasi dengan tabel pendaftaran utama (yang hanya menampilkan nomor registrasi dan badge status).
   - Menghapus elemen tanggal tersebut sehingga kolom No. Registrasi menjadi bersih dan rapi.

---

## 2. Asumsi & Batasan

- **Kepatuhan Aturan Blade ([`.agents/rules/AGENTS.md`](file:///d:/laragon/www/MAGKOM/.agents/rules/AGENTS.md))**:
  - Perubahan hanya berupa perbaikan teks dan pembersihan elemen markup redundan di `resources/views/admin/laporan/index.blade.php`.
  - Tidak ada penambahan CSS `<style>` inline baru atau JavaScript `<script>` di dalam Blade.
- **Integritas Fungsional & Backend**:
  - Kolom nomor registrasi, relasi data, paginasi, filter tab, dan tombol aksi ekspor tetap berjalan 100% normal.
  - Tidak ada perubahan route atau kontrak API.

---

## 3. Rencana Perubahan Berkas

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| [`resources/views/admin/laporan/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/laporan/index.blade.php) | **Modify** | 1. Ganti judul banner dari `Pusat Rekapitulasi &amp; Ekspor Kedinasan` menjadi `Pusat Rekapitulasi &amp; Ekspor Magang`.<br>2. Hapus blok tanggal `created_at` (baris 513–515) di dalam sel nomor registrasi tabel Rekap Pendaftaran. |

---

## 4. Langkah-Langkah Implementasi Bertahap

- [x] **Fase 1: Update Judul Banner Hero Laporan**
  - [x] 1.1 Edit [`resources/views/admin/laporan/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/laporan/index.blade.php) baris 13: Ganti teks judul banner menjadi `Pusat Rekapitulasi Data Magang`.

- [x] **Fase 2: Pembersihan Teks Tanggal pada Kolom No. Registrasi**
  - [x] 2.1 Edit [`resources/views/admin/laporan/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/laporan/index.blade.php) baris 513–515: Hapus `<div>` tanggal `{{ $p->created_at?->translatedFormat('d M Y') ?? '-' }}` di bawah badge nomor registrasi.

- [x] **Fase 3: Verifikasi & Quality Gates**
  - [x] 3.1 Jalankan test spesifik laporan: `vendor/bin/phpunit tests/Feature/AdminLaporanDanArsipTest.php` (8 passed).
  - [x] 3.2 Jalankan Laravel Pint: `vendor/bin/pint --test` (passed).
  - [x] 3.3 Jalankan Vite asset build: `npm run build` (sukses).
  - [x] 3.4 Jalankan full test suite: `vendor/bin/phpunit` (719 passed, 0 failures).

---

## 5. Kriteria Selesai (Definition of Done)

- [x] Judul banner di halaman Hub Laporan berubah menjadi `Pusat Rekapitulasi Data Magang` (fokus penuh pada domain magang).
- [x] Teks tanggal di bawah badge No. Registrasi pada tabel Rekapitulasi Pendaftaran sudah bersih dan rapi.
- [x] Seluruh test suite (719 tests) tetap 100% lulus tanpa regresi.
- [x] Format kode dan build aset Vite sukses.

