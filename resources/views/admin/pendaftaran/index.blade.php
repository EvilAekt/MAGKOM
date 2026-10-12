@extends('layouts.dashboard')

@section('title', 'Verifikasi Pendaftaran - Admin MagKom Sleman')
@section('page_title', 'Verifikasi Berkas Pendaftaran')
@section('page_description', 'Tinjau kelengkapan berkas pendaftaran calon peserta magang dan tentukan keputusan penerimaan secara tertib FIFO.')

@section('breadcrumbs')
    <nav style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; color: var(--neutral-600, #64748B);">
        <span>Portal MagKom</span>
        <span>/</span>
        <span>Administrasi</span>
        <span>/</span>
        <span style="color: var(--neutral-900, #0F172A); font-weight: 700;">Antrean Verifikasi Berkas</span>
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
        .filter-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.45rem 0.9rem;
            border-radius: 9999px;
            font-size: 0.8125rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.15s ease;
            border: 1px solid var(--neutral-300, #CBD5E1);
            color: var(--neutral-700, #334155);
            background: #ffffff;
            white-space: nowrap;
        }
        .filter-pill:hover {
            border-color: #0641CC;
            color: #0641CC;
            background: #F8FAFC;
        }
        .filter-pill.active {
            background: #0641CC;
            color: #ffffff;
            border-color: #0641CC;
            box-shadow: 0 2px 6px rgba(6, 65, 204, 0.25);
        }
        .filter-pill.active .pill-count {
            background: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }
        .pill-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.1rem 0.45rem;
            border-radius: 9999px;
            font-size: 0.72rem;
            background: var(--neutral-100, #F1F5F9);
            color: var(--neutral-600, #475569);
            font-weight: 700;
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
        .btn-tinjau {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.5rem 1rem;
            background: #0641CC;
            color: #ffffff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.8125rem;
            box-shadow: 0 1px 3px rgba(6, 65, 204, 0.2);
            transition: all 0.15s ease;
        }
        .btn-tinjau:hover {
            background: #0533A3;
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(6, 65, 204, 0.3);
        }
    </style>

    {{-- 1. HERO QUEUE METRIC CARDS --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
        {{-- Total Pengajuan --}}
        <div class="admin-stat-card">
            <div>
                <span style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-500, #64748B);">Total Pengajuan</span>
                <div style="font-size: 1.75rem; font-weight: 800; color: #0F172A; margin-top: 0.25rem; letter-spacing: -0.02em;">
                    {{ number_format($queueStats['total'] ?? $pendaftaranList->total()) }}
                </div>
                <span style="font-size: 0.75rem; color: var(--neutral-500, #64748B); display: inline-flex; align-items: center; gap: 0.3rem; margin-top: 0.35rem;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                    Semua riwayat pendaftaran
                </span>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #EFF6FF; color: #1D4ED8; display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>
            </div>
        </div>

        {{-- Menunggu Ditinjau (Diajukan) --}}
        <div class="admin-stat-card" style="border-left: 4px solid #F59E0B;">
            <div>
                <span style="font-size: 0.8125rem; font-weight: 600; color: #B45309;">Menunggu Ditinjau</span>
                <div style="font-size: 1.75rem; font-weight: 800; color: #B45309; margin-top: 0.25rem; letter-spacing: -0.02em;">
                    {{ number_format($queueStats['diajukan'] ?? 0) }}
                </div>
                <span style="font-size: 0.75rem; color: #D97706; display: inline-flex; align-items: center; gap: 0.3rem; margin-top: 0.35rem;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    Prioritas antrean FIFO
                </span>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </div>
        </div>

        {{-- Perlu Perbaikan Peserta --}}
        <div class="admin-stat-card">
            <div>
                <span style="font-size: 0.8125rem; font-weight: 600; color: #C2410C;">Perlu Perbaikan</span>
                <div style="font-size: 1.75rem; font-weight: 800; color: #C2410C; margin-top: 0.25rem; letter-spacing: -0.02em;">
                    {{ number_format($queueStats['perlu_perbaikan'] ?? 0) }}
                </div>
                <span style="font-size: 0.75rem; color: var(--neutral-500, #64748B); display: inline-flex; align-items: center; gap: 0.3rem; margin-top: 0.35rem;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    Menunggu revisi berkas
                </span>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FFEDD5; color: #EA580C; display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
            </div>
        </div>

        {{-- SLA Peringatan (> 3 Hari) --}}
        <div class="admin-stat-card" style="border-left: 4px solid {{ ($queueStats['sla_warning'] ?? 0) > 0 ? '#EF4444' : '#10B981' }};">
            <div>
                <span style="font-size: 0.8125rem; font-weight: 600; color: {{ ($queueStats['sla_warning'] ?? 0) > 0 ? '#B91C1C' : '#047857' }};">
                    SLA Kritis (> 3 Hari)
                </span>
                <div style="font-size: 1.75rem; font-weight: 800; color: {{ ($queueStats['sla_warning'] ?? 0) > 0 ? '#DC2626' : '#059669' }}; margin-top: 0.25rem; letter-spacing: -0.02em;">
                    {{ number_format($queueStats['sla_warning'] ?? 0) }}
                </div>
                <span style="font-size: 0.75rem; color: var(--neutral-500, #64748B); display: inline-flex; align-items: center; gap: 0.3rem; margin-top: 0.35rem;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                    Batas SLA ≤ 5 hari kerja
                </span>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 12px; background: {{ ($queueStats['sla_warning'] ?? 0) > 0 ? '#FEE2E2' : '#D1FAE5' }}; color: {{ ($queueStats['sla_warning'] ?? 0) > 0 ? '#DC2626' : '#059669' }}; display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>
            </div>
        </div>
    </div>

    {{-- 2. QUICK FILTER STATUS PILLS --}}
    <div style="display: flex; gap: 0.5rem; align-items: center; overflow-x: auto; padding-bottom: 0.25rem;">
        <span style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-600, #475569); margin-right: 0.25rem;">Filter Status:</span>
        @php
            $currentStatus = request('status', '');
            $makeFilterUrl = function($statusVal) {
                $params = request()->except('status', 'page');
                if ($statusVal !== '') {
                    $params['status'] = $statusVal;
                }
                return route('admin.pendaftaran.index', $params);
            };
        @endphp

        <a href="{{ $makeFilterUrl('') }}" class="filter-pill {{ $currentStatus === '' ? 'active' : '' }}">
            <span>Semua Status</span>
            <span class="pill-count">{{ $queueStats['total'] ?? $pendaftaranList->total() }}</span>
        </a>

        <a href="{{ $makeFilterUrl('diajukan') }}" class="filter-pill {{ $currentStatus === 'diajukan' ? 'active' : '' }}">
            <span>Diajukan</span>
            <span class="pill-count">{{ $queueStats['diajukan'] ?? 0 }}</span>
        </a>

        <a href="{{ $makeFilterUrl('perlu_perbaikan') }}" class="filter-pill {{ $currentStatus === 'perlu_perbaikan' ? 'active' : '' }}">
            <span>Perlu Perbaikan</span>
            <span class="pill-count">{{ $queueStats['perlu_perbaikan'] ?? 0 }}</span>
        </a>

        <a href="{{ $makeFilterUrl('waiting_list') }}" class="filter-pill {{ $currentStatus === 'waiting_list' ? 'active' : '' }}">
            <span>Waiting List</span>
            <span class="pill-count">{{ $queueStats['waiting_list'] ?? 0 }}</span>
        </a>

        <a href="{{ $makeFilterUrl('diterima') }}" class="filter-pill {{ $currentStatus === 'diterima' ? 'active' : '' }}">
            <span>Diterima</span>
            <span class="pill-count">{{ $queueStats['diterima'] ?? 0 }}</span>
        </a>

        <a href="{{ $makeFilterUrl('ditolak') }}" class="filter-pill {{ $currentStatus === 'ditolak' ? 'active' : '' }}">
            <span>Ditolak</span>
            <span class="pill-count">{{ $queueStats['ditolak'] ?? 0 }}</span>
        </a>
    </div>

    {{-- 2.5 QUICK FILTER SLA WATCHDOG PILLS --}}
    <div style="display: flex; gap: 0.5rem; align-items: center; overflow-x: auto; padding-bottom: 0.25rem;">
        <span style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-600, #475569); margin-right: 0.25rem;">Watchdog SLA:</span>
        @php
            $currentSla = request('sla', '');
            $makeSlaUrl = function($slaVal) {
                $params = request()->except('sla', 'page');
                if ($slaVal !== '') {
                    $params['sla'] = $slaVal;
                }
                return route('admin.pendaftaran.index', $params);
            };
        @endphp

        <a href="{{ $makeSlaUrl('') }}" class="filter-pill {{ $currentSla === '' ? 'active' : '' }}">
            <span>Semua SLA</span>
        </a>

        <a href="{{ $makeSlaUrl('mendekati') }}" class="filter-pill {{ $currentSla === 'mendekati' ? 'active' : '' }}" style="{{ $currentSla !== 'mendekati' ? 'border-color: #FCD34D; color: #92400E;' : '' }}">
            <span>Mendekati Batas (3–4 Hari)</span>
            <span class="pill-count" style="background: #FEF3C7; color: #92400E;">{{ $queueStats['sla_mendekati'] ?? 0 }}</span>
        </a>

        <a href="{{ $makeSlaUrl('breach') }}" class="filter-pill {{ $currentSla === 'breach' ? 'active' : '' }}" style="{{ $currentSla !== 'breach' ? 'border-color: #FECACA; color: #DC2626;' : '' }}">
            <span>Melebihi SLA (> 5 Hari)</span>
            <span class="pill-count" style="background: #FEE2E2; color: #B91C1C;">{{ $queueStats['sla_breach'] ?? 0 }}</span>
        </a>
    </div>

    {{-- 3. DETAILED FILTER TOOLBAR --}}
    <div class="admin-card" style="padding: 1.25rem 1.5rem;">
        <form method="GET" action="{{ route('admin.pendaftaran.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            {{-- Keep status parameter if set via pills --}}
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <div style="display: flex; flex-direction: column; gap: 0.375rem; flex: 2; min-width: 260px;">
                <label for="search-input" style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Pencarian Calon Peserta</label>
                <div style="position: relative;">
                    <input type="text" id="search-input" name="q" value="{{ request('q') }}" placeholder="Cari nama, no. registrasi (#REG-XXXXX), institusi, atau NIM..." style="width: 100%; height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem 0 2.4rem; font-size: 0.875rem; background: #ffffff; color: var(--neutral-900, #0F172A); transition: border-color 0.15s ease;" onfocus="this.style.borderColor='#0641CC'" onblur="this.style.borderColor='var(--neutral-300, #CBD5E1)'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 0.85rem; top: 13px; color: var(--neutral-400, #94A3B8);">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.375rem; flex: 1; min-width: 180px;">
                <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Periode Bulan Magang</label>
                <select name="bulan_magang_id" style="height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff; color: var(--neutral-800, #1E293B);">
                    <option value="">Semua Periode</option>
                    @foreach($bulanList as $bulan)
                        <option value="{{ $bulan->id }}" {{ request('bulan_magang_id') == $bulan->id ? 'selected' : '' }}>
                            {{ $bulan->bulan_mulai?->translatedFormat('F Y') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.375rem; flex: 1; min-width: 180px;">
                <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Jurusan Pilihan</label>
                <select name="jurusan_id" style="height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff; color: var(--neutral-800, #1E293B);">
                    <option value="">Semua Jurusan</option>
                    @foreach($jurusanList as $jur)
                        <option value="{{ $jur->id }}" {{ request('jurusan_id') == $jur->id ? 'selected' : '' }}>
                            {{ $jur->nama_jurusan }} ({{ strtoupper($jur->kategori) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.375rem; flex: 1; min-width: 140px;">
                <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Jalur Magang</label>
                <select name="jalur" style="height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff; color: var(--neutral-800, #1E293B);">
                    <option value="">Semua Jalur</option>
                    <option value="umum" {{ request('jalur') === 'umum' ? 'selected' : '' }}>Jalur Umum</option>
                    <option value="khusus" {{ request('jalur') === 'khusus' ? 'selected' : '' }}>Jalur Khusus</option>
                </select>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.375rem; flex: 1; min-width: 190px;">
                <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Urutan Antrean</label>
                <select name="sort" style="height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff; color: var(--neutral-800, #1E293B);">
                    <option value="fifo" {{ request('sort', 'fifo') === 'fifo' ? 'selected' : '' }}>FIFO (Terawal Masuk - Default)</option>
                    <option value="terbaru" {{ request('sort') === 'terbaru' ? 'selected' : '' }}>Paling Baru Dibuat</option>
                    <option value="terlama" {{ request('sort') === 'terlama' ? 'selected' : '' }}>Paling Lama Dibuat</option>
                </select>
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" style="height: 42px; padding: 0 1.25rem; background: #0641CC; color: #ffffff; font-weight: 600; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer; transition: all 0.15s ease; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 1px 3px rgba(6, 65, 204, 0.2);">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    <span>Filter</span>
                </button>
                <a href="{{ route('admin.pendaftaran.index') }}" style="height: 42px; padding: 0 1rem; background: var(--neutral-100, #F1F5F9); color: var(--neutral-700, #334155); font-weight: 600; font-size: 0.875rem; text-decoration: none; border-radius: 8px; display: inline-flex; align-items: center; border: 1px solid var(--neutral-200, #E2E8F0); transition: background 0.15s ease;">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- 4. DATA TABLE CARD --}}
    <div class="admin-card">
        @if($pendaftaranList->isEmpty())
            <div style="padding: 4rem 1.5rem; text-align: center; color: var(--neutral-600, #64748B);">
                <div style="width: 64px; height: 64px; background: #EFF6FF; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem auto; color: #1D4ED8;">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </div>
                <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-900, #0F172A); margin-bottom: 0.375rem;">
                    Tidak Ada Berkas yang Cocok
                </h3>
                <p style="font-size: 0.875rem; max-width: 480px; margin: 0 auto 1.5rem auto; line-height: 1.5;">
                    Tidak ditemukan berkas pendaftaran calon peserta magang yang sesuai dengan kriteria filter atau pencarian saat ini.
                </p>
                <a href="{{ route('admin.pendaftaran.index') }}" style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.5rem 1.25rem; background: #0641CC; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 0.875rem;">
                    Reset Semua Filter
                </a>
            </div>
        @else
            <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem; background: #ffffff;">
                <div style="display: flex; align-items: center; gap: 0.6rem;">
                    <span style="font-weight: 700; font-size: 0.9375rem; color: var(--neutral-900, #0F172A);">
                        Daftar Antrean Berkas Masuk
                    </span>
                    <span style="padding: 0.2rem 0.6rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: #EFF6FF; color: #1D4ED8;">
                        {{ $pendaftaranList->total() }} Peserta
                    </span>
                </div>
                <div style="font-size: 0.8125rem; color: var(--neutral-500, #64748B);">
                    Menampilkan {{ $pendaftaranList->firstItem() }}–{{ $pendaftaranList->lastItem() }} dari {{ $pendaftaranList->total() }} pengajuan
                </div>
            </div>

            <div style="overflow-x: auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 140px;">No. Registrasi</th>
                            <th>Calon Peserta</th>
                            <th>Instansi & Jurusan</th>
                            <th>Periode Magang</th>
                            <th>Status & SLA</th>
                            <th style="text-align: right; width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendaftaranList as $item)
                            @php
                                $val = $item->status->value ?? (string)$item->status;
                                $badgeStyle = match($val) {
                                    'berkas_belum_lengkap' => ['bg' => '#F1F5F9', 'color' => '#475569', 'border' => '#CBD5E1', 'icon' => 'file-text'],
                                    'diajukan' => ['bg' => '#FEF3C7', 'color' => '#92400E', 'border' => '#FCD34D', 'icon' => 'clock'],
                                    'perlu_perbaikan' => ['bg' => '#FFEDD5', 'color' => '#C2410C', 'border' => '#FDBA74', 'icon' => 'edit-3'],
                                    'waiting_list' => ['bg' => '#E0F2FE', 'color' => '#0369A1', 'border' => '#BAE6FD', 'icon' => 'users'],
                                    'diterima' => ['bg' => '#D1FAE5', 'color' => '#065F46', 'border' => '#A7F3D0', 'icon' => 'check-circle'],
                                    'ditolak' => ['bg' => '#FEE2E2', 'color' => '#991B1B', 'border' => '#FECACA', 'icon' => 'x-circle'],
                                    default => ['bg' => '#F1F5F9', 'color' => '#475569', 'border' => '#CBD5E1', 'icon' => 'info'],
                                };

                                // Hitung SLA jika status diajukan
                                $slaDays = null;
                                if ($val === 'diajukan' && $item->diajukan_at) {
                                    $slaDays = (int) $item->diajukan_at->diffInDays(now());
                                }
                            @endphp
                            <tr>
                                {{-- No. Pendaftaran --}}
                                <td>
                                    <span style="display: inline-block; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size: 0.8125rem; font-weight: 700; color: #0641CC; background: #EFF6FF; padding: 0.25rem 0.55rem; border-radius: 6px; border: 1px solid #DBEAFE;">
                                        {{ $item->nomor_pendaftaran }}
                                    </span>
                                    <div style="font-size: 0.72rem; color: var(--neutral-500, #64748B); margin-top: 0.25rem;">
                                        {{ $item->diajukan_at ? $item->diajukan_at->translatedFormat('d M Y, H:i') : ($item->created_at?->translatedFormat('d M Y') ?? '-') }}
                                    </div>
                                </td>

                                {{-- Calon Peserta --}}
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div style="width: 38px; height: 38px; border-radius: 10px; background: linear-gradient(135deg, #0641CC 0%, #2563EB 100%); color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.875rem; flex-shrink: 0; box-shadow: 0 1px 3px rgba(6, 65, 204, 0.25);">
                                            {{ strtoupper(substr($item->user?->nama ?? 'P', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; color: var(--neutral-900, #0F172A); font-size: 0.875rem;">
                                                {{ $item->user?->nama ?? 'Calon Peserta' }}
                                            </div>
                                            <div style="font-size: 0.78rem; color: var(--neutral-500, #64748B); margin-top: 0.1rem;">
                                                {{ $item->user?->email }}
                                            </div>
                                            @if($item->user?->profil?->nim_nisn)
                                                <div style="font-size: 0.72rem; color: var(--neutral-400, #94A3B8);">
                                                    NIM/NISN: {{ $item->user->profil->nim_nisn }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Instansi & Jurusan --}}
                                <td>
                                    <div style="font-weight: 600; color: var(--neutral-900, #0F172A); font-size: 0.85rem;">
                                        {{ $item->asal_instansi }}
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 0.4rem; margin-top: 0.25rem; flex-wrap: wrap;">
                                        <span style="color: var(--neutral-600, #475569); font-size: 0.8125rem;">
                                            {{ $item->kuota?->jurusan?->nama_jurusan ?? ($item->user?->profil?->jurusan?->nama_jurusan ?? '-') }}
                                        </span>
                                        @php
                                            $kategori = $item->kuota?->jurusan?->kategori ?? ($item->user?->profil?->jurusan?->kategori ?? '');
                                        @endphp
                                        @if($kategori)
                                            <span style="font-size: 0.68rem; font-weight: 700; text-transform: uppercase; padding: 0.15rem 0.4rem; border-radius: 4px; background: #F1F5F9; color: #475569;">
                                                {{ $kategori === 'smk' ? 'SMK' : 'Mahasiswa' }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Periode & Durasi --}}
                                <td>
                                    <div style="font-weight: 700; color: var(--neutral-800, #1E293B); font-size: 0.85rem;">
                                        {{ $item->bulanMagang?->bulan_mulai?->translatedFormat('F Y') ?? '-' }}
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 0.35rem; margin-top: 0.2rem; font-size: 0.75rem; color: var(--neutral-500, #64748B);">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                        <span>Durasi: <strong>{{ $item->durasi_bulan }} Bulan</strong></span>
                                    </div>
                                </td>

                                {{-- Status & SLA --}}
                                <td>
                                    <div style="display: flex; flex-direction: column; gap: 0.35rem; align-items: flex-start;">
                                        <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.25rem 0.65rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: {{ $badgeStyle['bg'] }}; color: {{ $badgeStyle['color'] }}; border: 1px solid {{ $badgeStyle['border'] }};">
                                            <span>{{ $item->status->label() }}</span>
                                        </span>

                                        @if($val === 'diajukan' && $slaDays !== null)
                                            @if($slaDays >= 5)
                                                <span style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.7rem; font-weight: 800; color: #991B1B; background: #FEE2E2; padding: 0.15rem 0.5rem; border-radius: 4px; border: 1px solid #FECACA;" title="SLA Terlampaui: > 5 Hari Kerja (PRD Bab 2.3)">
                                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                                    <span>Breach SLA ({{ $slaDays }} Hari)</span>
                                                </span>
                                            @elseif($slaDays >= 3)
                                                <span style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.7rem; font-weight: 700; color: #92400E; background: #FEF3C7; padding: 0.15rem 0.5rem; border-radius: 4px; border: 1px solid #FCD34D;" title="Mendekati Batas SLA Peninjauan">
                                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                                    <span>Mendekati Batas ({{ $slaDays }} Hari)</span>
                                                </span>
                                            @else
                                                <span style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.7rem; font-weight: 600; color: #065F46; background: #D1FAE5; padding: 0.15rem 0.5rem; border-radius: 4px;">
                                                    <span>SLA Aman ({{ $slaDays }} Hari)</span>
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                </td>

                                {{-- Aksi --}}
                                <td style="text-align: right;">
                                    <a href="{{ route('admin.pendaftaran.show', $item) }}" class="btn-tinjau">
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
                <div style="padding: 1.25rem 1.5rem; border-top: 1px solid var(--neutral-200, #E2E8F0); background: #ffffff;">
                    {{ $pendaftaranList->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
