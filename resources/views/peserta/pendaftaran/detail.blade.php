@extends('layouts.peserta')

@section('title', 'Detail & Berkas Pendaftaran Magang')
@section('page_title', 'Detail & Berkas Pendaftaran')

@push('styles')
<style>
    /* Timeline Tracker */
    .timeline-container {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin: 24px 0 28px 0;
    }
    .timeline-container::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 40px;
        right: 40px;
        height: 3px;
        background: var(--neutral-400);
        z-index: 1;
    }
    .timeline-step {
        position: relative;
        z-index: 2;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        width: 140px;
    }
    .timeline-circle {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: #ffffff;
        border: 3px solid var(--neutral-400);
        color: var(--neutral-500);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 15px;
        margin-bottom: 8px;
        transition: all 0.2s ease;
    }
    .timeline-step.completed .timeline-circle {
        background: var(--green-600);
        border-color: var(--green-600);
        color: #ffffff;
    }
    .timeline-step.active .timeline-circle {
        background: var(--blue-600);
        border-color: var(--blue-600);
        color: #ffffff;
        box-shadow: 0 0 0 4px rgba(7, 81, 255, 0.2);
    }
    .timeline-step.warning .timeline-circle {
        background: var(--amber-700);
        border-color: var(--amber-700);
        color: #ffffff;
    }
    .timeline-step.danger .timeline-circle {
        background: var(--red-600);
        border-color: var(--red-600);
        color: #ffffff;
    }
    .timeline-label {
        font-size: 12px;
        font-weight: 700;
        color: var(--neutral-600);
    }
    .timeline-step.active .timeline-label,
    .timeline-step.completed .timeline-label {
        color: var(--neutral-800);
    }

    /* Progress Bar */
    .progress-bar-container {
        background: var(--neutral-200);
        border-radius: var(--radius-full);
        height: 8px;
        overflow: hidden;
        margin-top: 8px;
    }
    .progress-bar-fill {
        height: 100%;
        border-radius: var(--radius-full);
        transition: width 0.4s ease;
    }

    /* Document Item Cards */
    .doc-card {
        border: 1px solid var(--neutral-400);
        border-radius: var(--radius-md);
        padding: 20px;
        margin-bottom: 20px;
        background: #ffffff;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .doc-card:hover {
        border-color: var(--neutral-500);
    }
    .doc-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 12px;
    }
    .doc-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--neutral-800);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .doc-desc {
        font-size: 13px;
        color: var(--neutral-600);
        margin-top: 4px;
        line-height: 1.4;
    }

    /* Uploaded File Box */
    .uploaded-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px;
        background: var(--neutral-100);
        border: 1px solid var(--neutral-300, #E2E8F0);
        border-radius: var(--radius-default);
        gap: 14px;
        flex-wrap: wrap;
    }
    .uploaded-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .uploaded-icon {
        width: 40px;
        height: 40px;
        border-radius: var(--radius-default);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .uploaded-icon.pdf {
        background: var(--red-100);
        color: var(--red-700);
    }
    .uploaded-icon.img {
        background: var(--blue-tint);
        color: var(--blue-700);
    }

    /* Dropzone Interactive Card */
    .dropzone-box {
        border: 2px dashed var(--neutral-400);
        border-radius: var(--radius-default);
        padding: 24px 20px;
        text-align: center;
        background: #fafbfc;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
    }
    .dropzone-box:hover, .dropzone-box.dragover {
        border-color: var(--blue-600);
        background: var(--blue-tint);
    }
    .dropzone-icon {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: #ffffff;
        color: var(--blue-600);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    }
    .dropzone-text {
        font-size: 14px;
        font-weight: 600;
        color: var(--neutral-800);
    }
    .dropzone-hint {
        font-size: 12px;
        color: var(--neutral-600);
        margin-top: 4px;
    }
    .dropzone-badge {
        display: inline-block;
        margin-top: 8px;
        font-size: 11px;
        font-weight: 600;
        color: var(--neutral-600);
        background: var(--neutral-200);
        padding: 2px 8px;
        border-radius: var(--radius-full);
    }

    /* Selected File Bar */
    .selected-file-bar {
        display: none;
        align-items: center;
        justify-content: space-between;
        margin-top: 12px;
        padding: 10px 14px;
        background: #ffffff;
        border: 1px solid var(--blue-600);
        border-radius: var(--radius-default);
        font-size: 13px;
    }

    /* Modal Konfirmasi */
    .modal {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 100;
        background: rgba(29, 37, 48, 0.6);
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .modal.open {
        display: flex;
    }
    .modal-box {
        background: #ffffff;
        border-radius: var(--radius-lg);
        width: 100%;
        max-width: 520px;
        padding: 24px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    .modal-box-large {
        background: #ffffff;
        border-radius: var(--radius-lg);
        width: 100%;
        max-width: 980px;
        max-height: 92vh;
        display: flex;
        flex-direction: column;
        padding: 20px 24px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        overflow: hidden;
    }
    .modal-body-preview {
        flex: 1;
        min-height: 60vh;
        max-height: 75vh;
        position: relative;
        background: var(--neutral-100);
        border: 1px solid var(--neutral-300);
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        margin-top: 14px;
    }
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--neutral-200);
    }
    .modal-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--neutral-800);
    }
    .modal-close {
        background: none;
        border: none;
        font-size: 22px;
        cursor: pointer;
        color: var(--neutral-500);
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    @media (max-width: 768px) {
        .timeline-container {
            overflow-x: auto;
            padding-bottom: 12px;
            justify-content: flex-start;
            gap: 20px;
        }
        .timeline-container::before {
            display: none;
        }
        .uploaded-box {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endpush

@section('content')
<div style="max-width: 980px; margin: 0 auto;">

    {{-- Breadcrumb & Back --}}
    <div style="margin-bottom: 18px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <a href="{{ route('peserta.pendaftaran.index') }}" style="color: var(--blue-600); text-decoration: none; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
            ← Kembali ke Riwayat Pendaftaran
        </a>

        @php
            $val = $pendaftaran->status->value ?? (string)$pendaftaran->status;
            $bisaBatal = in_array($val, ['berkas_belum_lengkap', 'diajukan', 'perlu_perbaikan', 'waiting_list']);
            $bisaUnggah = in_array($val, ['berkas_belum_lengkap', 'perlu_perbaikan']);
        @endphp

        @if($bisaBatal)
            <button type="button" class="btn btn-secondary" onclick="openCancelModal()" style="height: 34px; padding: 0 12px; font-size: 12px; color: var(--red-700); border-color: var(--red-300);">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right: 4px;">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="15" y1="9" x2="9" y2="15"></line>
                    <line x1="9" y1="9" x2="15" y2="15"></line>
                </svg>
                Batalkan Pengajuan Ini
            </button>
        @endif
    </div>

    {{-- Header Info Kartu Pendaftaran --}}
    <div class="card" style="margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
            <div>
                <div style="font-size: 12px; font-weight: 700; color: var(--blue-700); text-transform: uppercase; letter-spacing: 0.05em;">
                    ID Registrasi: #REG-{{ str_pad((string)$pendaftaran->id, 5, '0', STR_PAD_LEFT) }}
                </div>
                <h2 style="font-size: 22px; font-weight: 700; color: var(--neutral-800); margin-top: 4px;">
                    Magang Periode {{ $pendaftaran->bulanMagang?->bulan_mulai?->translatedFormat('F Y') }}
                </h2>
                <p style="font-size: 14px; color: var(--neutral-600); margin-top: 4px;">
                    Jurusan: <strong>{{ $pendaftaran->kuota?->jurusan?->nama_jurusan }}</strong> | Durasi: <strong>{{ $pendaftaran->durasi_bulan }} Bulan</strong>
                </p>
            </div>

            <div>
                @if($val === 'berkas_belum_lengkap')
                    <span class="badge badge-neutral" style="font-size: 13px; padding: 6px 14px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-dasharray="4 4"></circle></svg>
                        Berkas Belum Lengkap
                    </span>
                @elseif($val === 'diajukan')
                    <span class="badge badge-warning" style="font-size: 13px; padding: 6px 14px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        Menunggu Verifikasi Admin
                    </span>
                @elseif($val === 'waiting_list')
                    <span class="badge badge-info" style="font-size: 13px; padding: 6px 14px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        Waiting List (Antrean Cadangan)
                    </span>
                @elseif($val === 'perlu_perbaikan')
                    <span class="badge badge-warning" style="font-size: 13px; padding: 6px 14px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                        Perlu Perbaikan Berkas
                    </span>
                @elseif($val === 'diterima')
                    <span class="badge badge-success" style="font-size: 13px; padding: 6px 14px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Diterima Magang
                    </span>
                @elseif($val === 'ditolak')
                    <span class="badge badge-danger" style="font-size: 13px; padding: 6px 14px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                        Pengajuan Ditolak
                    </span>
                @elseif($val === 'dibatalkan')
                    <span class="badge badge-neutral" style="font-size: 13px; padding: 6px 14px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                        Dibatalkan
                    </span>
                @else
                    <span class="badge badge-neutral" style="font-size: 13px; padding: 6px 14px;">{{ $val }}</span>
                @endif
            </div>
        </div>

        {{-- Timeline Tracker Status --}}
        <div class="timeline-container">
            {{-- Step 1: Draf & Berkas --}}
            <div class="timeline-step {{ $val === 'berkas_belum_lengkap' ? 'active' : 'completed' }}">
                <div class="timeline-circle">
                    @if($val === 'berkas_belum_lengkap') 1 @else ✓ @endif
                </div>
                <div class="timeline-label">1. Berkas Persyaratan</div>
            </div>

            {{-- Step 2: Diajukan / Waiting List --}}
            <div class="timeline-step {{ in_array($val, ['diajukan', 'waiting_list']) ? 'active' : (in_array($val, ['perlu_perbaikan', 'diterima', 'ditolak']) ? 'completed' : '') }}">
                <div class="timeline-circle">
                    @if(in_array($val, ['perlu_perbaikan', 'diterima', 'ditolak'])) ✓ @else 2 @endif
                </div>
                <div class="timeline-label">
                    {{ $val === 'waiting_list' ? '2. Waiting List' : '2. Verifikasi Berkas' }}
                </div>
            </div>

            {{-- Step 3: Keputusan --}}
            <div class="timeline-step {{ $val === 'perlu_perbaikan' ? 'warning' : ($val === 'diterima' ? 'completed' : ($val === 'ditolak' ? 'danger' : '')) }}">
                <div class="timeline-circle">
                    @if($val === 'diterima') ✓ @elseif($val === 'perlu_perbaikan') ! @elseif($val === 'ditolak') ✕ @else 3 @endif
                </div>
                <div class="timeline-label">
                    @if($val === 'perlu_perbaikan') Perlu Perbaikan
                    @elseif($val === 'diterima') Diterima Magang
                    @elseif($val === 'ditolak') Ditolak
                    @else 3. Keputusan @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Panel Catatan Revisi Admin (AC-03-10) --}}
    @if($val === 'perlu_perbaikan')
        <div class="alert alert-warning" style="border-left: 4px solid var(--amber-700); margin-bottom: 24px;">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--amber-700); flex-shrink: 0; margin-top: 2px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div style="flex: 1;">
                <strong style="font-size: 15px; color: var(--amber-700);">Catatan Perbaikan Berkas dari Tim Verifikator:</strong>
                <div style="margin-top: 6px; padding: 12px 14px; background: #ffffff; border-radius: var(--radius-default); border: 1px solid var(--amber-300); font-size: 14px; color: var(--neutral-800); line-height: 1.5;">
                    "{{ $pendaftaran->catatan_admin ?? 'Harap tinjau kembali berkas persyaratan dan unggah ulang dokumen yang sesuai.' }}"
                </div>
                <p style="font-size: 13px; color: var(--neutral-700); margin-top: 8px;">
                    Silakan gunakan tombol <strong>Ganti Berkas</strong> pada dokumen terkait di bawah untuk memperbarui unggahan, lalu klik tombol <strong>Ajukan Pendaftaran Ulang</strong>.
                </p>
            </div>
        </div>
    @endif

    {{-- Alert Kelulusan Diterima & Unduh Surat (AC-16-1, F-16) --}}
    @if($val === 'diterima' && $pendaftaran->penempatan)
        <div class="alert alert-success" style="border-left: 4px solid var(--green-600); margin-bottom: 24px; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width: 24px; height: 24px; color: var(--green-600); flex-shrink: 0;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <strong style="font-size: 15px; color: var(--green-800);">Selamat! Permohonan Magang Anda Telah Disetujui</strong>
                    <div style="font-size: 13px; color: var(--neutral-700); margin-top: 2px;">
                        Surat Keterangan Diterima resmi bertanda tangan digital instansi siap dilihat langsung dan diunduh.
                    </div>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <button type="button" class="btn btn-secondary" onclick="openDocPreview('{{ route('peserta.penempatan.surat-diterima.pratinjau', $pendaftaran->penempatan) }}', '{{ route('peserta.penempatan.surat-diterima', $pendaftaran->penempatan) }}', 'Surat Keterangan Diterima Magang - #REG-{{ str_pad((string)$pendaftaran->id, 5, '0', STR_PAD_LEFT) }}', 'application/pdf')" style="height: 40px; padding: 0 16px; font-size: 13px; font-weight: 700; background: #ffffff; border-color: var(--green-600); color: var(--green-800);">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right: 6px;">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    Lihat Surat Diterima
                </button>
                <a href="{{ route('peserta.penempatan.surat-diterima', $pendaftaran->penempatan) }}" class="btn btn-primary" style="height: 40px; padding: 0 16px; font-size: 13px; font-weight: 700;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right: 6px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Unduh Surat Diterima (PDF)
                </a>
            </div>
        </div>
    @endif

    {{-- Perhitungan Kelengkapan Berkas & Progress Bar --}}
    @php
        $totalWajib = $jenisDokumenList->where('wajib', true)->count();
        $uploadedWajib = $jenisDokumenList->where('wajib', true)->filter(fn($j) => $dokumenTerunggah->has($j->id))->count();
        $persen = $totalWajib > 0 ? round(($uploadedWajib / $totalWajib) * 100) : 100;
        $semuaWajibLengkap = ($uploadedWajib >= $totalWajib);
    @endphp

    {{-- Daftar Dokumen Persyaratan --}}
    <div class="card">
        <div class="card-header" style="flex-direction: column; align-items: flex-start; gap: 8px;">
            <div style="width: 100%; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
                <div>
                    <h3 class="card-title">Berkas Persyaratan Magang</h3>
                    <p class="card-subtitle">
                        Format yang diizinkan: <strong>PDF, JPG, JPEG, PNG</strong> (Maksimal {{ $maksMb }} MB per berkas).
                    </p>
                </div>
                <div style="text-align: right;">
                    <span style="font-size: 13px; font-weight: 700; color: {{ $semuaWajibLengkap ? 'var(--green-700)' : 'var(--blue-700)' }};">
                        {{ $uploadedWajib }} dari {{ $totalWajib }} Berkas Wajib ({{ $persen }}%)
                    </span>
                </div>
            </div>

            {{-- Progress Bar --}}
            <div style="width: 100%;">
                <div class="progress-bar-container">
                    <div class="progress-bar-fill" style="width: {{ $persen }}%; background: {{ $semuaWajibLengkap ? 'var(--green-600)' : 'var(--blue-600)' }};"></div>
                </div>
            </div>
        </div>

        <div>
            @foreach($jenisDokumenList as $jenis)
                @php
                    $terunggah = $dokumenTerunggah->get($jenis->id);
                    $isPdf = $terunggah && str_contains(strtolower($terunggah->mime ?? ''), 'pdf');
                @endphp

                <div class="doc-card" id="card_doc_{{ $jenis->id }}">
                    <div class="doc-card-header">
                        <div>
                            <div class="doc-title">
                                {{ $jenis->nama }}
                                @if($jenis->wajib)
                                    <span class="badge badge-danger" style="font-size: 11px; padding: 2px 8px;">Wajib</span>
                                @else
                                    <span class="badge badge-neutral" style="font-size: 11px; padding: 2px 8px;">Opsional</span>
                                @endif
                            </div>
                            <div class="doc-desc">
                                {{ $jenis->deskripsi ?? 'Dokumen resmi yang diterbitkan oleh instansi pendidikan asal.' }}
                            </div>
                        </div>

                        <div>
                            @if($terunggah)
                                <span class="badge badge-success">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                    Sudah Terunggah
                                </span>
                            @else
                                <span class="badge badge-warning">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                    Belum Diunggah
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Card Berkas yang Sudah Diunggah --}}
                    @if($terunggah)
                        @php
                            // Signed URLs 5 Menit (PRD 8.4 & TDD 5.6)
                            $downloadUrl = $terunggah->getSignedDownloadUrl(5);
                            $previewUrl = $terunggah->getSignedPreviewUrl(5);
                        @endphp

                        <div class="uploaded-box">
                            <div class="uploaded-info">
                                <div class="uploaded-icon {{ $isPdf ? 'pdf' : 'img' }}">
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
                                    <div style="font-size: 14px; font-weight: 700; color: var(--neutral-800); word-break: break-all;">
                                        {{ $terunggah->nama_asli }}
                                    </div>
                                    <div style="font-size: 12px; color: var(--neutral-600); margin-top: 2px;">
                                        {{ round($terunggah->ukuran / 1024, 1) }} KB · Diunggah pada {{ $terunggah->created_at->translatedFormat('d M Y, H:i') }}
                                    </div>
                                </div>
                            </div>

                            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                <button type="button" class="btn btn-primary" onclick="openDocPreview('{{ $previewUrl }}', '{{ $downloadUrl }}', '{{ addslashes($jenis->nama) }} - {{ addslashes($terunggah->nama_asli) }}', '{{ $terunggah->mime }}')" style="height: 36px; padding: 0 14px; font-size: 13px;" title="Lihat berkas langsung di website">
                                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right: 5px;">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    Lihat Berkas
                                </button>

                                <a href="{{ $downloadUrl }}" class="btn btn-secondary" style="height: 36px; padding: 0 12px; font-size: 13px;" title="Unduh berkas fisik ke komputer">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right: 4px;">
                                        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"></path>
                                        <polyline points="7 10 12 15 17 10"></polyline>
                                        <line x1="12" y1="15" x2="12" y2="3"></line>
                                    </svg>
                                    Unduh
                                </a>

                                @if($bisaUnggah)
                                    <button type="button" class="btn btn-secondary" onclick="toggleDropzone({{ $jenis->id }})" style="height: 36px; padding: 0 12px; font-size: 13px;">
                                        Ganti Berkas
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Interactive Drag & Drop Dropzone (Vanilla JS Native) --}}
                    @if($bisaUnggah)
                        <div id="dropzone_wrapper_{{ $jenis->id }}" style="{{ $terunggah ? 'display: none; margin-top: 14px;' : 'margin-top: 6px;' }}">
                            <form action="{{ route('peserta.berkas.unggah', $pendaftaran) }}" method="POST" enctype="multipart/form-data" id="form_upload_{{ $jenis->id }}" onsubmit="handleUploadSubmit(this, {{ $jenis->id }})">
                                @csrf
                                <input type="hidden" name="jenis_dokumen_id" value="{{ $jenis->id }}">

                                {{-- Hidden Native Input File --}}
                                <input type="file"
                                       id="file_input_{{ $jenis->id }}"
                                       name="file"
                                       accept=".pdf,.jpg,.jpeg,.png"
                                       style="display: none;"
                                       onchange="handleFileSelected(this, {{ $jenis->id }}, {{ $maksMb }})">

                                {{-- Dropzone Box --}}
                                <div class="dropzone-box"
                                     id="dropzone_box_{{ $jenis->id }}"
                                     onclick="document.getElementById('file_input_{{ $jenis->id }}').click()"
                                     ondragover="handleDragOver(event, this)"
                                     ondragleave="handleDragLeave(event, this)"
                                     ondrop="handleDrop(event, this, {{ $jenis->id }}, {{ $maksMb }})">
                                    <div class="dropzone-icon">
                                        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"></path>
                                            <polyline points="17 8 12 3 7 8"></polyline>
                                            <line x1="12" y1="3" x2="12" y2="15"></line>
                                        </svg>
                                    </div>
                                    <div class="dropzone-text">
                                        Seret & lepas berkas ke sini, atau <span style="color: var(--blue-600); text-decoration: underline;">Pilih File</span>
                                    </div>
                                    <div class="dropzone-hint">
                                        Format PDF, JPG, JPEG, atau PNG (Maksimal {{ $maksMb }} MB)
                                    </div>
                                </div>

                                {{-- Bar Preview File Terpilih & Tombol Kirim --}}
                                <div class="selected-file-bar" id="selected_bar_{{ $jenis->id }}">
                                    <div style="display: flex; align-items: center; gap: 8px; overflow: hidden;">
                                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--blue-600); flex-shrink: 0;">
                                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"></path>
                                            <polyline points="14 2 14 8 20 8"></polyline>
                                        </svg>
                                        <span id="selected_name_{{ $jenis->id }}" style="font-weight: 600; color: var(--neutral-800); text-overflow: ellipsis; white-space: nowrap; overflow: hidden;"></span>
                                        <span id="selected_size_{{ $jenis->id }}" style="font-size: 12px; color: var(--neutral-600); flex-shrink: 0;"></span>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <button type="button" class="btn btn-secondary" onclick="resetFileSelection({{ $jenis->id }})" style="height: 32px; padding: 0 10px; font-size: 12px;">
                                            Batal
                                        </button>
                                        <button type="submit" class="btn btn-primary" id="btn_submit_{{ $jenis->id }}" style="height: 32px; padding: 0 14px; font-size: 12px;">
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

        {{-- Finalisasi Pengajuan Pendaftaran (AC-03-6 & AC-03-8) --}}
        @if($bisaUnggah)
            <div style="margin-top: 28px; padding-top: 20px; border-top: 1px solid var(--neutral-200); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                <div>
                    @if($semuaWajibLengkap)
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--green-600);">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span style="color: var(--green-700); font-size: 14px; font-weight: 700;">
                                Seluruh berkas persyaratan wajib telah lengkap!
                            </span>
                        </div>
                        <div style="font-size: 12px; color: var(--neutral-600); margin-top: 2px;">
                            Klik tombol di samping untuk mengirimkan pengajuan ke tim verifikasi admin.
                        </div>
                    @else
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--amber-700);">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            <span style="color: var(--amber-700); font-size: 14px; font-weight: 700;">
                                Masih ada berkas persyaratan bertanda [Wajib] yang belum diunggah.
                            </span>
                        </div>
                        <div style="font-size: 12px; color: var(--neutral-600); margin-top: 2px;">
                            Lengkapi seluruh berkas wajib di atas untuk mengaktifkan tombol pengajuan.
                        </div>
                    @endif
                </div>

                <form action="{{ route('peserta.pendaftaran.ajukan', $pendaftaran) }}" method="POST" id="formAjukanPendaftaran" onsubmit="handleAjukanSubmit(this)">
                    @csrf
                    <button type="submit" class="btn btn-primary" id="btnAjukanPendaftaran" {{ !$semuaWajibLengkap ? 'disabled' : '' }}>
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right: 6px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        Ajukan Pendaftaran Sekarang
                    </button>
                </form>
            </div>
        @else
            <div style="margin-top: 20px; padding: 14px 18px; background: var(--neutral-100); border-radius: var(--radius-default); font-size: 13px; color: var(--neutral-600); display: flex; align-items: center; gap: 10px;">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--neutral-500); flex-shrink: 0;">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0110 0v4"></path>
                </svg>
                Berkas pendaftaran saat ini terkunci karena telah diajukan atau telah diproses oleh admin.
            </div>
        @endif
    </div>

