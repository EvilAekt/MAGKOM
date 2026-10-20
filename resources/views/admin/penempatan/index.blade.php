@extends('layouts.dashboard')

@section('title', 'Penempatan Peserta - Admin MagKom Sleman')
@section('page_title', 'Penempatan Peserta')
@section('page_description', 'Kelola penugasan pembimbing lapangan, pantau peserta aktif, dan kelola administrasi penempatan magang.')

@section('breadcrumbs')
    <nav style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; color: var(--neutral-600, #64748B);">
        <span>Portal MagKom</span>
        <span>/</span>
        <span>Administrasi</span>
        <span>/</span>
        <span style="color: var(--neutral-900, #0F172A); font-weight: 700;">Penempatan Peserta</span>
    </nav>
@endsection

@section('page_actions')
    <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
        <a href="{{ route('admin.penempatan.ekspor-surat-diterima', request()->query()) }}" style="display: inline-flex; align-items: center; gap: 0.5rem; background: var(--neutral-0, #FFFFFF); color: var(--neutral-800, #1E293B); border: 1px solid var(--neutral-300, #CBD5E1); padding: 0.6rem 1.15rem; border-radius: 8px; font-weight: 600; font-size: 0.875rem; text-decoration: none; box-shadow: var(--shadow-subtle, 0 1px 2px rgba(0,0,0,0.05)); transition: all 0.15s ease;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            <span>Ekspor Rekap Surat (CSV)</span>
        </a>

        @if(isset($slotKhususList) && $slotKhususList->isNotEmpty())
            <button type="button" onclick="openManualModal()" style="display: inline-flex; align-items: center; gap: 0.5rem; background: #0641CC; color: #FFFFFF; border: none; padding: 0.6rem 1.25rem; border-radius: 8px; font-weight: 600; font-size: 0.875rem; cursor: pointer; box-shadow: 0 1px 3px rgba(6, 65, 204, 0.25); transition: all 0.15s ease;" onmouseover="this.style.background='#0533A3'" onmouseout="this.style.background='#0641CC'">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Penempatan Jalur Khusus</span>
            </button>
        @endif
    </div>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem; max-width: 1400px; margin: 0 auto; width: 100%;">

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
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(6px);
            z-index: 1050;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .modal-container {
            background: #ffffff;
            border-radius: 16px;
            max-width: 540px;
            width: 100%;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            border: 1px solid var(--neutral-200, #E2E8F0);
            animation: modalFadeIn 0.2s ease-out;
        }
        @keyframes modalFadeIn {
            from { opacity: 0; transform: scale(0.96); }
            to { opacity: 1; transform: scale(1); }
        }

        /* =====================================================================
           DARK MODE OVERRIDES (DS-MagKom Standards)
           ===================================================================== */
        [data-theme="dark"] .admin-stat-card {
            background-color: #182234 !important;
            border-color: #26354D !important;
        }

        [data-theme="dark"] .admin-stat-card div div {
            color: #F8FAFC !important;
        }

        [data-theme="dark"] .admin-stat-card span[style*="color: var(--neutral-500"] {
            color: #94A3B8 !important;
        }

        [data-theme="dark"] a[href*="ekspor-surat-diterima"] {
            background: #111C2E !important;
            border-color: #26354D !important;
            color: #CBD5E1 !important;
        }

        [data-theme="dark"] a[href*="ekspor-surat-diterima"]:hover {
            background: #162238 !important;
            border-color: #3B82F6 !important;
            color: #60A5FA !important;
        }

        [data-theme="dark"] .admin-card {
            background-color: #182234 !important;
            border-color: #26354D !important;
        }

        [data-theme="dark"] .admin-card label {
            color: #CBD5E1 !important;
        }

        [data-theme="dark"] .admin-card input#q-input {
            background-color: #0F172A !important;
            border-color: #26354D !important;
            color: #F8FAFC !important;
        }

        [data-theme="dark"] .admin-card select {
            background-color: #0F172A !important;
            border-color: #26354D !important;
            color: #F8FAFC !important;
        }

        [data-theme="dark"] .admin-card select option {
            background-color: #0F172A !important;
            color: #F8FAFC !important;
        }

        [data-theme="dark"] .admin-card a[href*="admin.penempatan.index"] {
            background: #111C2E !important;
            border-color: #26354D !important;
            color: #94A3B8 !important;
        }

        [data-theme="dark"] .admin-card a[href*="admin.penempatan.index"]:hover {
            background: #162238 !important;
            color: #F8FAFC !important;
            border-color: #3B82F6 !important;
        }

        [data-theme="dark"] .admin-card div[style*="background: #ffffff"] {
            background-color: #141E30 !important;
            border-color: #26354D !important;
        }

        [data-theme="dark"] .admin-card h3 {
            color: #F8FAFC !important;
        }

        [data-theme="dark"] .admin-table th {
            background-color: #0E1726 !important;
            color: #94A3B8 !important;
            border-bottom-color: #26354D !important;
        }

        [data-theme="dark"] .admin-table td {
            background-color: #182234 !important;
            color: #CBD5E1 !important;
            border-bottom-color: #26354D !important;
        }

        [data-theme="dark"] .admin-table tr:hover td,
        [data-theme="dark"] .admin-table tr:hover {
            background-color: #162238 !important;
        }

        [data-theme="dark"] .admin-table td div[style*="color: var(--neutral-900"] {
            color: #F8FAFC !important;
        }

        [data-theme="dark"] .admin-table td div[style*="color: var(--neutral-800"] {
            color: #CBD5E1 !important;
        }

        [data-theme="dark"] .admin-table td div[style*="color: var(--neutral-500"] {
            color: #94A3B8 !important;
        }

        [data-theme="dark"] .admin-table td a[style*="background: #ffffff"] {
            background: #111C2E !important;
            border-color: #26354D !important;
            color: #CBD5E1 !important;
        }

        [data-theme="dark"] .admin-table td a[style*="background: #ffffff"]:hover {
            background: #162238 !important;
            border-color: #3B82F6 !important;
            color: #60A5FA !important;
        }

        [data-theme="dark"] .modal-container {
            background-color: #182234 !important;
            border-color: #26354D !important;
            color: #F8FAFC;
        }

        [data-theme="dark"] .modal-container div[style*="background: #F8FAFC"] {
            background-color: #141E30 !important;
            border-bottom-color: #26354D !important;
        }

        [data-theme="dark"] .modal-container h3 {
            color: #F8FAFC !important;
        }

        [data-theme="dark"] .modal-container p {
            color: #94A3B8 !important;
        }

        [data-theme="dark"] .modal-container label {
            color: #CBD5E1 !important;
        }

        [data-theme="dark"] .modal-container select,
        [data-theme="dark"] .modal-container input,
        [data-theme="dark"] .modal-container textarea {
            background-color: #0F172A !important;
            border-color: #26354D !important;
            color: #F8FAFC !important;
        }

        [data-theme="dark"] .modal-container select option {
            background-color: #0F172A !important;
            color: #F8FAFC !important;
        }

        [data-theme="dark"] .modal-container div[style*="background: var(--neutral-100"] {
            background-color: #0F172A !important;
            border: 1px solid #26354D !important;
        }

        [data-theme="dark"] .modal-container div[style*="border-top: 1px solid"] {
            border-top-color: #26354D !important;
        }

        [data-theme="dark"] .modal-container button[style*="background: #ffffff"],
        [data-theme="dark"] .modal-container button[style*="background: var(--neutral-0"] {
            background: #111C2E !important;
            border-color: #26354D !important;
            color: #CBD5E1 !important;
        }

        [data-theme="dark"] #bulkActionBar {
            background-color: #182234 !important;
            border-color: #26354D !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5) !important;
        }
    </style>

    {{-- 1. HERO STATS CARDS --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
        {{-- Total Penempatan --}}
        <div class="admin-stat-card">
            <div>
                <span style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-500, #64748B);">Total Penempatan</span>
                <div style="font-size: 1.75rem; font-weight: 800; color: #0F172A; margin-top: 0.25rem; letter-spacing: -0.02em;">
                    {{ number_format($penempatanStats['total'] ?? $penempatanList->total()) }}
                </div>
                <span style="font-size: 0.75rem; color: var(--neutral-500, #64748B); display: inline-flex; align-items: center; gap: 0.3rem; margin-top: 0.35rem;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                    Semua riwayat peserta
                </span>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #EFF6FF; color: #1D4ED8; display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </div>
        </div>

        {{-- Peserta Aktif --}}
        <div class="admin-stat-card" style="border-left: 4px solid #10B981;">
            <div>
                <span style="font-size: 0.8125rem; font-weight: 600; color: #047857;">Peserta Magang Aktif</span>
                <div style="font-size: 1.75rem; font-weight: 800; color: #059669; margin-top: 0.25rem; letter-spacing: -0.02em;">
                    {{ number_format($penempatanStats['aktif'] ?? 0) }}
                </div>
                <span style="font-size: 0.75rem; color: #059669; display: inline-flex; align-items: center; gap: 0.3rem; margin-top: 0.35rem;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    Sedang masa bimbingan
                </span>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #D1FAE5; color: #059669; display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            </div>
        </div>

        {{-- Selesai Magang --}}
        <div class="admin-stat-card">
            <div>
                <span style="font-size: 0.8125rem; font-weight: 600; color: #1E40AF;">Selesai Magang</span>
                <div style="font-size: 1.75rem; font-weight: 800; color: #1E40AF; margin-top: 0.25rem; letter-spacing: -0.02em;">
                    {{ number_format($penempatanStats['selesai'] ?? 0) }}
                </div>
                <span style="font-size: 0.75rem; color: var(--neutral-500, #64748B); display: inline-flex; align-items: center; gap: 0.3rem; margin-top: 0.35rem;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    Telah tuntas & dinilai
                </span>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #DBEAFE; color: #1E40AF; display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
            </div>
        </div>

        {{-- Jalur Khusus (Kemitraan/Slot) --}}
        <div class="admin-stat-card">
            <div>
                <span style="font-size: 0.8125rem; font-weight: 600; color: #B45309;">Jalur Khusus Mitra</span>
                <div style="font-size: 1.75rem; font-weight: 800; color: #B45309; margin-top: 0.25rem; letter-spacing: -0.02em;">
                    {{ number_format($penempatanStats['khusus'] ?? 0) }}
                </div>
                <span style="font-size: 0.75rem; color: var(--neutral-500, #64748B); display: inline-flex; align-items: center; gap: 0.3rem; margin-top: 0.35rem;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg>
                    MoU & kuota terproteksi
                </span>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path></svg>
            </div>
        </div>

        {{-- Menunggu Penempatan (Antrean Lolos Berkas) --}}
        @if(($penempatanStats['belum_ditempatkan'] ?? 0) > 0)
            <div class="admin-stat-card" style="border-left: 4px solid #F59E0B; background: #FFFDF5;">
                <div>
                    <span style="font-size: 0.8125rem; font-weight: 600; color: #B45309;">Menunggu Plotting</span>
                    <div style="font-size: 1.75rem; font-weight: 800; color: #D97706; margin-top: 0.25rem; letter-spacing: -0.02em;">
                        {{ number_format($penempatanStats['belum_ditempatkan'] ?? 0) }}
                    </div>
                    <span style="font-size: 0.75rem; color: #B45309; display: inline-flex; align-items: center; gap: 0.3rem; margin-top: 0.35rem;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        Lolos berkas (Tahap 1)
                    </span>
                </div>
                <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEF3C7; color: #B45309; display: flex; align-items: center; justify-content: center;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
                </div>
            </div>
        @endif
    </div>

    {{-- 2. ANTREAN PESERTA LOLOS SELEKSI BERKAS (MENUNGGU PENETAPAN PEMBIMBING) --}}
    @if(isset($antreanBelumDitempatkan) && $antreanBelumDitempatkan->isNotEmpty())
        <div class="admin-card" style="border-left: 4px solid #F59E0B; background: #FFFBEB;">
            <div style="padding: 1.15rem 1.5rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #FDE68A; flex-wrap: wrap; gap: 0.75rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 36px; height: 36px; border-radius: 8px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    </div>
                    <div>
                        <h3 style="font-size: 1rem; font-weight: 800; color: #92400E; margin: 0;">
                            Antrean Peserta Lolos Berkas (Menunggu Penempatan)
                        </h3>
                        <p style="font-size: 0.8125rem; color: #B45309; margin: 0.2rem 0 0 0;">
                            Peserta di bawah ini telah disetujui berkasnya pada Tahap 1 dan siap ditetapkan Pembimbing Lapangan serta Bidang Dinas.
                        </p>
                    </div>
                </div>
                <span style="padding: 0.35rem 0.85rem; border-radius: 9999px; font-size: 0.8125rem; font-weight: 700; background: #FEF3C7; color: #B45309; border: 1px solid #FCD34D;">
                    {{ $antreanBelumDitempatkan->count() }} Menunggu Penempatan
                </span>
            </div>

            <div style="overflow-x: auto;">
                <table class="admin-table">
                    <thead>
                        <tr style="background: #FEF9C3;">
                            <th style="color: #854D0E;">Peserta Magang</th>
                            <th style="color: #854D0E;">Jurusan &amp; Kampus / Sekolah</th>
                            <th style="color: #854D0E;">Bulan Magang</th>
                            <th style="color: #854D0E;">Diterima Pada</th>
                            <th style="text-align: right; width: 150px; color: #854D0E;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($antreanBelumDitempatkan as $antrean)
                            @php
                                $firstBidang = $antrean->kuota?->jurusan?->bidang?->first();
                                $defBidangId = $firstBidang?->id;
                                $defBidangNama = $firstBidang?->nama_bidang ?? 'Dinas Kominfo';
                            @endphp
                            <tr style="background: #FFFFFF;">
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.65rem;">
                                        <div style="width: 34px; height: 34px; border-radius: 8px; background: #FDE68A; color: #92400E; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem;">
                                            {{ strtoupper(substr($antrean->user?->nama ?? 'P', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; color: #1E293B; font-size: 0.875rem;">
                                                {{ $antrean->user?->nama }}
                                            </div>
                                            <div style="font-size: 0.75rem; color: #64748B;">
                                                NIM: {{ $antrean->user?->profil?->nim_nisn ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: #1E293B;">
                                        {{ $antrean->kuota?->jurusan?->nama_jurusan ?? '-' }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: #64748B;">
                                        {{ $antrean->user?->profil?->institusi ?? '-' }}
                                    </div>
                                </td>
                                <td>
                                    <span style="font-weight: 700; color: #0641CC; font-size: 0.8125rem;">
                                        {{ $antrean->bulanMagang?->bulan_mulai?->translatedFormat('F Y') }}
                                    </span>
                                    <div style="font-size: 0.75rem; color: #64748B;">
                                        Durasi: {{ $antrean->durasi_bulan }} Bulan
                                    </div>
                                </td>
                                <td>
                                    <span style="font-size: 0.8125rem; color: #475569;">
                                        {{ $antrean->diputuskan_at?->translatedFormat('d M Y H:i') ?? '-' }}
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <button type="button" 
                                            onclick="openTetapkanModal({{ $antrean->id }}, '{{ addslashes($antrean->user?->nama) }}', '{{ addslashes($antrean->kuota?->jurusan?->nama_jurusan ?? '') }}', {{ $defBidangId ?? 'null' }}, '{{ addslashes($defBidangNama) }}')" 
                                            style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.5rem 0.95rem; background: #0641CC; color: #FFFFFF; border: none; border-radius: 8px; font-weight: 700; font-size: 0.8125rem; cursor: pointer; box-shadow: 0 1px 3px rgba(6, 65, 204, 0.25); transition: background 0.15s ease;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
                                        <span>Tempatkan</span>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- 3. FILTER BAR CARD --}}
    <div class="admin-card" style="padding: 1.25rem 1.5rem;">
        <form method="GET" action="{{ route('admin.penempatan.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            {{-- Search Bar --}}
            <div style="display: flex; flex-direction: column; gap: 0.375rem; flex: 2; min-width: 240px;">
                <label for="q-input" style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Pencarian Peserta</label>
                <div style="position: relative;">
                    <input type="text" id="q-input" name="q" value="{{ request('q') }}" placeholder="Cari nama peserta, email, kampus..." style="width: 100%; height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem 0 2.4rem; font-size: 0.875rem; background: #ffffff; color: var(--neutral-900, #0F172A);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 0.85rem; top: 13px; color: var(--neutral-400, #94A3B8);">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </div>
            </div>

            {{-- Filter Bulan Magang --}}
            <div style="display: flex; flex-direction: column; gap: 0.375rem; flex: 1; min-width: 180px;">
                <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Bulan Magang</label>
                <select name="bulan_magang_id" style="height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff; color: var(--neutral-800, #1E293B);">
                    <option value="">Semua Bulan Magang</option>
                    @foreach($bulanList as $bln)
                        <option value="{{ $bln->id }}" {{ request('bulan_magang_id') == $bln->id ? 'selected' : '' }}>
                            {{ $bln->nama_bulan ? $bln->nama_bulan . ' (' . $bln->bulan_mulai?->translatedFormat('F Y') . ')' : $bln->bulan_mulai?->translatedFormat('F Y') }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Status --}}
            <div style="display: flex; flex-direction: column; gap: 0.375rem; flex: 1; min-width: 180px;">
                <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Status Penempatan</label>
                <select name="status" style="height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff; color: var(--neutral-800, #1E293B);">
                    <option value="">Semua Status</option>
                    @foreach($statusEnum as $status)
                        <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Pembimbing --}}
            <div style="display: flex; flex-direction: column; gap: 0.375rem; flex: 1; min-width: 200px;">
                <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Pembimbing Lapangan</label>
                <select name="pembimbing_id" style="height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff; color: var(--neutral-800, #1E293B);">
                    <option value="">Semua Pembimbing</option>
                    @foreach($pembimbingList as $pemb)
                        <option value="{{ $pemb->id }}" {{ request('pembimbing_id') == $pemb->id ? 'selected' : '' }}>
                            {{ $pemb->nama }} [{{ $pemb->bidang?->nama_bidang ?? 'Dinas' }}]
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Bidang --}}
            <div style="display: flex; flex-direction: column; gap: 0.375rem; flex: 1; min-width: 180px;">
                <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Bidang Dinas</label>
                <select name="bidang_id" style="height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff; color: var(--neutral-800, #1E293B);">
                    <option value="">Semua Bidang</option>
                    @foreach($bidangList as $bdg)
                        <option value="{{ $bdg->id }}" {{ request('bidang_id') == $bdg->id ? 'selected' : '' }}>
                            {{ $bdg->nama_bidang }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" style="height: 42px; padding: 0 1.25rem; background: #0641CC; color: #FFFFFF; font-weight: 600; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer; transition: background 0.15s ease; box-shadow: 0 1px 3px rgba(6, 65, 204, 0.2);">
                    Terapkan Filter
                </button>
                <a href="{{ route('admin.penempatan.index') }}" style="height: 42px; padding: 0 1rem; background: var(--neutral-100, #F1F5F9); color: var(--neutral-700, #334155); font-weight: 600; font-size: 0.875rem; text-decoration: none; border-radius: 8px; display: inline-flex; align-items: center; border: 1px solid var(--neutral-200, #E2E8F0);">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- 3. DATA TABLE CARD --}}
    <div class="admin-card">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0); display: flex; justify-content: space-between; align-items: center; background: #ffffff;">
            <div style="display: flex; align-items: center; gap: 0.6rem;">
                <h3 style="font-size: 1.0625rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0;">
                    Data Penempatan Peserta
                </h3>
                <span style="padding: 0.2rem 0.6rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: #EFF6FF; color: #1D4ED8;">
                    {{ $penempatanList->total() }} Peserta
                </span>
            </div>
            <span style="font-size: 0.8125rem; color: var(--neutral-500, #64748B);">
                Menampilkan {{ $penempatanList->firstItem() ?? 0 }}–{{ $penempatanList->lastItem() ?? 0 }} dari {{ $penempatanList->total() }}
            </span>
        </div>

        @if($penempatanList->isEmpty())
            <div style="padding: 4rem 1.5rem; text-align: center; color: var(--neutral-600, #64748B);">
                <div style="width: 64px; height: 64px; background: #EFF6FF; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem auto; color: #1D4ED8;">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
                <h4 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-900, #0F172A); margin-bottom: 0.375rem;">
                    Belum Ada Data Penempatan
                </h4>
                <p style="font-size: 0.875rem; max-width: 460px; margin: 0 auto;">Belum ada peserta magang yang sesuai dengan kriteria filter yang dipilih.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 40px; text-align: center;">
                                <input type="checkbox" id="checkAllPenempatan" onclick="toggleAllPenempatan(this)" style="cursor: pointer; width: 16px; height: 16px;">
                            </th>
                            <th>Peserta Magang</th>
                            <th>Pembimbing Lapangan</th>
                            <th>Jalur / Bidang Dinas</th>
                            <th>Bulan Magang</th>
                            <th>Status</th>
                            <th style="text-align: right; width: 170px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($penempatanList as $p)
                            @php
                                $statusVal = is_object($p->status) ? $p->status->value : $p->status;
                                $statusObj = is_object($p->status) ? $p->status : \App\Enums\StatusPenempatanEnum::tryFrom($p->status);
                                $badgeStyle = match($statusVal) {
                                    'aktif' => ['bg' => '#D1FAE5', 'color' => '#065F46', 'border' => '#A7F3D0'],
                                    'selesai' => ['bg' => '#EFF6FF', 'color' => '#1D4ED8', 'border' => '#DBEAFE'],
                                    'mengundurkan_diri' => ['bg' => '#F1F5F9', 'color' => '#475569', 'border' => '#CBD5E1'],
                                    'nonaktif' => ['bg' => '#FEE2E2', 'color' => '#991B1B', 'border' => '#FECACA'],
                                    default => ['bg' => '#F1F5F9', 'color' => '#475569', 'border' => '#CBD5E1'],
                                };
                            @endphp
                            <tr>
                                <td style="text-align: center;">
                                    <input type="checkbox" value="{{ $p->id }}" class="penempatan-checkbox" onchange="updateBulkUI()" style="cursor: pointer; width: 16px; height: 16px;">
                                </td>
                                {{-- Peserta Magang --}}
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div style="width: 38px; height: 38px; border-radius: 10px; background: linear-gradient(135deg, #0641CC 0%, #2563EB 100%); color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.875rem; flex-shrink: 0; box-shadow: 0 1px 3px rgba(6, 65, 204, 0.2);">
                                            {{ strtoupper(substr($p->peserta?->nama ?? 'P', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; color: var(--neutral-900, #0F172A); font-size: 0.875rem;">
                                                {{ $p->peserta?->nama }}
                                            </div>
                                            <div style="font-size: 0.75rem; color: var(--neutral-500, #64748B); margin-top: 0.1rem;">
                                                {{ $p->peserta?->profil?->institusi ?? '-' }}
                                            </div>
                                            <small style="font-size: 0.7rem; color: var(--neutral-400, #94A3B8);">
                                                NIM: {{ $p->peserta?->profil?->nim_nisn ?? '-' }}
                                            </small>
                                        </div>
                                    </div>
                                </td>

                                {{-- Pembimbing Lapangan --}}
                                <td>
                                    <div style="font-weight: 700; color: var(--neutral-900, #0F172A);">
                                        {{ $p->pembimbing?->nama ?? 'Belum Ditentukan' }}
                                    </div>
                                    @if($p->pembimbing)
                                        <div style="font-size: 0.75rem; color: #0641CC; font-weight: 600; margin-top: 0.1rem;">
                                            [{{ $p->pembimbing->bidang?->nama_bidang ?? 'Dinas Kominfo' }}]
                                        </div>
                                        <div style="font-size: 0.75rem; color: var(--neutral-500, #64748B); margin-top: 0.15rem;">
                                            Beban: <strong>{{ $p->pembimbing->mentee_aktif_count ?? 1 }} Mentee Aktif</strong>
                                        </div>
                                        <button type="button" onclick="openPindahModal({{ $p->id }}, '{{ addslashes($p->peserta?->nama) }}', {{ $p->pembimbing_id ?? 'null' }})" style="border: none; background: none; color: #0641CC; font-size: 0.75rem; font-weight: 700; cursor: pointer; padding: 0; margin-top: 0.25rem; text-decoration: underline;">
                                            Ganti Pembimbing
                                        </button>
                                    @endif
                                </td>

                                {{-- Jalur & Bidang Dinas --}}
                                <td>
                                    <div style="font-weight: 600; color: var(--neutral-900, #0F172A);">
                                        {{ $p->bidang?->nama_bidang ?? 'Dinas Kominfo Sleman' }}
                                    </div>
                                    <div style="margin-top: 0.25rem;">
                                        @if($p->slotKhusus)
                                            <span style="font-size: 0.7rem; font-weight: 700; padding: 0.15rem 0.5rem; border-radius: 4px; background: #FEF3C7; color: #B45309; border: 1px solid #FCD34D;">
                                                Jalur Khusus: {{ $p->slotKhusus->nama_mitra }}
                                            </span>
                                        @else
                                            <span style="font-size: 0.7rem; font-weight: 600; color: var(--neutral-500, #64748B);">
                                                Jalur Reguler (Kuota Umum)
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Periode Magang --}}
                                <td style="font-size: 0.8125rem;">
                                    <div style="font-weight: 700; color: var(--neutral-800, #1E293B);">
                                        {{ \Carbon\Carbon::parse($p->tgl_mulai)->translatedFormat('d M Y') }} s/d
                                    </div>
                                    <div style="font-weight: 700; color: var(--neutral-800, #1E293B);">
                                        {{ \Carbon\Carbon::parse($p->tgl_selesai)->translatedFormat('d M Y') }}
                                    </div>
                                </td>

                                {{-- Status --}}
                                <td>
                                    <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.25rem 0.65rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: {{ $badgeStyle['bg'] }}; color: {{ $badgeStyle['color'] }}; border: 1px solid {{ $badgeStyle['border'] }};">
                                        {{ $statusObj ? $statusObj->label() : ucfirst($statusVal) }}
                                    </span>
                                </td>

                                {{-- Aksi --}}
                                <td style="text-align: right;">
                                    <div style="display: inline-flex; gap: 0.35rem; align-items: center;">
                                        <a href="{{ route('admin.penempatan.show', $p) }}" title="Lihat Detail Penempatan" style="display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.4rem 0.65rem; background: #EEF2FF; color: #4338CA; border: 1px solid #C7D2FE; border-radius: 6px; font-weight: 700; font-size: 0.75rem; text-decoration: none; transition: all 0.15s ease;" onmouseover="this.style.background='#E0E7FF'" onmouseout="this.style.background='#EEF2FF'">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                            <span>Detail</span>
                                        </a>

                                        <button type="button" onclick="openSuratPreview('{{ route('admin.penempatan.surat-diterima.pratinjau', $p) }}', '{{ route('admin.penempatan.surat-diterima', $p) }}', 'Surat Diterima - {{ addslashes($p->peserta?->nama) }}')" title="Pratinjau Surat Keterangan Diterima Langsung" style="display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.4rem 0.65rem; background: #EFF6FF; color: #1D4ED8; border: 1px solid #DBEAFE; border-radius: 6px; font-weight: 700; font-size: 0.75rem; cursor: pointer; transition: all 0.15s ease;" onmouseover="this.style.background='#DBEAFE'" onmouseout="this.style.background='#EFF6FF'">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                            <span>Surat</span>
                                        </button>

                                        <a href="{{ route('admin.penempatan.surat-diterima', $p) }}" title="Unduh Surat Diterima PDF" style="padding: 0.4rem 0.65rem; background: #ffffff; color: var(--neutral-700, #334155); border: 1px solid var(--neutral-300, #CBD5E1); text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 0.75rem; display: inline-flex; align-items: center;">
                                            Unduh
                                        </a>

                                        @if($statusVal === 'aktif')
                                            <button type="button" onclick="openBatalModal({{ $p->id }}, '{{ addslashes($p->peserta?->nama) }}')" style="padding: 0.4rem 0.65rem; background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; border-radius: 6px; font-weight: 600; font-size: 0.75rem; cursor: pointer;">
                                                Batalkan
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($penempatanList->hasPages())
                <div style="padding: 1.25rem 1.5rem; border-top: 1px solid var(--neutral-200, #E2E8F0); background: #ffffff;">
                    {{ $penempatanList->links() }}
                </div>
            @endif
        @endif
    </div>

</div>

{{-- MODAL PENEMPATAN MANUAL (JALUR KHUSUS) --}}
<div id="modal-penempatan-manual" class="modal-overlay">
    <div class="modal-container" style="max-width: 620px; max-height: 90vh; overflow-y: auto;">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0); display: flex; justify-content: space-between; align-items: center; background: #F8FAFC;">
            <div>
                <h3 style="font-size: 1.0625rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0;">Penempatan Peserta Jalur Khusus</h3>
                <p style="font-size: 0.75rem; color: var(--neutral-500, #64748B); margin: 0.15rem 0 0 0;">
                    Alokasi peserta magang berdasarkan kuota kemitraan slot khusus dinas (AC-07-3)
                </p>
            </div>
            <button type="button" onclick="closeManualModal()" style="background: transparent; border: none; font-size: 1.25rem; color: var(--neutral-400, #94A3B8); cursor: pointer;">✕</button>
        </div>

        <form method="POST" action="{{ route('admin.penempatan.manual') }}" id="form-penempatan-manual" style="padding: 1.5rem;">
            @csrf

            {{-- Switcher Mode Peserta --}}
            <div style="display: flex; gap: 0.5rem; margin-bottom: 1.25rem; background: var(--neutral-100, #F1F5F9); padding: 0.35rem; border-radius: 8px;">
                <button type="button" id="btn-mode-terdaftar" onclick="switchPesertaMode('terdaftar')" style="flex: 1; padding: 0.5rem; font-size: 0.8125rem; font-weight: 700; border: none; border-radius: 6px; cursor: pointer; background: #0641CC; color: #FFFFFF;">
                    Peserta Terdaftar
                </button>
                <button type="button" id="btn-mode-baru" onclick="switchPesertaMode('baru')" style="flex: 1; padding: 0.5rem; font-size: 0.8125rem; font-weight: 700; border: none; border-radius: 6px; cursor: pointer; background: transparent; color: var(--neutral-700, #334155);">
                    + Daftarkan Peserta Baru
                </button>
            </div>
            <input type="hidden" name="mode" id="input-mode-peserta" value="terdaftar">

            {{-- Mode 1: Peserta Terdaftar --}}
            <div id="panel-peserta-terdaftar" style="margin-bottom: 1.25rem;">
                <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B); display: block; margin-bottom: 0.375rem;">
                    Pilih Calon Peserta Terdaftar <span style="color: #DC2626;">*</span>
                </label>
                <select name="peserta_id" id="select-peserta-id" style="width: 100%; height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff;">
                    <option value="">-- Pilih Peserta Aktif --</option>
                    @foreach($pesertaList ?? [] as $pes)
                        <option value="{{ $pes->id }}">{{ $pes->nama }} ({{ $pes->email }}) &bull; {{ $pes->profil?->institusi ?? '-' }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Mode 2: Daftarkan Peserta Baru --}}
            <div id="panel-peserta-baru" style="display: none; flex-direction: column; gap: 0.875rem; margin-bottom: 1.25rem; background: #EFF6FF; border: 1px solid #DBEAFE; border-radius: 8px; padding: 1rem;">
                <div style="font-size: 0.8125rem; font-weight: 700; color: #1E40AF;">
                    Identitas Peserta Baru (Akun akan dibuat otomatis)
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-800, #1E293B);">Nama Lengkap <span style="color: #DC2626;">*</span></label>
                        <input type="text" name="nama" id="input-nama-baru" placeholder="Nama siswa/mahasiswa" style="width: 100%; height: 38px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 6px; padding: 0 0.625rem; font-size: 0.8125rem; background: #ffffff;">
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-800, #1E293B);">Email Aktif <span style="color: #DC2626;">*</span></label>
                        <input type="email" name="email" id="input-email-baru" placeholder="email@sekolah.ac.id" style="width: 100%; height: 38px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 6px; padding: 0 0.625rem; font-size: 0.8125rem; background: #ffffff;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-800, #1E293B);">NIM / NISN <span style="color: #DC2626;">*</span></label>
                        <input type="text" name="nim_nisn" id="input-nim-baru" placeholder="Nomor induk siswa/mhs" style="width: 100%; height: 38px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 6px; padding: 0 0.625rem; font-size: 0.8125rem; background: #ffffff;">
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-800, #1E293B);">Jenjang Pendidikan <span style="color: #DC2626;">*</span></label>
                        <select name="jenjang" style="width: 100%; height: 38px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 6px; padding: 0 0.625rem; font-size: 0.8125rem; background: #ffffff;">
                            <option value="s1">S1 (Strata 1)</option>
                            <option value="d3">D3 (Diploma 3)</option>
                            <option value="smk">SMK</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-800, #1E293B);">Asal Kampus / Sekolah <span style="color: #DC2626;">*</span></label>
                        <input type="text" name="institusi" id="input-institusi-baru" placeholder="Nama Universitas / SMK" style="width: 100%; height: 38px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 6px; padding: 0 0.625rem; font-size: 0.8125rem; background: #ffffff;">
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-800, #1E293B);">Jurusan Asal</label>
                        <select name="jurusan_id" style="width: 100%; height: 38px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 6px; padding: 0 0.625rem; font-size: 0.8125rem; background: #ffffff;">
                            <option value="">-- Sesuai Master Jurusan --</option>
                            @foreach($jurusanList ?? [] as $j)
                                <option value="{{ $j->id }}">{{ $j->nama_jurusan }} ({{ strtoupper(is_object($j->kategori) ? $j->kategori->value : (string)$j->kategori) }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Pilihan Slot Khusus --}}
            <div id="wrapper-slot-khusus" style="display: flex; flex-direction: column; gap: 0.375rem; margin-bottom: 1rem;">
                <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">
                    Pilih Slot Kemitraan Khusus <span style="color: #DC2626;">*</span>
                </label>
                <select name="slot_khusus_id" id="select-slot-khusus" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                    <option value="">-- Pilih Slot Kemitraan Khusus --</option>
                    @foreach($slotKhususList as $sk)
                        <option value="{{ $sk->id }}">
                            {{ $sk->nama_mitra }} &bull; Bulan: {{ $sk->bulan_mulai?->translatedFormat('F Y') }} (Sisa: {{ $sk->jumlah - $sk->terisi }} Kursi)
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Pembimbing & Bidang --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1rem;">
                <div>
                    <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Pembimbing Lapangan <span style="color: #DC2626;">*</span></label>
                    <select name="pembimbing_id" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                        <option value="">-- Pilih Pembimbing --</option>
                        @foreach($pembimbingList as $p)
                            <option value="{{ $p->id }}">{{ $p->nama }} [{{ $p->bidang?->nama_bidang ?? 'Dinas' }}] ({{ $p->mentee_aktif_count ?? 0 }} Mentee)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Bidang Dinas</label>
                    <select name="bidang_id" style="width: 100%; height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                        <option value="">-- Sesuai Pembimbing --</option>
                        @foreach($bidangList as $b)
                            <option value="{{ $b->id }}">{{ $b->nama_bidang }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Tanggal Mulai & Selesai --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1.25rem;">
                <div>
                    <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Tanggal Mulai <span style="color: #DC2626;">*</span></label>
                    <input type="date" name="tgl_mulai" required value="{{ date('Y-m-d') }}" style="width: 100%; height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                </div>
                <div>
                    <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Tanggal Selesai <span style="color: #DC2626;">*</span></label>
                    <input type="date" name="tgl_selesai" required value="{{ date('Y-m-d', strtotime('+3 months')) }}" style="width: 100%; height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.6rem; border-top: 1px solid var(--neutral-200, #E2E8F0); padding-top: 1.25rem;">
                <button type="button" onclick="closeManualModal()" style="padding: 0.55rem 1.25rem; background: #ffffff; border: 1px solid var(--neutral-300, #CBD5E1); color: var(--neutral-700, #334155); border-radius: 8px; font-weight: 600; font-size: 0.875rem; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="padding: 0.55rem 1.4rem; background: #0641CC; color: #ffffff; border: none; border-radius: 8px; font-weight: 600; font-size: 0.875rem; cursor: pointer; box-shadow: 0 1px 3px rgba(6, 65, 204, 0.25);">
                    Tempatkan Peserta
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL PINDAH PEMBIMBING --}}
<div id="modal-pindah-pembimbing" class="modal-overlay">
    <div class="modal-container">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0); display: flex; justify-content: space-between; align-items: center; background: #F8FAFC;">
            <div>
                <h3 style="font-size: 1.0625rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0;">Mutasi / Ganti Pembimbing</h3>
                <p style="font-size: 0.75rem; color: var(--neutral-500, #64748B); margin: 0.15rem 0 0 0;" id="pindah-peserta-nama">
                    Peserta: -
                </p>
            </div>
            <button type="button" onclick="closePindahModal()" style="background: transparent; border: none; font-size: 1.25rem; color: var(--neutral-400, #94A3B8); cursor: pointer;">✕</button>
        </div>

        <form method="POST" id="form-pindah-pembimbing" action="" style="padding: 1.5rem;">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 0.375rem; margin-bottom: 1.25rem;">
                <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Pilih Pembimbing Baru <span style="color: #DC2626;">*</span></label>
                <select name="pembimbing_id" id="select-pembimbing-baru" required style="height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                    @foreach($pembimbingList as $p)
                        <option value="{{ $p->id }}">{{ $p->nama }} [{{ $p->bidang?->nama_bidang ?? 'Dinas' }}] (Beban: {{ $p->mentee_aktif_count ?? 0 }} Mentee Aktif)</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.6rem; border-top: 1px solid var(--neutral-200, #E2E8F0); padding-top: 1.25rem;">
                <button type="button" onclick="closePindahModal()" style="padding: 0.55rem 1.25rem; background: #ffffff; border: 1px solid var(--neutral-300, #CBD5E1); color: var(--neutral-700, #334155); border-radius: 8px; font-weight: 600; font-size: 0.875rem; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="padding: 0.55rem 1.4rem; background: #0641CC; color: #ffffff; border: none; border-radius: 8px; font-weight: 600; font-size: 0.875rem; cursor: pointer; box-shadow: 0 1px 3px rgba(6, 65, 204, 0.25);">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL BATALKAN PENEMPATAN --}}
<div id="modal-batal-penempatan" class="modal-overlay">
    <div class="modal-container">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0); display: flex; justify-content: space-between; align-items: center; background: #FEF2F2;">
            <div>
                <h3 style="font-size: 1.0625rem; font-weight: 800; color: #991B1B; margin: 0;">Pembatalan / Pengunduran Diri</h3>
                <p style="font-size: 0.75rem; color: #B91C1C; margin: 0.15rem 0 0 0;" id="batal-peserta-nama"></p>
            </div>
            <button type="button" onclick="closeBatalModal()" style="background: transparent; border: none; font-size: 1.25rem; color: #B91C1C; cursor: pointer;">✕</button>
        </div>

        <form method="POST" id="form-batal-penempatan" action="" style="padding: 1.5rem;">
            @csrf
            <div style="background: #FFFBEB; border: 1px solid #FCD34D; border-radius: 8px; padding: 0.85rem 1rem; margin-bottom: 1.25rem; font-size: 0.75rem; color: #92400E; line-height: 1.4;">
                <strong>Ketentuan Pembatalan & Pengunduran Diri (BR-07):</strong>
                <ul style="margin: 0.25rem 0 0 1rem; padding: 0;">
                    <li><strong>Sebelum tanggal mulai:</strong> Kuota jurusan/mitra akan otomatis dikembalikan ke sistem.</li>
                    <li><strong>Sejak/setelah tanggal mulai:</strong> Kuota hangus (tidak dikembalikan) dan akun peserta otomatis dinonaktifkan.</li>
                </ul>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.375rem; margin-bottom: 1.5rem;">
                <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Alasan Pembatalan / Pengunduran Diri <span style="color: #DC2626;">*</span></label>
                <textarea name="alasan" required rows="3" placeholder="Sebutkan alasan pembatalan atau pengunduran diri secara detail..." style="border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0.75rem; font-size: 0.875rem; font-family: inherit; color: var(--neutral-900, #0F172A);"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.6rem; border-top: 1px solid var(--neutral-200, #E2E8F0); padding-top: 1.25rem;">
                <button type="button" onclick="closeBatalModal()" style="padding: 0.55rem 1.25rem; background: #ffffff; border: 1px solid var(--neutral-300, #CBD5E1); color: var(--neutral-700, #334155); border-radius: 8px; font-weight: 600; font-size: 0.875rem; cursor: pointer;">
                    Kembali
                </button>
                <button type="submit" style="padding: 0.55rem 1.4rem; background: #DC2626; color: #FFFFFF; border: none; border-radius: 8px; font-weight: 600; font-size: 0.875rem; cursor: pointer;">
                    Konfirmasi Batalkan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL IN-APP PRATINJAU SURAT DITERIMA --}}
