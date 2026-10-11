# Plan: Menghilangkan Scrollbar Mencolok pada Sidebar Dashboard SuperAdmin

- **Tanggal**: 2026-10-10
- **Status**: Draft (Menunggu Persetujuan)
- **Author**: Antigravity (web-planning)

---

## 1. Tujuan

Menghilangkan bilah scrollbar abu-abu tebal native Windows yang muncul di sisi kanan sidebar navigasi biru MagKom (`.dashboard-sidebar`) pada antarmuka Dashboard SuperAdmin. Tampilan sidebar akan dibuat bersih, mulus, dan elegan sesuai standar estetika modern (Figma Node 560-24892) tanpa mengorbankan aksesibilitas menu di layar beresolusi rendah.

---

## 2. Asumsi & Pertanyaan Terbuka

- **Asumsi**:
  1. Pengguna ingin bilah scrollbar abu-abu yang kontras dan tebal tersebut tidak terlihat lagi di layar desktop/laptop.
  2. Kemampuan navigasi menggulir (scrolling) tetap harus dapat dilakukan via *mouse wheel*, trackpad, atau sentuhan jika menu akordeon diperluas di layar dengan tinggi terbatas (< 850px), agar tidak ada tautan menu yang terpotong secara permanen.
  3. Header brand MagKom di bagian atas dan indikator footer "Sistem internal Kominfo" di bagian bawah sebaiknya tetap terkunci (*pinned/fixed*), sementara area daftar menu di tengah dapat bergerak dengan lancar.
- **Pertanyaan Terbuka**: Tidak ada pertanyaan kritis yang menghambat. Solusi dapat diimplementasikan langsung pada stylesheet layout blade.

---

## 3. Pendekatan Desain & Arsitektur

### Evaluasi Opsi Teknis:

| Opsi | Deskripsi | Kelebihan | Kekurangan |
| :--- | :--- | :--- | :--- |
| **Opsi 1: Seamless Invisible Scrollbar (Rekomendasi)** | Menerapkan `scrollbar-width: none;` (Firefox) dan `::-webkit-scrollbar { display: none; }` (Chromium/Safari/Edge) pada container sidebar/nav. | 1. Bilah scrollbar abu-abu hilang 100% secara visual.<br>2. Tampilan bersih dan menyatu dengan warna biru MagKom `#0641CC`.<br>3. Menu tetap dapat digulir halus menggunakan *mouse wheel* atau trackpad di layar kecil. | Tidak ada penanda posisi scroll berupa batang fisik. |
| **Opsi 2: Strict `overflow: hidden` (No-Scroll)** | Memaksa sidebar tidak dapat di-scroll sama sekali dengan `overflow: hidden`. | Scrollbar hilang 100%. | **Berbahaya**: Pada resolusi layar rendah (misal laptop 1366x768 atau saat akordeon dibuka), menu bagian bawah akan terpotong permanen dan tidak bisa diklik. |
| **Opsi 3: Ultra-Slim Translucent Scrollbar** | Scrollbar diperkecil menjadi 3px dengan thumb semi-transparan `rgba(255,255,255,0.2)`. | Posisi scroll terlihat samar. | Garis vertikal masih tampak di tepi kanan sidebar dan tidak memenuhi permintaan user yang ingin "menghilangkan scroll itu". |

### Opsi Terpilih:
**Opsi 1 (Seamless Invisible Scrollbar)** dikombinasikan dengan restrukturisasi hierarki scroll di dalam sidebar:
1. Menghilangkan visual scrollbar secara menyeluruh di Chromium (Chrome, Edge), Firefox, dan Safari.
2. Memindahkan batas scroll ke `.sidebar-nav` dengan *flex-grow*, sehingga header brand dan footer sistem tetap stabil tanpa ikut bergeser.
3. Memberikan sentuhan *compact vertical spacing* pada item menu navigasi agar di resolusi standar (tinggi >= 900px) seluruh menu utama dapat tampil utuh tanpa perlu banyak scrolling.

---

