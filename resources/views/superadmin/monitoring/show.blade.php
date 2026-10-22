@extends('layouts.dashboard')

@section('title', 'Detail Pemantauan Peserta - SuperAdmin MagKom')
@section('page_title', 'Detail Pemantauan Bimbingan Peserta Aktif')
@section('page_description', 'Tinjau rekam jejak bimbingan, tugas, logbook harian, dan snapshot nilai peserta aktif secara baca-saja (AC-07-7, AC-08-5).')

@section('breadcrumbs')
    <a href="{{ route('superadmin.dashboard') }}">SuperAdmin</a>
    <span class="breadcrumb-separator">/</span>
    <a href="{{ route('superadmin.monitoring.index') }}">Monitoring Peserta</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">{{ $penempatan->peserta?->nama }}</span>
@endsection

@section('page_actions')
    <div style="display: flex; gap: 0.5rem; align-items: center;">
        @if($penempatan->pendaftaran_id)
            <a href="{{ route('admin.penempatan.surat-diterima.pratinjau', $penempatan) }}" target="_blank" style="display: inline-flex; align-items: center; gap: 0.375rem; height: 40px; padding: 0 1rem; background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 8px; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); text-decoration: none; box-shadow: var(--shadow-subtle);">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                <span>Surat Diterima PDF</span>
            </a>
        @endif
        <a href="{{ route('superadmin.monitoring.index') }}" style="display: inline-flex; align-items: center; gap: 0.375rem; height: 40px; padding: 0 1rem; background: var(--neutral-200); border-radius: 8px; font-weight: 700; font-size: 0.8125rem; color: var(--neutral-800); text-decoration: none;">
            <span>&larr; Kembali</span>
        </a>
    </div>
@endsection

@push('styles')
<style>
    /* Dark Mode Overrides for SuperAdmin Monitoring Detail */
    [data-theme="dark"] div[style*="background: var(--neutral-0)"] {
        background-color: #182234 !important;
        border-color: #26354D !important;
    }

    [data-theme="dark"] div[style*="background: var(--blue-tint)"] {
        background-color: rgba(37, 99, 235, 0.15) !important;
        border-color: rgba(59, 130, 246, 0.3) !important;
    }

    [data-theme="dark"] div[style*="border: 1px solid var(--neutral-400)"],
    [data-theme="dark"] div[style*="border-bottom: 1px solid var(--neutral-400)"],
    [data-theme="dark"] div[style*="border-top: 1px solid var(--neutral-200)"],
    [data-theme="dark"] div[style*="border-bottom: 1px solid var(--neutral-200)"] {
        border-color: #26354D !important;
    }

    [data-theme="dark"] a[style*="background: var(--neutral-0)"] {
        background-color: #111C2E !important;
        border-color: #26354D !important;
        color: #F8FAFC !important;
    }

    [data-theme="dark"] a[style*="background: var(--neutral-200)"] {
        background-color: #111C2E !important;
        border-color: #26354D !important;
        color: #94A3B8 !important;
    }

    [data-theme="dark"] table thead tr {
        background-color: #141E30 !important;
        border-bottom-color: #26354D !important;
        color: #94A3B8 !important;
    }

    [data-theme="dark"] table th {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] table tbody tr {
        border-bottom-color: #26354D !important;
    }

    [data-theme="dark"] table tbody tr:hover {
        background-color: #162238 !important;
    }

    [data-theme="dark"] span[style*="color: var(--neutral-900)"],
    [data-theme="dark"] div[style*="color: var(--neutral-900)"],
    [data-theme="dark"] h2[style*="color: var(--neutral-900)"],
    [data-theme="dark"] span[style*="color: var(--neutral-800)"],
    [data-theme="dark"] h3[style*="color: var(--neutral-800)"] {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] span[style*="color: var(--neutral-600)"],
    [data-theme="dark"] div[style*="color: var(--neutral-600)"],
    [data-theme="dark"] p[style*="color: var(--neutral-700)"],
    [data-theme="dark"] p[style*="color: var(--neutral-600)"] {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] div[style*="background: var(--neutral-100)"] {
        background-color: #111C2E !important;
        border-color: #26354D !important;
    }
