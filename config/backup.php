<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Backup Storage & Encryption Configuration (BACKUP-001)
    |--------------------------------------------------------------------------
    |
    | Menentukan direktori penyimpanan lokal, flag enkripsi AES-256-CBC,
    | kunci enkripsi terpisah, batas retensi FIFO, dan disk offsite sekunder.
    |
    */

    'encryption_enabled' => (bool) env('BACKUP_ENCRYPTION_ENABLED', true),

    'encryption_key' => env('BACKUP_ENCRYPTION_KEY', env('APP_KEY')),

    'retention_max_backups' => (int) env('BACKUP_RETENTION_MAX', 30),

    'min_free_disk_mb' => (int) env('BACKUP_MIN_FREE_DISK_MB', 500),

    'offsite_disk' => env('BACKUP_OFFSITE_DISK', null),

    'storage_dir' => storage_path('app/backups'),

    'private_files_dir' => storage_path('app/private'),
];
