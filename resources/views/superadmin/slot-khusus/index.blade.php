@extends('layouts.dashboard')

@section('title', 'Slot Khusus Internal - SuperAdmin MagKom')
@section('page_title', 'Manajemen Slot Khusus Internal')
@section('page_description', 'Kelola alokasi kuota khusus instansi kerjasama atau penugasan internal dinas (F-06).')

@section('breadcrumbs')
    <a href="{{ route('superadmin.dashboard') }}">SuperAdmin</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Slot Khusus</span>
@endsection

@section('page_actions')
    <button type="button" onclick="document.getElementById('modalTambahSlot').style.display='flex'" style="display: inline-flex; align-items: center; gap: 0.5rem; height: 42px; padding: 0 1.25rem; background: var(--blue-600); color: #FFFFFF; font-weight: 700; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer; transition: all 0.15s ease; box-shadow: var(--shadow-subtle);">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        <span>Tambah Slot Khusus</span>
    </button>
@endsection

@push('styles')
<style>
    /* Dark Mode Overrides for SuperAdmin Slot Khusus */
    [data-theme="dark"] div[style*="background: var(--neutral-0)"] {
        background-color: #182234 !important;
        border-color: #26354D !important;
    }

    [data-theme="dark"] div[style*="background: var(--blue-tint)"] {
        background-color: rgba(37, 99, 235, 0.15) !important;
        border-color: rgba(59, 130, 246, 0.3) !important;
    }

    [data-theme="dark"] div[style*="border: 1px solid var(--neutral-400)"],
    [data-theme="dark"] div[style*="border-bottom: 1px solid var(--neutral-400)"],
    [data-theme="dark"] div[style*="border-top: 1px solid var(--neutral-200)"],
    [data-theme="dark"] div[style*="border-bottom: 1px solid var(--neutral-200)"] {
        border-color: #26354D !important;
    }

    [data-theme="dark"] table thead tr {
        background-color: #141E30 !important;
        border-bottom-color: #26354D !important;
        color: #94A3B8 !important;
    }

    [data-theme="dark"] table th {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] table tbody tr {
        border-bottom-color: #26354D !important;
    }

    [data-theme="dark"] table tbody tr:hover {
        background-color: #162238 !important;
    }

    [data-theme="dark"] span[style*="color: var(--neutral-900)"],
    [data-theme="dark"] div[style*="color: var(--neutral-900)"],
    [data-theme="dark"] span[style*="color: var(--neutral-800)"],
    [data-theme="dark"] h3[style*="color: var(--neutral-800)"] {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] span[style*="color: var(--neutral-600)"],
    [data-theme="dark"] div[style*="color: var(--neutral-600)"],
    [data-theme="dark"] p[style*="color: var(--neutral-700)"],
    [data-theme="dark"] p[style*="color: var(--neutral-600)"] {
        color: #94A3B8 !important;
    }

    /* Modals Dark Mode */
    [data-theme="dark"] #modalTambahSlot > div,
    [data-theme="dark"] #modalEditSlot > div {
        background-color: #182234 !important;
        border-color: #26354D !important;
        color: #F8FAFC !important;
    }

    [data-theme="dark"] #modalTambahSlot h3,
    [data-theme="dark"] #modalEditSlot h3 {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] input[type="text"],
    [data-theme="dark"] input[type="number"],
    [data-theme="dark"] textarea,
    [data-theme="dark"] select {
        background-color: #0F172A !important;
        color: #F8FAFC !important;
        border-color: #26354D !important;
    }

    [data-theme="dark"] button[style*="background: var(--neutral-100)"] {
        background-color: #111C2E !important;
        border-color: #26354D !important;
        color: #94A3B8 !important;
    }

    [data-theme="dark"] button[style*="background: var(--neutral-100)"]:hover {
        background-color: #162238 !important;
        color: #F8FAFC !important;
    }
