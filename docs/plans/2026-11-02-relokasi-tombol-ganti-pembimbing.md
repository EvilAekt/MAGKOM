# Rencana Kerja: Relokasi & Redesain Tombol Ganti Pembimbing Lapangan

**Dokumen Rencana**: `docs/plans/2026-11-02-relokasi-tombol-ganti-pembimbing.md`  
**Status**: Menunggu Konfirmasi & Persetujuan User  
**Tujuan**: Memperbaiki tata letak dan estetika visual tombol aksi *Ganti Pembimbing* yang saat ini terlihat canggung (berupa tautan teks biru bergaris bawah di samping badge beban mentee), serta menempatkannya pada posisi yang paling ergonomis, rapi, dan sesuai dengan standar antarmuka modern.

---

## 1. Analisis Masalah UI/UX Saat Ini

Berdasarkan tangkapan layar `media_1793554173631_c2db5d38.png` dan `media_1793554236159_706ac740.png`:
1. **Pencampuran Peran Kolom Data & Kolom Aksi**:
   Kolom *Pembimbing Lapangan* adalah kolom representasi entitas data (siapa pembimbingnya dan berapa beban menteenya). Menempatkan tautan interaktif teks biru bergaris bawah (`Ganti`) langsung di samping badge info membuat hierarki informasi rancu dan terkesan seperti teks hyperlink web era lama yang belum dipoles.
2. **Kepadatan Kolom Aksi di Sebelah Kanan**:
   Di sisi kanan tabel, sudah terdapat kolom **AKSI** dengan 4 tombol berjajar: `Detail`, `Surat`, `Unduh`, dan `Batalkan`. Menambahkan tombol teks lain secara mentah ke kolom AKSI berpotensi menyebabkan *overflow* atau tombol bertumpuk (*wrap*) pada layar resolusi menengah.

---

## 2. Evaluasi Opsi Penempatan & Desain

Kami menyusun 3 pendekatan alternatif yang dapat dipilih:

| Kriteria | **Opsi 1: Dropdown Aksi Terpadu `[ ··· ]` di Kolom AKSI** *(Rekomendasi Utama)* | **Opsi 2: Contextual Micro-Button `[ ⇄ Ganti ]`** | **Opsi 3: Tombol Tambahan Mandiri di Kolom AKSI** |
| :--- | :--- | :--- | :--- |
| **Deskripsi** | Tombol aksi sekunder (`Ganti Pembimbing`, `Unduh PDF`, `Batalkan`) digabung ke dalam menu dropdown elipsis `···` di kolom AKSI. | Tombol tetap di kolom pembimbing, tapi didesain ulang menjadi micro-button elegan (bukan hyperlink garis bawah). | Tombol `[ ⇄ Ganti ]` ditaruh sejajar dengan tombol `[ Detail ]`, `[ Surat ]`, dll. di kolom AKSI. |
| **Tampilan Kolom Pembimbing** | **Sangat Bersih**: Hanya Nama + Badge `1 Mentee Aktif`. Bebas dari tombol aksi. | Cukup rapi: Ada micro-pill tombol kecil di samping badge beban mentee. | **Sangat Bersih**: Hanya Nama + Badge `1 Mentee Aktif`. |
| **Tampilan Kolom AKSI** | **Modern & Rapi**: `[ 👁 Detail ]` `[ ✉ Surat ]` `[ ··· ]` (Dropdown menu Linear/Stripe style). | Tetap 4 tombol seperti saat ini. | Padat (5 tombol berjajar), berisiko wrap/patah baris di layar sempit. |
| **Kecepatan Akses** | 2 klik (Klik `···` -> Pilih *Ganti Pembimbing*). | 1 klik langsung di samping pembimbing. | 1 klik langsung di kolom AKSI. |
| **Kelebihan** | Standar emas UI/UX web app modern; declutter tabel; kolom tidak meluber. | Cepat diakses tanpa perlu melirik ke ujung kanan kolom aksi. | Semua aksi terpusat di kolom AKSI. |
| **Kekurangan** | Menambah 1 klik ekstra (namun aksi ganti pembimbing bukan aksi frekuensi tinggi tiap menit). | Masih menempatkan elemen interaktif di dalam kolom data. | Ruang horizontal kolom aksi menjadi sangat lebar dan sesak. |

