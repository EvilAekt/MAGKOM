@extends('layouts.dashboard')

@section('title', 'Penempatan Magang - Admin MagKom Sleman')
@section('page_title', 'Manajemen Penempatan Peserta')
@section('page_description', 'Kelola penugasan pembimbing lapangan, pantau peserta aktif, dan kelola administrasi penempatan magang.')

@section('breadcrumbs')
    <span class="breadcrumb-current">Penempatan Magang</span>
@endsection

@section('page_actions')
    @if(isset($slotKhususList) && $slotKhususList->isNotEmpty())
        <button type="button" onclick="openManualModal()" style="display: inline-flex; align-items: center; gap: 0.5rem; background: var(--blue-600); color: #FFFFFF; border: none; padding: 0.55rem 1.25rem; border-radius: 8px; font-weight: 600; font-size: 0.875rem; cursor: pointer; box-shadow: var(--shadow-subtle);">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span>Penempatan Jalur Khusus</span>
        </button>
    @endif
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">

    <!-- Filter Bar Card -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle);">
        <form method="GET" action="{{ route('admin.penempatan.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="display: flex; flex-direction: column; gap: 0.375rem; flex: 1; min-width: 220px;">
                <label style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">Filter Status Penempatan</label>
                <select name="status" style="height: 42px; border: 1px solid var(--neutral-500); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: var(--neutral-0);">
                    <option value="">Semua Status Penempatan</option>
                    @foreach($statusEnum as $status)
                        <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" style="height: 42px; padding: 0 1.25rem; background: var(--blue-600); color: #FFFFFF; font-weight: 600; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer;">
                    Terapkan Filter
                </button>
                <a href="{{ route('admin.penempatan.index') }}" style="height: 42px; padding: 0 1rem; background: var(--neutral-200); color: var(--neutral-700); font-weight: 600; font-size: 0.875rem; text-decoration: none; border-radius: 8px; display: inline-flex; align-items: center;">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Data Table Card -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-subtle);">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-400); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-800); margin: 0;">Data Penempatan Peserta</h3>
            <span style="font-size: 0.8125rem; color: var(--neutral-600);">Total: {{ $penempatanList->total() }} Peserta</span>
        </div>

        @if($penempatanList->isEmpty())
            <div style="padding: 3.5rem 1.5rem; text-align: center; color: var(--neutral-600);">
                <div style="width: 56px; height: 56px; background: var(--neutral-200); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto; color: var(--neutral-500);">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
                <h4 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">Belum Ada Data Penempatan</h4>
                <p style="font-size: 0.875rem; max-width: 460px; margin: 0 auto;">Belum ada peserta magang yang ditempatkan pada filter yang dipilih.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                    <thead>
                        <tr style="background: var(--neutral-200); border-bottom: 1px solid var(--neutral-400); height: 52px;">
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Peserta Magang</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Pembimbing Lapangan</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Jalur / Bidang Dinas</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Periode Magang</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Status</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800); text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($penempatanList as $p)
                            @php
                                $statusVal = is_object($p->status) ? $p->status->value : $p->status;
                                $statusObj = is_object($p->status) ? $p->status : \App\Enums\StatusPenempatanEnum::tryFrom($p->status);
                                $badgeStyle = match($statusVal) {
                                    'aktif' => ['bg' => 'var(--green-100)', 'color' => 'var(--green-700)'],
                                    'selesai' => ['bg' => 'var(--blue-tint)', 'color' => 'var(--blue-700)'],
                                    'mengundurkan_diri' => ['bg' => 'var(--neutral-200)', 'color' => 'var(--neutral-700)'],
                                    'nonaktif' => ['bg' => 'var(--red-200)', 'color' => 'var(--red-700)'],
                                    default => ['bg' => 'var(--neutral-200)', 'color' => 'var(--neutral-700)'],
                                };
                            @endphp
                            <tr style="border-bottom: 1px solid var(--neutral-400); height: 58px;">
                                <td style="padding: 0.75rem 1rem;">
                                    <div style="font-weight: 700; color: var(--neutral-800);">{{ $p->peserta?->nama }}</div>
                                    <small style="color: var(--neutral-600);">{{ $p->peserta?->profil?->institusi ?? '-' }}</small>
                                </td>
                                <td style="padding: 0.75rem 1rem;">
                                    <div style="font-weight: 600; color: var(--neutral-800);">{{ $p->pembimbing?->nama ?? 'Belum Ditentukan' }}</div>
                                    @if($p->pembimbing)
                                        <div style="font-size: 0.75rem; color: var(--neutral-600);">
                                            Beban: <strong>{{ $p->pembimbing->mentee_aktif_count ?? 1 }} Mentee Aktif</strong>
                                        </div>
                                        <button type="button" onclick="openPindahModal({{ $p->id }}, '{{ addslashes($p->peserta?->nama) }}', {{ $p->pembimbing_id ?? 'null' }})" style="border: none; background: none; color: var(--blue-700); font-size: 0.75rem; font-weight: 600; cursor: pointer; padding: 0; text-decoration: underline;">
                                            Ganti Pembimbing
                                        </button>
                                    @endif
                                </td>
                                <td style="padding: 0.75rem 1rem; color: var(--neutral-700);">
                                    <div style="font-weight: 600;">{{ $p->bidang?->nama_bidang ?? 'Dinas Kominfo' }}</div>
                                    @if($p->slotKhusus)
                                        <span class="role-chip-badge" style="background: var(--amber-100); color: var(--amber-900); font-size: 0.6875rem; padding: 1px 6px;">
                                            Jalur Khusus: {{ $p->slotKhusus->nama_mitra }}
                                        </span>
                                    @else
                                        <small style="color: var(--neutral-600);">Jalur Reguler (Kuota Umum)</small>
                                    @endif
                                </td>
                                <td style="padding: 0.75rem 1rem; color: var(--neutral-700); font-size: 0.8125rem;">
                                    <div>{{ \Carbon\Carbon::parse($p->tgl_mulai)->translatedFormat('d M Y') }} s/d</div>
                                    <div>{{ \Carbon\Carbon::parse($p->tgl_selesai)->translatedFormat('d M Y') }}</div>
                                </td>
                                <td style="padding: 0.75rem 1rem;">
                                    <span class="role-chip-badge" style="background: {{ $badgeStyle['bg'] }}; color: {{ $badgeStyle['color'] }}; font-weight: 700;">
                                        {{ $statusObj ? $statusObj->label() : ucfirst($statusVal) }}
                                    </span>
                                </td>
                                <td style="padding: 0.75rem 1rem; text-align: right;">
                                    <div style="display: inline-flex; gap: 0.375rem; align-items: center;">
                                        <button type="button" onclick="openSuratPreview('{{ route('admin.penempatan.surat-diterima.pratinjau', $p) }}', '{{ route('admin.penempatan.surat-diterima', $p) }}', 'Surat Diterima - {{ addslashes($p->peserta?->nama) }}')" title="Pratinjau Surat Keterangan Diterima Langsung" style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.375rem 0.625rem; background: var(--blue-tint); color: var(--blue-700); border: 1px solid var(--blue-300); border-radius: 6px; font-weight: 600; font-size: 0.75rem; cursor: pointer;">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                            <span>Surat</span>
                                        </button>

                                        <a href="{{ route('admin.penempatan.surat-diterima', $p) }}" title="Unduh Surat Diterima PDF" style="padding: 0.375rem 0.625rem; background: var(--neutral-200); color: var(--neutral-800); text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 0.75rem;">
                                            Unduh
                                        </a>

                                        @if($statusVal === 'aktif')
                                            <button type="button" onclick="openBatalModal({{ $p->id }}, '{{ addslashes($p->peserta?->nama) }}')" style="padding: 0.375rem 0.625rem; background: var(--red-100); color: var(--red-700); border: 1px solid var(--red-300); border-radius: 6px; font-weight: 600; font-size: 0.75rem; cursor: pointer;">
                                                Batalkan
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($penempatanList->hasPages())
                <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--neutral-400);">
                    {{ $penempatanList->links() }}
                </div>
            @endif
        @endif
    </div>

