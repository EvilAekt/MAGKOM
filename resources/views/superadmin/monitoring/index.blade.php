@extends('layouts.dashboard')

@section('title', 'Monitoring Pelaksanaan Magang - SuperAdmin MagKom')
@section('page_title', 'Monitoring Pelaksanaan Magang Aktif')
@section('page_description', 'Pantau seluruh aktivitas penugasan, logbook harian, dan proses bimbingan peserta aktif secara baca-saja (PRD 3.3).')

@section('breadcrumbs')
    <a href="{{ route('superadmin.dashboard') }}">SuperAdmin</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Monitoring Peserta</span>
@endsection

@push('styles')
<style>
    /* Dark Mode Overrides for SuperAdmin Monitoring Peserta */
    [data-theme="dark"] div[style*="background: var(--neutral-0)"] {
        background-color: #182234 !important;
        border-color: #26354D !important;
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

    <!-- 1. FILTER & SEARCH CARD -->
    <x-gentelella.card>
        <form method="GET" action="{{ route('superadmin.monitoring.index') }}" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; justify-content: space-between;">
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; flex: 1;">
                <div style="position: relative; min-width: 260px; flex: 1;">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, NIM, atau kampus..." style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem 0 2.5rem; font-size: 0.875rem; font-family: inherit;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position: absolute; left: 0.875rem; top: 50%; transform: translateY(-50%); color: var(--neutral-500);"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </div>

                <select name="pembimbing_id" style="height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; background: var(--neutral-0); font-family: inherit; min-width: 190px;">
                    <option value="">Semua Pembimbing</option>
                    @foreach($pembimbingList as $p)
                        <option value="{{ $p->id }}" {{ request('pembimbing_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->nama }}
                        </option>
                    @endforeach
                </select>

                <select name="jurusan_id" style="height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; background: var(--neutral-0); font-family: inherit; min-width: 180px;">
                    <option value="">Semua Jurusan</option>
                    @foreach($jurusanList as $j)
                        <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>
                            {{ $j->nama_jurusan }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" style="height: 42px; padding: 0 1.25rem; background: var(--neutral-900); color: #FFFFFF; font-weight: 700; font-size: 0.8125rem; border: none; border-radius: 8px; cursor: pointer; transition: all 0.15s; display: inline-flex; align-items: center; gap: 0.375rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                    Filter
                </button>

                @if(request()->hasAny(['search', 'pembimbing_id', 'jurusan_id']))
                    <a href="{{ route('superadmin.monitoring.index') }}" style="font-size: 0.8125rem; color: var(--neutral-600); text-decoration: none; padding: 0.5rem 0.75rem; font-weight: 600;">
                        Reset Filter
                    </a>
                @endif
            </div>
        </form>
    </x-gentelella.card>

    <!-- 2. DATA TABLE CARD -->
    <x-gentelella.card
        title="Daftar Peserta Magang Aktif"
        subtitle="Peserta yang sedang menjalankan masa magang di dinas Kominfo."
    >
        <x-slot:icon>
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="text-primary">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
        </x-slot:icon>

        <x-slot:actions>
            <span style="font-size: 0.8125rem; color: var(--neutral-600); font-weight: 600;">
                Total: <strong>{{ method_exists($penempatanList, 'total') ? $penempatanList->total() : $penempatanList->count() }} Peserta Aktif</strong>
            </span>
        </x-slot:actions>

        @if($penempatanList->isEmpty())
            <div style="padding: 4rem 1.5rem; text-align: center; color: var(--neutral-600);">
                <div style="width: 48px; height: 48px; margin: 0 auto 1rem; border-radius: 50%; background: var(--neutral-100); display: flex; align-items: center; justify-content: center; color: var(--neutral-500);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </div>
                <h4 style="font-size: 1rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.25rem;">Tidak Ada Peserta Magang Aktif</h4>
                <p style="font-size: 0.8125rem; margin: 0;">Saat ini belum ada data penempatan aktif yang cocok dengan kriteria pencarian.</p>
            </div>
        @else
            <div style="overflow-x: auto; margin: -10px -20px -20px -20px;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                    <thead>
                        <tr style="background: var(--neutral-100); border-bottom: 1px solid var(--neutral-400); color: var(--neutral-700); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <th style="padding: 1rem 1.25rem; font-weight: 700;">Nama Peserta</th>
                            <th style="padding: 1rem 1rem; font-weight: 700;">Institusi & Jurusan</th>
                            <th style="padding: 1rem 1rem; font-weight: 700;">Pembimbing & Bidang</th>
                            <th style="padding: 1rem 1rem; font-weight: 700;">Jadwal Bimbingan</th>
                            <th style="padding: 1rem 1rem; font-weight: 700; text-align: center; width: 110px;">Jalur</th>
                            <th style="padding: 1rem 1.25rem; font-weight: 700; text-align: right; width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($penempatanList as $item)
                            <tr style="border-bottom: 1px solid var(--neutral-200); transition: background 0.15s ease;" onmouseover="this.style.background='var(--neutral-100)'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 1rem 1.25rem;">
                                    <div style="font-weight: 700; color: var(--neutral-900); font-size: 0.9375rem;">
                                        {{ $item->peserta?->nama }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--neutral-500); font-family: monospace; margin-top: 0.15rem;">
                                        NIM: {{ $item->peserta?->profil?->nim_nisn ?? '-' }}
                                    </div>
                                </td>
                                <td style="padding: 1rem 1rem;">
                                    <div style="font-weight: 600; color: var(--neutral-800); font-size: 0.8125rem;">
                                        {{ $item->peserta?->profil?->institusi ?? '-' }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--neutral-500); margin-top: 0.15rem;">
                                        {{ $item->peserta?->profil?->jurusan?->nama_jurusan ?? ($item->kuota?->jurusan?->nama_jurusan ?? '-') }}
                                    </div>
                                </td>
                                <td style="padding: 1rem 1rem;">
                                    <div style="font-weight: 600; color: var(--neutral-800); font-size: 0.8125rem;">
                                        {{ $item->pembimbing?->nama ?? 'Belum Ditetapkan' }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--blue-700); margin-top: 0.15rem;">
                                        {{ $item->bidang?->nama_bidang ?? 'Umum' }}
                                    </div>
                                </td>
                                <td style="padding: 1rem 1rem; color: var(--neutral-700); font-size: 0.8125rem;">
                                    <div>
                                        {{ $item->tgl_mulai ? \Carbon\Carbon::parse($item->tgl_mulai)->format('d/m/Y') : '-' }} s/d {{ $item->tgl_selesai ? \Carbon\Carbon::parse($item->tgl_selesai)->format('d/m/Y') : '-' }}
                                    </div>
                                    <div style="color: var(--blue-700); font-weight: 700; font-size: 0.75rem; margin-top: 0.15rem;">
                                        {{ $item->durasi_bulan ?? 1 }} Bulan Magang
                                    </div>
                                </td>
                                <td style="padding: 1rem 1rem; text-align: center; white-space: nowrap;">
                                    @if($item->jalur?->value === 'khusus')
                                        <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.625rem; border-radius: 9999px; background: var(--amber-100); color: var(--amber-700); border: 1px solid var(--amber-300); font-weight: 700; font-size: 0.75rem;">
                                            Khusus
                                        </span>
                                    @else
                                        <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.625rem; border-radius: 9999px; background: var(--blue-tint); color: var(--blue-700); border: 1px solid var(--blue-tint-line); font-weight: 700; font-size: 0.75rem;">
                                            Umum
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 1rem 1.25rem; text-align: right; white-space: nowrap;">
                                    <a href="{{ route('superadmin.monitoring.show', $item) }}" style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.45rem 0.875rem; background: var(--blue-600); color: #FFFFFF; text-decoration: none; border-radius: 6px; font-weight: 700; font-size: 0.8125rem; transition: all 0.15s; box-shadow: var(--shadow-subtle);">
                                        <span>Pantau Detail</span>
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($penempatanList->hasPages())
                <div style="padding: 1.25rem 1.5rem; border-top: 1px solid var(--neutral-200); background: var(--neutral-100); margin: 0 -20px -20px -20px;">
                    {{ $penempatanList->links() }}
                </div>
            @endif
        @endif
    </x-gentelella.card>
</div>
@endsection
