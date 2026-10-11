@extends('layouts.peserta')

@section('title', 'Tugas & Proyek Magang')
@section('page_title', 'Tugas & Progres Magang')

@push('styles')
<style>
    /* Google Classroom Style Theme */
    .classroom-container {
        max-width: 1040px;
        margin: 0 auto;
    }

    /* Class Banner Header */
    .classroom-banner {
        background: linear-gradient(95deg, rgba(6, 65, 204, 0.96) 0%, rgba(6, 65, 204, 0.86) 45%, rgba(6, 65, 204, 0.35) 100%),
                    url('{{ asset('images/classroom_banner_bg.jpg') }}') right center / cover no-repeat;
        border-radius: var(--radius-lg);
        padding: 32px 36px;
        color: #ffffff;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 6px 20px rgba(6, 65, 204, 0.16);
        min-height: 185px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .classroom-banner::after {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 85% 50%, rgba(255, 255, 255, 0.08) 0%, transparent 60%);
        pointer-events: none;
    }

    .banner-title,
    .banner-subtitle,
    .banner-stats {
        position: relative;
        z-index: 2;
    }

    .banner-title {
        font-size: var(--text-2xl);
        font-weight: var(--fw-bold);
        margin-bottom: 6px;
        letter-spacing: -0.01em;
        line-height: 1.3;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }

    .banner-subtitle {
        font-size: var(--text-base);
        color: rgba(255, 255, 255, 0.92);
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        line-height: 1.5;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }

    .banner-stats {
        display: flex;
        gap: 12px;
        margin-top: 18px;
        flex-wrap: wrap;
    }

    .banner-stat-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(15, 23, 42, 0.45);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        padding: 6px 14px;
        border-radius: var(--radius-full);
        font-size: var(--text-xs);
        font-weight: var(--fw-semibold);
        border: 1px solid rgba(255, 255, 255, 0.28);
        color: #ffffff;
    }

    /* Tab Filter Navigasi Ala Classroom */
    .classroom-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 20px;
        border-bottom: 1px solid var(--neutral-400);
        padding-bottom: 2px;
        overflow-x: auto;
    }

    .tab-btn {
        background: transparent;
        border: none;
        padding: 10px 16px;
        font-size: var(--text-base);
        font-weight: var(--fw-semibold);
        color: var(--neutral-600);
        cursor: pointer;
        border-bottom: 3px solid transparent;
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
        font-family: inherit;
    }

    .tab-btn:hover {
        color: var(--blue-700);
        background: var(--neutral-100);
        border-radius: var(--radius-default) var(--radius-default) 0 0;
    }

    .tab-btn.active {
        color: var(--blue-700);
        border-bottom-color: var(--blue-600);
        font-weight: var(--fw-bold);
    }

    .tab-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 2px 8px;
        border-radius: var(--radius-full);
        font-size: var(--text-2xs);
        font-weight: var(--fw-semibold);
        background: var(--neutral-200);
        color: var(--neutral-700);
    }

    .tab-btn.active .tab-count {
        background: var(--blue-tint);
        color: var(--blue-700);
    }

    /* Classroom Assignment Stream Cards */
    .assignment-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .assignment-card {
        background: var(--neutral-0);
        border: 1px solid var(--neutral-400);
        border-radius: var(--radius-md);
        padding: 20px 24px;
        display: flex;
        align-items: flex-start;
        gap: 18px;
        text-decoration: none;
        color: inherit;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        position: relative;
    }

    .assignment-card:hover {
        border-color: var(--blue-600);
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(7, 81, 255, 0.10);
    }

    .assignment-icon-wrapper {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: var(--blue-tint);
        color: var(--blue-600);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .assignment-card.selesai .assignment-icon-wrapper {
        background: var(--green-100);
        color: var(--green-700);
    }

    .assignment-card.proses .assignment-icon-wrapper {
        background: var(--amber-100);
        color: var(--amber-700);
    }

    .assignment-body {
        flex: 1;
        min-width: 0;
    }

    .assignment-header-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 6px;
    }

    .assignment-title {
        font-size: var(--text-lg);
        font-weight: var(--fw-bold);
        color: var(--neutral-800);
        line-height: 1.35;
    }

    .assignment-card:hover .assignment-title {
        color: var(--blue-600);
    }

    .assignment-due {
        font-size: var(--text-sm);
        font-weight: var(--fw-semibold);
        color: var(--neutral-600);
        white-space: nowrap;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .assignment-due.terlambat {
        color: var(--red-600);
        font-weight: var(--fw-bold);
    }

    .assignment-due.mendekati {
        color: var(--amber-700);
        font-weight: var(--fw-bold);
    }

    .assignment-desc {
        font-size: var(--text-base);
        color: var(--neutral-600);
        line-height: 1.5;
        margin-bottom: 12px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .assignment-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        padding-top: 12px;
        border-top: 1px solid var(--neutral-200);
    }

    .assignment-meta-tags {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        font-size: var(--text-xs);
        color: var(--neutral-500);
    }

    .assignment-tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: var(--neutral-150);
        padding: 3px 10px;
        border-radius: var(--radius-full);
        font-size: var(--text-xs);
        font-weight: var(--fw-semibold);
        color: var(--neutral-700);
    }

    .btn-action-view {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: var(--text-sm);
        font-weight: var(--fw-semibold);
        color: var(--blue-600);
    }

    @media (max-width: 768px) {
        .classroom-banner { padding: 20px; }
        .assignment-card { padding: 16px; gap: 14px; }
        .assignment-header-row { flex-direction: column; gap: 4px; }
    }
