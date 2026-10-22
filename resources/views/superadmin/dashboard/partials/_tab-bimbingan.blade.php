{{-- 4 KARTU METRIK BIMBINGAN --}}
<div class="sa-grid sa-grid--metrics" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
    {{-- Metric 1: Total Tugas Aktif --}}
    <div class="sa-panel sa-metric sa-metric--primary" style="background: var(--cv-surface); border: 1px solid var(--cv-line); border-radius: 12px; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
        <div class="sa-metric__label" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--cv-ink-3); margin-bottom: 0.35rem;">Total Tugas Aktif</div>
        <div class="sa-metric__value" style="font-size: 2.25rem; font-weight: 800; font-family: monospace; color: var(--cv-blue-800); line-height: 1.1;">{{ number_format($totalTugasAktif) }}</div>
        <div class="sa-metric__hint" style="font-size: 0.75rem; color: var(--cv-ink-2); margin-top: 0.35rem;">Sedang & belum dikerjakan</div>
    </div>

    {{-- Metric 2: Logbook Pending Reviu --}}
    <div class="sa-panel sa-metric sa-metric--warning" style="background: var(--cv-surface); border: 1px solid var(--cv-line); border-left: 4px solid #F59E0B; border-radius: 12px; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
        <div class="sa-metric__label" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--cv-ink-3); margin-bottom: 0.35rem;">Logbook Pending Reviu</div>
        <div class="sa-metric__value" style="font-size: 2.25rem; font-weight: 800; font-family: monospace; color: #D97706; line-height: 1.1;">{{ number_format($totalLogbookPending) }}</div>
        <div class="sa-metric__hint" style="font-size: 0.75rem; color: var(--cv-ink-2); margin-top: 0.35rem;">Menunggu catatan pembimbing</div>
    </div>

    {{-- Metric 3: Penilaian Final Selesai --}}
    <div class="sa-panel sa-metric sa-metric--success" style="background: var(--cv-surface); border: 1px solid var(--cv-line); border-left: 4px solid #10B981; border-radius: 12px; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
        <div class="sa-metric__label" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--cv-ink-3); margin-bottom: 0.35rem;">Penilaian Final Selesai</div>
        <div class="sa-metric__value" style="font-size: 2.25rem; font-weight: 800; font-family: monospace; color: #059669; line-height: 1.1;">{{ number_format($totalPenilaianSelesai) }}</div>
        <div class="sa-metric__hint" style="font-size: 0.75rem; color: var(--cv-ink-2); margin-top: 0.35rem;">Lembar nilai siap diunduh</div>
    </div>

    {{-- Metric 4: Staf Pembimbing Aktif --}}
    <div class="sa-panel sa-metric sa-metric--neutral" style="background: var(--cv-surface); border: 1px solid var(--cv-line); border-radius: 12px; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
        <div class="sa-metric__label" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--cv-ink-3); margin-bottom: 0.35rem;">Staf Pembimbing Aktif</div>
        <div class="sa-metric__value" style="font-size: 2.25rem; font-weight: 800; font-family: monospace; color: var(--cv-ink); line-height: 1.1;">{{ number_format($totalPembimbing) }}</div>
        <div class="sa-metric__hint" style="font-size: 0.75rem; color: var(--cv-ink-2); margin-top: 0.35rem;">Tersedia di Diskominfo</div>
    </div>
</div>

