# Rencana Kerja: Penyederhanaan Footer Landing Page Sesuai Desain Asli (Clean 4-Column Layout)

**Tanggal:** 27 Oktober 2026  
**Status:** Menunggu Persetujuan Pengguna (*Pending Approval*)  
**Konteks:** Menyesuaikan isi footer pada landing page baru agar tidak terlalu padat informasi (*overcrowded*), dengan mengadopsi struktur 4 kolom bersih, tenang, dan elegan persis seperti desain aslinya (sesuai gambar referensi pengguna).

---

## 1. Analisis Masalah & Kebutuhan Pengguna
- **Masukan Pengguna:** Footer yang baru dibuat terlalu banyak memuat teks detail, badge, bullet panah, dan ikon berjejal. Pengguna menginginkan isi footer kembali bersih, minimalis, dan terstruktur rapi seperti pada gambar referensi lama (gambar kanan).
- **Struktur Footer Target (Identik dengan Gambar Referensi):**
  1. **Kolom 1: Alamat**
     - Alamat kantor dinas lengkap: *Jl Parasamya (Komplek Kantor Bupati Sleman) Beran, Tridadi, Sleman, Yogyakarta 55511*
     - Telepon: *(0274) 868405*
     - Email: *kominfo@slemankab.go.id*
     - Jam Layanan: *Senin sampai Kamis 07.30 sampai 15.30 WIB, Jumat 07.30 sampai 14.30 WIB*
  2. **Kolom 2: Media Sosial**
     - `kominfo_sleman` (Instagram resmi Diskominfo Sleman)
     - `diskominfosleman` (Twitter / X resmi Diskominfo Sleman)
  3. **Kolom 3: Tautan Terkait**
     - Kebijakan Privasi (tautan in-app ke `route('kebijakan-privasi')`)
     - Situs Kominfo Sleman (tautan eksternal ke portal dinas `kominfo.slemankab.go.id`)
     - Sleman Digital (tautan ke portal cerdas `slemankab.go.id`)
     - Verifikasi Dokumen Sah (opsional / tautan cek dokumen digital)
  4. **Kolom 4: Akun**
     - Masuk Peserta (`route('login')`)
     - Masuk Pembimbing (`route('login')`)
     - Masuk Admin (`route('login')`)
- **Estetika Visual:**
  - Mempertahankan background *Navy Dark* bergradasi dan efek partikel Wi-Fi emas melayang yang elegan di latar belakang.
  - Mengubah warna judul kolom menjadi biru cerah/cyan (`#38bdf8` / `#00b4d8`) seperti gambar referensi.
  - Menghilangkan ikon panah kecil `>` dan badge berlebih agar tipografi terlihat lega, bersih, dan nyaman dibaca.

---

## 2. File yang Akan Diubah

| File | Bagian yang Dimodifikasi | Rencana Perubahan |
| :--- | :--- | :--- |
| `resources/views/public/landing.blade.php` | Tag `<footer id="kontak" class="landing-footer">` | Mengganti isi `.landing-footer-grid` dengan struktur 4 kolom bersih (Alamat, Media Sosial, Tautan Terkait, Akun) dan menghapus bottom bar yang redundan. |
| `public/css/landing-modern.css` | Class `.landing-footer-grid`, `.footer-col-title`, `.footer-nav-list` | Menyesuaikan style judul kolom dengan warna cyan/biru muda (`#38bdf8`), jarak antar link yang lega, dan menghilangkan efek bullet icon berlebih. |

---

## 3. Langkah Implementasi Rinci

### Langkah 1: Modifikasi Markup Blade (`landing.blade.php`)
- Mengganti blok kolom 1 sampai 4 di dalam `.landing-footer-grid`:
  - **Kolom 1 (`Alamat`)**: Paragraf beralamat lengkap, kontak telepon, email, dan jam pelayanan.
  - **Kolom 2 (`Media Sosial`)**: Daftar link media sosial resmi Diskominfo Sleman dengan `target="_blank"`.
  - **Kolom 3 (`Tautan Terkait`)**: Link Kebijakan Privasi, Situs Resmi Kominfo, dan Sleman Digital.
  - **Kolom 4 (`Akun`)**: Link cepat login untuk Peserta, Pembimbing, dan Admin.
- Menyederhanakan baris bawah (*copyright bar*) agar bersih dan rapi.

### Langkah 2: Penyesuaian Styling CSS (`landing-modern.css`)
- Mengatur warna `.footer-col-title` menjadi `#38bdf8` (biru cyan cerah, tanpa border/shadow yang berat).
- Menghilangkan pseudo-icon panah `>` dan badge `PDF` dari link navigasi.
- Mengatur line-height dan margin paragraf pada kolom alamat agar rapi dan proporsional.

### Langkah 3: Uji Tampilan & Verifikasi
- Mengosongkan cache view (`php artisan view:clear`).
- Melakukan verifikasi HTTP 200 via curl dan pratinjau rendering.
- Memastikan visual footer bersih, nyaman dipandang, dan sesuai 100% dengan gambar referensi kanan.

---

## 4. Konvensi & Bahasa
- Seluruh kode sumber, class name, dan variabel ditulis dalam **Bahasa Inggris**.
- Penjelasan dan komunikasi kepada pengguna disampaikan dalam **Bahasa Indonesia**.
