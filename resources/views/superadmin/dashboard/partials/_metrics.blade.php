<div class="row tile_count sa-hero-dock" role="region" aria-label="Metrik Utama SuperAdmin">
    {{-- Card 1: Total Pengguna --}}
    <x-gentelella.tile-stat 
        title="Total pengguna" 
        :value="number_format($totalPengguna)" 
        icon="users"
        trendType="primary"
        trendText="+14"
        subText="minggu ini"
        col="col-md-3 col-sm-6 col-xs-12 sa-dock-metric"
    >
        <span class="sa-spark" aria-hidden="true">
            <i style="height: 40%"></i>
            <i style="height: 55%"></i>
            <i style="height: 50%"></i>
            <i style="height: 65%"></i>
            <i style="height: 70%"></i>
            <i style="height: 85%"></i>
            <i style="height: 100%"></i>
        </span>
        <span class="sa-sr-only">Seluruh akun terdaftar @if($stafPendingGantiPassword > 0) &bull; {{ $stafPendingGantiPassword }} Staf Pending Sandi Awal @endif</span>
    </x-gentelella.tile-stat>

    {{-- Card 2: Menunggu Verifikasi --}}
    <x-gentelella.tile-stat 
        title="Menunggu verifikasi" 
        :value="number_format($menungguVerifikasi)" 
        icon="clipboard-check"
        trendType="warning"
        trendText="{{ $antreanMelebihiSla > 0 ? $antreanMelebihiSla . ' lewat SLA' : 'Tertib SLA' }}"
        subText="· FIFO"
        col="col-md-3 col-sm-6 col-xs-12 sa-dock-metric warn"
    >
        <span class="sa-sr-only">Berkas perlu ditinjau</span>
    </x-gentelella.tile-stat>

    {{-- Card 3: Peserta Aktif --}}
    <x-gentelella.tile-stat 
        title="Peserta aktif" 
        :value="number_format($pesertaAktif)" 
        icon="user-check"
        trendType="success"
        subText="7 masa tenggang · 9 selesai ≤ 14 hari"
        col="col-md-3 col-sm-6 col-xs-12 sa-dock-metric"
    >
        <span class="sa-spark" aria-hidden="true">
            <i style="height: 60%"></i>
            <i style="height: 65%"></i>
            <i style="height: 72%"></i>
            <i style="height: 70%"></i>
            <i style="height: 78%"></i>
            <i style="height: 84%"></i>
            <i style="height: 90%"></i>
        </span>
        <div class="sa-sr-only">
            <div>{{ $totalPesertaJalurUmum }} Jalur Umum &bull; {{ $totalPesertaJalurKhusus }} Jalur Khusus</div>
            <div>
                {{ $totalAlumni }} Alumni Selesai
                @if($totalPesertaMasaTenggang > 0)
                    ({{ $totalPesertaMasaTenggang }} Masa Tenggang)
                @endif
                &bull; {{ $totalPesertaMundur }} Mengundurkan Diri
            </div>
        </div>
    </x-gentelella.tile-stat>

    {{-- Card 4: Akun Nonaktif --}}
    <x-gentelella.tile-stat 
        title="Akun nonaktif" 
        :value="number_format($akunNonaktif)" 
        icon="lock"
        trendType="danger"
        subText="{{ $stafPendingGantiPassword > 0 ? $stafPendingGantiPassword . ' staf belum ganti sandi' : 'Akses ditangguhkan' }}"
        col="col-md-3 col-sm-6 col-xs-12 sa-dock-metric bad"
    >
        <span class="sa-sr-only">Akses ditangguhkan</span>
    </x-gentelella.tile-stat>
</div>
