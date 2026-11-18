# Plan: Desain Kartu Tautan Acuan Tugas Peserta (Gaya Google Classroom Card Tanpa Tombol Hapus)

Dokumen ini merinci rencana teknis perbaikan dan penyempurnaan tampilan tautan referensi (`link_referensi`) yang ditugaskan oleh Pembimbing pada antarmuka detail tugas Peserta (`peserta.tugas.show`). Perbaikan ini menggantikan teks tautan sederhana (Gambar 1) menjadi bentuk kartu lampiran interaktif bergaya Google Classroom card yang rapi dan terstruktur (Gambar 2), namun **bersifat read-only (tanpa tombol silang 'x' untuk menghapus)**.

---

## 1. Tujuan & Ruang Lingkup

1. **Transformasi Visual Tautan Acuan Peserta**:
   * Mengubah tampilan tautan acuan dari teks link kecil sederhana (`🔗 Buka Tautan Acuan ↗`) menjadi kartu lampiran profesional berbasis badge dan meta info.
   * Format kartu menampilkan:
     * **Ikon Badge Kiri**: Kotak membulat berwarna biru lembut (`#E0F2FE`) dengan ikon rantai/link biru Sleman (`#0284C7`).
     * **Judul / URL Tautan**: Teks URL atau judul tautan acuan yang jelas dengan indikator panah eksternal (`↗`).
     * **Subteks Meta Informasi**: Menampilkan nama domain/host (misal: `magkom.slemankab.go.id` atau `drive.google.com`) dan label konteks `• Tautan Referensi`.
   * **Read-Only / Non-Deletable**: Menghilangkan tombol silang (`✕` / remove) karena bagi Peserta, materi acuan ini adalah mandat dari Pembimbing yang hanya untuk dibaca/diakses, bukan untuk dihapus atau diubah.
2. **Kepatuhan Arsitektur & Aturan Repositori**:
   * Mengikuti aturan baku Blade: Penambahan struktur semantik di Blade diminimalkan tanpa inline CSS/JS.
   * Seluruh styling ditempatkan di `resources/css/pages/peserta/tugas-detail.css`.
   * Mendukung mode gelap (*Dark Mode* / `[data-theme="dark"]`) secara presisi sesuai token desain MAGKOM.

---

## 2. Analisis Perbandingan Visual

| Aspek | Tampilan Lama (Gambar 1) | Tampilan Target (Gambar 2 yang Diminta) |
| :--- | :--- | :--- |
| **Elemen Utama** | Tag `<a>` sederhana dengan inline style `ref-link-pill` | Kartu lampiran terstruktur (`.tugas-attached-card--readonly`) |
| **Ikon** | Ikon SVG 14px kecil menyatu dalam teks | Badge kotak 40x40px dengan rounded-8px dan warna background aksen |
| **Hierarki Teks** | 1 baris teks: `Buka Tautan Acuan ↗` | 2 baris terstruktur: Baris 1 (URL/Nama Tautan), Baris 2 (Domain + Tautan Referensi) |
| **Interaktivitas** | Klik hanya pada teks link kecil | Seluruh permukaan kartu dapat diklik (*clickable card*) dengan feedback hover halus |
| **Tombol Hapus (`x`)** | Tidak ada | **Sengaja ditiadakan** (peserta membaca materi acuan dari pembimbing) |

---

## 3. Rencana Perubahan Komponen & File

### A. Perubahan Markup Blade (`resources/views/peserta/tugas/show.blade.php`)
* **Lokasi**: Sekitar baris 85–104 pada container `{{-- Lampiran Acuan Pembimbing (Jika Ada) --}}`.
* **Detail Perubahan**:
  1. Menghapus inline styles (`style="margin-top: 24px; padding-top: 16px; border-top: 1px dashed #E2E8F0; ..."`) dan menggantinya dengan class semantik `.classroom-instruction-attachments`.
  2. Mengganti markup `.ref-link-pill` menjadi kartu berstruktur:
     ```blade
     @if($tugas->link_referensi)
         @php
             $parsedHost = parse_url($tugas->link_referensi, PHP_URL_HOST) ?: 'Tautan Eksternal';
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
                         <span>{{ $parsedHost }} &bull; Tautan Referensi</span>
                     </div>
                 </div>
             </div>
         </a>
     @endif
     ```
  3. Memastikan jika ada berkas lampiran (`$tugas->file_lampiran`), tampilannya juga diselaraskan menggunakan struktur kartu serasi agar harmonis.

