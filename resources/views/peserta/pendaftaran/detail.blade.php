@extends('layouts.dashboard')

@section('title', 'Detail Pendaftaran ' . $pendaftaran->nomor_pendaftaran . ' - Peserta MagKom')
@section('page_title', 'Detail Berkas & Status Pendaftaran')
@section('page_description', 'Kelola berkas persyaratan dan pantau alur verifikasi magang Anda.')

@section('breadcrumbs')
    <a href="{{ route('peserta.pendaftaran.index') }}" class="breadcrumb-item">Pendaftaran Magang</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">{{ $pendaftaran->nomor_pendaftaran }}</span>
@endsection

@section('page_actions')
    <a href="{{ route('peserta.pendaftaran.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; background: var(--neutral-0); border: 1px solid var(--neutral-500); color: var(--neutral-800); padding: 0.5rem 1rem; border-radius: 8px; font-weight: 600; font-size: 0.8125rem; text-decoration: none;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        <span>Kembali ke Riwayat</span>
    </a>
@endsection

@section('content')
@php
    $status = $pendaftaran->status;
    $statusValue = $status->value;
    $canModify = in_array($statusValue, ['berkas_belum_lengkap', 'perlu_perbaikan'], true);

    // Hitung kelengkapan dokumen wajib
    $dokumenWajibList = $jenisDokumenList->where('wajib', true);
    $wajibCount = $dokumenWajibList->count();
    $uploadedWajibCount = 0;
    foreach ($dokumenWajibList as $dw) {
        if (isset($dokumenTerunggah[$dw->id])) {
            $uploadedWajibCount++;
        }
    }
    $allWajibUploaded = ($wajibCount > 0 && $uploadedWajibCount >= $wajibCount);
@endphp

