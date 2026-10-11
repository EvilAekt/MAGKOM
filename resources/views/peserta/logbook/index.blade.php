@extends('layouts.peserta')

@section('title', 'Logbook & Jurnal Mingguan Peserta')
@section('page_title', 'Logbook Mingguan')

@push('styles')
<style>
    /* Layout Container Full-Width Bersih */
    .logbook-wrapper {
        max-width: 1040px;
        margin: 0 auto;
    }

    .card-modern {
        background: #ffffff;
        border: 1px solid var(--neutral-300);
        border-radius: var(--radius-lg, 12px);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        padding: 24px;
    }

    /* Banner Penempatan Magang */
    .banner-penempatan {
        background: linear-gradient(95deg, rgba(6, 65, 204, 0.96) 0%, rgba(6, 65, 204, 0.88) 50%, rgba(6, 65, 204, 0.35) 100%),
                    url('{{ asset('images/logbook_banner_bg.jpg') }}') right center / cover no-repeat;
        border-radius: var(--radius-lg, 12px);
        padding: 32px 36px;
        color: #ffffff;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        box-shadow: 0 6px 20px rgba(6, 65, 204, 0.16);
        position: relative;
        overflow: hidden;
        min-height: 185px;
    }

    .banner-penempatan::after {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 85% 50%, rgba(255, 255, 255, 0.08) 0%, transparent 60%);
        pointer-events: none;
    }

    .banner-content-wrap,
    .banner-side-stat {
        position: relative;
        z-index: 2;
    }

    .banner-title {
        font-size: var(--text-2xl);
        font-weight: var(--fw-bold);
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
        line-height: 1.3;
    }

    .banner-meta {
        font-size: var(--text-base);
        color: rgba(255, 255, 255, 0.92);
        display: flex;
        flex-wrap: wrap;
        gap: 18px;
        line-height: 1.5;
    }

    .banner-meta-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* Weekly Progress Tracker Card */
    .progress-tracker-card {
        background: #ffffff;
        border: 1px solid var(--neutral-300);
        border-radius: var(--radius-lg, 12px);
        padding: 22px 24px;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    }

    .progress-tracker-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 12px;
    }

    .progress-tracker-title {
        font-size: var(--text-base);
        font-weight: var(--fw-bold);
        color: var(--neutral-900);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .progress-bar-wrap {
        height: 10px;
        background: var(--neutral-200);
        border-radius: 999px;
        overflow: hidden;
        margin-bottom: 16px;
    }

    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--blue-600) 0%, #3B82F6 100%);
        border-radius: 999px;
        transition: width 0.4s ease;
    }

    /* Weekly Stepper Scroll */
    .weekly-stepper-wrap {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 6px;
        scrollbar-width: thin;
    }

    .week-chip {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: var(--text-xs);
        font-weight: var(--fw-semibold);
        white-space: nowrap;
        background: var(--neutral-100);
        color: var(--neutral-700);
        border: 1px solid var(--neutral-300);
        transition: all 0.15s ease;
    }

    .week-chip.is-active-week {
        border-color: var(--blue-600);
        background: var(--blue-tint);
        color: var(--blue-700);
        font-weight: 700;
    }

    .week-chip.is-done {
        background: #ECFDF5;
        border-color: #A7F3D0;
        color: #065F46;
    }

    .week-chip.is-waiting {
        background: #EFF6FF;
        border-color: #BFDBFE;
        color: #1E40AF;
    }

    .week-chip.is-revisi {
        background: #FEF3C7;
        border-color: #FDE68A;
        color: #92400E;
    }

    /* Smart Reminder Banner */
    .smart-reminder-banner {
        background: #EFF6FF;
        border: 1px solid #BFDBFE;
        border-left: 4px solid var(--blue-600);
        border-radius: var(--radius-lg, 12px);
        padding: 18px 24px;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }

    .reminder-content {
        display: flex;
        align-items: flex-start;
        gap: 14px;
    }

    .reminder-icon-box {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: var(--blue-tint);
        color: var(--blue-600);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Action Bar */
    .action-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 20px;
    }

    /* Kartu Logbook Mingguan */
    .weekly-card {
        background: #ffffff;
        border: 1px solid var(--neutral-300);
        border-radius: var(--radius-lg, 12px);
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .weekly-card:hover {
        border-color: #BFDBFE;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
    }

    .weekly-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--neutral-200);
        margin-bottom: 16px;
    }

    .weekly-badge-box {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .week-number-pill {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: var(--blue-tint);
        color: var(--blue-700);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        line-height: 1;
        flex-shrink: 0;
        border: 1px solid rgba(7, 81, 255, 0.2);
    }

    .weekly-card-title {
        font-size: var(--text-lg);
        font-weight: var(--fw-bold);
        color: var(--neutral-900);
        line-height: 1.35;
        margin-bottom: 4px;
    }

    .weekly-card-meta {
        font-size: var(--text-sm);
        color: var(--neutral-600);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .weekly-body {
        font-size: var(--text-base);
        line-height: 1.65;
        color: var(--neutral-800);
        white-space: pre-line;
    }

    .review-callout {
        margin-top: 16px;
        padding: 14px 18px;
        background: #FEF3C7;
        border-left: 4px solid #D97706;
        border-radius: 6px;
        font-size: var(--text-base);
        color: #92400E;
        line-height: 1.5;
    }

    .weekly-attachments {
        margin-top: 16px;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        padding-top: 14px;
        border-top: 1px dashed var(--neutral-200);
    }

    .attachment-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        background: var(--neutral-100);
        border: 1px solid var(--neutral-300);
        border-radius: 6px;
        font-size: var(--text-sm);
        font-weight: var(--fw-semibold);
        color: var(--neutral-800);
        text-decoration: none;
        transition: all 0.15s ease;
    }

    .attachment-pill:hover {
        background: var(--blue-tint);
        border-color: var(--blue-600);
        color: var(--blue-700);
    }

    /* Modal Form Styling */
    .modal-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(15, 23, 42, 0.65);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1050;
        padding: 20px;
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
    }

    .modal-backdrop.is-open {
        display: flex;
    }

    .modal-box {
        background: #ffffff;
        border-radius: 14px;
        width: 100%;
        max-width: 620px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        animation: modalFadeIn 0.22s ease-out;
    }

    @keyframes modalFadeIn {
        from { opacity: 0; transform: translateY(16px) scale(0.97); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    .modal-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--neutral-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #FAFBFD;
        border-top-left-radius: 14px;
        border-top-right-radius: 14px;
    }

    .modal-body {
        padding: 24px;
    }

    .modal-footer {
        padding: 16px 24px;
        border-top: 1px solid var(--neutral-200);
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        background: #FAFBFD;
        border-bottom-left-radius: 14px;
        border-bottom-right-radius: 14px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 18px;
    }

    .form-label {
        font-size: var(--text-sm);
        font-weight: var(--fw-semibold);
        color: var(--neutral-800);
        display: flex;
        align-items: center;
        justify-content: space-between;
        line-height: 1.3;
    }

    .form-control {
        height: 42px;
        padding: 0 14px;
        border: 1px solid var(--neutral-400);
        border-radius: var(--radius-default);
        font-size: var(--text-base);
        color: var(--neutral-800);
        background: #ffffff;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
        font-family: inherit;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--blue-600);
        box-shadow: 0 0 0 3px rgba(7, 81, 255, 0.15);
    }

    textarea.form-control {
        height: auto;
        padding: 12px 14px;
        line-height: 1.5;
        resize: vertical;
    }

    .tips-box {
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 18px;
        font-size: var(--text-xs);
        color: var(--neutral-700);
        line-height: 1.5;
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }

    .char-counter {
        font-size: 0.75rem;
        color: var(--neutral-500);
        text-align: right;
        margin-top: 4px;
    }
