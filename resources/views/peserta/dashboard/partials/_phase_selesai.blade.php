{{-- ==========================================================================
   Fase 4: Selesai Magang (Kelulusan) - Dashboard Peserta
   Desain Modern Clean SaaS & DS-MagKom (Gentelella Parity)
   ========================================================================== --}}
@php
    /** @var \App\Models\Penempatan|null $penempatan */
    $hasSuratSelesai = $penempatan && ! empty($penempatan->file_surat_selesai) && \Illuminate\Support\Facades\Storage::disk('local')->exists($penempatan->file_surat_selesai);
    $isKelompok = $penempatan?->isKelompok();
    $namaKelompok = $penempatan?->pendaftaran?->kelompokMagang?->nama_kelompok;
@endphp
@if($penempatan)
    <div class="saas-card">
        <div class="saas-card-header">
            <div>
                <h3 class="saas-item-title">
                    {{ __('Penyelesaian Program Magang Diskominfo Sleman') }}
                </h3>
                <p class="saas-card-subtitle">
                    {{ __('Selamat! Anda telah menuntaskan seluruh masa magang dengan baik.') }}
                </p>
            </div>
            <span class="saas-badge-approved" style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 999px; font-size: 0.78rem; font-weight: 700; background: #EFF6FF; color: #1D4ED8; border: 1px solid #BFDBFE;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <circle cx="12" cy="8" r="7"></circle>
                    <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                </svg>
                <span>{{ __('Selesai') }}</span>
            </span>
        </div>

        <div class="saas-card-body">
            <div class="saas-selesai-banner">
                <div class="saas-selesai-text-box">
                    <p class="saas-selesai-desc">
                        {{ __('Seluruh kewajiban magang pada :bidang bersama pembimbing lapangan :pembimbing telah diverifikasi dan dinyatakan selesai secara lengkap.', [
                            'bidang' => $penempatan->bidang->nama_bidang ?? 'Dinas Kominfo Sleman',
                            'pembimbing' => $penempatan->pembimbing->nama ?? 'Tim Pembimbing Diskominfo',
                        ]) }}
                    </p>
                    @if($isKelompok && $namaKelompok)
                        <div style="margin-top: 6px; font-size: 0.8rem; font-weight: 700; color: #1E40AF;">
                            <span>{{ __('Anggota Tim: :kelompok', ['kelompok' => $namaKelompok]) }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Kartu Dokumen Resmi Surat Keterangan Selesai Magang --}}
            <div style="margin-top: 1rem; padding: 16px; border-radius: 12px; border: 1.5px solid {{ $hasSuratSelesai ? '#BFDBFE' : '#FDE68A' }}; background: {{ $hasSuratSelesai ? '#EFF6FF' : '#FFFBEB' }}; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 40px; height: 40px; border-radius: 10px; background: {{ $hasSuratSelesai ? '#DBEAFE' : '#FEF3C7' }}; color: {{ $hasSuratSelesai ? '#1D4ED8' : '#D97706' }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                    </div>
                    <div>
                        <h4 style="margin: 0 0 2px; font-size: 0.95rem; font-weight: 800; color: {{ $hasSuratSelesai ? '#1E3A8A' : '#92400E' }};">
                            {{ $hasSuratSelesai ? __('Surat Keterangan Selesai Magang (Dokumen Resmi)') : __('Surat Keterangan Selesai Sedang Diproses') }}
                        </h4>
                        <p style="margin: 0; font-size: 0.8rem; color: {{ $hasSuratSelesai ? '#1D4ED8' : '#B45309' }};">
                            @if($hasSuratSelesai)
                                {{ __('Diterbitkan pada :tanggal WIB. Dokumen sah terverifikasi dinas.', ['tanggal' => $penempatan->tgl_upload_surat_selesai?->translatedFormat('d M Y H:i') ?? '-']) }}
                            @else
                                {{ __('Berkas resmi sedang dalam proses penandatanganan dan pengunggahan oleh administrator.') }}
                            @endif
                        </p>
                    </div>
                </div>

                @if($hasSuratSelesai)
                    <a href="{{ route('peserta.selesai-magang.unduh') }}" 
                       class="btn-doc-action btn-doc-download" 
                       style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; font-size: 0.85rem; font-weight: 800; background: #003566; color: #FFFFFF; border-radius: 8px; text-decoration: none;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        <span>{{ __('Unduh Surat Selesai (PDF)') }}</span>
                    </a>
                @endif
            </div>
        </div>
    </div>
@endif