</style>
@endpush

@section('content')
<div class="classroom-container">

    {{-- 1. Classroom Class Banner Header --}}
    @php
        $penempatan = $tugasList->first()?->penempatan;
        $totalSemua = $tugasList->total();
        $totalBelum = 0;
        $totalProses = 0;
        $totalSelesai = 0;

        foreach($tugasList as $item) {
            $st = $item->status->value ?? (string)$item->status;
            if ($st === 'selesai') $totalSelesai++;
            elseif ($st === 'proses') $totalProses++;
            else $totalBelum++;
        }
    @endphp

    <div class="classroom-banner">
        <h1 class="banner-title">Tugas & Proyek Magang</h1>
        <div class="banner-subtitle">
            <span>Bidang: <strong>{{ $penempatan->bidang->nama_bidang ?? 'Dinas Komunikasi dan Informatika Sleman' }}</strong></span>
            <span>&bull;</span>
            <span>Pembimbing: <strong>{{ $penempatan->pembimbing->nama ?? $penempatan->pembimbing->name ?? 'Pembimbing Lapangan' }}</strong></span>
        </div>

        <div class="banner-stats">
            <div class="banner-stat-chip">
                <span>Total: <strong>{{ $totalSemua }} Tugas</strong></span>
            </div>
            <div class="banner-stat-chip">
                <span>Ditugaskan: <strong>{{ $totalBelum }}</strong></span>
            </div>
            <div class="banner-stat-chip">
                <span>Sedang Dikerjakan: <strong>{{ $totalProses }}</strong></span>
            </div>
            <div class="banner-stat-chip" style="background: rgba(22, 163, 74, 0.25); border-color: rgba(34, 197, 94, 0.4);">
                <span>✓ Selesai: <strong>{{ $totalSelesai }}</strong></span>
            </div>
        </div>
    </div>

    {{-- 2. Tab Filter Ala Google Classroom --}}
    <div class="classroom-tabs" role="tablist">
        <button class="tab-btn active" onclick="filterTugas('all', this)">
            Semua Tugas
            <span class="tab-count">{{ $totalSemua }}</span>
        </button>
        <button class="tab-btn" onclick="filterTugas('belum', this)">
            Ditugaskan
            <span class="tab-count">{{ $totalBelum }}</span>
        </button>
        <button class="tab-btn" onclick="filterTugas('proses', this)">
            Sedang Dikerjakan
            <span class="tab-count">{{ $totalProses }}</span>
        </button>
        <button class="tab-btn" onclick="filterTugas('selesai', this)">
            Selesai
            <span class="tab-count">{{ $totalSelesai }}</span>
        </button>
    </div>

    {{-- 3. Daftar Assignment Stream --}}
    @if($tugasList->isEmpty())
        <div class="card" style="text-align: center; padding: 56px 24px; border: 1px dashed var(--neutral-400);">
            <div style="width: 64px; height: 64px; border-radius: 50%; background: var(--blue-tint); color: var(--blue-600); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto;">
                <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
            </div>
            <h3 style="font-size: 18px; font-weight: 700; color: var(--neutral-800); margin-bottom: 6px;">
                Belum Ada Tugas Ditugaskan
            </h3>
            <p style="font-size: 14px; color: var(--neutral-600); max-width: 480px; margin: 0 auto;">
                Pembimbing lapangan belum memberikan penugasan baru. Anda dapat memeriksa panduan logbook harian atau berkonsultasi dengan pembimbing.
            </p>
        </div>
    @else
        <div class="assignment-list" id="assignmentList">
            @foreach($tugasList as $tugas)
                @php
                    $statusVal = $tugas->status->value ?? (string)$tugas->status;
                    $isSelesai = $statusVal === 'selesai';
                    $isProses = $statusVal === 'proses';
                    $deadline = \Carbon\Carbon::parse($tugas->deadline);
                    $now = now();
                    $isTerlambat = !$isSelesai && $deadline->isPast();
                    $isMendekati = !$isSelesai && !$isTerlambat && $now->diffInDays($deadline, false) <= 3;
                @endphp

                <a href="{{ route('peserta.tugas.show', $tugas) }}" 
                   class="assignment-card {{ $statusVal }}"
                   data-status="{{ $statusVal }}">
                    
                    {{-- Icon Classroom Tugas --}}
                    <div class="assignment-icon-wrapper">
                        @if($isSelesai)
                            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                            </svg>
                        @elseif($isProses)
                            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        @else
                            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        @endif
                    </div>

                    {{-- Konten Utama Tugas --}}
                    <div class="assignment-body">
                        <div class="assignment-header-row">
                            <h3 class="assignment-title">{{ $tugas->judul }}</h3>

                            <div class="assignment-due {{ $isTerlambat ? 'terlambat' : ($isMendekati ? 'mendekati' : '') }}">
                                @if($isTerlambat)
                                    <span>⚠️ Terlambat: {{ $deadline->isoFormat('D MMM Y') }}</span>
                                @elseif($isMendekati)
                                    <span>⏳ Tenggat: {{ $deadline->isoFormat('D MMM Y') }}</span>
                                @else
                                    <span>Tenggat: {{ $deadline->isoFormat('D MMM Y') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="assignment-desc">
                            {{ $tugas->deskripsi }}
                        </div>

                        <div class="assignment-footer">
                            <div class="assignment-meta-tags">
                                <span class="assignment-tag">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    {{ $tugas->kategori ?? 'Tugas Lapangan' }}
                                </span>

                                @if($tugas->progresLogs->count() > 0)
                                    <span class="assignment-tag" style="background: var(--blue-tint); color: var(--blue-700);">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                        </svg>
                                        {{ $tugas->progresLogs->count() }} Laporan Diserahkan
                                    </span>
                                @endif
                            </div>

                            <div style="display: flex; align-items: center; gap: 10px;">
                                {{-- Status Chip Ala Classroom --}}
                                @if($isSelesai)
                                    <span class="badge badge-success">✓ Selesai</span>
                                @elseif($isProses)
                                    <span class="badge badge-warning">● Sedang Dikerjakan</span>
                                @else
                                    <span class="badge badge-info">● Ditugaskan</span>
                                @endif

                                <span class="btn-action-view">
                                    Buka Tugas &rarr;
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        @if($tugasList->hasPages())
            <div style="margin-top: 24px;">
                {{ $tugasList->links() }}
            </div>
        @endif
    @endif

</div>

<script>
    function filterTugas(status, btn) {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const cards = document.querySelectorAll('.assignment-card');
        cards.forEach(card => {
            if (status === 'all') {
                card.style.display = 'flex';
            } else {
                const cardStatus = card.getAttribute('data-status');
                card.style.display = (cardStatus === status) ? 'flex' : 'none';
            }
        });
    }
</script>
@endsection
