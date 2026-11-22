@extends('layouts.dashboard')

@section('title', __('Kebijakan Kuota & Kapasitas Magang - Administrasi MagKom'))
@section('topbar_title', __('Kebijakan Kuota & Pendaftaran'))

@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}">{{ __('Administrasi') }}</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">{{ __('Kebijakan Kuota') }}</span>
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
                <h1 class="banner-title">{{ __('Kebijakan Kuota & Kapasitas Magang') }}</h1>
                <div class="banner-subtitle">
                    <span>{{ __('Pengelola:') }} <strong>{{ __('Staf Administrasi Magang Dinas') }}</strong></span>
                    <span class="bullet-separator">&bull;</span>
                    <span>{{ __('Status:') }} <strong>{{ __('Tersimpan di Sistem') }}</strong></span>
                </div>
            </div>
            <div class="banner-actions-group">
                <button type="submit" form="formKebijakanMagang" class="btn-banner-action">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    <span>{{ __('Simpan Kebijakan') }}</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Form Kebijakan Kuota, Pendaftaran & Kapasitas Magang --}}
    <form id="formKebijakanMagang" class="cfg-form sc-reveal-up sc-delay-1" data-scroll-reveal action="{{ route('admin.kebijakan.update') }}" method="POST">
        @csrf
        @method('PUT')

        <!-- KARTU: KEBIJAKAN KUOTA, PENDAFTARAN & KAPASITAS MAGANG -->
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
                        {{ __('Kebijakan Kuota, Pendaftaran & Kapasitas Magang') }}
                    </h2>
                </div>
            </div>

            <div class="cfg-form-grid cfg-form-grid-3">
                <div class="cfg-form-group">
                    <label class="cfg-label" for="kuota_umum_maks_per_bulan">
                        {{ __('Batas Kuota Umum Publik Per Bulan (Orang)') }} <span class="cfg-required-star">*</span>
                    </label>
                    <input type="number" id="kuota_umum_maks_per_bulan" name="kuota_umum_maks_per_bulan" min="1" max="100" required value="{{ old('kuota_umum_maks_per_bulan', $kuotaUmumMaksPerBulan) }}" class="cfg-input">
                </div>

                <div class="cfg-form-group">
                    <label class="cfg-label" for="slot_khusus_default">
                        {{ __('Default Alokasi Slot Khusus (Orang)') }} <span class="cfg-required-star">*</span>
                    </label>
                    <input type="number" id="slot_khusus_default" name="slot_khusus_default" min="0" max="50" required value="{{ old('slot_khusus_default', $slotKhususDefault) }}" class="cfg-input">
                </div>

                <div class="cfg-form-group">
                    <label class="cfg-label" for="maks_peserta_aktif_per_pembimbing">
                        {{ __('Beban Maksimal Mentee Per Pembimbing (Orang)') }}
                    </label>
                    <input type="number" id="maks_peserta_aktif_per_pembimbing" name="maks_peserta_aktif_per_pembimbing" min="1" max="50" value="{{ old('maks_peserta_aktif_per_pembimbing', $maksPesertaAktifPerPembimbing) }}" class="cfg-input">
                </div>

                <div class="cfg-form-group">
                    <label class="cfg-label" for="maks_anggota_kelompok">
                        {{ __('Batas Maksimal Anggota Kelompok (Orang)') }} <span class="cfg-required-star">*</span>
                    </label>
                    <input type="number" id="maks_anggota_kelompok" name="maks_anggota_kelompok" min="1" max="10" required value="{{ old('maks_anggota_kelompok', $maksAnggotaKelompok) }}" class="cfg-input">
                </div>

                <div class="cfg-form-group">
                    <label class="cfg-label" for="durasi_magang_maks_bulan">
                        {{ __('Durasi Magang Maksimal yang Diizinkan (Bulan)') }} <span class="cfg-required-star">*</span>
                    </label>
                    <input type="number" id="durasi_magang_maks_bulan" name="durasi_magang_maks_bulan" min="1" max="12" required value="{{ old('durasi_magang_maks_bulan', $durasiMagangMaksBulan) }}" class="cfg-input">
                </div>

                <div class="cfg-form-group">
                    <label class="cfg-label" for="sla_verifikasi_hari">
                        {{ __('Target Batas Waktu SLA Verifikasi Berkas (Hari Kerja)') }} <span class="cfg-required-star">*</span>
                    </label>
                    <input type="number" id="sla_verifikasi_hari" name="sla_verifikasi_hari" min="1" max="30" required value="{{ old('sla_verifikasi_hari', $slaVerifikasiHari) }}" class="cfg-input">
                </div>
            </div>
        </x-gentelella.card>
    </form>
</div>
@endsection