### B. Penambahan Styling CSS (`resources/css/pages/peserta/tugas-detail.css`)
* Menambahkan CSS scoped untuk:
  * `.classroom-instruction-attachments`: Container lampiran dengan border atas pemisah halus dan layout flexbox/grid yang rapi.
  * `.tugas-attached-card.tugas-attached-card--readonly`:
    * `display: flex; align-items: center; justify-content: space-between;`
    * `background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 10px 14px;`
    * `text-decoration: none; transition: all 0.2s ease; cursor: pointer;`
    * `hover`: `border-color: #93C5FD; background: #F0F9FF; box-shadow: 0 1px 3px rgba(0,0,0,0.05);`
  * `.tugas-file-badge.badge-link`:
    * `width: 40px; height: 40px; border-radius: 8px; background: #E0F2FE; color: #0284C7;`
  * `.tugas-attached-name`: Teks URL/judul berwarna `#1D4ED8` (Sleman Blue) dengan efek underline saat di-hover dan ellipsis jika teks sangat panjang.
  * `.tugas-attached-meta`: Teks abu-abu muted `#64748B`, font-size 12px.
  * **Dark Mode Tokens (`[data-theme="dark"]`)**:
    * Background: `var(--dm-canvas, #0F172A)`
    * Border: `var(--dm-border, #1E293B)`
    * Hover background: `#1E293B`
    * Teks meta: `var(--dm-text-muted, #94A3B8)`

---

## 4. Langkah Pengujian & Verifikasi

1. **Pemeriksaan Visual**:
   * Login sebagai akun Peserta (misal: `peserta12@gmail.com` / `Password123!`).
   * Buka tugas yang memiliki `link_referensi` (misal tugas ID `116` atau tugas yang memiliki tautan acuan).
   * Verifikasi kartu tautan acuan tampil dengan badge kotak biru, URL, domain, dan label "Tautan Referensi".
   * Pastikan **tidak ada tombol silang 'x'**.
   * Klik kartu tautan untuk memastikan URL terbuka di tab baru (`target="_blank"`).
2. **Uji Responsif & Tema**:
   * Uji pada layar desktop (> 1024px) dan mobile (< 640px) untuk memastikan teks URL terpotong rapi dengan ellipsis tanpa merusak lebar kartu.
   * Uji pergantian tema Light Mode dan Dark Mode.
3. **Pemeriksaan Regresi Test Suite**:
   * Jalankan `php artisan test --filter=PesertaTugas` untuk memastikan tidak ada alur penyerahan tugas peserta yang terganggu.
   * Jalankan `npm run build` untuk memverifikasi kompilasi aset Vite.

---

## 5. Keputusan & Pertanyaan Terbuka untuk Pengguna

1. **Perilaku Berkas Lampiran (File Guide/Panduan)**:  
   Jika tugas pembimbing juga melampirkan berkas (misal file PDF/panduan), apakah kartu berkas tersebut sebaiknya diseragamkan dengan bentuk kartu yang sama (badge merah untuk PDF / abu-abu untuk file, nama file, ukuran file, tombol unduh), agar konsisten berdampingan dengan kartu tautan ini?  
   *(Rekomendasi: Ya, diselaraskan agar kartu file dan kartu tautan tampak seragam dan elegan).*
2. **Kesesuaian Tampilan**:  
   Apakah format kartu dengan domain (misal `magkom.slemankab.go.id • Tautan Referensi`) sudah persis seperti yang diharapkan?
