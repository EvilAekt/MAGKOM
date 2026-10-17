@extends('layouts.dashboard')

@section('title', 'Modul Studio - Edit Skema ' . $modul->nama_modul)
@section('page_title', 'Modul Studio: Edit ' . $modul->nama_modul)
@section('page_description', 'Perbarui skema kolom, konfigurasi tata kelola izin, dan ikon modul ' . $modul->nama_modul . '.')

@section('breadcrumbs')
    <a href="{{ route('superadmin.dashboard') }}">SuperAdmin</a>
    <span class="breadcrumb-separator">/</span>
    <a href="{{ route('superadmin.modul-builder.index') }}">Pembuat Modul</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Edit: {{ $modul->nama_modul }}</span>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modul-studio.css') }}">
@endpush

@section('content')
<div class="studio-container">

    @if ($errors->any())
        <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #DC2626; padding: 1rem 1.25rem; border-radius: var(--studio-radius-md); font-size: 0.875rem;">
            <strong style="display: block; margin-bottom: 0.35rem;">Terdapat kesalahan pada formulir modul:</strong>
            <ul style="margin: 0; padding-left: 1.25rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('superadmin.modul-builder.update', $modul) }}" method="POST" id="form-builder">
        @csrf
        @method('PUT')
        <input type="hidden" name="tipe_sumber" value="{{ $modul->tipe_sumber }}">
        <input type="hidden" name="nama_tabel_fisik" value="{{ $modul->nama_tabel_fisik }}">
        <input type="hidden" name="ikon" id="input-ikon-val" value="{{ old('ikon', $modul->ikon ?? 'laptop') }}">

        {{-- STUDIO TOPBAR --}}
        <div class="studio-topbar">
            <div class="studio-header-title">
                <div class="studio-avatar-badge" id="header-avatar-preview">
                    {!! \App\Models\ModulDinamis::getSvgForIcon(old('ikon', $modul->ikon ?? 'laptop'), 26) !!}
                </div>
                <div class="studio-header-text">
                    <h1 id="header-title-preview">{{ old('nama_modul', $modul->nama_modul) }}</h1>
                    <p>Slug Rute: <code>/superadmin/modul/{{ $modul->slug }}</code> &bull; Sumber: {{ $modul->tipe_sumber === 'physical_table' ? 'Tabel Basis Data (' . $modul->nama_tabel_fisik . ')' : 'Skema Dinamis JSON' }}</p>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <a href="{{ route('superadmin.modul-builder.index') }}" style="height: 40px; padding: 0 1.25rem; border: 1px solid var(--studio-border); border-radius: var(--studio-radius-sm); font-size: 0.875rem; font-weight: 600; color: var(--neutral-700); background: var(--studio-card-bg); display: inline-flex; align-items: center; text-decoration: none;">
                    Batal
                </a>
                <button type="submit" style="height: 40px; padding: 0 1.5rem; border: none; border-radius: var(--studio-radius-sm); font-size: 0.875rem; font-weight: 800; color: #FFFFFF; background: var(--studio-primary); cursor: pointer; display: inline-flex; align-items: center; gap: 0.45rem; box-shadow: 0 2px 10px rgba(7, 81, 255, 0.2);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    <span>Simpan Perubahan Skema</span>
                </button>
            </div>
        </div>

        {{-- STUDIO 2-COLUMN SPLIT GRID --}}
        <div class="studio-grid-layout">

            {{-- SISI KIRI: WORKSPACE DESIGNER --}}
            <div class="studio-workspace">

                {{-- 1. IDENTITAS & IKON VISUAL --}}
                <div class="studio-card">
                    <div class="studio-card-header">
                        <div class="studio-card-title">
                            <span class="studio-step-num">1</span>
                            <div>
                                <h3>Identitas &amp; Ikon Modul</h3>
                                <p>Sesuaikan nama modul, visual ikon resmi, dan primary key.</p>
                            </div>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr; gap: 1.25rem;">
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase; margin-bottom: 0.35rem;">Nama Modul *</label>
                            <input type="text" name="nama_modul" id="input-nama-modul" value="{{ old('nama_modul', $modul->nama_modul) }}" required style="width: 100%; height: 42px; border: 1px solid var(--studio-border); border-radius: var(--studio-radius-sm); padding: 0 0.875rem; font-size: 0.875rem; background: var(--studio-card-bg); color: var(--neutral-900);">
                        </div>

                        {{-- WIDGET VISUAL ICON PICKER --}}
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase; margin-bottom: 0.35rem;">Ikon Visual Resmi Diskominfo *</label>
                            <div class="icon-picker-widget">
                                <div class="icon-preview-tile" id="widget-icon-tile">
                                    {!! \App\Models\ModulDinamis::getSvgForIcon(old('ikon', $modul->ikon ?? 'laptop'), 26) !!}
                                </div>
                                <div class="icon-picker-info">
                                    <strong id="widget-icon-name">{{ $availableIcons[old('ikon', $modul->ikon ?? 'laptop')] ?? 'Laptop / Komputer' }}</strong>
                                    <span id="widget-icon-slug">Kata Kunci: {{ old('ikon', $modul->ikon ?? 'laptop') }}</span>
                                </div>
                                <button type="button" class="btn-trigger-picker" onclick="openIconModal()">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                                    <span>Pilih Ikon (42 Pilihan)</span>
                                </button>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase; margin-bottom: 0.35rem;">Kolom Primary Key *</label>
                                <input type="text" name="primary_key" id="input-primary-key" value="{{ old('primary_key', $modul->primary_key ?? 'id') }}" required style="width: 100%; height: 42px; border: 1px solid var(--studio-border); border-radius: var(--studio-radius-sm); padding: 0 0.875rem; font-size: 0.875rem; font-family: monospace; background: var(--studio-card-bg); color: var(--neutral-900);">
                            </div>
                            <div style="display: flex; align-items: center; padding-top: 1.25rem;">
                                <label style="display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; font-weight: 600; cursor: pointer;">
                                    <input type="checkbox" name="is_manual_pk" id="check-manual-pk" value="1" {{ old('is_manual_pk', $modul->is_manual_pk) ? 'checked' : '' }}>
                                    <span>Primary Key Manual (Non-Increment)</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase; margin-bottom: 0.35rem;">Deskripsi Modul</label>
                            <textarea name="deskripsi" id="input-deskripsi" rows="2" style="width: 100%; border: 1px solid var(--studio-border); border-radius: var(--studio-radius-sm); padding: 0.625rem 0.875rem; font-size: 0.875rem; background: var(--studio-card-bg); color: var(--neutral-900);">{{ old('deskripsi', $modul->deskripsi) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- 2. SKEMA KOLOM & ATRIBUT DATA --}}
                <div class="studio-card">
                    <div class="studio-card-header">
                        <div class="studio-card-title">
                            <span class="studio-step-num">2</span>
                            <div>
                                <h3>Skema Kolom &amp; Desain Formulir</h3>
                                <p>Sesuaikan atribut data yang telah terdaftar pada modul ini.</p>
                            </div>
                        </div>
                    </div>

                    {{-- QUICK-ADD TOOLBAR --}}
                    <div class="quick-add-toolbar">
                        <span style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-600); text-transform: uppercase;">Tambah Cepat:</span>
                        <button type="button" class="quick-chip" onclick="quickAddField('text', 'kolom', 'Data Baru')">
                            <span style="color: #2563EB;">T</span> + Teks
                        </button>
                        <button type="button" class="quick-chip" onclick="quickAddField('number', 'jumlah', 'Jumlah')">
                            <span style="color: #7C3AED;">#</span> + Angka
                        </button>
                        <button type="button" class="quick-chip" onclick="quickAddField('date', 'tanggal', 'Tanggal')">
                            <span style="color: #059669;">📅</span> + Tanggal
                        </button>
                        <button type="button" class="quick-chip" onclick="quickAddField('file', 'dokumen', 'Berkas Lampiran')">
                            <span style="color: #D97706;">📎</span> + Berkas PDF/Foto
                        </button>
                        <button type="button" class="quick-chip" onclick="quickAddField('select', 'status', 'Status Pilihan')">
                            <span style="color: #0891B2;">▼</span> + Dropdown
                        </button>
                    </div>

                    {{-- FIELDS CONTAINER --}}
                    <div id="fields-container" style="display: flex; flex-direction: column; gap: 0.875rem;">
                        @foreach($modul->fields as $idx => $f)
                            <div class="field-card-item" data-index="{{ $idx }}">
                                <div class="field-card-main">
                                    <span class="field-drag-handle">⋮⋮</span>
                                    <div>
                                        <label style="display: block; font-size: 0.72rem; font-weight: 700; color: var(--neutral-600); margin-bottom: 0.25rem;">Nama Kolom (snake_case) *</label>
                                        <input type="text" name="fields[{{ $idx }}][nama_kolom]" class="field-name-input" value="{{ $f->nama_kolom }}" required style="width: 100%; height: 38px; border: 1px solid var(--studio-border); border-radius: var(--studio-radius-sm); padding: 0 0.75rem; font-size: 0.8125rem; font-family: monospace; background: var(--studio-card-bg); color: var(--neutral-900);">
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 0.72rem; font-weight: 700; color: var(--neutral-600); margin-bottom: 0.25rem;">Label Tampilan *</label>
                                        <input type="text" name="fields[{{ $idx }}][label]" class="field-label-input" value="{{ $f->label }}" required style="width: 100%; height: 38px; border: 1px solid var(--studio-border); border-radius: var(--studio-radius-sm); padding: 0 0.75rem; font-size: 0.8125rem; background: var(--studio-card-bg); color: var(--neutral-900);">
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 0.72rem; font-weight: 700; color: var(--neutral-600); margin-bottom: 0.25rem;">Tipe Data *</label>
                                        <select name="fields[{{ $idx }}][tipe_data]" class="field-type-select" onchange="handleTypeChange(this)" required style="width: 100%; height: 38px; border: 1px solid var(--studio-border); border-radius: var(--studio-radius-sm); padding: 0 0.5rem; font-size: 0.8125rem; background: var(--studio-card-bg); color: var(--neutral-900);">
                                            <option value="text" {{ $f->tipe_data === 'text' ? 'selected' : '' }}>Teks (String)</option>
                                            <option value="number" {{ $f->tipe_data === 'number' ? 'selected' : '' }}>Angka (Number)</option>
                                            <option value="textarea" {{ $f->tipe_data === 'textarea' ? 'selected' : '' }}>Teks Panjang (Textarea)</option>
                                            <option value="select" {{ $f->tipe_data === 'select' ? 'selected' : '' }}>Dropdown Pilihan (Select)</option>
                                            <option value="multiselect" {{ $f->tipe_data === 'multiselect' ? 'selected' : '' }}>Multi Pilihan (Array)</option>
                                            <option value="date" {{ $f->tipe_data === 'date' ? 'selected' : '' }}>Tanggal (Date)</option>
                                            <option value="datetime" {{ $f->tipe_data === 'datetime' ? 'selected' : '' }}>Tanggal &amp; Waktu (DateTime)</option>
                                            <option value="file" {{ $f->tipe_data === 'file' ? 'selected' : '' }}>Unggah Berkas (PDF/Gambar)</option>
                                            <option value="boolean" {{ $f->tipe_data === 'boolean' ? 'selected' : '' }}>Switch Ya/Tidak (Boolean)</option>
                                            <option value="relation" {{ $f->tipe_data === 'relation' ? 'selected' : '' }}>Relasi Tabel MagKom</option>
                                        </select>
                                    </div>
                                    <div class="field-actions-btn">
                                        <button type="button" class="btn-icon-action btn-toggle-advanced" onclick="toggleAdvancedRow(this)" title="Pengaturan Lanjutan">⚙</button>
                                        <button type="button" class="btn-icon-action btn-icon-danger btn-remove-row" title="Hapus Kolom">✕</button>
                                    </div>
                                </div>

                                <div class="extra-options-wrapper" style="display: {{ in_array($f->tipe_data, ['select', 'multiselect', 'relation']) ? 'block' : 'none' }}; padding-top: 0.5rem; border-top: 1px dashed var(--studio-border);">
                                    <div class="select-options-box" style="display: {{ in_array($f->tipe_data, ['select', 'multiselect']) ? 'block' : 'none' }};">
                                        <label style="display: block; font-size: 0.72rem; font-weight: 700; color: var(--neutral-600); margin-bottom: 0.25rem;">Opsi Pilihan (Pisahkan dengan tanda koma):</label>
                                        <input type="text" name="fields[{{ $idx }}][opsi_pilihan]" value="{{ is_array($f->opsi_pilihan) ? implode(', ', $f->opsi_pilihan) : '' }}" style="width: 100%; height: 34px; border: 1px solid var(--studio-border); border-radius: 6px; padding: 0 0.75rem; font-size: 0.75rem; background: var(--studio-card-bg); color: var(--neutral-900);">
                                    </div>
                                    <div class="relation-options-box" style="display: {{ $f->tipe_data === 'relation' ? 'grid' : 'none' }}; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                        <div>
                                            <label style="display: block; font-size: 0.72rem; font-weight: 700; color: var(--neutral-600); margin-bottom: 0.25rem;">Tabel Target (Relasi):</label>
                                            <input type="text" name="fields[{{ $idx }}][relasi_tabel]" value="{{ $f->relasi_tabel }}" style="width: 100%; height: 34px; border: 1px solid var(--studio-border); border-radius: 6px; padding: 0 0.75rem; font-size: 0.75rem; background: var(--studio-card-bg); color: var(--neutral-900);">
                                        </div>
                                        <div>
                                            <label style="display: block; font-size: 0.72rem; font-weight: 700; color: var(--neutral-600); margin-bottom: 0.25rem;">Kolom Tampil (Label):</label>
                                            <input type="text" name="fields[{{ $idx }}][relasi_kolom_tampil]" value="{{ $f->relasi_kolom_tampil }}" style="width: 100%; height: 34px; border: 1px solid var(--studio-border); border-radius: 6px; padding: 0 0.75rem; font-size: 0.75rem; background: var(--studio-card-bg); color: var(--neutral-900);">
                                        </div>
                                    </div>
                                </div>

                                <div class="advanced-settings-drawer" style="display: none; padding-top: 0.5rem; border-top: 1px dashed var(--studio-border);">
                                    <div style="display: flex; gap: 1.5rem; flex-wrap: wrap;">
                                        <label style="display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.75rem; font-weight: 600; cursor: pointer;">
                                            <input type="checkbox" name="fields[{{ $idx }}][wajib]" value="1" {{ $f->wajib ? 'checked' : '' }}> Wajib Diisi (Required)
                                        </label>
                                        <label style="display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.75rem; font-weight: 600; cursor: pointer;">
                                            <input type="checkbox" name="fields[{{ $idx }}][tampil_di_tabel]" value="1" {{ $f->tampil_di_tabel ? 'checked' : '' }} onchange="updateLivePreview()"> Tampil di Tabel
                                        </label>
                                        <label style="display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.75rem; font-weight: 600; cursor: pointer;">
                                            <input type="checkbox" name="fields[{{ $idx }}][dapat_dicari]" value="1" {{ $f->dapat_dicari ? 'checked' : '' }}> Kolom Pencarian
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- 3. HAK AKSES, PRESET PERIZINAN & ISOLASI DATA --}}
                <div class="studio-card">
                    <div class="studio-card-header">
                        <div class="studio-card-title">
                            <span class="studio-step-num">3</span>
                            <div>
                                <h3>Hak Akses &amp; Tata Kelola Keamanan</h3>
                                <p>Atur visibilitas publik, isolasi data OPD, dan peran pengguna.</p>
                            </div>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
                        <div style="border: 1px solid var(--studio-border); border-radius: var(--studio-radius-md); padding: 1rem; background: var(--studio-row-bg);">
                            <label style="display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; font-weight: 700; cursor: pointer; margin-bottom: 0.35rem;">
                                <input type="checkbox" name="is_public_read" value="1" {{ old('is_public_read', $modul->is_public_read) ? 'checked' : '' }} onchange="updatePublicBadge(this)">
                                <span>Buka Akses Publik (Tanpa Login)</span>
                            </label>
                            <p style="font-size: 0.75rem; color: var(--neutral-600); margin: 0;">
                                Menyediakan katalog informasi terbuka di <code>/publik/modul/{{ $modul->slug }}</code>.
                            </p>
                        </div>

                        <div style="border: 1px solid var(--studio-border); border-radius: var(--studio-radius-md); padding: 1rem; background: var(--studio-row-bg);">
                            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase; margin-bottom: 0.35rem;">Isolasi Multi-Tenancy Data</label>
                            <select name="scope_mode" id="select-scope-mode" style="width: 100%; height: 38px; border: 1px solid var(--studio-border); border-radius: var(--studio-radius-sm); padding: 0 0.5rem; font-size: 0.8125rem; background: var(--studio-card-bg); color: var(--neutral-900);">
                                <option value="none" {{ old('scope_mode', $modul->scope_mode) === 'none' ? 'selected' : '' }}>Bebas (Semua role melihat seluruh baris)</option>
                                <option value="bidang_scope" {{ old('scope_mode', $modul->scope_mode) === 'bidang_scope' ? 'selected' : '' }}>Isolasi Bidang Dinas (Pembimbing hanya lihat bidangnya)</option>
                                <option value="creator_scope" {{ old('scope_mode', $modul->scope_mode) === 'creator_scope' ? 'selected' : '' }}>Isolasi Pembuat (Hanya lihat data yang diinput sendiri)</option>
                            </select>
                        </div>
                    </div>

                    {{-- PRESET PERAN PENGGUNA --}}
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase; margin-bottom: 0.5rem;">Peran Pengguna yang Diberi Akses *</label>
                        <div class="role-preset-grid">
                            <div class="role-pill-card">
                                <div class="role-pill-header">
                                    <strong>SuperAdmin Sleman</strong>
                                    <span style="font-size: 0.7rem; color: #059669; font-weight: 700;">Akses Penuh</span>
                                </div>
                                <span style="font-size: 0.75rem; color: var(--neutral-600);">Hak kontrol penuh, audit trail, dan konfigurasi.</span>
                                <input type="hidden" name="target_roles[]" value="superadmin">
                            </div>

                            <label class="role-pill-card" style="cursor: pointer;">
                                <div class="role-pill-header">
                                    <strong>Admin MagKom</strong>
                                    <input type="checkbox" name="target_roles[]" value="admin" {{ in_array('admin', (array)old('target_roles', $modul->target_roles ?? [])) ? 'checked' : '' }}>
                                </div>
                                <span style="font-size: 0.75rem; color: var(--neutral-600);">Dapat mengelola data, menambah, edit, dan ekspor.</span>
                            </label>

                            <label class="role-pill-card" style="cursor: pointer;">
                                <div class="role-pill-header">
                                    <strong>Pembimbing Dinas</strong>
                                    <input type="checkbox" name="target_roles[]" value="pembimbing" {{ in_array('pembimbing', (array)old('target_roles', $modul->target_roles ?? [])) ? 'checked' : '' }}>
                                </div>
                                <span style="font-size: 0.75rem; color: var(--neutral-600);">Akses operasional sesuai unit bidang kerja.</span>
                            </label>

                            <label class="role-pill-card" style="cursor: pointer;">
                                <div class="role-pill-header">
                                    <strong>Peserta Magang</strong>
                                    <input type="checkbox" name="target_roles[]" value="peserta" {{ in_array('peserta', (array)old('target_roles', $modul->target_roles ?? [])) ? 'checked' : '' }}>
                                </div>
                                <span style="font-size: 0.75rem; color: var(--neutral-600);">Akses terbatas melihat daftar dan detail modul.</span>
                            </label>
                        </div>
                    </div>

                    {{-- PRESET SUB-AKSI --}}
                    <div style="border-top: 1px dashed var(--studio-border); padding-top: 1rem;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                            <label style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase; margin: 0;">Matriks Izin Sub-Aksi &amp; Pencatatan Audit Log</label>
                            <button type="button" onclick="toggleDetailedMatrix()" id="btn-toggle-matrix" style="font-size: 0.75rem; color: var(--studio-primary); background: transparent; border: none; cursor: pointer; font-weight: 700;">
                                + Tampilkan Rincian Sub-Aksi
                            </button>
                        </div>

                        <div id="detailed-matrix-box" style="display: none; overflow-x: auto; margin-top: 0.75rem;">
                            <table class="mockup-table" style="background: var(--studio-card-bg);">
                                <thead>
                                    <tr>
                                        <th>Sub-Aksi Standar</th>
                                        <th style="text-align: center;">Admin MagKom</th>
                                        <th style="text-align: center;">Pembimbing</th>
                                        <th style="text-align: center;">Peserta</th>
                                        <th style="text-align: center; color: var(--studio-primary);">Catat Log Audit (log=1)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($standardActions as $actKey => $actLabel)
                                        @php
                                            $currIzin = (array)old("izin_aksi.$actKey", $modul->izin_aksi[$actKey] ?? []);
                                            $currLog  = old("log_aksi.$actKey", $modul->log_aksi[$actKey] ?? false);
                                        @endphp
                                        <tr>
                                            <td>
                                                <strong>{{ $actLabel }}</strong>
                                                <div style="font-size: 0.6875rem; color: var(--neutral-500); font-family: monospace;">{{ $actKey }}</div>
                                            </td>
                                            <td style="text-align: center;">
                                                <input type="checkbox" name="izin_aksi[{{ $actKey }}][]" value="admin" {{ in_array('admin', $currIzin) ? 'checked' : '' }}>
                                            </td>
                                            <td style="text-align: center;">
                                                <input type="checkbox" name="izin_aksi[{{ $actKey }}][]" value="pembimbing" {{ in_array('pembimbing', $currIzin) ? 'checked' : '' }}>
                                            </td>
                                            <td style="text-align: center;">
                                                <input type="checkbox" name="izin_aksi[{{ $actKey }}][]" value="peserta" {{ in_array('peserta', $currIzin) ? 'checked' : '' }}>
                                            </td>
                                            <td style="text-align: center;">
                                                <input type="checkbox" name="log_aksi[{{ $actKey }}]" value="1" {{ $currLog ? 'checked' : '' }}>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SISI KANAN: STICKY LIVE INTERACTIVE PREVIEW CANVAS --}}
            <div class="studio-preview-pane">
                <div class="preview-device-card">
                    <div class="preview-device-header">
                        <div class="preview-dots">
                            <span class="preview-dot dot-red"></span>
                            <span class="preview-dot dot-yellow"></span>
                            <span class="preview-dot dot-green"></span>
                        </div>
                        <div class="preview-tab-switch">
                            <button type="button" class="preview-tab-btn active" id="btn-tab-table" onclick="switchPreviewTab('table')">
                                Pratinjau Tabel
                            </button>
                            <button type="button" class="preview-tab-btn" id="btn-tab-form" onclick="switchPreviewTab('form')">
                                Pratinjau Formulir
                            </button>
                        </div>
                    </div>

                    <div class="preview-device-body">
                        <div id="preview-container-table">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
                                <div>
                                    <h4 id="preview-module-title" style="font-size: 0.9375rem; font-weight: 800; margin: 0; color: var(--neutral-900);">
                                        {{ old('nama_modul', $modul->nama_modul) }}
                                    </h4>
                                    <span id="preview-public-badge" style="{{ $modul->is_public_read ? 'display: inline-block;' : 'display: none;' }} font-size: 0.6875rem; background: rgba(16, 185, 129, 0.1); color: #059669; padding: 0.15rem 0.5rem; border-radius: 4px; font-weight: 700; margin-top: 0.2rem;">
                                        Publik Terbuka
                                    </span>
                                </div>
                                <div style="display: flex; gap: 0.35rem;">
                                    <span style="font-size: 0.7rem; background: var(--studio-primary); color: #FFFFFF; padding: 0.35rem 0.65rem; border-radius: 4px; font-weight: 700;">
                                        + Tambah Data
                                    </span>
                                </div>
                            </div>

                            <div style="background: var(--studio-row-bg); border: 1px solid var(--studio-border); border-radius: 6px; padding: 0.4rem 0.75rem; margin-bottom: 0.875rem; font-size: 0.75rem; color: var(--neutral-500); display: flex; align-items: center; gap: 0.4rem;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                <span>Cari data pada modul ini...</span>
                            </div>

                            <div style="overflow-x: auto;">
                                <table class="mockup-table">
                                    <thead>
                                        <tr id="preview-table-header">
                                            <th style="width: 36px;">#</th>
                                            <th>Nama Data</th>
                                            <th style="text-align: center; width: 60px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="preview-table-body">
                                        <tr>
                                            <td>1</td>
                                            <td>Contoh Baris Data 1</td>
                                            <td style="text-align: center; color: var(--studio-primary); font-weight: 700;">⋮</td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>Contoh Baris Data 2</td>
                                            <td style="text-align: center; color: var(--studio-primary); font-weight: 700;">⋮</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div id="preview-container-form" style="display: none;">
                            <div style="border-bottom: 1px solid var(--studio-border); padding-bottom: 0.75rem; margin-bottom: 1rem;">
                                <h4 style="font-size: 0.9375rem; font-weight: 800; margin: 0; color: var(--neutral-900);">
                                    Tambah Data: <span id="preview-form-title">{{ old('nama_modul', $modul->nama_modul) }}</span>
                                </h4>
                                <span style="font-size: 0.7rem; color: var(--neutral-500);">Simulasi formulir modal pop-up yang akan tampil bagi pengguna</span>
                            </div>

                            <div id="preview-form-fields-list">
                                <div class="mockup-form-item">
                                    <label>Nama Data</label>
                                    <input type="text" class="mockup-form-input" placeholder="Masukkan nama data...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="background: var(--studio-row-bg); border: 1px dashed var(--studio-border); border-radius: var(--studio-radius-md); padding: 1rem; font-size: 0.78125rem; color: var(--neutral-600); line-height: 1.5;">
                    💡 <strong>Tips Studio:</strong> Perubahan skema dan nama label pada modul ini akan langsung tercermin secara instan di tabel pratinjau.
                </div>
            </div>
        </div>
    </form>
