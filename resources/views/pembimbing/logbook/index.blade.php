@extends('layouts.dashboard')

@section('title', 'Reviu Logbook Harian - Portal MagKom')

@section('content')
<div class="dashboard-content" style="display: flex; flex-direction: column; gap: 1.5rem;">

    <!-- Flash Notification -->
    @if(session('success'))
        <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 12px; padding: 1rem 1.25rem; display: flex; align-items: center; justify-content: space-between; color: #065f46;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                <span style="font-size: 0.875rem; font-weight: 500;">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" style="background: transparent; border: none; cursor: pointer; color: #065f46;">&times;</button>
        </div>
    @endif

    @if($errors->any())
        <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 12px; padding: 1rem 1.25rem; color: #991b1b;">
            <div style="font-weight: 600; font-size: 0.875rem; margin-bottom: 0.25rem;">Gagal Menyimpan Evaluasi Logbook:</div>
            <ul style="margin: 0; padding-left: 1.25rem; font-size: 0.8rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Header & Breadcrumbs -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <nav style="font-size: 0.8rem; color: var(--neutral-500); margin-bottom: 0.35rem; display: flex; align-items: center; gap: 0.4rem;">
                <a href="{{ route('pembimbing.dashboard') }}" style="color: var(--neutral-600); text-decoration: none;">Dashboard</a>
                <span>/</span>
                <span style="color: var(--neutral-900); font-weight: 600;">Reviu Logbook</span>
            </nav>
            <h1 style="font-size: 1.5rem; font-weight: 700; color: var(--neutral-900); margin: 0;">
                Reviu Logbook Harian Peserta (F-09)
            </h1>
            <p style="font-size: 0.875rem; color: var(--neutral-600); margin: 0.25rem 0 0 0;">
                Tinjau catatan aktivitas harian mentee, periksa lampiran hasil kerja, dan berikan bimbingan terarah.
            </p>
        </div>
    </div>

    <!-- Filter Bar Card -->
    <div style="background: #ffffff; border-radius: 14px; border: 1px solid var(--neutral-200); padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <form method="GET" action="{{ route('pembimbing.logbook.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            
            <!-- Filter Peserta -->
            <div style="flex: 1; min-width: 200px;">
                <label style="font-size: 0.8rem; font-weight: 600; color: var(--neutral-700); display: block; margin-bottom: 0.35rem;">Peserta Magang</label>
                <select name="penempatan_id" style="width: 100%; height: 40px; border: 1px solid var(--neutral-300); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff;">
                    <option value="">Semua Peserta Bimbingan</option>
                    @foreach($penempatanList as $pen)
                        <option value="{{ $pen->id }}" {{ request('penempatan_id') == $pen->id ? 'selected' : '' }}>
                            {{ $pen->peserta?->nama }} ({{ $pen->peserta?->profil?->institusi ?? 'Institusi' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status -->
            <div style="flex: 1; min-width: 160px;">
                <label style="font-size: 0.8rem; font-weight: 600; color: var(--neutral-700); display: block; margin-bottom: 0.35rem;">Status Reviu</label>
                <select name="status" style="width: 100%; height: 40px; border: 1px solid var(--neutral-300); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff;">
                    <option value="">Semua Status</option>
                    <option value="terkirim" {{ request('status') === 'terkirim' ? 'selected' : '' }}>Terkirim (Menunggu)</option>
                    <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="perlu_revisi" {{ request('status') === 'perlu_revisi' ? 'selected' : '' }}>Perlu Revisi</option>
                </select>
            </div>

            <!-- Filter Tanggal -->
            <div style="width: 170px;">
                <label style="font-size: 0.8rem; font-weight: 600; color: var(--neutral-700); display: block; margin-bottom: 0.35rem;">Tanggal Logbook</label>
                <input type="date" name="tanggal" value="{{ request('tanggal') }}" style="width: 100%; height: 40px; border: 1px solid var(--neutral-300); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
            </div>

            <!-- Pencarian Isi Aktivitas -->
            <div style="flex: 1; min-width: 200px;">
                <label style="font-size: 0.8rem; font-weight: 600; color: var(--neutral-700); display: block; margin-bottom: 0.35rem;">Cari Aktivitas</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari uraian kegiatan..." style="width: 100%; height: 40px; border: 1px solid var(--neutral-300); border-radius: 8px; padding: 0 0.85rem; font-size: 0.875rem;">
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" style="height: 40px; padding: 0 1.25rem; background: #0641CC; color: #ffffff; font-weight: 600; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer;">
                    Terapkan
                </button>
                @if(request()->hasAny(['penempatan_id', 'status', 'tanggal', 'q']))
                    <a href="{{ route('pembimbing.logbook.index') }}" style="height: 40px; padding: 0 1rem; background: var(--neutral-200); color: var(--neutral-700); font-weight: 600; font-size: 0.875rem; text-decoration: none; border-radius: 8px; display: inline-flex; align-items: center;">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Data Table Card -->
    <div style="background: #ffffff; border-radius: 14px; border: 1px solid var(--neutral-200); box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden;">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-200); display: flex; justify-content: space-between; align-items: center; background: #fafafa;">
            <div>
                <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-900); margin: 0;">Catatan Kegiatan Harian Peserta</h3>
                <span style="font-size: 0.75rem; color: var(--neutral-500);">Menampilkan riwayat pelaporan logbook dinas</span>
            </div>
            <span style="font-size: 0.8rem; font-weight: 600; color: var(--neutral-600); background: var(--neutral-100); padding: 0.25rem 0.65rem; border-radius: 999px;">
                Total: {{ $logbooks->total() }} Logbook
            </span>
        </div>

        @if($logbooks->isEmpty())
            <div style="padding: 3.5rem 1.5rem; text-align: center; color: var(--neutral-500);">
                <div style="width: 56px; height: 56px; background: var(--neutral-100); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto; color: var(--neutral-400);">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
                <h4 style="font-size: 1.1rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.35rem;">Belum Ada Catatan Logbook</h4>
                <p style="font-size: 0.85rem; max-width: 460px; margin: 0 auto;">Belum ada entri logbook harian yang sesuai dengan kriteria filter saat ini.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--neutral-200); background: #f8fafc; color: var(--neutral-600); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <th style="padding: 1rem 1.5rem; width: 140px;">Tanggal</th>
                            <th style="padding: 1rem 1rem;">Peserta Magang</th>
                            <th style="padding: 1rem 1rem;">Aktivitas Kegiatan</th>
                            <th style="padding: 1rem 1rem;">Lampiran</th>
                            <th style="padding: 1rem 1rem;">Status Reviu</th>
                            <th style="padding: 1rem 1.5rem; text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logbooks as $log)
                            @php
                                $statusVal = is_object($log->status) ? $log->status->value : $log->status;
                                $statusBg = match($statusVal) {
                                    'disetujui' => '#d1fae5',
                                    'perlu_revisi' => '#fee2e2',
                                    default => '#fef3c7'
                                };
                                $statusColor = match($statusVal) {
                                    'disetujui' => '#065f46',
                                    'perlu_revisi' => '#991b1b',
                                    default => '#92400e'
                                };
                            @endphp
                            <tr style="border-bottom: 1px solid var(--neutral-100); transition: background 0.15s ease;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 1rem 1.5rem; font-weight: 700; color: var(--neutral-900); white-space: nowrap;">
                                    {{ \Carbon\Carbon::parse($log->tanggal)->format('d M Y') }}
                                </td>
                                <td style="padding: 1rem 1rem;">
                                    <div style="font-weight: 600; color: var(--neutral-900);">
                                        {{ $log->penempatan?->peserta?->nama }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--neutral-500);">
                                        {{ $log->penempatan?->bidang?->nama_bidang ?? 'Dinas Kominfo' }}
                                    </div>
                                </td>
                                <td style="padding: 1rem 1rem; color: var(--neutral-800); max-width: 360px;">
                                    <div style="line-height: 1.5; font-size: 0.85rem;">
                                        {{ $log->kegiatan }}
                                    </div>
                                    @if($log->catatan_pembimbing)
                                        <div style="margin-top: 0.4rem; font-size: 0.75rem; color: #92400e; background: #fef3c7; padding: 0.35rem 0.6rem; border-radius: 6px; border-left: 3px solid #d97706;">
                                            <strong>Catatan Pembimbing:</strong> {{ $log->catatan_pembimbing }}
                                        </div>
                                    @endif
                                </td>
                                <td style="padding: 1rem 1rem;">
                                    <div style="display: flex; flex-direction: column; gap: 0.35rem;">
                                        @if($log->file_laporan)
                                            <a href="{{ route('peserta.logbook.lampiran', $log) }}" target="_blank" rel="noopener noreferrer" style="color: #059669; font-size: 0.75rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem;">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                                Berkas PDF
                                            </a>
                                        @endif
                                        @if($log->link_laporan)
                                            <a href="{{ $log->link_laporan }}" target="_blank" rel="noopener noreferrer" style="color: #0641CC; font-size: 0.75rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem;">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                                                Tautan Kerja ↗
                                            </a>
                                        @endif
                                        @if(!$log->file_laporan && !$log->link_laporan)
                                            <span style="color: var(--neutral-400); font-size: 0.75rem;">Tanpa Lampiran</span>
                                        @endif
                                    </div>
                                </td>
                                <td style="padding: 1rem 1rem;">
                                    <span style="padding: 0.25rem 0.6rem; border-radius: 999px; font-size: 0.75rem; font-weight: 700; background: {{ $statusBg }}; color: {{ $statusColor }};">
                                        {{ ucfirst(str_replace('_', ' ', $statusVal)) }}
                                    </span>
                                </td>
                                <td style="padding: 1rem 1.5rem; text-align: right;">
                                    <button type="button" onclick="openReviewModal({{ $log->id }}, '{{ addslashes($log->penempatan?->peserta?->nama ?? 'Peserta') }}', '{{ $log->tanggal?->format('d M Y') }}', '{{ $statusVal }}', '{{ addslashes($log->catatan_pembimbing ?? '') }}')" style="padding: 0.4rem 0.85rem; background: #0641CC; color: #ffffff; border: none; border-radius: 6px; font-weight: 600; font-size: 0.775rem; cursor: pointer; box-shadow: 0 1px 3px rgba(6, 65, 204, 0.2);">
                                        Reviu
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($logbooks->hasPages())
                <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--neutral-200); background: #fafafa;">
                    {{ $logbooks->links() }}
                </div>
            @endif
        @endif
    </div>

</div>

<!-- MODAL REVIU LOGBOOK (AC-09-3, AC-09-4) -->
<div id="modal-review-logbook" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
    <div style="background: #ffffff; border-radius: 16px; max-width: 500px; width: 100%; padding: 1.75rem; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); border: 1px solid var(--neutral-200);">
        
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
            <div>
                <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--neutral-900); margin: 0;">Reviu Catatan Logbook</h3>
                <p style="font-size: 0.8rem; color: var(--neutral-500); margin: 0.25rem 0 0 0;" id="review-modal-subtitle"></p>
            </div>
            <button type="button" onclick="closeReviewModal()" style="background: transparent; border: none; font-size: 1.5rem; cursor: pointer; color: var(--neutral-400); line-height: 1;">&times;</button>
        </div>

        <form method="POST" id="form-review-logbook" action="" style="display: flex; flex-direction: column; gap: 1rem; margin-top: 1rem;">
            @csrf

            <!-- Status Evaluasi -->
            <div>
                <label style="font-size: 0.8rem; font-weight: 600; color: var(--neutral-700); display: block; margin-bottom: 0.35rem;">
                    Status Evaluasi <span style="color: #ef4444;">*</span>
                </label>
                <select name="status" id="review-status-select" required onchange="handleStatusChange(this.value)" style="width: 100%; height: 42px; border: 1px solid var(--neutral-300); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                    <option value="terkirim">Terkirim / Sesuai Standar</option>
                    <option value="perlu_revisi">Perlu Revisi / Perbaikan (AC-09-4)</option>
                </select>
            </div>

            <!-- Catatan Pembimbing (Wajib jika Perlu Revisi / AC-09-4) -->
            <div>
                <label style="font-size: 0.8rem; font-weight: 600; color: var(--neutral-700); display: block; margin-bottom: 0.35rem;">
                    Catatan Pembimbing <span id="catatan-required-star" style="color: #ef4444; display: none;">* (Wajib diisi jika perlu revisi)</span>
                </label>
                <textarea name="catatan_pembimbing" id="review-catatan-textarea" rows="3" placeholder="Tuliskan catatan apresiasi, koreksi, atau instruksi perbaikan untuk peserta..." style="width: 100%; border: 1px solid var(--neutral-300); border-radius: 8px; padding: 0.75rem; font-size: 0.875rem; font-family: inherit;"></textarea>
                <span id="catatan-hint" style="font-size: 0.75rem; color: var(--neutral-500); margin-top: 0.25rem; display: block;">
                    Catatan ini akan langsung terlihat pada lembar riwayat logbook mentee.
                </span>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem; border-top: 1px solid var(--neutral-200); padding-top: 1rem;">
                <button type="button" onclick="closeReviewModal()" style="height: 40px; padding: 0 1.25rem; background: var(--neutral-100); color: var(--neutral-700); font-weight: 600; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="height: 40px; padding: 0 1.5rem; background: #0641CC; color: #ffffff; font-weight: 600; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer;">
                    Simpan Evaluasi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openReviewModal(logId, namaPeserta, tanggal, status, catatan) {
        document.getElementById('review-modal-subtitle').innerText = namaPeserta + ' • Tanggal: ' + tanggal;
        document.getElementById('form-review-logbook').action = '/pembimbing/logbook/' + logId + '/review';
        document.getElementById('review-status-select').value = status;
        document.getElementById('review-catatan-textarea').value = catatan;
        handleStatusChange(status);
        document.getElementById('modal-review-logbook').style.display = 'flex';
    }

    function closeReviewModal() {
        document.getElementById('modal-review-logbook').style.display = 'none';
    }

    function handleStatusChange(status) {
        const star = document.getElementById('catatan-required-star');
        const textarea = document.getElementById('review-catatan-textarea');
        if (status === 'perlu_revisi') {
            star.style.display = 'inline';
            textarea.setAttribute('required', 'required');
        } else {
            star.style.display = 'none';
            textarea.removeAttribute('required');
        }
    }
</script>
@endsection
