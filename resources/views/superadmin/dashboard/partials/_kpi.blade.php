<div class="sa-grid-kpi">
    <x-sa.stat-card label="Total pengguna" :value="$totalPengguna" tone="blue" icon="users"
        hint="Seluruh akun terdaftar"
        :meta="$totalAdmin.' Admin · '.$totalPembimbing.' Pembimbing aktif'.($stafPendingGantiPassword > 0 ? ' · '.$stafPendingGantiPassword.' Staf Pending Sandi Awal' : '')" />
    <x-sa.stat-card label="Menunggu verifikasi" :value="$menungguVerifikasi" tone="amber" icon="inbox"
        hint="Berkas perlu ditinjau" />
    <x-sa.stat-card label="Peserta aktif" :value="$pesertaAktif" tone="green" icon="clipboard-check"
        :hint="$totalPesertaJalurUmum.' Jalur Umum · '.$totalPesertaJalurKhusus.' Jalur Khusus'"
        :meta="$totalAlumni.' Alumni Selesai'.($totalPesertaMasaTenggang > 0 ? ' ('.$totalPesertaMasaTenggang.' Masa Tenggang)' : '').' · '.$totalPesertaMundur.' Mengundurkan Diri'" />
    <x-sa.stat-card label="Akun nonaktif" :value="$akunNonaktif" tone="red" icon="lock"
        hint="Akses ditangguhkan" />
</div>
