@extends('layouts.app')

@section('title', 'Magang Kominfo Sleman - Informasi Kuota & Pendaftaran')
@section('meta_description', 'Transparansi kuota pendaftaran magang Dinas Komunikasi dan Informatika untuk siswa SMK dan mahasiswa D3/S1.')
@section('og_title', 'Informasi Kuota Magang Dinas Kominfo - MagKom')
@section('og_description', 'Lihat ketersediaan kuota magang per jurusan secara langsung dan transparan.')

@push('styles')
<style>
    /* =====================================================================
       1. HERO SECTION (MATCHING FIGMA NODE 562-54996: 1440 Fill x 560)
       ===================================================================== */
    .hero-section {
        background: linear-gradient(rgba(15, 23, 42, 0.80), rgba(29, 37, 48, 0.90)), 
                    url('/images/hero_office_bg.png') center/cover no-repeat;
        color: #ffffff;
        padding: 5rem 0 4.5rem;
        position: relative;
    }

    .hero-grid {
        display: grid;
        grid-template-columns: 1.25fr 0.85fr;
        gap: 3.5rem;
        align-items: center;
    }

    /* Gelombang 2 Pill Badge */
    .hero-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.25);
        padding: 0.4rem 1rem;
        border-radius: 9999px;
        font-size: 0.8125rem;
        color: #ffffff;
        font-weight: 600;
        margin-bottom: 1.25rem;
    }

    .green-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #22c55e;
        display: inline-block;
    }

    .hero-title {
        font-size: clamp(2rem, 3.6vw, 2.75rem);
        font-weight: 700;
        line-height: 1.22;
        letter-spacing: -0.02em;
        margin-bottom: 1.25rem;
        color: #ffffff;
    }

    .hero-desc {
        font-size: 1.05rem;
        color: rgba(255, 255, 255, 0.88);
        line-height: 1.6;
        max-width: 500px;
        margin-bottom: 2rem;
    }

    .hero-cta-group {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    /* Hero Right Glass Card (Sisa kuota Gelombang 2) */
    .hero-glass-card {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 1rem;
        padding: 1.75rem 2rem;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.5);
    }

    .card-label-top {
        font-size: 0.8125rem;
        color: #cbd5e1;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .big-stat-row {
        display: flex;
        align-items: baseline;
        gap: 0.6rem;
        margin-bottom: 1rem;
    }

    .big-number {
        font-size: 3rem;
        font-weight: 800;
        color: #ffffff;
        line-height: 1;
    }

    .stat-total {
        font-size: 0.95rem;
        color: #cbd5e1;
        font-weight: 500;
    }

    /* Progress bar */
    .stat-progress-track {
        background: rgba(255, 255, 255, 0.2);
        height: 6px;
        border-radius: 9999px;
        width: 100%;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .stat-progress-fill {
        background: #38bdf8;
        height: 100%;
        border-radius: 9999px;
        width: 80%;
    }

    /* Table rows */
    .bidang-stat-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
    }

    .bidang-stat-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.875rem;
        color: #ffffff;
    }

    .bidang-stat-count {
        font-weight: 600;
        color: #cbd5e1;
    }

    .card-footer-note {
        font-size: 0.75rem;
        color: #94a3b8;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        padding-top: 0.85rem;
    }

    /* =====================================================================
       2. BIDANG DAN KUOTA SECTION (Figma + AC-01 Matrix)
       ===================================================================== */
    /* =====================================================================
       2. BIDANG DAN KUOTA SECTION (Figma + AC-01 Matrix)
       ===================================================================== */
    .section-bidang-kuota {
        padding: 5.5rem 0 5rem;
        background: var(--neutral-0);
    }

    .section-heading-center {
        text-align: center;
        margin-bottom: 2.75rem;
    }

    .section-heading-center h2 {
        font-size: 2.125rem;
        font-weight: 700;
        color: var(--neutral-800);
        margin-bottom: 0.5rem;
        letter-spacing: -0.02em;
    }

    .section-heading-center p {
        font-size: 1rem;
        color: var(--neutral-600);
    }

    /* Tab Switcher: Design System v1.0 */
    .tab-pills-wrap {
        display: flex;
        justify-content: center;
        gap: 0.75rem;
        margin-bottom: 3rem;
        flex-wrap: wrap;
    }

    .tab-pill-btn {
        background: var(--neutral-100);
        border: 1px solid var(--neutral-400);
        padding: 0.75rem 1.85rem;
        font-size: 0.9375rem;
        font-weight: 600;
        color: var(--neutral-600);
        border-radius: 9999px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .tab-pill-btn:hover {
        border-color: var(--blue-600);
        color: var(--blue-600);
    }

    .tab-pill-btn.active {
        background: var(--blue-600);
        color: #ffffff;
        border-color: var(--blue-600);
        box-shadow: 0 4px 14px rgba(7, 81, 255, 0.28);
    }

    /* Quota Grid */
    .quota-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 1.75rem;
        margin-bottom: 3.5rem;
    }

    .figma-quota-card {
        background: #ffffff;
        border: 1px solid var(--neutral-400);
        border-radius: 1rem;
        padding: 1.75rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.05);
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .figma-quota-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.09);
        border-color: var(--blue-600);
    }

    .card-top-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.25rem;
    }

    .card-icon-box {
        width: 3rem;
        height: 3rem;
        border-radius: 0.625rem;
        background: var(--blue-tint);
        border: 1px solid var(--blue-tint-line);
        color: var(--blue-600);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .card-icon-box svg {
        width: 1.5rem;
        height: 1.5rem;
        fill: currentColor;
    }

    .card-jenjang-badge {
        display: inline-block;
        padding: 0.3rem 0.85rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 700;
        background: var(--neutral-200);
        color: var(--neutral-700);
    }

    .card-jurusan-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--neutral-800);
        margin-bottom: 0.45rem;
        line-height: 1.3;
    }

    .card-jurusan-desc {
        font-size: 0.875rem;
        color: var(--neutral-600);
        margin-bottom: 1.25rem;
        line-height: 1.5;
        min-height: 42px;
    }

    /* Month Periods Inside Card */
    .card-period-box {
        background: var(--neutral-100);
        border: 1px solid var(--neutral-200);
        border-radius: 0.75rem;
        padding: 1.15rem;
        margin-bottom: 0.875rem;
    }

    .period-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--neutral-700);
        margin-bottom: 0.6rem;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.35rem 0.85rem;
        border-radius: 9999px;
        font-size: 0.8rem;
        font-weight: 700;
        margin-bottom: 0.65rem;
    }

    .status-tersedia {
        background: var(--green-100);
        color: var(--green-700);
        border: 1px solid var(--green-600);
    }

    .status-waiting-list {
        background: var(--blue-tint);
        color: var(--blue-700);
        border: 1px solid var(--blue-tint-line);
    }

    .status-segera {
        background: var(--blue-tint);
        color: var(--blue-700);
        border: 1px solid var(--blue-tint-line);
    }

    .status-tutup {
        background: var(--neutral-200);
        color: var(--neutral-600);
        border: 1px solid var(--neutral-400);
    }

    .quota-info {
        font-size: 0.825rem;
        color: var(--neutral-600);
        margin-bottom: 0.85rem;
        line-height: 1.4;
    }

    .quota-count {
        font-weight: 700;
        color: var(--neutral-800);
    }

    .btn-sm {
        padding: 0.55rem 1rem;
        font-size: 0.85rem;
        width: 100%;
        text-align: center;
        border-radius: 0.5rem;
    }

    .btn-disabled {
        background: var(--neutral-200);
        color: var(--neutral-500);
        border: 1px solid var(--neutral-400);
        cursor: not-allowed;
    }

    /* =====================================================================
       3. ALUR MAGANG SECTION (MATCHING FIGMA NODE 562-55102: 1440 Fill x 530)
       ===================================================================== */
    .section-alur-magang {
        background: linear-gradient(rgba(7, 26, 51, 0.88), rgba(11, 26, 48, 0.94)), 
                    url('/images/hero_office_bg.png') center/cover no-repeat;
        color: #ffffff;
        padding: 5.5rem 0 5rem;
        text-align: center;
        position: relative;
    }

    .alur-heading h2 {
        font-size: 2.125rem;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 0.5rem;
        letter-spacing: -0.02em;
    }

    .alur-heading p {
        font-size: 1rem;
        color: #cbd5e1;
        margin-bottom: 3.5rem;
    }

    .alur-steps-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
        margin-bottom: 3rem;
        position: relative;
    }

    .alur-step-card {
        background: rgba(255, 255, 255, 0.07);
        border: 1px solid rgba(255, 255, 255, 0.16);
        border-radius: 0.875rem;
        padding: 1.75rem 1.4rem;
        text-align: left;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.35);
        transition: transform 0.2s ease, border-color 0.2s ease, background 0.2s ease;
    }

    .alur-step-card:hover {
        transform: translateY(-3px);
        background: rgba(255, 255, 255, 0.11);
        border-color: rgba(56, 189, 248, 0.5);
    }

    .alur-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.25rem;
    }

    .alur-icon-wrap {
        width: 2.5rem;
        height: 2.5rem;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #38bdf8;
    }

    .alur-icon-wrap svg {
        width: 1.35rem;
        height: 1.35rem;
    }

    .alur-step-number {
        font-size: 1.5rem;
        font-weight: 800;
        color: #38bdf8;
        line-height: 1;
    }

    .alur-step-card h3 {
        font-size: 1.15rem;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 0.45rem;
    }

    .alur-step-card p {
        font-size: 0.85rem;
        color: #cbd5e1;
        line-height: 1.55;
    }

    .btn-panduan {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.25);
        color: #ffffff;
        padding: 0.75rem 2rem;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.925rem;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-panduan:hover {
        background: rgba(255, 255, 255, 0.18);
        border-color: rgba(255, 255, 255, 0.45);
        color: #ffffff;
    }

    /* =====================================================================
       4. PENGUMUMAN SECTION (MATCHING FIGMA NODE 562-55160)
       ===================================================================== */
    .section-pengumuman {
        padding: 5.5rem 0 5rem;
        background: var(--neutral-0);
    }

    .pengumuman-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.75rem;
    }

    .pengumuman-card {
        background: #ffffff;
        border: 1px solid var(--neutral-400);
        border-radius: 0.875rem;
        padding: 1.75rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .pengumuman-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
        border-color: var(--blue-600);
    }

    .pengumuman-card h3 {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--neutral-800);
        margin-bottom: 0.65rem;
        line-height: 1.35;
    }

    .pengumuman-card p {
        font-size: 0.875rem;
        color: var(--neutral-600);
        line-height: 1.5;
        margin-bottom: 1.5rem;
    }

    .pengumuman-date {
        color: var(--blue-600);
        font-size: 0.825rem;
        font-weight: 700;
    }

    /* =====================================================================
       5. LAYANAN SECTION (MATCHING FIGMA NODE 562-55179)
       ===================================================================== */
    .section-layanan {
        padding: 5.5rem 0 5rem;
        background: var(--neutral-100);
    }

    .layanan-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
    }

    .layanan-card {
        background: #ffffff;
        border: 1px solid var(--neutral-400);
        border-radius: 0.875rem;
        padding: 2.25rem 1.5rem;
        text-align: center;
        text-decoration: none;
        color: var(--neutral-800);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1.15rem;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .layanan-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08);
        border-color: var(--blue-600);
    }

    .layanan-icon-box {
        width: 3.5rem;
        height: 3.5rem;
        border-radius: 0.75rem;
        background: var(--blue-tint);
        color: var(--blue-600);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .layanan-icon-box svg {
        width: 1.75rem;
        height: 1.75rem;
    }

    .layanan-card span {
        font-size: 1rem;
        font-weight: 700;
        color: var(--neutral-800);
    }

    @media (max-width: 992px) {
        .hero-grid {
            grid-template-columns: 1fr;
            gap: 2.5rem;
        }
        .alur-steps-row {
            grid-template-columns: 1fr 1fr;
        }
        .pengumuman-grid {
            grid-template-columns: 1fr;
        }
        .layanan-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 640px) {
        .alur-steps-row {
            grid-template-columns: 1fr;
        }
        .layanan-grid {
            grid-template-columns: 1fr 1fr;
        }
    }
</style>
@endpush

@section('content')
<!-- =====================================================================
     1. HERO SECTION (Figma node 562-54996)
     ===================================================================== -->
<section class="hero-section">
    <div class="container">
        <div class="hero-grid">
            <!-- Left Hero Content -->
            <div>
                <div class="hero-pill">
                    <span class="green-dot"></span>
                    <span>Gelombang 2 dibuka · 1–15 Okt 2026</span>
                </div>
                <h1 class="hero-title">
                    Magang di Dinas Komunikasi dan Informatika Kabupaten Sleman
                </h1>
                <p class="hero-desc">
                    Daftar, pantau status, kerjakan tugas, dan unduh lembar nilai magangmu dalam satu portal.
                </p>
                <div class="hero-cta-group">
                    <a href="#kuota-section" class="btn btn-white">Daftar sekarang</a>
                    <a href="#kuota-section" class="btn btn-ghost-white">Lihat kuota</a>
                </div>
            </div>

            <!-- Right Hero Glass Card (Sisa Kuota Gelombang 2) -->
            <div>
                <div class="hero-glass-card">
                    <div class="card-label-top">Sisa kuota Gelombang 2</div>
                    <div class="big-stat-row">
                        <span class="big-number">24</span>
                        <span class="stat-total">dari 30 tempat</span>
                    </div>

                    <div class="stat-progress-track">
                        <div class="stat-progress-fill"></div>
                    </div>

                    <div class="bidang-stat-list">
                        <div class="bidang-stat-item">
                            <span>Aplikasi Informatika</span>
                            <span class="bidang-stat-count">8 dari 10</span>
                        </div>
                        <div class="bidang-stat-item">
                            <span>Statistik</span>
                            <span class="bidang-stat-count">6 dari 8</span>
                        </div>
                        <div class="bidang-stat-item">
                            <span>Persandian</span>
                            <span class="bidang-stat-count">5 dari 6</span>
                        </div>
                        <div class="bidang-stat-item">
                            <span>Sekretariat</span>
                            <span class="bidang-stat-count">5 dari 6</span>
                        </div>
                    </div>

                    <div class="card-footer-note">
                        Pendaftaran ditutup 15 Okt 2026 · magang mulai 2 Nov 2026
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =====================================================================
     2. BIDANG DAN KUOTA SECTION (Figma + AC-01 Matrix)
     ===================================================================== -->
<section id="kuota-section" class="section-bidang-kuota">
    <div class="container">
        <div class="section-heading-center">
            <h2>Bidang dan kuota</h2>
            <p>Gelombang 2 · pendaftaran 1–15 Okt 2026 · magang mulai 2 Nov 2026</p>
        </div>

        @if($bulanTampil->isEmpty())
            <div style="text-align: center; background: #ffffff; padding: 3rem; border-radius: 1rem; border: 1px dashed var(--gray-300);">
                <p style="color: var(--gray-600); font-size: 1rem;">
                    Saat ini belum ada periode pendaftaran magang yang dibuka untuk publik. Silakan cek kembali secara berkala.
                </p>
            </div>
        @else
            <!-- Tab Switcher SMK vs Mahasiswa (AC-01-1) -->
            <div class="tab-pills-wrap">
                <button type="button" class="tab-pill-btn active" id="tab-btn-smk" onclick="switchTab('smk')">
                    Jalur SMK (Sekolah Menengah Kejuruan)
                </button>
                <button type="button" class="tab-pill-btn" id="tab-btn-mahasiswa" onclick="switchTab('mahasiswa')">
                    Jalur Mahasiswa (D3 / S1)
                </button>
            </div>

            <!-- Tab Content: SMK -->
            <div id="tab-content-smk">
                <div class="quota-grid">
                    @forelse($jurusanSmk as $jurusan)
                        <div class="figma-quota-card">
                            <div>
                                <div class="card-top-row">
                                    <div class="card-icon-box">
                                        <svg viewBox="0 0 24 24"><path d="M20 18c1.1 0 1.99-.9 1.99-2L22 6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2H0v2h24v-2h-4zM4 6h16v10H4V6z"/></svg>
                                    </div>
                                    <span class="card-jenjang-badge">SMK</span>
                                </div>
                                <h3 class="card-jurusan-title">{{ $jurusan->nama_jurusan }}</h3>
                                <p class="card-jurusan-desc">{{ $jurusan->deskripsi ?? 'Kompetensi keahlian kejuruan tingkat menengah' }}</p>

                                <!-- Months list -->
                                @foreach($bulanTampil as $bulan)
                                    @php
                                        $data = $matriksKetersediaan[$jurusan->id][$bulan->id] ?? null;
                                        $status = $data['status'] ?? \App\Enums\StatusKetersediaanEnum::Tutup;
                                        $sisa = $data['sisa_kuota'] ?? 0;
                                        $isWaiting = $data['is_waiting_list'] ?? false;
                                        $buka = \Carbon\CarbonImmutable::parse($bulan->tgl_buka)->translatedFormat('d M Y');
                                    @endphp
                                    <div class="card-period-box">
                                        <div class="period-title">Mulai: {{ \Carbon\CarbonImmutable::parse($bulan->bulan_mulai)->translatedFormat('F Y') }}</div>
                                        @if($status === \App\Enums\StatusKetersediaanEnum::Tersedia)
                                            @if($isWaiting)
                                                <div class="status-badge status-waiting-list">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                                    <span>Kuota Penuh (Waiting List)</span>
                                                </div>
                                                <p class="quota-info">
                                                    Kuota terisi penuh.<br>
                                                    <span style="font-size: 0.75rem; color: #b45309;">Pendaftaran masuk antrean tunggu</span>
                                                </p>
                                            @else
                                                <div class="status-badge status-tersedia">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                                    <span>Tersedia</span>
                                                </div>
                                                <p class="quota-info">
                                                    Sisa kuota: <span class="quota-count">{{ $sisa }}</span> kursi
                                                </p>
                                            @endif
                                        @elseif($status === \App\Enums\StatusKetersediaanEnum::Segera)
                                            <div class="status-badge status-segera">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                                <span>Segera Dibuka</span>
                                            </div>
                                            <p class="quota-info">
                                                Buka pada: <strong>{{ $buka }}</strong>
                                            </p>
                                        @else
                                            <div class="status-badge status-tutup">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                                <span>Tutup</span>
                                            </div>
                                            <p class="quota-info">
                                                Pendaftaran belum atau sudah ditutup.
                                            </p>
                                        @endif

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
                        <p style="color: var(--gray-600); text-align: center; grid-column: 1/-1;">Belum ada master jurusan SMK yang aktif.</p>
                    @endforelse
                </div>
            </div>

            <!-- Tab Content: Mahasiswa -->
            <div id="tab-content-mahasiswa" style="display: none;">
                <div class="quota-grid">
                    @forelse($jurusanMahasiswa as $jurusan)
                        <div class="figma-quota-card">
                            <div>
                                <div class="card-top-row">
                                    <div class="card-icon-box">
                                        <svg viewBox="0 0 24 24"><path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3z"/></svg>
                                    </div>
                                    <span class="card-jenjang-badge">D3 · S1</span>
                                </div>
                                <h3 class="card-jurusan-title">{{ $jurusan->nama_jurusan }}</h3>
                                <p class="card-jurusan-desc">{{ $jurusan->deskripsi ?? 'Program studi perguruan tinggi (D3 / D4 / S1)' }}</p>

                                <!-- Months list -->
                                @foreach($bulanTampil as $bulan)
                                    @php
                                        $data = $matriksKetersediaan[$jurusan->id][$bulan->id] ?? null;
                                        $status = $data['status'] ?? \App\Enums\StatusKetersediaanEnum::Tutup;
                                        $sisa = $data['sisa_kuota'] ?? 0;
                                        $isWaiting = $data['is_waiting_list'] ?? false;
                                        $buka = \Carbon\CarbonImmutable::parse($bulan->tgl_buka)->translatedFormat('d M Y');
                                    @endphp
                                    <div class="card-period-box">
                                        <div class="period-title">Mulai: {{ \Carbon\CarbonImmutable::parse($bulan->bulan_mulai)->translatedFormat('F Y') }}</div>
                                        @if($status === \App\Enums\StatusKetersediaanEnum::Tersedia)
                                            @if($isWaiting)
                                                <div class="status-badge status-waiting-list">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                                    <span>Kuota Penuh (Waiting List)</span>
                                                </div>
                                                <p class="quota-info">
                                                    Kuota terisi penuh.<br>
                                                    <span style="font-size: 0.75rem; color: #b45309;">Pendaftaran masuk antrean tunggu</span>
                                                </p>
                                            @else
                                                <div class="status-badge status-tersedia">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                                    <span>Tersedia</span>
                                                </div>
                                                <p class="quota-info">
                                                    Sisa kuota: <span class="quota-count">{{ $sisa }}</span> kursi
                                                </p>
                                            @endif
                                        @elseif($status === \App\Enums\StatusKetersediaanEnum::Segera)
                                            <div class="status-badge status-segera">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                                <span>Segera Dibuka</span>
                                            </div>
                                            <p class="quota-info">
                                                Buka pada: <strong>{{ $buka }}</strong>
                                            </p>
                                        @else
                                            <div class="status-badge status-tutup">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                                <span>Tutup</span>
                                            </div>
                                            <p class="quota-info">
                                                Pendaftaran belum atau sudah ditutup.
                                            </p>
                                        @endif

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
                        <p style="color: var(--gray-600); text-align: center; grid-column: 1/-1;">Belum ada master jurusan Mahasiswa yang aktif.</p>
                    @endforelse
                </div>
            </div>

            <!-- Button Daftar ke bidang pilihan -->
            <div style="text-align: center;">
                <a href="#kuota-section" class="btn btn-primary" style="padding: 0.75rem 2.25rem; font-size: 0.95rem;">
                    Daftar ke bidang pilihan
                </a>
            </div>
        @endif
    </div>
</section>

<!-- =====================================================================
     3. ALUR MAGANG SECTION (MATCHING FIGMA NODE 562-55102: 1440 Fill x 530)
     ===================================================================== -->
<section id="alur-section" class="section-alur-magang">
    <div class="container">
        <div class="alur-heading">
            <h2>Alur magang</h2>
            <p>Empat langkah dari pendaftaran sampai lembar nilai.</p>
        </div>

        <div class="alur-steps-row">
            <!-- Step 01 -->
            <div class="alur-step-card">
                <div>
                    <div class="alur-card-top">
                        <div class="alur-icon-wrap">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                        </div>
                        <span class="alur-step-number">01</span>
                    </div>
                    <h3>Buat Akun</h3>
                    <p>Isi profil dan pilih bidang sesuai jurusanmu.</p>
                </div>
            </div>

            <!-- Step 02 -->
            <div class="alur-step-card">
                <div>
                    <div class="alur-card-top">
                        <div class="alur-icon-wrap">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                        </div>
                        <span class="alur-step-number">02</span>
                    </div>
                    <h3>Kirim Berkas</h3>
                    <p>Unggah surat pengantar, CV, dan transkrip. Admin memutuskan hasilnya.</p>
                </div>
            </div>

            <!-- Step 03 -->
            <div class="alur-step-card">
                <div>
                    <div class="alur-card-top">
                        <div class="alur-icon-wrap">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/><path d="m9 14 2 2 4-4"/></svg>
                        </div>
                        <span class="alur-step-number">03</span>
                    </div>
                    <h3>Kerjakan Tugas</h3>
                    <p>Terima tugas, unggah progres, dan isi logbook harian.</p>
                </div>
            </div>

            <!-- Step 04 -->
            <div class="alur-step-card">
                <div>
                    <div class="alur-card-top">
                        <div class="alur-icon-wrap">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>
                        </div>
                        <span class="alur-step-number">04</span>
                    </div>
                    <h3>Terima Nilai</h3>
                    <p>Unduh Lembar Nilai Magang (PDF) dari Pembimbing.</p>
                </div>
            </div>
        </div>

        <div>
            <a href="#alur-section" class="btn-panduan">Lihat panduan lengkap</a>
        </div>
    </div>
</section>

<!-- =====================================================================
     4. PENGUMUMAN SECTION (MATCHING FIGMA NODE 562-55160)
     ===================================================================== -->
<section id="pengumuman-section" class="section-pengumuman">
    <div class="container">
        <div class="section-heading-center">
            <h2>Pengumuman</h2>
        </div>

        <div class="pengumuman-grid">
            <div class="pengumuman-card">
                <div>
                    <h3>Pendaftaran magang gelombang 2 resmi dibuka</h3>
                    <p>Pendaftaran berlangsung 1 sampai 15 Oktober 2026 untuk semua bidang.</p>
                </div>
                <div class="pengumuman-date">28 Sep 2026</div>
            </div>

            <div class="pengumuman-card">
                <div>
                    <h3>Persyaratan berkas pendaftaran</h3>
                    <p>Siapkan surat pengantar institusi, CV, dan transkrip dalam format PDF.</p>
                </div>
                <div class="pengumuman-date">20 Sep 2026</div>
            </div>

            <div class="pengumuman-card">
                <div>
                    <h3>Hasil seleksi gelombang 1</h3>
                    <p>Peserta yang diterima dapat masuk ke dashboard dan melihat penempatan.</p>
                </div>
                <div class="pengumuman-date">12 Sep 2026</div>
            </div>
        </div>
    </div>
</section>

<!-- =====================================================================
     5. LAYANAN SECTION (MATCHING FIGMA NODE 562-55179)
     ===================================================================== -->
<section id="layanan-section" class="section-layanan">
    <div class="container">
        <div class="section-heading-center">
            <h2>Layanan</h2>
        </div>

        <div class="layanan-grid">
            <a href="#kuota-section" class="layanan-card">
                <div class="layanan-icon-box">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="12" y1="18" x2="12" y2="12"/>
                        <line x1="9" y1="15" x2="15" y2="15"/>
                    </svg>
                </div>
                <span>Daftar Magang</span>
            </a>

            <a href="{{ route('login') }}" class="layanan-card">
                <div class="layanan-icon-box">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                </div>
                <span>Cek Status</span>
            </a>

            <a href="{{ route('login') }}" class="layanan-card">
                <div class="layanan-icon-box">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                    </svg>
                </div>
                <span>Logbook</span>
            </a>

            <a href="{{ route('login') }}" class="layanan-card">
                <div class="layanan-icon-box">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="8" r="6"/>
                        <path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/>
                    </svg>
                </div>
                <span>Lembar Nilai</span>
            </a>
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

        if (!btnSmk || !btnMahasiswa || !contentSmk || !contentMahasiswa) return;

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
