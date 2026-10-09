@extends('layouts.peserta')

@section('title', 'Detail Pendaftaran Magang')
@section('page_title', 'Detail & Berkas Pendaftaran')

@push('styles')
<style>
    .timeline-container {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin: 24px 0 32px 0;
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
        width: 130px;
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

    .doc-item {
        border: 1px solid var(--neutral-400);
        border-radius: var(--radius-default);
        padding: 18px;
        margin-bottom: 16px;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }
    .doc-info {
        flex: 1;
        min-width: 250px;
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
    }
    .doc-action {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .upload-form {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .file-input {
        font-size: 13px;
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
        .doc-item {
            flex-direction: column;
            align-items: flex-start;
        }
        .doc-action {
            width: 100%;
            justify-content: space-between;
        }
    }
</style>
@endpush

@section('content')
<div style="max-width: 980px; margin: 0 auto;">

    {{-- Breadcrumb & Back --}}
    <div style="margin-bottom: 18px;">
        <a href="{{ route('peserta.pendaftaran.index') }}" style="color: var(--blue-600); text-decoration: none; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
            ← Kembali ke Riwayat Pendaftaran
        </a>
    </div>

    {{-- Header Info Kartu --}}
    <div class="card" style="margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
            <div>
                <div style="font-size: 13px; font-weight: 700; color: var(--blue-700); text-transform: uppercase;">
                    ID Registrasi: #REG-{{ str_pad((string)$pendaftaran->id, 5, '0', STR_PAD_LEFT) }}
                </div>
                <h2 style="font-size: 22px; font-weight: 700; color: var(--neutral-800); margin-top: 4px;">
                    Magang Periode {{ $pendaftaran->bulanMagang?->nama_bulan }} {{ $pendaftaran->bulanMagang?->tahun }}
                </h2>
                <p style="font-size: 14px; color: var(--neutral-600); margin-top: 4px;">
                    Bidang / Jurusan: <strong>{{ $pendaftaran->kuota?->jurusan?->nama_jurusan }}</strong> | Durasi: <strong>{{ $pendaftaran->durasi_bulan }} Bulan</strong>
                </p>
            </div>

            <div>
                @php
                    $val = $pendaftaran->status->value ?? (string)$pendaftaran->status;
                @endphp
                @if($val === 'berkas_belum_lengkap')
                    <span class="badge badge-warning" style="font-size: 14px; padding: 6px 14px;">Berkas Belum Lengkap</span>
                @elseif($val === 'diajukan')
                    <span class="badge badge-info" style="font-size: 14px; padding: 6px 14px;">Diajukan (Menunggu Review)</span>
                @elseif($val === 'waiting_list')
                    <span class="badge badge-warning" style="font-size: 14px; padding: 6px 14px;">Waiting List (Antrean Cadangan)</span>
                @elseif($val === 'perlu_perbaikan')
                    <span class="badge badge-danger" style="font-size: 14px; padding: 6px 14px;">Perlu Perbaikan Berkas</span>
                @elseif($val === 'diterima')
                    <span class="badge badge-success" style="font-size: 14px; padding: 6px 14px;">Selamat! Diterima Magang</span>
                @elseif($val === 'ditolak')
                    <span class="badge badge-danger" style="font-size: 14px; padding: 6px 14px;">Pengajuan Ditolak</span>
                @else
                    <span class="badge badge-neutral" style="font-size: 14px; padding: 6px 14px;">{{ $val }}</span>
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
                    @if($val === 'perlu_perbaikan') Perbaikan Berkas
                    @elseif($val === 'diterima') Diterima Magang
                    @elseif($val === 'ditolak') Ditolak
                    @else 3. Keputusan @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Alert Catatan Khusus --}}
    @if($val === 'perlu_perbaikan')
        <div class="alert alert-error" style="border-left: 4px solid var(--red-600); margin-bottom: 24px;">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div>
                <strong>Perhatian: Berkas Anda Memerlukan Perbaikan</strong>
                <p style="margin-top: 4px;">
                    {{ $pendaftaran->catatan_admin ?? 'Silakan unggah ulang berkas yang diminta oleh admin di bawah ini.' }}
                </p>
            </div>
        </div>
    @endif

    @if($val === 'diterima' && $pendaftaran->penempatan)
        <div class="alert alert-success" style="border-left: 4px solid var(--green-600); margin-bottom: 24px; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <strong>Selamat! Permohonan Magang Anda Telah Disetujui</strong>
                    <div style="font-size: 13px; margin-top: 2px;">
                        Anda dapat mengunduh Surat Keterangan Diterima resmi bertandatangan digital di sini.
                    </div>
                </div>
            </div>
            <a href="{{ route('peserta.penempatan.surat-diterima', $pendaftaran->penempatan) }}" class="btn btn-primary" style="height: 40px; padding: 0 18px; font-size: 13px;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Unduh Surat Diterima (PDF)
            </a>
        </div>
    @endif

    {{-- Daftar Dokumen Persyaratan --}}
    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="card-title">Berkas Persyaratan Magang</h3>
                <p class="card-subtitle">
                    Unggah seluruh dokumen wajib berikut (Format PDF atau DOCX, maksimal 5MB per berkas).
                </p>
            </div>
        </div>

        @php
            $semuaWajibLengkap = true;
            $bisaUnggah = in_array($val, ['berkas_belum_lengkap', 'perlu_perbaikan']);
        @endphp

        <div>
            @foreach($jenisDokumenList as $jenis)
                @php
                    $terunggah = $dokumenTerunggah->get($jenis->id);
                    if ($jenis->wajib && !$terunggah) {
                        $semuaWajibLengkap = false;
                    }
                @endphp
                <div class="doc-item">
                    <div class="doc-info">
                        <div class="doc-title">
                            {{ $jenis->nama }}
                            @if($jenis->wajib)
                                <span class="badge badge-danger" style="font-size: 11px; padding: 2px 8px;">Wajib</span>
                            @else
                                <span class="badge badge-neutral" style="font-size: 11px; padding: 2px 8px;">Opsional</span>
                            @endif
                        </div>
                        <div class="doc-desc">
                            {{ $jenis->deskripsi ?? 'Dokumen persyaratan resmi sesuai ketentuan instansi Kominfo.' }}
                        </div>
                    </div>

                    <div class="doc-action">
                        @if($terunggah)
                            <div style="text-align: right;">
                                <span class="badge badge-success">✓ Sudah Diunggah</span>
                                <div style="font-size: 12px; color: var(--neutral-600); margin-top: 4px;">
                                    {{ round($terunggah->ukuran / 1024, 1) }} KB | {{ $terunggah->created_at->translatedFormat('d M Y') }}
                                </div>
                            </div>

                            {{-- Tombol Lihat/Unduh dengan Signed URL --}}
                            @php
                                $downloadUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
                                    'berkas.download',
                                    now()->addMinutes(30),
                                    ['dokumen' => $terunggah->id]
                                );
                            @endphp
                            <a href="{{ $downloadUrl }}" class="btn btn-secondary" style="height: 36px; padding: 0 14px; font-size: 13px;" target="_blank">
                                Unduh / Lihat File
                            </a>
                        @else
                            <span class="badge badge-warning">Belum Ada File</span>
                        @endif

                        {{-- Form Unggah / Ganti Berkas --}}
                        @if($bisaUnggah)
                            <form action="{{ route('peserta.berkas.unggah', $pendaftaran) }}" method="POST" enctype="multipart/form-data" class="upload-form">
                                @csrf
                                <input type="hidden" name="jenis_dokumen_id" value="{{ $jenis->id }}">
                                <input type="file" name="file" accept=".pdf,.doc,.docx" required style="font-size: 12px; max-width: 170px;">
                                <button type="submit" class="btn btn-primary" style="height: 36px; padding: 0 12px; font-size: 12px;">
                                    {{ $terunggah ? 'Ganti File' : 'Unggah' }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Bagian Finalisasi Pengajuan --}}
        @if($bisaUnggah)
            <div style="margin-top: 28px; padding-top: 20px; border-top: 1px solid var(--neutral-200); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                <div>
                    @if($semuaWajibLengkap)
                        <span style="color: var(--green-700); font-size: 14px; font-weight: 700;">
                            ✓ Seluruh berkas persyaratan wajib telah lengkap!
                        </span>
                        <div style="font-size: 12px; color: var(--neutral-600);">
                            Klik tombol di samping untuk mengirimkan pengajuan ke tim verifikasi admin.
                        </div>
                    @else
                        <span style="color: var(--amber-700); font-size: 14px; font-weight: 600;">
                            ⚠ Masih ada berkas persyaratan bertanda [Wajib] yang belum diunggah.
                        </span>
                    @endif
                </div>

                <form action="{{ route('peserta.pendaftaran.ajukan', $pendaftaran) }}" method="POST" onsubmit="this.querySelector('button').disabled = true">
                    @csrf
                    <button type="submit" class="btn btn-primary" {{ !$semuaWajibLengkap ? 'disabled' : '' }}>
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        Ajukan Pendaftaran Sekarang
                    </button>
                </form>
            </div>
        @else
            <div style="margin-top: 20px; padding: 14px 18px; background: var(--neutral-100); border-radius: var(--radius-default); font-size: 13px; color: var(--neutral-600);">
                ℹ Berkas pendaftaran saat ini terkunci karena telah diajukan atau telah diproses oleh admin.
            </div>
        @endif
    </div>

</div>
@endsection
