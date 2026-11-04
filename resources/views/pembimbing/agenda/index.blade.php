@extends('layouts.dashboard')

@section('title', 'Agenda Mentoring & Notulensi Bimbingan - MagKom Sleman')
@section('topbar_title', 'Agenda Mentoring & Notulensi')
@section('content_area_class', 'gentelella-fluid-canvas')

@push('styles')
    @vite(['resources/css/pages/pembimbing/agenda-index.css'])
@endpush

@section('content')
<div class="classroom-container">

    {{-- 1. Classroom Hero Banner Khas Sleman MagKom --}}
    @php
        $authUser = auth()->user();
        $bidangNama = $authUser->bidang?->nama_bidang ?? 'Dinas Komunikasi dan Informatika Sleman';
    @endphp

    <div class="classroom-banner">
        <div class="banner-top-row">
            <div>
                <h1 class="banner-title">Agenda Mentoring &amp; Notulensi</h1>
                <div class="banner-subtitle">
                    <span>Bidang: <strong>{{ $bidangNama }}</strong></span>
                    <span>&bull;</span>
                    <span>Pembimbing: <strong>{{ $authUser->nama }}</strong></span>
                    <span>&bull;</span>
                    <span><strong>{{ $totalTerjadwal }} Agenda Terjadwal</strong></span>
                </div>
            </div>
            <div>
                <button type="button" id="btnOpenCreateAgenda" class="btn-create-task">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line><line x1="12" y1="14" x2="12" y2="18"></line><line x1="10" y1="16" x2="14" y2="16"></line></svg>
                    <span>+ Jadwalkan Mentoring Baru</span>
                </button>
            </div>
        </div>
    </div>

    {{-- 2. Tile Stats MagKom Gentelella (4 Metrik Agenda) --}}
    <div class="tiles-grid">
        <x-gentelella.tile-stat
            title="Total Agenda"
            value="{{ $totalAgenda }}"
            color="teal"
            trendDirection="up"
            trendText="{{ $totalAgenda }} Sesi"
            subText="Seluruh riwayat bimbingan"
            :sparkBars="[30, 40, 50, 65, 70, 80, 85, 92, 100]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <x-gentelella.tile-stat
            title="Agenda Terjadwal"
            value="{{ $totalTerjadwal }}"
            color="blue"
            trendType="info"
            trendDirection="up"
            trendText="{{ $totalTerjadwal }} Sesi"
            subText="Sesi bimbingan mendatang"
            :sparkBars="[40, 50, 65, 55, 70, 60, 75, 80, 85]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <x-gentelella.tile-stat
            title="Selesai Terlaksana"
            value="{{ $totalSelesai }}"
            color="teal"
            trendType="success"
            trendDirection="up"
            trendText="{{ $persenSelesai }}%"
            subText="Notulensi tercatat rapi"
            :sparkBars="[20, 35, 45, 60, 70, 75, 85, 90, 98]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <x-gentelella.tile-stat
            title="Metode Sesi"
            value="{{ $totalTatapMuka + $totalDaring }}"
            color="yellow"
            trendType="warning"
            trendText="{{ $totalTatapMuka }} Luring / {{ $totalDaring }} Daring"
            subText="Distribusi tatap muka &amp; daring"
            :sparkBars="[50, 60, 45, 55, 70, 65, 80, 75, 70]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>
    </div>

    {{-- 3. Daftar Agenda Bimbingan Khas Classroom --}}
    <x-gentelella.x-panel title="Daftar Jadwal &amp; Notulensi Sesi Mentoring" subtitle="Sesi bimbingan aktif dan rekam notulensi pelaksanaan">
        <div class="table-agenda-wrapper">
            <table class="table-agenda">
                <thead>
                    <tr>
                        <th>Mentee Magang</th>
                        <th>Kegiatan Bimbingan</th>
                        <th>Metode &amp; Lokasi</th>
                        <th>Waktu Pelaksanaan</th>
                        <th>Status</th>
                        <th>Notulensi Arahan</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($agendaList as $agenda)
                        @php
                            $initials = collect(explode(' ', $agenda->penempatan?->peserta?->nama ?? 'P'))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
                        @endphp
                        <tr>
                            <td>
                                <div class="mentee-identity-flex">
                                    <div class="avatar-initials-circle">
                                        {{ strtoupper($initials) }}
                                    </div>
                                    <div>
                                        <div style="font-weight: 700; color: #0F172A;">
                                            {{ $agenda->penempatan?->peserta?->nama }}
                                        </div>
                                        <div style="font-size: 0.75rem; color: #64748B;">
                                            {{ $agenda->penempatan?->peserta?->profil?->institusi ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="agenda-title-text">{{ $agenda->judul_kegiatan }}</div>
                            </td>
                            <td>
                                <div>
                                    @if($agenda->tipe === 'tatap_muka')
                                        <x-gentelella.badge variant="secondary">🏢 Tatap Muka</x-gentelella.badge>
                                    @else
                                        <x-gentelella.badge variant="info">💻 Daring (Online)</x-gentelella.badge>
                                    @endif
                                </div>
                                <div class="agenda-location-sub">{{ $agenda->lokasi_ruang ?? '-' }}</div>
                            </td>
                            <td>
                                <div class="schedule-date-text">{{ $agenda->tgl_bimbingan?->translatedFormat('d M Y') }}</div>
                                <div class="schedule-time-text">Pukul {{ $agenda->jam_mulai }} WIB</div>
                            </td>
                            <td>
                                @if($agenda->status === 'terjadwal')
                                    <x-gentelella.badge variant="warning">Terjadwal</x-gentelella.badge>
                                @elseif($agenda->status === 'selesai')
                                    <x-gentelella.badge variant="success">Selesai</x-gentelella.badge>
                                @else
                                    <x-gentelella.badge variant="secondary">Dibatalkan</x-gentelella.badge>
                                @endif
                            </td>
                            <td>
                                @if($agenda->notulensi_arahan)
                                    <div class="notulensi-preview-box">
                                        {{ $agenda->notulensi_arahan }}
                                    </div>
                                @else
                                    <span style="color: #94A3B8; font-size: 0.75rem; font-style: italic;">Belum ada notulensi</span>
                                @endif
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <button type="button" 
                                    class="btn-agenda-action btn-open-edit-agenda"
                                    data-agenda-id="{{ $agenda->id }}"
                                    data-agenda-status="{{ $agenda->status }}"
                                    data-agenda-notulensi="{{ $agenda->notulensi_arahan ?? '' }}">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                    <span>Notulensi / Edit</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="table-empty-td">
                                Belum ada jadwal agenda mentoring bimbingan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($agendaList->hasPages())
            <div style="margin-top: 24px;">
                {{ $agendaList->links() }}
            </div>
        @endif
    </x-gentelella.x-panel>

    {{-- 4. MODAL TAMBAH AGENDA --}}
    <div id="createModal" class="modal-overlay">
        <div class="modal-dialog-box">
            <div class="modal-header-bar">
                <h3 class="modal-header-title">Jadwalkan Mentoring Baru</h3>
                <button type="button" class="modal-close-btn" data-modal-close="#createModal">&times;</button>
            </div>
            <form method="POST" action="{{ route('pembimbing.agenda.store') }}" class="modal-body-form">
                @csrf
                <div class="modal-form-group">
                    <label>Pilih Mentee Magang *</label>
                    <select name="penempatan_id" required class="filter-control">
                        <option value="">-- Pilih Mentee Bimbingan --</option>
                        @foreach($daftarMentee as $m)
                            <option value="{{ $m->id }}">
                                {{ $m->peserta?->nama }} &bull; {{ $m->peserta?->profil?->institusi ?? 'Institusi' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="modal-form-group">
                    <label>Judul / Topik Sesi Mentoring *</label>
                    <input type="text" name="judul_kegiatan" required placeholder="Contoh: Evaluasi Progres Modul &amp; Arahan Tugas Pekanan" class="filter-control">
                </div>

                <div class="modal-form-row-2col">
                    <div class="modal-form-group">
                        <label>Metode Pelaksanaan *</label>
                        <select name="tipe" required class="filter-control">
                            <option value="tatap_muka">Tatap Muka Langsung</option>
                            <option value="daring">Daring (Online Meeting)</option>
                        </select>
                    </div>
                    <div class="modal-form-group">
                        <label>Jam Mulai *</label>
                        <input type="text" name="jam_mulai" value="09:00" required placeholder="09:00" class="filter-control">
                    </div>
                </div>

                <div class="modal-form-row-2col">
                    <div class="modal-form-group">
                        <label>Tanggal Bimbingan *</label>
                        <input type="date" name="tgl_bimbingan" value="{{ date('Y-m-d') }}" required class="filter-control">
                    </div>
                    <div class="modal-form-group">
                        <label>Ruang / Tautan</label>
                        <input type="text" name="lokasi_ruang" placeholder="Contoh: Ruang Rapat 2 / Meet URL" class="filter-control">
                    </div>
                </div>

                <div class="modal-form-group">
                    <label>Notulensi / Arahan Awal (Opsional)</label>
                    <textarea name="notulensi_arahan" rows="3" placeholder="Tuliskan poin arahan atau topik yang perlu dipersiapkan..." class="filter-control" style="height: auto; padding: 10px;"></textarea>
                </div>

                <div class="modal-footer-actions">
                    <button type="button" class="btn-modal-cancel" data-modal-close="#createModal">Batal</button>
                    <button type="submit" class="btn-modal-submit">Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>

    {{-- 5. MODAL EDIT NOTULENSI --}}
    <div id="editModal" class="modal-overlay">
        <div class="modal-dialog-box">
            <div class="modal-header-bar">
                <h3 class="modal-header-title">Notulensi &amp; Pembaruan Status</h3>
                <button type="button" class="modal-close-btn" data-modal-close="#editModal">&times;</button>
            </div>
            <form id="editForm" method="POST" class="modal-body-form">
                @csrf
                @method('PUT')
                <div class="modal-form-group">
                    <label>Status Sesi Bimbingan *</label>
                    <select name="status" id="editStatus" required class="filter-control">
                        <option value="terjadwal">Terjadwal</option>
                        <option value="selesai">Selesai (Terlaksana)</option>
                        <option value="dibatalkan">Dibatalkan</option>
                    </select>
                </div>

                <div class="modal-form-group">
                    <label>Catatan / Notulensi Arahan Pembimbing *</label>
                    <textarea name="notulensi_arahan" id="editNotulensi" rows="5" placeholder="Tuliskan evaluasi, feedback, dan arahan tugas mentoring..." class="filter-control" style="height: auto; padding: 10px;"></textarea>
                </div>

                <div class="modal-footer-actions">
                    <button type="button" class="btn-modal-cancel" data-modal-close="#editModal">Tutup</button>
                    <button type="submit" class="btn-modal-submit">Simpan Notulensi</button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
    @vite(['resources/js/pages/pembimbing/agenda-index.js'])
@endpush
@endsection
