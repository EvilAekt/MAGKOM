{{-- ==========================================================================
   Aside Widget - Dashboard Peserta Magang
   Desain DS-MagKom (Gentelella Parity & Soft-Simple-Elegan)
   Profil Pembimbing, Grid Aksi Cepat Peserta & Banner Ketentuan BR-09
   ========================================================================== --}}
@php
    /** @var string $fase */
    /** @var \App\Models\Penempatan|null $penempatan */
    /** @var \App\Models\User $user */
    /** @var \App\Models\PesertaProfil|null $profil */
@endphp

@if($fase === 'aktif' && $penempatan)
    {{-- CARD 1: PROFIL PEMBIMBING LAPANGAN --}}
    <div class="saas-card saas-aside-card">
        <div class="saas-aside-section-title">
            <span class="saas-aside-title-icon">
                <span class="saas-icon-badge-blue" aria-hidden="true">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </span>
                <span>Pembimbing Lapangan</span>
            </span>
            <span class="saas-badge-success-sm">Aktif</span>
        </div>

        <div class="saas-aside-mentor-profile">
            <div class="saas-aside-avatar" aria-hidden="true">
                {{ strtoupper(substr($penempatan->pembimbing->nama ?? 'PL', 0, 2)) }}
            </div>
            <div class="saas-aside-mentor-info">
                <h4 class="saas-aside-title">
                    {{ $penempatan->pembimbing->nama ?? 'Belum Ditugaskan' }}
                </h4>
                <p class="saas-aside-subtitle">
                    {{ $penempatan->bidang->nama_bidang ?? 'Dinas Komunikasi dan Informatika' }}
                </p>
            </div>
        </div>

        <div class="saas-aside-meta-list">
            <div class="saas-aside-contact-item">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                    <polyline points="22,6 12,13 2,6"></polyline>
                </svg>
                <span>{{ $penempatan->pembimbing->email ?? '-' }}</span>
            </div>
            <div class="saas-aside-contact-item">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M3 21h18M3 7v14M21 7v14M6 7V3h12v4M9 11h2M13 11h2M9 15h2M13 15h2"></path>
                </svg>
                <span>{{ $penempatan->bidang ? 'Bidang ' . $penempatan->bidang->nama_bidang : 'Dinas Kominfo Sleman' }}</span>
            </div>
        </div>

        @php
            $rawAsideHp = $penempatan->pembimbing->no_hp ?? null;
            $cleanAsideHp = $rawAsideHp ? preg_replace('/[^0-9]/', '', (string)$rawAsideHp) : null;
            if (is_string($cleanAsideHp) && str_starts_with($cleanAsideHp, '0')) {
                $cleanAsideHp = '62' . substr($cleanAsideHp, 1);
            }
            // Nomor resmi Helpdesk MagKom Diskominfo Sleman (Pusat Bantuan & Layanan Peserta)
            $helpdeskWaNumber = '628122788990';
            $waUrl = !empty($cleanAsideHp) 
                ? 'https://wa.me/' . $cleanAsideHp
                : 'https://wa.me/' . $helpdeskWaNumber . '?text=' . urlencode('Halo Admin MagKom Diskominfo Sleman, saya peserta ' . ($user->nama ?? ''));
            $waLabel = !empty($cleanAsideHp) ? 'Hubungi via WhatsApp' : 'Hubungi Helpdesk Magang';
        @endphp
        <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer" class="saas-btn-whatsapp" title="{{ $waLabel }}">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
            </svg>
            <span>{{ $waLabel }}</span>
        </a>
    </div>

    {{-- CARD 2: AKSI CEPAT PESERTA (GRID SIMETRIS) --}}
    <div class="saas-card saas-aside-card">
        <div class="saas-aside-section-title">
            <span class="saas-aside-title-icon">
                <span class="saas-icon-badge-amber" aria-hidden="true">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                    </svg>
                </span>
                <span>Aksi Cepat Peserta</span>
            </span>
        </div>

        <div class="saas-quick-grid">
            <a href="{{ route('peserta.logbook.index') }}" class="saas-quick-card" title="Isi Logbook">
                <span class="saas-quick-icon saas-quick-icon-blue" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </span>
                <span class="saas-quick-label">Isi Logbook</span>
            </a>

            <a href="{{ route('peserta.tugas.index') }}" class="saas-quick-card" title="Daftar Tugas">
                <span class="saas-quick-icon saas-quick-icon-green" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </span>
                <span class="saas-quick-label">Tugas Magang</span>
            </a>

            <a href="{{ route('peserta.penempatan.surat-diterima.pratinjau', $penempatan) }}" target="_blank" rel="noopener noreferrer" class="saas-quick-card" title="Unduh Surat Diterima PDF">
                <span class="saas-quick-icon saas-quick-icon-indigo" aria-hidden="true">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                    </svg>
                </span>
                <span class="saas-quick-label">Surat SK (PDF)</span>
            </a>

            <a href="{{ route('panduan.pdf') }}" target="_blank" rel="noopener noreferrer" class="saas-quick-card" title="Buku Panduan Magang PDF">
                <span class="saas-quick-icon saas-quick-icon-amber" aria-hidden="true">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                </span>
                <span class="saas-quick-label">Panduan PDF</span>
            </a>
        </div>
    </div>

    {{-- CARD 3: BANNER PANDUAN & KETENTUAN LOGBOOK (BR-09) --}}
    <div class="saas-card saas-br09-banner">
        <div class="saas-br09-header">
            <span class="saas-br09-title">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                    <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                </svg>
                <span>Ketentuan Logbook (BR-09)</span>
            </span>
            <span class="saas-br09-badge">Disiplin</span>
        </div>
        <p class="saas-br09-text">
            Pengisian jurnal harian wajib dilaporkan secara disiplin dan diserahkan tiap pekan sebelum <strong>Jumat pukul 23:59 WIB</strong>. Pastikan melampirkan tautan berkas bukti pengerjaan yang valid.
        </p>
    </div>

