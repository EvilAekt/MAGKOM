@extends('layouts.dashboard')

@section('title', 'Evaluasi Rubrik & Penilaian Magang - Portal MagKom')
@section('topbar_title', 'Evaluasi Rubrik & Penilaian Magang')
@section('content_area_class', 'gentelella-fluid-canvas')

@push('styles')
    @vite(['resources/css/pages/pembimbing/penilaian-index.css'])
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
                <h1 class="banner-title">Evaluasi Rubrik &amp; Penilaian Magang</h1>
                <div class="banner-subtitle">
                    <span>Bidang: <strong>{{ $bidangNama }}</strong></span>
                    <span>&bull;</span>
                    <span>Pembimbing: <strong>{{ $authUser->nama }}</strong></span>
                    <span>&bull;</span>
                    <span><strong>{{ $countFinal }} dari {{ $totalMentee }} Nilai Final</strong></span>
                </div>
            </div>
            <div>
                <a href="{{ route('pembimbing.penilaian.ekspor-bidang') }}" class="btn-create-task">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    <span>Ekspor Rekap Bimbingan (CSV)</span>
                </a>
            </div>
        </div>
    </div>

    {{-- 2. Tile Stats MagKom Gentelella (4 Metrik Evaluasi Nilai) --}}
    <div class="tiles-grid">
        <x-gentelella.tile-stat
            title="Total Peserta"
            value="{{ $totalMentee }}"
            color="teal"
            trendDirection="up"
            trendText="{{ $totalMentee }} Mentee"
            subText="Total peserta binaan"
            :sparkBars="[30, 45, 55, 65, 75, 85, 90, 95, 100]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <x-gentelella.tile-stat
            title="Belum Dinilai"
            value="{{ $countBelumDinilai }}"
            color="blue"
            trendType="info"
            trendDirection="up"
            trendText="{{ $persenBelum }}%"
            subText="Menunggu evaluasi akhir"
            :sparkBars="[50, 60, 55, 70, 65, 60, 50, 45, 40]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <x-gentelella.tile-stat
            title="Draf Tersimpan"
            value="{{ $countDraft }}"
            color="yellow"
            trendType="warning"
            trendDirection="down"
            trendText="{{ $countDraft }} Draf"
            subText="Belum difinalisasi resmi"
            :sparkBars="[20, 35, 40, 50, 45, 60, 55, 50, 45]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <x-gentelella.tile-stat
            title="Final Resmi"
            value="{{ $countFinal }}"
            color="teal"
            trendType="success"
            trendDirection="up"
            trendText="{{ $persenFinal }}%"
            subText="Nilai terkunci &amp; terverifikasi"
            :sparkBars="[10, 20, 30, 45, 60, 70, 80, 90, 98]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>
    </div>

    {{-- 3. Info Banner Panduan Rubrik --}}
    <div class="rubrik-guide-banner">
        <div class="rubrik-guide-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
        </div>
        <div class="rubrik-guide-content">
            <h3 class="rubrik-guide-title">Petunjuk Evaluasi Rubrik Resmi Diskominfo Sleman</h3>
            <p class="rubrik-guide-desc">
                Nilai akhir dikalkulasi otomatis berbobot 100% dari 5 kriteria rubrik aktif. Status Draf belum tampil ke peserta. Status Final mengunci nilai resmi, dan setiap perubahan selanjutnya wajib menyertakan alasan revisi minimal 10 karakter untuk jejak audit (AC-10-3).
            </p>
        </div>
    </div>

    {{-- 4. Filter Bar Card Modern --}}
    <div class="penilaian-filter-card">
        <form method="GET" action="{{ route('pembimbing.penilaian.index') }}" class="penilaian-filter-form">
            <div class="filter-group">
                <label>Status Nilai</label>
                <select name="status_nilai" class="filter-control">
                    <option value="">Semua Status Nilai</option>
                    <option value="belum" {{ request('status_nilai') === 'belum' ? 'selected' : '' }}>Belum Dinilai</option>
                    <option value="draft" {{ request('status_nilai') === 'draft' ? 'selected' : '' }}>Draf Tersimpan</option>
                    <option value="final" {{ request('status_nilai') === 'final' ? 'selected' : '' }}>Final Resmi</option>
                </select>
            </div>

            <div class="filter-group">
                <label>Status Magang</label>
                <select name="status_penempatan" class="filter-control">
                    <option value="">Semua Status Magang</option>
                    <option value="aktif" {{ request('status_penempatan') === 'aktif' ? 'selected' : '' }}>Aktif Berjalan</option>
                    <option value="selesai" {{ request('status_penempatan') === 'selesai' ? 'selected' : '' }}>Selesai Ditetapkan</option>
                </select>
            </div>

            <div class="filter-group filter-group-search">
                <label>Cari Mentee</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau institusi..." class="filter-control">
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-filter-apply">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                    <span>Filter</span>
                </button>
                @if(request()->hasAny(['status_nilai', 'status_penempatan', 'q']))
                    <a href="{{ route('pembimbing.penilaian.index') }}" class="btn-filter-reset">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- 5. Tabel Evaluasi Nilai Khas Classroom --}}
    <x-gentelella.x-panel title="Daftar Evaluasi Nilai Mentee" subtitle="Rekap status draf, finalisasi rubrik, dan riwayat perbaikan nilai">
        <div class="table-penilaian-wrapper">
            <table class="table-penilaian">
                <thead>
                    <tr>
                        <th>Peserta Magang</th>
                        <th>Institusi &amp; Jurusan</th>
                        <th>Status Magang</th>
                        <th>Status Evaluasi</th>
                        <th>Nilai Akhir &amp; Predikat</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penempatanList as $item)
                        @php
                            $penilaian = $item->penilaian;
                            $statusNilai = $penilaian ? $penilaian->status->value : 'belum';
                            $initials = collect(explode(' ', $item->peserta?->nama ?? 'P'))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
                        @endphp
                        <tr>
                            <td>
                                <div class="mentee-identity-flex">
                                    <div class="avatar-initials-circle">
                                        {{ strtoupper($initials) }}
                                    </div>
                                    <div>
                                        <div style="font-weight: 700; color: #0F172A;">
                                            {{ $item->peserta?->nama ?? '-' }}
                                        </div>
                                        <div style="font-size: 0.75rem; color: #64748B;">
                                            {{ $item->peserta?->email ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: #334155;">
                                    {{ $item->peserta?->profil?->institusi ?? '-' }}
                                </div>
                                <div style="font-size: 0.75rem; color: #64748B;">
                                    {{ $item->peserta?->profil?->jurusan?->nama_jurusan ?? '-' }}
                                </div>
                            </td>
                            <td>
                                @if($item->status->value === 'aktif')
                                    <x-gentelella.badge variant="info">Aktif</x-gentelella.badge>
                                @else
                                    <x-gentelella.badge variant="secondary">Selesai</x-gentelella.badge>
                                @endif
                            </td>
                            <td>
                                @if($statusNilai === 'final')
                                    <x-gentelella.badge variant="success">Final Resmi</x-gentelella.badge>
                                @elseif($statusNilai === 'draft')
                                    <x-gentelella.badge variant="warning">Draf Tersimpan</x-gentelella.badge>
                                @else
                                    <x-gentelella.badge variant="secondary">Belum Dinilai</x-gentelella.badge>
                                @endif
                            </td>
                            <td>
                                @if($penilaian && $penilaian->nilai_akhir !== null)
                                    <div>
                                        <span class="score-badge">{{ number_format((float) $penilaian->nilai_akhir, 2) }}</span>
                                        <span class="score-predicate">({{ $penilaian->predikat->value ?? $penilaian->predikat }})</span>
                                    </div>
                                @else
                                    <span style="color: #94A3B8; font-size: 0.8125rem;">-</span>
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; gap: 6px; justify-content: flex-end; align-items: center;">
                                    @if($penilaian && $penilaian->status->value === 'final')
                                        <a href="{{ route('pembimbing.penilaian.unduh', $item->id) }}" target="_blank" title="Unduh Lembar Nilai PDF" class="btn-penilaian-action btn-secondary-action">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                        </a>
                                    @endif
                                    <a href="{{ route('pembimbing.penilaian.show', $item->id) }}" class="btn-penilaian-action">
                                        <span>{{ $penilaian ? 'Buka Nilai' : 'Input Nilai' }}</span>
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="table-empty-td">
                                Belum ada data peserta bimbingan yang sesuai dengan kriteria filter saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($penempatanList->hasPages())
            <div style="margin-top: 24px;">
                {{ $penempatanList->links() }}
            </div>
        @endif
    </x-gentelella.x-panel>

</div>

@push('scripts')
    @vite(['resources/js/pages/pembimbing/penilaian-index.js'])
@endpush
@endsection
