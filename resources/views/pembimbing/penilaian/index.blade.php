@extends('layouts.dashboard')

@section('title', 'Penilaian Magang - Pembimbing MagKom')
@section('page_title', 'Evaluasi & Penilaian Magang')
@section('page_description', 'Kelola instrumen penilaian rubrik kompetensi untuk seluruh peserta magang bimbingan Anda.')

@section('breadcrumbs')
    <span class="breadcrumb-current">Penilaian Magang</span>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <!-- Info Banner -->
    <div style="background: var(--blue-50); border: 1px solid var(--blue-200); border-radius: 12px; padding: 1.25rem 1.5rem; display: flex; gap: 1rem; align-items: flex-start;">
        <div style="width: 36px; height: 36px; border-radius: 8px; background: var(--blue-600); color: #FFFFFF; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 20h9"></path>
                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
            </svg>
        </div>
        <div>
            <h3 style="font-size: 0.9375rem; font-weight: 700; color: var(--blue-900); margin: 0 0 0.25rem 0;">Petunjuk Evaluasi Rubrik (AC-10 & AC-12)</h3>
            <p style="font-size: 0.875rem; color: var(--blue-800); margin: 0; line-height: 1.5;">
                Penilaian dihitung secara otomatis berdasarkan 5 rubrik kompetensi aktif. Anda dapat menyimpan draf sementara, memfinalisasi nilai, melakukan revisi tercatat (audit log), dan menerbitkan Lembar Nilai Magang resmi berformat PDF.
            </p>
        </div>
    </div>

    <!-- Data Table Card -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-subtle);">
        @if($penempatanList->isEmpty())
            <div style="padding: 4rem 1.5rem; text-align: center; color: var(--neutral-600);">
                <div style="width: 64px; height: 64px; background: var(--neutral-200); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem auto; color: var(--neutral-500);">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
                <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-900); margin: 0 0 0.5rem 0;">Belum Ada Peserta Bimbingan</h3>
                <p style="font-size: 0.875rem; color: var(--neutral-600); margin: 0;">
                    Saat ini Anda belum memiliki peserta magang aktif yang ditempatkan pada bimbingan Anda.
                </p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem; text-align: left;">
                    <thead>
                        <tr style="background: var(--neutral-100); border-bottom: 1px solid var(--neutral-400); color: var(--neutral-700); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <th style="padding: 1rem 1.25rem; font-weight: 700;">Peserta Magang</th>
                            <th style="padding: 1rem 1rem; font-weight: 700;">Institusi / Jurusan</th>
                            <th style="padding: 1rem 1rem; font-weight: 700;">Bidang Penempatan</th>
                            <th style="padding: 1rem 1rem; font-weight: 700;">Status Evaluasi</th>
                            <th style="padding: 1rem 1rem; font-weight: 700;">Skor & Predikat</th>
                            <th style="padding: 1rem 1.25rem; font-weight: 700; text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody style="divide-y: 1px solid var(--neutral-300);">
                        @foreach($penempatanList as $item)
                            @php
                                $penilaian = $item->penilaian;
                                $statusNilai = $penilaian ? $penilaian->status->value : 'belum';
                                $badgeBg = match($statusNilai) {
                                    'final' => 'var(--green-50)',
                                    'draft' => 'var(--yellow-50)',
                                    default => 'var(--neutral-200)'
                                };
                                $badgeText = match($statusNilai) {
                                    'final' => 'var(--green-800)',
                                    'draft' => 'var(--yellow-800)',
                                    default => 'var(--neutral-700)'
                                };
                                $badgeLabel = match($statusNilai) {
                                    'final' => 'Final (Selesai)',
                                    'draft' => 'Draf Tersimpan',
                                    default => 'Belum Dinilai'
                                };
                            @endphp
                            <tr style="border-bottom: 1px solid var(--neutral-300); transition: background 0.15s;" onmouseover="this.style.background='var(--neutral-100)'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 1.125rem 1.25rem;">
                                    <div style="font-weight: 700; color: var(--neutral-900); font-size: 0.9375rem;">
                                        {{ $item->peserta->nama ?? '-' }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--neutral-600);">
                                        {{ $item->peserta->email ?? '-' }}
                                    </div>
                                </td>
                                <td style="padding: 1.125rem 1rem;">
                                    <div style="font-weight: 600; color: var(--neutral-800);">
                                        {{ $item->peserta->profil->institusi ?? ($item->peserta->profil->asal_instansi ?? '-') }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--neutral-600);">
                                        {{ $item->peserta->profil->jurusan?->nama_jurusan ?? ($item->peserta->profil->jurusan ?? '-') }}
                                    </div>
                                </td>
                                <td style="padding: 1.125rem 1rem; white-space: nowrap;">
                                    <span style="font-weight: 600; color: var(--neutral-800);">
                                        {{ $item->bidang->nama_bidang ?? '-' }}
                                    </span>
                                </td>
                                <td style="padding: 1.125rem 1rem; white-space: nowrap;">
                                    <span style="display: inline-block; padding: 0.25rem 0.625rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: {{ $badgeBg }}; color: {{ $badgeText }};">
                                        {{ $badgeLabel }}
                                    </span>
                                </td>
                                <td style="padding: 1.125rem 1rem; white-space: nowrap;">
                                    @if($penilaian && $penilaian->nilai_akhir !== null)
                                        <div style="display: flex; align-items: baseline; gap: 0.375rem;">
                                            <span style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900);">
                                                {{ number_format((float)$penilaian->nilai_akhir, 1) }}
                                            </span>
                                            <span style="font-size: 0.8125rem; font-weight: 700; color: var(--blue-700);">
                                                ({{ $penilaian->predikat->value ?? $penilaian->predikat }})
                                            </span>
                                        </div>
                                    @else
                                        <span style="color: var(--neutral-500); font-size: 0.8125rem;">-</span>
                                    @endif
                                </td>
                                <td style="padding: 1.125rem 1.25rem; text-align: right; white-space: nowrap;">
                                    <div style="display: inline-flex; gap: 0.5rem; align-items: center;">
                                        @if($penilaian && $penilaian->status->value === 'final')
                                            <a href="{{ route('pembimbing.penilaian.unduh', $item) }}" target="_blank" title="Unduh Lembar Nilai PDF" style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border: 1px solid var(--neutral-400); background: var(--neutral-0); color: var(--neutral-700); border-radius: 6px; text-decoration: none; transition: background 0.15s;">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                    <polyline points="7 10 12 15 17 10"></polyline>
                                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                                </svg>
                                            </a>
                                        @endif
                                        <a href="{{ route('pembimbing.penilaian.show', $item) }}" style="display: inline-flex; align-items: center; gap: 0.375rem; height: 36px; padding: 0 0.875rem; background: var(--blue-600); color: #FFFFFF; font-weight: 600; font-size: 0.8125rem; text-decoration: none; border-radius: 6px; transition: background 0.15s;">
                                            {{ $penilaian ? 'Buka Penilaian' : 'Input Nilai' }}
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="9 18 15 12 9 6"></polyline>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($penempatanList->hasPages())
                <div style="padding: 1rem 1.25rem; border-top: 1px solid var(--neutral-300);">
                    {{ $penempatanList->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
