@extends('layouts.dashboard')

@section('title', 'Konfigurasi Sistem - SuperAdmin MagKom')
@section('topbar_title', 'Parameter Konfigurasi Sistem')

@section('breadcrumbs')
    <a href="{{ route('superadmin.dashboard') }}">SuperAdmin</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Pengaturan Sistem</span>
@endsection

@push('styles')
    @vite(['resources/css/pages/superadmin/pengaturan.css'])
@endpush

@section('content')
<div class="sa-fluid-container cfg-container">

    {{-- 1. Hero Banner Khas Sleman MagKom --}}
    <div class="sa-hero-banner sc-reveal-hero" data-scroll-reveal>
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
    <div class="tiles-grid sc-reveal-up sc-delay-1" data-scroll-reveal>
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

    <form id="formPengaturanSistem" class="cfg-form sc-reveal-up sc-delay-2" data-scroll-reveal action="{{ route('superadmin.pengaturan.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- KARTU 1: KEBIJAKAN KUOTA, PENDAFTARAN & KAPASITAS MAGANG -->
        <x-gentelella.card :hoverable="false" class="cfg-card">
            <div class="cfg-card-header">
                <div class="cfg-card-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="cfg-card-title">
                        Kebijakan Kuota, Pendaftaran &amp; Kapasitas Magang
                    </h2>
                </div>
            </div>

            <div class="cfg-form-grid">
                <div class="cfg-form-group">
                    <label class="cfg-label" for="kuota_umum_maks_per_bulan">
                        Batas Kuota Umum Publik Per Bulan (Orang) <span class="cfg-required-star">*</span>
                    </label>
                    <input type="number" id="kuota_umum_maks_per_bulan" name="kuota_umum_maks_per_bulan" min="1" max="100" required value="{{ old('kuota_umum_maks_per_bulan', $pengaturan['kuota_umum_maks_per_bulan'] ?? 12) }}" class="cfg-input">
                </div>

                <div class="cfg-form-group">
                    <label class="cfg-label" for="slot_khusus_default">
                        Default Alokasi Slot Khusus (Orang) <span class="cfg-required-star">*</span>
                    </label>
                    <input type="number" id="slot_khusus_default" name="slot_khusus_default" min="0" max="50" required value="{{ old('slot_khusus_default', $pengaturan['slot_khusus_default'] ?? 3) }}" class="cfg-input">
                </div>

                <div class="cfg-form-group">
                    <label class="cfg-label" for="maks_peserta_aktif_per_pembimbing">
                        Beban Maksimal Mentee Per Pembimbing (Orang)
                    </label>
                    <input type="number" id="maks_peserta_aktif_per_pembimbing" name="maks_peserta_aktif_per_pembimbing" min="1" max="50" value="{{ old('maks_peserta_aktif_per_pembimbing', $pengaturan['maks_peserta_aktif_per_pembimbing'] ?? 20) }}" class="cfg-input">
                </div>

                <div class="cfg-form-group">
                    <label class="cfg-label" for="maks_anggota_kelompok">
                        Batas Maksimal Anggota Kelompok (Orang) <span class="cfg-required-star">*</span>
                    </label>
                    <input type="number" id="maks_anggota_kelompok" name="maks_anggota_kelompok" min="1" max="10" required value="{{ old('maks_anggota_kelompok', $pengaturan['maks_anggota_kelompok'] ?? 4) }}" class="cfg-input">
                </div>

                <div class="cfg-form-group">
                    <label class="cfg-label" for="durasi_magang_maks_bulan">
                        Durasi Magang Maksimal yang Diizinkan (Bulan) <span class="cfg-required-star">*</span>
                    </label>
                    <input type="number" id="durasi_magang_maks_bulan" name="durasi_magang_maks_bulan" min="1" max="12" required value="{{ old('durasi_magang_maks_bulan', $pengaturan['durasi_magang_maks_bulan'] ?? 6) }}" class="cfg-input">
                </div>
            </div>
        </x-gentelella.card>

        <!-- KARTU 2: ATURAN PELAKSANAAN, TUGAS & DOKUMEN -->
        <x-gentelella.card :hoverable="false" class="cfg-card">
            <div class="cfg-card-header">
                <div class="cfg-card-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                    </svg>
                </div>
                <div>
                    <h2 class="cfg-card-title">
                        Aturan Pelaksanaan, Logbook &amp; Dokumen
                    </h2>
                </div>
            </div>

            <div class="cfg-form-grid">
                <div class="cfg-form-group">
                    <label class="cfg-label" for="logbook_maks_mundur_hari">
                        Batas Mundur Pengisian Logbook (Hari) <span class="cfg-required-star">*</span>
                    </label>
                    <input type="number" id="logbook_maks_mundur_hari" name="logbook_maks_mundur_hari" min="1" max="30" required value="{{ old('logbook_maks_mundur_hari', $pengaturan['logbook_maks_mundur_hari'] ?? 3) }}" class="cfg-input">
                </div>

                <div class="cfg-form-group">
                    <label class="cfg-label" for="berkas_maks_mb">
                        Batas Ukuran Maksimal Berkas Upload (MB) <span class="cfg-required-star">*</span>
                    </label>
                    <input type="number" id="berkas_maks_mb" name="berkas_maks_mb" min="1" max="20" required value="{{ old('berkas_maks_mb', $pengaturan['berkas_maks_mb'] ?? 10) }}" class="cfg-input">
                </div>

                <div class="cfg-form-group">
                    <label class="cfg-label" for="deadline_mendekati_hari">
                        Peringatan Mendekati Deadline Tugas (Hari) <span class="cfg-required-star">*</span>
                    </label>
                    <input type="number" id="deadline_mendekati_hari" name="deadline_mendekati_hari" min="1" max="14" required value="{{ old('deadline_mendekati_hari', $pengaturan['deadline_mendekati_hari'] ?? 3) }}" class="cfg-input">
                </div>

                <div class="cfg-form-group">
                    <label class="cfg-label" for="sla_verifikasi_hari">
                        Target Batas Waktu SLA Verifikasi Berkas (Hari Kerja) <span class="cfg-required-star">*</span>
                    </label>
                    <input type="number" id="sla_verifikasi_hari" name="sla_verifikasi_hari" min="1" max="30" required value="{{ old('sla_verifikasi_hari', $pengaturan['sla_verifikasi_hari'] ?? 5) }}" class="cfg-input">
                </div>
            </div>
        </x-gentelella.card>

        <!-- KARTU 3: KEBIJAKAN RETENSI AKUN & AUDIT TRAIL -->
        <x-gentelella.card :hoverable="false" class="cfg-card">
            <div class="cfg-card-header">
                <div class="cfg-card-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <div>
                    <h2 class="cfg-card-title">
                        Kebijakan Retensi Akun &amp; Audit Trail
                    </h2>
                </div>
            </div>

            <div class="cfg-form-grid">
                <div class="cfg-form-group">
                    <label class="cfg-label" for="masa_tenggang_bulan">
                        Durasi Masa Tenggang Akun Pasca Magang (Bulan) <span class="cfg-required-star">*</span>
                    </label>
                    <input type="number" id="masa_tenggang_bulan" name="masa_tenggang_bulan" min="1" max="12" required value="{{ old('masa_tenggang_bulan', $pengaturan['masa_tenggang_bulan'] ?? 2) }}" class="cfg-input">
                </div>

                <div class="cfg-form-group">
                    <label class="cfg-label" for="retensi_audit_akses_data_hari">
                        Masa Retensi Log Akses Data (Hari) <span class="cfg-required-star">*</span>
                    </label>
                    <input type="number" id="retensi_audit_akses_data_hari" name="retensi_audit_akses_data_hari" min="30" max="1825" required value="{{ old('retensi_audit_akses_data_hari', $pengaturan['retensi_audit_akses_data_hari'] ?? 365) }}" class="cfg-input">
                </div>
            </div>

            <!-- Tombol Pemangkasan On-Demand -->
            <div class="cfg-maintenance-row">
                <div>
                    <span class="cfg-maintenance-label">Pemeliharaan Penyimpanan Audit Trail</span>
                </div>
                <button type="submit" form="formPurgeAudit" data-confirm="Apakah Anda yakin ingin memicu pemangkasan log akses data audit yang melampaui batas retensi sekarang?" class="cfg-btn-purge">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                    <span>Jalankan Pemangkasan Log Sekarang</span>
                </button>
            </div>
        </x-gentelella.card>

        <!-- KARTU 4: KONTEN PUBLIK LANDING PAGE (HERO BANNER & PANDUAN RESMI) -->
        <x-gentelella.card :hoverable="false" class="cfg-card">
            <div class="cfg-card-header">
                <div class="cfg-card-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                        <polyline points="21 15 16 10 5 21"></polyline>
                    </svg>
                </div>
                <div>
                    <h2 class="cfg-card-title">
                        Konten Publik Landing Page (Hero Banner &amp; Panduan Resmi)
                    </h2>
                </div>
            </div>

            <div class="cfg-media-grid">
                <!-- 1. Hero Banner Carousel Slide 2 -->
                <div class="cfg-media-col">
                    <div class="cfg-media-header">
                        <label class="cfg-label" for="hero_image">
                            Foto Dokumentasi Hero Carousel (Slide 2)
                        </label>
                        @if($hasCustomHeroImage)
                            <span class="cfg-badge cfg-badge--custom">Foto Kustom Aktif</span>
                        @else
                            <span class="cfg-badge cfg-badge--default">Foto Default Sleman</span>
                        @endif
                    </div>

                    <div class="cfg-preview-box">
                        <img id="heroImagePreview" 
                             src="{{ $heroImageUrl }}" 
                             alt="Pratinjau Foto Dokumentasi Hero Carousel" 
                             class="cfg-preview-img">
                        <div class="cfg-preview-overlay">
                            <span>Pratinjau Tampilan Landing Page</span>
                        </div>
                    </div>

                    <div class="cfg-upload-controls">
                        <input type="file" 
                               id="hero_image" 
                               name="hero_image" 
                               accept="image/png,image/jpeg,image/jpg,image/webp" 
                               class="cfg-file-input"
                               data-max-size="5242880"
                               data-preview-target="heroImagePreview">
                        <label for="hero_image" class="cfg-file-btn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                            <span id="heroFileLabelText">Pilih Foto Pengganti...</span>
                        </label>

                        @if($hasCustomHeroImage)
                            <button type="button" 
                                    class="cfg-btn-reset-media btn-trigger-reset-content" 
                                    data-action-url="{{ route('superadmin.pengaturan.reset-hero') }}"
                                    data-title="Konfirmasi Reset Foto Hero Carousel"
                                    data-item-name="Foto Dokumentasi Hero Carousel (Slide 2)"
                                    data-item-desc="Foto kustom aktif di landing page"
                                    data-consequence="Foto kustom yang saat ini terpasang akan dihapus dari penyimpanan publik, dan tampilan landing page akan kembali menampilkan foto dokumentasi resmi bawaan Diskominfo Sleman."
                                    data-submit-text="Ya, Kembalikan ke Default">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="1 4 1 10 7 10"></polyline>
                                    <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path>
                                </svg>
                                <span>Reset ke Default</span>
                            </button>
                        @endif
                    </div>
                    <p class="cfg-help-text">
                        Format: JPG, PNG, WebP (maks. 5MB). Rekomendasi rasio landscape 16:5 atau lebar min. 1200px.
                    </p>
                </div>

                <!-- 2. Berkas Panduan Pendaftaran PDF -->
                <div class="cfg-media-col">
                    <div class="cfg-media-header">
                        <label class="cfg-label" for="panduan_pdf">
                            Berkas Unduh Panduan Pendaftaran (PDF)
                        </label>
                        @if($hasCustomPanduanPdf)
                            <span class="cfg-badge cfg-badge--custom">PDF Kustom Aktif</span>
                        @else
                            <span class="cfg-badge cfg-badge--default">Generator PDF Sistem</span>
                        @endif
                    </div>

                    <div class="cfg-doc-box">
                        <div class="cfg-doc-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10 9 9 9 8 9"></polyline>
                            </svg>
                        </div>
                        <div class="cfg-doc-details">
                            <span class="cfg-doc-title" id="pdfFileNameDisplay">{{ $panduanPdfName }}</span>
                            <span class="cfg-doc-meta">
                                @if($hasCustomPanduanPdf && $panduanPdfSize)
                                    Ukuran: {{ $panduanPdfSize }} KB &bull; Berkas Kustom
                                @else
                                    Dihasilkan otomatis oleh sistem (A4 PDF)
                                @endif
                            </span>
                        </div>
                        <a href="{{ route('panduan.pdf') }}" target="_blank" rel="noopener noreferrer" class="cfg-doc-preview-link" title="Pratinjau Unduh PDF">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                <polyline points="15 3 21 3 21 9"></polyline>
                                <line x1="10" y1="14" x2="21" y2="3"></line>
                            </svg>
                        </a>
                    </div>

                    <div class="cfg-upload-controls">
                        <input type="file" 
                               id="panduan_pdf" 
                               name="panduan_pdf" 
                               accept="application/pdf" 
                               class="cfg-file-input"
                               data-max-size="10485760"
                               data-name-target="pdfFileNameDisplay">
                        <label for="panduan_pdf" class="cfg-file-btn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                            <span id="pdfFileLabelText">Pilih Berkas PDF Baru...</span>
                        </label>

                        @if($hasCustomPanduanPdf)
                            <button type="button" 
                                    class="cfg-btn-reset-media btn-trigger-reset-content" 
                                    data-action-url="{{ route('superadmin.pengaturan.reset-panduan') }}"
                                    data-title="Konfirmasi Reset Berkas Panduan PDF"
                                    data-item-name="Berkas Unduh Panduan Pendaftaran (PDF)"
                                    data-item-desc="{{ $panduanPdfName }}"
                                    data-consequence="Berkas PDF kustom akan dihapus dari penyimpanan publik, dan tombol 'Unduh Panduan PDF' di landing page akan kembali mengunduh berkas resmi yang dihasilkan otomatis oleh sistem."
                                    data-submit-text="Ya, Kembalikan ke PDF Sistem">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="1 4 1 10 7 10"></polyline>
                                    <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path>
                                </svg>
                                <span>Reset ke PDF Sistem</span>
                            </button>
                        @endif
                    </div>
                    <p class="cfg-help-text">
                        Format: Dokumen PDF (.pdf) (maks. 10MB). Berkas ini akan diunduh saat peserta mengklik tombol "Unduh Panduan PDF" di landing page.
                    </p>
                </div>
            </div>
        </x-gentelella.card>

        <!-- Tombol Aksi Bawah Form -->
        <div class="cfg-bottom-actions">
            <button type="submit" class="cfg-btn-submit">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                    <polyline points="7 3 7 8 15 8"></polyline>
                </svg>
                <span>Simpan Perubahan Konfigurasi</span>
            </button>
        </div>
    </form>

    <!-- Hidden Form untuk Trigger Pemangkasan Log On-Demand -->
    <form id="formPurgeAudit" action="{{ route('superadmin.pengaturan.purge-audit') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- =====================================================================
         MODAL KONFIRMASI RESET KONTEN PUBLIK (HERO BANNER & PANDUAN RESMI)
         Sesuai MagKom Design System v1.0 & Identik dengan Modal Staf
         ===================================================================== -->
    <div id="modalKonfirmasiResetKonten" 
         class="cfg-modal-backdrop"
         style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;"
         role="dialog" 
         aria-modal="true" 
         aria-labelledby="modalResetKontenTitle">
        <div class="cfg-modal-card" style="background: var(--neutral-0, #FFFFFF); border-radius: 16px; width: 100%; max-width: 480px; padding: 2rem; box-shadow: var(--shadow-modal, 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)); position: relative; border: 1px solid var(--neutral-300, #CBD5E1);">
            
            <!-- Header Modal -->
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.25rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div id="resetKontenIconWrap" style="width: 42px; height: 42px; border-radius: 10px; background: #FEE2E2; color: #DC2626; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <!-- SVG Warning Icon Identik Staf Modal -->
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                    </div>
                    <div>
                        <h3 id="modalResetKontenTitle" style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0; line-height: 1.3;">
                            {{ __('Konfirmasi Reset Konten') }}
                        </h3>
                        <span style="font-size: 0.8125rem; color: var(--neutral-500, #64748B);">
                            {{ __('Pengelolaan Konten Publik Landing Page') }}
                        </span>
                    </div>
                </div>
                <button type="button" 
                        class="cfg-modal-btn-dismiss"
                        data-modal-dismiss="modalKonfirmasiResetKonten" 
                        title="Tutup dialog">
                    &times;
                </button>
            </div>

            <!-- Form Aksi Nyata -->
            <form id="formResetKontenModal" method="POST" style="display: flex; flex-direction: column; gap: 1.25rem; margin: 0;">
                @csrf

                <!-- Kotak Peringatan Konsekuensi Identik Staf Modal -->
                <div id="resetKontenBoxAlert" style="background: #FFF1F2; border: 1px solid #FECDD3; border-radius: 10px; padding: 1rem 1.25rem;">
                    <p id="resetKontenMessage" style="font-size: 0.875rem; color: #9F1239; margin: 0 0 0.5rem 0; line-height: 1.5;">
                        {{ __('Apakah Anda yakin ingin mengembalikan konten berikut ke default bawaan sistem?') }}
                    </p>
                    <div style="background: rgba(255, 255, 255, 0.75); border-radius: 6px; padding: 0.5rem 0.75rem; border: 1px solid rgba(225, 29, 72, 0.15);">
                        <div style="font-weight: 700; color: var(--neutral-900, #0F172A); font-size: 0.875rem;" id="resetKontenNama">
                            {{ __('Nama Konten') }}
                        </div>
                        <div style="font-size: 0.75rem; color: var(--neutral-600, #475569);" id="resetKontenDesc">
                            {{ __('Keterangan') }}
                        </div>
                    </div>
                    <p id="resetKontenConsequence" style="font-size: 0.75rem; color: #BE123C; margin: 0.65rem 0 0 0; line-height: 1.4;">
                        &bull; {{ __('Aset kustom akan dihapus dan landing page akan kembali menggunakan tampilan resmi bawaan sistem.') }}
                    </p>
                </div>

                <!-- Footer Tombol Aksi -->
                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--neutral-200, #E2E8F0); padding-top: 1rem;">
                    <button type="button" 
                            class="cfg-modal-btn-cancel"
                            data-modal-dismiss="modalKonfirmasiResetKonten">
                        {{ __('Batal') }}
                    </button>
                    <button type="submit" 
                            id="btnSubmitResetKonten" 
                            class="cfg-modal-btn-danger">
                        <span id="btnSubmitResetKontenText">{{ __('Ya, Kembalikan ke Default') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    @vite(['resources/js/pages/superadmin/pengaturan.js'])
@endpush
@endsection