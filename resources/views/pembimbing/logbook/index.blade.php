@extends('layouts.dashboard')

@section('title', 'Reviu Logbook - Pembimbing MagKom')
@section('page_title', 'Reviu Logbook Harian Peserta')
@section('page_description', 'Tinjau catatan aktivitas harian, periksa lampiran hasil kerja, dan berikan evaluasi bimbingan.')

@section('breadcrumbs')
    <span class="breadcrumb-current">Reviu Logbook</span>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">

    <!-- Filter Bar Card -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle);">
        <form method="GET" action="{{ route('pembimbing.logbook.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="display: flex; flex-direction: column; gap: 0.375rem; flex: 1; min-width: 220px;">
                <label style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">Filter Peserta Bimbingan</label>
                <select name="penempatan_id" style="height: 42px; border: 1px solid var(--neutral-500); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: var(--neutral-0);">
                    <option value="">Semua Peserta Bimbingan</option>
                    @foreach($penempatanList as $pen)
                        <option value="{{ $pen->id }}" {{ request('penempatan_id') == $pen->id ? 'selected' : '' }}>
                            {{ $pen->peserta?->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.375rem; width: 180px;">
                <label style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">Filter Tanggal</label>
                <input type="date" name="tanggal" value="{{ request('tanggal') }}" style="height: 42px; border: 1px solid var(--neutral-500); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" style="height: 42px; padding: 0 1.25rem; background: var(--blue-600); color: #FFFFFF; font-weight: 600; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer;">
                    Terapkan
                </button>
                <a href="{{ route('pembimbing.logbook.index') }}" style="height: 42px; padding: 0 1rem; background: var(--neutral-200); color: var(--neutral-700); font-weight: 600; font-size: 0.875rem; text-decoration: none; border-radius: 8px; display: inline-flex; align-items: center;">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Data Table Card -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-subtle);">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-400); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-800); margin: 0;">Catatan Kegiatan Peserta</h3>
            <span style="font-size: 0.8125rem; color: var(--neutral-600);">Total: {{ $logbooks->total() }} Logbook</span>
        </div>

        @if($logbooks->isEmpty())
            <div style="padding: 3.5rem 1.5rem; text-align: center; color: var(--neutral-600);">
                <div style="width: 56px; height: 56px; background: var(--neutral-200); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto; color: var(--neutral-500);">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
                <h4 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">Belum Ada Catatan Logbook</h4>
                <p style="font-size: 0.875rem; max-width: 460px; margin: 0 auto;">Belum ada laporan kegiatan harian yang masuk dari peserta bimbingan Anda.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                    <thead>
                        <tr style="background: var(--neutral-200); border-bottom: 1px solid var(--neutral-400); height: 52px;">
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800); width: 140px;">Tanggal</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Peserta Magang</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Aktivitas Kegiatan</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Lampiran</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Status</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800); text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logbooks as $log)
                            @php
                                $statusVal = is_object($log->status) ? $log->status->value : $log->status;
                            @endphp
                            <tr style="border-bottom: 1px solid var(--neutral-400); height: 60px;">
                                <td style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">
                                    {{ \Carbon\Carbon::parse($log->tanggal)->translatedFormat('d M Y') }}
                                </td>
                                <td style="padding: 0.75rem 1rem; font-weight: 600; color: var(--neutral-800);">
                                    {{ $log->penempatan?->peserta?->nama }}
                                    <div style="font-size: 0.75rem; color: var(--neutral-600); font-weight: 400;">
                                        {{ $log->penempatan?->bidang?->nama_bidang ?? 'Dinas Kominfo' }}
                                    </div>
                                </td>
                                <td style="padding: 0.75rem 1rem; color: var(--neutral-800); max-width: 340px;">
                                    <div style="line-height: 1.45;">{{ $log->kegiatan }}</div>
                                    @if($log->catatan_pembimbing)
                                        <div style="margin-top: 0.25rem; font-size: 0.75rem; color: var(--amber-800); background: var(--amber-100); padding: 0.25rem 0.5rem; border-radius: 4px;">
                                            Catatan Anda: {{ $log->catatan_pembimbing }}
                                        </div>
                                    @endif
                                </td>
                                <td style="padding: 0.75rem 1rem;">
                                    <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                                        @if($log->file_laporan)
                                            <a href="{{ route('peserta.logbook.lampiran', $log) }}" target="_blank" style="color: var(--blue-700); font-size: 0.75rem; font-weight: 600; text-decoration: underline;">
                                                Unduh PDF ↗
                                            </a>
                                        @endif
                                        @if($log->link_laporan)
                                            <a href="{{ $log->link_laporan }}" target="_blank" rel="noopener noreferrer" style="color: var(--blue-700); font-size: 0.75rem; font-weight: 600; text-decoration: underline;">
                                                Tautan ↗
                                            </a>
                                        @endif
                                        @if(!$log->file_laporan && !$log->link_laporan)
                                            <span style="color: var(--neutral-500); font-size: 0.75rem;">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td style="padding: 0.75rem 1rem;">
                                    <span class="role-chip-badge" style="background: {{ $statusVal === 'perlu_revisi' ? 'var(--amber-200)' : 'var(--green-100)' }}; color: {{ $statusVal === 'perlu_revisi' ? 'var(--amber-800)' : 'var(--green-700)' }}; font-weight: 700;">
                                        {{ $statusVal === 'perlu_revisi' ? 'Perlu Revisi' : 'Terkirim' }}
                                    </span>
                                </td>
                                <td style="padding: 0.75rem 1rem; text-align: right;">
                                    <button type="button" onclick="openReviewModal({{ $log->id }}, '{{ addslashes($log->penempatan?->peserta?->nama) }}', '{{ $log->tanggal->format('d/m/Y') }}', '{{ $statusVal }}', '{{ addslashes($log->catatan_pembimbing ?? '') }}')" style="padding: 0.375rem 0.75rem; background: var(--blue-600); color: #FFFFFF; border: none; border-radius: 6px; font-weight: 600; font-size: 0.8125rem; cursor: pointer;">
                                        Reviu
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($logbooks->hasPages())
                <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--neutral-400);">
                    {{ $logbooks->links() }}
                </div>
            @endif
        @endif
    </div>

