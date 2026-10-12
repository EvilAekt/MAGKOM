@extends('layouts.peserta')

@section('title', 'Detail & Berkas Pendaftaran Magang')
@section('page_title', 'Detail & Berkas Pendaftaran')

@push('styles')
<style>
    /* ========================================================
       MAGKOM CORE DESIGN SYSTEM: DETAIL & BERKAS PENDAFTARAN
       ======================================================== */
    .detail-pendaftaran-wrapper {
        max-width: 1040px;
        margin: 0 auto;
        padding-bottom: 48px;
    }

    /* Global Reset untuk Tombol & Lencana */
    .detail-pendaftaran-wrapper button,
    .detail-pendaftaran-wrapper .btn,
    .modal-box button,
    .modal-box-large button,
    .modal-box .btn,
    .modal-box-large .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        vertical-align: middle;
        line-height: 1.25;
        font-family: inherit;
        font-weight: 600;
        border-radius: 8px;
        box-sizing: border-box;
        transition: all 0.2s ease;
        text-decoration: none;
        cursor: pointer;
    }

    .detail-pendaftaran-wrapper .badge,
    .modal-box-large .badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        line-height: 1;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 5px 10px;
        border-radius: 6px;
        vertical-align: middle;
        box-sizing: border-box;
    }

    /* 1. HERO BANNER KHAS MAGKOM */
    .detail-hero {
        background: linear-gradient(95deg, rgba(6, 65, 204, 0.96) 0%, rgba(7, 81, 255, 0.90) 55%, rgba(7, 81, 255, 0.82) 100%),
                    url('{{ asset('images/pendaftaran_banner_bg.jpg') }}') right center / cover no-repeat;
        border-radius: var(--radius-xl, 16px);
        padding: 28px 32px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        margin-bottom: 24px;
        box-shadow: 0 10px 25px -5px rgba(7, 81, 255, 0.25);
    }

    .detail-hero::before {
        content: 'MAGKOM';
        position: absolute;
        right: -20px;
        bottom: -25px;
        font-size: 6rem;
        font-weight: 900;
        letter-spacing: -0.05em;
        color: rgba(255, 255, 255, 0.06);
        pointer-events: none;
        user-select: none;
    }

    /* Top Action & Breadcrumb Bar di Atas Hero Banner */
    .detail-top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 16px;
    }

    .btn-top-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 16px;
        background: #ffffff;
        border: 1px solid var(--neutral-300, #E2E8F0);
        border-radius: 999px;
        color: var(--neutral-700, #334155);
        font-size: 0.8125rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    }

    .btn-top-back:hover {
        background: #EFF6FF;
        border-color: #93C5FD;
        color: var(--blue-600, #0751FF);
        transform: translateX(-2px);
    }

    .btn-top-cancel {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 16px;
        background: #FEF2F2;
        border: 1px solid #FECACA;
        border-radius: 999px;
        color: #DC2626;
        font-size: 0.8125rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 1px 2px rgba(220, 38, 38, 0.05);
    }

    .btn-top-cancel:hover {
        background: #FEE2E2;
        border-color: #F87171;
        color: #B91C1C;
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(220, 38, 38, 0.12);
    }

    .hero-main-content {
        position: relative;
        z-index: 2;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        flex-wrap: wrap;
        gap: 20px;
    }

    .hero-reg-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 4px 12px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        margin-bottom: 8px;
    }

    .hero-reg-title {
        font-size: 1.5rem;
        font-weight: 800;
        margin: 0 0 12px 0;
        color: #ffffff;
        line-height: 1.3;
        letter-spacing: -0.01em;
    }

    .hero-meta-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .hero-meta-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: rgba(0, 0, 0, 0.22);
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 500;
        color: #ffffff;
    }

    /* 2. MODERN STEPPER STATUS TRACKER */
    .stepper-card {
        background: #ffffff;
        border: 1px solid var(--neutral-200);
        border-radius: 12px;
        padding: 22px 28px;
        margin-bottom: 24px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
    }

    .stepper-timeline {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin: 6px 0;
    }

    .stepper-timeline::before {
        content: '';
        position: absolute;
        top: 22px;
        left: 50px;
        right: 50px;
        height: 3px;
        background: #E2E8F0;
        z-index: 1;
    }

    .stepper-step {
        position: relative;
        z-index: 2;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        width: 170px;
    }

    .stepper-icon-circle {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: #ffffff;
        border: 3px solid #CBD5E1;
        color: #64748B;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 15px;
        margin-bottom: 8px;
        transition: all 0.25s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .stepper-step.completed .stepper-icon-circle {
        background: #10B981;
        border-color: #10B981;
        color: #ffffff;
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
    }

    .stepper-step.active .stepper-icon-circle {
        background: #0751FF;
        border-color: #0751FF;
        color: #ffffff;
        box-shadow: 0 0 0 5px rgba(7, 81, 255, 0.2);
    }

    .stepper-step.warning .stepper-icon-circle {
        background: #D97706;
        border-color: #D97706;
        color: #ffffff;
        box-shadow: 0 0 0 4px rgba(217, 119, 6, 0.2);
    }

    .stepper-step.danger .stepper-icon-circle {
        background: #DC2626;
        border-color: #DC2626;
        color: #ffffff;
        box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.2);
    }

    .stepper-phase {
        font-size: 0.6875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 700;
        color: #94A3B8;
        margin-bottom: 2px;
    }

    .stepper-label {
        font-size: 0.8125rem;
        font-weight: 700;
        color: var(--neutral-700);
        line-height: 1.3;
    }

    .stepper-step.active .stepper-label,
    .stepper-step.completed .stepper-label {
        color: var(--neutral-900);
    }

    /* 3. WIDGET PROGRESS KELENGKAPAN BERKAS */
    .docs-summary-card {
        background: #ffffff;
        border: 1px solid var(--neutral-200);
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
    }

    .docs-summary-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 16px;
    }

    .docs-summary-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--neutral-900);
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
    }

    .docs-summary-subtitle {
        font-size: 0.8125rem;
        color: var(--neutral-600);
        margin: 4px 0 0 0;
    }

    .progress-bar-wrapper {
        background: #E2E8F0;
        border-radius: 999px;
        height: 10px;
        overflow: hidden;
        margin-top: 8px;
    }

    .progress-bar-meter {
        height: 100%;
        border-radius: 999px;
        transition: width 0.5s cubic-bezier(0.16, 1, 0.3, 1);
    }

    /* 4. MODULAR DOCUMENT CARDS */
    .doc-card-modern {
        background: #ffffff;
        border: 1.5px solid #E2E8F0;
        border-radius: 12px;
        padding: 20px 22px;
        margin-bottom: 18px;
        transition: all 0.2s ease;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
    }

    .doc-card-modern:hover {
        border-color: #BFDBFE;
        box-shadow: 0 4px 12px rgba(7, 81, 255, 0.05);
    }

    .doc-header-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 14px;
    }

    .doc-title-group {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .doc-name {
        font-size: 0.9375rem;
        font-weight: 700;
        color: var(--neutral-900);
    }

    .badge-doc-wajib {
        background: #FEE2E2;
        color: #991B1B;
        font-size: 0.6875rem;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 4px;
    }

    .badge-doc-opsional {
        background: #F1F5F9;
        color: #475569;
        font-size: 0.6875rem;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 4px;
    }

    .doc-instructions {
        font-size: 0.8125rem;
        color: var(--neutral-600);
        margin-top: 4px;
        line-height: 1.45;
    }

    /* Berkas Terunggah Box */
    .uploaded-box-modern {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 18px;
        background: #F8FAFC;
        border: 1.5px solid #E2E8F0;
        border-radius: 10px;
        gap: 16px;
        flex-wrap: wrap;
        transition: border-color 0.2s ease;
    }

    .uploaded-box-modern:hover {
        border-color: #CBD5E1;
    }

    .uploaded-left {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 240px;
    }

    .uploaded-icon-badge {
        width: 42px;
        height: 42px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .uploaded-icon-badge.pdf {
        background: #FEE2E2;
        color: #DC2626;
    }

    .uploaded-icon-badge.img {
        background: #E0E7FF;
        color: #4F46E5;
    }

    .uploaded-filename {
        font-size: 0.875rem;
        font-weight: 700;
        color: var(--neutral-900);
        word-break: break-all;
    }

    .uploaded-meta {
        font-size: 0.75rem;
        color: var(--neutral-500);
        margin-top: 2px;
    }

    .uploaded-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    /* Dropzone Upload */
    .dropzone-box-modern {
        border: 2px dashed #93C5FD;
        border-radius: 10px;
        padding: 22px 18px;
        text-align: center;
        background: #F0F7FF;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
    }

    .dropzone-box-modern:hover,
    .dropzone-box-modern.dragover {
        border-color: #0751FF;
        background: #E6EEFF;
        transform: translateY(-1px);
    }

    .dropzone-icon-circle {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: #ffffff;
        color: #0751FF;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 8px;
        box-shadow: 0 2px 6px rgba(7, 81, 255, 0.12);
    }

    .dropzone-prompt {
        font-size: 0.875rem;
        font-weight: 700;
        color: var(--neutral-800);
    }

    .dropzone-subtext {
        font-size: 0.75rem;
        color: var(--neutral-600);
        margin-top: 3px;
    }

    /* Selected File Bar */
    .selected-bar-modern {
        display: none;
        align-items: center;
        justify-content: space-between;
        margin-top: 12px;
        padding: 12px 16px;
        background: #ffffff;
        border: 1.5px solid #0751FF;
        border-radius: 8px;
        font-size: 0.8125rem;
        gap: 12px;
        flex-wrap: wrap;
    }

    /* 5. FOOTER FINALISASI PENDAFTARAN */
    .submit-final-card {
        background: #ffffff;
        border: 1.5px solid var(--neutral-200);
        border-radius: 12px;
        padding: 22px 26px;
        margin-top: 28px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 18px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .btn-submit-main {
        height: 44px;
        padding: 0 24px;
        font-size: 0.875rem;
        font-weight: 700;
        background: #0751FF;
        color: #ffffff;
        border: 1px solid #0751FF;
        box-shadow: 0 4px 12px rgba(7, 81, 255, 0.25);
    }

    .btn-submit-main:hover:not(:disabled) {
        background: #0641CC;
        box-shadow: 0 6px 16px rgba(7, 81, 255, 0.35);
        transform: translateY(-1px);
    }

    .btn-submit-main:disabled {
        background: #94A3B8;
        border-color: #94A3B8;
        cursor: not-allowed;
        box-shadow: none;
        opacity: 0.7;
    }

    /* 6. MODAL STYLING */
    .modal {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 100;
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(4px);
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .modal.open {
        display: flex;
    }

    .modal-box {
        background: #ffffff;
        border-radius: 16px;
        width: 100%;
        max-width: 520px;
        padding: 26px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .modal-box-large {
        background: #ffffff;
        border-radius: 16px;
        width: 100%;
        max-width: 980px;
        max-height: 92vh;
        display: flex;
        flex-direction: column;
        padding: 22px 26px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        overflow: hidden;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        padding-bottom: 14px;
        border-bottom: 1px solid #E2E8F0;
    }

    .modal-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--neutral-900);
        margin: 0;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: var(--neutral-500);
        padding: 2px 6px;
        line-height: 1;
        border-radius: 6px;
    }

    .modal-close:hover {
        color: var(--neutral-800);
        background: #F1F5F9;
    }

    .modal-body-preview {
        flex: 1;
        min-height: 60vh;
        max-height: 75vh;
        position: relative;
        background: #F8FAFC;
        border: 1.5px solid #E2E8F0;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        margin-top: 14px;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    @media (max-width: 768px) {
        .stepper-timeline {
            overflow-x: auto;
            padding-bottom: 10px;
            justify-content: flex-start;
            gap: 24px;
        }
        .stepper-timeline::before {
            display: none;
        }
        .uploaded-box-modern {
            flex-direction: column;
            align-items: flex-start;
        }
        .uploaded-actions {
            width: 100%;
            justify-content: flex-start;
        }
    }
</style>
@endpush

@section('content')
<div class="detail-pendaftaran-wrapper">

    @php
        $val = $pendaftaran->status->value ?? (string)$pendaftaran->status;
        $bisaBatal = in_array($val, ['berkas_belum_lengkap', 'diajukan', 'perlu_perbaikan', 'waiting_list']);
        $bisaUnggah = in_array($val, ['berkas_belum_lengkap', 'perlu_perbaikan']);

        $totalWajib = $jenisDokumenList->where('wajib', true)->count();
        $uploadedWajib = $jenisDokumenList->where('wajib', true)->filter(fn($j) => $dokumenTerunggah->has($j->id))->count();
        $persen = $totalWajib > 0 ? round(($uploadedWajib / $totalWajib) * 100) : 100;
        $semuaWajibLengkap = ($uploadedWajib >= $totalWajib);
    @endphp

    {{-- ========================================================
         TOP ACTION & BREADCRUMB BAR (DI ATAS HERO BANNER)
         ======================================================== --}}
    <div class="detail-top-bar">
        <a href="{{ route('peserta.pendaftaran.index') }}" class="btn-top-back">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Kembali ke Riwayat Pendaftaran
        </a>

        @if($bisaBatal)
            <button type="button" class="btn-top-cancel" onclick="openCancelModal()" title="Batalkan pengajuan magang ini">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="15" y1="9" x2="9" y2="15"></line>
                    <line x1="9" y1="9" x2="15" y2="15"></line>
                </svg>
                Batalkan Pengajuan
            </button>
        @endif
    </div>

    {{-- ========================================================
         1. HERO BANNER KHAS MAGKOM
         ======================================================== --}}
    <div class="detail-hero">
        <div class="hero-main-content">
            <div>
                <div class="hero-reg-badge">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                    </svg>
                    NOMOR REGISTRASI: #REG-{{ str_pad((string)$pendaftaran->id, 5, '0', STR_PAD_LEFT) }}
                </div>
                <h1 class="hero-reg-title">
                    Magang Periode {{ $pendaftaran->bulanMagang?->bulan_mulai?->translatedFormat('F Y') }}
                </h1>
                <div class="hero-meta-pills">
                    <div class="hero-meta-pill">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                            <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                        </svg>
                        <span>Jurusan: <strong>{{ $pendaftaran->kuota?->jurusan?->nama_jurusan }}</strong></span>
                    </div>
                    <div class="hero-meta-pill">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <span>Durasi: <strong>{{ $pendaftaran->durasi_bulan }} Bulan</strong></span>
                    </div>
                    <div class="hero-meta-pill">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <span>Dibuat: {{ $pendaftaran->created_at?->translatedFormat('d M Y') }}</span>
                    </div>
                </div>
            </div>

            <div>
                @if($val === 'berkas_belum_lengkap')
                    <span class="badge" style="background: rgba(255, 255, 255, 0.2); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.35); font-size: 0.8125rem; padding: 7px 14px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-dasharray="4 4"></circle></svg>
                        Berkas Belum Lengkap
                    </span>
                @elseif($val === 'diajukan')
                    <span class="badge" style="background: #FEF3C7; color: #92400E; font-size: 0.8125rem; padding: 7px 14px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        Menunggu Verifikasi Admin
                    </span>
                @elseif($val === 'waiting_list')
                    <span class="badge" style="background: #DBEAFE; color: #1E40AF; font-size: 0.8125rem; padding: 7px 14px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        Waiting List (Antrean Cadangan)
                    </span>
                @elseif($val === 'perlu_perbaikan')
                    <span class="badge" style="background: #FED7AA; color: #9A3412; font-size: 0.8125rem; padding: 7px 14px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                        Perlu Perbaikan Berkas
                    </span>
                @elseif($val === 'diterima')
                    <span class="badge" style="background: #D1FAE5; color: #065F46; font-size: 0.8125rem; padding: 7px 14px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Diterima Magang
                    </span>
                @elseif($val === 'ditolak')
                    <span class="badge" style="background: #FEE2E2; color: #991B1B; font-size: 0.8125rem; padding: 7px 14px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                        Pengajuan Ditolak
                    </span>
                @elseif($val === 'dibatalkan')
                    <span class="badge" style="background: #E2E8F0; color: #475569; font-size: 0.8125rem; padding: 7px 14px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                        Dibatalkan
                    </span>
                @else
                    <span class="badge badge-neutral" style="font-size: 0.8125rem; padding: 7px 14px;">{{ $val }}</span>
                @endif
            </div>
        </div>
    </div>

    {{-- ========================================================
         2. MODERN STEPPER STATUS TRACKER
         ======================================================== --}}
    <div class="stepper-card">
        <div class="stepper-timeline">
            {{-- Step 1: Berkas Persyaratan --}}
            <div class="stepper-step {{ $val === 'berkas_belum_lengkap' ? 'active' : 'completed' }}">
                <div class="stepper-icon-circle">
                    @if($val === 'berkas_belum_lengkap')
                        1
                    @else
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    @endif
                </div>
                <div class="stepper-phase">Tahap 1</div>
                <div class="stepper-label">1. Berkas Persyaratan</div>
            </div>

            {{-- Step 2: Verifikasi Tim Admin --}}
            <div class="stepper-step {{ in_array($val, ['diajukan', 'waiting_list']) ? 'active' : (in_array($val, ['perlu_perbaikan', 'diterima', 'ditolak']) ? 'completed' : '') }}">
                <div class="stepper-icon-circle">
                    @if(in_array($val, ['perlu_perbaikan', 'diterima', 'ditolak']))
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    @else
                        2
                    @endif
                </div>
                <div class="stepper-phase">Tahap 2</div>
                <div class="stepper-label">
                    {{ $val === 'waiting_list' ? '2. Waiting List' : '2. Verifikasi Berkas' }}
                </div>
            </div>

            {{-- Step 3: Keputusan Akhir --}}
            <div class="stepper-step {{ $val === 'perlu_perbaikan' ? 'warning' : ($val === 'diterima' ? 'completed' : ($val === 'ditolak' ? 'danger' : '')) }}">
                <div class="stepper-icon-circle">
                    @if($val === 'diterima')
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    @elseif($val === 'perlu_perbaikan')
                        !
                    @elseif($val === 'ditolak')
                        ✕
                    @else
                        3
                    @endif
                </div>
                <div class="stepper-phase">Tahap 3</div>
                <div class="stepper-label">
                    @if($val === 'perlu_perbaikan') Perlu Perbaikan
                    @elseif($val === 'diterima') Diterima Magang
                    @elseif($val === 'ditolak') Ditolak
                    @else 3. Keputusan @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Alert Catatan Revisi Admin (AC-03-10) --}}
    @if($val === 'perlu_perbaikan')
        <div style="background: #FFFBEB; border: 1.5px solid #FDE68A; border-left: 5px solid #D97706; border-radius: 12px; padding: 20px; margin-bottom: 24px; display: flex; gap: 14px; align-items: flex-start;">
            <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: #D97706; flex-shrink: 0; margin-top: 2px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div style="flex: 1;">
                <strong style="font-size: 0.9375rem; color: #92400E;">Catatan Perbaikan Berkas dari Tim Verifikator:</strong>
                <div style="margin-top: 8px; padding: 12px 16px; background: #ffffff; border-radius: 8px; border: 1px solid #FDE68A; font-size: 0.875rem; color: var(--neutral-800); line-height: 1.55;">
                    "{{ $pendaftaran->catatan_admin ?? 'Harap tinjau kembali berkas persyaratan dan perbarui dokumen yang diminta.' }}"
                </div>
                <p style="font-size: 0.8125rem; color: #78350F; margin-top: 8px;">
                    Silakan klik tombol <strong>Ganti Berkas</strong> pada dokumen terkait di bawah untuk memperbarui dokumen, kemudian tekan <strong>Ajukan Pendaftaran Ulang</strong>.
                </p>
            </div>
        </div>
    @endif

    {{-- Alert Kelulusan Diterima & Unduh Surat (AC-16-1, F-16) --}}
    @if($val === 'diterima' && $pendaftaran->penempatan)
        <div style="background: #ECFDF5; border: 1.5px solid #A7F3D0; border-left: 5px solid #10B981; border-radius: 12px; padding: 20px 24px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 44px; height: 44px; border-radius: 50%; background: #D1FAE5; color: #065F46; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <strong style="font-size: 1rem; color: #065F46;">Selamat! Permohonan Magang Anda Telah Disetujui</strong>
                    <div style="font-size: 0.8125rem; color: #047857; margin-top: 2px;">
                        Surat Keterangan Diterima resmi bertanda tangan digital instansi siap dilihat langsung dan diunduh.
                    </div>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <button type="button" class="btn" onclick="openDocPreview('{{ route('peserta.penempatan.surat-diterima.pratinjau', $pendaftaran->penempatan) }}', '{{ route('peserta.penempatan.surat-diterima', $pendaftaran->penempatan) }}', 'Surat Keterangan Diterima Magang - #REG-{{ str_pad((string)$pendaftaran->id, 5, '0', STR_PAD_LEFT) }}', 'application/pdf')" style="height: 40px; padding: 0 16px; font-size: 0.8125rem; font-weight: 700; background: #ffffff; border: 1.5px solid #10B981; color: #065F46;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    Lihat Surat Diterima
                </button>
                <a href="{{ route('peserta.penempatan.surat-diterima', $pendaftaran->penempatan) }}" class="btn btn-primary" style="height: 40px; padding: 0 18px; font-size: 0.8125rem; font-weight: 700;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Unduh Surat Diterima (PDF)
                </a>
            </div>
        </div>
    @endif

    {{-- ========================================================
         3. WIDGET SUMMARY KELENGKAPAN BERKAS PERSYARATAN
         ======================================================== --}}
    <div class="docs-summary-card">
        <div class="docs-summary-header">
            <div>
                <h2 class="docs-summary-title">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: #0751FF;">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                    </svg>
                    Berkas Persyaratan Magang
                </h2>
                <p class="docs-summary-subtitle">
                    Format yang diizinkan: <strong>PDF, JPG, JPEG, PNG</strong> (Maksimal {{ $maksMb }} MB per berkas).
                </p>
            </div>
            <div style="text-align: right;">
                <span class="badge" style="font-size: 0.8125rem; padding: 6px 12px; background: {{ $semuaWajibLengkap ? '#DCFCE7' : '#EFF6FF' }}; color: {{ $semuaWajibLengkap ? '#15803D' : '#1D4ED8' }};">
                    {{ $uploadedWajib }} dari {{ $totalWajib }} Berkas Wajib ({{ $persen }}%)
                </span>
            </div>
        </div>

        <div class="progress-bar-wrapper">
            <div class="progress-bar-meter" style="width: {{ $persen }}%; background: {{ $semuaWajibLengkap ? '#10B981' : '#0751FF' }};"></div>
        </div>
    </div>

    {{-- ========================================================
         4. DAFTAR KARTU DOKUMEN PERSYARATAN (MODULAR)
         ======================================================== --}}
    <div>
        @foreach($jenisDokumenList as $jenis)
            @php
                $terunggah = $dokumenTerunggah->get($jenis->id);
                $isPdf = $terunggah && str_contains(strtolower($terunggah->mime ?? ''), 'pdf');
            @endphp

            <div class="doc-card-modern" id="card_doc_{{ $jenis->id }}">
                <div class="doc-header-row">
                    <div>
                        <div class="doc-title-group">
                            <span class="doc-name">{{ $jenis->nama }}</span>
                            @if($jenis->wajib)
                                <span class="badge-doc-wajib">Wajib</span>
                            @else
                                <span class="badge-doc-opsional">Opsional</span>
                            @endif
                        </div>
                        <div class="doc-instructions">
                            {{ $jenis->deskripsi ?? 'Dokumen resmi yang diterbitkan oleh instansi pendidikan asal.' }}
                        </div>
                    </div>

                    <div>
                        @if($terunggah)
                            <span class="badge" style="background: #DCFCE7; color: #166534; font-size: 0.75rem;">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                Sudah Terunggah
                            </span>
                        @else
                            <span class="badge" style="background: #FEF3C7; color: #92400E; font-size: 0.75rem;">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                Belum Diunggah
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Tampilan Berkas yang Sudah Diunggah --}}
                @if($terunggah)
                    @php
                        $downloadUrl = $terunggah->getSignedDownloadUrl(5);
                        $previewUrl = $terunggah->getSignedPreviewUrl(5);
                    @endphp

                    <div class="uploaded-box-modern">
                        <div class="uploaded-left">
                            <div class="uploaded-icon-badge {{ $isPdf ? 'pdf' : 'img' }}">
                                @if($isPdf)
                                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                        <line x1="16" y1="13" x2="8" y2="13"></line>
                                        <line x1="16" y1="17" x2="8" y2="17"></line>
                                    </svg>
                                @else
                                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                        <polyline points="21 15 16 10 5 21"></polyline>
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <div class="uploaded-filename">{{ $terunggah->nama_asli }}</div>
                                <div class="uploaded-meta">
                                    {{ round($terunggah->ukuran / 1024, 1) }} KB &bull; Diunggah pada {{ $terunggah->created_at->translatedFormat('d M Y, H:i') }}
                                </div>
                            </div>
                        </div>

                        <div class="uploaded-actions">
                            <button type="button" class="btn btn-primary" onclick="openDocPreview('{{ $previewUrl }}', '{{ $downloadUrl }}', '{{ addslashes($jenis->nama) }} - {{ addslashes($terunggah->nama_asli) }}', '{{ $terunggah->mime }}')" style="height: 36px; padding: 0 14px; font-size: 0.8125rem;" title="Lihat pratinjau berkas">
                                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                Lihat Berkas
                            </button>

                            <a href="{{ $downloadUrl }}" class="btn btn-secondary" style="height: 36px; padding: 0 14px; font-size: 0.8125rem;" title="Unduh berkas fisik">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"></path>
                                    <polyline points="7 10 12 15 17 10"></polyline>
                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                </svg>
                                Unduh
                            </a>

                            @if($bisaUnggah)
                                <button type="button" class="btn btn-secondary" onclick="toggleDropzone({{ $jenis->id }})" style="height: 36px; padding: 0 12px; font-size: 0.8125rem;">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"></path>
                                    </svg>
                                    Ganti Berkas
                                </button>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Interactive Drag & Drop Dropzone --}}
                @if($bisaUnggah)
                    <div id="dropzone_wrapper_{{ $jenis->id }}" style="{{ $terunggah ? 'display: none; margin-top: 14px;' : 'margin-top: 8px;' }}">
                        <form action="{{ route('peserta.berkas.unggah', $pendaftaran) }}" method="POST" enctype="multipart/form-data" id="form_upload_{{ $jenis->id }}" onsubmit="handleUploadSubmit(this, {{ $jenis->id }})">
                            @csrf
                            <input type="hidden" name="jenis_dokumen_id" value="{{ $jenis->id }}">

                            <input type="file"
                                   id="file_input_{{ $jenis->id }}"
                                   name="file"
                                   accept=".pdf,.jpg,.jpeg,.png"
                                   style="display: none;"
                                   onchange="handleFileSelected(this, {{ $jenis->id }}, {{ $maksMb }})">

                            <div class="dropzone-box-modern"
                                 id="dropzone_box_{{ $jenis->id }}"
                                 onclick="document.getElementById('file_input_{{ $jenis->id }}').click()"
                                 ondragover="handleDragOver(event, this)"
                                 ondragleave="handleDragLeave(event, this)"
                                 ondrop="handleDrop(event, this, {{ $jenis->id }}, {{ $maksMb }})">
                                <div class="dropzone-icon-circle">
                                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"></path>
                                        <polyline points="17 8 12 3 7 8"></polyline>
                                        <line x1="12" y1="3" x2="12" y2="15"></line>
                                    </svg>
                                </div>
                                <div class="dropzone-prompt">
                                    Seret & lepas berkas ke sini, atau <span style="color: #0751FF; text-decoration: underline;">Pilih File</span>
                                </div>
                                <div class="dropzone-subtext">
                                    Format PDF, JPG, JPEG, atau PNG (Maksimal {{ $maksMb }} MB)
                                </div>
                            </div>

                            {{-- Selected File Bar --}}
                            <div class="selected-bar-modern" id="selected_bar_{{ $jenis->id }}">
                                <div style="display: flex; align-items: center; gap: 10px; overflow: hidden;">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: #0751FF; flex-shrink: 0;">
                                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                    </svg>
                                    <span id="selected_name_{{ $jenis->id }}" style="font-weight: 700; color: var(--neutral-800); text-overflow: ellipsis; white-space: nowrap; overflow: hidden;"></span>
                                    <span id="selected_size_{{ $jenis->id }}" style="font-size: 0.75rem; color: var(--neutral-500); flex-shrink: 0;"></span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <button type="button" class="btn btn-secondary" onclick="resetFileSelection({{ $jenis->id }})" style="height: 32px; padding: 0 12px; font-size: 0.75rem;">
                                        Batal
                                    </button>
                                    <button type="submit" class="btn btn-primary" id="btn_submit_{{ $jenis->id }}" style="height: 32px; padding: 0 16px; font-size: 0.75rem;">
                                        Unggah File Sekarang
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- ========================================================
         5. FINALISASI PENGAJUAN PENDAFTARAN
         ======================================================== --}}
    @if($bisaUnggah)
        <div class="submit-final-card">
            <div>
                @if($semuaWajibLengkap)
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 32px; height: 32px; border-radius: 50%; background: #DCFCE7; color: #16A34A; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </div>
                        <div>
                            <span style="color: #15803D; font-size: 0.9375rem; font-weight: 700; display: block;">
                                Seluruh berkas persyaratan wajib telah lengkap!
                            </span>
                            <span style="font-size: 0.8125rem; color: var(--neutral-600);">
                                Klik tombol di samping untuk mengirimkan pengajuan ke tim verifikasi admin.
                            </span>
                        </div>
                    </div>
                @else
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 32px; height: 32px; border-radius: 50%; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                        </div>
                        <div>
                            <span style="color: #92400E; font-size: 0.9375rem; font-weight: 700; display: block;">
                                Masih ada berkas persyaratan bertanda [Wajib] yang belum diunggah.
                            </span>
                            <span style="font-size: 0.8125rem; color: var(--neutral-600);">
                                Lengkapi seluruh berkas wajib di atas untuk mengaktifkan tombol pengajuan.
                            </span>
                        </div>
                    </div>
                @endif
            </div>

            <form action="{{ route('peserta.pendaftaran.ajukan', $pendaftaran) }}" method="POST" id="formAjukanPendaftaran" onsubmit="handleAjukanSubmit(this)">
                @csrf
                <button type="submit" class="btn btn-submit-main" id="btnAjukanPendaftaran" {{ !$semuaWajibLengkap ? 'disabled' : '' }}>
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    Ajukan Pendaftaran Sekarang
                </button>
            </form>
        </div>
    @else
        <div style="margin-top: 24px; padding: 16px 20px; background: #ffffff; border: 1.5px solid var(--neutral-200); border-radius: 12px; font-size: 0.8125rem; color: var(--neutral-600); display: flex; align-items: center; gap: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
            <div style="width: 32px; height: 32px; border-radius: 8px; background: #F1F5F9; color: #64748B; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0110 0v4"></path>
                </svg>
            </div>
            <span>Berkas pendaftaran saat ini terkunci karena telah diajukan atau sedang diproses oleh tim admin verifikator.</span>
        </div>
    @endif

