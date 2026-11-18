# Rencana Kerja: Redesain Tampilan Link Review & Evaluasi Penugasan

Dokumen ini merupakan rencana teknis terstruktur untuk memperbarui tampilan tautan (*link card*) pada halaman **Review & Evaluasi Penugasan Pembimbing** (`pembimbing.tugas.show`). Perubahan ini menggantikan format strip horizontal lama (*compact link strip* dengan ikon globe dan tombol 'Buka ↗') agar tampil presisi menyerupai kartu lampiran Google Classroom read-only (ikon badge rantai/link, nama URL bertaut, panah eksternal, dan subteks domain host) persis seperti tangkapan layar yang dilampirkan oleh pengguna.

---

## 1. Tujuan & Ruang Lingkup

1. **Transformasi Visual Tautan**:
   - Mengubah tampilan tautan pada halaman `Review & Evaluasi Penugasan` (`resources/views/pembimbing/tugas/show.blade.php`) dari format lama (`.compact-link-strip`) menjadi format kartu interaktif read-only (`.tugas-attached-card.tugas-attached-card--readonly`).
   - Menerapkan desain kartu pada 2 titik tautan:
     1. **Materi & Dokumen Acuan dari Pembimbing** (`$tugas->link_referensi`).
     2. **Kiriman Tugas Peserta** (`$latestLog->link`).
2. **Karakteristik Tampilan Target (Sesuai Gambar Referensi)**:
   - **Ikon Badge Kiri**: Kotak membulat 40x40px berwarna biru lembut (`#E0F2FE`) dengan ikon SVG rantai/link berwarna biru Sleman (`#0284C7`).
   - **Baris 1 (URL & Indikator Eksternal)**: Teks URL berwana biru (`#1D4ED8`) dengan teks terpotong (*ellipsis*) rapi jika panjang, disertai ikon panah eksternal `↗`. Efek hover memberikan garis bawah (*underline*).
   - **Baris 2 (Meta Domain & Label)**: Teks sekunder berwarna abu-abu redup (`#64748B`), menampilkan domain host hasil ekstraksi (misal: `www.youtube.com`) dan label konteks (misal: `• Tautan Referensi` atau `• Tautan Tugas Peserta`).
   - **Interaktivitas Penuh**: Seluruh area kartu merupakan elemen tautan interaktif (`<a>`) dengan status `target="_blank"` dan `rel="noopener noreferrer"`, dilengkapi transisi hover border dan background lembut tanpa tombol terpisah.
   - **Dukungan Dark Mode**: Penyelarasan token warna tema gelap (`[data-theme="dark"]`) agar kontras rasio tetap memenuhi standar WCAG 2.1 AA.
3. **Kepatuhan Terhadap Aturan Repositori (`AGENTS.md`)**:
   - Tidak menulis CSS atau JS inline di dalam Blade.
   - Seluruh styling baru ditampung di `resources/css/pages/pembimbing/tugas-review.css`.
   - Perubahan struktur Blade dimohonkan persetujuannya secara eksplisit sebelum dieksekusi.

---

## 2. Analisis Perbandingan Visual

| Parameter | Tampilan Saat Ini (Gambar 2) | Tampilan Target (Gambar 1 yang Diminta) |
| :--- | :--- | :--- |
| **Bentuk Kontainer** | Strip biru muda memanjang (`.compact-link-strip`) dengan background tint `#E6EEFF` | Kartu putih/slate lembut (`.tugas-attached-card--readonly`) dengan border `#E2E8F0` dan radius 8px |
| **Ikon Utama** | Ikon bola dunia/globe kecil 16px dengan latar putih bulat/persegi | Badge kotak 40x40px warna `#E0F2FE` dengan ikon rantai/link link SVG 18px warna `#0284C7` |
| **Struktur Teks** | 1 baris teks horizontal: Label tebal `Tautan Referensi:` + URL terpotong | 2 baris hierarki: Baris 1 (URL + Panah ↗), Baris 2 (`domain.com • Label`) |
| **Aksi Pembuka** | Tombol biru terpisah di sebelah kanan: `Buka ↗` (`.btn-compact-open`) | Seluruh permukaan kartu dapat diklik (*single unified clickable card*), tombol terpisah dihilangkan |
| **Lokasi Penerapan** | Berlaku di Materi Acuan & Kiriman Mentee | Diselaraskan di kedua area pada halaman Review & Evaluasi Penugasan |

---

