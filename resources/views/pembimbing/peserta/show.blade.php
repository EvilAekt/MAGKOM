@extends('layouts.dashboard')

@section('title', 'Profil Peserta Bimbingan: ' . ($penempatan->peserta?->nama ?? 'Peserta') . ' - Portal MagKom')
@section('topbar_title', 'Profil Peserta Bimbingan')
@section('content_area_class', 'mentee-canvas-bg')

@push('styles')
    @vite(['resources/css/pages/pembimbing/peserta-show.css'])
@endpush

@section('content')
<div class="dashboard-content mentee-profile-page">
    <h1 class="sr-only">Profil 360° Mentee Bimbingan</h1>

    {{-- 1. Top Action & Date Bar --}}
    <div class="mentee-date-action-bar">
        <div class="date-pill-amber">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            <span>{{ \Carbon\Carbon::now()->isoFormat('dddd, DD MMM Y') }}</span>
        </div>
        <a href="{{ route('pembimbing.peserta.index') }}" class="btn-back-navy" title="Kembali ke Direktori Peserta Bimbingan">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <span>Kembali</span>
        </a>
    </div>

    {{-- 2. Hero Card Profil Mentee (Navy Blue Card #0F3875) --}}
    @php
        $profil = $penempatan->peserta?->profil;
        $isSmk = $profil?->jenjang === \App\Enums\JenjangEnum::Smk;
    @endphp
    <div class="mentee-hero-card">
        <div class="mentee-hero-left">
            <div class="mentee-avatar-gold-ring">
                <span>{{ strtoupper(substr($penempatan->peserta?->nama ?? 'P', 0, 2)) }}</span>
            </div>
            <div class="mentee-hero-identity">
                <h1 class="mentee-hero-name">{{ $penempatan->peserta?->nama }}</h1>
                <p class="mentee-hero-institution">{{ $profil?->institusi ?? ($isSmk ? 'Sekolah Belum Dilengkapi' : 'Institusi Belum Dilengkapi') }}</p>
                <div class="mentee-badges-row">
                    <span class="badge-status-pill badge-status-aktif">
                        {{ ucfirst($penempatan->status->value) }}
                    </span>
                    <span class="badge-status-pill badge-jalur-outline">
                        Jalur {{ ucfirst($penempatan->jalur?->value ?? 'Umum') }}
                    </span>
                </div>
                <div class="mentee-action-buttons-row">
                    <a href="{{ route('pembimbing.logbook.index', ['masuk' => $penempatan->id]) }}" class="btn-pill-action btn-pill-logbook" title="Lihat logbook harian mentee">
                        Logbook
                    </a>
                    <a href="{{ route('pembimbing.tugas.index', ['masuk' => $penempatan->id]) }}" class="btn-pill-action btn-pill-tugas" title="Lihat tugas mentee">
                        Tugas
                    </a>
                </div>
            </div>
        </div>

        <div class="mentee-hero-right">
            <div class="data-peserta-header">DATA PESERTA</div>
            <div class="data-peserta-grid">
                <div class="data-peserta-cell">
                    <span class="data-peserta-label">EMAIL</span>
                    <span class="data-peserta-val" title="{{ $penempatan->peserta?->email }}">{{ $penempatan->peserta?->email ?? '-' }}</span>
                </div>
                <div class="data-peserta-cell">
                    <span class="data-peserta-label">TELEPON / WA</span>
                    <span class="data-peserta-val">{{ $penempatan->peserta?->no_telp ?? $profil?->no_hp ?? '-' }}</span>
                </div>
                <div class="data-peserta-cell">
                    <span class="data-peserta-label">{{ $isSmk ? 'KOMPETENSI KEAHLIAN' : 'PROGRAM STUDI' }}</span>
                    <span class="data-peserta-val">{{ $profil?->jurusan?->nama_jurusan ?? '-' }}</span>
                </div>
                <div class="data-peserta-cell">
                    <span class="data-peserta-label">{{ $isSmk ? 'NISN' : 'NIM' }}</span>
                    <span class="data-peserta-val">{{ $profil?->nim_nisn ?? '-' }}</span>
                </div>
                <div class="data-peserta-cell cell-full-width">
                    <span class="data-peserta-label">BIDANG</span>
                    <span class="data-peserta-val">{{ $penempatan->bidang?->nama_bidang ?? 'Dinas Komunikasi dan Informatika' }}</span>
                </div>
            </div>
            @if($profil?->nama_pembimbing_sekolah)
                <div class="data-peserta-dosen-note">
                    {{ ($isSmk ? 'Guru Pembimbing: ' : 'Dosen Pembimbing: ') . $profil->nama_pembimbing_sekolah }}
                </div>
            @endif
        </div>
    </div>

    {{-- 3. Baris 3 Kartu Metrik Ringkasan (3-Column KPI Cards) --}}
    <div class="mentee-kpi-summary-grid">
        {{-- KARTU 1: PROGRES TUGAS (DEEP BLUE) --}}
        <div class="kpi-card kpi-card-blue">
            <div class="kpi-card-title">PROGRES TUGAS</div>
            <div class="kpi-donut-layout">
                <div class="donut-chart-circle" style="--deg-selesai: {{ $degSelesai }}deg; --deg-proses: {{ $degProses }}deg;">
                    <div class="donut-chart-inner">
                        <span class="donut-percentage-text">{{ $completionRate }}%</span>
                    </div>
                </div>
                <div class="donut-chart-legends">
                    <div class="donut-legend-pill" title="Tugas Selesai: {{ $tugasSelesaiCount }}">
                        <div class="legend-pill-left">
                            <span class="legend-dot-indicator dot-selesai"></span>
                            <span class="legend-pill-text">Selesai</span>
                        </div>
                        <span class="legend-pill-count">{{ $tugasSelesaiCount }}</span>
                    </div>
                    <div class="donut-legend-pill" title="Tugas Dikerjakan: {{ $tugasProsesCount }}">
                        <div class="legend-pill-left">
                            <span class="legend-dot-indicator dot-dikerjakan"></span>
                            <span class="legend-pill-text">Dikerjakan</span>
                        </div>
                        <span class="legend-pill-count">{{ $tugasProsesCount }}</span>
                    </div>
                    <div class="donut-legend-pill" title="Tugas Belum Dikerjakan: {{ $tugasBelumCount }}">
                        <div class="legend-pill-left">
                            <span class="legend-dot-indicator dot-belum"></span>
                            <span class="legend-pill-text">Belum</span>
                        </div>
                        <span class="legend-pill-count">{{ $tugasBelumCount }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- KARTU 2: PERIODE MAGANG (VIBRANT YELLOW) --}}
        <div class="kpi-card kpi-card-yellow">
            <div class="kpi-card-title">PERIODE MAGANG</div>
            <div class="periode-metric-huge">{{ max(0, $sisaHari) }}</div>
            <div class="periode-subtitle">hari tersisa dari {{ $totalHariMagang }}</div>
            <div class="periode-progress-track">
                <div class="periode-progress-fill" style="width: {{ min(100, $progresHari) }}%;"></div>
            </div>
            <div class="periode-dates-row">
                <span>{{ $penempatan->tgl_mulai?->format('d M Y') ?? '-' }}</span>
                <span>{{ $penempatan->tgl_selesai?->format('d M Y') ?? '-' }}</span>
            </div>
        </div>

        {{-- KARTU 3: LOGBOOK (PURE WHITE) --}}
        <div class="kpi-card kpi-card-white">
            <div class="kpi-card-header-flex">
                <span class="kpi-card-title">LOGBOOK</span>
                <span class="logbook-progress-summary">{{ $logbookFilledCount }} dari 12 minggu</span>
            </div>
            <div class="logbook-weeks-grid-12">
                @for($w = 1; $w <= 12; $w++)
                    @php
                        $mInfo = $logbookPerMinggu[$w - 1] ?? null;
                        $boxClass = 'box-pending';
                        if ($mInfo) {
                            if ($mInfo['status'] === 'disetujui') {
                                $boxClass = 'box-selesai';
                            } elseif ($mInfo['status'] === 'perlu_revisi' || ($mInfo['is_lampau'] && $mInfo['status'] === 'belum_diisi')) {
                                $boxClass = 'box-terlambat';
                            }
                        }
                    @endphp
                    <div class="logbook-week-box {{ $boxClass }}" title="Minggu ke-{{ $w }}: {{ $mInfo ? ucfirst(str_replace('_', ' ', $mInfo['status'])) : 'Mendatang' }}">
                        {{ $w }}
                    </div>
                @endfor
            </div>
            <div class="logbook-legends-row">
                <span class="logbook-legend-entry">
                    <span class="legend-color-dot dot-logbook-selesai"></span>
                    <span>Selesai <strong>{{ $logbookDisetujuiCount }}</strong></span>
                </span>
                <span class="logbook-legend-entry">
                    <span class="legend-color-dot dot-logbook-terlambat"></span>
                    <span>Terlambat <strong>{{ $logbookTerlambatCount }}</strong></span>
                </span>
            </div>
        </div>
    </div>

    {{-- 4. Riwayat Penugasan (Kanban Task Board) --}}
    <div class="mentee-kanban-board-card">
        <div class="kanban-header-row">
            <h2 class="kanban-main-title">Riwayat Penugasan</h2>
            <a href="{{ route('pembimbing.tugas.index', ['masuk' => $penempatan->id]) }}" class="btn-kelola-tugas">
                <span>Kelola Tugas</span>
                <span class="arrow-symbol">&rarr;</span>
            </a>
        </div>

        {{-- Segmented Progress Bar & Legend --}}
        <div class="kanban-segmented-wrapper">
            <div class="kanban-legend-status-row">
                <div class="kanban-legend-item">
                    <span class="legend-color-dot dot-kanban-belum"></span>
                    <span>Belum <strong>{{ $tugasBelumCount }}</strong></span>
                </div>
                <div class="kanban-legend-item">
                    <span class="legend-color-dot dot-kanban-proses"></span>
                    <span>Proses <strong>{{ $tugasProsesCount }}</strong></span>
                </div>
                <div class="kanban-legend-item">
                    <span class="legend-color-dot dot-kanban-selesai"></span>
                    <span>Selesai <strong>{{ $tugasSelesaiCount }}</strong></span>
                </div>
            </div>
            <div class="kanban-segmented-bar">
                <div class="segment-fill segment-belum" style="width: {{ $persenBelum }}%;" title="Belum: {{ $tugasBelumCount }} ({{ $persenBelum }}%)"></div>
                <div class="segment-fill segment-proses" style="width: {{ $persenProses }}%;" title="Proses: {{ $tugasProsesCount }} ({{ $persenProses }}%)"></div>
                <div class="segment-fill segment-selesai" style="width: {{ $persenSelesai }}%;" title="Selesai: {{ $tugasSelesaiCount }} ({{ $persenSelesai }}%)"></div>
            </div>
        </div>

        {{-- 3 Kolom Kanban Board --}}
        <div class="kanban-columns-grid">
            {{-- KOLOM 1: BELUM --}}
            <div class="kanban-column">
                <div class="kanban-column-pill">
                    <div class="column-pill-title">
                        <span class="status-indicator-dot dot-belum"></span>
                        <span>Belum</span>
                    </div>
                    <span class="column-pill-count">{{ $tugasBelumCount }}</span>
                </div>
                <div class="kanban-cards-stack">
                    @forelse($tugasBelum as $tugas)
                        <div class="kanban-task-card card-style-belum">
                            <h3 class="task-card-title">{{ $tugas->judul }}</h3>
                            <p class="task-card-subtitle">{{ $tugas->kategori }} &bull; {{ $tugas->progresLogs->count() }} laporan progres</p>
                            <div class="task-card-footer">
                                <span class="task-card-date">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                    <span>{{ $tugas->deadline?->format('d M Y') ?? '-' }}</span>
                                </span>
                                <a href="{{ route('pembimbing.tugas.show', $tugas->id) }}" class="btn-card-action-outline">
                                    Detail
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="kanban-card-empty">
                            <span>Tidak ada tugas berstatus belum dikerjakan.</span>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- KOLOM 2: PROSES --}}
            <div class="kanban-column">
                <div class="kanban-column-pill">
                    <div class="column-pill-title">
                        <span class="status-indicator-dot dot-proses"></span>
                        <span>Proses</span>
                    </div>
                    <span class="column-pill-count">{{ $tugasProsesCount }}</span>
                </div>
                <div class="kanban-cards-stack">
                    @forelse($tugasProses as $tugas)
                        <div class="kanban-task-card card-style-proses">
                            <h3 class="task-card-title">{{ $tugas->judul }}</h3>
                            <p class="task-card-subtitle">{{ $tugas->kategori }} &bull; {{ $tugas->progresLogs->count() }} laporan progres</p>
                            <div class="task-card-footer">
                                <span class="task-card-date {{ $tugas->deadline && $tugas->deadline->isToday() ? 'is-today' : '' }}">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                    <span>{{ $tugas->deadline?->format('d M Y') ?? '-' }}{{ $tugas->deadline && $tugas->deadline->isToday() ? ' • Hari ini' : '' }}</span>
                                </span>
                                <a href="{{ route('pembimbing.tugas.show', $tugas->id) }}" class="btn-card-action-dark">
                                    Detail
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="kanban-card-empty">
                            <span>Tidak ada tugas dalam proses pengerjaan.</span>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- KOLOM 3: SELESAI --}}
            <div class="kanban-column">
                <div class="kanban-column-pill">
                    <div class="column-pill-title">
                        <span class="status-indicator-dot dot-selesai"></span>
                        <span>Selesai</span>
                    </div>
                    <span class="column-pill-count">{{ $tugasSelesaiCount }}</span>
                </div>
                <div class="kanban-cards-stack">
                    @forelse($tugasSelesaiList as $tugas)
                        <div class="kanban-task-card card-style-selesai">
                            <h3 class="task-card-title">{{ $tugas->judul }}</h3>
                            <p class="task-card-subtitle">{{ $tugas->kategori }} &bull; {{ $tugas->progresLogs->count() }} laporan progres</p>
                            <div class="task-card-footer">
                                <span class="task-card-date">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                    <span>{{ $tugas->deadline?->format('d M Y') ?? '-' }}</span>
                                </span>
                                <a href="{{ route('pembimbing.tugas.show', $tugas->id) }}" class="btn-card-action-outline">
                                    Detail
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="kanban-card-empty">
                            <span>Belum ada tugas yang diselesaikan.</span>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
    @vite(['resources/js/pages/pembimbing/peserta-show.js'])
@endpush
