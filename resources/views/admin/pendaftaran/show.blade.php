@extends('layouts.dashboard')

@section('title', 'Verifikasi ' . $pendaftaran->nomor_pendaftaran . ' - Admin MagKom Sleman')

@section('topbar_title', 'Verifikasi Berkas Pendaftaran Peserta')

@section('content')
@php
    $user = $pendaftaran->user;
    $profil = $user?->profil;
    $status = $pendaftaran->status;
    $statusValue = $status->value;
    $isFinal = in_array($statusValue, ['diterima', 'ditolak', 'dibatalkan'], true);

    $badgeVariant = match($statusValue) {
        'berkas_belum_lengkap' => 'secondary',
        'diajukan' => 'warning',
        'perlu_perbaikan' => 'danger',
        'waiting_list' => 'primary',
        'diterima' => 'success',
        'ditolak' => 'danger',
        default => 'secondary',
    };

    $badgeStyle = match($statusValue) {
        'berkas_belum_lengkap' => ['bg' => '#F1F5F9', 'color' => '#475569', 'border' => '#CBD5E1'],
        'diajukan' => ['bg' => '#FEF3C7', 'color' => '#92400E', 'border' => '#FCD34D'],
        'perlu_perbaikan' => ['bg' => '#FFEDD5', 'color' => '#C2410C', 'border' => '#FDBA74'],
        'waiting_list' => ['bg' => '#E0F2FE', 'color' => '#0369A1', 'border' => '#BAE6FD'],
        'diterima' => ['bg' => '#D1FAE5', 'color' => '#065F46', 'border' => '#A7F3D0'],
        'ditolak' => ['bg' => '#FEE2E2', 'color' => '#991B1B', 'border' => '#FECACA'],
        default => ['bg' => '#F1F5F9', 'color' => '#475569', 'border' => '#CBD5E1'],
    };

    $slaDays = null;
    if ($statusValue === 'diajukan' && $pendaftaran->diajukan_at) {
        $slaDays = (int) $pendaftaran->diajukan_at->diffInDays(now());
    }

    $namaInisial = '';
    if ($user?->nama) {
        $words = explode(' ', trim($user->nama));
        $namaInisial = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
    }

    $isSiswa = ($profil?->jenjang === \App\Enums\JenjangEnum::Smk
        || (is_string($profil?->jenjang) && strtolower((string)$profil->jenjang) === 'smk')
        || ($profil?->jenjang instanceof \App\Enums\JenjangEnum && $profil->jenjang->value === 'smk')
        || ($profil?->jurusan?->kategori === 'siswa'));
    $labelNomorInduk = $isSiswa ? 'NISN' : 'NIM';
    $labelBidangStudi = $isSiswa ? 'Jurusan' : 'Program Studi';
    $nomorRegClean = '#' . ltrim($pendaftaran->nomor_pendaftaran, '#');
@endphp

