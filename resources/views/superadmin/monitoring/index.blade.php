@extends('layouts.dashboard')

@section('title', 'Monitoring Pelaksanaan Magang - SuperAdmin MagKom')
@section('page_title', 'Monitoring Pelaksanaan Magang Aktif')
@section('page_description', 'Pantau seluruh aktivitas penugasan, logbook harian, dan proses bimbingan peserta aktif secara baca-saja (PRD 3.3).')

@section('breadcrumbs')
    <span class="breadcrumb-current">Monitoring Peserta Aktif</span>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">

    <!-- Filter & Search Card -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle);">
        <form method="GET" action="{{ route('superadmin.monitoring.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 1; min-width: 220px;">
                <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Cari Peserta / NIM / Institusi
                </label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama, email, NIM, atau kampus..." style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
            </div>

            <div style="min-width: 180px;">
                <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Pembimbing Lapangan
                </label>
                <select name="pembimbing_id" style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                    <option value="">Semua Pembimbing</option>
                    @foreach($pembimbingList as $p)
                        <option value="{{ $p->id }}" {{ request('pembimbing_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="min-width: 180px;">
                <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Jurusan
                </label>
                <select name="jurusan_id" style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                    <option value="">Semua Jurusan</option>
                    @foreach($jurusanList as $j)
                        <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>
                            {{ $j->nama_jurusan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" style="height: 38px; padding: 0 1.25rem; background: var(--blue-600); color: #fff; border: none; border-radius: 6px; font-weight: 700; font-size: 0.875rem; cursor: pointer;">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'pembimbing_id', 'jurusan_id']))
                    <a href="{{ route('superadmin.monitoring.index') }}" style="height: 38px; padding: 0 1rem; background: var(--neutral-200); color: var(--neutral-700); border-radius: 6px; font-weight: 600; font-size: 0.875rem; display: inline-flex; align-items: center; text-decoration: none;">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Data Table Card -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-subtle);">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-400); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-800); margin: 0;">
                Daftar Peserta Magang Aktif
            </h3>
            <span style="font-size: 0.8125rem; color: var(--neutral-600);">Total: {{ $penempatanList->total() }} Peserta Aktif</span>
        </div>

        @if($penempatanList->isEmpty())
            <div style="padding: 3.5rem 1.5rem; text-align: center; color: var(--neutral-600);">
                <div style="width: 56px; height: 56px; background: var(--neutral-200); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto; color: var(--neutral-500);">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </div>
                <h4 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">Tidak Ada Peserta Magang Aktif</h4>
                <p style="font-size: 0.875rem; max-width: 460px; margin: 0 auto;">Saat ini belum ada data penempatan aktif yang cocok dengan kriteria pencarian.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                    <thead>
                        <tr style="background: var(--neutral-200); border-bottom: 1px solid var(--neutral-400); height: 50px;">
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Nama Peserta</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Institusi & Jurusan</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Pembimbing & Bidang</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Periode Bimbingan</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800); text-align: center;">Jalur</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800); text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($penempatanList as $item)
                            <tr style="border-bottom: 1px solid var(--neutral-400); height: 56px;">
                                <td style="padding: 0.75rem 1rem; font-weight: 600; color: var(--neutral-800);">
                                    <div style="font-size: 0.9375rem;">{{ $item->peserta?->nama }}</div>
                                    <small style="color: var(--neutral-600); font-family: monospace;">NIM: {{ $item->peserta?->profil?->nim_nisn ?? '-' }}</small>
                                </td>
                                <td style="padding: 0.75rem 1rem; color: var(--neutral-700);">
                                    <div style="font-weight: 600;">{{ $item->peserta?->profil?->institusi ?? '-' }}</div>
                                    <small style="color: var(--neutral-600);">{{ $item->peserta?->profil?->jurusan?->nama_jurusan ?? ($item->kuota?->jurusan?->nama_jurusan ?? '-') }}</small>
                                </td>
                                <td style="padding: 0.75rem 1rem; color: var(--neutral-800);">
                                    <div style="font-weight: 600;">{{ $item->pembimbing?->nama ?? 'Belum Ditetapkan' }}</div>
                                    <small style="color: var(--neutral-600);">{{ $item->bidang?->nama_bidang ?? 'Umum' }}</small>
                                </td>
                                <td style="padding: 0.75rem 1rem; color: var(--neutral-700);">
                                    <div>{{ $item->tgl_mulai ? \Carbon\Carbon::parse($item->tgl_mulai)->format('d/m/Y') : '-' }} s/d {{ $item->tgl_selesai ? \Carbon\Carbon::parse($item->tgl_selesai)->format('d/m/Y') : '-' }}</div>
                                    <small style="color: var(--blue-700); font-weight: 600;">{{ $item->durasi_bulan ?? 1 }} Bulan Magang</small>
                                </td>
                                <td style="padding: 0.75rem 1rem; text-align: center;">
                                    @if($item->jalur?->value === 'khusus')
                                        <span class="role-chip-badge" style="background: var(--amber-100); color: var(--amber-800); border: 1px solid var(--amber-500); font-weight: 700;">
                                            Khusus
                                        </span>
                                    @else
                                        <span class="role-chip-badge" style="background: var(--blue-50); color: var(--blue-700); border: 1px solid var(--blue-600); font-weight: 700;">
                                            Umum
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 0.75rem 1rem; text-align: right;">
                                    <a href="{{ route('superadmin.monitoring.show', $item) }}" style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.45rem 0.875rem; background: var(--blue-600); color: #FFFFFF; text-decoration: none; border-radius: 6px; font-weight: 700; font-size: 0.8125rem;">
                                        <span>Pantau Detail</span>
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($penempatanList->hasPages())
                <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--neutral-400);">
                    {{ $penempatanList->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
