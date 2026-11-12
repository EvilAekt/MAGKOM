# Rencana Kerja: Redesign Halaman Detail & Berkas Pendaftaran Peserta Sesuai Mockup Referensi

## 1. Tujuan
Meredesain antarmuka halaman **Detail & Berkas Pendaftaran Peserta** (`/peserta/pendaftaran/{pendaftaran}`) agar memiliki keselarasan visual (visual parity) 1:1 dengan gambar referensi desain yang dilampirkan pengguna (`media_1794368498247_7c28ebf3.jpg`). 

Pembaruan ini mencakup 4 blok antarmuka utama:
1. **Hero Banner Sleman Navy**: Penambahan subtitle deskriptif dan restrukturisasi 4 kotak metadata di sisi kanan (`JURUSAN`, `DURASI`, `JALUR`, dan `STATUS` emas solid).
2. **Stepper Timeline Modern**: Visualisasi 3 tahap horizontal minimalis dengan lingkaran status centang `✓` (navy solid & emas solid) serta garis konektor horizontal antar-tahap yang presisi.
3. **Alert Banner Kelulusan Diterima**: Transformasi dari alert hijau inline ke container amber-gold elegan (`#FEF9C3`) ber-border emas dengan ikon centang square dan 2 tombol aksi (`Lihat Surat Diterima` & `Unduh Surat Diterima (PDF)`).
4. **Card Utama Berkas Persyaratan**: Pembersihan divider, styling baris dokumen minimalis dengan ikon outline, badge kapsul `Belum Ada` (atau tombol aksi jika sudah diunggah/bisa diunggah), serta box notice gembok terkunci berlatar soft gray (`#F8FAFC`).

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja Logis:
1. **Status Dinamis pada Hero Card**: Kotak ke-4 pada Hero Banner menampilkan status aktual pendaftaran (`$pendaftaran->status->label()`), berlatar emas solid `#FFC300` dengan teks gelap `#003566` ketika diterima, dan menyesuaikan warna tema yang harmonis untuk status lainnya (misal: kuning untuk diajukan/perlu perbaikan, abu-abu untuk dibatalkan).
2. **Fungsionalitas Stepper**: Lingkaran Tahap 1 (Berkas Persyaratan), Tahap 2 (Verifikasi Berkas), dan Tahap 3 (Diterima/Keputusan) menampilkan centang `✓` sesuai progres riil alur pendaftaran. Pada status `diterima`, ketiga tahap berstatus centang `✓` lengkap seperti pada gambar.
3. **Alert Kelulusan**: Alert kelulusan hanya ditampilkan jika status pendaftaran berstatus `diterima` dan data penempatan sudah tersedia (`$pendaftaran->penempatan`), sesuai dengan logika bisnis pendaftaran MagKom yang ada.
4. **Zero Inline Styles**: Sesuai `AGENTS.md` Pasal 3, seluruh `style="..."` inline yang saat ini ada pada alert penerimaan dan kotak berkas terkunci akan dihapus dari Blade dan dipindahkan menjadi utility class / scoped class di `resources/css/pages/peserta/pemberkasan.css`.
5. **Mode Kelompok & Mandiri**: Layout kartu berkas mendukung mode mandiri dan kelompok tanpa merusak tampilan personil tim jika pendaftaran bertipe kelompok.

---

## 3. Pendekatan Teknis (Evaluasi Opsi)

### Perbandingan Opsi:
- **Opsi A (Direkomendasikan) - Penyelarasan Terfokus Berbasis Design System Sleman MagKom**:
  - Memperbarui styling scoped pada `resources/css/pages/peserta/pemberkasan.css`.
  - Melakukan penyesuaian markup Blade semantik di [`resources/views/peserta/pendaftaran/detail.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/pendaftaran/detail.blade.php) dan partial [`resources/views/peserta/pendaftaran/partials/_doc_card_compact.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/pendaftaran/partials/_doc_card_compact.blade.php):
    - Membersihkan seluruh inline CSS (`style="..."`).
    - Menambahkan elemen subtitle pada Hero Banner.
    - Mengorganisir 4 kotak metadata sisi kanan (JURUSAN, DURASI, JALUR, STATUS).
    - Menyempurnakan garis timeline stepper dan ikon centang.
  - *Kelebihan*: Kode sangat bersih, mematuhi standar repositori, maintainable, dan akurat 1:1 dengan gambar.
  - *Kekurangan*: Memerlukan konfirmasi pengguna untuk sedikit modifikasi markup Blade.

- **Opsi B - CSS Override Murni**:
  - Mencoba menimpa tampilan dengan CSS murni tanpa mengubah Blade sama sekali.
  - *Kelebihan*: Tidak menyentuh file Blade.
  - *Kekurangan*: Rapuh (*fragile*), inline styles di Blade sulit di-override secara konsisten, dan teks deskripsi baru tidak dapat diinjeksi secara semantik.

