@extends('layouts.peserta')

@section('title', __('Tugas & Proyek Magang'))
@section('page_title', __('Tugas & Progres Magang'))

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
            <div class="banner-tag-gold">{{ __('PROGRAM MAGANG') }}</div>
            <h1 class="banner-title">{!! __('Tugas & Proyek Magang') !!}</h1>
            <div class="banner-info-stack">
                <div class="banner-info-box">
                    <span class="banner-info-label">{{ __('BIDANG') }}</span>
                    <span class="banner-info-val">{{ $penempatan?->bidang?->nama_bidang ?? __('Bidang Aplikasi Informatika (Aptika)') }}</span>
                </div>
                <div class="banner-info-box">
                    <span class="banner-info-label">{{ __('PEMBIMBING') }}</span>
                    <span class="banner-info-val">{{ ($penempatan?->pembimbing?->nama ?? $penempatan?->pembimbing?->name ?? 'Budi Santoso, S.Kom') . ' (' . __('Pembimbing Lapangan') . ')' }}</span>
                </div>
            </div>
        </div>

        {{-- Widget Progres Tugas (White Card) --}}
        <div class="banner-progress-card">
            <div class="progress-card-header">
                <span class="progress-card-title">{{ __('Progres Tugas') }}</span>
                <span class="progress-card-percent">{{ $persenProgres }}%</span>
            </div>
            <div class="progress-bar-track">
                <div class="progress-bar-fill" style="width: {{ $persenProgres }}%;"></div>
            </div>
            <p class="progress-card-sub">{{ __(':selesai dari :semua tugas telah selesai', ['selesai' => $totalSelesai, 'semua' => $totalSemua]) }}</p>
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
                <span class="tugas-kpi-label">{{ __('TOTAL TUGAS') }}</span>
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
                <span class="tugas-kpi-label">{{ __('SELESAI') }}</span>
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
                <span class="tugas-kpi-label">{{ __('LEWAT TENGGAT') }}</span>
                <span class="tugas-kpi-number">{{ $totalTerlambat }}</span>
            </div>
        </div>
    </div>

    {{-- Tab Filter Jenis Tugas: Semua Tugas, Tugas Tim, Tugas Individu (Di atas card Daftar Penugasan) --}}
    <div class="classroom-tipe-tabs" role="tablist" aria-label="{{ __('Filter Jenis Penugasan') }}">
        <button type="button" class="tipe-pill active" data-tipe-filter="all" role="tab" aria-selected="true">
            <span>{{ __('Semua Tugas') }}</span>
            <span class="tab-pill-badge">{{ $totalSemua }}</span>
        </button>
        <button type="button" class="tipe-pill" data-tipe-filter="kelompok" role="tab" aria-selected="false">
            <span>{{ __('Tugas Tim') }}</span>
            <span class="tab-pill-badge">{{ $totalTim ?? 0 }}</span>
        </button>
        <button type="button" class="tipe-pill" data-tipe-filter="individu" role="tab" aria-selected="false">
            <span>{{ __('Tugas Individu') }}</span>
            <span class="tab-pill-badge">{{ $totalIndividu ?? 0 }}</span>
        </button>
    </div>

    {{-- 3. Modern White Container: Daftar Penugasan & Proyek Magang --}}
    <div class="assignment-panel-container">
        <div class="assignment-panel-header">
            <h2 class="assignment-panel-title">{!! __('Daftar Penugasan & Proyek Magang') !!}</h2>
            <button type="button" class="assignment-chevron-btn" id="btnToggleAssignment" aria-expanded="true" aria-controls="assignmentAccordionBody" title="{{ __('Sembunyikan / Tampilkan Daftar') }}">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"></path>
                </svg>
            </button>
        </div>

        <div class="assignment-panel-body" id="assignmentAccordionBody">
            {{-- Tab Filter Navigasi Status: Selesai, Lewat Tenggat --}}
            <div class="classroom-tabs" role="tablist" aria-label="{{ __('Filter Penugasan') }}">
                <button type="button" class="tab-pill" data-filter="selesai" role="tab" aria-selected="false">
                    <span>{{ __('Selesai') }}</span>
                    <span class="tab-pill-badge">{{ $totalSelesai }}</span>
                </button>
                <button type="button" class="tab-pill" data-filter="terlambat" role="tab" aria-selected="false">
                    <span>{{ __('Lewat Tenggat') }}</span>
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
                        {{ __('Belum Ada Tugas Ditugaskan') }}
                    </h3>
                    <p class="assignment-empty-desc">
                        {{ __('Pembimbing lapangan belum memberikan penugasan baru. Anda dapat memeriksa panduan logbook mingguan atau berkonsultasi dengan pembimbing.') }}
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
                           class="classroom-task-row assignment-card {{ $statusVal }} {{ $isTerlambat ? 'terlambat' : '' }}"
                           data-status="{{ $statusVal }}"
                           data-tipe="{{ $tugas->tipe ?? 'individu' }}"
                           data-is-overdue="{{ $isTerlambat ? '1' : '0' }}"
                           aria-label="{{ __('Tugas: :judul. Klik untuk membuka detail.', ['judul' => $tugas->judul]) }}">
                            
                            <div class="task-row-left">
                                <div class="task-row-icon {{ $isSelesai ? 'icon-selesai' : ($isTerlambat ? 'icon-overdue' : ($isProses ? 'icon-pending' : 'icon-default')) }}" aria-hidden="true">
                                    @if($isSelesai)
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    @elseif($isTerlambat)
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                    @elseif($isProses)
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                    @else
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect><line x1="9" y1="12" x2="15" y2="12"></line><line x1="9" y1="16" x2="13" y2="16"></line></svg>
                                    @endif
                                </div>
                                <div class="task-row-title-wrap">
                                    <span class="task-row-title">{{ $tugas->judul }}</span>
                                    <div class="peserta-task-tipe-row">
                                        @if($tugas->isKelompok())
                                            <span class="peserta-tipe-badge is-tim">{{ __('Tugas Tim') }}{{ $tugas->kelompokMagang ? ' • ' . $tugas->kelompokMagang->nama_kelompok : '' }}</span>
                                        @else
                                            <span class="peserta-tipe-badge is-individu">{{ __('Tugas Individu') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="task-row-right">
                                <div class="task-row-due {{ $isTerlambat ? 'overdue' : '' }}">
                                    @if($isTerlambat)
                                        <span>⚠️ {{ __('Lewat:') }} {{ $deadline->translatedFormat('d M Y') }}</span>
                                    @elseif($tugas->deadline)
                                        <span>{{ __('Tenggat:') }} {{ $deadline->translatedFormat('d M Y') }}</span>
                                    @else
                                        <span>{{ __('Tanpa tenggat') }}</span>
                                    @endif
                                </div>

                                <div class="task-status-pill">
                                    @if($isSelesai)
                                        <x-gentelella.badge variant="success">{{ __('Selesai') }}</x-gentelella.badge>
                                    @elseif($isTerlambat)
                                        <x-gentelella.badge variant="danger">{{ __('Terlambat') }}</x-gentelella.badge>
                                    @elseif($isProses)
                                        <x-gentelella.badge variant="warning">{{ __('Sedang Dikerjakan') }}</x-gentelella.badge>
                                    @else
                                        <x-gentelella.badge variant="info">{{ __('Ditugaskan') }}</x-gentelella.badge>
                                    @endif
                                </div>

                                {{-- Screen-reader and test accessibility helper --}}
                                <span class="sr-only">{{ __('Ditugaskan') }} {{ __('Sedang Dikerjakan') }}</span>
                                <span class="sr-only btn-tugas-action">{{ __('Buka Tugas') }}</span>
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
                    <h4 id="filterEmptyTitle" class="filter-empty-title">{{ __('Tidak Ada Tugas') }}</h4>
                    <p id="filterEmptyDesc" class="filter-empty-desc">{{ __('Tidak ada tugas yang sesuai dengan filter ini.') }}</p>
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
