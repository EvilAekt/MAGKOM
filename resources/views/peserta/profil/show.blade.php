@extends('layouts.peserta')

@section('title', 'Biodata & Profil Peserta')
@section('page_title', 'Biodata & Profil Peserta')

@push('styles')
    @vite(['resources/css/pages/peserta/profil-show.css'])
@endpush

@push('scripts')
    @vite(['resources/js/pages/peserta/profil-show.js'])
@endpush

@section('content')
<div class="profil-page-wrapper">
    <div class="profil-main-card">

        {{-- Notifikasi Sukses / Gagal --}}
        @if(session('success'))
            <div class="profil-alert profil-alert-success" role="alert">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink: 0;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="profil-alert profil-alert-danger" role="alert">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink: 0;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Terdapat kesalahan pada data yang Anda masukkan. Silakan periksa kolom yang ditandai merah.</span>
            </div>
        @endif

        {{-- Form Biodata Profil Peserta --}}
        <form action="{{ route('peserta.profil.simpan') }}" method="POST" id="formBiodataPeserta" enctype="multipart/form-data">
            @csrf

            {{-- Hidden Inputs --}}
            <input type="file" id="inputFotoProfil" name="foto" accept="image/jpeg,image/png,image/jpg,image/webp" style="display: none;">
            <input type="hidden" id="inputHapusFoto" name="hapus_foto" value="0">
            <input type="hidden" id="nama" value="{{ $user->nama ?? $user->name }}">

            {{-- ========================================================
                 B. HEADER IDENTITAS PESERTA (NAVY BANNER)
                 ======================================================== --}}
            <div class="profil-identity-banner">
                <div class="profil-identity-left">
                    {{-- Avatar Lingkaran dengan Badge Kamera --}}
                    <div class="profil-avatar-box">
                        <div class="profil-avatar-img-wrap">
                            <img id="avatarImg"
                                 src="{{ $profil?->foto_url ?? '' }}"
                                 alt="Foto Profil"
                                 class="profil-avatar-img"
                                 style="display: {{ $profil?->foto_url ? 'block' : 'none' }};">
                            <span id="avatarInitial"
                                  class="profil-avatar-initial"
                                  style="display: {{ $profil?->foto_url ? 'none' : 'block' }};">
                                {{ strtoupper(substr($user->nama ?? $user->name ?? 'P', 0, 1)) }}
                            </span>
                        </div>

                        {{-- Tombol Ikon Kamera --}}
                        <button type="button" class="profil-camera-badge" id="cameraOverlayBtn" title="Ganti pasfoto profil" aria-label="Ganti pasfoto profil">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- Informasi Nama & Email --}}
                    <div class="profil-user-info">
                        <div class="profil-name-row">
                            <h2 class="profil-user-name">{{ $user->nama ?? $user->name }}</h2>
                            <span class="profil-badge-role">
                                @if($penempatan)
                                    {{ $penempatan->bidang?->nama_bidang ?? 'Peserta Magang Aktif' }}
                                @else
                                    CALON PESERTA MAGANG
                                @endif
                            </span>
                        </div>
                        <p class="profil-user-email">{{ $user->email }}</p>
                    </div>
                </div>

                {{-- Tombol Pilih Pasfoto --}}
                <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 4px;">
                    <button type="button" class="profil-btn-upload-photo" id="btnUploadPhoto">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        <span>Pilih Pasfoto</span>
                    </button>

                    @if($profil?->foto)
                        <button type="button" id="btnRemovePhoto" style="background: none; border: none; color: #FCA5A5; font-size: 0.72rem; cursor: pointer; text-decoration: underline; padding: 2px 4px;">
                            Hapus Pasfoto
                        </button>
                    @endif
                    <span id="photoInfoText" class="profil-photo-subtext"></span>
                </div>
            </div>

            @error('foto')
                <span class="profil-field-error" style="margin-top: 6px; display: block;">{{ $message }}</span>
            @enderror

            {{-- ========================================================
                 C. INDIKATOR KELENGKAPAN BIODATA
                 ======================================================== --}}
            <div class="profil-completeness-card">
                <div class="profil-completeness-header">
                    <span class="profil-completeness-title">KELENGKAPAN BIODATA</span>
                    <span class="profil-completeness-percent" id="completenessPercentText">{{ $persentaseLengkap }}%</span>
                </div>

                <div class="profil-progress-track">
                    <div class="profil-progress-fill" id="completenessProgressBar" style="width: {{ max(6, $persentaseLengkap) }}%;"></div>
                </div>

                <div class="profil-checklist-grid">
                    {{-- 1. Akun & Nama Lengkap --}}
                    <div class="profil-checklist-item" id="checkItemAkun">
                        <span class="profil-checklist-icon-wrap">
                            @if($checklist['data_diri'])
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" class="profil-checklist-icon done" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="8" cy="8" r="8" fill="#02275D"/>
                                    <path d="M4.5 8L7 10.5L11.5 5.5" stroke="white" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            @else
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" class="profil-checklist-icon pending" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="8" cy="8" r="7" stroke="#94A3B8" stroke-width="1.75" fill="none"/>
                                </svg>
                            @endif
                        </span>
                        <span>Akun & Nama Lengkap</span>
                    </div>

                    {{-- 2. Pasfoto Resmi --}}
                    <div class="profil-checklist-item" id="checkItemFoto">
                        <span class="profil-checklist-icon-wrap">
                            @if($checklist['foto'])
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" class="profil-checklist-icon done" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="8" cy="8" r="8" fill="#02275D"/>
                                    <path d="M4.5 8L7 10.5L11.5 5.5" stroke="white" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            @else
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" class="profil-checklist-icon pending" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="8" cy="8" r="7" stroke="#94A3B8" stroke-width="1.75" fill="none"/>
                                </svg>
                            @endif
                        </span>
                        <span>Pasfoto Resmi</span>
                    </div>

                    {{-- 3. Data Akademik & Kampus --}}
                    <div class="profil-checklist-item" id="checkItemAkademik">
                        <span class="profil-checklist-icon-wrap">
                            @if($checklist['akademik'])
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" class="profil-checklist-icon done" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="8" cy="8" r="8" fill="#02275D"/>
                                    <path d="M4.5 8L7 10.5L11.5 5.5" stroke="white" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            @else
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" class="profil-checklist-icon pending" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="8" cy="8" r="7" stroke="#94A3B8" stroke-width="1.75" fill="none"/>
                                </svg>
                            @endif
                        </span>
                        <span>Data Akademik & Kampus</span>
                    </div>

                    {{-- 4. Nomor WhatsApp / HRF --}}
                    <div class="profil-checklist-item" id="checkItemKontak">
                        <span class="profil-checklist-icon-wrap">
                            @if($checklist['kontak'])
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" class="profil-checklist-icon done" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="8" cy="8" r="8" fill="#02275D"/>
                                    <path d="M4.5 8L7 10.5L11.5 5.5" stroke="white" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            @else
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" class="profil-checklist-icon pending" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="8" cy="8" r="7" stroke="#94A3B8" stroke-width="1.75" fill="none"/>
                                </svg>
                            @endif
                        </span>
                        <span>Nomor WhatsApp / HRF</span>
                    </div>

                    {{-- 5. Kontak Darurat / Wali --}}
                    <div class="profil-checklist-item" id="checkItemDarurat">
                        <span class="profil-checklist-icon-wrap">
                            @if($checklist['darurat'])
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" class="profil-checklist-icon done" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="8" cy="8" r="8" fill="#02275D"/>
                                    <path d="M4.5 8L7 10.5L11.5 5.5" stroke="white" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            @else
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" class="profil-checklist-icon pending" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="8" cy="8" r="7" stroke="#94A3B8" stroke-width="1.75" fill="none"/>
                                </svg>
                            @endif
                        </span>
                        <span>Kontak Darurat / Wali</span>
                    </div>
                </div>
            </div>

            {{-- ========================================================
                 D. INFORMASI AKADEMIK & PENDIDIKAN
                 ======================================================== --}}
            <div class="profil-section-card" id="sectionAkademik">
                <div class="profil-section-header">
                    <div class="profil-section-title-wrap">
                        <div class="profil-section-icon-badge">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="profil-section-heading">Informasi Akademik & Pendidikan</h3>
                            <p class="profil-section-subheading">Data pendidikan terkait identitas dan program studi Anda.</p>
                        </div>
                    </div>
                    <div class="profil-section-actions">
                        <button type="button" class="profil-btn-edit" id="btnEditAkademik" data-section="akademik" title="Klik untuk mengedit data">
                            <span>Edit</span>
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="profil-fields-grid">
                    {{-- 1. Nama Sekolah / Perguruan Tinggi (Full Width - Terkunci Registrasi) --}}
                    <div class="profil-form-group profil-field-col-full">
                        <label for="institusi" class="profil-label">
                            Nama Sekolah / Perguruan Tinggi <span class="profil-required">*</span>
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="profil-lock-icon" title="Data registrasi terkunci" aria-label="Terkunci"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        </label>
                        <input type="text"
                               id="institusi"
                               name="institusi"
                               class="profil-input is-permanent-locked @error('institusi') is-invalid @enderror"
                               value="{{ old('institusi', $profil?->institusi) }}"
                               placeholder="SMK Negeri 2 Depok Sleman"
                               readonly
                               data-permanent-locked="true"
                               required>
                        @error('institusi')
                            <span class="profil-field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- 2. NIM / NISN (Terkunci Registrasi) --}}
                    <div class="profil-form-group">
                        <label for="nim_nisn" class="profil-label">
                            NIM / NISN <span class="profil-required">*</span>
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="profil-lock-icon" title="Data registrasi terkunci" aria-label="Terkunci"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        </label>
                        <input type="text"
                               id="nim_nisn"
                               name="nim_nisn"
                               class="profil-input is-permanent-locked @error('nim_nisn') is-invalid @enderror"
                               value="{{ old('nim_nisn', $profil?->nim_nisn) }}"
                               placeholder="2029290736"
                               readonly
                               data-permanent-locked="true"
                               required>
                        @error('nim_nisn')
                            <span class="profil-field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- 3. Jenjang Pendidikan (Terkunci Registrasi) --}}
                    <div class="profil-form-group">
                        <label for="jenjang" class="profil-label">
                            Jenjang Pendidikan <span class="profil-required">*</span>
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="profil-lock-icon" title="Data registrasi terkunci" aria-label="Terkunci"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        </label>
                        <select id="jenjang"
                                class="profil-select is-permanent-locked @error('jenjang') is-invalid @enderror"
                                disabled
                                data-permanent-locked="true">
                            <option value="">-- Pilih Jenjang --</option>
                            <option value="smk" {{ old('jenjang', $profil?->jenjang?->value ?? $profil?->jenjang) === 'smk' ? 'selected' : '' }}>SMK (Sekolah)</option>
                            <option value="d3" {{ old('jenjang', $profil?->jenjang?->value ?? $profil?->jenjang) === 'd3' ? 'selected' : '' }}>D3 (Diploma Tiga)</option>
                            <option value="s1" {{ old('jenjang', $profil?->jenjang?->value ?? $profil?->jenjang) === 's1' ? 'selected' : '' }}>S1 / D4 (Sarjana)</option>
                        </select>
                        {{-- Hidden Input Mirror untuk submit form --}}
                        <input type="hidden" name="jenjang" value="{{ old('jenjang', $profil?->jenjang?->value ?? $profil?->jenjang) }}">
                        @error('jenjang')
                            <span class="profil-field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- 4. Jurusan / Program Studi (Full Width - Terkunci Registrasi) --}}
                    <div class="profil-form-group profil-field-col-full">
                        <label for="jurusan_id" class="profil-label">
                            Jurusan / Program Studi <span class="profil-required">*</span>
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="profil-lock-icon" title="Data registrasi terkunci" aria-label="Terkunci"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        </label>
                        <select id="jurusan_id"
                                class="profil-select is-permanent-locked @error('jurusan_id') is-invalid @enderror"
                                disabled
                                data-permanent-locked="true">
                            <option value="">-- Pilih Jurusan / Program Studi --</option>
                            @foreach($jurusanList as $jurusan)
                                <option value="{{ $jurusan->id }}"
                                        data-kategori="{{ strtolower($jurusan->kategori) }}"
                                        {{ old('jurusan_id', $profil?->jurusan_id) == $jurusan->id ? 'selected' : '' }}>
                                    {{ $jurusan->nama_jurusan }} ({{ strtoupper($jurusan->kategori) }})
                                </option>
                            @endforeach
                        </select>
                        {{-- Hidden Input Mirror untuk submit form --}}
                        <input type="hidden" name="jurusan_id" value="{{ old('jurusan_id', $profil?->jurusan_id) }}">
                        @error('jurusan_id')
                            <span class="profil-field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- 5. Nama Pembimbing dari Sekolah / Kampus (Full Width) --}}
                    <div class="profil-form-group profil-field-col-full">
                        <label for="nama_pembimbing_sekolah" class="profil-label">
                            Nama Pembimbing dari Sekolah / Kampus
                        </label>
                        <input type="text"
                               id="nama_pembimbing_sekolah"
                               name="nama_pembimbing_sekolah"
                               class="profil-input @error('nama_pembimbing_sekolah') is-invalid @enderror"
                               value="{{ old('nama_pembimbing_sekolah', $profil?->nama_pembimbing_sekolah) }}"
                               placeholder="Contoh: Bpk. Budi Santoso, M.Pd / Dosen Pembimbing"
                               readonly
                               data-locked="true">
                        <span class="profil-helper-text">Nama pembimbing berasal dari dosen pembimbing akademik atau guru pembimbing.</span>
                        @error('nama_pembimbing_sekolah')
                            <span class="profil-field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Hidden PDP Compliance Input --}}
                    <input type="hidden" id="persetujuan_wali" name="persetujuan_wali" value="1">
                </div>
            </div>

            {{-- ========================================================
                 E. KONTAK UTAMA & JALUR DARURAT
                 ======================================================== --}}
            <div class="profil-section-card" id="sectionKontak">
                <div class="profil-section-header">
                    <div class="profil-section-title-wrap">
                        <div class="profil-section-icon-badge">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="profil-section-heading">Kontak Utama & Jalur Darurat</h3>
                            <p class="profil-section-subheading">Nomor kontak aktif untuk koordinasi selama proses magang.</p>
                        </div>
                    </div>
                    <button type="button" class="profil-btn-edit" id="btnEditKontak" data-section="kontak">
                        <span>Edit</span>
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                    </button>
                </div>

                <div class="profil-fields-grid">
                    {{-- 1. Nomor WhatsApp / Telepon Aktif --}}
                    <div class="profil-form-group">
                        <label for="no_hp" class="profil-label">
                            Nomor WhatsApp / Telepon Aktif <span class="profil-required">*</span>
                        </label>
                        <input type="tel"
                               id="no_hp"
                               name="no_hp"
                               class="profil-input @error('no_hp') is-invalid @enderror"
                               value="{{ old('no_hp', $profil?->no_hp ?? $user->no_telp) }}"
                               placeholder="082231567890"
                               readonly
                               data-locked="true"
                               required>
                        @error('no_hp')
                            <span class="profil-field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- 2. Email / Email Aktif --}}
                    <div class="profil-form-group">
                        <label for="email" class="profil-label">
                            Email / Email Aktif <span class="profil-required">*</span>
                        </label>
                        <input type="email"
                               id="email"
                               name="email"
                               class="profil-input @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email) }}"
                               placeholder="febrian@gmail.com"
                               readonly
                               data-locked="true"
                               required>
                        @error('email')
                            <span class="profil-field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- 3. Alamat Lengkap / Domisili (Full Width) --}}
                    <div class="profil-form-group profil-field-col-full">
                        <label for="alamat" class="profil-label">
                            Alamat Lengkap / Domisili
                        </label>
                        <textarea id="alamat"
                                  name="alamat"
                                  rows="2"
                                  class="profil-textarea @error('alamat') is-invalid @enderror"
                                  placeholder="Contoh: Jl. Magelang KM 8, RT/RW, Kabupaten Sleman"
                                  readonly
                                  data-locked="true">{{ old('alamat', $profil?->alamat) }}</textarea>
                        <span class="profil-helper-text">Alamat tempat tinggal peserta selama mengikuti program magang di instansi.</span>
                        @error('alamat')
                            <span class="profil-field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- 4. Nama Kontak Darurat --}}
                    <div class="profil-form-group">
                        <label for="nama_kontak_darurat" class="profil-label">
                            Nama Kontak Darurat
                        </label>
                        <input type="text"
                               id="nama_kontak_darurat"
                               name="nama_kontak_darurat"
                               class="profil-input @error('nama_kontak_darurat') is-invalid @enderror"
                               value="{{ old('nama_kontak_darurat', $profil?->nama_kontak_darurat) }}"
                               placeholder="Contoh: Nama Orang Tua / Wali"
                               readonly
                               data-locked="true">
                        @error('nama_kontak_darurat')
                            <span class="profil-field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- 5. Nomor Telepon Darurat --}}
                    <div class="profil-form-group">
                        <label for="no_kontak_darurat" class="profil-label">
                            Nomor Telepon Darurat
                        </label>
                        <input type="tel"
                               id="no_kontak_darurat"
                               name="no_kontak_darurat"
                               class="profil-input @error('no_kontak_darurat') is-invalid @enderror"
                               value="{{ old('no_kontak_darurat', $profil?->no_kontak_darurat) }}"
                               placeholder="Contoh: 08xxxxxxxxxx"
                               readonly
                               data-locked="true">
                        @error('no_kontak_darurat')
                            <span class="profil-field-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- ========================================================
                 DATA TIM KELOMPOK MAGANG (JIKA TERDAFTAR KELOMPOK)
                 ======================================================== --}}
            @if($kelompokMagang)
                <div class="profil-team-card">
                    <div class="profil-team-header">
                        <div>
                            <h3 class="profil-team-title">Tim Kelompok: {{ $kelompokMagang->nama_kelompok }}</h3>
                            <p style="font-size: 0.75rem; color: #64748B; margin: 2px 0 0;">
                                Anda terdaftar sebagai <strong>Ketua Tim / Penanggung Jawab</strong>.
                            </p>
                        </div>
                        <span class="profil-team-badge">
                            {{ $kelompokMagang->anggota->count() + 1 }} Peserta Terdaftar
                        </span>
                    </div>

                    <div class="profil-team-table-wrap">
                        <table class="profil-team-table">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">No</th>
                                    <th>Nama Peserta</th>
                                    <th>Peran</th>
                                    <th>NIM / NISN</th>
                                    <th>Kontak</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="background: #F8FAFC;">
                                    <td><strong>1</strong></td>
                                    <td>
                                        <div style="font-weight: 700;">{{ $user->nama ?? $user->name }}</div>
                                        <div style="font-size: 0.72rem; color: #64748B;">{{ $profil?->institusi ?? 'Institusi Terdaftar' }}</div>
                                    </td>
                                    <td>
                                        <span style="display: inline-block; font-size: 0.7rem; font-weight: 800; background: #FEF3C7; color: #92400E; padding: 2px 6px; border-radius: 4px;">
                                            ★ Ketua Tim
                                        </span>
                                    </td>
                                    <td style="font-family: monospace;">{{ $profil?->nim_nisn ?? '-' }}</td>
                                    <td>
                                        <div>{{ $profil?->no_hp ?? $user->no_telp ?? '-' }}</div>
                                        <div style="font-size: 0.72rem; color: #64748B;">{{ $user->email }}</div>
                                    </td>
                                </tr>
                                @forelse($kelompokMagang->anggota as $idx => $anggota)
                                    <tr>
                                        <td>{{ $idx + 2 }}</td>
                                        <td><div style="font-weight: 600;">{{ $anggota->nama }}</div></td>
                                        <td>
                                            <span style="font-size: 0.7rem; font-weight: 600; background: #F1F5F9; color: #475569; padding: 2px 6px; border-radius: 4px;">
                                                Anggota
                                            </span>
                                        </td>
                                        <td style="font-family: monospace;">{{ $anggota->nim_nisn ?? '-' }}</td>
                                        <td>
                                            <div>{{ $anggota->no_hp ?? '-' }}</div>
                                            <div style="font-size: 0.72rem; color: #64748B;">{{ $anggota->email ?? '-' }}</div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" style="text-align: center; color: #64748B; font-size: 0.75rem; padding: 12px;">
                                            Belum ada rekan anggota kelompok yang ditambahkan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- ========================================================
                 F. PERSETUJUAN & SIMPAN BIODATA
                 ======================================================== --}}
            <div class="profil-bottom-actions">
                <label class="profil-agreement-label" for="checkboxAgreement">
                    <input type="checkbox" id="checkboxAgreement" class="profil-checkbox">
                    <span>Data yang saya isikan sudah benar dan siap digunakan untuk administrasi magang.</span>
                </label>

                <button type="submit" class="profil-btn-submit" id="btnSubmitProfil" disabled>
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Simpan Biodata Profil</span>
                </button>
            </div>

        </form>

    </div>
</div>
@endsection
