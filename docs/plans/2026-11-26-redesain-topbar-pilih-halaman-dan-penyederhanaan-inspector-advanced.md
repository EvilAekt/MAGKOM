# Rencana Kerja: Redesain Topbar Kanvas Editor & Penyederhanaan Inspector Pengaturan Blok

Dokumen ini merinci rencana teknis terstruktur untuk merapikan antarmuka **Canvas Site Editor (`/superadmin/site-editor`)** sesuai 4 permintaan perubahan tata letak dan fungsionalitas:
1. **Pemindahan Dropdown Pilih Halaman ke Tengah**: Memindahkan dropdown pilihan halaman (`#select-canvas-page`) dari area header kiri ke header tengah (`.wp-header-center`), bersanding langsung dengan pengalih viewport (Desktop, Tablet, Mobile).
2. **Penghapusan Label Teks "Pilih Halaman"**: Menghilangkan teks statis "Pilih Halaman" di samping dropdown agar tampilan dropdown lebih ringkas, mandiri, dan modern (hanya berupa pill dropdown dengan ikon dokumen & chevron).
3. **Penghapusan Tombol "Pratinjau Draf"**: Menghapus tombol `#btn-preview-external` ("Pratinjau Draf") dari kelompok utilitas header kanan, sehingga area aksi kanan lebih lapang dan berfokus pada tautan "Situs Live" serta aksi penerbitan ("Terbitkan" & "Rollback").
4. **Penghapusan Tab/Subpane "Advanced" pada Pengaturan Blok**: Menghapus subtab `Advanced` dan panel konfigurasinya dari panel inspector blok, menyisakan dua tab utama yang intuitif: **General** dan **Style**.

---

## 1. Analisis Kebutuhan & Kondisi Eksisting

### 1.1 Kondisi Eksisting Header Topbar (`_topbar.blade.php`)
- **Sisi Kiri (`.wp-header-left`)**: Saat ini memuat Tombol Kembali, Logo & Teks Brand MAGKOM, serta pembungkus dropdown `.canvas-page-selector-wrap`. Keberadaan dropdown dan label teks "Pilih Halaman" di sisi kiri membuat sisi kiri terasa padat dan tidak seimbang.
- **Sisi Tengah (`.wp-header-center`)**: Hanya memuat segmented pill `.wp-viewport-switcher` (Desktop, Tablet, Mobile). Terdapat ruang kosong yang cukup luas di sisi tengah untuk menampung dropdown pemilih halaman.
- **Sisi Kanan (`.wp-header-right`)**: Memuat dua tombol ghost di `.wp-utility-group`: "Pratinjau Draf" (`#btn-preview-external`) dan "Situs Live" (`#btn-view-live-site`). Tombol "Pratinjau Draf" dirasa redundan karena kanvas editor itu sendiri sudah berfungsi sebagai pratinjau WYSIWYG interaktif secara *real-time*.

### 1.2 Kondisi Eksisting Inspector Subtabs (`_inspector.blade.php`)
- Saat blok aktif dipilih, inspector menampilkan 3 subtab:
  1. `General`: Konten formulir teks, gambar, repeater, dan data dinamis blok.
  2. `Style`: Pengaturan tipografi, warna, spasi, dan dekorasi visual.
  3. `Advanced`: ID blok, HTML anchor, custom CSS class, toggle visibilitas perangkat, lock block, dan reset style.
- Subtab `Advanced` memuat opsi teknis tingkat lanjut (anchor, custom CSS, HTML ID) yang jarang digunakan dan berpotensi membingungkan pengguna administrator umum. Penghapusan tab ini akan memangkas beban kognitif dan menyederhanakan alur kerja inspeksi blok.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja Logis:
1. **Aksesibilitas (WCAG 2.1 AA)**: Walaupun label visual "Pilih Halaman" dihapus dari layar, elemen `<select>` tetap mempertahankan atribut `aria-label="Pilih Halaman Publik yang Sedang Diedit"` dan `<label for="select-canvas-page" class="sr-only">Pilih Halaman</label>` agar lulus uji pembaca layar (*screen reader*).
2. **Keamanan JavaScript Tanpa Tombol Pratinjau**: Script `editor-app.js` yang sebelumnya menyinkronkan URL `#btn-preview-external` saat dropdown halaman berubah akan dibersihkan atau diproteksi agar tidak menimbulkan `TypeError: null`.
3. **Penyimpanan Skema Blok Tetap Utuh**: Menghapus antarmuka `Advanced` di Blade tidak menghapus kemampuan backend/validator dalam membaca `settings` blok (seperti `html_anchor` atau `custom_css_class`) jika sebelumnya sudah ada data tersimpan di draf/database.

---

## 3. Evaluasi Opsi Pendekatan Teknis

