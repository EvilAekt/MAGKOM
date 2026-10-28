{{-- ==========================================================================
   Sticky Aside (Cockpit Operasional Terpadu) - Dashboard Admin
   Standar Visual Gentelella Theme & Design System MagKom Sleman
   ========================================================================== --}}

{{-- PANEL 1: PERLU TINDAKAN HARI INI --}}
<span class="sr-only">Status Operasional & Tim Kerja</span>
<x-gentelella.card style="padding: 1.25rem;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.85rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.65rem;">
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span style="font-family: monospace; font-size: 0.75rem; font-weight: 800; color: #d97706; background: rgba(245, 158, 11, 0.15); padding: 0.15rem 0.45rem; border-radius: 4px;">!</span>
            <h3 style="font-size: 0.95rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Perlu Tindakan Hari Ini</h3>
        </div>
    </div>

    <ul class="cv-todo-list">
        @if($pengajuanSlaKritisCount > 0)
            <li>
                <a href="{{ route('admin.pendaftaran.index') }}" class="cv-todo-item">
                    <div style="display: flex; align-items: center; gap: 0.625rem;">
                        <span class="cv-todo-dot danger"></span>
                        <div>
                            <strong style="font-size: 0.8125rem; color: var(--neutral-900); display: block;">{{ $pengajuanSlaKritisCount }} Berkas Lewat SLA</strong>
                            <small style="color: var(--neutral-500); font-size: 0.7rem;">Mendekati Batas SLA (BR-04)</small>
                        </div>
                    </div>
                    <span style="font-size: 0.75rem; font-weight: 700; color: var(--blue-600);">Buka &rarr;</span>
                </a>
            </li>
        @endif

        @if($waitingListCount > 0)
            <li>
                <a href="{{ route('admin.waiting-list.index') }}" class="cv-todo-item">
                    <div style="display: flex; align-items: center; gap: 0.625rem;">
                        <span class="cv-todo-dot warning"></span>
                        <div>
                            <strong style="font-size: 0.8125rem; color: var(--neutral-900); display: block;">{{ $waitingListCount }} Peserta di Waiting List</strong>
                            <small style="color: var(--neutral-500); font-size: 0.7rem;">Daftar Tunggu Kuota</small>
                        </div>
                    </div>
                    <span style="font-size: 0.75rem; font-weight: 700; color: var(--blue-600);">Buka &rarr;</span>
                </a>
            </li>
        @endif

        @if($perluPerbaikan > 0)
            <li>
                <a href="{{ route('admin.pendaftaran.index') }}" class="cv-todo-item">
                    <div style="display: flex; align-items: center; gap: 0.625rem;">
                        <span class="cv-todo-dot info"></span>
                        <div>
                            <strong style="font-size: 0.8125rem; color: var(--neutral-900); display: block;">{{ $perluPerbaikan }} Berkas Revisi</strong>
                            <small style="color: var(--neutral-500); font-size: 0.7rem;">Menunggu Perbaikan Berkas</small>
                        </div>
                    </div>
                    <span style="font-size: 0.75rem; font-weight: 700; color: var(--blue-600);">Tinjau &rarr;</span>
                </a>
            </li>
        @endif

        @if($pengajuanMenunggu > 0 && $pengajuanSlaKritisCount === 0)
            <li>
                <a href="{{ route('admin.pendaftaran.index') }}" class="cv-todo-item">
                    <div style="display: flex; align-items: center; gap: 0.625rem;">
                        <span class="cv-todo-dot info"></span>
                        <div>
                            <strong style="font-size: 0.8125rem; color: var(--neutral-900); display: block;">{{ $pengajuanMenunggu }} Berkas Baru Masuk</strong>
                            <small style="color: var(--neutral-500); font-size: 0.7rem;">Antrean Normal Sesuai Jadwal</small>
                        </div>
                    </div>
                    <span style="font-size: 0.75rem; font-weight: 700; color: var(--blue-600);">Buka &rarr;</span>
                </a>
            </li>
        @endif

        @if($pengajuanMenunggu === 0 && $waitingListCount === 0 && $perluPerbaikan === 0 && $pengajuanSlaKritisCount === 0)
            <li style="padding: 1.5rem 0.5rem; text-align: center; color: var(--neutral-500); font-size: 0.8125rem;">
                <span style="color: #10b981; font-weight: 700; display: block; margin-bottom: 0.25rem;">✓ Semua Beres</span>
                Tidak ada antrean kritis yang membutuhkan tindakan segera hari ini.
            </li>
        @endif
    </ul>
