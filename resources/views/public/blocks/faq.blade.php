@php
    $faqHeading = $content['heading'] ?? \App\Support\Pengaturan::get('landing_faq_heading', 'Bingung? Berikut Beberapa Pertanyaan Yang Sering Ditanyakan');

    $defaultFaqData = [
        [
            'id' => 'syarat',
            'nama' => 'Syarat dan Tata Cara Mendaftar',
            'items' => [
                [
                    'q' => 'Bagaimana alur dan tata cara mendaftar magang di Diskominfo Sleman?',
                    'a' => 'Pendaftaran dilakukan secara mandiri atau kelompok melalui portal MAGKOM: (1) Buat akun portal, (2) Pilih formasi bidang dan bulan pelaksanaan yang masih tersedia kuotanya, (3) Unggah berkas persyaratan (Surat Pengantar Institusi, CV, Proposal, dan Transkrip/Rapor), (4) Tunggu verifikasi admin.'
                ],
                [
                    'q' => 'Apakah siswa SMK dan mahasiswa perguruan tinggi memiliki syarat yang berbeda?',
                    'a' => 'Secara umum sama, namun untuk siswa SMK surat permohonan ditandatangani oleh Kepala Sekolah / Waka Hubin dan dilengkapi izin orang tua, sedangkan mahasiswa melampirkan surat pengantar resmi dari Fakultas/Jurusan.'
                ],
                [
                    'q' => 'Apakah bisa mendaftar lebih dari satu bidang atau bulan magang?',
                    'a' => 'Tidak. Setiap peserta hanya dapat memilih satu bidang formasi dalam satu periode magang agar alokasi kuota adil bagi seluruh pendaftar.'
                ]
            ]
        ],
        [
            'id' => 'akun',
            'nama' => 'Akun',
            'items' => [
                [
                    'q' => 'Bagaimana jika tidak menerima email verifikasi akun?',
                    'a' => 'Silakan periksa folder Spam atau Promosi pada email Anda. Jika dalam 15 menit belum masuk, Anda dapat meminta kirim ulang link verifikasi melalui halaman login.'
                ],
                [
                    'q' => 'Apakah akun portal bisa digunakan untuk mendaftar periode berikutnya?',
                    'a' => 'Akun Anda berlaku permanen di portal MAGKOM. Anda dapat menggunakannya kembali untuk melihat riwayat sertifikat atau mendaftar periode berikutnya setelah magang sebelumnya selesai.'
                ]
            ]
        ],
        [
            'id' => 'kuis',
            'nama' => 'Kuis',
            'items' => [
                [
                    'q' => 'Apakah ada kuis atau tes kompetensi saat proses seleksi?',
                    'a' => 'Beberapa bidang teknis seperti Aplikasi Informatika (Aptika) dan Persandian dapat menyertakan tes kompetensi dasar atau kuis teknis singkat untuk memastikan kecocokan dengan proyek yang akan dikerjakan.'
                ],
                [
                    'q' => 'Berapa lama proses verifikasi berkas oleh admin?',
                    'a' => 'Proses verifikasi dan kurasi berkas administrasi membutuhkan waktu 1 hingga 3 hari kerja setelah pengajuan dikirimkan.'
                ]
            ]
        ],
        [
            'id' => 'benefit',
            'nama' => 'Benefit Magang',
            'items' => [
                [
                    'q' => 'Apa saja fasilitas dan benefit yang didapatkan peserta magang?',
                    'a' => 'Peserta mendapatkan bimbingan intensif dari Mentor ASN profesional, akses modul proyek nyata di pemerintahan, sertifikat resmi Diskominfo ber-QR code sah, serta penilaian kompetensi standar industri.'
                ],
                [
                    'q' => 'Apakah program magang di Diskominfo Sleman berbayar atau digaji?',
                    'a' => 'Program magang Diskominfo Sleman 100% GRATIS (tidak dipungut biaya apa pun) dan bersifat non-komersial tanpa kompensasi uang saku (unpaid government internship).'
                ]
            ]
        ],
        [
            'id' => 'kewajiban',
            'nama' => 'Kewajiban Peserta Magang',
            'items' => [
                [
                    'q' => 'Apa saja kewajiban dari peserta magang?',
                    'a' => 'Peserta wajib: (1) Mematuhi jam kerja ASN Diskominfo (Senin–Kamis 07.30–15.30 WIB, Jumat 07.30–14.30 WIB), (2) Mengenakan seragam sekolah/almamater rapi dan bersepatu, (3) Mengerjakan penugasan proyek tepat waktu, (4) Mengisi logbook mingguan secara disiplin, dan (5) Menjaga etika serta kerahasiaan data kedinasan.'
                ],
                [
                    'q' => 'Apakah magang bisa dilaksanakan secara daring / WFH?',
                    'a' => 'Tidak. Seluruh pelaksanaan magang di Diskominfo Sleman dilaksanakan secara luring (Work From Office) di lingkungan kantor Pemkab Sleman untuk memastikan pembimbingan maksimal.'
                ]
            ]
        ]
    ];

    $faqCategoriesData = !empty($content['items']) && is_array($content['items']) 
        ? $content['items'] 
        : \App\Support\Pengaturan::json('landing_faq_data', $defaultFaqData);
    if (empty($faqCategoriesData)) {
        $faqCategoriesData = $defaultFaqData;
    }
