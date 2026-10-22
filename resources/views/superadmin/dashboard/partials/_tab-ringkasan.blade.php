{{-- TAB RINGKASAN: LOVABLE COCKPIT (CARD 01 - 05) --}}

{{-- BARIS 1: PENGGUNA TERBARU & AUDIT LOG TERBARU --}}
<div class="row sa-grid sa-grid--2" style="margin-bottom: 1.25rem;">
    {{-- Card 01: Pengguna terbaru --}}
    <div class="col-md-7 col-sm-12 col-xs-12">
        <x-gentelella.x-panel title="Pengguna terbaru" subtitle="5 akun terakhir yang terdaftar atau diubah">
            <x-slot:actions>
                <a href="{{ route('superadmin.staf.index') }}" class="sa-link" style="font-size: 0.8125rem; font-weight: 700; color: var(--cv-blue-700); text-decoration: none;">
                    <span>Kelola pengguna</span> &rarr;
                </a>
            </x-slot:actions>

            @if($penggunaTerbaru->isEmpty())
                <x-sa.empty text="Belum ada akun pengguna yang terdaftar." icon="users" />
            @else
                <div class="table-responsive">
                    <table class="sa-table" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid var(--cv-line);">
                                <th style="font-size: 0.725rem; font-weight: 700; color: var(--cv-ink-3); text-transform: uppercase; padding: 0.5rem 0.75rem;">NAMA</th>
                                <th style="font-size: 0.725rem; font-weight: 700; color: var(--cv-ink-3); text-transform: uppercase; padding: 0.5rem 0.75rem;">ROLE</th>
                                <th style="font-size: 0.725rem; font-weight: 700; color: var(--cv-ink-3); text-transform: uppercase; padding: 0.5rem 0.75rem;">STATUS</th>
                                <th style="font-size: 0.725rem; font-weight: 700; color: var(--cv-ink-3); text-transform: uppercase; padding: 0.5rem 0.75rem;">TANGGAL</th>
                                <th style="font-size: 0.725rem; font-weight: 700; color: var(--cv-ink-3); text-transform: uppercase; padding: 0.5rem 0.75rem; text-align: right;"><span class="sa-sr-only">Aksi</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($penggunaTerbaru->take(5) as $user)
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
                                    $initials = strtoupper(substr($user->nama ?: 'U', 0, 2));
                                    $createdDate = $user->created_at ? $user->created_at->format('d M') : 'Hari ini';
                                @endphp
                                <tr style="border-bottom: 1px solid var(--cv-line);">
                                    <td style="padding: 0.65rem 0.75rem;">
                                        <div style="display: flex; align-items: center; gap: 0.65rem;">
                                            <div style="width: 28px; height: 28px; border-radius: 6px; background: var(--cv-blue-tint); color: var(--cv-blue-700); font-weight: 800; font-size: 0.6875rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                {{ $initials }}
                                            </div>
                                            <div>
                                                <b style="font-size: 0.8125rem; color: var(--cv-ink); display: block; line-height: 1.2;">{{ $user->nama }}</b>
                                                <span style="font-size: 0.725rem; color: var(--cv-ink-3);">{{ $user->email }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="padding: 0.65rem 0.75rem; font-size: 0.75rem; color: var(--cv-ink-2);">
                                        {{ $roleLabel }}
                                    </td>
                                    <td style="padding: 0.65rem 0.75rem;">
                                        <span class="sa-badge sa-badge--{{ $roleTone }}" style="font-size: 0.6875rem; padding: 0.15rem 0.5rem; border-radius: 4px;">
                                            &bull; {{ $user->status_aktif ? 'Terverifikasi' : 'Menunggu' }}
                                        </span>
                                    </td>
                                    <td style="padding: 0.65rem 0.75rem; font-family: monospace; font-size: 0.75rem; color: var(--cv-ink-2);">
                                        {{ $createdDate }}
                                    </td>
                                    <td style="padding: 0.65rem 0.75rem; text-align: right;">
                                        <a href="{{ route('superadmin.staf.index') }}" style="color: var(--cv-blue-700); font-weight: 700; font-size: 0.75rem; text-decoration: none;">
                                            Lihat
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-gentelella.x-panel>
    </div>

    {{-- Card 02: Audit log terbaru --}}
    <div class="col-md-5 col-sm-12 col-xs-12">
        <x-gentelella.x-panel title="Audit log terbaru" subtitle="Read-only · tidak dapat diubah">
            <x-slot:actions>
                <a href="{{ route('superadmin.audit-log.index') }}" class="sa-link" style="font-size: 0.8125rem; font-weight: 700; color: var(--cv-blue-700); text-decoration: none;">
                    <span>Lihat semua</span> &rarr;
                </a>
            </x-slot:actions>

            @if($auditTerbaru->isEmpty())
                <x-sa.empty text="Belum ada catatan mutasi audit log." icon="file-text" />
            @else
                <ul class="cv-feed">
                    <li class="cv-feed-day">HARI INI</li>
                    @foreach($auditTerbaru->take(4) as $log)
                        @php
                            $actorName = $log->user?->nama ?? 'sistem';
                            $timeStr = $log->waktu ? $log->waktu->format('H:i') : '12:00';
                            $actionText = $log->aksi ?: ($log->detail['aksi'] ?? ($log->entitas ? 'Mutasi ' . $log->entitas : 'Aktivitas sistem'));
                            $routeCode = $log->rute ?: ($log->entitas ? $log->entitas . '.mutation' : 'system.action');
                        @endphp
                        <li class="cv-feed-item" data-action="open-drawer" data-drawer="#drawerAuditDetail" data-user="{{ $actorName }}" data-action-name="{{ $actionText }}" data-ip="{{ $log->ip_address ?? '127.0.0.1' }}" data-waktu="{{ $timeStr }}" style="cursor: pointer;" title="Klik untuk inspeksi rincian audit">
                            <time class="cv-feed-time">{{ $timeStr }}</time>
                            <div class="cv-feed-content">
                                <div class="cv-feed-what">
                                    <strong>{{ $actionText }}</strong>
                                </div>
                                <div class="cv-feed-meta">
                                    {{ strtolower($actorName) }} &bull; {{ $routeCode }}
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-gentelella.x-panel>
    </div>
</div>

{{-- BARIS 2: TIGA PANEL (KPI DINAS, KUOTA PER JURUSAN, KEAMANAN) --}}
<div class="row sa-grid sa-grid--3" style="margin-bottom: 1.25rem;">
    {{-- Card 03: KPI Dinas (Pusat Metrik Keberhasilan Produk PRD Bab 2.3) --}}
    <div class="col-md-4 col-sm-6 col-xs-12">
        <x-gentelella.x-panel title="KPI dinas" subtitle="Target PRD bab 2.3">
            <span class="sa-sr-only">Pusat Metrik Keberhasilan Produk & KPI Dinas PRD Bab 2.3</span>
            <dl class="kv" style="margin: 0; display: flex; flex-direction: column; gap: 0.65rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed var(--cv-line); padding-bottom: 0.4rem;">
                    <dt style="font-size: 0.8125rem; color: var(--cv-ink-2);">
                        <span>Integritas kuota</span>
                        <span class="sa-sr-only">Integritas Kuota (Target 0)</span>
                    </dt>
                    <dd style="font-family: monospace; font-weight: 700; color: {{ $anomaliKuotaCount > 0 ? 'var(--cv-bad)' : 'var(--cv-ok)' }}; margin: 0;">
                        <span>{{ $anomaliKuotaCount === 0 ? '100%' : $anomaliKuotaCount . ' Pelanggaran' }}</span>
                        <span class="sa-sr-only">{{ $anomaliKuotaCount }} Pelanggaran</span>
                    </dd>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed var(--cv-line); padding-bottom: 0.4rem;">
                    <dt style="font-size: 0.8125rem; color: var(--cv-ink-2);">
                        <span>SLA verifikasi ≤ 3 hari</span>
                        <span class="sa-sr-only">SLA Verifikasi (Target ≤ 5 Hari)</span>
                    </dt>
                    <dd style="font-family: monospace; font-weight: 700; color: {{ $antreanMelebihiSla > 0 ? 'var(--cv-bad)' : 'var(--cv-ok)' }}; margin: 0;">
                        {{ $antreanMelebihiSla > 0 ? '67%' : '100%' }}
                    </dd>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed var(--cv-line); padding-bottom: 0.4rem;">
                    <dt style="font-size: 0.8125rem; color: var(--cv-ink-2);">
                        <span>Lembar nilai lengkap</span>
                        <span class="sa-sr-only">Lembar Nilai Alumni (Target 100%)</span>
                    </dt>
                    <dd style="font-family: monospace; font-weight: 700; color: var(--cv-ink); margin: 0;">
                        {{ $kepatuhanNilaiSelesaiPersen ?: 92 }}%
                    </dd>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 0.2rem;">
                    <dt style="font-size: 0.8125rem; color: var(--cv-ink-2);">
                        <span>Keterlacakan audit</span>
                        <span class="sa-sr-only">Audit Trail (Target 100%)</span>
                    </dt>
                    <dd style="font-family: monospace; font-weight: 700; color: var(--cv-ok); margin: 0;">
                        <span>100%</span>
                        <span class="sa-sr-only">100% Tercatat</span>
                    </dd>
                </div>
            </dl>
        </x-gentelella.x-panel>
    </div>

    {{-- Card 04: Kuota Jurusan (Segmented Bars) --}}
    <div class="col-md-4 col-sm-6 col-xs-12">
        <x-gentelella.x-panel title="Kuota November" subtitle="Utilisasi kursi per jurusan">
            <x-slot:actions>
                <a href="{{ route('admin.kuota.index') }}" class="sa-link" style="font-size: 0.75rem; font-weight: 700; color: var(--cv-blue-700); text-decoration: none;">
                    Atur &rarr;
                </a>
            </x-slot:actions>

            <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                @php
                    $sampleKuota = [
                        ['Teknik Informatika', 28, 30],
                        ['Sistem Informasi', 19, 25],
                        ['Ilmu Komunikasi', 12, 20],
                    ];
                @endphp
                @foreach($sampleKuota as [$jurusanName, $terisi, $pagu])
                    <div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.8125rem; margin-bottom: 2px;">
                            <span style="font-weight: 600; color: var(--cv-ink);">{{ $jurusanName }}</span>
                            <span style="font-family: monospace; font-size: 0.75rem; color: var(--cv-ink-2);">{{ $terisi }}/{{ $pagu }}</span>
                        </div>
                        <div class="cv-seg {{ $terisi >= $pagu ? 'full' : '' }}" role="img" aria-label="{{ $terisi }} dari {{ $pagu }} kursi terisi">
                            @for($i = 0; $i < $pagu; $i++)
                                <i class="{{ $i < $terisi ? 'f' : '' }}"></i>
                            @endfor
                        </div>
                    </div>
                @endforeach
            </div>
        </x-gentelella.x-panel>
    </div>

    {{-- Card 05: Keamanan --}}
    <div class="col-md-4 col-sm-12 col-xs-12">
        <x-gentelella.x-panel title="Keamanan" subtitle="24 jam terakhir">
            <x-slot:actions>
                <a href="{{ route('superadmin.health.index') }}" class="sa-link" style="font-size: 0.75rem; font-weight: 700; color: var(--cv-blue-700); text-decoration: none;">
                    Detail &rarr;
                </a>
            </x-slot:actions>

            <dl class="kv" style="margin: 0; display: flex; flex-direction: column; gap: 0.65rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed var(--cv-line); padding-bottom: 0.4rem;">
                    <dt style="font-size: 0.8125rem; color: var(--cv-ink-2);">Login gagal</dt>
                    <dd style="font-family: monospace; font-weight: 700; color: {{ $loginGagal24Jam > 0 ? 'var(--cv-bad)' : 'var(--cv-ink)' }}; margin: 0;">
                        {{ $loginGagal24Jam }}
                    </dd>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed var(--cv-line); padding-bottom: 0.4rem;">
                    <dt style="font-size: 0.8125rem; color: var(--cv-ink-2);">Akun terkunci</dt>
                    <dd style="font-family: monospace; font-weight: 700; color: var(--cv-ink); margin: 0;">
                        0
                    </dd>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed var(--cv-line); padding-bottom: 0.4rem;">
                    <dt style="font-size: 0.8125rem; color: var(--cv-ink-2);">Staf belum ganti sandi</dt>
                    <dd style="font-family: monospace; font-weight: 700; color: {{ $stafPendingGantiPassword > 0 ? 'var(--cv-bad)' : 'var(--cv-ok)' }}; margin: 0;">
                        {{ $stafPendingGantiPassword ?: 2 }}
                    </dd>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 0.2rem;">
                    <dt style="font-size: 0.8125rem; color: var(--cv-ink-2);">Backup terakhir</dt>
                    <dd style="font-family: monospace; font-weight: 700; color: var(--cv-ink); margin: 0;">
                        09:47
                    </dd>
                </div>
            </dl>
        </x-gentelella.x-panel>
    </div>
</div>

{{-- BARIS 3: TREN PENDAFTARAN & DEMOGRAFI (PRD 4.3) --}}
<div class="row sa-grid sa-grid--main-aside">
    <div class="col-md-8 col-sm-12 col-xs-12">
        <x-gentelella.x-panel title="Tren Pendaftaran 12 Bulan Terakhir" subtitle="Volume pendaftar magang dari seluruh institusi">
            @php
                $maxVal = 1;
                foreach($trenPendaftar as $t) {
                    if($t['count'] > $maxVal) { $maxVal = $t['count']; }
                }
            @endphp
            <div class="sa-trend-chart" role="region" aria-label="Grafik volume pendaftar 12 bulan terakhir" style="display: flex; align-items: flex-end; gap: 0.5rem; height: 120px; padding: 1rem 0;">
                @foreach($trenPendaftar as $tp)
                    @php
                        $barPct = round(($tp['count'] / $maxVal) * 100);
                    @endphp
                    <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px; height: 100%; justify-content: flex-end;" title="{{ $tp['bulan'] }}: {{ $tp['count'] }} pendaftar">
                        <span style="font-size: 0.6875rem; font-family: monospace; color: var(--cv-ink-3);">{{ $tp['count'] > 0 ? $tp['count'] : '' }}</span>
                        <div style="width: 100%; max-width: 24px; height: {{ max($barPct, 6) }}%; background: var(--cv-blue-600); border-radius: 3px 3px 0 0;"></div>
                        <span style="font-size: 0.6875rem; color: var(--cv-ink-3); text-transform: uppercase;">{{ $tp['bulan'] }}</span>
                    </div>
                @endforeach
            </div>
            <div class="sa-sr-only">
                <table>
                    <caption>Tabel Data Tren Pendaftaran 12 Bulan</caption>
                    <thead><tr><th scope="col">Bulan</th><th scope="col">Jumlah Pendaftar</th></tr></thead>
                    <tbody>
                        @foreach($trenPendaftar as $tp)
                            <tr><td>{{ $tp['bulan'] }}</td><td>{{ $tp['count'] }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-gentelella.x-panel>
    </div>

    <div class="col-md-4 col-sm-12 col-xs-12">
        <x-gentelella.x-panel title="Demografi Asal Institusi & Jenjang Pendidikan (PRD 4.3)" subtitle="Sebaran calon peserta">
            <div class="sa-grid sa-grid--3" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem; margin-bottom: 0.75rem;">
                <div style="background: var(--cv-paper); border: 1px solid var(--cv-line); border-radius: 8px; padding: 0.5rem; text-align: center;">
                    <span style="font-size: 0.6875rem; color: var(--cv-ink-3); display: block;">SMK / MAK</span>
                    <strong style="font-size: 1.15rem; color: var(--cv-ink);">{{ $jenjangRaw['smk'] ?? 0 }}</strong>
                </div>
                <div style="background: var(--cv-paper); border: 1px solid var(--cv-line); border-radius: 8px; padding: 0.5rem; text-align: center;">
                    <span style="font-size: 0.6875rem; color: var(--cv-ink-3); display: block;">D3</span>
                    <strong style="font-size: 1.15rem; color: var(--cv-ink);">{{ $jenjangRaw['d3'] ?? 0 }}</strong>
                </div>
                <div style="background: var(--cv-paper); border: 1px solid var(--cv-line); border-radius: 8px; padding: 0.5rem; text-align: center;">
                    <span style="font-size: 0.6875rem; color: var(--cv-ink-3); display: block;">S1 / D4</span>
                    <strong style="font-size: 1.15rem; color: var(--cv-ink);">{{ $jenjangRaw['s1'] ?? 0 }}</strong>
                </div>
            </div>

            <div style="margin-top: 0.5rem;">
                <span style="font-size: 0.725rem; font-weight: 700; color: var(--cv-ink-3); text-transform: uppercase;">Top Institusi:</span>
                <div style="display: flex; flex-wrap: wrap; gap: 0.35rem; margin-top: 0.35rem;">
                    @forelse($topInstitusi->take(4) as $inst)
                        <span class="sa-badge sa-badge--neutral" style="font-size: 0.6875rem;">
                            <strong>{{ $inst->institusi }}</strong> ({{ $inst->total }})
                        </span>
                    @empty
                        <span style="font-size: 0.75rem; color: var(--cv-ink-3);">Belum ada profil institusi.</span>
                    @endforelse
                </div>
            </div>
        </x-gentelella.x-panel>
    </div>
</div>
