> Diperbarui: 2026-11-09 | mode: Verified Living Documentation

# Arsitektur Proyek: Portal MagKom (Magang Kominfo)

Portal MagKom adalah aplikasi web terintegrasi untuk manajemen dan transparansi program magang pada Dinas Komunikasi dan Informatika (Kominfo). Sistem melayani 4 peran pengguna utama: **Peserta**, **Pembimbing Lapangan**, **Administrator**, dan **Super Administrator**, dengan fokus pada transparansi kuota penerimaan, tata kelola berkas, monitoring logbook harian, serta tata kelola landing page publik berbasis Gutenberg Visual Site Editor.

---

## 1. Ringkasan Eksekutif & Stack Teknologi

| Komponen | Teknologi | Keterangan Versi & Konfigurasi |
| :--- | :--- | :--- |
| **Bahasa & Runtime** | PHP 8.3.x / Node.js 20.x+ | Strict typing `declare(strict_types=1)` diaktifkan pada controller dan service |
| **Framework Backend** | Laravel 10.x | Arsitektur MVC + Dedicated Domain Service Layer (`app/Services`) |
| **Frontend Rendering** | Blade Templates | Komponen Blade modular, native CSS variables, Vite bundler |
| **Styling & Design System**| Custom Design System (DS-MagKom) + Vanilla CSS | Palet warna korporat Kominfo (Blue 600 `#0751FF`), tipografi Open Sans & Plus Jakarta Sans |
| **Database & ORM** | MySQL 8.0+ / MariaDB (Eloquent ORM) | 24 Tabel Domain MAGKOM + 5 Tabel Framework Laravel dengan audit log trail dan foreign keys ketat |
| **Keamanan & Auth** | Laravel Session + Sanctum + Socialite (Google OAuth) | Multi-guard RBAC, rate-limiting kustom, CSRF protection, signed URLs |
| **Realtime Engine** | Server-Sent Events (SSE) | HTTP streaming mandiri (`/realtime/stream`) via `RealtimeStreamController` |
| **Reporting & Export** | Barryvdh DomPDF & ZipArchive | Penerbitan PDF Surat Balasan Penerimaan/Penolakan dan bundel berkas ZIP |
| **Disaster Recovery** | Spatie Backup & Native Service | Backup terenkripsi/terkompresi `.sql.gz` dengan verifikasi hash SHA-256 |
| **Testing Suite** | PHPUnit 10.x | 54 Feature Test suites + Automated Security Deep Defense Sweep (SQLite in-memory) |

---

## 2. Diagram Arsitektur Tingkat Tinggi

```mermaid
graph TB
    subgraph ClientLayer ["Client & Interface Layer"]
        PublicUser["Publik & Calon Peserta"]
        PesertaUser["Peserta Magang"]
        PembimbingUser["Pembimbing Lapangan"]
        AdminUser["Admin & SuperAdmin"]
    end

    subgraph SecurityLayer ["Security & Ingress Layer"]
        RateLimiter["Rate Limiter & Throttle"]
        AuthMiddleware["Auth & Verification Guard"]
        RbacMiddleware["RBAC (role:peserta,pembimbing,admin,superadmin)"]
        SignedUrlCheck["Signed URL Verifier (Berkas Privat)"]
    end

    subgraph AppLayer ["Application Core (Laravel 10)"]
        Controllers["HTTP Controllers (app/Http/Controllers)"]
        ServiceLayer["Domain Services (app/Services)"]
        SseEngine["SSE Realtime Stream Engine"]
        PdfGenerator["DomPDF Letter & Report Engine"]
        BackupEngine["Backup & Disaster Recovery Service"]
    end

    subgraph DataStorageLayer ["Data & Storage Layer"]
        MysqlDB[(MySQL Database: 24 Domain + 5 Framework)]
        PrivateStorage[("Private Storage (storage/app/berkas)")]
        AuditTrail[(Audit Log Records: audit_log)]
    end

    PublicUser --> RateLimiter
    PesertaUser --> AuthMiddleware
    PembimbingUser --> AuthMiddleware
    AdminUser --> AuthMiddleware

    AuthMiddleware --> RbacMiddleware
    RateLimiter --> Controllers
    RbacMiddleware --> Controllers
    SignedUrlCheck --> Controllers

    Controllers --> ServiceLayer
    ServiceLayer --> SseEngine
    ServiceLayer --> PdfGenerator
    ServiceLayer --> BackupEngine
    ServiceLayer --> MysqlDB
    ServiceLayer --> AuditTrail
    Controllers --> PrivateStorage
```

