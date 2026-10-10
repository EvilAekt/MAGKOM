@extends('layouts.dashboard')

@section('title', 'Antrean Waiting List - Admin MagKom Sleman')
@section('page_title', 'Antrean Waiting List (Daftar Tunggu)')
@section('page_description', 'Kelola pendaftaran calon peserta saat kuota jurusan penuh berdasarkan urutan waktu pengajuan (FIFO).')

@section('breadcrumbs')
    <span class="breadcrumb-current">Antrean Waiting List</span>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">

    <!-- Info Banner Card & Bulk Closure Panel -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; align-items: center; gap: 0.875rem; flex: 1; min-width: 280px;">
            <div style="width: 42px; height: 42px; background: var(--blue-tint); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--blue-700); flex-shrink: 0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
            </div>
            <div>
                <h2 style="font-size: 1rem; font-weight: 700; color: var(--neutral-800); margin: 0 0 0.125rem 0;">Prinsip Antrean Tunggu FIFO</h2>
                <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0;">
                    Sesuai aturan bisnis BR-05, peserta diurutkan ketat berdasarkan stempel waktu submit berkas. Jika kuota terbuka (akibat penambahan kuota atau pembatalan), verifikator dapat langsung mempromosikan peserta teratas.
                </p>
            </div>
        </div>

        @if(isset($bulanAntreanList) && $bulanAntreanList->isNotEmpty())
            <div style="display: flex; flex-direction: column; gap: 0.5rem; align-items: flex-end;">
                <span style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-600);">Penutupan Antrean Awal Bulan (AC-04-7):</span>
                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    @foreach($bulanAntreanList as $bAntrean)
                        @php
                            $hariIni = today('Asia/Jakarta');
                            $tglMulai = \Carbon\Carbon::parse($bAntrean->bulan_mulai . '-01', 'Asia/Jakarta');
                            $bisaTutup = $hariIni->gte($tglMulai);
                        @endphp
                        <form method="POST" action="{{ route('admin.waiting-list.tutup', $bAntrean) }}" onsubmit="return confirm('Apakah Anda yakin ingin menutup seluruh sisa antrean waiting list untuk periode {{ $bAntrean->bulan_mulai?->translatedFormat('F Y') }}? Pendaftaran yang tersisa akan ditolak secara otomatis.');">
                            @csrf
                            <button type="submit" {{ !$bisaTutup ? 'disabled' : '' }} title="{{ $bisaTutup ? 'Tutup antrean sisa periode ini' : 'Hanya bisa ditutup setelah tanggal 1 bulan mulai periode magang' }}" style="padding: 0.4rem 0.75rem; border-radius: 6px; font-weight: 600; font-size: 0.75rem; border: 1px solid {{ $bisaTutup ? 'var(--red-600)' : 'var(--neutral-400)' }}; background: {{ $bisaTutup ? 'var(--red-100)' : 'var(--neutral-200)' }}; color: {{ $bisaTutup ? 'var(--red-800)' : 'var(--neutral-600)' }}; cursor: {{ $bisaTutup ? 'pointer' : 'not-allowed' }};">
                                Tutup Antrean {{ $bAntrean->bulan_mulai?->translatedFormat('M Y') }}
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Filter & Search Panel -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle);">
        <form method="GET" action="{{ route('admin.waiting-list.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 1; min-width: 220px;">
                <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Cari Calon Peserta / NIM / Institusi
                </label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama, email, NIM, atau kampus..." style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
            </div>

            <div style="min-width: 180px;">
                <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Periode Bulan
                </label>
                <select name="bulan_magang_id" style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                    <option value="">Semua Periode Bulan</option>
                    @foreach($bulanAntreanList as $b)
                        <option value="{{ $b->id }}" {{ request('bulan_magang_id') == $b->id ? 'selected' : '' }}>
                            {{ $b->bulan_mulai?->translatedFormat('F Y') }}
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
                    @foreach($jurusanList ?? [] as $j)
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
                @if(request()->hasAny(['search', 'bulan_magang_id', 'jurusan_id']))
                    <a href="{{ route('admin.waiting-list.index') }}" style="height: 38px; padding: 0 1rem; background: var(--neutral-200); color: var(--neutral-700); border-radius: 6px; font-weight: 600; font-size: 0.875rem; display: inline-flex; align-items: center; text-decoration: none;">
                        Reset
                    </a>
                @endif
            </div>
        </form>
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
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800); width: 60px;">Urutan</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">No. Pendaftaran</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Nama Calon Peserta</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Asal Instansi & Jurusan</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Periode Bulan</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800); text-align: center;">Ketersediaan Kuota</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Waktu Submit (FIFO)</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800); text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($waitingList as $index => $item)
                            @php
                                $kuotaModel = $item->kuota;
                                $sisaKuota = $kuotaModel ? max(0, $kuotaModel->jumlah - $kuotaModel->terisi) : 0;
                            @endphp
                            <tr style="border-bottom: 1px solid var(--neutral-400); height: 56px;">
                                <td style="padding: 0.75rem 1rem; font-weight: 800; color: var(--blue-700);">
                                    #{{ $waitingList->firstItem() + $index }}
                                </td>
                                <td style="padding: 0.75rem 1rem; font-weight: 700; color: var(--blue-700);">
                                    {{ $item->nomor_pendaftaran }}
                                </td>
                                <td style="padding: 0.75rem 1rem; font-weight: 600; color: var(--neutral-800);">
                                    <div>{{ $item->user?->nama }}</div>
                                    <small style="color: var(--neutral-600);">{{ $item->user?->email }}</small>
                                </td>
                                <td style="padding: 0.75rem 1rem; color: var(--neutral-700);">
                                    <div style="font-weight: 600;">{{ $item->asal_instansi ?? ($item->user?->profil?->institusi ?? '-') }}</div>
                                    <small style="color: var(--neutral-600);">{{ $kuotaModel?->jurusan?->nama_jurusan }}</small>
                                </td>
                                <td style="padding: 0.75rem 1rem; color: var(--neutral-800); font-weight: 600;">
                                    {{ $item->bulanMagang?->bulan_mulai?->translatedFormat('F Y') }}
                                </td>
                                <td style="padding: 0.75rem 1rem; text-align: center;">
                                    @if($sisaKuota > 0)
                                        <span class="role-chip-badge" style="background: var(--green-100); color: var(--green-800); border: 1px solid var(--green-600); font-weight: 700;">
                                            Tersedia {{ $sisaKuota }} Kursi
                                        </span>
                                    @else
                                        <span class="role-chip-badge" style="background: var(--neutral-200); color: var(--neutral-700); border: 1px solid var(--neutral-400);">
                                            Kuota Penuh
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 0.75rem 1rem; color: var(--neutral-700); font-family: monospace; font-size: 0.8125rem;">
                                    {{ $item->diajukan_at ? $item->diajukan_at->format('d/m/Y H:i:s') : '-' }}
                                </td>
                                <td style="padding: 0.75rem 1rem; text-align: right;">
                                    @if($sisaKuota > 0)
                                        <a href="{{ route('admin.pendaftaran.show', $item) }}" style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.45rem 0.875rem; background: var(--green-600); color: #FFFFFF; text-decoration: none; border-radius: 6px; font-weight: 700; font-size: 0.8125rem; box-shadow: var(--shadow-subtle);" title="Kuota terbuka! Proses penerimaan peserta ini">
                                            <span>Promosikan</span>
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="9 18 15 12 9 6"></polyline>
                                            </svg>
                                        </a>
                                    @else
                                        <a href="{{ route('admin.pendaftaran.show', $item) }}" style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.45rem 0.875rem; background: var(--blue-600); color: #FFFFFF; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 0.8125rem;">
                                            <span>Tinjau</span>
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="9 18 15 12 9 6"></polyline>
                                            </svg>
                                        </a>
                                    @endif
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
