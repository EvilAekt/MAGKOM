# Plan: Revamp Tampilan Tugas (Referensi Google Classroom), Logbook Harian, dan Penyelarasan Sidebar Peserta

| Metadata | Keterangan |
|---|---|
| **Tanggal Pembuatan** | 11 Oktober 2026 |
| **Status Dokumen** | Selesai Diimplementasikan (Completed) |
| **Target Area** | Portal Peserta: Layout Sidebar Peserta (`layouts/peserta.blade.php`), Modul Tugas (`peserta/tugas/index.blade.php` & `show.blade.php`), Modul Logbook (`peserta/logbook/index.blade.php`) |
| **Referensi Desain** | Google Classroom (Assignment & Stream Workflows), SuperAdmin Sidebar Design System (`layouts/dashboard.blade.php`, Figma Node 560-24892), `docs/07-DESIGN-SYSTEM-MAGKOM.md` |

---

## 1. Tujuan
Meningkatkan kualitas antarmuka (*UI/UX*) Portal Peserta secara signifikan dengan tiga fokus utama:
1. **Penyelarasan Sidebar Peserta dengan Super Admin**: Menyesuaikan font family (`Open Sans`), hierarki ukuran font (`0.875rem` / `14px`), tinggi item navigasi (`min-height: 40px`), padding (`0.5rem 0.75rem`), emblem logo 40x40px, section label (`0.6875rem` / `11px`), dan scrollbar tersembunyi (*scrollbar-width: none*) agar 100% konsisten dengan sidebar Super Admin di [resources/views/layouts/dashboard.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/layouts/dashboard.blade.php).
2. **Revamp Modul Tugas Mengadopsi Pola Google Classroom**:
   - **Halaman Daftar Tugas (`peserta.tugas.index`)**: Mengadopsi tata letak *Assignment Stream* ala Google Classroom dengan tab filter status ("Semua Tugas", "Ditugaskan", "Sedang Dikerjakan", "Selesai"), kartu tugas dengan ikon clipboard khas Classroom, indikator tenggat waktu (*Due Date*), kategori/kompleksitas, dan status chip yang informatif.
   - **Halaman Detail Tugas (`peserta.tugas.show`)**: Mengadopsi arsitektur 2 kolom Google Classroom:
     - **Kolom Kiri (65% - Lembar Penugasan)**: Ikon tugas Classroom, Judul Tugas besar, nama Pembimbing Lapangan, tanggal penugasan & batas tenggat, garis pemisah horizontal, instruksi/panduan pengerjaan, dan riwayat konsultasi/reviu.
     - **Kolom Kanan (35% - Panel "Tugas Anda / Your Work")**: Widget kartu sticky dengan status badge (Ditugaskan / Diserahkan / Selesai), daftar berkas lampiran yang sudah diunggah, tombol "+ Tambah berkas / tautan", tombol aksi utama "Serahkan / Kirim Progres", form catatan pribadi ke pembimbing (*Private Comments*), serta boks evaluasi umpan balik pembimbing lapangan.
3. **Revamp Modul Logbook Harian (`peserta.logbook.index`)**:
   - Mendesain antarmuka jurnal aktivitas harian dengan layout modern:
     - **Header Ringkasan**: Periode aktif, batas toleransi mundur input hari (`logbook_maks_mundur_hari`), indikator status reviu pembimbing.
     - **Panel Kiri (Form Entri Jurnal)**: Kalender pemilihan tanggal interaktif (dengan batasan maksimal hari ini), textarea panduan rincian kegiatan, pilihan unggah berkas PDF atau link bukti tautan Google Drive/HTTPS.
     - **Panel Kanan (Timeline Feed Jurnal)**: Kartu jurnal aktivitas berformat timeline bergaya kartu catatan, status chip (Terkirim / Perlu Revisi), dan bubble box catatan umpan balik pembimbing lapangan.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Teknis:
