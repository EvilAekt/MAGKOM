# Rencana Kerja: Penyelarasan Header Banner Biru Peserta, Teks Normal Kelompok, Teks Hitam Data Peserta, dan Efek Hover Biru Tombol Aksi

**Tanggal:** 10 November 2026  
**Status:** Menunggu Persetujuan Pengguna  
**Referensi File:**  
- Blade View: [`resources/views/admin/pendaftaran/show.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/pendaftaran/show.blade.php)  
- Stylesheet: [`resources/css/pages/admin/pendaftaran-show.css`](file:///d:/laragon/www/MAGKOM/resources/css/pages/admin/pendaftaran-show.css)  
- Komparasi Desain: [`resources/css/pages/peserta/profil-show.css`](file:///d:/laragon/www/MAGKOM/resources/css/pages/peserta/profil-show.css)  

---

## 1. Analisis Kebutuhan & Rekomendasi Senior Developer

### A. Poin 1: Header Identity Bar Biru Selaras dengan Profil Peserta (SS 1)
> *"untuk gambar ss 1 bg di chart nya warna biru sesuakan seperti profile yang ada di perserta warna nya icon nya juga sesuaikan warnanya untuk warna teksnya putih"*

**Analisis Desain & Kode Existing:**
- Di portal peserta ([`resources/css/pages/peserta/profil-show.css`](file:///d:/laragon/www/MAGKOM/resources/css/pages/peserta/profil-show.css) L71–L84), header identitas peserta menggunakan palet gradient **Navy/Royal Blue khas Sleman**:
  ```css
  background: radial-gradient(80% 120% at 100% 0%, rgba(63, 81, 181, 0.35), transparent 60%),
              linear-gradient(135deg, #02275D 0%, #01347C 60%, #003566 100%);
  border: 1px solid rgba(255, 255, 255, 0.12);
  box-shadow: 0 4px 18px rgba(2, 39, 93, 0.22);
  border-radius: 14px;
  ```
- **Penerapan pada Header Admin**:
  1. **Background**: Menggunakan formula gradient navy blue di atas (`#02275D` $\rightarrow$ `#01347C`).
  2. **Warna Teks**: 
     - Nama peserta (`.adm-candidate-name`): `#FFFFFF` (putih pekat).
     - Nomor registrasi (`#REG-00007`): `rgba(255, 255, 255, 0.85)`.
     - Waktu pengajuan: Label `rgba(255, 255, 255, 0.75)`, nilai waktu `#FFFFFF`.
  3. **Warna Icon / Avatar**:
     - Avatar inisial (`GI`) diselaraskan dengan header peserta: lingkaran navy berbingkai putih bersih (`border: 2.5px solid #FFFFFF; background: #02275D; color: #FFFFFF;`) atau aksen kuning emas elegan (`background: #FFCB05; color: #02275D; border: 2.5px solid #FFFFFF;`).
  4. **Tombol "Kembali ke Verifikasi Berkas"**:
     - `background: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.28); color: #FFFFFF;`
     - Saat hover: berubah menjadi putih bersih dengan teks navy (`background: #FFFFFF; color: #02275D;`).

---

### B. Poin 2: Teks Nama Anggota Kelompok Tidak Bold (SS 2)
> *"ss ke 2 teks nya jangan bold"*

**Analisis & Penyesuaian:**
- Pada tabel anggota kelompok di SS 2 (`media_1794280738562_89f273fa.png`), nama `gopal is king` dan `nopal` saat ini menggunakan `font-weight: 600` dan `500`.
- **Perubahan**:
  - Ubah `font-weight: 600` / `500` menjadi **`font-weight: 400` (normal)**.
  - Teks nama peserta menjadi rapi, tenang, dan tidak tampak terlalu tebal/mencolok.

---

### C. Poin 3: Warna Teks Hijau & Biru Diganti Hitam Seperti yang Lain (SS 3)
> *"ss ke 3 warna teks hijau dan biru di gantii hitam seperti yg lain"*

**Analisis & Penyesuaian:**
- Pada card *Data Calon Peserta* di SS 3 (`media_1794280772974_2ef15dd2.png`):
  - `Periode Bulan Magang`: teks bernilai biru (`#0641CC`).
  - `Status Kuota Bidang`: teks bernilai hijau (`#10B981`).
- Permintaan Anda sangat tepat untuk menjaga **keseragaman visual (*consistency*)**. Semua nilai data dalam form (Instansi, Jurusan, NISN, Email, WA, Periode, Kuota) kini memiliki hierarki yang sama.
- **Perubahan**:
  - Hilangkan styling inline warna biru (`#0641CC`) dan hijau (`#10B981`).
  - Keduanya kembali menggunakan warna hitam baku `.adm-biodata-dd`: **`#0F172A` (hitam pekat formal)**.

---

### D. Poin 4: Tombol Aksi "Tinjau Berkas" & "Unduh" Memiliki Efek Hover Warna Biru Interaktif
> *"semua aksi atau tombol di kasih hover warna biru sesuaika seperti yang lain ya konsistenkan, sesuaikan"*  
> *"+ini juga plan di kasih hover juga tombol tinjau berkas"*

**Analisis & Desain Interaksi Tombol Berkas:**
- Pada daftar berkas di panel *Verifikasi Berkas Calon Peserta* (tangkapan layar terbaru), tombol **`[👁️ Tinjau Berkas]`** dan tombol icon **`[📥 Unduh]`** saat ini berlatar putih dengan border abu-abu dan hanya berhover abu-abu pudar.
- **Standarisasi Hover Biru MagKom (`#0641CC`)**:
  1. **Tombol "Tinjau Berkas" (`.adm-btn-action-preview`)**:
     - **Default**: Latar putih (`#FFFFFF`), border slate (`#CBD5E1`), teks slate gelap (`#334155`), ikon mata abu-abu.
     - **Saat Hover**: 
       - Background bertransisi mulus ke **Biru MagKom (`#0641CC`)**.
       - Border berubah menjadi `#0641CC`.
       - Teks berubah menjadi **putih bersih (`#FFFFFF`)**.
       - Ikon SVG mata (`stroke="currentColor"`) otomatis berubah menjadi **putih bersih (`#FFFFFF`)**.
       - Ditambahkan elevasi bayangan biru halus: `box-shadow: 0 2px 8px rgba(6, 65, 204, 0.28);`.
  2. **Tombol Icon "Unduh Berkas" (`.adm-btn-action-download`)**:
     - **Default**: Latar putih (`#FFFFFF`), border slate (`#CBD5E1`), ikon download slate `#475569`.
     - **Saat Hover**: 
       - Background berubah menjadi **Biru MagKom (`#0641CC`)**.
       - Border berubah menjadi `#0641CC`.
       - Ikon panah unduh berubah menjadi **putih bersih (`#FFFFFF`)**.
       - Elevasi bayangan biru halus: `box-shadow: 0 2px 8px rgba(6, 65, 204, 0.28);`.
  3. **Tombol "Kembali ke Verifikasi Berkas" (`.adm-btn-back`)**:
     - **Default**: Semi-transparan di atas banner navy `rgba(255, 255, 255, 0.12)`.
     - **Saat Hover**: Berubah menjadi putih bersih berteks navy `#02275D` (atau aksen biru menyala).
  4. **Tombol Aksi Keputusan "Diterima"**:
     - **Default**: `background: #0641CC`.
     - **Saat Hover**: `background: #04329C; box-shadow: 0 4px 12px rgba(6, 65, 204, 0.3);`.

---

## 2. Wireframe Perbandingan Visual (Before vs After)

```text
========================================================================================================
SEBELUM (BEFORE)
========================================================================================================
[HEADER PUTIH]  [Avatar GI Kuning]  gopal is king (Hitam)               [Kembali ke Verifikasi] (Abu-abu)
                                    #REG-00007 (Abu-abu)

[TABEL KELOMPOK]
Peran          | Nama Peserta
Ketua Kelompok | gopal is king (BOLD TEBAL)
Anggota        | nopal (BOLD TEBAL)

[DATA CALON PESERTA]
Periode Bulan Magang : Desember 2026 (WARNA BIRU)
Status Kuota Bidang  : Tersedia sisa 4 kursi (WARNA HIJAU)

[TOMBOL AKSI BERKAS]
[Tinjau Berkas] -> Hover hanya abu-abu terang (#F8FAFC)
[Unduh]         -> Hover hanya abu-abu terang (#F8FAFC)

========================================================================================================
SESUDAH (AFTER)
========================================================================================================
[HEADER NAVY BLUE GRADIENT]  [Avatar GI Putih/Gold]  gopal is king (PUTIH #FFFFFF)  [Kembali] (Semi-transparan)
                                                     #REG-00007 (Putih Soft)

[TABEL KELOMPOK]
Peran          | Nama Peserta
Ketua Kelompok | gopal is king (TEKS NORMAL / REGULAR)
Anggota        | nopal (TEKS NORMAL / REGULAR)

[DATA CALON PESERTA]
Periode Bulan Magang : Desember 2026 (HITAM PEKAT #0F172A - KONSISTEN)
Status Kuota Bidang  : Tersedia sisa 4 kursi di bidang ini (HITAM PEKAT #0F172A - KONSISTEN)

[TOMBOL AKSI BERKAS]
[Tinjau Berkas] -> HOVER BERUBAH MENJADI BIRU PEKAT (#0641CC) & TEKS PUTIH!
[Unduh]         -> HOVER BERUBAH MENJADI BIRU PEKAT (#0641CC) & IKON PUTIH!
========================================================================================================
```

---

## 3. Rincian Perubahan File & CSS

### 1. `resources/css/pages/admin/pendaftaran-show.css`
- **`.adm-identity-bar`**:
  ```css
  background: radial-gradient(80% 120% at 100% 0%, rgba(63, 81, 181, 0.35), transparent 60%),
              linear-gradient(135deg, #02275D 0%, #01347C 60%, #003566 100%);
  border: 1px solid rgba(255, 255, 255, 0.15);
  box-shadow: 0 4px 18px rgba(2, 39, 93, 0.18);
  ```
- **`.adm-candidate-name`**: `color: #FFFFFF;`
- **`.adm-candidate-reg`**: `color: rgba(255, 255, 255, 0.85);`
- **`.adm-avatar-circle`**:
  ```css
  background: #02275D;
  color: #FFFFFF;
  border: 2.5px solid #FFFFFF;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
  ```
- **`.adm-btn-back`**:
  ```css
  background: rgba(255, 255, 255, 0.12);
  color: #FFFFFF;
  border: 1px solid rgba(255, 255, 255, 0.25);
  ```
  `&:hover`: `background: #FFFFFF; color: #02275D; border-color: #FFFFFF;`
- **`.adm-btn-action-preview:hover`** & **`.adm-btn-action-download:hover`**:
  ```css
  background: #0641CC;
  color: #FFFFFF;
  border-color: #0641CC;
  box-shadow: 0 2px 6px rgba(6, 65, 204, 0.25);
  ```

### 2. `resources/views/admin/pendaftaran/show.blade.php`
- Di tabel kelompok: ubah `font-weight: 600` dan `500` pada nama anggota menjadi `font-weight: 400`.
- Di card *Data Calon Peserta*: hapus inline style warna biru (`#0641CC`) dan hijau (`#10B981`), gunakan warna hitam seragam.
- Di teks waktu pengajuan header: ganti label menjadi `rgba(255, 255, 255, 0.8)` dan teks strong waktu menjadi `#FFFFFF`.

---

## 4. Rencana Pengujian & Quality Gates

1. **Vite Asset Build**: `npm run build`.
2. **Laravel Pint**: `vendor/bin/pint --test resources/views/admin/pendaftaran/show.blade.php`.
3. **Feature Test Suite Terkait**:
   `vendor/bin/phpunit tests/Feature/PendaftaranPesertaTest.php tests/Feature/AdminDuplicateNimDetectionTest.php tests/Feature/AdminSuperAdminPortalTest.php tests/Feature/AdminChildPagesExtendedVisualTest.php tests/Feature/DashboardModulesRenderingTest.php`.
4. **Full Regression Suite**: `vendor/bin/phpunit`.

