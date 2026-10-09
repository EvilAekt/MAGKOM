@extends('layouts.dashboard')

@section('title', 'Detail & Reviu Penugasan - Pembimbing MagKom')
@section('page_title', 'Detail & Reviu Penugasan')
@section('page_description', 'Tinjau perkembangan penyelesaian tugas peserta dan berikan umpan balik evaluasi.')

@section('breadcrumbs')
    <a href="{{ route('pembimbing.tugas.index') }}" class="breadcrumb-item">Manajemen Penugasan</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">{{ Str::limit($tugas->judul, 30) }}</span>
@endsection

@section('content')
@php
    $peserta = $tugas->penempatan->peserta;
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
        <a href="{{ route('pembimbing.tugas.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; color: var(--neutral-700); text-decoration: none; font-weight: 600; font-size: 0.875rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Kembali ke Daftar Penugasan
        </a>
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <span style="font-size: 0.8125rem; color: var(--neutral-600); font-weight: 600;">Status Tugas:</span>
            <span style="display: inline-block; padding: 0.375rem 0.875rem; border-radius: 9999px; font-size: 0.8125rem; font-weight: 700; background: {{ $statusBadgeBg }}; color: {{ $statusBadgeText }};">
                {{ $tugas->status->label() }}
            </span>
        </div>
    </div>

    <!-- 2 Column Layout -->
    <div style="display: grid; grid-template-columns: 1fr 340px; gap: 1.5rem; align-items: start;">
        <!-- Left Column: Task Overview & Progress Logs -->
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

            <!-- Progress Logs & Review List -->
            <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.75rem; box-shadow: var(--shadow-subtle);">
                <h2 style="font-size: 1.0625rem; font-weight: 700; color: var(--neutral-900); margin: 0 0 1.25rem 0; border-bottom: 1px solid var(--neutral-300); padding-bottom: 0.75rem;">
                    Laporan Perkembangan dari Peserta ({{ $tugas->progresLogs->count() }})
                </h2>

                @if($tugas->progresLogs->isEmpty())
                    <p style="text-align: center; color: var(--neutral-600); font-size: 0.875rem; margin: 2rem 0;">
                        Peserta belum mengirimkan laporan progres untuk penugasan ini.
                    </p>
                @else
                    <div style="display: flex; flex-direction: column; gap: 1.25rem;">
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
                            <div style="border: 1px solid var(--neutral-400); border-radius: 8px; padding: 1.25rem; background: var(--neutral-0);">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
                                    <div>
                                        <span style="font-weight: 700; color: var(--neutral-900); font-size: 0.9375rem;">
                                            Laporan: {{ $log->tgl_lapor->translatedFormat('d F Y') }}
                                        </span>
                                        <div style="font-size: 0.75rem; color: var(--neutral-600);">
                                            Diterima {{ $log->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                    <span style="padding: 0.25rem 0.625rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: {{ $reviewBg }}; color: {{ $reviewColor }};">
                                        {{ $log->status_review->label() ?? 'Menunggu Review' }}
                                    </span>
                                </div>

                                <div style="font-size: 0.875rem; color: var(--neutral-800); line-height: 1.6; margin-bottom: 0.875rem; background: var(--neutral-50); padding: 1rem; border-radius: 6px; border: 1px solid var(--neutral-200); white-space: pre-line;">
                                    {{ $log->catatan }}
                                </div>

                                @if($log->file)
                                    <div style="margin-bottom: 1rem;">
                                        <a href="{{ Storage::disk('private')->url($log->file) }}" target="_blank" style="display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.75rem; font-weight: 600; color: var(--blue-700); text-decoration: none; padding: 0.375rem 0.625rem; background: var(--blue-50); border-radius: 6px;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                            Unduh Berkas Lampiran Peserta
                                        </a>
                                    </div>
                                @endif

                                <!-- Review Form for this log -->
                                <div style="border-top: 1px dashed var(--neutral-300); padding-top: 1rem; margin-top: 0.75rem;">
                                    <form action="{{ route('pembimbing.progres.review', $log) }}" method="POST" style="display: flex; flex-direction: column; gap: 0.75rem;">
                                        @csrf
                                        <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                                            <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800);">Keputusan Review:</label>
                                            <label style="display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.8125rem; color: var(--green-800); font-weight: 600; cursor: pointer;">
                                                <input type="radio" name="status_review" value="disetujui" {{ $log->status_review->value === 'disetujui' ? 'checked' : '' }} required>
                                                Disetujui
                                            </label>
                                            <label style="display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.8125rem; color: var(--red-700); font-weight: 600; cursor: pointer;">
                                                <input type="radio" name="status_review" value="perlu_revisi" {{ $log->status_review->value === 'perlu_revisi' ? 'checked' : '' }} required>
                                                Perlu Revisi
                                            </label>
                                        </div>

                                        <div>
                                            <textarea name="feedback" rows="2" placeholder="Tuliskan umpan balik / arahan perbaikan untuk peserta..." required style="width: 100%; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0.625rem; font-size: 0.8125rem; font-family: inherit;">{{ old('feedback', $log->feedback) }}</textarea>
                                        </div>

                                        <div style="display: flex; justify-content: flex-end;">
                                            <button type="submit" style="display: inline-flex; align-items: center; gap: 0.375rem; height: 34px; padding: 0 1rem; background: var(--blue-600); color: #FFFFFF; font-weight: 600; font-size: 0.75rem; border: none; border-radius: 6px; cursor: pointer; transition: background 0.15s;">
                                                Simpan Feedback
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
            <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
                <h3 style="font-size: 0.875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--neutral-600); margin: 0 0 1rem 0;">
                    Peserta Bimbingan
                </h3>

                <div style="display: flex; gap: 0.875rem; align-items: center; margin-bottom: 1rem;">
                    <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--blue-100); color: var(--blue-700); font-weight: 700; display: flex; align-items: center; justify-content: center; font-size: 1.125rem; flex-shrink: 0;">
                        {{ substr($peserta->nama ?? 'P', 0, 1) }}
                    </div>
                    <div>
                        <div style="font-weight: 700; color: var(--neutral-900); font-size: 0.9375rem;">
                            {{ $peserta->nama ?? 'Peserta' }}
                        </div>
                        <div style="font-size: 0.75rem; color: var(--neutral-600);">
                            {{ $peserta->email ?? '-' }}
                        </div>
                    </div>
                </div>

                <div style="font-size: 0.8125rem; color: var(--neutral-700); display: flex; flex-direction: column; gap: 0.375rem; border-top: 1px solid var(--neutral-300); padding-top: 0.75rem;">
                    <div><strong>Bidang Penempatan:</strong> {{ $tugas->penempatan->bidang->nama_bidang ?? '-' }}</div>
                </div>
            </div>

            <!-- Update Status Tugas Card -->
            <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
                <h3 style="font-size: 0.875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--neutral-600); margin: 0 0 1rem 0;">
                    Ubah Status Penugasan
                </h3>

                <form action="{{ route('pembimbing.tugas.update', $tugas) }}" method="POST" style="display: flex; flex-direction: column; gap: 1rem;">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="penempatan_id" value="{{ $tugas->penempatan_id }}">
                    <input type="hidden" name="judul" value="{{ $tugas->judul }}">
                    <input type="hidden" name="deskripsi" value="{{ $tugas->deskripsi }}">
                    <input type="hidden" name="kategori" value="{{ is_object($tugas->kategori) ? $tugas->kategori->value : $tugas->kategori }}">
                    <input type="hidden" name="kompleksitas" value="{{ $tugas->kompleksitas->value }}">
                    <input type="hidden" name="deadline" value="{{ $tugas->deadline->format('Y-m-d') }}">

                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">Status Pengerjaan</label>
                        <select name="status" style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem; background: var(--neutral-0);">
                            <option value="belum" {{ $tugas->status->value === 'belum' ? 'selected' : '' }}>Belum Dikerjakan</option>
                            <option value="proses" {{ $tugas->status->value === 'proses' ? 'selected' : '' }}>Sedang Dikerjakan</option>
                            <option value="selesai" {{ $tugas->status->value === 'selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>

                    <button type="submit" style="width: 100%; height: 38px; background: var(--neutral-800); color: #FFFFFF; font-weight: 600; font-size: 0.8125rem; border: none; border-radius: 6px; cursor: pointer; transition: background 0.15s;">
                        Simpan Status Tugas
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
