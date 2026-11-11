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
@endphp

<section class="auth-panel-scroll" style="flex: 1 1 440px; min-height: 560px; position: relative; box-sizing: border-box; padding: 44px 40px 32px; max-height: 800px; overflow-y: auto; scrollbar-width: thin; scrollbar-color: #AAB6C8 transparent; display: flex; flex-direction: column; background: #FFFFFF">
  <div style="display: flex; flex-direction: column; width: 100%; max-width: 460px; margin: 0 auto; box-sizing: border-box">
    
    <div style="margin-bottom: 24px">
      <h1 style="margin: 0 0 8px; font-size: 34px; line-height: 1.15; font-weight: 800; letter-spacing: -0.02em; color: #003566">Register Akun</h1>
      <p style="margin: 0; font-size: 15px; line-height: 1.5; color: #5F6B76">Daftarkan akun Anda untuk memulai proses seleksi magang di Dinas Kominfo.</p>
    </div>

    <div style="display: flex; flex-direction: column; gap: 18px">

      <div style="display: flex; flex-direction: column; gap: 8px">
        <span style="font-size: 14px; font-weight: 700; color: #003566">Tipe Pendaftaran Magang</span>
        <div style="display: flex; gap: 4px; padding: 4px; border: 1px solid #D5DEEE; border-radius: 12px; background: #EEF3FB">
          <button type="button" data-reg-type="ind" aria-pressed="true" style="flex: 1; min-height: 44px; display: flex; align-items: center; justify-content: center; gap: 8px; border: 1px solid #003566; border-radius: 9px; background: #FFFFFF; color: #003566; font-family: inherit; font-size: 14px; font-weight: 700; cursor: pointer">
            <svg aria-hidden="true" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#003566" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"></circle><path d="M4 21c0-4 3.6-6 8-6s8 2 8 6"></path></svg>
            Individu (1 Orang)
          </button>
          <button type="button" data-reg-type="kel" aria-pressed="false" style="flex: 1; min-height: 44px; display: flex; align-items: center; justify-content: center; gap: 8px; border: 1px solid transparent; border-radius: 9px; background: transparent; color: #4A5560; font-family: inherit; font-size: 14px; font-weight: 600; cursor: pointer">
            <svg aria-hidden="true" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#4A5560" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"></path><path d="M2.5 20c0-3.3 2.9-5.5 6.5-5.5s6.5 2.2 6.5 5.5"></path><path d="M16 4.3a3.5 3.5 0 0 1 0 6.4M18 14.8c2 .6 3.5 2.2 3.5 5.2"></path></svg>
            Kelompok (2–4 Orang)
          </button>
        </div>
      </div>

      <div style="display: flex; flex-direction: column; gap: 12px">
        <span style="font-size: 14px; font-weight: 700; color: #003566">Identitas Akun Calon Peserta</span>

        <div style="position: relative">
          <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 16px; top: 16px"><circle cx="12" cy="8" r="4"></circle><path d="M4 21c0-4 3.6-6 8-6s8 2 8 6"></path></svg>
          <input id="nama" class="fld" type="text" autocomplete="name" aria-label="Nama lengkap" placeholder="nama lengkap" style="width: 100%; box-sizing: border-box; height: 50px; padding: 0 16px 0 46px; border: 1px solid #AAAAAA; border-radius: 10px; font-family: inherit; font-size: 15px; color: #212529; background: #FFFFFF">
        </div>

        <div>
          <div style="position: relative">
            <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 16px; top: 16px"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="M3 7l9 6 9-6"></path></svg>
            <input id="email" class="fld" type="email" autocomplete="email" aria-label="Email" aria-describedby="hint-email" placeholder="nama.peserta@gmail.com" style="width: 100%; box-sizing: border-box; height: 50px; padding: 0 16px 0 46px; border: 1px solid #AAAAAA; border-radius: 10px; font-family: inherit; font-size: 15px; color: #212529; background: #FFFFFF">
          </div>
          <p id="hint-email" style="margin: 6px 0 0; font-size: 12.5px; line-height: 1.4; color: #5F6B76">Gunakan email aktif untuk verifikasi berkas dan notifikasi seleksi.</p>
        </div>

        <div>
          <div style="position: relative">
            <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 16px; top: 16px"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2z"></path></svg>
            <input id="telp" class="fld" type="tel" autocomplete="tel" aria-label="Nomor telepon" aria-describedby="hint-telp" placeholder="no telp" style="width: 100%; box-sizing: border-box; height: 50px; padding: 0 16px 0 46px; border: 1px solid #AAAAAA; border-radius: 10px; font-family: inherit; font-size: 15px; color: #212529; background: #FFFFFF">
          </div>
          <p id="hint-telp" style="margin: 6px 0 0; font-size: 12.5px; line-height: 1.4; color: #5F6B76">Nomor WhatsApp aktif untuk koordinasi dan informasi seleksi.</p>
        </div>

        <div>
          <div style="position: relative">
            <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 16px; top: 16px"><rect x="5" y="11" width="14" height="9" rx="2"></rect><path d="M8 11V8a4 4 0 0 1 8 0v3"></path></svg>
            <input id="sandi" class="fld" type="password" autocomplete="new-password" aria-label="Kata sandi" aria-describedby="hint-sandi" placeholder="password" style="width: 100%; box-sizing: border-box; height: 50px; padding: 0 54px 0 46px; border: 1px solid #AAAAAA; border-radius: 10px; font-family: inherit; font-size: 15px; color: #212529; background: #FFFFFF">
            <button type="button" class="btn-ico" data-action="toggle-password" data-target="sandi" aria-label="Tampilkan kata sandi" style="position: absolute; right: 3px; top: 3px; width: 44px; height: 44px; border: none; border-radius: 8px; background: transparent; cursor: pointer; display: flex; align-items: center; justify-content: center">
              <svg class="pw-icon-show" aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"></path><circle cx="12" cy="12" r="3"></circle></svg>
              <svg class="pw-icon-hide" aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"></path><circle cx="12" cy="12" r="3"></circle><path d="M4 4l16 16"></path></svg>
            </button>
          </div>
          <p id="hint-sandi" style="margin: 6px 0 0; font-size: 12.5px; line-height: 1.4; color: #5F6B76">Minimal 8 karakter kombinasi huruf dan angka.</p>
        </div>
      </div>

      <div style="display: flex; flex-direction: column; gap: 12px">
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px">
          <span style="font-size: 14px; font-weight: 700; color: #003566">Informasi Pendidikan &amp; Pilihan Jurusan</span>
          <a href="#kuota" style="font-size: 13px; font-weight: 700; color: #003566; text-decoration: none; white-space: nowrap">Cek Kuota Terbuka →</a>
        </div>

        <div>
          <div style="position: relative">
            <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 16px; top: 16px; pointer-events: none"><path d="M12 6.5C10.5 5 8 4.5 3 4.5v13c5 0 7.5.5 9 2 1.5-1.5 4-2 9-2v-13c-5 0-7.5.5-9 2zM12 6.5v13"></path></svg>
            <select id="jenjang" class="fld" aria-label="Jenjang pendidikan" aria-describedby="hint-jenjang" style="width: 100%; box-sizing: border-box; height: 50px; padding: 0 44px 0 46px; border: 1px solid #AAAAAA; border-radius: 10px; font-family: inherit; font-size: 15px; color: #212529; background: #FFFFFF; appearance: none; -webkit-appearance: none; cursor: pointer">
              <option value="">-- Pilih Jenjang Pendidikan --</option>
              <option value="smk">SMK</option>
              <option value="mahasiswa">Mahasiswa (D3 / D4 / S1)</option>
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
            <select id="bidang" class="fld" aria-label="Bidang penempatan atau peminatan" aria-describedby="hint-bidang" style="width: 100%; box-sizing: border-box; height: 50px; padding: 0 44px 0 46px; border: 1px solid #AAAAAA; border-radius: 10px; font-family: inherit; font-size: 15px; color: #212529; background: #FFFFFF; appearance: none; -webkit-appearance: none; cursor: pointer">
              <option value="">-- Pilih Bidang Peminatan / Penempatan --</option>
              @if(isset($bidangList) && count($bidangList) > 0)
                @foreach($bidangList as $b)
                  <option value="{{ $b->id }}">{{ $b->nama_bidang }}</option>
                @endforeach
              @else
                <option value="aptika">Bidang Aplikasi Informatika (Aptika)</option>
                <option value="ikp">Bidang Informasi dan Komunikasi Publik (IKP)</option>
                <option value="egov">Bidang Tata Kelola E-Government</option>
                <option value="jaringan">Bidang Jaringan dan Infrastruktur TIK</option>
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
            <select id="jurusan" class="fld" aria-label="Jurusan atau program studi" aria-describedby="hint-jurusan" style="width: 100%; box-sizing: border-box; height: 50px; padding: 0 44px 0 46px; border: 1px solid #AAAAAA; border-radius: 10px; font-family: inherit; font-size: 15px; color: #212529; background: #FFFFFF; appearance: none; -webkit-appearance: none; cursor: pointer">
              <option value="">-- Pilih Jurusan / Program Studi --</option>
              @foreach($jurusanMasterData as $j)
                <option value="{{ $j['id'] }}">{{ $j['nama'] }}</option>
              @endforeach
            </select>
            <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#212529" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; right: 16px; top: 16px; pointer-events: none"><path d="M6 9l6 6 6-6"></path></svg>
          </div>
          <p id="hint-jurusan" style="margin: 6px 0 0; font-size: 12.5px; line-height: 1.4; color: #5F6B76">Jurusan akan disaring sesuai jenjang yang dipilih di atas.</p>
        </div>

        <script id="register-jurusan-data" type="application/json">
          {!! json_encode($jurusanMasterData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}
        </script>

        <div style="position: relative">
          <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 16px; top: 16px"><rect x="5" y="3" width="14" height="18" rx="1"></rect><path d="M9 7h2M13 7h2M9 11h2M13 11h2M10 21v-4h4v4"></path></svg>
          <input id="sekolah" class="fld" type="text" aria-label="Nama sekolah atau perguruan tinggi" placeholder="nama sekolah / perguruan tinggi (contoh: SMKN 2 Depok Sleman)" style="width: 100%; box-sizing: border-box; height: 50px; padding: 0 16px 0 46px; border: 1px solid #AAAAAA; border-radius: 10px; font-family: inherit; font-size: 15px; color: #212529; background: #FFFFFF; text-overflow: ellipsis">
        </div>

        <div style="position: relative">
          <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 16px; top: 16px"><rect x="3" y="5" width="18" height="14" rx="2"></rect><circle cx="9" cy="11" r="2"></circle><path d="M6 16c.5-1.5 1.8-2 3-2s2.5.5 3 2M14 10h4M14 13h4"></path></svg>
          <input id="nim" class="fld" type="text" aria-label="NIM atau NISN" placeholder="NIM / NISN" style="width: 100%; box-sizing: border-box; height: 50px; padding: 0 16px 0 46px; border: 1px solid #AAAAAA; border-radius: 10px; font-family: inherit; font-size: 15px; color: #212529; background: #FFFFFF">
        </div>
      </div>

      <div style="display: flex; align-items: flex-start; gap: 10px">
        <input id="setuju" type="checkbox" style="width: 18px; height: 18px; margin: 2px 0 0; flex-shrink: 0; accent-color: #003566">
        <label for="setuju" style="font-size: 13.5px; line-height: 1.5; color: #3B4650">Saya menyetujui <button type="button" data-action="open-modal-privasi" style="background: none; border: none; padding: 0; font-family: inherit; font-size: inherit; font-weight: 700; color: #003566; text-decoration: underline; cursor: pointer">Kebijakan Privasi &amp; Syarat Ketentuan</button> Dinas Komunikasi dan Informatika Kabupaten Sleman.</label>
      </div>

      <div style="display: flex; flex-direction: column; gap: 8px">
        <label for="kode" style="font-size: 14px; font-weight: 700; color: #003566">Kode Verifikasi Keamanan <span aria-hidden="true" style="color: #C62828">*</span></label>
        <div style="display: flex; gap: 10px; align-items: center">
          <div data-captcha-display style="width: 150px; height: 46px; box-sizing: border-box; border-radius: 10px; background: #E6ECF7; color: #003566; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 800; letter-spacing: 0.32em; padding-left: 0.32em; user-select: none">DE4F</div>
          <button type="button" class="btn-ico" data-action="refresh-captcha" aria-label="Ganti kode verifikasi" style="width: 46px; height: 46px; border: 1px solid #AAAAAA; border-radius: 10px; background: #FFFFFF; cursor: pointer; display: flex; align-items: center; justify-content: center">
            <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#003566" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 11a8 8 0 0 0-14.5-4M4 4v4h4"></path><path d="M4 13a8 8 0 0 0 14.5 4M20 20v-4h-4"></path></svg>
          </button>
        </div>
        <div>
          <div style="position: relative">
            <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 16px; top: 16px"><path d="M12 3l7 3v5c0 5-3 8-7 10-4-2-7-5-7-10V6z"></path></svg>
            <input id="kode" class="fld" type="text" maxlength="4" autocomplete="off" aria-describedby="hint-kode" placeholder="4 digit kode di atas" style="width: 100%; box-sizing: border-box; height: 50px; padding: 0 16px 0 46px; border: 1px solid #AAAAAA; border-radius: 10px; font-family: inherit; font-size: 15px; color: #212529; background: #FFFFFF">
          </div>
          <p id="hint-kode" style="margin: 6px 0 0; font-size: 12.5px; line-height: 1.4; color: #5F6B76">Masukkan 4 karakter yang tampil pada gambar (tidak sensitif huruf besar/kecil).</p>
        </div>
      </div>

      <button type="button" class="btn-main" style="width: 100%; height: 52px; border: none; border-bottom: 3px solid #FFC300; border-radius: 10px; background: #003566; color: #FFFFFF; font-family: inherit; font-size: 16px; font-weight: 700; cursor: pointer">Register</button>
    </div>

    <p style="margin: 22px 0 0; text-align: center; font-size: 15px; color: #5F6B76">Sudah punya akun? <a href="{{ route('login') }}" style="font-weight: 700; color: #003566; text-decoration: none">Login</a></p>
    <p style="margin: 10px 0 0; text-align: center; font-size: 12.5px; color: #5F6B76">Magang Kominfo • Dinas Kominfo Sleman • v1.0</p>
  </div>
</section>

@include('auth.partials._modal_privasi_syarat')
@endsection
