# Rencana Kerja: Perbaikan Tampilan Dashboard Administrasi Sesuai Desain Kedinasan

**Dokumen Rencana**: `docs/plans/2026-11-02-perbaikan-tampilan-dashboard-administrasi.md`  
**Status**: Selesai Diimplementasikan  
**Tujuan**: Memperbaiki tata letak, kebersihan informasi, ikonografi kedinasan, serta menambahkan kontrol paginasi per 10 data pada dashboard operasional administrasi (`admin/dashboard`) dan menyelaraskan konsistensi di seluruh halaman administrasi berdasarkan 5 poin evaluasi visual.

---

## 1. Analisis Kebutuhan & Masukan Visual Pengguna

Berdasarkan 5 tangkapan layar yang diberikan:
1. **Gambar 1 (Garis Vertikal Biru di Sisi Kiri Kartu)**:
   - *Masalah*: Kartu efisiensi operasional (`_attention.blade.php`) dan panel samping (`_aside.blade.php`) memiliki styling `border-left: 4px solid ...`. Hal ini tidak konsisten dengan bahasa desain Gentelella / MagKom Sleman yang menggunakan garis aksen di sisi **atas** kartu (`border-top: 3.5px solid ...`).
   - *Kebutuhan*: Hilangkan garis vertikal warna biru di sisi kiri (`border-left`), ganti menjadi aksen garis atas (`border-top`), dan konsistenkan di semua halaman administrasi.
2. **Gambar 2 (Teks Subtitle yang Membuat Tampilan Padat/Ramai)**:
   - *Masalah*: Terdapat subtitle deskriptif panjang: *"Tinjau dan proses berkas pendaftaran calon peserta magang yang membutuhkan verifikasi segera."* pada panel antrean verifikasi berkas dan *"Realisasi alokasi kursi magang periode aktif dinas."* pada panel kuota.
   - *Kebutuhan*: Hilangkan teks subtitle yang membuat tampilan menjadi ramai banyak teks sehingga header panel tampil bersih, ringkas, dan fokus.
3. **Gambar 3 (Ikon Petir `⚡` pada Kartu Aksi Cepat Admin)**:
   - *Masalah*: Menggunakan emoji petir `⚡` dalam span monospaced, terkesan seperti game dan tidak layak untuk standar portal instansi dinas pemerintah (Dinas Komunikasi dan Informatika).
   - *Kebutuhan*: Ganti dengan ikon SVG resmi kedinasan yang berkelas, formal, dan berwibawa (misal: Kompas Navigasi `compass` atau Pusat Kontrol Administrasi `sliders`/`briefcase`).
4. **Gambar 4 & Gambar 5 (Paginasi Kuota Jurusan Per 10 Item)**:
   - *Masalah*: Pada Gambar 4 ("Alokasi & Keterisian Kuota Magang Periode"), seluruh jurusan ditampilkan dalam satu daftar panjang ke bawah tanpa batas halaman.
   - *Kebutuhan*: Tampilkan data kuota per 10 baris secara default, dengan komponen paginasi 2 baris persis seperti referensi Gambar 5 (SuperAdmin Audit Log):
     - Baris 1: Informasi counter `Menampilkan 1 - 10 dari X kuota` + Selector per halaman `Menampilkan: [ 10 | 20 | 30 ]`.
     - Baris 2: Indikator halaman `Lembar Halaman 1 dari Y` + Kontrol navigasi `[ « Prev ] [ 1 ] [ 2 ] [ 3 ] ... [ Next » ]`.
5. **Konsistensi Visual**:
   - Selaraskan seluruh halaman administrasi (`admin/dashboard`, `admin/penempatan`, `admin/sertifikat`, dll.) agar tidak ada lagi kartu dengan `border-left` sisa masa lalu, menjaga token warna biru MagKom (`#01347C`), padding, dan border-radius seragam 12px.

---

## 2. Asumsi & Rekomendasi Desain

### A. Rekomendasi Ikon Kedinasan (Poin 3)
Untuk menggantikan emoji petir `⚡` pada header *"Aksi Cepat Admin"*, berikut 3 opsi ikon kedinasan resmi:
- **Opsi 1 (Rekomendasi Utama — Ikon `compass` / Kompas Navigasi)**:
  - *Makna*: Melambangkan navigasi terarah, ketepatan arah kerja, dan pintasan cepat menuju layanan operasional dinas.
  - *Estetika*: Menggunakan SVG vector bergaris presisi dalam wadah badge berlatar biru muda Sleman (`rgba(1, 52, 124, 0.08)`) dengan warna ikon biru tua kedinasan (`#01347C`).
