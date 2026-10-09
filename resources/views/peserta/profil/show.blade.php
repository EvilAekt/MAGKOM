@extends('layouts.peserta')

@section('title', 'Biodata Profil Peserta')
@section('page_title', 'Biodata & Profil Peserta')

@push('styles')
<style>
    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .form-group.full-width {
        grid-column: span 2;
    }
    .form-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--neutral-800);
    }
    .form-label span.req {
        color: var(--red-600);
    }
    .form-control {
        height: 44px;
        padding: 0 14px;
        border: 1px solid var(--neutral-500);
        border-radius: var(--radius-default);
        font-size: 14px;
        color: var(--neutral-800);
        background: #ffffff;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .form-control:focus {
        outline: none;
        border-color: var(--blue-600);
        box-shadow: 0 0 0 3px rgba(7, 81, 255, 0.2);
    }
    .form-control:disabled, .form-control[readonly] {
        background: var(--neutral-200);
        color: var(--neutral-600);
        cursor: not-allowed;
    }
    .form-control.is-invalid {
        border-color: var(--red-600);
    }
    .invalid-feedback {
        font-size: 12px;
        color: var(--red-600);
        font-weight: 500;
    }
    .section-divider {
        grid-column: span 2;
        margin: 16px 0 8px 0;
        padding-top: 16px;
        border-top: 1px solid var(--neutral-400);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .section-divider h3 {
        font-size: 15px;
        font-weight: 700;
        color: var(--neutral-800);
    }
    .section-divider p {
        font-size: 12px;
        color: var(--neutral-600);
    }
    .status-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-radius: var(--radius-default);
        margin-bottom: 24px;
    }
    .status-card.complete {
        background: var(--green-100);
        border: 1px solid var(--green-600);
    }
    .status-card.incomplete {
        background: var(--amber-100);
        border: 1px solid var(--amber-300);
    }
    .status-meta h4 {
        font-size: 15px;
        font-weight: 700;
    }
    .status-card.complete .status-meta h4 { color: var(--green-700); }
    .status-card.incomplete .status-meta h4 { color: var(--amber-700); }
    .status-meta p {
        font-size: 13px;
        margin-top: 2px;
    }
    .status-card.complete .status-meta p { color: var(--green-700); }
    .status-card.incomplete .status-meta p { color: var(--amber-700); }

    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
        .form-group.full-width, .section-divider {
            grid-column: span 1;
        }
        .status-card {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }
    }
</style>
@endpush