</div>

<!-- MODAL PENEMPATAN MANUAL (JALUR KHUSUS) -->
<!-- MODAL PENEMPATAN MANUAL (F-06/F-07, AC-07-3) -->
<div id="modal-penempatan-manual" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; align-items: center; justify-content: center; padding: 1rem;">
    <div style="background: var(--neutral-0); border-radius: 12px; max-width: 580px; width: 100%; max-height: 90vh; overflow-y: auto; padding: 1.75rem; box-shadow: var(--shadow-subtle);">
        <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-800); margin: 0 0 0.25rem 0;">Input Penempatan Manual (F-06/F-07, AC-07-3)</h3>
        <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0 0 1.25rem 0;">
            Penempatan peserta magang berdasarkan rekomendasi mitra internal atau penempatan langsung.
        </p>

        <form method="POST" action="{{ route('admin.penempatan.manual') }}" id="form-penempatan-manual">
            @csrf

            <!-- Switcher Mode Peserta -->
            <div style="display: flex; gap: 0.5rem; margin-bottom: 1.25rem; background: var(--neutral-100); padding: 0.375rem; border-radius: 8px; border: 1px solid var(--neutral-300);">
                <button type="button" id="btn-mode-terdaftar" onclick="switchPesertaMode('terdaftar')" style="flex: 1; padding: 0.5rem; font-size: 0.8125rem; font-weight: 700; border: none; border-radius: 6px; cursor: pointer; background: var(--blue-600); color: #FFFFFF;">
                    Peserta Terdaftar
                </button>
                <button type="button" id="btn-mode-baru" onclick="switchPesertaMode('baru')" style="flex: 1; padding: 0.5rem; font-size: 0.8125rem; font-weight: 700; border: none; border-radius: 6px; cursor: pointer; background: transparent; color: var(--neutral-700);">
                    + Daftarkan Peserta Baru
                </button>
            </div>
            <input type="hidden" name="mode" id="input-mode-peserta" value="terdaftar">

            <!-- Panel Mode 1: Peserta Terdaftar -->
            <div id="panel-peserta-terdaftar" style="margin-bottom: 1rem;">
                <label style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); display: block; margin-bottom: 0.375rem;">
                    Pilih Calon Peserta Terdaftar <span style="color: var(--red-600);">*</span>
                </label>
                <select name="peserta_id" id="select-peserta-id" style="width: 100%; height: 42px; border: 1px solid var(--neutral-500); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                    <option value="">-- Pilih Peserta Aktif --</option>
                    @foreach($pesertaList ?? [] as $pes)
                        <option value="{{ $pes->id }}">{{ $pes->nama }} ({{ $pes->email }}) &bull; {{ $pes->profil?->institusi ?? '-' }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Panel Mode 2: Daftarkan Peserta Baru (AC-07-3) -->
            <div id="panel-peserta-baru" style="display: none; flex-direction: column; gap: 0.875rem; margin-bottom: 1.25rem; background: var(--blue-tint); border: 1px solid var(--blue-tint-line); border-radius: 8px; padding: 1rem;">
                <div style="font-size: 0.8125rem; font-weight: 700; color: var(--blue-700);">
                    Identitas Peserta Baru (Akun akan dibuat otomatis)
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-800);">Nama Lengkap <span style="color: var(--red-600);">*</span></label>
                        <input type="text" name="nama" id="input-nama-baru" placeholder="Nama siswa/mahasiswa" style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.625rem; font-size: 0.8125rem; background: #FFFFFF;">
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-800);">Email Aktif <span style="color: var(--red-600);">*</span></label>
                        <input type="email" name="email" id="input-email-baru" placeholder="email@sekolah.ac.id" style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.625rem; font-size: 0.8125rem; background: #FFFFFF;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-800);">NIM / NISN <span style="color: var(--red-600);">*</span></label>
                        <input type="text" name="nim_nisn" id="input-nim-baru" placeholder="Nomor induk siswa/mhs" style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.625rem; font-size: 0.8125rem; background: #FFFFFF;">
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-800);">Jenjang Pendidikan <span style="color: var(--red-600);">*</span></label>
                        <select name="jenjang" style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.625rem; font-size: 0.8125rem; background: #FFFFFF;">
                            <option value="s1">S1 (Strata 1)</option>
                            <option value="d3">D3 (Diploma 3)</option>
                            <option value="smk">SMK</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-800);">Asal Kampus / Sekolah <span style="color: var(--red-600);">*</span></label>
                        <input type="text" name="institusi" id="input-institusi-baru" placeholder="Nama Universitas / SMK" style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.625rem; font-size: 0.8125rem; background: #FFFFFF;">
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-800);">Jurusan Asal</label>
                        <select name="jurusan_id" style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.625rem; font-size: 0.8125rem; background: #FFFFFF;">
                            <option value="">-- Sesuai Master Jurusan --</option>
                            @foreach($jurusanList ?? [] as $j)
                                <option value="{{ $j->id }}">{{ $j->nama_jurusan }} ({{ strtoupper(is_object($j->kategori) ? $j->kategori->value : (string)$j->kategori) }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Pilihan Jalur Magang (Khusus vs Umum) -->
            <div style="display: flex; flex-direction: column; gap: 0.375rem; margin-bottom: 1rem;">
                <label style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">Jalur Alokasi Kuota <span style="color: var(--red-600);">*</span></label>
                <div style="display: flex; gap: 1rem;">
                    <label style="display: flex; align-items: center; gap: 0.375rem; font-size: 0.8125rem; cursor: pointer;">
                        <input type="radio" name="jalur" value="khusus" checked onchange="toggleJalurOptions('khusus')">
                        <strong>Jalur Khusus (Slot Internal)</strong>
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.375rem; font-size: 0.8125rem; cursor: pointer;">
                        <input type="radio" name="jalur" value="umum" onchange="toggleJalurOptions('umum')">
                        <strong>Jalur Umum (Kuota Jurusan)</strong>
                    </label>
                </div>
            </div>

            <!-- Field Slot Khusus -->
            <div id="wrapper-slot-khusus" style="display: flex; flex-direction: column; gap: 0.375rem; margin-bottom: 1rem;">
                <label style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">Alokasi Slot Khusus Periode <span style="color: var(--red-600);">*</span></label>
                <select name="slot_khusus_id" id="select-slot-khusus" style="height: 42px; border: 1px solid var(--neutral-500); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                    <option value="">-- Pilih Slot Khusus Periode --</option>
                    @foreach($slotKhususList as $sk)
                        <option value="{{ $sk->id }}">Periode {{ $sk->bulan_mulai?->translatedFormat('F Y') }} (Sisa: {{ $sk->sisa }}/{{ $sk->jumlah }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Field Kuota Umum -->
            <div id="wrapper-kuota-umum" style="display: none; flex-direction: column; gap: 0.375rem; margin-bottom: 1rem;">
                <label style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">Alokasi Kuota Jurusan <span style="color: var(--red-600);">*</span></label>
                <select name="kuota_id" id="select-kuota-id" style="height: 42px; border: 1px solid var(--neutral-500); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                    <option value="">-- Pilih Kuota Jurusan Tersedia --</option>
                    @foreach($kuotaList ?? [] as $ku)
                        <option value="{{ $ku->id }}">
                            {{ $ku->jurusan?->nama_jurusan }} &bull; {{ $ku->bulanMagang?->bulan_mulai?->translatedFormat('F Y') }} (Sisa: {{ max(0, $ku->jumlah - $ku->terisi) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.375rem; margin-bottom: 1rem;">
                <label style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">Tetapkan Pembimbing Lapangan <span style="color: var(--red-600);">*</span></label>
                <select name="pembimbing_id" required style="height: 42px; border: 1px solid var(--neutral-500); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                    <option value="">-- Pilih Pembimbing --</option>
                    @foreach($pembimbingList as $pb)
                        <option value="{{ $pb->id }}">{{ $pb->nama }} &bull; {{ $pb->mentee_aktif_count ?? 0 }} Mentee Aktif</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.375rem; margin-bottom: 1rem;">
                <label style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">Penempatan Bidang Dinas (Opsional)</label>
                <select name="bidang_id" style="height: 42px; border: 1px solid var(--neutral-500); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                    <option value="">-- Sesuai Bidang Pembimbing --</option>
                    @foreach($bidangList ?? [] as $bd)
                        <option value="{{ $bd->id }}">{{ $bd->nama_bidang }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
                <div style="display: flex; flex-direction: column; gap: 0.375rem;">
                    <label style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">Bulan Mulai <span style="color: var(--red-600);">*</span></label>
                    <input type="month" name="bulan_mulai" value="{{ date('Y-m') }}" required style="height: 42px; border: 1px solid var(--neutral-500); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                </div>
                <div style="display: flex; flex-direction: column; gap: 0.375rem;">
                    <label style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">Durasi (Bulan) <span style="color: var(--red-600);">*</span></label>
                    <input type="number" name="durasi_bulan" min="1" max="6" value="3" required style="height: 42px; border: 1px solid var(--neutral-500); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                <button type="button" onclick="closeManualModal()" style="padding: 0.5rem 1rem; background: var(--neutral-200); border: none; border-radius: 6px; font-weight: 600; font-size: 0.8125rem; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="padding: 0.5rem 1.25rem; background: var(--blue-600); color: #FFFFFF; border: none; border-radius: 6px; font-weight: 600; font-size: 0.8125rem; cursor: pointer;">
                    Tempatkan Peserta
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL GANTI PEMBIMBING -->
<div id="modal-pindah-pembimbing" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; align-items: center; justify-content: center;">
    <div style="background: var(--neutral-0); border-radius: 12px; max-width: 480px; width: 90%; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
        <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-800); margin: 0 0 0.5rem 0;">Ganti Pembimbing Lapangan</h3>
        <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0 0 1.25rem 0;" id="pindah-peserta-nama"></p>

        <form method="POST" id="form-pindah-pembimbing" action="">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 0.375rem; margin-bottom: 1.25rem;">
                <label style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">Pilih Pembimbing Baru</label>
                <select name="pembimbing_id" id="select-pembimbing-baru" required style="height: 42px; border: 1px solid var(--neutral-500); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                    @foreach($pembimbingList as $pb)
                        <option value="{{ $pb->id }}">{{ $pb->nama }} &bull; {{ $pb->mentee_aktif_count ?? 0 }} Mentee Aktif</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                <button type="button" onclick="closePindahModal()" style="padding: 0.5rem 1rem; background: var(--neutral-200); border: none; border-radius: 6px; font-weight: 600; font-size: 0.8125rem; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="padding: 0.5rem 1.25rem; background: var(--blue-600); color: #FFFFFF; border: none; border-radius: 6px; font-weight: 600; font-size: 0.8125rem; cursor: pointer;">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL BATALKAN PENEMPATAN -->
<div id="modal-batal-penempatan" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; align-items: center; justify-content: center;">
    <div style="background: var(--neutral-0); border-radius: 12px; max-width: 480px; width: 90%; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
        <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--red-700); margin: 0 0 0.5rem 0;">Pembatalan / Pengunduran Diri</h3>
        <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0 0 1.25rem 0;" id="batal-peserta-nama"></p>

        <form method="POST" id="form-batal-penempatan" action="">
            @csrf

            <div style="background: var(--amber-50, #FFFBEB); border: 1px solid var(--amber-300, #FCD34D); border-radius: 8px; padding: 0.75rem 0.875rem; margin-bottom: 1rem; font-size: 0.75rem; color: var(--amber-900, #78350F); line-height: 1.4;">
                <strong>Ketentuan Pembatalan & Pengunduran Diri (BR-07):</strong>
                <ul style="margin: 0.25rem 0 0 1rem; padding: 0;">
                    <li><strong>Sebelum tanggal mulai:</strong> Kuota jurusan/mitra akan otomatis dikembalikan.</li>
                    <li><strong>Sejak/setelah tanggal mulai:</strong> Kuota hangus (tidak dikembalikan) dan akun peserta otomatis dinonaktifkan.</li>
                </ul>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.375rem; margin-bottom: 1.25rem;">
                <label style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">Alasan Pembatalan / Pengunduran Diri (Wajib)</label>
                <textarea name="alasan" required rows="3" placeholder="Sebutkan alasan pembatalan atau pengunduran diri secara detail..." style="border: 1px solid var(--neutral-500); border-radius: 8px; padding: 0.75rem; font-size: 0.875rem; font-family: inherit;"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                <button type="button" onclick="closeBatalModal()" style="padding: 0.5rem 1rem; background: var(--neutral-200); border: none; border-radius: 6px; font-weight: 600; font-size: 0.8125rem; cursor: pointer;">
                    Kembali
                </button>
                <button type="submit" style="padding: 0.5rem 1.25rem; background: var(--red-700); color: #FFFFFF; border: none; border-radius: 6px; font-weight: 600; font-size: 0.8125rem; cursor: pointer;">
                    Konfirmasi Batalkan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL IN-APP PRATINJAU SURAT DITERIMA --}}
<div id="modal-surat-preview" onclick="if(event.target === this) closeSuratPreview()" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.65); z-index: 9999; align-items: center; justify-content: center; padding: 1.25rem;">
    <div style="background: var(--neutral-0); border-radius: 12px; width: 100%; max-width: 960px; max-height: 92vh; display: flex; flex-direction: column; padding: 1.25rem 1.5rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); overflow: hidden;">
        <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 1rem; border-bottom: 1px solid var(--neutral-300);">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 38px; height: 38px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: var(--green-100); color: var(--green-700);">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                </div>
                <div>
                    <h3 id="surat-modal-title" style="font-size: 1rem; font-weight: 700; color: var(--neutral-800); margin: 0;">Surat Keterangan Diterima Magang</h3>
                    <p style="font-size: 0.75rem; color: var(--neutral-600); margin: 2px 0 0 0;">Dokumen Resmi Dinas Kominfo Kabupaten Sleman</p>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <a id="surat-modal-download" href="#" style="display: inline-flex; align-items: center; gap: 0.375rem; background: var(--blue-600); color: #FFFFFF; padding: 0.4rem 0.875rem; border-radius: 6px; font-weight: 600; font-size: 0.8125rem; text-decoration: none;">
                    Unduh PDF
                </a>
                <button type="button" onclick="closeSuratPreview()" style="background: none; border: none; font-size: 1.5rem; line-height: 1; cursor: pointer; color: var(--neutral-600); padding: 0 0.25rem;">&times;</button>
            </div>
        </div>

        <div style="flex: 1; min-height: 60vh; max-height: 75vh; position: relative; background: var(--neutral-100); border: 1px solid var(--neutral-300); border-radius: 8px; margin-top: 0.875rem; overflow: hidden;">
            <iframe id="surat-iframe" src="about:blank" style="width: 100%; height: 75vh; border: none;"></iframe>
        </div>
    </div>
</div>

<script>
    function openManualModal() {
        const modal = document.getElementById('modal-penempatan-manual');
        if (modal) modal.style.display = 'flex';
    }
    function closeManualModal() {
        const modal = document.getElementById('modal-penempatan-manual');
        if (modal) modal.style.display = 'none';
    }

    function switchPesertaMode(mode) {
        const btnTerdaftar = document.getElementById('btn-mode-terdaftar');
        const btnBaru = document.getElementById('btn-mode-baru');
        const panelTerdaftar = document.getElementById('panel-peserta-terdaftar');
        const panelBaru = document.getElementById('panel-peserta-baru');
        const inputMode = document.getElementById('input-mode-peserta');
        const selectPeserta = document.getElementById('select-peserta-id');

        inputMode.value = mode;
        if (mode === 'baru') {
            btnBaru.style.background = 'var(--blue-600)';
            btnBaru.style.color = '#FFFFFF';
            btnTerdaftar.style.background = 'transparent';
            btnTerdaftar.style.color = 'var(--neutral-700)';
            panelTerdaftar.style.display = 'none';
            panelBaru.style.display = 'flex';
            if (selectPeserta) selectPeserta.removeAttribute('required');
        } else {
            btnTerdaftar.style.background = 'var(--blue-600)';
            btnTerdaftar.style.color = '#FFFFFF';
            btnBaru.style.background = 'transparent';
            btnBaru.style.color = 'var(--neutral-700)';
            panelTerdaftar.style.display = 'block';
            panelBaru.style.display = 'none';
            if (selectPeserta) selectPeserta.setAttribute('required', 'required');
        }
    }

    function toggleJalurOptions(jalur) {
        const wrapperSlot = document.getElementById('wrapper-slot-khusus');
        const wrapperKuota = document.getElementById('wrapper-kuota-umum');
        const selectSlot = document.getElementById('select-slot-khusus');
        const selectKuota = document.getElementById('select-kuota-id');

        if (jalur === 'umum') {
            wrapperSlot.style.display = 'none';
            wrapperKuota.style.display = 'flex';
            if (selectSlot) selectSlot.removeAttribute('required');
            if (selectKuota) selectKuota.setAttribute('required', 'required');
        } else {
            wrapperSlot.style.display = 'flex';
            wrapperKuota.style.display = 'none';
            if (selectSlot) selectSlot.setAttribute('required', 'required');
            if (selectKuota) selectKuota.removeAttribute('required');
        }
    }

    function openPindahModal(penempatanId, namaPeserta, pembimbingId) {
        document.getElementById('pindah-peserta-nama').innerText = 'Peserta: ' + namaPeserta;
        document.getElementById('form-pindah-pembimbing').action = '/admin/penempatan/' + penempatanId + '/pindah-pembimbing';
        if (pembimbingId) {
            document.getElementById('select-pembimbing-baru').value = pembimbingId;
        }
        document.getElementById('modal-pindah-pembimbing').style.display = 'flex';
    }

    function closePindahModal() {
        document.getElementById('modal-pindah-pembimbing').style.display = 'none';
    }

    function openBatalModal(penempatanId, namaPeserta) {
        document.getElementById('batal-peserta-nama').innerText = 'Peserta: ' + namaPeserta;
        document.getElementById('form-batal-penempatan').action = '/admin/penempatan/' + penempatanId + '/batalkan';
        document.getElementById('modal-batal-penempatan').style.display = 'flex';
    }

    function closeBatalModal() {
        document.getElementById('modal-batal-penempatan').style.display = 'none';
    }

    function openSuratPreview(previewUrl, downloadUrl, title) {
        const modal = document.getElementById('modal-surat-preview');
        const iframe = document.getElementById('surat-iframe');
        const titleEl = document.getElementById('surat-modal-title');
        const downloadEl = document.getElementById('surat-modal-download');

        if (titleEl) titleEl.innerText = title;
        if (downloadEl) downloadEl.href = downloadUrl;
        if (iframe) iframe.src = previewUrl;
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    function closeSuratPreview() {
        const modal = document.getElementById('modal-surat-preview');
        const iframe = document.getElementById('surat-iframe');

        if (iframe) iframe.src = 'about:blank';
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeSuratPreview();
            closeManualModal();
            closePindahModal();
            closeBatalModal();
        }
    });
</script>
@endsection
