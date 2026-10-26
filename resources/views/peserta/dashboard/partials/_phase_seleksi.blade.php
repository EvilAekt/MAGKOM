{{-- ==========================================================================
   Fase 2: Seleksi Pendaftaran (Pendaftaran Aktif Berjalan) - Dashboard Peserta
   ========================================================================== --}}
@if($pendaftaran)
    @php
        $status = $pendaftaran->status;
        $badgeClass = match($status) {
            \App\Enums\StatusPendaftaranEnum::Diterima => 'badge-success',
            \App\Enums\StatusPendaftaranEnum::Diajukan, \App\Enums\StatusPendaftaranEnum::WaitingList => 'badge-warning',
            \App\Enums\StatusPendaftaranEnum::PerluPerbaikan, \App\Enums\StatusPendaftaranEnum::Ditolak, \App\Enums\StatusPendaftaranEnum::Dibatalkan => 'badge-danger',
            default => 'badge-neutral',
        };
    @endphp

    <x-gentelella.x-panel
        title="Status Pendaftaran Magang Aktif"
        subtitle="Pengajuan nomor #{{ $pendaftaran->id }} sedang diproses dalam tahapan seleksi."
    >
        <x-slot:actions>
            <span class="badge {{ $badgeClass }}" style="font-size: 13px; padding: 6px 14px;">
                ● {{ $status->label() }}
            </span>
        </x-slot:actions>

        <div class="info-strip">
            <div class="info-strip-item">
                <span>Periode Magang:</span>
                <strong>{{ $pendaftaran->bulanMagang ? \Carbon\Carbon::parse($pendaftaran->bulanMagang->bulan_mulai)->isoFormat('MMMM Y') : '-' }}</strong>
            </div>
            <div class="info-strip-item">
                <span>Jurusan Terdaftar:</span>
                <strong>{{ $pendaftaran->kuota->jurusan->nama_jurusan ?? '-' }}</strong>
            </div>
            <div class="info-strip-item">
                <span>Durasi:</span>
                <strong>{{ $pendaftaran->durasi_bulan }} Bulan</strong>
            </div>
            <div class="info-strip-item">
                <span>Kelengkapan Berkas:</span>
                <strong>{{ $pendaftaran->dokumen->count() }} Berkas Terunggah</strong>
            </div>
        </div>

        @if($pendaftaran->catatan_revisi)
            <div style="margin-bottom: 16px;">
                <x-gentelella.callout type="warning" title="Catatan Admin Verifikator">
                    {{ $pendaftaran->catatan_revisi }}
                </x-gentelella.callout>
            </div>
        @endif

        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="{{ route('peserta.pendaftaran.detail', $pendaftaran) }}" class="btn btn-primary">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-1.943-9.542-7z"></path>
                </svg>
                Buka Berkas & Detail Pendaftaran
            </a>

            @if($pendaftaran->penempatan)
                <a href="{{ route('peserta.penempatan.surat-diterima', $pendaftaran->penempatan) }}" class="btn btn-secondary" target="_blank">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Unduh Surat Keterangan Diterima (PDF)
                </a>
            @endif
        </div>
    </x-gentelella.x-panel>
@endif
