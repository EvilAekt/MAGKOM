@php
    $tahapanHeading = $content['heading'] ?? \App\Support\Pengaturan::get('landing_tahapan_heading', 'Bingung cara mendaftarnya?, berikut adalah Tahapan Pendaftaran Magang');
    $tahapanSummary = $content['summary'] ?? \App\Support\Pengaturan::get('landing_tahapan_summary', "Pertama calon peserta membuat akun, mengisi data pribadi, dan menunggu verifikasi akun oleh admin.\nKedua calon peserta memilih bidang dan mengunggah berkas, lalu admin melakukan verifikasi dan seleksi. Hasilnya diumumkan di portal MAGKOM.\nKetiga peserta melaksanakan magang, mengerjakan tugas, dan mengisi logbook sesuai durasi yang ditentukan.\nKeempat setelah magang selesai, peserta menyelesaikan program magang, mengunggah laporan akhir, dan dinyatakan lulus secara resmi.");
    $tahapanSummaryLines = array_values(array_filter(array_map('trim', explode("\n", str_replace("\r", "", (string) $tahapanSummary)))));

    $defaultTahapanSteps = [
        ['step' => '01', 'title' => 'Registrasi', 'badge' => 'REGISTRASI', 'desc' => 'Calon peserta membuat akun, mengisi data pribadi, dan menunggu akun diverifikasi admin.'],
        ['step' => '02', 'title' => 'Administrasi & Seleksi', 'badge' => 'ADMINISTRASI & SELEKSI', 'desc' => 'Pilih bidang, unggah berkas, lalu admin memverifikasi dan menyeleksi dalam 1–3 hari kerja.'],
        ['step' => '03', 'title' => 'Pelaksanaan Magang', 'badge' => 'PELAKSANAAN MAGANG', 'desc' => 'Kerjakan tugas, isi logbook harian, dan unggah progres sesuai durasi magang.'],
        ['step' => '04', 'title' => 'Selesai Magang', 'badge' => 'KELULUSAN', 'desc' => 'Penyelesaian program magang, pengumpulan laporan akhir, dan penetapan status kelulusan resmi.'],
    ];
    $tahapanSteps = !empty($content['steps']) && is_array($content['steps']) 
        ? $content['steps'] 
        : \App\Support\Pengaturan::json('landing_tahapan_steps', $defaultTahapanSteps);
    if (empty($tahapanSteps)) {
        $tahapanSteps = $defaultTahapanSteps;
    }

    $tahapanBtnPdf = $content['btn_pdf_text'] ?? \App\Support\Pengaturan::get('landing_tahapan_btn_pdf', 'Unduh Panduan PDF');
    $tahapanBtnPeta = $content['btn_peta_text'] ?? \App\Support\Pengaturan::get('landing_tahapan_btn_peta', 'Peta Alur Interaktif');
@endphp

