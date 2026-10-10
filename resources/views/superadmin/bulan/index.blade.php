@extends('layouts.dashboard')

@section('title', 'Periode Bulan Magang - SuperAdmin MagKom Sleman')
@section('page_title', 'Manajemen Periode Bulan Magang')
@section('page_description', 'Kelola jadwal pembukaan pendaftaran dan override status ketersediaan bulan magang.')

@section('breadcrumbs')
    <span class="breadcrumb-current">Bulan Magang</span>
@endsection

@section('page_actions')
    <button type="button" onclick="openTambahBulanModal()" style="display: inline-flex; align-items: center; gap: 0.5rem; background: var(--blue-600); color: #FFFFFF; border: none; padding: 0.55rem 1.25rem; border-radius: 8px; font-weight: 600; font-size: 0.875rem; cursor: pointer; box-shadow: var(--shadow-subtle);">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        <span>Tambah Periode Bulan</span>
    </button>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <!-- Data Table Card -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-subtle);">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-400); display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-800); margin: 0;">Daftar Periode Bulan Magang</h2>
            <span style="font-size: 0.8125rem; color: var(--neutral-600);">Total: {{ $bulanList->total() }} Periode</span>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                <thead>
                    <tr style="background: var(--neutral-200); border-bottom: 1px solid var(--neutral-400); height: 52px;">
                        <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Bulan Magang</th>
                        <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Periode Buka Pendaftaran</th>
                        <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Override Status</th>
                        <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Status Aktif</th>
                        <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800); text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bulanList as $bulan)
                        <tr style="border-bottom: 1px solid var(--neutral-400); height: 56px;">
                            <td style="padding: 0.75rem 1rem; font-weight: 700; color: var(--blue-700);">
                                {{ $bulan->bulan_mulai?->translatedFormat('F Y') }}
                            </td>
                            <td style="padding: 0.75rem 1rem; color: var(--neutral-700);">
                                {{ $bulan->tgl_buka?->format('d M Y') }} s/d {{ $bulan->tgl_tutup?->format('d M Y') }}
                            </td>
                            <td style="padding: 0.75rem 1rem;">
                                @if($bulan->override_status)
                                    <span class="role-chip-badge" style="background: var(--blue-tint); color: var(--blue-700); font-weight: 700;">
                                        Override: {{ strtoupper($bulan->override_status->value) }}
                                    </span>
                                @else
                                    <span style="color: var(--neutral-500); font-size: 0.8125rem;">Otomatis (Jadwal)</span>
                                @endif
                            </td>
                            <td style="padding: 0.75rem 1rem;">
                                @if($bulan->aktif)
                                    <span class="role-chip-badge" style="background: var(--green-100); color: var(--green-700); font-weight: 700;">
                                        Aktif
                                    </span>
                                @else
                                    <span class="role-chip-badge" style="background: var(--red-100); color: var(--red-700); font-weight: 700;">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 0.75rem 1rem; text-align: right;">
                                <div style="display: inline-flex; gap: 0.375rem; align-items: center;">
                                    <button type="button" onclick="openEditBulanModal({{ $bulan->id }}, '{{ $bulan->bulan_mulai?->format('Y-m-d') }}', '{{ $bulan->tgl_buka?->format('Y-m-d') }}', '{{ $bulan->tgl_tutup?->format('Y-m-d') }}', '{{ $bulan->override_status?->value ?? '' }}')" style="padding: 0.375rem 0.75rem; background: var(--blue-tint); color: var(--blue-700); border: 1px solid var(--blue-300); border-radius: 6px; font-weight: 600; font-size: 0.75rem; cursor: pointer;">
                                        Edit
                                    </button>
                                    <form method="POST" action="{{ route('superadmin.bulan.toggle', $bulan) }}" style="display: inline-block;" onsubmit="return confirm('Ubah status aktif periode bulan ini?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" style="padding: 0.375rem 0.75rem; background: var(--neutral-200); color: var(--neutral-800); border: 1px solid var(--neutral-400); border-radius: 6px; font-weight: 600; font-size: 0.75rem; cursor: pointer;">
                                            {{ $bulan->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding: 2rem; text-align: center; color: var(--neutral-600);">
                                Belum ada data periode bulan magang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bulanList->hasPages())
            <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--neutral-400);">
                {{ $bulanList->links() }}
            </div>
        @endif
    </div>
</div>

