@push('styles')
    @vite(['resources/css/pages/admin/penempatan-index.css'])
@endpush

@push('scripts')
    @vite(['resources/js/pages/admin/penempatan-antrean-show.js'])
@endpush

@extends('layouts.dashboard')

@php
    $user = $pendaftaran->user;
    $profil = $user?->profil;
    $cleanNama = str_replace(' (Peserta Magang)', '', (string) ($user?->nama ?? 'Peserta'));
    $fotoUrl = $profil?->foto_url ?? $user?->foto_url;
    $isTim = $pendaftaran->tipe === 'kelompok' || !is_null($pendaftaran->kelompok_magang_id);
    $namaTim = $pendaftaran->kelompokMagang?->nama_kelompok;
    $heroTitle = ($isTim && !empty($namaTim)) ? $namaTim : $cleanNama;
    $memberProfiles = $anggotaTimProfil ?? collect();
    $firstBidang = $pendaftaran->kuota?->jurusan?->bidang?->first();
    $defaultBidangId = old('bidang_id', $firstBidang?->id);
    $jenjangLabel = $profil?->jenjang instanceof \App\Enums\JenjangEnum
        ? $profil->jenjang->label()
        : ($profil?->jenjang ? strtoupper((string) $profil->jenjang) : '-');
    $nomorReg = 'REG-' . str_pad((string) $pendaftaran->id, 4, '0', STR_PAD_LEFT);
@endphp

@section('title', 'Detail Antrean Peserta: ' . $heroTitle . ' - Admin MagKom')
@section('topbar_title', 'Detail Antrean Penempatan')