<section id="tahapan" class="landing-section" data-wp-block="tahapan" data-wp-block-name="Alur & Tahapan" data-wp-block-id="{{ $blockId ?? 'block-tahapan-1' }}" style="order: {{ $order ?? 3 }}; padding-top: 2rem; padding-bottom: 5rem; {{ !($visible ?? true) && !($isEditorMode ?? false) ? 'display: none !important;' : '' }}">
    <div class="tahapan-layout-grid">
        <!-- Sisi Kiri (Sticky Info) -->
        <div class="tahapan-sticky-left">
            <span class="step-color-bar"></span>
            <h2 class="tahapan-main-heading">
                {!! str_contains((string) $tahapanHeading, 'Tahapan Pendaftaran Magang') ? str_replace('Tahapan Pendaftaran Magang', '<strong>Tahapan Pendaftaran Magang</strong>', e($tahapanHeading)) : e($tahapanHeading) !!}
            </h2>
            <div class="tahapan-summary-list">
                @foreach($tahapanSummaryLines as $sLine)
                    @php
                        $parts = explode(' ', $sLine, 2);
                        $firstWord = $parts[0] ?? '';
                        $restWord = $parts[1] ?? '';
                    @endphp
                    <span class="tahapan-summary-item"><strong>{{ $firstWord }}</strong> {{ $restWord }}</span>
                @endforeach
            </div>
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; margin-top: 1.25rem;">
                <a href="{{ route('panduan.pdf') }}" target="_blank" rel="noopener noreferrer" class="btn-tahapan-download" title="Unduh Petunjuk Teknis & Panduan Resmi Magang Diskominfo Sleman (PDF)">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 0.4rem; vertical-align: middle;">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    {{ $tahapanBtnPdf }}
                </a>
                <a href="{{ route('peta-alur') }}" class="btn-tahapan-download" style="background: rgba(7, 81, 255, 0.08); color: var(--primary-accent); border: 1.5px solid rgba(7, 81, 255, 0.25); cursor: pointer; text-decoration: none;" title="Buka Simulasi Peta Alur Interaktif Layar Penuh">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 0.4rem; vertical-align: middle;">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polygon points="12 8 8 12 12 16 12 8"></polygon>
                    </svg>
                    {{ $tahapanBtnPeta }}
                </a>
            </div>
        </div>

        <!-- Sisi Kanan (TahapanRoad Interactive SVG Engine) -->
        <span id="peta-alur" class="sr-only" aria-hidden="true"></span>
        <div id="tahapanRoadWrapper" class="tahapan-road-wrapper">
            <!-- SVG Road Canvas (H = 210, total = 840, T = 80, B = 80) -->
            <svg width="170" height="1000" viewBox="0 -80 170 1000" class="tahapan-svg-canvas">
                <defs>
                    <linearGradient id="roadGrad" gradientUnits="userSpaceOnUse" x1="0" y1="0" x2="0" y2="840">
                        <stop offset="0.0" stop-color="#e11d48"></stop>
                        <stop offset="0.25" stop-color="#e11d48"></stop>
                        <stop offset="0.25" stop-color="#6366f1"></stop>
                        <stop offset="0.5" stop-color="#6366f1"></stop>
                        <stop offset="0.5" stop-color="#0284c7"></stop>
                        <stop offset="0.75" stop-color="#0284c7"></stop>
                        <stop offset="0.75" stop-color="#eab308"></stop>
                        <stop offset="1.0" stop-color="#eab308"></stop>
                    </linearGradient>
                </defs>

                <!-- Base Asphalt Stroke -->
                <path id="roadSvgPath" d="M40 -80 L40 0 L40 40 C40 75 120 60 120 105 S40 135 40 170 L40 210 L40 250 C40 285 120 270 120 315 S40 345 40 380 L40 420 L40 460 C40 495 120 480 120 525 S40 555 40 590 L40 630 L40 670 C40 705 120 690 120 735 S40 765 40 800 L40 840 L40 920" 
                      fill="none" stroke="var(--road)" stroke-width="34" stroke-linecap="round"></path>

                <!-- Road Active Color Gradient Glow Stroke -->
                <path id="roadGradProgressPath" d="M40 -80 L40 0 L40 40 C40 75 120 60 120 105 S40 135 40 170 L40 210 L40 250 C40 285 120 270 120 315 S40 345 40 380 L40 420 L40 460 C40 495 120 480 120 525 S40 555 40 590 L40 630 L40 670 C40 705 120 690 120 735 S40 765 40 800 L40 840 L40 920" 
                      fill="none" stroke="url(#roadGrad)" stroke-width="34" stroke-linecap="round" stroke-dasharray="1000" stroke-dashoffset="1000" opacity="0.4"></path>

                <!-- Center Dashed Markings -->
                <path d="M40 -80 L40 0 L40 40 C40 75 120 60 120 105 S40 135 40 170 L40 210 L40 250 C40 285 120 270 120 315 S40 345 40 380 L40 420 L40 460 C40 495 120 480 120 525 S40 555 40 590 L40 630 L40 670 C40 705 120 690 120 735 S40 765 40 800 L40 840 L40 920" 
                      fill="none" stroke="var(--road-line)" stroke-width="2.5" stroke-dasharray="10 9"></path>

                <!-- START Indicator -->
                <g aria-label="Start Node">
                    <circle cx="40" cy="-72" r="22" fill="var(--success)" opacity="0.25"></circle>
                    <line x1="78" y1="-76" x2="78" y2="-36" stroke="var(--foreground)" stroke-width="2.5"></line>
                    <!-- Flag icon -->
                    <path d="M78 -76 L96 -66 L78 -56 Z" fill="var(--success)" stroke="var(--success)" stroke-width="1.5"></path>
                    <text x="106" y="-58" font-family="var(--font-display)" font-size="14" font-weight="700" fill="var(--success)">START</text>
                </g>

                <!-- Checkpoint 1 (Registrasi) -->
                <g data-checkpoint-index="0" class="cursor-pointer" style="cursor: pointer;" role="button" aria-label="Tahap 1 {{ $tahapanSteps[0]['title'] ?? 'Registrasi' }}">
                    <circle cx="120" cy="105" r="34" fill="#e11d48" opacity="0" class="checkpoint-halo" style="transition: all 0.4s ease;"></circle>
                    <circle cx="120" cy="105" r="26" fill="var(--background)" stroke="#e11d48" stroke-width="3" class="checkpoint-dot" data-step-color="#e11d48" style="transition: all 0.4s ease;"></circle>
                    <!-- UserPlus Icon -->
                    <g transform="translate(108, 93)" stroke="#e11d48" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="8.5" cy="7" r="4"></circle>
                        <line x1="20" y1="8" x2="20" y2="14"></line>
                        <line x1="23" y1="11" x2="17" y2="11"></line>
                    </g>
                </g>

                <!-- Checkpoint 2 (Administrasi & Seleksi) -->
                <g data-checkpoint-index="1" class="cursor-pointer" style="cursor: pointer;" role="button" aria-label="Tahap 2 {{ $tahapanSteps[1]['title'] ?? 'Administrasi & Seleksi' }}">
                    <circle cx="120" cy="315" r="34" fill="#6366f1" opacity="0" class="checkpoint-halo" style="transition: all 0.4s ease;"></circle>
                    <circle cx="120" cy="315" r="26" fill="var(--background)" stroke="#6366f1" stroke-width="3" class="checkpoint-dot" data-step-color="#6366f1" style="transition: all 0.4s ease;"></circle>
                    <!-- FileCheck2 Icon -->
                    <g transform="translate(108, 303)" stroke="#6366f1" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 22h14a2 2 0 0 0 2-2V7.5L14.5 2H6a2 2 0 0 0-2 2v4"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <path d="m3 15 2 2 4-4"></path>
                    </g>
                </g>

                <!-- Checkpoint 3 (Pelaksanaan Magang) -->
                <g data-checkpoint-index="2" class="cursor-pointer" style="cursor: pointer;" role="button" aria-label="Tahap 3 {{ $tahapanSteps[2]['title'] ?? 'Pelaksanaan Magang' }}">
                    <circle cx="120" cy="525" r="34" fill="#0284c7" opacity="0" class="checkpoint-halo" style="transition: all 0.4s ease;"></circle>
                    <circle cx="120" cy="525" r="26" fill="var(--background)" stroke="#0284c7" stroke-width="3" class="checkpoint-dot" data-step-color="#0284c7" style="transition: all 0.4s ease;"></circle>
                    <!-- Briefcase Icon -->
                    <g transform="translate(108, 513)" stroke="#0284c7" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="20" height="14" x="2" y="7" rx="2" ry="2"></rect>
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                    </g>
                </g>

                <!-- Checkpoint 4 (Kelulusan) -->
                <g data-checkpoint-index="3" class="cursor-pointer" style="cursor: pointer;" role="button" aria-label="Tahap 4 {{ $tahapanSteps[3]['title'] ?? 'Kelulusan' }}">
                    <circle cx="120" cy="735" r="34" fill="#eab308" opacity="0" class="checkpoint-halo" style="transition: all 0.4s ease;"></circle>
                    <circle cx="120" cy="735" r="26" fill="var(--background)" stroke="#eab308" stroke-width="3" class="checkpoint-dot" data-step-color="#eab308" style="transition: all 0.4s ease;"></circle>
                    <!-- GraduationCap Icon -->
                    <g transform="translate(108, 723)" stroke="#eab308" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                        <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                    </g>
                </g>

                <!-- FINISH Checkered Flag Line -->
                <g aria-label="Finish Node">
                    <line x1="78" y1="870" x2="78" y2="916" stroke="var(--foreground)" stroke-width="2.5"></line>
                    <!-- Checkered grid 4x3 -->
                    <rect x="79" y="870" width="7" height="7" fill="var(--foreground)"></rect>
                    <rect x="86" y="870" width="7" height="7" fill="var(--background)" stroke="var(--foreground)" stroke-width="0.5"></rect>
                    <rect x="93" y="870" width="7" height="7" fill="var(--foreground)"></rect>
                    <rect x="100" y="870" width="7" height="7" fill="var(--background)" stroke="var(--foreground)" stroke-width="0.5"></rect>

                    <rect x="79" y="877" width="7" height="7" fill="var(--background)" stroke="var(--foreground)" stroke-width="0.5"></rect>
                    <rect x="86" y="877" width="7" height="7" fill="var(--foreground)"></rect>
                    <rect x="93" y="877" width="7" height="7" fill="var(--background)" stroke="var(--foreground)" stroke-width="0.5"></rect>
                    <rect x="100" y="877" width="7" height="7" fill="var(--foreground)"></rect>

                    <rect x="79" y="884" width="7" height="7" fill="var(--foreground)"></rect>
                    <rect x="86" y="884" width="7" height="7" fill="var(--background)" stroke="var(--foreground)" stroke-width="0.5"></rect>
                    <rect x="93" y="884" width="7" height="7" fill="var(--foreground)"></rect>
                    <rect x="100" y="884" width="7" height="7" fill="var(--background)" stroke="var(--foreground)" stroke-width="0.5"></rect>

                    <text x="114" y="890" font-family="var(--font-display)" font-size="14" font-weight="700" fill="var(--foreground)">FINISH</text>
                </g>

                <!-- Animated Mini Car on Track -->
                <g id="roadCarGroup" transform="translate(40, -80) rotate(90)" style="filter: drop-shadow(0 3px 4px rgba(0, 0, 0, 0.4));">
                    <rect x="-15" y="-9" width="30" height="18" rx="6" fill="#1b3860"></rect>
                    <rect x="3" y="-7" width="7" height="14" rx="2" fill="#fbbf24"></rect>
                    <rect x="-11" y="-7" width="5" height="14" rx="2" fill="#fbbf24" opacity="0.65"></rect>
                    <rect x="12" y="-8" width="3" height="4" rx="1" fill="#ffffff"></rect>
                    <rect x="12" y="4" width="3" height="4" rx="1" fill="#ffffff"></rect>
                </g>
            </svg>

            <!-- Milestone Steps Cards (4 Steps aligned with checkpoints) -->
            <ol class="tahapan-cards-list">
                @foreach($tahapanSteps as $idx => $sCard)
                    <li class="tahapan-card-item">
                        <button type="button" class="tahapan-milestone-card inactive" data-milestone-index="{{ $idx }}">
                            <span class="card-triangle-pin" style="background-color: var(--step-{{ $idx + 1 }});"></span>
                            <p class="tahapan-number-lg" style="color: var(--step-{{ $idx + 1 }});">{{ $sCard['step'] ?? sprintf('%02d', $idx + 1) }}</p>
                            <h3 class="tahapan-card-title">{{ $sCard['title'] ?? '' }}</h3>
                            <p class="tahapan-card-desc">{{ $sCard['desc'] ?? '' }}</p>
                        </button>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>
</section>

