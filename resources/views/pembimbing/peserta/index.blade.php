@extends('layouts.dashboard')

@section('title', 'Direktori Peserta Bimbingan - Portal MagKom')

@section('content')
<div class="dashboard-content" style="display: flex; flex-direction: column; gap: 1.5rem;">

    <!-- Breadcrumb & Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <nav style="font-size: 0.8rem; color: var(--neutral-500); margin-bottom: 0.35rem; display: flex; align-items: center; gap: 0.4rem;">
                <a href="{{ route('pembimbing.dashboard') }}" style="color: var(--neutral-600); text-decoration: none;">Dashboard</a>
                <span>/</span>
                <span style="color: var(--neutral-900); font-weight: 600;">Peserta Bimbingan</span>
            </nav>
            <h1 style="font-size: 1.5rem; font-weight: 700; color: var(--neutral-900); margin: 0;">
                Direktori Peserta Bimbingan (Mentee Hub)
            </h1>
            <p style="font-size: 0.875rem; color: var(--neutral-600); margin: 0.25rem 0 0 0;">
                Kelola dan pantau seluruh peserta magang aktif maupun alumni bimbingan dinas.
            </p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <a href="{{ route('pembimbing.tugas.index') }}" class="btn" style="background: #0641CC; color: #ffffff; padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Beri Tugas Baru
            </a>
        </div>
    </div>

    <!-- Tab Filter & Pencarian -->
    <div style="background: #ffffff; border-radius: 14px; border: 1px solid var(--neutral-200); padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        
        <!-- Tab Aktif vs Selesai -->
        <div style="display: flex; gap: 0.5rem; background: var(--neutral-100); padding: 0.3rem; border-radius: 10px;">
            <a href="{{ route('pembimbing.peserta.index', ['tab' => 'aktif']) }}" style="padding: 0.45rem 1rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600; text-decoration: none; transition: all 0.2s ease; {{ $currentTab === 'aktif' ? 'background: #ffffff; color: #0641CC; box-shadow: 0 1px 3px rgba(0,0,0,0.1);' : 'color: var(--neutral-600);' }}">
                Peserta Aktif
                <span style="margin-left: 0.35rem; padding: 0.1rem 0.45rem; border-radius: 999px; font-size: 0.75rem; {{ $currentTab === 'aktif' ? 'background: #e0e7ff; color: #0641CC;' : 'background: var(--neutral-200); color: var(--neutral-600);' }}">
                    {{ $countAktif }}
                </span>
            </a>
            <a href="{{ route('pembimbing.peserta.index', ['tab' => 'selesai']) }}" style="padding: 0.45rem 1rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600; text-decoration: none; transition: all 0.2s ease; {{ $currentTab === 'selesai' ? 'background: #ffffff; color: #0641CC; box-shadow: 0 1px 3px rgba(0,0,0,0.1);' : 'color: var(--neutral-600);' }}">
                Alumni Selesai
                <span style="margin-left: 0.35rem; padding: 0.1rem 0.45rem; border-radius: 999px; font-size: 0.75rem; {{ $currentTab === 'selesai' ? 'background: #e0e7ff; color: #0641CC;' : 'background: var(--neutral-200); color: var(--neutral-600);' }}">
                    {{ $countSelesai }}
                </span>
            </a>
        </div>

        <!-- Form Pencarian -->
        <form method="GET" action="{{ route('pembimbing.peserta.index') }}" style="display: flex; gap: 0.5rem; flex: 1; max-width: 400px;">
            <input type="hidden" name="tab" value="{{ $currentTab }}">
            <div style="position: relative; width: 100%;">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, kampus, NIM..." style="width: 100%; height: 40px; padding: 0 1rem 0 2.5rem; border-radius: 8px; border: 1px solid var(--neutral-300); font-size: 0.875rem; outline: none;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--neutral-400)" stroke-width="2" style="position: absolute; left: 0.75rem; top: 11px;">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </div>
            <button type="submit" style="height: 40px; padding: 0 1rem; background: var(--neutral-800); color: #ffffff; border: none; border-radius: 8px; font-size: 0.875rem; font-weight: 600; cursor: pointer;">
                Cari
            </button>
            @if(request('q'))
                <a href="{{ route('pembimbing.peserta.index', ['tab' => $currentTab]) }}" style="height: 40px; padding: 0 0.75rem; background: var(--neutral-200); color: var(--neutral-700); border-radius: 8px; font-size: 0.875rem; font-weight: 600; display: inline-flex; align-items: center; text-decoration: none;">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Tabel Daftar Mentee -->
    <div style="background: #ffffff; border-radius: 14px; border: 1px solid var(--neutral-200); box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--neutral-200); background: #f8fafc; color: var(--neutral-600); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                        <th style="padding: 1rem 1.5rem;">Peserta Bimbingan</th>
                        <th style="padding: 1rem 1rem;">Asal Institusi & Jurusan</th>
                        <th style="padding: 1rem 1rem;">Jalur & Bidang</th>
                        <th style="padding: 1rem 1rem;">Periode Magang</th>
                        <th style="padding: 1rem 1rem;">Progres Durasi</th>
                        <th style="padding: 1rem 1rem;">Status Nilai</th>
                        <th style="padding: 1rem 1.5rem; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penempatanList as $p)
                        @php
                            $tglMulai = $p->tgl_mulai;
                            $tglSelesai = $p->tgl_selesai;
                            $totalHari = $tglMulai && $tglSelesai ? max(1, $tglMulai->diffInDays($tglSelesai)) : 1;
                            $hariBerjalan = $tglMulai ? max(0, \Carbon\Carbon::today()->diffInDays($tglMulai, false)) : 0;
                            $persenHari = $p->status->value === 'selesai' ? 100 : min(100, (int) round(($hariBerjalan / $totalHari) * 100));
                            $statusNilai = $p->penilaian?->status->value ?? 'belum';
                        @endphp
                        <tr style="border-bottom: 1px solid var(--neutral-100); transition: background 0.15s ease;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 1.1rem 1.5rem;">
                                <div style="display: flex; align-items: center; gap: 0.85rem;">
                                    <div style="width: 40px; height: 40px; border-radius: 50%; background: #e0e7ff; color: #3730a3; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9rem;">
                                        {{ strtoupper(substr($p->peserta?->nama ?? 'P', 0, 2)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('pembimbing.peserta.show', $p->id) }}" style="font-weight: 600; color: var(--neutral-900); text-decoration: none; font-size: 0.925rem;">
                                            {{ $p->peserta?->nama }}
                                        </a>
                                        <div style="font-size: 0.75rem; color: var(--neutral-500); margin-top: 0.1rem;">
                                            {{ $p->peserta?->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 1.1rem 1rem;">
                                <div style="font-weight: 500; color: var(--neutral-800);">
                                    {{ $p->peserta?->profil?->institusi ?? '-' }}
                                </div>
                                <div style="font-size: 0.75rem; color: var(--neutral-500);">
                                    {{ $p->peserta?->profil?->jurusan?->nama_jurusan ?? '-' }}
                                </div>
                            </td>
                            <td style="padding: 1.1rem 1rem;">
                                <div style="display: flex; flex-direction: column; gap: 0.2rem;">
                                    <span style="font-size: 0.75rem; font-weight: 600; color: #0641CC; background: rgba(6, 65, 204, 0.08); padding: 0.15rem 0.45rem; border-radius: 4px; display: inline-block; width: fit-content;">
                                        {{ ucfirst($p->jalur?->value ?? 'Umum') }}
                                    </span>
                                    <span style="font-size: 0.75rem; color: var(--neutral-600);">
                                        {{ $p->bidang?->nama_bidang ?? '-' }}
                                    </span>
                                </div>
                            </td>
                            <td style="padding: 1.1rem 1rem;">
                                <div style="font-size: 0.8rem; color: var(--neutral-800); font-weight: 500;">
                                    {{ $tglMulai?->format('d M') }} &ndash; {{ $tglSelesai?->format('d M Y') }}
                                </div>
                                <div style="font-size: 0.7rem; color: var(--neutral-500); margin-top: 0.15rem;">
                                    {{ $totalHari }} Hari Kalender
                                </div>
                            </td>
                            <td style="padding: 1.1rem 1rem; min-width: 140px;">
                                <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.75rem; margin-bottom: 0.25rem;">
                                    <span style="color: var(--neutral-700); font-weight: 600;">{{ $persenHari }}%</span>
                                    <span style="color: var(--neutral-500); font-size: 0.7rem;">
                                        {{ $p->status->value === 'selesai' ? 'Selesai' : $hariBerjalan . '/' . $totalHari . ' hr' }}
                                    </span>
                                </div>
                                <div style="height: 6px; width: 100%; background: var(--neutral-200); border-radius: 999px; overflow: hidden;">
                                    <div style="height: 100%; width: {{ $persenHari }}%; background: {{ $p->status->value === 'selesai' ? '#10b981' : '#0641CC' }}; border-radius: 999px;"></div>
                                </div>
                            </td>
                            <td style="padding: 1.1rem 1rem;">
                                @if($statusNilai === 'final')
                                    <span style="font-size: 0.75rem; font-weight: 600; background: #d1fae5; color: #065f46; padding: 0.25rem 0.55rem; border-radius: 999px; display: inline-flex; align-items: center; gap: 0.25rem;">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        Final: {{ $p->penilaian?->predikat ?? '-' }} ({{ number_format((float) ($p->penilaian?->nilai_akhir ?? 0), 2) }})
                                    </span>
                                @elseif($statusNilai === 'draft')
                                    <span style="font-size: 0.75rem; font-weight: 600; background: #fef3c7; color: #92400e; padding: 0.25rem 0.55rem; border-radius: 999px;">
                                        Draft
                                    </span>
                                @else
                                    <span style="font-size: 0.75rem; font-weight: 500; background: var(--neutral-100); color: var(--neutral-600); padding: 0.25rem 0.55rem; border-radius: 999px;">
                                        Belum Dinilai
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 1.1rem 1.5rem; text-align: right;">
                                <div style="display: inline-flex; gap: 0.4rem;">
                                    <a href="{{ route('pembimbing.peserta.show', $p->id) }}" style="padding: 0.4rem 0.75rem; background: var(--neutral-100); color: var(--neutral-700); font-size: 0.75rem; font-weight: 600; border-radius: 6px; text-decoration: none;" title="Buka Profil 360°">
                                        Profil
                                    </a>
                                    <a href="{{ route('pembimbing.penilaian.show', $p->id) }}" style="padding: 0.4rem 0.75rem; background: rgba(6, 65, 204, 0.08); color: #0641CC; font-size: 0.75rem; font-weight: 600; border-radius: 6px; text-decoration: none;" title="Buka Lembar Nilai">
                                        Nilai
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding: 3rem 1.5rem; text-align: center; color: var(--neutral-500);">
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
                                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--neutral-300)" stroke-width="1.5">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                    </svg>
                                    <span style="font-size: 1rem; font-weight: 600; color: var(--neutral-700);">
                                        Tidak Ada Data Peserta
                                    </span>
                                    <span style="font-size: 0.8rem;">
                                        {{ request('q') ? 'Tidak ditemukan data dengan kata kunci pencarian tersebut.' : ($currentTab === 'aktif' ? 'Tidak ada peserta bimbingan yang aktif saat ini.' : 'Belum ada riwayat alumni bimbingan yang telah selesai.') }}
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($penempatanList->hasPages())
            <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--neutral-200); background: #fafafa;">
                {{ $penempatanList->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
