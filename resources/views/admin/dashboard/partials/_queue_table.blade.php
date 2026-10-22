{{-- ==========================================================================
   Antrean Verifikasi Berkas Pendaftaran (Tabel Kerja Utama) - Admin
   ========================================================================== --}}
<x-gentelella.x-panel 
    title="Antrean Verifikasi Berkas Pendaftaran" 
    subtitle="Tinjau dan proses berkas pendaftaran calon peserta magang yang membutuhkan verifikasi segera."
    badge="{{ $pendaftaranTerbaru->count() }} Berkas"
    badgeType="warning"
>
    <x-slot:actions>
        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            <div class="adm-filter-pills">
                <button type="button" class="adm-pill-btn active" data-filter-target="#queueTable" data-filter-value="all">
                    Semua ({{ $pendaftaranTerbaru->count() }})
                </button>
                <button type="button" class="adm-pill-btn" data-filter-target="#queueTable" data-filter-value="diajukan">
                    Diajukan
                </button>
                <button type="button" class="adm-pill-btn" data-filter-target="#queueTable" data-filter-value="perlu_perbaikan">
                    Perlu Perbaikan
                </button>
            </div>
            <input type="text" data-table-search="#queueTable" placeholder="Cari nama, institusi..." style="padding: 0.35rem 0.75rem; border: 1px solid var(--cv-line, #CBD5E1); border-radius: 6px; font-size: 0.8rem; outline: none; min-width: 170px;">
        </div>
    </x-slot:actions>

    @if($pendaftaranTerbaru->isEmpty())
        <div style="padding: 3rem 1.5rem; text-align: center; color: var(--neutral-500);">
            <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(16, 185, 129, 0.12); color: var(--green-600); display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem auto;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
            </div>
            <h4 style="font-size: 1rem; font-weight: 700; color: var(--neutral-800); margin: 0 0 0.25rem 0;">Semua Berkas Sudah Selesai Diverifikasi</h4>
            <p style="font-size: 0.85rem; margin: 0;">Tidak ada berkas yang menunggu tindak lanjut saat ini.</p>
        </div>
    @else
        <div style="overflow-x: auto; width: 100%;">
            <table class="adm-table-spacious" id="queueTable">
                <thead>
                    <tr>
                        <th>Calon Peserta</th>
                        <th>Institusi & Jurusan</th>
                        <th>Bulan Magang</th>
                        <th>Usia Pengajuan</th>
                        <th style="text-align: center;">Status Berkas</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendaftaranTerbaru as $item)
                        @php
                            $tgl = $item->diajukan_at ?? $item->created_at;
                            $umur = (int) $tgl->diffInDays(now());
                            $isCritical = $umur >= 3;
                            $initial = strtoupper(substr($item->user?->nama ?? 'P', 0, 1));
                            $statusClass = $item->status->value === 'diajukan' ? 'status-diajukan' : 'status-perlu-perbaikan';
                        @endphp
                        <tr class="queue-row {{ $statusClass }}" data-status="{{ $item->status->value }}">
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div class="adm-avatar-circle">{{ $initial }}</div>
                                    <div>
                                        <div style="font-weight: 700; color: var(--neutral-800); font-size: 0.9rem;">{{ $item->user?->nama }}</div>
                                        <small style="color: var(--neutral-500); font-family: monospace;">{{ $item->nomor_pendaftaran }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: var(--neutral-800);">{{ $item->user?->profil?->institusi ?? '-' }}</div>
                                <small style="color: var(--neutral-500);">{{ $item->kuota?->jurusan?->nama_jurusan }}</small>
                            </td>
                            <td>
                                <span style="font-weight: 600; color: var(--neutral-700);">
                                    {{ $item->bulanMagang?->bulan_mulai?->translatedFormat('F Y') }}
                                </span>
                            </td>
                            <td>
                                @if($isCritical)
                                    <span class="adm-sla-pill-alert" title="Mendekati Batas SLA">⚠️ Mendekati Batas SLA ({{ $umur }} hari)</span>
                                @else
                                    <span class="adm-sla-pill-norm">{{ $umur === 0 ? 'Hari ini' : $umur . ' hari lalu' }}</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @if($item->status->value === 'diajukan')
                                    <span class="x_badge x_badge_warning">Diajukan</span>
                                @else
                                    <span class="x_badge x_badge_primary">Perlu Perbaikan</span>
                                @endif
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <button type="button" class="btn btn-default btn-xs" style="margin-right: 4px; padding: 0.35rem 0.55rem; border-radius: 6px; border: 1px solid var(--cv-line, #E2E8F0); background: #ffffff;" data-action="open-drawer" data-drawer="#drawerVerifikasi" data-nama="{{ $item->user?->nama }}" data-nomor="{{ $item->nomor_pendaftaran }}" data-institusi="{{ $item->user?->profil?->institusi ?? '-' }}" data-jurusan="{{ $item->kuota?->jurusan?->nama_jurusan ?? '-' }}" data-status="{{ $item->status->value === 'diajukan' ? 'Diajukan' : 'Perlu Perbaikan' }}" data-url="{{ route('admin.pendaftaran.show', $item) }}" title="Pratinjau Cepat">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                                <a href="{{ route('admin.pendaftaran.show', $item) }}" class="adm-btn-action-primary">
                                    <span>Tinjau Berkas</span>
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    <tr id="queueFilterEmptyRow" style="display: none;">
                        <td colspan="6" style="padding: 2.5rem 1.5rem; text-align: center; color: var(--neutral-500);">
                            <p style="margin: 0; font-size: 0.875rem; font-weight: 600;">Tidak ada berkas pendaftaran dengan filter status ini.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endif

    <div style="padding-top: 1rem; border-top: 1px solid var(--neutral-200); margin-top: 1rem; display: flex; justify-content: flex-end;">
        <a href="{{ route('admin.pendaftaran.index') }}" style="font-size: 0.8125rem; font-weight: 600; color: var(--blue-600); text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;">
            <span>Lihat Seluruh Arsip Pendaftaran</span>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </a>
    </div>
</x-gentelella.x-panel>
