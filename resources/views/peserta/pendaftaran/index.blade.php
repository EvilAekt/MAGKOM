@extends('layouts.peserta')

@section('title', 'Pendaftaran Magang')
@section('page_title', 'Pendaftaran Magang')

@push('styles')
<style>
    .header-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }
    .table-container {
        overflow-x: auto;
    }
    .table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }
    .table th {
        background: var(--neutral-200);
        padding: 12px 16px;
        font-size: 13px;
        font-weight: 700;
        color: var(--neutral-800);
        border-bottom: 1px solid var(--neutral-400);
    }
    .table td {
        padding: 16px;
        font-size: 14px;
        border-bottom: 1px solid var(--neutral-200);
        color: var(--neutral-800);
        vertical-align: middle;
    }
    .table tr:hover td {
        background: var(--neutral-100);
    }
    .modal {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 100;
        background: rgba(29, 37, 48, 0.6);
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .modal.open {
        display: flex;
    }
    .modal-box {
        background: #ffffff;
        border-radius: var(--radius-lg);
        width: 100%;
        max-width: 580px;
        padding: 28px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 14px;
        border-bottom: 1px solid var(--neutral-200);
    }
    .modal-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--neutral-800);
    }
    .modal-close {
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        color: var(--neutral-500);
    }
    .empty-state {
        text-align: center;
        padding: 48px 24px;
    }
    .empty-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 16px auto;
        background: var(--blue-tint);
        color: var(--blue-600);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .empty-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--neutral-800);
        margin-bottom: 6px;
    }
    .empty-desc {
        font-size: 14px;
        color: var(--neutral-600);
        max-width: 420px;
        margin: 0 auto 20px auto;
    }
</style>
@endpush

