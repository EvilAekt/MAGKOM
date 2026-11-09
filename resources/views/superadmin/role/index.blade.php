@extends('layouts.dashboard')

@section('title', 'Hak Akses dan Role Pengguna - SuperAdmin MagKom')
@section('topbar_title', 'Hak Akses dan Role Pengguna')

@section('breadcrumbs')
    <a href="{{ route('superadmin.dashboard') }}">SuperAdmin</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Hak Akses dan Role Pengguna</span>
@endsection

@push('styles')
    @vite(['resources/css/pages/superadmin/role-index.css'])
@endpush

@push('scripts')
    @vite(['resources/js/pages/superadmin/role-index.js'])
@endpush

@section('content')
<div class="sa-role-container">

    {{-- Alert Notifikasi Sukses / Error --}}
    @if(session('success'))
        <div class="alert alert-success" style="border-radius: 10px; padding: 1rem 1.25rem; background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0; display: flex; align-items: center; gap: 0.75rem;">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="border-radius: 10px; padding: 1rem 1.25rem; background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; display: flex; align-items: center; gap: 0.75rem;">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger" style="border-radius: 10px; padding: 1rem 1.25rem; background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA;">
            <div style="font-weight: 700; margin-bottom: 0.35rem;">Terdapat kesalahan pada input:</div>
            <ul style="margin: 0; padding-left: 1.25rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- 1. Hero Banner Khas Sleman MagKom (Adopsi Pola Logbook Peserta) --}}
    <div class="sa-role-banner">
        <div class="banner-top-row">
            <div>
                <h1 class="banner-title">Hak Akses dan Role Pengguna</h1>
                <div class="banner-subtitle">
                    <span>Cakupan: <strong>4 Role Sistem</strong></span>
                    <span class="bullet-separator">&bull;</span>
                    <span>Total Pengguna: <strong>{{ $totalPengguna }} Akun Terdaftar</strong></span>
                </div>
            </div>
            <div>
                <button type="button" class="btn-banner-action" id="btnBukaModalTambah">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
                    Tambah Hak Akses
                </button>
            </div>
        </div>
    </div>

    {{-- 2. KARTU STATISTIK 4 ROLE MENGADOPSI GENTELELLA TILE-STAT --}}
    <div class="tiles-grid">
        <!-- 1. SuperAdmin -->
        <x-gentelella.tile-stat
            title="SUPERADMIN"
            value="{{ $roleCounts['superadmin'] ?? 0 }}"
            color="blue"
            trendType="info"
            trendDirection="up"
            trendText="Eksekutif"
            subText="Tata Kelola Sistem & Eksekutif"
            :sparkBars="[30, 45, 60, 75, 85, 95]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <!-- 2. Administrasi Dinas -->
        <x-gentelella.tile-stat
            title="ADMINISTRASI"
            value="{{ $roleCounts['admin'] ?? 0 }}"
            color="teal"
            trendType="success"
            trendDirection="up"
            trendText="Dinas"
            subText="Operasional Seleksi & Kuota"
            :sparkBars="[25, 40, 55, 70, 80, 90]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <!-- 3. Pembimbing Lapangan -->
        <x-gentelella.tile-stat
            title="PEMBIMBING"
            value="{{ $roleCounts['pembimbing'] ?? 0 }}"
            color="yellow"
            trendType="warning"
            trendDirection="up"
            trendText="Lapangan"
            subText="Mentoring & Evaluasi Kinerja"
            :sparkBars="[40, 50, 65, 75, 85, 92]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 14l9-5-9-5-9 5 9 5z"></path>
                    <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <!-- 4. Peserta Magang -->
        <x-gentelella.tile-stat
            title="PESERTA MAGANG"
            value="{{ $roleCounts['peserta'] ?? 0 }}"
            color="teal"
            trendType="success"
            trendDirection="up"
            trendText="Aktif"
            subText="Pelaksanaan Program Magang"
            :sparkBars="[35, 50, 60, 75, 88, 100]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>
    </div>

    {{-- 3. FORM MATRIKS HAK AKSES MENYELURUH (MURNI 4 ROLE SISTEM) --}}
    <x-gentelella.x-panel class="has-table" title="Matriks Wewenang & Hak Akses Modul Dinamis">
        <x-slot:toolbox>
            <a href="{{ route('superadmin.staf.index') }}" style="font-size: 0.8125rem; font-weight: 700; color: var(--blue-600); text-decoration: none;">
                Kelola Akun Staf &rarr;
            </a>
        </x-slot:toolbox>

        <form action="{{ route('superadmin.role.matrix.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="table-responsive-gentelella" style="overflow-x: auto;">
                <table class="table jambo_table bulk_action sa-matrix-table" style="width: 100%; border-collapse: collapse; font-size: 0.8125rem; text-align: left;">
                    <thead>
                        <tr class="headings">
                            <th style="min-width: 320px;">Modul & Cakupan Fitur</th>
                            <th style="text-align: center; width: 140px;">SuperAdmin</th>
                            <th style="text-align: center; width: 140px;">Administrasi</th>
                            <th style="text-align: center; width: 140px;">Pembimbing</th>
                            <th style="text-align: center; width: 140px;">Peserta</th>
                        </tr>
                    </thead>
                    <tbody style="divide-y: 1px solid var(--neutral-300);">
                        @forelse($permissions as $permission)
                            @php
                                $assignedRoles = $permission->getAssignedRoles();
                                $isCoreSystem = $permission->is_system && in_array($permission->slug, [
                                    'keamanan.audit-trail',
                                    'staf.manajemen',
                                    'konfigurasi.parameter',
                                ], true);
                            @endphp
                            <tr class="sa-table-row" style="border-bottom: 1px solid var(--neutral-300);">
                                <td>
                                    <div class="sa-permission-title">
                                        <span>{{ $permission->nama_modul }}</span>
                                        <span class="badge-module-category">{{ $permission->kategori }}</span>
                                        @if($permission->is_system)
                                            <span class="badge-module-system" title="Modul Bawaan Sistem">System</span>
                                        @endif
                                    </div>
                                    @if($permission->deskripsi)
                                        <div class="sa-permission-desc">{{ $permission->deskripsi }}</div>
                                    @endif
                                </td>

                                <!-- SuperAdmin -->
                                <td style="text-align: center;">
                                    <label class="sa-checkbox-label" title="SuperAdmin">
                                        @if($isCoreSystem)
                                            <input type="hidden" name="matrix[{{ $permission->id }}][superadmin]" value="1">
                                            <input type="checkbox" class="sa-checkbox-input" checked disabled title="Wewenang mutlak sistem SuperAdmin">
                                        @else
                                            <input type="checkbox"
                                                   name="matrix[{{ $permission->id }}][superadmin]"
                                                   value="1"
                                                   class="sa-checkbox-input"
                                                   {{ in_array('superadmin', $assignedRoles, true) ? 'checked' : '' }}>
                                        @endif
                                    </label>
                                </td>

                                <!-- Admin -->
                                <td style="text-align: center;">
                                    <label class="sa-checkbox-label" title="Administrasi Dinas">
                                        <input type="checkbox"
                                               name="matrix[{{ $permission->id }}][admin]"
                                               value="1"
                                               class="sa-checkbox-input"
                                               {{ in_array('admin', $assignedRoles, true) ? 'checked' : '' }}>
                                    </label>
                                </td>

                                <!-- Pembimbing -->
                                <td style="text-align: center;">
                                    <label class="sa-checkbox-label" title="Pembimbing Lapangan">
                                        <input type="checkbox"
                                               name="matrix[{{ $permission->id }}][pembimbing]"
                                               value="1"
                                               class="sa-checkbox-input"
                                               {{ in_array('pembimbing', $assignedRoles, true) ? 'checked' : '' }}>
                                    </label>
                                </td>

                                <!-- Peserta -->
                                <td style="text-align: center;">
                                    <label class="sa-checkbox-label" title="Peserta Magang">
                                        <input type="checkbox"
                                               name="matrix[{{ $permission->id }}][peserta]"
                                               value="1"
                                               class="sa-checkbox-input"
                                               {{ in_array('peserta', $assignedRoles, true) ? 'checked' : '' }}>
                                    </label>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 2rem; color: var(--neutral-500);">
                                    Belum ada data modul hak akses yang terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="sa-matrix-footer">
                <div style="font-size: 0.8125rem; color: var(--neutral-600);">
                    💡 <em>Centang atau hapus centang untuk mengatur hak akses tiap peran. Perubahan ini langsung berlaku secara general untuk seluruh akun pemegang role tersebut.</em>
                </div>
                <button type="submit" class="btn-save-matrix">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg>
                    Simpan Perubahan Matriks
                </button>
            </div>
        </form>
    </x-gentelella.x-panel>

