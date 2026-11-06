{{-- ==========================================================================
   Four-Metric Dock & Operational Efficiency - Dashboard Admin (MagKom Sleman)
   ========================================================================== --}}
<div class="row tile_count adm-metrics-dock-grid" role="region" aria-label="Metrik Operasional Administrasi">
    {{-- Tile 1: Verifikasi Tertunda / Perlu Verifikasi Segera --}}
    <x-gentelella.tile-stat
        title="Verifikasi Tertunda"
        :value="number_format($pengajuanMenunggu)"
        icon="clipboard-check"
        color="yellow"
        trendType="{{ $pengajuanSlaKritisCount > 0 ? 'warning' : 'success' }}"
        :trendDirection="$pengajuanSlaKritisCount > 0 ? 'down' : 'up'"
        :trendText="$pengajuanSlaKritisCount > 0 ? $pengajuanSlaKritisCount . ' SLA' : 'Tertib'"
        subText="{{ $pengajuanSlaKritisCount > 0 ? $pengajuanSlaKritisCount . ' lewat SLA · Perlu Verifikasi Segera' : 'Tertib · Perlu Verifikasi Segera' }}"
        :sparkBars="[75, 80, 70, 85, 90, 65, 60]"
        col="col-md-3 col-sm-6 col-xs-12 adm-metric-card-col"
    />

    {{-- Tile 2: Kapasitas Kuota Terisi / Keterisian Kuota Magang Periode --}}
    <x-gentelella.tile-stat
        title="Kapasitas Kuota Terisi"
        :value="$realisasiKuotaPersen . '%'"
        icon="calendar"
        color="blue"
        trendType="info"
        trendDirection="up"
        trendText="Okupansi"
        subText="{{ $totalKuotaTerisi }}/{{ $totalKuotaUmum }} Kursi · Keterisian Kuota Magang Periode"
        :sparkBars="[40, 50, 60, 68, 75, 82, 90]"
        col="col-md-3 col-sm-6 col-xs-12 adm-metric-card-col"
    />

    {{-- Tile 3: Pendaftaran Diterima --}}
    <x-gentelella.tile-stat
        title="Pendaftaran Diterima"
        :value="number_format($pendaftaranDiterimaCount ?? 0)"
        icon="check-circle"
        color="teal"
        trendType="success"
        trendDirection="up"
        trendText="Diterima"
        subText="Telah lolos seleksi berkas"
        :sparkBars="[30, 45, 40, 55, 50, 65, 70]"
        col="col-md-3 col-sm-6 col-xs-12 adm-metric-card-col"
    />

    {{-- Tile 4: Peserta Magang Aktif --}}
    <x-gentelella.tile-stat
        title="Peserta Magang Aktif"
        :value="number_format($pesertaAktifCount)"
        icon="user-check"
        color="teal"
        trendType="success"
        trendDirection="up"
        trendText="Aktif"
        subText="Sedang bertugas di dinas"
        :sparkBars="[35, 55, 45, 65, 70, 85, 100]"
        col="col-md-3 col-sm-6 col-xs-12 adm-metric-card-col"
    />
</div>
