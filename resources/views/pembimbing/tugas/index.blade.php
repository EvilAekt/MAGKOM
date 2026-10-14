@extends('layouts.dashboard')

@section('title', 'Penugasan Peserta - Portal MagKom')

@section('content')
<div class="dashboard-content" style="display: flex; flex-direction: column; gap: 1.5rem;">



    <!-- Header & Action -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <nav style="font-size: 0.8rem; color: var(--neutral-500); margin-bottom: 0.35rem; display: flex; align-items: center; gap: 0.4rem;">
                <a href="{{ route('pembimbing.dashboard') }}" style="color: var(--neutral-600); text-decoration: none;">Dashboard</a>
                <span>/</span>
                <span style="color: var(--neutral-900); font-weight: 600;">Tugas Peserta</span>
            </nav>
            <h1 style="font-size: 1.5rem; font-weight: 700; color: var(--neutral-900); margin: 0;">
                Manajemen Penugasan & Progres (F-08)
            </h1>
            <p style="font-size: 0.875rem; color: var(--neutral-600); margin: 0.25rem 0 0 0;">
                Berikan penugasan terstruktur kepada peserta bimbingan dan evaluasi kemajuan kerja.
            </p>
        </div>
        <div>
            <a href="{{ route('pembimbing.tugas.create') }}" style="background: #0641CC; color: #ffffff; text-decoration: none; padding: 0.55rem 1.2rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 2px 6px rgba(6, 65, 204, 0.2);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Buat Tugas Baru
            </a>
        </div>
    </div>

    <!-- Filter & Toolbar -->
    <div style="background: #ffffff; border-radius: 14px; border: 1px solid var(--neutral-200); padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <form method="GET" action="{{ route('pembimbing.tugas.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            
            <!-- Filter Status (Sesuai StatusTugasEnum) -->
            <div style="flex: 1; min-width: 180px;">
                <label style="font-size: 0.8rem; font-weight: 600; color: var(--neutral-700); display: block; margin-bottom: 0.35rem;">Status Tugas</label>
                <select name="status" style="width: 100%; height: 40px; border: 1px solid var(--neutral-300); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff;">
                    <option value="">Semua Status</option>
                    <option value="belum" {{ request('status') === 'belum' ? 'selected' : '' }}>Belum Dikerjakan</option>
                    <option value="proses" {{ request('status') === 'proses' ? 'selected' : '' }}>Sedang Dikerjakan</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>

            <!-- Filter Mentee -->
            <div style="flex: 1; min-width: 200px;">
                <label style="font-size: 0.8rem; font-weight: 600; color: var(--neutral-700); display: block; margin-bottom: 0.35rem;">Peserta Magang</label>
                <select name="penempatan_id" style="width: 100%; height: 40px; border: 1px solid var(--neutral-300); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff;">
                    <option value="">Semua Mentee</option>
                    @foreach($penempatanList as $p)
                        <option value="{{ $p->id }}" {{ request('penempatan_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->peserta?->nama }} ({{ $p->peserta?->profil?->institusi ?? 'Institusi' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Pencarian Kata Kunci -->
            <div style="flex: 2; min-width: 220px;">
                <label style="font-size: 0.8rem; font-weight: 600; color: var(--neutral-700); display: block; margin-bottom: 0.35rem;">Cari Judul / Topik</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul tugas atau deskripsi..." style="width: 100%; height: 40px; border: 1px solid var(--neutral-300); border-radius: 8px; padding: 0 0.85rem; font-size: 0.875rem;">
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" style="height: 40px; padding: 0 1.25rem; background: #0641CC; color: #ffffff; font-weight: 600; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer;">
                    Filter
                </button>
                @if(request()->hasAny(['status', 'penempatan_id', 'q']))
                    <a href="{{ route('pembimbing.tugas.index') }}" style="height: 40px; padding: 0 1rem; background: var(--neutral-200); color: var(--neutral-700); font-weight: 600; font-size: 0.875rem; text-decoration: none; border-radius: 8px; display: inline-flex; align-items: center;">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Daftar Tugas -->
    <div style="background: #ffffff; border-radius: 14px; border: 1px solid var(--neutral-200); box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--neutral-200); background: #f8fafc; color: var(--neutral-600); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                        <th style="padding: 1rem 1.5rem;">Tugas & Kategori</th>
                        <th style="padding: 1rem 1rem;">Peserta Magang</th>
                        <th style="padding: 1rem 1rem;">Kompleksitas</th>
                        <th style="padding: 1rem 1rem;">Tenggat Waktu</th>
                        <th style="padding: 1rem 1rem;">Status</th>
                        <th style="padding: 1rem 1.5rem; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tugasList as $tugas)
                        @php
                            $isOverdue = $tugas->deadline && $tugas->deadline->isPast() && $tugas->status->value !== 'selesai';
                            $daysLeft = $tugas->deadline ? \Carbon\Carbon::today()->diffInDays($tugas->deadline, false) : 99;
                            $isDueSoon = !$isOverdue && $daysLeft >= 0 && $daysLeft <= 3 && $tugas->status->value !== 'selesai';
                        @endphp
                        <tr style="border-bottom: 1px solid var(--neutral-100); transition: background 0.15s ease;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 1rem 1.5rem;">
                                <div style="font-weight: 600; color: var(--neutral-900); font-size: 0.925rem;">
                                    <a href="{{ route('pembimbing.tugas.show', $tugas->id) }}" style="color: inherit; text-decoration: none;">
                                        {{ $tugas->judul }}
                                    </a>
                                </div>
                                <div style="display: flex; gap: 0.4rem; align-items: center; margin-top: 0.35rem; flex-wrap: wrap;">
                                    <span style="font-size: 0.7rem; background: #e0e7ff; color: #3730a3; padding: 0.1rem 0.45rem; border-radius: 4px; font-weight: 600;">
                                        {{ $tugas->kategori }}
                                    </span>
                                    <span style="font-size: 0.75rem; color: var(--neutral-500);">
                                        &bull; {{ $tugas->progresLogs->count() }} Laporan Progres
                                    </span>
                                    @if($tugas->file_lampiran)
                                        @php $badge = $tugas->badge_color_lampiran; @endphp
                                        <a href="{{ route('pembimbing.tugas.lampiran', $tugas->id) }}" title="{{ $tugas->nama_file_lampiran }}" style="display: inline-flex; align-items: center; gap: 0.2rem; font-size: 0.675rem; font-weight: 700; padding: 0.1rem 0.4rem; border-radius: 4px; background: {{ $badge['bg'] }}; color: {{ $badge['color'] }}; text-decoration: none; border: 1px solid {{ $badge['border'] }};">
                                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                            {{ $badge['label'] }} ({{ $tugas->ukuran_format }})
                                        </a>
                                    @endif
                                    @if($tugas->link_referensi)
                                        <a href="{{ $tugas->link_referensi }}" target="_blank" rel="noopener noreferrer" style="display: inline-flex; align-items: center; gap: 0.2rem; font-size: 0.675rem; font-weight: 700; padding: 0.1rem 0.4rem; border-radius: 4px; background: #eff6ff; color: #1d4ed8; text-decoration: none; border: 1px solid #bfdbfe;">
                                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                                            Link Acuan ↗
                                        </a>
                                    @endif
                                </div>
                            </td>
                            <td style="padding: 1rem 1rem;">
                                <div style="font-weight: 500; color: var(--neutral-800);">
                                    {{ $tugas->penempatan?->peserta?->nama ?? '-' }}
                                </div>
                                <div style="font-size: 0.75rem; color: var(--neutral-500);">
                                    {{ $tugas->penempatan?->peserta?->profil?->institusi ?? '-' }}
                                </div>
                            </td>
                            <td style="padding: 1rem 1rem;">
                                <span style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-700); background: var(--neutral-100); padding: 0.2rem 0.5rem; border-radius: 4px;">
                                    {{ ucfirst($tugas->kompleksitas?->value ?? 'Sedang') }}
                                </span>
                            </td>
                            <td style="padding: 1rem 1rem;">
                                <div style="font-size: 0.825rem; font-weight: 500; color: {{ $isOverdue ? '#b91c1c' : ($isDueSoon ? '#d97706' : 'var(--neutral-800)') }};">
                                    {{ $tugas->deadline?->format('d M Y') }}
                                </div>
                                @if($isOverdue)
                                    <span style="font-size: 0.7rem; font-weight: 700; color: #b91c1c; background: #fee2e2; padding: 0.1rem 0.4rem; border-radius: 4px; display: inline-block; margin-top: 0.15rem;">
                                        Terlambat (Overdue)
                                    </span>
                                @elseif($isDueSoon)
                                    <span style="font-size: 0.7rem; font-weight: 600; color: #92400e; background: #fef3c7; padding: 0.1rem 0.4rem; border-radius: 4px; display: inline-block; margin-top: 0.15rem;">
                                        Sisa {{ $daysLeft }} Hari
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 1rem 1rem;">
                                @if($tugas->status->value === 'selesai')
                                    <span style="font-size: 0.75rem; font-weight: 600; background: #d1fae5; color: #065f46; padding: 0.2rem 0.55rem; border-radius: 999px;">
                                        Selesai
                                    </span>
                                @elseif($tugas->status->value === 'proses')
                                    <span style="font-size: 0.75rem; font-weight: 600; background: #fef3c7; color: #92400e; padding: 0.2rem 0.55rem; border-radius: 999px;">
                                        Sedang Dikerjakan
                                    </span>
                                @else
                                    <span style="font-size: 0.75rem; font-weight: 500; background: var(--neutral-100); color: var(--neutral-600); padding: 0.2rem 0.55rem; border-radius: 999px;">
                                        Belum Dikerjakan
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 1rem 1.5rem; text-align: right;">
                                <a href="{{ route('pembimbing.tugas.show', $tugas->id) }}" style="padding: 0.4rem 0.85rem; background: rgba(6, 65, 204, 0.08); color: #0641CC; font-size: 0.75rem; font-weight: 600; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;">
                                    Detail & Reviu &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 3rem 1.5rem; text-align: center; color: var(--neutral-500);">
                                Belum ada penugasan yang sesuai dengan kriteria filter saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tugasList->hasPages())
            <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--neutral-200); background: #fafafa;">
                {{ $tugasList->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
