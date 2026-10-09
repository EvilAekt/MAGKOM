@extends('layouts.dashboard')

@section('title', 'Daftar Tugas Magang - Peserta MagKom')
@section('page_title', 'Tugas & Proyek Magang')
@section('page_description', 'Kelola penugasan proyek yang diberikan oleh pembimbing lapangan Anda.')

@section('breadcrumbs')
    <span class="breadcrumb-current">Tugas & Proyek</span>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <!-- Info Banner -->
    <div style="background: var(--blue-50); border: 1px solid var(--blue-200); border-radius: 12px; padding: 1.25rem 1.5rem; display: flex; gap: 1rem; align-items: flex-start;">
        <div style="width: 36px; height: 36px; border-radius: 8px; background: var(--blue-600); color: #FFFFFF; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <polyline points="10 9 9 9 8 9"></polyline>
            </svg>
        </div>
        <div>
            <h3 style="font-size: 0.9375rem; font-weight: 700; color: var(--blue-900); margin: 0 0 0.25rem 0;">Informasi Pelaporan Tugas</h3>
            <p style="font-size: 0.875rem; color: var(--blue-800); margin: 0; line-height: 1.5;">
                Selesaikan tugas sebelum tenggat waktu yang ditentukan. Anda dapat mengunggah laporan kemajuan berkala pada masing-masing tugas untuk ditinjau oleh pembimbing lapangan.
            </p>
        </div>
    </div>

    <!-- Data List Card -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-subtle);">
        @if($tugasList->isEmpty())
            <div style="padding: 4rem 1.5rem; text-align: center; color: var(--neutral-600);">
                <div style="width: 64px; height: 64px; background: var(--neutral-200); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem auto; color: var(--neutral-500);">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
                <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-900); margin: 0 0 0.5rem 0;">Belum Ada Tugas Diberikan</h3>
                <p style="font-size: 0.875rem; color: var(--neutral-600); margin: 0; max-width: 480px; margin-inline: auto;">
                    Pembimbing lapangan Anda belum memberikan penugasan baru. Silakan berkoordinasi langsung dengan pembimbing Anda.
                </p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem; text-align: left;">
                    <thead>
                        <tr style="background: var(--neutral-100); border-bottom: 1px solid var(--neutral-400); color: var(--neutral-700); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <th style="padding: 1rem 1.25rem; font-weight: 700;">Judul Tugas & Deskripsi</th>
                            <th style="padding: 1rem 1rem; font-weight: 700;">Kategori / Kompleksitas</th>
                            <th style="padding: 1rem 1rem; font-weight: 700;">Tenggat Waktu</th>
                            <th style="padding: 1rem 1rem; font-weight: 700;">Status</th>
                            <th style="padding: 1rem 1rem; font-weight: 700;">Progres Laporan</th>
                            <th style="padding: 1rem 1.25rem; font-weight: 700; text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody style="divide-y: 1px solid var(--neutral-300);">
                        @foreach($tugasList as $item)
                            @php
                                $isOverdue = $item->deadline->isPast() && $item->status->value !== 'selesai';
                                $statusBadgeBg = match($item->status->value) {
                                    'belum' => 'var(--neutral-200)',
                                    'proses' => 'var(--yellow-50)',
                                    'selesai' => 'var(--green-50)',
                                    default => 'var(--neutral-200)'
                                };
                                $statusBadgeText = match($item->status->value) {
                                    'belum' => 'var(--neutral-700)',
                                    'proses' => 'var(--yellow-800)',
                                    'selesai' => 'var(--green-800)',
                                    default => 'var(--neutral-700)'
                                };
                            @endphp
                            <tr style="border-bottom: 1px solid var(--neutral-300); transition: background 0.15s;" onmouseover="this.style.background='var(--neutral-100)'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 1.125rem 1.25rem; max-width: 320px;">
                                    <div style="font-weight: 700; color: var(--neutral-900); font-size: 0.9375rem; margin-bottom: 0.25rem;">
                                        {{ $item->judul }}
                                    </div>
                                    <div style="color: var(--neutral-600); font-size: 0.8125rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4;">
                                        {{ $item->deskripsi }}
                                    </div>
                                </td>
                                <td style="padding: 1.125rem 1rem; white-space: nowrap;">
                                    <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                                        <span style="font-weight: 600; color: var(--neutral-800);">{{ is_object($item->kategori) ? ($item->kategori->label() ?? $item->kategori->value) : $item->kategori }}</span>
                                        <span style="font-size: 0.75rem; color: var(--neutral-600);">Tingkat: {{ ucfirst($item->kompleksitas->value ?? 'Sedang') }}</span>
                                    </div>
                                </td>
                                <td style="padding: 1.125rem 1rem; white-space: nowrap;">
                                    <div style="font-weight: 600; color: {{ $isOverdue ? 'var(--red-700)' : 'var(--neutral-900)' }};">
                                        {{ $item->deadline->translatedFormat('d M Y') }}
                                    </div>
                                    <div style="font-size: 0.75rem; font-weight: 600; color: {{ $isOverdue ? 'var(--red-700)' : 'var(--neutral-600)' }};">
                                        @if($isOverdue)
                                            <span style="display: inline-flex; align-items: center; gap: 0.25rem;">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                                Terlambat {{ $item->deadline->diffForHumans() }}
                                            </span>
                                        @else
                                            {{ $item->deadline->diffForHumans() }}
                                        @endif
                                    </div>
                                </td>
                                <td style="padding: 1.125rem 1rem; white-space: nowrap;">
                                    <span style="display: inline-block; padding: 0.25rem 0.625rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: {{ $statusBadgeBg }}; color: {{ $statusBadgeText }};">
                                        {{ $item->status->label() }}
                                    </span>
                                </td>
                                <td style="padding: 1.125rem 1rem; white-space: nowrap;">
                                    <span style="display: inline-flex; align-items: center; gap: 0.375rem; font-weight: 600; color: var(--neutral-700);">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                        {{ $item->progresLogs->count() }} Laporan
                                    </span>
                                </td>
                                <td style="padding: 1.125rem 1.25rem; text-align: right; white-space: nowrap;">
                                    <a href="{{ route('peserta.tugas.show', $item) }}" style="display: inline-flex; align-items: center; gap: 0.375rem; height: 36px; padding: 0 0.875rem; background: var(--blue-600); color: #FFFFFF; font-weight: 600; font-size: 0.8125rem; text-decoration: none; border-radius: 6px; transition: background 0.15s;">
                                        Lihat Detail
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="9 18 15 12 9 6"></polyline>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($tugasList->hasPages())
                <div style="padding: 1rem 1.25rem; border-top: 1px solid var(--neutral-300);">
                    {{ $tugasList->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
