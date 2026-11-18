# Rencana Kerja: Sinkronisasi Toggle Sidebar (Esc & Tombol Navigasi Hamburger) pada Dashboard Superadmin, Admin, dan Pembimbing

> **Status:** Draft / Menunggu Persetujuan User  
> **Target Rilis:** Baseline Stabilitas Layout & Interaktivitas Navigasi MagKom  
> **Dokumen Terkait:** `docs/01-PRD-MagKom.md`, `docs/07-DESIGN-SYSTEM-MAGKOM.md`, `docs/plans/2026-11-18-perbaikan-sidebar-menu-terpotong.md`  

---

## 1. Tujuan
Memperbaiki alur toggle sidebar pada 3 dashboard peran internal (**Superadmin**, **Admin/Administrasi**, dan **Pembimbing**) agar ketika pengguna menekan tombol **Esc** (sidebar menciut menjadi mode mini `nav-sm`), lalu menekan tombol **navigasi hamburger di topbar**, sidebar dapat kembali membesar secara normal dan utuh (`nav-md` 230px) seperti pada Foto 1, tanpa mengalami kerusakan layout deformasi hibrida (ikon menciut tanpa label, teks submenu mencuat keluar) seperti pada Foto 2.

---

## 2. Temuan & Root Cause Analysis (RCA)

### Bukti Diagnostik Tangkapan Layar:
- **Foto 1 (Normal `nav-md`)**: Sidebar tampil penuh 230px dengan logo MAGKOM, teks menu, label modul, dan tombol hamburger topbar `[ ☰ ]`.
- **Foto 2 (Kondisi Rusak / Deformasi Hibrida)**:
  - Lebar sidebar kembali ke 230px, tetapi elemen-elemen di dalamnya berada dalam kondisi rusak:
    1. Lambang Sleman berada di tengah, teks `MAGKOM` hilang.
    2. Menu `Dashboard` menjadi tombol kotak 44px kuning di tengah tanpa teks label.
    3. Label modul (`MODUL OPERASIONAL`, `SISTEM`) hilang total.
    4. Menu item tunggal (`Data Staf`, `Hak Akses`, `Audit Log`, `Backup Database`) teksnya hilang total dan ikonnya memusat di tengah.
    5. Tombol grup akordeon (`Administrasi >`, `Pembimbing >`, `Peserta >`, `Konfigurasi >`) teks dan chevrons-nya justru **tetap muncul dan mencuat keluar**.

