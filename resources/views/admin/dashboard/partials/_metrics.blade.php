{{-- ==========================================================================
   Four-Metric Dock & Operational Efficiency - Dashboard Admin (MagKom Sleman)
   ========================================================================== --}}
<div class="row tile_count adm-metrics-dock-grid" role="region" aria-label="{{ __('Metrik Operasional Administrasi') }}">
    {{-- Tile 1: Verifikasi Tertunda / Perlu Verifikasi Segera --}}
    <x-gentelella.tile-stat
        :title="__('Verifikasi Tertunda')"
        :value="number_format($pengajuanMenunggu)"
        icon="clipboard-check"
        color="yellow"
        trendType="{{ $pengajuanSlaKritisCount > 0 ? 'warning' : 'success' }}"
        :trendDirection="$pengajuanSlaKritisCount > 0 ? 'down' : 'up'"
        :trendText="$pengajuanSlaKritisCount > 0 ? $pengajuanSlaKritisCount . ' ' . __('Kritis') : __('Tertib')"
        :subText="$pengajuanSlaKritisCount > 0 ? $pengajuanSlaKritisCount . ' ' . __('lewat batas waktu · Perlu Verifikasi Segera') : __('Tertib · Perlu Verifikasi Segera')"
        :sparkBars="[75, 80, 70, 85, 90, 65, 60]"
        col="col-md-3 col-sm-6 col-xs-12 adm-metric-card-col"
    />

    {{-- Tile 2: Kapasitas Kuota Terisi / Keterisian Kuota Magang Periode --}}
    <x-gentelella.tile-stat
        :title="__('Kapasitas Kuota Terisi')"
        :value="$realisasiKuotaPersen . '%'"
        icon="calendar"
        color="blue"
        trendType="info"
        trendDirection="up"
        :trendText="__('Okupansi')"
        :subText="$totalKuotaTerisi . '/' . $totalKuotaUmum . ' ' . __('Kursi · Keterisian Kuota Magang Periode')"
        :sparkBars="[40, 50, 60, 68, 75, 82, 90]"
        col="col-md-3 col-sm-6 col-xs-12 adm-metric-card-col"
    />

    {{-- Tile 3: Pendaftaran Diterima --}}
    <x-gentelella.tile-stat
        :title="__('Pendaftaran Diterima')"
        :value="number_format($pendaftaranDiterimaCount ?? 0)"
        icon="check-circle"
        color="teal"
        trendType="success"
        trendDirection="up"
        :trendText="__('Diterima')"
        :subText="__('Telah lolos seleksi berkas')"
        :sparkBars="[30, 45, 40, 55, 50, 65, 70]"
        col="col-md-3 col-sm-6 col-xs-12 adm-metric-card-col"
    />

    {{-- Tile 4: Peserta Magang Aktif --}}
    <x-gentelella.tile-stat
        :title="__('Peserta Magang Aktif')"
        :value="number_format($pesertaAktifCount)"
        icon="user-check"
        color="teal"
        trendType="success"
        trendDirection="up"
        :trendText="__('Aktif')"
        :subText="__('Sedang bertugas di dinas')"
        :sparkBars="[35, 55, 45, 65, 70, 85, 100]"
        col="col-md-3 col-sm-6 col-xs-12 adm-metric-card-col"
    />
</div>
