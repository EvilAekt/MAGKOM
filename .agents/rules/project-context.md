# Project Context: Fullstack PHP / Laravel Web Development (MAGKOM)

## 1. Stack Overview (Strict & Locked Baseline)
- **Runtime & Language**: **PHP 8.3.x Mutlak** (Kepatuhan strict typing `declare(strict_types=1);`).
- **Framework**: **Laravel 10.x Terkunci** (Jangan melakukan upgrade ke Laravel 11/12).
- **Dependency Manager**: Composer 2.2+ (Platform requirement locked to PHP 8.3).
- **Frontend Assets**: **Vite 6.x**, **Scoped Vanilla CSS (11 Master Peeper Tokens & Dark Mode)**, **Modular Vanilla JavaScript (ES Modules)**, dan **GSAP 3.15.1** (Dilarang menambahkan React, Vue, Tailwind baru di Blade, Bootstrap, atau jQuery).
- **Node & NPM**: Node.js v20.x LTS, NPM 10+.
- **Database**: **MySQL 8.0+ / MariaDB 10.5+ (InnoDB Storage Engine)** untuk local runtime; **SQLite `:memory:`** untuk testing suite otomatis terisolasi.
- **Testing Framework**: **PHPUnit 10.x** (Feature & Unit Tests terisolasi).
- **Code Style & Quality**: **Laravel Pint** (Standar PSR-12) & **Larastan / PHPStan Level 2 (1G memory limit)**.

## 2. Directory Structure
```text
MAGKOM/
├── app/
│   ├── Enums/               # 13 Domain Status & Type Enums
│   ├── Http/
│   │   ├── Controllers/     # Skinny Controllers per namespace (Public, Auth, Peserta, Pembimbing, Admin, SuperAdmin)
│   │   ├── Middleware/      # Auth, Role RBAC, MustChangePassword, AccountActive, SuperAdminReadOnly, SecurityHeaders
│   │   └── Requests/        # Form Request validation classes
│   ├── Models/              # 25 Eloquent Models aktif
│   ├── Policies/            # Kebijakan otorisasi ketat (termasuk SuperAdmin Read-Only)
│   ├── Services/            # 31 Dedicated Domain Services (Placement, Backup, CMS Block, Audit, dll.)
│   └── Providers/           # Service providers
├── bootstrap/               # App bootstrap & cache
├── config/                  # Configuration files
├── database/
│   ├── factories/           # Model factories for testing
│   ├── migrations/          # Database schema migrations (termasuk drop migration untuk modul yang di-decommission)
│   └── seeders/             # Database seeders (13 Modul Sistem Definitif)
├── public/                  # Web root (index.php, compiled assets, logo dinas)
├── resources/
│   ├── css/                 # Scoped Vanilla CSS: base/, components/, layouts/, pages/
│   ├── js/                  # Modular ES Modules: components/, layouts/, pages/, services/
│   └── views/               # Blade template views (Strictly presentation-only, ZERO inline scripts/styles)
├── routes/
│   ├── web.php              # Web routes (Session, CSRF, signed private document downloads)
│   ├── api.php              # Stateless API routes (Sanctum)
│   ├── console.php          # Scheduled tasks & artisan commands
│   └── channels.php         # Realtime broadcast channels
├── storage/                 # Logs, backups (.sql.gz), privat documents (storage/app/private/)
├── tests/
│   ├── Feature/             # HTTP & integration feature tests (SQLite in-memory)
│   └── Unit/                # Unit tests
└── vendor/                  # Composer dependencies
```

## 3. Essential Commands
- **Dev Server**: `php artisan serve --port=8080`
- **Asset Dev Server**: `npm run dev`
- **Asset Production Build**: `npm run build`
- **Database Migrations & Seed**: `php artisan migrate --seed`
- **Testing Suite**: `php artisan test`
- **Code Formatting / Linting**: `./vendor/bin/pint --test` (cek) atau `./vendor/bin/pint` (format)
- **Static Analysis**: `./vendor/bin/phpstan analyse --memory-limit=1G`
- **Route / Config Cache Clear**: `php artisan optimize:clear`
- **Diagnostik Server Internal**: `/superadmin/health-monitor` (Security Sweep, Optimize DB, Clear Cache, Storage Test)
- **Disaster Recovery Backup**: `/superadmin/backup` (Snapshot instan `.sql.gz` dengan checksum SHA-256)
