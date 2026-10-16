{{-- 4 KARTU METRIK BIMBINGAN --}}
<div class="sa-grid sa-grid--metrics">
    <div class="sa-panel sa-metric sa-metric--primary">
        <div class="sa-metric__label">Total Tugas Aktif</div>
        <div class="sa-metric__value">{{ number_format($totalTugasAktif) }}</div>
        <div class="sa-metric__hint">Sedang & belum dikerjakan</div>
    </div>

    <div class="sa-panel sa-metric sa-metric--warning">
        <div class="sa-metric__label">Logbook Pending Reviu</div>
        <div class="sa-metric__value">{{ number_format($totalLogbookPending) }}</div>
        <div class="sa-metric__hint">Menunggu catatan pembimbing</div>
    </div>

    <div class="sa-panel sa-metric sa-metric--success">
        <div class="sa-metric__label">Penilaian Final Selesai</div>
        <div class="sa-metric__value">{{ number_format($totalPenilaianSelesai) }}</div>
        <div class="sa-metric__hint">Lembar nilai siap diunduh</div>
    </div>

    <div class="sa-panel sa-metric sa-metric--neutral">
        <div class="sa-metric__label">Staf Pembimbing Aktif</div>
        <div class="sa-metric__value">{{ number_format($totalPembimbing) }}</div>
        <div class="sa-metric__hint">Tersedia di Diskominfo</div>
    </div>
</div>

{{-- BARIS KARTU PENGAWASAN KEDISIPLINAN TUGAS (PRD F-08 & BR-16) --}}
<div class="sa-grid sa-grid--2">
    {{-- Tugas Terlambat (Overdue) --}}
    <div class="sa-panel sa-metric sa-metric--danger">
        <div class="sa-metric__header">
            <div>
                <span class="sa-metric__label">Tugas Terlambat (Overdue)</span>
                <div class="sa-metric__value {{ $totalTugasTerlambat > 0 ? 'sa-color-danger' : '' }}">
                    {{ $totalTugasTerlambat }} Tugas
                </div>
            </div>
            <div class="sa-metric__icon">
                <x-sa.icon name="alert-circle" :size="20" />
            </div>
        </div>
        <div class="sa-metric__hint {{ $totalTugasTerlambat > 0 ? 'sa-color-danger' : 'sa-color-success' }}">
            {{ $totalTugasTerlambat > 0 ? 'Melewati deadline tanpa status selesai' : 'Semua tugas berjalan tepat waktu' }}
        </div>
    </div>

    {{-- Mendekati Deadline (≤ 3 Hari) --}}
    <div class="sa-panel sa-metric sa-metric--warning">
        <div class="sa-metric__header">
            <div>
                <span class="sa-metric__label">Mendekati Deadline (≤ 3 Hari)</span>
                <div class="sa-metric__value">
                    {{ $totalTugasMendekatiDeadline }} Tugas
                </div>
            </div>
            <div class="sa-metric__icon">
                <x-sa.icon name="clock" :size="20" />
            </div>
        </div>
        <div class="sa-metric__hint">
            Tenggat mendekati sesuai aturan BR-16
        </div>
    </div>
</div>

{{-- MATRIKS BEBAN PEMBIMBING LAPANGAN (BR-13) --}}
<x-sa.panel title="Matriks Utilisasi Beban Pembimbing Lapangan (BR-13)" subtitle="Batas maksimal beban mentee per pembimbing lapangan: {{ $maksBeban ? $maksBeban . ' Orang' : 'Tanpa Batas' }}">
    <div class="sa-panel__extra-links">
        <a href="{{ route('admin.penempatan.index') }}" class="sa-link">Kelola Penempatan Peserta &rarr;</a>
        <span class="sa-link-separator">&bull;</span>
        <a href="{{ route('superadmin.monitoring.index') }}" class="sa-link">Monitoring Peserta &rarr;</a>
    </div>

    @if($pembimbingList->isEmpty())
        <x-sa.empty text="Belum ada staf pembimbing aktif yang terdaftar di basis data." icon="users" />
    @else
        <div class="sa-grid sa-grid--metrics">
            @foreach($pembimbingList as $p)
                @php
                    $menteeCount = $p->mentee_aktif_count;
                    $maks = $maksBeban ?: 10;
                    $persenBeban = min(100, round(($menteeCount / $maks) * 100));
                    $isOverload = $maksBeban && $menteeCount >= $maksBeban;
                @endphp
                <div class="sa-panel sa-panel--sub">
                    <div class="sa-pembimbing-header">
                        <span class="sa-table-strong">{{ $p->nama }}</span>
                        <span class="sa-badge {{ $isOverload ? 'sa-badge--danger' : 'sa-badge--info' }}">
                            {{ $menteeCount }} Mentee
                        </span>
                    </div>
                    <span class="sa-table-sub">{{ $p->email }}</span>
                    
                    <div class="sa-meter-group">
                        <div class="sa-meter" role="meter" aria-valuenow="{{ $menteeCount }}" aria-valuemin="0" aria-valuemax="{{ $maks }}" aria-label="Beban Mentee {{ $p->nama }}">
                            <div class="sa-meter__fill {{ $isOverload ? 'sa-meter__fill--danger' : 'sa-meter__fill--primary' }}" data-v="{{ $persenBeban }}"></div>
                        </div>
                    </div>

                    <div class="sa-pembimbing-footer">
                        <span class="{{ $isOverload ? 'sa-color-danger' : 'sa-color-success' }}">
                            {{ $isOverload ? 'Kapasitas Penuh' : 'Kapasitas Tersedia' }}
                        </span>
                        <span class="sa-table-sub">Kapasitas: {{ $maksBeban ?: '∞' }}</span>
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
            <table class="sa-table">
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
                        @endphp
                        <tr>
                            <td><strong class="sa-table-strong">{{ $pen->peserta?->nama ?? 'Peserta' }}</strong></td>
                            <td>{{ $pen->bidang?->nama_bidang ?? 'Diskominfo' }}</td>
                            <td>{{ $pen->pembimbing?->nama ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($pen->tgl_selesai)->format('d M Y') }}</td>
                            <td class="sa-text-right">
                                <span class="sa-badge {{ $sisaHari <= 7 ? 'sa-badge--danger' : 'sa-badge--warning' }}">
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
    <div class="sa-policy-grid">
        <div class="sa-policy-item">
            <span class="sa-metric__label">Toleransi Logbook Mundur</span>
            <strong class="sa-policy-val">Maks. {{ $batasHariLogbookMundur }} Hari (BR-09)</strong>
        </div>
        <div class="sa-policy-item">
            <span class="sa-metric__label">Masa Tenggang Lembar Nilai</span>
            <strong class="sa-policy-val">{{ $masaTenggangUnduhNilaiHari }} Hari Pasca Nonaktif (BR-12)</strong>
        </div>
        <div class="sa-policy-item">
            <span class="sa-metric__label">Passing Grade Predikat</span>
            <strong class="sa-policy-val">Minimal Nilai {{ $passingGradeNilai }} (BR-11)</strong>
        </div>
        <div class="sa-policy-item sa-policy-item--action">
            <a href="{{ route('superadmin.pengaturan.index') }}" class="sa-link">
                Sesuaikan Parameter Sistem &rarr;
            </a>
        </div>
    </div>
</x-sa.panel>
