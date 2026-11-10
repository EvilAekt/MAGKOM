@extends('layouts.dashboard')

@section('title', 'Tambah Peserta Jalur Khusus - SuperAdmin MagKom')
@section('topbar_title', 'Pendaftaran Peserta Jalur Khusus')

@section('breadcrumbs')
    <a href="{{ route('superadmin.dashboard') }}">SuperAdmin</a>
    <span class="breadcrumb-separator">/</span>
    <a href="{{ route('superadmin.slot-khusus.index') }}">Jalur Khusus</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Tambah Peserta</span>
@endsection

@push('styles')
    @vite(['resources/css/pages/superadmin/superadmin-common.css'])
@endpush

@section('content')
<div class="sa-fluid-container" style="display: flex; flex-direction: column; gap: 1.5rem; max-width: 980px; margin: 0 auto; width: 100%;">

    {{-- 1. Hero Banner Khas Sleman MagKom --}}
    <div class="sa-hero-banner sc-reveal-hero" data-scroll-reveal>
        <div class="banner-top-row">
            <div>
                <h1 class="banner-title">Pendaftaran Peserta Jalur Khusus</h1>
                <div class="banner-subtitle">
                    <span>Cakupan: <strong>Penugasan Instansi &amp; Kemitraan Resmi</strong></span>
                </div>
            </div>
        </div>
    </div>

    <x-gentelella.x-panel class="sc-reveal-up sc-delay-1" data-scroll-reveal title="Formulir Pendaftaran Peserta Jalur Khusus">
        <div style="background: var(--blue-tint, #EFF6FF); border: 1px solid var(--blue-tint-line, #DBEAFE); border-radius: 8px; padding: 1rem 1.25rem; margin-bottom: 1.5rem; color: #1E40AF; font-size: 0.875rem; line-height: 1.5;">
            <strong>Informasi Jalur Khusus:</strong> Pendaftaran melalui formulir ini diperuntukkan bagi peserta magang penugasan instansi, rekomendasi khusus, atau program kemitraan resmi. Akun peserta akan langsung dibuat dengan role <strong>Peserta Magang</strong> dan status penempatan <strong>Aktif</strong> tanpa melalui tahapan antrean verifikasi berkas reguler.
        </div>

        <form action="{{ route('superadmin.peserta-khusus.store') }}" method="POST" style="display: flex; flex-direction: column; gap: 1.5rem;">
            @csrf

            {{-- 1. DATA PRIBADI & AKUN --}}
            <div>
                <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900); margin: 0 0 1rem 0; padding-bottom: 0.5rem; border-bottom: 2px solid var(--blue-600); display: inline-block;">
                    1. Data Pribadi & Kredensial Akun
                </h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Nama Lengkap Peserta <span style="color: var(--red-600);">*</span>
                        </label>
                        <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="Contoh: Muhammad Ihsan" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                        @error('nama')
                            <span style="color: var(--red-600); font-size: 0.75rem;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Alamat Email (Unik) <span style="color: var(--red-600);">*</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="ihsan.peserta@gmail.com" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                        @error('email')
                            <span style="color: var(--red-600); font-size: 0.75rem;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Kata Sandi Awal (Opsional)
                        </label>
                        <input type="password" name="password" placeholder="Kosongkan untuk kata sandi acak otomatis" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                        <span style="font-size: 0.725rem; color: var(--neutral-600); display: block; margin-top: 0.25rem;">
                            Peserta diwajibkan mengganti kata sandi pada saat pertama kali login.
                        </span>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Nomor WhatsApp / HP
                        </label>
                        <input type="text" name="no_hp" value="{{ old('no_hp') }}" placeholder="Contoh: 081234567890" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                    </div>
                </div>
            </div>

            {{-- 2. AKADEMIK --}}
            <div>
                <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900); margin: 0 0 1rem 0; padding-bottom: 0.5rem; border-bottom: 2px solid var(--blue-600); display: inline-block;">
                    2. Data Akademik & Institusi
                </h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Nomor Induk Siswa/Mahasiswa (NIM/NISN) <span style="color: var(--red-600);">*</span>
                        </label>
                        <input type="text" name="nim_nisn" value="{{ old('nim_nisn') }}" required placeholder="Contoh: 21120120140123" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Nama Sekolah / Perguruan Tinggi <span style="color: var(--red-600);">*</span>
                        </label>
                        <input type="text" name="institusi" value="{{ old('institusi') }}" required placeholder="Contoh: Universitas Gadjah Mada" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Jurusan / Program Studi <span style="color: var(--red-600);">*</span>
                        </label>
                        <select name="jurusan_id" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; background: var(--neutral-0); font-family: inherit;">
                            <option value="">-- Pilih Jurusan / Prodi --</option>
                            @foreach($jurusanList as $jurusan)
                                <option value="{{ $jurusan->id }}" {{ old('jurusan_id') == $jurusan->id ? 'selected' : '' }}>
                                    {{ $jurusan->nama_jurusan }} ({{ strtoupper($jurusan->kategori) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Jenjang Pendidikan <span style="color: var(--red-600);">*</span>
                        </label>
                        <select name="jenjang" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; background: var(--neutral-0); font-family: inherit;">
                            <option value="s1" {{ old('jenjang', 's1') === 's1' ? 'selected' : '' }}>S1 / Sarjana</option>
                            <option value="d3" {{ old('jenjang') === 'd3' ? 'selected' : '' }}>D3 / Diploma</option>
                            <option value="smk" {{ old('jenjang') === 'smk' ? 'selected' : '' }}>SMK / Kejuruan</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- 3. PENEMPATAN & PEMBIMBING --}}
            <div>
                <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900); margin: 0 0 1rem 0; padding-bottom: 0.5rem; border-bottom: 2px solid var(--blue-600); display: inline-block;">
                    3. Penempatan Dinas & Pembimbing Lapangan
                </h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Bidang Unit Kerja Penempatan
                        </label>
                        <select name="bidang_id" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; background: var(--neutral-0); font-family: inherit;">
                            <option value="">-- Tentukan Berdasarkan Bidang Pembimbing --</option>
                            @foreach($bidangList as $bidang)
                                <option value="{{ $bidang->id }}" {{ old('bidang_id') == $bidang->id ? 'selected' : '' }}>
                                    {{ $bidang->nama_bidang }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Pembimbing Lapangan <span style="color: var(--red-600);">*</span>
                        </label>
                        <select name="pembimbing_id" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; background: var(--neutral-0); font-family: inherit;">
                            <option value="">-- Pilih Pembimbing Lapangan --</option>
                            @foreach($pembimbingList as $p)
                                <option value="{{ $p->id }}" {{ old('pembimbing_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama }} ({{ $p->bidang?->nama_bidang ?? 'Umum' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Bulan Mulai Magang <span style="color: var(--red-600);">*</span>
                        </label>
                        <input type="month" name="bulan_mulai" value="{{ old('bulan_mulai', date('Y-m')) }}" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Durasi Magang (Bulan) <span style="color: var(--red-600);">*</span>
                        </label>
                        <input type="number" name="durasi_bulan" value="{{ old('durasi_bulan', 3) }}" min="1" max="12" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Alokasi Slot Khusus (Opsional)
                        </label>
                        <select name="slot_khusus_id" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; background: var(--neutral-0); font-family: inherit;">
                            <option value="">-- Tanpa Slot Khusus Spesifik --</option>
                            @foreach($slotKhususList as $slot)
                                <option value="{{ $slot->id }}" {{ old('slot_khusus_id') == $slot->id ? 'selected' : '' }}>
                                    Slot Khusus {{ $slot->bulan_mulai?->translatedFormat('F Y') }} (Sisa: {{ $slot->sisa }}/{{ $slot->jumlah }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Catatan / Rekomendasi (Opsional)
                        </label>
                        <input type="text" name="catatan" value="{{ old('catatan') }}" placeholder="Contoh: Program Kemitraan Kampus Merdeka" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                    </div>
                </div>
            </div>

            {{-- TOMBOL AKSI --}}
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1rem; border-top: 1px solid var(--neutral-200); padding-top: 1.25rem;">
                <a href="{{ route('superadmin.slot-khusus.index') }}" style="display: inline-flex; align-items: center; height: 42px; padding: 0 1.25rem; background: var(--neutral-100); border: 1px solid var(--neutral-400); border-radius: 8px; font-weight: 700; font-size: 0.875rem; color: var(--neutral-700); text-decoration: none;">
                    Batal
                </a>
                <button type="submit" style="display: inline-flex; align-items: center; gap: 0.5rem; height: 42px; padding: 0 1.75rem; background: var(--blue-600); color: #FFFFFF; border: none; border-radius: 8px; font-weight: 700; font-size: 0.875rem; cursor: pointer; box-shadow: var(--shadow-subtle);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    <span>Daftarkan & Tempatkan Peserta</span>
                </button>
            </div>
        </form>
    </x-gentelella.x-panel>

</div>

@push('scripts')
    @vite(['resources/js/pages/superadmin/slot-khusus.js'])
@endpush
@endsection

