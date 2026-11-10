# Project Context: Fullstack PHP / Laravel Web Development

## 1. Stack Overview
- **Runtime & Language**: PHP 8.2+
- **Framework**: Laravel 11 / 10
- **Dependency Manager**: Composer 2.x
- **Frontend Assets**: Vite, Tailwind CSS, Blade Templates / JavaScript / TypeScript
- **Node & NPM**: Node.js v24.x, npm 11.x
- **Database**: MySQL / MariaDB / PostgreSQL / SQLite

## 2. Directory Structure
```text
MAGKOM/
├── app/
│   ├── Http/
│   │   ├── Controllers/    # Application controllers
│   │   ├── Middleware/     # Request middleware
│   │   └── Requests/       # Form Request validation classes
│   ├── Models/             # Eloquent models
│   ├── Services/           # Business logic & domain services
│   └── Providers/          # Service providers
├── bootstrap/              # App bootstrap & cache
├── config/                 # Configuration files
├── database/
│   ├── factories/          # Model factories for testing
│   ├── migrations/         # Database schema migrations
│   └── seeders/            # Database seeders
├── public/                 # Web root (index.php, compiled assets)
├── resources/
│   ├── css/                # Stylesheets (Tailwind CSS)
│   ├── js/                 # Client scripts
│   └── views/              # Blade template views
├── routes/
│   ├── web.php             # Web routes (Session, CSRF)
│   └── api.php             # Stateless API routes (Sanctum)
├── storage/                # Logs, uploads, cache
├── tests/
│   ├── Feature/            # HTTP & integration tests
│   └── Unit/               # Unit tests
└── vendor/                 # Composer dependencies
```

## 3. Essential Commands
- **Dev Server**: `php artisan serve`
- **Asset Dev Server**: `npm run dev`
- **Asset Production Build**: `npm run build`
- **Database Migrations**: `php artisan migrate`
- **Code Generation**: `php artisan make:model <Name> -mcr`
- **Testing**: `php artisan test` atau `./vendor/bin/pest` / `./vendor/bin/phpunit`
- **Code Formatting / Linting**: `./vendor/bin/pint`
- **Route / Config Cache Clear**: `php artisan optimize:clear`