</style>
@endpush

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">

    <!-- 1. READ-ONLY NOTICE BANNER -->
    <x-gentelella.card style="background: var(--blue-tint); border: 1px solid var(--blue-tint-line); border-left: 4px solid var(--blue-600); padding: 1.25rem 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 40px; height: 40px; border-radius: 8px; background: var(--blue-600); color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
            </div>
            <div>
                <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--blue-700); margin: 0;">Mode Pemantauan Baca-Saja (SuperAdmin)</h4>
                <p style="font-size: 0.8125rem; color: var(--neutral-700); margin: 0.2rem 0 0 0; line-height: 1.4;">
                    Sesuai PRD 3.1 & 3.3, hak pengelolaan tugas dan penilaian berada penuh di tangan Pembimbing Lapangan. SuperAdmin memiliki hak akses supervisi terhadap seluruh progres bimbingan.
                </p>
            </div>
        </div>
    </x-gentelella.card>

    <!-- 2. PARTICIPANT OVERVIEW PROFILE CARD -->
    <x-gentelella.card style="padding: 1.75rem 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 1.25rem;">
                <div style="width: 56px; height: 56px; border-radius: 50%; background: var(--blue-tint); border: 2px solid var(--blue-tint-line); color: var(--blue-700); font-size: 1.5rem; font-weight: 800; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    {{ strtoupper(substr($penempatan->peserta?->nama ?? 'P', 0, 1)) }}
                </div>
                <div>
                    <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                        <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--neutral-900); margin: 0;">
                            {{ $penempatan->peserta?->nama }}
                        </h2>
                        <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.625rem; border-radius: 9999px; background: var(--green-100); color: var(--green-700); border: 1px solid rgba(22, 163, 74, 0.2); font-weight: 700; font-size: 0.75rem;">
                            <span style="width: 6px; height: 6px; border-radius: 50%; background: var(--green-600);"></span>
                            Magang Aktif
                        </span>
                        <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.625rem; border-radius: 9999px; background: var(--blue-tint); color: var(--blue-700); border: 1px solid var(--blue-tint-line); font-weight: 700; font-size: 0.75rem;">
                            Jalur {{ ucfirst($penempatan->jalur?->value ?? 'umum') }}
                        </span>
                    </div>
                    <p style="font-size: 0.875rem; color: var(--neutral-600); margin: 0.35rem 0 0 0;">
                        {{ $penempatan->peserta?->email }} &bull; NIM: <strong>{{ $penempatan->peserta?->profil?->nim_nisn ?? '-' }}</strong> &bull; No. HP: {{ $penempatan->peserta?->profil?->no_hp ?? '-' }}
                    </p>
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--neutral-200);">
            <div>
                <span style="font-size: 0.75rem; color: var(--neutral-500); display: block; text-transform: uppercase; font-weight: 700;">Asal Institusi</span>
                <span style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-900); margin-top: 0.2rem; display: block;">{{ $penempatan->peserta?->profil?->institusi ?? '-' }}</span>
            </div>
            <div>
                <span style="font-size: 0.75rem; color: var(--neutral-500); display: block; text-transform: uppercase; font-weight: 700;">Program Kejuruan</span>
                <span style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-900); margin-top: 0.2rem; display: block;">
                    {{ $penempatan->peserta?->profil?->jurusan?->nama_jurusan ?? ($penempatan->kuota?->jurusan?->nama_jurusan ?? '-') }}
                    ({{ strtoupper($penempatan->peserta?->profil?->jenjang?->value ?? 'S1') }})
                </span>
            </div>
            <div>
                <span style="font-size: 0.75rem; color: var(--neutral-500); display: block; text-transform: uppercase; font-weight: 700;">Pembimbing Lapangan</span>
                <span style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-900); margin-top: 0.2rem; display: block;">{{ $penempatan->pembimbing?->nama ?? 'Belum Ditentukan' }}</span>
                <small style="color: var(--blue-700); font-weight: 600;">{{ $penempatan->bidang?->nama_bidang ?? 'Bidang Umum' }}</small>
            </div>
            <div>
                <span style="font-size: 0.75rem; color: var(--neutral-500); display: block; text-transform: uppercase; font-weight: 700;">Rentang Waktu Magang</span>
                <span style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-900); margin-top: 0.2rem; display: block;">
                    {{ $penempatan->tgl_mulai ? \Carbon\Carbon::parse($penempatan->tgl_mulai)->format('d M Y') : '-' }} s/d {{ $penempatan->tgl_selesai ? \Carbon\Carbon::parse($penempatan->tgl_selesai)->format('d M Y') : '-' }}
                </span>
                <small style="color: var(--neutral-600);">Durasi: {{ $penempatan->durasi_bulan ?? 1 }} Bulan</small>
            </div>
        </div>
    </x-gentelella.card>

    <!-- 3. TAB SECTION CONTAINER -->
    <x-gentelella.x-panel title="Rekam Jejak Pemantauan Bimbingan" subtitle="Supervisi komprehensif terhadap penugasan, logbook harian, dan snapshot nilai peserta">
        <div style="padding: 0.75rem 0 1.25rem 0; border-bottom: 1px solid var(--neutral-200); display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <button type="button" onclick="switchMonitorTab('tugas')" id="tabBtnTugas" style="padding: 0.55rem 1.25rem; border-radius: 8px; font-weight: 700; font-size: 0.875rem; border: none; cursor: pointer; background: var(--blue-600); color: #FFFFFF; transition: all 0.15s;">
                Tugas & Progres ({{ $penempatan->tugas->count() }})
            </button>
            <button type="button" onclick="switchMonitorTab('logbook')" id="tabBtnLogbook" style="padding: 0.55rem 1.25rem; border-radius: 8px; font-weight: 700; font-size: 0.875rem; border: 1px solid var(--neutral-400); cursor: pointer; background: var(--neutral-0); color: var(--neutral-700); transition: all 0.15s;">
                Logbook Harian ({{ $penempatan->logbooks->count() }})
            </button>
            <button type="button" onclick="switchMonitorTab('nilai')" id="tabBtnNilai" style="padding: 0.55rem 1.25rem; border-radius: 8px; font-weight: 700; font-size: 0.875rem; border: 1px solid var(--neutral-400); cursor: pointer; background: var(--neutral-0); color: var(--neutral-700); transition: all 0.15s;">
                Penilaian & Lembar Nilai
            </button>
        </div>

        <!-- TAB CONTENT 1: TUGAS & PROGRES -->
        <div id="tabContentTugas" style="padding: 1.5rem;">
            @if($penempatan->tugas->isEmpty())
                <p style="text-align: center; color: var(--neutral-600); padding: 3rem 0; margin: 0; font-size: 0.875rem;">
                    Belum ada penugasan yang diberikan oleh pembimbing lapangan.
                </p>
            @else
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    @foreach($penempatan->tugas as $tugas)
                        @php
                            $isTerlambat = $tugas->deadline && now()->gt($tugas->deadline) && $tugas->status->value !== 'selesai';
                        @endphp
                        <div style="border: 1px solid var(--neutral-300); border-radius: 10px; padding: 1.25rem; background: var(--neutral-100); transition: border-color 0.15s ease;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem; flex-wrap: wrap; gap: 0.5rem;">
                                <div>
                                    <h4 style="font-size: 1rem; font-weight: 700; color: var(--neutral-900); margin: 0;">{{ $tugas->judul }}</h4>
                                    <span style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.15rem; display: block;">
                                        Kategori: <strong>{{ $tugas->kategori }}</strong> &bull; Kompleksitas: <strong>{{ ucfirst($tugas->kompleksitas?->value ?? 'sedang') }}</strong>
                                    </span>
                                </div>
                                <div style="display: flex; gap: 0.5rem; align-items: center;">
                                    @if($isTerlambat)
                                        <span style="display: inline-flex; align-items: center; padding: 0.2rem 0.5rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 800; background: var(--red-100); color: var(--red-700); border: 1px solid rgba(220, 38, 38, 0.2);">
                                            Terlambat
                                        </span>
                                    @endif
                                    <span style="display: inline-flex; align-items: center; padding: 0.2rem 0.625rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: var(--neutral-200); color: var(--neutral-800);">
                                        {{ strtoupper($tugas->status?->value ?? 'BELUM') }}
                                    </span>
                                </div>
                            </div>
                            <p style="font-size: 0.875rem; color: var(--neutral-700); margin: 0.5rem 0 0.75rem 0; line-height: 1.45;">
                                {{ $tugas->deskripsi ?: 'Tidak ada deskripsi rinci.' }}
                            </p>
                            <div style="font-size: 0.75rem; color: var(--neutral-600); border-top: 1px solid var(--neutral-300); padding-top: 0.625rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                                <span>Tenggat Waktu: <strong>{{ $tugas->deadline ? \Carbon\Carbon::parse($tugas->deadline)->format('d M Y') : '-' }}</strong></span>
                                <span>Total Progres Dilaporkan: <strong>{{ $tugas->progresLogs->count() }} Laporan</strong></span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- TAB CONTENT 2: LOGBOOK HARIAN -->
        <div id="tabContentLogbook" style="display: none; padding: 1.5rem;">
            @if($penempatan->logbooks->isEmpty())
                <p style="text-align: center; color: var(--neutral-600); padding: 3rem 0; margin: 0; font-size: 0.875rem;">
                    Belum ada catatan logbook harian yang diisi oleh peserta.
                </p>
            @else
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                        <thead>
                            <tr style="background: var(--neutral-100); border-bottom: 1px solid var(--neutral-400); color: var(--neutral-700); font-size: 0.75rem; text-transform: uppercase;">
                                <th style="padding: 0.75rem 1rem; font-weight: 700; width: 140px;">Tanggal</th>
                                <th style="padding: 0.75rem 1rem; font-weight: 700;">Aktivitas Harian</th>
                                <th style="padding: 0.75rem 1rem; font-weight: 700; text-align: center; width: 130px;">Status</th>
                                <th style="padding: 0.75rem 1rem; font-weight: 700; width: 220px;">Catatan Pembimbing</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($penempatan->logbooks->sortByDesc('tanggal') as $entry)
                                <tr style="border-bottom: 1px solid var(--neutral-200); transition: background 0.15s ease;" onmouseover="this.style.background='var(--neutral-100)'" onmouseout="this.style.background='transparent'">
                                    <td style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-900); font-family: monospace; font-size: 0.8125rem;">
                                        {{ \Carbon\Carbon::parse($entry->tanggal)->format('d/m/Y') }}
                                    </td>
                                    <td style="padding: 0.75rem 1rem; color: var(--neutral-800); line-height: 1.45;">
                                        {{ $entry->kegiatan }}
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: center; white-space: nowrap;">
                                        @if($entry->status?->value === 'terkirim')
                                            <span style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.75rem; font-weight: 700; padding: 0.25rem 0.625rem; border-radius: 9999px; background: var(--green-100); color: var(--green-700); border: 1px solid rgba(22, 163, 74, 0.2);">
                                                Terkirim
                                            </span>
                                        @else
                                            <span style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.75rem; font-weight: 700; padding: 0.25rem 0.625rem; border-radius: 9999px; background: var(--amber-100); color: var(--amber-700); border: 1px solid var(--amber-300);">
                                                Perlu Revisi
                                            </span>
                                        @endif
                                    </td>
                                    <td style="padding: 0.75rem 1rem; font-size: 0.8125rem; color: var(--neutral-600); line-height: 1.4;">
                                        {{ $entry->catatan_pembimbing ?: '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- TAB CONTENT 3: PENILAIAN & LEMBAR NILAI -->
        <div id="tabContentNilai" style="display: none; padding: 1.5rem;">
            @php
                $penilaian = $penempatan->penilaian;
            @endphp

            @if(!$penilaian)
                <div style="padding: 3rem 1.5rem; text-align: center; color: var(--neutral-600);">
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: var(--neutral-100); color: var(--neutral-500); display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem auto;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
                    </div>
                    <h4 style="font-size: 1rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.25rem;">Penilaian Belum Dimulai</h4>
                    <p style="font-size: 0.8125rem; margin: 0;">Pembimbing lapangan belum menginputkan nilai rubrik kompetensi untuk peserta ini.</p>
                </div>
            @else
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-200); flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <span style="font-size: 0.75rem; color: var(--neutral-500); display: block; text-transform: uppercase; font-weight: 700;">Status Penilaian</span>
                        <span style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900);">
                            {{ strtoupper($penilaian->status?->value ?? 'DRAFT') }}
                        </span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 1.5rem;">
                        <div style="text-align: right;">
                            <span style="font-size: 0.75rem; color: var(--neutral-500); display: block; text-transform: uppercase; font-weight: 700;">Nilai Akhir Terbobot</span>
                            <span style="font-size: 1.75rem; font-weight: 900; color: var(--blue-700); line-height: 1.2;">
                                {{ number_format((float) $penilaian->nilai_akhir, 2) }}
                            </span>
                        </div>
                        <div style="text-align: right;">
                            <span style="font-size: 0.75rem; color: var(--neutral-500); display: block; text-transform: uppercase; font-weight: 700;">Predikat Mutu</span>
                            <span style="font-size: 1.75rem; font-weight: 900; color: var(--green-700); line-height: 1.2;">
                                {{ $penilaian->predikat ?: 'A' }}
                            </span>
                        </div>
                        @if($penilaian->status?->value === 'final')
                            <a href="{{ route('pembimbing.penilaian.unduh', $penempatan) }}" target="_blank" style="padding: 0.55rem 1.25rem; background: var(--green-700); color: #fff; border-radius: 8px; font-weight: 700; font-size: 0.8125rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.375rem; box-shadow: var(--shadow-subtle);">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                <span>Unduh Lembar Nilai PDF</span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Detail Rubrik Table -->
                <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.75rem;">Rincian Kriteria Rubrik Penilaian:</h4>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                        <thead>
                            <tr style="background: var(--neutral-100); border-bottom: 1px solid var(--neutral-400); color: var(--neutral-700); font-size: 0.75rem; text-transform: uppercase;">
                                <th style="padding: 0.75rem 1rem; font-weight: 700;">Kriteria Kompetensi</th>
                                <th style="padding: 0.75rem 1rem; font-weight: 700; text-align: center; width: 140px;">Bobot Snapshot</th>
                                <th style="padding: 0.75rem 1rem; font-weight: 700; text-align: right; width: 140px;">Skor Pembimbing</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($penilaian->details as $d)
                                <tr style="border-bottom: 1px solid var(--neutral-200); transition: background 0.15s ease;" onmouseover="this.style.background='var(--neutral-100)'" onmouseout="this.style.background='transparent'">
                                    <td style="padding: 0.75rem 1rem; color: var(--neutral-900); font-weight: 600;">
                                        {{ $d->rubrik?->kriteria ?? 'Kriteria' }}
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: center; color: var(--neutral-700); font-weight: 600;">
                                        {{ $d->bobot_snapshot }}%
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: right; font-weight: 800; color: var(--blue-700); font-size: 1rem;">
                                        {{ number_format((float) $d->skor, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </x-gentelella.x-panel>
</div>

<script>
function switchMonitorTab(tab) {
    const tabs = ['tugas', 'logbook', 'nilai'];
    tabs.forEach(t => {
        const content = document.getElementById('tabContent' + t.charAt(0).toUpperCase() + t.slice(1));
        const btn = document.getElementById('tabBtn' + t.charAt(0).toUpperCase() + t.slice(1));
        if (t === tab) {
            content.style.display = 'block';
            btn.style.background = 'var(--blue-600)';
            btn.style.color = '#fff';
            btn.style.border = 'none';
        } else {
            content.style.display = 'none';
            btn.style.background = 'var(--neutral-0)';
            btn.style.color = 'var(--neutral-700)';
            btn.style.border = '1px solid var(--neutral-400)';
        }
    });
}
</script>
@endsection
