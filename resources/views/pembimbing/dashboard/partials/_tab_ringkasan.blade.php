{{-- ==========================================================================
   Tab 1: Ringkasan Utama - Dashboard Pembimbing
   ========================================================================== --}}
<!-- Peringatan Kedisiplinan Logbook (BR-09 Compliance Alert) -->
@if($inactiveLogbookMentees->count() > 0)
    <div style="background: #fff7ed; border: 1px solid #fed7aa; border-left: 4px solid #ea580c; border-radius: 12px; padding: 1.25rem 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ea580c" stroke-width="2">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
                <h3 style="font-size: 0.95rem; font-weight: 700; color: #9a3412; margin: 0;">
                    Peringatan: {{ $inactiveLogbookMentees->count() }} Mentee Belum Mengisi Logbook &ge; 2 Hari Kerja
                </h3>
            </div>
            <span style="font-size: 0.75rem; color: #c2410c;">Maksimal pengisian mundur adalah 3 hari (BR-09). Segera ingatkan mentee sebelum entri logbook hangus.</span>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 0.85rem;">
            @foreach($inactiveLogbookMentees as $item)
                @php
                    $m = $item['mentee'];
                @endphp
                <div style="background: #ffffff; border-radius: 10px; border: 1px solid #ffedd5; padding: 0.75rem 1rem; display: flex; justify-content: space-between; align-items: center; gap: 0.75rem;">
                    <div>
                        <div style="font-size: 0.875rem; font-weight: 600; color: var(--neutral-900, #0F172A);">
                            {{ $m->peserta?->nama }}
                        </div>
                        <div style="font-size: 0.75rem; color: var(--neutral-600, #475569); margin-top: 0.1rem;">
                            {{ $m->peserta?->profil?->institusi ?? '-' }}
                        </div>
                        <div style="font-size: 0.7rem; color: #ea580c; font-weight: 600; margin-top: 0.25rem;">
                            Tidak mengisi: {{ $item['hari_pasif'] }} hari &bull; Terakhir: {{ $item['terakhir_isi'] }}
                        </div>
                    </div>
                    <a href="{{ route('pembimbing.peserta.show', $m->id) }}" style="padding: 0.35rem 0.65rem; background: #ea580c; color: #ffffff; font-size: 0.75rem; font-weight: 600; border-radius: 6px; text-decoration: none; white-space: nowrap;">
                        Cek Profil
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endif

<!-- Panel 1: Komponen Kartu Tugas Terpadu dalam Gaya Gentelella x_panel -->
<x-gentelella.x-panel
    title="Tenggat Waktu Tugas Mendekati Batas Akhir & Penugasan Mentee"
    subtitle="Pemantauan penugasan aktif, tenggat waktu (&le; {{ $deadlineMendekatiHari }} hari), dan antrean evaluasi progres mentee."