</style>
@endpush

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">

    <!-- 1. ALERT BANNER ATURAN JALUR KHUSUS (F-06) -->
    <div style="background: var(--blue-tint); border: 1px solid var(--blue-tint-line); border-left: 4px solid var(--blue-600); border-radius: 12px; padding: 1.25rem 1.5rem; display: flex; align-items: flex-start; gap: 1rem; box-shadow: var(--shadow-subtle);">
        <div style="width: 36px; height: 36px; border-radius: 8px; background: var(--blue-600); color: #FFFFFF; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
            </svg>
        </div>
        <div>
            <h2 style="font-size: 0.9375rem; font-weight: 700; color: var(--blue-700); margin: 0 0 0.25rem 0;">
                Alokasi Jalur Khusus (F-06)
            </h2>
            <p style="font-size: 0.8125rem; color: var(--neutral-700); margin: 0; line-height: 1.5;">
                Slot khusus tidak mengurangi kuota umum publik dan dialokasikan untuk kemitraan strategis lembaga/kampus atau penugasan kedinasan khusus atas instruksi pimpinan Kominfo.
            </p>
        </div>
    </div>

    <!-- 2. DATA TABLE CARD -->
    <x-gentelella.x-panel title="Daftar Alokasi Slot Khusus Mitra" subtitle="Monitoring kapasitas reservasi dan realisasi pendaftar jalur kemitraan.">
        <x-slot:toolbox>
            <span style="font-size: 0.8125rem; color: var(--neutral-600); font-weight: 600;">
                Total: <strong>{{ method_exists($slotList, 'total') ? $slotList->total() : $slotList->count() }} Slot</strong>
            </span>
        </x-slot:toolbox>

        @if($slotList->isEmpty())
            <div style="padding: 4rem 1.5rem; text-align: center; color: var(--neutral-600);">
                <div style="width: 48px; height: 48px; margin: 0 auto 1rem; border-radius: 50%; background: var(--neutral-100); display: flex; align-items: center; justify-content: center; color: var(--neutral-500);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                </div>
                <p style="font-size: 1rem; font-weight: 600; color: var(--neutral-800); margin: 0 0 0.25rem 0;">Belum ada alokasi slot khusus internal yang dibuat.</p>
                <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0;">Klik tombol <strong>Tambah Slot Khusus</strong> untuk mengalokasikan kuota mitra.</p>
            </div>
        @else
            <div class="table-responsive-gentelella" style="overflow-x: auto;">
                <table class="table jambo_table bulk_action" style="width: 100%; border-collapse: collapse; font-size: 0.875rem; text-align: left;">
                    <thead>
                        <tr class="headings" style="background: var(--neutral-100); border-bottom: 1px solid var(--neutral-400); color: var(--neutral-700); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <th style="padding: 1rem 1.25rem; font-weight: 700;">Instansi / Keterangan</th>
                            <th style="padding: 1rem 1rem; font-weight: 700; width: 180px;">Bulan Magang</th>
                            <th style="padding: 1rem 1rem; font-weight: 700; text-align: center; width: 140px;">Kapasitas Slot</th>
                            <th style="padding: 1rem 1rem; font-weight: 700; text-align: center; width: 140px;">Terpakai</th>
                            <th style="padding: 1rem 1rem; font-weight: 700; width: 130px; text-align: center;">Status</th>
                            <th style="padding: 1rem 1.25rem; font-weight: 700; text-align: right; width: 160px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($slotList as $slot)
                            <tr style="border-bottom: 1px solid var(--neutral-200); transition: background 0.15s ease;" onmouseover="this.style.background='var(--neutral-100)'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 1rem 1.25rem;">
                                    <div style="font-weight: 700; color: var(--neutral-900); font-size: 0.9375rem;">
                                        {{ $slot->nama_instansi }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.25rem; line-height: 1.4;">
                                        {{ $slot->keterangan ?? 'Tanpa catatan khusus' }}
                                    </div>
                                </td>
                                <td style="padding: 1rem 1rem; white-space: nowrap;">
                                    <span style="display: inline-flex; align-items: center; gap: 0.375rem; font-weight: 700; color: var(--blue-700); font-size: 0.8125rem;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line></svg>
                                        {{ $slot->bulan_mulai?->translatedFormat('F Y') }}
                                    </span>
                                </td>
                                <td style="padding: 1rem 1rem; text-align: center;">
                                    <span style="display: inline-flex; align-items: center; justify-content: center; min-width: 38px; height: 32px; border-radius: 8px; font-size: 1rem; font-weight: 800; background: var(--neutral-100); color: var(--neutral-900); border: 1px solid var(--neutral-300);">
                                        {{ $slot->jumlah_slot }}
                                    </span>
                                </td>
                                <td style="padding: 1rem 1rem; text-align: center;">
                                    <span style="display: inline-flex; align-items: center; justify-content: center; min-width: 38px; height: 32px; border-radius: 8px; font-size: 1rem; font-weight: 800; background: {{ $slot->terpakai >= $slot->jumlah_slot ? 'var(--red-100)' : 'var(--blue-tint)' }}; color: {{ $slot->terpakai >= $slot->jumlah_slot ? 'var(--red-700)' : 'var(--blue-700)' }}; border: 1px solid {{ $slot->terpakai >= $slot->jumlah_slot ? 'var(--red-300)' : 'var(--blue-tint-line)' }};">
                                        {{ $slot->terpakai }}
                                    </span>
                                </td>
                                <td style="padding: 1rem 1rem; text-align: center; white-space: nowrap;">
                                    @if($slot->aktif)
                                        <span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.3rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: var(--green-100); color: var(--green-700); border: 1px solid rgba(220, 38, 38, 0.2);">
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: var(--green-600);"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.3rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: var(--red-100); color: var(--red-700); border: 1px solid rgba(220, 38, 38, 0.2);">
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: var(--red-600);"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 1rem 1.25rem; text-align: right; white-space: nowrap;">
                                    <form action="{{ route('superadmin.slot-khusus.toggle', $slot) }}" method="POST" style="display: inline-block; margin: 0;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" onclick="return confirm('{{ $slot->aktif ? 'Nonaktifkan slot khusus ini?' : 'Aktifkan kembali slot khusus ini?' }}')" style="padding: 0.45rem 0.875rem; background: {{ $slot->aktif ? 'var(--red-100)' : 'var(--green-100)' }}; border: 1px solid {{ $slot->aktif ? 'var(--red-300)' : 'rgba(22, 163, 74, 0.25)' }}; border-radius: 6px; font-size: 0.75rem; font-weight: 700; color: {{ $slot->aktif ? 'var(--red-700)' : 'var(--green-700)' }}; cursor: pointer; transition: all 0.15s;">
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
                <div style="padding: 1.25rem 1.5rem; border-top: 1px solid var(--neutral-200); background: var(--neutral-100);">
                    {{ $slotList->links() }}
                </div>
            @endif
        @endif
    </x-gentelella.x-panel>
</div>

<!-- =====================================================================
     MODAL TAMBAH SLOT KHUSUS
     ===================================================================== -->
<div id="modalTambahSlot" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
    <div style="background: var(--neutral-0); border-radius: 16px; width: 100%; max-width: 500px; padding: 2rem; box-shadow: var(--shadow-modal); position: relative; border: 1px solid var(--neutral-400);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.875rem;">
            <div style="display: flex; align-items: center; gap: 0.625rem;">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: var(--blue-tint); color: var(--blue-700); display: flex; align-items: center; justify-content: center;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                </div>
                <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Tambah Slot Khusus Internal</h3>
            </div>
            <button type="button" onclick="document.getElementById('modalTambahSlot').style.display='none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--neutral-500); line-height: 1; padding: 0.25rem;">&times;</button>
        </div>

        <form action="{{ route('superadmin.slot-khusus.store') }}" method="POST" style="display: flex; flex-direction: column; gap: 1.25rem;">
            @csrf
            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Nama Instansi Mitra <span style="color: var(--red-600);">*</span>
                </label>
                <input type="text" name="nama_instansi" required placeholder="Contoh: BSSN, Diskominfo DIY, UGM Lab" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        Bulan Mulai <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="date" name="bulan_mulai" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        Jumlah Kuota Slot <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="number" name="jumlah" value="{{ $defaultSlot ?? 3 }}" min="0" max="100" required placeholder="Contoh: 3" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                </div>
            </div>

            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Keterangan & Catatan MoU
                </label>
                <textarea name="keterangan" rows="3" placeholder="Nomor surat / dasar penugasan kerjasama..." style="width: 100%; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0.75rem; font-size: 0.875rem; font-family: inherit; line-height: 1.4; resize: vertical;"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem; border-top: 1px solid var(--neutral-200); padding-top: 1rem;">
                <button type="button" onclick="document.getElementById('modalTambahSlot').style.display='none'" style="height: 40px; padding: 0 1.25rem; background: var(--neutral-100); border: 1px solid var(--neutral-400); border-radius: 8px; font-weight: 700; font-size: 0.875rem; color: var(--neutral-700); cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="height: 40px; padding: 0 1.5rem; background: var(--blue-600); color: #FFFFFF; border: none; border-radius: 8px; font-weight: 700; font-size: 0.875rem; cursor: pointer; box-shadow: var(--shadow-subtle);">
                    Simpan Slot Khusus
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
