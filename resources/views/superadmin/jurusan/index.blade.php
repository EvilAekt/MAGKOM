@extends('layouts.dashboard')

@section('title', 'Master Jurusan - SuperAdmin MagKom')
@section('page_title', 'Master Data Jurusan')
@section('page_description', 'Kelola daftar jurusan program pendidikan (SMK dan Perguruan Tinggi) untuk alokasi kuota magang.')

@section('breadcrumbs')
    <span class="breadcrumb-current">Master Jurusan</span>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <!-- Info Banner & Create Button -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1rem; font-weight: 700; color: var(--neutral-900); margin: 0 0 0.25rem 0;">Aturan Penonaktifan Jurusan (BR-12)</h2>
            <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0;">
                Jurusan tidak dapat dinonaktifkan jika masih terikat dengan kuota aktif atau memiliki peserta magang aktif.
            </p>
        </div>

        <button onclick="document.getElementById('modalTambahJurusan').style.display='flex'" style="display: inline-flex; align-items: center; gap: 0.5rem; height: 40px; padding: 0 1.25rem; background: var(--blue-600); color: #FFFFFF; font-weight: 700; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer; transition: background 0.15s;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Tambah Jurusan Baru
        </button>
    </div>

    <!-- Data Table Card -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-subtle);">
        @if($jurusanList->isEmpty())
            <div style="padding: 4rem 1.5rem; text-align: center; color: var(--neutral-600);">
                <p style="font-size: 1rem; margin: 0;">Belum ada master data jurusan.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem; text-align: left;">
                    <thead>
                        <tr style="background: var(--neutral-100); border-bottom: 1px solid var(--neutral-400); color: var(--neutral-700); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <th style="padding: 1rem 1.25rem; font-weight: 700;">Nama Jurusan</th>
                            <th style="padding: 1rem 1rem; font-weight: 700;">Kategori Pendidikan</th>
                            <th style="padding: 1rem 1rem; font-weight: 700;">Status</th>
                            <th style="padding: 1rem 1.25rem; font-weight: 700; text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody style="divide-y: 1px solid var(--neutral-300);">
                        @foreach($jurusanList as $jurusan)
                            <tr style="border-bottom: 1px solid var(--neutral-300); transition: background 0.15s;" onmouseover="this.style.background='var(--neutral-100)'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 1rem 1.25rem; font-weight: 700; color: var(--neutral-900);">
                                    {{ $jurusan->nama_jurusan }}
                                </td>
                                <td style="padding: 1rem 1rem; white-space: nowrap;">
                                    @if($jurusan->kategori === 'smk')
                                        <span style="display: inline-block; padding: 0.25rem 0.625rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: var(--purple-50, #F3E8FF); color: var(--purple-800, #6B21A8);">
                                            SMK / Vokasi
                                        </span>
                                    @else
                                        <span style="display: inline-block; padding: 0.25rem 0.625rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: var(--blue-50); color: var(--blue-700);">
                                            Perguruan Tinggi (D3/D4/S1)
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 1rem 1rem; white-space: nowrap;">
                                    @if($jurusan->aktif)
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
                                        <button onclick="openModalEditJurusan('{{ $jurusan->id }}', '{{ addslashes($jurusan->nama_jurusan) }}', '{{ $jurusan->kategori }}')" style="padding: 0.375rem 0.75rem; background: var(--neutral-100); border: 1px solid var(--neutral-400); border-radius: 6px; font-size: 0.75rem; font-weight: 600; color: var(--neutral-800); cursor: pointer;">
                                            Edit
                                        </button>

                                        <form action="{{ route('superadmin.jurusan.toggle', $jurusan) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" onclick="return confirm('{{ $jurusan->aktif ? 'Nonaktifkan jurusan ini? Pastikan tidak ada kuota dan penempatan aktif.' : 'Aktifkan kembali jurusan ini?' }}')" style="padding: 0.375rem 0.75rem; background: {{ $jurusan->aktif ? 'var(--red-50)' : 'var(--green-50)' }}; border: 1px solid {{ $jurusan->aktif ? 'var(--red-200)' : 'var(--green-200)' }}; border-radius: 6px; font-size: 0.75rem; font-weight: 600; color: {{ $jurusan->aktif ? 'var(--red-700)' : 'var(--green-800)' }}; cursor: pointer;">
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
                <div style="padding: 1rem 1.25rem; border-top: 1px solid var(--neutral-300);">
                    {{ $jurusanList->links() }}
                </div>
            @endif
        @endif
    </div>
