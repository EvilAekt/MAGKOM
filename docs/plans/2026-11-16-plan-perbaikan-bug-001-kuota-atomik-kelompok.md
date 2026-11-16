# Rencana Kerja: Perbaikan & Hardening BUG-001 (Alokasi Kuota Atomik Pendaftaran Kelompok)

Dokumen ini merinci rencana teknis perbaikan dan hardening menyeluruh untuk mengatasi **TEMUAN BUG-001: Kuota Atomik Pendaftaran Kelompok Mengalami Under-Counting** yang teridentifikasi dalam laporan audit sistem (*Audit F-11, F-23, F-37, PRD BR-18 & AC-03A-6*).

---

## 1. Latar Belakang & Status Kode Eksisting

### Ringkasan Masalah (BUG-001):
- **Fitur Terkait**: F-11 (Registrasi Kelompok), F-23 (Pendaftaran Kelompok), F-37 (Keputusan Seleksi Administratif)
- **Kondisi yang Diharapkan (PRD BR-18 & AC-03A-6)**:
  - Saat berkas pendaftaran kelompok (1 ketua + hingga 3 anggota, total $N$ orang) diputuskan **Diterima** oleh Admin, kuota umum harus dipotong sejumlah total anggota kelompok secara atomik (`terisi += count(anggota) + 1`).
  - Jika sisa kuota yang tersedia $< N$, keputusan penerimaan harus ditolak seketika dengan melempar `KuotaPenuhException` dan seluruh transaksi database di-rollback tanpa sisa.
  - Saat penempatan dibatalkan sebelum tanggal mulai magang (`CancelPenempatanAction`), kuota yang dikembalikan ke sistem juga harus sebesar $N$.
- **Kondisi Awal (Sebelum Diperbaiki)**:
  - `ambilKuotaUmum` dan `lepasKuotaUmum` pada `QuotaAllocationService` hanya menerima `$kuotaId` tanpa parameter jumlah alokasi kuota, sehingga secara mutlak hanya melakukan `increment` dan `decrement` sebesar 1.
  - Rombongan 4 orang hanya mengurangi kuota sebesar 1, menyebabkan potensi over-quota fisik tersembunyi (*invisible capacity overrun*).
- **Status Audit Terkini di Repositori**:
  - Implementasi dasar penambahan parameter `$jumlah` pada `QuotaAllocationService`, `PlacementService::terimaBerkas`, dan `CancelPenempatanAction` telah sempat masuk dalam commit pembaruan sebelumnya.
  - Namun, dokumen rencana kerja resmi ini dibutuhkan untuk memetakan invariant defensif tambahan (seperti validasi `$jumlah <= 0` pada `ambilKuotaUmum`), memverifikasi seluruh kemungkinan *edge cases*, serta mengonsolidasikan test suite pembuktian (*proof-of-fix*) sesuai standar audit MAGKOM.

---

## 2. Tujuan
1. **Presisi Akuntansi Kuota 100%**: Menjamin pemotongan dan pengembalian kuota pendaftaran kelompok selalu setara dengan jumlah fisik peserta ($N = 1 \text{ ketua} + \text{anggota}$).
2. **Integritas Transaksi Atomik (Zero Race-Condition)**: Memastikan pengecekan sisa kuota dan pemotongan dilakukan di dalam blok `DB::transaction()` dengan *pessimistic row locking* (`lockForUpdate()`), sehingga jika sisa kuota tidak mencukupi, transaksi otomatis gagal (*atomic rejection*).
3. **Penyempurnaan Defensive Programming**:
   - Menambahkan guard invariant pada `ambilKuotaUmum(int $kuotaId, int $jumlah = 1)` agar menolak `$jumlah <= 0` dengan `InvalidArgumentException`.
   - Menjaga invariant pada `lepasKuotaUmum` agar tidak pernah menyebabkan nilai `terisi` menjadi negatif dan tetap mencatat `Log::warning` jika terdeteksi anomali akuntansi.
