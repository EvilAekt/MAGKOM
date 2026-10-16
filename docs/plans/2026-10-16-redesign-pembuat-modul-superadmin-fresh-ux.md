# Plan: Redesain Total Pembuat Modul SuperAdmin (Fresh, Modern Studio & Anti-AI Slop)

- **Tanggal**: 2026-10-16
- **Status**: Completed (Terimplementasi & Terverifikasi)
- **Author**: Antigravity (web-planning & Lead UI/UX Architect)

---

## 1. Tujuan
Melakukan perombakan menyeluruh (*revamp/redesign*) pada antarmuka **Pembuat Modul SuperAdmin** (`resources/views/superadmin/modul-builder/`) dari tampilan lama yang kaku, penuh istilah teknis internal (*jargon overloading*), dan terkesan *"AI slop"* (ratusan baris *inline CSS*, form panjang bertumpuk, tabel matriks checkbox mentah ala tahun 2000-an), menjadi **Modern Modul Studio & Schema Designer** yang *clean*, elegan, intuitif, berestetika tinggi (terinspirasi dari platform modern seperti *Supabase Studio*, *Airtable*, dan *Linear*), serta dilengkapi dengan **Live Interactive Preview** yang memungkinkan SuperAdmin melihat pratinjau tabel dan formulir secara *real-time* saat merancang kolom.

---

## 2. Asumsi & Pertanyaan Terbuka

### 2.1 Asumsi
1. **Fungsionalitas Backend Tetap Utuh**: Logika controller (`ModulBuilderController`), routing, API inspeksi tabel (`api.inspect`), dan model database (`ModulDinamis`, `ModulDinamisField`) sudah stabil dan lolos pengujian (`ModulDinamisBasefwParityTest`). Redesain ini berfokus pada **lapisan presentasi (UI/UX), struktur Blade view, interaktivitas JavaScript, dan CSS styling**.
2. **Design System & Dark Mode**: Tampilan baru akan mengadopsi token warna resmi MagKom/Sleman (palet biru dinas yang harmonis, *neutral slate*, dan mode gelap berbasis `[data-theme="dark"]`) tanpa lagi menggunakan *inline styles* yang berantakan.
3. **Formulir Tetap Kompatibel**: Nama atribut `name="..."` pada elemen form HTML (seperti `fields[i][nama_kolom]`, `fields[i][tipe_data]`, `target_roles[]`, dll.) tetap dipertahankan agar payload POST yang diterima backend tidak mengalami *breaking changes*.

### 2.2 Pertanyaan Terbuka
- Tidak ada pertanyaan pemblokir. Kebutuhan visual dan kenyamanan pengguna (*user-friendliness*) sudah sangat jelas: menghilangkan kesan kaku/AI-slop dan menggantinya dengan pengalaman studio modern.

---

## 3. Pendekatan Desain & Arsitektur (Anti-AI Slop Manifesto)

### 3.1 Mengapa Tampilan Saat Ini Terasa "AI Slop" & Canggung?
1. **Jargon Overload**: Label form mengekspos istilah teknis programmer internal, seperti *"Reverse-Engineering Basefw"*, *"Basefw CrudGenerator Parity"*, *"ckPublicRead"*, dan *"Matriks Sub-Aksi (CodeModule)"*. Pengguna SuperAdmin merasa asing dan bingung.
2. **Form Bertumpuk Tanpa Hirarki**: Pengguna dihadapkan pada tumpukan card kolom panjang dengan input teks mentah, tombol hapus merah menyolok, dan 3 checkbox di bawahnya tanpa indikator visual tipe data yang ramah.
3. **Tabel Matriks Mentah**: Matriks izin 30 checkbox disajikan seperti phpMyAdmin jadul, melelahkan mata dan membingungkan untuk dikonfigurasi.
4. **Ketiadaan Visual Feedback**: Pengguna tidak tahu bagaimana hasil tabel atau form yang sedang mereka buat sebelum tombol "Simpan" ditekan.
5. **Inline Styles Spaghetti**: Ratusan atribut `style="..."` membuat kode tidak terawat, respon hover kaku, dan adaptasi tema gelap terasa tambal sulam.

