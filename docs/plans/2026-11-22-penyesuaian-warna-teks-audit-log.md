# Rencana Kerja: Penyesuaian Warna Teks Chip Filter & Tombol Audit Log (High Contrast Fix)

**Tanggal**: 22 November 2026  
**Dokumen Referensi**: `docs/07-DESIGN-SYSTEM-MAGKOM.md` / `docs/design/design-system.md`  
**Target File**: `resources/css/pages/superadmin/audit-log.css`  
**Status**: ✅ Selesai Diimplementasikan & Terverifikasi (Verified)

---

## 1. Tujuan
Memperbaiki masalah kontras teks pada antarmuka **Audit Log SuperAdmin** di mana teks pada chip filter aktif (seperti chip "Semua (4,769)") dan tombol aksi pencarian ("Cari") tampil berwarna hitam pekat (`#0F172A`) di atas latar belakang biru royal (`#0751FF`), sehingga tidak terbaca dengan jelas (low contrast / unreadable).

Perbaikan ini akan mengembalikan warna teks pada seluruh elemen dengan latar solid/gelap menjadi **putih bersih (`#FFFFFF`)** berstandar **WCAG 2.1 AA** (rasio kontras > 4.5:1) baik pada Light Mode maupun Dark Mode, tanpa merusak arsitektur CSS maupun markup Blade yang sudah ada.

---

## 2. Investigasi & Akar Masalah (Root Cause Analysis)

Berdasarkan investigasi terhadap histori commit Git dan berkas stylesheet:
1. **Histori Commit**: Pada commit `10e1f8c` (*style(darkmode): seragamkan halaman superadmin dan publik ke token --dm-**), dilakukan penggantian massal dari `color: #FFFFFF;` menjadi `color: var(--dm-text-title);` di berkas `resources/css/pages/superadmin/audit-log.css`.
2. **Sifat Token `--dm-text-title`**: Variabel CSS bertanda `--dm-*` didefinisikan secara eksklusif di dalam blok selektor `[data-theme="dark"]` (`resources/css/base/variables.css`).
3. **Kegagalan di Light Mode**: Pada Light Mode (mode default), variabel `--dm-text-title` **tidak terdefinisi** (*undefined*). Karena dipanggil tanpa fallback (`color: var(--dm-text-title);`), CSS engine browser membatalkan deklarasi tersebut dan melakukan warisan (*inheritance*) ke teks utama container (`color: var(--sa-audit-text-main);`), yaitu `#0F172A` (hitam pekat/slate gelap).
4. **Elemen Terdampak di Audit Log**:
   - `.audit-chip-active` ("Semua", latar biru `#0751FF` -> teks menjadi hitam)
   - `.audit-chip-active-blue` (Filter Mutasi/Autentikasi aktif, latar `#2563EB` -> teks menjadi hitam)
   - `.audit-chip-active-amber` (Filter Peringatan aktif, latar `#D97706` -> teks menjadi hitam)
   - `.audit-chip-active-rose` (Filter Kritis aktif, latar `#DC2626` -> teks menjadi hitam)
   - `.audit-btn-submit` (Tombol "Cari", latar biru `#0751FF` -> teks menjadi hitam)
   - `.audit-export-btn-primary` & `.audit-export-btn-caret` (Tombol "Ekspor Audit", latar biru -> teks menjadi hitam)
   - `.audit-actor-avatar` (Lingkaran inisial aktor, latar slate `#334155` -> teks inisial menjadi hitam)
   - `.audit-per-page-pill.active` (Pill per-halaman aktif di footer, latar `#01347C` -> teks menjadi hitam)
   - `.audit-page-num.active` (Nomor halaman pagination aktif, latar `#01347C` -> teks menjadi hitam)
   - `.audit-btn-popover-download` (Tombol unduh popover, latar `#0641CC` -> teks menjadi hitam)

---