</div>

{{-- Modal Konfirmasi Pembatalan Pendaftaran (AC-03-9 & DS 3.4) --}}
@if($bisaBatal)
<div class="modal" id="cancelModal" onclick="if(event.target === this) closeCancelModal()">
    <div class="modal-box">
        <div class="modal-header">
            <div>
                <h3 class="modal-title">Konfirmasi Pembatalan Pendaftaran</h3>
                <p style="font-size: 13px; color: var(--neutral-600); margin-top: 2px;">
                    #REG-{{ str_pad((string)$pendaftaran->id, 5, '0', STR_PAD_LEFT) }} — {{ $pendaftaran->bulanMagang?->bulan_mulai?->translatedFormat('F Y') }}
                </p>
            </div>
            <button type="button" class="modal-close" onclick="closeCancelModal()">&times;</button>
        </div>

        <form action="{{ route('peserta.pendaftaran.batalkan', $pendaftaran) }}" method="POST" id="formBatalkanPendaftaran" onsubmit="handleBatalSubmit(this)">
            @csrf

            <div style="padding: 14px 16px; background: var(--amber-100); border: 1px solid var(--amber-300); border-radius: var(--radius-default); margin-bottom: 20px;">
                <div style="display: flex; gap: 12px; align-items: flex-start;">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--amber-700); flex-shrink: 0; margin-top: 2px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div>
                        <strong style="color: var(--amber-700); font-size: 14px;">Perhatian: Pembatalan Bersifat Final</strong>
                        <p style="font-size: 13px; color: var(--neutral-800); margin-top: 4px; line-height: 1.5;">
                            Jika dibatalkan, status pendaftaran akan berubah menjadi <strong>Dibatalkan</strong> dan tidak dapat diaktifkan kembali. Sesuai aturan (BR-03), Anda tidak dapat membuat pengajuan baru pada bulan mulai yang sama, namun Anda dapat memilih bulan magang lain yang masih dibuka.
                        </p>
                    </div>
                </div>
            </div>

            <p style="font-size: 14px; color: var(--neutral-700); margin-bottom: 24px;">
                Apakah Anda yakin ingin membatalkan pengajuan pendaftaran magang ini?
            </p>

            <div style="display: flex; justify-content: flex-end; gap: 12px; padding-top: 14px; border-top: 1px solid var(--neutral-200);">
                <button type="button" class="btn btn-secondary" onclick="closeCancelModal()">Kembali</button>
                <button type="submit" class="btn btn-danger" id="btnSubmitBatal" style="background: var(--red-700); border-color: var(--red-700); color: #ffffff;">
                    Ya, Batalkan Pendaftaran
                </button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- Modal In-App Pratinjau Dokumen (AC-03-9 & DS 3.4) --}}
