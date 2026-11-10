# Rencana Kerja (Plan): Redesign Pop-Up Modal Isi & Edit Logbook Peserta

**Tanggal Dibuat:** 10 November 2026  
**Status:** Selesai Diimplementasikan (Verified)  
**Tujuan Dokumen:** Perencanaan terstruktur perombakan visual dan arsitektur CSS/Blade antarmuka pop-up modal **Isi Logbook Mingguan** (`#modalTambahLogbook`) dan **Perbarui Logbook Mingguan** (`#modalEditLogbook`) pada portal peserta (`/peserta/logbook`), mengacu pada standar visual pop-up **Pengajuan Bulan Magang** (`#pendaftaranModal` di `/peserta/pendaftaran`) serta menyelesaikan isu scrollbar ganda, pemotongan konten (*clipping*), dan tombol footer yang terpotong.

---

## 1. Latar Belakang & Analisis Masalah

Berdasarkan tangkapan layar pengguna (`media_1794307357589_87c9646d.png` dan `media_1794307357588_5ae21bbc.png`) dan komparasi langsung dengan modal pengajuan bulan magang (`resources/views/peserta/pendaftaran/index.blade.php`):

### 1.1 Masalah pada Pop-Up Logbook Saat Ini
1. **Pemotongan Konten & Scrollbar Internal**:
   - Container modal (`.modal-box-compact`) memiliki `max-height: 90vh; overflow-y: auto;` pada container paling luar.
   - Hal ini menyebabkan scrollbar vertikal muncul di tepi kanan kartu modal, memotong tombol *close* `×`, serta memotong separuh tombol aksi footer (`Kirim Logbook Mingguan` dan `Simpan Perubahan`) di bagian bawah layar.
2. **Desain Form & Input Belum Selaras dengan Referensi Pendaftaran**:
   - Modal pendaftaran magang (`.pendaftaran-modal-*`) memiliki hierarki visual yang jauh lebih elegan:
     - Header ber-badge ikon squircle Sleman navy transparan (`rgba(2, 39, 93, 0.08)`), judul tegas `#003566`, dan subjudul bernuansa slate `#64748B`.
     - Garis pemisah header (`border-bottom: 1px solid #E2E8F0; padding-bottom: 14px;`) dan footer (`border-top: 1px solid #E2E8F0; padding-top: 16px;`).
     - Kartu ringkasan konteks aktif (`.pendaftaran-summary-card`) berlatar abu-abu lembut `#F8FAFC` yang menampilkan informasi penempatan dan pembimbing secara profesional.
     - Kontrol input dengan border `1.5px solid #CBD5E1`, border radius `8px`, tinggi `42px`, dan fokus cincin `#01347C`.
     - Tombol aksi primer `#003566` dengan ikon centang dan tombol sekunder putih-abu yang bersih.
3. **Panel Lampiran yang Terlalu Makan Tempat**:
   - Box selektor lampiran (`.logbook-lampiran-selector-box`) terlalu tinggi sehingga memicu overflow vertikal. Perlu dikompakkan dan diintegrasikan dengan gaya kartu referensi pendaftaran.

---

## 2. Asumsi Kerja & Batasan Teknis

1. **Color Palette Resmi MagKom Sleman**:
   - Navy Dominan: `#003566`, `#01347C`, `#02275D`
   - Aksen Emas/Kuning: `#FFC300`, `#FFCB05`
   - Slate & Netral: `#FFFFFF`, `#F8FAFC`, `#F1F5F9`, `#E2E8F0`, `#CBD5E1`, `#64748B`, `#334155`, `#0F172A`
   - Status/Error: `#DC2626`, `#FEE2E2`
2. **Preservasi Logika Backend & Uji Otomatis**:
   - Semua nama field form (`minggu_ke`, `tanggal`, `kegiatan`, `file_laporan`, `link_laporan`) tetap 100% sama.
   - Atribut ID penting untuk binding JavaScript dan pengujian feature test (`PesertaLogbookRevampTest` & `PesertaClassroomTugasLogbookTest`):
     - `#modalTambahLogbook`, `#modalEditLogbook`
     - `#formTambahLogbook`, `#formEditLogbook`
     - `#tambah_minggu_ke`, `#tambah_tanggal`, `#tambah_kegiatan`, `#tambah_char_count`, `#btnSubmitTambah`
     - `#tambah_file_laporan`, `#tambah_link_laporan`, `#btnTambahTriggerUploadFile`, `#btnTambahTriggerAddLink`
     - `#edit_minggu_ke`, `#edit_tanggal`, `#edit_kegiatan`, `#edit_char_count`
     - `#edit_file_laporan`, `#edit_link_laporan`, `#btnEditTriggerUploadFile`, `#btnEditTriggerAddLink`
     - `#modalLogbookUploadFileDialog`, `#modalLogbookAddLinkDialog`
