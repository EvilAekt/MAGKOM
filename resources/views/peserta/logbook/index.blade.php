@extends('layouts.dashboard')

@section('title', 'Logbook Harian - Peserta MagKom Sleman')
@section('page_title', 'Logbook Kegiatan Harian')
@section('page_description', 'Dokumentasikan aktivitas dan tugas kerja harian Anda selama program magang di Dinas Kominfo.')

@section('breadcrumbs')
    <span class="breadcrumb-current">Logbook Harian</span>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">

    @if(!$penempatan)
        <div style="background: var(--amber-100); border: 1px solid var(--amber-600); border-radius: 12px; padding: 1.25rem 1.5rem; display: flex; align-items: center; gap: 1rem;">
            <div style="width: 40px; height: 40px; background: var(--amber-200); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--amber-800); flex-shrink: 0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
            </div>
            <div>
                <h3 style="font-size: 1rem; font-weight: 700; color: var(--amber-900); margin: 0 0 0.25rem 0;">Penempatan Belum Aktif</h3>
                <p style="font-size: 0.8125rem; color: var(--amber-800); margin: 0;">
                    Anda belum memiliki status penempatan magang aktif. Fitur logbook harian dapat diakses setelah pendaftaran Anda diterima dan ditempatkan oleh dinas.
                </p>
            </div>
        </div>
    @else
        <!-- Metadata Penempatan & Pembimbing -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <span style="font-size: 0.75rem; color: var(--neutral-600); text-transform: uppercase; font-weight: 700;">Unit Penempatan</span>
                <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-800); margin: 0.125rem 0 0 0;">
                    {{ $penempatan->bidang?->nama_bidang ?? 'Dinas Komunikasi dan Informatika' }}
                </h3>
            </div>
            <div>
                <span style="font-size: 0.75rem; color: var(--neutral-600); text-transform: uppercase; font-weight: 700;">Pembimbing Lapangan</span>
                <p style="font-size: 0.9375rem; font-weight: 700; color: var(--blue-700); margin: 0.125rem 0 0 0;">
                    {{ $penempatan->pembimbing?->nama ?? 'Belum Ditetapkan' }}
                </p>
            </div>
            <div>
                <span style="font-size: 0.75rem; color: var(--neutral-600); text-transform: uppercase; font-weight: 700;">Batas Waktu Isi Mundur</span>
                <p style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-800); margin: 0.125rem 0 0 0;">
                    Maks. {{ $logbookMaksMundur }} Hari Kerja (BR-09)
                </p>
            </div>
        </div>

        <!-- Form Catat Logbook Harian -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
            <h3 style="font-size: 1.0625rem; font-weight: 700; color: var(--neutral-800); margin: 0 0 0.25rem 0;">
                Tambah Catatan Kegiatan Harian
            </h3>
            <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0 0 1.25rem 0;">
                Laporkan tugas, proyek, dan keterampilan yang Anda pelajari pada tanggal kegiatan bersangkutan.
            </p>

            <form method="POST" action="{{ route('peserta.logbook.store') }}" enctype="multipart/form-data">
                @csrf
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
                    <!-- Tanggal Kegiatan -->
                    <div style="display: flex; flex-direction: column; gap: 0.375rem;">
                        <label for="tanggal" style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">
                            Tanggal Kegiatan <span style="color: var(--red-600);">*</span>
                        </label>
                        @php
                            $minDate = now('Asia/Jakarta')->subDays($logbookMaksMundur)->toDateString();
                            $maxDate = now('Asia/Jakarta')->toDateString();
                        @endphp
                        <input type="date" id="tanggal" name="tanggal" required value="{{ old('tanggal', $maxDate) }}" min="{{ $minDate }}" max="{{ $maxDate }}" style="height: 44px; border: 1px solid var(--neutral-500); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                        <small style="font-size: 0.75rem; color: var(--neutral-600);">Pengisian mundur maksimal {{ $logbookMaksMundur }} hari kalender.</small>
                    </div>

                    <!-- Lampiran PDF -->
                    <div style="display: flex; flex-direction: column; gap: 0.375rem;">
                        <label for="file_laporan" style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">
                            Lampiran PDF (Opsional, Maks 2 MB)
                        </label>
                        <input type="file" id="file_laporan" name="file_laporan" accept=".pdf" style="height: 44px; border: 1px solid var(--neutral-500); border-radius: 8px; padding: 0.4rem 0.75rem; font-size: 0.8125rem;">
                    </div>

                    <!-- Link Tautan Eksternal -->
                    <div style="display: flex; flex-direction: column; gap: 0.375rem; grid-column: 1 / -1;">
                        <label for="link_laporan" style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">
                            Tautan Hasil Kerja / Repository / Drive (Opsional)
                        </label>
                        <input type="url" id="link_laporan" name="link_laporan" value="{{ old('link_laporan') }}" placeholder="https://github.com/..." style="height: 44px; border: 1px solid var(--neutral-500); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                    </div>
                </div>

                <!-- Deskripsi Kegiatan -->
                <div style="display: flex; flex-direction: column; gap: 0.375rem; margin-bottom: 1.25rem;">
                    <label for="kegiatan" style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">
                        Uraian Aktivitas & Hasil Pekerjaan <span style="color: var(--red-600);">*</span>
                    </label>
                    <textarea id="kegiatan" name="kegiatan" required rows="4" placeholder="Jelaskan secara terperinci apa yang Anda kerjakan, kendala yang dihadapi, dan hasil yang dicapai..." style="border: 1px solid var(--neutral-500); border-radius: 8px; padding: 0.75rem; font-size: 0.875rem; font-family: inherit;">{{ old('kegiatan') }}</textarea>
                </div>

                <div style="display: flex; justify-content: flex-end;">
                    <button type="submit" style="height: 44px; padding: 0 1.5rem; background: var(--blue-600); color: #FFFFFF; font-weight: 600; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="22" y1="2" x2="11" y2="13"></line>
                            <polyline points="22 2 15 22 11 13 2 9 22 2"></polyline>
                        </svg>
                        <span>Simpan & Kirim Logbook</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Tabel Riwayat Logbook -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-subtle);">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-400); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-800); margin: 0;">Riwayat Catatan Harian</h3>
            <span style="font-size: 0.8125rem; color: var(--neutral-600);">Total: {{ $logbooks->total() }} Catatan</span>
        </div>

        @if($logbooks->isEmpty())
            <div style="padding: 3.5rem 1.5rem; text-align: center; color: var(--neutral-600);">
                <div style="width: 56px; height: 56px; background: var(--neutral-200); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto; color: var(--neutral-500);">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                </div>
                <h4 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">Belum Ada Catatan Logbook</h4>
                <p style="font-size: 0.875rem; max-width: 460px; margin: 0 auto;">Isi catatan kegiatan harian pertama Anda menggunakan form di atas.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                    <thead>
                        <tr style="background: var(--neutral-200); border-bottom: 1px solid var(--neutral-400); height: 52px;">
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800); width: 140px;">Tanggal</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Uraian Kegiatan</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Lampiran</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Status Reviu</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Catatan Pembimbing</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logbooks as $log)
                            @php
                                $statusVal = is_object($log->status) ? $log->status->value : $log->status;
                            @endphp
                            <tr style="border-bottom: 1px solid var(--neutral-400); height: 60px;">
                                <td style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">
                                    {{ \Carbon\Carbon::parse($log->tanggal)->translatedFormat('d M Y') }}
                                </td>
                                <td style="padding: 0.75rem 1rem; color: var(--neutral-800); max-width: 380px;">
                                    <div style="line-height: 1.45;">{{ $log->kegiatan }}</div>
                                </td>
                                <td style="padding: 0.75rem 1rem;">
                                    <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                                        @if($log->file_laporan)
                                            <a href="{{ route('peserta.logbook.lampiran', $log) }}" target="_blank" style="color: var(--blue-700); font-size: 0.75rem; font-weight: 600; text-decoration: underline; display: inline-flex; align-items: center; gap: 0.25rem;">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                                <span>Unduh PDF</span>
                                            </a>
                                        @endif
                                        @if($log->link_laporan)
                                            <a href="{{ $log->link_laporan }}" target="_blank" rel="noopener noreferrer" style="color: var(--blue-700); font-size: 0.75rem; font-weight: 600; text-decoration: underline;">
                                                Tautan Kerja ↗
                                            </a>
                                        @endif
                                        @if(!$log->file_laporan && !$log->link_laporan)
                                            <span style="color: var(--neutral-500); font-size: 0.75rem;">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td style="padding: 0.75rem 1rem;">
                                    <span class="role-chip-badge" style="background: {{ $statusVal === 'perlu_revisi' ? 'var(--amber-200)' : 'var(--green-100)' }}; color: {{ $statusVal === 'perlu_revisi' ? 'var(--amber-800)' : 'var(--green-700)' }}; font-weight: 700;">
                                        {{ $statusVal === 'perlu_revisi' ? 'Perlu Revisi' : 'Terkirim' }}
                                    </span>
                                </td>
                                <td style="padding: 0.75rem 1rem; font-size: 0.8125rem; color: var(--neutral-700);">
                                    {{ $log->catatan_pembimbing ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($logbooks->hasPages())
                <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--neutral-400);">
                    {{ $logbooks->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
