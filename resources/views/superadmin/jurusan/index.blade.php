@extends('layouts.dashboard')

@section('title', 'Master Jurusan - SuperAdmin MagKom')
@section('page_title', 'Master Data Jurusan')
@section('page_description', 'Kelola daftar jurusan program pendidikan (SMK dan Perguruan Tinggi) untuk alokasi kuota magang.')

@section('breadcrumbs')
    <a href="{{ route('superadmin.dashboard') }}">SuperAdmin</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Master Jurusan</span>
@endsection

@section('page_actions')
    <button type="button" onclick="document.getElementById('modalTambahJurusan').style.display='flex'" style="display: inline-flex; align-items: center; gap: 0.5rem; height: 42px; padding: 0 1.25rem; background: var(--blue-600); color: #FFFFFF; font-weight: 700; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer; transition: all 0.15s ease; box-shadow: var(--shadow-subtle);">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        <span>Tambah Jurusan Baru</span>
    </button>
@endsection

@push('styles')
<style>
    /* Dark Mode Overrides for SuperAdmin Jurusan (DS-MagKom Industrial Standard) */
    [data-theme="dark"] div[style*="background: var(--neutral-0)"] {
        background-color: #003566 !important;
        border-color: #3F51B5 !important;
    }

    [data-theme="dark"] div[style*="background: var(--blue-tint)"] {
        background-color: rgba(59, 130, 246, 0.18) !important;
        border-color: rgba(147, 197, 253, 0.35) !important;
    }

    [data-theme="dark"] div[style*="border: 1px solid var(--neutral-400)"],
    [data-theme="dark"] div[style*="border-bottom: 1px solid var(--neutral-400)"],
    [data-theme="dark"] div[style*="border-top: 1px solid var(--neutral-200)"],
    [data-theme="dark"] div[style*="border-bottom: 1px solid var(--neutral-200)"] {
        border-color: rgba(63, 81, 181, 0.45) !important;
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
    [data-theme="dark"] #modalTambahJurusan > div,
    [data-theme="dark"] #modalEditJurusan > div {
        background-color: #003566 !important;
        border-color: #3F51B5 !important;
        color: #F8FAFC !important;
    }

    [data-theme="dark"] #modalTambahJurusan h3,
    [data-theme="dark"] #modalEditJurusan h3 {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] input[type="text"],
    [data-theme="dark"] select {
        background-color: #011C44 !important;
        color: #F8FAFC !important;
        border-color: rgba(63, 81, 181, 0.45) !important;
    }

    [data-theme="dark"] button[style*="background: var(--neutral-100)"] {
        background-color: rgba(255, 255, 255, 0.06) !important;
        border-color: rgba(63, 81, 181, 0.45) !important;
        color: #94A3B8 !important;
    }

    [data-theme="dark"] button[style*="background: var(--neutral-100)"]:hover {
        background-color: rgba(63, 81, 181, 0.2) !important;
        color: #F8FAFC !important;
    }
</style>
@endpush

