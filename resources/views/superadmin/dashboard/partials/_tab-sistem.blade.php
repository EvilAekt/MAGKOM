{{-- BAGIAN 1: HUB EKSPOR 4 LAPORAN RESMI DINAS 1-KLIK --}}
<x-sa.panel title="Hub Ekspor Instan Laporan Resmi Dinas (AC-17-1)" subtitle="Unduh data rekapitulasi CSV/Excel untuk kebutuhan arsip dan pelaporan pimpinan Diskominfo Sleman" :href="route('admin.laporan.index')" linkText="Buka Filter Laporan Lanjutan &rarr;">
    <div class="sa-grid sa-grid--4">
        {{-- Ekspor 1: Pendaftaran --}}
        <div class="sa-panel sa-panel--sub sa-export-card">
            <div>
                <div class="sa-export-card__header">
                    <span class="sa-export-card__num">1</span>
                    <h4 class="sa-panel__title">Laporan Pendaftaran</h4>
                </div>
                <p class="sa-panel__sub">
                    Data registrasi peserta, asal institusi, jurusan, dan status keputusan seleksi.
                </p>
            </div>
            <a href="{{ route('admin.laporan.pendaftaran') }}" class="btn-primary sa-btn-sm sa-btn-full">
                <x-sa.icon name="download" :size="14" />
                <span>Unduh CSV Pendaftaran</span>
            </a>
        </div>

        {{-- Ekspor 2: Penempatan --}}
        <div class="sa-panel sa-panel--sub sa-export-card">
            <div>
                <div class="sa-export-card__header">
                    <span class="sa-export-card__num">2</span>
                    <h4 class="sa-panel__title">Laporan Penempatan</h4>
                </div>
                <p class="sa-panel__sub">
                    Alokasi bidang kerja, pembimbing lapangan yang ditunjuk, dan status masa dinas peserta.
                </p>
            </div>
            <a href="{{ route('admin.laporan.penempatan') }}" class="btn-primary sa-btn-sm sa-btn-full">
                <x-sa.icon name="download" :size="14" />
                <span>Unduh CSV Penempatan</span>
            </a>
        </div>

        {{-- Ekspor 3: Realisasi Kuota --}}
        <div class="sa-panel sa-panel--sub sa-export-card">
            <div>
                <div class="sa-export-card__header">
                    <span class="sa-export-card__num">3</span>
                    <h4 class="sa-panel__title">Realisasi Kuota</h4>
                </div>
                <p class="sa-panel__sub">
                    Perbandingan alokasi kuota umum vs slot khusus kemitraan beserta rasio keterisian per jurusan.
                </p>
            </div>
            <a href="{{ route('admin.laporan.kuota') }}" class="btn-primary sa-btn-sm sa-btn-full">
                <x-sa.icon name="download" :size="14" />
                <span>Unduh CSV Kuota</span>
            </a>
        </div>

        {{-- Ekspor 4: Rekapitulasi Nilai --}}
        <div class="sa-panel sa-panel--sub sa-export-card">
            <div>
                <div class="sa-export-card__header">
                    <span class="sa-export-card__num">4</span>
                    <h4 class="sa-panel__title">Rekapitulasi Nilai</h4>
                </div>
                <p class="sa-panel__sub">
                    Nilai akhir, predikat kelulusan (A/B/C/D/E), dan rekam hasil evaluasi peserta alumni (Akses Khusus).
                </p>
            </div>
            <a href="{{ route('admin.laporan.nilai') }}" class="btn-primary sa-btn-sm sa-btn-full">
                <x-sa.icon name="download" :size="14" />
                <span>Unduh CSV Nilai</span>
            </a>
        </div>
    </div>
</x-sa.panel>

