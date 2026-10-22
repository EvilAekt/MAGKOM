{{-- ==========================================================================
   Sticky Aside (Perlu Reviu Hari Ini & Aksi Cepat) - Dashboard Pembimbing
   ========================================================================== --}}
{{-- PANEL 1: PERLU REVIU HARI INI --}}
<x-gentelella.card style="padding: 1.25rem;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.85rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.65rem;">
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span style="font-family: monospace; font-size: 0.75rem; font-weight: 800; color: #d97706; background: rgba(245, 158, 11, 0.15); padding: 0.15rem 0.45rem; border-radius: 4px;">!</span>
            <h3 style="font-size: 0.95rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Perlu Reviu Hari Ini</h3>
        </div>
    </div>

    <ul class="cv-todo-list">
        @if($logbookPendingCount > 0)
            <li>
                <button type="button" onclick="switchPembimbingTab('tab-antrean')" class="cv-todo-item" style="width: 100%; border: none; background: transparent; cursor: pointer; text-align: left;">
                    <div style="display: flex; align-items: center; gap: 0.625rem;">
                        <span class="cv-todo-dot warning"></span>
                        <div>
                            <strong style="font-size: 0.8125rem; color: var(--neutral-900); display: block;">{{ $logbookPendingCount }} Logbook Masuk</strong>
                            <small style="color: var(--neutral-500); font-size: 0.7rem;">Menunggu Konfirmasi Pembimbing</small>
                        </div>
                    </div>
                    <span style="font-size: 0.75rem; font-weight: 700; color: var(--blue-600);">Reviu &rarr;</span>
                </button>
            </li>
        @endif

        @if($progresPendingCount > 0)
            <li>
                <button type="button" onclick="switchPembimbingTab('tab-antrean')" class="cv-todo-item" style="width: 100%; border: none; background: transparent; cursor: pointer; text-align: left;">
                    <div style="display: flex; align-items: center; gap: 0.625rem;">
                        <span class="cv-todo-dot info"></span>
                        <div>
                            <strong style="font-size: 0.8125rem; color: var(--neutral-900); display: block;">{{ $progresPendingCount }} Laporan Progres</strong>
                            <small style="color: var(--neutral-500); font-size: 0.7rem;">Menunggu Evaluasi Tugas</small>
                        </div>
                    </div>
                    <span style="font-size: 0.75rem; font-weight: 700; color: var(--blue-600);">Evaluasi &rarr;</span>
                </button>
            </li>
        @endif

        @if($tugasOverdueCount > 0)
            <li>
                <button type="button" onclick="switchPembimbingTab('tab-ringkasan')" class="cv-todo-item" style="width: 100%; border: none; background: transparent; cursor: pointer; text-align: left;">
                    <div style="display: flex; align-items: center; gap: 0.625rem;">
                        <span class="cv-todo-dot danger"></span>
                        <div>
                            <strong style="font-size: 0.8125rem; color: var(--neutral-900); display: block;">{{ $tugasOverdueCount }} Tugas Overdue</strong>
                            <small style="color: var(--neutral-500); font-size: 0.7rem;">Melewati Batas Tenggat Waktu</small>
                        </div>
                    </div>
                    <span style="font-size: 0.75rem; font-weight: 700; color: var(--blue-600);">Cek &rarr;</span>
                </button>
            </li>
        @endif

        @if($inactiveLogbookMentees->count() > 0)
            <li>
                <button type="button" onclick="switchPembimbingTab('tab-ringkasan')" class="cv-todo-item" style="width: 100%; border: none; background: transparent; cursor: pointer; text-align: left;">
                    <div style="display: flex; align-items: center; gap: 0.625rem;">
                        <span class="cv-todo-dot warning"></span>
                        <div>
                            <strong style="font-size: 0.8125rem; color: var(--neutral-900); display: block;">{{ $inactiveLogbookMentees->count() }} Mentee Pasif Logbook</strong>
                            <small style="color: var(--neutral-500); font-size: 0.7rem;">&ge; 2 Hari Belum Isi (BR-09)</small>
                        </div>
                    </div>
                    <span style="font-size: 0.75rem; font-weight: 700; color: var(--blue-600);">Ingatkan &rarr;</span>
                </button>
            </li>
        @endif

        @if($offboardingMentees->count() > 0)
            <li>
                <button type="button" onclick="switchPembimbingTab('tab-evaluasi')" class="cv-todo-item" style="width: 100%; border: none; background: transparent; cursor: pointer; text-align: left;">
                    <div style="display: flex; align-items: center; gap: 0.625rem;">
                        <span class="cv-todo-dot info"></span>
                        <div>
                            <strong style="font-size: 0.8125rem; color: var(--neutral-900); display: block;">{{ $offboardingMentees->count() }} Selesai &le; 14 Hari</strong>
                            <small style="color: var(--neutral-500); font-size: 0.7rem;">Siapkan Lembar Nilai Rubrik</small>
                        </div>
                    </div>
                    <span style="font-size: 0.75rem; font-weight: 700; color: var(--blue-600);">Nilai &rarr;</span>
                </button>
            </li>
        @endif

        @if($logbookPendingCount === 0 && $progresPendingCount === 0 && $tugasOverdueCount === 0 && $inactiveLogbookMentees->count() === 0)
            <li style="padding: 1.5rem 0.5rem; text-align: center; color: var(--neutral-500); font-size: 0.8125rem;">
                <span style="color: #10b981; font-weight: 700; display: block; margin-bottom: 0.25rem;">✓ Seluruh Reviu Tuntas</span>
                Tidak ada antrean tugas atau logbook tertunda saat ini.
            </li>
        @endif
    </ul>
</x-gentelella.card>

{{-- PANEL 2: AKSI CEPAT PEMBIMBING --}}
<x-gentelella.card style="padding: 1.25rem;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.85rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.65rem;">
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span style="font-family: monospace; font-size: 0.75rem; font-weight: 800; color: #0751ff; background: rgba(7, 81, 255, 0.15); padding: 0.15rem 0.45rem; border-radius: 4px;">⚡</span>
            <h3 style="font-size: 0.95rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Aksi Cepat Pembimbing</h3>
        </div>
    </div>

    <div class="cv-quick-grid">
        <a href="{{ route('pembimbing.tugas.create') }}" class="cv-quick-btn primary">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            <span>+ Beri Tugas Baru</span>
        </a>
        <a href="{{ route('pembimbing.agenda.index') }}" class="cv-quick-btn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            <span>Agenda Mentoring</span>
        </a>
        <a href="{{ route('pembimbing.logbook.index') }}" class="cv-quick-btn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
            <span>Pusat Logbook</span>
        </a>
        <a href="{{ route('pembimbing.penilaian.index') }}" class="cv-quick-btn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
            <span>Kelola Nilai</span>
        </a>
        <a href="{{ route('pembimbing.peserta.index') }}" class="cv-quick-btn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            <span>Daftar Mentee 360°</span>
        </a>
    </div>
</x-gentelella.card>
