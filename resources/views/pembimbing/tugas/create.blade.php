@extends('layouts.dashboard')

@section('title', 'Buat Tugas Baru - Assignment Workspace MagKom')

@section('content')
<div class="dashboard-content" style="display: flex; flex-direction: column; gap: 1.5rem; max-width: 1400px; margin: 0 auto; width: 100%;">

    <style>
        .classroom-layout-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
            align-items: start;
        }
        @media (min-width: 1024px) {
            .classroom-layout-grid {
                grid-template-columns: 1.8fr 1.2fr;
            }
        }
        .form-card {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid var(--neutral-200, #E2E8F0);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
        }
        @media (min-width: 640px) {
            .form-card {
                padding: 1.75rem;
            }
        }
        .form-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--neutral-600, #475569);
            margin-bottom: 0.45rem;
        }
        .form-control-input {
            width: 100%;
            border-radius: 10px;
            border: 1px solid var(--neutral-300, #CBD5E1);
            background: #F8FAFC;
            padding: 0.65rem 0.9rem;
            font-size: 0.875rem;
            color: var(--neutral-900, #0F172A);
            transition: all 0.2s ease;
            box-sizing: border-box;
        }
        .form-control-input:focus {
            outline: none;
            background: #ffffff;
            border-color: #0641CC;
            box-shadow: 0 0 0 3px rgba(6, 65, 204, 0.15);
        }
        .complexity-option {
            cursor: pointer;
            position: relative;
        }
        .complexity-option input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }
        .complexity-box {
            padding: 0.6rem 0.5rem;
            text-align: center;
            border-radius: 10px;
            border: 1px solid var(--neutral-300, #CBD5E1);
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--neutral-600, #475569);
            background: #F8FAFC;
            transition: all 0.2s ease;
            user-select: none;
        }
        .complexity-option input[value="rendah"]:checked + .complexity-box {
            background: #ECFDF5;
            border-color: #10B981;
            color: #047857;
            box-shadow: 0 1px 3px rgba(16, 185, 129, 0.2);
        }
        .complexity-option input[value="sedang"]:checked + .complexity-box {
            background: #FFFBEB;
            border-color: #F59E0B;
            color: #B45309;
            box-shadow: 0 1px 3px rgba(245, 158, 11, 0.2);
        }
        .complexity-option input[value="tinggi"]:checked + .complexity-box {
            background: #EEF2FF;
            border-color: #6366F1;
            color: #4338CA;
            box-shadow: 0 1px 3px rgba(99, 102, 241, 0.2);
        }
        .dropzone-box {
            border: 2px dashed #CBD5E1;
            border-radius: 14px;
            padding: 1.75rem 1.25rem;
            text-align: center;
            cursor: pointer;
            background: #F8FAFC;
            transition: all 0.2s ease;
        }
        .dropzone-box:hover, .dropzone-box.drag-over {
            border-color: #0641CC;
            background: #EFF6FF;
        }
    </style>

    {{-- Top Action & Breadcrumb Navigation --}}
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0); padding-bottom: 1rem;">
        <div>
            <nav style="font-size: 0.8rem; color: var(--neutral-500, #64748B); margin-bottom: 0.4rem; display: flex; align-items: center; gap: 0.4rem;">
                <a href="{{ route('pembimbing.dashboard') }}" style="color: var(--neutral-600, #475569); text-decoration: none;">Dashboard</a>
                <span>/</span>
                <a href="{{ route('pembimbing.tugas.index') }}" style="color: var(--neutral-600, #475569); text-decoration: none;">Tugas Peserta</a>
                <span>/</span>
                <span style="color: var(--neutral-900, #0F172A); font-weight: 600;">Buat Tugas Baru</span>
            </nav>
            <div style="display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
                <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0; letter-spacing: -0.02em;">
                    Buat Tugas Baru
                </h1>
                <span style="display: inline-flex; align-items: center; padding: 0.2rem 0.65rem; border-radius: 9999px; font-size: 0.72rem; font-weight: 700; background: #DBEAFE; color: #1E40AF; text-transform: uppercase; letter-spacing: 0.04em;">
                    Classroom Workspace
                </span>
            </div>
            <p style="font-size: 0.85rem; color: var(--neutral-600, #475569); margin: 0.35rem 0 0 0;">
                Susun instruksi terstruktur, tautan referensi, dan lampiran berkas acuan kerja untuk mentee bimbingan.
            </p>
        </div>

        <div style="display: flex; items-center; gap: 0.75rem;">
            <a href="{{ route('pembimbing.tugas.index') }}" 
               style="padding: 0.55rem 1rem; background: #ffffff; color: var(--neutral-700, #334155); border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 10px; font-size: 0.875rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem; transition: background 0.15s ease;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0;"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Batal</span>
            </a>
            <button type="submit" 
                    form="formBuatTugasClassroom" 
                    id="btnSubmitTugas"
                    style="padding: 0.55rem 1.35rem; background: #0641CC; color: #ffffff; border: none; border-radius: 10px; font-size: 0.875rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 2px 6px rgba(6, 65, 204, 0.25); transition: all 0.2s ease;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                <span>Tugaskan Sekarang</span>
            </button>
        </div>
    </div>

    {{-- Error Banner Alert --}}
    @if ($errors->any())
        <div style="background: rgba(239, 68, 68, 0.08); border: 1px solid rgba(239, 68, 68, 0.25); border-radius: 12px; padding: 1rem 1.25rem; color: #991B1B;">
            <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2" style="flex-shrink: 0; margin-top: 0.1rem;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                <div>
                    <h3 style="font-size: 0.875rem; font-weight: 700; margin: 0 0 0.25rem 0;">Terdapat kesalahan pada isian formulir:</h3>
                    <ul style="margin: 0; padding-left: 1.25rem; font-size: 0.8rem; line-height: 1.5;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Main Two-Column Classroom Workspace --}}
    <form id="formBuatTugasClassroom" 
          action="{{ route('pembimbing.tugas.store') }}" 
          method="POST" 
          enctype="multipart/form-data">
        @csrf

        <div class="classroom-layout-grid">

            {{-- Kolom Kiri: Canvas Penugasan Utama (Main Canvas - ~60-65%) --}}
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">

                {{-- Kartu 1: Judul dan Deskripsi Instruksi --}}
                <div class="form-card" style="display: flex; flex-direction: column; gap: 1.25rem;">
                    <div>
                        <label for="judul" class="form-label">
                            Judul Penugasan <span style="color: #DC2626;">*</span>
                        </label>
                        <input type="text" 
                               name="judul" 
                               id="judul" 
                               required 
                               maxlength="150"
                               value="{{ old('judul') }}"
                               placeholder="Tuliskan judul tugas (contoh: Analisis Skema Database & REST Endpoint Antrean Layanan)" 
                               style="width: 100%; font-size: 1.15rem; font-weight: 700; color: var(--neutral-900, #0F172A); padding: 0.75rem 0.9rem; border-radius: 10px; border: 1px solid var(--neutral-300, #CBD5E1); background: #ffffff; box-sizing: border-box;"
                               class="form-control-input">
                    </div>

                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.45rem;">
                            <label for="deskripsi" class="form-label" style="margin-bottom: 0;">
                                Petunjuk & Instruksi Pengerjaan <span style="color: #DC2626;">*</span>
                            </label>
                            <span style="font-size: 0.75rem; color: var(--neutral-500, #64748B);">Jelaskan ekspektasi hasil & kriteria tugas</span>
                        </div>
                        <textarea name="deskripsi" 
                                  id="deskripsi" 
                                  rows="9" 
                                  required 
                                  placeholder="Tuliskan detail arahan penugasan untuk mentee...&#10;&#10;Contoh:&#10;1. Pelajari spesifikasi dokumen TOR terlampir.&#10;2. Implementasikan skema validasi data sesuai aturan kedinasan.&#10;3. Unggah hasil pull request atau lembar kerja dokumen ke repositori." 
                                  style="width: 100%; border-radius: 10px; border: 1px solid var(--neutral-300, #CBD5E1); background: #F8FAFC; padding: 0.85rem 1rem; font-size: 0.875rem; line-height: 1.6; color: var(--neutral-900, #0F172A); font-family: inherit; resize: vertical; box-sizing: border-box;"
                                  class="form-control-input">{{ old('deskripsi') }}</textarea>
                    </div>
                </div>

                {{-- Kartu 2: Materi Acuan & Lampiran Dokumen (Attachment Hub) --}}
                <div class="form-card" style="display: flex; flex-direction: column; gap: 1.25rem;">
                    <div style="border-bottom: 1px solid var(--neutral-200, #E2E8F0); padding-bottom: 0.85rem;">
                        <div style="display: flex; align-items: center; gap: 0.6rem;">
                            <div style="width: 32px; height: 32px; border-radius: 8px; background: #EFF6FF; color: #0641CC; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg>
                            </div>
                            <div>
                                <h2 style="font-size: 0.95rem; font-weight: 700; color: var(--neutral-900, #0F172A); margin: 0;">
                                    Materi Acuan & Lampiran Dokumen
                                </h2>
                                <p style="font-size: 0.78rem; color: var(--neutral-500, #64748B); margin: 0.15rem 0 0 0;">
                                    Sediakan materi pendukung agar mentee memiliki acuan pengerjaan yang jelas.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- 1. Input Tautan Referensi Eksternal --}}
                    <div>
                        <label for="link_referensi" class="form-label">
                            Tautan Referensi Eksternal (Opsional)
                        </label>
                        <div style="position: relative; display: flex; align-items: center;">
                            <span style="position: absolute; left: 0.85rem; color: var(--neutral-400, #94A3B8); pointer-events: none; display: flex; align-items: center;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            </span>
                            <input type="url" 
                                   name="link_referensi" 
                                   id="link_referensi" 
                                   value="{{ old('link_referensi') }}"
                                   placeholder="https://github.com/..., https://figma.com/..., atau docs.google.com/..." 
                                   style="padding-left: 2.35rem;"
                                   class="form-control-input">
                        </div>
                        <span style="font-size: 0.72rem; color: var(--neutral-500, #64748B); margin-top: 0.35rem; display: block;">
                            Tautan ke repositori, Google Docs, Figma, Jira, atau papan kerja kolaboratif.
                        </span>
                    </div>

                    {{-- 2. Dropzone Berkas Acuan Kerja --}}
                    <div>
                        <label class="form-label">
                            Unggah Dokumen Acuan / Lembar Kerja (Opsional)
                        </label>

                        <div id="dropzoneContainer" class="dropzone-box">
                            <input type="file" 
                                   name="file_lampiran" 
                                   id="file_lampiran" 
                                   style="display: none;" 
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.ppt,.pptx,.zip,.png,.jpg,.jpeg">
                            
                            <div id="dropzonePrompt" style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
                                <div style="width: 44px; height: 44px; border-radius: 50%; background: #EFF6FF; color: #0641CC; display: flex; align-items: center; justify-content: center;">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                </div>
                                <div style="font-size: 0.875rem; font-weight: 600; color: var(--neutral-800, #1E293B);">
                                    <span style="color: #0641CC; text-decoration: underline;">Klik untuk pilih berkas</span> atau tarik & lepas berkas ke sini
                                </div>
                                <p style="font-size: 0.75rem; color: var(--neutral-500, #64748B); margin: 0;">
                                    Mendukung PDF, Word (DOCX), Excel (XLSX), PPTX, ZIP, Gambar (Maksimum 10 MB)
                                </p>
                            </div>

                            {{-- File Preview Card --}}
                            <div id="filePreviewCard" style="display: none; background: #ffffff; border-radius: 10px; border: 1px solid var(--neutral-200, #E2E8F0); padding: 0.85rem 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); text-align: left;">
                                <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.75rem;">
                                    <div style="display: flex; align-items: center; gap: 0.75rem; min-width: 0;">
                                        <div id="previewBadge" style="width: 38px; height: 38px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.7rem; text-transform: uppercase; color: #ffffff; flex-shrink: 0; background: #0641CC;">
                                            DOC
                                        </div>
                                        <div style="min-width: 0;">
                                            <p id="previewFileName" style="font-size: 0.85rem; font-weight: 700; color: var(--neutral-900, #0F172A); margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">nama_dokumen.pdf</p>
                                            <p id="previewFileSize" style="font-size: 0.72rem; color: var(--neutral-500, #64748B); margin: 0.15rem 0 0 0;">1.2 MB</p>
                                        </div>
                                    </div>
                                    <button type="button" 
                                            id="btnRemoveFile" 
                                            style="padding: 0.4rem; border-radius: 6px; border: none; background: transparent; color: var(--neutral-400, #94A3B8); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: color 0.15s ease;"
                                            title="Batalkan pilihan file">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Kolom Kanan: Panel Pengaturan Penugasan (Settings Sidebar - ~35-40%) --}}
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">

                {{-- Kartu Pengaturan Parameter (Sticky) --}}
                <div class="form-card" style="position: sticky; top: 1.5rem; display: flex; flex-direction: column; gap: 1.25rem;">
                    <div style="border-bottom: 1px solid var(--neutral-200, #E2E8F0); padding-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0641CC" stroke-width="2" style="flex-shrink: 0;"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                        <h2 style="font-size: 0.85rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: var(--neutral-900, #0F172A); margin: 0;">
                            Parameter Penugasan
                        </h2>
                    </div>

                    {{-- 1. Target Mentee --}}
                    <div>
                        <label for="select_penempatan" class="form-label">
                            Target Mentee Bimbingan <span style="color: #DC2626;">*</span>
                        </label>
                        <select name="penempatan_id" 
                                id="select_penempatan" 
                                required 
                                class="form-control-input">
                            <option value="" disabled {{ old('penempatan_id', $selectedPenempatanId) ? '' : 'selected' }}>-- Pilih Peserta Bimbingan --</option>
                            @foreach ($penempatanList as $p)
                                @php
                                    $isSel = old('penempatan_id', $selectedPenempatanId) == $p->id;
                                    $tglSelesaiFormatted = $p->tgl_selesai ? \Carbon\Carbon::parse($p->tgl_selesai)->format('Y-m-d') : '';
                                    $tglSelesaiLabel = $p->tgl_selesai ? \Carbon\Carbon::parse($p->tgl_selesai)->format('d M Y') : 'N/A';
                                @endphp
                                <option value="{{ $p->id }}" 
                                        data-selesai="{{ $tglSelesaiFormatted }}"
                                        data-selesai-label="{{ $tglSelesaiLabel }}"
                                        data-kampus="{{ $p->peserta->profil->institusi ?? 'Institusi Terdaftar' }}"
                                        {{ $isSel ? 'selected' : '' }}>
                                    {{ $p->peserta->nama }} ({{ $p->peserta->profil->institusi ?? 'Magang' }})
                                </option>
                            @endforeach
                        </select>

                        {{-- Dynamic Mentee Info Box --}}
                        <div id="menteeInfoBox" style="display: none; margin-top: 0.65rem; padding: 0.75rem 0.9rem; border-radius: 10px; background: #EFF6FF; border: 1px solid #BFDBFE; font-size: 0.75rem; color: #1E3A8A;">
                            <div style="font-weight: 700; display: flex; align-items: center; gap: 0.4rem; margin-bottom: 0.25rem;">
                                <span style="width: 6px; height: 6px; border-radius: 50%; background: #0641CC; display: inline-block;"></span>
                                <span id="infoMenteeKampus">-</span>
                            </div>
                            <div style="font-size: 0.72rem; color: #1D4ED8;">
                                Batas Akhir Magang: <span id="infoMenteeSelesai" style="font-weight: 700;">-</span>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Kategori Tugas --}}
                    <div>
                        <label for="kategori" class="form-label">
                            Kategori Bidang <span style="color: #DC2626;">*</span>
                        </label>
                        <input type="text" 
                               name="kategori" 
                               id="kategori" 
                               list="kategoriList" 
                               required 
                               value="{{ old('kategori', 'Pengembangan Web') }}" 
                               placeholder="Pilih atau ketik kategori tugas..."
                               class="form-control-input">
                        <datalist id="kategoriList">
                            <option value="Pengembangan Web">
                            <option value="UI/UX Design">
                            <option value="Jaringan & Keamanan">
                            <option value="Analisis Data">
                            <option value="Administrasi TI">
                            <option value="Dokumentasi Teknis">
                            <option value="Pengujian Sistem">
                        </datalist>
                    </div>

                    {{-- 3. Tingkat Kompleksitas (Segmented Control) --}}
                    <div>
                        <label class="form-label">
                            Tingkat Kompleksitas <span style="color: #DC2626;">*</span>
                        </label>
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem;">
                            <label class="complexity-option">
                                <input type="radio" name="kompleksitas" value="rendah" {{ old('kompleksitas') === 'rendah' ? 'checked' : '' }}>
                                <div class="complexity-box">
                                    Rendah
                                </div>
                            </label>
                            <label class="complexity-option">
                                <input type="radio" name="kompleksitas" value="sedang" {{ old('kompleksitas', 'sedang') === 'sedang' ? 'checked' : '' }}>
                                <div class="complexity-box">
                                    Sedang
                                </div>
                            </label>
                            <label class="complexity-option">
                                <input type="radio" name="kompleksitas" value="tinggi" {{ old('kompleksitas') === 'tinggi' ? 'checked' : '' }}>
                                <div class="complexity-box">
                                    Tinggi
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- 4. Batas Waktu Pengerjaan (Deadline) --}}
                    <div>
                        <label for="deadline" class="form-label">
                            Tenggat Waktu (Deadline) <span style="color: #DC2626;">*</span>
                        </label>
                        <input type="date" 
                               name="deadline" 
                               id="deadline" 
                               required 
                               min="{{ date('Y-m-d') }}" 
                               value="{{ old('deadline', \Carbon\Carbon::today()->addDays(7)->format('Y-m-d')) }}"
                               class="form-control-input">
                        <span id="deadlineToSelesaiHint" style="font-size: 0.72rem; color: var(--neutral-500, #64748B); margin-top: 0.35rem; display: block;">
                            Tenggat pengerjaan tidak boleh melampaui tanggal selesai magang mentee.
                        </span>
                    </div>

                    {{-- Box Tips Pembimbing Diskominfo Sleman --}}
                    <div style="padding: 0.85rem 1rem; border-radius: 10px; background: #F8FAFC; border: 1px solid var(--neutral-200, #E2E8F0); font-size: 0.75rem; color: var(--neutral-600, #475569); display: flex; flex-direction: column; gap: 0.3rem;">
                        <div style="font-weight: 700; color: var(--neutral-800, #1E293B); display: flex; align-items: center; gap: 0.4rem;">
                            <span>💡</span>
                            <span>Panduan Penugasan Magang</span>
                        </div>
                        <p style="margin: 0; line-height: 1.5; color: var(--neutral-500, #64748B); font-size: 0.72rem;">
                            Berikan petunjuk pengerjaan yang spesifik dan tenggat terukur agar mentee dapat mengisi logbook harian dan menyerahkan hasil tepat waktu.
                        </p>
                    </div>

                </div>

            </div>

        </div>
    </form>

