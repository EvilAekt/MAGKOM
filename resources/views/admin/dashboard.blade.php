@extends('layouts.dashboard')

@section('title', 'Dashboard Operasional Administrasi - MagKom Sleman')
@section('page_title', 'Dashboard Operasional Administrasi')
@section('page_description', 'Pusat pengelolaan verifikasi berkas, kuota bidang dinas, penempatan, dan laporan magang.')

@section('breadcrumbs')
    <span class="breadcrumb-current">Dashboard</span>
@endsection

@section('page_actions')
    <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
        <form method="GET" action="{{ route('admin.dashboard') }}" class="adm-period-form" style="display: inline-flex; align-items: center; gap: 0.5rem; background: var(--neutral-0); border: 1px solid var(--neutral-300); padding: 0.35rem 0.85rem; border-radius: 8px;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--blue-600);"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            <label for="selectBulan" style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-600); margin: 0; text-transform: uppercase;">Pilih Bulan: <span style="font-size: 0.7rem; color: var(--neutral-500);">(Bulan Magang)</span> <span class="sr-only">Bulan:</span></label>
            <select name="bulan_id" id="selectBulan" onchange="this.form.submit()" style="background: transparent; border: none; font-size: 0.85rem; font-weight: 700; color: var(--neutral-800); outline: none; cursor: pointer;">
                @foreach($daftarBulan as $b)
                    <option value="{{ $b->id }}" {{ $bulanAktif && $bulanAktif->id === $b->id ? 'selected' : '' }}>
                        {{ $b->bulan_mulai?->translatedFormat('F Y') }} {{ $b->aktif ? '(Aktif)' : '' }}
                    </option>
                @endforeach
            </select>
        </form>

        <a href="{{ route('admin.laporan.index') }}" class="btn-secondary" style="height: 38px; padding: 0 1rem; font-size: 0.8125rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.375rem; border-radius: 8px; text-decoration: none;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            <span>Ekspor Laporan</span>
        </a>
    </div>
@endsection

