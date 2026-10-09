@extends('layouts.dashboard')

@section('title', 'Master Rubrik Penilaian - SuperAdmin MagKom')
@section('page_title', 'Master Rubrik Penilaian Magang')
@section('page_description', 'Kelola kriteria evaluasi kompetensi magang dengan penegakan total bobot tepat 100% (AC-10-4).')

@section('breadcrumbs')
    <span class="breadcrumb-current">Master Rubrik</span>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <!-- Bobot Status Card & Create Button -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; align-items: center; gap: 1.25rem;">
            <div style="padding: 1rem 1.25rem; border-radius: 10px; background: {{ $totalBobotAktif === 100 ? 'var(--green-50)' : 'var(--red-50)' }}; border: 1px solid {{ $totalBobotAktif === 100 ? 'var(--green-200)' : 'var(--red-200)' }};">
                <div style="font-size: 0.75rem; font-weight: 700; color: {{ $totalBobotAktif === 100 ? 'var(--green-800)' : 'var(--red-700)' }}; text-transform: uppercase;">
                    Total Bobot Aktif
                </div>
                <div style="font-size: 1.75rem; font-weight: 900; color: {{ $totalBobotAktif === 100 ? 'var(--green-900)' : 'var(--red-900)' }};">
                    {{ $totalBobotAktif }}%
                </div>
            </div>
            <div>
                <h2 style="font-size: 1rem; font-weight: 700; color: var(--neutral-900); margin: 0 0 0.25rem 0;">
                    Aturan Validasi Integritas Rubrik (AC-10-4)
                </h2>
                <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0; max-width: 520px;">
                    Sistem mewajibkan total akumulasi bobot dari seluruh rubrik yang berstatus <strong>Aktif</strong> tepat bernilai <strong>100%</strong> agar instrumen penilaian sah digunakan.
                </p>
            </div>
        </div>

        <button onclick="document.getElementById('modalTambahRubrik').style.display='flex'" style="display: inline-flex; align-items: center; gap: 0.5rem; height: 40px; padding: 0 1.25rem; background: var(--blue-600); color: #FFFFFF; font-weight: 700; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer; transition: background 0.15s;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Tambah Kriteria Baru
        </button>
    </div>

    <!-- Data Table Card -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-subtle);">
        @if($rubrikList->isEmpty())
            <div style="padding: 4rem 1.5rem; text-align: center; color: var(--neutral-600);">
                <p style="font-size: 1rem; margin: 0;">Belum ada kriteria rubrik penilaian.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem; text-align: left;">
                    <thead>
                        <tr style="background: var(--neutral-100); border-bottom: 1px solid var(--neutral-400); color: var(--neutral-700); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <th style="padding: 1rem 1rem; text-align: center; width: 50px;">ID</th>
                            <th style="padding: 1rem 1.25rem; font-weight: 700;">Nama Kriteria Rubrik</th>
                            <th style="padding: 1rem 1rem; font-weight: 700; text-align: center; width: 140px;">Bobot Penilaian</th>
                            <th style="padding: 1rem 1rem; font-weight: 700; width: 120px;">Status</th>
                            <th style="padding: 1rem 1.25rem; font-weight: 700; text-align: right; width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody style="divide-y: 1px solid var(--neutral-300);">
                        @foreach($rubrikList as $rubrik)
                            <tr style="border-bottom: 1px solid var(--neutral-300); transition: background 0.15s;" onmouseover="this.style.background='var(--neutral-100)'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 1rem 1rem; text-align: center; font-weight: 600; color: var(--neutral-600);">
                                    {{ $rubrik->id }}
                                </td>
                                <td style="padding: 1rem 1.25rem;">
                                    <div style="font-weight: 700; color: var(--neutral-900); font-size: 0.9375rem;">
                                        {{ $rubrik->kriteria }}
                                    </div>
                                    @if($rubrik->deskripsi)
                                        <div style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.25rem;">
                                            {{ $rubrik->deskripsi }}
                                        </div>
                                    @endif
                                </td>
                                <td style="padding: 1rem 1rem; text-align: center;">
                                    <span style="font-size: 1.125rem; font-weight: 800; color: var(--blue-700);">
                                        {{ $rubrik->bobot }}%
                                    </span>
                                </td>
                                <td style="padding: 1rem 1rem; white-space: nowrap;">
                                    @if($rubrik->aktif)
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
                                        <button onclick="openModalEditRubrik('{{ $rubrik->id }}', '{{ addslashes($rubrik->kriteria) }}', '{{ $rubrik->bobot }}')" style="padding: 0.375rem 0.75rem; background: var(--neutral-100); border: 1px solid var(--neutral-400); border-radius: 6px; font-size: 0.75rem; font-weight: 600; color: var(--neutral-800); cursor: pointer;">
                                            Edit
                                        </button>

                                        <form action="{{ route('superadmin.rubrik.toggle', $rubrik) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" onclick="return confirm('{{ $rubrik->aktif ? 'Nonaktifkan kriteria ini? (Pastikan total bobot aktif tetap 100%)' : 'Aktifkan kriteria ini? (Pastikan total bobot aktif menjadi tepat 100%)' }}')" style="padding: 0.375rem 0.75rem; background: {{ $rubrik->aktif ? 'var(--red-50)' : 'var(--green-50)' }}; border: 1px solid {{ $rubrik->aktif ? 'var(--red-200)' : 'var(--green-200)' }}; border-radius: 6px; font-size: 0.75rem; font-weight: 600; color: {{ $rubrik->aktif ? 'var(--red-700)' : 'var(--green-800)' }}; cursor: pointer;">
                                                {{ $rubrik->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Modal Tambah Rubrik -->
<div id="modalTambahRubrik" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
    <div style="background: var(--neutral-0); border-radius: 16px; width: 100%; max-width: 440px; padding: 2rem; box-shadow: var(--shadow-card); position: relative;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-300); padding-bottom: 0.75rem;">
            <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Tambah Kriteria Rubrik</h3>
            <button onclick="document.getElementById('modalTambahRubrik').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: var(--neutral-600);">&times;</button>
        </div>

        <form action="{{ route('superadmin.rubrik.store') }}" method="POST" style="display: flex; flex-direction: column; gap: 1rem;">
            @csrf
            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">Nama Kriteria Rubrik <span style="color: var(--red-600);">*</span></label>
                <input type="text" name="kriteria" required placeholder="Contoh: Kedisiplinan & Etika Kerja" style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
            </div>

            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">Bobot Persentase (%) <span style="color: var(--red-600);">*</span></label>
                <input type="number" name="bobot" min="1" max="100" required placeholder="1 - 100" style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                <span style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.25rem; display: block;">Kriteria baru dibuat dalam status Nonaktif sampai bobot disesuaikan.</span>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.75rem;">
                <button type="button" onclick="document.getElementById('modalTambahRubrik').style.display='none'" style="height: 40px; padding: 0 1.25rem; background: var(--neutral-200); border: none; border-radius: 6px; font-weight: 600; font-size: 0.875rem; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="height: 40px; padding: 0 1.5rem; background: var(--blue-600); color: #FFFFFF; border: none; border-radius: 6px; font-weight: 700; font-size: 0.875rem; cursor: pointer;">
                    Simpan Rubrik
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Rubrik -->
<div id="modalEditRubrik" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
    <div style="background: var(--neutral-0); border-radius: 16px; width: 100%; max-width: 440px; padding: 2rem; box-shadow: var(--shadow-card); position: relative;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-300); padding-bottom: 0.75rem;">
            <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Edit Master Rubrik</h3>
            <button onclick="document.getElementById('modalEditRubrik').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: var(--neutral-600);">&times;</button>
        </div>

        <form id="formEditRubrik" method="POST" style="display: flex; flex-direction: column; gap: 1rem;">
            @csrf
            @method('PUT')
            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">Nama Kriteria Rubrik <span style="color: var(--red-600);">*</span></label>
                <input type="text" id="editKriteriaRubrik" name="kriteria" required style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
            </div>

            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">Bobot Persentase (%) <span style="color: var(--red-600);">*</span></label>
                <input type="number" id="editBobotRubrik" name="bobot" min="1" max="100" required style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.75rem;">
                <button type="button" onclick="document.getElementById('modalEditRubrik').style.display='none'" style="height: 40px; padding: 0 1.25rem; background: var(--neutral-200); border: none; border-radius: 6px; font-weight: 600; font-size: 0.875rem; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="height: 40px; padding: 0 1.5rem; background: var(--blue-600); color: #FFFFFF; border: none; border-radius: 6px; font-weight: 700; font-size: 0.875rem; cursor: pointer;">
                    Perbarui Rubrik
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModalEditRubrik(id, kriteria, bobot) {
        document.getElementById('editKriteriaRubrik').value = kriteria;
        document.getElementById('editBobotRubrik').value = bobot;
        document.getElementById('formEditRubrik').action = '/superadmin/rubrik/' + id;
        document.getElementById('modalEditRubrik').style.display = 'flex';
    }
</script>
@endsection
