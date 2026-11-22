@extends('layouts.dashboard')

@section('title', __('Aturan Bimbingan & Dokumen - Pembimbing Lapangan MagKom'))
@section('topbar_title', __('Aturan Bimbingan & Dokumen'))

@section('breadcrumbs')
    <a href="{{ route('pembimbing.dashboard') }}">{{ __('Pembimbing') }}</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">{{ __('Aturan Bimbingan') }}</span>
@endsection

@push('styles')
    @vite(['resources/css/pages/superadmin/pengaturan.css'])
@endpush

@section('content')
<div class="sa-fluid-container cfg-container">

    {{-- Flash Notifications --}}
    @if(session('success'))
        <x-gentelella.callout type="success" :title="__('Berhasil')">
            {{ session('success') }}
        </x-gentelella.callout>
    @endif
    @if(session('error'))
        <x-gentelella.callout type="danger" :title="__('Gagal')">
            {{ session('error') }}
        </x-gentelella.callout>
    @endif
    @if($errors->any())
        <x-gentelella.callout type="danger" :title="__('Perhatian')">
            {{ $errors->first() }}
        </x-gentelella.callout>
    @endif

    {{-- Hero Banner Khas Sleman MagKom --}}
    <div class="sa-hero-banner sc-reveal-hero" data-scroll-reveal>
        <div class="banner-top-row">
            <div>
                <h1 class="banner-title">{{ __('Aturan Pelaksanaan, Tugas & Dokumen') }}</h1>
                <div class="banner-subtitle">
                    <span>{{ __('Pengelola:') }} <strong>{{ __('Pembimbing Lapangan ASN') }}</strong></span>
                    <span class="bullet-separator">&bull;</span>
                    <span>{{ __('Status:') }} <strong>{{ __('Tersimpan di Sistem') }}</strong></span>
                </div>
            </div>
            <div class="banner-actions-group">
                <button type="submit" form="formAturanBimbingan" class="btn-banner-action">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    <span>{{ __('Simpan Aturan') }}</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Form Aturan Pelaksanaan, Tugas & Dokumen --}}
    <form id="formAturanBimbingan" class="cfg-form sc-reveal-up sc-delay-1" data-scroll-reveal action="{{ route('pembimbing.aturan.update') }}" method="POST">
        @csrf
        @method('PUT')

        <!-- KARTU: ATURAN PELAKSANAAN, TUGAS & DOKUMEN -->
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
                        {{ __('Aturan Pelaksanaan, Logbook & Dokumen') }}
                    </h2>
                </div>
            </div>

            <div class="cfg-form-grid">
                <div class="cfg-form-group">
                    <label class="cfg-label" for="berkas_maks_mb">
                        {{ __('Batas Ukuran Maksimal Berkas Upload (MB)') }} <span class="cfg-required-star">*</span>
                    </label>
                    <input type="number" id="berkas_maks_mb" name="berkas_maks_mb" min="1" max="20" required value="{{ old('berkas_maks_mb', $berkasMaksMb) }}" class="cfg-input">
                </div>

                <div class="cfg-form-group">
                    <label class="cfg-label" for="deadline_mendekati_hari">
                        {{ __('Peringatan Mendekati Deadline Tugas (Hari)') }} <span class="cfg-required-star">*</span>
                    </label>
                    <input type="number" id="deadline_mendekati_hari" name="deadline_mendekati_hari" min="1" max="14" required value="{{ old('deadline_mendekati_hari', $deadlineMendekatiHari) }}" class="cfg-input">
                </div>
            </div>
        </x-gentelella.card>
    </form>
</div>
@endsection
