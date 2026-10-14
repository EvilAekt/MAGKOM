<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Portal MagKom - Autentikasi')</title>
    <meta name="description" content="@yield('meta_description', 'Portal Magang Resmi Dinas Komunikasi dan Informatika Kabupaten Sleman.')">

    <!-- Google Fonts: Open Sans (Design System DS-MagKom Bab 2.2) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,400;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">

    <style>
        :root {
            /* MagKom / Warna Tokens (Design System DS-MagKom Bab 2.1) */
            --blue-600: #0751FF;
            --blue-700: #0641CC;
            --blue-50: #EEF4FF;
            --blue-100: #E0EBFF;
            --blue-tint: #E6EEFF;
            --blue-tint-line: #B3C8FF;

            --neutral-0: #FFFFFF;
            --neutral-50: #F8FAFC;
            --neutral-100: #F1F5F9;
            --neutral-200: #E2E8F0;
            --neutral-300: #CBD5E1;
            --neutral-400: #94A3B8;
            --neutral-500: #64748B;
            --neutral-600: #475569;
            --neutral-700: #334155;
            --neutral-800: #1E293B;
            --neutral-900: #0F172A;

            /* Status Tokens */
            --green-50: #F0FDF4;
            --green-500: #22C55E;
            --green-600: #16A34A;
            --green-700: #15803D;

            --red-50: #FEF2F2;
            --red-500: #EF4444;
            --red-600: #DC2626;
            --red-700: #B91C1C;
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Open Sans', system-ui, -apple-system, sans-serif;
            background-color: var(--neutral-0);
            color: var(--neutral-800);
            min-height: 100vh;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        /* Layout Grid Split-Screen */
        .auth-container {
            display: grid;
            grid-template-columns: 1fr;
            min-height: 100vh;
            width: 100%;
        }

        @media (min-width: 1024px) {
            .auth-container {
                grid-template-columns: 1fr 1fr;
            }
        }

        /* Panel Kiri (Visual & Branding) */
        .auth-visual-panel {
            display: none;
            position: relative;
            background-image: url('{{ asset('images/auth_office_bg.png') }}');
            background-size: cover;
            background-position: center;
            overflow: hidden;
            color: #ffffff;
            padding: 3.5rem 4rem;
            flex-direction: column;
            justify-content: space-between;
        }

        @media (min-width: 1024px) {
            .auth-visual-panel {
                display: flex;
            }
        }

        .auth-visual-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.76) 0%, rgba(29, 37, 48, 0.86) 100%);
            z-index: 1;
        }

        .auth-visual-content {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }

        .brand-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.625rem;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 9999px;
            padding: 0.35rem 0.85rem;
            font-size: 0.8125rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            color: #ffffff;
            width: fit-content;
        }

        .brand-pill-dot {
            width: 8px;
            height: 8px;
            background: #4ade80;
            border-radius: 50%;
            box-shadow: 0 0 10px #4ade80;
        }

        .visual-headline {
            margin-top: 0;
            margin-bottom: 1rem;
            font-size: 2.25rem;
            font-weight: 800;
            line-height: 1.2;
            color: #ffffff;
            letter-spacing: -0.02em;
        }

        .visual-center-content {
            margin: auto 0;
        }

        .visual-bottom-group {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .visual-subtext {
            font-size: 1.0625rem;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.88);
            max-width: 520px;
        }

        .visual-features {
            display: flex;
            flex-direction: column;
            gap: 1.125rem;
            margin-top: 2.5rem;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 0.875rem;
        }

        .feature-icon-box {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            flex-shrink: 0;
            color: #ffffff;
        }

        .feature-text-title {
            font-size: 0.9375rem;
            font-weight: 700;
            color: #ffffff;
        }

        .feature-text-desc {
            font-size: 0.8125rem;
            color: rgba(255, 255, 255, 0.8);
        }

        .visual-footer {
            font-size: 0.8125rem;
            color: rgba(255, 255, 255, 0.75);
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
        }

        /* Panel Kanan (Form Interaktif) */
        .auth-form-panel {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background-color: var(--neutral-0);
            min-height: 100vh;
            padding: 2.5rem 1.5rem;
            overflow-y: auto;
        }

        @media (min-width: 640px) {
            .auth-form-panel {
                padding: 3rem 2.5rem;
            }
        }

        .auth-form-header-bar {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            width: 100%;
            margin-bottom: 2rem;
        }

        @media (max-width: 1023px) {
            .auth-form-header-bar {
                justify-content: space-between;
            }
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--neutral-700);
            background-color: var(--neutral-0);
            border: 1px solid var(--neutral-200);
            border-radius: 9999px;
            padding: 0.45rem 1rem;
            text-decoration: none;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .back-link:hover {
            color: var(--neutral-900);
            background-color: var(--neutral-50);
            border-color: var(--neutral-300);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            transform: translateY(-1px);
        }

        .back-link:active {
            transform: translateY(0);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .back-link svg {
            transition: transform 0.2s ease;
        }

        .back-link:hover svg {
            transform: translateX(-3px);
        }

        .mobile-brand {
            display: flex;
            align-items: center;
            gap: 0.625rem;
        }

        @media (min-width: 1024px) {
            .mobile-brand {
                display: none;
            }
        }

        .auth-form-wrapper {
            max-width: 480px;
            width: 100%;
            margin: auto;
        }

        /* Typography & Form Elements */
        .auth-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--neutral-900);
            line-height: 1.25;
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }

        .auth-subtitle {
            font-size: 0.9375rem;
            color: var(--neutral-500);
            margin-bottom: 2rem;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--neutral-700);
            margin-bottom: 0.5rem;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon-left {
            position: absolute;
            left: 1rem;
            color: var(--neutral-400);
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-input {
            width: 100%;
            height: 48px;
            padding: 0 1rem 0 2.85rem;
            font-size: 1rem;
            font-family: inherit;
            color: var(--neutral-900);
            background-color: var(--neutral-0);
            border: 1px solid var(--neutral-300);
            border-radius: 0.5rem;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .form-input.no-icon {
            padding-left: 1rem;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--blue-600);
            box-shadow: 0 0 0 3px rgba(7, 81, 255, 0.15);
        }

        .toggle-password-btn {
            position: absolute;
            right: 0.75rem;
            background: none;
            border: none;
            color: var(--neutral-400);
            cursor: pointer;
            padding: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.25rem;
            transition: color 0.15s ease;
        }

        .toggle-password-btn:hover {
            color: var(--neutral-700);
        }

        .form-hint {
            display: block;
            font-size: 0.8125rem;
            color: var(--neutral-500);
            margin-top: 0.375rem;
        }

        /* Buttons */
        .btn-primary {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 48px;
            background-color: var(--blue-600);
            color: #ffffff;
            font-size: 1rem;
            font-weight: 700;
            font-family: inherit;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: background-color 0.15s ease, transform 0.05s ease;
        }

        .btn-primary:hover {
            background-color: var(--blue-700);
        }

        .btn-primary:active {
            transform: scale(0.99);
        }

        .btn-google {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            width: 100%;
            height: 48px;
            background-color: #ffffff;
            color: var(--neutral-700);
            font-size: 0.9375rem;
            font-weight: 600;
            font-family: inherit;
            border: 1px solid var(--neutral-300);
            border-radius: 0.5rem;
            text-decoration: none;
            cursor: pointer;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
            transition: background-color 0.15s ease, border-color 0.15s ease;
        }

        .btn-google:hover {
            background-color: var(--neutral-50);
            border-color: var(--neutral-400);
        }

        .auth-divider {
            display: flex;
            align-items: center;
            margin: 1.25rem 0;
            text-align: center;
        }

        .auth-divider::before,
        .auth-divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid var(--neutral-200);
        }

        .auth-divider span {
            padding: 0 1rem;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--neutral-400);
            text-transform: lowercase;
        }

        /* Alerts & Toasts */
        .alerts-container {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 1.5rem;
            width: 100%;
        }

        .app-toast,
        .app-alert {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 14px 18px;
            background: #FFFFFF;
            border: 1px solid var(--neutral-200);
            border-radius: 14px;
            box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.06), 0 2px 6px -1px rgba(15, 23, 42, 0.04);
            position: relative;
            transition: all 0.25s ease;
            text-align: left;
        }

        .app-toast.toast-hiding,
        .app-alert.toast-hiding {
            opacity: 0;
            transform: translateY(-8px);
        }

        .toast-icon-badge,
        .alert-icon-badge {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .toast-icon-badge.success,
        .alert-icon-badge.success {
            background: #ECFDF5;
            color: #10B981;
        }

        .toast-icon-badge.error,
        .alert-icon-badge.error {
            background: #FEF2F2;
            color: #EF4444;
        }

        .toast-icon-badge.warning,
        .alert-icon-badge.warning {
            background: #FFFBEB;
            color: #F59E0B;
        }

        .toast-icon-badge.info,
        .alert-icon-badge.info {
            background: #EFF6FF;
            color: #3B82F6;
        }

        .toast-content,
        .alert-body {
            flex: 1;
            min-width: 0;
            padding-top: 1px;
        }

        .toast-title,
        .alert-title {
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--neutral-900);
            line-height: 1.35;
            margin-bottom: 2px;
        }

        .toast-desc,
        .alert-desc {
            font-size: 0.8125rem;
            font-weight: 400;
            color: var(--neutral-500);
            line-height: 1.45;
        }

        .toast-desc ul,
        .alert-desc ul {
            margin: 4px 0 0 16px;
            padding: 0;
        }

        .toast-close-btn,
        .alert-close-btn {
            background: transparent;
            border: none;
            cursor: pointer;
            color: var(--neutral-400);
            padding: 4px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: auto;
            flex-shrink: 0;
            transition: all 0.15s ease;
        }

        .toast-close-btn:hover,
        .alert-close-btn:hover {
            color: var(--neutral-600);
            background: var(--neutral-100);
        }

        .auth-footer-text {
            text-align: center;
            font-size: 0.875rem;
            color: var(--neutral-500);
            margin-top: 1.75rem;
        }

        .auth-footer-text a {
            color: var(--blue-600);
            font-weight: 700;
            text-decoration: none;
            transition: text-decoration 0.15s ease;
        }

        .auth-footer-text a:hover {
            text-decoration: underline;
        }

        /* Visually Hidden tapi tetap dibaca screen reader (WCAG 2.1) */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        .step-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background-color: var(--blue-50);
            color: var(--blue-700);
            border: 1px solid var(--blue-tint-line);
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <!-- Panel Kiri (Visual & Branding Sleman Presisi Figma) -->
        <aside class="auth-visual-panel" aria-label="Branding MagKom Kominfo Sleman">
            <div class="auth-visual-overlay"></div>
            <div class="auth-visual-content">
                <!-- Header Visual -->
                <!-- Header Visual (Logo Tetap Asli) -->
                <div style="display: flex; align-items: center; gap: 0.875rem;">
                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="40" height="40" rx="10" fill="#ffffff" fill-opacity="0.2"/>
                        <path d="M12 28V12L20 20L28 12V28" stroke="#ffffff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <div>
                        <div style="font-size: 1.125rem; font-weight: 800; letter-spacing: 0.05em; line-height: 1.2;">MAGKOM</div>
                        <div style="font-size: 0.75rem; font-weight: 600; opacity: 0.85; letter-spacing: 0.02em;">DINAS KOMINFO SLEMAN</div>
                    </div>
                </div>

                <!-- Bagian Tengah: Headline & Deskripsi (Center Vertikal) -->
                <div class="visual-center-content">
                    <h1 class="visual-headline">
                        DINAS KOMUNIKASI DAN INFORMATIKA
                    </h1>
                    <p class="visual-subtext">
                        Platform digital terintegrasi untuk administrasi, pemantauan, dan penilaian program magang siswa SMK dan mahasiswa di Kabupaten Sleman.
                    </p>
                </div>

                <!-- Bagian Bawah: Pill di atas Copyright Footer -->
                <div class="visual-bottom-group">
                    <div class="brand-pill">
                        <span class="brand-pill-dot"></span>
                        <span>Sistem Magang Terpadu Sleman</span>
                    </div>

                    <div class="visual-footer">
                        &copy; {{ date('Y') }} Dinas Komunikasi dan Informatika Kabupaten Sleman. Seluruh hak cipta dilindungi.
                    </div>
                </div>
            </div>
        </aside>

        <!-- Panel Kanan (Form Interaktif) -->
        <main class="auth-form-panel">
            <!-- Header Bar (Back Link & Mobile Brand) -->
            <div class="auth-form-header-bar">
                <div class="mobile-brand">
                    <svg width="28" height="28" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="40" height="40" rx="8" fill="#0751FF"/>
                        <path d="M12 28V12L20 20L28 12V28" stroke="#ffffff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span style="font-weight: 800; font-size: 0.9375rem; color: var(--neutral-900);">MAGKOM</span>
                </div>

                <a href="{{ route('landing') }}" class="back-link" title="Kembali ke beranda">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    <span>Kembali ke beranda</span>
                </a>
            </div>

            <!-- Konten Form Dinamis -->
            <div class="auth-form-wrapper">
                <div class="alerts-container">
                    <x-flash-alert />
                </div>
                @yield('content')
            </div>

            <!-- Footer Hak Cipta Ringkas Mobile -->
            <div style="max-width: 480px; width: 100%; margin: 1.5rem auto 0 auto; text-align: center; font-size: 0.75rem; color: var(--neutral-400);">
                Portal Magang Resmi Dinas Kominfo Sleman &bull; v1.0
            </div>
        </main>
    </div>

    <!-- Script Pembantu Show / Hide Password & Toast Dismiss -->
    <script>
        function dismissToast(btn) {
            const toast = btn.closest('.app-toast') || btn.closest('.app-alert');
            if (!toast) return;
            toast.classList.add('toast-hiding');
            setTimeout(() => toast.remove(), 300);
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Auto dismiss alert toast after 5s with pause on hover
            const alerts = document.querySelectorAll('.alerts-container .app-toast, .alerts-container .app-alert');
            alerts.forEach(alert => {
                let timer = setTimeout(() => {
                    alert.classList.add('toast-hiding');
                    setTimeout(() => alert.remove(), 300);
                }, 5000);

                alert.addEventListener('mouseenter', () => clearTimeout(timer));
                alert.addEventListener('mouseleave', () => {
                    timer = setTimeout(() => {
                        alert.classList.add('toast-hiding');
                        setTimeout(() => alert.remove(), 300);
                    }, 3000);
                });
            });

            // Password Toggle
            const toggleButtons = document.querySelectorAll('.toggle-password-btn');
            toggleButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const targetId = this.getAttribute('data-target');
                    const input = document.getElementById(targetId);
                    if (input) {
                        const isPassword = input.getAttribute('type') === 'password';
                        input.setAttribute('type', isPassword ? 'text' : 'password');
                        
                        // Toggle SVG icon
                        const eyeOpen = this.querySelector('.eye-open');
                        const eyeClosed = this.querySelector('.eye-closed');
                        if (eyeOpen && eyeClosed) {
                            if (isPassword) {
                                eyeOpen.style.display = 'none';
                                eyeClosed.style.display = 'block';
                            } else {
                                eyeOpen.style.display = 'block';
                                eyeClosed.style.display = 'none';
                            }
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
