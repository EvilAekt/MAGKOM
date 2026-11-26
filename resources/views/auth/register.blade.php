@extends('layouts.auth-depth', ['variant' => 'register'])

@section('title', 'MagKom - Register')

@section('auth-form')
@php
    $jurusanMasterData = [];
    if (isset($jurusanList) && count($jurusanList) > 0) {
        foreach ($jurusanList as $jur) {
            $bIds = [];
            if (isset($bidangList)) {
                foreach ($bidangList as $bid) {
                    if ($bid->jurusan && $bid->jurusan->contains('id', $jur->id)) {
                        $bIds[] = (string) $bid->id;
                    }
                }
            }
            $jurusanMasterData[] = [
                'id' => (string) $jur->id,
                'nama' => $jur->nama_jurusan,
                'kategori' => strtolower($jur->kategori ?? ''),
                'bidang_ids' => $bIds,
            ];
        }
    } else {
        $jurusanMasterData = [
            ['id' => '1', 'nama' => 'Teknik Komputer dan Jaringan', 'kategori' => 'smk', 'bidang_ids' => ['aptika', 'jaringan']],
            ['id' => '2', 'nama' => 'Rekayasa Perangkat Lunak', 'kategori' => 'smk', 'bidang_ids' => ['aptika', 'egov']],
            ['id' => '3', 'nama' => 'Multimedia / Desain Komunikasi Visual', 'kategori' => 'smk', 'bidang_ids' => ['ikp']],
            ['id' => '4', 'nama' => 'Teknik Informatika', 'kategori' => 'mahasiswa', 'bidang_ids' => ['aptika', 'egov']],
            ['id' => '5', 'nama' => 'Sistem Informasi', 'kategori' => 'mahasiswa', 'bidang_ids' => ['aptika', 'egov']],
            ['id' => '6', 'nama' => 'Ilmu Komunikasi / Jurnalistik', 'kategori' => 'mahasiswa', 'bidang_ids' => ['ikp']],
            ['id' => '7', 'nama' => 'Manajemen Informatika', 'kategori' => 'mahasiswa', 'bidang_ids' => ['aptika', 'egov']],
            ['id' => '8', 'nama' => 'Teknik Komputer / Jaringan', 'kategori' => 'mahasiswa', 'bidang_ids' => ['jaringan']],
        ];
    }

    $maksAnggota = (int) ($maksAnggotaKelompok ?? 4);
    $maksRekanTambahan = max(1, $maksAnggota - 1);

    $isKuotaTerpilih = (isset($selectedJurusan) && $selectedJurusan) || (isset($selectedBulan) && $selectedBulan) || request()->filled('bulan') || request()->filled('jurusan') || request()->filled('bidang');
    $isJumlahLocked = $isKuotaTerpilih && (request()->filled('jumlah') || request()->filled('redirect_jumlah'));
    $initialTipe = old('tipe_pendaftaran', $selectedTipe ?? request('tipe', 'mandiri'));
    $oldAnggotaCount = is_array(old('anggota')) ? count(old('anggota')) : 0;
    $initialJumlah = (int) old('redirect_jumlah', old('jumlah_kelompok', $selectedJumlah ?? request('jumlah', ($initialTipe === 'kelompok' ? max(2, $oldAnggotaCount + 1) : 1))));
    if ($initialJumlah < 1 || $initialJumlah > $maksAnggota) {
        $initialJumlah = ($initialTipe === 'kelompok' ? max(2, $oldAnggotaCount + 1) : 1);
    }
    $initialJenjang = old('jenjang', request('jenjang', ($selectedJurusan?->kategori === 'smk' ? 'smk' : ($selectedJurusan ? 'mahasiswa' : ''))));
    $initialBidangId = old('bidang_id', request('bidang', $selectedBidang?->id));
    $initialJurusanId = old('jurusan_id', request('jurusan', $selectedJurusan?->id));
    $initialBulanId = old('bulan_magang_id', request('bulan', $selectedBulan?->id));
@endphp

