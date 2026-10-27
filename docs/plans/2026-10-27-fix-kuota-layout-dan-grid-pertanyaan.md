# Rencana Kerja: Perbaikan Layout Kuota Explorer & Penempatan Pertanyaan "Magang sama siapa?" di Kanan

**Tanggal**: 27 Oktober 2026  
**Status**: Selesai Diimplementasikan & Terverifikasi (Production Ready)  
**Dokumen**: `docs/plans/2026-10-27-fix-kuota-layout-dan-grid-pertanyaan.md`  
**Area Terkait**: Section Kuota (`resources/views/public/landing.blade.php`, `public/css/landing-modern.css`, dan `public/js/landing-interactive.js`)

---

## 1. Analisis Masalah & Akar Penyebab
Berdasarkan tangkapan layar yang dikirimkan oleh pengguna:
1. **Akar Masalah Struktur HTML**:
   - Terdapat tag penutup `</div>` yang hilang pada blok Pertanyaan 2 (*"2. Mau mulai kapan?"*).
   - Akibatnya:
     - Pertanyaan 3 (*"3. Magang sama siapa?"*) masuk dan menumpuk ke dalam kolom Pertanyaan 2.
     - Kartu banner biru dongker (`.kuota-highlight-banner` / ring gauge) tidak sengaja tersedot ke dalam `.kuota-questions-grid` sehingga tampil sebagai kolom ketiga yang sempit dan meninggi di sebelah kanan pertanyaan.
     - Carousel kartu jurusan di bawahnya ikut tertekan dan layout aslinya menjadi rusak.
2. **Kebutuhan Pengguna**:
   - Mengembalikan layout asli yang bersih dan simetris.
   - Menempatkan Pertanyaan 3 (*"3. Magang sama siapa?"*) **berjajar horizontal di sebelah kanan** Pertanyaan 2 (*"2. Mau mulai kapan?"*):
     - Kolom 1: `1. Kamu siapa?`
     - Kolom 2: `2. Mau mulai kapan?`
     - Kolom 3: `3. Magang sama siapa?` (di kanan periode mulai magang)
   - Banner total kuota (`.kuota-highlight-banner`) kembali berada di bawah baris pertanyaan secara memanjang horizontal penuh (*full-width banner*).

---

## 2. Rincian Solusi Teknis

### A. Perbaikan Markup HTML (`resources/views/public/landing.blade.php`)
1. **Penutupan Tag yang Valid**:
   - Menutup blok Pertanyaan 2 secara utuh:
     ```html
     <div>
         <p class="question-label">2. Mau mulai kapan?</p>
         <div id="kuotaBulanPillContainer" class="pill-group">
             ...
         </div>
     </div> <!-- Penutup Pertanyaan 2 -->
     ```
   - Menyusun Pertanyaan 3 (*"3. Magang sama siapa?"*) sebagai elemen `div` saudara kandung (*sibling*) yang berdiri mandiri di sebelah kanannya:
     ```html
     <div>
         <p class="question-label">3. Magang sama siapa?</p>
         <div class="pill-group" id="kuotaModeGroup">
             <button type="button" class="pill-btn active" data-mode-pill="mandiri">Sendiri (1 Orang)</button>
             <button type="button" class="pill-btn" data-mode-pill="kelompok">Bareng Tim / Kelompok</button>
         </div>
     </div> <!-- Penutup Pertanyaan 3 -->
     ```
   - Menutup `.kuota-questions-grid` secara tuntas (`</div>`), sehingga banner total kuota (`.kuota-highlight-banner`) berada di luar grid dan kembali ke format banner horizontal penuh di bawahnya.

2. **Panel Pilihan Jumlah Tim (`#kuotaTeamSizeWrapper`)**:
   - Ditempatkan di antara baris pertanyaan dan banner total kuota.
   - Tampil anggun hanya ketika tombol *Bareng Tim / Kelompok* aktif, tanpa menggeser kolom pertanyaan di atasnya.

### B. Penyempurnaan Tata Letak CSS (`public/css/landing-modern.css`)
1. **Grid Pertanyaan 3 Kolom Sejajar**:
   - Pada layar desktop/laptop (`min-width: 900px`): `grid-template-columns: 1fr 1fr 1fr;` atau `repeat(3, minmax(0, 1fr));`.
   - Pada layar tablet (`640px` s/d `899px`): `grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));`.
   - Pada layar mobile: 1 kolom tersusun vertikal rapi.
2. **Pill Buttons & Wrap**:
   - `white-space: nowrap;` pada tombol pill agar teks bulan dan jenis pendaftaran tidak patah canggung menjadi dua baris jika ruang mencukupi.
3. **Banner Highlight (`.kuota-highlight-banner`)**:
   - Memastikan kembali ke format banner mendatar (*horizontal flex container*) selebar kontainer section.

---

## 3. Rencana Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/public/landing.blade.php` | Modify | Memperbaiki struktur penutupan `</div>` pada Pertanyaan 2, meletakkan Pertanyaan 3 sejajar di sebelah kanan Pertanyaan 2, dan membebaskan `.kuota-highlight-banner` ke luar grid pertanyaan. |
| `public/css/landing-modern.css` | Modify | Menata aturan responsif `.kuota-questions-grid` agar 3 pertanyaan sejajar rapi di desktop dan banner highlight kembali horizontal penuh. |

---

## 4. Langkah-Langkah Implementasi Bertahap

- [ ] **Langkah 1**: Perbaiki struktur tag HTML di `resources/views/public/landing.blade.php`:
  - Tutup tag `<div>` Pertanyaan 2.
  - Letakkan `<div>` Pertanyaan 3 ("3. Magang sama siapa?") sebagai kolom ke-3 di kanan Pertanyaan 2.
  - Tutup `.kuota-questions-grid` sebelum `.kuota-highlight-banner`.
- [ ] **Langkah 2**: Perbaiki CSS di `public/css/landing-modern.css` untuk `.kuota-questions-grid` (3 kolom sejajar di desktop) dan pastikan `.kuota-highlight-banner` tidak terdistorsi.
- [ ] **Langkah 3**: Bersihkan cache view (`php artisan view:clear`) dan verifikasi keutuhan layout di `http://127.0.0.1:8000/#kuota`.

---

## 5. Kriteria Selesai (Definition of Done)
1. **Pertanyaan 1, 2, dan 3 Sejajar Rapi**: "1. Kamu siapa?", "2. Mau mulai kapan?", dan "3. Magang sama siapa?" tampil berdampingan secara horizontal di sebelah kanan.
2. **Banner Ring Gauge Kembali Normal**: Kartu biru dongker (`.kuota-highlight-banner`) membentang horizontal penuh di bawah pertanyaan, bukan berdiri sempit di kolom samping.
3. **Carousel Kartu Jurusan Kembali Normal**: Memiliki ruang lebar penuh dengan tombol navigasi samping `<` dan `>`.
4. **Fungsi Dinamis Tetap Berjalan**: Pilihan Mandiri vs Kelompok dan jumlah anggota tetap mengubah link kartu jurusan secara real-time.
