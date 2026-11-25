# Rencana Kerja: Penyederhanaan Hero Banner (Tombol Kembali Saja) & Mode Kelola / Ganti / Hapus Surat Selesai Setelah Terbit

**Tanggal:** 2026-11-25  
**Target Branch:** `test-2`  
**Cakupan:**
1. Hero Banner konteks Surat Selesai pada `resources/views/admin/penempatan/show.blade.php`
2. State tampilan setelah surat terbit (`$hasSurat === true`) + fitur **Ganti Berkas** & **Hapus Berkas** pada `_show_kelompok.blade.php` dan Mode Fokus Individu di `admin/surat-selesai/index.blade.php`

---

## 1. Tujuan
1. **Menyederhanakan Hero Banner Konteks Surat Selesai (`show.blade.php`):** Menghapus tombol `Pratinjau Surat` dan `Unduh Surat Diterima` saat halaman diakses dari konteks Surat Selesai (`$isFromSuratSelesai`), sehingga hanya menyisakan tombol **Kembali** (`Kembali ke Surat Selesai Tim` / `Kembali ke Surat Selesai Peserta` / `Kembali ke Surat Selesai`).
2. **Memisahkan Tampilan Kondisi Awal vs Kondisi Sudah Terbit:**
   - **Saat belum ada surat (`!$hasSurat`):** Menampilkan form dropzone penuh (`Formulir Unggah Surat Selesai`).
   - **Saat surat sudah terbit (`$hasSurat`):** Dropzone tidak langsung ditampilkan secara default; sebagai gantinya tampil card ringkasan status terbit (informasi berkas terbit, tanggal unggah, tombol **Unduh Salinan PDF**) beserta tombol **Ganti Berkas** (membuka form unggah ulang secara tersembunyi/toggle saat diklik) dan tombol **Hapus Berkas** (menghapus surat yang salah unggah dari storage & database sehingga kembali ke tampilan dropzone awal).

---

## 2. Asumsi & Pertanyaan Terbuka
- **Asumsi Kerja Logis:**
  1. Pada `admin/penempatan/show.blade.php`, tombol `Pratinjau Surat` dan `Unduh Surat Diterima` hanya dihapus pada blok `@if($isFromSuratSelesai)` (konteks Surat Selesai). Pada tampilan standar Penempatan (`@else`), tombol SK Penempatan tidak diubah.
  2. Penghapusan surat selesai (`DELETE admin.penempatan.surat-selesai.hapus`) menggunakan otorisasi yang sama dengan unggah (`$this->authorize('manage', Penempatan::class)`), sehingga SuperAdmin, Pembimbing, dan Peserta tetap ditolak (`403 Forbidden`).
  3. Jika penempatan bertipe **kelompok**, aksi **Hapus Berkas** menghapus `file_surat_selesai` & `tgl_upload_surat_selesai` pada seluruh anggota kelompok secara atomik dalam `DB::transaction`, menghapus file fisik di `Storage::disk('local')` bila tidak dipakai penempatan lain, serta mencatat jejak audit (`aksi: 'hapus_surat_selesai'`).
  4. **Persetujuan Perubahan Blade & Route:** Sesuai aturan proyek (`.agents/rules/AGENTS.md` bagian 3.3 & 7), perubahan markup di Blade dan penambahan route `DELETE` memerlukan persetujuan eksplisit pengguna sebelum eksekusi.

---

## 3. Pendekatan Teknis

