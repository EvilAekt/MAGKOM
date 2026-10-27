{{-- ==========================================================================
   Fase 4: Selesai Magang (Pasca Penilaian / Kelulusan) - Dashboard Peserta
   Desain Modern Clean SaaS (Linear / Shadcn UI Inspired)
   ========================================================================== --}}
@if($penempatan)
    <div class="saas-card">
        <div class="saas-card-header">
            <div>
                <h3 class="saas-item-title" style="font-family: var(--font-display); font-size: 1rem; font-weight: 700; color: var(--neutral-900, #0f172a); margin: 0;">
                    Penyelesaian Program Magang Diskominfo Sleman
                </h3>
                <p class="saas-card-subtitle" style="font-size: 0.8125rem; color: var(--neutral-500, #64748b); margin: 3px 0 0 0;">
                    Selamat! Anda telah menuntaskan seluruh periode magang dengan baik.
                </p>
            </div>
            <span class="saas-badge-approved" style="font-size: 0.775rem; font-weight: 700; background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; padding: 4px 12px; border-radius: 9999px;">
                🎉 Lulus & Selesai
            </span>
        </div>

        <div class="saas-card-body">
            <div class="saas-selesai-banner" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px 24px;">
                <div style="max-width: 580px;">
                    <p style="font-size: 0.875rem; color: var(--neutral-700, #334155); line-height: 1.6; margin: 0;">
                        Seluruh kewajiban magang pada <strong>{{ $penempatan->bidang->nama_bidang ?? 'Dinas Kominfo Sleman' }}</strong> bersama pembimbing lapangan <strong>{{ $penempatan->pembimbing->nama ?? $penempatan->pembimbing->name ?? '-' }}</strong> telah diverifikasi dan dinilai secara lengkap.
                    </p>
                </div>

                @if($isFinalNilai)
                    <div class="saas-predikat-card" style="display: flex; flex-direction: column; align-items: center; justify-content: center; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 24px; min-width: 140px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                        <span style="font-size: 0.725rem; font-weight: 700; color: var(--neutral-500, #64748b); text-transform: uppercase; letter-spacing: 0.04em;">Predikat Akhir</span>
                        <div style="font-family: var(--font-display); font-size: 2.2rem; font-weight: 800; color: #16a34a; line-height: 1.1; margin: 4px 0;">
                            {{ $penilaian->predikat ?? 'A' }}
                        </div>
                        <span style="font-size: 0.775rem; font-weight: 600; color: var(--neutral-600, #475569);">Skor: {{ number_format($penilaian->nilai_akhir, 2) }} / 100</span>
                    </div>
                @endif
            </div>

            @if($isFinalNilai && $penilaian->catatan)
                <div class="saas-evaluasi-box" style="margin-top: 18px; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 16px 20px;">
                    <strong style="font-family: var(--font-display); font-size: 0.8125rem; color: #1e40af; display: block; margin-bottom: 4px;">Catatan & Evaluasi Pembimbing Lapangan:</strong>
                    <p style="font-size: 0.84rem; color: #1e3a8a; line-height: 1.5; margin: 0; font-style: italic;">"{{ $penilaian->catatan }}"</p>
                </div>
            @endif

            <div class="saas-divider" style="display: flex; gap: 12px; flex-wrap: wrap; margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--neutral-150, #f1f5f9);">
                <a href="{{ route('peserta.nilai.show') }}" class="saas-btn-primary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Lihat Rincian Nilai & Rubrik</span>
                </a>

                @if($isFinalNilai)
                    <a href="{{ route('peserta.nilai.unduh') }}" class="saas-btn-secondary">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        <span>Unduh Lembar Nilai Resmi (PDF)</span>
                    </a>
                @endif
            </div>
        </div>
    </div>
@endif
