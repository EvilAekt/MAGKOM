@extends('layouts.peserta')

@section('title', 'Detail Tugas: ' . $tugas->judul)
@section('page_title', 'Detail & Progres Tugas')

@push('styles')
<style>
    .tugas-detail-grid {
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        gap: 24px;
        align-items: start;
    }
    .progres-item {
        border-left: 3px solid var(--blue-600);
        padding-left: 16px;
        margin-bottom: 20px;
        position: relative;
    }
    .progres-meta {
        font-size: 12px;
        color: var(--neutral-600);
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 4px;
    }
    .progres-content {
        font-size: 14px;
        color: var(--neutral-800);
        white-space: pre-line;
        line-height: 1.5;
    }
    .review-box {
        margin-top: 8px;
        padding: 10px 14px;
        border-radius: var(--radius-default);
        font-size: 13px;
    }
    .review-box.perlu-revisi {
        background: var(--amber-100);
        border: 1px solid var(--amber-300);
        color: var(--amber-700);
    }
    .review-box.diterima {
        background: var(--green-100);
        border: 1px solid var(--green-600);
        color: var(--green-700);
    }

    @media (max-width: 900px) {
        .tugas-detail-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div style="max-width: 1000px; margin: 0 auto;">

    <div style="margin-bottom: 18px;">
        <a href="{{ route('peserta.tugas.index') }}" style="color: var(--blue-600); text-decoration: none; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
            ← Kembali ke Daftar Tugas
        </a>
    </div>

    {{-- Kartu Instruksi Tugas --}}
    <div class="card" style="margin-bottom: 24px;">
        @php
            $isSelesai = ($tugas->status->value ?? (string)$tugas->status) === 'selesai';
            $isProses = ($tugas->status->value ?? (string)$tugas->status) === 'proses';
            $deadline = \Carbon\Carbon::parse($tugas->deadline);
            $isTerlambat = !$isSelesai && $deadline->isPast();
        @endphp

        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 14px; margin-bottom: 16px;">
            <div>
                <span style="font-size: 12px; font-weight: 700; color: var(--blue-700); text-transform: uppercase;">Penugasan Magang</span>
                <h2 style="font-size: 22px; font-weight: 700; color: var(--neutral-800); margin-top: 4px;">{{ $tugas->judul }}</h2>
                <div style="font-size: 13px; color: var(--neutral-600); margin-top: 4px;">
                    Tenggat Waktu: <strong>{{ $deadline->translatedFormat('d F Y') }}</strong>
                    @if($isTerlambat)
                        <span class="badge badge-danger" style="margin-left: 8px;">Terlambat</span>
                    @endif
                </div>
            </div>

            <div>
                @if($isSelesai)
                    <span class="badge badge-success" style="font-size: 14px; padding: 6px 14px;">✓ Status: Selesai</span>
                @elseif($isProses)
                    <span class="badge badge-info" style="font-size: 14px; padding: 6px 14px;">Status: Dalam Pengerjaan</span>
                @else
                    <span class="badge badge-neutral" style="font-size: 14px; padding: 6px 14px;">Status: Belum Dimulai</span>
                @endif
            </div>
        </div>

        <div style="padding: 16px; background: var(--neutral-100); border-radius: var(--radius-default); border: 1px solid var(--neutral-200);">
            <h4 style="font-size: 13px; font-weight: 700; color: var(--neutral-700); margin-bottom: 6px; text-transform: uppercase;">Deskripsi & Instruksi:</h4>
            <div style="font-size: 14px; color: var(--neutral-800); white-space: pre-line; line-height: 1.6;">
                {{ $tugas->deskripsi ?: 'Tidak ada deskripsi rinci yang disertakan.' }}
            </div>

            <!-- Sumber Daya Panduan dari Pembimbing -->
            @if($tugas->file_lampiran || $tugas->link_referensi)
                <div style="margin-top: 14px; padding-top: 14px; border-top: 1px solid var(--neutral-200); display: flex; flex-direction: column; gap: 10px;">
                    <h5 style="font-size: 12px; font-weight: 700; color: var(--neutral-600); text-transform: uppercase; margin: 0;">
                        Lampiran & Acuan dari Pembimbing:
                    </h5>

                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        @if($tugas->link_referensi)
                            <a href="{{ $tugas->link_referensi }}" target="_blank" rel="noopener noreferrer" style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 6px; background: #eff6ff; color: #1d4ed8; text-decoration: none; font-size: 13px; font-weight: 600; border: 1px solid #bfdbfe;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                                <span>Buka Tautan Acuan ↗</span>
                            </a>
                        @endif

                        @if($tugas->file_lampiran)
                            @php $badge = $tugas->badge_color_lampiran; @endphp
                            <a href="{{ route('peserta.tugas.lampiran', $tugas->id) }}" style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 6px; background: #ffffff; color: var(--neutral-800); text-decoration: none; font-size: 13px; font-weight: 600; border: 1px solid var(--neutral-300);">
                                <span style="padding: 1px 5px; border-radius: 4px; background: {{ $badge['bg'] }}; color: {{ $badge['color'] }}; font-size: 11px; font-weight: 800;">
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

    <div class="tugas-detail-grid">
        {{-- Riwayat Submission Progres --}}
        <div class="card">
            <div class="card-header">
                <div>
                    <h3 class="card-title">Riwayat Progres Pengerjaan</h3>
                    <p class="card-subtitle">Laporan kemajuan tugas yang telah Anda kirimkan.</p>
                </div>
            </div>

            @if($tugas->progresLogs->isEmpty())
                <div style="text-align: center; padding: 32px 16px; color: var(--neutral-600); font-size: 14px;">
                    Belum ada laporan progres yang dikirim untuk tugas ini.
                </div>
            @else
                <div>
                    @foreach($tugas->progresLogs as $progres)
                        @php
                            $rev = $progres->status_review->value ?? (string)$progres->status_review;
                        @endphp
                        <div class="progres-item">
                            <div class="progres-meta">
                                <strong>{{ \Carbon\Carbon::parse($progres->tgl_lapor)->translatedFormat('d M Y') }}</strong>
                                <span>•</span>
                                @if($rev === 'diterima')
                                    <span class="badge badge-success">Diterima</span>
                                @elseif($rev === 'perlu_revisi')
                                    <span class="badge badge-danger">Perlu Revisi</span>
                                @else
                                    <span class="badge badge-info">Menunggu Reviu</span>
                                @endif
                            </div>

                            <div class="progres-content">{{ $progres->catatan }}</div>

                            @if($progres->file)
                                <div style="margin-top: 6px;">
                                    <span style="font-size: 12px; color: var(--neutral-600);">Lampiran: Berkas telah tersimpan secara privat.</span>
                                </div>
                            @endif

                            @if($progres->catatan_review)
                                <div class="review-box {{ $rev === 'perlu_revisi' ? 'perlu-revisi' : 'diterima' }}">
                                    <strong>Catatan Pembimbing:</strong> {{ $progres->catatan_review }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Form Unggah Progres --}}
        <div class="card">
            <div class="card-header">
                <div>
                    <h3 class="card-title">Kirim Progres Baru</h3>
                    <p class="card-subtitle">Laporkan hasil pengerjaan atau update terbaru.</p>
                </div>
            </div>

            @if($isSelesai)
                <div class="alert alert-info" style="margin-bottom: 0;">
                    Tugas ini telah ditandai <strong>Selesai</strong> oleh pembimbing Anda. Formulir pengiriman ditutup.
                </div>
            @else
                <form action="{{ route('peserta.tugas.progres', $tugas) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div style="margin-bottom: 16px;">
                        <label for="catatan" class="form-label" style="font-size: 13px; font-weight: 600; color: var(--neutral-800); display: block; margin-bottom: 6px;">
                            Laporan Kemajuan Pekerjaan <span style="color: var(--red-600);">*</span>
                        </label>
                        <textarea id="catatan" name="catatan" rows="4"
                                  class="form-control"
                                  placeholder="Jelaskan apa saja yang telah Anda selesaikan, kendala, atau capaian pekerjaan..." required style="width: 100%; padding: 10px 14px; border: 1px solid var(--neutral-500); border-radius: var(--radius-default); font-size: 14px;">{{ old('catatan') }}</textarea>
                        @error('catatan')
                            <span style="font-size: 12px; color: var(--red-600);">{{ $message }}</span>
                        @enderror
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label for="file" class="form-label" style="font-size: 13px; font-weight: 600; color: var(--neutral-800); display: block; margin-bottom: 6px;">
                            Lampiran Bukti / File Pekerjaan (Opsional)
                        </label>
                        <input type="file" id="file" name="file" accept=".pdf,.zip,.rar,.doc,.docx"
                               class="form-control" style="width: 100%; font-size: 13px; padding-top: 6px;">
                        <span style="font-size: 12px; color: var(--neutral-600); display: block; margin-top: 4px;">
                            Format PDF/ZIP/DOCX, maksimal 5MB.
                        </span>
                        @error('file')
                            <span style="font-size: 12px; color: var(--red-600);">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        Kirim Laporan Progres
                    </button>
                </form>
            @endif
        </div>
    </div>

</div>
@endsection
