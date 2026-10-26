{{-- ==========================================================================
   Tab 2: Monitoring Seleksi & Kuota - SuperAdmin Master Cockpit
   Standar Visual Gentelella Theme (MagKom Sleman)
   ========================================================================== --}}
<div class="gentelella-tab-pane gentelella-tab-seleksi">
{{-- BAGIAN 1: ANTREAN VERIFIKASI BERKAS TERTUNDA (FIFO BR-04) --}}
<x-sa.panel title="Antrean Verifikasi Berkas Tertunda (FIFO)" subtitle="Pendaftar menunggu pemeriksaan berkas administrasi dan keputusan tim seleksi" :href="route('admin.pendaftaran.index')" linkText="Buka Antrean Lengkap &rarr;">
    @if($pendaftaranMenunggu->isEmpty())
        <x-sa.empty text="Semua berkas pendaftaran telah diverifikasi! Tidak ada berkas yang menunggu antrean seleksi saat ini." icon="circle-check" />
    @else
        <div class="sa-table-scroll">
            <table class="sa-table cv-table-modern">
                <caption class="sa-sr-only">Tabel Antrean Verifikasi Berkas Tertunda</caption>
                <thead>
                    <tr>
                        <th scope="col" style="width: 40px;">No.</th>
                        <th scope="col">Pendaftar & Institusi</th>
                        <th scope="col">Jurusan & Bulan</th>
                        <th scope="col">Waktu Ajuan</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="sa-text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendaftaranMenunggu as $index => $p)
                        @php
                            $namaUser = $p->user?->nama ?? 'Peserta';
                            $instName = $p->user?->profil?->institusi ?? '-';
                            $jurusanName = $p->kuota?->jurusan?->nama_jurusan ?? ($p->user?->profil?->jurusan?->nama_jurusan ?? '-');
                            $bulanName = $p->bulanMagang?->nama_bulan ?? '-';
                            $tglAjuan = $p->diajukan_at ? $p->diajukan_at->format('d M Y, H:i') : ($p->created_at ? $p->created_at->format('d M Y, H:i') : '-');
                            $initials = collect(explode(' ', $namaUser))->take(2)->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('');
                        @endphp
                        <tr>
                            <td class="sa-text-muted" style="font-family: monospace; font-size: 0.75rem; font-weight: 700;">
                                {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <span class="cv-avatar-sm" aria-hidden="true">{{ $initials ?: 'PS' }}</span>
                                    <div>
                                        <strong class="sa-table-strong" style="display: block; line-height: 1.3;">{{ $namaUser }}</strong>
                                        <span class="sa-table-sub">{{ $instName }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <strong class="sa-table-strong" style="display: block; line-height: 1.3;">{{ $jurusanName }}</strong>
                                <span class="sa-table-sub">{{ $bulanName }} ({{ $p->durasi_bulan }} Bln)</span>
                            </td>
                            <td style="font-size: 0.8125rem; color: var(--cv-ink-2);">
                                {{ $tglAjuan }}
                            </td>
                            <td>
                                @if($p->status->value === 'diajukan')
                                    <span class="sa-badge sa-badge--warning" style="display: inline-flex; align-items: center; gap: 4px; border-radius: 999px; padding: 2px 10px; font-weight: 600;">
                                        <span style="width: 6px; height: 6px; border-radius: 50%; background: #D97706;"></span>
                                        Diajukan
                                    </span>
                                @else
                                    <span class="sa-badge sa-badge--info" style="display: inline-flex; align-items: center; gap: 4px; border-radius: 999px; padding: 2px 10px; font-weight: 600;">
                                        <span style="width: 6px; height: 6px; border-radius: 50%; background: #2563EB;"></span>
                                        Perlu Perbaikan
                                    </span>
                                @endif
                            </td>
                            <td class="sa-text-right">
                                <a href="{{ route('admin.pendaftaran.show', $p->id) }}" class="btn-primary sa-btn-sm" style="border-radius: 6px; font-weight: 600;">
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
        <div class="sa-pagu-box" style="background: linear-gradient(180deg, #F8FAFC 0%, #FFFFFF 100%); border: 1px solid var(--cv-line); border-radius: 10px; padding: 1rem 1.15rem; margin-bottom: 1.25rem;">
            <div class="sa-pagu-box__header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <span class="sa-table-strong" style="font-size: 0.8125rem;">Kepatuhan Pagu Kuota Dinas (BR-01):</span>
                <span class="sa-table-strong {{ $isExceedPagu ? 'sa-color-danger' : 'sa-color-primary' }}" style="font-family: monospace; font-size: 0.875rem;">
                    {{ $totalKuotaUmum }} / {{ $kuotaMaksPagu }} Kursi ({{ $persenPagu }}%)
                </span>
            </div>
            <div class="sa-meter" role="meter" aria-valuenow="{{ $totalKuotaUmum }}" aria-valuemin="0" aria-valuemax="{{ $kuotaMaksPagu }}" aria-label="Kepatuhan Pagu Kuota Dinas (BR-01)" style="height: 8px; background: var(--cv-line); border-radius: 999px; overflow: hidden;">
                <div class="sa-meter__fill {{ $isExceedPagu ? 'sa-meter__fill--danger' : 'sa-meter__fill--primary' }}" data-v="{{ $persenPagu }}" style="width: {{ $persenPagu }}%; height: 100%; border-radius: 999px; transition: width 0.3s ease;"></div>
            </div>
            <div class="sa-pagu-box__footer" style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.5rem; font-size: 0.75rem; color: var(--cv-ink-3);">
                <span>Batas pagu kuota umum bulanan dari kebijakan sistem</span>
                <a href="{{ route('superadmin.pengaturan.index') }}" class="sa-link" style="font-weight: 600; color: var(--cv-blue-700);">Ubah Pagu &rarr;</a>
            </div>
        </div>

        @if($kuotaList->isEmpty())
            <x-sa.empty text="Belum ada alokasi kuota jurusan pada bulan aktif ini." icon="layers" />
        @else
            <div class="sa-grid sa-grid--1" style="gap: 0.75rem;">
                @foreach($kuotaList as $k)
                    @php
                        $terisi = $k->terisi;
                        $kapasitas = $k->jumlah;
                        $jurusanLabel = $k->jurusan?->nama_jurusan ?? 'Jurusan';
                        $persen = $kapasitas > 0 ? min(100, round(($terisi / $kapasitas) * 100)) : 0;
                        $isFull = $terisi >= $kapasitas;
                    @endphp
                    <div class="sa-panel sa-panel--sub" style="background: var(--cv-surface); border: 1px solid var(--cv-line); border-radius: 8px; padding: 0.85rem 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                            <span class="sa-table-strong" style="font-size: 0.8125rem;">{{ $jurusanLabel }}</span>
                            <span style="font-family: monospace; font-size: 0.75rem; font-weight: 700; color: {{ $isFull ? '#D97706' : 'var(--cv-blue-800)' }};">
                                {{ $terisi }} / {{ $kapasitas }} ({{ $persen }}%)
                            </span>
                        </div>
                        
                        {{-- Preserved original component for test compatibility --}}
                        <x-sa.meter 
                            :value="$terisi" 
                            :max="$kapasitas" 
                            :label="$jurusanLabel" />

                        {{-- Segmented seat dots visual --}}
                        <div class="cv-seg {{ $isFull ? 'full' : '' }}" title="Keterisian Kursi: {{ $terisi }} dari {{ $kapasitas }}" aria-hidden="true">
                            @for($i = 0; $i < min($kapasitas, 20); $i++)
                                <i class="{{ $i < $terisi ? 'f' : '' }}"></i>
                            @endfor
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-sa.panel>

    {{-- Box B: Waiting List FIFO & Slot Khusus Kemitraan --}}
    <div class="sa-grid sa-grid--1" style="gap: 1.25rem;">
        {{-- Card Waiting List (BR-05) --}}
        <x-sa.panel title="Antrean Waiting List (BR-05)">
            <div style="display: flex; align-items: baseline; gap: 0.5rem; margin: 0.5rem 0 0.75rem;">
                <div class="sa-metric__value sa-color-warning" style="font-size: 2.25rem; font-weight: 800; line-height: 1; font-family: monospace;">
                    {{ $totalWaitingList }}
                </div>
                <span class="sa-table-sub" style="font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em;">Peserta Antre</span>
            </div>
            <p class="sa-panel__sub" style="font-size: 0.8125rem; line-height: 1.5; color: var(--cv-ink-2); margin-bottom: 1rem;">
                Pendaftar yang berkasnya valid namun kuota jurusan telah penuh. Sistem memproses secara otomatis jika ada peserta yang batal.
            </p>
            <div class="sa-panel__actions">
                <a href="{{ route('admin.waiting-list.index') }}" class="btn-secondary sa-btn-sm" style="display: inline-flex; align-items: center; gap: 0.35rem; font-weight: 600; border-radius: 6px;">
                    <span>Kelola Antrean Waiting List &rarr;</span>
                </a>
            </div>
        </x-sa.panel>

        {{-- Card Slot Khusus Kemitraan Sleman --}}
        <x-sa.panel title="Slot Khusus Kemitraan Sleman">
            <div style="display: flex; align-items: baseline; gap: 0.5rem; margin: 0.5rem 0 0.5rem;">
                <div class="sa-metric__value sa-color-purple" style="font-size: 2.25rem; font-weight: 800; line-height: 1; font-family: monospace; color: #7C3AED;">
                    {{ $slotKhusus ? $slotKhusus->terisi . ' / ' . $slotKhusus->jumlah : '0 / 0' }}
                </div>
                <span class="sa-table-sub" style="font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em;">Kursi Terisi</span>
            </div>
            <div class="sa-badge-row" style="margin-bottom: 0.75rem;">
                <span class="sa-badge sa-badge--info" style="display: inline-flex; align-items: center; gap: 0.35rem; background: #EDE9FE; color: #6D28D9; border: 1px solid #DDD6FE; font-weight: 600; font-size: 0.75rem; padding: 3px 10px; border-radius: 999px;">
                    <x-sa.icon name="lock" :size="12" />
                    <span>Isolasi Publik: 100% Tersembunyi (AC-01-5)</span>
                </span>
            </div>
            <p class="sa-panel__sub" style="font-size: 0.8125rem; line-height: 1.5; color: var(--cv-ink-2); margin-bottom: 1rem;">
                Alokasi jalur khusus untuk institusi yang memiliki nota kesepahaman (MoU) resmi dengan Pemkab Sleman.
            </p>
            <div class="sa-panel__actions">
                <a href="{{ route('superadmin.slot-khusus.index') }}" class="btn-secondary sa-btn-sm" style="display: inline-flex; align-items: center; gap: 0.35rem; font-weight: 600; border-radius: 6px;">
                    <span>Atur Slot Khusus Kemitraan &rarr;</span>
                </a>
            </div>
        </x-sa.panel>
    </div>
</div>
</div>
