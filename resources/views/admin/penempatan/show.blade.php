@extends('layouts.dashboard')

@section('title', 'Detail Penempatan ' . ($penempatan->peserta?->nama ?? 'Peserta') . ' - Admin MagKom')
@section('page_title', 'Detail Penempatan Peserta')
@section('page_description', 'Rincian data alokasi penempatan peserta, pembimbing lapangan, rekapitulasi progres magang, dan dokumen resmi.')

@section('breadcrumbs')
    <nav style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; color: var(--neutral-600, #64748B);">
        <span>Portal MagKom</span>
        <span>/</span>
        <a href="{{ route('admin.penempatan.index') }}" style="color: var(--neutral-600, #64748B); text-decoration: none;">Penempatan Peserta</a>
        <span>/</span>
        <span style="color: var(--neutral-900, #0F172A); font-weight: 700;">{{ $penempatan->peserta?->nama ?? 'Detail' }}</span>
    </nav>
@endsection

@section('page_actions')
    <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
        <a href="{{ route('admin.penempatan.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; background: #ffffff; border: 1px solid var(--neutral-300, #CBD5E1); color: var(--neutral-700, #334155); padding: 0.55rem 1.1rem; border-radius: 8px; font-weight: 600; font-size: 0.8125rem; text-decoration: none; transition: all 0.15s ease;" onmouseover="this.style.borderColor='#0641CC'; this.style.color='#0641CC'" onmouseout="this.style.borderColor='var(--neutral-300, #CBD5E1)'; this.style.color='var(--neutral-700, #334155)'">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <span>Kembali ke Daftar</span>
        </a>
        <a href="{{ route('admin.penempatan.surat-diterima', $penempatan) }}" style="display: inline-flex; align-items: center; gap: 0.5rem; background: #0641CC; color: #ffffff; padding: 0.55rem 1.1rem; border-radius: 8px; font-weight: 600; font-size: 0.8125rem; text-decoration: none; transition: all 0.15s ease; box-shadow: 0 1px 3px rgba(6, 65, 204, 0.2);">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            <span>Unduh Surat Diterima PDF</span>
        </a>
    </div>
@endsection

@section('content')
@php
    $peserta = $penempatan->peserta;
    $profil = $peserta?->profil;
    $pembimbing = $penempatan->pembimbing;
    $statusObj = $penempatan->status;
    $statusVal = $statusObj instanceof \App\Enums\StatusPenempatanEnum ? $statusObj->value : (string) $statusObj;

    $badgeStyle = match($statusVal) {
        'aktif' => ['bg' => '#ECFDF5', 'color' => '#065F46', 'border' => '#A7F3D0', 'dot' => '#10B981'],
        'selesai' => ['bg' => '#EFF6FF', 'color' => '#1E40AF', 'border' => '#BFDBFE', 'dot' => '#3B82F6'],
        'batal' => ['bg' => '#FEF2F2', 'color' => '#991B1B', 'border' => '#FECACA', 'dot' => '#EF4444'],
        default => ['bg' => '#F1F5F9', 'color' => '#475569', 'border' => '#CBD5E1', 'dot' => '#94A3B8'],
    };

    $noSuratResmi = $penempatan->no_surat ?? ('500.12/' . str_pad((string) $penempatan->id, 4, '0', STR_PAD_LEFT) . '/DISKOMINFO/' . date('Y'));
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
        .stat-badge-summary {
            padding: 1.25rem;
            border-radius: 12px;
            border: 1px solid var(--neutral-200, #E2E8F0);
            background: #ffffff;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .field-label {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--neutral-500, #64748B);
            margin-bottom: 0.25rem;
        }
        .field-value {
            font-size: 0.9375rem;
            font-weight: 600;
            color: var(--neutral-900, #0F172A);
        }
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1050;
            padding: 1rem;
        }
        .modal-container {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            width: 100%;
            overflow: hidden;
            border: 1px solid var(--neutral-200, #E2E8F0);
        }

        /* =====================================================================
           DARK MODE OVERRIDES (DS-MagKom Standards)
           ===================================================================== */
        [data-theme="dark"] .admin-card {
            background: #182234 !important;
            border-color: #26354D !important;
        }

        [data-theme="dark"] .admin-card[style*="linear-gradient"] {
            background: linear-gradient(135deg, #182234 0%, #111C2E 100%) !important;
        }

        [data-theme="dark"] .admin-card div[style*="background: #F8FAFC"] {
            background: #141E30 !important;
            border-bottom-color: #26354D !important;
        }

        [data-theme="dark"] .stat-badge-summary {
            background: #182234 !important;
            border-color: #26354D !important;
        }

        [data-theme="dark"] .field-label {
            color: #94A3B8 !important;
        }

        [data-theme="dark"] .field-value {
            color: #F8FAFC !important;
        }

        [data-theme="dark"] h2[style*="color: var(--neutral-900"],
        [data-theme="dark"] h3[style*="color: var(--neutral-900"],
        [data-theme="dark"] h4[style*="color: var(--neutral-900"] {
            color: #F8FAFC !important;
        }

        [data-theme="dark"] div[style*="color: var(--neutral-900"] {
            color: #F8FAFC !important;
        }

        [data-theme="dark"] div[style*="color: var(--neutral-800"] {
            color: #CBD5E1 !important;
        }

        [data-theme="dark"] div[style*="color: var(--neutral-600"],
        [data-theme="dark"] span[style*="color: var(--neutral-500"] {
            color: #94A3B8 !important;
        }

        [data-theme="dark"] a[style*="background: #ffffff"] {
            background: #111C2E !important;
            border-color: #26354D !important;
            color: #CBD5E1 !important;
        }

        [data-theme="dark"] a[style*="background: #ffffff"]:hover {
            background: #162238 !important;
            border-color: #3B82F6 !important;
            color: #60A5FA !important;
        }

        [data-theme="dark"] div[style*="background: #F8FAFC; border: 1px solid var(--neutral-200"] {
            background: #0F172A !important;
            border-color: #26354D !important;
        }

        [data-theme="dark"] .modal-container {
            background: #182234 !important;
            border-color: #26354D !important;
            color: #F8FAFC !important;
        }

        [data-theme="dark"] .modal-container div[style*="background: #F8FAFC"] {
            background: #141E30 !important;
            border-bottom-color: #26354D !important;
        }

        [data-theme="dark"] .modal-container select,
        [data-theme="dark"] .modal-container input,
        [data-theme="dark"] .modal-container textarea {
            background: #0F172A !important;
            border-color: #26354D !important;
            color: #F8FAFC !important;
        }

        [data-theme="dark"] .modal-container select option {
            background: #0F172A !important;
            color: #F8FAFC !important;
        }

        [data-theme="dark"] .modal-container label {
            color: #CBD5E1 !important;
        }

        [data-theme="dark"] .modal-container div[style*="border-top: 1px solid"] {
            border-top-color: #26354D !important;
        }

        [data-theme="dark"] .modal-container button[style*="background: #ffffff"],
        [data-theme="dark"] .modal-container button[style*="background: var(--neutral-0"] {
            background: #111C2E !important;
            border-color: #26354D !important;
            color: #CBD5E1 !important;
        }
    </style>



    {{-- TOP HERO CARD --}}
    <div class="admin-card" style="padding: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; background: linear-gradient(135deg, #F8FAFC 0%, #EFF6FF 100%);">
        <div style="display: flex; align-items: center; gap: 1.25rem; flex-wrap: wrap;">
            <div style="width: 64px; height: 64px; border-radius: 50%; background: #0641CC; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 800; border: 3px solid #DBEAFE; flex-shrink: 0;">
                @if($profil?->foto_url)
                    <img src="{{ $profil->foto_url }}" alt="{{ $peserta?->nama }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                @else
                    {{ strtoupper(substr($peserta?->nama ?? 'P', 0, 2)) }}
                @endif
            </div>
            <div>
                <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 0.25rem;">
                    <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0;">
                        {{ $peserta?->nama ?? '-' }}
                    </h2>
                    <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.2rem 0.65rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: {{ $badgeStyle['bg'] }}; color: {{ $badgeStyle['color'] }}; border: 1px solid {{ $badgeStyle['border'] }};">
                        <span style="width: 6px; height: 6px; border-radius: 50%; background: {{ $badgeStyle['dot'] }};"></span>
                        {{ $statusObj ? $statusObj->label() : ucfirst($statusVal) }}
                    </span>
                    @if($penempatan->slotKhusus)
                        <span style="font-size: 0.7rem; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 4px; background: #FEF3C7; color: #B45309; border: 1px solid #FCD34D;">
                            Jalur Khusus: {{ $penempatan->slotKhusus->nama_mitra }}
                        </span>
                    @else
                        <span style="font-size: 0.7rem; font-weight: 600; padding: 0.2rem 0.5rem; border-radius: 4px; background: #F1F5F9; color: #475569; border: 1px solid #CBD5E1;">
                            Jalur Reguler
                        </span>
                    @endif
                </div>
                <div style="font-size: 0.875rem; color: var(--neutral-600, #64748B); display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
                    <span><strong>NIM/NISN:</strong> {{ $profil?->nim_nisn ?? '-' }}</span>
                    <span>•</span>
                    <span><strong>Institusi:</strong> {{ $profil?->institusi ?? '-' }}</span>
                    <span>•</span>
                    <span><strong>No. Surat:</strong> {{ $noSuratResmi }}</span>
                </div>
            </div>
        </div>

        {{-- Action Buttons Toolbar --}}
        <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
            <a href="{{ route('admin.penempatan.surat-diterima.pratinjau', $penempatan) }}" target="_blank" style="padding: 0.5rem 0.9rem; background: #ffffff; color: #1D4ED8; border: 1px solid #BFDBFE; border-radius: 8px; font-size: 0.8125rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                <span>Pratinjau Surat</span>
            </a>

            @if($statusVal === 'aktif')
                <button type="button" onclick="openPindahModal()" style="padding: 0.5rem 0.9rem; background: #EFF6FF; color: #0641CC; border: 1px solid #DBEAFE; border-radius: 8px; font-size: 0.8125rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><polyline points="17 11 19 13 23 9"></polyline></svg>
                    <span>Pindah Pembimbing</span>
                </button>

                <button type="button" onclick="openBatalModal()" style="padding: 0.5rem 0.9rem; background: #FEF2F2; color: #991B1B; border: 1px solid #FECACA; border-radius: 8px; font-size: 0.8125rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    <span>Batalkan Penempatan</span>
                </button>
            @endif
        </div>
    </div>

    {{-- QUICK STATS PROGRESS ROW --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
        <div class="stat-badge-summary">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #EFF6FF; color: #0641CC; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"></path><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
            </div>
            <div>
                <div style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-500, #64748B); text-transform: uppercase;">Tugas Selesai</div>
                <div style="font-size: 1.25rem; font-weight: 800; color: var(--neutral-900, #0F172A);">
                    {{ $tugasSelesaiCount }} <span style="font-size: 0.875rem; font-weight: 600; color: var(--neutral-500, #64748B);">/ {{ $tugasCount }} tugas</span>
                </div>
            </div>
        </div>

        <div class="stat-badge-summary">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #ECFDF5; color: #065F46; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            </div>
            <div>
                <div style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-500, #64748B); text-transform: uppercase;">Total Logbook Terisi</div>
                <div style="font-size: 1.25rem; font-weight: 800; color: var(--neutral-900, #0F172A);">
                    {{ $logbookCount }} <span style="font-size: 0.875rem; font-weight: 600; color: var(--neutral-500, #64748B);">minggu</span>
                </div>
            </div>
        </div>

        <div class="stat-badge-summary">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #FFFBEB; color: #B45309; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
            </div>
            <div>
                <div style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-500, #64748B); text-transform: uppercase;">Nilai Akhir Magang</div>
                <div style="font-size: 1.25rem; font-weight: 800; color: var(--neutral-900, #0F172A);">
                    @if($nilaiAkhir !== null)
                        {{ number_format((float) $nilaiAkhir, 2) }}
                        <span style="font-size: 0.8125rem; font-weight: 700; color: #16A34A;">({{ $penempatan->penilaian?->predikat ?? 'Lulus' }})</span>
                    @else
                        <span style="font-size: 0.875rem; font-weight: 600; color: var(--neutral-400, #94A3B8);">Belum Dinilai</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="stat-badge-summary">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #F3E8FF; color: #7E22CE; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            </div>
            <div>
                <div style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-500, #64748B); text-transform: uppercase;">Durasi & Periode</div>
                <div style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900, #0F172A);">
                    {{ $penempatan->total_minggu }} <span style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-500, #64748B);">Minggu Magang</span>
                </div>
            </div>
        </div>
    </div>

    {{-- 2 COLUMNS GRID: INFORMASI DETAIL --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 1.5rem;">
        
        {{-- Card 1: Data Penempatan & Surat --}}
        <div class="admin-card">
            <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0); background: #F8FAFC;">
                <h3 style="margin: 0; font-size: 1rem; font-weight: 800; color: var(--neutral-900, #0F172A); display: flex; align-items: center; gap: 0.5rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                    <span>Informasi Penempatan & Surat Resmi</span>
                </h3>
            </div>
            <div style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
                <div>
                    <div class="field-label">Nomor Surat Keputusan</div>
                    <div class="field-value" style="font-family: ui-monospace, monospace; color: #0641CC;">
                        {{ $noSuratResmi }}
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <div class="field-label">Bidang Penempatan</div>
                        <div class="field-value">{{ $penempatan->bidang?->nama_bidang ?? 'Dinas Kominfo Sleman' }}</div>
                    </div>
                    <div>
                        <div class="field-label">Jalur Pendaftaran</div>
                        <div class="field-value">
                            @if($penempatan->slotKhusus)
                                <span style="color: #B45309;">Khusus ({{ $penempatan->slotKhusus->nama_mitra }})</span>
                            @else
                                <span>Reguler (Kuota Umum)</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <div class="field-label">Tanggal Mulai</div>
                        <div class="field-value">
                            {{ $penempatan->tgl_mulai ? \Carbon\Carbon::parse($penempatan->tgl_mulai)->translatedFormat('d F Y') : '-' }}
                        </div>
                    </div>
                    <div>
                        <div class="field-label">Tanggal Selesai</div>
                        <div class="field-value">
                            {{ $penempatan->tgl_selesai ? \Carbon\Carbon::parse($penempatan->tgl_selesai)->translatedFormat('d F Y') : '-' }}
                        </div>
                    </div>
                </div>

                <div>
                    <div class="field-label">Jurusan Terdaftar</div>
                    <div class="field-value">
                        {{ $penempatan->kuota?->jurusan?->nama_jurusan ?? ($profil?->jurusan?->nama_jurusan ?? '-') }}
                    </div>
                </div>

                @if($penempatan->pendaftaran)
                    <div>
                        <div class="field-label">Terkait Berkas Pendaftaran</div>
                        <div class="field-value">
                            <a href="{{ route('admin.pendaftaran.show', $penempatan->pendaftaran) }}" style="color: #0641CC; text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; gap: 0.35rem;">
                                <span>{{ $penempatan->pendaftaran->nomor_pendaftaran }}</span>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Card 2: Pembimbing Lapangan --}}
        <div class="admin-card">
            <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0); background: #F8FAFC; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; font-size: 1rem; font-weight: 800; color: var(--neutral-900, #0F172A); display: flex; align-items: center; gap: 0.5rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    <span>Pembimbing Lapangan</span>
                </h3>
                @if($statusVal === 'aktif')
                    <button type="button" onclick="openPindahModal()" style="font-size: 0.75rem; font-weight: 700; color: #0641CC; background: #EFF6FF; border: 1px solid #DBEAFE; padding: 0.25rem 0.6rem; border-radius: 6px; cursor: pointer;">
                        Alihkan Pembimbing
                    </button>
                @endif
            </div>
            <div style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 52px; height: 52px; border-radius: 12px; background: #EFF6FF; color: #0641CC; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; font-weight: 800; border: 1px solid #BFDBFE;">
                        {{ strtoupper(substr($pembimbing?->nama ?? 'P', 0, 2)) }}
                    </div>
                    <div>
                        <div style="font-size: 1rem; font-weight: 800; color: var(--neutral-900, #0F172A);">
                            {{ $pembimbing?->nama ?? 'Belum Ditugaskan' }}
                        </div>
                        <div style="font-size: 0.8125rem; color: var(--neutral-600, #64748B);">
                            {{ $pembimbing?->email ?? '-' }}
                        </div>
                    </div>
                </div>

                <div style="background: #F8FAFC; border: 1px solid var(--neutral-200, #E2E8F0); border-radius: 10px; padding: 1rem; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-500, #64748B);">Beban Bimbingan Aktif</div>
                        <div style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-800, #1E293B);">
                            {{ $pembimbing?->penempatanBimbingan()->where('status', 'aktif')->count() ?? 0 }} mentee aktif
                        </div>
                    </div>
                    <span style="font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 6px; background: #ECFDF5; color: #065F46;">
                        Tersedia
                    </span>
                </div>

                <div>
                    <div class="field-label">Unit / Bidang Pembimbing</div>
                    <div class="field-value">{{ $pembimbing?->bidang?->nama_bidang ?? 'Dinas Komunikasi dan Informatika' }}</div>
                </div>
            </div>
        </div>

        {{-- Card 3: Profil Lengkap Peserta --}}
        <div class="admin-card">
            <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0); background: #F8FAFC;">
                <h3 style="margin: 0; font-size: 1rem; font-weight: 800; color: var(--neutral-900, #0F172A); display: flex; align-items: center; gap: 0.5rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
                    <span>Biodata & Kontak Peserta</span>
                </h3>
            </div>
            <div style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <div class="field-label">Email Akun</div>
                        <div class="field-value" style="font-size: 0.875rem;">{{ $peserta?->email ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="field-label">No. Telepon / WhatsApp</div>
                        <div class="field-value">{{ $profil?->no_hp ?? ($peserta?->no_telp ?? '-') }}</div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <div class="field-label">Jenjang Pendidikan</div>
                        <div class="field-value">{{ $profil?->jenjang?->label() ?? (strtoupper($profil?->jenjang ?? '-')) }}</div>
                    </div>
                    <div>
                        <div class="field-label">Asal Kampus / Sekolah</div>
                        <div class="field-value">{{ $profil?->institusi ?? '-' }}</div>
                    </div>
                </div>

                @if($profil?->nama_kontak_darurat)
                    <div style="background: #F8FAFC; border: 1px solid var(--neutral-200, #E2E8F0); border-radius: 10px; padding: 0.75rem 1rem;">
                        <div class="field-label" style="margin-bottom: 0.15rem;">Kontak Darurat (Emergency)</div>
                        <div style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-900, #0F172A);">
                            {{ $profil->nama_kontak_darurat }} ({{ $profil->hubungan_kontak_darurat ?? 'Keluarga' }})
                        </div>
                        <div style="font-size: 0.8125rem; color: var(--neutral-600, #64748B);">
                            {{ $profil->no_kontak_darurat ?? '-' }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Card 4: Dokumen & Rekapitulasi Berkas --}}
        <div class="admin-card">
            <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0); background: #F8FAFC;">
                <h3 style="margin: 0; font-size: 1rem; font-weight: 800; color: var(--neutral-900, #0F172A); display: flex; align-items: center; gap: 0.5rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    <span>Dokumen Administrasi</span>
                </h3>
            </div>
            <div style="padding: 1.5rem; display: flex; flex-direction: column; gap: 0.75rem;">
                {{-- Surat Diterima PDF --}}
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid #BFDBFE; background: #EFF6FF;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1D4ED8" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                        <div>
                            <div style="font-size: 0.8125rem; font-weight: 700; color: #1E40AF;">Surat Keterangan Diterima Magang</div>
                            <div style="font-size: 0.75rem; color: #3B82F6;">PDF Resmi Tanda Tangan Basah / Stempel</div>
                        </div>
                    </div>
                    <div style="display: flex; gap: 0.35rem;">
                        <a href="{{ route('admin.penempatan.surat-diterima.pratinjau', $penempatan) }}" target="_blank" style="padding: 0.35rem 0.65rem; background: #ffffff; color: #1D4ED8; border: 1px solid #BFDBFE; border-radius: 6px; font-size: 0.75rem; font-weight: 700; text-decoration: none;">
                            Lihat
                        </a>
                        <a href="{{ route('admin.penempatan.surat-diterima', $penempatan) }}" style="padding: 0.35rem 0.65rem; background: #0641CC; color: #ffffff; border-radius: 6px; font-size: 0.75rem; font-weight: 700; text-decoration: none;">
                            Unduh
                        </a>
                    </div>
                </div>

                {{-- Berkas Pendaftaran jika ada --}}
                @if($penempatan->pendaftaran && $penempatan->pendaftaran->dokumen->isNotEmpty())
                    @foreach($penempatan->pendaftaran->dokumen as $dok)
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.6rem 0.85rem; border-radius: 8px; border: 1px solid var(--neutral-200, #E2E8F0); background: #F8FAFC;">
                            <div style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800, #1E293B);">
                                {{ ucwords(str_replace('_', ' ', $dok->jenis_dokumen)) }}
                            </div>
                            <span style="font-size: 0.7rem; font-weight: 700; padding: 0.15rem 0.45rem; border-radius: 4px; background: #ECFDF5; color: #065F46;">
                                Terverifikasi
                            </span>
                        </div>
                    @endforeach
                @else
                    <div style="font-size: 0.8125rem; color: var(--neutral-500, #64748B); text-align: center; padding: 0.75rem;">
                        Tidak ada berkas unggahan tambahan.
                    </div>
                @endif
            </div>
        </div>

    </div>

</div>

{{-- MODAL PINDAH PEMBIMBING --}}
<div id="modal-pindah-pembimbing" class="modal-overlay">
    <div class="modal-container" style="max-width: 480px;">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0); display: flex; justify-content: space-between; align-items: center; background: #F8FAFC;">
            <div>
                <h3 style="font-size: 1.0625rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0;">Alihkan Pembimbing Lapangan</h3>
                <p style="font-size: 0.75rem; color: var(--neutral-500, #64748B); margin: 0.15rem 0 0 0;">
                    Pindahkan alokasi peserta ke staf pembimbing lainnya (AC-07-4)
                </p>
            </div>
            <button type="button" onclick="closePindahModal()" style="background: transparent; border: none; font-size: 1.25rem; color: var(--neutral-400, #94A3B8); cursor: pointer;">✕</button>
        </div>

        <form method="POST" action="{{ route('admin.penempatan.pindah-pembimbing', $penempatan) }}" style="padding: 1.5rem;">
            @csrf
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B); margin-bottom: 0.35rem;">
                    Pilih Pembimbing Baru <span style="color: #DC2626;">*</span>
                </label>
                <select name="pembimbing_id" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                    <option value="">-- Pilih Pembimbing --</option>
                    @foreach($pembimbingList as $pemb)
                        <option value="{{ $pemb->id }}" {{ $pemb->id === $penempatan->pembimbing_id ? 'selected' : '' }}>
                            {{ $pemb->nama }} (Saat ini: {{ $pemb->mentee_aktif_count }} mentee)
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                <button type="button" onclick="closePindahModal()" style="padding: 0.6rem 1rem; background: var(--neutral-100, #F1F5F9); color: var(--neutral-700, #334155); border: 1px solid var(--neutral-200, #E2E8F0); border-radius: 8px; font-weight: 600; font-size: 0.8125rem; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="padding: 0.6rem 1.25rem; background: #0641CC; color: #FFFFFF; border: none; border-radius: 8px; font-weight: 700; font-size: 0.8125rem; cursor: pointer;">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL BATALKAN PENEMPATAN --}}
<div id="modal-batalkan-penempatan" class="modal-overlay">
    <div class="modal-container" style="max-width: 480px;">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0); display: flex; justify-content: space-between; align-items: center; background: #FEF2F2;">
            <div>
                <h3 style="font-size: 1.0625rem; font-weight: 800; color: #991B1B; margin: 0;">Batalkan Penempatan Peserta</h3>
                <p style="font-size: 0.75rem; color: #B91C1C; margin: 0.15rem 0 0 0;">
                    Catat pembatalan / pengunduran diri magang (AC-07-5, BR-07)
                </p>
            </div>
            <button type="button" onclick="closeBatalModal()" style="background: transparent; border: none; font-size: 1.25rem; color: #991B1B; cursor: pointer;">✕</button>
        </div>

        <form method="POST" action="{{ route('admin.penempatan.batalkan', $penempatan) }}" style="padding: 1.5rem;">
            @csrf
            @php
                $hariIni = today('Asia/Jakarta');
                $tglMulaiCarbon = \Carbon\Carbon::parse($penempatan->tgl_mulai);
                $sebelumMulai = $hariIni->lt($tglMulaiCarbon);
            @endphp
            <div style="margin-bottom: 1rem; padding: 0.75rem 1rem; border-radius: 8px; font-size: 0.8125rem; {{ $sebelumMulai ? 'background: #EFF6FF; color: #1E40AF; border: 1px solid #BFDBFE;' : 'background: #FFFBEB; color: #92400E; border: 1px solid #FCD34D;' }}">
                @if($sebelumMulai)
                    <strong>Informasi (Sebelum Pelaksanaan):</strong> Periode magang belum dimulai. Kuota akan dikembalikan ke jurusan dan akun login peserta tetap aktif.
                @else
                    <strong>Peringatan (Pengunduran Diri):</strong> Periode magang telah berjalan. Kuota TIDAK dikembalikan ke jurusan dan akun login peserta akan dinonaktifkan secara permanen.
                @endif
            </div>
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B); margin-bottom: 0.35rem;">
                    Alasan Pembatalan / Pengunduran Diri <span style="color: #DC2626;">*</span>
                </label>
                <textarea name="alasan" rows="4" required minlength="10" placeholder="Tuliskan alasan resmi pembatalan magang peserta (minimal 10 karakter)..." style="width: 100%; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0.75rem; font-size: 0.875rem; font-family: inherit;"></textarea>
                <span style="font-size: 0.75rem; color: var(--neutral-500, #64748B);">Alasan akan dicatat secara permanen di audit log sistem.</span>
            </div>

            <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                <button type="button" onclick="closeBatalModal()" style="padding: 0.6rem 1rem; background: var(--neutral-100, #F1F5F9); color: var(--neutral-700, #334155); border: 1px solid var(--neutral-200, #E2E8F0); border-radius: 8px; font-weight: 600; font-size: 0.8125rem; cursor: pointer;">
                    Kembali
                </button>
                <button type="submit" style="padding: 0.6rem 1.25rem; background: #DC2626; color: #FFFFFF; border: none; border-radius: 8px; font-weight: 700; font-size: 0.8125rem; cursor: pointer;">
                    Konfirmasi Pembatalan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openPindahModal() {
        document.getElementById('modal-pindah-pembimbing').style.display = 'flex';
    }
    function closePindahModal() {
        document.getElementById('modal-pindah-pembimbing').style.display = 'none';
    }
    function openBatalModal() {
        document.getElementById('modal-batalkan-penempatan').style.display = 'flex';
    }
    function closeBatalModal() {
        document.getElementById('modal-batalkan-penempatan').style.display = 'none';
    }
</script>
@endsection
