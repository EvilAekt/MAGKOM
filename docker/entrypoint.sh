#!/bin/sh
set -e

echo "[MAGKOM-BOOT] Mempersiapkan lingkungan aplikasi..."

# Buat symbolic link storage jika belum ada
if [ ! -L /var/www/public/storage ]; then
    echo "[MAGKOM-BOOT] Membuat symbolic link public/storage..."
    php /var/www/artisan storage:link || true
fi

# Jika APP_KEY belum disetel, buat otomatis (untuk inisialisasi awal container)
if [ -z "$APP_KEY" ]; then
    echo "[MAGKOM-BOOT] Menghasilkan APP_KEY baru..."
    php /var/www/artisan key:generate --force || true
fi

# Jalankan caching konfigurasi & rute jika di mode produksi
if [ "$APP_ENV" = "production" ]; then
    echo "[MAGKOM-BOOT] Mengaktifkan cache produksi (config, route, view)..."
    php /var/www/artisan config:cache || true
    php /var/www/artisan route:cache || true
    php /var/www/artisan view:cache || true
fi

echo "[MAGKOM-BOOT] Memulai supervisord (Nginx, PHP-FPM, Scheduler, Worker)..."
exec "$@"
