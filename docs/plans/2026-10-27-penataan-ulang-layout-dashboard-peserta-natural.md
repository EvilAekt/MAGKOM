# Rencana Kerja: Penataan Ulang Tata Letak & Tipografi Dashboard Peserta Menjadi Natural ala Landing Page

**Tanggal:** 27 Oktober 2026  
**Status:** Menunggu Persetujuan Pengguna (*Pending Approval*)  
**Konteks Masalah:**  
Pengguna menilai bahwa tampilan Dashboard Peserta saat ini tata letaknya masih tidak beraturan (*cluttered, misaligned, redundant*) dan belum terasa senatural Landing Page baru dari segi pemilihan font, spasi (*whitespace*), serta hirarki komponen.

---

## 1. Temuan Masalah Tata Letak Saat Ini (Root Causes)

| No | Komponen / Area | Masalah Saat Ini | Dampak Visual |
| :--- | :--- | :--- | :--- |
| 1 | **Header & Hero Bertumpuk** | Ada `<x-gentelella.page-title>` (Judul Dashboard + Deskripsi + 2 Tombol), lalu tepat di bawahnya langsung ada `.dashboard-hero` besar ("Selamat Datang, Nama!" + Bidang + Tanggal + Tombol SK). | Dua header besar bertumpuk membuat bagian atas layar sangat sesak, boros ruang, dan kaku (*redundant double-header*). |
| 2 | **Duplikasi Tombol Aksi 4 Kali** | Tombol yang sama (*Logbook*, *Tugas*, *SK*) muncul berulang di: (1) Page Title Bar, (2) Hero Banner, (3) Quick Action Bar di atas KPI, dan (4) Card Aksi Cepat di Sticky Aside. | Pengguna kebingungan melihat tombol kembar di berbagai tempat, tata letak terasa berantakan dan tidak fokus. |
| 3 | **Penyempitan Kolom Konten (Grid Bertabrakan)** | Layout utama dibagi 1fr + 340px aside. Namun di dalam 1fr tersebut dimasukkan grid bootstrap 2 kolom (`col-md-6 col-sm-12`) untuk Panel Tugas dan Panel Logbook. | Panel Tugas dan Logbook menjadi sangat sempit (masing-masing hanya ~350px), isi tabel dan judul tugas terpotong atau wrap canggung. |
| 4 | **4 Kartu Metrik KPI Terjepit** | 4 tile metrik Gentelella (`row tile_count`) ditaruh di dalam kolom 1fr yang sudah terpotong aside 340px, menggunakan sistem float/margin negatif lama. | Kartu KPI terdesak, bar progress bar mikro terlihat padat dan tidak simetris. |
| 5 | **Tipografi & Spasi Tidak Konsisten** | Masih ada sisa-sisa ukuran font mikro (11px, 12px) dan line-height tidak seragam bercampur dengan font ukuran besar. | Sulit dibaca secara natural dan tidak memiliki *visual breathing room* seperti di landing page. |

---

## 2. Solusi Desain & Arsitektur Tata Letak Natural (Ala Landing Page Baru)

### A. Alur Vertikal Terpadu (Clean Vertical Hierarchy)
Mengadopsi alur landing page yang bersih dan sistematis:
```
┌────────────────────────────────────────────────────────────────────────┐
│ 1. Unified Hero Header (Sleman Navy #1b3860, Soft, Natural)            │
│    - Sapaan Ramah: "Selamat Datang, [Nama]!"                           │
│    - Status Magang: Badge Sleman Aktif • Penempatan Bidang Diskominfo  │
│    - Periode & Hari Ini • Tombol Aksi Utama Terintegrasi Rapi          │
├────────────────────────────────────────────────────────────────────────┤
│ 2. Four-Metric KPI Bar (Full Width CSS Grid 4 Kolom)                   │
│    [ Tugas Selesai ]  [ Logbook Mingguan ]  [ Sisa Hari ]  [ Evaluasi ]│
├───────────────────────────────────────────────────────┬────────────────┤
│ 3. Kolom Konten Utama (68% Lebar - Luas & Bernapas)   │ 4. Kolom Kanan │
│    - Panel Tugas Bimbingan Mendesak                    │    (32% Lebar) │
│    - Panel Logbook Aktivitas Terbaru                  │ - Pembimbing   │
│    (Tampilan List/Tabel Bersih, Lega, Rapi)           │ - Jadwal/Info  │
└───────────────────────────────────────────────────────┴────────────────┘
```

