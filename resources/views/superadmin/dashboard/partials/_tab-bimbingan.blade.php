{{-- ==========================================================================
   Tab 3: Pelaksanaan & Bimbingan - SuperAdmin Master Cockpit
   Standar Visual Gentelella Theme (MagKom Sleman)
   ========================================================================== --}}
@php
    /** @var int $totalTugasAktif */
    /** @var int $totalLogbookPending */
    /** @var int $totalPenilaianSelesai */
    /** @var int $totalPembimbing */
    /** @var int $totalTugasTerlambat */
    /** @var int $totalTugasMendekatiDeadline */
    /** @var \Illuminate\Support\Collection<int, \App\Models\User> $pembimbingList */
    /** @var int|null $maksBeban */
    /** @var \Illuminate\Support\Collection<int, \App\Models\Penempatan> $pesertaMendekatiSelesai */
    /** @var int $batasHariLogbookMundur */
    /** @var int $masaTenggangUnduhNilaiHari */
    /** @var int $passingGradeNilai */
@endphp
<div class="gentelella-tab-pane gentelella-tab-bimbingan">
{{-- 4 KARTU METRIK BIMBINGAN --}}
<div class="sa-grid sa-grid--metrics" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
    {{-- Metric 1: Total Tugas Aktif --}}
    <div class="sa-panel sa-metric sa-metric--primary" style="background: var(--cv-surface); border: 1px solid var(--cv-line); border-radius: 12px; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
        <div class="sa-metric__label" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--cv-ink-3); margin-bottom: 0.35rem;">Total Tugas Aktif</div>
        <div class="sa-metric__value sa-stat-val-primary" style="font-size: 2.25rem; font-weight: 800; font-family: monospace; line-height: 1.1;">{{ number_format($totalTugasAktif) }}</div>
        <div class="sa-metric__hint" style="font-size: 0.75rem; color: var(--cv-ink-2); margin-top: 0.35rem;">Sedang & belum dikerjakan</div>
    </div>

    {{-- Metric 2: Logbook Pending Review --}}
    <div class="sa-panel sa-metric sa-metric--warning" style="background: var(--cv-surface); border: 1px solid var(--cv-line); border-radius: 12px; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
        <div class="sa-metric__label" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--cv-ink-3); margin-bottom: 0.35rem;">Logbook Pending Review</div>
        <div class="sa-metric__value sa-stat-val-warning" style="font-size: 2.25rem; font-weight: 800; font-family: monospace; line-height: 1.1;">{{ number_format($totalLogbookPending) }}</div>
        <div class="sa-metric__hint" style="font-size: 0.75rem; color: var(--cv-ink-2); margin-top: 0.35rem;">Menunggu catatan pembimbing</div>
    </div>

    {{-- Metric 3: Penilaian Final Selesai --}}
    <div class="sa-panel sa-metric sa-metric--success" style="background: var(--cv-surface); border: 1px solid var(--cv-line); border-radius: 12px; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
        <div class="sa-metric__label" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--cv-ink-3); margin-bottom: 0.35rem;">Penilaian Final Selesai</div>
        <div class="sa-metric__value sa-stat-val-success" style="font-size: 2.25rem; font-weight: 800; font-family: monospace; line-height: 1.1;">{{ number_format($totalPenilaianSelesai) }}</div>
        <div class="sa-metric__hint" style="font-size: 0.75rem; color: var(--cv-ink-2); margin-top: 0.35rem;">Lembar nilai siap diunduh</div>
    </div>

    {{-- Metric 4: Staf Pembimbing Aktif --}}
    <div class="sa-panel sa-metric sa-metric--neutral" style="background: var(--cv-surface); border: 1px solid var(--cv-line); border-radius: 12px; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
        <div class="sa-metric__label" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--cv-ink-3); margin-bottom: 0.35rem;">Staf Pembimbing Aktif</div>
        <div class="sa-metric__value sa-stat-val-neutral" style="font-size: 2.25rem; font-weight: 800; font-family: monospace; line-height: 1.1;">{{ number_format($totalPembimbing) }}</div>
        <div class="sa-metric__hint" style="font-size: 0.75rem; color: var(--cv-ink-2); margin-top: 0.35rem;">Tersedia di Diskominfo</div>
    </div>
</div>

