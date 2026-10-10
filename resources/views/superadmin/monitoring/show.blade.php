@extends('layouts.dashboard')

@section('title', 'Detail Pemantauan Peserta - SuperAdmin MagKom')
@section('page_title', 'Detail Pemantauan Bimbingan Peserta Aktif')
@section('page_description', 'Tinjau rekam jejak bimbingan, tugas, logbook harian, dan snapshot nilai peserta aktif secara baca-saja (AC-07-7, AC-08-5).')

@section('breadcrumbs')
    <a href="{{ route('superadmin.monitoring.index') }}" style="color: var(--neutral-600); text-decoration: none;">Monitoring Peserta</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">{{ $penempatan->peserta?->nama }}</span>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">

    <!-- Read-Only Notice Banner -->
    <div style="background: var(--sky-100); border: 1px solid var(--blue-tint-line); border-radius: 12px; padding: 1rem 1.25rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <div style="width: 36px; height: 36px; border-radius: 50%; background: var(--blue-600); color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
            </div>
            <div>
                <h4 style="font-size: 0.875rem; font-weight: 700; color: var(--blue-700); margin: 0;">Mode Pemantauan Baca-Saja (SuperAdmin)</h4>
                <p style="font-size: 0.8125rem; color: var(--neutral-700); margin: 0.125rem 0 0 0;">
                    Sesuai PRD 3.1 & 3.3, hak pengelolaan tugas dan penilaian berada penuh di tangan Pembimbing Lapangan. SuperAdmin memiliki hak akses pantau seluruh data secara baca-saja.
                </p>
            </div>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            @if($penempatan->pendaftaran_id)
                <a href="{{ route('admin.penempatan.surat-diterima.pratinjau', $penempatan) }}" target="_blank" style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.45rem 0.875rem; background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 6px; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); text-decoration: none;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                    <span>Surat Diterima PDF</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Participant Overview Profile Card -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
            <div>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--neutral-800); margin: 0;">
                        {{ $penempatan->peserta?->nama }}
                    </h2>
                    <span class="role-chip-badge" style="background: var(--green-100); color: var(--green-800); border: 1px solid var(--green-600); font-weight: 700;">
                        Magang Aktif
                    </span>
                    <span class="role-chip-badge" style="background: var(--blue-50); color: var(--blue-700); border: 1px solid var(--blue-600); font-weight: 700;">
                        Jalur {{ ucfirst($penempatan->jalur?->value ?? 'umum') }}
                    </span>
                </div>
                <p style="font-size: 0.875rem; color: var(--neutral-600); margin: 0.25rem 0 0 0;">
                    {{ $penempatan->peserta?->email }} · NIM: {{ $penempatan->peserta?->profil?->nim_nisn ?? '-' }} · No. HP: {{ $penempatan->peserta?->profil?->no_hp ?? '-' }}
                </p>
            </div>
            <a href="{{ route('superadmin.monitoring.index') }}" style="padding: 0.45rem 0.875rem; background: var(--neutral-200); border-radius: 6px; font-weight: 600; font-size: 0.8125rem; color: var(--neutral-700); text-decoration: none;">
                &larr; Kembali ke Daftar
            </a>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 1.25rem; padding-top: 1.25rem; border-top: 1px solid var(--neutral-300);">
            <div>
                <span style="font-size: 0.75rem; color: var(--neutral-600); display: block;">Asal Institusi</span>
                <span style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-800);">{{ $penempatan->peserta?->profil?->institusi ?? '-' }}</span>
            </div>
            <div>
                <span style="font-size: 0.75rem; color: var(--neutral-600); display: block;">Program Kejuruan</span>
                <span style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-800);">
                    {{ $penempatan->peserta?->profil?->jurusan?->nama_jurusan ?? ($penempatan->kuota?->jurusan?->nama_jurusan ?? '-') }}
                    ({{ strtoupper($penempatan->peserta?->profil?->jenjang?->value ?? 'S1') }})
                </span>
            </div>
            <div>
                <span style="font-size: 0.75rem; color: var(--neutral-600); display: block;">Pembimbing Lapangan</span>
                <span style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-800);">{{ $penempatan->pembimbing?->nama ?? 'Belum Ditentukan' }}</span>
            </div>
            <div>
                <span style="font-size: 0.75rem; color: var(--neutral-600); display: block;">Rentang Waktu Magang</span>
                <span style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-800);">
                    {{ $penempatan->tgl_mulai ? \Carbon\Carbon::parse($penempatan->tgl_mulai)->format('d M Y') : '-' }} s/d {{ $penempatan->tgl_selesai ? \Carbon\Carbon::parse($penempatan->tgl_selesai)->format('d M Y') : '-' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Tab Section Container -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-subtle);">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-400); background: var(--neutral-150); display: flex; gap: 1rem;">
            <button type="button" onclick="switchMonitorTab('tugas')" id="tabBtnTugas" style="padding: 0.5rem 1rem; border-radius: 6px; font-weight: 700; font-size: 0.875rem; border: none; cursor: pointer; background: var(--blue-600); color: #fff;">
                Tugas & Progres ({{ $penempatan->tugas->count() }})
            </button>
            <button type="button" onclick="switchMonitorTab('logbook')" id="tabBtnLogbook" style="padding: 0.5rem 1rem; border-radius: 6px; font-weight: 600; font-size: 0.875rem; border: 1px solid var(--neutral-400); cursor: pointer; background: var(--neutral-0); color: var(--neutral-700);">
                Logbook Harian ({{ $penempatan->logbooks->count() }})
            </button>
            <button type="button" onclick="switchMonitorTab('nilai')" id="tabBtnNilai" style="padding: 0.5rem 1rem; border-radius: 6px; font-weight: 600; font-size: 0.875rem; border: 1px solid var(--neutral-400); cursor: pointer; background: var(--neutral-0); color: var(--neutral-700);">
                Penilaian & Lembar Nilai
            </button>
        </div>

        <!-- TAB CONTENT 1: TUGAS & PROGRES -->
        <div id="tabContentTugas" style="padding: 1.5rem;">
            @if($penempatan->tugas->isEmpty())
                <p style="text-align: center; color: var(--neutral-600); padding: 2rem 0; margin: 0;">Belum ada penugasan yang diberikan oleh pembimbing lapangan.</p>
            @else
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    @foreach($penempatan->tugas as $tugas)
                        @php
                            $isTerlambat = $tugas->deadline && now()->gt($tugas->deadline) && $tugas->status->value !== 'selesai';
                        @endphp
                        <div style="border: 1px solid var(--neutral-300); border-radius: 8px; padding: 1.25rem; background: var(--neutral-150);">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                                <div>
                                    <h4 style="font-size: 1rem; font-weight: 700; color: var(--neutral-800); margin: 0;">{{ $tugas->judul }}</h4>
                                    <span style="font-size: 0.75rem; color: var(--neutral-600);">Kategori: {{ $tugas->kategori }} · Kompleksitas: {{ ucfirst($tugas->kompleksitas?->value ?? 'sedang') }}</span>
                                </div>
                                <div style="display: flex; gap: 0.5rem; align-items: center;">
                                    @if($isTerlambat)
                                        <span class="role-chip-badge" style="background: var(--red-100); color: var(--red-800); border: 1px solid var(--red-600); font-weight: 700;">
                                            Terlambat
                                        </span>
                                    @endif
                                    <span class="role-chip-badge" style="background: var(--neutral-200); color: var(--neutral-800); font-weight: 600;">
                                        {{ strtoupper($tugas->status?->value ?? 'BELUM') }}
                                    </span>
                                </div>
                            </div>
                            <p style="font-size: 0.875rem; color: var(--neutral-700); margin: 0 0 0.75rem 0;">{{ $tugas->deskripsi ?: 'Tidak ada deskripsi rinci.' }}</p>
                            <div style="font-size: 0.75rem; color: var(--neutral-600); border-top: 1px solid var(--neutral-300); padding-top: 0.5rem; display: flex; justify-content: space-between;">
                                <span>Tenggat Waktu: <strong>{{ $tugas->deadline ? \Carbon\Carbon::parse($tugas->deadline)->format('d M Y') : '-' }}</strong></span>
                                <span>Total Progres Dilaporkan: <strong>{{ $tugas->progresLogs->count() }}</strong></span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- TAB CONTENT 2: LOGBOOK HARIAN -->
        <div id="tabContentLogbook" style="display: none; padding: 1.5rem;">
            @if($penempatan->logbooks->isEmpty())
                <p style="text-align: center; color: var(--neutral-600); padding: 2rem 0; margin: 0;">Belum ada catatan logbook harian yang diisi oleh peserta.</p>
            @else
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                        <thead>
                            <tr style="background: var(--neutral-200); border-bottom: 1px solid var(--neutral-400); height: 44px;">
                                <th style="padding: 0.5rem 1rem; font-weight: 700; color: var(--neutral-800); width: 140px;">Tanggal</th>
                                <th style="padding: 0.5rem 1rem; font-weight: 700; color: var(--neutral-800);">Aktivitas Harian</th>
                                <th style="padding: 0.5rem 1rem; font-weight: 700; color: var(--neutral-800); text-align: center; width: 120px;">Status</th>
                                <th style="padding: 0.5rem 1rem; font-weight: 700; color: var(--neutral-800); width: 200px;">Catatan Pembimbing</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($penempatan->logbooks->sortByDesc('tanggal') as $entry)
                                <tr style="border-bottom: 1px solid var(--neutral-300); height: 50px;">
                                    <td style="padding: 0.5rem 1rem; font-weight: 700; color: var(--neutral-800);">
                                        {{ \Carbon\Carbon::parse($entry->tanggal)->format('d/m/Y') }}
                                    </td>
                                    <td style="padding: 0.5rem 1rem; color: var(--neutral-700);">
                                        {{ $entry->kegiatan }}
                                    </td>
                                    <td style="padding: 0.5rem 1rem; text-align: center;">
                                        @if($entry->status?->value === 'terkirim')
                                            <span style="font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 4px; background: var(--green-100); color: var(--green-800);">Terkirim</span>
                                        @else
                                            <span style="font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 4px; background: var(--amber-100); color: var(--amber-800);">Perlu Revisi</span>
                                        @endif
                                    </td>
                                    <td style="padding: 0.5rem 1rem; font-size: 0.8125rem; color: var(--neutral-600);">
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
                <div style="padding: 2.5rem; text-align: center; color: var(--neutral-600);">
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: var(--neutral-200); color: var(--neutral-500); display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem auto;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
                    </div>
                    <h4 style="font-size: 1rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.25rem;">Penilaian Belum Dimulai</h4>
                    <p style="font-size: 0.8125rem; margin: 0;">Pembimbing lapangan belum menginputkan nilai rubrik kompetensi untuk peserta ini.</p>
                </div>
            @else
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; padding-bottom: 1rem; border-bottom: 1px solid var(--neutral-300); flex-wrap: wrap; gap: 0.75rem;">
                    <div>
                        <span style="font-size: 0.75rem; color: var(--neutral-600); display: block;">Status Penilaian</span>
                        <span style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-800);">
                            {{ strtoupper($penilaian->status?->value ?? 'DRAFT') }}
                        </span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 1.5rem;">
                        <div style="text-align: right;">
                            <span style="font-size: 0.75rem; color: var(--neutral-600); display: block;">Nilai Akhir Terbobot</span>
                            <span style="font-size: 1.5rem; font-weight: 800; color: var(--blue-700);">
                                {{ number_format((float) $penilaian->nilai_akhir, 2) }}
                            </span>
                        </div>
                        <div style="text-align: right;">
                            <span style="font-size: 0.75rem; color: var(--neutral-600); display: block;">Predikat Mutu</span>
                            <span style="font-size: 1.5rem; font-weight: 800; color: var(--green-700);">
                                {{ $penilaian->predikat ?: 'A' }}
                            </span>
                        </div>
                        @if($penilaian->status?->value === 'final')
                            <a href="{{ route('pembimbing.penilaian.unduh', $penempatan) }}" target="_blank" style="padding: 0.5rem 1rem; background: var(--green-600); color: #fff; border-radius: 6px; font-weight: 700; font-size: 0.8125rem; text-decoration: none;">
                                Unduh Lembar Nilai PDF
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Detail Rubrik Table -->
                <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.75rem;">Rincian Kriteria Rubrik Penilaian:</h4>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                        <thead>
                            <tr style="background: var(--neutral-200); border-bottom: 1px solid var(--neutral-400); height: 40px;">
                                <th style="padding: 0.5rem 1rem; font-weight: 700; color: var(--neutral-800);">Kriteria Kompetensi</th>
                                <th style="padding: 0.5rem 1rem; font-weight: 700; color: var(--neutral-800); text-align: center; width: 120px;">Bobot Snapshot</th>
                                <th style="padding: 0.5rem 1rem; font-weight: 700; color: var(--neutral-800); text-align: right; width: 120px;">Skor Pembimbing</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($penilaian->details as $d)
                                <tr style="border-bottom: 1px solid var(--neutral-300); height: 44px;">
                                    <td style="padding: 0.5rem 1rem; color: var(--neutral-800); font-weight: 600;">
                                        {{ $d->rubrik?->kriteria ?? 'Kriteria' }}
                                    </td>
                                    <td style="padding: 0.5rem 1rem; text-align: center; color: var(--neutral-700);">
                                        {{ $d->bobot_snapshot }}%
                                    </td>
                                    <td style="padding: 0.5rem 1rem; text-align: right; font-weight: 700; color: var(--blue-700);">
                                        {{ number_format((float) $d->skor, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

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
