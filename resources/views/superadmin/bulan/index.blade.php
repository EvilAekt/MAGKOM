@extends('layouts.dashboard')

@section('title', 'Bulan Magang - SuperAdmin MagKom Sleman')
@section('topbar_title', 'Manajemen Bulan Magang')

@section('breadcrumbs')
    <a href="{{ route('superadmin.dashboard') }}">SuperAdmin</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Bulan Magang</span>
@endsection

@push('styles')
    @vite(['resources/css/pages/superadmin/superadmin-common.css'])
@endpush

@section('content')
<div class="sa-fluid-container" style="display: flex; flex-direction: column; gap: 1.5rem;">

    {{-- 1. Hero Banner Khas Sleman MagKom --}}
    <div class="sa-hero-banner sc-reveal-hero" data-scroll-reveal>
        <div class="banner-top-row">
            <div>
                <h1 class="banner-title">Manajemen Bulan Magang</h1>
                <div class="banner-subtitle">
                    <span>Cakupan: <strong>Jadwal &amp; Siklus Pendaftaran</strong></span>
                    <span class="bullet-separator">&bull;</span>
                    <span>Total Periode: <strong>{{ $totalBulan }} Bulan Terdata</strong></span>
                </div>
            </div>
            <div>
                <button type="button" class="btn-banner-action btn-trigger-tambah-bulan">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    <span>Tambah Bulan Magang</span>
                </button>
            </div>
        </div>
    </div>

    {{-- 2. Grid 4 Kartu Statistik Mengadopsi Gentelella Tile-Stat --}}
    <div class="tiles-grid sc-reveal-up sc-delay-1" data-scroll-reveal>
        <!-- 1. Total Periode Bulan -->
        <x-gentelella.tile-stat
            title="TOTAL PERIODE"
            value="{{ $totalBulan }}"
            color="blue"
            trendType="info"
            trendDirection="up"
            trendText="Periode"
            subText="Riwayat Bulan Magang Terdaftar"
            :sparkBars="[30, 45, 60, 75, 85, 95]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <!-- 2. Periode Aktif -->
        <x-gentelella.tile-stat
            title="PERIODE AKTIF"
            value="{{ $totalAktif }}"
            color="teal"
            trendType="success"
            trendDirection="up"
            trendText="Operasional"
            subText="Bulan Magang Berstatus Aktif"
            :sparkBars="[25, 40, 55, 70, 80, 90]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <!-- 3. Pendaftaran Terbuka -->
        <x-gentelella.tile-stat
            title="PENDAFTARAN BUKA"
            value="{{ $pendaftaranBuka }}"
            color="amber"
            trendType="warning"
            trendDirection="up"
            trendText="Registrasi"
            subText="Sedang Masuk Rentang Buka"
            :sparkBars="[20, 35, 50, 65, 80, 85]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 14 14"></polyline>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <!-- 4. Override Status -->
        <x-gentelella.tile-stat
            title="OVERRIDE STATUS"
            value="{{ $totalOverride }}"
            color="purple"
            trendType="danger"
            trendDirection="up"
            trendText="Manual"
            subText="Periode Dikontrol Khusus"
            :sparkBars="[15, 25, 40, 55, 70, 85]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <line x1="4" y1="21" x2="4" y2="14"></line>
                    <line x1="4" y1="10" x2="4" y2="3"></line>
                    <line x1="12" y1="21" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12" y2="3"></line>
                    <line x1="20" y1="21" x2="20" y2="16"></line>
                    <line x1="20" y1="12" x2="20" y2="3"></line>
                    <line x1="1" y1="14" x2="7" y2="14"></line>
                    <line x1="9" y1="8" x2="15" y2="8"></line>
                    <line x1="17" y1="16" x2="23" y2="16"></line>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>
    </div>

    <!-- DATA TABLE CARD -->
    <x-gentelella.x-panel class="has-table sc-reveal-up sc-delay-2" data-scroll-reveal title="Daftar Bulan Magang">
        <x-slot:toolbox>
            <span style="font-size: 0.8125rem; color: var(--neutral-600); font-weight: 600;">
                Total: <strong>{{ method_exists($bulanList, 'total') ? $bulanList->total() : $bulanList->count() }} Periode</strong>
            </span>
        </x-slot:toolbox>
        <div class="table-responsive-gentelella" style="overflow-x: auto;">
            <table class="table jambo_table bulk_action" style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                <thead>
                    <tr class="headings">
                        <th>Bulan Magang</th>
                        <th>Jadwal Buka Pendaftaran</th>
                        <th>Override Status</th>
                        <th style="width: 130px; text-align: center;">Status Aktif</th>
                        <th style="text-align: right; width: 200px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bulanList as $bulan)
                        <tr class="sa-table-row" style="border-bottom: 1px solid var(--neutral-200);">
                            <td style="padding: 1rem 1.25rem; font-weight: 700; font-size: 0.9375rem;">
                                <div class="sa-text-accent-blue" style="display: flex; align-items: center; gap: 0.5rem; font-weight: 700;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line></svg>
                                    {{ $bulan->bulan_mulai?->translatedFormat('F Y') }}
                                </div>
                                <div style="font-size: 0.75rem; color: var(--neutral-500); font-weight: 500; margin-top: 0.2rem;">
                                    ID #{{ $bulan->id }}
                                </div>
                            </td>
                            <td style="padding: 1rem 1rem; color: var(--neutral-700); font-size: 0.8125rem;">
                                <div style="font-weight: 600; color: var(--neutral-800);">
                                    {{ $bulan->tgl_buka?->format('d M Y') }} s/d {{ $bulan->tgl_tutup?->format('d M Y') }}
                                </div>
                            </td>
                            <td style="padding: 1rem 1rem; white-space: nowrap;">
                                @if($bulan->override_status)
                                    <span class="sa-badge-blue">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="3"></circle></svg>
                                        Override: {{ strtoupper($bulan->override_status->value) }}
                                    </span>
                                @else
                                    <span class="sa-badge-neutral">
                                        <span style="width: 6px; height: 6px; border-radius: 50%; background: var(--neutral-400);"></span>
                                        Otomatis (Jadwal)
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 1rem 1rem; text-align: center; white-space: nowrap;">
                                @if($bulan->aktif)
                                    <span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.3rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: var(--green-100); color: var(--green-700); border: 1px solid rgba(22, 163, 74, 0.2);">
                                        <span style="width: 6px; height: 6px; border-radius: 50%; background: var(--green-600);"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.3rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: var(--red-100); color: var(--red-700); border: 1px solid rgba(220, 38, 38, 0.2);">
                                        <span style="width: 6px; height: 6px; border-radius: 50%; background: var(--red-600);"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 1rem 1.25rem; text-align: right; white-space: nowrap;">
                                <div style="display: inline-flex; gap: 0.5rem; align-items: center; justify-content: flex-end;">
                                    <button type="button" 
                                            class="sa-btn-action-outline btn-trigger-edit-bulan"
                                            data-id="{{ $bulan->id }}"
                                            data-bulan-mulai="{{ $bulan->bulan_mulai?->format('Y-m-d') }}"
                                            data-tgl-buka="{{ $bulan->tgl_buka?->format('Y-m-d') }}"
                                            data-tgl-tutup="{{ $bulan->tgl_tutup?->format('Y-m-d') }}"
                                            data-override-status="{{ $bulan->override_status?->value ?? '' }}"
                                            data-action-url="{{ route('superadmin.bulan.update', $bulan) }}">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                        Edit
                                    </button>

                                    <button type="button" 
                                            class="btn-trigger-toggle-bulan {{ $bulan->aktif ? 'sa-btn-action-danger' : 'sa-btn-action-outline' }}" 
                                            style="{{ $bulan->aktif ? '' : 'color: var(--green-700); border-color: rgba(22, 163, 74, 0.25);' }}"
                                            data-id="{{ $bulan->id }}"
                                            data-nama="{{ $bulan->nama_bulan ?? $bulan->bulan_mulai?->format('F Y') }}"
                                            data-status-aktif="{{ $bulan->aktif ? '1' : '0' }}"
                                            data-action-url="{{ route('superadmin.bulan.toggle', $bulan) }}">
                                        {{ $bulan->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding: 4rem 1.5rem; text-align: center; color: var(--neutral-600);">
                                <div style="width: 48px; height: 48px; margin: 0 auto 1rem; border-radius: 50%; background: var(--neutral-100); display: flex; align-items: center; justify-content: center; color: var(--neutral-500);">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line></svg>
                                </div>
                                <p style="font-size: 1rem; font-weight: 600; color: var(--neutral-800); margin: 0 0 0.25rem 0;">Belum ada data bulan magang.</p>
                                <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0;">Klik tombol <strong>Tambah Bulan Magang</strong> untuk memulai gelombang pendaftaran.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bulanList->total() > 0)
            <div style="padding: 1.25rem 1.5rem; border-top: 1px solid var(--neutral-200); background: var(--neutral-100);">
                {{ $bulanList->links() }}
            </div>
        @endif
    </x-gentelella.x-panel>
</div>

<!-- =====================================================================
     MODAL TAMBAH BULAN MAGANG
     ===================================================================== -->
<div id="modal-tambah-bulan" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
    <div style="background: var(--neutral-0); border-radius: 16px; max-width: 500px; width: 100%; padding: 2rem; box-shadow: var(--shadow-modal); border: 1px solid var(--neutral-400); position: relative;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.875rem;">
            <div style="display: flex; align-items: center; gap: 0.625rem;">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: var(--blue-tint); color: var(--blue-700); display: flex; align-items: center; justify-content: center;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                </div>
                <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Tambah Bulan Magang</h3>
            </div>
            <button type="button" data-modal-dismiss="modal-tambah-bulan" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--neutral-500); line-height: 1; padding: 0.25rem;">&times;</button>
        </div>

        <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0 0 1.25rem 0; line-height: 1.4;">
            Buat jadwal pembukaan bulan magang baru untuk calon peserta dinas.
        </p>

        <form method="POST" action="{{ route('superadmin.bulan.store') }}" style="display: flex; flex-direction: column; gap: 1.25rem;">
            @csrf
            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Tanggal Bulan Mulai Magang <span style="color: var(--red-600);">*</span>
                </label>
                <input type="date" name="bulan_mulai" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                <small style="color: var(--neutral-600); font-size: 0.75rem; margin-top: 0.25rem; display: block;">Pilih tanggal awal bulan magang (misal: 01/11/2026).</small>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        Tanggal Buka <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="date" name="tgl_buka" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        Tanggal Tutup <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="date" name="tgl_tutup" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                </div>
            </div>

            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Override Status Ketersediaan (Opsional)
                </label>
                <select name="override_status" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; background: var(--neutral-0); font-family: inherit;">
                    <option value="">-- Otomatis Berdasarkan Jadwal --</option>
                    @foreach($statusEnum as $status)
                        <option value="{{ $status->value }}">{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem; border-top: 1px solid var(--neutral-200); padding-top: 1rem;">
                <button type="button" data-modal-dismiss="modal-tambah-bulan" style="height: 40px; padding: 0 1.25rem; background: var(--neutral-100); border: 1px solid var(--neutral-400); border-radius: 8px; font-weight: 700; font-size: 0.875rem; color: var(--neutral-700); cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="height: 40px; padding: 0 1.5rem; background: var(--blue-600); color: #FFFFFF; border: none; border-radius: 8px; font-weight: 700; font-size: 0.875rem; cursor: pointer; box-shadow: var(--shadow-subtle);">
                    Simpan Bulan Magang
                </button>
            </div>
        </form>
    </div>
</div>

<!-- =====================================================================
     MODAL EDIT BULAN MAGANG
     ===================================================================== -->
<div id="modal-edit-bulan" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
    <div style="background: var(--neutral-0); border-radius: 16px; max-width: 500px; width: 100%; padding: 2rem; box-shadow: var(--shadow-modal); border: 1px solid var(--neutral-400); position: relative;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.875rem;">
            <div style="display: flex; align-items: center; gap: 0.625rem;">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: var(--blue-tint); color: var(--blue-700); display: flex; align-items: center; justify-content: center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                </div>
                <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Perbarui Bulan Magang</h3>
            </div>
            <button type="button" data-modal-dismiss="modal-edit-bulan" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--neutral-500); line-height: 1; padding: 0.25rem;">&times;</button>
        </div>

        <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0 0 1.25rem 0; line-height: 1.4;">
            Sesuaikan jadwal pendaftaran atau ubah status override ketersediaan.
        </p>

        <form method="POST" id="form-edit-bulan" action="" style="display: flex; flex-direction: column; gap: 1.25rem;">
            @csrf
            @method('PUT')
            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Tanggal Bulan Mulai Magang <span style="color: var(--red-600);">*</span>
                </label>
                <input type="date" id="edit-bulan-mulai" name="bulan_mulai" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        Tanggal Buka <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="date" id="edit-tgl-buka" name="tgl_buka" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        Tanggal Tutup <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="date" id="edit-tgl-tutup" name="tgl_tutup" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                </div>
            </div>

            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Override Status Ketersediaan
                </label>
                <select id="edit-override-status" name="override_status" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; background: var(--neutral-0); font-family: inherit;">
                    <option value="">-- Otomatis Berdasarkan Jadwal --</option>
                    @foreach($statusEnum as $status)
                        <option value="{{ $status->value }}">{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem; border-top: 1px solid var(--neutral-200); padding-top: 1rem;">
                <button type="button" data-modal-dismiss="modal-edit-bulan" style="height: 40px; padding: 0 1.25rem; background: var(--neutral-100); border: 1px solid var(--neutral-400); border-radius: 8px; font-weight: 700; font-size: 0.875rem; color: var(--neutral-700); cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="height: 40px; padding: 0 1.5rem; background: var(--blue-600); color: #FFFFFF; border: none; border-radius: 8px; font-weight: 700; font-size: 0.875rem; cursor: pointer; box-shadow: var(--shadow-subtle);">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- =====================================================================
     MODAL KONFIRMASI STATUS BULAN MAGANG (NONAKTIFKAN / AKTIFKAN)
     Sesuai MagKom Design System v1.0 & Bebas Native Alert Pop-up
     ===================================================================== -->
<div id="modalKonfirmasiToggleBulan" 
     style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;"
     role="dialog" 
     aria-modal="true" 
     aria-labelledby="modalToggleBulanTitle">
    <div style="background: var(--neutral-0, #FFFFFF); border-radius: 16px; width: 100%; max-width: 480px; padding: 2rem; box-shadow: var(--shadow-modal, 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)); position: relative; border: 1px solid var(--neutral-300, #CBD5E1);">
        
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.25rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div id="toggleBulanIconWrap" style="width: 42px; height: 42px; border-radius: 10px; background: #FEE2E2; color: #DC2626; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                </div>
                <div>
                    <h3 id="modalToggleBulanTitle" style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0; line-height: 1.3;">
                        Ubah Status Bulan Magang
                    </h3>
                    <span style="font-size: 0.8125rem; color: var(--neutral-500, #64748B);">
                        Pengendalian Gelombang Pendaftaran Peserta
                    </span>
                </div>
            </div>
            <button type="button" 
                    data-modal-dismiss="modalKonfirmasiToggleBulan" 
                    style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--neutral-400, #94A3B8); line-height: 1; padding: 0.25rem; border-radius: 6px;" 
                    title="Tutup dialog">
                &times;
            </button>
        </div>

        <form id="formToggleBulan" method="POST" style="display: flex; flex-direction: column; gap: 1.25rem; margin: 0;">
            @csrf
            @method('PATCH')
            <div id="toggleBulanBoxAlert" style="background: #FFF1F2; border: 1px solid #FECDD3; border-radius: 10px; padding: 1rem 1.25rem;">
                <p id="toggleBulanMessage" style="font-size: 0.875rem; color: #9F1239; margin: 0 0 0.5rem 0; line-height: 1.5;">
                    Apakah Anda yakin ingin menonaktifkan periode bulan magang ini?
                </p>
                <div style="background: rgba(255, 255, 255, 0.7); border-radius: 6px; padding: 0.5rem 0.75rem; border: 1px solid rgba(220, 38, 38, 0.15);">
                    <div style="font-weight: 700; color: var(--neutral-900, #0F172A); font-size: 0.875rem;" id="toggleBulanNama">Periode Bulan</div>
                </div>
                <p id="toggleBulanConsequence" style="font-size: 0.75rem; color: #BE123C; margin: 0.65rem 0 0 0; line-height: 1.4;">
                    • Menonaktifkan bulan ini akan menutup akses pendaftaran calon peserta baru pada siklus terkait.
                </p>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--neutral-200, #E2E8F0); padding-top: 1rem;">
                <button type="button" 
                        data-modal-dismiss="modalKonfirmasiToggleBulan" 
                        style="height: 40px; padding: 0 1.25rem; background: var(--neutral-100, #F1F5F9); border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; font-weight: 700; font-size: 0.875rem; color: var(--neutral-700, #334155); cursor: pointer;">
                    Batal
                </button>
                <button type="submit" 
                        id="btnSubmitToggleBulan" 
                        style="height: 40px; padding: 0 1.5rem; background: #DC2626; border: none; border-radius: 8px; font-weight: 700; font-size: 0.875rem; color: #FFFFFF; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem; transition: background 0.15s ease;">
                    <span id="btnSubmitToggleBulanText">Ya, Nonaktifkan Bulan</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    @vite(['resources/js/pages/superadmin/bulan-index.js'])
@endpush
@endsection
