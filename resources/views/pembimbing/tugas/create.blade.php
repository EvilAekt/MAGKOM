@extends('layouts.dashboard')

@section('title', 'Buat Tugas Baru - Dashboard Pembimbing MAGKOM')

@push('styles')
    @vite(['resources/css/pages/pembimbing/tugas-create.css'])
@endpush

@push('scripts')
    @vite(['resources/js/pages/pembimbing/tugas-create.js'])
@endpush

@section('content')
<div class="tugas-workspace">

    {{-- Top Action & Header Bar (Sesuai Designnnn/Buat Tugas Baru@1x.png) --}}
    <div class="tugas-topbar">
        <div class="tugas-topbar-left">
            <div class="tugas-icon-circle" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="5" y="4" width="14" height="17" rx="2"></rect>
                    <path d="M9 3h6v3H9zM8.5 11h7M8.5 15h7"></path>
                </svg>
            </div>
            <h1 class="tugas-page-title">Buat Tugas Baru</h1>
            <span class="sr-only">Classroom Workspace</span>
            <nav aria-label="Breadcrumb" class="sr-only">
                <a href="{{ route('pembimbing.dashboard') }}">Dashboard</a> /
                <a href="{{ route('pembimbing.tugas.index') }}">Tugas Peserta</a> /
                <span>Buat Tugas Baru</span>
            </nav>
        </div>

        <div class="tugas-topbar-actions">
            {{-- Split Button: Tugaskan Sekarang + Dropdown Opsi --}}
            <div class="tugas-split-btn">
                <button type="button" class="tugas-btn-primary-action" id="btnSubmitTugaskanDirect">
                    <span>Tugaskan Sekarang</span>
                </button>
                <button type="button" class="tugas-btn-caret" id="btnToggleSplitMenu" aria-label="Opsi publikasi penugasan" aria-haspopup="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M6 9l6 6 6-6"></path>
                    </svg>
                </button>

                {{-- Dropdown Menu ala Designnnn --}}
                <div class="tugas-split-menu" id="menuSplitOptions" role="menu">
                    <button type="button" class="tugas-menu-item" id="menuOptTugaskanSekarang" role="menuitem">
                        <span>Tugaskan Sekarang</span>
                    </button>
                    <button type="button" class="tugas-menu-item" id="menuOptJadwalkan" role="menuitem">
                        <span>Jadwalkan</span>
                    </button>
                    <button type="button" class="tugas-menu-item" id="menuOptSimpanDraf" role="menuitem">
                        <span>Simpan draf</span>
                    </button>
                    <div class="tugas-menu-divider"></div>
                    <button type="button" class="tugas-menu-item tugas-menu-item--danger" id="menuOptHapusDraf" role="menuitem">
                        <span>Hapus draf</span>
                    </button>
                </div>
            </div>

            <a href="{{ route('pembimbing.tugas.index') }}" class="tugas-btn-cancel">
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
    <form id="formBuatTugasClassroom" action="{{ route('pembimbing.tugas.store') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
        @csrf

        {{-- Fallback Select untuk Kompatibilitas Sistem --}}
        <select name="penempatan_id" id="select_penempatan" style="display: none;">
            <option value="">-- Pilih Peserta --</option>
            @foreach ($penempatanList as $p)
                <option value="{{ $p->id }}" {{ (int) old('penempatan_id', $selectedPenempatanId) === (int) $p->id ? 'selected' : '' }}>
                    {{ $p->peserta->nama }}
                </option>
            @endforeach
        </select>

        {{-- Dynamic Hidden Container untuk Multi-Mentee IDs --}}
        <div id="hiddenMenteeInputsContainer">
            @if (is_array(old('penempatan_ids')))
                @foreach (old('penempatan_ids') as $pId)
                    <input type="hidden" name="penempatan_ids[]" value="{{ $pId }}">
                @endforeach
            @elseif ($selectedPenempatanId)
                <input type="hidden" name="penempatan_ids[]" value="{{ $selectedPenempatanId }}">
            @endif
        </div>

        {{-- Hidden Real File Input --}}
        <input type="file" name="file_lampiran" id="realFileInput" style="display: none;" accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.ppt,.pptx,.zip,.png,.jpg,.jpeg">

        {{-- Hidden Real Link Input --}}
        <input type="hidden" name="link_referensi" id="realLinkInput" value="{{ old('link_referensi', $selectedTemplate?->link_referensi) }}">

        {{-- Hidden Real Deadline Input --}}
        <input type="hidden" name="deadline" id="realDeadlineInput" value="{{ old('deadline', \Carbon\Carbon::today()->addDays(7)->format('Y-m-d')) }}">

        {{-- Hidden Real Aksi Publikasi (sekarang, terjadwal, draft) --}}
        <input type="hidden" name="aksi_publikasi" id="realAksiPublikasi" value="sekarang">

        <div class="tugas-body-layout">

            {{-- Kolom Kiri: Canvas Penugasan Utama --}}
            <main class="tugas-main-column">

                {{-- Panel 1: Judul dan Instruksi Pengerjaan --}}
                <section class="tugas-card">
                    @php
                        $hasInitialJudul = !empty(old('judul', $selectedTemplate?->judul));
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
                               value="{{ old('judul', $selectedTemplate?->judul) }}"
                               class="tugas-input-clean">
                    </div>

                    <div class="tugas-instruction-box {{ $errors->has('deskripsi') ? 'has-error' : '' }}">
                        <label for="editorPetunjukVisual" class="tugas-instruction-label">Petunjuk (opsional)</label>
                        <div id="editorPetunjukVisual" 
                             class="tugas-visual-editor" 
                             contenteditable="true" 
                             role="textbox" 
                             aria-multiline="true">{!! old('deskripsi', $selectedTemplate?->deskripsi) !!}</div>
                        <textarea id="inputPetunjuk" 
                                  name="deskripsi" 
                                  style="display: none;">{{ old('deskripsi', $selectedTemplate?->deskripsi) }}</textarea>

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
                        <span class="tugas-lampiran-meta" id="lampiranCountMeta">(opsional) · 0 lampiran</span>
                    </div>

                    <div class="tugas-lampiran-selector-box">
                        <div class="tugas-circle-action-item">
                            <button type="button" class="tugas-circle-btn" id="btnTriggerUploadFile" aria-label="Buka dialog upload file" title="Upload File">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M12 16V4M7 9l5-5 5 5M5 20h14"></path>
                                </svg>
                            </button>
                            <span class="tugas-circle-label">Upload File</span>
                        </div>

                        <div class="tugas-circle-action-item">
                            <button type="button" class="tugas-circle-btn" id="btnTriggerAddLink" aria-label="Buka dialog tambah tautan" title="Tambahkan Link">
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

                {{-- Parameter 1: Tugaskan ke --}}
                <div class="tugas-side-group">
                    <label class="tugas-side-label">Tugaskan ke <span class="req">*</span></label>
                    <button type="button" class="tugas-side-trigger" id="btnOpenMenteeDialog" aria-haspopup="dialog">
                        <svg class="trigger-icon-left" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                            <circle cx="9" cy="8" r="3.5"></circle>
                            <path d="M2.5 20a6.5 6.5 0 0 1 13 0M16 4.5a3.5 3.5 0 0 1 0 7M18 14a6.5 6.5 0 0 1 3.5 6"></path>
                        </svg>
                        <span class="trigger-text" id="txtSelectedMenteeSummary">Pilih peserta magang</span>
                        <svg class="trigger-icon-chevron" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M6 9l6 6 6-6"></path>
                        </svg>
                    </button>
                </div>

                {{-- Parameter 2: Kategori Bidang (Custom Select ala Google Classroom & Warna MAGKOM) --}}
                <div class="tugas-side-group tugas-custom-select-group" id="groupKategoriSelect">
                    <label class="tugas-side-label" id="lblKategori">Kategori Bidang <span class="req">*</span></label>

                    {{-- Native select tersembunyi untuk kompatibilitas form & PHPUnit --}}
                    <select id="selectKategori" name="kategori" required style="display: none;">
                        @php
                            $defaultKat = old('kategori', $selectedTemplate?->kategori ?? '');
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

                    {{-- Custom Trigger & Listbox ala Google Classroom Topik dengan Warna MAGKOM --}}
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
                        $initDeadline = old('deadline', \Carbon\Carbon::today()->addDays(7)->format('Y-m-d'));
                        $deadlineCarbon = \Carbon\Carbon::parse($initDeadline);
                        $deadlineIndoFormatted = $deadlineCarbon->isoFormat('dddd, D MMM Y');
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
                    <div class="tugas-side-hint">
                        Berakhir pukul 23.59 WIB. Tidak boleh melewati tanggal selesai magang peserta.
                    </div>
                </div>

            </aside>

        </div>

        {{-- Modal Dialog: Jadwalkan Tugas --}}
        <div class="tugas-modal-backdrop" id="modalJadwalkanDialog" role="dialog" aria-modal="true" aria-labelledby="ttlModalJadwal">
            <div class="tugas-dialog tugas-dialog--schedule">
                <div style="font-size: 16px; font-weight: 600; color: #111827;" id="ttlModalJadwal">Jadwalkan Tugas</div>
                <p style="font-size: 13px; color: #4b5563; margin: 8px 0 16px 0;">
                    Tentukan tanggal dan waktu publikasi tugas secara otomatis kepada mentee bimbingan terpilih.
                </p>

                <div style="display: flex; flex-direction: column; gap: 14px;">
                    <div>
                        <label class="tugas-side-label" style="margin-bottom: 6px;">Tanggal Publikasi</label>
                        @php
                            $initJadwalTgl = old('tgl_publikasi', date('Y-m-d'));
                            $jadwalCarbon = \Carbon\Carbon::parse($initJadwalTgl);
                            $jadwalIndoFormatted = $jadwalCarbon->isoFormat('dddd, D MMM Y');
                        @endphp
                        <button type="button" class="tugas-side-trigger" id="btnTriggerJadwalDate" style="width: 100%; border: 1px solid #dadce0; border-radius: 6px; padding: 10px 12px; background: #ffffff;" aria-haspopup="dialog">
                            <svg class="trigger-icon-left" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                                <rect x="3.5" y="5" width="17" height="15" rx="2"></rect>
                                <path d="M3.5 10h17M8 3v4M16 3v4"></path>
                            </svg>
                            <span class="trigger-text" id="txtSelectedJadwalDisplay">{{ $jadwalIndoFormatted }}</span>
                            <svg class="trigger-icon-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M6 9l6 6 6-6"></path>
                            </svg>
                        </button>
                        <input type="hidden" id="inputTglPublikasi" name="tgl_publikasi" value="{{ $initJadwalTgl }}">
                    </div>
                    <div>
                        <label for="inputWaktuPublikasi" style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Waktu Publikasi (WIB)</label>
                        <input type="time" id="inputWaktuPublikasi" name="waktu_publikasi" value="{{ old('waktu_publikasi', '08:00') }}" style="width: 100%; box-sizing: border-box; padding: 8px 12px; border-radius: 6px; border: 1px solid #dadce0; font-size: 14px;">
                    </div>
                </div>

                <div style="margin-top: 24px; display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="tugas-dialog-btn-text" id="btnCancelJadwalkan">Batal</button>
                    <button type="button" class="tugas-dialog-btn-pill" id="btnConfirmJadwalkan">Jadwalkan</button>
                </div>
            </div>
        </div>

    </form>

    {{-- Modal Dialog: Tugaskan ke (Multi-Mentee Selector) --}}
    <div class="tugas-modal-backdrop" id="modalMenteeDialog" role="dialog" aria-modal="true" aria-labelledby="ttlModalMentee">
        <div class="tugas-dialog tugas-dialog--mentee">
            <div class="tugas-dialog-header" id="ttlModalMentee">
                Tugaskan ke
            </div>

            <div class="tugas-dialog-search">
                <input type="text" id="inputSearchMentee" placeholder="Cari nama atau universitas mentee..." aria-label="Cari mentee">
            </div>

            <div class="tugas-mentee-list">
                {{-- Master Checkbox: Semua peserta --}}
                <label class="tugas-mentee-row is-all">
                    <input type="checkbox" id="chkSemuaPeserta" style="width: 18px; height: 18px; accent-color: #1d4ed8; cursor: pointer;">
                    <span>Semua peserta</span>
                </label>

                {{-- Individual Mentee Rows --}}
                @php
                    $avatarColors = ['#1d4ed8', '#059669', '#d97706', '#7c3aed', '#db2777', '#0891b2'];
                @endphp
                @forelse ($penempatanList as $idx => $p)
                    @php
                        $menteeInitial = strtoupper(substr($p->peserta->nama ?? 'P', 0, 1));
                        $colorBg = $avatarColors[$idx % count($avatarColors)];
                        $isSelected = is_array(old('penempatan_ids')) 
                            ? in_array($p->id, old('penempatan_ids')) 
                            : ($selectedPenempatanId && (int)$selectedPenempatanId === (int)$p->id);
                    @endphp
                    <label class="tugas-mentee-row js-mentee-item" data-nama="{{ strtolower($p->peserta->nama ?? '') }}" data-univ="{{ strtolower($p->peserta->profilPeserta->institusi ?? '') }}">
                        <input type="checkbox" 
                               class="js-chk-mentee" 
                               value="{{ $p->id }}" 
                               data-nama="{{ $p->peserta->nama }}"
                               {{ $isSelected ? 'checked' : '' }}
                               style="width: 18px; height: 18px; accent-color: #1d4ed8; cursor: pointer;">
                        <span class="tugas-mentee-avatar" style="background: {{ $colorBg }};">
                            {{ $menteeInitial }}
                        </span>
                        <div class="tugas-mentee-info">
                            <span class="tugas-mentee-name">{{ $p->peserta->nama }}</span>
                            <span class="tugas-mentee-sub">{{ $p->peserta->profilPeserta->institusi ?? 'Institusi Belum Diisi' }}</span>
                        </div>
                    </label>
                @empty
                    <div style="padding: 24px; text-align: center; color: #6b7280; font-size: 14px;">
                        Tidak ada peserta magang aktif dalam kewenangan Anda saat ini.
                    </div>
                @endforelse
            </div>

            <div class="tugas-dialog-footer">
                <button type="button" class="tugas-dialog-btn-text" id="btnCancelMentee">Batal</button>
                <button type="button" class="tugas-dialog-btn-pill" id="btnConfirmMentee">Selesai</button>
            </div>
        </div>
    </div>

    {{-- Modal Dialog: Upload File --}}
    <div class="tugas-modal-backdrop" id="modalUploadFileDialog" role="dialog" aria-modal="true" aria-labelledby="ttlModalUpload">
        <div class="tugas-dialog tugas-dialog--upload">
            <div style="font-size: 16px; font-weight: 600; color: #111827;" id="ttlModalUpload">Upload File</div>

            <div class="tugas-dropzone" id="tugasDropzoneArea" tabindex="0" role="button" aria-label="Area drag and drop upload berkas">
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
                <button type="button" class="tugas-dropzone-btn-choose" id="btnBrowseFile">Pilih File</button>
                <div class="tugas-dropzone-hint">atau tarik file ke sini</div>
                <div class="tugas-dropzone-format">PDF, ZIP, DOCX · maks. 10MB</div>
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
            <div style="font-size: 16px; font-weight: 600; color: #111827;" id="ttlModalLink">Tambahkan link</div>

            <div style="margin-top: 18px;">
                <div class="tugas-field-filled" style="padding: 8px 14px;">
                    <label for="inputModalLinkUrl" class="tugas-field-filled-label">Link*</label>
                    <input type="url" id="inputModalLinkUrl" placeholder="https://example.com/..." class="tugas-input-clean" style="font-size: 15px;">
                </div>
                <div id="linkErrorHint" style="margin: 6px 0 0 8px; font-size: 12px; color: #b3261e;">*Wajib diisi</div>
            </div>

            <div style="margin-top: 24px; display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="tugas-dialog-btn-text" id="btnCancelAddLink">Batal</button>
                <button type="button" class="tugas-dialog-btn-pill" id="btnSubmitAddLink" disabled>Tambahkan link</button>
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
                <span class="tugas-cal-label" id="calHeaderMonth">Nov</span>
                <button type="button" class="tugas-cal-nav-btn" id="calNextMonth" aria-label="Bulan berikutnya">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"></path></svg>
                </button>
            </div>
            <div class="tugas-cal-nav-group">
                <span class="tugas-cal-label" id="calHeaderYear">2026</span>
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

    {{-- Popup Calendar Jadwal Publikasi --}}
    <div class="tugas-calendar-popup" id="popupCalendarJadwal" role="dialog" aria-modal="false" aria-label="Kalender Tanggal Publikasi">
        <div class="tugas-cal-header">
            <div class="tugas-cal-nav-group">
                <button type="button" class="tugas-cal-nav-btn" id="calJadwalPrevMonth" aria-label="Bulan sebelumnya">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 18l-6-6 6-6"></path></svg>
                </button>
                <span class="tugas-cal-label" id="calJadwalHeaderMonth">Nov</span>
                <button type="button" class="tugas-cal-nav-btn" id="calJadwalNextMonth" aria-label="Bulan berikutnya">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"></path></svg>
                </button>
            </div>
            <div class="tugas-cal-nav-group">
                <span class="tugas-cal-label" id="calJadwalHeaderYear">2026</span>
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

        <div class="tugas-cal-grid-dates" id="calJadwalDatesGrid"></div>

        <div class="tugas-cal-footer">
            <button type="button" class="tugas-dialog-btn-text" id="calJadwalBtnClear">Hapus</button>
            <div>
                <button type="button" class="tugas-dialog-btn-text" id="calJadwalBtnCancel">Batal</button>
                <button type="button" class="tugas-dialog-btn-text" id="calJadwalBtnOk" style="font-weight: 700;">Oke</button>
            </div>
        </div>
    </div>

</div>
@endsection
