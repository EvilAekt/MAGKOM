{{-- ==========================================================================
   Tab 4: Tata Kelola Sistem & Laporan - SuperAdmin Master Cockpit
   Standar Visual Gentelella Theme (MagKom Sleman)
   ========================================================================== --}}
@php
    /** @var int|null $loginGagal24Jam */
    /** @var int|null $stafPendingGantiPassword */
    /** @var int|null $berkasMaksMb */
    /** @var array<string, mixed>|null $securityPosture */
    /** @var \Illuminate\Support\Collection<int, \App\Models\AuditLog> $insidenKeamananTerbaru */
    /** @var array<string, int>|null $auditTipeDistribution */
    /** @var int $logbookMaksMundurHari */
    /** @var int $masaTenggangBulan */
    /** @var int $slotKhususDefault */
    /** @var array<string, mixed> $systemHealth */
    /** @var array<string, mixed> $systemSummary */
@endphp
<div class="gentelella-tab-pane gentelella-tab-sistem">
{{-- BAGIAN 1: HUB EKSPOR 4 LAPORAN RESMI DINAS 1-KLIK --}}
<x-sa.panel title="Hub Ekspor Instan Laporan Resmi Dinas" :href="route('admin.laporan.index')" linkText="Buka Filter Laporan Lanjutan &rarr;">
    <div class="sa-grid sa-grid--4" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 1rem;">
        {{-- Ekspor 1: Pendaftaran --}}
        <div class="sa-panel sa-panel--sub sa-export-card" style="background: var(--cv-surface); border: 1px solid var(--cv-line); border-radius: 12px; padding: 1.25rem; display: flex; flex-direction: column; justify-content: space-between; gap: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
            <div>
                <div class="sa-export-card__header" style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.5rem;">
                    <span class="sa-export-card__num" style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 6px; font-weight: 800; font-family: monospace; font-size: 0.8125rem;">01</span>
                    <h4 class="sa-panel__title" style="font-size: 0.9375rem; font-weight: 700; color: var(--cv-ink); margin: 0;">Laporan Pendaftaran</h4>
                </div>
            </div>
            <a href="{{ route('admin.laporan.pendaftaran') }}" class="btn btn-primary sa-btn-sm sa-btn-full" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem; border-radius: 6px; font-weight: 600; padding: 0.5rem 0.85rem; text-decoration: none;">
                <x-sa.icon name="download" :size="14" />
                <span>Unduh CSV Pendaftaran</span>
            </a>
        </div>

        {{-- Ekspor 2: Penempatan --}}
        <div class="sa-panel sa-panel--sub sa-export-card" style="background: var(--cv-surface); border: 1px solid var(--cv-line); border-radius: 12px; padding: 1.25rem; display: flex; flex-direction: column; justify-content: space-between; gap: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
            <div>
                <div class="sa-export-card__header" style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.5rem;">
                    <span class="sa-export-card__num" style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 6px; font-weight: 800; font-family: monospace; font-size: 0.8125rem;">02</span>
                    <h4 class="sa-panel__title" style="font-size: 0.9375rem; font-weight: 700; color: var(--cv-ink); margin: 0;">Laporan Penempatan</h4>
                </div>
            </div>
            <a href="{{ route('admin.laporan.penempatan') }}" class="btn btn-primary sa-btn-sm sa-btn-full" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem; border-radius: 6px; font-weight: 600; padding: 0.5rem 0.85rem; text-decoration: none;">
                <x-sa.icon name="download" :size="14" />
                <span>Unduh CSV Penempatan</span>
            </a>
        </div>

        {{-- Ekspor 3: Realisasi Kuota --}}
        <div class="sa-panel sa-panel--sub sa-export-card" style="background: var(--cv-surface); border: 1px solid var(--cv-line); border-radius: 12px; padding: 1.25rem; display: flex; flex-direction: column; justify-content: space-between; gap: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
            <div>
                <div class="sa-export-card__header" style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.5rem;">
                    <span class="sa-export-card__num" style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 6px; font-weight: 800; font-family: monospace; font-size: 0.8125rem;">03</span>
                    <h4 class="sa-panel__title" style="font-size: 0.9375rem; font-weight: 700; color: var(--cv-ink); margin: 0;">Realisasi Kuota</h4>
                </div>
            </div>
            <a href="{{ route('admin.laporan.kuota') }}" class="btn btn-primary sa-btn-sm sa-btn-full" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem; border-radius: 6px; font-weight: 600; padding: 0.5rem 0.85rem; text-decoration: none;">
                <x-sa.icon name="download" :size="14" />
                <span>Unduh CSV Kuota</span>
            </a>
        </div>

        {{-- Ekspor 4: Rekapitulasi Nilai --}}
        <div class="sa-panel sa-panel--sub sa-export-card" style="background: var(--cv-surface); border: 1px solid var(--cv-line); border-radius: 12px; padding: 1.25rem; display: flex; flex-direction: column; justify-content: space-between; gap: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
            <div>
                <div class="sa-export-card__header" style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.5rem;">
                    <span class="sa-export-card__num" style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 6px; font-weight: 800; font-family: monospace; font-size: 0.8125rem;">04</span>
                    <h4 class="sa-panel__title" style="font-size: 0.9375rem; font-weight: 700; color: var(--cv-ink); margin: 0;">Rekapitulasi Nilai</h4>
                </div>
            </div>
            <a href="{{ route('admin.laporan.nilai') }}" class="btn btn-primary sa-btn-sm sa-btn-full" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem; border-radius: 6px; font-weight: 600; padding: 0.5rem 0.85rem; text-decoration: none;">
                <x-sa.icon name="download" :size="14" />
                <span>Unduh CSV Nilai</span>
            </a>
        </div>
    </div>