4. **Verifikasi Test Suite Komprehensif**: Memastikan seluruh 9 skenario pengujian pada `QuotaKelompokAllocationTest` dan pengujian regresi terkait berjalan hijau 100%.

---

## 3. Asumsi & Investigasi Alur Bisnis

### Asumsi Alur Pendaftaran & Kuota:
1. **Struktur Kelompok Magang**:
   - Pendaftaran kelompok diatur oleh model `KelompokMagang` (1 ketua memiliki akun `User`) dan tabel `kelompok_anggota` (data profil anggota tim 1–3 orang).
   - Total alokasi kuota fisik rombongan adalah:
     $$\text{Headcount} = 1 + \text{count(kelompok\_anggota)}$$
2. **Waktu Pemotongan Kuota (BR-18)**:
   - Kuota jurusan **TIDAK** dipotong saat draf pendaftaran pertama kali dibuat (`BerkasBelumLengkap`) atau saat diajukan (`Diajukan`).
   - Kuota jurusan baru dipotong secara definitif saat Admin memverifikasi dan menyetujui berkas menjadi `StatusPendaftaranEnum::Diterima` melalui `PlacementService::terimaBerkas`.
3. **Waktu Pengembalian Kuota (BR-07)**:
   - Jika penempatan dibatalkan **sebelum tanggal mulai** (`today < tgl_mulai`), kuota dikembalikan ke pool jurusan sejumlah $N$ orang.
   - Jika pembatalan terjadi **pada atau setelah tanggal mulai** (`today >= tgl_mulai`), kuota dianggap hangus dan **TIDAK** dikembalikan ke pool jurusan.

---

## 4. Evaluasi Pendekatan Teknis

| Parameter | Opsi 1 (Terpilih): Alokasi Atomik Tunggal Dinamis (`$jumlah`) | Opsi 2: Looping Pemanggilan Satuan ($N \times$) |
|---|---|---|
| **Mekanisme** | Menambahkan parameter `$jumlah` pada `ambilKuotaUmum($kuotaId, $jumlah)`. Satu query atomic increment `increment('terisi', $jumlah)` dengan pengecekan `(terisi + $jumlah) > jumlah`. | Menjalankan perulangan `for ($i = 0; $i < $jumlah; $i++)` yang memanggil `ambilKuotaUmum($kuotaId)` berulang kali. |
| **Kinerja Database** | **Tinggi (O(1))**: Hanya 1 round-trip query database per verifikasi kelompok. | **Rendah (O(N))**: Membebani database dengan N query increment dan N row-fetch. |
| **Keamanan Konkurensi** | **Sangat Aman**: Evaluasi sisa kapasitas dilakukan sekali secara menyeluruh di bawah baris `lockForUpdate()`. | **Rentan Cacat Parsial**: Jika kuota habis di tengah iterasi, penanganan rollback manual lebih kompleks. |
| **Kepatuhan Arsitektur** | Selaras dengan standar performa Laravel & PSR-12 di `AGENTS.md`. | Berpotensi memicu overhead lock transaksi berkepanjangan. |

> **Keputusan**: Menggunakan **Opsi 1**.

---