</style>
@endpush

@section('content')
<div class="logbook-wrapper">
    @if(!$penempatan)
        <div class="card-modern" style="border-left: 4px solid var(--amber-700); background: var(--amber-100); margin-bottom: 24px;">
            <div style="display: flex; align-items: flex-start; gap: 16px;">
                <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--amber-700); flex-shrink: 0; margin-top: 2px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                <div>
                    <h3 style="font-size: 1.0625rem; font-weight: 700; color: var(--amber-700); margin-bottom: 4px;">
                        Menu Logbook Mingguan Terkunci
                    </h3>
                    <p style="font-size: 0.875rem; color: var(--neutral-700); line-height: 1.5;">
                        Anda belum memiliki penempatan magang aktif. Fitur pelaporan logbook mingguan ini akan terbuka secara otomatis setelah status pendaftaran magang Anda berstatus <strong>Diterima</strong> dan masa penempatan magang telah dimulai.
                    </p>
                </div>
            </div>
        </div>
    @else
        {{-- Banner Info Penempatan Magang Aktif --}}
        <div class="banner-penempatan">
            <div class="banner-content-wrap">
                <div style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(8px); border-radius: 20px; font-size: var(--text-2xs); font-weight: var(--fw-bold); text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 8px; border: 1px solid rgba(255,255,255,0.2);">
                    <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #4ADE80;"></span>
                    Penempatan Magang Aktif
                </div>
                <h1 class="banner-title" style="text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);">
                    {{ $penempatan->bidang?->nama_bidang ?? 'Dinas Komunikasi dan Informatika' }}
                </h1>
                <div class="banner-meta" style="text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);">
                    <span class="banner-meta-item">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Pembimbing: <strong>{{ $penempatan->pembimbing?->name ?? 'Belum Ditentukan' }}</strong>
                    </span>
                    <span class="banner-meta-item">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Periode: {{ \Carbon\Carbon::parse($penempatan->tgl_mulai)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($penempatan->tgl_selesai)->translatedFormat('d M Y') }}
                    </span>
                </div>
            </div>
            <div class="banner-side-stat" style="background: rgba(15, 23, 42, 0.45); backdrop-filter: blur(10px); padding: 14px 22px; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.28); text-align: right;">
                <div style="font-size: var(--text-2xs); text-transform: uppercase; font-weight: 600; opacity: 0.95; letter-spacing: 0.04em; color: rgba(255, 255, 255, 0.9);">Minggu Berjalan Saat Ini</div>
                <div style="font-size: var(--text-2xl); font-weight: var(--fw-bold); margin-top: 2px;">Minggu ke-{{ $mingguAktif }}</div>
                <div style="font-size: var(--text-xs); opacity: 0.85; margin-top: 2px;">dari total {{ $totalMinggu }} minggu</div>
            </div>
        </div>

        {{-- Weekly Progress Tracker (User-Friendly Component) --}}
        <div class="progress-tracker-card">
            <div class="progress-tracker-header">
                <div class="progress-tracker-title">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--blue-600);">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Progres Pelaporan Logbook Mingguan
                </div>
                <div style="font-size: var(--text-sm); font-weight: 600; color: var(--neutral-700);">
                    <span style="color: var(--blue-700); font-size: var(--text-base); font-weight: 800;">{{ $totalTerisi }}</span> dari {{ $totalMinggu }} Minggu Terisi ({{ $persentaseTerisi }}%)
                </div>
            </div>

            <div class="progress-bar-wrap">
                <div class="progress-bar-fill" style="width: {{ max(4, $persentaseTerisi) }}%;"></div>
            </div>

            <div class="weekly-stepper-wrap">
                @foreach($daftarMinggu as $m)
                    @php
                        $chipClass = '';
                        $statusIcon = '📅';
                        if ($m['status'] === 'disetujui') {
                            $chipClass = 'is-done';
                            $statusIcon = '✓';
                        } elseif ($m['status'] === 'terkirim') {
                            $chipClass = 'is-waiting';
                            $statusIcon = '⏳';
                        } elseif ($m['status'] === 'perlu_revisi') {
                            $chipClass = 'is-revisi';
                            $statusIcon = '⚠️';
                        } elseif ($m['is_aktif']) {
                            $chipClass = 'is-active-week';
                            $statusIcon = '📍';
                        }
                    @endphp
                    <div class="week-chip {{ $chipClass }}" title="Minggu {{ $m['minggu_ke'] }}: {{ $m['label_periode'] }} ({{ ucfirst($m['status']) }})">
                        <span>{{ $statusIcon }}</span>
                        <span>M-{{ $m['minggu_ke'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Smart Reminder Banner jika minggu aktif belum diisi --}}
        @if(!$mingguAktifSudahDiisi)
            @php
                $periodeAktif = $daftarMinggu[$mingguAktif - 1]['label_periode'] ?? '';
            @endphp
            <div class="smart-reminder-banner">
                <div class="reminder-content">
                    <div class="reminder-icon-box">
                        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <div style="font-size: var(--text-base); font-weight: 700; color: #1E3A8A; margin-bottom: 2px;">
                            Saatnya Mengisi Laporan Minggu ke-{{ $mingguAktif }} ({{ $periodeAktif }})
                        </div>
                        <div style="font-size: var(--text-sm); color: #3B82F6; line-height: 1.4;">
                            Catat capaian pekerjaan dan aktivitas magang Anda minggu ini sebelum akhir pekan agar pembimbing dapat memantau perkembangan Anda.
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-primary" onclick="openTambahModal({{ $mingguAktif }})" style="box-shadow: 0 4px 12px rgba(7, 81, 255, 0.25); white-space: nowrap;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                    </svg>
                    + Isi Logbook Minggu Ini
                </button>
            </div>
        @endif

        {{-- Action Bar --}}
        <div class="action-bar">
            <div>
                <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Riwayat Jurnal Mingguan</h2>
                <p style="font-size: 0.875rem; color: var(--neutral-600); margin: 4px 0 0 0;">Daftar aktivitas dan evaluasi mingguan yang telah Anda laporkan kepada pembimbing lapangan</p>
            </div>

            <div style="display: flex; align-items: center; gap: 12px;">
                <span class="badge badge-info" style="font-size: 0.8125rem; padding: 6px 12px;">
                    Total: {{ $logbooks->total() }} Laporan
                </span>
                <button type="button" class="btn btn-primary" onclick="openTambahModal()" style="height: 40px; padding: 0 16px;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                    </svg>
                    + Isi Logbook Mingguan
                </button>
            </div>
        </div>

        {{-- Daftar Kartu Logbook Mingguan --}}
        @if($logbooks->isEmpty())
            <div class="card-modern" style="text-align: center; padding: 56px 24px;">
                <div style="width: 64px; height: 64px; border-radius: 50%; background: var(--blue-tint); color: var(--blue-600); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px;">
                    <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-900); margin-bottom: 6px;">Belum Ada Catatan Logbook Mingguan</h3>
                <p style="font-size: 0.875rem; color: var(--neutral-600); max-width: 440px; margin: 0 auto 20px auto; line-height: 1.5;">
                    Anda belum mengirimkan laporan mingguan. Klik tombol di bawah ini untuk mencatat ringkasan aktivitas magang Anda untuk minggu berjalan.
                </p>
                <button type="button" class="btn btn-primary" onclick="openTambahModal({{ $mingguAktif }})">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                    </svg>
                    + Isi Logbook Pertama Anda
                </button>
            </div>
        @else
            <div>
                @foreach($logbooks as $log)
                    @php
                        $statusValue = $log->status->value ?? (string)$log->status;
                        $isRevisi = ($statusValue === 'perlu_revisi');
                        $mingguKeTampil = $log->minggu_ke ?? ($loop->count - $loop->index);
                        $labelPeriodeTampil = $daftarMinggu[$mingguKeTampil - 1]['label_periode'] ?? $log->tanggal->translatedFormat('d M Y');
                    @endphp
                    <div class="weekly-card">
                        <div class="weekly-card-header">
                            <div class="weekly-badge-box">
                                <div class="week-number-pill" style="{{ $isRevisi ? 'background: #FEF3C7; color: #D97706; border-color: #FDE68A;' : '' }}">
                                    <span style="font-size: 0.625rem; font-weight: 700; text-transform: uppercase;">MG</span>
                                    <span style="font-size: 1.125rem;">{{ $mingguKeTampil }}</span>
                                </div>
                                <div>
                                    <div class="weekly-card-title">Logbook Minggu ke-{{ $mingguKeTampil }}</div>
                                    <div class="weekly-card-meta">
                                        <span>📅 Periode: <strong>{{ $labelPeriodeTampil }}</strong></span>
                                        <span>•</span>
                                        <span>Dilaporkan: {{ $log->tanggal->translatedFormat('d M Y') }} ({{ $log->created_at->diffForHumans() }})</span>
                                    </div>
                                </div>
                            </div>

                            <div style="display: flex; align-items: center; gap: 8px;">
                                @if($isRevisi)
                                    <span class="badge badge-danger">Perlu Revisi</span>
                                @elseif($statusValue === 'terkirim')
                                    <span class="badge badge-info">Terkirim</span>
                                @elseif($statusValue === 'disetujui')
                                    <span class="badge badge-success">Disetujui</span>
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst($statusValue) }}</span>
                                @endif

                                <button type="button" class="btn btn-secondary" style="height: 32px; padding: 0 12px; font-size: 0.75rem;"
                                        onclick="openEditModal({{ $log->id }}, {{ $mingguKeTampil }}, '{{ $log->tanggal->toDateString() }}', {{ json_encode($log->kegiatan) }}, '{{ $log->link_laporan ?? '' }}')">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                    Edit
                                </button>
                            </div>
                        </div>

                        <div class="weekly-body">{{ $log->kegiatan }}</div>

                        @php
                            $catatanFeedback = $log->catatan_pembimbing ?? $log->catatan_review;
                        @endphp
                        @if($catatanFeedback)
                            <div class="review-callout">
                                <div style="font-weight: 700; display: flex; align-items: center; gap: 6px; margin-bottom: 4px;">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                    </svg>
                                    Catatan Review Pembimbing Lapangan:
                                </div>
                                <div>{{ $catatanFeedback }}</div>
                            </div>
                        @endif

                        @if($log->file_laporan || $log->link_laporan)
                            <div class="weekly-attachments">
                                @if($log->file_laporan)
                                    <a href="{{ route('peserta.logbook.lampiran', $log) }}" target="_blank" class="attachment-pill">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--red-600);">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                        Berkas Laporan PDF ↗
                                    </a>
                                @endif

                                @if($log->link_laporan)
                                    <a href="{{ $log->link_laporan }}" target="_blank" rel="noopener noreferrer" class="attachment-pill">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--blue-600);">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                        Tautan Dokumen / Repositori ↗
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach

                @if($logbooks->hasPages())
                    <div style="margin-top: 24px;">
                        {{ $logbooks->links() }}
                    </div>
                @endif
            </div>
        @endif
    @endif
