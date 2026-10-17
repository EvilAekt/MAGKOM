@php
    $hasAttention = ($anomaliKuotaCount > 0)
        || ($totalTugasTerlambat > 0)
        || ($totalAntreanDiajukan > 0 || $totalWaitingList > 0)
        || ($antreanMelebihiSla > 0)
        || ($stafPendingGantiPassword > 0)
        || ($totalLogbookPending > 0);
@endphp

@if($hasAttention)
    <div class="sa-grid sa-grid--1">
        {{-- Bahaya: Anomali Kuota --}}
        @if($anomaliKuotaCount > 0)
            <div class="sa-attention sa-attention--danger">
                <div class="sa-attention__content">
                    <x-sa.icon name="triangle-alert" :size="20" />
                    <div>
                        <span class="sa-attention__title">Integritas Kuota Terganggu</span>
                        <span class="sa-attention__desc">{{ $anomaliKuotaCount }} pelanggaran integritas kuota terdeteksi.</span>
                    </div>
                </div>
                <a href="{{ route('superadmin.audit-log.index') }}" class="sa-link">Periksa Audit Log &rarr;</a>
            </div>
        @endif

        {{-- Bahaya: Tugas Terlambat --}}
        @if($totalTugasTerlambat > 0)
            <div class="sa-attention sa-attention--danger">
                <div class="sa-attention__content">
                    <x-sa.icon name="alert-circle" :size="20" />
                    <div>
                        <span class="sa-attention__title">Tugas Melewati Tenggat</span>
                        <span class="sa-attention__desc">{{ $totalTugasTerlambat }} tugas melewati tenggat tanpa status selesai.</span>
                    </div>
                </div>
                <a href="{{ route('superadmin.monitoring.index') }}" class="sa-link">Buka Monitoring &rarr;</a>
            </div>
        @endif

        {{-- Peringatan: Antrean Pendaftaran (Terkunci Tes: Perhatian Antrean Pendaftaran) --}}
        @if($totalAntreanDiajukan > 0 || $totalWaitingList > 0)
            <div class="sa-attention sa-attention--warning">
                <div class="sa-attention__content">
                    <x-sa.icon name="clock" :size="20" />
                    <div>
                        <span class="sa-attention__title">Perhatian Antrean Pendaftaran</span>
                        <span class="sa-attention__desc">
                            Terdapat <strong>{{ $totalAntreanDiajukan }}</strong> berkas baru menunggu verifikasi seleksi dan <strong>{{ $totalWaitingList }}</strong> pendaftar berstatus antrean kuota (waiting list).
                        </span>
                    </div>
                </div>
                <a href="{{ route('admin.pendaftaran.index') }}" class="sa-link">
                    Tinjau Berkas &rarr;
                </a>
            </div>
        @endif

        {{-- Peringatan: SLA Breached --}}
        @if($antreanMelebihiSla > 0)
            <div class="sa-attention sa-attention--warning">
                <div class="sa-attention__content">
                    <x-sa.icon name="clock" :size="20" />
                    <div>
                        <span class="sa-attention__title">SLA Verifikasi Terlampaui</span>
                        <span class="sa-attention__desc">{{ $antreanMelebihiSla }} berkas melewati batas SLA 5 hari.</span>
                    </div>
                </div>
                <a href="{{ route('admin.pendaftaran.index') }}" class="sa-link">Percepat Verifikasi &rarr;</a>
            </div>
        @endif

        {{-- Peringatan: Staf Pending Sandi Awal --}}
        @if($stafPendingGantiPassword > 0)
            <div class="sa-attention sa-attention--warning">
                <div class="sa-attention__content">
                    <x-sa.icon name="key" :size="20" />
                    <div>
                        <span class="sa-attention__title">Higienitas Kredensial Staf</span>
                        <span class="sa-attention__desc">{{ $stafPendingGantiPassword }} akun staf belum mengganti sandi awal.</span>
                    </div>
                </div>
                <a href="{{ route('superadmin.staf.index') }}" class="sa-link">Kelola Akun Staf &rarr;</a>
            </div>
        @endif

        {{-- Info: Logbook Pending --}}
        @if($totalLogbookPending > 0)
            <div class="sa-attention sa-attention--info">
                <div class="sa-attention__content">
                    <x-sa.icon name="book-open" :size="20" />
                    <div>
                        <span class="sa-attention__title">Logbook Pending Reviu</span>
                        <span class="sa-attention__desc">{{ $totalLogbookPending }} logbook menunggu reviu pembimbing lapangan.</span>
                    </div>
                </div>
                <a href="{{ route('superadmin.monitoring.index') }}" class="sa-link">Pantau Bimbingan &rarr;</a>
            </div>
        @endif
    </div>
@else
    <div class="sa-attention sa-attention--success">
        <div class="sa-attention__content">
            <x-sa.icon name="circle-check" :size="20" />
            <div>
                <span class="sa-attention__title">Kondisi Operasional Normal</span>
                <span class="sa-attention__desc">Tidak ada yang perlu ditindak saat ini. Semua antrean dan tugas berjalan sesuai SLA.</span>
            </div>
        </div>
    </div>
@endif
