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
        $hasSubmitted = $tugas->progresLogs->isNotEmpty();
        $latestReviewStatus = $latestProgres?->status_review?->value ?? (string)$latestProgres?->status_review;
        $bisaKumpulLagi = !$isSelesai && (!$hasSubmitted || $latestReviewStatus === 'perlu_revisi');
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
                        @elseif($hasSubmitted && $latestReviewStatus === 'perlu_revisi')
                            <span class="classroom-work-status" style="background: #FEF3C7; color: #B45309; border: 1px solid #FCD34D;">Perlu Revisi</span>
                        @elseif($hasSubmitted)
                            <span class="classroom-work-status status-turned-in">Diserahkan</span>
                        @elseif($isTerlambat)
                            <span class="classroom-work-status status-missing">Terlambat</span>
                        @else
                            <span class="classroom-work-status status-assigned">Ditugaskan</span>
                        @endif
                    </div>
                </div>

                {{-- Form Penyerahan Progres Tugas --}}
                <form action="{{ route('peserta.tugas.progres', $tugas) }}" method="POST" enctype="multipart/form-data" id="formProgresTugas"
                      data-unsubmitted-file="{{ session('unsubmitted_file') }}"
                      data-unsubmitted-file-name="{{ session('unsubmitted_file_name') }}"
                      data-unsubmitted-link="{{ session('unsubmitted_link') }}">
                    @csrf
                    <input type="file" id="formFileInput" name="file" accept=".pdf,.zip,.rar,.doc,.docx,image/*" style="display: none;">
                    <input type="hidden" id="formLinkInput" name="link" value="{{ old('link', session('unsubmitted_link')) }}">
                    <input type="hidden" id="formRecentPathInput" name="recent_file_path" value="{{ old('recent_file_path', session('unsubmitted_file')) }}">
                    <input type="hidden" id="formCatatanInput" name="catatan" value="{{ old('catatan', 'Mengumpulkan berkas pekerjaan tugas.') }}">

                    {{-- Lampiran Hasil Kerja yang Sudah Pernah Diserahkan (Jika Ada) --}}
                    @if($latestProgres)
                        <div class="submitted-items-container">
                            @if($latestProgres->file)
                                @php
                                    $ext = strtolower(pathinfo($latestProgres->file, PATHINFO_EXTENSION));
                                    $extLabel = strtoupper($ext) ?: 'PDF';
                                    $fileName = basename($latestProgres->file);
                                @endphp
                                <div class="submitted-preview-card">
                                    <div class="submitted-preview-info">
                                        <a href="{{ Storage::disk('private')->url($latestProgres->file) }}" target="_blank" rel="noopener noreferrer" class="submitted-preview-name" title="{{ $fileName }}">
                                            {{ $fileName }}
                                        </a>
                                        <span class="submitted-preview-type">{{ $extLabel }}</span>
                                    </div>
                                    <div class="submitted-preview-thumb">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                            <polyline points="14 2 14 8 20 8"></polyline>
                                        </svg>
                                    </div>
                                </div>
                            @endif

                            @if($latestProgres->link)
                                <div class="submitted-preview-card">
                                    <div class="submitted-preview-info">
                                        <a href="{{ $latestProgres->link }}" target="_blank" rel="noopener noreferrer" class="submitted-preview-name" title="{{ $latestProgres->link }}">
                                            <span>Buka Tautan Tugas ↗</span>
                                        </a>
                                        <span class="submitted-preview-type" title="{{ $latestProgres->link }}">{{ $latestProgres->link }}</span>
                                    </div>
                                    <div class="submitted-preview-thumb">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                                            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                                        </svg>
                                    </div>
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

                    @if($bisaKumpulLagi)
                        @if($latestReviewStatus === 'perlu_revisi')
                            <div style="background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 8px; padding: 12px; margin-bottom: 14px; font-size: 0.8125rem; color: #92400E;">
                                <div style="font-weight: 700; margin-bottom: 2px;">⚠️ Catatan Revisi Pembimbing:</div>
                                <div>{{ $latestProgres->feedback ?: $latestProgres->catatan_revisi ?: 'Silakan perbaiki progres tugas sesuai arahan pembimbing lalu serahkan kembali.' }}</div>
                            </div>
                        @endif

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
                            <span>{{ $latestReviewStatus === 'perlu_revisi' ? 'Serahkan Ulang Revisi' : 'Serahkan / Kirim Progres' }}</span>
                        </button>

                        <div class="work-caption-note">
                            Pekerjaan tidak dapat diserahkan setelah tenggat
                        </div>
                    @elseif($isSelesai)
                        <div style="background: #ECFDF5; border: 1px solid #A7F3D0; border-radius: 8px; padding: 14px; text-align: center; color: #065F46; font-size: 0.8125rem;">
                            ✓ Tugas ini telah ditinjau dan dinyatakan selesai oleh pembimbing.
                        </div>
                    @else
                        {{-- Kasus: sudah diserahkan dan sedang menunggu review --}}
                        <div class="submitted-waiting-box">
                            <div class="submitted-waiting-title">✓ Tugas Telah Diserahkan</div>
                            <div class="submitted-waiting-subtitle">
                                Diserahkan pada {{ \Carbon\Carbon::parse($latestProgres->created_at ?? $latestProgres->tgl_lapor)->translatedFormat('d M Y, H.i') }} WIB.
                            </div>
                        </div>

                        <button type="button" class="btn-batalkan-pengiriman" id="btnBukaModalBatalKirim">
                            Batalkan pengiriman
                        </button>

                        <div class="work-caption-note">
                            Batalkan pengiriman untuk menambahkan atau mengubah lampiran
                        </div>
                    @endif
                </form>
            </div>
        </div>

    </div>

