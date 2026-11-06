@extends('layouts.auth')

@section('title', 'Register - Magang Kominfo Sleman')
@section('meta_description', 'Registrasi akun calon peserta magang / PKL Dinas Komunikasi dan Informatika Kabupaten Sleman.')

@section('content')

    @php
        $isKuotaLocked = (isset($selectedJurusan) && $selectedJurusan) || request('jurusan');
        $initialTipe = old('tipe_pendaftaran', $selectedTipe ?? request('tipe', 'mandiri'));
        $initialJumlah = (int) old('jumlah_kelompok', old('redirect_jumlah', $selectedJumlah ?? request('jumlah', 2)));
        if ($initialJumlah < 2 || $initialJumlah > 4) {
            $initialJumlah = 2;
        }
    @endphp

    <!-- Header Form Register -->
    <h1 class="auth-title">Register Akun</h1>
    <p class="auth-subtitle">Daftarkan akun Anda untuk memulai proses seleksi magang di Dinas Kominfo.</p>

    @if(isset($selectedJurusan) && $selectedJurusan)
        <!-- Kartu Ringkasan Kuota Pilihan (Ringkas & Bersih) -->
        <div style="background: linear-gradient(135deg, rgba(1, 52, 124, 0.05) 0%, rgba(63, 81, 181, 0.08) 100%); border: 1.5px solid rgba(1, 52, 124, 0.2); border-radius: 0.875rem; padding: 0.9rem 1.15rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.85rem;">
            <div style="width: 2.5rem; height: 2.5rem; border-radius: 0.625rem; background: var(--blue-600); color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 10px rgba(1, 52, 124, 0.25);">
                @if(($selectedTipe ?? request('tipe')) === 'kelompok')
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                @else
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                @endif
            </div>
            <div style="flex: 1; min-width: 0;">
                {{-- Baris 1: Nama Jurusan & Badge Tipe --}}
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; flex-wrap: wrap;">
                    <div style="font-size: 0.975rem; font-weight: 700; color: var(--neutral-900);">
                        {{ $selectedJurusan->nama_jurusan }} ({{ strtoupper($selectedJurusan->kategori) }})
                    </div>
                    <span style="font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.65rem; border-radius: 9999px; background: {{ ($selectedTipe ?? request('tipe')) === 'kelompok' ? '#dbeafe' : '#e0e7ff' }}; color: {{ ($selectedTipe ?? request('tipe')) === 'kelompok' ? '#1e40af' : '#3730a3' }};">
                        {{ ($selectedTipe ?? request('tipe')) === 'kelompok' ? 'Kelompok (' . ($selectedJumlah ?? request('jumlah', 2)) . ' Orang)' : 'Individu (1 Orang)' }}
                    </span>
                </div>

                {{-- Baris 2: Periode Mulai & Badge Status Sisa Kuota --}}
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; margin-top: 0.35rem; flex-wrap: wrap;">
                    @if($selectedBulan)
                        <div style="font-size: 0.8125rem; color: var(--neutral-600);">
                            Periode Mulai: <strong>{{ \Carbon\CarbonImmutable::parse($selectedBulan->bulan_mulai)->translatedFormat('F Y') }}</strong>
                        </div>
                    @endif
                    <div>
                        @if(($selectedSisaKuota ?? 0) >= (($selectedTipe ?? request('tipe')) === 'kelompok' ? ($selectedJumlah ?? request('jumlah', 2)) : 1))
                            <span style="display: inline-flex; align-items: center; gap: 0.3rem; background: #DCFCE7; color: #166534; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 9999px; font-size: 0.75rem;">
                                🟢 Sisa {{ $selectedSisaKuota }} Kursi Tersedia
                            </span>
                        @elseif(($selectedSisaKuota ?? 0) > 0)
                            <span style="display: inline-flex; align-items: center; gap: 0.3rem; background: #FEF3C7; color: #92400E; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 9999px; font-size: 0.75rem;">
                                🟡 Sisa {{ $selectedSisaKuota }} Kursi (Tim Masuk Waiting List)
                            </span>
                        @else
                            <span style="display: inline-flex; align-items: center; gap: 0.3rem; background: #FEE2E2; color: #991B1B; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 9999px; font-size: 0.75rem;">
                                🔴 Kuota Penuh (Waiting List)
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Form Registrasi Akun Baru -->
    <form action="{{ route('register') }}" method="POST" id="formRegister" novalidate>
        @csrf

        @if(request('bulan'))
            <input type="hidden" name="redirect_bulan" value="{{ request('bulan') }}">
        @endif
        @if(request('jurusan'))
            <input type="hidden" name="redirect_jurusan" value="{{ request('jurusan') }}">
        @endif
        @if(request('tipe'))
            <input type="hidden" name="redirect_tipe" value="{{ request('tipe') }}">
        @endif
        @if(request('jumlah'))
            <input type="hidden" name="redirect_jumlah" value="{{ request('jumlah') }}">
        @endif

        @if($isKuotaLocked)
            {{-- Mode Terkunci: Tipe & Kuota sudah dipilih dari Landing Page --}}
            <input type="hidden" name="tipe_pendaftaran" id="inputTipePendaftaran" value="{{ $initialTipe }}">
            <input type="hidden" name="jumlah_kelompok" id="inputJumlahKelompok" value="{{ $initialJumlah }}">
        @else
            {{-- Mode Pendaftaran Langsung: Pilihan Mandiri vs Kelompok (Maks. 4 Orang) --}}
            <div id="sectionPilihTipe" style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-700); margin-bottom: 0.5rem;">
                    Tipe Pendaftaran Magang
                </label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; background: #F1F5F9; padding: 0.25rem; border-radius: 0.625rem; border: 1px solid #CBD5E1;">
                    <label class="tipe-pilihan-btn" id="labelTipeMandiri" style="display: flex; align-items: center; justify-content: center; gap: 0.4rem; padding: 0.6rem 0.75rem; border-radius: 0.5rem; cursor: pointer; font-size: 0.875rem; font-weight: 600; transition: all 0.2s;">
                        <input type="radio" name="tipe_pendaftaran" value="mandiri" id="radioMandiri" style="display: none;" {{ $initialTipe === 'mandiri' ? 'checked' : '' }}>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        <span>Individu (1 Orang)</span>
                    </label>
                    <label class="tipe-pilihan-btn" id="labelTipeKelompok" style="display: flex; align-items: center; justify-content: center; gap: 0.4rem; padding: 0.6rem 0.75rem; border-radius: 0.5rem; cursor: pointer; font-size: 0.875rem; font-weight: 600; transition: all 0.2s;">
                        <input type="radio" name="tipe_pendaftaran" value="kelompok" id="radioKelompok" style="display: none;" {{ $initialTipe === 'kelompok' ? 'checked' : '' }}>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        <span>Kelompok (2–4 Orang)</span>
                    </label>
                </div>

                <!-- Selector Jumlah Tim yang Tegas (Hanya jika Kelompok dipilih secara manual) -->
                <div id="sectionPilihJumlahTim" style="display: {{ $initialTipe === 'kelompok' ? 'block' : 'none' }}; margin-top: 0.75rem; padding: 0.75rem 0.875rem; background: #EFF6FF; border: 1.5px solid #93C5FD; border-radius: 0.625rem;">
                    <div style="font-size: 0.8125rem; font-weight: 600; color: #1E40AF; margin-bottom: 0.4rem;">
                        Pilih Jumlah Anggota Tim (Termasuk Ketua, Maks. 4 Orang):
                    </div>
                    <div style="display: flex; gap: 0.5rem;">
                        <button type="button" class="btn-jumlah-pilihan {{ $initialJumlah === 2 ? 'is-active' : '' }}" data-jumlah="2" style="flex: 1; padding: 0.5rem; border-radius: 0.5rem; border: 1.5px solid {{ $initialJumlah === 2 ? 'var(--blue-600)' : '#CBD5E1' }}; background: {{ $initialJumlah === 2 ? 'var(--blue-600)' : '#fff' }}; color: {{ $initialJumlah === 2 ? '#fff' : '#1E293B' }}; font-size: 0.8125rem; font-weight: 700; cursor: pointer; transition: all 0.2s;">2 Orang</button>
                        <button type="button" class="btn-jumlah-pilihan {{ $initialJumlah === 3 ? 'is-active' : '' }}" data-jumlah="3" style="flex: 1; padding: 0.5rem; border-radius: 0.5rem; border: 1.5px solid {{ $initialJumlah === 3 ? 'var(--blue-600)' : '#CBD5E1' }}; background: {{ $initialJumlah === 3 ? 'var(--blue-600)' : '#fff' }}; color: {{ $initialJumlah === 3 ? '#fff' : '#1E293B' }}; font-size: 0.8125rem; font-weight: 700; cursor: pointer; transition: all 0.2s;">3 Orang</button>
                        <button type="button" class="btn-jumlah-pilihan {{ $initialJumlah === 4 ? 'is-active' : '' }}" data-jumlah="4" style="flex: 1; padding: 0.5rem; border-radius: 0.5rem; border: 1.5px solid {{ $initialJumlah === 4 ? 'var(--blue-600)' : '#CBD5E1' }}; background: {{ $initialJumlah === 4 ? 'var(--blue-600)' : '#fff' }}; color: {{ $initialJumlah === 4 ? '#fff' : '#1E293B' }}; font-size: 0.8125rem; font-weight: 700; cursor: pointer; transition: all 0.2s;">4 Orang</button>
                    </div>
                    <input type="hidden" name="jumlah_kelompok" id="inputJumlahKelompok" value="{{ $initialJumlah }}">
                </div>
            </div>
        @endif

        <!-- Section Khusus Kelompok: Nama Kelompok -->
        <div id="sectionNamaKelompok" style="display: none; margin-bottom: 1.25rem;">
            <div class="form-group">
                <label for="nama_kelompok" class="form-label sr-only">Nama Kelompok</label>
                <div class="input-wrapper">
                    <span class="input-icon-left" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </span>
                    <input 
                        type="text" 
                        id="nama_kelompok" 
                        name="nama_kelompok" 
                        value="{{ old('nama_kelompok') }}" 
                        class="form-input" 
                        placeholder="nama kelompok (contoh: Tim Inovasi Sleman)"
                        aria-label="Nama Kelompok"
                    >
                </div>
                <span class="form-hint">Nama kelompok yang akan tampil pada berkas pendaftaran & dashboard magang.</span>
            </div>
        </div>

        <!-- Header Field Akun Pendaftar / Ketua -->
        <div id="labelHeaderAkun" style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-700); margin-bottom: 0.5rem;">
            Identitas Akun Calon Peserta
        </div>

        <!-- Field Username / Nama Pendaftar -->
        <div class="form-group">
            <label for="username" class="form-label sr-only" id="labelUsernameText">Nama Lengkap / Username</label>
            <div class="input-wrapper">
                <span class="input-icon-left" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </span>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    value="{{ old('username', old('nama')) }}" 
                    required 
                    autofocus 
                    class="form-input" 
                    placeholder="nama lengkap"
                    autocomplete="name"
                    aria-label="Nama Lengkap"
                >
            </div>
        </div>

        <!-- Field Alamat Email Aktif -->
        <div class="form-group">
            <label for="email" class="form-label sr-only">Alamat Email Aktif</label>
            <div class="input-wrapper">
                <span class="input-icon-left" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                </span>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required 
                    class="form-input" 
                    placeholder="email"
                    autocomplete="email"
                    aria-label="Alamat Email Aktif"
                >
            </div>
            <span class="form-hint">Gunakan email aktif untuk verifikasi berkas dan notifikasi seleksi.</span>
        </div>

        <!-- Field Nomor Telepon -->
        <div class="form-group">
            <label for="no_telp" class="form-label sr-only">Nomor Telepon</label>
            <div class="input-wrapper">
                <span class="input-icon-left" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                    </svg>
                </span>
                <input 
                    type="tel" 
                    id="no_telp" 
                    name="no_telp" 
                    value="{{ old('no_telp') }}" 
                    required 
                    class="form-input" 
                    placeholder="no telp"
                    autocomplete="tel"
                    aria-label="Nomor Telepon"
                >
            </div>
            <span class="form-hint">Nomor WhatsApp aktif untuk koordinasi dan informasi seleksi.</span>
        </div>

        <!-- Field Kata Sandi -->
        <div class="form-group">
            <label for="password" class="form-label sr-only">Kata Sandi / Password</label>
            <div class="input-wrapper">
                <span class="input-icon-left" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                </span>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required 
                    class="form-input" 
                    placeholder="password"
                    autocomplete="new-password"
                    aria-label="Kata Sandi"
                    style="padding-right: 2.75rem;"
                >
                <button type="button" class="toggle-password-btn" data-target="password" aria-label="Tampilkan atau sembunyikan kata sandi">
                    <svg class="eye-closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                        <line x1="1" y1="1" x2="23" y2="23"></line>
                    </svg>
                </button>
            </div>
            <span class="form-hint">Minimal 8 karakter kombinasi huruf dan angka.</span>
        </div>

        <!-- Section Anggota Tim (Khusus Kelompok - FIX Sesuai Kuota Terpilih, Maksimal 4 Orang) -->
        <div id="sectionAnggotaTim" style="display: none; margin-bottom: 1.5rem; background: #F8FAFC; border: 1.5px solid #CBD5E1; border-radius: 0.875rem; padding: 1.125rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.875rem; flex-wrap: wrap; gap: 0.5rem;">
                <div>
                    <h3 style="font-size: 0.9rem; font-weight: 700; color: var(--navy, #02275D); margin: 0;">Anggota Kelompok (<span id="textTotalAnggota">2</span> Orang)</h3>
                    <p style="font-size: 0.75rem; color: var(--neutral-600, #475569); margin: 0.2rem 0 0;">Ketua tim otomatis sebagai Anggota 1. Data rekan tim Anda diisi lengkap sesuai kuota fix.</p>
                </div>
                <span id="badgeJumlahAnggota" style="font-size: 0.75rem; font-weight: 700; background: #DBEAFE; color: #1E40AF; padding: 0.2rem 0.65rem; border-radius: 9999px;">
                    Slot Fix: <span id="textTotalBadge">2</span> Orang
                </span>
            </div>

            <!-- List Kartu Anggota Fix Sesuai Kuota Terpilih -->
            <div id="containerAnggotaRekan" style="display: flex; flex-direction: column; gap: 0.875rem;">
                <!-- Baris Anggota 1 (Ketua - Preview Ringkas) -->
                <div style="background: #FFFFFF; border: 1.5px dashed #93C5FD; border-radius: 0.625rem; padding: 0.75rem 0.875rem; display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 0.625rem;">
                        <span style="width: 24px; height: 24px; border-radius: 50%; background: #0751FF; color: #fff; font-size: 0.75rem; font-weight: 700; display: flex; align-items: center; justify-content: center;">1</span>
                        <div>
                            <div style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-900, #0F172A);" id="previewKetuaNama">Nama Ketua Tim</div>
                            <div style="font-size: 0.75rem; color: #0751FF; font-weight: 600;">Ketua Tim / Pemilik Akun</div>
                        </div>
                    </div>
                </div>

                <!-- Kartu Anggota 2 (Rekan 1) -->
                <div class="card-anggota-rekan" id="cardAnggota2" data-index="0" style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 0.625rem; padding: 0.875rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.625rem;">
                        <span class="label-anggota-ke" style="font-size: 0.8125rem; font-weight: 700; color: var(--blue-700, #003566);">Anggota 2</span>
                        <span style="font-size: 0.7rem; color: #DC2626; font-weight: 600;">*Wajib</span>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.625rem; margin-bottom: 0.5rem;">
                        <div>
                            <input type="text" id="inputAnggota0Nama" name="anggota[0][nama]" value="{{ old('anggota.0.nama') }}" class="form-input input-anggota-field" placeholder="Nama Lengkap *" style="font-size: 0.8125rem; padding: 0.55rem 0.75rem;">
                        </div>
                        <div>
                            <input type="text" id="inputAnggota0Nim" name="anggota[0][nim_nisn]" value="{{ old('anggota.0.nim_nisn') }}" class="form-input input-anggota-field" placeholder="NIM / NISN *" style="font-size: 0.8125rem; padding: 0.55rem 0.75rem;">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.625rem;">
                        <div>
                            <input type="tel" id="inputAnggota0Hp" name="anggota[0][no_hp]" value="{{ old('anggota.0.no_hp') }}" class="form-input input-anggota-field" placeholder="No. WhatsApp / HP *" style="font-size: 0.8125rem; padding: 0.55rem 0.75rem;">
                        </div>
                        <div>
                            <input type="email" id="inputAnggota0Email" name="anggota[0][email]" value="{{ old('anggota.0.email') }}" class="form-input input-anggota-field" placeholder="Email Aktif *" style="font-size: 0.8125rem; padding: 0.55rem 0.75rem;">
                        </div>
                    </div>
                </div>

                <!-- Kartu Anggota 3 (Rekan 2) -->
                <div class="card-anggota-rekan" id="cardAnggota3" data-index="1" style="display: none; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 0.625rem; padding: 0.875rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.625rem;">
                        <span class="label-anggota-ke" style="font-size: 0.8125rem; font-weight: 700; color: var(--blue-700, #003566);">Anggota 3</span>
                        <span style="font-size: 0.7rem; color: #DC2626; font-weight: 600;">*Wajib</span>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.625rem; margin-bottom: 0.5rem;">
                        <div>
                            <input type="text" id="inputAnggota1Nama" name="anggota[1][nama]" value="{{ old('anggota.1.nama') }}" class="form-input input-anggota-field" placeholder="Nama Lengkap *" style="font-size: 0.8125rem; padding: 0.55rem 0.75rem;">
                        </div>
                        <div>
                            <input type="text" id="inputAnggota1Nim" name="anggota[1][nim_nisn]" value="{{ old('anggota.1.nim_nisn') }}" class="form-input input-anggota-field" placeholder="NIM / NISN *" style="font-size: 0.8125rem; padding: 0.55rem 0.75rem;">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.625rem;">
                        <div>
                            <input type="tel" id="inputAnggota1Hp" name="anggota[1][no_hp]" value="{{ old('anggota.1.no_hp') }}" class="form-input input-anggota-field" placeholder="No. WhatsApp / HP *" style="font-size: 0.8125rem; padding: 0.55rem 0.75rem;">
                        </div>
                        <div>
                            <input type="email" id="inputAnggota1Email" name="anggota[1][email]" value="{{ old('anggota.1.email') }}" class="form-input input-anggota-field" placeholder="Email Aktif *" style="font-size: 0.8125rem; padding: 0.55rem 0.75rem;">
                        </div>
                    </div>
                </div>

                <!-- Kartu Anggota 4 (Rekan 3) -->
                <div class="card-anggota-rekan" id="cardAnggota4" data-index="2" style="display: none; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 0.625rem; padding: 0.875rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.625rem;">
                        <span class="label-anggota-ke" style="font-size: 0.8125rem; font-weight: 700; color: var(--blue-700, #003566);">Anggota 4</span>
                        <span style="font-size: 0.7rem; color: #DC2626; font-weight: 600;">*Wajib</span>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.625rem; margin-bottom: 0.5rem;">
                        <div>
                            <input type="text" id="inputAnggota2Nama" name="anggota[2][nama]" value="{{ old('anggota.2.nama') }}" class="form-input input-anggota-field" placeholder="Nama Lengkap *" style="font-size: 0.8125rem; padding: 0.55rem 0.75rem;">
                        </div>
                        <div>
                            <input type="text" id="inputAnggota2Nim" name="anggota[2][nim_nisn]" value="{{ old('anggota.2.nim_nisn') }}" class="form-input input-anggota-field" placeholder="NIM / NISN *" style="font-size: 0.8125rem; padding: 0.55rem 0.75rem;">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.625rem;">
                        <div>
                            <input type="tel" id="inputAnggota2Hp" name="anggota[2][no_hp]" value="{{ old('anggota.2.no_hp') }}" class="form-input input-anggota-field" placeholder="No. WhatsApp / HP *" style="font-size: 0.8125rem; padding: 0.55rem 0.75rem;">
                        </div>
                        <div>
                            <input type="email" id="inputAnggota2Email" name="anggota[2][email]" value="{{ old('anggota.2.email') }}" class="form-input input-anggota-field" placeholder="Email Aktif *" style="font-size: 0.8125rem; padding: 0.55rem 0.75rem;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seksi Informasi Akademik & Pilihan Jurusan -->
        <div id="sectionPendidikan" style="margin-bottom: 1.25rem;">
            <div style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-700); margin-bottom: 0.5rem; display: flex; align-items: center; justify-content: space-between;">
                <span>Informasi Pendidikan & Pilihan Jurusan</span>
                @if(!$isKuotaLocked)
                    <a href="{{ route('landing') }}#kuota" style="font-size: 0.75rem; font-weight: 600; color: var(--blue-600); text-decoration: none;">
                        Cek Kuota Terbuka &rarr;
                    </a>
                @endif
            </div>

            @if($isKuotaLocked && isset($selectedJurusan) && $selectedJurusan)
                {{-- Jika kuota sudah dipilih dari landing page, kunci jenjang & jurusan --}}
                <input type="hidden" name="jenjang" value="{{ $selectedJurusan->kategori === 'smk' ? 'smk' : 's1' }}">
                <input type="hidden" name="jurusan_id" value="{{ $selectedJurusan->id }}">
            @else
                <!-- Field Jenjang Pendidikan -->
                <div class="form-group">
                    <label for="selectJenjang" class="form-label sr-only">Jenjang Pendidikan</label>
                    <div class="input-wrapper">
                        <span class="input-icon-left" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </span>
                        <select 
                            id="selectJenjang" 
                            name="jenjang" 
                            class="form-input" 
                            style="padding-left: 2.75rem;"
                            aria-label="Jenjang Pendidikan"
                        >
                            <option value="">-- Pilih Jenjang Pendidikan --</option>
                            <option value="smk" {{ old('jenjang') === 'smk' ? 'selected' : '' }}>SMK (Siswa)</option>
                            <option value="d3" {{ old('jenjang') === 'd3' ? 'selected' : '' }}>Diploma 3 (D3)</option>
                            <option value="s1" {{ old('jenjang') === 's1' ? 'selected' : '' }}>Sarjana / D4 (S1)</option>
                        </select>
                    </div>
                    <span class="form-hint">Pilih jenjang pendidikan formal yang sedang Anda tempuh saat ini.</span>
                </div>

                <!-- Field Jurusan & Bidang Penempatan -->
                <div class="form-group">
                    <label for="selectJurusan" class="form-label sr-only">Jurusan / Program Studi</label>
                    <div class="input-wrapper">
                        <span class="input-icon-left" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </span>
                        <select 
                            id="selectJurusan" 
                            name="jurusan_id" 
                            class="form-input" 
                            style="padding-left: 2.75rem;"
                            aria-label="Jurusan atau Program Studi"
                        >
                            <option value="">-- Pilih Jurusan / Program Studi --</option>
                            @if(isset($jurusanList))
                                @foreach($jurusanList as $jur)
                                    @php
                                        $bidangNames = $jur->bidang->pluck('nama_bidang')->implode(', ');
                                        $sisa = (int) ($jur->total_sisa_kuota ?? 0);
                                        $sisaLabel = $sisa > 0 ? "Sisa {$sisa} Kursi" : 'Waiting List';
                                    @endphp
                                    <option 
                                        value="{{ $jur->id }}" 
                                        data-kategori="{{ $jur->kategori }}"
                                        data-sisa="{{ $sisa }}"
                                        data-kapasitas="{{ (int) ($jur->total_kapasitas ?? 0) }}"
                                        {{ old('jurusan_id') == $jur->id ? 'selected' : '' }}
                                    >
                                        {{ $jur->nama_jurusan }} ({{ strtoupper($jur->kategori) }} • {{ $sisaLabel }}{{ $bidangNames ? ' • ' . $bidangNames : '' }})
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <span class="form-hint">Jurusan akan disaring sesuai jenjang yang dipilih di atas.</span>
                    <div id="badgeStatusKuotaJurusan" style="display: none; margin-top: 0.5rem; padding: 0.55rem 0.75rem; border-radius: 0.5rem; font-size: 0.775rem; line-height: 1.4;"></div>
                </div>
            @endif

            <!-- Field Nama Sekolah / Perguruan Tinggi (Institusi) -->
            <div class="form-group">
                <label for="institusi" class="form-label sr-only">Nama Sekolah / Perguruan Tinggi</label>
                <div class="input-wrapper">
                    <span class="input-icon-left" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </span>
                    <input 
                        type="text" 
                        id="institusi" 
                        name="institusi" 
                        value="{{ old('institusi') }}" 
                        class="form-input" 
                        placeholder="nama sekolah / perguruan tinggi (contoh: SMKN 2 Depok Sleman / UGM)"
                        aria-label="Nama Sekolah atau Perguruan Tinggi"
                    >
                </div>
            </div>

            <!-- Field NIM / NISN -->
            <div class="form-group">
                <label for="nim_nisn" class="form-label sr-only">NIM / NISN</label>
                <div class="input-wrapper">
                    <span class="input-icon-left" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                        </svg>
                    </span>
                    <input 
                        type="text" 
                        id="nim_nisn" 
                        name="nim_nisn" 
                        value="{{ old('nim_nisn') }}" 
                        class="form-input" 
                        placeholder="NIM / NISN"
                        aria-label="Nomor Induk Mahasiswa atau Siswa"
                    >
                </div>
            </div>

            <!-- Checkbox Kepatuhan UU PDP (Persetujuan Orang Tua/Wali Khusus SMK) -->
            <div id="sectionPersetujuanWali" style="display: {{ old('jenjang') === 'smk' ? 'block' : 'none' }}; margin-top: 0.75rem; padding: 0.75rem 0.875rem; background: #F8FAFC; border: 1.5px solid #CBD5E1; border-radius: 0.625rem;">
                <label style="display: flex; align-items: flex-start; gap: 0.5rem; cursor: pointer; font-size: 0.8rem; color: var(--neutral-800); line-height: 1.4;">
                    <input type="checkbox" name="persetujuan_wali" id="persetujuan_wali" value="1" {{ old('persetujuan_wali') ? 'checked' : '' }} style="margin-top: 0.15rem; accent-color: var(--blue-600);">
                    <span>
                        <strong>Persetujuan Orang Tua / Wali (Khusus Jenjang SMK):</strong><br>
                        Saya menyatakan telah mendapatkan izin sah dari orang tua/wali untuk mendaftar magang dan memproses data administrasi sesuai ketentuan UU PDP No. 27/2022.
                    </span>
                </label>
            </div>
        </div>

        <!-- Checkbox Persetujuan Kebijakan Privasi -->
        <div style="margin-bottom: 1.5rem; font-size: 0.8125rem;">
            <label style="display: flex; align-items: flex-start; gap: 0.625rem; cursor: pointer; color: var(--neutral-600); line-height: 1.4;">
                <input type="checkbox" name="setuju_privasi" id="setuju_privasi" required checked style="width: 1.1rem; height: 1.1rem; accent-color: var(--blue-600); cursor: pointer; margin-top: 0.125rem;">
                <span>
                    Saya menyetujui <a href="{{ route('kebijakan-privasi') }}" target="_blank" style="color: var(--blue-600); font-weight: 600; text-decoration: underline;">Kebijakan Privasi</a> Dinas Komunikasi dan Informatika Kabupaten Sleman.
                </span>
            </label>
        </div>

        <!-- Cloudflare Turnstile CAPTCHA jika Diaktifkan -->
        @if(config('services.turnstile.enabled'))
            <div style="margin-bottom: 1.25rem;">
                <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}"></div>
                <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
            </div>
        @endif

        <!-- Dynamic SVG Security CAPTCHA (4 Kode) -->
        <x-captcha-field />

        <!-- Tombol Submit Register -->
        <button type="submit" class="btn-primary" id="btn-submit-register">
            Register
        </button>
    </form>

    <!-- Divider Pemisah -->
    <div class="auth-divider">
        <span>atau</span>
    </div>

    <!-- Tombol Daftar dengan Google OAuth -->
    <a href="{{ route('oauth.redirect', 'google') }}" class="btn-google" id="btn-register-google" title="Daftar dengan Google" aria-label="Daftar dengan Google">
        <svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
        </svg>
        <span>Register dengan Google</span>
    </a>

    <!-- Footer Tautan Switch ke Halaman Login -->
    <p class="auth-footer-text">
        Sudah punya akun? <a href="{{ route('login') }}">Login</a>
    </p>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isKuotaLocked = {{ $isKuotaLocked ? 'true' : 'false' }};
            let currentTipe = '{{ $initialTipe }}';
            let currentJumlah = {{ $initialJumlah }};

            const inputTipePendaftaran = document.getElementById('inputTipePendaftaran');
            const inputJumlahKelompok = document.getElementById('inputJumlahKelompok');
            const radioMandiri = document.getElementById('radioMandiri');
            const radioKelompok = document.getElementById('radioKelompok');
            const labelMandiri = document.getElementById('labelTipeMandiri');
            const labelKelompok = document.getElementById('labelTipeKelompok');
            const sectionPilihJumlahTim = document.getElementById('sectionPilihJumlahTim');
            const btnJumlahPills = document.querySelectorAll('.btn-jumlah-pilihan');

            const sectionNamaKelompok = document.getElementById('sectionNamaKelompok');
            const sectionAnggotaTim = document.getElementById('sectionAnggotaTim');
            const labelHeaderAkun = document.getElementById('labelHeaderAkun');
            const inputUsername = document.getElementById('username');
            const previewKetuaNama = document.getElementById('previewKetuaNama');
            const inputNamaKelompok = document.getElementById('nama_kelompok');

            const cardAnggota2 = document.getElementById('cardAnggota2');
            const cardAnggota3 = document.getElementById('cardAnggota3');
            const cardAnggota4 = document.getElementById('cardAnggota4');
            const textTotalAnggota = document.getElementById('textTotalAnggota');
            const textTotalBadge = document.getElementById('textTotalBadge');

            function applyJumlah(total) {
                total = parseInt(total, 10);
                if (isNaN(total) || total < 2 || total > 4) {
                    total = 2;
                }
                currentJumlah = total;
                if (inputJumlahKelompok) {
                    inputJumlahKelompok.value = total;
                }
                if (textTotalAnggota) textTotalAnggota.textContent = total;
                if (textTotalBadge) textTotalBadge.textContent = total;

                // Update styling tombol jumlah tim jika ada
                btnJumlahPills.forEach(btn => {
                    const btnJml = parseInt(btn.getAttribute('data-jumlah'), 10);
                    const isActive = (btnJml === total);
                    btn.classList.toggle('is-active', isActive);
                    btn.style.borderColor = isActive ? 'var(--blue-600)' : '#CBD5E1';
                    btn.style.background = isActive ? 'var(--blue-600)' : '#FFFFFF';
                    btn.style.color = isActive ? '#FFFFFF' : '#1E293B';
                });

                const isKelompok = (currentTipe === 'kelompok');

                // Konfigurasi kartu rekan 1 (Anggota 2) -> Aktif jika kelompok
                if (cardAnggota2) {
                    cardAnggota2.style.display = isKelompok ? 'block' : 'none';
                    cardAnggota2.querySelectorAll('.input-anggota-field').forEach(input => {
                        input.disabled = !isKelompok;
                        if (isKelompok) {
                            input.setAttribute('required', 'required');
                        } else {
                            input.removeAttribute('required');
                        }
                    });
                }

                // Konfigurasi kartu rekan 2 (Anggota 3) -> Aktif jika total >= 3
                const showCard3 = isKelompok && (total >= 3);
                if (cardAnggota3) {
                    cardAnggota3.style.display = showCard3 ? 'block' : 'none';
                    cardAnggota3.querySelectorAll('.input-anggota-field').forEach(input => {
                        input.disabled = !showCard3;
                        if (showCard3) {
                            input.setAttribute('required', 'required');
                        } else {
                            input.removeAttribute('required');
                        }
                    });
                }

                // Konfigurasi kartu rekan 3 (Anggota 4) -> Aktif jika total >= 4
                const showCard4 = isKelompok && (total >= 4);
                if (cardAnggota4) {
                    cardAnggota4.style.display = showCard4 ? 'block' : 'none';
                    cardAnggota4.querySelectorAll('.input-anggota-field').forEach(input => {
                        input.disabled = !showCard4;
                        if (showCard4) {
                            input.setAttribute('required', 'required');
                        } else {
                            input.removeAttribute('required');
                        }
                    });
                }

                updateKuotaInfo();
            }

            function applyTipe(tipe) {
                currentTipe = tipe;
                const isKelompok = (tipe === 'kelompok');

                if (inputTipePendaftaran) {
                    inputTipePendaftaran.value = tipe;
                }

                if (!isKuotaLocked) {
                    if (isKelompok) {
                        if (radioKelompok) radioKelompok.checked = true;
                        if (labelKelompok) {
                            labelKelompok.style.background = '#FFFFFF';
                            labelKelompok.style.color = 'var(--blue-600)';
                            labelKelompok.style.boxShadow = '0 1px 3px rgba(0,0,0,0.1)';
                            labelKelompok.style.border = '1px solid #93C5FD';
                        }
                        if (labelMandiri) {
                            labelMandiri.style.background = 'transparent';
                            labelMandiri.style.color = '#64748B';
                            labelMandiri.style.boxShadow = 'none';
                            labelMandiri.style.border = '1px solid transparent';
                        }
                        if (sectionPilihJumlahTim) sectionPilihJumlahTim.style.display = 'block';
                    } else {
                        if (radioMandiri) radioMandiri.checked = true;
                        if (labelMandiri) {
                            labelMandiri.style.background = '#FFFFFF';
                            labelMandiri.style.color = 'var(--blue-600)';
                            labelMandiri.style.boxShadow = '0 1px 3px rgba(0,0,0,0.1)';
                            labelMandiri.style.border = '1px solid #93C5FD';
                        }
                        if (labelKelompok) {
                            labelKelompok.style.background = 'transparent';
                            labelKelompok.style.color = '#64748B';
                            labelKelompok.style.boxShadow = 'none';
                            labelKelompok.style.border = '1px solid transparent';
                        }
                        if (sectionPilihJumlahTim) sectionPilihJumlahTim.style.display = 'none';
                    }
                }

                if (isKelompok) {
                    if (sectionNamaKelompok) sectionNamaKelompok.style.display = 'block';
                    if (sectionAnggotaTim) sectionAnggotaTim.style.display = 'block';
                    if (labelHeaderAkun) labelHeaderAkun.textContent = 'Identitas Akun Ketua Kelompok';
                    if (inputUsername) inputUsername.placeholder = 'nama lengkap ketua tim';
                    if (inputNamaKelompok) inputNamaKelompok.setAttribute('required', 'required');
                } else {
                    if (sectionNamaKelompok) sectionNamaKelompok.style.display = 'none';
                    if (sectionAnggotaTim) sectionAnggotaTim.style.display = 'none';
                    if (labelHeaderAkun) labelHeaderAkun.textContent = 'Identitas Akun Calon Peserta';
                    if (inputUsername) inputUsername.placeholder = 'nama lengkap';
                    if (inputNamaKelompok) inputNamaKelompok.removeAttribute('required');
                }

                applyJumlah(currentJumlah);
            }

            if (!isKuotaLocked) {
                if (labelMandiri) {
                    labelMandiri.addEventListener('click', function() {
                        applyTipe('mandiri');
                    });
                }
                if (labelKelompok) {
                    labelKelompok.addEventListener('click', function() {
                        applyTipe('kelompok');
                    });
                }

                btnJumlahPills.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const targetJumlah = parseInt(this.getAttribute('data-jumlah'), 10);
                        applyJumlah(targetJumlah);
                    });
                });
            }

            if (inputUsername && previewKetuaNama) {
                inputUsername.addEventListener('input', function() {
                    previewKetuaNama.textContent = this.value.trim() ? this.value.trim() : 'Nama Ketua Tim';
                });
                if (inputUsername.value.trim()) {
                    previewKetuaNama.textContent = inputUsername.value.trim();
                }
            }

            // Filter Jurusan & Persetujuan Wali berdasarkan Jenjang
            const selectJenjang = document.getElementById('selectJenjang');
            const selectJurusan = document.getElementById('selectJurusan');
            const sectionPersetujuanWali = document.getElementById('sectionPersetujuanWali');
            const badgeStatusKuotaJurusan = document.getElementById('badgeStatusKuotaJurusan');

            function updateKuotaInfo() {
                if (!badgeStatusKuotaJurusan || !selectJurusan) return;

                const selectedVal = selectJurusan.value;
                if (!selectedVal) {
                    badgeStatusKuotaJurusan.style.display = 'none';
                    return;
                }

                const selectedOpt = selectJurusan.querySelector(`option[value="${selectedVal}"]`);
                if (!selectedOpt || selectedOpt.disabled) {
                    badgeStatusKuotaJurusan.style.display = 'none';
                    return;
                }

                const sisa = parseInt(selectedOpt.getAttribute('data-sisa') || '0', 10);
                const kapasitas = parseInt(selectedOpt.getAttribute('data-kapasitas') || '0', 10);
                const isKelompok = (currentTipe === 'kelompok');
                const targetJumlah = isKelompok ? currentJumlah : 1;

                badgeStatusKuotaJurusan.style.display = 'block';

                if (isKelompok) {
                    if (sisa >= targetJumlah) {
                        badgeStatusKuotaJurusan.style.background = '#DCFCE7';
                        badgeStatusKuotaJurusan.style.border = '1px solid #86EFAC';
                        badgeStatusKuotaJurusan.style.color = '#166534';
                        badgeStatusKuotaJurusan.innerHTML = `<strong>🟢 Kuota Tim Cukup:</strong> Tersedia <strong>${sisa}</strong> dari ${kapasitas} kursi (mencukupi untuk tim ${targetJumlah} orang).`;
                    } else if (sisa > 0) {
                        badgeStatusKuotaJurusan.style.background = '#FEF3C7';
                        badgeStatusKuotaJurusan.style.border = '1px solid #FCD34D';
                        badgeStatusKuotaJurusan.style.color = '#92400E';
                        badgeStatusKuotaJurusan.innerHTML = `<strong>🟡 Masuk Waiting List:</strong> Sisa kuota hanya <strong>${sisa}</strong> kursi, tidak mencukupi untuk tim ${targetJumlah} orang. Pendaftaran tim dialihkan ke antrean Waiting List.`;
                    } else {
                        badgeStatusKuotaJurusan.style.background = '#FEE2E2';
                        badgeStatusKuotaJurusan.style.border = '1px solid #FCA5A5';
                        badgeStatusKuotaJurusan.style.color = '#991B1B';
                        badgeStatusKuotaJurusan.innerHTML = `<strong>🔴 Kuota Penuh:</strong> Kuota umum jurusan ini telah terisi penuh. Pendaftaran tim otomatis masuk antrean Waiting List.`;
                    }
                } else {
                    if (sisa > 0) {
                        badgeStatusKuotaJurusan.style.background = '#DCFCE7';
                        badgeStatusKuotaJurusan.style.border = '1px solid #86EFAC';
                        badgeStatusKuotaJurusan.style.color = '#166534';
                        badgeStatusKuotaJurusan.innerHTML = `<strong>🟢 Kuota Tersedia:</strong> Sisa <strong>${sisa}</strong> dari ${kapasitas} kursi untuk periode magang aktif.`;
                    } else {
                        badgeStatusKuotaJurusan.style.background = '#FEF3C7';
                        badgeStatusKuotaJurusan.style.border = '1px solid #FCD34D';
                        badgeStatusKuotaJurusan.style.color = '#92400E';
                        badgeStatusKuotaJurusan.innerHTML = `<strong>🟡 Kuota Penuh:</strong> Sisa 0 kursi. Pendaftaran Anda tetap dapat diproses melalui antrean <strong>Waiting List</strong>.`;
                    }
                }
            }

            function filterJurusanByJenjang(jenjang) {
                if (!selectJurusan) return;

                const options = selectJurusan.querySelectorAll('option');
                const targetKategori = (jenjang === 'smk') ? 'smk' : (jenjang ? 'mahasiswa' : '');

                options.forEach(opt => {
                    if (!opt.value) {
                        opt.style.display = 'block';
                        return;
                    }
                    const optKategori = opt.getAttribute('data-kategori');
                    if (!targetKategori || optKategori === targetKategori) {
                        opt.style.display = 'block';
                        opt.disabled = false;
                    } else {
                        opt.style.display = 'none';
                        opt.disabled = true;
                    }
                });

                const currentSelectedOpt = selectJurusan.querySelector(`option[value="${selectJurusan.value}"]`);
                if (currentSelectedOpt && currentSelectedOpt.disabled) {
                    selectJurusan.value = '';
                }

                if (sectionPersetujuanWali) {
                    sectionPersetujuanWali.style.display = (jenjang === 'smk') ? 'block' : 'none';
                }

                updateKuotaInfo();
            }

            if (selectJenjang) {
                selectJenjang.addEventListener('change', function() {
                    filterJurusanByJenjang(this.value);
                });
                if (selectJenjang.value) {
                    filterJurusanByJenjang(selectJenjang.value);
                }
            }

            if (selectJurusan) {
                selectJurusan.addEventListener('change', function() {
                    updateKuotaInfo();
                });
                if (selectJurusan.value) {
                    updateKuotaInfo();
                }
            }

            // Inisialisasi awal
            applyTipe(currentTipe);
        });
    </script>
@endsection
