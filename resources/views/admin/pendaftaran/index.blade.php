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
            <div style="display: flex; flex-direction: column; gap: 0.375rem; flex: 2; min-width: 240px;">
                <label for="search-input" style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">Pencarian Calon Peserta</label>
                <div style="position: relative;">
                    <input type="text" id="search-input" name="q" value="{{ request('q') }}" placeholder="Cari nama, no. registrasi, kampus/sekolah, atau NIM..." style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.75rem 0 2.25rem; font-size: 0.875rem; background: var(--neutral-0);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 0.75rem; top: 13px; color: var(--neutral-500);">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.375rem; flex: 1; min-width: 180px;">
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

            <div style="display: flex; flex-direction: column; gap: 0.375rem; flex: 1; min-width: 180px;">
                <label style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">Filter Jurusan</label>
                <select name="jurusan_id" style="height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: var(--neutral-0);">
                    <option value="">Semua Jurusan</option>
                    @foreach($jurusanList as $jur)
                        <option value="{{ $jur->id }}" {{ request('jurusan_id') == $jur->id ? 'selected' : '' }}>
                            {{ $jur->nama_jurusan }} ({{ strtoupper($jur->kategori) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.375rem; flex: 1; min-width: 140px;">
                <label style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">Filter Jalur</label>
                <select name="jalur" style="height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: var(--neutral-0);">
                    <option value="">Semua Jalur</option>
                    <option value="umum" {{ request('jalur') === 'umum' ? 'selected' : '' }}>Jalur Umum</option>
                    <option value="khusus" {{ request('jalur') === 'khusus' ? 'selected' : '' }}>Jalur Khusus</option>
                </select>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.375rem; flex: 1; min-width: 180px;">
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

            <div style="display: flex; flex-direction: column; gap: 0.375rem; flex: 1; min-width: 180px;">
                <label style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">Urutan Antrean</label>
                <select name="sort" style="height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: var(--neutral-0);">
                    <option value="fifo" {{ request('sort', 'fifo') === 'fifo' ? 'selected' : '' }}>FIFO (Terawal Masuk - Default)</option>
                    <option value="terbaru" {{ request('sort') === 'terbaru' ? 'selected' : '' }}>Paling Baru Dibuat</option>
                    <option value="terlama" {{ request('sort') === 'terlama' ? 'selected' : '' }}>Paling Lama Dibuat</option>
                </select>
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" style="height: 42px; padding: 0 1.25rem; background: var(--blue-600); color: #FFFFFF; font-weight: 600; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer; transition: background 0.15s; display: inline-flex; align-items: center; gap: 0.5rem;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    <span>Filter</span>
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
                <p style="font-size: 0.875rem; max-width: 460px; margin: 0 auto;">Saat ini belum ada berkas pendaftaran calon peserta magang yang sesuai dengan kriteria filter yang dipilih.</p>
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
                            @php
                                $val = $item->status->value ?? (string)$item->status;
                                $badgeStyle = match($val) {
                                    'berkas_belum_lengkap' => ['bg' => 'var(--neutral-200)', 'color' => 'var(--neutral-800)', 'border' => 'var(--neutral-400)'],
                                    'diajukan' => ['bg' => 'var(--amber-200)', 'color' => 'var(--amber-900)', 'border' => 'var(--amber-600)'],
                                    'perlu_perbaikan' => ['bg' => 'var(--amber-100)', 'color' => 'var(--amber-800)', 'border' => 'var(--amber-600)'],
                                    'waiting_list' => ['bg' => 'var(--blue-tint)', 'color' => 'var(--blue-700)', 'border' => 'var(--blue-300)'],
                                    'diterima' => ['bg' => 'var(--green-100)', 'color' => 'var(--green-800)', 'border' => 'var(--green-600)'],
                                    'ditolak' => ['bg' => 'var(--red-100)', 'color' => 'var(--red-800)', 'border' => 'var(--red-600)'],
                                    default => ['bg' => 'var(--neutral-200)', 'color' => 'var(--neutral-700)', 'border' => 'var(--neutral-400)'],
                                };
                            @endphp
                            <tr style="border-bottom: 1px solid var(--neutral-400); height: 56px;">
                                <td style="padding: 0.75rem 1rem; font-weight: 700; color: var(--blue-700);">
                                    {{ $item->nomor_pendaftaran }}
                                </td>
                                <td style="padding: 0.75rem 1rem;">
                                    <div style="font-weight: 700; color: var(--neutral-800);">{{ $item->user?->nama }}</div>
                                    <small style="color: var(--neutral-600);">{{ $item->user?->email }}</small>
                                </td>
                                <td style="padding: 0.75rem 1rem; color: var(--neutral-700);">
                                    <div style="font-weight: 600;">{{ $item->asal_instansi }}</div>
                                    <small style="color: var(--neutral-600);">{{ $item->kuota?->jurusan?->nama_jurusan }}</small>
                                </td>
                                <td style="padding: 0.75rem 1rem; color: var(--neutral-700);">
                                    <div style="font-weight: 600;">{{ $item->bulanMagang?->bulan_mulai?->translatedFormat('F Y') }}</div>
                                    <small style="color: var(--neutral-600);">Durasi: {{ $item->durasi_bulan }} Bulan</small>
                                </td>
                                <td style="padding: 0.75rem 1rem;">
                                    <span class="role-chip-badge" style="background: {{ $badgeStyle['bg'] }}; color: {{ $badgeStyle['color'] }}; border: 1px solid {{ $badgeStyle['border'] }}; font-weight: 700;">
                                        {{ $item->status->label() }}
                                    </span>
                                </td>
                                <td style="padding: 0.75rem 1rem; text-align: right;">
                                    <a href="{{ route('admin.pendaftaran.show', $item) }}" style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.45rem 0.875rem; background: var(--blue-600); color: #FFFFFF; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 0.8125rem;">
                                        <span>Tinjau Berkas</span>
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

            @if($pendaftaranList->hasPages())
                <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--neutral-400);">
                    {{ $pendaftaranList->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