- **Opsi 2 (Ikon `sliders` / Pusat Kontrol Operasional)**:
  - *Makna*: Melambangkan konsol kendali operasional dan manajemen alur kerja pendaftaran dinas.
- **Opsi 3 (Ikon `briefcase` / Tata Kelola Administrasi)**:
  - *Makna*: Melambangkan portofolio dan administrasi perkantoran dinas formal.

### B. Evaluasi Pendekatan Paginasi Kuota (Poin 4 & 5)

| Kriteria | **Pendekatan A: Client-Side Interactive Pagination (Rekomendasi)** | **Pendekatan B: Full Page Reload Server-Side Query** |
| :--- | :--- | :--- |
| **Deskripsi** | Data kuota periode aktif (biasanya 10–40 jurusan) dimuat ke DOM, lalu di-paginate secara mulus per 10 item menggunakan controller JS ringan di `resources/js/`. | Menggunakan Laravel `LengthAwarePaginator` dengan reload URL `?kuota_page=1&kuota_per_page=10`. |
| **Pengalaman Pengguna (UX)** | **Sangat Cepat & Responsif**: Admin dapat berpindah lembar 10/20/30 dan halaman 1, 2, 3 tanpa jeda reload dan posisi scroll dashboard tidak meloncat. | Terjadi refresh halaman penuh setiap kali menekan nomor halaman, scroll dashboard kembali ke atas. |
| **Akurasi Metrik** | Total kapasitas kursi magang di Hero Progress Bar tetap menghitung 100% kuota periode tanpa terpotong paginasi. | Membutuhkan kalkulasi terpisah agar paginasi list tidak memotong total kuota di hero bar. |
| **Kesesuaian Desain** | 100% identik dengan Gambar 5 (struktur 2 baris: counter + pill 10/20/30 + lembar halaman + prev/next). | 100% identik dengan Gambar 5. |
| **Rekomendasi** | **Dipilih (Pendekatan A)** untuk kenyamanan dan kecepatan admin. | Alternatif sekunder. |

---

## 3. Mockup Tata Letak Panel Kuota dengan Paginasi (Sesuai Gambar 5)

```text
+----------------------------------------------------------------------------------------------------+
| Alokasi & Keterisian Kuota Magang Periode                                   [ Kelola Kuota → ]   ^ |
+----------------------------------------------------------------------------------------------------+
| [Total Kapasitas Kursi Magang (November 2026)                      16 / 30 Kursi (53%)]            |
| [========================-------------------------------------------------------------]            |
|                                                                                                    |
| 1. Teknik Komputer dan Jaringan (TKJ) [SMK]                                             5 / 5 Penuh|
| 2. Rekayasa Perangkat Lunak (RPL) [SMK]                                         2 / 5 Sisa 3 Kursi |
| ... (maksimal 10 item per halaman)                                                                 |
+----------------------------------------------------------------------------------------------------+
| BARIS 1 FOOTER:                                                                                    |
| Menampilkan 1 - 10 dari 24 kuota                         Menampilkan: [ 10 ]  20   30              |
+----------------------------------------------------------------------------------------------------+
| BARIS 2 FOOTER:                                                                                    |
| Lembar Halaman 1 dari 3                                    [ « Prev ] [ 1 ]  [ 2 ]  [ 3 ] [ Next »]|
+----------------------------------------------------------------------------------------------------+
```

---

## 4. Perubahan Berkas

