@extends('layouts.dashboard')

@section('title', 'Edit Skema Modul: ' . $modul->nama_modul . ' - SuperAdmin MagKom')
@section('page_title', 'Perbarui Skema Modul Kustom')
@section('page_description', 'Sesuaikan metadata, konfigurasi basis data, otorisasi sub-aksi, dan skema kolom untuk modul ' . $modul->nama_modul . '.')

@section('breadcrumbs')
    <a href="{{ route('superadmin.dashboard') }}">SuperAdmin</a>
    <span class="breadcrumb-separator">/</span>
    <a href="{{ route('superadmin.modul-builder.index') }}">Pembuat Modul</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Edit: {{ $modul->nama_modul }}</span>
@endsection

@push('styles')
<style>
    .builder-card {
        background: var(--neutral-0);
        border: 1px solid var(--neutral-300);
        border-radius: 14px;
        padding: 1.5rem;
        box-shadow: var(--shadow-subtle);
        margin-bottom: 1.5rem;
    }

    .field-row-card {
        background: var(--neutral-100);
        border: 1px solid var(--neutral-300);
        border-radius: 10px;
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        position: relative;
        transition: all 0.15s ease;
    }

    .field-row-card:hover {
        border-color: var(--blue-400);
    }

    [data-theme="dark"] .builder-card {
        background-color: #182234 !important;
        border-color: #26354D !important;
    }

    [data-theme="dark"] .field-row-card {
        background-color: #111C2E !important;
        border-color: #26354D !important;
    }

    [data-theme="dark"] input[type="text"],
    [data-theme="dark"] textarea,
    [data-theme="dark"] select {
        background-color: #0F172A !important;
        color: #F8FAFC !important;
        border-color: #26354D !important;
        color-scheme: dark;
    }

    [data-theme="dark"] h1, [data-theme="dark"] h2, [data-theme="dark"] h3, [data-theme="dark"] strong {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] label {
        color: #CBD5E1 !important;
    }

    .matrix-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.8125rem;
    }
    .matrix-table th, .matrix-table td {
        padding: 0.65rem 0.75rem;
        border: 1px solid var(--neutral-300);
        text-align: center;
    }
    .matrix-table th:first-child, .matrix-table td:first-child {
        text-align: left;
    }
    [data-theme="dark"] .matrix-table th, [data-theme="dark"] .matrix-table td {
        border-color: #26354D !important;
    }
</style>
@endpush

