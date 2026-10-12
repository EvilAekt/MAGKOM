# Rencana Perbaikan Logika Validasi & Pencegahan Duplikasi Logbook Mingguan

## 1. Tujuan
Memperbaiki logika validasi dan penyajian data logbook mingguan pada portal peserta (`resources/views/peserta/logbook/index.blade.php`, `App\Http\Controllers\Peserta\LogbookController`, `App\Models\Logbook`, dan `App\Models\Penempatan`) sehingga:
1. Tidak ada entri logbook ganda untuk minggu yang sama (`minggu_ke`).
2. Peserta tidak dapat mengisi ulang minggu yang sudah dilaporkan (baik melalui UI modal maupun request submission).
3. Penomoran kartu logbook di riwayat tidak lagi tertukar atau terduplikasi akibat fallback sembarangan pada data lama.
4. Dropdown pilihan minggu di modal secara konsisten menonaktifkan (`disabled`) minggu yang sudah terisi dan memberikan notifikasi edukatif untuk mengedit entri yang sudah ada.

---

## 2. Asumsi & Analisis Akar Masalah (Root Cause)

### Temuan Investigasi:
1. **Penyebab Logbook Minggu ke-1 Muncul Dua Kali di Screenshot**:
   - Di database terdapat data lama (ID 7) dengan nilai `minggu_ke: null` bertanggal `2026-10-09` (sebenarnya jatuh pada Minggu ke-2 magang).
   - Di Blade `resources/views/peserta/logbook/index.blade.php` terdapat baris:
     ```php
     $mingguKeTampil = $log->minggu_ke ?? ($loop->count - $loop->index);
     ```
     Karena `minggu_ke` bernilai `null`, template menggunakan rumus `$loop->count - $loop->index`. Pada baris paling bawah, hasil perhitungannya adalah `4 - 3 = 1` (ditampilkan sebagai "Minggu ke-1").
   - Akibatnya, sebelum user mengisi ID 10, di layar sudah tampak tulisan "Logbook Minggu ke-1" (padahal itu data ID 7 dengan tanggal 9 Oktober).
   - Ketika user membuka modal tambah logbook, sistem memeriksa ke database `where('minggu_ke', 1)`. Karena belum ada satu pun record dengan kolom `minggu_ke = 1`, sistem mengizinkan pengisian Minggu ke-1 (ID 10 tersimpan dengan `minggu_ke = 1`).
   - Setelah ID 10 tersimpan, di layar muncullah DUA buah kartu yang sama-sama berlabel "Logbook Minggu ke-1" (ID 10 dari kolom asli, dan ID 7 dari fallback index loop).
2. **Kelemahan Validasi Duplikasi Minggu**:
   - Validasi saat ini di `LogbookController::store`:
     ```php
     $cekQuery = Logbook::where('penempatan_id', $penempatan->id)
         ->where(function ($q) use ($request, $mingguKe) {
             $q->whereDate('tanggal', $request->validated('tanggal'));
             if ($mingguKe) {
                 $q->orWhere('minggu_ke', $mingguKe);
             }
         });
     ```
     Jika record lama memiliki `minggu_ke: null`, query `orWhere('minggu_ke', $mingguKe)` tidak mendeteksi logbook lama yang sebenarnya mencakup minggu tersebut.
   - Di `LogbookRequest`, rule `minggu_ke` masih bertipe `nullable`, padahal sistem saat ini telah beralih ke jurnal mingguan terstruktur (`required`).
3. **Sinkronisasi Rentang Tanggal**:
   - Saat peserta memilih `Minggu ke-X`, tanggal pelaporan yang dipilih sebaiknya otomatis diselaraskan atau minimal divalidasi ke rentang minggu yang sesuai agar tidak ada tanggal di minggu 3 yang didaftarkan sebagai minggu 1.

---

## 3. Pendekatan Desain Solusi