<div class="modal" id="docPreviewModal" onclick="if(event.target === this) closeDocPreview()" role="dialog" aria-modal="true" aria-labelledby="previewModalTitle">
    <div class="modal-box-large">
        <div class="modal-header" style="margin-bottom: 0; padding-bottom: 14px;">
            <div style="display: flex; align-items: center; gap: 10px; overflow: hidden; padding-right: 12px;">
                <div id="previewModalIcon" style="width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background: var(--blue-tint); color: var(--blue-700);">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                </div>
                <div style="overflow: hidden;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <h3 class="modal-title" id="previewModalTitle" style="font-size: 16px; margin: 0; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
                            Pratinjau Dokumen
                        </h3>
                        <span id="previewModalBadge" class="badge badge-info" style="font-size: 11px; padding: 2px 8px;">PDF</span>
                    </div>
                    <p style="font-size: 12px; color: var(--neutral-600); margin: 2px 0 0 0;">
                        Tinjauan dokumen langsung di sistem &bull; Terproteksi otorisasi MagKom
                    </p>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 8px; flex-shrink: 0;">
                <a id="previewOpenNewTabBtn" href="#" target="_blank" rel="noopener noreferrer" class="btn btn-secondary" style="height: 34px; padding: 0 10px; font-size: 12px;" title="Buka pratinjau di tab browser baru">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right: 4px;">
                        <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"></path>
                        <polyline points="15 3 21 3 21 9"></polyline>
                        <line x1="10" y1="14" x2="21" y2="3"></line>
                    </svg>
                    Tab Baru
                </a>
                <a id="previewDownloadBtn" href="#" class="btn btn-secondary" style="height: 34px; padding: 0 10px; font-size: 12px;" title="Unduh berkas fisik">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right: 4px;">
                        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    Unduh
                </a>
                <button type="button" class="modal-close" onclick="closeDocPreview()" style="font-size: 24px; padding: 0 6px;" title="Tutup pratinjau (Esc)">&times;</button>
            </div>
        </div>

        <div class="modal-body-preview">
            {{-- Loading Indicator / Skeleton --}}
            <div id="previewLoading" style="position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; background: rgba(255, 255, 255, 0.95); z-index: 10;">
                <div style="width: 38px; height: 38px; border: 3px solid var(--neutral-300); border-top-color: var(--blue-600); border-radius: 50%; animation: spin 0.7s linear infinite; margin-bottom: 12px;"></div>
                <div style="font-size: 14px; font-weight: 600; color: var(--neutral-800);">Memuat Pratinjau Dokumen...</div>
                <div style="font-size: 12px; color: var(--neutral-600); margin-top: 4px;">Mengambil stream berkas aman dari disk privat</div>
            </div>

            {{-- PDF Iframe Container --}}
            <iframe id="previewIframe" src="about:blank" style="width: 100%; height: 75vh; border: none; display: none;"></iframe>

            {{-- Image Container (JPG/PNG) --}}
            <div id="previewImageWrapper" style="display: none; padding: 24px; width: 100%; height: 100%; text-align: center; overflow: auto; justify-content: center; align-items: center;">
                <img id="previewImage" alt="Pratinjau Berkas" style="max-width: 100%; max-height: 70vh; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.12); object-fit: contain;">
            </div>

            {{-- Error State Container --}}
            <div id="previewError" style="display: none; flex-direction: column; align-items: center; justify-content: center; padding: 30px; text-align: center;">
                <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--amber-700); margin-bottom: 12px;">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <div style="font-size: 15px; font-weight: 700; color: var(--neutral-800);">Tidak Dapat Memuat Pratinjau Langsung</div>
                <div style="font-size: 13px; color: var(--neutral-600); margin: 6px 0 16px 0; max-width: 420px;">
                    Format berkas atau konfigurasi browser tidak mendukung pratinjau inline. Silakan gunakan tombol unduh di bawah ini.
                </div>
                <a id="previewFallbackDownloadBtn" href="#" class="btn btn-primary" style="height: 38px; padding: 0 16px; font-size: 13px;">
                    Unduh Berkas Sekarang
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // In-App Document Preview Modal Handler (Dual Detection: Dynamic Onload + Safety Timer)
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

        // Reset timer jika ada
        if (previewTimer) {
            clearTimeout(previewTimer);
            previewTimer = null;
        }

        // Reset state modal
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
            modalBadge.className = 'badge badge-danger';
            if (iconContainer) {
                iconContainer.style.background = 'var(--red-100)';
                iconContainer.style.color = 'var(--red-700)';
            }
            // Format URL PDF dengan parameter toolbar bersih
            const finalPdfUrl = previewUrl.includes('#') ? previewUrl : (previewUrl + '#toolbar=0&navpanes=0');
            iframe.onload = function() {
                handlePreviewLoaded();
            };
            iframe.style.display = 'block';
            iframe.src = finalPdfUrl;

            // Safety timeout: sembunyikan loading overlay dalam 500ms
            previewTimer = setTimeout(function() {
                handlePreviewLoaded();
            }, 500);
        } else {
            modalBadge.textContent = 'Gambar';
            modalBadge.className = 'badge badge-info';
            if (iconContainer) {
                iconContainer.style.background = 'var(--blue-tint)';
                iconContainer.style.color = 'var(--blue-700)';
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

    // Keyboard navigation (Esc to close preview or cancel modal)
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

        // 1. Validasi ekstensi
        if (!allowedExtensions.includes(ext)) {
            alert('Format berkas tidak diizinkan. Hanya file PDF, JPG, JPEG, atau PNG yang diterima.');
            input.value = '';
            resetFileSelection(jenisId);
            return;
        }

        // 2. Validasi batas ukuran
        if (file.size > maxBytes) {
            alert(`Ukuran berkas (${(file.size / (1024 * 1024)).toFixed(2)} MB) melebihi batas maksimal ${maksMb} MB.`);
            input.value = '';
            resetFileSelection(jenisId);
            return;
        }

        // Tampilkan Bar File Terpilih
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
