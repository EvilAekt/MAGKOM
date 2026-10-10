# Plan: Penyempurnaan Modul Pemberkasan Peserta Magang & Penyelarasan Design System

- **Tanggal**: 2026-10-09
- **Status**: Completed (Implementasi Selesai & Terverifikasi)
- **Author**: Antigravity (web-implementation)
- **Dokumen Acuan**:
  - [`01-PRD-MagKom.md`](file:///g:/MAGKOM/docs/01-PRD-MagKom.md) (Fitur F-03, Bab 4.3, Bab 5 [BR-03, BR-04, BR-05, BR-06, BR-17], Bab 6.2, Bab 6.3, Bab 7 [AC-03-1 s/d AC-03-10], Bab 8.4, Bab 8.6)
  - [`02-TDD-MagKom.md`](file:///g:/MAGKOM/docs/02-TDD-MagKom.md) (Bab 4.2 Skema Dokumen, Bab 5.6 Berkas & Signed Route 5 Menit, Bab 8.1 Keamanan Berkas Privat, Bab 9 Audit Log)
  - [`07-DESIGN-SYSTEM-MAGKOM.md`](file:///g:/MAGKOM/docs/07-DESIGN-SYSTEM-MAGKOM.md) (Bab 2 Tokens, Bab 3.1 Tombol, Bab 3.3 Status Badges & Lucide Icons, Bab 3.4 Modal Konfirmasi, Bab 3.7 Pola Khusus MagKom: Upload Berkas & Form Bertahap, Bab 4 Pemetaan Status PRD)
  - [`03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md`](file:///g:/MAGKOM/docs/03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md) (Gelombang 2: Akun, Pendaftaran & Kuota Atomik Quality Gates)

---

## 1. Tujuan

Menyempurnakan alur dan antarmuka **Pemberkasan Peserta Magang (Fitur F-03)** agar 100% patuh terhadap ketentuan bisnis PRD, standar keamanan TDD, dan kaidah estetika modern Design System MagKom:
1. **Kepatuhan Fungsional & Logika Bisnis PRD**:
   * Melengkapi fitur **Pembatalan Pendaftaran Mandiri oleh Peserta (AC-03-9)** sebelum berkas diterima, menggunakan dialog konfirmasi standar Design System dan pencatatan audit log `TipeAuditEnum::Aksi`.
   * Memperbaiki alur pengajuan berkas: saat peserta mengajukan (`ajukan`), status pendaftaran **selalu** berubah menjadi `StatusPendaftaranEnum::Diajukan` (AC-03-6) dengan pencatatan `diajukan_at = now()`. Mengoreksi bug eksisting yang melompatkan status langsung ke `waiting_list` tanpa verifikasi admin (BR-06).
   * Menerapkan **Dropdown Berantai Pendaftaran (AC-03-2)**: Jurusan (otomatis terikat ke jurusan profil peserta) → Periode Bulan Magang (hanya bulan berstatus `Tersedia` dan memiliki alokasi kuota aktif untuk jurusan tersebut) → Rencana Durasi Magang (1–6 bulan).
   * Menegakkan aturan **1 Pendaftaran Aktif (BR-03, BR-17, AC-03-4)** secara visual pada antarmuka: menonaktifkan tombol pengajuan baru dan menampilkan banner ramah jika peserta sudah memiliki pendaftaran yang sedang berjalan.
   * Mengharmonisasi aturan upload berkas (PRD Bab 8.4 & Bab 8.6): batas ukuran dinamis `berkas_maks_mb` (default 2 MB) dan format yang diterima **hanya** `PDF, JPG, JPEG, PNG` (menghapus residu `.doc,.docx` yang dilarang).
   * Menyempurnakan pencatatan audit trail saat penggantian berkas: mencatat hash lama dan hash baru (AC-03-7).
   * Mengoreksi masa berlaku Temporary Signed URL unduhan berkas menjadi **5 menit** sesuai TDD 5.6 & PRD 8.4 (saat ini masih 30 menit).

2. **Peningkatan Kualitas Visual & UX (Design System MagKom Bab 3.7 & Bab 4)**:
   * Mengganti form upload tradisional (`<input type="file">` polos) dengan **Interactive Dropzone Card (Drag & Drop)** per jenis dokumen persyaratan, lengkap dengan area drop dinamis, indikator format, ukuran file, dan status visual berkas.
   * Menambahkan **Progress Bar Kelengkapan Dokumen** (*e.g., "2 dari 3 Berkas Wajib Terunggah — 67%"*) untuk memberikan umpan balik visual instan.
   * Menerapkan **Client-Side Pre-Upload Validation** via Vanilla JS untuk memvalidasi batas ukuran (≤ 2 MB) dan ekstensi file sebelum form disubmit, mencegah error server yang tidak perlu.
   * Menyelaraskan seluruh badge status sesuai **Design System Bab 4** lengkap dengan label baku, token warna resmi, dan ikon Lucide SVG.
   * Menampilkan **Panel Catatan Revisi Admin (AC-03-10)** secara menonjol pada status `perlu_perbaikan` lengkap dengan stempel waktu dan panduan jelas.
   * Menerapkan proteksi **Anti Double-Submit** dengan visual loading spinner pada tombol submit berkas dan tombol ajukan (AC-03-8).

---

## 2. Asumsi & Pertanyaan Terbuka

- **Asumsi**:
  1. Batas maksimal ukuran berkas diambil secara dinamis dari tabel `pengaturan` dengan kunci `berkas_maks_mb` (default 2 MB sesuai PRD Bab 8.4 dan 8.6). Fallback di controller dan form request diselaraskan ke 2 MB.
  2. Jenis dokumen persyaratan wajib ditentukan per jenjang peserta (SMK: Surat Pengantar Sekolah, CV, Rapor/Salinan Kartu Pelajar; D3/S1: Surat Pengantar Kampus, CV, Transkrip Nilai) yang bersumber dari tabel `jenis_dokumen`.
  3. Pembatalan pendaftaran mandiri oleh peserta (AC-03-9) hanya berlaku untuk status **sebelum diterima**: `berkas_belum_lengkap`, `diajukan`, `perlu_perbaikan`, atau `waiting_list`. Status `diterima`, `ditolak`, atau `dibatalkan` tidak dapat dibatalkan lagi oleh peserta.
  4. Penyimpanan berkas tetap aman di disk privat (`storage/app/private/documents/{pendaftaran_id}/...`) dan diakses melalui temporary signed route berumur 5 menit (`route('berkas.download')`).
  5. Sesuai PRD BR-14, sistem tidak mengirimkan blast email notifikasi; pemantauan status pendaftaran dilakukan murni melalui portal dashboard peserta.
- **Pertanyaan Terbuka**: Tidak ada ambiguitas kritis. Seluruh spesifikasi bisnis telah terdefinisi dengan sangat presisi di PRD, TDD, dan Design System MagKom.

---

## 3. Pendekatan Desain & Arsitektur

### Evaluasi Opsi Pendekatan Komponen Upload Berkas (Dropzone):

| Aspek | Opsi A: Dropzone Eksternal (Dropzone.js / FilePond via CDN/NPM) | Opsi B: Vanilla JS Dropzone Komponen Native Design System (Terpilih) |
| :--- | :--- | :--- |
| **Dependensi Eksternal** | Menambah library pihak ketiga; berpotensi konflik dengan CSP ketat (`script-src 'self'`) di TDD 8.3. | **Nol dependensi tambahan**; murni Vanilla JavaScript & Blade terintegrasi Design System MagKom. |
| **Kepatuhan Desain** | Sulit di-styling agar 100% presisi dengan token CSS MagKom (`--blue-600`, `--neutral-400`, Open Sans, radius 8px). | **100% konsisten** dengan token CSS resmi, dark/light contrast WCAG 2.1 AA, dan tata letak Figma. |
| **Aksesibilitas (WCAG 2.1 AA)** | Seringkali shadow DOM menyulitkan navigasi keyboard dan screen reader. | Menggunakan native `<input type="file">` tersembunyi yang tetap dapat diakses keyboard (*Tab + Space/Enter*). |
| **Ukuran & Performa** | ~40–60 KB script tambahan. | < 3 KB script inline modular, rendering cepat tanpa delay eksternal. |

- **Opsi Terpilih**: **Opsi B (Vanilla JS Native Component)**.
- **Alasan Pemilihan**: Sesuai dengan prinsip arsitektur MagKom (mengutamakan keandalan, performa tinggi tanpa bloating, serta kepatuhan penuh terhadap CSP lokal).

---

## 4. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| [`routes/web.php`](file:///g:/MAGKOM/routes/web.php) | **Modify** | Mendaftarkan route `POST /peserta/pendaftaran/{pendaftaran}/batalkan` bertarget `PesertaPendaftaranController::batalkan`. |
| [`app/Http/Controllers/Peserta/PendaftaranController.php`](file:///g:/MAGKOM/app/Http/Controllers/Peserta/PendaftaranController.php) | **Modify** | 1. Menambahkan method `batalkan(Request $request, Pendaftaran $pendaftaran)` dengan validasi status (AC-03-9) dan audit log.<br>2. Mengoreksi method `ajukan()`: status selalu diarahkan ke `StatusPendaftaranEnum::Diajukan` (AC-03-6 & BR-06).<br>3. Menyiapkan data kuota & bulan magang untuk dropdown berantai di `index()`. |
| [`app/Http/Controllers/Peserta/BerkasController.php`](file:///g:/MAGKOM/app/Http/Controllers/Peserta/BerkasController.php) | **Modify** | Menyempurnakan pencatatan audit log saat menimpa berkas lama dengan menyertakan hash lama dan hash baru (AC-03-7). |
| [`app/Http/Requests/UnggahDokumenRequest.php`](file:///g:/MAGKOM/app/Http/Requests/UnggahDokumenRequest.php) | **Modify** | Menyesuaikan fallback `berkas_maks_mb` dari 5 MB menjadi 2 MB sesuai PRD Bab 8.4 & 8.6. |
| [`app/Services/DocumentStorageService.php`](file:///g:/MAGKOM/app/Services/DocumentStorageService.php) | **Modify** | Menyelaraskan fallback `berkas_maks_mb` menjadi 2 MB dan memastikan integritas hash SHA-256. |
| [`resources/views/peserta/pendaftaran/index.blade.php`](file:///g:/MAGKOM/resources/views/peserta/pendaftaran/index.blade.php) | **Modify** | 1. Menambahkan visualisasi penegakan aturan 1 pengajuan aktif (BR-03, BR-17): banner peringatan informatif dan mengunci tombol pengajuan baru.<br>2. Menerapkan dropdown berantai pada modal pendaftaran (Jurusan Profil → Periode Bulan Magang → Durasi Magang) (AC-03-2).<br>3. Standardisasi badge status dan format tanggal sesuai Design System Bab 4. |
| [`resources/views/peserta/pendaftaran/detail.blade.php`](file:///g:/MAGKOM/resources/views/peserta/pendaftaran/detail.blade.php) | **Modify** | 1. Mengubah masa kedaluwarsa signed download URL dari 30 menit menjadi 5 menit (TDD 5.6 & PRD 8.4).<br>2. Menghapus referensi format `.doc,.docx` dan menggantinya dengan `PDF, JPG, JPEG, PNG` (PRD 8.4).<br>3. Menerapkan kartu Dropzone interaktif (drag-and-drop, validasi instan client-side ukuran/tipe, status badge).<br>4. Menambahkan Progress Bar Kelengkapan Dokumen.<br>5. Menambahkan Panel Catatan Revisi Admin saat status `perlu_perbaikan` (AC-03-10).<br>6. Menambahkan Modal Dialog Konfirmasi Pembatalan Pendaftaran (AC-03-9 & DS 3.4).<br>7. Memasang proteksi visual anti double-submit (AC-03-8).<br>8. Menyelaraskan status badges ke token dan ikon resmi Design System Bab 4. |
| [`tests/Feature/PesertaPendaftaranFlowTest.php`](file:///g:/MAGKOM/tests/Feature/PesertaPendaftaranFlowTest.php) | **Modify** | Menambahkan test case untuk pembatalan mandiri pendaftaran (AC-03-9), penolakan pembatalan jika pendaftaran sudah diterima, penegakan 1 pendaftaran aktif (BR-03), dan verifikasi status `diajukan` (AC-03-6). |

---

## 5. Langkah Implementasi

### Tahap 1: Backend Service, Route & Controller Pembatalan (AC-03-9, AC-03-6)
- [x] **Langkah 1.1**: Daftarkan route `POST /peserta/pendaftaran/{pendaftaran}/batalkan` pada group middleware `auth, verified, role:peserta` di [`routes/web.php`](file:///g:/MAGKOM/routes/web.php) dengan nama `peserta.pendaftaran.batalkan`.
- [x] **Langkah 1.2**: Implementasikan method `batalkan(Request $request, Pendaftaran $pendaftaran)` di [`app/Http/Controllers/Peserta/PendaftaranController.php`](file:///g:/MAGKOM/app/Http/Controllers/Peserta/PendaftaranController.php):
  - Validasi otorisasi kepemilikan data peserta (`$this->authorize('update', $pendaftaran)`).
  - Validasi status pendaftaran: tolak pembatalan bila status sudah `diterima`, `ditolak`, atau `dibatalkan`.
  - Ubah status pendaftaran menjadi `StatusPendaftaranEnum::Dibatalkan`.
  - Catat riwayat audit trail `TipeAuditEnum::Aksi` dengan detail aksi `Peserta membatalkan pengajuan pendaftaran magang`.
  - Redirect kembali ke `peserta.pendaftaran.index` dengan pesan flash sukses.
- [x] **Langkah 1.3**: Koreksi method `ajukan()` pada `PendaftaranController`:
  - Pastikan status pendaftaran yang diajukan selalu menjadi `StatusPendaftaranEnum::Diajukan` (karena alokasi `waiting_list` adalah kewenangan Admin setelah meninjau berkas per BR-06).
  - Set kolom `diajukan_at = now()`.
- [x] **Langkah 1.4**: Perbarui pencatatan audit di [`app/Http/Controllers/Peserta/BerkasController.php`](file:///g:/MAGKOM/app/Http/Controllers/Peserta/BerkasController.php) agar mencatat `hash` lama dan `hash` baru saat dokumen ditimpa (AC-03-7).
- [x] **Langkah 1.5**: Ubah fallback ukuran file di [`app/Http/Requests/UnggahDokumenRequest.php`](file:///g:/MAGKOM/app/Http/Requests/UnggahDokumenRequest.php) dan [`app/Services/DocumentStorageService.php`](file:///g:/MAGKOM/app/Services/DocumentStorageService.php) menjadi 2 MB (`Pengaturan::int('berkas_maks_mb', 2)`).

### Tahap 2: Standardisasi Antarmuka Indeks & Dropdown Berantai (AC-03-2, BR-03, BR-17)
- [x] **Langkah 2.1**: Perbarui [`resources/views/peserta/pendaftaran/index.blade.php`](file:///g:/MAGKOM/resources/views/peserta/pendaftaran/index.blade.php):
  - Jika `$pendaftaranAktif` terdeteksi, tampilkan banner informatif beraksen biru/info di atas tabel yang menerangkan bahwa peserta memiliki pengajuan aktif (nomor registrasi dan status saat ini).
  - Kunci tombol *"Buat Pengajuan Baru"* (disabled dengan tooltip penjelas BR-03/BR-17) selama masih ada pendaftaran aktif.
  - Perbaiki modal formulir pengajuan baru agar memiliki alur dropdown berantai yang intuitif (AC-03-2): Jurusan Profil → Pilihan Bulan Magang (hanya yang statusnya `Tersedia` dan memiliki alokasi kuota aktif untuk jurusan tersebut) → Pilihan Durasi (1–6 bulan).
  - Standarisasi badge status di tabel riwayat sesuai Design System Bab 4 (warna, label, ikon).

### Tahap 3: Redesign Modern Modul Pemberkasan & Interactive Dropzone (AC-03-5, AC-03-7, AC-03-8, AC-03-10, DS 3.7)
- [x] **Langkah 3.1**: Perbarui masa kedaluwarsa signed URL unduhan dokumen di [`resources/views/peserta/pendaftaran/detail.blade.php`](file:///g:/MAGKOM/resources/views/peserta/pendaftaran/detail.blade.php) menjadi **5 menit** (`now()->addMinutes(5)`).
- [x] **Langkah 3.2**: Perbaiki teks petunjuk dan atribut input file: hapus `.doc,.docx`, jadikan murni `.pdf,.jpg,.jpeg,.png` dengan ukuran maksimal dinamis `{{ $maksMb }} MB`.
- [x] **Langkah 3.3**: Tambahkan **Progress Bar Kelengkapan Dokumen** di atas daftar berkas:
  - Tampilkan teks ringkasan (*e.g., "2 dari 3 Berkas Wajib Terunggah (67%)"*).
  - Render progress bar bergaya Design System (tinggi 8px, background `--neutral-200`, fill bar `--green-600` atau `--blue-600` dengan transisi halus).
- [x] **Langkah 3.4**: Bangun komponen **Interactive Dropzone Card (Drag & Drop)** berbasis Vanilla JS:
  - Kotak dropzone dengan border dashed `--neutral-400`, berubah warna ke border solid `--blue-600` dan background `--blue-tint` saat kursor menyeret file di atasnya.
  - Tampilan kartu saat berkas sudah terunggah: ikon tipe dokumen (PDF merah / Gambar hijau), nama file asli, ukuran terformat, tanggal upload, tombol *Lihat/Unduh* via Signed URL 5 menit, dan tombol *Ganti Berkas* yang membuka kembali dropzone.
  - Tambahkan validasi instan sisi peramban (*instant client-side validation*): jika file melebihi batas MB atau format tidak sesuai, tampilkan alert/pesan error sebelum form dikirim ke server.
- [x] **Langkah 3.5**: Tambahkan **Panel Catatan Revisi Admin** khusus saat pendaftaran berstatus `perlu_perbaikan` (AC-03-10):
  - Alert box varian error/warning dengan border tebal, memuat catatan spesifik dari tim verifikator serta panduan perbaikan berkas.
- [x] **Langkah 3.6**: Tambahkan **Modal Dialog Konfirmasi Pembatalan Pendaftaran** (AC-03-9 & DS Bab 3.4):
  - Mengikuti spesifikasi modal Design System: lebar 520px, scrim 50% `#1D2530`, kotak peringatan amber yang menerangkan dampak pembatalan (pembatalan bersifat final), tombol *Batal* dan tombol destruktif *Ya, Batalkan Pendaftaran*.
  - Tombol pemicu *"Batalkan Pengajuan Ini"* diletakkan di kartu pendaftaran untuk status sebelum diterima.
- [x] **Langkah 3.7**: Pasang proteksi **Anti Double-Submit** pada tombol submit berkas dan tombol ajukan pendaftaran (AC-03-8):
  - Menonaktifkan tombol segera setelah diklik dan menampilkan animasi loading spinner mini agar proses tidak terkirim dua kali.

### Tahap 4: Verifikasi & Rangkaian Pengujian Otomatis
- [x] **Langkah 4.1**: Tambahkan test cases baru pada [`tests/Feature/PesertaPendaftaranFlowTest.php`](file:///g:/MAGKOM/tests/Feature/PesertaPendaftaranFlowTest.php):
  - `test_peserta_can_cancel_draft_or_submitted_pendaftaran`: Memastikan peserta dapat membatalkan pendaftaran yang belum diterima dan status menjadi `dibatalkan`.
  - `test_peserta_cannot_cancel_accepted_pendaftaran`: Memastikan pendaftaran yang sudah `diterima` tidak dapat dibatalkan oleh peserta (HTTP 403 / redirect error).
  - `test_submitting_application_sets_status_to_diajukan`: Memastikan submit pendaftaran selalu menghasilkan status `diajukan` dan `diajukan_at` terisi.
  - `test_peserta_blocked_from_creating_multiple_active_pendaftaran`: Menguji penegakan aturan BR-03 dan BR-17.
- [x] **Langkah 4.2**: Jalankan pengujian otomatis `php artisan test --filter=PesertaPendaftaranFlowTest` dan `php artisan test --filter=PesertaPortalWorkflowTest` hingga 100% lulus.
- [x] **Langkah 4.3**: Uji visual melalui browser subagent: periksa tampilan desktop (1440px) dan responsivitas mobile (390px) untuk memastikan estetika WOW dan kepatuhan WCAG 2.1 AA.

---

## 6. Strategi Testing

- **Unit & Feature Tests (`tests/Feature/PesertaPendaftaranFlowTest.php`)**:
  - Pengujian alur pembatalan mandiri pendaftaran (AC-03-9).
  - Pengujian pencegahan pembatalan untuk status diterima (HTTP 403).
  - Pengujian status pendaftaran lengkap menjadi `diajukan` dengan `diajukan_at` terisi (AC-03-6).
  - Pengujian penolakan pendaftaran ganda saat pendaftaran aktif masih berjalan (BR-03, BR-17).
  - Pengujian validasi MIME magic bytes dan ukuran file (AC-03-5).
- **Pengujian Browser & Visual**:
  - Drag-and-drop file upload: Menyeret file PDF ke kartu dropzone dan memastikan upload berjalan mulus.
  - Validasi ukuran client-side: Menyeret file melebihi 2 MB dan memastikan notifikasi penolakan muncul instan sebelum submit.
  - Responsivitas layar: Memastikan dropzone dan kartu berkas rapi pada desktop (1440px) dan ponsel (390px).

---

## 7. Risiko & Mitigasi

- **Risiko Manipulasi Input File Client-Side**:
  - *Mitigasi*: Validasi client-side hanya bertindak sebagai peningkatan UX (*fast feedback*). Validasi server-side di `UnggahDokumenRequest` dan `DocumentStorageService` (inspeksi magic bytes asli & batas ukuran) tetap menjadi garda utama yang tidak dapat dilewati.
- **Integritas Kuota Saat Pembatalan Mandiri**:
  - *Mitigasi*: Pembatalan mandiri oleh peserta hanya berlaku untuk status sebelum diterima. Kuota resmi baru bertambah terisi saat Admin menerbitkan keputusan `Diterima` di `PlacementService`. Oleh karena itu, pembatalan oleh peserta tidak memengaruhi integritas angka kuota.
- **Pencegahan Double-Submit**:
  - *Mitigasi*: Tombol dinonaktifkan secara instan via event submit JavaScript, dan route backend diproteksi oleh idempotensi state Eloquent.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Route dan method pembatalan pendaftaran mandiri peserta (`AC-03-9`) terimplementasi lengkap dengan modal konfirmasi dan audit trail.
- [x] Method `ajukan()` selalu mengubah status ke `diajukan` sesuai AC-03-6 dan BR-06.
- [x] Komponen dropzone berkas interaktif (drag & drop, progress bar kelengkapan, validasi ukuran real-time) terpasang di `detail.blade.php`.
- [x] Signed URL download berkas diperbarui menjadi berumur 5 menit sesuai PRD 8.4 dan TDD 5.6.
- [x] Penegakan 1 pendaftaran aktif (BR-03, BR-17) ditampilkan secara visual dan tombol pengajuan baru dinonaktifkan di `index.blade.php`.
- [x] Modal pendaftaran baru memiliki dropdown berantai yang dinamis (AC-03-2).
- [x] Rangkaian tes otomatis `PesertaPendaftaranFlowTest` dan `PesertaPortalWorkflowTest` lulus 100%.
- [x] Seluruh tampilan mematuhi Desain Sistem MagKom (WCAG 2.1 AA, Open Sans, CSS tokens, Lucide icons).

---

## 9. Catatan Perubahan & Saran Lanjutan

- **Perubahan selama pengerjaan**: Semua 4 tahap telah dieksekusi dan diverifikasi. Penyesuaian dilakukan pada audit log assertion untuk mencocokkan kolom `entitas` dan `entitas_id` di model `AuditLog`.
- **Saran Lanjutan (Out of Scope)**:
  - Notifikasi browser desktop berbasis WebSocket saat verifikator menyelesaikan peninjauan berkas.
