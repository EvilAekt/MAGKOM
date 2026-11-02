# Rencana Kerja: Pembuatan Card Background & Container Panel untuk Kuota per Bidang

**Tanggal Dokumen**: 2026-11-02  
**Modul**: Administrasi MagKom — Manajemen Bulan & Kuota Magang (`admin/kuota`)  
**Status**: ✅ Selesai Diimplementasikan & Diverifikasi  

---

## 1. Tujuan
Menyelaraskan tampilan section **"Kuota per bidang"** pada halaman Manajemen Kuota Magang (`resources/views/admin/kuota/index.blade.php`) agar dibungkus dalam container card background terpadu (`<x-gentelella.x-panel>`) yang memiliki header card resmi (title *"Kuota per bidang"*, subtitle *"Tentukan jumlah kuota, jurusan, jenjang, dan rentang durasi magang."*, slot aksi tombol *"+ Tambah kuota"*, dan tombol collapsible), border rounded, drop shadow halus, serta framing tabel dan callout validasi di dalamnya—seperti referensi kartu *"Daftar Permohonan Magang Masuk"* (`resources/views/admin/pendaftaran/index.blade.php`) pada screenshot referensi.

---

## 2. Perbandingan Visual (Sebelum vs Referensi)

````carousel
![Kondisi Saat Ini (Floating / Tanpa Card Panel)](C:/Users/ALDI%20BOYS/.gemini/antigravity/brain/8ac85b61-3384-4008-bb69-7f98f2cbd22d/kuota-card-sebelum.png)
<!-- slide -->
![Target Desain Referensi (Pendaftaran x-panel Card Background)](C:/Users/ALDI%20BOYS/.gemini/antigravity/brain/8ac85b61-3384-4008-bb69-7f98f2cbd22d/kuota-card-referensi.png)
````

### Analisis Kesenjangan Desain:
1. **Header & Container**:
   - *Saat Ini*: Section title "Kuota per bidang" dan tombol "+ Tambah kuota" berada di luar kotak (`<div class="section-header-row">`) langsung di atas canvas latar belakang halaman tanpa card background.
   - *Referensi*: Menggunakan container putih terpadu `<x-gentelella.x-panel>` dengan aksen garis biru atas 3.5px (`#01347C`), header berlatar putih dengan title, subtitle, slot tombol aksi di kanan atas, serta tombol collapse `^`.
2. **Keterpaduan Komponen**:
   - *Saat Ini*: Tabel memiliki border sendiri (`.card-table-wrap`), tetapi judul di atasnya dan alert di bawahnya melayang terpisah.
   - *Referensi*: Seluruh elemen (Header, Tabel, dan Alert Validasi) menjadi satu kesatuan (*cohesive card*) di dalam `.x_content`.

---

## 3. Asumsi & Evaluasi Pendekatan Teknis

### Asumsi Kerja:
1. Desain card standar yang digunakan sistem MAGKOM adalah komponen Blade reusable `<x-gentelella.x-panel>`.
2. Tombol `+ Tambah kuota` ditempatkan pada `<x-slot:actions>` di bagian header panel agar konsisten dengan pola tombol kelola di seluruh dashboard dan x-panel lainnya.
3. Tabel Kuota dan Callout Validasi Pembimbing diletakkan di dalam `.x_content` dari panel tersebut.

### Evaluasi Opsi Arsitektur:

| Kriteria | Opsi A: Menggunakan `<x-gentelella.x-panel>` (Direkomendasikan) | Opsi B: Custom `div.card-bg` Manual |
| :--- | :--- | :--- |
| **Konsistensi UI/UX** | **Tinggi (100% Paritas)**: Identik dengan Pendaftaran, Penempatan, Waiting List, dan Dashboard. | **Rendah**: Membutuhkan CSS kustom baru yang rawan desinkronisasi. |
| **Dukungan Dark Mode** | **Otomatis**: Sudah terintegrasi dengan variabel tema `[data-theme="dark"]` di `gentelella.css`. | **Manual**: Harus menulis ulang aturan warna dan kontras dark mode. |
| **Aksesibilitas & Fitur** | Lengkap: Memiliki role `region`, aria-label, dan interaksi collapse/expand teruji. | Terbatas: Hanya kotak statis tanpa integrasi collapse script. |
| **Kepatuhan Aturan Proyek** | Sangat Baik: Mematuhi prinsip DRY dan standardisasi design system MAGKOM. | Buruk: Menambah fragmentasi class CSS baru. |

> **Rekomendasi**: Menggunakan **Opsi A** (`<x-gentelella.x-panel>`).

---

## 4. Permohonan Izin Perubahan Markup Blade (Sesuai Aturan AGENTS.md Bagian 3.3)

> [!IMPORTANT]
> **Alasan Markup Baru Diperlukan**:  
> Perubahan visual dari elemen terpisah (floating title, button, table) menjadi satu kesatuan kartu berlatar belakang putih (*card bg*) dengan header terstruktur dan tombol collapse tidak dapat diselesaikan hanya melalui CSS saja. Perlu pembungkusan semantik ke dalam komponen `<x-gentelella.x-panel>` yang memiliki slot `<x-slot:actions>` dan container `.x_content`.

