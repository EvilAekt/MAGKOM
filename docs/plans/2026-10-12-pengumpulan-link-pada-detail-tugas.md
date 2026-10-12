# Rencana Implementasi Fitur Pengumpulan Tautan (Link) pada Detail Tugas

## 1. Tujuan
Memungkinkan peserta magang mengumpulkan tautan/URL (seperti Google Drive, GitHub repository, Figma mockup, atau tautan demo aplikasi) sebagai bukti atau hasil pengerjaan pada halaman detail tugas (`resources/views/peserta/tugas/show.blade.php`), serta menampilkan tautan tersebut secara interaktif di riwayat progres peserta maupun di halaman reviu pembimbing.

---

## 2. Asumsi & Analisis Sistem

### Kondisi Saat Ini:
1. Formulir serahkan progres tugas peserta (`#formProgresTugas`) saat ini hanya menyediakan input berkas file (`file`) dan catatan (`catatan`).
2. Tabel database `progres_log` saat ini memiliki kolom: `id`, `tugas_id`, `catatan`, `file`, `tgl_lapor`, `status_review`, `feedback`, `timestamps`. Kolom untuk menyimpan tautan (`link`) belum tersedia.
3. Di sisi pembimbing (`resources/views/pembimbing/tugas/show.blade.php`), reviu tugas baru memfasilitasi unduhan berkas fisik, belum menampilkan tautan eksternal dari mentee.

### Asumsi Teknis:
- Kolom baru `link` bertipe `string(2048)->nullable()` pada tabel `progres_log`.
- Format input tautan opsional, divalidasi dengan format URL valid (`https://` atau `http://`).
- Pengumpulan progres bersifat fleksibel: peserta dapat mengumpulkan **file saja**, **link saja**, atau **file + link bersamaan**, dengan tetap menyertakan catatan ringkasan pengerjaan.

---

## 3. Evaluasi Opsi Pendekatan Desain

| Kriteria | Opsi A: Gabung Link ke dalam Catatan Teks | Opsi B (Direkomendasikan): Kolom Dedicated `link` dengan Dedicated UI Input |
| :--- | :--- | :--- |
| **Konsep** | Peserta diminta menempelkan link secara manual di dalam textarea catatan. | Menyediakan input khusus `Tautan Tugas / Repositori / Demo` di form dan menyimpan ke kolom `link` di database. |
| **Kelebihan** | Tidak butuh migrasi database. | Tautan terstruktur, dapat divalidasi keabsahan format URL-nya, dan ditampilkan sebagai tombol interaktif (*clickable badge*) yang profesional. |
| **Kekurangan** | Link rawan typo, tidak bisa divalidasi otomatis, dan tampilan timeline tidak rapi. | Memerlukan 1 file migrasi baru untuk menambah kolom `link`. |
| **Pengalaman Pengguna (UX)** | Rentan terlewat oleh pembimbing. | **Sangat intuitif (seperti Google Classroom / Canvas LMS).** |

> **Rekomendasi**: **Opsi B (Dedicated Column & UI)** untuk menjaga standar kualitas dan konsistensi platform MagKom.

---

## 4. Rincian Desain & Penempatan Elemen UI

### A. Panel Formulir Penyerahan Progres (Sisi Peserta):
Di dalam `#formProgresTugas` pada `resources/views/peserta/tugas/show.blade.php`:
1. **Lampiran Berkas (PDF/ZIP/DOCX)**: Box upload berkas (opsional).
2. **Tautan Berkas / Repositori / Demo (Baru)**:
   - Label: `Tautan Tugas / Repositori / Demo` (Opsional, `https://`).
   - Input: Text/URL field dengan leading icon tautan/link (`svg`), placeholder: `Contoh: https://github.com/... atau https://drive.google.com/...`.
   - Bantuan: *"Gunakan tautan Google Drive/Docs, GitHub, atau Figma jika hasil kerja berupa berkas cloud atau kode sumber."*
3. **Catatan Progres Pekerjaan**: Textarea rangkuman hasil kerja (wajib, min. 5-10 karakter).
4. **Tombol "Serahkan / Kirim Progres"**.

### B. Tampilan Riwayat Progres (Sisi Peserta & Pembimbing):
- Jika entri progres memiliki `link`:
  - Ditampilkan tombol/chip tautan berwarna biru:
    `🔗 Buka Tautan Tugas ↗`
  - Membuka tab baru (`target="_blank" rel="noopener noreferrer"`).
- Jika ada berkas `file`:
  - Tetap menampilkan tombol unduh lampiran berkas seperti biasa.

