@extends('layouts.dashboard')

@section('title', 'Periode Bulan Magang - SuperAdmin MagKom Sleman')
@section('page_title', 'Manajemen Periode Bulan Magang')
@section('page_description', 'Kelola jadwal pembukaan pendaftaran dan override status ketersediaan bulan magang.')

@section('breadcrumbs')
    <span class="breadcrumb-current">Bulan Magang</span>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <!-- Data Table Card -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-subtle);">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-400); display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-800);">Daftar Periode Bulan Magang</h2>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                <thead>
                    <tr style="background: var(--neutral-200); border-bottom: 1px solid var(--neutral-400); height: 52px;">
                        <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Bulan Magang</th>
                        <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Periode Buka Pendaftaran</th>
                        <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Override Status</th>
                        <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Status Aktif</th>
                        <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800); text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bulanList as $bulan)
                        <tr style="border-bottom: 1px solid var(--neutral-400); height: 56px;">
                            <td style="padding: 0.75rem 1rem; font-weight: 700; color: var(--blue-700);">
                                {{ $bulan->bulan_mulai?->translatedFormat('F Y') }}
                            </td>
                            <td style="padding: 0.75rem 1rem; color: var(--neutral-700);">
                                {{ $bulan->tgl_buka?->format('d M Y') }} s/d {{ $bulan->tgl_tutup?->format('d M Y') }}
                            </td>
                            <td style="padding: 0.75rem 1rem;">
                                @if($bulan->override_status)
                                    <span class="role-chip-badge" style="background: var(--blue-tint); color: var(--blue-700);">
                                        Override: {{ $bulan->override_status->value }}
                                    </span>
                                @else
                                    <span style="color: var(--neutral-500); font-size: 0.8125rem;">Otomatis (Jadwal)</span>
                                @endif
                            </td>
                            <td style="padding: 0.75rem 1rem;">
                                @if($bulan->aktif)
                                    <span class="role-chip-badge" style="background: var(--green-100); color: var(--green-700);">
                                        Aktif
                                    </span>
                                @else
                                    <span class="role-chip-badge" style="background: var(--red-100); color: var(--red-700);">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 0.75rem 1rem; text-align: right;">
                                <form method="POST" action="{{ route('superadmin.bulan.toggle', $bulan) }}" style="display: inline-block;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" style="padding: 0.375rem 0.75rem; background: var(--neutral-200); color: var(--neutral-800); border: 1px solid var(--neutral-400); border-radius: 6px; font-weight: 600; font-size: 0.75rem; cursor: pointer;">
                                        {{ $bulan->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding: 2rem; text-align: center; color: var(--neutral-600);">
                                Belum ada data periode bulan magang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bulanList->hasPages())
            <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--neutral-400);">
                {{ $bulanList->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
