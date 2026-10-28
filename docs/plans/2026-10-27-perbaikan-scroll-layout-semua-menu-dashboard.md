# [PLAN] Perbaikan Masalah Scroll Atas-Bawah di Seluruh Halaman Menu Dashboard MagKom

---

## 1. Identifikasi Masalah & Akar Penyebab (Root Cause Analysis)

### Mengapa Semua Menu di Dashboard Tidak Bisa Di-scroll?
Berdasarkan investigasi mendalam terhadap struktur markup dan CSS di [resources/views/layouts/dashboard.blade.php](file:///d:/laragon/www/MAGKOM/resources/views/layouts/dashboard.blade.php) serta [public/css/gentelella-theme.css](file:///d:/laragon/www/MAGKOM/public/css/gentelella-theme.css):

1. **Tag HTML Unclosed / Tag Bocor pada Topbar (`top_nav`)**:
   - Di baris 2955, tag pembungkus topbar dibuka:
     ```html
     <div class="top_nav dashboard-topbar-wrapper">
         <header class="nav_menu dashboard-topbar">
             ...
         </header>
         <!-- BUG: Tag </div> penutup untuk top_nav TIDAK ADA di sini! -->
     ```
   - Akibat tidak adanya `</div>` penutup setelah `</header>`, seluruh elemen konten halaman `<main class="right_col ...">` dan `<footer>` **tertelan secara tidak sengaja masuk ke dalam elemen `<div class="top_nav">`**.
   - Di CSS, class `.top_nav` memiliki deklarasi:
     ```css
     .top_nav {
         height: var(--topbar-height); /* 64px */
         position: sticky;
         top: 0;
         z-index: 40;
     }
     ```
   - Karena kontainer `<main>` berada di dalam `.top_nav` yang tingginya dikunci hanya `64px`, browser memperlakukan seluruh isi halaman sebagai konten yang terperangkap dalam kontainer topbar 64px, sehingga perhitungan tinggi dokumen rusak dan **halaman di semua menu (Verifikasi Berkas, Manajemen Kuota, Waiting List, Laporan, dll.) tidak bisa di-scroll ke bawah sama sekali**.

2. **Konflik Display Flex Row pada Kontainer Induk (`.main_container` & `.dashboard-container`)**:
   - Class `.main_container` dan `.dashboard-container` memiliki deklarasi `display: flex;` tanpa `flex-direction: column;` (sehingga secara default bernilai `flex-direction: row;`).
   - Ketika elemen ditata secara horizontal (*row*), konten utama dan topbar bersaing dalam satu baris fleksibel, yang membatasi pemanjangan vertikal konten halaman secara alami.

3. **Scrollbar Sidebar Tersembunyi Total (`scrollbar-width: none`)**:
   - Di navigasi sidebar, jika resolusi layar pengguna pendek (misal laptop 1366x768 atau zoom 125%), daftar menu yang panjang (11 menu + header + status footer) terpotong dan tidak menampilkan indikator scrollbar karena `scrollbar-width: none;` dan `::-webkit-scrollbar { display: none; }`.

---

## 2. Pendekatan Teknis & Evaluasi Alternatif

| Aspek | Opsi A: Restrukturisasi Menyeluruh ke Standar Alami Gentelella (Direkomendasikan) | Opsi B: Membungkus Main Content dalam Overflow Kontainer Mandiri |
| :--- | :--- | :--- |
| **Deskripsi** | 1. Tutup tag `</div>` untuk `top_nav` tepat setelah `</header>`.<br>2. Jadikan `.main_container` dan `.dashboard-container` bertata letak kolom (`flex-direction: column` atau standar blok Gentelella) agar `top_nav`, `main`, dan `footer` mengalir vertikal.<br>3. Izinkan `html` dan `body` melakukan scroll alami dokumen (`overflow-y: auto;`). | Menjaga `top_nav` dan `body` tetap terkunci, lalu memberikan `height: calc(100vh - 64px); overflow-y: auto;` pada elemen `<main id="mainContent">`. |
| **Kelebihan** | - Menghasilkan scroll halaman yang halus, alami di semua browser dan perangkat sentuh.<br>- Kompatibel 100% dengan sticky header, pencarian `Ctrl + F`, dan penanda scroll dokumen.<br>- Struktur HTML menjadi valid (tidak ada tag bocor). | Topbar tetap terkunci tanpa memodifikasi struktur flex kontainer induk. |
| **Kekurangan** | Memerlukan verifikasi teliti pada margin-left dan lebar kanvas agar tidak menabrak sidebar tetap (*fixed sidebar*). | - Menghasilkan *double scrollbar* buruk.<br>- Menu dropdown atau modal popup di dalam tabel bisa terpotong oleh `overflow: auto` lokal.<br>- Pengalaman scrolling di perangkat sentuh/trackpad terasa kaku. |
| **Keputusan** | **Dipilih Opsi A (Rekomendasi)** karena menyelesaikan akar masalah secara arsitektural dan memastikan standar HTML valid. | Ditolak karena menimbulkan masalah UX *double scrollbar* dan *clipping*. |

---

## 3. Rencana Perubahan Berkas (Files to Modify)

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [resources/views/layouts/dashboard.blade.php](file:///d:/laragon/www/MAGKOM/resources/views/layouts/dashboard.blade.php) | **Modify** | 1. Menambahkan tag penutup `</div>` setelah `</header>` di topbar.<br>2. Mengatur `flex-direction: column;` pada `.main_container` dan `.dashboard-container` agar `top_nav`, `main`, dan `footer` tersusun vertikal secara semantik.<br>3. Menambahkan aturan scroll dokumen yang mulus pada `html` & `body` (`overflow-y: auto; scroll-behavior: smooth;`).<br>4. Memastikan `.sidebar-nav` memiliki scrolling yang nyaman pada layar laptop. |
| [public/css/gentelella-theme.css](file:///d:/laragon/www/MAGKOM/public/css/gentelella-theme.css) | **Modify** | Menyelaraskan selector `.main_container` agar konsisten memiliki `flex-direction: column;` dan tidak mengunci overflow vertikal. |

---

## 4. Langkah Implementasi Terstruktur

- [x] **Langkah 1: Perbaikan Struktur Tag HTML Topbar**
  - Buka [resources/views/layouts/dashboard.blade.php](file:///d:/laragon/www/MAGKOM/resources/views/layouts/dashboard.blade.php).
  - Tepat setelah penutup `</header>` (baris 3099) dan sebelum komentar `<!-- Container Konten Dinamis -->`, tambahkan tag penutup `</div>` untuk `<div class="top_nav dashboard-topbar-wrapper">`.
  - Pastikan pasangan tag pembuka dan penutup `<div>` pada kontainer utama berimbang (`container body` -> `main_container` -> ditutup rapi di akhir).

- [x] **Langkah 2: Penyelarasan Tata Letak Vertikal Kontainer Utama**
  - Perbarui CSS `.main_container` dan `.dashboard-container` di `dashboard.blade.php` dan `gentelella-theme.css`:
    ```css
    .main_container,
    .dashboard-container {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        width: 100%;
        position: relative;
    }
    ```
  - Pastikan `html` dan `body` memiliki:
    ```css
    html {
        overflow-y: auto;
        scroll-behavior: smooth;
    }
    body {
        overflow-x: hidden;
        min-height: 100vh;
    }
    ```

- [x] **Langkah 3: Penyelarasan Posisi & Margin Elemen Aliran Halaman**
  - Pastikan elemen `.top_nav` berada di atas dengan:
    ```css
    .top_nav {
        margin-left: var(--sidebar-width);
        width: calc(100% - var(--sidebar-width));
        height: var(--topbar-height);
        position: sticky;
        top: 0;
        z-index: 40;
    }
    ```
  - Pastikan `.right_col.dashboard-main` mengalir di bawah `.top_nav`:
    ```css
    .right_col {
        margin-left: var(--sidebar-width);
        width: calc(100% - var(--sidebar-width));
        min-height: calc(100vh - var(--topbar-height) - 50px);
        flex: 1;
    }
    ```
  - Pastikan `.gentelella-footer` mengalir di bagian paling bawah halaman:
    ```css
    .gentelella-footer {
        margin-left: var(--sidebar-width);
        width: calc(100% - var(--sidebar-width));
        flex-shrink: 0;
    }
    ```

- [x] **Langkah 4: Penyempurnaan Scroll Navigasi Sidebar**
  - Pastikan `.sidebar-nav` di sidebar dapat di-scroll dengan roda mouse/trackpad jika daftar menu melebihi tinggi layar:
    ```css
    .sidebar-top-container {
        display: flex;
        flex-direction: column;
        flex: 1;
        min-height: 0;
        overflow: hidden;
    }
    .sidebar-nav {
        flex: 1;
        overflow-y: auto;
        min-height: 0;
    }
    ```

- [x] **Langkah 5: Kompilasi Ulang Aset & Quality Gates**
  - Jalankan `npm run build` untuk memastikan seluruh aset Vite terkompilasi segar.
  - Jalankan `./vendor/bin/pint --test` untuk memastikan kepatuhan standar format kode.
  - Jalankan pengujian otomatis:
    - `php artisan test --filter=GentelellaDashboardRevampTest`
    - `php artisan test --filter=BrandingDesignSystemTest`
    - `php artisan test --filter=Dashboard`

---

## 5. Strategi Pengujian (Testing Strategy)

1. **Uji Fungsional Navigasi & Scroll di Browser**:
   - Halaman **Dashboard Operasional** (`/admin/dashboard`): scroll ke bawah hingga kartu metrik dan footer terlihat jelas.
   - Halaman **Verifikasi Berkas** (`/admin/pendaftaran`): scroll ke bawah tabel daftar peserta hingga pagination dan aksi terbawah.
   - Halaman **Manajemen Kuota** (`/admin/kuota`): scroll ke bawah daftar kartu bidang dan kuota.
   - Halaman **Waiting List** (`/admin/waiting-list`), **Penempatan** (`/admin/penempatan`), dan **Laporan** (`/admin/laporan`).
2. **Uji Otomatis (PHPUnit/Pest)**:
   - Memastikan 15 test di `GentelellaDashboardRevampTest` tetap **100% PASS** (151 assertions).
   - Memastikan tidak ada regresi pada assertion tag kerangka layout Gentelella (`container body`, `main_container`, `col-md-3 left_col`, `top_nav`, `right_col`, `gentelella-footer`).

---

## 6. Kriteria Selesai (Definition of Done)
- [x] Tag `</div>` penutup topbar telah diletakkan dengan benar dan struktur DOM HTML valid.
- [x] Halaman konten di seluruh menu sidebar dapat di-scroll dari atas ke bawah dengan mulus.
- [x] Sidebar tetap menempel rapi di sisi kiri (*fixed*) dan navigasi menu di dalamnya dapat di-scroll jika layar pendek.
- [x] Topbar tetap berada di bagian atas dan footer berada di akhir konten.
- [x] Seluruh pengujian otomatis `php artisan test --filter=GentelellaDashboardRevampTest` dan `Dashboard` lulus 100% tanpa regresi.
- [x] Kode bersih dari error linting (`./vendor/bin/pint --test` = Passed).
