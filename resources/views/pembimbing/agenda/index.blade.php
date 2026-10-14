@extends('layouts.dashboard')

@section('title', 'Agenda Mentoring & Notulensi Bimbingan - MagKom Sleman')

@section('content')
<div class="dashboard-content" style="display: flex; flex-direction: column; gap: 1.5rem; max-width: 1400px; margin: 0 auto; width: 100%;">

    <style>
        .agenda-stat-card {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid var(--neutral-200, #E2E8F0);
            padding: 1.25rem 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .agenda-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.875rem;
        }
        .agenda-table th {
            background: #F8FAFC;
            padding: 0.85rem 1.25rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--neutral-600, #475569);
            border-bottom: 1px solid var(--neutral-200, #E2E8F0);
        }
        .agenda-table td {
            padding: 0.95rem 1.25rem;
            border-bottom: 1px solid var(--neutral-200, #E2E8F0);
            vertical-align: middle;
            color: var(--neutral-800, #1E293B);
        }
        .agenda-table tr:hover {
            background: #F8FAFC;
        }
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.6);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            padding: 1rem;
        }
    </style>

    {{-- 1. HEADER & BREADCRUMB --}}
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0); padding-bottom: 1rem;">
        <div>
            <nav style="font-size: 0.8rem; color: var(--neutral-500, #64748B); margin-bottom: 0.35rem; display: flex; align-items: center; gap: 0.4rem;">
                <a href="{{ route('pembimbing.dashboard') }}" style="color: var(--neutral-600, #475569); text-decoration: none;">Dashboard Pembimbing</a>
                <span>/</span>
                <span style="color: var(--neutral-900, #0F172A); font-weight: 600;">Agenda Mentoring</span>
            </nav>
            <div style="display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
                <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0; letter-spacing: -0.02em;">
                    Agenda Penjadwalan Mentoring & Notulensi
                </h1>
                <span style="display: inline-flex; align-items: center; padding: 0.2rem 0.65rem; border-radius: 9999px; font-size: 0.72rem; font-weight: 700; background: #DBEAFE; color: #1E40AF; text-transform: uppercase; letter-spacing: 0.04em;">
                    Pilar 2 Pembimbing
                </span>
            </div>
            <p style="font-size: 0.85rem; color: var(--neutral-600, #475569); margin: 0.3rem 0 0 0;">
                Kelola jadwal sesi bimbingan tatap muka / daring serta arsip notulensi arahan tugas lapangan mentee.
            </p>
        </div>

        <div style="display: flex; gap: 0.75rem; align-items: center;">
            <a href="{{ route('pembimbing.dashboard') }}" style="height: 38px; padding: 0 1rem; background: #ffffff; color: var(--neutral-700, #334155); border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; font-size: 0.85rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
                <span>Dashboard</span>
            </a>
            <button type="button" onclick="openCreateModal()" style="height: 38px; padding: 0 1.25rem; background: #0641CC; color: #ffffff; border: none; border-radius: 8px; font-size: 0.85rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <span>Jadwalkan Mentoring Baru</span>
            </button>
        </div>
    </div>



    {{-- 2. METRIK KPI --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;">
        <div class="agenda-stat-card" style="border-left: 4px solid #0641CC;">
            <div>
                <span style="font-size: 0.8rem; font-weight: 700; color: var(--neutral-600, #475569); display: block; margin-bottom: 0.25rem;">Agenda Terjadwal</span>
                <span style="font-size: 1.85rem; font-weight: 800; color: #0641CC;">{{ number_format($totalTerjadwal) }}</span>
                <span style="font-size: 0.75rem; color: var(--neutral-500, #64748B); display: block; margin-top: 0.25rem;">Sesi bimbingan aktif</span>
            </div>
            <div style="width: 46px; height: 46px; border-radius: 10px; background: #EFF6FF; color: #0641CC; display: flex; align-items: center; justify-content: center;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            </div>
        </div>

        <div class="agenda-stat-card" style="border-left: 4px solid #10B981;">
            <div>
                <span style="font-size: 0.8rem; font-weight: 700; color: var(--neutral-600, #475569); display: block; margin-bottom: 0.25rem;">Selesai Terlaksana</span>
                <span style="font-size: 1.85rem; font-weight: 800; color: var(--neutral-900, #0F172A);">{{ number_format($totalSelesai) }}</span>
                <span style="font-size: 0.75rem; color: #166534; display: block; margin-top: 0.25rem;">Notulensi tercatat rapi</span>
            </div>
            <div style="width: 46px; height: 46px; border-radius: 10px; background: #DCFCE7; color: #16A34A; display: flex; align-items: center; justify-content: center;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
            </div>
        </div>
    </div>

    {{-- 3. DAFTAR AGENDA BIMBINGAN --}}
    <div style="background: #ffffff; border: 1px solid var(--neutral-200, #E2E8F0); border-radius: 14px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        @if($agendaList->isEmpty())
            <div style="padding: 3.5rem 1.5rem; text-align: center; color: var(--neutral-600, #475569);">
                <div style="width: 48px; height: 48px; border-radius: 50%; background: #EFF6FF; color: #0641CC; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem auto;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                </div>
                <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-900, #0F172A); margin: 0 0 0.25rem 0;">Belum Ada Jadwal Bimbingan</h3>
                <p style="font-size: 0.825rem; margin: 0 0 1rem 0;">Mulai jadwalkan sesi mentoring lapangan atau daring bersama mentee Anda.</p>
                <button type="button" onclick="openCreateModal()" style="height: 36px; padding: 0 1rem; background: #0641CC; color: #ffffff; border: none; border-radius: 6px; font-size: 0.825rem; font-weight: 700; cursor: pointer;">
                    + Jadwalkan Sesi Sekarang
                </button>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table class="agenda-table">
                    <thead>
                        <tr>
                            <th>Mentee Magang</th>
                            <th>Kegiatan Bimbingan</th>
                            <th>Metode & Lokasi</th>
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
                                            <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.15rem 0.5rem; border-radius: 9999px; font-size: 0.72rem; font-weight: 700; background: #F1F5F9; color: #334155;">
                                                🏢 Tatap Muka
                                            </span>
                                        @else
                                            <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.15rem 0.5rem; border-radius: 9999px; font-size: 0.72rem; font-weight: 700; background: #EFF6FF; color: #1E40AF;">
                                                💻 Daring (Online)
                                            </span>
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
                                        <span style="display: inline-flex; align-items: center; padding: 0.2rem 0.6rem; border-radius: 9999px; font-size: 0.72rem; font-weight: 700; background: #FEF3C7; color: #B45309; border: 1px solid #FDE68A;">
                                            Terjadwal
                                        </span>
                                    @elseif($agenda->status === 'selesai')
                                        <span style="display: inline-flex; align-items: center; padding: 0.2rem 0.6rem; border-radius: 9999px; font-size: 0.72rem; font-weight: 700; background: #DCFCE7; color: #166534; border: 1px solid #86EFAC;">
                                            Selesai
                                        </span>
                                    @else
                                        <span style="display: inline-flex; align-items: center; padding: 0.2rem 0.6rem; border-radius: 9999px; font-size: 0.72rem; font-weight: 700; background: #F1F5F9; color: #64748B; border: 1px solid #CBD5E1;">
                                            Dibatalkan
                                        </span>
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
                                            style="padding: 0.35rem 0.75rem; background: #ffffff; border: 1px solid var(--neutral-300, #CBD5E1); color: var(--neutral-700, #334155); border-radius: 6px; font-size: 0.75rem; font-weight: 600; cursor: pointer; margin-right: 0.25rem;">
                                        Notulensi / Edit
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($agendaList->hasPages())
                <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--neutral-200, #E2E8F0);">
                    {{ $agendaList->links() }}
                </div>
            @endif
        @endif
    </div>

    {{-- MODAL TAMBAH AGENDA --}}
    <div id="createModal" class="modal-overlay">
        <div style="background: #ffffff; border-radius: 14px; width: 100%; max-width: 520px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); overflow: hidden;">
            <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0); display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0;">Jadwalkan Mentoring Baru</h3>
                <button type="button" onclick="closeCreateModal()" style="background: transparent; border: none; font-size: 1.25rem; cursor: pointer; color: #64748B;">&times;</button>
            </div>
            <form method="POST" action="{{ route('pembimbing.agenda.store') }}" style="padding: 1.25rem 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
                @csrf
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Pilih Mentee Magang *</label>
                    <select name="penempatan_id" required style="width: 100%; height: 38px; border: 1px solid #CBD5E1; border-radius: 8px; padding: 0 0.75rem; font-size: 0.85rem;">
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
                    <input type="text" name="judul_kegiatan" required placeholder="Contoh: Evaluasi Progres Modul & Arahan Tugas Pekanan" style="width: 100%; height: 38px; border: 1px solid #CBD5E1; border-radius: 8px; padding: 0 0.75rem; font-size: 0.85rem; box-sizing: border-box;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Metode Pelaksanaan *</label>
                        <select name="tipe" required style="width: 100%; height: 38px; border: 1px solid #CBD5E1; border-radius: 8px; padding: 0 0.75rem; font-size: 0.85rem;">
                            <option value="tatap_muka">Tatap Muka Langsung</option>
                            <option value="daring">Daring (Online Meeting)</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Jam Mulai *</label>
                        <input type="text" name="jam_mulai" value="09:00" required placeholder="09:00" style="width: 100%; height: 38px; border: 1px solid #CBD5E1; border-radius: 8px; padding: 0 0.75rem; font-size: 0.85rem; box-sizing: border-box;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Tanggal Bimbingan *</label>
                        <input type="date" name="tgl_bimbingan" value="{{ date('Y-m-d') }}" required style="width: 100%; height: 38px; border: 1px solid #CBD5E1; border-radius: 8px; padding: 0 0.75rem; font-size: 0.85rem; box-sizing: border-box;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Ruang / Tautan</label>
                        <input type="text" name="lokasi_ruang" placeholder="Contoh: Ruang Rapat 2 / Meet URL" style="width: 100%; height: 38px; border: 1px solid #CBD5E1; border-radius: 8px; padding: 0 0.75rem; font-size: 0.85rem; box-sizing: border-box;">
                    </div>
                </div>

                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Notulensi / Arahan Awal (Opsional)</label>
                    <textarea name="notulensi_arahan" rows="3" placeholder="Tuliskan poin arahan atau topik yang perlu dipersiapkan oleh mentee..." style="width: 100%; border: 1px solid #CBD5E1; border-radius: 8px; padding: 0.5rem 0.75rem; font-size: 0.85rem; box-sizing: border-box;"></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem;">
                    <button type="button" onclick="closeCreateModal()" style="height: 38px; padding: 0 1rem; background: #F1F5F9; border: 1px solid #CBD5E1; border-radius: 8px; font-size: 0.85rem; font-weight: 600; cursor: pointer;">Batal</button>
                    <button type="submit" style="height: 38px; padding: 0 1.25rem; background: #0641CC; color: #ffffff; border: none; border-radius: 8px; font-size: 0.85rem; font-weight: 700; cursor: pointer;">Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT NOTULENSI --}}
    <div id="editModal" class="modal-overlay">
        <div style="background: #ffffff; border-radius: 14px; width: 100%; max-width: 520px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); overflow: hidden;">
            <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0); display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0;">Notulensi & Pembaruan Status</h3>
                <button type="button" onclick="closeEditModal()" style="background: transparent; border: none; font-size: 1.25rem; cursor: pointer; color: #64748B;">&times;</button>
            </div>
            <form id="editForm" method="POST" style="padding: 1.25rem 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
                @csrf
                @method('PUT')
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Status Sesi Bimbingan *</label>
                    <select name="status" id="editStatus" required style="width: 100%; height: 38px; border: 1px solid #CBD5E1; border-radius: 8px; padding: 0 0.75rem; font-size: 0.85rem;">
                        <option value="terjadwal">Terjadwal</option>
                        <option value="selesai">Selesai (Terlaksana)</option>
                        <option value="dibatalkan">Dibatalkan</option>
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Catatan / Notulensi Arahan Pembimbing *</label>
                    <textarea name="notulensi_arahan" id="editNotulensi" rows="5" placeholder="Tuliskan evaluasi, feedback, dan arahan tugas mentoring..." style="width: 100%; border: 1px solid #CBD5E1; border-radius: 8px; padding: 0.5rem 0.75rem; font-size: 0.85rem; box-sizing: border-box;"></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem;">
                    <button type="button" onclick="closeEditModal()" style="height: 38px; padding: 0 1rem; background: #F1F5F9; border: 1px solid #CBD5E1; border-radius: 8px; font-size: 0.85rem; font-weight: 600; cursor: pointer;">Tutup</button>
                    <button type="submit" style="height: 38px; padding: 0 1.25rem; background: #0641CC; color: #ffffff; border: none; border-radius: 8px; font-size: 0.85rem; font-weight: 700; cursor: pointer;">Simpan Notulensi</button>
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
