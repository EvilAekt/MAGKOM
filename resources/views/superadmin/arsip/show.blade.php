@extends('layouts.dashboard')

@section('title', 'Detail Arsip Peserta ' . ($penempatan->peserta?->nama ?? 'Alumni') . ' - SuperAdmin MagKom')
@section('page_title', 'Detail Arsip Alumni Magang')
@section('page_description', 'Rekam jejak komprehensif penempatan, biodata, tugas, logbook, rubrik penilaian, dan dokumen administrasi (AC-12-4, C9).')

@section('breadcrumbs')
    <nav style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; color: var(--neutral-600, #64748B);">
        <span>SuperAdmin</span>
        <span>/</span>
        <a href="{{ route('superadmin.arsip.index') }}" style="color: var(--neutral-600, #64748B); text-decoration: none;">Arsip Peserta</a>
        <span>/</span>
        <span style="color: var(--neutral-900, #0F172A); font-weight: 700;">{{ $penempatan->peserta?->nama }}</span>
    </nav>
@endsection

@section('page_actions')
    <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
        <a href="{{ route('admin.penempatan.surat-diterima', $penempatan) }}"
           style="display: inline-flex; align-items: center; gap: 0.375rem; height: 38px; padding: 0 1rem; background: #ffffff; color: #1D4ED8; border: 1px solid #BFDBFE; font-weight: 700; font-size: 0.8125rem; text-decoration: none; border-radius: 8px;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
            <span>Surat Diterima PDF</span>
        </a>

        @if($penempatan->penilaian && $penempatan->penilaian->status?->value === 'final')
            <a href="{{ route('pembimbing.penilaian.unduh', $penempatan) }}" target="_blank"
               style="display: inline-flex; align-items: center; gap: 0.375rem; height: 38px; padding: 0 1rem; background: #0641CC; color: #FFFFFF; font-weight: 700; font-size: 0.8125rem; text-decoration: none; border-radius: 8px; box-shadow: 0 1px 3px rgba(6,65,204,0.2);">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                <span>Unduh Lembar Nilai Resmi</span>
            </a>
        @endif

        <a href="{{ route('superadmin.arsip.index') }}"
           style="display: inline-flex; align-items: center; gap: 0.375rem; height: 38px; padding: 0 1rem; background: var(--neutral-100, #F1F5F9); color: var(--neutral-800, #1E293B); font-weight: 700; font-size: 0.8125rem; text-decoration: none; border-radius: 8px; border: 1px solid var(--neutral-300, #CBD5E1);">
            <span>&larr; Kembali</span>
        </a>
    </div>
@endsection

@section('content')
@php
    $peserta = $penempatan->peserta;
    $profil = $peserta?->profil;
    $pembimbing = $penempatan->pembimbing;
    $penilaian = $penempatan->penilaian;
@endphp

<div style="display: flex; flex-direction: column; gap: 1.5rem; width: 100%;">

    <style>
        .arsip-card {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid var(--neutral-200, #E2E8F0);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }
        .arsip-tab-bar {
            display: flex;
            gap: 0.5rem;
            border-bottom: 2px solid var(--neutral-200, #E2E8F0);
            overflow-x: auto;
            padding-bottom: 2px;
        }
        .arsip-tab-btn {
            padding: 0.75rem 1.25rem;
            border: none;
            background: transparent;
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--neutral-600, #64748B);
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.15s ease;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .arsip-tab-btn:hover {
            color: #0641CC;
        }
        .arsip-tab-btn.active {
            color: #0641CC;
            border-bottom-color: #0641CC;
            background: #EFF6FF;
            border-radius: 8px 8px 0 0;
        }
        .arsip-tab-pane {
            display: none;
        }
        .arsip-tab-pane.active {
            display: block;
        }

        /* Dark Mode Overrides for SuperAdmin Arsip Detail */
        [data-theme="dark"] .arsip-card {
            background: #182234 !important;
            border-color: #26354D !important;
            color: #F8FAFC !important;
        }
        [data-theme="dark"] .arsip-card[style*="linear-gradient"] {
            background: linear-gradient(135deg, #182234 0%, #111C2E 100%) !important;
            border-color: #26354D !important;
        }
        [data-theme="dark"] .arsip-tab-bar {
            border-bottom-color: #26354D !important;
        }
        [data-theme="dark"] .arsip-tab-btn {
            color: #94A3B8 !important;
        }
        [data-theme="dark"] .arsip-tab-btn:hover {
            color: #F8FAFC !important;
        }
        [data-theme="dark"] .arsip-tab-btn.active {
            color: #93C5FD !important;
            background: rgba(37, 99, 235, 0.2) !important;
            border-bottom-color: #3B82F6 !important;
        }
        [data-theme="dark"] a[href*="surat-diterima"] {
            background: #111C2E !important;
            border-color: #26354D !important;
            color: #93C5FD !important;
        }
        [data-theme="dark"] a[href*="arsip.index"] {
            background: #111C2E !important;
            border-color: #26354D !important;
            color: #94A3B8 !important;
        }
        [data-theme="dark"] table th {
            background: #141E30 !important;
            color: #94A3B8 !important;
            border-bottom-color: #26354D !important;
        }
        [data-theme="dark"] table td {
            color: #CBD5E1 !important;
            border-bottom-color: #26354D !important;
        }
        [data-theme="dark"] table tbody tr:hover td {
            background: #162238 !important;
        }
        [data-theme="dark"] div[style*="background: #ffffff"],
        [data-theme="dark"] div[style*="background: #FFFFFF"],
        [data-theme="dark"] div[style*="background: var(--neutral-0)"] {
            background-color: #182234 !important;
            border-color: #26354D !important;
        }
        [data-theme="dark"] div[style*="background: var(--neutral-100)"],
        [data-theme="dark"] div[style*="background: #F8FAFC"],
        [data-theme="dark"] div[style*="background: #f8fafc"] {
            background-color: #111C2E !important;
            border-color: #26354D !important;
        }
        [data-theme="dark"] h2,
        [data-theme="dark"] h3,
        [data-theme="dark"] h4,
        [data-theme="dark"] strong {
            color: #F8FAFC !important;
        }
    </style>

    {{-- 1. HEADER ALUMNI PROFILE CARD --}}
    <x-gentelella.card class="arsip-card" style="padding: 1.5rem 1.75rem; background: linear-gradient(135deg, #F8FAFC 0%, #EFF6FF 100%);">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.25rem; width: 100%;">
            <div style="display: flex; align-items: center; gap: 1.25rem;">
                <div style="width: 60px; height: 60px; border-radius: 50%; background: #0641CC; border: 3px solid #DBEAFE; color: #ffffff; font-size: 1.5rem; font-weight: 800; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    @if($profil?->foto_url)
                        <img src="{{ $profil->foto_url }}" alt="{{ $peserta?->nama }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                    @else
                        {{ strtoupper(substr($peserta?->nama ?? 'A', 0, 2)) }}
                    @endif
                </div>
                <div>
                    <div style="display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
                        <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0;">
                            {{ $peserta?->nama ?? '-' }}
                        </h2>
                        <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.2rem 0.6rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: {{ $penempatan->status?->value === 'selesai' ? '#ECFDF5' : '#F1F5F9' }}; color: {{ $penempatan->status?->value === 'selesai' ? '#065F46' : '#475569' }}; border: 1px solid {{ $penempatan->status?->value === 'selesai' ? '#A7F3D0' : '#CBD5E1' }};">
                            {{ $penempatan->status?->label() }}
                        </span>
                        <span style="font-size: 0.72rem; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 4px; background: #FEF3C7; color: #92400E; border: 1px solid #FCD34D;">
                            Arsip Read-Only (Governance)
                        </span>
                    </div>
                    <div style="font-size: 0.875rem; color: var(--neutral-600, #64748B); margin-top: 0.25rem; display: flex; gap: 1rem; flex-wrap: wrap;">
                        <span>Email: <strong>{{ $peserta?->email }}</strong></span>
                        <span>•</span>
                        <span>NIM/NISN: <strong>{{ $profil?->nim_nisn ?? '-' }}</strong></span>
                        <span>•</span>
                        <span>Institusi: <strong>{{ $profil?->institusi ?? '-' }}</strong></span>
                    </div>
                </div>
            </div>

            @if($penilaian)
                <div style="background: #ffffff; border: 1px solid #BFDBFE; border-radius: 10px; padding: 0.65rem 1rem; text-align: right;">
                    <div style="font-size: 0.7rem; font-weight: 700; color: var(--neutral-500, #64748B); text-transform: uppercase;">Nilai Akhir Magang</div>
                    <div style="font-size: 1.35rem; font-weight: 900; color: #0641CC; line-height: 1.1; margin-top: 0.15rem;">
                        {{ number_format((float) $penilaian->nilai_akhir, 2) }}
                        <span style="font-size: 0.8125rem; font-weight: 800; color: #16A34A;">(Predikat {{ $penilaian->predikat ?? 'Lulus' }})</span>
                    </div>
                </div>
            @endif
        </div>
    </x-gentelella.card>

    {{-- 2. TAB CONTROLLER --}}
    <div class="arsip-tab-bar" role="tablist">
        <button type="button" class="arsip-tab-btn active" onclick="switchArsipTab('tab-ringkasan', this)">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            <span>1. Ringkasan &amp; Biodata</span>
        </button>
        <button type="button" class="arsip-tab-btn" onclick="switchArsipTab('tab-tugas', this)">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"></path><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
            <span>2. Riwayat Tugas ({{ $penempatan->tugas->count() }})</span>
        </button>
        <button type="button" class="arsip-tab-btn" onclick="switchArsipTab('tab-logbook', this)">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
            <span>3. Logbook Harian ({{ $penempatan->logbooks->count() }})</span>
        </button>
        <button type="button" class="arsip-tab-btn" onclick="switchArsipTab('tab-nilai', this)">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
            <span>4. Rubrik Penilaian Resmi</span>
        </button>
        <button type="button" class="arsip-tab-btn" onclick="switchArsipTab('tab-administrasi', this)">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
            <span>5. Dokumen Pendaftaran</span>
        </button>
    </div>

    {{-- TAB 1: RINGKASAN & BIODATA --}}
    <div class="arsip-tab-pane active" id="tab-ringkasan">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 1.5rem;">
            
            <div class="arsip-card" style="padding: 1.5rem;">
                <h3 style="margin: 0 0 1rem 0; font-size: 1rem; font-weight: 800; color: var(--neutral-900, #0F172A); border-bottom: 1px solid var(--neutral-200, #E2E8F0); padding-bottom: 0.75rem;">
                    Data Penempatan & Institusi
                </h3>
                <div style="display: flex; flex-direction: column; gap: 0.85rem; font-size: 0.875rem;">
                    <div>
                        <span style="color: var(--neutral-500, #64748B); display: block; font-size: 0.72rem; font-weight: 700; text-transform: uppercase;">Bidang Dinas</span>
                        <strong style="color: var(--neutral-900, #0F172A);">{{ $penempatan->bidang?->nama_bidang ?? 'Dinas Kominfo Sleman' }}</strong>
                    </div>
                    <div>
                        <span style="color: var(--neutral-500, #64748B); display: block; font-size: 0.72rem; font-weight: 700; text-transform: uppercase;">Jurusan Terdaftar</span>
                        <strong>{{ $penempatan->kuota?->jurusan?->nama_jurusan ?? ($profil?->jurusan?->nama_jurusan ?? '-') }}</strong>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; background: #F8FAFC; padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid #E2E8F0;">
                        <div>
                            <span style="color: var(--neutral-500, #64748B); display: block; font-size: 0.6875rem; font-weight: 700;">TANGGAL MULAI</span>
                            <strong>{{ $penempatan->tgl_mulai?->translatedFormat('d M Y') ?? '-' }}</strong>
                        </div>
                        <div>
                            <span style="color: var(--neutral-500, #64748B); display: block; font-size: 0.6875rem; font-weight: 700;">TANGGAL SELESAI</span>
                            <strong>{{ $penempatan->tgl_selesai?->translatedFormat('d M Y') ?? '-' }}</strong>
                        </div>
                    </div>
                    <div>
                        <span style="color: var(--neutral-500, #64748B); display: block; font-size: 0.72rem; font-weight: 700; text-transform: uppercase;">Jalur & Sumber</span>
                        <span>{{ $penempatan->jalur?->label() ?? 'Reguler' }} &bull; {{ $penempatan->sumber?->label() ?? 'Portal' }}</span>
                    </div>
                </div>
            </div>

            <div class="arsip-card" style="padding: 1.5rem;">
                <h3 style="margin: 0 0 1rem 0; font-size: 1rem; font-weight: 800; color: var(--neutral-900, #0F172A); border-bottom: 1px solid var(--neutral-200, #E2E8F0); padding-bottom: 0.75rem;">
                    Pembimbing Lapangan
                </h3>
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: #EFF6FF; border: 1px solid #BFDBFE; display: flex; align-items: center; justify-content: center; font-weight: 800; color: #0641CC;">
                        {{ strtoupper(substr($pembimbing?->nama ?? 'P', 0, 2)) }}
                    </div>
                    <div>
                        <div style="font-size: 0.9375rem; font-weight: 800; color: var(--neutral-900, #0F172A);">{{ $pembimbing?->nama ?? 'Tidak Ada' }}</div>
                        <div style="font-size: 0.8125rem; color: var(--neutral-600, #64748B);">{{ $pembimbing?->email ?? '-' }}</div>
                    </div>
                </div>

                <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 1rem; font-size: 0.8125rem; display: flex; flex-direction: column; gap: 0.5rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--neutral-600, #64748B);">Total Tugas Diberikan:</span>
                        <strong>{{ $penempatan->tugas->count() }} Tugas</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--neutral-600, #64748B);">Tugas Selesai:</span>
                        <strong style="color: #16A34A;">{{ $penempatan->tugas->where('status.value', 'selesai')->count() }} Selesai</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--neutral-600, #64748B);">Total Logbook Terisi:</span>
                        <strong style="color: #0641CC;">{{ $penempatan->logbooks->count() }} Entri</strong>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- TAB 2: RIWAYAT TUGAS --}}
    <div class="arsip-tab-pane" id="tab-tugas">
        <div class="arsip-card" style="padding: 1.5rem;">
            <h3 style="margin: 0 0 1rem 0; font-size: 1rem; font-weight: 800; color: var(--neutral-900, #0F172A);">
                Daftar Tugas & Penugasan Magang (Read-Only)
            </h3>
            @if($penempatan->tugas->isNotEmpty())
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    @foreach($penempatan->tugas as $t)
                        <div style="padding: 1rem; border: 1px solid var(--neutral-200, #E2E8F0); border-radius: 10px; background: #F8FAFC;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; flex-wrap: wrap;">
                                <div>
                                    <h4 style="margin: 0; font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900, #0F172A);">
                                        {{ $t->judul }}
                                    </h4>
                                    <p style="margin: 0.25rem 0 0 0; font-size: 0.8125rem; color: var(--neutral-600, #64748B);">
                                        {{ $t->deskripsi }}
                                    </p>
                                </div>
                                <span style="font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 6px; background: {{ $t->status?->value === 'selesai' ? '#ECFDF5' : '#EFF6FF' }}; color: {{ $t->status?->value === 'selesai' ? '#065F46' : '#1D4ED8' }};">
                                    {{ $t->status?->label() ?? ucfirst($t->status?->value ?? 'Aktif') }}
                                </span>
                            </div>
                            @if($t->progresLogs->isNotEmpty())
                                <div style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px dashed var(--neutral-300, #CBD5E1); font-size: 0.78125rem; color: var(--neutral-700, #334155);">
                                    <strong>Progres Terakhir:</strong> {{ $t->progresLogs->first()->catatan ?? '-' }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 2rem; color: var(--neutral-500, #64748B); font-size: 0.875rem;">
                    Tidak ada catatan tugas untuk peserta ini.
                </div>
            @endif
        </div>
    </div>

    {{-- TAB 3: REKAPITULASI LOGBOOK --}}
    <div class="arsip-tab-pane" id="tab-logbook">
        <div class="arsip-card" style="padding: 1.5rem;">
            <h3 style="margin: 0 0 1rem 0; font-size: 1rem; font-weight: 800; color: var(--neutral-900, #0F172A);">
                Rekam Jejak Logbook Harian Magang (Read-Only)
            </h3>
            @if($penempatan->logbooks->isNotEmpty())
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.8125rem;">
                        <thead>
                            <tr style="background: #F8FAFC; border-bottom: 1px solid var(--neutral-300, #CBD5E1); text-transform: uppercase; color: var(--neutral-600, #475569);">
                                <th style="padding: 0.75rem; text-align: left;">Tanggal</th>
                                <th style="padding: 0.75rem; text-align: left;">Minggu</th>
                                <th style="padding: 0.75rem; text-align: left;">Aktivitas Magang</th>
                                <th style="padding: 0.75rem; text-align: center;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($penempatan->logbooks as $lb)
                                <tr style="border-bottom: 1px solid var(--neutral-200, #E2E8F0);">
                                    <td style="padding: 0.75rem; font-weight: 600;">{{ \Carbon\Carbon::parse($lb->tanggal)->translatedFormat('d M Y') }}</td>
                                    <td style="padding: 0.75rem;">Minggu ke-{{ $lb->minggu_ke ?? 1 }}</td>
                                    <td style="padding: 0.75rem;">{{ $lb->ringkasan_aktivitas ?? ($lb->aktivitas ?? '-') }}</td>
                                    <td style="padding: 0.75rem; text-align: center;">
                                        <span style="font-size: 0.7rem; font-weight: 700; padding: 0.15rem 0.45rem; border-radius: 4px; background: #ECFDF5; color: #065F46;">
                                            {{ ucfirst($lb->status?->value ?? (string)$lb->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="text-align: center; padding: 2rem; color: var(--neutral-500, #64748B); font-size: 0.875rem;">
                    Tidak ada catatan logbook yang ditemukan.
                </div>
            @endif
        </div>
    </div>

    {{-- TAB 4: RUBRIK PENILAIAN RESMI --}}
    <div class="arsip-tab-pane" id="tab-nilai">
        <div class="arsip-card" style="padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--neutral-200, #E2E8F0); padding-bottom: 0.75rem; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h3 style="margin: 0; font-size: 1rem; font-weight: 800; color: var(--neutral-900, #0F172A);">
                        Rincian Evaluasi Nilai Resmi Berdasarkan Rubrik
                    </h3>
                </div>
                @if($penilaian)
                    <div style="font-size: 1.25rem; font-weight: 800; color: #0641CC;">
                        Nilai Akhir: {{ number_format((float) $penilaian->nilai_akhir, 2) }}
                    </div>
                @endif
            </div>

            @if($penilaian && $penilaian->details->isNotEmpty())
                <div style="overflow-x: auto; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                        <thead>
                            <tr style="background: #F8FAFC; border-bottom: 1px solid var(--neutral-300, #CBD5E1); font-size: 0.75rem; text-transform: uppercase; color: var(--neutral-600, #475569);">
                                <th style="padding: 0.75rem 1rem; text-align: left;">Kriteria Rubrik</th>
                                <th style="padding: 0.75rem 1rem; text-align: center;">Bobot (%)</th>
                                <th style="padding: 0.75rem 1rem; text-align: center;">Skor (0-100)</th>
                                <th style="padding: 0.75rem 1rem; text-align: center;">Kontribusi Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($penilaian->details as $detail)
                                @php
                                    $kontribusi = ($detail->skor * $detail->bobot) / 100;
                                @endphp
                                <tr style="border-bottom: 1px solid var(--neutral-200, #E2E8F0);">
                                    <td style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-900, #0F172A);">
                                        {{ $detail->rubrik?->kriteria ?? '-' }}
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: center;">{{ $detail->bobot }}%</td>
                                    <td style="padding: 0.75rem 1rem; text-align: center; font-weight: 800; color: #0641CC;">{{ $detail->skor }}</td>
                                    <td style="padding: 0.75rem 1rem; text-align: center; font-weight: 800; color: #16A34A;">{{ number_format($kontribusi, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="text-align: center; padding: 2rem; color: var(--neutral-500, #64748B); font-size: 0.875rem;">
                    Belum ada nilai yang terdata.
                </div>
            @endif
        </div>
    </div>

    {{-- TAB 5: DOKUMEN PENDAFTARAN --}}
    <div class="arsip-tab-pane" id="tab-administrasi">
        <div class="arsip-card" style="padding: 1.5rem;">
            <h3 style="margin: 0 0 1rem 0; font-size: 1rem; font-weight: 800; color: var(--neutral-900, #0F172A);">
                Berkas Administrasi & Dokumen Terverifikasi
            </h3>
            @if($penempatan->pendaftaran && $penempatan->pendaftaran->dokumen->isNotEmpty())
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 0.75rem;">
                    @foreach($penempatan->pendaftaran->dokumen as $dok)
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid var(--neutral-200, #E2E8F0); background: #F8FAFC;">
                            <div>
                                <div style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-900, #0F172A);">
                                    {{ ucwords(str_replace('_', ' ', $dok->jenis_dokumen)) }}
                                </div>
                                <div style="font-size: 0.72rem; color: var(--neutral-500, #64748B);">
                                    Terverifikasi saat seleksi
                                </div>
                            </div>
                            <span style="font-size: 0.7rem; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 4px; background: #ECFDF5; color: #065F46;">
                                Valid
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 2rem; color: var(--neutral-500, #64748B); font-size: 0.875rem;">
                    Tidak ada berkas unggahan terdata.
                </div>
            @endif
        </div>
    </div>

</div>

<script>
    function switchArsipTab(tabId, btn) {
        document.querySelectorAll('.arsip-tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.arsip-tab-pane').forEach(p => p.classList.remove('active'));
        btn.classList.add('active');
        const target = document.getElementById(tabId);
        if (target) {
            target.classList.add('active');
        }
    }
</script>
@endsection
