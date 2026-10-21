<div class="sa-header-band">
    <div class="sa-header-band__top">
        <div class="sa-header-band__meta">
            <div class="sa-header-band__eyebrow">
                <span class="sa-badge sa-badge--info">
                    Dashboard Eksekutif Tata Kelola MagKom
                </span>
                <span class="sa-badge sa-badge--success">
                    <span class="status-dot-pulse"></span>
                    Sistem Operasional Aktif
                </span>
            </div>
            
            <div class="sa-header-band__info">
                @if($statusPublik)
                    @php
                        $statusTone = match($statusPublik->value ?? $statusPublik) {
                            'tersedia' => 'success',
                            'segera' => 'warning',
                            default => 'danger',
                        };
                        $statusTextLabel = match($statusPublik->value ?? $statusPublik) {
                            'tersedia' => 'Pendaftaran Tersedia',
                            'segera' => 'Segera Dibuka',
                            default => 'Pendaftaran Ditutup',
                        };
                    @endphp
                    <span class="sa-badge sa-badge--{{ $statusTone }}">
                        <x-sa.icon name="circle-check" :size="12" />
                        <span>Status Publik: {{ $statusTextLabel }}</span>
                        @if($isOverrideStatus)
                            <span class="sa-badge-sub">(Override SuperAdmin)</span>
                        @else
                            <span class="sa-badge-sub">(Jadwal Otomatis)</span>
                        @endif
                    </span>
                @endif

                @if($bulanAktif && $bulanAktif->tgl_buka && $bulanAktif->tgl_tutup)
                    @php
                        $tglBukaFormatted = \Carbon\Carbon::parse($bulanAktif->tgl_buka)->format('d M');
                        $tglTutupFormatted = \Carbon\Carbon::parse($bulanAktif->tgl_tutup)->format('d M Y');
                    @endphp
                    <span class="sa-badge sa-badge--neutral">
                        <x-sa.icon name="calendar" :size="12" />
                        <span>Jadwal: {{ $tglBukaFormatted }} – {{ $tglTutupFormatted }}</span>
                    </span>
                @endif

                <span class="sa-badge sa-badge--{{ $loginGagal24Jam > 0 ? 'warning' : 'success' }}">
                    <x-sa.icon :name="$loginGagal24Jam > 0 ? 'triangle-alert' : 'shield-check'" :size="12" />
                    <span>Login gagal 24 jam: {{ $loginGagal24Jam }}</span>
                </span>
            </div>
        </div>

        <div class="sa-header-band__actions">
            <a href="{{ route('superadmin.staf.index') }}" class="btn-primary">
                <x-sa.icon name="users" :size="16" />
                <span>Kelola Pengguna</span>
            </a>
            <a href="{{ route('superadmin.audit-log.index') }}" class="btn-secondary">
                <x-sa.icon name="file-text" :size="16" />
                <span>Audit Trail</span>
            </a>

            <form method="GET" action="{{ route('superadmin.dashboard') }}" class="sa-period-form">
                <label for="select-bulan" class="sa-sr-only">Pilih Bulan Magang:</label>
                <select id="select-bulan" name="bulan_id" data-auto-submit class="sa-select">
                    @foreach($daftarBulan as $b)
                        <option value="{{ $b->id }}" {{ $bulanAktif && $bulanAktif->id === $b->id ? 'selected' : '' }}>
                            {{ $b->nama_bulan }} {{ $b->override_status ? '[' . strtoupper($b->override_status->value) . ']' : '' }}
                        </option>
                    @endforeach
                </select>
                <noscript>
                    <button type="submit" class="sa-sr-only">Terapkan</button>
                </noscript>
            </form>
        </div>
    </div>

    <!-- Quick Actions Toolbar -->
    <div class="sa-shortcuts">
        <div class="sa-shortcuts__list">
            <span class="sa-shortcuts__label">
                Pintasan Tindakan:
            </span>
            <a href="{{ route('superadmin.staf.index') }}" class="sa-shortcut-chip">
                <x-sa.icon name="user-plus" :size="14" />
                <span>+ Akun Staf</span>
            </a>
            <a href="{{ route('superadmin.bulan.index') }}" class="sa-shortcut-chip">
                <x-sa.icon name="calendar" :size="14" />
                <span>+ Bulan Magang</span>
            </a>
            <a href="{{ route('admin.pendaftaran.index') }}" class="sa-shortcut-chip">
                <x-sa.icon name="clipboard-check" :size="14" />
                <span>Tinjau Berkas</span>
            </a>
            <a href="{{ route('admin.penempatan.index') }}" class="sa-shortcut-chip">
                <x-sa.icon name="clock" :size="14" />
                <span>Penempatan Peserta</span>
            </a>
            <a href="{{ route('admin.laporan.index') }}" class="sa-shortcut-chip">
                <x-sa.icon name="download" :size="14" />
                <span>Ekspor Laporan</span>
            </a>
            <a href="{{ route('superadmin.health.index') }}" class="sa-shortcut-chip">
                <x-sa.icon name="activity" :size="14" />
                <span>Telemetri Sistem</span>
            </a>
            <a href="{{ route('superadmin.backup.index') }}" class="sa-shortcut-chip">
                <x-sa.icon name="database" :size="14" />
                <span>Backup Hub</span>
            </a>
        </div>
    </div>
</div>