>
    <x-slot:actions>
        <div class="task-quick-filters">
            <button type="button" class="task-filter-btn active" onclick="filterDashboardTasks('semua', this)">
                Semua <span class="filter-badge">{{ $tugasPrioritasPembimbing->count() }}</span>
            </button>
            <button type="button" class="task-filter-btn" onclick="filterDashboardTasks('mendesak', this)">
                Mendesak <span class="filter-badge">{{ $tugasPrioritasPembimbing->filter(fn($t) => $t->is_overdue || $t->is_due_soon)->count() }}</span>
            </button>
            <button type="button" class="task-filter-btn" onclick="filterDashboardTasks('reviu', this)">
                Perlu Reviu <span class="filter-badge">{{ $tugasPrioritasPembimbing->where('pending_progres_count', '>', 0)->count() }}</span>
            </button>
            <a href="{{ route('pembimbing.tugas.index') }}" style="font-size: 0.8125rem; font-weight: 600; color: #0641CC; text-decoration: none; margin-left: 8px;">
                Lihat Seluruh Tugas &rarr;
            </a>
        </div>
    </x-slot:actions>

    <!-- Daftar Item Tugas -->
    @if($tugasPrioritasPembimbing->isNotEmpty())
        <div id="dashboard-tasks-container">
            @foreach($tugasPrioritasPembimbing as $tugas)
                @php
                    $diff = $tugas->diff_days;
                    $hasPendingReview = $tugas->pending_progres_count > 0;
                    $isUrgent = $tugas->is_overdue || ($tugas->deadline && $diff <= 3);
                    $menteeName = $tugas->penempatan?->peserta?->nama ?? 'Peserta Magang';
                    $initials = strtoupper(substr($menteeName, 0, 2));
                @endphp
                <div class="task-item-row" data-is-urgent="{{ $isUrgent ? '1' : '0' }}" data-has-pending="{{ $hasPendingReview ? '1' : '0' }}">
                    <div class="task-item-left">
                        <div class="mentee-avatar-chip" title="Mentee: {{ $menteeName }}">
                            {{ $initials }}
                        </div>
                        <div class="task-item-meta">
                            <h4>
                                <a href="{{ route('pembimbing.tugas.show', $tugas->id) }}">
                                    {{ $tugas->judul }}
                                </a>
                            </h4>
                            <div class="task-mentee-info">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                <span>Mentee: <strong>{{ $menteeName }}</strong></span>
                                <span>&bull;</span>
                                <span>{{ $tugas->penempatan?->peserta?->profil?->institusi ?? 'Institusi Belum Terisi' }}</span>
                            </div>
                            <div class="task-badges-row">
                                <!-- Deadline Pill Standar Peserta Magang -->
                                @if($tugas->is_overdue)
                                    <span class="deadline-pill is-urgent">
                                        <span>⚠️</span> Lewat Tenggat
                                    </span>
                                @elseif($tugas->is_due_today)
                                    <span class="deadline-pill is-urgent">
                                        <span>⚡</span> Tenggat Hari Ini!
                                    </span>
                                @elseif($tugas->is_due_tomorrow)
                                    <span class="deadline-pill is-urgent">
                                        <span>⚠️</span> Besok Jatuh Tempo
                                    </span>
                                @elseif($tugas->is_due_soon)
                                    <span class="deadline-pill is-warning">
                                        <span>⏳</span> Sisa {{ $diff }} Hari
                                    </span>
                                @elseif($tugas->deadline)
                                    <span class="deadline-pill">
                                        <span>📅</span> {{ \Carbon\Carbon::parse($tugas->deadline)->isoFormat('D MMM Y') }}
                                    </span>
                                @else
                                    <span class="deadline-pill">Tanpa Tenggat</span>
                                @endif

                                <!-- Category Chip -->
                                <span class="task-category-chip">
                                    {{ $tugas->kategori ?? 'Tugas Lapangan' }}
                                </span>

                                <!-- Pending Review Highlight Badge -->
                                @if($hasPendingReview)
                                    <span style="font-size: 0.7rem; font-weight: 700; background: #EEF2FF; color: #4338CA; border: 1px solid #C7D2FE; padding: 2px 8px; border-radius: 999px; display: inline-flex; align-items: center; gap: 4px;">
                                        <span>📥</span> {{ $tugas->pending_progres_count }} Laporan Progres Menunggu Evaluasi
                                    </span>
                                @endif

                                <!-- Attachment / Reference Links -->
                                @if($tugas->file_lampiran)
                                    @php $badge = $tugas->badge_color_lampiran; @endphp
                                    <a href="{{ route('pembimbing.tugas.lampiran', $tugas->id) }}" title="Unduh Berkas Panduan: {{ $tugas->nama_file_lampiran }}" style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.7rem; font-weight: 600; padding: 0.15rem 0.45rem; border-radius: 4px; background: {{ $badge['bg'] }}; color: {{ $badge['color'] }}; text-decoration: none; border: 1px solid {{ $badge['border'] }};">
                                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                        {{ $badge['label'] }} ({{ $tugas->ukuran_format }})
                                    </a>
                                @endif
                                @if($tugas->link_referensi)
                                    <a href="{{ $tugas->link_referensi }}" target="_blank" rel="noopener noreferrer" title="Buka Tautan Acuan: {{ $tugas->link_referensi }}" style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.7rem; font-weight: 600; padding: 0.15rem 0.45rem; border-radius: 4px; background: #eff6ff; color: #1d4ed8; text-decoration: none; border: 1px solid #bfdbfe;">
                                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                                        Tautan Acuan ↗
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi Cepat Kontekstual -->
                    <div>
                        @if($hasPendingReview)
                            <a href="{{ route('pembimbing.tugas.show', $tugas->id) }}" class="btn-task-action btn-task-action-primary" style="box-shadow: 0 4px 12px rgba(6, 65, 204, 0.22);">
                                <span>Evaluasi Progres</span>
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </a>
                        @else
                            <a href="{{ route('pembimbing.tugas.show', $tugas->id) }}" class="btn-task-action btn-task-action-secondary">
                                <span>Reviu & Detail</span>
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Empty state untuk filter client-side jika tidak ada item yang cocok -->
        <div id="task-filter-empty-state" style="display: none; text-align: center; padding: 2rem 1rem; color: var(--neutral-500, #64748B);">
            <p style="font-size: 0.875rem; font-weight: 600; color: var(--neutral-700, #334155); margin: 0 0 4px 0;">Tidak ada tugas pada kategori filter ini.</p>
            <p style="font-size: 0.775rem; color: var(--neutral-500, #64748B); margin: 0;">Silakan pilih filter "Semua" untuk melihat seluruh penugasan mentee.</p>
        </div>
    @else
        <div style="text-align: center; padding: 3rem 1.5rem; color: var(--neutral-500, #64748B);">
            <svg width="44" height="44" fill="none" stroke="#10b981" stroke-width="1.5" viewBox="0 0 24 24" style="margin: 0 auto 12px auto;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--neutral-900, #0F172A); margin: 0 0 4px 0;">Semua tugas mentee terkendali!</h4>
            <p style="font-size: 0.8125rem; color: var(--neutral-500, #64748B); margin: 0 0 1rem 0;">Tidak ada penugasan yang terlambat atau laporan progres yang tertunda saat ini.</p>
            <a href="{{ route('pembimbing.tugas.create') }}" style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.5rem 1.15rem; background: #0641CC; color: #ffffff; border-radius: 8px; font-size: 0.8125rem; font-weight: 600; text-decoration: none; box-shadow: 0 2px 6px rgba(6, 65, 204, 0.2);">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                + Berikan Tugas Baru
            </a>
        </div>
    @endif
