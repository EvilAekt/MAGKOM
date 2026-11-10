@extends('layouts.dashboard')

@section('title', 'Manajemen Staf Internal - SuperAdmin MagKom')
@section('topbar_title', 'Manajemen Staf Internal')

@section('breadcrumbs')
    <a href="{{ route('superadmin.dashboard') }}">SuperAdmin</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Manajemen Staf</span>
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
                <h1 class="banner-title">Manajemen Staf Internal</h1>
                <div class="banner-subtitle">
                    <span>Cakupan: <strong>Admin Verifikator &amp; Pembimbing</strong></span>
                    <span class="bullet-separator">&bull;</span>
                    <span>Total Staf: <strong>{{ $totalStaf }} Akun Terdaftar</strong></span>
                </div>
            </div>
            <div>
                <button type="button" class="btn-banner-action btn-trigger-tambah-staf">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
                    <span>Tambah Staf Baru</span>
                </button>
            </div>
        </div>
    </div>

    {{-- 2. Grid 4 Kartu Statistik Mengadopsi Gentelella Tile-Stat --}}
    <div class="tiles-grid sc-reveal-up sc-delay-1" data-scroll-reveal>
        <!-- 1. Total Staf Internal -->
        <x-gentelella.tile-stat
            title="TOTAL STAF"
            value="{{ $totalStaf }}"
            color="blue"
            trendType="info"
            trendDirection="up"
            trendText="Internal"
            subText="Staf Aktif Dinas Kominfo"
            :sparkBars="[35, 50, 65, 75, 85, 95]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 00-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 010 7.75"></path>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <!-- 2. Admin Verifikator -->
        <x-gentelella.tile-stat
            title="ADMIN VERIFIKATOR"
            value="{{ $totalAdmin }}"
            color="teal"
            trendType="success"
            trendDirection="up"
            trendText="Dinas"
            subText="Verifikasi & Seleksi Berkas"
            :sparkBars="[20, 35, 50, 65, 80, 90]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <!-- 3. Pembimbing Lapangan -->
        <x-gentelella.tile-stat
            title="PEMBIMBING LAPANGAN"
            value="{{ $totalPembimbing }}"
            color="amber"
            trendType="warning"
            trendDirection="up"
            trendText="Lapangan"
            subText="Pendamping Mentoring & Unit Kerja"
            :sparkBars="[30, 45, 60, 70, 80, 95]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <!-- 4. Menunggu Ganti Sandi -->
        <x-gentelella.tile-stat
            title="MENUNGGU GANTI SANDI"
            value="{{ $stafPendingPasswordCount }}"
            color="purple"
            trendType="danger"
            trendDirection="up"
            trendText="Atensi"
            subText="Wajib Ganti Password Awal"
            :sparkBars="[15, 25, 40, 55, 70, 85]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>
    </div>

    <!-- 3. FILTER & TOOLBAR CARD -->
    <div class="staf-filter-card sc-reveal-up sc-delay-2" data-scroll-reveal>
        <form method="GET" action="{{ route('superadmin.staf.index') }}" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; justify-content: space-between;">
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; flex: 1;">
                <div style="position: relative; min-width: 240px; flex: 1;">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email staf..." style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem 0 2.5rem; font-size: 0.875rem; font-family: inherit;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position: absolute; left: 0.875rem; top: 50%; transform: translateY(-50%); color: var(--neutral-500);"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </div>

                <select name="role" style="height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; background: var(--neutral-0); font-family: inherit; min-width: 180px;">
                    <option value="">Semua Peran Staf</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin Verifikator</option>
                    <option value="pembimbing" {{ request('role') === 'pembimbing' ? 'selected' : '' }}>Pembimbing Lapangan</option>
                </select>

                <select name="status_aktif" style="height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; background: var(--neutral-0); font-family: inherit; min-width: 140px;">
                    <option value="">Semua Status</option>
                    <option value="true" {{ request('status_aktif') === 'true' ? 'selected' : '' }}>Aktif</option>
                    <option value="false" {{ request('status_aktif') === 'false' ? 'selected' : '' }}>Nonaktif</option>
                </select>

                <select name="must_change_password" style="height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; background: var(--neutral-0); font-family: inherit; min-width: 170px;">
                    <option value="">Status Kata Sandi</option>
                    <option value="true" {{ request('must_change_password') === 'true' ? 'selected' : '' }}>Menunggu Ganti Sandi</option>
                    <option value="false" {{ request('must_change_password') === 'false' ? 'selected' : '' }}>Sudah Diganti</option>
                </select>

                <button type="submit" class="sa-btn-filter">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                    Filter
                </button>

                @if(request()->hasAny(['search', 'role', 'status_aktif', 'must_change_password']))
                    <a href="{{ route('superadmin.staf.index') }}" style="font-size: 0.8125rem; color: var(--neutral-600); text-decoration: none; padding: 0.5rem 0.75rem; font-weight: 600;">
                        Reset Filter
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 2. DATA TABLE CARD -->
    <x-gentelella.x-panel class="has-table sc-reveal-up sc-delay-3" data-scroll-reveal title="Daftar Akun Pengguna Internal">
        <x-slot:toolbox>
            <span style="font-size: 0.8125rem; color: var(--neutral-600); font-weight: 600;">
                Total: <strong>{{ method_exists($stafList, 'total') ? $stafList->total() : $stafList->count() }} Pengguna</strong>
            </span>
        </x-slot:toolbox>

        @if($stafList->isEmpty())
            <div style="padding: 4rem 1.5rem; text-align: center; color: var(--neutral-600);">
                <div style="width: 48px; height: 48px; margin: 0 auto 1rem; border-radius: 50%; background: var(--neutral-100); display: flex; align-items: center; justify-content: center; color: var(--neutral-500);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                </div>
                <p style="font-size: 1rem; font-weight: 600; color: var(--neutral-800); margin: 0 0 0.25rem 0;">Tidak ada data staf internal yang ditemukan.</p>
                <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0;">Silakan sesuaikan kriteria pencarian atau buat akun staf baru.</p>
            </div>
        @else
            <div class="table-responsive-gentelella" style="overflow-x: auto;">
                <table class="table jambo_table bulk_action" style="width: 100%; border-collapse: collapse; font-size: 0.875rem; text-align: left;">
                    <thead>
                        <tr class="headings">
                            <th>Nama & Email</th>
                            <th style="width: 220px;">Peran Akses</th>
                            <th>Unit Kerja / Bidang</th>
                            <th style="width: 130px; text-align: center;">Status Akun</th>
                            <th style="text-align: right; width: 240px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stafList as $staf)
                            @php
                                $badgeClass = match($staf->role->value) {
                                    'admin' => 'sa-badge-purple',
                                    'pembimbing' => 'sa-badge-blue',
                                    default => 'sa-badge-neutral',
                                };
                            @endphp
                            <tr class="sa-table-row" style="border-bottom: 1px solid var(--neutral-200);">
                                <td style="padding: 1rem 1.25rem;">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div class="{{ $badgeClass }}" style="width: 36px; height: 36px; border-radius: 50%; padding: 0; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.875rem; flex-shrink: 0;">
                                            {{ strtoupper(substr($staf->nama, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; color: var(--neutral-900); font-size: 0.9375rem;">
                                                {{ $staf->nama }}
                                            </div>
                                            <div style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.15rem;">
                                                {{ $staf->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 1rem 1rem; white-space: nowrap;">
                                    <span class="{{ $badgeClass }}">
                                        {{ $staf->role->label() }}
                                    </span>
                                    @if($staf->role->value === 'pembimbing')
                                        <div style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-700); margin-top: 0.35rem; display: flex; align-items: center; gap: 0.25rem;">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                                            {{ $staf->mentee_aktif_count ?? 0 }} Mentee Aktif
                                            @if($maksPeserta > 0)
                                                <span style="color: {{ ($staf->mentee_aktif_count ?? 0) >= $maksPeserta ? 'var(--red-600)' : 'var(--neutral-500)' }};">
                                                    / {{ $maksPeserta }} maks
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td style="padding: 1rem 1rem;">
                                    @if($staf->bidang)
                                        <span style="font-weight: 600; color: var(--neutral-800); font-size: 0.8125rem;">
                                            {{ $staf->bidang->nama_bidang }}
                                        </span>
                                    @else
                                        <span style="color: var(--neutral-500); font-size: 0.8125rem;">
                                            Seluruh Bidang (Lintas Unit)
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 1rem 1rem; text-align: center; white-space: nowrap;">
                                    @if($staf->status_aktif)
                                        <span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.3rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: var(--green-100); color: var(--green-700); border: 1px solid rgba(220, 38, 38, 0.2);">
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: var(--green-600);"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.3rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: var(--red-100); color: var(--red-700); border: 1px solid rgba(220, 38, 38, 0.2);">
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: var(--red-600);"></span>
                                            Nonaktif
                                        </span>
                                    @endif

                                    @if($staf->must_change_password)
                                        <div style="margin-top: 0.35rem;">
                                            <span style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.68rem; font-weight: 700; color: #b45309; background: #fef3c7; border: 1px solid #fde68a; padding: 0.15rem 0.45rem; border-radius: 4px;">
                                                ⚠️ Wajib Ganti Sandi
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td style="padding: 1rem 1.25rem; text-align: right; white-space: nowrap;">
                                    <div style="display: inline-flex; gap: 0.375rem; align-items: center; justify-content: flex-end;">
                                        <!-- Edit Profil Staf (AC-11-1) -->
                                        <button type="button" 
                                                class="sa-btn-action-outline btn-trigger-edit-staf"
                                                data-id="{{ $staf->id }}"
                                                data-nama="{{ $staf->nama }}"
                                                data-email="{{ $staf->email }}"
                                                data-role="{{ $staf->role->value }}"
                                                data-bidang-id="{{ $staf->bidang_id ?? '' }}"
                                                data-action-url="{{ route('superadmin.staf.update', $staf) }}">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                            Edit
                                        </button>

                                        <!-- Regenerate Temporary Password Button (BR-15) -->
                                        <form action="{{ route('superadmin.staf.regenerate-password', $staf) }}" method="POST" style="display: inline-block; margin: 0;" onsubmit="return confirm('Buat kata sandi sementara baru untuk akun staf ini? Sesi aktif staf akan ditutup dan staf wajib mengganti kata sandi saat login.');">
                                            @csrf
                                            <button type="submit" class="sa-btn-action-ghost" title="Buat kata sandi sementara baru secara instan">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                                                Sandi Baru
                                            </button>
                                        </form>

                                        <!-- Reset Password Manual Form Button -->
                                        <button type="button" 
                                                class="sa-btn-action-outline btn-trigger-reset-staf"
                                                data-id="{{ $staf->id }}"
                                                data-nama="{{ $staf->nama }}"
                                                data-action-url="{{ route('superadmin.staf.reset-password', $staf) }}">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                            Reset
                                        </button>

                                        <!-- Toggle Status Form -->
                                        <form action="{{ route('superadmin.staf.toggle', $staf) }}" method="POST" style="display: inline-block; margin: 0;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" onclick="return confirm('{{ $staf->status_aktif ? 'Nonaktifkan akun staf ini?' : 'Aktifkan kembali akun staf ini?' }}')" class="{{ $staf->status_aktif ? 'sa-btn-action-danger' : 'sa-btn-action-outline' }}" style="{{ $staf->status_aktif ? '' : 'color: var(--green-700); border-color: rgba(22, 163, 74, 0.25);' }}">
                                                {{ $staf->status_aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($stafList->total() > 0)
                <div style="padding: 1.25rem 1.5rem; border-top: 1px solid var(--neutral-200); background: var(--neutral-100);">
                    {{ $stafList->links() }}
                </div>
            @endif
        @endif
    </x-gentelella.x-panel>
</div>

<!-- =====================================================================
     MODAL TAMBAH STAF BARU
     ===================================================================== -->
<div id="modalTambahStaf" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
    <div style="background: var(--neutral-0); border-radius: 16px; width: 100%; max-width: 520px; padding: 2rem; box-shadow: var(--shadow-modal); position: relative; border: 1px solid var(--neutral-400);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.875rem;">
            <div style="display: flex; align-items: center; gap: 0.625rem;">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: var(--blue-tint); color: var(--blue-700); display: flex; align-items: center; justify-content: center;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                </div>
                <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Tambah Akun Staf Baru</h3>
            </div>
            <button type="button" data-modal-dismiss="modalTambahStaf" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--neutral-500); line-height: 1; padding: 0.25rem;">&times;</button>
        </div>

        <form action="{{ route('superadmin.staf.store') }}" method="POST" style="display: flex; flex-direction: column; gap: 1.25rem;">
            @csrf
            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Nama Lengkap Staf <span style="color: var(--red-600);">*</span>
                </label>
                <input type="text" name="nama" required placeholder="Contoh: Budi Santoso, S.Kom" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
            </div>

            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Alamat Email Akun Staf (Google / Gmail) <span style="color: var(--red-600);">*</span>
                </label>
                <input type="email" name="email" required placeholder="budi.santoso@gmail.com" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr; gap: 1rem;" id="gridRoleBidangTambah">
                <div>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        Peran Akses <span style="color: var(--red-600);">*</span>
                    </label>
                    <select name="role" id="tambahRole" onchange="toggleBidangField('tambah')" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; background: var(--neutral-0); font-family: inherit;">
                        <option value="admin" selected>Admin Verifikator</option>
                        <option value="pembimbing">Pembimbing Lapangan</option>
                    </select>
                </div>
                <div id="wrapBidangTambah" style="display: none;">
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        Bidang Unit Kerja <span style="color: var(--red-600);">*</span>
                    </label>
                    <select name="bidang_id" id="tambahBidang" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; background: var(--neutral-0); font-family: inherit;">
                        <option value="">-- Pilih Bidang Unit Kerja --</option>
                        @foreach($bidangList as $b)
                            <option value="{{ $b->id }}">{{ $b->nama_bidang }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Kata Sandi Awal <span style="color: var(--red-600);">*</span>
                </label>
                <input type="password" name="password" required placeholder="Minimal 8 karakter (huruf & angka)" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                <span style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.375rem; display: block; line-height: 1.4;">
                    Staf akan diwajibkan mengganti kata sandi pada saat pertama kali login (flag <code>must_change_password</code> aktif).
                </span>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem; border-top: 1px solid var(--neutral-200); padding-top: 1rem;">
                <button type="button" data-modal-dismiss="modalTambahStaf" style="height: 40px; padding: 0 1.25rem; background: var(--neutral-100); border: 1px solid var(--neutral-400); border-radius: 8px; font-weight: 700; font-size: 0.875rem; color: var(--neutral-700); cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="height: 40px; padding: 0 1.5rem; background: var(--blue-600); color: #FFFFFF; border: none; border-radius: 8px; font-weight: 700; font-size: 0.875rem; cursor: pointer; box-shadow: var(--shadow-subtle);">
                    Simpan Staf
                </button>
            </div>
        </form>
    </div>
</div>

<!-- =====================================================================
     MODAL RESET SANDI STAF
     ===================================================================== -->
<div id="modalResetSandi" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
    <div style="background: var(--neutral-0); border-radius: 16px; width: 100%; max-width: 460px; padding: 2rem; box-shadow: var(--shadow-modal); position: relative; border: 1px solid var(--neutral-400);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.875rem;">
            <div style="display: flex; align-items: center; gap: 0.625rem;">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: var(--amber-100); color: var(--amber-700); display: flex; align-items: center; justify-content: center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                </div>
                <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Reset Kata Sandi Staf</h3>
            </div>
            <button type="button" data-modal-dismiss="modalResetSandi" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--neutral-500); line-height: 1; padding: 0.25rem;">&times;</button>
        </div>

        <form id="formResetSandi" method="POST" style="display: flex; flex-direction: column; gap: 1.25rem;">
            @csrf
            <div style="background: var(--neutral-100); padding: 0.875rem 1rem; border-radius: 8px; border: 1px solid var(--neutral-300);">
                <p style="font-size: 0.8125rem; color: var(--neutral-700); margin: 0; line-height: 1.4;">
                    Mereset kata sandi untuk akun staf: <strong id="resetNamaStaf" style="color: var(--neutral-900);">-</strong>. Akun akan ditandai wajib ganti sandi saat login berikutnya.
                </p>
            </div>

            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Kata Sandi Baru <span style="color: var(--red-600);">*</span>
                </label>
                <input type="password" name="password_baru" required placeholder="Minimal 8 karakter kombinasi huruf & angka" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem; border-top: 1px solid var(--neutral-200); padding-top: 1rem;">
                <button type="button" data-modal-dismiss="modalResetSandi" style="height: 40px; padding: 0 1.25rem; background: var(--neutral-100); border: 1px solid var(--neutral-400); border-radius: 8px; font-weight: 700; font-size: 0.875rem; color: var(--neutral-700); cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="height: 40px; padding: 0 1.5rem; background: var(--blue-600); color: #FFFFFF; border: none; border-radius: 8px; font-weight: 700; font-size: 0.875rem; cursor: pointer; box-shadow: var(--shadow-subtle);">
                    Simpan Sandi Baru
                </button>
            </div>
        </form>
    </div>
</div>

<!-- =====================================================================
     MODAL EDIT STAF (AC-11-1)
     ===================================================================== -->
<div id="modalEditStaf" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
    <div style="background: var(--neutral-0); border-radius: 16px; width: 100%; max-width: 520px; padding: 2rem; box-shadow: var(--shadow-modal); position: relative; border: 1px solid var(--neutral-400);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.875rem;">
            <div style="display: flex; align-items: center; gap: 0.625rem;">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: var(--blue-tint); color: var(--blue-700); display: flex; align-items: center; justify-content: center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                </div>
                <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Edit Data Profil Staf</h3>
            </div>
            <button type="button" data-modal-dismiss="modalEditStaf" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--neutral-500); line-height: 1; padding: 0.25rem;">&times;</button>
        </div>

        <form id="formEditStaf" method="POST" style="display: flex; flex-direction: column; gap: 1.25rem;">
            @csrf
            @method('PUT')
            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Nama Lengkap Staf <span style="color: var(--red-600);">*</span>
                </label>
                <input type="text" id="editNama" name="nama" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
            </div>

            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Alamat Email Akun Staf (Google / Gmail) <span style="color: var(--red-600);">*</span>
                </label>
                <input type="email" id="editEmail" name="email" required placeholder="budi.santoso@gmail.com" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
            </div>

            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Peran Akses Sistem <span style="color: var(--red-600);">*</span>
                </label>
                <select id="editRole" name="role" onchange="toggleBidangField('edit')" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; background: var(--neutral-0); font-family: inherit;">
                    <option value="admin">Administrasi Kominfo (Verifikator)</option>
                    <option value="pembimbing">Pembimbing Lapangan</option>
                </select>
            </div>

            <div id="wrapBidangEdit">
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Bidang Penugasan Dinas <span style="color: var(--red-600);">*</span>
                </label>
                <select id="editBidang" name="bidang_id" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; background: var(--neutral-0); font-family: inherit;">
                    <option value="">-- Pilih Bidang Unit Kerja --</option>
                    @foreach($bidangList as $b)
                        <option value="{{ $b->id }}">{{ $b->nama_bidang }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem; border-top: 1px solid var(--neutral-200); padding-top: 1rem;">
                <button type="button" data-modal-dismiss="modalEditStaf" style="height: 40px; padding: 0 1.25rem; background: var(--neutral-100); border: 1px solid var(--neutral-400); border-radius: 8px; font-weight: 700; font-size: 0.875rem; color: var(--neutral-700); cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="height: 40px; padding: 0 1.5rem; background: var(--blue-600); color: #FFFFFF; border: none; border-radius: 8px; font-weight: 700; font-size: 0.875rem; cursor: pointer; box-shadow: var(--shadow-subtle);">
                    Perbarui Profil
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    @vite(['resources/js/pages/superadmin/staf-index.js'])
@endpush
@endsection
