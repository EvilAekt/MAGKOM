# Rencana Kerja: Evaluasi Logika & Redesain Kartu Bukti Surat Diterima di Panel Admin

| Informasi Dokumen | Nilai |
| :--- | :--- |
| **Tanggal Pembuatan** | 18 November 2026 |
| **Status Dokumen** | Draf Rencana (Menunggu Persetujuan Pengguna) |
| **Target Fitur** | Panel Keputusan Verifikasi Pendaftaran Admin (`resources/views/admin/pendaftaran/show.blade.php`) |
| **Branch Target** | `test-2` |
| **Referensi Masukan** | Tangkapan Layar `media_1794985078378_57ada878.png` |

---

## 1. Analisis Kritis Senior Developer: Mengapa Tombol Saat Ini Terasa Janggal?

Pandangan dan logika berpikir pengguna **100% tepat, logis, dan sangat tajam dari kacamata arsitektur UX Enterprise/GovTech**.

### A. Anomali Peran (*Role Mental Model Mismatch*)
* **Sisi Admin**:
  - Admin adalah **pihak pengunggah** (*author/uploader*). Dokumen PDF tersebut berasal dari komputer admin sendiri yang telah diparaf/ditandatangani secara resmi sebelum di-upload.
  - Setelah tombol submit ditekan dan surat terunggah, status pendaftaran menjadi final (*Diterima*).
  - Menampilkan tombol aksi mencolok **`[Unduh PDF]`** bagi admin adalah **anomali logika bisnis**: *Mengapa admin harus mengunduh kembali dokumen yang baru saja ia miliki dan ia unggah?*
  - Tombol aksi primer `[Unduh PDF]` sejatinya hanya relevan bagi **Peserta Magang** (karena peserta membutuhkan file fisik surat untuk dicetak dan diserahkan ke bagian akademik kampus/sekolah).

### B. Pola Antarmuka yang Keliru (*Misplaced Action vs Record State*)
* Tampilan saat ini memperlakukan admin seolah-olah sedang menerima kiriman file, padahal fungsi halaman bagi admin setelah keputusan selesai adalah **Record & Audit View** (tampilan bukti pengesahan).
* Dua tombol sejajar `[Pratinjau]` dan `[Unduh PDF]` (apalagi `Unduh PDF` diberi warna biru solid tebal) mengalihkan perhatian admin dan menciptakan kesan ada aksi lanjutan yang harus dilakukan, padahal proses administrasi pendaftaran tersebut sudah tuntas.

---

## 2. Usulan Solusi: Transformasi dari "Action Buttons" Menjadi "Verification & Audit Status"

Kita akan merombak blok kartu tersebut dari gaya tombol aksi menjadi **Status Dokumen Resmi Terbit (Audit-Ready)** yang berkelas profesional:

### Perbandingan Konsep:

```
[ KONDISI SAAT INI - JANGGAL & REDUNDAN ]
+-------------------------------------------------------------+
|  📄 Surat Keterangan Diterima Terunggah                     |
|  [ Pratinjau ]  [ Unduh PDF ]                               |
+-------------------------------------------------------------+

[ USULAN BARU - BERKELAS GOVTECH & LOGIS ]
+-------------------------------------------------------------+
|  📄 Surat Keterangan Diterima Resmi                         |
|     ✓ Telah Diterbitkan & Terkirim ke Portal Peserta        |
|     (opsional link subtil: "Lihat Berkas ↗" untuk audit)    |
+-------------------------------------------------------------+
```

### Opsi Implementasi yang Direkomendasikan:

* **Opsi 1 (Sangat Direkomendasikan - Bersih, Logis & Tetap Memiliki Akses Audit)**:
  - **Hapus tombol `[Unduh PDF]` sepenuhnya**.
  - Tampilkan kartu berstatus resmi:
    - Ikon dokumen PDF resmi.
    - Judul: **Surat Keterangan Diterima Resmi**.
    - Badge/Indikator Hijau Halus: **Telah Diterbitkan & Terkirim ke Peserta**.
    - Link Utilitas Audit Subtil: Di sisi kanan, sediakan teks link kecil yang tidak mencolok: `Lihat Berkas ↗` (membuka tab baru). Jika sewaktu-waktu ada pergantian staf admin atau audit inspektorat, admin lain tetap dapat mengecek berkas yang diunggah tanpa membuat antarmuka terasa kaku.
* **Opsi 2 (Ultra-Minimalis - Tanpa Tombol Sama Sekali)**:
  - **Hapus kedua tombol (`[Pratinjau]` dan `[Unduh PDF]`)**.
  - Kartu murni berfungsi sebagai penanda konfirmasi (*read-only status badge*):
    `✓ Surat Keputusan Penerimaan telah berhasil diunggah dan terkirim ke peserta.`

---

## 3. Rincian Perubahan Berkas

Pekerjaan ini mematuhi aturan `.agents/rules/AGENTS.md` (tidak mengubah route backend, mempertahankan integritas controller):

1. **Blade View**:
   - [`resources/views/admin/pendaftaran/show.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/pendaftaran/show.blade.php) (baris 286-297):
     - Menghapus tombol `<a class="adm-btn-doc-download">Unduh PDF</a>`.
     - Mengubah markup kartu menjadi indikator status resmi terbit dengan link pratinjau subtil (atau murni status badge).
2. **CSS Styling**:
   - [`resources/css/pages/admin/pendaftaran-show.css`](file:///d:/laragon/www/MAGKOM/resources/css/pages/admin/pendaftaran-show.css):
     - Merapikan tata letak `.adm-decision-doc-card` agar ramping, rapi, dan menggunakan token warna Sleman Kominfo DS-MagKom.
     - Menghapus styling tombol download yang tidak terpakai.
3. **Route & Controller Backend**:
   - Route `admin.pendaftaran.surat-diterima` dan `admin.pendaftaran.surat-diterima.pratinjau` tetap dipertahankan di backend agar tidak mematahkan API/kontrak dan test suite yang ada.

---

## 4. Rencana Pengujian & Quality Gate

* `npm run build` (kompilasi CSS)
* `vendor/bin/pint --test`
* `vendor/bin/phpstan analyse --memory-limit=1G`
* `php artisan test tests/Feature/AdminVerifikasiKategoriDanUploadSuratTest.php`
* `php artisan test tests/Feature/AdminPendaftaranPerbaikanFlowTest.php`

