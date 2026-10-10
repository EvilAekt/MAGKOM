# Plan: Perbaikan In-App PDF Viewer (Resolusi Error Pratinjau Berkas Langsung di Website)

- **Tanggal**: 2026-10-10
- **Status**: Draft (Menunggu Persetujuan)
- **Author**: Antigravity (web-planning)

---

## 1. Tujuan
Menyelesaikan masalah kegagalan pratinjau dokumen PDF pada modal in-app di halaman detail pendaftaran peserta (`/peserta/pendaftaran/2`) yang menampilkan pesan:
> *"Tidak Dapat Memuat Pratinjau Langsung - Format berkas atau konfigurasi browser tidak mendukung pratinjau inline. Silakan gunakan tombol unduh di bawah ini."*

Hasil akhir yang diharapkan:
- Seluruh berkas persyaratan magang berformat PDF (Curriculum Vitae, Surat Pengantar, Transkrip, dll.) serta Surat Keterangan Diterima dapat **langsung dibaca dan ditampilkan secara penuh di dalam modal pop-up website** tanpa memicu kotak error fallback.
- Menghilangkan konflik origin host (`localhost:8000` vs `127.0.0.1:8000`) dan mematuhi kebijakan keamanan `X-Frame-Options: SAMEORIGIN`.
- Menjaga kepatuhan otorisasi privat, keamanan signed URL, dan kompatibilitas peramban modern (Chrome, Edge, Firefox, Safari).

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi:
1. Pengguna saat ini mengakses aplikasi web di peramban Chrome/Chromium melalui alamat `http://127.0.0.1:8000/peserta/pendaftaran/2`.
2. Berkas fisik PDF di server (`storage/app/private/pendaftaran/2/`) telah berupa dokumen PDF valid (spesifikasi PDF-1.7 hasil generate sistem resmi).
3. Browser desktop modern yang digunakan pengguna mendukung fitur internal native PDF renderer (`application/pdf`) di dalam elemen `<iframe>`.

### Pertanyaan Terbuka:
- Tidak ada pertanyaan terbuka yang menghambat. Akar penyebab kegagalan telah teridentifikasi secara presisi melalui penelusuran arsitektur middleware keamanan dan lifecycle DOM event `<iframe>`.

---

## 3. Pendekatan Desain & Arsitektur

### Analisis Akar Masalah (Root Cause):
1. **Konflik Host Signature & `X-Frame-Options: SAMEORIGIN`**:
   - File konfigurasi `.env` memiliki `APP_URL=http://localhost:8000`.
   - Browser pengguna aktif mengakses website di `http://127.0.0.1:8000`.
   - Method `Dokumen::getSignedPreviewUrl()` menghasilkan URL absolut: `http://localhost:8000/berkas/1/pratinjau?expires=...&signature=...`.
   - Ketika elemen `<iframe>` di halaman `http://127.0.0.1:8000` mencoba memuat `http://localhost:8000/...`, browser menganggap keduanya adalah **Origin yang Berbeda** (*Cross-Origin*).
   - Middleware `App\Http\Middleware\SecurityHeaders` menetapkan header `X-Frame-Options: SAMEORIGIN`.
   - Akibatnya, peramban memblokir rendering dokumen di dalam `<iframe>` (*Refused to display in a frame because it set X-Frame-Options to sameorigin*).
2. **Pemicu Eager Handler `onerror` pada `<iframe>`**:
   - Elemen `<iframe id="previewIframe" onload="handlePreviewLoaded()" onerror="handlePreviewError()">` dipasangi event listener `onerror`.
   - Pada baris 891 JavaScript, inisialisasi awal modal melakukan `iframe.src = ''`. Di browser berbasis Chromium, assigning string kosong atau kegagalan cross-origin langsung menembakkan event `error` pada iframe.
   - Fungsi `handlePreviewError()` segera dieksekusi: menyembunyikan iframe (`iframe.style.display = 'none'`) dan memunculkan kotak kegagalan `#previewError` (*display: flex*).

### Opsi Solusi yang Dievaluasi:

| Kriteria | Opsi 1: Relative Signed Routes + Pembersihan Lifecycle Iframe (Direkomendasikan) | Opsi 2: Integrasi PDF.js (Mozilla Viewer) Eksternal |
| :--- | :--- | :--- |
| **Penyelesaian Akar Masalah** | **Tuntas**: Menyelesaikan mismatch origin, menghilangkan blokir `X-Frame-Options`, dan meniadakan false-positive `onerror`. | Menambah layer dependensi JavaScript besar tanpa menyelesaikan problem `X-Frame-Options` jika URL tetap cross-origin. |
| **Performa & Bundle Size** | **0 KB Overhead**: Memanfaatkan native hardware-accelerated PDF engine browser tanpa script eksternal. | Berat (menambah ~2 MB script JavaScript & worker PDF.js). |
| **Kepatuhan Security & PRD** | Mematuhi PRD 8.4, TDD 5.6, dan standar Laravel `signed:relative` secara elegan. | Memerlukan fetch blob manual dan konfigurasi worker origin. |
| **Kompatibilitas** | Kompatibel penuh dengan seluruh desktop browser (Chrome, Edge, Firefox, Safari). | Kompatibel namun lebih lambat render di awal. |

