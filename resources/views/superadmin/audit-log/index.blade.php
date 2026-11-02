@extends('layouts.dashboard')

@section('title', 'Jejak Audit & Kepatuhan - SuperAdmin MagKom')

{{-- 
  Catatan Arsitektur (Zero Duplicate Header Parity):
  @section('page_title') sengaja tidak dideklarasikan agar Gentelella layout 
  tidak merender header duplikat bawaan Bootstrap 3. 
  Header Card Minimalist di Canvas 5a4d7a8b menjadi satu-satunya header resmi.
--}}

@push('styles')
    @vite(['resources/css/pages/superadmin/audit-log.css'])
    @include('superadmin.audit-log.partials._styles')
@endpush

@section('content')
<div class="audit-workspace-root">

    {{-- SECTION 1: MAIN CONTAINER & HEADER (CLEAN WORKSPACE & ZERO CLUTTER) --}}
    @include('superadmin.audit-log.partials._header')

    {{-- SECTION 2: PURE AUDIT KPI METRICS STRIP (ZERO TELEMETRY) --}}
    @include('superadmin.audit-log.partials._kpi_strip')

    {{-- SECTION 3: 2-COLUMN GRID WRAPPER (3 COL LEFT : 9 COL RIGHT) --}}
    <div class="audit-main-layout">
        {{-- Kolom Kiri (3 Kolom): 15-Channel Navigator & Panel Integritas WORM --}}
        @include('superadmin.audit-log.partials._channels_sidebar')

        {{-- Kolom Kanan (9 Kolom): Stream Header, Filter Toolbar, Enterprise Table, Pagination & Diff Inspector --}}
        <main class="audit-stream-col" aria-label="Aliran Jejak Audit">
            @include('superadmin.audit-log.partials._stream_table')
            @include('superadmin.audit-log.partials._diff_inspector')
        </main>
    </div>

</div>
@endsection

@push('scripts')
    @include('superadmin.audit-log.partials._scripts')
@endpush
