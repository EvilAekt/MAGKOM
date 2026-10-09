@extends('layouts.dashboard')

@section('title', 'Lembar Nilai Magang - Peserta MagKom')
@section('page_title', 'Evaluasi Nilai Akhir Magang')
@section('page_description', 'Lembar hasil penilaian kompetensi magang resmi dari Dinas Kominfo Kabupaten Sleman.')

@section('breadcrumbs')
    <span class="breadcrumb-current">Nilai Akhir Magang</span>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem; max-width: 960px; margin-inline: auto;">
    <!-- Top Action Card -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: var(--green-500);"></span>
                <span style="font-weight: 700; color: var(--green-800); font-size: 0.875rem;">Status Nilai: Resmi & Telah Difinalisasi</span>
            </div>
            <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0.25rem 0 0 0;">
                Penilaian telah disahkan oleh pembimbing lapangan Anda. Anda dapat mengunduh dokumen resmi bertandatangan digital.
            </p>
        </div>

        <a href="{{ route('peserta.nilai.unduh') }}" target="_blank" style="display: inline-flex; align-items: center; gap: 0.5rem; height: 42px; padding: 0 1.25rem; background: var(--blue-600); color: #FFFFFF; font-weight: 700; font-size: 0.875rem; text-decoration: none; border-radius: 8px; box-shadow: 0 2px 4px rgba(2, 132, 199, 0.2); transition: background 0.15s;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            Unduh Lembar Nilai PDF
        </a>
    </div>

    <!-- Official Certificate Card -->
    <div style="background: var(--neutral-0); border: 2px solid var(--neutral-300); border-radius: 16px; padding: 2.5rem; box-shadow: var(--shadow-card); position: relative; overflow: hidden;">
        <!-- Watermark / Header line -->
        <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid var(--neutral-800); padding-bottom: 1.5rem; margin-bottom: 2rem;">
            <div style="display: flex; align-items: center; gap: 1.25rem;">
                <div style="width: 56px; height: 56px; background: var(--blue-600); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #FFFFFF; font-weight: 800; font-size: 1.5rem;">
                    MK
                </div>
                <div>
                    <h2 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0; text-transform: uppercase; letter-spacing: 0.05em;">
                        DINAS KOMUNIKASI DAN INFORMATIKA
                    </h2>
                    <div style="font-size: 0.875rem; font-weight: 600; color: var(--neutral-700);">
                        KABUPATEN SLEMAN
                    </div>
                    <div style="font-size: 0.75rem; color: var(--neutral-500); margin-top: 0.25rem;">
                        Sistem Informasi Pengelolaan Magang Kominfo (MagKom)
                    </div>
                </div>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-500); text-transform: uppercase;">Nomor Dokumen</div>
                <div style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-800); font-family: monospace;">
                    MGK-{{ date('Y') }}-{{ str_pad($penempatan->id, 4, '0', STR_PAD_LEFT) }}
                </div>
            </div>
        </div>

        <!-- Grade Summary Banner -->
        <div style="background: linear-gradient(135deg, var(--blue-50) 0%, var(--neutral-100) 100%); border: 1px solid var(--blue-200); border-radius: 12px; padding: 1.75rem; display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1.5rem;">
            <div>
                <div style="font-size: 0.8125rem; font-weight: 700; color: var(--blue-800); text-transform: uppercase; letter-spacing: 0.05em;">
                    HASIL PENILAIAN AKHIR
                </div>
                <h1 style="font-size: 2.25rem; font-weight: 900; color: var(--neutral-900); margin: 0.25rem 0;">
                    {{ number_format((float)$penilaian->nilai_akhir, 2) }}
                </h1>
                <div style="display: flex; gap: 0.5rem; align-items: center;">
                    <span style="font-size: 1rem; font-weight: 800; color: var(--blue-700);">
                        Predikat {{ $penilaian->predikat->value ?? $penilaian->predikat }}
                    </span>
                    <span style="color: var(--neutral-500);">•</span>
                    <span style="font-size: 0.875rem; font-weight: 600; color: var(--neutral-700);">
                        @php
                            $predikatLabel = match($penilaian->predikat->value ?? (string)$penilaian->predikat) {
                                'A' => 'Sangat Memuaskan',
                                'B' => 'Memuaskan / Baik',
                                'C' => 'Cukup',
                                'D' => 'Kurang',
                                default => 'Sangat Kurang'
                            };
                        @endphp
                        {{ $predikatLabel }}
                    </span>
                </div>
            </div>

            <!-- Mentee Metadata Box -->
            <div style="display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.875rem; min-width: 260px; border-left: 2px solid var(--blue-200); padding-left: 1.25rem;">
                <div>
                    <span style="color: var(--neutral-600); font-size: 0.75rem; text-transform: uppercase;">Peserta Magang</span>
                    <div style="font-weight: 700; color: var(--neutral-900);">{{ $penempatan->peserta->nama ?? '-' }}</div>
                </div>
                <div>
                    <span style="color: var(--neutral-600); font-size: 0.75rem; text-transform: uppercase;">Bidang Penempatan</span>
                    <div style="font-weight: 600; color: var(--neutral-800);">{{ $penempatan->bidang->nama_bidang ?? '-' }}</div>
                </div>
                <div>
                    <span style="color: var(--neutral-600); font-size: 0.75rem; text-transform: uppercase;">Pembimbing Lapangan</span>
                    <div style="font-weight: 600; color: var(--neutral-800);">{{ $penempatan->pembimbing->nama ?? '-' }}</div>
                </div>
            </div>
        </div>

        <!-- Detailed Rubrics Table -->
        <h3 style="font-size: 1rem; font-weight: 800; color: var(--neutral-900); margin: 0 0 1rem 0; text-transform: uppercase; letter-spacing: 0.05em;">
            Rincian Nilai Berdasarkan Rubrik Kompetensi
        </h3>

        <div style="overflow-x: auto; border: 1px solid var(--neutral-300); border-radius: 8px; margin-bottom: 2rem;">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                <thead>
                    <tr style="background: var(--neutral-100); border-bottom: 1px solid var(--neutral-300); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--neutral-700);">
                        <th style="padding: 0.875rem 1rem; text-align: left; width: 40px;">No</th>
                        <th style="padding: 0.875rem 1rem; text-align: left;">Kriteria Rubrik</th>
                        <th style="padding: 0.875rem 1rem; text-align: center; width: 90px;">Bobot</th>
                        <th style="padding: 0.875rem 1rem; text-align: right; width: 110px;">Skor</th>
                        <th style="padding: 0.875rem 1.25rem; text-align: right; width: 140px;">Kontribusi Nilai</th>
                    </tr>
                </thead>
                <tbody style="divide-y: 1px solid var(--neutral-300);">
                    @foreach($penilaian->details as $index => $detail)
                        @php
                            $kontribusi = ($detail->skor * $detail->rubrik->bobot) / 100;
                        @endphp
                        <tr style="border-bottom: 1px solid var(--neutral-300);">
                            <td style="padding: 1rem; font-weight: 600; color: var(--neutral-600);">
                                {{ $index + 1 }}
                            </td>
                            <td style="padding: 1rem;">
                                <div style="font-weight: 700; color: var(--neutral-900);">
                                    {{ $detail->rubrik->nama_rubrik ?? '-' }}
                                </div>
                                <div style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.25rem;">
                                    {{ $detail->rubrik->deskripsi ?? '' }}
                                </div>
                            </td>
                            <td style="padding: 1rem; text-align: center; font-weight: 600; color: var(--neutral-700);">
                                {{ $detail->rubrik->bobot ?? 0 }}%
                            </td>
                            <td style="padding: 1rem; text-align: right; font-weight: 700; color: var(--neutral-900);">
                                {{ number_format((float)$detail->skor, 1) }}
                            </td>
                            <td style="padding: 1rem 1.25rem; text-align: right; font-weight: 800; color: var(--blue-700);">
                                {{ number_format((float)$kontribusi, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background: var(--neutral-50); border-top: 2px solid var(--neutral-400);">
                        <td colspan="4" style="padding: 1rem; font-weight: 800; font-size: 0.9375rem; color: var(--neutral-900); text-align: right;">
                            Total Nilai Akhir (Skala 100):
                        </td>
                        <td style="padding: 1rem 1.25rem; text-align: right; font-weight: 900; font-size: 1.125rem; color: var(--blue-800);">
                            {{ number_format((float)$penilaian->nilai_akhir, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        @if($penilaian->catatan)
            <div style="background: var(--neutral-50); border: 1px solid var(--neutral-300); border-radius: 8px; padding: 1.25rem; margin-bottom: 2rem;">
                <h4 style="font-size: 0.8125rem; font-weight: 700; text-transform: uppercase; color: var(--neutral-700); margin: 0 0 0.5rem 0;">
                    Catatan & Ulasan Evaluasi Pembimbing:
                </h4>
                <div style="font-size: 0.875rem; color: var(--neutral-900); line-height: 1.6; font-style: italic;">
                    "{{ $penilaian->catatan }}"
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
