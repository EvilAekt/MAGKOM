{{-- PUSAT METRIK KEBERHASILAN PRODUK & KPI DINAS (PRD Bab 2.3) --}}
<x-gentelella.x-panel 
    title="Pusat Metrik Keberhasilan Produk & KPI Dinas" 
    badge="PRD Bab 2.3" 
    badgeType="info"
    subtitle="Target mutu layanan dan kepatuhan operasional Diskominfo Sleman yang diawasi SuperAdmin"
>
    <x-slot:actions>
        <span class="sa-badge sa-badge--success" style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.2rem 0.5rem; font-size: 0.75rem;">
            <span class="status-dot-pulse"></span>
            <span>Audit Otomatis Aktif</span>
        </span>
    </x-slot:actions>

    <div class="sa-grid sa-grid--metrics">
        {{-- KPI 1: Integritas Kuota --}}
        <div class="sa-panel sa-metric {{ $anomaliKuotaCount > 0 ? 'sa-metric--danger' : 'sa-metric--success' }}">
            <div class="sa-metric__label">Integritas Kuota (Target 0)</div>
            <div class="sa-metric__value">{{ $anomaliKuotaCount }} Pelanggaran</div>
            <div class="sa-metric__hint">
                {{ $anomaliKuotaCount === 0 ? '100% Bebas Kuota Minus / Bocor' : 'Terdeteksi kuota melebihi batas!' }}
            </div>
        </div>

        {{-- KPI 2: SLA Kecepatan Verifikasi --}}
        <div class="sa-panel sa-metric {{ $antreanMelebihiSla > 0 ? 'sa-metric--warning' : 'sa-metric--primary' }}">
            <div class="sa-metric__label">SLA Verifikasi (Target ≤ 5 Hari)</div>
            <div class="sa-metric__value">
                {{ $rataRataSlaVerifikasiHari !== null ? $rataRataSlaVerifikasiHari . ' Hari' : '- Hari' }}
            </div>
            <div class="sa-metric__hint">
                @if($antreanMelebihiSla > 0)
                    {{ $antreanMelebihiSla }} Berkas Melewati Batas 5 Hari
                @else
                    Kecepatan verifikasi sesuai target SLA
                @endif
            </div>
        </div>

        {{-- KPI 3: Kelulusan Lembar Nilai --}}
        <div class="sa-panel sa-metric sa-metric--primary">
            <div class="sa-metric__label">Lembar Nilai Alumni (Target 100%)</div>
            <div class="sa-metric__value">{{ $kepatuhanNilaiSelesaiPersen }}%</div>
            <div class="sa-metric__hint">
                {{ $penempatanSelesaiDenganNilai }} dari {{ $penempatanSelesaiTotal }} Alumni Selesai Nilai Final
            </div>
        </div>

        {{-- KPI 4: Keterlacakan Audit Trail --}}
        <div class="sa-panel sa-metric sa-metric--success">
            <div class="sa-metric__label">Audit Trail (Target 100%)</div>
            <div class="sa-metric__value">100% Tercatat</div>
            <div class="sa-metric__hint">
                {{ number_format($securityPosture['total_audit_events'] ?? 0) }} Event Terekam & Tersanitasi
            </div>
        </div>
    </div>
</x-gentelella.x-panel>

