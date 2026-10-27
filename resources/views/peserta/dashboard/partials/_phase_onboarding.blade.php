{{-- ==========================================================================
   Fase 1: Onboarding (Belum Mendaftar / Profil Baru) - Dashboard Peserta
   Desain Modern Clean SaaS (Linear / Shadcn UI Inspired)
   ========================================================================== --}}
<div class="saas-card">
    <div class="saas-card-header">
        <div>
            <h3 class="saas-item-title" style="font-family: var(--font-display); font-size: 1rem; font-weight: 700; color: var(--neutral-900, #0f172a); margin: 0;">
                Panduan Alur Pendaftaran Magang
            </h3>
            <p class="saas-card-subtitle" style="font-size: 0.8125rem; color: var(--neutral-500, #64748b); margin: 3px 0 0 0;">
                Ikuti 3 tahapan mudah di bawah ini untuk memulai pengajuan magang di Diskominfo Sleman.
            </p>
        </div>
        <span class="saas-badge-portal" style="font-size: 0.75rem; font-weight: 700; color: #2563eb; background: #eff6ff; padding: 4px 10px; border-radius: 9999px; border: 1px solid #bfdbfe;">
            Tahap Awal
        </span>
    </div>

    <div class="saas-card-body">
        <div class="saas-stepper">
            {{-- Langkah 1: Biodata --}}
            <a href="{{ route('peserta.profil.show') }}" class="saas-step-item {{ $profil ? 'completed' : 'active' }}" style="text-decoration: none; color: inherit; cursor: pointer;">
                <div class="saas-step-badge">
                    @if($profil)
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    @else
                        1
                    @endif
                </div>
                <div style="flex: 1;">
                    <div class="saas-step-title" style="font-family: var(--font-display); font-size: 0.9rem; font-weight: 700; color: var(--neutral-900, #0f172a); margin-bottom: 2px; display: flex; align-items: center; justify-content: space-between;">
                        <span>1. Lengkapi Biodata & Profil Peserta</span>
                        <span style="font-size: 0.775rem; color: #2563eb; font-weight: 600;">{{ $profil ? 'Perbarui &rarr;' : 'Isi Sekarang &rarr;' }}</span>
                    </div>
                    <p class="saas-step-desc" style="font-size: 0.8125rem; color: var(--neutral-600, #475569); margin: 0; line-height: 1.5;">
                        Isi asal universitas/sekolah, NIM/NISN, jurusan, kontak WhatsApp, dan kontak darurat.
                    </p>
                </div>
            </a>

            {{-- Langkah 2: Periode & Kuota --}}
            <a href="{{ route('peserta.pendaftaran.index') }}" class="saas-step-item {{ $profil ? 'active' : '' }}" style="text-decoration: none; color: inherit; cursor: pointer;">
                <div class="saas-step-badge">2</div>
                <div style="flex: 1;">
                    <div class="saas-step-title" style="font-family: var(--font-display); font-size: 0.9rem; font-weight: 700; color: var(--neutral-900, #0f172a); margin-bottom: 2px; display: flex; align-items: center; justify-content: space-between;">
                        <span>2. Pilih Periode & Kuota Magang</span>
                        <span style="font-size: 0.775rem; color: #2563eb; font-weight: 600;">Lihat Kuota &rarr;</span>
                    </div>
                    <p class="saas-step-desc" style="font-size: 0.8125rem; color: var(--neutral-600, #475569); margin: 0; line-height: 1.5;">
                        Tentukan bulan pelaksanaan magang yang kuotanya masih tersedia sesuai program studi Anda.
                    </p>
                </div>
            </a>

            {{-- Langkah 3: Unggah Berkas --}}
            <a href="{{ route('peserta.pendaftaran.index') }}" class="saas-step-item" style="text-decoration: none; color: inherit; cursor: pointer;">
                <div class="saas-step-badge">3</div>
                <div style="flex: 1;">
                    <div class="saas-step-title" style="font-family: var(--font-display); font-size: 0.9rem; font-weight: 700; color: var(--neutral-900, #0f172a); margin-bottom: 2px; display: flex; align-items: center; justify-content: space-between;">
                        <span>3. Unggah Berkas Persyaratan & Kirim</span>
                        <span style="font-size: 0.775rem; color: #2563eb; font-weight: 600;">Ajukan &rarr;</span>
                    </div>
                    <p class="saas-step-desc" style="font-size: 0.8125rem; color: var(--neutral-600, #475569); margin: 0; line-height: 1.5;">
                        Unggah surat pengantar resmi dari kampus/sekolah dan proposal, kemudian ajukan untuk diverifikasi.
                    </p>
                </div>
            </a>
        </div>

        <div class="saas-divider" style="display: flex; gap: 12px; flex-wrap: wrap; margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--neutral-150, #f1f5f9);">
            @if(!$profil)
                <a href="{{ route('peserta.profil.show') }}" class="saas-btn-primary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>Lengkapi Biodata Sekarang</span>
                </a>
            @else
                <a href="{{ route('peserta.pendaftaran.index') }}" class="saas-btn-primary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Buat Pengajuan Pendaftaran Magang</span>
                </a>
                <a href="{{ route('peserta.profil.show') }}" class="saas-btn-secondary">
                    <span>Periksa Biodata Saya</span>
                </a>
            @endif
        </div>
    </div>
</div>
