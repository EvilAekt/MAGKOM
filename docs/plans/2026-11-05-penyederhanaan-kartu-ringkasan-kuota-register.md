# Rencana Kerja: Penyederhanaan dan Pembersihan Tampilan Kartu Ringkasan Kuota Register

**Status Dokumen**: Menunggu Persetujuan Pengguna  
**Tanggal**: 5 November 2026  
**Target File**: `resources/views/auth/register.blade.php`  
**Referensi Tampilan**: Gambar yang diunggah pengguna (`media_1793853802030_6228886f.png`)

---

## 1. Analisis Kebutuhan & Masalah

### 1.1 Kondisi Tampilan Saat Ini
Pada halaman `/register` (ketika calon peserta datang dari Beranda setelah memilih kuota), kartu **"Pilihan Magang Terpilih"** saat ini memiliki tampilan yang padat dan bertumpuk:
1. Terdapat tautan teks navigasi **`← Ganti Pilihan Kuota`** di sebelah kanan periode mulai.
2. Terdapat teks penjelasan panjang di bagian bawah kartu:  
   *"Pendaftaran Ketua Tim: Anda mendaftar sebagai perwakilan tim untuk 3 orang. Rekan tim tidak perlu membuat akun terpisah, data rekan tim diisi lengkap pada formulir ini."*  
   (Teks ini redundan karena formulir di bawahnya sudah memiliki seksi data anggota tim).
3. Terdapat pembatas garis putus-putus (*dashed line*) ganda yang membuat kartu terlihat berat (*cluttered*).

### 1.2 Tujuan Perubahan
1. **Menghilangkan tombol/tautan `← Ganti Pilihan Kuota`** sesuai instruksi langsung dari pengguna.
2. **Menghilangkan paragraf teks penjelasan panjang (*declutter*)** agar kartu menjadi ringkas, fokus, dan bersih.
3. **Penyelarasan Tata Letak Menjadi 2 Baris Kompak & Elegan**:
   - **Baris 1**: Nama Jurusan & Kategori (`Administrasi Perkantoran (SMK)`) di sebelah kiri, serta Badge Tipe (`Kelompok (3 Orang)` atau `Mandiri (1 Orang)`) di sebelah kanan.
   - **Baris 2**: Periode Mulai (`Periode: Desember 2026`) di sebelah kiri, serta Badge Status Sisa Kuota (`🟢 Sisa 4 Kursi Tersedia` / `🟡 Waiting List`) di sebelah kanan.
   - Menghilangkan pembatas garis putus-putus berulang sehingga kartu tampak lebih modern dan lapang.

---

## 2. Perbandingan Desain (Sebelum vs Sesudah)

```
[SEBELUM]:
+---------------------------------------------------------------------------------+
| [Icon]  PILIHAN MAGANG TERPILIH                          [Kelompok (3 Orang)]   |
|         Administrasi Perkantoran (SMK)                                          |
|         Periode Mulai: Desember 2026                      ← Ganti Pilihan Kuota |
|         - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - |
|         Total Kapasitas: 4 Kursi                        [🟢 Sisa 4 Kursi Tersedia]|
|         - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - |
|         Pendaftaran Ketua Tim: Anda mendaftar sebagai perwakilan tim untuk 3... |
+---------------------------------------------------------------------------------+

[SESUDAH (RINGKAS & BERSIH)]:
+---------------------------------------------------------------------------------+
| [Icon]  Administrasi Perkantoran (SMK)                   [Kelompok (3 Orang)]   |
|         Periode Mulai: Desember 2026                   [🟢 Sisa 4 Kursi Tersedia]|
+---------------------------------------------------------------------------------+
```

---

## 3. Rincian Perubahan Berkas

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/auth/register.blade.php` | Modify | Hapus tombol `← Ganti Pilihan Kuota`, hapus paragraf deskripsi ketua tim, padatkan struktur kartu ringkasan kuota menjadi 2 baris kompak (Nama Jurusan + Badge Tipe, Periode Mulai + Badge Sisa Kuota). |

---

## 4. Rencana Langkah Kerja (Checklist Implementasi)

- [x] **Langkah 1: Modifikasi Markup Blade di `resources/views/auth/register.blade.php`**
  - [x] Hapus elemen tautan `<a href="{{ route('landing') }}#kuota">← Ganti Pilihan Kuota</a>`.
  - [x] Hapus blok teks `@if(($selectedTipe ?? request('tipe')) === 'kelompok') Pendaftaran Ketua Tim: ... @endif`.
  - [x] Format ulang susunan informasi: Baris atas untuk Judul Jurusan dan Badge Tipe, baris bawah untuk Periode Mulai dan Badge Sisa Kuota.
  - [x] Hapus garis pemisah putus-putus yang menumpuk.
- [x] **Langkah 2: Build Aset & Verifikasi Tampilan**
  - [x] Jalankan `npm run build` untuk memastikan kompilasi aset berjalan sukses.
  - [x] Jalankan automated test feature: `php artisan test tests/Feature/PendaftaranPesertaTest.php tests/Feature/PendaftaranKelompokTest.php`.
  - [x] Jalankan Laravel Pint: `vendor/bin/pint --test`.

---

## 5. Kriteria Selesai (Definition of Done)
- [x] Tautan `← Ganti Pilihan Kuota` sudah tidak ada di tampilan.
- [x] Teks paragraf panjang pendaftaran tim ketua sudah dibersihkan.
- [x] Tampilan kartu ringkasan kuota menjadi rapi, ringkas, dan proporsional (2 baris informatif).
- [x] Seluruh automated feature tests tetap lulus 100% hijau.