{{-- BARIS 2: TREN PENDAFTARAN & DEMOGRAFI --}}
<div class="row sa-grid sa-grid--main-aside">
    {{-- Tren Pendaftar 12 Bulan (2/3 lebar) --}}
    <div class="col-md-8 col-sm-12 col-xs-12">
        <x-gentelella.x-panel title="Tren Pendaftaran 12 Bulan Terakhir" subtitle="Volume pendaftar magang dari seluruh institusi">
            @php
                $maxVal = 1;
                foreach($trenPendaftar as $t) {
                    if($t['count'] > $maxVal) { $maxVal = $t['count']; }
                }
            @endphp

            <div class="sa-trend-chart" role="region" aria-label="Grafik volume pendaftar 12 bulan terakhir">
                @foreach($trenPendaftar as $tp)
                    @php
                        $barPct = round(($tp['count'] / $maxVal) * 100);
                    @endphp
                    <div class="sa-trend-col" title="{{ $tp['bulan'] }}: {{ $tp['count'] }} pendaftar">
                        <span class="sa-trend-val">{{ $tp['count'] > 0 ? $tp['count'] : '' }}</span>
                        <div class="sa-trend-bar" data-v="{{ $barPct }}"></div>
                        <span class="sa-trend-label">{{ $tp['bulan'] }}</span>
                    </div>
                @endforeach
            </div>

            {{-- Tabel ringkas aksesibel tersembunyi untuk screen reader --}}
            <div class="sa-sr-only">
                <table>
                    <caption class="sa-sr-only">Tabel Data Tren Pendaftaran 12 Bulan</caption>
                    <thead>
                        <tr><th scope="col">Bulan</th><th scope="col">Jumlah Pendaftar</th></tr>
                    </thead>
                    <tbody>
                        @foreach($trenPendaftar as $tp)
                            <tr><td>{{ $tp['bulan'] }}</td><td>{{ $tp['count'] }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-gentelella.x-panel>
    </div>

    {{-- Komposisi Jalur & Jenjang (1/3 lebar) --}}
    <div class="col-md-4 col-sm-12 col-xs-12">
        <x-gentelella.x-panel title="Demografi Asal Institusi & Jenjang Pendidikan (PRD 4.3)" subtitle="Sebaran calon peserta berdasarkan jenjang">
            <x-slot:actions>
                <a href="{{ route('admin.laporan.pendaftaran') }}" class="sa-link" style="font-size: 0.75rem;">Lihat Laporan Lengkap Demografi &rarr;</a>
            </x-slot:actions>

            <div class="sa-grid sa-grid--3">
                <div class="sa-panel sa-panel--sub">
                    <span class="sa-metric__label">SMK / MAK</span>
                    <div class="sa-metric__value">{{ $jenjangRaw['smk'] ?? 0 }}</div>
                </div>
                <div class="sa-panel sa-panel--sub">
                    <span class="sa-metric__label">Diploma 3 (D3)</span>
                    <div class="sa-metric__value">{{ $jenjangRaw['d3'] ?? 0 }}</div>
                </div>
                <div class="sa-panel sa-panel--sub">
                    <span class="sa-metric__label">Sarjana S1 / D4</span>
                    <div class="sa-metric__value">{{ $jenjangRaw['s1'] ?? 0 }}</div>
                </div>
            </div>

            <div class="sa-institusi-list" style="margin-top: 1rem;">
                <span class="sa-shortcuts__label">Top 5 Institusi:</span>
                <div class="sa-badges-wrap" style="display: flex; flex-wrap: wrap; gap: 0.35rem; margin-top: 0.35rem;">
                    @forelse($topInstitusi->take(5) as $inst)
                        <span class="sa-badge sa-badge--neutral">
                            <strong>{{ $inst->institusi }}</strong> ({{ $inst->total }})
                        </span>
                    @empty
                        <span class="sa-empty__text">Belum ada profil institusi.</span>
                    @endforelse
                </div>
            </div>
        </x-gentelella.x-panel>
    </div>
</div>

{{-- BARIS 3: DUA KOLOM PENGGUNA TERBARU & AUDIT LOG TERBARU --}}
<div class="row sa-grid sa-grid--2">
    {{-- Kolom Kiri: Pengguna terbaru --}}
    <div class="col-md-6 col-sm-6 col-xs-12">
        <x-gentelella.x-panel title="Pengguna terbaru" subtitle="Pendaftaran akun dan staf internal terkini">
            <x-slot:actions>
                <a href="{{ route('superadmin.staf.index') }}" class="btn-primary" style="padding: 0.25rem 0.6rem; font-size: 0.75rem; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem;">
                    <span>Kelola pengguna</span> &rarr;
                </a>
            </x-slot:actions>

            @if($penggunaTerbaru->isEmpty())
                <x-sa.empty text="Belum ada akun pengguna yang terdaftar." icon="users" />
            @else
                <div class="sa-list">
                    @foreach($penggunaTerbaru as $user)
                        @php
                            $roleVal = $user->role?->value ?? 'peserta';
                            $roleTone = match($roleVal) {
                                'superadmin' => 'danger',
                                'admin' => 'info',
                                'pembimbing' => 'warning',
                                default => 'success',
                            };
                            $roleLabel = match($roleVal) {
                                'superadmin' => 'SuperAdmin',
                                'admin' => 'Administrasi',
                                'pembimbing' => 'Pembimbing',
                                default => 'Peserta',
                            };
                            $initials = strtoupper(substr($user->nama ?: 'U', 0, 1));
                        @endphp
                        <div class="sa-list-item">
                            <div class="sa-list-item__left">
                                <div class="sa-avatar">
                                    {{ $initials }}
                                </div>
                                <div class="sa-list-item__text">
                                    <span class="sa-list-item__title">{{ $user->nama }}</span>
                                    <span class="sa-list-item__sub">{{ $user->email }}</span>
                                </div>
                            </div>
                            <span class="sa-badge sa-badge--{{ $roleTone }}">
                                {{ $roleLabel }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-gentelella.x-panel>
    </div>

    {{-- Kolom Kanan: Audit log terbaru --}}
    <div class="col-md-6 col-sm-6 col-xs-12">
        <x-gentelella.x-panel title="Audit log terbaru" subtitle="Rekam jejak mutasi data dan aksi terotorisasi">
            <x-slot:actions>
                <a href="{{ route('superadmin.audit-log.index') }}" class="btn-secondary" style="padding: 0.25rem 0.6rem; font-size: 0.75rem; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem;">
                    <span>Lihat semua</span> &rarr;
                </a>
            </x-slot:actions>

            @if($auditTerbaru->isEmpty())
                <x-sa.empty text="Belum ada catatan mutasi audit log." icon="file-text" />
            @else
                <div class="sa-list">
                    @foreach($auditTerbaru as $log)
                        @php
                            $actorName = $log->user?->nama ?? 'Sistem';
                            $timeStr = $log->waktu ? $log->waktu->format('H:i') : '-';
                            $dateStr = $log->waktu ? $log->waktu->format('d M') : '';
                            $actionText = $log->aksi ?: ($log->detail['aksi'] ?? ($log->entitas ? 'Mutasi ' . $log->entitas : 'Aktivitas sistem'));
                        @endphp
                        <div class="sa-list-item">
                            <div class="sa-list-item__icon">
                                <x-sa.icon name="check" :size="16" />
                            </div>
                            <div class="sa-list-item__text">
                                <span class="sa-list-item__title">{{ $actionText }}</span>
                                <div class="sa-list-item__meta">
                                    <strong>{{ $actorName }}</strong> &bull; {{ $dateStr }} {{ $timeStr }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-gentelella.x-panel>
    </div>
</div>
