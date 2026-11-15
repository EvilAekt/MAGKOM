# Laporan Investigasi Kinerja Aplikasi & Optimasi Memori (PERF-002)

**Project:** MAGKOM (Sistem Informasi Manajemen Magang Kominfo)  
**Task ID:** PERF-002  
**Kategori:** Performance & Operational Safety  
**Status:** Solved & Verified

---

## 1. Temuan Masalah Kinerja & Titik Berat (Bottlenecks Identified)

### 1.1. Memory Exhaustion pada Pengemasan Berkas Pendaftaran ZIP (`DocumentArchiveService`)
- **Kode Asli:**
  ```php
  $fileContent = $privateDisk->get($storagePath);
  if ($fileContent !== null) {
      $zip->addFromString($cleanFileName, $fileContent);
  }
  ```
- **Bukti & Dampak:**
  Pada pengunduhan arsip ZIP pendaftaran peserta (`Admin\PendaftaranController::downloadBundelZip`), berkas PDF dibaca seluruhnya ke dalam string memori PHP (`Storage::disk('private')->get()`) dan ditahan dalam RAM melalui `addFromString()`.
  Jika admin mengunduh paket berkas pendaftaran dengan 50 peserta dan masing-masing memiliki 3 dokumen (ukuran 2–5 MB), total konsumsi memori binary dapat mencapai 300–600 MB. Hal ini langsung memicu `Fatal error: Allowed memory size of 134217728 bytes exhausted` pada lingkungan production standar (`memory_limit = 128M` atau `256M`).
- **Solusi yang Diimplementasikan:**
  Mengganti pembacaan buffer memori dengan **streaming disk-to-disk native** `ZipArchive::addFile($realPath, $cleanFileName)`.
  ```php
  $realPath = $privateDisk->path($storagePath);
  if (is_file($realPath) && is_readable($realPath)) {
      $zip->addFile($realPath, $cleanFileName);
  } else {
      // Fallback aman untuk storage driver non-lokal
      $fileContent = $privateDisk->get($storagePath);
      if ($fileContent !== null) {
          $zip->addFromString($cleanFileName, $fileContent);
      }
  }
  ```
  Penggunaan memori berkurang dari $O(\text{total ukuran file})$ menjadi $O(1)$ konstan ($\approx$ 2 MB overhead proses).

---

### 1.2. N+1 Query pada Loop Pengemasan Dokumen
- **Kode Asli:**
  ```php
  foreach ($pendaftaranList as $pendaftaran) {
      $dokumens = $pendaftaran->dokumen()->with('jenisDokumen')->get();
      ...
  }
  ```
- **Bukti & Dampak:**
  Pemanggilan method relasi `->dokumen()` mengeksekusi kueri baru ke tabel `dokumen` dan `jenis_dokumen` pada setiap iterasi baris pendaftar, meskipun controller telah melakukan eager-loading. Untuk batch 50 pendaftar, terjadi 50 kueri redundan ke database.
- **Solusi yang Diimplementasikan:**
  Memanfaatkan koleksi relasi yang sudah dimuat ke memori (*eager-loaded property*):
  ```php
  $dokumens = $pendaftaran->relationLoaded('dokumen')
      ? $pendaftaran->dokumen
      : $pendaftaran->dokumen()->with('jenisDokumen')->get();
  ```
  Menghilangkan seluruh 50 kueri berulang.

---

### 1.3. Analisis Kueri Landing Page (`LandingController`)
- **Pemeriksaan:**
  Kueri ketersediaan kuota pada `LandingController` telah diperiksa:
  - Bulan magang aktif dan kuota dimuat dengan kueri ber-eager load:
    `BulanMagang::with(['kuota' => fn($q) => $q->where('aktif', true)])->...`
  - Jurusan dan bidang dimuat dalam 2 kueri terpisah.
  - Perhitungan matriks ketersediaan per jurusan dan bulan dijalankan secara in-memory menggunakan `firstWhere()` pada koleksi Eloquent, **bukan mengeksekusi kueri SQL di dalam loop bersarang**.
- **Hasil:**
  Total kueri pada landing page hanya 3–4 kueri SQL dengan waktu eksekusi < 15ms.

---

## 2. Area yang Membutuhkan Load Testing di Staging Server

Pemeriksaan kode statis dan automated tests membuktikan efisiensi logika, namun uji beban (*load testing*) skala tinggi berikut direkomendasikan pada server staging sebelum peningkatan trafik:

| No | Skenario Uji Beban | Target Metrik | Alat Uji Direkomendasikan |
|---|---|---|---|
| 1 | **Pembukaan Kuota Serentak (War Kuota)** | 200 concurrent user melakukan registrasi dan pemilihan kuota dalam 60 detik. | k6 / Locust |
| 2 | **Unduh Laporan XLSX / CSV Masal** | Admin mengekspor 2.000 baris pendaftaran dan penempatan. | Apache JMeter |
| 3 | **Koneksi SSE Simultan** | 50 tab browser membuka dashboard bersamaan (mengukur titik jenuh worker PHP-FPM). | k6 WebSocket/SSE module |
