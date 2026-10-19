@extends('layouts.dashboard')

@section('title', 'Bulan Magang - SuperAdmin MagKom Sleman')
@section('page_title', 'Manajemen Bulan Magang')
@section('page_description', 'Kelola jadwal pembukaan pendaftaran dan override status ketersediaan bulan magang.')

@section('breadcrumbs')
    <a href="{{ route('superadmin.dashboard') }}">SuperAdmin</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Bulan Magang</span>
@endsection

@section('page_actions')
    <button type="button" onclick="openTambahBulanModal()" style="display: inline-flex; align-items: center; gap: 0.5rem; height: 42px; padding: 0 1.25rem; background: var(--blue-600); color: #FFFFFF; font-weight: 700; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer; transition: all 0.15s ease; box-shadow: var(--shadow-subtle);">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        <span>Tambah Bulan Magang</span>
    </button>
@endsection

@push('styles')
<style>
    /* Dark Mode Overrides for SuperAdmin Bulan */
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
    [data-theme="dark"] #modal-tambah-bulan > div,
    [data-theme="dark"] #modal-edit-bulan > div {
        background-color: #182234 !important;
        border-color: #26354D !important;
        color: #F8FAFC !important;
    }

    [data-theme="dark"] #modal-tambah-bulan h3,
    [data-theme="dark"] #modal-edit-bulan h3 {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] input[type="text"],
    [data-theme="dark"] input[type="date"],
    [data-theme="dark"] select {
        background-color: #0F172A !important;
        color: #F8FAFC !important;
        border-color: #26354D !important;
        color-scheme: dark;
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

    <!-- 1. ALERT BANNER OPERASIONAL JADWAL -->
    <div style="background: var(--blue-tint); border: 1px solid var(--blue-tint-line); border-left: 4px solid var(--blue-600); border-radius: 12px; padding: 1.25rem 1.5rem; display: flex; align-items: flex-start; gap: 1rem; box-shadow: var(--shadow-subtle);">
        <div style="width: 36px; height: 36px; border-radius: 8px; background: var(--blue-600); color: #FFFFFF; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
        </div>
        <div>
            <h2 style="font-size: 0.9375rem; font-weight: 700; color: var(--blue-700); margin: 0 0 0.25rem 0;">
                Otomasi Siklus & Override Status Pendaftaran
            </h2>
            <p style="font-size: 0.8125rem; color: var(--neutral-700); margin: 0; line-height: 1.5;">
                Secara default sistem menghitung status ketersediaan berdasarkan rentang tanggal buka-tutup pendaftaran. SuperAdmin dapat melakukan intervensi manual (Override) jika kuota telah habis atau terdapat kebijakan diskresi khusus dinas.
            </p>
        </div>
    </div>

    <!-- 2. DATA TABLE CARD -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-subtle);">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-400); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
            <div>
                <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-800); margin: 0;">Daftar Bulan Magang</h3>
                <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0.125rem 0 0 0;">Riwayat jadwal gelombang magang dan status penerimaan calon peserta.</p>
            </div>
            <span style="font-size: 0.8125rem; color: var(--neutral-600); font-weight: 600;">
                Total: <strong>{{ $bulanList->total() }} Bulan Magang</strong>
            </span>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                <thead>
                    <tr style="background: var(--neutral-100); border-bottom: 1px solid var(--neutral-400); color: var(--neutral-700); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                        <th style="padding: 1rem 1.25rem; font-weight: 700;">Bulan Magang</th>
                        <th style="padding: 1rem 1rem; font-weight: 700;">Jadwal Buka Pendaftaran</th>
                        <th style="padding: 1rem 1rem; font-weight: 700;">Override Status</th>
                        <th style="padding: 1rem 1rem; font-weight: 700; width: 130px; text-align: center;">Status Aktif</th>
                        <th style="padding: 1rem 1.25rem; font-weight: 700; text-align: right; width: 200px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bulanList as $bulan)
                        <tr style="border-bottom: 1px solid var(--neutral-200); transition: background 0.15s ease;" onmouseover="this.style.background='var(--neutral-100)'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 1rem 1.25rem; font-weight: 700; color: var(--blue-700); font-size: 0.9375rem;">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--blue-600);"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line></svg>
                                    {{ $bulan->bulan_mulai?->translatedFormat('F Y') }}
                                </div>
                                <div style="font-size: 0.75rem; color: var(--neutral-500); font-weight: 500; margin-top: 0.2rem;">
                                    ID #{{ $bulan->id }}
                                </div>
                            </td>
                            <td style="padding: 1rem 1rem; color: var(--neutral-700); font-size: 0.8125rem;">
                                <div style="font-weight: 600; color: var(--neutral-800);">
                                    {{ $bulan->tgl_buka?->format('d M Y') }} s/d {{ $bulan->tgl_tutup?->format('d M Y') }}
                                </div>
                                <div style="font-size: 0.75rem; color: var(--neutral-500); margin-top: 0.15rem;">
                                    Durasi pendaftaran dinas
                                </div>
                            </td>
                            <td style="padding: 1rem 1rem; white-space: nowrap;">
                                @if($bulan->override_status)
                                    <span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.25rem 0.625rem; border-radius: 9999px; background: var(--blue-tint); color: var(--blue-700); font-weight: 700; font-size: 0.75rem; border: 1px solid var(--blue-tint-line);">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="3"></circle></svg>
                                        Override: {{ strtoupper($bulan->override_status->value) }}
                                    </span>
                                @else
                                    <span style="display: inline-flex; align-items: center; gap: 0.375rem; color: var(--neutral-500); font-size: 0.8125rem; font-weight: 500;">
                                        <span style="width: 6px; height: 6px; border-radius: 50%; background: var(--neutral-400);"></span>
                                        Otomatis (Jadwal)
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 1rem 1rem; text-align: center; white-space: nowrap;">
                                @if($bulan->aktif)
                                    <span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.3rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: var(--green-100); color: var(--green-700); border: 1px solid rgba(22, 163, 74, 0.2);">
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
                                <div style="display: inline-flex; gap: 0.5rem; align-items: center; justify-content: flex-end;">
                                    <button type="button" onclick="openEditBulanModal({{ $bulan->id }}, '{{ $bulan->bulan_mulai?->format('Y-m-d') }}', '{{ $bulan->tgl_buka?->format('Y-m-d') }}', '{{ $bulan->tgl_tutup?->format('Y-m-d') }}', '{{ $bulan->override_status?->value ?? '' }}')" style="padding: 0.45rem 0.875rem; background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 6px; font-size: 0.75rem; font-weight: 700; color: var(--neutral-800); cursor: pointer; transition: all 0.15s; display: inline-flex; align-items: center; gap: 0.375rem;" onmouseover="this.style.background='var(--neutral-100)'" onmouseout="this.style.background='var(--neutral-0)'">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                        Edit
                                    </button>

                                    <form method="POST" action="{{ route('superadmin.bulan.toggle', $bulan) }}" style="display: inline-block; margin: 0;" onsubmit="return confirm('Ubah status aktif bulan magang ini?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" style="padding: 0.45rem 0.875rem; background: {{ $bulan->aktif ? 'var(--red-100)' : 'var(--green-100)' }}; border: 1px solid {{ $bulan->aktif ? 'var(--red-300)' : 'rgba(22, 163, 74, 0.25)' }}; border-radius: 6px; font-size: 0.75rem; font-weight: 700; color: {{ $bulan->aktif ? 'var(--red-700)' : 'var(--green-700)' }}; cursor: pointer; transition: all 0.15s;">
                                            {{ $bulan->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding: 4rem 1.5rem; text-align: center; color: var(--neutral-600);">
                                <div style="width: 48px; height: 48px; margin: 0 auto 1rem; border-radius: 50%; background: var(--neutral-100); display: flex; align-items: center; justify-content: center; color: var(--neutral-500);">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line></svg>
                                </div>
                                <p style="font-size: 1rem; font-weight: 600; color: var(--neutral-800); margin: 0 0 0.25rem 0;">Belum ada data bulan magang.</p>
                                <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0;">Klik tombol <strong>Tambah Bulan Magang</strong> untuk memulai gelombang pendaftaran.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bulanList->hasPages())
            <div style="padding: 1.25rem 1.5rem; border-top: 1px solid var(--neutral-200); background: var(--neutral-100);">
                {{ $bulanList->links() }}
            </div>
        @endif
    </div>
</div>

<!-- =====================================================================
     MODAL TAMBAH BULAN MAGANG
     ===================================================================== -->
<div id="modal-tambah-bulan" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
    <div style="background: var(--neutral-0); border-radius: 16px; max-width: 500px; width: 100%; padding: 2rem; box-shadow: var(--shadow-modal); border: 1px solid var(--neutral-400); position: relative;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.875rem;">
            <div style="display: flex; align-items: center; gap: 0.625rem;">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: var(--blue-tint); color: var(--blue-700); display: flex; align-items: center; justify-content: center;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                </div>
                <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Tambah Bulan Magang</h3>
            </div>
            <button type="button" onclick="closeTambahBulanModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--neutral-500); line-height: 1; padding: 0.25rem;">&times;</button>
        </div>

        <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0 0 1.25rem 0; line-height: 1.4;">
            Buat jadwal pembukaan bulan magang baru untuk calon peserta dinas.
        </p>

        <form method="POST" action="{{ route('superadmin.bulan.store') }}" style="display: flex; flex-direction: column; gap: 1.25rem;">
            @csrf
            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Tanggal Bulan Mulai Magang <span style="color: var(--red-600);">*</span>
                </label>
                <input type="date" name="bulan_mulai" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                <small style="color: var(--neutral-600); font-size: 0.75rem; margin-top: 0.25rem; display: block;">Pilih tanggal awal bulan magang (misal: 01/11/2026).</small>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        Tanggal Buka <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="date" name="tgl_buka" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        Tanggal Tutup <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="date" name="tgl_tutup" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                </div>
            </div>

            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Override Status Ketersediaan (Opsional)
                </label>
                <select name="override_status" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; background: var(--neutral-0); font-family: inherit;">
                    <option value="">-- Otomatis Berdasarkan Jadwal --</option>
                    @foreach($statusEnum as $status)
                        <option value="{{ $status->value }}">{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem; border-top: 1px solid var(--neutral-200); padding-top: 1rem;">
                <button type="button" onclick="closeTambahBulanModal()" style="height: 40px; padding: 0 1.25rem; background: var(--neutral-100); border: 1px solid var(--neutral-400); border-radius: 8px; font-weight: 700; font-size: 0.875rem; color: var(--neutral-700); cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="height: 40px; padding: 0 1.5rem; background: var(--blue-600); color: #FFFFFF; border: none; border-radius: 8px; font-weight: 700; font-size: 0.875rem; cursor: pointer; box-shadow: var(--shadow-subtle);">
                    Simpan Bulan Magang
                </button>
            </div>
        </form>
    </div>
</div>

<!-- =====================================================================
     MODAL EDIT BULAN MAGANG
     ===================================================================== -->
<div id="modal-edit-bulan" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
    <div style="background: var(--neutral-0); border-radius: 16px; max-width: 500px; width: 100%; padding: 2rem; box-shadow: var(--shadow-modal); border: 1px solid var(--neutral-400); position: relative;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.875rem;">
            <div style="display: flex; align-items: center; gap: 0.625rem;">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: var(--blue-tint); color: var(--blue-700); display: flex; align-items: center; justify-content: center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                </div>
                <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Perbarui Bulan Magang</h3>
            </div>
            <button type="button" onclick="closeEditBulanModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--neutral-500); line-height: 1; padding: 0.25rem;">&times;</button>
        </div>

        <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0 0 1.25rem 0; line-height: 1.4;">
            Sesuaikan jadwal pendaftaran atau ubah status override ketersediaan.
        </p>

        <form method="POST" id="form-edit-bulan" action="" style="display: flex; flex-direction: column; gap: 1.25rem;">
            @csrf
            @method('PUT')
            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Tanggal Bulan Mulai Magang <span style="color: var(--red-600);">*</span>
                </label>
                <input type="date" id="edit-bulan-mulai" name="bulan_mulai" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        Tanggal Buka <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="date" id="edit-tgl-buka" name="tgl_buka" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        Tanggal Tutup <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="date" id="edit-tgl-tutup" name="tgl_tutup" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                </div>
            </div>

            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Override Status Ketersediaan
                </label>
                <select id="edit-override-status" name="override_status" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; background: var(--neutral-0); font-family: inherit;">
                    <option value="">-- Otomatis Berdasarkan Jadwal --</option>
                    @foreach($statusEnum as $status)
                        <option value="{{ $status->value }}">{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem; border-top: 1px solid var(--neutral-200); padding-top: 1rem;">
                <button type="button" onclick="closeEditBulanModal()" style="height: 40px; padding: 0 1.25rem; background: var(--neutral-100); border: 1px solid var(--neutral-400); border-radius: 8px; font-weight: 700; font-size: 0.875rem; color: var(--neutral-700); cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="height: 40px; padding: 0 1.5rem; background: var(--blue-600); color: #FFFFFF; border: none; border-radius: 8px; font-weight: 700; font-size: 0.875rem; cursor: pointer; box-shadow: var(--shadow-subtle);">
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
