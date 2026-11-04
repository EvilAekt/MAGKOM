@extends('layouts.peserta')

@section('title', 'Logbook & Jurnal Mingguan Peserta')
@section('page_title', 'Logbook Mingguan')

@push('styles')
    @vite(['resources/css/pages/peserta/logbook-index.css'])
@endpush

@section('content')
<div class="logbook-wrapper">
    <x-gentelella.page-title 
        title="Logbook & Jurnal Mingguan Peserta" 
    />

    @if(!$penempatan)
        <div class="card-modern" style="border-left: 4px solid var(--amber-700); background: var(--amber-100); margin-bottom: 24px;">
            <div style="display: flex; align-items: flex-start; gap: 16px;">
                <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--amber-700); flex-shrink: 0; margin-top: 2px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                <div>
                    <h3 style="font-size: 1.0625rem; font-weight: 700; color: var(--amber-700); margin-bottom: 4px;">Penempatan Belum Aktif</h3>
                    <p style="font-size: 0.875rem; color: var(--neutral-700); margin: 0; line-height: 1.5;">
                        Anda belum memiliki penempatan magang aktif. Fitur pelaporan logbook mingguan akan terbuka secara otomatis setelah status penempatan Anda diverifikasi oleh administrator.
                    </p>
                </div>
            </div>
        </div>
    @else
        {{-- Banner Penempatan Magang --}}
        <div class="banner-penempatan">
            <div class="banner-content-wrap">
                <div style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 999px; background: rgba(255, 255, 255, 0.15); font-size: 0.75rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 12px; backdrop-filter: blur(4px);">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #4ADE80;"></span>
                    <span>Penempatan Magang Aktif</span>
                </div>
                <h1 class="banner-title">{{ $penempatan->bidang->nama_bidang ?? 'Dinas Komunikasi dan Informatika Sleman' }}</h1>
                <div class="banner-meta">
                    <span class="banner-meta-item">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <span>Pembimbing: <strong>{{ $penempatan->pembimbing->nama ?? $penempatan->pembimbing->name ?? 'Pembimbing Lapangan' }}</strong></span>
                    </span>
                    <span class="banner-meta-item">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span>Periode: <strong>{{ \Carbon\Carbon::parse($penempatan->tgl_mulai)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($penempatan->tgl_selesai)->translatedFormat('d M Y') }}</strong></span>
                    </span>
                </div>
            </div>

            <div class="banner-side-stat" style="text-align: right; background: rgba(0, 0, 0, 0.22); padding: 16px 20px; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.15); min-width: 170px;">
                <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: rgba(255, 255, 255, 0.85); font-weight: 700;">Minggu Berjalan Saat Ini</div>
                <div style="font-size: 1.75rem; font-weight: 900; color: #ffffff; line-height: 1.2; margin: 4px 0 2px 0;">Minggu ke-{{ $mingguAktif }}</div>
                <div style="font-size: 0.75rem; color: rgba(255, 255, 255, 0.75);">dari total {{ $totalMinggu }} minggu</div>
            </div>
        </div>

        {{-- Weekly Progress Tracker Card --}}
        <div class="progress-tracker-card">
            <div class="progress-tracker-header">
                <div class="progress-tracker-title">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: #01347C;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <span>Progres Pelaporan Logbook Mingguan</span>
                </div>
                <div style="font-size: 0.8125rem; font-weight: 700; color: #0F172A;">
                    {{ $totalTerisi }} dari {{ $totalMinggu }} Minggu Terisi ({{ $persentaseTerisi }}%)
                </div>
            </div>

            <div class="progress-bar-wrap">
                <div class="progress-bar-fill" style="width: {{ $persentaseTerisi }}%;"></div>
            </div>

            <div class="weekly-stepper-wrap">
                @foreach($daftarMinggu as $m)
                    @php
                        $isCurrent = $m['minggu_ke'] == $mingguAktif;
                        $hasLog = $m['logbook'] !== null;
                        $chipClass = $isCurrent ? 'is-active-week' : '';
                        if ($hasLog) {
                            $chipClass = match($m['logbook']->status->value ?? (string)$m['logbook']->status) {
                                'disetujui' => 'is-done',
                                'perlu_revisi', 'ditolak' => 'is-revisi',
                                default => 'is-waiting',
                            };
                        }
                    @endphp
                    <a href="#logbook-card-{{ $m['logbook']?->id ?? '' }}"
                       class="week-chip {{ $chipClass }}"
                       title="Minggu {{ $m['minggu_ke'] }}: {{ $m['label_periode'] }}">
                        @if($hasLog)
                            <span>✓</span>
                        @elseif($isCurrent)
                            <span>⏳</span>
                        @else
                            <span>📅</span>
                        @endif
                        <span>Minggu {{ $m['minggu_ke'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Smart Reminder Banner --}}
        @if(!$mingguAktifSudahDiisi && $penempatan)
            <div class="card-modern" style="background: #EFF6FF; border: 1px solid #BFDBFE; border-left: 4px solid #01347C; margin-bottom: 24px; padding: 18px 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                    <div style="display: flex; align-items: flex-start; gap: 14px;">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: #DBEAFE; color: #01347C; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <strong style="font-size: 0.9375rem; color: #1E3A8A; display: block; margin-bottom: 3px;">
                                Saatnya Mengisi Logbook Minggu ke-{{ $mingguAktif }}
                            </strong>
                            <p style="font-size: 0.8125rem; color: #1E40AF; margin: 0; line-height: 1.45;">
                                Laporkan ringkasan kegiatan dan capaian magang Anda pekan ini sebelum batas waktu verifikasi pembimbing.
                            </p>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="openTambahModal({{ $mingguAktif }})" style="height: 38px; padding: 0 16px; font-weight: 700;">
                        + Isi Logbook Minggu Ini
                    </button>
                </div>
            </div>
        @endif

        {{-- Action Bar --}}
        <div class="action-bar">
            <div>
                <h2 style="font-size: 1.25rem; font-weight: 800; color: #0F172A; margin: 0;">Riwayat Jurnal Mingguan</h2>
            </div>

            <div style="display: flex; align-items: center; gap: 12px;">
                <span class="badge badge-info" style="font-size: 0.8125rem; padding: 6px 12px;">
                    Total: {{ $logbooks->total() }} Laporan
                </span>
                <button type="button" class="btn btn-primary" onclick="openTambahModal()" style="height: 40px; padding: 0 16px; font-weight: 700;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                    </svg>
                    + Isi Logbook Mingguan
                </button>
            </div>
        </div>

        {{-- Search Bar (Tanpa Tombol Buka/Tutup Semua) --}}
        @if($logbooks->isNotEmpty())
            <div class="logbook-filter-bar">
                <div class="logbook-search-box">
                    <div class="search-input-wrapper">
                        <svg class="search-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"></path>
                        </svg>
                        <input type="text" 
                               id="inputSearchLogbook" 
                               class="search-logbook-input" 
                               placeholder="Cari minggu ke- (contoh: 1, 2) atau kata kunci kegiatan..." 
                               oninput="handleSearchLogbook(this.value)">
                        <button type="button" 
                                id="btnClearSearch" 
                                class="btn-clear-search" 
                                onclick="clearSearchLogbook()" 
                                style="display: none;" 
                                title="Hapus pencarian">
                            &times;
                        </button>
                    </div>
                    <span id="searchResultCount" class="search-result-count" style="display: none;"></span>
                </div>
            </div>
        @endif

        {{-- Daftar Kartu Logbook Mingguan (Default Tertutup & Efek Hover Ala Tugas) --}}
        @if($logbooks->isEmpty())
            <div class="card-modern" style="text-align: center; padding: 56px 24px;">
                <div style="width: 64px; height: 64px; border-radius: 50%; background: #EFF6FF; color: #01347C; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px;">
                    <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h3 style="font-size: 1.125rem; font-weight: 700; color: #0F172A; margin: 0 0 6px 0;">Belum Ada Riwayat Logbook</h3>
                <p style="font-size: 0.875rem; color: #475569; max-width: 440px; margin: 0 auto 20px auto; line-height: 1.5;">
                    Anda belum membuat catatan kegiatan mingguan. Mulai laporkan capaian magang Anda secara teratur setiap pekan.
                </p>
                <button type="button" class="btn btn-primary" onclick="openTambahModal({{ $mingguAktif }})">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                    </svg>
                    + Isi Logbook Pertama Anda
                </button>
            </div>
        @else
            <div id="logbookCardsContainer">
                <div id="searchEmptyState" class="card-modern" style="display: none; text-align: center; padding: 40px 20px; margin-bottom: 20px;">
                    <p style="font-size: 0.9375rem; color: #475569; margin: 0;">
                        Tidak ada logbook yang cocok dengan pencarian "<span id="searchQueryText" style="font-weight: 700; color: #0F172A;"></span>".
                    </p>
                    <button type="button" class="btn btn-secondary" onclick="clearSearchLogbook()" style="margin-top: 12px; font-size: 0.8125rem; height: 32px; padding: 0 14px;">
                        Reset Pencarian
                    </button>
                </div>

                @foreach($logbooks as $log)
                    @php
                        $statusValue = $log->status->value ?? (string)$log->status;
                        $isRevisi = in_array($statusValue, ['perlu_revisi', 'ditolak']);
                        $mingguKeTampil = $log->minggu_ke ?? 1;
                        $labelPeriodeTampil = $daftarMinggu[$mingguKeTampil - 1]['label_periode'] ?? $log->tanggal->translatedFormat('d M Y');
                    @endphp
                    <div class="weekly-card" 
                         id="logbook-card-{{ $log->id }}"
                         data-minggu="{{ $mingguKeTampil }}"
                         data-status="{{ $statusValue }}">
                        
                        {{-- Header Accordion --}}
                        <div class="weekly-card-header" onclick="toggleCard({{ $log->id }})">
                            <div class="weekly-badge-box">
                                <div class="week-number-pill" style="{{ $isRevisi ? 'background: #FEF3C7; color: #D97706; border-color: #FDE68A;' : '' }}">
                                    <span style="font-size: 0.625rem; font-weight: 700; text-transform: uppercase;">MG</span>
                                    <span style="font-size: 1.125rem;">{{ $mingguKeTampil }}</span>
                                </div>
                                <div>
                                    <div class="weekly-card-title">Logbook Minggu ke-{{ $mingguKeTampil }}</div>
                                    <div class="weekly-card-meta">
                                        <span>📅 Periode: <strong>{{ $labelPeriodeTampil }}</strong></span>
                                        <span>•</span>
                                        <span>Dilaporkan: {{ $log->tanggal->translatedFormat('d M Y') }}</span>
                                    </div>
                                    <div class="weekly-collapsed-preview">{{ Str::limit($log->kegiatan, 110) }}</div>
                                </div>
                            </div>

                            <div style="display: flex; align-items: center; gap: 10px;" onclick="event.stopPropagation();">
                                @if($isRevisi)
                                    <x-gentelella.badge type="danger">Perlu Revisi</x-gentelella.badge>
                                @elseif($statusValue === 'terkirim')
                                    <x-gentelella.badge type="info">Terkirim</x-gentelella.badge>
                                @elseif($statusValue === 'disetujui')
                                    <x-gentelella.badge type="success">Disetujui</x-gentelella.badge>
                                @else
                                    <x-gentelella.badge type="secondary">{{ ucfirst($statusValue) }}</x-gentelella.badge>
                                @endif

                                <button type="button" class="btn-logbook-edit"
                                        onclick="openEditModal({{ $log->id }}, {{ $mingguKeTampil }}, '{{ $log->tanggal->toDateString() }}', {{ json_encode($log->kegiatan) }}, '{{ $log->link_laporan ?? '' }}', '{{ $log->file_laporan ? basename($log->file_laporan) : '' }}')">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                    <span>Edit</span>
                                </button>

                                <button type="button" class="btn-chevron-toggle" onclick="toggleCard({{ $log->id }})" aria-label="Buka tutup rincian">
                                    <svg class="chevron-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Konten Detail Accordion --}}
                        <div class="weekly-card-content" id="cardContent-{{ $log->id }}">
                            <div class="weekly-body">{{ $log->kegiatan }}</div>

                            @php
                                $catatanFeedback = $log->catatan_pembimbing ?? $log->catatan_review;
                            @endphp
                            @if($catatanFeedback)
                                <div style="margin-top: 14px;">
                                    <x-gentelella.callout type="warning" title="Catatan Review Pembimbing Lapangan">
                                        {{ $catatanFeedback }}
                                    </x-gentelella.callout>
                                </div>
                            @endif

                            @if($log->file_laporan || $log->link_laporan)
                                <div style="margin-top: 14px; padding-top: 12px; border-top: 1px dashed #E2E8F0; display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                    @if($log->file_laporan)
                                        <a href="{{ route('peserta.logbook.unduh', $log->id) }}" class="btn btn-secondary" style="height: 32px; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 6px;">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <span>Unduh Berkas PDF</span>
                                        </a>
                                    @endif

                                    @if($log->link_laporan)
                                        <a href="{{ $log->link_laporan }}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary" style="height: 32px; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 6px;">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                                            </svg>
                                            <span>Buka Tautan Lampiran ↗</span>
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            @if($logbooks->hasPages())
                <div style="margin-top: 24px;">
                    {{ $logbooks->links() }}
                </div>
            @endif
        @endif
    @endif
</div>

{{-- Modal Tambah Logbook Mingguan (Compact & Redesain Attachment Ala Tugas) --}}
<div id="modalTambahLogbook" class="modal-backdrop">
    <div class="modal-box-compact">
        <div class="modal-header-compact">
            <div class="modal-header-info">
                <div class="modal-icon-circle">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="modal-title-text">Isi Logbook Mingguan</h3>
                    <p class="modal-subtitle-text">Laporkan capaian dan aktivitas magang mingguan Anda</p>
                </div>
            </div>
            <button type="button" onclick="closeTambahModal()" class="modal-close-btn" title="Tutup">&times;</button>
        </div>

        <form id="formTambahLogbook" action="{{ route('peserta.logbook.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body-compact">

                {{-- 2-Column Grid: Minggu Magang & Tanggal Pelaporan --}}
                <div class="logbook-modal-grid-2">
                    <div class="form-group-compact">
                        <label for="tambah_minggu_ke" class="form-label-compact">
                            <span>Minggu Magang <span style="color: #DC2626;">*</span></span>
                            <span id="labelRentangMinggu" style="font-size: 0.6875rem; color: #01347C; font-weight: 700;"></span>
                        </label>
                        <select id="tambah_minggu_ke" name="minggu_ke" class="form-control-compact" required onchange="onMingguChange(this.value)">
                            <option value="">-- Pilih Minggu --</option>
                            @foreach($daftarMinggu as $m)
                                <option value="{{ $m['minggu_ke'] }}" 
                                        data-periode="{{ $m['label_periode'] }}"
                                        data-terisi="{{ $m['logbook'] ? '1' : '0' }}"
                                        {{ $m['logbook'] ? 'disabled' : '' }}>
                                    Minggu ke-{{ $m['minggu_ke'] }} ({{ $m['label_periode'] }}) {{ $m['logbook'] ? '— Sudah Diisi' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group-compact">
                        <label for="tambah_tanggal" class="form-label-compact">
                            <span>Tanggal Pelaporan <span style="color: #DC2626;">*</span></span>
                            <span style="font-size: 0.6875rem; color: #64748B;">Maks. hari ini</span>
                        </label>
                        <input type="date" id="tambah_tanggal" name="tanggal"
                               class="form-control-compact"
                               value="{{ old('tanggal', now()->toDateString()) }}"
                               min="{{ now()->subDays($logbookMaksMundur)->toDateString() }}"
                               max="{{ now()->toDateString() }}"
                               required>
                    </div>
                </div>

                {{-- Alert Jika Minggu Sudah Pernah Diisi --}}
                <div id="alertMingguSudahDiisi" style="display: none; padding: 12px 14px; background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 8px;">
                    <strong style="font-size: 0.8125rem; color: #1E3A8A; display: block;">Minggu Ini Sudah Pernah Dilaporkan</strong>
                    <span style="font-size: 0.75rem; color: #1E40AF;">Untuk mengubah data laporan, gunakan tombol <strong>Edit</strong> pada kartu logbook terkait di daftar bawah.</span>
                </div>

                <div id="formFieldsLogbook">
                    {{-- Ringkasan Kegiatan & Capaian --}}
                    <div class="form-group-compact">
                        <label for="tambah_kegiatan" class="form-label-compact">
                            <span>Ringkasan Kegiatan & Capaian <span style="color: #DC2626;">*</span></span>
                            <span style="font-size: 0.6875rem; color: #64748B;">Min. 10 karakter</span>
                        </label>
                        <textarea id="tambah_kegiatan" name="kegiatan" rows="4" class="form-control-compact"
                                  placeholder="Tuliskan aktivitas utama yang diselesaikan, kendala, dan hasil pekerjaan pekan ini..."
                                  required oninput="updateCharCount(this, 'tambah_char_count')">{{ old('kegiatan') }}</textarea>
                        <div id="tambah_char_count" class="char-counter-compact">0 karakter (minimal 10 karakter)</div>
                    </div>

                    {{-- Upload / Lampiran Persis Tugas Classroom (Tambah) --}}
                    <div class="logbook-attachment-section">
                        <label class="form-label-compact">
                            <span>Lampiran Berkas / Tautan (Opsional)</span>
                            <span style="font-size: 0.6875rem; color: #64748B;">PDF (maks. 2MB) atau Link HTTPS</span>
                        </label>

                        <input type="file" id="tambah_file_laporan" name="file_laporan" accept=".pdf" style="display: none;">
                        <input type="hidden" id="tambah_link_laporan" name="link_laporan" value="{{ old('link_laporan') }}">

                        {{-- Daftar Lampiran Terpilih --}}
                        <div id="tambahAttachmentsList" class="attached-items-list"></div>

                        {{-- Tombol Dropdown "+ Tambah berkas" (Outlined Pill) --}}
                        <div class="btn-dropdown-wrapper">
                            <button type="button" class="btn-add-attachment" id="btnTambahToggleDropdown">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <span>+ Tambah berkas</span>
                            </button>

                            {{-- Menu Dropdown Melayang (2 Opsi: Link & File) --}}
                            <div class="classroom-dropdown-menu" id="menuTambahDropdown">
                                <button type="button" class="dropdown-item-action" data-action="open-logbook-link" data-context="tambah">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                    </svg>
                                    <span>Link</span>
                                </button>
                                <button type="button" class="dropdown-item-action" data-action="open-logbook-file" data-context="tambah">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                    </svg>
                                    <span>File</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="modal-footer-compact">
                <button type="button" onclick="closeTambahModal()" class="btn btn-secondary">Batal</button>
                <button type="submit" id="btnSubmitTambah" class="btn btn-primary" style="font-weight: 700;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Kirim Logbook Mingguan</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit Logbook Mingguan (Compact) --}}
<div id="modalEditLogbook" class="modal-backdrop">
    <div class="modal-box-compact">
        <div class="modal-header-compact">
            <div class="modal-header-info">
                <div class="modal-icon-circle">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="modal-title-text">Perbarui Logbook Mingguan</h3>
                    <p id="modalEditSubtitle" class="modal-subtitle-text">Memperbarui rincian logbook</p>
                </div>
            </div>
            <button type="button" onclick="closeEditModal()" class="modal-close-btn" title="Tutup">&times;</button>
        </div>

        <form id="formEditLogbook" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <input type="hidden" id="edit_minggu_ke" name="minggu_ke">
            <input type="hidden" id="edit_tanggal" name="tanggal">

            <div class="modal-body-compact">
                <div class="form-group-compact">
                    <label for="edit_kegiatan" class="form-label-compact">
                        <span>Ringkasan Kegiatan & Capaian <span style="color: #DC2626;">*</span></span>
                        <span style="font-size: 0.6875rem; color: #64748B;">Min. 10 karakter</span>
                    </label>
                    <textarea id="edit_kegiatan" name="kegiatan" rows="4" class="form-control-compact" required oninput="updateCharCount(this, 'edit_char_count')"></textarea>
                    <div id="edit_char_count" class="char-counter-compact">0 karakter</div>
                </div>

                {{-- Upload / Lampiran Persis Tugas Classroom (Edit) --}}
                <div class="logbook-attachment-section">
                    <label class="form-label-compact">
                        <span>Lampiran Berkas / Tautan</span>
                        <span style="font-size: 0.6875rem; color: #64748B;">PDF (maks. 2MB) atau Link HTTPS</span>
                    </label>

                    <input type="file" id="edit_file_laporan" name="file_laporan" accept=".pdf" style="display: none;">
                    <input type="hidden" id="edit_link_laporan" name="link_laporan">

                    {{-- Daftar Lampiran Terpilih / Existing --}}
                    <div id="editAttachmentsList" class="attached-items-list"></div>

                    {{-- Tombol Dropdown "+ Tambah berkas" --}}
                    <div class="btn-dropdown-wrapper">
                        <button type="button" class="btn-add-attachment" id="btnEditToggleDropdown">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span>+ Tambah berkas</span>
                        </button>
                        <div class="classroom-dropdown-menu" id="menuEditDropdown">
                            <button type="button" class="dropdown-item-action" data-action="open-logbook-link" data-context="edit">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                </svg>
                                <span>Link</span>
                            </button>
                            <button type="button" class="dropdown-item-action" data-action="open-logbook-file" data-context="edit">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                </svg>
                                <span>File</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer-compact">
                <button type="button" onclick="closeEditModal()" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary" style="font-weight: 700;">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- =================================================================
     MODAL SUB-DIALOG 1: ADD LINK (GOOGLE CLASSROOM STYLE)
     ================================================================= --}}
<div class="classroom-modal-backdrop" id="modalLogbookLink" role="dialog" aria-modal="true" aria-labelledby="modalLogbookLinkTitle">
    <div class="classroom-modal-dialog modal-link-size">
        <div class="classroom-modal-header">
            <h3 class="classroom-modal-title" id="modalLogbookLinkTitle">Add link</h3>
            <button type="button" class="classroom-modal-close" data-action="close-logbook-submodal" aria-label="Tutup modal">&times;</button>
        </div>
        <div class="classroom-modal-body">
            <div style="margin-bottom: 8px;">
                <label for="inputLogbookUrl" style="display: block; font-size: 0.8125rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                    Link <span style="color: #DC2626;">*</span>
                </label>
                <input type="url" id="inputLogbookUrl" class="form-control" placeholder="https://..." style="width: 100%;">
                <span style="font-size: 0.75rem; color: #DC2626; display: block; margin-top: 4px;">*Required (gunakan format https://...)</span>
            </div>
        </div>
        <div class="classroom-modal-footer">
            <button type="button" class="btn btn-secondary" data-action="close-logbook-submodal" style="padding: 7px 18px; border-radius: 6px; font-weight: 600;">Cancel</button>
            <button type="button" id="btnSubmitLogbookLink" class="btn btn-primary" style="padding: 7px 20px; border-radius: 6px; font-weight: 600; background: #2563EB; border: none;" disabled>Add link</button>
        </div>
    </div>
</div>

{{-- =================================================================
     MODAL SUB-DIALOG 2: INSERT FILES (DROPZONE & BROWSE LOKAL)
     ================================================================= --}}
<div class="classroom-modal-backdrop" id="modalLogbookFile" role="dialog" aria-modal="true" aria-labelledby="modalLogbookFileTitle">
    <div class="classroom-modal-dialog modal-file-size" style="max-height: 480px;">
        <div class="classroom-modal-header">
            <h3 class="classroom-modal-title" id="modalLogbookFileTitle">Insert files</h3>
            <button type="button" class="classroom-modal-close" data-action="close-logbook-submodal" aria-label="Tutup modal">&times;</button>
        </div>

        <div class="classroom-modal-tabs">
            <button type="button" class="classroom-modal-tab-btn active">Upload</button>
        </div>

        <div class="classroom-tab-panel active">
            <input type="file" id="submodalLogbookFileInput" accept=".pdf" style="display: none;">
            <div class="file-dropzone-box" id="logbookDropzoneBox">
                <div class="cloud-upload-illustration">
                    <svg width="68" height="68" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                </div>
                <button type="button" class="btn-browse-file" id="btnBrowseLogbookFile">Browse</button>
                <div class="dropzone-label-text">or drag files here to upload</div>
                <div class="dropzone-sub-text">Maksimal 2MB (Format Berkas PDF)</div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    @vite(['resources/js/pages/peserta/logbook-index.js'])
@endpush
@endsection
