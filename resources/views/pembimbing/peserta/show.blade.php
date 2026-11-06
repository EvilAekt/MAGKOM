@extends('layouts.dashboard')

@section('title', 'Profil Mentee: ' . ($penempatan->peserta?->nama ?? 'Peserta') . ' - Portal MagKom')
@section('topbar_title', 'Profil 360° Mentee Bimbingan')

@push('styles')
    @vite(['resources/css/pages/pembimbing/peserta-show.css'])
@endpush

@section('content')
<div class="dashboard-content mentee-show-container">

    {{-- 1. Classroom Navy Banner (Breadcrumb Dihapus, Judul Tegas) --}}
    <div class="classroom-banner">
        <div class="classroom-banner-content">
            <div class="classroom-banner-text">
                <h1 class="classroom-banner-title">Profil 360° Mentee Bimbingan</h1>
                <p class="classroom-banner-subtitle">
                    Bidang: <strong>{{ $penempatan->bidang?->nama_bidang ?? 'Dinas Kominfo Sleman' }}</strong> &bull;
                    Pembimbing: <strong>{{ $penempatan->pembimbing?->nama ?? auth()->user()->nama }}</strong> &bull;
                    Mentee: <strong>{{ $penempatan->peserta?->nama }}</strong>
                </p>
            </div>
            <div class="classroom-banner-action">
                <a href="{{ route('pembimbing.peserta.index') }}" class="btn-banner-back" title="Kembali ke Direktori Peserta Bimbingan">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                    <span>Kembali ke Direktori</span>
                </a>
            </div>
        </div>
    </div>

    {{-- 2. Card Hero Profil Mentee --}}
    <div class="mentee-hero-card">
        <div class="mentee-hero-left">
            <div class="mentee-avatar-squircle">
                {{ strtoupper(substr($penempatan->peserta?->nama ?? 'P', 0, 2)) }}
            </div>
            <div class="mentee-hero-info">
                <div class="mentee-title-badges">
                    <h2 class="mentee-name">{{ $penempatan->peserta?->nama }}</h2>
                    <span class="badge-status-pill badge-status-{{ $penempatan->status->value }}">
                        Status: {{ ucfirst($penempatan->status->value) }}
                    </span>
                    <span class="badge-jalur-pill">
                        Jalur {{ ucfirst($penempatan->jalur?->value ?? 'Umum') }}
                    </span>
                </div>
                <div class="mentee-sub-meta">
                    <span class="meta-inst">{{ $penempatan->peserta?->profil?->institusi ?? 'Institusi Belum Dilengkapi' }}</span>
                    <span class="meta-dot">&bull;</span>
                    <span class="meta-jur">{{ $penempatan->peserta?->profil?->jurusan?->nama_jurusan ?? 'Jurusan Belum Dilengkapi' }}</span>
                </div>
                <div class="mentee-chips-row">
                    <span class="chip-item">Bidang: <strong>{{ $penempatan->bidang?->nama_bidang ?? '-' }}</strong></span>
                    <span class="meta-dot">&bull;</span>
                    <span class="chip-item">NIM/NISN: <strong>{{ $penempatan->peserta?->profil?->nim_nisn ?? '-' }}</strong></span>
                </div>
            </div>
        </div>

        <div class="mentee-hero-actions">
            <a href="{{ route('pembimbing.logbook.index', ['masuk' => $penempatan->id]) }}" class="btn-hero-action btn-hero-secondary" title="Lihat catatan logbook harian mentee ini">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                <span>Logbook Mentee</span>
            </a>
            <a href="{{ route('pembimbing.tugas.index', ['masuk' => $penempatan->id]) }}" class="btn-hero-action btn-hero-primary" title="Lihat daftar seluruh tugas mentee ini">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"></path><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                <span>Tugas Mentee</span>
            </a>
        </div>
    </div>

    {{-- 3. Konsolidasi Kinerja & Analitik (2-Panel Unified Performance Layout) --}}
    @php
        $persenTugas = $totalTugas > 0 ? (int) round(($tugasSelesai / $totalTugas) * 100) : 0;
        $totTugas = max(1, $tugasStats['total'] ?? 1);
        $pSelesai = round((($tugasStats['selesai'] ?? 0) / $totTugas) * 100);
        $pProgres = round((($tugasStats['dalam_pengerjaan'] ?? 0) / $totTugas) * 100);
        $pBelum = round((($tugasStats['belum_dimulai'] ?? 0) / $totTugas) * 100);
        $pTelat = round((($tugasStats['terlambat'] ?? 0) / $totTugas) * 100);

        $deg1 = round(($pSelesai / 100) * 360);
        $deg2 = $deg1 + round(($pProgres / 100) * 360);
        $deg3 = $deg2 + round(($pBelum / 100) * 360);
    @endphp

    <div class="mentee-kpi-grid">
        {{-- PANEL KIRI: KINERJA & KOMPOSISI PENUGASAN --}}
        <div class="kpi-card performance-panel">
            <div class="kpi-header">
                <span class="kpi-label">Penyelesaian Tugas</span>
                <span class="kpi-value-badge badge-blue">
                    {{ $tugasSelesai }} / {{ $totalTugas }} Selesai
                </span>
            </div>

            <div class="kpi-progress-track">
                <div class="kpi-progress-bar" style="width: {{ $persenTugas }}%;"></div>
            </div>

            <div class="kpi-footer-meta">
                <span>{{ $tugasAktif }} tugas sedang berjalan</span>
                <span>Tingkat Selesai: <strong>{{ $persenTugas }}%</strong></span>
            </div>

            <div class="panel-inner-divider"></div>

            <div class="chart-card-header sub-header">
                <h3 class="chart-card-title">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M12 2a10 10 0 0 1 10 10h-10z"></path></svg>
                    <span>Komposisi &amp; Rasio Tugas Peserta</span>
                </h3>
            </div>

            <div class="donut-chart-layout">
                <div class="donut-chart-graphic" style="background: conic-gradient(#10B981 0deg {{ $deg1 }}deg, #2563EB {{ $deg1 }}deg {{ $deg2 }}deg, #CBD5E1 {{ $deg2 }}deg {{ $deg3 }}deg, #EF4444 {{ $deg3 }}deg 360deg);">
                    <div class="donut-chart-center">
                        <span class="donut-center-count">{{ $tugasStats['selesai'] ?? 0 }}/{{ $tugasStats['total'] ?? 0 }}</span>
                        <span class="donut-center-label">Selesai</span>
                    </div>
                </div>

                <div class="donut-chart-legends">
                    <div class="legend-row">
                        <span class="legend-info">
                            <span class="legend-dot" style="background: #10B981;"></span>
                            <span>Selesai</span>
                        </span>
                        <span class="legend-val">{{ $tugasStats['selesai'] ?? 0 }}</span>
                    </div>
                    <div class="legend-row">
                        <span class="legend-info">
                            <span class="legend-dot" style="background: #2563EB;"></span>
                            <span>Dikerjakan</span>
                        </span>
                        <span class="legend-val">{{ $tugasStats['dalam_pengerjaan'] ?? 0 }}</span>
                    </div>
                    <div class="legend-row">
                        <span class="legend-info">
                            <span class="legend-dot" style="background: #CBD5E1;"></span>
                            <span>Belum Mulai</span>
                        </span>
                        <span class="legend-val">{{ $tugasStats['belum_dimulai'] ?? 0 }}</span>
                    </div>
                    @if(($tugasStats['terlambat'] ?? 0) > 0)
                        <div class="legend-row">
                            <span class="legend-info">
                                <span class="legend-dot" style="background: #EF4444;"></span>
                                <span style="color: #DC2626; font-weight: 600;">Terlambat</span>
                            </span>
                            <span class="legend-val danger">{{ $tugasStats['terlambat'] }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- PANEL KANAN: KEDISIPLINAN & PELACAKAN LOGBOOK --}}
        <div class="kpi-card performance-panel">
            <div class="kpi-header">
                <span class="kpi-label">Kedisiplinan Logbook</span>
                <span class="kpi-value-badge badge-amber">
                    Total {{ $totalLogbook }} Entri
                </span>
            </div>

            <div class="kpi-logbook-chips">
                <div class="chip-logbook-stat chip-success">
                    <span class="stat-name">Disetujui</span>
                    <span class="stat-count">{{ $logbookDisetujui }}</span>
                </div>
                <div class="chip-logbook-stat chip-danger">
                    <span class="stat-name">Perlu Revisi</span>
                    <span class="stat-count">{{ $logbookPerluRevisi }}</span>
                </div>
            </div>

            <div class="panel-inner-divider"></div>

            <div class="chart-card-header sub-header">
                <h3 class="chart-card-title">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2"><path d="M18 20V10"></path><path d="M12 20V4"></path><path d="M6 20v-6"></path></svg>
                    <span>Pelacakan Mingguan Logbook Magang</span>
                </h3>
                <span class="chart-target-badge">Target: 12 Minggu</span>
            </div>

            <div class="weekly-bars-container">
                @foreach($logbookPerMinggu ?? [] as $minggu)
                    @php
                        $st = $minggu['status'];
                        $barHeight = match($st) {
                            'disetujui' => '80px',
                            'terkirim' => '60px',
                            'perlu_revisi' => '40px',
                            'mendatang' => '12px',
                            default => '20px',
                        };
                        $barBg = match($st) {
                            'disetujui' => '#10B981',
                            'terkirim' => '#F59E0B',
                            'perlu_revisi' => '#EF4444',
                            'mendatang' => '#F1F5F9',
                            default => '#E2E8F0',
                        };
                    @endphp
                    <div class="weekly-bar-col" title="Minggu ke-{{ $minggu['minggu_ke'] }}: {{ ucfirst(str_replace('_', ' ', $st)) }}">
                        <div class="weekly-bar-pill" style="height: {{ $barHeight }}; background: {{ $barBg }};"></div>
                    </div>
                @endforeach
            </div>

            <div class="weekly-labels-row">
                @foreach($logbookPerMinggu ?? [] as $minggu)
                    <span class="weekly-label-item {{ $minggu['is_aktif'] ? 'active' : '' }}">M{{ $minggu['minggu_ke'] }}</span>
                @endforeach
            </div>

            <div class="weekly-legends-row">
                <span class="weekly-legend-item"><span class="weekly-legend-color" style="background: #10B981;"></span> Disetujui</span>
                <span class="weekly-legend-item"><span class="weekly-legend-color" style="background: #F59E0B;"></span> Menunggu Review</span>
                <span class="weekly-legend-item"><span class="weekly-legend-color" style="background: #EF4444;"></span> Perlu Revisi</span>
                <span class="weekly-legend-item"><span class="weekly-legend-color" style="background: #E2E8F0;"></span> Belum Terisi</span>
            </div>
        </div>
    </div>

    {{-- 4. Rincian Data Kontak & Timeline Magang --}}
    <div class="mentee-info-grid">
        {{-- Kolom Data Kontak & Akademik --}}
        <div class="info-panel-card">
            <h3 class="info-panel-title">Data Pribadi &amp; Kontak Akademik</h3>
            <div class="info-rows-list">
                <div class="info-row-item">
                    <span class="info-row-label">Email Resmi</span>
                    <span class="info-row-val">{{ $penempatan->peserta?->email }}</span>
                </div>
                <div class="info-row-item">
                    <span class="info-row-label">Nomor Telepon / WA</span>
                    <span class="info-row-val">{{ $penempatan->peserta?->no_telp ?? '-' }}</span>
                </div>
                <div class="info-row-item">
                    <span class="info-row-label">Asal Kampus / Sekolah</span>
                    <span class="info-row-val">{{ $penempatan->peserta?->profil?->institusi ?? '-' }}</span>
                </div>
                <div class="info-row-item">
                    <span class="info-row-label">Program Studi</span>
                    <span class="info-row-val">{{ $penempatan->peserta?->profil?->jurusan?->nama_jurusan ?? '-' }}</span>
                </div>
                <div class="info-row-item">
                    <span class="info-row-label">NIM / NISN</span>
                    <span class="info-row-val">{{ $penempatan->peserta?->profil?->nim_nisn ?? '-' }}</span>
                </div>
                <div class="info-row-item">
                    <span class="info-row-label">Dosen Pembimbing Kampus</span>
                    <span class="info-row-val">{{ $penempatan->peserta?->profil?->nama_dosen_pembimbing ?? '-' }}</span>
                </div>
                <div class="info-row-item">
                    <span class="info-row-label">Kontak Dosen / Kampus</span>
                    <span class="info-row-val">{{ $penempatan->peserta?->profil?->kontak_dosen_pembimbing ?? '-' }}</span>
                </div>
            </div>
        </div>

        {{-- Kolom Timeline Magang & Masa Tenggang --}}
        <div class="info-panel-card">
            <h3 class="info-panel-title">Timeline Periode &amp; Ketentuan Akun</h3>
            <div class="info-rows-list">
                <div class="info-row-item">
                    <span class="info-row-label">Tanggal Mulai Magang</span>
                    <span class="info-row-val">{{ $penempatan->tgl_mulai?->format('d F Y') ?? '-' }}</span>
                </div>
                <div class="info-row-item">
                    <span class="info-row-label">Tanggal Rencana Selesai</span>
                    <span class="info-row-val">{{ $penempatan->tgl_selesai?->format('d F Y') ?? '-' }}</span>
                </div>
                <div class="info-row-item">
                    <span class="info-row-label">Total Durasi</span>
                    <span class="info-row-val">{{ $totalHariMagang }} Hari Kalender</span>
                </div>
                <div class="info-row-item">
                    <span class="info-row-label">Sisa Waktu Magang</span>
                    <span class="info-row-val {{ $sisaHari <= 7 && $penempatan->status->value === 'aktif' ? 'danger' : '' }}">
                        @if($penempatan->status->value === 'selesai')
                            Selesai Ditetapkan
                        @else
                            {{ $sisaHari < 0 ? 'Melewati batas ' . abs($sisaHari) . ' hari' : $sisaHari . ' Hari Tersisa' }}
                        @endif
                    </span>
                </div>
                <div class="info-row-item dashed-top">
                    <span class="info-row-label">Tanggal Ditetapkan Selesai</span>
                    <span class="info-row-val success">{{ $penempatan->tgl_ditetapkan_selesai?->format('d F Y') ?? 'Belum Ditetapkan' }}</span>
                </div>
                <div class="info-row-item">
                    <span class="info-row-label">Batas Masa Tenggang Akun (BR-11)</span>
                    <span class="info-row-val">{{ $penempatan->tgl_nonaktif?->format('d F Y') ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- 5. Riwayat Penugasan Mentee (Subtitle Teks DIHAPUS) --}}
    <div class="mentee-task-card">
        <div class="task-card-header">
            <div>
                <h3 class="task-card-title">Riwayat Penugasan ({{ $penempatan->tugas->count() }})</h3>
            </div>
            <a href="{{ route('pembimbing.tugas.index', ['masuk' => $penempatan->id]) }}" class="btn-manage-tasks">
                <span>Kelola Tugas</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </a>
        </div>

        <div class="task-table-wrapper">
            <table class="task-table-modern">
                <thead>
                    <tr>
                        <th>Judul Tugas</th>
                        <th>Kategori</th>
                        <th>Kompleksitas</th>
                        <th>Deadline</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penempatan->tugas as $tugas)
                        <tr>
                            <td>
                                <div class="task-title-cell">
                                    <span class="task-title-name">{{ $tugas->judul }}</span>
                                    <span class="task-title-sub">{{ $tugas->progresLogs->count() }} Laporan Progres</span>
                                </div>
                            </td>
                            <td>
                                <span class="task-category-pill">{{ $tugas->kategori }}</span>
                            </td>
                            <td>
                                <span class="task-kompleksitas-val">{{ ucfirst($tugas->kompleksitas?->value ?? 'Sedang') }}</span>
                            </td>
                            <td>
                                <span class="task-deadline-val">{{ $tugas->deadline?->format('d M Y') }}</span>
                            </td>
                            <td>
                                @if($tugas->status->value === 'selesai')
                                    <span class="task-status-pill task-status-selesai">Selesai</span>
                                @elseif($tugas->status->value === 'proses')
                                    <span class="task-status-pill task-status-proses">Proses</span>
                                @else
                                    <span class="task-status-pill task-status-belum">Belum</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <a href="{{ route('pembimbing.tugas.show', $tugas->id) }}" class="task-link-detail">
                                    <span>Detail</span>
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="task-empty-state">
                                Belum ada penugasan yang dibuat untuk mentee ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
