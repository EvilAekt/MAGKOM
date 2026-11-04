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
            {{-- Tombol Tutup / Kembali ke Daftar Tugas --}}
            <a href="{{ route('pembimbing.tugas.index') }}" class="review-btn-close" title="Tutup dan kembali ke daftar tugas" aria-label="Tutup dan kembali">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </a>

            <div class="review-v-divider" aria-hidden="true"></div>

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
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#01347C" stroke-width="2.5" class="review-dropdown-item-check"><polyline points="20 6 9 17 4 12"></polyline></svg>
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
            @elseif($latestReviewVal === 'perlu_revisi')
                <span class="review-status-badge perlu_revisi">Perlu Revisi</span>
            @elseif($latestReviewVal === 'diterima')
                <span class="review-status-badge proses">Progres Diterima</span>
            @elseif($allLogs->isNotEmpty())
                <span class="review-status-badge proses">Menunggu Review</span>
            @else
                <span class="review-status-badge belum">Belum Ada Kiriman</span>
            @endif

            {{-- Tombol Edit Tugas (Jika belum selesai) --}}
            @if(!$isTugasSelesai && auth()->user()->role !== \App\Enums\RoleEnum::SuperAdmin)
                <a href="{{ route('pembimbing.tugas.edit', $tugas->id) }}" class="review-btn-action btn-edit" title="Ubah instruksi atau tenggat tugas">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    <span>Edit Tugas</span>
                </a>
            @endif

            {{-- Tombol Tandai Selesai Cepat (Jika belum selesai) --}}
            @if(!$isTugasSelesai && auth()->user()->role !== \App\Enums\RoleEnum::SuperAdmin)
                <form action="{{ route('pembimbing.tugas.selesai', $tugas->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin seluruh hasil kerja tugas ini telah tuntas dan disetujui?')">
                    @csrf
                    <button type="submit" class="review-btn-action btn-complete" title="Konfirmasi tugas ini telah selesai tuntas">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Tandai Selesai</span>
                    </button>
                </form>
            @endif
        </div>
    </header>

    {{-- Alert Notifikasi Session --}}
    @if(session('success'))
        <div style="background: #ECFDF5; border: 1px solid #A7F3D0; color: #065F46; padding: 12px 18px; margin: 12px 1.5rem 0 1.5rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600;">
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

    {{-- 2. Layout Tubuh: Canvas Pratinjau Kiri & Sidebar Evaluasi Kanan --}}
    <div class="review-body-layout">

        {{-- Area Kanvas Utama (Kiri / Tengah) --}}
        <main class="review-main-canvas">

            {{-- Tab Switcher: Kiriman Peserta vs Instruksi Tugas --}}
            <div class="review-canvas-tabs" role="tablist" aria-label="Tampilan Kanvas Tugas">
                <button type="button" id="tabBtnSubmission" class="review-tab-btn active" role="tab" aria-selected="true">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    <span>Hasil Kiriman Peserta</span>
                    @if($allLogs->isNotEmpty())
                        <span style="background: #E2E8F0; padding: 1px 7px; border-radius: 999px; font-size: 0.725rem; font-weight: 700;">{{ $allLogs->count() }}</span>
                    @endif
                </button>
                <button type="button" id="tabBtnInstruction" class="review-tab-btn" role="tab" aria-selected="false">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                    <span>Rincian Instruksi Tugas</span>
                </button>
            </div>

            {{-- Panel 1: Pratinjau Hasil Kiriman Peserta --}}
            <section id="panelSubmission" class="review-panel-view">
                @if($latestLog)
                    <div class="review-submission-card">
                        <div class="submission-header-row">
                            <div class="submission-title-group">
                                <h3>Kiriman Terakhir: {{ $latestLog->tgl_lapor ? \Carbon\Carbon::parse($latestLog->tgl_lapor)->translatedFormat('d F Y') : $latestLog->created_at->translatedFormat('d F Y, H:i') }} WIB</h3>
                                <div class="submission-meta-sub">
                                    Diajukan oleh <strong>{{ $menteeNama }}</strong> ({{ $latestLog->created_at->diffForHumans() }})
                                </div>
                            </div>
                            <div>
                                @if($latestReviewVal === 'diterima')
                                    <span style="padding: 4px 12px; background: #DCFCE7; color: #15803D; border-radius: 999px; font-size: 0.775rem; font-weight: 700;">✓ Disetujui</span>
                                @elseif($latestReviewVal === 'perlu_revisi')
                                    <span style="padding: 4px 12px; background: #FEE2E2; color: #B91C1C; border-radius: 999px; font-size: 0.775rem; font-weight: 700;">⚠️ Perlu Revisi</span>
                                @else
                                    <span style="padding: 4px 12px; background: #FEF3C7; color: #B45309; border-radius: 999px; font-size: 0.775rem; font-weight: 700;">Menunggu Evaluasi</span>
                                @endif
                            </div>
                        </div>

                        {{-- Tautan Hasil Kerja Mentee (Jika Ada) --}}
                        @if($latestLog->link)
                            <div class="review-link-preview-box">
                                <div class="link-preview-info">
                                    <div class="link-preview-icon" aria-hidden="true">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                                    </div>
                                    <div class="link-preview-text">
                                        <div class="link-preview-label">Tautan Progres Hasil Kerja Peserta</div>
                                        <a href="{{ $latestLog->link }}" target="_blank" rel="noopener noreferrer" class="link-preview-url" title="{{ $latestLog->link }}">
                                            {{ $latestLog->link }}
                                        </a>
                                    </div>
                                </div>
                                <a href="{{ $latestLog->link }}" target="_blank" rel="noopener noreferrer" class="btn-open-external">
                                    <span>Buka di Jendela Baru</span>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                                </a>
                            </div>
                        @endif

                        {{-- Berkas Lampiran Hasil Kerja Mentee (Jika Ada) --}}
                        @if($latestLog->file)
                            <div class="review-file-preview-box">
                                <div class="file-preview-info">
                                    <div class="file-preview-icon" aria-hidden="true">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>
                                    </div>
                                    <div>
                                        <div class="file-preview-name">{{ basename($latestLog->file) }}</div>
                                        <div class="file-preview-meta">Berkas Lampiran Unggahan Mentee</div>
                                    </div>
                                </div>
                                <a href="{{ route('berkas.download', ['dokumen' => $latestLog->file]) }}" target="_blank" class="btn-download-file">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                    <span>Unduh Berkas</span>
                                </a>
                            </div>
                        @endif

                        {{-- Catatan Deskripsi Pengerjaan dari Mentee --}}
                        <div style="margin-top: 1rem;">
                            <label style="font-size: 0.775rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.04em; display: block; margin-bottom: 6px;">
                                Catatan Progres Pengerjaan:
                            </label>
                            <div class="submission-notes-box">
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

            {{-- Panel 2: Rincian Instruksi Tugas Awal dari Pembimbing --}}
            <section id="panelInstruction" class="review-panel-view d-none">
                <div class="instruction-card">
                    <h2 class="instruction-task-title">{{ $tugas->judul }}</h2>

                    <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 1.25rem;">
                        <span style="padding: 3px 10px; background: #EFF6FF; color: #1D4ED8; border-radius: 6px; font-size: 0.75rem; font-weight: 700;">
                            {{ $tugas->kategori }}
                        </span>
                        <span style="padding: 3px 10px; background: #F1F5F9; color: #475569; border-radius: 6px; font-size: 0.75rem; font-weight: 600;">
                            Tenggat: {{ $tugas->deadline?->translatedFormat('d F Y') ?? 'Tanpa tenggat' }}
                        </span>
                    </div>

                    <div class="instruction-text-content">
                        {!! nl2br(e($tugas->deskripsi)) ?: '<em>Tidak ada instruksi khusus yang disertakan.</em>' !!}
                    </div>

                    {{-- Materi / Sumber Daya Acuan dari Pembimbing --}}
                    @if($tugas->file_lampiran || $tugas->link_referensi)
                        <div style="border-top: 1px solid #E2E8F0; padding-top: 1.25rem;">
                            <h4 style="font-size: 0.8125rem; font-weight: 700; color: #475569; text-transform: uppercase; margin: 0 0 10px 0;">
                                Materi & Dokumen Acuan dari Pembimbing:
                            </h4>
                            <div class="instruction-resources-grid">
                                @if($tugas->link_referensi)
                                    <div style="padding: 10px 14px; background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 8px; display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                                        <div style="min-width: 0;">
                                            <div style="font-size: 0.75rem; font-weight: 700; color: #166534;">Tautan Referensi Eksternal</div>
                                            <div style="font-size: 0.8125rem; color: #15803D; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 280px;">{{ $tugas->link_referensi }}</div>
                                        </div>
                                        <a href="{{ $tugas->link_referensi }}" target="_blank" rel="noopener noreferrer" style="font-size: 0.75rem; font-weight: 700; color: #166534; text-decoration: none; padding: 4px 8px; background: #DCFCE7; border-radius: 6px;">Buka ↗</a>
                                    </div>
                                @endif
                                @if($tugas->file_lampiran)
                                    <div style="padding: 10px 14px; background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 8px; display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                                        <div style="min-width: 0;">
                                            <div style="font-size: 0.75rem; font-weight: 700; color: #1E40AF;">Berkas Panduan Pembimbing</div>
                                            <div style="font-size: 0.8125rem; color: #1D4ED8; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 280px;">{{ $tugas->nama_file_lampiran ?: 'Berkas Lampiran' }}</div>
                                        </div>
                                        <a href="{{ route('pembimbing.tugas.lampiran', $tugas->id) }}" style="font-size: 0.75rem; font-weight: 700; color: #1E40AF; text-decoration: none; padding: 4px 8px; background: #DBEAFE; border-radius: 6px;">Unduh ⬇</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </section>
        </main>

        {{-- 3. Sidebar Kanan: Evaluasi, Umpan Balik & Pengembalian Tugas --}}
        <aside class="review-sidebar">
            <div class="sidebar-header">
                <h2>Evaluasi & Umpan Balik</h2>
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

                {{-- Formulir Evaluasi (Outcome-Based Mentoring: Diterima vs Perlu Revisi) --}}
                @if($latestLog && auth()->user()->role !== \App\Enums\RoleEnum::SuperAdmin)
                    <form action="{{ route('pembimbing.progres.review', $latestLog->id) }}" method="POST" id="formReviewProgres">
                        @csrf
                        <div class="eval-decision-group">
                            <label class="eval-decision-title">Hasil Keputusan Evaluasi:</label>
                            <div class="eval-radio-options">
                                <label id="radioPillDiterima" class="eval-radio-pill {{ $latestReviewVal === 'diterima' ? 'selected-diterima' : '' }}">
                                    <input type="radio" name="status_review" value="diterima" {{ $latestReviewVal === 'diterima' ? 'checked' : '' }} required>
                                    <span>✓ Diterima</span>
                                </label>
                                <label id="radioPillRevisi" class="eval-radio-pill {{ $latestReviewVal === 'perlu_revisi' ? 'selected-revisi' : '' }}">
                                    <input type="radio" name="status_review" value="perlu_revisi" {{ $latestReviewVal === 'perlu_revisi' ? 'checked' : '' }} required>
                                    <span>Kembalikan</span>
                                </label>
                            </div>
                        </div>

                        {{-- Template Arahan Evaluasi Cepat --}}
                        <div style="margin-top: 14px;">
                            <label for="snippetSelect" style="display: block; font-size: 0.775rem; font-weight: 700; color: #475569; margin-bottom: 4px;">
                                Template Arahan Cepat (Opsional):
                            </label>
                            <select id="snippetSelect" class="review-template-select">
                                <option value="">-- Pilih Arahan Cepat --</option>
                                <option value="Hasil pekerjaan telah sesuai dengan spesifikasi dan standar mutu dinas. Lanjutkan ke tahapan berikutnya.">Pekerjaan Sesuai Spesifikasi</option>
                                <option value="Perlu perbaikan format laporan, mohon sertakan dokumentasi hasil pengujian sistem secara lengkap.">Perlu Dokumentasi & Pengujian</option>
                                <option value="Tautan yang disertakan belum dapat diakses publik, mohon sesuaikan hak akses berbagi menjadi viewer/editor.">Perbaiki Hak Akses Tautan</option>
                                <option value="Progres berjalan baik, lanjutkan pengerjaan modul berikutnya sesuai jadwal.">Lanjutkan ke Tahap Berikutnya</option>
                                <option value="Terdapat kendala teknis pada hasil kerja, mari kita diskusikan saat sesi tatap muka mingguan.">Jadwalkan Diskusi Bimbingan</option>
                            </select>
                        </div>

                        {{-- Textarea Arahan / Umpan Balik untuk Mentee --}}
                        <div style="margin-top: 14px;">
                            <label for="feedbackInput" style="display: flex; justify-content: space-between; align-items: center; font-size: 0.775rem; font-weight: 700; color: #475569; margin-bottom: 4px;">
                                <span>Catatan Umpan Balik untuk Mentee</span>
                                <span id="feedbackReqBadge" style="color: #DC2626; display: none;">*(Wajib jika dikembalikan)</span>
                            </label>
                            <textarea name="feedback" 
                                      id="feedbackInput" 
                                      class="review-feedback-textarea" 
                                      placeholder="Tuliskan catatan apresiasi atau instruksi perbaikan untuk peserta...">{{ old('feedback', $latestLog->feedback) }}</textarea>
                        </div>

                        <div style="margin-top: 14px;">
                            <button type="submit" class="btn-submit-review">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <span>Simpan Evaluasi & Kirim</span>
                            </button>
                        </div>
                    </form>
                @elseif(!$latestLog)
                    <div style="padding: 1.25rem; background: #F8FAFC; border: 1px dashed #CBD5E1; border-radius: 8px; text-align: center; font-size: 0.8125rem; color: #64748B;">
                        Menunggu kiriman progres dari mentee sebelum dapat memberikan evaluasi atau mengembalikan tugas.
                    </div>
                @endif

                {{-- Aksi Tandai Selesai (Jika memenuhi kriteria) --}}
                @if(!$isTugasSelesai && auth()->user()->role !== \App\Enums\RoleEnum::SuperAdmin)
                    <div class="sidebar-complete-section">
                        <label style="display: block; font-size: 0.775rem; font-weight: 700; color: #475569; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.04em;">
                            Penyelesaian Tugas:
                        </label>
                        <form action="{{ route('pembimbing.tugas.selesai', $tugas->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin tugas ini telah tuntas memenuhi kriteria pembimbing?')">
                            @csrf
                            <button type="submit" class="btn-mark-finished" title="Setujui dan selesaikan tugas ini">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <span>Tandai Tugas Selesai</span>
                            </button>
                        </form>
                    </div>
                @endif

                {{-- Riwayat Evaluasi Sebelumnya --}}
                @if($allLogs->count() > 1)
                    <div class="sidebar-history-section">
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
