@extends('layouts.dashboard')

@section('title', 'Slot Khusus Internal - SuperAdmin MagKom')
@section('page_title', 'Manajemen Slot Khusus Internal')
@section('page_description', 'Kelola alokasi kuota khusus instansi kerjasama atau penugasan internal dinas (F-06).')

@section('breadcrumbs')
    <span class="breadcrumb-current">Slot Khusus</span>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <!-- Info Banner & Create Button -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1rem; font-weight: 700; color: var(--neutral-900); margin: 0 0 0.25rem 0;">Alokasi Jalur Khusus (F-06)</h2>
            <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0;">
                Slot khusus tidak mengurangi kuota umum publik dan dialokasikan untuk kemitraan strategis atau penugasan kedinasan khusus.
            </p>
        </div>

        <button onclick="document.getElementById('modalTambahSlot').style.display='flex'" style="display: inline-flex; align-items: center; gap: 0.5rem; height: 40px; padding: 0 1.25rem; background: var(--blue-600); color: #FFFFFF; font-weight: 700; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer; transition: background 0.15s;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Tambah Slot Khusus
        </button>
    </div>

    <!-- Data Table Card -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-subtle);">
        @if($slotList->isEmpty())
            <div style="padding: 4rem 1.5rem; text-align: center; color: var(--neutral-600);">
                <p style="font-size: 1rem; margin: 0;">Belum ada alokasi slot khusus internal yang dibuat.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem; text-align: left;">
                    <thead>
                        <tr style="background: var(--neutral-100); border-bottom: 1px solid var(--neutral-400); color: var(--neutral-700); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <th style="padding: 1rem 1.25rem; font-weight: 700;">Instansi / Keterangan</th>
                            <th style="padding: 1rem 1rem; font-weight: 700;">Bulan Periode</th>
                            <th style="padding: 1rem 1rem; font-weight: 700; text-align: center;">Kapasitas Slot</th>
                            <th style="padding: 1rem 1rem; font-weight: 700; text-align: center;">Terpakai</th>
                            <th style="padding: 1rem 1rem; font-weight: 700;">Status</th>
                            <th style="padding: 1rem 1.25rem; font-weight: 700; text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody style="divide-y: 1px solid var(--neutral-300);">
                        @foreach($slotList as $slot)
                            <tr style="border-bottom: 1px solid var(--neutral-300); transition: background 0.15s;" onmouseover="this.style.background='var(--neutral-100)'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 1rem 1.25rem;">
                                    <div style="font-weight: 700; color: var(--neutral-900);">{{ $slot->nama_instansi }}</div>
                                    <div style="font-size: 0.75rem; color: var(--neutral-600);">{{ $slot->keterangan ?? '-' }}</div>
                                </td>
                                <td style="padding: 1rem 1rem; white-space: nowrap;">
                                    <span style="font-weight: 600; color: var(--blue-700);">
                                        {{ $slot->bulan_mulai?->translatedFormat('F Y') }}
                                    </span>
                                </td>
                                <td style="padding: 1rem 1rem; text-align: center;">
                                    <span style="font-size: 1rem; font-weight: 800; color: var(--neutral-900);">{{ $slot->jumlah_slot }}</span>
                                </td>
                                <td style="padding: 1rem 1rem; text-align: center;">
                                    <span style="font-size: 1rem; font-weight: 800; color: {{ $slot->terpakai >= $slot->jumlah_slot ? 'var(--red-700)' : 'var(--blue-700)' }};">
                                        {{ $slot->terpakai }}
                                    </span>
                                </td>
                                <td style="padding: 1rem 1rem; white-space: nowrap;">
                                    @if($slot->aktif)
                                        <span style="display: inline-block; padding: 0.25rem 0.625rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: var(--green-50); color: var(--green-800);">
                                            Aktif
                                        </span>
                                    @else
                                        <span style="display: inline-block; padding: 0.25rem 0.625rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: var(--red-50); color: var(--red-700);">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 1rem 1.25rem; text-align: right; white-space: nowrap;">
                                    <form action="{{ route('superadmin.slot-khusus.toggle', $slot) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" onclick="return confirm('{{ $slot->aktif ? 'Nonaktifkan slot khusus ini?' : 'Aktifkan kembali slot khusus ini?' }}')" style="padding: 0.375rem 0.75rem; background: {{ $slot->aktif ? 'var(--red-50)' : 'var(--green-50)' }}; border: 1px solid {{ $slot->aktif ? 'var(--red-200)' : 'var(--green-200)' }}; border-radius: 6px; font-size: 0.75rem; font-weight: 600; color: {{ $slot->aktif ? 'var(--red-700)' : 'var(--green-800)' }}; cursor: pointer;">
                                            {{ $slot->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($slotList->hasPages())
                <div style="padding: 1rem 1.25rem; border-top: 1px solid var(--neutral-300);">
                    {{ $slotList->links() }}
                </div>
            @endif
        @endif
    </div>
</div>

<!-- Modal Tambah Slot Khusus -->
<div id="modalTambahSlot" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
    <div style="background: var(--neutral-0); border-radius: 16px; width: 100%; max-width: 480px; padding: 2rem; box-shadow: var(--shadow-card); position: relative;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-300); padding-bottom: 0.75rem;">
            <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Tambah Slot Khusus Internal</h3>
            <button onclick="document.getElementById('modalTambahSlot').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: var(--neutral-600);">&times;</button>
        </div>

        <form action="{{ route('superadmin.slot-khusus.store') }}" method="POST" style="display: flex; flex-direction: column; gap: 1rem;">
            @csrf
            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">Nama Instansi Mitra <span style="color: var(--red-600);">*</span></label>
                <input type="text" name="nama_instansi" required placeholder="Contoh: BSSN, Diskominfo DIY, UGM Lab" style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">Bulan Mulai <span style="color: var(--red-600);">*</span></label>
                    <input type="date" name="bulan_mulai" required style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">Jumlah Kuota Slot <span style="color: var(--red-600);">*</span></label>
                    <input type="number" name="jumlah" value="{{ $defaultSlot ?? 3 }}" min="0" max="100" required placeholder="Contoh: 3" style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                </div>
            </div>

            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">Keterangan & Catatan MoU</label>
                <textarea name="keterangan" rows="3" placeholder="Nomor surat / dasar penugasan kerjasama..." style="width: 100%; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0.625rem; font-size: 0.875rem; font-family: inherit;"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.75rem;">
                <button type="button" onclick="document.getElementById('modalTambahSlot').style.display='none'" style="height: 40px; padding: 0 1.25rem; background: var(--neutral-200); border: none; border-radius: 6px; font-weight: 600; font-size: 0.875rem; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="height: 40px; padding: 0 1.5rem; background: var(--blue-600); color: #FFFFFF; border: none; border-radius: 6px; font-weight: 700; font-size: 0.875rem; cursor: pointer;">
                    Simpan Slot Khusus
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