## 5. Ruang Lingkup Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| [`app/Services/QuotaAllocationService.php`](file:///d:/MAGKOM/MAGKOM/app/Services/QuotaAllocationService.php) | **Modify** | Tambahkan validasi invariant `if ($jumlah <= 0) throw new InvalidArgumentException(...)` pada `ambilKuotaUmum()`, pertahankan atomisitas `increment('terisi', $jumlah)` dan pengecekan kapasitas `($kuota->terisi + $jumlah) > $kuota->jumlah`. |
| [`app/Services/PlacementService.php`](file:///d:/MAGKOM/MAGKOM/app/Services/PlacementService.php) | **Modify / Verify** | Pastikan method `terimaBerkas()` secara konsisten menghitung `1 + $p->kelompokMagang->anggota()->count()` dan memanggil `ambilKuotaUmum($p->kuota_id, $jumlahPeserta)`. |
| [`app/Actions/Admin/Penempatan/CancelPenempatanAction.php`](file:///d:/MAGKOM/MAGKOM/app/Actions/Admin/Penempatan/CancelPenempatanAction.php) | **Modify / Verify** | Pastikan method `execute()` menghitung headcount kelompok yang sama saat penempatan dibatalkan sebelum tanggal mulai dan memanggil `lepasKuotaUmum((int) $p->kuota_id, $jumlahPeserta)`. |
| [`tests/Feature/QuotaKelompokAllocationTest.php`](file:///d:/MAGKOM/MAGKOM/tests/Feature/QuotaKelompokAllocationTest.php) | **Modify** | Tambahkan unit assertion untuk invariant `$jumlah <= 0` pada `ambilKuotaUmum()` dan pertahankan seluruh 9 skenario pengujian kelompok. |

---

## 6. Langkah Implementasi Bertahap

- [x] **Langkah 1: Hardening Invariant di `app/Services/QuotaAllocationService.php`**
  - Pada `ambilKuotaUmum(int $kuotaId, int $jumlah = 1)`:
    - Menambahkan validasi defensif:
      ```php
      if ($jumlah <= 0) {
          throw new \InvalidArgumentException('Jumlah alokasi kuota harus lebih besar dari 0.');
      }
      ```
    - Menjaga kondisi kuota penuh tetap presisi:
      ```php
      if (! $kuota->aktif || ($kuota->terisi + $jumlah) > $kuota->jumlah) {
          throw new KuotaPenuhException('Kuota umum untuk jurusan dan periode ini sudah terisi penuh.');
      }
      ```
    - Mempertahankan `$kuota->increment('terisi', $jumlah)`.
- [x] **Langkah 2: Verifikasi Alur Verifikasi Berkas di `app/Services/PlacementService.php`**
  - Memastikan blok transaksi `terimaBerkas()` memuat kalkulasi:
    ```php
    $jumlahPeserta = 1;
    if ($p->kelompok_magang_id && $p->kelompokMagang) {
        $jumlahPeserta = 1 + $p->kelompokMagang->anggota()->count();
    }
    $this->quotaAllocationService->ambilKuotaUmum($p->kuota_id, $jumlahPeserta);
    ```
- [x] **Langkah 3: Verifikasi Alur Pembatalan Penempatan di `CancelPenempatanAction.php`**
  - Memastikan method `execute()` saat `$sebelumMulai == true` menghitung jumlah peserta kelompok dan memanggil:
    ```php
    $this->quotaAllocationService->lepasKuotaUmum((int) $p->kuota_id, $jumlahPeserta);
    ```
- [x] **Langkah 4: Penambahan & Eksekusi Test Suite di `tests/Feature/QuotaKelompokAllocationTest.php`**
  - Menambahkan test case `test_alokasi_kuota_dengan_jumlah_nol_atau_negatif_melempar_invalid_argument_exception()`.
  - Menjalankan seluruh test suite kelompok:
    ```bash
    php artisan test tests/Feature/QuotaKelompokAllocationTest.php
    ```
  - Menjalankan test suite konkurensi kuota:
    ```bash
    php artisan test tests/Feature/QuotaConcurrencyTest.php
    ```

---

## 7. Strategi Testing Komprehensif

| Skenario Pengujian | Input & Kondisi | Ekspektasi Hasil | Status |
|---|---|---|---|
| **1. Pendaftaran Individu** | Kuota: kapasitas 10, terisi 0. Pendaftaran mandiri (1 orang) diterima Admin. | Kuota `terisi` bertambah tepat 1 (`terisi == 1`). | **PASS** |
| **2. Pendaftaran Kelompok (2 Orang)** | Kuota: kapasitas 10, terisi 0. Pendaftaran kelompok (1 ketua + 1 anggota) diterima. | Kuota `terisi` bertambah tepat 2 (`terisi == 2`). | **PASS** |
| **3. Pendaftaran Kelompok (4 Orang)** | Kuota: kapasitas 10, terisi 0. Pendaftaran kelompok (1 ketua + 3 anggota) diterima. | Kuota `terisi` bertambah tepat 4 (`terisi == 4`). | **PASS** |
| **4. Penolakan Kuota Tidak Cukup** | Kuota: kapasitas 5, terisi 3 (sisa 2). Pendaftaran kelompok 3 orang diterima. | Melempar `KuotaPenuhException`, status pendaftaran tetap `Diajukan`, kuota `terisi` tetap 3 (rollback). | **PASS** |
| **5. Pembatalan Sebelum Mulai** | Penempatan kelompok 3 orang aktif, tanggal mulai di masa depan. Admin membatalkan. | Kuota dikembalikan 3 (`terisi` berkurang 3), pendaftaran berubah `Dibatalkan`. | **PASS** |
| **6. Pembatalan Setelah Mulai** | Penempatan kelompok 3 orang aktif, tanggal mulai sudah lewat/hari ini. Admin membatalkan. | Kuota **tidak** dikembalikan (hangus), akun peserta dinonaktifkan. | **PASS** |
| **7. Pencegahan Pembatalan Ganda** | Penempatan yang sudah dibatalkan mencoba dibatalkan kembali. | Melempar `RuntimeException` ('Hanya penempatan aktif...'). | **PASS** |
| **8. Defensive Guard Argumen <= 0 (Lepas)** | Memanggil `lepasKuotaUmum($id, 0)` atau `lepasKuotaUmum($id, -1)`. | Melempar `InvalidArgumentException`. | **PASS** |
| **9. Defensive Guard Argumen <= 0 (Ambil)** | Memanggil `ambilKuotaUmum($id, 0)` atau `ambilKuotaUmum($id, -1)`. | Melempar `InvalidArgumentException`. | **PASS** |
| **10. Anomali Disparitas Pelepasan** | Melepas kuota lebih besar dari jumlah `terisi` (misal lepas 5 saat terisi 2). | Nilai `terisi` dijaga tidak negatif (menjadi 0) dan mencatat `Log::warning`. | **PASS** |

---

## 8. Analisis Risiko & Mitigasi

| Risiko | Tingkat | Mitigasi |
|---|---|---|
| **Race Condition / Over-allocation saat konkurensi tinggi** | Sedang | Dilindungi penuh menggunakan `DB::transaction()` dan `lockForUpdate()` pada baris `kuota`. Database engine MySQL mengunci baris kuota hingga transaksi komit/rollback. |
| **Inkonsistensi jika relasi kelompok null** | Rendah | Menggunakan pengecekan ganda `$p->kelompok_magang_id && $p->kelompokMagang` dengan fallback aman ke `1`. |
| **Potensi Deadlock** | Rendah | Urutan penguncian baris (*locking order*) konsisten: Kunci `Pendaftaran` terlebih dahulu, baru kunci baris `Kuota`. |

---

## 9. Kriteria Selesai (Definition of Done)

- [x] Method `ambilKuotaUmum` dan `lepasKuotaUmum` pada `QuotaAllocationService` mendukung parameter kuota dinamis `$jumlah` dan memvalidasi batas `$jumlah > 0`.
- [x] `PlacementService::terimaBerkas` memotong kuota jurusan sejumlah total anggota kelompok ($N$) secara atomik.
- [x] `CancelPenempatanAction` mengembalikan kuota jurusan sejumlah $N$ saat pembatalan dilakukan sebelum tanggal mulai.
- [x] Seluruh 10 skenario pengujian pada `tests/Feature/QuotaKelompokAllocationTest.php` lulus hijau (`PASS`).
- [x] Pengujian konkurensi pada `tests/Feature/QuotaConcurrencyTest.php` lulus hijau (`PASS`).
- [x] Tidak ada regresi pada modul Admin penempatan maupun pendaftaran peserta.