---

## 3. Struktur Direktori Beranotasi

```text
MAGKOM/
├── app/
│   ├── Console/Commands/       # Perintah Artisan kustom (AnonimkanPeserta, dll.)
│   ├── Enums/                  # PHP 8.1+ Enums (RealtimeEventTypeEnum, StatusPendaftaran, dll.)
│   ├── Http/
│   │   ├── Controllers/        # Controller terbagi per domain: Admin, Auth, Pembimbing, Peserta, Public, SuperAdmin
│   │   ├── Middleware/         # Guard RBAC, verifikasi email, proteksi password staf, throttle
│   │   └── Requests/           # Validasi form terpusat (Form Request validation)
│   ├── Models/                 # 25 Eloquent Models aktif dengan audit hooks dan relasi
│   ├── Providers/              # Service providers (RouteServiceProvider, AppServiceProvider)
│   └── Services/               # Domain Services (PlacementService, AuditService, GutenbergBlockService, BackupService, dll.)
├── database/
│   ├── factories/              # Factory data uji untuk automated testing
│   ├── migrations/             # 35 file migrasi database (skema terstruktur 2026 pasca-decommissioning)
│   └── seeders/                # Database seeder (akun default, master jurusan, izin sistem, pengaturan)
├── docs/
│   ├── architecture/           # [READ-ONLY INSPECTOR] Dokumentasi struktural sistem ini
│   ├── plans/                  # Dokumen rencana teknis (web-planning)
│   └── *.md                    # Spesifikasi PRD, TDD, Keamanan, Skema DB, dan Design System resmi
├── public/                     # Web root: index.php, assets build, security.txt RFC 9116
├── resources/
│   ├── css/                    # Entry CSS Vite (app.css, pages/, layouts/)
│   ├── js/                     # Client JavaScript untuk interaksi form, visual editor, dan SSE listener
│   └── views/                  # Blade template hierarkis (layouts, components, admin, pembimbing, peserta, superadmin)
├── routes/
│   ├── web.php                 # Rute utama aplikasi berorientasi session (130+ endpoints)
│   └── api.php                 # Rute RESTful API / Sanctum
├── storage/                    # Tempat penyimpanan berkas privat peserta, PDF, dan cache
└── tests/
    ├── Feature/                # 54 file pengujian integrasi alur bisnis (UAT, RBAC, IDOR, Lifecycle)
    └── Unit/                   # Unit test komponen dasar
```

---

## 4. Filosofi Desain & Alur Bisnis Utama

Sistem dirancang dengan pemisahan tanggung jawab yang ketat (*Separation of Concerns*):
1. **Controller Ramping, Service Gemuk**: Controller hanya menerima input HTTP, memanggil Form Request untuk validasi, mendelegasikan ke `app/Services`, dan mengembalikan view atau JSON.
2. **Keamanan Berkas Privat**: Berkas lamaran dan identitas (KTP, KTM, Surat Pengantar) tidak diletakkan di `public/`, melainkan di private storage yang hanya bisa diunduh melalui `temporarySignedRoute` dengan batasan waktu aktif.
3. **Audit Trail Otomatis**: Setiap aksi sensitif (perubahan kuota, keputusan penolakan/penerimaan, persetujuan tugas, pembuatan user, pemulihan backup) dicatat ke tabel `audit_log` via `AuditService`.
4. **Alokasi Kuota Atomik & Seleksi Tegas**: Menggunakan pesimistic database locking (`lockForUpdate()`) untuk mencegah overbooking kuota bulanan tanpa fasilitas *Waiting List* (pendaftaran ditolak tegas jika kuota habis).
5. **Penyelesaian Bimbingan 1-Klik**: Pembimbing lapangan dapat menyelesaikan bimbingan peserta secara langsung melalui tombol 1-klik yang mengotomatisasi update status peserta dan arsip data.

---

## 5. Indeks Referensi Terkait

- [Dokumentasi Rute Lengkap](./ROUTES.md)
- [Panduan Standar Pengembangan](./DEV-GUIDE.md)
- [Arsitektur Styling & Aset](./STYLING.md)
- [Spesifikasi PRD Terpadu](../01-PRD-MagKom.md)
- [Dokumen Arsitektur Teknis TDD](../02-TDD-MagKom.md)
- [Skema Basis Data & Data Dictionary](../04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md)