### 3.2 Solusi Desain Baru: "Modul Studio 2-Column Split Canvas"

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ MODUL STUDIO HEADER: [Nama Modul] | [Ikon] | Status: Draf Interaktif   [Batal] [Terbitkan] │
├─────────────────────────────────────────┬──────────────────────────────────────────────┤
│ KIRI (58%): SCHEMA DESIGNER WORKSPACE   │ KANAN (42%): LIVE INTERACTIVE PREVIEW       │
│                                         │                                              │
│ [Tab 1: Kolom] [Tab 2: Info] [Tab 3: Izin]  │  [Tab: Pratinjau Tabel] [Tab: Form Modal]    │
│ ─────────────────────────────────────── │ ──────────────────────────────────────────   │
│ Quick-Add Chips:                        │ ┌──────────────────────────────────────────┐ │
│ [+ Teks] [+ Angka] [+ Tanggal] [+ File] │ │ 🔍 Cari data...           [+ Tambah Data]│ │
│                                         │ │ ---------------------------------------- │ │
│ Daftar Kolom (Draggable & Expandable):  │ │ #  | NAMA BARANG | JUMLAH | TGL MASUK    │ │
│ ≡ [Teks]   Nama Barang      [⚙ Edit] [🗑]│ │ 1  | Laptop Dell | 15 unit| 16 Okt 2026   │ │
│ ≡ [Angka]  Jumlah Unit      [⚙ Edit] [🗑]│ │ 2  | Router WiFi | 8 unit | 14 Okt 2026   │ │
│ ≡ [Date]   Tanggal Masuk    [⚙ Edit] [🗑]│ └──────────────────────────────────────────┘ │
│                                         │                                              │
│ Mode Sumber Data (Pill Switcher):       │ *Pratinjau otomatis ter-update secara instan │
│ (•) Buat Skema Baru  ( ) Hubungkan Tabel│ saat Anda menambah kolom di sisi kiri!       │
└─────────────────────────────────────────┴──────────────────────────────────────────────┘
```

#### Pilar-Pilar Perubahan Visual:
1. **Human-Centric Copywriting**:
   - Mengganti *"Mode 1: Formulir Dinamis Bebas"* ➔ **"Rancang Skema Baru"** (dengan ikon kuas desain).
   - Mengganti *"Mode 2: Impor Tabel Basis Data (Reverse-Engineering Basefw)"* ➔ **"Hubungkan Tabel Database"** (dengan ikon basis data).
   - Mengganti *"ckPublicRead"* ➔ Saklar elegan **"Buka Akses Publik (Tanpa Login)"** dengan badge status visual.
   - Mengganti *"Matriks Sub-Aksi (CodeModule)"* ➔ **Preset Hak Akses Cepat** (*Full Access*, *Operator*, *Viewer*) ditambah opsi *Custom Detail* yang rapi.
2. **Visual Type Chips & Quick-Add Toolbar**:
   - Tombol cepat sekali klik untuk menambahkan tipe kolom umum: `[+ Teks]`, `[+ Angka]`, `[+ Tanggal]`, `[+ Unggah Berkas]`, `[+ Pilihan Dropdown]`, `[+ Relasi]`.
   - Setiap baris kolom menampilkan badge berwarna lembut sesuai tipe data (misal: Biru untuk Teks, Ungu untuk Angka, Hijau untuk Tanggal, Oranye untuk Berkas).
3. **Live Interactive Preview Canvas (Kanan)**:
   - Panel kanan menayangkan mock UI dari hasil rancangan secara *real-time*:
     - **Mode Tabel**: Menampilkan preview tabel data lengkap dengan kolom header, pagination dummy, dan tombol aksi.
     - **Mode Form Modal**: Menampilkan preview form input persis seperti yang akan dilihat oleh staf/admin saat mengisi data.
4. **Rich Visual Icon Picker Studio (40+ Ikon Visual Asli Sesuai Identitas MagKom & Sleman)**:
   - Menggantikan dropdown teks membosankan (`<select name="ikon">`) dengan **Interactive Icon Picker Modal/Grid Gallery**.
   - **Aturan Ketat Desain & Anti-Ikon Aneh**:
     - 🎯 **Gaya Visual Seragam**: 100% menggunakan gaya *stroke-based vector line icon* standar Lucide/Feather (garis halus 1.75px–2px, sudut `round`, tanpa isian tebal, tanpa efek 3D kartun, dan tanpa emoji).
     - 🏛️ **Konteks Relevan (Diskominfo & MagKom Only)**: Seluruh 40+ ikon dikurasi khusus untuk domain pemerintahan, teknologi informasi, arsip data, administrasi publik, dan inventaris dinas. *Dilarang keras memasukkan ikon acak/aneh yang tidak relevan (seperti hewan, makanan, gaming, atau simbol main-main).*
     - 🎨 **Palet Warna Harmonis (Anti-Warna Aneh/Norak)**: Ikon wajib mewarisi `currentColor` berbasis token MagKom resmi (`var(--blue-700)` untuk aksen aktif, `var(--neutral-700)` untuk netral, dan `#38BDF8`/`#E2E8F0` pada tema gelap). Kontainer ikon menggunakan warna dasar lembut (`rgba(7, 81, 255, 0.06)`). *Dilarang keras menggunakan gradasi neon, warna pelangi acak, atau warna neon yang menusuk mata.*
   - **Koleksi 40+ Ikon Terkurasi Sesuai Kategori**:
     - 💻 **Infrastruktur & Jaringan TI**: `laptop`, `server`, `cpu`, `hard-drive`, `wifi`, `terminal`, `smartphone`, `monitor`, `printer`, `shield`.
     - 📁 **Data & Berkas Administrasi**: `database`, `table`, `file-text`, `folder`, `book`, `clipboard`, `archive`, `layers`.
     - 👥 **Manajemen, Tim & Institusi**: `users`, `user-check`, `user-plus`, `briefcase`, `award`, `building`, `compass`, `flag`.
     - ⚙️ **Layanan Publik & Operasional**: `calendar`, `clock`, `check-circle`, `activity`, `bell`, `mail`, `phone`, `map-pin`, `tool`, `sliders`.
     - 📦 **Inventaris & Aset Logistik**: `box`, `package`, `tag`, `credit-card`, `qr-code`, `barcode`.
   - Dilengkapi *Live Icon Search* ("Cari ikon..."), status terpilih dengan ring halo lembut, dan sinkronisasi instan ke badge avatar modul serta preview canvas!
   - Di katalog modul (`index.blade.php`), kartu modul akan me-render bentuk visual asli sesuai pilihan ikon pengguna (tidak lagi hardcoded kotak statis).
