@extends('layouts.dashboard')

@section('title', 'Bulan & Kuota - Admin MagKom Sleman')

@section('topbar_title', 'Manajemen Kuota Magang')

@push('styles')
    @vite(['resources/css/pages/admin/kuota-index.css'])
@endpush

@section('content')
<div class="adm-kuota-revamp-wrap">


    {{-- =========================================================================
         HERO BANNER: INFORMASI BULAN & KUOTA (TERINTEGRASI RINGKAS)
         ========================================================================= --}}
    @if($selectedBulan)
        <div class="hero-banner-blue">
            <div class="hero-left-content">
                <div class="hero-header-eyebrow" style="display: flex; align-items: center; gap: 0.65rem; margin-bottom: 0.625rem; flex-wrap: wrap;">
                    <h1 class="kuota-page-title" style="font-size: 1.125rem; font-weight: 800; color: #FFFFFF; text-transform: uppercase; letter-spacing: 0.05em; margin: 0; line-height: 1.2;">Bulan &amp; kuota</h1>
                </div>
                <p class="kuota-page-subtitle sr-only">Atur bulan pendaftaran dan kuota magang per bidang.</p>
                
                <div class="hero-main-title">
                    <span>{{ $selectedBulan->nama_bulan ?? ($selectedBulan->bulan_mulai?->translatedFormat('F Y') ?? 'Gelombang 1') }}</span>
                    @if($isDraft ?? false)
                        <span class="hero-status-pill hero-status-draft">
                            <span class="hero-status-dot hero-dot-draft"></span>
                            <span>Draft</span>
                        </span>
                    @elseif($isPendaftaranBuka)
                        <span class="hero-status-pill hero-status-open">
                            <span style="width: 7px; height: 7px; border-radius: 50%; background: #16A34A;"></span>
                            <span>Dibuka</span>
                        </span>
                    @elseif($isTerjadwal ?? false)
                        <span class="hero-status-pill hero-status-scheduled">
                            <span class="hero-status-dot hero-dot-scheduled"></span>
                            <span>Terjadwal</span>
                        </span>
                    @else
                        <span class="hero-status-pill hero-status-closed">
                            <span style="width: 7px; height: 7px; border-radius: 50%; background: #CBD5E1;"></span>
                            <span>Ditutup</span>
                        </span>
                    @endif
                </div>

                <div class="hero-meta-grid">
                    {{-- 1. Tanggal Buka --}}
                    <div class="hero-meta-item">
                        <div class="hero-icon-box">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </div>
                        <div>
                            <span class="hero-meta-label">Tanggal buka</span>
                            <span class="hero-meta-val">
                                {{ $selectedBulan->tgl_buka ? \Carbon\Carbon::parse($selectedBulan->tgl_buka)->translatedFormat('d F Y') : '-' }}
                            </span>
                        </div>
                    </div>

                    {{-- 2. Tanggal Tutup --}}
                    <div class="hero-meta-item">
                        <div class="hero-icon-box">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path>
                                <line x1="4" y1="22" x2="4" y2="15"></line>
                            </svg>
                        </div>
                        <div>
                            <span class="hero-meta-label">Tanggal tutup</span>
                            <span class="hero-meta-val">
                                {{ $selectedBulan->tgl_tutup ? \Carbon\Carbon::parse($selectedBulan->tgl_tutup)->translatedFormat('d F Y') : '-' }}
                            </span>
                        </div>
                    </div>

                    {{-- 3. Mulai Magang --}}
                    <div class="hero-meta-item">
                        <div class="hero-icon-box">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="hero-meta-label">Mulai magang</span>
                            <span class="hero-meta-val">
                                {{ $selectedBulan->bulan_mulai ? $selectedBulan->bulan_mulai->translatedFormat('d F Y') : '-' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stat Box Kanan --}}
            <div class="hero-stat-card">
                <div class="stat-card-label">Kuota terisi <span class="sr-only">Okupansi:</span></div>
                <div class="stat-card-number-row">
                    <span class="stat-card-num-big">{{ $totalTerisi }}</span>
                    <span class="stat-card-num-sub">/ {{ $totalKapasitas }} peserta</span>
                </div>
                <div class="stat-card-bar-wrap">
                    <div class="stat-card-bar-fill" style="width: {{ $globalPersen }}%;"></div>
                </div>
                <div class="stat-card-countdown">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <span>
                        @if($isDraft ?? false)
                            Status Draft — belum dibuka di portal publik
                        @elseif($isPendaftaranBuka && $sisaHariTutup >= 0)
                            Ditutup dalam {{ $sisaHariTutup }} hari
                        @elseif($isPendaftaranBuka)
                            Pendaftaran sedang dibuka
                        @elseif(($isTerjadwal ?? false) && $selectedBulan->tgl_buka)
                            Pendaftaran dibuka mulai {{ \Carbon\Carbon::parse($selectedBulan->tgl_buka)->translatedFormat('d F Y') }}
                        @else
                            Pendaftaran ditutup
                        @endif
                    </span>
                </div>
            </div>
        </div>
    @else
        <div class="hero-banner-blue">
            <div class="hero-left-content">
                <div class="hero-header-eyebrow" style="display: flex; align-items: center; gap: 0.65rem; margin-bottom: 0.625rem; flex-wrap: wrap;">
                    <h1 class="kuota-page-title" style="font-size: 1.125rem; font-weight: 800; color: #FFFFFF; text-transform: uppercase; letter-spacing: 0.05em; margin: 0; line-height: 1.2;">Bulan &amp; kuota</h1>
                </div>
                <p class="kuota-page-subtitle sr-only">Atur bulan pendaftaran dan kuota magang per bidang.</p>
                <div class="hero-main-title">
                    <span>Belum Ada Periode Bulan Magang</span>
                </div>
                @if(! auth()->user()?->isSuperAdmin())
                    <div class="hero-empty-actions">
                        <button type="button" class="btn-primary-action" data-kuota-action="open-buat-bulan">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            <span>Buat bulan baru</span>
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- =========================================================================
         3. CONTROL SUB-BAR: SELECTOR BULAN & TOMBOL AKSI DI BAWAH KOTAKAN BIRU
         ========================================================================= --}}
    @if($bulanList->isNotEmpty())
        <div class="month-switch-strip">
            <div style="display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 0.45rem;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0641CC" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <span class="kuota-filter-label">Bulan Magang:</span>
                </div>
                <form method="GET" action="{{ route('admin.kuota.index') }}" style="display: flex; align-items: center; margin: 0;">
                    <select name="bulan_magang_id" data-action="auto-submit-form">
                        @foreach($bulanList as $b)
                            <option value="{{ $b->id }}" {{ $selectedBulan && $selectedBulan->id === $b->id ? 'selected' : '' }}>
                                {{ $b->nama_bulan ? $b->nama_bulan . ' (' . $b->bulan_mulai?->translatedFormat('F Y') . ')' : $b->bulan_mulai?->translatedFormat('F Y') }} {{ $b->aktif ? '(Aktif)' : '(Draft)' }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="kuota-page-actions">
                @if(auth()->user()?->isSuperAdmin())
                    <span class="kuota-active-chip">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                        <span>Mode Lihat Saja</span>
                    </span>
                @else
                    @if($selectedBulan && ! $selectedBulan->aktif)
                        <button type="button" class="btn-publish-draft-action" data-kuota-action="publish-draft">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            <span>Buka bulan</span>
                        </button>
                    @endif

                    @if($selectedBulan)
                        <button type="button" data-action="open-ubah-bulan" class="btn-outline-action">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 20h9"></path>
                                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                            </svg>
                            <span>Ubah bulan</span>
                        </button>
                    @endif

                    <button type="button" data-action="open-buat-bulan" class="btn-primary-action">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        <span>Buat bulan baru</span>
                    </button>
                @endif
            </div>
        </div>
    @endif

    {{-- =========================================================================
         4. SECTION & TABEL KUOTA PER BIDANG
         ========================================================================= --}}
    <x-gentelella.x-panel 
        title="Kuota per bidang"
    >
        @if($selectedBulan && !auth()->user()?->isSuperAdmin())
            <x-slot:actions>
                <button type="button" data-action="open-tambah-kuota" class="btn-primary-action btn-tambah-kuota-header">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    <span>Tambah kuota</span>
                </button>
            </x-slot:actions>
        @endif

        <div class="card-table-wrap">
            <div style="overflow-x: auto;">
                <table class="table-bidang">
                    <thead>
                        <tr>
                            <th style="width: 20%; min-width: 180px; text-align: left;">Bidang</th>
                            <th style="width: 34%; min-width: 250px; text-align: left;">Jurusan yang Dibutuhkan</th>
                            <th style="width: 10%; min-width: 85px; text-align: center;">Jenjang</th>
                            <th style="width: 10%; min-width: 85px; text-align: center;">Durasi</th>
                            <th style="width: 11%; min-width: 95px; text-align: center;">Kuota Terisi</th>
                            <th style="width: 9%; min-width: 85px; text-align: center;">Kapasitas</th>
                            <th style="width: 6%; min-width: 75px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bidangList as $item)
                            <tr>
                                {{-- 1. Bidang --}}
                                <td>
                                    <div class="bidang-cell" style="display: flex; align-items: center; gap: 0.85rem;">
                                        <div class="avatar-initial" style="background: {{ $item['bg_color'] }}; color: {{ $item['text_color'] }};">
                                            {{ $item['inisial'] }}
                                        </div>
                                        <span class="bidang-name">{{ $item['nama'] }}</span>
                                    </div>
                                </td>

                                {{-- 2. Jurusan yang Dibutuhkan --}}
                                <td>
                                    <div class="jurusan-tags-wrap">
                                        @if(!empty($item['jurusan_list']) && count($item['jurusan_list']) > 0)
                                            @foreach($item['jurusan_list'] as $j)
                                                <span class="jurusan-tag-item">{{ $j->nama_jurusan }}</span>
                                            @endforeach
                                        @else
                                            <span class="jurusan-text-muted">{{ $item['jurusan_names'] }}</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- 3. Jenjang --}}
                                <td style="text-align: center;">
                                    <div class="jenjang-badge-group" style="justify-content: center;">
                                        @foreach($item['jenjang_badges'] as $badge)
                                            <span class="badge-jenjang-pill">{{ $badge }}</span>
                                        @endforeach
                                    </div>
                                </td>

                                {{-- 4. Durasi --}}
                                <td style="text-align: center;">
                                    <span class="kuota-durasi-text">
                                        {{ $item['durasi'] }}
                                    </span>
                                </td>

                                {{-- 5. Kuota Terisi --}}
                                <td style="text-align: center;">
                                    <div class="kuota-terisi-val">
                                        {{ $item['terisi'] }} <span class="kuota-kapasitas-slash">/ {{ $item['kapasitas'] }}</span>
                                    </div>
                                    <div class="sr-only" aria-hidden="true">{{ $item['terisi'] }}/{{ $item['kapasitas'] }} {{ $item['persen'] }}%</div>
                                </td>

                                {{-- 6. Kapasitas & Status Ketersediaan Bidang --}}
                                <td style="text-align: center;">
                                    <span class="kuota-filter-label" style="font-size: 0.875rem;">
                                        {{ $item['kapasitas'] }} <span class="kuota-kapasitas-slash">Peserta</span>
                                    </span>
                                    <div class="sr-only" aria-hidden="true" style="display: none;">
                                        @if($item['kapasitas'] > 0)
                                            <span class="badge-status-bidang-buka">Dibuka &bull; {{ $item['kapasitas'] }} peserta</span>
                                        @else
                                            <span class="badge-status-bidang-tutup">Ditutup &bull; 0 peserta</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- 7. Aksi --}}
                                <td style="text-align: center;">
                                    @if(auth()->user()?->isSuperAdmin())
                                        <span style="font-size: 0.75rem; color: #94A3B8; font-weight: 500;">Read-Only</span>
                                    @else
                                        <button type="button" 
                                                class="btn-edit-action"
                                                data-action="open-edit-bidang"
                                                data-bidang-id="{{ $item['id'] }}"
                                                data-bidang-nama="{{ $item['nama'] }}"
                                                data-kapasitas-bimbingan="{{ $item['kapasitas_bimbingan'] }}"
                                                data-pembimbing-count="{{ $item['pembimbing_count'] }}"
                                                data-jurusans="{{ json_encode($item['jurusan_list']->map(fn($j) => ['id' => $j->id, 'nama' => $j->nama_jurusan, 'jumlah' => $selectedBulan?->kuota->where('jurusan_id', $j->id)->first()?->jumlah ?? 0, 'terisi' => $selectedBulan?->kuota->where('jurusan_id', $j->id)->first()?->terisi ?? 0])) }}">
                                            Ubah
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 3rem; color: #64748B;">
                                    Belum ada data bidang terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </x-gentelella.x-panel>