</x-sa.panel>

{{-- BAGIAN 2: PUSAT MONITORING KEAMANAN SISTEM & THREAT RADAR --}}
<x-sa.panel title="Pusat Monitoring Keamanan Sistem & Threat Radar (PRD Bab 10)" :href="route('superadmin.audit-log.index')" linkText="Audit Lengkap &rarr;">
    <div class="sa-grid sa-grid--metrics" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        {{-- Kartu 1: Radar Anomali Login (24 Jam) --}}
        <div class="sa-panel sa-metric {{ ($loginGagal24Jam ?? 0) > 0 ? 'sa-metric--danger' : 'sa-metric--success' }}" style="background: var(--cv-surface); border: 1px solid var(--cv-line); border-left: 4px solid {{ ($loginGagal24Jam ?? 0) > 0 ? '#EF4444' : '#10B981' }}; border-radius: 12px; padding: 1.25rem;">
            <div class="sa-metric__header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                <span class="sa-metric__label" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--cv-ink-3);">Radar Anomali Login (24 Jam)</span>
                <span class="sa-metric__icon" style="color: {{ ($loginGagal24Jam ?? 0) > 0 ? '#EF4444' : '#10B981' }};"><x-sa.icon name="shield" :size="18" /></span>
            </div>
            <div class="sa-metric__value {{ ($loginGagal24Jam ?? 0) > 0 ? 'sa-color-danger' : '' }}" style="font-size: 2.25rem; font-weight: 800; font-family: monospace; color: {{ ($loginGagal24Jam ?? 0) > 0 ? '#DC2626' : 'var(--cv-ink)' }}; line-height: 1.1;">
                {{ $loginGagal24Jam ?? 0 }} <span class="sa-table-sub" style="font-size: 0.8125rem; text-transform: uppercase;">Percobaan</span>
            </div>
            <div class="sa-metric__hint" style="font-size: 0.725rem; color: var(--cv-ink-2); margin-top: 0.35rem;">
                HTTP 401 & Rate Limit (429) &bull; {{ ($loginGagal24Jam ?? 0) > 0 ? 'Perlu Tinjauan' : 'Aman' }}
            </div>
        </div>

        {{-- Kartu 2: Higienitas Kredensial Staf (BR-15) --}}
        <div class="sa-panel sa-metric {{ ($stafPendingGantiPassword ?? 0) > 0 ? 'sa-metric--warning' : 'sa-metric--success' }}" style="background: var(--cv-surface); border: 1px solid var(--cv-line); border-left: 4px solid {{ ($stafPendingGantiPassword ?? 0) > 0 ? '#F59E0B' : '#10B981' }}; border-radius: 12px; padding: 1.25rem;">
            <div class="sa-metric__header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                <span class="sa-metric__label" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--cv-ink-3);">Higienitas Kredensial Staf (BR-15)</span>
                <span class="sa-metric__icon" style="color: {{ ($stafPendingGantiPassword ?? 0) > 0 ? '#F59E0B' : '#10B981' }};"><x-sa.icon name="key" :size="18" /></span>
            </div>
            <div class="sa-metric__value" style="font-size: 2.25rem; font-weight: 800; font-family: monospace; color: {{ ($stafPendingGantiPassword ?? 0) > 0 ? '#D97706' : 'var(--cv-ink)' }}; line-height: 1.1;">
                {{ $stafPendingGantiPassword ?? 0 }} <span class="sa-table-sub" style="font-size: 0.8125rem; text-transform: uppercase;">Akun</span>
            </div>
            <div class="sa-metric__hint" style="font-size: 0.725rem; color: var(--cv-ink-2); margin-top: 0.35rem;">
                Pending Ganti Password Awal &bull; {{ ($stafPendingGantiPassword ?? 0) > 0 ? 'Force Change Aktif' : 'Higienis' }}
            </div>
        </div>

        {{-- Kartu 3: Vault Dokumen Privat (NFR-S2) --}}
        <div class="sa-panel sa-metric sa-metric--primary" style="background: var(--cv-surface); border: 1px solid var(--cv-line); border-left: 4px solid var(--cv-blue-600); border-radius: 12px; padding: 1.25rem;">
            <div class="sa-metric__header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                <span class="sa-metric__label" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--cv-ink-3);">Vault Dokumen Privat (NFR-S2)</span>
                <span class="sa-metric__icon" style="color: var(--cv-blue-600);"><x-sa.icon name="lock" :size="18" /></span>
            </div>
            <div class="sa-metric__value sa-stat-val-primary" style="font-size: 2.25rem; font-weight: 800; font-family: monospace; line-height: 1.1;">
                Maks. {{ $berkasMaksMb ?? 2 }} <span class="sa-table-sub" style="font-size: 0.8125rem;">MB</span>
            </div>
            <div class="sa-metric__hint" style="font-size: 0.725rem; color: var(--cv-ink-2); margin-top: 0.35rem;">
                Signed URL (TTL 5 Menit) &bull; Anti-IDOR/BOLA
            </div>
        </div>

        {{-- Kartu 4: Audit Trail Imutabel (NFR-S9) --}}
        <div class="sa-panel sa-metric sa-metric--success" style="background: var(--cv-surface); border: 1px solid var(--cv-line); border-left: 4px solid #10B981; border-radius: 12px; padding: 1.25rem;">
            <div class="sa-metric__header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                <span class="sa-metric__label" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--cv-ink-3);">Audit Trail Imutabel (NFR-S9)</span>
                <span class="sa-metric__icon" style="color: #10B981;"><x-sa.icon name="clipboard-check" :size="18" /></span>
            </div>
            <div class="sa-metric__value" style="font-size: 2.25rem; font-weight: 800; font-family: monospace; color: #059669; line-height: 1.1;">
                {{ number_format($securityPosture['total_audit_events'] ?? 0) }} <span class="sa-table-sub" style="font-size: 0.8125rem;">Log</span>
            </div>
            <div class="sa-metric__hint" style="font-size: 0.725rem; color: var(--cv-ink-2); margin-top: 0.35rem;">
                Append-Only (No Delete/Edit) &bull; Integritas 100%
            </div>
        </div>
    </div>

    {{-- Detail Radar Insiden & Sebaran Kategori Audit PRD Bab 10.3 --}}
    <div class="sa-grid sa-grid--2" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.25rem;">
        {{-- Radar Anomali Login Terakhir --}}
        <div class="sa-panel sa-panel--sub" style="background: var(--cv-surface); border: 1px solid var(--cv-line); border-radius: 10px; padding: 1.25rem;">
            <div class="sa-panel__head" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                <h4 class="sa-panel__title" style="font-size: 0.875rem; font-weight: 700; color: var(--cv-ink); margin: 0;">Radar Anomali Login Terakhir</h4>
                <span class="sa-table-sub" style="font-size: 0.725rem; font-weight: 600;">5 Rekaman Terbaru</span>
            </div>

            @if($insidenKeamananTerbaru->isEmpty())
                <x-sa.empty text="Kondisi Sistem Bersih & Terkendali. Tidak ada rekaman percobaan login gagal, serangan brute force, atau pelanggaran rate limiting." icon="circle-check" />
            @else
                <div class="sa-table-scroll">
                    <table class="sa-table cv-table-modern">
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
                                    <td style="font-family: monospace; font-size: 0.75rem;">{{ \Carbon\Carbon::parse($insiden->waktu)->format('d/m H:i') }}</td>
                                    <td><code style="background: var(--cv-paper); padding: 2px 6px; border-radius: 4px; font-size: 0.75rem; border: 1px solid var(--cv-line);">{{ $insiden->ip ?? '-' }}</code></td>
                                    <td style="font-size: 0.8125rem;">{{ $insiden->user?->nama ?? $insiden->user?->email ?? ($insiden->payload['email'] ?? 'Publik/Guest') }}</td>
                                    <td class="sa-text-right">
                                        <span class="sa-badge sa-badge--danger" style="font-weight: 700; border-radius: 4px; padding: 2px 8px; font-size: 0.75rem;">
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
        <div class="sa-panel sa-panel--sub" style="background: var(--cv-surface); border: 1px solid var(--cv-line); border-radius: 10px; padding: 1.25rem;">
            @php
                $totAudit = max(1, ($auditTipeDistribution['auth'] ?? 0) + ($auditTipeDistribution['aksi'] ?? 0) + ($auditTipeDistribution['service'] ?? 0) + ($auditTipeDistribution['akses_data'] ?? 0));
                $pctAuth = round((($auditTipeDistribution['auth'] ?? 0) / $totAudit) * 100);
                $pctAksi = round((($auditTipeDistribution['aksi'] ?? 0) / $totAudit) * 100);
                $pctService = round((($auditTipeDistribution['service'] ?? 0) / $totAudit) * 100);
                $pctAkses = max(0, 100 - ($pctAuth + $pctAksi + $pctService));
            @endphp

            <div class="sa-panel__head" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                <h4 class="sa-panel__title" style="font-size: 0.875rem; font-weight: 700; color: var(--cv-ink); margin: 0;">Distribusi Audit Trail (PRD Bab 10.3)</h4>
                <strong class="sa-table-sub" style="font-family: monospace; font-size: 0.75rem;">{{ number_format($totAudit) }} Total Event</strong>
            </div>

            {{-- Visual Segmented Bar --}}
            <div class="sa-segmented-bar" role="meter" aria-valuenow="{{ $totAudit }}" aria-label="Distribusi kategori audit" style="display: flex; height: 10px; border-radius: 999px; overflow: hidden; background: var(--cv-line); margin-bottom: 1rem;">
                <div class="sa-segmented-bar__seg sa-seg--primary" data-v="{{ $pctAuth }}" title="Auth: {{ $pctAuth }}%" style="width: {{ $pctAuth }}%; background: var(--cv-blue-700);"></div>
                <div class="sa-segmented-bar__seg sa-seg--success" data-v="{{ $pctAksi }}" title="Aksi: {{ $pctAksi }}%" style="width: {{ $pctAksi }}%; background: #10B981;"></div>
                <div class="sa-segmented-bar__seg sa-seg--warning" data-v="{{ $pctService }}" title="Service: {{ $pctService }}%" style="width: {{ $pctService }}%; background: #F59E0B;"></div>
                <div class="sa-segmented-bar__seg sa-seg--neutral" data-v="{{ $pctAkses }}" title="Akses Data: {{ $pctAkses }}%" style="width: {{ $pctAkses }}%; background: #64748B;"></div>
            </div>

            {{-- Legend & Counts Grid --}}
            <div class="sa-grid sa-grid--2 sa-audit-legend" style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-bottom: 1rem; font-size: 0.75rem;">
                <div class="sa-legend-item" style="display: flex; justify-content: space-between; align-items: center; padding: 4px 8px; background: var(--cv-paper); border-radius: 6px;">
                    <span class="sa-legend-label" style="display: flex; align-items: center; gap: 6px;">
                        <span class="sa-dot sa-seg--primary" style="width: 8px; height: 8px; border-radius: 50%; background: var(--cv-blue-700);"></span>
                        Auth (Login/Sesi)
                    </span>
                    <strong style="font-family: monospace;">{{ number_format($auditTipeDistribution['auth'] ?? 0) }}</strong>
                </div>
                <div class="sa-legend-item" style="display: flex; justify-content: space-between; align-items: center; padding: 4px 8px; background: var(--cv-paper); border-radius: 6px;">
                    <span class="sa-legend-label" style="display: flex; align-items: center; gap: 6px;">
                        <span class="sa-dot sa-seg--success" style="width: 8px; height: 8px; border-radius: 50%; background: #10B981;"></span>
                        Aksi (Mutasi Data)
                    </span>
                    <strong style="font-family: monospace;">{{ number_format($auditTipeDistribution['aksi'] ?? 0) }}</strong>
                </div>
                <div class="sa-legend-item" style="display: flex; justify-content: space-between; align-items: center; padding: 4px 8px; background: var(--cv-paper); border-radius: 6px;">
                    <span class="sa-legend-label" style="display: flex; align-items: center; gap: 6px;">
                        <span class="sa-dot sa-seg--warning" style="width: 8px; height: 8px; border-radius: 50%; background: #F59E0B;"></span>
                        Service (Scheduler)
                    </span>
                    <strong style="font-family: monospace;">{{ number_format($auditTipeDistribution['service'] ?? 0) }}</strong>
                </div>
                <div class="sa-legend-item" style="display: flex; justify-content: space-between; align-items: center; padding: 4px 8px; background: var(--cv-paper); border-radius: 6px;">
                    <span class="sa-legend-label" style="display: flex; align-items: center; gap: 6px;">
                        <span class="sa-dot sa-seg--neutral" style="width: 8px; height: 8px; border-radius: 50%; background: #64748B;"></span>
                        Akses Data (Unduh)
                    </span>
                    <strong style="font-family: monospace;">{{ number_format($auditTipeDistribution['akses_data'] ?? 0) }}</strong>
                </div>
            </div>

            <div class="sa-hardening-list" style="display: flex; flex-direction: column; gap: 0.35rem; font-size: 0.725rem; color: var(--cv-ink-2);">
                <div class="sa-hardening-item" style="display: flex; justify-content: space-between; align-items: center; padding: 3px 0;">
                    <span>• HTTP Headers: X-Frame-Options DENY & nosniff (NFR-S8)</span>
                    <span class="sa-badge sa-badge--success" style="font-weight: 700; border-radius: 4px; padding: 1px 6px;">AKTIF</span>
                </div>
                <div class="sa-hardening-item" style="display: flex; justify-content: space-between; align-items: center; padding: 3px 0;">
                    <span>• Rate Limiter & Anti-Bruteforce Throttle (TDD Bab 8)</span>
                    <span class="sa-badge sa-badge--success" style="font-weight: 700; border-radius: 4px; padding: 1px 6px;">AKTIF</span>
                </div>
                <div class="sa-hardening-item" style="display: flex; justify-content: space-between; align-items: center; padding: 3px 0;">
                    <span>• Enkripsi Sandi Bcrypt (Cost 12) & Proteksi CSRF Form</span>
                    <span class="sa-badge sa-badge--success" style="font-weight: 700; border-radius: 4px; padding: 1px 6px;">AKTIF</span>
                </div>
            </div>
        </div>
    </div>