5. **Katalog Modul (`index.blade.php`) Modern Card Grid**:
   - Mengubah halaman daftar modul menjadi antarmuka katalog aplikasi layaknya *App Store / Workspace Dashboard*.
   - Filter cepat berdasarkan: *Semua*, *Publik*, *Privat*, *Tabel Database*.
   - Stat chip ringkas di atas: Total Modul Aktif, Modul Publik Terbuka, Total Baris Data Tersimpan.
   - Kartu modul dilengkapi avatar ikon dinamis asli, penanda badge role (`SuperAdmin`, `Admin`, dsb.), jumlah data realtime, dan menu aksi dropup modern.

---

## 4. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `app/Http/Controllers/SuperAdmin/ModulBuilderController.php` | Modify | Memperluas pustaka `AVAILABLE_ICONS` menjadi 40+ ikon tematik terstruktur. |
| `app/Models/ModulDinamis.php` | Modify | Menambahkan helper method `getIconSvgAttribute()` untuk merender SVG ikon secara dinamis di seluruh view. |
| `resources/views/superadmin/modul-builder/create.blade.php` | Modify | Redesain total form pembuatan modul menjadi Studio 2-kolom dengan Visual Icon Picker Modal, live preview, dan chips builder. |
| `resources/views/superadmin/modul-builder/edit.blade.php` | Modify | Menyelaraskan form edit skema dengan Visual Icon Picker dan layout Studio baru. |
| `resources/views/superadmin/modul-builder/index.blade.php` | Modify | Redesain katalog modul dengan kartu modern, avatar ikon dinamis asli, stat chip, filter tab, dan tombol aksi terpadu. |
| `public/css/modul-studio.css` | Create | File stylesheet terdedikasi untuk Modul Studio & Icon Picker agar bebas dari inline-styles dan teroptimasi mode gelap. |

---

## 5. Langkah Implementasi Bertahap

- [x] **Langkah 1: Pembuatan File CSS Terdedikasi (`public/css/modul-studio.css`)**
  - Definisikan layout 2-kolom studio (`.studio-layout`, `.studio-workspace`, `.studio-preview`).
  - Definisikan styling komponen kartu kolom, type badge, quick-add toolbar, dan switch toggle.
  - Definisikan styling komponen **Visual Icon Picker** (grid galeri ikon, search input, active halo state, preview trigger button).
  - Definisikan styling preview mockup (tabel data dummy dan form modal interaktif).
  - Terapkan dukungan tema gelap (`[data-theme="dark"]`) yang kontras dan nyaman di mata tanpa warna neon menyilaukan.

- [x] **Langkah 2: Perluasan Pustaka Ikon & Dynamic SVG Helper (`ModulBuilderController.php` & `ModulDinamis.php`)**
  - Perluas konstanta `AVAILABLE_ICONS` menjadi 40+ ikon dengan label dan metadata kategori.
  - Buat helper method `getIconSvgAttribute()` di model `ModulDinamis` untuk menghasilkan SVG inline yang bersih dan fleksibel.

- [x] **Langkah 3: Redesain Halaman Buat Modul (`create.blade.php`) & Integrasi Visual Icon Picker**
  - Ganti struktur lama dengan layout Studio 2-Kolom.
  - Pasang komponen **Visual Icon Picker Gallery**: tombol pemicu menampilkan ikon aktif besar, membuka modal/dropdown grid 40+ ikon dengan pencarian instan.
  - Sederhanakan switcher mode sumber data (Buat Baru vs Hubungkan Tabel) dengan desain tab pill modern.
  - Bangun quick-add toolbar untuk menyisipkan kolom hanya dengan satu klik (`[+ Teks]`, `[+ Tanggal]`, dll.).
  - Buat baris kolom yang ringkas (*compact*), dilengkapi badge tipe dan tombol toggle pengaturan lanjutan (*collapsible*).
  - Integrasikan skrip JavaScript untuk sinkronisasi live preview (perubahan nama, tipe, dan ikon langsung tercermin di sisi kanan).
  - Rapikan matriks perizinan menjadi kartu role dengan preset cerdas (*Full Control*, *Editor*, *Viewer*).