@else
    {{-- ONBOARDING / SELEKSI ASIDE --}}
    <div class="saas-card saas-aside-card">
        <div class="saas-aside-section-title">
            <span class="saas-aside-title-icon">
                <span class="saas-icon-badge-blue" aria-hidden="true">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                </span>
                <span>Layanan Magang Sleman</span>
            </span>
        </div>

        <p class="saas-aside-subtitle-block">
            Verifikasi berkas persyaratan magang diproses oleh tim Diskominfo Sleman pada hari kerja.
        </p>

        <div class="saas-aside-info-box">
            <div><strong>Hari Kerja:</strong> Senin &ndash; Jumat</div>
            <div><strong>Jam Layanan:</strong> 08:00 &ndash; 15:30 WIB</div>
            <div><strong>Lokasi:</strong> Diskominfo Kab. Sleman</div>
        </div>

        @php
            // Nomor resmi Helpdesk MagKom Diskominfo Sleman (Pusat Bantuan & Layanan Peserta)
            $helpdeskWaNumber = '628122788990';
            $helpdeskWa = 'https://wa.me/' . $helpdeskWaNumber . '?text=' . urlencode('Halo Helpdesk MagKom Diskominfo Sleman, saya ingin bertanya perihal pendaftaran magang.');
        @endphp
        <a href="{{ $helpdeskWa }}" target="_blank" rel="noopener noreferrer" class="saas-btn-whatsapp" title="Hubungi Helpdesk Magang via WhatsApp">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
            </svg>
            <span>Hubungi Helpdesk Magang</span>
        </a>
    </div>

    {{-- CARD 2: BERKAS & PANDUAN RESMI --}}
    <div class="saas-card saas-aside-card">
        <div class="saas-aside-section-title">
            <span class="saas-aside-title-icon">
                <span class="saas-icon-badge-amber" aria-hidden="true">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                    </svg>
                </span>
                <span>Berkas & Panduan</span>
            </span>
        </div>

        <div class="saas-aside-doc-list">
            <a href="{{ route('panduan.pdf') }}" target="_blank" rel="noopener noreferrer" class="saas-doc-row" title="Unduh Buku Panduan Magang PDF">
                <span class="saas-doc-left">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true" style="color: #f59e0b;">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                    <span>Buku Panduan Magang</span>
                </span>
                <span class="saas-doc-right-amber">Unduh &rarr;</span>
            </a>
        </div>
    </div>

    {{-- CARD 3: KETENTUAN PENDAFTARAN (BR-01) --}}
    <div class="saas-card saas-br09-banner">
        <div class="saas-br09-header">
            <span class="saas-br09-title">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <line x1="12" y1="17" x2="12" y2="22"></line>
                    <path d="M5 17h14v-1.76a2 2 0 0 0-1.11-1.79l-1.78-.9A2 2 0 0 1 15 10.76V6h1a1 1 0 0 0 0-2H8a1 1 0 0 0 0 2h1v4.76a2 2 0 0 1-1.11 1.79l-1.78.9A2 2 0 0 0 5 15.24Z"></path>
                </svg>
                <span>Ketentuan Pengajuan (BR-01)</span>
            </span>
            <span class="saas-br09-badge">Penting</span>
        </div>
        <p class="saas-br09-text">
            Pastikan seluruh berkas kampus (Surat Pengantar & Proposal) berformat <strong>PDF resmi</strong> dengan stempel basah atau tanda tangan digital sebelum diunggah.
        </p>
    </div>
@endif
