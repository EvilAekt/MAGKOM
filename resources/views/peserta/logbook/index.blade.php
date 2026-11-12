@extends('layouts.peserta')

@section('title', 'Logbook & Jurnal Mingguan Peserta')
@section('page_title', 'Logbook Mingguan')

@push('styles')
    @vite(['resources/css/pages/peserta/logbook-index.css'])
@endpush

@section('content')
<div class="logbook-wrapper">
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
                <h1 class="banner-title">{{ $penempatan->bidang->nama_bidang ?? 'Dinas Komunikasi dan Informatika Sleman' }}</h1>
                <div class="banner-info-stack">
                    <div class="banner-info-box">
                        <span class="banner-info-label">PEMBIMBING</span>
                        <span class="banner-info-val">{{ $penempatan->pembimbing->nama ?? $penempatan->pembimbing->name ?? 'Pembimbing Lapangan' }}</span>
                    </div>
                    <div class="banner-info-box">
                        <span class="banner-info-label">PERIODE</span>
                        <span class="banner-info-val">{{ \Carbon\Carbon::parse($penempatan->tgl_mulai)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($penempatan->tgl_selesai)->translatedFormat('d M Y') }}</span>
                    </div>
                </div>
            </div>

            <div class="banner-gold-widget">
                <div class="gold-widget-label">MINGGU BERJALAN SAAT INI</div>
                <div class="gold-widget-title">Minggu ke-{{ $mingguAktif }}</div>
                <div class="gold-widget-sub">dari total {{ $totalMinggu }} minggu</div>
            </div>
        </div>

        {{-- Weekly Progress Tracker Card --}}
        <div class="progress-tracker-card">
            <div class="progress-tracker-header">
                <h2 class="progress-tracker-title">Progres Pelaporan Logbook Mingguan</h2>
                <div class="tracker-pill-badge">
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
                        $isPastUnfilled = $m['minggu_ke'] < $mingguAktif && !$hasLog;
                        $isUpcoming = $m['minggu_ke'] > $mingguAktif;

                        if ($hasLog) {
                            $chipClass = 'is-done';
                            $chipIcon = '✓';
                        } elseif ($isCurrent) {
                            $chipClass = 'is-active-week';
                            $chipIcon = '⏳';
                        } elseif ($isPastUnfilled) {
                            $chipClass = 'is-unfilled';
                            $chipIcon = '📅';
                        } else {
                            $chipClass = 'is-upcoming';
                            $chipIcon = '📅';
                        }
                    @endphp
                    @if($hasLog)
                        <a href="#logbook-card-{{ $m['logbook']->id }}"
                           class="week-chip {{ $chipClass }}"
                           title="Minggu {{ $m['minggu_ke'] }}: {{ $m['label_periode'] }} (Sudah diisi)">
                            <span class="chip-icon">{{ $chipIcon }}</span>
                            <span>Minggu {{ $m['minggu_ke'] }}</span>
                        </a>
                    @elseif($isPastUnfilled || $isCurrent)
                        <button type="button"
                                class="week-chip {{ $chipClass }}"
                                onclick="openTambahModal({{ $m['minggu_ke'] }})"
                                title="Minggu {{ $m['minggu_ke'] }}: {{ $m['label_periode'] }} (Klik untuk isi)">
                            <span class="chip-icon">{{ $chipIcon }}</span>
                            <span>Minggu {{ $m['minggu_ke'] }}</span>
                        </button>
                    @else
                        <span class="week-chip {{ $chipClass }}"
                              aria-disabled="true"
                              title="Minggu {{ $m['minggu_ke'] }}: {{ $m['label_periode'] }}">
                            <span class="chip-icon">{{ $chipIcon }}</span>
                            <span>Minggu {{ $m['minggu_ke'] }}</span>
                        </span>
                    @endif
                @endforeach
            </div>

            <div class="stepper-legend-row">
                <div class="legend-item">
                    <span class="legend-dot dot-done"></span>
                    <span>Sudah diisi</span>
                </div>
                <div class="legend-item">
                    <span class="legend-dot dot-unfilled"></span>
                    <span>Belum diisi</span>
                </div>
                <div class="legend-item">
                    <span class="legend-dot dot-active"></span>
                    <span>Minggu berjalan</span>
                </div>
                <div class="legend-item">
                    <span class="legend-dot dot-upcoming"></span>
                    <span>Mendatang</span>
                </div>
            </div>
        </div>

        {{-- Card Riwayat Jurnal Mingguan --}}
        <div class="riwayat-jurnal-card" id="riwayatJurnal">
            <div class="riwayat-header-row">
                <h2 class="riwayat-title">Riwayat Jurnal Mingguan</h2>

                <div class="riwayat-actions-right">
                    <span class="total-laporan-pill">
                        Total: {{ $logbooks->total() }} Laporan
                    </span>
                    <button type="button" class="btn-tambah-logbook-gold" onclick="openTambahModal()">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                        </svg>
                        + Isi Logbook Mingguan
                    </button>
                </div>
            </div>

            {{-- Integrated Search Bar --}}
            @if($logbooks->isNotEmpty())
                <div class="logbook-search-bar">
                    <div class="search-input-wrapper">
                        <svg class="search-icon" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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
            @endif

            {{-- Daftar Kartu Logbook Mingguan --}}
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
                    <button type="button" class="btn-tambah-logbook-gold" onclick="openTambahModal({{ $mingguAktif }})">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
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
                                    <div class="week-number-squircle {{ $isRevisi ? 'is-revisi' : '' }}">
                                        <span class="squircle-label">MG</span>
                                        <span class="squircle-number">{{ $mingguKeTampil }}</span>
                                    </div>
                                    <div class="weekly-info-col">
                                        <div class="weekly-card-title">Logbook Minggu ke-{{ $mingguKeTampil }}</div>
                                        <div class="weekly-card-meta">
                                            <span>Periode: <strong>{{ $labelPeriodeTampil }}</strong></span>
                                            <span class="meta-separator">•</span>
                                            <span>Dilaporkan: {{ $log->tanggal->translatedFormat('d M Y') }}</span>
                                        </div>
                                        <div class="weekly-collapsed-preview">{{ Str::limit($log->kegiatan, 110) }}</div>
                                    </div>
                                </div>

                                <div class="weekly-actions-right" onclick="event.stopPropagation();">
                                    @if($isRevisi)
                                        <span class="status-capsule capsule-revisi">Perlu Revisi</span>
                                    @elseif($statusValue === 'terkirim')
                                        <span class="status-capsule capsule-terkirim">✓ Terkirim</span>
                                    @elseif($statusValue === 'disetujui')
                                        <span class="status-capsule capsule-disetujui">✓ Disetujui</span>
                                    @else
                                        <span class="status-capsule capsule-default">{{ ucfirst($statusValue) }}</span>
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
        </div>
    @endif