### Opsi Terpilih:
**Opsi 1 (Relative Signed Routes + Pembersihan Lifecycle Iframe)**:
1. Ubah middleware rute `berkas.download` dan `berkas.pratinjau` di `routes/web.php` menjadi `signed:relative`.
2. Di `App\Models\Dokumen`, ubah pembentukan URL signed menjadi relatif (`$absolute = false`). URL yang dihasilkan menjadi format `/berkas/{id}/pratinjau?expires=...&signature=...`.
3. Di `resources/views/peserta/pendaftaran/detail.blade.php`:
   - Hapus atribut `onerror="handlePreviewError()"` dari tag `<iframe>`.
   - Pada `openDocPreview()`, ubah pembersihan awal dari `iframe.src = ''` menjadi `iframe.src = 'about:blank'`.
   - Pastikan iframe langsung diset `style.display = 'block'` dan `previewError.style.display = 'none'`.
   - Gunakan `route('peserta.penempatan.surat-diterima.pratinjau', $pendaftaran->penempatan, false)` (relatif) untuk Surat Diterima.
4. Perbarui `APP_URL` pada `.env` lokal menjadi `http://127.0.0.1:8000` agar seluruh link absolut sistem sinkron dengan port lokal yang sedang aktif.

---

## 4. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [`routes/web.php`](file:///g:/MAGKOM/routes/web.php) | Modify | Mengganti middleware `signed` menjadi `signed:relative` pada rute `berkas.download` dan `berkas.pratinjau` agar signature valid tanpa memandang host. |
| [`app/Models/Dokumen.php`](file:///g:/MAGKOM/app/Models/Dokumen.php) | Modify | Mengubah `getSignedDownloadUrl()` dan `getSignedPreviewUrl()` agar menghasilkan relative URL (`$absolute = false`). |
| [`resources/views/peserta/pendaftaran/detail.blade.php`](file:///g:/MAGKOM/resources/views/peserta/pendaftaran/detail.blade.php) | Modify | Menghapus fragile `onerror` dari `<iframe>`, memperbaiki reset `src = 'about:blank'`, memastikan display iframe aktif, dan menggunakan relative route untuk Surat Diterima. |
| [`.env`](file:///g:/MAGKOM/.env) | Modify | Memperbarui `APP_URL=http://127.0.0.1:8000` untuk konsistensi lingkungan web dev lokal. |
| [`tests/Feature/PesertaPendaftaranFlowTest.php`](file:///g:/MAGKOM/tests/Feature/PesertaPendaftaranFlowTest.php) | Modify / Verify | Menyesuaikan dan memvalidasi pengujian pratinjau inline dengan format relative signed route. |

---

## 5. Langkah Implementasi
*Catatan: Checklist di bawah ini telah dieksekusi oleh skill `web-implementation` secara bertahap.*

- [x] **Langkah 1**: Perbarui `routes/web.php` untuk menggunakan middleware `signed:relative` pada rute `berkas.download` dan `berkas.pratinjau`.
- [x] **Langkah 2**: Perbarui `app/Models/Dokumen.php` pada fungsi `getSignedDownloadUrl()` dan `getSignedPreviewUrl()` dengan argumen `$absolute = false` di `URL::temporarySignedRoute()`.
- [x] **Langkah 3**: Perbarui `.env` dengan mengatur `APP_URL=http://127.0.0.1:8000`.
- [x] **Langkah 4**: Refaktor markup viewer dan JavaScript modal pada `resources/views/peserta/pendaftaran/detail.blade.php`:
  - Hapus `onerror="handlePreviewError()"` dan `onload` inline pada elemen `<iframe id="previewIframe">` dan `<img id="previewImage">`.
  - Pasang listener onload/onerror secara dinamis saat berkas dibuka di `openDocPreview()`.
  - Perbaiki reset state di `openDocPreview()` dan `closeDocPreview()` menggunakan `'about:blank'` dan `removeAttribute('src')`.
  - Pastikan timer loading (500ms) mulus menyembunyikan spinner saat PDF dirender.
- [x] **Langkah 5**: Jalankan suite automated testing PHPUnit (`php artisan test --filter=PesertaPendaftaranFlowTest`) untuk memverifikasi tidak ada regresi pada otorisasi, unduh, dan pratinjau berkas (13 tests lulus 100%).
- [x] **Langkah 6**: Validasi verifikasi visual langsung pada halaman browser `http://127.0.0.1:8000/peserta/pendaftaran/2` dengan mengklik tombol **Lihat Berkas** pada CV dan **Lihat Surat Diterima**.

---

## 6. Strategi Testing

### Unit & Integration Test:
- Eksekusi `php artisan test --filter=PesertaPendaftaranFlowTest` untuk memverifikasi:
  - `test_peserta_can_preview_uploaded_document_inline_with_signed_url`: memastikan HTTP 200, Content-Type `application/pdf`, dan Content-Disposition `inline`.
  - `test_accepted_peserta_shows_download_surat_diterima_pdf`: memastikan pratinjau Surat Diterima inline berhasil.
  - Memastikan otorisasi proteksi berkas tetap menolak akses tidak sah (403).

### UI & Manual Verification:
1. Buka modal berkas "Curriculum Vitae" di `http://127.0.0.1:8000/peserta/pendaftaran/2`.
2. Verifikasi iframe memuat dokumen PDF formal (dokumen resmi Dinas Kominfo Sleman) secara langsung di dalam area modal tanpa muncul kotak peringatan *"Tidak Dapat Memuat Pratinjau Langsung"*.
3. Klik tombol "Tab Baru" di header modal dan pastikan dokumen terbuka di tab peramban baru.
4. Klik tombol "Lihat Surat Diterima" pada banner kelulusan dan pastikan Surat Keterangan Diterima juga tampil langsung di modal.
5. Tutup modal menggunakan tombol silang (x) atau tombol keyboard `Esc`, lalu buka kembali berkas lain untuk memastikan tidak ada flickering atau sisa state sebelumnya.

---

## 7. Risiko & Mitigasi

- **Breaking Changes**: Tidak ada. Penggunaan `signed:relative` merupakan fitur bawaan resmi Laravel 10/11/12 yang secara spesifik dirancang untuk aplikasi di balik proxy, port berbeda, atau iframe internal.
- **Migrasi Data / Database**: Tidak ada modifikasi skema tabel atau manipulasi database.
- **Keamanan (OWASP & PRD)**:
  - Berkas tetap berada di `private disk` (bukan public storage).
  - Akses tetap dilindungi otorisasi `auth` dan token kriptografi HMAC SHA-256 yang kedaluwarsa otomatis dalam 5 menit.
  - `X-Frame-Options: SAMEORIGIN` tetap aktif melindungi situs dari serangan clickjacking eksternal karena framing hanya diizinkan dari origin aplikasi sendiri.
- **Aksesibilitas (a11y)**: Modal mempertahankan atribut ARIA (`role="dialog"`, `aria-modal="true"`, `aria-labelledby="previewModalTitle"`), dan navigasi keyboard `Escape` tetap berfungsi menutup modal.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Seluruh 6 langkah implementasi tercentang `- [x]`.
- [x] Pesan error *"Tidak Dapat Memuat Pratinjau Langsung"* tidak lagi muncul saat membuka pratinjau dokumen PDF valid.
- [x] Dokumen PDF (baik berkas persyaratan terunggah maupun Surat Diterima) tertampil langsung dan terbaca di dalam modal website.
- [x] Semua 13 automated tests di `PesertaPendaftaranFlowTest` lulus (100% green).
- [x] Tidak ada error di browser console.

---

## 9. Catatan Perubahan & Saran Lanjutan

- **Perubahan selama pengerjaan**:
  1. Ditemukan bahwa tag `<img id="previewImage">` sebelumnya memiliki inline `onerror="handlePreviewError()"`. Ketika fungsi JavaScript `openDocPreview()` mereset `img.src = ''`, browser Chromium memicu event error pada elemen gambar yang kemudian langsung mengeksekusi `handlePreviewError()`, menyembunyikan iframe PDF dan memunculkan kotak fallback error. Masalah ini diselesaikan secara tuntas dengan menghapus seluruh inline event handler dari tag HTML dan mengelolanya secara dinamis via JavaScript murni.
  2. URL signed diubah ke format relatif (`signed:relative`), menjamin konsistensi origin host di `127.0.0.1:8000` maupun `localhost:8000` dan meniadakan penolakan framing oleh header `X-Frame-Options: SAMEORIGIN`.
- **Saran Lanjutan (Out of Scope)**:
  - Menambahkan zoom in / zoom out controls kustom jika kelak dibutuhkan mode pembacaan dokumen layar penuh (*fullscreen reading mode*).
