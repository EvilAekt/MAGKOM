{{-- ==========================================================================
   Monitoring Kuota & Tim Operasional - Dashboard Admin
   ========================================================================== --}}
<div class="row">
    {{-- KOLOM KIRI: MONITORING KUOTA (x_panel dengan Segmented Bars) --}}
    <div class="col-md-6 col-sm-6 col-xs-12">
        <x-gentelella.x-panel 
            title="Alokasi & Keterisian Kuota Magang Periode" 
            subtitle="Realisasi kursi magang bulan {{ $bulanAktif ? $bulanAktif->bulan_mulai?->translatedFormat('F Y') : 'Aktif' }}."
        >
            <x-slot:actions>
                <a href="{{ route('admin.kuota.index') }}" class="btn-primary" style="padding: 0.25rem 0.6rem; font-size: 0.75rem; border-radius: 6px; text-decoration: none;">
                    <span>Kelola Kuota</span> &rarr;
                </a>
            </x-slot:actions>

            {{-- Total Capacity Strip --}}
            @if($bulanAktif)
                @php
                    $persenTotal = $totalKuotaUmum > 0 ? min(100, round(($totalKuotaTerisi / $totalKuotaUmum) * 100)) : 0;
                @endphp
                <div style="padding: 0.85rem 1rem; background: var(--blue-tint); border: 1px solid var(--blue-tint-line); border-radius: 10px; margin-bottom: 1rem;">
                    <div style="display: flex; justify-content: space-between; font-size: 0.8125rem; font-weight: 700; color: var(--blue-700); margin-bottom: 0.35rem;">
                        <span>Total Kuota Umum Terisi</span>
                        <span>{{ $totalKuotaTerisi }} / {{ $totalKuotaUmum }} Kursi ({{ $persenTotal }}%)</span>
                    </div>
                    <div style="width: 100%; height: 7px; background: rgba(6, 65, 204, 0.15); border-radius: 999px; overflow: hidden;">
                        <div style="height: 100%; width: {{ $persenTotal }}%; background: var(--blue-600); border-radius: 999px;"></div>
                    </div>
                </div>
            @endif

            {{-- List Kuota Jurusan dengan Segmented Bars --}}
            @if($kuotaList->isEmpty())
                <p style="margin: 0; font-size: 0.85rem; color: var(--neutral-500); text-align: center; padding: 1.5rem 0;">Belum ada kuota jurusan yang dikonfigurasi untuk bulan ini.</p>
            @else
                <div style="display: flex; flex-direction: column; gap: 0.65rem;">
                    @foreach($kuotaList as $kuota)
                        @php
                            $pct = $kuota->jumlah > 0 ? min(100, round(($kuota->terisi / $kuota->jumlah) * 100)) : 0;
                            $sisa = max(0, $kuota->jumlah - $kuota->terisi);
                            $color = $pct >= 100 ? 'var(--red-600)' : ($pct >= 75 ? 'var(--amber-700)' : 'var(--blue-600)');
                            $badgeType = $sisa > 0 ? 'success' : 'danger';
                            $segmentTotal = min(20, $kuota->jumlah); // batas render bar segmented
                            $filledSegments = $kuota->jumlah > 0 ? (int) round(($kuota->terisi / $kuota->jumlah) * $segmentTotal) : 0;
                        @endphp
                        <div class="adm-quota-row-item">
                            <div style="flex: 1; min-width: 0;">
                                <div style="display: flex; justify-content: space-between; align-items: baseline;">
                                    <span style="font-weight: 700; font-size: 0.85rem; color: var(--neutral-800); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $kuota->jurusan?->nama_jurusan }}
                                    </span>
                                    <span style="font-family: monospace; font-size: 0.75rem; font-weight: 700; color: {{ $color }};">
                                        {{ $kuota->terisi }}/{{ $kuota->jumlah }}
                                    </span>
                                </div>

                                {{-- Segmented Seat Bars (.cv-seg) --}}
                                <div class="cv-seg {{ $sisa === 0 ? 'full' : '' }}" role="img" aria-label="{{ $kuota->terisi }} dari {{ $kuota->jumlah }} kursi terisi">
                                    @for($i = 0; $i < $segmentTotal; $i++)
                                        <i class="{{ $i < $filledSegments ? 'filled' : '' }}"></i>
                                    @endfor
                                </div>

                                <div style="display: flex; justify-content: space-between; font-size: 0.6875rem; color: var(--neutral-500); margin-top: 0.25rem;">
                                    <span>{{ $kuota->jurusan?->kategori === 'smk' ? 'SMK' : 'Perguruan Tinggi' }}</span>
                                    <span>{{ $sisa > 0 ? 'Sisa ' . $sisa . ' Kursi' : 'Penuh' }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-gentelella.x-panel>
    </div>

    {{-- KOLOM KANAN: STATUS OPERASIONAL & TIM TERPADU (x_panel) --}}
    <div class="col-md-6 col-sm-6 col-xs-12">
        <x-gentelella.x-panel 
            title="Status Operasional & Tim Kerja" 
            subtitle="Ringkasan jadwal pendaftaran dan kapasitas pembimbing dinas."
        >
            {{-- 1. Status Bulan & Countdown --}}
            @if($bulanAktif)
                @php
                    $tglTutup = $bulanAktif->tgl_tutup ? \Carbon\Carbon::parse($bulanAktif->tgl_tutup) : null;
                    $tglBuka = $bulanAktif->tgl_buka ? \Carbon\Carbon::parse($bulanAktif->tgl_buka) : null;
                    $isBuka = $tglBuka && $tglTutup && now()->between($tglBuka, $tglTutup);
                    $sisaHari = $tglTutup && $tglTutup->isFuture() ? (int) now()->diffInDays($tglTutup, false) : 0;
                @endphp
                <div style="margin-bottom: 1rem;">
                    <span style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-500); text-transform: uppercase; margin-bottom: 0.35rem; display: block;">Jadwal Pendaftaran Bulan Ini</span>
                    <div style="padding: 0.75rem 1rem; background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 10px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <span style="font-size: 0.85rem; font-weight: 700; color: var(--neutral-800); display: block;">
                                Batas: {{ $tglTutup ? $tglTutup->translatedFormat('d F Y') : '-' }}
                            </span>
                            <small style="color: var(--neutral-500);">
                                @if($isBuka && $sisaHari >= 0)
                                    Tersisa <strong style="color: var(--blue-600);">{{ $sisaHari }} hari kalender</strong>
                                @else
                                    Pendaftaran tidak sedang berlangsung
                                @endif
                            </small>
                        </div>
                        <span class="x_badge {{ $isBuka ? 'x_badge_success' : 'x_badge_primary' }}">
                            {{ $isBuka ? '● Dibuka' : '● Ditutup' }}
                        </span>
                    </div>
                </div>
            @endif

            {{-- 2. Beban Pembimbing Lapangan --}}
            <div style="margin-bottom: 1rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <span style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-500); text-transform: uppercase;">Distribusi Pembimbing Lapangan</span>
                    <a href="{{ route('admin.penempatan.index') }}" style="font-size: 0.75rem; font-weight: 600; color: var(--blue-600); text-decoration: none;">
                        <span>Atur &rarr;</span>
                    </a>
                </div>

                @if($pembimbingWorkload->isEmpty())
                    <p style="font-size: 0.825rem; color: var(--neutral-500); margin: 0;">Belum ada pembimbing yang terdaftar.</p>
                @else
                    @foreach($pembimbingWorkload->take(3) as $pmb)
                        <div class="adm-mentor-clean-row">
                            <div style="display: flex; align-items: center; gap: 0.65rem; min-width: 0;">
                                <div class="adm-avatar-circle" style="width: 32px; height: 32px; font-size: 0.75rem;">
                                    {{ strtoupper(substr($pmb->nama, 0, 1)) }}
                                </div>
                                <div style="min-width: 0;">
                                    <div style="font-weight: 700; font-size: 0.825rem; color: var(--neutral-800); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $pmb->nama }}
                                    </div>
                                    <small style="color: var(--neutral-500); font-size: 0.7rem;">{{ $pmb->bidang?->nama_bidang ?? 'Dinas Kominfo Sleman' }}</small>
                                </div>
                            </div>
                            <span style="font-weight: 800; font-size: 0.775rem; color: var(--blue-600); white-space: nowrap;">
                                {{ $pmb->aktif_count }} Mentee
                            </span>
                        </div>
                    @endforeach
                @endif
            </div>

            {{-- 3. Tenggat Selesai Magang (Offboarding Watchdog) --}}
            @if(!$upcomingOffboardingList->isEmpty())
                <div style="margin-bottom: 1rem;">
                    <span style="font-size: 0.75rem; font-weight: 700; color: var(--amber-700); text-transform: uppercase; margin-bottom: 0.35rem; display: block;">Offboarding Watchdog (Peringatan Selesai H-14)</span>
                    @foreach($upcomingOffboardingList->take(2) as $offboard)
                        @php
                            $pen = $offboard['penempatan'];
                            $days = $offboard['days_remaining'];
                        @endphp
                        <div style="padding: 0.55rem 0.75rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.25); border-radius: 8px; margin-bottom: 0.4rem; display: flex; justify-content: space-between; align-items: center;">
                            <div style="font-size: 0.8125rem;">
                                <strong style="color: var(--neutral-900);">{{ $pen->peserta?->nama }}</strong>
                                <span style="color: var(--neutral-600); display: block; font-size: 0.7rem;">{{ $pen->bidang?->nama_bidang ?? '-' }}</span>
                            </div>
                            <span class="x_badge x_badge_warning">
                                {{ $days }} Hari Lagi
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- 4. Akses Modul Cepat --}}
            <div>
                <span style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-500); text-transform: uppercase; margin-bottom: 0.35rem; display: block;">
                    Akses Modul Administrasi <span class="sr-only">Timeline Gelombang Magang Offboarding Watchdog Peserta Selesai Segera</span>
                </span>
                <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                    <a href="{{ route('admin.waiting-list.index') }}" style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0.75rem; background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 8px; color: var(--neutral-800); text-decoration: none; font-size: 0.8125rem; font-weight: 600;">
                        <span>Antrean Waiting List</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </a>
                    <a href="{{ route('admin.penempatan.index') }}" style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0.75rem; background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 8px; color: var(--neutral-800); text-decoration: none; font-size: 0.8125rem; font-weight: 600;">
                        <span>Penempatan & Pembimbing</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </a>
                    <a href="{{ route('admin.sertifikat.index') }}" style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0.75rem; background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 8px; color: var(--neutral-800); text-decoration: none; font-size: 0.8125rem; font-weight: 600;">
                        <span>Penerbitan Sertifikat Kelulusan &bull; Hub Sertifikat</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </a>
                </div>
            </div>
        </x-gentelella.x-panel>
    </div>
</div>