</x-gentelella.x-panel>

<!-- Panel 2: Aktivitas Mentoring Minggu Ini (B11) -->
<x-gentelella.x-panel
    title="Aktivitas Mentoring Minggu Ini"
    subtitle="Logbook, progres tugas, dan sesi bimbingan lapangan terbaru"
>
    @if(!empty($aktivitasMingguIni) && $aktivitasMingguIni->isNotEmpty())
        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            @foreach($aktivitasMingguIni as $act)
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 1rem; border-radius: 8px; background: #F8FAFC; border: 1px solid var(--neutral-200, #E2E8F0); gap: 1rem; flex-wrap: wrap;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <span style="font-size: 0.7rem; font-weight: 700; padding: 0.2rem 0.55rem; border-radius: 6px; background: {{ $act['badge_bg'] }}; color: {{ $act['badge_color'] }}; text-transform: uppercase;">
                            {{ $act['badge'] }}
                        </span>
                        <div>
                            <div style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-900, #0F172A);">
                                {{ $act['judul'] }}
                            </div>
                            <div style="font-size: 0.75rem; color: var(--neutral-600, #475569); margin-top: 0.1rem;">
                                {{ $act['keterangan'] }}
                            </div>
                        </div>
                    </div>
                    <span style="font-size: 0.75rem; color: var(--neutral-500, #64748B); font-weight: 500;">
                        {{ \Carbon\Carbon::parse($act['waktu'])->diffForHumans() }}
                    </span>
                </div>
            @endforeach
        </div>
    @else
        <div style="text-align: center; padding: 1.5rem; color: var(--neutral-500, #64748B); font-size: 0.8125rem;">
            Belum ada aktivitas baru dari peserta bimbingan minggu ini.
        </div>
    @endif
</x-gentelella.x-panel>

