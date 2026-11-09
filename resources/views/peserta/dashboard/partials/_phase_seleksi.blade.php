{{-- ==========================================================================
   Fase 2: Seleksi Pendaftaran (Pendaftaran Aktif Berjalan) - Dashboard Peserta
   Desain Modern Clean SaaS & DS-MagKom (Gentelella Parity)
   ========================================================================== --}}
@php
    /** @var \App\Models\Pendaftaran|null $pendaftaran */
@endphp
@if($pendaftaran)
    @php
        $status = $pendaftaran->status;
        $statusClass = match($status) {
            \App\Enums\StatusPendaftaranEnum::Diterima => 'saas-status-success',
            \App\Enums\StatusPendaftaranEnum::Diajukan => 'saas-status-info',
            \App\Enums\StatusPendaftaranEnum::WaitingList => 'saas-status-warning',
            \App\Enums\StatusPendaftaranEnum::PerluPerbaikan, \App\Enums\StatusPendaftaranEnum::BerkasBelumLengkap => 'saas-status-amber',
            \App\Enums\StatusPendaftaranEnum::Ditolak, \App\Enums\StatusPendaftaranEnum::Dibatalkan => 'saas-status-danger',
            default => 'saas-status-default',
        };

        $isPending = in_array($status, [\App\Enums\StatusPendaftaranEnum::PerluPerbaikan, \App\Enums\StatusPendaftaranEnum::BerkasBelumLengkap]);
        $regNumber = '#REG-' . str_pad((string) $pendaftaran->id, 5, '0', STR_PAD_LEFT);
        $periodeFormatted = $pendaftaran->bulanMagang ? \Carbon\Carbon::parse($pendaftaran->bulanMagang->bulan_mulai)->isoFormat('MMMM Y') : '-';
    @endphp

    <div class="saas-card saas-content-card">
        <div class="saas-card-header" style="margin-bottom: 12px; padding-bottom: 10px; border-bottom: 1px solid #F1F5F9;">
            <div class="saas-heading-row">
                <span class="saas-accent-line" aria-hidden="true"></span>
                <h3 class="saas-card-heading">Status Pendaftaran Magang Aktif</h3>
                <span class="sr-only">{{ $status->label() }}</span>
            </div>
        </div>

        <div class="saas-card-body" style="padding: 0;">
            <div class="saas-pendaftaran-stream-row">
                <div class="saas-pendaftaran-stream-left">
                    <div class="saas-pendaftaran-stream-info">
                        <div class="saas-pendaftaran-stream-title">
                            <span class="saas-reg-chip">{{ $regNumber }}</span>
                            <a href="{{ route('peserta.pendaftaran.detail', $pendaftaran) }}" class="saas-pendaftaran-stream-title-text">
                                {{ $periodeFormatted }}
                            </a>
                        </div>
                        <div class="saas-pendaftaran-stream-meta">
                            <strong>{{ $pendaftaran->kuota->jurusan->nama_jurusan ?? '-' }}</strong>
                            <span class="saas-meta-bullet">&bull;</span>
                            <span>Durasi: <strong>{{ $pendaftaran->durasi_bulan }} Bulan</strong></span>
                            <span class="saas-meta-bullet">&bull;</span>
                            <span>Berkas: <strong>{{ $pendaftaran->dokumen->count() }} Terunggah</strong></span>
                        </div>
                    </div>
                </div>

                <div class="saas-pendaftaran-stream-right">
                    <a href="{{ route('peserta.pendaftaran.detail', $pendaftaran) }}" class="saas-btn-primary">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-1.943-9.542-7z"></path>
                        </svg>
                        <span>Buka Berkas & Detail Pendaftaran</span>
                    </a>

                    @if($pendaftaran->penempatan)
                        <a href="{{ route('peserta.penempatan.surat-diterima.pratinjau', $pendaftaran->penempatan) }}" class="saas-btn-secondary" target="_blank" rel="noopener noreferrer">
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span>Unduh Surat Diterima (PDF)</span>
                        </a>
                    @endif
                </div>
            </div>

            @if($pendaftaran->catatan_revisi)
                <div class="saas-alert-catatan" style="margin-top: 14px; margin-bottom: 0;">
                    <span class="saas-alert-catatan-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                    </span>
                    <div class="saas-alert-catatan-content">
                        <h5 class="saas-alert-catatan-title">Catatan Verifikator Diskominfo:</h5>
                        <p class="saas-alert-catatan-desc">
                            {{ $pendaftaran->catatan_revisi }}
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endif
