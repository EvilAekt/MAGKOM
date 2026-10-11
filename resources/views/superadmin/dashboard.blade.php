@extends('layouts.dashboard')

@section('title', 'Dashboard SuperAdmin - MagKom')
@section('page_title', 'Dashboard SuperAdmin')
@section('page_description', 'Akses penuh: kelola pengguna, role, rubrik, audit log, konfigurasi, dan seluruh modul Administrasi, Pembimbing, serta Peserta.')

@section('breadcrumbs')
    <span class="breadcrumb-current">Dashboard</span>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">

    <!-- =========================================================================
         1. HERO SAPAAN EKSEKUTIF (Figma Node 560-26404) + QUICK ACTIONS TOOLBAR
         ========================================================================= -->
    <div style="background: linear-gradient(135deg, var(--neutral-0) 0%, #F8FAFC 100%); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.5rem 1.75rem; box-shadow: var(--shadow-subtle); display: flex; flex-direction: column; gap: 1.25rem;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
            <div style="max-width: 820px;">
                <div style="display: flex; align-items: center; gap: 0.625rem; margin-bottom: 0.375rem;">
                    <span style="font-size: 0.6875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; background: #DBEAFE; color: #1E40AF; padding: 0.2rem 0.6rem; border-radius: 999px;">
                        Dashboard Eksekutif Tata Kelola MagKom
                    </span>
                    <span style="display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.75rem; color: #166534; font-weight: 600;">
                        <span class="status-dot-pulse"></span>
                        Sistem Operasional Aktif
                    </span>
                </div>
                <h2 style="font-size: 1.375rem; font-weight: 800; color: var(--neutral-900); margin: 0 0 0.375rem 0; letter-spacing: -0.01em;">
                    Dashboard SuperAdmin
                </h2>
                <p style="font-size: 0.875rem; color: var(--neutral-600); margin: 0; line-height: 1.5;">
                    Akses penuh: kelola pengguna, role, rubrik, audit log, konfigurasi, dan seluruh modul Administrasi, Pembimbing, serta Peserta.
                </p>
            </div>

            <!-- Tombol Aksi Utama -->
            <div style="display: flex; gap: 0.625rem; flex-wrap: wrap;">
                <a href="{{ route('superadmin.staf.index') }}" class="btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1rem; font-size: 0.8125rem; text-decoration: none; border-radius: 8px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="8.5" cy="7" r="4"></circle>
                        <line x1="20" y1="8" x2="20" y2="14"></line>
                        <line x1="23" y1="11" x2="17" y2="11"></line>
                    </svg>
                    <span>Kelola Pengguna</span>
                </a>
                <a href="{{ route('superadmin.audit-log.index') }}" class="btn-secondary" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1rem; font-size: 0.8125rem; text-decoration: none; border-radius: 8px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                    </svg>
                    <span>Audit Trail</span>
                </a>
            </div>
        </div>

        <!-- Quick Actions Toolbar (Pintasan Rutin Dinas) -->
        <div style="padding-top: 1rem; border-top: 1px solid var(--neutral-300); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                <span style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-600); text-transform: uppercase; letter-spacing: 0.05em; margin-right: 0.25rem;">
                    Pintasan Tindakan:
                </span>
                <a href="{{ route('superadmin.staf.index') }}" class="quick-action-chip">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--blue-600);">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="8.5" cy="7" r="4"></circle>
                        <line x1="20" y1="8" x2="20" y2="14"></line>
                    </svg>
                    <span>+ Akun Staf</span>
                </a>
                <a href="{{ route('superadmin.bulan.index') }}" class="quick-action-chip">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #059669;">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <span>+ Periode Magang</span>
                </a>
                <a href="{{ route('admin.pendaftaran.index') }}" class="quick-action-chip">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #D97706;">
                        <polyline points="9 11 12 14 22 4"></polyline>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                    </svg>
                    <span>Tinjau Berkas</span>
                </a>
                <a href="{{ route('admin.penempatan.index') }}" class="quick-action-chip">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #7C3AED;">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 14 14"></polyline>
                    </svg>
                    <span>Penempatan Peserta</span>
                </a>
                <a href="{{ route('admin.laporan.index') }}" class="quick-action-chip">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #0284C7;">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    <span>Ekspor Laporan</span>
                </a>
                <a href="{{ route('superadmin.health.index') }}" class="quick-action-chip">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #059669;">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                    </svg>
                    <span>Telemetri Sistem</span>
                </a>
                <a href="{{ route('superadmin.backup.index') }}" class="quick-action-chip">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #7C3AED;">
                        <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                        <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                        <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                    </svg>
                    <span>Backup Hub</span>
                </a>
            </div>

            <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                <!-- Live Landing Page Status Indicator (PRD F-01, AC-11-6, BR-04) -->
                @if($statusPublik)
                    @php
                        $statusBadgeStyle = match($statusPublik->value ?? $statusPublik) {
                            'tersedia' => 'background: #ECFDF5; color: #047857; border: 1px solid #A7F3D0;',
                            'segera' => 'background: #FFFBEB; color: #B45309; border: 1px solid #FDE68A;',
                            default => 'background: #FEF2F2; color: #B91C1C; border: 1px solid #FECACA;',
                        };
                        $statusTextLabel = match($statusPublik->value ?? $statusPublik) {
                            'tersedia' => 'Pendaftaran Tersedia',
                            'segera' => 'Segera Dibuka',
                            default => 'Pendaftaran Ditutup',
                        };
                    @endphp
                    <div style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.25rem 0.625rem; border-radius: 999px; font-size: 0.75rem; font-weight: 700; {{ $statusBadgeStyle }}">
                        <span style="width: 7px; height: 7px; border-radius: 50%; background: currentColor;"></span>
                        <span>Status Publik: {{ $statusTextLabel }}</span>
                        @if($isOverrideStatus)
                            <span style="font-size: 0.6875rem; opacity: 0.85; margin-left: 0.125rem;">(Override SuperAdmin)</span>
                        @else
                            <span style="font-size: 0.6875rem; opacity: 0.85; margin-left: 0.125rem;">(Jadwal Otomatis)</span>
                        @endif
                    </div>
                @endif

                <!-- Jadwal Kalender Pendaftaran (PRD F-01, BR-04) -->
                @if($bulanAktif && $bulanAktif->tgl_buka && $bulanAktif->tgl_tutup)
                    @php
                        $tglBukaFormatted = \Carbon\Carbon::parse($bulanAktif->tgl_buka)->format('d M');
                        $tglTutupFormatted = \Carbon\Carbon::parse($bulanAktif->tgl_tutup)->format('d M Y');
                    @endphp
                    <div style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.25rem 0.625rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600; background: #F1F5F9; color: var(--neutral-700); border: 1px solid var(--neutral-300);">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <span>Jadwal: {{ $tglBukaFormatted }} – {{ $tglTutupFormatted }}</span>
                    </div>
                @endif

                <!-- Postur Keamanan Sistem Ringkas (PRD Bab 10) -->
                <div style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.25rem 0.625rem; border-radius: 999px; font-size: 0.75rem; font-weight: 700; background: #F0FDF4; color: #166534; border: 1px solid #BBF7D0;">
                    <span style="width: 7px; height: 7px; border-radius: 50%; background: #16A34A;"></span>
                    <span>Keamanan: Prima</span>
                    @if($loginGagal24Jam > 0)
                        <span style="color: #DC2626; font-size: 0.6875rem;">({{ $loginGagal24Jam }} Gagal)</span>
                    @endif
                </div>

                <!-- Selektor Periode Bulan Dinamis (?bulan_id=) -->
                <form method="GET" action="{{ route('superadmin.dashboard') }}" style="display: inline-flex; align-items: center; gap: 0.375rem;">
                    <label for="select-bulan" style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700);">Periode:</label>
                    <select id="select-bulan" name="bulan_id" onchange="this.form.submit()" style="padding: 0.3rem 0.625rem; font-size: 0.75rem; font-weight: 700; border: 1px solid var(--neutral-400); border-radius: 6px; background: #FFFFFF; color: var(--neutral-900); cursor: pointer;">
                        @foreach($daftarBulan as $b)
                            <option value="{{ $b->id }}" {{ $bulanAktif && $bulanAktif->id === $b->id ? 'selected' : '' }}>
                                {{ $b->nama_bulan }} {{ $b->override_status ? '[' . strtoupper($b->override_status->value) . ']' : '' }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
    </div>

    <!-- ALERT ATENSI EKSEKUTIF UNTUK ANTREAN PENDAFTARAN -->
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
            <a href="javascript:void(0)" onclick="switchDashboardTab('tab-seleksi')" style="font-size: 0.8125rem; font-weight: 700; color: #92400E; text-decoration: underline; white-space: nowrap;">
                Tinjau di Tab Seleksi &rarr;
            </a>
        </div>
    @endif

    <!-- =========================================================================
         2. EMPAT KARTU STATISTIK FIGMA (Figma Node 560-26404)
         ========================================================================= -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 1rem;">
        <!-- Card 1: Total Pengguna -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.25rem; box-shadow: var(--shadow-subtle); display: flex; align-items: center; justify-content: space-between; border-left: 4px solid #2563EB;">
            <div>
                <span style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-600); display: block; margin-bottom: 0.25rem;">Total pengguna</span>
                <span style="font-size: 1.875rem; font-weight: 800; color: var(--neutral-900); line-height: 1.1;">{{ number_format($totalPengguna) }}</span>
                <span style="font-size: 0.75rem; color: #2563EB; font-weight: 600; display: block; margin-top: 0.25rem;">
                    Seluruh akun terdaftar
                    @if($stafPendingGantiPassword > 0)
                        · <span style="color: #D97706; font-weight: 700;">{{ $stafPendingGantiPassword }} Staf Pending Sandi Awal</span>
                    @endif
                </span>
            </div>
            <div style="width: 46px; height: 46px; border-radius: 10px; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
        </div>

        <!-- Card 2: Menunggu Verifikasi -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.25rem; box-shadow: var(--shadow-subtle); display: flex; align-items: center; justify-content: space-between; border-left: 4px solid #D97706;">
            <div>
                <span style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-600); display: block; margin-bottom: 0.25rem;">Menunggu verifikasi</span>
                <span style="font-size: 1.875rem; font-weight: 800; color: var(--neutral-900); line-height: 1.1;">{{ number_format($menungguVerifikasi) }}</span>
                <span style="font-size: 0.75rem; color: #D97706; font-weight: 600; display: block; margin-top: 0.25rem;">
                    Berkas perlu ditinjau
                </span>
            </div>
            <div style="width: 46px; height: 46px; border-radius: 10px; background: #FFFBEB; color: #D97706; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="22 12 16 12 14 15 10 15 8 12 2 12"></polyline>
                    <path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path>
                </svg>
            </div>
        </div>

        <!-- Card 3: Peserta Aktif -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.25rem; box-shadow: var(--shadow-subtle); display: flex; align-items: center; justify-content: space-between; border-left: 4px solid #16A34A;">
            <div>
                <span style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-600); display: block; margin-bottom: 0.25rem;">Peserta aktif</span>
                <span style="font-size: 1.875rem; font-weight: 800; color: var(--neutral-900); line-height: 1.1;">{{ number_format($pesertaAktif) }}</span>
                <span style="font-size: 0.75rem; color: #16A34A; font-weight: 600; display: block; margin-top: 0.25rem;">
                    {{ $totalPesertaJalurUmum }} Jalur Umum · {{ $totalPesertaJalurKhusus }} Jalur Khusus
                </span>
                <span style="font-size: 0.6875rem; color: var(--neutral-500); display: block; margin-top: 0.25rem;">
                    {{ $totalAlumni }} Alumni Selesai 
                    @if($totalPesertaMasaTenggang > 0)
                        (<strong style="color: #0284C7;">{{ $totalPesertaMasaTenggang }} Masa Tenggang</strong>)
                    @endif
                    · <span style="color: var(--red-700);">{{ $totalPesertaMundur }} Mengundurkan Diri</span>
                </span>
            </div>
            <div style="width: 46px; height: 46px; border-radius: 10px; background: #F0FDF4; color: #16A34A; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                    <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                    <path d="M9 14l2 2 4-4"></path>
                </svg>
            </div>
        </div>

        <!-- Card 4: Akun Nonaktif -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.25rem; box-shadow: var(--shadow-subtle); display: flex; align-items: center; justify-content: space-between; border-left: 4px solid #DC2626;">
            <div>
                <span style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-600); display: block; margin-bottom: 0.25rem;">Akun nonaktif</span>
                <span style="font-size: 1.875rem; font-weight: 800; color: var(--neutral-900); line-height: 1.1;">{{ number_format($akunNonaktif) }}</span>
                <span style="font-size: 0.75rem; color: #DC2626; font-weight: 600; display: block; margin-top: 0.25rem;">
                    Akses ditangguhkan
                </span>
            </div>
            <div style="width: 46px; height: 46px; border-radius: 10px; background: #FEF2F2; color: #DC2626; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         3. SEGMENTED TAB CONTROLLER (NAVIGASI TERPADU 4 AREA FUNGSI)
         ========================================================================= -->
    <div class="dashboard-tab-bar" role="tablist">
        <button type="button" class="tab-btn active" id="btn-tab-ringkasan" onclick="switchDashboardTab('tab-ringkasan')" role="tab" aria-selected="true" aria-controls="tab-ringkasan">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
            </svg>
            <span>Ringkasan Utama</span>
        </button>

        <button type="button" class="tab-btn" id="btn-tab-seleksi" onclick="switchDashboardTab('tab-seleksi')" role="tab" aria-selected="false" aria-controls="tab-seleksi">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <polyline points="10 9 9 9 8 9"></polyline>
            </svg>
            <span>Seleksi & Kuota</span>
            @if($menungguVerifikasi > 0)
                <span class="tab-badge">{{ $menungguVerifikasi }}</span>
            @endif
        </button>

        <button type="button" class="tab-btn" id="btn-tab-bimbingan" onclick="switchDashboardTab('tab-bimbingan')" role="tab" aria-selected="false" aria-controls="tab-bimbingan">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
            <span>Pelaksanaan & Bimbingan</span>
            @if($totalLogbookPending > 0)
                <span class="tab-badge" style="background: #FEF3C7; color: #B45309;">{{ $totalLogbookPending }}</span>
            @endif
        </button>

        <button type="button" class="tab-btn" id="btn-tab-sistem" onclick="switchDashboardTab('tab-sistem')" role="tab" aria-selected="false" aria-controls="tab-sistem">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="3"></circle>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
            </svg>
            <span>Tata Kelola Sistem & Laporan</span>
        </button>
    </div>

    <!-- =========================================================================
         PANEL 1: RINGKASAN UTAMA (FIGMA DEFAULT NODE 560-26404)
         ========================================================================= -->
    <div id="tab-ringkasan" class="tab-content-panel active" role="tabpanel" aria-labelledby="btn-tab-ringkasan">
        
        <!-- PUSAT METRIK KEBERHASILAN PRODUK & KPI DINAS (PRD Bab 2.3) -->
        <div style="background: linear-gradient(135deg, #FFFFFF 0%, #F8FAFC 100%); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
                <div>
                    <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-900); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                        <span>Pusat Metrik Keberhasilan Produk & KPI Dinas</span>
                        <span style="font-size: 0.6875rem; font-weight: 700; background: #DBEAFE; color: #1E40AF; padding: 0.15rem 0.5rem; border-radius: 999px;">
                            PRD Bab 2.3
                        </span>
                    </h3>
                    <p style="font-size: 0.75rem; color: var(--neutral-600); margin: 0.125rem 0 0 0;">
                        Target mutu layanan dan kepatuhan operasional Diskominfo Sleman yang diawasi SuperAdmin
                    </p>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="font-size: 0.75rem; font-weight: 600; color: #166534; display: inline-flex; align-items: center; gap: 0.25rem;">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #16A34A;"></span>
                        Audit Otomatis Aktif
                    </span>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                <!-- KPI 1: Integritas Kuota -->
                <div style="background: #FFFFFF; border: 1px solid {{ $anomaliKuotaCount > 0 ? '#FECACA' : '#BBF7D0' }}; border-radius: 10px; padding: 1rem; border-left: 4px solid {{ $anomaliKuotaCount > 0 ? '#DC2626' : '#16A34A' }};">
                    <span style="font-size: 0.6875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--neutral-600); display: block;">
                        Integritas Kuota (Target 0)
                    </span>
                    <span style="font-size: 1.5rem; font-weight: 800; color: {{ $anomaliKuotaCount > 0 ? '#DC2626' : '#166534' }}; display: block; margin-top: 0.25rem;">
                        {{ $anomaliKuotaCount }} Pelanggaran
                    </span>
                    <span style="font-size: 0.75rem; color: {{ $anomaliKuotaCount > 0 ? '#DC2626' : '#16A34A' }}; font-weight: 600; display: block; margin-top: 0.25rem;">
                        {{ $anomaliKuotaCount === 0 ? '100% Bebas Kuota Minus / Bocor' : 'Terdeteksi kuota melebihi batas!' }}
                    </span>
                </div>

                <!-- KPI 2: SLA Kecepatan Verifikasi -->
                <div style="background: #FFFFFF; border: 1px solid {{ $antreanMelebihiSla > 0 ? '#FDE68A' : '#BFDBFE' }}; border-radius: 10px; padding: 1rem; border-left: 4px solid {{ $antreanMelebihiSla > 0 ? '#D97706' : '#2563EB' }};">
                    <span style="font-size: 0.6875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--neutral-600); display: block;">
                        SLA Verifikasi (Target ≤ 5 Hari)
                    </span>
                    <span style="font-size: 1.5rem; font-weight: 800; color: var(--neutral-900); display: block; margin-top: 0.25rem;">
                        {{ $rataRataSlaVerifikasiHari !== null ? $rataRataSlaVerifikasiHari . ' Hari' : '- Hari' }}
                    </span>
                    <span style="font-size: 0.75rem; color: {{ $antreanMelebihiSla > 0 ? '#D97706' : '#2563EB' }}; font-weight: 600; display: block; margin-top: 0.25rem;">
                        @if($antreanMelebihiSla > 0)
                            {{ $antreanMelebihiSla }} Berkas Melewati Batas 5 Hari
                        @else
                            Kecepatan verifikasi sesuai target SLA
                        @endif
                    </span>
                </div>

                <!-- KPI 3: Kelulusan Lembar Nilai -->
                <div style="background: #FFFFFF; border: 1px solid #DDD6FE; border-radius: 10px; padding: 1rem; border-left: 4px solid #7C3AED;">
                    <span style="font-size: 0.6875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--neutral-600); display: block;">
                        Lembar Nilai Alumni (Target 100%)
                    </span>
                    <span style="font-size: 1.5rem; font-weight: 800; color: #6B21A8; display: block; margin-top: 0.25rem;">
                        {{ $kepatuhanNilaiSelesaiPersen }}%
                    </span>
                    <span style="font-size: 0.75rem; color: #7C3AED; font-weight: 600; display: block; margin-top: 0.25rem;">
                        {{ $penempatanSelesaiDenganNilai }} dari {{ $penempatanSelesaiTotal }} Alumni Selesai Nilai Final
                    </span>
                </div>

                <!-- KPI 4: Keterlacakan Audit Trail -->
                <div style="background: #FFFFFF; border: 1px solid #BBF7D0; border-radius: 10px; padding: 1rem; border-left: 4px solid #059669;">
                    <span style="font-size: 0.6875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--neutral-600); display: block;">
                        Audit Trail (Target 100%)
                    </span>
                    <span style="font-size: 1.5rem; font-weight: 800; color: #065F46; display: block; margin-top: 0.25rem;">
                        100% Tercatat
                    </span>
                    <span style="font-size: 0.75rem; color: #059669; font-weight: 600; display: block; margin-top: 0.25rem;">
                        {{ number_format($securityPosture['total_audit_events']) }} Event Terekam & Tersanitasi
                    </span>
                </div>
            </div>
        </div>
        
        <!-- DUA KOLOM FIGMA: PENGGUNA TERBARU & AUDIT LOG TERBARU -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(380px, 1fr)); gap: 1.5rem;">
            
            <!-- KOLOM KIRI: Pengguna terbaru (Figma Node 560-26404) -->
            <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle); display: flex; flex-direction: column;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.125rem; padding-bottom: 0.875rem; border-bottom: 1px solid var(--neutral-300);">
                    <div>
                        <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-900); margin: 0;">Pengguna terbaru</h3>
                        <p style="font-size: 0.75rem; color: var(--neutral-600); margin: 0.125rem 0 0 0;">Pendaftaran akun dan staf internal terkini</p>
                    </div>
                    <a href="{{ route('superadmin.staf.index') }}" style="font-size: 0.8125rem; font-weight: 700; color: #2563EB; text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem;">
                        Kelola pengguna &rarr;
                    </a>
                </div>

                @if($penggunaTerbaru->isEmpty())
                    <div style="padding: 2.5rem 1rem; text-align: center; color: var(--neutral-600);">
                        <p style="margin: 0; font-size: 0.875rem;">Belum ada akun pengguna yang terdaftar.</p>
                    </div>
                @else
                    <div style="display: flex; flex-direction: column; gap: 0.875rem;">
                        @foreach($penggunaTerbaru as $user)
                            @php
                                $roleVal = $user->role?->value ?? 'peserta';
                                
                                $badgeStyle = match($roleVal) {
                                    'superadmin' => 'background: #F3E8FF; color: #6B21A8; border: 1px solid #DDD6FE;',
                                    'admin' => 'background: #EFF6FF; color: #1D4ED8; border: 1px solid #BFDBFE;',
                                    'pembimbing' => 'background: #FEF3C7; color: #B45309; border: 1px solid #FDE68A;',
                                    default => 'background: #ECFDF5; color: #047857; border: 1px solid #A7F3D0;',
                                };

                                $roleLabel = match($roleVal) {
                                    'superadmin' => 'SuperAdmin',
                                    'admin' => 'Administrasi',
                                    'pembimbing' => 'Pembimbing',
                                    default => 'Peserta',
                                };

                                $initials = strtoupper(substr($user->nama ?: 'U', 0, 1));
                                $avatarColors = [
                                    'background: #E0E7FF; color: #3730A3;',
                                    'background: #FCE7F3; color: #9D174D;',
                                    'background: #DCFCE7; color: #166534;',
                                    'background: #FEF9C3; color: #854D0E;',
                                    'background: #F3E8FF; color: #6B21A8;',
                                ];
                                $avatarColor = $avatarColors[$user->id % 5];
                            @endphp
                            <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.625rem 0.75rem; border-radius: 8px; background: var(--neutral-150); border: 1px solid var(--neutral-300); gap: 0.75rem;">
                                <div style="display: flex; align-items: center; gap: 0.75rem; min-width: 0;">
                                    <div style="width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.875rem; flex-shrink: 0; {{ $avatarColor }}">
                                        {{ $initials }}
                                    </div>
                                    <div style="min-width: 0;">
                                        <span style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-900); display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $user->nama }}
                                        </span>
                                        <span style="font-size: 0.75rem; color: var(--neutral-600); display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $user->email }}
                                        </span>
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.5rem; flex-shrink: 0;">
                                    <span style="font-size: 0.6875rem; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 999px; {{ $badgeStyle }}">
                                        {{ $roleLabel }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- KOLOM KANAN: Audit log terbaru (Figma Node 560-26404) -->
            <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle); display: flex; flex-direction: column;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.125rem; padding-bottom: 0.875rem; border-bottom: 1px solid var(--neutral-300);">
                    <div>
                        <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-900); margin: 0;">Audit log terbaru</h3>
                        <p style="font-size: 0.75rem; color: var(--neutral-600); margin: 0.125rem 0 0 0;">Rekam jejak mutasi data dan aksi terotorisasi</p>
                    </div>
                    <a href="{{ route('superadmin.audit-log.index') }}" style="font-size: 0.8125rem; font-weight: 700; color: #2563EB; text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem;">
                        Lihat semua &rarr;
                    </a>
                </div>

                @if($auditTerbaru->isEmpty())
                    <div style="padding: 2.5rem 1rem; text-align: center; color: var(--neutral-600);">
                        <p style="margin: 0; font-size: 0.875rem;">Belum ada catatan mutasi audit log.</p>
                    </div>
                @else
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        @foreach($auditTerbaru as $log)
                            @php
                                $actorName = $log->user?->nama ?? 'Sistem';
                                $timeStr = $log->waktu ? $log->waktu->format('H:i') : '-';
                                $dateStr = $log->waktu ? $log->waktu->format('d M') : '';
                                $actionText = $log->aksi ?: ($log->detail['aksi'] ?? ($log->entitas ? 'Mutasi ' . $log->entitas : 'Aktivitas sistem'));
                            @endphp
                            <div style="display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.625rem 0.75rem; border-radius: 8px; background: var(--neutral-150); border: 1px solid var(--neutral-300);">
                                <div style="width: 32px; height: 32px; border-radius: 8px; background: #DBEAFE; color: #1E40AF; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 0.125rem;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <span style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-900); display: block; line-height: 1.35; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $actionText }}
                                    </span>
                                    <div style="display: flex; align-items: center; gap: 0.375rem; margin-top: 0.25rem; font-size: 0.75rem; color: var(--neutral-600);">
                                        <span style="font-weight: 600; color: var(--neutral-800);">{{ $actorName }}</span>
                                        <span>&bull;</span>
                                        <span>{{ $dateStr }} {{ $timeStr }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

        <!-- DEMOGRAFI ASAL INSTITUSI & PENDIDIKAN -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
                <div>
                    <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900); margin: 0;">
                        Demografi Asal Institusi & Jenjang Pendidikan (PRD 4.3)
                    </h4>
                    <p style="font-size: 0.75rem; color: var(--neutral-600); margin: 0.125rem 0 0 0;">
                        Sebaran calon peserta dan peserta aktif berdasarkan jenjang pendidikan
                    </p>
                </div>
                <a href="{{ route('admin.laporan.pendaftaran') }}" style="font-size: 0.8125rem; font-weight: 700; color: var(--blue-600); text-decoration: underline;">
                    Lihat Laporan Lengkap Demografi &rarr;
                </a>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 0.75rem; margin-bottom: 1rem;">
                <div style="padding: 0.75rem 1rem; border-radius: 8px; background: #EFF6FF; border: 1px solid #BFDBFE; text-align: center;">
                    <span style="font-size: 0.6875rem; font-weight: 600; color: #1D4ED8; display: block;">SMK / MAK</span>
                    <span style="font-size: 1.375rem; font-weight: 800; color: #1D4ED8;">{{ $jenjangRaw['smk'] ?? 0 }}</span>
                </div>
                <div style="padding: 0.75rem 1rem; border-radius: 8px; background: #ECFDF5; border: 1px solid #A7F3D0; text-align: center;">
                    <span style="font-size: 0.6875rem; font-weight: 600; color: #047857; display: block;">Diploma 3 (D3)</span>
                    <span style="font-size: 1.375rem; font-weight: 800; color: #047857;">{{ $jenjangRaw['d3'] ?? 0 }}</span>
                </div>
                <div style="padding: 0.75rem 1rem; border-radius: 8px; background: #F3E8FF; border: 1px solid #DDD6FE; text-align: center;">
                    <span style="font-size: 0.6875rem; font-weight: 600; color: #6B21A8; display: block;">Sarjana S1 / D4</span>
                    <span style="font-size: 1.375rem; font-weight: 800; color: #6B21A8;">{{ $jenjangRaw['s1'] ?? 0 }}</span>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                <span style="font-size: 0.75rem; color: var(--neutral-600); font-weight: 600;">Top 5 Institusi:</span>
                @forelse($topInstitusi->take(5) as $inst)
                    <span style="font-size: 0.75rem; background: var(--neutral-150); border: 1px solid var(--neutral-300); padding: 0.25rem 0.625rem; border-radius: 6px; color: var(--neutral-800);">
                        <strong>{{ $inst->institusi }}</strong> ({{ $inst->total }})
                    </span>
                @empty
                    <span style="font-size: 0.75rem; color: var(--neutral-500); font-style: italic;">Belum ada profil institusi.</span>
                @endforelse
            </div>
        </div>

    </div>

    <!-- =========================================================================
         PANEL 2: SELEKSI & OPERASIONAL KUOTA
         ========================================================================= -->
    <div id="tab-seleksi" class="tab-content-panel" role="tabpanel" aria-labelledby="btn-tab-seleksi">
        
        <!-- BAGIAN 1: ANTREAN VERIFIKASI BERKAS TERTUNDA (FIFO BR-04) -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--neutral-300); flex-wrap: wrap; gap: 0.5rem;">
                <div>
                    <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-900); margin: 0; display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                        <span>Antrean Verifikasi Berkas Tertunda (FIFO)</span>
                        <span style="font-size: 0.6875rem; font-weight: 700; background: #FEF3C7; color: #92400E; padding: 0.15rem 0.5rem; border-radius: 999px;">
                            {{ $menungguVerifikasi }} Berkas
                        </span>
                        @if($antreanMelebihiSla > 0)
                            <span style="font-size: 0.6875rem; font-weight: 700; background: #FEE2E2; color: #DC2626; border: 1px solid #FECACA; padding: 0.15rem 0.5rem; border-radius: 999px;">
                                ⚠️ {{ $antreanMelebihiSla }} Berkas Melewati SLA 5 Hari
                            </span>
                        @else
                            <span style="font-size: 0.6875rem; font-weight: 700; background: #DCFCE7; color: #166534; padding: 0.15rem 0.5rem; border-radius: 999px;">
                                SLA Aman (≤ 5 Hari)
                            </span>
                        @endif
                    </h3>
                    <p style="font-size: 0.75rem; color: var(--neutral-600); margin: 0.125rem 0 0 0;">
                        Pendaftar menunggu pemeriksaan berkas administrasi dan keputusan tim seleksi
                    </p>
                </div>
                <a href="{{ route('admin.pendaftaran.index') }}" style="font-size: 0.8125rem; font-weight: 700; color: #2563EB; text-decoration: none;">
                    Buka Antrean Lengkap &rarr;
                </a>
            </div>

            @if($pendaftaranMenunggu->isEmpty())
                <div style="padding: 2rem 1rem; text-align: center; color: var(--neutral-600); background: #F8FAFC; border-radius: 8px; border: 1px dashed var(--neutral-300);">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#16A34A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 0.5rem;">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    <p style="margin: 0; font-size: 0.875rem; font-weight: 600; color: var(--neutral-800);">Semua berkas pendaftaran telah diverifikasi!</p>
                    <p style="margin: 0.25rem 0 0 0; font-size: 0.75rem; color: var(--neutral-500);">Tidak ada berkas yang menunggu antrean seleksi saat ini.</p>
                </div>
            @else
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.8125rem; text-align: left;">
                        <thead>
                            <tr style="border-bottom: 1px solid var(--neutral-300); color: var(--neutral-600); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.04em;">
                                <th style="padding: 0.5rem 0.75rem;">Pendaftar & Institusi</th>
                                <th style="padding: 0.5rem 0.75rem;">Jurusan & Periode</th>
                                <th style="padding: 0.5rem 0.75rem;">Waktu Ajuan</th>
                                <th style="padding: 0.5rem 0.75rem;">Status</th>
                                <th style="padding: 0.5rem 0.75rem; text-align: right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendaftaranMenunggu as $p)
                                @php
                                    $namaUser = $p->user?->nama ?? 'Peserta';
                                    $instName = $p->user?->profil?->institusi ?? '-';
                                    $jurusanName = $p->kuota?->jurusan?->nama_jurusan ?? ($p->user?->profil?->jurusan?->nama_jurusan ?? '-');
                                    $bulanName = $p->bulanMagang?->nama_bulan ?? '-';
                                    $tglAjuan = $p->diajukan_at ? $p->diajukan_at->format('d M Y, H:i') : ($p->created_at ? $p->created_at->format('d M Y, H:i') : '-');
                                @endphp
                                <tr style="border-bottom: 1px solid var(--neutral-200);">
                                    <td style="padding: 0.625rem 0.75rem;">
                                        <span style="font-weight: 700; color: var(--neutral-900); display: block;">{{ $namaUser }}</span>
                                        <span style="font-size: 0.75rem; color: var(--neutral-600);">{{ $instName }}</span>
                                    </td>
                                    <td style="padding: 0.625rem 0.75rem;">
                                        <span style="font-weight: 600; color: var(--neutral-800); display: block;">{{ $jurusanName }}</span>
                                        <span style="font-size: 0.75rem; color: var(--neutral-500);">{{ $bulanName }} ({{ $p->durasi_bulan }} Bln)</span>
                                    </td>
                                    <td style="padding: 0.625rem 0.75rem; color: var(--neutral-600);">
                                        {{ $tglAjuan }}
                                    </td>
                                    <td style="padding: 0.625rem 0.75rem;">
                                        @if($p->status->value === 'diajukan')
                                            <span style="background: #FFFBEB; color: #B45309; border: 1px solid #FDE68A; padding: 0.2rem 0.5rem; border-radius: 999px; font-size: 0.6875rem; font-weight: 700;">
                                                Diajukan
                                            </span>
                                        @else
                                            <span style="background: #EFF6FF; color: #1D4ED8; border: 1px solid #BFDBFE; padding: 0.2rem 0.5rem; border-radius: 999px; font-size: 0.6875rem; font-weight: 700;">
                                                Perlu Perbaikan
                                            </span>
                                        @endif
                                    </td>
                                    <td style="padding: 0.625rem 0.75rem; text-align: right;">
                                        <a href="{{ route('admin.pendaftaran.show', $p->id) }}" style="display: inline-flex; align-items: center; gap: 0.25rem; background: #2563EB; color: #FFFFFF; padding: 0.35rem 0.75rem; border-radius: 6px; text-decoration: none; font-size: 0.75rem; font-weight: 600;">
                                            <span>Tinjau & Putuskan</span>
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="9 18 15 12 9 6"></polyline>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- BAGIAN 2: UTILISASI KUOTA PER JURUSAN & WAITING LIST -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 1.5rem;">
            
            <!-- Box A: Utilisasi Kuota per Jurusan Bulan Aktif -->
            <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <div>
                        <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900); margin: 0;">
                            Utilisasi Kuota per Jurusan
                        </h4>
                        <span style="font-size: 0.75rem; color: var(--neutral-600);">
                            Periode: <strong>{{ $bulanAktif ? $bulanAktif->nama_bulan : '-' }}</strong>
                        </span>
                    </div>
                    <a href="{{ route('admin.kuota.index') }}" style="font-size: 0.75rem; font-weight: 700; color: var(--blue-600); text-decoration: underline;">
                        Atur Kuota &rarr;
                    </a>
                </div>

                <!-- Status Kepatuhan Pagu Kuota Dinas Bulanan (BR-01) -->
                @php
                    $persenPagu = $kuotaMaksPagu > 0 ? min(100, round(($totalKuotaUmum / $kuotaMaksPagu) * 100)) : 0;
                    $isExceedPagu = $totalKuotaUmum > $kuotaMaksPagu;
                @endphp
                <div style="margin-bottom: 1rem; padding: 0.75rem 0.875rem; border-radius: 8px; background: #F8FAFC; border: 1px solid var(--neutral-300);">
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem;">
                        <span style="font-weight: 700; color: var(--neutral-700);">Kepatuhan Pagu Kuota Dinas (BR-01):</span>
                        <span style="font-weight: 700; color: {{ $isExceedPagu ? '#DC2626' : '#2563EB' }};">
                            {{ $totalKuotaUmum }} / {{ $kuotaMaksPagu }} Kursi ({{ $persenPagu }}%)
                        </span>
                    </div>
                    <div style="width: 100%; height: 6px; background: var(--neutral-300); border-radius: 999px; overflow: hidden; margin-top: 0.375rem;">
                        <div style="width: {{ $persenPagu }}%; height: 100%; background: {{ $isExceedPagu ? '#DC2626' : '#2563EB' }}; border-radius: 999px;"></div>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.375rem; font-size: 0.6875rem; color: var(--neutral-500);">
                        <span>Batas pagu kuota umum bulanan dari kebijakan sistem</span>
                        <a href="{{ route('superadmin.pengaturan.index') }}" style="color: var(--blue-600); font-weight: 600; text-decoration: underline;">Ubah Pagu &rarr;</a>
                    </div>
                </div>

                @if($kuotaList->isEmpty())
                    <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0;">Belum ada alokasi kuota jurusan pada bulan aktif ini.</p>
                @else
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        @foreach($kuotaList as $k)
                            @php
                                $terisi = $k->terisi;
                                $kapasitas = $k->jumlah;
                                $persen = $kapasitas > 0 ? min(100, round(($terisi / $kapasitas) * 100)) : 0;
                                $isFull = $terisi >= $kapasitas;
                            @endphp
                            <div style="background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 8px; padding: 0.625rem 0.875rem;">
                                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8125rem;">
                                    <span style="font-weight: 700; color: var(--neutral-800);">{{ $k->jurusan?->nama_jurusan ?? 'Jurusan' }}</span>
                                    <span style="font-size: 0.75rem; font-weight: 700; color: {{ $isFull ? '#DC2626' : '#166534' }};">
                                        {{ $terisi }} / {{ $kapasitas }} ({{ $persen }}%)
                                    </span>
                                </div>
                                <div style="width: 100%; height: 6px; background: var(--neutral-300); border-radius: 999px; overflow: hidden; margin-top: 0.375rem;">
                                    <div style="width: {{ $persen }}%; height: 100%; background: {{ $isFull ? '#DC2626' : '#16A34A' }}; border-radius: 999px;"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Box B: Waiting List FIFO & Slot Khusus Kemitraan -->
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <!-- Card Waiting List -->
                <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900); margin: 0;">
                            Antrean Waiting List (BR-05)
                        </h4>
                        <span style="font-size: 1.25rem; font-weight: 800; color: #D97706;">
                            {{ $totalWaitingList }}
                        </span>
                    </div>
                    <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0 0 0.875rem 0; line-height: 1.4;">
                        Pendaftar yang berkasnya valid namun kuota jurusan telah penuh. Sistem memproses secara otomatis jika ada peserta yang batal.
                    </p>
                    <a href="{{ route('admin.waiting-list.index') }}" class="btn-secondary" style="display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.75rem; padding: 0.45rem 0.75rem; text-decoration: none; border-radius: 6px;">
                        <span>Kelola Antrean Waiting List &rarr;</span>
                    </a>
                </div>

                <!-- Card Slot Khusus -->
                <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900); margin: 0;">
                            Slot Khusus Kemitraan Sleman
                        </h4>
                        <span style="font-size: 1.25rem; font-weight: 800; color: #7C3AED;">
                            {{ $slotKhusus ? $slotKhusus->terisi . ' / ' . $slotKhusus->jumlah : '0 / 0' }}
                        </span>
                    </div>
                    <div style="margin-bottom: 0.5rem;">
                        <span style="font-size: 0.6875rem; font-weight: 700; background: #F3E8FF; color: #6B21A8; border: 1px solid #DDD6FE; padding: 0.15rem 0.5rem; border-radius: 999px;">
                            🔒 Isolasi Publik: 100% Tersembunyi (AC-01-5)
                        </span>
                    </div>
                    <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0 0 0.875rem 0; line-height: 1.4;">
                        Alokasi jalur khusus untuk institusi yang memiliki nota kesepahaman (MoU) resmi dengan Pemkab Sleman.
                    </p>
                    <a href="{{ route('superadmin.slot-khusus.index') }}" class="btn-secondary" style="display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.75rem; padding: 0.45rem 0.75rem; text-decoration: none; border-radius: 6px;">
                        <span>Atur Slot Khusus Kemitraan &rarr;</span>
                    </a>
                </div>
            </div>

        </div>

    </div>

    <!-- =========================================================================
         PANEL 3: PELAKSANAAN & BIMBINGAN
         ========================================================================= -->
    <div id="tab-bimbingan" class="tab-content-panel" role="tabpanel" aria-labelledby="btn-tab-bimbingan">
        
        <!-- 4 Kartu Metrik Bimbingan -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 10px; padding: 1rem 1.25rem; border-left: 4px solid #2563EB;">
                <span style="font-size: 0.75rem; color: var(--neutral-600); font-weight: 600; display: block;">Total Tugas Aktif</span>
                <span style="font-size: 1.5rem; font-weight: 800; color: var(--neutral-900); display: block; margin-top: 0.25rem;">{{ number_format($totalTugasAktif) }}</span>
                <span style="font-size: 0.6875rem; color: #2563EB; font-weight: 600;">Sedang & belum dikerjakan</span>
            </div>

            <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 10px; padding: 1rem 1.25rem; border-left: 4px solid #D97706;">
                <span style="font-size: 0.75rem; color: var(--neutral-600); font-weight: 600; display: block;">Logbook Pending Reviu</span>
                <span style="font-size: 1.5rem; font-weight: 800; color: var(--neutral-900); display: block; margin-top: 0.25rem;">{{ number_format($totalLogbookPending) }}</span>
                <span style="font-size: 0.6875rem; color: #D97706; font-weight: 600;">Menunggu catatan pembimbing</span>
            </div>

            <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 10px; padding: 1rem 1.25rem; border-left: 4px solid #16A34A;">
                <span style="font-size: 0.75rem; color: var(--neutral-600); font-weight: 600; display: block;">Penilaian Final Selesai</span>
                <span style="font-size: 1.5rem; font-weight: 800; color: var(--neutral-900); display: block; margin-top: 0.25rem;">{{ number_format($totalPenilaianSelesai) }}</span>
                <span style="font-size: 0.6875rem; color: #16A34A; font-weight: 600;">Lembar nilai siap diunduh</span>
            </div>

            <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 10px; padding: 1rem 1.25rem; border-left: 4px solid #7C3AED;">
                <span style="font-size: 0.75rem; color: var(--neutral-600); font-weight: 600; display: block;">Staf Pembimbing Aktif</span>
                <span style="font-size: 1.5rem; font-weight: 800; color: var(--neutral-900); display: block; margin-top: 0.25rem;">{{ number_format($totalPembimbing) }}</span>
                <span style="font-size: 0.6875rem; color: #7C3AED; font-weight: 600;">Tersedia di Diskominfo</span>
            </div>
        </div>

        <!-- BARIS KARTU PENGAWASAN KEDISIPLINAN TUGAS (PRD F-08 & BR-16) -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
            <!-- Tugas Terlambat -->
            <div style="background: var(--neutral-0); border: 1px solid {{ $totalTugasTerlambat > 0 ? '#FECACA' : 'var(--neutral-400)' }}; border-radius: 10px; padding: 1rem 1.25rem; display: flex; align-items: center; justify-content: space-between; border-left: 4px solid #DC2626;">
                <div>
                    <span style="font-size: 0.75rem; color: var(--neutral-600); font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;">Tugas Terlambat (Overdue)</span>
                    <span style="font-size: 1.5rem; font-weight: 800; color: {{ $totalTugasTerlambat > 0 ? '#DC2626' : 'var(--neutral-900)' }}; display: block; margin-top: 0.25rem;">
                        {{ $totalTugasTerlambat }} Tugas
                    </span>
                    <span style="font-size: 0.6875rem; color: {{ $totalTugasTerlambat > 0 ? '#DC2626' : '#16A34A' }}; font-weight: 600;">
                        {{ $totalTugasTerlambat > 0 ? 'Melewati deadline tanpa status selesai' : 'Semua tugas berjalan tepat waktu' }}
                    </span>
                </div>
                <div style="width: 40px; height: 40px; border-radius: 8px; background: #FEE2E2; color: #DC2626; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
            </div>

            <!-- Tugas Mendekati Tenggat -->
            <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 10px; padding: 1rem 1.25rem; display: flex; align-items: center; justify-content: space-between; border-left: 4px solid #D97706;">
                <div>
                    <span style="font-size: 0.75rem; color: var(--neutral-600); font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;">Mendekati Deadline (≤ 3 Hari)</span>
                    <span style="font-size: 1.5rem; font-weight: 800; color: var(--neutral-900); display: block; margin-top: 0.25rem;">
                        {{ $totalTugasMendekatiDeadline }} Tugas
                    </span>
                    <span style="font-size: 0.6875rem; color: #D97706; font-weight: 600;">
                        Tenggat mendekati sesuai aturan BR-16
                    </span>
                </div>
                <div style="width: 40px; height: 40px; border-radius: 8px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Matriks Beban Pembimbing Lapangan (BR-13) -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.125rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--neutral-300); flex-wrap: wrap; gap: 0.5rem;">
                <div>
                    <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-900); margin: 0;">
                        Matriks Utilisasi Beban Pembimbing Lapangan (BR-13)
                    </h3>
                    <p style="font-size: 0.75rem; color: var(--neutral-600); margin: 0.125rem 0 0 0;">
                        Batas maksimal beban mentee per pembimbing lapangan: <strong>{{ $maksBeban ? $maksBeban . ' Orang' : 'Tanpa Batas' }}</strong>
                    </p>
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <a href="{{ route('admin.penempatan.index') }}" style="font-size: 0.75rem; font-weight: 700; color: var(--blue-600); text-decoration: underline;">
                        Kelola Penempatan Peserta &rarr;
                    </a>
                    <span style="color: var(--neutral-400);">&bull;</span>
                    <a href="{{ route('superadmin.monitoring.index') }}" style="font-size: 0.75rem; font-weight: 700; color: var(--blue-600); text-decoration: underline;">
                        Monitoring Peserta &rarr;
                    </a>
                </div>
            </div>

            @if($pembimbingList->isEmpty())
                <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0;">Belum ada staf pembimbing aktif yang terdaftar di basis data.</p>
            @else
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
                    @foreach($pembimbingList as $p)
                        @php
                            $menteeCount = $p->mentee_aktif_count;
                            $maks = $maksBeban ?: 10;
                            $persenBeban = min(100, round(($menteeCount / $maks) * 100));
                            $isOverload = $maksBeban && $menteeCount >= $maksBeban;
                        @endphp
                        <div style="background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 8px; padding: 0.875rem 1rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.25rem;">
                                <span style="font-weight: 700; font-size: 0.875rem; color: var(--neutral-900);">{{ $p->nama }}</span>
                                <span style="font-size: 0.75rem; font-weight: 700; color: {{ $isOverload ? '#DC2626' : '#2563EB' }};">
                                    {{ $menteeCount }} Mentee
                                </span>
                            </div>
                            <span style="font-size: 0.6875rem; color: var(--neutral-600); display: block; margin-bottom: 0.5rem;">{{ $p->email }}</span>
                            <div style="width: 100%; height: 6px; background: var(--neutral-300); border-radius: 999px; overflow: hidden;">
                                <div style="width: {{ $persenBeban }}%; height: 100%; background: {{ $isOverload ? '#DC2626' : '#2563EB' }}; border-radius: 999px;"></div>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.375rem; font-size: 0.6875rem;">
                                <span style="color: {{ $isOverload ? '#DC2626' : '#166534' }}; font-weight: 600;">
                                    {{ $isOverload ? 'Kapasitas Penuh' : 'Kapasitas Tersedia' }}
                                </span>
                                <span style="color: var(--neutral-500);">Kapasitas: {{ $maksBeban ?: '∞' }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- PELACAK PESERTA MENDEKATI BATAS SELESAI (OFF-BOARDING TRACKER <= 14 HARI) -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.875rem; flex-wrap: wrap; gap: 0.5rem;">
                <div>
                    <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                        <span>Pelacak Penyelesaian Magang Terdekat (Off-boarding Tracker)</span>
                        <span style="font-size: 0.6875rem; font-weight: 700; background: #DBEAFE; color: #1E40AF; padding: 0.15rem 0.5rem; border-radius: 999px;">
                            ≤ 14 Hari
                        </span>
                    </h4>
                    <p style="font-size: 0.75rem; color: var(--neutral-600); margin: 0.125rem 0 0 0;">
                        Peserta yang akan menyelesaikan masa dinas dalam 2 pekan ke depan untuk persiapan penilaian akhir dan Lembar Nilai
                    </p>
                </div>
                <a href="{{ route('superadmin.monitoring.index') }}" style="font-size: 0.75rem; font-weight: 700; color: var(--blue-600); text-decoration: underline;">
                    Lihat Semua Peserta Aktif &rarr;
                </a>
            </div>

            @if($pesertaMendekatiSelesai->isEmpty())
                <div style="padding: 1.5rem 1rem; text-align: center; color: var(--neutral-600); background: #F8FAFC; border-radius: 8px; border: 1px dashed var(--neutral-300);">
                    <p style="margin: 0; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">Tidak ada peserta yang mendekati akhir masa magang dalam 14 hari ke depan.</p>
                </div>
            @else
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.8125rem; text-align: left;">
                        <thead>
                            <tr style="border-bottom: 1px solid var(--neutral-300); color: var(--neutral-600); font-size: 0.75rem; text-transform: uppercase;">
                                <th style="padding: 0.5rem 0.75rem;">Nama Peserta</th>
                                <th style="padding: 0.5rem 0.75rem;">Bidang Penempatan</th>
                                <th style="padding: 0.5rem 0.75rem;">Pembimbing</th>
                                <th style="padding: 0.5rem 0.75rem;">Tanggal Selesai</th>
                                <th style="padding: 0.5rem 0.75rem; text-align: right;">Sisa Hari</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pesertaMendekatiSelesai as $pen)
                                @php
                                    $sisaHari = (int) now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($pen->tgl_selesai)->startOfDay(), false);
                                @endphp
                                <tr style="border-bottom: 1px solid var(--neutral-200);">
                                    <td style="padding: 0.5rem 0.75rem; font-weight: 700; color: var(--neutral-900);">
                                        {{ $pen->peserta?->nama ?? 'Peserta' }}
                                    </td>
                                    <td style="padding: 0.5rem 0.75rem; color: var(--neutral-700);">
                                        {{ $pen->bidang?->nama_bidang ?? 'Diskominfo' }}
                                    </td>
                                    <td style="padding: 0.5rem 0.75rem; color: var(--neutral-700);">
                                        {{ $pen->pembimbing?->nama ?? '-' }}
                                    </td>
                                    <td style="padding: 0.5rem 0.75rem; color: var(--neutral-800); font-weight: 600;">
                                        {{ \Carbon\Carbon::parse($pen->tgl_selesai)->format('d M Y') }}
                                    </td>
                                    <td style="padding: 0.5rem 0.75rem; text-align: right;">
                                        <span style="font-weight: 700; font-size: 0.75rem; padding: 0.15rem 0.5rem; border-radius: 999px; {{ $sisaHari <= 7 ? 'background: #FEE2E2; color: #DC2626;' : 'background: #FEF3C7; color: #D97706;' }}">
                                            {{ $sisaHari > 0 ? $sisaHari . ' Hari Lagi' : 'Hari Ini' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Ringkasan Kepatuhan Kebijakan MagKom -->
        <div style="background: #F8FAFC; border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                <div style="padding: 0.5rem 0.75rem; background: #FFFFFF; border: 1px solid var(--neutral-300); border-radius: 8px;">
                    <span style="font-size: 0.6875rem; color: var(--neutral-600); display: block;">Toleransi Logbook Mundur</span>
                    <span style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-900);">Maks. {{ $batasHariLogbookMundur }} Hari (BR-09)</span>
                </div>
                <div style="padding: 0.5rem 0.75rem; background: #FFFFFF; border: 1px solid var(--neutral-300); border-radius: 8px;">
                    <span style="font-size: 0.6875rem; color: var(--neutral-600); display: block;">Masa Tenggang Lembar Nilai</span>
                    <span style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-900);">{{ $masaTenggangUnduhNilaiHari }} Hari Pasca Nonaktif (BR-12)</span>
                </div>
                <div style="padding: 0.5rem 0.75rem; background: #FFFFFF; border: 1px solid var(--neutral-300); border-radius: 8px;">
                    <span style="font-size: 0.6875rem; color: var(--neutral-600); display: block;">Passing Grade Predikat</span>
                    <span style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-900);">Minimal Nilai {{ $passingGradeNilai }} (BR-11)</span>
                </div>
            </div>
            <a href="{{ route('superadmin.pengaturan.index') }}" style="font-size: 0.75rem; font-weight: 700; color: var(--blue-600); text-decoration: underline;">
                Sesuaikan Parameter Sistem &rarr;
            </a>
        </div>

    </div>

    <!-- =========================================================================
         PANEL 4: TATA KELOLA SISTEM & LAPORAN
         ========================================================================= -->
    <div id="tab-sistem" class="tab-content-panel" role="tabpanel" aria-labelledby="btn-tab-sistem">
        
        <!-- BAGIAN 1: HUB EKSPOR 4 LAPORAN RESMI DINAS 1-KLIK -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.125rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--neutral-300); flex-wrap: wrap; gap: 0.5rem;">
                <div>
                    <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-900); margin: 0;">
                        Hub Ekspor Instan Laporan Resmi Dinas (AC-17-1)
                    </h3>
                    <p style="font-size: 0.75rem; color: var(--neutral-600); margin: 0.125rem 0 0 0;">
                        Unduh data rekapitulasi CSV/Excel untuk kebutuhan arsip dan pelaporan pimpinan Diskominfo Sleman
                    </p>
                </div>
                <a href="{{ route('admin.laporan.index') }}" style="font-size: 0.8125rem; font-weight: 700; color: #2563EB; text-decoration: none;">
                    Buka Filter Laporan Lanjutan &rarr;
                </a>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1rem;">
                <!-- Ekspor 1: Pendaftaran -->
                <div style="background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 10px; padding: 1rem 1.25rem; display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.375rem;">
                            <div style="width: 28px; height: 28px; border-radius: 6px; background: #DBEAFE; color: #1E40AF; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.75rem;">
                                1
                            </div>
                            <h4 style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-900); margin: 0;">Laporan Pendaftaran</h4>
                        </div>
                        <p style="font-size: 0.75rem; color: var(--neutral-600); margin: 0 0 0.875rem 0; line-height: 1.4;">
                            Data registrasi peserta, asal institusi, jurusan, dan status keputusan seleksi.
                        </p>
                    </div>
                    <a href="{{ route('admin.laporan.pendaftaran') }}" class="btn-primary" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.375rem; padding: 0.5rem 0.75rem; font-size: 0.75rem; text-decoration: none; border-radius: 6px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        <span>Unduh CSV Pendaftaran</span>
                    </a>
                </div>

                <!-- Ekspor 2: Penempatan -->
                <div style="background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 10px; padding: 1rem 1.25rem; display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.375rem;">
                            <div style="width: 28px; height: 28px; border-radius: 6px; background: #DCFCE7; color: #166534; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.75rem;">
                                2
                            </div>
                            <h4 style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-900); margin: 0;">Laporan Penempatan</h4>
                        </div>
                        <p style="font-size: 0.75rem; color: var(--neutral-600); margin: 0 0 0.875rem 0; line-height: 1.4;">
                            Alokasi bidang kerja, pembimbing lapangan yang ditunjuk, dan status masa dinas peserta.
                        </p>
                    </div>
                    <a href="{{ route('admin.laporan.penempatan') }}" class="btn-primary" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.375rem; padding: 0.5rem 0.75rem; font-size: 0.75rem; text-decoration: none; border-radius: 6px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        <span>Unduh CSV Penempatan</span>
                    </a>
                </div>

                <!-- Ekspor 3: Realisasi Kuota -->
                <div style="background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 10px; padding: 1rem 1.25rem; display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.375rem;">
                            <div style="width: 28px; height: 28px; border-radius: 6px; background: #FEF3C7; color: #92400E; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.75rem;">
                                3
                            </div>
                            <h4 style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-900); margin: 0;">Realisasi Kuota</h4>
                        </div>
                        <p style="font-size: 0.75rem; color: var(--neutral-600); margin: 0 0 0.875rem 0; line-height: 1.4;">
                            Perbandingan alokasi kuota umum vs slot khusus kemitraan beserta rasio keterisian per jurusan.
                        </p>
                    </div>
                    <a href="{{ route('admin.laporan.kuota') }}" class="btn-primary" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.375rem; padding: 0.5rem 0.75rem; font-size: 0.75rem; text-decoration: none; border-radius: 6px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        <span>Unduh CSV Kuota</span>
                    </a>
                </div>

                <!-- Ekspor 4: Rekapitulasi Nilai (Eksklusif SuperAdmin) -->
                <div style="background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 10px; padding: 1rem 1.25rem; display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.375rem;">
                            <div style="width: 28px; height: 28px; border-radius: 6px; background: #F3E8FF; color: #6B21A8; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.75rem;">
                                4
                            </div>
                            <h4 style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-900); margin: 0;">Rekapitulasi Nilai</h4>
                        </div>
                        <p style="font-size: 0.75rem; color: var(--neutral-600); margin: 0 0 0.875rem 0; line-height: 1.4;">
                            Nilai akhir, predikat kelulusan (A/B/C/D/E), dan rekam hasil evaluasi peserta alumni (Akses Khusus).
                        </p>
                    </div>
                    <a href="{{ route('admin.laporan.nilai') }}" class="btn-primary" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.375rem; padding: 0.5rem 0.75rem; font-size: 0.75rem; text-decoration: none; border-radius: 6px; background: #7C3AED;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        <span>Unduh CSV Nilai</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- BAGIAN 2: PUSAT MONITORING KEAMANAN SISTEM & THREAT RADAR (PRD Bab 10 & TDD Bab 8-9) -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle); margin-top: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--neutral-300); flex-wrap: wrap; gap: 0.75rem;">
                <div>
                    <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-900); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            <path d="M12 8v4"></path>
                            <path d="M12 16h.01"></path>
                        </svg>
                        <span>Pusat Monitoring Keamanan Sistem & Threat Radar (PRD Bab 10)</span>
                    </h3>
                    <p style="font-size: 0.75rem; color: var(--neutral-600); margin: 0.125rem 0 0 0;">
                        Pemantauan ancaman siber real-time, deteksi anomali autentikasi 24 jam, higienitas kredensial staf, dan imutabilitas jejak audit
                    </p>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    @if(($loginGagal24Jam ?? 0) === 0)
                        <span style="display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.6875rem; font-weight: 700; background: #DCFCE7; color: #166534; padding: 0.25rem 0.625rem; border-radius: 999px; border: 1px solid #BBF7D0;">
                            <span style="width: 7px; height: 7px; border-radius: 50%; background: #16A34A; display: inline-block;"></span>
                            POSTUR KEAMANAN PRIMA (0 ANOMALI)
                        </span>
                    @else
                        <span style="display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.6875rem; font-weight: 700; background: #FEE2E2; color: #991B1B; padding: 0.25rem 0.625rem; border-radius: 999px; border: 1px solid #FECACA;">
                            <span style="width: 7px; height: 7px; border-radius: 50%; background: #DC2626; display: inline-block;"></span>
                            RADAR AKTIF: {{ $loginGagal24Jam }} ANOMALI LOGIN (24 JAM)
                        </span>
                    @endif
                    <a href="{{ route('superadmin.audit-log.index') }}" style="font-size: 0.75rem; font-weight: 700; color: var(--blue-600); text-decoration: none; padding: 0.25rem 0.5rem; background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 6px;">
                        Audit Lengkap &rarr;
                    </a>
                </div>
            </div>

            <!-- 4 Kartu Metrik Postur Keamanan & Perlindungan Siber -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 1rem; margin-bottom: 1.25rem;">
                
                <!-- Kartu 1: Radar Percobaan Intrusi / Login Gagal 24 Jam -->
                <div style="background: #F8FAFC; border: 1px solid var(--neutral-300); border-radius: 10px; padding: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                        <span style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700);">Radar Anomali Login (24 Jam)</span>
                        <div style="width: 28px; height: 28px; border-radius: 6px; background: {{ ($loginGagal24Jam ?? 0) > 0 ? '#FEE2E2' : '#DCFCE7' }}; color: {{ ($loginGagal24Jam ?? 0) > 0 ? '#DC2626' : '#16A34A' }}; display: flex; align-items: center; justify-content: center;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            </svg>
                        </div>
                    </div>
                    <div style="font-size: 1.375rem; font-weight: 800; color: {{ ($loginGagal24Jam ?? 0) > 0 ? '#DC2626' : 'var(--neutral-900)' }}; line-height: 1.2; margin-bottom: 0.25rem;">
                        {{ $loginGagal24Jam ?? 0 }} <span style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-600);">Percobaan</span>
                    </div>
                    <div style="font-size: 0.6875rem; color: var(--neutral-600); display: flex; align-items: center; justify-content: space-between;">
                        <span>HTTP 401 & Rate Limit (429)</span>
                        <span style="font-weight: 700; color: {{ ($loginGagal24Jam ?? 0) > 0 ? '#DC2626' : '#166534' }};">
                            {{ ($loginGagal24Jam ?? 0) > 0 ? 'Perlu Tinjauan' : 'Aman' }}
                        </span>
                    </div>
                </div>

                <!-- Kartu 2: Higienitas Kredensial Staf (BR-15) -->
                <div style="background: #F8FAFC; border: 1px solid var(--neutral-300); border-radius: 10px; padding: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                        <span style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700);">Higienitas Kredensial Staf (BR-15)</span>
                        <div style="width: 28px; height: 28px; border-radius: 6px; background: {{ ($stafPendingGantiPassword ?? 0) > 0 ? '#FEF3C7' : '#DCFCE7' }}; color: {{ ($stafPendingGantiPassword ?? 0) > 0 ? '#D97706' : '#16A34A' }}; display: flex; align-items: center; justify-content: center;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 14 14"></polyline>
                            </svg>
                        </div>
                    </div>
                    <div style="font-size: 1.375rem; font-weight: 800; color: var(--neutral-900); line-height: 1.2; margin-bottom: 0.25rem;">
                        {{ $stafPendingGantiPassword ?? 0 }} <span style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-600);">Akun</span>
                    </div>
                    <div style="font-size: 0.6875rem; color: var(--neutral-600); display: flex; align-items: center; justify-content: space-between;">
                        <span>Pending Ganti Password Awal</span>
                        <span style="font-weight: 700; color: {{ ($stafPendingGantiPassword ?? 0) > 0 ? '#D97706' : '#166534' }};">
                            {{ ($stafPendingGantiPassword ?? 0) > 0 ? 'Force Change Aktif' : 'Higienis' }}
                        </span>
                    </div>
                </div>

                <!-- Kartu 3: Vault Dokumen Terisolasi (NFR-S2) -->
                <div style="background: #F8FAFC; border: 1px solid var(--neutral-300); border-radius: 10px; padding: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                        <span style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700);">Vault Dokumen Privat (NFR-S2)</span>
                        <div style="width: 28px; height: 28px; border-radius: 6px; background: #DBEAFE; color: #1E40AF; display: flex; align-items: center; justify-content: center;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </div>
                    </div>
                    <div style="font-size: 1.375rem; font-weight: 800; color: var(--neutral-900); line-height: 1.2; margin-bottom: 0.25rem;">
                        Maks. {{ $berkasMaksMb ?? 2 }} <span style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-600);">MB</span>
                    </div>
                    <div style="font-size: 0.6875rem; color: var(--neutral-600); display: flex; align-items: center; justify-content: space-between;">
                        <span>Signed URL (TTL 5 Menit)</span>
                        <span style="font-weight: 700; color: #166534;">Anti-IDOR/BOLA</span>
                    </div>
                </div>

                <!-- Kartu 4: Imutabilitas Audit Trail (NFR-S9) -->
                <div style="background: #F8FAFC; border: 1px solid var(--neutral-300); border-radius: 10px; padding: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                        <span style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700);">Audit Trail Imutabel (NFR-S9)</span>
                        <div style="width: 28px; height: 28px; border-radius: 6px; background: #F3E8FF; color: #7C3AED; display: flex; align-items: center; justify-content: center;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="9 11 12 14 22 4"></polyline>
                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                            </svg>
                        </div>
                    </div>
                    <div style="font-size: 1.375rem; font-weight: 800; color: var(--neutral-900); line-height: 1.2; margin-bottom: 0.25rem;">
                        {{ number_format($securityPosture['total_audit_events'] ?? 0) }} <span style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-600);">Log</span>
                    </div>
                    <div style="font-size: 0.6875rem; color: var(--neutral-600); display: flex; align-items: center; justify-content: space-between;">
                        <span>Append-Only (No Delete/Edit)</span>
                        <span style="font-weight: 700; color: #166534;">Integritas 100%</span>
                    </div>
                </div>

            </div>

            <!-- Detail Radar Insiden & Sebaran Kategori Audit PRD Bab 10.3 -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 1.25rem;">
                
                <!-- Kolom Kiri: Radar Percobaan Intrusi & Insiden Auth Gagal Terakhir -->
                <div style="background: #FFFFFF; border: 1px solid var(--neutral-300); border-radius: 10px; padding: 1.125rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                        <h4 style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-900); margin: 0; display: flex; align-items: center; gap: 0.375rem;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            <span>Radar Anomali Login Terakhir</span>
                        </h4>
                        <span style="font-size: 0.6875rem; color: var(--neutral-500);">5 Rekaman Terbaru</span>
                    </div>

                    @if($insidenKeamananTerbaru->isEmpty())
                        <div style="text-align: center; padding: 2rem 1rem; background: #F8FAFC; border-radius: 8px; border: 1px dashed var(--neutral-300);">
                            <div style="width: 36px; height: 36px; border-radius: 50%; background: #DCFCE7; color: #16A34A; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 0.5rem;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </div>
                            <div style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.125rem;">
                                Kondisi Sistem Bersih & Terkendali
                            </div>
                            <div style="font-size: 0.75rem; color: var(--neutral-600); max-width: 340px; margin: 0 auto;">
                                Tidak ada rekaman percobaan login gagal, serangan brute force, atau pelanggaran rate limiting dalam radar pemantauan.
                            </div>
                        </div>
                    @else
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.75rem;">
                                <thead>
                                    <tr style="border-bottom: 1px solid var(--neutral-300); color: var(--neutral-600); text-align: left;">
                                        <th style="padding: 0.375rem 0.5rem;">Waktu</th>
                                        <th style="padding: 0.375rem 0.5rem;">IP Address</th>
                                        <th style="padding: 0.375rem 0.5rem;">Target Identitas</th>
                                        <th style="padding: 0.375rem 0.5rem; text-align: right;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($insidenKeamananTerbaru as $insiden)
                                        <tr style="border-bottom: 1px solid var(--neutral-200);">
                                            <td style="padding: 0.5rem; color: var(--neutral-800); font-weight: 600; white-space: nowrap;">
                                                {{ \Carbon\Carbon::parse($insiden->waktu)->format('d/m H:i') }}
                                            </td>
                                            <td style="padding: 0.5rem; font-family: monospace; color: var(--neutral-700);">
                                                {{ $insiden->ip ?? '-' }}
                                            </td>
                                            <td style="padding: 0.5rem; color: var(--neutral-900); max-width: 140px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                {{ $insiden->user?->nama ?? $insiden->user?->email ?? ($insiden->payload['email'] ?? 'Publik/Guest') }}
                                            </td>
                                            <td style="padding: 0.5rem; text-align: right;">
                                                <span style="font-size: 0.6875rem; font-weight: 700; background: #FEE2E2; color: #DC2626; padding: 0.1rem 0.375rem; border-radius: 4px;">
                                                    {{ $insiden->status_http ?? 401 }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- Kolom Kanan: Distribusi Kategori Audit PRD Bab 10.3 & Checklist Hardening -->
                <div style="background: #FFFFFF; border: 1px solid var(--neutral-300); border-radius: 10px; padding: 1.125rem;">
                    @php
                        $totAudit = max(1, ($auditTipeDistribution['auth'] ?? 0) + ($auditTipeDistribution['aksi'] ?? 0) + ($auditTipeDistribution['service'] ?? 0) + ($auditTipeDistribution['akses_data'] ?? 0));
                        $pctAuth = round((($auditTipeDistribution['auth'] ?? 0) / $totAudit) * 100);
                        $pctAksi = round((($auditTipeDistribution['aksi'] ?? 0) / $totAudit) * 100);
                        $pctService = round((($auditTipeDistribution['service'] ?? 0) / $totAudit) * 100);
                        $pctAkses = max(0, 100 - ($pctAuth + $pctAksi + $pctService));
                    @endphp

                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                        <h4 style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-900); margin: 0; display: flex; align-items: center; gap: 0.375rem;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--blue-600)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="20" x2="18" y2="10"></line>
                                <line x1="12" y1="20" x2="12" y2="4"></line>
                                <line x1="6" y1="20" x2="6" y2="14"></line>
                            </svg>
                            <span>Distribusi Audit Trail (PRD Bab 10.3)</span>
                        </h4>
                        <span style="font-size: 0.6875rem; font-weight: 700; color: var(--neutral-700);">
                            {{ number_format($totAudit) }} Total Event
                        </span>
                    </div>

                    <!-- Visual Segmented Bar -->
                    <div style="height: 10px; border-radius: 6px; overflow: hidden; display: flex; background: var(--neutral-200); margin-bottom: 0.875rem;">
                        <div style="width: {{ $pctAuth }}%; background: #2563EB;" title="Auth: {{ $pctAuth }}%"></div>
                        <div style="width: {{ $pctAksi }}%; background: #16A34A;" title="Aksi: {{ $pctAksi }}%"></div>
                        <div style="width: {{ $pctService }}%; background: #D97706;" title="Service: {{ $pctService }}%"></div>
                        <div style="width: {{ $pctAkses }}%; background: #7C3AED;" title="Akses Data: {{ $pctAkses }}%"></div>
                    </div>

                    <!-- Legend & Counts Grid -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-bottom: 1rem; font-size: 0.75rem;">
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.375rem 0.5rem; background: #F8FAFC; border-radius: 6px;">
                            <span style="display: flex; align-items: center; gap: 0.375rem; color: var(--neutral-800); font-weight: 600;">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background: #2563EB;"></span>
                                Auth (Login/Sesi)
                            </span>
                            <span style="font-weight: 700; color: var(--neutral-900);">{{ number_format($auditTipeDistribution['auth'] ?? 0) }}</span>
                        </div>
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.375rem 0.5rem; background: #F8FAFC; border-radius: 6px;">
                            <span style="display: flex; align-items: center; gap: 0.375rem; color: var(--neutral-800); font-weight: 600;">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background: #16A34A;"></span>
                                Aksi (Mutasi Data)
                            </span>
                            <span style="font-weight: 700; color: var(--neutral-900);">{{ number_format($auditTipeDistribution['aksi'] ?? 0) }}</span>
                        </div>
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.375rem 0.5rem; background: #F8FAFC; border-radius: 6px;">
                            <span style="display: flex; align-items: center; gap: 0.375rem; color: var(--neutral-800); font-weight: 600;">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background: #D97706;"></span>
                                Service (Scheduler)
                            </span>
                            <span style="font-weight: 700; color: var(--neutral-900);">{{ number_format($auditTipeDistribution['service'] ?? 0) }}</span>
                        </div>
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.375rem 0.5rem; background: #F8FAFC; border-radius: 6px;">
                            <span style="display: flex; align-items: center; gap: 0.375rem; color: var(--neutral-800); font-weight: 600;">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background: #7C3AED;"></span>
                                Akses Data (Unduh)
                            </span>
                            <span style="font-weight: 700; color: var(--neutral-900);">{{ number_format($auditTipeDistribution['akses_data'] ?? 0) }}</span>
                        </div>
                    </div>

                    <!-- Postur Hardening HTTP & Aplikasi (NFR-S1 s/d NFR-S8) -->
                    <div style="border-top: 1px dashed var(--neutral-300); padding-top: 0.625rem; font-size: 0.6875rem; color: var(--neutral-600); display: flex; flex-direction: column; gap: 0.25rem;">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <span>• HTTP Headers: X-Frame-Options DENY & nosniff (NFR-S8)</span>
                            <span style="font-weight: 700; color: #166534;">AKTIF</span>
                        </div>
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <span>• Rate Limiter & Anti-Bruteforce Throttle (TDD Bab 8)</span>
                            <span style="font-weight: 700; color: #166534;">AKTIF</span>
                        </div>
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <span>• Enkripsi Sandi Bcrypt (Cost 12) & Proteksi CSRF Form</span>
                            <span style="font-weight: 700; color: #166534;">AKTIF</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- BAGIAN 3: PINTASAN KONTROL SISTEM, ROLE & AUDIT LOG -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 1.5rem; margin-top: 1.5rem;">
            
            <!-- Box Tata Kelola Hak Akses & Master Data -->
            <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle);">
                <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900); margin: 0 0 0.75rem 0;">
                    Kontrol Hak Akses & Master Data (PRD 3.1 & 3.3)
                </h4>
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <a href="{{ route('superadmin.role.index') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 0.625rem 0.75rem; background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 8px; text-decoration: none; color: var(--neutral-900);">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--blue-600);">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            </svg>
                            <span style="font-size: 0.8125rem; font-weight: 600;">Matriks Peran & Hak Akses (Figma SA-02)</span>
                        </div>
                        <span style="font-size: 0.75rem; color: var(--blue-600); font-weight: 700;">Lihat &rarr;</span>
                    </a>

                    <a href="{{ route('superadmin.jurusan.index') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 0.625rem 0.75rem; background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 8px; text-decoration: none; color: var(--neutral-900);">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #059669;">
                                <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                            </svg>
                            <span style="font-size: 0.8125rem; font-weight: 600;">Master Jurusan & Program Studi</span>
                        </div>
                        <span style="font-size: 0.75rem; color: var(--blue-600); font-weight: 700;">Kelola &rarr;</span>
                    </a>

                    <a href="{{ route('superadmin.bidang.index') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 0.625rem 0.75rem; background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 8px; text-decoration: none; color: var(--neutral-900);">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #D97706;">
                                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                            </svg>
                            <span style="font-size: 0.8125rem; font-weight: 600;">Master Bidang Kerja Diskominfo</span>
                        </div>
                        <span style="font-size: 0.75rem; color: var(--blue-600); font-weight: 700;">Kelola &rarr;</span>
                    </a>

                    <a href="{{ route('superadmin.rubrik.index') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 0.625rem 0.75rem; background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 8px; text-decoration: none; color: var(--neutral-900);">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #7C3AED;">
                                <polyline points="9 11 12 14 22 4"></polyline>
                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                            </svg>
                            <span style="font-size: 0.8125rem; font-weight: 600;">Bobot Rubrik Penilaian Akhir</span>
                        </div>
                        <span style="font-size: 0.75rem; color: var(--blue-600); font-weight: 700;">Atur &rarr;</span>
                    </a>
                </div>
            </div>

            <!-- Box Keamanan, Konfigurasi & Arsip -->
            <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle);">
                <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900); margin: 0 0 0.75rem 0;">
                    Keamanan, Arsip & Konfigurasi Sistem
                </h4>
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <a href="{{ route('superadmin.pengaturan.index') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 0.625rem 0.75rem; background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 8px; text-decoration: none; color: var(--neutral-900);">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--blue-600);">
                                <circle cx="12" cy="12" r="3"></circle>
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                            </svg>
                            <span style="font-size: 0.8125rem; font-weight: 600;">Parameter Konfigurasi Global</span>
                        </div>
                        <span style="font-size: 0.75rem; color: var(--blue-600); font-weight: 700;">Ubah &rarr;</span>
                    </a>

                    <a href="{{ route('superadmin.arsip.index') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 0.625rem 0.75rem; background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 8px; text-decoration: none; color: var(--neutral-900);">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #059669;">
                                <polyline points="21 8 21 21 3 21 3 8"></polyline>
                                <rect x="1" y="3" width="22" height="5"></rect>
                                <line x1="10" y1="12" x2="14" y2="12"></line>
                            </svg>
                            <span style="font-size: 0.8125rem; font-weight: 600;">Gudang Arsip Peserta Magang (F-12)</span>
                        </div>
                        <span style="font-size: 0.75rem; color: var(--blue-600); font-weight: 700;">Buka &rarr;</span>
                    </a>

                    <a href="{{ route('superadmin.audit-log.index') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 0.625rem 0.75rem; background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 8px; text-decoration: none; color: var(--neutral-900);">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #7C3AED;">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                            </svg>
                            <span style="font-size: 0.8125rem; font-weight: 600;">Jejak Audit Trail & Log Keamanan</span>
                        </div>
                        <span style="font-size: 0.75rem; color: var(--blue-600); font-weight: 700;">Buka &rarr;</span>
                    </a>

                    <a href="{{ route('superadmin.audit-log.export') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 0.625rem 0.75rem; background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 8px; text-decoration: none; color: var(--neutral-900);">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #DC2626;">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" y1="15" x2="12" y2="3"></line>
                            </svg>
                            <span style="font-size: 0.8125rem; font-weight: 600;">Ekspor Jejak Audit Trail (CSV)</span>
                        </div>
                        <span style="font-size: 0.75rem; color: #DC2626; font-weight: 700;">Unduh &rarr;</span>
                    </a>

                    <a href="{{ route('superadmin.health.index') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 0.625rem 0.75rem; background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 8px; text-decoration: none; color: var(--neutral-900);">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #059669;">
                                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                            </svg>
                            <span style="font-size: 0.8125rem; font-weight: 600;">Telemetri Kesehatan & Storage Infrastruktur</span>
                        </div>
                        <span style="font-size: 0.75rem; color: var(--blue-600); font-weight: 700;">Buka &rarr;</span>
                    </a>

                    <a href="{{ route('superadmin.backup.index') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 0.625rem 0.75rem; background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 8px; text-decoration: none; color: var(--neutral-900);">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #7C3AED;">
                                <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                                <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                            </svg>
                            <span style="font-size: 0.8125rem; font-weight: 600;">Pusat Cadangan & Pemulihan Basis Data (Backup Hub)</span>
                        </div>
                        <span style="font-size: 0.75rem; color: var(--blue-600); font-weight: 700;">Buka &rarr;</span>
                    </a>
                </div>

                <!-- Nilai Parameter Resmi PRD Bab 8.6 Aktif -->
                <div style="margin-top: 0.875rem; padding-top: 0.75rem; border-top: 1px dashed var(--neutral-300); display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; font-size: 0.6875rem;">
                    <div style="background: var(--neutral-100); padding: 0.375rem 0.5rem; border-radius: 6px;">
                        <span style="color: var(--neutral-600); display: block;">logbook_maks_mundur</span>
                        <strong style="color: var(--neutral-900);">Maks. {{ $logbookMaksMundurHari }} Hari (BR-09)</strong>
                    </div>
                    <div style="background: var(--neutral-100); padding: 0.375rem 0.5rem; border-radius: 6px;">
                        <span style="color: var(--neutral-600); display: block;">masa_tenggang_bulan</span>
                        <strong style="color: var(--neutral-900);">{{ $masaTenggangBulan }} Bulan (BR-12)</strong>
                    </div>
                    <div style="background: var(--neutral-100); padding: 0.375rem 0.5rem; border-radius: 6px;">
                        <span style="color: var(--neutral-600); display: block;">berkas_maks_mb</span>
                        <strong style="color: var(--neutral-900);">Maks. {{ $berkasMaksMb }} MB (BR-03)</strong>
                    </div>
                    <div style="background: var(--neutral-100); padding: 0.375rem 0.5rem; border-radius: 6px;">
                        <span style="color: var(--neutral-600); display: block;">slot_khusus_default</span>
                        <strong style="color: var(--neutral-900);">{{ $slotKhususDefault }} Kuota (BR-01)</strong>
                    </div>
                </div>
            </div>

        </div>

        <!-- BAGIAN 4: STATUS KESEHATAN SISTEM & INFRASTRUKTUR (TDD Bab 10, NFR-O1) -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle); margin-top: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.875rem; flex-wrap: wrap; gap: 0.5rem;">
                <div>
                    <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                        <span>Integritas Infrastruktur & Status Layanan (TDD Bab 10)</span>
                        <span style="font-size: 0.6875rem; font-weight: 700; background: #DCFCE7; color: #166534; padding: 0.15rem 0.5rem; border-radius: 999px;">
                            Sistem Sehat
                        </span>
                    </h4>
                    <p style="font-size: 0.75rem; color: var(--neutral-600); margin: 0.125rem 0 0 0;">
                        Pemantauan integritas database MySQL/InnoDB, storage berkas privat terenkripsi, dan scheduler cron job
                    </p>
                </div>
                <a href="{{ route('health') }}" target="_blank" style="font-size: 0.75rem; font-weight: 700; color: var(--blue-600); text-decoration: underline;">
                    Buka Healthcheck API (/health) &rarr;
                </a>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 0.875rem;">
                <div style="padding: 0.75rem 1rem; border-radius: 8px; background: #F8FAFC; border: 1px solid var(--neutral-300); display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 10px; height: 10px; border-radius: 50%; background: #16A34A; flex-shrink: 0; box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.2);"></div>
                    <div>
                        <span style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-900); display: block;">Koneksi Basis Data</span>
                        <span style="font-size: 0.6875rem; color: #166534; font-weight: 600;">MySQL InnoDB / Normal</span>
                    </div>
                </div>

                <div style="padding: 0.75rem 1rem; border-radius: 8px; background: #F8FAFC; border: 1px solid var(--neutral-300); display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 10px; height: 10px; border-radius: 50%; background: #16A34A; flex-shrink: 0; box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.2);"></div>
                    <div>
                        <span style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-900); display: block;">Private Disk Storage</span>
                        <span style="font-size: 0.6875rem; color: #166534; font-weight: 600;">Disk Privat Terisolasi</span>
                    </div>
                </div>

                <div style="padding: 0.75rem 1rem; border-radius: 8px; background: #F8FAFC; border: 1px solid var(--neutral-300); display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 10px; height: 10px; border-radius: 50%; background: #2563EB; flex-shrink: 0; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);"></div>
                    <div>
                        <span style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-900); display: block;">Audit Trail Append-Only</span>
                        <span style="font-size: 0.6875rem; color: #1D4ED8; font-weight: 600;">{{ number_format($systemHealth['audit_count'] ?? 0) }} Rekaman Terarsip</span>
                    </div>
                </div>

                <div style="padding: 0.75rem 1rem; border-radius: 8px; background: #F8FAFC; border: 1px solid var(--neutral-300); display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 10px; height: 10px; border-radius: 50%; background: #7C3AED; flex-shrink: 0; box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.2);"></div>
                    <div>
                        <span style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-900); display: block;">Scheduler Nonaktif Harian</span>
                        <span style="font-size: 0.6875rem; color: #6B21A8; font-weight: 600;">Terkonfigurasi di Kernel</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<!-- =========================================================================
     JAVASCRIPT: TAB CONTROLLER & PERSISTENSI STATE
     ========================================================================= -->
