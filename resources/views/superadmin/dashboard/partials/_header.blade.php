{{-- ==========================================================================
   Header & Statusline - Dashboard Eksekutif Tata Kelola SuperAdmin
   Standar Visual Gentelella Theme (MagKom Sleman Navy #0641CC)
   ========================================================================== --}}
@php
    /** @var \App\Models\BulanMagang|null $bulanAktif */
    /** @var mixed $statusPublik */

    $tglBukaFormatted = $bulanAktif && $bulanAktif->tgl_buka ? \Carbon\Carbon::parse($bulanAktif->tgl_buka)->format('d M') : '01 Nov';
    $tglTutupFormatted = $bulanAktif && $bulanAktif->tgl_tutup ? \Carbon\Carbon::parse($bulanAktif->tgl_tutup)->format('d M Y') : '15 Nov ' . date('Y');
    
    $statusTextLabel = match($statusPublik->value ?? $statusPublik ?? 'tersedia') {
        'tersedia' => 'Pendaftaran Tersedia',
        'segera' => 'Segera Dibuka',
        default => 'Pendaftaran Ditutup',
    };
@endphp

<div class="sa-hero__head gentelella-sa-head">
    <div>
        <div class="sa-hero-eyebrow">
            <span>{{ strtoupper(\Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y · HH:mm [WIB]')) }}</span>
            <span class="sa-sr-only">Dashboard Eksekutif Tata Kelola MagKom KOMINFO PORTAL</span>
        </div>
        <h1 class="sa-hero-title">Dashboard SuperAdmin</h1>
        <span class="sa-sr-only">
            Akses penuh: kelola pengguna, role, rubrik, audit log, konfigurasi, dan seluruh modul Administrasi, Pembimbing, serta Peserta.
        </span>
        
        <div class="sa-stamp">
            <span class="dot"></span>
            <span>PENDAFTARAN DIBUKA · {{ strtoupper($tglBukaFormatted) }}–{{ strtoupper($tglTutupFormatted) }}</span>
        </div>
    </div>

    <div class="sa-hero-actions">
        <a href="{{ route('admin.laporan.index') }}" class="btn-hero-ghost">
            <x-sa.icon name="download" :size="15" />
            <span>Ekspor laporan</span>
        </a>
        <a href="{{ route('superadmin.staf.index') }}" class="btn-hero-primary">
            <x-sa.icon name="user-plus" :size="15" />
            <span>+ Tambah staf</span>
            <kbd>⌘N</kbd>
        </a>
    </div>
</div>
