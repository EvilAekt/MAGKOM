<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kebijakan Privasi &amp; Perlindungan Data – Magang Diskominfo Sleman</title>
    <meta name="description" content="Kebijakan Privasi dan komitmen perlindungan data pribadi peserta magang Dinas Komunikasi dan Informatika Kabupaten Sleman.">

    <!-- Favicon Resmi Sleman / MagKom -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <!-- Preload Logo Sleman -->
    <link rel="preload" as="image" href="{{ asset('images/logo-sleman.png') }}" fetchpriority="high">

    <!-- Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS via CDN untuk utilitas layout -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: {
                            900: '#0c1b2e',
                            800: '#1b3860',
                            700: '#234a7d',
                        },
                        brand: {
                            blue: '#0751FF',
                            gold: '#f59e0b',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800 flex flex-col justify-between selection:bg-blue-600 selection:text-white">

    <!-- Header Instansi Resmi -->
    <header class="bg-white border-b border-slate-200 shadow-sm sticky top-0 z-30">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-3.5 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('landing') }}" class="w-10 h-10 rounded-xl bg-white flex items-center justify-center p-1 border border-slate-200 shadow-sm hover:border-blue-500 transition-colors">
                    @include('partials.logo-sleman', ['class' => 'w-full h-full object-contain', 'width' => '32', 'height' => '32'])
                </a>
                <div>
                    <a href="{{ route('landing') }}" class="font-bold text-sm sm:text-base text-slate-900 leading-tight hover:text-blue-600 transition-colors">
                        Dinas Komunikasi dan Informatika
                    </a>
                    <p class="text-xs text-slate-500 font-medium">Pemerintah Kabupaten Sleman &bull; Portal MagKom</p>
                </div>
            </div>
            
            <div class="flex items-center space-x-2">
                <a href="{{ route('landing') }}" class="inline-flex items-center text-xs font-semibold text-slate-600 hover:text-blue-600 bg-slate-100 hover:bg-blue-50 px-3 py-2 rounded-xl border border-slate-200 transition-all">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    <span>Beranda Utama</span>
                </a>
                <a href="{{ route('landing') }}#kuota" class="hidden sm:inline-flex items-center text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 px-3.5 py-2 rounded-xl shadow-sm transition-all">
                    <span>Lihat Kuota</span>
                    <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </a>
            </div>
        </div>
    </header>

    <!-- Konten Utama Kebijakan Privasi -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 py-8 sm:py-12 w-full flex-grow">
        
        <!-- Breadcrumb Navigasi -->
        <nav class="flex items-center space-x-2 text-xs text-slate-500 mb-6" aria-label="Breadcrumb">
            <a href="{{ route('landing') }}" class="hover:text-blue-600">Beranda</a>
            <span>/</span>
            <span class="text-slate-900 font-semibold">Kebijakan Privasi</span>
        </nav>

        <article class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-200 overflow-hidden">
            <!-- Header Kartu -->
            <div class="bg-gradient-to-r from-navy-900 via-navy-800 to-blue-900 px-6 sm:px-10 py-8 text-white">
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-200 text-xs font-semibold uppercase tracking-wider mb-3">
                    <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                    </svg>
                    <span>Undang-Undang PDP No. 27 Tahun 2022</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Kebijakan Privasi &amp; Perlindungan Data</h1>
                <p class="text-xs sm:text-sm text-slate-300 mt-2">
                    Terakhir diperbarui: 27 Oktober 2026 &bull; Dinas Komunikasi dan Informatika Kabupaten Sleman
                </p>
            </div>

            <!-- Isi Dokumen Kebijakan -->
            <div class="p-6 sm:p-10 space-y-8 text-slate-700 leading-relaxed text-sm sm:text-base">
                
                <section>
                    <div class="flex items-center space-x-2.5 text-navy-800 font-bold text-lg mb-2">
                        <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-extrabold border border-blue-100">1</span>
                        <h2>Komitmen Perlindungan Data Pribadi</h2>
                    </div>
                    <p class="text-slate-600 pl-9">
                        Dinas Komunikasi dan Informatika (Diskominfo) Kabupaten Sleman berkomitmen penuh untuk melindungi privasi dan keamanan data pribadi calon peserta, peserta aktif, dan alumni program magang (MAGKOM). Kebijakan ini menjelaskan bagaimana kami mengumpulkan, menggunakan, menyimpan, dan melindungi informasi pribadi Anda.
                    </p>
                </section>

                <hr class="border-slate-100">

                <section>
                    <div class="flex items-center space-x-2.5 text-navy-800 font-bold text-lg mb-2">
                        <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-extrabold border border-blue-100">2</span>
                        <h2>Data yang Kami Kumpulkan</h2>
                    </div>
                    <p class="text-slate-600 pl-9 mb-3">
                        Untuk keperluan seleksi administrasi, penempatan bidang, dan penerbitan sertifikat resmi bertanda tangan elektronik (TTE), kami mengumpulkan data yang mencakup:
                    </p>
                    <ul class="list-disc list-inside space-y-1.5 pl-9 text-slate-600 text-sm">
                        <li><strong>Identitas Diri:</strong> Nama lengkap, NISN/NIM, NIK, tempat & tanggal lahir, serta foto profil resmi.</li>
                        <li><strong>Kontak:</strong> Alamat surat elektronik (email) aktif dan nomor telepon/WhatsApp.</li>
                        <li><strong>Afiliasi Akademik:</strong> Asal sekolah SMK/perguruan tinggi, program studi/jurusan, dan semester/tingkat kelas.</li>
                        <li><strong>Dokumen Persyaratan:</strong> Surat pengantar resmi institusi pendidikan, proposal/curriculum vitae, transkrip nilai, dan portofolio keahlian.</li>
                    </ul>
                </section>

                <hr class="border-slate-100">

                <section>
                    <div class="flex items-center space-x-2.5 text-navy-800 font-bold text-lg mb-2">
                        <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-extrabold border border-blue-100">3</span>
                        <h2>Keamanan Penyimpanan &amp; Tautan Bertanda Tangan (Signed URLs)</h2>
                    </div>
                    <p class="text-slate-600 pl-9 mb-3">
                        Seluruh berkas dokumen yang diunggah ke dalam sistem MAGKOM disimpan pada media penyimpanan privat yang terenkripsi. Berkas dokumen tidak dapat diakses secara publik tanpa otorisasi.
                    </p>
                    <div class="pl-9">
                        <div class="p-4 bg-blue-50 border border-blue-200/80 rounded-xl text-xs sm:text-sm text-blue-900 flex items-start space-x-3">
                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            <p class="leading-relaxed">
                                Verifikator dinas dan pembimbing lapangan hanya dapat mengakses berkas Anda melalui tautan sementara bertanda tangan digital (signed URL) dengan masa kedaluwarsa ketat untuk mencegah kebocoran informasi.
                            </p>
                        </div>
                    </div>
                </section>

                <hr class="border-slate-100">

                <section>
                    <div class="flex items-center space-x-2.5 text-navy-800 font-bold text-lg mb-2">
                        <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-extrabold border border-blue-100">4</span>
                        <h2>Larangan Komersialisasi &amp; Pembagian Pihak Ketiga</h2>
                    </div>
                    <p class="text-slate-600 pl-9">
                        Pemerintah Kabupaten Sleman menjamin bahwa data pribadi peserta <strong>tidak akan pernah diperjualbelikan, disewakan, atau dibagikan kepada pihak ketiga komersial</strong>. Data hanya digunakan secara internal untuk keperluan pembinaan kompetensi magang dan pelaporan kinerja instansi pemerintah.
                    </p>
                </section>

                <hr class="border-slate-100">

                <section>
                    <div class="flex items-center space-x-2.5 text-navy-800 font-bold text-lg mb-2">
                        <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-extrabold border border-blue-100">5</span>
                        <h2>Hak Subjek Data &amp; Kontak Bantuan</h2>
                    </div>
                    <p class="text-slate-600 pl-9 mb-4">
                        Anda memiliki hak untuk meminta pembaruan data, memperbaiki kekeliruan biodata, atau berkonsultasi mengenai status dokumen Anda melalui saluran resmi:
                    </p>
                    <div class="pl-9 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs sm:text-sm">
                        <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl">
                            <span class="text-slate-400 font-semibold block text-xs">Email Layanan:</span>
                            <span class="font-bold text-slate-800">kominfo@slemankab.go.id</span>
                        </div>
                        <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl">
                            <span class="text-slate-400 font-semibold block text-xs">Telepon Kantor:</span>
                            <span class="font-bold text-slate-800">(0274) 868405</span>
                        </div>
                    </div>
                </section>

                <!-- Box Navigasi Cepat Terintegrasi -->
                <div class="mt-8 p-6 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div>
                        <h3 class="font-bold text-base text-blue-950">Siap bergabung dengan Diskominfo Sleman?</h3>
                        <p class="text-xs text-blue-700 mt-0.5">Lihat kuota bidang yang tersedia dan mulai langkah pendaftaran Anda.</p>
                    </div>
                    <div class="flex items-center space-x-2 flex-shrink-0">
                        <a href="{{ route('panduan.pdf') }}" class="px-3.5 py-2 text-xs font-semibold text-blue-800 bg-white border border-blue-300 rounded-xl hover:bg-blue-100 transition-colors">
                            Unduh PDF Panduan
                        </a>
                        <a href="{{ route('landing') }}#kuota" class="px-4 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-md transition-all">
                            Pilih Kuota Magang &rarr;
                        </a>
                    </div>
                </div>

            </div>
        </article>
    </main>

    <!-- Footer Resmi Instansi -->
    <footer class="border-t border-slate-200 bg-white py-6 text-center text-xs text-slate-500">
        <div class="max-w-5xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
            <p>&copy; {{ date('Y') }} Dinas Komunikasi dan Informatika Kabupaten Sleman.</p>
            <div class="flex items-center space-x-4">
                <a href="{{ route('landing') }}" class="hover:text-blue-600 transition-colors">Beranda</a>
                <a href="{{ route('panduan-alur') }}" class="hover:text-blue-600 transition-colors">Peta Alur</a>
                <a href="{{ url('/verifikasi-dokumen/cek') }}" class="hover:text-blue-600 transition-colors">Verifikasi Dokumen</a>
                <a href="{{ route('login') }}" class="hover:text-blue-600 transition-colors">Login</a>
            </div>
        </div>
    </footer>

</body>
</html>
