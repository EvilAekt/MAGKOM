@extends('layouts.dashboard')

@section('title', 'Arsip Peserta Magang - SuperAdmin MagKom')
@section('page_title', 'Arsip & Rekam Jejak Alumni Magang')
@section('page_description', 'Bank data historis peserta magang selesai dan nonaktif secara baca-saja (AC-12-4, PRD 9.2).')

@section('breadcrumbs')
    <span class="breadcrumb-current">Arsip Peserta</span>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">

    <!-- Toolbar & Filter Card -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <form method="GET" action="{{ route('superadmin.arsip.index') }}" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; flex: 1;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, NIM, atau institusi..." style="height: 40px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; min-width: 260px;">

            <select name="status" style="height: 40px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: var(--neutral-0);">
                <option value="">Semua Status Arsip</option>
                <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai Magang (Masa Tenggang)</option>
                <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Akun Nonaktif (Arsip Final)</option>
                <option value="mengundurkan_diri" {{ request('status') === 'mengundurkan_diri' ? 'selected' : '' }}>Mengundurkan Diri</option>
            </select>

            <button type="submit" style="height: 40px; padding: 0 1.25rem; background: var(--blue-600); color: #FFFFFF; font-weight: 600; font-size: 0.8125rem; border: none; border-radius: 8px; cursor: pointer;">
                Cari Data
            </button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('superadmin.arsip.index') }}" style="font-size: 0.8125rem; color: var(--neutral-600); text-decoration: none; padding: 0.5rem;">Reset</a>
            @endif
        </form>

        <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; color: var(--neutral-600);">
            <span class="role-chip-badge" style="background: var(--blue-tint); color: var(--blue-700); font-weight: 700;">Strictly Read-Only</span>
        </div>
    </div>

    <!-- Data Table Card -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-subtle);">
        @if($arsipList->isEmpty())
            <div style="padding: 4rem 1.5rem; text-align: center; color: var(--neutral-600);">
                <div style="width: 48px; height: 48px; border-radius: 50%; background: var(--neutral-200); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto; color: var(--neutral-600);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="21 8 21 21 3 21 3 8"></polyline><rect x="1" y="3" width="22" height="5"></rect><line x1="10" y1="12" x2="14" y2="12"></line></svg>
                </div>
                <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-800); margin: 0 0 0.25rem 0;">Tidak Ada Data Arsip</h3>
                <p style="font-size: 0.8125rem; margin: 0;">Belum ada peserta dengan status magang selesai, akun nonaktif, atau mengundurkan diri yang tersimpan.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem; text-align: left;">
                    <thead>
                        <tr style="background: var(--neutral-100); border-bottom: 1px solid var(--neutral-400); color: var(--neutral-700); font-size: 0.75rem; text-transform: uppercase;">
                            <th style="padding: 1rem 1.25rem; font-weight: 700;">Peserta & Institusi</th>
                            <th style="padding: 1rem 1rem; font-weight: 700;">Jurusan / Bidang</th>
                            <th style="padding: 1rem 1rem; font-weight: 700;">Periode Pelaksanaan</th>
                            <th style="padding: 1rem 1rem; font-weight: 700;">Pembimbing Lapangan</th>
                            <th style="padding: 1rem 1rem; font-weight: 700; text-align: center;">Nilai & Predikat</th>
                            <th style="padding: 1rem 1rem; font-weight: 700;">Status Arsip</th>
                            <th style="padding: 1rem 1.25rem; font-weight: 700; text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($arsipList as $item)
                            <tr style="border-bottom: 1px solid var(--neutral-300); transition: background 0.15s;" onmouseover="this.style.background='var(--neutral-100)'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 1rem 1.25rem;">
                                    <div style="font-weight: 700; color: var(--neutral-900);">{{ $item->peserta?->nama }}</div>
                                    <div style="font-size: 0.75rem; color: var(--neutral-600);">{{ $item->peserta?->email }}</div>
                                    <small style="font-size: 0.75rem; color: var(--blue-700); font-weight: 600;">
                                        {{ $item->peserta?->profil?->institusi ?? '-' }} (NIM: {{ $item->peserta?->profil?->nim_nisn ?? '-' }})
                                    </small>
                                </td>

                                <td style="padding: 1rem 1rem;">
                                    <div style="font-weight: 600; color: var(--neutral-800);">
                                        {{ $item->kuota?->jurusan?->nama_jurusan ?? ($item->slotKhusus ? 'Jalur Khusus Mitra' : '-') }}
                                    </div>
                                    <small style="color: var(--neutral-600);">{{ $item->bidang?->nama_bidang ?? 'Bidang Umum' }}</small>
                                </td>

                                <td style="padding: 1rem 1rem; font-size: 0.8125rem;">
                                    <div><strong>Mulai:</strong> {{ $item->tgl_mulai?->translatedFormat('d F Y') }}</div>
                                    <div><strong>Selesai:</strong> {{ $item->tgl_selesai?->translatedFormat('d F Y') }}</div>
                                </td>

                                <td style="padding: 1rem 1rem;">
                                    <div style="font-weight: 600; color: var(--neutral-800);">{{ $item->pembimbing?->nama ?? '-' }}</div>
                                    <small style="color: var(--neutral-600);">{{ $item->pembimbing?->email }}</small>
                                </td>

                                <td style="padding: 1rem 1rem; text-align: center;">
                                    @if($item->penilaian)
                                         <div style="font-size: 1.125rem; font-weight: 900; color: var(--blue-700);">
                                             {{ $item->penilaian->nilai_akhir }}
                                         </div>
                                         <span class="role-chip-badge" style="background: var(--green-100); color: var(--green-800); border: 1px solid var(--green-600); font-weight: 800;">
                                             Predikat {{ $item->penilaian->predikat }}
                                         </span>
                                    @else
                                        <span style="color: var(--neutral-500); font-size: 0.8125rem;">-</span>
                                    @endif
                                </td>

                                <td style="padding: 1rem 1rem;">
                                    @if($item->status?->value === 'selesai')
                                        <span class="role-chip-badge" style="background: var(--sky-100, #E0F2FE); color: var(--blue-700); border: 1px solid var(--blue-300);">
                                            Selesai Magang
                                        </span>
                                    @elseif($item->status?->value === 'mengundurkan_diri')
                                        <span class="role-chip-badge" style="background: var(--red-100, #FEE2E2); color: var(--red-700, #B91C1C); border: 1px solid var(--red-300, #FCA5A5); font-weight: 700;">
                                            Mengundurkan Diri
                                        </span>
                                    @else
                                        <span class="role-chip-badge" style="background: var(--neutral-200); color: var(--neutral-700); border: 1px solid var(--neutral-400);">
                                            Nonaktif (Arsip)
                                        </span>
                                    @endif
                                </td>

                                <td style="padding: 1rem 1.25rem; text-align: right;">
                                    <a href="{{ route('superadmin.arsip.show', $item) }}" style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.45rem 0.875rem; background: var(--blue-600); color: #FFFFFF; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 0.8125rem; box-shadow: var(--shadow-subtle);">
                                        <span>Detail Arsip</span>
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($arsipList->hasPages())
                <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--neutral-300);">
                    {{ $arsipList->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