- [x] **Langkah 4: Redesain Halaman Edit Modul (`edit.blade.php`)**
  - Terapkan layout, komponen Icon Picker, dan styling baru dari `modul-studio.css` ke halaman edit.
  - Pastikan ikon dan nilai kolom eksisting ter-load dengan sempurna di galeri visual.

- [x] **Langkah 5: Redesain Halaman Katalog Modul (`index.blade.php`)**
  - Buat stat banner minimalis yang elegan (Total Modul, Modul Publik, Modul Privat).
  - Tambahkan tab filter (Semua, Publik, Khusus Internal, Terhubung Tabel).
  - Perbarui tampilan grid kartu modul dengan avatar yang me-render SVG ikon dinamis asli sesuai pilihan user.
  - Buat *empty state* yang artistik dan menarik jika belum ada modul yang dibuat.

- [x] **Langkah 6: Verifikasi Responsivitas, Automated Test & Mode Gelap**
  - Jalankan `php artisan test --filter=ModulDinamisBasefwParityTest` untuk memastikan 100% test lulus.
  - Uji interaksi pemilihan ikon, pencarian ikon, dan responsive switcher di mobile/desktop.

---

## 6. Strategi Testing

### 6.1 Automated Feature Test Regression
- Jalankan automated test suite untuk modul builder guna menjamin tidak ada fungsionalitas yang rusak:
  ```bash
  php artisan test --filter=ModulDinamisBasefwParityTest
  ```
- Seluruh 7 tes (`test_superadmin_can_get_database_tables_via_api`, `test_granular_rbac_matrix_authorization`, dll.) wajib tetap berstatus **PASSING (Hijau)**.

### 6.2 Manual & Visual Verification
1. **Uji Tambah Kolom Interaktif**: Klik `[+ Teks]`, `[+ Tanggal]`, `[+ File]`, pastikan kolom muncul di canvas dan langsung terlihat di tabel preview sebelah kanan.
2. **Uji Hubungkan Tabel Basis Data**: Pilih tabel fisik (misal: `bidangs`), klik inspeksi, pastikan seluruh kolom termuat otomatis ke dalam kanvas dan preview tabel terisi.
3. **Uji Simpan Modul**: Lakukan submit form, verifikasi modul baru berhasil dibuat, terdaftar di sidebar, dan data tabelnya bisa diakses.
4. **Uji Toggle Dark Mode**: Ganti tema ke mode gelap, pastikan tidak ada teks yang tenggelam atau kontras yang buruk.

---

## 7. Risiko & Mitigasi

| Potensi Risiko | Tingkat | Mitigasi |
| :--- | :---: | :--- |
| **Field Array Mismatch**: Payload input kolom (`fields[i][...]`) tidak terbaca saat submit. | Sedang | Mempertahankan skema penamaan array `fields[${index}][nama_kolom]` persis sama pada script generator baris kolom. |
| **Preview Script Lag**: Terlalu banyak kolom membuat live preview lambat me-render. | Rendah | Menggunakan *event delegation* dan teknik *lightweight DOM manipulation* tanpa re-render penuh. |
| **Tampilan di Layar Kecil**: Layar < 1200px membuat 2 kolom menjadi sempit. | Sedang | Terapkan media query responsive CSS: Pada layar < 1024px, preview berpindah ke bawah atau menjadi tab yang dapat di-switch. |

---

## 8. Kriteria Selesai (Definition of Done)

- [x] File stylesheet `public/css/modul-studio.css` berhasil dibuat dan dimuat di view builder.
- [x] Tampilan form tidak lagi menggunakan ratusan *inline styles* acak.
- [x] Jargon teknis internal (*Basefw Parity*, *ckPublicRead*, dll.) telah diganti dengan copywriting ramah pengguna.
- [x] Live Interactive Preview berfungsi mulus saat menambah/mengedit kolom.
- [x] Halaman katalog modul (`index.blade.php`) tampil segar, modern, dan informatif.
- [x] Seluruh 40+ ikon terverifikasi selaras dengan tema portal MagKom (stroke-based vector, monokrom/currentColor, tanpa warna/simbol aneh yang keluar konteks).
- [x] `php artisan test --filter=ModulDinamisBasefwParityTest` lolos 100%.
- [x] Mode gelap dan mode terang terverifikasi rapi dan berestetika tinggi.

