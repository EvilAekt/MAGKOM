@extends('layouts.peserta')

@section('title', 'Dashboard Peserta')
@section('page_title', 'Dashboard Peserta')

@push('styles')
<style>
    /* Dashboard Hero Banner */
    .dashboard-hero {
        background: linear-gradient(95deg, rgba(6, 65, 204, 0.96) 0%, rgba(6, 65, 204, 0.88) 45%, rgba(6, 65, 204, 0.35) 100%),
                    url('{{ asset('images/dashboard_hero_bg.jpg') }}') right center / cover no-repeat;
        border-radius: var(--radius-lg);
        padding: 34px 36px;
        color: #ffffff;
        margin-bottom: 28px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 6px 20px rgba(6, 65, 204, 0.16);
        min-height: 185px;
    }

    .dashboard-hero::after {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 85% 50%, rgba(255, 255, 255, 0.08) 0%, transparent 60%);
        pointer-events: none;
    }

    .hero-content {
        position: relative;
        z-index: 2;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .hero-greeting h1 {
        font-size: var(--text-3xl);
        font-weight: var(--fw-bold);
        letter-spacing: -0.01em;
        margin-bottom: 6px;
        line-height: 1.25;
    }

    .hero-greeting p {
        font-size: var(--text-base);
        color: rgba(255, 255, 255, 0.9);
        max-width: 600px;
        line-height: 1.5;
    }

    .hero-meta {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 8px;
    }

    .hero-date-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(8px);
        padding: 6px 14px;
        border-radius: var(--radius-full);
        font-size: var(--text-sm);
        font-weight: var(--fw-semibold);
        border: 1px solid rgba(255, 255, 255, 0.25);
    }

    .hero-role-pill {
        display: inline-block;
        padding: 4px 12px;
        background: #ffffff;
        color: var(--blue-700);
        border-radius: var(--radius-full);
        font-size: var(--text-2xs);
        font-weight: var(--fw-bold);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* KPI Grid Cards */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 28px;
    }

    .kpi-card {
        background: var(--neutral-0);
        border: 1px solid var(--neutral-400);
        border-radius: var(--radius-md);
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.08);
    }

    .kpi-icon-box {
        width: 48px;
        height: 48px;
        border-radius: var(--radius-default);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .kpi-icon-blue { background: var(--blue-tint); color: var(--blue-600); }
    .kpi-icon-green { background: var(--green-100); color: var(--green-600); }
    .kpi-icon-amber { background: var(--amber-100); color: var(--amber-700); }
    .kpi-icon-purple { background: #F3E8FF; color: #7E22CE; }

    .kpi-details {
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .kpi-label {
        font-size: var(--text-xs);
        font-weight: var(--fw-semibold);
        color: var(--neutral-600);
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 4px;
    }

    .kpi-value {
        font-size: var(--text-2xl);
        font-weight: var(--fw-bold);
        color: var(--neutral-800);
        line-height: 1.2;
    }

    .kpi-subtext {
        font-size: var(--text-xs);
        color: var(--neutral-500);
        margin-top: 2px;
    }

    /* Onboarding Card */
    .onboarding-card {
        background: var(--neutral-0);
        border: 1px solid var(--blue-tint-line);
        border-radius: var(--radius-md);
        padding: 28px;
        margin-bottom: 28px;
        box-shadow: 0 2px 8px rgba(7, 81, 255, 0.06);
    }

    .stepper-list {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin: 24px 0 20px 0;
    }

    .step-item {
        background: var(--neutral-100);
        border: 1px solid var(--neutral-200);
        border-radius: var(--radius-default);
        padding: 18px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        position: relative;
    }

    .step-item.completed {
        background: #F0FDF4;
        border-color: #BBF7D0;
    }

    .step-item.active {
        background: #EFF6FF;
        border-color: #BFDBFE;
    }

    .step-badge {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: var(--text-xs);
        font-weight: var(--fw-bold);
        background: var(--neutral-400);
        color: #ffffff;
    }

    .step-item.completed .step-badge { background: var(--green-600); }
    .step-item.active .step-badge { background: var(--blue-600); }

    .step-title {
        font-size: var(--text-base);
        font-weight: var(--fw-semibold);
        color: var(--neutral-800);
        line-height: 1.3;
    }

    .step-desc {
        font-size: var(--text-xs);
        color: var(--neutral-600);
        line-height: 1.4;
    }

    /* Phase Selection Tracker */
    .status-tracker-card {
        background: var(--neutral-0);
        border: 1px solid var(--neutral-400);
        border-radius: var(--radius-md);
        padding: 24px;
        margin-bottom: 28px;
    }

    .tracker-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .info-strip {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        background: var(--neutral-100);
        padding: 16px;
        border-radius: var(--radius-default);
        margin-bottom: 16px;
    }

    .info-strip-item span {
        font-size: var(--text-xs);
        color: var(--neutral-500);
        display: block;
    }

    .info-strip-item strong {
        font-size: var(--text-base);
        color: var(--neutral-800);
        font-weight: var(--fw-semibold);
    }

    /* Two Column Content Grid */
    .dashboard-columns {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 28px;
    }

    .list-card {
        background: var(--neutral-0);
        border: 1px solid var(--neutral-400);
        border-radius: var(--radius-md);
        padding: 20px;
        display: flex;
        flex-direction: column;
    }

    .list-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--neutral-200);
    }

    .list-card-title {
        font-size: var(--text-lg);
        font-weight: var(--fw-bold);
        color: var(--neutral-800);
        display: flex;
        align-items: center;
        gap: 8px;
        line-height: 1.3;
    }

    .item-row {
        padding: 12px 0;
        border-bottom: 1px solid var(--neutral-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
    }

    .item-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .item-meta h4 {
        font-size: var(--text-base);
        font-weight: var(--fw-semibold);
        color: var(--neutral-800);
        margin-bottom: 4px;
        line-height: 1.3;
    }

    .item-meta p {
        font-size: var(--text-xs);
        color: var(--neutral-500);
        line-height: 1.4;
    }

    /* Quick Action Buttons */
    .quick-actions-bar {
        display: flex;
        gap: 12px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .kpi-grid { grid-template-columns: repeat(2, 1fr); }
        .dashboard-columns { grid-template-columns: 1fr; }
        .stepper-list { grid-template-columns: 1fr; }
        .hero-meta { align-items: flex-start; }
    }

    @media (max-width: 640px) {
        .kpi-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

{{-- 1. Hero Greeting Banner --}}
<div class="dashboard-hero">
    <div class="hero-content">
        <div class="hero-greeting">
            <h1>Selamat Datang, {{ $user->nama ?? $user->name }}! 👋</h1>
            <p>
                Selamat datang di portal terpadu magang Dinas Komunikasi dan Informatika Kabupaten Sleman. Pantau status pendaftaran, kegiatan harian, tugas, dan evaluasi hasil kerja Anda secara terpusat.
            </p>
        </div>
        <div class="hero-meta">
            <div class="hero-date-badge">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span>{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</span>
            </div>
            <span class="hero-role-pill">Peran: Peserta Magang</span>
        </div>
    </div>
</div>

{{-- 2. Modul Berdasarkan Fase --}}

{{-- FASE 1: ONBOARDING (Belum Mendaftar / Profil Baru) --}}
@if($fase === 'onboarding')
    <div class="onboarding-card">
        <div class="card-header" style="margin-bottom: 8px;">
            <div>
                <h3 class="card-title">Panduan Alur Pendaftaran Magang</h3>
                <p class="card-subtitle">Ikuti langkah-langkah di bawah ini untuk memulai pendaftaran magang di Dinas Kominfo Sleman.</p>
            </div>
        </div>

        <div class="stepper-list">
            <div class="step-item {{ $profil ? 'completed' : 'active' }}">
                <div class="step-badge">{{ $profil ? '✓' : '1' }}</div>
                <div class="step-title">1. Lengkapi Biodata & Profil</div>
                <div class="step-desc">
                    Isi asal institusi/sekolah, NISN/NIM, jurusan, jenjang, kontak WhatsApp, dan kontak darurat.
                </div>
            </div>

            <div class="step-item {{ $profil ? 'active' : '' }}">
                <div class="step-badge">2</div>
                <div class="step-title">2. Pilih Periode & Kuota</div>
                <div class="step-desc">
                    Pilih bulan magang aktif yang kuotanya masih tersedia sesuai jurusan Anda dan buat draf pengajuan.
                </div>
            </div>

            <div class="step-item">
                <div class="step-badge">3</div>
                <div class="step-title">3. Unggah Berkas & Ajukan</div>
                <div class="step-desc">
                    Unggah surat pengantar, proposal, transkrip nilai, lalu kirim pengajuan untuk diverifikasi admin.
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            @if(!$profil)
                <a href="{{ route('peserta.profil.show') }}" class="btn btn-primary">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Lengkapi Biodata Sekarang
                </a>
            @else
                <a href="{{ route('peserta.pendaftaran.index') }}" class="btn btn-primary">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Buat Pengajuan Pendaftaran Magang
                </a>
                <a href="{{ route('peserta.profil.show') }}" class="btn btn-secondary">
                    Periksa Biodata Saya
                </a>
            @endif
        </div>
    </div>
@endif

{{-- FASE 2: SELEKSI PENDAFTARAN (Pendaftaran Aktif Berjalan) --}}
@if($fase === 'seleksi' && $pendaftaran)
    <div class="status-tracker-card">
        <div class="tracker-header">
            <div>
                <h3 class="card-title">Status Pendaftaran Magang Aktif</h3>
                <p class="card-subtitle">Pengajuan nomor #{{ $pendaftaran->id }} sedang diproses dalam tahapan seleksi.</p>
            </div>
            <div>
                @php
                    $status = $pendaftaran->status;
                    $badgeClass = match($status) {
                        \App\Enums\StatusPendaftaranEnum::Diterima => 'badge-success',
                        \App\Enums\StatusPendaftaranEnum::Diajukan, \App\Enums\StatusPendaftaranEnum::WaitingList => 'badge-warning',
                        \App\Enums\StatusPendaftaranEnum::PerluPerbaikan, \App\Enums\StatusPendaftaranEnum::Ditolak, \App\Enums\StatusPendaftaranEnum::Dibatalkan => 'badge-danger',
                        default => 'badge-neutral',
                    };
                @endphp
                <span class="badge {{ $badgeClass }}" style="font-size: 13px; padding: 6px 14px;">
                    ● {{ $status->label() }}
                </span>
            </div>
        </div>

        <div class="info-strip">
            <div class="info-strip-item">
                <span>Periode Magang:</span>
                <strong>{{ $pendaftaran->bulanMagang ? \Carbon\Carbon::parse($pendaftaran->bulanMagang->bulan_mulai)->isoFormat('MMMM Y') : '-' }}</strong>
            </div>
            <div class="info-strip-item">
                <span>Jurusan Terdaftar:</span>
                <strong>{{ $pendaftaran->kuota->jurusan->nama_jurusan ?? '-' }}</strong>
            </div>
            <div class="info-strip-item">
                <span>Durasi:</span>
                <strong>{{ $pendaftaran->durasi_bulan }} Bulan</strong>
            </div>
            <div class="info-strip-item">
                <span>Kelengkapan Berkas:</span>
                <strong>{{ $pendaftaran->dokumen->count() }} Berkas Terunggah</strong>
            </div>
        </div>

        @if($pendaftaran->catatan_revisi)
            <div class="alert alert-warning" style="margin-bottom: 16px;">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div>
                    <strong>Catatan Admin Verifikator:</strong> {{ $pendaftaran->catatan_revisi }}
                </div>
            </div>
        @endif

        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="{{ route('peserta.pendaftaran.detail', $pendaftaran) }}" class="btn btn-primary">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-1.943-9.542-7z"></path>
                </svg>
                Buka Berkas & Detail Pendaftaran
            </a>

            @if($pendaftaran->penempatan)
                <a href="{{ route('peserta.penempatan.surat-diterima', $pendaftaran->penempatan) }}" class="btn btn-secondary" target="_blank">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Unduh Surat Keterangan Diterima (PDF)
                </a>
            @endif
        </div>
    </div>
@endif

{{-- FASE 3: AKTIF MAGANG (Sedang Berjalan) --}}
@if($fase === 'aktif' && $penempatan)
    {{-- Banner Penempatan Aktif --}}
    <div class="card" style="border-left: 4px solid var(--blue-600); margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <div>
                <span class="badge badge-success" style="margin-bottom: 8px;">● Status Penempatan: Magang Aktif</span>
                <h3 style="font-size: 18px; font-weight: 700; color: var(--neutral-800);">
                    {{ $penempatan->bidang->nama_bidang ?? 'Dinas Komunikasi dan Informatika Kabupaten Sleman' }}
                </h3>
                <p style="font-size: 13px; color: var(--neutral-600); margin-top: 4px;">
                    Pembimbing Lapangan: <strong>{{ $penempatan->pembimbing->nama ?? $penempatan->pembimbing->name ?? 'Belum Ditugaskan' }}</strong> 
                    &bull; Periode: {{ \Carbon\Carbon::parse($penempatan->tgl_mulai ?? $penempatan->tanggal_mulai)->isoFormat('D MMM Y') }} s/d {{ \Carbon\Carbon::parse($penempatan->tgl_selesai ?? $penempatan->tanggal_selesai)->isoFormat('D MMM Y') }}
                </p>
            </div>
            <div>
                <a href="{{ route('peserta.penempatan.surat-diterima.pratinjau', $penempatan) }}" class="btn btn-secondary" target="_blank">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-1.943-9.542-7z"></path>
                    </svg>
                    Pratinjau SK Diterima
                </a>
            </div>
        </div>
    </div>

    {{-- Grid 4 KPI --}}
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-icon-box kpi-icon-blue">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
            </div>
            <div class="kpi-details">
                <span class="kpi-label">Tugas Selesai</span>
                <span class="kpi-value">{{ $tugasSelesai }} / {{ $totalTugas }}</span>
                <span class="kpi-subtext">{{ $tugasBelum }} tugas dalam proses</span>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon-box kpi-icon-green">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <div class="kpi-details">
                <span class="kpi-label">Logbook Mingguan</span>
                <span class="kpi-value">{{ $totalLogbook }} Laporan</span>
                <span class="kpi-subtext">
                    @if($logbookPerluRevisi > 0)
                        <strong style="color: var(--red-600);">{{ $logbookPerluRevisi }} perlu revisi</strong>
                    @else
                        Aktivitas tercatat
                    @endif
                </span>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon-box kpi-icon-amber">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="kpi-details">
                <span class="kpi-label">Sisa Hari Magang</span>
                <span class="kpi-value">{{ $sisaHari !== null ? $sisaHari . ' Hari' : '-' }}</span>
                <span class="kpi-subtext">Hingga akhir periode</span>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon-box kpi-icon-purple">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                </svg>
            </div>
            <div class="kpi-details">
                <span class="kpi-label">Status Evaluasi</span>
                <span class="kpi-value" style="font-size: 16px;">
                    {{ $isFinalNilai ? ($penilaian->predikat ?? 'A') . ' (' . number_format($penilaian->nilai_akhir, 1) . ')' : 'Dalam Proses' }}
                </span>
                <span class="kpi-subtext">Rubrik pembimbing</span>
            </div>
        </div>
    </div>

    {{-- Quick Action Bar --}}
    <div class="quick-actions-bar">
        <a href="{{ route('peserta.logbook.index') }}" class="btn btn-primary">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Isi Logbook Mingguan
        </a>

        <a href="{{ route('peserta.tugas.index') }}" class="btn btn-secondary">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            Lihat Daftar Tugas
        </a>

        <a href="{{ route('peserta.nilai.show') }}" class="btn btn-secondary">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            Cek Lembar Nilai
        </a>
    </div>

    {{-- 2 Kolom: Tugas Mendesak & Logbook Terbaru --}}
    <div class="dashboard-columns">
        {{-- Kolom Tugas --}}
        <div class="list-card">
            <div class="list-card-header">
                <h4 class="list-card-title">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Tugas Magang Mendesak
                </h4>
                <a href="{{ route('peserta.tugas.index') }}" style="font-size: 13px; color: var(--blue-600); text-decoration: none; font-weight: 600;">Lihat Semua &rarr;</a>
            </div>

            @if($tugasMendesak->isNotEmpty())
                @foreach($tugasMendesak as $t)
                    <div class="item-row">
                        <div class="item-meta">
                            <h4>{{ $t->judul }}</h4>
                            <p>Tenggat: {{ \Carbon\Carbon::parse($t->deadline)->isoFormat('D MMM Y') }} &bull; {{ $t->kategori ?? 'Tugas Lapangan' }}</p>
                        </div>
                        <div>
                            <a href="{{ route('peserta.tugas.show', $t) }}" class="btn btn-secondary" style="height: 32px; padding: 0 12px; font-size: 12px;">
                                Kerjakan
                            </a>
                        </div>
                    </div>
                @endforeach
            @else
                <div style="text-align: center; padding: 32px 16px; color: var(--neutral-500);">
                    <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin: 0 auto 12px auto; color: var(--green-600);">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p style="font-size: 14px; font-weight: 600; color: var(--neutral-700);">Semua tugas selesai!</p>
                    <p style="font-size: 12px;">Tidak ada tugas yang menunggu pengerjaan saat ini.</p>
                </div>
            @endif
        </div>

        {{-- Kolom Logbook --}}
        <div class="list-card">
            <div class="list-card-header">
                <h4 class="list-card-title">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    Logbook Mingguan Terkini
                </h4>
                <a href="{{ route('peserta.logbook.index') }}" style="font-size: 13px; color: var(--blue-600); text-decoration: none; font-weight: 600;">Lihat Semua &rarr;</a>
            </div>

            @if($logbookTerbaru->isNotEmpty())
                @foreach($logbookTerbaru as $lb)
                    <div class="item-row">
                        <div class="item-meta">
                            <h4>Minggu ke-{{ $lb->minggu_ke ?? ($loop->iteration) }} • {{ \Carbon\Carbon::parse($lb->tanggal)->isoFormat('D MMM Y') }}</h4>
                            <p>{{ \Illuminate\Support\Str::limit($lb->kegiatan, 60) }}</p>
                        </div>
                        <div>
                            @if($lb->status === \App\Enums\StatusLogbookEnum::PerluRevisi)
                                <span class="badge badge-danger">Perlu Revisi</span>
                            @else
                                <span class="badge badge-success">Terkirim</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div style="text-align: center; padding: 32px 16px; color: var(--neutral-500);">
                    <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin: 0 auto 12px auto; color: var(--neutral-400);">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <p style="font-size: 14px; font-weight: 600; color: var(--neutral-700);">Belum ada catatan logbook mingguan</p>
                    <p style="font-size: 12px;">Catat aktivitas mingguan magang Anda secara rutin.</p>
                </div>
            @endif
        </div>
    </div>
@endif

{{-- FASE 4: SELESAI MAGANG (Pasca Penilaian / Kelulusan) --}}
@if($fase === 'selesai' && $penempatan)
    <div class="card" style="border: 2px solid var(--green-600); background: #FAFDFB; padding: 32px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px;">
            <div>
                <span class="badge badge-success" style="font-size: 14px; padding: 6px 16px; margin-bottom: 12px;">
                    🎉 Selamat! Masa Magang Telah Selesai
                </span>
                <h2 style="font-size: 22px; font-weight: 800; color: var(--neutral-800); margin-bottom: 8px;">
                    Penyelesaian Program Magang Dinas Kominfo Sleman
                </h2>
                <p style="font-size: 14px; color: var(--neutral-600); max-width: 650px; line-height: 1.5;">
                    Anda telah menuntaskan seluruh kewajiban magang pada <strong>{{ $penempatan->bidang->nama_bidang ?? 'Dinas Kominfo Sleman' }}</strong> bersama pembimbing lapangan <strong>{{ $penempatan->pembimbing->nama ?? $penempatan->pembimbing->name ?? '-' }}</strong>.
                </p>
            </div>

            @if($isFinalNilai)
                <div style="text-align: right; background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: var(--radius-md); padding: 16px 24px;">
                    <span style="font-size: 12px; font-weight: 600; color: var(--neutral-500); text-transform: uppercase;">Predikat Kelulusan</span>
                    <div style="font-size: 36px; font-weight: 800; color: var(--green-700); line-height: 1.1;">
                        {{ $penilaian->predikat ?? 'A' }}
                    </div>
                    <span style="font-size: 13px; color: var(--neutral-600);">Skor: {{ number_format($penilaian->nilai_akhir, 2) }} / 100</span>
                </div>
            @endif
        </div>

        @if($isFinalNilai && $penilaian->catatan)
            <div style="margin-top: 20px; padding: 16px; background: var(--neutral-0); border-left: 4px solid var(--green-600); border-radius: var(--radius-default);">
                <strong style="font-size: 13px; color: var(--neutral-700); display: block; margin-bottom: 4px;">Catatan & Evaluasi Pembimbing:</strong>
                <p style="font-size: 14px; color: var(--neutral-800); font-style: italic;">"{{ $penilaian->catatan }}"</p>
            </div>
        @endif

        <div style="margin-top: 24px; display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="{{ route('peserta.nilai.show') }}" class="btn btn-primary">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Lihat Rincian Nilai 5 Rubrik
            </a>

            @if($isFinalNilai)
                <a href="{{ route('peserta.nilai.unduh') }}" class="btn btn-secondary">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Unduh Lembar Nilai Resmi (PDF)
                </a>
            @endif
        </div>
    </div>
@endif

@endsection
