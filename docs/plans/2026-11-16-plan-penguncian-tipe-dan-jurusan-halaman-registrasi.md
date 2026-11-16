# Rencana Penguncian Tipe Pendaftaran & Jurusan di Halaman Registrasi (Berasal dari Pilihan Kuota Landing Page)

**Tanggal**: 16 November 2026  
**Dokumen**: `docs/plans/2026-11-16-plan-penguncian-tipe-dan-jurusan-halaman-registrasi.md`  
**Status**: Menunggu Konfirmasi User  

---

## 1. Analisis Kebutuhan & Logika Bisnis

### 1.1 Latar Belakang Masalah
1. **Alur di Landing Page**:
   - Calon peserta magang telah menentukan seluruh preferensi magang di halaman beranda:
     - **Jenjang**: Siswa SMK atau Mahasiswa (D3/S1).
     - **Periode**: Bulan magang (contoh: November 2026).
     - **Tipe Pendaftaran**: Individu (1 Orang) atau Kelompok (Tim 2–4 Orang).
     - **Penempatan & Jurusan**: Mengklik kartu bidang dan memilih tombol jurusan yang sesuai (contoh: Administrasi Perkantoran).
   - Tombol **"Daftar Sekarang ➔"** membawa seluruh parameter pilihan tersebut ke formulir registrasi:  
     `/register?bulan=...&bidang=...&jurusan=...&jenjang=...&tipe=...&jumlah=...`.

2. **Masalah di Halaman Registrasi (`/register`) Saat Ini**:
   - Di bagian atas halaman registrasi, kartu ringkasan kuota sudah tampil dengan tombol *"Ubah Pilihan ↺"*.
   - **NAMUN**, di dalam formulir pendaftaran:
     - Toggle **Tipe Pendaftaran Magang** (`Individu` vs `Kelompok`) **masih bisa diklik dan diubah-ubah secara bebas**.
     - Dropdown **Jenjang**, **Bidang Penempatan**, dan **Jurusan** (`#jenjang`, `#bidang`, `#jurusan`) **juga masih terbuka bebas dan bisa dipilih ulang**.
   - **Dampak Negatif**:
     - Jika peserta mengubah tipe atau jurusan di form registrasi, pilihan kuota yang sudah diperiksa ketersediaannya dari landing page menjadi tidak sinkron atau konflik dengan data kuota yang dipesan.
     - Peserta menjadi bingung karena ada dua tempat pemilihan jurusan (di kartu ringkasan kuota dan di dropdown form).

3. **Kebutuhan Pengguna**:
   > *"kan dari menu landing page ni nah kita daftarnya dari situ pilih jurusan nya dan penempatan dan kelompok atau individu juga nah saya mau membenarkan logika saat udah mengeklik itu semua dan masuk di menu registrasi nah disitu harus nya ga bisa klik tipe nya juga dong dan jurusan nya gabisa di klik2 juga kalo mau ganti di penempatanya buat plan nya"*

---

## 2. Solusi & Desain Perilaku (Behavioral Architecture)

### 2.1 Kondisi A: Peserta Datang dari Landing Page (`$isKuotaTerpilih === true`)
Ketika URL registrasi membawa parameter kuota (`bulan`, `bidang`, `jurusan`):

1. **Penguncian Tipe Pendaftaran (`tipe_pendaftaran`)**:
   - Tombol tipe pendaftaran yang terpilih (*Individu* atau *Kelompok*) tetap aktif dengan visual tegas (Sleman Navy).
   - Ditambahkan ikon gembok mini `🔒` dan label indikator: *"Terkunci dari Beranda"*.
   - Tombol tipe yang lain dinonaktifkan (`disabled`, `opacity: 0.45`, `pointer-events: none`).
   - Handler JavaScript (`auth-depth.js`) tidak mengizinkan peralihan tipe saat status terkunci.
   - Jika tipe adalah *Kelompok*, form isian anggota rombongan tetap tampil otomatis sesuai jumlah anggota yang dipilih di landing page.

