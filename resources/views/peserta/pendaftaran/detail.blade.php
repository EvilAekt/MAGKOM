@extends('layouts.peserta')

@section('title', 'Detail & Berkas Pendaftaran Magang')
@section('page_title', 'Detail & Berkas Pendaftaran')

@push('styles')
    @vite(['resources/css/pages/peserta/pemberkasan.css'])
@endpush

@section('content')
<div class="pemberkasan-page-wrapper">

    @php
        $val = $pendaftaran->status->value ?? (string)$pendaftaran->status;
        $bisaBatal = in_array($val, ['berkas_belum_lengkap', 'diajukan', 'perlu_perbaikan', 'waiting_list']);
        $bisaUnggah = in_array($val, ['berkas_belum_lengkap', 'perlu_perbaikan']);

        $isKelompok = ($pendaftaran->tipe === 'kelompok' || $pendaftaran->kelompok_magang_id !== null);
        $jenisKolektif = $jenisDokumenList->whereIn('kode', ['surat_pengantar', 'proposal', 'surat_ortu']);
        $jenisIndividu = $jenisDokumenList->whereIn('kode', ['cv', 'transkrip', 'kartu_pelajar']);

        if (! $isKelompok) {
            $totalBerkas = $jenisDokumenList->count();
            $uploadedBerkas = $jenisDokumenList->filter(fn($j) => $dokumenTerunggah->has($j->id))->count();

            $totalWajib = $jenisDokumenList->where('wajib', true)->count();
            $uploadedWajib = $jenisDokumenList->where('wajib', true)->filter(fn($j) => $dokumenTerunggah->has($j->id))->count();
        } else {
            // Berkas kolektif
            $totalBerkas = $jenisKolektif->count();
            $uploadedBerkas = $jenisKolektif->filter(fn($j) => $dokumenTerunggah->has($j->id))->count();

            $totalWajib = $jenisKolektif->where('wajib', true)->count();
            $uploadedWajib = $jenisKolektif->where('wajib', true)->filter(fn($j) => $dokumenTerunggah->has($j->id))->count();

            // Berkas individu Ketua
            $totalBerkas += $jenisIndividu->count();
            $uploadedBerkas += $jenisIndividu->filter(fn($j) => $dokumenTerunggah->has($j->id))->count();

            $totalWajib += $jenisIndividu->where('wajib', true)->count();
            $uploadedWajib += $jenisIndividu->where('wajib', true)->filter(fn($j) => $dokumenTerunggah->has($j->id))->count();

            // Berkas individu setiap Anggota
            $anggotaList = $pendaftaran->kelompokMagang?->anggota ?? collect();
            foreach ($anggotaList as $anggota) {
                $totalBerkas += $jenisIndividu->count();
                $totalWajib += $jenisIndividu->where('wajib', true)->count();

                $anggotaDocs = ($dokumenAnggotaGroup ?? collect())->get($anggota->id) ?? collect();
                $uploadedBerkas += $jenisIndividu->filter(fn($j) => $anggotaDocs->contains('jenis_dokumen_id', $j->id))->count();
                $uploadedWajib += $jenisIndividu->where('wajib', true)->filter(fn($j) => $anggotaDocs->contains('jenis_dokumen_id', $j->id))->count();
            }
        }

        $persen = $totalBerkas > 0 ? round(($uploadedBerkas / $totalBerkas) * 100) : 100;
        $semuaWajibLengkap = ($uploadedWajib >= $totalWajib);
        $semuaBerkasLengkap = ($uploadedBerkas >= $totalBerkas);
    @endphp

    {{-- 1. Top Navigation Bar --}}
    <div class="pemberkasan-top-bar">
        <a href="{{ route('peserta.pendaftaran.index') }}" class="btn-back-nav">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <span>Kembali ke Riwayat</span>
        </a>
    </div>

    {{-- 2. Hero Banner (Navy Gradient + Sleman Gold Accent) --}}
    <div class="pemberkasan-hero-card">
        <div class="hero-main-flex">
            <div class="hero-left-content">
                <div class="hero-reg-pill">
                    #REG-{{ str_pad((string)$pendaftaran->id, 5, '0', STR_PAD_LEFT) }}
                </div>
                <h1 class="hero-title-main">
                    Pemberkasan {{ $pendaftaran->bulanMagang?->bulan_mulai?->translatedFormat('F Y') }}
                </h1>
                <p class="hero-subtitle">
                    Pantau tahapan pendaftaran dan kelengkapan berkas magang kamu.
                </p>
            </div>

            <div class="hero-right-content">
                <div class="hero-meta-grid">
                    <div class="hero-meta-box">
                        <span class="hero-meta-label">JURUSAN</span>
                        <span class="hero-meta-value">{{ $pendaftaran->kuota?->jurusan?->nama_jurusan }}</span>
                    </div>
                    <div class="hero-meta-box">
                        <span class="hero-meta-label">DURASI</span>
                        <span class="hero-meta-value">{{ $pendaftaran->durasi_bulan }} Bulan</span>
                    </div>
                    <div class="hero-meta-box">
                        <span class="hero-meta-label">JALUR</span>
                        <span class="hero-meta-value">
                            @if($isKelompok)
                                Kelompok: {{ $pendaftaran->kelompokMagang?->nama_kelompok ?? 'Tim' }}
                            @else
                                Mandiri
                            @endif
                        </span>
                    </div>
                    <div class="hero-meta-box {{ $val === 'diterima' ? 'status-box-gold' : ($val === 'diajukan' ? 'status-box-blue' : ($val === 'perlu_perbaikan' ? 'status-box-amber' : ($val === 'ditolak' ? 'status-box-danger' : 'status-box-default'))) }}">
                        <span class="hero-meta-label">STATUS</span>
                        <span class="hero-meta-value">{{ $pendaftaran->status->label() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. Stepper Tracker --}}
    <div class="pemberkasan-stepper-card">
        <div class="stepper-timeline-modern">
            <div class="stepper-step-modern {{ $val === 'berkas_belum_lengkap' ? 'active' : 'completed' }}">
                <div class="stepper-circle-modern">
                    @if($val === 'berkas_belum_lengkap')
                        1
                    @else
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    @endif
                </div>
                <div class="stepper-phase-tag">Tahap 1</div>
                <div class="stepper-label-text">
                    <span class="sr-only">1. Berkas Persyaratan</span>
                    <span aria-hidden="true">Berkas Persyaratan</span>
                </div>
            </div>

            <div class="stepper-line {{ !in_array($val, ['berkas_belum_lengkap']) ? 'completed' : '' }}" aria-hidden="true"></div>

            <div class="stepper-step-modern {{ in_array($val, ['diajukan', 'waiting_list']) ? 'active' : (in_array($val, ['perlu_perbaikan', 'diterima', 'ditolak']) ? 'completed' : '') }}">
                <div class="stepper-circle-modern">
                    @if(in_array($val, ['perlu_perbaikan', 'diterima', 'ditolak']))
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    @else
                        2
                    @endif
                </div>
                <div class="stepper-phase-tag">Tahap 2</div>
                <div class="stepper-label-text">
                    <span class="sr-only">2. Verifikasi Berkas</span>
                    <span aria-hidden="true">Verifikasi Berkas</span>
                </div>
            </div>

            <div class="stepper-line {{ in_array($val, ['perlu_perbaikan', 'diterima', 'ditolak']) ? 'completed' : '' }}" aria-hidden="true"></div>

            <div class="stepper-step-modern {{ $val === 'perlu_perbaikan' ? 'warning' : ($val === 'diterima' ? 'accepted' : ($val === 'ditolak' ? 'danger' : '')) }}">
                <div class="stepper-circle-modern">
                    @if($val === 'diterima')
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    @elseif($val === 'perlu_perbaikan')
                        !
                    @elseif($val === 'ditolak')
                        ✕
                    @else
                        3
                    @endif
                </div>
                <div class="stepper-phase-tag">Tahap 3</div>
                <div class="stepper-label-text">
                    <span class="sr-only">3. Keputusan</span>
                    <span aria-hidden="true">
                        @if($val === 'perlu_perbaikan') Perbaikan Berkas
                        @elseif($val === 'diterima') Diterima
                        @elseif($val === 'ditolak') Ditolak
                        @else Keputusan @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Alert Catatan Revisi Admin --}}
    @if($val === 'perlu_perbaikan')
        <div class="pemberkasan-revision-alert">
            <strong class="revision-title">
                Catatan Perbaikan Verifikator:
            </strong>
            <div class="revision-body">
                "{{ $pendaftaran->catatan_admin ?? 'Harap perbarui dokumen yang diminta.' }}"
            </div>
        </div>
    @endif

    {{-- Alert Kelulusan Diterima & Unduh Surat --}}
    @if($val === 'diterima' && $pendaftaran->penempatan)
        <div class="pemberkasan-approved-alert">
            <div class="approved-alert-left">
                <div class="approved-icon-box" aria-hidden="true">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>
                <div class="approved-text-col">
                    <strong class="approved-title">
                        Selamat! Permohonan Magang Anda Telah Disetujui
                    </strong>
                    <span class="approved-desc">
                        Surat Keterangan Diterima resmi siap diunduh.
                    </span>
                </div>
            </div>
            <div class="approved-alert-actions">
                <button type="button" class="btn-approved-preview" onclick="openDocPreview('{{ route('peserta.penempatan.surat-diterima.pratinjau', $pendaftaran->penempatan) }}', '{{ route('peserta.penempatan.surat-diterima', $pendaftaran->penempatan) }}', 'Surat Diterima', 'application/pdf')">
                    Lihat Surat Diterima
                </button>
                <a href="{{ route('peserta.penempatan.surat-diterima', $pendaftaran->penempatan) }}" class="btn-approved-download">
                    Unduh Surat Diterima (PDF)
                </a>
            </div>
        </div>
    @endif

    {{-- 4. Daftar Dokumen Persyaratan --}}
    <div class="pemberkasan-main-card">
        <div class="pemberkasan-header-row">
            <div class="pemberkasan-title-group">
                <div class="pemberkasan-title-flex">
                    <div class="pemberkasan-title-icon-box" aria-hidden="true">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                        </svg>
                    </div>
                    <div>
                        <div class="pemberkasan-heading-wrap">
                            <span class="pemberkasan-accent-line" aria-hidden="true"></span>
                            <h3>Berkas Persyaratan Magang</h3>
                        </div>
                        <div class="pemberkasan-guide-chips">
                            <span class="pemberkasan-chip-info">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                <span>Format: PDF, JPG, PNG</span>
                            </span>
                            <span class="pemberkasan-chip-info">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>
                                <span>Maks. {{ $maksMb }} MB per dokumen</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pemberkasan-badge-wrap">
                <span class="pemberkasan-progress-badge {{ $uploadedBerkas >= $totalBerkas ? 'complete' : '' }}">
                    <span class="pemberkasan-dot-status" aria-hidden="true"></span>
                    <span>{{ $uploadedBerkas }}/{{ $totalBerkas }} Berkas</span>
                </span>
            </div>
        </div>

        <div class="pemberkasan-header-divider" aria-hidden="true"></div>

        {{-- Daftar Berkas --}}
        <div>
            @if(! $isKelompok)
                {{-- Mode Mandiri --}}
                @foreach($jenisDokumenList as $jenis)
                    @include('peserta.pendaftaran.partials._doc_card_compact', [
                        'jenis' => $jenis,
                        'terunggah' => $dokumenTerunggah->get($jenis->id),
                        'elementKey' => (string)$jenis->id,
                        'anggotaId' => null,
                        'pendaftaran' => $pendaftaran,
                        'bisaUnggah' => $bisaUnggah,
                        'maksMb' => $maksMb,
                    ])
                @endforeach
            @else
                {{-- Mode Kelompok --}}

                {{-- A. Berkas Tim --}}
                <div style="margin-bottom: 20px;">
                    <div class="pemberkasan-section-header">
                        <h4 class="pemberkasan-section-title">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            <span>Berkas Tim</span>
                        </h4>
                        <span class="pemberkasan-section-badge">1 per Tim</span>
                    </div>

                    @foreach($jenisKolektif as $jenis)
                        @include('peserta.pendaftaran.partials._doc_card_compact', [
                            'jenis' => $jenis,
                            'terunggah' => $dokumenTerunggah->get($jenis->id),
                            'elementKey' => (string)$jenis->id,
                            'anggotaId' => null,
                            'pendaftaran' => $pendaftaran,
                            'bisaUnggah' => $bisaUnggah,
                            'maksMb' => $maksMb,
                        ])
                    @endforeach
                </div>

                {{-- B. Berkas Anggota Tim --}}
                <div>
                    <div class="pemberkasan-section-header">
                        <h4 class="pemberkasan-section-title">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                            <span>Berkas Personil Tim</span>
                        </h4>
                        <span class="pemberkasan-section-badge">CV & Transkrip</span>
                    </div>

                    {{-- 1. Kartu Ketua --}}
                    @php
                        $ketuaIndividuCount = $jenisIndividu->count();
                        $ketuaIndividuUploaded = $jenisIndividu->filter(fn($j) => $dokumenTerunggah->has($j->id))->count();
                        $ketuaWajibCount = $jenisIndividu->where('wajib', true)->count();
                        $ketuaWajibUploaded = $jenisIndividu->where('wajib', true)->filter(fn($j) => $dokumenTerunggah->has($j->id))->count();
                        $ketuaLengkap = ($ketuaWajibUploaded >= $ketuaWajibCount);
                    @endphp
                    <div class="team-member-bento">
                        <div class="team-member-header">
                            <div class="team-member-profile">
                                <div class="team-member-avatar ketua">
                                    {{ strtoupper(substr($pendaftaran->user->nama ?? 'K', 0, 1)) }}
                                </div>
                                <div class="team-member-name">
                                    <span>{{ $pendaftaran->user->nama }}</span>
                                    <span class="team-role-tag ketua">Ketua (Anda)</span>
                                </div>
                            </div>
                            <div>
                                <span class="team-status-pill {{ $ketuaIndividuUploaded >= $ketuaIndividuCount ? 'complete' : 'pending' }}">
                                    {{ $ketuaIndividuUploaded }}/{{ $ketuaIndividuCount }} Berkas
                                </span>
                            </div>
                        </div>
                        <div class="team-member-body">
                            @foreach($jenisIndividu as $jenis)
                                @include('peserta.pendaftaran.partials._doc_card_compact', [
                                    'jenis' => $jenis,
                                    'terunggah' => $dokumenTerunggah->get($jenis->id),
                                    'elementKey' => (string)$jenis->id,
                                    'anggotaId' => null,
                                    'pendaftaran' => $pendaftaran,
                                    'bisaUnggah' => $bisaUnggah,
                                    'maksMb' => $maksMb,
                                ])
                            @endforeach
                        </div>
                    </div>

                    {{-- 2. Kartu Anggota --}}
                    @foreach($pendaftaran->kelompokMagang?->anggota ?? [] as $idx => $anggota)
                        @php
                            $aDocsKeyed = (($dokumenAnggotaGroup ?? collect())->get($anggota->id) ?? collect())->keyBy('jenis_dokumen_id');
                            $aTotal = $jenisIndividu->count();
                            $aUploaded = $jenisIndividu->filter(fn($j) => $aDocsKeyed->has($j->id))->count();
                            $aWajibCount = $jenisIndividu->where('wajib', true)->count();
                            $aWajibUploaded = $jenisIndividu->where('wajib', true)->filter(fn($j) => $aDocsKeyed->has($j->id))->count();
                            $aLengkap = ($aWajibUploaded >= $aWajibCount);
                        @endphp
                        <div class="team-member-bento">
                            <div class="team-member-header">
                                <div class="team-member-profile">
                                    <div class="team-member-avatar anggota">
                                        {{ strtoupper(substr($anggota->nama ?? 'A', 0, 1)) }}
                                    </div>
                                    <div class="team-member-name">
                                        <span>{{ $anggota->nama }}</span>
                                        <span class="team-role-tag anggota">Anggota {{ $idx + 1 }}</span>
                                    </div>
                                </div>
                                <div>
                                    <span class="team-status-pill {{ $aUploaded >= $aTotal ? 'complete' : 'pending' }}">
                                        {{ $aUploaded }}/{{ $aTotal }} Berkas
                                    </span>
                                </div>
                            </div>
                            <div class="team-member-body">
                                @foreach($jenisIndividu as $jenis)
                                    @include('peserta.pendaftaran.partials._doc_card_compact', [
                                        'jenis' => $jenis,
                                        'terunggah' => $aDocsKeyed->get($jenis->id),
                                        'elementKey' => $jenis->id . '_' . $anggota->id,
                                        'anggotaId' => $anggota->id,
                                        'pendaftaran' => $pendaftaran,
                                        'bisaUnggah' => $bisaUnggah,
                                        'maksMb' => $maksMb,
                                    ])
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- 5. Submission Bar (1 Baris Ringkas & Bersih) --}}
        @if($bisaUnggah)
            <div class="pemberkasan-submit-card {{ $semuaWajibLengkap ? 'ready' : 'pending' }}">
                <div class="submit-card-left">
                    <div class="submit-icon-circle {{ $semuaWajibLengkap ? 'ready' : 'pending' }}" aria-hidden="true">
                        @if($semuaWajibLengkap)
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        @else
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        @endif
                    </div>
                    <div>
                        @if($semuaWajibLengkap)
                            <span class="submit-title-text ready">
                                @if($uploadedBerkas >= $totalBerkas)
                                    Seluruh berkas persyaratan telah lengkap
                                @else
                                    Berkas wajib telah lengkap ({{ $uploadedBerkas }}/{{ $totalBerkas }} berkas terunggah)
                                @endif
                            </span>
                        @else
                            <span class="submit-title-text pending">{{ $totalWajib - $uploadedWajib }} berkas wajib belum diunggah</span>
                        @endif
                    </div>
                </div>

                <form action="{{ route('peserta.pendaftaran.ajukan', $pendaftaran) }}" method="POST" id="formAjukanPendaftaran" onsubmit="handleAjukanSubmit(this)">
                    @csrf
                    <button type="submit" class="btn-submit-final" id="btnAjukanPendaftaran" {{ !$semuaWajibLengkap ? 'disabled' : '' }}>
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        <span>Ajukan Pendaftaran</span>
                    </button>
                </form>
            </div>
        @else
            <div class="pemberkasan-locked-notice">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="locked-icon" aria-hidden="true">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0110 0v4"></path>
                </svg>
                <span>Berkas pendaftaran saat ini terkunci karena sedang diproses oleh admin.</span>
            </div>
        @endif
    </div>
