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

    <div class="classroom-banner sc-reveal-hero" data-scroll-reveal>
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
    <div class="tiles-grid sc-reveal-up" data-scroll-reveal>
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

    {{-- 3. Filter Baris Jenis: Semua, Individu, Tim (Selaras dengan Halaman Tugas) --}}
    @php
        $isMasukMode = !empty($masukPenempatan);
        $isMasukKelompokMode = !empty($masukKelompok);
        $effectiveJenisLogbook = $jenisLogbook ?? 'semua';
        if ($isMasukKelompokMode || request()->filled('dari_kelompok')) {
            $effectiveJenisLogbook = 'kelompok';
        } elseif ($isMasukMode && $masukPenempatan) {
            $effectiveJenisLogbook = $masukPenempatan->isKelompok() ? 'kelompok' : 'individu';
        }
        $displayPenempatanList = $isMasukMode ? collect([$masukPenempatan]) : $penempatanList;
    @endphp
    @if(!$isMasukMode && !$isMasukKelompokMode)
        <div class="logbook-jenis-filter-nav">
            <a href="{{ route('pembimbing.logbook.index', array_merge(request()->except(['jenis', 'page', 'masuk', 'masuk_kelompok', 'dari_kelompok']), ['jenis' => 'semua'])) }}"
               class="filter-pill {{ $effectiveJenisLogbook === 'semua' ? 'active' : '' }}">
                <span>Semua ({{ $filterCounts['semua'] ?? 0 }})</span>
            </a>
            <a href="{{ route('pembimbing.logbook.index', array_merge(request()->except(['jenis', 'page', 'masuk', 'masuk_kelompok', 'dari_kelompok']), ['jenis' => 'individu'])) }}"
               class="filter-pill {{ $effectiveJenisLogbook === 'individu' ? 'active' : '' }}">
                <span>Individu ({{ $filterCounts['individu'] ?? 0 }})</span>
            </a>
            <a href="{{ route('pembimbing.logbook.index', array_merge(request()->except(['jenis', 'page', 'masuk', 'masuk_kelompok', 'dari_kelompok']), ['jenis' => 'kelompok'])) }}"
               class="filter-pill {{ $effectiveJenisLogbook === 'kelompok' ? 'active' : '' }}">
                <span>Tim ({{ $filterCounts['kelompok'] ?? ($kelompokList ?? collect())->count() }} Tim)</span>
            </a>
        </div>
    @endif

    <div id="logbookFilterCard" class="logbook-filter-card{{ $isMasukMode || $isMasukKelompokMode ? ' d-none' : '' }}">
        <form method="GET" action="{{ route('pembimbing.logbook.index') }}" class="logbook-filter-form">
            @if(request()->filled('jenis'))
                <input type="hidden" name="jenis" value="{{ request('jenis') }}">
            @endif
            <div class="filter-group" style="flex: 1.3;">
                <label for="inputSearchLogbook">{{ $effectiveJenisLogbook === 'kelompok' ? 'Pencarian Tim' : 'Pencarian Peserta' }}</label>
                <div class="peserta-search-input-wrap">
                    <input type="text" name="q" id="inputSearchLogbook" value="{{ request('q') }}" placeholder="{{ $effectiveJenisLogbook === 'kelompok' ? 'Cari nama tim, nama ketua...' : 'Cari nama, kampus, NIM...' }}" class="peserta-search-input">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="peserta-search-icon">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </div>
            </div>

            @if($effectiveJenisLogbook !== 'kelompok' && !$isMasukKelompokMode)
                <div class="filter-group" style="flex: 1;">
                    <label for="selectPenempatan">Filter Nama Peserta</label>
                    <select name="penempatan_id" id="selectPenempatan" class="filter-control" data-action="auto-submit-form">
                        <option value="">Semua Peserta Bimbingan</option>
                        @foreach($allPenempatan ?? $penempatanList as $pen)
                            <option value="{{ $pen->id }}" {{ request('penempatan_id') == $pen->id ? 'selected' : '' }}>
                                {{ $pen->peserta?->nama }} ({{ $pen->peserta?->profil?->institusi ?? 'Institusi' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="filter-group" style="flex: 1;">
                <label for="selectBulan">Filter Bulan</label>
                <select name="bulan" id="selectBulan" class="filter-control" data-action="auto-submit-form">
                    <option value="">Semua Bulan</option>
                    @foreach($daftarBulan ?? [] as $val => $label)
                        <option value="{{ $val }}" {{ request('bulan') == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-filter-apply">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                    <span>Filter</span>
                </button>
                @if(request()->anyFilled(['penempatan_id', 'bulan', 'q']))
                    <a href="{{ route('pembimbing.logbook.index') }}" class="btn-filter-reset">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- 4. Panel Utama Logbook: Pola Kartu Selaras dengan Halaman Tugas --}}
    <x-gentelella.x-panel id="logbookMainPanel">
        <x-slot:title>
            @if($isMasukMode)
                @php
                    $masukMentee = $masukPenempatan->peserta;
                    $masukNama = $masukMentee?->nama ?? 'Peserta Magang';
                    $masukInst = $masukMentee?->profil?->institusi ?? 'Institusi Belum Dilengkapi';
                    $masukInitials = collect(explode(' ', $masukNama))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
                    $masukLogbooks = $masukPenempatan->logbooks ?? collect();
                @endphp
                <div class="mentee-panel-title-wrap">
                    <div class="mentee-avatar-circle" aria-hidden="true">{{ strtoupper($masukInitials) }}</div>
                    <div class="mentee-text-meta">
                        <h3 class="mentee-primary-name">{{ $masukNama }}</h3>
                        <p class="mentee-sub-inst">{{ $masukInst }} &bull; {{ $masukLogbooks->count() }} Entri Logbook</p>
                    </div>
                </div>
            @elseif($isMasukKelompokMode)
                @php
                    $kNama = $masukKelompok->nama_kelompok ?? 'Tim Magang';
                    $kKetuaNama = $masukKelompok->ketua?->nama ?? 'Ketua Tim';
                    $kInstitusi = $masukKelompok->institusi ?? ($masukKelompok->ketua?->profil?->institusi ?? 'Institusi Belum Dilengkapi');
                    $kMemberTotal = ($masukKelompokMembers ?? collect())->count();
                @endphp
                <div class="mentee-panel-title-wrap">
                    <div class="mentee-avatar-circle" aria-hidden="true" style="background: #EFF6FF; color: #003566; border: 1.5px solid #BFDBFE;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <div class="mentee-text-meta">
                        <h3 class="mentee-primary-name">{{ $kNama }}</h3>
                        <p class="mentee-sub-inst">Ketua: {{ $kKetuaNama }} &bull; {{ $kInstitusi }} &bull; {{ $kMemberTotal }} Anggota</p>
                    </div>
                </div>
            @elseif($effectiveJenisLogbook === 'kelompok')
                <span id="defaultTitleText" class="default-panel-title">
                    Daftar Tim Bimbingan Magang
                </span>
            @else
                <span id="defaultTitleText" class="default-panel-title">
                    Daftar Logbook &amp; Catatan Pembimbing
                </span>
            @endif
        </x-slot:title>

        @if($isMasukMode)
            <x-slot:actions>
                @if(request()->filled('dari_kelompok'))
                    <a href="{{ route('pembimbing.logbook.index', ['masuk_kelompok' => request('dari_kelompok'), 'jenis' => 'kelompok']) }}" 
                       class="mentee-chevron-back-btn" 
                       title="Kembali ke Detail Tim"
                       aria-label="Kembali ke Detail Tim">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                    </a>
                @else
                    <a href="{{ route('pembimbing.logbook.index', request()->except('masuk')) }}" 
                       class="mentee-chevron-back-btn" 
                       title="Kembali ke Daftar Logbook"
                       aria-label="Kembali ke Daftar Logbook">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                    </a>
                @endif
            </x-slot:actions>
        @elseif($isMasukKelompokMode)
            <x-slot:actions>
                <a href="{{ route('pembimbing.logbook.index', array_merge(request()->except(['masuk_kelompok', 'page', 'dari_kelompok']), ['jenis' => 'kelompok'])) }}" 
                   class="mentee-chevron-back-btn" 
                   title="Kembali ke Daftar Tim"
                   aria-label="Kembali ke Daftar Tim">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </a>
            </x-slot:actions>
        @endif

        @if($isMasukMode)
            @php
                $masukMentee = $masukPenempatan->peserta;
                $masukNama = $masukMentee?->nama ?? 'Peserta Magang';
                $masukLogbooks = $logbooks;
            @endphp
            <div id="menteeStreamContainer" class="mentee-stream-container mentee-stream-focused" data-mentee-id="{{ $masukPenempatan->id }}">
                <div id="logs-mentee-{{ $masukPenempatan->id }}" class="mentee-tasks-drawer">
                    @if($masukLogbooks->count() > 0)
                        <div class="classroom-tasks-list">
                            @foreach($masukLogbooks as $log)
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
                                                <span class="date-badge logbook-date-badge">
                                                    {{ \Carbon\Carbon::parse($log->tanggal)->format('d M Y') }}
                                                </span>
                                                @if($log->file_laporan)
                                                    <a href="{{ route('peserta.logbook.lampiran', $log) }}" target="_blank" rel="noopener noreferrer" class="logbook-attachment-link pdf">
                                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                                        <span>Berkas PDF</span>
                                                    </a>
                                                @endif
                                                @if($log->link_laporan)
                                                    <a href="{{ $log->link_laporan }}" target="_blank" rel="noopener noreferrer" class="logbook-attachment-link web">
                                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                                        <span>Tautan ↗</span>
                                                    </a>
                                                @endif
                                            </div>
                                            <div class="logbook-kegiatan-text">
                                                {{ $log->kegiatan }}
                                            </div>
                                            @if($log->catatan_pembimbing)
                                                <div id="notes-view-{{ $log->id }}" class="supervisor-notes-box">
                                                    <strong class="notes-label">Catatan Pembimbing:</strong> <span class="notes-text">{{ $log->catatan_pembimbing }}</span>
                                                </div>
                                            @endif

                                            {{-- Form Inline Pemberian / Pengubahan Catatan Pembimbing (Muncul saat tombol diklik) --}}
                                            <div id="inline-note-form-{{ $log->id }}" class="inline-note-editor" hidden>
                                                <form method="POST" action="{{ route('pembimbing.logbook.review', $log->id) }}" class="note-edit-form">
                                                    @csrf

                                                    @php
                                                        $hasInitialNote = !empty($log->catatan_pembimbing);
                                                    @endphp

                                                    {{-- Material Floating Card Field ala Google Classroom --}}
                                                    <div class="logbook-note-field-filled {{ $hasInitialNote ? 'has-value' : '' }}">
                                                        <label for="textarea-note-{{ $log->id }}" class="logbook-note-floating-label">
                                                            <span>Catatan &amp; Arahan Pembimbing</span>
                                                        </label>
                                                        <textarea name="catatan_pembimbing" 
                                                                  id="textarea-note-{{ $log->id }}" 
                                                                  rows="3" 
                                                                  class="logbook-note-clean-textarea">{{ $log->catatan_pembimbing ?? '' }}</textarea>
                                                    </div>

                                                    <div class="inline-note-actions">
                                                        <button type="button" 
                                                                class="btn-note-cancel" 
                                                                data-action="cancel-inline-note" 
                                                                data-log-id="{{ $log->id }}">
                                                            Batal
                                                        </button>
                                                        <button type="submit" class="btn-note-save">
                                                            Simpan Catatan
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Aksi Tunggal Pembimbing: Memberikan Catatan Secara Inline (Read-only untuk SuperAdmin) --}}
                                    @if(!auth()->user()?->isSuperAdmin())
                                        <div class="task-row-right" style="margin-top: 2px;">
                                            <button type="button" 
                                                class="btn-logbook-action btn-toggle-inline-note"
                                                data-action="toggle-inline-note"
                                                data-log-id="{{ $log->id }}"
                                                title="{{ $log->catatan_pembimbing ? 'Ubah catatan pembimbing' : 'Beri catatan pembimbing' }}">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                <span>{{ $log->catatan_pembimbing ? 'Ubah Catatan' : 'Beri Catatan' }}</span>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-mentee-tasks logbook-empty-mentee">
                            Belum ada entri logbook yang diisi oleh mentee ini.
                        </div>
                    @endif
                </div>
            </div>
        @elseif($isMasukKelompokMode)
            {{-- Mode Detail Tim: Daftar Ketua Tim & Seluruh Anggota beserta Ringkasan Logbook --}}
            <div id="menteeStreamContainer" class="mentee-stream-container">
                @forelse($masukKelompokMembers ?? [] as $mUser)
                    @php
                        $isKetua = (int) $mUser->id === (int) $masukKelompok->ketua_user_id;
                        $mPenempatan = ($masukKelompokPenempatan ?? collect())->firstWhere('peserta_id', $mUser->id);
                        $mNama = $mUser->nama ?? 'Peserta Magang';
                        $mInst = $mUser->profil?->institusi ?? 'Institusi Belum Dilengkapi';
                        $mJurusan = $mUser->profil?->jurusan?->nama_jurusan;
                        $mSub = $mJurusan ? "{$mInst} • {$mJurusan}" : $mInst;
                        $mInitials = collect(explode(' ', $mNama))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
                        $mLogbooks = $mPenempatan?->logbooks ?? collect();
                        $totalLogsCount = $mLogbooks->count();
                        $catatanCount = $mLogbooks->filter(fn($l) => !empty($l->catatan_pembimbing))->count();
                        $menungguCount = $mLogbooks->filter(fn($l) => in_array($l->status->value ?? (string) $l->status, ['terkirim', 'menunggu']))->count();
                    @endphp

                    <div class="mentee-classroom-card" data-mentee-id="{{ $mPenempatan?->id }}">
                        @if($mPenempatan)
                            <a href="{{ route('pembimbing.logbook.index', ['masuk' => $mPenempatan->id, 'dari_kelompok' => $masukKelompok->id, 'jenis' => 'kelompok']) }}"
                               class="mentee-card-header mentee-card-link"
                               title="Masuk ke rincian logbook {{ $mNama }}">
                        @else
                            <div class="mentee-card-header" style="cursor: default;">
                        @endif
                            <div class="mentee-info-left">
                                <div class="mentee-avatar-circle" aria-hidden="true">{{ strtoupper($mInitials) }}</div>
                                <div class="mentee-text-meta">
                                    <div class="logbook-member-name-row">
                                        <h3 class="mentee-primary-name" style="margin: 0;">{{ $mNama }}</h3>
                                        @if($isKetua)
                                            <span class="logbook-role-badge is-ketua">Ketua Tim</span>
                                        @else
                                            <span class="logbook-role-badge is-anggota">Anggota</span>
                                        @endif
                                    </div>
                                    <p class="mentee-sub-inst">{{ $mSub }}</p>
                                </div>
                            </div>

                            <div class="mentee-meta-right">
                                @if($totalLogsCount > 0)
                                    <div class="mentee-stats-badges">
                                        <span class="mentee-count-chip">{{ $totalLogsCount }} Logbook</span>
                                        @if($catatanCount > 0)
                                            <span class="mentee-count-chip selesai">{{ $catatanCount }} Diberi Catatan</span>
                                        @endif
                                        @if($menungguCount > 0)
                                            <span class="mentee-count-chip review">{{ $menungguCount }} Menunggu</span>
                                        @endif
                                    </div>
                                @endif
                                @if($mPenempatan)
                                    <div class="mentee-chevron-icon" aria-hidden="true">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="9 18 15 12 9 6"></polyline>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        @if($mPenempatan)
                            </a>
                        @else
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="table-empty-td logbook-empty-peserta">
                        Belum ada anggota yang terdaftar pada tim ini.
                    </div>
                @endforelse
            </div>
        @else
            @php
                $activeJenis = $effectiveJenisLogbook ?? 'semua';
                $kelItems = $kelompokList ?? collect();
                $showKelompok = $activeJenis === 'kelompok' || ($activeJenis === 'semua' && $kelItems->isNotEmpty());
                $showIndividu = $activeJenis === 'individu' || ($activeJenis === 'semua' && $penempatanList->isNotEmpty());
                $hasItems = ($showKelompok && $kelItems->isNotEmpty()) || ($showIndividu && $penempatanList->isNotEmpty());
            @endphp

            {{-- Mode Daftar Tim & Peserta Individu --}}
            <div id="menteeStreamContainer" class="mentee-stream-container">
                @if($showKelompok)
                    @foreach($kelItems as $kel)
                        @php
                            $kNamaKetua = $kel->ketua?->nama ?? 'Ketua Tim';
                            $kNamaKelompok = $kel->nama_kelompok ?? ('Tim #' . $kel->id);
                            $totalAnggotaTim = ($kel->anggota_count ?? $kel->anggota->count()) + 1;
                            $kInstitusi = $kel->institusi ?? ($kel->ketua?->profil?->institusi ?? 'Institusi Belum Dilengkapi');
                        @endphp
                        <div class="mentee-classroom-card" data-kelompok-id="{{ $kel->id }}">
                            <a href="{{ route('pembimbing.logbook.index', array_merge(request()->except(['page', 'masuk']), ['masuk_kelompok' => $kel->id, 'jenis' => 'kelompok'])) }}"
                               class="mentee-card-header mentee-card-link"
                               title="Masuk ke daftar anggota tim {{ $kNamaKelompok }}">
                                <div class="mentee-info-left">
                                    <div class="mentee-avatar-circle" aria-hidden="true" style="background: #EFF6FF; color: #003566; border: 1.5px solid #BFDBFE;">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="9" cy="7" r="4"></circle>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                        </svg>
                                    </div>
                                    <div class="mentee-text-meta">
                                        <h3 class="mentee-primary-name">{{ $kNamaKelompok }}</h3>
                                        <p class="mentee-sub-inst">{{ $kNamaKetua }} (Ketua) &bull; {{ $kInstitusi }}</p>
                                    </div>
                                </div>

                                <div class="mentee-meta-right">
                                    <div class="mentee-stats-badges">
                                        <span class="mentee-count-chip" style="background: #EFF6FF; color: #1D4ED8; font-weight: 700;">
                                            {{ $totalAnggotaTim }} Peserta
                                        </span>
                                    </div>
                                    <div class="mentee-chevron-icon" aria-hidden="true">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="9 18 15 12 9 6"></polyline>
                                        </svg>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                @endif

                @if($showIndividu)
                    @foreach($penempatanList as $penempatan)
                        @php
                            $mentee = $penempatan->peserta;
                            $menteeNama = $mentee?->nama ?? 'Peserta Magang';
                            $institusi = $mentee?->profil?->institusi ?? 'Institusi Belum Dilengkapi';
                            $initials = collect(explode(' ', $menteeNama))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
                            $menteeLogbooks = $penempatan->logbooks ?? collect();
                        @endphp

                        <div class="mentee-classroom-card" data-mentee-id="{{ $penempatan->id }}">
                            <a href="{{ route('pembimbing.logbook.index', array_merge(request()->query(), ['masuk' => $penempatan->id])) }}" 
                               class="mentee-card-header mentee-card-link"
                               title="Masuk ke rincian logbook {{ $menteeNama }}">
                                <div class="mentee-info-left">
                                    <div class="mentee-avatar-circle" aria-hidden="true">
                                        {{ strtoupper($initials) }}
                                    </div>
                                    <div class="mentee-text-meta">
                                        <h3 class="mentee-primary-name">{{ $menteeNama }}</h3>
                                        <p class="mentee-sub-inst">{{ $institusi }}</p>
                                    </div>
                                </div>

                                @php
                                    $totalLogsCount = $menteeLogbooks->count();
                                    $catatanCount = $menteeLogbooks->filter(fn($l) => !empty($l->catatan_pembimbing))->count();
                                    $menungguCount = $menteeLogbooks->filter(fn($l) => in_array($l->status->value ?? (string)$l->status, ['terkirim', 'menunggu']))->count();
                                @endphp

                                <div class="mentee-meta-right">
                                    @if($totalLogsCount > 0)
                                        <div class="mentee-stats-badges">
                                            <span class="mentee-count-chip">{{ $totalLogsCount }} Logbook</span>
                                            @if($catatanCount > 0)
                                                <span class="mentee-count-chip selesai">{{ $catatanCount }} Diberi Catatan</span>
                                            @endif
                                            @if($menungguCount > 0)
                                                <span class="mentee-count-chip review">{{ $menungguCount }} Menunggu</span>
                                            @endif
                                        </div>
                                    @endif
                                    <div class="mentee-chevron-icon" aria-hidden="true">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="9 18 15 12 9 6"></polyline>
                                        </svg>
                                    </div>
                                </div>
                            </a>

                            <div id="logs-mentee-{{ $penempatan->id }}" class="mentee-tasks-drawer" hidden>
                            </div>
                        </div>
                    @endforeach
                @endif

                @if(!$hasItems)
                    <div class="table-empty-td logbook-empty-peserta">
                        @if($activeJenis === 'kelompok')
                            Belum ada tim magang bimbingan yang sesuai dengan filter.
                        @else
                            Tidak ada peserta bimbingan yang sesuai dengan filter.
                        @endif
                    </div>
                @endif
            </div>
        @endif

        {{-- 5. Paginasi Halaman Logbook / Mentee --}}
        @if($isMasukMode)
            @if($logbooks->hasPages())
                <div id="logbookPaginationWrapper" style="margin-top: 24px;">
                    {{ $logbooks->links() }}
                </div>
            @endif
        @elseif(!$isMasukKelompokMode && ($effectiveJenisLogbook ?? 'semua') !== 'kelompok')
            @if(method_exists($penempatanList, 'hasPages') && $penempatanList->hasPages())
                <div id="menteePaginationWrapper" style="margin-top: 24px;">
                    {{ $penempatanList->links() }}
                </div>
            @endif
        @endif
    </x-gentelella.x-panel>

</div>

@push('scripts')
    @vite(['resources/js/pages/pembimbing/logbook-index.js'])
@endpush
@endsection
