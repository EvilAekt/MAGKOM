<div id="tab-seleksi" class="tab-content-panel" role="tabpanel" aria-labelledby="btn-tab-seleksi">
    
    <!-- BAGIAN 1: ANTREAN VERIFIKASI BERKAS TERTUNDA (FIFO BR-04) -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--neutral-300); flex-wrap: wrap; gap: 0.5rem;">
            <div>
                <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-900); margin: 0; display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                    <span>Antrean Verifikasi Berkas Tertunda (FIFO)</span>
                    <span style="font-size: 0.6875rem; font-weight: 700; background: #FEF3C7; color: #92400E; padding: 0.15rem 0.5rem; border-radius: 999px;">
                        {{ $menungguVerifikasi }} Berkas
                    </span>
                    @if($antreanMelebihiSla > 0)
                        <span style="font-size: 0.6875rem; font-weight: 700; background: #FEE2E2; color: #DC2626; border: 1px solid #FECACA; padding: 0.15rem 0.5rem; border-radius: 999px;">
                            ⚠️ {{ $antreanMelebihiSla }} Berkas Melewati SLA 5 Hari
                        </span>
                    @else
                        <span style="font-size: 0.6875rem; font-weight: 700; background: #DCFCE7; color: #166534; padding: 0.15rem 0.5rem; border-radius: 999px;">
                            SLA Aman (≤ 5 Hari)
                        </span>
                    @endif
                </h3>
                <p style="font-size: 0.75rem; color: var(--neutral-600); margin: 0.125rem 0 0 0;">
                    Pendaftar menunggu pemeriksaan berkas administrasi dan keputusan tim seleksi
                </p>
            </div>
            <a href="{{ route('admin.pendaftaran.index') }}" style="font-size: 0.8125rem; font-weight: 700; color: #2563EB; text-decoration: none;">
                Buka Antrean Lengkap &rarr;
            </a>
        </div>

        @if($pendaftaranMenunggu->isEmpty())
            <div style="padding: 2rem 1rem; text-align: center; color: var(--neutral-600); background: #F8FAFC; border-radius: 8px; border: 1px dashed var(--neutral-300);">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#16A34A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 0.5rem;">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <p style="margin: 0; font-size: 0.875rem; font-weight: 600; color: var(--neutral-800);">Semua berkas pendaftaran telah diverifikasi!</p>
                <p style="margin: 0.25rem 0 0 0; font-size: 0.75rem; color: var(--neutral-500);">Tidak ada berkas yang menunggu antrean seleksi saat ini.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.8125rem; text-align: left;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--neutral-300); color: var(--neutral-600); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.04em;">
                            <th style="padding: 0.5rem 0.75rem;">Pendaftar & Institusi</th>
                            <th style="padding: 0.5rem 0.75rem;">Jurusan & Periode</th>
                            <th style="padding: 0.5rem 0.75rem;">Waktu Ajuan</th>
                            <th style="padding: 0.5rem 0.75rem;">Status</th>
                            <th style="padding: 0.5rem 0.75rem; text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendaftaranMenunggu as $p)
                            @php
                                $namaUser = $p->user?->nama ?? 'Peserta';
                                $instName = $p->user?->profil?->institusi ?? '-';
                                $jurusanName = $p->kuota?->jurusan?->nama_jurusan ?? ($p->user?->profil?->jurusan?->nama_jurusan ?? '-');
                                $bulanName = $p->bulanMagang?->bulan_mulai?->translatedFormat('F Y') ?? '-';
                                $tglAjuan = $p->diajukan_at ? $p->diajukan_at->format('d M Y, H:i') : ($p->created_at ? $p->created_at->format('d M Y, H:i') : '-');
                            @endphp
                            <tr style="border-bottom: 1px solid var(--neutral-200);">
                                <td style="padding: 0.625rem 0.75rem;">
                                    <span style="font-weight: 700; color: var(--neutral-900); display: block;">{{ $namaUser }}</span>
                                    <span style="font-size: 0.75rem; color: var(--neutral-600);">{{ $instName }}</span>
                                </td>
                                <td style="padding: 0.625rem 0.75rem;">
                                    <span style="font-weight: 600; color: var(--neutral-800); display: block;">{{ $jurusanName }}</span>
                                    <span style="font-size: 0.75rem; color: var(--neutral-500);">{{ $bulanName }} ({{ $p->durasi_bulan }} Bln)</span>
                                </td>
                                <td style="padding: 0.625rem 0.75rem; color: var(--neutral-600);">
                                    {{ $tglAjuan }}
                                </td>
                                <td style="padding: 0.625rem 0.75rem;">
                                    @if($p->status->value === 'diajukan')
                                        <span style="background: #FFFBEB; color: #B45309; border: 1px solid #FDE68A; padding: 0.2rem 0.5rem; border-radius: 999px; font-size: 0.6875rem; font-weight: 700;">
                                            Diajukan
                                        </span>
                                    @else
                                        <span style="background: #EFF6FF; color: #1D4ED8; border: 1px solid #BFDBFE; padding: 0.2rem 0.5rem; border-radius: 999px; font-size: 0.6875rem; font-weight: 700;">
                                            Perlu Perbaikan
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 0.625rem 0.75rem; text-align: right;">
                                    <a href="{{ route('admin.pendaftaran.show', $p->id) }}" style="display: inline-flex; align-items: center; gap: 0.25rem; background: #2563EB; color: #FFFFFF; padding: 0.35rem 0.75rem; border-radius: 6px; text-decoration: none; font-size: 0.75rem; font-weight: 600;">
                                        <span>Tinjau & Putuskan</span>
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="9 18 15 12 9 6"></polyline>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- BAGIAN 2: UTILISASI KUOTA PER JURUSAN & WAITING LIST -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 1.5rem;">
        
        <!-- Box A: Utilisasi Kuota per Jurusan Bulan Aktif -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <div>
                    <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900); margin: 0;">
                        Utilisasi Kuota per Jurusan
                    </h4>
                    <span style="font-size: 0.75rem; color: var(--neutral-600);">
                        Periode: <strong>{{ $bulanAktif ? $bulanAktif->bulan_mulai?->translatedFormat('F Y') : '-' }}</strong>
                    </span>
                </div>
                <a href="{{ route('admin.kuota.index') }}" style="font-size: 0.75rem; font-weight: 700; color: var(--blue-600); text-decoration: underline;">
                    Atur Kuota &rarr;
                </a>
            </div>

            <!-- Status Kepatuhan Pagu Kuota Dinas Bulanan (BR-01) -->
            @php
                $persenPagu = $kuotaMaksPagu > 0 ? min(100, round(($totalKuotaUmum / $kuotaMaksPagu) * 100)) : 0;
                $isExceedPagu = $totalKuotaUmum > $kuotaMaksPagu;
            @endphp
            <div style="margin-bottom: 1rem; padding: 0.75rem 0.875rem; border-radius: 8px; background: #F8FAFC; border: 1px solid var(--neutral-300);">
                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem;">
                    <span style="font-weight: 700; color: var(--neutral-700);">Kepatuhan Pagu Kuota Dinas (BR-01):</span>
                    <span style="font-weight: 700; color: {{ $isExceedPagu ? '#DC2626' : '#2563EB' }};">
                        {{ $totalKuotaUmum }} / {{ $kuotaMaksPagu }} Kursi ({{ $persenPagu }}%)
                    </span>
                </div>
                <div style="width: 100%; height: 6px; background: var(--neutral-300); border-radius: 999px; overflow: hidden; margin-top: 0.375rem;">
                    <div style="width: {{ $persenPagu }}%; height: 100%; background: {{ $isExceedPagu ? '#DC2626' : '#2563EB' }}; border-radius: 999px;"></div>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.375rem; font-size: 0.6875rem; color: var(--neutral-500);">
                    <span>Batas pagu kuota umum bulanan dari kebijakan sistem</span>
                    <a href="{{ route('superadmin.pengaturan.index') }}" style="color: var(--blue-600); font-weight: 600; text-decoration: underline;">Ubah Pagu &rarr;</a>
                </div>
            </div>

            @if($kuotaList->isEmpty())
                <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0;">Belum ada alokasi kuota jurusan pada bulan aktif ini.</p>
            @else
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    @foreach($kuotaList as $k)
                        @php
                            $terisi = $k->terisi;
                            $kapasitas = $k->jumlah;
                            $persen = $kapasitas > 0 ? min(100, round(($terisi / $kapasitas) * 100)) : 0;
                            $isFull = $terisi >= $kapasitas;
                        @endphp
                        <div style="background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 8px; padding: 0.625rem 0.875rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8125rem;">
                                <span style="font-weight: 700; color: var(--neutral-800);">{{ $k->jurusan?->nama_jurusan ?? 'Jurusan' }}</span>
                                <span style="font-size: 0.75rem; font-weight: 700; color: {{ $isFull ? '#DC2626' : '#166534' }};">
                                    {{ $terisi }} / {{ $kapasitas }} ({{ $persen }}%)
                                </span>
                            </div>
                            <div style="width: 100%; height: 6px; background: var(--neutral-300); border-radius: 999px; overflow: hidden; margin-top: 0.375rem;">
                                <div style="width: {{ $persen }}%; height: 100%; background: {{ $isFull ? '#DC2626' : '#16A34A' }}; border-radius: 999px;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Box B: Waiting List FIFO & Slot Khusus Kemitraan -->
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <!-- Card Waiting List -->
            <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900); margin: 0;">
                        Antrean Waiting List (BR-05)
                    </h4>
                    <span style="font-size: 1.25rem; font-weight: 800; color: #D97706;">
                        {{ $totalWaitingList }}
                    </span>
                </div>
                <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0 0 0.875rem 0; line-height: 1.4;">
                    Pendaftar yang berkasnya valid namun kuota jurusan telah penuh. Sistem memproses secara otomatis jika ada peserta yang batal.
                </p>
                <a href="{{ route('admin.waiting-list.index') }}" class="btn-secondary" style="display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.75rem; padding: 0.45rem 0.75rem; text-decoration: none; border-radius: 6px;">
                    <span>Kelola Antrean Waiting List &rarr;</span>
                </a>
            </div>

            <!-- Card Slot Khusus -->
            <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900); margin: 0;">
                        Slot Khusus Kemitraan Sleman
                    </h4>
                    <span style="font-size: 1.25rem; font-weight: 800; color: #7C3AED;">
                        {{ $slotKhusus ? $slotKhusus->terisi . ' / ' . $slotKhusus->jumlah : '0 / 0' }}
                    </span>
                </div>
                <div style="margin-bottom: 0.5rem;">
                    <span style="font-size: 0.6875rem; font-weight: 700; background: #F3E8FF; color: #6B21A8; border: 1px solid #DDD6FE; padding: 0.15rem 0.5rem; border-radius: 999px;">
                        🔒 Isolasi Publik: 100% Tersembunyi (AC-01-5)
                    </span>
                </div>
                <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0 0 0.875rem 0; line-height: 1.4;">
                    Alokasi jalur khusus untuk institusi yang memiliki nota kesepahaman (MoU) resmi dengan Pemkab Sleman.
                </p>
                <a href="{{ route('superadmin.slot-khusus.index') }}" class="btn-secondary" style="display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.75rem; padding: 0.45rem 0.75rem; text-decoration: none; border-radius: 6px;">
                    <span>Atur Slot Khusus Kemitraan &rarr;</span>
                </a>
            </div>
        </div>

    </div>

</div>