</div>

{{-- =================================================================
     MODAL 1: TAMBAH LINK (PARITY STANDAR PEMBIMBING)
     ================================================================= --}}
<div class="tugas-modal-backdrop classroom-modal-backdrop" id="modalTambahLink" role="dialog" aria-modal="true" aria-labelledby="modalLinkTitle">
    <div class="tugas-dialog tugas-dialog--link">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
            <div style="font-size: 16px; font-weight: 700; color: #111827;" id="modalLinkTitle">Tambahkan link</div>
            <button type="button" class="classroom-modal-close" data-action="close-modal" aria-label="Tutup modal">✕</button>
        </div>

        <div style="margin-top: 10px;">
            <div class="tugas-field-filled" id="wrapFieldModalLink">
                <label for="inputUrlLink" class="tugas-field-filled-label">Link*</label>
                <input type="url" id="inputUrlLink" placeholder="https://drive.google.com/..." class="tugas-input-clean" style="font-size: 14px;">
            </div>
            <div id="linkErrorHint" style="margin: 6px 0 0 4px; font-size: 12px; color: #b3261e; display: none;">*Wajib diisi dengan URL valid (https://...)</div>
        </div>

        <div style="margin-top: 24px; display: flex; justify-content: flex-end; gap: 10px;">
            <button type="button" class="tugas-dialog-btn-text" data-action="close-modal">Batal</button>
            <button type="button" id="btnSubmitLink" class="tugas-dialog-btn-pill" style="width: auto; padding: 0 18px;" disabled>Tambahkan link</button>
        </div>
    </div>
</div>

{{-- =================================================================
     MODAL 2: UPLOAD BERKAS & RIWAYAT (PARITY STANDAR PEMBIMBING)
     ================================================================= --}}
<div class="tugas-modal-backdrop classroom-modal-backdrop" id="modalSisipkanBerkas" role="dialog" aria-modal="true" aria-labelledby="modalFileTitle">
    <div class="tugas-dialog tugas-dialog--upload">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <div style="font-size: 16px; font-weight: 700; color: #111827;" id="modalFileTitle">Upload file</div>
            <button type="button" class="classroom-modal-close" data-action="close-modal" aria-label="Tutup modal">✕</button>
        </div>

        {{-- 2 Tab Bar: Upload & Riwayat Berkas --}}
        <div class="classroom-modal-tabs">
            <button type="button" class="classroom-modal-tab-btn active" id="tabBtnUpload">Upload</button>
            <button type="button" class="classroom-modal-tab-btn" id="tabBtnRecent">Riwayat Berkas</button>
        </div>

        {{-- TAB 1: UPLOAD (DROPZONE & BROWSE LOKAL) --}}
        <div class="classroom-tab-panel active" id="tabPanelUpload">
            <input type="file" id="modalFileInput" accept=".pdf,.zip,.rar,.doc,.docx,image/*" style="display: none;">
            <div class="tugas-dropzone" id="fileDropzoneBox" tabindex="0" role="button" aria-label="Area drag and drop upload berkas">
                <svg width="84" height="62" viewBox="0 0 150 110" aria-hidden="true" class="tugas-dropzone-cloud">
                    <defs>
                        <linearGradient id="cloudGrad" x1="0" y1="0" x2="1" y2="1">
                            <stop offset="0" stop-color="#4cc760"></stop>
                            <stop offset="1" stop-color="#b8e3fb"></stop>
                        </linearGradient>
                    </defs>
                    <path d="M38 100a30 30 0 0 1-2-60 38 38 0 0 1 72-8 28 28 0 0 1 8 56z" fill="url(#cloudGrad)" stroke="#222" stroke-width="1.5"></path>
                    <path d="M75 82V52M62 64l13-13 13 13" fill="none" stroke="#222" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
                <button type="button" class="tugas-dropzone-btn-choose" id="btnBrowseFile">Pilih Berkas</button>
                <div class="tugas-dropzone-hint">atau seret file ke sini untuk mengunggah</div>
                <div class="tugas-dropzone-format">Maksimal 5MB (PDF, DOCX, ZIP, RAR, Gambar)</div>
            </div>

            {{-- Selected File Info Preview inside Modal --}}
            <div id="modalSelectedFileInfo" style="display: none; margin-top: 14px; padding: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 10px; min-width: 0;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#01347C" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                    <div style="min-width: 0;">
                        <div id="modalFileNameTxt" style="font-size: 13px; font-weight: 600; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"></div>
                        <div id="modalFileSizeTxt" style="font-size: 12px; color: #64748b;"></div>
                    </div>
                </div>
                <button type="button" id="btnRemoveModalFile" style="background: none; border: none; cursor: pointer; color: #ef4444;" title="Hapus berkas">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                </button>
            </div>

            <div style="margin-top: 20px; display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="tugas-dialog-btn-text" data-action="close-modal">Batal</button>
                <button type="button" class="tugas-dialog-btn-pill" id="btnSubmitUploadFile" disabled>Upload</button>
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
                                        <svg width="18" height="18" fill="none" stroke="#01347C" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
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
            <div style="margin-top: 20px; display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="tugas-dialog-btn-text" data-action="close-modal">Batal</button>
                <button type="button" id="btnInsertRecent" class="tugas-dialog-btn-pill" style="width: auto; padding: 0 18px;" disabled>Sisipkan</button>
            </div>
        </div>
    </div>
</div>

{{-- =================================================================
     MODAL 3: KONFIRMASI BATALKAN PENGIRIMAN (GOOGLE CLASSROOM STYLE)
     ================================================================= --}}
<div class="tugas-modal-backdrop classroom-modal-backdrop" id="modalKonfirmasiBatalKirim" role="dialog" aria-modal="true" aria-labelledby="modalBatalKirimTitle">
    <div class="tugas-dialog tugas-dialog--batal-kirim">
        <div style="font-size: 16px; font-weight: 700; color: #111827; margin-bottom: 8px;" id="modalBatalKirimTitle">
            Batalkan pengiriman?
        </div>
        <p style="font-size: 13px; color: #475569; line-height: 1.5; margin: 0 0 20px 0;">
            Batalkan pengiriman untuk menambahkan atau mengubah lampiran. Jangan lupa menyerahkannya kembali setelah Anda selesai.
        </p>
        <form action="{{ route('peserta.tugas.batal-kirim', $tugas) }}" method="POST" id="formBatalKirimTugas">
            @csrf
            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="tugas-dialog-btn-text" data-action="close-modal">Batal</button>
                <button type="submit" id="btnConfirmBatalKirim" class="tugas-dialog-btn-pill btn-confirm-unsubmit">
                    Batalkan pengiriman
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    @vite(['resources/js/pages/peserta/tugas-detail.js'])
@endpush
@endsection
