@extends('layouts.peserta')

@section('title', 'Logbook Harian Peserta')
@section('page_title', 'Jurnal & Logbook Harian')

@push('styles')
<style>
    .logbook-grid {
        display: grid;
        grid-template-columns: 1fr 1.6fr;
        gap: 24px;
        align-items: start;
    }
    .table-container {
        overflow-x: auto;
    }
    .table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }
    .table th {
        background: var(--neutral-200);
        padding: 12px 14px;
        font-size: 13px;
        font-weight: 700;
        color: var(--neutral-800);
        border-bottom: 1px solid var(--neutral-400);
    }
    .table td {
        padding: 14px;
        font-size: 13px;
        border-bottom: 1px solid var(--neutral-200);
        color: var(--neutral-800);
        vertical-align: top;
    }
    .table tr:hover td {
        background: var(--neutral-100);
    }
    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 16px;
    }
    .form-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--neutral-800);
    }
    .form-control {
        height: 42px;
        padding: 0 14px;
        border: 1px solid var(--neutral-500);
        border-radius: var(--radius-default);
        font-size: 14px;
        color: var(--neutral-800);
        background: #ffffff;
    }
    .form-control:focus {
        outline: none;
        border-color: var(--blue-600);
        box-shadow: 0 0 0 3px rgba(7, 81, 255, 0.2);
    }
    textarea.form-control {
        height: auto;
        padding: 10px 14px;
    }

    @media (max-width: 1024px) {
        .logbook-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div>
    @if(!$penempatan)
        <div class="card" style="border-left: 4px solid var(--amber-700); background: var(--amber-100);">
            <div style="display: flex; align-items: flex-start; gap: 16px;">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--amber-700); flex-shrink: 0; margin-top: 2px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                <div>
                    <h3 style="font-size: 16px; font-weight: 700; color: var(--amber-700); margin-bottom: 4px;">
                        Menu Logbook Harian Terkunci
                    </h3>
                    <p style="font-size: 14px; color: var(--neutral-700);">
                        Anda belum memiliki penempatan magang aktif. Fitur pengisian logbook harian ini akan terbuka secara otomatis setelah pendaftaran magang Anda berstatus <strong>Diterima</strong> dan masa penempatan magang telah dimulai (AC-09-1).
                    </p>
                </div>
            </div>
        </div>
    @else
        {{-- Banner Info Penempatan Aktif --}}
        <div class="card" style="margin-bottom: 24px; background: linear-gradient(to right, #ffffff, var(--blue-tint));">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                <div>
                    <span class="badge badge-success">● Penempatan Magang Aktif</span>
                    <h2 style="font-size: 20px; font-weight: 700; color: var(--neutral-800); margin-top: 6px;">
                        Bidang: {{ $penempatan->bidang?->nama_bidang ?? 'Dinas Kominfo Sleman' }}
                    </h2>
                    <p style="font-size: 13px; color: var(--neutral-600); margin-top: 2px;">
                        Pembimbing Lapangan: <strong>{{ $penempatan->pembimbing?->name ?? 'Belum Ditentukan' }}</strong> |
                        Periode: {{ \Carbon\Carbon::parse($penempatan->tgl_mulai)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($penempatan->tgl_selesai)->translatedFormat('d M Y') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="logbook-grid">
            {{-- Form Tambah Entri Logbook --}}
            <div class="card">
                <div class="card-header">
                    <div>
                        <h3 class="card-title">Isi Jurnal Aktivitas</h3>
                        <p class="card-subtitle">Catat ringkasan pekerjaan atau kegiatan yang Anda lakukan hari ini.</p>
                    </div>
                </div>

                <form action="{{ route('peserta.logbook.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label for="tanggal" class="form-label">Tanggal Kegiatan <span style="color: var(--red-600);">*</span></label>
                        <input type="date" id="tanggal" name="tanggal"
                               class="form-control @error('tanggal') is-invalid @enderror"
                               value="{{ old('tanggal', now()->toDateString()) }}"
                               max="{{ now()->toDateString() }}"
                               required>
                        @error('tanggal')
                            <span style="font-size: 12px; color: var(--red-600);">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="kegiatan" class="form-label">Deskripsi & Rincian Kegiatan <span style="color: var(--red-600);">*</span></label>
                        <textarea id="kegiatan" name="kegiatan" rows="4"
                                  class="form-control @error('kegiatan') is-invalid @enderror"
                                  placeholder="Jelaskan secara ringkas kegiatan, tugas, atau pembelajaran yang dikerjakan..." required>{{ old('kegiatan') }}</textarea>
                        @error('kegiatan')
                            <span style="font-size: 12px; color: var(--red-600);">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="file_laporan" class="form-label">Lampiran Berkas (PDF, Opsional)</label>
                        <input type="file" id="file_laporan" name="file_laporan" accept=".pdf"
                               class="form-control" style="padding-top: 8px;">
                        <span style="font-size: 12px; color: var(--neutral-600);">Format PDF, maksimal 5MB.</span>
                        @error('file_laporan')
                            <span style="font-size: 12px; color: var(--red-600);">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="link_laporan" class="form-label">Tautan Eksternal (Opsional)</label>
                        <input type="url" id="link_laporan" name="link_laporan"
                               class="form-control @error('link_laporan') is-invalid @enderror"
                               value="{{ old('link_laporan') }}"
                               placeholder="https://github.com/... atau https://drive.google.com/...">
                        <span style="font-size: 12px; color: var(--neutral-600);">Harus diawali https://</span>
                        @error('link_laporan')
                            <span style="font-size: 12px; color: var(--red-600);">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                        Simpan Jurnal Harian
                    </button>
                </form>
            </div>

            {{-- Tabel Riwayat Logbook --}}
            <div class="card" style="padding: 0; overflow: hidden;">
                <div style="padding: 18px 20px; border-bottom: 1px solid var(--neutral-200);">
                    <h3 class="card-title">Riwayat Jurnal Logbook</h3>
                    <p class="card-subtitle">Daftar entri aktivitas harian dan status verifikasi pembimbing.</p>
                </div>

                @if($logbooks->isEmpty())
                    <div style="text-align: center; padding: 40px 20px;">
                        <p style="font-size: 14px; color: var(--neutral-600);">Belum ada catatan logbook yang disimpan.</p>
                    </div>
                @else
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 110px;">Tanggal</th>
                                    <th>Aktivitas</th>
                                    <th style="width: 110px;">Status</th>
                                    <th style="width: 90px; text-align: right;">Lampiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($logbooks as $log)
                                    <tr>
                                        <td>
                                            <strong>{{ $log->tanggal->translatedFormat('d M Y') }}</strong>
                                            <div style="font-size: 11px; color: var(--neutral-600);">
                                                {{ $log->tanggal->translatedFormat('l') }}
                                            </div>
                                        </td>
                                        <td>
                                            <p style="white-space: pre-line; line-height: 1.4;">{{ $log->kegiatan }}</p>
                                            @if($log->catatan_review)
                                                <div style="margin-top: 8px; padding: 8px 10px; background: var(--amber-100); border-left: 3px solid var(--amber-700); border-radius: 4px; font-size: 12px; color: var(--amber-700);">
                                                    <strong>Catatan Pembimbing:</strong> {{ $log->catatan_review }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $statusLog = $log->status->value ?? (string)$log->status;
                                            @endphp
                                            @if($statusLog === 'perlu_revisi')
                                                <span class="badge badge-danger">Perlu Revisi</span>
                                            @elseif($statusLog === 'terkirim')
                                                <span class="badge badge-info">Terkirim</span>
                                            @else
                                                <span class="badge badge-success">{{ $statusLog }}</span>
                                            @endif
                                        </td>
                                        <td style="text-align: right;">
                                            @if($log->file_laporan)
                                                <a href="{{ route('peserta.logbook.lampiran', $log) }}" target="_blank" class="btn btn-secondary" style="height: 30px; padding: 0 8px; font-size: 11px;" title="Unduh File PDF">
                                                    PDF ↗
                                                </a>
                                            @endif
                                            @if($log->link_laporan)
                                                <a href="{{ $log->link_laporan }}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary" style="height: 30px; padding: 0 8px; font-size: 11px; margin-top: 4px;" title="Buka Link">
                                                    Link ↗
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($logbooks->hasPages())
                        <div style="padding: 14px 20px; border-top: 1px solid var(--neutral-200);">
                            {{ $logbooks->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