{{-- BARIS KARTU PENGAWASAN KEDISIPLINAN TUGAS (PRD F-08 & BR-16) --}}
<div class="sa-grid sa-grid--2" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.25rem;">
    {{-- Tugas Terlambat (Overdue) --}}
    <div class="sa-panel sa-metric sa-metric--danger sa-task-overdue-card" style="border: 1px solid {{ $totalTugasTerlambat > 0 ? '#FECACA' : 'var(--cv-line)' }}; border-radius: 12px; padding: 1.25rem;">
        <div class="sa-metric__header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
            <div>
                <span class="sa-metric__label" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--cv-ink-3);">Tugas Terlambat (Overdue)</span>
                <div class="sa-metric__value sa-task-overdue-val {{ $totalTugasTerlambat > 0 ? 'sa-color-danger' : '' }}" style="font-size: 1.85rem; font-weight: 800; font-family: monospace; line-height: 1.2; margin-top: 2px;">
                    {{ $totalTugasTerlambat }} Tugas
                </div>
            </div>
            <div class="sa-metric__icon sa-task-overdue-icon {{ $totalTugasTerlambat > 0 ? 'is-danger' : 'is-success' }}" style="padding: 8px; border-radius: 8px;">
                <x-sa.icon name="alert-circle" :size="20" />
            </div>
        </div>
        <div class="sa-metric__hint sa-task-overdue-hint {{ $totalTugasTerlambat > 0 ? 'sa-color-danger' : 'sa-color-success' }}" style="font-size: 0.8125rem; font-weight: 600;">
            {{ $totalTugasTerlambat > 0 ? 'Melewati deadline tanpa status selesai' : 'Semua tugas berjalan tepat waktu' }}
        </div>
    </div>

    {{-- Mendekati Deadline (≤ 3 Hari) --}}
    <div class="sa-panel sa-metric sa-metric--warning sa-task-deadline-card" style="border: 1px solid var(--cv-line); border-radius: 12px; padding: 1.25rem;">
        <div class="sa-metric__header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
            <div>
                <span class="sa-metric__label" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--cv-ink-3);">Mendekati Deadline (≤ 3 Hari)</span>
                <div class="sa-metric__value sa-task-deadline-val" style="font-size: 1.85rem; font-weight: 800; font-family: monospace; line-height: 1.2; margin-top: 2px;">
                    {{ $totalTugasMendekatiDeadline }} Tugas
                </div>
            </div>
            <div class="sa-metric__icon sa-task-deadline-icon" style="padding: 8px; border-radius: 8px;">
                <x-sa.icon name="clock" :size="20" />
            </div>
        </div>
        <div class="sa-metric__hint" style="font-size: 0.8125rem; color: var(--cv-ink-2);">
            Tenggat mendekati batas akhir
        </div>
    </div>
</div>