</div>

{{-- ========================================================
     6. MODAL KONFIRMASI PEMBATALAN PENDAFTARAN
     ======================================================== --}}
@if($bisaBatal)
<div class="modal" id="cancelModal" onclick="if(event.target === this) closeCancelModal()">
    <div class="modal-box">
        <div class="modal-header">
            <div>
                <h3 class="modal-title">Konfirmasi Pembatalan Pendaftaran</h3>
                <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 3px 0 0 0;">
                    #REG-{{ str_pad((string)$pendaftaran->id, 5, '0', STR_PAD_LEFT) }} &bull; {{ $pendaftaran->bulanMagang?->bulan_mulai?->translatedFormat('F Y') }}
                </p>
            </div>
            <button type="button" class="modal-close" onclick="closeCancelModal()">&times;</button>
        </div>

        <form action="{{ route('peserta.pendaftaran.batalkan', $pendaftaran) }}" method="POST" id="formBatalkanPendaftaran" onsubmit="handleBatalSubmit(this)">
            @csrf

            <div style="padding: 16px; background: #FFFBEB; border: 1.5px solid #FDE68A; border-radius: 10px; margin-bottom: 20px;">
                <div style="display: flex; gap: 12px; align-items: flex-start;">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: #D97706; flex-shrink: 0; margin-top: 2px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div>
                        <strong style="color: #92400E; font-size: 0.875rem;">Perhatian: Pembatalan Bersifat Final</strong>
                        <p style="font-size: 0.8125rem; color: #78350F; margin-top: 4px; line-height: 1.5;">
                            Jika dibatalkan, status pendaftaran akan berubah menjadi <strong>Dibatalkan</strong> dan tidak dapat diaktifkan kembali. Sesuai aturan (BR-03), Anda tidak dapat membuat pengajuan baru pada bulan mulai yang sama, namun Anda dapat memilih bulan magang lain yang masih dibuka.
                        </p>
                    </div>
                </div>
            </div>

            <p style="font-size: 0.875rem; color: var(--neutral-700); margin-bottom: 24px;">
                Apakah Anda yakin ingin membatalkan pengajuan pendaftaran magang ini?
            </p>

            <div style="display: flex; justify-content: flex-end; gap: 12px; padding-top: 16px; border-top: 1px solid #E2E8F0;">
                <button type="button" class="btn btn-secondary" onclick="closeCancelModal()" style="height: 38px; padding: 0 16px; font-size: 0.8125rem;">Kembali</button>
                <button type="submit" class="btn btn-danger" id="btnSubmitBatal" style="height: 38px; padding: 0 18px; font-size: 0.8125rem; background: #DC2626; border-color: #DC2626; color: #ffffff;">
                    Ya, Batalkan Pendaftaran
                </button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- ========================================================
     7. MODAL IN-APP PRATINJAU DOKUMEN
     ======================================================== --}}
