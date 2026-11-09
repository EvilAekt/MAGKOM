@extends('layouts.dashboard')

@section('title', 'Konfigurasi Sistem - SuperAdmin MagKom')
@section('topbar_title', 'Parameter Konfigurasi Sistem')

@section('breadcrumbs')
    <a href="{{ route('superadmin.dashboard') }}">SuperAdmin</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Pengaturan Sistem</span>
@endsection

@push('styles')
    @vite(['resources/css/pages/superadmin/superadmin-common.css'])
@endpush

@section('content')
<div class="sa-fluid-container" style="display: flex; flex-direction: column; gap: 1.5rem; width: 100%;">

    {{-- 1. Hero Banner Khas Sleman MagKom --}}
    <div class="sa-hero-banner">
        <div class="banner-top-row">
            <div>
                <h1 class="banner-title">Parameter Konfigurasi Sistem</h1>
                <div class="banner-subtitle">
                    <span>Tata Kelola: <strong>Aturan Bisnis &amp; Ambang Batas</strong></span>
                    <span class="bullet-separator">&bull;</span>
                    <span>Status: <strong>Tersimpan di Database</strong></span>
                </div>
            </div>
            <div class="banner-actions-group">
                <a href="{{ route('superadmin.pengaturan.export-json') }}" class="btn-banner-action--secondary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    <span>Ekspor Snapshot JSON</span>
                </a>
                <button type="submit" form="formPengaturanSistem" class="btn-banner-action">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </div>
    </div>

    {{-- 2. Grid 4 Kartu Statistik Mengadopsi Gentelella Tile-Stat --}}
    <div class="tiles-grid">
        <!-- 1. Total Parameter -->
        <x-gentelella.tile-stat
            title="TOTAL PARAMETER"
            value="{{ $totalParameter }}"
            color="blue"
            trendType="info"
            trendDirection="up"
            trendText="Sistem"
            subText="Parameter Operasional Aktif"
            :sparkBars="[30, 45, 60, 75, 85, 95]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="3"></circle>
                    <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"></path>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <!-- 2. Kuota Umum Bulanan -->
        <x-gentelella.tile-stat
            title="KUOTA UMUM BULANAN"
            value="{{ $kuotaBulanan }} Kuota"
            color="teal"
            trendType="success"
            trendDirection="up"
            trendText="Default"
            subText="Batas Maksimal Kuota Umum / Bulan"
            :sparkBars="[25, 40, 55, 70, 80, 90]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <!-- 3. Rasio Mentee per Pembimbing -->
        <x-gentelella.tile-stat
            title="RASIO MENTEE / MENTOR"
            value="{{ $rasioMentee }} : 1"
            color="amber"
            trendType="warning"
            trendDirection="up"
            trendText="Kapasitas"
            subText="Maks Mentee per Pembimbing"
            :sparkBars="[20, 35, 50, 65, 80, 85]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 00-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 010 7.75"></path>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <!-- 4. Masa Tenggang Daftar Ulang -->
        <x-gentelella.tile-stat
            title="MASA TENGGANG"
            value="{{ $masaTenggang }} Bulan"
            color="purple"
            trendType="info"
            trendDirection="up"
            trendText="Cooldown"
            subText="Durasi Akun Pasca Magang"
            :sparkBars="[15, 25, 40, 55, 70, 85]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>
    </div>

    <form id="formPengaturanSistem" action="{{ route('superadmin.pengaturan.update') }}" method="POST" style="display: flex; flex-direction: column; gap: 1.5rem;">
        @csrf
        @method('PUT')

        <!-- KARTU 1: KEBIJAKAN KUOTA, PENDAFTARAN & KAPASITAS MAGANG -->
        <x-gentelella.card :hoverable="false" style="padding: 1.75rem 2rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 1rem;">
                <div style="width: 38px; height: 38px; border-radius: 8px; background: var(--blue-tint); color: var(--blue-700); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </div>
                <div>
                    <h2 style="font-size: 1.0625rem; font-weight: 800; color: var(--neutral-900); margin: 0;">
                        Kebijakan Kuota, Pendaftaran & Kapasitas Magang
                    </h2>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                <div>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        Batas Kuota Umum Publik Per Bulan (Orang) <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="number" name="kuota_umum_maks_per_bulan" min="1" max="100" required value="{{ old('kuota_umum_maks_per_bulan', $pengaturan['kuota_umum_maks_per_bulan'] ?? 12) }}" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                </div>

                <div>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        Default Alokasi Slot Khusus (Orang) <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="number" name="slot_khusus_default" min="0" max="50" required value="{{ old('slot_khusus_default', $pengaturan['slot_khusus_default'] ?? 3) }}" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                </div>

                <div>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        Beban Maksimal Mentee Per Pembimbing (Orang)
                    </label>
                    <input type="number" name="maks_peserta_aktif_per_pembimbing" min="1" max="50" value="{{ old('maks_peserta_aktif_per_pembimbing', $pengaturan['maks_peserta_aktif_per_pembimbing'] ?? 20) }}" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                </div>

                <div>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        Batas Maksimal Anggota Kelompok (Orang) <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="number" name="maks_anggota_kelompok" min="1" max="10" required value="{{ old('maks_anggota_kelompok', $pengaturan['maks_anggota_kelompok'] ?? 4) }}" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                </div>

                <div>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        Durasi Magang Maksimal yang Diizinkan (Bulan) <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="number" name="durasi_magang_maks_bulan" min="1" max="12" required value="{{ old('durasi_magang_maks_bulan', $pengaturan['durasi_magang_maks_bulan'] ?? 6) }}" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                </div>
            </div>
        </x-gentelella.card>

        <!-- KARTU 2: ATURAN PELAKSANAAN, TUGAS & DOKUMEN -->
        <x-gentelella.card :hoverable="false" style="padding: 1.75rem 2rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 1rem;">
                <div style="width: 38px; height: 38px; border-radius: 8px; background: var(--blue-tint); color: var(--blue-700); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                </div>
                <div>
                    <h2 style="font-size: 1.0625rem; font-weight: 800; color: var(--neutral-900); margin: 0;">
                        Aturan Pelaksanaan, Logbook & Dokumen
                    </h2>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                <div>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        Batas Mundur Pengisian Logbook (Hari) <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="number" name="logbook_maks_mundur_hari" min="1" max="30" required value="{{ old('logbook_maks_mundur_hari', $pengaturan['logbook_maks_mundur_hari'] ?? 3) }}" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                </div>

                <div>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        Batas Ukuran Maksimal Berkas Upload (MB) <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="number" name="berkas_maks_mb" min="1" max="20" required value="{{ old('berkas_maks_mb', $pengaturan['berkas_maks_mb'] ?? 10) }}" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                </div>

                <div>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        Peringatan Mendekati Deadline Tugas (Hari) <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="number" name="deadline_mendekati_hari" min="1" max="14" required value="{{ old('deadline_mendekati_hari', $pengaturan['deadline_mendekati_hari'] ?? 3) }}" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                </div>

                <div>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        Target Batas Waktu SLA Verifikasi Berkas (Hari Kerja) <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="number" name="sla_verifikasi_hari" min="1" max="30" required value="{{ old('sla_verifikasi_hari', $pengaturan['sla_verifikasi_hari'] ?? 5) }}" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                </div>
            </div>
        </x-gentelella.card>

        <!-- KARTU 3: KEBIJAKAN RETENSI AKUN, SERTIFIKAT & AUDIT TRAIL -->
        <x-gentelella.card :hoverable="false" style="padding: 1.75rem 2rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 1rem;">
                <div style="width: 38px; height: 38px; border-radius: 8px; background: var(--blue-tint); color: var(--blue-700); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                </div>
                <div>
                    <h2 style="font-size: 1.0625rem; font-weight: 800; color: var(--neutral-900); margin: 0;">
                        Kebijakan Retensi Akun & Audit Trail
                    </h2>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                <div>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        Durasi Masa Tenggang Akun Pasca Magang (Bulan) <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="number" name="masa_tenggang_bulan" min="1" max="12" required value="{{ old('masa_tenggang_bulan', $pengaturan['masa_tenggang_bulan'] ?? 2) }}" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                </div>

                <div>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        Masa Retensi Log Akses Data (Hari) <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="number" name="retensi_audit_akses_data_hari" min="30" max="1825" required value="{{ old('retensi_audit_akses_data_hari', $pengaturan['retensi_audit_akses_data_hari'] ?? 365) }}" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                </div>
            </div>

            <!-- Tombol Pemangkasan On-Demand -->
            <div style="margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid var(--neutral-200); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
                <div>
                    <span style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800);">Pemeliharaan Penyimpanan Audit Trail</span>
                </div>
                <button type="submit" form="formPurgeAudit" onclick="return confirm('Apakah Anda yakin ingin memicu pemangkasan log akses data audit yang melampaui batas retensi sekarang?')" class="btn btn-outline" style="font-size: 0.8125rem; color: var(--red-600); border-color: var(--red-300);">
                    Jalankan Pemangkasan Log Sekarang
                </button>
            </div>
        </x-gentelella.card>

        <!-- KARTU 4: SAKELAR MODUL FITUR SISTEM (FEATURE TOGGLES) -->
        <x-gentelella.card :hoverable="false" style="padding: 1.75rem 2rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 1rem;">
                <div style="width: 38px; height: 38px; border-radius: 8px; background: var(--blue-tint); color: var(--blue-700); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="1" y="5" width="22" height="14" rx="7" ry="7"></rect>
                        <circle cx="16" cy="12" r="3"></circle>
                    </svg>
                </div>
                <div>
                    <h2 style="font-size: 1.0625rem; font-weight: 800; color: var(--neutral-900); margin: 0;">
                        Sakelar Modul Fitur Sistem (Feature Toggles)
                    </h2>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                <!-- Toggle 1: Modul Analitik Tahunan Eksekutif -->
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.25rem; background: var(--neutral-100); border: 1px solid var(--neutral-300); border-radius: 10px;">
                    <div style="display: flex; align-items: center; gap: 0.875rem;">
                        <div style="width: 32px; height: 32px; border-radius: 6px; background: var(--purple-100, #F3E8FF); color: var(--purple-700, #7E22CE); display: flex; align-items: center; justify-content: center;">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div>
                            <div style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-900);">Modul Analitik Tahunan Eksekutif</div>
                        </div>
                    </div>
                    <label style="position: relative; display: inline-flex; align-items: center; cursor: pointer; user-select: none;">
                        <input type="checkbox" name="fitur_analitik_aktif" value="1" {{ old('fitur_analitik_aktif', $pengaturan['fitur_analitik_aktif'] ?? '1') === '1' ? 'checked' : '' }} style="width: 20px; height: 20px; accent-color: var(--blue-600); cursor: pointer;">
                        <span style="margin-left: 0.5rem; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-700);">Aktif</span>
                    </label>
                </div>

                <!-- Toggle 2: Modul Pendaftaran Kelompok / Tim -->
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.25rem; background: var(--neutral-100); border: 1px solid var(--neutral-300); border-radius: 10px;">
                    <div style="display: flex; align-items: center; gap: 0.875rem;">
                        <div style="width: 32px; height: 32px; border-radius: 6px; background: var(--amber-100, #FEF3C7); color: var(--amber-700, #B45309); display: flex; align-items: center; justify-content: center;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                        <div>
                            <div style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-900);">Modul Pendaftaran Kelompok / Tim</div>
                        </div>
                    </div>
                    <label style="position: relative; display: inline-flex; align-items: center; cursor: pointer; user-select: none;">
                        <input type="checkbox" name="fitur_pendaftaran_kelompok_aktif" value="1" {{ old('fitur_pendaftaran_kelompok_aktif', $pengaturan['fitur_pendaftaran_kelompok_aktif'] ?? '1') === '1' ? 'checked' : '' }} style="width: 20px; height: 20px; accent-color: var(--blue-600); cursor: pointer;">
                        <span style="margin-left: 0.5rem; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-700);">Aktif</span>
                    </label>
                </div>
            </div>
        </x-gentelella.card>

        <!-- Tombol Aksi Bawah Form -->
        <div style="display: flex; justify-content: flex-end; padding-top: 0.5rem;">
            <button type="submit" style="height: 44px; padding: 0 2rem; background: var(--blue-600); color: #FFFFFF; font-weight: 700; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer; transition: all 0.15s; box-shadow: var(--shadow-subtle);">
                Simpan Perubahan Konfigurasi
            </button>
        </div>
    </form>

    <!-- Hidden Form untuk Trigger Pemangkasan Log On-Demand -->
    <form id="formPurgeAudit" action="{{ route('superadmin.pengaturan.purge-audit') }}" method="POST" style="display: none;">
        @csrf
    </form>
</div>
@endsection