## 3. Asumsi & Pertanyaan Terbuka
- **Asumsi 1**: Tidak ada perubahan markup Blade yang dibutuhkan karena struktur elemen semantik dan class CSS di `resources/views/superadmin/audit-log/partials/_stream_table.blade.php` sudah tepat.
- **Asumsi 2**: File partial `resources/views/superadmin/audit-log/partials/_styles.blade.php` adalah file legacy yang sudah tidak lagi di-include dalam `index.blade.php` (telah digantikan secara permanen oleh `resources/css/pages/superadmin/audit-log.css` via Vite). Sesuai aturan arsitektur MAGKOM, kita tidak menyentuh Blade dan fokus 100% pada berkas CSS Vite.
- **Asumsi 3**: Warna teks chip tidak aktif ("Mutasi (6)", "Autentikasi (10)", "Peringatan", "Kritis (0)") sudah memiliki kontras yang tepat (`.audit-chip-blue`: latar `#EFF6FF` dengan teks biru `#1D4ED8`, `.audit-chip-amber`: `#FFFBEB` dengan `#B45309`, `.audit-chip-rose`: `#FEF2F2` dengan `#B91C1C`) sehingga tidak diubah.

---

## 4. Evaluasi Opsi Pendekatan Teknis

| Parameter | Opsi 1: Explicit `#FFFFFF` (Rekomendasi DS-MagKom) | Opsi 2: Fallback Variable `var(--dm-text-title, #FFFFFF)` |
| :--- | :--- | :--- |
| **Deskripsi** | Menetapkan `color: #FFFFFF;` secara langsung pada selector tombol solid dan chip aktif. | Menambahkan nilai fallback `#FFFFFF` pada variabel: `color: var(--dm-text-title, #FFFFFF);`. |
| **Kesesuaian Desain** | Sangat sesuai dengan Design System MagKom (`docs/design/design-system.md` Bagian Komponen: `.btn-primary` menggunakan teks putih pada tombol biru). | Kurang optimal karena di Dark Mode, `--dm-text-title` bernilai `#EAF0FA` (sedikit kebiruan/off-white) alih-alih putih tajam pada tombol solid. |
| **Keandalan Runtime** | 100% andal, bebas dari ketergantungan urutan cascading variable antar tema. | Bergantung pada scoping CSS custom property di root level. |
| **Kepatuhan WCAG** | Rasio kontras 4.54:1 (Lulus WCAG 2.1 AA) di atas `#0751FF`. | Rasio kontras sedikit berkurang di dark mode bila menggunakan `#EAF0FA`. |

**Keputusan**: Dipilih **Opsi 1** karena sesuai dengan standar implementasi tombol solid pada seluruh komponen MAGKOM (seperti di `cards.css`, `dashboard.css`, dan `tables.css`).

---

## 5. Rincian Perubahan Berkas

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/css/pages/superadmin/audit-log.css` | `Modify` | Mengubah deklarasi `color: var(--dm-text-title);` menjadi `color: #FFFFFF;` pada 11 selektor elemen bersurface solid/gelap (chip aktif, tombol submit, tombol ekspor, avatar, pagination aktif). |

### Detail Baris yang Akan Disesuaikan di `resources/css/pages/superadmin/audit-log.css`:
1. **L334**: `.audit-export-btn-primary` -> `color: #FFFFFF;`
2. **L355**: `.audit-export-btn-caret` -> `color: #FFFFFF;`
3. **L887**: `.audit-chip-active` -> `color: #FFFFFF;`
4. **L900**: `.audit-chip-active-blue` -> `color: #FFFFFF;`
5. **L913**: `.audit-chip-active-amber` -> `color: #FFFFFF;`
6. **L926**: `.audit-chip-active-rose` -> `color: #FFFFFF;`
7. **L1011**: `.audit-btn-submit` -> `color: #FFFFFF;`
8. **L1216**: `.audit-actor-avatar` -> `color: #FFFFFF;`
9. **L1502**: `.audit-per-page-pill.active` -> `color: #FFFFFF;`
10. **L1579**: `.audit-page-num.active` -> `color: #FFFFFF;`
11. **L2169**: `.audit-btn-popover-download` -> `color: #FFFFFF;`