### Perbandingan Opsi Interaksi "Ganti Berkas" & "Hapus Berkas"
1. **Opsi A (Direkomendasikan): Panel State Terbit + Toggle Form Ganti Berkas (Vanilla ES Module) + Form `DELETE` Hapus Berkas**
   - Saat `$hasSurat` bernilai `true`:
     - Tampilkan blok ringkasan `.published-summary-panel` (status hijau `Surat Resmi Telah Terbit`, timestamp unggah, tombol `Unduh Salinan PDF`), tombol `Ganti Berkas` (`data-action="toggle-replace-surat"`), dan tombol `Hapus Berkas` (form `DELETE` dengan konfirmasi aman).
     - Form dropzone (`Formulir Ganti Berkas Surat`) berada di dalam container `.replace-upload-panel` yang tersembunyi secara default (`hidden` / class `.is-open`), dan hanya muncul ketika admin menekan `Ganti Berkas` (ditangani di `resources/js/pages/admin/penempatan-show.js` dan `resources/js/pages/admin/surat-selesai-index.js`).
   - **Kelebihan:** Mengikuti 100% aturan proyek (tanpa inline JS/CSS di Blade), tampilan setelah terbit langsung terlihat berbeda dan rapi, namun admin tetap bisa mengganti berkas atau menghapus berkas yang salah unggah dalam 1 klik.
   - **Kekurangan:** Menambah 1 route `DELETE` dan 1 method service `hapusSuratSelesai()`.

2. **Opsi B: Menghapus Total Form Ganti Berkas dan Memaksa Admin Selalu Hapus Dulu Sebelum Unggah Ulang**
   - **Kelebihan:** Tidak perlu toggle JS.
   - **Kekurangan:** Kurang praktis jika admin hanya ingin langsung menimpa PDF tanpa menghapus terlebih dahulu.

**Keputusan:** Memilih **Opsi A** karena memenuhi kebutuhan *"bisa ganti yang diupload"* sekaligus *"bisa hapus yang udah diupload"* dengan tampilan awal & tampilan sesudah upload yang jelas berbeda.

---

## 4. Perubahan File

| File | Aksi (`Create`/`Modify`/`Delete`) | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `routes/web.php` | Modify | Menambahkan route `DELETE /penempatan/{penempatan}/surat-selesai` dengan nama `penempatan.surat-selesai.hapus`. |
| `app/Services/Admin/Penempatan/SuratSelesaiService.php` | Modify | Menambahkan method `hapusSuratSelesai(Penempatan $penempatan, User $admin): array` dengan `DB::transaction`, sinkronisasi kelompok, orphan file cleanup, dan `AuditService`. |
| `app/Http/Controllers/Admin/PenempatanController.php` | Modify | Menambahkan method `hapusSuratSelesai(Penempatan $penempatan, Request $request): RedirectResponse` dengan `$this->authorize('manage', Penempatan::class)`. |
| `resources/views/admin/penempatan/show.blade.php` | Modify | Menghapus tombol `Pratinjau Surat` dan `Unduh Surat Diterima` pada hero banner `@if($isFromSuratSelesai)` sehingga hanya tersisa tombol kembali. |
| `resources/views/admin/penempatan/partials/_show_kelompok.blade.php` | Modify | Memisahkan tampilan `!$hasSurat` (dropzone awal) vs `$hasSurat` (panel berkas terbit + tombol `Unduh`, `Ganti Berkas`, `Hapus Berkas`, dan form ganti tersembunyi). |
| `resources/views/admin/surat-selesai/index.blade.php` | Modify | Menerapkan pola yang sama pada Mode Fokus Individu (`masuk_individu`) agar konsisten antara individu dan kelompok. |
| `resources/css/pages/admin/surat-selesai-index.css` | Modify | Menambahkan style `.published-summary-panel`, `.published-actions-row`, `.btn-replace-surat`, `.btn-delete-surat`, dan `.replace-upload-panel`. |
| `resources/js/pages/admin/penempatan-show.js` & `resources/js/pages/admin/surat-selesai-index.js` | Modify | Menambahkan event listener `data-action="toggle-replace-surat"` (buka/tutup panel ganti berkas) dan konfirmasi sebelum submit hapus berkas. |
| `tests/Feature/AdminSuratSelesaiIndividuKelompokTest.php` & `tests/Feature/AdminSuratSelesaiKonektivitasEndToEndTest.php` | Modify | Menambahkan test hero banner hanya tombol kembali, tampilan state terbit (panel terbit + `Ganti Berkas` + `Hapus Berkas`), serta skenario `DELETE` hapus berkas (individu, kelompok sync, orphan cleanup, dan proteksi RBAC 403). |

---

## 5. Langkah Implementasi

