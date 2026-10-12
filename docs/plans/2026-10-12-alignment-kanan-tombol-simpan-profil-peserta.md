# Rencana Kerja: Penyesuaian Alignment Kanan Tombol Simpan Biodata Profil Peserta

## 1. Konteks & Permasalahan
Pada halaman **Biodata & Profil Peserta** (`resources/views/peserta/profil/show.blade.php`), di bagian bawah formulir (*Action Bar*):
- Teks catatan *"Periksa kembali data Anda sebelum menekan tombol simpan."* dan tombol **"Simpan Biodata Profil"** saat ini berada di sebelah kiri atau turun ke baris baru dan menempel di sisi kiri.
- Hal ini menyebabkan tampilan bagian bawah form terasa kosong di sisi kanan dan tidak simetris dengan grid input di atasnya (kolom Kontak Darurat / Wali & Nomor Telepon Darurat yang terbagi rata 2 kolom).

## 2. Solusi Desain yang Diusulkan
Menyelaraskan tombol **"Simpan Biodata Profil"** ke **sisi kanan formulir (*Right-Aligned*)**:

1. **Struktur Layout Action Bar (`.form-action-bar`)**:
   - Menata ulang layout action bar dengan `display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;`.
   - Membungkus tombol simpan dalam kontainer aksi `.form-action-buttons` dengan aturan `margin-left: auto;` agar:
     - Jika berada dalam satu baris (layar lebar): Teks informasi berada di sebelah kiri, tombol simpan berada di ujung kanan.
     - Jika teks informasi wrap ke atas: Tombol simpan tetap secara konsisten menempel rapi di ujung kanan bawah formulir, sejajar dengan batas kanan input *Nomor Telepon Darurat*.

2. **Penyempurnaan Visual & Estetika**:
   - Menambahkan ikon info kecil halus di samping teks bantuan untuk meningkatkan keterbacaan (*affordance*).
   - Memastikan tombol simpan memiliki efek hover yang mulus dengan bayangan biru lembut khas tema MagKom.
   - Pada tampilan mobile (`max-width: 640px`): Tombol simpan tetap fleksibel memenuhi lebar layar (*full-width*) agar mudah ditekan di layar sentuh ponsel.

## 3. File yang Dimodifikasi
1. `resources/views/peserta/profil/show.blade.php`:
   - Penyesuaian CSS `.form-action-bar`, `.form-action-meta`, dan penambahan `.form-action-buttons`.
   - Penyesuaian struktur markup pada bagian bawah formulir biodata peserta.

## 4. Pengujian & Verifikasi
1. **Pengujian Visual**:
   - Memverifikasi tombol simpan berada di pojok kanan formulir pada layar desktop dan tablet.
   - Memverifikasi tampilan responsif pada layar mobile (tombol full width yang nyaman ditekan).
2. **Pengujian Fungsional**:
   - Memastikan form submission tetap berjalan sukses menyimpan biodata profil peserta.
   - Menjalankan test suite peserta terkait untuk memastikan integritas fitur profil.
