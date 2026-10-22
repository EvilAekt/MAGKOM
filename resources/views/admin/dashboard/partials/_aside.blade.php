{{-- ==========================================================================
   Sticky Aside (Perlu Tindakan Hari Ini & Aksi Cepat) - Dashboard Admin
   ========================================================================== --}}
{{-- PANEL 1: PERLU TINDAKAN HARI INI --}}
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
                            <small style="color: var(--neutral-500); font-size: 0.7rem;">Antrean Verifikasi (BR-04)</small>
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

        @if($pengajuanMenunggu === 0 && $waitingListCount === 0 && $perluPerbaikan === 0)
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
