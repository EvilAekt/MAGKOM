@extends('layouts.dashboard')

@section('title', 'Pusat Cadangan Basis Data - SuperAdmin MagKom')
@section('topbar_title', 'Pusat Cadangan & Pemulihan Basis Data')

@section('breadcrumbs')
    <a href="{{ route('superadmin.dashboard') }}">SuperAdmin</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Cadangan Basis Data</span>
@endsection

@push('styles')
    @vite(['resources/css/pages/superadmin/superadmin-common.css'])
@endpush

@section('content')
<div class="sa-fluid-container" style="display: flex; flex-direction: column; gap: 1.5rem;">

    {{-- 1. Hero Banner Khas Sleman MagKom (Identik dengan Master Bidang) --}}
    <div class="sa-hero-banner sc-reveal-hero" data-scroll-reveal>
        <div class="banner-top-row">
            <div>
                <h1 class="banner-title">Pusat Cadangan Basis Data (Disaster Recovery)</h1>
                <div class="banner-subtitle">
                    <span>Cakupan: <strong>Enkapsulasi & Pemulihan Sistem</strong></span>
                    <span class="bullet-separator">&bull;</span>
                    <span>Total Snapshot: <strong>{{ $totalBackups }} Arsip Tersedia</strong></span>
                    <span class="bullet-separator">&bull;</span>
                    <span>Status: <strong>Akses Khusus SuperAdmin</strong><span class="sr-only">SEC-03 SuperAdmin Only</span></span>
                </div>
            </div>
            <div class="banner-actions-group">
                <form method="POST" action="{{ route('superadmin.backup.snapshot') }}" style="display: inline-block;" onsubmit="return confirm('Picu pembuatan snapshot basis data baru sekarang? Proses akan berjalan dengan kompresi Gzip.')">
                    @csrf
                    <button type="submit" class="btn-banner-action">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        <span>Buat Snapshot Baru</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ALERT NOTIFIKASI STATUS OPERASIONAL --}}
    @if(session('success'))
        <div style="padding: 1rem 1.25rem; border-radius: 10px; background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0; font-size: 0.875rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div style="padding: 1rem 1.25rem; border-radius: 10px; background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; font-size: 0.875rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- 2. Grid 4 Kartu Statistik Mengadopsi Gentelella Tile-Stat (Identik dengan Master Bidang) --}}
    <div class="tiles-grid sc-reveal-up sc-delay-1" data-scroll-reveal>
        <!-- 1. Total Snapshot -->
        <x-gentelella.tile-stat
            title="TOTAL SNAPSHOT"
            value="{{ $totalBackups }}"
            color="blue"
            trendType="info"
            trendDirection="up"
            trendText="Arsip"
            subText="Maksimal retensi 10 arsip cadangan"
            :sparkBars="[30, 45, 60, 75, 85, 95]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                    <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                    <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <!-- 2. Ukuran Arsip Terkompresi -->
        <x-gentelella.tile-stat
            title="UKURAN ARSIP"
            value="{{ $totalSizeMb }} MB"
            color="teal"
            trendType="success"
            trendDirection="up"
            trendText="Gzip"
            subText="Tersimpan di storage lokal privat"
            :sparkBars="[25, 40, 55, 70, 80, 90]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <!-- 3. Ruang Bebas Partisi Disk -->
        <x-gentelella.tile-stat
            title="RUANG BEBAS DISK"
            value="{{ number_format($diskTelemetry['free_disk_mb']) }} MB"
            color="{{ $diskTelemetry['is_disk_low'] ? 'red' : 'amber' }}"
            trendType="{{ $diskTelemetry['is_disk_low'] ? 'danger' : 'warning' }}"
            trendDirection="up"
            trendText="{{ $diskTelemetry['is_disk_low'] ? 'Kritis' : 'Aman' }}"
            subText="{{ $diskTelemetry['is_disk_low'] ? 'Kapasitas sisa < 500 MB' : 'Kapasitas aman pencadangan' }}"
            :sparkBars="[20, 35, 50, 65, 80, 85]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect>
                    <rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect>
                    <line x1="6" y1="6" x2="6.01" y2="6"></line>
                    <line x1="6" y1="18" x2="6.01" y2="18"></line>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <!-- 4. Format Enkapsulasi -->
        <x-gentelella.tile-stat
            title="FORMAT ENKAPSULASI"
            value="Gzip .gz"
            color="purple"
            trendType="info"
            trendDirection="up"
            trendText="85% Hemat"
            subText="Kompresi stream level 9 on-the-fly"
            :sparkBars="[15, 25, 40, 55, 70, 85]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>
    </div>

    {{-- 3. Data Table Panel Mengadopsi Gentelella X-Panel (Identik dengan Master Bidang) --}}
    <x-gentelella.x-panel class="has-table sc-reveal-up sc-delay-2" data-scroll-reveal title="Daftar Snapshot Cadangan Basis Data">
        @if($backupFiles->isEmpty())
            <div style="padding: 4rem 1.5rem; text-align: center; color: var(--neutral-600);">
                <div style="width: 48px; height: 48px; margin: 0 auto 1rem; border-radius: 50%; background: var(--neutral-100); display: flex; align-items: center; justify-content: center; color: var(--neutral-500);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>
                </div>
                <p style="font-size: 1rem; font-weight: 600; color: var(--neutral-800); margin: 0 0 0.25rem 0;">Belum ada arsip cadangan basis data.</p>
                <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0;">Klik tombol <strong>Buat Snapshot Baru</strong> untuk mencadangkan database sekarang.</p>
            </div>
        @else
            <div class="table-responsive-gentelella" style="overflow-x: auto;">
                <table class="table jambo_table bulk_action" style="width: 100%; border-collapse: collapse; font-size: 0.875rem; text-align: left;">
                    <thead>
                        <tr class="headings">
                            <th>Nama Berkas Snapshot</th>
                            <th style="width: 130px; text-align: center;">Format</th>
                            <th>Waktu Pembuatan</th>
                            <th>Ukuran Berkas</th>
                            <th>Integritas Checksum (SHA-256)</th>
                            <th style="text-align: right; width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($backupFiles as $item)
                            <tr class="sa-table-row" style="border-bottom: 1px solid var(--neutral-200);">
                                <td style="padding: 1rem 1.25rem; font-weight: 700; color: var(--neutral-900); white-space: nowrap;">
                                    <div style="font-size: 0.875rem; font-family: monospace; color: #01347C;">
                                        {{ $item['filename'] }}
                                    </div>
                                    <div style="font-size: 0.72rem; color: var(--neutral-500); font-weight: 500; margin-top: 0.2rem;">
                                        Lokasi: storage/app/backups
                                    </div>
                                </td>
                                <td style="padding: 1rem 1rem; text-align: center; white-space: nowrap;">
                                    <span class="badge-status-blue">
                                        {{ $item['format'] }}
                                    </span>
                                </td>
                                <td style="padding: 1rem 1rem; color: var(--neutral-700); font-size: 0.8125rem; white-space: nowrap;">
                                    {{ $item['created_at'] }}
                                </td>
                                <td style="padding: 1rem 1rem; font-weight: 700; color: var(--neutral-900); white-space: nowrap;">
                                    {{ $item['size_mb'] }} MB
                                </td>
                                <td style="padding: 1rem 1rem; white-space: nowrap;">
                                    <span class="backup-checksum-badge" title="{{ $item['checksum'] }}">
                                        {{ substr($item['checksum'], 0, 16) }}...{{ substr($item['checksum'], -8) }}
                                    </span>
                                </td>
                                <td style="padding: 1rem 1.25rem; text-align: right; white-space: nowrap;">
                                    <div style="display: inline-flex; gap: 0.5rem; align-items: center; justify-content: flex-end;">
                                        <a href="{{ route('superadmin.backup.download', $item['filename']) }}"
                                           class="sa-btn-action-outline"
                                           onclick="return confirm('Peringatan Privasi (UU PDP): Berkas cadangan basis data ini memuat data pribadi peserta dan seluruh informasi internal. Pastikan Anda menyimpannya di lokasi aman. Lanjutkan unduh?')">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                            <span>Unduh</span>
                                        </a>

                                        <button type="button" 
                                                class="sa-btn-action-danger btn-trigger-delete-backup" 
                                                data-filename="{{ $item['filename'] }}"
                                                data-size="{{ $item['size_mb'] }}"
                                                data-created="{{ $item['created_at'] }}"
                                                data-action-url="{{ route('superadmin.backup.destroy', $item['filename']) }}">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                            <span>Hapus</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- BILAH PAGINASI SESUAI STANDAR DESAIN (Menampilkan 1 sampai 10 dari X entri) --}}
            @if($backupFiles->total() > 0)
                <div style="padding: 1.25rem 1.5rem; border-top: 1px solid var(--neutral-200); background: var(--neutral-100);">
                    {{ $backupFiles->links() }}
                </div>
            @endif
        @endif
    </x-gentelella.x-panel>