<div class="modal" id="docPreviewModal" onclick="if(event.target === this) closeDocPreview()" role="dialog" aria-modal="true" aria-labelledby="previewModalTitle">
    <div class="modal-box-large">
        <div class="modal-header" style="margin-bottom: 0; padding-bottom: 14px;">
            <div style="display: flex; align-items: center; gap: 12px; overflow: hidden; padding-right: 12px;">
                <div id="previewModalIcon" style="width: 38px; height: 38px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background: var(--blue-tint); color: var(--blue-700);">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                </div>
                <div style="overflow: hidden;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <h3 class="modal-title" id="previewModalTitle" style="font-size: 1rem; margin: 0; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
                            Pratinjau Dokumen
                        </h3>
                        <span id="previewModalBadge" class="badge" style="background: #EFF6FF; color: #1D4ED8; font-size: 0.6875rem; padding: 2px 8px;">PDF</span>
                    </div>
                    <p style="font-size: 0.75rem; color: var(--neutral-500); margin: 3px 0 0 0;">
                        Tinjauan dokumen langsung di sistem &bull; Terproteksi otorisasi MagKom
                    </p>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 8px; flex-shrink: 0;">
                <a id="previewOpenNewTabBtn" href="#" target="_blank" rel="noopener noreferrer" class="btn btn-secondary" style="height: 34px; padding: 0 12px; font-size: 0.75rem;" title="Buka pratinjau di tab browser baru">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"></path>
                        <polyline points="15 3 21 3 21 9"></polyline>
                        <line x1="10" y1="14" x2="21" y2="3"></line>
                    </svg>
                    Tab Baru
                </a>
                <a id="previewDownloadBtn" href="#" class="btn btn-secondary" style="height: 34px; padding: 0 12px; font-size: 0.75rem;" title="Unduh berkas fisik">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    Unduh
                </a>
                <button type="button" class="modal-close" onclick="closeDocPreview()" title="Tutup pratinjau (Esc)">&times;</button>
            </div>
        </div>

        <div class="modal-body-preview">
            {{-- Loading Indicator --}}
            <div id="previewLoading" style="position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; background: rgba(255, 255, 255, 0.95); z-index: 10;">
                <div style="width: 38px; height: 38px; border: 3px solid #CBD5E1; border-top-color: #0751FF; border-radius: 50%; animation: spin 0.7s linear infinite; margin-bottom: 12px;"></div>
                <div style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-800);">Memuat Pratinjau Dokumen...</div>
                <div style="font-size: 0.75rem; color: var(--neutral-500); margin-top: 4px;">Mengambil berkas aman dari disk privat MagKom</div>
            </div>

            {{-- PDF Iframe Container --}}
            <iframe id="previewIframe" src="about:blank" style="width: 100%; height: 75vh; border: none; display: none;"></iframe>

            {{-- Image Container (JPG/PNG) --}}
            <div id="previewImageWrapper" style="display: none; padding: 24px; width: 100%; height: 100%; text-align: center; overflow: auto; justify-content: center; align-items: center;">
                <img id="previewImage" alt="Pratinjau Berkas" style="max-width: 100%; max-height: 70vh; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.12); object-fit: contain;">
            </div>

            {{-- Error State Container --}}
            <div id="previewError" style="display: none; flex-direction: column; align-items: center; justify-content: center; padding: 30px; text-align: center;">
                <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: #D97706; margin-bottom: 12px;">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <div style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-800);">Tidak Dapat Memuat Pratinjau Langsung</div>
                <div style="font-size: 0.8125rem; color: var(--neutral-600); margin: 6px 0 16px 0; max-width: 420px;">
                    Format berkas atau konfigurasi browser tidak mendukung pratinjau inline. Silakan gunakan tombol unduh di bawah ini.
                </div>
                <a id="previewFallbackDownloadBtn" href="#" class="btn btn-primary" style="height: 38px; padding: 0 18px; font-size: 0.8125rem;">
                    Unduh Berkas Sekarang
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // In-App Document Preview Modal Handler
    let previewTimer = null;

    function openDocPreview(previewUrl, downloadUrl, title, mime) {
        const modal = document.getElementById('docPreviewModal');
        const modalTitle = document.getElementById('previewModalTitle');
        const modalBadge = document.getElementById('previewModalBadge');
        const openNewTabBtn = document.getElementById('previewOpenNewTabBtn');
        const downloadBtn = document.getElementById('previewDownloadBtn');
        const fallbackBtn = document.getElementById('previewFallbackDownloadBtn');
        const loading = document.getElementById('previewLoading');
        const iframe = document.getElementById('previewIframe');
        const imgWrapper = document.getElementById('previewImageWrapper');
        const img = document.getElementById('previewImage');
        const errBox = document.getElementById('previewError');
        const iconContainer = document.getElementById('previewModalIcon');

        if (!modal) return;

        if (previewTimer) {
            clearTimeout(previewTimer);
            previewTimer = null;
        }

        loading.style.display = 'flex';
        iframe.style.display = 'none';
        iframe.onload = null;
        iframe.src = 'about:blank';
        imgWrapper.style.display = 'none';
        if (img) {
            img.onload = null;
            img.onerror = null;
            img.removeAttribute('src');
        }
        errBox.style.display = 'none';

        modalTitle.textContent = title || 'Pratinjau Dokumen';
        openNewTabBtn.href = previewUrl;
        downloadBtn.href = downloadUrl;
        if (fallbackBtn) fallbackBtn.href = downloadUrl;

        const isPdf = (mime && mime.toLowerCase().includes('pdf')) || (!mime && previewUrl.toLowerCase().includes('pdf'));

        if (isPdf) {
            modalBadge.textContent = 'PDF';
            modalBadge.style.background = '#FEE2E2';
            modalBadge.style.color = '#DC2626';
            if (iconContainer) {
                iconContainer.style.background = '#FEE2E2';
                iconContainer.style.color = '#DC2626';
            }
            const finalPdfUrl = previewUrl.includes('#') ? previewUrl : (previewUrl + '#toolbar=0&navpanes=0');
            iframe.onload = function() {
                handlePreviewLoaded();
            };
            iframe.style.display = 'block';
            iframe.src = finalPdfUrl;

            previewTimer = setTimeout(function() {
                handlePreviewLoaded();
            }, 600);
        } else {
            modalBadge.textContent = 'Gambar';
            modalBadge.style.background = '#E0E7FF';
            modalBadge.style.color = '#4F46E5';
            if (iconContainer) {
                iconContainer.style.background = '#E0E7FF';
                iconContainer.style.color = '#4F46E5';
            }
            img.onload = function() {
                handlePreviewLoaded();
            };
            img.onerror = function() {
                handlePreviewError();
            };
            img.src = previewUrl;
            imgWrapper.style.display = 'flex';

            previewTimer = setTimeout(function() {
                handlePreviewLoaded();
            }, 700);
        }

        modal.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function handlePreviewLoaded() {
        if (previewTimer) {
            clearTimeout(previewTimer);
            previewTimer = null;
        }
        const loading = document.getElementById('previewLoading');
        if (loading) loading.style.display = 'none';
    }

    function handlePreviewError() {
        if (previewTimer) {
            clearTimeout(previewTimer);
            previewTimer = null;
        }
        const loading = document.getElementById('previewLoading');
        const iframe = document.getElementById('previewIframe');
        const imgWrapper = document.getElementById('previewImageWrapper');
        const errBox = document.getElementById('previewError');

        if (loading) loading.style.display = 'none';
        if (iframe) iframe.style.display = 'none';
        if (imgWrapper) imgWrapper.style.display = 'none';
        if (errBox) errBox.style.display = 'flex';
    }

    function closeDocPreview() {
        if (previewTimer) {
            clearTimeout(previewTimer);
            previewTimer = null;
        }
        const modal = document.getElementById('docPreviewModal');
        const iframe = document.getElementById('previewIframe');
        const img = document.getElementById('previewImage');

        if (modal) {
            modal.classList.remove('open');
            document.body.style.overflow = '';
        }
        if (iframe) {
            iframe.onload = null;
            iframe.src = 'about:blank';
        }
        if (img) {
            img.onload = null;
            img.onerror = null;
            img.removeAttribute('src');
        }
    }

    // Keyboard navigation (Esc to close)
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' || e.key === 'Esc') {
            closeDocPreview();
            closeCancelModal();
        }
    });

    // Toggle Replace Dropzone
    function toggleDropzone(id) {
        const wrapper = document.getElementById('dropzone_wrapper_' + id);
        if (wrapper) {
            wrapper.style.display = (wrapper.style.display === 'none' || wrapper.style.display === '') ? 'block' : 'none';
        }
    }

    // Drag and Drop Event Handlers
    function handleDragOver(e, element) {
        e.preventDefault();
        e.stopPropagation();
        element.classList.add('dragover');
    }
    function handleDragLeave(e, element) {
        e.preventDefault();
        e.stopPropagation();
        element.classList.remove('dragover');
    }
    function handleDrop(e, element, jenisId, maksMb) {
        e.preventDefault();
        e.stopPropagation();
        element.classList.remove('dragover');

        const files = e.dataTransfer.files;
        if (files && files.length > 0) {
            const input = document.getElementById('file_input_' + jenisId);
            input.files = files;
            handleFileSelected(input, jenisId, maksMb);
        }
    }

    // Client-side instant validation & file preview (AC-03-5)
    function handleFileSelected(input, jenisId, maksMb) {
        const file = input.files[0];
        if (!file) return;

        const allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
        const ext = file.name.split('.').pop().toLowerCase();
        const maxBytes = maksMb * 1024 * 1024;

        if (!allowedExtensions.includes(ext)) {
            alert('Format berkas tidak diizinkan. Hanya file PDF, JPG, JPEG, atau PNG yang diterima.');
            input.value = '';
            resetFileSelection(jenisId);
            return;
        }

        if (file.size > maxBytes) {
            alert(`Ukuran berkas (${(file.size / (1024 * 1024)).toFixed(2)} MB) melebihi batas maksimal ${maksMb} MB.`);
            input.value = '';
            resetFileSelection(jenisId);
            return;
        }

        const bar = document.getElementById('selected_bar_' + jenisId);
        const nameSpan = document.getElementById('selected_name_' + jenisId);
        const sizeSpan = document.getElementById('selected_size_' + jenisId);

        nameSpan.textContent = file.name;
        sizeSpan.textContent = `(${(file.size / 1024).toFixed(1)} KB)`;
        bar.style.display = 'flex';
    }

    function resetFileSelection(jenisId) {
        const input = document.getElementById('file_input_' + jenisId);
        if (input) input.value = '';

        const bar = document.getElementById('selected_bar_' + jenisId);
        if (bar) bar.style.display = 'none';
    }

    // Anti Double-Submit pada Form Upload Berkas (AC-03-8)
    function handleUploadSubmit(form, jenisId) {
        const btn = document.getElementById('btn_submit_' + jenisId);
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `<span style="display:inline-block;width:12px;height:12px;border:2px solid #fff;border-top-color:transparent;border-radius:50%;animation:spin 0.6s linear infinite;margin-right:6px;"></span> Mengunggah...`;
        }
    }

    // Anti Double-Submit pada Form Ajukan Pendaftaran (AC-03-8)
    function handleAjukanSubmit(form) {
        const btn = document.getElementById('btnAjukanPendaftaran');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `<span style="display:inline-block;width:14px;height:14px;border:2px solid #fff;border-top-color:transparent;border-radius:50%;animation:spin 0.6s linear infinite;margin-right:8px;"></span> Mengirim Pengajuan...`;
        }
    }

    // Modal Konfirmasi Pembatalan (AC-03-9)
    function openCancelModal() {
        const modal = document.getElementById('cancelModal');
        if (modal) modal.classList.add('open');
    }
    function closeCancelModal() {
        const modal = document.getElementById('cancelModal');
        if (modal) modal.classList.remove('open');
    }
    function handleBatalSubmit(form) {
        const btn = document.getElementById('btnSubmitBatal');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `<span style="display:inline-block;width:14px;height:14px;border:2px solid #fff;border-top-color:transparent;border-radius:50%;animation:spin 0.6s linear infinite;margin-right:8px;"></span> Memproses...`;
        }
    }
</script>
@endpush
@endsection