<div style="display: flex; flex-direction: column; gap: 1.5rem;">

    <!-- 1. KARTU METADATA & RINGKASAN PENDAFTARAN -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;">
            <div>
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.375rem;">
                    <span style="font-size: 0.8125rem; font-weight: 700; color: var(--blue-700); text-transform: uppercase; letter-spacing: 0.05em;">Nomor Pengajuan</span>
                    @php
                        $badgeStyle = match($statusValue) {
                            'berkas_belum_lengkap' => ['bg' => 'var(--neutral-200)', 'color' => 'var(--neutral-800)', 'label' => 'Berkas Belum Lengkap'],
                            'diajukan' => ['bg' => 'var(--amber-200)', 'color' => 'var(--amber-700)', 'label' => 'Menunggu Verifikasi'],
                            'perlu_perbaikan' => ['bg' => 'var(--amber-200)', 'color' => 'var(--amber-700)', 'label' => 'Perlu Perbaikan'],
                            'waiting_list' => ['bg' => 'var(--blue-tint)', 'color' => 'var(--blue-700)', 'label' => 'Waiting List'],
                            'diterima' => ['bg' => 'var(--green-100)', 'color' => 'var(--green-700)', 'label' => 'Diterima'],
                            'ditolak' => ['bg' => 'var(--red-200)', 'color' => 'var(--red-700)', 'label' => 'Ditolak'],
                            default => ['bg' => 'var(--neutral-200)', 'color' => 'var(--neutral-700)', 'label' => $status->label()],
                        };
                    @endphp
                    <span class="role-chip-badge" style="background: {{ $badgeStyle['bg'] }}; color: {{ $badgeStyle['color'] }}; font-weight: 700;">
                        {{ $badgeStyle['label'] }}
                    </span>
                </div>
                <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--neutral-800); margin: 0;">
                    {{ $pendaftaran->nomor_pendaftaran }}
                </h1>
            </div>

            <div style="text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 0.25rem;">
                <span style="font-size: 0.8125rem; color: var(--neutral-600);">Kelengkapan Berkas Wajib</span>
                <span style="font-size: 1.125rem; font-weight: 700; color: {{ $allWajibUploaded ? 'var(--green-700)' : 'var(--amber-700)' }};">
                    {{ $uploadedWajibCount }} / {{ $wajibCount }} Berkas
                </span>
            </div>
        </div>

        <!-- Grid Ringkasan Detail -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; padding-top: 1rem; border-top: 1px solid var(--neutral-300);">
            <div>
                <span style="font-size: 0.75rem; color: var(--neutral-600); text-transform: uppercase; font-weight: 600;">Periode Magang</span>
                <p style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-800); margin: 0.25rem 0 0 0;">
                    {{ $pendaftaran->bulanMagang?->bulan_mulai?->translatedFormat('F Y') }}
                </p>
            </div>
            <div>
                <span style="font-size: 0.75rem; color: var(--neutral-600); text-transform: uppercase; font-weight: 600;">Jurusan / Bidang</span>
                <p style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-800); margin: 0.25rem 0 0 0;">
                    {{ $pendaftaran->kuota?->jurusan?->nama_jurusan ?? '-' }}
                </p>
            </div>
            <div>
                <span style="font-size: 0.75rem; color: var(--neutral-600); text-transform: uppercase; font-weight: 600;">Rencana Durasi</span>
                <p style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-800); margin: 0.25rem 0 0 0;">
                    {{ $pendaftaran->durasi_bulan }} Bulan
                </p>
            </div>
            <div>
                <span style="font-size: 0.75rem; color: var(--neutral-600); text-transform: uppercase; font-weight: 600;">Waktu Pengajuan</span>
                <p style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-800); margin: 0.25rem 0 0 0;">
                    {{ $pendaftaran->diajukan_at ? $pendaftaran->diajukan_at->translatedFormat('d M Y, H:i') : 'Belum Diajukan' }}
                </p>
            </div>
        </div>
    </div>

    <!-- 2. STATUS STEPPER (VISUAL ALUR SELEKSI) -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
        <h3 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-800); margin: 0 0 1.25rem 0;">Tahapan Alur Pendaftaran</h3>

        @php
            // Hitung active step (1..4)
            $step = match($statusValue) {
                'berkas_belum_lengkap' => 1,
                'diajukan', 'waiting_list' => 2,
                'perlu_perbaikan' => 3,
                'diterima', 'ditolak', 'dibatalkan' => 4,
                default => 1,
            };
        @endphp

        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; position: relative;">
            <!-- Step 1: Pengisian Berkas -->
            <div style="display: flex; flex-direction: column; align-items: center; text-align: center; gap: 0.5rem;">
                <div style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.875rem;
                    background: {{ $step >= 1 ? ($step > 1 ? 'var(--green-600)' : 'var(--blue-600)') : 'var(--neutral-200)' }};
                    color: {{ $step >= 1 ? '#FFFFFF' : 'var(--neutral-600)' }};">
                    @if($step > 1)
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    @else
                        1
                    @endif
                </div>
                <div>
                    <span style="font-size: 0.8125rem; font-weight: 700; color: {{ $step === 1 ? 'var(--blue-700)' : 'var(--neutral-800)' }};">Unggah Berkas</span>
                    <p style="font-size: 0.75rem; color: var(--neutral-600); margin: 0.125rem 0 0 0;">Lengkapi dokumen wajib</p>
                </div>
            </div>

            <!-- Step 2: Verifikasi Admin -->
            <div style="display: flex; flex-direction: column; align-items: center; text-align: center; gap: 0.5rem;">
                <div style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.875rem;
                    background: {{ $step >= 2 ? ($step > 2 ? 'var(--green-600)' : 'var(--amber-500)') : 'var(--neutral-200)' }};
                    color: {{ $step >= 2 ? '#FFFFFF' : 'var(--neutral-600)' }};">
                    @if($step > 2)
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    @else
                        2
                    @endif
                </div>
                <div>
                    <span style="font-size: 0.8125rem; font-weight: 700; color: {{ $step === 2 ? 'var(--amber-800)' : 'var(--neutral-800)' }};">Verifikasi Berkas</span>
                    <p style="font-size: 0.75rem; color: var(--neutral-600); margin: 0.125rem 0 0 0;">Pemeriksaan oleh Admin</p>
                </div>
            </div>

            <!-- Step 3: Tinjauan / Perbaikan -->
            <div style="display: flex; flex-direction: column; align-items: center; text-align: center; gap: 0.5rem;">
                <div style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.875rem;
                    background: {{ $step >= 3 ? ($step > 3 ? 'var(--green-600)' : 'var(--amber-600)') : 'var(--neutral-200)' }};
                    color: {{ $step >= 3 ? '#FFFFFF' : 'var(--neutral-600)' }};">
                    @if($step > 3)
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    @else
                        3
                    @endif
                </div>
                <div>
                    <span style="font-size: 0.8125rem; font-weight: 700; color: {{ $step === 3 ? 'var(--amber-800)' : 'var(--neutral-800)' }};">Tinjauan Revisi</span>
                    <p style="font-size: 0.75rem; color: var(--neutral-600); margin: 0.125rem 0 0 0;">Jika ada koreksi berkas</p>
                </div>
            </div>

            <!-- Step 4: Keputusan Akhir -->
            <div style="display: flex; flex-direction: column; align-items: center; text-align: center; gap: 0.5rem;">
                <div style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.875rem;
                    background: {{ $step === 4 ? ($statusValue === 'diterima' ? 'var(--green-600)' : 'var(--red-600)') : 'var(--neutral-200)' }};
                    color: {{ $step === 4 ? '#FFFFFF' : 'var(--neutral-600)' }};">
                    @if($step === 4 && $statusValue === 'diterima')
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    @elseif($step === 4 && $statusValue === 'ditolak')
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    @else
                        4
                    @endif
                </div>
                <div>
                    <span style="font-size: 0.8125rem; font-weight: 700; color: {{ $step === 4 ? ($statusValue === 'diterima' ? 'var(--green-700)' : 'var(--red-700)') : 'var(--neutral-800)' }};">Keputusan Akhir</span>
                    <p style="font-size: 0.75rem; color: var(--neutral-600); margin: 0.125rem 0 0 0;">Diterima / Ditolak</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. BANNER KHUSUS STATUS TERTENTU -->
    @if($statusValue === 'diterima')
        <!-- Banner Sukses & Download Surat Diterima PDF -->
        <div style="background: var(--green-100); border: 2px solid var(--green-600); border-radius: 12px; padding: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.25rem;">
            <div style="display: flex; align-items: flex-start; gap: 1rem;">
                <div style="width: 48px; height: 48px; background: var(--green-600); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #FFFFFF; flex-shrink: 0;">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                </div>
                <div>
                    <h2 style="font-size: 1.125rem; font-weight: 800; color: var(--green-900); margin: 0 0 0.25rem 0;">
                        Selamat! Pendaftaran Anda Diterima
                    </h2>
                    <p style="font-size: 0.875rem; color: var(--green-800); margin: 0 0 0.5rem 0;">
                        Anda telah resmi diterima sebagai peserta magang di Dinas Komunikasi dan Informatika Kabupaten Sleman.
                    </p>
                    @if($pendaftaran->penempatan)
                        <div style="font-size: 0.8125rem; color: var(--green-900); display: flex; flex-wrap: wrap; gap: 1rem;">
                            <span>Bidang: <strong>{{ $pendaftaran->penempatan->bidang?->nama_bidang ?? 'Dinas Kominfo' }}</strong></span>
                            <span>Pembimbing: <strong>{{ $pendaftaran->penempatan->pembimbing?->nama ?? 'Menunggu Penetapan' }}</strong></span>
                            <span>Mulai: <strong>{{ \Carbon\Carbon::parse($pendaftaran->penempatan->tgl_mulai)->translatedFormat('d F Y') }}</strong></span>
                        </div>
                    @endif
                </div>
            </div>

            @if($pendaftaran->penempatan)
                <a href="{{ route('peserta.penempatan.surat-diterima', $pendaftaran->penempatan) }}" style="display: inline-flex; align-items: center; gap: 0.5rem; background: var(--green-700); color: #FFFFFF; text-decoration: none; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 700; font-size: 0.875rem; box-shadow: var(--shadow-subtle);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    <span>Unduh Surat Keterangan Diterima (PDF)</span>
                </a>
            @endif
        </div>
    @elseif($statusValue === 'waiting_list')
        <!-- Banner Waiting List -->
        <div style="background: var(--blue-tint); border: 1px solid var(--blue-tint-line); border-radius: 12px; padding: 1.25rem 1.5rem; display: flex; align-items: flex-start; gap: 1rem;">
            <div style="width: 40px; height: 40px; background: var(--blue-600); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #FFFFFF; flex-shrink: 0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
            </div>
            <div>
                <h3 style="font-size: 1rem; font-weight: 700; color: var(--blue-900); margin: 0 0 0.25rem 0;">Status: Antrean Tunggu (Waiting List)</h3>
                <p style="font-size: 0.875rem; color: var(--blue-800); margin: 0;">
                    Pendaftaran Anda saat ini berada dalam antrean <strong>Waiting List</strong> karena kuota reguler untuk jurusan ini telah terisi penuh. 
                    Jika ada kuota yang terbuka sebelum periode magang dimulai, tim admin akan memproses penempatan Anda secara berurutan.
                </p>
            </div>
        </div>
    @elseif($statusValue === 'perlu_perbaikan')
        <!-- Banner Catatan Perbaikan -->
        <div style="background: var(--amber-100); border: 2px solid var(--amber-600); border-radius: 12px; padding: 1.25rem 1.5rem; display: flex; align-items: flex-start; gap: 1rem;">
            <div style="width: 40px; height: 40px; background: var(--amber-600); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #FFFFFF; flex-shrink: 0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
            </div>
            <div>
                <h3 style="font-size: 1rem; font-weight: 700; color: var(--amber-900); margin: 0 0 0.25rem 0;">Berkas Memerlukan Perbaikan</h3>
                <p style="font-size: 0.875rem; color: var(--amber-800); margin: 0 0 0.5rem 0;">
                    <strong>Catatan Tim Verifikator:</strong> {{ $pendaftaran->catatan ?? 'Terdapat berkas yang belum sesuai kriteria. Silakan periksa berkas yang ditandai dan unggah ulang perbaikan.' }}
                </p>
                <p style="font-size: 0.8125rem; color: var(--amber-900); margin: 0;">
                    Setelah memperbaiki berkas di bawah, pastikan Anda menekan tombol <strong>"Ajukan Ulang Pendaftaran"</strong> di bagian bawah halaman.
                </p>
            </div>
        </div>
    @elseif($statusValue === 'ditolak')
        <!-- Banner Ditolak -->
        <div style="background: var(--red-100); border: 1px solid var(--red-600); border-radius: 12px; padding: 1.25rem 1.5rem; display: flex; align-items: flex-start; gap: 1rem;">
            <div style="width: 40px; height: 40px; background: var(--red-600); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #FFFFFF; flex-shrink: 0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="15" y1="9" x2="9" y2="15"></line>
                    <line x1="9" y1="9" x2="15" y2="15"></line>
                </svg>
            </div>
            <div>
                <h3 style="font-size: 1rem; font-weight: 700; color: var(--red-900); margin: 0 0 0.25rem 0;">Pengajuan Magang Ditolak</h3>
                <p style="font-size: 0.875rem; color: var(--red-800); margin: 0;">
                    Alasan: {{ $pendaftaran->catatan ?? 'Pengajuan magang tidak dapat disetujui pada periode ini.' }}
                </p>
            </div>
        </div>
    @endif

    <!-- 4. DAFTAR KARTU UNGGAH BERKAS PERSYARATAN -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
        <div style="border-bottom: 1px solid var(--neutral-400); padding-bottom: 1rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
            <div>
                <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-800); margin: 0 0 0.25rem 0;">
                    Berkas Persyaratan Magang
                </h2>
                <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0;">
                    Format berkas yang didukung: <strong>PDF, JPG, JPEG, PNG</strong> &bull; Ukuran maksimal: <strong>{{ $maksMb }} MB per file</strong>
                </p>
            </div>
            <div>
                <span style="font-size: 0.8125rem; color: var(--neutral-600);">
                    Jenjang: <strong>{{ strtoupper($profil?->jenjang?->value ?? 'SMK') }}</strong>
                </span>
            </div>
        </div>

        <!-- Grid Kartu Berkas -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.25rem;">
            @foreach($jenisDokumenList as $jenis)
                @php
                    $dokumen = $dokumenTerunggah[$jenis->id] ?? null;
                    $isUploaded = $dokumen !== null;
                @endphp

                <div style="background: var(--neutral-100); border: 1px solid {{ $isUploaded ? 'var(--green-600)' : 'var(--neutral-400)' }}; border-radius: 10px; padding: 1.25rem; display: flex; flex-direction: column; justify-content: space-between; gap: 1rem;">
                    <div>
                        <!-- Header Berkas -->
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.5rem; margin-bottom: 0.5rem;">
                            <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; padding: 0.2rem 0.5rem; border-radius: 4px;
                                background: {{ $jenis->wajib ? 'var(--amber-200)' : 'var(--neutral-200)' }};
                                color: {{ $jenis->wajib ? 'var(--amber-800)' : 'var(--neutral-700)' }};">
                                {{ $jenis->wajib ? 'Wajib Diunggah' : 'Opsional' }}
                            </span>

                            @if($isUploaded)
                                <span style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.75rem; font-weight: 700; color: var(--green-700); background: var(--green-100); padding: 0.2rem 0.5rem; border-radius: 4px;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    <span>Terunggah</span>
                                </span>
                            @else
                                <span style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-600); background: var(--neutral-200); padding: 0.2rem 0.5rem; border-radius: 4px;">
                                    Belum Diunggah
                                </span>
                            @endif
                        </div>

                        <!-- Judul Jenis Berkas -->
                        <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-800); margin: 0 0 0.5rem 0; line-height: 1.4;">
                            {{ $jenis->nama }}
                        </h4>

                        @if($isUploaded)
                            <!-- Info File Terunggah -->
                            <div style="background: var(--neutral-0); border: 1px solid var(--neutral-300); border-radius: 8px; padding: 0.75rem; margin-top: 0.5rem;">
                                <div style="display: flex; align-items: center; gap: 0.625rem; overflow: hidden;">
                                    <div style="width: 36px; height: 36px; background: var(--blue-tint); border-radius: 6px; display: flex; align-items: center; justify-content: center; color: var(--blue-700); flex-shrink: 0;">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                            <polyline points="14 2 14 8 20 8"></polyline>
                                        </svg>
                                    </div>
                                    <div style="overflow: hidden; flex: 1;">
                                        <div style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $dokumen->nama_asli }}">
                                            {{ $dokumen->nama_asli }}
                                        </div>
                                        <div style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.125rem;">
                                            {{ number_format($dokumen->ukuran / 1024, 1) }} KB &bull; Diperbarui {{ $dokumen->updated_at?->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Area Aksi Unggah / Unduh -->
                    <div>
                        @if($isUploaded)
                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                <a href="{{ $dokumen->getSignedDownloadUrl(5) }}" target="_blank" style="flex: 1; height: 38px; display: inline-flex; align-items: center; justify-content: center; gap: 0.375rem; background: var(--neutral-0); border: 1px solid var(--neutral-500); color: var(--neutral-800); border-radius: 6px; font-size: 0.8125rem; font-weight: 600; text-decoration: none;">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                    <span>Tinjau Berkas</span>
                                </a>

                                @if($canModify)
                                    <button type="button" onclick="document.getElementById('upload-form-{{ $jenis->id }}').style.display = 'block'; this.style.display = 'none';" style="height: 38px; padding: 0 0.875rem; background: var(--neutral-200); border: 1px solid var(--neutral-400); color: var(--neutral-700); border-radius: 6px; font-size: 0.8125rem; font-weight: 600; cursor: pointer;">
                                        Ganti Berkas
                                    </button>
                                @endif
                            </div>
                        @endif

                        @if($canModify)
                            <!-- Form Upload File (Muncul jika belum diunggah atau saat ganti berkas) -->
                            <div id="upload-form-{{ $jenis->id }}" style="{{ $isUploaded ? 'display: none;' : '' }} margin-top: 0.5rem;">
                                <form method="POST" action="{{ route('peserta.berkas.unggah', $pendaftaran) }}" enctype="multipart/form-data" onsubmit="return validateFileSize(this, {{ $maksMb }})">
                                    @csrf
                                    <input type="hidden" name="jenis_dokumen_id" value="{{ $jenis->id }}">

                                    <div class="drop-zone" style="border: 2px dashed var(--neutral-400); border-radius: 8px; padding: 1rem; text-align: center; background: var(--neutral-0); cursor: pointer; transition: all 0.15s ease;" onclick="this.querySelector('input[type=file]').click();">
                                        <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" required style="display: none;" onchange="handleFileSelected(this, {{ $jenis->id }}, {{ $maksMb }})">
                                        
                                        <div id="zone-prompt-{{ $jenis->id }}">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--blue-600)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 0.25rem auto;">
                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                <polyline points="17 8 12 3 7 8"></polyline>
                                                <line x1="12" y1="3" x2="12" y2="15"></line>
                                            </svg>
                                            <p style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin: 0;">Klik atau seret file ke sini</p>
                                            <small style="font-size: 0.75rem; color: var(--neutral-600);">PDF, JPG, PNG (Maks {{ $maksMb }} MB)</small>
                                        </div>

                                        <div id="zone-selected-{{ $jenis->id }}" style="display: none;">
                                            <p style="font-size: 0.8125rem; font-weight: 700; color: var(--blue-700); margin: 0;" class="selected-filename"></p>
                                            <small style="font-size: 0.75rem; color: var(--neutral-600);" class="selected-filesize"></small>
                                        </div>
                                    </div>

                                    <div id="file-error-{{ $jenis->id }}" style="display: none; color: var(--red-700); font-size: 0.75rem; font-weight: 600; margin-top: 0.375rem;"></div>

                                    <button type="submit" id="btn-submit-{{ $jenis->id }}" style="width: 100%; height: 38px; margin-top: 0.625rem; background: var(--blue-600); color: #FFFFFF; font-weight: 600; font-size: 0.8125rem; border: none; border-radius: 6px; cursor: pointer;">
                                        Unggah Berkas Ini
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- 5. BILAH AKSI BAWAH: AJUKAN PENDAFTARAN -->
    @if($canModify)
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-800); margin: 0 0 0.25rem 0;">
                    Kirim Pengajuan Pendaftaran Magang
                </h3>
                <p style="font-size: 0.8125rem; color: {{ $allWajibUploaded ? 'var(--green-700)' : 'var(--amber-700)' }}; margin: 0; font-weight: 600;">
                    @if($allWajibUploaded)
                        Seluruh berkas persyaratan wajib ({{ $wajibCount }}/{{ $wajibCount }}) telah terunggah. Pendaftaran siap dikirim ke tim verifikator.
                    @else
                        Masih ada {{ $wajibCount - $uploadedWajibCount }} berkas wajib yang belum diunggah. Lengkapi seluruh berkas wajib di atas untuk mengaktifkan tombol pengajuan.
                    @endif
                </p>
            </div>

            <form method="POST" action="{{ route('peserta.pendaftaran.ajukan', $pendaftaran) }}" onsubmit="return confirm('Apakah Anda yakin seluruh berkas yang diunggah sudah benar dan siap diverifikasi oleh admin?');">
                @csrf
                <button type="submit" {{ $allWajibUploaded ? '' : 'disabled' }} style="height: 46px; padding: 0 2rem; background: {{ $allWajibUploaded ? 'var(--blue-600)' : 'var(--neutral-300)' }}; color: {{ $allWajibUploaded ? '#FFFFFF' : 'var(--neutral-500)' }}; font-weight: 700; font-size: 0.875rem; border: none; border-radius: 8px; cursor: {{ $allWajibUploaded ? 'pointer' : 'not-allowed' }}; transition: background 0.15s; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: {{ $allWajibUploaded ? 'var(--shadow-subtle)' : 'none' }};">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="22" y1="2" x2="11" y2="13"></line>
                        <polyline points="22 2 15 22 11 13 2 9 22 2"></polyline>
                    </svg>
                    <span>{{ $statusValue === 'perlu_perbaikan' ? 'Ajukan Ulang Pendaftaran' : 'Ajukan Pendaftaran Sekarang' }}</span>
                </button>
            </form>
        </div>
    @endif

