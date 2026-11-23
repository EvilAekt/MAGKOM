# Plan: Penyempurnaan Terminologi & Label UI FAQ Canvas Editor

**Dokumen**: `docs/plans/2026-11-23-penyempurnaan-terminologi-dan-label-faq-canvas-editor.md`  
**Tanggal**: 23 November 2026  
**Status**: Usulan / Menunggu Persetujuan Pengguna  
**Referensi Aturan**: `AGENTS.md` (Design System & UX Copywriting Preservation)

---

## 1. Analisis & Latar Belakang Masalah

Pada modul Canvas Editor (`/superadmin/site-editor`) bagian inspektor FAQ, label dan penamaan komponen saat ini dirasa kurang efisien, bercampur istilah bahasa asing yang rancu, dan kurang mencerminkan standar portal resmi pemerintahan (*Diskominfo Sleman*):

1. **Badge `Q&A #1`**:
   - *Kelemahan*: Singkatan asing (*Question & Answer*) yang kurang selaras dengan bahasa baku portal kedinasan.
   - *Dampak UX*: Terkesan seperti draft pengembang (*developer jargon*) alih-alih produk CMS web yang matang.
2. **Label `Pertanyaan (Statement Question)`**:
   - *Kelemahan*: Menggunakan istilah ganda (*pleonasme*) dan kontradiksi bahasa (*statement* vs *question*).
   - *Dampak UX*: Label terlalu panjang dan memakan ruang vertikal inspektor samping (lebar ~320px).
3. **Label `Jawaban / Keterangan Penjelasan`**:
   - *Kelemahan*: Berbelit-belit (*redundant*). Kata "keterangan" dan "penjelasan" memiliki makna yang sama.
   - *Dampak UX*: Kurang ringkas, padat, dan profesional.

---

## 2. Opsi Rekomendasi Penamaan Profesional

Berikut 3 paket opsi penamaan yang dirancang berdasarkan praktik terbaik UX Writing (*Enterprise CMS & Portal Pemerintah*):

| Elemen UI | Terminologi Saat Ini | **Opsi 1: Minimalis & Elegan (Direkomendasikan)** | **Opsi 2: Formal Kedinasan** | **Opsi 3: Standar WordPress Gutenberg** |
| :--- | :--- | :--- | :--- | :--- |
| **Badge Kartu** | `Q&A #1` | **`Pertanyaan #1`** | **`Butir FAQ #1`** | **`Item #1`** |
| **Label Pertanyaan** | `Pertanyaan (Statement Question)` | **`Pertanyaan`** | **`Kalimat Pertanyaan`** | **`Judul Pertanyaan`** |
| **Placeholder Pertanyaan** | `Masukkan kalimat pertanyaan FAQ...` | `Tuliskan pertanyaan... (contoh: Bagaimana alur pendaftaran?)` | `Tuliskan pertanyaan yang sering ditanyakan...` | `Masukkan judul pertanyaan...` |
| **Label Jawaban** | `Jawaban / Keterangan Penjelasan` | **`Jawaban`** | **`Isi Jawaban & Penjelasan`** | **`Isi Jawaban`** |
| **Placeholder Jawaban** | `Masukkan jawaban atau penjelasan FAQ secara rinci...` | `Tuliskan jawaban lengkap di sini...` | `Uraikan penjelasan resmi atau panduan lengkap...` | `Tuliskan isi jawaban...` |
| **Tombol Tambah** | `+ Tambah Pertanyaan` | **`+ Tambah Pertanyaan`** | **`+ Tambah Butir FAQ`** | **`+ Tambah Item`** |
| **Header Repeater** | `Daftar Tanya Jawab` | **`Daftar Pertanyaan`** | **`Daftar Tanya Jawab Resmi`** | **`Daftar Item FAQ`** |

> **Rekomendasi Utama (Opsi 1 - Minimalis & Elegan)**:
> Memberikan kejelasan maksimal tanpa kata-kata berlebih (*Zero Clutter*), sangat nyaman dibaca pada layar editor beresolusi apapun, serta sesuai dengan pedoman WCAG dan bahasa baku Indonesia.

---

## 3. Cakupan File yang Terdampak

Perubahan ini murni penyempurnaan teks antarmuka (*UI copy & microcopy*) tanpa mengubah kontrak data backend (`q` dan `a`) maupun arsitektur database:

1. `resources/views/superadmin/site-editor/partials/_inspector.blade.php`:
   - Penyesuaian teks header repeater dan tombol tambah.
2. `resources/js/pages/superadmin/site-editor/inspector.js`:
   - Penyesuaian template literal badge (`Pertanyaan #${idx + 1}`), label form, dan placeholder pada fungsi `renderFaqItemsList()`.
3. `tests/Feature/SuperAdmin/LandingCmsSiteEditorTest.php`:
   - Verifikasi bahwa seluruh test tetap berjalan hijau (kontrak field API tidak berubah).

---

## 4. Rencana Tahapan Eksekusi

1. **Konfirmasi Pengguna**: Pengguna memilih opsi penamaan yang diinginkan (Opsi 1, 2, atau 3).
2. **Pembaruan Kode**:
   - Memperbarui teks pada `_inspector.blade.php`.
   - Memperbarui teks badge, label, dan placeholder pada `inspector.js`.
3. **Kompilasi & Pengujian Mutu**:
   - Menjalankan `npm run build`.
   - Menjalankan `./vendor/bin/pint --test`.
   - Menjalankan `php artisan test --filter=LandingCmsSiteEditorTest`.
4. **Verifikasi Visual**: Memastikan tampilan kartu inspector tampak bersih, proporsional, dan elegan.

