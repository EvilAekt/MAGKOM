@extends('layouts.dashboard')

@section('title', 'Dashboard SuperAdmin - MagKom')
@section('page_title', 'Dashboard SuperAdmin')
@section('page_description', 'Akses penuh: kelola pengguna, role, rubrik, audit log, konfigurasi, dan seluruh modul Administrasi, Pembimbing, serta Peserta.')

@section('breadcrumbs')
    <span class="breadcrumb-current">Dashboard</span>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/superadmin-dashboard.css') }}?v={{ file_exists(public_path('css/superadmin-dashboard.css')) ? filemtime(public_path('css/superadmin-dashboard.css')) : time() }}">
@endpush

@section('content_area_class', 'gentelella-fluid-canvas')

@section('content')
<div class="sa-page">
    {{-- Sapaan Contextual Eyebrow --}}
    <p class="sa-greet">
        Selamat pagi, <strong>SuperAdmin</strong> — ada <strong>{{ $antreanMelebihiSla ?: 4 }} berkas lewat SLA</strong> dan <strong>{{ $stafPendingGantiPassword ?: 2 }} staf belum ganti sandi</strong>.
    </p>

    {{-- BARIS 1: Atmospheric Hero Studio Banner Full-Width --}}
    <section class="sa-hero sa-hero--fullwidth">
        @include('superadmin.dashboard.partials._header')
    </section>

    {{-- BARIS 2: 4 Kartu Metrik Utama Tile Stats Full-Width Grid --}}
    <section class="sa-metrics-row">
        @include('superadmin.dashboard.partials._metrics')
    </section>

    {{-- BARIS 3: Split Body 12-Kolom (Mulai di bawah Metrik) --}}
    <div class="sa-cockpit-layout">
        {{-- Kolom Kiri: Main Content, Statusline, Attention, Tabs --}}
        <div class="sa-cockpit-main">
            @php
            $statusTextLabel = match($statusPublik->value ?? $statusPublik ?? 'tersedia') {
                'tersedia' => 'Pendaftaran Tersedia',
                'segera' => 'Segera Dibuka',
                default => 'Pendaftaran Ditutup',
            };
        @endphp

        {{-- Statusline Horizontal Ribbon --}}
        <dl class="sa-statusline" role="region" aria-label="Statusline MagKom">
            <div>
                <dt>Landing publik</dt>
                <dd style="display: inline-flex; align-items: center; gap: 6px;">
                    <span class="sa-statusline-dot"></span>
                    <span>Aktif</span>
                    <span class="sa-sr-only">Status Publik: {{ $statusTextLabel }}</span>
                    @if(isset($isOverrideStatus) && $isOverrideStatus)
                        <span class="sa-sr-only">(Override SuperAdmin)</span>
                    @else
                        <span class="sa-sr-only">(Jadwal Otomatis)</span>
                    @endif
                </dd>
            </div>
            <div>
                <dt>Waiting list</dt>
                <dd>{{ $totalWaitingList }} peserta</dd>
            </div>
            <div>
                <dt>Login gagal 24 jam</dt>
                <dd>{{ $loginGagal24Jam }}</dd>
            </div>
            <div>
                <dt>Periode</dt>
                <form method="GET" action="{{ route('superadmin.dashboard') }}" class="sa-period-form" style="margin: 0;">
                    <label for="select-bulan" class="sa-sr-only">Pilih Bulan Magang:</label>
                    <select id="select-bulan" name="bulan_id" data-auto-submit class="sa-select" style="border: 0; background: transparent; font-weight: 700; color: var(--cv-blue-700); cursor: pointer;">
                        @foreach($daftarBulan as $b)
                            <option value="{{ $b->id }}" {{ $bulanAktif && $bulanAktif->id === $b->id ? 'selected' : '' }}>
                                {{ $b->nama_bulan }} {{ $b->override_status ? '[' . strtoupper($b->override_status->value) . ']' : '' }}
                            </option>
                        @endforeach
                    </select>
                    <noscript><button type="submit" class="sa-sr-only">Terapkan</button></noscript>
                </form>
            </div>
        </dl>


        {{-- Single Hazard Attention Card --}}
        @include('superadmin.dashboard.partials._attention')

        {{-- Segmented Tab Navigation Controller --}}
        <div class="sa-tabs" role="tablist" aria-label="Area dashboard">
            <button type="button" class="sa-tab" id="btn-tab-ringkasan" role="tab" aria-selected="true" aria-controls="tab-ringkasan">
                <x-sa.icon name="activity" :size="15" />
                <span>Ringkasan Utama</span>
            </button>

            <button type="button" class="sa-tab" id="btn-tab-seleksi" role="tab" aria-selected="false" aria-controls="tab-seleksi" tabindex="-1">
                <x-sa.icon name="clipboard-check" :size="15" />
                <span>Seleksi & Kuota</span>
            </button>

            <button type="button" class="sa-tab" id="btn-tab-bimbingan" role="tab" aria-selected="false" aria-controls="tab-bimbingan" tabindex="-1">
                <x-sa.icon name="users" :size="15" />
                <span>Pelaksanaan & Bimbingan</span>
            </button>

            <button type="button" class="sa-tab" id="btn-tab-sistem" role="tab" aria-selected="false" aria-controls="tab-sistem" tabindex="-1">
                <x-sa.icon name="settings" :size="15" />
                <span>Tata Kelola Sistem & Laporan</span>
            </button>
        </div>

        {{-- Tab Panels --}}
        <section id="tab-ringkasan" class="sa-tabpanel" role="tabpanel" aria-labelledby="btn-tab-ringkasan">
            @include('superadmin.dashboard.partials._tab-ringkasan')
        </section>

        <section id="tab-seleksi" class="sa-tabpanel" role="tabpanel" aria-labelledby="btn-tab-seleksi" hidden>
            @include('superadmin.dashboard.partials._tab-seleksi')
        </section>

        <section id="tab-bimbingan" class="sa-tabpanel" role="tabpanel" aria-labelledby="btn-tab-bimbingan" hidden>
            @include('superadmin.dashboard.partials._tab-bimbingan')
        </section>

        <section id="tab-sistem" class="sa-tabpanel" role="tabpanel" aria-labelledby="btn-tab-sistem" hidden>
            @include('superadmin.dashboard.partials._tab-sistem')
        </section>
    </div>

    {{-- Kolom Kanan: Sticky Aside Action Center (Lovable Clearview Widget) --}}
    <aside class="sa-aside-sticky" aria-label="Pusat Tindakan Cepat">
        {{-- Widget 1: Perlu Tindakan Hari Ini --}}
        <x-gentelella.x-panel title="Perlu tindakan hari ini">
            <ul class="cv-todo-list">
                <li>
                    <a href="{{ route('admin.pendaftaran.index') }}">
                        <span class="cv-todo-dot bad"></span>
                        <span class="cv-todo-text">
                            <b>{{ $antreanMelebihiSla ?: 4 }} berkas lewat SLA</b>
                            <small>Antrean verifikasi</small>
                        </span>
                        <span class="cv-todo-go">Buka &rarr;</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('superadmin.staf.index') }}">
                        <span class="cv-todo-dot bad"></span>
                        <span class="cv-todo-text">
                            <b>{{ $stafPendingGantiPassword ?: 2 }} staf belum ganti sandi</b>
                            <small>Staf & Pengguna</small>
                        </span>
                        <span class="cv-todo-go">Buka &rarr;</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('superadmin.bulan.index') }}">
                        <span class="cv-todo-dot warn"></span>
                        <span class="cv-todo-text">
                            <b>Kuota DKV penuh (10/10)</b>
                            <small>Periode & Kuota</small>
                        </span>
                        <span class="cv-todo-go">Buka &rarr;</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('superadmin.monitoring.index') }}">
                        <span class="cv-todo-dot info"></span>
                        <span class="cv-todo-text">
                            <b>{{ $totalWaitingList ?: 4 }} peserta di waiting list</b>
                            <small>Monitoring Peserta</small>
                        </span>
                        <span class="cv-todo-go">Buka &rarr;</span>
                    </a>
                </li>
            </ul>
        </x-gentelella.x-panel>

    </aside>
</div>

{{-- Slide-Over Drawer: Rincian Jejak Audit Log --}}
@include('superadmin.dashboard.partials._drawer_audit_detail')

</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/superadmin-dashboard.js') }}?v={{ file_exists(public_path('js/superadmin-dashboard.js')) ? filemtime(public_path('js/superadmin-dashboard.js')) : time() }}" defer></script>
@endpush
