# Rencana Kerja: Penyederhanaan Lampiran Langsung pada Modal Logbook & Harmonisasi Tema Warna MagKom

Dokumen ini berisi spesifikasi teknis dan rencana eksekusi terstruktur untuk:
1. Menghilangkan alur tombol dropdown `+ Tambah berkas` dan sub-modal pop-up bertumpuk (*nested popup*) pada modal **Isi Logbook Mingguan** & **Perbarui Logbook**, serta menggantinya dengan input berkas PDF dan input link HTTPS yang langsung tampil di dalam formulir modal (*direct attachment inputs*).
2. Menyelaraskan seluruh palet warna, elemen visual, tombol, focus ring, dan badge pada modul Logbook Peserta agar konsisten 100% dengan **Design System MagKom** (Sleman Primary Navy `#01347C`, Sleman Midnight Navy `#003566`, Sleman Gold/Yellow `#FFCB05`/`#FDB515`).

---

## 1. Tujuan
- **Efisiensi Pengisian Jurnal**: Peserta dapat langsung memilih berkas PDF atau menempelkan tautan (*link*) tugas di dalam satu formulir modal tanpa perlu membuka popup di dalam popup (*no sub-dialog / no nested pop-up*).
- **Konsistensi Visual MagKom**: Menggantikan warna biru Tailwind generik (`#2563EB`, `#3B82F6`) dengan warna resmi MagKom Navy & Sleman Gold pada tombol, dropzone, indikator, tab, kartu progress, dan status badge logbook.
- **Integritas Backend & Validasi**: Menjaga seluruh kontrak request `LogbookRequest`, controller, dan relasi database tanpa *breaking changes*, serta memastikan validasi format PDF (maks. 2MB) dan HTTPS link tetap ditegakkan dengan ketat.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja:
1. **Sifat Lampiran**: Lampiran berkas PDF dan tautan link bersifat **opsional** bagi peserta saat mengisi atau memperbarui logbook mingguan. Peserta diperbolehkan:
   - Hanya mengisi berkas PDF, atau
   - Hanya mengisi tautan link, atau
   - Mengisi keduanya sekaligus (berkas PDF dan tautan link), atau
   - Tidak mengisi keduanya (hanya mengisi ringkasan kegiatan & capaian mingguan).
2. **Validasi File & Link**:
   - Berkas: Hanya menerima format `.pdf` dengan ukuran maksimal 2MB.
   - Tautan: Wajib menggunakan format URL aman berprotokol `https://` (validasi `SafeExternalUrlRule`).
3. **Paritas Modal Tambah & Modal Edit**:
   - Penyederhanaan berlaku seragam baik pada modal *Tambah Logbook* (`#modalTambahLogbook`) maupun modal *Edit Logbook* (`#modalEditLogbook`).
   - Pada modal Edit, apabila logbook sudah memiliki berkas lama, nama berkas yang ada (*existing file*) langsung ditampilkan dengan badge info dan tombol opsi ganti/hapus berkas.

---

## 3. Evaluasi Opsi Arsitektur Antarmuka (UI/UX)

| Aspek | Opsi A: Dropdown Berkas / Nested Sub-Modal (Existing) | Opsi B: Direct 2-Column Attachment Card (Rekomendasi) |
| :--- | :--- | :--- |
| **Alur Pengguna (User Journey)** | Pengguna harus klik `+ Tambah berkas`, memilih tipe pada dropdown, lalu membuka modal sub-dialog terpisah (`modalLogbookLink` atau `modalLogbookFile`), klik konfirmasi, baru kembali ke modal utama. | Pengguna langsung melihat field Berkas PDF dan field Link HTTPS di dalam modal utama. Tinggal klik 'Pilih Berkas' atau ketik URL tanpa modal tambahan. |
| **Jumlah Klik & Friction** | 4–6 klik (banyak konteks bertumpuk dan rawan *modal stacking z-index collision*). | 1–2 klik langsung di tempat. Sangat cepat, intuitif, dan *straightforward*. |
| **Kesesuaian dengan Permintaan User** | **Tidak Sesuai** (Pengguna secara spesifik mengeluhkan: *"berkas dan link nya bisa langsung di tampilkan gausah gitu"*). | **Sangat Sesuai** dengan permintaan pengguna dan bukti tangkapan layar. |
| **Kerumitan Kode JS/DOM** | Kompleks (mengatur submodal state, event listener sinkronisasi context tambah vs edit, dropdown click outside). | Bersih dan modular (langsung terikat ke input native form HTML5). |