</div>


{{-- Modal In-App Pratinjau Dokumen --}}
<div class="modal" id="docPreviewModal" onclick="if(event.target === this) closeDocPreview()" role="dialog" aria-modal="true" aria-labelledby="previewModalTitle">
    <div class="modal-box-large">
        <div class="modal-header" style="margin-bottom: 0; padding-bottom: 12px;">
            <div style="display: flex; align-items: center; gap: 10px; overflow: hidden; padding-right: 10px;">
                <div id="previewModalIcon" style="width: 34px; height: 34px; border-radius: 8px; background: #EFF6FF; color: #02275D; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                </div>
                <div style="overflow: hidden;">
                    <h3 class="modal-title" id="previewModalTitle" style="font-size: 0.95rem; margin: 0; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
                        Pratinjau Dokumen
                    </h3>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 8px; flex-shrink: 0;">
                <a id="previewOpenNewTabBtn" href="#" target="_blank" rel="noopener noreferrer" class="btn btn-secondary" style="height: 32px; padding: 0 10px; font-size: 0.75rem;">
                    Tab Baru
                </a>
                <a id="previewDownloadBtn" href="#" class="btn btn-secondary" style="height: 32px; padding: 0 10px; font-size: 0.75rem;">
                    Unduh
                </a>
                <button type="button" class="modal-close" onclick="closeDocPreview()">&times;</button>
            </div>
        </div>

        <div class="modal-body-preview">
            <div id="previewLoading" class="preview-loading-overlay">
                <div class="preview-loading-spinner"></div>
                <div style="font-size: 0.84rem; font-weight: 700;">Memuat Pratinjau...</div>
            </div>

            <iframe id="previewIframe" src="about:blank" style="width: 100%; height: 75vh; border: none; display: none;"></iframe>

            <div id="previewImageWrapper" style="display: none; padding: 20px; width: 100%; height: 100%; text-align: center; overflow: auto; justify-content: center; align-items: center;">
                <img id="previewImage" alt="Pratinjau Berkas" style="max-width: 100%; max-height: 70vh; border-radius: 8px; object-fit: contain;">
            </div>

            <div id="previewError" style="display: none; flex-direction: column; align-items: center; justify-content: center; padding: 24px; text-align: center;">
                <div style="font-size: 0.875rem; font-weight: 700; margin-bottom: 6px;">Tidak Dapat Memuat Pratinjau</div>
                <a id="previewFallbackDownloadBtn" href="#" class="btn btn-primary" style="height: 34px; padding: 0 14px; font-size: 0.775rem;">
                    Unduh Berkas
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let previewTimer = null;

    function openDocPreview(previewUrl, downloadUrl, title, mime) {
        const modal = document.getElementById('docPreviewModal');
        const modalTitle = document.getElementById('previewModalTitle');
        const openNewTabBtn = document.getElementById('previewOpenNewTabBtn');
        const downloadBtn = document.getElementById('previewDownloadBtn');
        const fallbackBtn = document.getElementById('previewFallbackDownloadBtn');
        const loading = document.getElementById('previewLoading');
        const iframe = document.getElementById('previewIframe');
        const imgWrapper = document.getElementById('previewImageWrapper');
        const img = document.getElementById('previewImage');
        const errBox = document.getElementById('previewError');

        if (!modal) return;

        if (previewTimer) {
            clearTimeout(previewTimer);
            previewTimer = null;
        }

        loading.style.display = 'flex';
        iframe.style.display = 'none';
        iframe.onload = null;
        iframe.src = 'about:blank';
        imgWrapper.style.display = 'none';
        if (img) {
            img.onload = null;
            img.onerror = null;
            img.removeAttribute('src');
        }
        errBox.style.display = 'none';

        modalTitle.textContent = title || 'Pratinjau Dokumen';
        openNewTabBtn.href = previewUrl;
        downloadBtn.href = downloadUrl;
        if (fallbackBtn) fallbackBtn.href = downloadUrl;

        const isPdf = (mime && mime.toLowerCase().includes('pdf')) || (!mime && previewUrl.toLowerCase().includes('pdf'));

        if (isPdf) {
            const finalPdfUrl = previewUrl.includes('#') ? previewUrl : (previewUrl + '#toolbar=0&navpanes=0');
            iframe.onload = function() {
                handlePreviewLoaded();
            };
            iframe.style.display = 'block';
            iframe.src = finalPdfUrl;

            previewTimer = setTimeout(function() {
                handlePreviewLoaded();
            }, 600);
        } else {
            img.onload = function() {
                handlePreviewLoaded();
            };
            img.onerror = function() {
                handlePreviewError();
            };
            img.src = previewUrl;
            imgWrapper.style.display = 'flex';

            previewTimer = setTimeout(function() {
                handlePreviewLoaded();
            }, 700);
        }

        modal.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function handlePreviewLoaded() {
        if (previewTimer) {
            clearTimeout(previewTimer);
            previewTimer = null;
        }
        const loading = document.getElementById('previewLoading');
        if (loading) loading.style.display = 'none';
    }

    function handlePreviewError() {
        if (previewTimer) {
            clearTimeout(previewTimer);
            previewTimer = null;
        }
        const loading = document.getElementById('previewLoading');
        const iframe = document.getElementById('previewIframe');
        const imgWrapper = document.getElementById('previewImageWrapper');
        const errBox = document.getElementById('previewError');

        if (loading) loading.style.display = 'none';
        if (iframe) iframe.style.display = 'none';
        if (imgWrapper) imgWrapper.style.display = 'none';
        if (errBox) errBox.style.display = 'flex';
    }

    function closeDocPreview() {
        if (previewTimer) {
            clearTimeout(previewTimer);
            previewTimer = null;
        }
        const modal = document.getElementById('docPreviewModal');
        const iframe = document.getElementById('previewIframe');
        const img = document.getElementById('previewImage');

        if (modal) {
            modal.classList.remove('open');
            document.body.style.overflow = '';
        }
        if (iframe) {
            iframe.onload = null;
            iframe.src = 'about:blank';
        }
        if (img) {
            img.onload = null;
            img.onerror = null;
            img.removeAttribute('src');
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' || e.key === 'Esc') {
            closeDocPreview();
        }
    });

    function toggleDropzone(id) {
        const wrapper = document.getElementById('dropzone_wrapper_' + id);
        if (wrapper) {
            wrapper.style.display = (wrapper.style.display === 'none' || wrapper.style.display === '') ? 'flex' : 'none';
        }
    }

    function handleDragOver(e, element) {
        e.preventDefault();
        e.stopPropagation();
        element.classList.add('dragover');
    }
    function handleDragLeave(e, element) {
        e.preventDefault();
        e.stopPropagation();
        element.classList.remove('dragover');
    }
    function handleDrop(e, element, jenisId, maksMb) {
        e.preventDefault();
        e.stopPropagation();
        element.classList.remove('dragover');

        const files = e.dataTransfer.files;
        if (files && files.length > 0) {
            const input = document.getElementById('file_input_' + jenisId);
            input.files = files;
            handleFileSelected(input, jenisId, maksMb);
        }
    }

    function handleFileSelected(input, jenisId, maksMb) {
        const file = input.files[0];
        if (!file) return;

        const allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
        const ext = file.name.split('.').pop().toLowerCase();
        const maxBytes = maksMb * 1024 * 1024;

        if (!allowedExtensions.includes(ext)) {
            const msg = 'Format berkas tidak diizinkan. Hanya file PDF, JPG, JPEG, atau PNG yang diterima.';
            if (window.MagKomToast) window.MagKomToast.warning(msg); else alert(msg);
            input.value = '';
            resetFileSelection(jenisId);
            return;
        }

        if (file.size > maxBytes) {
            const msg = `Ukuran berkas (${(file.size / (1024 * 1024)).toFixed(2)} MB) melebihi batas maksimal ${maksMb} MB.`;
            if (window.MagKomToast) window.MagKomToast.warning(msg); else alert(msg);
            input.value = '';
            resetFileSelection(jenisId);
            return;
        }

        const bar = document.getElementById('selected_bar_' + jenisId);
        const nameSpan = document.getElementById('selected_name_' + jenisId);
        const sizeSpan = document.getElementById('selected_size_' + jenisId);

        nameSpan.textContent = file.name;
        sizeSpan.textContent = `(${(file.size / 1024).toFixed(1)} KB)`;
        bar.style.display = 'flex';
    }

    function resetFileSelection(jenisId) {
        const input = document.getElementById('file_input_' + jenisId);
        if (input) input.value = '';

        const bar = document.getElementById('selected_bar_' + jenisId);
        if (bar) bar.style.display = 'none';
    }

    function handleUploadSubmit(form, jenisId) {
        const btn = document.getElementById('btn_submit_' + jenisId);
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `<span style="display:inline-block;width:12px;height:12px;border:2px solid #fff;border-top-color:transparent;border-radius:50%;animation:spin 0.6s linear infinite;margin-right:6px;"></span> Mengunggah...`;
        }
    }

    function handleAjukanSubmit(form) {
        const btn = document.getElementById('btnAjukanPendaftaran');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `<span style="display:inline-block;width:14px;height:14px;border:2px solid #fff;border-top-color:transparent;border-radius:50%;animation:spin 0.6s linear infinite;margin-right:8px;"></span> Mengajukan...`;
        }
    }
</script>
@endpush
@endsection