| Kriteria | Opsi A: Hanya Tambahkan Validasi Controller | Opsi B (Direkomendasikan): Solusi Menyeluruh (Data Migration Backfill + Controller Strict Unique Validation + Model Accessor + UI Locking) |
| :--- | :--- | :--- |
| **Konsep** | Hanya mengecek `where('minggu_ke', $mingguKe)` di controller. | 1. Backfill otomatis record `logbook` yang `minggu_ke`-nya masih `null` berdasarkan selisih tanggal dengan `tgl_mulai`.<br>2. Jadikan `minggu_ke` wajib (`required`) dan validasi keunikan per `penempatan_id`.<br>3. Perbaiki model `Logbook` dan `Penempatan::getDaftarMingguMagang()` agar pemetaan 1-to-1 konsisten.<br>4. Hapus fallback liar `$loop->count - $loop->index` di Blade.<br>5. Proteksi UI modal: kunci form dan tampilkan pesan tegas jika minggu sudah terisi. |
| **Kelebihan** | Cepat ditulis. | **Menyelesaikan akar masalah data historis maupun alur baru secara permanen**, tidak ada lagi anomali duplikasi visual, dan integritas data terjaga. |
| **Kekurangan** | Data lama tetap cacat di tampilan dan sewaktu-waktu bisa muncul bug serupa. | Memerlukan penyesuaian di beberapa layer (Request, Controller, Model, View). |

> **Rekomendasi**: **Opsi B** untuk menyelesaikan masalah secara tuntas dan kokoh.

---

## 4. Rincian Perubahan Teknis

### A. Backfill & Normalisasi Data `minggu_ke`:
- Buat query satu kali / migration aman untuk mengisi record `logbook` yang `minggu_ke IS NULL` dihitung dari:
  `floor(datediff(tanggal, tgl_mulai) / 7) + 1`.
- Model `Logbook`: Tambahkan accessor `getMingguKeAttribute` yang menghitung minggu secara otomatis jika kolom bernilai null, sehingga `Logbook::minggu_ke` selalu mengembalikan integer minggu yang valid.

### B. Validasi Form Request (`LogbookRequest.php`):
- Ubah rule `minggu_ke`:
  `'minggu_ke' => ['required', 'integer', 'min:1', 'max:52']`.
- Validasi keunikan: Tambahkan pesan error yang jelas jika minggu tersebut sudah terisi untuk penempatan aktif peserta.

### C. Controller (`LogbookController.php`):
- Di method `store`:
  - Cek apakah `penempatan->logbooks()->where('minggu_ke', $mingguKe)->exists()`.
  - Jika ya: tolak langsung dengan flash message error:
    *"Catatan logbook untuk Minggu ke-{$mingguKe} sudah pernah dilaporkan. Silakan gunakan tombol Edit pada riwayat jika ingin memperbarui laporan."*
  - Sinkronkan `tanggal` agar otomatis menggunakan tanggal hari ini atau tanggal dalam rentang minggu terkait.

### D. Antarmuka Pengguna (`resources/views/peserta/logbook/index.blade.php`):
- **Kartu Riwayat Jurnal**:
  - Ganti `$mingguKeTampil = $log->minggu_ke ?? ($loop->count - $loop->index);` dengan `$mingguKeTampil = $log->minggu_ke;` (yang sudah selalu memiliki nilai pasti).
- **Modal Tambah Logbook**:
  - Pada `<select id="tambah_minggu_ke">`, opsi yang sudah pernah diisi memiliki atribut `disabled` dan penanda jelas: `(Sudah Dilaporkan)`.
  - Pada fungsi `onMingguChange(mingguVal)`:
    - Jika user memilih minggu yang sudah terisi:
      - Formulir isian (`#formFieldsLogbook`) disembunyikan.
      - Tampilkan box peringatan `#alertMingguSudahDiisi` yang ramah namun tegas.
      - Nonaktifkan tombol submit (`#btnSubmitTambah.disabled = true`).
    - Jika user memilih minggu yang belum terisi:
      - Tampilkan formulir isian `#formFieldsLogbook`.
      - Tombol submit aktif.
- **Banner Pengingat Mingguan**:
  - Jika minggu aktif sudah dilaporkan, tombol `+ Isi Logbook Minggu Ini` tidak muncul (banner berganti status atau tersembunyi).

---

