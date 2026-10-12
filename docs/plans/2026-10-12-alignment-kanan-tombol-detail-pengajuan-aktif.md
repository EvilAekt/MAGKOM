# Rencana Kerja: Penyelarasan Posisi Kanan (Right-Aligned) Tombol "Buka Berkas & Detail Pengajuan"

## 1. Konteks & Permasalahan
Pada kartu spotlight pendaftaran aktif di halaman **Riwayat Pendaftaran Magang** (`resources/views/peserta/pendaftaran/index.blade.php`):
- Tombol aksi utama **"Buka Berkas & Detail Pengajuan"** saat ini jatuh ke baris baru di bawah teks informasi sistem (*BR-03 & BR-17*) dan menempel di **sebelah kiri**.
- Hal ini menyebabkan bagian kanan kartu terlihat kosong dan tidak seimbang (*unbalanced visual weight*), serta tombol aksi terkesan tenggelam di bawah teks catatan.

## 2. Solusi Desain yang Diusulkan
Menyelaraskan tombol aksi ke **sisi kanan (*Right-Aligned*)** dengan pembagian tata letak footer kartu yang bersih dan modern:

1. **Struktur Layout Footer Kartu (`.active-card-footer`)**:
   - Menambahkan border pemisah halus putus-putus (`border-top: 1px dashed var(--neutral-200); padding-top: 18px;`) di bawah grid 4 ringkasan informasi magang.
   - Menggunakan flexbox layout `display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;`.

2. **Pembagian Sisi Kiri & Sisi Kanan**:
   - **Sisi Kiri (`.active-card-note`)**:
     - Membatasi lebar teks catatan (*max-width: 600px*) dengan ikon informasi biru MagKom, sehingga penjelasan sistem tetap nyaman dibaca (*readable*).
   - **Sisi Kanan (`.active-card-actions`)**:
     - Menggunakan `margin-left: auto; display: flex; align-items: center; gap: 10px;`.
     - Tombol **"Buka Berkas & Detail Pengajuan"** secara konsisten menempel rapi di **ujung kanan bawah kartu**, sejajar dengan kolom kartu ringkasan status penempatan di atasnya.
     - Jika berada di satu baris (layar lebar): Teks di kiri, tombol di kanan sejajar.
     - Jika teks membungkus di layar medium/sempit: Tombol tetap menempel rapi di sisi kanan bawah.

3. **Responsivitas Mobile**:
   - Pada layar ponsel pintar (`max-width: 640px`): Tombol aksi otomatis melebar penuh (*full-width*) agar mudah dan ergonomis ditekan.

4. **Konsistensi dengan Dashboard Peserta**:
   - Menyesuaikan juga tombol aksi kartu ringkasan seleksi pendaftaran pada `resources/views/peserta/dashboard.blade.php` agar keduanya memiliki konsistensi layout aksi kanan yang selaras di seluruh portal peserta.

## 3. File yang Dimodifikasi
1. `resources/views/peserta/pendaftaran/index.blade.php`:
   - Penambahan style CSS `.active-card-footer`, `.active-card-note`, `.active-card-actions`.
   - Perbaruan markup footer kartu pengajuan aktif.
2. `resources/views/peserta/dashboard.blade.php` (penyelarasan opsional jika diperlukan konsistensi).

## 4. Pengujian & Verifikasi
1. **Pengujian Visual**:
   - Memastikan tombol "Buka Berkas & Detail Pengajuan" menempel di pojok kanan kartu pada akun demo `pendaftar@magkom.go.id` dan `peserta@magkom.go.id`.
2. **Pengujian Otomatis**:
   - Menjalankan `tests/Feature/PesertaPendaftaranFlowTest.php` dan `tests/Feature/PesertaFullPortalFeatureTest.php` untuk memastikan seluruh alur pendaftaran dan navigasi tetap 100% PASS.
