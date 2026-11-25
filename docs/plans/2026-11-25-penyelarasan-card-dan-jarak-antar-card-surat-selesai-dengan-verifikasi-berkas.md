# Rencana Kerja: Penyelarasan Gaya Card & Jarak Antar Card Surat Selesai Magang dengan Verifikasi Berkas

**Tanggal:** 2026-11-25  
**Target Branch:** `test-2`  
**Cakupan:** Halaman Daftar Surat Selesai Magang (`resources/css/pages/admin/surat-selesai-index.css` dan perbaikan atribut `subText` KPI di `resources/views/admin/surat-selesai/index.blade.php`)

---

## 1. Tujuan
Menyamakan gaya visual antar card (radius, border, padding, warna badge tab aktif) serta jarak vertikal antar section/card pada halaman **Surat Selesai Magang** agar identik dan proporsional dengan halaman **Verifikasi Berkas Pendaftaran Peserta**, sekaligus memperbaiki tampilan teks `&amp;` pada subtext KPI card (`:subText`).

---

## 2. Asumsi & Pertanyaan Terbuka
- **Asumsi Kerja Logis:**
  1. Acuan desain yang digunakan adalah `resources/css/pages/admin/pendaftaran-index.css` (`.adm-hero-banner`, `.adm-tile-count-grid`, `.adm-category-nav-bar`, `.adm-filter-card`) sesuai dua tangkapan layar perbandingan yang dilampirkan.
  2. Perubahan difokuskan pada spacing, border, radius, padding, dan active-badge styling di `resources/css/pages/admin/surat-selesai-index.css`, plus perbaikan `:subText` pada `<x-gentelella.tile-stat>` di `resources/views/admin/surat-selesai/index.blade.php` agar tanda `&` tidak ter-render sebagai `&amp;`.
  3. **Persetujuan Perubahan Blade:** Sesuai aturan proyek (`.agents/rules/AGENTS.md` bagian 3.3), perubahan atribut `:subText` di `resources/views/admin/surat-selesai/index.blade.php` memerlukan persetujuan eksplisit pengguna sebelum eksekusi.

---

## 3. Pendekatan Teknis

### Perbandingan Opsi Implementasi
1. **Opsi A (Direkomendasikan): Menyamakan Token Spacing & Gaya Card di `surat-selesai-index.css`**
   - Menyamakan jarak vertikal (`margin-bottom: 1.5rem`) pada `.adm-hero-banner`, `.adm-tile-count-grid`, `.adm-category-nav-wrap`, dan `.surat-filter-card`.
   - Menyamakan gaya card `.adm-hero-banner` (`border-radius: 16px; padding: 24px 30px;`), `.surat-filter-card` (`border: 1.5px solid #CBD5E1; border-radius: 14px; padding: 1.25rem 1.5rem;`), dan `.surat-panel-container` (`border: 1.5px solid #CBD5E1; border-radius: 14px;`).
   - Menambahkan warna `.adm-category-nav-badge` saat tab aktif (`.active`, `.active-individu`, `.active-kelompok`) sesuai `pendaftaran-index.css`.
   - Mengubah `subText="{{ __('...') }}"` menjadi `:subText="__('...')"` pada `<x-gentelella.tile-stat>` di `index.blade.php` agar karakter `&` tidak di-escape dua kali.
   - **Kelebihan:** Konsistensi 1:1 dengan Verifikasi Berkas, perubahan CSS terisolasi di `surat-selesai-index.css`, dan memperbaiki `&amp;` pada KPI card.
   - **Kekurangan:** Tidak ada.

2. **Opsi B: Mengimpor `pendaftaran-index.css` ke `surat-selesai/index.blade.php`**
   - **Kelebihan:** Tidak perlu menulis rule `.adm-tile-count-grid` di dua file CSS.
   - **Kekurangan:** Berisiko membawa rule spesifik halaman pendaftaran yang tidak relevan dan berpotensi bentrok dengan layout Surat Selesai.

**Keputusan:** Memilih **Opsi A** agar CSS halaman Surat Selesai tetap terisolasi dan aman.

---

## 4. Perubahan File

| File | Aksi (`Create`/`Modify`/`Delete`) | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/css/pages/admin/surat-selesai-index.css` | Modify | Menyamakan jarak vertikal (`1.5rem`) antar banner, KPI grid, category bar, filter card, dan panel daftar; menyamakan border (`#CBD5E1`), border-radius (`14px` / `16px`), padding filter card (`1.25rem 1.5rem`), serta warna badge tab aktif dengan `pendaftaran-index.css`. |
| `resources/views/admin/surat-selesai/index.blade.php` | Modify | Mengubah `subText="{{ __('...') }}"` menjadi `:subText="__('...')"` pada komponen `<x-gentelella.tile-stat>` agar tidak terjadi double-escaping `&amp;`. |

---

## 5. Langkah Implementasi

- [x] **Persetujuan Perubahan Blade**: Memastikan persetujuan eksplisit user untuk mengubah atribut `subText` menjadi `:subText` di `resources/views/admin/surat-selesai/index.blade.php`.
- [x] **Update `resources/css/pages/admin/surat-selesai-index.css`**:
  - Sesuaikan `.adm-hero-banner` (`border-radius: 16px; padding: 24px 30px; margin-bottom: 1.5rem;`).
  - Sesuaikan `.adm-tile-count-grid` (`margin: 0 0 1.5rem 0 !important;`).
  - Sesuaikan `.adm-category-nav-wrap` (`margin-bottom: 1.5rem;`) dan tambahkan rule `.adm-category-nav-item.active* .adm-category-nav-badge`.
  - Sesuaikan `.surat-filter-card` (`border: 1.5px solid #CBD5E1; border-radius: 14px; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem;`) dan `.surat-panel-container` (`border: 1.5px solid #CBD5E1; border-radius: 14px;`).
- [x] **Update `resources/views/admin/surat-selesai/index.blade.php`**:
  - Ubah `subText="{{ __('Individu & Rombongan Kelompok') }}"` dan `subText="{{ __('Akumulasi individu & anggota tim') }}"` menjadi `:subText="__('...')"`.
- [x] **Quality Gate & Verifikasi**:
  - Jalankan `npm run build` dan `php artisan test --filter=AdminSuratSelesai`.

---

## 6. Strategi Testing
- **Asset Build:** `npm run build` berhasil tanpa error.
- **Feature Test (`php artisan test --filter=AdminSuratSelesai`):** Seluruh 20 test lulus dan tidak ada teks `&amp;` literal pada output HTML KPI card.

---

## 7. Risiko & Mitigasi
- **Risiko:** Perubahan padding/radius di `surat-selesai-index.css` memengaruhi mode fokus.  
  *Mitigasi:* Hanya mengubah `.adm-hero-banner`, `.adm-tile-count-grid`, `.adm-category-nav-*`, `.surat-filter-card`, dan `.surat-panel-container` yang berada di mode daftar utama Tier 1.

---

## 8. Kriteria Selesai (Definition of Done)
- [x] Jarak antar card dan gaya border/radius/padding di halaman Surat Selesai Magang selaras dengan Verifikasi Berkas.
- [x] Warna badge tab aktif pada `.adm-category-nav-bar` selaras dengan Verifikasi Berkas.
- [x] Subtext KPI card tampil normal (`&`, bukan `&amp;`).
- [x] `npm run build` dan `php artisan test --filter=AdminSuratSelesai` lulus seluruhnya.