### B. Harmonisasi Tipografi & Ukuran Font (Natural Scale)
- **Judul Utama & Heading Section**: `Lexend`, font-weight 600-700, warna Slate-900 `#0f172a`.
- **Label & Metrik Angka**: `Lexend`, font-weight 700, proporsional `1.75rem` (bukan raksasa, nyaman dipandang).
- **Body Text & Keterangan**: `Source Sans 3`, font-weight 400-500, warna Slate-600 `#475569`, line-height 1.6 yang nyaman untuk membaca dokumen/laporan.
- **Konsistensi Spasi**:
  - Padding card seragam: `20px` - `24px`.
  - Gap antar komponen: `20px`.
  - Border radius: `12px` - `16px`.
  - Border kartu: `1px solid #e2e8f0` di atas latar Slate-50 `#f8fafc`.

### C. Konsolidasi Tombol Aksi Cepat
- Menghapus tombol-tombol duplikat yang berceceran.
- Menempatkan tombol aksi secara terpadu di 2 area logis:
  1. Tombol utama di Hero Header kanan (`+ Tulis Logbook Mingguan` & `Unduh SK Diterima (PDF)`).
  2. Tombol navigasi kontekstual langsung di header panel masing-masing ("Lihat Semua Tugas &rarr;" di Panel Tugas, "Lihat Semua Logbook &rarr;" di Panel Logbook).

---

## 3. Rencana Modifikasi File

| File | Komponen | Tindakan Perbaikan |
| :--- | :--- | :--- |
| `resources/views/peserta/dashboard.blade.php` | Root Workspace Layout | 1. Menata ulang grid: KPI Bar di atas full width, lalu konten 2 kolom (Utama 68% + Aside 32%).<br>2. Menghapus page-title ganda yang bertumpuk dengan hero. |
| `resources/views/peserta/dashboard/partials/_header.blade.php` | Unified Hero Header | 1. Mengintegrasikan sapaan, bidang penempatan, tanggal, dan aksi utama ke dalam 1 kartu hero Sleman Navy yang natural dan ramping.<br>2. Menghapus redundansi double-header. |
| `resources/views/peserta/dashboard/partials/_phase_aktif.blade.php` | KPI Bar & Panel Konten | 1. Mengubah 4 tile metrik menjadi modern CSS Grid (4 kolom responsive, simetris).<br>2. Menghilangkan `quick-actions-bar` duplikat.<br>3. Menata Panel Tugas dan Panel Logbook agar lapang dan tinggi seimbang (*equal height*). |
| `resources/views/peserta/dashboard/partials/_aside.blade.php` | Sidebar Pendamping | 1. Menata profil Pembimbing Lapangan dengan layout kartu bersih dan kontak dinas yang jelas.<br>2. Menyederhanakan kartu bantuan/ketentuan agar tidak menumpuk. |

---

## 4. Langkah Pelaksanaan & Validasi

1. **Langkah 1**: Perbarui struktur tata letak di `resources/views/peserta/dashboard.blade.php` agar KPI Bar berjejer rapi di atas dan konten terbagi 2 kolom proporsional.
2. **Langkah 2**: Satukan dan rapikan `_header.blade.php` menjadi satu kesatuan hero header yang natural.
3. **Langkah 3**: Perbarui `_phase_aktif.blade.php` untuk merapikan 4 metrik KPI dan panel daftar tugas/logbook.
4. **Langkah 4**: Rapikan `_aside.blade.php` agar menjadi kolom pelengkap yang tenang dan tidak penuh tombol duplikat.
5. **Langkah 5**: Kompilasi Blade views (`php artisan view:clear` & `php artisan view:cache`) dan validasi responsivitas.

---

## 5. Konvensi Bahasa & Integritas Sistem
- Seluruh kode sumber, variabel, dan kelas CSS tetap ditulis dalam **Bahasa Inggris**.
- Penjelasan dan laporan disampaikan dalam **Bahasa Indonesia**.
- Modifikasi tetap terbatas di dalam direktori `d:\MAGKOM\MAGKOM`.
