<div id="modalPrivasiSyarat" class="auth-modal-backdrop" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="modalPrivasiTitle" style="display: none; position: fixed; inset: 0; background: rgba(1, 24, 58, 0.75); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px); z-index: 9999; align-items: center; justify-content: center; padding: 20px; box-sizing: border-box">
  <div class="auth-modal-card" style="position: relative; width: 100%; max-width: 680px; max-height: 85vh; background: #FFFFFF; border-radius: 22px; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 30px 70px rgba(0, 20, 50, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.15)">
    
    <!-- Header Modal -->
    <div style="background: linear-gradient(135deg, #003566 0%, #01347C 100%); color: #FFFFFF; padding: 22px 28px; display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #FFC300">
      <div style="display: flex; align-items: center; gap: 12px">
        <img src="{{ asset('images/logo-sleman.png') }}" alt="Lambang Kabupaten Sleman" style="height: 38px; width: auto; border-radius: 4px; display: block">
        <div>
          <span style="display: block; font-size: 11px; font-weight: 700; letter-spacing: 0.08em; color: #FFC300; text-transform: uppercase">Dinas Kominfo Kab. Sleman</span>
          <h2 id="modalPrivasiTitle" style="margin: 0; font-size: 18px; font-weight: 800; line-height: 1.25">Kebijakan Privasi &amp; Syarat Ketentuan</h2>
        </div>
      </div>
      <button type="button" class="btn-ico" data-action="close-modal-privasi" aria-label="Tutup jendela informasi" style="width: 36px; height: 36px; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.2); background: rgba(255, 255, 255, 0.1); color: #FFFFFF; cursor: pointer; display: flex; align-items: center; justify-content: center">
        <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
      </button>
    </div>

    <!-- Tab Tombol Modal -->
    <div style="display: flex; background: #EEF3FB; padding: 6px 12px; gap: 8px; border-bottom: 1px solid #D5DEEE">
      <button type="button" id="modalTabBtnPrivasi" data-modal-tab="privasi" style="flex: 1; padding: 10px 14px; border: 1px solid #003566; border-radius: 10px; background: #FFFFFF; color: #003566; font-family: inherit; font-size: 13.5px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px">
        <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
        Kebijakan Privasi (UU PDP)
      </button>
      <button type="button" id="modalTabBtnSyarat" data-modal-tab="syarat" style="flex: 1; padding: 10px 14px; border: 1px solid transparent; border-radius: 10px; background: transparent; color: #4A5560; font-family: inherit; font-size: 13.5px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px">
        <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
        Syarat &amp; Ketentuan Magang
      </button>
    </div>

    <!-- Body Konten Scrollable -->
    <div class="auth-panel-scroll" style="flex: 1; overflow-y: auto; padding: 24px 28px; font-size: 14px; line-height: 1.6; color: #212529">
      
      <!-- Konten Tab Privasi -->
      <div id="modalContentPrivasi" style="display: flex; flex-direction: column; gap: 16px">
        <div style="background: #E6ECF7; border-left: 4px solid #003566; padding: 12px 16px; border-radius: 8px">
          <strong style="color: #003566">Kepatuhan UU No. 27 Tahun 2022 (PDP)</strong>
          <p style="margin: 4px 0 0; font-size: 13px; color: #3B4650">Dinas Kominfo Kabupaten Sleman mengelola seluruh data pribadi pendaftar magang secara aman dan terlindungi demi ketertiban administrasi pemerintahan.</p>
        </div>

        <div>
          <h4 style="margin: 0 0 6px; font-size: 14.5px; font-weight: 700; color: #003566">1. Data yang Dikumpulkan</h4>
          <p style="margin: 0; color: #5F6B76">Mencakup nama lengkap, NIK, NISN/NIM, kontak aktif, asal sekolah/kampus, jurusan, serta dokumen resmi (Surat Pengantar, CV, Proposal, dan Transkrip Nilai).</p>
        </div>

        <div>
          <h4 style="margin: 0 0 6px; font-size: 14.5px; font-weight: 700; color: #003566">2. Keamanan Penyimpanan &amp; Akses Terbatas</h4>
          <p style="margin: 0; color: #5F6B76">Seluruh berkas dokumen disimpan pada penyimpanan privat terenkripsi. Berkas hanya dapat diakses oleh verifikator resmi melalui tautan aman bertanda tangan digital (signed URL) dengan masa kedaluwarsa ketat.</p>
        </div>

        <div>
          <h4 style="margin: 0 0 6px; font-size: 14.5px; font-weight: 700; color: #003566">3. Larangan Komersialisasi &amp; Pihak Ketiga</h4>
          <p style="margin: 0; color: #5F6B76">Pemerintah Kabupaten Sleman menjamin data pribadi Anda tidak akan pernah diperjualbelikan atau dibagikan kepada pihak komersial.</p>
        </div>
      </div>

      <!-- Konten Tab Syarat & Ketentuan -->
      <div id="modalContentSyarat" style="display: none; flex-direction: column; gap: 16px">
        <div style="background: #FEF3C7; border-left: 4px solid #F59E0B; padding: 12px 16px; border-radius: 8px">
          <strong style="color: #92400E">Tata Tertib &amp; Aturan Magang</strong>
          <p style="margin: 4px 0 0; font-size: 13px; color: #78350F">Peserta magang diwajibkan menjunjung tinggi kedisiplinan dan etika kedinasan di lingkungan Dinas Kominfo Sleman.</p>
        </div>

        <div>
          <h4 style="margin: 0 0 6px; font-size: 14.5px; font-weight: 700; color: #003566">1. Jam Dinas &amp; Kehadiran</h4>
          <p style="margin: 0; color: #5F6B76">Peserta wajib hadir mengikuti jam dinas (Senin – Jumat, 07.30 s/d 15.30 WIB) dan mengenakan pakaian rapi, sopan, serta bersepatu.</p>
        </div>

        <div>
          <h4 style="margin: 0 0 6px; font-size: 14.5px; font-weight: 700; color: #003566">2. Kewajiban Pengisian Logbook Mingguan</h4>
          <p style="margin: 0; color: #5F6B76">Setiap aktivitas dan progres tugas wajib dilaporkan secara berkala melalui modul Logbook Mingguan di portal MagKom untuk divalidasi oleh pembimbing.</p>
        </div>

        <div>
          <h4 style="margin: 0 0 6px; font-size: 14.5px; font-weight: 700; color: #003566">3. Kerahasiaan Informasi Pemerintah</h4>
          <p style="margin: 0; color: #5F6B76">Dilarang keras menyebarluaskan kode sumber aplikasi, basis data, atau data internal kedinasan kepada pihak luar tanpa izin tertulis dari Kepala Dinas.</p>
        </div>

        <div>
          <h4 style="margin: 0 0 6px; font-size: 14.5px; font-weight: 700; color: #003566">4. Kelulusan &amp; Sertifikat Resmi</h4>
          <p style="margin: 0; color: #5F6B76">Sertifikat magang resmi dengan Tanda Tangan Elektronik (TTE) diberikan kepada peserta yang memenuhi seluruh capaian tugas dan berintegritas.</p>
        </div>
      </div>

    </div>

    <!-- Footer Modal -->
    <div style="background: #F8FAFC; border-top: 1px solid #E2E8F0; padding: 16px 28px; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap">
      <a href="{{ route('kebijakan-privasi') }}" target="_blank" rel="noopener noreferrer" style="font-size: 13.5px; font-weight: 700; color: #003566; text-decoration: none; display: flex; align-items: center; gap: 4px">
        <span>Buka Halaman Lengkap</span>
        <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
      </a>

      <button type="button" class="btn-main" data-action="close-modal-privasi" style="min-width: 130px; height: 44px; padding: 0 20px; border: none; border-bottom: 2px solid #FFC300; border-radius: 9px; background: #003566; color: #FFFFFF; font-family: inherit; font-size: 14px; font-weight: 700; cursor: pointer">
        Saya Mengerti
      </button>
    </div>

  </div>
</div>

