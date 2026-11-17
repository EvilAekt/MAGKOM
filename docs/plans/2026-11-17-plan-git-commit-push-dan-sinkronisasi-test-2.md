# 📋 PLAN: Git Commit, Push ke Branch `test-2` & Pembaruan Dokumentasi Sistem

- **Tanggal Dokumen**: 17 November 2026
- **Target Branch**: `test-2`
- **Status Rencana**: ⏳ **MENUNGGU KONFIRMASI USER UNTUK EKSEKUSI GIT**
- **Klasifikasi**: UI/UX Standardization, Bugfix & Documentation Parity

---

## 1. Ringkasan Seluruh Perubahan (Executive Summary)

Siklus kerja pada sesi ini mencakup perbaikan bug sintaksis, standarisasi antarmuka Bento Grid lintas peran, dan penguncian kuota registrasi:

1. **Perbaikan Syntax Parse Error pada Komponen Blade SuperAdmin**:
   - Memperbaiki tabrakan tanda kutip tunggal (*unescaped nested quotes*) pada atribut `:title` komponen `<x-sa.panel>` di file `_tab-bimbingan.blade.php`, `_tab-seleksi.blade.php`, dan `_tab-sistem.blade.php`.
   - Menghilangkan 6 error sintaksis yang sebelumnya ditandai warna merah pada sidebar explorer IDE.
2. **Penyelarasan Warna Kartu "Perlu Tindakan" SuperAdmin (Color Parity)**:
   - Menyelaraskan kartu peringatan SLA (`.sa-action-card`) dengan latar merah kirmisi pekat (`#DC2626` / `rgba(220, 38, 38, 0.95)`), bingkai inner bergaris halus, dan titik status segmen (`.sa-dot.is-active`).
3. **Penyelarasan Visual Diagram Batang "Status Antrean Berkas" SuperAdmin (Chart Bar Parity)**:
   - Menyelaraskan diagram batang vertikal 1:1 dengan standar kartu "Distribusi Tugas" Bento (pembimbing/peserta).
   - Menambahkan bingkai kartu dalam (`.sa-antrean-chart-box`), mengaktifkan pola arsir garis diagonal `-45deg` pada track bar di Light & Dark Mode, mengubah geometri bar menjadi *pill-top flush bottom* (`border-radius: 12px 12px 0 0`), serta menghitung tinggi bar secara proporsional persentase (`%`).
4. **Standarisasi Shell Navigasi & Footer Multi-Peran**:
   - Menyelaraskan warna sidebar dan merapikan footer persisten rata kanan (`padding: 12px 32px`, latar navy pekat `#003566` / `#031B40`) pada dashboard Admin, Pembimbing, Peserta, dan SuperAdmin.
5. **Pembaruan Living Documentation**:
   - Menyelaraskan `docs/design/design-system.md` dengan menambahkan dokumentasi Seksi 5.6 (Diagram Batang Vertikal Bento) dan Seksi 5.7 (Kartu Peringatan Kritis SLA).
   - Menyelaraskan `docs/architecture/frontend.md` dengan Seksi 3.3 (Standar Escaping Atribut Blade) dan Seksi 4.5 (Standarisasi Shell Multi-Peran).

---

## 2. Inventaris Berkas yang Terlibat (File Impact Inventory)

### A. Berkas Kode Sumber Termodifikasi (14 Files):
| No | Path Berkas | Modul / Peran | Deskripsi Perubahan |
|---|---|---|---|
| 1 | `resources/views/superadmin/dashboard/partials/_tab-bimbingan.blade.php` | SuperAdmin Blade | Escape tanda kutip tunggal `'sr-only'` pada panel title (1 error fix) |
| 2 | `resources/views/superadmin/dashboard/partials/_tab-seleksi.blade.php` | SuperAdmin Blade | Escape tanda kutip tunggal `'sr-only'` pada panel title (2 error fix) |
| 3 | `resources/views/superadmin/dashboard/partials/_tab-sistem.blade.php` | SuperAdmin Blade | Escape tanda kutip tunggal `'sr-only'` pada panel title (3 error fix) |
| 4 | `resources/views/superadmin/dashboard.blade.php` | SuperAdmin Blade | Menambahkan wrapper `.sa-antrean-chart-box`, tinggi proporsional `%`, dan dot indicator SLA |
| 5 | `resources/css/pages/superadmin/dashboard.css` | SuperAdmin CSS | Inner chart box, pola garis diagonal `-45deg` Dark/Light, pill-top bar, warna kartu merah |
| 6 | `resources/css/pages/admin/dashboard.css` | Admin CSS | Standarisasi sidebar navy dan footer rata kanan |
| 7 | `resources/css/pages/pembimbing/dashboard.css` | Pembimbing CSS | Variabel latar dark footer `var(--dm-navy-deep, #031B40)` |
| 8 | `resources/css/pages/peserta/dashboard.css` | Peserta CSS | Standarisasi padding footer rata kanan |
| 9 | `resources/css/layouts/dashboard-layout-tokens.css` | Global Layout CSS | Token sidebar dan footer gelap konsisten lintas 4 peran |
| 10 | `resources/css/layouts/dashboard.css` | Global Layout CSS | Penyesuaian alignment footer Gentelella |
| 11 | `resources/css/layouts/gentelella.css` | Global Layout CSS | Footer theme dark synchronization |
| 12 | `resources/css/layouts/peserta-layout-tokens.css` | Peserta Layout CSS | Standarisasi padding footer peserta `12px 32px` |
| 13 | `docs/design/design-system.md` | Dokumentasi | Penambahan seksi 5.6 (Vertical Striped Bar Charts) & 5.7 (SLA Alert Cards) |
| 14 | `docs/architecture/frontend.md` | Dokumentasi | Penambahan seksi 3.3 (Blade Attribute Escaping) & 4.5 (Multi-Role Shell) |