- **A-1**: Tetap menggunakan struktur backend Laravel Blade & Vanilla CSS yang sudah ada tanpa library CSS eksternal (Tailwind tetap tidak digunakan di file ini agar sesuai arsitektur vanilla CSS yang seragam).
- **A-2**: Fungsionalitas backend di `TugasController` (`index`, `show`, `unggahProgres`) dan `LogbookController` (`index`, `store`, `update`, `unduhLampiran`) sudah lengkap dan bekerja dengan baik; perubahan difokuskan pada penyempurnaan Blade view & CSS presentation layer tanpa merusak skema database atau relasi Eloquent.
- **A-3**: Tampilan responsif pada ponsel (<1024px) secara otomatis beralih dari 2 kolom menjadi 1 kolom bertumpuk (*stacked*), di mana panel "Tugas Anda" berada di atas atau di bawah instruksi tugas secara proporsional.

### Pertanyaan Terbuka:
- Tidak ada pertanyaan pemblokir. Spesifikasi Google Classroom untuk tugas dan desain sidebar SuperAdmin sudah terdokumentasi lengkap di kode sumber proyek.

---

## 3. Evaluasi Opsi Desain

### 3.1 Penyelarasan Sidebar Peserta
- **Opsi 1: Menghubungkan peserta langsung ke layout bersama `layouts.dashboard`.**
  - *Kekurangan*: Berisiko membocorkan menu staf/admin jika conditional Blade bocor, dan struktur sub-menu accordion SuperAdmin tidak relevan untuk peserta.
- **Opsi 2: Menyelaraskan token CSS, font, ukuran, padding, dan struktur DOM sidebar di `layouts/peserta.blade.php` agar identik secara visual dengan Super Admin (Direkomendasikan).**
  - *Kelebihan*: Zero-risk privilege escalation, kode sidebar tetap bersih dan modular, namun tampilan visual, ukuran font, jarak, emblem logo, dan feel interaksi 100% kembar dengan Super Admin.
  - *Rekomendasi*: **Opsi 2**.

### 3.2 Pola Layout Detail Tugas Google Classroom
- **Opsi 1: Layout 1 Kolom Tradisional (Vertikal ke Bawah).**
  - *Kekurangan*: Pengguna harus scroll panjang ke bawah untuk menemukan tombol submit tugas; tidak menyerupai Google Classroom.
- **Opsi 2: Layout 2 Kolom Asimetris (Instruksi 65% + Panel Sticky "Tugas Anda" 35%) (Direkomendasikan).**
  - *Kelebihan*: Pola UX familiar bagi siswa/mahasiswa Indonesia yang terbiasa dengan Google Classroom. Status pengumpulan selalu terlihat di panel kanan (*sticky*), formulir upload berkas dan komentar pribadi terorganisir dengan rapi.
  - *Rekomendasi*: **Opsi 2**.

---

## 4. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
|---|---|---|
| `resources/views/layouts/peserta.blade.php` | Ubah | Menyelaraskan font, ukuran teks nav-item (`0.875rem`), tinggi tombol (`40px`), section label (`0.6875rem`), brand emblem (40x40px, radius 10px), dan scrollbar-hidden agar identik dengan SuperAdmin. |
| `resources/views/peserta/tugas/index.blade.php` | Ubah | Revamp tampilan daftar tugas menjadi *Classroom Stream*: kartu tugas ikonik, filter tab status, badge deadline, dan pratinjau ringkas. |
| `resources/views/peserta/tugas/show.blade.php` | Ubah | Revamp detail tugas menjadi 2 kolom Google Classroom: area instruksi bimbingan (kiri) dan panel interaktif "Tugas Anda / Your Work" (kanan) lengkap dengan status, upload, dan feedback pembimbing. |
| `resources/views/peserta/logbook/index.blade.php` | Ubah | Revamp halaman logbook: formulir jurnal harian modern di sisi kiri dan feed timeline kartu jurnal bimbingan di sisi kanan dengan umpan balik pembimbing. |
| `tests/Feature/PesertaClassroomTugasLogbookTest.php` | Buat | Feature test otomatis untuk memvalidasi rendering antarmuka baru, form upload progres tugas Classroom, dan entri logbook harian. |

---

## 5. Langkah Implementasi Terperinci