- **File yang akan diubah**: `resources/views/admin/kuota/index.blade.php`
- **Bagian**: Baris 1008 hingga 1120 (Section 4 "Kuota per bidang" dan Section 5 "Alert Validasi Pembimbing").
- **Tindakan**:
  - Membungkus seluruh section tersebut dengan `<x-gentelella.x-panel title="Kuota per bidang" subtitle="Tentukan jumlah kuota, jurusan, jenjang, dan rentang durasi magang.">`.
  - Memasukkan tombol `+ Tambah kuota` ke dalam `<x-slot:actions>`.
  - Memasukkan container tabel (`.card-table-wrap`) dan `<x-gentelella.callout>` ke dalam body panel (`.x_content`).

---

## 5. Rencana Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/admin/kuota/index.blade.php` | Modify | Membungkus Section 4 & 5 dengan `<x-gentelella.x-panel>`, memasang `<x-slot:actions>` untuk tombol tambah kuota, dan membersihkan wrapper non-semantik lama. |
| `resources/css/pages/admin/dashboard.css` | Modify | Penyesuaian padding/margin mikro antara tabel dan callout validasi di dalam `.x_content` agar simetris dan rapi. |
| `tests/Feature/AdminKuotaRevampTest.php` | Modify | Menambahkan assertion untuk memvalidasi elemen `x_panel` ter-render dengan benar pada halaman Kuota. |

---

## 6. Langkah-Langkah Implementasi Bertahap

- [x] **Fase 1: Konfirmasi & Persetujuan Pengguna**
  - [x] Memperoleh persetujuan resmi dari pengguna untuk rencana kerja dan perubahan markup Blade terkait.
- [x] **Fase 2: Refactoring Markup Blade (`admin/kuota/index.blade.php`)**
  - [x] Ganti `<div class="section-header-row">` dan wrapper luar dengan `<x-gentelella.x-panel title="Kuota per bidang" subtitle="Tentukan jumlah kuota, jurusan, jenjang, dan rentang durasi magang.">`.
  - [x] Pasang `<x-slot:actions>` yang memuat tombol `+ Tambah kuota` (`.btn-primary-action`) ketika `$selectedBulan` tersedia.
  - [x] Tempatkan `.card-table-wrap` dan `<x-gentelella.callout>` di dalam slot default panel.
- [x] **Fase 3: Penyelarasan CSS & Dark Mode**
  - [x] Pastikan padding `.card-table-wrap` menyatu lembut (*seamless*) dengan interior `.x_panel`.
  - [x] Verifikasi tampilan kontras dan warna pada mode terang (`#FFFFFF`) dan mode gelap (`#182234`).
- [x] **Fase 4: Asset Compilation & Quality Gate**
  - [x] Jalankan `npm run build` untuk mengompilasi asset Vite.
  - [x] Jalankan `php vendor/bin/pint --dirty --test` untuk memastikan kebersihan format kode.
  - [x] Jalankan pengujian PHPUnit `tests/Feature/AdminKuotaRevampTest.php` (memastikan 12 assertions lolos 100%).
- [x] **Fase 5: Verifikasi Visual Browser (Playwright MCP)**
  - [x] Buka browser pada URL `http://magkom.test/admin/kuota`.
  - [x] Ambil tangkapan layar untuk memastikan visual card background, header, tombol tambah kuota, dan tabel sudah persis seperti contoh referensi Screenshot 2.

---

## 7. Strategi Testing

1. **Automated Testing (PHPUnit)**:
   - `tests/Feature/AdminKuotaRevampTest.php`:
     - `test_kuota_page_displays_quota_per_bidang_table()` ➔ Memastikan teks header kolom dan data kuota tetap tampil utuh.
     - `test_kuota_page_displays_mentor_capacity_validation_banner()` ➔ Memastikan banner validasi kapasitas tetap ada di DOM.
     - Tambahan assertion: Memastikan class `x_panel` dan struktur header panel terdeteksi.
2. **Quality Gate**:
   - `php vendor/bin/pint --dirty --test` (0 issues).
   - `npm run build` (build sukses tanpa error).
3. **Manual / Visual Testing**:
   - Verifikasi melalui Playwright pada resolusi desktop (1280px dan 1920px) serta tema terang dan gelap.

---

## 8. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
| :--- | :--- | :--- |
| Interaksi JavaScript modal terputus karena relokasi tombol | Sedang | Tombol tetap menggunakan fungsi `onclick="openTambahKuotaModal()"` bawaan tanpa mengubah ID atau event handling apa pun. |
| Konflik margin ganda antara `.x_panel` dan `.card-table-wrap` | Rendah | Mengatur margin top `.card-table-wrap` di dalam `.x_content` agar selaras dan proporsional (margin: 0 atau gap terukur). |

---

## 9. Kriteria Selesai (Definition of Done)

- [x] Section "Kuota per bidang" terbungkus dalam satu kartu background putih terpadu dengan border-top biru khas MAGKOM.
- [x] Terdapat header card yang memuat judul, subtitle, tombol `+ Tambah kuota` di kanan atas, dan icon collapse `^`.
- [x] Tabel kuota dan callout validasi berada di dalam card dengan layout rapi dan bersih.
- [x] Tampilan paritas 1:1 dengan kartu pada screenshot referensi (Pendaftaran).
- [x] Semua automated tests hijau (`Tests: 12 passed`).
- [x] Working tree git bersih dan siap diuji.

