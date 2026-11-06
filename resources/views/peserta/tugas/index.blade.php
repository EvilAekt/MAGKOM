@extends('layouts.peserta')

@section('title', 'Tugas & Proyek Magang')
@section('page_title', 'Tugas & Progres Magang')

@push('styles')
    @vite(['resources/css/pages/peserta/tugas-index.css'])
@endpush

@section('content')
<div class="classroom-container">

    @php
        $penempatan = $tugasList->first()?->penempatan;
        $totalSemua = $totalSemua ?? $tugasList->total();
        $totalBelum = $totalBelum ?? 0;
        $totalProses = $totalProses ?? 0;
        $totalSelesai = $totalSelesai ?? 0;
        $totalTerlambat = $totalTerlambat ?? 0;
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

    {{-- 2. Tile Stats MagKom Gentelella (3 Metrik Utama Selaras Tab) --}}
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
            title="Selesai"
            value="{{ $totalSelesai }}"
            subtitle="Tuntas diverifikasi"
            color="success">
            <x-slot:icon>
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <x-gentelella.tile-stat
            title="Lewat Tenggat"
            value="{{ $totalTerlambat }}"
            subtitle="Perlu segera ditindak"
            color="danger">
            <x-slot:icon>
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>
    </div>

    {{-- 3. X-Panel Daftar Penugasan & Stream --}}
    <x-gentelella.x-panel title="Daftar Penugasan & Proyek Magang">
        {{-- Tab Filter Navigasi Segmented Pill 3 Tab: Semua Tugas, Selesai, Lewat Tenggat --}}
        <div class="classroom-tabs" role="tablist" aria-label="Filter Penugasan">
            <button type="button" class="tab-pill active" data-filter="all" role="tab" aria-selected="true">
                <span>Semua Tugas</span>
                <span class="tab-pill-badge">{{ $totalSemua }}</span>
            </button>
            <button type="button" class="tab-pill" data-filter="selesai" role="tab" aria-selected="false">
                <span>Selesai</span>
                <span class="tab-pill-badge">{{ $totalSelesai }}</span>
            </button>
            <button type="button" class="tab-pill" data-filter="terlambat" role="tab" aria-selected="false">
                <span>Lewat Tenggat</span>
                <span class="tab-pill-badge {{ $totalTerlambat > 0 ? 'badge-overdue' : '' }}">{{ $totalTerlambat }}</span>
            </button>
        </div>

        {{-- Daftar Assignment Stream --}}
        @if($tugasList->isEmpty())
            <div style="text-align: center; padding: 56px 24px; border: 1px dashed var(--neutral-300, #CBD5E1); border-radius: 8px;">
                <div style="width: 64px; height: 64px; border-radius: 50%; background: #EFF6FF; color: #01347C; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto;">
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
                       class="assignment-card {{ $statusVal }} {{ $isTerlambat ? 'terlambat' : '' }}"
                       data-status="{{ $statusVal }}"
                       data-is-overdue="{{ $isTerlambat ? '1' : '0' }}">
                        
                        {{-- Icon Classroom Tugas --}}
                        <div class="assignment-icon-wrapper">
                            @if($isSelesai)
                                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                </svg>
                            @elseif($isTerlambat)
                                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
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
                                        <span class="assignment-due terlambat">⚠️ Terlambat: {{ $deadline->translatedFormat('d M Y') }}</span>
                                    @elseif($isMendekati)
                                        <span class="assignment-due mendekati">⏳ Tenggat: {{ $deadline->translatedFormat('d M Y') }}</span>
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

                                <div style="display: flex; align-items: center; gap: 12px;">
                                    @if($isSelesai)
                                        <x-gentelella.badge variant="success">✓ Selesai</x-gentelella.badge>
                                    @endif
                                    <span class="sr-only">Ditugaskan Sedang Dikerjakan</span>

                                    <span class="btn-tugas-action">
                                        <span>Buka Tugas</span>
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="9 18 15 12 9 6"></polyline>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Filter Empty State --}}
            <div id="filterEmptyState" class="filter-empty-state">
                <div class="filter-empty-icon">
                    <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h4 id="filterEmptyTitle" class="filter-empty-title">Tidak Ada Tugas</h4>
                <p id="filterEmptyDesc" class="filter-empty-desc">Tidak ada tugas yang sesuai dengan kategori ini.</p>
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
    @vite(['resources/js/pages/peserta/tugas-index.js'])
@endpush
@endsection