### B. Dokumen Rencana Kerja / PRD Baru (Untracked Files):
| No | Path Berkas | Deskripsi |
|---|---|---|
| 15 | `docs/plans/2026-11-17-task-senior-dev-chart-bar-visual-parity.md` | PRD & plan visual parity diagram batang vertikal |
| 16 | `docs/plans/2026-11-17-task-senior-dev-color-parity-perlu-tindakan.md` | PRD & plan visual parity kartu merah perlu tindakan |
| 17 | `docs/plans/2026-11-17-plan-git-commit-push-dan-sinkronisasi-test-2.md` | Rencana commit & push ini |

---

## 3. Rencana Commit Git (Conventional Commits Strategy)

Sesuai aturan imperatif repositori pada `git-workflow.md`, commit disusun secara terstruktur dan terfokus:

### Opsi A: Commit Tunggal Terpadu (Atomic Feature & Parity Release) - *Direkomendasikan*
```bash
git add resources/views/superadmin/ resources/css/ docs/
git commit -m "style(dashboard): standardize bento bar chart, alert cards, and layout parity

- Add diagonal stripes hatching pattern and inner chart box to superadmin queue bar chart
- Align red alert card styling with bento standard and segmented pill indicators
- Standardize dark navy sidebar and persistent right-aligned footer across 4 roles
- Fix unescaped single quotes in superadmin blade panel components
- Update design system and frontend architecture documentation"
git push origin test-2
```

### Opsi B: Commit Bertahap Terpisah (Multi-Commit Breakdown)
1. **Commit 1: Perbaikan Sintaksis Blade**
   ```bash
   git add resources/views/superadmin/dashboard/partials/_tab-*.blade.php
   git commit -m "fix(superadmin): escape single quotes in blade panel component titles"
   ```
2. **Commit 2: Penyelarasan Visual Bar Chart & Alert Card**
   ```bash
   git add resources/views/superadmin/dashboard.blade.php resources/css/pages/superadmin/dashboard.css
   git commit -m "style(superadmin): align queue bar chart and alert card with bento standard"
   ```
3. **Commit 3: Standarisasi Layout Shell Multi-Peran**
   ```bash
   git add resources/css/layouts/ resources/css/pages/admin/ resources/css/pages/pembimbing/ resources/css/pages/peserta/
   git commit -m "style(layouts): unify sidebar navy tokens and right-aligned footer across roles"
   ```
4. **Commit 4: Pembaruan Dokumentasi Sistem & Plan**
   ```bash
   git add docs/
   git commit -m "docs: update design system, frontend architecture, and parity task plans"
   ```
5. **Push ke Remote**:
   ```bash
   git push origin test-2
   ```

---

## 4. Status Verifikasi Quality Gate Sebelum Push

Seluruh pemeriksaan mutu telah dijalankan secara aktual pada branch `test-2`:
- ✅ **Frontend Asset Compilation**: `npm run build` sukses (vite v6.4.5, 164 modul, 2.29s).
- ✅ **PHP View Linting**: `php -l` pada view terkompilasi menghasilkan 0 parse error.
- ✅ **Test Suite SuperAdmin**: `php artisan test --filter=SuperAdmin` lulus 100% (261 passed, 1496 assertions).
- ✅ **Code Formatting**: `./vendor/bin/pint --test` lulus (0 violations).
- ✅ **Knowledge Graph**: `graphify update .` sukses sinkronisasi AST code graph.

---

## 5. Instruksi Selanjutnya

Rencana ini siap dieksekusi setelah Anda memberikan konfirmasi atau memilih opsi commit yang diinginkan (Opsi A satu commit terpadu atau Opsi B commit bertahap).