@endphp

<section id="faq" class="faq-section-bg" data-wp-block="faq" data-wp-block-name="FAQ Accordion" data-wp-block-id="{{ $blockId ?? 'block-faq-1' }}" aria-labelledby="faqHeading" style="order: {{ $order ?? 4 }}; {{ !($visible ?? true) ? (!empty($isEditorMode) ? 'opacity: 0.35;' : 'display: none !important;') : '' }}">
    <div class="faq-redesign-container">
        
        <!-- Kolom Kiri: Ilustrasi Visual 3D FAQ -->
        <div class="faq-visual-col" aria-hidden="true">
            <div class="faq-illustration-card">
                <svg class="faq-svg-canvas" viewBox="0 0 540 380" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="faq3DGradFront" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#1b3860"/>
                            <stop offset="50%" stop-color="#1d4ed8"/>
                            <stop offset="100%" stop-color="#0284c7"/>
                        </linearGradient>
                        <linearGradient id="faq3DGradSide" x1="0%" y1="0%" x2="0%" y2="100%">
                            <stop offset="0%" stop-color="#0f1f38"/>
                            <stop offset="100%" stop-color="#071426"/>
                        </linearGradient>
                        <linearGradient id="bubbleGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#ffffff"/>
                            <stop offset="100%" stop-color="#f8fafc"/>
                        </linearGradient>
                        <filter id="softGlow" x="-20%" y="-20%" width="140%" height="140%">
                            <feDropShadow dx="0" dy="8" stdDeviation="12" flood-color="#0f172a" flood-opacity="0.12"/>
                        </filter>
                    </defs>

                    <!-- Bayangan Lantai Elips -->
                    <ellipse cx="270" cy="355" rx="230" ry="16" fill="#e2e8f0" opacity="0.6"/>
                    <ellipse cx="280" cy="355" rx="170" ry="10" fill="#cbd5e1" opacity="0.4"/>

                    <!-- Siluet Gedung & Kantor Dinas Belakang -->
                    <g opacity="0.28">
                        <rect x="220" y="160" width="45" height="185" rx="2" fill="#94a3b8"/>
                        <rect x="230" y="175" width="8" height="12" rx="1" fill="#ffffff"/>
                        <rect x="245" y="175" width="8" height="12" rx="1" fill="#ffffff"/>
                        <rect x="230" y="195" width="8" height="12" rx="1" fill="#ffffff"/>
                        <rect x="245" y="195" width="8" height="12" rx="1" fill="#ffffff"/>
                        <rect x="230" y="215" width="8" height="12" rx="1" fill="#ffffff"/>
                        <rect x="245" y="215" width="8" height="12" rx="1" fill="#ffffff"/>

                        <rect x="360" y="130" width="55" height="215" rx="3" fill="#94a3b8"/>
                        <rect x="372" y="145" width="10" height="14" rx="1" fill="#ffffff"/>
                        <rect x="390" y="145" width="10" height="14" rx="1" fill="#ffffff"/>
                        <rect x="372" y="170" width="10" height="14" rx="1" fill="#ffffff"/>
                        <rect x="390" y="170" width="10" height="14" rx="1" fill="#ffffff"/>

                        <rect x="425" y="190" width="60" height="155" rx="2" fill="#cbd5e1"/>
                        <rect x="440" y="205" width="8" height="12" rx="1" fill="#ffffff"/>
                        <rect x="460" y="205" width="8" height="12" rx="1" fill="#ffffff"/>

                        <path d="M180 230 L205 200 L205 345 L180 345 Z" fill="#cbd5e1"/>
                    </g>

                    <!-- Balon Chat Background "?" Atas -->
                    <g filter="url(#softGlow)">
                        <rect x="155" y="45" width="88" height="65" rx="10" fill="url(#bubbleGrad)" stroke="#cbd5e1" stroke-width="1.8"/>
                        <polygon points="175,110 185,122 195,110" fill="#ffffff"/>
                        <line x1="175" y1="110" x2="185" y2="122" stroke="#cbd5e1" stroke-width="1.8"/>
                        <!-- Chat Bubble Lines -->
                        <line x1="170" y1="65" x2="198" y2="65" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round"/>
                        <line x1="170" y1="75" x2="194" y2="75" stroke="#cbd5e1" stroke-width="2.5" stroke-linecap="round"/>
                        <line x1="170" y1="85" x2="188" y2="85" stroke="#cbd5e1" stroke-width="2.5" stroke-linecap="round"/>
                        <!-- Navy Blue Question Mark -->
                        <text x="210" y="88" font-family="'Lexend', sans-serif" font-size="36" font-weight="800" fill="#0284c7">?</text>
                    </g>

                    <!-- Balon Chat Kecil Bulat Kiri & Kanan -->
                    <circle cx="120" cy="85" r="14" fill="#ffffff" stroke="#e2e8f0" stroke-width="1.5" opacity="0.85"/>
                    <text x="116" y="90" font-family="sans-serif" font-size="14" font-weight="700" fill="#94a3b8">?</text>

                    <circle cx="230" cy="120" r="10" fill="#ffffff" stroke="#e2e8f0" stroke-width="1.5" opacity="0.75"/>
                    <text x="227" y="124" font-family="sans-serif" font-size="10" font-weight="700" fill="#94a3b8">?</text>

                    <!-- Karakter Peserta Magang (Seragam Resmi Kominfo Sleman) -->
                    <g id="charGroup">
                        <!-- Sepatu Formal Hitam Mengkilap -->
                        <ellipse cx="140" cy="346" rx="14" ry="7" fill="#0f172a"/>
                        <ellipse cx="178" cy="344" rx="14" ry="7" fill="#0f172a"/>

                        <!-- Celana Panjang Formal Abu-abu Gelap Dinas -->
                        <path d="M130 248 L148 343 L134 345 L118 248 Z" fill="#334155"/>
                        <path d="M172 248 L186 341 L172 343 L158 248 Z" fill="#1e293b"/>
                        <path d="M125 240 L178 240 L174 260 L128 260 Z" fill="#475569"/>

                        <!-- Sabuk Hitam & Gesper Emas -->
                        <rect x="126" y="238" width="50" height="7" rx="1.5" fill="#0f172a"/>
                        <rect x="146" y="237" width="10" height="9" rx="1.5" fill="#f59e0b"/>

                        <!-- Kemeja Dinas Biru Navy Diskominfo Sleman -->
                        <path d="M115 155 Q152 145 188 155 L178 242 L124 242 Z" fill="#1b3860"/>
                        
                        <!-- Kerah Kemeja Rapi dengan Aksen Biru Muda -->
                        <polygon points="151,154 139,173 151,181" fill="#1e40af"/>
                        <polygon points="151,154 163,173 151,181" fill="#122846"/>
                        <path d="M139 173 L151 181 L163 173" stroke="#60a5fa" stroke-width="1.2" fill="none"/>

                        <!-- Lencana / Patch Badge MAGKOM di Dada Kiri -->
                        <rect x="160" y="193" width="14" height="7" rx="1.5" fill="#1e40af" stroke="#60a5fa" stroke-width="0.8"/>
                        <rect x="162" y="195" width="4" height="3" rx="0.5" fill="#f59e0b"/>
                        <line x1="167" y1="196" x2="172" y2="196" stroke="#ffffff" stroke-width="1"/>
                        <line x1="167" y1="198" x2="171" y2="198" stroke="#93c5fd" stroke-width="0.8"/>

                        <!-- Tali Lanyard ID Card Pemkab Sleman & Kartu Tanda Pengenal -->
                        <path d="M145 160 L151 202 L157 160" stroke="#3b82f6" stroke-width="2.2" fill="none"/>
                        <rect x="145" y="202" width="13" height="19" rx="2" fill="#ffffff" stroke="#cbd5e1" stroke-width="1"/>
                        <rect x="145" y="204" width="9" height="5" fill="#1b3860"/>
                        <rect x="145" y="211" width="9" height="2" fill="#f59e0b"/>
                        <rect x="145" y="215" width="6" height="2" fill="#94a3b8"/>

                        <!-- Tangan Kanan (Istirahat di Pinggang) -->
                        <path d="M117 160 Q100 185 106 215 Q112 222 124 220" stroke="#1b3860" stroke-width="13" stroke-linecap="round" fill="none"/>
                        <ellipse cx="122" cy="221" rx="5" ry="5" fill="#f8c291"/>

                        <!-- Leher & Wajah Karakter -->
                        <rect x="146" y="142" width="11" height="16" fill="#e8a87c" rx="2"/>
                        <ellipse cx="152" cy="126" rx="17" ry="19" fill="#f8c291"/>

                        <!-- Rambut Hitam Rapi Eksekutif -->
                        <path d="M135 124 C134 105 168 102 170 120 C171 123 167 114 158 113 C149 112 140 117 135 124 Z" fill="#0f172a"/>
                        <path d="M135 122 Q132 128 135 132" stroke="#0f172a" stroke-width="2" fill="none"/>

                        <!-- Mata & Alis Ramah Menyapa -->
                        <path d="M142 121 Q146 119 149 122" stroke="#0f172a" stroke-width="1.8" fill="none"/>
                        <path d="M155 122 Q159 119 163 121" stroke="#0f172a" stroke-width="1.8" fill="none"/>
                        <circle cx="146" cy="126" r="1.8" fill="#0f172a"/>
                        <circle cx="159" cy="126" r="1.8" fill="#0f172a"/>
                        <!-- Senyum Ramah -->
                        <path d="M148 134 Q153 138 158 134" stroke="#c0392b" stroke-width="1.8" stroke-linecap="round" fill="none"/>

                        <!-- Tangan Kiri Beranimasi: Gesture Menunjuk ke Arah FAQ & Informasi -->
                        <g id="charPointingArm" class="faq-pointing-arm">
                            <!-- Lengan Kemeja Navy Menuju ke FAQ -->
                            <path d="M185 160 Q208 172 226 170" stroke="#1b3860" stroke-width="13" stroke-linecap="round" fill="none"/>
                            <!-- Manset Kemeja Rapi Bergaris Biru Muda -->
                            <ellipse cx="225" cy="170" rx="3.5" ry="6.5" fill="#1e40af"/>
                            <line x1="223" y1="165" x2="223" y2="175" stroke="#60a5fa" stroke-width="1.5"/>
                            <!-- Telapak Tangan & Jari Menunjuk ke Arah Teks FAQ -->
                            <ellipse cx="230" cy="170" rx="5" ry="5.5" fill="#f8c291"/>
                            <!-- Jari Telunjuk Menunjuk Lembut -->
                            <path d="M232 168 L244 167 C246 167 247 170 245 171 L233 173 Z" fill="#f8c291"/>
                            <!-- Jempol Menggenggam -->
                            <path d="M228 166 Q232 164 233 167" stroke="#f8c291" stroke-width="3" stroke-linecap="round" fill="none"/>
                        </g>
                    </g>

                    <!-- Tulisan 3D Tebal Besar "FAQ" (Navy-Blue MagKom Theme) -->
                    <g id="faq3DTextGroup">
                        <!-- Bayangan Tebal Ekstrusi 3D (Deep Navy Shadow) -->
                        <text x="238" y="325" font-family="'Lexend', 'Source Sans 3', sans-serif" font-size="124" font-weight="900" fill="url(#faq3DGradSide)" letter-spacing="-2px">FAQ</text>
                        <text x="236" y="323" font-family="'Lexend', 'Source Sans 3', sans-serif" font-size="124" font-weight="900" fill="url(#faq3DGradSide)" letter-spacing="-2px">FAQ</text>
                        <text x="234" y="321" font-family="'Lexend', 'Source Sans 3', sans-serif" font-size="124" font-weight="900" fill="url(#faq3DGradSide)" letter-spacing="-2px">FAQ</text>
                        <text x="232" y="319" font-family="'Lexend', 'Source Sans 3', sans-serif" font-size="124" font-weight="900" fill="url(#faq3DGradSide)" letter-spacing="-2px">FAQ</text>
                        <text x="230" y="317" font-family="'Lexend', 'Source Sans 3', sans-serif" font-size="124" font-weight="900" fill="url(#faq3DGradSide)" letter-spacing="-2px">FAQ</text>

                        <!-- Sisi Muka Depan (Navy Blue Gradient #1b3860 ke #0284c7) -->
                        <text x="226" y="313" font-family="'Lexend', 'Source Sans 3', sans-serif" font-size="124" font-weight="900" fill="url(#faq3DGradFront)" letter-spacing="-2px" stroke="#0f1f38" stroke-width="1.5">FAQ</text>
                    </g>
                </svg>
            </div>
        </div>

        <!-- Kolom Kanan: Judul & Accordion Kategori Bertingkat -->
        <div class="faq-content-col">
            <div class="faq-accent-bar" aria-hidden="true"></div>
            <h2 id="faqHeading" class="faq-headline-bold">
                {!! str_contains((string) $faqHeading, 'Bingung?') ? str_replace('Bingung?', '<strong>Bingung?</strong>', e($faqHeading)) : e($faqHeading) !!}
            </h2>

            <div id="faqCategoryAccordion" class="faq-category-stack" role="tablist" aria-label="Kategori Pertanyaan yang Sering Ditanyakan">
                @foreach($faqCategoriesData as $cat)
                    <div class="faq-category-item" data-category-id="{{ $cat['id'] }}">
                        <button type="button" class="faq-category-header-btn" aria-expanded="false" aria-controls="faq-drawer-{{ $cat['id'] }}">
                            <span>{{ $cat['nama'] }}</span>
                            <svg class="faq-category-icon-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </button>
                        <div id="faq-drawer-{{ $cat['id'] }}" class="faq-questions-drawer" role="region" style="display: none;">
                            @foreach($cat['items'] ?? [] as $qIdx => $qa)
                                <div class="faq-sub-qa-item">
                                    <button type="button" class="faq-sub-question-btn" aria-expanded="false">
                                        <span>{{ $qa['q'] }}</span>
                                        <svg class="faq-sub-qa-toggle-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="12" y1="5" x2="12" y2="19"></line>
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                        </svg>
                                    </button>
                                    <div class="faq-sub-answer-pane" style="display: none;">
                                        {{ $qa['a'] }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</section>