| Parameter | Opsi A: Restrukturisasi Topbar Center + Clean Subtabs (Direkomendasikan) | Opsi B: Sembunyikan via CSS (`display: none`) Saja |
| :--- | :--- | :--- |
| **Konsep** | Memindahkan markup `.canvas-page-selector-wrap` ke `.wp-header-center`, menghapus tombol `Pratinjau Draf` dan subtab `Advanced` langsung dari markup Blade serta membersihkan CSS/JS terkait. | Membiarkan markup Blade utuh, lalu menyembunyikan label, tombol draf, dan subtab Advanced menggunakan selector CSS `display: none !important;`. |
| **Kelebihan** | Kode bersih (*clean code*), DOM lebih ringan, tidak ada elemen hantu yang membingungkan screen reader atau navigasi keyboard (Tab index), arsitektur rapi. | Sangat cepat dibuat karena hanya menambahkan beberapa baris CSS. |
| **Kekurangan** | Memerlukan modifikasi terkoordinasi pada file Blade, CSS, dan JS. | Menyisakan kode mati (*dead markup*) di DOM, rentan bocor jika CSS gagal termuat, dan atribut form tersembunyi tetap terkirim/terindeks. |
| **Rekomendasi** | **Dipilih Opsi A** demi standar profesionalisme dan kepatuhan arsitektur kode MAGKOM. | Ditolak karena menciptakan utang teknis (*technical debt*). |

---

## 4. Rincian Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/superadmin/site-editor/partials/_topbar.blade.php` | Modify | 1. Pindahkan `.canvas-page-selector-wrap` dari `.wp-header-left` ke dalam `.wp-header-center`.<br>2. Hapus label visual `<label class="canvas-page-selector-label">Pilih Halaman</label>` (gantikan dengan `.sr-only`).<br>3. Hapus tombol link `#btn-preview-external` ("Pratinjau Draf"). |
| `resources/views/superadmin/site-editor/partials/_inspector.blade.php` | Modify | 1. Hapus tombol subtab `<button data-subtab="advanced">Advanced</button>` pada `.wp-block-subtabs`.<br>2. Hapus kontainer panel `#wp-subpane-advanced`. |
| `resources/css/pages/superadmin/site-editor.css` | Modify | 1. Sesuaikan layout `.wp-header-center` dengan flexbox gap (12px) dan divider pemisah antara dropdown halaman dan switcher viewport.<br>2. Hapus style yang sudah tidak digunakan (`.canvas-page-selector-label`).<br>3. Pastikan grid subtabs `.wp-block-subtabs` otomatis terbagi rata 2 kolom (50% - 50% untuk General dan Style). |
| `resources/js/pages/superadmin/site-editor/editor-app.js` | Modify | Bersihkan referensi DOM `previewExternalBtn` agar tidak lagi mencoba memanipulasi elemen `#btn-preview-external` yang telah dihapus. |
| `tests/Feature/LandingPage/SiteEditorResponsiveCanvasTest.php` | Modify | Tambahkan assertion untuk memastikan dropdown halaman berada di topbar center, tombol Pratinjau Draf tidak lagi dirender, dan subtab Advanced tidak lagi ditampilkan. |

---

## 5. Langkah Implementasi Terstruktur

### Fase 1: Penataan Ulang Topbar Header
- [x] **Langkah 1.1**: Buka `resources/views/superadmin/site-editor/partials/_topbar.blade.php`.
- [x] **Langkah 1.2**: Pindahkan blok `.canvas-page-selector-wrap` dari `.wp-header-left` ke `.wp-header-center` (diletakkan sebelum atau sesudah `.wp-viewport-switcher`).
- [x] **Langkah 1.3**: Ubah label teks visual menjadi `sr-only` dan tambahkan `aria-label` pada select:
  ```html
  <div class="canvas-page-selector-wrap" title="Pilih Halaman Publik yang Sedang Diedit">
      <label for="select-canvas-page" class="sr-only">Pilih Halaman</label>
      <div class="canvas-page-select-box">
          <svg class="canvas-page-icon" ...>...</svg>
          <select id="select-canvas-page" class="canvas-page-select" aria-label="Pilih Halaman Publik yang Sedang Diedit">
              <option value="landing" selected>Landing Page (/)</option>
              <option value="kebijakan-privasi">Kebijakan Privasi (/kebijakan-privasi)</option>
          </select>
          <svg class="canvas-page-chevron" ...>...</svg>
      </div>
  </div>
  ```
- [x] **Langkah 1.4**: Hapus elemen link `#btn-preview-external` ("Pratinjau Draf") di `.wp-utility-group`.

### Fase 2: Penyesuaian CSS Topbar & Subtabs Inspector
- [x] **Langkah 2.1**: Di `resources/css/pages/superadmin/site-editor.css`:
  - Perbarui `.wp-header-center`:
    ```css
    .wp-header-center {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }
    ```
  - Tambahkan divider pemisah opsional antara dropdown halaman dan pengalih perangkat jika diperlukan:
    ```css
    .wp-header-center-divider {
        width: 1px;
        height: 20px;
        background: #e2e8f0;
    }
    ```
  - Pastikan `.canvas-page-select-box` memiliki ukuran proporsional (height: 34px) yang selaras dengan tinggi `.wp-viewport-switcher`.
  - Pastikan `.wp-block-subtabs` yang kini hanya berisi 2 tombol (`general` & `style`) membagi lebarnya secara seimbang (`flex: 1`).