| Berkas | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/admin/dashboard/partials/_attention.blade.php` | `Modify` | Hapus semua `border-left: 4px solid ...` pada 3 kartu efisiensi KPI. Pasang `border-top: 3.5px solid ...` agar konsisten dengan standar Gentelella. |
| `resources/views/admin/dashboard/partials/_aside.blade.php` | `Modify` | Hapus `border-left` pada kartu aside; ganti emoji petir `⚡` dengan SVG ikon kedinasan resmi (`compass`) berpalet biru Sleman. |
| `resources/views/admin/dashboard/partials/_queue_table.blade.php` | `Modify` | Hapus atribut `subtitle="..."` panjang yang membuat tampilan header ramai. |
| `resources/views/admin/dashboard/partials/_quota_monitoring.blade.php` | `Modify` | Hapus atribut `subtitle="..."`, bungkus baris kuota dalam wadah paginasi, dan tambahkan komponen paginasi 2 baris (counter + switcher 10/20/30 + lembar halaman + kontrol navigasi). |
| `resources/views/admin/penempatan/index.blade.php` | `Modify` | Bersihkan deklarasi sisa `border-left` pada kartu statistik penempatan demi konsistensi. |
| `resources/views/admin/sertifikat/index.blade.php` | `Modify` | Bersihkan deklarasi sisa `border-left` pada kartu statistik sertifikat demi konsistensi. |
| `resources/css/components/pagination.css` | `Modify` | Tambahkan kelas pendukung paginasi kuota dashboard (`.kuota-pagination-*`, `.kuota-per-page-*`) yang mengadopsi standar Gambar 5 (responsif Light & Dark Mode). |
| `resources/js/pages/admin/dashboard.js` atau `resources/js/layouts/dashboard.js` | `Modify` | Tambahkan controller pagination interaktif kuota (menangani tombol 10/20/30, prev/next, tombol lembar halaman, dan update counter baris). |
| `tests/Feature/AdminDashboardEnhancementTest.php` | `Modify` | Tambahkan assertion untuk memvalidasi: ketiadaan subtitle ramai, ketiadaan `border-left` pada kartu admin, keberadaan ikon kedinasan SVG, serta markup paginasi kuota per 10 data. |

---

## 5. Langkah Implementasi (Checklist)

- [x] **Langkah 1: Eliminasi Garis Vertikal Kiri (`border-left`) & Standardisasi Garis Atas (`border-top`)**
  - Buka `resources/views/admin/dashboard/partials/_attention.blade.php`:
    - Ubah kartu 1: ganti `border-left: 4px solid var(--blue-600);` menjadi `border-top: 3.5px solid var(--blue-600); border-left: none;`.
    - Ubah kartu 2: ganti `border-left: 4px solid var(--green-600);` menjadi `border-top: 3.5px solid var(--green-600); border-left: none;`.
    - Ubah kartu 3: ganti `border-left: 4px solid #4F46E5;` menjadi `border-top: 3.5px solid #4F46E5; border-left: none;`.
  - Buka `resources/views/admin/dashboard/partials/_aside.blade.php`:
    - Bersihkan inline `border-left: 4px solid var(--amber-700, #B45309);` pada panel aside.
  - Periksa `admin/penempatan/index.blade.php` dan `admin/sertifikat/index.blade.php`: bersihkan atribut `border-left` pada stat cards.

- [x] **Langkah 2: Pembersihan Teks Subtitle yang Membuat Ramai**
  - Buka `resources/views/admin/dashboard/partials/_queue_table.blade.php`:
    - Hapus properti `subtitle="Tinjau dan proses berkas pendaftaran calon peserta magang yang membutuhkan verifikasi segera."`.
  - Buka `resources/views/admin/dashboard/partials/_quota_monitoring.blade.php`:
    - Hapus properti `subtitle="Realisasi alokasi kursi magang periode aktif dinas."`.

- [x] **Langkah 3: Penggantian Ikon Petir dengan Ikon Kedinasan Berwibawa**
  - Buka `resources/views/admin/dashboard/partials/_aside.blade.php` pada panel *"Aksi Cepat Admin"*:
    - Hapus span emoji `⚡`.
    - Ganti dengan komponen ikon SVG kedinasan berwibawa:
      ```html
      <span class="adm-quick-icon-badge" style="width: 28px; height: 28px; border-radius: 6px; background: rgba(1, 52, 124, 0.08); color: var(--magkom-primary-blue, #01347C); display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;">
          <x-sa.icon name="compass" :size="16" />
      </span>
      ```
    - Tampilan judul menjadi harmonis, rapi, dan sekelas portal dinas resmi.

- [x] **Langkah 4: Implementasi Komponen Paginasi Kuota Per 10 (Sesuai Gambar 5)**
  - Buka `resources/views/admin/dashboard/partials/_quota_monitoring.blade.php`:
    - Berikan atribut unik pada container baris kuota (misal `data-kuota-item`) dan atribut wrapper paginasi kuota.
    - Sisipkan markup footer paginasi 2 baris di bawah `.cv-goal-list`:
      - Baris 1: Counter info data (`Menampilkan <span id="kuotaStart">1</span> - <span id="kuotaEnd">10</span> dari <span id="kuotaTotal">...</span> jurusan`) + Segmented pill buttons (`10`, `20`, `30`).
      - Baris 2: Indikator Lembar Halaman (`Lembar Halaman <span id="kuotaCurrentPage">1</span> dari <span id="kuotaTotalPages">...</span>`) + Navigasi tombol nomor lembar (`« Prev`, tombol angka, `Next »`).
  - Tambahkan controller interaksi di `resources/js/layouts/dashboard.js` atau modul JS admin:
    - Fungsi inisialisasi paginasi: membagi item kuota sesuai `perPage` (default 10).
    - Event listener pada segmented pill `10`, `20`, `30` untuk mengubah `perPage` dan me-refresh daftar secara instan.
    - Event listener navigasi halaman (`Prev`, `Next`, angka 1..N).

