@extends('layouts.dashboard')

@section('title', 'Detail Tugas: ' . $tugas->judul . ' - Portal MagKom')

@section('content')
@php
    $peserta = $tugas->penempatan->peserta;
    $isOverdue = $tugas->deadline && $tugas->deadline->isPast() && $tugas->status->value !== 'selesai';
    $daysLeft = $tugas->deadline ? \Carbon\Carbon::today()->diffInDays($tugas->deadline, false) : 99;
    $isDueSoon = !$isOverdue && $daysLeft >= 0 && $daysLeft <= 3 && $tugas->status->value !== 'selesai';
@endphp

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
            <div style="font-weight: 600; font-size: 0.875rem; margin-bottom: 0.25rem;">Gagal Menyimpan Umpan Balik:</div>
            <ul style="margin: 0; padding-left: 1.25rem; font-size: 0.8rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Top Action Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <nav style="font-size: 0.8rem; color: var(--neutral-500); margin-bottom: 0.35rem; display: flex; align-items: center; gap: 0.4rem;">
                <a href="{{ route('pembimbing.dashboard') }}" style="color: var(--neutral-600); text-decoration: none;">Dashboard</a>
                <span>/</span>
                <a href="{{ route('pembimbing.tugas.index') }}" style="color: var(--neutral-600); text-decoration: none;">Tugas Peserta</a>
                <span>/</span>
                <span style="color: var(--neutral-900); font-weight: 600;">{{ Str::limit($tugas->judul, 25) }}</span>
            </nav>
            <h1 style="font-size: 1.5rem; font-weight: 700; color: var(--neutral-900); margin: 0;">
                Detail & Reviu Penugasan
            </h1>
        </div>

        <div style="display: flex; gap: 0.75rem; align-items: center;">
            <a href="{{ route('pembimbing.tugas.index') }}" style="padding: 0.5rem 1rem; background: var(--neutral-100); color: var(--neutral-700); font-size: 0.875rem; font-weight: 600; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem;">
                &larr; Kembali
            </a>

            @if($tugas->status->value !== 'selesai')
                <form action="{{ route('pembimbing.tugas.selesai', $tugas->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menandai tugas ini sebagai Selesai?')">
                    @csrf
                    <button type="submit" style="padding: 0.5rem 1rem; background: #059669; color: #ffffff; font-size: 0.875rem; font-weight: 600; border: none; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem; box-shadow: 0 2px 4px rgba(5, 150, 105, 0.2);">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        Tandai Selesai
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- 2 Column Layout -->
    <div style="display: grid; grid-template-columns: 1fr 340px; gap: 1.5rem; align-items: start;">
        
        <!-- Left Column: Task Overview & Progress Logs -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            
            <!-- Task Info Card -->
            <div style="background: #ffffff; border: 1px solid var(--neutral-200); border-radius: 14px; padding: 1.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div style="display: flex; gap: 0.5rem; margin-bottom: 0.75rem; flex-wrap: wrap; align-items: center;">
                    <span style="padding: 0.2rem 0.6rem; background: #e0e7ff; color: #3730a3; border-radius: 6px; font-size: 0.75rem; font-weight: 700;">
                        {{ $tugas->kategori }}
                    </span>
                    <span style="padding: 0.2rem 0.6rem; background: var(--neutral-100); color: var(--neutral-700); border-radius: 6px; font-size: 0.75rem; font-weight: 700;">
                        Kompleksitas: {{ ucfirst($tugas->kompleksitas->value ?? 'Sedang') }}
                    </span>
                    @if($isOverdue)
                        <span style="padding: 0.2rem 0.6rem; background: #fee2e2; color: #b91c1c; border-radius: 6px; font-size: 0.75rem; font-weight: 700;">
                            Terlambat (Overdue)
                        </span>
                    @elseif($isDueSoon)
                        <span style="padding: 0.2rem 0.6rem; background: #fef3c7; color: #92400e; border-radius: 6px; font-size: 0.75rem; font-weight: 700;">
                            Mendekati Deadline ({{ $daysLeft }} Hari)
                        </span>
                    @endif
                </div>

                <h2 style="font-size: 1.35rem; font-weight: 800; color: var(--neutral-900); margin: 0 0 1rem 0; line-height: 1.3;">
                    {{ $tugas->judul }}
                </h2>

                <div style="color: var(--neutral-800); font-size: 0.925rem; line-height: 1.7; white-space: pre-line; background: #f8fafc; padding: 1.25rem; border-radius: 10px; border: 1px solid var(--neutral-200);">
                    {{ $tugas->deskripsi ?: 'Tidak ada deskripsi rinci yang disertakan.' }}
                </div>

                <!-- Sumber Daya & Berkas Acuan Tugas -->
                @if($tugas->file_lampiran || $tugas->link_referensi)
                    <div style="margin-top: 1.25rem; padding-top: 1.25rem; border-top: 1px solid var(--neutral-200); display: flex; flex-direction: column; gap: 0.75rem;">
                        <h4 style="font-size: 0.85rem; font-weight: 700; color: var(--neutral-700); margin: 0; text-transform: uppercase; letter-spacing: 0.025em;">
                            Materi & Sumber Daya Penugasan:
                        </h4>

                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 0.75rem;">
                            @if($tugas->link_referensi)
                                <div style="background: #f0f7ff; border: 1px solid #bfdbfe; border-radius: 10px; padding: 0.85rem 1rem; display: flex; justify-content: space-between; align-items: center; gap: 0.75rem;">
                                    <div style="display: flex; align-items: center; gap: 0.65rem; min-width: 0;">
                                        <div style="width: 34px; height: 34px; border-radius: 8px; background: #dbeafe; color: #1d4ed8; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                                        </div>
                                        <div style="min-width: 0;">
                                            <div style="font-size: 0.75rem; font-weight: 700; color: #1e40af;">Tautan Referensi Eksternal</div>
                                            <div style="font-size: 0.8125rem; color: #1d4ed8; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px;">
                                                {{ $tugas->link_referensi }}
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{ $tugas->link_referensi }}" target="_blank" rel="noopener noreferrer" style="padding: 0.35rem 0.75rem; background: #1d4ed8; color: #ffffff; font-size: 0.75rem; font-weight: 600; border-radius: 6px; text-decoration: none; white-space: nowrap; display: inline-flex; align-items: center; gap: 0.3rem;">
                                        <span>Buka</span>
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                                    </a>
                                </div>
                            @endif

                            @if($tugas->file_lampiran)
                                @php $badge = $tugas->badge_color_lampiran; @endphp
                                <div style="background: #fafafa; border: 1px solid var(--neutral-200); border-radius: 10px; padding: 0.85rem 1rem; display: flex; justify-content: space-between; align-items: center; gap: 0.75rem;">
                                    <div style="display: flex; align-items: center; gap: 0.65rem; min-width: 0;">
                                        <div style="padding: 0.25rem 0.5rem; border-radius: 6px; background: {{ $badge['bg'] }}; color: {{ $badge['color'] }}; font-weight: 800; font-size: 0.7rem; border: 1px solid {{ $badge['border'] }}; flex-shrink: 0;">
                                            {{ $badge['label'] }}
                                        </div>
                                        <div style="min-width: 0;">
                                            <div style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-900); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 220px;" title="{{ $tugas->nama_file_lampiran }}">
                                                {{ $tugas->nama_file_lampiran ?: 'Berkas Panduan' }}
                                            </div>
                                            <div style="font-size: 0.725rem; color: var(--neutral-500);">
                                                Ukuran: {{ $tugas->ukuran_format ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{ route('pembimbing.tugas.lampiran', $tugas->id) }}" style="padding: 0.35rem 0.75rem; background: #0641CC; color: #ffffff; font-size: 0.75rem; font-weight: 600; border-radius: 6px; text-decoration: none; white-space: nowrap; display: inline-flex; align-items: center; gap: 0.35rem;">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                        <span>Unduh</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Progress Logs & Review List -->
            <div style="background: #ffffff; border: 1px solid var(--neutral-200); border-radius: 14px; padding: 1.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.75rem;">
                    <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--neutral-900); margin: 0;">
                        Laporan Progres dari Peserta ({{ $tugas->progresLogs->count() }})
                    </h3>
                    <span style="font-size: 0.75rem; color: var(--neutral-500);">
                        Review setiap laporan untuk memandu kemajuan mentee
                    </span>
                </div>

                @if($tugas->progresLogs->isEmpty())
                    <div style="padding: 3rem 1.5rem; text-align: center; color: var(--neutral-500);">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--neutral-300)" stroke-width="1.5" style="margin-bottom: 0.5rem;">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                        </svg>
                        <div style="font-weight: 600; color: var(--neutral-700);">Belum Ada Laporan Progres</div>
                        <div style="font-size: 0.8rem; margin-top: 0.2rem;">Peserta belum mengirimkan progres pekerjaan untuk tugas ini.</div>
                    </div>
                @else
                    <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                        @foreach($tugas->progresLogs->sortByDesc('created_at') as $log)
                            @php
                                $reviewVal = $log->status_review->value ?? 'menunggu';
                                $reviewBg = match($reviewVal) {
                                    'disetujui' => '#d1fae5',
                                    'perlu_revisi' => '#fee2e2',
                                    default => '#fef3c7'
                                };
                                $reviewColor = match($reviewVal) {
                                    'disetujui' => '#065f46',
                                    'perlu_revisi' => '#991b1b',
                                    default => '#92400e'
                                };
                            @endphp
                            <div style="border: 1px solid var(--neutral-200); border-radius: 10px; padding: 1.25rem; background: #ffffff;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
                                    <div>
                                        <div style="font-weight: 700; color: var(--neutral-900); font-size: 0.925rem;">
                                            Laporan Progres: {{ $log->tgl_lapor ? \Carbon\Carbon::parse($log->tgl_lapor)->format('d F Y') : $log->created_at->format('d F Y') }}
                                        </div>
                                        <div style="font-size: 0.75rem; color: var(--neutral-500);">
                                            Diajukan {{ $log->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                    <span style="padding: 0.25rem 0.65rem; border-radius: 999px; font-size: 0.75rem; font-weight: 700; background: {{ $reviewBg }}; color: {{ $reviewColor }};">
                                        {{ ucfirst(str_replace('_', ' ', $reviewVal)) }}
                                    </span>
                                </div>

                                <div style="font-size: 0.875rem; color: var(--neutral-800); line-height: 1.6; margin-bottom: 0.875rem; background: #f8fafc; padding: 1rem; border-radius: 8px; border: 1px solid var(--neutral-200); white-space: pre-line;">
                                    {{ $log->deskripsi ?: $log->catatan }}
                                </div>

                                @if($log->file)
                                    <div style="margin-bottom: 1rem;">
                                        <a href="{{ route('berkas.download', ['dokumen' => $log->file]) }}" target="_blank" style="display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.75rem; font-weight: 600; color: #0641CC; text-decoration: none; padding: 0.4rem 0.75rem; background: rgba(6, 65, 204, 0.08); border-radius: 6px;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                            Unduh Berkas Lampiran Progres
                                        </a>
                                    </div>
                                @endif

                                <!-- Review Form for this log (AC-08-4) -->
                                <div style="border-top: 1px dashed var(--neutral-200); padding-top: 1rem; margin-top: 0.75rem;">
                                    <form action="{{ route('pembimbing.progres.review', $log->id) }}" method="POST" style="display: flex; flex-direction: column; gap: 0.75rem;">
                                        @csrf
                                        <div style="display: flex; gap: 1.25rem; align-items: center; flex-wrap: wrap;">
                                            <span style="font-size: 0.8rem; font-weight: 700; color: var(--neutral-800);">Hasil Evaluasi:</span>
                                            <label style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.8125rem; color: #065f46; font-weight: 600; cursor: pointer;">
                                                <input type="radio" name="status_review" value="disetujui" {{ $reviewVal === 'disetujui' ? 'checked' : '' }} required onchange="toggleFeedbackRequired('{{ $log->id }}', false)">
                                                Disetujui
                                            </label>
                                            <label style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.8125rem; color: #991b1b; font-weight: 600; cursor: pointer;">
                                                <input type="radio" name="status_review" value="perlu_revisi" {{ $reviewVal === 'perlu_revisi' ? 'checked' : '' }} required onchange="toggleFeedbackRequired('{{ $log->id }}', true)">
                                                Perlu Revisi
                                            </label>
                                        </div>

                                        <div>
                                            <label style="display: block; font-size: 0.75rem; font-weight: 600; color: var(--neutral-600); margin-bottom: 0.25rem;">
                                                Catatan Umpan Balik / Arahan Perbaikan <span id="feedback_req_badge_{{ $log->id }}" style="color: #ef4444; display: {{ $reviewVal === 'perlu_revisi' ? 'inline' : 'none' }};">* (Wajib diisi jika perlu revisi)</span>
                                            </label>
                                            <textarea name="feedback" id="feedback_input_{{ $log->id }}" rows="2" placeholder="Tuliskan umpan balik atau instruksi perbaikan untuk peserta..." style="width: 100%; border: 1px solid var(--neutral-300); border-radius: 8px; padding: 0.65rem; font-size: 0.8125rem; font-family: inherit;">{{ old('feedback', $log->feedback) }}</textarea>
                                        </div>

                                        <div style="display: flex; justify-content: flex-end;">
                                            <button type="submit" style="height: 36px; padding: 0 1.25rem; background: #0641CC; color: #ffffff; font-weight: 600; font-size: 0.775rem; border: none; border-radius: 6px; cursor: pointer;">
                                                Simpan Umpan Balik
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column: Mentee & Status Updates -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            
            <!-- Mentee Card -->
            <div style="background: #ffffff; border: 1px solid var(--neutral-200); border-radius: 14px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <h3 style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--neutral-500); margin: 0 0 1rem 0;">
                    Peserta Bimbingan
                </h3>

                <div style="display: flex; gap: 0.875rem; align-items: center; margin-bottom: 1rem;">
                    <div style="width: 44px; height: 44px; border-radius: 50%; background: #e0e7ff; color: #3730a3; font-weight: 700; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0;">
                        {{ strtoupper(substr($peserta->nama ?? 'P', 0, 2)) }}
                    </div>
                    <div>
                        <div style="font-weight: 700; color: var(--neutral-900); font-size: 0.925rem;">
                            {{ $peserta->nama ?? 'Peserta' }}
                        </div>
                        <div style="font-size: 0.75rem; color: var(--neutral-500);">
                            {{ $peserta->profil?->institusi ?? '-' }} &bull; {{ $peserta->profil?->jurusan?->nama_jurusan ?? '-' }}
                        </div>
                    </div>
                </div>

                <div style="font-size: 0.8rem; color: var(--neutral-700); display: flex; flex-direction: column; gap: 0.4rem; border-top: 1px solid var(--neutral-100); padding-top: 0.75rem;">
                    <div><strong>Bidang:</strong> {{ $tugas->penempatan->bidang->nama_bidang ?? '-' }}</div>
                    <div><strong>Deadline Tugas:</strong> {{ $tugas->deadline?->format('d M Y') }}</div>
                    <div><strong>Status Tugas:</strong> 
                        <span style="font-weight: 600; color: {{ $tugas->status->value === 'selesai' ? '#065f46' : '#92400e' }};">
                            {{ ucfirst($tugas->status->value) }}
                        </span>
                    </div>
                </div>

                <div style="margin-top: 1rem; padding-top: 0.75rem; border-top: 1px solid var(--neutral-100);">
                    <a href="{{ route('pembimbing.peserta.show', $tugas->penempatan_id) }}" style="font-size: 0.8rem; font-weight: 600; color: #0641CC; text-decoration: none;">
                        Lihat Profil Lengkap Mentee &rarr;
                    </a>
                </div>
            </div>

            <!-- Update Status Tugas Form -->
            <div style="background: #ffffff; border: 1px solid var(--neutral-200); border-radius: 14px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <h3 style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--neutral-500); margin: 0 0 1rem 0;">
                    Perbarui Status Tugas
                </h3>

                <form action="{{ route('pembimbing.tugas.update', $tugas->id) }}" method="POST" style="display: flex; flex-direction: column; gap: 1rem;">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="penempatan_id" value="{{ $tugas->penempatan_id }}">
                    <input type="hidden" name="judul" value="{{ $tugas->judul }}">
                    <input type="hidden" name="deskripsi" value="{{ $tugas->deskripsi }}">
                    <input type="hidden" name="kategori" value="{{ is_object($tugas->kategori) ? $tugas->kategori->value : $tugas->kategori }}">
                    <input type="hidden" name="kompleksitas" value="{{ $tugas->kompleksitas->value }}">
                    <input type="hidden" name="deadline" value="{{ $tugas->deadline?->format('Y-m-d') }}">

                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--neutral-700); margin-bottom: 0.35rem;">Status Pengerjaan</label>
                        <select name="status" style="width: 100%; height: 40px; border: 1px solid var(--neutral-300); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff;">
                            <option value="belum" {{ $tugas->status->value === 'belum' ? 'selected' : '' }}>Belum Dikerjakan</option>
                            <option value="proses" {{ $tugas->status->value === 'proses' ? 'selected' : '' }}>Sedang Dikerjakan</option>
                            <option value="selesai" {{ $tugas->status->value === 'selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>

                    <button type="submit" style="width: 100%; height: 38px; background: #0641CC; color: #ffffff; font-weight: 600; font-size: 0.8125rem; border: none; border-radius: 8px; cursor: pointer;">
                        Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleFeedbackRequired(logId, isRequired) {
        const badge = document.getElementById('feedback_req_badge_' + logId);
        const input = document.getElementById('feedback_input_' + logId);
        if (badge) {
            badge.style.display = isRequired ? 'inline' : 'none';
        }
        if (input && isRequired) {
            input.setAttribute('required', 'required');
        } else if (input) {
            input.removeAttribute('required');
        }
    }
</script>
@endsection