</div>

{{-- Modal Tambah Logbook Mingguan (Sesuai Referensi Pendaftaran Magang) --}}
<div id="modalTambahLogbook" class="modal-backdrop" onclick="if(event.target === this) closeTambahModal()">
    <div class="modal-card-logbook modal-box-compact">
        <div class="modal-header-logbook modal-header-compact">
            <div class="modal-header-main modal-header-info">
                <div class="modal-icon-badge modal-icon-circle" aria-hidden="true">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="modal-title-logbook modal-title-text">Isi Logbook Mingguan</h3>
                </div>
            </div>
            <button type="button" onclick="closeTambahModal()" class="modal-close-logbook modal-close-btn" aria-label="Tutup Formulir" title="Tutup">&times;</button>
        </div>

        <form id="formTambahLogbook" class="modal-form-logbook" action="{{ route('peserta.logbook.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body-logbook modal-body-compact">

                {{-- 2-Column Grid: Minggu Magang & Tanggal Pelaporan --}}
                <div class="logbook-modal-grid-2">
                    <div class="form-group-logbook form-group-compact">
                        <label for="tambah_minggu_ke" class="label-logbook form-label-compact">
                            <span>Minggu Magang <span class="req">*</span></span>
                        </label>
                        <select id="tambah_minggu_ke" name="minggu_ke" class="select-logbook form-control-compact" required onchange="onMingguChange(this.value)">
                            <option value="">-- Pilih Minggu --</option>
                            @foreach($daftarMinggu as $m)
                                @php
                                    $isSudahDiisi = $m['logbook'] !== null;
                                    $isMendatang = $m['minggu_ke'] > $mingguAktif;
                                    $isDisabled = $isSudahDiisi || $isMendatang;
                                    $statusLabel = '';
                                    if ($isSudahDiisi) {
                                        $statusLabel = ' (Sudah Diisi)';
                                    } elseif ($isMendatang) {
                                        $statusLabel = ' (Belum Dibuka)';
                                    }
                                @endphp
                                <option value="{{ $m['minggu_ke'] }}" 
                                        data-periode="{{ $m['label_periode'] }}"
                                        data-terisi="{{ $isSudahDiisi ? '1' : '0' }}"
                                        data-mendatang="{{ $isMendatang ? '1' : '0' }}"
                                        data-tgl-awal="{{ $m['tgl_awal']->toDateString() }}"
                                        data-tgl-akhir="{{ $m['tgl_akhir']->toDateString() }}"
                                        {{ $isDisabled ? 'disabled' : '' }}>
                                    Minggu ke-{{ $m['minggu_ke'] }} ({{ $m['label_periode'] }}){{ $statusLabel }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group-logbook form-group-compact">
                        <label for="tambah_tanggal" class="label-logbook form-label-compact">
                            <span>Tanggal Pelaporan <span class="req">*</span></span>
                        </label>
                        <input type="date" id="tambah_tanggal" name="tanggal"
                               class="input-logbook form-control-compact"
                               value="{{ old('tanggal', now()->toDateString()) }}"
                               min="{{ \Carbon\Carbon::parse($penempatan->tgl_mulai)->toDateString() }}"
                               max="{{ now()->toDateString() }}"
                               required>
                    </div>
                </div>

                {{-- Alert Jika Minggu Sudah Pernah Diisi --}}
                <div id="alertMingguSudahDiisi" class="alert-logbook-notice is-warning" style="display: none;">
                    <strong class="alert-notice-title text-blue">Minggu Ini Sudah Pernah Dilaporkan</strong>
                    <span class="alert-notice-desc text-blue-muted">Untuk mengubah data laporan, gunakan tombol <strong>Edit</strong> pada kartu logbook terkait di daftar bawah.</span>
                </div>

                {{-- Alert Jika Minggu Mendatang --}}
                <div id="alertMingguMendatang" class="alert-logbook-notice is-future" style="display: none;">
                    <strong class="alert-notice-title text-red">Minggu Belum Berjalan</strong>
                    <span class="alert-notice-desc text-red-muted">Logbook untuk minggu mendatang belum dapat diisi. Silakan pilih minggu yang sedang aktif atau minggu lampau yang belum diisi.</span>
                </div>

                <div id="formFieldsLogbook">
                    {{-- Ringkasan Kegiatan & Capaian --}}
                    <div class="form-group-logbook form-group-compact">
                        <label for="tambah_kegiatan" class="label-logbook form-label-compact">
                            <span>Ringkasan Kegiatan & Capaian <span class="req">*</span></span>
                            <span id="tambah_char_count" class="char-counter-tag char-counter-compact">0 karakter (minimal 10)</span>
                        </label>
                        <textarea id="tambah_kegiatan" name="kegiatan" rows="4" class="textarea-logbook form-control-compact"
                                  placeholder="Tuliskan aktivitas utama yang diselesaikan, kendala, dan hasil pekerjaan pekan ini..."
                                  required minlength="10"
                                  oninput="updateCharCount(this, 'tambah_char_count')"
                                  onchange="updateCharCount(this, 'tambah_char_count')">{{ old('kegiatan') }}</textarea>
                    </div>

                    {{-- Panel Lampiran Terpadu --}}
                    <div class="logbook-lampiran-section">
                        <div class="logbook-lampiran-header">
                            <label class="label-logbook form-label-compact" style="margin-bottom: 0;">
                                <span>Lampiran</span>
                            </label>
                            <span class="label-hint" id="tambahLampiranMeta">(opsional) · PDF (maks. 2MB) atau Link HTTPS</span>
                        </div>

                        <div class="logbook-lampiran-selector-box">
                            <div class="logbook-circle-action-item">
                                <button type="button" class="logbook-circle-btn" id="btnTambahTriggerUploadFile" aria-label="Upload File" title="Upload File">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 16V4M7 9l5-5 5 5M5 20h14"></path>
                                    </svg>
                                </button>
                                <span class="logbook-circle-label">Upload File</span>
                            </div>

                            <div class="logbook-circle-action-item">
                                <button type="button" class="logbook-circle-btn" id="btnTambahTriggerAddLink" aria-label="Link" title="Link">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M10 14a4 4 0 0 0 5.7 0l3-3a4 4 0 0 0-5.7-5.7l-1 1M14 10a4 4 0 0 0-5.7 0l-3 3A4 4 0 0 0 11 18.7l1-1"></path>
                                    </svg>
                                </button>
                                <span class="logbook-circle-label">Link</span>
                            </div>
                        </div>

                        <input type="file" id="tambah_file_laporan" name="file_laporan" accept=".pdf" class="logbook-native-file-input">
                        <input type="hidden" id="tambah_link_laporan" name="link_laporan" value="{{ old('link_laporan') }}">

                        <div class="logbook-attached-list" id="tambahAttachedFilesContainer"></div>
                        <div class="logbook-attached-list" id="tambahAttachedLinksContainer"></div>
                        <div id="tambahFileFeedbackError" class="logbook-field-error" style="display: none;"></div>
                        <div id="tambahLinkFeedbackError" class="logbook-field-error" style="display: none;"></div>
                    </div>
                </div>

            </div>

            <div class="modal-footer-logbook modal-footer-compact">
                <button type="button" onclick="closeTambahModal()" class="btn-modal-cancel">Batal</button>
                <button type="submit" id="btnSubmitTambah" class="btn-modal-submit">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Kirim Logbook Mingguan</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit Logbook Mingguan (Sesuai Referensi Pendaftaran Magang) --}}
