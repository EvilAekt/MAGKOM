@extends('layouts.dashboard')

@section('title', 'Verifikasi Pendaftaran - Admin MagKom Sleman')
@section('page_title', 'Verifikasi Berkas Pendaftaran')
@section('page_description', 'Tinjau dan putuskan pengajuan berkas pendaftaran calon peserta magang.')

@section('breadcrumbs')
    <span class="breadcrumb-current">Verifikasi Pendaftaran</span>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <!-- Filter Bar Card -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle);">
        <form method="GET" action="{{ route('admin.pendaftaran.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="display: flex; flex-direction: column; gap: 0.375rem; flex: 1; min-width: 200px;">
                <label style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">Filter Periode Bulan</label>
                <select name="bulan_magang_id" style="height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: var(--neutral-0);">
                    <option value="">Semua Periode Bulan</option>
                    @foreach($bulanList as $bulan)
                        <option value="{{ $bulan->id }}" {{ request('bulan_magang_id') == $bulan->id ? 'selected' : '' }}>
                            {{ $bulan->bulan_mulai?->translatedFormat('F Y') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.375rem; flex: 1; min-width: 200px;">
                <label style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">Filter Status Berkas</label>
                <select name="status" style="height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: var(--neutral-0);">
                    <option value="">Semua Status</option>
                    @foreach($statusEnum as $status)
                        <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" style="height: 42px; padding: 0 1.25rem; background: var(--blue-600); color: #FFFFFF; font-weight: 600; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer; transition: background 0.15s;">
                    Terapkan Filter
                </button>
                <a href="{{ route('admin.pendaftaran.index') }}" style="height: 42px; padding: 0 1rem; background: var(--neutral-200); color: var(--neutral-700); font-weight: 600; font-size: 0.875rem; text-decoration: none; border-radius: 8px; display: inline-flex; align-items: center;">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Data Table Card -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-subtle);">
        @if($pendaftaranList->isEmpty())
            <div style="padding: 3.5rem 1.5rem; text-align: center; color: var(--neutral-600);">
                <div style="width: 56px; height: 56px; background: var(--neutral-200); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto; color: var(--neutral-500);">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
                <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">Belum Ada Pengajuan Pendaftaran</h3>
                <p style="font-size: 0.875rem; max-width: 460px; margin: 0 auto;">Saat ini belum ada berkas pendaftaran calon peserta magang yang perlu diverifikasi pada filter yang dipilih.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                    <thead>
                        <tr style="background: var(--neutral-200); border-bottom: 1px solid var(--neutral-400); height: 52px;">
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">No. Pendaftaran</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Nama Calon Peserta</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Asal Instansi & Jurusan</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Periode Magang</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Status</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800); text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendaftaranList as $item)
                            <tr style="border-bottom: 1px solid var(--neutral-400); height: 56px;">
                                <td style="padding: 0.75rem 1rem; font-weight: 600; color: var(--blue-700);">
                                    {{ $item->nomor_pendaftaran }}
                                </td>
                                <td style="padding: 0.75rem 1rem; font-weight: 600; color: var(--neutral-800);">
                                    {{ $item->user?->nama }}
                                </td>
                                <td style="padding: 0.75rem 1rem; color: var(--neutral-700);">
                                    <div>{{ $item->asal_instansi }}</div>
                                    <small style="color: var(--neutral-600);">{{ $item->kuota?->jurusan?->nama_jurusan }}</small>
                                </td>
                                <td style="padding: 0.75rem 1rem; color: var(--neutral-700);">
                                    {{ $item->bulanMagang?->bulan_mulai?->translatedFormat('F Y') }}
                                </td>
                                <td style="padding: 0.75rem 1rem;">
                                    <span class="role-chip-badge" style="background: var(--neutral-200); color: var(--neutral-700);">
                                        {{ $item->status->label() }}
                                    </span>
                                </td>
                                <td style="padding: 0.75rem 1rem; text-align: right;">
                                    <a href="{{ route('admin.pendaftaran.show', $item) }}" style="padding: 0.375rem 0.75rem; background: var(--blue-600); color: #FFFFFF; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 0.8125rem;">
                                        Tinjau Berkas
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($pendaftaranList->hasPages())
                <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--neutral-400);">
                    {{ $pendaftaranList->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