<div class="adm-workspace-wrapper" style="display: flex; flex-direction: column; gap: 1.25rem; width: 100%;">

    {{-- 1. HEADER PROFIL PESERTA MODERN --}}
    <div class="adm-identity-bar">
        <div class="adm-identity-left">
            <div class="adm-avatar-circle" aria-hidden="true">
                {{ $namaInisial ?: 'MG' }}
            </div>
            <div style="min-width: 0;">
                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 4px;">
                    <h1 class="adm-candidate-name">
                        {{ $user?->nama ?? 'Nama Tidak Terdata' }}
                    </h1>
                    <span class="sr-only">{{ $status->label() }}</span>
                    @if($slaDays !== null && $slaDays >= 3)
                        <span class="adm-badge-status-pill adm-badge-ditolak" title="Target SLA Peninjauan ≤ 5 Hari Kerja (NFR-O1)">
                            <span class="adm-status-dot" aria-hidden="true"></span>
                            Antrean {{ $slaDays }} Hari
                        </span>
                    @endif
                </div>
                <div class="adm-candidate-sub">
                    <span class="adm-candidate-reg">{{ $nomorRegClean }}</span>
                </div>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
            <div style="text-align: right; font-size: 0.775rem; color: rgba(255, 255, 255, 0.8);">
                Waktu Pengajuan:
                <strong style="color: #FFFFFF; display: block;">{{ $pendaftaran->diajukan_at ? $pendaftaran->diajukan_at->translatedFormat('d F Y, H:i') . ' WIB' : 'Belum Diajukan' }}</strong>
            </div>
            <a href="{{ route('admin.pendaftaran.index') }}" class="adm-btn-back" aria-label="Kembali ke Verifikasi Berkas">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                <span>Kembali ke Verifikasi Berkas</span>
            </a>
        </div>
    </div>

    {{-- 2. SPLIT LAYOUT (KIRI: DOKUMEN & BIODATA | KANAN: KEPUTUSAN & RIWAYAT) --}}
    <div class="adm-split-grid">

        {{-- KOLOM KIRI --}}
        <div style="display: flex; flex-direction: column; gap: 1.25rem;">

            {{-- Info Kelompok Magang (TASK 03) --}}
            @if($pendaftaran->kelompokMagang)
                <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px; padding: 14px 18px; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; flex-wrap: wrap; gap: 8px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="color: #64748B; font-size: 0.8125rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
                                Kelompok:
                            </span>
                            <strong style="color: #0F172A; font-size: 1.05rem; font-weight: 800;">
                                {{ $pendaftaran->kelompokMagang->nama_kelompok }}
                            </strong>
                        </div>
                    </div>
                    <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 8px; overflow: hidden;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.8rem;">
                            <thead>
                                <tr style="background: #F8FAFC; border-bottom: 1px solid #E2E8F0; text-align: left; color: #475569;">
                                    <th style="padding: 6px 12px;">Peran</th>
                                    <th style="padding: 6px 12px;">Nama Peserta</th>
                                    <th style="padding: 6px 12px;">{{ $labelNomorInduk }}</th>
                                    <th style="padding: 6px 12px;">Kontak WhatsApp</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="border-bottom: 1px solid #F1F5F9;">
                                    <td style="padding: 6px 12px;"><span style="font-weight: 500; color: #64748B;">Ketua Kelompok</span></td>
                                    <td style="padding: 6px 12px; font-weight: 400; color: #64748B;">{{ $pendaftaran->user?->nama }}</td>
                                    <td style="padding: 6px 12px; color: #64748B;">{{ $pendaftaran->user?->profil?->nim_nisn ?? '-' }}</td>
                                    <td style="padding: 6px 12px; color: #64748B;">{{ $pendaftaran->user?->profil?->no_hp ?? '-' }}</td>
                                </tr>
                                @foreach($pendaftaran->kelompokMagang->anggota as $anggota)
                                    <tr style="border-bottom: 1px solid #F1F5F9;">
                                        <td style="padding: 6px 12px;"><span style="color: #64748B;">Anggota</span></td>
                                        <td style="padding: 6px 12px; font-weight: 400; color: #64748B;">{{ $anggota->nama }}</td>
                                        <td style="padding: 6px 12px; color: #64748B;">{{ $anggota->nim_nisn ?? '-' }}</td>
                                        <td style="padding: 6px 12px; color: #64748B;">{{ $anggota->no_hp ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- PANEL DOKUMEN PERSYARATAN --}}
            <x-gentelella.x-panel title="Verifikasi Berkas Calon Peserta" :toolbox="false">
                @if($pendaftaran->dokumen->isEmpty())
                    <div style="text-align: center; padding: 2.5rem 1rem; color: var(--neutral-500, #64748B); background: var(--neutral-100, #F8FAFC); border-radius: 8px; border: 1px dashed var(--neutral-400, #CBD5E1);">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" style="margin: 0 auto 0.5rem auto; color: var(--neutral-400, #94A3B8);"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        <p style="margin: 0; font-size: 0.875rem; font-weight: 600;">Calon peserta belum mengunggah dokumen persyaratan.</p>
                    </div>
                @else
                    <div class="adm-doc-list">
                        @foreach($pendaftaran->dokumen as $dok)
                            @php
                                $isPdf = str_contains(strtolower($dok->mime ?? ''), 'pdf') || str_ends_with(strtolower($dok->nama_asli ?? ''), '.pdf');
                            @endphp
                            <div class="adm-doc-item">
                                <div class="adm-doc-info">
                                    <div class="adm-doc-icon {{ $isPdf ? 'adm-doc-icon-pdf' : 'adm-doc-icon-img' }}">
                                        @if($isPdf)
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                        @else
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                        @endif
                                    </div>
                                    <div class="adm-doc-meta">
                                        <div class="adm-doc-title" title="{{ $dok->nama_asli }}">
                                            <span>{{ $dok->jenisDokumen?->nama ?? 'Dokumen Pendukung' }}</span>
                                            @if($pendaftaran->tipe === 'kelompok' || $pendaftaran->kelompokMagang)
                                                @if($dok->kelompokAnggota)
                                                    <span style="font-size: 0.6875rem; font-weight: 700; background: #E0F2FE; color: #0369A1; padding: 2px 7px; border-radius: 999px; margin-left: 6px;">
                                                        Anggota: {{ $dok->kelompokAnggota->nama }}
                                                    </span>
                                                @elseif(in_array($dok->jenisDokumen?->kode, ['cv', 'transkrip', 'kartu_pelajar']))
                                                    <span style="font-size: 0.6875rem; font-weight: 700; background: #FEF3C7; color: #92400E; padding: 2px 7px; border-radius: 999px; margin-left: 6px;">
                                                        Ketua Kelompok
                                                    </span>
                                                @else
                                                    <span style="font-size: 0.6875rem; font-weight: 700; background: #F1F5F9; color: #475569; padding: 2px 7px; border-radius: 999px; margin-left: 6px;">
                                                        Kolektif Kelompok
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                        <div class="adm-doc-detail">
                                            {{ $dok->nama_asli }} &bull; {{ number_format($dok->ukuran / 1024, 1) }} KB
                                        </div>
                                    </div>
                                </div>

                                <div class="adm-doc-actions">
                                    <button type="button" onclick="openDocPreview('{{ $dok->getSignedPreviewUrl(5) }}', '{{ $dok->getSignedDownloadUrl(5) }}', '{{ addslashes($dok->jenisDokumen?->nama ?? 'Dokumen Persyaratan') }} - {{ addslashes($dok->nama_asli) }}', '{{ $dok->mime }}')" class="adm-btn-action-preview" title="Tinjau berkas langsung di dalam layar">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                        <span>Tinjau Berkas</span>
                                    </button>
                                    <a href="{{ $dok->getSignedDownloadUrl(5) }}" class="adm-btn-action-download" title="Unduh berkas fisik">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-gentelella.x-panel>

            {{-- PANEL DATA CALON PESERTA (BIODATA DI KOLOM KIRI BAWAH) --}}
            <x-gentelella.x-panel title="Data Calon Peserta" :toolbox="false">
                <dl class="adm-biodata-grid-2col" style="margin: 0;">
                    <div class="adm-biodata-card-item">
                        <dt class="adm-biodata-dt">Asal Instansi</dt>
                        <dd class="adm-biodata-dd">{{ $profil?->institusi ?? '-' }}</dd>
                    </div>
                    <div class="adm-biodata-card-item">
                        <dt class="adm-biodata-dt">{{ $labelBidangStudi }}</dt>
                        <dd class="adm-biodata-dd">{{ $profil?->jurusan?->nama_jurusan ?? '-' }}</dd>
                    </div>
                    <div class="adm-biodata-card-item">
                        <dt class="adm-biodata-dt">{{ $labelNomorInduk }}</dt>
                        <dd class="adm-biodata-dd">
                            <span style="font-family: ui-monospace, monospace;">{{ $profil?->nim_nisn ?? '-' }}</span>
                            @if(isset($duplikasiNimCount) && $duplikasiNimCount > 0)
                                <span class="sr-only">Peringatan Keamanan: Terdaftar pada {{ $duplikasiNimCount }} akun peserta lainnya ({{ $profil?->nim_nisn }})</span>
                            @endif
                        </dd>
                    </div>
                    <div class="adm-biodata-card-item">
                        <dt class="adm-biodata-dt">Periode Bulan Magang</dt>
                        <dd class="adm-biodata-dd">
                            {{ $pendaftaran->bulanMagang?->bulan_mulai?->translatedFormat('F Y') ?? 'Belum ditentukan' }}
                        </dd>
                    </div>
                    <div class="adm-biodata-card-item">
                        <dt class="adm-biodata-dt">Email Calon Peserta</dt>
                        <dd class="adm-biodata-dd" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $user?->email }}">{{ $user?->email ?? '-' }}</dd>
                    </div>
                    <div class="adm-biodata-card-item">
                        <dt class="adm-biodata-dt">Kontak WhatsApp</dt>
                        <dd class="adm-biodata-dd">{{ $profil?->no_hp ?? '-' }}</dd>
                    </div>
                    <div class="adm-biodata-card-item adm-biodata-card-item-full">
                        <dt class="adm-biodata-dt">Status Kuota Bidang</dt>
                        @if($pendaftaran->kuota)
                            <dd class="adm-biodata-dd">
                                {{ $sisaKuota > 0 ? "Tersedia sisa {$sisaKuota} kursi di bidang ini" : 'Kuota Bidang Penuh' }}
                            </dd>
                        @else
                            <dd class="adm-biodata-dd" style="color: #64748B;">
                                Belum ada kuota ditentukan
                            </dd>
                        @endif
                    </div>
                </dl>
            </x-gentelella.x-panel>

        </div>

        {{-- KOLOM KANAN (SIDEBAR STICKY) --}}
        <div class="adm-sidebar-sticky">

            {{-- PANEL KEPUTUSAN VERIFIKASI --}}
            <x-gentelella.x-panel title="Keputusan Verifikasi" :toolbox="false">
                @if($isFinal)
                    <div style="background: {{ $statusValue === 'diterima' ? '#ECFDF5' : '#FEF2F2' }}; border: 1px solid {{ $statusValue === 'diterima' ? '#A7F3D0' : '#FECACA' }}; border-radius: 8px; padding: 1rem;">
                        <div style="display: flex; align-items: center; gap: 8px; font-weight: 800; color: {{ $statusValue === 'diterima' ? '#065F46' : '#991B1B' }}; font-size: 0.95rem;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>
                            <span>Pengajuan Telah Selesai Diputuskan</span>
                        </div>
                        <p style="margin: 0.6rem 0 0 0; color: #334155; line-height: 1.5; font-size: 0.85rem;">
                            Status resmi saat ini adalah <strong>{{ $status->label() }}</strong> pada {{ $pendaftaran->diputuskan_at?->translatedFormat('d F Y, H:i') ?? '-' }}.
                            @if($pendaftaran->catatan)
                                <div style="margin-top: 0.6rem; padding: 0.75rem 1rem; background: rgba(255, 255, 255, 0.85); border-radius: 6px; font-style: italic; color: #1E293B; border-left: 3px solid {{ $statusValue === 'diterima' ? '#10B981' : '#DC2626' }};">
                                    &ldquo;{{ $pendaftaran->catatan }}&rdquo;
                                </div>
                            @endif
                        </p>
                        @if($statusValue === 'diterima' && $pendaftaran->file_surat_diterima)
                            <div style="margin-top: 0.85rem; padding: 0.75rem 1rem; background: #FFFFFF; border: 1px solid #A7F3D0; border-radius: 6px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                                    <span style="font-weight: 700; color: #065F46; font-size: 0.8125rem;">Surat Keterangan Diterima Terunggah</span>
                                </div>
                                <div style="display: flex; gap: 0.5rem;">
                                    <a href="{{ route('admin.pendaftaran.surat-diterima.pratinjau', $pendaftaran) }}" target="_blank" style="padding: 0.25rem 0.65rem; border-radius: 6px; font-size: 0.75rem; font-weight: 700; text-decoration: none; background: #ECFDF5; color: #047857; border: 1px solid #A7F3D0;">Pratinjau</a>
                                    <a href="{{ route('admin.pendaftaran.surat-diterima', $pendaftaran) }}" style="padding: 0.25rem 0.65rem; border-radius: 6px; font-size: 0.75rem; font-weight: 700; text-decoration: none; background: #059669; color: #FFFFFF;">Unduh PDF</a>
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    @if(auth()->user()?->isSuperAdmin())
                        <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 1.25rem; text-align: center; color: #475569;">
                            <span style="display: inline-block; background: #EFF6FF; color: #1D4ED8; font-weight: 700; font-size: 0.75rem; padding: 0.25rem 0.6rem; border-radius: 999px; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em;">Mode Lihat Saja (Read-Only)</span>
                            <p style="margin: 0; font-size: 0.8125rem; line-height: 1.4; color: #64748B;">
                                Super Admin memiliki hak akses lihat saja pada modul verifikasi pendaftaran. Tindakan penerimaan atau penolakan berkas merupakan kewenangan staf Administrasi / Verifikator.
                            </p>
                        </div>
                    @else
                    <div>
                        {{-- Indikator Status Kuota --}}
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.65rem 0.9rem; border-radius: 8px; font-size: 0.8125rem; font-weight: 700; margin-bottom: 1rem; background: {{ $sisaKuota > 0 ? '#ECFDF5' : '#FEF2F2' }}; border: 1px solid {{ $sisaKuota > 0 ? '#A7F3D0' : '#FECACA' }}; color: {{ $sisaKuota > 0 ? '#065F46' : '#991B1B' }};">
                            <span>Status Kuota:</span>
                            <span>{{ $sisaKuota > 0 ? "Tersedia ({$sisaKuota} Kursi)" : 'Kuota Penuh (0 Kursi)' }}</span>
                        </div>

                        {{-- Tab Switcher Aksi (Hanya Terima & Tolak) --}}
                        <div class="adm-decision-tabs">
                            <button type="button" id="tab-btn-terima" onclick="switchActionTab('terima')" class="adm-tab-pill active-terima">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <span>Terima</span>
                            </button>
                            <button type="button" id="tab-btn-tolak" onclick="switchActionTab('tolak')" class="adm-tab-pill">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                <span>Tolak</span>
                            </button>
                        </div>

                        {{-- Form Keputusan --}}
                        <form method="POST" action="{{ route('admin.pendaftaran.putuskan', $pendaftaran) }}" id="decision-form" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="keputusan" id="keputusan-input" value="terima">

                            {{-- PANEL 1: TERIMA --}}
                            <div id="action-panel-terima" style="display: flex; flex-direction: column;">
                                <span class="sr-only">Mentee Aktif</span>

                                <div class="adm-form-field" style="margin-bottom: 0.85rem;">
                                    <label for="file_surat_diterima" style="display: flex; justify-content: space-between; align-items: center; font-weight: 700; font-size: 0.8125rem; color: #1E293B;">
                                        <span>Upload Surat Keterangan Diterima (PDF)</span>
                                        <span style="font-size: 0.725rem; color: #64748B; font-weight: 500;">Maks. 2 MB</span>
                                    </label>
                                    <input type="file" id="file_surat_diterima" name="file_surat_diterima" accept="application/pdf" style="width: 100%; font-size: 0.8125rem; padding: 0.45rem 0.65rem; border-radius: 8px; border: 1px solid #CBD5E1; background: #FFFFFF;">
                                    <small style="color: #64748B; font-size: 0.725rem; display: block; margin-top: 0.25rem; line-height: 1.4;">
                                        Unggah dokumen PDF resmi bertanda tangan dinas untuk peserta.
                                    </small>
                                </div>

                                <div class="adm-form-field">
                                    <label for="catatan_terima">Catatan Penerimaan (Opsional)</label>
                                    <textarea id="catatan_terima" name="catatan" rows="2" placeholder="Tambahkan catatan verifikasi berkas jika diperlukan..."></textarea>
                                </div>

                                <button type="submit" onclick="return confirm('Apakah Anda yakin menerima berkas calon peserta ini?');" style="height: 42px; width: 100%; border: none; border-radius: 8px; background: #0641CC; color: #FFFFFF; font-weight: 700; font-size: 0.875rem; cursor: pointer; transition: background 0.15s ease;">
                                    Diterima
                                </button>
                            </div>

                            {{-- PANEL 2: TOLAK --}}
                            <div id="action-panel-tolak" style="display: none; flex-direction: column;">
                                <div class="adm-form-field">
                                    <label for="catatan_tolak">
                                        Alasan Penolakan <span style="color: #DC2626;">*</span>
                                    </label>
                                    <textarea id="catatan_tolak" name="catatan" rows="3" placeholder="Tuliskan alasan penolakan pengajuan (min. 10 karakter)..."></textarea>
                                </div>

                                <button type="submit" onclick="return confirm('Apakah Anda yakin MENOLAK pengajuan pendaftaran ini?');" style="height: 42px; width: 100%; border: none; border-radius: 8px; background: #DC2626; color: #FFFFFF; font-weight: 700; font-size: 0.85rem; cursor: pointer;">
                                    Tolak Pengajuan Pendaftaran
                                </button>
                            </div>
                        </form>

                        <script>
                            function switchActionTab(action) {
                                document.getElementById('keputusan-input').value = action;
                                
                                const panels = ['terima', 'tolak'];
                                panels.forEach(p => {
                                    const panel = document.getElementById('action-panel-' + p);
                                    const btn = document.getElementById('tab-btn-' + p);
                                    if (!panel || !btn) return;

                                    if (p === action) {
                                        panel.style.display = 'flex';
                                        btn.className = 'adm-tab-pill active-' + p;
                                    } else {
                                        panel.style.display = 'none';
                                        btn.className = 'adm-tab-pill';
                                    }
                                });

                                const catatanTolak = document.getElementById('catatan_tolak');
                                if (catatanTolak) catatanTolak.required = (action === 'tolak');
                            }
                        </script>
                    </div>
                    @endif
                @endif
            </x-gentelella.x-panel>

            {{-- PANEL REKAM JEJAK PENDAFTARAN (TIMELINE VERTIKAL KRONOLOGIS) --}}
            <x-gentelella.card title="Rekam Jejak Pendaftaran" icon="clock">
                <ol class="adm-timeline-ol" role="list">
                    <li class="adm-timeline-item">
                        <span class="adm-timeline-dot completed" aria-hidden="true"></span>
                        <h4 class="adm-timeline-title">1. Registrasi Akun</h4>
                        <time class="adm-timeline-time">{{ $user?->created_at?->translatedFormat('d M Y, H:i') ?? '-' }}</time>
                    </li>
                    <li class="adm-timeline-item">
                        <span class="adm-timeline-dot completed" aria-hidden="true"></span>
                        <h4 class="adm-timeline-title">2. Pembuatan Pendaftaran</h4>
                        <time class="adm-timeline-time">{{ $pendaftaran->created_at?->translatedFormat('d M Y, H:i') ?? '-' }}</time>
                    </li>
                    <li class="adm-timeline-item">
                        <span class="adm-timeline-dot {{ $pendaftaran->diajukan_at ? 'completed' : ($statusValue === 'berkas_belum_lengkap' ? 'active' : 'pending') }}" aria-hidden="true"></span>
                        <h4 class="adm-timeline-title">3. Pengajuan Berkas</h4>
                        <time class="adm-timeline-time">{{ $pendaftaran->diajukan_at ? $pendaftaran->diajukan_at->translatedFormat('d M Y, H:i') : 'Belum Diajukan (Draf)' }}</time>
                    </li>
                    <li class="adm-timeline-item">
                        <span class="adm-timeline-dot {{ $isFinal || $statusValue === 'diajukan' ? 'active' : 'pending' }}" aria-hidden="true"></span>
                        <h4 class="adm-timeline-title">4. {{ $isFinal ? 'Keputusan: ' . $status->label() : 'Verifikasi Berkas' }}</h4>
                        <time class="adm-timeline-time">
                            @if($isFinal)
                                {{ $pendaftaran->diputuskan_at?->translatedFormat('d M Y, H:i') ?? $pendaftaran->updated_at?->translatedFormat('d M Y, H:i') }}
                            @elseif($statusValue === 'diajukan')
                                Sedang Ditinjau Verifikator
                            @else
                                Menunggu Pengajuan Berkas
                            @endif
                        </time>
                    </li>
                </ol>
            </x-gentelella.card>

        </div>

    </div>