</div>

{{-- Interactive Client-side Logic (Vanilla JS) --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('file_lampiran');
    const dropzoneContainer = document.getElementById('dropzoneContainer');
    const dropzonePrompt = document.getElementById('dropzonePrompt');
    const filePreviewCard = document.getElementById('filePreviewCard');
    const previewFileName = document.getElementById('previewFileName');
    const previewFileSize = document.getElementById('previewFileSize');
    const previewBadge = document.getElementById('previewBadge');
    const btnRemoveFile = document.getElementById('btnRemoveFile');

    const selectPenempatan = document.getElementById('select_penempatan');
    const menteeInfoBox = document.getElementById('menteeInfoBox');
    const infoMenteeKampus = document.getElementById('infoMenteeKampus');
    const infoMenteeSelesai = document.getElementById('infoMenteeSelesai');
    const inputDeadline = document.getElementById('deadline');

    // 1. File Upload Dropzone Handler
    if (dropzoneContainer && fileInput) {
        dropzoneContainer.addEventListener('click', function (e) {
            if (!e.target.closest('#btnRemoveFile')) {
                fileInput.click();
            }
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            dropzoneContainer.addEventListener(eventName, function (e) {
                e.preventDefault();
                e.stopPropagation();
                dropzoneContainer.classList.add('drag-over');
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzoneContainer.addEventListener(eventName, function (e) {
                e.preventDefault();
                e.stopPropagation();
                dropzoneContainer.classList.remove('drag-over');
            });
        });

        dropzoneContainer.addEventListener('drop', function (e) {
            const dt = e.dataTransfer;
            if (dt && dt.files && dt.files.length > 0) {
                fileInput.files = dt.files;
                handleFileSelected(dt.files[0]);
            }
        });

        fileInput.addEventListener('change', function () {
            if (fileInput.files && fileInput.files.length > 0) {
                handleFileSelected(fileInput.files[0]);
            }
        });

        if (btnRemoveFile) {
            btnRemoveFile.addEventListener('click', function (e) {
                e.stopPropagation();
                fileInput.value = '';
                filePreviewCard.style.display = 'none';
                dropzonePrompt.style.display = 'flex';
            });
        }
    }

    function formatBytes(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
    }

    function getBadgeColorByExt(ext) {
        ext = ext.toLowerCase();
        if (['pdf'].includes(ext)) return { bg: '#DC2626', label: 'PDF' };
        if (['doc', 'docx'].includes(ext)) return { bg: '#2563EB', label: 'DOC' };
        if (['xls', 'xlsx', 'csv'].includes(ext)) return { bg: '#16A34A', label: 'XLS' };
        if (['ppt', 'pptx'].includes(ext)) return { bg: '#EA580C', label: 'PPT' };
        if (['zip', 'rar', '7z'].includes(ext)) return { bg: '#7C3AED', label: 'ZIP' };
        return { bg: '#475569', label: ext.toUpperCase() };
    }

    function handleFileSelected(file) {
        const ext = file.name.split('.').pop() || '';
        const badgeInfo = getBadgeColorByExt(ext);

        previewFileName.textContent = file.name;
        previewFileSize.textContent = formatBytes(file.size);
        previewBadge.textContent = badgeInfo.label;
        previewBadge.style.backgroundColor = badgeInfo.bg;

        dropzonePrompt.style.display = 'none';
        filePreviewCard.style.display = 'block';
    }

    // 2. Mentee Select & Dynamic Constraint Handler
    function updateMenteeInfo() {
        if (!selectPenempatan) return;
        const opt = selectPenempatan.options[selectPenempatan.selectedIndex];
        if (opt && opt.value) {
            const selesaiDate = opt.getAttribute('data-selesai');
            const selesaiLabel = opt.getAttribute('data-selesai-label');
            const kampus = opt.getAttribute('data-kampus');

            if (kampus && selesaiLabel) {
                infoMenteeKampus.textContent = kampus;
                infoMenteeSelesai.textContent = selesaiLabel;
                menteeInfoBox.style.display = 'block';
            }

            if (selesaiDate && inputDeadline) {
                inputDeadline.setAttribute('max', selesaiDate);
                if (inputDeadline.value && inputDeadline.value > selesaiDate) {
                    inputDeadline.value = selesaiDate;
                }
            }
        } else {
            if (menteeInfoBox) menteeInfoBox.style.display = 'none';
            if (inputDeadline) inputDeadline.removeAttribute('max');
        }
    }

    if (selectPenempatan) {
        selectPenempatan.addEventListener('change', updateMenteeInfo);
        updateMenteeInfo();
    }
});
</script>
@endsection
