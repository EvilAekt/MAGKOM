@extends('layouts.dashboard')

@section('title', 'Dashboard Operasional Administrasi - MagKom Sleman')

@section('content')
<div class="dashboard-content" style="display: flex; flex-direction: column; gap: 1.5rem; max-width: 1400px; margin: 0 auto; width: 100%;">

    <style>
        .admin-stat-card {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid var(--neutral-200, #E2E8F0);
            padding: 1.25rem 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .admin-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
        }
        .admin-card {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid var(--neutral-200, #E2E8F0);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        .admin-tab-nav {
            display: flex;
            gap: 0.5rem;
            border-bottom: 1px solid var(--neutral-200, #E2E8F0);
            padding: 0 1.25rem;
            background: #ffffff;
            overflow-x: auto;
        }
        .admin-tab-btn {
            background: transparent;
            border: none;
            padding: 1rem 1rem;
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--neutral-600, #64748B);
            cursor: pointer;
            border-bottom: 2px solid transparent;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            white-space: nowrap;
            transition: all 0.2s ease;
        }
        .admin-tab-btn:hover {
            color: #0641CC;
        }
        .admin-tab-btn.active {
            color: #0641CC;
            border-bottom-color: #0641CC;
        }
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.875rem;
        }
        .admin-table th {
            background: #F8FAFC;
            padding: 0.85rem 1.25rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--neutral-600, #475569);
            border-bottom: 1px solid var(--neutral-200, #E2E8F0);
        }
        .admin-table td {
            padding: 0.95rem 1.25rem;
            border-bottom: 1px solid var(--neutral-200, #E2E8F0);
            vertical-align: middle;
            color: var(--neutral-800, #1E293B);
        }
        .admin-table tr:hover {
            background: #F8FAFC;
        }
    </style>

    {{-- 1. TOPBAR & OPERATIONAL HEADER --}}
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0); padding-bottom: 1rem;">
        <div>
            <nav style="font-size: 0.8rem; color: var(--neutral-500, #64748B); margin-bottom: 0.35rem; display: flex; align-items: center; gap: 0.4rem;">
                <span style="color: var(--neutral-600, #475569);">Portal MagKom</span>
                <span>/</span>
                <span style="color: var(--neutral-900, #0F172A); font-weight: 600;">Operasional Administrasi</span>
            </nav>
            <div style="display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
                <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0; letter-spacing: -0.02em;">
                    Dashboard Operasional Administrasi
                </h1>
                <span style="display: inline-flex; align-items: center; padding: 0.2rem 0.65rem; border-radius: 9999px; font-size: 0.72rem; font-weight: 700; background: #DBEAFE; color: #1E40AF; text-transform: uppercase; letter-spacing: 0.04em;">
                    Command Center
                </span>
            </div>
            <p style="font-size: 0.85rem; color: var(--neutral-600, #475569); margin: 0.3rem 0 0 0;">
                Pantau aktivitas pendaftaran, antrean verifikasi berkas, dan utilisasi kuota magang secara real-time.
            </p>
        </div>

        {{-- Filter Periode Bulan & Aksi Cepat --}}
        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            <form method="GET" action="{{ route('admin.dashboard') }}" style="display: flex; align-items: center; gap: 0.5rem; margin: 0;">
                <label for="selectBulan" style="font-size: 0.8rem; font-weight: 600; color: var(--neutral-700, #334155); margin: 0;">
                    Periode:
                </label>
                <select name="bulan_id" 
                        id="selectBulan" 
                        onchange="this.form.submit()" 
                        style="height: 38px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 2rem 0 0.75rem; font-size: 0.85rem; font-weight: 600; color: var(--neutral-800, #1E293B); background: #ffffff; cursor: pointer;">
                    @foreach($daftarBulan as $b)
                        <option value="{{ $b->id }}" {{ $bulanAktif && $bulanAktif->id === $b->id ? 'selected' : '' }}>
                            {{ $b->bulan_mulai?->translatedFormat('F Y') }} {{ $b->aktif ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
            </form>

            <a href="{{ route('admin.laporan.index') }}" 
               style="height: 38px; padding: 0 1rem; background: #ffffff; color: var(--neutral-700, #334155); border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; font-size: 0.85rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem; transition: background 0.15s ease;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                <span>Ekspor Laporan</span>
            </a>
        </div>
    </div>

    {{-- 2. HERO OPERATIONAL BANNER (STATUS BULAN BERJALAN & REKAP KAPASITAS) --}}
    @if($bulanAktif)
        @php
            $tglTutup = $bulanAktif->tgl_tutup ? \Carbon\Carbon::parse($bulanAktif->tgl_tutup) : null;
            $tglBuka = $bulanAktif->tgl_buka ? \Carbon\Carbon::parse($bulanAktif->tgl_buka) : null;
            $isBuka = $tglBuka && $tglTutup && now()->between($tglBuka, $tglTutup);
            $sisaHari = $tglTutup && $tglTutup->isFuture() ? (int) now()->diffInDays($tglTutup, false) : 0;
            $persenKuotaUmum = $totalKuotaUmum > 0 ? min(100, round(($totalKuotaTerisi / $totalKuotaUmum) * 100)) : 0;
        @endphp
        <div style="background: linear-gradient(135deg, #EFF6FF 0%, #FFFFFF 100%); border: 1px solid #BFDBFE; border-radius: 14px; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: #0641CC; color: #ffffff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 2px 8px rgba(6, 65, 204, 0.25);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                </div>
                <div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                        <span style="font-size: 0.8rem; font-weight: 700; color: #1E40AF; text-transform: uppercase;">
                            Keterisian Kuota Magang Periode {{ $bulanAktif->bulan_mulai?->translatedFormat('F Y') }}
                        </span>
                        @if($isBuka)
                            <span style="padding: 0.15rem 0.5rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 700; background: #DCFCE7; color: #166534; border: 1px solid #86EFAC;">
                                Pendaftaran Buka
                            </span>
                        @else
                            <span style="padding: 0.15rem 0.5rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 700; background: #F1F5F9; color: #475569; border: 1px solid #CBD5E1;">
                                Pendaftaran Tutup
                            </span>
                        @endif
                    </div>
                    <p style="font-size: 0.85rem; color: var(--neutral-700, #334155); margin: 0.25rem 0 0 0;">
                        Jadwal Pendaftaran: <strong>{{ $tglBuka ? $tglBuka->translatedFormat('d M Y') : '-' }}</strong> s/d <strong>{{ $tglTutup ? $tglTutup->translatedFormat('d M Y') : '-' }}</strong>
                        @if($isBuka && $sisaHari >= 0)
                            <span style="color: #0641CC; font-weight: 700; margin-left: 0.4rem;">({{ $sisaHari }} hari tersisa)</span>
                        @endif
                    </p>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap;">
                <div style="text-align: right;">
                    <span style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-500, #64748B); display: block;">Realisasi Kuota Umum</span>
                    <span style="font-size: 1.15rem; font-weight: 800; color: #0641CC;">
                        {{ $totalKuotaTerisi }} <span style="font-size: 0.85rem; color: var(--neutral-500, #64748B); font-weight: 600;">/ {{ $totalKuotaUmum }} Slot</span>
                    </span>
                    <span style="font-size: 0.72rem; color: var(--neutral-600, #475569); display: block;">{{ $persenKuotaUmum }}% Terisi (BR-01)</span>
                </div>
                @if($slotKhususTerisi > 0)
                    <div style="text-align: right; border-left: 1px solid #BFDBFE; padding-left: 1.25rem;">
                        <span style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-500, #64748B); display: block;">Slot Khusus Internal</span>
                        <span style="font-size: 1.15rem; font-weight: 800; color: #7C3AED;">
                            {{ $slotKhususTerisi }} <span style="font-size: 0.85rem; color: var(--neutral-500, #64748B); font-weight: 600;">Terisi</span>
                        </span>
                        <span style="font-size: 0.72rem; color: var(--neutral-600, #475569); display: block;">Jalur Khusus (BR-02)</span>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- 3. 4 KPI SUMMARY STAT METRICS CARDS --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;">
        
        <!-- Card 1: Perlu Verifikasi Segera -->
        <div class="admin-stat-card" style="border-left: 4px solid #F59E0B;">
            <div>
                <span style="font-size: 0.8rem; font-weight: 700; color: var(--neutral-600, #475569); display: block; margin-bottom: 0.25rem;">Perlu Verifikasi Segera</span>
                <span style="font-size: 1.85rem; font-weight: 800; color: var(--neutral-900, #0F172A); line-height: 1.1;">{{ number_format($pengajuanMenunggu) }}</span>
                @if($pengajuanSlaKritisCount > 0)
                    <span style="display: inline-flex; align-items: center; gap: 0.3rem; font-size: 0.72rem; font-weight: 700; color: #DC2626; margin-top: 0.35rem; background: #FEF2F2; padding: 0.15rem 0.45rem; border-radius: 6px; border: 1px solid #FECACA;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        <span>{{ $pengajuanSlaKritisCount }} Mendekati Batas SLA</span>
                    </span>
                @else
                    <span style="font-size: 0.75rem; color: #B45309; font-weight: 600; display: block; margin-top: 0.25rem;">Status: Diajukan</span>
                @endif
            </div>
            <div style="width: 46px; height: 46px; border-radius: 10px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
            </div>
        </div>

        <!-- Card 2: Menunggu Perbaikan Berkas -->
        <div class="admin-stat-card" style="border-left: 4px solid #0641CC;">
            <div>
                <span style="font-size: 0.8rem; font-weight: 700; color: var(--neutral-600, #475569); display: block; margin-bottom: 0.25rem;">Perlu Perbaikan Dokumen</span>
                <span style="font-size: 1.85rem; font-weight: 800; color: var(--neutral-900, #0F172A); line-height: 1.1;">{{ number_format($perluPerbaikan) }}</span>
                <span style="font-size: 0.75rem; color: #0641CC; font-weight: 600; display: block; margin-top: 0.25rem;">Menunggu revisi peserta</span>
            </div>
            <div style="width: 46px; height: 46px; border-radius: 10px; background: #EFF6FF; color: #0641CC; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
            </div>
        </div>

        <!-- Card 3: Antrean Waiting List -->
        <div class="admin-stat-card" style="border-left: 4px solid #7C3AED;">
            <div>
                <span style="font-size: 0.8rem; font-weight: 700; color: var(--neutral-600, #475569); display: block; margin-bottom: 0.25rem;">Antrean Waiting List</span>
                <span style="font-size: 1.85rem; font-weight: 800; color: var(--neutral-900, #0F172A); line-height: 1.1;">{{ number_format($waitingListCount) }}</span>
                <span style="font-size: 0.75rem; color: #6D28D9; font-weight: 600; display: block; margin-top: 0.25rem;">Prioritas antrean FIFO (BR-05)</span>
            </div>
            <div style="width: 46px; height: 46px; border-radius: 10px; background: #F5F3FF; color: #7C3AED; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </div>
        </div>

        <!-- Card 4: Peserta Magang Aktif -->
        <div class="admin-stat-card" style="border-left: 4px solid #10B981;">
            <div>
                <span style="font-size: 0.8rem; font-weight: 700; color: var(--neutral-600, #475569); display: block; margin-bottom: 0.25rem;">Peserta Magang Aktif</span>
                <span style="font-size: 1.85rem; font-weight: 800; color: var(--neutral-900, #0F172A); line-height: 1.1;">{{ number_format($pesertaAktifCount) }}</span>
                <span style="font-size: 0.75rem; color: #047857; font-weight: 600; display: block; margin-top: 0.25rem;">Sedang menjalani tugas</span>
            </div>
            <div style="width: 46px; height: 46px; border-radius: 10px; background: #ECFDF5; color: #10B981; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </div>
        </div>

    </div>

    {{-- 4. COMMAND CENTER WORKSPACE: 4 TABS --}}
    <div class="admin-card">
        
        {{-- Tab Navigation --}}
        <div class="admin-tab-nav">
            <button type="button" class="admin-tab-btn active" onclick="switchAdminTab('tab-action-hub', this)">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                <span>Antrean Verifikasi Mendesak ({{ $pendaftaranTerbaru->count() }})</span>
            </button>
            <button type="button" class="admin-tab-btn" onclick="switchAdminTab('tab-kuota', this)">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                <span>Keterisian Kuota Jurusan ({{ $kuotaList->count() }})</span>
            </button>
            <button type="button" class="admin-tab-btn" onclick="switchAdminTab('tab-pembimbing', this)">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
                <span>Beban Pembimbing Lapangan ({{ $pembimbingWorkload->count() }})</span>
            </button>
            <button type="button" class="admin-tab-btn" onclick="switchAdminTab('tab-penempatan', this)">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                <span>Arsip Penempatan Terkini ({{ $penempatanTerbaru->count() }})</span>
            </button>
        </div>

        {{-- TAB 1: ACTION HUB & ANTREAN VERIFIKASI MENDESAK --}}
        <div id="tab-action-hub" class="admin-tab-content" style="display: block;">
            <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
                <div>
                    <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-900, #0F172A); margin: 0;">
                        Antrean Verifikasi Perlu Tindak Lanjut
                    </h3>
                    <p style="font-size: 0.8rem; color: var(--neutral-600, #475569); margin: 0.2rem 0 0 0;">
                        Pengajuan berstatus Diajukan dan Perlu Perbaikan diurutkan berdasarkan usia berkas untuk penegakan SLA target ≤ 5 hari kerja (PRD 2.3).
                    </p>
                </div>
                <a href="{{ route('admin.pendaftaran.index') }}" style="font-size: 0.825rem; font-weight: 700; color: #0641CC; text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;">
                    <span>Buka Semua Antrean Pendaftaran</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </a>
            </div>

            @if($pendaftaranTerbaru->isEmpty())
                <div style="padding: 3rem 1.5rem; text-align: center; color: var(--neutral-600, #475569);">
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: #ECFDF5; color: #10B981; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem auto;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <h4 style="font-size: 1rem; font-weight: 700; color: var(--neutral-900, #0F172A); margin-bottom: 0.25rem;">Semua Pengajuan Telah Diproses</h4>
                    <p style="font-size: 0.8125rem; margin: 0;">Tidak ada berkas yang tertahan dalam antrean verifikasi saat ini.</p>
                </div>
            @else
                <div style="overflow-x: auto;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>No. Pendaftaran</th>
                                <th>Calon Peserta</th>
                                <th>Institusi & Jurusan</th>
                                <th>Bulan Diajukan</th>
                                <th>Usia Pengajuan (SLA)</th>
                                <th style="text-align: center;">Status</th>
                                <th style="text-align: right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendaftaranTerbaru as $item)
                                @php
                                    $tglPengajuan = $item->diajukan_at ?? $item->created_at;
                                    $umurHari = (int) $tglPengajuan->diffInDays(now());
                                    $slaKritis = $umurHari >= 3;
                                @endphp
                                <tr>
                                    <td style="font-weight: 700; color: #0641CC;">
                                        {{ $item->nomor_pendaftaran }}
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; color: var(--neutral-900, #0F172A);">{{ $item->user?->nama }}</div>
                                        <small style="color: var(--neutral-500, #64748B);">{{ $item->user?->email }}</small>
                                    </td>
                                    <td>
                                        <div>{{ $item->user?->profil?->institusi ?? '-' }}</div>
                                        <small style="color: var(--neutral-500, #64748B);">{{ $item->kuota?->jurusan?->nama_jurusan }}</small>
                                    </td>
                                    <td>
                                        {{ $item->bulanMagang?->bulan_mulai?->translatedFormat('F Y') }}
                                    </td>
                                    <td>
                                        @if($slaKritis)
                                            <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.2rem 0.55rem; border-radius: 6px; font-size: 0.72rem; font-weight: 700; background: #FEF2F2; color: #DC2626; border: 1px solid #FECACA;">
                                                ⚠️ {{ $umurHari }} hari lalu
                                            </span>
                                        @else
                                            <span style="display: inline-flex; align-items: center; padding: 0.2rem 0.55rem; border-radius: 6px; font-size: 0.72rem; font-weight: 600; background: #F8FAFC; color: var(--neutral-600, #475569); border: 1px solid var(--neutral-200, #E2E8F0);">
                                                {{ $umurHari === 0 ? 'Hari ini' : $umurHari . ' hari lalu' }}
                                            </span>
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        @if($item->status->value === 'diajukan')
                                            <span style="display: inline-flex; align-items: center; padding: 0.2rem 0.65rem; border-radius: 9999px; font-size: 0.72rem; font-weight: 700; background: #FEF3C7; color: #92400E; border: 1px solid #FCD34D;">
                                                Diajukan
                                            </span>
                                        @else
                                            <span style="display: inline-flex; align-items: center; padding: 0.2rem 0.65rem; border-radius: 9999px; font-size: 0.72rem; font-weight: 700; background: #EFF6FF; color: #1E40AF; border: 1px solid #BFDBFE;">
                                                Perlu Perbaikan
                                            </span>
                                        @endif
                                    </td>
                                    <td style="text-align: right;">
                                        <a href="{{ route('admin.pendaftaran.show', $item) }}" 
                                           style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.4rem 0.85rem; background: #0641CC; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 700; font-size: 0.8rem; box-shadow: 0 1px 3px rgba(6, 65, 204, 0.2);">
                                            <span>Tinjau</span>
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- TAB 2: UTILISASI KUOTA JURUSAN --}}
        <div id="tab-kuota" class="admin-tab-content" style="display: none; padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
                <div>
                    <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-900, #0F172A); margin: 0;">
                        Alokasi Kuota Jurusan (Periode {{ $bulanAktif ? $bulanAktif->bulan_mulai?->translatedFormat('F Y') : 'Aktif' }})
                    </h3>
                    <p style="font-size: 0.8rem; color: var(--neutral-600, #475569); margin: 0.2rem 0 0 0;">
                        Monitoring kuota umum per jurusan. Batas aturan bisnis: total kuota umum ≤ 12 (BR-01).
                    </p>
                </div>
                <a href="{{ route('admin.kuota.index') }}" style="font-size: 0.825rem; font-weight: 700; color: #0641CC; text-decoration: none;">
                    Kelola Konfigurasi Kuota &rarr;
                </a>
            </div>

            @if($kuotaList->isEmpty())
                <div style="padding: 2.5rem; text-align: center; color: var(--neutral-600, #475569); background: #F8FAFC; border-radius: 10px;">
                    <p style="margin: 0; font-size: 0.875rem;">Belum ada kuota jurusan yang dikonfigurasi untuk periode ini.</p>
                </div>
            @else
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem;">
                    @foreach($kuotaList as $kuota)
                        @php
                            $persen = $kuota->jumlah > 0 ? min(100, round(($kuota->terisi / $kuota->jumlah) * 100)) : 0;
                            $sisa = max(0, $kuota->jumlah - $kuota->terisi);
                            $barColor = $persen >= 100 ? '#DC2626' : ($persen >= 75 ? '#F59E0B' : '#0641CC');
                        @endphp
                        <div style="background: #F8FAFC; border: 1px solid var(--neutral-200, #E2E8F0); border-radius: 12px; padding: 1.25rem;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                                <div>
                                    <span style="font-weight: 700; font-size: 0.95rem; color: var(--neutral-900, #0F172A); display: block;">
                                        {{ $kuota->jurusan?->nama_jurusan }}
                                    </span>
                                    <span style="font-size: 0.75rem; color: var(--neutral-500, #64748B); text-transform: uppercase;">
                                        Jenjang: {{ $kuota->jurusan?->kategori === 'smk' ? 'SMK' : 'Mahasiswa' }}
                                    </span>
                                </div>
                                <span style="font-size: 0.72rem; font-weight: 700; padding: 0.2rem 0.55rem; border-radius: 6px; background: {{ $sisa > 0 ? '#ECFDF5' : '#FEF2F2' }}; color: {{ $sisa > 0 ? '#047857' : '#DC2626' }}; border: 1px solid {{ $sisa > 0 ? '#A7F3D0' : '#FECACA' }};">
                                    {{ $sisa > 0 ? 'Sisa ' . $sisa . ' Slot' : 'Penuh' }}
                                </span>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: var(--neutral-600, #475569); margin-bottom: 0.4rem;">
                                <span>Terisi: <strong>{{ $kuota->terisi }}</strong> dari {{ $kuota->jumlah }}</span>
                                <span style="font-weight: 700; color: {{ $barColor }};">{{ $persen }}%</span>
                            </div>
                            <div style="width: 100%; height: 8px; background: #E2E8F0; border-radius: 999px; overflow: hidden;">
                                <div style="width: {{ $persen }}%; height: 100%; background: {{ $barColor }}; border-radius: 999px; transition: width 0.3s ease;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- TAB 3: MONITORING BEBAN PEMBIMBING LAPANGAN (BR-13) --}}
        <div id="tab-pembimbing" class="admin-tab-content" style="display: none; padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
                <div>
                    <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-900, #0F172A); margin: 0;">
                        Monitoring Kapasitas & Beban Kerja Pembimbing (BR-13)
                    </h3>
                    <p style="font-size: 0.8rem; color: var(--neutral-600, #475569); margin: 0.2rem 0 0 0;">
                        Pastikan distribusi peserta merata dan pembimbing tidak melampaui batas beban aktif bimbingan.
                    </p>
                </div>
                <a href="{{ route('admin.penempatan.index') }}" style="font-size: 0.825rem; font-weight: 700; color: #0641CC; text-decoration: none;">
                    Kelola Penempatan Staf &rarr;
                </a>
            </div>

            @if($pembimbingWorkload->isEmpty())
                <div style="padding: 2.5rem; text-align: center; color: var(--neutral-600, #475569); background: #F8FAFC; border-radius: 10px;">
                    <p style="margin: 0; font-size: 0.875rem;">Belum ada akun pembimbing yang terdaftar dan aktif.</p>
                </div>
            @else
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
                    @foreach($pembimbingWorkload as $pmb)
                        <div style="background: #F8FAFC; border: 1px solid var(--neutral-200, #E2E8F0); border-radius: 12px; padding: 1.25rem; display: flex; flex-direction: column; justify-content: space-between;">
                            <div>
                                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                                    <div style="width: 40px; height: 40px; border-radius: 10px; background: #DBEAFE; color: #1E40AF; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.9rem; flex-shrink: 0;">
                                        {{ strtoupper(substr($pmb->nama, 0, 1)) }}
                                    </div>
                                    <div style="min-width: 0;">
                                        <span style="font-weight: 700; font-size: 0.95rem; color: var(--neutral-900, #0F172A); display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $pmb->nama }}
                                        </span>
                                        <span style="font-size: 0.75rem; color: var(--neutral-500, #64748B);">
                                            {{ $pmb->bidang?->nama_bidang ?? 'Dinas Kominfo Sleman' }}
                                        </span>
                                    </div>
                                </div>
                                <div style="padding: 0.6rem 0.75rem; border-radius: 8px; background: #ffffff; border: 1px solid var(--neutral-200, #E2E8F0); margin-bottom: 0.75rem; display: flex; justify-content: space-between; align-items: center;">
                                    <span style="font-size: 0.75rem; color: var(--neutral-600, #475569);">Peserta Dibimbing:</span>
                                    <span style="font-weight: 800; font-size: 0.95rem; color: #0641CC;">
                                        {{ $pmb->aktif_count }} Mentee Aktif
                                    </span>
                                </div>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="display: inline-flex; align-items: center; gap: 0.3rem; font-size: 0.72rem; font-weight: 700; color: #166534;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #16A34A;"></span>
                                    <span>Siap Menerima Mentee</span>
                                </span>
                                <a href="{{ route('admin.penempatan.index') }}" style="font-size: 0.75rem; font-weight: 700; color: #0641CC; text-decoration: none;">
                                    Tempatkan &rarr;
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- TAB 4: ARSIP PENEMPATAN TERKINI --}}
        <div id="tab-penempatan" class="admin-tab-content" style="display: none;">
            <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
                <div>
                    <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-900, #0F172A); margin: 0;">
                        Peserta Baru Ditempatkan
                    </h3>
                    <p style="font-size: 0.8rem; color: var(--neutral-600, #475569); margin: 0.2rem 0 0 0;">
                        Daftar 5 penempatan aktif terkini lengkap dengan akses dokumen Surat Keterangan Diterima resmi (F-16).
                    </p>
                </div>
                <a href="{{ route('admin.penempatan.index') }}" style="font-size: 0.825rem; font-weight: 700; color: #0641CC; text-decoration: none;">
                    Buka Direktori Penempatan &rarr;
                </a>
            </div>

            @if($penempatanTerbaru->isEmpty())
                <div style="padding: 3rem 1.5rem; text-align: center; color: var(--neutral-600, #475569);">
                    <p style="margin: 0; font-size: 0.875rem;">Belum ada peserta yang ditempatkan pada periode ini.</p>
                </div>
            @else
                <div style="overflow-x: auto;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Nama Peserta</th>
                                <th>Institusi</th>
                                <th>Pembimbing Lapangan</th>
                                <th>Durasi & Periode Magang</th>
                                <th>Surat Diterima (F-16)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($penempatanTerbaru as $p)
                                <tr>
                                    <td>
                                        <div style="font-weight: 700; color: var(--neutral-900, #0F172A);">{{ $p->peserta?->nama }}</div>
                                        <small style="color: var(--neutral-500, #64748B);">{{ $p->peserta?->email }}</small>
                                    </td>
                                    <td>
                                        <div>{{ $p->peserta?->profil?->institusi ?? '-' }}</div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: #0641CC;">{{ $p->pembimbing?->nama ?? 'Belum Ditentukan' }}</div>
                                        <small style="color: var(--neutral-500, #64748B);">{{ $p->bidang?->nama_bidang ?? 'Dinas Kominfo' }}</small>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.8rem;">
                                            {{ $p->tgl_mulai?->format('d M Y') }} - {{ $p->tgl_selesai?->format('d M Y') }}
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.penempatan.surat-diterima', $p->id) }}" 
                                           target="_blank" 
                                           style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.35rem 0.65rem; background: #ffffff; border: 1px solid var(--neutral-300, #CBD5E1); color: var(--neutral-700, #334155); border-radius: 6px; font-weight: 600; font-size: 0.75rem; text-decoration: none;">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                                            <span>Unduh PDF</span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>

    {{-- 5. QUICK ACTION SHORTCUTS FOOTER --}}
    <div style="background: #ffffff; border: 1px solid var(--neutral-200, #E2E8F0); border-radius: 14px; padding: 1.25rem 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <h3 style="font-size: 0.9rem; font-weight: 700; color: var(--neutral-900, #0F172A); margin: 0 0 0.85rem 0; display: flex; align-items: center; gap: 0.45rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0641CC" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
            <span>Aksi Cepat Operasional Administrasi</span>
        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 0.85rem;">
            <a href="{{ route('admin.pendaftaran.index') }}" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; background: #F8FAFC; border: 1px solid var(--neutral-200, #E2E8F0); border-radius: 10px; text-decoration: none; color: var(--neutral-800, #1E293B); transition: all 0.15s ease;">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: #0751FF; color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><circle cx="10" cy="13" r="2"></circle><path d="m20 17-1.09-1.09a2 2 0 0 0-2.82 0L10 22"></path></svg>
                </div>
                <div>
                    <span style="font-weight: 700; font-size: 0.85rem; display: block;">Verifikasi Berkas</span>
                    <span style="font-size: 0.72rem; color: var(--neutral-500, #64748B);">Tinjau pengajuan baru</span>
                </div>
            </a>

            <a href="{{ route('admin.kuota.index') }}" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; background: #F8FAFC; border: 1px solid var(--neutral-200, #E2E8F0); border-radius: 10px; text-decoration: none; color: var(--neutral-800, #1E293B); transition: all 0.15s ease;">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: #0641CC; color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                </div>
                <div>
                    <span style="font-weight: 700; font-size: 0.85rem; display: block;">Manajemen Kuota</span>
                    <span style="font-size: 0.72rem; color: var(--neutral-500, #64748B);">Alokasi kuota jurusan</span>
                </div>
            </a>

            <a href="{{ route('admin.penempatan.index') }}" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; background: #F8FAFC; border: 1px solid var(--neutral-200, #E2E8F0); border-radius: 10px; text-decoration: none; color: var(--neutral-800, #1E293B); transition: all 0.15s ease;">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: #10B981; color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
                </div>
                <div>
                    <span style="font-weight: 700; font-size: 0.85rem; display: block;">Penempatan Staf</span>
                    <span style="font-size: 0.72rem; color: var(--neutral-500, #64748B);">Input manual & pembimbing</span>
                </div>
            </a>

            <a href="{{ route('admin.waiting-list.index') }}" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; background: #F8FAFC; border: 1px solid var(--neutral-200, #E2E8F0); border-radius: 10px; text-decoration: none; color: var(--neutral-800, #1E293B); transition: all 0.15s ease;">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: #7C3AED; color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                </div>
                <div>
                    <span style="font-weight: 700; font-size: 0.85rem; display: block;">Waiting List</span>
                    <span style="font-size: 0.72rem; color: var(--neutral-500, #64748B);">Kelola antrean FIFO</span>
                </div>
            </a>
        </div>
    </div>

</div>

<script>
function switchAdminTab(tabId, btn) {
    document.querySelectorAll('.admin-tab-content').forEach(content => {
        content.style.display = 'none';
    });
    document.querySelectorAll('.admin-tab-btn').forEach(b => {
        b.classList.remove('active');
    });

    const target = document.getElementById(tabId);
    if (target) {
        target.style.display = 'block';
    }
    if (btn) {
        btn.classList.add('active');
    }
}
</script>
@endsection