<!-- Pelacak Peserta Mendekati Batas Akhir Magang (Off-boarding Tracker <= 14 Hari) -->
@if($offboardingMentees->count() > 0)
    <div style="background: #fffbeb; border: 1px solid #fef3c7; border-left: 4px solid #f59e0b; border-radius: 12px; padding: 1.25rem 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <h3 style="font-size: 0.95rem; font-weight: 700; color: #92400e; margin: 0;">
                    Perhatian: {{ $offboardingMentees->count() }} Mentee Mendekati Batas Selesai Magang (&le; 14 Hari)
                </h3>
            </div>
            <span style="font-size: 0.75rem; color: #b45309;">Sesuai regulasi, segera susun penilaian rubrik final sebelum periode berakhir (BR-10, BR-11).</span>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1rem;">
            @foreach($offboardingMentees as $mentee)
                @php
                    $sisaHari = \Carbon\Carbon::today()->diffInDays($mentee->tgl_selesai, false);
                    $statusNilai = $mentee->penilaian?->status->value ?? 'belum';
                @endphp
                <div style="background: #ffffff; border-radius: 10px; border: 1px solid #fde68a; padding: 0.875rem 1rem; display: flex; justify-content: space-between; align-items: center; gap: 0.75rem;">
                    <div>
                        <div style="font-size: 0.875rem; font-weight: 600; color: var(--neutral-900, #0F172A);">
                            {{ $mentee->peserta?->nama }}
                        </div>
                        <div style="font-size: 0.75rem; color: var(--neutral-600, #475569); margin-top: 0.15rem;">
                            {{ $mentee->peserta?->profil?->institusi ?? 'Institusi Belum Diisi' }}
                        </div>
                        <div style="display: flex; gap: 0.5rem; align-items: center; margin-top: 0.35rem;">
                            <span style="font-size: 0.7rem; font-weight: 600; padding: 0.1rem 0.4rem; border-radius: 4px; background: {{ $sisaHari <= 3 ? '#fee2e2' : '#fef3c7' }}; color: {{ $sisaHari <= 3 ? '#b91c1c' : '#92400e' }};">
                                {{ $sisaHari < 0 ? 'Berakhir ' . abs($sisaHari) . ' hari lalu' : ($sisaHari == 0 ? 'Berakhir Hari Ini' : 'Tersisa ' . $sisaHari . ' hari') }}
                            </span>
                            <span style="font-size: 0.7rem; color: var(--neutral-500, #64748B);">
                                Nilai: <strong>{{ ucfirst($statusNilai) }}</strong>
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('pembimbing.penilaian.show', $mentee->id) }}" style="padding: 0.4rem 0.75rem; background: #0641CC; color: #ffffff; font-size: 0.75rem; font-weight: 600; border-radius: 6px; text-decoration: none; text-align: center; white-space: nowrap;">
                        Lembar Nilai
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endif