<div id="modal-surat-preview" onclick="if(event.target === this) closeSuratPreview()" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(6px); z-index: 9999; align-items: center; justify-content: center; padding: 1.25rem;">
    <div style="background: #ffffff; border-radius: 16px; width: 100%; max-width: 960px; max-height: 92vh; display: flex; flex-direction: column; padding: 1.25rem 1.5rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); overflow: hidden; border: 1px solid var(--neutral-200, #E2E8F0);">
        <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 1rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0);">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 38px; height: 38px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: #D1FAE5; color: #065F46;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                </div>
                <div>
                    <h3 id="surat-modal-title" style="font-size: 1rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0;">Surat Keterangan Diterima Magang</h3>
                    <p style="font-size: 0.75rem; color: var(--neutral-500, #64748B); margin: 2px 0 0 0;">Dokumen Resmi Dinas Kominfo Kabupaten Sleman</p>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <a id="surat-modal-download" href="#" style="display: inline-flex; align-items: center; gap: 0.375rem; background: #0641CC; color: #FFFFFF; padding: 0.45rem 0.95rem; border-radius: 8px; font-weight: 600; font-size: 0.8125rem; text-decoration: none; box-shadow: 0 1px 3px rgba(6, 65, 204, 0.2);">
                    Unduh PDF
                </a>
                <button type="button" onclick="closeSuratPreview()" style="background: none; border: none; font-size: 1.5rem; line-height: 1; cursor: pointer; color: var(--neutral-400, #94A3B8); padding: 0 0.25rem;">&times;</button>
            </div>
        </div>

        <div style="flex: 1; min-height: 60vh; max-height: 75vh; position: relative; background: #F8FAFC; border: 1px solid var(--neutral-200, #E2E8F0); border-radius: 10px; margin-top: 0.875rem; overflow: hidden;">
            <iframe id="surat-iframe" src="about:blank" style="width: 100%; height: 75vh; border: none;"></iframe>
        </div>
    </div>
