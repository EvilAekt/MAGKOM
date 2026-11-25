@extends('layouts.dashboard')

@section('title', 'Manajemen Penugasan Peserta - Portal MagKom')
@section('topbar_title', 'Tugas & Progres Magang')
@section('content_area_class', 'gentelella-fluid-canvas')

@push('styles')
    @vite(['resources/css/pages/pembimbing/tugas-index.css'])
@endpush

@section('content')
<div class="classroom-container">

    {{-- 1. Classroom Hero Banner Header Khas Sleman MagKom --}}
    @php
        $authUser = auth()->user();
        $bidangNama = $authUser->bidang?->nama_bidang ?? 'Dinas Komunikasi dan Informatika Sleman';
        $totalMentee = method_exists($penempatanList, 'total') ? $penempatanList->total() : $penempatanList->count();
    @endphp

    <div class="classroom-banner sc-reveal-hero" data-scroll-reveal>
        <div class="banner-top-row">
            <div>
                <h1 class="banner-title">Tugas & Progres Magang</h1>
                <div class="banner-subtitle">
                    <span>Bidang: <strong>{{ $bidangNama }}</strong></span>
                    <span>&bull;</span>
                    <span>Pembimbing: <strong>{{ $authUser->nama }}</strong></span>
                    <span>&bull;</span>
                    <span><strong>{{ $totalMentee }} Mentee Aktif Bimbingan</strong></span>
                </div>
            </div>
            <div>
                @if(auth()->user()?->isSuperAdmin())
                    <span class="badge-read-only-superadmin">
                        Mode Lihat Saja
                    </span>
                @else
                    <a href="{{ route('pembimbing.tugas.create') }}" class="btn-create-task">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>Buat Tugas Baru</span>
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- 2. Tile Stats MagKom Gentelella (Metrik Utama Selaras Tab) --}}
    @php
        $totalTugas = $counts['total'] ?? 0;
        $belumTugas = $counts['belum'] ?? 0;
        $selesaiTugas = $counts['selesai'] ?? 0;
        $reviewTugas = $counts['perlu_review'] ?? 0;

        $persenBelum = $totalTugas > 0 ? round(($belumTugas / $totalTugas) * 100) : 0;
        $persenSelesai = $totalTugas > 0 ? round(($selesaiTugas / $totalTugas) * 100) : 0;
        $persenReview = $totalTugas > 0 ? round(($reviewTugas / $totalTugas) * 100) : 0;
    @endphp

    <div class="tiles-grid sc-reveal-up" data-scroll-reveal>
        <x-gentelella.tile-stat
            title="Total Tugas"
            value="{{ $totalTugas }}"
            color="teal"
            trendDirection="up"
            trendText="{{ $totalTugas }} Total"
            subText="Seluruh penugasan magang"
            :sparkBars="[30, 45, 55, 60, 70, 78, 85, 92, 100]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <x-gentelella.tile-stat
            title="Ditugaskan"
            value="{{ $belumTugas }}"
            color="blue"
            trendType="info"
            trendDirection="up"
            trendText="{{ $persenBelum }}%"
            subText="Dalam proses pengerjaan"
            :sparkBars="[40, 50, 65, 55, 70, 60, 80, 75, 70]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <x-gentelella.tile-stat
            title="Selesai"
            value="{{ $selesaiTugas }}"
            color="teal"
            trendType="success"
            trendDirection="up"
            trendText="{{ $persenSelesai }}%"
            subText="Tuntas diverifikasi"
            :sparkBars="[20, 35, 45, 55, 65, 75, 80, 90, 95]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <x-gentelella.tile-stat
            title="Perlu Evaluasi"
            value="{{ $reviewTugas }}"
            color="yellow"
            trendType="{{ $reviewTugas > 0 ? 'warning' : 'success' }}"
            trendDirection="{{ $reviewTugas > 0 ? 'down' : 'up' }}"
            trendText="{{ $reviewTugas > 0 ? $reviewTugas . ' Review' : 'Tuntas' }}"
            subText="Laporan menunggu review"
            :sparkBars="[55, 70, 80, 65, 85, 90, 75, 60, 45]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>
    </div>

    {{-- Filter Baris Jenis Tugas: Semua, Individu, Kelompok (Kebutuhan 2 & 3 PRD MagKom) --}}
    @php
        $isMasukMode = !empty($masukPenempatan);
        $isMasukKelompokMode = !empty($masukKelompok);
        $effectiveJenisTugas = $jenisTugas ?? 'semua';
        if ($isMasukKelompokMode || request()->filled('dari_kelompok')) {
            $effectiveJenisTugas = 'kelompok';
        } elseif ($isMasukMode && $masukPenempatan) {
            $effectiveJenisTugas = $masukPenempatan->isKelompok() ? 'kelompok' : 'individu';
        }
    @endphp
    <div class="tugas-jenis-filter-nav" style="display: flex; gap: 8px; margin-bottom: 1.25rem; flex-wrap: wrap;">
        @if($isMasukKelompokMode)
            <a href="{{ route('pembimbing.tugas.index', ['masuk_kelompok' => $masukKelompok->id, 'jenis_tugas' => 'kelompok', 'tab_tim' => 'anggota']) }}"
               class="filter-pill {{ ($tabTim ?? 'anggota') === 'anggota' ? 'active' : '' }}"
               role="tab"
               aria-selected="{{ ($tabTim ?? 'anggota') === 'anggota' ? 'true' : 'false' }}">
                <span>Anggota &amp; Tugas Individu ({{ $masukKelompokMembers->count() }})</span>
            </a>
            <a href="{{ route('pembimbing.tugas.index', ['masuk_kelompok' => $masukKelompok->id, 'jenis_tugas' => 'kelompok', 'tab_tim' => 'tugas_tim']) }}"
               class="filter-pill {{ ($tabTim ?? 'anggota') === 'tugas_tim' ? 'active' : '' }}"
               role="tab"
               aria-selected="{{ ($tabTim ?? 'anggota') === 'tugas_tim' ? 'true' : 'false' }}">
                <span>Tugas Tim ({{ ($masukKelompokTugasTim ?? collect())->count() }})</span>
            </a>
        @else
            <a href="{{ route('pembimbing.tugas.index', array_merge(request()->except(['jenis_tugas', 'page', 'masuk', 'masuk_kelompok', 'dari_kelompok', 'tab_tim']), ['jenis_tugas' => 'semua'])) }}"
               class="filter-pill {{ $effectiveJenisTugas === 'semua' ? 'active' : '' }}">
                <span>Semua ({{ $filterCounts['semua'] ?? 0 }})</span>
            </a>
            <a href="{{ route('pembimbing.tugas.index', array_merge(request()->except(['jenis_tugas', 'page', 'masuk', 'masuk_kelompok', 'dari_kelompok', 'tab_tim']), ['jenis_tugas' => 'individu'])) }}"
               class="filter-pill {{ $effectiveJenisTugas === 'individu' ? 'active' : '' }}">
                <span>Individu ({{ $filterCounts['individu'] ?? 0 }})</span>
            </a>
            <a href="{{ route('pembimbing.tugas.index', array_merge(request()->except(['jenis_tugas', 'page', 'masuk', 'masuk_kelompok', 'dari_kelompok', 'tab_tim']), ['jenis_tugas' => 'kelompok'])) }}"
               class="filter-pill {{ $effectiveJenisTugas === 'kelompok' ? 'active' : '' }}">
                <span>Tim ({{ $filterCounts['kelompok'] ?? $kelompokList->count() }} Tim)</span>
            </a>
        @endif
    </div>

    {{-- 2.5 Filter Bar: Sederhana (HANYA Filter Nama Peserta) --}}
    @php
        $filterPenempatanList = $isMasukKelompokMode ? $masukKelompokPenempatan : ($allPenempatan ?? $penempatanList);
        $isFocusActive = !empty($selectedPenempatan);
        $displayPenempatanList = $isMasukMode ? collect([$masukPenempatan]) : ($isMasukKelompokMode ? $masukKelompokPenempatan : $penempatanList);
    @endphp
    <div id="tugasFilterCard" class="tugas-filter-card{{ $isMasukMode || $isMasukKelompokMode ? ' d-none' : '' }}">
        <form method="GET" action="{{ route('pembimbing.tugas.index') }}" class="tugas-filter-form">
            @if(request()->filled('jenis_tugas'))
                <input type="hidden" name="jenis_tugas" value="{{ request('jenis_tugas') }}">
            @endif
            @if(request()->filled('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            @if(request()->filled('kategori'))
                <input type="hidden" name="kategori" value="{{ request('kategori') }}">
            @endif

            <div class="filter-group" style="flex: 1.3;">
                <label for="inputSearchTugas">{{ ($jenisTugas ?? '') === 'kelompok' ? 'Pencarian Tim' : 'Pencarian Peserta' }}</label>
                <div class="peserta-search-input-wrap">
                    <input type="text" name="q" id="inputSearchTugas" value="{{ request('q') }}" placeholder="{{ ($jenisTugas ?? '') === 'kelompok' ? 'Cari nama tim, nama ketua...' : 'Cari nama, kampus, NIM...' }}" class="peserta-search-input">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="peserta-search-icon">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </div>
            </div>

            @if(($jenisTugas ?? '') !== 'kelompok')
                <div class="filter-group">
                    <label for="selectPenempatan">Filter Nama Peserta</label>
                    <select name="penempatan_id" id="selectPenempatan" class="filter-control" data-action="auto-submit-form">
                        <option value="">Semua Peserta Bimbingan</option>
                        @foreach($filterPenempatanList as $pen)
                            <option value="{{ $pen->id }}" {{ request('penempatan_id') == $pen->id ? 'selected' : '' }}>
                                {{ $pen->peserta?->nama }} ({{ $pen->peserta?->profil?->institusi ?? 'Institusi' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
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
            @endif

            <div class="filter-actions">
                <button type="submit" class="btn-filter-apply">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                    <span>Filter</span>
                </button>
                @if(request()->anyFilled(['penempatan_id', 'bulan', 'q']))
                    <a href="{{ route('pembimbing.tugas.index', request()->except(['penempatan_id', 'bulan', 'q'])) }}" class="btn-filter-reset">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- 3. X-Panel Daftar Penugasan & Stream Ala Classroom --}}
    <x-gentelella.x-panel id="tugasMainPanel">
        <x-slot:title>
            @if($isMasukMode)
                @php
                    $masukMentee = $masukPenempatan->peserta;
                    $masukNama = $masukMentee?->nama ?? 'Mentee Belum Ditentukan';
                    $masukInst = $masukMentee?->profil?->institusi ?? 'Institusi Belum Dilengkapi';
                    $masukInitials = collect(explode(' ', $masukNama))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
                @endphp
                <div class="mentee-panel-title-wrap">
                    <div class="mentee-avatar-circle" aria-hidden="true">
                        @if($masukMentee?->profil?->foto_url)
                            <img src="{{ $masukMentee->profil->foto_url }}" alt="{{ $masukNama }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                        @else
                            {{ $masukInitials }}
                        @endif
                    </div>
                    <div class="mentee-text-meta">
                        <h3 class="mentee-primary-name">{{ $masukNama }}</h3>
                        <p class="mentee-sub-inst">{{ $masukInst }}</p>
                    </div>
                </div>
            @elseif($isMasukKelompokMode)
                @php
                    $kNama = $masukKelompok->nama_kelompok ?? 'Tim Magang';
                    $kKetuaNama = $masukKelompok->ketua?->nama ?? 'Ketua Tim';
                    $kInstitusi = $masukKelompok->institusi ?? ($masukKelompok->ketua?->profil?->institusi ?? 'Institusi Belum Dilengkapi');
                    $kMemberTotal = $masukKelompokMembers->count();
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
            @elseif(($jenisTugas ?? '') === 'kelompok')
                <span id="defaultTitleText" class="default-panel-title">
                    Daftar Tim Bimbingan Magang
                </span>
            @else
                <span id="defaultTitleText" class="default-panel-title">
                    Daftar Penugasan &amp; Progres Mentee
                </span>
            @endif
        </x-slot:title>

        @if($isMasukMode)
            <x-slot:actions>
                @if(request()->filled('dari_kelompok'))
                    <a href="{{ route('pembimbing.tugas.index', ['masuk_kelompok' => request('dari_kelompok'), 'jenis_tugas' => 'kelompok']) }}" 
                       class="mentee-chevron-back-btn" 
                       title="Kembali ke Detail Tim"
                       aria-label="Kembali ke Detail Tim">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                    </a>
                @else
                    <a href="{{ route('pembimbing.tugas.index', array_merge(request()->except(['masuk', 'dari_kelompok']), ['jenis_tugas' => $effectiveJenisTugas])) }}" 
                       class="mentee-chevron-back-btn" 
                       title="Kembali ke Daftar Penugasan"
                       aria-label="Kembali ke Daftar Penugasan">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                    </a>
                @endif
            </x-slot:actions>
        @elseif($isMasukKelompokMode)
            <x-slot:actions>
                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: nowrap; flex-shrink: 0;">
                    @if(!auth()->user()?->isSuperAdmin())
                        <a href="{{ route('pembimbing.tugas.create', ['tipe' => 'kelompok', 'kelompok_id' => $masukKelompok->id]) }}" 
                           class="btn-tugas-kelompok-action" 
                           title="Buat tugas baru untuk tim {{ $masukKelompok->nama_kelompok }}">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="flex-shrink: 0;"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            <span>Buat Tugas Tim</span>
                        </a>
                    @endif
                    <a href="{{ route('pembimbing.tugas.index', array_merge(request()->except(['masuk_kelompok', 'page', 'dari_kelompok', 'tab_tim']), ['jenis_tugas' => 'kelompok'])) }}" 
                       class="mentee-chevron-back-btn" 
                       title="Kembali ke Daftar Tim"
                       aria-label="Kembali ke Daftar Tim">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                    </a>
                </div>
            </x-slot:actions>
        @endif

        {{-- Switcher Mode Tampilan (Dihilangkan: Tampilan 100% Berbasis Kartu) --}}
        <div class="classroom-tabs-wrapper view-switcher-only" style="display: none;">
            <div class="view-switcher-group" role="group" aria-label="Pilih Mode Tampilan">
                <button type="button" id="btnViewCard" class="btn-toggle-view active" title="Tampilan Kartu Classroom" aria-pressed="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    <span>Kartu</span>
                </button>
                <button type="button" id="btnViewTable" class="btn-toggle-view" title="Tampilan Tabel Administratif" aria-pressed="false">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                    <span>Tabel</span>
                </button>
            </div>
        </div>

        {{-- 4. Tampilan Kartu Mentee & Stream Penugasan Ala Classroom --}}
        @if($isMasukMode)
            @php
                $tasks = $tugasList;
            @endphp
            <div id="menteeStreamContainer" class="mentee-stream-container mentee-stream-focused" data-mentee-id="{{ $masukPenempatan->id }}">
                <div id="tasks-mentee-{{ $masukPenempatan->id }}" class="mentee-tasks-drawer">
                    @if($tasks->count() > 0)
                        <div class="classroom-tasks-list">
                            @foreach($tasks as $tugas)
                                @php
                                    $tStatus = $tugas->status->value ?? (string)$tugas->status;
                                    $isTugasSelesai = $tStatus === 'selesai';
                                    $isTugasOverdue = $tugas->deadline && $tugas->deadline->isPast() && !$isTugasSelesai;
                                    $hasTugasPending = $tugas->progresLogs->contains(fn($l) => in_array($l->status_review?->value ?? (string)$l->status_review, ['menunggu']));
                                @endphp
                                <div class="classroom-task-row"
                                     data-task-url="{{ route('pembimbing.tugas.show', $tugas->id) }}"
                                     tabindex="0"
                                     role="button"
                                     aria-label="Tugas: {{ $tugas->judul }}. Klik dua kali untuk membuka detail."
                                     title="Klik dua kali (double click) untuk membuka detail tugas">
                                    <div class="task-row-left">
                                        <div class="task-row-icon {{ $isTugasSelesai ? 'icon-selesai' : ($hasTugasPending ? 'icon-pending' : 'icon-default') }}" aria-hidden="true">
                                            @if($isTugasSelesai)
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                            @elseif($hasTugasPending)
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                            @else
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect><line x1="9" y1="12" x2="15" y2="12"></line><line x1="9" y1="16" x2="13" y2="16"></line></svg>
                                            @endif
                                        </div>
                                        <div class="task-row-title-wrap">
                                            <a href="{{ route('pembimbing.tugas.show', $tugas->id) }}" class="task-row-title">
                                                {{ $tugas->judul }}
                                            </a>
                                        </div>
                                    </div>

                                    <div class="task-row-right">
                                        <div class="task-row-due {{ $isTugasOverdue ? 'overdue' : '' }}">
                                            @if($isTugasOverdue)
                                                <span>⚠️ Lewat: {{ $tugas->deadline?->translatedFormat('d M Y') ?? $tugas->deadline?->format('d M Y') }}</span>
                                            @elseif($tugas->deadline)
                                                <span>Tenggat: {{ $tugas->deadline->translatedFormat('d M Y') ?? $tugas->deadline->format('d M Y') }}</span>
                                            @else
                                                <span>Tanpa tenggat</span>
                                            @endif
                                        </div>

                                        <div class="task-status-pill">
                                            @if($isTugasSelesai)
                                                <x-gentelella.badge variant="success">Selesai</x-gentelella.badge>
                                            @elseif($hasTugasPending)
                                                <x-gentelella.badge variant="warning">Review</x-gentelella.badge>
                                            @else
                                                <x-gentelella.badge variant="info">Ditugaskan</x-gentelella.badge>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-mentee-tasks">
                            <p>Belum ada tugas untuk peserta ini{{ request('status') ? ' pada filter ini' : '' }}.</p>
                            @if(!auth()->user()?->isSuperAdmin())
                                <a href="{{ route('pembimbing.tugas.create', ['penempatan_id' => $masukPenempatan->id]) }}" class="btn-create-task-inline">
                                    <span>Buat Tugas Baru</span>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @elseif($isMasukKelompokMode)
            {{-- 4.B Mode Masuk Kelompok: Konten Tab Tugas Tim vs Anggota & Tugas Individu --}}
            @if(($tabTim ?? 'anggota') === 'tugas_tim')
                <div id="timTasksContainer" class="mentee-stream-container mentee-stream-focused">
                    @if(($masukKelompokTugasTim ?? collect())->isNotEmpty())
                        <div class="classroom-tasks-list">
                            @foreach($masukKelompokTugasTim as $tugasTim)
                                @php
                                    $tStatus = $tugasTim->status->value ?? (string)$tugasTim->status;
                                    $isTugasSelesai = $tStatus === 'selesai';
                                    $isTugasOverdue = $tugasTim->deadline && $tugasTim->deadline->isPast() && !$isTugasSelesai;
                                    $hasTugasPending = $tugasTim->progresLogs->contains(fn($l) => in_array($l->status_review?->value ?? (string)$l->status_review, ['menunggu']));
                                    $latestPelapor = $tugasTim->progresLogs->sortByDesc('id')->first()?->pelapor?->nama;
                                @endphp
                                <div class="classroom-task-row"
                                     data-task-url="{{ route('pembimbing.tugas.show', $tugasTim->id) }}"
                                     tabindex="0"
                                     role="button"
                                     aria-label="Tugas Tim: {{ $tugasTim->judul }}. Klik dua kali untuk membuka review.">
                                    <div class="task-row-left">
                                        <div class="task-row-icon {{ $isTugasSelesai ? 'icon-selesai' : ($hasTugasPending ? 'icon-pending' : 'icon-default') }}" aria-hidden="true">
                                            @if($isTugasSelesai)
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                            @elseif($hasTugasPending)
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                            @else
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                            @endif
                                        </div>
                                        <div class="task-row-title-wrap">
                                            <a href="{{ route('pembimbing.tugas.show', $tugasTim->id) }}" class="task-row-title">
                                                {{ $tugasTim->judul }}
                                            </a>
                                            <div class="tim-task-submeta">
                                                <span class="tim-badge-chip">Tugas Tim &bull; {{ $tugasTim->pesertaDitugaskan->count() }} Anggota</span>
                                                @if($latestPelapor)
                                                    <span class="tim-pelapor-note">Laporan terakhir: {{ $latestPelapor }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="task-row-right">
                                        <div class="task-row-due {{ $isTugasOverdue ? 'overdue' : '' }}">
                                            @if($isTugasOverdue)
                                                <span>⚠️ Lewat: {{ $tugasTim->deadline?->translatedFormat('d M Y') ?? $tugasTim->deadline?->format('d M Y') }}</span>
                                            @elseif($tugasTim->deadline)
                                                <span>Tenggat: {{ $tugasTim->deadline->translatedFormat('d M Y') ?? $tugasTim->deadline->format('d M Y') }}</span>
                                            @else
                                                <span>Tanpa tenggat</span>
                                            @endif
                                        </div>

                                        <div class="task-status-pill">
                                            @if($isTugasSelesai)
                                                <x-gentelella.badge variant="success">Selesai</x-gentelella.badge>
                                            @elseif($hasTugasPending)
                                                <x-gentelella.badge variant="warning">Review</x-gentelella.badge>
                                            @else
                                                <x-gentelella.badge variant="info">Ditugaskan</x-gentelella.badge>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-mentee-tasks">
                            <p>Belum ada tugas tim yang diberikan untuk {{ $masukKelompok->nama_kelompok }}.</p>
                            @if(!auth()->user()?->isSuperAdmin())
                                <a href="{{ route('pembimbing.tugas.create', ['tipe' => 'kelompok', 'kelompok_id' => $masukKelompok->id]) }}" class="btn-create-task-inline">
                                    <span>Buat Tugas Tim Baru</span>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            @else
                <div id="menteeStreamContainer" class="mentee-stream-container">
                @forelse($masukKelompokMembers as $mUser)
                    @php
                        $isKetua = (int)$mUser->id === (int)$masukKelompok->ketua_user_id;
                        $mPenempatan = $masukKelompokPenempatan->firstWhere('peserta_id', $mUser->id);
                        $mNama = $mUser->nama;
                        $mInst = $mUser->profil?->institusi ?? 'Institusi Belum Dilengkapi';
                        $mJurusan = $mUser->profil?->jurusan?->nama_jurusan;
                        $mSub = $mJurusan ? "{$mInst} • {$mJurusan}" : $mInst;
                        $mInitials = collect(explode(' ', $mNama))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');

                        $mTasks = $mPenempatan 
                            ? ($mPenempatan->tugas ?? collect())->merge($mPenempatan->projectBersama ?? collect())->unique('id')
                            : collect();

                        $totalTasksCount = $mTasks->count();
                        $completedCount = $mTasks->filter(fn($t) => ($t->status->value ?? (string)$t->status) === 'selesai')->count();
                        $pendingReviewCount = $mTasks->filter(fn($t) => $t->progresLogs->contains(fn($l) => in_array($l->status_review?->value ?? (string)$l->status_review, ['menunggu'])))->count();
                    @endphp

                    <div class="mentee-classroom-card" 
                         data-mentee-id="{{ $mPenempatan?->id }}" 
                         data-mentee-name="{{ $mNama }}" 
                         data-mentee-inst="{{ $mInst }}">
                        @if($mPenempatan)
                            <a href="{{ route('pembimbing.tugas.index', ['masuk' => $mPenempatan->id, 'dari_kelompok' => $masukKelompok->id, 'jenis_tugas' => 'kelompok']) }}" 
                               class="mentee-card-header mentee-card-link"
                               title="Masuk ke rincian tugas {{ $mNama }}">
                        @else
                            <div class="mentee-card-header" style="cursor: default;">
                        @endif
                            <div class="mentee-info-left">
                                <div class="mentee-avatar-circle" aria-hidden="true">
                                    @if($mUser->profil?->foto_url)
                                        <img src="{{ $mUser->profil->foto_url }}" alt="{{ $mNama }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                    @else
                                        {{ $mInitials }}
                                    @endif
                                </div>
                                <div class="mentee-text-meta">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <h3 class="mentee-primary-name" style="margin: 0;">{{ $mNama }}</h3>
                                        @if($isKetua)
                                            <span style="font-size: 0.725rem; font-weight: 800; padding: 2px 8px; border-radius: 6px; background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A;">Ketua Tim</span>
                                        @else
                                            <span style="font-size: 0.725rem; font-weight: 700; padding: 2px 8px; border-radius: 6px; background: #F1F5F9; color: #475569;">Anggota</span>
                                        @endif
                                    </div>
                                    <p class="mentee-sub-inst">{{ $mSub }}</p>
                                </div>
                            </div>

                            <div class="mentee-meta-right">
                                @if($totalTasksCount > 0)
                                    <div class="mentee-stats-badges">
                                        <span class="mentee-count-chip">{{ $totalTasksCount }} Tugas</span>
                                        @if($completedCount > 0)
                                            <span class="mentee-count-chip selesai">{{ $completedCount }} Selesai</span>
                                        @endif
                                        @if($pendingReviewCount > 0)
                                            <span class="mentee-count-chip review">{{ $pendingReviewCount }} Review</span>
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
                    <div class="empty-state-card">
                        <p class="empty-state-desc">Belum ada anggota yang terdaftar pada tim ini.</p>
                    </div>
                @endforelse
                </div>
            @endif
        @else
            @php
                $activeJenis = $jenisTugas ?? 'semua';
                $showKelompok = $activeJenis === 'kelompok' || ($activeJenis === 'semua' && $kelompokList->isNotEmpty());
                $showIndividu = $activeJenis === 'individu' || ($activeJenis === 'semua' && $penempatanList->isNotEmpty());
                $hasItems = ($showKelompok && $kelompokList->isNotEmpty()) || ($showIndividu && $penempatanList->isNotEmpty());
            @endphp

            <div id="menteeStreamContainer" class="mentee-stream-container">
                @if($showKelompok)
                    @foreach($kelompokList as $kel)
                        @php
                            $kNamaKetua = $kel->ketua?->nama ?? 'Ketua Tim';
                            $kNamaKelompok = $kel->nama_kelompok ?? ('Tim #' . $kel->id);
                            $totalAnggotaTim = ($kel->anggota_count ?? $kel->anggota->count()) + 1;
                            $kInstitusi = $kel->institusi ?? ($kel->ketua?->profil?->institusi ?? 'Institusi Belum Dilengkapi');
                        @endphp
                        <div class="mentee-classroom-card" 
                             data-kelompok-id="{{ $kel->id }}" 
                             data-kelompok-name="{{ $kNamaKelompok }}" 
                             data-ketua-name="{{ $kNamaKetua }}">
                            <a href="{{ route('pembimbing.tugas.index', array_merge(request()->except(['page', 'masuk']), ['masuk_kelompok' => $kel->id, 'jenis_tugas' => 'kelompok'])) }}" 
                               class="mentee-card-header mentee-card-link"
                               title="Masuk ke tim {{ $kNamaKelompok }}"
                               data-detail-url="{{ route('pembimbing.tugas.kelompok.show', $kel->id) }}">
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
                            $menteeNama = $mentee?->nama ?? 'Mentee Belum Ditentukan';
                            $institusi = $mentee?->profil?->institusi ?? 'Institusi Belum Dilengkapi';
                            $initials = collect(explode(' ', $menteeNama))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
                            
                            $allTasks = ($penempatan->tugas ?? collect())
                                ->merge($penempatan->projectBersama ?? collect())
                                ->unique('id');

                            if (($jenisTugas ?? '') === 'individu') {
                                $allTasks = $allTasks->filter(fn($t) => ($t->tipe->value ?? (string)$t->tipe) === 'individu');
                            } elseif (($jenisTugas ?? '') === 'kelompok') {
                                $allTasks = $allTasks->filter(fn($t) => ($t->tipe->value ?? (string)$t->tipe) === 'kelompok');
                            }

                            $activeStatus = request('status');
                            $tasks = $allTasks;
                            if ($activeStatus === 'belum') {
                                $tasks = $allTasks->filter(fn($t) => in_array($t->status->value ?? (string)$t->status, ['belum', 'proses']));
                            } elseif ($activeStatus === 'selesai') {
                                $tasks = $allTasks->filter(fn($t) => ($t->status->value ?? (string)$t->status) === 'selesai');
                            } elseif ($activeStatus === 'perlu_review') {
                                $tasks = $allTasks->filter(fn($t) => $t->progresLogs->contains(fn($l) => in_array($l->status_review?->value ?? (string)$l->status_review, ['menunggu'])));
                            }

                            $totalTasksCount = $allTasks->count();
                            $completedCount = $allTasks->filter(fn($t) => ($t->status->value ?? (string)$t->status) === 'selesai')->count();
                            $pendingReviewCount = $allTasks->filter(fn($t) => $t->progresLogs->contains(fn($l) => in_array($l->status_review?->value ?? (string)$l->status_review, ['menunggu'])))->count();
                        @endphp

                        <div class="mentee-classroom-card" 
                             data-mentee-id="{{ $penempatan->id }}" 
                             data-mentee-name="{{ $menteeNama }}" 
                             data-mentee-inst="{{ $institusi }}">
                            <a href="{{ route('pembimbing.tugas.index', array_merge(request()->except(['page']), ['masuk' => $penempatan->id, 'jenis_tugas' => ($effectiveJenisTugas === 'kelompok' ? 'kelompok' : ($penempatan->isKelompok() ? 'kelompok' : 'individu'))])) }}" 
                               class="mentee-card-header mentee-card-link"
                               title="Masuk ke rincian tugas {{ $menteeNama }}">
                                <div class="mentee-info-left">
                                    <div class="mentee-avatar-circle" aria-hidden="true">
                                        @if($penempatan->peserta?->profil?->foto_url)
                                            <img src="{{ $penempatan->peserta->profil->foto_url }}" alt="{{ $menteeNama }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                        @else
                                            {{ $initials }}
                                        @endif
                                    </div>
                                    <div class="mentee-text-meta">
                                        <h3 class="mentee-primary-name">{{ $menteeNama }}</h3>
                                        <p class="mentee-sub-inst">{{ $institusi }}</p>
                                    </div>
                                </div>

                                <div class="mentee-meta-right">
                                    @if($totalTasksCount > 0)
                                        <div class="mentee-stats-badges">
                                            <span class="mentee-count-chip">{{ $totalTasksCount }} Tugas</span>
                                            @if($completedCount > 0)
                                                <span class="mentee-count-chip selesai">{{ $completedCount }} Selesai</span>
                                            @endif
                                            @if($pendingReviewCount > 0)
                                                <span class="mentee-count-chip review">{{ $pendingReviewCount }} Review</span>
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

                            <div id="tasks-mentee-{{ $penempatan->id }}" class="mentee-tasks-drawer" hidden>
                            </div>
                        </div>
                    @endforeach
                @endif

                @if(! $hasItems)
                    <div class="empty-state-card">
                        <div class="empty-state-icon">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                        <h3 class="empty-state-title">
                            @if($activeJenis === 'kelompok')
                                Belum Ada Tim Magang Bimbingan
                            @elseif($activeJenis === 'individu')
                                Belum Ada Mentee Bimbingan Aktif
                            @else
                                Belum Ada Mentee atau Tim Bimbingan
                            @endif
                        </h3>
                        <p class="empty-state-desc">
                            @if($activeJenis === 'kelompok')
                                Saat ini belum ada peserta magang jalur tim yang ditempatkan pada bimbingan Anda.
                            @elseif($activeJenis === 'individu')
                                Saat ini belum ada peserta magang jalur individu yang dialokasikan dalam bimbingan Anda.
                            @else
                                Saat ini belum ada peserta magang maupun tim yang dialokasikan dalam bimbingan Anda.
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        @endif

        {{-- 5. Tampilan Tabel Administratif Asli (Table View Mode Ala MagKom Gentelella) --}}
        <div id="classroomTableContainer" class="classroom-table-container {{ $isMasukMode ? 'is-focused' : '' }}" style="display: none;">
            
            {{-- A. Sub-View 1: Tabel Ringkasan Peserta Magang (Level Semua Mentee - Tabel Murni Beda Format dari Cart) --}}
            <div id="tableMenteesSummaryView" class="table-responsive-wrapper {{ $isMasukMode || $isMasukKelompokMode ? 'd-none' : '' }}">
                <table class="task-table mentee-summary-table" id="menteesSummaryTable">
                    <thead>
                        <tr>
                            <th class="col-mentee-no">No</th>
                            <th class="col-peserta">Peserta Magang</th>
                            <th class="col-mentee-inst">Institusi / Asal</th>
                            <th class="col-mentee-total">Total Tugas</th>
                            <th class="col-mentee-status">Status Progres</th>
                            <th class="col-mentee-aksi">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($displayPenempatanList as $idx => $penempatan)
                            @php
                                $mentee = $penempatan->peserta;
                                $menteeNama = $mentee?->nama ?? 'Mentee Belum Ditentukan';
                                $institusi = $mentee?->profil?->institusi ?? 'Institusi Belum Dilengkapi';
                                $initials = collect(explode(' ', $menteeNama))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
                                
                                $allTasks = ($penempatan->tugas ?? collect())
                                    ->merge($penempatan->projectBersama ?? collect())
                                    ->unique('id');

                                if (($jenisTugas ?? '') === 'individu') {
                                    $allTasks = $allTasks->filter(fn($t) => ($t->tipe->value ?? (string)$t->tipe) === 'individu');
                                } elseif (($jenisTugas ?? '') === 'kelompok') {
                                    $allTasks = $allTasks->filter(fn($t) => ($t->tipe->value ?? (string)$t->tipe) === 'kelompok');
                                }

                                $totalCount = $allTasks->count();
                                $selesaiCount = $allTasks->filter(fn($t) => ($t->status->value ?? (string)$t->status) === 'selesai')->count();
                                $belumCount = $allTasks->filter(fn($t) => in_array($t->status->value ?? (string)$t->status, ['belum', 'proses']))->count();
                                $reviewCount = $allTasks->filter(fn($t) => $t->progresLogs->contains(fn($l) => in_array($l->status_review?->value ?? (string)$l->status_review, ['menunggu'])))->count();
                            @endphp
                            <tr class="mentee-table-row" data-mentee-id="{{ $penempatan->id }}">
                                <td class="col-mentee-no">{{ (method_exists($penempatanList, 'firstItem') ? $penempatanList->firstItem() : 1) + $idx }}</td>
                                <td class="col-peserta">
                                    <div class="mentee-table-cell">
                                        <div class="mentee-avatar-mini" aria-hidden="true">{{ $initials }}</div>
                                        <div class="mentee-table-names">
                                            <strong class="mentee-table-primary-name">{{ $menteeNama }}</strong>
                                        </div>
                                    </div>
                                </td>
                                <td class="col-mentee-inst">
                                    <span class="mentee-table-inst-text">{{ $institusi }}</span>
                                </td>
                                <td class="col-mentee-total">
                                    <span class="mentee-count-chip">{{ $totalCount }} Tugas</span>
                                </td>
                                <td class="col-mentee-status">
                                    <div class="mentee-progress-chips">
                                        <span class="mentee-count-chip selesai">{{ $selesaiCount }} Selesai</span>
                                        @if($reviewCount > 0)
                                            <span class="mentee-count-chip review">{{ $reviewCount }} Review</span>
                                        @endif
                                        @if($belumCount > 0)
                                            <span class="mentee-count-chip">{{ $belumCount }} Ditugaskan</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="col-mentee-aksi">
                                    <a href="{{ route('pembimbing.tugas.index', array_merge(request()->query(), ['masuk' => $penempatan->id])) }}" 
                                       class="btn-tugas-action" 
                                       title="Lihat tugas {{ $menteeNama }}">
                                        <span>Lihat Tugas</span>
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="table-empty-td">
                                    Belum ada peserta magang dalam bimbingan aktif Anda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- B. Sub-View 2: Tabel Penugasan Mentee Terfokus (Level Mentee Terpilih) --}}
            <div id="tableMenteeTasksView" class="table-responsive-wrapper {{ $isMasukMode ? '' : 'd-none' }}">
                <table class="task-table {{ $isMasukMode ? 'table-focused-mentee' : '' }}" id="mainTugasTable">
                    <thead>
                        <tr>
                            <th class="col-judul">Judul Penugasan</th>
                            <th class="col-peserta">Peserta Magang</th>
                            <th class="col-due">Tenggat Waktu</th>
                            <th class="col-status">Status</th>
                            <th class="col-aksi">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tugasTableBody">
                        @forelse($tugasList as $tugas)
                            @php
                                $tStatus = $tugas->status->value ?? (string)$tugas->status;
                                $isTugasSelesai = $tStatus === 'selesai';
                                $isTugasOverdue = $tugas->deadline && $tugas->deadline->isPast() && !$isTugasSelesai;
                                $hasTugasPending = $tugas->progresLogs->contains(fn($l) => in_array($l->status_review?->value ?? (string)$l->status_review, ['menunggu']));
                                $mentee = $tugas->penempatan?->peserta;
                                $menteeNama = $mentee?->nama ?? 'Belum Ditentukan';
                                $institusi = $mentee?->profil?->institusi ?? 'Institusi Belum Dilengkapi';
                            @endphp
                            <tr class="classroom-table-row" data-mentee-id="{{ $tugas->penempatan_id }}">
                                <td class="col-judul">
                                    <div class="task-row-left">
                                        <div class="task-row-icon {{ $isTugasSelesai ? 'icon-selesai' : ($hasTugasPending ? 'icon-pending' : 'icon-default') }}" aria-hidden="true">
                                            @if($isTugasSelesai)
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                            @elseif($hasTugasPending)
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                            @else
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect><line x1="9" y1="12" x2="15" y2="12"></line><line x1="9" y1="16" x2="13" y2="16"></line></svg>
                                            @endif
                                        </div>
                                        <div class="task-row-title-wrap">
                                            <a href="{{ route('pembimbing.tugas.show', $tugas->id) }}" class="task-row-title">
                                                {{ $tugas->judul }}
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td class="col-peserta">
                                    <div class="table-mentee-cell">
                                        <button type="button" 
                                                class="btn-mentee-filter-link"
                                                data-action="focus-mentee" 
                                                data-mentee-id="{{ $tugas->penempatan_id }}" 
                                                data-mentee-name="{{ $menteeNama }}" 
                                                data-mentee-inst="{{ $institusi }}"
                                                title="Fokus hanya tugas {{ $menteeNama }}">
                                            <span class="table-mentee-name">{{ $menteeNama }}</span>
                                            <span class="table-mentee-inst">{{ $institusi }}</span>
                                        </button>
                                    </div>
                                </td>
                                <td class="col-due">
                                    <div class="task-row-due {{ $isTugasOverdue ? 'overdue' : '' }}">
                                        @if($isTugasOverdue)
                                            <span>⚠️ Lewat: {{ $tugas->deadline?->translatedFormat('d M Y') ?? $tugas->deadline?->format('d M Y') }}</span>
                                        @elseif($tugas->deadline)
                                            <span>Tenggat: {{ $tugas->deadline->translatedFormat('d M Y') ?? $tugas->deadline->format('d M Y') }}</span>
                                        @else
                                            <span>Tanpa tenggat</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="col-status">
                                    <div class="task-status-pill">
                                        @if($isTugasSelesai)
                                            <x-gentelella.badge variant="success">Selesai</x-gentelella.badge>
                                        @elseif($hasTugasPending)
                                            <x-gentelella.badge variant="warning">Review</x-gentelella.badge>
                                        @else
                                            <x-gentelella.badge variant="info">Ditugaskan</x-gentelella.badge>
                                        @endif
                                    </div>
                                </td>
                                <td class="col-aksi">
                                    <a href="{{ route('pembimbing.tugas.show', $tugas->id) }}" class="btn-tugas-action">
                                        <span>Detail</span>
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="table-empty-td">
                                    Belum ada penugasan yang sesuai dengan kriteria filter saat ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 5. Paginasi Halaman --}}
        @if($isMasukMode)
            @if($tugasList->hasPages())
                <div id="tugasPaginationWrapper" style="margin-top: 24px;">
                    {{ $tugasList->links() }}
                </div>
            @endif
        @elseif(!$isMasukKelompokMode)
            @if(method_exists($penempatanList, 'hasPages') && $penempatanList->hasPages())
                <div id="menteePaginationWrapper" style="margin-top: 24px;">
                    {{ $penempatanList->links() }}
                </div>
            @endif
        @endif
    </x-gentelella.x-panel>

</div>

@push('scripts')
    @vite(['resources/js/pages/pembimbing/tugas-index.js'])
@endpush
@endsection
