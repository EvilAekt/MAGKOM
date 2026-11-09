@extends('layouts.dashboard')

@section('title', 'Direktori Peserta Bimbingan - Portal MagKom')
@section('topbar_title', 'Direktori Peserta Bimbingan')
@section('content_area_class', 'gentelella-fluid-canvas')

@push('styles')
    @vite(['resources/css/pages/pembimbing/peserta-index.css'])
@endpush

@section('content')
<div class="classroom-container">

    {{-- 1. Classroom Hero Banner Khas Sleman MagKom --}}
    @php
        $authUser = auth()->user();
        $bidangNama = $authUser->bidang?->nama_bidang ?? 'Dinas Komunikasi dan Informatika Sleman';
    @endphp

    <div class="classroom-banner sc-reveal-hero" data-scroll-reveal>
        <div class="banner-top-row">
            <div>
                <h1 class="banner-title">Direktori Peserta Bimbingan</h1>
                <div class="banner-subtitle">
                    <span>Bidang: <strong>{{ $bidangNama }}</strong></span>
                    <span>&bull;</span>
                    <span>Pembimbing: <strong>{{ $authUser->nama }}</strong></span>
                    <span>&bull;</span>
                    <span><strong>{{ $countAktif }} Mentee Aktif Bimbingan</strong></span>
                </div>
            </div>
            <div>
                <a href="{{ route('pembimbing.tugas.create') }}" class="btn-create-task">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    <span>Beri Tugas Baru</span>
                </a>
            </div>
        </div>
    </div>

    {{-- 2. Tile Stats MagKom Gentelella (3 Metrik Utama) --}}
    <div class="tiles-grid sc-reveal-up" data-scroll-reveal>
        <x-gentelella.tile-stat
            title="Total Mentee"
            value="{{ $totalMentee }}"
            color="teal"
            trendDirection="up"
            trendText="{{ $totalMentee }} Total"
            subText="Seluruh riwayat penempatan"
            :sparkBars="[35, 45, 55, 65, 75, 82, 88, 92, 100]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <x-gentelella.tile-stat
            title="Peserta Aktif"
            value="{{ $countAktif }}"
            color="blue"
            trendType="info"
            trendDirection="up"
            trendText="{{ $persenAktif }}%"
            subText="Sedang menjalankan magang"
            :sparkBars="[40, 50, 65, 55, 70, 60, 80, 75, 70]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <x-gentelella.tile-stat
            title="Alumni Selesai"
            value="{{ $countSelesai }}"
            color="teal"
            trendType="success"
            trendDirection="up"
            trendText="{{ $persenSelesai }}%"
            subText="Magang tuntas & lulus"
            :sparkBars="[15, 25, 40, 50, 65, 75, 85, 95, 100]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>
    </div>

    {{-- 3. Filter Bar & Segmented Tabs --}}
    <div class="peserta-filter-card sc-reveal-up sc-delay-1" data-scroll-reveal>
        <div class="peserta-tabs">
            <a href="{{ route('pembimbing.peserta.index', ['tab' => 'aktif']) }}" class="peserta-tab-btn {{ $currentTab !== 'selesai' ? 'active' : '' }}">
                <span>Peserta Aktif</span>
                <span class="peserta-tab-badge">{{ $countAktif }}</span>
            </a>
            <a href="{{ route('pembimbing.peserta.index', ['tab' => 'selesai']) }}" class="peserta-tab-btn {{ $currentTab === 'selesai' ? 'active' : '' }}">
                <span>Alumni Selesai</span>
                <span class="peserta-tab-badge">{{ $countSelesai }}</span>
            </a>
        </div>

        <form method="GET" action="{{ route('pembimbing.peserta.index') }}" class="peserta-search-form">
            <input type="hidden" name="tab" value="{{ $currentTab }}">
            <div class="peserta-search-input-wrap">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, kampus, NIM..." class="peserta-search-input">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="peserta-search-icon">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </div>
            <button type="submit" class="btn-search">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <span>Cari</span>
            </button>
            @if(request('q'))
                <a href="{{ route('pembimbing.peserta.index', ['tab' => $currentTab]) }}" class="btn-reset">Reset</a>
            @endif
        </form>
    </div>

    {{-- 4. Tabel Daftar Mentee Khas Classroom --}}
    <x-gentelella.x-panel title="Daftar Mentee Bimbingan">
        <div class="table-peserta-wrapper sc-reveal-up sc-delay-2" data-scroll-reveal>
            <table class="table-peserta">
                <thead>
                    <tr>
                        <th>Peserta Bimbingan</th>
                        <th>Asal Institusi &amp; Jurusan</th>
                        <th>Jalur &amp; Bidang</th>
                        <th>Periode Magang</th>
                        <th>Progres Durasi</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penempatanList as $p)
                        @php
                            $tglMulai = $p->tgl_mulai;
                            $tglSelesai = $p->tgl_selesai;
                            $totalHari = $tglMulai && $tglSelesai ? max(1, $tglMulai->diffInDays($tglSelesai)) : 1;
                            $hariBerjalan = $tglMulai ? max(0, \Carbon\Carbon::today()->diffInDays($tglMulai, false)) : 0;
                            $persenHari = $p->status->value === 'selesai' ? 100 : min(100, (int) round(($hariBerjalan / $totalHari) * 100));
                            $initials = collect(explode(' ', $p->peserta?->nama ?? 'P'))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
                        @endphp
                        <tr>
                            <td>
                                <div class="mentee-avatar-cell">
                                    <div class="avatar-initials-circle">
                                        {{ strtoupper($initials) }}
                                    </div>
                                    <div class="mentee-name-col">
                                        <div style="display: flex; align-items: center; gap: 6px;">
                                            <a href="{{ route('pembimbing.peserta.show', $p->id) }}" class="mentee-name-text">
                                                {{ $p->peserta?->nama }}
                                            </a>
                                        </div>
                                        <span class="mentee-sub-text">{{ $p->peserta?->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="mentee-univ-text">{{ $p->peserta?->profil?->institusi ?? '-' }}</div>
                                <div class="mentee-jurusan-text">{{ $p->peserta?->profil?->jurusan?->nama_jurusan ?? '-' }}</div>
                            </td>
                            <td>
                                <div>
                                    <x-gentelella.badge variant="info">{{ ucfirst($p->jalur?->value ?? 'Umum') }}</x-gentelella.badge>
                                </div>
                                <div class="mentee-sub-text">{{ $p->bidang?->nama_bidang ?? '-' }}</div>
                            </td>
                            <td>
                                <div class="mentee-name-text">{{ $tglMulai?->format('d M') }} &ndash; {{ $tglSelesai?->format('d M Y') }}</div>
                                <div class="mentee-sub-text">{{ $totalHari }} Hari Kalender</div>
                            </td>
                            <td>
                                <div class="progress-micro-wrap">
                                    <div class="progress-micro-track">
                                        <div class="progress-micro-fill {{ $p->status->value === 'selesai' ? 'complete' : '' }}" style="width: {{ $persenHari }}%;"></div>
                                    </div>
                                    <div class="progress-micro-label">
                                        <span>{{ $persenHari }}%</span>
                                        <span>{{ $p->status->value === 'selesai' ? 'Selesai' : $hariBerjalan . '/' . $totalHari . ' hr' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="peserta-action-group">
                                    <a href="{{ route('pembimbing.peserta.show', $p->id) }}" class="btn-peserta-action" title="Buka Profil 360°">
                                        <span>Profil</span>
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="table-empty-td">
                                Tidak ada peserta bimbingan yang sesuai dengan filter saat ini.
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
    @vite(['resources/js/pages/pembimbing/peserta-index.js'])
@endpush
@endsection
