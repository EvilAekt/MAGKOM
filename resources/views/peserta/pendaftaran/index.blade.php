@extends('layouts.dashboard')

@section('title', 'Pendaftaran Magang - Peserta MagKom Sleman')
@section('page_title', 'Status & Riwayat Pendaftaran Magang')
@section('page_description', 'Pantau progres pengajuan berkas magang Anda di Dinas Komunikasi dan Informatika Sleman.')

@section('breadcrumbs')
    <span class="breadcrumb-current">Pendaftaran Magang</span>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <!-- Info Banner Card -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.25rem;">Program Magang Aktif Kominfo</h2>
            <p style="font-size: 0.875rem; color: var(--neutral-600); max-width: 600px;">
                Pastikan profil biodata Anda sudah terisi lengkap sebelum mengajukan berkas ke periode bulan yang tersedia.
            </p>
        </div>
        <div>
            <a href="{{ route('peserta.profil.show') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; background: var(--blue-600); color: #FFFFFF; text-decoration: none; padding: 0.625rem 1.25rem; border-radius: 8px; font-weight: 600; font-size: 0.875rem;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <span>Periksa Profil Biodata</span>
            </a>
        </div>
    </div>

    <!-- Data Table Card -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-subtle);">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-400);">
            <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-800);">Riwayat Pengajuan Pendaftaran</h3>
        </div>

        @if($pendaftaranList->isEmpty())
            <div style="padding: 3.5rem 1.5rem; text-align: center; color: var(--neutral-600);">
                <div style="width: 56px; height: 56px; background: var(--blue-tint); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto; color: var(--blue-700);">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                        <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                    </svg>
                </div>
                <h4 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">Belum Ada Pengajuan Magang</h4>
                <p style="font-size: 0.875rem; max-width: 480px; margin: 0 auto 1.5rem auto;">
                    Anda belum memiliki riwayat pengajuan pendaftaran magang. Silakan lengkapi biodata profil Anda dan pantau ketersediaan kuota pada beranda utama.
                </p>
                <a href="{{ route('landing') }}#kuota-section" style="display: inline-flex; align-items: center; gap: 0.5rem; background: var(--neutral-200); color: var(--neutral-800); padding: 0.625rem 1.25rem; border-radius: 8px; font-weight: 600; font-size: 0.875rem; text-decoration: none;">
                    Lihat Ketersediaan Kuota Magang
                </a>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                    <thead>
                        <tr style="background: var(--neutral-200); border-bottom: 1px solid var(--neutral-400); height: 52px;">
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">No. Pendaftaran</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Periode Bulan</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Jurusan</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Status</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800); text-align: right;">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendaftaranList as $item)
                            <tr style="border-bottom: 1px solid var(--neutral-400); height: 56px;">
                                <td style="padding: 0.75rem 1rem; font-weight: 600; color: var(--blue-700);">
                                    {{ $item->nomor_pendaftaran }}
                                </td>
                                <td style="padding: 0.75rem 1rem; color: var(--neutral-800);">
                                    {{ $item->bulanMagang?->bulan_mulai?->translatedFormat('F Y') }}
                                </td>
                                <td style="padding: 0.75rem 1rem; color: var(--neutral-700);">
                                    {{ $item->kuota?->jurusan?->nama_jurusan }}
                                </td>
                                <td style="padding: 0.75rem 1rem;">
                                    <span class="role-chip-badge" style="background: var(--blue-tint); color: var(--blue-700);">
                                        {{ $item->status->label() }}
                                    </span>
                                </td>
                                <td style="padding: 0.75rem 1rem; text-align: right;">
                                    <a href="{{ route('peserta.pendaftaran.detail', $item) }}" style="padding: 0.375rem 0.75rem; background: var(--blue-600); color: #FFFFFF; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 0.8125rem;">
                                        Lihat Status
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
