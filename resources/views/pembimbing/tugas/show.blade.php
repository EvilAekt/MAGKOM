@extends('layouts.dashboard')

@section('title', 'Review Tugas: ' . $tugas->judul . ' - Portal MagKom')
@section('topbar_title', 'Review & Evaluasi Penugasan')
@section('content_area_class', 'gentelella-fluid-canvas')

@push('styles')
    @vite(['resources/css/pages/pembimbing/tugas-review.css'])
@endpush

@section('content')
@php
    $peserta = $tugas->penempatan?->peserta;
    $profil = $peserta?->profil;
    $menteeNama = $peserta?->nama ?? 'Peserta Magang';
    $institusi = $profil?->institusi ?? 'Institusi Belum Dilengkapi';
    $jurusan = $profil?->jurusan?->nama_jurusan ?? '';
    $initials = collect(explode(' ', $menteeNama))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');

    $isOverdue = $tugas->deadline && $tugas->deadline->isPast() && ($tugas->status->value ?? (string)$tugas->status) !== 'selesai';
    $daysLeft = $tugas->deadline ? \Carbon\Carbon::today()->diffInDays($tugas->deadline, false) : 99;
    $isDueSoon = !$isOverdue && $daysLeft >= 0 && $daysLeft <= 3 && ($tugas->status->value ?? (string)$tugas->status) !== 'selesai';

    $allLogs = $tugas->progresLogs->sortByDesc('created_at');
    $latestLog = $allLogs->first();
    $latestReviewVal = $latestLog?->status_review?->value ?? (string)$latestLog?->status_review;
    $isTugasSelesai = ($tugas->status->value ?? (string)$tugas->status) === 'selesai';

    $hasLink = !empty($latestLog?->link);
    $hasFile = !empty($latestLog?->file);
@endphp

