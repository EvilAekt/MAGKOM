# Rencana Kerja: Penyederhanaan Indikator Terkunci Sidebar Menjadi Ikon Gembok Minimalis (Icon-Only)

## 1. Konteks & Permasalahan
Pada sidebar peserta (`resources/views/layouts/peserta.blade.php`), item menu yang belum dapat diakses (Logbook Mingguan, Tugas & Progres, Lembar Nilai) saat ini menggunakan badge bertuliskan:
`[🔒 Terkunci]`

### Permasalahan Visual:
- Teks kata `"Terkunci"` memakan ruang horizontal yang cukup lebar pada sidebar.
- Menyebabkan area navigasi terasa sesak (*cluttered*) dan badge terlalu menonjol/memenuhi sisi kanan sidebar.

## 2. Solusi Desain yang Diusulkan
Menghilangkan teks `"Terkunci"` dan hanya menampilkan **ikon gembok minimalis (*icon-only lock badge*)**:

1. **Desain Ikon Gembok Minimalis (`.badge-locked`)**:
   - Berbentuk kotak bundar halus (`width: 22px; height: 22px; border-radius: 6px`).
   - Latar belakang semi-transparan elegan (`background: rgba(255, 255, 255, 0.12)`).
   - Ikon gembok vektor SVG yang presisi dan tajam (`width: 13px; height: 13px`).
   - `margin-left: auto;` agar tetap menempel rapi di sisi kanan baris navigasi.
   - Tetap menyertakan tooltip `title="Terkunci: Fitur tersedia setelah permohonan magang disetujui & ditempatkan"` dan `aria-label="Terkunci"` untuk aksesibilitas dan kemudahan pemahaman pengguna.

2. **Dampak Estetika**:
   - Teks menu (misal: "Logbook Mingguan", "Tugas & Progres") memiliki ruang horizontal yang lega.
   - Tampilan sidebar menjadi jauh lebih elegan, modern, bersih, dan setara dengan standar UI modern dashboard (seperti GitHub/Linear/Figma).

## 3. File yang Dimodifikasi
1. `resources/views/layouts/peserta.blade.php`:
   - Penyesuaian CSS `.badge-locked` menjadi ukuran persegi halus (22x22px).
   - Penghapusan teks tulisan `"Terkunci"` dari dalam tag badge, menyisakan ikon gembok SVG dengan `aria-label="Terkunci"`.

## 4. Pengujian & Verifikasi
1. **Pengujian Visual**:
   - Memeriksa tampilan sidebar saat login sebagai peserta belum diterima (`pendaftar@magkom.go.id`).
   - Memastikan hanya ikon gembok rapi yang tampil di sisi kanan menu yang terkunci tanpa teks panjang.
2. **Automated Testing**:
   - Menjalankan `tests/Feature/PesertaAksesFiturMagangTest.php` untuk memastikan seluruh tes proteksi rute dan pendeteksian status terkunci tetap 100% PASS.
