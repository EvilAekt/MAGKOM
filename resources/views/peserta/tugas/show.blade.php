@extends('layouts.dashboard')

@section('title', 'Detail Tugas: ' . $tugas->judul . ' - Peserta MagKom')
@section('page_title', 'Detail Tugas & Proyek')
@section('page_description', 'Tinjau rincian tugas dan laporkan perkembangan berkala kepada pembimbing lapangan.')

@section('breadcrumbs')
    <a href="{{ route('peserta.tugas.index') }}" class="breadcrumb-item">Tugas & Proyek</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">{{ Str::limit($tugas->judul, 30) }}</span>
@endsection

@section('content')
@php
    $isOverdue = $tugas->deadline->isPast() && $tugas->status->value !== 'selesai';
    $statusBadgeBg = match($tugas->status->value) {
        'belum' => 'var(--neutral-200)',
        'proses' => 'var(--yellow-50)',
        'selesai' => 'var(--green-50)',
        default => 'var(--neutral-200)'
    };
    $statusBadgeText = match($tugas->status->value) {
        'belum' => 'var(--neutral-700)',
        'proses' => 'var(--yellow-800)',
        'selesai' => 'var(--green-800)',
        default => 'var(--neutral-700)'
    };
@endphp

<div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <!-- Top Action Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <a href="{{ route('peserta.tugas.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; color: var(--neutral-700); text-decoration: none; font-weight: 600; font-size: 0.875rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Kembali ke Daftar Tugas
        </a>
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <span style="font-size: 0.8125rem; color: var(--neutral-600); font-weight: 600;">Status Pengerjaan:</span>
            <span style="display: inline-block; padding: 0.375rem 0.875rem; border-radius: 9999px; font-size: 0.8125rem; font-weight: 700; background: {{ $statusBadgeBg }}; color: {{ $statusBadgeText }};">
                {{ $tugas->status->label() }}
            </span>
        </div>
    </div>

    <!-- 2 Column Layout -->
    <div style="display: grid; grid-template-columns: 1fr 340px; gap: 1.5rem; align-items: start;">
        <!-- Left Column: Task Detail & Submissions -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <!-- Task Info Card -->
            <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.75rem; box-shadow: var(--shadow-subtle);">
                <div style="display: flex; gap: 0.5rem; margin-bottom: 0.75rem; flex-wrap: wrap;">
                    <span style="padding: 0.25rem 0.625rem; background: var(--blue-50); color: var(--blue-700); border-radius: 6px; font-size: 0.75rem; font-weight: 700;">
                        Kategori: {{ is_object($tugas->kategori) ? ($tugas->kategori->label() ?? $tugas->kategori->value) : $tugas->kategori }}
                    </span>
                    <span style="padding: 0.25rem 0.625rem; background: var(--neutral-100); color: var(--neutral-700); border-radius: 6px; font-size: 0.75rem; font-weight: 700;">
                        Kompleksitas: {{ ucfirst($tugas->kompleksitas->value ?? 'Sedang') }}
                    </span>
                </div>

                <h1 style="font-size: 1.375rem; font-weight: 800; color: var(--neutral-900); margin: 0 0 1rem 0; line-height: 1.3;">
                    {{ $tugas->judul }}
                </h1>

                <div style="color: var(--neutral-800); font-size: 0.9375rem; line-height: 1.7; white-space: pre-line; background: var(--neutral-50); padding: 1.25rem; border-radius: 8px; border: 1px solid var(--neutral-300);">
                    {{ $tugas->deskripsi }}
                </div>
            </div>

            <!-- Upload Progres Form Card -->
            @if(!in_array($tugas->status->value, ['selesai', 'dibatalkan']))
                <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.75rem; box-shadow: var(--shadow-subtle);">
                    <div style="display: flex; align-items: center; gap: 0.625rem; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-300); padding-bottom: 0.75rem;">
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: var(--blue-50); color: var(--blue-600); display: flex; align-items: center; justify-content: center;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                        </div>
                        <h2 style="font-size: 1.0625rem; font-weight: 700; color: var(--neutral-900); margin: 0;">
                            Lapor Kemajuan Progres Kerja
                        </h2>
                    </div>

                    <form action="{{ route('peserta.tugas.progres', $tugas) }}" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 1.25rem;">
                        @csrf
                        <div>
                            <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">
                                Catatan / Hasil Pekerjaan <span style="color: var(--red-600);">*</span>
                            </label>
                            <textarea name="catatan" rows="4" required placeholder="Jelaskan ringkasan apa saja yang telah dikerjakan, kendala yang dihadapi, atau tautan repositori/dokumen..." style="width: 100%; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0.75rem; font-size: 0.875rem; font-family: inherit; resize: vertical;">{{ old('catatan') }}</textarea>
                            @error('catatan')
                                <span style="font-size: 0.75rem; color: var(--red-600); font-weight: 600;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">
                                Berkas Pendukung (Opsional, PDF / ZIP / Dokumen maks 2 MB)
                            </label>
                            <input type="file" name="file" accept=".pdf,.zip,.rar,.docx,.xlsx" style="width: 100%; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0.5rem; font-size: 0.875rem; background: var(--neutral-50);">
                            @error('file')
                                <span style="font-size: 0.75rem; color: var(--red-600); font-weight: 600;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div style="display: flex; justify-content: flex-end;">
                            <button type="submit" style="display: inline-flex; align-items: center; gap: 0.5rem; height: 42px; padding: 0 1.5rem; background: var(--blue-600); color: #FFFFFF; font-weight: 700; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer; transition: background 0.15s;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                                Kirim Laporan Progres
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            <!-- History of Progress Submissions -->
            <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.75rem; box-shadow: var(--shadow-subtle);">
                <h2 style="font-size: 1.0625rem; font-weight: 700; color: var(--neutral-900); margin: 0 0 1.25rem 0; border-bottom: 1px solid var(--neutral-300); padding-bottom: 0.75rem;">
                    Riwayat Laporan Progres ({{ $tugas->progresLogs->count() }})
                </h2>

                @if($tugas->progresLogs->isEmpty())
                    <p style="text-align: center; color: var(--neutral-600); font-size: 0.875rem; margin: 1.5rem 0;">
                        Belum ada laporan progres yang dikirimkan untuk tugas ini.
                    </p>
                @else
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        @foreach($tugas->progresLogs->sortByDesc('id') as $log)
                            @php
                                $reviewBg = match($log->status_review->value ?? '') {
                                    'disetujui' => 'var(--green-50)',
                                    'perlu_revisi' => 'var(--red-50)',
                                    default => 'var(--yellow-50)'
                                };
                                $reviewColor = match($log->status_review->value ?? '') {
                                    'disetujui' => 'var(--green-800)',
                                    'perlu_revisi' => 'var(--red-700)',
                                    default => 'var(--yellow-800)'
                                };
                            @endphp
                            <div style="border: 1px solid var(--neutral-300); border-radius: 8px; padding: 1.25rem; background: var(--neutral-50);">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                                    <div>
                                        <span style="font-weight: 700; color: var(--neutral-900); font-size: 0.875rem;">
                                            Laporan Tanggal {{ $log->tgl_lapor->translatedFormat('d F Y') }}
                                        </span>
                                        <div style="font-size: 0.75rem; color: var(--neutral-600);">
                                            Dikirim {{ $log->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                    <span style="padding: 0.25rem 0.625rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: {{ $reviewBg }}; color: {{ $reviewColor }};">
                                        {{ $log->status_review->label() ?? 'Menunggu Review' }}
                                    </span>
                                </div>

                                <div style="font-size: 0.875rem; color: var(--neutral-800); line-height: 1.6; margin-bottom: 0.75rem; white-space: pre-line;">
                                    {{ $log->catatan }}
                                </div>

                                @if($log->file)
                                    <div style="margin-bottom: 0.75rem;">
                                        <a href="{{ Storage::disk('private')->url($log->file) }}" target="_blank" style="display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.75rem; font-weight: 600; color: var(--blue-700); text-decoration: none; padding: 0.25rem 0.5rem; background: var(--blue-50); border-radius: 4px;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                            Unduh Berkas Lampiran Progres
                                        </a>
                                    </div>
                                @endif

                                @if($log->feedback)
                                    <div style="border-top: 1px dashed var(--neutral-300); padding-top: 0.75rem; margin-top: 0.5rem; background: var(--neutral-100); border-radius: 6px; padding: 0.75rem;">
                                        <div style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); margin-bottom: 0.25rem; display: flex; align-items: center; gap: 0.375rem;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                            Umpan Balik Pembimbing:
                                        </div>
                                        <div style="font-size: 0.8125rem; color: var(--neutral-900); line-height: 1.5; font-style: italic;">
                                            "{{ $log->feedback }}"
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column: Meta Info Card -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <!-- Deadline & Schedule Card -->
            <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
                <h3 style="font-size: 0.875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--neutral-600); margin: 0 0 1rem 0;">
                    Tenggat Waktu
                </h3>

                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <div style="padding: 1rem; border-radius: 8px; background: {{ $isOverdue ? 'var(--red-50)' : 'var(--blue-50)' }}; border: 1px solid {{ $isOverdue ? 'var(--red-200)' : 'var(--blue-200)' }};">
                        <div style="font-size: 0.75rem; font-weight: 600; color: {{ $isOverdue ? 'var(--red-700)' : 'var(--blue-700)' }};">
                            Batas Akhir:
                        </div>
                        <div style="font-size: 1.125rem; font-weight: 800; color: {{ $isOverdue ? 'var(--red-900)' : 'var(--blue-900)' }}; margin-top: 0.25rem;">
                            {{ $tugas->deadline->translatedFormat('d F Y') }}
                        </div>
                        <div style="font-size: 0.8125rem; font-weight: 600; color: {{ $isOverdue ? 'var(--red-700)' : 'var(--blue-800)' }}; margin-top: 0.25rem;">
                            @if($isOverdue)
                                ⚠️ Terlambat {{ $tugas->deadline->diffForHumans() }}
                            @else
                                Sisa waktu: {{ $tugas->deadline->diffForHumans() }}
                            @endif
                        </div>
                    </div>

                    <div style="display: flex; justify-content: space-between; font-size: 0.8125rem; padding: 0.5rem 0; border-bottom: 1px solid var(--neutral-300);">
                        <span style="color: var(--neutral-600);">Dibuat Pada</span>
                        <span style="font-weight: 600; color: var(--neutral-800);">{{ $tugas->created_at->translatedFormat('d M Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Mentor Card -->
            <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
                <h3 style="font-size: 0.875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--neutral-600); margin: 0 0 1rem 0;">
                    Pembimbing Lapangan
                </h3>

                <div style="display: flex; gap: 0.875rem; align-items: center;">
                    <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--blue-100); color: var(--blue-700); font-weight: 700; display: flex; align-items: center; justify-content: center; font-size: 1.125rem; flex-shrink: 0;">
                        {{ substr($tugas->penempatan->pembimbing->nama ?? 'P', 0, 1) }}
                    </div>
                    <div>
                        <div style="font-weight: 700; color: var(--neutral-900); font-size: 0.9375rem;">
                            {{ $tugas->penempatan->pembimbing->nama ?? 'Pembimbing' }}
                        </div>
                        <div style="font-size: 0.75rem; color: var(--neutral-600);">
                            {{ $tugas->penempatan->pembimbing->email ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
