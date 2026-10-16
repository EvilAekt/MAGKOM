<div class="sa-grid sa-grid--metrics">
    {{-- Card 1: Total Pengguna --}}
    <x-sa.metric 
        label="Total pengguna" 
        :value="number_format($totalPengguna)" 
        icon="users"
        tone="primary">
        <span>Seluruh akun terdaftar</span>
        @if($stafPendingGantiPassword > 0)
            <span> · {{ $stafPendingGantiPassword }} Staf Pending Sandi Awal</span>
        @endif
    </x-sa.metric>

    {{-- Card 2: Menunggu Verifikasi --}}
    <x-sa.metric 
        label="Menunggu verifikasi" 
        :value="number_format($menungguVerifikasi)" 
        icon="clipboard-check"
        tone="warning">
        <span>Berkas perlu ditinjau</span>
    </x-sa.metric>

    {{-- Card 3: Peserta Aktif --}}
    <x-sa.metric 
        label="Peserta aktif" 
        :value="number_format($pesertaAktif)" 
        icon="user-check"
        tone="success">
        <div>{{ $totalPesertaJalurUmum }} Jalur Umum · {{ $totalPesertaJalurKhusus }} Jalur Khusus</div>
        <div>
            {{ $totalAlumni }} Alumni Selesai
            @if($totalPesertaMasaTenggang > 0)
                ({{ $totalPesertaMasaTenggang }} Masa Tenggang)
            @endif
            · {{ $totalPesertaMundur }} Mengundurkan Diri
        </div>
    </x-sa.metric>

    {{-- Card 4: Akun Nonaktif --}}
    <x-sa.metric 
        label="Akun nonaktif" 
        :value="number_format($akunNonaktif)" 
        icon="lock"
        tone="danger">
        <span>Akses ditangguhkan</span>
    </x-sa.metric>
</div>
