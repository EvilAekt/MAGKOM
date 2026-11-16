# Rencana Perapian Tampilan Kuota & Penyederhanaan Teks di Halaman Registrasi (/register)

**Tanggal**: 16 November 2026  
**Dokumen**: `docs/plans/2026-11-16-plan-perapian-visibilitas-kuota-dan-penyederhanaan-teks.md`  
**Status**: Menunggu Konfirmasi User  
**Referensi Masalah**:
1. Gambar 1 (`media_1794813882750_7dd40f07.png`): Kartu status kuota terlalu tebal/kebesaran dan teks di bawah dropdown terlalu panjang.
2. Gambar 2 (`media_1794813935670_36ed2045.png`): Teks petunjuk dinamis di bawah penempatan dan jurusan terlalu panjang dan memenuhi layar.

---

## 1. Analisis Masalah

### 1.1 Masalah 1: Teks di Bawah Dropdown Terlalu Panjang
Saat calon pendaftar memilih jenjang atau penempatan, JavaScript menimpa teks petunjuk dengan kalimat panjang yang berulang:
- ❌ *“Menampilkan 4 bidang penempatan yang membuka formasi untuk jenjang MAHASISWA.”*
- ❌ *“Menampilkan 4 jurusan yang relevan untuk penempatan Aplikasi Informatika & E-Government (EGOV).”*
- ❌ *“Menampilkan 1 jurusan yang relevan untuk penempatan Sekretariat Dinas.”*

Teks ini memakan ruang vertikal, membuat visual form terlihat padat/penuh, dan terasa bising (*noisy*).

### 1.2 Masalah 2: Kartu Status Kuota Kebesaran (Oversized)
Elemen `#live-quota-status-card` saat ini menggunakan layout kotak besar dua baris:
- Padding tebal (`12px 16px`).
- Judul tebal + paragraf deskripsi panjang yang mengulang kembali nama bidang dan bulan:  
  *“Penempatan di Sekretariat Dinas untuk periode Desember 2026 siap diajukan.”*
- Akibatnya, kartu ini mendorong field sekolah/universitas dan NIM/NISN jauh ke bawah, sehingga form terasa penuh sesak.

---

## 2. Rencana Perbaikan & Solusi Desain

### 2.1 Penyederhanaan Teks Petunjuk Dropdown (Clean & Subtle Hints)
- **Hapus penimpaan kalimat panjang** di JavaScript (`auth-depth.js`).
- Gunakan teks petunjuk standar yang pendek, tenang, dan proporsional:
  - **Di bawah Bulan**: *“Pilih periode mulai pelaksanaan magang.”* (1 baris ringkas).
  - **Di bawah Jenjang**: *“Pilih jenjang pendidikan yang sedang Anda tempuh.”* (1 baris ringkas).
  - **Di bawah Penempatan**: *“Pilih bidang penempatan yang tersedia kuotanya.”* (tetap tenang, tidak berubah menjadi kalimat panjang).
  - **Di bawah Jurusan**: *“Pilihan jurusan menyesuaikan bidang penempatan di atas.”* (tetap tenang dan konsisten).

### 2.2 Rampingkan Kartu Status Kuota Menjadi Compact Inline Banner
Ubah `#live-quota-status-card` menjadi bilah status horizontal (*slim inline badge/banner*) yang ramping dan elegan:
- **Layout Horizontal Satu Baris**:
  - `align-items: center` (bukan vertikal bertingkat).
  - Padding dirampingkan: dari `12px 16px` menjadi `7px 12px` (ketinggian berkurang ~50%).
  - Border-radius: `8px`.
  - Icon ramping: `16x16px` (proporsional dan sejajar vertikal dengan teks).
- **Isi Teks Langsung To-The-Point (Tanpa Kalimat Berulang)**:
  - **Jika Kuota Tersedia**:
    - Teks: `🟢 Kuota Tersedia: [X] Kursi Tersisa`
    - (Tidak perlu deskripsi panjang yang mengulang nama bidang/bulan karena sudah tertera di dropdown).
  - **Jika Kuota Penuh**:
    - Teks: `🟡 Kuota Penuh (Otomatis Masuk Waiting List)`
  - **Jika Kelompok Melebihi Kuota**:
    - Teks: `🟡 Sisa [X] Kursi — Kelompok Masuk Waiting List`

---

## 3. Perbandingan Sebelum & Sesudah

| Aspek | Kondisi Saat Ini (Sebelum) | Kondisi Baru (Sesudah) |
|---|---|---|
| **Teks di Bawah Penempatan** | `Menampilkan 4 bidang penempatan yang membuka formasi untuk jenjang MAHASISWA.` | `Pilih bidang penempatan yang tersedia kuotanya.` |
| **Teks di Bawah Jurusan** | `Menampilkan 1 jurusan yang relevan untuk penempatan Sekretariat Dinas.` | `Pilihan jurusan menyesuaikan bidang penempatan di atas.` |
| **Ukuran Kotak Kuota** | Tebal (`12px 16px`, 2 tingkat judul + paragraf) | Ramping & Sleek (`7px 12px`, 1 baris inline) |
| **Teks Kotak Kuota** | `Kuota Tersedia (4 Kursi Tersisa)`<br>`Penempatan di Sekretariat Dinas untuk periode Desember 2026 siap diajukan.` | `✓ Kuota Tersedia (4 Kursi Tersisa)` |
| **Ketinggian Vertikal** | ~75px (kebesaran) | ~34px (ramping, pas, dan proporsional) |

---

## 4. File yang Akan Dimodifikasi

1. **`resources/css/pages/auth/auth-depth.css`**:
   - Memperbarui styling `.auth-live-quota-card`:
     - Ubah ke `display: flex; align-items: center; gap: 8px; padding: 7px 12px; border-radius: 8px;`.
     - Atur font size teks status menjadi `13px; font-weight: 600; line-height: 1.2;`.
     - Hapus style terpisah `.lqc-title` dan `.lqc-desc` yang bertingkat, jadikan satu baris teks bersih.
2. **`resources/js/pages/auth/auth-depth.js`**:
   - Di `updateBidang()`: Jangan menimpa `hintBidang.textContent` dengan kalimat panjang.
   - Di `updateJurusan()`: Jangan menimpa `hintJurusan.textContent` dengan kalimat panjang.
   - Di `updateLiveQuotaCard()`: Render teks status dalam satu baris ringkas tanpa paragraf deskripsi tambahan.
3. **`resources/views/auth/register.blade.php`**:
   - Menyelaraskan markup `#live-quota-status-card` agar strukturnya satu baris (`icon` + `span text`).

---

## 5. Verifikasi & Quality Gates

1. **Vite Build**: Jalankan `npm run build` untuk memastikan bundle terkompilasi bersih.
2. **PHPUnit**: Jalankan `php artisan test --filter="LandingPageToRegistrationPrefillTest|AuthDepthVisualParityTest"`.
3. **Laravel Pint**: Jalankan `./vendor/bin/pint --test`.
4. **Visual Check**: Verifikasi langsung pada browser di `http://127.0.0.1:8000/register` untuk memastikan tampilan menjadi rapi, tidak kebesaran, dan teks tidak terlalu panjang.

---

## 6. Persetujuan Eksekusi
Rencana ini secara tepat mengatasi kedua keluhan Anda (merampingkan kartu status kuota dan menyingkat/merapikan teks di bawah dropdown).

Silakan berikan konfirmasi dengan mengetik **"gas"** untuk segera saya eksekusi!

