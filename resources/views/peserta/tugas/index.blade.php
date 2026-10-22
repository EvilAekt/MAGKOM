@extends('layouts.peserta')

@section('title', 'Tugas & Proyek Magang')
@section('page_title', 'Tugas & Progres Magang')

@push('styles')
<style>
    /* ========================================================
       MAGKOM GENTELELLA DESIGN SYSTEM: CLASSROOM TUGAS INDEX
       ======================================================== */
    .classroom-container {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    /* Class Banner Header Khas Gentelella MagKom */
    .classroom-banner {
        background: linear-gradient(135deg, #0641CC 0%, #0751FF 60%, #1E40AF 100%);
        border-radius: var(--radius-lg, 12px);
        padding: 28px 32px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 25px -5px rgba(7, 81, 255, 0.25);
    }

    .classroom-banner::before {
        content: 'CLASSROOM';
        position: absolute;
        right: -20px;
        bottom: -25px;
        font-size: 5rem;
        font-weight: 900;
        letter-spacing: -0.05em;
        color: rgba(255, 255, 255, 0.06);
        pointer-events: none;
        user-select: none;
    }

    .banner-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: #ffffff;
        margin: 0 0 6px 0;
        letter-spacing: -0.01em;
        line-height: 1.3;
    }

    .banner-subtitle {
        font-size: 0.9375rem;
        color: rgba(255, 255, 255, 0.92);
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        line-height: 1.5;
    }

    /* Tile Stats Grid */
    .tiles-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
    }

    /* Tab Filter Navigasi */
    .classroom-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 20px;
        border-bottom: 1px solid var(--neutral-300, #CBD5E1);
        padding-bottom: 2px;
        overflow-x: auto;
    }

    .tab-btn {
        background: transparent;
        border: none;
        padding: 10px 16px;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--neutral-600, #475569);
        cursor: pointer;
        border-bottom: 3px solid transparent;
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
        font-family: inherit;
    }

    .tab-btn:hover {
        color: #0751FF;
        background: #F8FAFC;
        border-radius: 6px 6px 0 0;
    }

    .tab-btn.active {
        color: #0751FF;
        border-bottom-color: #0751FF;
        font-weight: 700;
    }

    .tab-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        background: var(--neutral-200, #E2E8F0);
        color: var(--neutral-700, #334155);
    }

    .tab-btn.active .tab-count {
        background: #EFF6FF;
        color: #0751FF;
    }

    /* Classroom Assignment Stream Cards */
    .assignment-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .assignment-card {
        background: #ffffff;
        border: 1px solid var(--neutral-300, #CBD5E1);
        border-radius: var(--radius-lg, 12px);
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
        border-color: #0751FF;
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(7, 81, 255, 0.10);
    }

    .assignment-icon-wrapper {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: #EFF6FF;
        color: #0751FF;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .assignment-card.selesai .assignment-icon-wrapper {
        background: #DCFCE7;
        color: #15803D;
    }

    .assignment-card.proses .assignment-icon-wrapper {
        background: #FEF3C7;
        color: #B45309;
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
        font-size: 1.0625rem;
        font-weight: 700;
        color: var(--neutral-900, #0F172A);
        line-height: 1.35;
        margin: 0;
    }

    .assignment-card:hover .assignment-title {
        color: #0751FF;
    }

    .assignment-due {
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--neutral-600, #475569);
        white-space: nowrap;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .assignment-due.terlambat {
        color: #DC2626;
        font-weight: 700;
    }

    .assignment-due.mendekati {
        color: #D97706;
        font-weight: 700;
    }

    .assignment-desc {
        font-size: 0.875rem;
        color: var(--neutral-600, #475569);
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
        border-top: 1px solid var(--neutral-200, #E2E8F0);
    }

    .assignment-meta-tags {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        font-size: 0.75rem;
        color: var(--neutral-500, #64748B);
    }

    .assignment-tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #F1F5F9;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--neutral-700, #334155);
    }

    /* Dark Mode Harmonization */
    [data-theme="dark"] .assignment-card {
        background: #182234 !important;
        border-color: #26354D !important;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.25) !important;
    }

    [data-theme="dark"] .assignment-card:hover {
        border-color: #0751FF !important;
        box-shadow: 0 8px 24px rgba(7, 81, 255, 0.2) !important;
    }

    [data-theme="dark"] .assignment-title {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .assignment-desc,
    [data-theme="dark"] .tab-btn {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .assignment-tag {
        background: #0F172A !important;
        color: #CBD5E1 !important;
        border: 1px solid #26354D;
    }

    [data-theme="dark"] .assignment-footer,
    [data-theme="dark"] .classroom-tabs {
        border-color: #26354D !important;
    }

    [data-theme="dark"] .tab-btn:hover {
        background: #0F172A !important;
        color: #60A5FA !important;
    }

    [data-theme="dark"] .tab-btn.active {
        color: #60A5FA !important;
        border-bottom-color: #60A5FA !important;
    }

    [data-theme="dark"] .tab-count {
        background: #0F172A !important;
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .tab-btn.active .tab-count {
        background: rgba(30, 58, 138, 0.4) !important;
        color: #93C5FD !important;
    }
</style>
@endpush

@section('content')
<div class="classroom-container">

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

    {{-- 1. Classroom Banner Header --}}
    <div class="classroom-banner">
        <h1 class="banner-title">Tugas & Proyek Magang</h1>
        <div class="banner-subtitle">
            <span>Bidang: <strong>{{ $penempatan->bidang->nama_bidang ?? 'Dinas Komunikasi dan Informatika Sleman' }}</strong></span>
            <span>&bull;</span>
            <span>Pembimbing: <strong>{{ $penempatan->pembimbing->nama ?? $penempatan->pembimbing->name ?? 'Pembimbing Lapangan' }}</strong></span>
        </div>
    </div>

    {{-- 2. Tile Stats MagKom Gentelella --}}
    <div class="tiles-grid">
        <x-gentelella.tile-stat
            title="Total Tugas"
            value="{{ $totalSemua }}"
            subtitle="Seluruh penugasan"
            color="primary">
            <x-slot:icon>
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <x-gentelella.tile-stat
            title="Ditugaskan"
            value="{{ $totalBelum }}"
            subtitle="Perlu dikerjakan"
            color="info">
            <x-slot:icon>
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <x-gentelella.tile-stat
            title="Sedang Dikerjakan"
            value="{{ $totalProses }}"
            subtitle="Dalam pengerjaan"
            color="warning">
            <x-slot:icon>
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <x-gentelella.tile-stat
            title="Selesai"
            value="{{ $totalSelesai }}"
            subtitle="Tuntas diverifikasi"
            color="success">
            <x-slot:icon>
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>
    </div>

    {{-- 3. X-Panel Daftar Penugasan & Stream --}}
    <x-gentelella.x-panel title="Daftar Penugasan & Proyek Magang" subtitle="Tinjau materi instruksi dan serahkan laporan progres pengerjaan tugas kepada pembimbing.">
        {{-- Tab Filter Navigasi Ala Google Classroom --}}
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

        {{-- Daftar Assignment Stream --}}
        @if($tugasList->isEmpty())
            <div style="text-align: center; padding: 56px 24px; border: 1px dashed var(--neutral-300, #CBD5E1); border-radius: 8px;">
                <div style="width: 64px; height: 64px; border-radius: 50%; background: #EFF6FF; color: #0751FF; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto;">
                    <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
                <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-800, #1E293B); margin-bottom: 6px;">
                    Belum Ada Tugas Ditugaskan
                </h3>
                <p style="font-size: 0.875rem; color: var(--neutral-600, #475569); max-width: 480px; margin: 0 auto;">
                    Pembimbing lapangan belum memberikan penugasan baru. Anda dapat memeriksa panduan logbook mingguan atau berkonsultasi dengan pembimbing.
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
                                        <span style="color: #DC2626; font-weight: 700;">⚠️ Terlambat: {{ $deadline->translatedFormat('d M Y') }}</span>
                                    @elseif($isMendekati)
                                        <span style="color: #D97706; font-weight: 700;">⏳ Tenggat: {{ $deadline->translatedFormat('d M Y') }}</span>
                                    @else
                                        <span>Tenggat: {{ $deadline->translatedFormat('d M Y') }}</span>
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
                                        <span class="assignment-tag" style="background: #EFF6FF; color: #1D4ED8;">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                            </svg>
                                            {{ $tugas->progresLogs->count() }} Laporan Diserahkan
                                        </span>
                                    @endif
                                </div>

                                <div style="display: flex; align-items: center; gap: 10px;">
                                    @if($isSelesai)
                                        <x-gentelella.badge variant="success">✓ Selesai</x-gentelella.badge>
                                    @elseif($isProses)
                                        <x-gentelella.badge variant="warning">● Sedang Dikerjakan</x-gentelella.badge>
                                    @else
                                        <x-gentelella.badge variant="info">● Ditugaskan</x-gentelella.badge>
                                    @endif

                                    <span style="font-size: 0.8125rem; font-weight: 600; color: #0751FF;">
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
    </x-gentelella.x-panel>

</div>

@push('scripts')
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
@endpush
@endsection