@section('content')
<div>
    {{-- Validasi Kelengkapan Profil (AC-03-1) --}}
    @php
        $profilLengkap = $profil && $profil->nim_nisn && $profil->institusi && $profil->jurusan_id && $profil->no_hp;
    @endphp

    @if(!$profilLengkap)
        <div class="alert alert-warning" style="margin-bottom: 24px;">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div style="flex: 1;">
                <h3 style="font-size: 15px; font-weight: 700; color: var(--amber-700); margin-bottom: 4px;">
                    Lengkapi Biodata Profil Terlebih Dahulu
                </h3>
                <p style="font-size: 13px; color: var(--neutral-700); margin-bottom: 12px; line-height: 1.5;">
                    Sesuai ketentuan, Anda harus melengkapi biodata profil (institusi sekolah/kampus, NISN/NIM, jurusan, dan kontak darurat) sebelum dapat membuat pengajuan magang baru.
                </p>
                <a href="{{ route('peserta.profil.show') }}" class="btn btn-primary" style="height: 38px; padding: 0 16px; font-size: 13px;">
                    Lengkapi Profil Sekarang →
                </a>
            </div>
        </div>
    @endif

    {{-- Banner Peringatan 1 Pendaftaran Aktif (BR-03, BR-17) --}}
    @if($pendaftaranAktif)
        <div class="alert alert-info" style="margin-bottom: 24px; border-left: 4px solid var(--blue-600); display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
            <div style="display: flex; align-items: flex-start; gap: 12px; flex: 1;">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--blue-700); flex-shrink: 0; margin-top: 2px;">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                <div>
                    <strong style="color: var(--blue-800); font-size: 15px;">Anda Memiliki Pengajuan Magang Aktif: #REG-{{ str_pad((string)$pendaftaranAktif->id, 5, '0', STR_PAD_LEFT) }}</strong>
                    <p style="font-size: 13px; color: var(--neutral-700); margin-top: 4px; line-height: 1.5;">
                        Periode <strong>{{ $pendaftaranAktif->bulanMagang?->bulan_mulai?->translatedFormat('F Y') }}</strong> ({{ $pendaftaranAktif->kuota?->jurusan?->nama_jurusan }}).
                        Sesuai aturan sistem (BR-03 & BR-17), peserta hanya boleh memiliki 1 pengajuan aktif. Anda belum dapat membuat pengajuan baru sampai pengajuan ini selesai diproses atau dibatalkan.
                    </p>
                </div>
            </div>
            <a href="{{ route('peserta.pendaftaran.detail', $pendaftaranAktif) }}" class="btn btn-secondary" style="height: 38px; padding: 0 16px; font-size: 13px;">
                Buka Detail Pengajuan Aktif →
            </a>
        </div>
    @endif

    <div class="header-actions">
        <div>
            <h2 style="font-size: 20px; font-weight: 700; color: var(--neutral-800);">Riwayat Pendaftaran Magang</h2>
            <p style="font-size: 13px; color: var(--neutral-600); margin-top: 2px;">
                Daftar pengajuan magang yang telah Anda daftarkan di Dinas Kominfo Sleman.
            </p>
        </div>

        @if($pendaftaranAktif)
            <button type="button" class="btn btn-secondary" disabled title="Anda masih memiliki pengajuan aktif (BR-03 / BR-17)" style="opacity: 0.65; cursor: not-allowed;">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0110 0v4"></path>
                </svg>
                Buat Pengajuan Baru (Terkunci)
            </button>
        @elseif($profilLengkap)
            <button type="button" class="btn btn-primary" onclick="openModal()">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                </svg>
                Buat Pengajuan Baru
            </button>
        @else
            <button type="button" class="btn btn-secondary" disabled title="Lengkapi profil terlebih dahulu" style="opacity: 0.65; cursor: not-allowed;">
                Buat Pengajuan Baru (Profil Belum Lengkap)
            </button>
        @endif
    </div>

    {{-- Tabel Riwayat Pendaftaran --}}
    <div class="card" style="padding: 0; overflow: hidden;">
        @if($pendaftaranList->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">
                    <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="empty-title">Belum Ada Pengajuan Magang</h3>
                <p class="empty-desc">
                    Anda belum pernah mengajukan permohonan magang. Klik tombol "Buat Pengajuan Baru" di atas untuk memilih periode bulan dan kuota magang.
                </p>
                @if($profilLengkap && !$pendaftaranAktif)
                    <button type="button" class="btn btn-primary" onclick="openModal()">
                        Daftar Magang Sekarang
                    </button>
                @endif
            </div>
        @else
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No. Pendaftaran</th>
                            <th>Periode Magang</th>
                            <th>Jurusan & Durasi</th>
                            <th>Status Pengajuan</th>
                            <th>Tanggal Buat</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendaftaranList as $item)
                            <tr>
                                <td>
                                    <strong style="color: var(--blue-700);">#REG-{{ str_pad((string)$item->id, 5, '0', STR_PAD_LEFT) }}</strong>
                                </td>
                                <td>
                                    <strong>{{ $item->bulanMagang?->bulan_mulai?->translatedFormat('F Y') }}</strong>
                                    <div style="font-size: 12px; color: var(--neutral-600);">
                                        Mulai: {{ $item->bulanMagang?->bulan_mulai ? $item->bulanMagang->bulan_mulai->translatedFormat('d M Y') : '-' }}
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $item->kuota?->jurusan?->nama_jurusan ?? '-' }}</div>
                                    <div style="font-size: 12px; color: var(--neutral-600);">
                                        Durasi: <strong>{{ $item->durasi_bulan }} Bulan</strong>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $val = $item->status->value ?? (string)$item->status;
                                    @endphp
                                    @if($val === 'berkas_belum_lengkap')
                                        <span class="badge badge-neutral">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-dasharray="4 4"></circle></svg>
                                            Berkas Belum Lengkap
                                        </span>
                                    @elseif($val === 'diajukan')
                                        <span class="badge badge-warning">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                            Menunggu Verifikasi
                                        </span>
                                    @elseif($val === 'waiting_list')
                                        <span class="badge badge-info">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                            Waiting List
                                        </span>
                                    @elseif($val === 'perlu_perbaikan')
                                        <span class="badge badge-warning">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                            Perlu Perbaikan
                                        </span>
                                    @elseif($val === 'diterima')
                                        <span class="badge badge-success">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Diterima
                                        </span>
                                    @elseif($val === 'ditolak')
                                        <span class="badge badge-danger">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                                            Ditolak
                                        </span>
                                    @elseif($val === 'dibatalkan')
                                        <span class="badge badge-neutral">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                            Dibatalkan
                                        </span>
                                    @else
                                        <span class="badge badge-neutral">{{ $val }}</span>
                                    @endif
                                </td>
                                <td style="font-size: 13px; color: var(--neutral-600);">
                                    {{ $item->created_at->translatedFormat('d M Y, H:i') }}
                                </td>
                                <td style="text-align: right;">
                                    <a href="{{ route('peserta.pendaftaran.detail', $item) }}" class="btn btn-secondary" style="height: 36px; padding: 0 14px; font-size: 13px;">
                                        Lihat Detail & Berkas →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- Modal Pengajuan Pendaftaran Magang Baru (AC-03-2) --}}
