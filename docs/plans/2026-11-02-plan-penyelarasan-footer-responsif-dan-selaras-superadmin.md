# Rencana Kerja: Penyelarasan & Fleksibilitas Footer Dashboard (Paritas SuperAdmin)

- **Tanggal Pembuatan**: 2026-11-02
- **Penulis**: AI Assistant (Pair Programming Antigravity)
- **Status**: Menunggu Konfirmasi Pengguna

---

## 1. Tujuan
Mewujudkan footer yang rapi, modern, fleksibel, dan adaptif pada seluruh dashboard (Peserta, Pembimbing, Admin, dan SuperAdmin) dengan skema warna, tipografi, dan perilaku responsif yang seragam 1:1 dengan dashboard SuperAdmin.

---

## 2. Asumsi & Analisis Saat Ini

### Asumsi Teknis
1. **Warna & Token SuperAdmin**:
   - Background: `#003566` (Navy Sleman khas MagKom, persisten di mode terang maupun mode gelap).
   - Border Atas: `1px solid #2C3A4E`.
   - Warna Teks: `#94A3B8` (Slate subdued).
   - Tipografi: `0.75rem` (12px), `font-weight: 500`.
   - Elevasi / Shadow: `0 -2px 10px rgba(0, 0, 0, 0.08)`.
2. **Struktur Layout Berbeda**:
   - Di `layouts/dashboard.blade.php` (SuperAdmin, Admin, Pembimbing): Footer berada sejajar di bawah `right_col` dan menggunakan `margin-left: var(--sidebar-width)` dengan transisi saat sidebar ciut (`.nav-sm`).
   - Di `layouts/peserta.blade.php` (Peserta): Footer berada di dalam `.main-wrapper` (yang sudah memiliki `margin-left`), sehingga footer di Peserta harus menggunakan `width: 100%` dengan `margin-left: 0` agar tidak terjadi pergeseran (*double margin*).
3. **Kebutuhan Fleksibilitas**:
   - Di layar Desktop (> 992px): Konten footer rata kanan (`justify-content: flex-end`) atau terdistribusi rapi.
   - Di layar Mobile/Tablet (≤ 991.98px): Konten footer otomatis berpusat (`justify-content: center; text-align: center;`), padding menyesuaikan (`0.75rem 1rem`), dan membungkus secara fleksibel (*flex-wrap: wrap*) tanpa menyebabkan *horizontal scrollbar*.

---

## 3. Evaluasi Opsi Pendekatan Teknis

| Parameter | Opsi A: CSS Flexbox Modern Terpadu (Direkomendasikan) | Opsi B: Floats Tradisional (`.pull-right` & `.clearfix`) |
| :--- | :--- | :--- |
| **Deskripsi** | Mengganti float usang dengan CSS Flexbox modern (`display: flex; justify-content: flex-end; align-items: center; flex-wrap: wrap`) baik di `layouts.dashboard` maupun `layouts.peserta`. | Mempertahankan `<div class="pull-right">` dan `<div class="clearfix">` lalu menimpa inline margin. |
| **Kelebihan** | 1. 100% fleksibel di semua resolusi (desktop, tablet, mobile).<br>2. Tidak rentan terhadap *clearing bug* atau *overflow shift*.<br>3. Mendukung penambahan elemen baru (misal status sistem/versi) secara otomatis dan rapi. | Meminimalkan perubahan struktur HTML lama. |
| **Kekurangan** | Perlu sedikit refactor kelas wrapper di blade view. | Tidak fleksibel di layar kecil, rentan teks bertumpuk atau keluar layar jika panjang teks dinamis. |
| **Keputusan** | **Dipilih Opsi A** karena menjamin kerapian, keandalan, dan responsivitas modern di berbagai ukuran layar. | Ditinggalkan. |

---

## 4. Rincian Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/layouts/peserta.blade.php` | Modify | Selaraskan CSS `.gentelella-footer` dan `.peserta-footer`: palet `#003566`, border `#2C3A4E`, box-shadow, flexbox responsif di mobile (`@media (max-width: 991.98px)`), dan pembersihan sisa float. |
| `resources/views/layouts/dashboard.blade.php` | Modify | Pastikan styling footer di SuperAdmin tetap prima, perkuat flexbox responsif pada breakpoint mobile, dan pastikan transisi `nav-sm` tetap mulus. |
| `public/css/gentelella-theme.css` | Modify | Update styling master `.gentelella-footer` agar konsisten mendukung flexbox modern dan palet resmi MagKom. |

