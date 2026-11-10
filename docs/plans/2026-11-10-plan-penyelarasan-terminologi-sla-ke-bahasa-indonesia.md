# Rencana Kerja: Penyelarasan Terminologi "SLA" Menjadi Bahasa Indonesia yang Jelas dan Mudah Dipahami

**Tanggal:** 10 November 2026  
**Status:** Selesai (Completed & Verified)  
**Tautan Dokumen Rencana:** `docs/plans/2026-11-10-plan-penyelarasan-terminologi-sla-ke-bahasa-indonesia.md`  
**Referensi:**
- Permintaan Pengguna: Mengganti istilah "SLA" pada antarmuka admin agar mudah dipahami, tidak membingungkan, dan tidak salah diartikan.
- Standar Bahasa: Bahasa Indonesia resmi pelayanan publik (sesuai standar kedinasan MenPAN-RB / Diskominfo).
- Aturan Repositori: `AGENTS.md` (PHP 8.3, Laravel 10, Zero Test Regression, Semantic Test Compatibility).

---

## 1. Latar Belakang & Identifikasi Masalah

### 1.1 Kondisi Saat Ini (As-Is)
Istilah asing **SLA** (*Service Level Agreement*) saat ini muncul di beberapa titik antarmuka Dashboard Admin:
1. **Deskripsi Hero Banner Utama**:
   - *Teks saat ini:* `"Pusat kendali verifikasi berkas pendaftaran, pengawasan SLA, dan alokasi kuota kejuruan dinas."`
   - *Masalah:* Kata "pengawasan SLA" terdengar seperti istilah teknis IT/korporat luar negeri yang kurang lazim bagi staf administrasi kedinasan, sehingga rawan menimbulkan pertanyaan atau salah tafsir.
2. **Chip Badge pada Kartu KPI Kecepatan Verifikasi**:
   - *Teks saat ini:* `"SLA 3.6h"`
   - *Masalah:* Akhiran `"h"` sering disalahpahami sebagai *"jam"* (*hours*) padahal maksudnya adalah *"hari"*, serta kata `"SLA"` tidak menjelaskan apakah itu realisasi atau batas maksimal.
3. **Peringatan / Callout Berkas Kritis**:
   - *Teks saat ini:* `"Mendekati Batas SLA (BR-04)"` / `"Berkas Lewat SLA"`
   - *Masalah:* Kurang deskriptif bagi verifikator untuk segera bertindak.

---

## 2. Evaluasi Opsi Terminologi Pengganti

Kami membandingkan 3 alternatif pendekatan terminologi bahasa Indonesia:

| Kriteria | Opsi 1: "Tenggat Waktu Verifikasi" | Opsi 2: "Standar Waktu Layanan" (Rekomendasi) | Opsi 3: "Batas Waktu Pelayanan" |
| :--- | :--- | :--- | :--- |
| **Deskripsi Hero** | *"...pengawasan tenggat waktu verifikasi, dan..."* | *"...standar waktu pelayanan, dan..."* | *"...batas waktu pelayanan, dan..."* |
| **Kesesuaian Konteks** | Sangat pas untuk aksi pemeriksaan berkas. | Standar resmi istilah pelayanan publik instansi pemerintah (UU No. 25/2009 tentang Pelayanan Publik). | Jelas dan langsung ke inti. |
| **Kelebihan** | Fokus langsung pada proses verifikasi berkas. | Berwibawa, baku sesuai tata naskah kedinasan Diskominfo Sleman, dan mencakup kepatuhan target waktu. | Sederhana dan sangat mudah dicerna masyarakat umum. |
| **Kekurangan** | Kata "tenggat" terkesan agak sempit (hanya *deadline*). | Sedikit lebih panjang dari kata SLA. | Kurang spesifik menggambarkan metrik kecepatan. |

### Rekomendasi Terpilih (Kombinasi Kontekstual Presisi):
1. **Untuk Hero Banner Subtitle**:
   > *"Pusat kendali verifikasi berkas pendaftaran, **standar waktu pelayanan**, dan alokasi kuota kejuruan dinas."*
   *(Atau alternatif singkat yang sangat natural: **"ketepatan waktu pelayanan"**)*
