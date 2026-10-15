@extends('layouts.dashboard')

@section('title', 'Ciptakan Modul Kustom Baru - SuperAdmin MagKom')
@section('page_title', 'Rancang Modul Kustom Baru (Visual Builder & Reverse-Engineering)')
@section('page_description', 'Definisikan skema kolom secara bebas (No-Code) atau impor skema langsung dari tabel basis data riil (Basefw Parity).')

@section('breadcrumbs')
    <a href="{{ route('superadmin.dashboard') }}">SuperAdmin</a>
    <span class="breadcrumb-separator">/</span>
    <a href="{{ route('superadmin.modul-builder.index') }}">Pembuat Modul</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Baru</span>
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

    .mode-tab-btn {
        flex: 1;
        padding: 0.875rem 1rem;
        border: 2px solid var(--neutral-300);
        border-radius: 10px;
        background: var(--neutral-0);
        cursor: pointer;
        font-weight: 700;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }

    .mode-tab-btn.active {
        border-color: var(--blue-700);
        background: rgba(7, 81, 255, 0.05);
        color: var(--blue-700);
    }

    [data-theme="dark"] .builder-card {
        background-color: #182234 !important;
        border-color: #26354D !important;
    }

    [data-theme="dark"] .field-row-card {
        background-color: #111C2E !important;
        border-color: #26354D !important;
    }

    [data-theme="dark"] .mode-tab-btn {
        background-color: #111C2E !important;
        border-color: #26354D !important;
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .mode-tab-btn.active {
        border-color: #38BDF8 !important;
        background-color: rgba(56, 189, 248, 0.1) !important;
        color: #38BDF8 !important;
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

    {{-- PEMILIH MODE GENERATOR --}}
    <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
        <button type="button" class="mode-tab-btn active" id="btn-mode-dynamic" onclick="switchBuilderMode('dynamic')">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            <span>Mode 1: Formulir Dinamis Bebas (No-Code Form)</span>
        </button>
        <button type="button" class="mode-tab-btn" id="btn-mode-table" onclick="switchBuilderMode('physical_table')">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
            <span>Mode 2: Impor Tabel Basis Data (Reverse-Engineering Basefw)</span>
        </button>
    </div>

    <form action="{{ route('superadmin.modul-builder.store') }}" method="POST" id="form-builder">
        @csrf
        <input type="hidden" name="tipe_sumber" id="input-tipe-sumber" value="{{ old('tipe_sumber', 'dynamic') }}">

        {{-- PANEL REVERSE-ENGINEERING BASIS DATA (Khusus Mode 2) --}}
        <div class="builder-card" id="panel-reverse-engineering" style="display: none; border-left: 4px solid var(--blue-700);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                <div>
                    <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Pilih Tabel Basis Data Riil</h3>
                    <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0.15rem 0 0 0;">Sistem akan menginspeksi skema SQL, membaca kolom, dan memetakannya otomatis ke formulir.</p>
                </div>
                <span style="font-size: 0.75rem; background: rgba(7, 81, 255, 0.1); color: var(--blue-700); padding: 0.25rem 0.5rem; border-radius: 6px; font-weight: 700;">
                    Basefw CrudGenerator Parity
                </span>
            </div>

            <div style="display: grid; grid-template-columns: 2fr auto; gap: 0.75rem; align-items: flex-end;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase; margin-bottom: 0.35rem;">Pilih Tabel Target *</label>
                    <select name="nama_tabel_fisik" id="select-tabel-fisik" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: var(--neutral-0);">
                        <option value="">-- Pilih Tabel MySQL / Database --</option>
                        @foreach($availableTables as $tbl)
                            <option value="{{ $tbl }}" {{ old('nama_tabel_fisik') === $tbl ? 'selected' : '' }}>
                                {{ $tbl }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="button" id="btn-inspect-table" onclick="inspectSelectedTable()" style="height: 42px; padding: 0 1.25rem; background: var(--blue-700); color: #FFFFFF; font-weight: 700; font-size: 0.8125rem; border: none; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                        <span>Inspeksi &amp; Muat Kolom</span>
                    </button>
                </div>
            </div>
            <div id="inspect-status-box" style="margin-top: 0.75rem; font-size: 0.8125rem; display: none;"></div>
        </div>

        {{-- 1. INFORMASI UTAMA MODUL --}}
        <div class="builder-card">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.875rem;">
                <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(7, 81, 255, 0.1); color: var(--blue-700); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.875rem;">
                    1
                </div>
                <div>
                    <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Informasi Utama Modul</h3>
                    <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0.15rem 0 0 0;">Tentukan nama modul, ikon visual, dan konfigurasi primary key.</p>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase; margin-bottom: 0.35rem;">Nama Modul *</label>
                    <input type="text" name="nama_modul" id="input-nama-modul" value="{{ old('nama_modul') }}" placeholder="Contoh: Inventaris Perangkat Laptop" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase; margin-bottom: 0.35rem;">Ikon Visual *</label>
                    <select name="ikon" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: var(--neutral-0);">
                        @foreach($availableIcons as $key => $label)
                            <option value="{{ $key }}" {{ old('ikon') === $key ? 'selected' : '' }}>
                                {{ $label }} ({{ $key }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase; margin-bottom: 0.35rem;">Kolom Primary Key *</label>
                    <input type="text" name="primary_key" id="input-primary-key" value="{{ old('primary_key', 'id') }}" placeholder="id" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: monospace;">
                </div>
                <div style="display: flex; align-items: center; padding-top: 1.25rem;">
                    <label style="display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; font-weight: 600; cursor: pointer;">
                        <input type="checkbox" name="is_manual_pk" id="check-manual-pk" value="1" {{ old('is_manual_pk') ? 'checked' : '' }}>
                        <span>Primary Key Manual (Non Auto-Increment, contoh: NIK, Kode)</span>
                    </label>
                </div>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase; margin-bottom: 0.35rem;">Deskripsi Modul</label>
                <textarea name="deskripsi" id="input-deskripsi" rows="2" placeholder="Jelaskan tujuan penggunaan modul ini di lingkungan Dinas Kominfo Sleman..." style="width: 100%; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0.625rem 0.875rem; font-size: 0.875rem;">{{ old('deskripsi') }}</textarea>
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
                        <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0.15rem 0 0 0;">Definisikan atribut kolom formulir atau hasil pemetaan otomatis dari tabel basis data.</p>
                    </div>
                </div>

                <button type="button" id="btn-add-field" style="height: 38px; padding: 0 1rem; background: var(--blue-700); color: #FFFFFF; font-weight: 700; font-size: 0.8125rem; border: none; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    <span>+ Tambah Kolom</span>
                </button>
            </div>

            <div id="fields-container" style="display: flex; flex-direction: column; gap: 1rem;">
                <!-- Baris Kolom Pertama (Default) -->
                <div class="field-row-card" data-index="0">
                    <div style="display: grid; grid-template-columns: 2fr 2fr 1.5fr auto; gap: 0.75rem; align-items: flex-end;">
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); margin-bottom: 0.35rem;">Nama Kolom (snake_case) *</label>
                            <input type="text" name="fields[0][nama_kolom]" value="nama" placeholder="nama" required style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.8125rem; font-family: monospace;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); margin-bottom: 0.35rem;">Label Tampilan *</label>
                            <input type="text" name="fields[0][label]" value="Nama Data" placeholder="Nama Data" required style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.8125rem;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); margin-bottom: 0.35rem;">Tipe Data *</label>
                            <select name="fields[0][tipe_data]" onchange="toggleExtraOptions(this)" required style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.5rem; font-size: 0.8125rem; background: var(--neutral-0);">
                                <option value="text" selected>Teks (String)</option>
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

                    <!-- Pengaturan Tambahan -->
                    <div class="extra-options" style="display: none; padding-top: 0.5rem; border-top: 1px dashed var(--neutral-300);">
                        <div class="select-options-box" style="display: none;">
                            <label style="display: block; font-size: 0.72rem; font-weight: 700; color: var(--neutral-600); margin-bottom: 0.25rem;">Opsi Pilihan (Pisahkan dengan tanda koma):</label>
                            <input type="text" name="fields[0][opsi_pilihan]" placeholder="Aktif, Menunggu, Selesai" style="width: 100%; height: 34px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.75rem;">
                        </div>
                        <div class="relation-options-box" style="display: none; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                            <div>
                                <label style="display: block; font-size: 0.72rem; font-weight: 700; color: var(--neutral-600); margin-bottom: 0.25rem;">Tabel Target (Relasi):</label>
                                <input type="text" name="fields[0][relasi_tabel]" placeholder="users, bidangs, jurusans" style="width: 100%; height: 34px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.75rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.72rem; font-weight: 700; color: var(--neutral-600); margin-bottom: 0.25rem;">Kolom Tampil (Label):</label>
                                <input type="text" name="fields[0][relasi_kolom_tampil]" placeholder="nama, judul" style="width: 100%; height: 34px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.75rem;">
                            </div>
                        </div>
                    </div>

                    <!-- Checkboxes Behavior -->
                    <div style="display: flex; gap: 1.5rem; flex-wrap: wrap; padding-top: 0.25rem;">
                        <label style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.75rem; font-weight: 600; cursor: pointer;">
                            <input type="checkbox" name="fields[0][wajib]" value="1"> Wajib Diisi (Required)
                        </label>
                        <label style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.75rem; font-weight: 600; cursor: pointer;">
                            <input type="checkbox" name="fields[0][tampil_di_tabel]" value="1" checked> Tampil di Tabel
                        </label>
                        <label style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.75rem; font-weight: 600; cursor: pointer;">
                            <input type="checkbox" name="fields[0][dapat_dicari]" value="1" checked> Dapat Dicari (Searchable)
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. HAK AKSES, FITUR KHUSUS & MATRIKS SUB-AKSI (Basefw CodeModule & CrudGenerator Parity) --}}
        <div class="builder-card">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.875rem;">
                <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(7, 81, 255, 0.1); color: var(--blue-700); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.875rem;">
                    3
                </div>
                <div>
                    <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Otorisasi Hak Akses, Fitur Khusus &amp; Matriks Sub-Aksi</h3>
                    <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0.15rem 0 0 0;">Konfigurasikan akses publik, pembatasan multi-tenancy, dan matriks izin sub-aksi (CodeModule).</p>
                </div>
            </div>

            {{-- 3.1 Saklar Khusus Basefw (Public & Scope) --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div style="border: 1px solid var(--neutral-300); border-radius: 10px; padding: 1rem;">
                    <label style="display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; font-weight: 700; cursor: pointer; margin-bottom: 0.35rem;">
                        <input type="checkbox" name="is_public_read" value="1" {{ old('is_public_read') ? 'checked' : '' }}>
                        <span>Endpoint Publik Read-Only (ckPublicRead)</span>
                    </label>
                    <p style="font-size: 0.75rem; color: var(--neutral-600); margin: 0;">
                        Menyediakan URL publik tanpa login di <code>/publik/modul/{slug}</code> untuk katalog atau informasi terbuka masyarakat.
                    </p>
                </div>

                <div style="border: 1px solid var(--neutral-300); border-radius: 10px; padding: 1rem;">
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase; margin-bottom: 0.35rem;">Isolasi Multi-Tenancy (Unit Scope)</label>
                    <select name="scope_mode" id="select-scope-mode" style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.5rem; font-size: 0.8125rem; background: var(--neutral-0);">
                        <option value="none" {{ old('scope_mode') === 'none' ? 'selected' : '' }}>None (Semua role berhak melihat seluruh data)</option>
                        <option value="bidang_scope" {{ old('scope_mode') === 'bidang_scope' ? 'selected' : '' }}>Isolasi Bidang Dinas (Pembimbing hanya melihat data bidangnya)</option>
                        <option value="creator_scope" {{ old('scope_mode') === 'creator_scope' ? 'selected' : '' }}>Isolasi Pembuat (Pengguna hanya melihat baris buatannya)</option>
                    </select>
                </div>
            </div>

            {{-- 3.2 Target Hak Akses Makro --}}
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase; margin-bottom: 0.5rem;">Target Role Pengguna (Makro) *</label>
                <div style="display: flex; gap: 1.5rem; flex-wrap: wrap;">
                    <label style="display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.8125rem; font-weight: 600;">
                        <input type="checkbox" checked disabled> SuperAdmin (Wajib Penuh)
                        <input type="hidden" name="target_roles[]" value="superadmin">
                    </label>
                    <label style="display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.8125rem; font-weight: 600;">
                        <input type="checkbox" name="target_roles[]" value="admin" checked> Admin MagKom
                    </label>
                    <label style="display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.8125rem; font-weight: 600;">
                        <input type="checkbox" name="target_roles[]" value="pembimbing"> Pembimbing Dinas
                    </label>
                    <label style="display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.8125rem; font-weight: 600;">
                        <input type="checkbox" name="target_roles[]" value="peserta"> Peserta Magang
                    </label>
                </div>
            </div>

            {{-- 3.3 Matriks Granular Sub-Aksi & Logging Toggle (CodeModule Parity) --}}
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
                                <tr>
                                    <td>
                                        <strong>{{ $actLabel }}</strong>
                                        <div style="font-size: 0.7rem; color: var(--neutral-500); font-family: monospace;">{{ $actKey }}</div>
                                    </td>
                                    <td>
                                        <input type="checkbox" name="izin_aksi[{{ $actKey }}][]" value="admin" checked>
                                    </td>
                                    <td>
                                        <input type="checkbox" name="izin_aksi[{{ $actKey }}][]" value="pembimbing" {{ in_array($actKey, ['list', 'show', 'print', 'export_pdf']) ? 'checked' : '' }}>
                                    </td>
                                    <td>
                                        <input type="checkbox" name="izin_aksi[{{ $actKey }}][]" value="peserta" {{ in_array($actKey, ['list', 'show']) ? 'checked' : '' }}>
                                    </td>
                                    <td>
                                        <input type="checkbox" name="log_aksi[{{ $actKey }}]" value="1" {{ in_array($actKey, ['create', 'edit', 'delete', 'export_csv', 'export_pdf']) ? 'checked' : '' }}>
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
                <span>Ciptakan &amp; Terbitkan Modul</span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    let fieldIndex = 1;

    function switchBuilderMode(mode) {
        document.getElementById('input-tipe-sumber').value = mode;
        const btnDynamic = document.getElementById('btn-mode-dynamic');
        const btnTable = document.getElementById('btn-mode-table');
        const panelTable = document.getElementById('panel-reverse-engineering');

        if (mode === 'physical_table') {
            btnTable.classList.add('active');
            btnDynamic.classList.remove('active');
            panelTable.style.display = 'block';
        } else {
            btnDynamic.classList.add('active');
            btnTable.classList.remove('active');
            panelTable.style.display = 'none';
        }
    }

    // Toggle opsi ekstra untuk tipe data select / relation
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

    // Inspeksi tabel terpilih via API reverse-engineering
    function inspectSelectedTable() {
        const table = document.getElementById('select-tabel-fisik').value;
        const statusBox = document.getElementById('inspect-status-box');

        if (!table) {
            alert('Silakan pilih tabel basis data terlebih dahulu.');
            return;
        }

        statusBox.style.display = 'block';
        statusBox.style.color = 'var(--blue-700)';
        statusBox.innerHTML = '<em>Memeriksa skema kolom tabel ' + table + '...</em>';

        fetch('{{ route('superadmin.modul-builder.api.inspect') }}?table=' + encodeURIComponent(table))
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    statusBox.style.color = '#DC2626';
                    statusBox.innerHTML = '<strong>Gagal:</strong> ' + (data.message || 'Tabel tidak dapat diinspeksi');
                    return;
                }

                const schema = data.schema;
                statusBox.style.color = '#059669';
                statusBox.innerHTML = '<strong>Berhasil!</strong> Ditemukan ' + schema.columns.length + ' kolom pada tabel ' + schema.table + '.';

                // Auto-fill nama modul jika kosong
                const nameInput = document.getElementById('input-nama-modul');
                if (!nameInput.value.trim()) {
                    nameInput.value = schema.table.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                }

                // Set Primary Key & Manual PK Checkbox
                document.getElementById('input-primary-key').value = schema.primary_key;
                document.getElementById('check-manual-pk').checked = schema.is_manual_pk;

                // Set Scope Mode jika ada kolom unit scope
                if (schema.has_unit_scope) {
                    document.getElementById('select-scope-mode').value = 'bidang_scope';
                }

                // Render fields container dengan kolom hasil inspeksi
                const container = document.getElementById('fields-container');
                container.innerHTML = '';
                fieldIndex = 0;

                schema.columns.forEach(col => {
                    addFieldRow(col.name, col.label, col.data_type, !col.is_nullable, col.is_visible, col.is_searchable);
                });
            })
            .catch(err => {
                statusBox.style.color = '#DC2626';
                statusBox.innerHTML = '<strong>Error koneksi:</strong> ' + err.message;
            });
    }

    function addFieldRow(name = '', label = '', type = 'text', required = false, visible = true, searchable = true) {
        const container = document.getElementById('fields-container');
        const idx = fieldIndex;

        const row = document.createElement('div');
        row.className = 'field-row-card';
        row.setAttribute('data-index', idx);

        row.innerHTML = `
            <div style="display: grid; grid-template-columns: 2fr 2fr 1.5fr auto; gap: 0.75rem; align-items: flex-end;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); margin-bottom: 0.35rem;">Nama Kolom (snake_case) *</label>
                    <input type="text" name="fields[${idx}][nama_kolom]" value="${name}" placeholder="nama_kolom" required style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.8125rem; font-family: monospace;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); margin-bottom: 0.35rem;">Label Tampilan *</label>
                    <input type="text" name="fields[${idx}][label]" value="${label}" placeholder="Label Tampilan" required style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.8125rem;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); margin-bottom: 0.35rem;">Tipe Data *</label>
                    <select name="fields[${idx}][tipe_data]" onchange="toggleExtraOptions(this)" required style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.5rem; font-size: 0.8125rem; background: var(--neutral-0);">
                        <option value="text" ${type === 'text' ? 'selected' : ''}>Teks (String)</option>
                        <option value="number" ${type === 'number' ? 'selected' : ''}>Angka (Number)</option>
                        <option value="textarea" ${type === 'textarea' ? 'selected' : ''}>Teks Panjang (Textarea)</option>
                        <option value="select" ${type === 'select' ? 'selected' : ''}>Dropdown Pilihan (Select)</option>
                        <option value="multiselect" ${type === 'multiselect' ? 'selected' : ''}>Multi Pilihan (Array)</option>
                        <option value="date" ${type === 'date' ? 'selected' : ''}>Tanggal (Date)</option>
                        <option value="datetime" ${type === 'datetime' ? 'selected' : ''}>Tanggal &amp; Waktu (DateTime)</option>
                        <option value="file" ${type === 'file' ? 'selected' : ''}>Unggah Berkas (PDF/Gambar/Doc)</option>
                        <option value="boolean" ${type === 'boolean' ? 'selected' : ''}>Switch Ya/Tidak (Boolean)</option>
                        <option value="relation" ${type === 'relation' ? 'selected' : ''}>Relasi Tabel MagKom (Lookup)</option>
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
                    <input type="checkbox" name="fields[${idx}][wajib]" value="1" ${required ? 'checked' : ''}> Wajib Diisi (Required)
                </label>
                <label style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.75rem; font-weight: 600; cursor: pointer;">
                    <input type="checkbox" name="fields[${idx}][tampil_di_tabel]" value="1" ${visible ? 'checked' : ''}> Tampil di Tabel
                </label>
                <label style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.75rem; font-weight: 600; cursor: pointer;">
                    <input type="checkbox" name="fields[${idx}][dapat_dicari]" value="1" ${searchable ? 'checked' : ''}> Dapat Dicari (Searchable)
                </label>
            </div>
        `;

        container.appendChild(row);
        fieldIndex++;
    }

    document.getElementById('btn-add-field').addEventListener('click', function() {
        addFieldRow('', '', 'text', false, true, true);
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

    // Inisialisasi awal
    @if(old('tipe_sumber') === 'physical_table')
        switchBuilderMode('physical_table');
    @endif
</script>
@endpush
