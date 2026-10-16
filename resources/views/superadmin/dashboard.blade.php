@extends('layouts.dashboard')

@section('title', 'Dashboard SuperAdmin - MagKom')
@section('page_title', 'Dashboard SuperAdmin')
@section('page_description', 'Akses penuh: kelola pengguna, role, rubrik, audit log, konfigurasi, dan seluruh modul Administrasi, Pembimbing, serta Peserta.')

@section('breadcrumbs')
    <span class="breadcrumb-current">Dashboard</span>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/superadmin-dashboard.css') }}">
@endpush

@section('content')
<div class="sa-page">
    {{-- A. Header Band & Pintasan Tindakan --}}
    @include('superadmin.dashboard.partials._header')

    {{-- B. Perhatian / Tindakan Cepat --}}
    @include('superadmin.dashboard.partials._attention')

    {{-- C. Empat Kartu Metrik Utama --}}
    @include('superadmin.dashboard.partials._metrics')

    {{-- D. Segmented Tab Navigation Controller --}}
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
@endsection

@push('scripts')
    <script src="{{ asset('js/superadmin-dashboard.js') }}" defer></script>
@endpush