</div>

{{-- Modal Tambah Logbook Mingguan --}}
<div id="modalTambahLogbook" class="modal-backdrop">
    <div class="modal-box">
        <div class="modal-header">
            <div>
                <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-900); margin: 0;">Isi Logbook Mingguan</h3>
                <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 2px 0 0 0;">Laporkan capaian dan aktivitas magang mingguan Anda</p>
            </div>
            <button type="button" onclick="closeTambahModal()" style="background: none; border: none; font-size: 22px; color: var(--neutral-500); cursor: pointer; padding: 4px; line-height: 1;">&times;</button>
        </div>

        <form id="formTambahLogbook" action="{{ route('peserta.logbook.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <div class="tips-box">
                    <span style="font-size: 16px;">💡</span>
                    <div>
                        <strong>Panduan Penulisan Logbook Mingguan:</strong>
                        <br>Uraikan pekerjaan utama yang diselesaikan, kendala yang dihadapi, solusi yang diterapkan, serta rencana minggu berikutnya.
                    </div>
                </div>

                <div class="form-group">
                    <label for="tambah_minggu_ke" class="form-label">
                        <span>Pilih Minggu Magang <span style="color: var(--red-600);">*</span></span>
                        <span id="labelRentangMinggu" style="font-size: 0.75rem; color: var(--blue-600); font-weight: 600;"></span>
                    </label>
                    <select id="tambah_minggu_ke" name="minggu_ke" class="form-control" required onchange="onMingguChange(this.value)">
                        @foreach($daftarMinggu as $m)
                            <option value="{{ $m['minggu_ke'] }}" 
                                    data-periode="{{ $m['label_periode'] }}"
                                    {{ $m['minggu_ke'] === $mingguAktif ? 'selected' : '' }}
                                    {{ $m['logbook'] ? 'disabled' : '' }}>
                                Minggu ke-{{ $m['minggu_ke'] }} ({{ $m['label_periode'] }}) {{ $m['logbook'] ? '— Sudah Diisi' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="tambah_tanggal" class="form-label">
                        <span>Tanggal Pelaporan <span style="color: var(--red-600);">*</span></span>
                        <span style="font-size: 0.6875rem; color: var(--neutral-500); font-weight: 400;">Maks. hari ini</span>
                    </label>
                    <input type="date" id="tambah_tanggal" name="tanggal"
                           class="form-control"
                           value="{{ old('tanggal', now()->toDateString()) }}"
                           min="{{ now()->subDays($logbookMaksMundur)->toDateString() }}"
                           max="{{ now()->toDateString() }}"
                           required>
                </div>

                <div class="form-group">
                    <label for="tambah_kegiatan" class="form-label">
                        <span>Ringkasan Kegiatan & Capaian <span style="color: var(--red-600);">*</span></span>
                        <span style="font-size: 0.6875rem; color: var(--neutral-500); font-weight: 400;">Min. 10 karakter</span>
                    </label>
                    <textarea id="tambah_kegiatan" name="kegiatan" rows="5" class="form-control"
                              placeholder="Contoh: Pada minggu ini berhasil menyelesaikan slicing antarmuka dashboard, menghubungkan API autentikasi, serta melakukan perbaikan bug..."
                              required oninput="updateCharCount(this, 'tambah_char_count')">{{ old('kegiatan') }}</textarea>
                    <div id="tambah_char_count" class="char-counter">0 karakter (minimal 10 karakter)</div>
                </div>

                <div class="form-group">
                    <label for="tambah_file_laporan" class="form-label">
                        <span>Unggah Berkas Laporan (PDF)</span>
                        <span style="font-size: 0.6875rem; color: var(--neutral-500); font-weight: 400;">Opsional, Maks. 2MB</span>
                    </label>
                    <input type="file" id="tambah_file_laporan" name="file_laporan" accept=".pdf" class="form-control" style="padding-top: 8px;">
                </div>

                <div class="form-group">
                    <label for="tambah_link_laporan" class="form-label">
                        <span>Tautan Berkas / Repositori</span>
                        <span style="font-size: 0.6875rem; color: var(--neutral-500); font-weight: 400;">Opsional (https://)</span>
                    </label>
                    <input type="url" id="tambah_link_laporan" name="link_laporan" class="form-control"
                           placeholder="https://drive.google.com/... atau https://github.com/..."
                           value="{{ old('link_laporan') }}">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" onclick="closeTambahModal()" class="btn btn-secondary">Batal</button>
                <button type="submit" id="btnSubmitTambah" class="btn btn-primary">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Kirim Logbook Mingguan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit Logbook Mingguan --}}
<div id="modalEditLogbook" class="modal-backdrop">
    <div class="modal-box">
        <div class="modal-header">
            <div>
                <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-900); margin: 0;">Perbarui Logbook Mingguan</h3>
                <p id="modalEditSubtitle" style="font-size: 0.8125rem; color: var(--neutral-600); margin: 2px 0 0 0;">Minggu ke-X</p>
            </div>
            <button type="button" onclick="closeEditModal()" style="background: none; border: none; font-size: 22px; color: var(--neutral-500); cursor: pointer; padding: 4px; line-height: 1;">&times;</button>
        </div>

        <form id="formEditLogbook" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <input type="hidden" id="edit_minggu_ke" name="minggu_ke">
            <input type="hidden" id="edit_tanggal" name="tanggal">

            <div class="modal-body">
                <div class="form-group">
                    <label for="edit_kegiatan" class="form-label">
                        <span>Ringkasan Kegiatan & Capaian <span style="color: var(--red-600);">*</span></span>
                        <span style="font-size: 0.6875rem; color: var(--neutral-500); font-weight: 400;">Min. 10 karakter</span>
                    </label>
                    <textarea id="edit_kegiatan" name="kegiatan" rows="5" class="form-control" required oninput="updateCharCount(this, 'edit_char_count')"></textarea>
                    <div id="edit_char_count" class="char-counter">0 karakter</div>
                </div>

                <div class="form-group">
                    <label for="edit_file_laporan" class="form-label">
                        <span>Perbarui Berkas Laporan (PDF)</span>
                        <span style="font-size: 0.6875rem; color: var(--neutral-500); font-weight: 400;">Opsional, Maks. 2MB</span>
                    </label>
                    <input type="file" id="edit_file_laporan" name="file_laporan" accept=".pdf" class="form-control" style="padding-top: 8px;">
                    <span style="font-size: 0.75rem; color: var(--neutral-500); margin-top: 2px;">Kosongkan jika tidak ingin mengubah berkas yang sudah ada.</span>
                </div>

                <div class="form-group">
                    <label for="edit_link_laporan" class="form-label">
                        <span>Perbarui Tautan Berkas / Repositori</span>
                        <span style="font-size: 0.6875rem; color: var(--neutral-500); font-weight: 400;">Opsional (https://)</span>
                    </label>
                    <input type="url" id="edit_link_laporan" name="link_laporan" class="form-control" placeholder="https://...">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" onclick="closeEditModal()" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function updateCharCount(textarea, counterId) {
        const counter = document.getElementById(counterId);
        if (!counter) return;
        const len = textarea.value.trim().length;
        if (len < 10) {
            counter.textContent = `${len} karakter (kurang ${10 - len} karakter)`;
            counter.style.color = 'var(--red-600)';
        } else {
            counter.textContent = `${len} karakter (memenuhi syarat min. 10)`;
            counter.style.color = '#10B981';
        }
    }

    function onMingguChange(mingguVal) {
        const select = document.getElementById('tambah_minggu_ke');
        const selectedOpt = select.options[select.selectedIndex];
        const label = document.getElementById('labelRentangMinggu');
        if (selectedOpt && label) {
            const periode = selectedOpt.getAttribute('data-periode');
            label.textContent = periode ? `Periode: ${periode}` : '';
        }
    }

    function openTambahModal(preSelectMinggu = null) {
        const modal = document.getElementById('modalTambahLogbook');
        const select = document.getElementById('tambah_minggu_ke');

        if (preSelectMinggu && select) {
            select.value = preSelectMinggu;
        }

        onMingguChange(select?.value);
        modal.classList.add('is-open');

        // Auto focus textarea
        setTimeout(() => {
            document.getElementById('tambah_kegiatan')?.focus();
        }, 150);
    }

    function closeTambahModal() {
        document.getElementById('modalTambahLogbook')?.classList.remove('is-open');
    }

    function openEditModal(id, mingguKe, tanggal, kegiatan, link) {
        const modal = document.getElementById('modalEditLogbook');
        const form = document.getElementById('formEditLogbook');
        const subtitle = document.getElementById('modalEditSubtitle');
        const inputMinggu = document.getElementById('edit_minggu_ke');
        const inputTanggal = document.getElementById('edit_tanggal');
        const inputKegiatan = document.getElementById('edit_kegiatan');
        const inputLink = document.getElementById('edit_link_laporan');

        form.action = `/peserta/logbook/${id}`;
        subtitle.textContent = `Minggu ke-${mingguKe} • Dilaporkan: ${tanggal}`;
        inputMinggu.value = mingguKe;
        inputTanggal.value = tanggal;
        inputKegiatan.value = kegiatan;
        inputLink.value = link || '';

        updateCharCount(inputKegiatan, 'edit_char_count');

        modal.classList.add('is-open');

        setTimeout(() => {
            inputKegiatan.focus();
        }, 150);
    }

    function closeEditModal() {
        document.getElementById('modalEditLogbook')?.classList.remove('is-open');
    }

    // Anti double-submit
    document.getElementById('formTambahLogbook')?.addEventListener('submit', function() {
        const btn = document.getElementById('btnSubmitTambah');
        if (btn) {
            btn.disabled = true;
            btn.textContent = 'Menyimpan...';
        }
    });

    // Close on backdrop click & ESC key
    window.addEventListener('click', function(e) {
        const modalTambah = document.getElementById('modalTambahLogbook');
        const modalEdit = document.getElementById('modalEditLogbook');
        if (e.target === modalTambah) closeTambahModal();
        if (e.target === modalEdit) closeEditModal();
    });

    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeTambahModal();
            closeEditModal();
        }
    });
</script>
@endpush
@endsection
