# Rencana Kerja: Perbaikan Item Menu Sidebar Terpotong / Hilang (Sidebar Height & Flex Truncation)

> **Status:** Draft / Menunggu Persetujuan User  
> **Target Rilis:** Baseline Stabilitas Layout Cockpit MagKom  
> **Dokumen Terkait:** `docs/01-PRD-MagKom.md`, `docs/07-DESIGN-SYSTEM-MAGKOM.md`  

---

## 1. Tujuan
Memperbaiki masalah pada sidebar navigasi utama (Superadmin, Admin, Pembimbing) di mana item-item menu di bawah "Pembimbing" (seperti grup "Peserta", header "SISTEM", "Data Staf", "Hak Akses", "Audit Log", "Backup Database", dan "Konfigurasi") tidak muncul atau terpotong pada ketinggian ~230px, sehingga seluruh menu navigasi dapat tampil utuh dan dapat digulir (*scrollable*) dengan mulus tanpa merusak responsivitas desktop maupun mobile.

---

## 2. Temuan & Root Cause Analysis (RCA)

### Bukti Diagnostik Tangkapan Layar:
- Pada tangkapan layar pengguna, sidebar hanya menampilkan:
  - Header MAGKOM & Logo
  - Menu `Dashboard`
  - Label `MODUL OPERASIONAL`
  - Grup `Administrasi`
  - Grup `Pembimbing`
  - Di bawah grup Pembimbing (pada koordinat vertikal y ≈ 228–231px), hanya tampak 3–4 pixel ujung atas dari ikon `Peserta`.
  - Di bawah y = 231px hingga dasar viewport (lebih dari 300px), area sidebar sepenuhnya kosong melompong (hanya warna latar biru abisal `#02275D`).

