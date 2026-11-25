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

    {{-- Grid Konten Terfokus: Kolom Kiri (Foto & Nama + Info Penempatan) & Kolom Kanan (Biodata + Formulir Unggah) --}}
    <div class="focused-content-grid focused-content-grid--balanced">

        {{-- Kolom Kiri: Card Identitas Singkat + Informasi Penempatan Peserta --}}
        <div class="focused-left-column">
            <div class="focused-card focused-profile-card">
                <div class="mentee-avatar-circle" aria-hidden="true">
                    @if($avatarUrl)
                        <img src="{{ $avatarUrl }}" alt="{{ $peserta?->nama }}" class="avatar-img">
                    @else
                        <span>{{ strtoupper(substr($peserta?->nama ?? 'P', 0, 2)) }}</span>
                    @endif
                </div>
                <div class="focused-identity-text">
                    <h2 class="focused-identity-name">{{ $peserta?->nama ?? '-' }}</h2>
                </div>
            </div>

            <div class="focused-card info-card">
            <h3 class="focused-card-title">{{ __('Informasi Penempatan Peserta') }}</h3>
            <div class="info-list">
                <div class="info-item">
                    <span class="info-label">{{ __('Nama Peserta') }}</span>
                    <span class="info-value">{{ $peserta?->nama ?? '-' }}</span>
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
            </div>
        </div>

        {{-- Kolom Kanan: Card Biodata Peserta + Card Formulir Unggah Surat Selesai --}}
        <div class="focused-right-column">
            <div class="focused-card biodata-card">
                <div class="biodata-card-header">
                    <h3 class="focused-card-title">{{ __('Biodata Peserta') }}</h3>
                    <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                        <span class="kelompok-badge-pill">{{ $kelompok?->nama_kelompok ?? __('Kelompok Magang') }}</span>
                        <span class="role-badge-pill {{ $isKetua ? 'ketua' : 'anggota' }}">
                            {{ $isKetua ? __('Ketua Kelompok') : __('Anggota Tim') }}
                        </span>
                    </div>
                </div>
                <div class="biodata-grid">
                    <div class="biodata-item">
                        <span class="info-label">{{ __('Institusi / Asal') }}</span>
                        <span class="info-value">{{ $profil?->institusi ?? ($kelompok?->institusi ?? '-') }}</span>
                    </div>
                    <div class="biodata-item">
                        <span class="info-label">{{ __('NIM / NISN') }}</span>
                        <span class="info-value">{{ $profil?->nim_nisn ?? '-' }}</span>
                    </div>
                    <div class="biodata-item">
                        <span class="info-label">{{ __('Program Studi / Jurusan') }}</span>
                        <span class="info-value">{{ $profil?->jurusan?->nama_jurusan ?? '-' }}</span>
                    </div>
                    <div class="biodata-item">
                        <span class="info-label">{{ __('Jenjang') }}</span>
                        <span class="info-value">
                            {{ $profil?->jenjang instanceof \App\Enums\JenjangEnum ? $profil->jenjang->label() : ($profil?->jenjang ?? '-') }}
                        </span>
                    </div>
                    <div class="biodata-item">
                        <span class="info-label">{{ __('Email') }}</span>
                        <span class="info-value">{{ $peserta?->email ?? '-' }}</span>
                    </div>
                    <div class="biodata-item">
                        <span class="info-label">{{ __('No. HP / Telepon') }}</span>
                        <span class="info-value">{{ $profil?->no_hp ?? ($peserta?->no_telp ?? '-') }}</span>
                    </div>
                    <div class="biodata-item">
                        <span class="info-label">{{ __('Pembimbing Sekolah') }}</span>
                        <span class="info-value">{{ $profil?->nama_pembimbing_sekolah ?? '-' }}</span>
                    </div>
                    <div class="biodata-item">
                        <span class="info-label">{{ __('Alamat') }}</span>
                        <span class="info-value">{{ $profil?->alamat ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- Card Pengelolaan Berkas Surat Selesai --}}
            <div class="focused-card upload-card">
                @if($statusVal === 'selesai' && !auth()->user()?->isSuperAdmin())
                    @if($hasSurat)
                        <div class="upload-card-header">
                            <h3 class="focused-card-title">{{ __('Berkas Surat Selesai Terbit') }}</h3>
                            <p class="upload-card-desc">
                                {{ __('Surat selesai telah diterbitkan. Anda dapat mengunduh, mengganti, atau menghapus berkas jika terjadi kesalahan unggah.') }}
                            </p>
                        </div>

                        <div class="published-summary-panel">
                            <div class="published-summary-head">
                                <span class="published-summary-badge">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    <span>{{ __('Surat Resmi Telah Terbit') }}</span>
                                </span>
                                <span class="published-summary-date">
                                    {{ $penempatan->tgl_upload_surat_selesai?->translatedFormat('d M Y, H:i') ?? '-' }} WIB
                                </span>
                            </div>
                            <p class="published-summary-filename">
                                {{ basename($penempatan->file_surat_selesai) }}
                            </p>
                            <div class="published-actions-row">
                                <a href="{{ route('admin.penempatan.surat-selesai.unduh', $penempatan) }}" class="btn-download-primary">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                    <span>{{ __('Unduh PDF') }}</span>
                                </a>

                                <button type="button"
                                        class="btn-replace-surat"
                                        data-action="toggle-replace-surat"
                                        data-target="replacePanelKelShow"
                                        aria-expanded="false">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 4 23 10 17 10"></polyline><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
                                    <span>{{ __('Ganti Berkas') }}</span>
                                </button>

                                <form method="POST"
                                      action="{{ route('admin.penempatan.surat-selesai.hapus', $penempatan) }}"
                                      class="delete-surat-form"
                                      data-confirm-delete-surat="{{ __('Hapus berkas surat selesai yang sudah diunggah? Status akan kembali ke belum diunggah untuk seluruh tim.') }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete-surat">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path><path d="M10 11v6"></path><path d="M14 11v6"></path></svg>
                                        <span>{{ __('Hapus Berkas') }}</span>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="replace-upload-panel" id="replacePanelKelShow" hidden>
                            <form method="POST" action="{{ route('admin.penempatan.surat-selesai.upload', $penempatan) }}" enctype="multipart/form-data" class="dedicated-upload-form">
                                @csrf
                                <label for="file_surat_kel_show" class="upload-dropzone-panel" tabindex="0" role="button" aria-label="{{ __('Pilih atau seret berkas PDF pengganti surat selesai magang') }}">
                                    <div class="dropzone-icon-circle" aria-hidden="true">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                            <polyline points="17 8 12 3 7 8"></polyline>
                                            <line x1="12" y1="3" x2="12" y2="15"></line>
                                        </svg>
                                    </div>
                                    <h4 class="dropzone-prompt-title">{{ __('Pilih Berkas PDF Pengganti') }}</h4>
                                    <p class="dropzone-prompt-desc">{{ __('Klik di mana saja atau seret berkas .PDF baru (Maks. 2.0 MB)') }}</p>
                                    <input type="file" name="file_surat" id="file_surat_kel_show" accept="application/pdf" required class="visually-hidden-file-input">
                                    <div class="dynamic-file-chip" id="previewFileNameKelShow"></div>
                                </label>
                                <div class="upload-footer-wrap">
                                    <button type="submit" class="btn-compact-submit btn-submit-full">
                                        <span>{{ __('Simpan Perubahan Berkas') }}</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="upload-card-header">
                            <h3 class="focused-card-title">{{ __('Formulir Unggah Surat Selesai') }}</h3>
                            <p class="upload-card-desc">
                                {{ __('Unggah berkas resmi format PDF (Maks. 2.0 MB).') }}
                            </p>
                        </div>

                        <form method="POST" action="{{ route('admin.penempatan.surat-selesai.upload', $penempatan) }}" enctype="multipart/form-data" class="dedicated-upload-form">
                            @csrf
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
                                <div class="dynamic-file-chip" id="previewFileNameKelShow"></div>
                            </label>

                            <div class="upload-footer-wrap">
                                <button type="submit" class="btn-compact-submit btn-submit-full">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="17 8 12 3 7 8"></polyline>
                                        <line x1="12" y1="3" x2="12" y2="15"></line>
                                    </svg>
                                    <span>{{ __('Unggah Surat Resmi') }}</span>
                                </button>
                            </div>
                        </form>
                    @endif
                @else
                    <div class="upload-card-header">
                        <h3 class="focused-card-title">{{ __('Formulir Unggah Surat Selesai') }}</h3>
                    </div>
                    <div class="notice-non-selesai-box">
                        <p class="notice-non-selesai-text">
                            <em>{{ __('Surat selesai magang dapat diunggah setelah status peserta dinyatakan selesai magang.') }}</em>
                        </p>
                    </div>
                @endif
            </div>
        </div>

    </div>

</div>
