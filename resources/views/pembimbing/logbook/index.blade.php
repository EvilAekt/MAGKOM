@extends('layouts.dashboard')

@section('title', 'Review Logbook Harian - Portal MagKom')
@section('topbar_title', 'Review Logbook Harian Peserta')
@section('content_area_class', 'gentelella-fluid-canvas')

@push('styles')
    @vite(['resources/css/pages/pembimbing/logbook-index.css'])
@endpush

@section('content')
<div class="classroom-container">

    {{-- 1. Classroom Hero Banner Khas Sleman MagKom --}}
    @php
        $authUser = auth()->user();
        $bidangNama = $authUser->bidang?->nama_bidang ?? 'Dinas Komunikasi dan Informatika Sleman';
    @endphp

    <div class="classroom-banner">
        <div class="banner-top-row">
            <div>
                <h1 class="banner-title">Review Logbook Harian Peserta</h1>
                <div class="banner-subtitle">
                    <span>Bidang: <strong>{{ $bidangNama }}</strong></span>
                    <span>&bull;</span>
                    <span>Pembimbing: <strong>{{ $authUser->nama }}</strong></span>
                    <span>&bull;</span>
                    <span><strong>{{ $countMenunggu }} Logbook Menunggu Review</strong></span>
                </div>
            </div>
            <div>
                <a href="{{ route('pembimbing.logbook.index', ['status' => 'terkirim']) }}" class="btn-create-task">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>
                    Review Menunggu ({{ $countMenunggu }})
                </a>
            </div>
        </div>
    </div>

    {{-- 2. Tile Stats MagKom Gentelella (4 Metrik Logbook) --}}
    <div class="tiles-grid">
        <x-gentelella.tile-stat
            title="Total Logbook"
            value="{{ $totalLogbook }}"
            color="teal"
            trendDirection="up"
            trendText="{{ $totalLogbook }} Entri"
            subText="Akumulasi aktivitas harian"
            :sparkBars="[30, 45, 55, 60, 70, 78, 85, 92, 100]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <x-gentelella.tile-stat
            title="Perlu Review"
            value="{{ $countMenunggu }}"
            color="yellow"
            trendType="{{ $countMenunggu > 0 ? 'warning' : 'success' }}"
            trendDirection="{{ $countMenunggu > 0 ? 'down' : 'up' }}"
            trendText="{{ $countMenunggu > 0 ? $countMenunggu . ' Menunggu' : 'Tuntas' }}"
            subText="Menunggu verifikasi pembimbing"
            :sparkBars="[60, 75, 85, 70, 90, 80, 65, 50, 40]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <x-gentelella.tile-stat
            title="Disetujui"
            value="{{ $countDisetujui }}"
            color="teal"
            trendType="success"
            trendDirection="up"
            trendText="{{ $persenDisetujui }}%"
            subText="Aktivitas valid & diterima"
            :sparkBars="[25, 40, 50, 60, 70, 80, 85, 92, 98]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <x-gentelella.tile-stat
            title="Perlu Revisi"
            value="{{ $countRevisi }}"
            color="red"
            trendType="danger"
            trendDirection="down"
            trendText="{{ $countRevisi > 0 ? $countRevisi . ' Revisi' : 'Nihil' }}"
            subText="Catatan perbaikan dikirim"
            :sparkBars="[40, 30, 45, 20, 35, 25, 15, 10, 5]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>
    </div>

    {{-- 3. Filter Bar Card Modern --}}
    <div class="logbook-filter-card">
        <form method="GET" action="{{ route('pembimbing.logbook.index') }}" class="logbook-filter-form">
            <div class="filter-group">
                <label>Peserta Magang</label>
                <select name="penempatan_id" class="filter-control">
                    <option value="">Semua Peserta Bimbingan</option>
                    @foreach($penempatanList as $pen)
                        <option value="{{ $pen->id }}" {{ request('penempatan_id') == $pen->id ? 'selected' : '' }}>
                            {{ $pen->peserta?->nama }} ({{ $pen->peserta?->profil?->institusi ?? 'Institusi' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label>Status Review</label>
                <select name="status" class="filter-control">
                    <option value="">Semua Status</option>
                    <option value="terkirim" {{ request('status') === 'terkirim' ? 'selected' : '' }}>Terkirim (Menunggu)</option>
                    <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="perlu_revisi" {{ request('status') === 'perlu_revisi' ? 'selected' : '' }}>Perlu Revisi</option>
                </select>
            </div>

            <div class="filter-group filter-group-date">
                <label>Tanggal Logbook</label>
                <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="filter-control">
            </div>

            <div class="filter-group">
                <label>Cari Aktivitas</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari uraian kegiatan..." class="filter-control">
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-filter-apply">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                    <span>Terapkan</span>
                </button>
                @if(request()->hasAny(['penempatan_id', 'status', 'tanggal', 'q']))
                    <a href="{{ route('pembimbing.logbook.index') }}" class="btn-filter-reset">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- 4. Tabel Logbook Khas Classroom --}}
    <x-gentelella.x-panel title="Catatan Kegiatan Harian Peserta" subtitle="Menampilkan riwayat pelaporan logbook dinas">
        <div class="table-logbook-wrapper">
            <table class="table-logbook">
                <thead>
                    <tr>
                        <th class="checkbox-col">
                            <input type="checkbox" id="selectAllCheckbox" class="logbook-checkbox" title="Pilih Semua">
                        </th>
                        <th style="width: 120px;">Tanggal</th>
                        <th>Peserta Magang</th>
                        <th>Aktivitas Kegiatan</th>
                        <th>Lampiran</th>
                        <th>Status Review</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logbooks as $log)
                        @php
                            $statusVal = is_object($log->status) ? $log->status->value : $log->status;
                            $badgeVariant = match($statusVal) {
                                'disetujui' => 'success',
                                'perlu_revisi' => 'danger',
                                default => 'warning'
                            };
                            $initials = collect(explode(' ', $log->penempatan?->peserta?->nama ?? 'P'))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
                        @endphp
                        <tr>
                            <td class="checkbox-col">
                                <input type="checkbox" class="logbook-checkbox" value="{{ $log->id }}" {{ $statusVal === 'disetujui' ? 'disabled' : '' }}>
                            </td>
                            <td>
                                <span class="date-badge">
                                    {{ \Carbon\Carbon::parse($log->tanggal)->format('d M Y') }}
                                </span>
                            </td>
                            <td>
                                <div class="mentee-identity-flex">
                                    <div class="avatar-initials-circle">
                                        {{ strtoupper($initials) }}
                                    </div>
                                    <div>
                                        <div style="font-weight: 700; color: #0F172A;">
                                            {{ $log->penempatan?->peserta?->nama }}
                                        </div>
                                        <div style="font-size: 0.75rem; color: #64748B;">
                                            {{ $log->penempatan?->bidang?->nama_bidang ?? 'Dinas Kominfo' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="activity-desc-box">
                                    {{ $log->kegiatan }}
                                </div>
                                @if($log->catatan_pembimbing)
                                    <div class="supervisor-notes-box">
                                        <strong>Catatan Pembimbing:</strong> {{ $log->catatan_pembimbing }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                    @if($log->file_laporan)
                                        <a href="{{ route('peserta.logbook.lampiran', $log) }}" target="_blank" rel="noopener noreferrer" style="color: #059669; font-size: 0.8125rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                            <span>Berkas PDF</span>
                                        </a>
                                    @endif
                                    @if($log->link_laporan)
                                        <a href="{{ $log->link_laporan }}" target="_blank" rel="noopener noreferrer" style="color: #01347C; font-size: 0.8125rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                            <span>Tautan Kerja ↗</span>
                                        </a>
                                    @endif
                                    @if(!$log->file_laporan && !$log->link_laporan)
                                        <span style="color: #94A3B8; font-size: 0.75rem;">Tanpa Lampiran</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <x-gentelella.badge :variant="$badgeVariant">
                                    {{ ucfirst(str_replace('_', ' ', $statusVal)) }}
                                </x-gentelella.badge>
                            </td>
                            <td style="text-align: right;">
                                <button type="button" 
                                    class="btn-logbook-action btn-open-review-modal"
                                    data-log-id="{{ $log->id }}"
                                    data-peserta-nama="{{ $log->penempatan?->peserta?->nama ?? 'Peserta' }}"
                                    data-tanggal="{{ \Carbon\Carbon::parse($log->tanggal)->format('d M Y') }}"
                                    data-status="{{ $statusVal }}"
                                    data-catatan="{{ $log->catatan_pembimbing ?? '' }}">
                                    <span>Review</span>
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="table-empty-td">
                                Belum ada entri logbook harian yang sesuai dengan filter saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logbooks->hasPages())
            <div style="margin-top: 24px;">
                {{ $logbooks->links() }}
            </div>
        @endif
    </x-gentelella.x-panel>

</div>

{{-- 5. MODAL REVIEW LOGBOOK (AC-09-3, AC-09-4) --}}
<div id="modal-review-logbook" class="modal-overlay">
    <div class="modal-dialog-box">
        <div class="modal-header-bar">
            <div>
                <h3 class="modal-title">Review Catatan Logbook</h3>
                <p class="modal-subtitle" id="review-modal-subtitle"></p>
            </div>
            <button type="button" class="modal-close-btn" data-modal-close="#modal-review-logbook">&times;</button>
        </div>

        <form method="POST" id="form-review-logbook" action="" class="modal-form">
            @csrf

            <div>
                <label style="font-size: 0.8125rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem; display: block;">
                    Status Evaluasi <span style="color: #ef4444;">*</span>
                </label>
                <select name="status" id="review-status-select" required class="filter-control">
                    <option value="terkirim">Terkirim / Sesuai Standar</option>
                    <option value="disetujui">Disetujui</option>
                    <option value="perlu_revisi">Perlu Revisi / Perbaikan (AC-09-4)</option>
                </select>
            </div>

            <div>
                <label style="font-size: 0.8125rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem; display: block;">
                    Template Umpan Balik Cepat
                </label>
                <select id="quick-canned-select" class="filter-control">
                    <option value="">-- Pilih Template Komentar --</option>
                    <option value="Laporan kegiatan terverifikasi lengkap dan sesuai arahan penugasan dinas.">Laporan Lengkap &amp; Sesuai Arahan</option>
                    <option value="Mohon lengkapi dokumentasi output pekerjaan atau lampirkan tautan bukti hasil kerja.">Minta Lampiran / Bukti Kerja</option>
                    <option value="Uraian aktivitas terlalu singkat, mohon dirinci poin pekerjaan dan kendala yang dihadapi.">Uraian Terlalu Singkat</option>
                    <option value="Kerja bagus! Konsistensi dan kedisiplinan pelaporan sudah sangat memuaskan.">Apresiasi Kedisiplinan</option>
                </select>
            </div>

            <div>
                <label style="font-size: 0.8125rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem; display: block;">
                    Catatan Pembimbing <span id="catatan-required-star" style="color: #ef4444; display: none;">* (Wajib diisi jika perlu revisi)</span>
                </label>
                <textarea name="catatan_pembimbing" id="review-catatan-textarea" rows="3" placeholder="Tuliskan catatan apresiasi, koreksi, atau instruksi perbaikan..." class="filter-control" style="height: auto; padding: 10px;"></textarea>
            </div>

            <div class="modal-footer-actions">
                <button type="button" class="btn-modal-cancel" data-modal-close="#modal-review-logbook">Batal</button>
                <button type="submit" class="btn-modal-submit">Simpan Evaluasi</button>
            </div>
        </form>
    </div>
</div>

{{-- 6. FLOATING BULK APPROVAL ACTION BAR --}}
<div id="bulk-action-bar" class="bulk-floating-bar">
    <div style="display: flex; align-items: center; gap: 8px;">
        <span id="bulk-selected-count" class="bulk-count-badge">0</span>
        <span style="font-size: 0.875rem; font-weight: 600;">Logbook Terpilih</span>
    </div>
    <form id="form-bulk-review" method="POST" action="{{ route('pembimbing.logbook.bulk-review') }}" style="display: inline-flex; align-items: center; gap: 10px; margin: 0;">
        @csrf
        <div id="bulk-hidden-inputs"></div>
        <button type="submit" class="btn-bulk-approve">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
            <span>Setujui Terpilih (1-Klik)</span>
        </button>
    </form>
    <button type="button" id="btnCancelBulk" class="btn-bulk-cancel">Batal</button>
</div>

@push('scripts')
    @vite(['resources/js/pages/pembimbing/logbook-index.js'])
@endpush
@endsection