## 3. Asumsi Kerja & Evaluasi Alternatif

### A. Asumsi Kerja
1. Target halaman yang dimaksud adalah `resources/views/pembimbing/tugas/show.blade.php` (halaman topbar *Review & Evaluasi Penugasan*).
2. Tautan acuan (`$tugas->link_referensi`) dan tautan hasil tugas mentee (`$latestLog->link`) keduanya akan dikonversi ke format kartu modern ini demi keseragaman visual.
3. Berkas lampiran (`file_lampiran` acuan dan `file` hasil mentee):
   - Tetap dipertahankan fungsionalitas unduhnya.
   - Opsi default pada plan ini fokus meredesain tautan (link) sesuai permintaan, namun disiapkan opsi penyelarasan berkas lampiran jika diinginkan agar tampilan keduanya serasi.

### B. Evaluasi Alternatif Arsitektur

- **Opsi 1 (Hanya Mengubah CSS dari `.compact-link-strip`)**:
  - *Kelebihan*: Tidak mengubah file Blade.
  - *Kekurangan*: Struktur HTML eksisting memiliki tombol terpisah `<a class="btn-compact-open">` dan ikon globe `<div class="compact-link-icon">`, serta tidak memiliki elemen untuk teks domain host terpisah (baris 2). Memaksakan CSS akan menghasilkan styling yang rapuh dan mengorbankan aksesibilitas.
- **Opsi 2 (Rekomendasi - Penyesuaian Semantik Blade + CSS Scoped di `tugas-review.css`)**:
  - *Kelebihan*: Menggunakan pola komponen yang identik dengan kartu acuan pada `peserta.tugas.show` yang sudah terbukti stabil, semantik murni (`<a>`), ramah aksesibilitas, dan mendukung dark mode tanpa hack.
  - *Kekurangan*: Memerlukan penyesuaian markup Blade minimal pada 2 blok link di `resources/views/pembimbing/tugas/show.blade.php`.

---

## 4. Rincian Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/pembimbing/tugas/show.blade.php` | Modify | 1. Mengganti blok `$tugas->link_referensi` dari `.compact-link-strip` menjadi `.tugas-attached-card--readonly`.<br>2. Mengganti blok `$latestLog->link` menjadi `.tugas-attached-card--readonly`.<br>3. Menambahkan parsing host URL menggunakan `parse_url()` standar PHP. |
| `resources/css/pages/pembimbing/tugas-review.css` | Modify | Menambahkan class styling `.tugas-attached-card--readonly`, `.tugas-attached-left`, `.tugas-file-badge.badge-link`, `.tugas-attached-info`, `.tugas-attached-name`, `.external-icon`, `.tugas-attached-meta`, serta dukungan `[data-theme="dark"]`. |
| `tests/Feature/PembimbingFocusedMenteeTugasViewTest.php` | Modify | Menambahkan pengujian assertion bahwa kartu tautan acuan dan kiriman tugas ditampilkan dengan class dan struktur kartu yang benar pada halaman review tugas. |

---

## 5. Rencana Markup & Kode

### A. Markup Blade (`resources/views/pembimbing/tugas/show.blade.php`)

#### 1. Pada Bagian Materi & Dokumen Acuan Pembimbing (sekitar baris 238):
```blade
@if($tugas->link_referensi)
    @php
        $parsedRefHost = parse_url($tugas->link_referensi, PHP_URL_HOST) ?: 'Tautan Eksternal';
    @endphp
    <a href="{{ $tugas->link_referensi }}"
       target="_blank"
       rel="noopener noreferrer"
       class="tugas-attached-card tugas-attached-card--readonly"
       title="{{ $tugas->link_referensi }}">
        <div class="tugas-attached-left">
            <div class="tugas-file-badge badge-link" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                </svg>
            </div>
            <div class="tugas-attached-info">
                <div class="tugas-attached-name">
                    <span>{{ $tugas->link_referensi }}</span>
                    <svg class="external-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="7" y1="17" x2="17" y2="7"></line>
                        <polyline points="7 7 17 7 17 7"></polyline>
                    </svg>
                </div>
                <div class="tugas-attached-meta">
                    <span>{{ $parsedRefHost }} &bull; {{ __('Tautan Referensi') }}</span>
                </div>
            </div>
        </div>
    </a>
@endif
```

