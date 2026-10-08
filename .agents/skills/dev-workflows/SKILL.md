---
name: dev-workflows
description: >-
  Triggered when the user invokes /new-feature, /fix-bug, /review, /ui-check, /release, or /commit.
  Provides structured, reproducible web development runbooks and quality gates.
---

# Developer Workflows & Slash Commands

Skill ini menyediakan runbook terstruktur untuk alur kerja harian web development.

---

## 1. `/new-feature` : Alur Pengembangan Fitur Baru
**Trigger**: Prompt diawali dengan `/new-feature <deskripsi fitur>`.

### Tahapan Eksekusi:
1. **Perencanaan (Planning)**:
   - Analisis kebutuhan dan tentukan arsitektur (Model, Migration, Controller/Action, Service, Blade/UI).
   - Lakukan tinjauan keamanan awal (validasi input, autorisasi, mitigasi OWASP).
   - Paparkan rencana singkat kepada user sebelum implementasi.
2. **Implementasi (Implementation)**:
   - Tulis kode secara bertahap dan terfokus.
   - Patuhi aturan di `code-style.md` dan `backend-security.md`.
3. **Pengujian (Testing)**:
   - Buat skenario pengujian Feature / Unit test (`php artisan test`).
   - Pastikan happy path dan sad path (validasi gagal, unauthorized) teruji.
4. **Lint & Format**:
   - Jalankan linter/formatter (`./vendor/bin/pint` atau `npm run lint`).
5. **Ringkasan Perubahan**:
   - Berikan laporan ringkas berisi daftar file yang dibuat/diubah dan instruksi verifikasi.

---

## 2. `/fix-bug` : Alur Perbaikan Bug Sistematis
**Trigger**: Prompt diawali dengan `/fix-bug <deskripsi masalah/error>`.

### Tahapan Eksekusi:
1. **Reproduksi Masalah**:
   - Pahami pesan error, stack trace, atau anomali perilaku sistem.
   - Tuliskan tes otomatis yang gagal (failing test) untuk mengunci bug, jika memungkinkan.
2. **Root Cause Analysis (RCA)**:
   - Temukan akar permasalahan sebenarnya, bukan sekadar menyamarkan gejala (monkey patch).
3. **Perbaikan Terfokus**:
   - Lakukan perbaikan minimal dan tepat sasaran pada file terkait.
4. **Regression Testing**:
   - Jalankan failing test untuk memastikan bug tuntas (`PASS`).
   - Jalankan seluruh test suite terkait untuk memastikan tidak ada fitur lain yang rusak.

---

## 3. `/review` : Tinjauan Kualitas & Keamanan Kode
**Trigger**: Prompt diawali dengan `/review` atau `/review <file/fitur>`.

### Tahapan Eksekusi:
Evaluasi kode berdasarkan 4 pilar standar proyek:
1. **Bugs & Logic Flaws**: Deteksi null pointer, edge cases, off-by-one errors, atau unhandled exceptions.
2. **Cybersecurity & OWASP**: Periksa CSRF, sanitasi input, parameter binding query, mass assignment, otorisasi IDOR.
3. **Performance & Optimization**: Deteksi potensi N+1 query Eloquent, indexing database yang hilang, render blocking assets.
4. **Frontend & Accessibility (a11y)**: Semantic HTML, form labels, mobile responsiveness, Core Web Vitals.
*Output*: Berikan kesimpulan berbobot (Pass / Needs Changes / Critical Blockers) beserta rekomendasi aksi.

---

## 4. `/ui-check` : Inspeksi Antarmuka & Browser Automation
**Trigger**: Prompt diawali dengan `/ui-check <url atau route>`.

### Tahapan Eksekusi:
1. **Navigasi Browser**: Buka halaman menggunakan browser automation (Playwright MCP atau Chrome DevTools).
2. **Console & Network Audit**: Periksa apakah ada JavaScript error, unhandled promise rejection, atau request gagal (404/500).
3. **Responsive Verification**: Uji tampilan pada viewport Mobile (375x667), Tablet (768x1024), dan Desktop (1920x1080).
4. **Laporan & Screenshot**: Dokumentasikan anomali layout, kontras warna, atau elemen yang meluap (overflow).

---

## 5. `/release` : Quality Gate & Rilis Versi
**Trigger**: Prompt diawali dengan `/release <versi/tag>`.

### Tahapan Eksekusi:
1. **Quality Gate**:
   - Jalankan linter (`./vendor/bin/pint`, `npm run lint`).
   - Jalankan seluruh automated test suite (`php artisan test`).
2. **Asset Build**:
   - Eksekusi kompilasi aset produksi (`npm run build`).
3. **Environment Audit**:
   - Validasi bahwa `.env.example` sinkron dengan config terbaru dan tidak ada file `.env` yang masuk git tracking.
4. **Changelog**:
   - Buat ringkasan changelog terstruktur (Features, Fixes, Breaking Changes) dari commit log terkini.

---

## 6. `/commit` : Pembuatan Conventional Commit dari Diff
**Trigger**: Prompt diawali dengan `/commit` atau `/commit <konteks opsional>`.

### Tahapan Eksekusi:
1. **Inspect Diff**: Periksa status file staging (`git status`, `git diff --staged` atau `git diff`).
2. **Kategorisasi**: Tentukan type (`feat`, `fix`, `refactor`, `perf`, `test`, `style`, `docs`, `chore`) dan scope.
3. **Formulasi Pesan**: Tulis pesan commit imperatif dalam Bahasa Inggris sesuai format Conventional Commits.
4. **Konfirmasi**: Tampilkan pesan commit yang diusulkan kepada user sebelum melakukan commit.
