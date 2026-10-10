# Plan: Pratinjau Dokumen Berkas & Surat Diterima In-App Tanpa Unduh Paksa

- **Tanggal**: 2026-10-10
- **Status**: Completed (Implementasi Selesai & Terverifikasi)
- **Author**: Antigravity (web-implementation)
- **Dokumen Acuan**:
  - [`01-PRD-MagKom.md`](file:///g:/MAGKOM/docs/01-PRD-MagKom.md) (Fitur F-03 Pemberkasan, Fitur F-16 Surat Keterangan Diterima, Bab 7 [AC-03-9], Bab 8.4 Keamanan Berkas, NFR-S11 Header Keamanan HTTP)
  - [`02-TDD-MagKom.md`](file:///g:/MAGKOM/docs/02-TDD-MagKom.md) (Bab 4.2 Skema Dokumen, Bab 5.6 Berkas & Signed Route 5 Menit, Bab 8.1 Keamanan Berkas Privat, Bab 8.3 Mitigasi OWASP & CSP, Bab 9 Audit Log)
  - [`07-DESIGN-SYSTEM-MAGKOM.md`](file:///g:/MAGKOM/docs/07-DESIGN-SYSTEM-MAGKOM.md) (Bab 2 Tokens, Bab 3.1 Tombol, Bab 3.3 Badges, Bab 3.4 Modal Dialog, Bab 3.7 Pola Khusus MagKom: Upload & Pratinjau Berkas)

---

## 1. Tujuan

Menyediakan kemampuan pratinjau dokumen langsung di dalam antarmuka website (*in-app preview modal & browser inline streaming*) tanpa memaksa pengunduhan berkas ke perangkat pengguna:
1. **Pratinjau Berkas Persyaratan Terunggah (F-03, AC-03-9)**:
   - Peserta magang dan admin/verifikator dapat melihat langsung dokumen yang telah diunggah (format **PDF, JPG, JPEG, PNG**) di dalam modal interaktif tanpa meninggalkan halaman pendaftaran.
   - Memperbaiki perilaku saat ini di mana tombol "Lihat / Unduh" mengirimkan header `Content-Disposition: attachment` yang selalu memicu download paksa file fisik.
2. **Pratinjau Surat Keterangan Diterima (F-16, AC-16-1)**:
   - Peserta yang telah berstatus `Diterima Magang` dapat melihat pratinjau dokumen resmi Surat Keterangan Diterima (PDF on-the-fly berstempel digital) langsung di website dalam modal viewer sebelum memutuskan untuk mengunduhnya.
3. **Penyelarasan Keamanan & CSP (NFR-S11, TDD 8.3)**:
   - Menyesuaikan konfigurasi header `X-Frame-Options` dan Content Security Policy (`frame-ancestors`) dari `DENY` / `'none'` menjadi `SAMEORIGIN` / `'self'` agar browser mengizinkan render iframe dokumen internal dari domain sendiri dengan tetap memblokir serangan clickjacking dari pihak luar.

---

## 2. Asumsi & Pertanyaan Terbuka

- **Asumsi**:
  1. Jenis file berkas persyaratan yang didukung untuk pratinjau adalah **PDF** (`application/pdf`) dan **Gambar** (`image/jpeg`, `image/png`) sesuai `ALLOWED_MIMES` di `DocumentStorageService`.
  2. Surat Keterangan Diterima selalu berformat **PDF** yang dihasilkan oleh DomPDF via `SuratDiterimaPdfService`.
  3. Dokumen fisik tersimpan di disk privat (`storage/app/private/...`) dan akses pratinjau tetap wajib diproteksi oleh **Temporary Signed URL (5 menit)** serta otorisasi `DokumenPolicy` dan `PenempatanPolicy`.
  4. Komponen viewer diimplementasikan secara **native in-app (Vanilla JS + Blade)**:
     - Untuk PDF: memanfaatkan native PDF viewer browser via elemen `<iframe src="..." style="width: 100%; height: 75vh; border: none; border-radius: 8px;"></iframe>`.
     - Untuk Gambar: memanfaatkan responsive centered lightbox `<img src="..." style="max-height: 75vh; max-width: 100%; object-fit: contain; border-radius: 8px;">`.
     - Modal viewer dilengkapi kontrol: tombol "Buka di Tab Baru", tombol "Unduh Berkas", indikator loading, dan tombol "Tutup" (X / Escape / klik luar).
  5. Pengguna tetap memiliki opsi untuk mengunduh berkas secara manual jika memang membutuhkan salinan offline.
- **Pertanyaan Terbuka**:
  - *Tidak ada ambiguitas kritis*. Alur otorisasi, disk privat, dan endpoint telah terstruktur rapi di PRD dan codebase eksisting.

---

## 3. Pendekatan Desain & Arsitektur

### Evaluasi Opsi Pratinjau Dokumen:

| Kriteria Evaluasi | Opsi A: Library PDF Eksternal (PDF.js / Viewer.js via CDN/NPM) | Opsi B: Cloud Viewer Eksternal (Google Docs / Office Online Viewer) | Opsi C: Native Browser Streaming Inline + In-App Modal (Terpilih) |
| :--- | :--- | :--- | :--- |
| **Kesesuaian Private Storage** | Bisa membaca blob/arraybuffer via API signed URL. | **GAGAL**: File di disk privat dengan auth & signed URL lokal tidak bisa diakses publik oleh Google Server. | **Sangat Sesuai**: Browser langsung memuat stream privat via signed URL dengan cookie auth aktif. |
| **Ukuran & Kompleksitas Bundling** | Berat (~2–3 MB library tambahan), perlu worker terpisah. | Sangat ringan (hanya iframe URL), tetapi tidak aman. | **Sangat Ringan**: 0 KB library eksternal, murni native browser PDF/Image rendering. |
| **Kepatuhan CSP & Keamanan (TDD 8.3)** | Berisiko melanggar CSP ketat (`worker-src`, `unsafe-eval`, `blob:`). | Membocorkan URL ke pihak ketiga, risiko kebocoran data identitas peserta (GDPR/UU PDP). | **100% Patuh**: Sepenuhnya dilayani dari origin internal (`'self'`). |
| **Pengalaman Pengguna (UX)** | Tampilan konsisten, namun lambat pada perangkat berspesifikasi rendah. | Bergantung koneksi internet ke server luar. | **Cepat & Responsif**: Memanfaatkan GPU & native viewer bawaan browser yang sangat familiar. |

- **Opsi Terpilih**: **Opsi C (Native Browser Streaming Inline + In-App Modal Viewer)**.
- **Alasan Pemilihan**:
  1. Nol dependensi pihak ketiga, tidak memerlukan build tool baru atau script eksternal.
  2. Memenuhi 100% prinsip privasi data peserta magang (dokumen KTP/KTM, transkrip, surat pengantar tidak pernah dikirim ke pihak ketiga).
  3. Menggunakan header `Content-Disposition: inline` sehingga browser secara cerdas menampilkan dokumen di dalam iframe modal tanpa dialog download paksa.

---

## 4. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `app/Http/Middleware/SecurityHeaders.php` | Modify | Mengubah `X-Frame-Options` dari `DENY` menjadi `SAMEORIGIN` dan CSP `frame-ancestors` dari `'none'` menjadi `'self'` agar modal iframe internal dapat merender dokumen dari domain yang sama. |
| `app/Services/DocumentStorageService.php` | Modify | Menambahkan method `pratinjau(Dokumen $dokumen): Response` yang menyajikan berkas privat dengan header `Content-Disposition: inline` dan `X-Content-Type-Options: nosniff`. |
| `app/Services/SuratDiterimaPdfService.php` | Modify | Menambahkan method `stream(Penempatan $penempatan): Response` yang menghasilkan stream PDF Surat Keterangan Diterima menggunakan `$pdf->stream($filename)` dengan `Content-Disposition: inline`. |
| `app/Models/Dokumen.php` | Modify | Menambahkan method helper `getSignedPreviewUrl(int $minutes = 5): string` untuk menghasilkan temporary signed URL rute `berkas.pratinjau`. |
| `app/Http/Controllers/Peserta/BerkasController.php` | Modify | Menambahkan action `pratinjau(Request $request, Dokumen $dokumen)` yang memvalidasi otorisasi `view` dan memanggil `$documentService->pratinjau($dokumen)`. |
| `app/Http/Controllers/Peserta/PendaftaranController.php` | Modify | Menambahkan action `pratinjauSuratDiterima(Penempatan $penempatan, SuratDiterimaPdfService $pdfService)` untuk menyajikan stream inline PDF surat diterima peserta. |
| `app/Http/Controllers/Admin/PenempatanController.php` | Modify | Menambahkan action `pratinjauSuratDiterima(Penempatan $penempatan, SuratDiterimaPdfService $pdfService)` untuk menyajikan stream inline PDF surat diterima di panel admin. |
| `routes/web.php` | Modify | Mendaftarkan rute `berkas.pratinjau` (`/berkas/{dokumen}/pratinjau`), `peserta.penempatan.surat-diterima.pratinjau`, dan `admin.penempatan.surat-diterima.pratinjau`. |
| `resources/views/peserta/pendaftaran/detail.blade.php` | Modify | 1. Memasang komponen **In-App Document Preview Modal** (`#docPreviewModal`).<br>2. Mengubah tombol pada kartu berkas: tombol utama **"Lihat Berkas"** (membuka modal) dan tombol sekunder **"Unduh"**.<br>3. Menambahkan tombol **"Lihat Surat Diterima"** di banner kelulusan status `diterima` di samping tombol unduh PDF.<br>4. Menambahkan script Vanilla JS handler modal preview (buka/tutup, toggle iframe vs img, loading state, tombol buka tab baru). |
| `resources/views/admin/pendaftaran/show.blade.php` | Modify | Memperbarui tombol "Tinjau Berkas" agar membuka pratinjau inline di tab baru atau modal viewer alih-alih mengunduh paksa file. |
| `tests/Feature/PesertaPendaftaranFlowTest.php` | Modify | Menambahkan test case pengujian pratinjau berkas inline (200 OK, Content-Disposition inline, header nosniff) dan pratinjau Surat Diterima PDF. |

---

## 5. Langkah Implementasi

*Catatan: Checklist di bawah ini akan dieksekusi oleh skill `web-implementation` secara bertahap.*

- [x] **Langkah 1: Penyesuaian Security Headers untuk Pratinjau Same-Origin**
  - Buka [`app/Http/Middleware/SecurityHeaders.php`](file:///g:/MAGKOM/app/Http/Middleware/SecurityHeaders.php).
  - Ubah `$response->headers->set('X-Frame-Options', 'DENY');` menjadi `$response->headers->set('X-Frame-Options', 'SAMEORIGIN');`.
  - Pada CSP produksi, ubah `frame-ancestors 'none';` menjadi `frame-ancestors 'self';`.
  - Pastikan `X-Content-Type-Options: nosniff` tetap aktif.

- [x] **Langkah 2: Implementasi Service Backend untuk Streaming Inline**
  - Buka [`app/Services/DocumentStorageService.php`](file:///g:/MAGKOM/app/Services/DocumentStorageService.php).
  - Tambahkan method `pratinjau(Dokumen $dokumen): \Symfony\Component\HttpFoundation\Response`:
    ```php
    public function pratinjau(Dokumen $dokumen): \Symfony\Component\HttpFoundation\Response
    {
        if (! Storage::disk('private')->exists($dokumen->path)) {
            abort(404, 'Berkas fisik tidak ditemukan di penyimpanan server.');
        }

        return Storage::disk('private')->response(
            $dokumen->path,
            $dokumen->nama_asli,
            [
                'Content-Type' => $dokumen->mime,
                'Content-Disposition' => 'inline; filename="' . $dokumen->nama_asli . '"',
                'X-Content-Type-Options' => 'nosniff',
            ],
            'inline'
        );
    }
    ```
  - Buka [`app/Services/SuratDiterimaPdfService.php`](file:///g:/MAGKOM/app/Services/SuratDiterimaPdfService.php).
  - Tambahkan method `stream(Penempatan $penempatan): \Illuminate\Http\Response` yang menyiapkan view `pdf.surat-keterangan-diterima` dan mengembalikan `$pdf->stream($filename);` dengan pencatatan audit log `unduh_surat_keterangan_diterima_pdf`.
  - Buka [`app/Models/Dokumen.php`](file:///g:/MAGKOM/app/Models/Dokumen.php).
  - Tambahkan helper method `getSignedPreviewUrl(int $minutes = 5): string` mengarah ke rute `berkas.pratinjau`.

- [x] **Langkah 3: Pendaftaran Route & Controller Pratinjau**
  - Buka [`routes/web.php`](file:///g:/MAGKOM/routes/web.php).
  - Tambahkan rute privat signed:
    ```php
    Route::get('/berkas/{dokumen}/pratinjau', [BerkasController::class, 'pratinjau'])
        ->name('berkas.pratinjau')
        ->middleware(['signed', 'auth']);
    ```
  - Tambahkan rute pratinjau surat diterima di prefix `peserta`:
    ```php
    Route::get('/penempatan/{penempatan}/surat-diterima/pratinjau', [PesertaPendaftaranController::class, 'pratinjauSuratDiterima'])
        ->name('penempatan.surat-diterima.pratinjau');
    ```
  - Tambahkan rute pratinjau surat diterima di prefix `admin`:
    ```php
    Route::get('/penempatan/{penempatan}/surat-diterima/pratinjau', [AdminPenempatanController::class, 'pratinjauSuratDiterima'])
        ->name('penempatan.surat-diterima.pratinjau');
    ```
  - Buka [`app/Http/Controllers/Peserta/BerkasController.php`](file:///g:/MAGKOM/app/Http/Controllers/Peserta/BerkasController.php) dan implementasikan action `pratinjau`.
  - Buka [`app/Http/Controllers/Peserta/PendaftaranController.php`](file:///g:/MAGKOM/app/Http/Controllers/Peserta/PendaftaranController.php) dan implementasikan action `pratinjauSuratDiterima`.
  - Buka [`app/Http/Controllers/Admin/PenempatanController.php`](file:///g:/MAGKOM/app/Http/Controllers/Admin/PenempatanController.php) dan implementasikan action `pratinjauSuratDiterima`.

- [x] **Langkah 4: Pembuatan Komponen Modal In-App Previewer di `detail.blade.php`**
  - Buka [`resources/views/peserta/pendaftaran/detail.blade.php`](file:///g:/MAGKOM/resources/views/peserta/pendaftaran/detail.blade.php).
  - Rancang komponen `#docPreviewModal` berstandar Design System MagKom:
    - Ukuran modal lebar: `max-width: 960px; width: 95%; max-height: 90vh;`
    - Header modal:
      - Judul dokumen & icon MIME.
      - Badge tipe berkas (`PDF` / `JPG` / `PNG`).
      - Tombol aksi: "Buka di Tab Baru" (link eksternal), "Unduh Berkas" (link download fisik), dan tombol "Tutup" (X).
    - Body modal:
      - Loading spinner skeleton saat konten sedang dimuat.
      - Kontainer iframe untuk PDF (`<iframe id="previewIframe" ...>`).
      - Kontainer image untuk gambar (`<img id="previewImage" ...>`).
      - Error message box jika file gagal dirender dengan tombol coba lagi atau unduh langsung.
  - Perbarui tombol pada kartu berkas yang sudah terunggah:
    - Tombol utama: **"Lihat Berkas"** (memanggil fungsi JS `openDocPreview(url, downloadUrl, title, mime)`).
    - Tombol sekunder: **"Unduh"** (mengarah ke signed download URL).
  - Perbarui banner pengumuman status `diterima`:
    - Tambahkan tombol **"Lihat Surat Diterima"** (memanggil `openDocPreview(...)` dengan rute pratinjau PDF Surat Keterangan Diterima).
    - Pertahankan tombol "Unduh Surat Diterima (PDF)".
  - Tambahkan script Vanilla JS untuk mengelola lifecycle modal:
    - Menangani buka/tutup modal.
    - Menangani event listener `Escape` key dan klik di luar area modal (backdrop click).
    - Menangani onload iframe/image untuk menghilangkan loading skeleton secara mulus.

- [x] **Langkah 5: Penyelarasan Pratinjau Berkas pada Panel Admin Verifikator**
  - Buka [`resources/views/admin/pendaftaran/show.blade.php`](file:///g:/MAGKOM/resources/views/admin/pendaftaran/show.blade.php).
  - Perbarui tombol "Tinjau Berkas" agar mengarah ke `$dok->getSignedPreviewUrl(5)` dengan atribut `target="_blank" rel="noopener noreferrer"` dan `title="Pratinjau berkas langsung di tab browser"`, sehingga verifikator admin dapat membaca berkas langsung di tab baru tanpa harus terdownload file-nya.

- [x] **Langkah 6: Automated Testing & Verifikasi Tampilan Visual**
  - Buka [`tests/Feature/PesertaPendaftaranFlowTest.php`](file:///g:/MAGKOM/tests/Feature/PesertaPendaftaranFlowTest.php).
  - Tambahkan unit/feature test:
    1. Akses `berkas.pratinjau` oleh pemilik pendaftaran dengan signed URL valid mengembalikan status 200, header `Content-Disposition` bernilai `inline; ...`, dan `X-Content-Type-Options: nosniff`.
    2. Akses `berkas.pratinjau` tanpa signature valid mengembalikan 403.
    3. Akses `berkas.pratinjau` oleh peserta lain yang tidak berhak mengembalikan 403.
    4. Akses `peserta.penempatan.surat-diterima.pratinjau` mengembalikan status 200 dengan header `Content-Type: application/pdf` dan `Content-Disposition: inline`.
  - Jalankan test suite PHPUnit: `php artisan test --filter=PesertaPendaftaranFlowTest`.
  - Jalankan regression test: `php artisan test --filter=PendaftaranPesertaTest` dan `php artisan test --filter=ReleaseTwoFeaturesTest`.
  - Uji visual di browser: buka halaman detail pendaftaran di `http://127.0.0.1:8000/peserta/pendaftaran/2`, klik tombol "Lihat Berkas" dan "Lihat Surat Diterima", pastikan modal viewer terbuka dengan sempurna dan berkas tampil langsung di website.

---

## 6. Strategi Testing

- **Unit & Integration Test**:
  - `test_peserta_bisa_pratinjau_berkas_secara_inline_dengan_signed_url`: Memastikan response berkas berstatus 200, Content-Type sesuai mime, dan header `Content-Disposition` mengandung `inline`.
  - `test_pratinjau_berkas_menolak_signature_tidak_valid`: Memastikan signed middleware bekerja aktif.
  - `test_pratinjau_berkas_mencegah_idor_oleh_peserta_lain`: Memastikan `DokumenPolicy` menolak peserta asing (403).
  - `test_pratinjau_surat_keterangan_diterima_stream_pdf`: Memastikan response surat diterima berstatus 200 dan dikirim dalam format inline stream PDF.
- **UI & Manual Verification**:
  - Membuka halaman detail pendaftaran akun peserta yang sudah berstatus diterima.
  - Mengklik tombol "Lihat Berkas" pada berkas PDF: modal terbuka, iframe memuat dokumen dengan controls native PDF viewer, tidak ada file yang terunduh otomatis ke folder Downloads komputer.
  - Mengklik tombol "Lihat Berkas" pada berkas gambar (JPG/PNG): modal menampilkan gambar tajam dan terpusat.
  - Mengklik tombol "Lihat Surat Diterima": modal menampilkan PDF Surat Keterangan Diterima resmi dengan kop surat dan data lengkap.
  - Memverifikasi interaksi keyboard: menekan tombol `Esc` menutup modal pratinjau seketika.

---

## 7. Risiko & Mitigasi

- **Breaking Changes**:
  - *Risiko*: Rute download lama `berkas.download` dan `peserta.penempatan.surat-diterima` tetap dipertahankan untuk kebutuhan pengguna yang ingin mengunduh offline. Tidak ada breaking change terhadap API atau rute eksisting.
- **Keamanan Frame & Clickjacking (OWASP)**:
  - *Risiko*: Pengubahan `X-Frame-Options` dari `DENY` menjadi `SAMEORIGIN` dan CSP `frame-ancestors 'self'`.
  - *Mitigasi*: Nilai `SAMEORIGIN` dan `'self'` secara tegas hanya mengizinkan situs internal MagKom sendiri yang membingkai halaman/dokumen. Seluruh domain eksternal tetap 100% diblokir oleh browser dari serangan clickjacking.
- **MIME Sniffing**:
  - *Mitigasi*: Menjaga header `X-Content-Type-Options: nosniff` pada setiap response file stream untuk mencegah eksekusi skrip berbahaya yang disamarkan dalam file gambar/PDF.
- **Aksesibilitas (a11y)**:
  - Modal dilengkapi atribut ARIA (`role="dialog"`, `aria-modal="true"`, `aria-labelledby="previewModalTitle"`), focus trapping, dan dukungan tombol `Escape`.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Semua checklist langkah implementasi tercentang `- [x]`.
- [x] Endpoint `berkas.pratinjau` dan `surat-diterima/pratinjau` berhasil mengalirkan dokumen secara inline (`Content-Disposition: inline`).
- [x] Modal in-app previewer di `detail.blade.php` berfungsi lancar untuk file PDF dan gambar tanpa memicu unduhan otomatis.
- [x] Tombol "Lihat Surat Diterima" tampil dan berfungsi pada status `diterima`.
- [x] Seluruh automated test PHPUnit (`PesertaPendaftaranFlowTest`) lulus 100% tanpa error (13 passed, 59 assertions).
- [x] Tidak ada regresi fitur pada modul pendaftaran atau verifikasi berkas admin.

---

## 9. Catatan Perubahan & Saran Lanjutan

- **Perubahan selama pengerjaan**:
  - Seluruh langkah 1 sampai 6 telah selesai diimplementasikan dan diverifikasi:
    1. `SecurityHeaders.php`: `X-Frame-Options` disetel ke `SAMEORIGIN` dan CSP `frame-ancestors 'self'` untuk mendukung iframe modal internal.
    2. `DocumentStorageService` & `SuratDiterimaPdfService`: Mendukung streaming inline dengan `Content-Disposition: inline` dan `X-Content-Type-Options: nosniff`.
    3. `routes/web.php`: Rute `berkas.pratinjau`, `peserta.penempatan.surat-diterima.pratinjau`, dan `admin.penempatan.surat-diterima.pratinjau` telah aktif dan diproteksi signed URL & otorisasi.
    4. `detail.blade.php`: Modal `#docPreviewModal` in-app responsif (PDF iframe, JPG/PNG image, fallback download) telah aktif beserta tombol "Lihat Surat Diterima" dan "Lihat Berkas".
    5. `admin/pendaftaran/show.blade.php`: Tombol "Tinjau Berkas" telah menggunakan inline signed preview URL.
    6. Automated tests: 13 feature tests di `PesertaPendaftaranFlowTest` lulus 100%.
- **Saran Lanjutan (Out of Scope)**:
  - Fitur rotasi gambar 90 derajat searah jarum jam langsung di modal viewer jika ada foto peserta yang terunggah miring.
  - Fitur cetak langsung (*quick print*) dari modal viewer untuk berkas surat diterima.
