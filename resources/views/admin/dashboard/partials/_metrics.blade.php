{{-- ==========================================================================
   Four-Metric Dock & Operational Efficiency - Dashboard Admin (MagKom Sleman)
   ========================================================================== --}}
<div class="row tile_count" role="region" aria-label="Metrik Operasional Administrasi">
    {{-- Tile 1: Verifikasi Tertunda / Perlu Verifikasi Segera --}}
    <x-gentelella.tile-stat
        title="Verifikasi Tertunda"
        :value="number_format($pengajuanMenunggu)"
        icon="clipboard-check"
        trendType="{{ $pengajuanSlaKritisCount > 0 ? 'danger' : 'warning' }}"
        col="col-md-3 col-sm-6 col-xs-12"
    >
        @if($pengajuanSlaKritisCount > 0)
            <span style="color: #fca5a5; font-weight: 700;">⚠️ {{ $pengajuanSlaKritisCount }} Perlu Verifikasi Segera</span>
        @else
            <span>Perlu Verifikasi Segera</span>
        @endif
    </x-gentelella.tile-stat>

    {{-- Tile 2: Kapasitas Kuota Terisi / Keterisian Kuota Magang Periode --}}
    <x-gentelella.tile-stat
        title="Kapasitas Kuota Terisi"
        :value="$realisasiKuotaPersen . '%'"
        icon="calendar"
        trendType="primary"
        col="col-md-3 col-sm-6 col-xs-12"
    >
        <span>{{ $totalKuotaTerisi }}/{{ $totalKuotaUmum }} Kursi &bull; Keterisian Kuota Magang Periode</span>
    </x-gentelella.tile-stat>

    {{-- Tile 3: Antrean Waiting List --}}
    <x-gentelella.tile-stat
        title="Antrean Waiting List"
        :value="number_format($waitingListCount)"
        icon="clock"
        trendType="info"
        col="col-md-3 col-sm-6 col-xs-12"
    >
        <span>{{ $perluPerbaikan }} Revisi Dokumen &bull; Daftar tunggu antrean</span>
    </x-gentelella.tile-stat>

    {{-- Tile 4: Peserta Magang Aktif --}}
    <x-gentelella.tile-stat
        title="Peserta Magang Aktif"
        :value="number_format($pesertaAktifCount)"
        icon="user-check"
        trendType="success"
        col="col-md-3 col-sm-6 col-xs-12"
    >
        <span>Sedang bertugas di dinas</span>
    </x-gentelella.tile-stat>
</div>
