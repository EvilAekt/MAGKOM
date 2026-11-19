# Rencana Kerja: Penambahan Input Persyaratan Umum & Dokumen Pendaftaran Hero Banner di Canvas Editor

**Tanggal**: 20 November 2026  
**Status**: Selesai Diimplementasikan (Completed)  
**Dokumen Terkait**: `docs/01-PRD-MagKom.md`, `docs/07-DESIGN-SYSTEM-MAGKOM.md`, `.agents/rules/AGENTS.md`  
**Area Terdampak**: SuperAdmin Site Editor, Hero Block Renderer, Canvas Bridge & Runtime, Landing CMS Staging Service.

---

## 1. Tujuan
Memungkinkan SuperAdmin untuk mengubah judul label dan deskripsi/butir-butir pada:
1. **Kolom 2 Hero: Persyaratan Umum** (`landing_label_persyaratan` dan `landing_persyaratan_umum`).
2. **Kolom 3 Hero: Dokumen Pendaftaran** (`landing_label_dokumen` dan `landing_dokumen_pendaftaran`).
langsung dari panel **Inspector (Tab General > Konten Hero Banner)** di Canvas Editor (`/superadmin/site-editor`), dengan pembaruan pratinjau reaktif di kanvas iframe (*live WYSIWYG*) dan persistensi penuh saat disimpan sebagai draf maupun diterbitkan ke publik.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja:
1. **Format Input Butir**: Butir persyaratan dan berkas dokumen dituliskan dalam bentuk baris baru (satu baris untuk setiap butir poin) di dalam `textarea` Inspector, kemudian dirender otomatis sebagai daftar `<ul><li>...</li></ul>` di kanvas dan halaman publik landing page.
2. **Penyelarasan Desain (Figma & Desain Bawaan)**: Judul kolom dipecah otomatis pada spasi pertama untuk memberi aksen warna ungu (`text-accent`) pada kata kedua, sesuai format eksisting (contoh: "Persyaratan `<br><span class="text-accent">Umum</span>`").
3. **Kompatibilitas Layar**: Perubahan teks ini akan berdampak konsisten pada tampilan Desktop (Slide 1 kolom 2 & 3) dan tampilan Mobile (Slide 2 & Slide 3).

### Pertanyaan Terbuka:
- *Tidak ada ambiguitas kritis.* Seluruh skema database, aturan validasi FormRequest, dan adapter legacy telah tersedia di backend, sehingga integrasi murni melengkapi UI inspector dan pipa reaktivitas kanvas.

---

## 3. Pendekatan Teknis & Evaluasi Arsitektur

### Perbandingan Opsi:
- **Opsi A (Direkomendasikan) — Integrasi Terpadu pada Accordion Konten Hero Banner**:
  Menambahkan kontrol input judul teks dan textarea multi-baris di dalam grup formulir Hero Banner pada `_inspector.blade.php`. Mengaitkan field ke `data-field` yang sudah divalidasi oleh `SaveLandingSiteEditorRequest`, serta menghubungkan reaktivitas kanvas via `WP_SET_TEXT` di `landing-canvas-runtime.js`.
  - *Kelebihan*: Sangat intuitif bagi admin, menjaga tata letak 3-kolom kartu hero di Desktop dan kartu detail di Mobile tetap solid tanpa memecah arsitektur blok, data tersinkronisasi dua arah (*flat keys* & *nested document JSON*).
  - *Kekurangan*: Membutuhkan penambahan markup kontrol input di Blade Inspector dan penambahan atribut `data-wp-editable` pada template hero.
- **Opsi B — Memisahkan Persyaratan & Dokumen Menjadi Blok Independen**:
  Membuat blok terpisah di luar Hero Banner agar bisa digeser-geser.
  - *Kelemahan*: Merusak komposisi visual Hero Banner Sleman (3 kolom dalam satu box pendaftaran) dan membuat tampilan mobile terpecah tidak konsisten dengan arahan screenshot pengguna.

