@extends('layouts.dashboard')

@section('title', 'Slot Khusus Internal - SuperAdmin MagKom')
@section('topbar_title', 'Manajemen Slot Khusus Internal')

@section('breadcrumbs')
    <a href="{{ route('superadmin.dashboard') }}">SuperAdmin</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Slot Khusus</span>
@endsection

@push('styles')
    @vite(['resources/css/pages/superadmin/superadmin-common.css'])
@endpush

@push('scripts')
    @vite(['resources/js/pages/superadmin/slot-khusus.js'])
@endpush

@section('content')
<div class="sa-fluid-container" style="display: flex; flex-direction: column; gap: 1.5rem;">

    {{-- ALERT KREDENSIAL AKUN BARU JALUR KHUSUS --}}
    @if(session('akun_baru_dibuat'))
        <div style="border-top: 3.5px solid #10B981; background: #ECFDF5; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; border-radius: 8px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.12);">
            <div style="display: flex; align-items: flex-start; gap: 0.85rem;">
                <div style="width: 42px; height: 42px; border-radius: 10px; background: #D1FAE5; color: #059669; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                </div>
                <div>
                    <h3 style="font-size: 1rem; font-weight: 800; color: #065F46; margin: 0;">
                        Akun Peserta Jalur Khusus Berhasil Dibuat &amp; Ditempatkan!
                    </h3>
                    <p style="font-size: 0.8125rem; color: #047857; margin: 0.25rem 0 0.5rem 0;">
                        Kredensial login berikut telah aktif dan tersimpan terenkripsi pada sistem.
                    </p>
                    <div style="display: inline-flex; align-items: center; gap: 1rem; background: #ffffff; border: 1px solid #A7F3D0; border-radius: 8px; padding: 0.5rem 0.85rem; font-size: 0.8125rem; color: #1E293B; font-family: monospace;">
                        <span><strong>Email:</strong> {{ session('email_peserta') }}</span>
                        <span style="color: #CBD5E1;">|</span>
                        <span><strong>Password Awal:</strong> <span style="font-weight: 700; color: #0641CC;">{{ session('password_baru') }}</span></span>
                    </div>
                </div>
            </div>
            <button type="button" 
                    class="sa-btn-copy-kredensial" 
                    style="padding: 0.55rem 1rem; font-size: 0.8125rem;"
                    data-nama="{{ session('nama_peserta') }}"
                    data-email="{{ session('email_peserta') }}"
                    data-sandi="{{ session('password_baru') }}"
                    data-status="aktif"
                    data-login-url="{{ route('login') }}"
                    aria-label="Salin Kredensial Baru">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                <span>Salin Kredensial</span>
            </button>
        </div>
    @endif

    {{-- 1. Hero Banner Khas Sleman MagKom --}}
    <div class="sa-hero-banner sc-reveal-hero" data-scroll-reveal>
        <div class="banner-top-row">
            <div>
                <h1 class="banner-title">Manajemen Slot Khusus Internal</h1>
                <div class="banner-subtitle">
                    <span>Cakupan: <strong>Diskresi &amp; Kemitraan Khusus</strong></span>
                    <span class="bullet-separator">&bull;</span>
                    <span>Alokasi: <strong>Terpisah dari Kuota Reguler</strong></span>
                </div>
            </div>
            <div class="banner-actions-group">
                <a href="{{ route('superadmin.peserta-khusus.create') }}" class="btn-banner-action--secondary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="8.5" cy="7" r="4"></circle>
                        <line x1="20" y1="8" x2="20" y2="14"></line>
                        <line x1="23" y1="11" x2="17" y2="11"></line>
                    </svg>
                    <span>Tambah Peserta Khusus</span>
                </a>
                <button type="button" class="btn-banner-action" onclick="document.getElementById('modalTambahSlot').style.display='flex'">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    <span>Tambah Slot Khusus</span>
                </button>
            </div>
        </div>
    </div>

    {{-- 2. Grid 4 Kartu Statistik Mengadopsi Gentelella Tile-Stat --}}
    <div class="tiles-grid sc-reveal-up sc-delay-1" data-scroll-reveal>
        <!-- 1. Total Alokasi Slot -->
        <x-gentelella.tile-stat
            title="TOTAL ALOKASI SLOT"
            value="{{ $totalAlokasiSlot }}"
            color="blue"
            trendType="info"
            trendDirection="up"
            trendText="Alokasi"
            subText="Total Kuota Khusus Disediakan"
            :sparkBars="[30, 45, 60, 75, 85, 95]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <!-- 2. Slot Terisi -->
        <x-gentelella.tile-stat
            title="SLOT TERISI"
            value="{{ $totalSlotTerisi }}"
            color="teal"
            trendType="success"
            trendDirection="up"
            trendText="Terpakai"
            subText="Peserta Khusus Terverifikasi"
            :sparkBars="[25, 40, 55, 70, 80, 90]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <polyline points="16 11 18 13 22 9"></polyline>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <!-- 3. Sisa Kuota Khusus -->
        <x-gentelella.tile-stat
            title="SISA KUOTA KHUSUS"
            value="{{ $sisaSlotKhusus }}"
            color="amber"
            trendType="warning"
            trendDirection="up"
            trendText="Tersedia"
            subText="Slot Kosong Siap Dialokasikan"
            :sparkBars="[20, 35, 50, 65, 80, 85]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 14 14"></polyline>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <!-- 4. Default Slot Bulanan -->
        <x-gentelella.tile-stat
            title="DEFAULT BULANAN"
            value="{{ $defaultSlot }} Slot"
            color="purple"
            trendType="info"
            trendDirection="up"
            trendText="Baseline"
            subText="Alokasi Default per Bulan Baru"
            :sparkBars="[15, 25, 40, 55, 70, 85]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>
    </div>

    <!-- DATA TABLE CARD -->
    <x-gentelella.x-panel class="has-table sc-reveal-up sc-delay-2" data-scroll-reveal title="Daftar Alokasi Slot Khusus Mitra">
        <x-slot:toolbox>
            <span style="font-size: 0.8125rem; color: var(--neutral-600); font-weight: 600;">
                Total: <strong>{{ method_exists($slotList, 'total') ? $slotList->total() : $slotList->count() }} Slot</strong>
            </span>
        </x-slot:toolbox>

        @if($slotList->isEmpty())
            <div style="padding: 4rem 1.5rem; text-align: center; color: var(--neutral-600);">
                <div style="width: 48px; height: 48px; margin: 0 auto 1rem; border-radius: 50%; background: var(--neutral-100); display: flex; align-items: center; justify-content: center; color: var(--neutral-500);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                </div>
                <p style="font-size: 1rem; font-weight: 600; color: var(--neutral-800); margin: 0 0 0.25rem 0;">Belum ada alokasi slot khusus internal yang dibuat.</p>
                <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0;">Klik tombol <strong>Tambah Slot Khusus</strong> untuk mengalokasikan kuota mitra.</p>
            </div>
        @else
            <div class="table-responsive-gentelella" style="overflow-x: auto;">
                <table class="table jambo_table bulk_action" style="width: 100%; border-collapse: collapse; font-size: 0.875rem; text-align: left;">
                    <thead>
                        <tr class="headings">
                            <th>Instansi / Keterangan</th>
                            <th style="width: 180px;">Bulan Magang</th>
                            <th style="text-align: center; width: 140px;">Kapasitas Slot</th>
                            <th style="text-align: center; width: 140px;">Terpakai</th>
                            <th style="width: 130px; text-align: center;">Status</th>
                            <th style="text-align: right; width: 160px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($slotList as $slot)
                            <tr class="sa-table-row" style="border-bottom: 1px solid var(--neutral-200);">
                                <td style="padding: 1rem 1.25rem;">
                                    <div style="font-weight: 700; color: var(--neutral-900); font-size: 0.9375rem;">
                                        {{ $slot->nama_instansi ?? 'Slot Khusus Internal' }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.25rem; line-height: 1.4;">
                                        Dibuat oleh: {{ $slot->creator?->nama ?? 'SuperAdmin' }}
                                    </div>
                                </td>
                                <td style="padding: 1rem 1rem; white-space: nowrap;">
                                    <span class="sa-text-accent-blue font-bold text-sm" style="display: inline-flex; align-items: center; gap: 0.375rem;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line></svg>
                                        {{ $slot->bulan_mulai?->translatedFormat('F Y') }}
                                    </span>
                                </td>
                                <td style="padding: 1rem 1rem; text-align: center;">
                                    <span class="sa-text-slot-val">
                                        {{ $slot->jumlah }} Slot
                                    </span>
                                </td>
                                <td style="padding: 1rem 1rem; text-align: center;">
                                    <span class="sa-text-slot-val {{ $slot->terisi >= $slot->jumlah ? 'is-full' : '' }}">
                                        {{ $slot->terisi }} Slot
                                    </span>
                                </td>
                                <td style="padding: 1rem 1rem; text-align: center; white-space: nowrap;">
                                    @if($slot->aktif)
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
                                    <form action="{{ route('superadmin.slot-khusus.toggle', $slot) }}" method="POST" style="display: inline-block; margin: 0;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" onclick="return confirm('{{ $slot->aktif ? 'Nonaktifkan slot khusus ini?' : 'Aktifkan kembali slot khusus ini?' }}')" class="{{ $slot->aktif ? 'sa-btn-action-danger' : 'sa-btn-action-outline' }}" style="{{ $slot->aktif ? '' : 'color: var(--green-700); border-color: rgba(22, 163, 74, 0.35);' }}">
                                            {{ $slot->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($slotList->total() > 0)
                <div style="padding: 1.25rem 1.5rem; border-top: 1px solid var(--neutral-200); background: var(--neutral-100);">
                    {{ $slotList->appends(request()->query())->links() }}
                </div>
            @endif
        @endif
    </x-gentelella.x-panel>

    {{-- TABEL 2: DAFTAR PESERTA MAGANG JALUR KHUSUS AKTIF --}}
    <x-gentelella.x-panel class="has-table" title="Daftar Peserta Magang Jalur Khusus">
        <x-slot:toolbox>
            <span style="font-size: 0.8125rem; color: var(--neutral-600); font-weight: 600;">
                Total: <strong>{{ method_exists($pesertaKhususList, 'total') ? $pesertaKhususList->total() : $pesertaKhususList->count() }} Peserta</strong>
            </span>
        </x-slot:toolbox>

        @if($pesertaKhususList->isEmpty())
            <div style="padding: 4rem 1.5rem; text-align: center; color: var(--neutral-600);">
                <div style="width: 48px; height: 48px; margin: 0 auto 1rem; border-radius: 50%; background: var(--neutral-100); display: flex; align-items: center; justify-content: center; color: var(--neutral-500);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <p style="font-size: 1rem; font-weight: 600; color: var(--neutral-800); margin: 0 0 0.25rem 0;">Belum ada peserta magang jalur khusus.</p>
                <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0;">Gunakan tombol <strong>Tambah Peserta Khusus</strong> di atas untuk mendaftarkan peserta jalur penugasan.</p>
            </div>
        @else
            <div class="table-responsive-gentelella" style="overflow-x: auto;">
                <table class="table jambo_table bulk_action" style="width: 100%; border-collapse: collapse; font-size: 0.875rem; text-align: left;">
                    <thead>
                        <tr class="headings">
                            <th>Peserta Magang</th>
                            <th>Institusi &amp; Program Studi</th>
                            <th>Penempatan &amp; Pembimbing</th>
                            <th>Periode Magang</th>
                            <th style="text-align: center; width: 140px;">Slot Bulan</th>
                            <th style="width: 130px; text-align: center;">Status</th>
                            <th style="width: 175px; text-align: right;">Kredensial Awal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pesertaKhususList as $penempatan)
                            <tr class="sa-table-row" style="border-bottom: 1px solid var(--neutral-200);">
                                <td style="padding: 1rem 1.25rem;">
                                    <div style="font-weight: 700; color: var(--neutral-900); font-size: 0.9375rem;">
                                        {{ $penempatan->peserta?->nama ?? 'Nama Tidak Terdata' }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.2rem; display: flex; align-items: center; gap: 0.35rem;">
                                        <span>{{ $penempatan->peserta?->email ?? '-' }}</span>
                                    </div>
                                    <div style="margin-top: 0.35rem;">
                                        <span class="sa-badge-blue" style="font-size: 0.6875rem; padding: 0.15rem 0.5rem;">
                                            Jalur Khusus
                                        </span>
                                    </div>
                                </td>
                                <td style="padding: 1rem 1rem;">
                                    <div style="font-weight: 600; color: var(--neutral-800); font-size: 0.875rem;">
                                        {{ $penempatan->peserta?->profil?->institusi ?? 'Institusi Belum Tercatat' }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.25rem;">
                                        {{ $penempatan->peserta?->profil?->jurusan?->nama_jurusan ?? ($penempatan->peserta?->profil?->jenjang?->label() ?? 'Umum') }}
                                        @if($penempatan->peserta?->profil?->nim_nisn)
                                            <span style="opacity: 0.7;">&bull; NIM: {{ $penempatan->peserta->profil->nim_nisn }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td style="padding: 1rem 1rem;">
                                    <div style="font-weight: 600; color: var(--neutral-800); font-size: 0.875rem;">
                                        {{ $penempatan->bidang?->nama_bidang ?? 'Bidang Belum Ditentukan' }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.25rem; display: flex; align-items: center; gap: 0.25rem;">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                        <span>{{ $penempatan->pembimbing?->nama ?? 'Belum Ditugaskan' }}</span>
                                    </div>
                                </td>
                                <td style="padding: 1rem 1rem; white-space: nowrap;">
                                    <div style="font-weight: 600; color: var(--neutral-800); font-size: 0.8125rem;">
                                        {{ $penempatan->tgl_mulai?->translatedFormat('d M Y') ?? '-' }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--neutral-500); margin-top: 0.15rem;">
                                        s/d {{ $penempatan->tgl_selesai?->translatedFormat('d M Y') ?? '-' }}
                                    </div>
                                </td>
                                <td style="padding: 1rem 1rem; text-align: center; white-space: nowrap;">
                                    <span class="sa-text-accent-blue font-bold text-sm" style="display: inline-flex; align-items: center; gap: 0.375rem;">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line></svg>
                                        {{ $penempatan->slotKhusus?->bulan_mulai?->translatedFormat('M Y') ?? '-' }}
                                    </span>
                                </td>
                                <td style="padding: 1rem 1rem; text-align: center; white-space: nowrap;">
                                    @if($penempatan->status?->value === 'aktif' || $penempatan->status === \App\Enums\StatusPenempatanEnum::Aktif)
                                        <span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.3rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: var(--green-100); color: var(--green-700); border: 1px solid rgba(22, 163, 74, 0.2);">
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: var(--green-600);"></span>
                                            Aktif
                                        </span>
                                    @elseif($penempatan->status?->value === 'selesai' || $penempatan->status === \App\Enums\StatusPenempatanEnum::Selesai)
                                        <span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.3rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: var(--blue-100); color: var(--blue-700); border: 1px solid rgba(59, 130, 246, 0.2);">
                                            Selesai
                                        </span>
                                    @else
                                        <span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.3rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: var(--neutral-100); color: var(--neutral-700); border: 1px solid var(--neutral-300);">
                                            {{ $penempatan->status?->label() ?? ucfirst((string)$penempatan->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 1rem 1.25rem; text-align: right; white-space: nowrap;">
                                    <button type="button" 
                                            class="sa-btn-copy-kredensial" 
                                            data-nama="{{ $penempatan->peserta?->nama }}"
                                            data-email="{{ $penempatan->peserta?->email }}"
                                            data-sandi="{{ $penempatan->peserta?->password_awal ?? '' }}"
                                            data-status="{{ $penempatan->peserta?->must_change_password ? 'aktif' : 'changed' }}"
                                            data-login-url="{{ route('login') }}"
                                            title="Salin Akun &amp; Kata Sandi Awal untuk WhatsApp">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                        <span>Salin Akun</span>
                                    </button>
                                    <div style="margin-top: 0.35rem;">
                                        @if($penempatan->peserta?->must_change_password)
                                            <span class="sa-badge-cred-active" title="Peserta belum aktivasi / belum mengganti kata sandi awal">
                                                <span style="width: 5px; height: 5px; border-radius: 50%; background: #D97706;"></span>
                                                Sandi Awal Aktif
                                            </span>
                                        @else
                                            <span class="sa-badge-cred-changed" title="Peserta sudah memperbarui kata sandi pribadi; sandi awal tidak berlaku lagi untuk login">
                                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                Sandi Diubah Peserta
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($pesertaKhususList->total() > 0)
                <div style="padding: 1.25rem 1.5rem; border-top: 1px solid var(--neutral-200); background: var(--neutral-100);">
                    {{ $pesertaKhususList->appends(request()->query())->links() }}
                </div>
            @endif
        @endif
    </x-gentelella.x-panel>
</div>

<!-- =====================================================================
     MODAL TAMBAH SLOT KHUSUS
     ===================================================================== -->
<div id="modalTambahSlot" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
    <div style="background: var(--neutral-0); border-radius: 16px; width: 100%; max-width: 500px; padding: 2rem; box-shadow: var(--shadow-modal); position: relative; border: 1px solid var(--neutral-400);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.875rem;">
            <div style="display: flex; align-items: center; gap: 0.625rem;">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: var(--blue-tint); color: var(--blue-700); display: flex; align-items: center; justify-content: center;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                </div>
                <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Tambah Slot Khusus Internal</h3>
            </div>
            <button type="button" onclick="document.getElementById('modalTambahSlot').style.display='none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--neutral-500); line-height: 1; padding: 0.25rem;">&times;</button>
        </div>

        <form action="{{ route('superadmin.slot-khusus.store') }}" method="POST" style="display: flex; flex-direction: column; gap: 1.25rem;">
            @csrf
            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Nama Instansi Mitra <span style="color: var(--red-600);">*</span>
                </label>
                <input type="text" name="nama_instansi" required placeholder="Contoh: BSSN, Diskominfo DIY, UGM Lab" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        Bulan Mulai <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="date" name="bulan_mulai" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        Jumlah Kuota Slot <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="number" name="jumlah" value="{{ $defaultSlot ?? 3 }}" min="0" max="100" required placeholder="Contoh: 3" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                </div>
            </div>

            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Keterangan & Catatan MoU
                </label>
                <textarea name="keterangan" rows="3" placeholder="Nomor surat / dasar penugasan kerjasama..." style="width: 100%; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0.75rem; font-size: 0.875rem; font-family: inherit; line-height: 1.4; resize: vertical;"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem; border-top: 1px solid var(--neutral-200); padding-top: 1rem;">
                <button type="button" onclick="document.getElementById('modalTambahSlot').style.display='none'" style="height: 40px; padding: 0 1.25rem; background: var(--neutral-100); border: 1px solid var(--neutral-400); border-radius: 8px; font-weight: 700; font-size: 0.875rem; color: var(--neutral-700); cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="height: 40px; padding: 0 1.5rem; background: var(--blue-600); color: #FFFFFF; border: none; border-radius: 8px; font-weight: 700; font-size: 0.875rem; cursor: pointer; box-shadow: var(--shadow-subtle);">
                    Simpan Slot Khusus
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
