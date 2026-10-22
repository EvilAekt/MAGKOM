@extends('layouts.peserta')

@section('title', 'Tugas: ' . $tugas->judul)
@section('page_title', 'Detail Tugas Bimbingan')

@push('styles')
<style>
    /* ========================================================
       MAGKOM GENTELELLA DESIGN SYSTEM: CLASSROOM TUGAS SHOW
       ======================================================== */
    .classroom-assignment-container {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    /* 2 Kolom Asimetris Google Classroom (65% : 35%) */
    .classroom-assignment-grid {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 24px;
        align-items: start;
    }

    .assignment-header-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--neutral-200, #E2E8F0);
        margin-bottom: 20px;
    }

    .classroom-icon-circle {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #EFF6FF;
        color: #0751FF;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 2px 6px rgba(7, 81, 255, 0.15);
    }

    .meta-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        background: #F1F5F9;
        border: 1px solid #CBD5E1;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--neutral-700, #334155);
    }

    .instruction-body-text {
        font-size: 0.9375rem;
        line-height: 1.7;
        color: var(--neutral-800, #1E293B);
        white-space: pre-line;
        background: #FAFBFD;
        border: 1px solid #E2E8F0;
        border-radius: 10px;
        padding: 20px 24px;
    }

    .ref-link-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 6px;
        background: #EFF6FF;
        color: #1D4ED8;
        text-decoration: none;
        font-size: 0.8125rem;
        font-weight: 600;
        border: 1px solid #BFDBFE;
        transition: all 0.2s ease;
    }

    .ref-link-pill:hover {
        background: #DBEAFE;
    }

    .file-lampiran-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 6px;
        background: #ffffff;
        color: var(--neutral-800, #1E293B);
        text-decoration: none;
        font-size: 0.8125rem;
        font-weight: 600;
        border: 1px solid #CBD5E1;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        transition: all 0.2s ease;
    }

    .submission-timeline-item {
        border: 1px solid var(--neutral-300, #CBD5E1);
        background: #ffffff;
        border-radius: 10px;
        padding: 18px 22px;
        margin-bottom: 16px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
    }

    .submission-avatar-mini {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #0751FF;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .feedback-bubble-box {
        margin-top: 14px;
        padding: 14px 18px;
        background: #FEF3C7;
        border-left: 4px solid #D97706;
        border-radius: 8px;
        font-size: 0.875rem;
        color: #92400E;
        line-height: 1.5;
    }

    .your-work-panel {
        position: sticky;
        top: 84px;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

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
        border-color: #0751FF;
        background: #E6EEFF;
    }

    .file-preview-pill {
        display: none;
        align-items: center;
        justify-content: space-between;
        padding: 8px 12px;
        background: #EFF6FF;
        border: 1px solid #BFDBFE;
        border-radius: 6px;
        font-size: 0.75rem;
        color: #1E40AF;
        margin-top: 8px;
        font-weight: 600;
    }

    .file-preview-pill.active {
        display: flex;
    }

    @media (max-width: 1024px) {
        .classroom-assignment-grid {
            grid-template-columns: 1fr;
        }
        .your-work-panel {
            position: static;
        }
    }

    /* Dark Mode Harmonization */
    [data-theme="dark"] .instruction-body-text {
        background: #0F172A !important;
        border-color: #26354D !important;
        color: #E2E8F0 !important;
    }

    [data-theme="dark"] .meta-chip {
        background: #0F172A !important;
        border-color: #26354D !important;
        color: #CBD5E1 !important;
    }

    [data-theme="dark"] .submission-timeline-item {
        background: #0F172A !important;
        border-color: #26354D !important;
    }

    [data-theme="dark"] .file-upload-box {
        background: #0F172A !important;
        border-color: #26354D !important;
    }

    [data-theme="dark"] .file-upload-box:hover {
        background: #182234 !important;
        border-color: #0751FF !important;
    }

    [data-theme="dark"] .file-lampiran-pill {
        background: #0F172A !important;
        border-color: #26354D !important;
        color: #CBD5E1 !important;
    }
</style>
@endpush

@section('content')
<div class="classroom-assignment-container">

    {{-- Breadcrumb Navigasi Bersih --}}
    <div>
        <a href="{{ route('peserta.tugas.index') }}" class="btn btn-secondary" style="border-radius: 999px;">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
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
        <div style="display: flex; flex-direction: column; gap: 24px;">

            {{-- 1. Lembar Penugasan Utama --}}
            <x-gentelella.x-panel title="Lembar Penugasan Magang" subtitle="Diterbitkan oleh pembimbing lapangan kedinasan">
                <div class="assignment-header-row">
                    <div style="display: flex; align-items: flex-start; gap: 16px; flex: 1;">
                        <div class="classroom-icon-circle">
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 style="font-size: 1.375rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0 0 8px 0;">
                                {{ $tugas->judul }}
                            </h1>
                            <div style="display: flex; align-items: center; flex-wrap: wrap; gap: 10px; font-size: 0.8125rem; color: var(--neutral-600, #475569);">
                                <span>Pembimbing: <strong>{{ $mentorNama }}</strong></span>
                                <span>&bull;</span>
                                <span class="meta-chip">📁 {{ $kategoriNama }}</span>
                                <span class="meta-chip">⚡ Kompleksitas: {{ $kompleksitasNama }}</span>
                            </div>
                        </div>
                    </div>

                    <div style="text-align: right; flex-shrink: 0;">
                        <div style="font-size: 0.6875rem; text-transform: uppercase; font-weight: 700; color: var(--neutral-500, #64748B); letter-spacing: 0.04em; margin-bottom: 4px;">
                            Tenggat Waktu
                        </div>
                        <div style="font-size: 0.9375rem; font-weight: 800; display: flex; align-items: center; justify-content: flex-end; gap: 6px; color: {{ $isTerlambat ? '#DC2626' : 'var(--neutral-800, #1E293B)' }};">
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>{{ $deadline->translatedFormat('d M Y') }}</span>
                        </div>
                        @if($isTerlambat)
                            <span style="font-size: 0.6875rem; color: #DC2626; font-weight: 700;">(Lewat Tenggat)</span>
                        @endif
                    </div>
                </div>

                {{-- Panduan Pengerjaan --}}
                <div>
                    <div style="display: flex; align-items: center; gap: 8px; font-size: 0.75rem; font-weight: 700; color: var(--neutral-500, #64748B); text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 10px;">
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
                        <div style="margin-top: 16px; padding-top: 14px; border-top: 1px dashed var(--neutral-200, #E2E8F0); display: flex; flex-direction: column; gap: 8px;">
                            <div style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700, #334155); text-transform: uppercase; letter-spacing: 0.04em;">
                                Lampiran & Acuan dari Pembimbing:
                            </div>

                            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                                @if($tugas->link_referensi)
                                    <a href="{{ $tugas->link_referensi }}" target="_blank" rel="noopener noreferrer" class="ref-link-pill">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                                        <span>Buka Tautan Acuan ↗</span>
                                    </a>
                                @endif

                                @if($tugas->file_lampiran)
                                    @php $badge = $tugas->badge_color_lampiran; @endphp
                                    <a href="{{ route('peserta.tugas.lampiran', $tugas->id) }}" class="file-lampiran-pill">
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
            </x-gentelella.x-panel>

            {{-- 2. Riwayat Laporan Progres & Umpan Balik --}}
            <x-gentelella.x-panel title="Riwayat Laporan Progres & Feedback" subtitle="Daftar reviu dan penilaian kemajuan berkala oleh pembimbing">
                <x-slot:toolbar>
                    <x-gentelella.badge variant="info">
                        {{ $tugas->progresLogs->count() }} Laporan Terkirim
                    </x-gentelella.badge>
                </x-slot:toolbar>

                @if($tugas->progresLogs->isEmpty())
                    <div style="text-align: center; padding: 36px 16px; border: 1px dashed var(--neutral-300, #CBD5E1); border-radius: 8px;">
                        <svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin: 0 auto 10px auto; color: var(--neutral-400, #94A3B8);">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <div style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-800, #1E293B); margin-bottom: 2px;">Belum Ada Laporan Progres</div>
                        <p style="font-size: 0.75rem; color: var(--neutral-500, #64748B); margin: 0 auto; max-width: 380px;">
                            Gunakan panel di sebelah kanan untuk mengirimkan draf pengerjaan atau hasil tugas kepada pembimbing Anda.
                        </p>
                    </div>
                @else
                    @foreach($tugas->progresLogs as $progres)
                        @php
                            $rev = $progres->status_review->value ?? (string)$progres->status_review;
                        @endphp
                        <div class="submission-timeline-item">
                            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; margin-bottom: 10px;">
                                <div style="display: flex; align-items: center; gap: 8px; font-size: 0.8125rem; font-weight: 600;">
                                    <div class="submission-avatar-mini">{{ substr(auth()->user()->name ?? 'P', 0, 1) }}</div>
                                    <span>Laporan Progres &bull; {{ \Carbon\Carbon::parse($progres->tgl_lapor)->translatedFormat('d F Y') }}</span>
                                </div>

                                <div>
                                    @if($rev === 'diterima')
                                        <x-gentelella.badge variant="success">✓ Diterima Pembimbing</x-gentelella.badge>
                                    @elseif($rev === 'perlu_revisi')
                                        <x-gentelella.badge variant="danger">⚠️ Perlu Revisi</x-gentelella.badge>
                                    @else
                                        <x-gentelella.badge variant="info">⏳ Menunggu Reviu</x-gentelella.badge>
                                    @endif
                                </div>
                            </div>

                            <div style="font-size: 0.875rem; line-height: 1.6; white-space: pre-line;">{{ $progres->catatan }}</div>

                            @if($progres->file)
                                <div style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #F1F5F9; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 0.75rem; font-weight: 600; margin-top: 10px;">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: #DC2626;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                    </svg>
                                    <span>Lampiran berkas pengerjaan terunggah di server privat</span>
                                </div>
                            @endif

                            @if($progres->link)
                                <div style="margin-top: 6px;">
                                    <a href="{{ $progres->link }}" target="_blank" rel="noopener noreferrer" style="display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 6px; background: #EFF6FF; color: #1D4ED8; font-size: 0.75rem; font-weight: 700; text-decoration: none; border: 1px solid #BFDBFE;">
                                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"></path>
                                        </svg>
                                        <span>Buka Tautan Tugas ↗</span>
                                    </a>
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
            </x-gentelella.x-panel>

        </div>

        {{-- ============================================================
             KOLOM KANAN (35%): WIDGET "TUGAS ANDA"
             ============================================================ --}}
        <div class="your-work-panel">
            <x-gentelella.x-panel title="Tugas Anda" subtitle="Serah terima pekerjaan kepada pembimbing">
                <x-slot:toolbar>
                    @if($isSelesai)
                        <x-gentelella.badge variant="success">Selesai</x-gentelella.badge>
                    @elseif($tugas->progresLogs->isNotEmpty())
                        <x-gentelella.badge variant="warning">Diserahkan</x-gentelella.badge>
                    @else
                        <x-gentelella.badge variant="info">Ditugaskan</x-gentelella.badge>
                    @endif
                </x-slot:toolbar>

                @if($isSelesai)
                    <div style="background: #ECFDF5; border: 1px solid #A7F3D0; border-radius: 8px; padding: 20px; text-align: center; color: #065F46;">
                        <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin: 0 auto 8px auto; color: #10B981;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div style="font-size: 0.9375rem; font-weight: 700; margin-bottom: 4px;">Tugas Telah Selesai</div>
                        <div style="font-size: 0.8125rem; line-height: 1.45;">Tugas ini telah ditinjau dan dinyatakan selesai oleh pembimbing lapangan Anda.</div>
                    </div>
                @else
                    <form action="{{ route('peserta.tugas.progres', $tugas) }}" method="POST" enctype="multipart/form-data" id="formProgresTugas">
                        @csrf

                        {{-- 1. Lampiran Bukti / File Tugas --}}
                        <div class="form-group" style="margin-bottom: 16px;">
                            <label class="form-label" style="font-weight: 700; display: flex; justify-content: space-between;">
                                <span>Lampiran Bukti / File Tugas</span>
                                <span style="font-size: 0.6875rem; color: var(--neutral-500, #64748B); font-weight: 400;">Maks. 5MB</span>
                            </label>

                            <input type="file" id="file" name="file" accept=".pdf,.zip,.rar,.doc,.docx" style="display: none;" onchange="handleFileSelected(this)">

                            <label for="file" class="file-upload-box" id="dropzoneBox">
                                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: #0751FF; margin: 0 auto 6px auto; display: block;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <div style="font-size: 0.8125rem; font-weight: 700; color: #0751FF;">+ Tambah Berkas Lampiran</div>
                                <div style="font-size: 0.6875rem; color: var(--neutral-500, #64748B); margin-top: 2px;">PDF, ZIP, DOCX (Maksimal 5MB)</div>
                            </label>

                            <div id="filePreviewPill" class="file-preview-pill">
                                <span id="fileNameDisplay" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 260px;"></span>
                                <button type="button" onclick="clearSelectedFile()" style="background: none; border: none; color: #DC2626; font-weight: 700; cursor: pointer; padding: 2px 6px;">✕</button>
                            </div>

                            @error('file')
                                <span style="font-size: 0.75rem; color: #DC2626; display: block; margin-top: 4px;">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- 2. Tautan Tugas / Demo --}}
                        <div class="form-group" style="margin-bottom: 16px;">
                            <label for="link" class="form-label" style="font-weight: 700; display: flex; justify-content: space-between;">
                                <span>Tautan Tugas / Repositori / Demo</span>
                                <span style="font-size: 0.6875rem; color: var(--neutral-500, #64748B); font-weight: 400;">Opsional</span>
                            </label>
                            <input type="url" id="link" name="link"
                                   class="form-control @error('link') is-invalid @enderror"
                                   placeholder="Contoh: https://github.com/... atau https://drive.google.com/..."
                                   value="{{ old('link') }}">
                            @error('link')
                                <span style="font-size: 0.75rem; color: #DC2626; display: block; margin-top: 4px;">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- 3. Catatan Progres Pekerjaan --}}
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label for="catatan" class="form-label" style="font-weight: 700;">
                                Catatan Progres Pekerjaan <span style="color: #DC2626;">*</span>
                            </label>
                            <textarea id="catatan" name="catatan" rows="4" class="form-control @error('catatan') is-invalid @enderror"
                                      placeholder="Tuliskan ringkasan hasil kerja atau kendala pengerjaan..."
                                      required>{{ old('catatan') }}</textarea>
                            @error('catatan')
                                <span style="font-size: 0.75rem; color: #DC2626; display: block; margin-top: 4px;">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" id="btnSubmitProgres" class="btn btn-primary" style="width: 100%; height: 44px; font-weight: 700;">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            Serahkan / Kirim Progres
                        </button>
                    </form>
                @endif
            </x-gentelella.x-panel>

            {{-- Kartu Info Panduan Penilaian --}}
            <x-gentelella.card style="background: #F8FAFC; border: 1px dashed var(--neutral-300, #CBD5E1);">
                <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--neutral-600, #475569); letter-spacing: 0.04em; display: flex; align-items: center; gap: 6px; margin-bottom: 8px;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: #0751FF;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Informasi Pengerjaan
                </div>
                <div style="font-size: 0.8125rem; color: var(--neutral-600, #475569); line-height: 1.5;">
                    Setiap progres yang Anda kirimkan akan dievaluasi oleh pembimbing lapangan Anda untuk penilaian 5 rubrik kinerja magang.
                </div>
            </x-gentelella.card>
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

    document.getElementById('formProgresTugas')?.addEventListener('submit', function() {
        const btn = document.getElementById('btnSubmitProgres');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `<span style="display:inline-block;width:14px;height:14px;border:2px solid #fff;border-top-color:transparent;border-radius:50%;animation:spin 0.6s linear infinite;margin-right:8px;"></span> Mengirimkan Progres...`;
        }
    });
</script>
<style>
@keyframes spin { to { transform: rotate(360deg); } }
</style>
@endpush
@endsection