### Fase 3: Pemangkasan Subtab & Panel Advanced di Inspector
- [x] **Langkah 3.1**: Di `resources/views/superadmin/site-editor/partials/_inspector.blade.php`:
  - Hapus baris `<button type="button" class="wp-subtab-btn" data-subtab="advanced" role="tab">Advanced</button>`.
  - Hapus blok div `#wp-subpane-advanced` (baris 527 s/d 578).
- [x] **Langkah 3.2**: Di `resources/js/pages/superadmin/site-editor/editor-app.js`:
  - Hapus referensi `document.getElementById('btn-preview-external')`.

### Fase 4: Verifikasi & Pengujian
- [x] **Langkah 4.1**: Jalankan kompilasi aset Vite `npm run build`.
- [x] **Langkah 4.2**: Jalankan pengujian otomatis PHPUnit `php artisan test --filter=SiteEditorResponsiveCanvasTest`.
- [x] **Langkah 4.3**: Jalankan pemeriksaan linter `./vendor/bin/pint --test`.
- [x] **Langkah 4.4**: Verifikasi visual di browser pada `/superadmin/site-editor`:
  - Topbar kiri: Bersih hanya memuat tombol kembali dan brand MAGKOM.
  - Topbar tengah: Menampilkan Dropdown Halaman (Landing Page / Kebijakan Privasi) bersanding rapi dengan tombol Desktop, Tablet, Mobile.
  - Topbar kanan: Hanya menampilkan tombol "Situs Live", status simpan, dan tombol aksi "Terbitkan / Rollback".
  - Panel Inspector: Hanya menampilkan 2 subtab: **General** dan **Style**.

---

## 6. Strategi Testing

1. **Uji Otomatis (PHPUnit Feature Test)**:
   - `test_site_editor_topbar_structure`:
     - Memastikan `#select-canvas-page` ada di halaman editor.
     - Memastikan tombol `#btn-preview-external` **tidak ada** di response HTML (`assertDontSee('id="btn-preview-external"', false)`).
     - Memastikan subtab Advanced **tidak ada** di response HTML (`assertDontSee('data-subtab="advanced"', false)`).
     - Memastikan label teks visual "Pilih Halaman" tidak muncul sebagai teks polos di luar `.sr-only`.
2. **Uji Regresi Fungsional**:
   - Jalankan `php artisan test --filter=LandingPage` (memastikan 102 test suite tetap 100% lulus tanpa efek samping).
3. **Uji Interaksi JavaScript**:
   - Memastikan pergantian dropdown `#select-canvas-page` dari "Landing Page" ke "Kebijakan Privasi" tetap berfungsi mengubah `iframe.src` tanpa memicu error di console browser.
   - Memastikan klik pada subtab "General" dan "Style" tetap membuka panel yang sesuai secara mulus.

---

## 7. Analisis Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
| :--- | :--- | :--- |
| **Lebar Topbar Sempit pada Layar Resolusi Kecil (e.g. Laptop 1366x768)** | Header center dan header right berhimpitan | Atur `flex-wrap: nowrap` dengan min-width pada `.wp-header-center`, serta sesuaikan padding dropdown agar tetap ringkas dan elegan. |
| **JavaScript Null Pointer Exception pasca Tombol Draf Dihapus** | Script `editor-app.js` crash saat pergantian halaman jika masih mencari `#btn-preview-external` | Bersihkan kode pencarian `#btn-preview-external` dan pastikan setiap manipulasi DOM dilindungi pengecekan `if (element)`. |
| **Pelanggaran Aksesibilitas akibat Penghapusan Teks Label** | Form control select kehilangan nama yang dapat diakses pembaca layar | Tetap sediakan label tersembunyi menggunakan kelas `.sr-only` dan sematan atribut `aria-label`. |

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Dropdown pilih halaman berpindah ke bagian tengah topbar, bersanding dengan tombol Desktop, Tablet, dan Mobile.
- [x] Label teks visual "Pilih Halaman" di samping dropdown telah dihapus (tampilan hanya berupa pill dropdown bersih).
- [x] Tombol "Pratinjau Draf" di topbar kanan telah dihapus sepenuhnya.
- [x] Subtab "Advanced" dan panel `#wp-subpane-advanced` pada pengaturan blok inspector telah dihapus, menyisakan tab General dan Style.
- [x] Pergantian halaman (Landing Page ↔ Kebijakan Privasi) dan tombol viewport switcher tetap berfungsi 100% normal tanpa JavaScript error.
- [x] Kompilasi aset `npm run build` sukses tanpa error.
- [x] Seluruh pengujian otomatis PHPUnit lulus hijau (100% passed).
