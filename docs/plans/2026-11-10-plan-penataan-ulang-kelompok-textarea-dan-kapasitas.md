# Rencana Kerja: Penataan Ulang Tata Letak Kelompok pada Tabel, Pembersihan Resize Handle Textarea, dan Penyederhanaan Kolom Kapasitas Kuota

**Tanggal:** 10 November 2026  
**Status:** Menunggu Persetujuan Pengguna  
**Referensi File:**  
- View Tabel Verifikasi Pendaftaran: [`resources/views/admin/pendaftaran/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/pendaftaran/index.blade.php)  
- View Detail Verifikasi Berkas: [`resources/views/admin/pendaftaran/show.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/pendaftaran/show.blade.php)  
- Stylesheet Verifikasi Berkas: [`resources/css/pages/admin/pendaftaran-show.css`](file:///d:/laragon/www/MAGKOM/resources/css/pages/admin/pendaftaran-show.css)  
- View Tabel Alokasi Kuota: [`resources/views/admin/kuota/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/kuota/index.blade.php)  

---

## 1. Analisis Masalah & Solusi Senior Developer

### A. Poin 1: Tata Letak Teks Kelompok & Tim yang Berantakan (SS 1, 2, 3)
> *"jujur gue bingung tata letak teks berwarna ungu bertulisan kelompok dan tim, kalo di taruh situ menjadi berantakan tidak rapi dan layout jelek, bantu saya untuk menaruhkan tata letak yang pass dong anda kan sebagai senior developer profesional harusnya bisa dong"*

**Analisis Masalah Desain Saat Ini:**
1. **Redundansi & Inkonsistensi Terminologi**:
   - Di Kolom 1 (No. Registrasi): Ada badge ungu `Kelompok (2 Org)`.
   - Di Kolom 2 (Calon Peserta): Di bawah email ada teks ungu terpisah `Tim: gopalo`.
   - Mengapa satu elemen disebut *"Kelompok"* sedangkan elemen lainnya disebut *"Tim"*? Padahal keduanya merujuk ke entitas yang sama.
2. **Fragmentasi Data**: Informasi kelompok terbelah di dua kolom berbeda, membuat baris tabel memanjang ke bawah dan mata verifikator melompat-lompat saat membaca data.
3. **Warna Ungu yang Mencolok**: Warna ungu neon (`#7C3AED` & `#F5F3FF`) bertabrakan dengan palet utama tabel (navy, slate, dan biru).

**Solusi Desain Enterprise (Prinsip Single Cohesive Metadata):**
Satukan informasi kelompok menjadi **SATU entitas metadata yang rapi dan elegan**.

- **Opsi Rekomendasi Utama (Terpusat di Kolom Calon Peserta - Paling Rapi)**:
  - **Kolom 1 (No. Registrasi)**: Bersih! Hanya badge nomor registrasi `#REG-00007`. Kolom ini menjadi seragam tinggi dan sejajar di semua baris.
  - **Kolom 2 (Calon Peserta)**:
    - Baris 1: Nama Peserta (`gopal is king`)
    - Baris 2: Badge Kelompok Rapi:  
      `<span class="adm-badge-kelompok-clean">👥 Kelompok: gopalo · 2 Peserta</span>`  
      *(Menggunakan palet slate-indigo netral yang teduh: background `#F1F5F9`, teks `#334155`, border `#CBD5E1`)*.
    - Baris 3: `gopal42@gmail.com` &bull; `NISN: 24.04.1976` (bisa bersanding rapi dalam 1 baris).
  - *Hasil*: Tampilan tabel langsung terlihat lapang, terstruktur, profesional, dan tidak ada teks mengambang yang canggung!

- **Opsi Alternatif (Badge Terpadu di Kolom No. Registrasi)**:
  - **Kolom 1**: `#REG-00007` dan di bawahnya satu badge ringkas: `[👥 Kelompok: gopalo (2 Org)]`.
  - **Kolom 2**: Bersih total tanpa baris `Tim: gopalo`. Hanya memuat Avatar, Nama, Email, dan NISN/NIM.

---

### B. Poin 2: Garis Vertikal/Diagonal di Pojok Kanan Bawah Kotak Input (SS 4)
> *"gambar ss ke 4 perbaiki juga ada garis vertikal 2 itu"*

**Analisis Teknis & Masalah:**
- Pada SS 4 (`media_1794282532946_33ebbf68.png`), terdapat 2–3 garis miring/vertikal (`///`) di pojok kanan bawah.
- **Penyebab**: Garis tersebut adalah *native resize gripper* bawaan browser pada elemen `<textarea>` (Catatan Penerimaan / Alasan Penolakan di halaman verifikasi berkas). Saat CSS textarea memiliki properti default atau `resize: vertical;`, browser otomatis merender ikon gripper di pojok bawah.
- **Solusi**:
  Tambahkan aturan CSS `resize: none;` pada textarea:
  ```css
  .adm-form-field textarea {
      width: 100%;
      border: 1px solid #CBD5E1;
      border-radius: 8px;
      padding: 0.65rem 0.85rem;
      font-size: 0.8125rem;
      font-family: inherit;
      resize: none; /* Menghilangkan garis gripper pojok kanan bawah secara total */
  }
  ```
  Pojok kanan bawah textarea akan menjadi mulus, melengkung sempurna (*clean rounded corner*), dan bebas dari garis gripper browser.

---

### C. Poin 3: Kolom Kapasitas Terlalu Rame Keterangan Buka dan Tutup (SS 5)
> *"ss ke 5 juga gue bingung itu teks nya terlalu rame keterangan buka dan tutup menjadi rame jadi menurut anda gmn"*

**Analisis Masalah Desain Saat Ini di `admin/kuota/index.blade.php`:**
- Pada setiap baris tabel kapasitas bidang, terdapat:
  1. Badge besar bertingkat: `[ • Dibuka ]` atau `[ • Ditutup ]`.
  2. Teks di bawahnya: `15 peserta` atau `0 peserta`.
- **Mengapa Terasa "Terlalu Rame"?**
  - Ketika admin melihat tabel dengan 8 bidang, mata admin melihat 8 badge lonjong bertumpuk secara vertikal yang menduplikasi informasi: bidang dengan "15 peserta" sudah pasti dibuka, dan "0 peserta" sudah pasti ditutup.
  - Akibatnya kolom *Kapasitas* menjadi sangat padat dan mendominasi tabel.

**Solusi Desain Senior Developer:**
- **Opsi Rekomendasi 1 (Badge Kapasitas Satu Baris Ringkas & Modern)**:
  Satukan status dan angka menjadi **satu badge pill ramping**:
  - Jika Kuota > 0:  
    `[🟢 15 Peserta]` (pill hijau lembut: background `#ECFDF5`, border `#A7F3D0`, teks `#065F46`, dengan dot indikator hijau `#10B981`).
  - Jika Kuota == 0:  
    `[⚪ Tutup (0 Kursi)]` (pill netral abu-abu: background `#F8FAFC`, border `#E2E8F0`, teks `#64748B`).
  - *Keuntungan*: Kolom menjadi sangat ramping (1 baris), tidak ada teks menumpuk, visual langsung terbaca dalam 0.5 detik (*high glanceability*).
- **Opsi Rekomendasi 2 (Angka Kuota Bersih dengan Status Dot Mini)**:
  - Jika Kuota > 0:  
    `🟢 15 peserta` (Teks hitam tebal `15` + label `peserta` abu-abu didampingi titik hijau mini).
  - Jika Kuota == 0:  
    `⚪ Ditutup` (Teks abu-abu lembut dengan titik abu-abu).

---

## 2. Wireframe Perbandingan Visual (Before vs After)

```text
========================================================================================================
POIN 1: TABEL VERIFIKASI PENDAFTARAN (admin/pendaftaran/index.blade.php)
========================================================================================================
[SEBELUM - BERANTAKAN & PECAH DUA KOLOM]
NO. REGISTRASI            CALON PESERTA
+-----------------------+ +---------------------------------------------------------+
| #REG-00007            | | [GI] gopal is king                                      |
| [Kelompok (2 Org)]    | |      gopal42@gmail.com                                  |
| (Ungu mencolok)       | |      Tim: gopalo (Teks ungu menyempil di sini)         |
|                       | |      NISN: 24.04.1976                                   |
+-----------------------+ +---------------------------------------------------------+

[SESUDAH - TERPUSAT, BERSIH, & RAPI]
NO. REGISTRASI            CALON PESERTA
+-----------------------+ +---------------------------------------------------------+
| #REG-00007            | | [GI] gopal is king                                      |
| (Sejajar & seragam    | |      [👥 Kelompok: gopalo · 2 Peserta] (Badge Slate Rapi)|
| dengan baris lain)    | |      gopal42@gmail.com  •  NISN: 24.04.1976             |
+-----------------------+ +---------------------------------------------------------+

========================================================================================================
POIN 2: TEXTAREA RESIZE HANDLE (admin/pendaftaran/show.blade.php)
========================================================================================================
[SEBELUM]  Kotak textarea ada garis gripper pojok kanan bawah: [ ____________ /// ]
[SESUDAH]  Kotak textarea mulus, bersih, rounded corner rapi:  [ ____________     ]

========================================================================================================
POIN 3: KOLOM KAPASITAS (admin/kuota/index.blade.php)
========================================================================================================
[SEBELUM - TERLALU RAME & MENUMPUK]
KAPASITAS
+---------------------------+
| [ • Dibuka ]              |
| 15 peserta                |
+---------------------------+
| [ • Ditutup ]             |
| 0 peserta                 |
+---------------------------+

[SESUDAH - 1 BARIS RINGKAS, ELEGAN, & CEPAT DIPAHAMI]
KAPASITAS
+---------------------------+
| [🟢 15 Peserta]           |
+---------------------------+
| [⚪ Ditutup (0 Kursi)]    |
+---------------------------+
========================================================================================================
```

---

## 3. Rincian Perubahan File & Baris Kode

1. **`resources/views/admin/pendaftaran/index.blade.php`**:
   - Di kolom `No. Registrasi`: Hapus badge kelompok yang membuat kolom bengkak.
   - Di kolom `Calon Peserta`: Hapus teks ungu terpisah `Tim: gopalo`. Ganti dengan badge kelompok terpadu di bawah nama peserta `👥 Kelompok: {{ $item->kelompokMagang->nama_kelompok }} ({{ $item->kelompokMagang->anggota->count() + 1 }} Peserta)`.
   - Update styling CSS `.adm-badge-kelompok` menjadi netral slate/navy lembut.

2. **`resources/css/pages/admin/pendaftaran-show.css`**:
   - Di `.adm-form-field textarea`: tambahkan `resize: none;` untuk menghilangkan garis gripper pada form keputusan verifikasi.

3. **`resources/views/admin/kuota/index.blade.php`**:
   - Di kolom `Kapasitas` (L1180–L1198): Sederhanakan markup dari 2 baris (badge + teks) menjadi **1 badge kapasitas ringkas** (`[🟢 15 Peserta]` / `[⚪ Ditutup (0 Kursi)]`).

---

## 4. Rencana Verifikasi & Quality Gates

1. **Kompilasi Aset Vite**: `npm run build`.
2. **Laravel Pint**: Linter pada file yang diubah.
3. **Feature Tests**:
   - `vendor/bin/phpunit tests/Feature/PendaftaranPesertaTest.php tests/Feature/AdminDuplicateNimDetectionTest.php tests/Feature/AdminSuperAdminPortalTest.php tests/Feature/AdminChildPagesExtendedVisualTest.php tests/Feature/DashboardModulesRenderingTest.php`.
4. **Visual Inspection**: Memastikan tabel pendaftaran rapi, textarea bebas gripper, dan kolom kapasitas bersih.

