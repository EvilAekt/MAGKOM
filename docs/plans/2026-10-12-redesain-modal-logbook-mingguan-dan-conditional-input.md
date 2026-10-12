# Rencana Redesain Modal Logbook Mingguan: Alur Pemilihan Minggu Bertahap & Penyelarasan Tema MagKom

## 1. Tujuan
1. Mengubah alur pengisian modal Logbook Mingguan (`#modalTambahLogbook` di `resources/views/peserta/logbook/index.blade.php`) agar form isian logbook **terkunci/tersembunyi terlebih dahulu sampai peserta secara eksplisit memilih minggu magang**.
2. Memindahkan kotak **Panduan Penulisan Logbook** dari posisi atas modal ke **bagian bawah sendiri** (tepat di atas tombol aksi/footer).
3. Menyelaraskan estetika visual modal popup (header, form kontrol, badge status, footer) dengan **tema utama Design System MagKom** (aksen biru khas Kominfo, tipografi Open Sans yang konsisten, animasi halus, dan visual hierarchy yang premium).
4. Menyelaraskan pula modal edit (`#modalEditLogbook`) agar memiliki gaya visual yang seragam dan kohesif.

---

## 2. Asumsi & Analisis Kebutuhan

### Masalah UX Saat Ini:
1. **Form Langsung Terbuka Tanpa Pemilihan Sadar**: Saat tombol "Isi Logbook Mingguan" diklik, form langsung terbuka dengan minggu default yang mungkin bukan minggu yang dimaksud peserta.
2. **Posisi Panduan Mengaburkan Fokus Input**: Kotak panduan kuning/abu di paling atas mendorong input field ke bawah dan membuat pengguna harus scroll sejak awal.
3. **Modal Terasa Standar/Polos**: Header dan styling modal saat ini belum menampilkan karakter visual khas MagKom (kurang aksen biru khas Sleman Kominfo, border dan radius belum serasi dengan halaman profil/dashboard).

### Asumsi Teknis:
- Backend `LogbookController@store` dan `LogbookController@update` sudah memiliki validasi unik `minggu_ke` dan rentang tanggal (BR-09). Tidak ada perubahan skema database atau breaking API.
- Struktur data `$daftarMinggu` sudah menyediakan metadata lengkap (`minggu_ke`, `label_periode`, `status`, `logbook`, `tgl_mulai`, `tgl_selesai`).

---

## 3. Evaluasi Opsi Pendekatan Desain

| Kriteria | Opsi A: Disable Fields (Field terlihat abu-abu/disabled sampai minggu dipilih) | Opsi B (Direkomendasikan): Conditional State Reveal (Prompt Interaktif & Smooth Reveal) |
| :--- | :--- | :--- |
| **Konsep** | Semua field tetap muncul tetapi diberi atribut `disabled` dan opacity 0.5 hingga dropdown minggu dipilih. | Dropdown "Pilih Minggu Magang" ditaruh di atas dengan opsi awal `-- Pilih Minggu Magang --`. Sebelum dipilih, area form menampilkan ilustrasi/state placeholder bersih: *"Pilih minggu magang di atas untuk membuka formulir pelaporan"*. Begitu minggu dipilih, form muncul dengan animasi slide-down halus. |
| **Kelebihan** | Sederhana dalam implementasi. | **Sangat ramah pengguna, modal tidak memanjang sebelum waktunya, fokus pengguna 100% pada pemilihan minggu.** Jika minggu yang dipilih sudah pernah diisi, langsung muncul notifikasi elegan dengan tautan untuk mengedit entri tersebut. |
| **Kekurangan** | Modal tetap panjang sejak awal dan terasa ramai. | Membutuhkan handling JavaScript state transition sederhana. |
| **Estetika** | Biasa saja. | **Sangat modern, interaktif, dan premium.** |

> **Rekomendasi**: **Opsi B (Conditional State Reveal)**. Solusi ini paling sesuai dengan arahan pengguna: *"setelah di klik yang saya ss itu baru bisa mengisi log book nya jadi sebelum di klik belom bisa mengisi setelah di klik minggunya baru bisa mengisi"*.

---

## 4. Rincian Desain & Perubahan Visual yang Diusulkan

### A. Penyelarasan Tema Utama Design System MagKom pada Pop Up:
1. **Header Modal**:
   - Header berlatar gradasi halus atau aksen biru khas MagKom dengan border aksen kiri (`border-left: 4px solid var(--blue-600)`).
   - Ikon kalender/buku logbook dinas berlatar `var(--blue-tint)` dengan warna ikon `var(--blue-600)`.
   - Judul berbobot 800 (`Open Sans`) dan subjudul informatif.
   - Tombol tutup `×` dengan hover effect rounded yang lembut.
2. **Dropdown Pemilihan Minggu**:
   - Desain select field yang elevated, dengan badge periode dinamis (misal: `Periode: 11 Okt – 17 Okt 2026`).
   - Opsi default: `<option value="">-- Pilih Minggu Magang --</option>`.
   - Label status jelas jika minggu sudah pernah diisi (opsi berlabel *"— Sudah Dilaporkan"*).
