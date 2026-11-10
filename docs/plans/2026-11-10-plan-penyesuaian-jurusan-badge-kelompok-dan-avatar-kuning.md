# Rencana Kerja: Penyesuaian Dinamis Label Jurusan/Program Studi, Penyelarasan Badge Kelompok Hitam, dan Avatar Inisial Kuning

**Tanggal:** 10 November 2026  
**Status:** Menunggu Persetujuan Pengguna  
**Referensi File:**  
- Blade View: [`resources/views/admin/pendaftaran/show.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/pendaftaran/show.blade.php)  
- Stylesheet: [`resources/css/pages/admin/pendaftaran-show.css`](file:///d:/laragon/www/MAGKOM/resources/css/pages/admin/pendaftaran-show.css)  

---

## 1. Analisis Masalah & Solusi Senior Developer

### A. Poin 1: Penamaan Program Studi vs Jurusan yang Relevan & Cerdas
> *"Untuk penamaan program studi kurang relevan dan kurang tepat kalo di pikir pikir kan yang magang bukan mahasiswa doang aja ada smk juga jadi kalo bisa di ganti jurusan aja, kalo engga di bikin gini kalo yg daftar smk di bikin jurusan kalo mahasiswa di bikin progran studi, kalo menurut anda gmn sebagai senior developer, pikir yg cerdas dong"*

**Tanggapan & Analisis Senior Developer:**
- Pemikiran Anda **sangat cerdas, presisi, dan tepat secara kontekstual**.
- Dalam nomenklatur pendidikan di Indonesia:
  - **SMK (Sekolah Menengah Kejuruan)**: Tidak pernah menggunakan istilah "Program Studi". Istilah resminya adalah **Jurusan** atau *Kompetensi Keahlian* (contoh: *Rekayasa Perangkat Lunak (RPL)*, *Teknik Komputer & Jaringan (TKJ)*).
  - **Perguruan Tinggi (Universitas / Institut / Politeknik)**: Menggunakan istilah baku **Program Studi** (contoh: *S1 Teknik Informatika*, *D3 Manajemen Informatika*).
- **Solusi Cerdas & Otomatis**:
  Sama seperti logika dinamis yang telah kita terapkan pada nomor induk (**NISN** untuk SMK dan **NIM** untuk Mahasiswa), kita akan membuat label bidang studi **100% otomatis mendeteksi profil pendaftar**:
  ```php
  $isSiswa = ($profil?->jenjang === \App\Enums\JenjangEnum::Smk
      || (is_string($profil?->jenjang) && strtolower((string)$profil->jenjang) === 'smk')
      || ($profil?->jenjang instanceof \App\Enums\JenjangEnum && $profil->jenjang->value === 'smk')
      || ($profil?->jurusan?->kategori === 'siswa'));

  $labelNomorInduk = $isSiswa ? 'NISN' : 'NIM';
  $labelBidangStudi = $isSiswa ? 'Jurusan' : 'Program Studi';
  ```
  - Jika pendaftar adalah siswa SMK $\rightarrow$ Label otomatis tampil: **`JURUSAN`**.
  - Jika pendaftar adalah mahasiswa $\rightarrow$ Label otomatis tampil: **`PROGRAM STUDI`**.
  - Tampilan menjadi sangat cerdas, adaptif, dan bebas kesalahan penamaan.

---

### B. Poin 2: Penyelarasan Badge & Box Kelompok (SS 2)
> *"gambar ss 2 di bikin hitam aja jangan ada kolom birunya warna hitam aja sesuaikan pada yg lain"*

**Tanggapan & Analisis Desain Antarmuka:**
- Pada SS 2 (`media_1794280009038_65ab7423.png`), elemen `[Kelompok] gopalo` saat ini menggunakan background pil biru menyala (`background: #0641CC; color: #FFFFFF`) di dalam kontainer biru muda (`#EFF6FF`).
- Hal ini menimbulkan inkonsistensi visual karena semua panel dan card lainnya di dashboard menggunakan judul teks hitam tegas (`#0F172A`) dengan kontainer putih bersih berborder netral (`#E2E8F0`).
- **Solusi Penyelarasan**:
  1. **Hilangkan pil biru menyala** (*kolom/blok biru*).
  2. Gunakan **teks hitam formal (`#0F172A`)** yang selaras dengan seluruh judul card:
     ```html
     <div style="display: flex; align-items: center; gap: 8px;">
         <span style="color: #64748B; font-size: 0.8125rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
             Kelompok:
         </span>
         <strong style="color: #0F172A; font-size: 1.05rem; font-weight: 800;">
             {{ $pendaftaran->kelompokMagang->nama_kelompok }}
         </strong>
     </div>
     ```
  3. Ubah kotak kontainer dari biru muda menjadi kontainer card standar: background putih bersih (`#FFFFFF`), border netral abu-abu (`#E2E8F0`), dan shadow lembut.

---

### C. Poin 3: Logo Inisial Avatar (SS 3) Disesuaikan dengan Aksen Kuning (SS 4)
> *"ss gambar ke 3 warna nya pada gambar ss ke 4 jadi untuk logo gopal is king atau gi warna kuning sesuaikan aja"*

**Tanggapan & Analisis Palet Warna:**
- Pada SS 3 (`media_1794280060056_1ea2d1cd.png`), logo inisial avatar (`GI`) menggunakan palet biru keunguan/indigo (`#EEF2FF`, teks `#4F46E5`).
- Pada SS 4 (`media_1794280141730_b8ebc784.png`), terdapat aksen kuning khas MagKom / Pemkab Sleman (warna tombol dan highlight kuning emas cerah `#F59E0B` / `#FBBF24`).
- **Solusi Desain Avatar Kuning**:
  Kita ubah styling `.adm-avatar-circle` di [`resources/css/pages/admin/pendaftaran-show.css`](file:///d:/laragon/www/MAGKOM/resources/css/pages/admin/pendaftaran-show.css):
  - **Opsi Utama (Rekomendasi - Elegan & Kontras Tinggi)**:
    - Background: `#FEF3C7` (amber-100 cerah hangat)
    - Teks Inisial: `#B45309` (amber-800 tebal kontras tinggi, WCAG AA compliant)
    - Border: `1.5px solid #FCD34D` (ring amber halus)
    - Glow: `box-shadow: 0 1px 3px rgba(245, 158, 11, 0.15)`
  - **Opsi Alternatif (Solid Gold Vibrant - Persis Tombol Kuning SS 4)**:
    - Background: `#F59E0B` (amber-500 solid emas)
    - Teks Inisial: `#1E293B` (dark slate / hitam pekat)
    - Border: `1.5px solid #D97706`

---

## 2. Perbandingan Visual (Before vs After)

```text
========================================================================================================
SEBELUM (BEFORE)
========================================================================================================
[AVATAR BIRU/UNGU: GI]  gopal is king
                         #REG-00007

[BOX BIRU MUDA]
[PIL BIRU: Kelompok] gopalo (Teks Biru)
[Tabel Anggota Kelompok]

[Card: Data Calon Peserta]
+------------------------------------+------------------------------------+
| ASAL INSTANSI                      | PROGRAM STUDI (Keliru utk SMK!)    |
| Smk N 1 Seyegan                    | Rekayasa Perangkat Lunak (RPL)     |
+------------------------------------+------------------------------------+
| NISN                               | PERIODE BULAN MAGANG               |
| 24.04.1976                         | Desember 2026                      |
+------------------------------------+------------------------------------+

========================================================================================================
SESUDAH (AFTER)
========================================================================================================
[AVATAR KUNING EMAS: GI]  gopal is king
                          #REG-00007

[BOX PUTIH BERSIH NETRAL]
KELOMPOK: gopalo (Teks Hitam Pekat Formal #0F172A - Tanpa Pil Biru)
[Tabel Anggota Kelompok]

[Card: Data Calon Peserta]
+------------------------------------+------------------------------------+
| ASAL INSTANSI                      | JURUSAN (Otomatis SMK!)            |
| Smk N 1 Seyegan                    | Rekayasa Perangkat Lunak (RPL)     |
+------------------------------------+------------------------------------+
| NISN                               | PERIODE BULAN MAGANG               |
| 24.04.1976                         | Desember 2026                      |
+------------------------------------+------------------------------------+
*(Catatan: Jika pendaftar mahasiswa, label otomatis berubah menjadi "PROGRAM STUDI" & "NIM")*
========================================================================================================
```

---

## 3. Rincian Perubahan File & Kode

### File 1: `resources/views/admin/pendaftaran/show.blade.php`
1. **Definisi Label Bidang Studi Dinamis**:
   ```php
   $labelNomorInduk = $isSiswa ? 'NISN' : 'NIM';
   $labelBidangStudi = $isSiswa ? 'Jurusan' : 'Program Studi';
   ```
2. **Penggunaan pada Card `Data Calon Peserta`**:
   ```html
   <div class="adm-biodata-card-item">
       <dt class="adm-biodata-dt">{{ $labelBidangStudi }}</dt>
       <dd class="adm-biodata-dd">{{ $profil?->jurusan?->nama_jurusan ?? '-' }}</dd>
   </div>
   ```
3. **Pembersihan Box & Badge Kelompok**:
   - Ganti kontainer `#EFF6FF` dengan `#FFFFFF; border: 1px solid #E2E8F0;`.
   - Hapus pil biru `background: #0641CC;`, ganti dengan judul teks hitam pekat `Kelompok: {{ $pendaftaran->kelompokMagang->nama_kelompok }}` dengan warna `#0F172A`.

### File 2: `resources/css/pages/admin/pendaftaran-show.css`
1. **Perubahan Warna Avatar `.adm-avatar-circle`**:
   ```css
   .adm-avatar-circle {
       width: 52px;
       height: 52px;
       border-radius: 9999px;
       background: #FEF3C7;
       color: #B45309;
       display: flex;
       align-items: center;
       justify-content: center;
       font-size: 1.25rem;
       font-weight: 800;
       letter-spacing: 0.03em;
       border: 1.5px solid #FCD34D;
       box-shadow: 0 1px 3px rgba(245, 158, 11, 0.12);
       flex-shrink: 0;
   }
   ```

---

## 4. Rencana Verifikasi & Quality Gates

1. **Kompilasi Aset Frontend (Vite)**:
   - `npm run build` (memastikan bundle CSS terkompilasi tanpa error).
2. **Pint Code Style Check**:
   - `vendor/bin/pint --test resources/views/admin/pendaftaran/show.blade.php`.
3. **Feature Tests Terkait**:
   - `vendor/bin/phpunit tests/Feature/PendaftaranPesertaTest.php tests/Feature/AdminDuplicateNimDetectionTest.php tests/Feature/AdminSuperAdminPortalTest.php tests/Feature/AdminChildPagesExtendedVisualTest.php`.
4. **Full Regression Suite**:
   - `vendor/bin/phpunit` (750 tests wajib 100% lulus).

