{{-- ==========================================================================
   Fase 3: Magang Aktif (Sedang Berjalan) - Dashboard Peserta
   ========================================================================== --}}
@if($penempatan)
    @php
        $persenTugas = $totalTugas > 0 ? min(100, round(($tugasSelesai / $totalTugas) * 100)) : 0;
        
        $tglMulaiCarbon = $penempatan ? \Carbon\Carbon::parse($penempatan->tgl_mulai ?? $penempatan->tanggal_mulai)->startOfDay() : null;
        $tglSelesaiCarbon = $penempatan ? \Carbon\Carbon::parse($penempatan->tgl_selesai ?? $penempatan->tanggal_selesai)->startOfDay() : null;
        $totalHariMagang = ($tglMulaiCarbon && $tglSelesaiCarbon) ? max(1, $tglMulaiCarbon->diffInDays($tglSelesaiCarbon)) : 1;
        $hariBerjalan = ($tglMulaiCarbon) ? max(0, min($totalHariMagang, (int)$tglMulaiCarbon->diffInDays(now()->startOfDay()))) : 0;
        $persenHariMagang = min(100, round(($hariBerjalan / $totalHariMagang) * 100));
    @endphp

    {{-- Quick Action Bar --}}
    <div class="quick-actions-bar" style="display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;">
        <a href="{{ route('peserta.logbook.index') }}" class="btn btn-primary" style="box-shadow: 0 4px 12px rgba(7, 81, 255, 0.22); font-weight: 700;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            + Isi Logbook Mingguan
        </a>

        <a href="{{ route('peserta.tugas.index') }}" class="btn btn-secondary" style="font-weight: 600;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
            </svg>
            Lihat Daftar Tugas
        </a>

        <a href="{{ route('peserta.nilai.show') }}" class="btn btn-secondary" style="font-weight: 600;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
            </svg>
            Cek Lembar Nilai
        </a>
    </div>

    {{-- Gentelella Tile Count 4 Metrik Peserta Magang --}}
    <div class="row tile_count" style="margin-bottom: 24px;">
        <!-- Tile 1: Tugas Selesai -->
        <x-gentelella.tile-stat
            title="Tugas Selesai"
            value="{{ $tugasSelesai }} / {{ $totalTugas }}"
            icon="clipboard-check"
            trendText="{{ $persenTugas }}% Tuntas"
            trendType="{{ $persenTugas >= 80 ? 'success' : ($persenTugas >= 40 ? 'warning' : 'info') }}"
            subText="{{ $tugasBelum }} tugas dalam proses"
            col="col-md-3 col-sm-6"
        >
            <div class="micro-progress-bar" style="margin-top: 6px;">
                <div class="micro-progress-fill progress-fill-blue" style="width: {{ $persenTugas }}%;"></div>
            </div>
        </x-gentelella.tile-stat>

        <!-- Tile 2: Logbook Mingguan -->
        <x-gentelella.tile-stat
            title="Logbook Mingguan"
            value="{{ $totalLogbook }} Laporan"
            icon="book-open"
            trendText="{{ $logbookPerluRevisi > 0 ? $logbookPerluRevisi . ' Perlu Revisi' : 'Tuntas' }}"
            trendType="{{ $logbookPerluRevisi > 0 ? 'danger' : 'success' }}"
            subText="{{ $logbookPerluRevisi > 0 ? 'Perlu perbaikan segera' : 'Aktivitas tercatat rapi' }}"
            col="col-md-3 col-sm-6"
        >
            <div class="micro-progress-bar" style="margin-top: 6px;">
                <div class="micro-progress-fill progress-fill-green" style="width: 100%;"></div>
            </div>
        </x-gentelella.tile-stat>

        <!-- Tile 3: Sisa Hari Magang -->
        <x-gentelella.tile-stat
            title="Sisa Hari Magang"
            value="{{ $sisaHari !== null ? $sisaHari . ' Hari' : '-' }}"
            icon="calendar"
            trendText="Hari ke-{{ $hariBerjalan }}"
            trendType="{{ $sisaHari !== null && $sisaHari <= 7 ? 'warning' : 'info' }}"
            subText="Dari total {{ $totalHariMagang }} hari ({{ $persenHariMagang }}%)"
            col="col-md-3 col-sm-6"
        >
            <div class="micro-progress-bar" style="margin-top: 6px;">
                <div class="micro-progress-fill progress-fill-amber" style="width: {{ $persenHariMagang }}%;"></div>
            </div>
        </x-gentelella.tile-stat>

        <!-- Tile 4: Status Evaluasi -->
        <x-gentelella.tile-stat
            title="Status Evaluasi"
            value="{{ $isFinalNilai ? ($penilaian->predikat ?? 'A') . ' (' . number_format($penilaian->nilai_akhir, 1) . ')' : 'Dalam Proses' }}"
            icon="award"
            trendText="{{ $isFinalNilai ? 'Nilai final resmi' : '5 rubrik dinas' }}"
            trendType="{{ $isFinalNilai ? 'success' : 'neutral' }}"
            subText="{{ $isFinalNilai ? 'Evaluasi tuntas' : 'Penilaian pembimbing' }}"
            col="col-md-3 col-sm-6"
        >
            <div class="micro-progress-bar" style="margin-top: 6px;">
                <div class="micro-progress-fill progress-fill-purple" style="width: {{ $isFinalNilai ? 100 : 50 }}%;"></div>
            </div>
        </x-gentelella.tile-stat>
    </div>

    {{-- 2 Kolom Gentelella: Tugas Mendesak & Logbook Terbaru --}}
    <div class="row">
        {{-- Kolom Tugas --}}
        <div class="col-md-6 col-sm-12">
            <x-gentelella.x-panel
                title="Tugas Magang Mendesak"
                subtitle="Daftar tugas bimbingan yang membutuhkan penyelesaian segera"
            >
                <x-slot:actions>
                    <a href="{{ route('peserta.tugas.index') }}" style="font-size: 13px; color: #0641CC; text-decoration: none; font-weight: 600;">Lihat Semua &rarr;</a>
                </x-slot:actions>

                @if($tugasMendesak->isNotEmpty())
                    @foreach($tugasMendesak as $t)
                        @php
                            $deadlineCarbon = \Carbon\Carbon::parse($t->deadline)->startOfDay();
                            $diffDays = now()->startOfDay()->diffInDays($deadlineCarbon, false);
                        @endphp
                        <div class="item-row">
                            <div class="item-meta">
                                <h4>{{ $t->judul }}</h4>
                                <p style="display: flex; align-items: center; gap: 8px; margin-top: 4px;">
                                    @if($diffDays < 0)
                                        <span class="deadline-pill is-urgent">Lewat Tenggat</span>
                                    @elseif($diffDays === 0)
                                        <span class="deadline-pill is-urgent">⚡ Hari Ini</span>
                                    @elseif($diffDays === 1)
                                        <span class="deadline-pill is-urgent">⚠️ Besok</span>
                                    @elseif($diffDays <= 3)
                                        <span class="deadline-pill is-warning">⏳ {{ $diffDays }} Hari Lagi</span>
                                    @else
                                        <span class="deadline-pill">📅 {{ $deadlineCarbon->isoFormat('D MMM') }}</span>
                                    @endif
                                    <span>&bull; {{ $t->kategori ?? 'Tugas Lapangan' }}</span>
                                </p>
                            </div>
                            <div>
                                <a href="{{ route('peserta.tugas.show', $t) }}" class="btn btn-secondary" style="height: 32px; padding: 0 12px; font-size: 12px; white-space: nowrap;">
                                    Kerjakan
                                </a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="dashboard-empty-state">
                        <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin: 0 auto 12px auto; color: var(--green-600);">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="dashboard-empty-title">Semua tugas selesai!</p>
                        <p class="dashboard-empty-subtext">Tidak ada tugas yang menunggu pengerjaan saat ini.</p>
                    </div>
                @endif
            </x-gentelella.x-panel>
        </div>

        {{-- Kolom Logbook --}}
        <div class="col-md-6 col-sm-12">
            <x-gentelella.x-panel
                title="Logbook Mingguan Terkini"
                subtitle="Catatan kemajuan dan laporan kegiatan mingguan"
            >
                <x-slot:actions>
                    <a href="{{ route('peserta.logbook.index') }}" style="font-size: 13px; color: #0641CC; text-decoration: none; font-weight: 600;">Lihat Semua &rarr;</a>
                </x-slot:actions>

                @if($logbookTerbaru->isNotEmpty())
                    @foreach($logbookTerbaru as $lb)
                        @php
                            $statusValue = $lb->status->value ?? (string)$lb->status;
                        @endphp
                        <div class="item-row">
                            <div style="display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0;">
                                <span class="week-mini-badge">
                                    MG{{ $lb->minggu_ke ?? $loop->iteration }}
                                </span>
                                <div class="item-meta">
                                    <h4>Minggu ke-{{ $lb->minggu_ke ?? ($loop->iteration) }} &bull; {{ \Carbon\Carbon::parse($lb->tanggal)->isoFormat('D MMM Y') }}</h4>
                                    <p>{{ \Illuminate\Support\Str::limit($lb->kegiatan, 65) }}</p>
                                </div>
                            </div>
                            <div>
                                @if($statusValue === 'perlu_revisi' || $statusValue === 'ditolak')
                                    <span class="badge badge-danger">Perlu Revisi</span>
                                @elseif($statusValue === 'disetujui')
                                    <span class="badge badge-success">Disetujui</span>
                                @else
                                    <span class="badge badge-info">Terkirim</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="dashboard-empty-state">
                        <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin: 0 auto 12px auto; color: var(--neutral-400);">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <p class="dashboard-empty-title">Belum ada catatan logbook mingguan</p>
                        <p class="dashboard-empty-subtext">Catat aktivitas mingguan magang Anda secara rutin.</p>
                    </div>
                @endif
            </x-gentelella.x-panel>
        </div>
    </div>
@endif