### Tahap 1: Penyelarasan Sidebar Peserta dengan Super Admin
- [x] Buka `resources/views/layouts/peserta.blade.php`.
- [x] Ubah struktur dan styling CSS `.sidebar`:
  - Gunakan `width: var(--sidebar-width)` (256px), background `var(--blue-700)`, border-right `1px solid rgba(255, 255, 255, 0.12)`.
  - Sembunyikan scrollbar dengan `scrollbar-width: none; -ms-overflow-style: none;` dan `::-webkit-scrollbar { display: none; }`.
- [x] Ubah emblem brand logo:
  - Kotak emblem ukuran `40px x 40px`, border radius `10px`, background `rgba(255, 255, 255, 0.15)`, border `1px solid rgba(255, 255, 255, 0.3)`.
  - Judul brand: `font-size: 1rem; font-weight: 700; color: #FFFFFF`.
  - Subtitle brand: `font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em`.
- [x] Ubah section label & nav-item link:
  - Section label: `font-size: 0.6875rem` (11px), `font-weight: 700`, uppercase, padding `0.625rem 0.75rem 0.25rem 0.75rem`, color `rgba(255, 255, 255, 0.6)`.
  - Nav item link: `min-height: 40px; padding: 0.5rem 0.75rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600; gap: 0.75rem`.
  - Active state: background `var(--neutral-0)`, color `var(--blue-700)`, icon `color: var(--blue-700); stroke-width: 2.25`.
  - Footer logout: selaraskan ukuran tombol dan ikon dengan standar Super Admin.

### Tahap 2: Revamp Daftar Tugas Peserta (`peserta.tugas.index`) ala Google Classroom
- [x] Perbarui `resources/views/peserta/tugas/index.blade.php`:
  - **Banner Kelas / Penempatan**: Header bergaya banner Google Classroom yang menampilkan Bidang Penempatan dan Pembimbing Lapangan.
  - **Filter Tab Navigasi**: Tab filter ("Semua Tugas", "Ditugaskan", "Sedang Dikerjakan", "Selesai") dengan counter jumlah tugas.
  - **Assignment Cards**:
    - Ikon bulat khas tugas Classroom (lingkaran biru dengan simbol clipboard/dokumen).
    - Judul tugas tebal dengan tautan ke detail.
    - Info tenggat waktu (misal: "Tenggat: 15 Okt, 23.59" dengan warna merah jika mendekati/terlambat).
    - Status pill di sisi kanan: "Ditugaskan" (Badge Netral/Biru), "Sedang Dikerjakan" (Badge Amber), "Selesai" (Badge Hijau).
    - Empty state ramah jika belum ada tugas pada filter yang dipilih.

### Tahap 3: Revamp Detail Tugas Peserta (`peserta.tugas.show`) 2 Kolom Google Classroom
- [x] Perbarui `resources/views/peserta/tugas/show.blade.php`:
  - **Layout Grid 2 Kolom**: Grid asimetris (Kolom Kiri 65%, Kolom Kanan 35% sticky).
  - **Kolom Kiri (Area Tugas & Instruksi)**:
    - Ikon tugas Classroom + H1 Judul Tugas.
    - Meta informasi: Dibuat oleh Pembimbing Lapangan, tanggal penugasan, batas tenggat waktu (*Due Date*), dan bobot/kompleksitas tugas.
    - Garis pemisah abu-abu tipis (divider).
    - Konten deskripsi instruksi bimbingan lengkap.
    - Bagian riwayat progres submission yang sudah dikirim sebelumnya beserta tanggapan pembimbing.
  - **Kolom Kanan (Widget "Tugas Anda / Your Work")**:
    - Kartu panel sticky "Tugas Anda" dengan status header (Ditugaskan / Diserahkan / Selesai).
    - Daftar file lampiran yang diunggah peserta.
    - Form unggah file progres baru (drag & drop / input file) + textarea catatan pengerjaan.
    - Tombol aksi utama: Tombol "Serahkan / Kirim Progres" (Turn in).
    - Box "Komentar Pribadi / Catatan ke Pembimbing": Komunikasi catatan privat peserta ke pembimbing.
    - Box "Umpan Balik Pembimbing": Box khusus evaluasi dan arahan revisi jika pembimbing telah memberikan feedback.

