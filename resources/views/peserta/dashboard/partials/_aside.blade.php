{{-- ==========================================================================
   Sticky Aside (Pembimbing & Aksi Cepat) - Dashboard Peserta Magang
   ========================================================================== --}}
@if($fase === 'aktif' && $penempatan)
    {{-- CARD 1: PROFIL PEMBIMBING LAPANGAN --}}
    <x-gentelella.card style="padding: 1.25rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.85rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.65rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="font-family: monospace; font-size: 0.75rem; font-weight: 800; color: #0751ff; background: rgba(7, 81, 255, 0.15); padding: 0.15rem 0.45rem; border-radius: 4px;">👤</span>
                <h3 style="font-size: 0.95rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Pembimbing Lapangan</h3>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 0.85rem; margin-bottom: 1rem;">
            <div style="width: 44px; height: 44px; border-radius: 50%; background: #0641CC; color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1rem; flex-shrink: 0;">
                {{ strtoupper(substr($penempatan->pembimbing->nama ?? $penempatan->pembimbing->name ?? 'P', 0, 2)) }}
            </div>
            <div style="min-width: 0;">
                <h4 style="font-size: 0.9rem; font-weight: 700; color: var(--neutral-900); margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    {{ $penempatan->pembimbing->nama ?? $penempatan->pembimbing->name ?? 'Belum Ditugaskan' }}
                </h4>
                <p style="font-size: 0.75rem; color: var(--neutral-500); margin: 2px 0 0 0;">
                    {{ $penempatan->bidang->nama_bidang ?? 'Dinas Kominfo Sleman' }}
                </p>
            </div>
        </div>

        <div style="background: var(--neutral-100); border-radius: 8px; padding: 0.75rem; font-size: 0.775rem; color: var(--neutral-700);">
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.35rem;">
                <span style="color: var(--neutral-500);">Email Dinas:</span>
                <span style="font-weight: 600;">{{ $penempatan->pembimbing->email ?? '-' }}</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span style="color: var(--neutral-500);">Ruang Kerja:</span>
                <span style="font-weight: 600;">Gedung Smart Room / Bidang</span>
            </div>
        </div>
    </x-gentelella.card>

    {{-- CARD 2: AKSI CEPAT PESERTA --}}
    <x-gentelella.card style="padding: 1.25rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.85rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.65rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="font-family: monospace; font-size: 0.75rem; font-weight: 800; color: #10b981; background: rgba(16, 185, 129, 0.15); padding: 0.15rem 0.45rem; border-radius: 4px;">⚡</span>
                <h3 style="font-size: 0.95rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Aksi Cepat Peserta</h3>
            </div>
        </div>

        <div class="cv-quick-grid">
            <a href="{{ route('peserta.logbook.index') }}" class="cv-quick-btn primary" data-action="open-drawer" data-drawer="#drawerQuickLogbook">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                <span>+ Isi Logbook</span>
            </a>
            <a href="{{ route('peserta.tugas.index') }}" class="cv-quick-btn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                <span>Daftar Tugas</span>
            </a>
            <a href="{{ route('peserta.nilai.show') }}" class="cv-quick-btn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                <span>Lembar Nilai</span>
            </a>
            <a href="{{ route('peserta.penempatan.surat-diterima.pratinjau', $penempatan) }}" target="_blank" class="cv-quick-btn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                <span>Surat Diterima</span>
            </a>
        </div>
    </x-gentelella.card>

    {{-- CARD 3: KETENTUAN LOGBOOK BR-09 --}}
    <x-gentelella.card style="padding: 1.25rem;">
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
            <span style="color: #d97706; font-size: 0.9rem;">⚠️</span>
            <h4 style="font-size: 0.85rem; font-weight: 700; color: var(--neutral-900); margin: 0;">Aturan Logbook Harian (BR-09)</h4>
        </div>
        <p style="font-size: 0.775rem; color: var(--neutral-600); line-height: 1.45; margin: 0;">
            Pengisian logbook maksimal mundur <strong>3 hari kalender</strong>. Segera catat aktivitas kerja harian agar tidak hangus dan terdokumentasi dalam lembar evaluasi.
        </p>
    </x-gentelella.card>
@else
    {{-- CARD 1: PUSAT BANTUAN PENDAFTARAN --}}
    <x-gentelella.card style="padding: 1.25rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.85rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.65rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="font-family: monospace; font-size: 0.75rem; font-weight: 800; color: #0751ff; background: rgba(7, 81, 255, 0.15); padding: 0.15rem 0.45rem; border-radius: 4px;">ℹ️</span>
                <h3 style="font-size: 0.95rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Helpdesk Pendaftaran</h3>
            </div>
        </div>

        <p style="font-size: 0.8rem; color: var(--neutral-600); line-height: 1.45; margin: 0 0 0.85rem 0;">
            Butuh bantuan mengenai alur pendaftaran magang atau verifikasi berkas dinas?
        </p>

        <div style="background: var(--neutral-100); border-radius: 8px; padding: 0.75rem; font-size: 0.775rem; color: var(--neutral-700); display: flex; flex-direction: column; gap: 0.35rem;">
            <div><strong>Layanan Verifikasi:</strong> Senin - Jumat (08:00 - 15:30 WIB)</div>
            <div><strong>SLA Verifikasi:</strong> Maksimal 3 Hari Kerja</div>
            <div><strong>Lokasi:</strong> Dinas Kominfo Kabupaten Sleman</div>
        </div>
    </x-gentelella.card>

    {{-- CARD 2: AKSI CEPAT --}}
    <x-gentelella.card style="padding: 1.25rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.85rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.65rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="font-family: monospace; font-size: 0.75rem; font-weight: 800; color: #10b981; background: rgba(16, 185, 129, 0.15); padding: 0.15rem 0.45rem; border-radius: 4px;">⚡</span>
                <h3 style="font-size: 0.95rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Aksi Cepat</h3>
            </div>
        </div>

        <div class="cv-quick-grid">
            <a href="{{ route('peserta.profil.show') }}" class="cv-quick-btn primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                <span>Kelola Profil</span>
            </a>
            <a href="{{ route('peserta.pendaftaran.index') }}" class="cv-quick-btn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 4v16m8-8H4"></path></svg>
                <span>Pendaftaran</span>
            </a>
            <a href="{{ route('peserta.pengaturan.index') }}" class="cv-quick-btn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                <span>Pengaturan</span>
            </a>
        </div>
    </x-gentelella.card>
@endif
