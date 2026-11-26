# Rencana Kerja: Penyelarasan Garis Batas Vertikal Tombol Terbitkan dan Panel Pengaturan Blok

Dokumen ini merinci rencana teknis terstruktur untuk merapikan dan menyelaraskan tata letak vertikal antara area kontrol kanan topbar (**Tombol "Terbitkan" & "Rollback"**) dengan batas garis kiri panel kanan (**Inspector "Pengaturan Blok"**) pada **Canvas Site Editor (`/superadmin/site-editor`)**.

---

## 1. Deskripsi Masalah & Root Cause Analysis (RCA)

### 1.1 Masalah Tampilan yang Dilaporkan Pengguna
Berdasarkan tangkapan layar yang dilampirkan pengguna (`media_1795696616429_9de001e3.png`), terdapat ketidakrapian dan ketidaksejajaran vertikal yang mencolok:
- **Garis Batas Panel Inspector**: Panel kanan (`.wp-site-inspector`) dengan header "Pengaturan Blok" memiliki garis batas vertikal kiri (`border-left: 1px solid var(--wp-admin-border)`).
- **Tombol Terbitkan Menyeberang Garis**: Tombol biru utama `#btn-publish` ("Terbitkan") di topbar dimulai sekitar 14px-15px di sebelah kiri garis batas panel Pengaturan Blok, sehingga menjorok/lewat keluar ke area kanvas tengah.
- **Ketiadaan Kontinuitas Garis Vertikal**: Topbar (`.wp-editor-header`) tidak memiliki garis vertikal pemisah yang menyambung lurus dengan garis batas kiri Inspector di bawahnya. Hal ini membuat tata letak terkesan terputus (*disconnected*) dan tidak presisi (*misaligned*).

### 1.2 Root Cause Analysis (Akar Masalah Teknis)
1. **Lebar Container Tidak Sinkron**:
   - Di bagian bawah, panel Inspector memiliki lebar responsif:
     ```css
     .wp-site-inspector {
         width: clamp(460px, 28vw, 540px);
         flex: 0 0 clamp(460px, 28vw, 540px);
         border-left: 1px solid var(--wp-admin-border);
     }
     ```
   - Di bagian atas, container `.wp-header-right` memiliki lebar otomatis (`width: auto`) dan mengalir dengan `display: flex; gap: 8px; justify-content: flex-end`.
2. **Posisi Elemen di Header Kanan Terlalu Lebar**:
   - Di dalam `.wp-header-right`, terdapat grup tombol:
     - `.wp-utility-group` (Tombol "Situs Live")
     - `.wp-publish-group` (Tombol "Terbitkan" & "Rollback")
     - `.wp-header-vseparator`
     - `#btn-toggle-inspector` (Tombol ikon toggle inspector)
   - Akumulasi lebar dari tombol-tombol tersebut membuat tepi kiri tombol "Terbitkan" jatuh di koordinat horizontal yang melebihi batas kiri panel Inspector, sehingga "menabrak" dan melewati garis batas tersebut.
3. **Duplikasi Tag HTML di Topbar**:
   - Pada `resources/views/superadmin/site-editor/partials/_topbar.blade.php` baris 81-88, terdapat sisa tag penutup dan SVG duplikat dari iterasi sebelumnya yang perlu dibersihkan.

---

## 2. Asumsi & Ketergantungan

1. **Konsistensi Visual Studio/Editor Modern**: Mengikuti konvensi desain WordPress Gutenberg Site Editor dan Figma, kolom aksi panel kanan di header harus menyatu secara visual dengan panel samping di bawahnya.
2. **Kepatuhan DS-MagKom & Aturan Proyek**:
   - Tidak menambahkan CSS atau JS di dalam file Blade. Seluruh penyesuaian gaya dilakukan di `resources/css/pages/superadmin/site-editor.css`.
   - Menjaga interaktivitas tombol `#btn-publish`, `#btn-rollback`, `#btn-view-live-site`, dan `#btn-toggle-inspector`.
3. **Dukungan Responsif Layar**:
   - Sinkronisasi lebar kolom kanan menggunakan token rumus yang identik: `width: clamp(460px, 28vw, 540px)`.

---

## 3. Evaluasi Opsi Pendekatan Teknis