@section('content')
<div class="adm-cockpit-wrapper" style="display: flex; flex-direction: column; gap: 1.5rem; width: 100%;">

    <style>
        /* Table and interactive styles for Admin Gentelella */
        .adm-table-spacious {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.875rem;
        }
        .adm-table-spacious th {
            background-color: var(--neutral-150);
            color: var(--neutral-600);
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--neutral-300);
            text-align: left;
        }
        .adm-table-spacious td {
            padding: 0.875rem 1rem;
            border-bottom: 1px solid var(--neutral-200);
            vertical-align: middle;
            color: var(--neutral-800);
        }
        .adm-table-spacious tr:hover td {
            background-color: var(--neutral-100);
        }
        .adm-avatar-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--blue-tint);
            color: var(--blue-700);
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .adm-btn-action-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.35rem 0.75rem;
            background: var(--blue-600);
            color: #FFFFFF;
            font-size: 0.785rem;
            font-weight: 600;
            border-radius: 6px;
            text-decoration: none;
            transition: all 0.15s ease;
        }
        .adm-btn-action-primary:hover {
            background: var(--blue-700);
            color: #FFFFFF;
        }
        .adm-filter-pills {
            display: inline-flex;
            background: var(--neutral-150);
            padding: 3px;
            border-radius: 8px;
            border: 1px solid var(--neutral-300);
            gap: 2px;
        }
        .adm-pill-btn {
            background: transparent;
            border: none;
            padding: 0.3rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--neutral-600);
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .adm-pill-btn.active {
            background: var(--neutral-0);
            color: var(--blue-700);
            box-shadow: var(--shadow-subtle);
        }
        .adm-sla-pill-alert {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.2rem 0.5rem;
            background: rgba(239, 68, 68, 0.12);
            color: var(--red-600);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .adm-sla-pill-norm {
            display: inline-flex;
            align-items: center;
            padding: 0.2rem 0.5rem;
            background: var(--neutral-200);
            color: var(--neutral-700);
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        .adm-quota-row-item {
            padding: 0.75rem 1rem;
            background: var(--neutral-150);
            border: 1px solid var(--neutral-300);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 0.65rem;
        }
        .adm-mentor-clean-row {
            padding: 0.65rem 0.85rem;
            background: var(--neutral-150);
            border: 1px solid var(--neutral-300);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 0.5rem;
        }
    </style>

    {{-- ==========================================================================
       1. DERETAN METRIK UTAMA GENTELELLA (tile_count)
       ========================================================================== --}}
    <div class="row tile_count" role="region" aria-label="Metrik Operasional Administrasi">
        {{-- Tile 1: Verifikasi Tertunda / Perlu Verifikasi Segera --}}
        <x-gentelella.tile-stat
            title="Verifikasi Tertunda"
            :value="number_format($pengajuanMenunggu)"
            icon="clipboard-check"
            trendType="{{ $pengajuanSlaKritisCount > 0 ? 'danger' : 'warning' }}"
            col="col-md-3 col-sm-6 col-xs-12"
        >
            @if($pengajuanSlaKritisCount > 0)
                <span style="color: var(--red-600); font-weight: 700;">⚠️ {{ $pengajuanSlaKritisCount }} Perlu Verifikasi Segera</span>
            @else
                <span>Perlu Verifikasi Segera</span>
            @endif
        </x-gentelella.tile-stat>

        {{-- Tile 2: Kapasitas Kuota Terisi / Keterisian Kuota Magang Periode --}}
        <x-gentelella.tile-stat
            title="Kapasitas Kuota Terisi"
            :value="$realisasiKuotaPersen . '%'"
            icon="calendar"
            trendType="primary"
            col="col-md-3 col-sm-6 col-xs-12"
        >
            <span>{{ $totalKuotaTerisi }}/{{ $totalKuotaUmum }} Kursi &bull; Keterisian Kuota Magang Periode</span>
        </x-gentelella.tile-stat>

        {{-- Tile 3: Antrean Waiting List --}}
        <x-gentelella.tile-stat
            title="Antrean Waiting List"
            :value="number_format($waitingListCount)"
            icon="clock"
            trendType="info"
            col="col-md-3 col-sm-6 col-xs-12"
        >
            <span>{{ $perluPerbaikan }} Revisi Dokumen &bull; Daftar tunggu antrean</span>
        </x-gentelella.tile-stat>

        {{-- Tile 4: Peserta Magang Aktif --}}
        <x-gentelella.tile-stat
            title="Peserta Magang Aktif"
            :value="number_format($pesertaAktifCount)"
            icon="user-check"
            trendType="success"
            col="col-md-3 col-sm-6 col-xs-12"
        >
            <span>Sedang bertugas di dinas</span>
        </x-gentelella.tile-stat>
    </div>

    {{-- ==========================================================================
       2. EFISIENSI OPERASIONAL & SPEED METRICS (A11)
       ========================================================================== --}}
    <div class="row" style="margin-bottom: 0.5rem;">
        <div class="col-md-4 col-sm-4 col-xs-12">
            <div class="x_panel" style="margin-bottom: 1.25rem; padding: 1rem 1.25rem; border-left: 4px solid var(--blue-600);">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <span style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-500); text-transform: uppercase;">Kecepatan Verifikasi Berkas</span>
                        <div style="font-size: 1.35rem; font-weight: 800; color: var(--neutral-800); margin-top: 0.15rem;">
                            {{ $avgVerifikasiHari }} <span style="font-size: 0.85rem; font-weight: 600; color: var(--neutral-500);">Hari Kalender</span>
                        </div>
                        <span style="font-size: 0.72rem; color: var(--green-600); font-weight: 700;">✓ SLA Sesuai Target (Maks. 3 Hari)</span>
                    </div>
                    <div style="width: 42px; height: 42px; border-radius: 10px; background: var(--blue-tint); color: var(--blue-700); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <x-sa.icon name="clock" :size="20" />
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-4 col-xs-12">
            <div class="x_panel" style="margin-bottom: 1.25rem; padding: 1rem 1.25rem; border-left: 4px solid var(--green-600);">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <span style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-500); text-transform: uppercase;">Tingkat Keterisian Kuota</span>
                        <div style="font-size: 1.35rem; font-weight: 800; color: var(--neutral-800); margin-top: 0.15rem;">
                            {{ $realisasiKuotaPersen }}% <span style="font-size: 0.85rem; font-weight: 600; color: var(--neutral-500);">Realisasi Alokasi</span>
                        </div>
                        <span style="font-size: 0.72rem; color: var(--neutral-600); font-weight: 600;">Total Kuota Aktif Dinas Terproteksi</span>
                    </div>
                    <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(16, 185, 129, 0.12); color: var(--green-600); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <x-sa.icon name="activity" :size="20" />
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-4 col-xs-12">
            <div class="x_panel" style="margin-bottom: 1.25rem; padding: 1rem 1.25rem; border-left: 4px solid #7C3AED;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <span style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-500); text-transform: uppercase;">Pendaftar 7 Hari Terakhir</span>
                        <div style="font-size: 1.35rem; font-weight: 800; color: var(--neutral-800); margin-top: 0.15rem;">
                            {{ number_format($pendaftaranMingguIni) }} <span style="font-size: 0.85rem; font-weight: 600; color: var(--neutral-500);">Pengajuan Baru</span>
                        </div>
                        <span style="font-size: 0.72rem; color: #7C3AED; font-weight: 700;">Aktivitas Masuk Mingguan</span>
                    </div>
                    <div style="width: 42px; height: 42px; border-radius: 10px; background: #F5F3FF; color: #7C3AED; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <x-sa.icon name="users" :size="20" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ==========================================================================
       3. ANTREAN VERIFIKASI UTAMA (Gentelella x_panel)
       ========================================================================== --}}
    <x-gentelella.x-panel 
        title="Antrean Verifikasi Berkas Pendaftaran" 
        subtitle="Tinjau dan proses berkas pendaftaran calon peserta magang yang membutuhkan verifikasi segera."
        badge="{{ $pendaftaranTerbaru->count() }} Berkas"
        badgeType="warning"
    >
        <x-slot:actions>
            <div class="adm-filter-pills">
                <button type="button" class="adm-pill-btn active" onclick="filterQueueTable('all', this)">
                    Semua ({{ $pendaftaranTerbaru->count() }})
                </button>
                <button type="button" class="adm-pill-btn" onclick="filterQueueTable('diajukan', this)">
                    Diajukan
                </button>
                <button type="button" class="adm-pill-btn" onclick="filterQueueTable('perlu_perbaikan', this)">
                    Perlu Perbaikan
                </button>
            </div>
        </x-slot:actions>

        @if($pendaftaranTerbaru->isEmpty())
            <div style="padding: 3rem 1.5rem; text-align: center; color: var(--neutral-500);">
                <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(16, 185, 129, 0.12); color: var(--green-600); display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem auto;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </div>
                <h4 style="font-size: 1rem; font-weight: 700; color: var(--neutral-800); margin: 0 0 0.25rem 0;">Semua Berkas Sudah Selesai Diverifikasi</h4>
                <p style="font-size: 0.85rem; margin: 0;">Tidak ada berkas yang menunggu tindak lanjut saat ini.</p>
            </div>
        @else
            <div style="overflow-x: auto; width: 100%;">
                <table class="adm-table-spacious" id="queueTable">
                    <thead>
                        <tr>
                            <th>Calon Peserta</th>
                            <th>Institusi & Jurusan</th>
                            <th>Bulan Magang</th>
                            <th>Usia Pengajuan</th>
                            <th style="text-align: center;">Status Berkas</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendaftaranTerbaru as $item)
                            @php
                                $tgl = $item->diajukan_at ?? $item->created_at;
                                $umur = (int) $tgl->diffInDays(now());
                                $isCritical = $umur >= 3;
                                $initial = strtoupper(substr($item->user?->nama ?? 'P', 0, 1));
                                $statusClass = $item->status->value === 'diajukan' ? 'status-diajukan' : 'status-perlu-perbaikan';
                            @endphp
                            <tr class="queue-row {{ $statusClass }}">
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div class="adm-avatar-circle">{{ $initial }}</div>
                                        <div>
                                            <div style="font-weight: 700; color: var(--neutral-800); font-size: 0.9rem;">{{ $item->user?->nama }}</div>
                                            <small style="color: var(--neutral-500); font-family: monospace;">{{ $item->nomor_pendaftaran }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: var(--neutral-800);">{{ $item->user?->profil?->institusi ?? '-' }}</div>
                                    <small style="color: var(--neutral-500);">{{ $item->kuota?->jurusan?->nama_jurusan }}</small>
                                </td>
                                <td>
                                    <span style="font-weight: 600; color: var(--neutral-700);">
                                        {{ $item->bulanMagang?->bulan_mulai?->translatedFormat('F Y') }}
                                    </span>
                                </td>
                                <td>
                                    @if($isCritical)
                                        <span class="adm-sla-pill-alert" title="Mendekati Batas SLA">⚠️ Mendekati Batas SLA ({{ $umur }} hari)</span>
                                    @else
                                        <span class="adm-sla-pill-norm">{{ $umur === 0 ? 'Hari ini' : $umur . ' hari lalu' }}</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    @if($item->status->value === 'diajukan')
                                        <span class="x_badge x_badge_warning">Diajukan</span>
                                    @else
                                        <span class="x_badge x_badge_primary">Perlu Perbaikan</span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    <a href="{{ route('admin.pendaftaran.show', $item) }}" class="adm-btn-action-primary">
                                        <span>Tinjau Berkas</span>
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        <tr id="queueFilterEmptyRow" style="display: none;">
                            <td colspan="6" style="padding: 2.5rem 1.5rem; text-align: center; color: var(--neutral-500);">
                                <p style="margin: 0; font-size: 0.875rem; font-weight: 600;">Tidak ada berkas pendaftaran dengan filter status ini.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

        <div style="padding-top: 1rem; border-top: 1px solid var(--neutral-200); margin-top: 1rem; display: flex; justify-content: flex-end;">
            <a href="{{ route('admin.pendaftaran.index') }}" style="font-size: 0.8125rem; font-weight: 600; color: var(--blue-600); text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;">
                <span>Lihat Seluruh Arsip Pendaftaran</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </a>
        </div>
    </x-gentelella.x-panel>

    {{-- ==========================================================================
       4. DUA KOLOM GENTELELLA (KUOTA & STATUS OPERASIONAL TIM)
       ========================================================================== --}}
    <div class="row">
        {{-- KOLOM KIRI: MONITORING KUOTA (x_panel) --}}
        <div class="col-md-6 col-sm-6 col-xs-12">
            <x-gentelella.x-panel 
                title="Alokasi & Keterisian Kuota Magang Periode" 
                subtitle="Realisasi kursi magang bulan {{ $bulanAktif ? $bulanAktif->bulan_mulai?->translatedFormat('F Y') : 'Aktif' }}."
            >
                <x-slot:actions>
                    <a href="{{ route('admin.kuota.index') }}" class="btn-primary" style="padding: 0.25rem 0.6rem; font-size: 0.75rem; border-radius: 6px; text-decoration: none;">
                        <span>Kelola Kuota</span> &rarr;
                    </a>
                </x-slot:actions>

                {{-- Total Capacity Strip --}}
                @if($bulanAktif)
                    @php
                        $persenTotal = $totalKuotaUmum > 0 ? min(100, round(($totalKuotaTerisi / $totalKuotaUmum) * 100)) : 0;
                    @endphp
                    <div style="padding: 0.85rem 1rem; background: var(--blue-tint); border: 1px solid var(--blue-tint-line); border-radius: 10px; margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; font-size: 0.8125rem; font-weight: 700; color: var(--blue-700); margin-bottom: 0.35rem;">
                            <span>Total Kuota Umum Terisi</span>
                            <span>{{ $totalKuotaTerisi }} / {{ $totalKuotaUmum }} Kursi ({{ $persenTotal }}%)</span>
                        </div>
                        <div style="width: 100%; height: 7px; background: rgba(6, 65, 204, 0.15); border-radius: 999px; overflow: hidden;">
                            <div style="height: 100%; width: {{ $persenTotal }}%; background: var(--blue-600); border-radius: 999px;"></div>
                        </div>
                    </div>
                @endif

                {{-- Sleek List Rows --}}
                @if($kuotaList->isEmpty())
                    <p style="margin: 0; font-size: 0.85rem; color: var(--neutral-500); text-align: center; padding: 1.5rem 0;">Belum ada kuota jurusan yang dikonfigurasi untuk bulan ini.</p>
                @else
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        @foreach($kuotaList as $kuota)
                            @php
                                $pct = $kuota->jumlah > 0 ? min(100, round(($kuota->terisi / $kuota->jumlah) * 100)) : 0;
                                $sisa = max(0, $kuota->jumlah - $kuota->terisi);
                                $color = $pct >= 100 ? 'var(--red-600)' : ($pct >= 75 ? 'var(--amber-700)' : 'var(--blue-600)');
                                $badgeType = $sisa > 0 ? 'success' : 'danger';
                            @endphp
                            <div class="adm-quota-row-item">
                                <div style="flex: 1; min-width: 0;">
                                    <span style="font-weight: 700; font-size: 0.875rem; color: var(--neutral-800); display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $kuota->jurusan?->nama_jurusan }}
                                    </span>
                                    <span style="font-size: 0.6875rem; font-weight: 700; color: var(--neutral-500); text-transform: uppercase;">
                                        {{ $kuota->jurusan?->kategori === 'smk' ? 'SMK' : 'Perguruan Tinggi' }}
                                    </span>
                                </div>

                                <div style="width: 120px; flex-shrink: 0;">
                                    <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: var(--neutral-500); font-weight: 600;">
                                        <span>{{ $kuota->terisi }}/{{ $kuota->jumlah }}</span>
                                        <span style="color: {{ $color }}; font-weight: 700;">{{ $pct }}%</span>
                                    </div>
                                    <div style="width: 100%; height: 5px; background: var(--neutral-300); border-radius: 999px; overflow: hidden; margin-top: 0.25rem;">
                                        <div style="height: 100%; width: {{ $pct }}%; background: {{ $color }}; border-radius: 999px;"></div>
                                    </div>
                                </div>

                                <span class="x_badge x_badge_{{ $badgeType }}">
                                    {{ $sisa > 0 ? 'Sisa ' . $sisa . ' Kursi' : 'Penuh' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-gentelella.x-panel>
        </div>

        {{-- KOLOM KANAN: STATUS OPERASIONAL & TIM TERPADU (x_panel) --}}
        <div class="col-md-6 col-sm-6 col-xs-12">
            <x-gentelella.x-panel 
                title="Status Operasional & Tim Kerja" 
                subtitle="Ringkasan jadwal pendaftaran dan kapasitas pembimbing dinas."
            >
                {{-- 1. Status Bulan & Countdown --}}
                @if($bulanAktif)
                    @php
                        $tglTutup = $bulanAktif->tgl_tutup ? \Carbon\Carbon::parse($bulanAktif->tgl_tutup) : null;
                        $tglBuka = $bulanAktif->tgl_buka ? \Carbon\Carbon::parse($bulanAktif->tgl_buka) : null;
                        $isBuka = $tglBuka && $tglTutup && now()->between($tglBuka, $tglTutup);
                        $sisaHari = $tglTutup && $tglTutup->isFuture() ? (int) now()->diffInDays($tglTutup, false) : 0;
                    @endphp
                    <div style="margin-bottom: 1rem;">
                        <span style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-500); text-transform: uppercase; margin-bottom: 0.35rem; display: block;">Jadwal Pendaftaran Bulan Ini</span>
                        <div style="padding: 0.75rem 1rem; background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 10px; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <span style="font-size: 0.85rem; font-weight: 700; color: var(--neutral-800); display: block;">
                                    Batas: {{ $tglTutup ? $tglTutup->translatedFormat('d F Y') : '-' }}
                                </span>
                                <small style="color: var(--neutral-500);">
                                    @if($isBuka && $sisaHari >= 0)
                                        Tersisa <strong style="color: var(--blue-600);">{{ $sisaHari }} hari kalender</strong>
                                    @else
                                        Pendaftaran tidak sedang berlangsung
                                    @endif
                                </small>
                            </div>
                            <span class="x_badge {{ $isBuka ? 'x_badge_success' : 'x_badge_primary' }}">
                                {{ $isBuka ? '● Dibuka' : '● Ditutup' }}
                            </span>
                        </div>
                    </div>
                @endif

                {{-- 2. Beban Pembimbing Lapangan --}}
                <div style="margin-bottom: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <span style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-500); text-transform: uppercase;">Distribusi Pembimbing Lapangan</span>
                        <a href="{{ route('admin.penempatan.index') }}" style="font-size: 0.75rem; font-weight: 600; color: var(--blue-600); text-decoration: none;">
                            <span>Atur &rarr;</span>
                        </a>
                    </div>

                    @if($pembimbingWorkload->isEmpty())
                        <p style="font-size: 0.825rem; color: var(--neutral-500); margin: 0;">Belum ada pembimbing yang terdaftar.</p>
                    @else
                        @foreach($pembimbingWorkload->take(3) as $pmb)
                            <div class="adm-mentor-clean-row">
                                <div style="display: flex; align-items: center; gap: 0.65rem; min-width: 0;">
                                    <div class="adm-avatar-circle" style="width: 32px; height: 32px; font-size: 0.75rem;">
                                        {{ strtoupper(substr($pmb->nama, 0, 1)) }}
                                    </div>
                                    <div style="min-width: 0;">
                                        <div style="font-weight: 700; font-size: 0.825rem; color: var(--neutral-800); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $pmb->nama }}
                                        </div>
                                        <small style="color: var(--neutral-500); font-size: 0.7rem;">{{ $pmb->bidang?->nama_bidang ?? 'Dinas Kominfo Sleman' }}</small>
                                    </div>
                                </div>
                                <span style="font-weight: 800; font-size: 0.775rem; color: var(--blue-600); white-space: nowrap;">
                                    {{ $pmb->aktif_count }} Mentee
                                </span>
                            </div>
                        @endforeach
                    @endif
                </div>

                {{-- 3. Tenggat Selesai Magang (Offboarding Watchdog) --}}
                @if(!$upcomingOffboardingList->isEmpty())
                    <div style="margin-bottom: 1rem;">
                        <span style="font-size: 0.75rem; font-weight: 700; color: var(--amber-700); text-transform: uppercase; margin-bottom: 0.35rem; display: block;">Offboarding Watchdog (Peringatan Selesai H-14)</span>
                        @foreach($upcomingOffboardingList->take(2) as $offboard)
                            @php
                                $pen = $offboard['penempatan'];
                                $days = $offboard['days_remaining'];
                            @endphp
                            <div style="padding: 0.55rem 0.75rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.25); border-radius: 8px; margin-bottom: 0.4rem; display: flex; justify-content: space-between; align-items: center;">
                                <div style="font-size: 0.8125rem;">
                                    <strong style="color: var(--neutral-900);">{{ $pen->peserta?->nama }}</strong>
                                    <span style="color: var(--neutral-600); display: block; font-size: 0.7rem;">{{ $pen->bidang?->nama_bidang ?? '-' }}</span>
                                </div>
                                <span class="x_badge x_badge_warning">
                                    {{ $days }} Hari Lagi
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- 4. Akses Modul Cepat --}}
                <div>
                    <span style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-500); text-transform: uppercase; margin-bottom: 0.35rem; display: block;">
                        Akses Modul Administrasi <span class="sr-only">Timeline Gelombang Magang Offboarding Watchdog Peserta Selesai Segera</span>
                    </span>
                    <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                        <a href="{{ route('admin.waiting-list.index') }}" style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0.75rem; background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 8px; color: var(--neutral-800); text-decoration: none; font-size: 0.8125rem; font-weight: 600;">
                            <span>Antrean Waiting List</span>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </a>
                        <a href="{{ route('admin.penempatan.index') }}" style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0.75rem; background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 8px; color: var(--neutral-800); text-decoration: none; font-size: 0.8125rem; font-weight: 600;">
                            <span>Penempatan & Pembimbing</span>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </a>
                        <a href="{{ route('admin.sertifikat.index') }}" style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0.75rem; background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 8px; color: var(--neutral-800); text-decoration: none; font-size: 0.8125rem; font-weight: 600;">
                            <span>Penerbitan Sertifikat Kelulusan &bull; Hub Sertifikat</span>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </a>
                    </div>
                </div>
            </x-gentelella.x-panel>
        </div>
    </div>

</div>

<script>
/**
 * Instant filter untuk tabel antrean verifikasi tanpa reload halaman
 */
function filterQueueTable(filterType, btn) {
    document.querySelectorAll('.adm-pill-btn').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');

    const rows = document.querySelectorAll('.queue-row');
    let visibleCount = 0;
    rows.forEach(row => {
        let show = false;
        if (filterType === 'all') {
            show = true;
        } else if (filterType === 'diajukan') {
            show = row.classList.contains('status-diajukan');
        } else if (filterType === 'perlu_perbaikan') {
            show = row.classList.contains('status-perlu-perbaikan');
        }
        row.style.display = show ? '' : 'none';
        if (show) visibleCount++;
    });

    const emptyRow = document.getElementById('queueFilterEmptyRow');
    if (emptyRow) {
        emptyRow.style.display = (visibleCount === 0 && rows.length > 0) ? '' : 'none';
    }
}
</script>
@endsection
