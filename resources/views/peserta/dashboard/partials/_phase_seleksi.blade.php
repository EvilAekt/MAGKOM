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
            \App\Enums\StatusPendaftaranEnum::Diajukan, \App\Enums\StatusPendaftaranEnum::WaitingList => 'saas-status-warning',
            \App\Enums\StatusPendaftaranEnum::PerluPerbaikan, \App\Enums\StatusPendaftaranEnum::Ditolak, \App\Enums\StatusPendaftaranEnum::Dibatalkan => 'saas-status-danger',
            default => 'saas-status-default',
        };
    @endphp

    <div class="saas-card">
        <div class="saas-card-header">
            <div>
                <h3 class="saas-item-title">
                    Status Pendaftaran Magang Aktif
                </h3>
                <p class="saas-card-subtitle">
                    Pengajuan nomor <strong>#{{ $pendaftaran->id }}</strong> sedang dalam tahapan verifikasi berkas.
                </p>
            </div>
            <span class="saas-status-pill {{ $statusClass }}">
                ● {{ $status->label() }}
            </span>
        </div>

        <div class="saas-card-body">
            <div class="saas-info-strip">
                <div class="saas-info-item">
                    <span>Periode Pelaksanaan</span>
                    <strong>{{ $pendaftaran->bulanMagang ? \Carbon\Carbon::parse($pendaftaran->bulanMagang->bulan_mulai)->isoFormat('MMMM Y') : '-' }}</strong>
                </div>
                <div class="saas-info-item">
                    <span>Jurusan Terdaftar</span>
                    <strong>{{ $pendaftaran->kuota->jurusan->nama_jurusan ?? '-' }}</strong>
                </div>
                <div class="saas-info-item">
                    <span>Durasi Magang</span>
                    <strong>{{ $pendaftaran->durasi_bulan }} Bulan</strong>
                </div>
                <div class="saas-info-item">
                    <span>Kelengkapan Berkas</span>
                    <strong>{{ $pendaftaran->dokumen->count() }} Berkas Terunggah</strong>
                </div>
            </div>

            @if($pendaftaran->catatan_revisi)
                <div class="saas-alert-catatan">
                    <span class="saas-alert-catatan-icon" aria-hidden="true">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                    </span>
                    <div>
                        <h5 class="saas-alert-catatan-title">Catatan Verifikator Diskominfo:</h5>
                        <p class="saas-alert-catatan-desc">
                            {{ $pendaftaran->catatan_revisi }}
                        </p>
                    </div>
                </div>
            @endif

            <div class="saas-divider-actions">
                <a href="{{ route('peserta.pendaftaran.detail', $pendaftaran) }}" class="saas-btn-primary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-1.943-9.542-7z"></path>
                    </svg>
                    <span>Buka Berkas & Detail Pendaftaran</span>
                </a>

                @if($pendaftaran->penempatan)
                    <a href="{{ route('peserta.penempatan.surat-diterima.pratinjau', $pendaftaran->penempatan) }}" class="saas-btn-secondary" target="_blank" rel="noopener noreferrer">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Unduh Surat Diterima (PDF)</span>
                    </a>
                @endif
            </div>
        </div>
    </div>
@endif