**Pilihan**: Menggunakan **Opsi A**.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/css/pages/peserta/pemberkasan.css` | **Modify** | Menambahkan styling 4 kotak metadata hero banner, garis konektor stepper horizontal presisi, class alert kelulusan amber-gold (`.pemberkasan-approved-alert`), styling baris berkas persyaratan rapi, badge `Belum Ada`, serta locked notice box. |
| `resources/views/peserta/pendaftaran/detail.blade.php` | **Modify** | 1. Tambah subtitle hero.<br>2. Struktur ulang 4 chip metadata sisi kanan hero.<br>3. Bersihkan label nomor pada stepper (menjadi `Berkas Persyaratan`, `Verifikasi Berkas`, `Diterima`).<br>4. Ganti inline style alert kelulusan dengan class semantic `.pemberkasan-approved-alert`.<br>5. Ganti inline style kotak terkunci dengan class `.pemberkasan-locked-notice`. |
| `resources/views/peserta/pendaftaran/partials/_doc_card_compact.blade.php` | **Modify** | Penyelarasan styling class ikon dokumen outline, penataan badge `Belum Ada` abu-abu terang, dan pembersihan padding/spacing agar presisi dengan mockup. |

---

## 5. Langkah Implementasi Terstruktur

### Tahap 1: Pembaruan Styling CSS (`resources/css/pages/peserta/pemberkasan.css`)
- [ ] 1.1 Definisikan styling Hero Banner:
  - `.hero-subtitle`: font-size 0.84rem, opacity 0.85, margin-top 4px.
  - `.hero-meta-grid`: container flex/grid untuk 4 kotak metadata.
  - `.hero-meta-box`: background `rgba(0, 0, 0, 0.22)`, border `1px solid rgba(255, 255, 255, 0.18)`, border-radius 8px, padding 8px 14px, display flex flex-direction column.
  - `.hero-meta-label`: uppercase, font-size 0.65rem, letter-spacing 0.05em, color `rgba(255, 255, 255, 0.7)`.
  - `.hero-meta-value`: font-weight 700, font-size 0.82rem, color `#ffffff`.
  - `.hero-meta-box.status-gold`: background `#FFC300`, border-color `#FFC300`, teks label gelap (`#003566`), teks value tebal gelap (`#003566`).
- [ ] 1.2 Definisikan styling Stepper Timeline Modern:
  - `.stepper-timeline-modern`: flex horizontal dengan garis konektor presisi antar-lingkaran.
  - Garis konektor `.stepper-connector`: garis tipis solid 2px (`#003566` untuk tahap selesai/aktif, `#E2E8F0` untuk tahap belum aktif) yang memanjang di antara titik lingkaran.
  - Lingkaran `.stepper-circle-modern`: ukuran 36px, border 2px, center aligned icon centang `✓`.
  - Tahap 1 & 2 selesai: background `#003566`, border `#003566`, centang putih.
  - Tahap 3 (Diterima): background `#FFC300`, border `#003566`, centang `#003566` atau hitam.
- [ ] 1.3 Definisikan styling Alert Kelulusan Diterima:
  - `.pemberkasan-approved-alert`: background `#FEF9C3`, border `1.5px solid #FDE047`, border-radius 14px, padding 16px 20px, flex layout.
  - `.approved-icon-box`: width/height 40px, rounded-8px, background `#FFC300`, icon checkmark navy `#003566`.
  - `.approved-btn-preview`: background `#ffffff`, border `1px solid #CBD5E1`, text `#0F172A`, font-weight 600, border-radius 8px.
  - `.approved-btn-download`: background `#003566`, border none, text `#ffffff`, font-weight 600, border-radius 8px.
- [ ] 1.4 Definisikan styling Berkas Persyaratan & Locked Notice:
  - `.pemberkasan-main-card`: clean white container, border `1px solid #E2E8F0`, rounded 16px.
  - Header kartu berkas: ikon navy-emas, garis aksen kuning Sleman, badges format file dan batas ukuran.
  - Divider horizontal tipis `.pemberkasan-header-divider`.
  - Row berkas `.doc-card-premium`: border `1px solid #E2E8F0`, rounded 10px, padding 14px 20px, hover effect halus.
  - Status capsule `.status-badge-pending`: background `#F1F5F9`, border `1px solid #E2E8F0`, color `#64748B`, font-size 0.75rem, font-weight 600, padding 4px 12px, rounded-full.
  - Locked notice `.pemberkasan-locked-notice`: background `#F8FAFC`, border `1px dashed #CBD5E1`, border-radius 8px, padding 12px 18px, flex align items center, color `#475569`.

