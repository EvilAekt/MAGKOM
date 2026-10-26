{{-- ==========================================================================
   Header & Atmospheric Hero Banner - Dashboard Peserta Magang (MagKom Sleman)
   ========================================================================== --}}
<!-- 1. Gentelella Page Title Bar -->
<x-gentelella.page-title
    title="Dashboard Peserta"
    description="Pantau progres magang, penugasan bimbingan, pengisian logbook harian, dan evaluasi hasil."
>
    @if($fase === 'aktif' && $penempatan)
        <x-slot:actions>
            <a href="{{ route('peserta.logbook.index') }}" class="btn btn-sm btn-primary" style="display: inline-flex; align-items: center; gap: 0.4rem; background: #0641CC; color: #ffffff; border-radius: 8px; font-weight: 700; padding: 0.5rem 1rem; text-decoration: none; box-shadow: 0 2px 6px rgba(6, 65, 204, 0.2);">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                + Isi Logbook Mingguan
            </a>
            <a href="{{ route('peserta.tugas.index') }}" class="btn btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem; background: #ffffff; color: var(--neutral-700, #334155); border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; font-weight: 600; padding: 0.5rem 1rem; text-decoration: none;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                Lihat Daftar Tugas
            </a>
        </x-slot:actions>
    @endif
</x-gentelella.page-title>

{{-- 2. Hero Greeting Banner (Atmospheric Sleman Blue) --}}
@if($fase === 'aktif' && $penempatan)
    <div class="dashboard-hero">
        <div class="hero-content">
            <div class="hero-greeting">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                    <span class="hero-status-pill">● Status Penempatan: Magang Aktif</span>
                </div>
                <h1>Selamat Datang, {{ $user->nama ?? $user->name }}! 👋</h1>
                <h2 class="hero-placement-title">{{ $penempatan->bidang->nama_bidang ?? 'Dinas Komunikasi dan Informatika Kabupaten Sleman' }}</h2>
                
                <div class="hero-meta-badges">
                    <div class="hero-meta-chip">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>Pembimbing Lapangan: <strong>{{ $penempatan->pembimbing->nama ?? $penempatan->pembimbing->name ?? 'Belum Ditugaskan' }}</strong></span>
                    </div>
                    <div class="hero-meta-chip">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Periode: {{ \Carbon\Carbon::parse($penempatan->tgl_mulai ?? $penempatan->tanggal_mulai)->isoFormat('D MMM Y') }} &ndash; {{ \Carbon\Carbon::parse($penempatan->tgl_selesai ?? $penempatan->tanggal_selesai)->isoFormat('D MMM Y') }}</span>
                    </div>
                </div>
            </div>

            <div class="hero-meta">
                <div class="hero-date-badge">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</span>
                </div>
                <a href="{{ route('peserta.penempatan.surat-diterima.pratinjau', $penempatan) }}" class="hero-glass-btn" target="_blank">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-1.943-9.542-7z"></path>
                    </svg>
                    Pratinjau SK Diterima
                </a>
            </div>
        </div>
    </div>

    {{-- Banner Alert Catatan Perhatian --}}
    @if($logbookPerluRevisi > 0)
        <x-gentelella.callout type="warning" title="Tindak Lanjut Diperlukan">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; width: 100%;">
                <div>
                    Terdapat <strong>{{ $logbookPerluRevisi }}</strong> laporan logbook mingguan yang memerlukan perbaikan dari pembimbing lapangan.
                </div>
                <a href="{{ route('peserta.logbook.index') }}" class="btn btn-sm btn-warning" style="font-weight: 700; text-decoration: none; border-radius: 6px;">
                    Perbaiki Logbook Sekarang &rarr;
                </a>
            </div>
        </x-gentelella.callout>
    @endif
@else
    <div class="dashboard-hero">
        <div class="hero-content">
            <div class="hero-greeting">
                <h1>Selamat Datang, {{ $user->nama ?? $user->name }}! 👋</h1>
                <p>
                    Selamat datang di portal terpadu magang Dinas Komunikasi dan Informatika Kabupaten Sleman. Pantau status pendaftaran, kegiatan harian, tugas, dan evaluasi hasil kerja Anda secara terpusat.
                </p>
            </div>
            <div class="hero-meta">
                <div class="hero-date-badge">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</span>
                </div>
                <span class="hero-role-pill">Peran: Peserta Magang</span>
            </div>
        </div>
    </div>
@endif
