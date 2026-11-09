# Rencana Kerja: Harmonisasi Lampiran Modal Logbook Peserta ala Pembimbing (Single Header & Circle Buttons)

Dokumen ini berisi spesifikasi desain antarmuka, arsitektur teknis, dan rencana eksekusi terstruktur untuk menata ulang komponen lampiran pada modal **Isi Logbook Mingguan** (`#modalTambahLogbook`) dan **Perbarui Logbook** (`#modalEditLogbook`) agar 100% simetris, ringkas, dan selaras dengan standar pembuatan tugas pembimbing (*Google Classroom style*).

---

## 1. Analisis Masalah (Berdasarkan Feedback Pengguna & Tangkapan Layar)

1. **Ketidaksinkronan Visual (Asymmetric Two-Column Layout)**:
   - Sisi kiri (*Berkas Laporan PDF*) menggunakan kotak dashed card dengan tinggi ~64px, ikon berkas, judul, dan subteks.
   - Sisi kanan (*Tautan / Link Laporan*) menggunakan input teks solid standar dengan tinggi ~38px.
   - Dua judul terpisah (*"Berkas Laporan (PDF)"* dan *"Tautan / Link Laporan"*) membuat form tampak timpang, tidak seimbang secara proporsi, dan memenuhi ruang vertikal modal.
2. **Kebutuhan Standardisasi Visual Antarmuka**:
   - Pengguna meminta agar bagian tersebut:
     - Diringkas menjadi satu judul utama: **"Lampiran"** (dengan teks meta `(opsional)`).
     - Di bawahnya disediakan tombol aksi melingkar/ikonik: **Upload File** dan **Link**, persis seperti tampilan di halaman Buat Tugas Pembimbing (`resources/views/pembimbing/tugas/create.blade.php`).
     - Berkas atau link yang sudah dipilih/diisi tampil sebagai kartu lampiran rapi (*attached preview card*) lengkap dengan tombol hapus `✕`.

---

## 2. Rencana Arsitektur & Perubahan Komponen

### A. Markup Blade (`resources/views/peserta/logbook/index.blade.php`)
1. **Penyatuan Header & Tombol Aksi Bulat (*Circle Action Buttons*)**:
   - Mengganti `.logbook-attachments-direct-box` dua kolom dengan:
     ```html
     {{-- Panel Lampiran Terpadu (Single Header + Selector Bulat ala Pembimbing) --}}
     <div class="logbook-lampiran-section">
         <div class="logbook-lampiran-header">
             <label class="form-label-compact" style="margin-bottom: 0;">
                 <span>Lampiran</span>
             </label>
             <span class="label-hint" id="tambahLampiranMeta">(opsional) · 0 lampiran</span>
         </div>

         <div class="logbook-lampiran-selector-box">
             {{-- Tombol 1: Upload File --}}
             <div class="logbook-circle-action-item">
                 <button type="button" class="logbook-circle-btn" id="btnTambahTriggerUploadFile" aria-label="Unggah Berkas PDF" title="Unggah Berkas PDF">
                     <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                         <path d="M12 16V4M7 9l5-5 5 5M5 20h14"></path>
                     </svg>
                 </button>
                 <span class="logbook-circle-label">Upload File</span>
             </div>

             {{-- Tombol 2: Link --}}
             <div class="logbook-circle-action-item">
                 <button type="button" class="logbook-circle-btn" id="btnTambahTriggerAddLink" aria-label="Tambahkan Tautan Link" title="Tambahkan Tautan Link">
                     <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                         <path d="M10 14a4 4 0 0 0 5.7 0l3-3a4 4 0 0 0-5.7-5.7l-1 1M14 10a4 4 0 0 0-5.7 0l-3 3A4 4 0 0 0 11 18.7l1-1"></path>
                     </svg>
                 </button>
                 <span class="logbook-circle-label">Link</span>
             </div>
         </div>

         {{-- Hidden Native Inputs untuk Pengiriman Form --}}
         <input type="file" id="tambah_file_laporan" name="file_laporan" accept=".pdf" style="display: none;">
         <input type="hidden" id="tambah_link_laporan" name="link_laporan" value="{{ old('link_laporan') }}">

         {{-- Kontainer Kartu Lampiran Terpilih --}}
         <div class="logbook-attached-list" id="tambahAttachedFilesContainer"></div>
         <div class="logbook-attached-list" id="tambahAttachedLinksContainer"></div>
     </div>
     ```
   - Pola yang identik diterapkan pada `#modalEditLogbook` dengan ID yang sesuai (`#btnEditTriggerUploadFile`, `#btnEditTriggerAddLink`, `#editAttachedFilesContainer`, dsb.).

2. **Dialog Pop-up Lampiran (Paritas Pembimbing / Google Classroom)**:
   - Menyediakan pop-up modal dialog yang bersih dan terisolasi:
     - `#modalLogbookUploadFileDialog`: Dialog unggah file dengan drag & drop dropzone, info ukuran, tombol Batal, dan tombol Simpan.
     - `#modalLogbookAddLinkDialog`: Dialog input tautan HTTPS dengan field URL, validasi format, tombol Batal, dan tombol Simpan.
   - Dialog ini dipakai bersama (*reusable*) oleh modal Tambah maupun Edit logbook.

---

### B. Styling CSS (`resources/css/pages/peserta/logbook-index.css`)
- Menerapkan styling token MagKom Design System:
  - `.logbook-lampiran-selector-box`: Kotak pembungkus simetris dengan border tipis `#dadce0`, `border-radius: 8px`, `min-height: 96px`, `display: flex`, `justify-content: center`, `gap: 48px`.
  - `.logbook-circle-btn`: Lingkaran `50px × 50px`, border `#c4c9d4`, background `#ffffff`, hover state dengan MagKom Navy `#01347C`, background `#f0f5ff`, dan subtle elevation shadow.
  - `.logbook-circle-label`: Tipografi sans-serif `13px`, font-weight `600`, warna `#374151`.
  - `.logbook-attached-card`: Kartu preview lampiran terpilih dengan ikon jenis berkas/tautan, label nama/URL, ukuran format, serta tombol `✕` hapus lampiran.
  - Dukungan penuh tema gelap (*Dark Mode*).

---

### C. Logika JavaScript (`resources/js/pages/peserta/logbook-index.js`)
- Mengatur alur buka/tutup dialog pop-up saat tombol bulat diklik.
- Menghubungkan pemilihan file dan pengisian URL ke hidden input form:
  - Validasi file: Hanya PDF dan maksimal 2MB.
  - Validasi link: Wajib protokol `https://`.
- Menampilkan kartu lampiran ke `#tambahAttachedFilesContainer` atau `#tambahAttachedLinksContainer` begitu file/link ditambahkan.
- Tombol `✕` pada kartu lampiran akan mengosongkan nilai input form dan menghapus kartu dari layar.
- Pada modal Edit, file atau link yang sudah ada dari database langsung otomatis dirender sebagai kartu terlampir.

---

## 3. Verifikasi & Quality Gates
1. **PHPUnit Testing**: Menjalankan seluruh test logbook peserta (`PesertaLogbookRevampTest`).
2. **Asset Build**: Menjalankan `npm run build` untuk memvalidasi kompilasi CSS dan JS.
3. **Pint & PHPStan**: Menjalankan linter dan analisis statis.