### Tahap 2: Penyelarasan Blade Markup
- [ ] 2.1 Perbarui [`resources/views/peserta/pendaftaran/detail.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/pendaftaran/detail.blade.php):
  - Tambahkan subtitle pada hero left: `<p class="hero-subtitle">Pantau tahapan pendaftaran dan kelengkapan berkas magang kamu.</p>`.
  - Ganti `.hero-chips-wrap` dengan 4 kotak metadata `.hero-meta-grid` (`JURUSAN`, `DURASI`, `JALUR`, dan `STATUS`).
  - Hapus penomoran ganda pada stepper ("1.", "2.", "3.") sehingga teks murni: `Berkas Persyaratan`, `Verifikasi Berkas`, `Diterima`.
  - Ganti elemen alert kelulusan inline style menjadi komponen berkelas `.pemberkasan-approved-alert`.
  - Ganti inline style kotak notice gembok terkunci menjadi `.pemberkasan-locked-notice`.
- [ ] 2.2 Perbarui [`resources/views/peserta/pendaftaran/partials/_doc_card_compact.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/pendaftaran/partials/_doc_card_compact.blade.php):
  - Pastikan icon outline dokumen dan nama dokumen tersusun presisi.
  - Pastikan badge `Belum Ada` menggunakan kelas `.status-badge-pending`.

### Tahap 3: Verifikasi & Kompilasi Aset
- [ ] 3.1 Jalankan `npm run build` untuk memvalidasi tidak ada syntax error pada CSS.
- [ ] 3.2 Bersihkan view cache dengan `php artisan view:clear`.
- [ ] 3.3 Jalankan code formatting dengan `./vendor/bin/pint --test`.
- [ ] 3.4 Jalankan automated test feature pendaftaran peserta:
  - `php artisan test tests/Feature/PendaftaranPesertaTest.php`
  - `php artisan test tests/Feature/PesertaPendaftaranFlowTest.php`

---

## 6. Strategi Testing

1. **Automated Feature Tests**:
   - `PendaftaranPesertaTest.php`: Memastikan aliran unggah berkas, verifikasi admin, dan status kelulusan tetap lulus 100%.
   - `PesertaPendaftaranFlowTest.php`: Memastikan navigasi detail pendaftaran dan view rendering berjalan tanpa error 500.
2. **Visual & Responsive Verification**:
   - Memastikan tampilan desktop (>= 1024px) memiliki susunan 4 kotak metadata sejajar di kanan hero banner.
   - Memastikan tampilan tablet & mobile (< 768px) membungkus (*wrap*) kotak metadata dengan rapi tanpa overflow horizontal.
   - Memastikan warna kartu kelulusan berpenampilan amber-gold lembut (`#FEF9C3`) kontras dan mudah dibaca (WCAG AA).
3. **Regression Check**:
   - Memastikan fungsi pratinjau in-app modal (`openDocPreview`) dan tombol unduh PDF tetap bekerja normal.
   - Memastikan aksi unggah berkas dan pengajuan pendaftaran mandiri tetap berfungsi saat status pendaftaran memerlukan unggah berkas.

---

## 7. Risiko & Mitigasi

| Risiko | Tingkat | Mitigasi |
| :--- | :---: | :--- |
| **Overflow metadata pada layar sempit/mobile** | Sedang | Terapkan `flex-wrap: wrap` dan media query `@media (max-width: 768px)` agar 4 kotak metadata menjadi grid 2x2 pada layar ponsel. |
| **Ketidaksesuaian warna teks pada badge status gold** | Rendah | Gunakan warna teks Sleman Navy gelap (`#003566`) di atas background `#FFC300` agar rasio kontras melebihi 7:1 (standar WCAG AAA). |
| **Breaking perubahan mode kelompok** | Rendah | Pada kartu `JALUR`, jika pendaftaran merupakan kelompok, tampilkan label `Kelompok` dan nama kelompok secara aman tanpa merusak layout. |

---

## 8. Kriteria Selesai (Definition of Done)

- [ ] Seluruh visual Hero Banner, Stepper 3 Tahap, Alert Kelulusan Amber-Gold, dan Card Berkas Persyaratan identik dengan mockup referensi `media_1794368498247_7c28ebf3.jpg`.
- [ ] Tidak ada penulisan CSS `<style>` atau inline `style="..."` pada file Blade yang diubah (`detail.blade.php`).
- [ ] Seluruh styling tersimpan rapi dan modular di `resources/css/pages/peserta/pemberkasan.css`.
- [ ] Kompilasi `npm run build` sukses tanpa error.
- [ ] Seluruh automated test suite (`PendaftaranPesertaTest.php` & `PesertaPendaftaranFlowTest.php`) lulus hijau.