> **Rekomendasi**: Dipilih **Opsi B** karena menghilangkan hambatan friksi berlapis, mematuhi prinsip *skinny & semantic UI*, dan menyederhanakan arsitektur JavaScript modal.

---

## 4. Pendekatan Teknis

### A. Rekayasa Markup Formulir Modal (`resources/views/peserta/logbook/index.blade.php`)
1. **Hapus Sub-Modal Bertumpuk**:
   - Menghapus elemen `#modalLogbookLink` dan `#modalLogbookFile` di bagian bawah Blade.
   - Menghapus tombol dropdown `.btn-dropdown-wrapper` (`#btnTambahToggleDropdown` & `#btnEditToggleDropdown`).
2. **Tambahkan Bagian Lampiran Langsung (*Direct Attachment Inputs*)**:
   - Di bawah textarea kegiatan, sediakan kontainer:
     ```html
     <div class="logbook-attachments-direct-box">
         <div class="logbook-attach-row">
             {{-- Input Berkas PDF Langsung --}}
             <div class="logbook-attach-col">
                 <label class="form-label-compact">
                     <span>Berkas Laporan (PDF)</span>
                     <span class="label-hint">Opsional, maks. 2MB</span>
                 </label>
                 <div class="logbook-file-upload-card" id="tambahFileUploadCard">
                     <input type="file" id="tambah_file_laporan" name="file_laporan" accept=".pdf" class="logbook-native-file-input">
                     <div class="logbook-file-picker-display">
                         <div class="file-picker-icon">
                             <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                         </div>
                         <div class="file-picker-text-wrap">
                             <span class="file-picker-label" id="tambahFileNameLabel">Pilih Berkas PDF</span>
                             <span class="file-picker-subtext" id="tambahFileSizeLabel">Klik untuk telusuri dokumen</span>
                         </div>
                         <button type="button" class="btn-clear-picked-file" id="btnTambahClearFile" style="display: none;" title="Hapus pilihan berkas">✕</button>
                     </div>
                 </div>
                 <div id="tambahFileFeedbackError" class="logbook-field-error" style="display: none;"></div>
             </div>

             {{-- Input Tautan / Link Langsung --}}
             <div class="logbook-attach-col">
                 <label for="tambah_link_laporan" class="form-label-compact">
                     <span>Tautan / Link Laporan</span>
                     <span class="label-hint">Opsional (Google Drive/GitHub/Figma)</span>
                 </label>
                 <div class="logbook-link-input-wrapper">
                     <span class="logbook-link-icon">
                         <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                     </span>
                     <input type="url" id="tambah_link_laporan" name="link_laporan" class="form-control-compact logbook-link-control" placeholder="https://..." value="{{ old('link_laporan') }}">
                 </div>
             </div>
         </div>
     </div>
     ```
   - Pola yang identik diterapkan pada formulir `#modalEditLogbook` (`#edit_file_laporan` dan `#edit_link_laporan`).

### B. Harmonisasi Palet Warna MagKom (`resources/css/pages/peserta/logbook-index.css`)
1. **Warna Sleman Navy (`#01347C` & `#003566`)**:
   - Header banner MagKom, ikon header modal `.modal-icon-circle`, tombol aksi utama `.btn-primary` (`#01347C`, hover `#00224f`), border aktif picker berkas, dan focus ring form fields (`border-color: #01347C; box-shadow: 0 0 0 3px rgba(1, 52, 124, 0.15)`).
