@extends('layouts.dashboard')

@section('title', __('Jalur Khusus - Administrasi MagKom Sleman'))
@section('topbar_title', __('Manajemen Jalur Khusus'))

@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}">{{ __('Administrasi') }}</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">{{ __('Jalur Khusus') }}</span>
@endsection

@push('styles')
    @vite(['resources/css/pages/superadmin/superadmin-common.css'])
@endpush

@push('scripts')
    @vite(['resources/js/pages/admin/slot-khusus.js'])
@endpush

@section('content')
<div class="sa-fluid-container" style="display: flex; flex-direction: column; gap: 1.5rem;">

    {{-- Banner Khusus Mode Lihat Saja jika diakses SuperAdmin (SEC-12) --}}
    @if(auth()->user()?->isSuperAdmin())
        <div class="alert-readonly-banner" style="background: #EFF6FF; border: 1px solid #BFDBFE; border-left: 4px solid #2563EB; border-radius: 8px; padding: 1rem 1.25rem; display: flex; align-items: center; gap: 0.75rem;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            <div>
                <strong style="color: #1E40AF; display: block; font-size: 0.875rem;">{{ __('Mode Lihat Saja (Read-Only)') }}</strong>
                <span style="color: #1E3A8A; font-size: 0.8125rem;">{{ __('Akun SuperAdmin hanya memiliki hak akses pemantauan pada modul jalur khusus. Alokasi slot dan pendaftaran peserta dikelola oleh Administrator.') }}</span>
            </div>
        </div>
    @endif

    {{-- ALERT KREDENSIAL AKUN BARU JALUR KHUSUS --}}
    @if(session('akun_baru_dibuat'))
        <div style="border-top: 3.5px solid #10B981; background: #ECFDF5; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; border-radius: 8px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.12);">
            <div style="display: flex; align-items: flex-start; gap: 0.85rem;">
                <div style="width: 42px; height: 42px; border-radius: 10px; background: #D1FAE5; color: #059669; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                </div>
                <div>
                    <h3 style="font-size: 1rem; font-weight: 800; color: #065F46; margin: 0;">
                        {{ __('Akun Peserta Jalur Khusus Berhasil Dibuat & Ditempatkan!') }}
                    </h3>
                    <p style="font-size: 0.8125rem; color: #047857; margin: 0.25rem 0 0.5rem 0;">
                        {{ __('Kredensial login berikut telah aktif dan tersimpan terenkripsi pada sistem.') }}
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
                    aria-label="{{ __('Salin Kredensial Baru') }}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                <span>{{ __('Salin Kredensial') }}</span>
            </button>
        </div>
    @endif

    {{-- 1. Hero Banner Khas Sleman MagKom --}}
    <div class="sa-hero-banner sc-reveal-hero" data-scroll-reveal>
        <div class="banner-top-row">
            <div>
                <h1 class="banner-title">{{ __('Manajemen Slot Khusus Internal') }}</h1>
                <div class="banner-subtitle">
                    <span>{{ __('Cakupan:') }} <strong>{!! __('Diskresi & Kemitraan Khusus') !!}</strong></span>
                    <span class="bullet-separator">&bull;</span>
                    <span>{{ __('Alokasi:') }} <strong>{!! __('Terpisah dari Kuota Reguler') !!}</strong></span>
                </div>
            </div>
            @if(! auth()->user()?->isSuperAdmin())
                <div class="banner-actions-group">
                    <a href="{{ route('admin.peserta-khusus.create') }}" class="btn-banner-action--secondary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="8.5" cy="7" r="4"></circle>
                            <line x1="20" y1="8" x2="20" y2="14"></line>
                            <line x1="23" y1="11" x2="17" y2="11"></line>
                        </svg>
                        <span>{{ __('Tambah Peserta Khusus') }}</span>
                    </a>
                    <button type="button" class="btn-banner-action btn-trigger-tambah-slot" id="btnOpenTambahSlot">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        <span>{{ __('Tambah Alokasi Slot') }}</span>
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- 2. Grid 3 Kartu Statistik Khas Sleman MagKom --}}
    <div class="tiles-grid tiles-grid--3-cols sc-reveal-up sc-delay-1" data-scroll-reveal>
        <x-gentelella.tile-stat
            title="{{ __('TOTAL ALOKASI') }}"
            value="{{ $totalAlokasiSlot }}"
            color="blue"
            trendType="info"
            trendDirection="up"
            trendText="{{ __('Slot') }}"
            subText="{{ __('Total Alokasi Slot Khusus') }}"
            :sparkBars="[30, 45, 60, 75, 85, 95]">
            <x-slot:icon>
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <x-gentelella.tile-stat
            title="{{ __('SLOT TERPAKAI') }}"
            value="{{ $totalSlotTerisi }}"
            color="purple"
            trendType="warning"
            trendDirection="up"
            trendText="{{ __('Peserta') }}"
            subText="{{ __('Peserta Magang Terplot') }}"
            :sparkBars="[20, 35, 50, 65, 80, 85]">
            <x-slot:icon>
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <x-gentelella.tile-stat
            title="{{ __('SISA SLOT') }}"
            value="{{ $sisaSlotKhusus }}"
            color="teal"
            trendType="success"
            trendDirection="up"
            trendText="{{ __('Tersedia') }}"
            subText="{{ __('Siap Digunakan Dinas') }}"
            :sparkBars="[40, 50, 65, 75, 85, 90]">
            <x-slot:icon>
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>
    </div>

    {{-- TABEL 1: DAFTAR ALOKASI SLOT KHUSUS --}}
    <x-gentelella.x-panel class="has-table sc-reveal-up sc-delay-2" data-scroll-reveal title="{{ __('Daftar Alokasi Slot Khusus Mitra') }}">
        <x-slot:toolbox>
            <span style="font-size: 0.8125rem; color: var(--neutral-600); font-weight: 600;">
                {{ __('Total:') }} <strong>{{ $slotList->total() }} {{ __('Alokasi Slot') }}</strong>
            </span>
        </x-slot:toolbox>

        @if($slotList->isEmpty())
            <div style="padding: 4rem 1.5rem; text-align: center; color: var(--neutral-600);">
                <div style="width: 48px; height: 48px; margin: 0 auto 1rem; border-radius: 50%; background: var(--neutral-100); display: flex; align-items: center; justify-content: center; color: var(--neutral-500);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                </div>
                <p style="font-size: 1rem; font-weight: 600; color: var(--neutral-800); margin: 0 0 0.25rem 0;">{{ __('Belum ada alokasi slot khusus internal.') }}</p>
                <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0;">{!! __('Gunakan tombol <strong>Tambah Alokasi Slot</strong> untuk membuat kuota kemitraan.') !!}</p>
            </div>
        @else
            <div class="table-responsive-gentelella" style="overflow-x: auto;">
                <table class="table jambo_table bulk_action" style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                    <thead>
                        <tr class="headings">
                            <th>{{ __('Instansi Mitra') }}</th>
                            <th>{{ __('Bulan Mulai') }}<span class="sr-only"> {{ __('Bulan Magang') }}</span></th>
                            <th style="text-align: center;">{{ __('Total Kuota') }}</th>
                            <th style="text-align: center;">{{ __('Terpakai') }}</th>
                            <th style="text-align: center; width: 130px;">{{ __('Status') }}</th>
                            <th style="text-align: right; width: 180px;">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($slotList as $slot)
                            <tr class="sa-table-row" style="border-bottom: 1px solid var(--neutral-200);">
                                <td style="padding: 1rem 1.25rem;">
                                    <div style="font-weight: 700; color: var(--neutral-900); font-size: 0.9375rem;">
                                        {{ $slot->nama_instansi ?? __('Slot Khusus Internal') }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.25rem; line-height: 1.4;">
                                        {{ __('Dibuat oleh:') }} {{ $slot->creator?->nama ?? 'Administrator' }}
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
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: var(--green-600);"></span>{{ __('Aktif') }}</span>
                                    @else
                                        <span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.3rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: var(--red-100); color: var(--red-700); border: 1px solid rgba(220, 38, 38, 0.2);">
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: var(--red-600);"></span>{{ __('Nonaktif') }}</span>
                                    @endif
                                </td>
                                <td style="padding: 1rem 1.25rem; text-align: right; white-space: nowrap;">
                                    @if(! auth()->user()?->isSuperAdmin())
                                        <button type="button" 
                                                class="btn-trigger-toggle-slot {{ $slot->aktif ? 'sa-btn-action-danger' : 'sa-btn-action-outline' }}" 
                                                style="{{ $slot->aktif ? '' : 'color: var(--green-700); border-color: rgba(22, 163, 74, 0.35);' }}"
                                                data-id="{{ $slot->id }}"
                                                data-instansi="{{ $slot->nama_instansi }}"
                                                data-jumlah="{{ $slot->jumlah }}"
                                                data-status-aktif="{{ $slot->aktif ? '1' : '0' }}"
                                                data-action-url="{{ route('admin.slot-khusus.toggle', $slot) }}">
                                            {{ $slot->aktif ? __('Nonaktifkan') : __('Aktifkan') }}
                                        </button>
                                    @else
                                        <span style="font-size: 0.75rem; color: #94A3B8; font-weight: 500;">Read-Only</span>
                                    @endif
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
    <x-gentelella.x-panel class="has-table" title="{{ __('Daftar Peserta Magang Jalur Khusus') }}">
        <x-slot:toolbox>
            <span style="font-size: 0.8125rem; color: var(--neutral-600); font-weight: 600;">
                {{ __('Total:') }} <strong>{{ $pesertaKhususList->total() }} {{ __('Peserta Terdaftar') }}</strong>
            </span>
        </x-slot:toolbox>

        @if($pesertaKhususList->isEmpty())
            <div style="padding: 3.5rem 1.5rem; text-align: center; color: var(--neutral-600);">
                <p style="font-size: 0.9375rem; font-weight: 600; color: var(--neutral-800); margin: 0 0 0.25rem 0;">{{ __('Belum ada peserta yang terdaftar melalui jalur khusus.') }}</p>
                <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0;">{{ __('Pendaftaran peserta jalur khusus akan langsung memotong kuota slot khusus terkait.') }}</p>
            </div>
        @else
            <div class="table-responsive-gentelella" style="overflow-x: auto;">
                <table class="table jambo_table bulk_action" style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                    <thead>
                        <tr class="headings">
                            <th>{{ __('Peserta & Institusi') }}</th>
                            <th>{{ __('Jurusan / Jenjang') }}</th>
                            <th>{{ __('Unit Kerja (Bidang)') }}</th>
                            <th>{{ __('Pembimbing') }}</th>
                            <th>{{ __('Periode Magang') }}</th>
                            <th style="text-align: right; width: 140px;">{{ __('Kredensial') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pesertaKhususList as $p)
                            <tr class="sa-table-row" style="border-bottom: 1px solid var(--neutral-200);">
                                <td style="padding: 1rem 1.25rem;">
                                    <div style="font-weight: 700; color: var(--neutral-900); font-size: 0.9375rem;">
                                        {{ $p->peserta?->nama }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.2rem;">
                                        {{ $p->peserta?->email }} &bull; {{ $p->peserta?->profil?->institusi ?? '-' }}
                                    </div>
                                    @if($p->slotKhusus)
                                        <div style="margin-top: 0.35rem;">
                                            <span style="display: inline-flex; align-items: center; gap: 0.3rem; font-size: 0.7rem; font-weight: 700; color: #4338CA; background: #EEF2FF; border: 1px solid #C7D2FE; padding: 0.15rem 0.5rem; border-radius: 4px;">
                                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                                {{ $p->slotKhusus->nama_instansi }}
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td style="padding: 1rem 1rem;">
                                    <div style="font-weight: 600; color: var(--neutral-800);">
                                        {{ $p->peserta?->profil?->jurusan?->nama_jurusan ?? '-' }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--neutral-500); text-transform: uppercase;">
                                        {{ $p->peserta?->profil?->jenjang ?? '-' }}
                                    </div>
                                </td>
                                <td style="padding: 1rem 1rem;">
                                    <div style="font-weight: 600; color: var(--neutral-800);">
                                        {{ $p->bidang?->nama_bidang ?? '-' }}
                                    </div>
                                </td>
                                <td style="padding: 1rem 1rem;">
                                    <div style="font-weight: 600; color: var(--neutral-800);">
                                        {{ $p->pembimbing?->nama ?? '-' }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--neutral-500);">
                                        {{ $p->pembimbing?->email ?? '' }}
                                    </div>
                                </td>
                                <td style="padding: 1rem 1rem; white-space: nowrap;">
                                    <div style="font-weight: 600; color: var(--neutral-800);">
                                        {{ $p->tgl_mulai?->format('d/m/Y') }} s/d {{ $p->tgl_selesai?->format('d/m/Y') }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--neutral-500);">
                                        {{ $p->durasi_bulan }} Bulan Magang
                                    </div>
                                </td>
                                <td style="padding: 1rem 1.25rem; text-align: right; white-space: nowrap;">
                                    <button type="button" 
                                            class="sa-btn-action-outline sa-btn-copy-kredensial" 
                                            style="padding: 0.4rem 0.75rem; font-size: 0.75rem;"
                                            data-nama="{{ $p->peserta?->nama }}"
                                            data-email="{{ $p->peserta?->email }}"
                                            data-sandi="{{ $p->peserta?->password_awal ?? '' }}"
                                            data-status="{{ $p->peserta?->must_change_password ? 'aktif' : 'changed' }}"
                                            data-login-url="{{ route('login') }}"
                                            title="{{ __('Salin Akun & Kata Sandi Awal untuk WhatsApp') }}">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                        <span>{{ __('Salin Akun') }}</span>
                                    </button>
                                    <div style="margin-top: 0.35rem;">
                                        @if($p->peserta?->must_change_password)
                                            <span class="sa-badge-cred-active" title="{{ __('Peserta belum aktivasi / belum mengganti kata sandi awal') }}">
                                                <span style="width: 5px; height: 5px; border-radius: 50%; background: #D97706;"></span>
                                                {{ __('Sandi Awal Aktif') }}
                                            </span>
                                        @else
                                            <span class="sa-badge-cred-changed" title="{{ __('Peserta sudah memperbarui kata sandi pribadi; sandi awal tidak berlaku lagi untuk login') }}">
                                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                {{ __('Sandi Diubah Peserta') }}
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

@if(! auth()->user()?->isSuperAdmin())
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
                <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0;">{{ __('Tambah Slot Khusus Internal') }}</h3>
            </div>
            <button type="button" data-modal-dismiss="modalTambahSlot" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--neutral-500); line-height: 1; padding: 0.25rem;">&times;</button>
        </div>

        <form action="{{ route('admin.slot-khusus.store') }}" method="POST" style="display: flex; flex-direction: column; gap: 1.25rem;">
            @csrf
            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    {{ __('Nama Instansi Mitra') }} <span style="color: var(--red-600);">*</span>
                </label>
                <input type="text" name="nama_instansi" required placeholder="Contoh: BSSN, Diskominfo DIY, UGM Lab" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        {{ __('Bulan Mulai') }} <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="date" name="bulan_mulai" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        {{ __('Jumlah Kuota Slot') }} <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="number" name="jumlah" value="{{ $defaultSlot ?? 3 }}" min="0" max="100" required placeholder="Contoh: 3" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                </div>
            </div>

            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    {!! __('Keterangan & Catatan MoU') !!}
                </label>
                <textarea name="keterangan" rows="3" placeholder="{{ __('Nomor surat / dasar penugasan kerjasama...') }}" style="width: 100%; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0.75rem; font-size: 0.875rem; font-family: inherit; line-height: 1.4; resize: vertical;"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem; border-top: 1px solid var(--neutral-200); padding-top: 1rem;">
                <button type="button" data-modal-dismiss="modalTambahSlot" style="height: 40px; padding: 0 1.25rem; background: var(--neutral-100); border: 1px solid var(--neutral-400); border-radius: 8px; font-weight: 700; font-size: 0.875rem; color: var(--neutral-700); cursor: pointer;">{{ __('Batal') }}</button>
                <button type="submit" style="height: 40px; padding: 0 1.5rem; background: var(--blue-600); color: #FFFFFF; border: none; border-radius: 8px; font-weight: 700; font-size: 0.875rem; cursor: pointer; box-shadow: var(--shadow-subtle);">{{ __('Simpan Slot Khusus') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- =====================================================================
     MODAL KONFIRMASI STATUS SLOT KHUSUS INTERNAL (NONAKTIFKAN / AKTIFKAN)
     ===================================================================== -->
<div id="modalKonfirmasiToggleSlot" 
     style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;"
     role="dialog" 
     aria-modal="true" 
     aria-labelledby="modalToggleSlotTitle">
    <div style="background: var(--neutral-0, #FFFFFF); border-radius: 16px; width: 100%; max-width: 480px; padding: 2rem; box-shadow: var(--shadow-modal, 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)); position: relative; border: 1px solid var(--neutral-300, #CBD5E1);">
        
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.25rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div id="toggleSlotIconWrap" style="width: 42px; height: 42px; border-radius: 10px; background: #FEE2E2; color: #DC2626; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                    </svg>
                </div>
                <div>
                    <h3 id="modalToggleSlotTitle" style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0; line-height: 1.3;">
                        {{ __('Konfirmasi Status Slot Khusus') }}
                    </h3>
                    <span style="font-size: 0.8125rem; color: var(--neutral-500, #64748B);">
                        {!! __('Slot Magang Kemitraan & Penugasan Khusus') !!}
                    </span>
                </div>
            </div>
            <button type="button" 
                    data-modal-dismiss="modalKonfirmasiToggleSlot" 
                    style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--neutral-400, #94A3B8); line-height: 1; padding: 0.25rem; border-radius: 6px;"
                    title="{{ __('Tutup dialog') }}">
                &times;
            </button>
        </div>

        <form id="formToggleSlot" method="POST" style="display: flex; flex-direction: column; gap: 1.25rem; margin: 0;">
            @csrf
            @method('PATCH')
            <div id="toggleSlotBoxAlert" style="background: #FFF1F2; border: 1px solid #FECDD3; border-radius: 10px; padding: 1rem 1.25rem;">
                <p id="toggleSlotMessage" style="font-size: 0.875rem; color: #9F1239; margin: 0 0 0.5rem 0; line-height: 1.5;">
                    {{ __('Apakah Anda yakin ingin menonaktifkan alokasi slot khusus ini?') }}
                </p>
                <div style="background: rgba(255, 255, 255, 0.7); border-radius: 6px; padding: 0.5rem 0.75rem; border: 1px solid rgba(220, 38, 38, 0.15);">
                    <div style="font-weight: 700; color: var(--neutral-900, #0F172A); font-size: 0.875rem;" id="toggleSlotInstansi">{{ __('Instansi Mitra') }}</div>
                    <div style="font-size: 0.75rem; color: var(--neutral-600, #475569);" id="toggleSlotDetail">{{ __('Alokasi: 0 Kuota') }}</div>
                </div>
                <p id="toggleSlotConsequence" style="font-size: 0.75rem; color: #BE123C; margin: 0.65rem 0 0 0; line-height: 1.4;">
                    {{ __('• Slot yang dinonaktifkan tidak dapat digunakan untuk input penempatan peserta jalur kemitraan baru.') }}
                </p>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--neutral-200, #E2E8F0); padding-top: 1rem;">
                <button type="button" 
                        data-modal-dismiss="modalKonfirmasiToggleSlot" 
                        style="height: 40px; padding: 0 1.25rem; background: var(--neutral-100, #F1F5F9); border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; font-weight: 700; font-size: 0.875rem; color: var(--neutral-700, #334155); cursor: pointer;">{{ __('Batal') }}</button>
                <button type="submit" 
                        id="btnSubmitToggleSlot" 
                        style="height: 40px; padding: 0 1.5rem; background: #DC2626; border: none; border-radius: 8px; font-weight: 700; font-size: 0.875rem; color: #FFFFFF; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem; transition: background 0.15s ease;">
                    <span id="btnSubmitToggleSlotText">{{ __('Ya, Nonaktifkan Slot') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection
