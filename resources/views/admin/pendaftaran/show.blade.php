@extends('layouts.dashboard')

@section('title', 'Verifikasi ' . $pendaftaran->nomor_pendaftaran . ' - Admin MagKom Sleman')

@section('breadcrumbs')
    <nav style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; color: var(--neutral-500, #64748B);">
        <a href="{{ route('admin.dashboard') }}" style="color: var(--neutral-500, #64748B); text-decoration: none;">Dashboard</a>
        <span>/</span>
        <a href="{{ route('admin.pendaftaran.index') }}" style="color: var(--neutral-500, #64748B); text-decoration: none;">Antrean Verifikasi</a>
        <span>/</span>
        <span style="color: var(--neutral-900, #0F172A); font-weight: 700;">{{ $pendaftaran->nomor_pendaftaran }}</span>
    </nav>
@endsection

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
@endphp

<div class="adm-workspace-wrapper" style="display: flex; flex-direction: column; gap: 1.25rem; max-width: 1400px; margin: 0 auto; width: 100%;">

    <style>
        .adm-identity-bar {
            background: var(--neutral-0, #FFFFFF);
            border: 1px solid var(--neutral-300, #E2E8F0);
            border-radius: 12px;
            padding: 1.25rem 1.75rem;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1.25rem;
        }
        .adm-identity-left {
            display: flex;
            align-items: center;
            gap: 16px;
            min-width: 0;
        }
        .adm-avatar-circle {
            width: 54px;
            height: 54px;
            border-radius: 12px;
            background: var(--blue-tint, #EFF6FF);
            color: #0641CC;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            font-weight: 800;
            letter-spacing: 0.03em;
            border: 1px solid var(--blue-tint-line, #DBEAFE);
            flex-shrink: 0;
        }
        .adm-candidate-name {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--neutral-900, #0F172A);
            margin: 0;
            line-height: 1.3;
        }
        .adm-candidate-sub {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.825rem;
            color: var(--neutral-500, #64748B);
            flex-wrap: wrap;
        }
        .adm-split-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 1.25rem;
            align-items: start;
        }
        @media (max-width: 1024px) {
            .adm-split-grid {
                grid-template-columns: 1fr;
            }
        }
        .adm-doc-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        .adm-doc-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--neutral-0, #FFFFFF);
            border: 1px solid var(--neutral-300, #E2E8F0);
            border-radius: 10px;
            padding: 0.9rem 1.15rem;
            transition: all 0.15s ease;
            gap: 1rem;
        }
        .adm-doc-item:hover {
            border-color: #0641CC;
            box-shadow: 0 2px 8px rgba(6, 65, 204, 0.06);
        }
        .adm-doc-info {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            min-width: 0;
        }
        .adm-doc-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .adm-doc-icon-pdf {
            background: #FEE2E2;
            color: #DC2626;
        }
        .adm-doc-icon-img {
            background: var(--blue-tint, #EFF6FF);
            color: #0641CC;
        }
        .adm-doc-meta {
            min-width: 0;
        }
        .adm-doc-title {
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--neutral-900, #0F172A);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .adm-doc-detail {
            font-size: 0.75rem;
            color: var(--neutral-500, #64748B);
            margin-top: 2px;
        }
        .adm-doc-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-shrink: 0;
        }
        .adm-btn-action-preview {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 6px;
            background: #0641CC;
            color: #FFFFFF;
            font-size: 0.78rem;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: background 0.15s ease;
        }
        .adm-btn-action-preview:hover {
            background: #0751FF;
        }
        .adm-btn-action-download {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            background: var(--neutral-100, #F1F5F9);
            color: var(--neutral-700, #334155);
            border: 1px solid var(--neutral-300, #CBD5E1);
            text-decoration: none;
        }
        .adm-summary-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        @media (max-width: 640px) {
            .adm-summary-grid {
                grid-template-columns: 1fr;
            }
        }
        .adm-summary-cell {
            display: flex;
            flex-direction: column;
            gap: 4px;
            background: var(--neutral-100, #F8FAFC);
            padding: 0.85rem 1rem;
            border-radius: 8px;
            border: 1px solid var(--neutral-300, #E2E8F0);
        }
        .adm-summary-label {
            font-size: 0.725rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--neutral-500, #64748B);
        }
        .adm-summary-val {
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--neutral-900, #0F172A);
        }
        .adm-decision-tabs {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 4px;
            background: var(--neutral-200, #F1F5F9);
            padding: 4px;
            border-radius: 8px;
            margin-bottom: 1.25rem;
        }
        .adm-tab-pill {
            border: none;
            background: transparent;
            padding: 8px 4px;
            border-radius: 6px;
            font-size: 0.775rem;
            font-weight: 700;
            color: var(--neutral-600, #475569);
            cursor: pointer;
            transition: all 0.15s ease;
            text-align: center;
        }
        .adm-tab-pill.active-terima {
            background: #10B981;
            color: #FFFFFF;
            box-shadow: 0 1px 3px rgba(16, 185, 129, 0.3);
        }
        .adm-tab-pill.active-waiting_list {
            background: #0641CC;
            color: #FFFFFF;
            box-shadow: 0 1px 3px rgba(6, 65, 204, 0.3);
        }
        .adm-tab-pill.active-perlu_perbaikan {
            background: #D97706;
            color: #FFFFFF;
            box-shadow: 0 1px 3px rgba(217, 119, 6, 0.3);
        }
        .adm-tab-pill.active-tolak {
            background: #DC2626;
            color: #FFFFFF;
            box-shadow: 0 1px 3px rgba(220, 38, 38, 0.3);
        }
        .adm-tab-pill:disabled {
            color: var(--neutral-400, #94A3B8);
            cursor: not-allowed;
            opacity: 0.6;
        }
        .adm-form-field {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 1rem;
        }
        .adm-form-field label {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--neutral-900, #1E293B);
        }
        .adm-form-field select, .adm-form-field textarea {
            width: 100%;
            border: 1px solid var(--neutral-400, #CBD5E1);
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 0.85rem;
            color: var(--neutral-900, #0F172A);
            background: var(--neutral-0, #FFFFFF);
            outline: none;
            font-family: inherit;
        }
        .adm-form-field select:focus, .adm-form-field textarea:focus {
            border-color: #0641CC;
            box-shadow: 0 0 0 2px rgba(6, 65, 204, 0.1);
        }
        .doc-modal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(6px);
            align-items: center;
            justify-content: center;
            padding: 1.25rem;
        }
        .doc-modal.open {
            display: flex;
        }
        .doc-modal-box {
            background: var(--neutral-0, #ffffff);
            border-radius: 12px;
            width: 100%;
            max-width: 1000px;
            max-height: 92vh;
            display: flex;
            flex-direction: column;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            border: 1px solid var(--neutral-300, #E2E8F0);
        }
        .doc-modal-body {
            flex: 1;
            min-height: 60vh;
            max-height: 75vh;
            position: relative;
            background: var(--neutral-100, #F8FAFC);
            border: 1px solid var(--neutral-300, #E2E8F0);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin-top: 0.875rem;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        [data-theme="dark"] .adm-identity-bar,
        [data-theme="dark"] .adm-doc-item,
        [data-theme="dark"] .adm-summary-cell,
        [data-theme="dark"] .doc-modal-box {
            background: #182234 !important;
            border-color: #26354D !important;
        }
        [data-theme="dark"] .adm-candidate-name,
        [data-theme="dark"] .adm-doc-title,
        [data-theme="dark"] .adm-summary-val,
        [data-theme="dark"] .adm-form-field label,
        [data-theme="dark"] #previewModalTitle {
            color: #F8FAFC !important;
        }
        [data-theme="dark"] .adm-form-field textarea,
        [data-theme="dark"] .adm-form-field select {
            background: #0F172A !important;
            border-color: #26354D !important;
            color: #F8FAFC !important;
        }
        [data-theme="dark"] .doc-modal-body {
            background: #0F172A !important;
            border-color: #26354D !important;
        }
        [data-theme="dark"] .adm-btn-action-download {
            background: #111C2E !important;
            border-color: #26354D !important;
            color: #CBD5E1 !important;
        }
    </style>

    {{-- SEC-15: Indikator Peringatan Deteksi NIM/NISN Ganda Lintas Akun --}}
    @if(isset($duplikasiNimCount) && $duplikasiNimCount > 0)
        <div style="background: #FEF3C7; border: 1.5px solid #F59E0B; border-radius: 12px; padding: 14px 18px; color: #92400E; display: flex; align-items: flex-start; gap: 12px; box-shadow: 0 2px 6px rgba(245, 158, 11, 0.08);">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" style="flex-shrink: 0; margin-top: 1px; color: #D97706;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div style="font-size: 0.875rem; line-height: 1.45;">
                <strong style="color: #78350F; font-size: 0.9375rem;">Peringatan Keamanan & Integritas Data (SEC-15 / BR-17):</strong><br>
                Nomor identitas (NIM/NISN) <code>{{ $profil?->nim_nisn }}</code> ini juga terdaftar pada <strong>{{ $duplikasiNimCount }} akun peserta lainnya</strong> di sistem. Harap verifikator memeriksa keabsahan dokumen identitas pendaftar sebelum mengambil keputusan.
            </div>
        </div>
    @endif

    {{-- 1. COMPACT IDENTITY BAR DENGAN GENTELELLA CHIPS --}}
    <div class="adm-identity-bar">
        <div class="adm-identity-left">
            <div class="adm-avatar-circle">
                {{ $namaInisial ?: 'MG' }}
            </div>
            <div style="display: flex; flex-direction: column; gap: 4px; min-width: 0;">
                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                    <span style="font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size: 0.775rem; font-weight: 700; background: var(--blue-tint, #EFF6FF); color: #0641CC; border: 1px solid var(--blue-tint-line, #DBEAFE); padding: 2px 8px; border-radius: 6px;">
                        {{ $pendaftaran->nomor_pendaftaran }}
                    </span>
                    <span class="adm-status-badge" style="background: {{ $badgeStyle['bg'] }}; color: {{ $badgeStyle['color'] }}; border: 1px solid {{ $badgeStyle['border'] }}; padding: 2px 9px; border-radius: 9999px; font-size: 0.75rem; font-weight: 700;">
                        {{ $status->label() }}
                    </span>
                    @if($slaDays !== null && $slaDays >= 3)
                        <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 0.725rem; font-weight: 700; background: #FEE2E2; color: #DC2626; border: 1px solid #FECACA; padding: 2px 8px; border-radius: 9999px;" title="Target SLA Peninjauan ≤ 5 Hari Kerja (NFR-O1)">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            Antrean {{ $slaDays }} Hari
                        </span>
                    @endif
                </div>
                <h1 class="adm-candidate-name">{{ $user?->nama }}</h1>
                <div class="adm-candidate-sub">
                    <span>{{ $profil?->institusi ?? 'Institusi Pendidikan' }}</span>
                    <span>&bull;</span>
                    <span>{{ strtoupper($profil?->jenjang?->value ?? 'PESERTA') }}</span>
                    <span>&bull;</span>
                    <span>{{ $pendaftaran->kuota?->jurusan?->nama_jurusan ?? ($profil?->jurusan?->nama_jurusan ?? 'Semua Jurusan') }}</span>
                </div>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
            <div style="text-align: right; font-size: 0.775rem; color: var(--neutral-500, #64748B);">
                Waktu Pengajuan:
                <strong style="color: var(--neutral-900, #0F172A); display: block;">{{ $pendaftaran->diajukan_at ? $pendaftaran->diajukan_at->translatedFormat('d F Y, H:i') . ' WIB' : 'Belum Diajukan' }}</strong>
            </div>
            <a href="{{ route('admin.pendaftaran.index') }}" style="display: inline-flex; align-items: center; gap: 6px; padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.8125rem; font-weight: 600; text-decoration: none; background: var(--neutral-100, #F1F5F9); color: var(--neutral-700, #334155); border: 1px solid var(--neutral-300, #CBD5E1);">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                <span>Kembali ke Antrean</span>
            </a>
        </div>
    </div>

    {{-- 2. SPLIT LAYOUT (KIRI: DOKUMEN & BIODATA | KANAN: KEPUTUSAN & RIWAYAT) --}}
    <div class="adm-split-grid">

        {{-- KOLOM KIRI --}}
        <div style="display: flex; flex-direction: column; gap: 1.25rem;">

            {{-- PANEL DOKUMEN PERSYARATAN --}}
            <x-gentelella.x-panel title="Verifikasi Berkas Calon Peserta" subtitle="{{ $pendaftaran->dokumen->count() }} Berkas Persyaratan Terlampir" :toolbox="false">
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
                                            {{ $dok->jenisDokumen?->nama ?? 'Dokumen Pendukung' }}
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

            {{-- PANEL RANGKUMAN BIODATA & BULAN MAGANG --}}
            <x-gentelella.x-panel title="Rangkuman Biodata &amp; Bulan Magang" subtitle="Durasi Pengajuan: {{ $pendaftaran->durasi_bulan }} Bulan" :toolbox="false">
                <div class="adm-summary-grid">
                    <div class="adm-summary-cell">
                        <span class="adm-summary-label">Asal Instansi & Program Studi</span>
                        <span class="adm-summary-val">{{ $profil?->institusi ?? '-' }}</span>
                        <small style="color: var(--neutral-500, #64748B); font-size: 0.775rem;">{{ $profil?->jurusan?->nama_jurusan ?? '-' }}</small>
                    </div>
                    <div class="adm-summary-cell">
                        <span class="adm-summary-label">NIM / NISN & Jenjang</span>
                        <span class="adm-summary-val" style="font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;">{{ $profil?->nim_nisn ?? '-' }}</span>
                        <small style="color: var(--neutral-500, #64748B); font-size: 0.775rem;">Jenjang: {{ strtoupper($profil?->jenjang?->value ?? 'PESERTA') }}</small>
                    </div>
                    <div class="adm-summary-cell">
                        <span class="adm-summary-label">Email & Kontak Calon Peserta</span>
                        <span class="adm-summary-val">{{ $user?->email }}</span>
                        <small style="color: var(--neutral-500, #64748B); font-size: 0.775rem;">WhatsApp: {{ $profil?->no_hp ?? '-' }}</small>
                    </div>
                    <div class="adm-summary-cell">
                        <span class="adm-summary-label">Bulan Magang & Kuota</span>
                        <span class="adm-summary-val" style="color: #0641CC;">
                            {{ $pendaftaran->bulanMagang?->bulan_mulai?->translatedFormat('F Y') ?? 'Belum ditentukan' }}
                        </span>
                        @if($pendaftaran->kuota)
                            <small style="color: {{ $sisaKuota > 0 ? '#10B981' : '#DC2626' }}; font-weight: 700;">
                                {{ $sisaKuota > 0 ? "Tersedia sisa {$sisaKuota} kursi" : 'Kuota Jurusan Penuh' }}
                            </small>
                        @endif
                    </div>
                </div>
            </x-gentelella.x-panel>

        </div>

        {{-- KOLOM KANAN --}}
        <div style="display: flex; flex-direction: column; gap: 1.25rem;">

            {{-- PANEL KEPUTUSAN VERIFIKASI --}}
            <x-gentelella.x-panel title="Keputusan Verifikasi" subtitle="Keputusan kelayakan berkas administratif pendaftaran" :toolbox="false">
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
                    </div>
                @else
                    <div>
                        {{-- Indikator Status Kuota --}}
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.65rem 0.9rem; border-radius: 8px; font-size: 0.8125rem; font-weight: 700; margin-bottom: 1rem; background: {{ $sisaKuota > 0 ? '#ECFDF5' : '#FEF2F2' }}; border: 1px solid {{ $sisaKuota > 0 ? '#A7F3D0' : '#FECACA' }}; color: {{ $sisaKuota > 0 ? '#065F46' : '#991B1B' }};">
                            <span>Status Kuota:</span>
                            <span>{{ $sisaKuota > 0 ? "Tersedia ({$sisaKuota} Kursi)" : 'Penuh (Masuk Waiting List)' }}</span>
                        </div>

                        {{-- Tab Switcher Aksi --}}
                        <div class="adm-decision-tabs">
                            <button type="button" id="tab-btn-terima" onclick="switchActionTab('terima')" class="adm-tab-pill active-terima">
                                Terima
                            </button>
                            @if($sisaKuota <= 0)
                                <button type="button" id="tab-btn-waiting_list" onclick="switchActionTab('waiting_list')" class="adm-tab-pill">
                                    Waitlist
                                </button>
                            @else
                                <button type="button" disabled title="Waiting List hanya tersedia jika kuota telah penuh" class="adm-tab-pill">
                                    Waitlist
                                </button>
                            @endif
                            <button type="button" id="tab-btn-revisi" onclick="switchActionTab('perlu_perbaikan')" class="adm-tab-pill">
                                Revisi
                            </button>
                            <button type="button" id="tab-btn-tolak" onclick="switchActionTab('tolak')" class="adm-tab-pill">
                                Tolak
                            </button>
                        </div>

                        {{-- Form Keputusan --}}
                        <form method="POST" action="{{ route('admin.pendaftaran.putuskan', $pendaftaran) }}" id="decision-form">
                            @csrf
                            <input type="hidden" name="keputusan" id="keputusan-input" value="terima">

                            {{-- PANEL 1: TERIMA --}}
                            <div id="action-panel-terima" style="display: flex; flex-direction: column;">
                                <div style="background: var(--blue-tint, #EFF6FF); border: 1px solid var(--blue-tint-line, #DBEAFE); border-radius: 8px; padding: 0.85rem 1rem; margin-bottom: 1rem; font-size: 0.8125rem; color: #1E40AF; line-height: 1.45;">
                                    <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2" style="flex-shrink: 0; margin-top: 2px;">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="12" y1="16" x2="12" y2="12"></line>
                                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                        </svg>
                                        <div>
                                            <strong>Verifikasi Tahap 1:</strong> Menyetujui pendaftaran ini akan mengalokasikan kuota umum. Penugasan pembimbing lapangan dan bidang dinas dapat dilakukan di menu <strong>Penempatan Peserta</strong>. <span class="sr-only">Mentee Aktif</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="adm-form-field">
                                    <label for="catatan_terima">Catatan Penerimaan (Opsional)</label>
                                    <textarea id="catatan_terima" name="catatan" rows="2" placeholder="Tambahkan catatan verifikasi berkas jika diperlukan..."></textarea>
                                </div>

                                <button type="submit" onclick="return confirm('Apakah Anda yakin menerima berkas calon peserta ini?');" style="height: 42px; width: 100%; border: none; border-radius: 8px; background: #0641CC; color: #FFFFFF; font-weight: 700; font-size: 0.875rem; cursor: pointer; transition: background 0.15s ease;">
                                    Diterima
                                </button>
                            </div>

                            {{-- PANEL 2: WAITING LIST --}}
                            <div id="action-panel-waiting_list" style="display: none; flex-direction: column;">
                                <div class="adm-form-field">
                                    <label for="catatan_waiting_list">Catatan Antrean Waiting List (Opsional)</label>
                                    <textarea id="catatan_waiting_list" name="catatan" rows="3" placeholder="Tambahkan catatan khusus untuk antrean waiting list jika diperlukan..."></textarea>
                                </div>

                                <button type="submit" onclick="return confirm('Alihkan pendaftaran ini ke antrean Waiting List?');" style="height: 42px; width: 100%; border: none; border-radius: 8px; background: #0641CC; color: #FFFFFF; font-weight: 700; font-size: 0.85rem; cursor: pointer;">
                                    Masukkan ke Waiting List
                                </button>
                            </div>

                            {{-- PANEL 3: PERLU PERBAIKAN --}}
                            <div id="action-panel-perlu_perbaikan" style="display: none; flex-direction: column;">
                                <div class="adm-form-field">
                                    <label for="catatan_revisi">
                                        Catatan / Instruksi Perbaikan <span style="color: #DC2626;">*</span>
                                    </label>
                                    <textarea id="catatan_revisi" name="catatan" rows="3" placeholder="Sebutkan berkas yang perlu diperbaiki secara spesifik (min. 10 karakter)..."></textarea>
                                </div>

                                <button type="submit" onclick="return confirm('Kirimkan permintaan perbaikan berkas kepada calon peserta?');" style="height: 42px; width: 100%; border: none; border-radius: 8px; background: #D97706; color: #FFFFFF; font-weight: 700; font-size: 0.85rem; cursor: pointer;">
                                    Minta Perbaikan Berkas
                                </button>
                            </div>

                            {{-- PANEL 4: TOLAK --}}
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
                                
                                const panels = ['terima', 'waiting_list', 'perlu_perbaikan', 'tolak'];
                                panels.forEach(p => {
                                    const panel = document.getElementById('action-panel-' + p);
                                    const btn = document.getElementById('tab-btn-' + (p === 'perlu_perbaikan' ? 'revisi' : p));
                                    if (!panel || !btn) return;

                                    if (p === action) {
                                        panel.style.display = 'flex';
                                        btn.className = 'adm-tab-pill active-' + p;
                                    } else {
                                        panel.style.display = 'none';
                                        btn.className = 'adm-tab-pill';
                                    }
                                });

                                const catatanRevisi = document.getElementById('catatan_revisi');
                                const catatanTolak = document.getElementById('catatan_tolak');
                                if (catatanRevisi) catatanRevisi.required = (action === 'perlu_perbaikan');
                                if (catatanTolak) catatanTolak.required = (action === 'tolak');
                            }
                        </script>
                    </div>
                @endif
            </x-gentelella.x-panel>

            {{-- PANEL REKAM JEJAK PENDAFTARAN --}}
            <x-gentelella.card title="Rekam Jejak Pendaftaran" icon="fa fa-history">
                <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.8125rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--neutral-200, #F1F5F9); padding-bottom: 0.5rem;">
                        <span style="color: var(--neutral-500, #64748B);">Registrasi Akun</span>
                        <span style="font-weight: 700; color: var(--neutral-900, #0F172A);">{{ $user?->created_at?->translatedFormat('d M Y, H:i') ?? '-' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--neutral-200, #F1F5F9); padding-bottom: 0.5rem;">
                        <span style="color: var(--neutral-500, #64748B);">Pengajuan Berkas</span>
                        <span style="font-weight: 700; color: var(--neutral-900, #0F172A);">{{ $pendaftaran->diajukan_at?->translatedFormat('d M Y, H:i') ?? 'Belum' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: var(--neutral-500, #64748B);">Pembaruan Terakhir</span>
                        <span style="font-weight: 700; color: var(--neutral-900, #0F172A);">{{ $pendaftaran->updated_at?->translatedFormat('d M Y, H:i') ?? '-' }}</span>
                    </div>
                </div>
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