| Parameter | Opsi A: Sinkronisasi Kolom Header Kanan + Border Vertikal Lurus (Direkomendasikan) | Opsi B: Memindahkan Tombol "Situs Live" & Manual Offset Padding |
| :--- | :--- | :--- |
| **Deskripsi** | 1. Berikan `.wp-header-right` lebar yang persis sama dengan Inspector: `width: clamp(460px, 28vw, 540px); flex: 0 0 clamp(460px, 28vw, 540px);`.<br>2. Tambahkan `border-left: 1px solid var(--wp-admin-border);` pada `.wp-header-right` sehingga garis batas dari atas (header) ke bawah (inspector) **lurus menyatu 100% (*continuous line*)**.<br>3. Pindahkan tombol `#btn-view-live-site` ("Situs Live") ke `.wp-header-center` (di samping pengalih viewport) atau tempatkan secara proporsional di dalam `.wp-header-right`. | Membiarkan container fleksibel, lalu menambahkan `margin-left` atau `padding-left` statis pada `.wp-publish-group` agar tombol Terbitkan terdorong ke kanan garis. |
| **Kelebihan** | - Garis vertikal pemisah menyambung lurus sempurna secara geometris.<br>- Tombol "Terbitkan" dan "Rollback" terjamin 100% berada di sisi kanan garis (di dalam zona Pengaturan Blok), tidak mungkin menyeberang ke kanvas.<br>- Tata letak stabil di semua resolusi layar (desktop 1366px, 1440px, 1920px). | Cepat diimplementasikan hanya dengan sedikit CSS. |
| **Kekurangan** | Memerlukan pengaturan transisi saat Inspector ditutup via tombol toggle. | Rentan patah/bergeser saat viewport berubah (*fragile*), karena `margin` statis tidak akan selalu cocok dengan fungsi `clamp()` di bawahnya. |
| **Rekomendasi** | **Dipilih Opsi A** demi presisi geometris dan estetika antarmuka kelas profesional. | Ditolak karena tidak menyelesaikan akar masalah secara kokoh. |

---

## 4. Rincian Perubahan File

| File | Tipe Perubahan | Ringkasan Modifikasi |
| :--- | :--- | :--- |
| `resources/views/superadmin/site-editor/partials/_topbar.blade.php` | Markup Blade | 1. Bersihkan kode duplikasi pada baris 81-88.<br>2. Pindahkan tombol `#btn-view-live-site` ("Situs Live") ke `.wp-header-center` (sebagai utility kanvas di samping dropdown & viewport switcher) ATAU letakkan di awal `.wp-header-right` dengan rapi.<br>3. Pastikan struktur markup `.wp-header-right` bersih dan semantik. |
| `resources/css/pages/superadmin/site-editor.css` | Stylesheet | 1. Terapkan `width: clamp(460px, 28vw, 540px);` dan `flex: 0 0 clamp(460px, 28vw, 540px);` pada `.wp-header-right`.<br>2. Tambahkan `border-left: 1px solid var(--wp-admin-border);` pada `.wp-header-right` dengan `padding: 0 20px;` agar menyatu lurus sempurna dengan `border-left` milik `.wp-site-inspector`.<br>3. Tata tombol di `.wp-header-right` dengan `display: flex; align-items: center; justify-content: space-between;` (aksi Terbitkan/Rollback di kiri, toggle inspector di kanan) sehingga tombol Terbitkan bersandar rapi dan aman di dalam kolom Pengaturan Blok.<br>4. Atur state transisi ketika inspector ditutup (`.is-inspector-closed`). |
| `resources/js/pages/superadmin/site-editor/editor-app.js` | JavaScript | Sinkronkan class state pada header saat inspector ditutup/dibuka agar transisi tampilan tetap harmonis. |
| `tests/Feature/LandingPage/SiteEditorResponsiveCanvasTest.php` | Automated Test | Tambahkan assertion untuk memverifikasi struktur header kanan dan ketiadaan error markup/tag duplikat. |

---

## 5. Langkah Implementasi Terstruktur

### Fase 1: Pembersihan Markup & Restrukturisasi Topbar
- [x] **Langkah 1.1**: Bersihkan duplikasi markup di `resources/views/superadmin/site-editor/partials/_topbar.blade.php` (hapus sisa tag ganda pada baris 81-88).
- [x] **Langkah 1.2**: Posisikan tombol `#btn-view-live-site` ("Situs Live") secara harmonis:
  - Opsi terbaik: Masukkan tombol "Situs Live" ke dalam `.wp-header-center` di samping dropdown halaman dan switcher viewport sebagai kontrol preview/kanvas publik.
  - Dengan demikian, `.wp-header-right` dikhususkan 100% untuk panel Pengaturan Blok & Aksi Penerbitan (Tombol Terbitkan, Rollback, dan Toggle Inspector).

