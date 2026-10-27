{{-- ==========================================================================
   Clean Welcome Card - Dashboard Peserta Magang
   Desain Modern Clean SaaS (Linear / Shadcn UI Inspired)
   ========================================================================== --}}
@if($fase === 'aktif' && $penempatan)
    <div class="saas-card" style="padding: 24px 28px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
            <div style="max-width: 650px;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                    <span class="saas-badge-success" style="display: inline-flex; align-items: center; gap: 6px; background: #f0fdf4; color: #16a34a; font-size: 0.775rem; font-weight: 700; padding: 4px 10px; border-radius: 9999px; border: 1px solid #bbf7d0;">
                        <span class="saas-dot-success" style="width: 6px; height: 6px; border-radius: 50%; background: #16a34a;"></span>
                        Status Penempatan: Magang Aktif
                    </span>
                    <span class="saas-header-sep" style="color: var(--neutral-300, #cbd5e1);">&bull;</span>
                    <span class="saas-header-date" style="font-size: 0.8125rem; color: var(--neutral-500, #64748b);">
                        {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
                    </span>
                </div>

                <h1 class="saas-header-title" style="font-family: var(--font-display); font-size: 1.5rem; font-weight: 700; color: var(--neutral-900, #0f172a); margin: 0 0 6px 0; line-height: 1.3;">
                    Halo, {{ $user->nama ?? $user->name }} 👋
                </h1>
                
                <p class="saas-header-desc" style="font-size: 0.875rem; color: var(--neutral-600, #475569); margin: 0 0 10px 0; line-height: 1.5;">
                    Peserta Magang di <strong>{{ $penempatan->bidang->nama_bidang ?? 'Dinas Komunikasi dan Informatika Kabupaten Sleman' }}</strong>
                </p>

                <div class="saas-header-periode" style="display: inline-flex; align-items: center; gap: 8px; font-size: 0.8125rem; color: var(--neutral-600, #475569); background: var(--neutral-100, #f8fafc); padding: 5px 12px; border-radius: 8px; border: 1px solid var(--neutral-200, #e2e8f0);">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    <span>Periode: <strong>{{ \Carbon\Carbon::parse($penempatan->tgl_mulai ?? $penempatan->tanggal_mulai)->isoFormat('D MMMM Y') }} &ndash; {{ \Carbon\Carbon::parse($penempatan->tgl_selesai ?? $penempatan->tanggal_selesai)->isoFormat('D MMMM Y') }}</strong></span>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 10px;">
                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                    <a href="{{ route('peserta.tugas.index') }}" class="saas-btn-secondary">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        <span>Lihat Tugas</span>
                    </a>

                    <a href="{{ route('peserta.logbook.index') }}" class="saas-btn-primary" style="cursor: pointer; text-decoration: none;" title="Isi Logbook Mingguan">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        <span>+ Isi Logbook Mingguan</span>
                    </a>
                </div>

                <a href="{{ route('peserta.penempatan.surat-diterima.pratinjau', $penempatan) }}" target="_blank" class="saas-link-sk" style="font-size: 0.8125rem; font-weight: 600; color: #2563eb; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; padding: 4px 8px; border-radius: 6px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Unduh SK Magang (PDF) &rarr;</span>
                </a>
            </div>
        </div>
    </div>
@else
    <div class="saas-card" style="padding: 24px 28px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <div>
                <div class="saas-badge-portal" style="display: inline-flex; align-items: center; gap: 6px; background: #eff6ff; color: #2563eb; font-size: 0.775rem; font-weight: 700; padding: 4px 10px; border-radius: 9999px; border: 1px solid #bfdbfe; margin-bottom: 8px;">
                    Portal Pendaftaran & Seleksi Magang
                </div>
                <h1 class="saas-header-title" style="font-family: var(--font-display); font-size: 1.5rem; font-weight: 700; color: var(--neutral-900, #0f172a); margin: 0 0 6px 0; line-height: 1.3;">
                    Halo, {{ $user->nama ?? $user->name }} 👋
                </h1>
                <p class="saas-header-desc" style="font-size: 0.875rem; color: var(--neutral-600, #475569); margin: 0; line-height: 1.5; max-width: 680px;">
                    Selamat datang di portal magang Dinas Komunikasi dan Informatika Kabupaten Sleman. Pantau alur pendaftaran, verifikasi berkas, dan hasil seleksi penempatan Anda secara transparan.
                </p>
            </div>
            <div class="saas-header-date" style="font-size: 0.8125rem; color: var(--neutral-500, #64748b); font-weight: 500;">
                {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
            </div>
        </div>
    </div>
@endif
