@extends('layouts.dashboard')

@section('title', 'Arsip Peserta Magang - SuperAdmin MagKom')
@section('topbar_title', 'Arsip Peserta')

@section('breadcrumbs')
    <a href="{{ route('superadmin.dashboard') }}">SuperAdmin</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Arsip Peserta</span>
@endsection

@push('styles')
    @vite(['resources/css/pages/superadmin/superadmin-common.css'])
@endpush

@section('content')
<div class="sa-fluid-container" style="display: flex; flex-direction: column; gap: 1.5rem;">

    {{-- 1. Hero Banner Khas Sleman MagKom --}}
    <div class="sa-hero-banner sc-reveal-hero" data-scroll-reveal>
        <div class="banner-top-row">
            <div>
                <h1 class="banner-title">Arsip &amp; Rekam Jejak Alumni Magang</h1>
                <div class="banner-subtitle">
                    <span>Cakupan: <strong>Selesai Magang, Nonaktif &amp; Purna Tugas</strong></span>
                    <span class="bullet-separator">&bull;</span>
                    <span>Total Arsip: <strong>{{ $totalArsip }} Alumni Terdata</strong></span>
                </div>
            </div>
            <div class="banner-actions-group" style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                <div class="btn-banner-action--secondary" style="cursor: default; pointer-events: none;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                    <span>Strictly Read-Only Bank Data</span>
                </div>
                <a href="{{ route('admin.laporan.index', ['tab' => 'peserta']) }}" class="btn-banner-action">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    <span>Ekspor Laporan</span>
                </a>
            </div>
        </div>
    </div>

    {{-- 2. Grid 4 Kartu Statistik Mengadopsi Gentelella Tile-Stat --}}
    <div class="tiles-grid sc-reveal-up sc-delay-1" data-scroll-reveal>
        <!-- 1. Total Arsip -->
        <x-gentelella.tile-stat
            title="TOTAL ARSIP"
            value="{{ $totalArsip }}"
            color="blue"
            trendType="info"
            trendDirection="up"
            trendText="Alumni"
            subText="Total Riwayat Peserta Magang"
            :sparkBars="[35, 50, 65, 75, 85, 95]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <polyline points="21 8 21 21 3 21 3 8"></polyline>
                    <rect x="1" y="3" width="22" height="5"></rect>
                    <line x1="10" y1="12" x2="14" y2="12"></line>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <!-- 2. Selesai Magang -->
        <x-gentelella.tile-stat
            title="SELESAI MAGANG"
            value="{{ $totalSelesai }}"
            color="teal"
            trendType="success"
            trendDirection="up"
            trendText="Lulus"
            subText="Masa Bimbingan Tuntas (Masa Tenggang)"
            :sparkBars="[25, 40, 55, 70, 85, 95]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                    <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <!-- 3. Akun Nonaktif -->
        <x-gentelella.tile-stat
            title="AKUN NONAKTIF"
            value="{{ $totalNonaktif }}"
            color="purple"
            trendType="info"
            trendDirection="up"
            trendText="Arsip Final"
            subText="Purna Retensi & Terkunci Permanen"
            :sparkBars="[20, 35, 50, 60, 75, 90]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0110 0v4"></path>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <!-- 4. Mengundurkan Diri -->
        <x-gentelella.tile-stat
            title="MENGUNDURKAN DIRI"
            value="{{ $totalMengundurkanDiri }}"
            color="amber"
            trendType="danger"
            trendDirection="up"
            trendText="Purna Dini"
            subText="Penghentian Sebelum Periode Selesai"
            :sparkBars="[10, 20, 30, 45, 60, 75]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="8.5" cy="7" r="4"></circle>
                    <line x1="23" y1="11" x2="17" y2="11"></line>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>
    </div>

    <!-- 3. FILTER & TOOLBAR CARD -->
    <div class="staf-filter-card sc-reveal-up sc-delay-2" data-scroll-reveal>
        <form method="GET" action="{{ route('superadmin.arsip.index') }}" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; justify-content: space-between;">
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; flex: 1;">
                {{-- Search Box --}}
                <div style="position: relative; min-width: 260px; flex: 1;">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, NIM, atau institusi..." style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem 0 2.5rem; font-size: 0.875rem; font-family: inherit;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position: absolute; left: 0.875rem; top: 50%; transform: translateY(-50%); color: var(--neutral-500);">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </div>

                {{-- Status Filter --}}
                <select name="status" style="height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; background: var(--neutral-0); font-family: inherit; min-width: 200px;">
                    <option value="">Semua Status Arsip</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai Magang</option>
                    <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Akun Nonaktif (Arsip)</option>
                    <option value="mengundurkan_diri" {{ request('status') === 'mengundurkan_diri' ? 'selected' : '' }}>Mengundurkan Diri</option>
                </select>

                {{-- Year Filter --}}
                <select name="tahun" style="height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; background: var(--neutral-0); font-family: inherit; min-width: 160px;">
                    <option value="">Semua Tahun</option>
                    @foreach($tahunList as $th)
                        <option value="{{ $th }}" {{ request('tahun') == $th ? 'selected' : '' }}>Tahun {{ $th }}</option>
                    @endforeach
                </select>

                {{-- Submit Filter Button --}}
                <button type="submit" class="sa-btn-filter">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    <span>Filter</span>
                </button>

                @if(request()->hasAny(['search', 'status', 'tahun']))
                    <a href="{{ route('superadmin.arsip.index') }}" style="font-size: 0.8125rem; color: var(--neutral-600); text-decoration: none; padding: 0.5rem 0.75rem; font-weight: 600;">
                        Reset Filter
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- 4. DATA TABLE CARD --}}
    <div class="gentelella-card sc-reveal-up sc-delay-3" data-scroll-reveal style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-top: 3.5px solid var(--magkom-primary-blue, #01347C) !important; border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-subtle);">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-400); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
            <div>
                <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-800); margin: 0;">
                    Bank Data Riwayat &amp; Arsip Alumni Magang
                </h3>
            </div>
            <span style="font-size: 0.8125rem; color: var(--neutral-600); font-weight: 600; background: var(--neutral-100); padding: 0.35rem 0.75rem; border-radius: 20px; border: 1px solid var(--neutral-300);">
                Total Arsip: <strong>{{ method_exists($arsipList, 'total') ? $arsipList->total() : $arsipList->count() }} Data</strong>
            </span>
        </div>

        @if($arsipList->isEmpty())
            <div style="padding: 4rem 1.5rem; text-align: center; color: var(--neutral-600);">
                <div style="width: 48px; height: 48px; border-radius: 50%; background: var(--neutral-100); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto; color: var(--neutral-500);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="21 8 21 21 3 21 3 8"></polyline>
                        <rect x="1" y="3" width="22" height="5"></rect>
                        <line x1="10" y1="12" x2="14" y2="12"></line>
                    </svg>
                </div>
                <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-800); margin: 0 0 0.25rem 0;">Tidak Ada Data Arsip</h3>
                <p style="font-size: 0.8125rem; margin: 0;">Belum ada data peserta purna magang yang sesuai dengan kriteria filter pencarian.</p>
            </div>
        @else
            <div class="table-responsive-gentelella" style="overflow-x: auto;">
                <table class="table jambo_table bulk_action" style="width: 100%; border-collapse: collapse; font-size: 0.875rem; text-align: left;">
                    <thead>
                        <tr class="headings">
                            <th style="padding: 0.875rem 1.25rem;">Peserta &amp; Institusi</th>
                            <th style="padding: 0.875rem 1rem;">Jurusan &amp; Bidang</th>
                            <th style="padding: 0.875rem 1rem;">Jadwal Magang</th>
                            <th style="padding: 0.875rem 1rem;">Pembimbing Lapangan</th>
                            <th style="padding: 0.875rem 1rem; width: 160px; text-align: center;">Status Arsip</th>
                            <th style="padding: 0.875rem 1.25rem; text-align: right; width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($arsipList as $item)
                            @php
                                $namaPeserta = $item->peserta?->nama ?? 'Peserta';
                                $initials = collect(explode(' ', $namaPeserta))->take(2)->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('');
                            @endphp
                            <tr class="sa-table-row" style="border-bottom: 1px solid var(--neutral-200);">
                                <td style="padding: 1rem 1.25rem;">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div style="width: 38px; height: 38px; min-width: 38px; border-radius: 8px; background: linear-gradient(135deg, #01347C 0%, #003566 100%); color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8125rem; box-shadow: 0 2px 4px rgba(1, 52, 124, 0.2);">
                                            {{ $initials ?: 'AP' }}
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; color: var(--neutral-900); font-size: 0.9375rem;">
                                                {{ $namaPeserta }}
                                            </div>
                                            <div style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.15rem;">
                                                {{ $item->peserta?->email }}
                                            </div>
                                            <small style="font-size: 0.75rem; color: var(--blue-700); font-weight: 600; display: block; margin-top: 0.15rem;">
                                                {{ $item->peserta?->profil?->institusi ?? '-' }} (NIM: {{ $item->peserta?->profil?->nim_nisn ?? '-' }})
                                            </small>
                                        </div>
                                    </div>
                                </td>

                                <td style="padding: 1rem 1rem;">
                                    <div style="font-weight: 600; color: var(--neutral-800); font-size: 0.8125rem;">
                                        {{ $item->kuota?->jurusan?->nama_jurusan ?? ($item->slotKhusus ? 'Jalur Khusus Mitra' : '-') }}
                                    </div>
                                    <small style="color: var(--neutral-600);">{{ $item->bidang?->nama_bidang ?? 'Bidang Umum' }}</small>
                                </td>

                                <td style="padding: 1rem 1rem; font-size: 0.8125rem; color: var(--neutral-700);">
                                    <div><strong>Mulai:</strong> {{ $item->tgl_mulai?->translatedFormat('d M Y') }}</div>
                                    <div><strong>Selesai:</strong> {{ $item->tgl_selesai?->translatedFormat('d M Y') }}</div>
                                </td>

                                <td style="padding: 1rem 1rem;">
                                    <div style="font-weight: 600; color: var(--neutral-800); font-size: 0.8125rem;">
                                        {{ $item->pembimbing?->nama ?? '-' }}
                                    </div>
                                    <small style="color: var(--neutral-600);">{{ $item->pembimbing?->email }}</small>
                                </td>

                                <td style="padding: 1rem 1rem; text-align: center; white-space: nowrap;">
                                    @if($item->status?->value === 'selesai')
                                        <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.35rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: var(--blue-tint); color: var(--blue-700); border: 1px solid var(--blue-tint-line);">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                            Selesai Magang
                                        </span>
                                    @elseif($item->status?->value === 'mengundurkan_diri')
                                        <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.35rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: var(--red-100); color: var(--red-700); border: 1px solid var(--red-300);">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                            Mengundurkan Diri
                                        </span>
                                    @else
                                        <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.35rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: var(--neutral-200); color: var(--neutral-700); border: 1px solid var(--neutral-400);">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0110 0v4"></path></svg>
                                            Nonaktif (Arsip)
                                        </span>
                                    @endif
                                </td>

                                <td style="padding: 1rem 1.25rem; text-align: right; white-space: nowrap;">
                                    <a href="{{ route('superadmin.arsip.show', $item) }}" style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.45rem 0.875rem; background: var(--blue-600); color: #FFFFFF; text-decoration: none; border-radius: 6px; font-weight: 700; font-size: 0.8125rem; box-shadow: var(--shadow-subtle); transition: all 0.15s;">
                                        <span>Detail Arsip</span>
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($arsipList->total() > 0)
                <div style="padding: 1.25rem 1.5rem; border-top: 1px solid var(--neutral-200); background: var(--neutral-100);">
                    {{ $arsipList->links() }}
                </div>
            @endif
        @endif
    </div>
</div>

@push('scripts')
    @vite(['resources/js/pages/superadmin/staf-index.js'])
@endpush
@endsection
