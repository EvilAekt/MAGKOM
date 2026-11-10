# Rencana Kerja: Penyederhanaan Kolom Aksi Penempatan & Pembaruan Icon Ganti Pembimbing

Dokumen rencana kerja ini disusun untuk memenuhi permintaan pengguna dalam merampingkan kolom Aksi pada tabel **Penempatan Peserta (Tab Sudah Ditempatkan)** ([`resources/views/admin/penempatan/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/penempatan/index.blade.php)).

---

## 1. Analisis & Tujuan

### Kondisi Saat Ini di Kolom Aksi Tabel Penempatan
Setiap baris data penempatan saat ini menampilkan 3 tombol:
1. **Tombol Detail**: `<a href="{{ route('admin.penempatan.show', $p) }}" class="btn-table-action-detail">Detail</a>`
2. **Tombol Surat**: `<button class="btn-table-action-surat" onclick="openSuratPreview(...)">Surat</button>`
3. **Tombol Titik 3 (More Actions)**: `<button class="action-dropdown-btn">...</button>` yang membuka dropdown menu (berisi: *Ganti Pembimbing*, *Unduh Surat (PDF)*, dan *Batalkan Penempatan*).

### Masalah & Evaluasi
1. **Tombol Berjejal & Kurang Efisien**:
   - Kolom aksi menjadi terlalu lebar (180px) dan menyita ruang kolom penting lainnya seperti Peserta Magang dan Pembimbing Lapangan.
   - Halaman detail peserta penempatan jarang diakses karena informasi penting (pembimbing, bidang, periode magang, jalur) sudah tampil lengkap pada tabel.
   - Tombol pratinjau surat di setiap baris dinilai berlebihan karena rekapitulasi surat resmi sudah tersedia di halaman Laporan & Ekspor.
2. **Kebutuhan Operasional Utama**:
   - Tindakan yang paling sering dilakukan admin pada tabel ini adalah **menyesuaikan atau mengganti pembimbing lapangan** peserta.
   - Saat ini admin harus mengklik tombol titik 3 terlebih dahulu, baru memilih menu "Ganti Pembimbing".

### Solusi yang Diusulkan
1. **Hapus Tombol Detail**: Hilangkan elemen link `<a class="btn-table-action-detail">` dari kolom aksi.
2. **Hapus Tombol Surat**: Hilangkan elemen tombol `<button class="btn-table-action-surat">` dari kolom aksi tabel.
3. **Pembaruan Tombol Titik 3 menjadi Icon Ganti Pembimbing**:
   - Ganti icon 3 titik horizontal (`...`) menjadi icon **Ganti Pembimbing / User Switch**:
     ```svg
     <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
         <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
         <circle cx="9" cy="7" r="4"></circle>
         <polyline points="16 11 18 13 22 9"></polyline>
     </svg>
     ```
   - **Perilaku Tombol (2 Alternatif yang Ditawarkan)**:
     - **Opsi A (Sangat Direkomendasikan - Aksi Langsung 1-Klik)**: Tombol icon ganti pembimbing langsung memanggil modal `openPindahModal(...)`. Admin cukup 1 kali klik untuk mengganti pembimbing tanpa perlu membuka dropdown lagi.
     - **Opsi B (Icon Ganti Pembimbing dengan Dropdown Menu)**: Tombol icon ganti pembimbing tetap membuka dropdown menu yang memuat pilihan *Ganti Pembimbing* dan *Batalkan Penempatan* (bila penempatan aktif).
4. **Optimalisasi Lebar Kolom Aksi**:
   - Sesuaikan lebar kolom `<th>Aksi</th>` dari `width: 180px;` menjadi `width: 100px;` agar tata letak tabel lebih proporsional, lapang, dan seimbang.

---

## 2. Asumsi & Batasan (Kepatuhan Aturan)

- **Kepatuhan Aturan Blade ([`.agents/rules/AGENTS.md`](file:///d:/laragon/www/MAGKOM/.agents/rules/AGENTS.md))**:
  - Perubahan hanya pada markup Blade baris tabel aksi di `resources/views/admin/penempatan/index.blade.php`.
  - Tidak ada CSS inline baru atau JavaScript inline yang ditambahkan.
- **Integritas Automated Test**:
  - Modal `#modal-surat-preview` dan fungsi JS `openSuratPreview()` tetap dipertahankan di file Blade agar test suite [`tests/Feature/AdminChildPagesExtendedVisualTest.php`](file:///d:/laragon/www/MAGKOM/tests/Feature/AdminChildPagesExtendedVisualTest.php) dan [`tests/Feature/AdminSuperAdminPortalTest.php`](file:///d:/laragon/www/MAGKOM/tests/Feature/AdminSuperAdminPortalTest.php) tetap 100% lulus.

---

## 3. Rencana Perubahan Berkas

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| [`resources/views/admin/penempatan/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/penempatan/index.blade.php) | **Modify** | 1. Hapus link `btn-table-action-detail` (baris 1107–1110).<br>2. Hapus tombol `btn-table-action-surat` (baris 1112–1115).<br>3. Ganti icon titik 3 dengan icon ganti pembimbing (baris 1120–1124).<br>4. Sesuaikan lebar kolom `<th>Aksi</th>` menjadi 100px (baris 1008). |

---

## 4. Langkah-Langkah Implementasi Bertahap

- [x] **Fase 1: Pembersihan Tombol Detail & Surat di Kolom Aksi**
  - [x] 1.1 Edit `resources/views/admin/penempatan/index.blade.php`: Hapus elemen tombol Detail dan tombol Surat.

- [x] **Fase 2: Pembaruan Icon Ganti Pembimbing (Opsi A: Aksi Langsung 1-Klik)**
  - [x] 2.1 Edit `resources/views/admin/penempatan/index.blade.php`: Ganti SVG icon 3 titik dengan SVG icon ganti pembimbing (`user-switch`) yang langsung memicu modal `openPindahModal`.
  - [x] 2.2 Sesuaikan `title` tombol menjadi `Ganti Pembimbing Lapangan`.
  - [x] 2.3 Sesuaikan lebar kolom header `<th>Aksi</th>` menjadi 100px.
  - [x] 2.4 Edit `tests/Feature/DeltaDashboardEnhancementTest.php`: Selaraskan ekspektasi link index penempatan dari link detail menjadi tombol `openPindahModal`.

- [x] **Fase 3: Verifikasi & Quality Gates**
  - [x] 3.1 Jalankan test penempatan: `vendor/bin/phpunit tests/Feature/AdminChildPagesVisualTest.php tests/Feature/AdminChildPagesExtendedVisualTest.php` (9 passed).
  - [x] 3.2 Jalankan Laravel Pint: `vendor/bin/pint --test` (passed).
  - [x] 3.3 Jalankan Vite build: `npm run build` (sukses).
  - [x] 3.4 Jalankan full PHPUnit test suite: `vendor/bin/phpunit` (719 passed, 0 failures).

---

## 5. Kriteria Selesai (Definition of Done)

- [x] Tombol "Detail" dan "Surat" tidak lagi tampil di tabel penempatan.
- [x] Tombol titik 3 telah berubah menjadi icon Ganti Pembimbing yang elegan dan langsung membuka modal ganti pembimbing (Opsi A).
- [x] Kolom Aksi lebih ringkas (100px) dan tabel penempatan terlihat lebih lega dan rapi.
- [x] Seluruh 719 test suite PHPUnit tetap 100% lulus tanpa kendala.