3. **Aturan Frontend Repositori**:
   - Nol `<style>` dan `<script>` inline di Blade.
   - 100% penataan gaya dikelola di `resources/css/pages/peserta/logbook-index.css` dan dikompilasi melalui Vite.

---

## 3. Evaluasi Opsi Pendekatan Teknis

### Opsi A (Direkomendasikan): Mengadopsi Arsitektur Struktur Modal Pendaftaran (`pendaftaran-modal-*`) yang Diadaptasi untuk Logbook
- **Deskripsi**:
  - Merekayasa ulang layout modal logbook menggunakan arsitektur flex 3-zona:
    1. **Fixed Header**: Berada di atas, tidak ikut tergulung, berisi badge ikon squircle, judul tebal Sleman navy, subjudul, dan tombol close `×`.
    2. **Scrollable Body**: Hanya area form body yang dapat digulung jika layar sempit (`overflow-y: auto; max-height: calc(85vh - 160px);`), dilengkapi padding kanan yang memadai agar scrollbar tidak menimpa elemen form.
    3. **Fixed Footer**: Terkunci di bagian bawah kartu modal, selalu terlihat, berisi tombol `Batal` dan `Kirim Logbook Mingguan` / `Simpan Perubahan`.
  - Menambahkan **Kartu Ringkasan Konteks** (`.logbook-summary-card`) di bagian atas form seperti pada pendaftaran magang untuk menampilkan Bidang, Pembimbing, dan Status Penempatan/Minggu.
  - Memperbarui gaya input (`select`, `input date`, `textarea`) agar identik dengan kontrol di pendaftaran (`border: 1.5px solid #CBD5E1; border-radius: 8px; height: 42px;`).
- **Kelebihan**:
  - Mengeliminasi 100% masalah pemotongan tombol footer dan scrollbar ganda.
  - Menyamakan estetika dan konsistensi UX antarmuka peserta di seluruh modul MagKom.
  - Mempertahankan backward compatibility 100% dengan skrip JavaScript dan PHPUnit feature test.
- **Kekurangan**: Membutuhkan pembaruan struktur wrapper internal di Blade dan penyesuaian CSS terkait.

### Opsi B: Hanya Memperlebar Modal Melalui CSS Tanpa Mengubah Struktur Layout
- **Deskripsi**: Menaikkan `max-width` dan mengecilkan font melalui CSS saja tanpa memisahkan header/body/footer.
- **Kelemahan**: Pada layar laptop dengan resolusi vertikal terbatas (misal 768px), tombol footer tetap akan terdorong ke bawah dan terpotong di dalam container overflow.
- **Keputusan**: Dipilih **Opsi A**.

---

## 4. Rincian Perubahan File

| File | Tipe Perubahan | Rincian Perubahan |
| :--- | :--- | :--- |
| `resources/views/peserta/logbook/index.blade.php` | Markup Semantik | 1. Restrukturisasi `#modalTambahLogbook` dan `#modalEditLogbook` dengan layout 3-zona (Fixed Header, Scrollable Body, Fixed Footer).<br>2. Tambahkan kartu ringkasan konteks penempatan dan pembimbing (`.logbook-summary-card`).<br>3. Selaraskan styling label, input, textarea, char counter, dan tombol aksi sesuai pattern pendaftaran modal.<br>4. Pertahankan seluruh ID form, input tersembunyi, dan event handler JS. |
| `resources/css/pages/peserta/logbook-index.css` | Styling CSS | 1. Implementasikan kelas modal gaya pendaftaran untuk logbook (`.modal-backdrop`, `.modal-card-logbook`, `.modal-header-logbook`, `.modal-body-logbook`, `.modal-footer-logbook`).<br>2. Styling kartu ringkasan konteks (`.logbook-summary-card`, `.logbook-summary-badge`).<br>3. Styling form controls modern (`.input-logbook`, `.select-logbook`, `.textarea-logbook`, `.label-logbook`, `.hint-logbook`).<br>4. Perbaiki panel lampiran terpadu agar lebih proporsional dan tidak menyebabkan tinggi berlebih.<br>5. Pastikan tombol footer (`.btn-modal-cancel`, `.btn-modal-submit`) tampil kontras, proporsional, dan tidak terpotong.<br>6. Tambahkan dukungan dark mode lengkap. |
| `resources/js/pages/peserta/logbook-index.js` | Verifikasi JS | Memastikan integrasi `openTambahModal`, `closeTambahModal`, `openEditModal`, `closeEditModal`, `updateCharCount`, dan attachment upload tetap berjalan mulus tanpa error konsol. |