</x-sa.panel>

{{-- BAGIAN 3: PINTASAN KONTROL SISTEM, ROLE & AUDIT LOG --}}
<div class="sa-grid sa-grid--2" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.25rem;">
    {{-- Box Tata Kelola Hak Akses & Master Data --}}
    <x-sa.panel title="Kontrol Hak Akses & Master Data (PRD 3.1 & 3.3)">
        <div class="sa-list" style="display: flex; flex-direction: column; gap: 0.5rem;">
            <a href="{{ route('superadmin.role.index') }}" class="sa-list-item sa-list-item--link" style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; background: var(--cv-surface); border: 1px solid var(--cv-line); border-radius: 8px; text-decoration: none; color: var(--cv-ink); transition: all 0.15s ease;">
                <div class="sa-list-item__left" style="display: flex; align-items: center; gap: 0.6rem;">
                    <x-sa.icon name="shield" :size="16" />
                    <span class="sa-table-strong" style="font-size: 0.8125rem;">Matriks Peran & Hak Akses (Figma SA-02)</span>
                </div>
                <span class="sa-link" style="font-weight: 600; font-size: 0.8125rem;">Lihat &rarr;</span>
            </a>

            <a href="{{ route('superadmin.jurusan.index') }}" class="sa-list-item sa-list-item--link" style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; background: var(--cv-surface); border: 1px solid var(--cv-line); border-radius: 8px; text-decoration: none; color: var(--cv-ink); transition: all 0.15s ease;">
                <div class="sa-list-item__left" style="display: flex; align-items: center; gap: 0.6rem;">
                    <x-sa.icon name="book-open" :size="16" />
                    <span class="sa-table-strong" style="font-size: 0.8125rem;">Master Jurusan & Program Studi</span>
                </div>
                <span class="sa-link" style="font-weight: 600; font-size: 0.8125rem;">Kelola &rarr;</span>
            </a>

            <a href="{{ route('superadmin.bidang.index') }}" class="sa-list-item sa-list-item--link" style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; background: var(--cv-surface); border: 1px solid var(--cv-line); border-radius: 8px; text-decoration: none; color: var(--cv-ink); transition: all 0.15s ease;">
                <div class="sa-list-item__left" style="display: flex; align-items: center; gap: 0.6rem;">
                    <x-sa.icon name="layers" :size="16" />
                    <span class="sa-table-strong" style="font-size: 0.8125rem;">Master Bidang Kerja Diskominfo</span>
                </div>
                <span class="sa-link" style="font-weight: 600; font-size: 0.8125rem;">Kelola &rarr;</span>
            </a>

            <a href="{{ route('superadmin.rubrik.index') }}" class="sa-list-item sa-list-item--link" style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; background: var(--cv-surface); border: 1px solid var(--cv-line); border-radius: 8px; text-decoration: none; color: var(--cv-ink); transition: all 0.15s ease;">
                <div class="sa-list-item__left" style="display: flex; align-items: center; gap: 0.6rem;">
                    <x-sa.icon name="award" :size="16" />
                    <span class="sa-table-strong" style="font-size: 0.8125rem;">Bobot Rubrik Penilaian Akhir</span>
                </div>
                <span class="sa-link" style="font-weight: 600; font-size: 0.8125rem;">Atur &rarr;</span>
            </a>
        </div>
    </x-sa.panel>

    {{-- Box Keamanan, Konfigurasi & Arsip --}}
    <x-sa.panel title="Keamanan, Arsip & Konfigurasi Sistem">
        <div class="sa-list" style="display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 1rem;">
            <a href="{{ route('superadmin.pengaturan.index') }}" class="sa-list-item sa-list-item--link" style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; background: var(--cv-surface); border: 1px solid var(--cv-line); border-radius: 8px; text-decoration: none; color: var(--cv-ink); transition: all 0.15s ease;">
                <div class="sa-list-item__left" style="display: flex; align-items: center; gap: 0.6rem;">
                    <x-sa.icon name="settings" :size="16" />
                    <span class="sa-table-strong" style="font-size: 0.8125rem;">Parameter Konfigurasi Global</span>
                </div>
                <span class="sa-link" style="font-weight: 600; font-size: 0.8125rem;">Ubah &rarr;</span>
            </a>

            <a href="{{ route('superadmin.arsip.index') }}" class="sa-list-item sa-list-item--link" style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; background: var(--cv-surface); border: 1px solid var(--cv-line); border-radius: 8px; text-decoration: none; color: var(--cv-ink); transition: all 0.15s ease;">
                <div class="sa-list-item__left" style="display: flex; align-items: center; gap: 0.6rem;">
                    <x-sa.icon name="archive" :size="16" />
                    <span class="sa-table-strong" style="font-size: 0.8125rem;">Gudang Arsip Peserta Magang (F-12)</span>
                </div>
                <span class="sa-link" style="font-weight: 600; font-size: 0.8125rem;">Buka &rarr;</span>
            </a>

            <a href="{{ route('superadmin.audit-log.index') }}" class="sa-list-item sa-list-item--link" style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; background: var(--cv-surface); border: 1px solid var(--cv-line); border-radius: 8px; text-decoration: none; color: var(--cv-ink); transition: all 0.15s ease;">
                <div class="sa-list-item__left" style="display: flex; align-items: center; gap: 0.6rem;">
                    <x-sa.icon name="file-text" :size="16" />
                    <span class="sa-table-strong" style="font-size: 0.8125rem;">Jejak Audit Trail & Log Keamanan</span>
                </div>
                <span class="sa-link" style="font-weight: 600; font-size: 0.8125rem;">Buka &rarr;</span>
            </a>

            <a href="{{ route('superadmin.audit-log.export') }}" class="sa-list-item sa-list-item--link" style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; background: var(--cv-surface); border: 1px solid var(--cv-line); border-radius: 8px; text-decoration: none; color: var(--cv-ink); transition: all 0.15s ease;">
                <div class="sa-list-item__left" style="display: flex; align-items: center; gap: 0.6rem;">
                    <x-sa.icon name="download" :size="16" />
                    <span class="sa-table-strong" style="font-size: 0.8125rem;">Ekspor Jejak Audit Trail (CSV)</span>
                </div>
                <span class="sa-link sa-color-danger" style="font-weight: 600; color: #DC2626; font-size: 0.8125rem;">Unduh &rarr;</span>
            </a>

            <a href="{{ route('superadmin.health.index') }}" class="sa-list-item sa-list-item--link" style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; background: var(--cv-surface); border: 1px solid var(--cv-line); border-radius: 8px; text-decoration: none; color: var(--cv-ink); transition: all 0.15s ease;">
                <div class="sa-list-item__left" style="display: flex; align-items: center; gap: 0.6rem;">
                    <x-sa.icon name="activity" :size="16" />
                    <span class="sa-table-strong" style="font-size: 0.8125rem;">Telemetri Kesehatan & Storage Infrastruktur</span>
                </div>
                <span class="sa-link" style="font-weight: 600; font-size: 0.8125rem;">Buka &rarr;</span>
            </a>

            <a href="{{ route('superadmin.backup.index') }}" class="sa-list-item sa-list-item--link" style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; background: var(--cv-surface); border: 1px solid var(--cv-line); border-radius: 8px; text-decoration: none; color: var(--cv-ink); transition: all 0.15s ease;">
                <div class="sa-list-item__left" style="display: flex; align-items: center; gap: 0.6rem;">
                    <x-sa.icon name="database" :size="16" />
                    <span class="sa-table-strong" style="font-size: 0.8125rem;">Pusat Cadangan & Pemulihan Basis Data (Backup Hub)</span>
                </div>
                <span class="sa-link" style="font-weight: 600; font-size: 0.8125rem;">Buka &rarr;</span>
            </a>
        </div>

        <div class="sa-grid sa-grid--2 sa-param-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
            <div class="sa-panel sa-panel--sub" style="background: var(--cv-paper); border: 1px solid var(--cv-line); border-radius: 6px; padding: 0.5rem 0.75rem;">
                <span class="sa-table-sub" style="font-family: monospace; font-size: 0.6875rem; display: block;">logbook_maks_mundur</span>
                <strong style="font-size: 0.8125rem;">Maks. {{ $logbookMaksMundurHari }} Hari</strong>
            </div>
            <div class="sa-panel sa-panel--sub" style="background: var(--cv-paper); border: 1px solid var(--cv-line); border-radius: 6px; padding: 0.5rem 0.75rem;">
                <span class="sa-table-sub" style="font-family: monospace; font-size: 0.6875rem; display: block;">masa_tenggang_bulan</span>
                <strong style="font-size: 0.8125rem;">{{ $masaTenggangBulan }} Bulan</strong>
            </div>
            <div class="sa-panel sa-panel--sub" style="background: var(--cv-paper); border: 1px solid var(--cv-line); border-radius: 6px; padding: 0.5rem 0.75rem;">
                <span class="sa-table-sub" style="font-family: monospace; font-size: 0.6875rem; display: block;">berkas_maks_mb</span>
                <strong style="font-size: 0.8125rem;">Maks. {{ $berkasMaksMb }} MB (BR-03)</strong>
            </div>
            <div class="sa-panel sa-panel--sub" style="background: var(--cv-paper); border: 1px solid var(--cv-line); border-radius: 6px; padding: 0.5rem 0.75rem;">
                <span class="sa-table-sub" style="font-family: monospace; font-size: 0.6875rem; display: block;">slot_khusus_default</span>
                <strong style="font-size: 0.8125rem;">{{ $slotKhususDefault }} Kuota (BR-01)</strong>
            </div>
        </div>
    </x-sa.panel>
