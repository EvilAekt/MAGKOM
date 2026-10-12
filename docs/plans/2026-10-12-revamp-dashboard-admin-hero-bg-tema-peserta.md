# Rencana Kerja: Revamp Dashboard Admin Mengadopsi Signature Hero Background MagKom (Tema Peserta)

Dokumen ini merinci rencana integrasi elemen visual signature dari **Dashboard Peserta** ke **Dashboard Admin** (`admin.dashboard`), khususnya penerapan **Hero Banner Premium dengan Background Image** (`images/dashboard_hero_bg.jpg`), lapisan gradien biru Kominfo Sleman, serta elemen *glassmorphism* modern agar konsisten dan memukau seperti tampilan portal peserta.

---

## 1. Latar Belakang & Inspirasi Desain

- **Permintaan Pengguna**: Mengadopsi desain visual dashboard peserta yang memiliki background hero gambar khas MagKom, sehingga tampilan dashboard admin tidak terlihat polos, datar, atau kaku.
- **Aset Visual yang Digunakan**:
  - Background image: `{{ asset('images/dashboard_hero_bg.jpg') }}` yang sudah tersedia di folder public.
  - Lapisan gradien: `linear-gradient(95deg, rgba(6, 65, 204, 0.96) 0%, rgba(6, 65, 204, 0.88) 45%, rgba(6, 65, 204, 0.35) 100%)`.
  - Radial highlight: `radial-gradient(circle at 85% 50%, rgba(255, 255, 255, 0.08) 0%, transparent 60%)`.
  - Efek *glassmorphism*: Badge chips ber-`backdrop-filter: blur(8px)`, border semi-transparan `rgba(255, 255, 255, 0.25)`, dan tombol glass mewah.

---

## 2. Struktur Komponen yang Akan Diterapkan

```
+-------------------------------------------------------------------------------------------------+
|  HERO GREETING BANNER (Background: dashboard_hero_bg.jpg + Blue Gradient Overlay)                |
|                                                                                                 |
|  [● Status: Operasional Aktif]                                 [Tanggal Live: Senin, 12 Okt]    |
|  Selamat Bertugas, Administrator! 👋                                                            |
|  Pusat Kendali Administrasi Magang Dinas Kominfo Sleman                                         |
|                                                                                                 |
|  [Chip: Periode November 2026] [Chip: Pendaftaran Dibuka]      [Glass Btn: Hub Sertifikat]      |
|  [Chip: Kuota Terisi 15/30]                                    [Glass Btn: Ekspor Laporan]      |
+-------------------------------------------------------------------------------------------------+
|  4 KPI METRICS CARDS (Desain Elevated Clean & Presisi)                                           |
|  [Menunggu Verifikasi]   [Revisi Dokumen]   [Antrean Cadangan]   [Peserta Magang Aktif]         |
+-------------------------------------------------------------------------------------------------+
|  SPLIT GRID 2-KOLOM (Cockpit Layout Terpadu)                                                    |
|                                                                                                 |
|  KOLOM KIRI (UTAMA - 68%)                          |  KOLOM KANAN (SIDEBAR - 32%)               |
|  1. Antrean Verifikasi Berkas                      |  1. Kapasitas & Realisasi Kuota            |
|     - Filter Pills (Semua, Diajukan, Perbaikan)    |     - Meter visual periode aktif           |
|     - Tabel avatar, SLA humanis, tombol Tinjau     |  2. Tenggat Selesai Magang (H-14 Watchdog) |
|  2. Alokasi Kuota per Jurusan (Card Grid)          |  3. Beban Pembimbing Lapangan (Mini List)  |
|  3. Jadwal Gelombang Magang                        |  4. Pintasan Modul Administrasi            |
+----------------------------------------------------+--------------------------------------------+
```

---

## 3. Rincian Teknis Perubahan

### A. Dashboard Hero Banner (Gaya Dashboard Peserta)
- Menggantikan header bar biasa dengan kontainer `.dashboard-hero` berlatar belakang `dashboard_hero_bg.jpg` dan gradien biru Kominfo.
- Menyematkan teks sapaan: *"Selamat Bertugas, Administrator! 👋"*.
- Mengintegrasikan informasi periode magang aktif langsung ke dalam hero banner dalam bentuk **Glass Meta Chips** (periode aktif, status pendaftaran buka/tutup, dan ringkasan kuota).
- Menyematkan tombol aksi di kanan atas hero banner dalam bentuk **Glass Action Buttons** (*Hub Sertifikat* dan *Ekspor Data*) yang menyatu secara harmonis dengan background hero.

### B. Harmonisasi Warna & Kartu Konten
- Kartu KPI, tabel antrean, dan widget samping diselaraskan dengan estetika bersih ber-border `#E2E8F0` dan sudut membulat `12px - 14px`.
- Tetap mempertahankan **Layout Grid 2-Kolom** yang sudah terbukti ergonomis dan bebas dari scrollbar horizontal tab yang canggung.
- Tetap mempertahankan **Filter Pills instan** pada tabel antrean verifikasi.

---

## 4. File yang Terdampak

| No | File | Deskripsi Perubahan |
|----|------|---------------------|
| 1 | [`resources/views/admin/dashboard.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/admin/dashboard.blade.php) | Memasang komponen `.dashboard-hero` dengan background image `images/dashboard_hero_bg.jpg`, gradien biru, glass chips, dan glass buttons seperti pada dashboard peserta. Menyelaraskan kartu konten dan grid 2-kolom. |

---

## 5. Checklist Implementasi

- [ ] **Langkah 1**: Menambahkan styling CSS `.dashboard-hero`, `.hero-content`, `.hero-greeting`, `.hero-meta-chip`, `.hero-glass-btn`, dan `.hero-date-badge` yang diadaptasi dari `peserta/dashboard.blade.php`.
- [ ] **Langkah 2**: Memasang markup HTML hero banner dengan background image `{{ asset('images/dashboard_hero_bg.jpg') }}` di posisi teratas dashboard admin.
- [ ] **Langkah 3**: Menata sapaan admin, status sistem, meta chips (periode, jadwal, kuota), dan tombol aksi glassmorphism (Hub Sertifikat & Ekspor).
- [ ] **Langkah 4**: Mengintegrasikan hero banner dengan KPI cards dan grid 2-kolom di bawahnya tanpa merusak tata letak data yang sudah rapi.
- [ ] **Langkah 5**: Menjalankan render testing dan verifikasi di Laravel engine untuk memastikan 100% bebas error.

---

## 6. Definition of Done (DoD)

1. Dashboard Admin memiliki visual signature Hero Banner dengan background image resmi MagKom dan gradien biru elegan seperti pada dashboard peserta.
2. Tidak ada scrollbar horizontal yang canggung.
3. Seluruh fungsi filter periode, tombol aksi, filter antrean verifikasi, dan data kuota/pembimbing bekerja normal.
4. Rencana disetujui terlebih dahulu oleh pengguna sebelum dieksekusi.
