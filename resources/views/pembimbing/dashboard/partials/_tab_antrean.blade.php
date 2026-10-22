{{-- ==========================================================================
   Tab 2: Pusat Antrean Reviu Cepat (Action Hub) - Dashboard Pembimbing
   ========================================================================== --}}
<!-- Antrean Seluruh Logbook Harian Terkirim -->
<x-gentelella.x-panel
    title="Seluruh Antrean Logbook Menunggu Reviu ({{ $allPendingLogbooks->count() }})"
    subtitle="Logbook harian terkirim dari mentee yang belum diberikan catatan/konfirmasi pembimbing."
>
    <x-slot:actions>
        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            <input type="text" data-table-search="#table-antrean-logbook" placeholder="Cari nama atau kegiatan..." style="padding: 0.45rem 0.85rem; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; font-size: 0.8rem; outline: none; min-width: 200px;">
            <a href="{{ route('pembimbing.logbook.index', ['status' => 'terkirim']) }}" style="font-size: 0.8rem; font-weight: 600; color: #0641CC; text-decoration: none;">
                Kelola di Modul Logbook &rarr;
            </a>
        </div>
    </x-slot:actions>

    <div style="overflow-x: auto;">
        <table id="table-antrean-logbook" style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
            <thead>
                <tr style="border-bottom: 1px solid var(--neutral-200, #E2E8F0); background: #f8fafc; color: var(--neutral-600, #475569); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                    <th style="padding: 0.875rem 1.5rem;">Mentee</th>
                    <th style="padding: 0.875rem 1rem;">Tanggal Logbook</th>
                    <th style="padding: 0.875rem 1rem;">Kegiatan / Aktivitas Kerja</th>
                    <th style="padding: 0.875rem 1rem;">Lampiran</th>
                    <th style="padding: 0.875rem 1rem;">Diajukan</th>
                    <th style="padding: 0.875rem 1.5rem; text-align: right;">Aksi Reviu</th>
                </tr>
            </thead>
            <tbody>
                @forelse($allPendingLogbooks as $logbook)
                    <tr style="border-bottom: 1px solid var(--neutral-100, #F1F5F9);">
                        <td style="padding: 1rem 1.5rem;">
                            <div style="font-weight: 600; color: var(--neutral-900, #0F172A);">
                                {{ $logbook->penempatan?->peserta?->nama }}
                            </div>
                            <div style="font-size: 0.75rem; color: var(--neutral-500, #64748B);">
                                {{ $logbook->penempatan?->peserta?->profil?->institusi ?? '-' }}
                            </div>
                        </td>
                        <td style="padding: 1rem 1rem; font-size: 0.8rem; font-weight: 600; color: #92400e; white-space: nowrap;">
                            {{ $logbook->tanggal?->format('d M Y') }}
                        </td>
                        <td style="padding: 1rem 1rem; color: var(--neutral-700, #334155); max-width: 380px;">
                            <div style="line-height: 1.45; font-size: 0.8125rem;">
                                {{ $logbook->kegiatan }}
                            </div>
                        </td>
                        <td style="padding: 1rem 1rem; white-space: nowrap;">
                            @if($logbook->link_laporan)
                                <a href="{{ $logbook->link_laporan }}" target="_blank" rel="noopener noreferrer" style="font-size: 0.75rem; color: #0641CC; text-decoration: underline; display: block;">
                                    Tautan Lampiran &nearr;
                                </a>
                            @endif
                            @if($logbook->file_laporan)
                                <span style="font-size: 0.75rem; color: #059669; font-weight: 600; display: block;">
                                    Berkas PDF Tersedia
                                </span>
                            @endif
                            @if(!$logbook->link_laporan && !$logbook->file_laporan)
                                <span style="font-size: 0.75rem; color: var(--neutral-400, #94A3B8);">-</span>
                            @endif
                        </td>
                        <td style="padding: 1rem 1rem; font-size: 0.75rem; color: var(--neutral-500, #64748B); white-space: nowrap;">
                            {{ $logbook->created_at?->diffForHumans() }}
                        </td>
                        <td style="padding: 1rem 1.5rem; text-align: right; white-space: nowrap;">
                            <button type="button" class="btn btn-default btn-xs" style="margin-right: 4px; padding: 0.35rem 0.55rem; border-radius: 6px; border: 1px solid var(--cv-line, #CBD5E1); background: #ffffff;" data-action="open-drawer" data-drawer="#drawerReviewLogbook" data-mentee="{{ $logbook->penempatan?->peserta?->nama }}" data-institusi="{{ $logbook->penempatan?->peserta?->profil?->institusi ?? '-' }}" data-tanggal="{{ $logbook->tanggal?->format('d M Y') }}" data-kegiatan="{{ $logbook->kegiatan }}" data-url="{{ route('pembimbing.logbook.index', ['penempatan_id' => $logbook->penempatan_id, 'tanggal' => $logbook->tanggal?->toDateString()]) }}" title="Pratinjau Cepat">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                            <a href="{{ route('pembimbing.logbook.index', ['penempatan_id' => $logbook->penempatan_id, 'tanggal' => $logbook->tanggal?->toDateString()]) }}" style="padding: 0.4rem 0.85rem; background: #0641CC; color: #ffffff; font-size: 0.75rem; font-weight: 600; border-radius: 6px; text-decoration: none;">
                                Tanggapi Logbook
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: 2rem; text-align: center; color: var(--neutral-500, #64748B);">
                            Tidak ada antrean logbook harian yang menunggu reviu.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-gentelella.x-panel>

<!-- Antrean Seluruh Progres Tugas Menunggu Evaluasi -->
<x-gentelella.x-panel
    title="Seluruh Antrean Progres Tugas Menunggu Evaluasi ({{ $allPendingProgresLogs->count() }})"
    subtitle="Laporan progres tugas dari mentee yang membutuhkan evaluasi dan umpan balik pembimbing."
>
    <x-slot:actions>
        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            <input type="text" data-table-search="#table-antrean-progres" placeholder="Cari mentee atau judul tugas..." style="padding: 0.45rem 0.85rem; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; font-size: 0.8rem; outline: none; min-width: 200px;">
            <a href="{{ route('pembimbing.tugas.index') }}" style="font-size: 0.8rem; font-weight: 600; color: #0641CC; text-decoration: none;">
                Kelola di Modul Tugas &rarr;
            </a>
        </div>
    </x-slot:actions>

    <div style="overflow-x: auto;">
        <table id="table-antrean-progres" style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
            <thead>
                <tr style="border-bottom: 1px solid var(--neutral-200, #E2E8F0); background: #f8fafc; color: var(--neutral-600, #475569); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                    <th style="padding: 0.875rem 1.5rem;">Mentee</th>
                    <th style="padding: 0.875rem 1rem;">Tugas & Kategori</th>
                    <th style="padding: 0.875rem 1rem;">Laporan Kemajuan</th>
                    <th style="padding: 0.875rem 1rem;">Deadline Tugas</th>
                    <th style="padding: 0.875rem 1.5rem; text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($allPendingProgresLogs as $progres)
                    <tr style="border-bottom: 1px solid var(--neutral-100, #F1F5F9);">
                        <td style="padding: 1rem 1.5rem;">
                            <div style="font-weight: 600; color: var(--neutral-900, #0F172A);">
                                {{ $progres->tugas?->penempatan?->peserta?->nama }}
                            </div>
                            <div style="font-size: 0.75rem; color: var(--neutral-500, #64748B);">
                                {{ $progres->tugas?->penempatan?->peserta?->profil?->institusi ?? '-' }}
                            </div>
                        </td>
                        <td style="padding: 1rem 1rem;">
                            <div style="font-weight: 600; color: var(--neutral-800, #1E293B);">
                                {{ $progres->tugas?->judul }}
                            </div>
                            <span style="font-size: 0.7rem; background: #e0e7ff; color: #3730a3; padding: 0.1rem 0.45rem; border-radius: 4px; font-weight: 600;">
                                {{ $progres->tugas?->kategori ?? 'Tugas' }}
                            </span>
                        </td>
                        <td style="padding: 1rem 1rem; color: var(--neutral-700, #334155); max-width: 380px;">
                            <div style="line-height: 1.45; font-size: 0.8125rem;">
                                {{ $progres->catatan ?? $progres->deskripsi ?? '-' }}
                            </div>
                            <span style="font-size: 0.7rem; color: var(--neutral-400, #94A3B8); display: block; margin-top: 0.2rem;">
                                Diajukan {{ $progres->created_at?->diffForHumans() }}
                            </span>
                        </td>
                        <td style="padding: 1rem 1rem; font-size: 0.8rem; color: var(--neutral-700, #334155); white-space: nowrap;">
                            {{ $progres->tugas?->deadline ? \Carbon\Carbon::parse($progres->tugas->deadline)->format('d M Y') : '-' }}
                        </td>
                        <td style="padding: 1rem 1.5rem; text-align: right; white-space: nowrap;">
                            <a href="{{ route('pembimbing.tugas.show', $progres->tugas_id) }}" style="padding: 0.4rem 0.85rem; background: #4f46e5; color: #ffffff; font-size: 0.75rem; font-weight: 600; border-radius: 6px; text-decoration: none;">
                                Evaluasi Tugas
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding: 2rem; text-align: center; color: var(--neutral-500, #64748B);">
                            Seluruh laporan progres tugas telah dievaluasi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-gentelella.x-panel>
