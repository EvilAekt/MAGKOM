{{-- BAGIAN 1: ANTREAN VERIFIKASI BERKAS TERTUNDA (FIFO BR-04) --}}
<x-sa.panel title="Antrean Verifikasi Berkas Tertunda (FIFO)" subtitle="Pendaftar menunggu pemeriksaan berkas administrasi dan keputusan tim seleksi" :href="route('admin.pendaftaran.index')" linkText="Buka Antrean Lengkap &rarr;">
    @if($pendaftaranMenunggu->isEmpty())
        <x-sa.empty text="Semua berkas pendaftaran telah diverifikasi! Tidak ada berkas yang menunggu antrean seleksi saat ini." icon="circle-check" />
    @else
        <div class="sa-table-scroll">
            <table class="sa-table">
                <caption class="sa-sr-only">Tabel Antrean Verifikasi Berkas Tertunda</caption>
                <thead>
                    <tr>
                        <th scope="col">Pendaftar & Institusi</th>
                        <th scope="col">Jurusan & Bulan</th>
                        <th scope="col">Waktu Ajuan</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="sa-text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendaftaranMenunggu as $p)
                        @php
                            $namaUser = $p->user?->nama ?? 'Peserta';
                            $instName = $p->user?->profil?->institusi ?? '-';
                            $jurusanName = $p->kuota?->jurusan?->nama_jurusan ?? ($p->user?->profil?->jurusan?->nama_jurusan ?? '-');
                            $bulanName = $p->bulanMagang?->nama_bulan ?? '-';
                            $tglAjuan = $p->diajukan_at ? $p->diajukan_at->format('d M Y, H:i') : ($p->created_at ? $p->created_at->format('d M Y, H:i') : '-');
                        @endphp
                        <tr>
                            <td>
                                <strong class="sa-table-strong">{{ $namaUser }}</strong>
                                <span class="sa-table-sub">{{ $instName }}</span>
                            </td>
                            <td>
                                <strong class="sa-table-strong">{{ $jurusanName }}</strong>
                                <span class="sa-table-sub">{{ $bulanName }} ({{ $p->durasi_bulan }} Bln)</span>
                            </td>
                            <td>
                                {{ $tglAjuan }}
                            </td>
                            <td>
                                @if($p->status->value === 'diajukan')
                                    <span class="sa-badge sa-badge--warning">
                                        Diajukan
                                    </span>
                                @else
                                    <span class="sa-badge sa-badge--info">
                                        Perlu Perbaikan
                                    </span>
                                @endif
                            </td>
                            <td class="sa-text-right">
                                <a href="{{ route('admin.pendaftaran.show', $p->id) }}" class="btn-primary sa-btn-sm">
                                    <span>Tinjau & Putuskan</span>
                                    <x-sa.icon name="chevron-right" :size="12" />
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-sa.panel>

{{-- BAGIAN 2: UTILISASI KUOTA PER JURUSAN & WAITING LIST --}}
<div class="sa-grid sa-grid--2">
    {{-- Box A: Utilisasi Kuota per Jurusan Bulan Aktif --}}
    <x-sa.panel title="Utilisasi Kuota per Jurusan" subtitle="Bulan: {{ $bulanAktif ? $bulanAktif->nama_bulan : '-' }}" :href="route('admin.kuota.index')" linkText="Atur Kuota &rarr;">
        @php
            $persenPagu = $kuotaMaksPagu > 0 ? min(100, round(($totalKuotaUmum / $kuotaMaksPagu) * 100)) : 0;
            $isExceedPagu = $totalKuotaUmum > $kuotaMaksPagu;
        @endphp
        
        {{-- Status Kepatuhan Pagu Kuota Dinas Bulanan (BR-01) --}}
        <div class="sa-pagu-box">
            <div class="sa-pagu-box__header">
                <span class="sa-table-strong">Kepatuhan Pagu Kuota Dinas (BR-01):</span>
                <span class="sa-table-strong {{ $isExceedPagu ? 'sa-color-danger' : 'sa-color-primary' }}">
                    {{ $totalKuotaUmum }} / {{ $kuotaMaksPagu }} Kursi ({{ $persenPagu }}%)
                </span>
            </div>
            <div class="sa-meter" role="meter" aria-valuenow="{{ $totalKuotaUmum }}" aria-valuemin="0" aria-valuemax="{{ $kuotaMaksPagu }}" aria-label="Kepatuhan Pagu Kuota Dinas (BR-01)">
                <div class="sa-meter__fill {{ $isExceedPagu ? 'sa-meter__fill--danger' : 'sa-meter__fill--primary' }}" data-v="{{ $persenPagu }}"></div>
            </div>
            <div class="sa-pagu-box__footer">
                <span>Batas pagu kuota umum bulanan dari kebijakan sistem</span>
                <a href="{{ route('superadmin.pengaturan.index') }}" class="sa-link">Ubah Pagu &rarr;</a>
            </div>
        </div>

        @if($kuotaList->isEmpty())
            <x-sa.empty text="Belum ada alokasi kuota jurusan pada bulan aktif ini." icon="layers" />
        @else
            <div class="sa-grid sa-grid--1">
                @foreach($kuotaList as $k)
                    @php
                        $terisi = $k->terisi;
                        $kapasitas = $k->jumlah;
                    @endphp
                    <div class="sa-panel sa-panel--sub">
                        <x-sa.meter 
                            :value="$terisi" 
                            :max="$kapasitas" 
                            :label="$k->jurusan?->nama_jurusan ?? 'Jurusan'" />
                    </div>
                @endforeach
            </div>
        @endif
    </x-sa.panel>

    {{-- Box B: Waiting List FIFO & Slot Khusus Kemitraan --}}
    <div class="sa-grid sa-grid--1">
        {{-- Card Waiting List (BR-05) --}}
        <x-sa.panel title="Antrean Waiting List (BR-05)">
            <div class="sa-metric__value sa-color-warning">
                {{ $totalWaitingList }}
            </div>
            <p class="sa-panel__sub">
                Pendaftar yang berkasnya valid namun kuota jurusan telah penuh. Sistem memproses secara otomatis jika ada peserta yang batal.
            </p>
            <div class="sa-panel__actions">
                <a href="{{ route('admin.waiting-list.index') }}" class="btn-secondary sa-btn-sm">
                    <span>Kelola Antrean Waiting List &rarr;</span>
                </a>
            </div>
        </x-sa.panel>

        {{-- Card Slot Khusus Kemitraan Sleman --}}
        <x-sa.panel title="Slot Khusus Kemitraan Sleman">
            <div class="sa-metric__value sa-color-purple">
                {{ $slotKhusus ? $slotKhusus->terisi . ' / ' . $slotKhusus->jumlah : '0 / 0' }}
            </div>
            <div class="sa-badge-row">
                <span class="sa-badge sa-badge--info">
                    <x-sa.icon name="lock" :size="12" />
                    <span>Isolasi Publik: 100% Tersembunyi (AC-01-5)</span>
                </span>
            </div>
            <p class="sa-panel__sub">
                Alokasi jalur khusus untuk institusi yang memiliki nota kesepahaman (MoU) resmi dengan Pemkab Sleman.
            </p>
            <div class="sa-panel__actions">
                <a href="{{ route('superadmin.slot-khusus.index') }}" class="btn-secondary sa-btn-sm">
                    <span>Atur Slot Khusus Kemitraan &rarr;</span>
                </a>
            </div>
        </x-sa.panel>
    </div>
</div>
