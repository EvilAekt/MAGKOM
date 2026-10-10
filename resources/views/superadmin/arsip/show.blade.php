@extends('layouts.dashboard')

@section('title', 'Detail Arsip Peserta - SuperAdmin MagKom')
@section('page_title', 'Detail Arsip Alumni Magang')
@section('page_description', 'Rekam jejak historis penempatan, evaluasi nilai, dan logbook peserta (AC-12-4).')

@section('breadcrumbs')
    <a href="{{ route('superadmin.arsip.index') }}" style="color: var(--neutral-600); text-decoration: none;">Arsip Peserta</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">{{ $penempatan->peserta?->nama }}</span>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem; max-width: 1040px;">

    <!-- 1. HEADER ALUMNI PROFILE CARD -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.75rem 2rem; box-shadow: var(--shadow-subtle); display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1.25rem;">
        <div style="display: flex; align-items: center; gap: 1.25rem;">
            <div style="width: 56px; height: 56px; border-radius: 50%; background: var(--blue-tint); border: 2px solid var(--blue-tint-line); color: var(--blue-700); font-size: 1.5rem; font-weight: 800; display: flex; align-items: center; justify-content: center;">
                {{ substr($penempatan->peserta?->nama ?? 'A', 0, 1) }}
            </div>
            <div>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--neutral-900); margin: 0;">
                        {{ $penempatan->peserta?->nama }}
                    </h2>
                    <span class="role-chip-badge" style="background: {{ $penempatan->status?->value === 'selesai' ? 'var(--sky-100)' : 'var(--neutral-200)' }}; color: {{ $penempatan->status?->value === 'selesai' ? 'var(--blue-700)' : 'var(--neutral-700)' }}; font-weight: 700;">
                        {{ $penempatan->status?->label() }}
                    </span>
                </div>
                <div style="font-size: 0.875rem; color: var(--neutral-600); margin-top: 0.25rem;">
                    {{ $penempatan->peserta?->email }} &bull; NIM: {{ $penempatan->peserta?->profil?->nim_nisn ?? '-' }}
                </div>
                <div style="font-size: 0.8125rem; color: var(--blue-700); font-weight: 600; margin-top: 0.125rem;">
                    {{ $penempatan->peserta?->profil?->institusi ?? '-' }} &bull; Jenjang: {{ strtoupper($penempatan->peserta?->profil?->jenjang?->value ?? '-') }}
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 0.75rem;">
            @if($penempatan->penilaian && $penempatan->penilaian->status?->value === 'final')
                <a href="{{ route('pembimbing.penilaian.unduh', $penempatan) }}" target="_blank"
                   style="display: inline-flex; align-items: center; gap: 0.5rem; height: 40px; padding: 0 1.25rem; background: var(--blue-600); color: #FFFFFF; font-weight: 700; font-size: 0.875rem; text-decoration: none; border-radius: 8px; box-shadow: var(--shadow-subtle);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    <span>Unduh Lembar Nilai Resmi</span>
                </a>
            @endif
            <a href="{{ route('superadmin.arsip.index') }}"
               style="display: inline-flex; align-items: center; gap: 0.375rem; height: 40px; padding: 0 1rem; background: var(--neutral-200); color: var(--neutral-800); font-weight: 600; font-size: 0.875rem; text-decoration: none; border-radius: 8px;">
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <!-- 2. DETAIL PENEMPATAN & PEMBIMBING GRID -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">
        <!-- Card Riwayat Magang -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
            <h3 style="font-size: 0.9375rem; font-weight: 800; color: var(--neutral-900); border-bottom: 1px solid var(--neutral-300); padding-bottom: 0.625rem; margin-bottom: 1rem;">
                Informasi Penempatan Magang
            </h3>

            <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.875rem;">
                <div>
                    <span style="color: var(--neutral-600); display: block; font-size: 0.75rem;">Jurusan & Unit Dinas:</span>
                    <strong>{{ $penempatan->kuota?->jurusan?->nama_jurusan ?? ($penempatan->slotKhusus ? 'Jalur Khusus Mitra' : '-') }}</strong>
                    <span style="color: var(--neutral-700);">({{ $penempatan->bidang?->nama_bidang ?? 'Bidang Belum Ditentukan' }})</span>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <span style="color: var(--neutral-600); display: block; font-size: 0.75rem;">Tanggal Mulai:</span>
                        <strong>{{ $penempatan->tgl_mulai?->translatedFormat('d F Y') }}</strong>
                    </div>
                    <div>
                        <span style="color: var(--neutral-600); display: block; font-size: 0.75rem;">Tanggal Selesai:</span>
                        <strong>{{ $penempatan->tgl_selesai?->translatedFormat('d F Y') }}</strong>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <span style="color: var(--neutral-600); display: block; font-size: 0.75rem;">Jalur Penerimaan:</span>
                        <strong>{{ $penempatan->jalur?->label() }}</strong>
                    </div>
                    <div>
                        <span style="color: var(--neutral-600); display: block; font-size: 0.75rem;">Sumber Penempatan:</span>
                        <strong>{{ $penempatan->sumber?->label() }}</strong>
                    </div>
                </div>

                <div>
                    <span style="color: var(--neutral-600); display: block; font-size: 0.75rem;">Status Akun & Jadwal Nonaktif:</span>
                    <span>Ditetapkan selesai pada {{ $penempatan->tgl_ditetapkan_selesai?->translatedFormat('d F Y') ?? '-' }}</span>
                    @if($penempatan->tgl_nonaktif)
                        <br><small style="color: var(--neutral-600);">Batas Nonaktif Akun: {{ $penempatan->tgl_nonaktif->translatedFormat('d F Y') }}</small>
                    @endif
                </div>
            </div>
        </div>

        <!-- Card Pembimbing Lapangan -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
            <h3 style="font-size: 0.9375rem; font-weight: 800; color: var(--neutral-900); border-bottom: 1px solid var(--neutral-300); padding-bottom: 0.625rem; margin-bottom: 1rem;">
                Pembimbing Lapangan
            </h3>

            @if($penempatan->pembimbing)
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                    <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--neutral-200); display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--neutral-700);">
                        {{ substr($penempatan->pembimbing->nama, 0, 1) }}
                    </div>
                    <div>
                        <h4 style="font-size: 0.9375rem; font-weight: 700; margin: 0; color: var(--neutral-900);">{{ $penempatan->pembimbing->nama }}</h4>
                        <div style="font-size: 0.8125rem; color: var(--neutral-600);">{{ $penempatan->pembimbing->email }}</div>
                    </div>
                </div>
            @else
                <p style="color: var(--neutral-600); font-size: 0.875rem;">Belum ada data pembimbing.</p>
            @endif

            <div style="background: var(--neutral-100); border: 1px solid var(--neutral-300); border-radius: 8px; padding: 0.875rem; font-size: 0.8125rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                    <span style="color: var(--neutral-700);">Tugas Diberikan:</span>
                    <strong>{{ $penempatan->tugas->count() }} Tugas</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                    <span style="color: var(--neutral-700);">Tugas Selesai:</span>
                    <strong style="color: var(--green-700);">{{ $penempatan->tugas->where('status.value', 'selesai')->count() }} Selesai</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--neutral-700);">Logbook Harian Terisi:</span>
                    <strong>{{ $penempatan->logbooks->count() }} Entri</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. EVALUASI NILAI FINAL -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--neutral-300); padding-bottom: 0.75rem; margin-bottom: 1.25rem;">
            <h3 style="font-size: 1rem; font-weight: 800; color: var(--neutral-900); margin: 0;">
                Rincian Evaluasi Penilaian Kompetensi
            </h3>
            @if($penempatan->penilaian)
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="font-size: 0.875rem; font-weight: 600; color: var(--neutral-700);">Nilai Akhir:</div>
                    <div style="font-size: 1.5rem; font-weight: 900; color: var(--blue-700);">{{ $penempatan->penilaian->nilai_akhir }}</div>
                    <span class="role-chip-badge" style="background: var(--green-100); color: var(--green-800); border: 1px solid var(--green-600); font-weight: 800; font-size: 0.875rem;">
                        Predikat {{ $penempatan->penilaian->predikat }}
                    </span>
                </div>
            @endif
        </div>

        @if($penempatan->penilaian && $penempatan->penilaian->details->isNotEmpty())
            <div style="overflow-x: auto; border: 1px solid var(--neutral-300); border-radius: 8px;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem; text-align: left;">
                    <thead style="background: var(--neutral-100); border-bottom: 1px solid var(--neutral-300);">
                        <tr>
                            <th style="padding: 0.75rem 1rem;">No.</th>
                            <th style="padding: 0.75rem 1rem;">Kriteria Rubrik Penilaian</th>
                            <th style="padding: 0.75rem 1rem; text-align: center;">Bobot (%)</th>
                            <th style="padding: 0.75rem 1rem; text-align: center;">Skor (0-100)</th>
                            <th style="padding: 0.75rem 1rem; text-align: center;">Kontribusi Skor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($penempatan->penilaian->details as $index => $detail)
                            @php
                                $kontribusi = ($detail->skor * $detail->bobot) / 100;
                            @endphp
                            <tr style="border-bottom: 1px solid var(--neutral-200);">
                                <td style="padding: 0.75rem 1rem; color: var(--neutral-600);">{{ $index + 1 }}</td>
                                <td style="padding: 0.75rem 1rem; font-weight: 600;">{{ $detail->rubrik?->kriteria ?? '-' }}</td>
                                <td style="padding: 0.75rem 1rem; text-align: center;">{{ $detail->bobot }}%</td>
                                <td style="padding: 0.75rem 1rem; text-align: center; font-weight: 700; color: var(--blue-700);">{{ $detail->skor }}</td>
                                <td style="padding: 0.75rem 1rem; text-align: center; font-weight: 700; color: var(--green-700);">{{ number_format($kontribusi, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($penempatan->penilaian->catatan)
                <div style="margin-top: 1rem; background: var(--neutral-100); border: 1px solid var(--neutral-300); border-radius: 8px; padding: 1rem; font-size: 0.875rem;">
                    <strong style="color: var(--neutral-800); display: block; margin-bottom: 0.25rem;">Catatan Pembimbing:</strong>
                    <p style="margin: 0; color: var(--neutral-700);">{{ $penempatan->penilaian->catatan }}</p>
                </div>
            @endif
        @else
            <p style="color: var(--neutral-600); font-size: 0.875rem; text-align: center; padding: 1.5rem;">
                Belum ada rincian penilaian yang tercatat untuk peserta ini.
            </p>
        @endif
    </div>

</div>
@endsection
