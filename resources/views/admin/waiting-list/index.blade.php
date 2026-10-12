@extends('layouts.dashboard')

@section('title', 'Antrean Waiting List - Admin MagKom Sleman')
@section('page_title', 'Antrean Waiting List (Daftar Tunggu)')
@section('page_description', 'Kelola pendaftaran calon peserta saat kuota jurusan penuh berdasarkan urutan waktu pengajuan (FIFO).')

@section('breadcrumbs')
    <nav style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; color: var(--neutral-600, #64748B);">
        <span>Portal MagKom</span>
        <span>/</span>
        <span>Administrasi</span>
        <span>/</span>
        <span style="color: var(--neutral-900, #0F172A); font-weight: 700;">Antrean Waiting List</span>
    </nav>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem; max-width: 1400px; margin: 0 auto; width: 100%;">

    <style>
        .admin-stat-card {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid var(--neutral-200, #E2E8F0);
            padding: 1.25rem 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .admin-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
        }
        .admin-card {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid var(--neutral-200, #E2E8F0);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.875rem;
        }
        .admin-table th {
            background: #F8FAFC;
            padding: 0.85rem 1.25rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--neutral-600, #475569);
            border-bottom: 1px solid var(--neutral-200, #E2E8F0);
        }
        .admin-table td {
            padding: 0.95rem 1.25rem;
            border-bottom: 1px solid var(--neutral-200, #E2E8F0);
            vertical-align: middle;
            color: var(--neutral-800, #1E293B);
        }
        .admin-table tr:hover {
            background: #F8FAFC;
        }
        .btn-action-promosi {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.45rem 0.85rem;
            background: #10B981;
            color: #FFFFFF;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 700;
            font-size: 0.8125rem;
            box-shadow: 0 1px 2px rgba(16, 185, 129, 0.25);
            transition: all 0.15s ease;
        }
        .btn-action-promosi:hover {
            background: #059669;
            transform: translateY(-1px);
        }
        .btn-action-tinjau {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.45rem 0.85rem;
            background: #0641CC;
            color: #FFFFFF;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.8125rem;
            box-shadow: 0 1px 2px rgba(6, 65, 204, 0.2);
            transition: all 0.15s ease;
        }
        .btn-action-tinjau:hover {
            background: #0533A3;
            transform: translateY(-1px);
        }
    </style>

    {{-- 1. INFO BANNER & BULK CLOSURE PANEL --}}
    <div class="admin-card" style="padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.25rem; background: linear-gradient(135deg, #EFF6FF 0%, #FFFFFF 100%); border: 1px solid #DBEAFE;">
        <div style="display: flex; align-items: flex-start; gap: 0.875rem; flex: 1; min-width: 280px;">
            <div style="width: 44px; height: 44px; background: #DBEAFE; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #1D4ED8; flex-shrink: 0;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
            </div>
            <div>
                <h2 style="font-size: 1.0625rem; font-weight: 800; color: #1E40AF; margin: 0 0 0.25rem 0; letter-spacing: -0.01em;">
                    Prinsip Antrean Tunggu FIFO (First-In, First-Out)
                </h2>
                <p style="font-size: 0.8125rem; color: #1E3A8A; margin: 0; line-height: 1.45; max-width: 780px;">
                    Sesuai aturan bisnis <strong>BR-05</strong>, peserta diurutkan secara ketat berdasarkan stempel waktu submit berkas. Apabila kuota terbuka (akibat penambahan kuota jurusan atau pembatalan mentee), verifikator dapat langsung mempromosikan peserta teratas secara transparan.
                </p>
            </div>
        </div>

        @if(isset($bulanAntreanList) && $bulanAntreanList->isNotEmpty())
            <div style="display: flex; flex-direction: column; gap: 0.4rem; align-items: flex-end;">
                <span style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-600, #475569);">
                    Penutupan Antrean Awal Bulan (AC-04-7):
                </span>
                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    @foreach($bulanAntreanList as $bAntrean)
                        @php
                            $hariIni = today('Asia/Jakarta');
                            $tglMulai = \Carbon\Carbon::parse($bAntrean->bulan_mulai . '-01', 'Asia/Jakarta');
                            $bisaTutup = $hariIni->gte($tglMulai);
                        @endphp
                        <form method="POST" action="{{ route('admin.waiting-list.tutup', $bAntrean) }}" onsubmit="return confirm('Apakah Anda yakin ingin menutup seluruh sisa antrean waiting list untuk periode {{ $bAntrean->bulan_mulai?->translatedFormat('F Y') }}? Pendaftaran yang tersisa akan ditolak secara otomatis.');">
                            @csrf
                            <button type="submit" {{ !$bisaTutup ? 'disabled' : '' }} title="{{ $bisaTutup ? 'Tutup antrean sisa periode ini' : 'Hanya bisa ditutup setelah tanggal 1 bulan mulai periode magang' }}" style="padding: 0.45rem 0.85rem; border-radius: 8px; font-weight: 700; font-size: 0.75rem; border: 1px solid {{ $bisaTutup ? '#FCA5A5' : 'var(--neutral-300, #CBD5E1)' }}; background: {{ $bisaTutup ? '#FEE2E2' : '#F1F5F9' }}; color: {{ $bisaTutup ? '#DC2626' : '#94A3B8' }}; cursor: {{ $bisaTutup ? 'pointer' : 'not-allowed' }}; transition: all 0.15s ease;">
                                Tutup Antrean {{ $bAntrean->bulan_mulai?->translatedFormat('M Y') }}
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- 2. FILTER & SEARCH PANEL --}}
    <div class="admin-card" style="padding: 1.25rem 1.5rem;">
        <form method="GET" action="{{ route('admin.waiting-list.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            {{-- Search Bar --}}
            <div style="flex: 2; min-width: 240px;">
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B); margin-bottom: 0.375rem;">
                    Pencarian Calon Peserta / NIM / Institusi
                </label>
                <div style="position: relative;">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama, email, NIM, atau kampus..." style="width: 100%; height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem 0 2.4rem; font-size: 0.875rem; background: #ffffff; color: var(--neutral-900, #0F172A);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 0.85rem; top: 13px; color: var(--neutral-400, #94A3B8);">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </div>
            </div>

            {{-- Filter Periode Bulan --}}
            <div style="min-width: 200px; flex: 1;">
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B); margin-bottom: 0.375rem;">
                    Periode Bulan Magang
                </label>
                <select name="bulan_magang_id" style="width: 100%; height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff; color: var(--neutral-800, #1E293B);">
                    <option value="">Semua Periode Bulan</option>
                    @foreach($bulanAntreanList as $b)
                        <option value="{{ $b->id }}" {{ request('bulan_magang_id') == $b->id ? 'selected' : '' }}>
                            {{ $b->bulan_mulai?->translatedFormat('F Y') }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Jurusan --}}
            <div style="min-width: 200px; flex: 1;">
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B); margin-bottom: 0.375rem;">
                    Kejuruan Pilihan
                </label>
                <select name="jurusan_id" style="width: 100%; height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff; color: var(--neutral-800, #1E293B);">
                    <option value="">Semua Jurusan</option>
                    @foreach($jurusanList ?? [] as $j)
                        <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>
                            {{ $j->nama_jurusan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" style="height: 42px; padding: 0 1.25rem; background: #0641CC; color: #fff; border: none; border-radius: 8px; font-weight: 700; font-size: 0.875rem; cursor: pointer; transition: background 0.15s ease; box-shadow: 0 1px 3px rgba(6, 65, 204, 0.2);">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'bulan_magang_id', 'jurusan_id']))
                    <a href="{{ route('admin.waiting-list.index') }}" style="height: 42px; padding: 0 1rem; background: var(--neutral-100, #F1F5F9); color: var(--neutral-700, #334155); border-radius: 8px; font-weight: 600; font-size: 0.875rem; display: inline-flex; align-items: center; text-decoration: none; border: 1px solid var(--neutral-200, #E2E8F0);">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- 3. DATA TABLE CARD --}}
    <div class="admin-card">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0); display: flex; justify-content: space-between; align-items: center; background: #ffffff;">
            <div style="display: flex; align-items: center; gap: 0.6rem;">
                <h3 style="font-size: 1.0625rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0;">
                    Daftar Antrean Calon Peserta
                </h3>
                <span style="padding: 0.2rem 0.6rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: #EFF6FF; color: #1D4ED8;">
                    {{ $waitingList->total() }} Menunggu
                </span>
            </div>
            <span style="font-size: 0.8125rem; color: var(--neutral-500, #64748B);">
                Urutan antrean dijamin ketat berdasarkan stempel waktu (FIFO)
            </span>
        </div>

        @if($waitingList->isEmpty())
            <div style="padding: 4rem 1.5rem; text-align: center; color: var(--neutral-600, #64748B);">
                <div style="width: 64px; height: 64px; background: #EFF6FF; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem auto; color: #1D4ED8;">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <h4 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-900, #0F172A); margin-bottom: 0.375rem;">
                    Tidak Ada Antrean Waiting List
                </h4>
                <p style="font-size: 0.875rem; max-width: 480px; margin: 0 auto; line-height: 1.5;">
                    Saat ini tidak ada pendaftaran calon peserta magang yang tertahan dalam antrean tunggu.
                </p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 80px; text-align: center;">Urutan</th>
                            <th style="width: 140px;">No. Pendaftaran</th>
                            <th>Nama Calon Peserta</th>
                            <th>Instansi & Jurusan</th>
                            <th>Periode Magang</th>
                            <th style="text-align: center;">Ketersediaan Kuota</th>
                            <th>Waktu Submit (FIFO)</th>
                            <th style="text-align: right; width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($waitingList as $index => $item)
                            @php
                                $kuotaModel = $item->kuota;
                                $sisaKuota = $kuotaModel ? max(0, $kuotaModel->jumlah - $kuotaModel->terisi) : 0;
                                $urutanNomor = $waitingList->firstItem() + $index;
                            @endphp
                            <tr>
                                {{-- Urutan FIFO --}}
                                <td style="text-align: center;">
                                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 50%; background: {{ $urutanNomor === 1 ? '#EFF6FF' : '#F1F5F9' }}; color: {{ $urutanNomor === 1 ? '#1D4ED8' : '#475569' }}; font-weight: 800; font-size: 0.8125rem; border: 1px solid {{ $urutanNomor === 1 ? '#BFDBFE' : '#E2E8F0' }};">
                                        #{{ $urutanNomor }}
                                    </span>
                                </td>

                                {{-- No. Pendaftaran --}}
                                <td>
                                    <span style="display: inline-block; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size: 0.8125rem; font-weight: 700; color: #0641CC; background: #EFF6FF; padding: 0.25rem 0.55rem; border-radius: 6px; border: 1px solid #DBEAFE;">
                                        {{ $item->nomor_pendaftaran }}
                                    </span>
                                </td>

                                {{-- Nama Calon Peserta --}}
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div style="width: 36px; height: 36px; border-radius: 8px; background: linear-gradient(135deg, #0641CC 0%, #2563EB 100%); color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8125rem; flex-shrink: 0;">
                                            {{ strtoupper(substr($item->user?->nama ?? 'P', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; color: var(--neutral-900, #0F172A); font-size: 0.875rem;">
                                                {{ $item->user?->nama }}
                                            </div>
                                            <div style="font-size: 0.75rem; color: var(--neutral-500, #64748B); margin-top: 0.1rem;">
                                                {{ $item->user?->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Instansi & Jurusan --}}
                                <td>
                                    <div style="font-weight: 600; color: var(--neutral-900, #0F172A); font-size: 0.85rem;">
                                        {{ $item->asal_instansi ?? ($item->user?->profil?->institusi ?? '-') }}
                                    </div>
                                    <small style="color: var(--neutral-500, #64748B); font-size: 0.78rem;">
                                        {{ $kuotaModel?->jurusan?->nama_jurusan ?? '-' }}
                                    </small>
                                </td>

                                {{-- Periode --}}
                                <td>
                                    <div style="font-weight: 700; color: var(--neutral-800, #1E293B); font-size: 0.85rem;">
                                        {{ $item->bulanMagang?->bulan_mulai?->translatedFormat('F Y') }}
                                    </div>
                                </td>

                                {{-- Ketersediaan Kuota --}}
                                <td style="text-align: center;">
                                    @if($sisaKuota > 0)
                                        <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.25rem 0.65rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: #D1FAE5; color: #065F46; border: 1px solid #A7F3D0;">
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #10B981;"></span>
                                            Tersedia {{ $sisaKuota }} Kursi
                                        </span>
                                    @else
                                        <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.25rem 0.65rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: #F1F5F9; color: #475569; border: 1px solid #CBD5E1;">
                                            Kuota Penuh
                                        </span>
                                    @endif
                                </td>

                                {{-- Waktu Submit (FIFO) --}}
                                <td style="color: var(--neutral-600, #475569); font-family: ui-monospace, SFMono-Regular, monospace; font-size: 0.8125rem;">
                                    {{ $item->diajukan_at ? $item->diajukan_at->format('d/m/Y H:i:s') : '-' }}
                                </td>

                                {{-- Aksi --}}
                                <td style="text-align: right;">
                                    @if($sisaKuota > 0)
                                        <form method="POST" action="{{ route('admin.waiting-list.promote') }}" style="display: inline-block; margin: 0;" onsubmit="return confirm('Promosikan {{ addslashes($item->user?->nama ?? 'peserta') }} menjadi Diterima secara instan sesuai kuota yang tersedia?');">
                                            @csrf
                                            <input type="hidden" name="pendaftaran_id" value="{{ $item->id }}">
                                            <button type="submit" class="btn-action-promosi" style="border: none; cursor: pointer;" title="Promosikan 1-klik langsung sesuai kuota kosong">
                                                <span>Promote FIFO</span>
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="9 18 15 12 9 6"></polyline>
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('admin.pendaftaran.show', $item) }}" class="btn-action-tinjau" title="Tinjau berkas pendaftaran">
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
                <div style="padding: 1.25rem 1.5rem; border-top: 1px solid var(--neutral-200, #E2E8F0); background: #ffffff;">
                    {{ $waitingList->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