@section('content')
<div class="antrean-detail-page">

    {{-- 1. HERO BANNER --}}
    <div class="adm-hero-banner">
        <div class="adm-hero-content">
            <div class="adm-hero-text">
                <h1 class="adm-hero-title">{{ $heroTitle }}</h1>
                <div class="adm-hero-subtitle">
                    <span>{{ $nomorReg }}</span>
                    <span class="sep">&bull;</span>
                    @if($isTim)
                        <span>Pendaftaran Tim ({{ $memberProfiles->count() ?: (($pendaftaran->kelompokMagang?->anggota?->count() ?? 0) + 1) }} Orang)</span>
                        <span class="sep">&bull;</span>
                        <span>Ketua: {{ $cleanNama }}</span>
                    @else
                        <span>Pendaftaran Individu</span>
                    @endif
                    <span class="sep">&bull;</span>
                    <span>Lolos Berkas &bull; Menunggu Penempatan Pembimbing</span>
                </div>
            </div>
            <div class="adm-hero-actions">
                <a href="{{ route('admin.penempatan.index', ['tab' => 'belum']) }}" class="adm-hero-btn-secondary">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    <span>Kembali ke Antrean</span>
                </a>
            </div>
        </div>
    </div>

    {{-- FLASH MESSAGES --}}
    @if(session('error'))
        <div class="antrean-detail-alert antrean-detail-alert-error" role="alert">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="antrean-detail-alert antrean-detail-alert-error" role="alert">
            <ul class="antrean-detail-error-list">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- 2. GRID 2 KOLOM --}}
    <div class="antrean-detail-grid">

        {{-- KOLOM KIRI: FOTO & BIODATA PESERTA (INDIVIDU ATAU SELURUH ANGGOTA TIM) --}}
        <div class="antrean-detail-col">
            @if($isTim && $memberProfiles->isNotEmpty())
                <x-gentelella.x-panel title="Biodata &amp; Foto Seluruh Anggota Tim" id="panel-biodata-tim">
                    {{-- Pemilih Anggota Tim (Ketua + Anggota) --}}
                    <div class="antrean-member-tabs" role="tablist" aria-label="Pilih Anggota Tim">
                        @foreach($memberProfiles as $idx => $member)
                            <button type="button"
                                    id="tab-member-{{ $member['key'] }}"
                                    class="antrean-member-tab {{ $idx === 0 ? 'is-active' : '' }}"
                                    role="tab"
                                    aria-selected="{{ $idx === 0 ? 'true' : 'false' }}"
                                    aria-controls="panel-member-{{ $member['key'] }}"
                                    data-action="select-member-tab"
                                    data-member-key="{{ $member['key'] }}">
                                <span class="antrean-member-tab-avatar" aria-hidden="true">
                                    @if(!empty($member['foto_url']))
                                        <img src="{{ $member['foto_url'] }}" alt="{{ $member['nama'] }}">
                                    @else
                                        {{ strtoupper(substr($member['nama'], 0, 1)) }}
                                    @endif
                                </span>
                                <span class="antrean-member-tab-meta">
                                    <span class="antrean-member-tab-name">{{ $member['nama'] }}</span>
                                    <span class="antrean-member-tab-role">
                                        {{ $member['peran'] }} &bull; {{ $member['kelengkapan_persen'] }}%
                                    </span>
                                </span>
                            </button>
                        @endforeach
                    </div>

                    {{-- Panel Biodata & Foto per Anggota --}}
                    @foreach($memberProfiles as $idx => $member)
                        <div id="panel-member-{{ $member['key'] }}"
                             class="antrean-member-panel"
                             role="tabpanel"
                             aria-labelledby="tab-member-{{ $member['key'] }}"
                             data-member-panel="{{ $member['key'] }}"
                             {{ $idx > 0 ? 'hidden' : '' }}>
                            <div class="antrean-profile-header">
                                <div class="antrean-profile-photo-wrap">
                                    @if(!empty($member['foto_url']))
                                        <img src="{{ $member['foto_url'] }}" alt="Foto {{ $member['nama'] }}" class="antrean-profile-photo">
                                    @else
                                        <div class="antrean-profile-photo-fallback" aria-hidden="true">
                                            {{ strtoupper(substr($member['nama'], 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="antrean-profile-summary">
                                    <h2 class="antrean-profile-name">{{ $member['nama'] }}</h2>
                                    <p class="antrean-profile-instansi">{{ $member['institusi'] }}</p>
                                    <div class="antrean-profile-badges">
                                        <span class="antrean-badge {{ $member['is_ketua'] ? 'antrean-badge-ketua' : 'antrean-badge-anggota' }}">
                                            {{ $member['peran'] }}
                                        </span>
                                        <span class="antrean-badge {{ $member['is_profil_lengkap'] ? 'antrean-badge-success' : 'antrean-badge-warning' }}">
                                            {{ $member['is_profil_lengkap'] ? 'Profil Lengkap (100%)' : 'Profil ' . $member['kelengkapan_persen'] . '%' }}
                                        </span>
                                        <span class="antrean-badge antrean-badge-info">
                                            {{ $member['status_aktif'] ? 'Akun Aktif' : 'Akun Belum Aktif' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <dl class="antrean-biodata-grid">
                                <div class="antrean-biodata-item">
                                    <dt>Email Akun</dt>
                                    <dd>{{ $member['email'] }}</dd>
                                </div>
                                <div class="antrean-biodata-item">
                                    <dt>NIM / NISN</dt>
                                    <dd>{{ $member['nim_nisn'] }}</dd>
                                </div>
                                <div class="antrean-biodata-item">
                                    <dt>Jenjang Pendidikan</dt>
                                    <dd>{{ $member['jenjang_label'] }}</dd>
                                </div>
                                <div class="antrean-biodata-item">
                                    <dt>Jurusan / Program Studi</dt>
                                    <dd>{{ $member['jurusan_nama'] }}</dd>
                                </div>
                                <div class="antrean-biodata-item">
                                    <dt>No. HP / WhatsApp</dt>
                                    <dd>{{ $member['no_hp'] }}</dd>
                                </div>
                                <div class="antrean-biodata-item">
                                    <dt>Pembimbing Sekolah / Kampus</dt>
                                    <dd>{{ $member['nama_pembimbing_sekolah'] }}</dd>
                                </div>
                                <div class="antrean-biodata-item antrean-biodata-item-full">
                                    <dt>Alamat Domisili</dt>
                                    <dd>{{ $member['alamat'] }}</dd>
                                </div>
                                <div class="antrean-biodata-item antrean-biodata-item-full">
                                    <dt>Kontak Darurat</dt>
                                    <dd>{{ $member['kontak_darurat'] }}</dd>
                                </div>
                            </dl>
                        </div>
                    @endforeach
                </x-gentelella.x-panel>

                <x-gentelella.x-panel title="Daftar Anggota Tim: {{ $pendaftaran->kelompokMagang?->nama_kelompok ?? 'Tim Magang' }}">
                    <div class="adm-table-container">
                        <table class="adm-table admin-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Peserta</th>
                                    <th>Peran</th>
                                    <th>NIM / NISN</th>
                                    <th>Kelengkapan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($memberProfiles as $idx => $member)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td>
                                            <div class="antrean-table-member-cell">
                                                <div class="antrean-table-avatar" aria-hidden="true">
                                                    @if(!empty($member['foto_url']))
                                                        <img src="{{ $member['foto_url'] }}" alt="{{ $member['nama'] }}">
                                                    @else
                                                        {{ strtoupper(substr($member['nama'], 0, 1)) }}
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="antrean-table-member-name">{{ $member['nama'] }}</div>
                                                    <div class="antrean-table-member-sub">{{ $member['email'] }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="antrean-badge {{ $member['is_ketua'] ? 'antrean-badge-ketua' : 'antrean-badge-anggota' }}">
                                                {{ $member['peran'] }}
                                            </span>
                                        </td>
                                        <td>{{ $member['nim_nisn'] }}</td>
                                        <td>
                                            <span class="antrean-badge {{ $member['is_profil_lengkap'] ? 'antrean-badge-success' : 'antrean-badge-warning' }}">
                                                {{ $member['kelengkapan_persen'] }}%
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button"
                                                    class="antrean-btn-view-member"
                                                    data-action="select-member-tab"
                                                    data-member-key="{{ $member['key'] }}"
                                                    data-scroll-target="panel-biodata-tim">
                                                Lihat Profil
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-gentelella.x-panel>
            @else
                <x-gentelella.x-panel title="Biodata &amp; Foto Peserta">
                    <div class="antrean-profile-header">
                        <div class="antrean-profile-photo-wrap">
                            @if($fotoUrl)
                                <img src="{{ $fotoUrl }}" alt="Foto {{ $cleanNama }}" class="antrean-profile-photo">
                            @else
                                <div class="antrean-profile-photo-fallback" aria-hidden="true">
                                    {{ strtoupper(substr($cleanNama, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="antrean-profile-summary">
                            <h2 class="antrean-profile-name">{{ $cleanNama }}</h2>
                            <p class="antrean-profile-instansi">{{ $profil?->institusi ?? 'Instansi belum diisi' }}</p>
                            <div class="antrean-profile-badges">
                                <span class="antrean-badge antrean-badge-success">Diterima (Lolos Berkas)</span>
                                <span class="antrean-badge antrean-badge-info">{{ $isTim ? 'Tim' : 'Individu' }}</span>
                            </div>
                        </div>
                    </div>

                    <dl class="antrean-biodata-grid">
                        <div class="antrean-biodata-item">
                            <dt>Email Akun</dt>
                            <dd>{{ $user?->email ?? '-' }}</dd>
                        </div>
                        <div class="antrean-biodata-item">
                            <dt>NIM / NISN</dt>
                            <dd>{{ $profil?->nim_nisn ?? '-' }}</dd>
                        </div>
                        <div class="antrean-biodata-item">
                            <dt>Jenjang Pendidikan</dt>
                            <dd>{{ $jenjangLabel }}</dd>
                        </div>
                        <div class="antrean-biodata-item">
                            <dt>Jurusan / Program Studi</dt>
                            <dd>{{ $pendaftaran->kuota?->jurusan?->nama_jurusan ?? $profil?->jurusan?->nama_jurusan ?? '-' }}</dd>
                        </div>
                        <div class="antrean-biodata-item">
                            <dt>No. HP / WhatsApp</dt>
                            <dd>{{ $profil?->no_hp ?? $user?->no_telp ?? '-' }}</dd>
                        </div>
                        <div class="antrean-biodata-item">
                            <dt>Pembimbing Sekolah / Kampus</dt>
                            <dd>{{ $profil?->nama_pembimbing_sekolah ?? '-' }}</dd>
                        </div>
                        <div class="antrean-biodata-item antrean-biodata-item-full">
                            <dt>Alamat Domisili</dt>
                            <dd>{{ $profil?->alamat ?? '-' }}</dd>
                        </div>
                        <div class="antrean-biodata-item antrean-biodata-item-full">
                            <dt>Kontak Darurat</dt>
                            <dd>
                                @if($profil?->nama_kontak_darurat)
                                    {{ $profil->nama_kontak_darurat }}
                                    ({{ $profil->hubungan_kontak_darurat ?? 'Wali' }})
                                    &mdash; {{ $profil->no_kontak_darurat ?? '-' }}
                                @else
                                    -
                                @endif
                            </dd>
                        </div>
                    </dl>
                </x-gentelella.x-panel>
            @endif
        </div>

        {{-- KOLOM KANAN: DETAIL PENERIMAAN & FORM PENETAPAN PEMBIMBING --}}
        <div class="antrean-detail-col">
            <x-gentelella.x-panel title="Ringkasan Pendaftaran Diterima">
                <dl class="antrean-summary-list">
                    <div class="antrean-summary-row">
                        <dt>Nomor Registrasi</dt>
                        <dd>{{ $nomorReg }}</dd>
                    </div>
                    <div class="antrean-summary-row">
                        <dt>Bulan Mulai Magang</dt>
                        <dd>{{ $pendaftaran->bulanMagang?->bulan_mulai?->translatedFormat('F Y') ?? '-' }}</dd>
                    </div>
                    <div class="antrean-summary-row">
                        <dt>Durasi Magang</dt>
                        <dd>{{ $pendaftaran->durasi_bulan ?? '-' }} Bulan</dd>
                    </div>
                    <div class="antrean-summary-row">
                        <dt>Tanggal Diajukan</dt>
                        <dd>{{ $pendaftaran->diajukan_at?->translatedFormat('d M Y H:i') ?? '-' }}</dd>
                    </div>
                    <div class="antrean-summary-row">
                        <dt>Diterima Pada</dt>
                        <dd>{{ $pendaftaran->diputuskan_at?->translatedFormat('d M Y H:i') ?? '-' }}</dd>
                    </div>
                    <div class="antrean-summary-row">
                        <dt>Bidang Rekomendasi</dt>
                        <dd>{{ $firstBidang?->nama_bidang ?? 'Dinas Komunikasi dan Informatika' }}</dd>
                    </div>
                </dl>
            </x-gentelella.x-panel>

            <x-gentelella.x-panel title="Penetapan Pembimbing Lapangan">
                @if(auth()->user()?->isSuperAdmin())
                    <div class="antrean-readonly-box">
                        <strong>Mode Read-Only:</strong> SuperAdmin dapat meninjau biodata dan status antrean peserta, namun tidak memiliki akses untuk menetapkan penempatan pembimbing.
                    </div>
                @else
                    <form method="POST" action="{{ route('admin.penempatan.tetapkan') }}" class="antrean-assign-form">
                        @csrf
                        <input type="hidden" name="pendaftaran_id" value="{{ $pendaftaran->id }}">
                        @if($firstBidang)
                            <input type="hidden" name="bidang_id" value="{{ $firstBidang->id }}">
                        @endif

                        <div class="antrean-form-group">
                            <span class="antrean-form-label">Bidang Tujuan Peserta</span>
                            <div class="antrean-bidang-readonly">
                                {{ $firstBidang?->nama_bidang ?? 'Dinas Komunikasi dan Informatika' }}
                            </div>
                        </div>

                        <div class="antrean-form-group">
                            <label for="antrean-pembimbing-id">Pembimbing Lapangan <span class="antrean-req">*</span></label>
                            <select name="pembimbing_id" id="antrean-pembimbing-id" required class="antrean-form-select">
                                <option value="">-- Pilih Pembimbing Lapangan --</option>
                                @foreach($pembimbingList as $pem)
                                    <option value="{{ $pem->id }}"
                                            {{ (string) old('pembimbing_id') === (string) $pem->id ? 'selected' : '' }}>
                                        {{ $pem->nama }} [{{ $pem->bidang?->nama_bidang ?? 'Dinas Kominfo' }}] ({{ $pem->mentee_aktif_count ?? 0 }} Mentee Aktif)
                                    </option>
                                @endforeach
                            </select>
                            <small class="antrean-form-hint">
                                Menampilkan pembimbing yang tersedia sesuai bidang pilihan peserta.
                            </small>
                        </div>

                        <div class="antrean-form-actions">
                            <button type="submit" class="antrean-btn-submit">
                                Tetapkan Pembimbing
                            </button>
                        </div>
                    </form>
                @endif
            </x-gentelella.x-panel>
        </div>

    </div>
</div>
@endsection