## 4. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [`resources/views/layouts/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/layouts/dashboard.blade.php) | Modify | 1. Tambahkan aturan CSS `scrollbar-width: none;` dan `::-webkit-scrollbar { display: none; width: 0; }` pada `.dashboard-sidebar` dan `.sidebar-nav`.<br>2. Jadikan `.dashboard-sidebar` memiliki `overflow-y: hidden;` sementara `.sidebar-nav` memiliki `overflow-y: auto;` dengan invisible scrollbar.<br>3. Pastikan header dan footer sidebar tetap terkunci rapi. |
| [`tests/Feature/SuperAdminFigmaDesignAlignmentTest.php`](file:///g:/MAGKOM/tests/Feature/SuperAdminFigmaDesignAlignmentTest.php) | Modify | Tambahkan assertion untuk memastikan style invisible scrollbar terpasang pada markup dashboard. |

---

## 5. Langkah Implementasi

- [x] **Langkah 1**: Perbarui CSS layout di [`resources/views/layouts/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/layouts/dashboard.blade.php):
  - Tambahkan utilitas no-scrollbar pada `.dashboard-sidebar` dan `.sidebar-nav`:
    ```css
    .dashboard-sidebar,
    .sidebar-nav {
        scrollbar-width: none; /* Firefox */
        -ms-overflow-style: none; /* IE/Edge */
    }
    .dashboard-sidebar::-webkit-scrollbar,
    .sidebar-nav::-webkit-scrollbar {
        display: none;
        width: 0;
        height: 0;
    }
    ```
  - Atur `.dashboard-sidebar` menjadi `overflow: hidden; display: flex; flex-direction: column;` sehingga pembungkus luar tidak pernah menampilkan scrollbar.
  - Atur `.sidebar-top-container` menjadi `display: flex; flex-direction: column; flex: 1; min-height: 0; overflow: hidden;` dan `.sidebar-nav` menjadi `flex: 1; overflow-y: auto; min-height: 0;`.
- [x] **Langkah 2**: Optimalkan kerapatan vertikal navigasi:
  - Sesuaikan padding pada `.sidebar-nav` dan `.nav-item-link` secara proporsional agar tetap nyaman disentuh (memenuhi target sentuh >= 40px) namun efisien secara ruang.
- [x] **Langkah 3**: Tambahkan automated test di [`tests/Feature/SuperAdminFigmaDesignAlignmentTest.php`](file:///g:/MAGKOM/tests/Feature/SuperAdminFigmaDesignAlignmentTest.php) untuk memverifikasi penghilangan visual scrollbar di sidebar.
- [x] **Langkah 4**: Jalankan automated test suite `SuperAdminFigmaDesignAlignmentTest` dan `SuperAdminUnifiedDashboardFeaturesTest` untuk memastikan tidak ada regresi.
- [x] **Langkah 5**: Lakukan verifikasi visual langsung untuk memastikan bilah scrollbar abu-abu telah lenyap dan interaktivitas akordeon tetap berfungsi sempurna.

---

## 6. Strategi Testing

1. **Automated Feature Tests**:
   - `php artisan test tests/Feature/SuperAdminFigmaDesignAlignmentTest.php` (5 passed, 46 assertions)
   - `php artisan test tests/Feature/SuperAdminUnifiedDashboardFeaturesTest.php` (8 passed, 70 assertions)
   - `php artisan test tests/Feature/SuperAdminDashboardAndAuditLogTest.php` (4 passed, 23 assertions)
   - `php artisan test tests/Feature/DashboardModulesRenderingTest.php` (3 passed, 37 assertions)
2. **Browser & Visual Verification**:
   - Buka `/superadmin/dashboard` pada viewport desktop (misal 1920x911 dan 1366x768).
   - Pastikan tidak ada bilah scrollbar abu-abu native di antara sidebar dan area konten utama.
   - Buka menu akordeon "Administrasi" dan "Konfigurasi"; pastikan menu dapat digulir dengan mouse wheel tanpa memunculkan bilah abu-abu.

---

## 7. Risiko & Mitigasi

| Potensi Risiko | Tingkat Dampak | Strategi Mitigasi |
| :--- | :---: | :--- |
| **Pengguna mouse tanpa wheel di layar kecil** | Sangat Rendah | Seluruh menu penting (Dashboard, Modul Utama, Pengguna) berada di paruh atas sidebar. Menu akordeon fleksibel dapat diciutkan/dilebarkan. |
| **Cross-browser compatibility** | Rendah | Menggunakan kombinasi properti standar W3C (`scrollbar-width: none`), prefix WebKit (`::-webkit-scrollbar`), dan Microsoft (`-ms-overflow-style: none`). |

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Bilah scrollbar abu-abu native Windows pada sisi kanan sidebar hilang total secara visual.
- [x] Header brand dan footer status sistem tetap terkunci kokoh pada posisinya.
- [x] Navigasi tetap dapat digulir dengan mulus menggunakan mouse wheel / trackpad jika sub-menu dibuka penuh.
- [x] Seluruh automated test berjalan sukses 100% tanpa regresi (20 passed, 176 assertions).
- [x] Verifikasi browser membuktikan tampilan sidebar mulus dan elegan.

---

## 9. Catatan Perubahan & Saran Lanjutan

- **Perubahan selama pengerjaan**:
  - Implementasi teknik `scrollbar-width: none` dan `::-webkit-scrollbar { display: none; }` diterapkan pada `.dashboard-sidebar` dan `.sidebar-nav`.
  - Struktur flexbox internal sidebar dioptimalkan dengan `min-height: 0` dan `flex-shrink: 0` pada header dan footer untuk menjaga stabilitas elemen brand dan status sistem.
- **Saran Lanjutan (Out of Scope)**:
  - Menambahkan pengaturan auto-collapse akordeon (hanya 1 sub-menu yang terbuka dalam satu waktu) jika pengguna menginginkan perilaku akordeon yang lebih eksklusif.