{{-- MATRIKS BEBAN PEMBIMBING LAPANGAN (BR-13) --}}
<x-sa.panel title="Matriks Utilisasi Beban Pembimbing Lapangan">
    {{-- Header Actions & Summary Ribbon --}}
    <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 1.25rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
            {{-- Quick Summary Badges --}}
            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                <span class="badge" style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.35rem 0.65rem; border-radius: 8px; background: var(--blue-tint); color: var(--blue-700); font-weight: 600; font-size: 0.75rem; border: 1px solid rgba(1, 52, 124, 0.15);">
                    <x-sa.icon name="users" :size="14" />
                    <span>{{ $pembimbingList->count() }} Pembimbing Aktif</span>
                </span>
                <span class="badge" style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.35rem 0.65rem; border-radius: 8px; background: rgba(16, 185, 129, 0.1); color: #059669; font-weight: 600; font-size: 0.75rem; border: 1px solid rgba(16, 185, 129, 0.2);">
                    <x-sa.icon name="user-check" :size="14" />
                    <span>{{ $pembimbingList->sum('mentee_aktif_count') }} Total Mentee</span>
                </span>
                <span class="badge" style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.35rem 0.65rem; border-radius: 8px; background: var(--cv-paper); color: var(--cv-ink-2); font-weight: 600; font-size: 0.75rem; border: 1px solid var(--cv-line);">
                    <x-sa.icon name="sliders" :size="14" />
                    <span>Maks. {{ $maksBeban ?: 10 }} Mentee/Staf</span>
                </span>
            </div>

            {{-- Quick Navigation Actions --}}
            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                <a href="{{ route('admin.penempatan.index') }}" class="sa-link">
                    <x-sa.icon name="user-plus" :size="14" />
                    <span>Kelola Penempatan Peserta &rarr;</span>
                </a>
                <a href="{{ route('superadmin.monitoring.index') }}" class="sa-link">
                    <x-sa.icon name="activity" :size="14" />
                    <span>Monitoring Peserta &rarr;</span>
                </a>
            </div>
        </div>
    </div>

    @if($pembimbingList->isEmpty())
        <x-sa.empty text="Belum ada staf pembimbing aktif yang terdaftar di basis data." icon="users" />
    @else
        <div class="sa-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.25rem;">
            @foreach($pembimbingList as $p)
                @php
                    $menteeCount = $p->mentee_aktif_count;
                    $maks = $maksBeban ?: 10;
                    $persenBeban = min(100, round(($menteeCount / $maks) * 100));
                    $isOverload = $maksBeban && $menteeCount >= $maksBeban;
                    $isNearFull = !$isOverload && $persenBeban >= 70;
                    $sisaKapasitas = max(0, $maks - $menteeCount);
                    $initials = collect(explode(' ', $p->nama))->take(2)->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('');
                    
                    if ($isOverload) {
                        $statusText = 'Kapasitas Penuh';
                        $statusBg = 'rgba(239, 68, 68, 0.1)';
                        $statusColor = '#EF4444';
                        $statusBorder = 'rgba(239, 68, 68, 0.25)';
                        $barColor = 'linear-gradient(90deg, #F87171, #EF4444)';
                    } elseif ($isNearFull) {
                        $statusText = 'Hampir Penuh';
                        $statusBg = 'rgba(245, 158, 11, 0.1)';
                        $statusColor = '#D97706';
                        $statusBorder = 'rgba(245, 158, 11, 0.25)';
                        $barColor = 'linear-gradient(90deg, #FBBF24, #F59E0B)';
                    } else {
                        $statusText = 'Kapasitas Tersedia';
                        $statusBg = 'rgba(16, 185, 129, 0.1)';
                        $statusColor = '#059669';
                        $statusBorder = 'rgba(16, 185, 129, 0.25)';
                        $barColor = 'linear-gradient(90deg, #38BDF8, #01347C)';
                    }
                @endphp
                <div class="sa-pembimbing-card" style="background: var(--cv-surface); border: 1px solid var(--cv-line); border-radius: 12px; padding: 1.25rem; display: flex; flex-direction: column; justify-content: space-between; gap: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.03); transition: transform 0.2s ease, box-shadow 0.2s ease;">
                    {{-- Top Section: Avatar & Info --}}
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.75rem;">
                            <div style="display: flex; align-items: center; gap: 0.75rem; min-width: 0;">
                                <div style="width: 44px; height: 44px; min-width: 44px; border-radius: 10px; background: linear-gradient(135deg, #01347C 0%, #0641CC 100%); color: #FFFFFF; font-weight: 800; font-size: 0.875rem; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(1, 52, 124, 0.22); position: relative; flex-shrink: 0;">
                                    {{ $initials ?: 'PL' }}
                                    <span style="position: absolute; bottom: -2px; right: -2px; width: 10px; height: 10px; border-radius: 999px; background: {{ $statusColor }}; border: 2px solid var(--cv-surface);"></span>
                                </div>
                                <div style="min-width: 0;">
                                    <h4 class="sa-table-strong" style="margin: 0; font-size: 0.9375rem; font-weight: 700; color: var(--cv-ink); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $p->nama }}
                                    </h4>
                                    <span class="sa-table-sub" style="display: flex; align-items: center; gap: 0.35rem; font-size: 0.75rem; color: var(--cv-ink-3); margin-top: 2px;">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0;"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                        <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $p->email }}</span>
                                    </span>
                                </div>
                            </div>
                            
                            {{-- Mentee Pill --}}
                            <div style="flex-shrink: 0;">
                                <span class="sa-badge-mentee" style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.25rem 0.6rem; border-radius: 999px; background: var(--blue-tint); color: var(--blue-700); font-weight: 800; font-size: 0.75rem; font-family: monospace; border: 1px solid rgba(1, 52, 124, 0.15);">
                                    {{ $menteeCount }} Mentee
                                </span>
                            </div>
                        </div>

                        {{-- Meter & Progress --}}
                        <div style="margin-top: 1rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; margin-bottom: 0.35rem;">
                                <span style="font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; color: var(--cv-ink-3); font-size: 0.7rem;">Utilisasi Beban</span>
                                <span style="font-weight: 700; font-family: monospace; color: var(--cv-ink);">{{ $persenBeban }}%</span>
                            </div>
                            <div style="height: 7px; background: var(--cv-line); border-radius: 999px; overflow: hidden;" role="meter" aria-valuenow="{{ $menteeCount }}" aria-valuemin="0" aria-valuemax="{{ $maks }}" aria-label="Beban Mentee {{ $p->nama }}">
                                <div style="width: {{ $persenBeban }}%; height: 100%; border-radius: 999px; background: {{ $barColor }}; transition: width 0.3s ease;"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Bottom Footer: Status & Capacity Info --}}
                    <div style="border-top: 1px dashed var(--cv-line); padding-top: 0.75rem; display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem;">
                        <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.2rem 0.5rem; border-radius: 6px; font-weight: 700; font-size: 0.725rem; background: {{ $statusBg }}; color: {{ $statusColor }}; border: 1px solid {{ $statusBorder }};">
                            <span style="width: 6px; height: 6px; border-radius: 999px; background: {{ $statusColor }};"></span>
                            <span>{{ $statusText }}</span>
                        </span>
                        
                        <div style="text-align: right; color: var(--cv-ink-2); font-size: 0.725rem;">
                            <span style="font-weight: 600;">Kapasitas:</span>
                            <strong style="color: var(--cv-ink); font-family: monospace; font-size: 0.8125rem;">{{ $maksBeban ?: '∞' }}</strong>
                            <span style="color: var(--cv-ink-3); font-size: 0.7rem;">(Sisa: {{ $sisaKapasitas }})</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-sa.panel>

