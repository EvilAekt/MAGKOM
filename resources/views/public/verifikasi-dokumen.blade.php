<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Keabsahan Dokumen Resmi - MagKom Sleman</title>
    <!-- Preload Aset Logo Prioritas Tinggi (Cegah Flicker / Kedipan saat Navigasi) -->
    <link rel="preload" as="image" href="{{ asset('images/logo-sleman.png') }}" fetchpriority="high">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800 flex flex-col justify-between">

    <!-- Header Instansi Resmi -->
    <header class="bg-white border-b border-slate-200 shadow-sm sticky top-0 z-30">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center p-1 border border-slate-200 shadow-sm">
                    @include('partials.logo-sleman', ['class' => 'w-full h-full object-contain', 'width' => '32', 'height' => '32'])
                </div>
                <div>
                    <h1 class="font-bold text-base text-slate-900 leading-tight">Dinas Komunikasi dan Informatika</h1>
                    <p class="text-xs text-slate-500 font-medium">Pemerintah Kabupaten Sleman &bull; Magang Kominfo</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('landing') }}" class="text-xs font-semibold text-slate-700 hover:text-blue-600 bg-slate-100 hover:bg-blue-50 px-3 py-1.5 rounded-lg border border-slate-200 transition-colors">
                    &larr; Portal Utama
                </a>
                <a href="{{ route('landing') }}#kuota" class="hidden sm:inline-flex text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 px-3 py-1.5 rounded-lg shadow-sm transition-colors">
                    Pilih Kuota
                </a>
            </div>
        </div>
    </header>

    <!-- Konten Utama -->
    <main class="max-w-3xl mx-auto px-4 sm:px-6 py-8 sm:py-12 w-full">
        @if($dokumen)
            @if($dokumen->status_keabsahan === 'sah')
                <!-- Status: DOKUMEN SAH & TERVERIFIKASI -->
                <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/60 border border-slate-200/80 overflow-hidden">
                    <!-- Header Banner Hijau -->
                    <div class="bg-gradient-to-r from-emerald-600 to-teal-700 px-6 py-6 text-white text-center">
                        <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-white/20 backdrop-blur-sm text-white mb-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight">Dokumen Resmi Terverifikasi</h2>
                        <p class="text-xs sm:text-sm text-emerald-100 mt-1 max-w-md mx-auto">
                            Dokumen ini dinyatakan ASLI dan tercatat sah dalam pangkalan data Dinas Komunikasi dan Informatika Kabupaten Sleman.
                        </p>
                    </div>
            @else
                <!-- Status: DOKUMEN DICABUT / TIDAK BERLAKU -->
                <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/60 border border-rose-200 overflow-hidden">
                    <!-- Header Banner Merah -->
                    <div class="bg-gradient-to-r from-rose-600 to-red-700 px-6 py-6 text-white text-center">
                        <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-white/20 backdrop-blur-sm text-white mb-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight">DOKUMEN DICABUT / TIDAK BERLAKU</h2>
                        <p class="text-xs sm:text-sm text-rose-100 mt-1 max-w-md mx-auto">
                            Status keabsahan dokumen ini telah DICABUT atau DIBATALKAN oleh instansi penerbit. Dokumen tidak lagi memiliki kekuatan hukum.
                        </p>
                    </div>
            @endif

                <div class="p-6 sm:p-8 space-y-6">
                    <!-- Nomor & Kode Tiket -->
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Nomor Registrasi Dokumen</span>
                            <span class="text-base font-bold text-slate-900 font-mono">{{ $dokumen->nomor_dokumen }}</span>
                        </div>
                        <div class="sm:text-right">
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Kode Verifikasi Elektronik</span>
                            <span class="inline-flex items-center text-xs font-mono font-bold bg-blue-100 text-blue-800 px-2.5 py-1 rounded-md">
                                {{ $dokumen->kode_unik }}
                            </span>
                        </div>
                    </div>

                    <!-- Detail Spesifikasi Dokumen -->
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider text-slate-500 mb-3">Rincian Informasi Dokumen</h3>
                        <div class="border border-slate-200 rounded-xl divide-y divide-slate-200 text-sm overflow-hidden">
                            <div class="grid grid-cols-1 sm:grid-cols-3 p-3.5 bg-white">
                                <dt class="text-slate-500 font-medium">Jenis Dokumen</dt>
                                <dd class="sm:col-span-2 font-semibold text-slate-900">
                                    @if($dokumen->tipe_dokumen === 'surat_diterima')
                                        Surat Keterangan Diterima Magang
                                    @elseif($dokumen->tipe_dokumen === 'lembar_nilai')
                                        Lembar Nilai Resmi Magang
                                    @elseif($dokumen->tipe_dokumen === 'sertifikat')
                                        Sertifikat Kelulusan Praktik Kerja Lapangan
                                    @else
                                        {{ ucwords(str_replace('_', ' ', $dokumen->tipe_dokumen)) }}
                                    @endif
                                </dd>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 p-3.5 bg-slate-50/50">
                                <dt class="text-slate-500 font-medium">Nama Peserta</dt>
                                <dd class="sm:col-span-2 font-bold text-slate-900 text-base">
                                    {{ $dokumen->metadata['nama_peserta'] ?? $dokumen->penempatan?->peserta?->nama ?? '-' }}
                                </dd>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 p-3.5 bg-white">
                                <dt class="text-slate-500 font-medium">NIM / NISN</dt>
                                <dd class="sm:col-span-2 font-mono text-slate-800">
                                    {{ $dokumen->metadata['nim_nisn'] ?? $dokumen->penempatan?->peserta?->profil?->nim_nisn ?? '-' }}
                                </dd>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 p-3.5 bg-slate-50/50">
                                <dt class="text-slate-500 font-medium">Institusi Asal</dt>
                                <dd class="sm:col-span-2 text-slate-800 font-medium">
                                    {{ $dokumen->metadata['institusi'] ?? $dokumen->penempatan?->peserta?->profil?->institusi ?? '-' }}
                                </dd>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 p-3.5 bg-white">
                                <dt class="text-slate-500 font-medium">Jurusan / Program Studi</dt>
                                <dd class="sm:col-span-2 text-slate-800">
                                    {{ $dokumen->metadata['jurusan'] ?? $dokumen->penempatan?->kuota?->jurusan?->nama_jurusan ?? '-' }}
                                </dd>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 p-3.5 bg-slate-50/50">
                                <dt class="text-slate-500 font-medium">Periode Pelaksanaan</dt>
                                <dd class="sm:col-span-2 text-slate-800 font-medium">
                                    {{ $dokumen->metadata['tgl_mulai'] ?? '-' }} s/d {{ $dokumen->metadata['tgl_selesai'] ?? '-' }}
                                </dd>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 p-3.5 bg-white">
                                <dt class="text-slate-500 font-medium">Tanggal Diterbitkan</dt>
                                <dd class="sm:col-span-2 text-slate-800">
                                    {{ \Carbon\Carbon::parse($dokumen->tanggal_terbit)->translatedFormat('d F Y') }}
                                </dd>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 p-3.5 bg-slate-50/50">
                                <dt class="text-slate-500 font-medium">Otoritas Penandatangan</dt>
                                <dd class="sm:col-span-2 text-slate-800 font-medium">
                                    {{ $dokumen->metadata['penandatangan'] ?? 'Kepala Dinas Komunikasi dan Informatika' }}
                                </dd>
                            </div>
                        </div>
                    </div>

                    <!-- Catatan Keabsahan Hukum -->
                    <div class="rounded-xl p-4 bg-emerald-50 border border-emerald-200/70 text-xs text-emerald-800 flex items-start space-x-3">
                        <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="leading-relaxed">
                            Dokumen ini telah ditandatangani secara elektronik sesuai dengan ketentuan Undang-Undang Informasi dan Transaksi Elektronik (UU ITE). Segala bentuk pemalsuan atau modifikasi isi dokumen dapat dikenakan sanksi hukum yang berlaku.
                        </p>
                    </div>
                </div>
            </div>
        @else
            <!-- Status: DOKUMEN TIDAK VALID / TIDAK DITEMUKAN -->
            <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/60 border border-rose-200 overflow-hidden text-center p-8 sm:p-12">
                <div class="w-16 h-16 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-slate-900">Dokumen Tidak Ditemukan atau Tidak Sah</h2>
                <p class="text-sm text-slate-600 mt-2 max-w-md mx-auto">
                    Kode verifikasi <code class="bg-slate-100 px-2 py-0.5 rounded text-rose-600 font-mono font-bold">{{ $kodeUnik }}</code> tidak tercatat dalam pangkalan data resmi Diskominfo Sleman.
                </p>
                <div class="mt-6 p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-500 max-w-md mx-auto text-left">
                    <p class="font-semibold text-slate-700 mb-1">Kemungkinan penyebab:</p>
                    <ul class="list-disc list-inside space-y-1">
                        <li>Kode QR atau tautan verifikasi rusak atau salah diketik.</li>
                        <li>Dokumen telah dibatalkan atau dicabut masa berlakunya.</li>
                        <li>Dokumen palsu atau tidak diterbitkan oleh portal resmi MagKom.</li>
                    </ul>
                </div>
                <div class="mt-8">
                    <a href="{{ route('landing') }}" class="inline-flex items-center px-4 py-2.5 bg-blue-900 hover:bg-blue-800 text-white text-sm font-semibold rounded-xl shadow-md transition-all">
                        Kembali ke Halaman Utama
                    </a>
                </div>
            </div>
        @endif
    </main>

    <!-- Footer Resmi -->
    <footer class="border-t border-slate-200 bg-white py-6 text-center text-xs text-slate-500">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div>
                <p>&copy; {{ date('Y') }} Dinas Komunikasi dan Informatika Kabupaten Sleman.</p>
                <p class="mt-0.5 text-slate-400">Jl. Parasamya No. 1, Beran, Tridadi, Sleman, D.I. Yogyakarta 55511</p>
            </div>
            <div class="flex items-center space-x-3 text-slate-600 font-medium">
                <a href="{{ route('landing') }}" class="hover:text-blue-600 transition-colors">Beranda</a>
                <span>&bull;</span>
                <a href="{{ route('landing') }}#kuota" class="hover:text-blue-600 transition-colors">Kuota Magang</a>
                <span>&bull;</span>
                <a href="{{ route('panduan-alur') }}" class="hover:text-blue-600 transition-colors">Peta Alur</a>
                <span>&bull;</span>
                <a href="{{ route('kebijakan-privasi') }}" class="hover:text-blue-600 transition-colors">Kebijakan Privasi</a>
            </div>
        </div>
    </footer>

</body>
</html>
