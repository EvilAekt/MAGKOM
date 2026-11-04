import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/pages/public/landing.css',
                'resources/css/pages/public/landing-faq.css',
                'resources/css/pages/public/landing-peta-alur-modal.css',
                'resources/css/pages/public/panduan-alur.css',
                'resources/css/pages/public/peta-alur.css',
                'resources/css/pages/superadmin/dashboard.css',
                'resources/css/pages/superadmin/audit-log.css',
                'resources/css/pages/superadmin/modul-builder.css',
                'resources/css/pages/admin/dashboard.css',
                'resources/css/pages/peserta/tugas-detail.css',
                'resources/css/pages/peserta/tugas-index.css',
                'resources/css/pages/peserta/logbook-index.css',
                'resources/css/pages/pembimbing/tugas-create.css',
                'resources/js/pages/public/landing.js',
                'resources/js/pages/public/panduan-alur.js',
                'resources/js/pages/public/peta-alur.js',
                'resources/js/pages/superadmin/dashboard.js',
                'resources/js/pages/peserta/tugas-detail.js',
                'resources/js/pages/peserta/tugas-index.js',
                'resources/js/pages/peserta/logbook-index.js',
                'resources/js/pages/pembimbing/tugas-create.js',
                'resources/js/services/realtime.js',
            ],
            refresh: true,
        }),
    ],
});
