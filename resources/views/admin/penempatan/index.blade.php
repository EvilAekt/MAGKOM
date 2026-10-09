@extends('layouts.dashboard')

@section('title', 'Penempatan Magang - Admin MagKom Sleman')
@section('page_title', 'Manajemen Penempatan Peserta')
@section('page_description', 'Kelola penugasan pembimbing lapangan, pantau peserta aktif, dan kelola administrasi penempatan magang.')

@section('breadcrumbs')
    <span class="breadcrumb-current">Penempatan Magang</span>
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
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Bidang Dinas</th>
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
                                        <button type="button" onclick="openPindahModal({{ $p->id }}, '{{ addslashes($p->peserta?->nama) }}', {{ $p->pembimbing_id ?? 'null' }})" style="border: none; background: none; color: var(--blue-700); font-size: 0.75rem; font-weight: 600; cursor: pointer; padding: 0; text-decoration: underline;">
                                            Ganti Pembimbing
                                        </button>
                                    @endif
                                </td>
                                <td style="padding: 0.75rem 1rem; color: var(--neutral-700);">
                                    {{ $p->bidang?->nama_bidang ?? 'Dinas Kominfo' }}
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
                                        <a href="{{ route('admin.penempatan.surat-diterima', $p) }}" title="Unduh Surat Diterima PDF" style="padding: 0.375rem 0.625rem; background: var(--neutral-200); color: var(--neutral-800); text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 0.75rem;">
                                            PDF
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
                        <option value="{{ $pb->id }}">{{ $pb->nama }}</option>
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

<script>
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
</script>
@endsection