</div>

<!-- MODAL REVIU LOGBOOK -->
<div id="modal-review-logbook" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; align-items: center; justify-content: center;">
    <div style="background: var(--neutral-0); border-radius: 12px; max-width: 480px; width: 90%; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
        <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-800); margin: 0 0 0.25rem 0;">Reviu Catatan Logbook</h3>
        <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0 0 1.25rem 0;" id="review-modal-subtitle"></p>

        <form method="POST" id="form-review-logbook" action="">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 0.375rem; margin-bottom: 1rem;">
                <label style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">Status Evaluasi</label>
                <select name="status" id="review-status-select" required style="height: 42px; border: 1px solid var(--neutral-500); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                    <option value="terkirim">Diterima / Sesuai (Terkirim)</option>
                    <option value="perlu_revisi">Perlu Revisi / Perbaikan</option>
                </select>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.375rem; margin-bottom: 1.25rem;">
                <label style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">Catatan Umpan Balik / Arahan</label>
                <textarea name="catatan_pembimbing" id="review-catatan-textarea" rows="3" placeholder="Tuliskan arahan, komentar, atau koreksi untuk peserta..." style="border: 1px solid var(--neutral-500); border-radius: 8px; padding: 0.75rem; font-size: 0.875rem; font-family: inherit;"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                <button type="button" onclick="closeReviewModal()" style="padding: 0.5rem 1rem; background: var(--neutral-200); border: none; border-radius: 6px; font-weight: 600; font-size: 0.8125rem; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="padding: 0.5rem 1.25rem; background: var(--blue-600); color: #FFFFFF; border: none; border-radius: 6px; font-weight: 600; font-size: 0.8125rem; cursor: pointer;">
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
        document.getElementById('modal-review-logbook').style.display = 'flex';
    }

    function closeReviewModal() {
        document.getElementById('modal-review-logbook').style.display = 'none';
    }
</script>
@endsection
