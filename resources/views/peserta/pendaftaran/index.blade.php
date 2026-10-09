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
        <div class="card" style="border-left: 4px solid var(--amber-700); background: var(--amber-100);">
            <div style="display: flex; align-items: flex-start; gap: 16px;">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--amber-700); flex-shrink: 0; margin-top: 2px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div style="flex: 1;">
                    <h3 style="font-size: 16px; font-weight: 700; color: var(--amber-700); margin-bottom: 4px;">
                        Lengkapi Biodata Profil Terlebih Dahulu
                    </h3>
                    <p style="font-size: 14px; color: var(--neutral-700); margin-bottom: 16px;">
                        Sesuai ketentuan, Anda harus melengkapi biodata profil (institusi sekolah/kampus, NISN/NIM, jurusan, dan kontak darurat) sebelum dapat membuat pengajuan magang baru.
                    </p>
                    <a href="{{ route('peserta.profil.show') }}" class="btn btn-primary" style="height: 40px; padding: 0 16px;">
                        Lengkapi Profil Sekarang →
                    </a>
                </div>
            </div>
        </div>
    @endif

    <div class="header-actions">
        <div>
            <h2 style="font-size: 20px; font-weight: 700; color: var(--neutral-800);">Riwayat Pendaftaran Magang</h2>
            <p style="font-size: 13px; color: var(--neutral-600); margin-top: 2px;">
                Daftar pengajuan magang yang telah Anda daftarkan di Dinas Kominfo Sleman.
            </p>
        </div>

        @if($profilLengkap)
            <button type="button" class="btn btn-primary" onclick="openModal()">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                </svg>
                Buat Pengajuan Baru
            </button>
        @else
            <button type="button" class="btn btn-primary" disabled title="Lengkapi profil terlebih dahulu">
                Buat Pengajuan Baru (Terkunci)
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
                @if($profilLengkap)
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
                                        <span class="badge badge-warning">Berkas Belum Lengkap</span>
                                    @elseif($val === 'diajukan')
                                        <span class="badge badge-info">Diajukan (Verifikasi)</span>
                                    @elseif($val === 'waiting_list')
                                        <span class="badge badge-warning">Waiting List</span>
                                    @elseif($val === 'perlu_perbaikan')
                                        <span class="badge badge-danger">Perlu Perbaikan</span>
                                    @elseif($val === 'diterima')
                                        <span class="badge badge-success">Diterima</span>
                                    @elseif($val === 'ditolak')
                                        <span class="badge badge-danger">Ditolak</span>
                                    @elseif($val === 'dibatalkan')
                                        <span class="badge badge-neutral">Dibatalkan</span>
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

{{-- Modal Pengajuan Pendaftaran Magang Baru --}}
@if($profilLengkap)
<div class="modal" id="pendaftaranModal" onclick="if(event.target === this) closeModal()">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title">Form Pengajuan Magang Baru</h3>
            <button type="button" class="modal-close" onclick="closeModal()">&times;</button>
        </div>

        <form action="{{ route('peserta.pendaftaran.store') }}" method="POST">
            @csrf

            <div style="display: flex; flex-direction: column; gap: 18px;">
                {{-- Info Jurusan Profil --}}
                <div style="padding: 12px 16px; background: var(--blue-tint); border-radius: var(--radius-default); border: 1px solid var(--blue-tint-line);">
                    <div style="font-size: 12px; color: var(--blue-700); font-weight: 700; text-transform: uppercase;">Jurusan Terdaftar Anda:</div>
                    <div style="font-size: 14px; font-weight: 700; color: var(--neutral-800); margin-top: 2px;">
                        {{ $profil->jurusan?->nama_jurusan }} ({{ strtoupper($profil->jenjang?->value ?? $profil->jenjang) }})
                    </div>
                </div>

                <div class="form-group">
                    <label for="kuota_id" class="form-label">Pilih Periode Bulan Magang & Kuota <span class="req" style="color: var(--red-600);">*</span></label>
                    <select id="kuota_id" name="kuota_id" class="form-control" required onchange="handleKuotaChange(this)">
                        <option value="">-- Pilih Periode Bulan Magang --</option>
                        @foreach($kuotaList as $kuota)
                            @php
                                $sisa = max(0, $kuota->jumlah - $kuota->terisi);
                            @endphp
                            <option value="{{ $kuota->id }}"
                                    data-bulan-id="{{ $kuota->bulan_magang_id }}"
                                    {{ old('kuota_id') == $kuota->id ? 'selected' : '' }}>
                                {{ $kuota->bulanMagang?->bulan_mulai?->translatedFormat('F Y') }}
                                (Sisa Kuota: {{ $sisa > 0 ? $sisa . ' kursi' : 'Penuh (Waiting List)' }})
                            </option>
                        @endforeach
                    </select>
                    <span style="font-size: 12px; color: var(--neutral-600);">
                        Hanya menampilkan periode bulan yang dibuka untuk jurusan Anda.
                    </span>
                </div>

                {{-- Hidden input bulan_magang_id yang diisi otomatis via JS dari select kuota_id --}}
                <input type="hidden" id="bulan_magang_id" name="bulan_magang_id" value="{{ old('bulan_magang_id') }}">

                <div class="form-group">
                    <label for="durasi_bulan" class="form-label">Rencana Durasi Magang <span class="req" style="color: var(--red-600);">*</span></label>
                    <select id="durasi_bulan" name="durasi_bulan" class="form-control" required>
                        <option value="1" {{ old('durasi_bulan') == 1 ? 'selected' : '' }}>1 Bulan</option>
                        <option value="2" {{ old('durasi_bulan') == 2 ? 'selected' : '' }}>2 Bulan</option>
                        <option value="3" {{ old('durasi_bulan', 3) == 3 ? 'selected' : '' }}>3 Bulan (Standar)</option>
                        <option value="4" {{ old('durasi_bulan') == 4 ? 'selected' : '' }}>4 Bulan</option>
                        <option value="5" {{ old('durasi_bulan') == 5 ? 'selected' : '' }}>5 Bulan</option>
                        <option value="6" {{ old('durasi_bulan') == 6 ? 'selected' : '' }}>6 Bulan (Maksimal)</option>
                    </select>
                    <span style="font-size: 12px; color: var(--neutral-600);">
                        Sesuai rekomendasi surat pengantar institusi pendidikan Anda.
                    </span>
                </div>
            </div>

            <div style="margin-top: 28px; display: flex; justify-content: flex-end; gap: 12px;">
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
        document.getElementById('pendaftaranModal').classList.add('open');
    }
    function closeModal() {
        document.getElementById('pendaftaranModal').classList.remove('open');
    }
    function handleKuotaChange(select) {
        const selectedOption = select.options[select.selectedIndex];
        const bulanId = selectedOption.getAttribute('data-bulan-id');
        document.getElementById('bulan_magang_id').value = bulanId || '';
    }
    document.addEventListener('DOMContentLoaded', () => {
        const select = document.getElementById('kuota_id');
        if (select && select.value) {
            handleKuotaChange(select);
        }
    });
</script>
@endpush
@endsection