## 5. Rencana Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `app/Models/Logbook.php` | Ubah | Tambahkan kalkulasi fallback cerdas pada accessor `getMingguKeAttribute` dan `getLabelMingguAttribute`. |
| `app/Models/Penempatan.php` | Ubah | Perbaiki pemetaan `getDaftarMingguMagang` agar memprioritaskan kolom `minggu_ke` dan tidak menimpa record logbook secara sembarangan. |
| `app/Http/Requests/LogbookRequest.php` | Ubah | Jadikan `minggu_ke` wajib (`required`, integer) dan tambahkan pesan validasi khusus. |
| `app/Http/Controllers/Peserta/LogbookController.php` | Ubah | Perketat validasi keunikan `minggu_ke` pada penempatan aktif saat `store`. |
| `resources/views/peserta/logbook/index.blade.php` | Ubah | 1. Hapus fallback acak `$loop->count - $loop->index` pada looping kartu.<br>2. Perbaiki logika pemilihan minggu dan disable submit jika minggu sudah terisi.<br>3. Rapikan data yang terduplikasi di lokal jika ada. |
| `tests/Feature/PesertaClassroomTugasLogbookTest.php` | Ubah/Tambah | Tambahkan unit/feature test: mencegah pengisian duplikat minggu yang sama. |
| `docs/plans/2026-10-12-perbaikan-logika-dan-validasi-logbook-mingguan.md` | Buat | Dokumen rencana master. |

---

## 6. Langkah Implementasi Terurut

- [x] **Langkah 1**: Normalisasi data di database lokal dengan skrip artisan aman: lakukan backfill kolom `minggu_ke` yang bernilai `null` pada tabel `logbook` berdasarkan `tgl_mulai` penempatan, serta rapikan duplikasi ID 10 dan ID 7 jika bertabrakan.
- [x] **Langkah 2**: Perbarui model `App\Models\Logbook` dengan accessor `getMingguKeAttribute` yang selalu mengembalikan nomor minggu yang valid.
- [x] **Langkah 3**: Perbarui method `getDaftarMingguMagang()` di `App\Models\Penempatan` agar pemetaan `logbookMap` menggunakan `minggu_ke` yang presisi.
- [x] **Langkah 4**: Perbarui `App\Http\Requests\LogbookRequest` agar `minggu_ke` wajib diisi (`required`, `integer`, `min:1`).
- [x] **Langkah 5**: Perbarui method `store` pada `App\Http\Controllers\Peserta\LogbookController` dengan validasi ketat keunikan `minggu_ke` per penempatan aktif.
- [x] **Langkah 6**: Perbarui `resources/views/peserta/logbook/index.blade.php`: hapus `$loop->count - $loop->index`, perbaiki handling `select` minggu di modal dan proteksi tombol submit saat minggu terisi dipilih.
- [x] **Langkah 7**: Jalankan automated test suite `PesertaClassroomTugasLogbookTest` dan `TugasLogbookTest` untuk memastikan seluruh skenario logbook lulus 100%.

---

## 7. Strategi Testing
1. **Automated Feature Tests**:
   - Uji peserta mengisi logbook minggu ke-1 -> berhasil.
   - Uji peserta mencoba mengisi kembali logbook minggu ke-1 -> ditolak dengan error validasi / flash error.
   - Uji peserta mengedit logbook minggu ke-1 yang sudah ada -> berhasil disimpan tanpa error duplikasi.
2. **Manual Visual QA**:
   - Buka `http://localhost:8000/peserta/logbook`.
   - Pastikan riwayat kartu menampilkan nomor minggu yang benar (Minggu ke-1, Minggu ke-2, Minggu ke-3, dst. secara berurutan dan unik).
   - Buka popup "+ Isi Logbook Mingguan", verifikasi opsi Minggu ke-1 berstatus `disabled (Sudah Diisi)`.
   - Pastikan tombol simpan terkunci jika minggu yang sudah terisi dipilih.

---

## 8. Risiko & Mitigasi
- **Risiko**: Data logbook lama yang dibuat tanpa `minggu_ke` menjadi tidak konsisten.
  - **Mitigasi**: Skrip backfill otomatis menghitung `minggu_ke` dari selisih `tgl_mulai` penempatan dengan `tanggal` logbook, sehingga setiap logbook lama mendapatkan nomor minggu yang akurat secara matematis.

---

## 9. Kriteria Selesai (Definition of Done)
- [x] Kolom `minggu_ke` selalu memiliki nilai integer valid pada setiap logbook.
- [x] Peserta dicegah mengisi ulang minggu yang sudah dilaporkan (baik di modal UI maupun di Controller).
- [x] Riwayat logbook tidak lagi menampilkan nomor minggu yang duplikat atau melompat.
- [x] Dropdown pemilihan minggu di modal secara akurat menonaktifkan minggu yang sudah terisi.
- [x] Seluruh automated feature tests terkait logbook lulus 100%.
