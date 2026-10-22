{{-- ==========================================================================
   Fase 1: Onboarding (Belum Mendaftar / Profil Baru) - Dashboard Peserta
   ========================================================================== --}}
<x-gentelella.x-panel
    title="Panduan Alur Pendaftaran Magang"
    subtitle="Ikuti langkah-langkah di bawah ini untuk memulai pendaftaran magang di Dinas Kominfo Sleman."
>
    <div class="stepper-list">
        <div class="step-item {{ $profil ? 'completed' : 'active' }}">
            <div class="step-badge">{{ $profil ? '✓' : '1' }}</div>
            <div class="step-title">1. Lengkapi Biodata & Profil</div>
            <div class="step-desc">
                Isi asal institusi/sekolah, NISN/NIM, jurusan, jenjang, kontak WhatsApp, dan kontak darurat.
            </div>
        </div>

        <div class="step-item {{ $profil ? 'active' : '' }}">
            <div class="step-badge">2</div>
            <div class="step-title">2. Pilih Periode & Kuota</div>
            <div class="step-desc">
                Pilih bulan magang aktif yang kuotanya masih tersedia sesuai jurusan Anda dan buat draf pengajuan.
            </div>
        </div>

        <div class="step-item">
            <div class="step-badge">3</div>
            <div class="step-title">3. Unggah Berkas & Ajukan</div>
            <div class="step-desc">
                Unggah surat pengantar, proposal, transkrip nilai, lalu kirim pengajuan untuk diverifikasi admin.
            </div>
        </div>
    </div>

    <div style="display: flex; gap: 12px; flex-wrap: wrap; margin-top: 20px;">
        @if(!$profil)
            <a href="{{ route('peserta.profil.show') }}" class="btn btn-primary">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Lengkapi Biodata Sekarang
            </a>
        @else
            <a href="{{ route('peserta.pendaftaran.index') }}" class="btn btn-primary">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                </svg>
                Buat Pengajuan Pendaftaran Magang
            </a>
            <a href="{{ route('peserta.profil.show') }}" class="btn btn-secondary">
                Periksa Biodata Saya
            </a>
        @endif
    </div>
</x-gentelella.x-panel>
