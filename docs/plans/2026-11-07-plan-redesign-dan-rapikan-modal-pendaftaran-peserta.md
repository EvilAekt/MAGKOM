# Rencana Kerja: Redesign & Merapikan Tampilan Popup (Modal) Pengajuan Pendaftaran Magang Peserta

## 1. Konteks & Latar Belakang
Pada menu **Pendaftaran Magang Peserta** ([resources/views/peserta/pendaftaran/index.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/pendaftaran/index.blade.php)):
- Ketika peserta mengklik tombol **"Daftar Sekarang"** (baik dari hero banner maupun dari salah satu kartu kuota bulan magang), muncul jendela popup (modal formulir pengajuan).
- Tampilan popup saat ini masih menggunakan gaya dasar dengan banyak *inline styles* kaku, kotak biru kontras tinggi, susunan chip durasi yang belum proporsional, serta visual estimasi tanggal yang belum selaras dengan identitas **Design System MagKom** (Sleman Abyssal Navy `#02275D`, Sleman Gold `#FFCB05`, dan Clean SaaS Paper Card).
- Pengguna meminta untuk **merubah dan merapikan tampilan popup** ini agar tampak profesional, modern, mudah dipahami calon peserta, dan responsif.

---

## 2. Analisis Komponen Modal & Rencana Perubahan Visual

| Bagian Popup | Kondisi Saat Ini | Desain Baru yang Lebih Rapi & Modern |
|---|---|---|
| **Header Modal** | Teks judul biasa dengan tombol `&times;` kotak kecil abu-abu. | Header beraksen Sleman Navy `#02275D` dengan ikon modal pendaftaran (badge bulat lembut), tipografi judul tebal dengan KaTeX/font display, dan tombol close bulat modern dengan micro-interaction hover. |
| **Profil & Jurusan Strip** | Kotak biru solid (`#EFF6FF`) dengan border tebal dan tata letak teks bertumpuk padat. | Kartu ringkasan terstruktur (*Summary Card*): nama peserta, jenjang (badge pill), jurusan, asal institusi, dan bidang penempatan dengan ikon gedung Diskominfo Sleman yang rapi. |
| **Pemilihan Periode Bulan** | Dropdown biasa dengan font standar. | Dropdown dengan label tegas (*floating label / modern form-group*), opsi bulan magang dengan indikator sisa kursi yang jelas (misal: *Desember 2026 — Tersisa 4 Kursi*). |
| **Pemilihan Durasi Magang** | Chip 1 s/d 6 bulan terbagi dalam 3 kolom dengan border kaku. | Interactive Segmented Duration Grid: 6 chip dengan visual elegan. Chip durasi 3 bulan diberi badge `Direkomendasikan`, 6 bulan `Maksimal`. Efek active: navy Sleman `#02275D` dengan bayangan halus. |
| **Estimasi Rentang Magang** | Kotak dashed border sederhana yang muncul di bawah chip durasi. | Preview Banner Beraksen (*Timeline Preview*): Menampilkan tanggal mulai s/d tanggal selesai secara jelas (*misal: 01 Des 2026 – 28 Feb 2027 • 3 Bulan*) dengan latar belakang lembut dan ikon kalender. |
| **Footer & Tombol Aksi** | Tombol standar Batal dan Daftar Sekarang. | Tombol aksi terstruktur: Tombol "Batal" (Ghost/Secondary) dan tombol "Daftar Sekarang" (Primary Sleman Navy `#02275D` dengan efek hover dan loading state spinner saat diklik). |
| **Aksesibilitas & Responsif** | Hanya click button close. | Support keyboard `Esc` untuk menutup, klik area luar backdrop untuk menutup, animasi buka halus (*fade & scale up*), dan padding adaptif di layar smartphone. |

---

## 3. Rencana Implementasi Bertahap

### Tahap 1: Pembentukan Styling Terpusat di CSS ([resources/css/layouts/dashboard-cockpit.css](file:///d:/MAGKOM/MAGKOM/resources/css/layouts/dashboard-cockpit.css))
1. Menambahkan class-class styling terstruktur untuk modal pendaftaran:
   - `.pendaftaran-modal-backdrop`: Backdrop blur modern dengan `backdrop-filter: blur(6px)` dan animasi fade-in.
   - `.pendaftaran-modal-card`: Container dialog dengan `border-radius: 18px`, `max-width: 580px`, bayangan halus `0 20px 40px -10px rgba(2, 39, 93, 0.25)`, dan scroll internal jika layar kecil.
   - `.pendaftaran-modal-header`: Header dengan icon badge, judul, dan tombol close bulat.
   - `.pendaftaran-summary-strip`: Strip kartu identitas pendaftar dengan aksen lembut.
   - `.pendaftaran-duration-grid` & `.pendaftaran-duration-chip`: Chip durasi interaktif dengan hover dan state active.
   - `.pendaftaran-timeline-preview`: Kotak estimasi pelaksanaan beraksen navy-gold.
   - `.pendaftaran-modal-footer`: Area tombol aksi bawah.
2. Mematuhi aturan proyek: Semua styling berada di `resources/css/` dan tidak menulis `<style>` inline berlebihan di Blade.

### Tahap 2: Refactor Struktur Markup Blade ([resources/views/peserta/pendaftaran/index.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/pendaftaran/index.blade.php))
1. Merapikan elemen modal dialog `#pendaftaranModal` menggunakan class semantic baru.
2. Menyajikan informasi profil pendaftar (nama, institusi, jurusan, jenjang, bidang penempatan) dengan visual hirarki yang bersih dan mudah dibaca.
3. Memastikan input tersembunyi `bulan_magang_id` dan `durasi_bulan` tetap sinkron dan valid untuk submission form ke `PendaftaranController@store`.

### Tahap 3: Penyempurnaan Skrip Interaktivitas Modal
1. Memperbarui fungsi JavaScript:
   - `openModalPendaftaran()` dan `openModalWithKuota(kuotaId)`.
   - `closeModalPendaftaran()` dengan event listener `Escape` dan backdrop click.
   - `selectDurationChip(months)` dengan animasi aktif dan pembaruan estimasi tanggal instan.
   - `updateEstimasiPeriode()` untuk memformat tanggal Indonesia yang rapi (*misal: "01 Desember 2026 s/d 28 Februari 2027 (3 Bulan)"*).
   - `handleFormSubmit()` dengan animasi loading button.

### Tahap 4: Build Asset & Quality Gate
1. Jalankan build Vite:
   ```powershell
   npm run build
   ```
2. Bersihkan view cache:
   ```powershell
   php artisan view:clear
   ```
3. Jalankan pengujian otomatis untuk memastikan fungsionalitas pendaftaran tidak terganggu:
   ```powershell
   php artisan test tests/Feature/PendaftaranPesertaTest.php tests/Feature/PesertaFullPortalFeatureTest.php
   ```
4. Audit format dengan Laravel Pint:
   ```powershell
   ./vendor/bin/pint --test
   ```

---

## 4. Konfirmasi & Masukan Pengguna
- Apakah ada informasi spesifik tambahan yang ingin ditampilkan di dalam popup (misalnya ketentuan jam kerja magang atau pernyataan komitmen)?
- Jika rencana di atas sudah sesuai, silakan berikan instruksi **"implementasikan"** untuk mulai mengeksekusi perubahan.

