# Project Knowledge & Memory Base: MAGKOM

Dokumen ini adalah ringkasan memori dan konteks proyek untuk AI Agents di **Sokudo IDE**.

## 1. Arsitektur & Aturan Proyek
- **Tech Stack**: Laravel 11, PHP 8.2+, MySQL (Laragon local runtime), Blade, Tailwind / Gentelella Admin.
- **Project Rules**: Tersedia di folder .agents/rules/ dan docs/wiki/project-rules/.
- **Knowledge Graph Codebase**: Lihat [CODEBASE_GRAPH_REPORT.md](./CODEBASE_GRAPH_REPORT.md) untuk relasi modul, controller, view, dan komunitas node.

## 2. Model & Relasi Utama
- **User** (Roles: Superadmin, Admin, Instruktur, Peserta, Reviewer)
- **Activity & Audit Logs**: Riwayat aksi user di /superadmin/audit-logs
- **Dashboard Modul**: Dilengkapi cockpit monitoring, metric cards, dan layout Gentelella modern.

## 3. Skills yang Terhubung
Semua agent skills telah diekspor ke global ~/.agents/skills/ dan workspace .agents/skills/.
Gunakan skill seperti web-planning, web-implementation, ui-ux-pro-max, impeccable, dan security scanning Strix sesuai kebutuhan.
