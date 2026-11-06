{{-- ==========================================================================
   Fase 3: Magang Aktif (Sedang Berjalan) - Dashboard Peserta
   Desain Modern Clean SaaS & DS-MagKom (Gentelella Parity)
   ========================================================================== --}}
@php
    /** @var \App\Models\Penempatan|null $penempatan */
    /** @var int $logbookPerluRevisi */
    /** @var \Illuminate\Support\Collection<int, \App\Models\TugasMagang> $tugasMendesak */
    /** @var \Illuminate\Support\Collection<int, \App\Models\Logbook> $logbookTerbaru */
@endphp
@if($penempatan)
    {{-- Alert Catatan Revisi (Jika Ada) --}}
    @if($logbookPerluRevisi > 0)
        <div class="saas-alert-revisi">
            <div class="saas-alert-revisi-left">
                <span class="saas-alert-icon" aria-hidden="true">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                </span>
                <span class="saas-alert-revisi-text">
                    Terdapat <strong>{{ $logbookPerluRevisi }}</strong> laporan logbook yang memerlukan perbaikan dari pembimbing.
                </span>
            </div>
            <a href="{{ route('peserta.logbook.index') }}" class="saas-btn-revisi">
                Perbaiki Sekarang &rarr;
            </a>
        </div>
    @endif

    {{-- Grid Konten Utama: Tugas Bimbingan & Logbook Mingguan --}}
    <div class="saas-phase-grid">
        
        {{-- Card 1: Tugas Bimbingan / Tugas Magang --}}
        <div class="saas-card x_panel">
            <div class="saas-card-header x_title" style="display: flex; justify-content: space-between; align-items: center; gap: 16px; padding: 16px 20px;">
                <div style="min-width: 0;">
                    <h3 class="saas-item-title" style="font-size: 0.975rem; font-weight: 700; color: var(--neutral-900, #0f172a); margin: 0;">
                        Tugas Magang
                    </h3>
                    <p class="saas-card-subtitle" style="font-size: 0.775rem; color: var(--neutral-500, #64748b); margin: 3px 0 0 0;">
                        Tugas bimbingan dalam proses pengerjaan
                    </p>
                </div>
                <a href="{{ route('peserta.tugas.index') }}" class="saas-link-more" title="Lihat Semua Tugas">
                    <span>Lihat Semua</span>
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </a>
            </div>

            <div class="saas-card-body-compact" style="padding: 6px 20px 14px 20px;">
                @if($tugasMendesak->isNotEmpty())
                    <div class="saas-list-container">
                        @foreach($tugasMendesak as $t)
                            @php
                                /** @var \App\Models\TugasMagang $t */
                                $deadlineCarbon = $t->deadline ? \Carbon\Carbon::parse($t->deadline)->startOfDay() : null;
                                $diffDays = $deadlineCarbon ? \Carbon\Carbon::now()->startOfDay()->diffInDays($deadlineCarbon, false) : null;
                            @endphp
                            <div class="saas-list-item" style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid var(--neutral-150, #f1f5f9); gap: 16px;">
                                <div style="min-width: 0; flex: 1;">
                                    <h4 class="saas-item-title-heading" style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-900, #0f172a); margin: 0 0 5px 0;" title="{{ $t->judul }}">
                                        {{ $t->judul }}
                                    </h4>
                                    <div class="saas-item-meta" style="display: flex; align-items: center; gap: 8px; font-size: 0.75rem; color: var(--neutral-500, #64748b); flex-wrap: wrap;">
                                        <span style="display: inline-flex; align-items: center; gap: 4px;">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                            <span>Tenggat: <strong>{{ $deadlineCarbon ? $deadlineCarbon->isoFormat('D MMM Y') : 'Tanpa tenggat' }}</strong></span>
                                        </span>
                                        <span class="saas-meta-sep">&bull;</span>
                                        <span class="saas-tag-category" style="padding: 2px 7px; font-size: 0.725rem; border-radius: 4px; background: var(--neutral-100, #f1f5f9); color: #475569; font-weight: 500;">{{ $t->kategori ?? 'Tugas Magang' }}</span>
                                    </div>
                                </div>
                                <a href="{{ route('peserta.tugas.show', $t) }}" class="saas-btn-detail" style="padding: 5px 14px; font-size: 0.775rem; flex-shrink: 0;">
                                    Detail
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="saas-empty-box" style="padding: 24px 12px;">
                        <div class="saas-empty-icon-green" aria-hidden="true">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div class="saas-empty-box-title">Tidak Ada Tugas Tertunda</div>
                        <p class="saas-empty-box-sub">Semua tugas bimbingan telah terselesaikan.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Card 2: Logbook Mingguan / Logbook Mingguan Terkini --}}
        <div class="saas-card x_panel">
            <div class="saas-card-header x_title" style="display: flex; justify-content: space-between; align-items: center; gap: 16px; padding: 16px 20px;">
                <div style="min-width: 0;">
                    <h3 class="saas-item-title" style="font-size: 0.975rem; font-weight: 700; color: var(--neutral-900, #0f172a); margin: 0;">
                        Logbook Mingguan Terkini
                    </h3>
                    <p class="saas-card-subtitle" style="font-size: 0.775rem; color: var(--neutral-500, #64748b); margin: 3px 0 0 0;">
                        Catatan aktivitas dan jurnal berkala
                    </p>
                </div>
                <a href="{{ route('peserta.logbook.index') }}" class="saas-link-more" title="Lihat Semua Logbook">
                    <span>Lihat Semua</span>
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </a>
            </div>

            <div class="saas-card-body-compact" style="padding: 6px 20px 14px 20px;">
                @if($logbookTerbaru->isNotEmpty())
                    <div class="saas-list-container">
                        @foreach($logbookTerbaru as $lb)
                            @php
                                $statusValue = $lb->status->value ?? (string)$lb->status;
                            @endphp
                            <div class="saas-list-item" style="display: flex; justify-content: space-between; align-items: center; padding: 11px 0; border-bottom: 1px solid var(--neutral-150, #f1f5f9); gap: 16px;">
                                <div style="min-width: 0; flex: 1;">
                                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px; flex-wrap: wrap;">
                                        <span class="saas-week-tag" style="padding: 2px 7px; font-size: 0.725rem; font-weight: 700;">
                                            Minggu {{ $lb->minggu_ke ?? $loop->iteration }}
                                        </span>
                                        <span style="font-size: 0.75rem; color: var(--neutral-500, #64748b); font-weight: 500;">
                                            {{ \Carbon\Carbon::parse($lb->tanggal)->isoFormat('D MMMM Y') }}
                                        </span>
                                        @if($statusValue === 'perlu_revisi' || $statusValue === 'ditolak')
                                            <span class="saas-badge-revisi" style="padding: 2px 8px; font-size: 0.7rem;">Perlu Revisi</span>
                                        @elseif($statusValue === 'disetujui')
                                            <span class="saas-badge-approved" style="padding: 2px 8px; font-size: 0.7rem;">Disetujui</span>
                                        @else
                                            <span class="saas-badge-submitted" style="padding: 2px 8px; font-size: 0.7rem;">Terkirim</span>
                                        @endif
                                    </div>
                                    <p style="margin: 0; font-size: 0.8125rem; color: var(--neutral-700, #334155); line-height: 1.45; white-space: normal; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-align: left;" title="{{ $lb->kegiatan }}">
                                        {{ $lb->kegiatan }}
                                    </p>
                                </div>
                                <a href="{{ route('peserta.logbook.index') }}" class="saas-btn-detail" style="padding: 5px 12px; font-size: 0.75rem; flex-shrink: 0;">
                                    Lihat
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="saas-empty-box" style="padding: 24px 12px;">
                        <div class="saas-empty-icon-blue" aria-hidden="true">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        <div class="saas-empty-box-title">Belum Ada Logbook</div>
                        <p class="saas-empty-box-sub">Catat aktivitas berkala Anda secara rutin.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif
