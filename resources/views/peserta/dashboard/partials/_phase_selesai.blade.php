{{-- ==========================================================================
   Fase 4: Selesai Magang (Pasca Penilaian / Kelulusan) - Dashboard Peserta
   Desain Modern Clean SaaS & DS-MagKom (Gentelella Parity)
   ========================================================================== --}}
@php
    /** @var \App\Models\Penempatan|null $penempatan */
    /** @var bool $isFinalNilai */
    /** @var \App\Models\Penilaian|null $penilaian */
@endphp
@if($penempatan)
    <div class="saas-card">
        <div class="saas-card-header">
            <div>
                <h3 class="saas-item-title">
                    Penyelesaian Program Magang Diskominfo Sleman
                </h3>
                <p class="saas-card-subtitle">
                    Selamat! Anda telah menuntaskan seluruh periode magang dengan baik.
                </p>
            </div>
            <span class="saas-badge-approved">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <circle cx="12" cy="8" r="7"></circle>
                    <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                </svg>
                <span>Lulus &amp; Selesai</span>
            </span>
        </div>

        <div class="saas-card-body">
            <div class="saas-selesai-banner">
                <div class="saas-selesai-text-box">
                    <p class="saas-selesai-desc">
                        Seluruh kewajiban magang pada <strong>{{ $penempatan->bidang->nama_bidang ?? 'Dinas Kominfo Sleman' }}</strong> bersama pembimbing lapangan <strong>{{ $penempatan->pembimbing->nama ?? 'Tim Pembimbing Diskominfo' }}</strong> telah diverifikasi dan dinilai secara lengkap.
                    </p>
                </div>

                @if($isFinalNilai)
                    <div class="saas-predikat-card">
                        <span class="saas-predikat-label">Predikat Akhir</span>
                        <div class="saas-predikat-val">
                            {{ $penilaian->predikat ?? 'A' }}
                        </div>
                        <span class="saas-predikat-score">Skor: {{ number_format((float)($penilaian->nilai_akhir ?? 0), 2) }} / 100</span>
                    </div>
                @endif
            </div>

            @if($isFinalNilai && $penilaian->catatan)
                <div class="saas-evaluasi-box">
                    <strong class="saas-evaluasi-title">Catatan & Evaluasi Pembimbing Lapangan:</strong>
                    <p class="saas-evaluasi-content">"{{ $penilaian->catatan }}"</p>
                </div>
            @endif

            <div class="saas-divider-actions">
                <a href="{{ route('peserta.nilai.show') }}" class="saas-btn-primary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Lihat Rincian Nilai & Rubrik</span>
                </a>

                @if($isFinalNilai)
                    <a href="{{ route('peserta.nilai.unduh') }}" class="saas-btn-secondary">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        <span>Unduh Lembar Nilai Resmi (PDF)</span>
                    </a>
                @endif
            </div>
        </div>
    </div>
@endif
