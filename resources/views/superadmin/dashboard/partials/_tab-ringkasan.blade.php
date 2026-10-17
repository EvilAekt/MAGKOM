<div id="tab-ringkasan" class="tab-content-panel active" role="tabpanel" aria-labelledby="btn-tab-ringkasan">
    <div class="sa-page">
        <div class="sa-grid-2">
            <x-sa.panel title="Pengguna terbaru" subtitle="Pendaftaran akun dan staf internal terkini" :href="route('superadmin.staf.index')" link-label="Kelola pengguna">
                @forelse($penggunaTerbaru as $user)
                    @php
                        $roleVal = $user->role?->value ?? 'peserta';
                        $roleTone = match($roleVal) { 'superadmin' => 'info', 'admin' => 'info', 'pembimbing' => 'warning', default => 'success' };
                        $roleLabel = match($roleVal) { 'superadmin' => 'SuperAdmin', 'admin' => 'Administrasi', 'pembimbing' => 'Pembimbing', default => 'Peserta' };
                    @endphp
                    @if($loop->first)<ul class="sa-list">@endif
                    <li class="sa-list__item">
                        <x-sa.avatar :name="$user->nama" />
                        <div class="sa-list__body">
                            <p class="sa-list__title">{{ $user->nama }}</p>
                            <p class="sa-list__sub">{{ $user->email }}</p>
                        </div>
                        <x-sa.badge :tone="$roleTone">{{ $roleLabel }}</x-sa.badge>
                    </li>
                    @if($loop->last)</ul>@endif
                @empty
                    <x-sa.empty-state icon="users" text="Belum ada akun pengguna yang terdaftar." />
                @endforelse
            </x-sa.panel>

            <x-sa.panel title="Audit log terbaru" subtitle="Rekam jejak mutasi data dan aksi terotorisasi" :href="route('superadmin.audit-log.index')" link-label="Lihat semua">
                @forelse($auditTerbaru as $log)
                    @php
                        $actorName = $log->user?->nama ?? 'Sistem';
                        $waktu = $log->waktu ? $log->waktu->format('d M H:i') : '-';
                        $actionText = $log->aksi ?: ($log->detail['aksi'] ?? ($log->entitas ? 'Mutasi '.$log->entitas : 'Aktivitas sistem'));
                    @endphp
                    @if($loop->first)<ul class="sa-list">@endif
                    <li class="sa-list__item">
                        <span class="sa-dot sa-dot--info"><x-sa.icon name="file-text" :size="16" /></span>
                        <div class="sa-list__body">
                            <p class="sa-list__title">{{ $actionText }}</p>
                            <p class="sa-list__sub">{{ $actorName }}</p>
                        </div>
                        <span class="sa-list__time">{{ $waktu }}</span>
                    </li>
                    @if($loop->last)</ul>@endif
                @empty
                    <x-sa.empty-state icon="file-text" text="Belum ada catatan mutasi audit log." />
                @endforelse
            </x-sa.panel>
        </div>

        <x-sa.panel :title="new \Illuminate\Support\HtmlString('Pusat Metrik Keberhasilan Produk & KPI Dinas')" subtitle="Target mutu layanan dan kepatuhan operasional Diskominfo Sleman (PRD Bab 2.3)">
            <div class="sa-grid-4">
                <x-sa.stat-card class="sa-stat--mini" label="Integritas Kuota (Target 0)"
                    :value="$anomaliKuotaCount.' Pelanggaran'" :tone="$anomaliKuotaCount > 0 ? 'red' : 'green'" icon="shield"
                    :hint="$anomaliKuotaCount === 0 ? '100% Bebas Kuota Minus / Bocor' : 'Terdeteksi kuota melebihi batas!'" />
                <x-sa.stat-card class="sa-stat--mini" label="SLA Verifikasi (Target ≤ 5 Hari)"
                    :value="$rataRataSlaVerifikasiHari !== null ? $rataRataSlaVerifikasiHari.' Hari' : '- Hari'"
                    :tone="$antreanMelebihiSla > 0 ? 'amber' : 'blue'" icon="clock"
                    :hint="$antreanMelebihiSla > 0 ? $antreanMelebihiSla.' Berkas Melewati Batas 5 Hari' : 'Kecepatan verifikasi sesuai target SLA'" />
                <x-sa.stat-card class="sa-stat--mini" label="Lembar Nilai Alumni (Target 100%)"
                    :value="$kepatuhanNilaiSelesaiPersen.'%'" tone="blue" icon="clipboard-check"
                    :hint="$penempatanSelesaiDenganNilai.' dari '.$penempatanSelesaiTotal.' Alumni Selesai Nilai Final'" />
                <x-sa.stat-card class="sa-stat--mini" label="Audit Trail (Target 100%)"
                    value="100% Tercatat" tone="green" icon="activity"
                    :hint="number_format($securityPosture['total_audit_events']).' Event Terekam & Tersanitasi'" />
            </div>
        </x-sa.panel>

        <div class="sa-grid-2">
            <x-sa.panel title="Status landing page" subtitle="Periode terpilih (PRD F-01, BR-04)">
                @if($bulanAktif)
                    @php
                        $statusVal = $statusPublik->value ?? $statusPublik;
                        $statusTone = match($statusVal) { 'tersedia' => 'success', 'segera' => 'warning', default => 'danger' };
                        $statusLabel = match($statusVal) { 'tersedia' => 'Pendaftaran Tersedia', 'segera' => 'Segera Dibuka', default => 'Pendaftaran Ditutup' };
                    @endphp
                    <ul class="sa-list">
                        <li class="sa-list__item">
                            <div class="sa-list__body"><p class="sa-list__sub">Periode</p><p class="sa-list__title">{{ $bulanAktif->nama_bulan }}</p></div>
                        </li>
                        <li class="sa-list__item">
                            <div class="sa-list__body"><p class="sa-list__sub">Status Publik:</p></div>
                            <x-sa.badge :tone="$statusTone">{{ $statusLabel }}</x-sa.badge>
                            <x-sa.badge>{{ $isOverrideStatus ? 'Override SuperAdmin' : 'Jadwal Otomatis' }}</x-sa.badge>
                        </li>
                        @if($bulanAktif->tgl_buka && $bulanAktif->tgl_tutup)
                            <li class="sa-list__item">
                                <div class="sa-list__body"><p class="sa-list__sub">Jadwal pendaftaran</p></div>
                                <p class="sa-list__title">{{ \Carbon\Carbon::parse($bulanAktif->tgl_buka)->format('d M') }} – {{ \Carbon\Carbon::parse($bulanAktif->tgl_tutup)->format('d M Y') }}</p>
                            </li>
                        @endif
                        <li class="sa-list__item">
                            <div class="sa-list__body"><p class="sa-list__sub">Keamanan</p></div>
                            <x-sa.badge tone="success">Prima</x-sa.badge>
                            @if($loginGagal24Jam > 0)<x-sa.badge tone="danger">{{ $loginGagal24Jam }} Gagal</x-sa.badge>@endif
                        </li>
                    </ul>
                @else
                    <x-sa.empty-state icon="clock" text="Belum ada periode bulan magang aktif." />
                @endif
            </x-sa.panel>

            <x-sa.panel title="Demografi Asal Institusi & Jenjang (PRD 4.3)" subtitle="Sebaran peserta berdasarkan jenjang pendidikan" :href="route('admin.laporan.pendaftaran')" link-label="Laporan lengkap">
                @php
                    $jenjang = ['SMK / MAK' => $jenjangRaw['smk'] ?? 0, 'Diploma 3 (D3)' => $jenjangRaw['d3'] ?? 0, 'Sarjana S1 / D4' => $jenjangRaw['s1'] ?? 0];
                    $jenjangMax = max(1, max($jenjang));
                @endphp
                @foreach($jenjang as $label => $total)
                    <div class="sa-bar-row">
                        <span class="sa-bar-row__label">{{ $label }}</span>
                        <span class="sa-bar-row__value">{{ $total }}</span>
                        <div class="sa-progress"><span style="width: {{ round($total / $jenjangMax * 100) }}%"></span></div>
                    </div>
                @endforeach
                <p class="sa-list__sub">Top 5 institusi:</p>
                <div class="sa-attention__chips">
                    @forelse($topInstitusi->take(5) as $inst)
                        <x-sa.badge><strong>{{ $inst->institusi }}</strong>&nbsp;({{ $inst->total }})</x-sa.badge>
                    @empty
                        <span class="sa-list__sub">Belum ada profil institusi.</span>
                    @endforelse
                </div>
            </x-sa.panel>
        </div>
    </div>
</div>