@section('content')
<div style="max-width: 1080px; margin: 0 auto; width: 100%;">

    @if ($errors->any())
        <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #DC2626; padding: 1rem 1.25rem; border-radius: 10px; margin-bottom: 1.5rem; font-size: 0.875rem;">
            <strong style="display: block; margin-bottom: 0.35rem;">Terdapat kesalahan pada formulir modul:</strong>
            <ul style="margin: 0; padding-left: 1.25rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('superadmin.modul-builder.update', $modul) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="tipe_sumber" value="{{ $modul->tipe_sumber }}">
        <input type="hidden" name="nama_tabel_fisik" value="{{ $modul->nama_tabel_fisik }}">

        {{-- 1. INFORMASI UTAMA MODUL --}}
        <div class="builder-card">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.875rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(7, 81, 255, 0.1); color: var(--blue-700); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.875rem;">
                        1
                    </div>
                    <div>
                        <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Informasi Utama Modul</h3>
                        <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0.15rem 0 0 0;">Slug URL modul: <code>{{ $modul->slug }}</code></p>
                    </div>
                </div>

                <div>
                    @if($modul->tipe_sumber === 'physical_table')
                        <span style="font-size: 0.75rem; background: rgba(16, 185, 129, 0.1); color: #059669; padding: 0.35rem 0.65rem; border-radius: 6px; font-weight: 700;">
                            Mode Basis Data: {{ $modul->nama_tabel_fisik }}
                        </span>
                    @else
                        <span style="font-size: 0.75rem; background: rgba(7, 81, 255, 0.1); color: var(--blue-700); padding: 0.35rem 0.65rem; border-radius: 6px; font-weight: 700;">
                            Mode Dynamic JSON Schema
                        </span>
                    @endif
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase; margin-bottom: 0.35rem;">Nama Modul *</label>
                    <input type="text" name="nama_modul" value="{{ old('nama_modul', $modul->nama_modul) }}" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase; margin-bottom: 0.35rem;">Ikon Visual *</label>
                    <select name="ikon" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: var(--neutral-0);">
                        @foreach($availableIcons as $key => $label)
                            <option value="{{ $key }}" {{ old('ikon', $modul->ikon) === $key ? 'selected' : '' }}>
                                {{ $label }} ({{ $key }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase; margin-bottom: 0.35rem;">Kolom Primary Key *</label>
                    <input type="text" name="primary_key" value="{{ old('primary_key', $modul->primary_key ?? 'id') }}" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: monospace;">
                </div>
                <div style="display: flex; align-items: center; padding-top: 1.25rem;">
                    <label style="display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; font-weight: 600; cursor: pointer;">
                        <input type="checkbox" name="is_manual_pk" value="1" {{ old('is_manual_pk', $modul->is_manual_pk) ? 'checked' : '' }}>
                        <span>Primary Key Manual (Non Auto-Increment)</span>
                    </label>
                </div>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase; margin-bottom: 0.35rem;">Deskripsi Modul</label>
                <textarea name="deskripsi" rows="2" style="width: 100%; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0.625rem 0.875rem; font-size: 0.875rem;">{{ old('deskripsi', $modul->deskripsi) }}</textarea>
            </div>
        </div>

        {{-- 2. SKEMA KOLOM & ATRIBUT DATA --}}
        <div class="builder-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.875rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(7, 81, 255, 0.1); color: var(--blue-700); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.875rem;">
                        2
                    </div>
                    <div>
                        <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Daftar Kolom &amp; Atribut Data</h3>
                        <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0.15rem 0 0 0;">Sesuaikan kolom atribut formulir yang digunakan.</p>
                    </div>
                </div>

                <button type="button" id="btn-add-field" style="height: 38px; padding: 0 1rem; background: var(--blue-700); color: #FFFFFF; font-weight: 700; font-size: 0.8125rem; border: none; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    <span>+ Tambah Kolom</span>
                </button>
            </div>

            <div id="fields-container" style="display: flex; flex-direction: column; gap: 1rem;">
                @foreach($modul->fields as $idx => $f)
                    <div class="field-row-card" data-index="{{ $idx }}">
                        <div style="display: grid; grid-template-columns: 2fr 2fr 1.5fr auto; gap: 0.75rem; align-items: flex-end;">
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); margin-bottom: 0.35rem;">Nama Kolom (snake_case) *</label>
                                <input type="text" name="fields[{{ $idx }}][nama_kolom]" value="{{ $f->nama_kolom }}" required style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.8125rem; font-family: monospace;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); margin-bottom: 0.35rem;">Label Tampilan *</label>
                                <input type="text" name="fields[{{ $idx }}][label]" value="{{ $f->label }}" required style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.8125rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); margin-bottom: 0.35rem;">Tipe Data *</label>
                                <select name="fields[{{ $idx }}][tipe_data]" onchange="toggleExtraOptions(this)" required style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.5rem; font-size: 0.8125rem; background: var(--neutral-0);">
                                    <option value="text" {{ $f->tipe_data === 'text' ? 'selected' : '' }}>Teks (String)</option>
                                    <option value="number" {{ $f->tipe_data === 'number' ? 'selected' : '' }}>Angka (Number)</option>
                                    <option value="textarea" {{ $f->tipe_data === 'textarea' ? 'selected' : '' }}>Teks Panjang (Textarea)</option>
                                    <option value="select" {{ $f->tipe_data === 'select' ? 'selected' : '' }}>Dropdown Pilihan (Select)</option>
                                    <option value="multiselect" {{ $f->tipe_data === 'multiselect' ? 'selected' : '' }}>Multi Pilihan (Array)</option>
                                    <option value="date" {{ $f->tipe_data === 'date' ? 'selected' : '' }}>Tanggal (Date)</option>
                                    <option value="datetime" {{ $f->tipe_data === 'datetime' ? 'selected' : '' }}>Tanggal &amp; Waktu (DateTime)</option>
                                    <option value="file" {{ $f->tipe_data === 'file' ? 'selected' : '' }}>Unggah Berkas (PDF/Gambar/Doc)</option>
                                    <option value="boolean" {{ $f->tipe_data === 'boolean' ? 'selected' : '' }}>Switch Ya/Tidak (Boolean)</option>
                                    <option value="relation" {{ $f->tipe_data === 'relation' ? 'selected' : '' }}>Relasi Tabel MagKom (Lookup)</option>
                                </select>
                            </div>
                            <div style="padding-bottom: 0.25rem;">
                                <button type="button" class="btn-remove-row" style="height: 38px; width: 38px; border-radius: 6px; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.25); color: #DC2626; cursor: pointer; display: flex; align-items: center; justify-content: center;" title="Hapus Kolom">
                                    ✕
                                </button>
                            </div>
                        </div>

                        <!-- Extra Options -->
                        <div class="extra-options" style="display: {{ in_array($f->tipe_data, ['select', 'multiselect', 'relation']) ? 'block' : 'none' }}; padding-top: 0.5rem; border-top: 1px dashed var(--neutral-300);">
                            <div class="select-options-box" style="display: {{ in_array($f->tipe_data, ['select', 'multiselect']) ? 'block' : 'none' }};">
                                <label style="display: block; font-size: 0.72rem; font-weight: 700; color: var(--neutral-600); margin-bottom: 0.25rem;">Opsi Pilihan (Pisahkan dengan tanda koma):</label>
                                <input type="text" name="fields[{{ $idx }}][opsi_pilihan]" value="{{ is_array($f->opsi_pilihan) ? implode(', ', $f->opsi_pilihan) : '' }}" style="width: 100%; height: 34px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.75rem;">
                            </div>
                            <div class="relation-options-box" style="display: {{ $f->tipe_data === 'relation' ? 'grid' : 'none' }}; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                <div>
                                    <label style="display: block; font-size: 0.72rem; font-weight: 700; color: var(--neutral-600); margin-bottom: 0.25rem;">Tabel Target (Relasi):</label>
                                    <input type="text" name="fields[{{ $idx }}][relasi_tabel]" value="{{ $f->relasi_tabel }}" style="width: 100%; height: 34px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.75rem;">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.72rem; font-weight: 700; color: var(--neutral-600); margin-bottom: 0.25rem;">Kolom Tampil (Label):</label>
                                    <input type="text" name="fields[{{ $idx }}][relasi_kolom_tampil]" value="{{ $f->relasi_kolom_tampil }}" style="width: 100%; height: 34px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.75rem;">
                                </div>
                            </div>
                        </div>

                        <!-- Checkboxes -->
                        <div style="display: flex; gap: 1.5rem; flex-wrap: wrap; padding-top: 0.25rem;">
                            <label style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.75rem; font-weight: 600; cursor: pointer;">
                                <input type="checkbox" name="fields[{{ $idx }}][wajib]" value="1" {{ $f->wajib ? 'checked' : '' }}> Wajib Diisi (Required)
                            </label>
                            <label style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.75rem; font-weight: 600; cursor: pointer;">
                                <input type="checkbox" name="fields[{{ $idx }}][tampil_di_tabel]" value="1" {{ $f->tampil_di_tabel ? 'checked' : '' }}> Tampil di Tabel
                            </label>
                            <label style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.75rem; font-weight: 600; cursor: pointer;">
                                <input type="checkbox" name="fields[{{ $idx }}][dapat_dicari]" value="1" {{ $f->dapat_dicari ? 'checked' : '' }}> Dapat Dicari (Searchable)
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- 3. HAK AKSES, FITUR KHUSUS & MATRIKS SUB-AKSI --}}
        <div class="builder-card">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.875rem;">
                <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(7, 81, 255, 0.1); color: var(--blue-700); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.875rem;">
                    3
                </div>
                <div>
                    <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Otorisasi Hak Akses, Fitur Khusus &amp; Matriks Sub-Aksi</h3>
                    <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0.15rem 0 0 0;">Pengaturan akses publik, multi-tenancy dinas, dan matriks izin aksi.</p>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div style="border: 1px solid var(--neutral-300); border-radius: 10px; padding: 1rem;">
                    <label style="display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; font-weight: 700; cursor: pointer; margin-bottom: 0.35rem;">
                        <input type="checkbox" name="is_public_read" value="1" {{ old('is_public_read', $modul->is_public_read) ? 'checked' : '' }}>
                        <span>Endpoint Publik Read-Only (ckPublicRead)</span>
                    </label>
                    <p style="font-size: 0.75rem; color: var(--neutral-600); margin: 0;">
                        Menyediakan URL publik tanpa login di <code>/publik/modul/{{ $modul->slug }}</code>.
                    </p>
                </div>

                <div style="border: 1px solid var(--neutral-300); border-radius: 10px; padding: 1rem;">
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase; margin-bottom: 0.35rem;">Isolasi Multi-Tenancy (Unit Scope)</label>
                    <select name="scope_mode" style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.5rem; font-size: 0.8125rem; background: var(--neutral-0);">
                        <option value="none" {{ old('scope_mode', $modul->scope_mode) === 'none' ? 'selected' : '' }}>None (Semua role berhak melihat seluruh data)</option>
                        <option value="bidang_scope" {{ old('scope_mode', $modul->scope_mode) === 'bidang_scope' ? 'selected' : '' }}>Isolasi Bidang Dinas (Pembimbing hanya melihat data bidangnya)</option>
                        <option value="creator_scope" {{ old('scope_mode', $modul->scope_mode) === 'creator_scope' ? 'selected' : '' }}>Isolasi Pembuat (Pengguna hanya melihat baris buatannya)</option>
                    </select>
                </div>
            </div>

            @php
                $targetRoles = (array) ($modul->target_roles ?? []);
                $izinAksi = (array) ($modul->izin_aksi ?? []);
                $logAksi = (array) ($modul->log_aksi ?? []);
            @endphp

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase; margin-bottom: 0.5rem;">Target Role Pengguna (Makro) *</label>
                <div style="display: flex; gap: 1.5rem; flex-wrap: wrap;">
                    <label style="display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.8125rem; font-weight: 600;">
                        <input type="checkbox" checked disabled> SuperAdmin (Wajib Penuh)
                        <input type="hidden" name="target_roles[]" value="superadmin">
                    </label>
                    <label style="display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.8125rem; font-weight: 600;">
                        <input type="checkbox" name="target_roles[]" value="admin" {{ in_array('admin', $targetRoles) ? 'checked' : '' }}> Admin MagKom
                    </label>
                    <label style="display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.8125rem; font-weight: 600;">
                        <input type="checkbox" name="target_roles[]" value="pembimbing" {{ in_array('pembimbing', $targetRoles) ? 'checked' : '' }}> Pembimbing Dinas
                    </label>
                    <label style="display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.8125rem; font-weight: 600;">
                        <input type="checkbox" name="target_roles[]" value="peserta" {{ in_array('peserta', $targetRoles) ? 'checked' : '' }}> Peserta Magang
                    </label>
                </div>
            </div>

            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase; margin-bottom: 0.5rem;">Matriks Izin Sub-Aksi &amp; Perekaman Audit Log (CodeModule)</label>
                <div style="overflow-x: auto;">
                    <table class="matrix-table">
                        <thead>
                            <tr style="background: var(--neutral-100);">
                                <th>Sub-Aksi Standar</th>
                                <th>Admin MagKom</th>
                                <th>Pembimbing</th>
                                <th>Peserta</th>
                                <th style="color: var(--blue-700);">Catat Log Audit (log=1)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($standardActions as $actKey => $actLabel)
                                @php
                                    $rolesWithAction = (array) ($izinAksi[$actKey] ?? []);
                                    $isLogged = !empty($logAksi[$actKey]) || (empty($logAksi) && in_array($actKey, ['create', 'edit', 'delete', 'export_csv', 'export_pdf']));
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $actLabel }}</strong>
                                        <div style="font-size: 0.7rem; color: var(--neutral-500); font-family: monospace;">{{ $actKey }}</div>
                                    </td>
                                    <td>
                                        <input type="checkbox" name="izin_aksi[{{ $actKey }}][]" value="admin" {{ in_array('admin', $rolesWithAction) || empty($izinAksi) ? 'checked' : '' }}>
                                    </td>
                                    <td>
                                        <input type="checkbox" name="izin_aksi[{{ $actKey }}][]" value="pembimbing" {{ in_array('pembimbing', $rolesWithAction) || (empty($izinAksi) && in_array($actKey, ['list', 'show', 'print', 'export_pdf'])) ? 'checked' : '' }}>
                                    </td>
                                    <td>
                                        <input type="checkbox" name="izin_aksi[{{ $actKey }}][]" value="peserta" {{ in_array('peserta', $rolesWithAction) || (empty($izinAksi) && in_array($actKey, ['list', 'show'])) ? 'checked' : '' }}>
                                    </td>
                                    <td>
                                        <input type="checkbox" name="log_aksi[{{ $actKey }}]" value="1" {{ $isLogged ? 'checked' : '' }}>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- TOMBOL AKSI SIMPAN --}}
        <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-bottom: 2rem;">
            <a href="{{ route('superadmin.modul-builder.index') }}" style="height: 42px; padding: 0 1.25rem; border: 1px solid var(--neutral-400); border-radius: 8px; font-size: 0.875rem; font-weight: 700; color: var(--neutral-700); background: var(--neutral-0); display: inline-flex; align-items: center; text-decoration: none;">
                Batal
            </a>
            <button type="submit" style="height: 42px; padding: 0 1.75rem; border: none; border-radius: 8px; font-size: 0.875rem; font-weight: 800; color: #FFFFFF; background: var(--blue-700); cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem; box-shadow: var(--shadow-subtle);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                <span>Simpan Perubahan Skema</span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    let fieldIndex = {{ $modul->fields->count() }};

    function toggleExtraOptions(selectElem) {
        const row = selectElem.closest('.field-row-card');
        const extraBox = row.querySelector('.extra-options');
        const selectBox = row.querySelector('.select-options-box');
        const relationBox = row.querySelector('.relation-options-box');

        const val = selectElem.value;
        if (val === 'select' || val === 'multiselect') {
            extraBox.style.display = 'block';
            selectBox.style.display = 'block';
            relationBox.style.display = 'none';
        } else if (val === 'relation') {
            extraBox.style.display = 'block';
            selectBox.style.display = 'none';
            relationBox.style.display = 'grid';
        } else {
            extraBox.style.display = 'none';
            selectBox.style.display = 'none';
            relationBox.style.display = 'none';
        }
    }

    document.getElementById('btn-add-field').addEventListener('click', function() {
        const container = document.getElementById('fields-container');
        const idx = fieldIndex;

        const row = document.createElement('div');
        row.className = 'field-row-card';
        row.setAttribute('data-index', idx);

        row.innerHTML = `
            <div style="display: grid; grid-template-columns: 2fr 2fr 1.5fr auto; gap: 0.75rem; align-items: flex-end;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); margin-bottom: 0.35rem;">Nama Kolom (snake_case) *</label>
                    <input type="text" name="fields[${idx}][nama_kolom]" placeholder="nama_kolom" required style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.8125rem; font-family: monospace;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); margin-bottom: 0.35rem;">Label Tampilan *</label>
                    <input type="text" name="fields[${idx}][label]" placeholder="Label Tampilan" required style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.8125rem;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); margin-bottom: 0.35rem;">Tipe Data *</label>
                    <select name="fields[${idx}][tipe_data]" onchange="toggleExtraOptions(this)" required style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.5rem; font-size: 0.8125rem; background: var(--neutral-0);">
                        <option value="text">Teks (String)</option>
                        <option value="number">Angka (Number)</option>
                        <option value="textarea">Teks Panjang (Textarea)</option>
                        <option value="select">Dropdown Pilihan (Select)</option>
                        <option value="multiselect">Multi Pilihan (Array)</option>
                        <option value="date">Tanggal (Date)</option>
                        <option value="datetime">Tanggal &amp; Waktu (DateTime)</option>
                        <option value="file">Unggah Berkas (PDF/Gambar/Doc)</option>
                        <option value="boolean">Switch Ya/Tidak (Boolean)</option>
                        <option value="relation">Relasi Tabel MagKom (Lookup)</option>
                    </select>
                </div>
                <div style="padding-bottom: 0.25rem;">
                    <button type="button" class="btn-remove-row" style="height: 38px; width: 38px; border-radius: 6px; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.25); color: #DC2626; cursor: pointer; display: flex; align-items: center; justify-content: center;" title="Hapus Kolom">
                        ✕
                    </button>
                </div>
            </div>

            <div class="extra-options" style="display: none; padding-top: 0.5rem; border-top: 1px dashed var(--neutral-300);">
                <div class="select-options-box" style="display: none;">
                    <label style="display: block; font-size: 0.72rem; font-weight: 700; color: var(--neutral-600); margin-bottom: 0.25rem;">Opsi Pilihan (Pisahkan dengan tanda koma):</label>
                    <input type="text" name="fields[${idx}][opsi_pilihan]" placeholder="Pilihan 1, Pilihan 2, Pilihan 3" style="width: 100%; height: 34px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.75rem;">
                </div>
                <div class="relation-options-box" style="display: none; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <label style="display: block; font-size: 0.72rem; font-weight: 700; color: var(--neutral-600); margin-bottom: 0.25rem;">Tabel Target (Relasi):</label>
                        <input type="text" name="fields[${idx}][relasi_tabel]" placeholder="users, bidangs, jurusans" style="width: 100%; height: 34px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.75rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.72rem; font-weight: 700; color: var(--neutral-600); margin-bottom: 0.25rem;">Kolom Tampil (Label):</label>
                        <input type="text" name="fields[${idx}][relasi_kolom_tampil]" placeholder="nama, judul" style="width: 100%; height: 34px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.75rem;">
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 1.5rem; flex-wrap: wrap; padding-top: 0.25rem;">
                <label style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.75rem; font-weight: 600; cursor: pointer;">
                    <input type="checkbox" name="fields[${idx}][wajib]" value="1"> Wajib Diisi (Required)
                </label>
                <label style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.75rem; font-weight: 600; cursor: pointer;">
                    <input type="checkbox" name="fields[${idx}][tampil_di_tabel]" value="1" checked> Tampil di Tabel
                </label>
                <label style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.75rem; font-weight: 600; cursor: pointer;">
                    <input type="checkbox" name="fields[${idx}][dapat_dicari]" value="1" checked> Dapat Dicari (Searchable)
                </label>
            </div>
        `;

        container.appendChild(row);
        fieldIndex++;
    });

    document.getElementById('fields-container').addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-row')) {
            const row = e.target.closest('.field-row-card');
            const totalRows = document.querySelectorAll('.field-row-card').length;
            if (totalRows <= 1) {
                alert('Modul harus memiliki setidaknya satu kolom.');
                return;
            }
            row.remove();
        }
    });
</script>
@endpush