</div>

{{-- =========================================================================
     MODAL 1: UBAH BULAN (JADWAL PENDAFTARAN & BULAN MULAI)
     ========================================================================= --}}
{{-- =========================================================================
     MODAL 1: UBAH BULAN (JADWAL PENDAFTARAN & BULAN MULAI)
     ========================================================================= --}}
@if($selectedBulan)
<div id="modalUbahBulan" class="modal-overlay">
    <div class="modal-card" style="max-width: 480px; padding: 1.75rem; border-radius: 14px;">
        <div class="modal-header-row" style="align-items: flex-start; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid #E2E8F0;">
            <div>
                <h3 class="modal-title" style="font-size: 1.25rem; font-weight: 800; color: #0F172A;">Ubah bulan</h3>
                <p class="modal-header-desc" style="font-size: 0.8125rem; color: #64748B; margin: 0.25rem 0 0 0;">Tentukan jadwal pendaftaran dan mulai magang.</p>
            </div>
            <button type="button" data-action="close-ubah-bulan" class="modal-btn-close-box" aria-label="Tutup">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.kuota.bulan.update', $selectedBulan->id) }}" id="formUbahBulan">
            @csrf
            @method('PUT')

            {{-- 1. Nama Bulan --}}
            <div class="form-row-group">
                <label style="font-size: 0.8125rem; font-weight: 700; color: #1E293B;">Nama bulan</label>
                <input type="text" 
                       name="nama_bulan" 
                       id="ubahNamaBulan"
                       value="{{ $selectedBulan->nama_bulan }}" 
                       placeholder="Contoh: Gelombang 2" 
                       style="height: 42px; border: 1px solid #CBD5E1; border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem;">
            </div>

            {{-- 2. Tanggal Buka & Tanggal Tutup (Grid 2 Kolom) --}}
            <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.875rem; width: 100%;">
                <div class="form-row-group">
                    <label style="font-size: 0.8125rem; font-weight: 700; color: #1E293B;">Tanggal buka</label>
                    <div class="input-calendar-wrap">
                        <input type="date" 
                               name="tgl_buka" 
                               id="ubahBulanTglBuka"
                               value="{{ $selectedBulan->tgl_buka ? \Carbon\Carbon::parse($selectedBulan->tgl_buka)->format('Y-m-d') : '' }}"
                               required 
                               placeholder="Pilih tanggal">
                        <span class="input-calendar-icon">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="form-row-group">
                    <label style="font-size: 0.8125rem; font-weight: 700; color: #1E293B;">Tanggal tutup</label>
                    <div class="input-calendar-wrap">
                        <input type="date" 
                               name="tgl_tutup" 
                               id="ubahBulanTglTutup"
                               value="{{ $selectedBulan->tgl_tutup ? \Carbon\Carbon::parse($selectedBulan->tgl_tutup)->format('Y-m-d') : '' }}"
                               required 
                               placeholder="Pilih tanggal">
                        <span class="input-calendar-icon">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </span>
                    </div>
                </div>
            </div>

            {{-- 3. Tanggal Mulai Magang --}}
            <div class="form-row-group">
                <label style="font-size: 0.8125rem; font-weight: 700; color: #1E293B;">Tanggal mulai magang</label>
                <div class="input-calendar-wrap">
                    <input type="date" 
                           name="bulan_mulai" 
                           id="ubahBulanMulai"
                           value="{{ $selectedBulan->bulan_mulai?->format('Y-m-d') }}"
                           required 
                           placeholder="Pilih tanggal">
                    <span class="input-calendar-icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </span>
                </div>
                <span class="form-helper-text">Harus setelah tanggal tutup pendaftaran.</span>
            </div>

            {{-- 4. Status Bulan (Segmented Pills) --}}
            <div class="form-row-group" style="margin-top: 0.5rem; margin-bottom: 0.5rem;">
                <label style="font-size: 0.8125rem; font-weight: 700; color: #1E293B; margin-bottom: 0.4rem;">Status bulan</label>
                <input type="hidden" name="status_awal" id="statusUbahInput" value="{{ $selectedBulan->aktif ? 'dibuka' : 'draft' }}">
                <div class="status-pills-group">
                    <button type="button" 
                            id="btnUbahStatusDraft"
                            data-action="set-status-ubah-bulan" data-status="draft" 
                            class="btn-status-pill {{ ! $selectedBulan->aktif ? 'active' : '' }}">
                        <span id="checkIconUbahDraft" style="display: {{ ! $selectedBulan->aktif ? 'inline-flex' : 'none' }}; align-items: center;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </span>
                        <span>Draft</span>
                    </button>
                    <button type="button" 
                            id="btnUbahStatusDibuka"
                            data-action="set-status-ubah-bulan" data-status="dibuka" 
                            class="btn-status-pill {{ $selectedBulan->aktif ? 'active' : '' }}">
                        <span id="checkIconUbahDibuka" style="display: {{ $selectedBulan->aktif ? 'inline-flex' : 'none' }}; align-items: center;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </span>
                        <span>Dibuka</span>
                    </button>
                </div>
            </div>

            {{-- 5. Alert Callout Box --}}
            <div class="modal-callout-info">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0;">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                <span>Perubahan jadwal pendaftaran dan mulai magang akan langsung disinkronkan ke portal publik.</span>
            </div>

            {{-- 6. Footer Buttons --}}
            <div class="modal-footer-btns" style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #E2E8F0;">
                <button type="button" data-action="close-ubah-bulan" class="btn-outline-action" style="padding: 0.6rem 1.35rem; font-weight: 600;">
                    Batal
                </button>
                <button type="submit" id="btnSubmitUbahBulan" class="btn-primary-action" style="padding: 0.6rem 1.4rem; font-weight: 700;">
                    Simpan perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- =========================================================================
     MODAL 2: BUAT BULAN BARU
     ========================================================================= --}}
