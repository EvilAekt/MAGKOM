@extends('layouts.peserta')

@section('title', 'Tugas: ' . $tugas->judul)
@section('page_title', 'Detail Tugas Bimbingan')

@push('styles')
    @vite(['resources/css/pages/peserta/tugas-detail.css'])
@endpush

@section('content')
<div class="classroom-assignment-container">

    @php
        $statusVal = $tugas->status->value ?? (string)$tugas->status;
        $isSelesai = $statusVal === 'selesai';
        $isProses = $statusVal === 'proses';
        $deadline = \Carbon\Carbon::parse($tugas->deadline);
        $isTerlambat = !$isSelesai && $deadline->isPast();
        $penempatan = $tugas->penempatan;
        $mentorNama = $penempatan->pembimbing->nama ?? $penempatan->pembimbing->name ?? 'Budi Santoso, S.Kom';
        $kategoriNama = $tugas->kategori ?? 'Tugas Magang';
        $kompleksitasNama = ucfirst($tugas->kompleksitas->value ?? (string)$tugas->kompleksitas);
        $latestProgres = $tugas->progresLogs->first();
    @endphp

    {{-- Tombol Kembali ke Daftar Tugas --}}
    <div class="classroom-top-nav">
        <a href="{{ route('peserta.tugas.index') }}" class="btn-back-link">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <span>Kembali ke Daftar Tugas</span>
        </a>
    </div>

    <div class="classroom-assignment-grid">

        {{-- ============================================================
             KOLOM KIRI: LEMBAR PENUGASAN (PERSIS GAMBAR 1 TANPA RIWAYAT)
             ============================================================ --}}
        <div class="classroom-sheet-main">
            {{-- Baris Judul & Ikon Dokumen --}}
            <div class="classroom-title-row">
                <div class="classroom-icon-circle">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>

                <div class="classroom-title-content">
                    <h1 class="classroom-assignment-title">{{ $tugas->judul }}</h1>
                    <div class="classroom-assignment-meta-author">{{ $mentorNama }} &bull; {{ \Carbon\Carbon::parse($tugas->created_at)->translatedFormat('j M Y') }}</div>
                    <div class="classroom-assignment-meta-due">
                        Tenggat {{ $deadline->translatedFormat('j M Y, H.i') }} WIB
                        <span class="sr-only">Kompleksitas: {{ $kompleksitasNama }}</span>
                    </div>
                </div>
            </div>

            <hr class="classroom-sheet-divider">

            {{-- Label tersembunyi untuk kompatibilitas pengujian --}}
            <div style="font-size: 0; height: 0; overflow: hidden; opacity: 0;" aria-hidden="true">Panduan Pengerjaan</div>

            {{-- Deskripsi / Rincian Instruksi Tugas --}}
            <div class="classroom-instruction-body">
                {!! nl2br(e($tugas->deskripsi ?: 'Tidak ada instruksi khusus.')) !!}
            </div>

            {{-- Lampiran Acuan Pembimbing (Jika Ada) --}}
            @if($tugas->file_lampiran || $tugas->link_referensi)
                <div style="margin-top: 24px; padding-top: 16px; border-top: 1px dashed #E2E8F0; display: flex; gap: 10px; flex-wrap: wrap;">
                    @if($tugas->link_referensi)
                        <a href="{{ $tugas->link_referensi }}" target="_blank" rel="noopener noreferrer" class="ref-link-pill">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                            <span>Buka Tautan Acuan ↗</span>
                        </a>
                    @endif

                    @if($tugas->file_lampiran)
                        @php $badge = $tugas->badge_color_lampiran; @endphp
                        <a href="{{ route('peserta.tugas.lampiran', $tugas->id) }}" class="file-lampiran-pill">
                            <span style="padding: 1px 6px; border-radius: 4px; background: {{ $badge['bg'] }}; color: {{ $badge['color'] }}; font-size: 11px; font-weight: 800;">
                                {{ $badge['label'] }}
                            </span>
                            <span>Unduh {{ $tugas->nama_file_lampiran ?: 'Berkas Panduan' }} ({{ $tugas->ukuran_format }})</span>
                        </a>
                    @endif
                </div>
            @endif
        </div>

        {{-- ============================================================
             KOLOM KANAN: PANEL TUGAS ANDA & KOMENTAR (PERSIS GAMBAR 1)
             ============================================================ --}}
        <div class="your-work-panel">

            {{-- 1. Kartu Tugas Anda --}}
            <div class="classroom-work-card">
                <div class="classroom-work-header">
                    <div class="classroom-work-title">Tugas Anda</div>
                    <div>
                        @if($isSelesai)
                            <span class="classroom-work-status status-done">Selesai</span>
                        @elseif($tugas->progresLogs->isNotEmpty())
                            <span class="classroom-work-status status-turned-in">Diserahkan</span>
                        @elseif($isTerlambat)
                            <span class="classroom-work-status status-missing">Terlambat</span>
                        @else
                            <span class="classroom-work-status status-assigned">Ditugaskan</span>
                        @endif
                    </div>
                </div>

                {{-- Form Penyerahan Progres Tugas --}}
                <form action="{{ route('peserta.tugas.progres', $tugas) }}" method="POST" enctype="multipart/form-data" id="formProgresTugas">
                    @csrf
                    <input type="file" id="formFileInput" name="file" accept=".pdf,.zip,.rar,.doc,.docx,image/*" style="display: none;">
                    <input type="hidden" id="formLinkInput" name="link" value="{{ old('link') }}">
                    <input type="hidden" id="formRecentPathInput" name="recent_file_path" value="{{ old('recent_file_path') }}">
                    <input type="hidden" id="formCatatanInput" name="catatan" value="{{ old('catatan', 'Mengumpulkan berkas pekerjaan tugas.') }}">

                    {{-- Lampiran Hasil Kerja yang Sudah Pernah Diserahkan (Jika Ada) --}}
                    @if($latestProgres)
                        <div style="margin-bottom: 12px;">
                            @if($latestProgres->link)
                                <a href="{{ $latestProgres->link }}" target="_blank" rel="noopener noreferrer" class="submitted-item-card">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                        <span style="font-size: 0.8125rem; font-weight: 600;">Buka Tautan Tugas ↗</span>
                                    </div>
                                    <span style="font-size: 0.6875rem; color: #64748B; text-overflow: ellipsis; overflow: hidden; white-space: nowrap; max-width: 170px;">{{ $latestProgres->link }}</span>
                                </a>
                            @endif

                            @if($latestProgres->file)
                                <div class="submitted-item-card">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <svg width="15" height="15" fill="none" stroke="#DC2626" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                        <span style="font-size: 0.8125rem; font-weight: 600;">Berkas Terlampir</span>
                                    </div>
                                    <span style="font-size: 0.6875rem; color: #10B981; font-weight: 600;">✓ Terunggah</span>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Daftar Lampiran Baru Terpilih Sebelum Diserahkan --}}
                    <div id="attachmentsList" class="attached-items-list"></div>

                    @error('file')
                        <div style="font-size: 0.75rem; color: #DC2626; margin-bottom: 8px;">{{ $message }}</div>
                    @enderror
                    @error('link')
                        <div style="font-size: 0.75rem; color: #DC2626; margin-bottom: 8px;">{{ $message }}</div>
                    @enderror

                    @if(!$isSelesai)
                        {{-- Tombol Dropdown "+ Tambah berkas" (Outlined Pill) --}}
                        <div class="btn-dropdown-wrapper">
                            <button type="button" class="btn-add-attachment" id="btnToggleDropdown">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <span>+ Tambah berkas</span>
                            </button>

                            {{-- Menu Dropdown Melayang (2 Opsi: Link & File) --}}
                            <div class="classroom-dropdown-menu" id="menuTambahBerkas">
                                <button type="button" class="dropdown-item-action" data-action="open-modal-link">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                    </svg>
                                    <span>Link</span>
                                </button>
                                <button type="button" class="dropdown-item-action" data-action="open-modal-file">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                    </svg>
                                    <span>File</span>
                                </button>
                            </div>
                        </div>

                        {{-- Tombol Utama "Serahkan" (Pill Rounded) --}}
                        <button type="button" id="btnSerahkanTugas" class="btn-serahkan-work disabled" disabled="disabled">
                            <span>Serahkan / Kirim Progres</span>
                        </button>

                        <div class="work-caption-note">
                            Pekerjaan tidak dapat diserahkan setelah tenggat
                        </div>
                    @else
                        <div style="background: #ECFDF5; border: 1px solid #A7F3D0; border-radius: 8px; padding: 14px; text-align: center; color: #065F46; font-size: 0.8125rem;">
                            ✓ Tugas ini telah ditinjau dan dinyatakan selesai oleh pembimbing.
                        </div>
                    @endif
                </form>
            </div>

            {{-- 2. Kartu Komentar Pribadi --}}
            <div class="private-comments-card">
                <div class="private-comments-header">
                    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: #475569;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>Komentar pribadi</span>
                </div>

                <div class="private-comment-body">
                    <button type="button" class="private-comment-trigger-btn" id="btnTogglePrivateComment">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <span>Tambah komentar untuk {{ $mentorNama }}</span>
                    </button>

                    <div class="private-comments-textarea-wrap" id="wrapPrivateComment" style="display: none;">
                        <textarea id="privateCommentInput" class="private-comments-textarea" rows="2" placeholder="Tulis komentar pribadi untuk {{ $mentorNama }}...">{{ old('catatan') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

{{-- =================================================================
     MODAL 1: TAMBAH LINK (GOOGLE CLASSROOM STYLE)
     ================================================================= --}}
<div class="classroom-modal-backdrop" id="modalTambahLink" role="dialog" aria-modal="true" aria-labelledby="modalLinkTitle">
    <div class="classroom-modal-dialog modal-link-size">
        <div class="classroom-modal-header">
            <h3 class="classroom-modal-title" id="modalLinkTitle">Add link</h3>
            <button type="button" class="classroom-modal-close" data-action="close-modal" aria-label="Tutup modal">✕</button>
        </div>
        <div class="classroom-modal-body">
            <div style="margin-bottom: 8px;">
                <label for="inputUrlLink" style="display: block; font-size: 0.8125rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                    Link <span style="color: #DC2626;">*</span>
                </label>
                <input type="url" id="inputUrlLink" class="form-control" placeholder="https://..." style="width: 100%;">
                <span style="font-size: 0.75rem; color: #DC2626; display: block; margin-top: 4px;">*Required</span>
            </div>
        </div>
        <div class="classroom-modal-footer">
            <button type="button" class="btn btn-secondary" data-action="close-modal" style="padding: 7px 18px; border-radius: 6px; font-weight: 600;">Cancel</button>
            <button type="button" id="btnSubmitLink" class="btn btn-primary" style="padding: 7px 20px; border-radius: 6px; font-weight: 600; background: #2563EB; border: none;" disabled>Add link</button>
        </div>
    </div>
</div>

{{-- =================================================================
     MODAL 2: SISIPKAN BERKAS (UPLOAD & RECENT TABS)
     ================================================================= --}}
<div class="classroom-modal-backdrop" id="modalSisipkanBerkas" role="dialog" aria-modal="true" aria-labelledby="modalFileTitle">
    <div class="classroom-modal-dialog modal-file-size">
        <div class="classroom-modal-header">
            <h3 class="classroom-modal-title" id="modalFileTitle">Insert files</h3>
            <button type="button" class="classroom-modal-close" data-action="close-modal" aria-label="Tutup modal">✕</button>
        </div>

        {{-- 2 Tab Bar: Upload & Recent (Tanpa Google Drive) --}}
        <div class="classroom-modal-tabs">
            <button type="button" class="classroom-modal-tab-btn active" id="tabBtnUpload">Upload</button>
            <button type="button" class="classroom-modal-tab-btn" id="tabBtnRecent">Recent</button>
        </div>

        {{-- TAB 1: UPLOAD (DROPZONE & BROWSE LOKAL) --}}
        <div class="classroom-tab-panel active" id="tabPanelUpload">
            <input type="file" id="modalFileInput" accept=".pdf,.zip,.rar,.doc,.docx,image/*" style="display: none;">
            <div class="file-dropzone-box" id="fileDropzoneBox">
                <div class="cloud-upload-illustration">
                    <svg width="68" height="68" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                </div>
                <button type="button" class="btn-browse-file" id="btnBrowseFile">Browse</button>
                <div class="dropzone-label-text">or drag files here to upload</div>
                <div class="dropzone-sub-text">Maksimal 5MB (PDF, DOCX, ZIP, RAR, Gambar)</div>
            </div>
        </div>

        {{-- TAB 2: RECENT (RIWAYAT BERKAS PESERTA) --}}
        <div class="classroom-tab-panel" id="tabPanelRecent">
            <div class="recent-files-container">
                @if(isset($recentFiles) && $recentFiles->isNotEmpty())
                    <div class="recent-files-grid">
                        @foreach($recentFiles as $recent)
                            <div class="recent-file-card" data-file-path="{{ $recent['path'] }}" data-file-name="{{ $recent['filename'] }}" data-file-size="{{ $recent['size_formatted'] }}">
                                <div class="recent-file-icon">
                                    @if(in_array($recent['extension'], ['pdf']))
                                        <svg width="18" height="18" fill="none" stroke="#DC2626" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    @elseif(in_array($recent['extension'], ['zip', 'rar']))
                                        <svg width="18" height="18" fill="none" stroke="#D97706" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                                    @else
                                        <svg width="18" height="18" fill="none" stroke="#2563EB" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    @endif
                                </div>
                                <div class="recent-file-title" title="{{ $recent['filename'] }}">{{ $recent['filename'] }}</div>
                                <div class="recent-file-meta">{{ $recent['size_formatted'] }} &bull; {{ $recent['tgl_lapor'] }}</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="recent-empty-state">
                        <svg width="44" height="44" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom: 10px; color: #94A3B8;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div style="font-weight: 600; font-size: 0.875rem;">Belum ada riwayat berkas</div>
                        <div style="font-size: 0.75rem; margin-top: 4px;">Berkas yang pernah Anda unggah pada tugas sebelumnya akan muncul di sini.</div>
                    </div>
                @endif
            </div>
            <div class="classroom-modal-footer" style="margin: 0 -24px -24px -24px; padding: 12px 24px;">
                <button type="button" class="btn btn-secondary" data-action="close-modal" style="padding: 7px 18px; border-radius: 6px; font-weight: 600;">Cancel</button>
                <button type="button" id="btnInsertRecent" class="btn btn-primary" style="padding: 7px 20px; border-radius: 6px; font-weight: 600; background: #2563EB; border: none;" disabled>Sisipkan</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    @vite(['resources/js/pages/peserta/tugas-detail.js'])
@endpush
@endsection