---

## 5. Rencana Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `database/migrations/YYYY_MM_DD_HHMMSS_add_link_to_progres_log_table.php` | Buat | Migrasi penambahan kolom `link` (`string(2048)->nullable()`) pada tabel `progres_log`. |
| `app/Models/ProgresLog.php` | Ubah | Menambahkan `link` ke properti `$fillable`. |
| `app/Http/Requests/ProgresLogRequest.php` | Ubah | Menambahkan aturan validasi `'link' => ['nullable', 'url', 'max:2048']`. |
| `app/Http/Controllers/Peserta/TugasController.php` | Ubah | Menyimpan `link` dari request yang telah tervalidasi ke dalam record `ProgresLog::create()`. |
| `resources/views/peserta/tugas/show.blade.php` | Ubah | 1. Menambahkan field input `link` pada formulir penyerahan tugas.<br>2. Menampilkan tombol tautan pada riwayat progres timeline peserta. |
| `resources/views/pembimbing/tugas/show.blade.php` | Ubah | Menampilkan chip tautan tugas peserta pada kartu reviu evaluasi pembimbing. |
| `tests/Feature/PesertaClassroomTugasLogbookTest.php` | Ubah | Menambahkan uji pengiriman progres tugas dengan lampiran link. |
| `docs/plans/2026-10-12-pengumpulan-link-pada-detail-tugas.md` | Buat | Dokumen rencana kerja master. |

---

## 6. Langkah Implementasi Terurut

- [x] **Langkah 1**: Buat file migrasi `add_link_to_progres_log_table` dan jalankan `php artisan migrate` menggunakan PHP 8.2 XAMPP.
- [x] **Langkah 2**: Perbarui model `App\Models\ProgresLog` dengan menambahkan `'link'` ke array `$fillable`.
- [x] **Langkah 3**: Perbarui `App\Http\Requests\ProgresLogRequest` dengan rule validasi `'link' => ['nullable', 'url', 'max:2048']`.
- [x] **Langkah 4**: Perbarui method `unggahProgres` di `App\Http\Controllers\Peserta\TugasController` untuk menyimpan nilai `link`.
- [x] **Langkah 5**: Perbarui formulir penyerahan di `resources/views/peserta/tugas/show.blade.php` dengan menambahkan field input tautan dan menampilkan chip link pada timeline riwayat progres.
- [x] **Langkah 6**: Perbarui tampilan reviu progres di `resources/views/pembimbing/tugas/show.blade.php` agar pembimbing dapat langsung mengklik tautan tugas peserta.
- [x] **Langkah 7**: Jalankan automated test suite `tests/Feature/PesertaClassroomTugasLogbookTest.php` dan `tests/Feature/TugasLogbookTest.php` untuk memastikan regresi aman dan fitur baru lulus 100%.

---

## 7. Strategi Testing
1. **Automated Feature Tests**:
   - Uji peserta mengirimkan progres hanya dengan link (tanpa file).
   - Uji peserta mengirimkan progres dengan file + link sekaligus.
   - Uji validasi URL tidak valid (misal: string asal-asalan ditolak).
2. **Manual Verifikasi UI**:
   - Buka halaman tugas peserta, isi URL Google Drive/GitHub, serahkan.
   - Pastikan link muncul di riwayat progres peserta dan dapat diklik.
   - Buka portal pembimbing pada tugas yang sama, pastikan link tampil dan dapat dibuka oleh pembimbing.

---

## 8. Risiko & Mitigasi
- **Risiko**: URL berbahaya (XSS / JavaScript URI).
   - **Mitigasi**: Validasi aturan `'url'` di Laravel FormRequest dan gunakan sanitasi Blade bawaan `{{ $progres->link }}` pada atribut `href` dengan prefix `https://` / `http://`.
- **Risiko**: Link sangat panjang (> 255 karakter).
   - **Mitigasi**: Tipe kolom di database menggunakan panjang hingga 2048 karakter.

---

## 9. Kriteria Selesai (Definition of Done)
- [x] Tersedia input field tautan/link di panel penyerahan tugas detail peserta.
- [x] Peserta dapat mengumpulkan link tugas dengan sukses.
- [x] Link tersimpan di database `progres_log.link`.
- [x] Link tampil sebagai tombol/chip yang dapat diklik di riwayat progres peserta.
- [x] Link tampil dan dapat diklik oleh pembimbing pada halaman reviu tugas pembimbing.
- [x] Seluruh automated test terkait tugas & logbook lulus 100%.
