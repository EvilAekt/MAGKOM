{{-- ==========================================================================
   Fase 4: Selesai Magang (Pasca Penilaian / Kelulusan) - Dashboard Peserta
   ========================================================================== --}}
@if($penempatan)
    <x-gentelella.x-panel
        title="Penyelesaian Program Magang Dinas Kominfo Sleman"
        subtitle="🎉 Selamat! Masa Magang Telah Selesai"
    >
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px;">
            <div>
                <span class="badge badge-success" style="font-size: 14px; padding: 6px 16px; margin-bottom: 12px;">
                    🎉 Selamat! Masa Magang Telah Selesai
                </span>
                <p style="font-size: 14px; color: var(--neutral-600); max-width: 650px; line-height: 1.5; margin-top: 8px;">
                    Anda telah menuntaskan seluruh kewajiban magang pada <strong>{{ $penempatan->bidang->nama_bidang ?? 'Dinas Kominfo Sleman' }}</strong> bersama pembimbing lapangan <strong>{{ $penempatan->pembimbing->nama ?? $penempatan->pembimbing->name ?? '-' }}</strong>.
                </p>
            </div>

            @if($isFinalNilai)
                <div class="completion-grade-box">
                    <span style="font-size: 12px; font-weight: 600; color: var(--neutral-500); text-transform: uppercase;">Predikat Kelulusan</span>
                    <div class="completion-grade-score">
                        {{ $penilaian->predikat ?? 'A' }}
                    </div>
                    <span style="font-size: 13px; color: var(--neutral-600);">Skor: {{ number_format($penilaian->nilai_akhir, 2) }} / 100</span>
                </div>
            @endif
        </div>

        @if($isFinalNilai && $penilaian->catatan)
            <div class="completion-notes-box">
                <strong style="font-size: 13px; color: var(--neutral-700); display: block; margin-bottom: 4px;">Catatan & Evaluasi Pembimbing:</strong>
                <p style="font-size: 14px; color: var(--neutral-800); font-style: italic;">"{{ $penilaian->catatan }}"</p>
            </div>
        @endif

        <div style="margin-top: 24px; display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="{{ route('peserta.nilai.show') }}" class="btn btn-primary">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Lihat Rincian Nilai 5 Rubrik
            </a>

            @if($isFinalNilai)
                <a href="{{ route('peserta.nilai.unduh') }}" class="btn btn-secondary">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Unduh Lembar Nilai Resmi (PDF)
                </a>
            @endif
        </div>
    </x-gentelella.x-panel>
@endif