- [x] **Persetujuan Perubahan Blade & Route**: Memastikan persetujuan eksplisit user atas perubahan Blade dan penambahan route `DELETE`.
- [x] **Backend (`SuratSelesaiService`, `PenempatanController`, `routes/web.php`)**:
  - Tambahkan `hapusSuratSelesai()` di `SuratSelesaiService` (transaction + group sync + orphan cleanup + audit log).
  - Tambahkan `hapusSuratSelesai()` di `PenempatanController` dengan `$this->authorize('manage', Penempatan::class)`.
  - Daftarkan route `DELETE /penempatan/{penempatan}/surat-selesai` (`admin.penempatan.surat-selesai.hapus`).
- [x] **View (`show.blade.php`, `_show_kelompok.blade.php`, `surat-selesai/index.blade.php`)**:
  - Hapus `Pratinjau Surat` dan `Unduh Surat Diterima` di `@if($isFromSuratSelesai)` pada `show.blade.php`.
  - Pisahkan state `!$hasSurat` (dropzone awal) dan `$hasSurat` (panel ringkasan terbit + `Ganti Berkas` + `Hapus Berkas` + form ganti tersembunyi) di `_show_kelompok.blade.php` dan Mode Fokus Individu di `surat-selesai/index.blade.php`.
- [x] **CSS & JS (`surat-selesai-index.css`, `penempatan-show.js`, `surat-selesai-index.js`)**:
  - Tambahkan styling panel terbit & tombol aksi di `surat-selesai-index.css`.
  - Tambahkan toggle `Ganti Berkas` dan konfirmasi hapus berkas di `penempatan-show.js` & `surat-selesai-index.js`.
- [x] **Feature Test & Quality Gate**:
  - Tambahkan test lengkap untuk hero banner, state `$hasSurat`, dan `DELETE` hapus surat selesai.
  - Jalankan `npm run build`, `php artisan test --filter=AdminSuratSelesai`, dan `./vendor/bin/pint --test`.

---

## 6. Strategi Testing
- **Feature Test (`php artisan test --filter=AdminSuratSelesai`):**
  - *Hero Banner:* Halaman `admin.penempatan.show` dengan `ref=surat-selesai` tidak menampilkan `Pratinjau Surat` dan `Unduh Surat Diterima`, hanya tombol kembali.
  - *State Terbit:* Setelah unggah surat selesai, tampilan menampilkan panel terbit (`Ganti Berkas`, `Hapus Berkas`) dan menyembunyikan dropzone awal.
  - *Hapus Berkas (`DELETE`):* Menghapus berkas mengosongkan `file_surat_selesai` pada peserta (dan seluruh rekan tim jika kelompok), menghapus file fisik di storage, dan mengembalikan tampilan ke dropzone awal.
  - *RBAC (Sad Path):* SuperAdmin, Pembimbing, dan Peserta mendapat `403 Forbidden` saat mencoba `DELETE` surat selesai.

---

## 7. Risiko & Mitigasi
- **Risiko 1 — Penghapusan Berkas Kelompok Secara Tidak Sengaja:**  
  *Mitigasi:* Menambahkan konfirmasi sebelum submit `DELETE` dan mencatat audit log (`hapus_surat_selesai`) sehingga riwayat tetap terlacak.
- **Risiko 2 — Orphan File atau File Dipakai Penempatan Lain:**  
  *Mitigasi:* Menggunakan pengecekan `Penempatan::where('file_surat_selesai', $oldPath)->whereNotIn('id', $targetIds)->exists()` sebelum `Storage::disk('local')->delete()`.

---

## 8. Kriteria Selesai (Definition of Done)
- [x] Pada `show.blade.php` konteks Surat Selesai, tombol `Pratinjau Surat` dan `Unduh Surat Diterima` terhapus dan hanya tersisa tombol kembali.
- [x] Dropzone upload awal hanya tampil saat surat belum diunggah (`!$hasSurat`).
- [x] Saat surat sudah terbit (`$hasSurat`), tampil ringkasan berkas terbit dengan aksi **Unduh**, **Ganti Berkas**, dan **Hapus Berkas**.
- [x] `npm run build`, `php artisan test --filter=AdminSuratSelesai`, dan `./vendor/bin/pint --test` seluruhnya lulus.
