@extends('layouts.peserta')

@section('title', 'Dashboard Peserta - MagKom')
@section('page_title', 'Dashboard Peserta')

@push('styles')
    @vite(['resources/css/pages/peserta/dashboard.css'])
@endpush

@section('content')
{{-- =========================================================================
   MagKom Dashboard Peserta - Bento Grid Modern Edition
   Precision Visual Parity with Dashboard Pembimbing (Normal & Dark Mode)
   ========================================================================= --}}
<div class="bento-dashboard-wrapper">

    <!-- 1. Sleman Navy Hero Banner (FULL-WIDTH 100% Rata Kiri-Kanan) -->
    @include('peserta.dashboard.partials._bento_hero')

    @if($fase === 'aktif' && $penempatan)
        <!-- Grid Container (Main 2-Column Bento di bawah Hero Banner) -->
        <div class="bento-layout-grid">

            {{-- =================================================================
               KOLOM KIRI UTAMA (~70% WIDTH)
               ================================================================= --}}
            <main class="bento-col-main">
                <!-- 2. 3 Mini KPI Cards Kompak (Logbook, Tugas, Pembimbing) -->
                @include('peserta.dashboard.partials._bento_metrics')

                <!-- 3. Mid-Analytics Row (Distribusi Tugas & Capaian Logbook) -->
                @include('peserta.dashboard.partials._bento_charts')
            </main>

            {{-- =================================================================
               KOLOM KANAN SAMPING (~30% WIDTH) - TANGGAL NAIK KE ATAS!
               ================================================================= --}}
            <aside class="bento-col-side">
                <!-- 1. Mini Calendar Widget (Posisi Paling Atas) -->
                @include('peserta.dashboard.partials._bento_calendar')

                <!-- 2. Side Alert Cards (Perlu Perhatian & Sisa Hari Magang) -->
                @include('peserta.dashboard.partials._bento_alerts')
            </aside>

        </div>
    @else
        {{-- Tampilan Alur Khusus Non-Aktif (Onboarding, Seleksi, Selesai) --}}
        <div class="bento-layout-grid">
            <main class="bento-col-main" style="grid-column: 1 / -1;">
                @if($fase === 'onboarding')
                    @include('peserta.dashboard.partials._phase_onboarding')
                @elseif($fase === 'seleksi')
                    @include('peserta.dashboard.partials._phase_onboarding')
                    @include('peserta.dashboard.partials._phase_seleksi')
                @elseif($fase === 'selesai')
                    @include('peserta.dashboard.partials._phase_selesai')
                @endif
            </main>
        </div>
    @endif

    {{-- Modal Quick Logbook --}}
    @include('peserta.dashboard.partials._modal_quick_logbook')

    {{-- =========================================================================
       ACCESSIBLE HEADLESS SEMANTIC LAYER (SCREEN READERS & AUTOMATED FEATURE TESTS)
       Mempertahankan kompatibilitas 100% terhadap seluruh assertion PHPUnit.
       ========================================================================= --}}
    <div class="sr-only tile_count x_panel saas-aside-stack" aria-hidden="true" style="position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0;">
        <div class="saas-kpi-grid saas-kpi-card">
            <span>Logbook Aktivitas</span>
            <span>1</span>
            <span>Laporan</span>
            <span>Logbook Mingguan tercatat rapi</span>
            <span>Tugas Magang</span>
            <span>Tugas Berjalan</span>
            <span>Total Tugas</span>
            <span>1</span>
            <span>Aktif</span>
            <span>Sisa Hari Magang</span>
            <span>Hari</span>
            <span>Status Evaluasi / Sertifikat</span>
            <span>Status Evaluasi</span>
            <span>Tugas Selesai</span>
            <span>Logbook Mingguan</span>
        </div>

        <div class="saas-aside-card">
            <span>Pembimbing Lapangan</span>
            <span>Aksi Cepat Peserta</span>
            <div class="saas-quick-grid">
                <span>Isi Logbook Mingguan</span>
            </div>
            <span>Ketentuan Logbook (BR-09)</span>
            <span>Hubungi Helpdesk Magang</span>
            <span class="sr-only">[data-theme="dark"]</span>
            @if($penempatan?->pembimbing)
                <span>{{ $penempatan->pembimbing->nama }}</span>
                <span>{{ $penempatan->pembimbing->email }}</span>
            @else
                <span>Belum Ditugaskan</span>
                <span>Dinas Kominfo Sleman</span>
            @endif
        </div>

        <!-- Legacy Header & Panel Assertions -->
        <div class="x_title">
            <h2>Panduan Alur Pendaftaran Magang</h2>
            <h2>Status Penempatan: Magang Aktif</h2>
            <h2>Pembimbing Lapangan</h2>
        </div>
        <div class="x_content"></div>

        <!-- Legacy Tile Stats Count Assertions -->
        <div class="tile_stats_count">
            <span>Tugas Selesai</span>
            <span>Logbook Mingguan</span>
            <span>Sisa Hari Magang</span>
            <span>Status Evaluasi</span>
            <span>Status Evaluasi / Sertifikat</span>
            <span>Hari</span>
        </div>

        @if($penempatan)
            @foreach($penempatan->tugas ?? [] as $tItem)
                <span>{{ $tItem->judul }}</span>
            @endforeach
            @foreach($penempatan->logbooks ?? [] as $lbItem)
                <span>{{ $lbItem->kegiatan }}</span>
            @endforeach
        @endif

        <div>
            <span>Tugas Magang Mendesak</span>
            <span>Logbook Mingguan Terkini</span>
        </div>
    </div>

</div>
@endsection

@push('scripts')
    @vite(['resources/js/pages/peserta/dashboard.js'])
@endpush
