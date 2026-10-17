@extends('layouts.dashboard')

@section('title', 'Arsip Peserta Magang - SuperAdmin MagKom')
@section('page_title', 'Arsip & Rekam Jejak Alumni Magang')
@section('page_description', 'Bank data historis peserta magang selesai dan nonaktif secara baca-saja (AC-12-4, PRD 9.2).')

@section('breadcrumbs')
    <a href="{{ route('superadmin.dashboard') }}">SuperAdmin</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Arsip Peserta</span>
@endsection

@section('page_actions')
    <div style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; border-radius: 8px; background: var(--blue-tint); border: 1px solid var(--blue-tint-line); font-size: 0.8125rem; font-weight: 700; color: var(--blue-700);">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
        <span>Strictly Read-Only Bank Data</span>
    </div>
@endsection

@push('styles')
<style>
    /* Dark Mode Overrides for SuperAdmin Arsip */
    [data-theme="dark"] div[style*="background: var(--neutral-0)"] {
        background-color: #182234 !important;
        border-color: #26354D !important;
    }

    [data-theme="dark"] div[style*="background: var(--blue-tint)"] {
        background-color: rgba(37, 99, 235, 0.15) !important;
        border-color: rgba(59, 130, 246, 0.3) !important;
    }

    [data-theme="dark"] input[type="text"],
    [data-theme="dark"] select {
        background-color: #0F172A !important;
        color: #F8FAFC !important;
        border-color: #26354D !important;
    }

    [data-theme="dark"] button[style*="background: var(--neutral-900)"] {
        background-color: #2563EB !important;
        color: #FFFFFF !important;
    }

    [data-theme="dark"] div[style*="border: 1px solid var(--neutral-400)"],
    [data-theme="dark"] div[style*="border-bottom: 1px solid var(--neutral-400)"] {
        border-color: #26354D !important;
    }

    [data-theme="dark"] table thead tr {
        background-color: #141E30 !important;
        border-bottom-color: #26354D !important;
        color: #94A3B8 !important;
    }

    [data-theme="dark"] table th {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] table tbody tr {
        border-bottom-color: #26354D !important;
    }

    [data-theme="dark"] table tbody tr:hover {
        background-color: #162238 !important;
    }

    [data-theme="dark"] span[style*="color: var(--neutral-900)"],
    [data-theme="dark"] div[style*="color: var(--neutral-900)"],
    [data-theme="dark"] strong[style*="color: var(--neutral-900)"],
    [data-theme="dark"] span[style*="color: var(--neutral-800)"],
    [data-theme="dark"] h3[style*="color: var(--neutral-800)"] {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] span[style*="color: var(--neutral-600)"],
    [data-theme="dark"] div[style*="color: var(--neutral-600)"],
    [data-theme="dark"] p[style*="color: var(--neutral-600)"] {
        color: #94A3B8 !important;
    }
