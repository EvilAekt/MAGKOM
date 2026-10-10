@extends('layouts.dashboard')

@section('title', 'Dashboard Eksekutif - SuperAdmin MagKom')
@section('page_title', 'Dashboard Eksekutif Tata Kelola MagKom')
@section('page_description', 'Ikhtisar menyeluruh kapasitas peserta, tata kelola staf, pemantauan slot khusus, dan jejak audit trail.')

@section('breadcrumbs')
    <span class="breadcrumb-current">Dashboard SuperAdmin</span>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.75rem;">

    <!-- 1. EXECUTIVE SUMMARY STAT CARDS -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;">
        <!-- Card 1: Peserta Aktif -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle); display: flex; align-items: center; justify-content: space-between; border-left: 4px solid var(--blue-600);">
            <div>
                <span style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-600); display: block; margin-bottom: 0.25rem;">Peserta Magang Aktif</span>
                <span style="font-size: 1.875rem; font-weight: 800; color: var(--neutral-800); line-height: 1.1;">{{ number_format($totalPesertaAktif) }}</span>
                <span style="font-size: 0.75rem; color: var(--blue-600); font-weight: 600; display: block; margin-top: 0.25rem;">
                    {{ $totalPesertaJalurUmum }} Jalur Umum · {{ $totalPesertaJalurKhusus }} Jalur Khusus
                </span>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 10px; background: var(--blue-50); color: var(--blue-600); display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
        </div>

        <!-- Card 2: Total Alumni Selesai & Mundur -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle); display: flex; align-items: center; justify-content: space-between; border-left: 4px solid var(--green-600);">
            <div>
                <span style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-600); display: block; margin-bottom: 0.25rem;">Total Alumni Magang</span>
                <span style="font-size: 1.875rem; font-weight: 800; color: var(--neutral-800); line-height: 1.1;">{{ number_format($totalAlumni) }}</span>
                <span style="font-size: 0.75rem; color: var(--neutral-600); font-weight: 600; display: block; margin-top: 0.25rem;">
                    <span style="color: var(--green-700);">Selesai</span> · <span style="color: var(--red-700);">{{ number_format($totalPesertaMundur) }} Mengundurkan Diri</span>
                </span>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 10px; background: var(--green-100); color: var(--green-700); display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="21 8 21 21 3 21 3 8"></polyline>
                    <rect x="1" y="3" width="22" height="5"></rect>
                    <line x1="10" y1="12" x2="14" y2="12"></line>
                </svg>
            </div>
        </div>

        <!-- Card 3: Staf Internal (Admin & Pembimbing) -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle); display: flex; align-items: center; justify-content: space-between; border-left: 4px solid var(--blue-700);">
            <div>
                <span style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-600); display: block; margin-bottom: 0.25rem;">Staf Dinas Aktif</span>
                <span style="font-size: 1.875rem; font-weight: 800; color: var(--neutral-800); line-height: 1.1;">{{ number_format($totalAdmin + $totalPembimbing) }}</span>
                <span style="font-size: 0.75rem; color: var(--neutral-600); font-weight: 600; display: block; margin-top: 0.25rem;">{{ $totalAdmin }} Admin · {{ $totalPembimbing }} Pembimbing</span>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 10px; background: var(--neutral-200); color: var(--blue-700); display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="8.5" cy="7" r="4"></circle>
                    <line x1="20" y1="8" x2="20" y2="14"></line>
                    <line x1="23" y1="11" x2="17" y2="11"></line>
                </svg>
            </div>
        </div>

        <!-- Card 4: Slot Khusus vs Kuota Umum -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle); display: flex; align-items: center; justify-content: space-between; border-left: 4px solid var(--amber-500);">
            <div>
                <span style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-600); display: block; margin-bottom: 0.25rem;">Slot Khusus Aktif</span>
                <span style="font-size: 1.875rem; font-weight: 800; color: var(--neutral-800); line-height: 1.1;">
                    {{ $slotKhusus ? $slotKhusus->terisi . ' / ' . $slotKhusus->jumlah : '0 / 0' }}
                </span>
                <span style="font-size: 0.75rem; color: var(--amber-700); font-weight: 600; display: block; margin-top: 0.25rem;">
                    Kuota Umum: {{ $totalKuotaTerisi }}/{{ $totalKuotaUmum }}
                </span>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 10px; background: var(--amber-100); color: var(--amber-700); display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    <polyline points="9 12 11 14 15 10"></polyline>
                </svg>
            </div>
        </div>
    </div>

    <!-- 1B. ALERT/INFO ATENSI EKSEKUTIF UNTUK ANTREAN PENDAFTARAN -->
    @if($totalAntreanDiajukan > 0 || $totalWaitingList > 0)
        <div style="background: #FFFBEB; border: 1px solid #FCD34D; border-left: 4px solid var(--amber-500); border-radius: 8px; padding: 1rem 1.25rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <div>
                    <span style="font-size: 0.875rem; font-weight: 700; color: #92400E; display: block;">Perhatian Antrean Pendaftaran</span>
                    <span style="font-size: 0.8125rem; color: #78350F;">
                        Terdapat <strong>{{ $totalAntreanDiajukan }}</strong> berkas baru menunggu verifikasi seleksi dan <strong>{{ $totalWaitingList }}</strong> pendaftar berstatus antrean kuota (waiting list).
                    </span>
                </div>
            </div>
            <a href="{{ route('admin.pendaftaran.index') }}" style="font-size: 0.8125rem; font-weight: 700; color: #92400E; text-decoration: underline; white-space: nowrap;">
                Tinjau Antrean Pendaftaran &rarr;
            </a>
        </div>
    @endif

    <!-- 2. QUICK MANAGEMENT ACTIONS -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle);">
        <h3 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.875rem; display: flex; align-items: center; gap: 0.5rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
            Akses Tata Kelola & Kontrol Sistem
        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.875rem;">
            <a href="{{ route('superadmin.bulan.index') }}" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 8px; text-decoration: none; color: var(--neutral-800);">
                <div style="width: 36px; height: 36px; border-radius: 6px; background: var(--blue-700); color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                </div>
                <div>
                    <span style="font-weight: 700; font-size: 0.875rem; display: block;">Bulan Magang</span>
                    <span style="font-size: 0.75rem; color: var(--neutral-600);">Periode & override</span>
                </div>
            </a>

            <a href="{{ route('superadmin.slot-khusus.index') }}" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 8px; text-decoration: none; color: var(--neutral-800);">
                <div style="width: 36px; height: 36px; border-radius: 6px; background: var(--amber-600); color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><polyline points="9 12 11 14 15 10"></polyline></svg>
                </div>
                <div>
                    <span style="font-weight: 700; font-size: 0.875rem; display: block;">Slot Khusus</span>
                    <span style="font-size: 0.75rem; color: var(--neutral-600);">Kemitraan internal</span>
                </div>
            </a>

            <a href="{{ route('superadmin.staf.index') }}" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 8px; text-decoration: none; color: var(--neutral-800);">
                <div style="width: 36px; height: 36px; border-radius: 6px; background: var(--blue-600); color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
                </div>
                <div>
                    <span style="font-weight: 700; font-size: 0.875rem; display: block;">Manajemen Staf</span>
                    <span style="font-size: 0.75rem; color: var(--neutral-600);">Akun & pembimbing</span>
                </div>
            </a>

            <a href="{{ route('superadmin.pengaturan.index') }}" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 8px; text-decoration: none; color: var(--neutral-800);">
                <div style="width: 36px; height: 36px; border-radius: 6px; background: var(--neutral-800); color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                </div>
                <div>
                    <span style="font-weight: 700; font-size: 0.875rem; display: block;">Pengaturan Sistem</span>
                    <span style="font-size: 0.75rem; color: var(--neutral-600);">Predikat & batas beban</span>
                </div>
            </a>
        </div>
    </div>

    <!-- 3. TWO COLUMN GRID: BEBAN PEMBIMBING & DEMOGRAFI PESERTA -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(420px, 1fr)); gap: 1.5rem;">
        <!-- Column 1: Utilisasi Beban Pembimbing Lapangan (BR-13) -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-800); margin: 0;">
                    Beban Pembimbing Lapangan
                </h3>
                <span style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-600); background: var(--neutral-200); padding: 0.2rem 0.5rem; border-radius: 4px;">
                    Batas: {{ $maksBeban ? $maksBeban . ' Mentee' : 'Tanpa Batas' }}
                </span>
            </div>

            @if($pembimbingList->isEmpty())
                <p style="font-size: 0.875rem; color: var(--neutral-600); margin: 0;">Belum ada staf pembimbing aktif yang terdaftar.</p>
            @else
                <div style="display: flex; flex-direction: column; gap: 0.875rem;">
                    @foreach($pembimbingList as $p)
                        @php
                            $menteeCount = $p->mentee_aktif_count;
                            $maks = $maksBeban ?: 10;
                            $persenBeban = min(100, round(($menteeCount / $maks) * 100));
                            $isOverload = $maksBeban && $menteeCount >= $maksBeban;
                        @endphp
                        <div style="border: 1px solid var(--neutral-300); border-radius: 8px; padding: 0.875rem; background: var(--neutral-150);">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.375rem;">
                                <div>
                                    <span style="font-weight: 700; font-size: 0.875rem; color: var(--neutral-800); display: block;">{{ $p->nama }}</span>
                                    <small style="color: var(--neutral-600);">{{ $p->email }}</small>
                                </div>
                                <span style="font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 4px; background: {{ $isOverload ? 'var(--red-100)' : 'var(--blue-50)' }}; color: {{ $isOverload ? 'var(--red-800)' : 'var(--blue-700)' }};">
                                    {{ $menteeCount }} Mentee Aktif
                                </span>
                            </div>
                            <div style="width: 100%; height: 6px; background: var(--neutral-300); border-radius: 999px; overflow: hidden; margin-top: 0.375rem;">
                                <div style="width: {{ $persenBeban }}%; height: 100%; background: {{ $isOverload ? 'var(--red-600)' : 'var(--blue-600)' }}; border-radius: 999px;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Column 2: Demografi Pendidikan & 5 Kampus/Sekolah Terbanyak (PRD 4.3) -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
            <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-800); margin: 0 0 1rem 0;">
                Demografi Asal Institusi & Jenjang
            </h3>

            <!-- Jenjang Chips -->
            <div style="display: flex; gap: 0.625rem; margin-bottom: 1.25rem; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 100px; padding: 0.625rem 0.75rem; border-radius: 8px; background: var(--blue-50); border: 1px solid var(--blue-600); text-align: center;">
                    <span style="font-size: 0.75rem; font-weight: 600; color: var(--blue-700); display: block;">SMK</span>
                    <span style="font-size: 1.125rem; font-weight: 800; color: var(--blue-700);">{{ $jenjangRaw['smk'] ?? 0 }}</span>
                </div>
                <div style="flex: 1; min-width: 100px; padding: 0.625rem 0.75rem; border-radius: 8px; background: var(--green-100); border: 1px solid var(--green-600); text-align: center;">
                    <span style="font-size: 0.75rem; font-weight: 600; color: var(--green-800); display: block;">Diploma 3</span>
                    <span style="font-size: 1.125rem; font-weight: 800; color: var(--green-800);">{{ $jenjangRaw['d3'] ?? 0 }}</span>
                </div>
                <div style="flex: 1; min-width: 100px; padding: 0.625rem 0.75rem; border-radius: 8px; background: #F3E8FF; border: 1px solid #7E22CE; text-align: center;">
                    <span style="font-size: 0.75rem; font-weight: 600; color: #7E22CE; display: block;">Sarjana S1</span>
                    <span style="font-size: 1.125rem; font-weight: 800; color: #7E22CE;">{{ $jenjangRaw['s1'] ?? 0 }}</span>
                </div>
            </div>

            <!-- Top 5 Institusi List -->
            <span style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-700); display: block; margin-bottom: 0.5rem;">5 Asal Kampus / Sekolah Terbanyak:</span>
            @if($topInstitusi->isEmpty())
                <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0;">Belum ada data pendaftar terisi.</p>
            @else
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    @foreach($topInstitusi as $idx => $inst)
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8125rem; padding: 0.35rem 0.5rem; background: var(--neutral-150); border-radius: 6px;">
                            <span style="color: var(--neutral-800); font-weight: 600;">
                                {{ $idx + 1 }}. {{ $inst->institusi }}
                            </span>
                            <span style="font-weight: 800; color: var(--blue-700);">
                                {{ $inst->total }} Peserta
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- 4. FEED JEJAK AUDIT TRAIL MUTASI 5 TERBARU (AC-13-6) -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-subtle);">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-400); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
            <div>
                <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-800); margin: 0;">Jejak Audit Trail Terkini</h3>
                <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0.125rem 0 0 0;">Catatan mutasi database dan aksi terotorisasi terbaru (AC-13).</p>
            </div>
            <a href="{{ route('superadmin.audit-log.index') }}" style="font-size: 0.8125rem; font-weight: 700; color: var(--blue-600); text-decoration: none;">
                Buka Log Lengkap &rarr;
            </a>
        </div>

        @if($auditTerbaru->isEmpty())
            <div style="padding: 2.5rem; text-align: center; color: var(--neutral-600);">
                <p style="margin: 0; font-size: 0.875rem;">Belum ada catatan log aktivitas.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                    <thead>
                        <tr style="background: var(--neutral-200); border-bottom: 1px solid var(--neutral-400); height: 44px;">
                            <th style="padding: 0.5rem 1rem; font-weight: 700; color: var(--neutral-800);">Waktu</th>
                            <th style="padding: 0.5rem 1rem; font-weight: 700; color: var(--neutral-800);">Pengguna</th>
                            <th style="padding: 0.5rem 1rem; font-weight: 700; color: var(--neutral-800);">Tipe</th>
                            <th style="padding: 0.5rem 1rem; font-weight: 700; color: var(--neutral-800);">Entitas / Tabel</th>
                            <th style="padding: 0.5rem 1rem; font-weight: 700; color: var(--neutral-800);">Deskripsi Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($auditTerbaru as $log)
                            <tr style="border-bottom: 1px solid var(--neutral-400); height: 48px;">
                                <td style="padding: 0.5rem 1rem; font-family: monospace; font-size: 0.8125rem; color: var(--neutral-700);">
                                    {{ $log->waktu?->format('d/m/Y H:i:s') ?? '-' }}
                                </td>
                                <td style="padding: 0.5rem 1rem; font-weight: 600; color: var(--neutral-800);">
                                    {{ $log->user?->nama ?? 'Sistem' }}
                                </td>
                                <td style="padding: 0.5rem 1rem;">
                                    <span style="font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 4px; background: var(--neutral-200); color: var(--neutral-800);">
                                        {{ strtoupper($log->tipe?->value ?? 'AKSI') }}
                                    </span>
                                </td>
                                <td style="padding: 0.5rem 1rem; color: var(--blue-700); font-weight: 600;">
                                    {{ $log->entitas ?? '-' }}
                                </td>
                                <td style="padding: 0.5rem 1rem; color: var(--neutral-700);">
                                    {{ $log->aksi ?? ($log->detail['aksi'] ?? '-') }}
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
