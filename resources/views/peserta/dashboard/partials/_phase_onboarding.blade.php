{{-- ==========================================================================
   Fase 1: Onboarding (Belum Mendaftar / Profil Baru) - Dashboard Peserta
   Desain Modern Clean SaaS & DS-MagKom (Gentelella Parity)
   ========================================================================== --}}
@php
    /** @var \App\Models\PesertaProfil|null $profil */
    /** @var \App\Models\Pendaftaran|null $pendaftaran */

    // 1. Validasi Kelengkapan Langkah 1 (Biodata & Profil Full 100%)
    if (!isset($isProfilLengkap) || !isset($persentaseProfil)) {
        $kelengkapan = $profil?->hitungKelengkapan() ?? [
            'checklist' => [],
            'persentase' => 0,
            'is_lengkap' => false,
        ];
        $isProfilLengkap = (bool) $kelengkapan['is_lengkap'];
        $persentaseProfil = (int) $kelengkapan['persentase'];
    }

    // 2. Validasi Langkah 2 (Periode & Kuota dipilih -> membuat pendaftaran aktif)
    $hasPendaftaran = (bool) $pendaftaran;
    $canPilihKuota = (bool) $isProfilLengkap;

    // 3. Validasi Langkah 3 (Unggah Berkas & Ajukan Pendaftaran)
    $canUnggahBerkas = (bool) $hasPendaftaran;
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
            {{-- Langkah 1: Biodata & Profil --}}
            <a href="{{ route('peserta.profil.show') }}" class="saas-step-item {{ $isProfilLengkap ? 'completed' : 'active' }}" title="Lengkapi Biodata & Profil">
                <div class="saas-step-badge">
                    @if($isProfilLengkap)
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    @else
                        1
                    @endif
                </div>
                <div class="saas-step-body">
                    <div class="saas-step-title">
                        <span>1. Lengkapi Biodata & Profil Peserta</span>
                        @if($isProfilLengkap)
                            <span class="saas-step-state state-done">✓ Selesai</span>
                        @else
                            <span class="saas-step-state state-action">Belum Lengkap ({{ $persentaseProfil }}%)</span>
                        @endif
                    </div>
                </div>
            </a>

            {{-- Langkah 2: Bulan & Kuota --}}
            @if($canPilihKuota)
                <a href="{{ route('peserta.pendaftaran.index') }}" class="saas-step-item {{ $hasPendaftaran ? 'completed' : 'active' }}" title="Pilih Bulan & Kuota Magang">
                    <div class="saas-step-badge">
                        @if($hasPendaftaran)
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        @else
                            2
                        @endif
                    </div>
                    <div class="saas-step-body">
                        <div class="saas-step-title">
                            <span>2. Pilih Bulan & Kuota Magang</span>
                            @if($hasPendaftaran)
                                <span class="saas-step-state state-done">✓ Terpilih</span>
                            @else
                                <span class="saas-step-state state-action">Pilih Kuota</span>
                            @endif
                        </div>
                    </div>
                </a>
            @else
                <div class="saas-step-item is-locked" role="button" aria-disabled="true" tabindex="-1" title="Lengkapi biodata profil 100% terlebih dahulu untuk membuka pemilihan kuota">
                    <div class="saas-step-badge">2</div>
                    <div class="saas-step-body">
                        <div class="saas-step-title">
                            <span style="color: #64748B;">2. Pilih Bulan & Kuota Magang</span>
                            <span class="saas-step-state state-locked">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                <span>Terkunci</span>
                            </span>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Langkah 3: Unggah Berkas & Ajukan Pendaftaran --}}
            @if($canUnggahBerkas && $pendaftaran)
                @php
                    $statusVal = $pendaftaran->status?->value ?? (string)$pendaftaran->status;
                    $isDiajukan = in_array($statusVal, [
                        \App\Enums\StatusPendaftaranEnum::Diajukan->value,
                        \App\Enums\StatusPendaftaranEnum::WaitingList->value,
                        \App\Enums\StatusPendaftaranEnum::Diterima->value,
                        'diajukan',
                        'waiting_list',
                        'diterima',
                    ], true);
                @endphp
                <a href="{{ route('peserta.pendaftaran.detail', $pendaftaran) }}" class="saas-step-item {{ $isDiajukan ? 'completed' : 'active' }}" title="Unggah Berkas & Ajukan Pendaftaran">
                    <div class="saas-step-badge">
                        @if($isDiajukan)
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        @else
                            3
                        @endif
                    </div>
                    <div class="saas-step-body">
                        <div class="saas-step-title">
                            <span>3. Unggah Berkas & Ajukan Pendaftaran</span>
                            @if($isDiajukan)
                                <span class="saas-step-state state-done">✓ Diajukan</span>
                            @else
                                <span class="saas-step-state state-action">Unggah Berkas</span>
                            @endif
                        </div>
                    </div>
                </a>
            @else
                <div class="saas-step-item is-locked" role="button" aria-disabled="true" tabindex="-1" title="Pilih bulan dan kuota magang terlebih dahulu untuk melanjutkan tahap pemberkasan">
                    <div class="saas-step-badge">3</div>
                    <div class="saas-step-body">
                        <div class="saas-step-title">
                            <span style="color: #64748B;">3. Unggah Berkas & Ajukan Pendaftaran</span>
                            <span class="saas-step-state state-locked">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                <span>Terkunci</span>
                            </span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
