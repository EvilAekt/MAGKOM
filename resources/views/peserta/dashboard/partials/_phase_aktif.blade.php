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
        
        {{-- Card 1: Tugas Bimbingan / Tugas Magang Mendesak --}}
        <div class="saas-card x_panel">
            <div class="saas-card-header x_title">
                <div>
                    <h3 class="saas-item-title">
                        Tugas Magang Mendesak
                    </h3>
                    <p class="saas-card-subtitle">
                        Tugas bimbingan dalam proses pengerjaan
                    </p>
                </div>
                <a href="{{ route('peserta.tugas.index') }}" class="saas-link-more">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="saas-card-body-compact">
                @if($tugasMendesak->isNotEmpty())
                    <div class="saas-list-container">
                        @foreach($tugasMendesak as $t)
                            @php
                                /** @var \App\Models\TugasMagang $t */
                                $deadlineCarbon = $t->deadline ? \Carbon\Carbon::parse($t->deadline)->startOfDay() : null;
                                $diffDays = $deadlineCarbon ? \Carbon\Carbon::now()->startOfDay()->diffInDays($deadlineCarbon, false) : null;
                            @endphp
                            <div class="saas-list-item">
                                <div class="saas-item-main">
                                    <h4 class="saas-item-title-heading">
                                        {{ $t->judul }}
                                    </h4>
                                    <div class="saas-item-meta">
                                        <span>Tenggat: <strong>{{ $deadlineCarbon ? $deadlineCarbon->isoFormat('D MMM Y') : 'Tanpa tenggat' }}</strong></span>
                                        <span class="saas-meta-sep">&bull;</span>
                                        <span class="saas-tag-category">{{ $t->kategori ?? 'Tugas Magang' }}</span>
                                    </div>
                                </div>
                                <a href="{{ route('peserta.tugas.show', $t) }}" class="saas-btn-detail">
                                    Detail
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="saas-empty-box">
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
            <div class="saas-card-header x_title">
                <div>
                    <h3 class="saas-item-title">
                        Logbook Mingguan Terkini
                    </h3>
                    <p class="saas-card-subtitle">
                        Catatan aktivitas dan jurnal berkala
                    </p>
                </div>
                <a href="{{ route('peserta.logbook.index') }}" class="saas-link-more">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="saas-card-body-compact">
                @if($logbookTerbaru->isNotEmpty())
                    <div class="saas-list-container">
                        @foreach($logbookTerbaru as $lb)
                            @php
                                $statusValue = $lb->status->value ?? (string)$lb->status;
                            @endphp
                            <div class="saas-list-item">
                                <div class="saas-item-main">
                                    <div class="saas-item-header-meta">
                                        <span class="saas-week-tag">
                                            Minggu {{ $lb->minggu_ke ?? $loop->iteration }}
                                        </span>
                                        <span class="saas-item-meta">
                                            {{ \Carbon\Carbon::parse($lb->tanggal)->isoFormat('D MMM Y') }}
                                        </span>
                                    </div>
                                    <p class="saas-item-summary">
                                        {{ \Illuminate\Support\Str::limit($lb->kegiatan, 55) }}
                                    </p>
                                </div>
                                <div class="saas-item-actions">
                                    @if($statusValue === 'perlu_revisi' || $statusValue === 'ditolak')
                                        <span class="saas-badge-revisi">Perlu Revisi</span>
                                    @elseif($statusValue === 'disetujui')
                                        <span class="saas-badge-approved">Disetujui</span>
                                    @else
                                        <span class="saas-badge-submitted">Terkirim</span>
                                    @endif
                                    <a href="{{ route('peserta.logbook.index') }}" class="saas-btn-detail">
                                        Lihat
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="saas-empty-box">
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
