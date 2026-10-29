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
                <button type="button" class="btn-modal-audio-toggle" id="btnModalAudioToggle" onclick="toggleModalAudio()" title="Aktifkan / Nonaktifkan Efek Suara & Musik Ambient" aria-label="Toggle Audio">
                    <svg id="audioIconOn" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="display: none;"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"/></svg>
                    <svg id="audioIconOff" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><line x1="23" y1="9" x2="17" y2="15"/><line x1="17" y1="9" x2="23" y2="15"/></svg>
                    <span id="audioToggleLabel">Audio FX: Off</span>
                </button>

                <div class="peta-alur-sim-mode-tag" title="Simulasi panorama sinematik aktif">
                    <span class="sim-pulse-dot"></span>
                    <span>Panorama 360°</span>
                </div>

                <button type="button" class="btn-modal-close" onclick="closePetaAlurModal()" aria-label="Tutup Dialog Peta Alur">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
        </div>

        <!-- Filter Quick Strip & Timeline Selector -->
        <div class="peta-alur-modal-toolbar">
            <div class="peta-alur-filter-row">
                <span style="font-weight: 600;">Kategori Jenjang:</span>
                <div class="filter-pill-group" role="radiogroup" aria-label="Kategori Jenjang">
                    <button type="button" class="filter-pill-btn is-active" data-jenjang="semua" onclick="setModalFilterJenjang('semua')">Semua Jenjang</button>
                    <button type="button" class="filter-pill-btn" data-jenjang="smk" onclick="setModalFilterJenjang('smk')">Siswa SMK (PKL)</button>
                    <button type="button" class="filter-pill-btn" data-jenjang="mahasiswa" onclick="setModalFilterJenjang('mahasiswa')">Mahasiswa (D3/S1)</button>
                </div>
            </div>

            <div class="peta-alur-filter-row">
                <span style="font-weight: 600;">Tipe:</span>
                <div class="filter-pill-group" role="radiogroup" aria-label="Tipe Permohonan">
                    <button type="button" class="filter-pill-btn is-active" data-tipe="mandiri" onclick="setModalFilterTipe('mandiri')">Mandiri (1 Org)</button>
                    <button type="button" class="filter-pill-btn" data-tipe="kelompok" onclick="setModalFilterTipe('kelompok')">Kelompok (2–5 Org)</button>
                </div>
            </div>

            <div class="peta-alur-filter-row">
                <span style="font-weight: 600;">Rencana Masuk:</span>
                <select id="modalFilterBulan" class="filter-select-bulan" onchange="setModalFilterBulan(this.value)" aria-label="Pilih Rencana Bulan Pelaksanaan Magang">
                    <option value="jul" selected>Juli 2026</option>
                    <option value="agu">Agustus 2026</option>
                    <option value="sep">September 2026</option>
                    <option value="okt">Oktober 2026</option>
                    <option value="nov">November 2026</option>
                    <option value="des">Desember 2026</option>
                </select>
            </div>

            <div class="peta-alur-filter-row action-row-right">
                <button type="button" class="btn-sim-autoplay" id="btnSimAutoplay" onclick="toggleSimAutoplay()" title="Jalankan tur animasi otomatis 4 tahapan alur magang">
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
                    <button type="button" class="btn-road-zoom" id="btnRoadZoomIn" onclick="modalRoadZoomIn()" title="Perbesar Kamera (+)" aria-label="Zoom In">+</button>
                    <button type="button" class="btn-road-zoom" id="btnRoadZoomOut" onclick="modalRoadZoomOut()" title="Perkecil Kamera (-)" aria-label="Zoom Out">-</button>
                    <button type="button" class="btn-road-zoom btn-road-fit" id="btnRoadZoomReset" onclick="modalRoadZoomReset()" title="Pemandangan Panorama Utuh (Semua)" aria-label="Reset Zoom">Semua</button>
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
                        <g class="road-checkpoint-group cursor-pointer" id="roadCp1" onclick="selectModalStep(1)" role="button" aria-label="Pilih Tahap 1 Registrasi" style="cursor: pointer;">
                            <circle cx="0" cy="0" r="34" class="cp-pulse-halo" fill="#e11d48" opacity="0"/>
                            <circle cx="0" cy="0" r="24" class="cp-circle-bg" fill="#e11d48" stroke="#ffffff" stroke-width="3.5"/>
                            <text x="0" y="8" text-anchor="middle" font-size="20" font-weight="800" fill="#ffffff" font-family="'Lexend', sans-serif">1</text>
                            <text x="0" y="44" text-anchor="middle" font-size="13" font-weight="700" fill="#ffffff" class="cp-label-text" font-family="'Lexend', sans-serif">Registrasi</text>
                        </g>

                        <!-- Checkpoint 2 (Pengajuan) -->
                        <g class="road-checkpoint-group cursor-pointer" id="roadCp2" onclick="selectModalStep(2)" role="button" aria-label="Pilih Tahap 2 Pengajuan" style="cursor: pointer;">
                            <circle cx="0" cy="0" r="34" class="cp-pulse-halo" fill="#6366f1" opacity="0"/>
                            <circle cx="0" cy="0" r="24" class="cp-circle-bg" fill="#6366f1" stroke="#ffffff" stroke-width="3.5"/>
                            <text x="0" y="8" text-anchor="middle" font-size="20" font-weight="800" fill="#ffffff" font-family="'Lexend', sans-serif">2</text>
                            <text x="0" y="44" text-anchor="middle" font-size="13" font-weight="700" fill="#ffffff" class="cp-label-text" font-family="'Lexend', sans-serif">Pengajuan</text>
                        </g>

                        <!-- Checkpoint 3 (Pelaksanaan) -->
                        <g class="road-checkpoint-group cursor-pointer" id="roadCp3" onclick="selectModalStep(3)" role="button" aria-label="Pilih Tahap 3 Pelaksanaan" style="cursor: pointer;">
                            <circle cx="0" cy="0" r="34" class="cp-pulse-halo" fill="#0284c7" opacity="0"/>
                            <circle cx="0" cy="0" r="24" class="cp-circle-bg" fill="#0284c7" stroke="#ffffff" stroke-width="3.5"/>
                            <text x="0" y="8" text-anchor="middle" font-size="20" font-weight="800" fill="#ffffff" font-family="'Lexend', sans-serif">3</text>
                            <text x="0" y="44" text-anchor="middle" font-size="13" font-weight="700" fill="#ffffff" class="cp-label-text" font-family="'Lexend', sans-serif">Pelaksanaan</text>
                        </g>

                        <!-- Checkpoint 4 (Evaluasi) -->
                        <g class="road-checkpoint-group cursor-pointer" id="roadCp4" onclick="selectModalStep(4)" role="button" aria-label="Pilih Tahap 4 Evaluasi" style="cursor: pointer;">
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

                            <!-- VIGNETTE 4: Selebrasi E-Sertifikat & Hujan Konfeti (Tahap 4: Pelataran Kantor Diskominfo) -->
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
                                <text x="31" y="-20" text-anchor="middle" font-size="9.5" font-weight="700" fill="#ffffff">E-Sertifikat Terbit! 🎓</text>
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
                <button type="button" class="road-tab-btn is-active" role="tab" id="roadTab1" aria-selected="true" data-step="1" onclick="selectModalStep(1)">
                    <div class="road-tab-top">
                        <span class="road-tab-num num-1">1</span>
                        <span class="road-tab-title">Registrasi</span>
                    </div>
                    <div class="road-tab-score" id="roadTabScore1">0/4 langkah</div>
                    <div class="road-tab-line line-1"></div>
                </button>

                <!-- Tab 2 -->
                <button type="button" class="road-tab-btn" role="tab" id="roadTab2" aria-selected="false" data-step="2" onclick="selectModalStep(2)">
                    <div class="road-tab-top">
                        <span class="road-tab-num num-2">2</span>
                        <span class="road-tab-title">Pengajuan</span>
                    </div>
                    <div class="road-tab-score" id="roadTabScore2">0/5 langkah</div>
                    <div class="road-tab-line line-2"></div>
                </button>

                <!-- Tab 3 -->
                <button type="button" class="road-tab-btn" role="tab" id="roadTab3" aria-selected="false" data-step="3" onclick="selectModalStep(3)">
                    <div class="road-tab-top">
                        <span class="road-tab-num num-3">3</span>
                        <span class="road-tab-title">Pelaksanaan</span>
                    </div>
                    <div class="road-tab-score" id="roadTabScore3">0/4 langkah</div>
                    <div class="road-tab-line line-3"></div>
                </button>

                <!-- Tab 4 -->
                <button type="button" class="road-tab-btn" role="tab" id="roadTab4" aria-selected="false" data-step="4" onclick="selectModalStep(4)">
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
                <button type="button" class="btn-modal-secondary" onclick="closePetaAlurModal()">Tutup</button>
                <a href="{{ route('register') }}" class="btn-modal-primary">
                    <span>Mulai Daftar Sekarang</span>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
            </div>
        </div>

    </div>