<div id="modalEditLogbook" class="modal-backdrop" onclick="if(event.target === this) closeEditModal()">
    <div class="modal-card-logbook modal-box-compact">
        <div class="modal-header-logbook modal-header-compact">
            <div class="modal-header-main modal-header-info">
                <div class="modal-icon-badge modal-icon-circle" aria-hidden="true">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="modal-title-logbook modal-title-text">Perbarui Logbook Mingguan</h3>
                </div>
            </div>
            <button type="button" onclick="closeEditModal()" class="modal-close-logbook modal-close-btn" aria-label="Tutup Formulir" title="Tutup">&times;</button>
        </div>

        <form id="formEditLogbook" class="modal-form-logbook" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <input type="hidden" id="edit_minggu_ke" name="minggu_ke">
            <input type="hidden" id="edit_tanggal" name="tanggal">

            <div class="modal-body-logbook modal-body-compact">
                <div class="form-group-logbook form-group-compact">
                    <label for="edit_kegiatan" class="label-logbook form-label-compact">
                        <span>Ringkasan Kegiatan & Capaian <span class="req">*</span></span>
                        <span id="edit_char_count" class="char-counter-tag char-counter-compact">0 karakter (minimal 10)</span>
                    </label>
                    <textarea id="edit_kegiatan" name="kegiatan" rows="4" class="textarea-logbook form-control-compact" required minlength="10" oninput="updateCharCount(this, 'edit_char_count')"></textarea>
                </div>

                {{-- Panel Lampiran Terpadu --}}
                <div class="logbook-lampiran-section">
                    <div class="logbook-lampiran-header">
                        <label class="label-logbook form-label-compact" style="margin-bottom: 0;">
                            <span>Lampiran</span>
                        </label>
                        <span class="label-hint" id="editLampiranMeta">(opsional) · PDF (maks. 2MB) atau Link HTTPS</span>
                    </div>

                    <div class="logbook-lampiran-selector-box">
                        <div class="logbook-circle-action-item">
                            <button type="button" class="logbook-circle-btn" id="btnEditTriggerUploadFile" aria-label="Upload File" title="Upload File">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 16V4M7 9l5-5 5 5M5 20h14"></path>
                                </svg>
                            </button>
                            <span class="logbook-circle-label">Upload File</span>
                        </div>

                        <div class="logbook-circle-action-item">
                            <button type="button" class="logbook-circle-btn" id="btnEditTriggerAddLink" aria-label="Link" title="Link">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M10 14a4 4 0 0 0 5.7 0l3-3a4 4 0 0 0-5.7-5.7l-1 1M14 10a4 4 0 0 0-5.7 0l-3 3A4 4 0 0 0 11 18.7l1-1"></path>
                                </svg>
                            </button>
                            <span class="logbook-circle-label">Link</span>
                        </div>
                    </div>

                    <input type="file" id="edit_file_laporan" name="file_laporan" accept=".pdf" class="logbook-native-file-input">
                    <input type="hidden" id="edit_link_laporan" name="link_laporan">

                    <div class="logbook-attached-list" id="editAttachedFilesContainer"></div>
                    <div class="logbook-attached-list" id="editAttachedLinksContainer"></div>
                    <div id="editFileFeedbackError" class="logbook-field-error" style="display: none;"></div>
                    <div id="editLinkFeedbackError" class="logbook-field-error" style="display: none;"></div>
                </div>
            </div>

            <div class="modal-footer-logbook modal-footer-compact">
                <button type="button" onclick="closeEditModal()" class="btn-modal-cancel">Batal</button>
                <button type="submit" class="btn-modal-submit">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Dialog Pop-up Unggah Berkas PDF (Sub-modal ala Pembimbing) --}}