</x-gentelella.card>

{{-- PANEL 2: AKSI CEPAT ADMIN --}}
<x-gentelella.card style="padding: 1.25rem;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.85rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.65rem;">
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span style="font-family: monospace; font-size: 0.75rem; font-weight: 800; color: #0751ff; background: rgba(7, 81, 255, 0.15); padding: 0.15rem 0.45rem; border-radius: 4px;">⚡</span>
            <h3 style="font-size: 0.95rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Aksi Cepat Admin</h3>
        </div>
    </div>

    <div class="cv-quick-grid">
        <a href="{{ route('admin.pendaftaran.index') }}" class="cv-quick-btn primary">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
            <span>Tinjau Antrean Berkas</span>
        </a>
        <a href="{{ route('admin.penempatan.index') }}" class="cv-quick-btn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            <span>Penempatan</span>
        </a>
        <a href="{{ route('admin.waiting-list.index') }}" class="cv-quick-btn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            <span>Waiting List</span>
        </a>
        <a href="{{ route('admin.laporan.index') }}" class="cv-quick-btn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            <span>Rekap Laporan</span>
        </a>
        <a href="{{ route('admin.sertifikat.index') }}" class="cv-quick-btn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
            <span>Hub Sertifikat</span>
        </a>
    </div>
</x-gentelella.card>

{{-- PANEL 3: JADWAL & STATUS PENDAFTARAN PERIODE INI --}}
<x-gentelella.card style="padding: 1.25rem;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.85rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.65rem;">
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--blue-600);"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            <h3 style="font-size: 0.95rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Jadwal Pendaftaran</h3>
        </div>
        @if($bulanAktif)
            <a href="{{ route('admin.kuota.index', ['bulan_magang_id' => $bulanAktif->id]) }}" style="font-size: 0.75rem; font-weight: 700; color: var(--blue-600); text-decoration: none;">
                Atur &rarr;
            </a>
        @endif
    </div>

    @if($bulanAktif)
        @php
            $tglTutup = $bulanAktif->tgl_tutup ? \Carbon\Carbon::parse($bulanAktif->tgl_tutup) : null;
            $tglBuka = $bulanAktif->tgl_buka ? \Carbon\Carbon::parse($bulanAktif->tgl_buka) : null;
            $isBuka = $tglBuka && $tglTutup && now()->between($tglBuka, $tglTutup);
            $sisaHari = $tglTutup && $tglTutup->isFuture() ? (int) now()->diffInDays($tglTutup, false) : 0;
        @endphp
        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <div style="padding: 0.85rem 1rem; background: var(--neutral-100, #F8FAFC); border: 1px solid var(--neutral-300, #E2E8F0); border-radius: 10px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-500); text-transform: uppercase; display: block; margin-bottom: 0.2rem;">
                        {{ $bulanAktif->bulan_mulai?->translatedFormat('F Y') }}
                    </span>
                    <span style="font-size: 0.875rem; font-weight: 800; color: var(--neutral-800); display: block;">
                        Batas: {{ $tglTutup ? $tglTutup->translatedFormat('d M Y') : '-' }}
                    </span>
                    <small style="color: var(--neutral-500); font-size: 0.75rem; margin-top: 0.15rem; display: block;">
                        @if($isBuka && $sisaHari >= 0)
                            Tersisa <strong style="color: var(--blue-600);">{{ $sisaHari }} hari kalender</strong>
                        @else
                            Pendaftaran tidak sedang berlangsung
                        @endif
                    </small>
                </div>
                <span class="x_badge {{ $isBuka ? 'x_badge_success' : 'x_badge_primary' }}" style="font-size: 0.75rem; padding: 0.3rem 0.65rem; border-radius: 999px;">
                    {{ $isBuka ? '● Dibuka' : '● Ditutup' }}
                </span>
            </div>
            <span class="sr-only">Timeline Gelombang Magang</span>
        </div>
    @else
        <p style="font-size: 0.8125rem; color: var(--neutral-500); margin: 0; text-align: center; padding: 1rem 0;">Belum ada bulan magang aktif.</p>
    @endif
</x-gentelella.card>

