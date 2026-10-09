@extends('layouts.dashboard')

@section('title', 'Penugasan Peserta - Pembimbing MagKom')
@section('page_title', 'Manajemen Penugasan & Progres')
@section('page_description', 'Kelola penugasan proyek dan tinjau laporan perkembangan peserta magang bimbingan.')

@section('breadcrumbs')
    <span class="breadcrumb-current">Manajemen Penugasan</span>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <!-- Filter & Toolbar Bar -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle);">
        <form method="GET" action="{{ route('pembimbing.tugas.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="display: flex; flex-direction: column; gap: 0.375rem; flex: 1; min-width: 220px;">
                <label style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">Filter Status Tugas</label>
                <select name="status" style="height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: var(--neutral-0);">
                    <option value="">Semua Status Tugas</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="diberikan" {{ request('status') === 'diberikan' ? 'selected' : '' }}>Diberikan</option>
                    <option value="dikerjakan" {{ request('status') === 'dikerjakan' ? 'selected' : '' }}>Sedang Dikerjakan</option>
                    <option value="menunggu_review" {{ request('status') === 'menunggu_review' ? 'selected' : '' }}>Menunggu Review</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="dibatalkan" {{ request('status') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" style="height: 42px; padding: 0 1.25rem; background: var(--blue-600); color: #FFFFFF; font-weight: 600; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer; transition: background 0.15s;">
                    Terapkan Filter
                </button>
                <a href="{{ route('pembimbing.tugas.index') }}" style="height: 42px; padding: 0 1rem; background: var(--neutral-200); color: var(--neutral-700); font-weight: 600; font-size: 0.875rem; text-decoration: none; border-radius: 8px; display: inline-flex; align-items: center;">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Data Table Card -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-subtle);">
        @if($tugasList->isEmpty())
            <div style="padding: 3.5rem 1.5rem; text-align: center; color: var(--neutral-600);">
                <div style="width: 56px; height: 56px; background: var(--neutral-200); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto; color: var(--neutral-500);">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                </div>
                <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">Belum Ada Tugas Diberikan</h3>
                <p style="font-size: 0.875rem; max-width: 480px; margin: 0 auto;">Saat ini belum ada data penugasan untuk peserta magang bimbingan Anda. Tugas yang dibuat akan muncul di tabel ini.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                    <thead>
                        <tr style="background: var(--neutral-200); border-bottom: 1px solid var(--neutral-400); height: 52px;">
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Judul Tugas</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Peserta Magang</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Bidang Penempatan</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Batas Waktu</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Status</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800); text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tugasList as $tugas)
                            <tr style="border-bottom: 1px solid var(--neutral-400); height: 56px;">
                                <td style="padding: 0.75rem 1rem; font-weight: 600; color: var(--neutral-800);">
                                    <div>{{ $tugas->judul }}</div>
                                    <small style="color: var(--neutral-600); font-weight: 400;">{{ Str::limit($tugas->deskripsi, 50) }}</small>
                                </td>
                                <td style="padding: 0.75rem 1rem; color: var(--neutral-800); font-weight: 500;">
                                    {{ $tugas->penempatan?->peserta?->nama ?? '-' }}
                                </td>
                                <td style="padding: 0.75rem 1rem; color: var(--neutral-700);">
                                    {{ $tugas->penempatan?->bidang?->nama_bidang ?? '-' }}
                                </td>
                                <td style="padding: 0.75rem 1rem; color: var(--neutral-700);">
                                    {{ $tugas->deadline ? \Carbon\Carbon::parse($tugas->deadline)->translatedFormat('d M Y') : '-' }}
                                </td>
                                <td style="padding: 0.75rem 1rem;">
                                    <span class="role-chip-badge" style="background: var(--blue-100); color: var(--blue-700);">
                                        {{ ucfirst(str_replace('_', ' ', is_object($tugas->status) ? $tugas->status->value : $tugas->status)) }}
                                    </span>
                                </td>
                                <td style="padding: 0.75rem 1rem; text-align: right;">
                                    <span style="font-size: 0.8125rem; color: var(--neutral-600);">
                                        {{ $tugas->progresLogs->count() }} Laporan
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($tugasList->hasPages())
                <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--neutral-400);">
                    {{ $tugasList->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