<div class="tugas-modal-backdrop" id="modalLogbookUploadFileDialog" role="dialog" aria-modal="true" aria-labelledby="ttlModalUploadFile">
    <div class="tugas-dialog tugas-dialog--upload">
        <div style="font-size: 16px; font-weight: 600; color: #111827;" id="ttlModalUploadFile">Upload File</div>

        <div class="tugas-dropzone" id="logbookDropzoneArea" tabindex="0" role="button" aria-label="Area drag and drop upload berkas PDF">
            <svg width="84" height="62" viewBox="0 0 150 110" aria-hidden="true" class="tugas-dropzone-cloud">
                <defs>
                    <linearGradient id="cloudGradLogbook" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0" stop-color="#4cc760"></stop>
                        <stop offset="1" stop-color="#b8e3fb"></stop>
                    </linearGradient>
                </defs>
                <path d="M38 100a30 30 0 0 1-2-60 38 38 0 0 1 72-8 28 28 0 0 1 8 56z" fill="url(#cloudGradLogbook)" stroke="#222" stroke-width="1.5"></path>
                <path d="M75 82V52M62 64l13-13 13 13" fill="none" stroke="#222" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
            <button type="button" class="tugas-dropzone-btn-choose" id="btnLogbookBrowseFile">Pilih File</button>
            <div class="tugas-dropzone-hint">atau tarik file ke sini</div>
            <div class="tugas-dropzone-format">PDF · maks. 2MB</div>
        </div>

        <input type="file" id="modalStagedFileInput" accept=".pdf" style="display: none;">

        {{-- Preview Berkas Terpilih di dalam Modal --}}
        <div id="submodalFileInfo" style="display: none; margin-top: 14px; padding: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 10px; min-width: 0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                <div style="min-width: 0;">
                    <div id="submodalFileName" style="font-size: 13px; font-weight: 600; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"></div>
                    <div id="submodalFileSize" style="font-size: 12px; color: #64748b;"></div>
                </div>
            </div>
            <button type="button" id="btnSubmodalRemoveFile" style="background: none; border: none; cursor: pointer; color: #ef4444;" title="Hapus berkas">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
            </button>
        </div>

        <div id="submodalFileError" class="logbook-field-error" style="display: none; margin-top: 8px;"></div>

        <div style="margin-top: 24px; display: flex; justify-content: flex-end; gap: 10px;">
            <button type="button" class="tugas-dialog-btn-text" id="btnCancelSubmodalFile" data-action="close-submodal">Batal</button>
            <button type="button" class="tugas-dialog-btn-pill" id="btnSubmodalConfirmFile" disabled>Upload</button>
        </div>
    </div>