</div>

{{-- MODAL VISUAL ICON PICKER --}}
<div class="icon-modal-backdrop" id="icon-modal-backdrop" onclick="handleBackdropClick(event)">
    <div class="icon-modal-card">
        <div class="icon-modal-header">
            <div>
                <h3>Pilih Ikon Visual Resmi Diskominfo</h3>
                <span style="font-size: 0.75rem; color: var(--neutral-600);">Pilih ikon vektor yang selaras dengan identitas portal MagKom Sleman</span>
            </div>
            <button type="button" class="btn-icon-action" onclick="closeIconModal()">✕</button>
        </div>

        <div class="icon-search-bar">
            <input type="text" id="icon-search-input" class="icon-search-input" placeholder="Ketik kata kunci untuk mencari ikon: laptop, database, dokumen, server..." oninput="filterIconGallery(this.value)">
        </div>

        <div class="icon-category-tabs">
            <button type="button" class="cat-tab-btn active" onclick="filterIconCategory('all', this)">Semua (42)</button>
            <button type="button" class="cat-tab-btn" onclick="filterIconCategory('ti', this)">💻 TI &amp; Jaringan</button>
            <button type="button" class="cat-tab-btn" onclick="filterIconCategory('dokumen', this)">📁 Data &amp; Dokumen</button>
            <button type="button" class="cat-tab-btn" onclick="filterIconCategory('tim', this)">👥 Manajemen &amp; Tim</button>
            <button type="button" class="cat-tab-btn" onclick="filterIconCategory('layanan', this)">⚙️ Layanan Publik</button>
            <button type="button" class="cat-tab-btn" onclick="filterIconCategory('aset', this)">📦 Inventaris &amp; Aset</button>
        </div>

        <div class="icon-gallery-grid" id="icon-gallery-grid">
            @php
                $categoryMap = [
                    'laptop' => 'ti', 'server' => 'ti', 'cpu' => 'ti', 'hard-drive' => 'ti', 'wifi' => 'ti',
                    'terminal' => 'ti', 'smartphone' => 'ti', 'monitor' => 'ti', 'printer' => 'ti', 'shield' => 'ti',
                    'database' => 'dokumen', 'table' => 'dokumen', 'file-text' => 'dokumen', 'folder' => 'dokumen',
                    'book' => 'dokumen', 'clipboard' => 'dokumen', 'archive' => 'dokumen', 'layers' => 'dokumen', 'file-plus' => 'dokumen',
                    'users' => 'tim', 'user-check' => 'tim', 'user-plus' => 'tim', 'briefcase' => 'tim', 'award' => 'tim',
                    'building' => 'tim', 'compass' => 'tim', 'flag' => 'tim',
                    'calendar' => 'layanan', 'clock' => 'layanan', 'check-circle' => 'layanan', 'activity' => 'layanan',
                    'bell' => 'layanan', 'mail' => 'layanan', 'phone' => 'layanan', 'map-pin' => 'layanan', 'tool' => 'layanan', 'sliders' => 'layanan',
                    'box' => 'aset', 'package' => 'aset', 'tag' => 'aset', 'credit-card' => 'aset', 'qr-code' => 'aset',
                ];
            @endphp

            @foreach($availableIcons as $iconKey => $iconLabel)
                <div class="icon-tile-item {{ old('ikon', $modul->ikon ?? 'laptop') === $iconKey ? 'selected' : '' }}" 
                     data-key="{{ $iconKey }}" 
                     data-label="{{ $iconLabel }}" 
                     data-cat="{{ $categoryMap[$iconKey] ?? 'ti' }}" 
                     onclick="selectIcon('{{ $iconKey }}', '{{ $iconLabel }}')">
                    {!! \App\Models\ModulDinamis::getSvgForIcon($iconKey, 24) !!}
                    <span>{{ explode('/', $iconLabel)[0] }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let fieldCounter = {{ count($modul->fields) }};

    // Sinkronisasi Nama Modul
    const nameInput = document.getElementById('input-nama-modul');
    if (nameInput) {
        nameInput.addEventListener('input', function() {
            const val = this.value.trim() || 'Modul Kustom';
            document.getElementById('header-title-preview').textContent = val;
            document.getElementById('preview-module-title').textContent = val;
            document.getElementById('preview-form-title').textContent = val;
        });
    }

    // Switch Preview Tab
    function switchPreviewTab(tab) {
        const btnTable = document.getElementById('btn-tab-table');
        const btnForm = document.getElementById('btn-tab-form');
        const containerTable = document.getElementById('preview-container-table');
        const containerForm = document.getElementById('preview-container-form');

        if (tab === 'table') {
            btnTable.classList.add('active');
            btnForm.classList.remove('active');
            containerTable.style.display = 'block';
            containerForm.style.display = 'none';
        } else {
            btnTable.classList.remove('active');
            btnForm.classList.add('active');
            containerTable.style.display = 'none';
            containerForm.style.display = 'block';
        }
    }

    // Modal Icon Picker Logic
    function openIconModal() {
        document.getElementById('icon-modal-backdrop').classList.add('open');
        document.getElementById('icon-search-input').focus();
    }

    function closeIconModal() {
        document.getElementById('icon-modal-backdrop').classList.remove('open');
    }

    function handleBackdropClick(e) {
        if (e.target.id === 'icon-modal-backdrop') {
            closeIconModal();
        }
    }

    function selectIcon(iconKey, iconLabel) {
        document.getElementById('input-ikon-val').value = iconKey;
        document.getElementById('widget-icon-name').textContent = iconLabel;
        document.getElementById('widget-icon-slug').textContent = 'Kata Kunci: ' + iconKey;

        document.querySelectorAll('.icon-tile-item').forEach(item => {
            item.classList.toggle('selected', item.dataset.key === iconKey);
        });

        const selectedTile = document.querySelector(`.icon-tile-item[data-key="${iconKey}"] svg`);
        if (selectedTile) {
            const svgHtml = selectedTile.outerHTML;
            document.getElementById('widget-icon-tile').innerHTML = svgHtml;
            document.getElementById('header-avatar-preview').innerHTML = svgHtml;
        }

        closeIconModal();
    }

    function filterIconGallery(query) {
        const q = query.toLowerCase().trim();
        document.querySelectorAll('.icon-tile-item').forEach(tile => {
            const key = tile.dataset.key.toLowerCase();
            const label = tile.dataset.label.toLowerCase();
            const match = key.includes(q) || label.includes(q);
            tile.style.display = match ? 'flex' : 'none';
        });
    }

    function filterIconCategory(cat, btn) {
        document.querySelectorAll('.cat-tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        document.querySelectorAll('.icon-tile-item').forEach(tile => {
            if (cat === 'all' || tile.dataset.cat === cat) {
                tile.style.display = 'flex';
            } else {
                tile.style.display = 'none';
            }
        });
    }

    function quickAddField(type, namePrefix, labelPrefix) {
        const index = fieldCounter++;
        const uniqueName = namePrefix + '_' + index;
        const uniqueLabel = labelPrefix + ' ' + index;

        const container = document.getElementById('fields-container');
        const div = document.createElement('div');
        div.className = 'field-card-item';
        div.dataset.index = index;

        div.innerHTML = `
            <div class="field-card-main">
                <span class="field-drag-handle">⋮⋮</span>
                <div>
                    <label style="display: block; font-size: 0.72rem; font-weight: 700; color: var(--neutral-600); margin-bottom: 0.25rem;">Nama Kolom (snake_case) *</label>
                    <input type="text" name="fields[${index}][nama_kolom]" class="field-name-input" value="${uniqueName}" required style="width: 100%; height: 38px; border: 1px solid var(--studio-border); border-radius: var(--studio-radius-sm); padding: 0 0.75rem; font-size: 0.8125rem; font-family: monospace; background: var(--studio-card-bg); color: var(--neutral-900);">
                </div>
                <div>
                    <label style="display: block; font-size: 0.72rem; font-weight: 700; color: var(--neutral-600); margin-bottom: 0.25rem;">Label Tampilan *</label>
                    <input type="text" name="fields[${index}][label]" class="field-label-input" value="${uniqueLabel}" required style="width: 100%; height: 38px; border: 1px solid var(--studio-border); border-radius: var(--studio-radius-sm); padding: 0 0.75rem; font-size: 0.8125rem; background: var(--studio-card-bg); color: var(--neutral-900);">
                </div>
                <div>
                    <label style="display: block; font-size: 0.72rem; font-weight: 700; color: var(--neutral-600); margin-bottom: 0.25rem;">Tipe Data *</label>
                    <select name="fields[${index}][tipe_data]" class="field-type-select" onchange="handleTypeChange(this)" required style="width: 100%; height: 38px; border: 1px solid var(--studio-border); border-radius: var(--studio-radius-sm); padding: 0 0.5rem; font-size: 0.8125rem; background: var(--studio-card-bg); color: var(--neutral-900);">
                        <option value="text" ${type === 'text' ? 'selected' : ''}>Teks (String)</option>
                        <option value="number" ${type === 'number' ? 'selected' : ''}>Angka (Number)</option>
                        <option value="textarea" ${type === 'textarea' ? 'selected' : ''}>Teks Panjang (Textarea)</option>
                        <option value="select" ${type === 'select' ? 'selected' : ''}>Dropdown Pilihan (Select)</option>
                        <option value="date" ${type === 'date' ? 'selected' : ''}>Tanggal (Date)</option>
                        <option value="datetime" ${type === 'datetime' ? 'selected' : ''}>Tanggal &amp; Waktu (DateTime)</option>
                        <option value="file" ${type === 'file' ? 'selected' : ''}>Unggah Berkas (PDF/Gambar)</option>
                        <option value="boolean" ${type === 'boolean' ? 'selected' : ''}>Switch Ya/Tidak (Boolean)</option>
                        <option value="relation" ${type === 'relation' ? 'selected' : ''}>Relasi Tabel MagKom</option>
                    </select>
                </div>
                <div class="field-actions-btn">
                    <button type="button" class="btn-icon-action btn-toggle-advanced" onclick="toggleAdvancedRow(this)">⚙</button>
                    <button type="button" class="btn-icon-action btn-icon-danger btn-remove-row">✕</button>
                </div>
            </div>

            <div class="extra-options-wrapper" style="display: ${type === 'select' || type === 'relation' ? 'block' : 'none'}; padding-top: 0.5rem; border-top: 1px dashed var(--studio-border);">
                <div class="select-options-box" style="display: ${type === 'select' ? 'block' : 'none'};">
                    <label style="display: block; font-size: 0.72rem; font-weight: 700; color: var(--neutral-600); margin-bottom: 0.25rem;">Opsi Pilihan (Pisahkan dengan tanda koma):</label>
                    <input type="text" name="fields[${index}][opsi_pilihan]" placeholder="Aktif, Menunggu, Selesai" style="width: 100%; height: 34px; border: 1px solid var(--studio-border); border-radius: 6px; padding: 0 0.75rem; font-size: 0.75rem; background: var(--studio-card-bg); color: var(--neutral-900);">
                </div>
                <div class="relation-options-box" style="display: ${type === 'relation' ? 'grid' : 'none'}; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <label style="display: block; font-size: 0.72rem; font-weight: 700; color: var(--neutral-600); margin-bottom: 0.25rem;">Tabel Target (Relasi):</label>
                        <input type="text" name="fields[${index}][relasi_tabel]" placeholder="users, bidangs" style="width: 100%; height: 34px; border: 1px solid var(--studio-border); border-radius: 6px; padding: 0 0.75rem; font-size: 0.75rem; background: var(--studio-card-bg); color: var(--neutral-900);">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.72rem; font-weight: 700; color: var(--neutral-600); margin-bottom: 0.25rem;">Kolom Tampil (Label):</label>
                        <input type="text" name="fields[${index}][relasi_kolom_tampil]" placeholder="nama, judul" style="width: 100%; height: 34px; border: 1px solid var(--studio-border); border-radius: 6px; padding: 0 0.75rem; font-size: 0.75rem; background: var(--studio-card-bg); color: var(--neutral-900);">
                    </div>
                </div>
            </div>

            <div class="advanced-settings-drawer" style="display: none; padding-top: 0.5rem; border-top: 1px dashed var(--studio-border);">
                <div style="display: flex; gap: 1.5rem; flex-wrap: wrap;">
                    <label style="display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.75rem; font-weight: 600; cursor: pointer;">
                        <input type="checkbox" name="fields[${index}][wajib]" value="1"> Wajib Diisi (Required)
                    </label>
                    <label style="display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.75rem; font-weight: 600; cursor: pointer;">
                        <input type="checkbox" name="fields[${index}][tampil_di_tabel]" value="1" checked onchange="updateLivePreview()"> Tampil di Tabel
                    </label>
                    <label style="display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.75rem; font-weight: 600; cursor: pointer;">
                        <input type="checkbox" name="fields[${index}][dapat_dicari]" value="1" checked> Kolom Pencarian
                    </label>
                </div>
            </div>
        `;

        container.appendChild(div);
        bindFieldEvents(div);
        updateLivePreview();
    }

    function toggleAdvancedRow(btn) {
        const card = btn.closest('.field-card-item');
        const drawer = card.querySelector('.advanced-settings-drawer');
        drawer.style.display = (drawer.style.display === 'none' || drawer.style.display === '') ? 'block' : 'none';
    }

    function handleTypeChange(select) {
        const card = select.closest('.field-card-item');
        const extraWrapper = card.querySelector('.extra-options-wrapper');
        const selectBox = card.querySelector('.select-options-box');
        const relBox = card.querySelector('.relation-options-box');

        const val = select.value;
        if (val === 'select' || val === 'multiselect') {
            extraWrapper.style.display = 'block';
            selectBox.style.display = 'block';
            relBox.style.display = 'none';
        } else if (val === 'relation') {
            extraWrapper.style.display = 'block';
            selectBox.style.display = 'none';
            relBox.style.display = 'grid';
        } else {
            extraWrapper.style.display = 'none';
            selectBox.style.display = 'none';
            relBox.style.display = 'none';
        }

        updateLivePreview();
    }

    function toggleDetailedMatrix() {
        const box = document.getElementById('detailed-matrix-box');
        const btn = document.getElementById('btn-toggle-matrix');
        if (box.style.display === 'none' || box.style.display === '') {
            box.style.display = 'block';
            btn.textContent = '▲ Tutup Rincian Sub-Aksi';
        } else {
            box.style.display = 'none';
            btn.textContent = '+ Tampilkan Rincian Sub-Aksi';
        }
    }

    function updatePublicBadge(checkbox) {
        const badge = document.getElementById('preview-public-badge');
        if (badge) {
            badge.style.display = checkbox.checked ? 'inline-block' : 'none';
        }
    }

    function bindFieldEvents(container) {
        const removeBtn = container.querySelector('.btn-remove-row');
        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                const total = document.querySelectorAll('.field-card-item').length;
                if (total <= 1) {
                    alert('Modul wajib memiliki minimal 1 kolom data.');
                    return;
                }
                container.remove();
                updateLivePreview();
            });
        }

        const labelInput = container.querySelector('.field-label-input');
        if (labelInput) {
            labelInput.addEventListener('input', updateLivePreview);
        }
    }

    function updateLivePreview() {
        const headerRow = document.getElementById('preview-table-header');
        const bodyRows = document.getElementById('preview-table-body');
        const formFieldsList = document.getElementById('preview-form-fields-list');

        let headersHtml = '<th style="width: 36px;">#</th>';
        let bodySample1 = '<td>1</td>';
        let bodySample2 = '<td>2</td>';
        let formFieldsHtml = '';

        const fieldCards = document.querySelectorAll('.field-card-item');
        fieldCards.forEach((card, idx) => {
            const labelInput = card.querySelector('.field-label-input');
            const typeSelect = card.querySelector('.field-type-select');
            const showInTableCheck = card.querySelector('input[name*="[tampil_di_tabel]"]');

            const label = (labelInput && labelInput.value.trim()) ? labelInput.value.trim() : `Kolom ${idx + 1}`;
            const type = typeSelect ? typeSelect.value : 'text';
            const showInTable = showInTableCheck ? showInTableCheck.checked : true;

            if (showInTable) {
                headersHtml += `<th>${escapeHtml(label)}</th>`;

                if (type === 'number') {
                    bodySample1 += `<td>${(idx + 1) * 12} unit</td>`;
                    bodySample2 += `<td>${(idx + 2) * 5} unit</td>`;
                } else if (type === 'date' || type === 'datetime') {
                    bodySample1 += `<td>16 Okt 2026</td>`;
                    bodySample2 += `<td>14 Okt 2026</td>`;
                } else if (type === 'file') {
                    bodySample1 += `<td><span style="color:#0751FF;">📄 berkas-${idx + 1}.pdf</span></td>`;
                    bodySample2 += `<td><span style="color:#0751FF;">📄 lampiran.png</span></td>`;
                } else if (type === 'boolean') {
                    bodySample1 += `<td><span style="color:#059669; font-weight:700;">✓ Ya</span></td>`;
                    bodySample2 += `<td><span style="color:#DC2626;">✕ Tidak</span></td>`;
                } else {
                    bodySample1 += `<td>Data ${escapeHtml(label)} 1</td>`;
                    bodySample2 += `<td>Data ${escapeHtml(label)} 2</td>`;
                }
            }

            formFieldsHtml += `
                <div class="mockup-form-item">
                    <label>${escapeHtml(label)}</label>
                    <input type="text" class="mockup-form-input" placeholder="Masukkan ${escapeHtml(label)}...">
                </div>
            `;
        });

        headersHtml += '<th style="text-align: center; width: 60px;">Aksi</th>';
        bodySample1 += '<td style="text-align: center; color: var(--studio-primary); font-weight: 700;">⋮</td>';
        bodySample2 += '<td style="text-align: center; color: var(--studio-primary); font-weight: 700;">⋮</td>';

        if (headerRow) headerRow.innerHTML = headersHtml;
        if (bodyRows) bodyRows.innerHTML = `<tr>${bodySample1}</tr><tr>${bodySample2}</tr>`;
        if (formFieldsList) formFieldsList.innerHTML = formFieldsHtml;
    }

    function escapeHtml(str) {
        return str.replace(/[&<>'"]/g, tag => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[tag] || tag));
    }

    document.querySelectorAll('.field-card-item').forEach(bindFieldEvents);
    updateLivePreview();
</script>
@endpush