{{-- PELACAK PESERTA MENDEKATI BATAS SELESAI (OFF-BOARDING TRACKER <= 14 HARI) --}}
<x-sa.panel title="Pelacak Penyelesaian Magang Terdekat<span class='sr-only'> Pelacak Penyelesaian Magang Terdekat (Off-boarding Tracker)</span>" :href="route('superadmin.monitoring.index')" linkText="Lihat Semua Peserta Aktif &rarr;">
    @if($pesertaMendekatiSelesai->isEmpty())
        <x-sa.empty text="Tidak ada peserta yang mendekati akhir masa magang dalam 14 hari ke depan." icon="calendar" />
    @else
        <div class="sa-table-scroll">
            <table class="sa-table cv-table-modern">
                <caption class="sa-sr-only">Tabel Pelacak Penyelesaian Magang Terdekat</caption>
                <thead>
                    <tr>
                        <th scope="col">Nama Peserta</th>
                        <th scope="col">Bidang Penempatan</th>
                        <th scope="col">Pembimbing</th>
                        <th scope="col">Tanggal Selesai</th>
                        <th scope="col" class="sa-text-right">Sisa Hari</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pesertaMendekatiSelesai as $pen)
                        @php
                            $sisaHari = (int) now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($pen->tgl_selesai)->startOfDay(), false);
                            $pName = $pen->peserta?->nama ?? 'Peserta';
                            $initials = collect(explode(' ', $pName))->take(2)->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('');
                        @endphp
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.6rem;">
                                    <span class="cv-avatar-sm" aria-hidden="true">{{ $initials ?: 'PS' }}</span>
                                    <strong class="sa-table-strong">{{ $pName }}</strong>
                                </div>
                            </td>
                            <td><span class="sa-badge sa-badge--info sa-badge-bidang" style="font-size: 0.75rem; border-radius: 4px;">{{ $pen->bidang?->nama_bidang ?? 'Diskominfo' }}</span></td>
                            <td style="color: var(--cv-ink-2);">{{ $pen->pembimbing?->nama ?? '-' }}</td>
                            <td style="font-family: monospace; font-size: 0.8125rem;">{{ \Carbon\Carbon::parse($pen->tgl_selesai)->format('d M Y') }}</td>
                            <td class="sa-text-right">
                                <span class="sa-badge {{ $sisaHari <= 7 ? 'sa-badge--danger' : 'sa-badge--warning' }}" style="font-weight: 700; border-radius: 999px; padding: 2px 10px;">
                                    {{ $sisaHari > 0 ? $sisaHari . ' Hari Lagi' : 'Hari Ini' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-sa.panel>

{{-- RINGKASAN KEBIJAKAN MAGKOM --}}
<x-sa.panel>
    <div class="sa-policy-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: center;">
        <div class="sa-policy-item" style="padding: 0.5rem 0;">
            <span class="sa-metric__label" style="font-size: 0.725rem; color: var(--cv-ink-3); text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 2px;">Toleransi Logbook Mundur</span>
            <strong class="sa-policy-val" style="font-size: 0.875rem; color: var(--cv-ink);">Maks. {{ $batasHariLogbookMundur }} Hari</strong>
        </div>
        <div class="sa-policy-item" style="padding: 0.5rem 0;">
            <span class="sa-metric__label" style="font-size: 0.725rem; color: var(--cv-ink-3); text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 2px;">Masa Tenggang Lembar Nilai</span>
            <strong class="sa-policy-val" style="font-size: 0.875rem; color: var(--cv-ink);">{{ $masaTenggangUnduhNilaiHari }} Hari Pasca Nonaktif</strong>
        </div>
        <div class="sa-policy-item" style="padding: 0.5rem 0;">
            <span class="sa-metric__label" style="font-size: 0.725rem; color: var(--cv-ink-3); text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 2px;">Passing Grade Predikat</span>
            <strong class="sa-policy-val" style="font-size: 0.875rem; color: var(--cv-ink);">Minimal Nilai {{ $passingGradeNilai }}</strong>
        </div>
        <div class="sa-policy-item sa-policy-item--action" style="text-align: right;">
            <a href="{{ route('superadmin.pengaturan.index') }}" class="sa-link">
                Sesuaikan Parameter Sistem &rarr;
            </a>
        </div>
    </div>
</x-sa.panel>
</div>