**Keputusan**: Dipilih **Opsi A** demi konsistensi visual dan arsitektur yang stabil.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/superadmin/site-editor/partials/_inspector.blade.php` | Modify | Menambahkan field input teks dan textarea untuk Persyaratan Umum dan Dokumen Pendaftaran di bawah accordion Konten Hero Banner. |
| `resources/views/public/blocks/hero.blade.php` | Modify | Menambahkan atribut `data-wp-editable` pada heading dan daftar `<ul>` kolom 2 & 3 hero untuk reaktivitas kanvas. |
| `resources/js/pages/superadmin/site-editor/inspector.js` | Modify | Mendaftarkan pemetaan `landing_label_persyaratan`, `landing_persyaratan_umum`, `landing_label_dokumen`, dan `landing_dokumen_pendaftaran` ke blok `hero` dalam `syncFieldToBlockContent`. |
| `resources/js/pages/public/landing-canvas-runtime.js` | Modify | Memperluas penanganan pesan `WP_SET_TEXT` agar mampu merender pembaruan daftar `<ul><li>` dan format aksen judul `.hero-subhead` secara instan. |
| `app/Services/LandingCmsStagingService.php` | Modify | Menambahkan sinkronisasi flat keys persyaratan & dokumen ke dalam `content` blok hero pada `saveSiteEditorDraft()` dan `publishDraft()`. |
| `tests/Feature/SuperAdmin/LandingCmsSiteEditorTest.php` | Modify | Menambahkan pengujian pengubahan persyaratan umum & dokumen pendaftaran dari site editor sampai terbit live. |

---

## 5. Langkah Implementasi Terstruktur

### Tahap 1: Backend Staging Service Synchronization
- [x] Buka `app/Services/LandingCmsStagingService.php` pada method `saveSiteEditorDraft()`.
- [x] Tambahkan sinkronisasi `$b['content']['label_persyaratan']`, `$b['content']['persyaratan_umum']`, `$b['content']['label_dokumen']`, dan `$b['content']['dokumen_pendaftaran']` saat tipe blok adalah `hero`.
- [x] Pastikan pada method `publishDraft()` dokumen live `landing_page_document` ikut memperbarui konten blok hero jika kunci-kunci live tersebut diperbarui.

### Tahap 2: Penambahan Kontrol Form di Inspector (Markup Blade)
- [x] Buka `resources/views/superadmin/site-editor/partials/_inspector.blade.php` pada section `block-form-hero`.
- [x] Tambahkan elemen input grup untuk **Persyaratan Umum**:
  - Input label: `id="input-hero-label-persyaratan"`, `data-field="landing_label_persyaratan"`.
  - Textarea list: `id="input-hero-persyaratan-umum"`, `data-field="landing_persyaratan_umum"`, rows: 4, dengan hint pemisah baris baru `.wp-field-help`.
- [x] Tambahkan elemen input grup untuk **Dokumen Pendaftaran**:
  - Input label: `id="input-hero-label-dokumen"`, `data-field="landing_label_dokumen"`.
  - Textarea list: `id="input-hero-dokumen-pendaftaran"`, `data-field="landing_dokumen_pendaftaran"`, rows: 4, dengan hint pemisah baris baru `.wp-field-help`.

### Tahap 3: Reaktivitas Kanvas & Inspector Bridge
- [x] Buka `resources/views/public/blocks/hero.blade.php`:
  - Tambahkan `data-wp-editable="hero_label_persyaratan"` pada `<h2>` persyaratan.
  - Tambahkan `data-wp-editable="hero_persyaratan_list"` pada `<ul>` persyaratan.
  - Tambahkan `data-wp-editable="hero_label_dokumen"` pada `<h2>` dokumen.
  - Tambahkan `data-wp-editable="hero_dokumen_list"` pada `<ul>` dokumen.
- [x] Buka `resources/js/pages/superadmin/site-editor/inspector.js`:
  - Tambahkan mapping field persyaratan dan dokumen ke blok `hero` di `fieldMap`.
- [x] Buka `resources/js/pages/public/landing-canvas-runtime.js`:
  - Sempurnakan handler `WP_SET_TEXT` agar bila target adalah `<ul>`, teks dipisahkan per baris dan diubah menjadi `<li>`, serta bila target adalah `.hero-subhead`, membagi kata dengan aksen warna ungu `<span class="text-accent">`.

### Tahap 4: Build Aset & Pengujian
- [x] Jalankan `npm run build` untuk mengompilasi bundel Vite.
- [x] Jalankan `./vendor/bin/pint --test` untuk memastikan kerapian PSR-12.
- [x] Jalankan test otomatis `LandingCmsSiteEditorTest` dan pastikan semua skenario lulus hijau.
- [x] Verifikasi di browser: Buka Canvas Editor, ubah butir persyaratan, amati perubahan live di kanvas, klik "Simpan Draf", dan uji "Terbitkan".

---

## 6. Strategi Testing

1. **Unit & Feature Test (`LandingCmsSiteEditorTest`)**:
   - `test_superadmin_can_update_hero_persyaratan_and_dokumen_content()`:
     - Mengirim payload `landing_label_persyaratan`, `landing_persyaratan_umum`, `landing_label_dokumen`, dan `landing_dokumen_pendaftaran` via endpoint `save-draft`.
     - Memastikan nilai draf tersimpan di database dan sinkron pada blok `hero` dalam `draft_landing_page_document`.
     - Menjalankan endpoint `publish`.
     - Memastikan nilai terbit live di halaman publik `/` dan merender butir-butir baru tanpa masalah sanitasi.
2. **Negative & Edge Cases**:
   - Input berisi baris kosong/spasi berlebih (harus di-trim rapi tanpa membuat bullet kosong).
   - Input teks melebihi 2.000 karakter (harus tertolak oleh validasi FormRequest).
   - Karakter khusus HTML (harus tersanitasi aman dari XSS injection).

---

## 7. Risiko & Mitigasi

| Potensi Risiko | Tingkat | Mitigasi |
| :--- | :--- | :--- |
| Bullet poin kosong akibat admin menekan Enter ganda | Rendah | Menggunakan `array_filter` dan `trim` saat memecah baris baru di Blade/Runtime agar tidak ada elemen `<li></li>` kosong. |
| Konflik pemetaan `fieldMap` antara blok `hero` dan `informasi` | Rendah | Menyelaraskan konten ke blok yang sedang aktif di viewport kanvas dan sinkronisasi ganda. |
| Pelanggaran aturan Blade (inline styles/scripts) | Nol | Menggunakan kelas CSS yang sudah ada (`.wp-field-group`, `.wp-text-input`, `.wp-textarea-input`, `.wp-field-help`) tanpa menyisipkan CSS/JS inline di Blade. |

---

## 8. Kriteria Selesai (Definition of Done)

- [ ] SuperAdmin dapat melihat field judul dan deskripsi Persyaratan Umum serta Dokumen Pendaftaran di tab **General > Konten Hero Banner** Canvas Editor.
- [ ] Perubahan teks di field langsung tercermin di kanvas pratinjau secara instan (*live preview*).
- [ ] Nilai tersimpan dengan aman saat tombol **Simpan Draf** diklik.
- [ ] Nilai berhasil tayang pada halaman publik Landing Page saat tombol **Terbitkan** diklik.
- [ ] Semua pengujian PHPUnit (`LandingCmsSiteEditorTest`), PHPStan, dan Pint lulus 100%.
- [ ] Tidak ada regresi pada tampilan desktop maupun mobile.

