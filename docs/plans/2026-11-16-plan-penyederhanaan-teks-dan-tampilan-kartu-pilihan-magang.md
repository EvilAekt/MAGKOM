# Rencana Kerja: Penyederhanaan Bahasa (UX Copywriting) & Perapian Tata Letak Kartu Pilihan Magang

**Tanggal:** 16 November 2026  
**Status:** Draf Perencanaan (Menunggu Persetujuan Pengguna)  
**Dokumen Acuan:** `docs/01-PRD-MagKom.md`, `docs/07-DESIGN-SYSTEM-MAGKOM.md`, `.agents/rules/AGENTS.md`  
**Aset Referensi:** Tangkapan Layar `media_1794806805839_93624d9c.png`

---

## 1. Ringkasan Akar Masalah (Root Cause Analysis)

Berdasarkan tangkapan layar pengguna (`media_1794806805839_93624d9c.png`):
1. **Jargon Teknis Tidak Ramah Pengguna:**  
   Istilah *"Landing Page"* adalah istilah teknis *developer/marketer* yang tidak dipahami oleh calon peserta awam (siswa SMK atau mahasiswa). Kata ini membingungkan dan terasa kaku.
2. **Kepadatan Teks & Tumpang Tindih Tata Letak (*Layout Collision*):**  
   Kalimat *"Melanjutkan permohonan magang dari kuota pilihan Anda."* tampil secara visual di samping judul kartu, menyebabkan header terdesak ke 2 baris dan mendorong badge *"Individu (1 Orang)"* jatuh ke baris baru secara berantakan.
3. **Tautan Bawah Terlalu Panjang:**  
   Frasa *"Ubah Pilihan di Landing Page ↺"* terlalu bertele-tele dan mengulang jargon yang sama. Cukup diganti menjadi aksi yang lugas: *"Ubah Pilihan ↺"*.

---

## 2. Sasaran Perubahan (Target Copywriting & Visual)

| Elemen | Teks Lama (Bermasalah) | Teks Baru (Bersih & Ramah Peserta) | Alasan & Manfaat |
| :--- | :--- | :--- | :--- |
| **Judul Kartu** | `Pilihan Magang dari Landing Page` | **`Pilihan Magang Anda`** | Sederhana, personal, langsung ke intinya, tanpa istilah teknis *landing page*. |
| **Teks Penjelas Kanan** | `Melanjutkan permohonan magang dari kuota pilihan Anda.` | **Dihapus dari tampilan visual** (hanya di level kode/aksesibilitas). | Menghilangkan penumpukan teks agar header lega dan badge pill tetap rapi di pojok kanan atas. |
| **Tautan Aksi Bawah** | `Ubah Pilihan di Landing Page ↺` | **`Ubah Pilihan ↺`** | Singkat, jelas, dan intuitif bagi peserta magang. |
| **CSS Kelas `.sr-only`** | Belum memiliki utility CSS ketat di `auth-depth.css` | Ditambahkan utility class standar aksesibilitas CSS `.sr-only` agar tidak merusak visual. | Mencegah teks tersembunyi merusak tata letak visual. |

---

## 3. Komparasi Tampilan Antarmuka (Before vs After)

```
[ SEBELUM (Bermasalah - media_1794806805839_93624d9c.png) ]
┌────────────────────────────────────────────────────────────────────────┐
│ [🧭] Pilihan Magang            Melanjutkan permohonan                  │
│     dari Landing Page          magang dari kuota pilihan Anda.         │
│ (Individu 1 Orang)                                                     │
│ Administrasi Perkantoran (SMK) • Penempatan: Aplikasi Informatika...   │
│ Mulai Periode: Desember 2026                     ● Tersedia (4 Kursi) │
│ ────────────────────────────────────────────────────────────────────── │
│                                      Ubah Pilihan di Landing Page ↺   │
└────────────────────────────────────────────────────────────────────────┘

[ SESUDAH (Bersih, Rapi, Bebas Jargon) ]
┌────────────────────────────────────────────────────────────────────────┐
│ [🧭] Pilihan Magang Anda                          (Individu 1 Orang)   │
│                                                                        │
│ Administrasi Perkantoran (SMK) • Penempatan: Aplikasi Informatika...   │
│ Mulai Periode: Desember 2026                     ● Tersedia (4 Kursi) │
│ ────────────────────────────────────────────────────────────────────── │
│                                                        Ubah Pilihan ↺  │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 4. Rincian Perubahan Berkas

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/auth/partials/_selected_quota_card.blade.php` | **Modify** | 1. Ubah judul menjadi `Pilihan Magang Anda`.<br>2. Hapus teks `Melanjutkan permohonan...` dari visual.<br>3. Ubah teks link menjadi `Ubah Pilihan ↺`. |
| `resources/css/pages/auth/auth-depth.css` | **Modify** | Pastikan selector `.sr-only` didefinisikan dengan standar CSS clipping jika digunakan, dan tata letak header `.lpc-header` terkunci 1 baris (`align-items: center`). |
| `tests/Feature/LandingPageToRegistrationPrefillTest.php` | **Modify** | Sesuaikan assertion pengujian dari teks lama `Melanjutkan permohonan...` menjadi `Pilihan Magang Anda` dan `Ubah Pilihan`. |

---

## 5. Langkah-Langkah Eksekusi Bertahap

- [x] **Langkah 1: Perbarui Teks & Markup pada Partial Blade (`_selected_quota_card.blade.php`)**
  - Ganti teks judul dari `Pilihan Magang dari Landing Page` menjadi `Pilihan Magang Anda`.
  - Hapus teks penjelasan yang menyebabkan layout collision.
  - Ganti teks tautan menjadi `Ubah Pilihan` dengan simbol `↺`.
- [x] **Langkah 2: Periksa & Tambahkan Utility `.sr-only` pada `resources/css/pages/auth/auth-depth.css`**
  - Pastikan setiap elemen tersembunyi tidak mengambil ruang visual (`display: none` atau `clip: rect(...)`).
- [x] **Langkah 3: Sinkronkan Assertion Pengujian PHPUnit (`LandingPageToRegistrationPrefillTest.php`)**
  - Perbarui assertion teks agar sesuai dengan salinan (*copy*) bahasa baru.
- [x] **Langkah 4: Kompilasi Aset Frontend dengan Vite (`npm run build`)**
  - Jalankan `npm run build` untuk memastikan bundling bersih.
- [x] **Langkah 5: Verifikasi Quality Gates**
  - Jalankan `php artisan test` untuk suite terkait.
  - Jalankan `vendor/bin/pint --test` untuk formatting PSR-12.
  - Verifikasi tampilan HTML hasil rendering server lokal.

---

## 6. Kriteria Selesai (Definition of Done)

- [x] Kata *"Landing Page"* tidak lagi muncul pada kartu ringkasan magang.
- [x] Judul kartu ringkas dan bersih: **"Pilihan Magang Anda"**.
- [x] Header kartu rapi sejajar: judul di sebelah kiri, badge tipe di sebelah kanan tanpa teks bertumpuk.
- [x] Tautan aksi bawah ringkas: **"Ubah Pilihan ↺"**.
- [x] Semua pengujian otomatis PHPUnit lulus 100% HIJAU.
- [x] Format kode lulus Laravel Pint.

