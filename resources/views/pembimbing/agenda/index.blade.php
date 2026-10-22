@extends('layouts.dashboard')

@section('title', 'Agenda Mentoring & Notulensi Bimbingan - MagKom Sleman')
@section('page_title', 'Agenda Mentoring & Notulensi')
@section('page_description', 'Kelola jadwal sesi bimbingan tatap muka / daring serta arsip notulensi arahan tugas lapangan mentee.')

@section('breadcrumbs')
    <nav style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; color: var(--neutral-600, #64748B);">
        <a href="{{ route('pembimbing.dashboard') }}" style="color: var(--neutral-600, #64748B); text-decoration: none;">Dashboard</a>
        <span>/</span>
        <span style="color: var(--neutral-900, #0F172A); font-weight: 700;">Agenda Mentoring</span>
    </nav>
@endsection

@section('page_actions')
    <div style="display: flex; gap: 0.75rem; align-items: center;">
        <x-gentelella.button variant="primary" icon="fa fa-calendar-plus-o" onclick="openCreateModal()">
            Jadwalkan Mentoring Baru
        </x-gentelella.button>
    </div>
@endsection

@section('content')
<div class="dashboard-content" style="display: flex; flex-direction: column; gap: 1.5rem; width: 100%;">

    {{-- 2. METRIK KPI --}}
    <div class="row">
        <div class="col-md-6 col-sm-6">
            <x-gentelella.tile-stat 
                title="Agenda Terjadwal"
                :value="number_format($totalTerjadwal)"
                subtitle="Sesi bimbingan aktif"
                icon="fa fa-calendar"
                color="blue"
            />
        </div>
        <div class="col-md-6 col-sm-6">
            <x-gentelella.tile-stat 
                title="Selesai Terlaksana"
                :value="number_format($totalSelesai)"
                subtitle="Notulensi tercatat rapi"
                icon="fa fa-check-square-o"
                color="green"
            />
        </div>
    </div>

    {{-- 3. DAFTAR AGENDA BIMBINGAN --}}
    <x-gentelella.x-panel title="Daftar Jadwal &amp; Notulensi Sesi Mentoring" subtitle="Sesi bimbingan aktif dan rekam notulensi pelaksanaan">
        @if($agendaList->isEmpty())
            <div style="padding: 3.5rem 1.5rem; text-align: center; color: var(--neutral-600, #475569);">
                <div style="width: 48px; height: 48px; border-radius: 50%; background: #EFF6FF; color: #0641CC; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem auto;">
                    <i class="fa fa-calendar-o" style="font-size: 1.5rem;"></i>
                </div>
                <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-900, #0F172A); margin: 0 0 0.25rem 0;">Belum Ada Jadwal Bimbingan</h3>
                <p style="font-size: 0.825rem; margin: 0 0 1rem 0;">Mulai jadwalkan sesi mentoring lapangan atau daring bersama mentee Anda.</p>
                <x-gentelella.button variant="primary" icon="fa fa-calendar-plus-o" onclick="openCreateModal()">
                    Jadwalkan Sesi Sekarang
                </x-gentelella.button>
            </div>
        @else
            <x-gentelella.table :hover="true" :striped="false" class="jambo_table">
                <thead>
                    <tr class="headings">
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
                    @foreach($agendaList as $agenda)
                        <tr>
                            <td>
                                <div style="font-weight: 700; color: var(--neutral-900, #0F172A);">{{ $agenda->penempatan?->peserta?->nama }}</div>
                                <small style="color: var(--neutral-500, #64748B);">{{ $agenda->penempatan?->peserta?->profil?->institusi ?? '-' }}</small>
                            </td>
                            <td>
                                <div style="font-weight: 700; color: #0641CC;">{{ $agenda->judul_kegiatan }}</div>
                            </td>
                            <td>
                                <div>
                                    @if($agenda->tipe === 'tatap_muka')
                                        <x-gentelella.badge variant="secondary">
                                            🏢 Tatap Muka
                                        </x-gentelella.badge>
                                    @else
                                        <x-gentelella.badge variant="info">
                                            💻 Daring (Online)
                                        </x-gentelella.badge>
                                    @endif
                                </div>
                                <small style="color: var(--neutral-500, #64748B); display: block; margin-top: 0.2rem;">{{ $agenda->lokasi_ruang ?? '-' }}</small>
                            </td>
                            <td>
                                <div style="font-weight: 700; color: var(--neutral-800, #1E293B);">{{ $agenda->tgl_bimbingan?->translatedFormat('d M Y') }}</div>
                                <small style="color: var(--neutral-500, #64748B);">Pukul {{ $agenda->jam_mulai }} WIB</small>
                            </td>
                            <td>
                                @if($agenda->status === 'terjadwal')
                                    <x-gentelella.badge variant="warning">
                                        Terjadwal
                                    </x-gentelella.badge>
                                @elseif($agenda->status === 'selesai')
                                    <x-gentelella.badge variant="success">
                                        Selesai
                                    </x-gentelella.badge>
                                @else
                                    <x-gentelella.badge variant="secondary">
                                        Dibatalkan
                                    </x-gentelella.badge>
                                @endif
                            </td>
                            <td style="max-width: 250px;">
                                @if($agenda->notulensi_arahan)
                                    <p style="margin: 0; font-size: 0.8rem; color: var(--neutral-700, #334155); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        {{ $agenda->notulensi_arahan }}
                                    </p>
                                @else
                                    <em style="color: var(--neutral-400, #94A3B8); font-size: 0.75rem;">Belum ada notulensi</em>
                                @endif
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <button type="button" 
                                        onclick='openEditModal(@json($agenda))' 
                                        class="btn btn-sm btn-default"
                                        style="font-size: 0.75rem; font-weight: 600; cursor: pointer;">
                                    <i class="fa fa-pencil"></i> Notulensi / Edit
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-gentelella.table>

            @if($agendaList->hasPages())
                <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--neutral-200, #E2E8F0);">
                    {{ $agendaList->links() }}
                </div>
            @endif
        @endif
    </x-gentelella.x-panel>

    {{-- MODAL TAMBAH AGENDA --}}
    <div id="createModal" class="modal-overlay" style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center; z-index: 9999; padding: 1rem;">
        <div style="background: #ffffff; border-radius: 14px; width: 100%; max-width: 520px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); overflow: hidden;">
            <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0); display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0;">Jadwalkan Mentoring Baru</h3>
                <button type="button" onclick="closeCreateModal()" style="background: transparent; border: none; font-size: 1.25rem; cursor: pointer; color: #64748B;">&times;</button>
            </div>
            <form method="POST" action="{{ route('pembimbing.agenda.store') }}" style="padding: 1.25rem 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
                @csrf
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Pilih Mentee Magang *</label>
                    <select name="penempatan_id" required class="form-control" style="width: 100%; height: 38px; border-radius: 8px; font-size: 0.85rem;">
                        <option value="">-- Pilih Mentee Bimbingan --</option>
                        @foreach($daftarMentee as $m)
                            <option value="{{ $m->id }}">
                                {{ $m->peserta?->nama }} &bull; {{ $m->peserta?->profil?->institusi ?? 'Institusi' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Judul / Topik Sesi Mentoring *</label>
                    <input type="text" name="judul_kegiatan" required placeholder="Contoh: Evaluasi Progres Modul & Arahan Tugas Pekanan" class="form-control" style="width: 100%; height: 38px; border-radius: 8px; font-size: 0.85rem;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Metode Pelaksanaan *</label>
                        <select name="tipe" required class="form-control" style="width: 100%; height: 38px; border-radius: 8px; font-size: 0.85rem;">
                            <option value="tatap_muka">Tatap Muka Langsung</option>
                            <option value="daring">Daring (Online Meeting)</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Jam Mulai *</label>
                        <input type="text" name="jam_mulai" value="09:00" required placeholder="09:00" class="form-control" style="width: 100%; height: 38px; border-radius: 8px; font-size: 0.85rem;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Tanggal Bimbingan *</label>
                        <input type="date" name="tgl_bimbingan" value="{{ date('Y-m-d') }}" required class="form-control" style="width: 100%; height: 38px; border-radius: 8px; font-size: 0.85rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Ruang / Tautan</label>
                        <input type="text" name="lokasi_ruang" placeholder="Contoh: Ruang Rapat 2 / Meet URL" class="form-control" style="width: 100%; height: 38px; border-radius: 8px; font-size: 0.85rem;">
                    </div>
                </div>

                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Notulensi / Arahan Awal (Opsional)</label>
                    <textarea name="notulensi_arahan" rows="3" placeholder="Tuliskan poin arahan atau topik yang perlu dipersiapkan oleh mentee..." class="form-control" style="width: 100%; border-radius: 8px; font-size: 0.85rem;"></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem;">
                    <button type="button" onclick="closeCreateModal()" class="btn btn-default" style="height: 38px; padding: 0 1rem; border-radius: 8px; font-size: 0.85rem; font-weight: 600; cursor: pointer;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="height: 38px; padding: 0 1.25rem; background: #0641CC; color: #ffffff; border: none; border-radius: 8px; font-size: 0.85rem; font-weight: 700; cursor: pointer;">Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT NOTULENSI --}}
    <div id="editModal" class="modal-overlay" style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center; z-index: 9999; padding: 1rem;">
        <div style="background: #ffffff; border-radius: 14px; width: 100%; max-width: 520px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); overflow: hidden;">
            <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0); display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0;">Notulensi &amp; Pembaruan Status</h3>
                <button type="button" onclick="closeEditModal()" style="background: transparent; border: none; font-size: 1.25rem; cursor: pointer; color: #64748B;">&times;</button>
            </div>
            <form id="editForm" method="POST" style="padding: 1.25rem 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
                @csrf
                @method('PUT')
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Status Sesi Bimbingan *</label>
                    <select name="status" id="editStatus" required class="form-control" style="width: 100%; height: 38px; border-radius: 8px; font-size: 0.85rem;">
                        <option value="terjadwal">Terjadwal</option>
                        <option value="selesai">Selesai (Terlaksana)</option>
                        <option value="dibatalkan">Dibatalkan</option>
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Catatan / Notulensi Arahan Pembimbing *</label>
                    <textarea name="notulensi_arahan" id="editNotulensi" rows="5" placeholder="Tuliskan evaluasi, feedback, dan arahan tugas mentoring..." class="form-control" style="width: 100%; border-radius: 8px; font-size: 0.85rem;"></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem;">
                    <button type="button" onclick="closeEditModal()" class="btn btn-default" style="height: 38px; padding: 0 1rem; border-radius: 8px; font-size: 0.85rem; font-weight: 600; cursor: pointer;">Tutup</button>
                    <button type="submit" class="btn btn-primary" style="height: 38px; padding: 0 1.25rem; background: #0641CC; color: #ffffff; border: none; border-radius: 8px; font-size: 0.85rem; font-weight: 700; cursor: pointer;">Simpan Notulensi</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
function openCreateModal() {
    document.getElementById('createModal').style.display = 'flex';
}
function closeCreateModal() {
    document.getElementById('createModal').style.display = 'none';
}
function openEditModal(agenda) {
    const form = document.getElementById('editForm');
    form.action = `/pembimbing/agenda/${agenda.id}`;
    document.getElementById('editStatus').value = agenda.status || 'terjadwal';
    document.getElementById('editNotulensi').value = agenda.notulensi_arahan || '';
    document.getElementById('editModal').style.display = 'flex';
}
function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}
</script>
@endsection
