@extends('layouts.dashboard')

@section('title', 'Penugasan Peserta - Portal MagKom')

@section('content')
<div class="dashboard-content" style="display: flex; flex-direction: column; gap: 1.5rem;">

    <!-- Flash Notification -->
    @if(session('success'))
        <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 12px; padding: 1rem 1.25rem; display: flex; align-items: center; justify-content: space-between; color: #065f46;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                <span style="font-size: 0.875rem; font-weight: 500;">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" style="background: transparent; border: none; cursor: pointer; color: #065f46;">&times;</button>
        </div>
    @endif

    @if($errors->any())
        <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 12px; padding: 1rem 1.25rem; color: #991b1b;">
            <div style="font-weight: 600; font-size: 0.875rem; margin-bottom: 0.25rem;">Gagal Memproses Permintaan:</div>
            <ul style="margin: 0; padding-left: 1.25rem; font-size: 0.8rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Header & Action -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <nav style="font-size: 0.8rem; color: var(--neutral-500); margin-bottom: 0.35rem; display: flex; align-items: center; gap: 0.4rem;">
                <a href="{{ route('pembimbing.dashboard') }}" style="color: var(--neutral-600); text-decoration: none;">Dashboard</a>
                <span>/</span>
                <span style="color: var(--neutral-900); font-weight: 600;">Tugas Peserta</span>
            </nav>
            <h1 style="font-size: 1.5rem; font-weight: 700; color: var(--neutral-900); margin: 0;">
                Manajemen Penugasan & Progres (F-08)
            </h1>
            <p style="font-size: 0.875rem; color: var(--neutral-600); margin: 0.25rem 0 0 0;">
                Berikan penugasan terstruktur kepada peserta bimbingan dan evaluasi kemajuan kerja.
            </p>
        </div>
        <div>
            <button type="button" onclick="document.getElementById('modalBuatTugas').style.display='flex'" style="background: #0641CC; color: #ffffff; padding: 0.55rem 1.2rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 2px 6px rgba(6, 65, 204, 0.2);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Buat Tugas Baru
            </button>
        </div>
    </div>

    <!-- Filter & Toolbar -->
    <div style="background: #ffffff; border-radius: 14px; border: 1px solid var(--neutral-200); padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <form method="GET" action="{{ route('pembimbing.tugas.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            
            <!-- Filter Status (Sesuai StatusTugasEnum) -->
            <div style="flex: 1; min-width: 180px;">
                <label style="font-size: 0.8rem; font-weight: 600; color: var(--neutral-700); display: block; margin-bottom: 0.35rem;">Status Tugas</label>
                <select name="status" style="width: 100%; height: 40px; border: 1px solid var(--neutral-300); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff;">
                    <option value="">Semua Status</option>
                    <option value="belum" {{ request('status') === 'belum' ? 'selected' : '' }}>Belum Dikerjakan</option>
                    <option value="proses" {{ request('status') === 'proses' ? 'selected' : '' }}>Sedang Dikerjakan</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>

            <!-- Filter Mentee -->
            <div style="flex: 1; min-width: 200px;">
                <label style="font-size: 0.8rem; font-weight: 600; color: var(--neutral-700); display: block; margin-bottom: 0.35rem;">Peserta Magang</label>
                <select name="penempatan_id" style="width: 100%; height: 40px; border: 1px solid var(--neutral-300); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff;">
                    <option value="">Semua Mentee</option>
                    @foreach($penempatanList as $p)
                        <option value="{{ $p->id }}" {{ request('penempatan_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->peserta?->nama }} ({{ $p->peserta?->profil?->institusi ?? 'Institusi' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Pencarian Kata Kunci -->
            <div style="flex: 2; min-width: 220px;">
                <label style="font-size: 0.8rem; font-weight: 600; color: var(--neutral-700); display: block; margin-bottom: 0.35rem;">Cari Judul / Topik</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul tugas atau deskripsi..." style="width: 100%; height: 40px; border: 1px solid var(--neutral-300); border-radius: 8px; padding: 0 0.85rem; font-size: 0.875rem;">
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" style="height: 40px; padding: 0 1.25rem; background: #0641CC; color: #ffffff; font-weight: 600; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer;">
                    Filter
                </button>
                @if(request()->hasAny(['status', 'penempatan_id', 'q']))
                    <a href="{{ route('pembimbing.tugas.index') }}" style="height: 40px; padding: 0 1rem; background: var(--neutral-200); color: var(--neutral-700); font-weight: 600; font-size: 0.875rem; text-decoration: none; border-radius: 8px; display: inline-flex; align-items: center;">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Daftar Tugas -->
    <div style="background: #ffffff; border-radius: 14px; border: 1px solid var(--neutral-200); box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--neutral-200); background: #f8fafc; color: var(--neutral-600); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                        <th style="padding: 1rem 1.5rem;">Tugas & Kategori</th>
                        <th style="padding: 1rem 1rem;">Peserta Magang</th>
                        <th style="padding: 1rem 1rem;">Kompleksitas</th>
                        <th style="padding: 1rem 1rem;">Tenggat Waktu</th>
                        <th style="padding: 1rem 1rem;">Status</th>
                        <th style="padding: 1rem 1.5rem; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tugasList as $tugas)
                        @php
                            $isOverdue = $tugas->deadline && $tugas->deadline->isPast() && $tugas->status->value !== 'selesai';
                            $daysLeft = $tugas->deadline ? \Carbon\Carbon::today()->diffInDays($tugas->deadline, false) : 99;
                            $isDueSoon = !$isOverdue && $daysLeft >= 0 && $daysLeft <= 3 && $tugas->status->value !== 'selesai';
                        @endphp
                        <tr style="border-bottom: 1px solid var(--neutral-100); transition: background 0.15s ease;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 1rem 1.5rem;">
                                <div style="font-weight: 600; color: var(--neutral-900); font-size: 0.925rem;">
                                    <a href="{{ route('pembimbing.tugas.show', $tugas->id) }}" style="color: inherit; text-decoration: none;">
                                        {{ $tugas->judul }}
                                    </a>
                                </div>
                                <div style="display: flex; gap: 0.5rem; align-items: center; margin-top: 0.25rem;">
                                    <span style="font-size: 0.7rem; background: #e0e7ff; color: #3730a3; padding: 0.1rem 0.45rem; border-radius: 4px; font-weight: 600;">
                                        {{ $tugas->kategori }}
                                    </span>
                                    <span style="font-size: 0.75rem; color: var(--neutral-500);">
                                        {{ $tugas->progresLogs->count() }} Laporan Progres
                                    </span>
                                </div>
                            </td>
                            <td style="padding: 1rem 1rem;">
                                <div style="font-weight: 500; color: var(--neutral-800);">
                                    {{ $tugas->penempatan?->peserta?->nama ?? '-' }}
                                </div>
                                <div style="font-size: 0.75rem; color: var(--neutral-500);">
                                    {{ $tugas->penempatan?->peserta?->profil?->institusi ?? '-' }}
                                </div>
                            </td>
                            <td style="padding: 1rem 1rem;">
                                <span style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-700); background: var(--neutral-100); padding: 0.2rem 0.5rem; border-radius: 4px;">
                                    {{ ucfirst($tugas->kompleksitas?->value ?? 'Sedang') }}
                                </span>
                            </td>
                            <td style="padding: 1rem 1rem;">
                                <div style="font-size: 0.825rem; font-weight: 500; color: {{ $isOverdue ? '#b91c1c' : ($isDueSoon ? '#d97706' : 'var(--neutral-800)') }};">
                                    {{ $tugas->deadline?->format('d M Y') }}
                                </div>
                                @if($isOverdue)
                                    <span style="font-size: 0.7rem; font-weight: 700; color: #b91c1c; background: #fee2e2; padding: 0.1rem 0.4rem; border-radius: 4px; display: inline-block; margin-top: 0.15rem;">
                                        Terlambat (Overdue)
                                    </span>
                                @elseif($isDueSoon)
                                    <span style="font-size: 0.7rem; font-weight: 600; color: #92400e; background: #fef3c7; padding: 0.1rem 0.4rem; border-radius: 4px; display: inline-block; margin-top: 0.15rem;">
                                        Sisa {{ $daysLeft }} Hari
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 1rem 1rem;">
                                @if($tugas->status->value === 'selesai')
                                    <span style="font-size: 0.75rem; font-weight: 600; background: #d1fae5; color: #065f46; padding: 0.2rem 0.55rem; border-radius: 999px;">
                                        Selesai
                                    </span>
                                @elseif($tugas->status->value === 'proses')
                                    <span style="font-size: 0.75rem; font-weight: 600; background: #fef3c7; color: #92400e; padding: 0.2rem 0.55rem; border-radius: 999px;">
                                        Sedang Dikerjakan
                                    </span>
                                @else
                                    <span style="font-size: 0.75rem; font-weight: 500; background: var(--neutral-100); color: var(--neutral-600); padding: 0.2rem 0.55rem; border-radius: 999px;">
                                        Belum Dikerjakan
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 1rem 1.5rem; text-align: right;">
                                <a href="{{ route('pembimbing.tugas.show', $tugas->id) }}" style="padding: 0.4rem 0.85rem; background: rgba(6, 65, 204, 0.08); color: #0641CC; font-size: 0.75rem; font-weight: 600; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;">
                                    Detail & Reviu &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 3rem 1.5rem; text-align: center; color: var(--neutral-500);">
                                Belum ada penugasan yang sesuai dengan kriteria filter saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tugasList->hasPages())
            <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--neutral-200); background: #fafafa;">
                {{ $tugasList->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal Buat Tugas Baru (AC-08) -->
<div id="modalBuatTugas" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
    <div style="background: #ffffff; border-radius: 16px; width: 100%; max-width: 560px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); overflow: hidden; max-height: 90vh; display: flex; flex-direction: column;">
        
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-200); display: flex; justify-content: space-between; align-items: center; background: #fafafa;">
            <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--neutral-900); margin: 0;">
                Buat Tugas Baru untuk Mentee
            </h3>
            <button type="button" onclick="document.getElementById('modalBuatTugas').style.display='none'" style="background: transparent; border: none; font-size: 1.5rem; cursor: pointer; color: var(--neutral-400); line-height: 1;">&times;</button>
        </div>

        <form method="POST" action="{{ route('pembimbing.tugas.store') }}" style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem; overflow-y: auto;">
            @csrf

            <!-- Pilih Mentee Penempatan -->
            <div>
                <label style="font-size: 0.8rem; font-weight: 600; color: var(--neutral-700); display: block; margin-bottom: 0.35rem;">Peserta Bimbingan <span style="color: #ef4444;">*</span></label>
                <select name="penempatan_id" id="modal_penempatan_id" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-300); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                    <option value="">-- Pilih Peserta Bimbingan --</option>
                    @foreach($penempatanList as $p)
                        <option value="{{ $p->id }}" data-tgl-selesai="{{ $p->tgl_selesai?->format('Y-m-d') }}">
                            {{ $p->peserta?->nama }} - {{ $p->peserta?->profil?->institusi ?? 'Institusi' }} (Selesai: {{ $p->tgl_selesai?->format('d/m/Y') }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Judul Tugas -->
            <div>
                <label style="font-size: 0.8rem; font-weight: 600; color: var(--neutral-700); display: block; margin-bottom: 0.35rem;">Judul Tugas <span style="color: #ef4444;">*</span></label>
                <input type="text" name="judul" required maxlength="150" placeholder="Contoh: Implementasi Modul Laporan Presensi" style="width: 100%; height: 42px; border: 1px solid var(--neutral-300); border-radius: 8px; padding: 0 0.85rem; font-size: 0.875rem;">
            </div>

            <!-- Kategori & Kompleksitas -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; color: var(--neutral-700); display: block; margin-bottom: 0.35rem;">Kategori Bidang <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="kategori" required placeholder="Pengembangan Web, Jaringan, ..." style="width: 100%; height: 42px; border: 1px solid var(--neutral-300); border-radius: 8px; padding: 0 0.85rem; font-size: 0.875rem;">
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; color: var(--neutral-700); display: block; margin-bottom: 0.35rem;">Tingkat Kompleksitas <span style="color: #ef4444;">*</span></label>
                    <select name="kompleksitas" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-300); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                        <option value="rendah">Rendah (Dasar/Minor)</option>
                        <option value="sedang" selected>Sedang (Standar)</option>
                        <option value="tinggi">Tinggi (Kompleks/Utama)</option>
                    </select>
                </div>
            </div>

            <!-- Tenggat Waktu (Deadline) - AC-08-1 -->
            <div>
                <label style="font-size: 0.8rem; font-weight: 600; color: var(--neutral-700); display: block; margin-bottom: 0.35rem;">
                    Batas Waktu (Deadline) <span style="color: #ef4444;">*</span>
                </label>
                <input type="date" name="deadline" id="modal_deadline" min="{{ date('Y-m-d') }}" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-300); border-radius: 8px; padding: 0 0.85rem; font-size: 0.875rem;">
                <span id="deadline_hint" style="font-size: 0.75rem; color: var(--neutral-500); margin-top: 0.25rem; display: block;">
                    Batas waktu tidak boleh di masa lampau dan tidak boleh melampaui batas selesai magang mentee (AC-08-1).
                </span>
            </div>

            <!-- Deskripsi Rinci Tugas -->
            <div>
                <label style="font-size: 0.8rem; font-weight: 600; color: var(--neutral-700); display: block; margin-bottom: 0.35rem;">Deskripsi & Ekspektasi Output</label>
                <textarea name="deskripsi" rows="3" placeholder="Rincikan instruksi tugas, kriteria penerimaan, dan format dokumen output yang diharapkan..." style="width: 100%; border: 1px solid var(--neutral-300); border-radius: 8px; padding: 0.75rem; font-size: 0.875rem; font-family: inherit;"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem; border-top: 1px solid var(--neutral-200); padding-top: 1rem;">
                <button type="button" onclick="document.getElementById('modalBuatTugas').style.display='none'" style="height: 40px; padding: 0 1.25rem; background: var(--neutral-100); color: var(--neutral-700); font-weight: 600; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="height: 40px; padding: 0 1.5rem; background: #0641CC; color: #ffffff; font-weight: 600; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer;">
                    Simpan & Berikan Tugas
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('modal_penempatan_id')?.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const tglSelesai = selected.getAttribute('data-tgl-selesai');
        const deadlineInput = document.getElementById('modal_deadline');
        const hint = document.getElementById('deadline_hint');
        if (tglSelesai && deadlineInput) {
            deadlineInput.max = tglSelesai;
            hint.textContent = 'Maksimal batas waktu untuk mentee ini: ' + tglSelesai;
        } else if (deadlineInput) {
            deadlineInput.removeAttribute('max');
        }
    });
</script>
@endsection