### Fase 2: Penyelarasan Geometris CSS Header Kanan & Inspector
- [x] **Langkah 2.1**: Di `resources/css/pages/superadmin/site-editor.css`, ubah styling `.wp-header-right`:
  ```css
  .wp-header-right {
      width: clamp(460px, 28vw, 540px);
      flex: 0 0 clamp(460px, 28vw, 540px);
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 20px;
      border-left: 1px solid var(--wp-admin-border);
      background: var(--wp-admin-surface);
      transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1), flex 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  }
  ```
- [x] **Langkah 2.2**: Pastikan `.wp-publish-group` memiliki margin yang pas di dalam kolom kanan:
  ```css
  .wp-publish-group {
      display: flex;
      align-items: center;
      gap: 8px;
  }
  ```
- [x] **Langkah 2.3**: Tambahkan penanganan state saat panel inspector ditutup melalui toggle:
  ```css
  body.is-inspector-collapsed .wp-header-right {
      width: auto;
      flex: 0 0 auto;
      border-left-color: transparent;
  }
  ```

### Fase 3: Penyesuaian JavaScript & Integrasi State
- [x] **Langkah 3.1**: Di `resources/js/pages/superadmin/site-editor/editor-app.js`, perbarui fungsi `toggleInspector` agar menambahkan/menghapus class `is-inspector-collapsed` pada `document.body`.

### Fase 4: Validasi & Kompilasi
- [x] **Langkah 4.1**: Jalankan kompilasi Vite: `npm run build`.
- [x] **Langkah 4.2**: Jalankan pengujian otomatis PHPUnit dengan binary PHP 8.3 aktual:
  ```powershell
  & "D:\laragon\bin\php\php-8.3.33-Win32-vs16-x64\php.exe" artisan test --filter=SiteEditorResponsiveCanvasTest
  ```
- [x] **Langkah 4.3**: Lakukan inspeksi visual untuk memverifikasi bahwa garis batas panel Pengaturan Blok dan garis batas kolom kanan topbar tersambung lurus 100%, serta tombol "Terbitkan" tidak lagi menyeberang garis ke kiri.

---

## 6. Strategi Pengujian (Testing Strategy)

1. **Uji Geometris & CSS Alignment**:
   - Memastikan koordinat horizontal (`getBoundingClientRect().left`) dari `.wp-header-right` sama persis dengan `.wp-site-inspector`.
   - Memastikan tombol `#btn-publish` berada sepenuhnya di dalam area koordinat `.wp-header-right` (tidak ada overflow negatif ke kiri).
2. **Uji Fungsionalitas Tombol**:
   - Tombol "Terbitkan" tetap memicu modal konfirmasi penerbitan publik.
   - Tombol "Rollback" tetap memicu modal konfirmasi rollback draf.
   - Tombol "Toggle Inspector" tetap dapat membuka dan menutup panel samping secara lancar.
3. **Uji Responsif Viewport**:
   - Verifikasi pada resolusi layar 1366px, 1440px, dan 1920px bahwa garis vertikal tetap sejajar dan tidak bergeser.

---

## 7. Risiko & Rencana Mitigasi

| Potensi Risiko | Tingkat | Rencana Mitigasi |
| :--- | :--- | :--- |
| Tombol di `.wp-header-center` menjadi padat jika "Situs Live" dipindahkan ke tengah | Rendah | Menggunakan layout flexbox dengan gap proporsional (8px - 12px) dan teks responsif (`.wp-btn-label` otomatis tersembunyi pada layar < 1280px). |
| Transisi toggle inspector terasa melompat | Rendah | Menggunakan CSS `transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);` yang seragam antara header kanan dan panel inspector. |

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Garis batas vertikal kiri `.wp-header-right` tersambung lurus vertikal sempurna (*100% aligned*) dengan garis batas vertikal kiri `.wp-site-inspector`.
- [x] Tombol `#btn-publish` ("Terbitkan") berada rapi di sisi kanan garis batas (di dalam zona kolom Pengaturan Blok), tidak ada bagian tombol yang menyeberang ke area kanvas tengah.
- [x] Duplikasi markup pada `_topbar.blade.php` baris 81-88 telah dibersihkan sepenuhnya.
- [x] Build asset `npm run build` sukses tanpa error dan tanpa warning.
- [x] Seluruh pengujian otomatis `SiteEditorResponsiveCanvasTest` lulus 100% menggunakan PHP 8.3.