@section('content')
<div style="max-width: 900px; margin: 0 auto;">

    {{-- Status Kelengkapan Profil --}}
    @if($profil && $profil->nim_nisn && $profil->institusi && $profil->jurusan_id && $profil->no_hp)
        <div class="status-card complete">
            <div class="status-meta">
                <h4>✓ Profil Peserta Lengkap</h4>
                <p>Data profil Anda sudah siap. Anda dapat melanjutkan ke proses pendaftaran magang.</p>
            </div>
            <a href="{{ route('peserta.pendaftaran.index') }}" class="btn btn-primary" style="height: 38px; padding: 0 16px; font-size: 13px;">
                Daftar Magang →
            </a>
        </div>
    @else
        <div class="status-card incomplete">
            <div class="status-meta">
                <h4>⚠ Profil Belum Lengkap</h4>
                <p>Silakan lengkapi formulir biodata berikut agar dapat mengajukan pendaftaran magang baru.</p>
            </div>
            <span class="badge badge-warning">Langkah 1 dari 2</span>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="card-title">Formulir Biodata Peserta</h2>
                <p class="card-subtitle">Pastikan data institusi dan nomor kontak sesuai dengan data resmi Anda.</p>
            </div>
        </div>

        <form action="{{ route('peserta.profil.simpan') }}" method="POST">
            @csrf

            <div class="form-grid">
                {{-- Data Akun (Readonly) --}}
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" value="{{ $user->nama ?? $user->name }}" readonly disabled>
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat Email Terdaftar</label>
                    <input type="email" class="form-control" value="{{ $user->email }}" readonly disabled>
                </div>

                {{-- Bagian Akademik & Pendidikan --}}
                <div class="section-divider">
                    <div>
                        <h3>Informasi Pendidikan & Akademik</h3>
                        <p>Data sekolah atau perguruan tinggi asal peserta magang</p>
                    </div>
                </div>

                <div class="form-group">
                    <label for="institusi" class="form-label">Nama Sekolah / Kampus <span class="req">*</span></label>
                    <input type="text" id="institusi" name="institusi"
                           class="form-control @error('institusi') is-invalid @enderror"
                           value="{{ old('institusi', $profil?->institusi) }}"
                           placeholder="Contoh: Universitas Gadjah Mada / SMKN 2 Depok" required>
                    @error('institusi')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="nim_nisn" class="form-label">NISN / NIM <span class="req">*</span></label>
                    <input type="text" id="nim_nisn" name="nim_nisn"
                           class="form-control @error('nim_nisn') is-invalid @enderror"
                           value="{{ old('nim_nisn', $profil?->nim_nisn) }}"
                           placeholder="Masukkan nomor identitas siswa/mahasiswa" required>
                    @error('nim_nisn')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="jenjang" class="form-label">Jenjang Pendidikan <span class="req">*</span></label>
                    <select id="jenjang" name="jenjang" class="form-control @error('jenjang') is-invalid @enderror" required>
                        <option value="">-- Pilih Jenjang --</option>
                        <option value="smk" {{ old('jenjang', $profil?->jenjang?->value ?? $profil?->jenjang) === 'smk' ? 'selected' : '' }}>SMK (Siswa)</option>
                        <option value="d3" {{ old('jenjang', $profil?->jenjang?->value ?? $profil?->jenjang) === 'd3' ? 'selected' : '' }}>D3 (Diploma Tiga)</option>
                        <option value="s1" {{ old('jenjang', $profil?->jenjang?->value ?? $profil?->jenjang) === 's1' ? 'selected' : '' }}>S1 / D4 (Sarjana)</option>
                    </select>
                    @error('jenjang')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="jurusan_id" class="form-label">Jurusan / Program Studi <span class="req">*</span></label>
                    <select id="jurusan_id" name="jurusan_id" class="form-control @error('jurusan_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Jurusan --</option>
                        @foreach($jurusanList as $jurusan)
                            <option value="{{ $jurusan->id }}" {{ old('jurusan_id', $profil?->jurusan_id) == $jurusan->id ? 'selected' : '' }}>
                                {{ $jurusan->nama_jurusan }} ({{ strtoupper($jurusan->kategori) }})
                            </option>
                        @endforeach
                    </select>
                    @error('jurusan_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Kontak Pribadi & Darurat --}}
                <div class="section-divider">
                    <div>
                        <h3>Kontak Pribadi & Darurat</h3>
                        <p>Untuk kebutuhan koordinasi pembimbingan dan komunikasi darurat instansi</p>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label for="no_hp" class="form-label">Nomor WhatsApp / Telepon Aktif <span class="req">*</span></label>
                    <input type="tel" id="no_hp" name="no_hp"
                           class="form-control @error('no_hp') is-invalid @enderror"
                           value="{{ old('no_hp', $profil?->no_hp ?? $user->no_telp) }}"
                           placeholder="081234567890" required>
                    @error('no_hp')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="nama_kontak_darurat" class="form-label">Nama Kontak Darurat</label>
                    <input type="text" id="nama_kontak_darurat" name="nama_kontak_darurat"
                           class="form-control @error('nama_kontak_darurat') is-invalid @enderror"
                           value="{{ old('nama_kontak_darurat', $profil?->nama_kontak_darurat) }}"
                           placeholder="Contoh: Budi Santoso (Ayah / Wali)">
                    @error('nama_kontak_darurat')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="hubungan_kontak_darurat" class="form-label">Hubungan dengan Kontak</label>
                    <input type="text" id="hubungan_kontak_darurat" name="hubungan_kontak_darurat"
                           class="form-control @error('hubungan_kontak_darurat') is-invalid @enderror"
                           value="{{ old('hubungan_kontak_darurat', $profil?->hubungan_kontak_darurat) }}"
                           placeholder="Contoh: Orang Tua / Wali / Kakak">
                    @error('hubungan_kontak_darurat')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group full-width">
                    <label for="no_kontak_darurat" class="form-label">Nomor Telepon Kontak Darurat</label>
                    <input type="tel" id="no_kontak_darurat" name="no_kontak_darurat"
                           class="form-control @error('no_kontak_darurat') is-invalid @enderror"
                           value="{{ old('no_kontak_darurat', $profil?->no_kontak_darurat) }}"
                           placeholder="089876543210">
                    @error('no_kontak_darurat')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div style="margin-top: 32px; display: flex; justify-content: flex-end; gap: 12px;">
                <button type="submit" class="btn btn-primary" id="btnSimpanProfil">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simpan Biodata Profil
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
