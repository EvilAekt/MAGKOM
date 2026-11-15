{{-- =========================================================================
     MODAL PRATINJAU INTERAKTIF PETA ALUR MAGANG (100% PANORAMIC LIVING WORLD)
     Portal MAGKOM - Diskominfo Kabupaten Sleman
     ========================================================================= --}}

<div id="petaAlurModal" class="peta-alur-modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="modalPetaAlurTitle" aria-hidden="true" style="display: none;">
    <div class="peta-alur-modal-container modal-panoramic-container" role="document">
        
        <!-- Header Modal -->
        <div class="peta-alur-modal-header">
            <div class="peta-alur-modal-header-info">
                <div class="peta-alur-badge-strip">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polygon points="12 8 8 12 12 16 12 8"/></svg>
                    Peta Alur Interaktif • Simulator Magang
                </div>
                <h3 id="modalPetaAlurTitle" class="peta-alur-modal-title">Panduan Alur Pendaftaran &amp; Pelaksanaan Magang</h3>
                <p class="peta-alur-modal-subtitle">Diorama panorama interaktif Diskominfo Sleman: lanskap hidup, karakter dinamis, mobil sinematik berlampu sorot, dan kartu misi terpadu.</p>
            </div>
            
            <div class="peta-alur-header-actions">
                <!-- Audio Sound FX Controller -->
                <button type="button" class="btn-modal-audio-toggle" id="btnModalAudioToggle" data-action="toggle-audio" title="Aktifkan / Nonaktifkan Efek Suara & Musik Ambient" aria-label="Toggle Audio">
                    <svg id="audioIconOn" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="display: none;"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"/></svg>
                    <svg id="audioIconOff" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><line x1="23" y1="9" x2="17" y2="15"/><line x1="17" y1="9" x2="23" y2="15"/></svg>
                    <span id="audioToggleLabel">Audio FX: Off</span>
                </button>

                <div class="peta-alur-sim-mode-tag" title="Simulasi panorama sinematik aktif">
                    <span class="sim-pulse-dot"></span>
                    <span>Panorama 360°</span>
                </div>

                <button type="button" class="btn-modal-close" data-action="close-modal" aria-label="Tutup Dialog Peta Alur">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
        </div>

        <!-- Filter Quick Strip & Timeline Selector -->
        <div class="peta-alur-modal-toolbar">
            <div class="peta-alur-filter-row">
                <span style="font-weight: 600;">Kategori Jenjang:</span>
                <div class="filter-pill-group" role="radiogroup" aria-label="Kategori Jenjang">
                    <button type="button" class="filter-pill-btn is-active" data-jenjang="semua" data-action="filter-jenjang" data-jenjang="semua">Semua Jenjang</button>
                    <button type="button" class="filter-pill-btn" data-jenjang="smk" data-action="filter-jenjang" data-jenjang="smk">Siswa SMK (PKL)</button>
                    <button type="button" class="filter-pill-btn" data-jenjang="mahasiswa" data-action="filter-jenjang" data-jenjang="mahasiswa">Mahasiswa (D3/S1)</button>
                </div>
            </div>

            <div class="peta-alur-filter-row">
                <span style="font-weight: 600;">Tipe:</span>
                <div class="filter-pill-group" role="radiogroup" aria-label="Tipe Permohonan">
                    <button type="button" class="filter-pill-btn is-active" data-tipe="mandiri" data-action="filter-tipe" data-tipe="mandiri">Mandiri (1 Org)</button>
                    <button type="button" class="filter-pill-btn" data-tipe="kelompok" data-action="filter-tipe" data-tipe="kelompok">Kelompok (2–5 Org)</button>
                </div>
            </div>

            <div class="peta-alur-filter-row">
                <span style="font-weight: 600;">Rencana Masuk:</span>
                <select id="modalFilterBulan" class="filter-select-bulan" data-action="filter-bulan" aria-label="Pilih Rencana Bulan Pelaksanaan Magang">
                    <option value="jul" selected>Juli 2026</option>
                    <option value="agu">Agustus 2026</option>
                    <option value="sep">September 2026</option>
                    <option value="okt">Oktober 2026</option>
                    <option value="nov">November 2026</option>
                    <option value="des">Desember 2026</option>
                </select>
            </div>

            <div class="peta-alur-filter-row action-row-right">
                <button type="button" class="btn-sim-autoplay" id="btnSimAutoplay" data-action="toggle-autoplay" title="Jalankan tur animasi otomatis 4 tahapan alur magang">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                    <span>Tour Otomatis</span>
                </button>
            </div>
        </div>

        <!-- Body: 100% Full Panoramic Canvas & Floating Mission HUD Layout -->
        <div class="peta-alur-modal-body modal-panoramic-layout">
            <div class="modal-road-canvas-wrap modal-panoramic-wrap" id="modalRoadCanvasWrap">
                
                <!-- Floating Zoom Controls in Top Left -->
                <div class="road-zoom-controls">
                    <button type="button" class="btn-road-zoom" id="btnRoadZoomIn" data-action="zoom-in" title="Perbesar Kamera (+)" aria-label="Zoom In">+</button>
                    <button type="button" class="btn-road-zoom" id="btnRoadZoomOut" data-action="zoom-out" title="Perkecil Kamera (-)" aria-label="Zoom Out">-</button>
                    <button type="button" class="btn-road-zoom btn-road-fit" id="btnRoadZoomReset" data-action="zoom-reset" title="Pemandangan Panorama Utuh (Semua)" aria-label="Reset Zoom">Semua</button>
                </div>

                <!-- Living SVG Scene Canvas (960 x 540 Widescreen Panorama) -->
                <div class="modal-road-canvas-inner" id="modalRoadCanvasInner">
                    <svg id="modalRoadCanvas" viewBox="0 0 960 540" class="road-svg-canvas panoramic-svg-canvas">
                        <defs>
                            <!-- Sky Gradient -->
                            <linearGradient id="skyGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                                <stop offset="0%" stop-color="#060c17"/>
                                <stop offset="55%" stop-color="#0b162c"/>
                                <stop offset="100%" stop-color="#14243f"/>
                            </linearGradient>

                            <!-- Merapi Mountain Gradient -->
                            <linearGradient id="merapiGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#0c1626"/>
                                <stop offset="50%" stop-color="#112038"/>
                                <stop offset="100%" stop-color="#080e1a"/>
                            </linearGradient>

                            <!-- Active Road Path Trail Gradient -->
                            <linearGradient id="activeTrailGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#10b981"/>
                                <stop offset="100%" stop-color="#059669"/>
                            </linearGradient>

                            <!-- Headlight Beam Cone Gradient -->
                            <linearGradient id="headlightBeamGrad" x1="0%" y1="50%" x2="100%" y2="50%">
                                <stop offset="0%" stop-color="#fef08a" stop-opacity="0.9"/>
                                <stop offset="40%" stop-color="#fde047" stop-opacity="0.45"/>
                                <stop offset="100%" stop-color="#fef08a" stop-opacity="0"/>
                            </linearGradient>

                            <!-- Streetlamp Light Cone Gradient -->
                            <linearGradient id="streetLampGrad" x1="50%" y1="0%" x2="50%" y2="100%">
                                <stop offset="0%" stop-color="#fde047" stop-opacity="0.38"/>
                                <stop offset="100%" stop-color="#fde047" stop-opacity="0"/>
                            </linearGradient>

                            <!-- Pin Glow Filter -->
                            <filter id="pinGlow" x="-50%" y="-50%" width="200%" height="200%">
                                <feDropShadow dx="0" dy="4" stdDeviation="6" flood-color="#f59e0b" flood-opacity="0.85"/>
                            </filter>
                        </defs>

                        <!-- ==========================================================
                             LAYER 1: SKY, STARS, & MERAPI MOUNTAIN BACKDROP
                             ========================================================== -->
                        <rect width="960" height="540" fill="url(#skyGrad)"/>

                        <!-- Ambient Twinkling Stars -->
                        <g class="ambient-stars" opacity="0.75">
                            <circle cx="65" cy="35" r="1.4" fill="#ffffff" class="star-twinkle-1"/>
                            <circle cx="180" cy="55" r="1.8" fill="#93c5fd" class="star-twinkle-2"/>
                            <circle cx="310" cy="30" r="1.3" fill="#ffffff" class="star-twinkle-1"/>
                            <circle cx="460" cy="50" r="2.0" fill="#fef08a" class="star-twinkle-3"/>
                            <circle cx="620" cy="25" r="1.5" fill="#ffffff" class="star-twinkle-2"/>
                            <circle cx="750" cy="60" r="1.7" fill="#93c5fd" class="star-twinkle-1"/>
                            <circle cx="890" cy="40" r="1.5" fill="#fef08a" class="star-twinkle-3"/>
                        </g>

                        <!-- Majestic Gunung Merapi Silhouette -->
                        <g id="modalMerapiBackdrop" class="merapi-backdrop">
                            <path d="M 120,440 L 480,95 L 840,440 Z" fill="url(#merapiGrad)"/>
                            <path d="M 480,95 L 530,210 L 600,290 L 840,440 L 480,440 Z" fill="#070d17" opacity="0.45"/>
                            <path d="M 465,110 Q 480,80 495,110 Z" fill="#38bdf8" opacity="0.3"/>
                        </g>

                        <!-- Floating Ambient Clouds (CSS infinite drift) -->
                        <g class="floating-clouds-group">
                            <path class="floating-cloud cloud-speed-1" d="M 40,80 Q 65,60 95,75 Q 125,55 155,75 Q 180,60 195,80 Z" fill="#334d75" opacity="0.3"/>
                            <path class="floating-cloud cloud-speed-2" d="M 520,100 Q 550,80 585,95 Q 615,75 650,95 Q 675,80 695,100 Z" fill="#334d75" opacity="0.22"/>
                        </g>

                        <!-- ==========================================================
                             LAYER 2: CITYSCAPE, DISKOMINFO HUB & STREETLIGHTS
                             ========================================================== -->
                        <!-- Diskominfo Hub & Communication Towers (Finish Area) -->
                        <g id="diskominfoHubScene" transform="translate(760, 260)">
                            <!-- Office Building -->
                            <rect x="0" y="30" width="110" height="100" rx="4" fill="#0f192b" stroke="#1e293b" stroke-width="2"/>
                            <rect x="6" y="22" width="98" height="9" rx="2" fill="#1e3a8a"/>
                            <!-- Glowing Office Windows -->
                            <rect x="12" y="42" width="18" height="14" rx="2" fill="#38bdf8" opacity="0.85"/>
                            <rect x="36" y="42" width="18" height="14" rx="2" fill="#fef08a" opacity="0.75"/>
                            <rect x="60" y="42" width="18" height="14" rx="2" fill="#38bdf8" opacity="0.85"/>
                            <rect x="84" y="42" width="18" height="14" rx="2" fill="#93c5fd" opacity="0.75"/>
                            <rect x="12" y="66" width="18" height="14" rx="2" fill="#93c5fd" opacity="0.75"/>
                            <rect x="36" y="66" width="18" height="14" rx="2" fill="#38bdf8" opacity="0.85"/>
                            <rect x="60" y="66" width="18" height="14" rx="2" fill="#fef08a" opacity="0.8"/>
                            <rect x="84" y="66" width="18" height="14" rx="2" fill="#38bdf8" opacity="0.85"/>
                            <!-- Entrance Canopy -->
                            <rect x="35" y="102" width="40" height="28" fill="#0284c7" opacity="0.9"/>
                            <text x="55" y="120" text-anchor="middle" font-size="8" font-weight="800" fill="#ffffff">KOMINFO</text>
                            <!-- Radio Transmission Tower -->
                            <line x1="55" y1="22" x2="55" y2="-32" stroke="#cbd5e1" stroke-width="2.5"/>
                            <line x1="42" y1="8" x2="68" y2="8" stroke="#94a3b8" stroke-width="1.5"/>
                            <line x1="46" y1="-10" x2="64" y2="-10" stroke="#94a3b8" stroke-width="1.5"/>
                            <!-- Beacon Lights -->
                            <circle cx="55" cy="-34" r="4.5" fill="#ef4444" class="tower-beacon-pulse"/>
                            <circle cx="55" cy="-34" r="10" fill="#ef4444" opacity="0.3" class="tower-beacon-glow"/>
                        </g>

                        <!-- Warm Streetlamps -->
                        <g transform="translate(190, 80)">
                            <line x1="0" y1="0" x2="0" y2="45" stroke="#94a3b8" stroke-width="2.2"/>
                            <path d="M 0,0 Q 8,-10 16,-4" fill="none" stroke="#94a3b8" stroke-width="2.2"/>
                            <circle cx="16" cy="-4" r="3.5" fill="#fde047"/>
                            <polygon points="8,4 42,70 -12,70" fill="url(#streetLampGrad)"/>
                        </g>
                        <g transform="translate(580, 85)">
                            <line x1="0" y1="0" x2="0" y2="45" stroke="#94a3b8" stroke-width="2.2"/>
                            <path d="M 0,0 Q -8,-10 -16,-4" fill="none" stroke="#94a3b8" stroke-width="2.2"/>
                            <circle cx="-16" cy="-4" r="3.5" fill="#fde047"/>
                            <polygon points="-8,4 -42,70 12,70" fill="url(#streetLampGrad)"/>
                        </g>

                        <!-- ==========================================================
                             LAYER 3: HIGHWAY ROAD WITH ACTIVE TRAIL (WIDESCREEN S-CURVE)
                             ========================================================== -->
                        <!-- Road Curb Border -->
                        <path id="svgRoadBorder" d="M 80,140 C 260,80 400,85 530,135 C 680,195 620,320 440,345 C 270,370 180,440 320,445 C 470,450 640,430 830,400"
                              fill="none" stroke="#1f2d47" stroke-width="52" stroke-linecap="round" stroke-linejoin="round" opacity="0.7"/>

                        <!-- Asphalt Surface -->
                        <path id="svgRoadBase" d="M 80,140 C 260,80 400,85 530,135 C 680,195 620,320 440,345 C 270,370 180,440 320,445 C 470,450 640,430 830,400"
                              fill="none" stroke="#111827" stroke-width="44" stroke-linecap="round" stroke-linejoin="round"/>

                        <!-- Glowing Active Trail (Progress) -->
                        <path id="svgRoadProgress" d="M 80,140 C 260,80 400,85 530,135 C 680,195 620,320 440,345 C 270,370 180,440 320,445 C 470,450 640,430 830,400"
                              fill="none" stroke="url(#activeTrailGrad)" stroke-width="42" stroke-linecap="round" stroke-linejoin="round"
                              stroke-dasharray="1400" stroke-dashoffset="1400" opacity="0.92"/>

                        <!-- Center Dashed Markings -->
                        <path id="svgRoadDashed" d="M 80,140 C 260,80 400,85 530,135 C 680,195 620,320 440,345 C 270,370 180,440 320,445 C 470,450 640,430 830,400"
                              fill="none" stroke="#f8fafc" stroke-width="3" stroke-dasharray="10 10" opacity="0.6"/>

                        <!-- ==========================================================
                             LAYER 4: CHECKPOINT NODES (1, 2, 3, 4)
                             ========================================================== -->
                        <!-- Checkpoint 1 (Registrasi) -->
                        <g class="road-checkpoint-group cursor-pointer" id="roadCp1" data-action="select-step" data-step="1" role="button" aria-label="Pilih Tahap 1 Registrasi" style="cursor: pointer;">
                            <circle cx="0" cy="0" r="34" class="cp-pulse-halo" fill="#e11d48" opacity="0"/>
                            <circle cx="0" cy="0" r="24" class="cp-circle-bg" fill="#e11d48" stroke="#ffffff" stroke-width="3.5"/>
                            <text x="0" y="8" text-anchor="middle" font-size="20" font-weight="800" fill="#ffffff" font-family="'Lexend', sans-serif">1</text>
                            <text x="0" y="44" text-anchor="middle" font-size="13" font-weight="700" fill="#ffffff" class="cp-label-text" font-family="'Lexend', sans-serif">Registrasi</text>
                        </g>

                        <!-- Checkpoint 2 (Pengajuan) -->
                        <g class="road-checkpoint-group cursor-pointer" id="roadCp2" data-action="select-step" data-step="2" role="button" aria-label="Pilih Tahap 2 Pengajuan" style="cursor: pointer;">
                            <circle cx="0" cy="0" r="34" class="cp-pulse-halo" fill="#6366f1" opacity="0"/>
                            <circle cx="0" cy="0" r="24" class="cp-circle-bg" fill="#6366f1" stroke="#ffffff" stroke-width="3.5"/>
                            <text x="0" y="8" text-anchor="middle" font-size="20" font-weight="800" fill="#ffffff" font-family="'Lexend', sans-serif">2</text>
                            <text x="0" y="44" text-anchor="middle" font-size="13" font-weight="700" fill="#ffffff" class="cp-label-text" font-family="'Lexend', sans-serif">Pengajuan</text>
                        </g>

                        <!-- Checkpoint 3 (Pelaksanaan) -->
                        <g class="road-checkpoint-group cursor-pointer" id="roadCp3" data-action="select-step" data-step="3" role="button" aria-label="Pilih Tahap 3 Pelaksanaan" style="cursor: pointer;">
                            <circle cx="0" cy="0" r="34" class="cp-pulse-halo" fill="#0284c7" opacity="0"/>
                            <circle cx="0" cy="0" r="24" class="cp-circle-bg" fill="#0284c7" stroke="#ffffff" stroke-width="3.5"/>
                            <text x="0" y="8" text-anchor="middle" font-size="20" font-weight="800" fill="#ffffff" font-family="'Lexend', sans-serif">3</text>
                            <text x="0" y="44" text-anchor="middle" font-size="13" font-weight="700" fill="#ffffff" class="cp-label-text" font-family="'Lexend', sans-serif">Pelaksanaan</text>
                        </g>

                        <!-- Checkpoint 4 (Evaluasi) -->
                        <g class="road-checkpoint-group cursor-pointer" id="roadCp4" data-action="select-step" data-step="4" role="button" aria-label="Pilih Tahap 4 Evaluasi" style="cursor: pointer;">
                            <circle cx="0" cy="0" r="34" class="cp-pulse-halo" fill="#eab308" opacity="0"/>
                            <circle cx="0" cy="0" r="24" class="cp-circle-bg" fill="#eab308" stroke="#ffffff" stroke-width="3.5"/>
                            <text x="0" y="8" text-anchor="middle" font-size="20" font-weight="800" fill="#ffffff" font-family="'Lexend', sans-serif">4</text>
                            <text x="0" y="44" text-anchor="middle" font-size="13" font-weight="700" fill="#ffffff" class="cp-label-text" font-family="'Lexend', sans-serif">Evaluasi</text>
                        </g>

                        <!-- ==========================================================
                             LAYER 5: LIVING ANIMATED CHARACTERS (4 STAGES - PANGGUNG DEDIKASI)
                             ========================================================== -->
                        <g id="modalCharacterScene" class="living-character-showcase">
                            <!-- VIGNETTE 1: Calon Peserta Mengetik di Laptop (Tahap 1: Panggung Kiri Atas) -->
                            <g id="charScene1" class="char-scene-item" transform="translate(190, 185)">
                                <ellipse cx="30" cy="48" rx="42" ry="14" fill="#000000" opacity="0.4"/>
                                <rect x="0" y="26" width="65" height="7" rx="2.5" fill="#334155"/>
                                <line x1="8" y1="33" x2="8" y2="47" stroke="#1e293b" stroke-width="3"/>
                                <line x1="57" y1="33" x2="57" y2="47" stroke="#1e293b" stroke-width="3"/>
                                <polygon points="18,26 44,26 48,10 14,10" fill="#0284c7" opacity="0.9"/>
                                <rect x="16" y="8" width="30" height="17" rx="2" fill="#38bdf8" class="laptop-screen-pulse"/>
                                <circle cx="31" cy="-7" r="11" fill="#fcd34d"/>
                                <path d="M 20,-11 Q 31,-18 42,-11 Q 40,-4 38,-1 Q 25,-1 20,-11 Z" fill="#1e293b"/>
                                <path d="M 18,6 L 44,6 L 41,28 L 21,28 Z" fill="#e11d48"/>
                                <g class="char-typing-arm">
                                    <line x1="20" y1="13" x2="29" y2="25" stroke="#fcd34d" stroke-width="3" stroke-linecap="round"/>
                                    <line x1="42" y1="13" x2="33" y2="25" stroke="#fcd34d" stroke-width="3" stroke-linecap="round"/>
                                </g>
                                <g class="floating-account-particle">
                                    <circle cx="50" cy="1" r="8" fill="#10b981"/>
                                    <polyline points="47,1 49,3 54,-2" fill="none" stroke="#ffffff" stroke-width="2"/>
                                </g>
                                <rect x="-18" y="-33" width="98" height="20" rx="10" fill="#0f172a" stroke="#e11d48" stroke-width="1.6"/>
                                <text x="31" y="-20" text-anchor="middle" font-size="9.5" font-weight="700" fill="#ffffff">Registrasi Akun...</text>
                            </g>

                            <!-- VIGNETTE 2: Menyerahkan Berkas & Stempel Verifikasi (Tahap 2: Panggung Kanan Atas) -->
                            <g id="charScene2" class="char-scene-item" transform="translate(605, 175)" style="display: none;">
                                <ellipse cx="30" cy="48" rx="42" ry="14" fill="#000000" opacity="0.4"/>
                                <rect x="0" y="26" width="65" height="7" rx="2.5" fill="#334155"/>
                                <rect x="18" y="21" width="24" height="7" rx="1" fill="#f8fafc"/>
                                <rect x="23" y="17" width="20" height="7" rx="1" fill="#6366f1"/>
                                <circle cx="12" cy="-4" r="10" fill="#fcd34d"/>
                                <path d="M 3,-8 Q 12,-16 21,-8 Z" fill="#312e81"/>
                                <rect x="4" y="6" width="16" height="25" rx="3" fill="#6366f1"/>
                                <rect x="16" y="14" width="15" height="11" rx="1.5" fill="#3b82f6" stroke="#ffffff" stroke-width="1"/>
                                <g class="stamp-arm-anim">
                                    <line x1="44" y1="-8" x2="44" y2="9" stroke="#cbd5e1" stroke-width="3.2"/>
                                    <rect x="35" y="9" width="18" height="9" rx="2" fill="#ef4444"/>
                                    <text x="44" y="27" text-anchor="middle" font-size="7.5" font-weight="900" fill="#10b981">✓ VERIFIED</text>
                                </g>
                                <rect x="-18" y="-33" width="98" height="20" rx="10" fill="#0f172a" stroke="#6366f1" stroke-width="1.6"/>
                                <text x="31" y="-20" text-anchor="middle" font-size="9.5" font-weight="700" fill="#ffffff">Validasi Berkas!</text>
                            </g>

                            <!-- VIGNETTE 3: Peserta & Mentor ASN Sleman (Tahap 3: Panggung Kiri Bawah) -->
                            <g id="charScene3" class="char-scene-item" transform="translate(340, 365)" style="display: none;">
                                <ellipse cx="30" cy="48" rx="44" ry="14" fill="#000000" opacity="0.4"/>
                                <circle cx="14" cy="-4" r="10" fill="#fcd34d"/>
                                <rect x="6" y="7" width="16" height="28" rx="3" fill="#0284c7"/>
                                <line x1="12" y1="7" x2="14" y2="16" stroke="#fbbf24" stroke-width="1.3"/>
                                <rect x="12" y="16" width="5.5" height="8" rx="0.8" fill="#ffffff"/>
                                <g class="mentor-nod-anim">
                                    <circle cx="46" cy="-5" r="10.5" fill="#fed7aa"/>
                                    <path d="M 37,-10 Q 46,-18 55,-10 Z" fill="#1e293b"/>
                                    <rect x="37" y="6" width="18" height="30" rx="3" fill="#1b3860"/>
                                    <rect x="49" y="11" width="3.5" height="3.5" fill="#fbbf24"/>
                                    <path d="M 36,18 Q 28,15 26,12" fill="none" stroke="#fed7aa" stroke-width="2.8" stroke-linecap="round"/>
                                </g>
                                <rect x="-18" y="-33" width="98" height="20" rx="10" fill="#0f172a" stroke="#0284c7" stroke-width="1.6"/>
                                <text x="31" y="-20" text-anchor="middle" font-size="9.5" font-weight="700" fill="#ffffff">Bimbingan ASN ✓</text>
                            </g>

                            <!-- VIGNETTE 4: Selebrasi Selesai Magang & Hujan Konfeti (Tahap 4: Pelataran Kantor Diskominfo) -->
                            <g id="charScene4" class="char-scene-item" transform="translate(760, 375)" style="display: none;">
                                <ellipse cx="30" cy="48" rx="42" ry="14" fill="#000000" opacity="0.4"/>
                                <g class="char-jump-joy">
                                    <circle cx="30" cy="-11" r="11" fill="#fcd34d"/>
                                    <polygon points="12,-16 48,-16 30,-24" fill="#1e293b"/>
                                    <line x1="30" y1="-24" x2="39" y2="-13" stroke="#eab308" stroke-width="1.6"/>
                                    <rect x="21" y="2" width="18" height="28" rx="3" fill="#eab308"/>
                                    <line x1="21" y1="9" x2="13" y2="-6" stroke="#fcd34d" stroke-width="3.2" stroke-linecap="round"/>
                                    <line x1="39" y1="9" x2="47" y2="-6" stroke="#fcd34d" stroke-width="3.2" stroke-linecap="round"/>
                                    <rect x="8" y="-15" width="44" height="11" rx="2" fill="#ffffff" stroke="#eab308" stroke-width="1.6"/>
                                    <circle cx="30" cy="-9.5" r="4" fill="#ef4444"/>
                                </g>
                                <g class="confetti-particles">
                                    <rect x="4" y="-22" width="3.5" height="3.5" fill="#eab308" class="confetti-c1"/>
                                    <rect x="58" y="-16" width="4.5" height="2.5" fill="#38bdf8" class="confetti-c2"/>
                                    <rect x="14" y="-3" width="2.5" height="4.5" fill="#ef4444" class="confetti-c3"/>
                                    <rect x="50" y="6" width="3.5" height="3.5" fill="#10b981" class="confetti-c1"/>
                                    <polygon points="30,-38 32,-33 38,-33 34,-29 36,-24 30,-27 24,-24 26,-29 22,-33 28,-33" fill="#fde047" class="confetti-c2"/>
                                </g>
                                <rect x="-18" y="-33" width="98" height="20" rx="10" fill="#0f172a" stroke="#eab308" stroke-width="1.6"/>
                                <text x="31" y="-20" text-anchor="middle" font-size="9.5" font-weight="700" fill="#ffffff">Magang Selesai! 🎓</text>
                            </g>
                        </g>

                        <!-- ==========================================================
                             LAYER 6: FLOATING GPS PIN & CINEMATIC MINI CAR
                             ========================================================== -->
                        <!-- Floating Bouncing GPS Waypoint Pin Marker -->
                        <g id="modalRoadGpsPin" transform="translate(0, 0)" style="filter: url(#pinGlow); pointer-events: none; transition: transform 0.45s cubic-bezier(0.16, 1, 0.3, 1);">
                            <path d="M 0,-48 C -12,-48 -22,-38 -22,-26 C -22,-12 0,0 0,0 C 0,0 22,-12 22,-26 C 22,-38 12,-48 0,-48 Z"
                                  fill="#f59e0b" stroke="#ffffff" stroke-width="3.8" stroke-linejoin="round"/>
                            <circle cx="0" cy="-26" r="7.5" fill="#0f172a"/>
                        </g>

                        <!-- Animated Cinematic Mini Car with Glowing Headlight Rays -->
                        <g id="modalRoadCarGroup" transform="translate(0, 0) rotate(0)" style="filter: drop-shadow(0 6px 12px rgba(0,0,0,0.65)); pointer-events: none;" class="cinematic-car-body">
                            <!-- Powerful Front Headlight Beams illuminating Road -->
                            <polygon class="car-headlight-beam" points="16,-7 105,-28 105,28 16,7" fill="url(#headlightBeamGrad)"/>

                            <!-- Car Shadow -->
                            <ellipse cx="0" cy="0" rx="20" ry="12" fill="#000000" opacity="0.45"/>
                            <!-- Sleman Government Service SUV Body -->
                            <rect x="-18" y="-11" width="36" height="22" rx="6" fill="#1b3860" stroke="#3b82f6" stroke-width="1.5"/>
                            <!-- White Roof Shell -->
                            <rect x="-9" y="-8.5" width="20" height="17" rx="4" fill="#f8fafc"/>
                            <!-- Front Windshield Tint -->
                            <rect x="4" y="-7.5" width="6" height="15" rx="1.8" fill="#38bdf8" opacity="0.85"/>
                            <!-- Rear Window Tint -->
                            <rect x="-8" y="-7.5" width="4" height="15" rx="1.2" fill="#38bdf8" opacity="0.6"/>
                            <!-- Rotating Amber Siren Beacon on Roof -->
                            <circle cx="-0.5" cy="0" r="3.5" fill="#f59e0b" class="siren-strobe-beacon"/>
                            <!-- Front Halogen Headlight Bulbs -->
                            <circle cx="17.5" cy="-7" r="2.2" fill="#ffffff"/>
                            <circle cx="17.5" cy="7" r="2.2" fill="#ffffff"/>
                            <!-- Rear Red LED Taillights -->
                            <rect x="-18.5" y="-8.5" width="2.2" height="4" fill="#ef4444"/>
                            <rect x="-18.5" y="4.5" width="2.2" height="4" fill="#ef4444"/>
                        </g>
                    </svg>
                </div>

                <!-- ==========================================================
                     FLOATING MISSION HUD OVERLAY (CARD KACA MELAYANG DI DALAM DUNIA ANIMASI)
                     ========================================================== -->
                <div class="mission-hud-overlay" id="missionHudOverlay">
                    <div class="mission-hud-card" id="modalStepDetailPanel" role="region" aria-label="Informasi Misi Tahap Aktif">
                        <!-- Konten dinamis dirender via renderStepDetail() -->
                    </div>
                </div>

            </div>

            <!-- Horizontal Step Tab Bar at Bottom of Panoramic Viewport -->
            <div class="modal-road-tab-bar" role="tablist" aria-label="Bilah Tahapan Magang">
                <!-- Tab 1 -->
                <button type="button" class="road-tab-btn is-active" role="tab" id="roadTab1" aria-selected="true" data-step="1" data-action="select-step" data-step="1">
                    <div class="road-tab-top">
                        <span class="road-tab-num num-1">1</span>
                        <span class="road-tab-title">Registrasi</span>
                    </div>
                    <div class="road-tab-score" id="roadTabScore1">0/4 langkah</div>
                    <div class="road-tab-line line-1"></div>
                </button>

                <!-- Tab 2 -->
                <button type="button" class="road-tab-btn" role="tab" id="roadTab2" aria-selected="false" data-step="2" data-action="select-step" data-step="2">
                    <div class="road-tab-top">
                        <span class="road-tab-num num-2">2</span>
                        <span class="road-tab-title">Pengajuan</span>
                    </div>
                    <div class="road-tab-score" id="roadTabScore2">0/5 langkah</div>
                    <div class="road-tab-line line-2"></div>
                </button>

                <!-- Tab 3 -->
                <button type="button" class="road-tab-btn" role="tab" id="roadTab3" aria-selected="false" data-step="3" data-action="select-step" data-step="3">
                    <div class="road-tab-top">
                        <span class="road-tab-num num-3">3</span>
                        <span class="road-tab-title">Pelaksanaan</span>
                    </div>
                    <div class="road-tab-score" id="roadTabScore3">0/4 langkah</div>
                    <div class="road-tab-line line-3"></div>
                </button>

                <!-- Tab 4 -->
                <button type="button" class="road-tab-btn" role="tab" id="roadTab4" aria-selected="false" data-step="4" data-action="select-step" data-step="4">
                    <div class="road-tab-top">
                        <span class="road-tab-num num-4">4</span>
                        <span class="road-tab-title">Evaluasi</span>
                    </div>
                    <div class="road-tab-score" id="roadTabScore4">0/4 langkah</div>
                    <div class="road-tab-line line-4"></div>
                </button>
            </div>
        </div>

        <!-- Footer Modal -->
        <div class="peta-alur-modal-footer">
            <div class="modal-footer-hint">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                <span>Klik checkpoint di lanskap atau centang checklist di dalam kartu misi melayang untuk simulasi interaktif.</span>
            </div>
            <div class="modal-footer-buttons">
                <button type="button" class="btn-modal-secondary" data-action="close-modal">Tutup</button>
                <a href="{{ route('register') }}" class="btn-modal-primary">
                    <span>Mulai Daftar Sekarang</span>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
            </div>
        </div>

    </div>
</div>

{{-- Catatan: Logika interaksi peta alur telah dimigrasikan ke resources/js/pages/public/peta-alur.js dan halaman mandiri /peta-alur --}}
