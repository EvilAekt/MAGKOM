# Rencana Kerja: Penyesuaian Menu Pemilihan Kuota Mobile Sesuai Gambar Referensi

Berdasarkan gambar referensi yang diunggah (`media_1794833648169_29d2cc1e.png`), tampilan seksi pemilihan kriteria kuota di landing page mobile akan disesuaikan menjadi satu kartu kontainer yang rapi, elegan, dan identik dengan referensi.

---

## 1. Analisis Visual & Struktur Komponen (Sesuai Gambar)

| Elemen | Tampilan di Gambar Referensi | Implementasi Teknis |
| :--- | :--- | :--- |
| **Kontainer Luar** | Satu kartu putih solid melengkung (`border-radius: 20px`), bergaris tepi abu-abu halus (`#E2E8F0`), bayangan lembut. | `.kuota-questions-grid` diberi latar `#FFFFFF`, border halus, radius 20px, dan padding 20px 16px di mobile. |
| **Badge Nomor** | Lingkaran biru gelap (`#0A2540`) dengan angka putih `1`, `2`, `3` di sebelah kiri teks pertanyaan. | Elemen semantik `<span class="question-step-badge">1</span>` di dalam label pertanyaan. |
| **Pertanyaan 1** | Teks: **"Dari mana Anda?"**<br>Dua tombol 2 kolom: `Siswa SMK` (Aktif: solid navy) & `Mahasiswa (D3/S1)` (Inaktif: putih garis tepi). | Grid 2-kolom (`grid-template-columns: 1fr 1fr`), tombol melengkung 12px, tinggi sentuh ergonomis (46px). |
| **Pertanyaan 2** | Teks: **"Kapan Anda siap?"**<br>Satu tombol penuh (full width): Ikon kalender + `Desember 2026` (Aktif: solid navy). | Container flex kolom (`width: 100%`), tombol bulan full width dengan ikon kalender SVG elegan di samping nama bulan. |
| **Pertanyaan 3** | Teks: **"Magang untuk berapa orang?"**<br>Dua tombol 2 kolom: `Individu (1 Orang)` (Aktif: solid navy) & `Kelompok (Tim)` (Inaktif: putih garis tepi). | Grid 2-kolom (`grid-template-columns: 1fr 1fr`), tombol melengkung 12px. |

---

## 2. File yang Akan Diubah

### A. Markup Blade: [resources/views/public/blocks/kuota.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/public/blocks/kuota.blade.php)
- **Alasan Perubahan Markup**: Diperlukan elemen penampung badge lingkaran angka (`<span class="question-step-badge">`), penampung teks pertanyaan (`<span class="question-title-text">`), serta ikon SVG kalender di tombol bulan agar tampilan identik dengan gambar.
- **Pembaruan Default Label**:
  - `landing_kuota_q1_label` default menjadi: **"Dari mana Anda?"** (dengan parser nomor otomatis).
  - `landing_kuota_q2_label` default menjadi: **"Kapan Anda siap?"**.
  - `landing_kuota_q3_label` default menjadi: **"Magang untuk berapa orang?"**.

### B. Styling CSS: [resources/css/pages/public/landing.css](file:///d:/MAGKOM/MAGKOM/resources/css/pages/public/landing.css) & [public/css/landing-modern.css](file:///d:/MAGKOM/MAGKOM/public/css/landing-modern.css)
- Menambahkan aturan `@media (max-width: 768px)` khusus untuk:
  - Kartu putih melengkung `.kuota-questions-grid`.
  - Badge lingkaran angka biru dongker `.question-step-badge`.
  - Tata letak tombol 2 kolom `.kuota-pills-row` dan tombol bulan full-width `.kuota-bulan-pills-row`.
  - Styling state aktif (navy solid `#0A2540`, teks putih, shadow lembut) dan inaktif (putih border `#CBD5E1`, teks `#1E293B`).
- Menjaga tampilan desktop ($\ge 768\text{px}$) tetap sejajar 3 kolom horizontal dengan badge angka modern.

---

## 3. Langkah Quality Gate

1. Jalankan `npm run build` untuk mengompilasi CSS/JS Vite.
2. Jalankan `./vendor/bin/pint --test` untuk memastikan standar kode PHP/Blade.
3. Jalankan suite pengujian: `php artisan test --filter="LandingPage|SuperAdminModuleEnhancementTest"` untuk memastikan semua test assertion tetap 100% hijau.

