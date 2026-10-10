@extends('layouts.dashboard')

@section('title', 'Dashboard Administrasi - MagKom Sleman')
@section('page_title', 'Dashboard Operasional Administrasi')
@section('page_description', 'Pantau aktivitas pendaftaran, antrean verifikasi berkas, dan utilisasi kuota magang secara real-time.')

@section('breadcrumbs')
    <span class="breadcrumb-current">Dashboard Administrasi</span>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.75rem;">

    <!-- 1. SUMMARY STAT METRICS CARDS -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;">
        <!-- Card 1: Pengajuan Menunggu Verifikasi -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle); display: flex; align-items: center; justify-content: space-between; border-left: 4px solid var(--amber-500);">
            <div>
                <span style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-600); display: block; margin-bottom: 0.25rem;">Perlu Verifikasi Segera</span>
                <span style="font-size: 1.875rem; font-weight: 800; color: var(--neutral-800); line-height: 1.1;">{{ number_format($pengajuanMenunggu) }}</span>
                <span style="font-size: 0.75rem; color: var(--amber-700); font-weight: 600; display: block; margin-top: 0.25rem;">Status: Diajukan</span>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 10px; background: var(--amber-100); color: var(--amber-700); display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                </svg>
            </div>
        </div>

        <!-- Card 2: Menunggu Perbaikan Berkas -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle); display: flex; align-items: center; justify-content: space-between; border-left: 4px solid var(--blue-500);">
            <div>
                <span style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-600); display: block; margin-bottom: 0.25rem;">Perlu Perbaikan Dokumen</span>
                <span style="font-size: 1.875rem; font-weight: 800; color: var(--neutral-800); line-height: 1.1;">{{ number_format($perluPerbaikan) }}</span>
                <span style="font-size: 0.75rem; color: var(--blue-600); font-weight: 600; display: block; margin-top: 0.25rem;">Menunggu unggah ulang</span>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 10px; background: var(--blue-50); color: var(--blue-600); display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
            </div>
        </div>

        <!-- Card 3: Antrean Waiting List -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle); display: flex; align-items: center; justify-content: space-between; border-left: 4px solid var(--purple-500, #8A2BE2);">
            <div>
                <span style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-600); display: block; margin-bottom: 0.25rem;">Antrean Waiting List</span>
                <span style="font-size: 1.875rem; font-weight: 800; color: var(--neutral-800); line-height: 1.1;">{{ number_format($waitingListCount) }}</span>
                <span style="font-size: 0.75rem; color: var(--neutral-600); font-weight: 600; display: block; margin-top: 0.25rem;">Urutan FIFO BR-05</span>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 10px; background: #F3E8FF; color: #7E22CE; display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
            </div>
        </div>

        <!-- Card 4: Peserta Aktif Bimbingan -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle); display: flex; align-items: center; justify-content: space-between; border-left: 4px solid var(--green-600);">
            <div>
                <span style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-600); display: block; margin-bottom: 0.25rem;">Peserta Magang Aktif</span>
                <span style="font-size: 1.875rem; font-weight: 800; color: var(--neutral-800); line-height: 1.1;">{{ number_format($pesertaAktifCount) }}</span>
                <span style="font-size: 0.75rem; color: var(--green-700); font-weight: 600; display: block; margin-top: 0.25rem;">Sedang menjalani tugas</span>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 10px; background: var(--green-100); color: var(--green-700); display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- 2. QUICK ACTION SHORTCUTS -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle);">
        <h3 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.875rem; display: flex; align-items: center; gap: 0.5rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
            Aksi Cepat Operasional Administrasi
        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 0.875rem;">
            <a href="{{ route('admin.pendaftaran.index') }}" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 8px; text-decoration: none; color: var(--neutral-800); transition: all 0.15s ease;">
                <div style="width: 36px; height: 36px; border-radius: 6px; background: var(--blue-600); color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><circle cx="10" cy="13" r="2"></circle><path d="m20 17-1.09-1.09a2 2 0 0 0-2.82 0L10 22"></path></svg>
                </div>
                <div>
                    <span style="font-weight: 700; font-size: 0.875rem; display: block;">Verifikasi Berkas</span>
                    <span style="font-size: 0.75rem; color: var(--neutral-600);">Tinjau pengajuan baru</span>
                </div>
            </a>

            <a href="{{ route('admin.kuota.index') }}" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 8px; text-decoration: none; color: var(--neutral-800); transition: all 0.15s ease;">
                <div style="width: 36px; height: 36px; border-radius: 6px; background: var(--blue-700); color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                </div>
                <div>
                    <span style="font-weight: 700; font-size: 0.875rem; display: block;">Manajemen Kuota</span>
                    <span style="font-size: 0.75rem; color: var(--neutral-600);">Alokasi kuota jurusan</span>
                </div>
            </a>

            <a href="{{ route('admin.penempatan.index') }}" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 8px; text-decoration: none; color: var(--neutral-800); transition: all 0.15s ease;">
                <div style="width: 36px; height: 36px; border-radius: 6px; background: var(--green-600); color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
                </div>
                <div>
                    <span style="font-weight: 700; font-size: 0.875rem; display: block;">Penempatan Staf</span>
                    <span style="font-size: 0.75rem; color: var(--neutral-600);">Input manual & pembimbing</span>
                </div>
            </a>

            <a href="{{ route('admin.laporan.index') }}" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 8px; text-decoration: none; color: var(--neutral-800); transition: all 0.15s ease;">
                <div style="width: 36px; height: 36px; border-radius: 6px; background: var(--neutral-800); color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                </div>
                <div>
                    <span style="font-weight: 700; font-size: 0.875rem; display: block;">Hub Laporan</span>
                    <span style="font-size: 0.75rem; color: var(--neutral-600);">Rekap & ekspor data</span>
                </div>
            </a>
        </div>
    </div>

    <!-- 3. KETERISIAN KUOTA BULAN BERJALAN -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
            <div>
                <h3 style="font-size: 1.0625rem; font-weight: 700; color: var(--neutral-800); margin: 0;">
                    Keterisian Kuota Magang Periode {{ $bulanAktif ? $bulanAktif->bulan_mulai?->translatedFormat('F Y') : 'Saat Ini' }}
                </h3>
                <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0.25rem 0 0 0;">
                    Monitoring alokasi kuota umum vs jumlah peserta yang telah diterima pada periode berjalan (BR-01).
                </p>
            </div>
            <a href="{{ route('admin.kuota.index') }}" style="font-size: 0.8125rem; font-weight: 700; color: var(--blue-600); text-decoration: none;">
                Kelola Kuota Jurusan &rarr;
            </a>
        </div>

        @if($kuotaList->isEmpty())
            <div style="padding: 2rem; text-align: center; color: var(--neutral-600); background: var(--neutral-150); border-radius: 8px;">
                <p style="margin: 0; font-size: 0.875rem;">Belum ada kuota jurusan yang dikonfigurasi untuk periode bulan aktif saat ini.</p>
            </div>
        @else
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
                @foreach($kuotaList as $kuota)
                    @php
                        $persen = $kuota->jumlah > 0 ? min(100, round(($kuota->terisi / $kuota->jumlah) * 100)) : 0;
                        $sisa = max(0, $kuota->jumlah - $kuota->terisi);
                        $barColor = $persen >= 100 ? 'var(--red-600)' : ($persen >= 75 ? 'var(--amber-500)' : 'var(--blue-600)');
                    @endphp
                    <div style="background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 8px; padding: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <span style="font-weight: 700; font-size: 0.875rem; color: var(--neutral-800);">
                                {{ $kuota->jurusan?->nama_jurusan }}
                            </span>
                            <span style="font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 4px; background: {{ $sisa > 0 ? 'var(--green-100)' : 'var(--red-100)' }}; color: {{ $sisa > 0 ? 'var(--green-800)' : 'var(--red-800)' }};">
                                {{ $sisa > 0 ? 'Sisa ' . $sisa : 'Penuh' }}
                            </span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: var(--neutral-600); margin-bottom: 0.375rem;">
                            <span>Terisi: {{ $kuota->terisi }} dari {{ $kuota->jumlah }}</span>
                            <span style="font-weight: 700;">{{ $persen }}%</span>
                        </div>
                        <div style="width: 100%; height: 8px; background: var(--neutral-300); border-radius: 999px; overflow: hidden;">
                            <div style="width: {{ $persen }}%; height: 100%; background: {{ $barColor }}; border-radius: 999px; transition: width 0.3s ease;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- 4. TABEL 5 PENGAJUAN TERBARU YANG BUTUH TINDAKAN -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-subtle);">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-400); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
            <div>
                <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-800); margin: 0;">Pengajuan Terbaru Perlu Tindakan</h3>
                <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0.125rem 0 0 0;">Daftar 5 berkas terkini berstatus Diajukan atau Perlu Perbaikan.</p>
            </div>
            <a href="{{ route('admin.pendaftaran.index') }}" style="font-size: 0.8125rem; font-weight: 700; color: var(--blue-600); text-decoration: none;">
                Buka Semua Antrean &rarr;
            </a>
        </div>

        @if($pendaftaranTerbaru->isEmpty())
            <div style="padding: 3rem 1.5rem; text-align: center; color: var(--neutral-600);">
                <div style="width: 48px; height: 48px; border-radius: 50%; background: var(--green-100); color: var(--green-700); display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem auto;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </div>
                <h4 style="font-size: 1rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.25rem;">Semua Pengajuan Telah Diproses</h4>
                <p style="font-size: 0.8125rem; margin: 0;">Tidak ada berkas yang tertahan dalam antrean verifikasi saat ini.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                    <thead>
                        <tr style="background: var(--neutral-200); border-bottom: 1px solid var(--neutral-400); height: 48px;">
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">No. Pendaftaran</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Calon Peserta</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Asal Institusi & Jurusan</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Bulan Diajukan</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800); text-align: center;">Status</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800); text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendaftaranTerbaru as $item)
                            <tr style="border-bottom: 1px solid var(--neutral-400); height: 52px;">
                                <td style="padding: 0.75rem 1rem; font-weight: 700; color: var(--blue-700);">
                                    {{ $item->nomor_pendaftaran }}
                                </td>
                                <td style="padding: 0.75rem 1rem; font-weight: 600; color: var(--neutral-800);">
                                    <div>{{ $item->user?->nama }}</div>
                                    <small style="color: var(--neutral-600);">{{ $item->user?->email }}</small>
                                </td>
                                <td style="padding: 0.75rem 1rem; color: var(--neutral-700);">
                                    <div>{{ $item->user?->profil?->institusi ?? '-' }}</div>
                                    <small style="color: var(--neutral-600);">{{ $item->kuota?->jurusan?->nama_jurusan }}</small>
                                </td>
                                <td style="padding: 0.75rem 1rem; color: var(--neutral-800);">
                                    {{ $item->bulanMagang?->bulan_mulai?->translatedFormat('F Y') }}
                                </td>
                                <td style="padding: 0.75rem 1rem; text-align: center;">
                                    @if($item->status->value === 'diajukan')
                                        <span class="role-chip-badge" style="background: var(--amber-100); color: var(--amber-800); border: 1px solid var(--amber-500); font-weight: 700;">
                                            Diajukan
                                        </span>
                                    @else
                                        <span class="role-chip-badge" style="background: var(--blue-50); color: var(--blue-700); border: 1px solid var(--blue-600); font-weight: 700;">
                                            Perlu Perbaikan
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 0.75rem 1rem; text-align: right;">
                                    <a href="{{ route('admin.pendaftaran.show', $item) }}" style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.35rem 0.75rem; background: var(--blue-600); color: #fff; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 0.8125rem;">
                                        <span>Tinjau</span>
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection
