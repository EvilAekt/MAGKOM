@extends('layouts.dashboard')

@section('title', 'Dashboard SuperAdmin - MagKom')
@section('page_title', 'Dashboard SuperAdmin')
@section('page_description', 'Akses penuh: kelola pengguna, role, rubrik, audit log, konfigurasi, dan seluruh modul Administrasi, Pembimbing, serta Peserta.')

@section('breadcrumbs')
    <span class="breadcrumb-current">Dashboard</span>
@endsection


@push('styles')
    @include('superadmin.dashboard.partials._styles')
@endpush

@section('content')
<div class="sa-page">
    @include('superadmin.dashboard.partials._header')
    @include('superadmin.dashboard.partials._perlu-tindakan')
    @include('superadmin.dashboard.partials._kpi')
    @include('superadmin.dashboard.partials._tab-nav')
    @include('superadmin.dashboard.partials._tab-ringkasan')
    @include('superadmin.dashboard.partials._tab-seleksi')
    @include('superadmin.dashboard.partials._tab-bimbingan')
    @include('superadmin.dashboard.partials._tab-sistem')
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/superadmin-dashboard.js') }}" defer></script>
@endpush