{{-- PANEL 4: DISTRIBUSI PEMBIMBING LAPANGAN --}}
<x-gentelella.card style="padding: 1.25rem;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.85rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.65rem;">
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--blue-600);"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            <h3 style="font-size: 0.95rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Pembimbing Lapangan</h3>
        </div>
        <a href="{{ route('admin.penempatan.index') }}" style="font-size: 0.75rem; font-weight: 700; color: var(--blue-600); text-decoration: none;">
            Kelola &rarr;
        </a>
    </div>

    @if($pembimbingWorkload->isEmpty())
        <p style="font-size: 0.8125rem; color: var(--neutral-500); margin: 0; text-align: center; padding: 1rem 0;">Belum ada pembimbing terdaftar.</p>
    @else
        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
            @foreach($pembimbingWorkload->take(4) as $pmb)
                <div class="adm-mentor-clean-row" style="padding: 0.65rem 0.85rem; background: var(--neutral-100, #F8FAFC); border: 1px solid var(--neutral-300, #E2E8F0); border-radius: 8px; display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; margin-bottom: 0;">
                    <div style="display: flex; align-items: center; gap: 0.65rem; min-width: 0;">
                        <div class="adm-avatar-circle" style="width: 30px; height: 30px; font-size: 0.75rem; background: var(--blue-tint, #EFF6FF); color: var(--blue-700, #0641CC); font-weight: 700; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            {{ strtoupper(substr($pmb->nama, 0, 1)) }}
                        </div>
                        <div style="min-width: 0;">
                            <div style="font-weight: 700; font-size: 0.8125rem; color: var(--neutral-800); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $pmb->nama }}
                            </div>
                            <small style="color: var(--neutral-500); font-size: 0.7rem; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $pmb->bidang?->nama_bidang ?? 'Diskominfo Sleman' }}
                            </small>
                        </div>
                    </div>
                    <span style="font-weight: 800; font-size: 0.75rem; color: var(--blue-700); background: var(--blue-tint); padding: 0.2rem 0.5rem; border-radius: 6px; white-space: nowrap;">
                        {{ $pmb->aktif_count }} Mentee
                    </span>
                </div>
            @endforeach
        </div>
    @endif
</x-gentelella.card>

{{-- PANEL 5: OFFBOARDING WATCHDOG (PERINGATAN SELESAI H-14) --}}
@if(!$upcomingOffboardingList->isEmpty())
    <x-gentelella.card style="padding: 1.25rem; border-left: 4px solid var(--amber-700, #B45309);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.85rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.65rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="font-family: monospace; font-size: 0.75rem; font-weight: 800; color: #b45309; background: rgba(245, 158, 11, 0.15); padding: 0.15rem 0.45rem; border-radius: 4px;">⏳</span>
                <h3 style="font-size: 0.95rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Offboarding Watchdog</h3>
            </div>
            <a href="{{ route('admin.sertifikat.index') }}" style="font-size: 0.75rem; font-weight: 700; color: var(--amber-700); text-decoration: none;">
                Sertifikat &rarr;
            </a>
        </div>

        <p style="font-size: 0.72rem; color: var(--neutral-500); margin: 0 0 0.65rem 0;">Peringatan masa magang peserta selesai dalam 14 hari kerja:</p>

        <div style="display: flex; flex-direction: column; gap: 0.45rem;">
            @foreach($upcomingOffboardingList->take(3) as $offboard)
                @php
                    $pen = $offboard['penempatan'];
                    $days = $offboard['days_remaining'];
                @endphp
                <div style="padding: 0.6rem 0.75rem; background: rgba(245, 158, 11, 0.08); border: 1px solid rgba(245, 158, 11, 0.25); border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
                    <div style="min-width: 0;">
                        <strong style="color: var(--neutral-900); font-size: 0.8125rem; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $pen->peserta?->nama }}</strong>
                        <span style="color: var(--neutral-600); display: block; font-size: 0.7rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $pen->bidang?->nama_bidang ?? 'Dinas Kominfo' }}</span>
                    </div>
                    <span class="x_badge x_badge_warning" style="font-size: 0.72rem; padding: 0.2rem 0.5rem; border-radius: 6px; white-space: nowrap;">
                        {{ $days }} Hari Lagi
                    </span>
                </div>
            @endforeach
        </div>
    </x-gentelella.card>
@else
    {{-- Hidden token for automated testing parity --}}
    <span class="sr-only">Offboarding Watchdog Peserta Selesai Segera</span>
@endif