---

### Ilustrasi Visual Opsi

#### Visual Opsi 1 (Rekomendasi Utama: Dropdown More Actions `···`):
```text
[ PEMBIMBING LAPANGAN ]                 [ AKSI ]
--------------------------------------------------------------------------
Budi Santoso, S.Kom                     [ 👁 Detail ]  [ ✉ Surat ]  [ ··· ] ▾
[ 👥 1 Mentee Aktif ]                                                ┌──────────────────────┐
                                                                     │ 🔄 Ganti Pembimbing  │
                                                                     │ 📥 Unduh Surat (PDF) │
                                                                     │ ────────────         │
                                                                     │ ⛔ Batalkan          │
                                                                     └──────────────────────┘
```

#### Visual Opsi 2 (Micro-Action Button Halus di Kolom Pembimbing):
```text
[ PEMBIMBING LAPANGAN ]                 [ AKSI ]
--------------------------------------------------------------------------
Budi Santoso, S.Kom                     [ 👁 Detail ]  [ ✉ Surat ]  [ Unduh ]  [ Batalkan ]
[ 👥 1 Mentee Aktif ]  [ ⇄ Ganti ]  <-- Button mini (pill abu-abu border halus + icon swap)
```

---

## 3. Rencana Perubahan Berkas (Jika Opsi 1 Dipilih)

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/admin/penempatan/index.blade.php` | Modify | 1. Hapus tautan `<button>Ganti</button>` dari cell pembimbing lapangan.<br>2. Perbarui kolom `<td style="text-align: right;">` untuk menerapkan tombol menu `[ ··· ]` yang memuat aksi `Ganti Pembimbing`, `Unduh`, dan `Batalkan`.<br>3. Tambahkan script handler dropdown toggler dengan auto-close saat klik di luar area. |
| `tests/Feature/AdminChildPagesExtendedVisualTest.php` | Modify / Test | Memastikan modal pindah pembimbing tetap dapat dibuka dengan benar dari elemen pemicu baru dan tidak ada JavaScript error. |

---

## 4. Langkah Implementasi Bertahap

- [ ] **Langkah 1**: Bersihkan cell `PEMBIMBING LAPANGAN` di `resources/views/admin/penempatan/index.blade.php` sehingga hanya menampilkan nama pembimbing yang bersih dan badge beban mentee tanpa tautan biru bergaris bawah.
- [ ] **Langkah 2**: Bangun komponen dropdown menu aksi sekunder pada kolom `AKSI` di `resources/views/admin/penempatan/index.blade.php`:
  - Tombol Primer: `[ 👁 Detail ]` dan `[ ✉ Surat ]` tetap terlihat langsung.
  - Tombol Sekunder: `[ ··· ]` (Aksi Lainnya) yang ketika diklik memunculkan dropdown menu:
    - *Ganti Pembimbing* (`onclick="openPindahModal(...)"`)
    - *Unduh Surat* (`href="..."`)
    - *Batalkan Penempatan* (`onclick="openBatalModal(...)"`)
- [ ] **Langkah 3**: Tambahkan penataan gaya CSS untuk dropdown: `z-index`, bayangan lembut (*box-shadow: 0 4px 12px rgba(0,0,0,0.1)*), *border-radius: 8px*, serta dukungan *dark mode*.
- [ ] **Langkah 4**: Jalankan pengujian visual di browser dengan Playwright untuk memverifikasi kerapian antarmuka dan interaksi klik dropdown.
- [ ] **Langkah 5**: Jalankan `php artisan test` untuk memastikan seluruh *test suite* lulus.

---

## 5. Kriteria Selesai (Definition of Done)

- [ ] Kolom *Pembimbing Lapangan* tidak lagi memiliki teks hyperlink bergaris bawah yang mengganggu.
- [ ] Aksi *Ganti Pembimbing* memiliki rumah yang jelas, rapi, dan mudah diakses tanpa membuat kolom tabel berantakan.
- [ ] Modal *Alihkan Pembimbing Lapangan* berfungsi normal saat dipicu.
- [ ] Tampilan responsif dan rapi baik di mode terang maupun mode gelap.
- [ ] Seluruh pengujian otomatis lulus 100%.

