@extends('layouts.dashboard')

@section('title', 'Master Bidang - SuperAdmin MagKom')
@section('page_title', 'Master Bidang Dinas Kominfo')
@section('page_description', 'Kelola daftar unit kerja / bidang dinas tempat penempatan peserta magang di lingkungan Diskominfo Sleman.')

@section('breadcrumbs')
    <span class="breadcrumb-current">Master Bidang</span>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <!-- Top Action Bar -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1rem; font-weight: 700; color: var(--neutral-900); margin: 0 0 0.25rem 0;">Unit Kerja Penempatan Magang</h2>
            <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0;">
                Setiap peserta magang yang diterima akan ditempatkan di salah satu bidang dinas di bawah bimbingan staf pembimbing terkait.
            </p>
        </div>

        <button onclick="document.getElementById('modalTambahBidang').style.display='flex'" style="display: inline-flex; align-items: center; gap: 0.5rem; height: 40px; padding: 0 1.25rem; background: var(--blue-600); color: #FFFFFF; font-weight: 700; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer; transition: background 0.15s;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Tambah Bidang Baru
        </button>
    </div>

    <!-- Data Table Card -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-subtle);">
        @if($bidangList->isEmpty())
            <div style="padding: 4rem 1.5rem; text-align: center; color: var(--neutral-600);">
                <p style="font-size: 1rem; margin: 0;">Belum ada master data bidang dinas.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem; text-align: left;">
                    <thead>
                        <tr style="background: var(--neutral-100); border-bottom: 1px solid var(--neutral-400); color: var(--neutral-700); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <th style="padding: 1rem 1.25rem; font-weight: 700;">Nama Bidang Dinas</th>
                            <th style="padding: 1rem 1rem; font-weight: 700;">Deskripsi Tugas & Fungsi</th>
                            <th style="padding: 1rem 1rem; font-weight: 700; text-align: center;">Jumlah Staf</th>
                            <th style="padding: 1rem 1rem; font-weight: 700;">Status</th>
                            <th style="padding: 1rem 1.25rem; font-weight: 700; text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody style="divide-y: 1px solid var(--neutral-300);">
                        @foreach($bidangList as $bidang)
                            <tr style="border-bottom: 1px solid var(--neutral-300); transition: background 0.15s;" onmouseover="this.style.background='var(--neutral-100)'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 1rem 1.25rem; font-weight: 700; color: var(--neutral-900); white-space: nowrap;">
                                    {{ $bidang->nama_bidang }}
                                </td>
                                <td style="padding: 1rem 1rem; color: var(--neutral-700); font-size: 0.8125rem; max-width: 360px;">
                                    {{ $bidang->deskripsi ?? '-' }}
                                </td>
                                <td style="padding: 1rem 1rem; text-align: center;">
                                    <span style="font-weight: 700; color: var(--blue-700); font-size: 0.9375rem;">
                                        {{ $bidang->users_count }}
                                    </span>
                                </td>
                                <td style="padding: 1rem 1rem; white-space: nowrap;">
                                    @if($bidang->aktif)
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
                                    <div style="display: inline-flex; gap: 0.5rem; align-items: center;">
                                        <button onclick="openModalEditBidang('{{ $bidang->id }}', '{{ addslashes($bidang->nama_bidang) }}', '{{ addslashes($bidang->deskripsi ?? '') }}')" style="padding: 0.375rem 0.75rem; background: var(--neutral-100); border: 1px solid var(--neutral-400); border-radius: 6px; font-size: 0.75rem; font-weight: 600; color: var(--neutral-800); cursor: pointer;">
                                            Edit
                                        </button>

                                        <form action="{{ route('superadmin.bidang.toggle', $bidang) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" onclick="return confirm('{{ $bidang->aktif ? 'Nonaktifkan bidang ini?' : 'Aktifkan kembali bidang ini?' }}')" style="padding: 0.375rem 0.75rem; background: {{ $bidang->aktif ? 'var(--red-50)' : 'var(--green-50)' }}; border: 1px solid {{ $bidang->aktif ? 'var(--red-200)' : 'var(--green-200)' }}; border-radius: 6px; font-size: 0.75rem; font-weight: 600; color: {{ $bidang->aktif ? 'var(--red-700)' : 'var(--green-800)' }}; cursor: pointer;">
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
                <div style="padding: 1rem 1.25rem; border-top: 1px solid var(--neutral-300);">
                    {{ $bidangList->links() }}
                </div>
            @endif
        @endif
    </div>
</div>

<!-- Modal Tambah Bidang -->
<div id="modalTambahBidang" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
    <div style="background: var(--neutral-0); border-radius: 16px; width: 100%; max-width: 480px; padding: 2rem; box-shadow: var(--shadow-card); position: relative;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-300); padding-bottom: 0.75rem;">
            <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Tambah Bidang Dinas Baru</h3>
            <button onclick="document.getElementById('modalTambahBidang').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: var(--neutral-600);">&times;</button>
        </div>

        <form action="{{ route('superadmin.bidang.store') }}" method="POST" style="display: flex; flex-direction: column; gap: 1rem;">
            @csrf
            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">Nama Bidang <span style="color: var(--red-600);">*</span></label>
                <input type="text" name="nama_bidang" required placeholder="Contoh: Bidang Aplikasi Informatika (Aptika)" style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
            </div>

            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">Deskripsi Tugas & Fungsi</label>
                <textarea name="deskripsi" rows="3" placeholder="Uraian singkat bidang kerja..." style="width: 100%; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0.625rem; font-size: 0.875rem; font-family: inherit;"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.75rem;">
                <button type="button" onclick="document.getElementById('modalTambahBidang').style.display='none'" style="height: 40px; padding: 0 1.25rem; background: var(--neutral-200); border: none; border-radius: 6px; font-weight: 600; font-size: 0.875rem; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="height: 40px; padding: 0 1.5rem; background: var(--blue-600); color: #FFFFFF; border: none; border-radius: 6px; font-weight: 700; font-size: 0.875rem; cursor: pointer;">
                    Simpan Bidang
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Bidang -->
<div id="modalEditBidang" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
    <div style="background: var(--neutral-0); border-radius: 16px; width: 100%; max-width: 480px; padding: 2rem; box-shadow: var(--shadow-card); position: relative;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-300); padding-bottom: 0.75rem;">
            <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Edit Master Bidang</h3>
            <button onclick="document.getElementById('modalEditBidang').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: var(--neutral-600);">&times;</button>
        </div>

        <form id="formEditBidang" method="POST" style="display: flex; flex-direction: column; gap: 1rem;">
            @csrf
            @method('PUT')
            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">Nama Bidang <span style="color: var(--red-600);">*</span></label>
                <input type="text" id="editNamaBidang" name="nama_bidang" required style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
            </div>

            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">Deskripsi Tugas & Fungsi</label>
                <textarea id="editDeskripsiBidang" name="deskripsi" rows="3" style="width: 100%; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0.625rem; font-size: 0.875rem; font-family: inherit;"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.75rem;">
                <button type="button" onclick="document.getElementById('modalEditBidang').style.display='none'" style="height: 40px; padding: 0 1.25rem; background: var(--neutral-200); border: none; border-radius: 6px; font-weight: 600; font-size: 0.875rem; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="height: 40px; padding: 0 1.5rem; background: var(--blue-600); color: #FFFFFF; border: none; border-radius: 6px; font-weight: 700; font-size: 0.875rem; cursor: pointer;">
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