{{-- BARIS KARTU PENGAWASAN KEDISIPLINAN TUGAS (PRD F-08 & BR-16) --}}
<div class="sa-grid sa-grid--2" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
    {{-- Tugas Terlambat (Overdue) --}}
    <div class="sa-panel sa-metric sa-metric--danger" style="background: var(--cv-surface); border: 1px solid {{ $totalTugasTerlambat > 0 ? '#FECACA' : 'var(--cv-line)' }}; border-left: 4px solid {{ $totalTugasTerlambat > 0 ? '#EF4444' : '#10B981' }}; border-radius: 12px; padding: 1.25rem;">
        <div class="sa-metric__header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
            <div>
                <span class="sa-metric__label" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--cv-ink-3);">Tugas Terlambat (Overdue)</span>
                <div class="sa-metric__value {{ $totalTugasTerlambat > 0 ? 'sa-color-danger' : '' }}" style="font-size: 1.85rem; font-weight: 800; font-family: monospace; color: {{ $totalTugasTerlambat > 0 ? '#DC2626' : 'var(--cv-ink)' }}; line-height: 1.2; margin-top: 2px;">
                    {{ $totalTugasTerlambat }} Tugas
                </div>
            </div>
            <div class="sa-metric__icon" style="color: {{ $totalTugasTerlambat > 0 ? '#EF4444' : '#10B981' }}; background: {{ $totalTugasTerlambat > 0 ? '#FEF2F2' : '#ECFDF5' }}; padding: 8px; border-radius: 8px;">
                <x-sa.icon name="alert-circle" :size="20" />
            </div>
        </div>
        <div class="sa-metric__hint {{ $totalTugasTerlambat > 0 ? 'sa-color-danger' : 'sa-color-success' }}" style="font-size: 0.8125rem; font-weight: 600; color: {{ $totalTugasTerlambat > 0 ? '#DC2626' : '#059669' }};">
            {{ $totalTugasTerlambat > 0 ? 'Melewati deadline tanpa status selesai' : 'Semua tugas berjalan tepat waktu' }}
        </div>
    </div>

    {{-- Mendekati Deadline (≤ 3 Hari) --}}
    <div class="sa-panel sa-metric sa-metric--warning" style="background: var(--cv-surface); border: 1px solid var(--cv-line); border-left: 4px solid #F59E0B; border-radius: 12px; padding: 1.25rem;">
        <div class="sa-metric__header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
            <div>
                <span class="sa-metric__label" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--cv-ink-3);">Mendekati Deadline (≤ 3 Hari)</span>
                <div class="sa-metric__value" style="font-size: 1.85rem; font-weight: 800; font-family: monospace; color: #D97706; line-height: 1.2; margin-top: 2px;">
                    {{ $totalTugasMendekatiDeadline }} Tugas
                </div>
            </div>
            <div class="sa-metric__icon" style="color: #F59E0B; background: #FFFBEB; padding: 8px; border-radius: 8px;">
                <x-sa.icon name="clock" :size="20" />
            </div>
        </div>
        <div class="sa-metric__hint" style="font-size: 0.8125rem; color: var(--cv-ink-2);">
            Tenggat mendekati sesuai aturan BR-16
        </div>
    </div>
</div>

