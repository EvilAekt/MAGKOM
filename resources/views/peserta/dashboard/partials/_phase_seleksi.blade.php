{{-- ==========================================================================
   Fase 2: Seleksi Pendaftaran (Pendaftaran Aktif Berjalan) - Dashboard Peserta
   Desain Modern Clean SaaS (Linear / Shadcn UI Inspired)
   ========================================================================== --}}
@if($pendaftaran)
    @php
        $status = $pendaftaran->status;
        $statusStyle = match($status) {
            \App\Enums\StatusPendaftaranEnum::Diterima => ['bg' => '#f0fdf4', 'color' => '#16a34a', 'border' => '#bbf7d0'],
            \App\Enums\StatusPendaftaranEnum::Diajukan, \App\Enums\StatusPendaftaranEnum::WaitingList => ['bg' => '#fffbeb', 'color' => '#d97706', 'border' => '#fde68a'],
            \App\Enums\StatusPendaftaranEnum::PerluPerbaikan, \App\Enums\StatusPendaftaranEnum::Ditolak, \App\Enums\StatusPendaftaranEnum::Dibatalkan => ['bg' => '#fef2f2', 'color' => '#dc2626', 'border' => '#fecaca'],
            default => ['bg' => '#f8fafc', 'color' => '#64748b', 'border' => '#e2e8f0'],
        };
    @endphp

    <div class="saas-card">
        <div class="saas-card-header">
            <div>
                <h3 class="saas-item-title" style="font-family: var(--font-display); font-size: 1rem; font-weight: 700; color: var(--neutral-900, #0f172a); margin: 0;">
                    Status Pendaftaran Magang Aktif
                </h3>
                <p class="saas-card-subtitle" style="font-size: 0.8125rem; color: var(--neutral-500, #64748b); margin: 3px 0 0 0;">
                    Pengajuan nomor <strong>#{{ $pendaftaran->id }}</strong> sedang dalam tahapan verifikasi berkas.
                </p>
            </div>
            <span style="font-size: 0.775rem; font-weight: 700; background: {{ $statusStyle['bg'] }}; color: {{ $statusStyle['color'] }}; border: 1px solid {{ $statusStyle['border'] }}; padding: 4px 12px; border-radius: 9999px;">
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
                <div class="saas-alert-catatan" style="margin-top: 18px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 12px; padding: 14px 18px; display: flex; align-items: flex-start; gap: 12px;">
                    <span style="font-size: 1.1rem; line-height: 1;">📝</span>
                    <div>
                        <h5 style="font-family: var(--font-display); font-size: 0.8125rem; font-weight: 700; color: #92400e; margin: 0 0 4px 0;">Catatan Verifikator Diskominfo:</h5>
                        <p style="font-size: 0.8125rem; color: #b45309; line-height: 1.5; margin: 0;">
                            {{ $pendaftaran->catatan_revisi }}
                        </p>
                    </div>
                </div>
            @endif

            <div class="saas-divider" style="display: flex; gap: 12px; flex-wrap: wrap; margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--neutral-150, #f1f5f9);">
                <a href="{{ route('peserta.pendaftaran.detail', $pendaftaran) }}" class="saas-btn-primary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-1.943-9.542-7z"></path>
                    </svg>
                    <span>Buka Berkas & Detail Pendaftaran</span>
                </a>

                @if($pendaftaran->penempatan)
                    <a href="{{ route('peserta.penempatan.surat-diterima.pratinjau', $pendaftaran->penempatan) }}" class="saas-btn-secondary" target="_blank">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Unduh Surat Diterima (PDF)</span>
                    </a>
                @endif
            </div>
        </div>
    </div>
@endif