@section('content')
<div class="sa-fluid-container" style="display: flex; flex-direction: column; gap: 1.5rem;">

    <!-- 1. ALERT BANNER ATURAN PENONAKTIFAN (BR-12) -->
    <div style="background: var(--blue-tint); border: 1px solid var(--blue-tint-line); border-left: 4px solid var(--blue-600); border-radius: 12px; padding: 1.25rem 1.5rem; display: flex; align-items: flex-start; gap: 1rem; box-shadow: var(--shadow-subtle);">
        <div style="width: 36px; height: 36px; border-radius: 8px; background: var(--blue-600); color: #FFFFFF; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
        </div>
        <div>
            <h2 style="font-size: 0.9375rem; font-weight: 700; color: var(--blue-700); margin: 0 0 0.25rem 0;">
                Aturan Penonaktifan Jurusan (BR-12)
            </h2>
            <p style="font-size: 0.8125rem; color: var(--neutral-700); margin: 0; line-height: 1.5;">
                Jurusan tidak dapat dinonaktifkan jika masih terikat dengan kuota aktif pada bulan pendaftaran berjalan atau masih memiliki peserta magang aktif yang ditempatkan di dinas.
            </p>
        </div>
    </div>

    <!-- 2. DATA TABLE CARD -->
    <x-gentelella.x-panel class="has-table" title="Master Program Studi & Jurusan" subtitle="Daftar program kualifikasi akademik yang diakomodasi untuk seleksi magang.">
        <x-slot:toolbox>
            <span style="font-size: 0.8125rem; color: var(--neutral-600); font-weight: 600;">
                Total Jurusan: <strong>{{ method_exists($jurusanList, 'total') ? $jurusanList->total() : $jurusanList->count() }}</strong>
            </span>
        </x-slot:toolbox>

        @if($jurusanList->isEmpty())
            <div style="padding: 4rem 1.5rem; text-align: center; color: var(--neutral-600);">
                <div style="width: 48px; height: 48px; margin: 0 auto 1rem; border-radius: 50%; background: var(--neutral-100); display: flex; align-items: center; justify-content: center; color: var(--neutral-500);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                </div>
                <p style="font-size: 1rem; font-weight: 600; color: var(--neutral-800); margin: 0 0 0.25rem 0;">Belum ada master data jurusan.</p>
                <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0;">Klik tombol <strong>Tambah Jurusan Baru</strong> untuk menambahkan prodi baru.</p>
            </div>
        @else
            <div class="table-responsive-gentelella" style="overflow-x: auto;">
                <table class="table jambo_table bulk_action" style="width: 100%; border-collapse: collapse; font-size: 0.875rem; text-align: left;">
                    <thead>
                        <tr class="headings">
                            <th>Nama Jurusan</th>
                            <th style="width: 240px;">Kategori Pendidikan</th>
                            <th style="width: 130px; text-align: center;">Status</th>
                            <th style="text-align: right; width: 200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jurusanList as $jurusan)
                            <tr class="sa-table-row" style="border-bottom: 1px solid var(--neutral-200);">
                                <td style="padding: 1rem 1.25rem;">
                                    <div style="font-weight: 700; color: var(--neutral-900); font-size: 0.9375rem;">
                                        {{ $jurusan->nama_jurusan }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--neutral-500); margin-top: 0.2rem;">
                                        ID #{{ $jurusan->id }}
                                    </div>
                                </td>
                                <td style="padding: 1rem 1rem; white-space: nowrap;">
                                    @if($jurusan->kategori === 'smk')
                                        <span class="sa-badge-purple">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                                            SMK / Vokasi
                                        </span>
                                    @else
                                        <span class="sa-badge-blue">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
                                            Perguruan Tinggi (D3/D4/S1)
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 1rem 1rem; text-align: center; white-space: nowrap;">
                                    @if($jurusan->aktif)
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
                                        <button type="button" onclick="openModalEditJurusan('{{ $jurusan->id }}', '{{ addslashes($jurusan->nama_jurusan) }}', '{{ $jurusan->kategori }}')" class="sa-btn-action-outline">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                            Edit
                                        </button>

                                        <form action="{{ route('superadmin.jurusan.toggle', $jurusan) }}" method="POST" style="display: inline-block; margin: 0;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" onclick="return confirm('{{ $jurusan->aktif ? 'Nonaktifkan jurusan ini? Pastikan tidak ada kuota dan penempatan aktif.' : 'Aktifkan kembali jurusan ini?' }}')" class="{{ $jurusan->aktif ? 'sa-btn-action-danger' : 'sa-btn-action-outline' }}" style="{{ $jurusan->aktif ? '' : 'color: var(--green-700); border-color: rgba(22, 163, 74, 0.25);' }}">
                                                {{ $jurusan->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($jurusanList->hasPages())
                <div style="padding: 1.25rem 1.5rem; border-top: 1px solid var(--neutral-200); background: var(--neutral-100);">
                    {{ $jurusanList->links() }}
                </div>
            @endif
        @endif
    </x-gentelella.x-panel>
</div>

<!-- =====================================================================
     MODAL TAMBAH JURUSAN
     ===================================================================== -->
<div id="modalTambahJurusan" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
    <div style="background: var(--neutral-0); border-radius: 16px; width: 100%; max-width: 480px; padding: 2rem; box-shadow: var(--shadow-modal); position: relative; border: 1px solid var(--neutral-400);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.875rem;">
            <div style="display: flex; align-items: center; gap: 0.625rem;">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: var(--blue-tint); color: var(--blue-700); display: flex; align-items: center; justify-content: center;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                </div>
                <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Tambah Jurusan Baru</h3>
            </div>
            <button type="button" onclick="document.getElementById('modalTambahJurusan').style.display='none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--neutral-500); line-height: 1; padding: 0.25rem;">&times;</button>
        </div>

        <form action="{{ route('superadmin.jurusan.store') }}" method="POST" style="display: flex; flex-direction: column; gap: 1.25rem;">
            @csrf
            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Nama Jurusan <span style="color: var(--red-600);">*</span>
                </label>
                <input type="text" name="nama_jurusan" required placeholder="Contoh: Rekayasa Perangkat Lunak" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
            </div>

            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Kategori Pendidikan <span style="color: var(--red-600);">*</span>
                </label>
                <select name="kategori" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; background: var(--neutral-0); font-family: inherit;">
                    <option value="smk">SMK / Sekolah Menengah Kejuruan</option>
                    <option value="mahasiswa">Perguruan Tinggi (D3/D4/S1)</option>
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem; border-top: 1px solid var(--neutral-200); padding-top: 1rem;">
                <button type="button" onclick="document.getElementById('modalTambahJurusan').style.display='none'" style="height: 40px; padding: 0 1.25rem; background: var(--neutral-100); border: 1px solid var(--neutral-400); border-radius: 8px; font-weight: 700; font-size: 0.875rem; color: var(--neutral-700); cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="height: 40px; padding: 0 1.5rem; background: var(--blue-600); color: #FFFFFF; border: none; border-radius: 8px; font-weight: 700; font-size: 0.875rem; cursor: pointer; box-shadow: var(--shadow-subtle);">
                    Simpan Jurusan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- =====================================================================
     MODAL EDIT JURUSAN
     ===================================================================== -->
<div id="modalEditJurusan" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
    <div style="background: var(--neutral-0); border-radius: 16px; width: 100%; max-width: 480px; padding: 2rem; box-shadow: var(--shadow-modal); position: relative; border: 1px solid var(--neutral-400);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.875rem;">
            <div style="display: flex; align-items: center; gap: 0.625rem;">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: var(--blue-tint); color: var(--blue-700); display: flex; align-items: center; justify-content: center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                </div>
                <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Edit Master Jurusan</h3>
            </div>
            <button type="button" onclick="document.getElementById('modalEditJurusan').style.display='none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--neutral-500); line-height: 1; padding: 0.25rem;">&times;</button>
        </div>

        <form id="formEditJurusan" method="POST" style="display: flex; flex-direction: column; gap: 1.25rem;">
            @csrf
            @method('PUT')
            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Nama Jurusan <span style="color: var(--red-600);">*</span>
                </label>
                <input type="text" id="editNamaJurusan" name="nama_jurusan" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
            </div>

            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Kategori Pendidikan <span style="color: var(--red-600);">*</span>
                </label>
                <select id="editKategoriJurusan" name="kategori" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; background: var(--neutral-0); font-family: inherit;">
                    <option value="smk">SMK / Sekolah Menengah Kejuruan</option>
                    <option value="mahasiswa">Perguruan Tinggi (D3/D4/S1)</option>
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem; border-top: 1px solid var(--neutral-200); padding-top: 1rem;">
                <button type="button" onclick="document.getElementById('modalEditJurusan').style.display='none'" style="height: 40px; padding: 0 1.25rem; background: var(--neutral-100); border: 1px solid var(--neutral-400); border-radius: 8px; font-weight: 700; font-size: 0.875rem; color: var(--neutral-700); cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="height: 40px; padding: 0 1.5rem; background: var(--blue-600); color: #FFFFFF; border: none; border-radius: 8px; font-weight: 700; font-size: 0.875rem; cursor: pointer; box-shadow: var(--shadow-subtle);">
                    Perbarui Jurusan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModalEditJurusan(id, nama, kategori) {
        document.getElementById('editNamaJurusan').value = nama;
        document.getElementById('editKategoriJurusan').value = kategori;
        document.getElementById('formEditJurusan').action = '/superadmin/jurusan/' + id;
        document.getElementById('modalEditJurusan').style.display = 'flex';
    }
</script>
@endsection