---

## 5. Rencana Langkah Implementasi

### Fase 1: Pembaruan Styling CSS di `resources/css/pages/peserta/logbook-index.css`
- [x] **Langkah 1.1**: Tulis styling modal 3-zona dengan fixed header, scrollable body, dan fixed footer (`.modal-card-logbook`).
- [x] **Langkah 1.2**: Implementasikan styling badge ikon squircle Sleman navy (`.modal-icon-badge`), judul, subjudul, dan tombol close `×`.
- [x] **Langkah 1.3**: Terapkan styling `.logbook-summary-card` (informasi bidang, pembimbing, dan badge status).
- [x] **Langkah 1.4**: Selaraskan styling input fields, select minggu, input date, dan textarea dengan border `1.5px solid #CBD5E1` serta focus ring navy `#01347C`.
- [x] **Langkah 1.5**: Sempurnakan panel lampiran terpadu (selector tombol bulat upload berkas & link) agar lebih kompak, rapi, dan simetris.
- [x] **Langkah 1.6**: Terapkan styling tombol footer `.btn-modal-cancel` dan `.btn-modal-submit` (Sleman navy `#003566` dengan ikon centang).
- [x] **Langkah 1.7**: Tambahkan Dark Mode variables dan breakpoint responsive mobile (< 640px).

### Fase 2: Penyesuaian Markup Blade di `resources/views/peserta/logbook/index.blade.php`
- [x] **Langkah 2.1**: Terapkan struktur modal baru pada `#modalTambahLogbook`:
  - Header dengan icon badge, judul, subjudul, dan close button.
  - Kartu konteks ringkasan penempatan peserta.
  - Grid 2 kolom Minggu Magang & Tanggal Pelaporan ber-hint elegan.
  - Textarea kegiatan dengan char counter pill.
  - Panel lampiran berkas PDF dan link HTTPS.
  - Footer dengan tombol Batal dan Kirim Logbook Mingguan (bebas dari pemotongan).
- [x] **Langkah 2.2**: Terapkan struktur modal baru pada `#modalEditLogbook` dengan pola identik.
- [x] **Langkah 2.3**: Verifikasi sub-modal dropzone `#modalLogbookUploadFileDialog` dan `#modalLogbookAddLinkDialog` agar serasi.

### Fase 3: Kompilasi Aset, Pengujian, dan Verifikasi Quality Gate
- [x] **Langkah 3.1**: Jalankan kompilasi aset Vite (`npm run build`).
- [x] **Langkah 3.2**: Jalankan pengujian feature test suite:
  ```powershell
  & "D:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" artisan test tests/Feature/PesertaLogbookRevampTest.php tests/Feature/PesertaClassroomTugasLogbookTest.php
  ```
- [x] **Langkah 3.3**: Jalankan linter Laravel Pint (`./vendor/bin/pint --test`).
- [x] **Langkah 3.4**: Verifikasi visual interaktif pop-up modal di browser pada `http://127.0.0.1:8000/peserta/logbook`.

---

## 6. Kriteria Selesai (Definition of Done)

- [x] Pop-up modal "Isi Logbook Mingguan" dan "Perbarui Logbook Mingguan" memiliki tampilan modern yang selaras dengan pop-up "Pengajuan Bulan Magang".
- [x] Tidak ada lagi scrollbar ganda internal atau pemotongan tombol footer di bagian bawah modal.
- [x] Terdapat kartu ringkasan konteks penempatan (bidang & pembimbing) pada pop-up modal.
- [x] Seluruh form control memiliki styling Sleman yang tajam, kontras, dan ramah pengguna.
- [x] Semua fungsionalitas pengiriman form, pemilihan minggu, penghitungan karakter, dan lampiran tetap berfungsi 100%.
- [x] Seluruh pengujian fitur (`PesertaLogbookRevampTest` & `PesertaClassroomTugasLogbookTest`) lulus 100%.
- [x] Laravel Pint dan kompilasi Vite lulus tanpa error.

