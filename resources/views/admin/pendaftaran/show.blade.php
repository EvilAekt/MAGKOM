@extends('layouts.dashboard')

@section('title', 'Verifikasi Pendaftaran ' . $pendaftaran->nomor_pendaftaran . ' - Admin MagKom')
@section('page_title', 'Verifikasi Berkas Calon Peserta')
@section('page_description', 'Tinjau kelengkapan dokumen persyaratan dan tetapkan keputusan penerimaan pendaftaran.')

@section('breadcrumbs')
    <a href="{{ route('admin.pendaftaran.index') }}" class="breadcrumb-item">Verifikasi Pendaftaran</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">{{ $pendaftaran->nomor_pendaftaran }}</span>
@endsection

@section('page_actions')
    <a href="{{ route('admin.pendaftaran.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; background: var(--neutral-0); border: 1px solid var(--neutral-500); color: var(--neutral-800); padding: 0.5rem 1rem; border-radius: 8px; font-weight: 600; font-size: 0.8125rem; text-decoration: none;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        <span>Kembali ke Daftar</span>
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
        'berkas_belum_lengkap' => ['bg' => 'var(--neutral-200)', 'color' => 'var(--neutral-800)'],
        'diajukan' => ['bg' => 'var(--amber-200)', 'color' => 'var(--amber-800)'],
        'perlu_perbaikan' => ['bg' => 'var(--amber-200)', 'color' => 'var(--amber-800)'],
        'waiting_list' => ['bg' => 'var(--blue-tint)', 'color' => 'var(--blue-700)'],
        'diterima' => ['bg' => 'var(--green-100)', 'color' => 'var(--green-700)'],
        'ditolak' => ['bg' => 'var(--red-200)', 'color' => 'var(--red-700)'],
        default => ['bg' => 'var(--neutral-200)', 'color' => 'var(--neutral-700)'],
    };
@endphp

