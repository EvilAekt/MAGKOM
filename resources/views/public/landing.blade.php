@extends('layouts.app')

@section('title', 'Portal MagKom - Informasi Kuota & Pendaftaran Magang Kominfo')
@section('meta_description', 'Transparansi kuota pendaftaran magang Dinas Komunikasi dan Informatika untuk siswa SMK dan mahasiswa D3/S1.')
@section('og_title', 'Informasi Kuota Magang Dinas Kominfo - MagKom')
@section('og_description', 'Lihat ketersediaan kuota magang per jurusan secara langsung dan transparan.')

@push('styles')
<style>
    .hero-section {
        background: radial-gradient(circle at 80% 20%, rgba(37, 99, 235, 0.25), transparent 50%),
                    linear-gradient(135deg, #0b1a30 0%, #102a4e 100%);
        color: #ffffff;
        padding: 5rem 0 4rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(37, 99, 235, 0.2);
        border: 1px solid rgba(59, 130, 246, 0.4);
        padding: 0.35rem 0.85rem;
        border-radius: 9999px;
        font-size: 0.8125rem;
        color: #60a5fa;
        font-weight: 600;
        margin-bottom: 1.5rem;
    }

    .hero-title {
        font-size: clamp(2rem, 5vw, 3.25rem);
        font-weight: 800;
        line-height: 1.15;
        letter-spacing: -0.025em;
        margin-bottom: 1.25rem;
        max-width: 800px;
    }

    .hero-title span {
        background: linear-gradient(135deg, #60a5fa, #2dd4bf);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .hero-desc {
        font-size: 1.125rem;
        color: #94a3b8;
        max-width: 680px;
        line-height: 1.6;
        margin-bottom: 2rem;
    }

    .section-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 0.5rem;
    }

    .section-subtitle {
        color: var(--gray-600);
        font-size: 1rem;
        margin-bottom: 2rem;
    }

    .tab-nav {
        display: flex;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
        border-bottom: 2px solid var(--gray-200);
        padding-bottom: 0.5rem;
    }

    .tab-btn {
        background: transparent;
        border: none;
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
        font-weight: 600;
        color: var(--gray-600);
        cursor: pointer;
        border-radius: 0.5rem 0.5rem 0 0;
        transition: all 0.2s ease;
        position: relative;
    }

    .tab-btn.active {
        color: var(--primary-600);
        background: #ffffff;
    }

    .tab-btn.active::after {
        content: '';
        position: absolute;
        bottom: -0.6rem;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--primary-600);
        border-radius: 3px;
    }

    .quota-card {
        background: #ffffff;
        border: 1px solid var(--gray-200);
        border-radius: 1rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .quota-card:hover {
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
        transform: translateY(-2px);
    }

    .jurusan-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.25rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--gray-100);
    }

    .jurusan-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--gray-900);
    }

    .jurusan-badge {
        display: inline-block;
        padding: 0.25rem 0.65rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .badge-smk { background: #e0f2fe; color: #0369a1; }
    .badge-d3 { background: #fef3c7; color: #92400e; }
    .badge-s1 { background: #dcfce7; color: #166534; }

    .month-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1rem;
    }

    .month-box {
        background: var(--gray-50);
        border: 1px solid var(--gray-200);
        border-radius: 0.75rem;
        padding: 1rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .month-box-title {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 0.5rem;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.65rem;
        border-radius: 0.375rem;
        font-size: 0.8125rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
    }

    .status-tersedia {
        background: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    .status-waiting-list {
        background: #fffbeb;
        color: #92400e;
        border: 1px solid #fde68a;
    }

    .status-segera {
        background: #eff6ff;
        color: #1e40af;
        border: 1px solid #bfdbfe;
    }

    .status-tutup {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
    }

    .quota-info {
        font-size: 0.875rem;
        color: var(--gray-600);
        margin-bottom: 1rem;
    }

    .quota-count {
        font-weight: 700;
        color: var(--gray-900);
        font-size: 1.1rem;
    }

    .btn-sm {
        padding: 0.45rem 1rem;
        font-size: 0.8125rem;
        border-radius: 0.375rem;
        width: 100%;
        text-align: center;
    }

    .btn-disabled {
        background: #e2e8f0;
        color: #94a3b8;
        cursor: not-allowed;
        box-shadow: none;
    }

    .step-card {
        background: #ffffff;
        border: 1px solid var(--gray-200);
        border-radius: 0.75rem;
        padding: 1.5rem;
        text-align: center;
    }

    .step-number {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 9999px;
        background: #2563eb;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        margin: 0 auto 1rem;
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-badge">
            <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#34d399;"></span>
            Penerimaan Magang Aktif • {{ $sekarang->translatedFormat('F Y') }}
        </div>
        <h1 class="hero-title">
            Bangun Pengalaman Nyata di <span>Dinas Komunikasi & Informatika</span>
        </h1>
        <p class="hero-desc">
            Sistem pendaftaran magang terbuka, objektif, dan transparan untuk talenta digital dari SMK dan Perguruan Tinggi. Pantau kuota jurusan favoritmu secara langsung tanpa perantara.
        </p>
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <a href="#kuota-section" class="btn btn-primary" style="padding: 0.75rem 1.75rem; font-size: 1rem;">
                Lihat Kuota Jurusan
            </a>
            <a href="#alur-section" class="btn btn-outline" style="padding: 0.75rem 1.75rem; font-size: 1rem;">
                Panduan Pendaftaran
            </a>
        </div>
    </div>
</section>

<!-- Kuota Section -->
<section id="kuota-section" style="padding: 4rem 0;">
    <div class="container">
        <div style="text-align: center; max-width: 640px; margin: 0 auto 2.5rem;">
            <h2 class="section-title">Ketersediaan Kuota Magang</h2>
            <p class="section-subtitle">
                Data kuota diperbarui secara real-time. Kuota yang habis tetap dapat didaftarkan untuk masuk daftar tunggu (Waiting List).
            </p>
        </div>

        @if($bulanTampil->isEmpty())
            <div style="text-align: center; background: #ffffff; padding: 3rem; border-radius: 1rem; border: 1px dashed var(--gray-200);">
                <p style="font-size: 1.125rem; color: var(--gray-600); font-weight: 500;">
                    Saat ini belum ada periode pendaftaran magang yang dibuka untuk publik. Silakan cek kembali secara berkala.
                </p>
            </div>
        @else
            <!-- Navigasi Tab SMK vs Mahasiswa (AC-01-1) -->
            <div class="tab-nav">
                <button type="button" class="tab-btn active" id="tab-btn-smk" onclick="switchTab('smk')">
                    Jalur SMK (Sekolah Menengah Kejuruan)
                </button>
                <button type="button" class="tab-btn" id="tab-btn-mahasiswa" onclick="switchTab('mahasiswa')">
                    Jalur Mahasiswa (D3 / S1)
                </button>
            </div>

            <!-- Tab Content: SMK -->
            <div id="tab-content-smk">
                @forelse($jurusanSmk as $jurusan)
                    <div class="quota-card">
                        <div class="jurusan-header">
                            <div>
                                <h3 class="jurusan-title">{{ $jurusan->nama_jurusan }}</h3>
                                <p style="font-size: 0.8125rem; color: var(--gray-600); margin-top: 0.25rem;">
                                    {{ $jurusan->deskripsi ?? 'Kompetensi keahlian kejuruan' }}
                                </p>
                            </div>
                            <span class="jurusan-badge badge-smk">SMK</span>
                        </div>

                        <div class="month-grid">
                            @foreach($bulanTampil as $bulan)
                                @php
                                    $data = $matriksKetersediaan[$jurusan->id][$bulan->id] ?? null;
                                    $status = $data['status'] ?? \App\Enums\StatusKetersediaanEnum::Tutup;
                                    $sisa = $data['sisa_kuota'] ?? 0;
                                    $isWaiting = $data['is_waiting_list'] ?? false;
                                    $buka = \Carbon\CarbonImmutable::parse($bulan->tgl_buka)->translatedFormat('d M Y');
                                @endphp
                                <div class="month-box">
                                    <div>
                                        <div class="month-box-title">
                                            Mulai: {{ \Carbon\CarbonImmutable::parse($bulan->bulan_mulai)->translatedFormat('F Y') }}
                                        </div>

                                        @if($status === \App\Enums\StatusKetersediaanEnum::Tersedia)
                                            @if($isWaiting)
                                                <div class="status-badge status-waiting-list">
                                                    Kuota Penuh (Waiting List)
                                                </div>
                                                <p class="quota-info">
                                                    Kuota terisi penuh.<br>
                                                    <span style="font-size: 0.75rem; color: #b45309;">Pendaftaran masuk antrean tunggu</span>
                                                </p>
                                            @else
                                                <div class="status-badge status-tersedia">
                                                    Tersedia
                                                </div>
                                                <p class="quota-info">
                                                    Sisa kuota: <span class="quota-count">{{ $sisa }}</span> kursi
                                                </p>
                                            @endif
                                        @elseif($status === \App\Enums\StatusKetersediaanEnum::Segera)
                                            <div class="status-badge status-segera">
                                                Segera Dibuka
                                            </div>
                                            <p class="quota-info">
                                                Buka pada: <strong>{{ $buka }}</strong>
                                            </p>
                                        @else
                                            <div class="status-badge status-tutup">
                                                Tutup
                                            </div>
                                            <p class="quota-info">
                                                Pendaftaran belum atau sudah ditutup.
                                            </p>
                                        @endif
                                    </div>

                                    <div>
                                        @if($status === \App\Enums\StatusKetersediaanEnum::Tersedia)
                                            <a href="{{ url('/register?bulan=' . $bulan->id . '&jurusan=' . $jurusan->id) }}" 
                                               class="btn btn-primary btn-sm">
                                                {{ $isWaiting ? 'Daftar Waiting List' : 'Daftar Sekarang' }}
                                            </a>
                                        @else
                                            <button type="button" class="btn btn-disabled btn-sm" disabled>
                                                Pendaftaran Ditutup
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p style="color: var(--gray-600); text-align: center; padding: 2rem;">Belum ada master jurusan SMK yang aktif.</p>
                @endforelse
            </div>

            <!-- Tab Content: Mahasiswa -->
            <div id="tab-content-mahasiswa" style="display: none;">
                @forelse($jurusanMahasiswa as $jurusan)
                    <div class="quota-card">
                        <div class="jurusan-header">
                            <div>
                                <h3 class="jurusan-title">{{ $jurusan->nama_jurusan }}</h3>
                                <p style="font-size: 0.8125rem; color: var(--gray-600); margin-top: 0.25rem;">
                                    {{ $jurusan->deskripsi ?? 'Program studi perguruan tinggi' }}
                                </p>
                            </div>
                            <span class="jurusan-badge badge-s1">
                                Mahasiswa
                            </span>
                        </div>

                        <div class="month-grid">
                            @foreach($bulanTampil as $bulan)
                                @php
                                    $data = $matriksKetersediaan[$jurusan->id][$bulan->id] ?? null;
                                    $status = $data['status'] ?? \App\Enums\StatusKetersediaanEnum::Tutup;
                                    $sisa = $data['sisa_kuota'] ?? 0;
                                    $isWaiting = $data['is_waiting_list'] ?? false;
                                    $buka = \Carbon\CarbonImmutable::parse($bulan->tgl_buka)->translatedFormat('d M Y');
                                @endphp
                                <div class="month-box">
                                    <div>
                                        <div class="month-box-title">
                                            Mulai: {{ \Carbon\CarbonImmutable::parse($bulan->bulan_mulai)->translatedFormat('F Y') }}
                                        </div>

                                        @if($status === \App\Enums\StatusKetersediaanEnum::Tersedia)
                                            @if($isWaiting)
                                                <div class="status-badge status-waiting-list">
                                                    Kuota Penuh (Waiting List)
                                                </div>
                                                <p class="quota-info">
                                                    Kuota terisi penuh.<br>
                                                    <span style="font-size: 0.75rem; color: #b45309;">Pendaftaran masuk antrean tunggu</span>
                                                </p>
                                            @else
                                                <div class="status-badge status-tersedia">
                                                    Tersedia
                                                </div>
                                                <p class="quota-info">
                                                    Sisa kuota: <span class="quota-count">{{ $sisa }}</span> kursi
                                                </p>
                                            @endif
                                        @elseif($status === \App\Enums\StatusKetersediaanEnum::Segera)
                                            <div class="status-badge status-segera">
                                                Segera Dibuka
                                            </div>
                                            <p class="quota-info">
                                                Buka pada: <strong>{{ $buka }}</strong>
                                            </p>
                                        @else
                                            <div class="status-badge status-tutup">
                                                Tutup
                                            </div>
                                            <p class="quota-info">
                                                Pendaftaran belum atau sudah ditutup.
                                            </p>
                                        @endif
                                    </div>

                                    <div>
                                        @if($status === \App\Enums\StatusKetersediaanEnum::Tersedia)
                                            <a href="{{ url('/register?bulan=' . $bulan->id . '&jurusan=' . $jurusan->id) }}" 
                                               class="btn btn-primary btn-sm">
                                                {{ $isWaiting ? 'Daftar Waiting List' : 'Daftar Sekarang' }}
                                            </a>
                                        @else
                                            <button type="button" class="btn btn-disabled btn-sm" disabled>
                                                Pendaftaran Ditutup
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p style="color: var(--gray-600); text-align: center; padding: 2rem;">Belum ada master jurusan Mahasiswa yang aktif.</p>
                @endforelse
            </div>
        @endif
    </div>
</section>

<!-- Alur Seleksi Section -->
<section id="alur-section" style="background: #ffffff; padding: 4.5rem 0; border-top: 1px solid var(--gray-200);">
    <div class="container">
        <div style="text-align: center; max-width: 640px; margin: 0 auto 3rem;">
            <h2 class="section-title">Alur Pendaftaran Magang</h2>
            <p class="section-subtitle">
                Proses seleksi transparan dan terukur dari awal registrasi hingga penerbitan surat penerimaan.
            </p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem;">
            <div class="step-card">
                <div class="step-number">1</div>
                <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.5rem;">Pilih Kuota & Registrasi</h3>
                <p style="font-size: 0.875rem; color: var(--gray-600); line-height: 1.5;">
                    Pilih periode bulan dan jurusan yang tersedia, lalu buat akun calon peserta serta verifikasi email.
                </p>
            </div>
            <div class="step-card">
                <div class="step-number">2</div>
                <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.5rem;">Lengkapi Biodata & Berkas</h3>
                <p style="font-size: 0.875rem; color: var(--gray-600); line-height: 1.5;">
                    Unggah surat pengantar resmi institusi, proposal magang, dan CV dalam format PDF aman.
                </p>
            </div>
            <div class="step-card">
                <div class="step-number">3</div>
                <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.5rem;">Verifikasi Administrasi</h3>
                <p style="font-size: 0.875rem; color: var(--gray-600); line-height: 1.5;">
                    Admin Kominfo memeriksa keabsahan dokumen dan mengalokasikan unit bidang penempatan.
                </p>
            </div>
            <div class="step-card">
                <div class="step-number">4</div>
                <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.5rem;">Mulai Masa Magang</h3>
                <p style="font-size: 0.875rem; color: var(--gray-600); line-height: 1.5;">
                    Dapatkan pembimbing lapangan, kerjakan tugas berkala, dan isi logbook aktivitas harian.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Persyaratan Dokumen Section -->
<section id="persyaratan-section" style="padding: 4.5rem 0;">
    <div class="container">
        <div style="text-align: center; max-width: 640px; margin: 0 auto 3rem;">
            <h2 class="section-title">Persyaratan Berkas Pendaftaran</h2>
            <p class="section-subtitle">
                Pastikan dokumen tersimpan dalam format PDF (maks. 5MB) sebelum memulai pengajuan pendaftaran.
            </p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 2rem;">
            <div style="background: #ffffff; padding: 2rem; border-radius: 1rem; border: 1px solid var(--gray-200);">
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
                    <span class="jurusan-badge badge-smk">Jalur SMK</span>
                    <h3 style="font-size: 1.15rem; font-weight: 700;">Dokumen Wajib Siswa SMK</h3>
                </div>
                <ul style="list-style: none; line-height: 2; font-size: 0.9rem; color: var(--gray-700);">
                    <li>✓ Surat Permohonan PKL/Magang dari Kepala Sekolah</li>
                    <li>✓ Curriculum Vitae (CV) / Portofolio keahlian</li>
                    <li>✓ Salinan Kartu Pelajar yang masih aktif</li>
                    <li>✓ Surat Persetujuan Orang Tua / Wali</li>
                </ul>
            </div>

            <div style="background: #ffffff; padding: 2rem; border-radius: 1rem; border: 1px solid var(--gray-200);">
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
                    <span class="jurusan-badge badge-s1">Jalur Mahasiswa</span>
                    <h3 style="font-size: 1.15rem; font-weight: 700;">Dokumen Wajib D3 / S1</h3>
                </div>
                <ul style="list-style: none; line-height: 2; font-size: 0.9rem; color: var(--gray-700);">
                    <li>✓ Surat Pengantar Magang resmi dari Fakultas / Dekanat</li>
                    <li>✓ Proposal Kegiatan Magang & Rencana Riset/Tugas Akhir</li>
                    <li>✓ Curriculum Vitae (CV) & Tautan Portofolio Proyek</li>
                    <li>✓ Salinan Kartu Tanda Mahasiswa (KTM) & Transkrip Nilai</li>
                </ul>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    function switchTab(type) {
        const btnSmk = document.getElementById('tab-btn-smk');
        const btnMahasiswa = document.getElementById('tab-btn-mahasiswa');
        const contentSmk = document.getElementById('tab-content-smk');
        const contentMahasiswa = document.getElementById('tab-content-mahasiswa');

        if (type === 'smk') {
            btnSmk.classList.add('active');
            btnMahasiswa.classList.remove('active');
            contentSmk.style.display = 'block';
            contentMahasiswa.style.display = 'none';
        } else {
            btnMahasiswa.classList.add('active');
            btnSmk.classList.remove('active');
            contentMahasiswa.style.display = 'block';
            contentSmk.style.display = 'none';
        }
    }
</script>
@endpush
