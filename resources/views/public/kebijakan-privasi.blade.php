<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kebijakan Privasi &amp; Syarat Ketentuan – Magang Diskominfo Sleman</title>
    <meta name="description" content="Kebijakan Privasi, komitmen perlindungan data pribadi UU PDP, serta Syarat &amp; Ketentuan Magang Dinas Komunikasi dan Informatika Kabupaten Sleman.">

    <!-- Favicon Resmi Sleman / MagKom -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <!-- Preload Logo Sleman -->
    <link rel="preload" as="image" href="{{ asset('images/logo-sleman.png') }}" fetchpriority="high">

    <!-- Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (Vite Asset Pipeline) -->
    @vite(['resources/css/pages/public/public-tailwind.css', 'resources/js/pages/public/kebijakan-privasi.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
    @if(isset($isPreview) && $isPreview)
        @vite(['resources/css/pages/public/landing.css'])
    @endif
</head>
<body class="bg-slate-50 min-h-screen text-slate-800 flex flex-col justify-between selection:bg-blue-600 selection:text-white">

    {{-- Floating Control Bar saat Mode Pratinjau Staging Aktif --}}
    @include('public.partials._preview_control_bar')

    <!-- Header Instansi Resmi -->
    <header class="bg-[#1B3860] border-b border-white/10 shadow-md sticky top-0 z-30 transition-colors">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-3.5 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('landing') }}" class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center p-1 border border-white/15 shadow-sm hover:bg-white/20 transition-all">
                    @include('partials.logo-sleman', ['class' => 'w-full h-full object-contain', 'width' => '32', 'height' => '32'])
                </a>
                <div>
                    <a href="{{ route('landing') }}" class="font-bold text-sm sm:text-base text-white leading-tight hover:text-amber-300 transition-colors">
                        Dinas Komunikasi dan Informatika
                    </a>
                    <p class="text-xs text-slate-300 font-medium">Pemerintah Kabupaten Sleman &bull; Portal MagKom</p>
                </div>
            </div>
            
            <div class="flex items-center space-x-2">
                <a href="{{ route('landing') }}" class="inline-flex items-center text-xs font-semibold text-slate-200 hover:text-white bg-white/10 hover:bg-white/20 px-3 py-2 rounded-xl border border-white/15 transition-all">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    <span>Beranda Utama</span>
                </a>
                <a href="{{ route('landing') }}#kuota" class="hidden sm:inline-flex items-center text-xs font-bold text-slate-900 bg-amber-400 hover:bg-amber-500 px-3.5 py-2 rounded-xl shadow-sm transition-all">
                    <span>Lihat Kuota</span>
                    <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </a>
            </div>
        </div>
    </header>

    <!-- Konten Utama Kebijakan Privasi & Syarat Ketentuan -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 pt-6 pb-12 sm:pt-8 sm:pb-16 w-full flex-grow">
        <article class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-200 overflow-hidden">
            <span class="sr-only">Kebijakan Privasi &amp; Syarat Ketentuan</span>
            @php
                $kebijakanBadge = \App\Support\Pengaturan::get('kebijakan_privasi_badge', 'Undang-Undang PDP No. 27 Tahun 2022');
                $kebijakanTitle = \App\Support\Pengaturan::get('kebijakan_privasi_title', 'Kebijakan Privasi & Perlindungan Data');
                $kebijakanUpdatedAt = \App\Support\Pengaturan::get('kebijakan_privasi_updated_at', 'Terakhir diperbarui: 27 Oktober 2026 • Dinas Komunikasi dan Informatika Kabupaten Sleman');

                $defaultKebijakanSections = [
                    [
                        'nomor' => 1,
                        'judul' => 'Komitmen Perlindungan Data Pribadi',
                        'konten' => 'Dinas Komunikasi dan Informatika (Diskominfo) Kabupaten Sleman berkomitmen penuh untuk melindungi privasi dan keamanan data pribadi calon peserta, peserta aktif, dan alumni program magang (MAGKOM). Kebijakan ini menjelaskan bagaimana kami mengumpulkan, menggunakan, menyimpan, dan melindungi informasi pribadi Anda.',
                    ],
                    [
                        'nomor' => 2,
                        'judul' => 'Data yang Kami Kumpulkan',
                        'konten' => 'Untuk keperluan seleksi administrasi, penempatan bidang, dan penetapan status kelulusan resmi, kami mengumpulkan data yang mencakup:',
                        'items' => [
                            'Identitas Diri: Nama lengkap, NISN/NIM, NIK, tempat & tanggal lahir, serta foto profil resmi.',
                            'Kontak: Alamat surat elektronik (email) aktif dan nomor telepon/WhatsApp.',
                            'Afiliasi Akademik: Asal sekolah SMK/perguruan tinggi, program studi/jurusan, dan semester/tingkat kelas.',
                            'Dokumen Persyaratan: Surat pengantar resmi institusi pendidikan, proposal/curriculum vitae, transkrip nilai, dan portofolio keahlian.',
                        ],
                    ],
                    [
                        'nomor' => 3,
                        'judul' => 'Keamanan Penyimpanan & Tautan Bertanda Tangan (Signed URLs)',
                        'konten' => 'Seluruh berkas dokumen yang diunggah ke dalam sistem MAGKOM disimpan pada media penyimpanan privat yang terenkripsi. Berkas dokumen tidak dapat diakses secara publik tanpa otorisasi.',
                        'highlight' => 'Verifikator dinas dan pembimbing lapangan hanya dapat mengakses berkas Anda melalui tautan sementara bertanda tangan digital (signed URL) dengan masa kedaluwarsa ketat untuk mencegah kebocoran informasi.',
                    ],
                    [
                        'nomor' => 4,
                        'judul' => 'Larangan Komersialisasi & Pembagian Pihak Ketiga',
                        'konten' => 'Pemerintah Kabupaten Sleman menjamin bahwa data pribadi peserta tidak akan pernah diperjualbelikan, disewakan, atau dibagikan kepada pihak ketiga komersial. Data hanya digunakan secara internal untuk keperluan pembinaan kompetensi magang dan pelaporan kinerja instansi pemerintah.',
                    ],
                    [
                        'nomor' => 5,
                        'judul' => 'Hak Subjek Data & Kontak Bantuan',
                        'konten' => 'Anda memiliki hak untuk meminta pembaruan data, memperbaiki kekeliruan biodata, atau berkonsultasi mengenai status dokumen Anda melalui saluran resmi:',
                    ],
                ];

                $kebijakanSections = \App\Support\Pengaturan::json('kebijakan_privasi_sections', $defaultKebijakanSections);
                if (empty($kebijakanSections)) {
                    $kebijakanSections = $defaultKebijakanSections;
                }

                $defaultSyaratSections = [
                    [
                        'nomor' => 1,
                        'judul' => 'Kualifikasi & Status Calon Peserta',
                        'konten' => 'Program Magang MagKom diperuntukkan bagi peserta didik aktif jenjang SMK/MAK dan mahasiswa aktif (D3, D4, atau S1). Calon peserta wajib melampirkan surat pengantar resmi dari institusi pendidikan asal dan memastikan seluruh data yang diisikan adalah benar serta dapat dipertanggungjawabkan secara hukum.',
                    ],
                    [
                        'nomor' => 2,
                        'judul' => 'Tata Tertib, Kehadiran & Jam Kerja Kedinasan',
                        'konten' => 'Peserta magang diwajibkan mematuhi jam operasional kedinasan Pemerintah Kabupaten Sleman (Senin s/d Jumat, pukul 07.30 s/d 15.30 WIB), mengenakan pakaian rapi, sopan, dan beridentitas almamater/institusi, serta menjaga etika dan norma kesopanan birokrasi pemerintahan.',
                    ],
                    [
                        'nomor' => 3,
                        'judul' => 'Kewajiban Pengisian Logbook Mingguan & Tugas',
                        'konten' => 'Setiap peserta berkewajiban mencatat aktivitas magang secara berkala pada modul Logbook Mingguan di portal MagKom dan menyelesaikan setiap penugasan teknis yang didelegasikan oleh Pembimbing Lapangan sebelum tenggat waktu berakhir.',
                        'highlight' => 'Kelalaian pengisian logbook berkala dapat memengaruhi evaluasi kedisiplinan dan status kelulusan magang.',
                    ],
                    [
                        'nomor' => 4,
                        'judul' => 'Kerahasiaan Informasi & Keamanan Siber (Non-Disclosure)',
                        'konten' => 'Peserta dilarang keras menyebarluaskan, menduplikasi, mempublikasikan, atau membocorkan data internal, kode sumber aplikasi (source code), konfigurasi server/jaringan, atau data pribadi kependudukan masyarakat yang diakses selama masa magang di Dinas Kominfo Sleman kepada pihak luar tanpa izin tertulis.',
                    ],
                    [
                        'nomor' => 5,
                        'judul' => 'Penyelesaian Magang, Kelulusan & Pembatalan Status',
                        'konten' => 'Verifikasi penyelesaian program dilakukan oleh pembimbing dinas berdasarkan disiplin, kualitas luaran tugas, dan laporan akhir. Peserta yang menyelesaikan program dengan baik berhak mendapatkan status lulus program magang resmi. Pelanggaran berat terhadap tata tertib atau hukum dapat mengakibatkan pencabutan status magang secara sepihak dan pemberitahuan resmi ke institusi asal.',
                    ],
                ];

                $layananEmail = \App\Support\Pengaturan::get('landing_footer_email', 'kominfo@slemankab.go.id');
                $layananTelepon = \App\Support\Pengaturan::get('landing_footer_telepon', '(0274) 868405');
            @endphp

            <!-- Header Kartu -->
            <div class="bg-gradient-to-r from-[#122846] via-[#1B3860] to-[#003566] px-6 sm:px-10 py-8 text-white relative overflow-hidden">
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-amber-400/20 border border-amber-400/30 text-amber-300 text-xs font-semibold uppercase tracking-wider mb-3">
                    <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                    </svg>
                    <span>{{ $kebijakanBadge }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">{{ $kebijakanTitle }}</h1>
                <p class="text-xs sm:text-sm text-slate-300 mt-2">
                    {{ $kebijakanUpdatedAt }}
                </p>
            </div>

            <!-- Tab Switcher Antara Kebijakan Privasi dan Syarat & Ketentuan -->
            <div class="flex border-b border-slate-200 bg-slate-100/80 p-2 gap-2">
                <button type="button" id="tabBtnPrivasi" class="flex-1 py-2.5 px-4 rounded-xl font-bold text-xs sm:text-sm text-center transition-all bg-white text-[#003566] shadow-sm border border-slate-200 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-[#003566]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    <span>Kebijakan Privasi (UU PDP)</span>
                </button>
                <button type="button" id="tabBtnSyarat" class="flex-1 py-2.5 px-4 rounded-xl font-bold text-xs sm:text-sm text-center transition-all text-slate-600 hover:text-[#003566] hover:bg-white/60 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span>Syarat &amp; Ketentuan Magang</span>
                </button>
            </div>

            <!-- PANE 1: Isi Dokumen Kebijakan Privasi -->
            <div id="panePrivasi" class="p-6 sm:p-10 space-y-8 text-slate-700 leading-relaxed text-sm sm:text-base">
                @foreach($kebijakanSections as $idx => $sec)
                    <section>
                        <div class="flex items-center space-x-2.5 text-navy-800 font-bold text-lg mb-2">
                            <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-extrabold border border-blue-100">
                                {{ $sec['nomor'] ?? ($idx + 1) }}
                            </span>
                            <h2>{{ $sec['judul'] ?? '' }}</h2>
                        </div>
                        <p class="text-slate-600 pl-9 mb-3">
                            {{ $sec['konten'] ?? '' }}
                        </p>

                        @if(!empty($sec['items']) && is_array($sec['items']))
                            <ul class="list-disc list-inside space-y-1.5 pl-9 text-slate-600 text-sm mb-3">
                                @foreach($sec['items'] as $itemText)
                                    <li>{{ $itemText }}</li>
                                @endforeach
                            </ul>
                        @endif

                        @if(!empty($sec['highlight']))
                            <div class="pl-9 mb-3">
                                <div class="p-4 bg-blue-50 border border-blue-200/80 rounded-xl text-xs sm:text-sm text-blue-900 flex items-start space-x-3">
                                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                    <p class="leading-relaxed">
                                        {{ $sec['highlight'] }}
                                    </p>
                                </div>
                            </div>
                        @endif

                        @if(($sec['nomor'] ?? ($idx + 1)) == 5)
                            <div class="pl-9 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs sm:text-sm mt-3">
                                <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl">
                                    <span class="text-slate-400 font-semibold block text-xs">Email Layanan:</span>
                                    <span class="font-bold text-slate-800">{{ $layananEmail }}</span>
                                </div>
                                <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl">
                                    <span class="text-slate-400 font-semibold block text-xs">Telepon Kantor:</span>
                                    <span class="font-bold text-slate-800">{{ $layananTelepon }}</span>
                                </div>
                            </div>
                        @endif
                    </section>

                    @if(!$loop->last)
                        <hr class="border-slate-100">
                    @endif
                @endforeach
            </div>

            <!-- PANE 2: Isi Dokumen Syarat & Ketentuan Magang -->
            <div id="paneSyarat" class="hidden p-6 sm:p-10 space-y-8 text-slate-700 leading-relaxed text-sm sm:text-base">
                @foreach($defaultSyaratSections as $sIdx => $syarat)
                    <section>
                        <div class="flex items-center space-x-2.5 text-navy-800 font-bold text-lg mb-2">
                            <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center text-xs font-extrabold border border-amber-200">
                                {{ $syarat['nomor'] }}
                            </span>
                            <h2>Pasal {{ $syarat['nomor'] }}: {{ $syarat['judul'] }}</h2>
                        </div>
                        <p class="text-slate-600 pl-9 mb-3">
                            {{ $syarat['konten'] }}
                        </p>

                        @if(!empty($syarat['highlight']))
                            <div class="pl-9 mb-3">
                                <div class="p-4 bg-amber-50 border border-amber-200/80 rounded-xl text-xs sm:text-sm text-amber-950 flex items-start space-x-3">
                                    <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <p class="leading-relaxed">
                                        {{ $syarat['highlight'] }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    </section>

                    @if(!$loop->last)
                        <hr class="border-slate-100">
                    @endif
                @endforeach
            </div>

            <!-- Box Navigasi Cepat Terintegrasi -->
            <div class="p-6 sm:p-10 pt-0">
                <div class="p-6 bg-gradient-to-r from-slate-50 to-blue-50/40 border border-slate-200 rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div>
                        <h3 class="font-bold text-base text-[#003566]">Siap bergabung dengan Diskominfo Sleman?</h3>
                        <p class="text-xs text-slate-600 mt-0.5">Lihat kuota bidang yang tersedia dan mulai langkah pendaftaran Anda.</p>
                    </div>
                    <div class="flex items-center space-x-2 flex-shrink-0">
                        <a href="{{ route('panduan.pdf') }}" class="px-3.5 py-2 text-xs font-semibold text-[#003566] bg-white border border-slate-300 rounded-xl hover:bg-slate-100 transition-colors">
                            Unduh PDF Panduan
                        </a>
                        <a href="{{ route('landing') }}#kuota" class="px-4 py-2 text-xs font-bold text-slate-900 bg-amber-400 hover:bg-amber-500 rounded-xl shadow-md transition-all">
                            Pilih Kuota Magang &rarr;
                        </a>
                    </div>
                </div>
            </div>

        </article>
    </main>

    <!-- Footer Resmi Instansi -->
    <footer class="border-t border-white/10 bg-[#122846] py-6 text-center text-xs text-slate-400">
        <div class="max-w-5xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-center">
            <p>&copy; {{ date('Y') }} Dinas Komunikasi dan Informatika Kabupaten Sleman. Seluruh hak cipta dilindungi.</p>
        </div>
    </footer>
</body>
</html>
