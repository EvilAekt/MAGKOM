<div class="row tile_count sa-grid sa-grid--metrics" role="region" aria-label="Metrik Utama SuperAdmin">
    {{-- Card 1: Total Pengguna --}}
    <x-gentelella.tile-stat 
        title="Total pengguna" 
        :value="number_format($totalPengguna)" 
        icon="users"
        trendType="primary"
        col="col-md-3 col-sm-6 col-xs-12"
    >
        <span>Seluruh akun terdaftar</span>
        @if($stafPendingGantiPassword > 0)
            <span> &bull; {{ $stafPendingGantiPassword }} Staf Pending Sandi Awal</span>
        @endif
    </x-gentelella.tile-stat>

    {{-- Card 2: Menunggu Verifikasi --}}
    <x-gentelella.tile-stat 
        title="Menunggu verifikasi" 
        :value="number_format($menungguVerifikasi)" 
        icon="clipboard-check"
        trendType="warning"
        col="col-md-3 col-sm-6 col-xs-12"
    >
        <span>Berkas perlu ditinjau</span>
    </x-gentelella.tile-stat>

    {{-- Card 3: Peserta Aktif --}}
    <x-gentelella.tile-stat 
        title="Peserta aktif" 
        :value="number_format($pesertaAktif)" 
        icon="user-check"
        trendType="success"
        col="col-md-3 col-sm-6 col-xs-12"
    >
        <div>{{ $totalPesertaJalurUmum }} Jalur Umum &bull; {{ $totalPesertaJalurKhusus }} Jalur Khusus</div>
        <div>
            {{ $totalAlumni }} Alumni Selesai
            @if($totalPesertaMasaTenggang > 0)
                ({{ $totalPesertaMasaTenggang }} Masa Tenggang)
            @endif
            &bull; {{ $totalPesertaMundur }} Mengundurkan Diri
        </div>
    </x-gentelella.tile-stat>

    {{-- Card 4: Akun Nonaktif --}}
    <x-gentelella.tile-stat 
        title="Akun nonaktif" 
        :value="number_format($akunNonaktif)" 
        icon="lock"
        trendType="danger"
        col="col-md-3 col-sm-6 col-xs-12"
    >
        <span>Akses ditangguhkan</span>
    </x-gentelella.tile-stat>
</div>

