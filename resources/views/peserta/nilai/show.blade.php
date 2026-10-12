@extends('layouts.peserta')

@section('title', 'Lembar Evaluasi Nilai Magang')
@section('page_title', 'Lembar Nilai Magang')

@push('styles')
<style>
    .nilai-summary-card {
        background: linear-gradient(135deg, var(--blue-700) 0%, var(--blue-600) 100%);
        border-radius: var(--radius-md);
        padding: 32px;
        color: #ffffff;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }
    .nilai-score-box {
        display: flex;
        align-items: baseline;
        gap: 16px;
    }
    .score-number {
        font-size: 54px;
        font-weight: 800;
        line-height: 1;
    }
    .grade-badge {
        font-size: 24px;
        font-weight: 800;
        padding: 4px 18px;
        background: rgba(255, 255, 255, 0.2);
        border: 2px solid rgba(255, 255, 255, 0.4);
        border-radius: var(--radius-default);
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
        padding: 14px 16px;
        font-size: 13px;
        font-weight: 700;
        color: var(--neutral-800);
        border-bottom: 1px solid var(--neutral-400);
    }
    .table td {
        padding: 16px;
        font-size: 14px;
        border-bottom: 1px solid var(--neutral-200);
        color: var(--neutral-800);
    }
    .table tr.total-row td {
        background: var(--neutral-100);
        font-weight: 700;
        font-size: 15px;
    }

    /* Dark Mode Harmonization ala Menu Tugas */
    [data-theme="dark"] .table th {
        background: #0F172A !important;
        color: #94A3B8 !important;
        border-bottom-color: #26354D !important;
    }

    [data-theme="dark"] .table td {
        border-bottom-color: #26354D !important;
        color: #E2E8F0 !important;
    }

    [data-theme="dark"] .table tr.total-row td {
        background: #111C2E !important;
        color: #F8FAFC !important;
    }
</style>
@endpush

@section('content')
<div style="max-width: 960px; margin: 0 auto;">

    @if(!$penempatan || !$isFinal)
        {{-- State Belum Ada Penilaian Final --}}
        <div class="card" style="text-align: center; padding: 48px 24px;">
            <div style="width: 64px; height: 64px; border-radius: 50%; background: var(--amber-100); color: var(--amber-700); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto;">
                <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 style="font-size: 18px; font-weight: 700; color: var(--neutral-800); margin-bottom: 8px;">
                Lembar Nilai Belum Tersedia
            </h3>
            <p style="font-size: 14px; color: var(--neutral-600); max-width: 480px; margin: 0 auto 20px auto; line-height: 1.5;">
                Evaluasi nilai resmi akan dirilis setelah periode penempatan magang Anda selesai dan pembimbing lapangan telah melakukan finalisasi penginputan 5 rubrik penilaian magang (AC-10-2).
            </p>
            @if($penempatan)
                <div style="display: inline-block; padding: 10px 18px; background: var(--neutral-100); border-radius: var(--radius-default); font-size: 13px; color: var(--neutral-700);">
                    Status Penempatan: <strong>{{ ucfirst($penempatan->status->value ?? (string)$penempatan->status) }}</strong>
                    | Pembimbing: <strong>{{ $penempatan->pembimbing?->name ?? 'Belum ditentukan' }}</strong>
                </div>
            @endif
        </div>
    @else
        {{-- State Nilai Sudah Final --}}
        <div class="nilai-summary-card">
            <div>
                <span style="font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em; opacity: 0.9;">
                    Evaluasi Resmi Akhir Magang Kominfo
                </span>
                <h2 style="font-size: 24px; font-weight: 800; margin-top: 4px; color: #ffffff;">
                    Hasil Penilaian Akhir
                </h2>
                <p style="font-size: 14px; opacity: 0.85; margin-top: 4px;">
                    Bidang: <strong>{{ $penempatan->bidang?->nama_bidang }}</strong> | Evaluator: <strong>{{ $penempatan->pembimbing?->name }}</strong>
                </p>
            </div>

            <div style="display: flex; align-items: center; gap: 20px;">
                <div class="nilai-score-box">
                    <div>
                        <div style="font-size: 11px; text-transform: uppercase; opacity: 0.8;">Nilai Akhir</div>
                        <div class="score-number">{{ number_format($penilaian->nilai_akhir, 2) }}</div>
                    </div>
                    <div>
                        <div style="font-size: 11px; text-transform: uppercase; opacity: 0.8;">Predikat</div>
                        <div class="grade-badge">{{ $penilaian->predikat }}</div>
                    </div>
                </div>

                <a href="{{ route('peserta.nilai.unduh') }}" class="btn" style="background: #ffffff; color: var(--blue-700); height: 44px; padding: 0 20px; font-weight: 700; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Unduh PDF Resmi
                </a>
            </div>
        </div>

        {{-- Tabel Rincian 5 Rubrik Penilaian --}}
        <div class="card" style="padding: 0; overflow: hidden; margin-bottom: 24px;">
            <div style="padding: 20px 24px; border-bottom: 1px solid var(--neutral-200);">
                <h3 class="card-title">Rincian 5 Rubrik Penilaian</h3>
                <p class="card-subtitle">Snapshot bobot dan skor masing-masing kompetensi evaluasi peserta.</p>
            </div>

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Kriteria Evaluasi</th>
                            <th style="width: 120px; text-align: center;">Bobot (%)</th>
                            <th style="width: 120px; text-align: center;">Skor (0-100)</th>
                            <th style="width: 140px; text-align: right;">Nilai Terbobot</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalBobot = 0;
                            $totalTertimbang = 0;
                        @endphp
                        @forelse($penilaian->details as $index => $detail)
                            @php
                                $totalBobot += $detail->bobot;
                                $terbobot = ($detail->skor * $detail->bobot) / 100;
                                $totalTertimbang += $terbobot;
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $detail->rubrik?->nama_kriteria ?? 'Kriteria Penilaian ' . ($index + 1) }}</strong>
                                    @if($detail->rubrik?->deskripsi)
                                        <div style="font-size: 12px; color: var(--neutral-600); margin-top: 2px;">
                                            {{ $detail->rubrik->deskripsi }}
                                        </div>
                                    @endif
                                </td>
                                <td style="text-align: center;">{{ $detail->bobot }}%</td>
                                <td style="text-align: center; font-weight: 700;">{{ number_format($detail->skor, 1) }}</td>
                                <td style="text-align: right; font-weight: 600; color: var(--blue-700);">
                                    {{ number_format($terbobot, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--neutral-600);">
                                    Tidak ada rincian rubrik penilaian yang tersimpan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td colspan="2" style="text-align: right;">Total Evaluasi Kumulatif:</td>
                            <td style="text-align: center;">{{ $totalBobot }}%</td>
                            <td style="text-align: center;">-</td>
                            <td style="text-align: right; color: var(--blue-700);">
                                {{ number_format($penilaian->nilai_akhir, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Catatan Evaluasi Pembimbing --}}
        @if($penilaian->catatan)
            <div class="card">
                <h4 style="font-size: 14px; font-weight: 700; color: var(--neutral-700); text-transform: uppercase; margin-bottom: 8px;">
                    Catatan & Ulasan Pembimbing Lapangan:
                </h4>
                <div style="font-size: 14px; color: var(--neutral-800); line-height: 1.6; white-space: pre-line; padding: 14px; background: var(--neutral-100); border-radius: var(--radius-default); border-left: 3px solid var(--blue-600);">
                    {{ $penilaian->catatan }}
                </div>
            </div>
        @endif
    @endif

</div>
@endsection
