{{-- ==========================================================================
   Fase 1: Onboarding (Belum Mendaftar / Profil Baru) - Dashboard Peserta
   Desain Modern Clean SaaS & DS-MagKom (Gentelella Parity)
   ========================================================================== --}}
@php
    /** @var \App\Models\PesertaProfil|null $profil */
@endphp
<div class="saas-card x_panel">
    <div class="saas-card-header x_title">
        <div>
            <h3 class="saas-item-title">
                Panduan Alur Pendaftaran Magang
            </h3>
            <p class="saas-card-subtitle">
                Ikuti 3 tahapan mudah di bawah ini untuk memulai pengajuan magang di Diskominfo Sleman.
            </p>
        </div>
        <span class="saas-badge-portal">
            Tahap Awal
        </span>
    </div>

    <div class="saas-card-body">
        <div class="saas-stepper">
            {{-- Langkah 1: Biodata --}}
            <a href="{{ route('peserta.profil.show') }}" class="saas-step-item {{ $profil ? 'completed' : 'active' }}" title="Lengkapi Biodata & Profil">
                <div class="saas-step-badge">
                    @if($profil)
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    @else
                        1
                    @endif
                </div>
                <div class="saas-step-body">
                    <div class="saas-step-title">
                        <span>1. Lengkapi Biodata & Profil Peserta</span>
                        <span class="saas-step-action">{{ $profil ? 'Perbarui &rarr;' : 'Isi Sekarang &rarr;' }}</span>
                    </div>
                    <p class="saas-step-desc">
                        Isi asal universitas/sekolah, NIM/NISN, jurusan, kontak WhatsApp, dan kontak darurat.
                    </p>
                </div>
            </a>

            {{-- Langkah 2: Periode & Kuota --}}
            <a href="{{ route('peserta.pendaftaran.index') }}" class="saas-step-item {{ $profil ? 'active' : '' }}" title="Pilih Periode & Kuota Magang">
                <div class="saas-step-badge">2</div>
                <div class="saas-step-body">
                    <div class="saas-step-title">
                        <span>2. Pilih Periode & Kuota Magang</span>
                        <span class="saas-step-action">Lihat Kuota &rarr;</span>
                    </div>
                    <p class="saas-step-desc">
                        Tentukan bulan pelaksanaan magang yang kuotanya masih tersedia sesuai program studi Anda.
                    </p>
                </div>
            </a>

            {{-- Langkah 3: Unggah Berkas --}}
            <a href="{{ route('peserta.pendaftaran.index') }}" class="saas-step-item {{ $profil ? 'active' : '' }}" title="Unggah Berkas & Ajukan Pendaftaran">
                <div class="saas-step-badge">3</div>
                <div class="saas-step-body">
                    <div class="saas-step-title">
                        <span>3. Unggah Berkas & Ajukan Pendaftaran</span>
                        <span class="saas-step-action">Ajukan &rarr;</span>
                    </div>
                    <p class="saas-step-desc">
                        Unggah surat pengantar resmi dari kampus/sekolah dan proposal, kemudian ajukan untuk diverifikasi.
                    </p>
                </div>
            </a>
        </div>

        <div class="saas-divider-actions">
            @if(!$profil)
                <a href="{{ route('peserta.profil.show') }}" class="saas-btn-primary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>Lengkapi Biodata Sekarang</span>
                </a>
            @else
                <a href="{{ route('peserta.pendaftaran.index') }}" class="saas-btn-primary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
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