</div>

<script>
(function() {
    // =========================================================================
    // 1. DATASET 4 TAHAPAN MAGANG DISKOMINFO SLEMAN
    // =========================================================================
    const stepData = {
        1: {
            title: "Tahap 1: Registrasi & Pembuatan Akun",
            badge: "Langkah Awal",
            duration: "1–2 Hari Kerja",
            timelineKey: "tahap1",
            desc: "Calon peserta magang mendaftarkan diri di portal MAGKOM menggunakan email aktif. Setelah verifikasi akun berhasil, peserta melengkapi biodata diri, kontak darurat, serta data institusi pendidikan (sekolah atau universitas).",
            requirements: [
                "Mengisi identitas lengkap (NIK, NIM/NISN, No. WhatsApp aktif)",
                "Mengunggah pas foto formal berlatar belakang polos",
                "Memverifikasi tautan aktivasi akun yang dikirimkan ke email terdaftar",
                "Mengisi data kontak darurat (orang tua / wali / instansi)"
            ],
            docs: ["KTP / Kartu Pelajar", "Pas Foto Formal (JPG/PNG)", "Email Aktif Institusi/Pribadi"],
            outcome: "Akun diverifikasi dan peserta siap mengajukan kuota formasi."
        },
        2: {
            title: "Tahap 2: Pengajuan Bidang & Seleksi Berkas",
            badge: "Verifikasi & Seleksi",
            duration: "3–7 Hari Kerja",
            timelineKey: "tahap2",
            desc: "Peserta memilih bidang penempatan yang diminati (misal: Aplikasi & Informatika, Infrastruktur Jaringan, Informasi Publik, atau Statistik). Admin Diskominfo memeriksa validitas berkas dan kesesuaian formasi kuota yang tersedia.",
            requirements: [
                "Memilih bulan pelaksanaan magang yang kuotanya masih terbuka",
                "Mengunggah Surat Permohonan / Pengantar Resmi dari Sekolah/Kampus",
                "Mengunggah Proposal Magang & Curriculum Vitae (CV) terbaru",
                "Melampirkan Transkrip Nilai atau Rapor semester terakhir (Format PDF)",
                "Untuk jalur Kelompok: Ketua kelompok menambahkan anggota dan mengunggah surat gabungan"
            ],
            docs: ["Surat Pengantar Institusi (PDF)", "Proposal Kegiatan Magang", "Curriculum Vitae (CV)", "Transkrip / Rapor"],
            outcome: "Pengumuman status: Diterima (Penempatan terbit), Perbaikan Berkas (revisi 3 hari), atau Waiting List."
        },
        3: {
            title: "Tahap 3: Pelaksanaan Magang & Logbook Mingguan",
            badge: "Aktivitas Inti",
            duration: "1–6 Bulan (Sesuai SK)",
            timelineKey: "tahap3",
            desc: "Peserta yang diterima menjalani orientasi kerja di kantor Diskominfo Sleman. Selama masa penempatan, peserta dipandu oleh Mentor/Pembimbing Lapangan, mengerjakan tugas terstruktur di platform, dan rutin mengisi laporan logbook mingguan.",
            requirements: [
                "Menghadiri briefing orientasi perdana dan mematuhi tata tertib jam kerja ASN Diskominfo",
                "Mengerjakan penugasan proyek dan modul yang diberikan oleh Pembimbing",
                "Mengisi logbook kegiatan harian dan merangkumnya setiap akhir pekan",
                "Mendapatkan persetujuan (review) mingguan dari Pembimbing Lapangan"
            ],
            docs: ["Kartu Tanda Peserta Magang", "Laporan Logbook Mingguan", "Dokumentasi Foto Kegiatan"],
            outcome: "Aktivitas tercatat rapi dan seluruh capaian kompetensi tervalidasi oleh mentor."
        },
        4: {
            title: "Tahap 4: Evaluasi Akhir & Penerbitan E-Sertifikat",
            badge: "Kelulusan & Sertifikasi",
            duration: "1–3 Hari Setelah Selesai",
            timelineKey: "tahap4",
            desc: "Di penghujung masa magang, Pembimbing memberikan evaluasi penilaian menyeluruh (aspek disiplin, ketrampilan teknis, kerjasama tim, dan inisiatif). Peserta mengisi kuesioner survei kepuasan dan dapat langsung mengunduh Sertifikat Resmi bertanda tangan digital.",
            requirements: [
                "Menyelesaikan seluruh kewajiban penugasan dan laporan akhir magang",
                "Mengisi survei kepuasan layanan magang Diskominfo Sleman",
                "Pembimbing menginput lembar nilai akhir (Nilai Angka & Predikat Memuaskan/Sangat Baik)",
                "Mengunduh E-Sertifikat Resmi ber-QR Code validasi dari portal peserta"
            ],
            docs: ["Laporan Akhir Magang (PDF)", "Lembar Penilaian Pembimbing", "E-Sertifikat Resmi Diskominfo"],
            outcome: "Penerbitan Lembar Nilai Resmi dan Sertifikat Digital yang dapat diverifikasi keasliannya."
        }
    };

    // Kamus Tips & Format Dokumen Resmi (Document Inspector)
    const docTips = {
        "KTP / Kartu Pelajar": {
            format: "JPG, PNG, atau PDF (Maks. 2 MB)",
            note: "Scan asli jelas, tidak buram, seluruh teks NIK/NISN terbaca tanpa terpotong.",
            rule: "Wajib bagi semua pendaftar untuk validasi identitas kependudukan Sleman / Nasional."
        },
        "Pas Foto Formal (JPG/PNG)": {
            format: "JPG atau PNG (Maks. 1 MB, Min. 400x600 px)",
            note: "Foto setengah badan resmi dengan latar belakang polos (merah atau biru), berpakaian sopan berkerah atau almamater.",
            rule: "Akan dicetak pada Kartu Tanda Peserta Magang resmi."
        },
        "Email Aktif Institusi/Pribadi": {
            format: "Alamat email aktif (Disarankan email universitas / sekolah / Gmail)",
            note: "Pastikan inbox dapat menerima email verifikasi aktivasi dan notifikasi penempatan.",
            rule: "Tautan aktivasi kedaluwarsa dalam 24 jam."
        },
        "Surat Pengantar Institusi (PDF)": {
            format: "PDF (Maks. 2 MB)",
            note: "Wajib bertanda tangan Kepala Sekolah / Dekan / Kaprodi, berstempel resmi atau memiliki Barcode TTE instansi.",
            rule: "Harus mencantumkan nama calon peserta, jurusan, dan perkiraan tanggal magang."
        },
        "Proposal Kegiatan Magang": {
            format: "PDF (Maks. 5 MB)",
            note: "Memuat latar belakang, tujuan kegiatan, kompetensi yang ingin dicapai, dan jadwal pelaksanaan.",
            rule: "Khusus pendaftaran kelompok, cukup 1 proposal yang memuat seluruh nama anggota."
        },
        "Curriculum Vitae (CV)": {
            format: "PDF (Maks. 2 MB)",
            note: "CV profesional yang mencantumkan riwayat pendidikan, keahlian teknis (tools/programming), dan tautan portofolio/GitHub jika ada.",
            rule: "Membantu admin menyelaraskan ke bidang penempatan yang paling relevan."
        },
        "Transkrip / Rapor": {
            format: "PDF (Maks. 3 MB)",
            note: "Transkrip nilai semester terakhir (Mahasiswa) atau nilai rapor semester 1-4 yang dilegalisasi (SMK).",
            rule: "Bahan pertimbangan kualifikasi akademik bidang."
        },
        "Kartu Tanda Peserta Magang": {
            format: "E-Card Otomatis ber-QR Code dari Portal",
            note: "Wajib diunduh dan dicetak/disimpan di smartphone saat hadir pertama kali di kantor Kominfo.",
            rule: "Digunakan untuk presensi dan pengenal resmi selama bertugas."
        },
        "Laporan Logbook Mingguan": {
            format: "Input Form Digital di Portal MagKom",
            note: "Diisi setiap hari kerja dan dikirim untuk persetujuan mentor setiap hari Jumat sore.",
            rule: "Syarat mutlak untuk kelulusan dan pembukaan akses lembar penilaian."
        },
        "Dokumentasi Foto Kegiatan": {
            format: "JPG atau PNG pendukung pada logbook mingguan",
            note: "Foto aktivitas riil penugasan di lingkungan kerja Diskominfo Sleman.",
            rule: "Sebagai bukti otentik portofolio hasil magang."
        },
        "Laporan Akhir Magang (PDF)": {
            format: "PDF (Maks. 10 MB)",
            note: "Laporan komprehensif karya atau proyek yang dihasilkan selama penempatan di Diskominfo.",
            rule: "Wajib disetujui Pembimbing Lapangan sebelum unduh sertifikat."
        },
        "Lembar Penilaian Pembimbing": {
            format: "Digital Scorecard diisi Mentor",
            note: "Aspek penilaian meliputi Disiplin, Inisiatif, Kerjasama, Integritas, dan Penguasaan Teknis.",
            rule: "Predikat minimal Cukup untuk berhak memperoleh Sertifikat Resmi."
        },
        "E-Sertifikat Resmi Diskominfo": {
            format: "PDF Resmi bertanda tangan elektronik (TTE) & QR Code",
            note: "Dapat diverifikasi keasliannya di menu Verifikasi Dokumen portal MagKom.",
            rule: "Diterbitkan gratis secara digital tanpa dipungut biaya apa pun."
        }
    };

    // Database Jadwal Estimasi (Bulan Mulai -> Milestone)
    const timelineEstimates = {
        jul: { tahap1: "15 Mei – 31 Mei 2026", tahap2: "1 Juni – 15 Juni 2026", tahap3: "1 Juli – 30 September 2026", tahap4: "1 Oktober – 5 Oktober 2026" },
        agu: { tahap1: "15 Juni – 30 Juni 2026", tahap2: "1 Juli – 15 Juli 2026", tahap3: "1 Agustus – 31 Oktober 2026", tahap4: "1 November – 5 November 2026" },
        sep: { tahap1: "15 Juli – 31 Juli 2026", tahap2: "1 Agustus – 15 Agustus 2026", tahap3: "1 September – 30 November 2026", tahap4: "1 Desember – 5 Desember 2026" },
        okt: { tahap1: "15 Agustus – 31 Agustus 2026", tahap2: "1 September – 15 September 2026", tahap3: "1 Oktober – 31 Desember 2026", tahap4: "2 Januari – 6 Januari 2027" },
        nov: { tahap1: "15 September – 30 September 2026", tahap2: "1 Oktober – 15 Oktober 2026", tahap3: "1 November 2026 – 31 Januari 2027", tahap4: "1 Februari – 5 Februari 2027" },
        des: { tahap1: "15 Oktober – 31 Oktober 2026", tahap2: "1 November – 15 November 2026", tahap3: "1 Desember 2026 – 28 Februari 2027", tahap4: "1 Maret – 5 Maret 2027" }
    };

    // =========================================================================
    // 2. WEB AUDIO SYNTHESIZER (ZERO-DEPENDENCY SOUND FX & MUSIC AMBIENCE)
    // =========================================================================
    let audioCtx = null;
    let isAudioEnabled = false;
    let ambientOsc1 = null;
    let ambientOsc2 = null;
    let ambientGain = null;

    function initAudioContext() {
        if (!audioCtx) {
            const AudioContextClass = window.AudioContext || window.webkitAudioContext;
            if (AudioContextClass) {
                audioCtx = new AudioContextClass();
            }
        }
        if (audioCtx && audioCtx.state === 'suspended') {
            audioCtx.resume();
        }
    }

    const MagkomSound = {
        playClick: function() {
            if (!isAudioEnabled || !audioCtx) return;
            try {
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(600, audioCtx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(300, audioCtx.currentTime + 0.05);
                gain.gain.setValueAtTime(0.12, audioCtx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.05);
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                osc.start();
                osc.stop(audioCtx.currentTime + 0.06);
            } catch(e) {}
        },
        playDing: function() {
            if (!isAudioEnabled || !audioCtx) return;
            try {
                // Two-tone chime (C6 -> G6)
                const now = audioCtx.currentTime;
                const osc1 = audioCtx.createOscillator();
                const gain1 = audioCtx.createGain();
                osc1.type = 'triangle';
                osc1.frequency.setValueAtTime(1046.5, now); // C6
                gain1.gain.setValueAtTime(0.2, now);
                gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.28);
                osc1.connect(gain1);
                gain1.connect(audioCtx.destination);
                osc1.start(now);
                osc1.stop(now + 0.3);

                const osc2 = audioCtx.createOscillator();
                const gain2 = audioCtx.createGain();
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(1567.98, now + 0.09); // G6
                gain2.gain.setValueAtTime(0.25, now + 0.09);
                gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.45);
                osc2.connect(gain2);
                gain2.connect(audioCtx.destination);
                osc2.start(now + 0.09);
                osc2.stop(now + 0.46);
            } catch(e) {}
        },
        playWhoosh: function() {
            if (!isAudioEnabled || !audioCtx) return;
            try {
                const now = audioCtx.currentTime;
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(120, now);
                osc.frequency.exponentialRampToValueAtTime(260, now + 0.25);
                osc.frequency.exponentialRampToValueAtTime(80, now + 0.5);
                gain.gain.setValueAtTime(0.08, now);
                gain.gain.exponentialRampToValueAtTime(0.001, now + 0.5);
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                osc.start(now);
                osc.stop(now + 0.52);
            } catch(e) {}
        },
        playFanfare: function() {
            if (!isAudioEnabled || !audioCtx) return;
            try {
                const notes = [523.25, 659.25, 783.99, 1046.50]; // C5, E5, G5, C6
                notes.forEach((freq, i) => {
                    const start = audioCtx.currentTime + (i * 0.12);
                    const osc = audioCtx.createOscillator();
                    const gain = audioCtx.createGain();
                    osc.type = 'triangle';
                    osc.frequency.setValueAtTime(freq, start);
                    gain.gain.setValueAtTime(0.18, start);
                    gain.gain.exponentialRampToValueAtTime(0.001, start + 0.4);
                    osc.connect(gain);
                    gain.connect(audioCtx.destination);
                    osc.start(start);
                    osc.stop(start + 0.42);
                });
            } catch(e) {}
        },
        startAmbient: function() {
            if (!isAudioEnabled || !audioCtx) return;
            try {
                if (ambientOsc1) return;
                ambientGain = audioCtx.createGain();
                ambientGain.gain.setValueAtTime(0.04, audioCtx.currentTime);

                ambientOsc1 = audioCtx.createOscillator();
                ambientOsc1.type = 'sine';
                ambientOsc1.frequency.setValueAtTime(146.83, audioCtx.currentTime); // D3

                ambientOsc2 = audioCtx.createOscillator();
                ambientOsc2.type = 'triangle';
                ambientOsc2.frequency.setValueAtTime(220, audioCtx.currentTime); // A3

                ambientOsc1.connect(ambientGain);
                ambientOsc2.connect(ambientGain);
                ambientGain.connect(audioCtx.destination);

                ambientOsc1.start();
                ambientOsc2.start();
            } catch(e) {}
        },
        stopAmbient: function() {
            try {
                if (ambientOsc1) {
                    ambientOsc1.stop();
                    ambientOsc1.disconnect();
                    ambientOsc1 = null;
                }
                if (ambientOsc2) {
                    ambientOsc2.stop();
                    ambientOsc2.disconnect();
                    ambientOsc2 = null;
                }
            } catch(e) {}
        }
    };

    window.toggleModalAudio = function() {
        initAudioContext();
        isAudioEnabled = !isAudioEnabled;
        const btn = document.getElementById('btnModalAudioToggle');
        const iconOn = document.getElementById('audioIconOn');
        const iconOff = document.getElementById('audioIconOff');
        const label = document.getElementById('audioToggleLabel');

        if (isAudioEnabled) {
            if (iconOn) iconOn.style.display = 'inline-block';
            if (iconOff) iconOff.style.display = 'none';
            if (label) label.textContent = 'Audio FX: On ♫';
            if (btn) btn.classList.add('is-active');
            MagkomSound.playDing();
            MagkomSound.startAmbient();
        } else {
            if (iconOn) iconOn.style.display = 'none';
            if (iconOff) iconOff.style.display = 'inline-block';
            if (label) label.textContent = 'Audio FX: Off';
            if (btn) btn.classList.remove('is-active');
            MagkomSound.stopAmbient();
        }
    };

    // =========================================================================
    // 3. STATE APLIKASI & GEOMETRI JALAN RAYA PANORAMA
    // =========================================================================
    let currentStep = 1;
    let currentJenjang = 'semua';
    let currentTipe = 'mandiri';
    let currentBulan = 'jul';
    let activeDocInspection = null;
    let isHudMinimized = false;
    let isAutoplayRunning = false;
    let autoplayTimer = null;

    // Camera ViewBox State (960 x 540 default)
    const baseViewBox = { x: 0, y: 0, w: 960, h: 540 };
    let curCam = { x: 0, y: 0, w: 960, h: 540 };
    let camAnimFrame = null;

    // Checkbox Checklist State
    const checklistState = {
        1: {},
        2: {},
        3: {},
        4: {}
    };

    // Checkpoints ratio di sepanjang aspal kurva 960x540
    let roadTotalLength = 1400;
    const cpRatios = [0.08, 0.36, 0.65, 0.94];
    const cpPositions = [];
    let currentCarLength = 0;
    let carAnimFrame = null;

    // Framing kamera per tahapan (fokus dinamis & auto-zoom)
    const stepCameraFrames = {
        1: { x: 40, y: 30, w: 720, h: 405 },    // Zoom ke pos 1
        2: { x: 300, y: 30, w: 640, h: 360 },   // Zoom ke pos 2
        3: { x: 120, y: 160, w: 700, h: 394 },  // Zoom ke pos 3
        4: { x: 320, y: 150, w: 640, h: 360 }   // Zoom ke pos 4 & finish hub
    };

    function initRoadGeometry() {
        const roadPath = document.getElementById('svgRoadBase');
        if (!roadPath) return;

        try {
            roadTotalLength = roadPath.getTotalLength();
        } catch (e) {
            roadTotalLength = 1400;
        }

        // Posisi (x, y) setiap checkpoint
        cpRatios.forEach((ratio, idx) => {
            const len = ratio * roadTotalLength;
            try {
                const pt = roadPath.getPointAtLength(len);
                cpPositions[idx] = { x: pt.x, y: pt.y, len: len };

                const cpGroup = document.getElementById('roadCp' + (idx + 1));
                if (cpGroup) {
                    cpGroup.setAttribute('transform', `translate(${pt.x}, ${pt.y})`);
                }
            } catch (err) {}
        });

        // Set awal posisi mobil & GPS pin
        currentCarLength = cpRatios[currentStep - 1] * roadTotalLength;
        updateCarAndPinVisual(currentCarLength, currentStep);
        updateCharacterScene(currentStep);
    }

    // Kamera Dinamis (Auto-Zoom & Pan ke Target ViewBox)
    function animateCameraTo(target, duration = 600) {
        if (camAnimFrame) cancelAnimationFrame(camAnimFrame);
        const svg = document.getElementById('modalRoadCanvas');
        if (!svg) return;

        const start = { ...curCam };
        const startTime = performance.now();

        function stepCam(now) {
            const elapsed = now - startTime;
            const p = Math.min(1, elapsed / duration);
            // Ease out cubic
            const ease = 1 - Math.pow(1 - p, 3);

            curCam.x = start.x + (target.x - start.x) * ease;
            curCam.y = start.y + (target.y - start.y) * ease;
            curCam.w = start.w + (target.w - start.w) * ease;
            curCam.h = start.h + (target.h - start.h) * ease;

            svg.setAttribute('viewBox', `${curCam.x.toFixed(1)} ${curCam.y.toFixed(1)} ${curCam.w.toFixed(1)} ${curCam.h.toFixed(1)}`);

            if (p < 1) {
                camAnimFrame = requestAnimationFrame(stepCam);
            }
        }

        camAnimFrame = requestAnimationFrame(stepCam);
    }

    // Alihkan Vignette Karakter Bergerak
    function updateCharacterScene(stepNum) {
        [1, 2, 3, 4].forEach(s => {
            const charScene = document.getElementById('charScene' + s);
            if (charScene) {
                charScene.style.display = (s === stepNum) ? 'block' : 'none';
            }
        });
    }

    // Animasi Mobil Sinematik dengan Lampu Sorot
    function animateCarToStep(targetStep) {
        const roadPath = document.getElementById('svgRoadBase');
        const carGroup = document.getElementById('modalRoadCarGroup');
        const progressPath = document.getElementById('svgRoadProgress');
        const gpsPin = document.getElementById('modalRoadGpsPin');
        if (!roadPath || !carGroup) return;

        const targetLength = cpRatios[targetStep - 1] * roadTotalLength;
        const startLength = currentCarLength;
        const dist = targetLength - startLength;
        const startTime = performance.now();
        const duration = Math.min(850, Math.max(450, Math.abs(dist) * 0.7));

        MagkomSound.playWhoosh();

        if (carAnimFrame) cancelAnimationFrame(carAnimFrame);

        function stepAnim(now) {
            const elapsed = now - startTime;
            const progress = Math.min(1, elapsed / duration);
            // Ease in-out cubic
            const ease = progress < 0.5 ? 4 * progress * progress * progress : 1 - Math.pow(-2 * progress + 2, 3) / 2;
            const curLen = startLength + (dist * ease);
            currentCarLength = curLen;

            try {
                const pt = roadPath.getPointAtLength(curLen);
                const ptAhead = roadPath.getPointAtLength(Math.min(roadTotalLength, curLen + 4));
                const ptBehind = roadPath.getPointAtLength(Math.max(0, curLen - 4));
                const angle = (Math.atan2(ptAhead.y - ptBehind.y, ptAhead.x - ptBehind.x) * 180) / Math.PI;

                carGroup.setAttribute('transform', `translate(${pt.x}, ${pt.y}) rotate(${angle})`);

                if (progressPath) {
                    progressPath.style.strokeDasharray = roadTotalLength;
                    progressPath.style.strokeDashoffset = roadTotalLength - curLen;
                }
            } catch (e) {}

            if (progress < 1) {
                carAnimFrame = requestAnimationFrame(stepAnim);
            } else {
                updateCarAndPinVisual(targetLength, targetStep);
                if (targetStep === 4) {
                    MagkomSound.playFanfare();
                }
            }
        }

        carAnimFrame = requestAnimationFrame(stepAnim);
        updateCharacterScene(targetStep);

        // Auto Camera Framing Zoom
        const targetFrame = stepCameraFrames[targetStep] || baseViewBox;
        animateCameraTo(targetFrame, duration + 100);
    }

    function updateCarAndPinVisual(targetLen, stepNum) {
        const roadPath = document.getElementById('svgRoadBase');
        const carGroup = document.getElementById('modalRoadCarGroup');
        const gpsPin = document.getElementById('modalRoadGpsPin');
        const progressPath = document.getElementById('svgRoadProgress');
        if (!roadPath) return;

        try {
            const pt = roadPath.getPointAtLength(targetLen);
            const ptAhead = roadPath.getPointAtLength(Math.min(roadTotalLength, targetLen + 4));
            const ptBehind = roadPath.getPointAtLength(Math.max(0, targetLen - 4));
            const angle = (Math.atan2(ptAhead.y - ptBehind.y, ptAhead.x - ptBehind.x) * 180) / Math.PI;

            if (carGroup) carGroup.setAttribute('transform', `translate(${pt.x}, ${pt.y}) rotate(${angle})`);
            if (progressPath) {
                progressPath.style.strokeDasharray = roadTotalLength;
                progressPath.style.strokeDashoffset = roadTotalLength - targetLen;
            }
            if (gpsPin) {
                gpsPin.setAttribute('transform', `translate(${pt.x}, ${pt.y})`);
            }
        } catch (e) {}

        // Update halo checkpoint aktif
        [1, 2, 3, 4].forEach(s => {
            const cpGroup = document.getElementById('roadCp' + s);
            if (cpGroup) {
                const halo = cpGroup.querySelector('.cp-pulse-halo');
                if (halo) halo.setAttribute('opacity', s === stepNum ? '0.35' : '0');
            }
        });
    }

    // Zoom Controls Functionality
    window.modalRoadZoomIn = function() {
        MagkomSound.playClick();
        const nw = Math.max(480, curCam.w * 0.85);
        const nh = Math.max(270, curCam.h * 0.85);
        const nx = curCam.x + (curCam.w - nw) / 2;
        const ny = curCam.y + (curCam.h - nh) / 2;
        animateCameraTo({ x: nx, y: ny, w: nw, h: nh }, 350);
    };

    window.modalRoadZoomOut = function() {
        MagkomSound.playClick();
        const nw = Math.min(1080, curCam.w * 1.15);
        const nh = Math.min(608, curCam.h * 1.15);
        const nx = Math.max(0, curCam.x - (nw - curCam.w) / 2);
        const ny = Math.max(0, curCam.y - (nh - curCam.h) / 2);
        animateCameraTo({ x: nx, y: ny, w: nw, h: nh }, 350);
    };

    window.modalRoadZoomReset = function() {
        MagkomSound.playClick();
        animateCameraTo(baseViewBox, 500);
    };

    // =========================================================================
    // 4. FLOATING MISSION HUD & CHECKLIST LOGIC
    // =========================================================================
    function getStepChecklistProgress(stepNum) {
        const step = stepData[stepNum];
        if (!step) return { checked: 0, total: 0, percent: 0 };
        const total = step.requirements.length;
        let checked = 0;
        for (let i = 0; i < total; i++) {
            if (checklistState[stepNum][i]) checked++;
        }
        const percent = total > 0 ? Math.round((checked / total) * 100) : 0;
        return { checked, total, percent };
    }

    function updateChecklistUI() {
        [1, 2, 3, 4].forEach(s => {
            const p = getStepChecklistProgress(s);
            const scoreEl = document.getElementById('roadTabScore' + s);
            if (scoreEl) scoreEl.textContent = `${p.checked}/${p.total} langkah`;
        });
    }

    window.toggleHudMinimize = function() {
        isHudMinimized = !isHudMinimized;
        const panel = document.getElementById('missionHudOverlay');
        if (panel) {
            panel.classList.toggle('is-minimized', isHudMinimized);
        }
        renderStepDetail(currentStep);
    };

    // Render Floating Mission HUD Panel
    function renderStepDetail(stepNum) {
        const data = stepData[stepNum] || stepData[1];
        const panel = document.getElementById('modalStepDetailPanel');
        if (!panel) return;

        const timelineObj = timelineEstimates[currentBulan] || timelineEstimates['jul'];
        const currentTimelineEst = timelineObj[data.timelineKey] || data.duration;
        const stepProg = getStepChecklistProgress(stepNum);

        // Jika HUD diminimize, tampilkan bentuk pill mengambang yang ringkas
        if (isHudMinimized) {
            panel.innerHTML = `
                <div class="hud-minimized-pill" onclick="toggleHudMinimize()" role="button" aria-label="Buka kembali kartu misi">
                    <span class="hud-pill-badge">${data.badge}</span>
                    <span class="hud-pill-title">${data.title}</span>
                    <span class="hud-pill-prog">${stepProg.checked}/${stepProg.total} Siap (${stepProg.percent}%)</span>
                    <button type="button" class="btn-hud-expand" aria-label="Expand Kartu Misi">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 3 21 3 21 9"/><polyline points="9 21 3 21 3 15"/><line x1="21" y1="3" x2="14" y2="10"/><line x1="3" y1="21" x2="10" y2="14"/></svg>
                    </button>
                </div>
            `;
            updateChecklistUI();
            return;
        }

        let jenjangNotice = '';
        if (currentJenjang === 'smk') {
            jenjangNotice = '<div class="sim-notice-card smk"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg><div><strong>Khusus SMK:</strong> Surat pengantar wajib ditandatangani Kepala Sekolah / Waka Hubin &amp; menyertakan izin orang tua.</div></div>';
        } else if (currentJenjang === 'mahasiswa') {
            jenjangNotice = '<div class="sim-notice-card mhs"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg><div><strong>Khusus Mahasiswa:</strong> Wajib melampirkan Surat Pengantar Dekan/Kaprodi dan CV portofolio akademik.</div></div>';
        }

        let tipeNotice = '';
        if (currentTipe === 'kelompok' && stepNum === 2) {
            tipeNotice = '<div class="sim-notice-card kelompok"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg><div><strong>Jalur Kelompok:</strong> Cukup Ketua yang mengajukan dengan memasukkan ID anggota (maks. 5 orang).</div></div>';
        }

        const reqItemsHtml = data.requirements.map((item, idx) => {
            const isChecked = Boolean(checklistState[stepNum][idx]);
            return `
                <li class="interactive-checklist-item ${isChecked ? 'is-checked' : ''}" onclick="toggleRequirement(${stepNum}, ${idx})">
                    <input type="checkbox" id="chk_${stepNum}_${idx}" class="interactive-chk-input" ${isChecked ? 'checked' : ''} aria-label="${item}" onclick="event.stopPropagation(); toggleRequirement(${stepNum}, ${idx});">
                    <span class="interactive-chk-box">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                    </span>
                    <label for="chk_${stepNum}_${idx}" class="interactive-chk-label">${item}</label>
                </li>
            `;
        }).join('');

        const docsBadgesHtml = data.docs.map(doc => {
            const isInspected = activeDocInspection === doc;
            return `
                <button type="button" class="detail-doc-badge ${isInspected ? 'is-active-doc' : ''}" onclick="inspectDocument('${doc.replace(/'/g, "\\'")}')" title="Klik untuk cek format resmi">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    <span>${doc}</span>
                </button>
            `;
        }).join('');

        let docInspectionCard = '';
        if (activeDocInspection && docTips[activeDocInspection]) {
            const tip = docTips[activeDocInspection];
            docInspectionCard = `
                <div class="doc-inspection-popover">
                    <div class="doc-inspection-header">
                        <div class="doc-inspection-title-wrap">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            <strong>Ketentuan: ${activeDocInspection}</strong>
                        </div>
                        <button type="button" class="btn-close-doc-inspect" onclick="inspectDocument(null)" aria-label="Tutup Pratinjau Dokumen">&times;</button>
                    </div>
                    <div class="doc-inspection-body">
                        <div class="doc-inspection-row">
                            <span class="doc-inspect-tag">Format</span>
                            <span>${tip.format}</span>
                        </div>
                        <div class="doc-inspection-row">
                            <span class="doc-inspect-tag">Catatan</span>
                            <span>${tip.note}</span>
                        </div>
                        <div class="doc-inspection-row">
                            <span class="doc-inspect-tag">Ketentuan</span>
                            <span style="color: #38bdf8;">${tip.rule}</span>
                        </div>
                    </div>
                </div>
            `;
        }

        panel.innerHTML = `
            <div class="mission-hud-top-bar">
                <div class="hud-top-badges">
                    <span class="detail-step-chip">${data.badge}</span>
                    <span class="detail-timeline-chip">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        ${currentTimelineEst}
                    </span>
                </div>
                <button type="button" class="btn-hud-minimize" onclick="toggleHudMinimize()" title="Perkecil kartu misi untuk melihat pemandangan utuh" aria-label="Minimize Kartu Misi">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </button>
            </div>

            <h4 class="hud-step-title">${data.title}</h4>
            <div class="hud-desc-box">${data.desc}</div>

            ${jenjangNotice}
            ${tipeNotice}

            <!-- Checklist & Energy Progress -->
            <div class="hud-checklist-header">
                <div class="hud-checklist-label">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                    Uji Kesiapan &amp; Ketentuan Wajib
                </div>
                <div class="step-readiness-pill ${stepProg.percent === 100 ? 'is-complete' : ''}">
                    ${stepProg.checked}/${stepProg.total} Siap (${stepProg.percent}%)
                </div>
            </div>

            <div class="hud-energy-bar-wrap">
                <div class="hud-energy-bar-fill" style="width: ${stepProg.percent}%;"></div>
            </div>

            <ul class="interactive-checklist-container hud-checklist-list">${reqItemsHtml}</ul>

            <!-- Document Inspection Badges -->
            <div class="hud-docs-row">
                <div class="hud-docs-title">Dokumen Pendukung:</div>
                <div class="detail-docs-badge-strip">${docsBadgesHtml}</div>
            </div>

            ${docInspectionCard}

            <!-- Step Navigation Footer Controls -->
            <div class="step-nav-bar hud-nav-bar">
                <button type="button" class="btn-step-nav btn-step-prev" onclick="navigateModalStep(-1)" ${stepNum === 1 ? 'disabled style="opacity: 0.35; cursor: not-allowed;"' : ''}>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="15 18 9 12 15 6"/></svg>
                    <span>Sebelumnya</span>
                </button>

                <div class="step-nav-counter">Tahap ${stepNum} dari 4</div>

                ${stepNum < 4 ? `
                    <button type="button" class="btn-step-nav btn-step-next" onclick="navigateModalStep(1)">
                        <span>Lanjut ke Tahap ${stepNum + 1}</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                ` : `
                    <a href="{{ route('register') }}" class="btn-step-nav btn-step-finish">
                        <span>Mulai Pendaftaran 🚀</span>
                    </a>
                `}
            </div>
        `;

        updateChecklistUI();
    }

    // =========================================================================
    // 5. PUBLIC WINDOW HANDLERS
    // =========================================================================
    window.selectModalStep = function(stepNum) {
        currentStep = stepNum;

        document.querySelectorAll('.road-tab-btn').forEach(el => {
            const isMatch = parseInt(el.getAttribute('data-step')) === stepNum;
            el.classList.toggle('is-active', isMatch);
            el.setAttribute('aria-selected', isMatch ? 'true' : 'false');
        });

        animateCarToStep(stepNum);
        activeDocInspection = null;
        renderStepDetail(stepNum);
    };

    window.navigateModalStep = function(direction) {
        const nextStep = currentStep + direction;
        if (nextStep >= 1 && nextStep <= 4) {
            window.selectModalStep(nextStep);
        }
    };

    window.toggleRequirement = function(stepNum, idx) {
        checklistState[stepNum][idx] = !checklistState[stepNum][idx];
        if (checklistState[stepNum][idx]) {
            MagkomSound.playDing();
        } else {
            MagkomSound.playClick();
        }
        renderStepDetail(stepNum);
    };

    window.inspectDocument = function(docName) {
        MagkomSound.playClick();
        activeDocInspection = activeDocInspection === docName ? null : docName;
        renderStepDetail(currentStep);
    };

    window.setModalFilterJenjang = function(jenjang) {
        MagkomSound.playClick();
        currentJenjang = jenjang;
        document.querySelectorAll('.filter-pill-btn[data-jenjang]').forEach(el => {
            el.classList.toggle('is-active', el.getAttribute('data-jenjang') === jenjang);
        });
        renderStepDetail(currentStep);
    };

    window.setModalFilterTipe = function(tipe) {
        MagkomSound.playClick();
        currentTipe = tipe;
        document.querySelectorAll('.filter-pill-btn[data-tipe]').forEach(el => {
            el.classList.toggle('is-active', el.getAttribute('data-tipe') === tipe);
        });
        renderStepDetail(currentStep);
    };

    window.setModalFilterBulan = function(bulan) {
        MagkomSound.playClick();
        currentBulan = bulan;
        renderStepDetail(currentStep);
    };

    // Autoplay Simulation Mode (Tour Otomatis)
    window.toggleSimAutoplay = function() {
        const btn = document.getElementById('btnSimAutoplay');
        if (isAutoplayRunning) {
            isAutoplayRunning = false;
            if (autoplayTimer) clearInterval(autoplayTimer);
            if (btn) btn.classList.remove('is-active');
            return;
        }

        isAutoplayRunning = true;
        if (btn) btn.classList.add('is-active');
        MagkomSound.playClick();

        let s = currentStep;
        autoplayTimer = setInterval(() => {
            s = (s % 4) + 1;
            window.selectModalStep(s);
            if (s === 4) {
                setTimeout(() => {
                    if (isAutoplayRunning) {
                        window.toggleSimAutoplay();
                    }
                }, 4000);
            }
        }, 3600);
    };

    window.openPetaAlurModal = function(e) {
        if (e && e.preventDefault) e.preventDefault();
        const modal = document.getElementById('petaAlurModal');
        if (!modal) return;
        
        modal.style.display = 'flex';
        void modal.offsetWidth; // Force reflow
        modal.classList.add('is-active');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        setTimeout(() => {
            initRoadGeometry();
            renderStepDetail(currentStep);
        }, 50);

        const closeBtn = modal.querySelector('.btn-modal-close');
        if (closeBtn) closeBtn.focus();
    };

    window.closePetaAlurModal = function() {
        const modal = document.getElementById('petaAlurModal');
        if (!modal) return;
        
        if (isAutoplayRunning) window.toggleSimAutoplay();
        MagkomSound.stopAmbient();

        modal.classList.remove('is-active');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        
        setTimeout(() => {
            modal.style.display = 'none';
        }, 280);
    };

    document.addEventListener('click', function(e) {
        const modal = document.getElementById('petaAlurModal');
        if (modal && e.target === modal) {
            window.closePetaAlurModal();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('petaAlurModal');
            if (modal && modal.classList.contains('is-active')) {
                window.closePetaAlurModal();
            }
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        initRoadGeometry();
        renderStepDetail(1);
    });
})();
</script>