</div>

<!-- Modal Tambah Jurusan -->
<div id="modalTambahJurusan" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
    <div style="background: var(--neutral-0); border-radius: 16px; width: 100%; max-width: 440px; padding: 2rem; box-shadow: var(--shadow-card); position: relative;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-300); padding-bottom: 0.75rem;">
            <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Tambah Jurusan Baru</h3>
            <button onclick="document.getElementById('modalTambahJurusan').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: var(--neutral-600);">&times;</button>
        </div>

        <form action="{{ route('superadmin.jurusan.store') }}" method="POST" style="display: flex; flex-direction: column; gap: 1rem;">
            @csrf
            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">Nama Jurusan <span style="color: var(--red-600);">*</span></label>
                <input type="text" name="nama_jurusan" required placeholder="Contoh: Rekayasa Perangkat Lunak" style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
            </div>

            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">Kategori Pendidikan <span style="color: var(--red-600);">*</span></label>
                <select name="kategori" required style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem; background: var(--neutral-0);">
                    <option value="smk">SMK / Sekolah Menengah Kejuruan</option>
                    <option value="mahasiswa">Perguruan Tinggi (D3/D4/S1)</option>
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.75rem;">
                <button type="button" onclick="document.getElementById('modalTambahJurusan').style.display='none'" style="height: 40px; padding: 0 1.25rem; background: var(--neutral-200); border: none; border-radius: 6px; font-weight: 600; font-size: 0.875rem; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="height: 40px; padding: 0 1.5rem; background: var(--blue-600); color: #FFFFFF; border: none; border-radius: 6px; font-weight: 700; font-size: 0.875rem; cursor: pointer;">
                    Simpan Jurusan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Jurusan -->
<div id="modalEditJurusan" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
    <div style="background: var(--neutral-0); border-radius: 16px; width: 100%; max-width: 440px; padding: 2rem; box-shadow: var(--shadow-card); position: relative;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-300); padding-bottom: 0.75rem;">
            <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Edit Master Jurusan</h3>
            <button onclick="document.getElementById('modalEditJurusan').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: var(--neutral-600);">&times;</button>
        </div>

        <form id="formEditJurusan" method="POST" style="display: flex; flex-direction: column; gap: 1rem;">
            @csrf
            @method('PUT')
            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">Nama Jurusan <span style="color: var(--red-600);">*</span></label>
                <input type="text" id="editNamaJurusan" name="nama_jurusan" required style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
            </div>

            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">Kategori Pendidikan <span style="color: var(--red-600);">*</span></label>
                <select id="editKategoriJurusan" name="kategori" required style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem; background: var(--neutral-0);">
                    <option value="smk">SMK / Sekolah Menengah Kejuruan</option>
                    <option value="mahasiswa">Perguruan Tinggi (D3/D4/S1)</option>
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.75rem;">
                <button type="button" onclick="document.getElementById('modalEditJurusan').style.display='none'" style="height: 40px; padding: 0 1.25rem; background: var(--neutral-200); border: none; border-radius: 6px; font-weight: 600; font-size: 0.875rem; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="height: 40px; padding: 0 1.5rem; background: var(--blue-600); color: #FFFFFF; border: none; border-radius: 6px; font-weight: 700; font-size: 0.875rem; cursor: pointer;">
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