### Akar Masalah Teknis (Global Event Listener Leak & Class Contamination):
1. **Kebocoran Event Listener Global di `peserta-layout.js`**:
   Di [peserta-layout.js](file:///g:/MAGKOM/resources/js/layouts/peserta-layout.js#L114-L119):
   ```javascript
   document.addEventListener('keydown', (e) => {
       if (e.key === 'Escape' || e.key === 'Esc') {
           closeProfileMenu();
           toggleSidebarPeserta(false);
       }
   });
   ```
   Listener ini didaftarkan secara **global** tanpa pengecekan apakah rute aktif adalah `/peserta*`. Akibatnya, saat pengguna berada di halaman Superadmin, Admin, atau Pembimbing dan menekan tombol `Esc`:
   - `peserta-layout.js` ikut terpanggil dan memanggil `toggleSidebarPeserta(false)`.
   - Fungsi tersebut menambahkan class `sidebar-collapsed` ke elemen `document.documentElement` (`<html>`).
   - Di saat yang sama, `dashboard.js` mengubah class `body` dan `html` dari `nav-md` ke `nav-sm`.

2. **Kegagalan Pembersihan State saat Tombol Navigasi Diklik**:
   Ketika pengguna mengklik tombol hamburger navigasi topbar (`#menu_toggle` / `data-action="toggle-sidebar"`):
   - `dashboard.js` menjalankan fungsi `toggleSidebar()` dan mengalihkan `nav-sm` kembali ke `nav-md` pada `body` dan `html`.
   - Namun, class `sidebar-collapsed` yang ditanam oleh `peserta-layout.js` **tidak pernah dibersihkan** dari `<html>`.

3. **Kontaminasi Aturan CSS dari `peserta-layout-tokens.css`**:
   File `resources/css/app.css` mengimpor `resources/css/layouts/peserta-layout-tokens.css`. Di dalamnya terdapat aturan:
   ```css
   html.sidebar-collapsed .sidebar-brand-text,
   html.sidebar-collapsed .nav-section-label,
   html.sidebar-collapsed .nav-item-link span:not(.badge-locked):not(.nav-item-icon) {
       display: none !important;
   }
   html.sidebar-collapsed .nav-item-link {
       justify-content: center !important;
       width: 44px !important;
       margin: 0.25rem auto !important;
   }
   ```
   Karena portal Peserta tidak memiliki elemen `.nav-group-btn` (akordeon Administrasi, Pembimbing, Konfigurasi), aturan `html.sidebar-collapsed` **hanya menyembunyikan elemen `.nav-item-link` dan teks brand**, sementara tombol grup akordeon dibiarkan utuh.
   Akibatnya, saat sidebar membesar ke 230px (`nav-md`), aturan `html.sidebar-collapsed` tetap membungkam teks `Dashboard`, teks `MAGKOM`, dan item-item sistem, sehingga menghasilkan tampilan cacat seperti pada **Foto 2**.

---

## 3. Evaluasi Opsi Arsitektur

| Aspek | Opsi A: Isolasi Ketat Jalur URL & Sanitasi Class (Rekomendasi) | Opsi B: Menggabungkan Seluruh Handler Peserta ke dalam `dashboard.js` |
| :--- | :--- | :--- |
| **Deskripsi** | 1. Berikan *route guard* ketat pada `peserta-layout.js` agar hanya merespons saat berada di URL `/peserta*`.<br>2. Pada `dashboard.js`, pastikan class `sidebar-collapsed` otomatis dicabut (*sanitized*) saat toggle sidebar berlangsung.<br>3. Lingkup (*scope*) aturan `sidebar-collapsed` di CSS agar hanya menargetkan `#sidebarNav` milik peserta. | Menghapus file `peserta-layout.js` dan memusatkan seluruh logika sidebar ke `dashboard.js`. |
| **Kelebihan** | Mempertahankan isolasi arsitektur Peserta yang sudah stabil dan bekerja dengan baik (seperti konfirmasi user: *"untuk bagian peserta udah bisa"*), tanpa risiko regresi pada portal peserta. | Satu file kontrol untuk semua peran. |
| **Kekurangan** | Memerlukan sinkronisasi sanitasi class antar-skrip. | Risiko regresi tinggi pada alur peserta yang sudah stabil dan memiliki banyak pengujian spesifik. |
| **Rekomendasi** | **Direkomendasikan (Terpilih).** | Tidak disarankan. |

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/js/layouts/peserta-layout.js` | Modify | Tambahkan guard `if (!/^\/peserta($\|\/)/i.test(window.location.pathname))` pada seluruh event listener (`keydown`, `click`, `resize`) sehingga logika peserta tidak pernah bocor ke Superadmin, Admin, atau Pembimbing. |
| `resources/js/layouts/dashboard.js` | Modify | 1. Tambahkan guard `/^\/peserta/` pada event listener `keydown`.<br>2. Di dalam `toggleSidebar()`, tambahkan sanitasi mutlak: selalu hapus `sidebar-collapsed` dari `document.documentElement` dan `document.body` saat switching `nav-sm` ke `nav-md`.<br>3. Di `DOMContentLoaded`, bersihkan `sidebar-collapsed` jika berada di portal internal. |
| `resources/css/layouts/peserta-layout-tokens.css` | Modify | Batasi selektor `html.sidebar-collapsed` agar hanya berlaku di dalam kontainer sidebar peserta (`#sidebarNav` atau `aside.sidebar`) sehingga tidak dapat merusak `#mainSidebar` dashboard internal. |
| `tests/Feature/DashboardLayoutTest.php` | Modify | Tambahkan unit test untuk memvalidasi transisi state: Normal (`nav-md`) -> Tekan Esc (`nav-sm`) -> Klik Tombol Toggle -> Kembali Normal (`nav-md`), serta memastikan class `sidebar-collapsed` tidak bocor ke peran internal. |

---

## 5. Langkah Implementasi

- [x] **Langkah 1: Pengamanan Route Guard di `resources/js/layouts/peserta-layout.js`**
  - Pastikan listener `keydown` (Esc), `click`, dan `resize` di `peserta-layout.js` hanya dieksekusi jika `window.location.pathname` berawalan `/peserta`.
  - Jika bukan rute peserta, lakukan pembersihan pencegahan `document.documentElement.classList.remove('sidebar-collapsed')`.

- [x] **Langkah 2: Penyempurnaan `toggleSidebar()` dan Keydown di `resources/js/layouts/dashboard.js`**
  - Pada listener `keydown` di `dashboard.js`, tambahkan guard abaikan jika di rute peserta.
  - Pada fungsi `toggleSidebar()`:
    - Saat berpindah ke `nav-md` (membesar kembali), pastikan:
      ```javascript
      body.classList.remove('nav-sm');
      body.classList.add('nav-md');
      html.classList.remove('nav-sm', 'sidebar-collapsed');
      html.classList.add('nav-md');
      ```
    - Saat berpindah ke `nav-sm` (menciut), pastikan `sidebar-collapsed` juga dicabut agar tidak terjadi tabrakan CSS.

- [x] **Langkah 3: Scoping CSS di `resources/css/layouts/peserta-layout-tokens.css`**
  - Persempit aturan `html.sidebar-collapsed` agar hanya mempengaruhi `#sidebarNav`:
    ```css
    html.sidebar-collapsed #sidebarNav .sidebar-brand-text,
    html.sidebar-collapsed #sidebarNav .nav-section-label,
    html.sidebar-collapsed #sidebarNav .nav-item-link span:not(.badge-locked):not(.nav-item-icon) {
        display: none !important;
    }
    ```

- [x] **Langkah 4: Kompilasi Aset Frontend dengan Vite**
  - Jalankan `npm run build` untuk menghasilkan bundel manifest dan JS/CSS produksi terbaru.

- [x] **Langkah 5: Pengujian Otomatis & Regression Check**
  - Tambahkan assertion pada `tests/Feature/DashboardLayoutTest.php` untuk memvalidasi isolasi state Esc dan toggle hamburger pada Superadmin, Admin, dan Pembimbing.
  - Jalankan `php artisan test --filter=DashboardLayoutTest`.
  - Jalankan `php artisan test --filter=GentelellaDashboardRevampTest`.

- [x] **Langkah 6: Verifikasi Manual Interaksi**
  - Uji alur di browser pada dashboard Superadmin, Admin, dan Pembimbing:
    1. Masuk ke dashboard (kondisi awal: sidebar normal/besar).
    2. Tekan tombol `Esc` -> sidebar menciut rapi (`nav-sm`).
    3. Klik tombol hamburger topbar `[ ☰ ]` -> sidebar kembali membesar penuh (`nav-md`) dengan semua label teks utuh sempurna (paritas Foto 1).
  - Pastikan dashboard Peserta tetap berjalan normal tanpa gangguan.

---

## 6. Strategi Testing
1. **Automated Tests**:
   - `php artisan test --filter=DashboardLayoutTest`
   - `php artisan test --filter=GentelellaDashboardRevampTest`
2. **Behavioral Integrity**:
   - Memastikan toggle via tombol hamburger di topbar dan via tombol Esc berjalan simetris 2 arah (Expand <-> Collapse) tanpa meninggalkan residu CSS class.
   - Memastikan tidak ada error JavaScript di console browser.

---

## 7. Risiko & Mitigasi
- **Risiko:** Perubahan pada `peserta-layout.js` berpotensi mengganggu fungsi toggle pada Peserta.  
  **Mitigasi:** Logika internal Peserta tidak diubah sama sekali; perubahan murni menambahkan pembatas (*guard boundary*) `/^\/peserta/` agar tidak bocor ke peran lain.

---

## 8. Kriteria Selesai (Definition of Done)
- [x] Tekan Esc pada Superadmin, Admin, dan Pembimbing membuat sidebar menciut (`nav-sm`) secara rapi.
- [x] Klik tombol hamburger navigasi topbar setelah Esc langsung mengembalikan sidebar ke ukuran normal penuh (`nav-md`) dengan seluruh teks label tampil utuh (Foto 1).
- [x] Deformasi hibrida pada Foto 2 tidak terjadi lagi.
- [x] Portal Peserta tetap berfungsi normal seperti sebelumnya.
- [x] Kompilasi `npm run build` dan PHPUnit test suite lolos 100%.
