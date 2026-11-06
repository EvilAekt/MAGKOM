# MagKom Developer & Agent Guide (AGENTS.md)

Selamat datang di repositori **MagKom (Magang Kominfo)** — Portal Manajemen Magang Dinas Komunikasi dan Informatika.
Dokumen ini merupakan panduan utama bagi semua Agen AI (Antigravity IDE, Gemini CLI, Claude Code, Cursor, Copilot, OpenCode) dalam menjalankan tugas pengembangan, pengujian, dan pemeliharaan kode pada repositori ini.

---

## 🎯 Aturan Inti Repositori (Core Rules)

Semua agen wajib mematuhi aturan standar yang tersimpan pada direktori `.agents/rules/`:
1. [**project-context.md**](file:///.agents/rules/project-context.md) : Arsitektur Laravel, struktur direktori, dan perintah esensial.
2. [**backend-security.md**](file:///.agents/rules/backend-security.md) : Keamanan OWASP Top 10, sanitasi input, Eloquent binding, CSRF, signed URLs, dan audit trail.
3. [**code-style.md**](file:///.agents/rules/code-style.md) : Standar PHP 8.3 & PSR-12, type hinting ketat, penamaan, dan *Skinny Controllers*.
4. [**frontend.md**](file:///.agents/rules/frontend.md) : Semantic HTML, Tailwind CSS, WCAG 2.1 AA a11y, CWV, dan mobile responsiveness.
5. [**git-workflow.md**](file:///.agents/rules/git-workflow.md) : Strategi branching dan Conventional Commits imperatif.
6. [**testing.md**](file:///.agents/rules/testing.md) : Metodologi Pest / PHPUnit (AAA: Arrange-Act-Assert) & Feature Testing.

---

## 🛠️ Daftar Skill Agen yang Tersedia (Agent Skills)

Repositori ini telah dikonfigurasi dengan 10 skill agen terstruktur di bawah direktori `.agents/skills/`:

### 1. Developer Workflows & Slash Commands
- [**dev-workflows**](file:///.agents/skills/dev-workflows/SKILL.md) : Prosedur runbook pengembangan harian terstruktur:
  - `/new-feature <deskripsi>` : Perencanaan arsitektur, implementasi bertahap, dan pembuatan test suite.
  - `/fix-bug <deskripsi>` : Reproduksi bug, Root Cause Analysis (RCA), failing test, dan regression testing.
  - `/review [file]` : Tinjauan kode 4 pilar (Bugs, Cybersecurity/OWASP, Performance/N+1, Frontend/a11y).
  - `/ui-check <url>` : Inspeksi otomatis antarmuka, audit console/network, dan responsive check.
  - `/release <versi>` : Quality gate check, kompilasi aset, dan changelog generation.
  - `/commit` : Inspeksi git diff dan pembuatan Conventional Commit imperatif.

### 2. Autonomous Security & Pentesting Skills (Strix & OWASP)
- [**api-security-testing**](file:///.agents/skills/api-security-testing/SKILL.md) : Pengujian keamanan REST/GraphQL API berbasis OWASP API Security Top 10 (2023) (BOLA, IDOR, Mass Assignment, dsb.).
- [**application-security-testing**](file:///.agents/skills/application-security-testing/SKILL.md) : Pengujian keamanan end-to-end aplikasi web secara komprehensif.
- [**ci-security-scanning-with-strix**](file:///.agents/skills/ci-security-scanning-with-strix/SKILL.md) : Integrasi security scanner AI pada CI/CD pre-merge pull request.
- [**find-security-vulnerabilities-in-code**](file:///.agents/skills/find-security-vulnerabilities-in-code/SKILL.md) : White-box code security audit untuk deteksi injection, XSS, SSRF, dan flawed business logic.
- [**fix-security-vulnerabilities-with-strix**](file:///.agents/skills/fix-security-vulnerabilities-with-strix/SKILL.md) : Panduan perbaikan celah keamanan berdasarkan hasil temuan audit dan verifikasi ulang exploit.
- [**managed-pentesting-with-strix**](file:///.agents/skills/managed-pentesting-with-strix/SKILL.md) : Menjalankan cloud-managed penetration testing tanpa beban lokal.
- [**owasp-top-10-testing**](file:///.agents/skills/owasp-top-10-testing/SKILL.md) : Pengujian kepatuhan terhadap OWASP Top 10 (2025) & OWASP API Top 10 (2023).
- [**penetration-testing-with-strix**](file:///.agents/skills/penetration-testing-with-strix/SKILL.md) : Kerangka kerja penetrasi dan eksploitasi PoC mandiri.
- [**web-app-penetration-testing**](file:///.agents/skills/web-app-penetration-testing/SKILL.md) : Black-box DAST pentesting pada web live/staging.

---

## 📚 Dokumentasi Proyek (Single Source of Truth)

Seluruh spesifikasi teknis dan bisnis proyek tersimpan di dalam folder `docs/`:
- `docs/01-PRD-MagKom.md` : PRD v1.1, 17 Aturan Bisnis (BR-01 s/d BR-17), User Stories.
- `docs/02-TDD-MagKom.md` : TDD v1.1, Laravel 10 / PHP 8.3, atomic quota locking (`lockForUpdate`), Audit Trail.
- `docs/03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md` : Tahapan rilis Wave 0 s/d Wave 4.
- `docs/04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md` : ERD Mermaid & kamus data 20 tabel.
- `docs/05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md` : Matriks RBAC 4 role & isolasi SuperAdmin Read-Only.
- `docs/06-ADDENDUM-KEAMANAN-DAN-QA.md` : Model ancaman & matriks QA.
- `docs/07-DESIGN-SYSTEM-MAGKOM.md` : Spesifikasi token warna, komponen, dan WCAG 2.1 AA.