</div>

{{-- 3. MODAL IN-APP PRATINJAU DOKUMEN --}}
<div class="doc-modal" id="docPreviewModal" onclick="if(event.target === this) closeDocPreview()" role="dialog" aria-modal="true" aria-labelledby="previewModalTitle">
    <div class="doc-modal-box">
        <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 1rem; border-bottom: 1px solid var(--neutral-300, #E2E8F0);">
            <div style="display: flex; align-items: center; gap: 0.75rem; overflow: hidden; padding-right: 0.75rem;">
                <div id="previewModalIcon" style="width: 38px; height: 38px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background: var(--blue-tint, #EFF6FF); color: #0641CC;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                </div>
                <div style="overflow: hidden;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <h3 id="previewModalTitle" style="font-size: 0.95rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
                            Pratinjau Dokumen
                        </h3>
                        <span id="previewModalBadge" style="font-size: 0.6875rem; padding: 2px 8px; border-radius: 9999px; background: #FEE2E2; color: #991B1B; font-weight: 700;">PDF</span>
                    </div>
                    <p style="font-size: 0.75rem; color: var(--neutral-500, #64748B); margin: 2px 0 0 0;">
                        Streaming berkas aman langsung dari penyimpanan privat Pemkab Sleman
                    </p>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 0.5rem; flex-shrink: 0;">
                <a id="previewOpenNewTabBtn" href="#" target="_blank" rel="noopener noreferrer" style="display: inline-flex; align-items: center; gap: 0.375rem; background: var(--neutral-0, #ffffff); border: 1px solid var(--neutral-400, #CBD5E1); color: var(--neutral-700, #334155); padding: 0.45rem 0.85rem; border-radius: 6px; font-weight: 600; font-size: 0.78rem; text-decoration: none;" title="Buka pratinjau di tab browser baru">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                        <polyline points="15 3 21 3 21 9"></polyline>
                        <line x1="10" y1="14" x2="21" y2="3"></line>
                    </svg>
                    <span>Tab Baru</span>
                </a>
                <a id="previewDownloadBtn" href="#" style="display: inline-flex; align-items: center; gap: 0.375rem; background: var(--neutral-0, #ffffff); border: 1px solid var(--neutral-400, #CBD5E1); color: var(--neutral-700, #334155); padding: 0.45rem 0.85rem; border-radius: 6px; font-weight: 600; font-size: 0.78rem; text-decoration: none;" title="Unduh berkas fisik">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    <span>Unduh</span>
                </a>
                <button type="button" onclick="closeDocPreview()" style="background: none; border: none; font-size: 1.5rem; line-height: 1; cursor: pointer; color: var(--neutral-400, #94A3B8); padding: 0 0.25rem;" title="Tutup pratinjau (Esc)">&times;</button>
            </div>
        </div>

        <div class="doc-modal-body">
            {{-- Loading Spinner --}}
            <div id="previewLoading" style="position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; background: rgba(255, 255, 255, 0.95); z-index: 10;">
                <div style="width: 38px; height: 38px; border: 3px solid var(--neutral-300, #E2E8F0); border-top-color: #0641CC; border-radius: 50%; animation: spin 0.7s linear infinite; margin-bottom: 12px;"></div>
                <div style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-900, #0F172A);">Memuat Pratinjau Dokumen...</div>
                <div style="font-size: 0.75rem; color: var(--neutral-500, #64748B); margin-top: 4px;">Mengambil stream berkas aman dari disk privat</div>
            </div>

            {{-- PDF Iframe Container --}}
            <iframe id="previewIframe" src="about:blank" style="width: 100%; height: 75vh; border: none; display: none;"></iframe>

            {{-- Image Container (JPG/PNG) --}}
            <div id="previewImageWrapper" style="display: none; padding: 1.5rem; width: 100%; height: 100%; text-align: center; overflow: auto; justify-content: center; align-items: center;">
                <img id="previewImage" alt="Pratinjau Berkas" style="max-width: 100%; max-height: 70vh; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.12); object-fit: contain;">
            </div>

            {{-- Error Fallback Container --}}
            <div id="previewError" style="display: none; flex-direction: column; align-items: center; justify-content: center; padding: 2rem; text-align: center;">
                <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: #D97706; margin-bottom: 0.75rem;">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <div style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900, #0F172A);">Tidak Dapat Memuat Pratinjau Langsung</div>
                <div style="font-size: 0.8125rem; color: var(--neutral-500, #64748B); margin: 0.375rem 0 1rem 0; max-width: 420px;">
                    Format berkas atau konfigurasi browser tidak mendukung pratinjau inline. Silakan gunakan tombol unduh di bawah ini.
                </div>
                <a id="previewFallbackDownloadBtn" href="#" style="padding: 0.5rem 1rem; background: #0641CC; color: #FFFFFF; font-weight: 600; font-size: 0.8125rem; text-decoration: none; border-radius: 6px;">
                    Unduh Berkas Sekarang
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    let previewTimer = null;

    function openDocPreview(previewUrl, downloadUrl, title, mime) {
        const modal = document.getElementById('docPreviewModal');
        const modalTitle = document.getElementById('previewModalTitle');
        const modalBadge = document.getElementById('previewModalBadge');
        const openNewTabBtn = document.getElementById('previewOpenNewTabBtn');
        const downloadBtn = document.getElementById('previewDownloadBtn');
        const fallbackBtn = document.getElementById('previewFallbackDownloadBtn');
        const loading = document.getElementById('previewLoading');
        const iframe = document.getElementById('previewIframe');
        const imgWrapper = document.getElementById('previewImageWrapper');
        const img = document.getElementById('previewImage');
        const errBox = document.getElementById('previewError');
        const iconContainer = document.getElementById('previewModalIcon');

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
            modalBadge.textContent = 'PDF';
            modalBadge.style.background = '#FEE2E2';
            modalBadge.style.color = '#991B1B';
            if (iconContainer) {
                iconContainer.style.background = '#FEE2E2';
                iconContainer.style.color = '#DC2626';
            }
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
            modalBadge.textContent = 'Gambar';
            modalBadge.style.background = 'var(--blue-tint, #EFF6FF)';
            modalBadge.style.color = '#0641CC';
            if (iconContainer) {
                iconContainer.style.background = 'var(--blue-tint, #EFF6FF)';
                iconContainer.style.color = '#0641CC';
            }
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
        if (e.key === 'Escape') {
            closeDocPreview();
        }
    });
</script>
@endsection