</div>

{{-- Dialog Pop-up Tambahkan Link (Sub-modal ala Pembimbing) --}}
<div class="tugas-modal-backdrop" id="modalLogbookAddLinkDialog" role="dialog" aria-modal="true" aria-labelledby="ttlModalAddLink">
    <div class="tugas-dialog tugas-dialog--link">
        <div style="font-size: 16px; font-weight: 600; color: #111827;" id="ttlModalAddLink">Tambahkan link</div>

        <div style="margin-top: 18px;">
            <div class="tugas-field-filled" id="submodalLinkFieldFilled" style="padding: 8px 14px;">
                <label for="inputSubmodalLinkUrl" class="tugas-field-filled-label">Link*</label>
                <input type="url" id="inputSubmodalLinkUrl" placeholder="https://drive.google.com/..." class="tugas-input-clean" style="font-size: 15px;">
            </div>
            <div id="submodalLinkError" style="margin: 6px 0 0 8px; font-size: 12px; color: #b3261e; display: none;">*Wajib diisi</div>
        </div>

        <div style="margin-top: 24px; display: flex; justify-content: flex-end; gap: 10px;">
            <button type="button" class="tugas-dialog-btn-text" id="btnCancelSubmodalLink" data-action="close-submodal">Batal</button>
            <button type="button" class="tugas-dialog-btn-pill" id="btnSubmodalConfirmLink" disabled>Tambahkan link</button>
        </div>
    </div>
</div>

@push('scripts')
    @vite(['resources/js/pages/peserta/logbook-index.js'])
@endpush
@endsection
