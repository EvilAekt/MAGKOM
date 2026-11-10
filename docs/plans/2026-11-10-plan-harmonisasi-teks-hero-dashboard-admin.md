# Rencana Kerja: Harmonisasi Teks Informasi Hero Banner Dashboard Admin

**Tanggal:** 10 November 2026  
**Status:** Selesai (Implemented)  
**Tautan Dokumen Rencana:** `docs/plans/2026-11-10-plan-harmonisasi-teks-hero-dashboard-admin.md`  
**Referensi:**  
- Kondisi Eksisting Hero: `media_1794291358917_8473ce28.jpg` (SS 1)  
- Acuan Teks Informasi: `media_1794291391166_0123b133.png` (SS 2)  
- Aturan Pengembangan: `AGENTS.md` (PHP 8.3, Laravel 10, Vanilla CSS di `resources/css/`, Vanilla JS di `resources/js/`, Nol inline CSS/JS di Blade, Zero Test Regression).

---

## 1. Analisis Kebutuhan & Spesifikasi Desain

### Perubahan yang Diminta User:
1. **Penyelarasan Teks Informasi Hero Banner**:
   Mengganti teks saat ini (SS 1) dengan teks informasi dari SS 2 dengan tetap mempertahankan font eksisting (*Plus Jakarta Sans*):
   - **Eyebrow / Tanggal**: Ganti `DISKOMINFO SLEMAN` menjadi format penanggalan dinamis Bahasa Indonesia huruf kapital:
     `{{ mb_strtoupper(\Carbon\Carbon::now()->translatedFormat('l, d F Y')) }}` (Contoh: `SELASA, 10 NOVEMBER 2026`).
   - **Judul Utama**: Ganti `Kendalikan verifikasi dan kuota magang` menjadi:
     `Dashboard Operasional Administrasi` (Font *Plus Jakarta Sans*, weight 800, ukuran 28px, warna putih `#FFFFFF`).
   - **Deskripsi / Subtitle**: Menambahkan baris keterangan di bawah judul:
     `Pusat kendali verifikasi berkas pendaftaran, pengawasan SLA, dan alokasi kuota kejuruan dinas.` (Font *Plus Jakarta Sans*, weight 500, warna putih semi-transparan `rgba(255, 255, 255, 0.85)`).
   - **Pill Badge Status Bulan**: Menambahkan badge aktif di bawah deskripsi:
     `• Bulan: {{ $activeMonthName }} (Aktif)` dengan indikator titik hijau (*emerald/mint dot*), mempertegas periode kerja dinas yang sedang berlangsung.
2. **Penghapusan Tombol**:
   - Menghapus tombol kuning `Tinjau Berkas` dan tombol outline `Ekspor Laporan` dari visual hero banner sesuai permintaan eksplisit user.
3. **Konsistensi Visual & Grafis**:
   - Mempertahankan 4 batang dekoratif vertikal di sisi kanan banner (`#3F51B5`, `#7B8CC9`, `#FFC300`, `#C5CDE6`) agar identitas visual MagKom tetap mewah, elegan, dan seimbang dengan layout Bento Grid.

---

## 2. Rencana Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/admin/dashboard.blade.php` | Modify | Menata ulang bagian `<section class="adm-hero-banner">` dengan teks tanggal dinamis, judul `Dashboard Operasional Administrasi`, deskripsi pengawasan SLA/alokasi kuota, pill badge periode aktif, dan menghapus kedua tombol aksi (`Tinjau Berkas` & `Ekspor Laporan`). |
| `resources/css/pages/admin/dashboard.css` | Modify | Menyesuaikan styling hero banner: styling subtitle deskripsi, styling pill badge bulan aktif (`.adm-hero-badge` dengan dot hijau), serta membersihkan rule tombol CTA yang dihapus. |

---

## 3. Langkah Implementasi (Checklist)

- [x] **Langkah 1**: Edit `resources/views/admin/dashboard.blade.php`:
  - Ubah eyebrow menjadi tanggal kapital dinamis Bahasa Indonesia.
  - Ubah judul menjadi `Dashboard Operasional Administrasi`.
  - Tambahkan paragraf deskripsi pengawasan SLA dan kuota kejuruan.
  - Tambahkan pill badge bulan aktif dengan status dot hijau.
  - Hapus tombol aksi `Tinjau Berkas` dan `Ekspor Laporan`.
- [x] **Langkah 2**: Edit `resources/css/pages/admin/dashboard.css`:
  - Tambahkan styling `.adm-hero-desc` (font-size 13.5px, color `rgba(255,255,255,0.85)`, max-width 620px, line-height 1.55, margin-bottom 14px).
  - Tambahkan styling `.adm-hero-status-pill` (padding 6px 14px, background `rgba(16, 185, 129, 0.12)`, border `1px solid rgba(16, 185, 129, 0.35)`, border-radius 9999px, font-size 12px, font-weight 700, color `#34D399`).
  - Tambahkan styling `.adm-status-dot-green` (width 7px, height 7px, border-radius 50%, background `#10B981`, box-shadow `0 0 8px rgba(16, 185, 129, 0.7)`).
- [x] **Langkah 3**: Kompilasi asset:
  - `npm run build` (Sukses dalam 2.77s).
- [x] **Langkah 4**: Jalankan pemeriksaan kode:
  - `vendor/bin/pint --test app/Services/AdminDashboardMetricsService.php` (Passed).
- [x] **Langkah 5**: Jalankan pengujian otomatis PHPUnit:
  - Seluruh 41 skenario pengujian dashboard lulus 100%.

---

## 4. Strategi Pengujian (Testing Strategy)

1. **Pengujian Tampilan & Typography**:
   - Memastikan teks tanggal dirender dalam format Bahasa Indonesia kapital (misal `SELASA, 10 NOVEMBER 2026`).
   - Memastikan judul `Dashboard Operasional Administrasi` dirender tebal menggunakan font *Plus Jakarta Sans*.
   - Memastikan teks deskripsi tampil elegan dan kontras di atas gradien navy.
   - Memastikan pill status `Bulan: November 2026 (Aktif)` memiliki dot hijau yang presisi.
   - Memastikan tombol `Tinjau Berkas` dan `Ekspor Laporan` tidak lagi muncul di banner.
2. **Automated Feature Tests (PHPUnit)**:
   - Memastikan seluruh 41 skenario pengujian pada 6 test suites dashboard tetap 100% PASS (Hijau).
   - Memastikan assertion `assertSee('Dashboard Operasional Administrasi')` dan `assertSee('Bulan:')` terpenuhi secara alami dari konten visual.

---

## 5. Kriteria Selesai (Definition of Done)

- [x] Teks informasi pada Hero Banner 100% selaras dengan SS 2 (Tanggal hari ini, Judul Operasional, Deskripsi SLA/Kuota, Pill Bulan Aktif).
- [x] Font tetap konsisten menggunakan *Plus Jakarta Sans*.
- [x] Tombol `Tinjau Berkas` dan `Ekspor Laporan` terhapus dari Hero Banner.
- [x] 4 batang vertikal dekoratif di sisi kanan tetap tampil seimbang dan proporsional.
- [x] `npm run build` sukses.
- [x] `vendor/bin/pint --test` lolos.
- [x] Seluruh pengujian PHPUnit lulus 100%.