</div>

{{-- 4. MODAL DIALOG: TAMBAH HAK AKSES MODUL BARU --}}
<div class="sa-modal-backdrop" id="modalTambahPermission" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <div class="sa-modal-content">
        <div class="sa-modal-header">
            <h3 class="sa-modal-title" id="modalTitle">Tambah Hak Akses Modul Baru</h3>
            <button type="button" class="btn-modal-close" id="btnTutupModal" aria-label="Tutup dialog">&times;</button>
        </div>

        <form action="{{ route('superadmin.role.permission.store') }}" method="POST">
            @csrf
            <div class="sa-modal-body">
                <div class="form-group-field">
                    <label class="form-label-field" for="inputNamaModul">Nama Modul / Hak Akses <span style="color: #EF4444;">*</span></label>
                    <input type="text"
                           id="inputNamaModul"
                           name="nama_modul"
                           class="form-input-field"
                           placeholder="Contoh: Manajemen Sertifikat Elektronik"
                           required
                           value="{{ old('nama_modul') }}">
                </div>

                <div class="form-group-field">
                    <label class="form-label-field" for="inputSlug">Kode Unik / Slug (Opsional)</label>
                    <input type="text"
                           id="inputSlug"
                           name="slug"
                           class="form-input-field"
                           placeholder="Otomatis dibuat jika kosong (contoh: sertifikat.manajemen)"
                           value="{{ old('slug') }}">
                </div>

                <div class="form-group-field">
                    <label class="form-label-field" for="selectKategori">Kategori Modul <span style="color: #EF4444;">*</span></label>
                    <select id="selectKategori" name="kategori" class="form-select-field" required>
                        <option value="Operasional Seleksi">Operasional Seleksi</option>
                        <option value="Pendaftaran">Pendaftaran</option>
                        <option value="Bimbingan">Bimbingan</option>
                        <option value="Penilaian">Penilaian</option>
                        <option value="Kuota">Kuota</option>
                        <option value="Penempatan">Penempatan</option>
                        <option value="Tata Kelola">Tata Kelola</option>
                        <option value="Keamanan">Keamanan</option>
                        <option value="Laporan">Laporan</option>
                        <option value="Umum" selected>Umum</option>
                    </select>
                </div>

                <div class="form-group-field">
                    <label class="form-label-field" for="textareaDeskripsi">Deskripsi Cakupan</label>
                    <textarea id="textareaDeskripsi"
                              name="deskripsi"
                              class="form-textarea-field"
                              placeholder="Deskripsi singkat fungsi dan tujuan hak akses ini...">{{ old('deskripsi') }}</textarea>
                </div>

                <div class="form-group-field">
                    <label class="form-label-field">Aktifkan untuk Peran (Role Assignment):</label>
                    <div class="role-checkboxes-grid">
                        <label class="role-checkbox-item">
                            <input type="checkbox" name="roles[]" value="superadmin" checked>
                            <span>SuperAdmin</span>
                        </label>
                        <label class="role-checkbox-item">
                            <input type="checkbox" name="roles[]" value="admin">
                            <span>Administrasi Dinas</span>
                        </label>
                        <label class="role-checkbox-item">
                            <input type="checkbox" name="roles[]" value="pembimbing">
                            <span>Pembimbing</span>
                        </label>
                        <label class="role-checkbox-item">
                            <input type="checkbox" name="roles[]" value="peserta">
                            <span>Peserta Magang</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="sa-modal-footer">
                <button type="button" class="btn-secondary" id="btnBatalModal">Batal</button>
                <button type="submit" class="btn-primary-action">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg>
                    Simpan Hak Akses
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
