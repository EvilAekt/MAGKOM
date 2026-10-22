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
        <a href="{{ route('admin.penempatan.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; background: var(--neutral-0, #ffffff); border: 1px solid var(--neutral-300, #CBD5E1); color: var(--neutral-700, #334155); padding: 0.55rem 1.1rem; border-radius: 8px; font-weight: 600; font-size: 0.8125rem; text-decoration: none; transition: all 0.15s ease;">
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

    $badgeVariant = match($statusVal) {
        'aktif' => 'success',
        'selesai' => 'primary',
        'batal' => 'danger',
        default => 'secondary',
    };

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
            background: var(--neutral-0, #ffffff);
            border-radius: 12px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            width: 100%;
            overflow: hidden;
            border: 1px solid var(--neutral-300, #E2E8F0);
        }
        [data-theme="dark"] .field-label {
            color: #94A3B8 !important;
        }
        [data-theme="dark"] .field-value {
            color: #F8FAFC !important;
        }
        [data-theme="dark"] .modal-container {
            background: #182234 !important;
            border-color: #26354D !important;
            color: #F8FAFC !important;
        }
        [data-theme="dark"] .modal-container select,
        [data-theme="dark"] .modal-container textarea {
            background: #0F172A !important;
            border-color: #26354D !important;
            color: #F8FAFC !important;
        }
    </style>

    {{-- TOP HERO CARD GENTELELLA --}}
    <x-gentelella.card variant="default">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 1.25rem; flex-wrap: wrap;">
                <div style="width: 64px; height: 64px; border-radius: 50%; background: #0641CC; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 800; border: 3px solid #DBEAFE; flex-shrink: 0; overflow: hidden;">
                    @if($profil?->foto_url)
                        <img src="{{ $profil->foto_url }}" alt="{{ $peserta?->nama }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        {{ strtoupper(substr($peserta?->nama ?? 'P', 0, 2)) }}
                    @endif
                </div>
                <div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 0.25rem;">
                        <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0;">
                            {{ $peserta?->nama ?? '-' }}
                        </h2>
                        <x-gentelella.badge :variant="$badgeVariant">
                            {{ $statusObj ? $statusObj->label() : ucfirst($statusVal) }}
                        </x-gentelella.badge>
                        @if($penempatan->slotKhusus)
                            <span style="font-size: 0.7rem; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 4px; background: #FEF3C7; color: #B45309; border: 1px solid #FCD34D;">
                                Jalur Khusus: {{ $penempatan->slotKhusus->nama_mitra }}
                            </span>
                        @else
                            <span style="font-size: 0.7rem; font-weight: 600; padding: 0.2rem 0.5rem; border-radius: 4px; background: var(--neutral-100, #F1F5F9); color: var(--neutral-700, #475569); border: 1px solid var(--neutral-300, #CBD5E1);">
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
                <a href="{{ route('admin.penempatan.surat-diterima.pratinjau', $penempatan) }}" target="_blank" style="padding: 0.5rem 0.9rem; background: var(--neutral-0, #ffffff); color: #0641CC; border: 1px solid var(--blue-tint-line, #BFDBFE); border-radius: 8px; font-size: 0.8125rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    <span>Pratinjau Surat</span>
                </a>

                @if($statusVal === 'aktif')
                    <button type="button" onclick="openPindahModal()" style="padding: 0.5rem 0.9rem; background: var(--blue-tint, #EFF6FF); color: #0641CC; border: 1px solid var(--blue-tint-line, #DBEAFE); border-radius: 8px; font-size: 0.8125rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem;">
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
    </x-gentelella.card>

    {{-- QUICK STATS PROGRESS ROW (4 TILE-STAT GENTELELLA) --}}
    <div class="row top_tiles" style="margin-bottom: 0;">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <x-gentelella.tile-stat 
                title="Tugas Selesai" 
                value="{{ $tugasSelesaiCount }} / {{ $tugasCount }}" 
                icon="fa fa-tasks" 
                color="blue" 
                trend="Tugas Magang" />
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <x-gentelella.tile-stat 
                title="Total Logbook" 
                value="{{ $logbookCount }} Minggu" 
                icon="fa fa-book" 
                color="green" 
                trend="Jurnal Harian" />
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <x-gentelella.tile-stat 
                title="Nilai Akhir" 
                value="{{ $nilaiAkhir !== null ? number_format((float) $nilaiAkhir, 2) : 'Belum Ada' }}" 
                icon="fa fa-star" 
                color="orange" 
                trend="{{ $penempatan->penilaian?->predikat ?? 'Evaluasi' }}" />
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <x-gentelella.tile-stat 
                title="Durasi Magang" 
                value="{{ $penempatan->total_minggu }} Minggu" 
                icon="fa fa-calendar" 
                color="purple" 
                trend="Total Periode" />
        </div>
    </div>

    {{-- 2 COLUMNS GRID: INFORMASI DETAIL --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 1.5rem;">
        
        {{-- Card 1: Data Penempatan & Surat --}}
        <x-gentelella.x-panel title="Informasi Penempatan &amp; Surat Resmi" subtitle="Rincian alokasi kedinasan dan nomor surat resmi" :toolbox="false">
            <div style="display: flex; flex-direction: column; gap: 1rem;">
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
            </div>
        </x-gentelella.x-panel>

        {{-- Card 2: Pembimbing Lapangan --}}
        <x-gentelella.x-panel title="Pembimbing Lapangan" subtitle="Staf pembimbing teknis yang ditugaskan" :toolbox="false">
            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                @if($pembimbing)
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 52px; height: 52px; border-radius: 50%; background: #0641CC; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; font-weight: 800; flex-shrink: 0;">
                            {{ strtoupper(substr($pembimbing->nama, 0, 2)) }}
                        </div>
                        <div>
                            <div style="font-size: 1rem; font-weight: 800; color: var(--neutral-900, #0F172A);">
                                {{ $pembimbing->nama }}
                            </div>
                            <div style="font-size: 0.8125rem; color: var(--neutral-500, #64748B);">
                                {{ $pembimbing->email }}
                            </div>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; padding-top: 0.5rem; border-top: 1px solid var(--neutral-200, #E2E8F0);">
                        <div>
                            <div class="field-label">NIP Pembimbing</div>
                            <div class="field-value" style="font-family: ui-monospace, monospace;">
                                {{ $pembimbing->nip ?? '-' }}
                            </div>
                        </div>
                        <div>
                            <div class="field-label">Beban Mentee</div>
                            <div class="field-value">
                                {{ $pembimbing->mentee_aktif_count ?? 0 }} Mentee Aktif
                            </div>
                        </div>
                    </div>
                @else
                    <div style="text-align: center; padding: 2rem 1rem; color: var(--neutral-500, #64748B);">
                        <p style="margin: 0; font-size: 0.875rem;">Belum ada pembimbing lapangan yang ditugaskan.</p>
                    </div>
                @endif
            </div>
        </x-gentelella.x-panel>

        {{-- Card 3: Dokumen Magang & Unduhan --}}
        <x-gentelella.x-panel title="Berkas &amp; Dokumen Magang" subtitle="Arsip dokumen persyaratan dan surat diterima" :toolbox="false">
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                {{-- Surat Diterima PDF --}}
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid var(--blue-tint-line, #BFDBFE); background: var(--blue-tint, #EFF6FF);">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0641CC" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                        <div>
                            <div style="font-size: 0.8125rem; font-weight: 700; color: #1E40AF;">Surat Keterangan Diterima Magang</div>
                            <div style="font-size: 0.75rem; color: #3B82F6;">PDF Resmi Tanda Tangan Basah / Stempel</div>
                        </div>
                    </div>
                    <div style="display: flex; gap: 0.35rem;">
                        <a href="{{ route('admin.penempatan.surat-diterima.pratinjau', $penempatan) }}" target="_blank" style="padding: 0.35rem 0.65rem; background: var(--neutral-0, #ffffff); color: #0641CC; border: 1px solid var(--blue-tint-line, #BFDBFE); border-radius: 6px; font-size: 0.75rem; font-weight: 700; text-decoration: none;">
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
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.6rem 0.85rem; border-radius: 8px; border: 1px solid var(--neutral-300, #E2E8F0); background: var(--neutral-100, #F8FAFC);">
                            <div style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-900, #1E293B);">
                                {{ ucwords(str_replace('_', ' ', $dok->jenis_dokumen)) }}
                            </div>
                            <x-gentelella.badge variant="success">
                                Terverifikasi
                            </x-gentelella.badge>
                        </div>
                    @endforeach
                @else
                    <div style="font-size: 0.8125rem; color: var(--neutral-500, #64748B); text-align: center; padding: 0.75rem;">
                        Tidak ada berkas unggahan tambahan.
                    </div>
                @endif
            </div>
        </x-gentelella.x-panel>

    </div>

</div>

{{-- MODAL PINDAH PEMBIMBING --}}
<div id="modal-pindah-pembimbing" class="modal-overlay">
    <div class="modal-container" style="max-width: 480px;">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-300, #E2E8F0); display: flex; justify-content: space-between; align-items: center; background: var(--neutral-100, #F8FAFC);">
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
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-900, #1E293B); margin-bottom: 0.35rem;">
                    Pilih Pembimbing Baru <span style="color: #DC2626;">*</span>
                </label>
                <select name="pembimbing_id" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                    <option value="">-- Pilih Pembimbing --</option>
                    @foreach($pembimbingList as $pemb)
                        <option value="{{ $pemb->id }}" {{ $pemb->id === $penempatan->pembimbing_id ? 'selected' : '' }}>
                            {{ $pemb->nama }} (Saat ini: {{ $pemb->mentee_aktif_count }} mentee)
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                <button type="button" onclick="closePindahModal()" style="padding: 0.6rem 1rem; background: var(--neutral-100, #F1F5F9); color: var(--neutral-700, #334155); border: 1px solid var(--neutral-300, #E2E8F0); border-radius: 8px; font-weight: 600; font-size: 0.8125rem; cursor: pointer;">
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
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-300, #E2E8F0); display: flex; justify-content: space-between; align-items: center; background: #FEF2F2;">
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
            <div style="margin-bottom: 1rem; padding: 0.75rem 1rem; border-radius: 8px; font-size: 0.8125rem; {{ $sebelumMulai ? 'background: var(--blue-tint, #EFF6FF); color: #1E40AF; border: 1px solid var(--blue-tint-line, #BFDBFE);' : 'background: #FFFBEB; color: #92400E; border: 1px solid #FCD34D;' }}">
                @if($sebelumMulai)
                    <strong>Informasi (Sebelum Pelaksanaan):</strong> Periode magang belum dimulai. Kuota akan dikembalikan ke jurusan dan akun login peserta tetap aktif.
                @else
                    <strong>Peringatan (Pengunduran Diri):</strong> Periode magang telah berjalan. Kuota TIDAK dikembalikan ke jurusan dan akun login peserta akan dinonaktifkan secara permanen.
                @endif
            </div>
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-900, #1E293B); margin-bottom: 0.35rem;">
                    Alasan Pembatalan / Pengunduran Diri <span style="color: #DC2626;">*</span>
                </label>
                <textarea name="alasan" rows="4" required minlength="10" placeholder="Tuliskan alasan resmi pembatalan magang peserta (minimal 10 karakter)..." style="width: 100%; border: 1px solid var(--neutral-400, #CBD5E1); border-radius: 8px; padding: 0.75rem; font-size: 0.875rem; font-family: inherit;"></textarea>
                <span style="font-size: 0.75rem; color: var(--neutral-500, #64748B);">Alasan akan dicatat secara permanen di audit log sistem.</span>
            </div>

            <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                <button type="button" onclick="closeBatalModal()" style="padding: 0.6rem 1rem; background: var(--neutral-100, #F1F5F9); color: var(--neutral-700, #334155); border: 1px solid var(--neutral-300, #E2E8F0); border-radius: 8px; font-weight: 600; font-size: 0.8125rem; cursor: pointer;">
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