### Tahap 4: Revamp Halaman Logbook Harian (`peserta.logbook.index`)
- [x] Perbarui `resources/views/peserta/logbook/index.blade.php`:
  - **Info Bar Jurnal**: Banner ringkas status penempatan aktif, nama pembimbing, dan aturan batas mundur input hari.
  - **Form Entri Jurnal (Kiri)**: Input tanggal kegiatan dengan date picker, field rincian deskripsi kegiatan harian, input berkas lampiran PDF atau link Google Drive/HTTPS.
  - **Timeline Feed Logbook (Kanan)**:
    - Daftar riwayat logbook dengan urutan tanggal terbaru.
    - Kartu bergaris waktu (*timeline bullet*) dengan tanggal, status badge (Terkirim / Perlu Revisi), dan cuplikan deskripsi kegiatan.
    - Boks umpan balik pembimbing lapangan dengan latar belakang berbeda (*callout alert*).

### Tahap 5: Pengujian Otomatis & Verifikasi Visual
- [x] Buat file test `tests/Feature/PesertaClassroomTugasLogbookTest.php` untuk memverifikasi:
  - Render halaman `peserta.tugas.index` menampilkan elemen Classroom (tab filter, due date, badge).
  - Render halaman `peserta.tugas.show` menampilkan panel 2 kolom (instruksi tugas & widget "Tugas Anda").
  - Form submit progres tugas berhasil terkirim.
  - Render halaman `peserta.logbook.index` menampilkan form jurnal & riwayat timeline.
- [x] Jalankan test suite via `php artisan test --filter=PesertaClassroomTugasLogbookTest` dan seluruh test peserta yang ada.

---

## 6. Strategi Testing

| Kasus Uji | Target | Kondisi | Ekspektasi |
|---|---|---|---|
| Classroom Tugas Index | `GET /peserta/tugas` | Peserta aktif dengan 3 tugas | HTTP 200, Terlihat banner penempatan, badge status, info tenggat waktu |
| Classroom Tugas Detail | `GET /peserta/tugas/{id}` | Peserta melihat tugas aktif | HTTP 200, Terlihat layout 2 kolom (Instruksi & panel "Tugas Anda") |
| Submit Progres Classroom | `POST /peserta/tugas/{id}/progres` | Peserta upload file & catatan | Redirect 302 dengan pesan sukses, status berubah ke proses |
| Timeline Logbook Index | `GET /peserta/logbook` | Peserta dengan 3 entri logbook | HTTP 200, Terlihat formulir input & feed timeline riwayat logbook |
| Sidebar Styling Check | `GET /peserta/dashboard` | Peserta login | HTTP 200, Sidebar memiliki font `0.875rem`, emblem 40px, dan section label `0.6875rem` |

---

## 7. Risiko & Mitigasi

| Risiko | Tingkat | Mitigasi |
|---|---|---|
| Layout 2 kolom pecah di layar ponsel | Sedang | Menerapkan media query `@media (max-width: 1024px)` sehingga grid beralih mulus ke 1 kolom. |
| Ketidaksesuaian field upload file | Rendah | Menggunakan atribut form `enctype="multipart/form-data"` dan nama field `file` serta `catatan` yang sudah di-handle controller. |
| Kontras teks atau font tidak standar | Rendah | Menggunakan token resmi CSS MagKom (`var(--font-sans)`, `var(--blue-700)`, `var(--neutral-800)`). |

---

## 8. Kriteria Selesai (Definition of Done)
- [x] Sidebar pada `layouts/peserta.blade.php` memiliki font, ukuran (`0.875rem`), emblem logo (40x40px), section label (`0.6875rem`), dan scrollbar tersembunyi yang identik dengan Super Admin.
- [x] Halaman `peserta/tugas/index.blade.php` memiliki tampilan kartu tugas ala Google Classroom dengan tab filter status dan badge tenggat waktu.
- [x] Halaman `peserta/tugas/show.blade.php` memiliki layout 2 kolom ala Google Classroom (Instruksi tugas di kiri & panel "Tugas Anda" di kanan).
- [x] Halaman `peserta/logbook/index.blade.php` memiliki tampilan form jurnal dan feed timeline aktivitas harian yang rapi.
- [x] Seluruh skenario pengujian pada `PesertaClassroomTugasLogbookTest` dan suite peserta lolos 100% (Green).
