<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Peta Alur Interaktif • Simulator Magang Diskominfo Kabupaten Sleman</title>
    <meta name="description" content="Simulasi diorama panorama alur pendaftaran dan pelaksanaan magang Diskominfo Sleman: lanskap hidup, karakter bergerak di tiap tahap, dan panduan persyaratan resmi.">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo_diskominfo.png') }}">

    <!-- Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/peta-alur-fullscreen.css') }}?v={{ time() }}">

    <!-- Vite Asset Pipeline -->
    @vite(['resources/css/pages/public/peta-alur.css', 'resources/js/pages/public/peta-alur.js'])
    @if(isset($isPreview) && $isPreview)
        @vite(['resources/css/pages/public/landing.css'])
    @endif
</head>
<body>

{{-- Floating Control Bar saat Mode Pratinjau Staging Aktif --}}
@include('public.partials._preview_control_bar')

<div class="peta-alur-fullscreen-app">
    
    <!-- =========================================================================
         1. TOPBAR IMMERSIVE NAVIGATION & SIMULATOR CONTROLS
         ========================================================================= -->
    <header class="peta-alur-topbar">
        <div class="topbar-left">
            <a href="{{ route('landing') }}" class="btn-nav-back" title="Kembali ke Beranda MAGKOM">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                <span>Beranda</span>
            </a>

            <div class="topbar-branding">
                <span class="topbar-badge">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polygon points="12 8 8 12 12 16 12 8"/></svg>
                    Peta Alur Interaktif
                </span>
                <h1 class="topbar-title">Simulator Magang Diskominfo Sleman</h1>
            </div>
        </div>

        <!-- Right Action Controls -->
        <div class="topbar-right">
            <!-- Audio Controller -->
            <button type="button" class="btn-audio-toggle" id="btnModalAudioToggle" onclick="toggleModalAudio()" title="Aktifkan / Nonaktifkan Efek Suara Ramah">
                <svg id="audioIconOn" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="display: none;"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"/></svg>
                <svg id="audioIconOff" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><line x1="23" y1="9" x2="17" y2="15"/><line x1="17" y1="9" x2="23" y2="15"/></svg>
                <span id="audioToggleLabel">Suara: Off</span>
            </button>

            <!-- Start Register Button -->
            <a href="{{ route('landing') }}#kuota" class="btn-topbar-register">
                <span>Daftar Sekarang</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>
    </header>

    <!-- =========================================================================
         2. WORKSPACE 100% FULL ANIMASI KANVAS DENGAN IN-SCENE STAGE HUD CARD
         ========================================================================= -->
    <div class="peta-alur-main-workspace" id="petaAlurWorkspace">
        
        <!-- KANVAS PANORAMA 100% LEGA DENGAN IN-SCENE HOLOGRAPHIC STAGE CARD -->
        <main class="peta-alur-viewport" id="modalRoadCanvasWrap">
            
            <!-- Virtual Analog Joystick HUD (Kontrol Kemudi Analog Game 2D) -->
            <div class="virtual-analog-container" id="gameAnalogContainer" role="region" aria-label="Kontrol Analog Mobil">
                <!-- Tombol Klakson Mobil (Klik Atas / Tombol Klakson) -->
                <div class="analog-top-controls">
                    <button type="button" class="analog-horn-btn" id="btnSteerHorn" onclick="triggerCarHorn()" data-sound-horn="playCarHorn" title="Bunyikan Klakson Mobil (▲ / Panah Atas / W)" aria-label="Bunyikan Klakson Mobil">
                        <span class="horn-icon">&#128227;</span>
                        <span class="horn-label">&#9650; KLAKSON</span>
                    </button>
                </div>

                <div class="analog-base" id="analogBase" role="toolbar" aria-label="Tuas Kemudi Mobil" draggable="false">
                    <button type="button" class="analog-steer-btn btn-steer-left" id="btnSteerLeft" title="Mundur / Pos Sebelumnya (◀)" aria-label="Kemudi Mundur">
                        <span class="steer-arrow-char">&#9664;</span>
                    </button>
                    <div class="analog-axis-guide" pointer-events="none" style="pointer-events: none;">
                        <span class="guide-label" pointer-events="none">KEMUDI</span>
                    </div>
                    <div class="analog-thumb" id="analogThumb" role="slider" aria-label="Tuas Kemudi Mobil (Tarik / Geser Kiri-Kanan)" draggable="false">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" pointer-events="none" style="pointer-events: none; user-select: none;">
                            <polygon points="7 12 12 7 12 17" fill="currentColor"/>
                            <polygon points="17 12 12 7 12 17" fill="currentColor"/>
                        </svg>
                    </div>
                    <button type="button" class="analog-steer-btn btn-steer-right" id="btnSteerRight" title="Maju / Pos Berikutnya (▶)" aria-label="Kemudi Maju">
                        <span class="steer-arrow-char">&#9654;</span>
                    </button>
                </div>

                <div class="analog-meta">
                    <span class="analog-hint-text">🕹️ Klik / Tahan Panah atau Tarik Tuas</span>
                    <span class="analog-kbd-hint">Keyboard: <strong>&larr; / &rarr;</strong> atau <strong>A / D</strong> • Klakson: <strong>&uarr; / W</strong></span>
                </div>
            </div>


            <!-- Living SVG Scene Canvas (2D Side-Scrolling Game Panorama - Lebar Dunia 2400 x 540) -->
            <svg id="modalRoadCanvas" viewBox="0 0 960 540" preserveAspectRatio="xMidYMid slice" class="panoramic-svg-canvas">
                <defs>
                    <!-- Sky Gradient Pagi/Siang Hari Asri Sleman -->
                    <linearGradient id="skyGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" stop-color="#7dd3fc"/>
                        <stop offset="40%" stop-color="#bae6fd"/>
                        <stop offset="75%" stop-color="#e0f2fe"/>
                        <stop offset="100%" stop-color="#fed7aa"/>
                    </linearGradient>

                    <!-- Pegunungan Jauh Merapi Gradient (Biru Keabuan Megah Sesuai Gambar 2) -->
                    <linearGradient id="merapiFarGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" stop-color="#64748b"/>
                        <stop offset="50%" stop-color="#475569"/>
                        <stop offset="100%" stop-color="#334155"/>
                    </linearGradient>

                    <!-- Pegunungan Tengah (Teal Blue Gunung Merapi Sleman) -->
                    <linearGradient id="merapiGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" stop-color="#0284c7"/>
                        <stop offset="45%" stop-color="#0369a1"/>
                        <stop offset="100%" stop-color="#0f766e"/>
                    </linearGradient>

                    <!-- Active Road Trail Gradient -->
                    <linearGradient id="activeTrailGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="0%" stop-color="#0284c7"/>
                        <stop offset="100%" stop-color="#38bdf8"/>
                    </linearGradient>

                    <!-- Sorotan Lampu Depan Mobil Kuning Halus -->
                    <linearGradient id="headlightGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="0%" stop-color="#fde047" stop-opacity="0.85"/>
                        <stop offset="45%" stop-color="#fef08a" stop-opacity="0.35"/>
                        <stop offset="100%" stop-color="#fef08a" stop-opacity="0"/>
                    </linearGradient>

                    <!-- Pin Glow Soft Shadow -->
                    <filter id="pinGlow" x="-50%" y="-50%" width="200%" height="200%">
                        <feDropShadow dx="0" dy="4" stdDeviation="5" flood-color="#0f172a" flood-opacity="0.25"/>
                    </filter>
                </defs>

                <!-- LAYER 1: LANGIT PAGI PANORAMA LEBAR (2400px) -->
                <rect x="-400" y="-100" width="3200" height="640" fill="url(#skyGrad)"/>

                <!-- Matahari Hangat Sleman -->
                <circle cx="1850" cy="75" r="42" fill="#fde047" opacity="0.35"/>
                <circle cx="1850" cy="75" r="26" fill="#fef08a" opacity="0.9"/>

                <!-- Awan Putih Bersih Melayang di Langit Luas -->
                <g class="floating-clouds-group">
                    <path class="floating-cloud cloud-speed-1" d="M 60,65 Q 90,40 130,58 Q 170,38 210,60 Q 240,42 270,68 Q 245,85 200,85 Q 120,85 60,65 Z" fill="#ffffff" opacity="0.9"/>
                    <path class="floating-cloud cloud-speed-2" d="M 650,80 Q 690,52 740,72 Q 780,50 820,74 Q 850,56 880,82 Q 850,100 800,100 Q 720,100 650,80 Z" fill="#ffffff" opacity="0.82"/>
                    <path class="floating-cloud cloud-speed-1" d="M 1320,60 Q 1360,35 1400,55 Q 1440,38 1480,58 Q 1510,42 1530,65 Q 1500,80 1450,80 Q 1380,80 1320,60 Z" fill="#ffffff" opacity="0.88"/>
                    <path class="floating-cloud cloud-speed-2" d="M 1980,85 Q 2020,58 2060,78 Q 2100,56 2140,80 Q 2170,62 2190,88 Q 2160,102 2110,102 Q 2040,102 1980,85 Z" fill="#ffffff" opacity="0.85"/>
                </g>

                <!-- Siluet Burung-Burung Terbang Melintasi Langit Pegunungan -->
                <g class="birds-flock" transform="translate(1050, 80)">
                    <path d="M 0,0 Q 8,-8 16,0 Q 24,-8 32,0" fill="none" stroke="#475569" stroke-width="2" stroke-linecap="round"/>
                    <path d="M 45,-15 Q 52,-22 59,-15 Q 66,-22 73,-15" fill="none" stroke="#475569" stroke-width="1.8" stroke-linecap="round"/>
                    <path d="M 30,18 Q 36,12 42,18 Q 48,12 54,18" fill="none" stroke="#475569" stroke-width="1.6" stroke-linecap="round"/>
                </g>

                <!-- LAYER 2: PEGUNUNGAN MERAPI MEGAH BERLAPIS (SESUAI GAMBAR 2 USER) -->
                <g id="modalMerapiBackdrop" class="merapi-backdrop">
                    <!-- Lapisan Pegunungan Jauh (Puncak Berbatu Biru-Abu) -->
                    <path d="M -100,340 L 80,180 L 220,250 L 380,140 L 520,240 L 720,110 L 900,230 L 1120,85 L 1320,220 L 1520,120 L 1700,240 L 1940,95 L 2140,210 L 2340,130 L 2500,340 Z" fill="url(#merapiFarGrad)"/>
                    <!-- Facet Bayangan & Kilau Puncak Pegunungan Jauh -->
                    <polygon points="380,140 440,210 380,240 320,200" fill="#cbd5e1" opacity="0.35"/>
                    <polygon points="720,110 790,190 730,220 660,180" fill="#cbd5e1" opacity="0.4"/>
                    <polygon points="1120,85 1200,180 1130,220 1050,170" fill="#ffffff" opacity="0.45"/>
                    <polygon points="1940,95 2010,170 1950,210 1880,160" fill="#cbd5e1" opacity="0.35"/>

                    <!-- Lapisan Pegunungan Tengah (Gunung Merapi Sleman yang Ikonik & Megah) -->
                    <path d="M -100,380 L 150,240 L 400,310 L 680,190 L 980,310 L 1260,180 L 1540,300 L 1820,170 L 2100,290 L 2350,210 L 2500,380 Z" fill="url(#merapiGrad)" opacity="0.9"/>

                    <!-- Kawah Merapi Sleman & Kepulan Asap Tipis Melayang -->
                    <g transform="translate(1260, 180)">
                        <path d="M -22,0 Q 0,-18 22,0 Z" fill="#ffffff" opacity="0.85"/>
                        <ellipse cx="0" cy="5" rx="26" ry="6" fill="#e0f2fe" opacity="0.7"/>
                        <ellipse cx="6" cy="-24" rx="14" ry="4.5" fill="#ffffff" opacity="0.6" class="merapi-steam-1"/>
                        <ellipse cx="18" cy="-42" rx="22" ry="7" fill="#ffffff" opacity="0.4" class="merapi-steam-2"/>
                    </g>
                </g>

                <!-- LAYER 3: PERBUKITAN HIJAU ASRI SLEMAN & HUTAN CEMARA/PINUS (GAMBAR 2) -->
                <!-- Bukit Hijau Tua Bertingkat -->
                <path d="M -100,420 Q 300,310 700,350 Q 1100,300 1500,340 Q 1900,290 2300,330 L 2500,420 L 2500,500 L -100,500 Z" fill="#047857" opacity="0.75"/>
                <!-- Bukit Hijau Terang Sleman -->
                <path d="M -100,435 Q 240,345 620,385 Q 1020,340 1420,375 Q 1820,330 2220,365 L 2500,435 L 2500,520 L -100,520 Z" fill="#15803d" opacity="0.9"/>
                <path d="M -100,442 Q 200,380 500,410 Q 800,375 1100,405 Q 1400,370 1700,405 Q 2000,370 2300,405 L 2500,442 L 2500,540 L -100,540 Z" fill="#22c55e" opacity="0.95"/>

                <!-- Klaster Pohon Cemara/Pinus di Kaki Pegunungan Sesuai Gambar 2 -->
                <g class="pine-forest-group">
                    <!-- Kelompok Pinus 1 (Dekat Pos 1) -->
                    <polygon points="120,438 135,390 150,438" fill="#065f46"/>
                    <polygon points="138,438 152,382 166,438" fill="#047857"/>
                    <polygon points="155,438 168,394 181,438" fill="#065f46"/>
                    <!-- Kelompok Pinus 2 (Dekat Pos 2) -->
                    <polygon points="680,438 695,385 710,438" fill="#065f46"/>
                    <polygon points="702,438 718,375 734,438" fill="#047857"/>
                    <polygon points="726,438 740,390 754,438" fill="#065f46"/>
                    <!-- Kelompok Pinus 3 (Dekat Pos 3) -->
                    <polygon points="1280,438 1295,380 1310,438" fill="#065f46"/>
                    <polygon points="1302,438 1318,370 1334,438" fill="#047857"/>
                    <polygon points="1326,438 1340,388 1354,438" fill="#065f46"/>
                    <!-- Kelompok Pinus 4 (Dekat Pos 4) -->
                    <polygon points="1840,438 1855,385 1870,438" fill="#065f46"/>
                    <polygon points="1860,438 1876,375 1892,438" fill="#047857"/>
                </g>

                <!-- Pepohonan Rindang Tropis Sleman yang Bergoyang Sepoi-sepoi -->
                <g transform="translate(420, 395)" class="tree-swaying">
                    <rect x="7" y="16" width="5" height="18" fill="#78350f" rx="1.5"/>
                    <circle cx="9" cy="10" r="17" fill="#15803d"/>
                    <circle cx="14" cy="6" r="13" fill="#22c55e" opacity="0.85"/>
                </g>
                <g transform="translate(1040, 390)" class="tree-swaying">
                    <rect x="7" y="16" width="5" height="18" fill="#78350f" rx="1.5"/>
                    <circle cx="9" cy="10" r="17" fill="#16a34a"/>
                    <circle cx="5" cy="7" r="13" fill="#4ade80" opacity="0.8"/>
                </g>
                <g transform="translate(1680, 395)" class="tree-swaying">
                    <rect x="7" y="16" width="5" height="18" fill="#78350f" rx="1.5"/>
                    <circle cx="9" cy="10" r="18" fill="#15803d"/>
                    <circle cx="15" cy="6" r="14" fill="#22c55e" opacity="0.85"/>
                </g>

                <!-- LAYER 4: GEDUNG PERKANTORAN, KAMPUS, & GAPURA SLEMAN (PADAT & MENYATU DI KEDUA UJUNG) -->
                <!-- A. Gapura Gerbang Selamat Datang Kabupaten Sleman & Kampus Pendaftaran (Ujung Kiri: x = -60 s/d 180) -->
                <g id="westSlemanGateScene" transform="translate(10, 210)">
                    <!-- Siluet Gedung Kampus / Kampus Pendaftaran Sleman Barat -->
                    <rect x="-80" y="50" width="110" height="110" rx="4" fill="#f1f5f9" stroke="#cbd5e1" stroke-width="2"/>
                    <rect x="-86" y="44" width="122" height="9" rx="2" fill="#0284c7"/>
                    <!-- Jendela Kampus -->
                    <rect x="-65" y="62" width="18" height="14" rx="2" fill="#bae6fd" stroke="#0284c7" stroke-width="0.8"/>
                    <rect x="-35" y="62" width="18" height="14" rx="2" fill="#bae6fd" stroke="#0284c7" stroke-width="0.8"/>
                    <rect x="-5" y="62" width="18" height="14" rx="2" fill="#bae6fd" stroke="#0284c7" stroke-width="0.8"/>
                    <rect x="-65" y="86" width="18" height="14" rx="2" fill="#bae6fd" stroke="#0284c7" stroke-width="0.8"/>
                    <rect x="-35" y="86" width="18" height="14" rx="2" fill="#bae6fd" stroke="#0284c7" stroke-width="0.8"/>
                    <rect x="-5" y="86" width="18" height="14" rx="2" fill="#bae6fd" stroke="#0284c7" stroke-width="0.8"/>
                    <!-- Gapura Megah Gerbang Masuk Sleman Barat -->
                    <g transform="translate(50, 0)">
                        <!-- Pilar Kiri & Kanan Candi Sleman -->
                        <rect x="0" y="55" width="18" height="105" rx="3" fill="#334155" stroke="#475569" stroke-width="1.5"/>
                        <rect x="110" y="55" width="18" height="105" rx="3" fill="#334155" stroke="#475569" stroke-width="1.5"/>
                        <rect x="-4" y="152" width="26" height="8" rx="2" fill="#1e293b"/>
                        <rect x="106" y="152" width="26" height="8" rx="2" fill="#1e293b"/>
                        <!-- Lengkungan Busur Atas Gapura -->
                        <path d="M 9,55 Q 64,15 119,55 L 128,55 Q 64,2 0,55 Z" fill="#1b3860" stroke="#f59e0b" stroke-width="2"/>
                        <!-- Plang Selamat Datang -->
                        <rect x="22" y="38" width="84" height="18" rx="4" fill="#0f2444" stroke="#38bdf8" stroke-width="1.2"/>
                        <text x="64" y="50" text-anchor="middle" font-size="6.8" font-weight="900" fill="#ffffff" letter-spacing="0.5" font-family="'Lexend', sans-serif">KABUPATEN SLEMAN</text>
                    </g>
                </g>

                <!-- B. Gedung Tematik Pos 1: Pusat Pendaftaran Mahasiswa Magang (Registration Center Sleman) -->
                <g id="gedungPos1Scene" class="checkpoint-building" transform="translate(340, 200)">
                    <ellipse cx="65" cy="115" rx="72" ry="10" fill="#0f172a" opacity="0.14"/>
                    <!-- Badan Gedung 2 Lantai Modern -->
                    <rect x="0" y="20" width="130" height="95" rx="6" fill="#ffffff" stroke="#cbd5e1" stroke-width="2"/>
                    <!-- Kanopi & Lis Atap Merah Sleman & Emas -->
                    <rect x="-4" y="12" width="138" height="10" rx="3" fill="#e11d48"/>
                    <rect x="10" y="8" width="110" height="4" rx="1" fill="#f59e0b"/>
                    <!-- Baris Jendela Lantai 2 (Kaca Biru Siang) -->
                    <rect x="12" y="38" width="22" height="18" rx="2" fill="#bae6fd" stroke="#0284c7" stroke-width="0.8"/>
                    <rect x="40" y="38" width="22" height="18" rx="2" fill="#bae6fd" stroke="#0284c7" stroke-width="0.8"/>
                    <rect x="68" y="38" width="22" height="18" rx="2" fill="#bae6fd" stroke="#0284c7" stroke-width="0.8"/>
                    <rect x="96" y="38" width="22" height="18" rx="2" fill="#bae6fd" stroke="#0284c7" stroke-width="0.8"/>
                    <!-- Baris Lantai 1 & Pintu Kaca -->
                    <rect x="12" y="68" width="22" height="20" rx="2" fill="#bae6fd" stroke="#0284c7" stroke-width="0.8"/>
                    <rect x="96" y="68" width="22" height="20" rx="2" fill="#bae6fd" stroke="#0284c7" stroke-width="0.8"/>
                    <!-- Pintu Masuk Kaca & Kanopi Merah-Putih -->
                    <rect x="44" y="66" width="42" height="49" rx="3" fill="#1e293b"/>
                    <rect x="48" y="72" width="15" height="43" fill="#e0f2fe"/>
                    <rect x="67" y="72" width="15" height="43" fill="#e0f2fe"/>
                    <path d="M 40,66 L 90,66 L 86,61 L 44,61 Z" fill="#e11d48"/>
                    <!-- Pohon Taman Samping Gedung -->
                    <circle cx="138" cy="100" r="13" fill="#15803d"/>
                    <circle cx="140" cy="95" r="9" fill="#22c55e" opacity="0.85"/>
                </g>

                <!-- C. Gedung Tematik Pos 2: Layanan Seleksi Berkas & PTSP Diskominfo Sleman -->
                <g id="gedungPos2Scene" class="checkpoint-building" transform="translate(940, 200)">
                    <ellipse cx="65" cy="115" rx="72" ry="10" fill="#0f172a" opacity="0.14"/>
                    <!-- Badan Gedung Fasad Modern Biru Sleman -->
                    <rect x="0" y="20" width="132" height="95" rx="6" fill="#f8fafc" stroke="#cbd5e1" stroke-width="2"/>
                    <!-- Lis Atap Biru Diskominfo -->
                    <rect x="-4" y="12" width="140" height="10" rx="3" fill="#0284c7"/>
                    <rect x="12" y="8" width="108" height="4" rx="1" fill="#38bdf8"/>
                    <!-- Jendela Kaca Horizontal Strip Modern -->
                    <rect x="12" y="38" width="30" height="18" rx="2" fill="#bae6fd" stroke="#0284c7" stroke-width="0.8"/>
                    <rect x="50" y="38" width="32" height="18" rx="2" fill="#bae6fd" stroke="#0284c7" stroke-width="0.8"/>
                    <rect x="90" y="38" width="30" height="18" rx="2" fill="#bae6fd" stroke="#0284c7" stroke-width="0.8"/>
                    <rect x="12" y="68" width="30" height="18" rx="2" fill="#bae6fd" stroke="#0284c7" stroke-width="0.8"/>
                    <rect x="90" y="68" width="30" height="18" rx="2" fill="#bae6fd" stroke="#0284c7" stroke-width="0.8"/>
                    <!-- Pintu Utama Lobi Seleksi -->
                    <rect x="50" y="68" width="32" height="47" rx="3" fill="#1b3860"/>
                    <rect x="53" y="73" width="11" height="42" fill="#e0f2fe"/>
                    <rect x="68" y="73" width="11" height="42" fill="#e0f2fe"/>
                    <!-- Tanaman Hias Taman Seleksi -->
                    <circle cx="-10" cy="100" r="12" fill="#16a34a"/>
                    <circle cx="-8" cy="95" r="8" fill="#4ade80" opacity="0.85"/>
                </g>

                <!-- D. Gedung Tematik Pos 3: Sleman Cyber Smart Hub & Innovation Lab -->
                <g id="gedungPos3Scene" class="checkpoint-building" transform="translate(1540, 195)">
                    <ellipse cx="68" cy="120" rx="76" ry="11" fill="#0f172a" opacity="0.14"/>
                    <!-- Menara Kaca Cyber Lab Modern -->
                    <rect x="0" y="15" width="136" height="105" rx="6" fill="#ffffff" stroke="#0f766e" stroke-width="2"/>
                    <!-- Lis Atap Toska Sleman & Antena Sensor IoT -->
                    <rect x="-4" y="8" width="144" height="9" rx="2.5" fill="#0f766e"/>
                    <line x1="68" y1="8" x2="68" y2="-22" stroke="#64748b" stroke-width="2.5"/>
                    <circle cx="68" cy="-24" r="3.5" fill="#38bdf8" class="tower-beacon-pulse"/>
                    <!-- Curtain Wall Panel Kaca Biru Toska -->
                    <rect x="12" y="32" width="112" height="50" rx="3" fill="#ccfbf1" stroke="#0f766e" stroke-width="0.8"/>
                    <line x1="40" y1="32" x2="40" y2="82" stroke="#0f766e" stroke-width="0.8"/>
                    <line x1="68" y1="32" x2="68" y2="82" stroke="#0f766e" stroke-width="0.8"/>
                    <line x1="96" y1="32" x2="96" y2="82" stroke="#0f766e" stroke-width="0.8"/>
                    <line x1="12" y1="57" x2="124" y2="57" stroke="#0f766e" stroke-width="0.8"/>
                    <!-- Pintu Otomatis Smart Lab -->
                    <rect x="48" y="88" width="40" height="32" rx="2" fill="#1e293b"/>
                    <rect x="52" y="91" width="14" height="29" fill="#99f6e4"/>
                    <rect x="70" y="91" width="14" height="29" fill="#99f6e4"/>
                    <!-- Pepohonan Taman Inovasi -->
                    <circle cx="144" cy="105" r="14" fill="#047857"/>
                    <circle cx="146" cy="100" r="10" fill="#34d399" opacity="0.85"/>
                </g>

                <!-- E. Gedung Kantor Resmi Diskominfo Kabupaten Sleman & Auditorium Kelulusan (Pos 4) -->
                <g id="diskominfoHubScene" transform="translate(1960, 230)">
                    <ellipse cx="70" cy="165" rx="80" ry="14" fill="#0f172a" opacity="0.15"/>
                    <!-- Badan Gedung Megah Putih Modern -->
                    <rect x="0" y="30" width="140" height="130" rx="8" fill="#ffffff" stroke="#cbd5e1" stroke-width="2.5"/>
                    <!-- Lis Atap Biru Navy & Emas Diskominfo Sleman -->
                    <rect x="-6" y="20" width="152" height="13" rx="3.5" fill="#1b3860"/>
                    <rect x="8" y="16" width="124" height="4.5" rx="1" fill="#f59e0b"/>
                    <!-- Jendela Kaca Biru Siang Sleman -->
                    <rect x="15" y="44" width="22" height="18" rx="2" fill="#bae6fd" stroke="#0284c7" stroke-width="1"/>
                    <rect x="45" y="44" width="22" height="18" rx="2" fill="#bae6fd" stroke="#0284c7" stroke-width="1"/>
                    <rect x="75" y="44" width="22" height="18" rx="2" fill="#bae6fd" stroke="#0284c7" stroke-width="1"/>
                    <rect x="105" y="44" width="22" height="18" rx="2" fill="#bae6fd" stroke="#0284c7" stroke-width="1"/>
                    <rect x="15" y="74" width="22" height="18" rx="2" fill="#bae6fd" stroke="#0284c7" stroke-width="1"/>
                    <rect x="45" y="74" width="22" height="18" rx="2" fill="#bae6fd" stroke="#0284c7" stroke-width="1"/>
                    <rect x="75" y="74" width="22" height="18" rx="2" fill="#bae6fd" stroke="#0284c7" stroke-width="1"/>
                    <rect x="105" y="74" width="22" height="18" rx="2" fill="#bae6fd" stroke="#0284c7" stroke-width="1"/>
                    <!-- Pintu Utama Kaca & Kanopi Depan -->
                    <rect x="48" y="112" width="44" height="48" rx="3" fill="#1b3860"/>
                    <rect x="53" y="118" width="16" height="42" fill="#e0f2fe"/>
                    <rect x="71" y="118" width="16" height="42" fill="#e0f2fe"/>
                    <rect x="42" y="108" width="56" height="5" rx="1.5" fill="#f59e0b"/>
                    <!-- Tiang Pemancar Komunikasi Sleman Smart City -->
                    <line x1="70" y1="20" x2="70" y2="-44" stroke="#94a3b8" stroke-width="3"/>
                    <line x1="56" y1="0" x2="84" y2="0" stroke="#0284c7" stroke-width="2"/>
                    <line x1="60" y1="-20" x2="80" y2="-20" stroke="#0284c7" stroke-width="2"/>
                    <circle cx="70" cy="-46" r="5" fill="#16a34a" class="tower-beacon-pulse"/>
                    <!-- Bendera Merah Putih Berkibar -->
                    <g class="flag-waving">
                        <rect x="72" y="-40" width="18" height="6" fill="#ef4444"/>
                        <rect x="72" y="-34" width="18" height="6" fill="#ffffff" stroke="#e2e8f0" stroke-width="0.5"/>
                    </g>
                </g>

                <!-- C. Gedung Pemerintahan & Perkantoran Kompleks Pemkab Sleman (Ujung Kanan: x = 2140 s/d 2480) -->
                <g id="eastCivicPlazaScene" transform="translate(2150, 225)">
                    <!-- Gedung Sayap Kanan 3 Lantai -->
                    <rect x="0" y="40" width="130" height="120" rx="6" fill="#f8fafc" stroke="#cbd5e1" stroke-width="2"/>
                    <rect x="-4" y="32" width="138" height="10" rx="2" fill="#0f2444"/>
                    <rect x="12" y="28" width="106" height="4" rx="1" fill="#f59e0b"/>
                    <!-- Baris Jendela Gedung Pemkab -->
                    <rect x="14" y="52" width="22" height="16" rx="2" fill="#bae6fd" stroke="#0284c7" stroke-width="0.8"/>
                    <rect x="44" y="52" width="22" height="16" rx="2" fill="#bae6fd" stroke="#0284c7" stroke-width="0.8"/>
                    <rect x="74" y="52" width="22" height="16" rx="2" fill="#bae6fd" stroke="#0284c7" stroke-width="0.8"/>
                    <rect x="14" y="80" width="22" height="16" rx="2" fill="#bae6fd" stroke="#0284c7" stroke-width="0.8"/>
                    <rect x="44" y="80" width="22" height="16" rx="2" fill="#bae6fd" stroke="#0284c7" stroke-width="0.8"/>
                    <rect x="74" y="80" width="22" height="16" rx="2" fill="#bae6fd" stroke="#0284c7" stroke-width="0.8"/>
                    <rect x="14" y="108" width="22" height="16" rx="2" fill="#bae6fd" stroke="#0284c7" stroke-width="0.8"/>
                    <rect x="44" y="108" width="22" height="16" rx="2" fill="#bae6fd" stroke="#0284c7" stroke-width="0.8"/>
                    <rect x="74" y="108" width="22" height="16" rx="2" fill="#bae6fd" stroke="#0284c7" stroke-width="0.8"/>
                    <!-- Gedung Menara Kaca Timur -->
                    <rect x="140" y="20" width="120" height="140" rx="6" fill="#ffffff" stroke="#94a3b8" stroke-width="2"/>
                    <rect x="136" y="12" width="128" height="10" rx="2" fill="#0284c7"/>
                    <line x1="140" y1="60" x2="260" y2="60" stroke="#bae6fd" stroke-width="1.5"/>
                    <line x1="140" y1="100" x2="260" y2="100" stroke="#bae6fd" stroke-width="1.5"/>
                    <rect x="155" y="30" width="90" height="22" rx="2" fill="#e0f2fe" stroke="#0284c7" stroke-width="0.8"/>
                    <rect x="155" y="70" width="90" height="22" rx="2" fill="#e0f2fe" stroke="#0284c7" stroke-width="0.8"/>
                    <rect x="155" y="110" width="90" height="22" rx="2" fill="#e0f2fe" stroke="#0284c7" stroke-width="0.8"/>
                    <!-- Pepohonan Taman Kantor -->
                    <circle cx="134" cy="150" r="14" fill="#15803d"/>
                    <circle cx="138" cy="144" r="10" fill="#22c55e" opacity="0.85"/>
                    <circle cx="268" cy="150" r="16" fill="#16a34a"/>
                </g>

                <!-- LAYER 5: JALUR JALAN ASPAL LURUS MENDATAR (FULL BLEED PANJANG x = -100 s/d 2650) -->
                <!-- Trotoar Pejalan Kaki (Sidewalk Lebar di Pinggir Jalan: y = 295 s/d 355) -->
                <rect x="-100" y="295" width="2750" height="60" fill="#e2e8f0"/>
                <!-- Kerb / Batu Tepi Trotoar -->
                <rect x="-100" y="347" width="2750" height="8" fill="#cbd5e1" stroke="#94a3b8" stroke-width="0.8"/>
                <line x1="-100" y1="355" x2="2650" y2="355" stroke="#64748b" stroke-width="2"/>

                <!-- Jalan Aspal Utama Mendatar Lurus (Klik di mana saja untuk melaju ke sana: y = 355 s/d 440) -->
                <rect id="svgRoadBase" x="-100" y="355" width="2750" height="85" fill="#334155" style="cursor: pointer;"/>
                <line id="svgRoadBorder" x1="-100" y1="355" x2="2650" y2="355" stroke="#94a3b8" stroke-width="4"/>

                <!-- Jejak Progres Jalan Aktif (Biru Sleman Segar Mendatar) -->
                <line id="svgRoadProgress" x1="-100" y1="398" x2="2650" y2="398" stroke="url(#activeTrailGrad)" stroke-width="85" opacity="0.18"/>

                <!-- Marka Putih Putus-Putus Jalan Raya Bergerak Halus -->
                <line id="svgRoadDashed" x1="-100" y1="398" x2="2650" y2="398" stroke="#ffffff" stroke-width="3.5" stroke-dasharray="24 16" class="road-marking-animated" pointer-events="none"/>

                <!-- Tepian Rumput Depan & Bunga Liar Segar 2-Tone (Vibrant Meadow Setinggi 100px: y = 440 s/d 540) -->
                <!-- Lapisan Rumput Belakang (Hijau Daun Alami) -->
                <path d="M -100,440 Q 120,436 340,442 T 780,436 T 1220,443 T 1660,436 T 2100,442 T 2540,437 T 2650,440 L 2650,540 L -100,540 Z" fill="#15803d"/>
                <!-- Lapisan Rumput Depan (Hijau Cerah Segar) -->
                <path d="M -100,448 Q 160,442 420,450 T 940,443 T 1460,450 T 1980,443 T 2500,449 T 2650,446 L 2650,540 L -100,540 Z" fill="#22c55e"/>

                <!-- Gerigi Rumpun Daun Rumput Alami Sepanjang Bibir Aspal -->
                <g class="grass-tufts-group" fill="#22c55e" stroke="#15803d" stroke-width="0.6">
                    <path d="M -20,442 L -24,435 L -20,438 L -16,433 L -17,442 Z"/>
                    <path d="M 120,443 L 116,436 L 120,439 L 124,434 L 123,443 Z"/>
                    <path d="M 280,442 L 276,435 L 280,438 L 284,433 L 283,442 Z"/>
                    <path d="M 440,444 L 436,437 L 440,440 L 444,435 L 443,444 Z"/>
                    <path d="M 620,441 L 616,434 L 620,437 L 624,432 L 623,441 Z"/>
                    <path d="M 800,443 L 796,436 L 800,439 L 804,434 L 803,443 Z"/>
                    <path d="M 980,444 L 976,437 L 980,440 L 984,435 L 983,444 Z"/>
                    <path d="M 1160,442 L 1156,435 L 1160,438 L 1164,433 L 1163,442 Z"/>
                    <path d="M 1340,443 L 1336,436 L 1340,439 L 1344,434 L 1343,443 Z"/>
                    <path d="M 1520,442 L 1516,435 L 1520,438 L 1524,433 L 1523,442 Z"/>
                    <path d="M 1700,444 L 1696,437 L 1700,440 L 1704,435 L 1703,444 Z"/>
                    <path d="M 1880,442 L 1876,435 L 1880,438 L 1884,433 L 1883,442 Z"/>
                    <path d="M 2060,443 L 2056,436 L 2060,439 L 2064,434 L 2063,443 Z"/>
                    <path d="M 2240,442 L 2236,435 L 2240,438 L 2244,433 L 2243,442 Z"/>
                    <path d="M 2420,443 L 2416,436 L 2420,439 L 2424,434 L 2423,443 Z"/>
                </g>

                <!-- Bunga-bunga Liar Segar Aneka Warna (Kuning, Putih, Merah Muda, Oranye) -->
                <g class="wildflowers-group">
                    <!-- Bunga Kuning Cerah -->
                    <circle cx="-50" cy="468" r="3.5" fill="#facc15"/><circle cx="-50" cy="468" r="1.5" fill="#ca8a04"/>
                    <circle cx="60" cy="470" r="3.8" fill="#facc15"/><circle cx="60" cy="470" r="1.5" fill="#ca8a04"/>
                    <circle cx="210" cy="466" r="3.5" fill="#facc15"/><circle cx="210" cy="466" r="1.5" fill="#ca8a04"/>
                    <circle cx="360" cy="472" r="3.8" fill="#facc15"/><circle cx="360" cy="472" r="1.5" fill="#ca8a04"/>
                    <circle cx="510" cy="468" r="3.5" fill="#facc15"/><circle cx="510" cy="468" r="1.5" fill="#ca8a04"/>
                    <circle cx="660" cy="471" r="3.8" fill="#facc15"/><circle cx="660" cy="471" r="1.5" fill="#ca8a04"/>
                    <circle cx="810" cy="467" r="3.5" fill="#facc15"/><circle cx="810" cy="467" r="1.5" fill="#ca8a04"/>
                    <circle cx="960" cy="472" r="3.8" fill="#facc15"/><circle cx="960" cy="472" r="1.5" fill="#ca8a04"/>
                    <circle cx="1110" cy="468" r="3.5" fill="#facc15"/><circle cx="1110" cy="468" r="1.5" fill="#ca8a04"/>
                    <circle cx="1260" cy="471" r="3.8" fill="#facc15"/><circle cx="1260" cy="471" r="1.5" fill="#ca8a04"/>
                    <circle cx="1410" cy="466" r="3.5" fill="#facc15"/><circle cx="1410" cy="466" r="1.5" fill="#ca8a04"/>
                    <circle cx="1560" cy="472" r="3.8" fill="#facc15"/><circle cx="1560" cy="472" r="1.5" fill="#ca8a04"/>
                    <circle cx="1710" cy="468" r="3.5" fill="#facc15"/><circle cx="1710" cy="468" r="1.5" fill="#ca8a04"/>
                    <circle cx="1860" cy="471" r="3.8" fill="#facc15"/><circle cx="1860" cy="471" r="1.5" fill="#ca8a04"/>
                    <circle cx="2010" cy="467" r="3.5" fill="#facc15"/><circle cx="2010" cy="467" r="1.5" fill="#ca8a04"/>
                    <circle cx="2160" cy="472" r="3.8" fill="#facc15"/><circle cx="2160" cy="472" r="1.5" fill="#ca8a04"/>
                    <circle cx="2310" cy="468" r="3.5" fill="#facc15"/><circle cx="2310" cy="468" r="1.5" fill="#ca8a04"/>
                    <circle cx="2460" cy="470" r="3.8" fill="#facc15"/><circle cx="2460" cy="470" r="1.5" fill="#ca8a04"/>

                    <!-- Bunga Putih Melati & Merah Muda Segar -->
                    <circle cx="-10" cy="474" r="3" fill="#ffffff"/><circle cx="-10" cy="474" r="1.2" fill="#fbbf24"/>
                    <circle cx="130" cy="476" r="3.2" fill="#f43f5e"/><circle cx="130" cy="476" r="1.2" fill="#ffe4e6"/>
                    <circle cx="280" cy="475" r="3" fill="#ffffff"/><circle cx="280" cy="475" r="1.2" fill="#fbbf24"/>
                    <circle cx="430" cy="477" r="3.2" fill="#f43f5e"/><circle cx="430" cy="477" r="1.2" fill="#ffe4e6"/>
                    <circle cx="580" cy="475" r="3" fill="#ffffff"/><circle cx="580" cy="475" r="1.2" fill="#fbbf24"/>
                    <circle cx="730" cy="478" r="3.2" fill="#f43f5e"/><circle cx="730" cy="478" r="1.2" fill="#ffe4e6"/>
                    <circle cx="880" cy="474" r="3" fill="#ffffff"/><circle cx="880" cy="474" r="1.2" fill="#fbbf24"/>
                    <circle cx="1030" cy="477" r="3.2" fill="#f43f5e"/><circle cx="1030" cy="477" r="1.2" fill="#ffe4e6"/>
                    <circle cx="1180" cy="475" r="3" fill="#ffffff"/><circle cx="1180" cy="475" r="1.2" fill="#fbbf24"/>
                    <circle cx="1330" cy="478" r="3.2" fill="#f43f5e"/><circle cx="1330" cy="478" r="1.2" fill="#ffe4e6"/>
                    <circle cx="1480" cy="474" r="3" fill="#ffffff"/><circle cx="1480" cy="474" r="1.2" fill="#fbbf24"/>
                    <circle cx="1630" cy="477" r="3.2" fill="#f43f5e"/><circle cx="1630" cy="477" r="1.2" fill="#ffe4e6"/>
                    <circle cx="1780" cy="475" r="3" fill="#ffffff"/><circle cx="1780" cy="475" r="1.2" fill="#fbbf24"/>
                    <circle cx="1930" cy="478" r="3.2" fill="#f43f5e"/><circle cx="1930" cy="478" r="1.2" fill="#ffe4e6"/>
                    <circle cx="2080" cy="474" r="3" fill="#ffffff"/><circle cx="2080" cy="474" r="1.2" fill="#fbbf24"/>
                    <circle cx="2230" cy="477" r="3.2" fill="#f43f5e"/><circle cx="2230" cy="477" r="1.2" fill="#ffe4e6"/>
                    <circle cx="2380" cy="475" r="3" fill="#ffffff"/><circle cx="2380" cy="475" r="1.2" fill="#fbbf24"/>
                    <circle cx="2530" cy="477" r="3.2" fill="#f43f5e"/><circle cx="2530" cy="477" r="1.2" fill="#ffe4e6"/>
                </g>

                <!-- LAYER 5.5: JAJARAN TIANG LAMPU JALAN PERKOTAAN SLEMAN (BERDIRI DI ATAS TROTOAR, BUKAN DI JALAN) -->
                <g id="streetLampsTrack" class="street-lamp-group">
                    <!-- Lampu 1: Dekat Gerbang Barat Sleman (x: 80, y: 275 menapak trotoar) -->
                    <g class="street-lamp-item" transform="translate(80, 275)">
                        <rect x="-2" y="10" width="4" height="60" fill="#334155" rx="1.5"/>
                        <rect x="-5" y="66" width="10" height="6" fill="#1e293b" rx="2"/>
                        <path d="M 0,10 Q 0,-6 14,-6 L 16,-4" fill="none" stroke="#334155" stroke-width="3" stroke-linecap="round"/>
                        <path d="M 10,-4 L 22,-4 L 20,4 L 12,4 Z" fill="#1e293b"/>
                        <circle cx="16" cy="4" r="3.5" fill="#facc15" class="street-lamp-glow"/>
                        <polygon points="16,6 2,68 30,68" fill="#fef08a" opacity="0.18" pointer-events="none"/>
                    </g>

                    <!-- Lampu 2: Antara Pos 1 dan Pos 2 (x: 580) -->
                    <g class="street-lamp-item" transform="translate(580, 275)">
                        <rect x="-2" y="10" width="4" height="60" fill="#334155" rx="1.5"/>
                        <rect x="-5" y="66" width="10" height="6" fill="#1e293b" rx="2"/>
                        <path d="M 0,10 Q 0,-6 14,-6 L 16,-4" fill="none" stroke="#334155" stroke-width="3" stroke-linecap="round"/>
                        <path d="M 10,-4 L 22,-4 L 20,4 L 12,4 Z" fill="#1e293b"/>
                        <circle cx="16" cy="4" r="3.5" fill="#facc15" class="street-lamp-glow"/>
                        <polygon points="16,6 2,68 30,68" fill="#fef08a" opacity="0.18" pointer-events="none"/>
                    </g>

                    <!-- Lampu 3: Antara Pos 2 dan Pos 3 (x: 1180) -->
                    <g class="street-lamp-item" transform="translate(1180, 275)">
                        <rect x="-2" y="10" width="4" height="60" fill="#334155" rx="1.5"/>
                        <rect x="-5" y="66" width="10" height="6" fill="#1e293b" rx="2"/>
                        <path d="M 0,10 Q 0,-6 14,-6 L 16,-4" fill="none" stroke="#334155" stroke-width="3" stroke-linecap="round"/>
                        <path d="M 10,-4 L 22,-4 L 20,4 L 12,4 Z" fill="#1e293b"/>
                        <circle cx="16" cy="4" r="3.5" fill="#facc15" class="street-lamp-glow"/>
                        <polygon points="16,6 2,68 30,68" fill="#fef08a" opacity="0.18" pointer-events="none"/>
                    </g>

                    <!-- Lampu 4: Antara Pos 3 dan Pos 4 (x: 1780) -->
                    <g class="street-lamp-item" transform="translate(1780, 275)">
                        <rect x="-2" y="10" width="4" height="60" fill="#334155" rx="1.5"/>
                        <rect x="-5" y="66" width="10" height="6" fill="#1e293b" rx="2"/>
                        <path d="M 0,10 Q 0,-6 14,-6 L 16,-4" fill="none" stroke="#334155" stroke-width="3" stroke-linecap="round"/>
                        <path d="M 10,-4 L 22,-4 L 20,4 L 12,4 Z" fill="#1e293b"/>
                        <circle cx="16" cy="4" r="3.5" fill="#facc15" class="street-lamp-glow"/>
                        <polygon points="16,6 2,68 30,68" fill="#fef08a" opacity="0.18" pointer-events="none"/>
                    </g>

                    <!-- Lampu 5: Depan Gerbang Kompleks Diskominfo (x: 2140) -->
                    <g class="street-lamp-item" transform="translate(2140, 275)">
                        <rect x="-2" y="10" width="4" height="60" fill="#334155" rx="1.5"/>
                        <rect x="-5" y="66" width="10" height="6" fill="#1e293b" rx="2"/>
                        <path d="M 0,10 Q 0,-6 14,-6 L 16,-4" fill="none" stroke="#334155" stroke-width="3" stroke-linecap="round"/>
                        <path d="M 10,-4 L 22,-4 L 20,4 L 12,4 Z" fill="#1e293b"/>
                        <circle cx="16" cy="4" r="3.5" fill="#facc15" class="street-lamp-glow"/>
                        <polygon points="16,6 2,68 30,68" fill="#fef08a" opacity="0.18" pointer-events="none"/>
                    </g>

                    <!-- Lampu 6: Ujung Timur Boulevard Perkantoran (x: 2360) -->
                    <g class="street-lamp-item" transform="translate(2360, 275)">
                        <rect x="-2" y="10" width="4" height="60" fill="#334155" rx="1.5"/>
                        <rect x="-5" y="66" width="10" height="6" fill="#1e293b" rx="2"/>
                        <path d="M 0,10 Q 0,-6 14,-6 L 16,-4" fill="none" stroke="#334155" stroke-width="3" stroke-linecap="round"/>
                        <path d="M 10,-4 L 22,-4 L 20,4 L 12,4 Z" fill="#1e293b"/>
                        <circle cx="16" cy="4" r="3.5" fill="#facc15" class="street-lamp-glow"/>
                        <polygon points="16,6 2,68 30,68" fill="#fef08a" opacity="0.18" pointer-events="none"/>
                    </g>
                </g>

                <!-- LAYER 6: 4 HALTE CHECKPOINT BERDIRI DI PINGGIR JALAN (TROTOAR: y = 310) DENGAN BALON KOMIK WEBTOON MELAYANG BERSIH -->
                @php
                    $defaultTahapanSteps = [
                        ['step' => '01', 'title' => 'Registrasi', 'badge' => 'REGISTRASI', 'desc' => 'Calon peserta membuat akun resmi, mengisi data pribadi lengkap, dan menunggu verifikasi admin.'],
                        ['step' => '02', 'title' => 'Administrasi & Seleksi', 'badge' => 'ADMINISTRASI & SELEKSI', 'desc' => 'Pilih bidang, unggah berkas, lalu admin menyeleksi dan memverifikasi dalam rentang 1–3 hari kerja.'],
                        ['step' => '03', 'title' => 'Pelaksanaan Magang', 'badge' => 'PELAKSANAAN MAGANG', 'desc' => 'Kerjakan proyek riil, isi logbook harian, serta dapatkan bimbingan langsung dari mentor ASN Sleman.'],
                        ['step' => '04', 'title' => 'Kelulusan', 'badge' => 'KELULUSAN & SERTIFIKAT', 'desc' => 'Unduh lembar nilai evaluasi dan E-Sertifikat resmi bertanda tangan elektronik Diskominfo Sleman.'],
                    ];
                    $stepsData = \App\Support\Pengaturan::json('landing_tahapan_steps', $defaultTahapanSteps);
                    if (empty($stepsData) || count($stepsData) < 4) {
                        $stepsData = $defaultTahapanSteps;
                    }

                    $formatLines = function(string $text) {
                        $lines = explode("\n", wordwrap(trim($text), 48, "\n"));
                        return [
                            $lines[0] ?? '',
                            isset($lines[1]) ? ($lines[1] . (isset($lines[2]) ? '...' : '')) : ''
                        ];
                    };
                @endphp

                <!-- Pos 1: Halte Registrasi (x: 280, y: 310 di pinggir jalan) -->
                @php [$p1Line1, $p1Line2] = $formatLines($stepsData[0]['desc'] ?? ''); @endphp
                <g class="road-checkpoint-group cursor-pointer" id="roadCp1" transform="translate(280, 310)" onclick="selectModalStep(1)" role="button" aria-label="Pilih Pos 1 {{ $stepsData[0]['title'] ?? 'Registrasi' }}" style="cursor: pointer;">
                    <!-- Tiang Halte Pinggir Jalan yang Rapi & Kokoh Berdiri di Trotoar -->
                    <line x1="0" y1="8" x2="0" y2="36" stroke="#475569" stroke-width="4.5" stroke-linecap="round"/>
                    <ellipse cx="0" cy="36" rx="9" ry="3" fill="#64748b"/>
                    <!-- Lingkaran Plang Halte Pos 1 -->
                    <circle cx="0" cy="-12" r="20" class="cp-circle-bg" fill="#e11d48" stroke="#ffffff" stroke-width="3" style="filter: drop-shadow(0 4px 8px rgba(225,29,72,0.4));"/>
                    <!-- Ikon Dokumen Formulir Akun -->
                    <rect x="-6" y="-19" width="12" height="14" rx="1.5" fill="#ffffff"/>
                    <line x1="-3" y1="-15" x2="3" y2="-15" stroke="#e11d48" stroke-width="1.2"/>
                    <line x1="-3" y1="-12" x2="3" y2="-12" stroke="#e11d48" stroke-width="1.2"/>
                    <line x1="-3" y1="-9" x2="1" y2="-9" stroke="#e11d48" stroke-width="1.2"/>
                    <!-- Papan Teks Halte Pinggir Jalan -->
                    <rect x="-44" y="10" width="88" height="15" rx="4" fill="#1b3860" stroke="#ffffff" stroke-width="1.2" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));"/>
                    <text x="0" y="21" text-anchor="middle" font-size="7.5" font-weight="900" fill="#ffffff" font-family="'Lexend', sans-serif">01 {{ \Illuminate\Support\Str::limit(strtoupper($stepsData[0]['title'] ?? 'REGISTRASI'), 12, '') }}</text>

                    <!-- BALON KOMIK / WEBTOON SPEECH BUBBLE POS 1 (Ekor Segitiga Pendek & Rapi, Tidak Menutupi Plang) -->
                    <g class="webtoon-speech-bubble is-active" id="webtoonBubble1">
                        <path d="M -5,-140 L 275,-140 Q 290,-140 290,-125 L 290,-30 Q 290,-15 275,-15 L 35,-15 L 20,0 L 5,-15 L -5,-15 Q -20,-15 -20,-30 L -20,-125 Q -20,-140 -5,-140 Z" fill="#ffffff" stroke="#e11d48" stroke-width="2.5" style="filter: drop-shadow(0 10px 24px rgba(15,23,42,0.22));"/>
                        <rect x="-3" y="-132" width="56" height="18" rx="9" fill="#e11d48"/>
                        <text x="25" y="-120" text-anchor="middle" font-size="8.5" font-weight="900" fill="#ffffff" font-family="'Lexend', sans-serif">POS 01</text>
                        <text x="65" y="-119" text-anchor="start" font-size="11" font-weight="900" fill="#0f172a" font-family="'Lexend', sans-serif">{!! !str_contains(strtoupper($stepsData[0]['title'] ?? ''), 'AKUN') && strtoupper($stepsData[0]['title'] ?? '') === 'REGISTRASI' ? 'REGISTRASI AKUN' : strtoupper($stepsData[0]['title'] ?? 'REGISTRASI AKUN') !!}</text>
                        <line x1="-3" y1="-106" x2="273" y2="-106" stroke="#f1f5f9" stroke-width="1.5"/>
                        <text x="-3" y="-88" font-size="9.2" font-weight="600" fill="#334155" font-family="'Plus Jakarta Sans', sans-serif">{{ $p1Line1 }}</text>
                        <text x="-3" y="-72" font-size="9.2" font-weight="600" fill="#334155" font-family="'Plus Jakarta Sans', sans-serif">{{ $p1Line2 }}</text>
                        <g class="cursor-pointer btn-webtoon-action" onclick="event.stopPropagation(); selectModalStep(2);" role="button" aria-label="Lanjut ke Pos 2">
                            <rect x="150" y="-52" width="122" height="26" rx="13" fill="#e11d48" style="filter: drop-shadow(0 2px 6px rgba(225,29,72,0.3));"/>
                            <text x="211" y="-36" text-anchor="middle" font-size="9" font-weight="800" fill="#ffffff" font-family="'Lexend', sans-serif">Lanjut Pos 2 ➔</text>
                        </g>
                    </g>
                </g>

                <!-- Pos 2: Halte Administrasi & Seleksi (x: 880, y: 310 di pinggir jalan) -->
                @php [$p2Line1, $p2Line2] = $formatLines($stepsData[1]['desc'] ?? ''); @endphp
                <g class="road-checkpoint-group cursor-pointer" id="roadCp2" transform="translate(880, 310)" onclick="selectModalStep(2)" role="button" aria-label="Pilih Pos 2 {{ $stepsData[1]['title'] ?? 'Administrasi & Seleksi' }}" style="cursor: pointer;">
                    <!-- Tiang Halte Pinggir Jalan yang Rapi & Kokoh Berdiri di Trotoar -->
                    <line x1="0" y1="8" x2="0" y2="36" stroke="#475569" stroke-width="4.5" stroke-linecap="round"/>
                    <ellipse cx="0" cy="36" rx="9" ry="3" fill="#64748b"/>
                    <!-- Lingkaran Plang Halte Pos 2 -->
                    <circle cx="0" cy="-12" r="20" class="cp-circle-bg" fill="#0284c7" stroke="#ffffff" stroke-width="3" style="filter: drop-shadow(0 4px 8px rgba(2,132,199,0.4));"/>
                    <!-- Ikon Stempel Checklist Berkas -->
                    <path d="M -5,-12 L -2,-9 L 5,-16" fill="none" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                    <!-- Papan Teks Halte Pinggir Jalan -->
                    <rect x="-48" y="10" width="96" height="15" rx="4" fill="#1b3860" stroke="#ffffff" stroke-width="1.2" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));"/>
                    <text x="0" y="21" text-anchor="middle" font-size="7.5" font-weight="900" fill="#ffffff" font-family="'Lexend', sans-serif">02 {{ \Illuminate\Support\Str::limit(strtoupper($stepsData[1]['title'] ?? 'ADMINISTRASI'), 12, '') }}</text>

                    <!-- BALON KOMIK / WEBTOON SPEECH BUBBLE POS 2 (Ekor Segitiga Pendek & Rapi, Tidak Menutupi Plang) -->
                    <g class="webtoon-speech-bubble" id="webtoonBubble2">
                        <path d="M -5,-140 L 275,-140 Q 290,-140 290,-125 L 290,-30 Q 290,-15 275,-15 L 35,-15 L 20,0 L 5,-15 L -5,-15 Q -20,-15 -20,-30 L -20,-125 Q -20,-140 -5,-140 Z" fill="#ffffff" stroke="#0284c7" stroke-width="2.5" style="filter: drop-shadow(0 10px 24px rgba(15,23,42,0.22));"/>
                        <rect x="-3" y="-132" width="56" height="18" rx="9" fill="#0284c7"/>
                        <text x="25" y="-120" text-anchor="middle" font-size="8.5" font-weight="900" fill="#ffffff" font-family="'Lexend', sans-serif">POS 02</text>
                        <text x="65" y="-119" text-anchor="start" font-size="11" font-weight="900" fill="#0f172a" font-family="'Lexend', sans-serif">{!! strtoupper($stepsData[1]['title'] ?? 'ADMINISTRASI & SELEKSI') !!}</text>
                        <line x1="-3" y1="-106" x2="273" y2="-106" stroke="#f1f5f9" stroke-width="1.5"/>
                        <text x="-3" y="-88" font-size="9.2" font-weight="600" fill="#334155" font-family="'Plus Jakarta Sans', sans-serif">{{ $p2Line1 }}</text>
                        <text x="-3" y="-72" font-size="9.2" font-weight="600" fill="#334155" font-family="'Plus Jakarta Sans', sans-serif">{{ $p2Line2 }}</text>
                        <g class="cursor-pointer btn-webtoon-action" onclick="event.stopPropagation(); selectModalStep(3);" role="button" aria-label="Lanjut ke Pos 3">
                            <rect x="150" y="-52" width="122" height="26" rx="13" fill="#0284c7" style="filter: drop-shadow(0 2px 6px rgba(2,132,199,0.3));"/>
                            <text x="211" y="-36" text-anchor="middle" font-size="9" font-weight="800" fill="#ffffff" font-family="'Lexend', sans-serif">Lanjut Pos 3 ➔</text>
                        </g>
                    </g>
                </g>

                <!-- Pos 3: Halte Pelaksanaan Magang (x: 1480, y: 310 di pinggir jalan) -->
                @php [$p3Line1, $p3Line2] = $formatLines($stepsData[2]['desc'] ?? ''); @endphp
                <g class="road-checkpoint-group cursor-pointer" id="roadCp3" transform="translate(1480, 310)" onclick="selectModalStep(3)" role="button" aria-label="Pilih Pos 3 {{ $stepsData[2]['title'] ?? 'Pelaksanaan Magang' }}" style="cursor: pointer;">
                    <!-- Tiang Halte Pinggir Jalan yang Rapi & Kokoh Berdiri di Trotoar -->
                    <line x1="0" y1="8" x2="0" y2="36" stroke="#475569" stroke-width="4.5" stroke-linecap="round"/>
                    <ellipse cx="0" cy="36" rx="9" ry="3" fill="#64748b"/>
                    <!-- Lingkaran Plang Halte Pos 3 -->
                    <circle cx="0" cy="-12" r="20" class="cp-circle-bg" fill="#0f766e" stroke="#ffffff" stroke-width="3" style="filter: drop-shadow(0 4px 8px rgba(15,118,110,0.4));"/>
                    <!-- Ikon Laptop & Bimbingan -->
                    <rect x="-6" y="-17" width="12" height="8" rx="1" fill="#ffffff"/>
                    <line x1="-8" y1="-8" x2="8" y2="-8" stroke="#ffffff" stroke-width="1.8"/>
                    <!-- Papan Teks Halte Pinggir Jalan -->
                    <rect x="-48" y="10" width="96" height="15" rx="4" fill="#1b3860" stroke="#ffffff" stroke-width="1.2" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));"/>
                    <text x="0" y="21" text-anchor="middle" font-size="7.5" font-weight="900" fill="#ffffff" font-family="'Lexend', sans-serif">03 {{ \Illuminate\Support\Str::limit(strtoupper($stepsData[2]['title'] ?? 'PELAKSANAAN'), 12, '') }}</text>

                    <!-- BALON KOMIK / WEBTOON SPEECH BUBBLE POS 3 (Ekor Segitiga Pendek & Rapi, Tidak Menutupi Plang) -->
                    <g class="webtoon-speech-bubble" id="webtoonBubble3">
                        <path d="M -5,-140 L 275,-140 Q 290,-140 290,-125 L 290,-30 Q 290,-15 275,-15 L 35,-15 L 20,0 L 5,-15 L -5,-15 Q -20,-15 -20,-30 L -20,-125 Q -20,-140 -5,-140 Z" fill="#ffffff" stroke="#0f766e" stroke-width="2.5" style="filter: drop-shadow(0 10px 24px rgba(15,23,42,0.22));"/>
                        <rect x="-3" y="-132" width="56" height="18" rx="9" fill="#0f766e"/>
                        <text x="25" y="-120" text-anchor="middle" font-size="8.5" font-weight="900" fill="#ffffff" font-family="'Lexend', sans-serif">POS 03</text>
                        <text x="65" y="-119" text-anchor="start" font-size="11" font-weight="900" fill="#0f172a" font-family="'Lexend', sans-serif">{!! strtoupper($stepsData[2]['title'] ?? 'PELAKSANAAN MAGANG') !!}</text>
                        <line x1="-3" y1="-106" x2="273" y2="-106" stroke="#f1f5f9" stroke-width="1.5"/>
                        <text x="-3" y="-88" font-size="9.2" font-weight="600" fill="#334155" font-family="'Plus Jakarta Sans', sans-serif">{{ $p3Line1 }}</text>
                        <text x="-3" y="-72" font-size="9.2" font-weight="600" fill="#334155" font-family="'Plus Jakarta Sans', sans-serif">{{ $p3Line2 }}</text>
                        <g class="cursor-pointer btn-webtoon-action" onclick="event.stopPropagation(); selectModalStep(4);" role="button" aria-label="Lanjut ke Pos 4">
                            <rect x="150" y="-52" width="122" height="26" rx="13" fill="#0f766e" style="filter: drop-shadow(0 2px 6px rgba(15,118,110,0.3));"/>
                            <text x="211" y="-36" text-anchor="middle" font-size="9" font-weight="800" fill="#ffffff" font-family="'Lexend', sans-serif">Lanjut Pos 4 ➔</text>
                        </g>
                    </g>
                </g>

                <!-- Pos 4: Halte Kelulusan (x: 2080, y: 310 di pinggir jalan) -->
                @php [$p4Line1, $p4Line2] = $formatLines($stepsData[3]['desc'] ?? ''); @endphp
                <g class="road-checkpoint-group cursor-pointer" id="roadCp4" transform="translate(2080, 310)" onclick="selectModalStep(4)" role="button" aria-label="Pilih Pos 4 {{ $stepsData[3]['title'] ?? 'Kelulusan' }}" style="cursor: pointer;">
                    <!-- Tiang Halte Pinggir Jalan yang Rapi & Kokoh Berdiri di Trotoar -->
                    <line x1="0" y1="8" x2="0" y2="36" stroke="#475569" stroke-width="4.5" stroke-linecap="round"/>
                    <ellipse cx="0" cy="36" rx="9" ry="3" fill="#64748b"/>
                    <!-- Lingkaran Plang Halte Pos 4 -->
                    <circle cx="0" cy="-12" r="20" class="cp-circle-bg" fill="#f59e0b" stroke="#ffffff" stroke-width="3" style="filter: drop-shadow(0 4px 8px rgba(245,158,11,0.4));"/>
                    <!-- Ikon Topi Toga Wisuda -->
                    <polygon points="0,-18 7,-14 0,-10 -7,-14" fill="#ffffff"/>
                    <line x1="4" y1="-12" x2="6" y2="-8" stroke="#ffffff" stroke-width="1.2"/>
                    <!-- Papan Teks Halte Pinggir Jalan -->
                    <rect x="-44" y="10" width="88" height="15" rx="4" fill="#1b3860" stroke="#ffffff" stroke-width="1.2" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));"/>
                    <text x="0" y="21" text-anchor="middle" font-size="7.5" font-weight="900" fill="#ffffff" font-family="'Lexend', sans-serif">04 {{ \Illuminate\Support\Str::limit(strtoupper($stepsData[3]['title'] ?? 'KELULUSAN'), 12, '') }}</text>

                    <!-- BALON KOMIK / WEBTOON SPEECH BUBBLE POS 4 (Ekor Segitiga Pendek & Rapi, Tidak Menutupi Plang) -->
                    <g class="webtoon-speech-bubble" id="webtoonBubble4">
                        <path d="M -5,-140 L 275,-140 Q 290,-140 290,-125 L 290,-30 Q 290,-15 275,-15 L 50,-15 L 35,0 L 20,-15 L -5,-15 Q -20,-15 -20,-30 L -20,-125 Q -20,-140 -5,-140 Z" fill="#ffffff" stroke="#f59e0b" stroke-width="2.5" style="filter: drop-shadow(0 10px 24px rgba(15,23,42,0.22));"/>
                        <rect x="-3" y="-132" width="56" height="18" rx="9" fill="#f59e0b"/>
                        <text x="25" y="-120" text-anchor="middle" font-size="8.5" font-weight="900" fill="#ffffff" font-family="'Lexend', sans-serif">POS 04</text>
                        <text x="65" y="-119" text-anchor="start" font-size="11" font-weight="900" fill="#0f172a" font-family="'Lexend', sans-serif">{!! !str_contains(strtoupper($stepsData[3]['title'] ?? ''), 'SERTIFIKAT') && strtoupper($stepsData[3]['title'] ?? '') === 'KELULUSAN' ? 'KELULUSAN & SERTIFIKAT' : strtoupper($stepsData[3]['title'] ?? 'KELULUSAN & SERTIFIKAT') !!}</text>
                        <line x1="-3" y1="-106" x2="273" y2="-106" stroke="#f1f5f9" stroke-width="1.5"/>
                        <text x="-3" y="-88" font-size="9.2" font-weight="600" fill="#334155" font-family="'Plus Jakarta Sans', sans-serif">{{ $p4Line1 }}</text>
                        <text x="-3" y="-72" font-size="9.2" font-weight="600" fill="#334155" font-family="'Plus Jakarta Sans', sans-serif">{{ $p4Line2 }}</text>
                        <a href="{{ route('landing') }}#kuota" class="cursor-pointer" aria-label="Daftar Sekarang">
                            <rect x="150" y="-52" width="122" height="26" rx="13" fill="#16a34a" style="filter: drop-shadow(0 2px 6px rgba(22,163,74,0.35));"/>
                            <text x="211" y="-36" text-anchor="middle" font-size="9" font-weight="800" fill="#ffffff" font-family="'Lexend', sans-serif">Daftar Sekarang 🚀</text>
                        </a>
                    </g>
                </g>

                <!-- LAYER 7: KARAKTER MANUSIA ANIMASI UTUH (FULL-BODY) BERDIRI TEGAK DI TROTOAR TIAP POS -->
                <g id="modalCharacterScene" class="living-character-showcase">
                    <!-- Karakter Pos 1: Mahasiswa Muda Utuh Membawa Map Pendaftaran & Menyapa Ramah -->
                    <g id="charScene1" class="char-scene-item" transform="translate(180, 285)">
                        <!-- Bayangan Halus di Atas Trotoar -->
                        <ellipse cx="25" cy="63" rx="18" ry="4" fill="#0f172a" opacity="0.18"/>
                        <!-- Kaki & Sepatu Sneakers Utuh -->
                        <rect x="18" y="38" width="6" height="23" rx="2.5" fill="#334155"/>
                        <path d="M 15,57 L 25,57 L 25,62 L 14,62 Z" fill="#f8fafc" stroke="#0284c7" stroke-width="0.8"/>
                        <rect x="26" y="38" width="6" height="23" rx="2.5" fill="#334155"/>
                        <path d="M 26,57 L 36,57 L 36,62 L 25,62 Z" fill="#f8fafc" stroke="#0284c7" stroke-width="0.8"/>
                        <!-- Badan: Kaos Polo Merah MAGKOM Rapi -->
                        <rect x="16" y="16" width="18" height="23" rx="3" fill="#e11d48"/>
                        <polygon points="25,18 21,24 29,24" fill="#ffffff"/>
                        <!-- Ransel Mahasiswa di Punggung -->
                        <rect x="11" y="18" width="6" height="16" rx="2" fill="#1e293b"/>
                        <!-- Leher & Kepala dengan Rambut Trendi -->
                        <rect x="23" y="11" width="4" height="6" fill="#fed7aa"/>
                        <circle cx="25" cy="7" r="9" fill="#fed7aa"/>
                        <circle cx="27.5" cy="6.5" r="1.2" fill="#0f172a"/>
                        <path d="M 26,10 Q 28,12 30,10" fill="none" stroke="#0f172a" stroke-width="0.8" stroke-linecap="round"/>
                        <path d="M 16,5 Q 25,-3 34,4 Q 31,0 25,0 Q 18,1 16,5 Z" fill="#1e293b"/>
                        <!-- Lengan Kiri Memegang Map Formulir MAGKOM -->
                        <path d="M 17,20 L 10,26" fill="none" stroke="#fed7aa" stroke-width="3" stroke-linecap="round"/>
                        <rect x="6" y="22" width="10" height="13" rx="1.5" fill="#ffffff" stroke="#e11d48" stroke-width="1.2"/>
                        <line x1="8" y1="26" x2="14" y2="26" stroke="#e11d48" stroke-width="0.8"/>
                        <line x1="8" y1="29" x2="14" y2="29" stroke="#e11d48" stroke-width="0.8"/>
                        <!-- Lengan Kanan Melambai Menyapa Calon Peserta -->
                        <g class="char-wave-arm">
                            <path d="M 33,20 Q 42,12 38,2" fill="none" stroke="#fed7aa" stroke-width="3" stroke-linecap="round"/>
                            <circle cx="38" cy="2" r="2.5" fill="#fed7aa"/>
                        </g>
                        <!-- Partikel Badge Akun Terverifikasi Mengapung -->
                        <g class="floating-account-particle" transform="translate(42, -6)">
                            <circle cx="0" cy="0" r="9" fill="#16a34a" stroke="#ffffff" stroke-width="1.8"/>
                            <polyline points="-3.5,0 -1,2.8 3.5,-2.8" fill="none" stroke="#ffffff" stroke-width="1.8" stroke-linecap="round"/>
                        </g>
                    </g>

                    <!-- Karakter Pos 2: Petugas ASN Sleman Utuh Berseragam Dinas & Clipboard Berkas -->
                    <g id="charScene2" class="char-scene-item" transform="translate(770, 285)">
                        <!-- Bayangan Halus di Trotoar -->
                        <ellipse cx="25" cy="63" rx="18" ry="4" fill="#0f172a" opacity="0.18"/>
                        <!-- Kaki & Sepatu Pantofel Formal -->
                        <rect x="18" y="38" width="6" height="23" rx="2.5" fill="#1e293b"/>
                        <path d="M 15,57 L 25,57 L 25,62 L 14,62 Z" fill="#0f172a"/>
                        <rect x="26" y="38" width="6" height="23" rx="2.5" fill="#1e293b"/>
                        <path d="M 26,57 L 36,57 L 36,62 L 25,62 Z" fill="#0f172a"/>
                        <!-- Baju Dinas Harian Biru Diskominfo / Khaki Pemkab -->
                        <rect x="16" y="16" width="18" height="23" rx="3" fill="#1d4ed8"/>
                        <polygon points="25,16 23,21 27,21" fill="#ffffff"/>
                        <!-- Lencana Korpri Emas di Dada -->
                        <circle cx="20" cy="21" r="1.5" fill="#f59e0b"/>
                        <!-- Leher & Kepala dengan Kacamata Profesional -->
                        <rect x="23" y="11" width="4" height="6" fill="#fed7aa"/>
                        <circle cx="25" cy="7" r="9" fill="#fed7aa"/>
                        <circle cx="23.5" cy="6.5" r="2" fill="none" stroke="#0f172a" stroke-width="0.7"/>
                        <circle cx="28" cy="6.5" r="2" fill="none" stroke="#0f172a" stroke-width="0.7"/>
                        <line x1="25.5" y1="6.5" x2="26" y2="6.5" stroke="#0f172a" stroke-width="0.7"/>
                        <path d="M 16,5 Q 25,-3 34,4 Q 31,0 25,-1 Q 17,0 16,5 Z" fill="#1e293b"/>
                        <!-- Papan Klip (Clipboard) Checklist Berkas -->
                        <g transform="translate(18, 20)">
                            <rect x="-4" y="0" width="14" height="18" rx="1.5" fill="#78350f"/>
                            <rect x="-2" y="2" width="10" height="14" rx="1" fill="#ffffff"/>
                            <rect x="0" y="-1.5" width="6" height="3" rx="0.5" fill="#94a3b8"/>
                            <polyline points="0,6 2,8 6,4" fill="none" stroke="#16a34a" stroke-width="1.2" stroke-linecap="round"/>
                            <polyline points="0,11 2,13 6,9" fill="none" stroke="#16a34a" stroke-width="1.2" stroke-linecap="round"/>
                        </g>
                        <!-- Lengan Stempel Verifikasi -->
                        <g class="stamp-arm-anim" transform="translate(36, 16)">
                            <line x1="0" y1="0" x2="-4" y2="10" stroke="#fed7aa" stroke-width="3" stroke-linecap="round"/>
                            <rect x="-8" y="10" width="8" height="5" rx="1" fill="#ef4444"/>
                            <circle cx="-4" cy="-2" r="2.5" fill="#78350f"/>
                        </g>
                        <!-- Badge Floating VERIFIED Berkas Lolos -->
                        <g transform="translate(48, -4)">
                            <rect x="-6" y="-6" width="56" height="18" rx="5" fill="#16a34a" stroke="#ffffff" stroke-width="1.5" style="filter: drop-shadow(0 2px 6px rgba(22,163,74,0.3));"/>
                            <text x="22" y="6" text-anchor="middle" font-size="7.5" font-weight="900" fill="#ffffff" font-family="'Lexend', sans-serif">VERIFIED ✓</text>
                        </g>
                    </g>

                    <!-- Karakter Pos 3: Mentor ASN Senior & Mahasiswa Magang Utuh Kolaborasi Proyek -->
                    <g id="charScene3" class="char-scene-item" transform="translate(1360, 285)">
                        <!-- Bayangan Berdua di Trotoar -->
                        <ellipse cx="32" cy="63" rx="30" ry="4.5" fill="#0f172a" opacity="0.2"/>
                        <!-- Mahasiswa Magang (Kiri): Kaki Jeans & Sneakers -->
                        <rect x="8" y="38" width="5.5" height="23" rx="2" fill="#1e3a8a"/>
                        <path d="M 6,58 L 14,58 L 14,62 L 5,62 Z" fill="#f8fafc"/>
                        <rect x="15" y="38" width="5.5" height="23" rx="2" fill="#1e3a8a"/>
                        <path d="M 14,58 L 22,58 L 22,62 L 13,62 Z" fill="#f8fafc"/>
                        <!-- Badan Mahasiswa Kaos Toska -->
                        <rect x="7" y="16" width="15" height="23" rx="3" fill="#0f766e"/>
                        <!-- Wajah Mahasiswa Menyimak -->
                        <circle cx="14.5" cy="8" r="8" fill="#fed7aa"/>
                        <path d="M 7,6 Q 14.5,-1 22,5 Z" fill="#1e293b"/>
                        <!-- Tablet Proyek Digital -->
                        <rect x="16" y="24" width="13" height="9" rx="1.5" fill="#1e293b" stroke="#38bdf8" stroke-width="0.8"/>
                        <rect x="18" y="25.5" width="9" height="6" fill="#e0f2fe"/>
                        <!-- Mentor ASN Senior (Kanan): Kaki Celana Formal & Pantofel -->
                        <rect x="34" y="38" width="6" height="23" rx="2.5" fill="#0f172a"/>
                        <path d="M 32,58 L 41,58 L 41,62 L 31,62 Z" fill="#0f172a"/>
                        <rect x="42" y="38" width="6" height="23" rx="2.5" fill="#0f172a"/>
                        <path d="M 41,58 L 50,58 L 50,62 L 40,62 Z" fill="#0f172a"/>
                        <!-- Badan Mentor Seragam Batik Sleman Navy -->
                        <rect x="32" y="15" width="18" height="24" rx="3" fill="#1b3860"/>
                        <line x1="41" y1="15" x2="41" y2="25" stroke="#f59e0b" stroke-width="1.5"/>
                        <!-- Kepala Mentor (Animasi Mengangguk Bimbingan) -->
                        <g class="mentor-nod-anim">
                            <circle cx="41" cy="7" r="8.5" fill="#fed7aa"/>
                            <path d="M 33,5 Q 41,-2 49,4 Z" fill="#475569"/>
                            <path d="M 33,22 L 27,27" stroke="#fed7aa" stroke-width="2.8" stroke-linecap="round"/>
                        </g>
                        <!-- Balon Dialog Mini Ide Kolaborasi Inovatif -->
                        <g transform="translate(28, -12)">
                            <rect x="-12" y="-10" width="28" height="15" rx="6" fill="#ffffff" stroke="#0f766e" stroke-width="1.5"/>
                            <circle cx="-4" cy="-2.5" r="1.8" fill="#0f766e"/>
                            <circle cx="2" cy="-2.5" r="1.8" fill="#0f766e"/>
                            <circle cx="8" cy="-2.5" r="1.8" fill="#0f766e"/>
                        </g>
                    </g>

                    <!-- Karakter Pos 4: Wisudawan Magang Utuh Berjubah & Topi Toga Mengangkat E-Sertifikat -->
                    <g id="charScene4" class="char-scene-item" transform="translate(1960, 280)">
                        <!-- Bayangan di Trotoar -->
                        <ellipse cx="28" cy="68" rx="22" ry="4.5" fill="#0f172a" opacity="0.2"/>
                        <!-- Kaki Celana Hitam & Pantofel -->
                        <rect x="20" y="42" width="6" height="24" rx="2.5" fill="#0f172a"/>
                        <path d="M 17,63 L 27,63 L 27,67 L 16,67 Z" fill="#0f172a"/>
                        <rect x="29" y="42" width="6" height="24" rx="2.5" fill="#0f172a"/>
                        <path d="M 28,63 L 38,63 L 38,67 L 27,67 Z" fill="#0f172a"/>
                        <!-- Jubah Toga Wisuda Hitam Elegan -->
                        <path d="M 17,20 L 39,20 L 42,45 L 14,45 Z" fill="#1b3860" stroke="#0f172a" stroke-width="1"/>
                        <!-- Samir / Selendang Wisuda Kuning Emas Sleman -->
                        <path d="M 23,20 L 26,36 L 28,36 L 33,20" fill="none" stroke="#f59e0b" stroke-width="3" stroke-linecap="round"/>
                        <circle cx="27" cy="38" r="3.5" fill="#f59e0b" stroke="#ffffff" stroke-width="0.8"/>
                        <!-- Wajah Gembira & Topi Toga Wisuda -->
                        <circle cx="28" cy="11" r="9" fill="#fed7aa"/>
                        <path d="M 25,13 Q 28,17 31,13" fill="#ffffff" stroke="#0f172a" stroke-width="0.8"/>
                        <!-- Topi Toga Segiempat Berumbai Emas -->
                        <polygon points="12,4 44,4 28,-3" fill="#0f172a"/>
                        <rect x="21" y="4" width="14" height="4" fill="#1e293b"/>
                        <line x1="28" y1="-3" x2="38" y2="7" stroke="#f59e0b" stroke-width="1.8"/>
                        <!-- Tangan Mengangkat Tabung E-Sertifikat Kelulusan Emas -->
                        <g class="char-jump-joy">
                            <path d="M 37,22 L 46,12" stroke="#fed7aa" stroke-width="3.2" stroke-linecap="round"/>
                            <rect x="44" y="2" width="8" height="16" rx="2" fill="#f59e0b" stroke="#ffffff" stroke-width="1.2" transform="rotate(25, 48, 10)"/>
                            <line x1="47" y1="5" x2="52" y2="5" stroke="#ffffff" stroke-width="1" transform="rotate(25, 48, 10)"/>
                            <line x1="47" y1="9" x2="52" y2="9" stroke="#ffffff" stroke-width="1" transform="rotate(25, 48, 10)"/>
                        </g>
                        <!-- Konfeti Perayaan Kelulusan Bertebaran -->
                        <g class="confetti-particles">
                            <rect x="6" y="-12" width="3.5" height="3.5" fill="#f59e0b" class="confetti-c1"/>
                            <rect x="48" y="-6" width="4" height="2.5" fill="#0284c7" class="confetti-c2"/>
                            <rect x="12" y="6" width="2.5" height="4" fill="#ef4444" class="confetti-c3"/>
                            <rect x="52" y="16" width="3.5" height="3.5" fill="#16a34a" class="confetti-c1"/>
                        </g>
                    </g>

                    <!-- Animasi Orang-Orang Bergerak Berjalan Santai di Trotoar Pinggir Jalan (Living Pedestrians) -->
                    <!-- Pejalan Kaki 1 (Mahasiswa Magang Berjalan Santai) -->
                    <g class="animated-pedestrian walker-1" transform="translate(540, 310)">
                        <circle cx="8" cy="-6" r="6.5" fill="#fed7aa"/>
                        <rect x="4" y="0" width="8" height="15" rx="2" fill="#0284c7"/>
                        <!-- Tas Ransel -->
                        <rect x="-1" y="2" width="5" height="9" rx="1.5" fill="#1e293b"/>
                        <line x1="5" y1="15" x2="3" y2="28" stroke="#1e293b" stroke-width="2.5" class="walker-leg-1"/>
                        <line x1="11" y1="15" x2="13" y2="28" stroke="#1e293b" stroke-width="2.5" class="walker-leg-2"/>
                    </g>

                    <!-- Pejalan Kaki 2 (Staf ASN Sleman Berjalan Membawa Berkas) -->
                    <g class="animated-pedestrian walker-2" transform="translate(1180, 310)">
                        <circle cx="8" cy="-6" r="6.5" fill="#fed7aa"/>
                        <rect x="4" y="0" width="8" height="15" rx="2" fill="#1b3860"/>
                        <!-- Papan Berkas Putih -->
                        <rect x="10" y="4" width="6" height="8" rx="1" fill="#ffffff" stroke="#94a3b8" stroke-width="0.8"/>
                        <line x1="5" y1="15" x2="3" y2="28" stroke="#334155" stroke-width="2.5" class="walker-leg-1"/>
                        <line x1="11" y1="15" x2="13" y2="28" stroke="#334155" stroke-width="2.5" class="walker-leg-2"/>
                    </g>
                </g>

                <!-- LAYER 8: NISSAN SKYLINE GT-R R34 (BAYSIDE BLUE) SESUAI GAMBAR PENGGUNA TANPA TULISAN -->
                <g id="modalRoadCarGroup" transform="translate(280, 390)" class="cinematic-car-body" role="slider" aria-label="Nissan Skyline GT-R R34 Bayside Blue (Dapat Digerakkan dengan Analog / Panah Keyboard)" tabindex="0" style="cursor: grab;">
                    <!-- Wrapper Suspensi Halus Mobil (Gerak Lokal, Tidak Menimpa Posisi Dunia Mobil) -->
                    <g class="car-suspension-wrap">
                        <!-- Sorotan Lampu Depan LED Xenon di Atas Aspal (Menyorot ke Arah Kanan) -->
                        <polygon class="car-headlight-beam" id="carHeadlightBeam" points="72,8 190,-12 190,26 72,18" fill="url(#headlightGrad)" opacity="0.68"/>

                        <!-- Efek Visual Gelombang Suara Klakson Sport (( 📢 )) -->
                        <g id="carHornRipples" class="car-horn-ripples" pointer-events="none">
                            <path class="horn-wave horn-wave-1" d="M 76,0 Q 86,10 76,20" fill="none" stroke="#facc15" stroke-width="2.5" stroke-linecap="round"/>
                            <path class="horn-wave horn-wave-2" d="M 84,-6 Q 98,10 84,26" fill="none" stroke="#38bdf8" stroke-width="2.5" stroke-linecap="round"/>
                            <path class="horn-wave horn-wave-3" d="M 92,-12 Q 110,10 92,32" fill="none" stroke="#facc15" stroke-width="2" stroke-linecap="round"/>
                        </g>

                        <!-- Bayangan Bawah Mobil di Atas Aspal (Ground Shadow GT-R R34) -->
                        <ellipse cx="0" cy="27" rx="80" ry="6.5" fill="#090d16" opacity="0.5"/>
                        <rect x="-70" y="24" width="144" height="3.5" rx="1.75" fill="#020617" opacity="0.65"/>

                        <!-- Pipa Knalpot Balap Stainless Chrome Besar (JDM Big Single Exhaust Tip) -->
                        <ellipse cx="-72" cy="21" rx="4" ry="3.5" fill="#64748b" stroke="#cbd5e1" stroke-width="1.2"/>
                        <ellipse cx="-72" cy="21" rx="2.5" ry="2" fill="#090d16"/>

                        <!-- SPOILER BELAKANG IKONIK SKYLINE GT-R R34 (REAR WING AERO) -->
                        <!-- Tiang Spoiler Kiri & Kanan -->
                        <path d="M -56,3 L -59,-13 L -55,-13 L -52,3 Z" fill="#1d4ed8"/>
                        <path d="M -46,3 L -48,-13 L -44,-13 L -42,3 Z" fill="#2563eb"/>
                        <!-- Bilah Sayap Atas Aerodinamis GT-R R34 -->
                        <path d="M -66,-13 Q -51,-17 -36,-13 L -35,-10.5 Q -51,-14.5 -65,-10.5 Z" fill="#3b82f6" stroke="#1d4ed8" stroke-width="1"/>
                        <polygon points="-66,-13 -63,-7 -59,-7 -62,-13" fill="#1e3a8a"/>
                        <polygon points="-40,-13 -37,-7 -33,-7 -36,-13" fill="#1e40af"/>

                        <!-- BODI UTAMA NISSAN SKYLINE GT-R R34 (WARNA BAYSIDE BLUE #2563eb DENGAN LEKUKAN AGRESIF) -->
                        <!-- Siluet Silindris & Garis Panel Aerokit Samping -->
                        <path d="M 76,21 L 78,14 Q 77,9 74,7 L 66,6 L 20,2 L 16,1 L -4,-16 L -32,-16 L -54,2 L -68,3 L -72,8 L -73,14 L -70,21 L -58,21 Q -56,10 -42,6 Q -28,10 -26,21 L 26,21 Q 28,10 42,6 Q 56,10 58,21 Z"
                              fill="#2563eb" stroke="#1d4ed8" stroke-width="1.8" stroke-linejoin="round"/>

                        <!-- Highlight Kilap Atas Bodi & Kap Mesin Bayside Blue -->
                        <path d="M 74,7 L 66,6 L 20,2 L 16,1 L -4,-16 L -32,-16 L -34,-14 L -4,-14 L 14,3 L 64,8 Z" fill="#60a5fa" opacity="0.45"/>

                        <!-- Garis Karakter Bahu Bodi (Muscular Shoulder Line R34) -->
                        <path d="M -68,6 Q -10,6 64,8" stroke="#1e40af" stroke-width="1.2" fill="none"/>
                        <!-- Fender Flare Depan & Belakang Berotot (GT-R Widebody) -->
                        <path d="M -58,21 Q -56,7 -42,4 Q -28,7 -26,21" stroke="#1e3a8a" stroke-width="1.4" fill="none"/>
                        <path d="M 26,21 Q 28,7 42,4 Q 56,7 58,21" stroke="#1e3a8a" stroke-width="1.4" fill="none"/>

                        <!-- Front Lip Spoiler & Side Skirt Aerodinamis Hitam Matte -->
                        <rect x="62" y="19" width="16" height="3" rx="1" fill="#0f172a"/>
                        <rect x="-24" y="20" width="50" height="2.5" rx="0.8" fill="#0f172a"/>
                        <rect x="-71" y="19" width="12" height="3" rx="1" fill="#0f172a"/>

                        <!-- KABIN & KACA JENDELA GELAP COUPE (DARK TINTED GLASS) -->
                        <!-- Kaca Samping Tinted Hitam Pekat -->
                        <path d="M 14,1 L -3,-14 L -30,-14 L -49,1 Z" fill="#090d16" stroke="#020617" stroke-width="1.5"/>
                        <!-- Pantulan Garis Diagonal Kaca Samping -->
                        <polygon points="8,1 0,1 -10,-14 -2,-14" fill="#ffffff" opacity="0.2"/>
                        <polygon points="-16,1 -24,1 -34,-14 -26,-14" fill="#ffffff" opacity="0.12"/>
                        <!-- Pilar B Hitam Matte R34 -->
                        <rect x="-14" y="-14" width="4" height="15" fill="#020617"/>
                        <!-- Spion Samping Aerodinamis Bayside Blue -->
                        <path d="M 13,0 Q 18,-2 19,2 Q 15,3 12,2 Z" fill="#2563eb" stroke="#1d4ed8" stroke-width="0.8"/>

                        <!-- LAMPU DEPAN MENYIPIT XENON KHAS SKYLINE R34 -->
                        <polygon points="64,5 73,6 72,10 63,9" fill="#0f172a" stroke="#64748b" stroke-width="0.8"/>
                        <circle cx="68" cy="7.5" r="2.2" fill="#e0f2fe" style="filter: drop-shadow(0 0 4px #38bdf8);"/>
                        <circle cx="68" cy="7.5" r="1.2" fill="#ffffff"/>

                        <!-- LAMPU BELAKANG BULAT IKONIK SKYLINE GT-R -->
                        <ellipse cx="-70" cy="7" rx="2.5" ry="4" fill="#ef4444" stroke="#991b1b" stroke-width="0.8"/>
                        <circle cx="-70" cy="7" r="1.5" fill="#fca5a5"/>

                        <!-- Lubang Pendingin Rem Bumper Depan (Intercooler & Brake Air Duct) -->
                        <polygon points="68,13 75,13 74,18 67,18" fill="#020617"/>
                        <line x1="68" y1="15" x2="74" y2="15" stroke="#334155" stroke-width="0.6"/>

                        <!-- Garis Pintu Coupe & Gagang Pintu Halus -->
                        <path d="M -24,3 L -24,20" stroke="#172554" stroke-width="1.2"/>
                        <rect x="-18" y="6" width="7" height="1.8" rx="0.9" fill="#1e40af"/>

                        <!-- RODA BELAKANG (Bisa Berputar - Velg Racing 6-Spoke NISMO LM GT4 Style) -->
                        <g id="carWheelRear" transform="translate(-42, 21)" class="car-wheel-group">
                            <!-- Ban Luar Karet Hitam Sporty (Profil Rendah) -->
                            <circle cx="0" cy="0" r="14" fill="#18181b" stroke="#090d16" stroke-width="1"/>
                            <!-- Tapak Velg Dalam -->
                            <circle cx="0" cy="0" r="10.5" fill="#27272a"/>
                            <!-- Cakram Rem Ventilasi Perak Logam -->
                            <circle cx="0" cy="0" r="8.5" fill="#94a3b8" stroke="#cbd5e1" stroke-width="0.6"/>
                            <circle cx="0" cy="0" r="6.5" fill="none" stroke="#64748b" stroke-width="0.6" stroke-dasharray="1.5,1.5"/>
                            <!-- Kaliper Rem Sport Anodized Blue -->
                            <path d="M 5,-5 Q 8,0 5,5" stroke="#38bdf8" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                            <!-- Palang Velg Racing Bintang 6 Hitam Glossy (NISMO LM GT4 Style) -->
                            <circle cx="0" cy="0" r="3.2" fill="#09090b" stroke="#27272a" stroke-width="0.8"/>
                            <circle cx="0" cy="0" r="1.2" fill="#cbd5e1"/>
                            <line x1="0" y1="-2.5" x2="0" y2="-10" stroke="#09090b" stroke-width="2.2" stroke-linecap="round"/>
                            <line x1="2.2" y1="-1.2" x2="8.6" y2="-5" stroke="#09090b" stroke-width="2.2" stroke-linecap="round"/>
                            <line x1="2.2" y1="1.2" x2="8.6" y2="5" stroke="#09090b" stroke-width="2.2" stroke-linecap="round"/>
                            <line x1="0" y1="2.5" x2="0" y2="10" stroke="#09090b" stroke-width="2.2" stroke-linecap="round"/>
                            <line x1="-2.2" y1="1.2" x2="-8.6" y2="5" stroke="#09090b" stroke-width="2.2" stroke-linecap="round"/>
                            <line x1="-2.2" y1="-1.2" x2="-8.6" y2="-5" stroke="#09090b" stroke-width="2.2" stroke-linecap="round"/>
                        </g>

                        <!-- RODA DEPAN (Bisa Berputar - Velg Racing 6-Spoke NISMO LM GT4 Style) -->
                        <g id="carWheelFront" transform="translate(42, 21)" class="car-wheel-group">
                            <!-- Ban Luar Karet Hitam Sporty (Profil Rendah) -->
                            <circle cx="0" cy="0" r="14" fill="#18181b" stroke="#090d16" stroke-width="1"/>
                            <!-- Tapak Velg Dalam -->
                            <circle cx="0" cy="0" r="10.5" fill="#27272a"/>
                            <!-- Cakram Rem Ventilasi Perak Logam -->
                            <circle cx="0" cy="0" r="8.5" fill="#94a3b8" stroke="#cbd5e1" stroke-width="0.6"/>
                            <circle cx="0" cy="0" r="6.5" fill="none" stroke="#64748b" stroke-width="0.6" stroke-dasharray="1.5,1.5"/>
                            <!-- Kaliper Rem Sport Anodized Blue -->
                            <path d="M 5,-5 Q 8,0 5,5" stroke="#38bdf8" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                            <!-- Palang Velg Racing Bintang 6 Hitam Glossy (NISMO LM GT4 Style) -->
                            <circle cx="0" cy="0" r="3.2" fill="#09090b" stroke="#27272a" stroke-width="0.8"/>
                            <circle cx="0" cy="0" r="1.2" fill="#cbd5e1"/>
                            <line x1="0" y1="-2.5" x2="0" y2="-10" stroke="#09090b" stroke-width="2.2" stroke-linecap="round"/>
                            <line x1="2.2" y1="-1.2" x2="8.6" y2="-5" stroke="#09090b" stroke-width="2.2" stroke-linecap="round"/>
                            <line x1="2.2" y1="1.2" x2="8.6" y2="5" stroke="#09090b" stroke-width="2.2" stroke-linecap="round"/>
                            <line x1="0" y1="2.5" x2="0" y2="10" stroke="#09090b" stroke-width="2.2" stroke-linecap="round"/>
                            <line x1="-2.2" y1="1.2" x2="-8.6" y2="5" stroke="#09090b" stroke-width="2.2" stroke-linecap="round"/>
                            <line x1="-2.2" y1="-1.2" x2="-8.6" y2="-5" stroke="#09090b" stroke-width="2.2" stroke-linecap="round"/>
                        </g>
                    </g>
                </g>
            </svg>
        </main>

    </div>

    <!-- =========================================================================
         3. FOOTER PERSISTENT SLEMAN NAVY (SELARAS DASHBOARD MAGKOM)
         ========================================================================= -->
    <footer class="peta-alur-mini-footer">
        <div class="peta-footer-inner">
            <span class="peta-footer-copy">&copy; {{ date('Y') }} Dinas Komunikasi dan Informatika Kabupaten Sleman. MAGKOM — Sistem Penerimaan Mahasiswa Magang.</span>
        </div>
    </footer>

</div>

</body>
</html>
