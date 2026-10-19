@extends('layouts.dashboard')

@section('title', 'Hub Penerbitan Sertifikat Kelulusan - MagKom Sleman')

@section('content')
<div class="dashboard-content" style="display: flex; flex-direction: column; gap: 1.5rem; max-width: 1400px; margin: 0 auto; width: 100%;">

    <style>
        .cert-header-bar {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 1rem;
            border-bottom: 1px solid var(--neutral-200, #E2E8F0);
            padding-bottom: 1rem;
        }
        .cert-breadcrumb a {
            color: var(--neutral-600, #475569);
            text-decoration: none;
        }
        .cert-page-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--neutral-900, #0F172A);
            margin: 0;
            letter-spacing: -0.02em;
        }
        .cert-page-subtitle {
            font-size: 0.85rem;
            color: var(--neutral-600, #475569);
            margin: 0.3rem 0 0 0;
        }
        .cert-btn-back {
            height: 38px;
            padding: 0 1rem;
            background: #ffffff;
            color: var(--neutral-700, #334155);
            border: 1px solid var(--neutral-300, #CBD5E1);
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.15s ease;
        }
        .cert-btn-back:hover {
            border-color: #0641CC;
            color: #0641CC;
            background: #F8FAFC;
        }
        .cert-stat-card {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid var(--neutral-200, #E2E8F0);
            padding: 1.25rem 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: transform 0.15s ease;
        }
        .cert-stat-label {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--neutral-600, #475569);
            display: block;
            margin-bottom: 0.25rem;
        }
        .cert-stat-hint {
            font-size: 0.75rem;
            color: var(--neutral-500, #64748B);
            display: block;
            margin-top: 0.25rem;
        }
        .cert-filter-card {
            background: #ffffff;
            border: 1px solid var(--neutral-200, #E2E8F0);
            border-radius: 14px;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }
        .cert-input-search {
            width: 100%;
            height: 38px;
            border: 1px solid var(--neutral-300, #CBD5E1);
            border-radius: 8px;
            padding: 0 0.85rem;
            font-size: 0.85rem;
            box-sizing: border-box;
            background: #ffffff;
            color: #0F172A;
            outline: none;
            transition: all 0.15s ease;
        }
        .cert-input-search:focus {
            border-color: #0641CC;
            box-shadow: 0 0 0 3px rgba(6, 65, 204, 0.1);
        }
        .cert-select-bidang {
            width: 100%;
            height: 38px;
            border: 1px solid var(--neutral-300, #CBD5E1);
            border-radius: 8px;
            padding: 0 0.85rem;
            font-size: 0.85rem;
            color: var(--neutral-800, #1E293B);
            background: #ffffff;
            outline: none;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .cert-select-bidang:focus {
            border-color: #0641CC;
            box-shadow: 0 0 0 3px rgba(6, 65, 204, 0.1);
        }
        .cert-table-card {
            background: #ffffff;
            border: 1px solid var(--neutral-200, #E2E8F0);
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .cert-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.875rem;
        }
        .cert-table th {
            background: #F8FAFC;
            padding: 0.85rem 1.25rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--neutral-600, #475569);
            border-bottom: 1px solid var(--neutral-200, #E2E8F0);
        }
        .cert-table td {
            padding: 0.95rem 1.25rem;
            border-bottom: 1px solid var(--neutral-200, #E2E8F0);
            vertical-align: middle;
            color: var(--neutral-800, #1E293B);
        }
        .cert-table tr:hover {
            background: #F8FAFC;
        }
        .cert-btn-preview {
            padding: 0.35rem 0.75rem;
            border: 1px solid var(--neutral-300, #CBD5E1);
            background: #ffffff;
            color: var(--neutral-700, #334155);
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            text-decoration: none;
            margin-right: 0.35rem;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            transition: all 0.15s ease;
        }
        .cert-btn-preview:hover {
            border-color: #0641CC;
            color: #0641CC;
            background: #EFF6FF;
        }

        /* =====================================================================
           DARK MODE OVERRIDES (DS-MagKom Standards)
           ===================================================================== */
        [data-theme="dark"] .cert-header-bar {
            border-bottom-color: #26354D !important;
        }
        [data-theme="dark"] .cert-breadcrumb a {
            color: #94A3B8 !important;
        }
        [data-theme="dark"] .cert-breadcrumb span {
            color: #F8FAFC !important;
        }
        [data-theme="dark"] .cert-page-title {
            color: #F8FAFC !important;
        }
        [data-theme="dark"] .cert-page-subtitle {
            color: #94A3B8 !important;
        }
        [data-theme="dark"] .cert-btn-back {
            background: #111C2E !important;
            border-color: #26354D !important;
            color: #CBD5E1 !important;
        }
        [data-theme="dark"] .cert-btn-back:hover {
            background: #162238 !important;
            border-color: #3B82F6 !important;
            color: #60A5FA !important;
        }
        [data-theme="dark"] .cert-stat-card {
            background: #182234 !important;
            border-color: #26354D !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25) !important;
        }
        [data-theme="dark"] .cert-stat-label {
            color: #94A3B8 !important;
        }
        [data-theme="dark"] .cert-stat-card .cert-stat-val-dark {
            color: #F8FAFC !important;
        }
        [data-theme="dark"] .cert-stat-card .cert-stat-hint-dark {
            color: #34D399 !important;
        }
        [data-theme="dark"] .cert-filter-card {
            background: #182234 !important;
            border-color: #26354D !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25) !important;
        }
        [data-theme="dark"] .cert-input-search {
            background: #0F172A !important;
            border-color: #26354D !important;
            color: #F8FAFC !important;
        }
        [data-theme="dark"] .cert-input-search:focus {
            border-color: #3B82F6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2) !important;
        }
        [data-theme="dark"] .cert-select-bidang {
            background: #0F172A !important;
            border-color: #26354D !important;
            color: #F8FAFC !important;
        }
        [data-theme="dark"] .cert-select-bidang:focus {
            border-color: #3B82F6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2) !important;
        }
        [data-theme="dark"] .cert-select-bidang option {
            background: #0F172A !important;
            color: #F8FAFC !important;
        }
        [data-theme="dark"] .cert-table-card {
            background: #182234 !important;
            border-color: #26354D !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25) !important;
        }
        [data-theme="dark"] .cert-table th {
            background: #0E1726 !important;
            color: #94A3B8 !important;
            border-bottom-color: #26354D !important;
        }
        [data-theme="dark"] .cert-table td {
            background: #182234 !important;
            color: #CBD5E1 !important;
            border-bottom-color: #26354D !important;
        }
        [data-theme="dark"] .cert-table tr:hover td {
            background: #162238 !important;
        }
        [data-theme="dark"] .cert-table td .cert-alumni-name {
            color: #F8FAFC !important;
        }
        [data-theme="dark"] .cert-table td .cert-nomor-seri {
            color: #93C5FD !important;
        }
        [data-theme="dark"] .cert-table td .cert-bidang-chip {
            background: #111C2E !important;
            border: 1px solid #26354D !important;
            color: #CBD5E1 !important;
        }
        [data-theme="dark"] .cert-table td .cert-nilai-akhir {
            color: #93C5FD !important;
        }
        [data-theme="dark"] .cert-table td .cert-predikat {
            color: #34D399 !important;
        }
        [data-theme="dark"] .cert-table td .cert-meta-sub {
            color: #94A3B8 !important;
        }
        [data-theme="dark"] .cert-btn-preview {
            background: #111C2E !important;
            border-color: #26354D !important;
            color: #CBD5E1 !important;
        }
        [data-theme="dark"] .cert-btn-preview:hover {
            background: #162238 !important;
            border-color: #3B82F6 !important;
            color: #60A5FA !important;
        }
        [data-theme="dark"] .cert-empty-box {
            color: #94A3B8 !important;
        }
        [data-theme="dark"] .cert-empty-box h3 {
            color: #F8FAFC !important;
        }
        [data-theme="dark"] .cert-empty-box .cert-empty-icon {
            background: rgba(37, 99, 235, 0.2) !important;
            color: #93C5FD !important;
        }
        [data-theme="dark"] .cert-pagination-wrap {
            background: #141E30 !important;
            border-top-color: #26354D !important;
        }
    </style>

    {{-- 1. HEADER & BREADCRUMB --}}
    <div class="cert-header-bar">
        <div>
            <nav class="cert-breadcrumb" style="font-size: 0.8rem; margin-bottom: 0.35rem; display: flex; align-items: center; gap: 0.4rem;">
                <a href="{{ route('admin.dashboard') }}">Dashboard Admin</a>
                <span style="color: #64748B;">/</span>
                <span style="font-weight: 600;">Sertifikat Kelulusan</span>
            </nav>
            <div style="display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
                <h1 class="cert-page-title">
                    Hub Sertifikat Kelulusan Resmi
                </h1>
                <span style="display: inline-flex; align-items: center; padding: 0.2rem 0.65rem; border-radius: 9999px; font-size: 0.72rem; font-weight: 700; background: #DBEAFE; color: #1E40AF; text-transform: uppercase; letter-spacing: 0.04em;">
                    AC-12 & BR-10
                </span>
            </div>
            <p class="cert-page-subtitle">
                Penerbitan dan pengarsipan Sertifikat Kelulusan Magang bagi alumni dengan evaluasi nilai yang telah berstatus final.
            </p>
        </div>

        <div>
            <a href="{{ route('admin.dashboard') }}" class="cert-btn-back">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
                <span>Kembali ke Dashboard</span>
            </a>
        </div>
    </div>

    {{-- 2. METRIK RINGKASAN --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;">
        <div class="cert-stat-card" style="border-left: 4px solid #0641CC;">
            <div>
                <span class="cert-stat-label">Siap Terbit Sertifikat</span>
                <span style="font-size: 1.85rem; font-weight: 800; color: #0641CC;">{{ number_format($totalBersertifikat) }}</span>
                <span class="cert-stat-hint">Nilai telah difinalisasi</span>
            </div>
            <div style="width: 46px; height: 46px; border-radius: 10px; background: #EFF6FF; color: #0641CC; display: flex; align-items: center; justify-content: center;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
            </div>
        </div>

        <div class="cert-stat-card" style="border-left: 4px solid #10B981;">
            <div>
                <span class="cert-stat-label">Total Alumni Selesai Magang</span>
                <span class="cert-stat-val-dark" style="font-size: 1.85rem; font-weight: 800; color: var(--neutral-900, #0F172A);">{{ number_format($totalSelesai) }}</span>
                <span class="cert-stat-hint-dark" style="font-size: 0.75rem; color: #166534; display: block; margin-top: 0.25rem;">Status Penempatan Selesai</span>
            </div>
            <div style="width: 46px; height: 46px; border-radius: 10px; background: #DCFCE7; color: #16A34A; display: flex; align-items: center; justify-content: center;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            </div>
        </div>
    </div>

    {{-- 3. FILTER & PENCARIAN --}}
    <div class="cert-filter-card">
        <form method="GET" action="{{ route('admin.sertifikat.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
            <div style="flex: 1; min-width: 260px;">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Cari nama alumni, NIM/NISN, atau institusi..." 
                       class="cert-input-search">
            </div>

            <div style="min-width: 200px;">
                <select name="bidang_id" class="cert-select-bidang">
                    <option value="">-- Semua Bidang Penempatan --</option>
                    @foreach($daftarBidang as $b)
                        <option value="{{ $b->id }}" {{ request('bidang_id') == $b->id ? 'selected' : '' }}>
                            {{ $b->nama_bidang }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" style="height: 38px; padding: 0 1.25rem; background: #0641CC; color: #ffffff; border: none; border-radius: 8px; font-size: 0.85rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem; transition: background 0.15s ease;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <span>Filter</span>
            </button>

            @if(request()->hasAny(['search', 'bidang_id']))
                <a href="{{ route('admin.sertifikat.index') }}" style="font-size: 0.825rem; color: #64748B; text-decoration: underline;">
                    Reset Filter
                </a>
            @endif
        </form>
    </div>

    {{-- 4. TABEL DAFTAR ALUMNI BERSERTIFIKAT --}}
    <div class="cert-table-card">
        @if($alumniList->isEmpty())
            <div class="cert-empty-box" style="padding: 3.5rem 1.5rem; text-align: center; color: var(--neutral-600, #475569);">
                <div class="cert-empty-icon" style="width: 48px; height: 48px; border-radius: 50%; background: #EFF6FF; color: #0641CC; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem auto;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                </div>
                <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-900, #0F172A); margin: 0 0 0.25rem 0;">Belum Ada Alumni Siap Terbit</h3>
                <p style="font-size: 0.825rem; margin: 0;">Sertifikat kelulusan hanya muncul apabila penempatan berstatus Selesai dan pembimbing telah memfinalisasi evaluasi nilai.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table class="cert-table">
                    <thead>
                        <tr>
                            <th>No. Seri Sertifikat</th>
                            <th>Nama Alumni</th>
                            <th>Institusi & Jurusan</th>
                            <th>Bidang Penempatan</th>
                            <th>Bulan Magang</th>
                            <th style="text-align: center;">Nilai & Predikat</th>
                            <th style="text-align: right;">Aksi Penerbitan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($alumniList as $penempatan)
                            @php
                                $nomorSeri = '500.12/SRT/' . str_pad((string) $penempatan->id, 4, '0', STR_PAD_LEFT) . '/DISKOMINFO/' . ($penempatan->tgl_selesai?->format('Y') ?? date('Y'));
                            @endphp
                            <tr>
                                <td class="cert-nomor-seri" style="font-weight: 700; color: #0641CC; font-size: 0.8rem; font-family: monospace;">
                                    {{ $nomorSeri }}
                                </td>
                                <td>
                                    <div class="cert-alumni-name" style="font-weight: 700; color: var(--neutral-900, #0F172A);">{{ $penempatan->peserta?->nama }}</div>
                                    <small class="cert-meta-sub" style="color: var(--neutral-500, #64748B);">{{ $penempatan->peserta?->email }}</small>
                                </td>
                                <td>
                                    <div style="font-weight: 600;">{{ $penempatan->peserta?->profil?->institusi ?? '-' }}</div>
                                    <small class="cert-meta-sub" style="color: var(--neutral-500, #64748B);">{{ $penempatan->peserta?->profil?->jurusan ?? '-' }}</small>
                                </td>
                                <td>
                                    <span class="cert-bidang-chip" style="display: inline-flex; align-items: center; padding: 0.2rem 0.55rem; border-radius: 6px; font-size: 0.75rem; font-weight: 600; background: #F1F5F9; color: var(--neutral-700, #334155);">
                                        {{ $penempatan->bidang?->nama_bidang ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="cert-meta-sub" style="font-size: 0.8rem; color: var(--neutral-700, #334155); font-weight: 600;">
                                        {{ $penempatan->tgl_mulai?->format('d/m/Y') }} &ndash; {{ $penempatan->tgl_selesai?->format('d/m/Y') }}
                                    </div>
                                    <small class="cert-meta-sub" style="color: var(--neutral-500, #64748B);">Pembimbing: {{ $penempatan->pembimbing?->nama ?? '-' }}</small>
                                </td>
                                <td style="text-align: center;">
                                    <span class="cert-nilai-akhir" style="font-size: 0.95rem; font-weight: 800; color: #0641CC;">
                                        {{ number_format((float) $penempatan->penilaian?->nilai_akhir, 2) }}
                                    </span>
                                    <div class="cert-predikat" style="font-size: 0.72rem; font-weight: 700; color: #166534;">
                                        Predikat {{ $penempatan->penilaian?->predikat ?? '-' }}
                                    </div>
                                </td>
                                <td style="text-align: right; white-space: nowrap;">
                                    <a href="{{ route('admin.sertifikat.cetak', ['penempatan' => $penempatan->id, 'preview' => 1]) }}" 
                                       target="_blank" 
                                       class="cert-btn-preview">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                        <span>Pratinjau</span>
                                    </a>
                                    <a href="{{ route('admin.sertifikat.cetak', $penempatan->id) }}" 
                                       style="padding: 0.35rem 0.85rem; background: #0641CC; color: #ffffff; border-radius: 6px; font-size: 0.75rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 0.3rem; transition: background 0.15s ease;">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                        <span>Unduh PDF</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($alumniList->hasPages())
                <div class="cert-pagination-wrap" style="padding: 1rem 1.5rem; border-top: 1px solid var(--neutral-200, #E2E8F0);">
                    {{ $alumniList->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
