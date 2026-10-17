<header class="sa-header">
    <div>
        <div class="sa-header__meta sa-header__meta--flush">
            <x-sa.badge tone="info">Dashboard Eksekutif Tata Kelola MagKom</x-sa.badge>
            <span class="sa-live"><span class="status-dot-pulse"></span> Sistem Operasional Aktif</span>
        </div>
    </div>

    <div class="sa-header__actions">
        <form method="GET" action="{{ route('superadmin.dashboard') }}" class="sa-period">
            <label for="select-bulan">Periode</label>
            <select id="select-bulan" name="bulan_id" data-autosubmit>
                @foreach($daftarBulan as $b)
                    <option value="{{ $b->id }}" @selected($bulanAktif && $bulanAktif->id === $b->id)>
                        {{ $b->bulan_mulai?->translatedFormat('F Y') }} {{ $b->override_status ? '['.strtoupper($b->override_status->value).']' : '' }}
                    </option>
                @endforeach
            </select>
            <noscript><button type="submit" class="sa-btn sa-btn--secondary">Terapkan</button></noscript>
        </form>
        <a href="{{ route('superadmin.staf.index') }}" class="sa-btn sa-btn--primary">
            <x-sa.icon name="user-plus" :size="16" /> <span>Kelola Pengguna</span>
        </a>
        <a href="{{ route('superadmin.audit-log.index') }}" class="sa-btn sa-btn--secondary">
            <x-sa.icon name="file-text" :size="16" /> <span>Audit Trail</span>
        </a>
    </div>
</header>

<nav class="sa-shortcuts" aria-label="Pintasan tindakan">
    <span class="sa-shortcuts__label">Pintasan Tindakan:</span>
    <a href="{{ route('superadmin.staf.index') }}" class="quick-action-chip">+ Akun Staf</a>
    <a href="{{ route('superadmin.bulan.index') }}" class="quick-action-chip">+ Periode Magang</a>
    <a href="{{ route('admin.pendaftaran.index') }}" class="quick-action-chip">Tinjau Berkas</a>
    <a href="{{ route('admin.penempatan.index') }}" class="quick-action-chip">Penempatan Peserta</a>
    <a href="{{ route('admin.laporan.index') }}" class="quick-action-chip">Ekspor Laporan</a>
    <a href="{{ route('superadmin.health.index') }}" class="quick-action-chip">Telemetri Sistem</a>
    <a href="{{ route('superadmin.backup.index') }}" class="quick-action-chip">Backup Hub</a>
</nav>