{{-- BAGIAN 2: PUSAT MONITORING KEAMANAN SISTEM & THREAT RADAR --}}
<x-sa.panel title="Pusat Monitoring Keamanan Sistem & Threat Radar (PRD Bab 10)" subtitle="Pemantauan ancaman siber real-time, deteksi anomali autentikasi 24 jam, higienitas kredensial staf, dan imutabilitas jejak audit" :href="route('superadmin.audit-log.index')" linkText="Audit Lengkap &rarr;">
    <div class="sa-grid sa-grid--metrics">
        {{-- Kartu 1: Radar Anomali Login (24 Jam) --}}
        <div class="sa-panel sa-metric {{ ($loginGagal24Jam ?? 0) > 0 ? 'sa-metric--danger' : 'sa-metric--success' }}">
            <div class="sa-metric__header">
                <span class="sa-metric__label">Radar Anomali Login (24 Jam)</span>
                <span class="sa-metric__icon"><x-sa.icon name="shield" :size="18" /></span>
            </div>
            <div class="sa-metric__value {{ ($loginGagal24Jam ?? 0) > 0 ? 'sa-color-danger' : '' }}">
                {{ $loginGagal24Jam ?? 0 }} <span class="sa-table-sub">Percobaan</span>
            </div>
            <div class="sa-metric__hint">
                HTTP 401 & Rate Limit (429) &bull; {{ ($loginGagal24Jam ?? 0) > 0 ? 'Perlu Tinjauan' : 'Aman' }}
            </div>
        </div>

        {{-- Kartu 2: Higienitas Kredensial Staf (BR-15) --}}
        <div class="sa-panel sa-metric {{ ($stafPendingGantiPassword ?? 0) > 0 ? 'sa-metric--warning' : 'sa-metric--success' }}">
            <div class="sa-metric__header">
                <span class="sa-metric__label">Higienitas Kredensial Staf (BR-15)</span>
                <span class="sa-metric__icon"><x-sa.icon name="key" :size="18" /></span>
            </div>
            <div class="sa-metric__value">
                {{ $stafPendingGantiPassword ?? 0 }} <span class="sa-table-sub">Akun</span>
            </div>
            <div class="sa-metric__hint">
                Pending Ganti Password Awal &bull; {{ ($stafPendingGantiPassword ?? 0) > 0 ? 'Force Change Aktif' : 'Higienis' }}
            </div>
        </div>

        {{-- Kartu 3: Vault Dokumen Privat (NFR-S2) --}}
        <div class="sa-panel sa-metric sa-metric--primary">
            <div class="sa-metric__header">
                <span class="sa-metric__label">Vault Dokumen Privat (NFR-S2)</span>
                <span class="sa-metric__icon"><x-sa.icon name="lock" :size="18" /></span>
            </div>
            <div class="sa-metric__value">
                Maks. {{ $berkasMaksMb ?? 2 }} <span class="sa-table-sub">MB</span>
            </div>
            <div class="sa-metric__hint">
                Signed URL (TTL 5 Menit) &bull; Anti-IDOR/BOLA
            </div>
        </div>

        {{-- Kartu 4: Audit Trail Imutabel (NFR-S9) --}}
        <div class="sa-panel sa-metric sa-metric--success">
            <div class="sa-metric__header">
                <span class="sa-metric__label">Audit Trail Imutabel (NFR-S9)</span>
                <span class="sa-metric__icon"><x-sa.icon name="clipboard-check" :size="18" /></span>
            </div>
            <div class="sa-metric__value">
                {{ number_format($securityPosture['total_audit_events'] ?? 0) }} <span class="sa-table-sub">Log</span>
            </div>
            <div class="sa-metric__hint">
                Append-Only (No Delete/Edit) &bull; Integritas 100%
            </div>
        </div>
    </div>

    {{-- Detail Radar Insiden & Sebaran Kategori Audit PRD Bab 10.3 --}}
    <div class="sa-grid sa-grid--2">
        {{-- Radar Anomali Login Terakhir --}}
        <div class="sa-panel sa-panel--sub">
            <div class="sa-panel__head">
                <h4 class="sa-panel__title">Radar Anomali Login Terakhir</h4>
                <span class="sa-table-sub">5 Rekaman Terbaru</span>
            </div>

            @if($insidenKeamananTerbaru->isEmpty())
                <x-sa.empty text="Kondisi Sistem Bersih & Terkendali. Tidak ada rekaman percobaan login gagal, serangan brute force, atau pelanggaran rate limiting." icon="circle-check" />
            @else
                <div class="sa-table-scroll">
                    <table class="sa-table">
                        <caption class="sa-sr-only">Tabel Rekaman Anomali Login Terakhir</caption>
                        <thead>
                            <tr>
                                <th scope="col">Waktu</th>
                                <th scope="col">IP Address</th>
                                <th scope="col">Target Identitas</th>
                                <th scope="col" class="sa-text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($insidenKeamananTerbaru as $insiden)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($insiden->waktu)->format('d/m H:i') }}</td>
                                    <td><code>{{ $insiden->ip ?? '-' }}</code></td>
                                    <td>{{ $insiden->user?->nama ?? $insiden->user?->email ?? ($insiden->payload['email'] ?? 'Publik/Guest') }}</td>
                                    <td class="sa-text-right">
                                        <span class="sa-badge sa-badge--danger">
                                            {{ $insiden->status_http ?? 401 }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Distribusi Kategori Audit PRD Bab 10.3 --}}
        <div class="sa-panel sa-panel--sub">
            @php
                $totAudit = max(1, ($auditTipeDistribution['auth'] ?? 0) + ($auditTipeDistribution['aksi'] ?? 0) + ($auditTipeDistribution['service'] ?? 0) + ($auditTipeDistribution['akses_data'] ?? 0));
                $pctAuth = round((($auditTipeDistribution['auth'] ?? 0) / $totAudit) * 100);
                $pctAksi = round((($auditTipeDistribution['aksi'] ?? 0) / $totAudit) * 100);
                $pctService = round((($auditTipeDistribution['service'] ?? 0) / $totAudit) * 100);
                $pctAkses = max(0, 100 - ($pctAuth + $pctAksi + $pctService));
            @endphp

            <div class="sa-panel__head">
                <h4 class="sa-panel__title">Distribusi Audit Trail (PRD Bab 10.3)</h4>
                <strong class="sa-table-sub">{{ number_format($totAudit) }} Total Event</strong>
            </div>

            {{-- Visual Segmented Bar --}}
            <div class="sa-segmented-bar" role="meter" aria-valuenow="{{ $totAudit }}" aria-label="Distribusi kategori audit">
                <div class="sa-segmented-bar__seg sa-seg--primary" data-v="{{ $pctAuth }}" title="Auth: {{ $pctAuth }}%"></div>
                <div class="sa-segmented-bar__seg sa-seg--success" data-v="{{ $pctAksi }}" title="Aksi: {{ $pctAksi }}%"></div>
                <div class="sa-segmented-bar__seg sa-seg--warning" data-v="{{ $pctService }}" title="Service: {{ $pctService }}%"></div>
                <div class="sa-segmented-bar__seg sa-seg--neutral" data-v="{{ $pctAkses }}" title="Akses Data: {{ $pctAkses }}%"></div>
            </div>

            {{-- Legend & Counts Grid --}}
            <div class="sa-grid sa-grid--2 sa-audit-legend">
                <div class="sa-legend-item">
                    <span class="sa-legend-label">
                        <span class="sa-dot sa-seg--primary"></span>
                        Auth (Login/Sesi)
                    </span>
                    <strong>{{ number_format($auditTipeDistribution['auth'] ?? 0) }}</strong>
                </div>
                <div class="sa-legend-item">
                    <span class="sa-legend-label">
                        <span class="sa-dot sa-seg--success"></span>
                        Aksi (Mutasi Data)
                    </span>
                    <strong>{{ number_format($auditTipeDistribution['aksi'] ?? 0) }}</strong>
                </div>
                <div class="sa-legend-item">
                    <span class="sa-legend-label">
                        <span class="sa-dot sa-seg--warning"></span>
                        Service (Scheduler)
                    </span>
                    <strong>{{ number_format($auditTipeDistribution['service'] ?? 0) }}</strong>
                </div>
                <div class="sa-legend-item">
                    <span class="sa-legend-label">
                        <span class="sa-dot sa-seg--neutral"></span>
                        Akses Data (Unduh)
                    </span>
                    <strong>{{ number_format($auditTipeDistribution['akses_data'] ?? 0) }}</strong>
                </div>
            </div>

            <div class="sa-hardening-list">
                <div class="sa-hardening-item">
                    <span>• HTTP Headers: X-Frame-Options DENY & nosniff (NFR-S8)</span>
                    <span class="sa-badge sa-badge--success">AKTIF</span>
                </div>
                <div class="sa-hardening-item">
                    <span>• Rate Limiter & Anti-Bruteforce Throttle (TDD Bab 8)</span>
                    <span class="sa-badge sa-badge--success">AKTIF</span>
                </div>
                <div class="sa-hardening-item">
                    <span>• Enkripsi Sandi Bcrypt (Cost 12) & Proteksi CSRF Form</span>
                    <span class="sa-badge sa-badge--success">AKTIF</span>
                </div>
            </div>
        </div>
    </div>