---

## 5. Langkah Implementasi Bertahap

### Fase 1: Standarisasi Desain Token & CSS Footer
- [x] **1.1** Tambahkan variabel / utility token footer di `public/css/gentelella-theme.css`:
  - Background `#003566 !important`
  - Border-top `1px solid #2C3A4E !important`
  - Teks `#94A3B8 !important`
  - Font size `0.75rem`, weight `500`
  - Box shadow `0 -2px 10px rgba(0, 0, 0, 0.08)`
- [x] **1.2** Terapkan aturan Flexbox pada `.gentelella-footer`:
  - `display: flex; align-items: center; justify-content: flex-end; flex-wrap: wrap; gap: 0.75rem;`
  - Pada breakpoint mobile (`max-width: 991.98px`): `justify-content: center; text-align: center; padding: 0.75rem 1rem;`

### Fase 2: Penyelarasan Layout Peserta (`layouts/peserta.blade.php`)
- [x] **2.1** Rapikan markup footer di `resources/views/layouts/peserta.blade.php` agar menggunakan flex container bersih (menghilangkan wrapper `.pull-right` float lama).
- [x] **2.2** Pastikan `margin-left: 0; width: 100%;` diatur spesifik untuk Peserta karena sudah berada di dalam `.main-wrapper`.
- [x] **2.3** Pastikan dark mode dan light mode memiliki visual yang identik (paritas dark navy dengan SuperAdmin).

### Fase 3: Penyelarasan Layout Dashboard Utama (`layouts/dashboard.blade.php`)
- [x] **3.1** Selaraskan markup footer di `resources/views/layouts/dashboard.blade.php` dengan struktur flexbox yang sama.
- [x] **3.2** Verifikasi penyesuaian lebar saat sidebar menciut (`body.nav-sm`) dan saat mode mobile drawer aktif.

### Fase 4: Pengujian & Validasi
- [x] **4.1** Bersihkan cache view: `php artisan view:clear`.
- [x] **4.2** Jalankan test suite layout dan dashboard:
  - `php artisan test tests/Feature/DashboardLayoutTest.php`
  - `php artisan test tests/Feature/GentelellaDashboardRevampTest.php`
- [x] **4.3** Verifikasi visual di browser (Desktop 1920x1080, Tablet 768px, Mobile 375px) baik di dashboard SuperAdmin (`/superadmin/dashboard`) maupun Peserta (`/peserta/dashboard`).

---

## 6. Strategi Testing

1. **Automated Feature Tests**:
   - `DashboardLayoutTest`: Memastikan footer tetap merender string yang di-assert oleh unit test (`Portal MagKom • Dinas Komunikasi dan Informatika Kabupaten Sleman`).
   - `GentelellaDashboardRevampTest`: Memastikan komponen struktur Gentelella tidak mengalami regresi.
2. **Manual Visual / Responsiveness Check**:
   - Uji pergeseran sidebar saat di-*toggle* (expand & collapse).
   - Uji resolusi mobile untuk memastikan tidak ada *overflow-x* atau garis horizontal yang pecah.
   - Uji kontras warna teks `#94A3B8` di atas background `#003566` (memenuhi standar WCAG AA).

---

## 7. Risiko & Mitigasi

| Risiko Potensial | Mitigasi |
| :--- | :--- |
| Teks assert unit test hilang jika markup diubah | Pertahankan teks persis `Portal MagKom &bull; Dinas Komunikasi dan Informatika Kabupaten Sleman` di dalam `<span>` footer. |
| Footer melayang di tengah layar jika konten halaman sangat sedikit | Pasang `min-height: 100vh` pada wrapper utama dan `margin-top: auto` pada footer (Sticky Footer pattern). |
| Konflik margin pada layout yang berbeda | Spesifikasikan `margin-left` hanya pada layout yang membutuhkan (dashboard master vs peserta). |

---

## 8. Kriteria Selesai (Definition of Done)
- [x] Warna background `#003566`, border `#2C3A4E`, dan teks `#94A3B8` seragam 100% di dashboard SuperAdmin dan Peserta.
- [x] Footer fleksibel, rapi, dan tidak menimbulkan *horizontal scroll* di perangkat mobile maupun desktop.
- [x] Saat sidebar di-expand/collapse, footer menyesuaikan posisi dengan transisi mulus.
- [x] Seluruh test suite PHPUnit lulus 100% tanpa regresi.
