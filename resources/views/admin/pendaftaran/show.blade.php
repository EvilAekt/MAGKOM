@extends('layouts.dashboard')

@section('title', 'Verifikasi Pendaftaran ' . $pendaftaran->nomor_pendaftaran . ' - Admin MagKom')
@section('page_title', 'Verifikasi Berkas Calon Peserta')
@section('page_description', 'Tinjau kelengkapan dokumen persyaratan dan tetapkan keputusan penerimaan pendaftaran.')

@section('breadcrumbs')
    <nav style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; color: var(--neutral-600, #64748B);">
        <span>Portal MagKom</span>
        <span>/</span>
        <a href="{{ route('admin.pendaftaran.index') }}" style="color: var(--neutral-600, #64748B); text-decoration: none;">Antrean Verifikasi</a>
        <span>/</span>
        <span style="color: var(--neutral-900, #0F172A); font-weight: 700;">{{ $pendaftaran->nomor_pendaftaran }}</span>
    </nav>
@endsection

@section('page_actions')
    <a href="{{ route('admin.pendaftaran.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; background: #ffffff; border: 1px solid var(--neutral-300, #CBD5E1); color: var(--neutral-700, #334155); padding: 0.55rem 1.1rem; border-radius: 8px; font-weight: 600; font-size: 0.8125rem; text-decoration: none; transition: all 0.15s ease; box-shadow: 0 1px 2px rgba(0,0,0,0.05);" onmouseover="this.style.borderColor='#0641CC'; this.style.color='#0641CC'" onmouseout="this.style.borderColor='var(--neutral-300, #CBD5E1)'; this.style.color='var(--neutral-700, #334155)'">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        <span>Kembali ke Antrean</span>
    </a>
@endsection

@section('content')
@php
    $user = $pendaftaran->user;
    $profil = $user?->profil;
    $status = $pendaftaran->status;
    $statusValue = $status->value;
    $isFinal = in_array($statusValue, ['diterima', 'ditolak', 'dibatalkan'], true);

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
@endphp

<div style="display: flex; flex-direction: column; gap: 1.5rem; max-width: 1400px; margin: 0 auto; width: 100%;">

    <style>
        .admin-card {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid var(--neutral-200, #E2E8F0);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        .doc-item-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.25rem;
            background: #F8FAFC;
            border: 1px solid var(--neutral-200, #E2E8F0);
            border-radius: 10px;
            flex-wrap: wrap;
            gap: 0.75rem;
            transition: all 0.15s ease;
        }
        .doc-item-card:hover {
            border-color: #0641CC;
            background: #ffffff;
            box-shadow: 0 2px 6px rgba(6, 65, 204, 0.08);
        }
        .btn-tab-action {
            flex: 1;
            min-width: 90px;
            padding: 0.6rem 0.5rem;
            border-radius: 8px;
            border: 1px solid transparent;
            font-weight: 700;
            font-size: 0.8125rem;
            cursor: pointer;
            transition: all 0.15s ease;
            text-align: center;
        }
    </style>

    {{-- 1. TOP METADATA CARD --}}
    <div class="admin-card" style="padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.4rem; flex-wrap: wrap;">
                <span style="font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size: 0.8125rem; font-weight: 700; color: #0641CC; background: #EFF6FF; padding: 0.2rem 0.6rem; border-radius: 6px; border: 1px solid #DBEAFE;">
                    {{ $pendaftaran->nomor_pendaftaran }}
                </span>
                <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.2rem 0.65rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: {{ $badgeStyle['bg'] }}; color: {{ $badgeStyle['color'] }}; border: 1px solid {{ $badgeStyle['border'] }};">
                    {{ $status->label() }}
                </span>
                @if($slaDays !== null && $slaDays >= 3)
                    <span style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.7rem; font-weight: 700; color: #DC2626; background: #FEE2E2; padding: 0.15rem 0.5rem; border-radius: 4px;" title="Target SLA Peninjauan ≤ 5 Hari Kerja (NFR-O1)">
                        SLA: {{ $slaDays }} Hari Masuk
                    </span>
                @endif
            </div>
            <h1 style="font-size: 1.35rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0; letter-spacing: -0.02em;">
                {{ $user?->nama }} &bull; <span style="font-weight: 600; color: var(--neutral-600, #475569); font-size: 1.15rem;">{{ $profil?->institusi ?? 'Institusi Pendidikan' }}</span>
            </h1>
        </div>

        <div style="text-align: right;">
            <span style="font-size: 0.78rem; color: var(--neutral-500, #64748B); font-weight: 600;">Stempel Waktu Pengajuan:</span>
            <div style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900, #0F172A); margin-top: 0.15rem;">
                {{ $pendaftaran->diajukan_at ? $pendaftaran->diajukan_at->translatedFormat('d F Y, H:i') . ' WIB' : 'Belum Diajukan' }}
            </div>
        </div>
    </div>

    {{-- 2. SPLIT-SCREEN CONTAINER --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 1.5rem; align-items: start;">

        {{-- LEFT PANEL: PROFIL & BERKAS PERSYARATAN (60%) --}}
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            {{-- Biodata Calon Peserta --}}
            <div class="admin-card" style="padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0); padding-bottom: 0.85rem;">
                    <div style="display: flex; align-items: center; gap: 0.6rem;">
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: #EFF6FF; color: #1D4ED8; display: flex; align-items: center; justify-content: center;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        </div>
                        <h3 style="font-size: 1rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0;">
                            Biodata & Identitas Calon Peserta
                        </h3>
                    </div>
                    <span style="font-size: 0.75rem; font-weight: 700; color: #1D4ED8; background: #EFF6FF; padding: 0.2rem 0.6rem; border-radius: 9999px;">
                        {{ strtoupper($profil?->jenjang?->value ?? 'PESERTA') }}
                    </span>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.25rem; font-size: 0.875rem;">
                    <div>
                        <span style="font-size: 0.75rem; color: var(--neutral-500, #64748B); text-transform: uppercase; font-weight: 700;">Nama Lengkap</span>
                        <p style="font-weight: 700; color: var(--neutral-900, #0F172A); margin: 0.25rem 0 0 0;">{{ $user?->nama }}</p>
                    </div>
                    <div>
                        <span style="font-size: 0.75rem; color: var(--neutral-500, #64748B); text-transform: uppercase; font-weight: 700;">Email & Kontak</span>
                        <p style="font-weight: 600; color: var(--neutral-900, #0F172A); margin: 0.25rem 0 0 0;">{{ $user?->email }}</p>
                        <small style="color: var(--neutral-500, #64748B);">{{ $profil?->no_hp ?? '-' }}</small>
                    </div>
                    <div>
                        <span style="font-size: 0.75rem; color: var(--neutral-500, #64748B); text-transform: uppercase; font-weight: 700;">Asal Instansi</span>
                        <p style="font-weight: 700; color: var(--neutral-900, #0F172A); margin: 0.25rem 0 0 0;">{{ $profil?->institusi ?? '-' }}</p>
                        <small style="color: var(--neutral-500, #64748B);">NIM/NISN: {{ $profil?->nim_nisn ?? '-' }}</small>
                    </div>
                    <div>
                        <span style="font-size: 0.75rem; color: var(--neutral-500, #64748B); text-transform: uppercase; font-weight: 700;">Kejuruan Pilihan</span>
                        <p style="font-weight: 700; color: var(--neutral-900, #0F172A); margin: 0.25rem 0 0 0;">
                            {{ $pendaftaran->kuota?->jurusan?->nama_jurusan ?? ($profil?->jurusan?->nama_jurusan ?? '-') }}
                        </p>
                    </div>
                    <div>
                        <span style="font-size: 0.75rem; color: var(--neutral-500, #64748B); text-transform: uppercase; font-weight: 700;">Periode & Durasi</span>
                        <p style="font-weight: 700; color: #0641CC; margin: 0.25rem 0 0 0;">
                            {{ $pendaftaran->bulanMagang?->bulan_mulai?->translatedFormat('F Y') }} ({{ $pendaftaran->durasi_bulan }} Bulan)
                        </p>
                    </div>
                </div>
            </div>

            {{-- Berkas Persyaratan Terunggah --}}
            <div class="admin-card" style="padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0); padding-bottom: 0.85rem;">
                    <div style="display: flex; align-items: center; gap: 0.6rem;">
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: #EFF6FF; color: #1D4ED8; display: flex; align-items: center; justify-content: center;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                        </div>
                        <h3 style="font-size: 1rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0;">
                            Dokumen Persyaratan Calon Peserta
                        </h3>
                    </div>
                    <span style="font-size: 0.8125rem; font-weight: 700; color: #1D4ED8; background: #EFF6FF; padding: 0.25rem 0.7rem; border-radius: 9999px;">
                        Total: {{ $pendaftaran->dokumen->count() }} Dokumen
                    </span>
                </div>

                @if($pendaftaran->dokumen->isEmpty())
                    <div style="text-align: center; padding: 2.5rem 1rem; color: var(--neutral-500, #64748B); background: #F8FAFC; border-radius: 10px; border: 1px dashed var(--neutral-300, #CBD5E1);">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin: 0 auto 0.5rem auto; color: var(--neutral-400, #94A3B8);"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        <p style="margin: 0; font-size: 0.875rem; font-weight: 600;">Calon peserta belum mengunggah dokumen persyaratan.</p>
                    </div>
                @else
                    <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                        @foreach($pendaftaran->dokumen as $dok)
                            @php
                                $isPdf = str_contains(strtolower($dok->mime ?? ''), 'pdf') || str_ends_with(strtolower($dok->nama_asli ?? ''), '.pdf');
                            @endphp
                            <div class="doc-item-card">
                                <div style="display: flex; align-items: center; gap: 0.85rem;">
                                    <div style="width: 42px; height: 42px; background: {{ $isPdf ? '#FEE2E2' : '#EFF6FF' }}; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: {{ $isPdf ? '#DC2626' : '#1D4ED8' }}; flex-shrink: 0;">
                                        @if($isPdf)
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                        @else
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                        @endif
                                    </div>
                                    <div>
                                        <div style="font-weight: 700; font-size: 0.875rem; color: var(--neutral-900, #0F172A);">
                                            {{ $dok->jenisDokumen?->nama ?? 'Dokumen Pendukung' }}
                                        </div>
                                        <div style="font-size: 0.75rem; color: var(--neutral-500, #64748B); margin-top: 0.15rem;">
                                            {{ $dok->nama_asli }} &bull; {{ number_format($dok->ukuran / 1024, 1) }} KB
                                        </div>
                                    </div>
                                </div>

                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <button type="button" onclick="openDocPreview('{{ $dok->getSignedPreviewUrl(5) }}', '{{ $dok->getSignedDownloadUrl(5) }}', '{{ addslashes($dok->jenisDokumen?->nama ?? 'Dokumen Persyaratan') }} - {{ addslashes($dok->nama_asli) }}', '{{ $dok->mime }}')" style="display: inline-flex; align-items: center; gap: 0.4rem; background: #0641CC; color: #FFFFFF; border: none; padding: 0.5rem 0.9rem; border-radius: 8px; font-weight: 600; font-size: 0.8125rem; cursor: pointer; transition: all 0.15s ease; box-shadow: 0 1px 3px rgba(6, 65, 204, 0.2);" title="Tinjau berkas langsung di dalam website tanpa mengunduh" onmouseover="this.style.background='#0533A3'" onmouseout="this.style.background='#0641CC'">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                        <span>Tinjau Berkas</span>
                                    </button>
                                    <a href="{{ $dok->getSignedDownloadUrl(5) }}" style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; background: #ffffff; border: 1px solid var(--neutral-300, #CBD5E1); color: var(--neutral-700, #334155); border-radius: 8px; transition: all 0.15s ease;" title="Unduh berkas asli" onmouseover="this.style.borderColor='#0641CC'; this.style.color='#0641CC'" onmouseout="this.style.borderColor='var(--neutral-300, #CBD5E1)'; this.style.color='var(--neutral-700, #334155)'">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- RIGHT PANEL: FORMULIR KEPUTUSAN VERIFIKASI (40%) --}}
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <div class="admin-card" style="padding: 1.5rem;">
                <h3 style="font-size: 1.0625rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0 0 0.4rem 0;">
                    Keputusan Verifikasi Berkas
                </h3>
                <p style="font-size: 0.8125rem; color: var(--neutral-500, #64748B); margin: 0 0 1.25rem 0;">
                    Tentukan kelayakan berkas calon peserta sesuai kriteria penerimaan Dinas Kominfo.
                </p>

                @if($isFinal)
                    <div style="padding: 1.25rem; border-radius: 10px; font-size: 0.875rem;
                        background: {{ $statusValue === 'diterima' ? '#D1FAE5' : '#F8FAFC' }};
                        border: 1px solid {{ $statusValue === 'diterima' ? '#A7F3D0' : '#E2E8F0' }};">
                        <strong style="color: {{ $statusValue === 'diterima' ? '#065F46' : '#1E293B' }};">
                            Pengajuan Telah Selesai Diputuskan
                        </strong>
                        <p style="margin: 0.4rem 0 0 0; color: var(--neutral-700, #334155); line-height: 1.5;">
                            Status pengajuan saat ini adalah <strong>{{ $status->label() }}</strong> pada {{ $pendaftaran->diputuskan_at?->translatedFormat('d F Y, H:i') }}.
                            @if($pendaftaran->catatan)
                                <br><span style="color: var(--neutral-600, #475569); display: inline-block; margin-top: 0.35rem;">Catatan Verifikator: <em>"{{ $pendaftaran->catatan }}"</em></span>
                            @endif
                        </p>
                    </div>
                @else
                    {{-- Indikator Kuota Real-time --}}
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1.25rem; font-size: 0.8125rem; font-weight: 700;
                        background: {{ $sisaKuota > 0 ? '#ECFDF5' : '#FEF2F2' }};
                        border: 1px solid {{ $sisaKuota > 0 ? '#A7F3D0' : '#FECACA' }};
                        color: {{ $sisaKuota > 0 ? '#065F46' : '#991B1B' }};">
                        <span>Status Kuota Jurusan:</span>
                        @if($sisaKuota > 0)
                            <span style="display: inline-flex; align-items: center; gap: 0.35rem;">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background: #10B981;"></span>
                                Tersedia ({{ $sisaKuota }} Kursi Sisa)
                            </span>
                        @else
                            <span style="display: inline-flex; align-items: center; gap: 0.35rem; color: #DC2626;">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background: #EF4444;"></span>
                                Kuota Penuh (Masuk Waiting List)
                            </span>
                        @endif
                    </div>

                    {{-- Tab Switcher Aksi Keputusan --}}
                    <div style="display: flex; gap: 0.4rem; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0); padding-bottom: 0.75rem; flex-wrap: wrap;">
                        <button type="button" id="tab-btn-terima" onclick="switchActionTab('terima')" class="btn-tab-action" style="background: #10B981; color: #FFFFFF;">
                            Terima
                        </button>

                        @if($sisaKuota <= 0)
                            <button type="button" id="tab-btn-waiting_list" onclick="switchActionTab('waiting_list')" class="btn-tab-action" style="background: var(--neutral-100, #F1F5F9); color: var(--neutral-700, #334155);">
                                Waiting List
                            </button>
                        @else
                            <button type="button" disabled title="Opsi Waiting List hanya tersedia jika kuota umum telah penuh (AC-04-4)" class="btn-tab-action" style="background: #F1F5F9; color: #94A3B8; cursor: not-allowed; opacity: 0.6;">
                                Waiting List
                            </button>
                        @endif

                        <button type="button" id="tab-btn-revisi" onclick="switchActionTab('perlu_perbaikan')" class="btn-tab-action" style="background: var(--neutral-100, #F1F5F9); color: var(--neutral-700, #334155);">
                            Minta Revisi
                        </button>

                        <button type="button" id="tab-btn-tolak" onclick="switchActionTab('tolak')" class="btn-tab-action" style="background: var(--neutral-100, #F1F5F9); color: var(--neutral-700, #334155);">
                            Tolak
                        </button>
                    </div>

                    {{-- Form Keputusan --}}
                    <form method="POST" action="{{ route('admin.pendaftaran.putuskan', $pendaftaran) }}" id="decision-form">
                        @csrf
                        <input type="hidden" name="keputusan" id="keputusan-input" value="terima">

                        {{-- Tab 1: TERIMA --}}
                        <div id="action-panel-terima" style="display: flex; flex-direction: column; gap: 1rem;">
                            <div style="background: #EFF6FF; border: 1px solid #DBEAFE; border-radius: 8px; padding: 0.85rem 1rem; font-size: 0.8125rem; color: #1E40AF; line-height: 1.4;">
                                Menerima pengajuan akan mengurangi kuota umum jurusan secara atomik dan membentuk penempatan resmi magang.
                            </div>

                            <div style="display: flex; flex-direction: column; gap: 0.375rem;">
                                <label for="pembimbing_id" style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">
                                    Tetapkan Pembimbing Lapangan <span style="color: #DC2626;">*</span>
                                </label>
                                <select id="pembimbing_id" name="pembimbing_id" style="height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff; color: var(--neutral-900, #0F172A);">
                                    <option value="">-- Pilih Pembimbing Lapangan --</option>
                                    @foreach($pembimbingList as $p)
                                        <option value="{{ $p->id }}">{{ $p->nama }} ({{ $p->email }}) &bull; {{ $p->mentee_aktif_count ?? 0 }} Mentee Aktif</option>
                                    @endforeach
                                </select>
                            </div>

                            <div style="display: flex; flex-direction: column; gap: 0.375rem;">
                                <label for="bidang_id" style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">
                                    Penempatan Bidang Dinas (Opsional)
                                </label>
                                <select id="bidang_id" name="bidang_id" style="height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff; color: var(--neutral-900, #0F172A);">
                                    <option value="">-- Belum Ditentukan / Sesuai Pembimbing --</option>
                                    @foreach($bidangList as $b)
                                        <option value="{{ $b->id }}">{{ $b->nama_bidang }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" onclick="return confirm('Apakah Anda yakin menerima calon peserta ini dan menetapkan penempatan magang?');" style="height: 44px; margin-top: 0.5rem; background: #0641CC; color: #FFFFFF; font-weight: 700; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer; transition: background 0.15s ease; box-shadow: 0 1px 3px rgba(6, 65, 204, 0.25);" onmouseover="this.style.background='#0533A3'" onmouseout="this.style.background='#0641CC'">
                                Terima & Tempatkan Peserta
                            </button>
                        </div>

                        {{-- Tab 2: WAITING LIST --}}
                        <div id="action-panel-waiting_list" style="display: none; flex-direction: column; gap: 1rem;">
                            <div style="background: #EFF6FF; border: 1px solid #DBEAFE; border-radius: 8px; padding: 0.85rem 1rem; font-size: 0.8125rem; color: #1E40AF; line-height: 1.4;">
                                Karena kuota umum telah penuh, berkas calon peserta dinyatakan lolos dan dialihkan ke antrean Waiting List terurut berdasarkan waktu pengajuan (FIFO).
                            </div>

                            <div style="display: flex; flex-direction: column; gap: 0.375rem;">
                                <label for="catatan_waiting_list" style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">
                                    Catatan Antrean Waiting List (Opsional)
                                </label>
                                <textarea id="catatan_waiting_list" name="catatan" rows="3" placeholder="Tambahkan catatan khusus untuk antrean waiting list jika diperlukan..." style="border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0.75rem; font-size: 0.875rem; font-family: inherit; color: var(--neutral-900, #0F172A);"></textarea>
                            </div>

                            <button type="submit" onclick="return confirm('Alihkan pendaftaran ini ke antrean Waiting List?');" style="height: 44px; margin-top: 0.5rem; background: #0641CC; color: #FFFFFF; font-weight: 700; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer;">
                                Masukkan ke Waiting List
                            </button>
                        </div>

                        {{-- Tab 3: PERLU PERBAIKAN --}}
                        <div id="action-panel-perlu_perbaikan" style="display: none; flex-direction: column; gap: 1rem;">
                            <div style="background: #FFFBEB; border: 1px solid #FCD34D; border-radius: 8px; padding: 0.85rem 1rem; font-size: 0.8125rem; color: #92400E; line-height: 1.4;">
                                Status pengajuan akan dikembalikan ke calon peserta untuk perbaikan berkas sesuai catatan di bawah.
                            </div>

                            <div style="display: flex; flex-direction: column; gap: 0.375rem;">
                                <label for="catatan_revisi" style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">
                                    Catatan / Instruksi Perbaikan (Min. 10 Karakter) <span style="color: #DC2626;">*</span>
                                </label>
                                <textarea id="catatan_revisi" name="catatan" rows="4" placeholder="Sebutkan berkas yang perlu diperbaiki dan alasannya secara spesifik (minimal 10 karakter)..." style="border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0.75rem; font-size: 0.875rem; font-family: inherit; color: var(--neutral-900, #0F172A);"></textarea>
                            </div>

                            <button type="submit" onclick="return confirm('Kirimkan permintaan perbaikan berkas kepada calon peserta?');" style="height: 44px; margin-top: 0.5rem; background: #D97706; color: #FFFFFF; font-weight: 700; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer;">
                                Minta Perbaikan Berkas
                            </button>
                        </div>

                        {{-- Tab 4: TOLAK --}}
                        <div id="action-panel-tolak" style="display: none; flex-direction: column; gap: 1rem;">
                            <div style="background: #FEF2F2; border: 1px solid #FECACA; border-radius: 8px; padding: 0.85rem 1rem; font-size: 0.8125rem; color: #991B1B; line-height: 1.4;">
                                Penolakan bersifat final. Kuota tidak terpakai dan calon peserta akan melihat alasan penolakan pada dashboard mereka.
                            </div>

                            <div style="display: flex; flex-direction: column; gap: 0.375rem;">
                                <label for="catatan_tolak" style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">
                                    Alasan Penolakan (Min. 10 Karakter) <span style="color: #DC2626;">*</span>
                                </label>
                                <textarea id="catatan_tolak" name="catatan" rows="4" placeholder="Tuliskan alasan penolakan pengajuan (minimal 10 karakter)..." style="border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0.75rem; font-size: 0.875rem; font-family: inherit; color: var(--neutral-900, #0F172A);"></textarea>
                            </div>

                            <button type="submit" onclick="return confirm('Apakah Anda yakin MENOLAK pengajuan pendaftaran ini?');" style="height: 44px; margin-top: 0.5rem; background: #DC2626; color: #FFFFFF; font-weight: 700; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer;">
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
                                    if (p === 'terima') btn.style.background = '#10B981';
                                    else if (p === 'waiting_list') btn.style.background = '#0641CC';
                                    else if (p === 'perlu_perbaikan') btn.style.background = '#D97706';
                                    else btn.style.background = '#DC2626';
                                    btn.style.color = '#FFFFFF';
                                } else {
                                    panel.style.display = 'none';
                                    btn.style.background = 'var(--neutral-100, #F1F5F9)';
                                    btn.style.color = 'var(--neutral-700, #334155)';
                                }
                            });

                            // Toggle required attributes
                            const pembimbingSelect = document.getElementById('pembimbing_id');
                            if (pembimbingSelect) pembimbingSelect.required = (action === 'terima');
                            const catatanRevisi = document.getElementById('catatan_revisi');
                            const catatanTolak = document.getElementById('catatan_tolak');
                            if (catatanRevisi) catatanRevisi.required = (action === 'perlu_perbaikan');
                            if (catatanTolak) catatanTolak.required = (action === 'tolak');
                        }
                    </script>
                @endif
            </div>
        </div>

    </div>

</div>

{{-- Modal In-App Pratinjau Dokumen (Verifikator Admin) --}}
<div class="doc-modal" id="docPreviewModal" onclick="if(event.target === this) closeDocPreview()" role="dialog" aria-modal="true" aria-labelledby="previewModalTitle">
    <div class="doc-modal-box">
        <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 1rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0);">
            <div style="display: flex; align-items: center; gap: 0.75rem; overflow: hidden; padding-right: 0.75rem;">
                <div id="previewModalIcon" style="width: 38px; height: 38px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background: #EFF6FF; color: #1D4ED8;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                </div>
                <div style="overflow: hidden;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <h3 id="previewModalTitle" style="font-size: 1rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
                            Pratinjau Dokumen
                        </h3>
                        <span id="previewModalBadge" class="role-chip-badge" style="font-size: 0.6875rem; padding: 2px 8px; background: #FEE2E2; color: #991B1B; font-weight: 700;">PDF</span>
                    </div>
                    <p style="font-size: 0.75rem; color: var(--neutral-500, #64748B); margin: 2px 0 0 0;">
                        Tinjauan berkas pendaftaran calon peserta &bull; Streaming terproteksi Disk Privat
                    </p>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 0.5rem; flex-shrink: 0;">
                <a id="previewOpenNewTabBtn" href="#" target="_blank" rel="noopener noreferrer" style="display: inline-flex; align-items: center; gap: 0.375rem; background: #ffffff; border: 1px solid var(--neutral-300, #CBD5E1); color: var(--neutral-700, #334155); padding: 0.45rem 0.85rem; border-radius: 8px; font-weight: 600; font-size: 0.78rem; text-decoration: none;" title="Buka pratinjau di tab browser baru">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                        <polyline points="15 3 21 3 21 9"></polyline>
                        <line x1="10" y1="14" x2="21" y2="3"></line>
                    </svg>
                    <span>Tab Baru</span>
                </a>
                <a id="previewDownloadBtn" href="#" style="display: inline-flex; align-items: center; gap: 0.375rem; background: #ffffff; border: 1px solid var(--neutral-300, #CBD5E1); color: var(--neutral-700, #334155); padding: 0.45rem 0.85rem; border-radius: 8px; font-weight: 600; font-size: 0.78rem; text-decoration: none;" title="Unduh berkas fisik">
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
            {{-- Loading Indicator / Skeleton --}}
            <div id="previewLoading" style="position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; background: rgba(255, 255, 255, 0.95); z-index: 10;">
                <div style="width: 38px; height: 38px; border: 3px solid var(--neutral-200, #E2E8F0); border-top-color: #0641CC; border-radius: 50%; animation: spin 0.7s linear infinite; margin-bottom: 12px;"></div>
                <div style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-900, #0F172A);">Memuat Pratinjau Dokumen...</div>
                <div style="font-size: 0.75rem; color: var(--neutral-500, #64748B); margin-top: 4px;">Mengambil stream berkas aman dari disk privat</div>
            </div>

            {{-- PDF Iframe Container --}}
            <iframe id="previewIframe" src="about:blank" style="width: 100%; height: 75vh; border: none; display: none;"></iframe>

            {{-- Image Container (JPG/PNG) --}}
            <div id="previewImageWrapper" style="display: none; padding: 1.5rem; width: 100%; height: 100%; text-align: center; overflow: auto; justify-content: center; align-items: center;">
                <img id="previewImage" alt="Pratinjau Berkas" style="max-width: 100%; max-height: 70vh; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.12); object-fit: contain;">
            </div>

            {{-- Error State Container --}}
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

<style>
    .doc-modal {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(6px);
        align-items: center;
        justify-content: center;
        padding: 1.25rem;
    }
    .doc-modal.open {
        display: flex;
    }
    .doc-modal-box {
        background: #ffffff;
        border-radius: 16px;
        width: 100%;
        max-width: 980px;
        max-height: 92vh;
        display: flex;
        flex-direction: column;
        padding: 1.25rem 1.5rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        overflow: hidden;
        border: 1px solid var(--neutral-200, #E2E8F0);
    }
    .doc-modal-body {
        flex: 1;
        min-height: 60vh;
        max-height: 75vh;
        position: relative;
        background: #F8FAFC;
        border: 1px solid var(--neutral-200, #E2E8F0);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        margin-top: 0.875rem;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>

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
            }, 500);
        } else {
            modalBadge.textContent = 'Gambar';
            modalBadge.style.background = '#EFF6FF';
            modalBadge.style.color = '#1D4ED8';
            if (iconContainer) {
                iconContainer.style.background = '#EFF6FF';
                iconContainer.style.color = '#1D4ED8';
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
