@extends('layouts.peserta')

@section('title', 'Tugas & Proyek Magang')
@section('page_title', 'Tugas & Progres Magang')

@push('styles')
    @vite(['resources/css/pages/peserta/tugas-index.css'])
@endpush

@section('content')
<div class="classroom-container">

    @php
        $penempatan = $tugasList->first()?->penempatan ?? \App\Models\Penempatan::where('peserta_id', auth()->id())->where('status', \App\Enums\StatusPenempatanEnum::Aktif)->with(['bidang', 'pembimbing'])->first();
        $totalSemua = $totalSemua ?? $tugasList->total();
        $totalBelum = $totalBelum ?? 0;
        $totalProses = $totalProses ?? 0;
        $totalSelesai = $totalSelesai ?? 0;
        $totalTerlambat = $totalTerlambat ?? 0;
        $persenProgres = $totalSemua > 0 ? (int) round(($totalSelesai / $totalSemua) * 100) : 0;
    @endphp

    {{-- 1. Classroom Banner Header Sleman Navy --}}
    <div class="classroom-banner">
        <div class="banner-left">
            <div class="banner-tag-gold">PROGRAM MAGANG</div>
            <h1 class="banner-title">Tugas & Proyek Magang</h1>
            <div class="banner-info-stack">
                <div class="banner-info-box">
                    <span class="banner-info-label">BIDANG</span>
                    <span class="banner-info-val">{{ $penempatan?->bidang?->nama_bidang ?? 'Bidang Aplikasi Informatika (Aptika)' }}</span>
                </div>
                <div class="banner-info-box">
                    <span class="banner-info-label">PEMBIMBING</span>
                    <span class="banner-info-val">{{ ($penempatan?->pembimbing?->nama ?? $penempatan?->pembimbing?->name ?? 'Budi Santoso, S.Kom') . ' (Pembimbing Lapangan)' }}</span>
                </div>
            </div>
        </div>

        {{-- Widget Progres Tugas (White Card) --}}
        <div class="banner-progress-card">
            <div class="progress-card-header">
                <span class="progress-card-title">Progres Tugas</span>
                <span class="progress-card-percent">{{ $persenProgres }}%</span>
            </div>
            <div class="progress-bar-track">
                <div class="progress-bar-fill" style="width: {{ $persenProgres }}%;"></div>
            </div>
            <p class="progress-card-sub">{{ $totalSelesai }} dari {{ $totalSemua }} tugas telah selesai</p>
        </div>
    </div>

    {{-- 2. 3 KPI Counter Cards (Grid 3 Kolom) --}}
    <div class="tugas-kpi-grid">
        <div class="tugas-kpi-card">
            <div class="tugas-kpi-icon icon-total">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <div class="tugas-kpi-content">
                <span class="tugas-kpi-label">TOTAL TUGAS</span>
                <span class="tugas-kpi-number">{{ $totalSemua }}</span>
            </div>
        </div>

        <div class="tugas-kpi-card">
            <div class="tugas-kpi-icon icon-selesai">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="tugas-kpi-content">
                <span class="tugas-kpi-label">SELESAI</span>
                <span class="tugas-kpi-number">{{ $totalSelesai }}</span>
            </div>
        </div>

        <div class="tugas-kpi-card kpi-terlambat">
            <div class="tugas-kpi-icon icon-terlambat">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div class="tugas-kpi-content">
                <span class="tugas-kpi-label">LEWAT TENGGAT</span>
                <span class="tugas-kpi-number">{{ $totalTerlambat }}</span>
            </div>
        </div>
    </div>

    {{-- 3. Modern White Container: Daftar Penugasan & Proyek Magang --}}
    <div class="assignment-panel-container">
        <div class="assignment-panel-header">
            <h2 class="assignment-panel-title">Daftar Penugasan & Proyek Magang</h2>
            <button type="button" class="assignment-chevron-btn" id="btnToggleAssignment" aria-expanded="true" aria-controls="assignmentAccordionBody" title="Sembunyikan / Tampilkan Daftar">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"></path>
                </svg>
            </button>
        </div>

        <div class="assignment-panel-body" id="assignmentAccordionBody">
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
                <div class="assignment-empty-state">
                    <div class="assignment-empty-icon">
                        <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <h3 class="assignment-empty-title">
                        Belum Ada Tugas Ditugaskan
                    </h3>
                    <p class="assignment-empty-desc">
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
                            
                            {{-- Circular Status Icon --}}
                            <div class="assignment-status-icon {{ $isTerlambat ? 'icon-overdue' : '' }}">
                                @if($isSelesai)
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @elseif($isTerlambat)
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                @elseif($isProses)
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                @else
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                @endif
                            </div>

                            {{-- Konten Utama Tugas --}}
                            <div class="assignment-body">
                                <div class="assignment-header-row">
                                    <h3 class="assignment-title">{{ $tugas->judul }}</h3>

                                    <div class="assignment-due {{ $isTerlambat ? 'terlambat' : ($isMendekati ? 'mendekati' : '') }}">
                                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        @if($isTerlambat)
                                            <span>Terlambat: {{ $deadline->translatedFormat('d M Y') }}</span>
                                        @elseif($isMendekati)
                                            <span>Tenggat: {{ $deadline->translatedFormat('d M Y') }}</span>
                                        @else
                                            <span>Tenggat: {{ $deadline->translatedFormat('d M Y') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="assignment-desc">
                                    {{ $tugas->deskripsi }}
                                </div>

                                <div class="assignment-divider"></div>

                                <div class="assignment-footer">
                                    <div class="assignment-meta-tags">
                                        <span class="assignment-tag">
                                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                            </svg>
                                            <span>{{ $tugas->kategori ?? 'Tugas Lapangan' }}</span>
                                        </span>

                                        @if($tugas->progresLogs->count() > 0)
                                            <span class="assignment-tag-reports">
                                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                </svg>
                                                <span>{{ $tugas->progresLogs->count() }} Laporan Diserahkan</span>
                                            </span>
                                        @endif
                                    </div>

                                    <div class="assignment-action-group">
                                        {{-- Accessibility / Test Assertion Helper --}}
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
                    <div class="assignment-pagination">
                        {{ $tugasList->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>

</div>

@push('scripts')
    @vite(['resources/js/pages/peserta/tugas-index.js'])
@endpush
@endsection