<div style="display: flex; flex-direction: column; gap: 1.5rem;">

    <!-- TOP HEADER CARD METADATA -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.25rem;">
                <span style="font-size: 0.8125rem; font-weight: 700; color: var(--blue-700);">NO. PENDAFTARAN</span>
                <span class="role-chip-badge" style="background: {{ $badgeStyle['bg'] }}; color: {{ $badgeStyle['color'] }}; font-weight: 700;">
                    {{ $status->label() }}
                </span>
            </div>
            <h1 style="font-size: 1.375rem; font-weight: 800; color: var(--neutral-800); margin: 0;">
                {{ $pendaftaran->nomor_pendaftaran }} &bull; {{ $user?->nama }}
            </h1>
        </div>

        <div style="text-align: right;">
            <span style="font-size: 0.8125rem; color: var(--neutral-600);">Diajukan Pada:</span>
            <div style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-800);">
                {{ $pendaftaran->diajukan_at ? $pendaftaran->diajukan_at->translatedFormat('d F Y, H:i') . ' WIB' : 'Belum Diajukan' }}
            </div>
        </div>
    </div>

    <!-- SPLIT-SCREEN CONTAINER -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 1.5rem; align-items: start;">

        <!-- LEFT PANEL: PROFIL & BERKAS PERSYARATAN -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <!-- Profil Calon Peserta -->
            <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
                <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-800); margin: 0 0 1rem 0; border-bottom: 1px solid var(--neutral-300); padding-bottom: 0.75rem;">
                    Biodata Calon Peserta
                </h3>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; font-size: 0.875rem;">
                    <div>
                        <span style="font-size: 0.75rem; color: var(--neutral-600); text-transform: uppercase; font-weight: 600;">Nama Lengkap</span>
                        <p style="font-weight: 700; color: var(--neutral-800); margin: 0.25rem 0 0 0;">{{ $user?->nama }}</p>
                    </div>
                    <div>
                        <span style="font-size: 0.75rem; color: var(--neutral-600); text-transform: uppercase; font-weight: 600;">Email & Kontak</span>
                        <p style="font-weight: 600; color: var(--neutral-800); margin: 0.25rem 0 0 0;">{{ $user?->email }}</p>
                        <small style="color: var(--neutral-600);">{{ $profil?->no_hp ?? '-' }}</small>
                    </div>
                    <div>
                        <span style="font-size: 0.75rem; color: var(--neutral-600); text-transform: uppercase; font-weight: 600;">Asal Instansi</span>
                        <p style="font-weight: 700; color: var(--neutral-800); margin: 0.25rem 0 0 0;">{{ $profil?->institusi ?? '-' }}</p>
                        <small style="color: var(--neutral-600);">NIM/NISN: {{ $profil?->nim_nisn ?? '-' }}</small>
                    </div>
                    <div>
                        <span style="font-size: 0.75rem; color: var(--neutral-600); text-transform: uppercase; font-weight: 600;">Jenjang & Jurusan</span>
                        <p style="font-weight: 700; color: var(--neutral-800); margin: 0.25rem 0 0 0;">
                            {{ strtoupper($profil?->jenjang?->value ?? '-') }} &bull; {{ $pendaftaran->kuota?->jurusan?->nama_jurusan ?? '-' }}
                        </p>
                    </div>
                    <div>
                        <span style="font-size: 0.75rem; color: var(--neutral-600); text-transform: uppercase; font-weight: 600;">Periode Magang</span>
                        <p style="font-weight: 700; color: var(--blue-700); margin: 0.25rem 0 0 0;">
                            {{ $pendaftaran->bulanMagang?->bulan_mulai?->translatedFormat('F Y') }} ({{ $pendaftaran->durasi_bulan }} Bulan)
                        </p>
                    </div>
                </div>
            </div>

            <!-- Berkas Persyaratan Terunggah -->
            <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid var(--neutral-300); padding-bottom: 0.75rem;">
                    <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-800); margin: 0;">
                        Dokumen Persyaratan Calon Peserta
                    </h3>
                    <span style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-600);">
                        Total: {{ $pendaftaran->dokumen->count() }} Dokumen
                    </span>
                </div>

                @if($pendaftaran->dokumen->isEmpty())
                    <div style="text-align: center; padding: 2rem 1rem; color: var(--neutral-600); background: var(--neutral-100); border-radius: 8px;">
                        <p style="margin: 0; font-size: 0.875rem;">Calon peserta belum mengunggah dokumen persyaratan.</p>
                    </div>
                @else
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        @foreach($pendaftaran->dokumen as $dok)
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.875rem 1rem; background: var(--neutral-100); border: 1px solid var(--neutral-300); border-radius: 8px; flex-wrap: wrap; gap: 0.75rem;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="width: 38px; height: 38px; background: var(--blue-tint); border-radius: 6px; display: flex; align-items: center; justify-content: center; color: var(--blue-700); flex-shrink: 0;">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                            <polyline points="14 2 14 8 20 8"></polyline>
                                        </svg>
                                    </div>
                                    <div>
                                        <div style="font-weight: 700; font-size: 0.875rem; color: var(--neutral-800);">
                                            {{ $dok->jenisDokumen?->nama ?? 'Dokumen Pendukung' }}
                                        </div>
                                        <div style="font-size: 0.75rem; color: var(--neutral-600);">
                                            {{ $dok->nama_asli }} &bull; {{ number_format($dok->ukuran / 1024, 1) }} KB
                                        </div>
                                    </div>
                                </div>

                                <a href="{{ $dok->getSignedDownloadUrl(5) }}" target="_blank" style="display: inline-flex; align-items: center; gap: 0.375rem; background: var(--blue-600); color: #FFFFFF; text-decoration: none; padding: 0.45rem 0.875rem; border-radius: 6px; font-weight: 600; font-size: 0.8125rem;">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    <span>Tinjau Berkas</span>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- RIGHT PANEL: FORMULIR KEPUTUSAN VERIFIKASI -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
                <h3 style="font-size: 1.0625rem; font-weight: 800; color: var(--neutral-800); margin: 0 0 0.5rem 0;">
                    Keputusan Verifikasi Berkas
                </h3>
                <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0 0 1.25rem 0;">
                    Pilih salah satu keputusan berikut untuk memproses pengajuan calon peserta magang.
                </p>

                @if($isFinal)
                    <div style="padding: 1.25rem; border-radius: 8px; font-size: 0.875rem;
                        background: {{ $statusValue === 'diterima' ? 'var(--green-100)' : 'var(--neutral-200)' }};
                        border: 1px solid {{ $statusValue === 'diterima' ? 'var(--green-600)' : 'var(--neutral-400)' }};">
                        <strong>Pengajuan Sudah Diputuskan</strong>
                        <p style="margin: 0.375rem 0 0 0;">
                            Status pengajuan ini adalah <strong>{{ $status->label() }}</strong> pada {{ $pendaftaran->diputuskan_at?->translatedFormat('d F Y, H:i') }}.
                            @if($pendaftaran->catatan)
                                <br><span style="color: var(--neutral-700);">Catatan: {{ $pendaftaran->catatan }}</span>
                            @endif
                        </p>
                    </div>
                @else
                    <!-- Tab Switcher Aksi Keputusan -->
                    <div style="display: flex; gap: 0.5rem; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-300); padding-bottom: 0.5rem;">
                        <button type="button" id="tab-btn-terima" onclick="switchActionTab('terima')" style="flex: 1; padding: 0.5rem 0.75rem; border-radius: 6px; border: none; font-weight: 700; font-size: 0.8125rem; cursor: pointer; background: var(--green-600); color: #FFFFFF;">
                            Terima & Tempatkan
                        </button>
                        <button type="button" id="tab-btn-revisi" onclick="switchActionTab('perlu_perbaikan')" style="flex: 1; padding: 0.5rem 0.75rem; border-radius: 6px; border: none; font-weight: 700; font-size: 0.8125rem; cursor: pointer; background: var(--neutral-200); color: var(--neutral-800);">
                            Minta Revisi
                        </button>
                        <button type="button" id="tab-btn-tolak" onclick="switchActionTab('tolak')" style="flex: 1; padding: 0.5rem 0.75rem; border-radius: 6px; border: none; font-weight: 700; font-size: 0.8125rem; cursor: pointer; background: var(--neutral-200); color: var(--neutral-800);">
                            Tolak
                        </button>
                    </div>

                    <!-- Form Keputusan -->
                    <form method="POST" action="{{ route('admin.pendaftaran.putuskan', $pendaftaran) }}" id="decision-form">
                        @csrf
                        <input type="hidden" name="keputusan" id="keputusan-input" value="terima">

                        <!-- Tab 1: TERIMA -->
                        <div id="action-panel-terima" style="display: flex; flex-direction: column; gap: 1rem;">
                            <div style="background: var(--green-100); border: 1px solid var(--green-600); border-radius: 8px; padding: 0.875rem 1rem; font-size: 0.8125rem; color: var(--green-900);">
                                Menerima pengajuan akan mengurangi kuota umum jurusan secara atomik dan membentuk penempatan resmi.
                            </div>

                            <div style="display: flex; flex-direction: column; gap: 0.375rem;">
                                <label for="pembimbing_id" style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">
                                    Tetapkan Pembimbing Lapangan <span style="color: var(--red-600);">*</span>
                                </label>
                                <select id="pembimbing_id" name="pembimbing_id" style="height: 42px; border: 1px solid var(--neutral-500); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: var(--neutral-0);">
                                    <option value="">-- Pilih Pembimbing Lapangan --</option>
                                    @foreach($pembimbingList as $p)
                                        <option value="{{ $p->id }}">{{ $p->nama }} ({{ $p->email }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div style="display: flex; flex-direction: column; gap: 0.375rem;">
                                <label for="bidang_id" style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">
                                    Penempatan Bidang Dinas (Opsional)
                                </label>
                                <select id="bidang_id" name="bidang_id" style="height: 42px; border: 1px solid var(--neutral-500); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: var(--neutral-0);">
                                    <option value="">-- Belum Ditentukan / Sesuai Pembimbing --</option>
                                    @foreach($bidangList as $b)
                                        <option value="{{ $b->id }}">{{ $b->nama_bidang }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" onclick="return confirm('Apakah Anda yakin menerima calon peserta ini dan menetapkan penempatan magang?');" style="height: 44px; margin-top: 0.5rem; background: var(--green-700); color: #FFFFFF; font-weight: 700; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer; transition: background 0.15s;">
                                Terima & Tempatkan Peserta
                            </button>
                        </div>

                        <!-- Tab 2: PERLU PERBAIKAN -->
                        <div id="action-panel-perlu_perbaikan" style="display: none; flex-direction: column; gap: 1rem;">
                            <div style="background: var(--amber-100); border: 1px solid var(--amber-600); border-radius: 8px; padding: 0.875rem 1rem; font-size: 0.8125rem; color: var(--amber-900);">
                                Status pengajuan akan dikembalikan ke calon peserta untuk perbaikan berkas sesuai catatan di bawah.
                            </div>

                            <div style="display: flex; flex-direction: column; gap: 0.375rem;">
                                <label for="catatan_revisi" style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">
                                    Catatan / Instruksi Perbaikan <span style="color: var(--red-600);">*</span>
                                </label>
                                <textarea id="catatan_revisi" name="catatan" rows="4" placeholder="Sebutkan berkas yang perlu diperbaiki dan alasannya secara spesifik..." style="border: 1px solid var(--neutral-500); border-radius: 8px; padding: 0.75rem; font-size: 0.875rem; font-family: inherit;"></textarea>
                            </div>

                            <button type="submit" onclick="return confirm('Kirimkan permintaan perbaikan berkas kepada calon peserta?');" style="height: 44px; margin-top: 0.5rem; background: var(--amber-600); color: #FFFFFF; font-weight: 700; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer;">
                                Minta Perbaikan Berkas
                            </button>
                        </div>

                        <!-- Tab 3: TOLAK -->
                        <div id="action-panel-tolak" style="display: none; flex-direction: column; gap: 1rem;">
                            <div style="background: var(--red-100); border: 1px solid var(--red-600); border-radius: 8px; padding: 0.875rem 1rem; font-size: 0.8125rem; color: var(--red-900);">
                                Penolakan bersifat final. Kuota tidak terpakai dan calon peserta akan melihat alasan penolakan pada dashboard mereka.
                            </div>

                            <div style="display: flex; flex-direction: column; gap: 0.375rem;">
                                <label for="catatan_tolak" style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">
                                    Alasan Penolakan <span style="color: var(--red-600);">*</span>
                                </label>
                                <textarea id="catatan_tolak" name="catatan" rows="4" placeholder="Tuliskan alasan penolakan pengajuan..." style="border: 1px solid var(--neutral-500); border-radius: 8px; padding: 0.75rem; font-size: 0.875rem; font-family: inherit;"></textarea>
                            </div>

                            <button type="submit" onclick="return confirm('Apakah Anda yakin MENOLAK pengajuan pendaftaran ini?');" style="height: 44px; margin-top: 0.5rem; background: var(--red-700); color: #FFFFFF; font-weight: 700; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer;">
                                Tolak Pengajuan Pendaftaran
                            </button>
                        </div>
                    </form>

                    <script>
                        function switchActionTab(action) {
                            document.getElementById('keputusan-input').value = action;
                            
                            const panels = ['terima', 'perlu_perbaikan', 'tolak'];
                            panels.forEach(p => {
                                const panel = document.getElementById('action-panel-' + p);
                                const btn = document.getElementById('tab-btn-' + (p === 'perlu_perbaikan' ? 'revisi' : p));
                                if (p === action) {
                                    panel.style.display = 'flex';
                                    btn.style.background = p === 'terima' ? 'var(--green-600)' : (p === 'perlu_perbaikan' ? 'var(--amber-600)' : 'var(--red-600)');
                                    btn.style.color = '#FFFFFF';
                                } else {
                                    panel.style.display = 'none';
                                    btn.style.background = 'var(--neutral-200)';
                                    btn.style.color = 'var(--neutral-800)';
                                }
                            });

                            // Toggle required attributes
                            document.getElementById('pembimbing_id').required = (action === 'terima');
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
@endsection