{{-- MATRIKS BEBAN PEMBIMBING LAPANGAN (BR-13) --}}
<x-sa.panel title="Matriks Utilisasi Beban Pembimbing Lapangan (BR-13)" subtitle="Batas maksimal beban mentee per pembimbing lapangan: {{ $maksBeban ? $maksBeban . ' Orang' : 'Tanpa Batas' }}">
    <div class="sa-panel__extra-links" style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
        <a href="{{ route('admin.penempatan.index') }}" class="sa-link" style="font-weight: 600; color: var(--cv-blue-700); font-size: 0.8125rem;">Kelola Penempatan Peserta &rarr;</a>
        <span class="sa-link-separator" style="color: var(--cv-line-strong);">&bull;</span>
        <a href="{{ route('superadmin.monitoring.index') }}" class="sa-link" style="font-weight: 600; color: var(--cv-blue-700); font-size: 0.8125rem;">Monitoring Peserta &rarr;</a>
    </div>

    @if($pembimbingList->isEmpty())
        <x-sa.empty text="Belum ada staf pembimbing aktif yang terdaftar di basis data." icon="users" />
    @else
        <div class="sa-grid sa-grid--metrics" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem;">
            @foreach($pembimbingList as $p)
                @php
                    $menteeCount = $p->mentee_aktif_count;
                    $maks = $maksBeban ?: 10;
                    $persenBeban = min(100, round(($menteeCount / $maks) * 100));
                    $isOverload = $maksBeban && $menteeCount >= $maksBeban;
                    $initials = collect(explode(' ', $p->nama))->take(2)->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('');
                @endphp
                <div class="sa-panel sa-panel--sub" style="background: var(--cv-surface); border: 1px solid var(--cv-line); border-radius: 10px; padding: 1rem; position: relative;">
                    <div class="sa-pembimbing-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.35rem;">
                        <div style="display: flex; align-items: center; gap: 0.6rem;">
                            <span class="cv-avatar-sm" aria-hidden="true">{{ $initials ?: 'PB' }}</span>
                            <div>
                                <span class="sa-table-strong" style="display: block; line-height: 1.3;">{{ $p->nama }}</span>
                                <span class="sa-table-sub" style="font-size: 0.725rem;">{{ $p->email }}</span>
                            </div>
                        </div>
                        <span class="sa-badge {{ $isOverload ? 'sa-badge--danger' : 'sa-badge--info' }}" style="font-weight: 700; border-radius: 999px; padding: 2px 8px; font-size: 0.75rem;">
                            {{ $menteeCount }} Mentee
                        </span>
                    </div>
                    
                    <div class="sa-meter-group" style="margin: 0.75rem 0;">
                        <div class="sa-meter" role="meter" aria-valuenow="{{ $menteeCount }}" aria-valuemin="0" aria-valuemax="{{ $maks }}" aria-label="Beban Mentee {{ $p->nama }}" style="height: 6px; background: var(--cv-line); border-radius: 999px; overflow: hidden;">
                            <div class="sa-meter__fill {{ $isOverload ? 'sa-meter__fill--danger' : 'sa-meter__fill--primary' }}" data-v="{{ $persenBeban }}" style="width: {{ $persenBeban }}%; height: 100%; border-radius: 999px; background: {{ $isOverload ? '#EF4444' : 'var(--cv-blue-600)' }};"></div>
                        </div>
                    </div>

                    <div class="sa-pembimbing-footer" style="display: flex; justify-content: space-between; align-items: center; font-size: 0.725rem;">
                        <span class="{{ $isOverload ? 'sa-color-danger' : 'sa-color-success' }}" style="font-weight: 600; color: {{ $isOverload ? '#DC2626' : '#059669' }};">
                            {{ $isOverload ? 'Kapasitas Penuh' : 'Kapasitas Tersedia' }}
                        </span>
                        <span class="sa-table-sub" style="font-family: monospace;">Kapasitas: {{ $maksBeban ?: '∞' }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-sa.panel>

{{-- PELACAK PESERTA MENDEKATI BATAS SELESAI (OFF-BOARDING TRACKER <= 14 HARI) --}}
<x-sa.panel title="Pelacak Penyelesaian Magang Terdekat (Off-boarding Tracker)" subtitle="Peserta yang akan menyelesaikan masa dinas dalam 2 pekan ke depan untuk persiapan penilaian akhir dan Lembar Nilai" :href="route('superadmin.monitoring.index')" linkText="Lihat Semua Peserta Aktif &rarr;">
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
                            <td><span class="sa-badge sa-badge--info" style="font-size: 0.75rem; border-radius: 4px;">{{ $pen->bidang?->nama_bidang ?? 'Diskominfo' }}</span></td>
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
            <strong class="sa-policy-val" style="font-size: 0.875rem; color: var(--cv-ink);">Maks. {{ $batasHariLogbookMundur }} Hari (BR-09)</strong>
        </div>
        <div class="sa-policy-item" style="padding: 0.5rem 0;">
            <span class="sa-metric__label" style="font-size: 0.725rem; color: var(--cv-ink-3); text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 2px;">Masa Tenggang Lembar Nilai</span>
            <strong class="sa-policy-val" style="font-size: 0.875rem; color: var(--cv-ink);">{{ $masaTenggangUnduhNilaiHari }} Hari Pasca Nonaktif (BR-12)</strong>
        </div>
        <div class="sa-policy-item" style="padding: 0.5rem 0;">
            <span class="sa-metric__label" style="font-size: 0.725rem; color: var(--cv-ink-3); text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 2px;">Passing Grade Predikat</span>
            <strong class="sa-policy-val" style="font-size: 0.875rem; color: var(--cv-ink);">Minimal Nilai {{ $passingGradeNilai }} (BR-11)</strong>
        </div>
        <div class="sa-policy-item sa-policy-item--action" style="text-align: right;">
            <a href="{{ route('superadmin.pengaturan.index') }}" class="sa-link" style="font-weight: 700; color: var(--cv-blue-700); font-size: 0.8125rem;">
                Sesuaikan Parameter Sistem &rarr;
            </a>
        </div>
    </div>
</x-sa.panel>