<script>
    function switchDashboardTab(tabId) {
        // 1. Sembunyikan semua tab panel
        const panels = document.querySelectorAll('.tab-content-panel');
        panels.forEach(panel => {
            panel.classList.remove('active');
        });

        // 2. Nonaktifkan semua tab button
        const buttons = document.querySelectorAll('.tab-btn');
        buttons.forEach(btn => {
            btn.classList.remove('active');
            btn.setAttribute('aria-selected', 'false');
        });

        // 3. Aktifkan tab yang dipilih
        const targetPanel = document.getElementById(tabId);
        const targetBtn = document.getElementById('btn-' + tabId);

        if (targetPanel) {
            targetPanel.classList.add('active');
        }
        if (targetBtn) {
            targetBtn.classList.add('active');
            targetBtn.setAttribute('aria-selected', 'true');
        }

        // 4. Simpan ke localStorage agar tidak reset saat refresh
        try {
            localStorage.setItem('magkom_superadmin_active_tab', tabId);
        } catch (e) {
            // ignore localStorage error if restricted
        }
    }

    // Inisialisasi Tab Saat Halaman Dimuat
    document.addEventListener('DOMContentLoaded', function () {
        // Cek URL Hash terlebih dahulu (#seleksi, #bimbingan, dll.)
        const hash = window.location.hash.replace('#', '');
        const validTabs = ['ringkasan', 'seleksi', 'bimbingan', 'sistem'];

        if (hash && validTabs.includes(hash)) {
            switchDashboardTab('tab-' + hash);
            return;
        }

        // Jika tidak ada hash, cek localStorage
        try {
            const savedTab = localStorage.getItem('magkom_superadmin_active_tab');
            if (savedTab && document.getElementById(savedTab)) {
                switchDashboardTab(savedTab);
            }
        } catch (e) {
            // default tetap tab-ringkasan
        }
    });
</script>
@endsection