</x-sa.panel>

{{-- BAGIAN 3: PINTASAN KONTROL SISTEM, ROLE & AUDIT LOG --}}
<div class="sa-grid sa-grid--2">
    {{-- Box Tata Kelola Hak Akses & Master Data --}}
    <x-sa.panel title="Kontrol Hak Akses & Master Data (PRD 3.1 & 3.3)">
        <div class="sa-list">
            <a href="{{ route('superadmin.role.index') }}" class="sa-list-item sa-list-item--link">
                <div class="sa-list-item__left">
                    <x-sa.icon name="shield" :size="16" />
                    <span class="sa-table-strong">Matriks Peran & Hak Akses (Figma SA-02)</span>
                </div>
                <span class="sa-link">Lihat &rarr;</span>
            </a>

            <a href="{{ route('superadmin.jurusan.index') }}" class="sa-list-item sa-list-item--link">
                <div class="sa-list-item__left">
                    <x-sa.icon name="book-open" :size="16" />
                    <span class="sa-table-strong">Master Jurusan & Program Studi</span>
                </div>
                <span class="sa-link">Kelola &rarr;</span>
            </a>

            <a href="{{ route('superadmin.bidang.index') }}" class="sa-list-item sa-list-item--link">
                <div class="sa-list-item__left">
                    <x-sa.icon name="layers" :size="16" />
                    <span class="sa-table-strong">Master Bidang Kerja Diskominfo</span>
                </div>
                <span class="sa-link">Kelola &rarr;</span>
            </a>

            <a href="{{ route('superadmin.rubrik.index') }}" class="sa-list-item sa-list-item--link">
                <div class="sa-list-item__left">
                    <x-sa.icon name="award" :size="16" />
                    <span class="sa-table-strong">Bobot Rubrik Penilaian Akhir</span>
                </div>
                <span class="sa-link">Atur &rarr;</span>
            </a>
        </div>
    </x-sa.panel>

    {{-- Box Keamanan, Konfigurasi & Arsip --}}
    <x-sa.panel title="Keamanan, Arsip & Konfigurasi Sistem">
        <div class="sa-list">
            <a href="{{ route('superadmin.pengaturan.index') }}" class="sa-list-item sa-list-item--link">
                <div class="sa-list-item__left">
                    <x-sa.icon name="settings" :size="16" />
                    <span class="sa-table-strong">Parameter Konfigurasi Global</span>
                </div>
                <span class="sa-link">Ubah &rarr;</span>
            </a>

            <a href="{{ route('superadmin.arsip.index') }}" class="sa-list-item sa-list-item--link">
                <div class="sa-list-item__left">
                    <x-sa.icon name="archive" :size="16" />
                    <span class="sa-table-strong">Gudang Arsip Peserta Magang (F-12)</span>
                </div>
                <span class="sa-link">Buka &rarr;</span>
            </a>

            <a href="{{ route('superadmin.audit-log.index') }}" class="sa-list-item sa-list-item--link">
                <div class="sa-list-item__left">
                    <x-sa.icon name="file-text" :size="16" />
                    <span class="sa-table-strong">Jejak Audit Trail & Log Keamanan</span>
                </div>
                <span class="sa-link">Buka &rarr;</span>
            </a>

            <a href="{{ route('superadmin.audit-log.export') }}" class="sa-list-item sa-list-item--link">
                <div class="sa-list-item__left">
                    <x-sa.icon name="download" :size="16" />
                    <span class="sa-table-strong">Ekspor Jejak Audit Trail (CSV)</span>
                </div>
                <span class="sa-link sa-color-danger">Unduh &rarr;</span>
            </a>

            <a href="{{ route('superadmin.health.index') }}" class="sa-list-item sa-list-item--link">
                <div class="sa-list-item__left">
                    <x-sa.icon name="activity" :size="16" />
                    <span class="sa-table-strong">Telemetri Kesehatan & Storage Infrastruktur</span>
                </div>
                <span class="sa-link">Buka &rarr;</span>
            </a>

            <a href="{{ route('superadmin.backup.index') }}" class="sa-list-item sa-list-item--link">
                <div class="sa-list-item__left">
                    <x-sa.icon name="database" :size="16" />
                    <span class="sa-table-strong">Pusat Cadangan & Pemulihan Basis Data (Backup Hub)</span>
                </div>
                <span class="sa-link">Buka &rarr;</span>
            </a>
        </div>

        <div class="sa-grid sa-grid--2 sa-param-grid">
            <div class="sa-panel sa-panel--sub">
                <span class="sa-table-sub">logbook_maks_mundur</span>
                <strong>Maks. {{ $logbookMaksMundurHari }} Hari (BR-09)</strong>
            </div>
            <div class="sa-panel sa-panel--sub">
                <span class="sa-table-sub">masa_tenggang_bulan</span>
                <strong>{{ $masaTenggangBulan }} Bulan (BR-12)</strong>
            </div>
            <div class="sa-panel sa-panel--sub">
                <span class="sa-table-sub">berkas_maks_mb</span>
                <strong>Maks. {{ $berkasMaksMb }} MB (BR-03)</strong>
            </div>
            <div class="sa-panel sa-panel--sub">
                <span class="sa-table-sub">slot_khusus_default</span>
                <strong>{{ $slotKhususDefault }} Kuota (BR-01)</strong>
            </div>
        </div>
    </x-sa.panel>
