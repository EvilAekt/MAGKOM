{{-- =========================================================================
     PARTIAL DETAIL PENEMPATAN KHUSUS PESERTA ANGGOTA KELOMPOK
     (Single-Participant Focus with Team Context - Tanpa Tabel Anggota)
     ========================================================================= --}}
@php
    $peserta = $penempatan->peserta;
    $profil = $peserta?->profil;
    $kelompok = $penempatan->pendaftaran?->kelompokMagang;
    $isKetua = $kelompok && (int) $kelompok->ketua_user_id === (int) $peserta?->id;
    $hasSurat = ! empty($penempatan->file_surat_selesai) && \Illuminate\Support\Facades\Storage::disk('local')->exists($penempatan->file_surat_selesai);
    $avatarUrl = $profil?->foto_url ?? ($profil?->foto_profil && \Illuminate\Support\Facades\Storage::disk('public')->exists($profil->foto_profil) ? asset('storage/'.$profil->foto_profil) : null);
    $statusVal = $penempatan->status instanceof \App\Enums\StatusPenempatanEnum ? $penempatan->status->value : (string) $penempatan->status;
@endphp

<div class="focused-workspace-wrapper" style="margin-top: 0.5rem;">

    {{-- 1. Baris Identitas Peserta dengan Konteks Tim --}}
    <div class="focused-identity-bar">
        <div class="focused-identity-left">
            <div class="mentee-avatar-circle" aria-hidden="true" style="width: 48px; height: 48px; border-radius: 50%; background: #EFF6FF; color: #1D4ED8; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.05rem; border: 2px solid #BFDBFE; flex-shrink: 0; overflow: hidden;">
                @if($avatarUrl)
                    <img src="{{ $avatarUrl }}" alt="{{ $peserta?->nama }}" style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    <span>{{ strtoupper(substr($peserta?->nama ?? 'P', 0, 2)) }}</span>
                @endif
            </div>
            <div class="focused-identity-text">
                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                    <h2 class="focused-identity-name" style="margin: 0;">{{ $peserta?->nama ?? '-' }}</h2>
                    <span class="kelompok-badge-pill">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        <span>{{ $kelompok?->nama_kelompok ?? __('Kelompok Magang') }}</span>
                    </span>
                    <span class="role-badge-pill {{ $isKetua ? 'ketua' : 'anggota' }}">
                        {{ $isKetua ? __('Ketua Kelompok') : __('Anggota Tim') }}
                    </span>
                </div>
                <p class="focused-identity-sub" style="margin-top: 4px;">
                    {{ $profil?->institusi ?? ($kelompok?->institusi ?? '-') }} &bull; NIM/NISN: <strong>{{ $profil?->nim_nisn ?? '-' }}</strong> &bull; {{ $profil?->jurusan?->nama_jurusan ?? '-' }}
                </p>
            </div>
        </div>

        <div class="focused-identity-right">
            @if($hasSurat)
                <span class="status-badge-chip success">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    <span>{{ __('SURAT TERBIT') }}</span>
                </span>
            @else
                <span class="status-badge-chip warning">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <span>{{ __('BELUM DIUNGGAH') }}</span>
                </span>
            @endif
        </div>
    </div>

    {{-- 2. Grid Konten Terfokus: Info Penempatan Peserta & Formulir Unggah --}}
    <div class="focused-content-grid">

        {{-- Kolom Kiri: Informasi Penempatan Peserta --}}
        <div class="focused-card info-card">
            <h3 class="focused-card-title">{{ __('Informasi Penempatan Peserta') }}</h3>
            <div class="info-list">
                <div class="info-item">
                    <span class="info-label">{{ __('Nama Peserta') }}</span>
                    <span class="info-value">{{ $peserta?->nama ?? '-' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">{{ __('Rombongan Tim') }}</span>
                    <span class="info-value">
                        {{ $kelompok?->nama_kelompok ?? '-' }}
                        <span style="color: #64748B; font-weight: normal;">({{ $isKetua ? __('Ketua Kelompok') : __('Anggota') }})</span>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">{{ __('Bidang Penempatan') }}</span>
                    <span class="info-value">{{ $penempatan->bidang?->nama_bidang ?? '-' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">{{ __('Pembimbing Lapangan') }}</span>
                    <span class="info-value">{{ $penempatan->pembimbing?->nama ?? '-' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">{{ __('Periode Magang') }}</span>
                    <span class="info-value">{{ $penempatan->tgl_mulai ? \Carbon\Carbon::parse($penempatan->tgl_mulai)->translatedFormat('d M Y') : '-' }} s/d {{ $penempatan->tgl_selesai ? \Carbon\Carbon::parse($penempatan->tgl_selesai)->translatedFormat('d M Y') : '-' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">{{ __('Tanggal Selesai') }}</span>
                    <span class="info-value">{{ $penempatan->tgl_selesai ? \Carbon\Carbon::parse($penempatan->tgl_selesai)->translatedFormat('d F Y') : '-' }}</span>
                </div>
            </div>

            {{-- Status Terbit & Unduh Surat Selesai --}}
            <div class="published-status-box {{ $hasSurat ? 'is-published' : 'is-pending' }}">
                <div class="published-box-header">
                    <h4 class="published-box-title">{{ __('Status Surat Selesai') }}</h4>
                    <span class="published-status-text">
                        {{ $hasSurat ? __('Tersedia di Dashboard Peserta & Tim') : __('Menunggu Pengunggahan Berkas') }}
                    </span>
                </div>
                @if($hasSurat)
                    <div class="published-meta">
                        <span>{{ __('Diunggah pada:') }} {{ $penempatan->tgl_upload_surat_selesai?->translatedFormat('d F Y, H:i') ?? '-' }} WIB</span>
                        <a href="{{ route('admin.penempatan.surat-selesai.unduh', $penempatan) }}" class="btn-download-primary">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                            <span>{{ __('Unduh Salinan Surat Selesai (PDF)') }}</span>
                        </a>
                    </div>
                @else
                    <p class="published-note">
                        {{ __('Surat keterangan selesai magang belum diunggah. Berkas yang diunggah di formulir di samping akan otomatis tersinkronisasi ke dashboard peserta ini dan rombongan timnya.') }}
                    </p>
                @endif
            </div>
        </div>

        {{-- Kolom Kanan: Card Formulir Unggah Surat Selesai (Kompak & To-The-Point) --}}
        <div class="focused-card upload-card">
            <div class="upload-card-header">
                <h3 class="focused-card-title">
                    {{ $hasSurat ? __('Perbarui / Ganti Berkas Surat') : __('Formulir Unggah Surat Selesai') }}
                </h3>
                <p class="upload-card-desc">
                    {{ __('Unggah berkas resmi format PDF (Maks. 2.0 MB).') }}
                </p>
            </div>

            @if($statusVal === 'selesai' && !auth()->user()?->isSuperAdmin())
                <form method="POST" action="{{ route('admin.penempatan.surat-selesai.upload', $penempatan) }}" enctype="multipart/form-data" class="dedicated-upload-form">
                    @csrf
                    
                    {{-- Area Dropzone: Seluruh Card Merupakan Label yang Dapat Diklik Langsung --}}
                    <label for="file_surat_kel_show" class="upload-dropzone-panel" tabindex="0" role="button" aria-label="{{ __('Pilih atau seret berkas PDF surat selesai magang') }}">
                        <div class="dropzone-icon-circle" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                        </div>
                        <h4 class="dropzone-prompt-title">{{ __('Pilih Berkas PDF Surat Selesai') }}</h4>
                        <p class="dropzone-prompt-desc">{{ __('Klik di mana saja atau seret berkas .PDF (Maks. 2.0 MB)') }}</p>

                        <input type="file" name="file_surat" id="file_surat_kel_show" accept="application/pdf" required class="visually-hidden-file-input">

                        {{-- Chip nama file dinamis: hanya muncul saat file dipilih --}}
                        <div class="dynamic-file-chip" id="previewFileNameKelShow"></div>
                    </label>

                    <div class="upload-footer-wrap">
                        <button type="submit" class="btn-compact-submit" style="width: 100%; justify-content: center; padding: 10px 20px; font-size: 0.85rem;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                            <span>{{ $hasSurat ? __('Simpan Perubahan Berkas') : __('Unggah Surat Resmi') }}</span>
                        </button>
                    </div>
                </form>
            @else
                <div class="notice-non-selesai-box">
                    <p class="notice-non-selesai-text">
                        <em>{{ __('Surat selesai magang dapat diunggah setelah status peserta dinyatakan selesai magang.') }}</em>
                    </p>
                </div>
            @endif
        </div>

    </div>

</div>
