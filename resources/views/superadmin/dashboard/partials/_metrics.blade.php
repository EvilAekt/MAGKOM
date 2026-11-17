@php
    /** @var int $totalPengguna */
    /** @var int $stafPendingGantiPassword */
    /** @var int $menungguVerifikasi */
    /** @var int $antreanMelebihiSla */
    /** @var int $pesertaAktif */
    /** @var int $totalPesertaJalurUmum */
    /** @var int $totalPesertaJalurKhusus */
    /** @var int $totalAlumni */
    /** @var int $totalPesertaMasaTenggang */
    /** @var int $totalPesertaMundur */
    /** @var int $akunNonaktif */
@endphp
<div class="row tile_count sa-metrics-dock-grid" role="region" aria-label="{{ __('Metrik Utama SuperAdmin') }}">
    {{-- Card 1: Total Pengguna --}}
    <x-gentelella.tile-stat 
        :title="__('Total pengguna')" 
        :value="number_format($totalPengguna)" 
        icon="users"
        color="teal"
        trendDirection="up"
        trendText="+14%"
        :subText="__('+14 minggu ini')"
        :sparkBars="[35, 55, 45, 65, 70, 85, 100]"
        col="col-md-3 col-sm-6 col-xs-12 sa-metric-card-col"
    >
        <span class="sa-sr-only">{{ __('Seluruh akun terdaftar') }} @if($stafPendingGantiPassword > 0) &bull; {{ $stafPendingGantiPassword }} {{ __('Staf Pending Sandi Awal') }} @endif</span>
    </x-gentelella.tile-stat>

    {{-- Card 2: Menunggu Verifikasi --}}
    <x-gentelella.tile-stat 
        :title="__('Menunggu verifikasi')" 
        :value="number_format($menungguVerifikasi)" 
        icon="clipboard-check"
        color="yellow"
        trendType="warning"
        :trendDirection="$antreanMelebihiSla > 0 ? 'down' : 'up'"
        :trendText="$antreanMelebihiSla > 0 ? $antreanMelebihiSla . ' SLA' : __('Tertib')"
        :subText="__('3 lewat SLA')"
        :sparkBars="[75, 80, 70, 85, 90, 65, 60]"
        col="col-md-3 col-sm-6 col-xs-12 sa-metric-card-col warn"
    >
        <span class="sa-sr-only">{{ __('Berkas perlu ditinjau FIFO') }}</span>
    </x-gentelella.tile-stat>

    {{-- Card 3: Peserta Aktif --}}
    <x-gentelella.tile-stat 
        :title="__('Peserta aktif')" 
        :value="number_format($pesertaAktif)" 
        icon="user-check"
        color="blue"
        trendDirection="up"
        :trendText="__('Aktif')"
        :subText="__('7 masa tenggang · 9 selesai ≤ 14 hari')"
        :sparkBars="[40, 50, 60, 68, 75, 82, 90]"
        col="col-md-3 col-sm-6 col-xs-12 sa-metric-card-col"
    >
        <div class="sa-sr-only">
            <div>{{ $totalPesertaJalurUmum }} {{ __('Jalur Umum') }} &bull; {{ $totalPesertaJalurKhusus }} {{ __('Jalur Khusus') }}</div>
            <div>
                {{ $totalAlumni }} {{ __('Alumni Selesai') }}
                @if($totalPesertaMasaTenggang > 0)
                    ({{ $totalPesertaMasaTenggang }} {{ __('Masa Tenggang') }})
                @endif
                &bull; {{ $totalPesertaMundur }} {{ __('Mengundurkan Diri') }}
            </div>
        </div>
    </x-gentelella.tile-stat>

    {{-- Card 4: Akun Nonaktif --}}
    <x-gentelella.tile-stat 
        :title="__('Akun nonaktif')" 
        :value="number_format($akunNonaktif)" 
        icon="lock"
        color="red"
        trendType="danger"
        trendDirection="down"
        :trendText="__('Nonaktif')"
        :subText="$stafPendingGantiPassword > 0 ? $stafPendingGantiPassword . ' ' . __('staf belum ganti sandi') : __('Akses ditangguhkan')"
        :sparkBars="[30, 25, 40, 20, 35, 15, 10]"
        col="col-md-3 col-sm-6 col-xs-12 sa-metric-card-col bad"
    >
        <span class="sa-sr-only">{{ __('Akses ditangguhkan') }}</span>
    </x-gentelella.tile-stat>
</div>