<div class="review-workspace">

    {{-- 1. Topbar Navigation Ala Google Classroom Review Canvas --}}
    <header class="review-topbar">
        <div class="review-topbar-left">
            {{-- Mentee Selector & Navigation Panah (< dan >) --}}
            <div class="review-mentee-nav">
                @if(isset($siblingTugasList) && $siblingTugasList->count() > 1)
                    <div class="review-dropdown-wrapper">
                        <button type="button" 
                                id="btnMenteeDropdown" 
                                class="review-mentee-picker" 
                                aria-haspopup="true" 
                                aria-expanded="false" 
                                title="Pilih penugasan / mentee untuk direview">
                            <span class="review-mentee-avatar" aria-hidden="true">{{ $initials }}</span>
                            <span class="review-mentee-picker-name">{{ Str::limit($menteeNama, 20) }}</span>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </button>
                        <div id="menteeDropdownMenu" class="review-dropdown-menu">
                            <div class="review-dropdown-header">
                                <span>Pilih Tugas / Peserta Bimbingan</span>
                            </div>
                            <div class="review-dropdown-scroll">
                                @foreach($siblingTugasList as $st)
                                    @php
                                        $stNama = $st->penempatan?->peserta?->nama ?? 'Mentee';
                                        $stInitials = collect(explode(' ', $stNama))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
                                        $isCurrent = $st->id === $tugas->id;
                                        $isBatch = !empty($tugas->batch_id);
                                    @endphp
                                    <a class="review-dropdown-item {{ $isCurrent ? 'active' : '' }}" href="{{ route('pembimbing.tugas.show', $st->id) }}">
                                        <span class="review-dropdown-item-avatar">{{ $stInitials }}</span>
                                        <div class="review-dropdown-item-text">
                                            @if($isBatch)
                                                <strong class="review-dropdown-item-name">{{ $stNama }}</strong>
                                                <small class="review-dropdown-item-inst">{{ Str::limit($st->penempatan?->peserta?->profil?->institusi ?? '', 26) }}</small>
                                            @else
                                                <strong class="review-dropdown-item-name">{{ Str::limit($st->judul, 26) }}</strong>
                                                <small class="review-dropdown-item-inst">{{ $stNama }} &bull; {{ $st->status->label() ?? $st->status }}</small>
                                            @endif
                                        </div>
                                        @if($isCurrent)
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--review-primary, #0641CC)" stroke-width="2.5" class="review-dropdown-item-check"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <div class="review-mentee-picker">
                        <span class="review-mentee-avatar" aria-hidden="true">{{ $initials }}</span>
                        <span class="review-mentee-picker-name">{{ $menteeNama }}</span>
                    </div>
                @endif

                {{-- Navigasi Mentee Sebelumnya --}}
                <a href="{{ isset($prevTugas) && $prevTugas ? route('pembimbing.tugas.show', $prevTugas->id) : '#' }}" 
                   id="prevMenteeLink" 
                   class="review-nav-arrow {{ !isset($prevTugas) || !$prevTugas ? 'disabled' : '' }}" 
                   title="Tugas peserta sebelumnya (Alt + ←)"
                   aria-label="Tugas peserta sebelumnya">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </a>

                {{-- Navigasi Mentee Selanjutnya --}}
                <a href="{{ isset($nextTugas) && $nextTugas ? route('pembimbing.tugas.show', $nextTugas->id) : '#' }}" 
                   id="nextMenteeLink" 
                   class="review-nav-arrow {{ !isset($nextTugas) || !$nextTugas ? 'disabled' : '' }}" 
                   title="Tugas peserta selanjutnya (Alt + →)"
                   aria-label="Tugas peserta selanjutnya">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </a>
            </div>

            {{-- Chip Tautan / Berkas Kiriman Peserta --}}
            @if($hasLink)
                <a href="{{ $latestLog->link }}" target="_blank" rel="noopener noreferrer" class="review-sub-chip" title="Buka tautan tugas yang diserahkan">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                    <span>Link Tugas ↗</span>
                </a>
            @elseif($hasFile)
                <a href="{{ route('berkas.download', ['dokumen' => $latestLog->file]) }}" class="review-sub-chip" title="Unduh berkas lampiran yang diserahkan">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    <span>Berkas Tugas ⬇</span>
                </a>
            @endif
        </div>

        <div class="review-topbar-right">
            {{-- Indikator Status Evaluasi Progres / Tugas --}}
            @if($isTugasSelesai)
                <span class="review-status-badge selesai">Selesai</span>
            @elseif($latestReviewVal === 'diterima')
                <span class="review-status-badge proses">Progres Diterima</span>
            @elseif($allLogs->isNotEmpty() && $latestReviewVal !== 'perlu_revisi')
                <span class="review-status-badge proses">Menunggu Review</span>
            @elseif($allLogs->isEmpty())
                <span class="review-status-badge belum">Belum Ada Kiriman</span>
            @endif

            {{-- Tombol Edit Tugas (Jika belum selesai) --}}
            @if(!$isTugasSelesai && auth()->user()->role !== \App\Enums\RoleEnum::SuperAdmin)
                <a href="{{ route('pembimbing.tugas.edit', $tugas->id) }}" class="review-btn-action btn-edit" title="Ubah instruksi atau tenggat tugas">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    <span>Edit Tugas</span>
                </a>
            @endif

            {{-- Tombol Tandai Selesai Split Button (Dengan Sub-Tombol Kembalikan Tugas) --}}
            @if(!$isTugasSelesai && auth()->user()->role !== \App\Enums\RoleEnum::SuperAdmin)
                {{-- Form Konfirmasi Tandai Selesai --}}
                <form action="{{ route('pembimbing.tugas.selesai', $tugas->id) }}" method="POST" id="formTandaiSelesai">
                    @csrf
                </form>

                <div class="review-split-btn" id="reviewSplitBtn">
                    <button type="button" class="review-btn-primary-action" id="btnSubmitSelesaiDirect" title="Konfirmasi tugas ini telah selesai tuntas">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Tandai Selesai</span>
                    </button>
                    <button type="button" class="review-btn-caret" id="btnToggleReviewSplitMenu" aria-label="Opsi evaluasi penugasan" aria-haspopup="true" aria-expanded="false" title="Pilihan aksi evaluasi">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M6 9l6 6 6-6"></path>
                        </svg>
                    </button>

                    {{-- Dropdown Menu Opsi Evaluasi (Hanya Sub-Tombol Kembalikan Tugas) --}}
                    <div class="review-split-menu" id="reviewSplitMenu" role="menu">
                        <button type="button" class="review-menu-item review-menu-item--danger" id="menuOptKembalikanTugas" role="menuitem" {{ !$latestLog ? 'disabled' : '' }} title="{{ !$latestLog ? 'Menunggu kiriman peserta' : 'Kembalikan tugas dengan catatan umpan balik' }}">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
                            <span>Kembalikan Tugas</span>
                        </button>
                    </div>
                </div>
            @endif

            {{-- Tombol Batal / Kembali ke Daftar Tugas (Di sebelah kanan Tandai Selesai) --}}
            <a href="{{ route('pembimbing.tugas.index') }}" class="review-btn-cancel" title="Batal dan kembali ke daftar tugas">
                Batal
            </a>
        </div>
    </header>

    {{-- Alert Notifikasi Session --}}
    @if(session('success'))
        <div style="background: #EFF6FF; border: 1px solid #BFDBFE; color: #1E40AF; padding: 12px 18px; margin: 12px 1.5rem 0 1.5rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600;">
            ✓ {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div style="background: #FEF2F2; border: 1px solid #FECACA; color: #991B1B; padding: 12px 18px; margin: 12px 1.5rem 0 1.5rem; border-radius: 8px; font-size: 0.875rem;">
            <strong>Harap periksa kembali:</strong>
            <ul style="margin: 4px 0 0 18px; padding: 0;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- 2. Layout Tubuh: Canvas Terpadu Kiri & Sidebar Evaluasi Kanan --}}
    <div class="review-body-layout">

        {{-- Floating Restore Tab on Right Edge when Sidebar is Collapsed (Icon-Only) --}}
        <button type="button" 
                id="btnRestoreSidebar" 
                class="btn-restore-sidebar" 
                title="Buka panel evaluasi ({{ $menteeNama }})" 
                aria-label="Buka panel evaluasi">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="11 17 6 12 11 7"></polyline>
                <polyline points="18 17 13 12 18 7"></polyline>
            </svg>
        </button>

        {{-- Area Kanvas Utama (Kiri / Tengah): Rincian Tugas & Kiriman Peserta Terintegrasi --}}
        <main class="review-main-canvas">

            {{-- 1. Bagian Rincian & Instruksi Tugas --}}
            <section class="instruction-card review-integrated-section">
                <div class="section-top-badge-row">
                    <span class="section-label-badge">Rincian Penugasan</span>
                </div>

                <h2 class="instruction-task-title" style="margin-top: 0.75rem;">{{ $tugas->judul }}</h2>

                <div class="instruction-text-content">
                    {!! nl2br(e($tugas->deskripsi)) ?: '<em>Tidak ada instruksi khusus yang disertakan.</em>' !!}
                </div>

                {{-- Materi / Sumber Daya Acuan dari Pembimbing --}}
                @if($tugas->file_lampiran || $tugas->link_referensi)
                    <div style="border-top: 1px solid #E2E8F0; padding-top: 1.25rem; margin-top: 1.25rem;">
                        <h4 style="font-size: 0.8125rem; font-weight: 700; color: #475569; text-transform: uppercase; margin: 0 0 10px 0;">
                            Materi & Dokumen Acuan dari Pembimbing:
                        </h4>
                        <div class="instruction-resources-grid">
                            @if($tugas->link_referensi)
                                <div class="compact-link-strip">
                                    <div class="compact-link-meta">
                                        <div class="compact-link-icon" aria-hidden="true">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                                        </div>
                                        <div class="compact-link-text">
                                            <span class="compact-link-label">Tautan Referensi:</span>
                                            <a href="{{ $tugas->link_referensi }}" target="_blank" rel="noopener noreferrer" class="compact-link-anchor" title="{{ $tugas->link_referensi }}">
                                                {{ $tugas->link_referensi }}
                                            </a>
                                        </div>
                                    </div>
                                    <a href="{{ $tugas->link_referensi }}" target="_blank" rel="noopener noreferrer" class="btn-compact-open">
                                        <span>Buka</span>
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                                    </a>
                                </div>
                            @endif
                            @if($tugas->file_lampiran)
                                <div class="compact-file-strip">
                                    <div class="compact-file-meta">
                                        <div class="compact-file-icon" aria-hidden="true">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>
                                        </div>
                                        <div class="compact-file-text">
                                            <span class="compact-file-label">Berkas Panduan:</span>
                                            <span class="compact-file-name" title="{{ $tugas->nama_file_lampiran ?: 'Berkas Lampiran' }}">{{ $tugas->nama_file_lampiran ?: 'Berkas Lampiran' }}</span>
                                        </div>
                                    </div>
                                    <a href="{{ route('pembimbing.tugas.lampiran', $tugas->id) }}" class="btn-compact-download">
                                        <span>Unduh</span>
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </section>

            {{-- 2. Bagian Hasil Kiriman Peserta --}}
            <section class="review-panel-view" style="margin-top: 1.5rem;">
                @if($latestLog)
                    <div class="review-submission-card review-submission-card--compact">
                        <div class="submission-header-compact">
                            <div class="submission-info-compact">
                                <h3 class="card-submission-title">Kiriman Tugas Peserta</h3>
                            </div>
                            <div class="submission-status-wrap">
                                @if($isTugasSelesai || $latestReviewVal === 'diterima')
                                    <span class="review-status-pill pill-success">✓ Disetujui</span>
                                @elseif($latestReviewVal === 'perlu_revisi')
                                    {{-- teks perlu revisi dihapus --}}
                                @else
                                    <span class="review-status-pill pill-warning">Menunggu Evaluasi</span>
                                @endif
                            </div>
                        </div>

                        {{-- Tautan Hasil Kerja Mentee (Compact Strip) --}}
                        @if($latestLog->link)
                            <div class="compact-link-strip">
                                <div class="compact-link-meta">
                                    <div class="compact-link-icon" aria-hidden="true">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                                    </div>
                                    <div class="compact-link-text">
                                        <span class="compact-link-label">Tautan:</span>
                                        <a href="{{ $latestLog->link }}" target="_blank" rel="noopener noreferrer" class="compact-link-anchor" title="{{ $latestLog->link }}">
                                            {{ $latestLog->link }}
                                        </a>
                                    </div>
                                </div>
                                <a href="{{ $latestLog->link }}" target="_blank" rel="noopener noreferrer" class="btn-compact-open" title="Buka di Jendela Baru">
                                    <span>Buka<span class="sr-only"> di Jendela Baru</span></span>
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                                </a>
                            </div>
                        @endif

                        {{-- Berkas Lampiran Hasil Kerja Mentee (Compact Strip) --}}
                        @if($latestLog->file)
                            <div class="compact-file-strip">
                                <div class="compact-file-meta">
                                    <div class="compact-file-icon" aria-hidden="true">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>
                                    </div>
                                    <div class="compact-file-text">
                                        <span class="compact-file-label">Berkas Lampiran:</span>
                                        <span class="compact-file-name" title="{{ basename($latestLog->file) }}">{{ basename($latestLog->file) }}</span>
                                    </div>
                                </div>
                                <a href="{{ route('berkas.download', ['dokumen' => $latestLog->file]) }}" target="_blank" class="btn-compact-download">
                                    <span>Unduh</span>
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                </a>
                            </div>
                        @endif

                        {{-- Catatan Deskripsi Pengerjaan dari Mentee (Compact Notes) --}}
                        <div class="compact-notes-wrap">
                            <span class="compact-notes-label">Catatan Progres Pengerjaan:</span>
                            <div class="compact-notes-content">
                                {{ $latestLog->deskripsi ?: ($latestLog->catatan ?: 'Peserta tidak menyertakan catatan teks tambahan.') }}
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Empty State: Mentee Belum Menyerahkan Tugas --}}
                    <div class="review-empty-state">
                        <div class="review-empty-icon" aria-hidden="true">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="12" y1="18" x2="12" y2="12"></line>
                                <line x1="9" y1="15" x2="15" y2="15"></line>
                            </svg>
                        </div>
                        <h4 class="review-empty-title">Belum Ada Kiriman dari Peserta</h4>
                        <p class="review-empty-desc">
                            Peserta bimbingan belum mengunggah laporan atau mengirimkan tautan hasil pekerjaan untuk penugasan ini.
                        </p>
                    </div>
                @endif
            </section>
        </main>

        {{-- 3. Sidebar Kanan: Evaluasi, Umpan Balik & Kontrol Aksi Terpadu --}}
        <aside class="review-sidebar">
            <div class="sidebar-header">
                <h2>Evaluasi & Kontrol Aksi</h2>
                <button type="button" id="btnToggleSidebar" class="btn-toggle-sidebar" title="Sembunyikan / Buka panel evaluasi" aria-expanded="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="13 17 18 12 13 7"></polyline><polyline points="6 17 11 12 6 7"></polyline></svg>
                </button>
            </div>

            <div class="sidebar-content">

                {{-- Ringkasan Penyerahan Mentee --}}
                <div class="sidebar-submission-meta">
                    <div class="meta-row">
                        <span class="meta-label">Peserta Magang:</span>
                        <span class="meta-val">{{ $menteeNama }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Institusi:</span>
                        <span class="meta-val">{{ Str::limit($institusi, 22) }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Diserahkan pada:</span>
                        <span class="meta-val">
                            @if($latestLog)
                                {{ $latestLog->created_at->format('d M, H:i') }} WIB
                            @else
                                <span class="text-muted">Belum ada</span>
                            @endif
                        </span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Batas Tenggat:</span>
                        <span class="meta-val {{ $isOverdue ? 'overdue' : '' }}">
                            {{ $tugas->deadline?->format('d M Y') ?? 'Tanpa tenggat' }}
                            @if($isOverdue)
                                (Lewat)
                            @endif
                        </span>
                    </div>
                </div>

                {{-- Area Kontrol Aksi Terpadu: Tandai Selesai & Kembalikan --}}
                @if(!$isTugasSelesai && auth()->user()->role !== \App\Enums\RoleEnum::SuperAdmin)
                    <div class="unified-review-action-area" style="margin-top: 1.25rem;">
                        {{-- Field Catatan Umpan Balik Bergaya Modern Floating Card Box (Material Classroom Style) --}}
                        @php
                            $initialFeedback = old('feedback', $latestLog?->feedback);
                            $hasFeedbackVal = !empty(trim((string)$initialFeedback));
                        @endphp
                        <div class="review-feedback-field {{ $hasFeedbackVal ? 'has-value' : '' }} {{ $errors->has('feedback') ? 'has-error' : '' }}" id="feedbackFieldBox" style="margin-bottom: 16px;">
                            <label for="feedbackInput" class="review-feedback-label">
                                <span class="label-title">Catatan Umpan Balik</span>
                                <span id="feedbackReqBadge" class="label-badge-req">*(Wajib jika dikembalikan)</span>
                            </label>
                            <textarea id="feedbackInput" 
                                      name="feedback_display" 
                                      class="review-feedback-textarea-clean" 
                                      rows="4" 
                                      spellcheck="false">{{ $initialFeedback }}</textarea>
                        </div>

                        {{-- Form Hidden untuk Pengembalian Tugas (Dijalankan via Sub-Tombol Topbar) --}}
                        @if($latestLog)
                            <form action="{{ route('pembimbing.progres.review', $latestLog->id) }}" method="POST" id="formActionKembalikan" class="d-none">
                                @csrf
                                <input type="hidden" name="status_review" value="perlu_revisi">
                                <input type="hidden" name="feedback" id="hiddenFeedbackKembalikan">
                            </form>
                        @endif
                    </div>
                @else
                    <div class="review-completed-notice">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Tugas Ini Telah Ditandai Selesai</span>
                    </div>
                @endif

                {{-- Riwayat Evaluasi Sebelumnya --}}
                @if($allLogs->count() > 1)
                    <div class="sidebar-history-section" style="margin-top: 1.5rem;">
                        <label style="display: block; font-size: 0.775rem; font-weight: 700; color: #475569; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.04em;">
                            Riwayat Kiriman Sebelumnya ({{ $allLogs->count() }}):
                        </label>
                        @foreach($allLogs->slice(1) as $oldLog)
                            <div class="history-item">
                                <div class="history-header">
                                    <span>{{ $oldLog->created_at->format('d M Y, H:i') }}</span>
                                    <span>{{ ucfirst(str_replace('_', ' ', $oldLog->status_review?->value ?? (string)$oldLog->status_review)) }}</span>
                                </div>
                                <div class="history-body">
                                    {{ Str::limit($oldLog->feedback ?: ($oldLog->deskripsi ?: 'Tidak ada catatan'), 90) }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
        </aside>
    </div>
</div>

@push('scripts')
    @vite(['resources/js/pages/pembimbing/tugas-review.js'])
@endpush
@endsection