- [x] **Langkah 5: Penataan CSS Paginasi & Responsivitas**
  - Buka `resources/css/components/pagination.css`:
    - Pastikan kelas paginasi 2 baris (`.kuota-pagination-*` dan `.kuota-per-page-*`) memiliki visual token yang 100% selaras dengan Gambar 5 (`audit-log.css`).
    - Pastikan active state pill tombol menggunakan warna biru MagKom (`#01347C` / `#0641CC`) dan tombol non-aktif memiliki latar halus.
    - Dukung tampilan pada layar smartphone (`flex-wrap`, penyesuaian ukuran tombol).

- [x] **Langkah 6: Quality Gate & Automated Testing**
  - Jalankan `php artisan test tests/Feature/AdminDashboardEnhancementTest.php` untuk memverifikasi fungsionalitas admin dashboard.
  - Jalankan test visual & Blade integrity:
    - Pastikan tidak ada string `border-left` yang tidak diinginkan di kartu admin.
    - Pastikan subtitle panjang sudah bersih.
    - Pastikan emoji petir `⚡` sudah tidak ada di markup.
    - Pastikan elemen paginasi kuota hadir dan dirender dengan benar.
  - Jalankan `./vendor/bin/pint --test` untuk memastikan formatting kode.
  - Jalankan `npm run build` untuk memvalidasi kompilasi aset Vite.

---

## 6. Strategi Testing

1. **Automated Feature Testing**:
   - `test_admin_dashboard_renders_clean_headers_without_cluttered_subtitles`:
     - Memastikan respons HTTP 200.
     - Memastikan teks *"Tinjau dan proses berkas pendaftaran calon peserta magang"* dan *"Realisasi alokasi kursi magang periode aktif dinas"* **TIDAK** ada di HTML respons.
   - `test_admin_dashboard_cards_use_top_border_and_no_left_border`:
     - Memastikan tidak ada inline style `border-left: 4px solid` pada kartu-kartu KPI efisiensi.
   - `test_admin_dashboard_quick_actions_uses_official_government_icon`:
     - Memastikan emoji `⚡` tidak lagi digunakan pada header Aksi Cepat Admin.
   - `test_admin_dashboard_quota_has_pagination_component_matching_image_5`:
     - Memastikan markup paginasi 2 baris (counter, switcher 10/20/30, lembar halaman, prev/next) hadir di halaman dashboard admin.

2. **Cross-Browser & Visual Polish**:
   - Verifikasi kerapian layout 2 kolom dashboard admin pada resolusi desktop (1920x1080 dan 1366x768) serta tablet/mobile.

---

## 7. Risiko & Mitigasi

| Potensi Risiko | Tingkat | Mitigasi |
| :--- | :--- | :--- |
| **Kalkulasi Total Kapasitas Kuota di Hero Bar Terpengaruh Paginasi** | Sedang | Hero bar progress kuota dihitung langsung dari total agregat PHP (`$totalKuotaUmum` dan `$totalKuotaTerisi`), sedangkan paginasi hanya membatasi visibilitas elemen daftar jurusan di bawahnya secara visual. Angka total kapasitas tetap 100% akurat. |
| **Tampilan Paginasi Rusak di Layar Sempit (Mobile)** | Rendah | Menggunakan layout flexbox dengan `flex-wrap: wrap` dan `gap: 0.5rem`, serta styling per-page pill yang otomatis membungkus rapi seperti pada Audit Log SuperAdmin. |
| **Aturan AGENTS.md Mengenai Blade & Asset** | Rendah | Tidak menulis `<style>` atau `<script>` baru di Blade. Seluruh interaksi ditempatkan di `resources/js/` dan CSS di `resources/css/components/pagination.css`. Perubahan Blade murni untuk struktur semantik container. |

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Semua kartu administrasi bebas dari garis vertikal sisi kiri (`border-left`) dan seragam menggunakan garis aksen atas (`border-top: 3.5px solid ...`).
- [x] Teks subtitle deskripsi panjang pada header panel antrean verifikasi dan kuota berhasil dihilangkan sehingga tampilan rapi dan tidak padat.
- [x] Emoji petir `⚡` berhasil digantikan dengan ikon SVG kedinasan resmi (`compass`) berwibawa khas Diskominfo Sleman.
- [x] Panel kuota jurusan menampilkan maksimal 10 jurusan per lembar dengan kontrol paginasi 2 baris persis seperti Gambar 5 (10/20/30, lembar halaman, prev/next).
- [x] Semua automated tests lulus (`php artisan test`).
- [x] Asset pipeline Vite terkompilasi bersih (`npm run build`).
- [x] Laporan akhir disajikan lengkap dan transparan.