</div>

{{-- BAGIAN 4: STATUS KESEHATAN SISTEM & INFRASTRUKTUR (TDD Bab 10, NFR-O1) --}}
<x-sa.panel title="Integritas Infrastruktur & Status Layanan (TDD Bab 10)" :href="route('health')" linkText="Buka Healthcheck API (/health) &rarr;">
    <span class="sa-sr-only">Telemetri Infrastruktur &amp; Sistem &bull; Pemantauan integritas database MySQL/InnoDB, storage berkas privat terenkripsi, dan scheduler cron job</span>
    <div class="sa-grid sa-grid--4" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1.25rem;">
        <div class="sa-panel sa-panel--sub sa-infra-item" style="display: flex; align-items: center; gap: 0.75rem; background: var(--cv-surface); border: 1px solid var(--cv-line); border-radius: 8px; padding: 1rem;">
            <div class="sa-infra-icon sa-infra-icon--primary" style="padding: 8px; border-radius: 8px;"><x-sa.icon name="database" :size="18" /></div>
            <div>
                <span class="sa-table-strong" style="display: block; font-size: 0.8125rem;">Koneksi Basis Data</span>
                <span class="sa-table-sub" style="font-size: 0.75rem;">MySQL InnoDB / Normal</span>
            </div>
        </div>

        <div class="sa-panel sa-panel--sub sa-infra-item" style="display: flex; align-items: center; gap: 0.75rem; background: var(--cv-surface); border: 1px solid var(--cv-line); border-radius: 8px; padding: 1rem;">
            <div class="sa-infra-icon sa-infra-icon--success" style="padding: 8px; border-radius: 8px;"><x-sa.icon name="hard-drive" :size="18" /></div>
            <div>
                <span class="sa-table-strong" style="display: block; font-size: 0.8125rem;">Private Disk Storage</span>
                <span class="sa-table-sub" style="font-size: 0.75rem;">Disk Privat Terisolasi</span>
            </div>
        </div>

        <div class="sa-panel sa-panel--sub sa-infra-item" style="display: flex; align-items: center; gap: 0.75rem; background: var(--cv-surface); border: 1px solid var(--cv-line); border-radius: 8px; padding: 1rem;">
            <div class="sa-infra-icon sa-infra-icon--primary" style="padding: 8px; border-radius: 8px;"><x-sa.icon name="clipboard-check" :size="18" /></div>
            <div>
                <span class="sa-table-strong" style="display: block; font-size: 0.8125rem;">Audit Trail Append-Only</span>
                <span class="sa-table-sub" style="font-size: 0.75rem;">{{ number_format($systemHealth['audit_count'] ?? 0) }} Rekaman Terarsip</span>
            </div>
        </div>

        <div class="sa-panel sa-panel--sub sa-infra-item" style="display: flex; align-items: center; gap: 0.75rem; background: var(--cv-surface); border: 1px solid var(--cv-line); border-radius: 8px; padding: 1rem;">
            <div class="sa-infra-icon sa-infra-icon--warning" style="padding: 8px; border-radius: 8px;"><x-sa.icon name="clock" :size="18" /></div>
            <div>
                <span class="sa-table-strong" style="display: block; font-size: 0.8125rem;">Scheduler Nonaktif Harian</span>
                <span class="sa-table-sub" style="font-size: 0.75rem;">Terkonfigurasi di Kernel</span>
            </div>
        </div>
    </div>

    {{-- System Summary Text (as plain informative text per §8 & §11) --}}
    <div class="sa-infra-meta" style="display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center; font-size: 0.75rem; color: var(--cv-ink-3); background: var(--cv-paper); border: 1px solid var(--cv-line); border-radius: 8px; padding: 0.75rem 1rem;">
        <span>Cadangan terakhir: {{ $systemSummary['last_backup'] ?? 'Tercatat' }}</span>
        <span>&bull;</span>
        <span>Kapasitas disk: {{ $systemSummary['disk_free_gb'] ?? 0 }} GB bebas (Terpakai {{ $systemSummary['disk_usage_percent'] ?? 0 }}%)</span>
        <span>&bull;</span>
        <span>Sesi aktif: {{ $systemSummary['active_sessions'] ?? 0 }} akun</span>
        <span>&bull;</span>
        <span>PHP {{ $systemSummary['php_version'] ?? phpversion() }}</span>
    </div>
</x-sa.panel>
</div>