### Akar Masalah Teknis (CSS Flex Direction vs Flex Basis Collision):
1. **Struktur DOM**:
   Di [dashboard.blade.php](file:///g:/MAGKOM/resources/views/layouts/dashboard.blade.php#L103-L106):
   ```html
   <aside id="mainSidebar" class="col-md-3 left_col dashboard-sidebar" aria-label="Navigasi Utama">
       <div class="left_col scroll-view sidebar-top-container">
           ...
           <nav id="sidebar-menu" class="main_menu_side hidden-print main_menu sidebar-nav">
               <div class="menu_section"> ... </div>
           </nav>
       </div>
       <div class="sidebar-footer"> ... </div>
   </aside>
   ```
2. **Karakteristik Flex Parent**:
   Elemen `<aside id="mainSidebar">` memiliki aturan CSS:
   `display: flex; flex-direction: column; justify-content: space-between;`
   Artinya, anak langsungnya berada dalam sumbu vertikal (*column axis*).
3. **Tabrakan Selektor `.left_col`**:
   Class `.left_col` dipakai pada elemen parent `<aside>` DAN elemen anak `div.sidebar-top-container`.
4. **Shorthand `flex: 0 0 230px !important;`**:
   Di [dashboard.css](file:///g:/MAGKOM/resources/css/layouts/dashboard.css#L1034-L1042) dan [gentelella.css](file:///g:/MAGKOM/resources/css/layouts/gentelella.css#L15-L23):
   ```css
   body.nav-md #mainSidebar,
   body.nav-md .dashboard-sidebar,
   body.nav-md .left_col {
       width: 230px !important;
       max-width: 230px !important;
       min-width: 230px !important;
       flex: 0 0 230px !important;
   }
   ```
   Dalam konteks `flex-direction: column`, properti `flex-basis: 230px` (nilai ketiga dari shorthand `flex`) diterapkan pada sumbu utama (*main axis*), yang mana pada orientasi kolom adalah **TINGGI (HEIGHT)**, bukan LEBAR!
5. **Dampak Pemotongan**:
   Elemen anak `div.sidebar-top-container` yang seharusnya memiliki `flex: 1` dipaksa oleh selektor `.left_col` menjadi `flex: 0 0 230px !important;`. Ditambah dengan aturan `overflow: hidden;`, kontainer tersebut terkunci mati tepat pada ketinggian 230px. Akumulasi tinggi menu dari atas mencapai 228px tepat di atas ikon Peserta, sehingga seluruh menu selebihnya terpotong dan lenyap.

---

## 3. Evaluasi Opsi Arsitektur

| Aspek | Opsi A: Modifikasi Markup Blade (`dashboard.blade.php`) | Opsi B: Koreksi Selektor CSS & Proteksi Kontainer (Rekomendasi) |
| :--- | :--- | :--- |
| **Deskripsi** | Menghapus class `.left_col` dari elemen anak `div.scroll-view.sidebar-top-container` di Blade. | Mengisolasi selektor `body.nav-md .left_col` menjadi `aside.left_col` dan menambahkan aturan penegasan `flex: 1 1 auto !important` pada `.sidebar-top-container`. |
| **Kepatuhan Aturan Proyek** | ⚠️ Melanggar aturan ketat *Blade Read-Only Default* (AGENTS.md Section 3.2). Berisiko mempengaruhi tes integrasi yang mencari struktur warisan Gentelella. | ✅ Sepenuhnya patuh pada *One Source of Truth in resources/css/* (AGENTS.md Section 4). Blade tidak disentuh sama sekali. |
| **Ketahanan (*Resilience*)** | Rentan muncul kembali jika template turunan lain masih memakai class tersebut. | Memberikan perlindungan mutlak bagi kontainer navigasi pada semua mode desktop (`nav-md`), mini (`nav-sm`), dan mobile drawer. |
| **Rekomendasi** | Tidak disarankan. | **Direkomendasikan (Terpilih).** |

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/css/layouts/dashboard.css` | Modify | 1. Batasi cakupan selektor lebar sidebar agar hanya mengenai elemen `aside` (`body.nav-md aside.left_col` dan `body.nav-sm aside.left_col`).<br>2. Tambahkan deklarasi proteksi spesifisitas tinggi untuk `.sidebar-top-container` dan `.left_col.sidebar-top-container` (`flex: 1 1 auto !important; height: auto !important; min-height: 0 !important; width: 100% !important;`).<br>3. Pastikan `.sidebar-nav` mempertahankan `flex: 1 1 auto !important; overflow-y: auto !important;`. |
| `resources/css/layouts/gentelella.css` | Modify | Selaraskan selektor `body.nav-md aside.left_col` dan `body.nav-sm aside.left_col` serta proteksi `.sidebar-top-container` untuk paritas Gentelella v4. |
| `tests/Feature/DashboardLayoutTest.php` | Modify | Tambahkan pengujian layout integrity otomatis untuk memverifikasi bahwa `sidebar-top-container` tidak lagi terbatasi oleh aturan `flex: 0 0 230px` atau `flex: 0 0 72px`. |

---

## 5. Langkah Implementasi

- [x] **Langkah 1: Perbaikan Selektor CSS di `resources/css/layouts/dashboard.css`**
  - Ubah `body.nav-md .left_col` menjadi `body.nav-md aside.left_col, body.nav-md aside#mainSidebar.left_col`.
  - Ubah `body.nav-sm .left_col` menjadi `body.nav-sm aside.left_col, body.nav-sm aside#mainSidebar.left_col`.
  - Tambahkan deklarasi reset & proteksi flex pada `.sidebar-top-container`:
    ```css
    .sidebar-top-container,
    .left_col.sidebar-top-container {
        display: flex !important;
        flex-direction: column !important;
        flex: 1 1 auto !important;
        min-height: 0 !important;
        max-height: none !important;
        width: 100% !important;
        max-width: 100% !important;
        overflow: hidden !important;
    }
    ```
  - Pastikan `.sidebar-nav` memiliki properti:
    ```css
    .sidebar-nav,
    .main_menu_side.sidebar-nav {
        flex: 1 1 auto !important;
        min-height: 0 !important;
        max-height: none !important;
        overflow-y: auto !important;
    }
    ```

- [x] **Langkah 2: Sinkronisasi Selektor di `resources/css/layouts/gentelella.css`**
  - Sesuaikan selektor `body.nav-md .left_col` dan `body.nav-sm .left_col` menjadi spesifik untuk elemen `aside`.
  - Tambahkan aturan proteksi `.sidebar-top-container` yang sama agar konsisten saat fallback Gentelella dimuat.

- [x] **Langkah 3: Kompilasi Aset Vite**
  - Jalankan `npm run build` untuk memverifikasi tidak ada sintaks CSS yang rusak dan manifest Vite diperbarui secara bersih.

- [x] **Langkah 4: Penambahan & Eksekusi Pengujian Otomatis**
  - Tambahkan unit assertion di `tests/Feature/DashboardLayoutTest.php` untuk memvalidasi bahwa seluruh menu Superadmin (dari Dashboard hingga Konfigurasi) ter-render lengkap tanpa pemotongan.
  - Jalankan `php artisan test --filter=DashboardLayoutTest`.
  - Jalankan `php artisan test --filter=GentelellaDashboardRevampTest`.

- [x] **Langkah 5: Verifikasi Visual & Validasi Aksesibilitas**
  - Periksa rendering sidebar pada role Superadmin, Admin, Pembimbing, dan Peserta.
  - Pastikan footer toolbar (`Pengaturan Akun`, `Theme Toggle`, `Logout`) tetap tertambat (*sticky/anchored*) di bagian paling bawah.
  - Pastikan menu dapat digulir (*scrollable*) saat item menu banyak atau saat viewport pendek/resolusi layar rendah.

---

## 6. Strategi Testing
1. **Automated Feature Test**:
   - `php artisan test --filter=DashboardLayoutTest` (Semua 10+ tes harus PASS).
   - `php artisan test --filter=GentelellaDashboardRevampTest` (Semua 15 tes harus PASS).
2. **Regression Check**:
   - Memastikan tidak ada regresi pada mode desktop normal (`nav-md`), mode ciut (`nav-sm`), dan mobile drawer (`< 1024px`).
   - Memastikan token warna abisal `#02275D` dan teks putih tetap kontras sesuai WCAG 2.1 AA.

---

## 7. Risiko & Mitigasi
- **Risiko:** Perubahan selektor mempengaruhi lebar sidebar pada browser lama jika ada dependensi pada `.left_col`.  
  **Mitigasi:** Selektor `aside.left_col` tetap mempertahankan aturan lebar `width: 230px !important; max-width: 230px !important;` untuk elemen sidebar itu sendiri.
- **Risiko:** Scrollbar ganda atau scrollbar yang tidak diinginkan muncul.  
  **Mitigasi:** Aturan seamless scrollbar (`scrollbar-width: none` / `::-webkit-scrollbar { display: none; }` atau scrollbar tipis standar) tetap dipertahankan dengan rapi.

---

## 8. Kriteria Selesai (Definition of Done)
- [x] Item menu di bawah Pembimbing ("Peserta", "SISTEM", "Data Staf", "Hak Akses", "Audit Log", "Backup Database", "Konfigurasi") tampil utuh.
- [x] Kontainer `sidebar-top-container` tidak terkunci pada tinggi 230px dan dapat mengisi ruang vertikal secara fleksibel.
- [x] Kontainer `sidebar-nav` dapat di-scroll dengan lancar jika tinggi layar tidak mencukupi.
- [x] Footer toolbar navigasi (`Pengaturan`, `Theme Toggle`, `Logout`) tetap berada di posisi bawah dengan benar.
- [x] Kompilasi `npm run build` berhasil tanpa error.
- [x] Seluruh test suite PHPUnit terkait dashboard lolos 100% tanpa regresi.