<section class="auth-panel-scroll" style="flex: 1 1 440px; min-height: 560px; position: relative; box-sizing: border-box; padding: 44px 40px 32px; max-height: 800px; overflow-y: auto; scrollbar-width: thin; scrollbar-color: #AAB6C8 transparent; display: flex; flex-direction: column; background: #FFFFFF">
  <div style="display: flex; flex-direction: column; width: 100%; max-width: 460px; margin: 0 auto; box-sizing: border-box">

    @if(!($isPendaftaranDibuka ?? true))
      <div data-register-locked="true" style="margin: 24px 0; padding: 36px 28px; border-radius: 16px; background: #F8FAFC; border: 1.5px solid #E2E8F0; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 14px">
        <div style="width: 68px; height: 68px; border-radius: 50%; background: #E2E8F0; color: #475569; display: flex; align-items: center; justify-content: center">
          <svg aria-hidden="true" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
          </svg>
        </div>
        <h1 style="margin: 4px 0 0; font-size: 26px; line-height: 1.2; font-weight: 800; color: #003566">Pendaftaran Ditutup</h1>
        <p style="margin: 0; font-size: 14.5px; line-height: 1.6; color: #5F6B76; max-width: 360px">
          Pendaftaran akun calon peserta magang Diskominfo Sleman saat ini sedang ditutup karena belum ada periode aktif yang dibuka.
        </p>
        <div style="display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; margin-top: 8px; width: 100%">
          <a href="{{ route('login') }}" style="display: inline-flex; align-items: center; justify-content: center; height: 46px; padding: 0 22px; border-radius: 10px; background: #003566; border-bottom: 3px solid #FFC300; color: #FFFFFF; font-size: 14.5px; font-weight: 700; text-decoration: none">Kembali ke Login</a>
          <a href="{{ url('/') }}" style="display: inline-flex; align-items: center; justify-content: center; height: 46px; padding: 0 20px; border-radius: 10px; background: #FFFFFF; border: 1.5px solid #CBD5E1; color: #334155; font-size: 14.5px; font-weight: 700; text-decoration: none">Lihat Beranda</a>
        </div>
      </div>
    @else
    <div style="margin-bottom: 24px">
      <h1 style="margin: 0 0 8px; font-size: 34px; line-height: 1.15; font-weight: 800; letter-spacing: -0.02em; color: #003566">Register Akun</h1>
      <p style="margin: 0; font-size: 15px; line-height: 1.5; color: #5F6B76">Daftarkan akun Anda untuk memulai proses seleksi magang di Dinas Kominfo.</p>
    </div>

    @if($isKuotaTerpilih)
      @include('auth.partials._selected_quota_card', [
        'selectedJurusan' => $selectedJurusan,
        'selectedBulan' => $selectedBulan,
        'selectedBidang' => $selectedBidang,
        'initialTipe' => $initialTipe,
        'initialJumlah' => $initialJumlah,
      ])
    @endif

    @if(session('warning'))
      <div style="margin-bottom: 20px; padding: 12px 16px; border-radius: 10px; background: #FEF3C7; border: 1px solid #FCD34D; color: #92400E; font-size: 13.5px; line-height: 1.5; display: flex; align-items: flex-start; gap: 8px">
        <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 1px"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        <span>{{ session('warning') }}</span>
      </div>
    @elseif($errors->any())
      <div style="margin-bottom: 20px; padding: 12px 16px; border-radius: 10px; background: #FEE2E2; border: 1px solid #FCA5A5; color: #991B1B; font-size: 13.5px; line-height: 1.5">
        {{ $errors->first() }}
      </div>
    @endif

    <form action="{{ route('register') }}" method="POST" novalidate style="display: flex; flex-direction: column; gap: 18px">
      @csrf
      <input type="hidden" name="tipe_pendaftaran" id="tipe_pendaftaran" value="{{ $initialTipe }}">
      <input type="hidden" name="redirect_bulan" value="{{ request('bulan', $selectedBulan?->id) }}">
      <input type="hidden" name="redirect_bidang" value="{{ request('bidang', $selectedBidang?->id) }}">
      <input type="hidden" name="redirect_jurusan" value="{{ request('jurusan', $selectedJurusan?->id) }}">
      <input type="hidden" name="redirect_tipe" value="{{ request('tipe', $initialTipe) }}">
      <input type="hidden" name="redirect_jumlah" value="{{ request('jumlah', $initialJumlah) }}">

      <div style="display: flex; flex-direction: column; gap: 8px">
        <span style="font-size: 14px; font-weight: 700; color: #003566">Tipe Pendaftaran Magang</span>
        <div class="reg-type-container {{ $isKuotaTerpilih ? 'is-locked' : '' }}" data-locked="{{ $isKuotaTerpilih ? 'true' : 'false' }}" style="display: flex; gap: 4px; padding: 4px; border: 1px solid #D5DEEE; border-radius: 12px; background: #EEF3FB">
          <button type="button" data-reg-type="ind" aria-pressed="{{ $initialTipe === 'kelompok' ? 'false' : 'true' }}" {{ $isKuotaTerpilih && $initialTipe === 'kelompok' ? 'disabled' : '' }} style="flex: 1; min-height: 44px; display: flex; align-items: center; justify-content: center; gap: 8px; border: 1px solid {{ $initialTipe === 'kelompok' ? 'transparent' : '#003566' }}; border-radius: 9px; background: {{ $initialTipe === 'kelompok' ? 'transparent' : '#FFFFFF' }}; color: {{ $initialTipe === 'kelompok' ? '#4A5560' : '#003566' }}; font-family: inherit; font-size: 14px; font-weight: {{ $initialTipe === 'kelompok' ? '600' : '700' }}; cursor: {{ $isKuotaTerpilih ? 'default' : 'pointer' }}">
            <svg aria-hidden="true" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="{{ $initialTipe === 'kelompok' ? '#4A5560' : '#003566' }}" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"></circle><path d="M4 21c0-4 3.6-6 8-6s8 2 8 6"></path></svg>
            Individu (1 Orang)
          </button>
          <button type="button" data-reg-type="kel" aria-pressed="{{ $initialTipe === 'kelompok' ? 'true' : 'false' }}" {{ $isKuotaTerpilih && $initialTipe !== 'kelompok' ? 'disabled' : '' }} style="flex: 1; min-height: 44px; display: flex; align-items: center; justify-content: center; gap: 8px; border: 1px solid {{ $initialTipe === 'kelompok' ? '#003566' : 'transparent' }}; border-radius: 9px; background: {{ $initialTipe === 'kelompok' ? '#FFFFFF' : 'transparent' }}; color: {{ $initialTipe === 'kelompok' ? '#003566' : '#4A5560' }}; font-family: inherit; font-size: 14px; font-weight: {{ $initialTipe === 'kelompok' ? '700' : '600' }}; cursor: {{ $isKuotaTerpilih ? 'default' : 'pointer' }}">
            <svg aria-hidden="true" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="{{ $initialTipe === 'kelompok' ? '#003566' : '#4A5560' }}" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"></path><path d="M2.5 20c0-3.3 2.9-5.5 6.5-5.5s6.5 2.2 6.5 5.5"></path><path d="M16 4.3a3.5 3.5 0 0 1 0 6.4M18 14.8c2 .6 3.5 2.2 3.5 5.2"></path></svg>
            Tim (2–{{ $maksAnggota }} Orang)
          </button>
        </div>
      </div>

      {{-- Bagian Khusus Pendaftaran Kelompok --}}
      <div id="section-kelompok-wrapper" style="display: {{ $initialTipe === 'kelompok' ? 'flex' : 'none' }}; flex-direction: column; gap: 12px; padding: 16px; border-radius: 12px; background: #F8FAFC; border: 1.5px solid #CBD5E1">
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px">
          <span style="font-size: 14px; font-weight: 700; color: #003566">Data Tim Magang</span>
          <span style="font-size: 12px; font-weight: 600; color: #5F6B76">Ketua + Anggota (Maks. {{ $maksAnggota }})</span>
        </div>

        <div style="position: relative">
          <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 16px; top: 16px"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
          <input id="nama_kelompok" name="nama_kelompok" class="fld" type="text" aria-label="Nama Tim" placeholder="Team name" value="{{ old('nama_kelompok') }}" {{ $initialTipe !== 'kelompok' ? 'disabled' : '' }} style="width: 100%; box-sizing: border-box; height: 50px; padding: 0 16px 0 46px; border: 1px solid #AAAAAA; border-radius: 10px; font-family: inherit; font-size: 15px; color: #212529; background: #FFFFFF">
        </div>

        <div style="display: flex; flex-direction: column; gap: 10px">
          <div style="display: flex; align-items: center; justify-content: space-between">
            <span style="font-size: 13px; font-weight: 700; color: #334155">Daftar Rekan Anggota Tim</span>
            <span style="font-size: 12px; color: #64748B">Anda mendaftar sebagai Ketua Tim</span>
          </div>

          @php
            $oldAnggotaCount = is_array(old('anggota')) ? count(old('anggota')) : 0;
            $anggotaCount = max(1, min($maksRekanTambahan, $oldAnggotaCount ?: ($initialJumlah - 1)));
          @endphp
          <div id="roster-anggota-container" 
               data-target-anggota="{{ $anggotaCount }}"
               data-max-rekan="{{ $maksRekanTambahan }}"
               data-min-rekan="1"
               data-max-total="{{ $maksAnggota }}"
               data-locked-count="{{ $isJumlahLocked ? 'true' : 'false' }}"
               style="display: flex; flex-direction: column; gap: 10px">
            @for($i = 0; $i < $maksRekanTambahan; $i++)
              @php
                $isVisibleAnggota = ($initialTipe === 'kelompok' && $i < $anggotaCount);
              @endphp
              <div class="card-anggota-item" data-anggota-index="{{ $i }}" style="display: {{ $isVisibleAnggota ? 'flex' : 'none' }}; flex-direction: column; gap: 8px; padding: 12px; border-radius: 9px; background: #FFFFFF; border: 1px solid #D5DEEE">
                <div class="card-anggota-header" style="display: flex; align-items: center; justify-content: space-between">
                  <span class="card-anggota-title" style="font-size: 12.5px; font-weight: 700; color: #003566">Rekan Anggota #{{ $i + 1 }}</span>
                  <button type="button" class="btn-remove-anggota" data-action="remove-anggota" aria-label="Hapus rekan anggota ini" title="Hapus rekan ini" style="{{ ($isJumlahLocked || ($i === 0 && $anggotaCount === 1)) ? 'display: none;' : '' }}">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                  </button>
                </div>
                <div class="card-anggota-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px">
                  <input name="anggota[{{ $i }}][nama]" class="fld" type="text" placeholder="Full name" value="{{ old("anggota.$i.nama") }}" {{ !$isVisibleAnggota ? 'disabled' : '' }} style="height: 40px; padding: 0 12px; border: 1px solid #AAAAAA; border-radius: 8px; font-family: inherit; font-size: 13.5px; color: #212529">
                  <input name="anggota[{{ $i }}][nim_nisn]" class="fld" type="text" placeholder="NIM / NISN" value="{{ old("anggota.$i.nim_nisn") }}" {{ !$isVisibleAnggota ? 'disabled' : '' }} style="height: 40px; padding: 0 12px; border: 1px solid #AAAAAA; border-radius: 8px; font-family: inherit; font-size: 13.5px; color: #212529">
                </div>
                <div class="card-anggota-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px">
                  <input name="anggota[{{ $i }}][no_hp]" class="fld" type="tel" placeholder="No" value="{{ old("anggota.$i.no_hp") }}" {{ !$isVisibleAnggota ? 'disabled' : '' }} style="height: 40px; padding: 0 12px; border: 1px solid #AAAAAA; border-radius: 8px; font-family: inherit; font-size: 13.5px; color: #212529">
                  <input name="anggota[{{ $i }}][email]" class="fld" type="email" placeholder="Email" value="{{ old("anggota.$i.email") }}" {{ !$isVisibleAnggota ? 'disabled' : '' }} style="height: 40px; padding: 0 12px; border: 1px solid #AAAAAA; border-radius: 8px; font-family: inherit; font-size: 13.5px; color: #212529">
                </div>
                <div class="card-anggota-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px">
                  <div class="card-anggota-pw-wrap">
                    <input id="anggota_{{ $i }}_password" name="anggota[{{ $i }}][password]" class="fld fld-pw-compact" type="password" placeholder="Password" autocomplete="new-password" aria-label="Kata sandi akun rekan {{ $i + 1 }}" {{ !$isVisibleAnggota ? 'disabled' : '' }} style="height: 40px; padding: 0 38px 0 12px; border: 1px solid #AAAAAA; border-radius: 8px; font-family: inherit; font-size: 13.5px; color: #212529">
                    <button type="button" class="btn-ico btn-toggle-pw-compact" data-action="toggle-password" data-target="anggota_{{ $i }}_password" aria-label="Tampilkan kata sandi">
                      <svg class="pw-icon-show" aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                      <svg class="pw-icon-hide" aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"></path><circle cx="12" cy="12" r="3"></circle><path d="M4 4l16 16"></path></svg>
                    </button>
                  </div>
                  <div class="card-anggota-pw-wrap">
                    <input id="anggota_{{ $i }}_password_confirmation" name="anggota[{{ $i }}][password_confirmation]" class="fld fld-pw-compact" type="password" placeholder="Confirm password" autocomplete="new-password" aria-label="Ulangi kata sandi akun rekan {{ $i + 1 }}" {{ !$isVisibleAnggota ? 'disabled' : '' }} style="height: 40px; padding: 0 38px 0 12px; border: 1px solid #AAAAAA; border-radius: 8px; font-family: inherit; font-size: 13.5px; color: #212529">
                    <button type="button" class="btn-ico btn-toggle-pw-compact" data-action="toggle-password" data-target="anggota_{{ $i }}_password_confirmation" aria-label="Tampilkan konfirmasi kata sandi">
                      <svg class="pw-icon-show" aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                      <svg class="pw-icon-hide" aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"></path><circle cx="12" cy="12" r="3"></circle><path d="M4 4l16 16"></path></svg>
                    </button>
                  </div>
                </div>
                <p id="hint_anggota_{{ $i }}_password_confirmation" class="card-anggota-pw-hint" data-anggota-hint="{{ $i }}" style="display: none;"></p>
              </div>
            @endfor
          </div>

          {{-- Kontrol Aksi Tambah Anggota --}}
          @if(!$isJumlahLocked)
            <div class="roster-actions-wrap" style="display: flex; flex-direction: column; gap: 8px; margin-top: 4px;">
              <button type="button" id="btn-add-anggota" class="btn-add-anggota" {{ $anggotaCount >= $maksRekanTambahan ? 'disabled' : '' }} style="display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; height: 42px; border: 1.5px dashed #003566; border-radius: 9px; background: rgba(0, 53, 102, 0.04); color: #003566; font-size: 13.5px; font-weight: 700; cursor: {{ $anggotaCount >= $maksRekanTambahan ? 'not-allowed' : 'pointer' }}; transition: all 0.2s ease;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <span>Tambah Rekan Anggota</span>
              </button>
              <div id="roster-max-hint" style="display: {{ $anggotaCount >= $maksRekanTambahan ? 'block' : 'none' }}; padding: 8px 12px; border-radius: 8px; background: #EEF2F6; border: 1px solid #CBD5E1; color: #475569; font-size: 12px; font-weight: 600; text-align: center;">
                Maksimal {{ $maksAnggota }} anggota tim (1 Ketua + {{ $maksRekanTambahan }} Rekan) telah tercapai.
              </div>
            </div>
          @endif
        </div>
      </div>

      <div style="display: flex; flex-direction: column; gap: 12px">
        <span style="font-size: 14px; font-weight: 700; color: #003566">Identitas Akun Calon Peserta</span>

        <div style="position: relative">
          <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 16px; top: 16px"><circle cx="12" cy="8" r="4"></circle><path d="M4 21c0-4 3.6-6 8-6s8 2 8 6"></path></svg>
          <input id="nama" name="username" class="fld" type="text" autocomplete="name" aria-label="Nama lengkap" placeholder="Full name" value="{{ old('username', old('nama', $oauthRegisterData['nama'] ?? '')) }}" style="width: 100%; box-sizing: border-box; height: 50px; padding: 0 16px 0 46px; border: 1px solid #AAAAAA; border-radius: 10px; font-family: inherit; font-size: 15px; color: #212529; background: #FFFFFF">
        </div>

        <div>
          <div style="position: relative">
            <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 16px; top: 16px"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="M3 7l9 6 9-6"></path></svg>
            <input id="email" name="email" class="fld" type="email" autocomplete="email" aria-label="Email" aria-describedby="hint-email" placeholder="Email" value="{{ old('email', $oauthRegisterData['email'] ?? '') }}" {{ !empty($oauthRegisterData['email']) ? 'readonly' : '' }} style="width: 100%; box-sizing: border-box; height: 50px; padding: 0 16px 0 46px; border: 1px solid #AAAAAA; border-radius: 10px; font-family: inherit; font-size: 15px; color: #212529; background: #FFFFFF">
          </div>
          <p id="hint-email" style="margin: 6px 0 0; font-size: 12.5px; line-height: 1.4; color: #5F6B76">{{ !empty($oauthRegisterData['email']) ? 'Email terverifikasi dari akun Google Anda.' : 'Gunakan email aktif untuk verifikasi berkas dan notifikasi seleksi.' }}</p>
        </div>

        <div>
          <div style="position: relative">
            <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 16px; top: 16px"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2z"></path></svg>
            <input id="telp" name="no_telp" class="fld" type="tel" autocomplete="tel" aria-label="Nomor telepon" aria-describedby="hint-telp" placeholder="No" value="{{ old('no_telp') }}" style="width: 100%; box-sizing: border-box; height: 50px; padding: 0 16px 0 46px; border: 1px solid #AAAAAA; border-radius: 10px; font-family: inherit; font-size: 15px; color: #212529; background: #FFFFFF">
          </div>
          <p id="hint-telp" style="margin: 6px 0 0; font-size: 12.5px; line-height: 1.4; color: #5F6B76">Nomor WhatsApp aktif untuk koordinasi dan informasi seleksi.</p>
        </div>

        <div style="display: flex; flex-direction: column; gap: 8px">
          <div>
            <div style="position: relative">
              <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 16px; top: 16px"><rect x="5" y="11" width="14" height="9" rx="2"></rect><path d="M8 11V8a4 4 0 0 1 8 0v3"></path></svg>
              <input id="sandi" name="password" class="fld" type="password" autocomplete="new-password" aria-label="Kata sandi" aria-describedby="hint-sandi" placeholder="Password" style="width: 100%; box-sizing: border-box; height: 50px; padding: 0 54px 0 46px; border: 1px solid #AAAAAA; border-radius: 10px; font-family: inherit; font-size: 15px; color: #212529; background: #FFFFFF">
              <button type="button" class="btn-ico" data-action="toggle-password" data-target="sandi" aria-label="Tampilkan kata sandi" style="position: absolute; right: 3px; top: 3px; width: 44px; height: 44px; border: none; border-radius: 8px; background: transparent; cursor: pointer; display: flex; align-items: center; justify-content: center">
                <svg class="pw-icon-show" aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                <svg class="pw-icon-hide" aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"></path><circle cx="12" cy="12" r="3"></circle><path d="M4 4l16 16"></path></svg>
              </button>
            </div>
            <div class="pw-strength-wrapper" data-target-input="sandi"></div>
            <p id="hint-sandi" style="margin: 4px 0 0; font-size: 12px; line-height: 1.35; color: #5F6B76">Minimal 8 karakter kombinasi huruf dan angka.</p>
          </div>

          <div>
            <div style="position: relative">
              <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 16px; top: 16px">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
              <input id="sandi_confirmation" name="password_confirmation" class="fld" type="password" autocomplete="new-password" aria-label="Konfirmasi kata sandi" aria-describedby="hint-sandi-confirmation" placeholder="Confirm password" style="width: 100%; box-sizing: border-box; height: 50px; padding: 0 54px 0 46px; border: 1px solid #AAAAAA; border-radius: 10px; font-family: inherit; font-size: 15px; color: #212529; background: #FFFFFF">
              <button type="button" class="btn-ico" data-action="toggle-password" data-target="sandi_confirmation" aria-label="Tampilkan konfirmasi kata sandi" style="position: absolute; right: 3px; top: 3px; width: 44px; height: 44px; border: none; border-radius: 8px; background: transparent; cursor: pointer; display: flex; align-items: center; justify-content: center">
                <svg class="pw-icon-show" aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                <svg class="pw-icon-hide" aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"></path><circle cx="12" cy="12" r="3"></circle><path d="M4 4l16 16"></path></svg>
              </button>
            </div>
            <p id="hint-sandi-confirmation" class="pw-match-hint" style="margin: 4px 0 0; font-size: 12px; line-height: 1.35; color: #5F6B76">Masukkan ulang kata sandi yang sama untuk konfirmasi.</p>
          </div>
        </div>
      </div>

      <div style="display: flex; flex-direction: column; gap: 12px">
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px">
          <span style="font-size: 14px; font-weight: 700; color: #003566">Informasi Pendidikan &amp; Pilihan Jurusan</span>
        </div>

        @if($isKuotaTerpilih)
          <input type="hidden" name="jenjang" value="{{ $initialJenjang }}">
          <input type="hidden" name="bidang_id" value="{{ $initialBidangId }}">
          <input type="hidden" name="jurusan_id" value="{{ $initialJurusanId }}">
        @endif

        @if(!$isKuotaTerpilih)
          <div>
            <div style="position: relative">
              <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 16px; top: 16px; pointer-events: none">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
              </svg>
              <select id="bulan_magang" name="bulan_magang_id" data-initial-bulan="{{ $initialBulanId }}" class="fld" aria-label="Periode Bulan Magang" aria-describedby="hint-bulan" style="width: 100%; box-sizing: border-box; height: 50px; padding: 0 44px 0 46px; border: 1px solid #AAAAAA; border-radius: 10px; font-family: inherit; font-size: 15px; color: #212529; background: #FFFFFF; appearance: none; -webkit-appearance: none; cursor: pointer">
                <option value="">-- Pilih Periode Bulan Magang --</option>
                @if(isset($semuaBulanTersedia) && count($semuaBulanTersedia) > 0)
                  @foreach($semuaBulanTersedia as $b)
                    <option value="{{ $b->id }}" {{ (string)$b->id === (string)$initialBulanId || count($semuaBulanTersedia) === 1 ? 'selected' : '' }}>{{ $b->nama_bulan }} (Pendaftaran Terbuka)</option>
                  @endforeach
                @else
                  <option value="default" selected>Periode Aktif Terdekat</option>
                @endif
              </select>
              <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#212529" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; right: 16px; top: 16px; pointer-events: none">
                <path d="M6 9l6 6 6-6"></path>
              </svg>
            </div>
            <p id="hint-bulan" style="margin: 6px 0 0; font-size: 12.5px; line-height: 1.4; color: #5F6B76">Pilih periode awal mulai pelaksanaan magang di Dinas Kominfo Sleman.</p>
          </div>
        @endif

        <div>
          <div style="position: relative">
            <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 16px; top: 16px; pointer-events: none"><path d="M12 6.5C10.5 5 8 4.5 3 4.5v13c5 0 7.5.5 9 2 1.5-1.5 4-2 9-2v-13c-5 0-7.5.5-9 2zM12 6.5v13"></path></svg>
            <select id="jenjang" {{ $isKuotaTerpilih ? 'disabled data-locked=true' : 'name=jenjang' }} class="fld {{ $isKuotaTerpilih ? 'auth-field-locked' : '' }}" aria-label="Jenjang pendidikan" aria-describedby="hint-jenjang" style="width: 100%; box-sizing: border-box; height: 50px; padding: 0 44px 0 46px; border: 1px solid #AAAAAA; border-radius: 10px; font-family: inherit; font-size: 15px; color: #212529; background: #FFFFFF; appearance: none; -webkit-appearance: none; cursor: {{ $isKuotaTerpilih ? 'not-allowed' : 'pointer' }}">
              <option value="">-- Pilih Jenjang Pendidikan --</option>
              <option value="smk" {{ $initialJenjang === 'smk' ? 'selected' : '' }}>SMK</option>
              <option value="mahasiswa" {{ in_array($initialJenjang, ['mahasiswa', 's1', 'd3'], true) ? 'selected' : '' }}>Mahasiswa (D3 / D4 / S1)</option>
            </select>
            <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#212529" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; right: 16px; top: 16px; pointer-events: none"><path d="M6 9l6 6 6-6"></path></svg>
          </div>
          <p id="hint-jenjang" style="margin: 6px 0 0; font-size: 12.5px; line-height: 1.4; color: #5F6B76">Pilih jenjang pendidikan formal yang sedang Anda tempuh saat ini.</p>
        </div>

        <div>
          <div style="position: relative">
            <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 16px; top: 16px; pointer-events: none">
              <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
              <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
            </svg>
            <select id="bidang" {{ $isKuotaTerpilih ? 'disabled data-locked=true' : 'name=bidang_id' }} data-initial-bidang="{{ $initialBidangId }}" class="fld {{ $isKuotaTerpilih ? 'auth-field-locked' : '' }}" aria-label="Bidang penempatan atau peminatan" aria-describedby="hint-bidang" style="width: 100%; box-sizing: border-box; height: 50px; padding: 0 44px 0 46px; border: 1px solid #AAAAAA; border-radius: 10px; font-family: inherit; font-size: 15px; color: #212529; background: #FFFFFF; appearance: none; -webkit-appearance: none; cursor: {{ $isKuotaTerpilih ? 'not-allowed' : 'pointer' }}">
              <option value="">-- Pilih Bidang Peminatan / Penempatan --</option>
              @if(isset($bidangList) && count($bidangList) > 0)
                @foreach($bidangList as $b)
                  <option value="{{ $b->id }}" {{ (string)$b->id === (string)$initialBidangId ? 'selected' : '' }}>{{ $b->nama_bidang }}</option>
                @endforeach
              @else
                <option value="aptika" {{ (string)$initialBidangId === 'aptika' ? 'selected' : '' }}>Bidang Aplikasi Informatika (Aptika)</option>
                <option value="ikp" {{ (string)$initialBidangId === 'ikp' ? 'selected' : '' }}>Bidang Informasi dan Komunikasi Publik (IKP)</option>
                <option value="egov" {{ (string)$initialBidangId === 'egov' ? 'selected' : '' }}>Bidang Tata Kelola E-Government</option>
                <option value="jaringan" {{ (string)$initialBidangId === 'jaringan' ? 'selected' : '' }}>Bidang Jaringan dan Infrastruktur TIK</option>
              @endif
            </select>
            <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#212529" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; right: 16px; top: 16px; pointer-events: none">
              <path d="M6 9l6 6 6-6"></path>
            </svg>
          </div>
          <p id="hint-bidang" style="margin: 6px 0 0; font-size: 12.5px; line-height: 1.4; color: #5F6B76">Pilih bidang penempatan di Dinas Kominfo yang sesuai dengan peminatan magang Anda.</p>
        </div>

        <div>
          <div style="position: relative">
            <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 16px; top: 16px; pointer-events: none"><path d="M2 9l10-5 10 5-10 5z"></path><path d="M6 11.5V16c0 1.5 3 3 6 3s6-1.5 6-3v-4.5"></path></svg>
            <select id="jurusan" {{ $isKuotaTerpilih ? 'disabled data-locked=true' : 'name=jurusan_id' }} data-initial-jurusan="{{ $initialJurusanId }}" class="fld {{ $isKuotaTerpilih ? 'auth-field-locked' : '' }}" aria-label="Jurusan atau program studi" aria-describedby="hint-jurusan" style="width: 100%; box-sizing: border-box; height: 50px; padding: 0 44px 0 46px; border: 1px solid #AAAAAA; border-radius: 10px; font-family: inherit; font-size: 15px; color: #212529; background: #FFFFFF; appearance: none; -webkit-appearance: none; cursor: {{ $isKuotaTerpilih ? 'not-allowed' : 'pointer' }}">
              <option value="">-- Pilih Jurusan / Program Studi --</option>
              @foreach($jurusanMasterData as $j)
                <option value="{{ $j['id'] }}" {{ (string)$j['id'] === (string)$initialJurusanId ? 'selected' : '' }}>{{ $j['nama'] }}</option>
              @endforeach
            </select>
            <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#212529" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; right: 16px; top: 16px; pointer-events: none"><path d="M6 9l6 6 6-6"></path></svg>
          </div>
          <p id="hint-jurusan" style="margin: 6px 0 0; font-size: 12.5px; line-height: 1.4; color: #5F6B76">
            {{ $isKuotaTerpilih ? 'Jurusan disesuaikan dengan kuota pilihan Anda dari beranda.' : 'Pilihan jurusan menyesuaikan bidang penempatan di atas.' }}
          </p>
        </div>

        @if(!$isKuotaTerpilih)
          <div id="live-quota-status-card" class="auth-live-quota-card" style="display: none">
            <span class="lqc-icon-wrap" id="lqc-icon"></span>
            <span class="lqc-title" id="lqc-title"></span>
          </div>
        @endif

        <script id="register-penempatan-matrix" type="application/json">
          {!! json_encode($matriksPenempatan ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}
        </script>
        <script id="register-jurusan-data" type="application/json">
          {!! json_encode($jurusanMasterData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}
        </script>

        <div style="position: relative">
          <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 16px; top: 16px"><rect x="5" y="3" width="14" height="18" rx="1"></rect><path d="M9 7h2M13 7h2M9 11h2M13 11h2M10 21v-4h4v4"></path></svg>
          <input id="sekolah" name="institusi" class="fld" type="text" aria-label="Nama sekolah atau perguruan tinggi" placeholder="School / University" value="{{ old('institusi') }}" style="width: 100%; box-sizing: border-box; height: 50px; padding: 0 16px 0 46px; border: 1px solid #AAAAAA; border-radius: 10px; font-family: inherit; font-size: 15px; color: #212529; background: #FFFFFF; text-overflow: ellipsis">
        </div>

        <div style="position: relative">
          <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 16px; top: 16px"><rect x="3" y="5" width="18" height="14" rx="2"></rect><circle cx="9" cy="11" r="2"></circle><path d="M6 16c.5-1.5 1.8-2 3-2s2.5.5 3 2M14 10h4M14 13h4"></path></svg>
          <input id="nim" name="nim_nisn" class="fld" type="text" aria-label="NIM atau NISN" placeholder="NIM / NISN" value="{{ old('nim_nisn') }}" style="width: 100%; box-sizing: border-box; height: 50px; padding: 0 16px 0 46px; border: 1px solid #AAAAAA; border-radius: 10px; font-family: inherit; font-size: 15px; color: #212529; background: #FFFFFF">
        </div>
      </div>

      <div style="display: flex; align-items: flex-start; gap: 10px">
        <input id="setuju" name="persetujuan_kebijakan" type="checkbox" value="1" style="width: 18px; height: 18px; margin: 2px 0 0; flex-shrink: 0; accent-color: #003566">
        <label for="setuju" style="font-size: 13.5px; line-height: 1.5; color: #3B4650">Saya menyetujui <button type="button" data-action="open-modal-privasi" style="background: none; border: none; padding: 0; font-family: inherit; font-size: inherit; font-weight: 700; color: #003566; text-decoration: underline; cursor: pointer">Kebijakan Privasi &amp; Syarat Ketentuan</button> Dinas Komunikasi dan Informatika Kabupaten Sleman.</label>
      </div>

      <div style="display: flex; flex-direction: column; gap: 8px">
        <label for="kode" style="font-size: 14px; font-weight: 700; color: #003566">Kode Verifikasi Keamanan <span aria-hidden="true" style="color: #C62828">*</span></label>
        <div style="display: flex; gap: 10px; align-items: center">
          <div style="width: 150px; height: 46px; box-sizing: border-box; border-radius: 10px; overflow: hidden; display: flex; align-items: center; justify-content: center; background: #F8FAFC; border: 1px solid #CBD5E1">
            <img data-captcha-img src="{{ route('captcha.image') }}" alt="Kode Verifikasi Keamanan" style="width: 150px; height: 46px; display: block; object-fit: cover; user-select: none; cursor: pointer" width="150" height="46" title="Klik untuk memuat ulang kode">
          </div>
          <button type="button" class="btn-ico" data-action="refresh-captcha" aria-label="Ganti kode verifikasi" style="width: 46px; height: 46px; border: 1px solid #AAAAAA; border-radius: 10px; background: #FFFFFF; cursor: pointer; display: flex; align-items: center; justify-content: center">
            <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#003566" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 11a8 8 0 0 0-14.5-4M4 4v4h4"></path><path d="M4 13a8 8 0 0 0 14.5 4M20 20v-4h-4"></path></svg>
          </button>
        </div>
        <div>
          <div style="position: relative">
            <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 16px; top: 16px"><path d="M12 3l7 3v5c0 5-3 8-7 10-4-2-7-5-7-10V6z"></path></svg>
            <input id="kode" name="captcha" class="fld captcha-input" type="text" maxlength="4" autocomplete="off" autocapitalize="characters" spellcheck="false" aria-describedby="hint-kode" placeholder="Security code" style="width: 100%; box-sizing: border-box; height: 50px; padding: 0 16px 0 46px; border: 1px solid #AAAAAA; border-radius: 10px; font-family: inherit; font-size: 15px; color: #212529; background: #FFFFFF">
          </div>
          <p id="hint-kode" style="margin: 6px 0 0; font-size: 12.5px; line-height: 1.4; color: #5F6B76">Masukkan 4 karakter yang tampil pada gambar (tidak sensitif huruf besar/kecil).</p>
        </div>
      </div>

      <button type="submit" class="btn-main" style="width: 100%; height: 52px; border: none; border-bottom: 3px solid #FFC300; border-radius: 10px; background: #003566; color: #FFFFFF; font-family: inherit; font-size: 16px; font-weight: 700; cursor: pointer">Register</button>
    </form>

    <p style="margin: 22px 0 0; text-align: center; font-size: 15px; color: #5F6B76">Sudah punya akun? <a href="{{ route('login') }}" style="font-weight: 700; color: #003566; text-decoration: none">Login</a></p>
    @endif
    <p style="margin: 10px 0 0; text-align: center; font-size: 12.5px; color: #5F6B76">Magang Kominfo • Dinas Kominfo Sleman • v1.0</p>
  </div>
</section>

@include('auth.partials._modal_privasi_syarat')
@endsection
