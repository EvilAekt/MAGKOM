@extends('layouts.dashboard')

@section('title', 'Evaluasi Mutu & Survei Kepuasan - MagKom Sleman')
@section('page_title', 'Indeks Kepuasan Magang (IKM) & Net Promoter Score')
@section('page_description', 'Rekapitulasi evaluasi pengalaman bimbingan, relevansi penugasan, dan saran perbaikan dari alumni peserta (PRD 4.3 Poin 4).')

@section('breadcrumbs')
    <a href="{{ route('admin.laporan.index') }}" class="breadcrumb-item">Laporan & Ekspor</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Survei Kepuasan Magang</span>
@endsection

@section('content')
<div class="flex flex-col gap-6">

    <!-- TOP TOOLBAR -->
    <div class="flex justify-between items-center flex-wrap gap-4 bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
        <div>
            <span class="text-xs font-bold text-blue-900 uppercase tracking-wider">Survei Evaluasi Penyelenggaraan</span>
            <div class="text-sm text-slate-600 mt-1">
                Total Responden Terdata: <strong class="text-slate-900">{{ number_format($totalResponden) }} Alumni</strong>
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('admin.laporan.index') }}" class="btn btn-outline text-xs">
                &larr; Hub Laporan
            </a>
            <a href="{{ route('admin.survei.export') }}" class="btn btn-primary text-xs inline-flex items-center gap-1.5">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Ekspor Hasil Survei (CSV)
            </a>
        </div>
    </div>

    <!-- 4 RINGKASAN METRIK IKM & NPS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- IKM INDEKS KEPUASAN -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
            <div class="text-xs font-bold text-blue-800 uppercase">Indeks Kepuasan Magang (IKM)</div>
            <div class="text-3xl font-extrabold text-blue-900 mt-1">
                {{ $ikmSkala100 }} <span class="text-sm text-slate-500 font-semibold">/ 100</span>
            </div>
            <div class="text-xs text-slate-600 mt-1">
                Kategori: 
                <strong class="{{ $ikmSkala100 >= 80 ? 'text-emerald-700' : ($ikmSkala100 >= 60 ? 'text-blue-700' : 'text-amber-700') }}">
                    {{ $ikmSkala100 >= 80 ? 'Sangat Memuaskan (A)' : ($ikmSkala100 >= 60 ? 'Memuaskan (B)' : 'Cukup (C)') }}
                </strong>
            </div>
        </div>

        <!-- NET PROMOTER SCORE -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
            <div class="text-xs font-bold text-emerald-800 uppercase">Net Promoter Score (NPS)</div>
            <div class="text-3xl font-extrabold {{ $npsScore >= 50 ? 'text-emerald-700' : ($npsScore >= 0 ? 'text-blue-700' : 'text-rose-700') }} mt-1">
                {{ $npsScore > 0 ? '+' : '' }}{{ $npsScore }}
            </div>
            <div class="text-xs text-slate-600 mt-1">
                {{ $promoters }} Promoters &bull; {{ $passives }} Passives &bull; {{ $detractors }} Detractors
            </div>
        </div>

        <!-- SKOR MENTORING -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem; box-shadow: var(--shadow-subtle);">
            <div style="font-size: 0.75rem; font-weight: 700; color: var(--purple-700, #7E22CE); text-transform: uppercase;">Bimbingan Mentor</div>
            <div style="font-size: 2.25rem; font-weight: 900; color: var(--purple-700, #7E22CE); margin-top: 0.25rem;">
                {{ $avgMentoring }} <span style="font-size: 1rem; color: var(--neutral-500); font-weight: 600;">/ 5.0</span>
            </div>
            <div style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.25rem;">Kualitas arahan & asistensi pembimbing</div>
        </div>

        <!-- SKOR RELEVANSI TUGAS -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem; box-shadow: var(--shadow-subtle);">
            <div style="font-size: 0.75rem; font-weight: 700; color: var(--amber-700); text-transform: uppercase;">Relevansi Tugas Proyek</div>
            <div style="font-size: 2.25rem; font-weight: 900; color: var(--amber-700); margin-top: 0.25rem;">
                {{ $avgTugas }} <span style="font-size: 1rem; color: var(--neutral-500); font-weight: 600;">/ 5.0</span>
            </div>
            <div style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.25rem;">Kesesuaian kurikulum kerja kejuruan</div>
        </div>
    </div>

    <!-- DAFTAR MASUKAN & TESTIMONI SURVEI -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
        <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0 0 0.5rem 0;">Daftar Evaluasi & Testimoni Peserta</h3>
        <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0 0 1.25rem 0;">Rekap masukan kualitatif untuk perbaikan mutu dinas secara berkelanjutan.</p>

        <div style="overflow-x: auto;" class="clean-scrollbar">
            <table class="table" style="width: 100%; text-align: left; border-collapse: collapse; font-size: 0.8125rem;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--neutral-300); color: var(--neutral-600);">
                        <th style="padding: 0.75rem;">Peserta & Institusi</th>
                        <th style="padding: 0.75rem; text-align: center;">Mentoring</th>
                        <th style="padding: 0.75rem; text-align: center;">Tugas</th>
                        <th style="padding: 0.75rem; text-align: center;">Fasilitas</th>
                        <th style="padding: 0.75rem; text-align: center;">NPS</th>
                        <th style="padding: 0.75rem; width: 40%;">Umpan Balik & Saran Perbaikan</th>
                        <th style="padding: 0.75rem; text-align: right;">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($daftarSurvei as $item)
                        @php
                            $peserta = $item->penempatan?->peserta;
                        @endphp
                        <tr style="border-bottom: 1px solid var(--neutral-200);">
                            <td style="padding: 0.75rem;">
                                <strong style="color: var(--neutral-900); display: block;">{{ $peserta?->nama ?? $peserta?->name ?? '-' }}</strong>
                                <span style="font-size: 0.75rem; color: var(--neutral-600);">{{ $peserta?->profil?->institusi ?? '-' }}</span>
                            </td>
                            <td style="padding: 0.75rem; text-align: center; font-weight: 700; color: var(--purple-700, #7E22CE);">
                                {{ $item->skor_mentoring }}/5
                            </td>
                            <td style="padding: 0.75rem; text-align: center; font-weight: 700; color: var(--blue-700);">
                                {{ $item->skor_tugas }}/5
                            </td>
                            <td style="padding: 0.75rem; text-align: center; font-weight: 700; color: var(--amber-700);">
                                {{ $item->skor_fasilitas }}/5
                            </td>
                            <td style="padding: 0.75rem; text-align: center;">
                                <span class="badge {{ $item->skor_nps >= 9 ? 'badge-success' : ($item->skor_nps >= 7 ? 'badge-warning' : 'badge-danger') }}" style="font-weight: 800; font-size: 0.75rem;">
                                    {{ $item->skor_nps }}/10
                                </span>
                            </td>
                            <td style="padding: 0.75rem;">
                                @if($item->umpan_balik)
                                    <p style="margin: 0 0 0.25rem 0; font-style: italic; color: var(--neutral-800);">
                                        "{{ $item->umpan_balik }}"
                                    </p>
                                @endif
                                @if($item->saran)
                                    <div style="font-size: 0.75rem; color: var(--blue-800); background: var(--blue-50); padding: 0.25rem 0.5rem; border-radius: 6px; display: inline-block;">
                                        <strong>Saran:</strong> {{ $item->saran }}
                                    </div>
                                @endif
                                @if(!$item->umpan_balik && !$item->saran)
                                    <span style="color: var(--neutral-400); font-style: italic;">Tidak ada catatan teks.</span>
                                @endif
                            </td>
                            <td style="padding: 0.75rem; text-align: right; color: var(--neutral-600); white-space: nowrap;">
                                {{ $item->created_at->translatedFormat('d M Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2.5rem; color: var(--neutral-500);">
                                Belum ada pengisian survei kepuasan magang dari peserta.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 1rem;">
            {{ $daftarSurvei->links() }}
        </div>
    </div>

</div>
@endsection