2. **Warna Sleman Gold (`#FFCB05` & `#FFC300`)**:
   - Indikator minggu aktif / progress fill, aksen kartu periode berjalan, dan badge status *Menunggu Review* (`background: #FEF3C7; color: #B45309; border: 1px solid #FCD34D`).
3. **Pembersihan CSS Tidak Terpakai**:
   - Menghapus aturan CSS `.classroom-modal-backdrop`, `.classroom-modal-dialog`, `.classroom-modal-tabs`, `.classroom-modal-tab-btn`, `.classroom-dropdown-menu`, `.btn-dropdown-wrapper`, `.btn-add-attachment` dari stylesheet logbook peserta.
   - Menambahkan aturan baru untuk `.logbook-attachments-direct-box`, `.logbook-file-upload-card`, `.logbook-link-input-wrapper`, dan penyesuaian tema gelap (`[data-theme="dark"]`).

### C. Refactor Interaksi JavaScript (`resources/js/pages/peserta/logbook-index.js`)
1. **Hapus Logika Sub-Modal**:
   - Menghapus `openLogbookSubmodal()`, `closeLogbookSubmodal()`, event listener tab upload/recent sub-modal, dan tombol `open-logbook-link` / `open-logbook-file`.
2. **Tambahkan Handler Input Langsung**:
   - Listen event `change` pada `#tambah_file_laporan` dan `#edit_file_laporan`:
     - Validasi ukuran berkas (maks. 2MB = 2.097.152 bytes) dan ekstensi PDF.
     - Tampilkan nama file dan ukuran pada label kustom.
     - Tampilkan tombol silang `✕` untuk menghapus / mereset berkas yang dipilih.
   - Reset otomatis input file dan link saat modal dibuka/ditutup.
   - Pada modal Edit, isi input link dengan data lama dan tampilkan informasi file lama (jika ada).

---

## 5. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/peserta/logbook/index.blade.php` | **Modify** | Ganti tombol dropdown & sub-modal bertumpuk dengan field berkas PDF & tautan link langsung pada modal Tambah & Edit; selaraskan warna MagKom. |
| `resources/css/pages/peserta/logbook-index.css` | **Modify** | Tambah styling kontainer lampiran langsung; harmonisasi token warna Sleman Navy & Gold; bersihkan rule sub-modal; dukung dark mode. |
| `resources/js/pages/peserta/logbook-index.js` | **Modify** | Hapus logika sub-dialog & dropdown; tambahkan handler interaksi file picker langsung & tombol hapus berkas. |
| `tests/Feature/PesertaLogbookRevampTest.php` | **Modify** | Perbarui asersi view test untuk mencerminkan field berkas/link langsung dan ketiadaan sub-modal `modalLogbookFile`/`modalLogbookLink`. |

---

## 6. Langkah Implementasi Bertahap

### Fase 1: Perbarui Tampilan & Markup Modal (Blade)
- [ ] 1.1 Buka `resources/views/peserta/logbook/index.blade.php`, gantikan struktur `.logbook-attachment-section` pada `#modalTambahLogbook` dengan `.logbook-attachments-direct-box` (field file PDF langsung + field input link langsung).
- [ ] 1.2 Terapkan struktur yang sama pada `#modalEditLogbook` dengan dukungan penampil file existing.
- [ ] 1.3 Hapus elemen sub-modal `#modalLogbookLink` dan `#modalLogbookFile` dari baris bawah file Blade.
- [ ] 1.4 Periksa tombol CTA "+ Isi Logbook Mingguan" dan elemen header agar menggunakan styling MagKom Navy & Sleman Gold.