</div>

<!-- =====================================================================
     MODAL KONFIRMASI HAPUS SNAPSHOT CADANGAN BASIS DATA (HIGH DANGER)
     Sesuai MagKom Design System v1.0 & Bebas Native Alert Pop-up
     ===================================================================== -->
<div id="modalKonfirmasiHapusBackup" 
     style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;"
     role="dialog" 
     aria-modal="true" 
     aria-labelledby="modalHapusBackupTitle">
    <div style="background: var(--neutral-0, #FFFFFF); border-radius: 16px; width: 100%; max-width: 480px; padding: 2rem; box-shadow: var(--shadow-modal, 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)); position: relative; border: 1px solid #FECDD3;">
        
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.25rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 42px; height: 42px; border-radius: 10px; background: #FEE2E2; color: #DC2626; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        <line x1="10" y1="11" x2="10" y2="17"></line>
                        <line x1="14" y1="11" x2="14" y2="17"></line>
                    </svg>
                </div>
                <div>
                    <h3 id="modalHapusBackupTitle" style="font-size: 1.125rem; font-weight: 800; color: #991B1B; margin: 0; line-height: 1.3;">
                        Hapus Snapshot Basis Data?
                    </h3>
                    <span style="font-size: 0.8125rem; color: var(--neutral-500, #64748B);">
                        Pusat Cadangan & Pemulihan Sistem (Disaster Recovery)
                    </span>
                </div>
            </div>
            <button type="button" 
                    data-modal-dismiss="modalKonfirmasiHapusBackup" 
                    style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--neutral-400, #94A3B8); line-height: 1; padding: 0.25rem; border-radius: 6px;" 
                    title="Tutup dialog">
                &times;
            </button>
        </div>

        <form id="formHapusBackup" method="POST" style="display: flex; flex-direction: column; gap: 1.25rem; margin: 0;">
            @csrf
            @method('DELETE')
            <div style="background: #FFF1F2; border: 1px solid #FECDD3; border-radius: 10px; padding: 1rem 1.25rem;">
                <p style="font-size: 0.875rem; color: #9F1239; margin: 0 0 0.5rem 0; line-height: 1.5;">
                    Anda akan menghapus berkas snapshot arsip database berikut secara permanen dari server:
                </p>
                <div style="background: #FFFFFF; border-radius: 6px; padding: 0.65rem 0.85rem; border: 1px solid rgba(225, 29, 72, 0.2); font-family: monospace;">
                    <div style="font-weight: 700; color: #881337; font-size: 0.8125rem; word-break: break-all;" id="hapusBackupFilename">backup-2026.sql.gz</div>
                    <div style="font-size: 0.75rem; color: var(--neutral-600, #475569); margin-top: 0.25rem; font-family: inherit;" id="hapusBackupMeta">Ukuran: 0 MB &bull; Waktu: -</div>
                </div>
                <div style="margin-top: 0.75rem; font-size: 0.75rem; color: #BE123C; line-height: 1.4;">
                    <strong>PERINGATAN PEMULIHAN SISTEM:</strong>
                    <ul style="margin: 0.25rem 0 0 1rem; padding: 0;">
                        <li>Berkas ini tidak dapat dipulihkan kembali setelah dihapus.</li>
                        <li>Pastikan Anda telah memiliki salinan cadangan sekunder di media penyimpanan eksternal yang aman.</li>
                    </ul>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--neutral-200, #E2E8F0); padding-top: 1rem;">
                <button type="button" 
                        data-modal-dismiss="modalKonfirmasiHapusBackup" 
                        style="height: 40px; padding: 0 1.25rem; background: var(--neutral-100, #F1F5F9); border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; font-weight: 700; font-size: 0.875rem; color: var(--neutral-700, #334155); cursor: pointer;">
                    Batal
                </button>
                <button type="submit" 
                        style="height: 40px; padding: 0 1.5rem; background: #DC2626; border: none; border-radius: 8px; font-weight: 700; font-size: 0.875rem; color: #FFFFFF; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem; transition: background 0.15s ease;">
                    <span>Ya, Hapus Snapshot Permanen</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    @vite(['resources/js/pages/superadmin/backup-index.js'])
@endpush
@endsection