<!-- MODAL TAMBAH BULAN MAGANG -->
<div id="modal-tambah-bulan" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; align-items: center; justify-content: center;">
    <div style="background: var(--neutral-0); border-radius: 12px; max-width: 500px; width: 90%; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
        <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-800); margin: 0 0 0.5rem 0;">Tambah Periode Bulan Magang</h3>
        <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0 0 1.25rem 0;">
            Buat jadwal pembukaan periode magang baru untuk calon peserta dinas.
        </p>

        <form method="POST" action="{{ route('superadmin.bulan.store') }}">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 0.375rem; margin-bottom: 1rem;">
                <label style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">Tanggal Bulan Mulai Magang <span style="color: var(--red-600);">*</span></label>
                <input type="date" name="bulan_mulai" required style="height: 42px; border: 1px solid var(--neutral-500); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                <small style="color: var(--neutral-600); font-size: 0.75rem;">Pilih tanggal awal bulan magang (misal: 01/11/2026).</small>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div style="display: flex; flex-direction: column; gap: 0.375rem;">
                    <label style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">Tanggal Buka <span style="color: var(--red-600);">*</span></label>
                    <input type="date" name="tgl_buka" required style="height: 42px; border: 1px solid var(--neutral-500); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                </div>
                <div style="display: flex; flex-direction: column; gap: 0.375rem;">
                    <label style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">Tanggal Tutup <span style="color: var(--red-600);">*</span></label>
                    <input type="date" name="tgl_tutup" required style="height: 42px; border: 1px solid var(--neutral-500); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.375rem; margin-bottom: 1.25rem;">
                <label style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">Override Status Ketersediaan (Opsional)</label>
                <select name="override_status" style="height: 42px; border: 1px solid var(--neutral-500); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                    <option value="">-- Otomatis Berdasarkan Jadwal --</option>
                    @foreach($statusEnum as $status)
                        <option value="{{ $status->value }}">{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                <button type="button" onclick="closeTambahBulanModal()" style="padding: 0.5rem 1rem; background: var(--neutral-200); border: none; border-radius: 6px; font-weight: 600; font-size: 0.8125rem; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="padding: 0.5rem 1.25rem; background: var(--blue-600); color: #FFFFFF; border: none; border-radius: 6px; font-weight: 600; font-size: 0.8125rem; cursor: pointer;">
                    Simpan Periode
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT BULAN MAGANG -->
<div id="modal-edit-bulan" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; align-items: center; justify-content: center;">
    <div style="background: var(--neutral-0); border-radius: 12px; max-width: 500px; width: 90%; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
        <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-800); margin: 0 0 0.5rem 0;">Perbarui Periode Bulan Magang</h3>
        <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0 0 1.25rem 0;">
            Sesuaikan jadwal pendaftaran atau ubah status override ketersediaan.
        </p>

        <form method="POST" id="form-edit-bulan" action="">
            @csrf
            @method('PUT')
            <div style="display: flex; flex-direction: column; gap: 0.375rem; margin-bottom: 1rem;">
                <label style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">Tanggal Bulan Mulai Magang <span style="color: var(--red-600);">*</span></label>
                <input type="date" id="edit-bulan-mulai" name="bulan_mulai" required style="height: 42px; border: 1px solid var(--neutral-500); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div style="display: flex; flex-direction: column; gap: 0.375rem;">
                    <label style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">Tanggal Buka <span style="color: var(--red-600);">*</span></label>
                    <input type="date" id="edit-tgl-buka" name="tgl_buka" required style="height: 42px; border: 1px solid var(--neutral-500); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                </div>
                <div style="display: flex; flex-direction: column; gap: 0.375rem;">
                    <label style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">Tanggal Tutup <span style="color: var(--red-600);">*</span></label>
                    <input type="date" id="edit-tgl-tutup" name="tgl_tutup" required style="height: 42px; border: 1px solid var(--neutral-500); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.375rem; margin-bottom: 1.25rem;">
                <label style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">Override Status Ketersediaan</label>
                <select id="edit-override-status" name="override_status" style="height: 42px; border: 1px solid var(--neutral-500); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                    <option value="">-- Otomatis Berdasarkan Jadwal --</option>
                    @foreach($statusEnum as $status)
                        <option value="{{ $status->value }}">{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                <button type="button" onclick="closeEditBulanModal()" style="padding: 0.5rem 1rem; background: var(--neutral-200); border: none; border-radius: 6px; font-weight: 600; font-size: 0.8125rem; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="padding: 0.5rem 1.25rem; background: var(--blue-600); color: #FFFFFF; border: none; border-radius: 6px; font-weight: 600; font-size: 0.8125rem; cursor: pointer;">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openTambahBulanModal() {
        document.getElementById('modal-tambah-bulan').style.display = 'flex';
    }
    function closeTambahBulanModal() {
        document.getElementById('modal-tambah-bulan').style.display = 'none';
    }

    function openEditBulanModal(bulanId, bulanMulai, tglBuka, tglTutup, overrideStatus) {
        document.getElementById('form-edit-bulan').action = '/superadmin/bulan/' + bulanId;
        document.getElementById('edit-bulan-mulai').value = bulanMulai;
        document.getElementById('edit-tgl-buka').value = tglBuka;
        document.getElementById('edit-tgl-tutup').value = tglTutup;
        document.getElementById('edit-override-status').value = overrideStatus;
        document.getElementById('modal-edit-bulan').style.display = 'flex';
    }
    function closeEditBulanModal() {
        document.getElementById('modal-edit-bulan').style.display = 'none';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeTambahBulanModal();
            closeEditBulanModal();
        }
    });
</script>
@endsection