#### 2. Pada Bagian Kiriman Tugas Peserta (sekitar baris 299):
```blade
{{-- Tautan Hasil Kerja Mentee (Classroom Card Style) --}}
@if($latestLog->link)
    @php
        $parsedSubmissionHost = parse_url($latestLog->link, PHP_URL_HOST) ?: 'Tautan Eksternal';
    @endphp
    <a href="{{ $latestLog->link }}"
       target="_blank"
       rel="noopener noreferrer"
       class="tugas-attached-card tugas-attached-card--readonly"
       title="{{ $latestLog->link }}">
        <div class="tugas-attached-left">
            <div class="tugas-file-badge badge-link" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                </svg>
            </div>
            <div class="tugas-attached-info">
                <div class="tugas-attached-name">
                    <span>{{ $latestLog->link }}</span>
                    <svg class="external-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="7" y1="17" x2="17" y2="7"></line>
                        <polyline points="7 7 17 7 17 7"></polyline>
                    </svg>
                </div>
                <div class="tugas-attached-meta">
                    <span>{{ $parsedSubmissionHost }} &bull; {{ __('Tautan Tugas Peserta') }}</span>
                </div>
            </div>
        </div>
    </a>
@endif
```

---

## 6. Langkah Implementasi Bertahap

- [ ] **Langkah 1**: Dapatkan persetujuan formal pengguna atas rencana kerja ini dan izin penyesuaian markup Blade.
- [ ] **Langkah 2**: Perbarui `resources/css/pages/pembimbing/tugas-review.css`:
  - [ ] Tambahkan styling `.tugas-attached-card--readonly` (layout flex, border, radius, shadow, background).
  - [ ] Tambahkan styling `.tugas-file-badge.badge-link` (dimensi 40x40px, warna `#0284C7`, latar `#E0F2FE`).
  - [ ] Tambahkan hierarki font untuk `.tugas-attached-name` dan meta `.tugas-attached-meta`.
  - [ ] Tambahkan aturan `[data-theme="dark"]` untuk kartu, badge link, dan teks.
- [ ] **Langkah 3**: Perbarui markup Blade pada `resources/views/pembimbing/tugas/show.blade.php`:
  - [ ] Ganti blok tautan referensi pembimbing dengan markup kartu baru.
  - [ ] Ganti blok tautan kiriman peserta mentee dengan markup kartu baru.
- [ ] **Langkah 4**: Jalankan kompilasi frontend `npm run build` untuk memverifikasi sintaks Vite dan bundel CSS.
- [ ] **Langkah 5**: Tambahkan Feature Test pada `tests/Feature/PembimbingFocusedMenteeTugasViewTest.php`:
  - [ ] Verifikasi kartu tautan acuan memiliki class `tugas-attached-card--readonly` dan teks `Tautan Referensi`.
  - [ ] Verifikasi kartu tautan kiriman memiliki host domain dan atribut `target="_blank"`.
- [ ] **Langkah 6**: Jalankan test suite `php artisan test --filter=PembimbingFocusedMenteeTugasViewTest` dan verifikasi hasilnya hijau.

---

## 7. Strategi Testing & Quality Gates

1. **Testing Otomatis**:
   - `php artisan test --filter=PembimbingFocusedMenteeTugasViewTest`
   - `./vendor/bin/pint --test` untuk memastikan kerapian kode PHP.
   - `npm run build` untuk memvalidasi build aset Vite tanpa error.
2. **Pengujian Manual & Visual**:
   - Buka halaman review tugas pembimbing dengan data yang memiliki link referensi dan link kiriman peserta.
   - Periksa apakah teks link panjang terpotong (*ellipsis*) dengan rapi dan tidak merusak layout (*no overflow*).
   - Klik kartu untuk memastikan tautan terbuka di tab baru (`target="_blank"`).
   - Uji perpindahan mode terang (*light mode*) dan mode gelap (*dark mode*) untuk memastikan kontras warna tetap tajam.

---

## 8. Kriteria Selesai (Definition of Done)

- [ ] Tampilan link pada bagian "Materi & Dokumen Acuan dari Pembimbing" tampil identik dengan Gambar 1.
- [ ] Tampilan link pada bagian "Kiriman Tugas Peserta" tampil identik dengan Gambar 1.
- [ ] Tidak ada CSS atau JS inline yang disisipkan di dalam file Blade.
- [ ] Asset CSS di-bundle sukses via Vite (`npm run build`).
- [ ] Feature Test lolos 100%.
- [ ] Working tree Git tetap rapi dan tidak ada perubahan lokal yang hilang.