<div id="modalBuatBulan" class="modal-overlay">
    <div class="modal-card" style="max-width: 480px; padding: 1.75rem; border-radius: 14px;">
        <div class="modal-header-row" style="align-items: flex-start; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid #E2E8F0;">
            <div>
                <h3 class="modal-title" style="font-size: 1.25rem; font-weight: 800; color: #0F172A;">Buat bulan baru</h3>
                <p class="modal-header-desc" style="font-size: 0.8125rem; color: #64748B; margin: 0.25rem 0 0 0;">Tentukan jadwal pendaftaran dan mulai magang.</p>
            </div>
            <button type="button" data-action="close-buat-bulan" class="modal-btn-close-box" aria-label="Tutup">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.kuota.bulan.store') }}" id="formBuatBulan">
            @csrf

            {{-- 1. Nama Bulan --}}
            <div class="form-row-group">
                <label style="font-size: 0.8125rem; font-weight: 700; color: #1E293B;">Nama bulan</label>
                <input type="text" 
                       name="nama_bulan" 
                       placeholder="Contoh: Gelombang 3" 
                       style="height: 42px; border: 1px solid #CBD5E1; border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem;">
            </div>

            {{-- 2. Tanggal Buka & Tanggal Tutup (Grid 2 Kolom) --}}
            <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.875rem; width: 100%;">
                <div class="form-row-group">
                    <label style="font-size: 0.8125rem; font-weight: 700; color: #1E293B;">Tanggal buka</label>
                    <div class="input-calendar-wrap">
                        <input type="date" 
                               name="tgl_buka" 
                               id="buatBulanTglBuka"
                               required 
                               placeholder="Pilih tanggal">
                        <span class="input-calendar-icon">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="form-row-group">
                    <label style="font-size: 0.8125rem; font-weight: 700; color: #1E293B;">Tanggal tutup</label>
                    <div class="input-calendar-wrap">
                        <input type="date" 
                               name="tgl_tutup" 
                               id="buatBulanTglTutup"
                               required 
                               placeholder="Pilih tanggal">
                        <span class="input-calendar-icon">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </span>
                    </div>
                </div>
            </div>

            {{-- 3. Tanggal Mulai Magang --}}
            <div class="form-row-group">
                <label style="font-size: 0.8125rem; font-weight: 700; color: #1E293B;">Tanggal mulai magang</label>
                <div class="input-calendar-wrap">
                    <input type="date" 
                           name="bulan_mulai" 
                           id="buatBulanMulai"
                           required 
                           placeholder="Pilih tanggal">
                    <span class="input-calendar-icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </span>
                </div>
                <span class="form-helper-text">Harus setelah tanggal tutup pendaftaran.</span>
            </div>

            {{-- 4. Status Awal (Segmented Pills) --}}
            <div class="form-row-group" style="margin-top: 0.5rem; margin-bottom: 0.5rem;">
                <label style="font-size: 0.8125rem; font-weight: 700; color: #1E293B; margin-bottom: 0.4rem;">Status awal</label>
                <input type="hidden" name="status_awal" id="statusAwalInput" value="draft">
                <div class="status-pills-group">
                    <button type="button" 
                            id="btnStatusDraft"
                            data-action="set-status-awal" data-status="draft" 
                            class="btn-status-pill active">
                        <span id="checkIconDraft" style="display: inline-flex; align-items: center;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </span>
                        <span>Draft</span>
                    </button>
                    <button type="button" 
                            id="btnStatusDibuka"
                            data-action="set-status-awal" data-status="dibuka" 
                            class="btn-status-pill">
                        <span id="checkIconDibuka" style="display: none; align-items: center;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </span>
                        <span>Dibuka</span>
                    </button>
                </div>
            </div>

            {{-- 5. Alert Callout Box --}}
            <div class="modal-callout-info">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0;">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                <span>Setelah bulan magang disimpan, atur kuota tiap bidang lewat tombol Tambah kuota.</span>
            </div>

            {{-- 6. Footer Buttons --}}
            <div class="modal-footer-btns" style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #E2E8F0;">
                <button type="button" data-action="close-buat-bulan" class="btn-outline-action" style="padding: 0.6rem 1.35rem; font-weight: 600;">
                    Batal
                </button>
                <button type="submit" class="btn-primary-action" style="padding: 0.6rem 1.4rem; font-weight: 700;">
                    Simpan bulan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- =========================================================================
     MODAL 3: TAMBAH KUOTA JURUSAN
     ========================================================================= --}}
