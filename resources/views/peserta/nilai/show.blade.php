@extends('layouts.peserta')

@section('title', 'Lembar Evaluasi Nilai Magang')
@section('page_title', 'Lembar Nilai Magang')

@push('styles')
<style>
    .nilai-summary-card {
        background: linear-gradient(135deg, #1b3860 0%, #162e4f 100%);
        border-radius: 16px;
        padding: 32px;
        color: #ffffff;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        box-shadow: 0 4px 20px -2px rgba(27, 56, 96, 0.18);
        border: 1px solid rgba(255, 255, 255, 0.12);
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

    /* Dark Mode Harmonization */
    [data-theme="dark"] .nilai-summary-card {
        background: linear-gradient(135deg, #1E3A8A 0%, #172554 100%) !important;
        border: 1px solid rgba(59, 130, 246, 0.3) !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4) !important;
    }

    [data-theme="dark"] .nilai-summary-card .btn-unduh-nilai {
        background: #182234 !important;
        color: #60A5FA !important;
        border: 1px solid rgba(59, 130, 246, 0.4) !important;
    }

    [data-theme="dark"] .nilai-summary-card .btn-unduh-nilai:hover {
        background: #1E293B !important;
        color: #93C5FD !important;
    }

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

    [data-theme="dark"] .catatan-pembimbing-box {
        background: #0F172A !important;
        border-left-color: #3B82F6 !important;
        color: #E2E8F0 !important;
    }
</style>
@endpush

@section('content')
<div style="max-width: 960px; margin: 0 auto;">

    <x-gentelella.page-title
        title="Lembar Evaluasi Nilai Magang"
        description="Hasil evaluasi kompetensi akhir dan rincian skor 5 rubrik penilaian resmi kedinasan."
    />

    @if(!$penempatan || !$isFinal)
        {{-- State Belum Ada Penilaian Final --}}
        <x-gentelella.card style="text-align: center; padding: 48px 24px;">
            <div style="width: 64px; height: 64px; border-radius: 50%; background: var(--amber-100, #FEF3C7); color: var(--amber-700, #B45309); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto;">
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
        </x-gentelella.card>
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

            <div style="display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
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

                <a href="{{ route('peserta.nilai.unduh') }}" class="btn btn-unduh-nilai" style="background: #ffffff; color: var(--blue-700); height: 44px; padding: 0 20px; font-weight: 700; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Unduh PDF Resmi
                </a>
            </div>
        </div>

        {{-- Tabel Rincian 5 Rubrik Penilaian --}}
        <x-gentelella.card
            title="Rincian 5 Rubrik Penilaian"
            subtitle="Snapshot bobot dan skor masing-masing kompetensi evaluasi peserta."
            class="mb-4"
        >
            <x-slot:icon>
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="text-primary">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
            </x-slot:icon>

            <div class="table-container" style="margin: -10px -20px -20px -20px;">
                <table class="table" style="margin-bottom: 0;">
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
                                $bobot = $detail->bobot_snapshot ?? $detail->bobot ?? $detail->rubrik?->bobot ?? 0;
                                $totalBobot += $bobot;
                                $terbobot = ($detail->skor * $bobot) / 100;
                                $totalTertimbang += $terbobot;
                                $kriteria = $detail->rubrik?->kriteria ?? $detail->rubrik?->nama_kriteria ?? ('Kriteria Penilaian ' . ($index + 1));
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $kriteria }}</strong>
                                    @if($detail->rubrik?->deskripsi)
                                        <div style="font-size: 12px; color: var(--neutral-600); margin-top: 2px;">
                                             {{ $detail->rubrik->deskripsi }}
                                        </div>
                                    @endif
                                </td>
                                <td style="text-align: center;">{{ $bobot }}%</td>
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
        </x-gentelella.card>

        {{-- Catatan Evaluasi Pembimbing --}}
        @if($penilaian->catatan)
            <x-gentelella.callout
                type="info"
                title="Catatan & Ulasan Pembimbing Lapangan"
            >
                <div class="catatan-pembimbing-box" style="font-size: 14px; line-height: 1.6; white-space: pre-line; color: inherit;">
                    "{{ $penilaian->catatan }}"
                </div>
            </x-gentelella.callout>
        @endif
    @endif

</div>
@endsection
