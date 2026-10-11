@extends('layouts.peserta')

@section('title', 'Tugas: ' . $tugas->judul)
@section('page_title', 'Detail Tugas Bimbingan')

@push('styles')
<style>
    /* Google Classroom Assignment View Layout */
    .classroom-assignment-container {
        max-width: 1120px;
        margin: 0 auto;
    }

    /* Back Navigation Pill */
    .back-nav {
        margin-bottom: 22px;
    }

    .back-link-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        background: #ffffff;
        border: 1px solid var(--neutral-300);
        border-radius: 20px;
        color: var(--neutral-700);
        font-size: var(--text-sm);
        font-weight: var(--fw-semibold);
        text-decoration: none;
        transition: all 0.15s ease;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
    }

    .back-link-pill:hover {
        background: var(--blue-tint);
        border-color: var(--blue-600);
        color: var(--blue-700);
    }

    /* 2 Kolom Asimetris Google Classroom (65% : 35%) */
    .classroom-assignment-grid {
        display: grid;
        grid-template-columns: 1fr 370px;
        gap: 28px;
        align-items: start;
    }

    /* Card Modern Base */
    .card-modern {
        background: #ffffff;
        border: 1px solid var(--neutral-300);
        border-radius: var(--radius-lg, 12px);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }

    /* Lembar Penugasan Utama (Kolom Kiri) */
    .assignment-sheet-card {
        padding: 32px 36px;
        margin-bottom: 24px;
    }

    .assignment-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--neutral-200);
        margin-bottom: 24px;
    }

    .assignment-title-area {
        display: flex;
        align-items: flex-start;
        gap: 18px;
        flex: 1;
    }

    /* Ikon Dokumen Khas Google Classroom */
    .classroom-icon-circle {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: #E8F0FE;
        color: #1A73E8;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 2px 6px rgba(26, 115, 232, 0.15);
    }

    .assignment-heading-wrap h1 {
        font-size: var(--text-2xl);
        font-weight: var(--fw-bold);
        color: var(--neutral-900);
        line-height: 1.3;
        margin: 0 0 8px 0;
        letter-spacing: -0.01em;
    }

    .assignment-meta-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        font-size: var(--text-sm);
        color: var(--neutral-600);
        line-height: 1.4;
    }

    .meta-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 10px;
        background: var(--neutral-100);
        border: 1px solid var(--neutral-300);
        border-radius: 6px;
        font-size: var(--text-xs);
        font-weight: var(--fw-semibold);
        color: var(--neutral-700);
    }

    /* Badge Tenggat Waktu di Kanan Atas */
    .deadline-banner-box {
        text-align: right;
        flex-shrink: 0;
    }

    .deadline-label {
        font-size: var(--text-xs);
        text-transform: uppercase;
        font-weight: var(--fw-bold);
        color: var(--neutral-500);
        letter-spacing: 0.04em;
        margin-bottom: 4px;
    }

    .deadline-value {
        font-size: var(--text-sm);
        font-weight: var(--fw-bold);
        color: var(--neutral-800);
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 6px;
    }

    .deadline-value.is-late {
        color: var(--red-600);
    }

    /* Konten Instruksi & Panduan */
    .section-eyebrow {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: var(--text-xs);
        font-weight: var(--fw-bold);
        color: var(--neutral-500);
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 12px;
    }

    .instruction-body-text {
        font-size: var(--text-base);
        line-height: 1.7;
        color: var(--neutral-800);
        white-space: pre-line;
        background: #FAFBFD;
        border: 1px solid var(--neutral-250, #E9ECEF);
        border-radius: 10px;
        padding: 20px 24px;
    }

    /* Timeline Riwayat Pengumpulan (Bawah Kiri) */
    .submission-stream-card {
        padding: 28px 36px;
    }

    .stream-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 14px;
        border-bottom: 1px solid var(--neutral-200);
    }

    .stream-title {
        font-size: var(--text-lg);
        font-weight: var(--fw-bold);
        color: var(--neutral-900);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .submission-timeline-item {
        border: 1px solid var(--neutral-200);
        background: #ffffff;
        border-radius: 10px;
        padding: 18px 22px;
        margin-bottom: 16px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
        transition: border-color 0.15s ease;
    }

    .submission-timeline-item:hover {
        border-color: #BFDBFE;
    }

    .submission-top-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 10px;
    }

    .submission-author-info {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: var(--text-sm);
        font-weight: var(--fw-semibold);
        color: var(--neutral-800);
    }

    .submission-avatar-mini {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: var(--blue-600);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: var(--text-2xs);
        font-weight: var(--fw-bold);
    }

    .submission-desc {
        font-size: var(--text-sm);
        line-height: 1.6;
        color: var(--neutral-800);
        white-space: pre-line;
    }

    .submission-file-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        background: var(--neutral-100);
        border: 1px solid var(--neutral-300);
        border-radius: 6px;
        font-size: var(--text-xs);
        font-weight: var(--fw-semibold);
        color: var(--neutral-700);
        margin-top: 10px;
    }

    .feedback-bubble-box {
        margin-top: 14px;
        padding: 14px 18px;
        background: #FEF3C7;
        border-left: 4px solid #D97706;
        border-radius: 8px;
        font-size: var(--text-sm);
        color: #92400E;
        line-height: 1.5;
    }

    /* Kolom Kanan: Panel "Tugas Anda" (Sticky Your Work) */
    .your-work-panel {
        position: sticky;
        top: 84px;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .your-work-card {
        padding: 24px;
    }

    .your-work-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--neutral-200);
        margin-bottom: 20px;
    }

    .your-work-title {
        font-size: var(--text-lg);
        font-weight: var(--fw-bold);
        color: var(--neutral-900);
        margin: 0;
    }

    .form-group-clean {
        margin-bottom: 16px;
    }

    .form-label-clean {
        font-size: var(--text-xs);
        font-weight: var(--fw-bold);
        color: var(--neutral-700);
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 6px;
    }

    .textarea-clean {
        width: 100%;
        padding: 12px 14px;
        border: 1.5px solid #94A3B8;
        border-radius: 8px;
        font-size: var(--text-sm);
        color: var(--neutral-900);
        background: #ffffff;
        line-height: 1.5;
        font-family: inherit;
        resize: vertical;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
        box-sizing: border-box;
    }

    .textarea-clean:focus {
        outline: none;
        border-color: #1A73E8;
        box-shadow: 0 0 0 3px rgba(26, 115, 232, 0.18);
        background: #ffffff;
    }

    /* Custom File Upload Box */
    .file-upload-box {
        display: block;
        border: 1.5px dashed #93C5FD;
        border-radius: 8px;
        padding: 16px;
        background: #F0F7FF;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .file-upload-box:hover {
        border-color: #1A73E8;
        background: #E8F0FE;
    }

    .file-preview-pill {
        display: none;
        align-items: center;
        justify-content: space-between;
        padding: 8px 12px;
        background: #EFF6FF;
        border: 1px solid #BFDBFE;
        border-radius: 6px;
        font-size: var(--text-xs);
        color: #1E40AF;
        margin-top: 8px;
        font-weight: 600;
    }

    .file-preview-pill.active {
        display: flex;
    }

    /* Tombol Serahkan Google Classroom */
    .btn-turn-in-classroom {
        width: 100%;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: var(--text-sm);
        font-weight: var(--fw-bold);
        border-radius: 8px;
        background: #1A73E8;
        color: #ffffff;
        border: none;
        cursor: pointer;
        transition: background 0.15s ease, box-shadow 0.15s ease;
        margin-top: 6px;
        box-shadow: 0 2px 6px rgba(26, 115, 232, 0.25);
    }

    .btn-turn-in-classroom:hover:not(:disabled) {
        background: #1557B0;
        box-shadow: 0 4px 12px rgba(26, 115, 232, 0.35);
    }

    .btn-turn-in-classroom:disabled {
        background: var(--neutral-300);
        color: var(--neutral-500);
        cursor: not-allowed;
        box-shadow: none;
    }

    /* Info Card Pembimbing */
    .guidance-info-card {
        padding: 20px 22px;
        background: #F8FAFC;
        border: 1px solid var(--neutral-250, #E2E8F0);
        border-radius: var(--radius-lg, 12px);
    }

    .guidance-title {
        font-size: var(--text-xs);
        font-weight: var(--fw-bold);
        text-transform: uppercase;
        color: var(--neutral-600);
        letter-spacing: 0.04em;
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 8px;
    }

    .guidance-desc {
        font-size: var(--text-xs);
        color: var(--neutral-600);
        line-height: 1.55;
    }

    /* Responsif */
    @media (max-width: 1024px) {
        .classroom-assignment-grid {
            grid-template-columns: 1fr;
        }

        .your-work-panel {
            position: static;
        }

        .assignment-sheet-card,
        .submission-stream-card {
            padding: 24px 20px;
        }
    }
</style>
@endpush

@section('content')
<div class="classroom-assignment-container">

    {{-- Breadcrumb Navigasi Bersih --}}
    <div class="back-nav">
        <a href="{{ route('peserta.tugas.index') }}" class="back-link-pill">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Daftar Tugas
        </a>
    </div>

    @php
        $statusVal = $tugas->status->value ?? (string)$tugas->status;
        $isSelesai = $statusVal === 'selesai';
        $isProses = $statusVal === 'proses';
        $deadline = \Carbon\Carbon::parse($tugas->deadline);
        $isTerlambat = !$isSelesai && $deadline->isPast();
        $penempatan = $tugas->penempatan;
        $mentorNama = $penempatan->pembimbing->nama ?? $penempatan->pembimbing->name ?? 'Budi Santoso, S.Kom';
        $kategoriNama = $tugas->kategori ?? 'Tugas Magang';
        $kompleksitasNama = ucfirst($tugas->kompleksitas->value ?? (string)$tugas->kompleksitas);
    @endphp

    <div class="classroom-assignment-grid">

        {{-- ============================================================
             KOLOM KIRI (65%): LEMBAR PENUGASAN & INSTRUKSI CLASSROOM
             ============================================================ --}}
        <div>
            {{-- Kartu Utama Penugasan --}}
            <div class="card-modern assignment-sheet-card">
                <div class="assignment-header">
                    <div class="assignment-title-area">
                        <div class="classroom-icon-circle">
                            <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>

                        <div class="assignment-heading-wrap">
                            <h1>{{ $tugas->judul }}</h1>
                            <div class="assignment-meta-row">
                                <span>Pembimbing: <strong>{{ $mentorNama }}</strong></span>
                                <span>&bull;</span>
                                <span class="meta-chip">📁 {{ $kategoriNama }}</span>
                                <span class="meta-chip">⚡ Kompleksitas: {{ $kompleksitasNama }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Tenggat Waktu di Kanan Atas --}}
                    <div class="deadline-banner-box">
                        <div class="deadline-label">Tenggat Waktu</div>
                        <div class="deadline-value {{ $isTerlambat ? 'is-late' : '' }}">
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>{{ $deadline->translatedFormat('d M Y') }}</span>
                        </div>
                        @if($isTerlambat)
                            <span style="font-size: 0.6875rem; color: var(--red-600); font-weight: 700;">(Lewat Tenggat)</span>
                        @endif
                    </div>
                </div>

                {{-- Lembar Instruksi / Panduan Pengerjaan --}}
                <div>
                    <div class="section-eyebrow">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Panduan Pengerjaan
                    </div>

                    <div class="instruction-body-text">
                        {{ $tugas->deskripsi ?: 'Tidak ada deskripsi rinci yang disertakan.' }}
                    </div>

                    {{-- Sumber Daya Panduan & Lampiran dari Pembimbing --}}
                    @if($tugas->file_lampiran || $tugas->link_referensi)
                        <div style="margin-top: 16px; padding-top: 14px; border-top: 1px dashed var(--neutral-300); display: flex; flex-direction: column; gap: 8px;">
                            <div style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase; letter-spacing: 0.04em;">
                                Lampiran & Acuan dari Pembimbing:
                            </div>

                            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                                @if($tugas->link_referensi)
                                    <a href="{{ $tugas->link_referensi }}" target="_blank" rel="noopener noreferrer" style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 6px; background: #eff6ff; color: #1d4ed8; text-decoration: none; font-size: 13px; font-weight: 600; border: 1px solid #bfdbfe;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                                        <span>Buka Tautan Acuan ↗</span>
                                    </a>
                                @endif

                                @if($tugas->file_lampiran)
                                    @php $badge = $tugas->badge_color_lampiran; @endphp
                                    <a href="{{ route('peserta.tugas.lampiran', $tugas->id) }}" style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 6px; background: #ffffff; color: var(--neutral-800); text-decoration: none; font-size: 13px; font-weight: 600; border: 1px solid var(--neutral-300); box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                        <span style="padding: 1px 6px; border-radius: 4px; background: {{ $badge['bg'] }}; color: {{ $badge['color'] }}; font-size: 11px; font-weight: 800;">
                                            {{ $badge['label'] }}
                                        </span>
                                        <span>Unduh {{ $tugas->nama_file_lampiran ?: 'Berkas Panduan' }} ({{ $tugas->ukuran_format }})</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Kartu Riwayat Progres & Umpan Balik Pembimbing --}}
            <div class="card-modern submission-stream-card">
                <div class="stream-header">
                    <div class="stream-title">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: #1A73E8;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Riwayat Laporan Progres & Feedback
                    </div>
                    <span class="badge badge-info" style="font-size: 0.75rem;">
                        {{ $tugas->progresLogs->count() }} Laporan Terkirim
                    </span>
                </div>

                @if($tugas->progresLogs->isEmpty())
                    <div style="text-align: center; padding: 36px 16px; color: var(--neutral-500); background: #FAFBFD; border-radius: 8px; border: 1px dashed var(--neutral-300);">
                        <svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin: 0 auto 10px auto; color: var(--neutral-400);">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <div style="font-size: var(--text-sm); font-weight: 600; color: var(--neutral-700); margin-bottom: 2px;">Belum Ada Laporan Progres</div>
                        <p style="font-size: var(--text-xs); color: var(--neutral-500); margin: 0; max-width: 380px; margin: 0 auto;">
                            Gunakan panel di sebelah kanan untuk mengirimkan draf pengerjaan atau hasil tugas kepada pembimbing Anda.
                        </p>
                    </div>
                @else
                    @foreach($tugas->progresLogs as $progres)
                        @php
                            $rev = $progres->status_review->value ?? (string)$progres->status_review;
                        @endphp
                        <div class="submission-timeline-item">
                            <div class="submission-top-row">
                                <div class="submission-author-info">
                                    <div class="submission-avatar-mini">{{ substr(auth()->user()->name ?? 'P', 0, 1) }}</div>
                                    <span>Laporan Progres &bull; {{ \Carbon\Carbon::parse($progres->tgl_lapor)->translatedFormat('d F Y') }}</span>
                                </div>

                                <div>
                                    @if($rev === 'diterima')
                                        <span class="badge badge-success">✓ Diterima Pembimbing</span>
                                    @elseif($rev === 'perlu_revisi')
                                        <span class="badge badge-danger">⚠️ Perlu Revisi</span>
                                    @else
                                        <span class="badge badge-info">⏳ Menunggu Reviu</span>
                                    @endif
                                </div>
                            </div>

                            <div class="submission-desc">{{ $progres->catatan }}</div>

                            @if($progres->file)
                                <div class="submission-file-chip">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--red-600);">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                    </svg>
                                    <span>Lampiran berkas pengerjaan terunggah di server privat</span>
                                </div>
                            @endif

                            @if($progres->catatan_review)
                                <div class="feedback-bubble-box">
                                    <div style="font-weight: 700; display: flex; align-items: center; gap: 6px; margin-bottom: 4px;">
                                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                        </svg>
                                        Umpan Balik Pembimbing ({{ $mentorNama }}):
                                    </div>
                                    <div>"{{ $progres->catatan_review }}"</div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        {{-- ============================================================
             KOLOM KANAN (35%): WIDGET "TUGAS ANDA" (YOUR WORK CARD)
             ============================================================ --}}
        <div class="your-work-panel">
            <div class="card-modern your-work-card">
                <div class="your-work-header">
                    <h3 class="your-work-title">Tugas Anda</h3>
                    @if($isSelesai)
                        <span class="badge badge-success" style="font-size: 0.75rem; padding: 4px 10px;">Selesai</span>
                    @elseif($tugas->progresLogs->isNotEmpty())
                        <span class="badge badge-warning" style="font-size: 0.75rem; padding: 4px 10px;">Diserahkan</span>
                    @else
                        <span class="badge badge-info" style="font-size: 0.75rem; padding: 4px 10px;">Ditugaskan</span>
                    @endif
                </div>

                @if($isSelesai)
                    <div style="background: #ECFDF5; border: 1px solid #A7F3D0; border-radius: 8px; padding: 16px; text-align: center; color: #065F46;">
                        <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin: 0 auto 8px auto; color: #10B981;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div style="font-size: var(--text-sm); font-weight: 700; margin-bottom: 2px;">Tugas Telah Selesai</div>
                        <div style="font-size: var(--text-xs); line-height: 1.4;">Tugas ini telah ditinjau dan dinyatakan selesai oleh pembimbing lapangan Anda.</div>
                    </div>
                @else
                    <form action="{{ route('peserta.tugas.progres', $tugas) }}" method="POST" enctype="multipart/form-data" id="formProgresTugas">
                        @csrf

                        {{-- 1. Lampiran Bukti / File Tugas (Di Atas) --}}
                        <div class="form-group-clean">
                            <label class="form-label-clean">
                                <span>Lampiran Bukti / File Tugas</span>
                                <span style="font-size: 0.6875rem; color: var(--neutral-500); font-weight: 400;">Maks. 5MB</span>
                            </label>

                            <input type="file" id="file" name="file" accept=".pdf,.zip,.rar,.doc,.docx" style="display: none;" onchange="handleFileSelected(this)">

                            <label for="file" class="file-upload-box" id="dropzoneBox">
                                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: #1A73E8; margin: 0 auto 6px auto; display: block;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <div style="font-size: var(--text-xs); font-weight: 700; color: #1A73E8;">+ Tambah Berkas Lampiran</div>
                                <div style="font-size: 0.6875rem; color: var(--neutral-500); margin-top: 2px;">PDF, ZIP, DOCX (Maksimal 5MB)</div>
                            </label>

                            <div id="filePreviewPill" class="file-preview-pill">
                                <span id="fileNameDisplay" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 260px;"></span>
                                <button type="button" onclick="clearSelectedFile()" style="background: none; border: none; color: var(--red-600); font-weight: 700; cursor: pointer; padding: 2px 6px;">✕</button>
                            </div>

                            @error('file')
                                <span style="font-size: 0.75rem; color: var(--red-600); display: block; margin-top: 4px;">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- 2. Catatan Progres Pekerjaan (Di Bawah, Bergaris Tegas) --}}
                        <div class="form-group-clean" style="margin-top: 18px;">
                            <label for="catatan" class="form-label-clean">
                                <span>Catatan Progres Pekerjaan <span style="color: var(--red-600);">*</span></span>
                                <span id="catatanLengthHint" style="font-size: 0.6875rem; color: var(--neutral-500); font-weight: 400;">Rangkuman kemajuan</span>
                            </label>
                            <textarea id="catatan" name="catatan" rows="4" class="textarea-clean"
                                      placeholder="Tuliskan ringkasan hasil kerja, tautan demo/repositori, atau kendala pengerjaan..."
                                      required>{{ old('catatan') }}</textarea>
                            @error('catatan')
                                <span style="font-size: 0.75rem; color: var(--red-600); display: block; margin-top: 4px;">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" id="btnSubmitProgres" class="btn-turn-in-classroom">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            Serahkan / Kirim Progres
                        </button>
                    </form>
                @endif
            </div>

            {{-- Kartu Info Panduan Penilaian --}}
            <div class="guidance-info-card">
                <div class="guidance-title">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: #1A73E8;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Informasi Pengerjaan
                </div>
                <div class="guidance-desc">
                    Setiap progres yang Anda kirimkan akan dievaluasi oleh pembimbing lapangan Anda untuk penilaian 5 rubrik kinerja magang.
                </div>
            </div>
        </div>

    </div>

</div>

@push('scripts')
<script>
    function handleFileSelected(input) {
        const preview = document.getElementById('filePreviewPill');
        const display = document.getElementById('fileNameDisplay');
        if (input.files && input.files[0]) {
            const f = input.files[0];
            const sizeMb = (f.size / (1024 * 1024)).toFixed(2);
            display.textContent = `📄 ${f.name} (${sizeMb} MB)`;
            preview.classList.add('active');
        } else {
            preview.classList.remove('active');
        }
    }

    function clearSelectedFile() {
        const input = document.getElementById('file');
        const preview = document.getElementById('filePreviewPill');
        if (input) input.value = '';
        if (preview) preview.classList.remove('active');
    }

    // Anti double submit
    document.getElementById('formProgresTugas')?.addEventListener('submit', function() {
        const btn = document.getElementById('btnSubmitProgres');
        if (btn) {
            btn.disabled = true;
            btn.textContent = 'Mengirimkan Progres...';
        }
    });
</script>
@endpush
@endsection
