{{-- ==========================================================================
   Fase 3: Magang Aktif (Sedang Berjalan) - Dashboard Peserta
   Desain Modern Clean SaaS (Linear / Shadcn UI Inspired)
   ========================================================================== --}}
@if($penempatan)
    {{-- Alert Catatan Revisi (Jika Ada) --}}
    @if($logbookPerluRevisi > 0)
        <div class="saas-alert-revisi" style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 12px; padding: 14px 18px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 1.1rem;">⚠️</span>
                <span class="saas-alert-revisi-text" style="font-size: 0.875rem; color: #92400e;">
                    Terdapat <strong>{{ $logbookPerluRevisi }}</strong> laporan logbook yang memerlukan perbaikan dari pembimbing.
                </span>
            </div>
            <a href="{{ route('peserta.logbook.index') }}" style="background: #d97706; color: #ffffff; font-size: 0.8125rem; font-weight: 600; text-decoration: none; border-radius: 8px; padding: 6px 14px; transition: background 0.15s ease;">
                Perbaiki Sekarang &rarr;
            </a>
        </div>
    @endif

    {{-- Grid Konten Utama: Tugas Bimbingan & Logbook Mingguan --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; width: 100%;">
        
        {{-- Card 1: Tugas Bimbingan --}}
        <div class="saas-card">
            <div class="saas-card-header">
                <div>
                    <h3 class="saas-item-title" style="font-family: var(--font-display); font-size: 0.95rem; font-weight: 700; color: var(--neutral-900, #0f172a); margin: 0;">
                        Tugas Bimbingan
                    </h3>
                    <p class="saas-card-subtitle" style="font-size: 0.775rem; color: var(--neutral-500, #64748b); margin: 2px 0 0 0;">
                        Tugas dalam proses pengerjaan
                    </p>
                </div>
                <a href="{{ route('peserta.tugas.index') }}" class="saas-link-more" style="font-family: var(--font-display); font-size: 0.8125rem; color: #2563eb; text-decoration: none; font-weight: 600;">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div style="padding: 16px 20px;">
                @if($tugasMendesak->isNotEmpty())
                    <div style="display: flex; flex-direction: column;">
                        @foreach($tugasMendesak as $t)
                            @php
                                $deadlineCarbon = \Carbon\Carbon::parse($t->deadline)->startOfDay();
                                $diffDays = now()->startOfDay()->diffInDays($deadlineCarbon, false);
                            @endphp
                            <div class="saas-list-item" style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid var(--neutral-150, #f1f5f9); gap: 12px;">
                                <div style="min-width: 0; flex: 1;">
                                    <h4 class="saas-item-title" style="font-family: var(--font-display); font-size: 0.875rem; font-weight: 600; color: var(--neutral-900, #0f172a); margin: 0 0 4px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $t->judul }}
                                    </h4>
                                    <div class="saas-item-meta" style="display: flex; align-items: center; gap: 8px; font-size: 0.75rem; color: var(--neutral-500, #64748b);">
                                        <span>Tenggat: <strong>{{ $deadlineCarbon->isoFormat('D MMM Y') }}</strong></span>
                                        <span>&bull;</span>
                                        <span class="saas-tag-category" style="background: var(--neutral-100, #f1f5f9); padding: 1px 6px; border-radius: 4px;">{{ $t->kategori ?? 'Tugas Magang' }}</span>
                                    </div>
                                </div>
                                <a href="{{ route('peserta.tugas.show', $t) }}" class="saas-btn-detail" style="padding: 6px 12px; font-size: 0.775rem; font-weight: 600; color: var(--neutral-700, #334155); background: #ffffff; border: 1px solid var(--neutral-200, #e2e8f0); border-radius: 8px; text-decoration: none; white-space: nowrap; transition: all 0.15s ease;">
                                    Detail
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="saas-empty-box" style="text-align: center; padding: 32px 16px; color: var(--neutral-500, #64748b);">
                        <div class="saas-empty-icon-green" style="width: 40px; height: 40px; margin: 0 auto 10px auto; border-radius: 10px; background: #f0fdf4; color: #16a34a; display: flex; align-items: center; justify-content: center;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div class="saas-empty-box-title" style="font-family: var(--font-display); font-size: 0.875rem; font-weight: 600; color: var(--neutral-800, #1e293b);">Tidak Ada Tugas Tertunda</div>
                        <p style="font-size: 0.775rem; margin: 2px 0 0 0;">Semua tugas bimbingan telah terselesaikan.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Card 2: Logbook Mingguan --}}
        <div class="saas-card">
            <div class="saas-card-header">
                <div>
                    <h3 class="saas-item-title" style="font-family: var(--font-display); font-size: 0.95rem; font-weight: 700; color: var(--neutral-900, #0f172a); margin: 0;">
                        Logbook Mingguan
                    </h3>
                    <p class="saas-card-subtitle" style="font-size: 0.775rem; color: var(--neutral-500, #64748b); margin: 2px 0 0 0;">
                        Catatan aktivitas dan jurnal berkala
                    </p>
                </div>
                <a href="{{ route('peserta.logbook.index') }}" class="saas-link-more" style="font-family: var(--font-display); font-size: 0.8125rem; color: #2563eb; text-decoration: none; font-weight: 600;">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div style="padding: 16px 20px;">
                @if($logbookTerbaru->isNotEmpty())
                    <div style="display: flex; flex-direction: column;">
                        @foreach($logbookTerbaru as $lb)
                            @php
                                $statusValue = $lb->status->value ?? (string)$lb->status;
                            @endphp
                            <div class="saas-list-item" style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid var(--neutral-150, #f1f5f9); gap: 12px;">
                                <div style="min-width: 0; flex: 1;">
                                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 3px;">
                                        <span class="saas-week-tag" style="font-size: 0.725rem; font-weight: 700; color: #1b3860; background: #eff6ff; padding: 2px 6px; border-radius: 4px;">
                                            Minggu {{ $lb->minggu_ke ?? $loop->iteration }}
                                        </span>
                                        <span class="saas-item-meta" style="font-size: 0.75rem; color: var(--neutral-500, #64748b);">
                                            {{ \Carbon\Carbon::parse($lb->tanggal)->isoFormat('D MMM Y') }}
                                        </span>
                                    </div>
                                    <p style="font-size: 0.8125rem; color: var(--neutral-700, #334155); margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ \Illuminate\Support\Str::limit($lb->kegiatan, 55) }}
                                    </p>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    @if($statusValue === 'perlu_revisi' || $statusValue === 'ditolak')
                                        <span class="saas-badge-revisi" style="font-size: 0.725rem; font-weight: 600; color: #dc2626; background: #fef2f2; border: 1px solid #fecaca; padding: 3px 8px; border-radius: 9999px;">Perlu Revisi</span>
                                    @elseif($statusValue === 'disetujui')
                                        <span class="saas-badge-approved" style="font-size: 0.725rem; font-weight: 600; color: #16a34a; background: #f0fdf4; border: 1px solid #bbf7d0; padding: 3px 8px; border-radius: 9999px;">Disetujui</span>
                                    @else
                                        <span class="saas-badge-submitted" style="font-size: 0.725rem; font-weight: 600; color: #2563eb; background: #eff6ff; border: 1px solid #bfdbfe; padding: 3px 8px; border-radius: 9999px;">Terkirim</span>
                                    @endif
                                    <a href="{{ route('peserta.logbook.index') }}" class="saas-btn-detail" style="padding: 5px 10px; font-size: 0.75rem; font-weight: 600; color: var(--neutral-700, #334155); background: #ffffff; border: 1px solid var(--neutral-200, #e2e8f0); border-radius: 6px; text-decoration: none; white-space: nowrap; transition: all 0.15s ease;">
                                        Lihat
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="saas-empty-box" style="text-align: center; padding: 32px 16px; color: var(--neutral-500, #64748b);">
                        <div class="saas-empty-icon-blue" style="width: 40px; height: 40px; margin: 0 auto 10px auto; border-radius: 10px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        <div class="saas-empty-box-title" style="font-family: var(--font-display); font-size: 0.875rem; font-weight: 600; color: var(--neutral-800, #1e293b);">Belum Ada Logbook</div>
                        <p style="font-size: 0.775rem; margin: 2px 0 0 0;">Catat aktivitas berkala Anda secara rutin.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif
