# Rencana Kerja: Perampingan Label Jalur & Redesign Modern Popup Penempatan

Dokumen rencana kerja ini disusun untuk memenuhi 2 permintaan perbaikan visual pada antarmuka **Penempatan Peserta** ([`resources/views/admin/penempatan/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/penempatan/index.blade.php)) berdasarkan tangkapan layar dan arahan pengguna.

---

## 1. Analisis & Sasaran Perubahan

### Poin 1 (Gambar SS 1): Perampingan Label Jalur pada Tabel Penempatan
- **Kondisi Saat Ini**:
  - Kolom `JALUR / BIDANG DINAS` menampilkan nama bidang, lalu di bawahnya teks panjang:
    - `Jalur Reguler (Kuota Umum)`
    - `Jalur Khusus: <Nama Mitra>`
  - Teks tersebut terlalu panjang dan membuat baris tabel terlihat ramai dan sesak.
- **Solusi**:
  - Persingkat teks penamaan jalur menjadi ringkas, tegas, dan bersih:
    - Dari `Jalur Reguler (Kuota Umum)` menjadi **`Umum`** (dengan warna abu-abu/biru slate yang tenang).
    - Dari `Jalur Khusus: <Nama Mitra>` menjadi **`Khusus`** (badge amber `#FEF3C7` yang kontras dan bersih, dengan nama mitra sebagai tooltip/atribut title bila tersedia).
  - Tampilan kolom bidang dan jalur menjadi jauh lebih rapi, minimalis, dan tidak ramai.

---

### Poin 2 (Gambar SS 2): Redesign Popup Modal "Penetapan Penempatan" Menjadi Modern & Elegan
- **Kondisi Saat Ini**:
  - Modal `#modal-tetapkan-penempatan` saat ini memiliki:
    1. **Alert Info Biru Panjang**: 3 baris teks penjelasan berulang (*"Penetapan Pembimbing Magang: Bidang dinas telah otomatis terhubung..."*).
    2. **Label Field Bertele-tele**: *"Unit Bidang Dinas (Otomatis dari Registrasi)"* dengan subteks *"Terhubung langsung dengan kuota jurusan pilihan pendaftar."*.
    3. **Subteks Select Pembimbing**: *"Daftar pembimbing lapangan dilengkapi dengan asal bidang dan beban mentee aktif saat ini."*.
    4. **Tombol Terlalu Panjang**: *"Tetapkan Pembimbing & Aktifkan"*.
  - Desain modal terkesan kuno, penuh dengan teks penjelasan instruksional yang memenuhi layar.
- **Solusi Redesign (Modern, Elegan, Tertata, & Terstruktur)**:
  1. **Hapus Alert Info Biru**: Menghilangkan seluruh kotak alert biru yang memenuhi modal, langsung menampilkan konten esensial.
  2. **Header Modal Bersih**:
     - Judul: **Penetapan Penempatan**
     - Subjudul: Menampilkan nama peserta dan jurusannya secara proporsional (`Peserta: [Nama] • [Jurusan]`).
  3. **Kartu Bidang Dinas Minimalis**:
     - Label: **Bidang Dinas** (ringkas & elegan).
     - Kotak informasi bergaya card Sleman Light `#F8FAFC` dengan icon layout modern, menampilkan nama bidang dinas secara tegas.
     - Hapus seluruh subteks redundan.
  4. **Pilihan Pembimbing Lapangan Fokus**:
     - Label: **Pembimbing Lapangan \***.
     - Select dropdown berukuran ideal (height: 42px, radius: 8px) tanpa subteks berlebihan.
  5. **Tombol Aksi Proporsional**:
     - Tombol Batal (secondary outline lembut).
     - Tombol Submit disederhanakan menjadi: **`Tetapkan Pembimbing`** (primary Sleman Navy/Blue `#0641CC` berbayangan halus).
  6. **Struktur Modal Kompak**:
     - Lebar modal dirampingkan dari `580px` menjadi `480px` dengan sudut melengkung `14px` dan bayangan lembut elevasi modern.

---

## 2. Kepatuhan Aturan ([`.agents/rules/AGENTS.md`](file:///d:/laragon/www/MAGKOM/.agents/rules/AGENTS.md))

- **Blade Read-Only Default**: Hanya merapikan markup HTML modal dan label tabel di `resources/views/admin/penempatan/index.blade.php`.
- **Zero Breaking Logic**: Semua ID elemen form modal (`modal-tetapkan-penempatan`, `tetapkan-pendaftaran-id`, `tetapkan-bidang-id`, `tetapkan-bidang-display`, `select-tetapkan-pembimbing`) tetap dipertahankan sehingga handler JavaScript `openTetapkanModal` dan submit POST backend tetap 100% normal.
- **Zero Inline Script**: Tidak menambahkan inline script atau inline event handler baru.

---

## 3. Rencana Perubahan Berkas

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| [`resources/views/admin/penempatan/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/penempatan/index.blade.php) | **Modify** | 1. Baris 1075–1083: Persingkat label jalur menjadi `Umum` dan badge `Khusus`.<br>2. Baris 1616–1688: Redesign modal `#modal-tetapkan-penempatan` menjadi modern, bersih, bebas dari alert/teks berlebih. |

---

## 4. Langkah-Langkah Implementasi Bertahap

- [x] **Fase 1: Perampingan Label Jalur di Tabel Penempatan (Gambar 1)**
  - [x] 1.1 Edit `resources/views/admin/penempatan/index.blade.php`: Ganti teks `Jalur Reguler (Kuota Umum)` menjadi `Umum` dan `Jalur Khusus: ...` menjadi badge `Khusus`.

- [x] **Fase 2: Redesign Modern Modal Penetapan Penempatan (Gambar 2)**
  - [x] 2.1 Hapus blok alert info biru pada `#modal-tetapkan-penempatan`.
  - [x] 2.2 Sederhanakan label field menjadi `Bidang Dinas` dan `Pembimbing Lapangan`.
  - [x] 2.3 Rapikan kartu tampilan bidang dinas dan dropdown pembimbing.
  - [x] 2.4 Ganti teks tombol menjadi `Tetapkan Pembimbing`.

- [x] **Fase 3: Verifikasi & Quality Gates**
  - [x] 3.1 Jalankan feature test penempatan: `vendor/bin/phpunit tests/Feature/AdminChildPagesVisualTest.php tests/Feature/AdminChildPagesExtendedVisualTest.php` (9 passed).
  - [x] 3.2 Jalankan Laravel Pint: `vendor/bin/pint --test` (passed).
  - [x] 3.3 Jalankan Vite build: `npm run build` (sukses).
  - [x] 3.4 Jalankan full PHPUnit test suite: `vendor/bin/phpunit` (719 passed, 0 failures).

---

## 5. Kriteria Selesai (Definition of Done)

- [x] Kolom Jalur pada tabel penempatan hanya menampilkan `Umum` dan `Khusus` tanpa teks panjang yang ramai.
- [x] Modal Penetapan Penempatan tampil modern, terstruktur, elegan, bersih, dan bebas dari teks instruksional yang berlebihan.
- [x] Seluruh 719 test suite PHPUnit tetap 100% hijau tanpa regresi.
- [x] Format kode Pint dan build aset Vite sukses 100%.