@if($profilLengkap && !$pendaftaranAktif)
<div class="modal" id="pendaftaranModal" onclick="if(event.target === this) closeModal()">
    <div class="modal-box">
        <div class="modal-header">
            <div>
                <h3 class="modal-title">Form Pengajuan Magang Baru</h3>
                <p style="font-size: 13px; color: var(--neutral-600); margin-top: 2px;">
                    Pilih periode magang dan durasi pelaksanaan yang Anda ajukan.
                </p>
            </div>
            <button type="button" class="modal-close" onclick="closeModal()">&times;</button>
        </div>

        <form action="{{ route('peserta.pendaftaran.store') }}" method="POST" id="formPendaftaranBaru" onsubmit="handleFormSubmit(this)">
            @csrf

            <div style="display: flex; flex-direction: column; gap: 18px;">
                {{-- 1. Info Jurusan Profil Peserta (AC-03-2) --}}
                <div style="padding: 14px 16px; background: var(--blue-tint); border-radius: var(--radius-default); border: 1px solid var(--blue-tint-line);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 11px; color: var(--blue-700); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Jurusan Terdaftar Anda</span>
                        <span class="badge badge-info" style="font-size: 11px; padding: 2px 8px;">{{ strtoupper($profil->jenjang?->value ?? $profil->jenjang) }}</span>
                    </div>
                    <div style="font-size: 15px; font-weight: 700; color: var(--neutral-800); margin-top: 4px;">
                        {{ $profil->jurusan?->nama_jurusan }}
                    </div>
                    <div style="font-size: 12px; color: var(--neutral-600); margin-top: 2px;">
                        Institusi: {{ $profil->institusi }}
                    </div>
                </div>

                {{-- 2. Dropdown Bulan Magang & Kuota Aktif (AC-03-2) --}}
                <div class="form-group">
                    <label for="kuota_id" class="form-label" style="font-size: 14px; font-weight: 600; color: var(--neutral-800);">
                        Pilih Periode Bulan Magang <span class="req" style="color: var(--red-600);">*</span>
                    </label>
                    <select id="kuota_id" name="kuota_id" class="form-control" required onchange="handleKuotaChange(this)" style="height: 44px; font-size: 14px; border: 1px solid var(--neutral-400); border-radius: var(--radius-default); padding: 0 12px; width: 100%;">
                        <option value="">-- Pilih Periode Bulan Magang --</option>
                        @foreach($kuotaList as $kuota)
                            @php
                                $sisa = max(0, $kuota->jumlah - $kuota->terisi);
                                $bulanMulaiIso = $kuota->bulanMagang?->bulan_mulai?->format('Y-m-d');
                            @endphp
                            <option value="{{ $kuota->id }}"
                                    data-bulan-id="{{ $kuota->bulan_magang_id }}"
                                    data-bulan-mulai="{{ $bulanMulaiIso }}"
                                    {{ old('kuota_id') == $kuota->id ? 'selected' : '' }}>
                                {{ $kuota->bulanMagang?->bulan_mulai?->translatedFormat('F Y') }}
                                (Sisa Kuota: {{ $sisa > 0 ? $sisa . ' kursi' : 'Penuh (Waiting List)' }})
                            </option>
                        @endforeach
                    </select>
                    <span style="font-size: 12px; color: var(--neutral-600); margin-top: 4px; display: block;">
                        Hanya menampilkan bulan magang yang berstatus Tersedia untuk jurusan Anda.
                    </span>
                </div>

                {{-- Hidden input bulan_magang_id yang diisi otomatis via JS dari select kuota_id --}}
                <input type="hidden" id="bulan_magang_id" name="bulan_magang_id" value="{{ old('bulan_magang_id') }}">

                {{-- 3. Dropdown Durasi Magang 1 s/d 6 Bulan (BR-08) --}}
                <div class="form-group">
                    <label for="durasi_bulan" class="form-label" style="font-size: 14px; font-weight: 600; color: var(--neutral-800);">
                        Rencana Durasi Magang <span class="req" style="color: var(--red-600);">*</span>
                    </label>
                    <select id="durasi_bulan" name="durasi_bulan" class="form-control" required onchange="updateEstimasiPeriode()" style="height: 44px; font-size: 14px; border: 1px solid var(--neutral-400); border-radius: var(--radius-default); padding: 0 12px; width: 100%;">
                        <option value="1" {{ old('durasi_bulan') == 1 ? 'selected' : '' }}>1 Bulan</option>
                        <option value="2" {{ old('durasi_bulan') == 2 ? 'selected' : '' }}>2 Bulan</option>
                        <option value="3" {{ old('durasi_bulan', 3) == 3 ? 'selected' : '' }}>3 Bulan (Standar)</option>
                        <option value="4" {{ old('durasi_bulan') == 4 ? 'selected' : '' }}>4 Bulan</option>
                        <option value="5" {{ old('durasi_bulan') == 5 ? 'selected' : '' }}>5 Bulan</option>
                        <option value="6" {{ old('durasi_bulan') == 6 ? 'selected' : '' }}>6 Bulan (Maksimal)</option>
                    </select>
                    <span style="font-size: 12px; color: var(--neutral-600); margin-top: 4px; display: block;">
                        Sesuai rekomendasi surat pengantar institusi pendidikan Anda (1 s/d 6 bulan).
                    </span>
                </div>

                {{-- Live Preview Rentang Tanggal (BR-08) --}}
                <div id="previewPeriodeBox" style="display: none; padding: 12px 14px; background: var(--neutral-100); border-radius: var(--radius-default); border: 1px dashed var(--neutral-400); font-size: 13px;">
                    <div style="font-weight: 700; color: var(--neutral-800);">📅 Estimasi Rentang Pelaksanaan:</div>
                    <div id="previewPeriodeText" style="color: var(--blue-700); font-weight: 600; margin-top: 2px;"></div>
                </div>
            </div>

            <div style="margin-top: 28px; display: flex; justify-content: flex-end; gap: 12px; padding-top: 16px; border-top: 1px solid var(--neutral-200);">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn btn-primary" id="btnSubmitPendaftaran">
                    Lanjut ke Pengunggahan Berkas →
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@push('scripts')
<script>
    function openModal() {
        const modal = document.getElementById('pendaftaranModal');
        if (modal) modal.classList.add('open');
    }
    function closeModal() {
        const modal = document.getElementById('pendaftaranModal');
        if (modal) modal.classList.remove('open');
    }
    function handleKuotaChange(select) {
        const selectedOption = select.options[select.selectedIndex];
        const bulanId = selectedOption ? selectedOption.getAttribute('data-bulan-id') : '';
        document.getElementById('bulan_magang_id').value = bulanId || '';
        updateEstimasiPeriode();
    }
    function updateEstimasiPeriode() {
        const selectKuota = document.getElementById('kuota_id');
        const selectDurasi = document.getElementById('durasi_bulan');
        const previewBox = document.getElementById('previewPeriodeBox');
        const previewText = document.getElementById('previewPeriodeText');

        if (!selectKuota || !selectDurasi || !previewBox || !previewText) return;

        const selectedOption = selectKuota.options[selectKuota.selectedIndex];
        const bulanMulaiStr = selectedOption ? selectedOption.getAttribute('data-bulan-mulai') : null;
        const durasi = parseInt(selectDurasi.value, 10);

        if (bulanMulaiStr && durasi) {
            const startDate = new Date(bulanMulaiStr);
            // End date = last day of month + (durasi - 1)
            const endDate = new Date(startDate.getFullYear(), startDate.getMonth() + durasi, 0);

            const options = { day: 'numeric', month: 'long', year: 'numeric' };
            const startFmt = startDate.toLocaleDateString('id-ID', options);
            const endFmt = endDate.toLocaleDateString('id-ID', options);

            previewText.textContent = `${startFmt} s/d ${endFmt} (${durasi} Bulan)`;
            previewBox.style.display = 'block';
        } else {
            previewBox.style.display = 'none';
        }
    }
    function handleFormSubmit(form) {
        const btn = document.getElementById('btnSubmitPendaftaran');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `<span style="display:inline-block;width:14px;height:14px;border:2px solid #fff;border-top-color:transparent;border-radius:50%;animation:spin 0.6s linear infinite;margin-right:8px;"></span> Menyimpan...`;
        }
    }
    document.addEventListener('DOMContentLoaded', () => {
        const select = document.getElementById('kuota_id');
        if (select && select.value) {
            handleKuotaChange(select);
        }
    });
</script>
<style>
@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>
@endpush
@endsection
