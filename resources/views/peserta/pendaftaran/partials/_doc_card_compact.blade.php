@php
    /** @var \App\Models\JenisDokumen $jenis */
    /** @var \App\Models\Dokumen|null $terunggah */
    /** @var string $elementKey */
    /** @var int|null $anggotaId */
    /** @var \App\Models\Pendaftaran $pendaftaran */
    /** @var bool $bisaUnggah */
    /** @var int $maksMb */

    $isPdf = $terunggah && (str_contains(strtolower($terunggah->mime ?? ''), 'pdf') || str_ends_with(strtolower($terunggah->nama_asli ?? ''), '.pdf'));
    $key = $elementKey;
    $kode = strtolower($jenis->kode ?? '');
@endphp

<div class="doc-card-premium {{ $terunggah ? 'is-complete' : '' }}" id="card_doc_{{ $key }}">
    <div class="doc-row-content">
        {{-- Sisi Kiri: Ikon + Nama Dokumen + Badge Wajib (Clean, tanpa teks paragraf berulang) --}}
        <div class="doc-left-area">
            <div class="doc-type-icon" aria-hidden="true">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                </svg>
            </div>

            <div class="doc-info-col">
                <div class="doc-name-row">
                    <span class="doc-title-text">{{ $jenis->nama }}</span>
                    <span class="sr-only">({{ $jenis->wajib ? 'Wajib' : 'Opsional' }})</span>
                </div>
                @if($terunggah)
                    <div class="doc-uploaded-meta">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span class="doc-uploaded-filename" title="{{ $terunggah->nama_asli }}">{{ Str::limit($terunggah->nama_asli, 36) }}</span>
                        <span class="doc-uploaded-filesize">({{ round($terunggah->ukuran / 1024, 1) }} KB)</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Sisi Kanan: Status Berkas & Aksi Ringkas --}}
        <div class="doc-right-action">
            @if($terunggah)
                @php
                    $downloadUrl = $terunggah->getSignedDownloadUrl(5);
                    $previewUrl = $terunggah->getSignedPreviewUrl(5);
                @endphp
                <div class="action-btns-group">
                    <button type="button" class="btn-action-round view" onclick="openDocPreview('{{ $previewUrl }}', '{{ $downloadUrl }}', '{{ addslashes($jenis->nama) }} - {{ addslashes($terunggah->nama_asli) }}', '{{ $terunggah->mime }}')" title="Lihat Berkas">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        <span>Lihat</span>
                    </button>
                    @if($bisaUnggah)
                        <button type="button" class="btn-action-round" onclick="toggleDropzone('{{ $key }}')" title="Ganti Berkas">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"></path></svg>
                            <span>Ganti</span>
                        </button>
                    @endif
                </div>
            @else
                @if($bisaUnggah)
                    <button type="button"
                            class="btn-upload-trigger"
                            id="dropzone_box_{{ $key }}"
                            onclick="document.getElementById('file_input_{{ $key }}').click()"
                            ondragover="handleDragOver(event, this)"
                            ondragleave="handleDragLeave(event, this)"
                            ondrop="handleDrop(event, this, '{{ $key }}', {{ $maksMb }})"
                            title="Klik atau seret berkas ke sini">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                        <span>Unggah File</span>
                    </button>
                @else
                    <span class="status-badge-pending">Belum Ada</span>
                @endif
            @endif
        </div>
    </div>

    {{-- Form Unggah (File Input Tersembunyi & Konfirmasi Berkas Ringkas) --}}
    @if($bisaUnggah)
        <form action="{{ route('peserta.berkas.unggah', $pendaftaran) }}" method="POST" enctype="multipart/form-data" id="form_upload_{{ $key }}" onsubmit="handleUploadSubmit(this, '{{ $key }}')">
            @csrf
            <input type="hidden" name="jenis_dokumen_id" value="{{ $jenis->id }}">
            @if(!empty($anggotaId))
                <input type="hidden" name="kelompok_anggota_id" value="{{ $anggotaId }}">
            @endif

            <input type="file"
                   id="file_input_{{ $key }}"
                   name="file"
                   accept=".pdf,.jpg,.jpeg,.png"
                   style="display: none;"
                   onchange="handleFileSelected(this, '{{ $key }}', {{ $maksMb }})">

            {{-- Baris Penggantian Berkas --}}
            @if($terunggah)
                <div id="dropzone_wrapper_{{ $key }}" class="replace-inline-bar" style="display: none;">
                    <span style="font-size: 0.775rem; color: #475569;">Pilih file pengganti:</span>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <button type="button"
                                class="btn-upload-trigger"
                                onclick="document.getElementById('file_input_{{ $key }}').click()"
                                ondragover="handleDragOver(event, this)"
                                ondragleave="handleDragLeave(event, this)"
                                ondrop="handleDrop(event, this, '{{ $key }}', {{ $maksMb }})">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                            <span>Pilih Berkas</span>
                        </button>
                        <button type="button" class="btn-action-round" onclick="toggleDropzone('{{ $key }}')">
                            Batal
                        </button>
                    </div>
                </div>
            @endif

            {{-- Konfirmasi Berkas yang Dipilih --}}
            <div class="selected-file-confirm-bar" id="selected_bar_{{ $key }}">
                <div class="selected-file-left">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: #02275D; flex-shrink: 0;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                    <span id="selected_name_{{ $key }}" class="selected-file-text"></span>
                    <span id="selected_size_{{ $key }}" class="selected-file-size"></span>
                </div>
                <div class="selected-file-actions">
                    <button type="button" class="btn-action-round" onclick="resetFileSelection('{{ $key }}')">
                        Batal
                    </button>
                    <button type="submit" class="btn-confirm-upload" id="btn_submit_{{ $key }}">
                        Unggah
                    </button>
                </div>
            </div>
        </form>
    @endif
</div>