### Fase 2: Harmonisasi Warna MagKom & Styling Input Baru (CSS)
- [ ] 2.1 Buka `resources/css/pages/peserta/logbook-index.css`, tambahkan styling kelas `.logbook-attachments-direct-box`, `.logbook-file-upload-card`, `.logbook-link-input-wrapper`, dan state hover/focus MagKom Navy (`#01347C`).
- [ ] 2.2 Selaraskan warna tombol `.btn-primary` dan border focus form controls ke Sleman Navy `#01347C`.
- [ ] 2.3 Hapus selector sub-modal yang sudah usang (`.classroom-modal-backdrop`, `.btn-add-attachment`, dll.).
- [ ] 2.4 Tambahkan styling penyesuaian mode gelap (`[data-theme="dark"]`).

### Fase 3: Refactor Interaksi Frontend (JavaScript)
- [ ] 3.1 Buka `resources/js/pages/peserta/logbook-index.js`, hapus fungsi dan listener sub-modal (`openLogbookSubmodal`, `modalLogbookLink`, `modalLogbookFile`).
- [ ] 3.2 Pasang event listener `change` pada file input untuk memvalidasi batas ukuran (2MB), menampilkan nama berkas, dan memicu tombol hapus `✕`.
- [ ] 3.3 Sinkronkan modal Edit agar mengisi data file dan link existing langsung pada input yang bersangkutan.

### Fase 4: Quality Gate & Pengujian
- [ ] 4.1 Jalankan `npm run build` untuk mengompilasi stylesheet dan modul JS.
- [ ] 4.2 Perbarui file pengujian `tests/Feature/PesertaLogbookRevampTest.php`.
- [ ] 4.3 Jalankan seluruh test suite terkait: `php artisan test tests/Feature/PesertaLogbookRevampTest.php tests/Feature/PesertaClassroomTugasLogbookTest.php tests/Feature/ReleaseTwoFeaturesTest.php`.
- [ ] 4.4 Jalankan Laravel Pint: `./vendor/bin/pint --test`.
- [ ] 4.5 Jalankan PHPStan: `./vendor/bin/phpstan analyse --memory-limit=1G`.

---

## 7. Strategi Testing
- **Feature Test Pengujian UI**:
  - Memverifikasi halaman logbook menampilkan input langsung berkas PDF dan input link HTTPS.
  - Memverifikasi ketiadaan tombol dropdown bertumpuk dan sub-modal.
- **Feature Test Pengiriman Data (Store & Update)**:
  - Mengirim logbook dengan hanya teks kegiatan (lolos).
  - Mengirim logbook dengan berkas PDF (lolos).
  - Mengirim logbook dengan tautan link valid HTTPS (lolos).
  - Mengirim logbook dengan berkas PDF dan link sekaligus (lolos).
  - Mengirim file selain PDF atau ukuran > 2MB (gagal validasi 422).
  - Mengirim link tidak valid HTTP/insecure (gagal validasi 422).

---

## 8. Risiko & Mitigasi
- **Risiko**: Pengguna tidak sengaja memilih file PDF yang salah di modal.
  - **Mitigasi**: Tombol silang `✕` merah langsung tersedia di samping nama file untuk mereset pilihan dalam 1 klik tanpa harus menutup modal.
- **Risiko**: Responsivitas pada layar ponsel (mobile).
  - **Mitigasi**: `.logbook-attach-row` menggunakan `grid-template-columns: 1fr 1fr` pada layar desktop/tablet dan otomatis bertumpuk menjadi `1fr` pada layar sempit `< 640px`.

---

## 9. Kriteria Selesai (Definition of Done)
- [ ] Berkas PDF dan tautan link langsung terlihat dan dapat diisi di dalam modal Tambah & Edit tanpa membuka sub-popup.
- [ ] Sub-modal bertumpuk (`#modalLogbookLink` & `#modalLogbookFile`) telah dihapus secara bersih.
- [ ] Seluruh skema warna modul logbook selaras dengan tema resmi MagKom (Sleman Navy & Sleman Gold).
- [ ] Kompilasi aset Vite (`npm run build`) sukses tanpa error.
- [ ] Seluruh automated test suite PHPUnit, PHPStan, dan Pint berstatus **HIJAU (100% Passed)**.

