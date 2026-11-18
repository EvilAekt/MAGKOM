# Rencana Kerja: Redesign & Perapian Pop-Up Konfirmasi Review Tugas Pembimbing (Modern & Terstruktur)

Dokumen rencana kerja ini dibuat untuk menindaklanjuti permintaan perapian layout modal konfirmasi pada Gambar 1 (**Konfirmasi Penyelesaian Tugas**) dan Gambar 2 (**Konfirmasi Pengembalian Tugas**) pada modul Review Tugas Pembimbing ([`resources/views/pembimbing/tugas/show.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/pembimbing/tugas/show.blade.php)).

---

## 1. Analisis Akar Masalah (Mengapa Tampilan Rusak & Berantakan)

Berdasarkan pemeriksaan kode pada [`resources/views/pembimbing/tugas/show.blade.php:533-554`](file:///d:/laragon/www/MAGKOM/resources/views/pembimbing/tugas/show.blade.php#L533-L554) dan stylesheet [`resources/css/components/confirm-modal.css:238-246`](file:///d:/laragon/www/MAGKOM/resources/css/components/confirm-modal.css#L238-L246):

1. **Bug Flex Direction Row (Kolom Terjepit)**:
   - Seluruh konten isi dialog (`reviewModalQuestion`, `.magkom-modal-target-box`, `#reviewModalFeedbackBox`, dan `#reviewModalConsequence`) dibungkus sekaligus di dalam satu container:
     `<div id="reviewModalCallout" class="magkom-modal-callout variant-danger">`
   - Class `.magkom-modal-callout` memiliki rule bawaan:
     ```css
     .magkom-modal-callout {
         display: flex;
         align-items: flex-start;
         gap: 0.75rem;
     }
     ```
   - Secara default di CSS, `flex-direction` bernilai `row` (horizontal). Akibatnya, ke-4 elemen di dalamnya dipaksa berjejer ke samping dalam 1 baris yang sama.
2. **Teks Terjepit Vertikal (*Excessive Word Wrapping*)**:
   - Kalimat pertanyaan *"Apakah Anda yakin seluruh hasil kerja..."* terhimpit dalam kolom sempit selebar ~100px sehingga setiap 1–2 kata terpotong ke baris baru.
3. **Konten Meluap Keluar Kotak (*Overflow Out of Bounds*)**:
   - Pada Gambar 2, ketika catatan umpan balik (*feedback note*) muncul, kolom ke-4 (`#reviewModalConsequence`) terdorong melebihi lebar kontainer modal dan teks merahnya tumpah ke luar latar belakang kotak.
4. **Hilangnya Struktur Semantik & Hierarki**:
   - Tidak ada wrapper `.magkom-modal-body` yang membatasi margin dan alur vertikal.
   - Target tugas dan identitas mentee tidak memiliki kartu avatar inisial profesional seperti modal verifikasi admin.
   - Tombol tutup dialog masih menggunakan karakter HTML kasar `&times;`.

---

## 2. Solusi Desain: Standar Modern Enterprise DS-MagKom

Kita akan menyelaraskan modal ini dengan standar emas modal konfirmasi MAGKOM yang sudah terbukti rapi dan elegan pada modul Admin ([`resources/views/admin/pendaftaran/show.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/pendaftaran/show.blade.php)):

```
┌───────────────────────────────────────────────────────────────────┐
│ (✓ / ↺)  Konfirmasi Penyelesaian / Pengembalian            [ ✕ ] │
│          Persetujuan / Evaluasi Hasil Penugasan Peserta           │
├───────────────────────────────────────────────────────────────────┤
│                                                                   │
│  Apakah Anda yakin ingin menyelesaikan / mengembalikan tugas ini? │
│                                                                   │
│  ┌── [Identitas Tugas & Peserta (Identity Strip)] ─────────────┐  │
│  │ ( F )  HIBOUVCYOVUYCGVUGY                     [#TUGAS-12]   │  │
│  │        febrian • SMK Negeri 2 Depok Sleman                  │  │
│  └─────────────────────────────────────────────────────────────┘  │
│                                                                   │
│  ┌── [Catatan Umpan Balik (Khusus Pengembalian)] ──────────────┐  │
│  │ 💬 CATATAN UMPAN BALIK:                                     │  │
│  │ "Tolong lengkapi daftar pustaka dan lampirkan diagram alur" │  │
│  └─────────────────────────────────────────────────────────────┘  │
│                                                                   │
│  ┌── [Banner Dampak Tindakan (Impact Notice Strip)] ───────────┐  │
│  │ ℹ️  Tugas ini akan ditandai tuntas selesai dan tidak dapat  │  │
│  │     diubah kembali menjadi draft atau perlu perbaikan.      │  │
│  └─────────────────────────────────────────────────────────────┘  │
│                                                                   │
├───────────────────────────────────────────────────────────────────┤
│                                [ Batal ]  [ Ya, Tandai Selesai ]  │
└───────────────────────────────────────────────────────────────────┘
```

### Poin-Poin Peningkatan:
1. **Alur Vertikal Berjenjang (*Natural Vertical Flow*)**: Setiap informasi (pertanyaan, identitas tugas, catatan umpan balik, dan konsekuensi) memiliki baris dan jarak (*spacing*) yang proporsional.
2. **User & Task Identity Strip**:
   - Menampilkan avatar inisial peserta dengan palet aksen yang selaras.
   - Judul tugas yang jelas, tajam, dan tebal.
   - Metadata mentee & institusi dilengkapi ikon SVG representatif.
   - Badge nomor ID tugas (`#TUGAS-xx`).
3. **Dedicated Feedback Card**:
   - Catatan revisi tampil sebagai callout box elegan berwarna lembut (warm amber/rose tint) dengan ikon chat dan teks yang mudah dibaca.
4. **Impact Notice Banner**:
   - Menggantikan bullet point mentah dengan banner ramping berikon status (`info` / `alert`) dan warna latar lembut (`#F0FDF4` untuk selesai, `#FEF2F2` untuk revisi).
5. **Modern Header Close Button**: Menggantikan karakter `&times;` dengan ikon SVG close yang presisi.

---

## 3. Berkas yang Akan Diubah & Rincian Perubahan

Sesuai **`AGENTS.md` Rule 3 (Aturan khusus Blade)**:
- Perubahan markup Blade hanya dilakukan untuk memperbaiki struktur kontainer modal agar tidak lagi flex-row tertekan.
- Seluruh styling ditempatkan di file CSS (`resources/css/`).
- Seluruh interaksi ditempatkan di file JavaScript (`resources/js/`).

| Berkas | Aksi | Rincian Perubahan |
| :--- | :---: | :--- |
| [`resources/views/pembimbing/tugas/show.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/pembimbing/tugas/show.blade.php) | **Modify** | 1. Ganti tombol close `&times;` dengan icon SVG presisi.<br>2. Hapus pembungkus luar `.magkom-modal-callout` yang merusak layout.<br>3. Bungkus dalam `.magkom-modal-body` terstruktur.<br>4. Gunakan `.magkom-modal-identity-strip` untuk tugas & peserta.<br>5. Tempatkan `#reviewModalFeedbackBox` secara rapi di tengah.<br>6. Gunakan `.magkom-modal-impact-banner` untuk `#reviewModalCallout` dan konsekuensi. |
| [`resources/css/pages/pembimbing/tugas-review.css`](file:///d:/laragon/www/MAGKOM/resources/css/pages/pembimbing/tugas-review.css) | **Modify** | 1. Perbarui styling `.review-modal-feedback-box` agar berpenampilan callout bersih (light theme & dark mode).<br>2. Pastikan styling `.magkom-modal-identity-strip` pada konteks review tugas responsif dan rapi di mobile. |
| [`resources/js/pages/pembimbing/tugas-review.js`](file:///d:/laragon/www/MAGKOM/resources/js/pages/pembimbing/tugas-review.js) | **Modify** | 1. Perbarui teks `modalConsequence` agar bersih dari bullet mentah `• ` (karena sudah diwakili oleh icon SVG impact banner).<br>2. Pastikan `setModalVariant` memperbarui warna avatar dan banner konsekuensi secara mulus. |

---

## 4. Tahapan Pelaksanaan

### Tahap 1: Penataan Ulang Struktur Markup Blade
- Menata ulang elemen modal pada baris 500–569 di [`resources/views/pembimbing/tugas/show.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/pembimbing/tugas/show.blade.php).
- Memastikan atribut `id` tetap identik agar kompatibilitas event listener di JavaScript tidak terputus.

### Tahap 2: Penyempurnaan CSS (Light & Dark Theme)
- Menambahkan aturan styling untuk `.review-modal-feedback-header`, `.review-modal-feedback-box`, dan `.review-modal-feedback-content` di [`resources/css/pages/pembimbing/tugas-review.css`](file:///d:/laragon/www/MAGKOM/resources/css/pages/pembimbing/tugas-review.css).
- Memastikan kompatibilitas dark mode (`[data-theme="dark"]`).

### Tahap 3: Penyelarasan JavaScript Handler
- Menyesuaikan teks konsekuensi di [`resources/js/pages/pembimbing/tugas-review.js`](file:///d:/laragon/www/MAGKOM/resources/js/pages/pembimbing/tugas-review.js) agar kalimatnya mengalir natural tanpa karakter bullet `• `.
- Menyesuaikan varian avatar dan icon SVG secara dinamis saat modal terbuka.

### Tahap 4: Quality Gates & Pengujian Menyeluruh
- `npm run build` (Kompilasi asset Vite tanpa error)
- `php artisan view:cache` (Verifikasi sintaks dan kompilasi Blade 100% valid)
- `php vendor/bin/pint --test` (Linter PSR-12)
- `phpstan analyse --memory-limit=1G` (Analisis statis PHP 8.3)
- `php artisan test tests/Feature/PembimbingReviewClassroomTest.php tests/Feature/TypographyDesignSystemTest.php` (Pengujian feature test terkait)

---

## 5. Pertanyaan & Persetujuan (Konfirmasi Pengguna)

Sesuai aturan `AGENTS.md` Bagian 3.3:
> *"Perubahan markup Blade hanya boleh dilakukan apabila perubahan struktur HTML memang mutlak diperlukan... Minta persetujuan eksplisit pengguna sebelum mengubah Blade."*