2. **Penguncian Pilihan Pendidikan & Jurusan (`jenjang`, `bidang`, `jurusan`)**:
   - Nilai jenjang, bidang penempatan, dan jurusan **terkunci rapat**:
     - Elemen dropdown di-render dalam keadaan nonaktif/terkunci (`disabled` dengan latar belakang `#F8FAFC`, border `#CBD5E1`, warna teks `#334155`, cursor `not-allowed`, serta ikon gembok `🔒`).
     - Agar form submit POST tetap valid dan data tidak hilang (karena browser tidak mengirim input yang berstatus `disabled`), disediakan elemen `<input type="hidden">` pendamping:
       - `<input type="hidden" name="jenjang" value="{{ $initialJenjang }}">`
       - `<input type="hidden" name="bidang_id" value="{{ $initialBidangId }}">`
       - `<input type="hidden" name="jurusan_id" value="{{ $initialJurusanId }}">`
     - Teks bantuan di bawah field menegaskan:  
       *“Pilihan jurusan & penempatan terkunci sesuai kuota yang Anda pilih dari beranda. Untuk mengganti, silakan klik tombol Ubah Pilihan di atas.”*
     - Modul JavaScript `initCascadingEducationFields()` tidak mereset pilihan saat berstatus terkunci.

3. **Alur Jika Ingin Mengganti**:
   - Calon peserta diarahkan untuk mengeklik tombol **"Ubah Pilihan ↺"** pada kartu ringkasan kuota di bagian atas formulir.
   - Tombol tersebut akan mengembalikan peserta ke seksi `#kuota` di landing page untuk memilih kombinasi baru yang valid kuotanya.

---

### 2.2 Kondisi B: Peserta Mendaftar Langsung Tanpa Landing Page (`$isKuotaTerpilih === false`)
- Formulir pendaftaran tetap fleksibel seperti semula:
  - Toggle tipe (*Individu* vs *Kelompok*) dapat dipilih bebas.
  - Dropdown jenjang, bidang, dan jurusan aktif dan berfungsi cascading secara interaktif.

---

## 3. Rincian Teknis Perubahan

### 3.1 Perubahan View Blade: `resources/views/auth/register.blade.php`
- Menambahkan kondisi `$isKuotaTerpilih`:
  1. Pada toggle tipe pendaftaran:
     - Jika `$isKuotaTerpilih`, tambahkan atribut `disabled` / `style="pointer-events: none"` pada tombol toggle tipe yang tidak aktif, dan tambahkan badge/ikon gembok halus pada tombol yang aktif.
  2. Pada seksi jurusan dan penempatan:
     - Tambahkan atribut `disabled` pada `<select id="jenjang">`, `<select id="bidang">`, dan `<select id="jurusan">`.
     - Tambahkan elemen `<input type="hidden">` untuk `jenjang`, `bidang_id`, dan `jurusan_id` agar data POST ke controller registrasi tetap utuh dan valid.
     - Tambahkan catatan instruksi yang ramah pengguna mengarahkan ke tombol *"Ubah Pilihan"*.

### 3.2 Perubahan JavaScript: `resources/js/pages/auth/auth-depth.js`
- Pada fungsi `initRegisterForm()`:
  - Periksa apakah kontainer tipe memiliki atribut `data-locked="true"`. Jika ya, abaikan event listener klik pergantian tipe.
- Pada fungsi `initCascadingEducationFields()`:
  - Periksa apakah field jurusan berstatus `disabled` / `data-locked="true"`. Jika ya, lewati cascading reset agar nilai tidak terhapus.

### 3.3 Perubahan Stylesheet: `resources/css/pages/auth/auth-depth.css`
- Menambahkan utility styling untuk elemen terkunci:
  - `.auth-field-locked`: background `#F8FAFC`, border `#D5DEEE`, color `#334155`, cursor `not-allowed`.
  - `.auth-lock-badge`: badge kecil penanda bahwa pilihan ini terkunci otomatis dari beranda.

---

## 4. Rencana Pengujian & Quality Gates

1. **Feature Testing (PHPUnit)**:
   - Verifikasi bahwa halaman `/register?bulan=...&bidang=...&jurusan=...&tipe=kelompok&jumlah=3` menampilkan field tipe dan jurusan dalam keadaan terkunci.
   - Verifikasi bahwa form submit tetap berhasil memproses registrasi akun dan menyimpan profil preferensi kuota ke sesi pengguna.
   - Verifikasi bahwa halaman `/register` tanpa parameter query tetap membuka dropdown dan tipe secara bebas (jalur normal).
2. **Kompilasi Aset Frontend**:
   - Jalankan `npm run build` dengan Vite.
3. **Format & Standar Kode**:
   - Jalankan `./vendor/bin/pint --test`.
4. **Verifikasi Browser**:
   - Menguji interaksi langsung di dev server `http://127.0.0.1:8000/register?bulan=1&bidang=1&jurusan=1&tipe=mandiri&jumlah=1`.

