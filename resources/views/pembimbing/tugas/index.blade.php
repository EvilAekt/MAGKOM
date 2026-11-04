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

    <div class="classroom-banner">
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
                <a href="{{ route('pembimbing.tugas.create') }}" class="btn-create-task">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    + Buat Tugas Baru
                </a>
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

    <div class="tiles-grid">
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

    {{-- 3. X-Panel Daftar Penugasan & Stream Ala Classroom --}}
    @php
        $isFocusActive = !empty($selectedPenempatan);
        $focusedNama = $selectedPenempatan?->peserta?->nama ?? '';
        $focusedInst = $selectedPenempatan?->peserta?->profil?->institusi ?? '';
        $focusedInitials = collect(explode(' ', $focusedNama))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
    @endphp

    <x-gentelella.x-panel id="tugasMainPanel">
        <x-slot:title>
            {{-- Mode 1: Default Title saat Semua Mentee Tampil --}}
            <span id="defaultTitleText" class="default-panel-title {{ $isFocusActive ? 'd-none' : '' }}">
                Daftar Penugasan & Progres Mentee
            </span>

            {{-- Mode 2: Header Terpadu saat Mentee Difokuskan (Identitas di Kiri, Tombol Kembali di Kanan) --}}
            <div id="menteeUnifiedHeader" class="mentee-unified-header {{ $isFocusActive ? '' : 'd-none' }}">
                <div class="header-mentee-col">
                    <div id="focusedMenteeAvatar" class="header-mentee-avatar" aria-hidden="true">
                        {{ $focusedInitials }}
                    </div>
                    <div class="header-mentee-text">
                        <span id="focusedMenteeName" class="header-mentee-name">{{ $focusedNama }}</span>
                        <span id="focusedMenteeInst" class="header-mentee-inst">{{ $focusedInst }}</span>
                    </div>
                </div>

                <a href="{{ route('pembimbing.tugas.index', request()->except('penempatan_id')) }}" 
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

        {{-- Switcher Mode Tampilan (Card vs Table) --}}
        <div class="classroom-tabs-wrapper view-switcher-only">
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

        {{-- 4. Tampilan Kartu Mentee & Stream Penugasan Ala Classroom (Mentee-First Mode) --}}
        <div id="menteeStreamContainer" class="mentee-stream-container {{ $isFocusActive ? 'is-focused' : '' }}">
            @forelse($penempatanList as $penempatan)
                @php
                    $mentee = $penempatan->peserta;
                    $menteeNama = $mentee?->nama ?? 'Mentee Belum Ditentukan';
                    $institusi = $mentee?->profil?->institusi ?? 'Institusi Belum Dilengkapi';
                    $initials = collect(explode(' ', $menteeNama))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
                    
                    // Filter tugas sesuai tab yang aktif (jika ada)
                    $allTasks = $penempatan->tugas ?? collect();
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
                    $isThisMenteeSelected = ($selectedPenempatanId && $selectedPenempatanId === $penempatan->id);
                @endphp

                <div class="mentee-classroom-card {{ $isThisMenteeSelected ? 'is-active-focus is-expanded' : '' }}" 
                     data-mentee-id="{{ $penempatan->id }}" 
                     data-mentee-name="{{ $menteeNama }}" 
                     data-mentee-inst="{{ $institusi }}">
                    <!-- Header Kartu Mentee (Trigger Fokus Mentee) -->
                    <button type="button" 
                            class="mentee-card-header" 
                            data-action="focus-mentee"
                            data-mentee-id="{{ $penempatan->id }}"
                            data-mentee-name="{{ $menteeNama }}"
                            data-mentee-inst="{{ $institusi }}"
                            aria-expanded="{{ $isThisMenteeSelected ? 'true' : 'false' }}" 
                            aria-controls="tasks-mentee-{{ $penempatan->id }}"
                            title="Klik untuk melihat hanya tugas {{ $menteeNama }}">
                        <div class="mentee-info-left">
                            <div class="mentee-avatar-circle" aria-hidden="true">
                                {{ $initials }}
                            </div>
                            <div class="mentee-text-meta">
                                <h3 class="mentee-primary-name">{{ $menteeNama }}</h3>
                                <p class="mentee-sub-inst">{{ $institusi }}</p>
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

                    <!-- Drawer Tugas Mentee (Format Ramping Ala Google Classroom: Kartu & Tabel) -->
                    <div id="tasks-mentee-{{ $penempatan->id }}" class="mentee-tasks-drawer" {{ $isThisMenteeSelected ? '' : 'hidden' }}>
                        @if($tasks->count() > 0)
                            {{-- Mode Kartu Tugas Ala Google Classroom --}}
                            <div class="classroom-tasks-list">
                                @foreach($tasks as $tugas)
                                    @php
                                        $tStatus = $tugas->status->value ?? (string)$tugas->status;
                                        $isTugasSelesai = $tStatus === 'selesai';
                                        $isTugasOverdue = $tugas->deadline && $tugas->deadline->isPast() && !$isTugasSelesai;
                                        $hasTugasPending = $tugas->progresLogs->contains(fn($l) => in_array($l->status_review?->value ?? (string)$l->status_review, ['menunggu']));
                                    @endphp
                                    <div class="classroom-task-row">
                                        <div class="task-row-left">
                                            <div class="task-row-icon {{ $isTugasSelesai ? 'icon-selesai' : ($hasTugasPending ? 'icon-pending' : 'icon-default') }}" aria-hidden="true">
                                                @if($isTugasSelesai)
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                @elseif($hasTugasPending)
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                                @else
                                                    <!-- Ikon Clipboard Google Classroom -->
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

                                            <a href="{{ route('pembimbing.tugas.show', $tugas->id) }}" class="btn-tugas-action">
                                                <span>Detail</span>
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-mentee-tasks">
                                <p>Belum ada tugas untuk peserta ini{{ request('status') ? ' pada filter ini' : '' }}.</p>
                                <a href="{{ route('pembimbing.tugas.create', ['penempatan_id' => $penempatan->id]) }}" class="btn-create-task-inline">
                                    + Buat Tugas Baru
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="empty-state-card">
                    <div class="empty-state-icon">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <h3 class="empty-state-title">Belum Ada Mentee Bimbingan Aktif</h3>
                    <p class="empty-state-desc">Saat ini belum ada peserta magang yang dialokasikan dalam bimbingan Anda.</p>
                </div>
            @endforelse
        </div>

        {{-- 5. Tampilan Tabel Administratif Asli (Table View Mode Ala MagKom Gentelella) --}}
        <div id="classroomTableContainer" class="classroom-table-container {{ $isFocusActive ? 'is-focused' : '' }}" style="display: none;">
            
            {{-- A. Sub-View 1: Tabel Ringkasan Peserta Magang (Level Semua Mentee - Tabel Murni Beda Format dari Cart) --}}
            <div id="tableMenteesSummaryView" class="table-responsive-wrapper {{ $isFocusActive ? 'd-none' : '' }}">
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
                        @forelse($penempatanList as $idx => $penempatan)
                            @php
                                $mentee = $penempatan->peserta;
                                $menteeNama = $mentee?->nama ?? 'Mentee Belum Ditentukan';
                                $institusi = $mentee?->profil?->institusi ?? 'Institusi Belum Dilengkapi';
                                $initials = collect(explode(' ', $menteeNama))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
                                
                                $allTasks = $penempatan->tugas ?? collect();
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
                                    <button type="button" 
                                            class="btn-tugas-action" 
                                            data-action="focus-mentee" 
                                            data-mentee-id="{{ $penempatan->id }}" 
                                            data-mentee-name="{{ $menteeNama }}" 
                                            data-mentee-inst="{{ $institusi }}"
                                            title="Lihat tugas {{ $menteeNama }}">
                                        <span>Lihat Tugas</span>
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                    </button>
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
            <div id="tableMenteeTasksView" class="table-responsive-wrapper {{ $isFocusActive ? '' : 'd-none' }}">
                <table class="task-table {{ $isFocusActive ? 'table-focused-mentee' : '' }}" id="mainTugasTable">
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
        @if(!$isFocusActive && method_exists($penempatanList, 'hasPages') && $penempatanList->hasPages())
            <div id="menteePaginationWrapper" style="margin-top: 24px;">
                {{ $penempatanList->links() }}
            </div>
        @elseif($isFocusActive && $tugasList->hasPages())
            <div id="tugasPaginationWrapper" style="margin-top: 24px;">
                {{ $tugasList->links() }}
            </div>
        @endif
    </x-gentelella.x-panel>

</div>

@push('scripts')
    @vite(['resources/js/pages/pembimbing/tugas-index.js'])
@endpush
@endsection
