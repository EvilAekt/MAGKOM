@extends('layouts.dashboard')

@section('title', 'Logbook - Portal MagKom')
@section('topbar_title', 'Logbook Peserta')
@section('content_area_class', 'gentelella-fluid-canvas')

@push('styles')
    @vite(['resources/css/pages/pembimbing/logbook-index.css'])
@endpush

@section('content')
<div class="classroom-container">

    {{-- 1. Hero Banner Khas Sleman MagKom --}}
    @php
        $authUser = auth()->user();
        $bidangNama = $authUser->bidang?->nama_bidang ?? 'Dinas Komunikasi dan Informatika Sleman';
    @endphp

    <div class="classroom-banner">
        <div class="banner-top-row">
            <div>
                <h1 class="banner-title">Logbook Peserta</h1>
                <div class="banner-subtitle">
                    <span>Bidang: <strong>{{ $bidangNama }}</strong></span>
                    <span>&bull;</span>
                    <span>Pembimbing: <strong>{{ $authUser->nama }}</strong></span>
                    <span>&bull;</span>
                    <span><strong>{{ $penempatanList->count() }} Mentee Aktif Bimbingan</strong></span>
                </div>
                <div class="sr-only">
                    Review Logbook Harian Peserta - Total Logbook: {{ $totalLogbook }}, Perlu Review: {{ $countMenunggu }}, Disetujui: {{ $countDisetujui }}, Perlu Revisi: {{ $countRevisi }}
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Tile Stats MagKom Gentelella (Fokus Logbook & Catatan Pembimbing) --}}
    <div class="tiles-grid">
        <x-gentelella.tile-stat
            title="Total Logbook"
            value="{{ $totalLogbook }}"
            color="teal"
            trendDirection="up"
            trendText="{{ $totalLogbook }} Entri"
            subText="Akumulasi aktivitas peserta"
            :sparkBars="[30, 45, 55, 60, 70, 78, 85, 92, 100]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <x-gentelella.tile-stat
            title="Mentee Melapor"
            value="{{ $totalMenteeMelapor }}"
            color="blue"
            trendType="info"
            trendDirection="up"
            trendText="{{ $totalMenteeMelapor }} Peserta"
            subText="Peserta aktif mengisi logbook"
            :sparkBars="[40, 50, 60, 65, 75, 80, 85, 90, 95]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <x-gentelella.tile-stat
            title="Sudah Diberi Catatan"
            value="{{ $countDenganCatatan }}"
            color="teal"
            trendType="success"
            trendDirection="up"
            trendText="{{ $totalLogbook > 0 ? round(($countDenganCatatan / $totalLogbook) * 100) : 0 }}%"
            subText="Logbook dengan catatan arahan"
            :sparkBars="[25, 40, 50, 60, 70, 80, 85, 92, 98]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <x-gentelella.tile-stat
            title="Belum Diberi Catatan"
            value="{{ $countTanpaCatatan }}"
            color="yellow"
            trendType="{{ $countTanpaCatatan > 0 ? 'warning' : 'success' }}"
            trendDirection="{{ $countTanpaCatatan > 0 ? 'down' : 'up' }}"
            trendText="{{ $countTanpaCatatan }} Menunggu"
            subText="Menunggu catatan pembimbing"
            :sparkBars="[60, 75, 85, 70, 90, 80, 65, 50, 40]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>
    </div>

    {{-- 3. Filter Bar: Sederhana (HANYA Filter Nama Peserta) --}}
    @php
        $isFocusActive = $selectedPenempatan !== null;
        $focusedNama = $selectedPenempatan?->peserta?->nama ?? '';
        $focusedInst = $selectedPenempatan?->peserta?->profil?->institusi ?? '';
        $focusedInitials = collect(explode(' ', $focusedNama))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
    @endphp
    <div id="logbookFilterCard" class="logbook-filter-card {{ $isFocusActive ? 'd-none' : '' }}">
        <form method="GET" action="{{ route('pembimbing.logbook.index') }}" class="logbook-filter-form">
            <div class="filter-group" style="flex: 1;">
                <label for="selectPenempatan">Filter Nama Peserta</label>
                <select name="penempatan_id" id="selectPenempatan" class="filter-control" onchange="this.form.submit()">
                    <option value="">Semua Peserta Bimbingan</option>
                    @foreach($penempatanList as $pen)
                        <option value="{{ $pen->id }}" {{ request('penempatan_id') == $pen->id ? 'selected' : '' }}>
                            {{ $pen->peserta?->nama }} ({{ $pen->peserta?->profil?->institusi ?? 'Institusi' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-filter-apply">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                    <span>Filter</span>
                </button>
                @if(request()->filled('penempatan_id'))
                    <a href="{{ route('pembimbing.logbook.index') }}" class="btn-filter-reset">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- 4. Panel Utama Logbook: Pola Kartu Selaras dengan Halaman Tugas --}}
    <x-gentelella.x-panel id="logbookMainPanel">
        <x-slot:title>
            {{-- Mode 1: Default Title saat Semua Mentee Tampil --}}
            <span id="defaultTitleText" class="default-panel-title {{ $isFocusActive ? 'd-none' : '' }}">
                Daftar Logbook &amp; Catatan Pembimbing
            </span>

            {{-- Mode 2: Header Terpadu saat Mentee Difokuskan (Avatar, Nama, Inst di Kiri, Tombol Kembali di Kanan Sesuai Tugas) --}}
            <div id="menteeUnifiedHeader" class="mentee-unified-header {{ $isFocusActive ? '' : 'd-none' }}">
                <div class="header-mentee-col">
                    <div id="focusedMenteeAvatar" class="header-mentee-avatar" aria-hidden="true">
                        {{ strtoupper($focusedInitials) }}
                    </div>
                    <div class="header-mentee-text">
                        <span id="focusedMenteeName" class="header-mentee-name">{{ $focusedNama }}</span>
                        <span id="focusedMenteeInst" class="header-mentee-inst">{{ $focusedInst }}</span>
                    </div>
                </div>

                <a href="{{ route('pembimbing.logbook.index', request()->except('penempatan_id')) }}" 
                   id="btnBackToMentees" 
                   class="btn-back-mentee-modern" 
                   title="Kembali ke Daftar Semua Mentee">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    <span>Semua Mentee</span>
                </a>
            </div>
        </x-slot:title>

        {{-- Tampilan Kartu Mentee & Stream Logbook (Pure Card View 100% Tanpa Switcher Tabel) --}}
        <div id="menteeStreamContainer" class="mentee-stream-container {{ $isFocusActive ? 'is-focused' : '' }}">
            @forelse($penempatanList as $penempatan)
                @php
                    $mentee = $penempatan->peserta;
                    $menteeNama = $mentee?->nama ?? 'Peserta Magang';
                    $institusi = $mentee?->profil?->institusi ?? 'Institusi Belum Dilengkapi';
                    $initials = collect(explode(' ', $menteeNama))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
                    $menteeLogbooks = $penempatan->logbooks ?? collect();
                    $isThisMenteeSelected = $selectedPenempatanId == $penempatan->id;
                @endphp

                <div class="mentee-classroom-card {{ $isThisMenteeSelected ? 'is-active-focus is-expanded' : '' }}" data-mentee-id="{{ $penempatan->id }}">
                    <!-- Header Kartu Mentee: Klik untuk Masuk ke Halaman Logbook Mentee -->
                    <button type="button" 
                            class="mentee-card-header" 
                            data-action="focus-mentee"
                            data-mentee-id="{{ $penempatan->id }}"
                            data-mentee-name="{{ $menteeNama }}"
                            data-mentee-inst="{{ $institusi }}"
                            aria-expanded="{{ $isThisMenteeSelected ? 'true' : 'false' }}"
                            aria-controls="logs-mentee-{{ $penempatan->id }}"
                            title="Klik untuk melihat logbook {{ $menteeNama }}">
                        <div class="mentee-info-left">
                            <div class="mentee-avatar-circle" aria-hidden="true">
                                {{ strtoupper($initials) }}
                            </div>
                            <div class="mentee-text-meta">
                                <h3 class="mentee-primary-name">{{ $menteeNama }}</h3>
                                <p class="mentee-sub-inst">{{ $institusi }} &bull; {{ $menteeLogbooks->count() }} Entri Logbook</p>
                            </div>
                        </div>

                        <div class="mentee-meta-right">
                            <div class="mentee-chevron-icon" aria-hidden="true">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </div>
                        </div>
                    </button>

                    <!-- Drawer/Daftar Entri Logbook Mentee (Hanya Tampil saat Mentee Difokuskan) -->
                    <div id="logs-mentee-{{ $penempatan->id }}" class="mentee-tasks-drawer" {{ $isThisMenteeSelected ? '' : 'hidden' }}>
                        @if($menteeLogbooks->count() > 0)
                            <div class="classroom-tasks-list">
                                @foreach($menteeLogbooks as $log)
                                    <div class="classroom-task-row logbook-item-row" style="align-items: flex-start; padding: 14px 16px;">
                                        <div class="task-row-left" style="flex: 1; align-items: flex-start; gap: 14px;">
                                            <div class="task-row-icon icon-default" style="margin-top: 2px;" aria-hidden="true">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                                </svg>
                                            </div>
                                            <div class="task-row-title-wrap" style="flex: 1;">
                                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                                    <span class="date-badge" style="background: #F1F5F9; color: #334155; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 700;">
                                                        {{ \Carbon\Carbon::parse($log->tanggal)->format('d M Y') }}
                                                    </span>
                                                    @if($log->file_laporan)
                                                        <a href="{{ route('peserta.logbook.lampiran', $log) }}" target="_blank" rel="noopener noreferrer" style="color: #059669; font-size: 0.75rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 3px;">
                                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                                            <span>Berkas PDF</span>
                                                        </a>
                                                    @endif
                                                    @if($log->link_laporan)
                                                        <a href="{{ $log->link_laporan }}" target="_blank" rel="noopener noreferrer" style="color: #01347C; font-size: 0.75rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 3px;">
                                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                                            <span>Tautan ↗</span>
                                                        </a>
                                                    @endif
                                                </div>
                                                <div style="font-size: 0.875rem; color: #1E293B; line-height: 1.5; margin-bottom: 6px;">
                                                    {{ $log->kegiatan }}
                                                </div>
                                                @if($log->catatan_pembimbing)
                                                    <div class="supervisor-notes-box" style="margin-top: 6px; padding: 8px 12px; background: #F8FAFC; border-left: 3px solid #01347C; border-radius: 4px; font-size: 0.8125rem; color: #334155;">
                                                        <strong style="color: #01347C;">Catatan Pembimbing:</strong> {{ $log->catatan_pembimbing }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Aksi Tunggal Pembimbing: Memberikan Catatan --}}
                                        <div class="task-row-right" style="margin-top: 2px;">
                                            <button type="button" 
                                                class="btn-logbook-action btn-open-catatan-modal"
                                                data-log-id="{{ $log->id }}"
                                                data-peserta-nama="{{ $menteeNama }}"
                                                data-tanggal="{{ \Carbon\Carbon::parse($log->tanggal)->format('d M Y') }}"
                                                data-kegiatan="{{ e($log->kegiatan) }}"
                                                data-catatan="{{ e($log->catatan_pembimbing ?? '') }}"
                                                title="Beri atau ubah catatan pembimbing">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                <span>{{ $log->catatan_pembimbing ? 'Ubah Catatan' : 'Beri Catatan' }}</span>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div style="padding: 2.5rem 1.5rem; text-align: center; color: #64748B; font-size: 0.875rem;">
                                Belum ada entri logbook yang diisi oleh mentee ini.
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="table-empty-td" style="padding: 2rem; text-align: center; background: #FFFFFF; border-radius: 8px;">
                    Tidak ada peserta bimbingan yang sesuai dengan filter.
                </div>
            @endforelse
        </div>
    </x-gentelella.x-panel>

