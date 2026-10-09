@extends('layouts.peserta')

@section('title', 'Daftar Tugas Bimbingan')
@section('page_title', 'Tugas & Progres Magang')

@push('styles')
<style>
    .tugas-card {
        border: 1px solid var(--neutral-400);
        border-radius: var(--radius-default);
        padding: 20px;
        background: #ffffff;
        margin-bottom: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .tugas-card:hover {
        border-color: var(--blue-600);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .tugas-info {
        flex: 1;
        min-width: 280px;
    }
    .tugas-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 6px;
        flex-wrap: wrap;
    }
    .tugas-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--neutral-800);
    }
    .tugas-desc {
        font-size: 13px;
        color: var(--neutral-600);
        line-height: 1.5;
        margin-bottom: 8px;
    }
    .tugas-meta {
        display: flex;
        align-items: center;
        gap: 16px;
        font-size: 12px;
        color: var(--neutral-500);
    }
</style>
@endpush

@section('content')
<div style="max-width: 960px; margin: 0 auto;">

    <div style="margin-bottom: 24px;">
        <h2 style="font-size: 20px; font-weight: 700; color: var(--neutral-800);">Tugas & Proyek Magang</h2>
        <p style="font-size: 13px; color: var(--neutral-600); margin-top: 2px;">
            Daftar penugasan dari pembimbing lapangan beserta tenggat waktu pengerjaan.
        </p>
    </div>

    @if($tugasList->isEmpty())
        <div class="card" style="text-align: center; padding: 48px 24px;">
            <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="color: var(--neutral-500); margin: 0 auto 16px auto;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
            </svg>
            <h3 style="font-size: 16px; font-weight: 700; color: var(--neutral-800); margin-bottom: 6px;">Belum Ada Tugas Diberikan</h3>
            <p style="font-size: 13px; color: var(--neutral-600);">
                Pembimbing lapangan belum memberikan penugasan baru untuk Anda.
            </p>
        </div>
    @else
        <div>
            @foreach($tugasList as $tugas)
                @php
                    $isSelesai = ($tugas->status->value ?? (string)$tugas->status) === 'selesai';
                    $isProses = ($tugas->status->value ?? (string)$tugas->status) === 'proses';
                    $deadline = \Carbon\Carbon::parse($tugas->deadline);
                    $now = now();
                    $isTerlambat = !$isSelesai && $deadline->isPast();
                    $isMendekati = !$isSelesai && !$isTerlambat && $now->diffInDays($deadline, false) <= 3;
                @endphp
                <div class="tugas-card">
                    <div class="tugas-info">
                        <div class="tugas-header">
                            <h3 class="tugas-title">{{ $tugas->judul }}</h3>

                            {{-- Status Badge --}}
                            @if($isSelesai)
                                <span class="badge badge-success">✓ Selesai</span>
                            @elseif($isProses)
                                <span class="badge badge-info">Dalam Proses</span>
                            @else
                                <span class="badge badge-neutral">Belum Dikerjakan</span>
                            @endif

                            {{-- Indikator Keterlambatan (AC-08-2) --}}
                            @if($isTerlambat)
                                <span class="badge badge-danger">⚠ Terlambat</span>
                            @elseif($isMendekati)
                                <span class="badge badge-warning">⏳ Sisa ≤ 3 Hari</span>
                            @endif
                        </div>

                        <div class="tugas-desc">
                            {{ \Illuminate\Support\Str::limit($tugas->deskripsi, 140) }}
                        </div>

                        <div class="tugas-meta">
                            <span>Deadline: <strong>{{ $deadline->translatedFormat('d F Y') }}</strong></span>
                            <span>•</span>
                            <span>Progres Terkirim: <strong>{{ $tugas->progresLogs->count() }} laporan</strong></span>
                        </div>
                    </div>

                    <div>
                        <a href="{{ route('peserta.tugas.show', $tugas) }}" class="btn btn-primary" style="height: 38px; padding: 0 16px; font-size: 13px;">
                            Lihat & Kirim Progres →
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        @if($tugasList->hasPages())
            <div style="margin-top: 24px;">
                {{ $tugasList->links() }}
            </div>
        @endif
    @endif

</div>
@endsection
