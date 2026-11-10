@extends('layouts.dashboard')

@section('title', 'Edit Tugas: ' . $tugas->judul . ' - Portal MagKom')
@section('topbar_title', 'Edit Tugas Mentee')

@push('styles')
    @vite(['resources/css/pages/pembimbing/tugas-edit.css'])
@endpush

@push('scripts')
    @vite(['resources/js/pages/pembimbing/tugas-edit.js'])
@endpush

@section('content')
<div class="tugas-workspace">

    {{-- Top Action & Header Bar (Sesuai Gaya Google Classroom Workspace) --}}
    <div class="tugas-topbar">
        <div class="tugas-topbar-left">
            <div class="tugas-icon-circle" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="5" y="4" width="14" height="17" rx="2"></rect>
                    <path d="M9 3h6v3H9zM8.5 11h7M8.5 15h7"></path>
                </svg>
            </div>
            <h1 class="tugas-page-title">Edit Tugas Mentee</h1>
            <span class="sr-only">Classroom Workspace</span>
            <nav aria-label="Breadcrumb" class="sr-only">
                <a href="{{ route('pembimbing.dashboard') }}">Dashboard</a> /
                <a href="{{ route('pembimbing.tugas.index') }}">Tugas Peserta</a> /
                <a href="{{ route('pembimbing.tugas.show', $tugas) }}">{{ Str::limit($tugas->judul, 25) }}</a> /
                <span>Edit Tugas</span>
            </nav>
        </div>

        <div class="tugas-topbar-actions">
            <button type="submit" form="formEditTugasClassroom" class="tugas-btn-primary-action" id="btnSubmitEdit">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>Simpan Perubahan</span>
            </button>

            <a href="{{ route('pembimbing.tugas.show', $tugas) }}" class="tugas-btn-cancel">
                Batal
            </a>
        </div>
    </div>

    {{-- Alert Validasi Backend --}}
    @if ($errors->any())
        <div class="tugas-alert-error" role="alert" style="background: #fef2f2; border: 1px solid #fca5a5; border-radius: 8px; padding: 12px 16px; color: #991b1b; font-size: 14px;">
            <strong style="display: block; margin-bottom: 4px;">Harap periksa kembali isian formulir:</strong>
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Main Two-Column Form --}}
    <form id="formEditTugasClassroom" action="{{ route('pembimbing.tugas.update', $tugas) }}" method="POST" enctype="multipart/form-data" autocomplete="off">
        @csrf
        @method('PUT')

        {{-- Locked Mentee ID --}}
        <input type="hidden" name="penempatan_id" value="{{ $tugas->penempatan_id }}">

        {{-- Fallback Kompleksitas agar validasi backend tetap terpenuhi tanpa membebani UI --}}
        <input type="hidden" name="kompleksitas" value="{{ old('kompleksitas', $tugas->kompleksitas?->value ?? 'sedang') }}">

        {{-- Hidden Real File Input --}}
        <input type="file" name="file_lampiran" id="realFileInput" style="display: none;" accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.ppt,.pptx,.zip,.png,.jpg,.jpeg">

        {{-- Hidden Real Link Input --}}
        <input type="hidden" name="link_referensi" id="realLinkInput" value="{{ old('link_referensi', $tugas->link_referensi) }}">

        {{-- Hidden Real Deadline Input --}}
        <input type="hidden" name="deadline" id="realDeadlineInput" value="{{ old('deadline', $tugas->deadline?->format('Y-m-d')) }}">

        <div class="tugas-body-layout">

            {{-- Kolom Kiri: Canvas Penugasan Utama --}}
            <main class="tugas-main-column">

                {{-- Panel 1: Judul dan Instruksi Pengerjaan --}}
                <section class="tugas-card">
                    @php
                        $hasInitialJudul = !empty(old('judul', $tugas->judul));
                    @endphp
                    <div class="tugas-field-filled {{ $hasInitialJudul ? 'has-value' : '' }} {{ $errors->has('judul') ? 'has-error' : '' }}">
                        <label for="inputJudul" class="tugas-field-filled-label"><span class="sr-only">Judul Penugasan</span><span aria-hidden="true">Judul*</span></label>
                        <input id="inputJudul" 
                               type="text" 
                               name="judul" 
                               required 
                               maxlength="150" 
                               autocomplete="off"
                               spellcheck="false"
                               value="{{ old('judul', $tugas->judul) }}" 
                               class="tugas-input-clean">
                    </div>

                    <div class="tugas-instruction-box {{ $errors->has('deskripsi') ? 'has-error' : '' }}">
                        <label for="editorPetunjukVisual" class="tugas-instruction-label">Petunjuk (opsional)</label>
                        <div id="editorPetunjukVisual" 
                             class="tugas-visual-editor" 
                             contenteditable="true" 
                             role="textbox" 
                             aria-multiline="true">{!! old('deskripsi', $tugas->deskripsi) !!}</div>
                        <textarea id="inputPetunjuk" 
                                  name="deskripsi" 
                                  style="display: none;">{{ old('deskripsi', $tugas->deskripsi) }}</textarea>

                        {{-- Rich Format Toolbar --}}
                        <div class="tugas-format-toolbar">
                            <button type="button" class="tugas-tool-btn" id="toolBtnBold" aria-label="Tebal" title="Tebal (Ctrl+B)">
                                <strong>B</strong>
                            </button>
                            <button type="button" class="tugas-tool-btn" id="toolBtnItalic" aria-label="Miring" title="Miring (Ctrl+I)">
                                <em>I</em>
                            </button>
                            <button type="button" class="tugas-tool-btn" id="toolBtnUnderline" aria-label="Garis bawah" title="Garis bawah (Ctrl+U)">
                                <u>U</u>
                            </button>
                            <button type="button" class="tugas-tool-btn" id="toolBtnList" aria-label="Daftar bernomor" title="Daftar Bernomor">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                                    <path d="M10 6h10M10 12h10M10 18h10M4 6h2M4 12h2M4 18h2"></path>
                                </svg>
                            </button>
                            <button type="button" class="tugas-tool-btn" id="toolBtnClear" aria-label="Hapus format" title="Hapus Format">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                                    <path d="M6 5h12M12 5l-3 14M5 19l14-14"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </section>

                {{-- Panel 2: Lampirkan (Upload File & Tambahkan Link) --}}
                <section class="tugas-card tugas-card--accent">
                    <div class="tugas-lampiran-header">
                        <h2 class="tugas-lampiran-title">Lampirkan</h2>
                        <span class="sr-only">Materi Acuan</span>
                        <span class="tugas-lampiran-meta">(opsional)</span>
                    </div>

                    {{-- Berkas yang Sedang Terpasang Saat Ini --}}
                    @if($tugas->file_lampiran)
                        <div class="tugas-existing-file-card">
                            <div class="tugas-existing-file-info">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1d4ed8" stroke-width="2" aria-hidden="true">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                </svg>
                                <div>
                                    <span class="tugas-existing-file-name">{{ $tugas->nama_file_lampiran ?: 'Berkas Lampiran Saat Ini' }}</span>
                                    @if($tugas->ukuran_file_lampiran)
                                        <span class="tugas-existing-file-size">({{ number_format($tugas->ukuran_file_lampiran / 1024, 1) }} KB)</span>
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('pembimbing.tugas.lampiran', $tugas) }}" class="tugas-btn-file-download" title="Unduh berkas lampiran">
                                Unduh Lampiran &darr;
                            </a>
                        </div>
                    @endif

                    <div class="tugas-lampiran-selector-box">
                        <div class="tugas-circle-action-item">
                            <button type="button" class="tugas-circle-btn" id="btnTriggerUploadFile" aria-label="Buka dialog upload berkas" title="Upload File Baru">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M12 16V4M7 9l5-5 5 5M5 20h14"></path>
                                </svg>
                            </button>
                            <span class="tugas-circle-label">{{ $tugas->file_lampiran ? 'Ganti File' : 'Upload File' }}</span>
                        </div>

                        <div class="tugas-circle-action-item">
                            <button type="button" class="tugas-circle-btn" id="btnTriggerAddLink" aria-label="Buka dialog tautan" title="Tambahkan Link">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M10 14a4 4 0 0 0 5.7 0l3-3a4 4 0 0 0-5.7-5.7l-1 1M14 10a4 4 0 0 0-5.7 0l-3 3A4 4 0 0 0 11 18.7l1-1"></path>
                                </svg>
                            </button>
                            <span class="tugas-circle-label">Link</span>
                        </div>
                    </div>

                    {{-- Attached Preview Cards Containers --}}
                    <div class="tugas-attached-list" id="attachedFilesContainer"></div>
                    <div class="tugas-attached-list" id="attachedLinksContainer"></div>
                </section>

            </main>

            {{-- Kolom Kanan: Parameter Penugasan --}}
            <aside class="tugas-side-column" aria-label="Parameter Penugasan">
                <span class="sr-only">Parameter Penugasan</span>

                {{-- Parameter 1: Penerima Tugas (Terkunci Sesuai Penugasan Mentee) --}}
                <div class="tugas-side-group">
                    <label class="tugas-side-label">Penerima Tugas</label>
                    @php
                        $pesertaNama = $tugas->penempatan?->peserta?->nama ?? 'Peserta Magang';
                        $initialPeserta = strtoupper(substr($pesertaNama, 0, 1));
                    @endphp
                    <div class="tugas-locked-mentee-card">
                        <div class="tugas-locked-avatar" aria-hidden="true">
                            {{ $initialPeserta }}
                        </div>
                        <div class="tugas-locked-info">
                            <span class="tugas-locked-name">{{ $pesertaNama }}</span>
                            <span class="tugas-locked-sub">
                                {{ $tugas->penempatan?->bidang?->nama_bidang ?? 'Dinas Kominfo Sleman' }} &bull; Selesai {{ $tugas->penempatan?->tgl_selesai?->translatedFormat('d M Y') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Parameter 2: Kategori Bidang (Google Classroom Custom Select) --}}
                <div class="tugas-side-group tugas-custom-select-group" id="groupKategoriSelect">
                    <label class="tugas-side-label" id="lblKategori">Kategori Bidang <span class="req">*</span></label>

                    {{-- Native select tersembunyi untuk kompatibilitas form & PHPUnit --}}
                    <select id="selectKategori" name="kategori" required style="display: none;">
                        @php
                            $defaultKat = old('kategori', $tugas->kategori ?? '');
                            $kategoriOptions = [
                                'Pengembangan Web',
                                'UI/UX Design',
                                'Jaringan & Keamanan',
                                'Analisis Data',
                                'Administrasi TI',
                                'Dokumentasi Teknis',
                                'Pengujian Sistem',
                            ];
                        @endphp
                        <option value="" disabled {{ empty($defaultKat) ? 'selected' : '' }}>Pilih kategori bidang</option>
                        @foreach ($kategoriOptions as $kat)
                            <option value="{{ $kat }}" {{ $defaultKat === $kat ? 'selected' : '' }}>
                                {{ $kat }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Custom Trigger & Listbox ala Google Classroom --}}
                    <div class="tugas-select-wrapper">
                        <button type="button" 
                                class="tugas-custom-select-trigger {{ empty($defaultKat) ? 'is-placeholder' : '' }}" 
                                id="btnTriggerKategori" 
                                aria-haspopup="listbox" 
                                aria-expanded="false">
                            <span class="trigger-text" id="btnTriggerKategoriText">{{ !empty($defaultKat) ? $defaultKat : 'Pilih kategori bidang' }}</span>
                            <svg class="trigger-chevron" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M6 9l6 6 6-6"></path>
                            </svg>
                        </button>

                        <ul class="tugas-custom-select-menu" id="menuKategoriList" role="listbox" aria-labelledby="lblKategori" tabindex="-1">
                            @foreach ($kategoriOptions as $kat)
                                @php $isSel = (!empty($defaultKat) && $defaultKat === $kat); @endphp
                                <li class="tugas-custom-select-option {{ $isSel ? 'is-selected' : '' }}" 
                                    role="option" 
                                    aria-selected="{{ $isSel ? 'true' : 'false' }}"
                                    data-value="{{ $kat }}">
                                    <span>{{ $kat }}</span>
                                    @if ($isSel)
                                        <svg class="option-check" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                {{-- Parameter 3: Tenggat Waktu (Deadline) --}}
                <div class="tugas-side-group">
                    <label class="tugas-side-label">Tenggat <span class="req">*</span></label>
                    @php
                        $initDeadline = old('deadline', $tugas->deadline?->format('Y-m-d'));
                        $deadlineCarbon = $initDeadline ? \Carbon\Carbon::parse($initDeadline) : null;
                        $deadlineIndoFormatted = $deadlineCarbon ? $deadlineCarbon->isoFormat('dddd, D MMM Y') : 'Pilih tenggat waktu';
                    @endphp
                    <button type="button" class="tugas-side-trigger" id="btnTriggerDeadline" aria-haspopup="dialog">
                        <svg class="trigger-icon-left" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                            <rect x="3.5" y="5" width="17" height="15" rx="2"></rect>
                            <path d="M3.5 10h17M8 3v4M16 3v4"></path>
                        </svg>
                        <span class="trigger-text" id="txtSelectedDeadlineDisplay">{{ $deadlineIndoFormatted }}</span>
                        <svg class="trigger-icon-chevron" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M6 9l6 6 6-6"></path>
                        </svg>
                    </button>
                    @if($tugas->penempatan?->tgl_selesai)
                        <div class="tugas-side-hint">
                            Berakhir pukul 23.59 WIB. Maks. tanggal {{ $tugas->penempatan->tgl_selesai->format('d/m/Y') }} (akhir magang peserta).
                        </div>
                    @endif
                </div>

            </aside>

        </div>

    </form>

    {{-- Modal Dialog: Upload File --}}
    <div class="tugas-modal-backdrop" id="modalUploadFileDialog" role="dialog" aria-modal="true" aria-labelledby="ttlModalUpload">
        <div class="tugas-dialog tugas-dialog--upload">
            <div style="font-size: 16px; font-weight: 600; color: #111827;" id="ttlModalUpload">Upload File Baru</div>

            <div class="tugas-dropzone" id="tugasDropzoneArea" tabindex="0" role="button" aria-label="Area drag and drop upload berkas">
                <svg width="84" height="62" viewBox="0 0 150 110" aria-hidden="true" class="tugas-dropzone-cloud">
                    <defs>
                        <linearGradient id="cloudGradEdit" x1="0" y1="0" x2="1" y2="1">
                            <stop offset="0" stop-color="#4cc760"></stop>
                            <stop offset="1" stop-color="#b8e3fb"></stop>
                        </linearGradient>
                    </defs>
                    <path d="M38 100a30 30 0 0 1-2-60 38 38 0 0 1 72-8 28 28 0 0 1 8 56z" fill="url(#cloudGradEdit)" stroke="#222" stroke-width="1.5"></path>
                    <path d="M75 82V52M62 64l13-13 13 13" fill="none" stroke="#222" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
                <button type="button" class="tugas-dropzone-btn-choose" id="btnBrowseFile">Pilih File</button>
                <div class="tugas-dropzone-hint">atau tarik file ke sini</div>
                <div class="tugas-dropzone-format">PDF, ZIP, DOCX, XLS, PPT, Gambar · maks. 10MB</div>
            </div>

            {{-- Selected File Info Preview inside Modal --}}
            <div id="modalSelectedFileInfo" style="display: none; margin-top: 14px; padding: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 10px; min-width: 0;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                    <div style="min-width: 0;">
                        <div id="modalFileNameTxt" style="font-size: 13px; font-weight: 600; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"></div>
                        <div id="modalFileSizeTxt" style="font-size: 12px; color: #64748b;"></div>
                    </div>
                </div>
                <button type="button" id="btnRemoveModalFile" style="background: none; border: none; cursor: pointer; color: #ef4444;" title="Hapus berkas">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                </button>
            </div>

            <div style="margin-top: 24px; display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="tugas-dialog-btn-text" id="btnCancelUploadFile">Batal</button>
                <button type="button" class="tugas-dialog-btn-pill" id="btnSubmitUploadFile" disabled>Upload</button>
            </div>
        </div>
    </div>

    {{-- Modal Dialog: Tambahkan Link --}}
    <div class="tugas-modal-backdrop" id="modalAddLinkDialog" role="dialog" aria-modal="true" aria-labelledby="ttlModalLink">
        <div class="tugas-dialog tugas-dialog--link">
            <div style="font-size: 16px; font-weight: 600; color: #111827;" id="ttlModalLink">Kelola Tautan Referensi</div>

            <div style="margin-top: 18px;">
                <div class="tugas-field-filled" style="padding: 8px 14px;">
                    <label for="inputModalLinkUrl" class="tugas-field-filled-label">Link*</label>
                    <input type="url" id="inputModalLinkUrl" placeholder="https://drive.google.com/..." class="tugas-input-clean" style="font-size: 15px;" value="{{ old('link_referensi', $tugas->link_referensi) }}">
                </div>
                <div id="linkErrorHint" style="margin: 6px 0 0 8px; font-size: 12px; color: #b3261e; display: none;">*Wajib diisi url valid</div>
            </div>

            <div style="margin-top: 24px; display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="tugas-dialog-btn-text" id="btnCancelAddLink">Batal</button>
                <button type="button" class="tugas-dialog-btn-pill" id="btnSubmitAddLink">Simpan Tautan</button>
            </div>
        </div>
    </div>

    {{-- Popup Calendar Tenggat (Datepicker Popup) --}}
    <div class="tugas-calendar-popup" id="popupCalendarDeadline" role="dialog" aria-modal="false" aria-label="Kalender Tenggat Waktu">
        <div class="tugas-cal-header">
            <div class="tugas-cal-nav-group">
                <button type="button" class="tugas-cal-nav-btn" id="calPrevMonth" aria-label="Bulan sebelumnya">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 18l-6-6 6-6"></path></svg>
                </button>
                <span class="tugas-cal-label" id="calHeaderMonth">Bulan</span>
                <button type="button" class="tugas-cal-nav-btn" id="calNextMonth" aria-label="Bulan berikutnya">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"></path></svg>
                </button>
            </div>
            <div class="tugas-cal-nav-group">
                <span class="tugas-cal-label" id="calHeaderYear">Tahun</span>
            </div>
        </div>

        <div class="tugas-cal-grid-days">
            <span class="tugas-cal-day-name">M</span>
            <span class="tugas-cal-day-name">S</span>
            <span class="tugas-cal-day-name">S</span>
            <span class="tugas-cal-day-name">R</span>
            <span class="tugas-cal-day-name">K</span>
            <span class="tugas-cal-day-name">J</span>
            <span class="tugas-cal-day-name">S</span>
        </div>

        <div class="tugas-cal-grid-dates" id="calDatesGrid"></div>

        <div class="tugas-cal-footer">
            <button type="button" class="tugas-dialog-btn-text" id="calBtnClear">Hapus</button>
            <div>
                <button type="button" class="tugas-dialog-btn-text" id="calBtnCancel">Batal</button>
                <button type="button" class="tugas-dialog-btn-text" id="calBtnOk" style="font-weight: 700;">Oke</button>
            </div>
        </div>
    </div>

</div>
@endsection
