@extends('layouts.dashboard')

@section('title', 'Master Bidang - SuperAdmin MagKom')
@section('page_title', 'Master Bidang Dinas Kominfo')
@section('page_description', 'Kelola daftar unit kerja / bidang dinas tempat penempatan peserta magang di lingkungan Diskominfo Sleman.')

@section('breadcrumbs')
    <a href="{{ route('superadmin.dashboard') }}">SuperAdmin</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Master Bidang</span>
@endsection

@section('page_actions')
    <button type="button" onclick="document.getElementById('modalTambahBidang').style.display='flex'" style="display: inline-flex; align-items: center; gap: 0.5rem; height: 42px; padding: 0 1.25rem; background: var(--blue-600); color: #FFFFFF; font-weight: 700; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer; transition: all 0.15s ease; box-shadow: var(--shadow-subtle);">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        <span>Tambah Bidang Baru</span>
    </button>
@endsection

@push('styles')
<style>
    /* Dark Mode Overrides for SuperAdmin Bidang */
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
    [data-theme="dark"] #modalTambahBidang > div,
    [data-theme="dark"] #modalEditBidang > div {
        background-color: #182234 !important;
        border-color: #26354D !important;
        color: #F8FAFC !important;
    }

    [data-theme="dark"] #modalTambahBidang h3,
    [data-theme="dark"] #modalEditBidang h3 {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] input[type="text"],
    [data-theme="dark"] textarea {
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

    <!-- 1. ALERT BANNER UNIT KERJA PENEMPATAN -->
    <div style="background: var(--blue-tint); border: 1px solid var(--blue-tint-line); border-left: 4px solid var(--blue-600); border-radius: 12px; padding: 1.25rem 1.5rem; display: flex; align-items: flex-start; gap: 1rem; box-shadow: var(--shadow-subtle);">
        <div style="width: 36px; height: 36px; border-radius: 8px; background: var(--blue-600); color: #FFFFFF; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 21h18"></path>
                <path d="M5 21V7l8-4v18"></path>
                <path d="M19 21V11l-6-3"></path>
                <path d="M9 9h1"></path>
                <path d="M9 13h1"></path>
                <path d="M9 17h1"></path>
            </svg>
        </div>
        <div>
            <h2 style="font-size: 0.9375rem; font-weight: 700; color: var(--blue-700); margin: 0 0 0.25rem 0;">
                Unit Kerja Penempatan Magang
            </h2>
            <p style="font-size: 0.8125rem; color: var(--neutral-700); margin: 0; line-height: 1.5;">
                Setiap peserta magang yang lolos seleksi akan ditempatkan di salah satu bidang unit kerja dinas di bawah bimbingan staf pembimbing lapangan terkait sesuai formasi penugasan.
            </p>
        </div>
    </div>

    <!-- 2. DATA TABLE CARD -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-subtle);">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-400); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
            <div>
                <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-800); margin: 0;">Daftar Bidang & Unit Kerja Diskominfo</h3>
                <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0.125rem 0 0 0;">Struktur organisasi dinas tempat alokasi bimbingan teknis magang.</p>
            </div>
            <span style="font-size: 0.8125rem; color: var(--neutral-600); font-weight: 600;">
                Total Bidang: <strong>{{ method_exists($bidangList, 'total') ? $bidangList->total() : $bidangList->count() }}</strong>
            </span>
        </div>

        @if($bidangList->isEmpty())
            <div style="padding: 4rem 1.5rem; text-align: center; color: var(--neutral-600);">
                <div style="width: 48px; height: 48px; margin: 0 auto 1rem; border-radius: 50%; background: var(--neutral-100); display: flex; align-items: center; justify-content: center; color: var(--neutral-500);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><line x1="9" y1="22" x2="9" y2="2"></line><line x1="15" y1="12" x2="15" y2="12.01"></line></svg>
                </div>
                <p style="font-size: 1rem; font-weight: 600; color: var(--neutral-800); margin: 0 0 0.25rem 0;">Belum ada master data bidang dinas.</p>
                <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0;">Klik tombol <strong>Tambah Bidang Baru</strong> untuk mendaftarkan unit kerja.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem; text-align: left;">
                    <thead>
                        <tr style="background: var(--neutral-100); border-bottom: 1px solid var(--neutral-400); color: var(--neutral-700); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <th style="padding: 1rem 1.25rem; font-weight: 700;">Nama Bidang Dinas</th>
                            <th style="padding: 1rem 1rem; font-weight: 700;">Deskripsi Tugas & Fungsi</th>
                            <th style="padding: 1rem 1rem; font-weight: 700; text-align: center; width: 140px;">Jumlah Staf</th>
                            <th style="padding: 1rem 1rem; font-weight: 700; width: 130px; text-align: center;">Status</th>
                            <th style="padding: 1rem 1.25rem; font-weight: 700; text-align: right; width: 200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bidangList as $bidang)
                            <tr style="border-bottom: 1px solid var(--neutral-200); transition: background 0.15s ease;" onmouseover="this.style.background='var(--neutral-100)'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 1rem 1.25rem; font-weight: 700; color: var(--neutral-900); white-space: nowrap;">
                                    <div style="font-size: 0.9375rem; color: var(--neutral-900);">
                                        {{ $bidang->nama_bidang }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--neutral-500); font-weight: 500; margin-top: 0.2rem;">
                                        ID #{{ $bidang->id }}
                                    </div>
                                </td>
                                <td style="padding: 1rem 1rem; color: var(--neutral-700); font-size: 0.8125rem; max-width: 380px; line-height: 1.45;">
                                    {{ $bidang->deskripsi ?? '-' }}
                                </td>
                                <td style="padding: 1rem 1rem; text-align: center;">
                                    <span style="display: inline-flex; align-items: center; justify-content: center; gap: 0.25rem; padding: 0.25rem 0.625rem; border-radius: 8px; background: var(--blue-tint); color: var(--blue-700); font-weight: 800; font-size: 0.875rem; border: 1px solid var(--blue-tint-line);">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                                        {{ $bidang->users_count }} Staf
                                    </span>
                                </td>
                                <td style="padding: 1rem 1rem; text-align: center; white-space: nowrap;">
                                    @if($bidang->aktif)
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
                                        <button type="button" onclick="openModalEditBidang('{{ $bidang->id }}', '{{ addslashes($bidang->nama_bidang) }}', '{{ addslashes($bidang->deskripsi ?? '') }}')" style="padding: 0.45rem 0.875rem; background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 6px; font-size: 0.75rem; font-weight: 700; color: var(--neutral-800); cursor: pointer; transition: all 0.15s; display: inline-flex; align-items: center; gap: 0.375rem;" onmouseover="this.style.background='var(--neutral-100)'" onmouseout="this.style.background='var(--neutral-0)'">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                            Edit
                                        </button>

                                        <form action="{{ route('superadmin.bidang.toggle', $bidang) }}" method="POST" style="display: inline-block; margin: 0;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" onclick="return confirm('{{ $bidang->aktif ? 'Nonaktifkan bidang ini?' : 'Aktifkan kembali bidang ini?' }}')" style="padding: 0.45rem 0.875rem; background: {{ $bidang->aktif ? 'var(--red-100)' : 'var(--green-100)' }}; border: 1px solid {{ $bidang->aktif ? 'var(--red-300)' : 'rgba(22, 163, 74, 0.25)' }}; border-radius: 6px; font-size: 0.75rem; font-weight: 700; color: {{ $bidang->aktif ? 'var(--red-700)' : 'var(--green-700)' }}; cursor: pointer; transition: all 0.15s;">
                                                {{ $bidang->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($bidangList->hasPages())
                <div style="padding: 1.25rem 1.5rem; border-top: 1px solid var(--neutral-200); background: var(--neutral-100);">
                    {{ $bidangList->links() }}
                </div>
            @endif
        @endif
    </div>
</div>

<!-- =====================================================================
     MODAL TAMBAH BIDANG
     ===================================================================== -->
<div id="modalTambahBidang" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
    <div style="background: var(--neutral-0); border-radius: 16px; width: 100%; max-width: 500px; padding: 2rem; box-shadow: var(--shadow-modal); position: relative; border: 1px solid var(--neutral-400);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.875rem;">
            <div style="display: flex; align-items: center; gap: 0.625rem;">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: var(--blue-tint); color: var(--blue-700); display: flex; align-items: center; justify-content: center;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                </div>
                <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Tambah Bidang Dinas Baru</h3>
            </div>
            <button type="button" onclick="document.getElementById('modalTambahBidang').style.display='none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--neutral-500); line-height: 1; padding: 0.25rem;">&times;</button>
        </div>

        <form action="{{ route('superadmin.bidang.store') }}" method="POST" style="display: flex; flex-direction: column; gap: 1.25rem;">
            @csrf
            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Nama Bidang <span style="color: var(--red-600);">*</span>
                </label>
                <input type="text" name="nama_bidang" required placeholder="Contoh: Bidang Aplikasi Informatika (Aptika)" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
            </div>

            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Deskripsi Tugas & Fungsi
                </label>
                <textarea name="deskripsi" rows="3" placeholder="Uraian singkat bidang kerja dan lingkup bimbingan..." style="width: 100%; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0.75rem; font-size: 0.875rem; font-family: inherit; line-height: 1.4; resize: vertical;"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem; border-top: 1px solid var(--neutral-200); padding-top: 1rem;">
                <button type="button" onclick="document.getElementById('modalTambahBidang').style.display='none'" style="height: 40px; padding: 0 1.25rem; background: var(--neutral-100); border: 1px solid var(--neutral-400); border-radius: 8px; font-weight: 700; font-size: 0.875rem; color: var(--neutral-700); cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="height: 40px; padding: 0 1.5rem; background: var(--blue-600); color: #FFFFFF; border: none; border-radius: 8px; font-weight: 700; font-size: 0.875rem; cursor: pointer; box-shadow: var(--shadow-subtle);">
                    Simpan Bidang
                </button>
            </div>
        </form>
    </div>
</div>

<!-- =====================================================================
     MODAL EDIT BIDANG
     ===================================================================== -->
<div id="modalEditBidang" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
    <div style="background: var(--neutral-0); border-radius: 16px; width: 100%; max-width: 500px; padding: 2rem; box-shadow: var(--shadow-modal); position: relative; border: 1px solid var(--neutral-400);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.875rem;">
            <div style="display: flex; align-items: center; gap: 0.625rem;">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: var(--blue-tint); color: var(--blue-700); display: flex; align-items: center; justify-content: center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                </div>
                <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Edit Master Bidang</h3>
            </div>
            <button type="button" onclick="document.getElementById('modalEditBidang').style.display='none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--neutral-500); line-height: 1; padding: 0.25rem;">&times;</button>
        </div>

        <form id="formEditBidang" method="POST" style="display: flex; flex-direction: column; gap: 1.25rem;">
            @csrf
            @method('PUT')
            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Nama Bidang <span style="color: var(--red-600);">*</span>
                </label>
                <input type="text" id="editNamaBidang" name="nama_bidang" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
            </div>

            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Deskripsi Tugas & Fungsi
                </label>
                <textarea id="editDeskripsiBidang" name="deskripsi" rows="3" style="width: 100%; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0.75rem; font-size: 0.875rem; font-family: inherit; line-height: 1.4; resize: vertical;"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem; border-top: 1px solid var(--neutral-200); padding-top: 1rem;">
                <button type="button" onclick="document.getElementById('modalEditBidang').style.display='none'" style="height: 40px; padding: 0 1.25rem; background: var(--neutral-100); border: 1px solid var(--neutral-400); border-radius: 8px; font-weight: 700; font-size: 0.875rem; color: var(--neutral-700); cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="height: 40px; padding: 0 1.5rem; background: var(--blue-600); color: #FFFFFF; border: none; border-radius: 8px; font-weight: 700; font-size: 0.875rem; cursor: pointer; box-shadow: var(--shadow-subtle);">
                    Perbarui Bidang
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModalEditBidang(id, nama, deskripsi) {
        document.getElementById('editNamaBidang').value = nama;
        document.getElementById('editDeskripsiBidang').value = deskripsi;
        document.getElementById('formEditBidang').action = '/superadmin/bidang/' + id;
        document.getElementById('modalEditBidang').style.display = 'flex';
    }
</script>
@endsection