<!-- Row 2 Kolom Antrean Mendesak -->
<div class="row">
    <!-- Kolom Kiri: Antrean Progres Tugas -->
    <div class="col-md-6 col-sm-12">
        <x-gentelella.x-panel
            title="Antrean Progres Tugas ({{ $pendingProgresLogs->count() }})"
            subtitle="Laporan kemajuan tugas menunggu umpan balik pembimbing"
        >
            <x-slot:actions>
                <button type="button" onclick="switchPembimbingTab('tab-antrean')" style="background: transparent; border: none; font-size: 0.8rem; font-weight: 600; color: #0641CC; cursor: pointer;">
                    Buka Semua &rarr;
                </button>
            </x-slot:actions>

            @forelse($pendingProgresLogs as $progres)
                <div style="padding: 0.875rem 0; border-bottom: 1px solid var(--neutral-100, #F1F5F9); display: flex; justify-content: space-between; align-items: center; gap: 1rem;">
                    <div style="display: flex; flex-direction: column; gap: 0.2rem; flex: 1;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="font-size: 0.875rem; font-weight: 600; color: var(--neutral-900, #0F172A);">
                                {{ $progres->tugas?->penempatan?->peserta?->nama ?? 'Peserta' }}
                            </span>
                            <span style="font-size: 0.7rem; background: #e0e7ff; color: #3730a3; padding: 0.1rem 0.45rem; border-radius: 4px; font-weight: 600;">
                                {{ $progres->tugas?->kategori ?? 'Tugas' }}
                            </span>
                        </div>
                        <span style="font-size: 0.8rem; color: var(--neutral-700, #334155); font-weight: 500;">
                            {{ $progres->tugas?->judul }}
                        </span>
                        <span style="font-size: 0.75rem; color: var(--neutral-500, #64748B); line-height: 1.4;">
                            {{ \Illuminate\Support\Str::limit($progres->catatan ?? $progres->deskripsi ?? 'Laporan progres kerja', 90) }}
                        </span>
                        <span style="font-size: 0.7rem; color: var(--neutral-400, #94A3B8);">
                            Diajukan {{ $progres->created_at?->diffForHumans() }}
                        </span>
                    </div>
                    <a href="{{ route('pembimbing.tugas.show', $progres->tugas_id) }}" style="padding: 0.45rem 0.85rem; background: rgba(79, 70, 229, 0.1); color: #4f46e5; font-size: 0.75rem; font-weight: 600; border-radius: 6px; text-decoration: none; white-space: nowrap;">
                        Evaluasi
                    </a>
                </div>
            @empty
                <div style="padding: 2.5rem 1rem; text-align: center; color: var(--neutral-500, #64748B); display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="1.5">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    <span style="font-size: 0.875rem; font-weight: 600; color: var(--neutral-700, #334155);">Tidak Ada Antrean Progres</span>
                    <span style="font-size: 0.75rem;">Seluruh laporan progres tugas dari mentee telah dievaluasi.</span>
                </div>
            @endforelse
        </x-gentelella.x-panel>
    </div>

    <!-- Kolom Kanan: Antrean Reviu Logbook -->
    <div class="col-md-6 col-sm-12">
        <x-gentelella.x-panel
            title="Antrean Reviu Logbook ({{ $pendingLogbooks->count() }})"
            subtitle="Logbook harian menunggu validasi pembimbing"
        >
            <x-slot:actions>
                <button type="button" onclick="switchPembimbingTab('tab-antrean')" style="background: transparent; border: none; font-size: 0.8rem; font-weight: 600; color: #0641CC; cursor: pointer;">
                    Buka Semua &rarr;
                </button>
            </x-slot:actions>

            @forelse($pendingLogbooks as $logbook)
                <div style="padding: 0.875rem 0; border-bottom: 1px solid var(--neutral-100, #F1F5F9); display: flex; justify-content: space-between; align-items: center; gap: 1rem;">
                    <div style="display: flex; flex-direction: column; gap: 0.2rem; flex: 1;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="font-size: 0.875rem; font-weight: 600; color: var(--neutral-900, #0F172A);">
                                {{ $logbook->penempatan?->peserta?->nama ?? 'Peserta' }}
                            </span>
                            <span style="font-size: 0.7rem; background: #fef3c7; color: #92400e; padding: 0.1rem 0.45rem; border-radius: 4px; font-weight: 600;">
                                {{ $logbook->tanggal?->format('d M Y') }}
                            </span>
                        </div>
                        <span style="font-size: 0.8rem; color: var(--neutral-700, #334155); line-height: 1.4;">
                            {{ \Illuminate\Support\Str::limit($logbook->kegiatan, 90) }}
                        </span>
                        <div style="display: flex; align-items: center; gap: 0.75rem; margin-top: 0.15rem;">
                            @if($logbook->link_laporan)
                                <span style="font-size: 0.7rem; color: #0641CC; display: inline-flex; align-items: center; gap: 0.2rem;">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                                    Tautan Lampir
                                </span>
                            @endif
                            @if($logbook->file_laporan)
                                <span style="font-size: 0.7rem; color: #059669; display: inline-flex; align-items: center; gap: 0.2rem;">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                    Berkas PDF
                                </span>
                            @endif
                            <span style="font-size: 0.7rem; color: var(--neutral-400, #94A3B8);">
                                Masuk {{ $logbook->created_at?->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('pembimbing.logbook.index', ['penempatan_id' => $logbook->penempatan_id, 'tanggal' => $logbook->tanggal?->toDateString()]) }}" style="padding: 0.45rem 0.85rem; background: rgba(217, 119, 6, 0.1); color: #d97706; font-size: 0.75rem; font-weight: 600; border-radius: 6px; text-decoration: none; white-space: nowrap;">
                        Periksa
                    </a>
                </div>
            @empty
                <div style="padding: 2.5rem 1rem; text-align: center; color: var(--neutral-500, #64748B); display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="1.5">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    <span style="font-size: 0.875rem; font-weight: 600; color: var(--neutral-700, #334155);">Tidak Ada Antrean Logbook</span>
                    <span style="font-size: 0.75rem;">Semua entri logbook harian peserta bimbingan telah direviu.</span>
                </div>
            @endforelse
        </x-gentelella.x-panel>
    </div>
</div>

<!-- Panel 3: Ringkasan Cepat Mentee Bimbingan Aktif -->
<x-gentelella.x-panel
    title="Daftar Mentee Bimbingan Aktif (Ringkasan)"
    subtitle="Total {{ $totalMenteeAktif }} peserta sedang aktif menjalani magang di bawah bimbingan Anda."
>
    <x-slot:actions>
        <button type="button" onclick="switchPembimbingTab('tab-mentee')" style="background: transparent; border: none; font-size: 0.8rem; font-weight: 600; color: #0641CC; cursor: pointer;">
            Buka Monitoring Lengkap 360° &rarr;
        </button>
    </x-slot:actions>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
            <thead>
                <tr style="border-bottom: 1px solid var(--neutral-200, #E2E8F0); background: #f8fafc; color: var(--neutral-600, #475569); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                    <th style="padding: 0.875rem 1.5rem;">Peserta Magang</th>
                    <th style="padding: 0.875rem 1rem;">Institusi & Jurusan</th>
                    <th style="padding: 0.875rem 1rem;">Periode Magang</th>
                    <th style="padding: 0.875rem 1rem;">Progres Durasi</th>
                    <th style="padding: 0.875rem 1rem;">Status Nilai</th>
                    <th style="padding: 0.875rem 1.5rem; text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($menteeAktifList as $mentee)
                    @php
                        $tglMulai = $mentee->tgl_mulai;
                        $tglSelesai = $mentee->tgl_selesai;
                        $totalHari = $tglMulai && $tglSelesai ? max(1, $tglMulai->diffInDays($tglSelesai)) : 1;
                        $hariBerjalan = $tglMulai ? max(0, \Carbon\Carbon::today()->diffInDays($tglMulai, false)) : 0;
                        $persenHari = min(100, (int) round(($hariBerjalan / $totalHari) * 100));
                        $statusNilai = $mentee->penilaian?->status->value ?? 'belum';
                    @endphp
                    <tr style="border-bottom: 1px solid var(--neutral-100, #F1F5F9); transition: background 0.15s ease;">
                        <td style="padding: 1rem 1.5rem;">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div style="width: 36px; height: 36px; border-radius: 50%; background: #e0e7ff; color: #3730a3; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.875rem;">
                                    {{ strtoupper(substr($mentee->peserta?->nama ?? 'P', 0, 2)) }}
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: var(--neutral-900, #0F172A);">
                                        {{ $mentee->peserta?->nama }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--neutral-500, #64748B);">
                                        {{ $mentee->peserta?->email }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 1rem 1rem;">
                            <div style="font-weight: 500; color: var(--neutral-800, #1E293B);">
                                {{ $mentee->peserta?->profil?->institusi ?? '-' }}
                            </div>
                            <div style="font-size: 0.75rem; color: var(--neutral-500, #64748B);">
                                {{ $mentee->peserta?->profil?->jurusan?->nama_jurusan ?? '-' }}
                            </div>
                        </td>
                        <td style="padding: 1rem 1rem; font-size: 0.8rem; color: var(--neutral-700, #334155); white-space: nowrap;">
                            {{ $tglMulai?->format('d M Y') }} s/d {{ $tglSelesai?->format('d M Y') }}
                        </td>
                        <td style="padding: 1rem 1rem; min-width: 140px;">
                            <div style="display: flex; justify-content: space-between; font-size: 0.7rem; color: var(--neutral-500, #64748B); margin-bottom: 0.25rem;">
                                <span>Hari ke-{{ $hariBerjalan }}</span>
                                <span>{{ $persenHari }}%</span>
                            </div>
                            <div style="height: 6px; width: 100%; background: var(--neutral-200, #E2E8F0); border-radius: 999px; overflow: hidden;">
                                <div style="height: 100%; width: {{ $persenHari }}%; background: #0641CC; border-radius: 999px;"></div>
                            </div>
                        </td>
                        <td style="padding: 1rem 1rem;">
                            <span style="font-size: 0.75rem; font-weight: 600; padding: 0.2rem 0.6rem; border-radius: 999px; background: {{ $statusNilai === 'final' ? '#dcfce7' : ($statusNilai === 'draft' ? '#fef3c7' : '#f1f5f9') }}; color: {{ $statusNilai === 'final' ? '#166534' : ($statusNilai === 'draft' ? '#92400e' : '#475569') }};">
                                {{ ucfirst($statusNilai) }}
                            </span>
                        </td>
                        <td style="padding: 1rem 1.5rem; text-align: right; white-space: nowrap;">
                            <div style="display: flex; gap: 0.4rem; justify-content: flex-end;">
                                <a href="{{ route('pembimbing.peserta.show', $mentee->id) }}" style="padding: 0.35rem 0.65rem; background: rgba(6, 65, 204, 0.08); color: #0641CC; font-size: 0.75rem; font-weight: 600; border-radius: 6px; text-decoration: none;">
                                    Profil 360°
                                </a>
                                <a href="{{ route('pembimbing.penilaian.show', $mentee->id) }}" style="padding: 0.35rem 0.65rem; background: #0641CC; color: #ffffff; font-size: 0.75rem; font-weight: 600; border-radius: 6px; text-decoration: none;">
                                    Nilai
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: 2rem; text-align: center; color: var(--neutral-500, #64748B);">
                            Belum ada peserta magang yang aktif dibimbing saat ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-gentelella.x-panel>
