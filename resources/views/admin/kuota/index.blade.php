@extends('layouts.dashboard')

@section('title', 'Manajemen Kuota - Admin MagKom Sleman')
@section('page_title', 'Manajemen Kuota Magang Per Jurusan')
@section('page_description', 'Kelola alokasi jumlah kursi kuota umum magang per kejuruan pada setiap periode bulan.')

@section('breadcrumbs')
    <nav style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; color: var(--neutral-600, #64748B);">
        <span>Portal MagKom</span>
        <span>/</span>
        <span>Administrasi</span>
        <span>/</span>
        <span style="color: var(--neutral-900, #0F172A); font-weight: 700;">Manajemen Kuota Jurusan</span>
    </nav>
@endsection

@section('page_actions')
    @if($selectedBulan)
        <button type="button" onclick="openTambahKuotaModal()" style="display: inline-flex; align-items: center; gap: 0.5rem; background: #0641CC; color: #ffffff; border: none; padding: 0.6rem 1.25rem; border-radius: 8px; font-weight: 600; font-size: 0.875rem; cursor: pointer; box-shadow: 0 1px 3px rgba(6, 65, 204, 0.25); transition: all 0.15s ease;" onmouseover="this.style.background='#0533A3'" onmouseout="this.style.background='#0641CC'">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span>Alokasikan Kuota Jurusan</span>
        </button>
    @endif
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
            max-width: 500px;
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
    </style>

    {{-- 1. PERIODE SELECTOR BAR --}}
    <div class="admin-card" style="padding: 1.25rem 1.5rem; background: #ffffff;">
        <form method="GET" action="{{ route('admin.kuota.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end; justify-content: space-between;">
            <div style="display: flex; flex-direction: column; gap: 0.375rem; flex: 1; min-width: 280px; max-width: 600px;">
                <label for="bulan-select" style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B); display: flex; align-items: center; gap: 0.4rem;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    Pilih Periode Bulan Magang
                </label>
                <select id="bulan-select" name="bulan_magang_id" onchange="this.form.submit()" style="height: 44px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.85rem; font-size: 0.875rem; background: #ffffff; color: var(--neutral-900, #0F172A); font-weight: 600;">
                    @foreach($bulanList as $b)
                        <option value="{{ $b->id }}" {{ $selectedBulan && $selectedBulan->id === $b->id ? 'selected' : '' }}>
                            {{ $b->bulan_mulai?->translatedFormat('F Y') }} (Pendaftaran: {{ \Carbon\Carbon::parse($b->tgl_buka)->translatedFormat('d M') }} – {{ \Carbon\Carbon::parse($b->tgl_tutup)->translatedFormat('d M Y') }})
                        </option>
                    @endforeach
                </select>
            </div>

            @if($selectedBulan)
                <div style="display: flex; align-items: center; gap: 0.5rem; background: #EFF6FF; border: 1px solid #DBEAFE; border-radius: 8px; padding: 0.6rem 1rem;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1D4ED8" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                    <span style="font-size: 0.8125rem; color: #1E40AF; font-weight: 600;">
                        Jadwal Pendaftaran: {{ \Carbon\Carbon::parse($selectedBulan->tgl_buka)->translatedFormat('d F Y') }} s.d. {{ \Carbon\Carbon::parse($selectedBulan->tgl_tutup)->translatedFormat('d F Y') }}
                    </span>
                </div>
            @endif
        </form>
    </div>

    @php
        $totalKapasitas = $selectedBulan ? $selectedBulan->kuota->sum('jumlah') : 0;
        $totalTerisi = $selectedBulan ? $selectedBulan->kuota->sum('terisi') : 0;
        $sisaKursi = max(0, $totalKapasitas - $totalTerisi);
        $globalPersen = $totalKapasitas > 0 ? min(100, round(($totalTerisi / $totalKapasitas) * 100)) : 0;
    @endphp

    {{-- 2. HERO PERIOD KPI STATS CARDS --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
        {{-- Total Kapasitas Kuota --}}
        <div class="admin-stat-card">
            <div>
                <span style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-500, #64748B);">Total Kapasitas Kursi</span>
                <div style="font-size: 1.75rem; font-weight: 800; color: #0F172A; margin-top: 0.25rem; letter-spacing: -0.02em;">
                    {{ number_format($totalKapasitas) }} <span style="font-size: 0.875rem; font-weight: 600; color: var(--neutral-500, #64748B);">Kursi</span>
                </div>
                <span style="font-size: 0.75rem; color: var(--neutral-500, #64748B); display: inline-flex; align-items: center; gap: 0.3rem; margin-top: 0.35rem;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    Alokasi seluruh jurusan
                </span>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #EFF6FF; color: #1D4ED8; display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
            </div>
        </div>

        {{-- Kursi Terisi --}}
        <div class="admin-stat-card">
            <div>
                <span style="font-size: 0.8125rem; font-weight: 600; color: #B45309;">Kursi Terisi</span>
                <div style="font-size: 1.75rem; font-weight: 800; color: #B45309; margin-top: 0.25rem; letter-spacing: -0.02em;">
                    {{ number_format($totalTerisi) }} <span style="font-size: 0.875rem; font-weight: 600; color: #D97706;">Kursi</span>
                </div>
                <span style="font-size: 0.75rem; color: #D97706; display: inline-flex; align-items: center; gap: 0.3rem; margin-top: 0.35rem;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                    Peserta magang diterima
                </span>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </div>
        </div>

        {{-- Sisa Kursi Tersedia --}}
        <div class="admin-stat-card">
            <div>
                <span style="font-size: 0.8125rem; font-weight: 600; color: {{ $sisaKursi > 0 ? '#047857' : '#B91C1C' }};">Sisa Kursi Tersedia</span>
                <div style="font-size: 1.75rem; font-weight: 800; color: {{ $sisaKursi > 0 ? '#059669' : '#DC2626' }}; margin-top: 0.25rem; letter-spacing: -0.02em;">
                    {{ number_format($sisaKursi) }} <span style="font-size: 0.875rem; font-weight: 600; color: {{ $sisaKursi > 0 ? '#059669' : '#DC2626' }};">Kursi</span>
                </div>
                <span style="font-size: 0.75rem; color: var(--neutral-500, #64748B); display: inline-flex; align-items: center; gap: 0.3rem; margin-top: 0.35rem;">
                    @if($sisaKursi > 0)
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>
                        <span style="color: #059669;">Tersedia untuk pendaftar</span>
                    @else
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                        <span style="color: #DC2626;">Kuota bulan ini penuh</span>
                    @endif
                </span>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 12px; background: {{ $sisaKursi > 0 ? '#D1FAE5' : '#FEE2E2' }}; color: {{ $sisaKursi > 0 ? '#059669' : '#DC2626' }}; display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </div>
        </div>

        {{-- Okupansi Global --}}
        <div class="admin-stat-card">
            <div>
                <span style="font-size: 0.8125rem; font-weight: 600; color: #1E40AF;">Okupansi Global Periode</span>
                <div style="font-size: 1.75rem; font-weight: 800; color: #1E40AF; margin-top: 0.25rem; letter-spacing: -0.02em;">
                    {{ $globalPersen }}%
                </div>
                <div style="margin-top: 0.4rem; width: 140px; height: 6px; background: var(--neutral-200, #E2E8F0); border-radius: 9999px; overflow: hidden;">
                    <div style="width: {{ $globalPersen }}%; height: 100%; background: {{ $globalPersen >= 100 ? '#EF4444' : ($globalPersen >= 75 ? '#F59E0B' : '#0641CC') }}; border-radius: 9999px;"></div>
                </div>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #DBEAFE; color: #1E40AF; display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path><path d="M22 12A10 10 0 0 0 12 2v10z"></path></svg>
            </div>
        </div>
    </div>

    {{-- 3. DATA TABLE CARD --}}
    <div class="admin-card">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem; background: #ffffff;">
            <div>
                <h3 style="font-size: 1.0625rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0; letter-spacing: -0.01em;">
                    Alokasi Kuota Jurusan: {{ $selectedBulan ? $selectedBulan->bulan_mulai?->translatedFormat('F Y') : '-' }}
                </h3>
                <p style="font-size: 0.8125rem; color: var(--neutral-500, #64748B); margin: 0.2rem 0 0 0;">
                    Distribusi kuota kursi magang per jurusan pada periode pendaftaran ini
                </p>
            </div>
            <span style="font-size: 0.8125rem; font-weight: 700; color: #1D4ED8; background: #EFF6FF; padding: 0.35rem 0.8rem; border-radius: 9999px; border: 1px solid #DBEAFE;">
                {{ $selectedBulan ? $selectedBulan->kuota->count() : 0 }} Jurusan Terdaftar
            </span>
        </div>

        @if(!$selectedBulan || $selectedBulan->kuota->isEmpty())
            <div style="padding: 4rem 1.5rem; text-align: center; color: var(--neutral-600, #64748B);">
                <div style="width: 64px; height: 64px; background: #EFF6FF; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem auto; color: #1D4ED8;">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
                <h4 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-900, #0F172A); margin-bottom: 0.375rem;">
                    Belum Ada Alokasi Kuota Terdaftar
                </h4>
                <p style="font-size: 0.875rem; max-width: 480px; margin: 0 auto 1.5rem auto; line-height: 1.5;">
                    Belum ada alokasi kuota jurusan untuk periode bulan ini. Klik tombol di bawah untuk mulai mengalokasikan kuota kursi.
                </p>
                @if($selectedBulan)
                    <button type="button" onclick="openTambahKuotaModal()" style="display: inline-flex; align-items: center; gap: 0.5rem; background: #0641CC; color: #ffffff; border: none; padding: 0.55rem 1.25rem; border-radius: 8px; font-weight: 600; font-size: 0.875rem; cursor: pointer; box-shadow: 0 1px 3px rgba(6, 65, 204, 0.25);">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>Alokasikan Kuota Jurusan Sekarang</span>
                    </button>
                @endif
            </div>
        @else
            <div style="overflow-x: auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nama Jurusan</th>
                            <th>Kategori Jenjang</th>
                            <th style="text-align: center; width: 120px;">Total Kuota</th>
                            <th style="text-align: center; width: 180px;">Terisi & Okupansi</th>
                            <th style="text-align: center; width: 120px;">Sisa Kuota</th>
                            <th>Status Alokasi</th>
                            <th style="text-align: right; width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($selectedBulan->kuota as $k)
                            @php
                                $sisa = max(0, $k->jumlah - $k->terisi);
                                $persen = $k->jumlah > 0 ? min(100, round(($k->terisi / $k->jumlah) * 100)) : 0;
                                $barColor = $persen >= 100 ? '#EF4444' : ($persen >= 75 ? '#F59E0B' : '#0641CC');
                            @endphp
                            <tr>
                                {{-- Nama Jurusan --}}
                                <td>
                                    <div style="font-weight: 700; color: var(--neutral-900, #0F172A); font-size: 0.9375rem;">
                                        {{ $k->jurusan?->nama_jurusan ?? 'Semua Jurusan' }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--neutral-500, #64748B); margin-top: 0.15rem;">
                                        Kode ID Kuota: #KT-{{ str_pad((string)$k->id, 4, '0', STR_PAD_LEFT) }}
                                    </div>
                                </td>

                                {{-- Kategori Jenjang --}}
                                <td>
                                    <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; padding: 0.25rem 0.6rem; border-radius: 6px; background: #EFF6FF; color: #1D4ED8; border: 1px solid #DBEAFE;">
                                        {{ $k->jurusan?->kategori === 'smk' ? 'SMK' : 'Mahasiswa (D3/S1)' }}
                                    </span>
                                </td>

                                {{-- Total Kuota --}}
                                <td style="text-align: center; font-weight: 800; color: var(--neutral-900, #0F172A); font-size: 1rem;">
                                    {{ $k->jumlah }}
                                </td>

                                {{-- Terisi & Okupansi --}}
                                <td style="text-align: center;">
                                    <div style="display: flex; align-items: baseline; justify-content: center; gap: 0.25rem;">
                                        <span style="font-weight: 800; color: {{ $persen >= 100 ? '#DC2626' : '#B45309' }}; font-size: 0.9375rem;">
                                            {{ $k->terisi }}
                                        </span>
                                        <span style="font-size: 0.75rem; color: var(--neutral-500, #64748B);">/ {{ $k->jumlah }}</span>
                                    </div>

                                    <div style="margin-top: 0.35rem; width: 100%; max-width: 120px; height: 6px; background: var(--neutral-200, #E2E8F0); border-radius: 9999px; overflow: hidden; margin-left: auto; margin-right: auto;" title="Okupansi: {{ $persen }}%">
                                        <div style="width: {{ $persen }}%; height: 100%; background: {{ $barColor }}; border-radius: 9999px;"></div>
                                    </div>
                                    <span style="font-size: 0.72rem; font-weight: 700; color: var(--neutral-500, #64748B); display: inline-block; margin-top: 0.2rem;">
                                        Okupansi: {{ $persen }}%
                                    </span>
                                </td>

                                {{-- Sisa Kuota --}}
                                <td style="text-align: center;">
                                    <span style="font-weight: 800; font-size: 1rem; color: {{ $sisa > 0 ? '#059669' : '#DC2626' }};">
                                        {{ $sisa }}
                                    </span>
                                    <div style="font-size: 0.72rem; color: {{ $sisa > 0 ? '#059669' : '#DC2626' }}; font-weight: 600;">
                                        {{ $sisa > 0 ? 'Tersedia' : 'Penuh' }}
                                    </div>
                                </td>

                                {{-- Status Alokasi --}}
                                <td>
                                    <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.25rem 0.65rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: {{ $k->aktif ? '#D1FAE5' : '#F1F5F9' }}; color: {{ $k->aktif ? '#065F46' : '#475569' }}; border: 1px solid {{ $k->aktif ? '#A7F3D0' : '#CBD5E1' }};">
                                        <span style="width: 6px; height: 6px; border-radius: 50%; background: {{ $k->aktif ? '#10B981' : '#94A3B8' }};"></span>
                                        <span>{{ $k->aktif ? 'Aktif' : 'Nonaktif' }}</span>
                                    </span>
                                </td>

                                {{-- Aksi --}}
                                <td style="text-align: right;">
                                    <div style="display: inline-flex; gap: 0.4rem; align-items: center;">
                                        <button type="button" onclick="openEditKuotaModal({{ $k->id }}, {{ $k->jumlah }}, {{ $k->terisi }}, '{{ addslashes($k->jurusan?->nama_jurusan ?? '') }}')" style="padding: 0.45rem 0.8rem; background: #ffffff; border: 1px solid var(--neutral-300, #CBD5E1); color: var(--neutral-700, #334155); border-radius: 6px; font-weight: 600; font-size: 0.8125rem; cursor: pointer; transition: all 0.15s ease;" onmouseover="this.style.borderColor='#0641CC'; this.style.color='#0641CC'" onmouseout="this.style.borderColor='var(--neutral-300, #CBD5E1)'; this.style.color='var(--neutral-700, #334155)'">
                                            Edit
                                        </button>
                                        <form method="POST" action="{{ route('admin.kuota.toggle', $k) }}" style="display: inline;" onsubmit="return confirm('Ubah status aktif kuota jurusan ini?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" style="padding: 0.45rem 0.8rem; background: {{ $k->aktif ? '#FEE2E2' : '#D1FAE5' }}; color: {{ $k->aktif ? '#991B1B' : '#065F46' }}; border: 1px solid {{ $k->aktif ? '#FECACA' : '#A7F3D0' }}; border-radius: 6px; font-weight: 600; font-size: 0.8125rem; cursor: pointer; transition: opacity 0.15s ease;" onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                                                {{ $k->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>

{{-- MODAL TAMBAH KUOTA --}}
@if($selectedBulan)
<div id="modal-tambah-kuota" class="modal-overlay">
    <div class="modal-container">
        {{-- Modal Header --}}
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0); display: flex; justify-content: space-between; align-items: center; background: #F8FAFC;">
            <div style="display: flex; align-items: center; gap: 0.6rem;">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: #EFF6FF; color: #1D4ED8; display: flex; align-items: center; justify-content: center;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                </div>
                <div>
                    <h3 style="font-size: 1.0625rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0;">Tambah Kuota Jurusan</h3>
                    <p style="font-size: 0.75rem; color: var(--neutral-500, #64748B); margin: 0.15rem 0 0 0;">
                        Periode: <strong>{{ $selectedBulan->bulan_mulai?->translatedFormat('F Y') }}</strong>
                    </p>
                </div>
            </div>
            <button type="button" onclick="closeTambahKuotaModal()" style="background: transparent; border: none; font-size: 1.25rem; color: var(--neutral-400, #94A3B8); cursor: pointer; width: 32px; height: 32px; border-radius: 6px; display: flex; align-items: center; justify-content: center;" onmouseover="this.style.color='#0F172A'; this.style.background='#F1F5F9'" onmouseout="this.style.color='var(--neutral-400, #94A3B8)'; this.style.background='transparent'">
                ✕
            </button>
        </div>

        {{-- Modal Form --}}
        <form method="POST" action="{{ route('admin.kuota.store') }}" style="padding: 1.5rem;">
            @csrf
            <input type="hidden" name="bulan_magang_id" value="{{ $selectedBulan->id }}">

            <div style="display: flex; flex-direction: column; gap: 0.375rem; margin-bottom: 1.25rem;">
                <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">
                    Pilih Kejuruan <span style="color: #DC2626;">*</span>
                </label>
                <select name="jurusan_id" required style="height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff; color: var(--neutral-900, #0F172A);">
                    <option value="">-- Pilih Jurusan yang Tersedia --</option>
                    @foreach($jurusanList as $jur)
                        <option value="{{ $jur->id }}">{{ $jur->nama_jurusan }} ({{ strtoupper($jur->kategori) }})</option>
                    @endforeach
                </select>
                <span style="font-size: 0.75rem; color: var(--neutral-500, #64748B);">Pastikan jurusan aktif belum dialokasikan pada periode bulan ini.</span>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.375rem; margin-bottom: 1.5rem;">
                <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">
                    Jumlah Kuota Kursi <span style="color: #DC2626;">*</span>
                </label>
                <input type="number" name="jumlah" min="1" max="100" value="5" required style="height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff; color: var(--neutral-900, #0F172A);">
                <span style="font-size: 0.75rem; color: var(--neutral-500, #64748B);">Merujuk aturan BR-01: Alokasi umum disarankan mempertimbangkan kapasitas maksimal dinas.</span>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.6rem; border-top: 1px solid var(--neutral-200, #E2E8F0); padding-top: 1.25rem;">
                <button type="button" onclick="closeTambahKuotaModal()" style="padding: 0.55rem 1.25rem; background: #ffffff; border: 1px solid var(--neutral-300, #CBD5E1); color: var(--neutral-700, #334155); border-radius: 8px; font-weight: 600; font-size: 0.875rem; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="padding: 0.55rem 1.4rem; background: #0641CC; color: #ffffff; border: none; border-radius: 8px; font-weight: 600; font-size: 0.875rem; cursor: pointer; box-shadow: 0 1px 3px rgba(6, 65, 204, 0.25);">
                    Simpan Alokasi Kuota
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT KUOTA --}}
<div id="modal-edit-kuota" class="modal-overlay">
    <div class="modal-container">
        {{-- Modal Header --}}
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0); display: flex; justify-content: space-between; align-items: center; background: #F8FAFC;">
            <div style="display: flex; align-items: center; gap: 0.6rem;">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: #EFF6FF; color: #1D4ED8; display: flex; align-items: center; justify-content: center;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                </div>
                <div>
                    <h3 style="font-size: 1.0625rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0;">Perbarui Alokasi Kuota</h3>
                    <p style="font-size: 0.75rem; color: var(--neutral-500, #64748B); margin: 0.15rem 0 0 0;" id="edit-kuota-jurusan-title">
                        Jurusan: -
                    </p>
                </div>
            </div>
            <button type="button" onclick="closeEditKuotaModal()" style="background: transparent; border: none; font-size: 1.25rem; color: var(--neutral-400, #94A3B8); cursor: pointer; width: 32px; height: 32px; border-radius: 6px; display: flex; align-items: center; justify-content: center;" onmouseover="this.style.color='#0F172A'; this.style.background='#F1F5F9'" onmouseout="this.style.color='var(--neutral-400, #94A3B8)'; this.style.background='transparent'">
                ✕
            </button>
        </div>

        {{-- Modal Form --}}
        <form method="POST" id="form-edit-kuota" action="" style="padding: 1.5rem;">
            @csrf
            @method('PUT')
            <input type="hidden" name="bulan_magang_id" value="{{ $selectedBulan->id }}">

            <div style="display: flex; flex-direction: column; gap: 0.375rem; margin-bottom: 1.5rem;">
                <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">
                    Jumlah Kuota Baru (Kursi) <span style="color: #DC2626;">*</span>
                </label>
                <input type="number" id="edit-input-jumlah" name="jumlah" required style="height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff; color: var(--neutral-900, #0F172A);">
                <div id="edit-info-terisi" style="font-size: 0.75rem; color: #B45309; background: #FEF3C7; padding: 0.35rem 0.65rem; border-radius: 6px; margin-top: 0.35rem; font-weight: 600;">
                    Saat ini telah terisi: 0 kursi.
                </div>
                <span style="font-size: 0.72rem; color: var(--neutral-500, #64748B); margin-top: 0.2rem;">
                    Sesuai AC-05-3: Kuota baru tidak boleh lebih kecil dari jumlah kursi yang telah terisi.
                </span>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.6rem; border-top: 1px solid var(--neutral-200, #E2E8F0); padding-top: 1.25rem;">
                <button type="button" onclick="closeEditKuotaModal()" style="padding: 0.55rem 1.25rem; background: #ffffff; border: 1px solid var(--neutral-300, #CBD5E1); color: var(--neutral-700, #334155); border-radius: 8px; font-weight: 600; font-size: 0.875rem; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="padding: 0.55rem 1.4rem; background: #0641CC; color: #ffffff; border: none; border-radius: 8px; font-weight: 600; font-size: 0.875rem; cursor: pointer; box-shadow: 0 1px 3px rgba(6, 65, 204, 0.25);">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
    function openTambahKuotaModal() {
        const modal = document.getElementById('modal-tambah-kuota');
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }
    function closeTambahKuotaModal() {
        const modal = document.getElementById('modal-tambah-kuota');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    function openEditKuotaModal(kuotaId, jumlah, terisi, namaJurusan) {
        const titleEl = document.getElementById('edit-kuota-jurusan-title');
        const inputEl = document.getElementById('edit-input-jumlah');
        const infoEl = document.getElementById('edit-info-terisi');
        const formEl = document.getElementById('form-edit-kuota');
        const modal = document.getElementById('modal-edit-kuota');

        if (titleEl) titleEl.innerText = 'Jurusan: ' + namaJurusan;
        if (inputEl) {
            inputEl.value = jumlah;
            inputEl.min = terisi;
        }
        if (infoEl) infoEl.innerText = 'Saat ini telah terisi: ' + terisi + ' kursi (batas minimum).';
        if (formEl) formEl.action = '/admin/kuota/' + kuotaId;
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }
    function closeEditKuotaModal() {
        const modal = document.getElementById('modal-edit-kuota');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    // Close on Escape or click outside modal container
    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeTambahKuotaModal();
            closeEditKuotaModal();
        }
    });

    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) {
                closeTambahKuotaModal();
                closeEditKuotaModal();
            }
        });
    });
</script>
@endsection