</style>
@endpush

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">

    <!-- 1. TOOLBAR & FILTER CARD -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle);">
        <form method="GET" action="{{ route('superadmin.arsip.index') }}" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; justify-content: space-between;">
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; flex: 1;">
                <div style="position: relative; min-width: 280px; flex: 1;">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, NIM, atau institusi..." style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem 0 2.5rem; font-size: 0.875rem; font-family: inherit;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position: absolute; left: 0.875rem; top: 50%; transform: translateY(-50%); color: var(--neutral-500);"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </div>

                <select name="status" style="height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; background: var(--neutral-0); font-family: inherit; min-width: 220px;">
                    <option value="">Semua Status Arsip</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai Magang (Masa Tenggang)</option>
                    <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Akun Nonaktif (Arsip Final)</option>
                    <option value="mengundurkan_diri" {{ request('status') === 'mengundurkan_diri' ? 'selected' : '' }}>Mengundurkan Diri</option>
                </select>

                <button type="submit" style="height: 42px; padding: 0 1.25rem; background: var(--neutral-900); color: #FFFFFF; font-weight: 700; font-size: 0.8125rem; border: none; border-radius: 8px; cursor: pointer; transition: all 0.15s; display: inline-flex; align-items: center; gap: 0.375rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                    Cari Data
                </button>

                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('superadmin.arsip.index') }}" style="font-size: 0.8125rem; color: var(--neutral-600); text-decoration: none; padding: 0.5rem 0.75rem; font-weight: 600;">
                        Reset Filter
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 2. DATA TABLE CARD -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-subtle);">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-400); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
            <div>
                <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-800); margin: 0;">
                    Bank Data Riwayat & Arsip Alumni Magang
                </h3>
                <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0.125rem 0 0 0;">
                    Data peserta yang telah menyelesaikan bimbingan atau berstatus purna tugas di Diskominfo Sleman.
                </p>
            </div>
            <span style="font-size: 0.8125rem; color: var(--neutral-600); font-weight: 600;">
                Total Arsip: <strong>{{ method_exists($arsipList, 'total') ? $arsipList->total() : $arsipList->count() }} Data</strong>
            </span>
        </div>

        @if($arsipList->isEmpty())
            <div style="padding: 4rem 1.5rem; text-align: center; color: var(--neutral-600);">
                <div style="width: 48px; height: 48px; border-radius: 50%; background: var(--neutral-100); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto; color: var(--neutral-500);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="21 8 21 21 3 21 3 8"></polyline><rect x="1" y="3" width="22" height="5"></rect><line x1="10" y1="12" x2="14" y2="12"></line></svg>
                </div>
                <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-800); margin: 0 0 0.25rem 0;">Tidak Ada Data Arsip</h3>
                <p style="font-size: 0.8125rem; margin: 0;">Belum ada peserta dengan status magang selesai, akun nonaktif, atau mengundurkan diri yang tersimpan.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem; text-align: left;">
                    <thead>
                        <tr style="background: var(--neutral-100); border-bottom: 1px solid var(--neutral-400); color: var(--neutral-700); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <th style="padding: 1rem 1.25rem; font-weight: 700;">Peserta & Institusi</th>
                            <th style="padding: 1rem 1rem; font-weight: 700;">Jurusan / Bidang</th>
                            <th style="padding: 1rem 1rem; font-weight: 700;">Periode Pelaksanaan</th>
                            <th style="padding: 1rem 1rem; font-weight: 700;">Pembimbing Lapangan</th>
                            <th style="padding: 1rem 1rem; font-weight: 700; text-align: center; width: 140px;">Nilai & Predikat</th>
                            <th style="padding: 1rem 1rem; font-weight: 700; width: 150px; text-align: center;">Status Arsip</th>
                            <th style="padding: 1rem 1.25rem; font-weight: 700; text-align: right; width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($arsipList as $item)
                            <tr style="border-bottom: 1px solid var(--neutral-200); transition: background 0.15s ease;" onmouseover="this.style.background='var(--neutral-100)'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 1rem 1.25rem;">
                                    <div style="font-weight: 700; color: var(--neutral-900); font-size: 0.9375rem;">
                                        {{ $item->peserta?->nama }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.15rem;">
                                        {{ $item->peserta?->email }}
                                    </div>
                                    <small style="font-size: 0.75rem; color: var(--blue-700); font-weight: 600; display: block; margin-top: 0.15rem;">
                                        {{ $item->peserta?->profil?->institusi ?? '-' }} (NIM: {{ $item->peserta?->profil?->nim_nisn ?? '-' }})
                                    </small>
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

                                <td style="padding: 1rem 1rem; text-align: center;">
                                    @if($item->penilaian)
                                         <div style="font-size: 1.125rem; font-weight: 800; color: var(--blue-700); line-height: 1.2;">
                                             {{ $item->penilaian->nilai_akhir }}
                                         </div>
                                         <span style="display: inline-flex; align-items: center; padding: 0.2rem 0.5rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 800; background: var(--green-100); color: var(--green-700); border: 1px solid rgba(22, 163, 74, 0.2); margin-top: 0.25rem;">
                                             Predikat {{ $item->penilaian->predikat }}
                                         </span>
                                    @else
                                        <span style="color: var(--neutral-500); font-size: 0.8125rem;">-</span>
                                    @endif
                                </td>

                                <td style="padding: 1rem 1rem; text-align: center; white-space: nowrap;">
                                    @if($item->status?->value === 'selesai')
                                        <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.625rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: var(--blue-tint); color: var(--blue-700); border: 1px solid var(--blue-tint-line);">
                                            Selesai Magang
                                        </span>
                                    @elseif($item->status?->value === 'mengundurkan_diri')
                                        <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.625rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: var(--red-100); color: var(--red-700); border: 1px solid var(--red-300);">
                                            Mengundurkan Diri
                                        </span>
                                    @else
                                        <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.625rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: var(--neutral-200); color: var(--neutral-700); border: 1px solid var(--neutral-400);">
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

            @if($arsipList->hasPages())
                <div style="padding: 1.25rem 1.5rem; border-top: 1px solid var(--neutral-200); background: var(--neutral-100);">
                    {{ $arsipList->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
