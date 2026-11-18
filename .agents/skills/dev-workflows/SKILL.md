---
name: dev-workflows
description: >-
  Triggered when the user invokes /new-feature, /fix-bug, /review, /ui-check, /release, /commit, /test-web, /security-scan, /design-review, or /monitor.
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
   - Paparkan rencana singkat kepada user sebelum implementasi (gunakan `web-planning`).
2. **Implementasi (Implementation)**:
   - Tulis kode secara bertahap dan terfokus.
   - Patuhi aturan di `code-style.md`, `backend-security.md`, dan `design-skills-routing.md`.
3. **Pengujian (Testing)**:
   - Buat skenario pengujian Feature / Unit test (`php artisan test`).
   - Jalankan `/test-web` jika menyentuh interaksi frontend kompleks.
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

## 3. `/review` : Tinjauan Kualitas & Keamanan Kode Terintegrasi
**Trigger**: Prompt diawali dengan `/review` atau `/review <file/fitur>`.

### Tahapan Eksekusi:
Evaluasi kode berdasarkan integrasi pilar standar proyek:
1. **Bugs & Logic Flaws**: Deteksi null pointer, edge cases, off-by-one errors, atau unhandled exceptions.
2. **Design & Interface Review**: Picu `/design-review` (quick) jika ada perubahan UI Blade/CSS/JS untuk memeriksa a11y, layout, dan hierarki visual.
3. **Cybersecurity & OWASP**: Picu `/security-scan` (quick) untuk memverifikasi potensi IDOR, CSRF, sanitasi input, dan parameter binding.
4. **Performance & Optimization**: Deteksi potensi N+1 query Eloquent, indexing database yang hilang, dan render blocking assets.
*Output*: Berikan kesimpulan berbobot (Pass / Needs Changes / Critical Blockers) beserta rekomendasi aksi konkret.

---

## 4. `/ui-check` : Inspeksi Antarmuka & Browser Automation
**Trigger**: Prompt diawali dengan `/ui-check <url atau route>`.

### Tahapan Eksekusi:
1. **Navigasi Browser**: Buka halaman menggunakan browser automation (Playwright MCP atau Chrome DevTools).
2. **Console & Network Audit**: Periksa apakah ada JavaScript error, unhandled promise rejection, atau request gagal (404/500).
3. **Responsive Verification**: Uji tampilan pada viewport Mobile (375x667), Tablet (768x1024), dan Desktop (1440x900).
4. **Laporan & Screenshot**: Dokumentasikan anomali layout, kontras warna, atau elemen yang meluap (overflow).

---

## 5. `/release` : Quality Gate & Rilis Versi
**Trigger**: Prompt diawali dengan `/release <versi/tag>`.

### Tahapan Eksekusi:
1. **Quality Gates Terintegrasi**:
   - Jalankan linter (`./vendor/bin/pint`, `npm run lint`).
   - Jalankan seluruh automated test suite (`php artisan test`).
   - Jalankan `/test-web` untuk memastikan fungsionalitas end-to-end tidak regresi.
   - Jalankan `/security-scan` (quick) untuk memastikan tidak ada celah keamanan baru.
   - Jalankan `/design-review` (quick) untuk memastikan aksesibilitas (WCAG AA) terpenuhi.
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

---

## 7. `/test-web` : Pengujian Otomatis Berbasis AI (TestSprite)
**Trigger**: Prompt diawali dengan `/test-web` atau `/test-web <url/skenario>`.

### Tahapan Eksekusi:
1. **Cek Dev Server**: Periksa apakah server dev lokal berjalan (misal `http://127.0.0.1:8000`). Jika belum, konfirmasi ke pengguna sebelum menjalankan.
2. **Eksekusi Pengujian**: Panggil TestSprite MCP (`@testsprite/testsprite-mcp`) untuk menghasilkan skenario uji dan menjalankan validasi alur interaksi pengguna.
3. **Ringkasan Hasil**:
   - Tampilkan matriks pengujian: Skenario Berhasil (Passed), Gagal (Failed), dan Edge Cases yang terdeteksi.
   - **Guardrail**: DILARANG mengubah kode secara otomatis. Usulkan perbaikan melalui dokumen rencana (`web-planning`).

---

## 8. `/security-scan` : Penetration Testing AI Cepat (Strix)
**Trigger**: Prompt diawali dengan `/security-scan` atau `/security-scan <target-lokal>`.

### Tahapan Eksekusi:
1. **Validasi Target**: Pastikan target HANYA `localhost` atau URL staging milik sendiri (`tooling-safety.md`). Dilarang memindai domain pihak ketiga.
2. **Jalankan Mode Quick**:
   - Jalankan Strix mode cepat: `strix run --target http://127.0.0.1:8000 --quick` (atau via skill `penetration-testing-with-strix`).
3. **Laporan Temuan**:
   - Kelompokkan temuan berdasarkan severity (Critical, High, Medium, Low).
   - Sertakan Proof-of-Concept (PoC) singkat jika ditemukan eksploitasi valid.
   - **Guardrail**: HANYA melaporkan dan mengusulkan langkah remediasi; tidak melakukan auto-patching tanpa persetujuan.

---

## 9. `/design-review` : Audit & Polish Antarmuka Terpadu
**Trigger**: Prompt diawali dengan `/design-review` atau `/design-review <halaman/komponen>`.

### Tahapan Eksekusi:
1. **Audit Heuristik & Polish (`impeccable`)**:
   - Buka halaman target dan jalankan audit estetika, hierarki visual, kontras elemen, dan konsistensi token desain.
2. **Review Detail Spesifik (`jakubkrehel/interface-review`)**:
   - Pindai pelanggaran aksesibilitas WCAG 2.2 (`better-accessibility`).
   - Periksa konsistensi tipografi & optical alignment (`better-typography` & `better-ui`).
   - Periksa fluiditas layout responsif & overflow (`better-layout`).
3. **Daftar Temuan Berprioritas**:
   - Sajikan laporan: Blocker Aksesibilitas, Anomali Tata Letak, dan Rekomendasi Peningkatan Visual.

---

## 10. `/monitor` : Observabilitas & Telemetri Real-Time
**Trigger**: Prompt diawali dengan `/monitor` atau `/monitor <periode>`.

### Tahapan Eksekusi:
1. **Inspeksi Sentry**: Panggil Sentry MCP resmi (`https://mcp.sentry.dev/mcp/evill`) untuk mengambil daftar exception dan error teratas yang belum diselesaikan (*unresolved issues*).
2. **Inspeksi Datadog**: Query metrik latency, error rate, dan APM trace melalui Datadog MCP jika akun aktif.
3. **Inspeksi Cloudflare**: Periksa analitik traffic atau status WAF jika mengaktifkan Cloudflare.
4. **Laporan Eksekutif**:
   - Sajikan ringkasan insiden aktif, tren lonjakan error, dan rekomendasi investigasi akar masalah.
   - **Guardrail**: Seluruh tindakan bersifat read-only; dilarang me-resolve issue tanpa konfirmasi.