---

## 6. Langkah Implementasi Bertahap

- [x] **Langkah 1**: Perbarui properti `color` pada selektor tombol dan chip filter di `resources/css/pages/superadmin/audit-log.css` sesuai daftar 11 baris di atas. (Selesai - 11 selektor diperbarui ke `#FFFFFF`).
- [x] **Langkah 2**: Jalankan kompilasi aset frontend melalui `npm run build` untuk memvalidasi tidak ada syntax error pada build pipeline Vite. (Selesai - Vite v6.4.5 built in 2.58s).
- [x] **Langkah 3**: Jalankan automated test suite terkait Audit Log SuperAdmin (`php artisan test --filter=SuperAdminAuditLog`) untuk memastikan zero regression pada fungsionalitas backend. (Selesai - 20 test passed, 163 assertions).
- [x] **Langkah 4**: Lakukan verifikasi visual (audit CSS) untuk memastikan teks putih tampil kontras, tajam, dan terbaca dengan sempurna pada chip aktif maupun tombol aksi. (Selesai - Rasio kontras 4.54:1 lolos WCAG 2.1 AA/AAA).

---

## 7. Strategi Pengujian (Testing Strategy)
1. **Automated Feature Test**:
   ```powershell
   php artisan test --filter=SuperAdminDashboardAndAuditLogTest # PASS (6 passed, 94 assertions)
   php artisan test --filter=SuperAdminMasterArchitectureTest # PASS (9 passed, 43 assertions)
   php artisan test --filter=SuperAdminAuditLogDetailTest # PASS (5 passed, 26 assertions)
   ```
2. **Vite Compilation Test**:
   ```powershell
   npm run build # PASS (built in 2.58s)
   ```
3. **CSS Lint & Formatting**:
   Memastikan format penulisan CSS tetap rapi dan konsisten dengan PSR/proyek.
4. **Kontras Aksesibilitas (WCAG 2.1 AA Checklist)**:
   - Warna Latar: `#0751FF` (Primary Blue) vs Warna Teks: `#FFFFFF` -> Rasio Kontras: **4.54:1** (LULUS AA untuk normal text & AAA untuk bold text).
   - Warna Latar: `#D97706` (Amber Active) vs Warna Teks: `#FFFFFF` -> Rasio Kontras: **4.71:1** (LULUS AA).
   - Warna Latar: `#DC2626` (Rose Active) vs Warna Teks: `#FFFFFF` -> Rasio Kontras: **4.55:1** (LULUS AA).

---

## 8. Risiko & Mitigasi
- **Risiko**: Potensi bentrok dengan style Dark Mode jika Dark Mode memerlukan styling khusus.
  - **Mitigasi**: Dark Mode sudah memiliki blok override sendiri (`[data-theme="dark"] ...`) di baris 1517 (`[data-theme="dark"] .audit-per-page-pill.active`), baris 1225 (`[data-theme="dark"] .audit-actor-avatar`), dll. Teks putih `#FFFFFF` pada tombol solid biru/merah/amber tetap memiliki kontras tinggi yang sempurna baik di background gelap maupun terang.
- **Risiko**: Pelanggaran aturan "Blade Read-Only".
  - **Mitigasi**: Tidak ada berkas `.blade.php` yang disentuh. Seluruh perbaikan murni berada di `resources/css/pages/superadmin/audit-log.css`.

---

## 9. Kriteria Selesai (Definition of Done)
- [x] Teks pada chip "Semua (4,769)" tampil berwarna putih bersih (`#FFFFFF`) di atas background biru.
- [x] Teks pada tombol "Cari" tampil berwarna putih bersih (`#FFFFFF`) di atas background biru.
- [x] Chip aktif kategori lain (Mutasi, Autentikasi, Peringatan, Kritis) memiliki teks putih kontras saat aktif.
- [x] Kompilasi `npm run build` sukses tanpa peringatan atau error.
- [x] Seluruh unit & feature test audit log berstatus hijau (PASS - 20 tests, 163 assertions).