@if($selectedBulan)
<div id="modalTambahKuota" class="modal-overlay modal-tambah-kuota">
    <div class="modal-card" style="max-width: 540px; padding: 1.75rem; border-radius: 14px;">
        <div class="modal-header-row" style="align-items: flex-start; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid #E2E8F0;">
            <div>
                <h3 class="modal-title" style="font-size: 1.25rem; font-weight: 800; color: #0F172A;">Tambah kuota <span class="sr-only">Alokasikan Kuota Jurusan</span></h3>
                <p class="modal-header-desc" style="font-size: 0.8125rem; color: #64748B; margin: 0.25rem 0 0 0;">Atur kuota, jurusan, jenjang, dan durasi untuk satu bidang.</p>
            </div>
            <button type="button" data-action="close-tambah-kuota" class="modal-btn-close-box" aria-label="Tutup">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.kuota.store') }}" id="formTambahKuota">
            @csrf
            <input type="hidden" name="bulan_magang_id" value="{{ $selectedBulan->id }}">

            {{-- 1. Baris 1: Bulan (Read-Only) & Bidang (Dropdown) --}}
            <div style="display: grid; grid-template-columns: 130px 1fr; gap: 0.875rem; width: 100%;">
                <div class="form-row-group">
                    <label style="font-size: 0.8125rem; font-weight: 700; color: #1E293B;">Bulan</label>
                    <input type="text" 
                           value="{{ $selectedBulan->nama_bulan ?? ($selectedBulan->bulan_mulai?->translatedFormat('F Y') ?? 'Gelombang 1') }}" 
                           disabled 
                           style="background: #F8FAFC; color: #475569; font-weight: 600; cursor: not-allowed; text-align: center;">
                </div>
                <div class="form-row-group">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <label style="font-size: 0.8125rem; font-weight: 700; color: #1E293B;">Bidang <span style="color: #DC2626;">*</span></label>
                        <span id="infoBidangKapasitasMini" style="font-size: 0.725rem; color: #0641CC; font-weight: 600;"></span>
                    </div>
                    <select name="bidang_id" id="tambahKuotaBidangSelect"  required style="width: 100%; height: 42px; border: 1px solid #CBD5E1; border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; background: #FFFFFF; font-weight: 600; color: #0F172A;">
                        @foreach($bidangList as $b)
                            <option value="{{ $b['id'] }}" {{ $loop->first ? 'selected' : '' }}>
                                {{ $b['nama'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- 2. Baris 2: Jurusan yang Dibutuhkan (Chips) --}}
            <div class="form-row-group" style="margin-top: 0.25rem;">
                <label style="font-size: 0.8125rem; font-weight: 700; color: #1E293B; margin-bottom: 0.35rem;">Jurusan yang dibutuhkan</label>
                <div class="jurusan-chips-wrap" id="jurusanChipsContainer" style="display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center;">
                    {{-- Dinamis diisi via JavaScript --}}
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.5rem;">
                    <button type="button" class="btn-add-jurusan-chip" data-action="toggle-extra-jurusan">
                        <span>+ Jurusan</span>
                    </button>
                    <select id="extraJurusanSelect"  style="display: none; height: 36px; font-size: 0.8125rem; padding: 0 0.75rem; border-radius: 8px; border: 1px solid #CBD5E1; background: #FFFFFF; color: #1E293B;">
                        <option value="">-- Pilih Jurusan Lain --</option>
                        @foreach($jurusanList as $jur)
                            <option value="{{ $jur->id }}" data-nama="{{ $jur->nama_jurusan }}">
                                {{ $jur->nama_jurusan }} ({{ strtoupper($jur->kategori) }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div id="hiddenJurusanInputs"></div>
            </div>

            {{-- 3. Baris 3: Jenjang --}}
            <div class="form-row-group">
                <label style="font-size: 0.8125rem; font-weight: 700; color: #1E293B; margin-bottom: 0.35rem;">Jenjang</label>
                <div class="jenjang-pills-wrap">
                    <button type="button" id="pillJenjangSMK" data-action="toggle-jenjang" data-jenjang="SMK" class="btn-jenjang-pill">
                        <span class="jenjang-check" style="display: none;">✓ </span>SMK
                    </button>
                    <button type="button" id="pillJenjangD3" data-action="toggle-jenjang" data-jenjang="D3" class="btn-jenjang-pill">
                        <span class="jenjang-check" style="display: none;">✓ </span>D3
                    </button>
                    <button type="button" id="pillJenjangS1" data-action="toggle-jenjang" data-jenjang="S1" class="btn-jenjang-pill active">
                        <span class="jenjang-check" style="display: inline;">✓ </span>S1
                    </button>
                </div>
                <input type="hidden" name="jenjang[]" id="inputJenjangSMK" value="" disabled>
                <input type="hidden" name="jenjang[]" id="inputJenjangD3" value="" disabled>
                <input type="hidden" name="jenjang[]" id="inputJenjangS1" value="S1">
            </div>

            {{-- 4. Baris 4: Jumlah Kuota, Durasi Minimum, Durasi Maksimum (3 Kolom) --}}
            <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 0.75rem; width: 100%;">
                <div class="form-row-group">
                    <label style="font-size: 0.8125rem; font-weight: 700; color: #1E293B;">Jumlah kuota <span style="color: #DC2626;">*</span></label>
                    <input type="number" 
                           name="jumlah" 
                           id="tambahKuotaJumlahInput" 
                           min="1" 
                           max="100" 
                           value="3" 
                            
                           required>
                </div>
                <div class="form-row-group">
                    <label style="font-size: 0.8125rem; font-weight: 700; color: #1E293B;">Durasi minimum</label>
                    <select name="durasi_min">
                        <option value="1 bulan">1 bulan</option>
                        <option value="2 bulan">2 bulan</option>
                        <option value="3 bulan" selected>3 bulan</option>
                        <option value="4 bulan">4 bulan</option>
                        <option value="5 bulan">5 bulan</option>
                        <option value="6 bulan">6 bulan</option>
                    </select>
                </div>
                <div class="form-row-group">
                    <label style="font-size: 0.8125rem; font-weight: 700; color: #1E293B;">Durasi maksimum</label>
                    <select name="durasi_max">
                        <option value="1 bulan">1 bulan</option>
                        <option value="2 bulan">2 bulan</option>
                        <option value="3 bulan">3 bulan</option>
                        <option value="4 bulan">4 bulan</option>
                        <option value="5 bulan">5 bulan</option>
                        <option value="6 bulan" selected>6 bulan</option>
                    </select>
                </div>
            </div>

            {{-- 5. Baris 5: Warning Box Peringatan Kapasitas Pembimbing --}}
            <div id="boxWarningKapasitas" class="box-capacity-warning" style="display: none;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 2px;">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <div>
                    <strong>Melebihi kapasitas pembimbing</strong>
                    <span id="textWarningKapasitasDetail" style="display: block; font-size: 0.785rem; color: #B91C1C; margin-top: 0.2rem; line-height: 1.4;">
                        Kapasitas Persandian 12 peserta (2 Pembimbing x 6) dan kuota terpasang sudah 6, sisa kapasitas 6. Kurangi jumlah kuota yang ditambahkan atau tambah Pembimbing aktif di bidang ini.
                    </span>
                </div>
            </div>

            {{-- 6. Footer Buttons --}}
            <div class="modal-footer-btns" style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #E2E8F0;">
                <button type="button" data-action="close-tambah-kuota" class="btn-outline-action" style="padding: 0.6rem 1.35rem; font-weight: 600;">
                    Batal
                </button>
                <button type="submit" id="btnSubmitTambahKuota" class="btn-primary-action" style="padding: 0.6rem 1.4rem; font-weight: 700;">
                    Simpan kuota
                </button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- =========================================================================
     MODAL 4: UBAH KUOTA PER BIDANG
     ========================================================================= --}}
<div id="modalEditBidang" class="modal-overlay modal-edit-kuota">
    <div class="modal-card" style="max-width: 580px; padding: 1.75rem; border-radius: 14px;">
        <div class="modal-header-row" style="align-items: flex-start; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid #E2E8F0;">
            <div>
                <h3 class="modal-title" id="editBidangTitle" style="font-size: 1.25rem; font-weight: 800; color: #0F172A;">Ubah Kuota Bidang</h3>
                <p class="modal-header-desc" style="font-size: 0.8125rem; color: #64748B; margin: 0.25rem 0 0 0;">Atur status buka/tutup dan kapasitas peserta untuk bidang ini.</p>
            </div>
            <button type="button" data-action="close-edit-bidang" class="modal-btn-close-box" aria-label="Tutup">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <form id="formEditBidang" method="POST" action="">
            @csrf
            @method('PUT')
            <input type="hidden" name="bulan_magang_id" value="{{ $selectedBulan?->id }}">
            <input type="hidden" name="status_bidang" id="inputStatusBidang" value="buka">

            {{-- 1. Status Kuota Penempatan Bidang (Buka / Tutup) --}}
            <div class="form-row-group" style="margin-bottom: 1.15rem;">
                <label style="font-size: 0.8125rem; font-weight: 700; color: #1E293B; margin-bottom: 0.4rem;">Status Penempatan Bidang</label>
                <div class="status-pills-group" style="display: flex; gap: 0.5rem;">
                    <button type="button" 
                            id="btnBidangBuka" 
                            data-action="set-status-bidang" data-status="buka" 
                            class="btn-status-pill active" 
                            style="flex: 1; height: 38px; display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem; border-radius: 8px; font-weight: 700; font-size: 0.8125rem; cursor: pointer;">
                        <span id="checkIconBidangBuka" style="display: inline-flex; align-items: center;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </span>
                        <span>Dibuka (Ada Kuota)</span>
                    </button>
                    <button type="button" 
                            id="btnBidangTutup" 
                            data-action="set-status-bidang" data-status="tutup" 
                            class="btn-status-pill" 
                            style="flex: 1; height: 38px; display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem; border-radius: 8px; font-weight: 700; font-size: 0.8125rem; cursor: pointer;">
                        <span id="checkIconBidangTutup" style="display: none; align-items: center;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </span>
                        <span>Ditutup (Tanpa Kuota)</span>
                    </button>
                </div>
            </div>

            {{-- 2. Ringkasan Total Kapasitas Masuk Bidang --}}
            <div id="boxSummaryKapasitasBidang" style="display: flex; justify-content: space-between; align-items: center; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; padding: 0.85rem 1rem; margin-bottom: 1rem;">
                <div>
                    <div style="font-size: 0.75rem; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.04em;">Total Kapasitas Masuk</div>
                    <div style="font-size: 1.2rem; font-weight: 800; color: #0F172A; margin-top: 0.15rem;">
                        <span id="totalKapasitasBidangDisplay">0</span> <span style="font-size: 0.8125rem; font-weight: 600; color: #64748B;">peserta</span>
                    </div>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 0.725rem; color: #64748B; font-weight: 600;">Batas Kapasitas Pembimbing</div>
                    <div style="font-size: 0.875rem; font-weight: 700; color: #334155; margin-top: 0.15rem;">
                        Maks. <span id="maxKapasitasBimbinganDisplay">18</span> peserta
                    </div>
                    <div style="font-size: 0.7rem; color: #94A3B8;">(<span id="pembimbingCountDisplay">3</span> Pembimbing aktif)</div>
                </div>
            </div>

            {{-- 3. Daftar Kejuruan yang Dibutuhkan --}}
            <div id="wrapJurusanAllocSection">
                <label style="font-size: 0.8125rem; font-weight: 700; color: #1E293B; margin-bottom: 0.5rem; display: block;">Alokasi Kuota per Kejuruan</label>
                <div id="jurusanAllocList" style="display: flex; flex-direction: column; gap: 0.75rem; max-height: 280px; overflow-y: auto; padding-right: 0.25rem;">
                    {{-- Dinamis diisi via JavaScript --}}
                </div>
            </div>

            {{-- 4. Keterangan saat bidang ditutup --}}
            <div id="boxBidangTutupNotice" style="display: none; background: #FFFBEB; border: 1px solid #FCD34D; border-radius: 8px; padding: 0.85rem 1rem; color: #92400E; font-size: 0.8125rem; line-height: 1.45;">
                <div style="font-weight: 700; margin-bottom: 0.2rem;">Penempatan Bidang Ditutup:</div>
                Jika disimpan dalam status ditutup, kuota penempatan untuk bidang ini akan dinonaktifkan (kapasitas 0 peserta) pada bulan magang ini.
            </div>

            {{-- 5. Warning Box jika kuota melebihi kapasitas bimbingan --}}
            <div id="boxWarningKapasitasBidang" class="box-capacity-warning" style="display: none; margin-top: 0.75rem;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <div>
                    <strong>Melebihi kapasitas bimbingan!</strong> Total kuota (<span id="warnTotalKapasitas"></span> peserta) melebihi batas bimbingan pembimbing (<span id="warnMaxKapasitas"></span> peserta). Kurangi alokasi atau tambah pembimbing aktif.
                </div>
            </div>

            <div class="modal-footer-btns" style="margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid #E2E8F0;">
                <button type="button" data-action="close-edit-bidang" class="btn-outline-action">Batal</button>
                <button type="submit" id="btnSubmitEditBidang" class="btn-primary-action">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script type="application/json" id="magkom-bidang-master-data">@json($bidangJsonData ?? [])</script>
@vite(['resources/js/pages/admin/kuota-index.js'])
@endsection
