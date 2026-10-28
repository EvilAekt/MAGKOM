@extends('layouts.dashboard')

@section('title', 'Master Rubrik Penilaian - SuperAdmin MagKom')
@section('page_title', 'Master Rubrik Penilaian Magang')
@section('page_description', 'Kelola kriteria evaluasi kompetensi magang dengan penegakan total bobot tepat 100% (AC-10-4).')

@section('breadcrumbs')
    <a href="{{ route('superadmin.dashboard') }}">SuperAdmin</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Master Rubrik</span>
@endsection

@section('page_actions')
    <button type="button" onclick="document.getElementById('modalTambahRubrik').style.display='flex'" style="display: inline-flex; align-items: center; gap: 0.5rem; height: 42px; padding: 0 1.25rem; background: var(--blue-600); color: #FFFFFF; font-weight: 700; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer; transition: all 0.15s ease; box-shadow: var(--shadow-subtle);">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        <span>Tambah Kriteria Baru</span>
    </button>
@endsection

@push('styles')
<style>
    /* Dark Mode Overrides for SuperAdmin Rubrik Penilaian (DS-MagKom Industrial Standard) */
    [data-theme="dark"] div[style*="background: var(--neutral-0)"] {
        background-color: #003566 !important;
        border-color: #3F51B5 !important;
    }

    [data-theme="dark"] div[style*="border: 1px solid var(--neutral-400)"],
    [data-theme="dark"] div[style*="border-bottom: 1px solid var(--neutral-400)"],
    [data-theme="dark"] div[style*="border-top: 1px solid var(--neutral-200)"],
    [data-theme="dark"] div[style*="border-bottom: 1px solid var(--neutral-200)"] {
        border-color: rgba(63, 81, 181, 0.45) !important;
    }

    [data-theme="dark"] span[style*="color: var(--neutral-900)"],
    [data-theme="dark"] div[style*="color: var(--neutral-900)"],
    [data-theme="dark"] h2[style*="color: var(--neutral-900)"],
    [data-theme="dark"] span[style*="color: var(--neutral-800)"],
    [data-theme="dark"] h3[style*="color: var(--neutral-800)"] {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] span[style*="color: var(--neutral-600)"],
    [data-theme="dark"] div[style*="color: var(--neutral-600)"],
    [data-theme="dark"] p[style*="color: var(--neutral-600)"] {
        color: #94A3B8 !important;
    }

    /* Modals Dark Mode */
    [data-theme="dark"] #modalTambahRubrik > div,
    [data-theme="dark"] #modalEditRubrik > div {
        background-color: #003566 !important;
        border-color: #3F51B5 !important;
        color: #F8FAFC !important;
    }

    [data-theme="dark"] #modalTambahRubrik h3,
    [data-theme="dark"] #modalEditRubrik h3 {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] input[type="text"],
    [data-theme="dark"] input[type="number"],
    [data-theme="dark"] textarea {
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

    <!-- 1. KARTU STATUS INTEGRITAS BOBOT 100% (AC-10-4) -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.25rem; border-left: 4px solid {{ $totalBobotAktif === 100 ? 'var(--green-600)' : 'var(--red-600)' }};">
        <div style="display: flex; align-items: center; gap: 1.25rem; flex-wrap: wrap;">
            <!-- Badge Angka Persentase -->
            <div style="padding: 0.875rem 1.25rem; border-radius: 10px; background: {{ $totalBobotAktif === 100 ? 'var(--green-100)' : 'var(--red-100)' }}; border: 1px solid {{ $totalBobotAktif === 100 ? 'var(--green-600)' : 'var(--red-300)' }}; display: flex; flex-direction: column; align-items: center; justify-content: center; min-width: 110px;">
                <span style="font-size: 0.6875rem; font-weight: 700; color: {{ $totalBobotAktif === 100 ? 'var(--green-700)' : 'var(--red-700)' }}; text-transform: uppercase; letter-spacing: 0.05em;">
                    Total Bobot Aktif
                </span>
                <span style="font-size: 1.875rem; font-weight: 800; color: {{ $totalBobotAktif === 100 ? 'var(--green-700)' : 'var(--red-700)' }}; line-height: 1.2;">
                    {{ $totalBobotAktif }}%
                </span>
            </div>

            <div style="max-width: 580px;">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                    <h2 style="font-size: 1rem; font-weight: 700; color: var(--neutral-900); margin: 0;">
                        Aturan Validasi Integritas Rubrik (AC-10-4)
                    </h2>
                    @if($totalBobotAktif === 100)
                        <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.15rem 0.5rem; border-radius: 9999px; background: var(--green-100); color: var(--green-700); font-size: 0.6875rem; font-weight: 700;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            Sah & Terpenuhi
                        </span>
                    @else
                        <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.15rem 0.5rem; border-radius: 9999px; background: var(--red-100); color: var(--red-700); font-size: 0.6875rem; font-weight: 700;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                            Belum 100% (Selisih {{ 100 - $totalBobotAktif }}%)
                        </span>
                    @endif
                </div>
                <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0; line-height: 1.5;">
                    Sistem mewajibkan total akumulasi bobot dari seluruh rubrik yang berstatus <strong>Aktif</strong> tepat bernilai <strong>100%</strong> agar instrumen penilaian sah digunakan oleh pembimbing lapangan saat evaluasi nilai akhir.
                </p>
            </div>
        </div>

        <!-- Quick Summary Mini Progress Bar -->
        <div style="min-width: 200px; flex: 1; max-width: 260px;">
            <div style="display: flex; justify-content: space-between; font-size: 0.75rem; font-weight: 600; color: var(--neutral-700); margin-bottom: 0.375rem;">
                <span>Progres Akumulasi</span>
                <span>{{ (string) min(100, (int) $totalBobotAktif) }} / 100%</span>
            </div>
            <div style="width: 100%; height: 8px; background: var(--neutral-200); border-radius: 9999px; overflow: hidden;">
                <div style="width: {{ (string) min(100, (int) $totalBobotAktif) }}%; height: 100%; background: {{ $totalBobotAktif === 100 ? 'var(--green-600)' : 'var(--red-600)' }}; transition: width 0.3s ease;"></div>
            </div>
        </div>
    </div>

    <!-- 2. DATA TABLE CARD -->
    <x-gentelella.x-panel class="has-table" title="Daftar Kriteria Rubrik Penilaian" subtitle="Komponen penilaian kompetensi dan etika kerja peserta magang.">
        <x-slot:toolbox>
            <span style="font-size: 0.8125rem; color: var(--neutral-600); font-weight: 600;">
                Total Kriteria: <strong>{{ (string) $rubrikList->count() }}</strong>
            </span>
        </x-slot:toolbox>

        @if($rubrikList->isEmpty())
            <div style="padding: 4rem 1.5rem; text-align: center; color: var(--neutral-600);">
                <div style="width: 48px; height: 48px; margin: 0 auto 1rem; border-radius: 50%; background: var(--neutral-100); display: flex; align-items: center; justify-content: center; color: var(--neutral-500);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><line x1="9" y1="15" x2="15" y2="15"></line></svg>
                </div>
                <p style="font-size: 1rem; font-weight: 600; color: var(--neutral-800); margin: 0 0 0.25rem 0;">Belum ada kriteria rubrik penilaian.</p>
                <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0;">Klik tombol <strong>Tambah Kriteria Baru</strong> untuk membuat instrumen evaluasi.</p>
            </div>
        @else
            <div class="table-responsive-gentelella" style="overflow-x: auto;">
                <table class="table jambo_table bulk_action" style="width: 100%; border-collapse: collapse; font-size: 0.875rem; text-align: left;">
                    <thead>
                        <tr class="headings">
                            <th style="text-align: center; width: 60px;">ID</th>
                            <th>Nama Kriteria Rubrik</th>
                            <th style="text-align: center; width: 150px;">Bobot Penilaian</th>
                            <th style="width: 130px; text-align: center;">Status</th>
                            <th style="text-align: right; width: 200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rubrikList as $rubrik)
                            <tr class="sa-table-row" style="border-bottom: 1px solid var(--neutral-200);">
                                <td style="padding: 1rem 1rem; text-align: center; font-weight: 600; color: var(--neutral-500); font-size: 0.8125rem;">
                                    #{{ $rubrik->id }}
                                </td>
                                <td style="padding: 1rem 1.25rem;">
                                    <div style="font-weight: 700; color: var(--neutral-900); font-size: 0.9375rem;">
                                        {{ $rubrik->kriteria }}
                                    </div>
                                    @if($rubrik->deskripsi)
                                        <div style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.25rem; line-height: 1.4;">
                                            {{ $rubrik->deskripsi }}
                                        </div>
                                    @endif
                                </td>
                                <td style="padding: 1rem 1rem; text-align: center;">
                                    <span class="sa-badge-metric" style="display: inline-flex; align-items: baseline; justify-content: center; font-size: 1.125rem;">
                                        {{ $rubrik->bobot }}<span style="font-size: 0.75rem; font-weight: 700; margin-left: 2px;">%</span>
                                    </span>
                                </td>
                                <td style="padding: 1rem 1rem; text-align: center; white-space: nowrap;">
                                    @if($rubrik->aktif)
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
                                    <div style="display: inline-flex; gap: 0.5rem; align-items: center; justify-content: flex-end;">
                                        <button type="button" onclick="openModalEditRubrik('{{ $rubrik->id }}', '{{ addslashes($rubrik->kriteria) }}', '{{ $rubrik->bobot }}')" class="sa-btn-action-outline">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                            Edit
                                        </button>

                                        <form action="{{ route('superadmin.rubrik.toggle', $rubrik) }}" method="POST" style="display: inline-block; margin: 0;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" onclick="return confirm('{{ $rubrik->aktif ? 'Nonaktifkan kriteria ini? (Pastikan total bobot aktif tetap 100%)' : 'Aktifkan kriteria ini? (Pastikan total bobot aktif menjadi tepat 100%)' }}')" class="{{ $rubrik->aktif ? 'sa-btn-action-danger' : 'sa-btn-action-outline' }}" style="{{ $rubrik->aktif ? '' : 'color: var(--green-700); border-color: rgba(22, 163, 74, 0.25);' }}">
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
    </x-gentelella.x-panel>
</div>

<!-- =====================================================================
     MODAL TAMBAH RUBRIK (Backdrop Blur, Sleman Elevation)
     ===================================================================== -->
<div id="modalTambahRubrik" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
    <div style="background: var(--neutral-0); border-radius: 16px; width: 100%; max-width: 480px; padding: 2rem; box-shadow: var(--shadow-modal); position: relative; border: 1px solid var(--neutral-400);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.875rem;">
            <div style="display: flex; align-items: center; gap: 0.625rem;">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: var(--blue-tint); color: var(--blue-700); display: flex; align-items: center; justify-content: center;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                </div>
                <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Tambah Kriteria Rubrik</h3>
            </div>
            <button type="button" onclick="document.getElementById('modalTambahRubrik').style.display='none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--neutral-500); line-height: 1; padding: 0.25rem;">&times;</button>
        </div>

        <form action="{{ route('superadmin.rubrik.store') }}" method="POST" style="display: flex; flex-direction: column; gap: 1.25rem;">
            @csrf
            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Nama Kriteria Rubrik <span style="color: var(--red-600);">*</span>
                </label>
                <input type="text" name="kriteria" required placeholder="Contoh: Kedisiplinan & Etika Kerja" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
            </div>

            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Bobot Persentase (%) <span style="color: var(--red-600);">*</span>
                </label>
                <div style="position: relative;">
                    <input type="number" name="bobot" min="1" max="100" required placeholder="1 - 100" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 2.5rem 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                    <span style="position: absolute; right: 0.875rem; top: 50%; transform: translateY(-50%); font-weight: 700; color: var(--neutral-500); font-size: 0.875rem;">%</span>
                </div>
                <span style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.375rem; display: block; line-height: 1.4;">
                    Kriteria baru dibuat dalam status <strong>Nonaktif</strong> sampai konfigurasi bobot disesuaikan menjadi tepat 100%.
                </span>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem; border-top: 1px solid var(--neutral-200); padding-top: 1rem;">
                <button type="button" onclick="document.getElementById('modalTambahRubrik').style.display='none'" style="height: 40px; padding: 0 1.25rem; background: var(--neutral-100); border: 1px solid var(--neutral-400); border-radius: 8px; font-weight: 700; font-size: 0.875rem; color: var(--neutral-700); cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="height: 40px; padding: 0 1.5rem; background: var(--blue-600); color: #FFFFFF; border: none; border-radius: 8px; font-weight: 700; font-size: 0.875rem; cursor: pointer; box-shadow: var(--shadow-subtle);">
                    Simpan Rubrik
                </button>
            </div>
        </form>
    </div>
</div>

<!-- =====================================================================
     MODAL EDIT RUBRIK
     ===================================================================== -->
<div id="modalEditRubrik" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
    <div style="background: var(--neutral-0); border-radius: 16px; width: 100%; max-width: 480px; padding: 2rem; box-shadow: var(--shadow-modal); position: relative; border: 1px solid var(--neutral-400);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.875rem;">
            <div style="display: flex; align-items: center; gap: 0.625rem;">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: var(--blue-tint); color: var(--blue-700); display: flex; align-items: center; justify-content: center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                </div>
                <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Edit Master Rubrik</h3>
            </div>
            <button type="button" onclick="document.getElementById('modalEditRubrik').style.display='none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--neutral-500); line-height: 1; padding: 0.25rem;">&times;</button>
        </div>

        <form id="formEditRubrik" method="POST" style="display: flex; flex-direction: column; gap: 1.25rem;">
            @csrf
            @method('PUT')
            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Nama Kriteria Rubrik <span style="color: var(--red-600);">*</span>
                </label>
                <input type="text" id="editKriteriaRubrik" name="kriteria" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
            </div>

            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Bobot Persentase (%) <span style="color: var(--red-600);">*</span>
                </label>
                <div style="position: relative;">
                    <input type="number" id="editBobotRubrik" name="bobot" min="1" max="100" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 2.5rem 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                    <span style="position: absolute; right: 0.875rem; top: 50%; transform: translateY(-50%); font-weight: 700; color: var(--neutral-500); font-size: 0.875rem;">%</span>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem; border-top: 1px solid var(--neutral-200); padding-top: 1rem;">
                <button type="button" onclick="document.getElementById('modalEditRubrik').style.display='none'" style="height: 40px; padding: 0 1.25rem; background: var(--neutral-100); border: 1px solid var(--neutral-400); border-radius: 8px; font-weight: 700; font-size: 0.875rem; color: var(--neutral-700); cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="height: 40px; padding: 0 1.5rem; background: var(--blue-600); color: #FFFFFF; border: none; border-radius: 8px; font-weight: 700; font-size: 0.875rem; cursor: pointer; box-shadow: var(--shadow-subtle);">
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
