@extends('layouts.dashboard')

@section('title', 'Profil Biodata - Peserta MagKom Sleman')
@section('page_title', 'Biodata Profil Peserta')
@section('page_description', 'Lengkapi identitas diri, jenjang pendidikan, dan kontak darurat Anda sebelum mengajukan berkas magang.')

@section('breadcrumbs')
    <a href="{{ route('peserta.pendaftaran.index') }}">Peserta</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Profil Saya</span>
@endsection

@section('content')
<div style="max-width: 860px;">
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 2rem; box-shadow: var(--shadow-subtle);">
        <form method="POST" action="{{ route('peserta.profil.simpan') }}" style="display: flex; flex-direction: column; gap: 1.5rem;">
            @csrf

            <!-- Informasi Akun Dasar -->
            <div style="border-bottom: 1px solid var(--neutral-200); padding-bottom: 1.25rem;">
                <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.25rem;">Informasi Akun</h3>
                <p style="font-size: 0.8125rem; color: var(--neutral-600);">Kredensial dasar yang digunakan untuk masuk ke portal.</p>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem; margin-top: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">Nama Lengkap</label>
                        <input type="text" value="{{ $user->nama }}" disabled style="width: 100%; height: 44px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; background: var(--neutral-200); color: var(--neutral-600); font-size: 0.875rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">Alamat Email</label>
                        <input type="email" value="{{ $user->email }}" disabled style="width: 100%; height: 44px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; background: var(--neutral-200); color: var(--neutral-600); font-size: 0.875rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">Nomor Telepon / WhatsApp</label>
                        <input type="text" value="{{ $user->no_telp ?? '-' }}" disabled style="width: 100%; height: 44px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; background: var(--neutral-200); color: var(--neutral-600); font-size: 0.875rem;">
                    </div>
                </div>
            </div>

            <!-- Identitas & Pendidikan -->
            <div style="border-bottom: 1px solid var(--neutral-200); padding-bottom: 1.25rem;">
                <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.25rem;">Pendidikan & Asal Instansi</h3>
                <p style="font-size: 0.8125rem; color: var(--neutral-600);">Data instansi sekolah atau perguruan tinggi tempat Anda menempuh pendidikan.</p>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem; margin-top: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">Jenjang Pendidikan <span style="color: var(--red-600);">*</span></label>
                        <select name="jenjang" required style="width: 100%; height: 44px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; background: var(--neutral-0);">
                            <option value="">Pilih Jenjang</option>
                            <option value="smk" {{ old('jenjang', $profil?->jenjang?->value ?? $profil?->jenjang) === 'smk' ? 'selected' : '' }}>SMK (Sekolah Menengah Kejuruan)</option>
                            <option value="d3" {{ old('jenjang', $profil?->jenjang?->value ?? $profil?->jenjang) === 'd3' ? 'selected' : '' }}>Diploma 3 (D3)</option>
                            <option value="s1" {{ old('jenjang', $profil?->jenjang?->value ?? $profil?->jenjang) === 's1' ? 'selected' : '' }}>Strata 1 (S1)</option>
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">Jurusan Terdaftar <span style="color: var(--red-600);">*</span></label>
                        <select name="jurusan_id" required style="width: 100%; height: 44px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; background: var(--neutral-0);">
                            <option value="">Pilih Jurusan</option>
                            @foreach($jurusanList as $j)
                                <option value="{{ $j->id }}" {{ old('jurusan_id', $profil?->jurusan_id) == $j->id ? 'selected' : '' }}>
                                    [{{ strtoupper($j->kategori) }}] {{ $j->nama_jurusan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">NISN / NIM <span style="color: var(--red-600);">*</span></label>
                        <input type="text" name="nomor_induk" value="{{ old('nomor_induk', $profil?->nomor_induk) }}" required placeholder="Contoh: 215410088" style="width: 100%; height: 44px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem;">
                    </div>

                    <div style="grid-column: 1 / -1;">
                        <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">Nama Asal Sekolah / Kampus <span style="color: var(--red-600);">*</span></label>
                        <input type="text" name="asal_instansi" value="{{ old('asal_instansi', $profil?->asal_instansi) }}" required placeholder="Contoh: Universitas Gadjah Mada / SMKN 1 Sleman" style="width: 100%; height: 44px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem;">
                    </div>
                </div>
            </div>

            <!-- Kontak Darurat & Alamat -->
            <div>
                <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.25rem;">Kontak Darurat & Domisili</h3>
                <p style="font-size: 0.8125rem; color: var(--neutral-600);">Kontak yang dapat dihubungi dalam keadaan penting saat pelaksanaan magang.</p>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem; margin-top: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">Nama Kontak Darurat</label>
                        <input type="text" name="kontak_darurat_nama" value="{{ old('kontak_darurat_nama', $profil?->kontak_darurat_nama) }}" placeholder="Contoh: Bpk. Bambang (Orang Tua)" style="width: 100%; height: 44px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">Nomor Telepon Darurat</label>
                        <input type="text" name="kontak_darurat_no" value="{{ old('kontak_darurat_no', $profil?->kontak_darurat_no) }}" placeholder="Contoh: 081234567890" style="width: 100%; height: 44px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem;">
                    </div>

                    <div style="grid-column: 1 / -1;">
                        <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">Alamat Domisili di Yogyakarta / Sleman</label>
                        <textarea name="alamat_domisili" rows="3" placeholder="Masukkan alamat tempat tinggal atau kos selama magang..." style="width: 100%; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0.75rem 0.875rem; font-size: 0.875rem; font-family: inherit;">{{ old('alamat_domisili', $profil?->alamat_domisili) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Tombol Simpan -->
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1rem; border-top: 1px solid var(--neutral-200); padding-top: 1.25rem;">
                <button type="submit" style="display: inline-flex; align-items: center; gap: 0.5rem; background: var(--blue-600); color: #FFFFFF; font-weight: 600; font-size: 0.875rem; padding: 0.625rem 1.5rem; border: none; border-radius: 8px; cursor: pointer; min-height: 44px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    <span>Simpan Biodata Profil</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
