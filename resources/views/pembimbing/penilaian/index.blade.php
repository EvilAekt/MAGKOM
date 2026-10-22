@extends('layouts.dashboard')

@section('title', 'Penilaian Magang - Portal MagKom')
@section('page_title', 'Evaluasi Rubrik & Penilaian Magang')
@section('page_description', 'Kelola lembar evaluasi kompetensi rubrik, catat riwayat revisi nilai, dan tetapkan kelulusan magang.')

@section('breadcrumbs')
    <nav style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; color: var(--neutral-600, #64748B);">
        <a href="{{ route('pembimbing.dashboard') }}" style="color: var(--neutral-600, #64748B); text-decoration: none;">Dashboard</a>
        <span>/</span>
        <span style="color: var(--neutral-900, #0F172A); font-weight: 700;">Penilaian Magang</span>
    </nav>
@endsection

@section('page_actions')
    <div style="display: flex; gap: 0.75rem;">
        <a href="{{ route('pembimbing.penilaian.ekspor-bidang') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; height: 38px; padding: 0 1.15rem; background: #0641CC; color: #ffffff; font-weight: 600; font-size: 0.8125rem; border-radius: 8px; text-decoration: none; box-shadow: 0 1px 3px rgba(6, 65, 204, 0.25);">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            <span>Ekspor Rekap Bimbingan (CSV)</span>
        </a>
    </div>
@endsection

@section('content')
<div class="dashboard-content" style="display: flex; flex-direction: column; gap: 1.5rem;">


    <!-- Info Banner Panduan -->
    <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-left: 4px solid #0641CC; border-radius: 12px; padding: 1.25rem 1.5rem; display: flex; gap: 1rem; align-items: flex-start;">
        <div style="width: 36px; height: 36px; border-radius: 8px; background: #0641CC; color: #ffffff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
        </div>
        <div>
            <h3 style="font-size: 0.95rem; font-weight: 700; color: #1e3a8a; margin: 0 0 0.25rem 0;">Petunjuk Evaluasi Rubrik Resmi Diskominfo Sleman</h3>
            <p style="font-size: 0.85rem; color: #1e40af; margin: 0; line-height: 1.5;">
                Nilai akhir dikalkulasi otomatis berbobot 100% dari 5 kriteria rubrik aktif. Status Draf belum tampil ke peserta. Status Final mengunci nilai resmi, dan setiap perubahan selanjutnya wajib menyertakan alasan revisi minimal 10 karakter untuk jejak audit (AC-10-3).
            </p>
        </div>
    </div>

    <!-- Filter Bar Card -->
    <div class="app-card" style="background: var(--neutral-0); border-radius: 14px; border: 1px solid var(--neutral-200); padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <form method="GET" action="{{ route('pembimbing.penilaian.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            
            <!-- Filter Status Penilaian -->
            <div style="flex: 1; min-width: 180px;">
                <label style="font-size: 0.8rem; font-weight: 600; color: var(--neutral-700); display: block; margin-bottom: 0.35rem;">Status Nilai</label>
                <select name="status_nilai" style="width: 100%; height: 40px; border: 1px solid var(--neutral-300); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff;">
                    <option value="">Semua Status Nilai</option>
                    <option value="belum" {{ request('status_nilai') === 'belum' ? 'selected' : '' }}>Belum Dinilai</option>
                    <option value="draft" {{ request('status_nilai') === 'draft' ? 'selected' : '' }}>Draf Tersimpan</option>
                    <option value="final" {{ request('status_nilai') === 'final' ? 'selected' : '' }}>Final Resmi</option>
                </select>
            </div>

            <!-- Filter Status Penempatan -->
            <div style="flex: 1; min-width: 180px;">
                <label style="font-size: 0.8rem; font-weight: 600; color: var(--neutral-700); display: block; margin-bottom: 0.35rem;">Status Magang</label>
                <select name="status_penempatan" style="width: 100%; height: 40px; border: 1px solid var(--neutral-300); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff;">
                    <option value="">Semua Status Magang</option>
                    <option value="aktif" {{ request('status_penempatan') === 'aktif' ? 'selected' : '' }}>Aktif Berjalan</option>
                    <option value="selesai" {{ request('status_penempatan') === 'selesai' ? 'selected' : '' }}>Selesai Ditetapkan</option>
                </select>
            </div>

            <!-- Pencarian Peserta -->
            <div style="flex: 2; min-width: 220px;">
                <label style="font-size: 0.8rem; font-weight: 600; color: var(--neutral-700); display: block; margin-bottom: 0.35rem;">Cari Mentee</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau institusi..." style="width: 100%; height: 40px; border: 1px solid var(--neutral-300); border-radius: 8px; padding: 0 0.85rem; font-size: 0.875rem;">
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" style="height: 40px; padding: 0 1.25rem; background: #0641CC; color: #ffffff; font-weight: 600; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer;">
                    Filter
                </button>
                @if(request()->hasAny(['status_nilai', 'status_penempatan', 'q']))
                    <a href="{{ route('pembimbing.penilaian.index') }}" style="height: 40px; padding: 0 1rem; background: var(--neutral-200); color: var(--neutral-700); font-weight: 600; font-size: 0.875rem; text-decoration: none; border-radius: 8px; display: inline-flex; align-items: center;">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Data Table Card -->
    <x-gentelella.x-panel title="Daftar Evaluasi Nilai Mentee" subtitle="Rekap status draf, finalisasi rubrik, dan riwayat perbaikan nilai">
        @if($penempatanList->isEmpty())
            <div style="padding: 4rem 1.5rem; text-align: center; color: var(--neutral-500);">
                <div style="width: 64px; height: 64px; background: var(--neutral-100); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem auto; color: var(--neutral-400);">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
                <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--neutral-800); margin: 0 0 0.5rem 0;">Belum Ada Data Peserta</h3>
                <p style="font-size: 0.85rem; color: var(--neutral-500); margin: 0;">
                    Belum ada data peserta bimbingan yang sesuai dengan filter pencarian Anda.
                </p>
            </div>
        @else
            <div class="table-responsive-gentelella" style="overflow-x: auto;">
                <table class="table jambo_table bulk_action" style="width: 100%; border-collapse: collapse; font-size: 0.875rem; text-align: left;">
                    <thead>
                        <tr class="headings" style="border-bottom: 1px solid var(--neutral-200); background: #f8fafc; color: var(--neutral-600); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <th style="padding: 1rem 1.5rem;">Peserta Magang</th>
                            <th style="padding: 1rem 1rem;">Institusi & Jurusan</th>
                            <th style="padding: 1rem 1rem;">Status Magang</th>
                            <th style="padding: 1rem 1rem;">Status Evaluasi</th>
                            <th style="padding: 1rem 1rem;">Nilai Akhir & Predikat</th>
                            <th style="padding: 1rem 1.5rem; text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($penempatanList as $item)
                            @php
                                $penilaian = $item->penilaian;
                                $statusNilai = $penilaian ? $penilaian->status->value : 'belum';
                                $badgeBg = match($statusNilai) {
                                    'final' => '#d1fae5',
                                    'draft' => '#fef3c7',
                                    default => 'var(--neutral-100)'
                                };
                                $badgeText = match($statusNilai) {
                                    'final' => '#065f46',
                                    'draft' => '#92400e',
                                    default => 'var(--neutral-600)'
                                };
                                $badgeLabel = match($statusNilai) {
                                    'final' => 'Final Resmi',
                                    'draft' => 'Draf Tersimpan',
                                    default => 'Belum Dinilai'
                                };
                            @endphp
                            <tr style="border-bottom: 1px solid var(--neutral-100); transition: background 0.15s ease;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 1.1rem 1.5rem;">
                                    <div style="font-weight: 700; color: var(--neutral-900); font-size: 0.925rem;">
                                        {{ $item->peserta?->nama ?? '-' }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--neutral-500);">
                                        {{ $item->peserta?->email ?? '-' }}
                                    </div>
                                </td>
                                <td style="padding: 1.1rem 1rem;">
                                    <div style="font-weight: 500; color: var(--neutral-800);">
                                        {{ $item->peserta?->profil?->institusi ?? '-' }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--neutral-500);">
                                        {{ $item->peserta?->profil?->jurusan?->nama_jurusan ?? '-' }}
                                    </div>
                                </td>
                                <td style="padding: 1.1rem 1rem;">
                                    <span style="font-size: 0.75rem; font-weight: 600; padding: 0.15rem 0.5rem; border-radius: 999px; background: {{ $item->status->value === 'aktif' ? '#e0e7ff' : '#f3f4f6' }}; color: {{ $item->status->value === 'aktif' ? '#3730a3' : '#374151' }};">
                                        {{ ucfirst($item->status->value) }}
                                    </span>
                                </td>
                                <td style="padding: 1.1rem 1rem;">
                                    <span style="display: inline-block; padding: 0.25rem 0.6rem; border-radius: 999px; font-size: 0.75rem; font-weight: 700; background: {{ $badgeBg }}; color: {{ $badgeText }};">
                                        {{ $badgeLabel }}
                                    </span>
                                </td>
                                <td style="padding: 1.1rem 1rem;">
                                    @if($penilaian && $penilaian->nilai_akhir !== null)
                                        <div style="display: flex; align-items: baseline; gap: 0.4rem;">
                                            <span style="font-size: 1.15rem; font-weight: 800; color: var(--neutral-900);">
                                                {{ number_format((float) $penilaian->nilai_akhir, 2) }}
                                            </span>
                                            <span style="font-size: 0.8rem; font-weight: 700; color: #0641CC;">
                                                ({{ $penilaian->predikat->value ?? $penilaian->predikat }})
                                            </span>
                                        </div>
                                    @else
                                        <span style="color: var(--neutral-400); font-size: 0.8125rem;">-</span>
                                    @endif
                                </td>
                                <td style="padding: 1.1rem 1.5rem; text-align: right;">
                                    <div style="display: inline-flex; gap: 0.4rem; align-items: center;">
                                        @if($penilaian && $penilaian->status->value === 'final')
                                            <a href="{{ route('pembimbing.penilaian.unduh', $item->id) }}" target="_blank" title="Unduh Lembar Nilai PDF" style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border: 1px solid var(--neutral-300); background: #ffffff; color: var(--neutral-700); border-radius: 6px; text-decoration: none;">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                    <polyline points="7 10 12 15 17 10"></polyline>
                                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                                </svg>
                                            </a>
                                        @endif
                                        <a href="{{ route('pembimbing.penilaian.show', $item->id) }}" style="display: inline-flex; align-items: center; gap: 0.35rem; height: 34px; padding: 0 0.85rem; background: #0641CC; color: #ffffff; font-weight: 600; font-size: 0.8rem; text-decoration: none; border-radius: 6px;">
                                            {{ $penilaian ? 'Buka Nilai' : 'Input Nilai' }} &rarr;
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($penempatanList->hasPages())
                <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--neutral-200); background: #fafafa;">
                    {{ $penempatanList->links() }}
                </div>
            @endif
        @endif
    </x-gentelella.x-panel>
</div>
@endsection