</div>

<script>
    // Penanganan Drag and Drop & Validasi Ukuran File (Maks MB)
    function handleFileSelected(input, jenisId, maksMb) {
        const file = input.files[0];
        const errorDiv = document.getElementById('file-error-' + jenisId);
        const promptDiv = document.getElementById('zone-prompt-' + jenisId);
        const selectedDiv = document.getElementById('zone-selected-' + jenisId);
        const submitBtn = document.getElementById('btn-submit-' + jenisId);

        errorDiv.style.display = 'none';
        errorDiv.innerText = '';

        if (!file) {
            promptDiv.style.display = 'block';
            selectedDiv.style.display = 'none';
            return;
        }

        const maksBytes = maksMb * 1024 * 1024;
        if (file.size > maksBytes) {
            errorDiv.innerText = `Ukuran file (${(file.size / (1024 * 1024)).toFixed(2)} MB) melebihi batas maksimal ${maksMb} MB.`;
            errorDiv.style.display = 'block';
            input.value = '';
            promptDiv.style.display = 'block';
            selectedDiv.style.display = 'none';
            submitBtn.disabled = true;
            return;
        }

        submitBtn.disabled = false;
        promptDiv.style.display = 'none';
        selectedDiv.style.display = 'block';
        selectedDiv.querySelector('.selected-filename').innerText = file.name;
        selectedDiv.querySelector('.selected-filesize').innerText = (file.size / 1024).toFixed(1) + ' KB';
    }

    function validateFileSize(form, maksMb) {
        const fileInput = form.querySelector('input[type=file]');
        if (fileInput && fileInput.files[0]) {
            const file = fileInput.files[0];
            const maksBytes = maksMb * 1024 * 1024;
            if (file.size > maksBytes) {
                alert(`Ukuran berkas melebihi batas maksimal ${maksMb} MB.`);
                return false;
            }
        }
        return true;
    }

    // Inisialisasi drag & drop highlight
    document.querySelectorAll('.drop-zone').forEach(zone => {
        zone.addEventListener('dragover', e => {
            e.preventDefault();
            zone.style.borderColor = 'var(--blue-600)';
            zone.style.background = 'var(--blue-tint)';
        });
        zone.addEventListener('dragleave', e => {
            e.preventDefault();
            zone.style.borderColor = 'var(--neutral-400)';
            zone.style.background = 'var(--neutral-0)';
        });
        zone.addEventListener('drop', e => {
            e.preventDefault();
            zone.style.borderColor = 'var(--neutral-400)';
            zone.style.background = 'var(--neutral-0)';
            const input = zone.querySelector('input[type=file]');
            if (e.dataTransfer.files.length) {
                input.files = e.dataTransfer.files;
                input.dispatchEvent(new Event('change'));
            }
        });
    });
</script>
@endsection