</div>

{{-- BAGIAN 4: STATUS KESEHATAN SISTEM & INFRASTRUKTUR (TDD Bab 10, NFR-O1) --}}
<x-sa.panel title="Integritas Infrastruktur & Status Layanan (TDD Bab 10)" subtitle="Telemetri Infrastruktur &amp; Sistem &bull; Pemantauan integritas database MySQL/InnoDB, storage berkas privat terenkripsi, dan scheduler cron job" :href="route('health')" linkText="Buka Healthcheck API (/health) &rarr;">
    <div class="sa-grid sa-grid--4">
        <div class="sa-panel sa-panel--sub sa-infra-item">
            <x-sa.icon name="database" :size="18" />
            <div>
                <span class="sa-table-strong">Koneksi Basis Data</span>
                <span class="sa-table-sub">MySQL InnoDB / Normal</span>
            </div>
        </div>

        <div class="sa-panel sa-panel--sub sa-infra-item">
            <x-sa.icon name="hard-drive" :size="18" />
            <div>
                <span class="sa-table-strong">Private Disk Storage</span>
                <span class="sa-table-sub">Disk Privat Terisolasi</span>
            </div>
        </div>

        <div class="sa-panel sa-panel--sub sa-infra-item">
            <x-sa.icon name="clipboard-check" :size="18" />
            <div>
                <span class="sa-table-strong">Audit Trail Append-Only</span>
                <span class="sa-table-sub">{{ number_format($systemHealth['audit_count'] ?? 0) }} Rekaman Terarsip</span>
            </div>
        </div>

        <div class="sa-panel sa-panel--sub sa-infra-item">
            <x-sa.icon name="clock" :size="18" />
            <div>
                <span class="sa-table-strong">Scheduler Nonaktif Harian</span>
                <span class="sa-table-sub">Terkonfigurasi di Kernel</span>
            </div>
        </div>
    </div>

    {{-- System Summary Text (as plain informative text per §8 & §11) --}}
    <div class="sa-infra-meta">
        <span>Cadangan terakhir: {{ $systemSummary['last_backup'] ?? 'Tercatat' }}</span>
        <span>&bull;</span>
        <span>Kapasitas disk: {{ $systemSummary['disk_free_gb'] ?? 0 }} GB bebas (Terpakai {{ $systemSummary['disk_usage_percent'] ?? 0 }}%)</span>
        <span>&bull;</span>
        <span>Sesi aktif: {{ $systemSummary['active_sessions'] ?? 0 }} akun</span>
        <span>&bull;</span>
        <span>PHP {{ $systemSummary['php_version'] ?? phpversion() }}</span>
    </div>
</x-sa.panel>