3. **State Kondisional Form**:
   - **State 1 (Belum Memilih Minggu)**:
     - Area di bawah dropdown menampilkan kotak panduan kosong yang ramah:
       *Ikon kalender biru + "Pilih minggu magang di atas terlebih dahulu untuk mengisi capaian kerja mingguan Anda."*
     - Tombol Simpan disabled atau disembunyikan.
   - **State 2 (Minggu Belum Diisi - Siap Input)**:
     - Form isian terbuka otomatis:
       - Tanggal pelaporan (otomatis terisi tanggal default dalam batas minggu terkait).
       - Textarea Ringkasan Kegiatan & Capaian (dengan live counter karakter).
       - Upload berkas laporan PDF & tautan berkas/repositori.
   - **State 3 (Minggu Sudah Diisi)**:
     - Menampilkan info alert biru/amber: *"Minggu ke-X sudah Anda laporkan pada tanggal ... Ingin mengubahnya? Klik tombol Edit Entri."*
4. **Posisi Panduan Penulisan**:
   - Kotak panduan tips (`tips-box`) diletakkan di **bagian paling bawah modal** (di bawah upload berkas, tepat sebelum modal-footer).
   - Tampilan tips dirapikan dengan ikon lampu, latar `#F8FAFC`, dan border halus `#E2E8F0`.
5. **Modal Edit Logbook**:
   - Diselaraskan dengan tema header dan footer yang sama agar konsisten antar popup di portal logbook.

---

## 5. Rencana Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/peserta/logbook/index.blade.php` | Ubah | 1. Restrukturisasi `#modalTambahLogbook` dengan conditional state reveal.<br>2. Pindahkan tips-box ke bagian paling bawah form.<br>3. Perbarui CSS modal (`modal-header`, `modal-box`, `tips-box`, `form-control`) sesuai tema utama MagKom.<br>4. Perbarui JavaScript `onMingguChange()` dan handler pembukaan modal. |
| `docs/plans/2026-10-12-redesain-modal-logbook-mingguan-dan-conditional-input.md` | Buat | Dokumen master plan arsitektur dan spesifikasi modal logbook. |

---

## 6. Langkah Implementasi Terurut

- [x] **Langkah 1**: Perbarui markup HTML modal `#modalTambahLogbook` di `resources/views/peserta/logbook/index.blade.php`:
  - Tambahkan placeholder opsi default `-- Pilih Minggu Magang --`.
  - Bungkus field isian logbook ke dalam container `#formFieldsLogbook` dengan initial state `display: none`.
  - Buat `#placeholderPilihMinggu` yang aktif saat belum ada minggu dipilih.
  - Pindahkan `.tips-box` ke bagian bawah `#formFieldsLogbook` (di atas modal footer).
- [x] **Langkah 2**: Perbarui logika JavaScript di `@push('scripts')`:
  - `onMingguChange(mingguVal)`: Mengatur toggle visibilitas `#formFieldsLogbook` vs `#placeholderPilihMinggu`.
  - Tangani pre-fill atau disable jika minggu sudah diisi.
  - Atur fungsi `openTambahModal(preSelectMinggu)` agar jika dipanggil dari chip minggu tertentu, dropdown otomatis memilih minggu tersebut dan langsung menampilkan form.
- [x] **Langkah 3**: Perbarui styling CSS modal (`modal-box`, `modal-header`, `modal-footer`, `tips-box`) agar selaras dengan tema utama MagKom (Design System Bab 2: `--blue-600`, `--blue-tint`, `--neutral-800`, dsb).
- [x] **Langkah 4**: Selaraskan styling `#modalEditLogbook` agar seragam dengan modal tambah.
- [x] **Langkah 5**: Jalankan test suite logbook dan fitur peserta (`php artisan test --filter=Logbook` dan `php artisan test --filter=PesertaClassroomTugasLogbookTest`) untuk memastikan fungsi CRUD logbook tetap 100% lulus.

---

## 7. Strategi Testing
1. **Automated Feature Tests**:
   - `tests/Feature/PesertaClassroomTugasLogbookTest.php`
   - `tests/Feature/TugasLogbookTest.php`
2. **Manual & Interactive Check**:
   - Buka modal tanpa pre-select -> pastikan form belum muncul dan placeholder aktif.
   - Pilih Minggu ke-X yang belum diisi -> form muncul mulus, tips ada di bagian bawah.
   - Coba pilih minggu yang sudah diisi -> muncul indikasi bahwa minggu telah dilaporkan.
   - Uji tombol Simpan dan Batal.

---

## 8. Risiko & Mitigasi
- **Risiko**: Nilai `minggu_ke` kosong saat form disubmit jika validasi client-side lolos.
  - **Mitigasi**: Field `select` tetap memiliki atribut `required` dan form submit button terkunci jika `minggu_ke` belum dipilih.

---

## 9. Kriteria Selesai (Definition of Done)
- [x] Sebelum minggu dipilih di dropdown, form input kegiatan belum terbuka / belum bisa diisi.
- [x] Setelah minggu dipilih, form isian logbook terbuka secara mulus.
- [x] Kotak panduan/deskripsi penulisan berpindah ke bagian paling bawah form.
- [x] Tampilan popup modal bernuansa biru khas tema utama MagKom.
- [x] Seluruh pengujian fitur logbook lulus 100%.