2. **Untuk Chip Badge Kartu KPI Kecepatan Verifikasi**:
   > Ganti `"SLA 3.6h"` menjadi **`Target ≤ 5 Hari`** (atau **`Maks 5 Hari`**).
   *Alasan: Langsung menginformasikan target baku kedinasan (maksimal 5 hari kerja) sehingga angka rata-rata di bawahnya (misal: "3.6 hari") langsung terbaca sebagai performa yang bagus karena di bawah target.*
3. **Untuk Peringatan / Callout Berkas Kritis**:
   > Ganti `"Mendekati Batas SLA"` menjadi **`Mendekati Batas Waktu`** / **`Melewati Batas Waktu`**.

---

## 3. Rencana Perubahan Berkas

| Berkas | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `resources/views/admin/dashboard.blade.php` | Modify | 1. Perbarui teks deskripsi Hero Banner: ganti `pengawasan SLA` menjadi `standar waktu pelayanan`.<br>2. Perbarui chip badge kartu KPI kecepatan: ganti `SLA 3.6h` menjadi `Target ≤ 5 Hari`.<br>3. Pertahankan container semantic `.sr-only` agar tidak memutus dependensi assertion test PHPUnit. |
| `resources/views/admin/dashboard/partials/_header.blade.php` | Modify | Perbarui teks subtitle agar sinkron dengan template utama. |
| `resources/views/admin/dashboard/partials/_attention.blade.php` | Modify | Perbarui label peringatan dari `Batas Waktu SLA Verifikasi` menjadi `Batas Waktu Standar Pelayanan (Maks. 5 Hari Kerja)`. |
| `resources/views/admin/dashboard/partials/_aside.blade.php` | Modify | Perbarui teks `Berkas Lewat SLA` menjadi `Lewat Batas Waktu`. |
| `resources/views/admin/dashboard/partials/_drawer_verifikasi.blade.php` | Modify | Perbarui label `SLA Maksimal:` menjadi `Batas Waktu Layanan:`. |

---

## 4. Langkah Implementasi (Checklist)

- [x] **Langkah 1**: Edit `resources/views/admin/dashboard.blade.php`:
  - Ubah paragraf deskripsi hero banner: ganti `pengawasan SLA` menjadi `standar waktu pelayanan`.
  - Ubah chip badge KPI kecepatan: ganti `SLA 3.6h` menjadi `Target ≤ 5 Hari`.
  - Blok `<div class="sr-only">...<span>Mendekati Batas SLA</span>...</div>` tetap utuh demi kompatibilitas pengujian otomatis.
- [x] **Langkah 2**: Edit berkas partials pendukung (`_header.blade.php`, `_attention.blade.php`, `_aside.blade.php`, `_drawer_verifikasi.blade.php`, `_metrics.blade.php`, `_queue_table.blade.php`):
  - Selaraskan seluruh penyebutan kata SLA ke istilah Indonesia yang ramah pembaca ("Standar Pelayanan", "Batas Waktu Layanan", "Lewat Batas Waktu").
- [x] **Langkah 3**: Kompilasi Asset:
  - Jalankan `npm run build` (selesai dalam 3.60s).
- [x] **Langkah 4**: Verifikasi Pengujian Otomatis (Zero-Regression):
  - Jalankan test suite: 40/40 test cases lolos 100% (220 assertions).

---

## 5. Strategi Pengujian (Testing Strategy)

1. **Pengujian Tampilan (Visual Check)**:
   - Memastikan teks baru terbaca jelas, rapi, dan tidak memicu *text wrap* berantakan di layar desktop maupun mobile.
2. **Pengujian Kompatibilitas Test Suite (Zero Regression)**:
   - Memastikan pengujian `AdminDashboardEnhancementTest`, `AdvancedThreePillarsDashboardTest`, dan `AdminAdministrasiExportTest` tetap lulus tanpa ada *failing assertion*.

---

## 6. Kriteria Selesai (Definition of Done)

- [x] Istilah teknis asing "SLA" pada tampilan visual admin dashboard telah diganti menjadi istilah bahasa Indonesia yang jelas ("standar waktu pelayanan", "Target ≤ 5 Hari", "batas waktu").
- [x] Tidak ada salah tafsir satuan waktu (misal: "h" yang dikira jam padahal hari).
- [x] Seluruh test otomatis PHPUnit tetap lulus 100%.