</div>

{{-- 5. MODAL MEMBERIKAN CATATAN LOGBOOK (Aksi Tunggal Pembimbing) --}}
<div id="modal-catatan-logbook" class="modal-overlay">
    <div class="modal-dialog-box">
        <div class="modal-header-bar">
            <div>
                <h3 class="modal-title">Berikan Catatan Pembimbing</h3>
                <p class="modal-subtitle" id="catatan-modal-subtitle"></p>
            </div>
            <button type="button" class="modal-close-btn" data-modal-close="#modal-catatan-logbook">&times;</button>
        </div>

        <form method="POST" id="form-catatan-logbook" action="" class="modal-form">
            @csrf

            <div>
                <label style="font-size: 0.8125rem; font-weight: 700; color: #475569; margin-bottom: 0.35rem; display: block; text-transform: uppercase; letter-spacing: 0.04em;">
                    Uraian Aktivitas Peserta:
                </label>
                <div id="catatan-modal-kegiatan" style="padding: 10px 14px; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; font-size: 0.875rem; color: #334155; line-height: 1.5; max-height: 120px; overflow-y: auto;">
                </div>
            </div>

            <div style="margin-top: 1rem;">
                <label for="modal-catatan-textarea" style="font-size: 0.8125rem; font-weight: 700; color: #0F172A; margin-bottom: 0.35rem; display: block;">
                    Catatan &amp; Arahan Pembimbing <span style="color: #ef4444;">*</span>
                </label>
                <textarea name="catatan_pembimbing" 
                          id="modal-catatan-textarea" 
                          rows="4" 
                          required 
                          placeholder="Tuliskan catatan apresiasi, evaluasi aktivitas, atau arahan kerja untuk peserta..." 
                          class="filter-control" 
                          style="height: auto; padding: 10px;"></textarea>
            </div>

            <div class="modal-footer-actions" style="margin-top: 1.25rem;">
                <button type="button" class="btn-modal-cancel" data-modal-close="#modal-catatan-logbook">Batal</button>
                <button type="submit" class="btn-modal-submit" style="background: #01347C; color: #ffffff;">Simpan Catatan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    @vite(['resources/js/pages/pembimbing/logbook-index.js'])
@endpush
@endsection
