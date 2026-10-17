@extends('layouts.dashboard')

@section('title', 'Profil Mentee: ' . ($penempatan->peserta?->nama ?? 'Peserta') . ' - Portal MagKom')

@section('content')
<div class="dashboard-content" style="display: flex; flex-direction: column; gap: 1.5rem;">

    <!-- Breadcrumb & Navigasi Kembali -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <nav style="font-size: 0.8rem; color: var(--neutral-500); margin-bottom: 0.35rem; display: flex; align-items: center; gap: 0.4rem;">
                <a href="{{ route('pembimbing.dashboard') }}" style="color: var(--neutral-600); text-decoration: none;">Dashboard</a>
                <span>/</span>
                <a href="{{ route('pembimbing.peserta.index') }}" style="color: var(--neutral-600); text-decoration: none;">Peserta Bimbingan</a>
                <span>/</span>
                <span style="color: var(--neutral-900); font-weight: 600;">{{ $penempatan->peserta?->nama }}</span>
            </nav>
            <h1 style="font-size: 1.5rem; font-weight: 700; color: var(--neutral-900); margin: 0;">
                Profil 360° Mentee Bimbingan
            </h1>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <a href="{{ route('pembimbing.peserta.index') }}" class="btn" style="background: var(--neutral-100); color: var(--neutral-700); padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem;">
                &larr; Kembali ke Direktori
            </a>
            <a href="{{ route('pembimbing.penilaian.show', $penempatan->id) }}" class="btn" style="background: #0641CC; color: #ffffff; padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="8" r="7"></circle>
                    <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                </svg>
                Lembar Nilai Magang
            </a>
        </div>
    </div>

    <!-- Header Profil Hero Card -->
    <div style="background: #ffffff; border-radius: 16px; border: 1px solid var(--neutral-200); padding: 1.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
        <div style="display: flex; align-items: center; gap: 1.25rem;">
            <div style="width: 68px; height: 68px; border-radius: 16px; background: linear-gradient(135deg, #0641CC 0%, #3B82F6 100%); color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.5rem; box-shadow: 0 4px 12px rgba(6, 65, 204, 0.25);">
                {{ strtoupper(substr($penempatan->peserta?->nama ?? 'P', 0, 2)) }}
            </div>
            <div>
                <div style="display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
                    <h2 style="font-size: 1.35rem; font-weight: 700; color: var(--neutral-900); margin: 0;">
                        {{ $penempatan->peserta?->nama }}
                    </h2>
                    <span style="font-size: 0.75rem; font-weight: 600; padding: 0.2rem 0.6rem; border-radius: 999px; background: {{ $penempatan->status->value === 'aktif' ? '#d1fae5' : '#e0e7ff' }}; color: {{ $penempatan->status->value === 'aktif' ? '#065f46' : '#3730a3' }};">
                        Status: {{ ucfirst($penempatan->status->value) }}
                    </span>
                    <span style="font-size: 0.75rem; font-weight: 600; padding: 0.2rem 0.6rem; border-radius: 999px; background: rgba(6, 65, 204, 0.08); color: #0641CC;">
                        Jalur {{ ucfirst($penempatan->jalur?->value ?? 'Umum') }}
                    </span>
                </div>
                <div style="font-size: 0.875rem; color: var(--neutral-600); margin-top: 0.25rem;">
                    {{ $penempatan->peserta?->profil?->institusi ?? 'Institusi Belum Dilengkapi' }} &bull; {{ $penempatan->peserta?->profil?->jurusan?->nama_jurusan ?? 'Jurusan Belum Dilengkapi' }}
                </div>
                <div style="font-size: 0.8rem; color: var(--neutral-500); margin-top: 0.2rem;">
                    Bidang: <strong>{{ $penempatan->bidang?->nama_bidang ?? '-' }}</strong> &bull; NIM/NISN: <strong>{{ $penempatan->peserta?->profil?->nim_nisn ?? '-' }}</strong>
                </div>
            </div>
        </div>

        <!-- Quick Action Buttons -->
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <a href="{{ route('pembimbing.logbook.index', ['penempatan_id' => $penempatan->id]) }}" style="padding: 0.5rem 0.85rem; background: var(--neutral-100); color: var(--neutral-700); font-size: 0.8rem; font-weight: 600; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                Logbook Mentee
            </a>
            <a href="{{ route('pembimbing.tugas.index', ['penempatan_id' => $penempatan->id]) }}" style="padding: 0.5rem 0.85rem; background: rgba(6, 65, 204, 0.08); color: #0641CC; font-size: 0.8rem; font-weight: 600; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"></path><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                Tugas Mentee
            </a>
        </div>
    </div>

    <!-- 3 Widget KPI Kinerja Mentee -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.25rem;">
        
        <!-- Widget 1: Tugas Mentee -->
        <div style="background: #ffffff; border-radius: 14px; border: 1px solid var(--neutral-200); padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                <span style="font-size: 0.8rem; font-weight: 600; color: var(--neutral-500); text-transform: uppercase;">Penyelesaian Tugas</span>
                <span style="font-size: 0.8rem; font-weight: 700; color: #0641CC;">
                    {{ $tugasSelesai }} / {{ $totalTugas }} Selesai
                </span>
            </div>
            @php
                $persenTugas = $totalTugas > 0 ? (int) round(($tugasSelesai / $totalTugas) * 100) : 0;
            @endphp
            <div style="height: 8px; width: 100%; background: var(--neutral-100); border-radius: 999px; overflow: hidden; margin-bottom: 0.75rem;">
                <div style="height: 100%; width: {{ $persenTugas }}%; background: #10b981; border-radius: 999px;"></div>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: var(--neutral-500);">
                <span>{{ $tugasAktif }} tugas sedang berjalan</span>
                <span>Tingkat Selesai: {{ $persenTugas }}%</span>
            </div>
        </div>

        <!-- Widget 2: Logbook Harian -->
        <div style="background: #ffffff; border-radius: 14px; border: 1px solid var(--neutral-200); padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                <span style="font-size: 0.8rem; font-weight: 600; color: var(--neutral-500); text-transform: uppercase;">Kedisiplinan Logbook</span>
                <span style="font-size: 0.8rem; font-weight: 700; color: #d97706;">
                    Total {{ $totalLogbook }} Entri
                </span>
            </div>
            <div style="display: flex; gap: 0.75rem; margin-bottom: 0.5rem;">
                <div style="flex: 1; background: #f0fdf4; padding: 0.4rem 0.6rem; border-radius: 6px; text-align: center;">
                    <div style="font-size: 0.7rem; color: #166534;">Disetujui</div>
                    <div style="font-size: 0.95rem; font-weight: 700; color: #15803d;">{{ $logbookDisetujui }}</div>
                </div>
                <div style="flex: 1; background: #fef2f2; padding: 0.4rem 0.6rem; border-radius: 6px; text-align: center;">
                    <div style="font-size: 0.7rem; color: #991b1b;">Perlu Revisi</div>
                    <div style="font-size: 0.95rem; font-weight: 700; color: #b91c1c;">{{ $logbookPerluRevisi }}</div>
                </div>
            </div>
            <div style="font-size: 0.75rem; color: var(--neutral-500); text-align: right;">
                Rata-rata 1 entri per hari kerja dinas
            </div>
        </div>

        <!-- Widget 3: Status Nilai Magang -->
        <div style="background: #ffffff; border-radius: 14px; border: 1px solid var(--neutral-200); padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <span style="font-size: 0.8rem; font-weight: 600; color: var(--neutral-500); text-transform: uppercase;">Status Penilaian</span>
                @if($penempatan->penilaian?->status->value === 'final')
                    <span style="font-size: 0.75rem; font-weight: 600; background: #d1fae5; color: #065f46; padding: 0.15rem 0.5rem; border-radius: 999px;">Final</span>
                @elseif($penempatan->penilaian?->status->value === 'draft')
                    <span style="font-size: 0.75rem; font-weight: 600; background: #fef3c7; color: #92400e; padding: 0.15rem 0.5rem; border-radius: 999px;">Draft</span>
                @else
                    <span style="font-size: 0.75rem; font-weight: 500; background: var(--neutral-100); color: var(--neutral-600); padding: 0.15rem 0.5rem; border-radius: 999px;">Belum Dinilai</span>
                @endif
            </div>
            <div style="display: flex; align-items: baseline; gap: 0.5rem; margin-top: 0.25rem;">
                <span style="font-size: 1.75rem; font-weight: 800; color: var(--neutral-900);">
                    {{ $penempatan->penilaian ? number_format((float) $penempatan->penilaian->nilai_akhir, 2) : '-' }}
                </span>
                @if($penempatan->penilaian?->predikat)
                    <span style="font-size: 1.1rem; font-weight: 700; color: #0641CC;">
                        Predikat {{ $penempatan->penilaian->predikat }}
                    </span>
                @endif
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.5rem; font-size: 0.75rem;">
                <span style="color: var(--neutral-500);">Berdasarkan 5 kriteria rubrik</span>
                <a href="{{ route('pembimbing.penilaian.show', $penempatan->id) }}" style="color: #0641CC; font-weight: 600; text-decoration: none;">
                    Kelola Nilai &rarr;
                </a>
            </div>
        </div>

    </div>

    {{-- VISUALISASI ANALITIK PROGRES MENTEE (B9) --}}
    @php
        $totTugas = max(1, $tugasStats['total'] ?? 1);
        $pSelesai = round((($tugasStats['selesai'] ?? 0) / $totTugas) * 100);
        $pProgres = round((($tugasStats['dalam_pengerjaan'] ?? 0) / $totTugas) * 100);
        $pBelum = round((($tugasStats['belum_dimulai'] ?? 0) / $totTugas) * 100);
        $pTelat = round((($tugasStats['terlambat'] ?? 0) / $totTugas) * 100);

        $deg1 = round(($pSelesai / 100) * 360);
        $deg2 = $deg1 + round(($pProgres / 100) * 360);
        $deg3 = $deg2 + round(($pBelum / 100) * 360);
    @endphp
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 1.5rem;">
        
        {{-- Card Donut Chart: Komposisi Tugas --}}
        <div style="background: #ffffff; border-radius: 14px; border: 1px solid var(--neutral-200); padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-900); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M12 2a10 10 0 0 1 10 10h-10z"></path></svg>
                    <span>Komposisi &amp; Rasio Tugas Peserta</span>
                </h3>
                <span style="font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 9999px; background: #EFF6FF; color: #1E40AF; border: 1px solid #BFDBFE;">
                    Tingkat Selesai: {{ $completionRate }}%
                </span>
            </div>

            <div style="display: flex; align-items: center; justify-content: space-around; flex-wrap: wrap; gap: 1.5rem;">
                {{-- CSS Donut Chart --}}
                <div style="position: relative; width: 140px; height: 140px; border-radius: 50%; background: conic-gradient(#10B981 0deg {{ $deg1 }}deg, #2563EB {{ $deg1 }}deg {{ $deg2 }}deg, #CBD5E1 {{ $deg2 }}deg {{ $deg3 }}deg, #EF4444 {{ $deg3 }}deg 360deg); display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: inset 0 2px 4px rgba(0,0,0,0.06);">
                    <div style="width: 90px; height: 90px; border-radius: 50%; background: #ffffff; display: flex; flex-direction: column; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(0,0,0,0.05);">
                        <span style="font-size: 1.35rem; font-weight: 800; color: var(--neutral-900); line-height: 1;">{{ $tugasStats['selesai'] ?? 0 }}/{{ $tugasStats['total'] ?? 0 }}</span>
                        <span style="font-size: 0.6875rem; font-weight: 600; color: var(--neutral-500); text-transform: uppercase;">Selesai</span>
                    </div>
                </div>

                {{-- Legends --}}
                <div style="display: flex; flex-direction: column; gap: 0.5rem; min-width: 160px; font-size: 0.8125rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.75rem;">
                        <span style="display: flex; align-items: center; gap: 0.4rem;">
                            <span style="width: 10px; height: 10px; border-radius: 3px; background: #10B981; display: inline-block;"></span>
                            <span style="color: var(--neutral-700);">Selesai</span>
                        </span>
                        <span style="font-weight: 700; color: var(--neutral-900);">{{ $tugasStats['selesai'] ?? 0 }} ({{ $pSelesai }}%)</span>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.75rem;">
                        <span style="display: flex; align-items: center; gap: 0.4rem;">
                            <span style="width: 10px; height: 10px; border-radius: 3px; background: #2563EB; display: inline-block;"></span>
                            <span style="color: var(--neutral-700);">Dikerjakan</span>
                        </span>
                        <span style="font-weight: 700; color: var(--neutral-900);">{{ $tugasStats['dalam_pengerjaan'] ?? 0 }} ({{ $pProgres }}%)</span>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.75rem;">
                        <span style="display: flex; align-items: center; gap: 0.4rem;">
                            <span style="width: 10px; height: 10px; border-radius: 3px; background: #CBD5E1; display: inline-block;"></span>
                            <span style="color: var(--neutral-700);">Belum Mulai</span>
                        </span>
                        <span style="font-weight: 700; color: var(--neutral-900);">{{ $tugasStats['belum_dimulai'] ?? 0 }} ({{ $pBelum }}%)</span>
                    </div>
                    @if(($tugasStats['terlambat'] ?? 0) > 0)
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.75rem;">
                            <span style="display: flex; align-items: center; gap: 0.4rem;">
                                <span style="width: 10px; height: 10px; border-radius: 3px; background: #EF4444; display: inline-block;"></span>
                                <span style="color: #DC2626; font-weight: 600;">Terlambat</span>
                            </span>
                            <span style="font-weight: 700; color: #DC2626;">{{ $tugasStats['terlambat'] }} tugas</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Card Bar Chart: Timeline Logbook Mingguan --}}
        <div style="background: #ffffff; border-radius: 14px; border: 1px solid var(--neutral-200); padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-900); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2"><path d="M18 20V10"></path><path d="M12 20V4"></path><path d="M6 20v-6"></path></svg>
                    <span>Pelacakan Mingguan Logbook Magang</span>
                </h3>
                <span style="font-size: 0.75rem; color: var(--neutral-500); font-weight: 600;">
                    Target: 12 Minggu
                </span>
            </div>

            <div style="display: flex; align-items: flex-end; justify-content: space-between; gap: 0.4rem; height: 110px; padding: 0.5rem 0.25rem 0.25rem 0.25rem; border-bottom: 2px solid var(--neutral-200); margin-bottom: 0.5rem;">
                @foreach($logbookPerMinggu ?? [] as $minggu)
                    @php
                        $st = $minggu['status'];
                        $barHeight = match($st) {
                            'disetujui' => '90px',
                            'terkirim' => '70px',
                            'perlu_revisi' => '45px',
                            'mendatang' => '15px',
                            default => '25px',
                        };
                        $barBg = match($st) {
                            'disetujui' => '#10B981',
                            'terkirim' => '#F59E0B',
                            'perlu_revisi' => '#EF4444',
                            'mendatang' => '#F1F5F9',
                            default => '#E2E8F0',
                        };
                    @endphp
                    <div style="display: flex; flex-direction: column; align-items: center; flex: 1; min-width: 18px;" title="Minggu ke-{{ $minggu['minggu_ke'] }}: {{ ucfirst(str_replace('_', ' ', $st)) }}">
                        <div style="width: 100%; max-width: 22px; height: {{ $barHeight }}; background: {{ $barBg }}; border-radius: 4px 4px 0 0; transition: all 0.2s ease;"></div>
                    </div>
                @endforeach
            </div>

            <div style="display: flex; justify-content: space-between; font-size: 0.7rem; color: var(--neutral-500); padding: 0 0.25rem;">
                @foreach($logbookPerMinggu ?? [] as $minggu)
                    <span style="flex: 1; text-align: center; font-weight: {{ $minggu['is_aktif'] ? '800; color: #0641CC;' : '600;' }}">M{{ $minggu['minggu_ke'] }}</span>
                @endforeach
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 0.85rem; font-size: 0.72rem; color: var(--neutral-600); justify-content: center; flex-wrap: wrap;">
                <span style="display: flex; align-items: center; gap: 0.3rem;"><span style="width: 8px; height: 8px; border-radius: 2px; background: #10B981;"></span> Disetujui</span>
                <span style="display: flex; align-items: center; gap: 0.3rem;"><span style="width: 8px; height: 8px; border-radius: 2px; background: #F59E0B;"></span> Menunggu Review</span>
                <span style="display: flex; align-items: center; gap: 0.3rem;"><span style="width: 8px; height: 8px; border-radius: 2px; background: #EF4444;"></span> Perlu Revisi</span>
                <span style="display: flex; align-items: center; gap: 0.3rem;"><span style="width: 8px; height: 8px; border-radius: 2px; background: #E2E8F0;"></span> Belum Terisi</span>
            </div>
        </div>

    </div>

    <!-- Rincian Data Diri & Timeline Magang -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 1.5rem;">
        
        <!-- Kolom Data Kontak & Akademik -->
        <div style="background: #ffffff; border-radius: 14px; border: 1px solid var(--neutral-200); padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-900); margin: 0 0 1rem 0; padding-bottom: 0.75rem; border-bottom: 1px solid var(--neutral-100);">
                Data Pribadi & Kontak Akademik
            </h3>
            <div style="display: flex; flex-direction: column; gap: 0.85rem; font-size: 0.875rem;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--neutral-500);">Email Resmi</span>
                    <span style="font-weight: 600; color: var(--neutral-900);">{{ $penempatan->peserta?->email }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--neutral-500);">Nomor Telepon / WA</span>
                    <span style="font-weight: 600; color: var(--neutral-900);">{{ $penempatan->peserta?->no_telp ?? '-' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--neutral-500);">Asal Kampus / Sekolah</span>
                    <span style="font-weight: 600; color: var(--neutral-900);">{{ $penempatan->peserta?->profil?->institusi ?? '-' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--neutral-500);">Program Studi</span>
                    <span style="font-weight: 600; color: var(--neutral-900);">{{ $penempatan->peserta?->profil?->jurusan?->nama_jurusan ?? '-' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--neutral-500);">NIM / NISN</span>
                    <span style="font-weight: 600; color: var(--neutral-900);">{{ $penempatan->peserta?->profil?->nim_nisn ?? '-' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--neutral-500);">Dosen Pembimbing Kampus</span>
                    <span style="font-weight: 600; color: var(--neutral-900);">{{ $penempatan->peserta?->profil?->nama_dosen_pembimbing ?? '-' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--neutral-500);">Kontak Dosen / Kampus</span>
                    <span style="font-weight: 600; color: var(--neutral-900);">{{ $penempatan->peserta?->profil?->kontak_dosen_pembimbing ?? '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Kolom Timeline Magang & Masa Tenggang -->
        <div style="background: #ffffff; border-radius: 14px; border: 1px solid var(--neutral-200); padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-900); margin: 0 0 1rem 0; padding-bottom: 0.75rem; border-bottom: 1px solid var(--neutral-100);">
                Timeline Periode & Ketentuan Akun
            </h3>
            <div style="display: flex; flex-direction: column; gap: 0.85rem; font-size: 0.875rem;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--neutral-500);">Tanggal Mulai Magang</span>
                    <span style="font-weight: 600; color: var(--neutral-900);">{{ $penempatan->tgl_mulai?->format('d F Y') ?? '-' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--neutral-500);">Tanggal Rencana Selesai</span>
                    <span style="font-weight: 600; color: var(--neutral-900);">{{ $penempatan->tgl_selesai?->format('d F Y') ?? '-' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--neutral-500);">Total Durasi</span>
                    <span style="font-weight: 600; color: var(--neutral-900);">{{ $totalHariMagang }} Hari Kalender</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--neutral-500);">Sisa Waktu Magang</span>
                    <span style="font-weight: 600; color: {{ $sisaHari <= 7 && $penempatan->status->value === 'aktif' ? '#b91c1c' : 'var(--neutral-900)' }};">
                        @if($penempatan->status->value === 'selesai')
                            Selesai Ditetapkan
                        @else
                            {{ $sisaHari < 0 ? 'Melewati batas ' . abs($sisaHari) . ' hari' : $sisaHari . ' Hari Tersisa' }}
                        @endif
                    </span>
                </div>
                <div style="display: flex; justify-content: space-between; border-top: 1px dashed var(--neutral-200); padding-top: 0.75rem;">
                    <span style="color: var(--neutral-500);">Tanggal Ditetapkan Selesai</span>
                    <span style="font-weight: 600; color: #059669;">{{ $penempatan->tgl_ditetapkan_selesai?->format('d F Y') ?? 'Belum Ditetapkan' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--neutral-500);">Batas Masa Tenggang Akun (BR-11)</span>
                    <span style="font-weight: 600; color: var(--neutral-900);">{{ $penempatan->tgl_nonaktif?->format('d F Y') ?? '-' }}</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Riwayat Penugasan Mentee Ini -->
    <div style="background: #ffffff; border-radius: 14px; border: 1px solid var(--neutral-200); box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden;">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-200); display: flex; justify-content: space-between; align-items: center; background: #fafafa;">
            <div>
                <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-900); margin: 0;">
                    Riwayat Penugasan ({{ $penempatan->tugas->count() }})
                </h3>
                <span style="font-size: 0.75rem; color: var(--neutral-500);">
                    Daftar seluruh tugas terstruktur yang telah diberikan kepada mentee ini.
                </span>
            </div>
            <a href="{{ route('pembimbing.tugas.index', ['penempatan_id' => $penempatan->id]) }}" style="font-size: 0.8rem; font-weight: 600; color: #0641CC; text-decoration: none;">
                Kelola Tugas &rarr;
            </a>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--neutral-200); background: #f8fafc; color: var(--neutral-600); font-size: 0.75rem; text-transform: uppercase;">
                        <th style="padding: 0.875rem 1.5rem;">Judul Tugas</th>
                        <th style="padding: 0.875rem 1rem;">Kategori</th>
                        <th style="padding: 0.875rem 1rem;">Kompleksitas</th>
                        <th style="padding: 0.875rem 1rem;">Deadline</th>
                        <th style="padding: 0.875rem 1rem;">Status</th>
                        <th style="padding: 0.875rem 1.5rem; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penempatan->tugas as $tugas)
                        <tr style="border-bottom: 1px solid var(--neutral-100);">
                            <td style="padding: 0.875rem 1.5rem;">
                                <div style="font-weight: 600; color: var(--neutral-900);">
                                    {{ $tugas->judul }}
                                </div>
                                <div style="font-size: 0.75rem; color: var(--neutral-500);">
                                    {{ $tugas->progresLogs->count() }} Laporan Progres
                                </div>
                            </td>
                            <td style="padding: 0.875rem 1rem;">
                                <span style="font-size: 0.75rem; background: var(--neutral-100); color: var(--neutral-700); padding: 0.15rem 0.45rem; border-radius: 4px;">
                                    {{ $tugas->kategori }}
                                </span>
                            </td>
                            <td style="padding: 0.875rem 1rem;">
                                <span style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-700);">
                                    {{ ucfirst($tugas->kompleksitas?->value ?? 'Sedang') }}
                                </span>
                            </td>
                            <td style="padding: 0.875rem 1rem; font-size: 0.8rem; color: var(--neutral-800);">
                                {{ $tugas->deadline?->format('d M Y') }}
                            </td>
                            <td style="padding: 0.875rem 1rem;">
                                @if($tugas->status->value === 'selesai')
                                    <span style="font-size: 0.75rem; font-weight: 600; background: #d1fae5; color: #065f46; padding: 0.15rem 0.5rem; border-radius: 999px;">
                                        Selesai
                                    </span>
                                @elseif($tugas->status->value === 'proses')
                                    <span style="font-size: 0.75rem; font-weight: 600; background: #fef3c7; color: #92400e; padding: 0.15rem 0.5rem; border-radius: 999px;">
                                        Proses
                                    </span>
                                @else
                                    <span style="font-size: 0.75rem; font-weight: 500; background: var(--neutral-100); color: var(--neutral-600); padding: 0.15rem 0.5rem; border-radius: 999px;">
                                        Belum
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 0.875rem 1.5rem; text-align: right;">
                                <a href="{{ route('pembimbing.tugas.show', $tugas->id) }}" style="font-size: 0.75rem; font-weight: 600; color: #0641CC; text-decoration: none;">
                                    Detail &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 2rem 1.5rem; text-align: center; color: var(--neutral-500);">
                                Belum ada penugasan yang dibuat untuk mentee ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
