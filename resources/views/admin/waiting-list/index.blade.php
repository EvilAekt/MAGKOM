@extends('layouts.dashboard')

@section('title', 'Antrean Waiting List - Admin MagKom Sleman')
@section('page_title', 'Antrean Waiting List (Daftar Tunggu)')
@section('page_description', 'Kelola pendaftaran calon peserta saat kuota jurusan penuh berdasarkan urutan waktu pengajuan (FIFO).')

@section('breadcrumbs')
    <span class="breadcrumb-current">Antrean Waiting List</span>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">

    <!-- Info Banner Card -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; align-items: center; gap: 0.875rem;">
            <div style="width: 42px; height: 42px; background: var(--blue-tint); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--blue-700); flex-shrink: 0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
            </div>
            <div>
                <h2 style="font-size: 1rem; font-weight: 700; color: var(--neutral-800); margin: 0 0 0.125rem 0;">Prinsip Antrean Tunggu FIFO</h2>
                <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0;">
                    Sesuai aturan bisnis BR-05, peserta diurutkan ketat berdasarkan stempel waktu submit berkas. Jika kuota terbuka, peserta teratas diproses terlebih dahulu.
                </p>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-subtle);">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-400); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-800); margin: 0;">Daftar Antrean Calon Peserta</h3>
            <span style="font-size: 0.8125rem; color: var(--neutral-600);">Total: {{ $waitingList->total() }} Calon Peserta</span>
        </div>

        @if($waitingList->isEmpty())
            <div style="padding: 3.5rem 1.5rem; text-align: center; color: var(--neutral-600);">
                <div style="width: 56px; height: 56px; background: var(--neutral-200); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto; color: var(--neutral-500);">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <h4 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">Tidak Ada Antrean Waiting List</h4>
                <p style="font-size: 0.875rem; max-width: 460px; margin: 0 auto;">Saat ini tidak ada pendaftaran calon peserta magang yang tertahan dalam antrean tunggu.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                    <thead>
                        <tr style="background: var(--neutral-200); border-bottom: 1px solid var(--neutral-400); height: 52px;">
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800); width: 60px;">No.</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">No. Pendaftaran</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Nama Calon Peserta</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Asal Instansi & Jurusan</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Periode Bulan</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Waktu Submit (FIFO)</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800); text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($waitingList as $index => $item)
                            <tr style="border-bottom: 1px solid var(--neutral-400); height: 56px;">
                                <td style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-700);">
                                    {{ $waitingList->firstItem() + $index }}
                                </td>
                                <td style="padding: 0.75rem 1rem; font-weight: 600; color: var(--blue-700);">
                                    {{ $item->nomor_pendaftaran }}
                                </td>
                                <td style="padding: 0.75rem 1rem; font-weight: 600; color: var(--neutral-800);">
                                    {{ $item->user?->nama }}
                                </td>
                                <td style="padding: 0.75rem 1rem; color: var(--neutral-700);">
                                    <div>{{ $item->asal_instansi ?? ($item->user?->profil?->institusi ?? '-') }}</div>
                                    <small style="color: var(--neutral-600);">{{ $item->kuota?->jurusan?->nama_jurusan }}</small>
                                </td>
                                <td style="padding: 0.75rem 1rem; color: var(--neutral-800); font-weight: 500;">
                                    {{ $item->bulanMagang?->bulan_mulai?->translatedFormat('F Y') }}
                                </td>
                                <td style="padding: 0.75rem 1rem; color: var(--neutral-700); font-family: monospace; font-size: 0.8125rem;">
                                    {{ $item->diajukan_at ? $item->diajukan_at->format('d/m/Y H:i:s') : '-' }}
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

            @if($waitingList->hasPages())
                <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--neutral-400);">
                    {{ $waitingList->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
