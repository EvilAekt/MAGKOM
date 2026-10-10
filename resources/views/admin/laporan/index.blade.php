@extends('layouts.dashboard')

@section('title', 'Hub Laporan & Ekspor - Admin MagKom')
@section('page_title', 'Hub Laporan & Ekspor Data')
@section('page_description', 'Rekapitulasi berkas pendaftaran, alokasi kuota, penempatan, dan evaluasi peserta magang (AC-17-1, AC-17-2).')

@section('breadcrumbs')
    <span class="breadcrumb-current">Laporan & Ekspor</span>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">

    <!-- 1. SUMMARY METRICS CARDS -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem;">
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem; box-shadow: var(--shadow-subtle);">
            <span style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-600); text-transform: uppercase;">Total Pengajuan</span>
            <div style="font-size: 1.75rem; font-weight: 900; color: var(--blue-700); margin-top: 0.25rem;">
                {{ number_format($metricCards['total_pendaftaran']) }}
            </div>
            <small style="color: var(--neutral-600); font-size: 0.75rem;">Semua status pendaftaran</small>
        </div>

        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem; box-shadow: var(--shadow-subtle);">
            <span style="font-size: 0.75rem; font-weight: 700; color: var(--green-700); text-transform: uppercase;">Peserta Diterima</span>
            <div style="font-size: 1.75rem; font-weight: 900; color: var(--green-700); margin-top: 0.25rem;">
                {{ number_format($metricCards['total_diterima']) }}
            </div>
            <small style="color: var(--neutral-600); font-size: 0.75rem;">Lolos seleksi berkas & kuota</small>
        </div>

        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem; box-shadow: var(--shadow-subtle);">
            <span style="font-size: 0.75rem; font-weight: 700; color: var(--blue-600); text-transform: uppercase;">Penempatan Aktif</span>
            <div style="font-size: 1.75rem; font-weight: 900; color: var(--blue-600); margin-top: 0.25rem;">
                {{ number_format($metricCards['penempatan_aktif']) }}
            </div>
            <small style="color: var(--neutral-600); font-size: 0.75rem;">Sedang menjalani bimbingan</small>
        </div>

        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem; box-shadow: var(--shadow-subtle);">
            <span style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase;">Magang Selesai</span>
            <div style="font-size: 1.75rem; font-weight: 900; color: var(--neutral-800); margin-top: 0.25rem;">
                {{ number_format($metricCards['penempatan_selesai']) }}
            </div>
            <small style="color: var(--neutral-600); font-size: 0.75rem;">Alumni & evaluasi final</small>
        </div>

        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem; box-shadow: var(--shadow-subtle);">
            <span style="font-size: 0.75rem; font-weight: 700; color: var(--amber-700); text-transform: uppercase;">Realisasi Kuota Umum</span>
            <div style="font-size: 1.75rem; font-weight: 900; color: var(--amber-700); margin-top: 0.25rem;">
                {{ number_format($metricCards['kuota_terisi']) }}
            </div>
            <small style="color: var(--neutral-600); font-size: 0.75rem;">Kursi terisi jalur online</small>
        </div>

        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem; box-shadow: var(--shadow-subtle);">
            <span style="font-size: 0.75rem; font-weight: 700; color: var(--purple-700, #7E22CE); text-transform: uppercase;">Slot Khusus Terisi</span>
            <div style="font-size: 1.75rem; font-weight: 900; color: var(--purple-700, #7E22CE); margin-top: 0.25rem;">
                {{ number_format($metricCards['slot_terisi']) }}
            </div>
            <small style="color: var(--neutral-600); font-size: 0.75rem;">Jalur kemitraan internal</small>
        </div>
    </div>

    <!-- 2. TAB SWITCHER -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 0.5rem; box-shadow: var(--shadow-subtle); display: flex; gap: 0.5rem; flex-wrap: wrap;">
        <a href="{{ route('admin.laporan.index', ['tab' => 'pendaftaran']) }}"
           style="flex: 1; min-width: 160px; padding: 0.625rem 1rem; border-radius: 8px; text-decoration: none; font-size: 0.875rem; font-weight: 700; text-align: center; transition: all 0.15s;
           background: {{ $tab === 'pendaftaran' ? 'var(--blue-600)' : 'transparent' }};
           color: {{ $tab === 'pendaftaran' ? '#FFFFFF' : 'var(--neutral-700)' }};">
            Rekap Pendaftaran
        </a>

        <a href="{{ route('admin.laporan.index', ['tab' => 'penempatan']) }}"
           style="flex: 1; min-width: 160px; padding: 0.625rem 1rem; border-radius: 8px; text-decoration: none; font-size: 0.875rem; font-weight: 700; text-align: center; transition: all 0.15s;
           background: {{ $tab === 'penempatan' ? 'var(--blue-600)' : 'transparent' }};
           color: {{ $tab === 'penempatan' ? '#FFFFFF' : 'var(--neutral-700)' }};">
            Data Penempatan
        </a>

        <a href="{{ route('admin.laporan.index', ['tab' => 'kuota']) }}"
           style="flex: 1; min-width: 160px; padding: 0.625rem 1rem; border-radius: 8px; text-decoration: none; font-size: 0.875rem; font-weight: 700; text-align: center; transition: all 0.15s;
           background: {{ $tab === 'kuota' ? 'var(--blue-600)' : 'transparent' }};
           color: {{ $tab === 'kuota' ? '#FFFFFF' : 'var(--neutral-700)' }};">
            Realisasi Kuota & Slot
        </a>

        @if($isSuperAdmin)
            <a href="{{ route('admin.laporan.index', ['tab' => 'nilai']) }}"
               style="flex: 1; min-width: 180px; padding: 0.625rem 1rem; border-radius: 8px; text-decoration: none; font-size: 0.875rem; font-weight: 700; text-align: center; transition: all 0.15s;
               background: {{ $tab === 'nilai' ? 'var(--blue-700)' : 'transparent' }};
               color: {{ $tab === 'nilai' ? '#FFFFFF' : 'var(--neutral-700)' }}; display: flex; align-items: center; justify-content: center; gap: 0.375rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
                <span>Rekap Nilai (SuperAdmin)</span>
            </a>
        @endif
    </div>

    <!-- 3. TAB 1: REKAP PENDAFTARAN -->
    @if($tab === 'pendaftaran')
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;">
                <div>
                    <h2 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0 0 0.25rem 0;">Laporan Rekapitulasi Pendaftaran</h2>
                    <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0;">Data calon peserta pendaftaran magang berdasarkan periode bulan dan status keputusan.</p>
                </div>

                <div style="display: flex; gap: 0.5rem; align-items: center;">
                    <a href="{{ route('admin.laporan.pendaftaran', request()->all()) }}"
                       style="display: inline-flex; align-items: center; gap: 0.5rem; height: 40px; padding: 0 1.25rem; background: var(--green-600); color: #FFFFFF; font-weight: 700; font-size: 0.875rem; text-decoration: none; border-radius: 8px; box-shadow: var(--shadow-subtle);">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        <span>Ekspor CSV Sanitasi</span>
                    </a>
                </div>
            </div>

            <!-- Toolbar Filter -->
            <form method="GET" action="{{ route('admin.laporan.index') }}" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; background: var(--neutral-100); padding: 1rem; border-radius: 8px; margin-bottom: 1.25rem; border: 1px solid var(--neutral-300);">
                <input type="hidden" name="tab" value="pendaftaran">

                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <label style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700);">Periode Bulan Magang</label>
                    <select name="bulan_magang_id" style="height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.8125rem; background: #FFFFFF;">
                        <option value="">Semua Periode Bulan</option>
                        @foreach($bulanMagangList as $bm)
                            <option value="{{ $bm->id }}" {{ request('bulan_magang_id') == $bm->id ? 'selected' : '' }}>
                                {{ $bm->bulan_mulai?->translatedFormat('F Y') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <label style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700);">Status Keputusan</label>
                    <select name="status" style="height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.8125rem; background: #FFFFFF;">
                        <option value="">Semua Status</option>
                        @foreach($statusPendaftaran as $sp)
                            <option value="{{ $sp->value }}" {{ request('status') == $sp->value ? 'selected' : '' }}>
                                {{ $sp->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <label style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700);">Jurusan</label>
                    <select name="jurusan_id" style="height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.8125rem; background: #FFFFFF;">
                        <option value="">Semua Jurusan</option>
                        @foreach($jurusanList as $jur)
                            <option value="{{ $jur->id }}" {{ request('jurusan_id') == $jur->id ? 'selected' : '' }}>
                                {{ $jur->nama_jurusan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <label style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700);">Dari Tanggal</label>
                    <input type="date" name="dari_tanggal" value="{{ request('dari_tanggal') }}" style="height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.5rem; font-size: 0.8125rem; background: #FFFFFF;">
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <label style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700);">Sampai Tanggal</label>
                    <input type="date" name="sampai_tanggal" value="{{ request('sampai_tanggal') }}" style="height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.5rem; font-size: 0.8125rem; background: #FFFFFF;">
                </div>

                <div style="display: flex; align-items: flex-end; gap: 0.5rem; margin-top: 1rem;">
                    <button type="submit" style="height: 38px; padding: 0 1.25rem; background: var(--blue-600); color: #FFFFFF; font-weight: 600; font-size: 0.8125rem; border: none; border-radius: 6px; cursor: pointer;">
                        Terapkan Filter
                    </button>
                    @if(request()->hasAny(['bulan_magang_id', 'status', 'jurusan_id', 'dari_tanggal', 'sampai_tanggal']))
                        <a href="{{ route('admin.laporan.index', ['tab' => 'pendaftaran']) }}" style="font-size: 0.8125rem; color: var(--neutral-600); text-decoration: none; padding: 0.5rem;">Reset</a>
                    @endif
                </div>
            </form>

            <!-- Table Preview -->
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem; text-align: left;">
                    <thead>
                        <tr style="background: var(--neutral-100); border-bottom: 1px solid var(--neutral-400); color: var(--neutral-700); font-size: 0.75rem; text-transform: uppercase;">
                            <th style="padding: 0.75rem 1rem;">No. Registrasi</th>
                            <th style="padding: 0.75rem 1rem;">Nama & Institusi</th>
                            <th style="padding: 0.75rem 1rem;">Jurusan Pilihan</th>
                            <th style="padding: 0.75rem 1rem;">Periode Mulai</th>
                            <th style="padding: 0.75rem 1rem;">Durasi</th>
                            <th style="padding: 0.75rem 1rem;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendaftaranData as $p)
                            <tr style="border-bottom: 1px solid var(--neutral-300);">
                                <td style="padding: 0.75rem 1rem; font-weight: 700; color: var(--blue-700);">
                                    {{ $p->nomor_pendaftaran }}
                                </td>
                                <td style="padding: 0.75rem 1rem;">
                                    <div style="font-weight: 600;">{{ $p->user?->nama }}</div>
                                    <small style="color: var(--neutral-600);">{{ $p->asal_instansi ?? ($p->user?->profil?->institusi ?? '-') }}</small>
                                </td>
                                <td style="padding: 0.75rem 1rem;">
                                    {{ $p->kuota?->jurusan?->nama_jurusan ?? '-' }}
                                </td>
                                <td style="padding: 0.75rem 1rem;">
                                    {{ $p->bulanMagang?->bulan_mulai?->translatedFormat('F Y') }}
                                </td>
                                <td style="padding: 0.75rem 1rem;">
                                    {{ $p->durasi_bulan }} Bulan
                                </td>
                                <td style="padding: 0.75rem 1rem;">
                                    <span class="role-chip-badge" style="background: var(--neutral-200); color: var(--neutral-800); border: 1px solid var(--neutral-400);">
                                        {{ $p->status?->label() }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding: 2.5rem; text-align: center; color: var(--neutral-600);">
                                    Tidak ada data pendaftaran yang sesuai dengan filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($pendaftaranData->hasPages())
                <div style="margin-top: 1rem;">
                    {{ $pendaftaranData->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- 4. TAB 2: DATA PENEMPATAN -->
    @if($tab === 'penempatan')
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;">
                <div>
                    <h2 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0 0 0.25rem 0;">Laporan Data Penempatan Magang</h2>
                    <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0;">Rekapitulasi penempatan aktif, selesai, serta pembagian pembimbing dan jalur magang.</p>
                </div>

                <div style="display: flex; gap: 0.5rem; align-items: center;">
                    <a href="{{ route('admin.laporan.penempatan', request()->all()) }}"
                       style="display: inline-flex; align-items: center; gap: 0.5rem; height: 40px; padding: 0 1.25rem; background: var(--green-600); color: #FFFFFF; font-weight: 700; font-size: 0.875rem; text-decoration: none; border-radius: 8px; box-shadow: var(--shadow-subtle);">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        <span>Ekspor CSV Sanitasi</span>
                    </a>
                </div>
            </div>

            <!-- Toolbar Filter Penempatan -->
            <form method="GET" action="{{ route('admin.laporan.index') }}" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; background: var(--neutral-100); padding: 1rem; border-radius: 8px; margin-bottom: 1.25rem; border: 1px solid var(--neutral-300);">
                <input type="hidden" name="tab" value="penempatan">

                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <label style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700);">Status Penempatan</label>
                    <select name="status" style="height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.8125rem; background: #FFFFFF;">
                        <option value="">Semua Status</option>
                        @foreach($statusPenempatan as $st)
                            <option value="{{ $st->value }}" {{ request('status') == $st->value ? 'selected' : '' }}>
                                {{ $st->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <label style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700);">Jalur Penerimaan</label>
                    <select name="jalur" style="height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.8125rem; background: #FFFFFF;">
                        <option value="">Semua Jalur</option>
                        <option value="umum" {{ request('jalur') == 'umum' ? 'selected' : '' }}>Jalur Umum (Online)</option>
                        <option value="khusus" {{ request('jalur') == 'khusus' ? 'selected' : '' }}>Jalur Khusus (Slot Internal)</option>
                    </select>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <label style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700);">Jurusan</label>
                    <select name="jurusan_id" style="height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.8125rem; background: #FFFFFF;">
                        <option value="">Semua Jurusan</option>
                        @foreach($jurusanList as $jur)
                            <option value="{{ $jur->id }}" {{ request('jurusan_id') == $jur->id ? 'selected' : '' }}>
                                {{ $jur->nama_jurusan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <label style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700);">Mulai Dari Tanggal</label>
                    <input type="date" name="dari_tanggal" value="{{ request('dari_tanggal') }}" style="height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.5rem; font-size: 0.8125rem; background: #FFFFFF;">
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <label style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700);">Sampai Tanggal</label>
                    <input type="date" name="sampai_tanggal" value="{{ request('sampai_tanggal') }}" style="height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.5rem; font-size: 0.8125rem; background: #FFFFFF;">
                </div>

                <div style="display: flex; align-items: flex-end; gap: 0.5rem; margin-top: 1rem;">
                    <button type="submit" style="height: 38px; padding: 0 1.25rem; background: var(--blue-600); color: #FFFFFF; font-weight: 600; font-size: 0.8125rem; border: none; border-radius: 6px; cursor: pointer;">
                        Terapkan Filter
                    </button>
                    @if(request()->hasAny(['status', 'jalur', 'jurusan_id', 'dari_tanggal', 'sampai_tanggal']))
                        <a href="{{ route('admin.laporan.index', ['tab' => 'penempatan']) }}" style="font-size: 0.8125rem; color: var(--neutral-600); text-decoration: none; padding: 0.5rem;">Reset</a>
                    @endif
                </div>
            </form>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem; text-align: left;">
                    <thead>
                        <tr style="background: var(--neutral-100); border-bottom: 1px solid var(--neutral-400); color: var(--neutral-700); font-size: 0.75rem; text-transform: uppercase;">
                            <th style="padding: 0.75rem 1rem;">Nama Peserta</th>
                            <th style="padding: 0.75rem 1rem;">Pembimbing Lapangan</th>
                            <th style="padding: 0.75rem 1rem;">Jalur / Sumber</th>
                            <th style="padding: 0.75rem 1rem;">Rentang Waktu Magang</th>
                            <th style="padding: 0.75rem 1rem;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penempatanData as $pen)
                            <tr style="border-bottom: 1px solid var(--neutral-300);">
                                <td style="padding: 0.75rem 1rem;">
                                    <div style="font-weight: 600;">{{ $pen->peserta?->nama }}</div>
                                    <small style="color: var(--neutral-600);">{{ $pen->peserta?->profil?->institusi ?? '-' }}</small>
                                </td>
                                <td style="padding: 0.75rem 1rem;">
                                    {{ $pen->pembimbing?->nama ?? '-' }}
                                    @if($pen->bidang)
                                        <br><small style="color: var(--neutral-600);">{{ $pen->bidang->nama_bidang }}</small>
                                    @endif
                                </td>
                                <td style="padding: 0.75rem 1rem;">
                                    <span style="font-weight: 700; color: {{ $pen->jalur?->value === 'khusus' ? 'var(--purple-700, #7E22CE)' : 'var(--blue-700)' }};">
                                        {{ $pen->jalur?->label() }}
                                    </span>
                                    <small style="color: var(--neutral-600);">({{ $pen->sumber?->label() }})</small>
                                </td>
                                <td style="padding: 0.75rem 1rem;">
                                    {{ $pen->tgl_mulai?->format('d/m/Y') }} s/d {{ $pen->tgl_selesai?->format('d/m/Y') }}
                                </td>
                                <td style="padding: 0.75rem 1rem;">
                                    <span class="role-chip-badge" style="background: var(--blue-tint); color: var(--blue-700); border: 1px solid var(--blue-tint-line);">
                                        {{ $pen->status?->label() }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="padding: 2.5rem; text-align: center; color: var(--neutral-600);">
                                    Tidak ada data penempatan yang sesuai dengan filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($penempatanData->hasPages())
                <div style="margin-top: 1rem;">
                    {{ $penempatanData->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- 5. TAB 3: REALISASI KUOTA & SLOT -->
    @if($tab === 'kuota')
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;">
                <div>
                    <h2 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0 0 0.25rem 0;">Laporan Realisasi Kuota Umum vs Slot Khusus</h2>
                    <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0;">Monitoring kapasitas kuota per jurusan dan alokasi slot khusus kemitraan.</p>
                </div>

                <div>
                    <a href="{{ route('admin.laporan.kuota') }}"
                       style="display: inline-flex; align-items: center; gap: 0.5rem; height: 40px; padding: 0 1.25rem; background: var(--green-600); color: #FFFFFF; font-weight: 700; font-size: 0.875rem; text-decoration: none; border-radius: 8px; box-shadow: var(--shadow-subtle);">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        <span>Ekspor CSV Realisasi Kuota</span>
                    </a>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">
                <!-- Tabel Kuota Umum -->
                <div>
                    <h3 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.75rem;">Realisasi Kuota Umum Per Jurusan</h3>
                    <div style="overflow-x: auto; border: 1px solid var(--neutral-300); border-radius: 8px;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.8125rem; text-align: left;">
                            <thead style="background: var(--neutral-100); border-bottom: 1px solid var(--neutral-300);">
                                <tr>
                                    <th style="padding: 0.625rem 0.75rem;">Periode</th>
                                    <th style="padding: 0.625rem 0.75rem;">Jurusan</th>
                                    <th style="padding: 0.625rem 0.75rem; text-align: center;">Kapasitas</th>
                                    <th style="padding: 0.625rem 0.75rem; text-align: center;">Terisi</th>
                                    <th style="padding: 0.625rem 0.75rem; text-align: center;">Sisa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kuotaData as $k)
                                    <tr style="border-bottom: 1px solid var(--neutral-200);">
                                        <td style="padding: 0.625rem 0.75rem; font-weight: 600;">
                                            {{ $k->bulanMagang?->bulan_mulai?->translatedFormat('M Y') }}
                                        </td>
                                        <td style="padding: 0.625rem 0.75rem;">{{ $k->jurusan?->nama_jurusan }}</td>
                                        <td style="padding: 0.625rem 0.75rem; text-align: center;">{{ $k->jumlah }}</td>
                                        <td style="padding: 0.625rem 0.75rem; text-align: center; font-weight: 700; color: var(--amber-700);">{{ $k->terisi }}</td>
                                        <td style="padding: 0.625rem 0.75rem; text-align: center; font-weight: 700; color: var(--green-700);">
                                            {{ max(0, $k->jumlah - $k->terisi) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" style="padding: 1rem; text-align: center;">Tidak ada data kuota.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tabel Slot Khusus -->
                <div>
                    <h3 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.75rem;">Realisasi Slot Khusus Kemitraan</h3>
                    <div style="overflow-x: auto; border: 1px solid var(--neutral-300); border-radius: 8px;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.8125rem; text-align: left;">
                            <thead style="background: var(--neutral-100); border-bottom: 1px solid var(--neutral-300);">
                                <tr>
                                    <th style="padding: 0.625rem 0.75rem;">Periode Bulan</th>
                                    <th style="padding: 0.625rem 0.75rem; text-align: center;">Alokasi Slot</th>
                                    <th style="padding: 0.625rem 0.75rem; text-align: center;">Terisi</th>
                                    <th style="padding: 0.625rem 0.75rem; text-align: center;">Sisa Slot</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($slotKhususData as $sk)
                                    <tr style="border-bottom: 1px solid var(--neutral-200);">
                                        <td style="padding: 0.625rem 0.75rem; font-weight: 600;">
                                            {{ $sk->bulan_mulai?->translatedFormat('F Y') }}
                                        </td>
                                        <td style="padding: 0.625rem 0.75rem; text-align: center;">{{ $sk->jumlah }}</td>
                                        <td style="padding: 0.625rem 0.75rem; text-align: center; font-weight: 700; color: var(--purple-700, #7E22CE);">{{ $sk->terisi }}</td>
                                        <td style="padding: 0.625rem 0.75rem; text-align: center; font-weight: 700; color: var(--green-700);">{{ $sk->sisa }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" style="padding: 1rem; text-align: center;">Tidak ada data slot khusus.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- 6. TAB 4: REKAP NILAI (SUPERADMIN EXCLUSIVE) -->
    @if($tab === 'nilai' && $isSuperAdmin)
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;">
                <div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <h2 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Laporan Rekapitulasi Nilai & Kelulusan</h2>
                        <span class="role-chip-badge" style="background: var(--blue-tint); color: var(--blue-700); font-weight: 700;">SuperAdmin Only (A-20)</span>
                    </div>
                    <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0.25rem 0 0 0;">Evaluasi hasil penilaian kompetensi, skor akhir, dan predikat resmi seluruh peserta magang.</p>
                </div>

                <div>
                    <a href="{{ route('admin.laporan.nilai') }}"
                       style="display: inline-flex; align-items: center; gap: 0.5rem; height: 40px; padding: 0 1.25rem; background: var(--green-600); color: #FFFFFF; font-weight: 700; font-size: 0.875rem; text-decoration: none; border-radius: 8px; box-shadow: var(--shadow-subtle);">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        <span>Ekspor CSV Nilai Magang</span>
                    </a>
                </div>
            </div>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem; text-align: left;">
                    <thead>
                        <tr style="background: var(--neutral-100); border-bottom: 1px solid var(--neutral-400); color: var(--neutral-700); font-size: 0.75rem; text-transform: uppercase;">
                            <th style="padding: 0.75rem 1rem;">Nama Peserta</th>
                            <th style="padding: 0.75rem 1rem;">NIM / Institusi</th>
                            <th style="padding: 0.75rem 1rem;">Pembimbing</th>
                            <th style="padding: 0.75rem 1rem; text-align: center;">Nilai Akhir</th>
                            <th style="padding: 0.75rem 1rem; text-align: center;">Predikat</th>
                            <th style="padding: 0.75rem 1rem;">Status Evaluasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($nilaiData as $nl)
                            <tr style="border-bottom: 1px solid var(--neutral-300);">
                                <td style="padding: 0.75rem 1rem; font-weight: 600;">
                                    {{ $nl->penempatan?->peserta?->nama }}
                                </td>
                                <td style="padding: 0.75rem 1rem;">
                                    <div>{{ $nl->penempatan?->peserta?->profil?->nim_nisn ?? '-' }}</div>
                                    <small style="color: var(--neutral-600);">{{ $nl->penempatan?->peserta?->profil?->institusi ?? '-' }}</small>
                                </td>
                                <td style="padding: 0.75rem 1rem;">
                                    {{ $nl->pembimbing?->nama ?? '-' }}
                                </td>
                                <td style="padding: 0.75rem 1rem; text-align: center; font-weight: 900; font-size: 1rem; color: var(--blue-700);">
                                    {{ $nl->nilai_akhir }}
                                </td>
                                <td style="padding: 0.75rem 1rem; text-align: center;">
                                    <span class="role-chip-badge" style="background: var(--green-100); color: var(--green-800); border: 1px solid var(--green-600); font-weight: 800;">
                                        {{ $nl->predikat }}
                                    </span>
                                </td>
                                <td style="padding: 0.75rem 1rem;">
                                    <span class="role-chip-badge" style="background: {{ $nl->status?->value === 'final' ? 'var(--green-100)' : 'var(--amber-100)' }}; color: {{ $nl->status?->value === 'final' ? 'var(--green-800)' : 'var(--amber-800)' }};">
                                        {{ strtoupper($nl->status?->value ?? 'DRAFT') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding: 2.5rem; text-align: center; color: var(--neutral-600);">
                                    Belum ada data penilaian tersimpan di sistem.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($nilaiData->hasPages())
                <div style="margin-top: 1rem;">
                    {{ $nilaiData->links() }}
                </div>
            @endif
        </div>
    @endif

</div>
@endsection
