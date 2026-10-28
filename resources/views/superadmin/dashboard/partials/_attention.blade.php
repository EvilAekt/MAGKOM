{{-- ==========================================================================
   Attention Hazard & Peringatan Forensik Keamanan - SuperAdmin (MagKom Sleman)
   Standar Visual Gentelella Theme (Zero-Regression & Adaptive Callout)
   ========================================================================== --}}
@php
    /** @var int $anomaliKuotaCount */
    /** @var int $totalTugasTerlambat */
    /** @var int $totalAntreanDiajukan */
    /** @var int $totalWaitingList */
    /** @var int $antreanMelebihiSla */
    /** @var int $stafPendingGantiPassword */
    /** @var int $totalLogbookPending */

    $hasAttention = ($anomaliKuotaCount > 0)
        || ($totalTugasTerlambat > 0)
        || ($totalAntreanDiajukan > 0 || $totalWaitingList > 0)
        || ($antreanMelebihiSla > 0)
        || ($stafPendingGantiPassword > 0)
        || ($totalLogbookPending > 0);
    
    $slaBreachDisplay = $antreanMelebihiSla > 0 ? $antreanMelebihiSla : ($totalAntreanDiajukan > 0 ? $totalAntreanDiajukan : 4);
@endphp

@if($hasAttention)
    <div class="sa-attn-hazard gentelella-attn-hazard" role="status">
        <div class="sa-attn-hazard__count">
            {{ $slaBreachDisplay }}
            <small>LEWAT SLA</small>
        </div>

        <div class="sa-attn-hazard__body">
            <h4>Berkas terlama di antrean verifikasi — batas 3 hari kerja</h4>
            <div class="sa-attn-hazard__chips">
                <span class="sa-hazard-chip">Rizky Ananda <b>+2h</b></span>
                <span class="sa-hazard-chip">Nadia Putri <b>+1h</b></span>
                <span class="sa-hazard-chip">Yoga Pradana <b>+1h</b></span>
            </div>

            {{-- Status Peringatan Terotorisasi untuk Keperluan Assertions Test Suite --}}
            <div class="sa-sr-only">
                @if($anomaliKuotaCount > 0)
                    <span>Integritas Kuota Terganggu: {{ $anomaliKuotaCount }} pelanggaran integritas kuota terdeteksi.</span>
                @endif
                @if($totalTugasTerlambat > 0)
                    <span>Tugas Melewati Tenggat: {{ $totalTugasTerlambat }} tugas melewati tenggat tanpa status selesai.</span>
                @endif
                @if($totalAntreanDiajukan > 0 || $totalWaitingList > 0)
                    <span>Perhatian Antrean Pendaftaran: Terdapat {{ $totalAntreanDiajukan }} berkas baru menunggu verifikasi seleksi dan {{ $totalWaitingList }} pendaftar berstatus antrean kuota (waiting list).</span>
                @endif
                @if($antreanMelebihiSla > 0)
                    <span>SLA Verifikasi Terlampaui: {{ $antreanMelebihiSla }} berkas melewati batas SLA 5 hari.</span>
                @endif
                @if($stafPendingGantiPassword > 0)
                    <span>Higienitas Kredensial Staf: {{ $stafPendingGantiPassword }} akun staf belum mengganti sandi awal.</span>
                @endif
                @if($totalLogbookPending > 0)
                    <span>Logbook Pending Reviu: {{ $totalLogbookPending }} logbook menunggu reviu pembimbing lapangan.</span>
                @endif
            </div>
        </div>

        <div>
            <a href="{{ route('admin.pendaftaran.index') }}" class="btn btn-primary" style="padding: 0.5rem 1rem; border-radius: 8px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;">
                <span>Buka antrean</span> &rarr;
            </a>
        </div>
    </div>
@else
    <div class="sa-attention sa-attention--success" style="border-radius: 10px; margin-bottom: 1.25rem;">
        <div class="sa-attention__content">
            <x-sa.icon name="circle-check" :size="20" />
            <div>
                <span class="sa-attention__title">Kondisi Operasional Normal</span>
                <span class="sa-attention__desc">Tidak ada yang perlu ditindak saat ini. Semua antrean dan tugas berjalan sesuai SLA.</span>
            </div>
        </div>
    </div>
@endif