</div>

{{-- MODAL PENETAPAN PEMBIMBING & BIDANG (TAHAP 2) --}}
<div id="modal-tetapkan-penempatan" class="modal-overlay">
    <div class="modal-container" style="max-width: 580px;">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0); display: flex; justify-content: space-between; align-items: center; background: #F8FAFC;">
            <div>
                <h3 style="font-size: 1.0625rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0;">Penetapan Penempatan Peserta</h3>
                <p style="font-size: 0.75rem; color: var(--neutral-500, #64748B); margin: 0.15rem 0 0 0;" id="tetapkan-peserta-info">
                    Peserta: -
                </p>
            </div>
            <button type="button" onclick="closeTetapkanModal()" style="background: transparent; border: none; font-size: 1.25rem; color: var(--neutral-400, #94A3B8); cursor: pointer;">✕</button>
        </div>

        <form method="POST" id="form-tetapkan-penempatan" action="{{ route('admin.penempatan.tetapkan') }}" style="padding: 1.5rem;">
            @csrf
            <input type="hidden" name="pendaftaran_id" id="tetapkan-pendaftaran-id" value="">

            <div style="background: #EFF6FF; border: 1px solid #DBEAFE; border-radius: 8px; padding: 0.85rem 1rem; margin-bottom: 1.25rem; font-size: 0.8125rem; color: #1E40AF; line-height: 1.45;">
                <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2" style="flex-shrink: 0; margin-top: 2px;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    <div>
                        <strong>Penetapan Tahap 2:</strong> Pilih pembimbing lapangan dan unit bidang dinas. Surat Keterangan Diterima resmi akan otomatis siap diunduh setelah penempatan disimpan.
                    </div>
                </div>
            </div>

            {{-- Bidang Dinas --}}
            <div style="display: flex; flex-direction: column; gap: 0.375rem; margin-bottom: 1.25rem;">
                <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Unit Bidang Dinas <span style="color: #DC2626;">*</span></label>
                <select name="bidang_id" id="select-tetapkan-bidang" required style="height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff;">
                    <option value="">-- Pilih Bidang Dinas --</option>
                    @foreach($bidangList as $b)
                        <option value="{{ $b->id }}">{{ $b->nama_bidang }}</option>
                    @endforeach
                </select>
                <small style="font-size: 0.725rem; color: var(--neutral-500, #64748B);">Otomatis direkomendasikan sesuai bidang keahlian jurusan pendaftar.</small>
            </div>

            {{-- Pembimbing Lapangan --}}
            <div style="display: flex; flex-direction: column; gap: 0.375rem; margin-bottom: 1.5rem;">
                <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Pembimbing Lapangan <span style="color: #DC2626;">*</span></label>
                <select name="pembimbing_id" id="select-tetapkan-pembimbing" required style="height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff;">
                    <option value="">-- Pilih Pembimbing Dinas --</option>
                    @foreach($pembimbingList as $p)
                        <option value="{{ $p->id }}" data-bidang-id="{{ $p->bidang_id ?? '' }}">
                            {{ $p->nama }} [{{ $p->bidang?->nama_bidang ?? 'Dinas Kominfo' }}] ({{ $p->mentee_aktif_count ?? 0 }} Mentee Aktif)
                        </option>
                    @endforeach
                </select>
                <small style="font-size: 0.725rem; color: var(--neutral-500, #64748B);">Daftar pembimbing lapangan dilengkapi dengan asal bidang dan beban mentee aktif saat ini.</small>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.6rem; border-top: 1px solid var(--neutral-200, #E2E8F0); padding-top: 1.25rem;">
                <button type="button" onclick="closeTetapkanModal()" style="padding: 0.55rem 1.25rem; background: #ffffff; border: 1px solid var(--neutral-300, #CBD5E1); color: var(--neutral-700, #334155); border-radius: 8px; font-weight: 600; font-size: 0.875rem; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="padding: 0.55rem 1.4rem; background: #0641CC; color: #ffffff; border: none; border-radius: 8px; font-weight: 600; font-size: 0.875rem; cursor: pointer; box-shadow: 0 1px 3px rgba(6, 65, 204, 0.25);">
                    Tetapkan &amp; Aktifkan Penempatan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openTetapkanModal(pendaftaranId, namaPeserta, namaJurusan, defaultBidangId, defaultBidangNama) {
        const modal = document.getElementById('modal-tetapkan-penempatan');
        const infoEl = document.getElementById('tetapkan-peserta-info');
        const idInput = document.getElementById('tetapkan-pendaftaran-id');
        const bidangSelect = document.getElementById('select-tetapkan-bidang');

        if (infoEl) infoEl.innerText = 'Peserta: ' + namaPeserta + ' (' + namaJurusan + ')';
        if (idInput) idInput.value = pendaftaranId;
        if (bidangSelect && defaultBidangId) bidangSelect.value = defaultBidangId;

        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    function closeTetapkanModal() {
        const modal = document.getElementById('modal-tetapkan-penempatan');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }
    function openManualModal() {
        const modal = document.getElementById('modal-penempatan-manual');
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }
    function closeManualModal() {
        const modal = document.getElementById('modal-penempatan-manual');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    function switchPesertaMode(mode) {
        const btnTerdaftar = document.getElementById('btn-mode-terdaftar');
        const btnBaru = document.getElementById('btn-mode-baru');
        const panelTerdaftar = document.getElementById('panel-peserta-terdaftar');
        const panelBaru = document.getElementById('panel-peserta-baru');
        const inputMode = document.getElementById('input-mode-peserta');
        const selectPeserta = document.getElementById('select-peserta-id');

        if (!inputMode) return;
        inputMode.value = mode;

        if (mode === 'baru') {
            if (btnBaru) {
                btnBaru.style.background = '#0641CC';
                btnBaru.style.color = '#FFFFFF';
            }
            if (btnTerdaftar) {
                btnTerdaftar.style.background = 'transparent';
                btnTerdaftar.style.color = 'var(--neutral-700, #334155)';
            }
            if (panelTerdaftar) panelTerdaftar.style.display = 'none';
            if (panelBaru) panelBaru.style.display = 'flex';
            if (selectPeserta) selectPeserta.removeAttribute('required');
        } else {
            if (btnTerdaftar) {
                btnTerdaftar.style.background = '#0641CC';
                btnTerdaftar.style.color = '#FFFFFF';
            }
            if (btnBaru) {
                btnBaru.style.background = 'transparent';
                btnBaru.style.color = 'var(--neutral-700, #334155)';
            }
            if (panelTerdaftar) panelTerdaftar.style.display = 'block';
            if (panelBaru) panelBaru.style.display = 'none';
            if (selectPeserta) selectPeserta.setAttribute('required', 'required');
        }
    }

    function openPindahModal(penempatanId, namaPeserta, pembimbingId) {
        const modal = document.getElementById('modal-pindah-pembimbing');
        const nameEl = document.getElementById('pindah-peserta-nama');
        const formEl = document.getElementById('form-pindah-pembimbing');
        const selectEl = document.getElementById('select-pembimbing-baru');

        if (nameEl) nameEl.innerText = 'Peserta: ' + namaPeserta;
        if (formEl) formEl.action = '/admin/penempatan/' + penempatanId + '/pindah-pembimbing';
        if (selectEl && pembimbingId) selectEl.value = pembimbingId;
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    function closePindahModal() {
        const modal = document.getElementById('modal-pindah-pembimbing');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    function openBatalModal(penempatanId, namaPeserta) {
        const modal = document.getElementById('modal-batal-penempatan');
        const nameEl = document.getElementById('batal-peserta-nama');
        const formEl = document.getElementById('form-batal-penempatan');

        if (nameEl) nameEl.innerText = 'Peserta: ' + namaPeserta;
        if (formEl) formEl.action = '/admin/penempatan/' + penempatanId + '/batalkan';
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    function closeBatalModal() {
        const modal = document.getElementById('modal-batal-penempatan');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    function openSuratPreview(previewUrl, downloadUrl, title) {
        const modal = document.getElementById('modal-surat-preview');
        const iframe = document.getElementById('surat-iframe');
        const titleEl = document.getElementById('surat-modal-title');
        const downloadEl = document.getElementById('surat-modal-download');

        if (titleEl) titleEl.innerText = title;
        if (downloadEl) downloadEl.href = downloadUrl;
        if (iframe) iframe.src = previewUrl;
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    function closeSuratPreview() {
        const modal = document.getElementById('modal-surat-preview');
        const iframe = document.getElementById('surat-iframe');

        if (iframe) iframe.src = 'about:blank';
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeSuratPreview();
            closeManualModal();
            closePindahModal();
            closeBatalModal();
        }
    });

    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) {
                closeManualModal();
                closePindahModal();
                closeBatalModal();
                closeBulkReassignModal();
            }
        });
    });

    function toggleAllPenempatan(masterCheckbox) {
        const checkboxes = document.querySelectorAll('.penempatan-checkbox');
        checkboxes.forEach(cb => cb.checked = masterCheckbox.checked);
        updateBulkUI();
    }

    function updateBulkUI() {
        const checkedBoxes = document.querySelectorAll('.penempatan-checkbox:checked');
        const count = checkedBoxes.length;
        const floatingBar = document.getElementById('floating-bulk-bar');
        const countBadge = document.getElementById('bulk-selected-count');

        if (countBadge) countBadge.innerText = count;

        if (floatingBar) {
            if (count > 0) {
                floatingBar.style.display = 'flex';
            } else {
                floatingBar.style.display = 'none';
            }
        }
    }

    function openBulkReassignModal() {
        const checkedBoxes = document.querySelectorAll('.penempatan-checkbox:checked');
        if (checkedBoxes.length === 0) {
            const msg = 'Pilih minimal satu penempatan peserta terlebih dahulu.';
            if (window.MagKomToast) {
                window.MagKomToast.warning(msg);
            } else {
                alert(msg);
            }
            return;
        }

        const container = document.getElementById('bulk-ids-container');
        if (container) {
            container.innerHTML = '';
            checkedBoxes.forEach(cb => {
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'penempatan_ids[]';
                hidden.value = cb.value;
                container.appendChild(hidden);
            });
        }

        const countEl = document.getElementById('bulk-modal-count');
        if (countEl) countEl.innerText = checkedBoxes.length;

        const modal = document.getElementById('modal-bulk-reassign');
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    function closeBulkReassignModal() {
        const modal = document.getElementById('modal-bulk-reassign');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }
</script>

{{-- FLOATING BULK ACTION BAR --}}
<div id="floating-bulk-bar" style="display: none; position: fixed; bottom: 2rem; left: 50%; transform: translateX(-50%); background: #0F172A; color: #ffffff; padding: 0.85rem 1.5rem; border-radius: 9999px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3); z-index: 1040; align-items: center; gap: 1.25rem;">
    <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem;">
        <span id="bulk-selected-count" style="background: #0641CC; color: #ffffff; padding: 0.15rem 0.55rem; border-radius: 999px; font-weight: 700; font-size: 0.8125rem;">0</span>
        <span>Peserta Dipilih</span>
    </div>
    <div style="display: flex; align-items: center; gap: 0.5rem;">
        <button type="button" onclick="openBulkReassignModal()" style="padding: 0.45rem 1rem; background: #0641CC; color: #ffffff; border: none; border-radius: 999px; font-weight: 700; font-size: 0.8125rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><polyline points="16 11 18 13 22 9"></polyline></svg>
            <span>Pindahkan Pembimbing Massal</span>
        </button>
        <button type="button" onclick="toggleAllPenempatan({checked: false})" style="background: transparent; border: none; color: #94A3B8; font-size: 0.8125rem; cursor: pointer; padding: 0.25rem 0.5rem;">
            Batal
        </button>
    </div>
</div>

{{-- MODAL BULK REASSIGN PEMBIMBING --}}
<div id="modal-bulk-reassign" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(6px); z-index: 1060; align-items: center; justify-content: center; padding: 1rem;">
    <div style="background: #ffffff; border-radius: 14px; width: 100%; max-width: 520px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); padding: 1.75rem; border: 1px solid var(--neutral-200);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.85rem;">
            <div>
                <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-900); margin: 0;">Pindahkan Pembimbing Massal</h3>
                <p style="font-size: 0.8rem; color: var(--neutral-500); margin: 0.2rem 0 0 0;">
                    Alihkan <span id="bulk-modal-count" style="font-weight: 700; color: #0641CC;">0</span> penempatan peserta ke pembimbing baru.
                </p>
            </div>
            <button type="button" onclick="closeBulkReassignModal()" style="background: transparent; border: none; cursor: pointer; color: var(--neutral-500); font-size: 1.25rem;">&times;</button>
        </div>

        <form method="POST" action="{{ route('admin.penempatan.bulk-reassign') }}">
            @csrf
            <div id="bulk-ids-container"></div>

            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.4rem;">
                    Pilih Pembimbing Lapangan Baru <span style="color: #ef4444;">*</span>
                </label>
                <select name="pembimbing_id" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-300); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff;">
                    <option value="">-- Pilih Pembimbing Aktif --</option>
                    @foreach($pembimbingList as $pem)
                        <option value="{{ $pem->id }}">
                            {{ $pem->nama }} (Beban: {{ $pem->mentee_aktif_count }} mentee aktif)
                        </option>
                    @endforeach
                </select>
                <small style="display: block; font-size: 0.75rem; color: var(--neutral-500); margin-top: 0.35rem;">
                    Sistem akan memvalidasi batas kapasitas <code>maks_peserta_aktif_per_pembimbing</code> (BR-13).
                </small>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--neutral-200); padding-top: 1rem;">
                <button type="button" onclick="closeBulkReassignModal()" style="padding: 0.5rem 1.25rem; background: var(--neutral-200); color: var(--neutral-700); font-weight: 600; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="padding: 0.5rem 1.5rem; background: #0641CC; color: #ffffff; font-weight: 600; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer;">
                    Konfirmasi Pindah Pembimbing
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
