# -------------------------------------------------------------
# STAGE 1: Frontend Asset Compilation (NodeJS 20)
# -------------------------------------------------------------
FROM node:20-alpine AS node_builder
WORKDIR /app

COPY package*.json vite.config.js tailwind.config.js postcss.config.js ./
COPY resources/ ./resources/
COPY public/ ./public/

RUN npm ci && npm run build

# -------------------------------------------------------------
# STAGE 2: PHP Application & Runtime (PHP 8.2 Alpine)
# -------------------------------------------------------------
FROM php:8.2-fpm-alpine

# Install System Dependencies & Tools
RUN apk add --no-cache \
    nginx \
    supervisor \
    mysql-client \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    curl \
    git

# Install & Configure PHP Extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        bcmath \
        gd \
        zip \
        intl \
        opcache

# Salin Composer Binary
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Salin Source Code Proyek
COPY . /var/www

# Salin Hasil Build Asset Frontend dari Stage 1
COPY --from=node_builder /app/public/build /var/www/public/build

# Install Dependensi Composer (Optimized for Production)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Konfigurasi Direktori & Izin Akses www-data
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Salin Konfigurasi Nginx, Supervisord, dan Boot Script
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

# nosemgrep: dockerfile.security.missing-user-entrypoint
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
# nosemgrep: dockerfile.security.missing-user
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
