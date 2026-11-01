@extends('layouts.dashboard')

@section('title', 'Hak Akses dan Role - SuperAdmin MagKom')
@section('page_title', 'Hak Akses dan Role Pengguna')

@section('breadcrumbs')
    <a href="{{ route('superadmin.dashboard') }}">SuperAdmin</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Role & Hak Akses</span>
@endsection

@push('styles')
<style>
    /* Dark Mode Overrides for SuperAdmin Roles & Permissions (DS-MagKom Industrial Standard) */
    [data-theme="dark"] div[style*="background: var(--neutral-0)"] {
        background-color: #003566 !important;
        border-color: #3F51B5 !important;
    }

    [data-theme="dark"] div[style*="background: var(--blue-tint)"] {
        background-color: rgba(59, 130, 246, 0.18) !important;
        border-color: rgba(147, 197, 253, 0.35) !important;
    }

    [data-theme="dark"] div[style*="border: 1px solid var(--neutral-400)"],
    [data-theme="dark"] div[style*="border-bottom: 1px solid var(--neutral-400)"] {
        border-color: rgba(63, 81, 181, 0.45) !important;
    }

    [data-theme="dark"] span[style*="color: var(--neutral-900)"],
    [data-theme="dark"] div[style*="color: var(--neutral-900)"],
    [data-theme="dark"] span[style*="color: var(--neutral-800)"] {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] span[style*="color: var(--neutral-600)"],
    [data-theme="dark"] div[style*="color: var(--neutral-600)"],
    [data-theme="dark"] p[style*="color: var(--neutral-700)"] {
        color: #94A3B8 !important;
    }
</style>
@endpush

@section('content')
<div class="sa-fluid-container" style="display: flex; flex-direction: column; gap: 1.5rem;">

    <!-- 1. KARTU RINGKASAN 4 ROLE UTAMA -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;">
        <!-- Card 1: SuperAdmin -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle); border-top: 4px solid #7E22CE;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #7E22CE; background: #F3E8FF; padding: 0.2rem 0.5rem; border-radius: 4px;">SuperAdmin</span>
                <span style="font-size: 1.5rem; font-weight: 800; color: var(--neutral-900);">{{ $roleCounts['superadmin'] ?? 0 }}</span>
            </div>
            <span style="font-weight: 700; font-size: 0.875rem; color: var(--neutral-800); display: block;">Tata Kelola Sistem & Eksekutif</span>
        </div>

        <!-- Card 2: Admin Dinas -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle); border-top: 4px solid var(--blue-600);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--blue-700); background: var(--blue-tint); padding: 0.2rem 0.5rem; border-radius: 4px;">Administrasi</span>
                <span style="font-size: 1.5rem; font-weight: 800; color: var(--neutral-900);">{{ $roleCounts['admin'] ?? 0 }}</span>
            </div>
            <span style="font-weight: 700; font-size: 0.875rem; color: var(--neutral-800); display: block;">Operasional Seleksi & Kuota</span>
        </div>

        <!-- Card 3: Pembimbing Lapangan -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle); border-top: 4px solid var(--amber-600, #D97706);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--amber-700); background: var(--amber-100); padding: 0.2rem 0.5rem; border-radius: 4px;">Pembimbing</span>
                <span style="font-size: 1.5rem; font-weight: 800; color: var(--neutral-900);">{{ $roleCounts['pembimbing'] ?? 0 }}</span>
            </div>
            <span style="font-weight: 700; font-size: 0.875rem; color: var(--neutral-800); display: block;">Mentoring & Evaluasi Kinerja</span>
        </div>

        <!-- Card 4: Peserta Magang -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle); border-top: 4px solid var(--green-600);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--green-700); background: var(--green-100); padding: 0.2rem 0.5rem; border-radius: 4px;">Peserta Magang</span>
                <span style="font-size: 1.5rem; font-weight: 800; color: var(--neutral-900);">{{ $roleCounts['peserta'] ?? 0 }}</span>
            </div>
            <span style="font-weight: 700; font-size: 0.875rem; color: var(--neutral-800); display: block;">Pelaksanaan Program Magang</span>
        </div>
    </div>

    <!-- 2. TABEL MATRIKS HAK AKSES MENYELURUH -->
    <x-gentelella.x-panel class="has-table" title="Matriks Wewenang & Hak Akses Modul">
        <x-slot:toolbox>
            <a href="{{ route('superadmin.staf.index') }}" style="font-size: 0.8125rem; font-weight: 700; color: var(--blue-600); text-decoration: none;">
                Kelola Akun Staf &rarr;
            </a>
        </x-slot:toolbox>

        <div class="table-responsive-gentelella" style="overflow-x: auto;">
            <table class="table jambo_table bulk_action" style="width: 100%; border-collapse: collapse; font-size: 0.8125rem; text-align: left;">
                <thead>
                    <tr class="headings">
                        <th>Modul & Cakupan Fitur</th>
                        <th style="text-align: center; width: 130px;">SuperAdmin</th>
                        <th style="text-align: center; width: 130px;">Administrasi</th>
                        <th style="text-align: center; width: 130px;">Pembimbing</th>
                        <th style="text-align: center; width: 130px;">Peserta</th>
                    </tr>
                </thead>
                <tbody style="divide-y: 1px solid var(--neutral-300);">
                    @foreach($matriksAkses as $akses)
                        <tr class="sa-table-row" style="border-bottom: 1px solid var(--neutral-300);">
                            <td style="padding: 0.875rem 1.25rem;">
                                <div style="font-weight: 700; color: var(--neutral-900); font-size: 0.875rem;">{{ $akses['modul'] }}</div>
                            </td>

                            <!-- SuperAdmin -->
                            <td style="padding: 0.875rem 1rem; text-align: center;">
                                @if($akses['superadmin'])
                                    <span class="sa-check-icon" style="background: rgba(168, 85, 247, 0.18); color: #A855F7;">✓</span>
                                @else
                                    <span style="color: var(--neutral-400); font-weight: 700;">—</span>
                                @endif
                            </td>

                            <!-- Admin -->
                            <td style="padding: 0.875rem 1rem; text-align: center;">
                                @if($akses['admin'])
                                    <span class="sa-check-icon sa-check-blue">✓</span>
                                @else
                                    <span style="color: var(--neutral-400); font-weight: 700;">—</span>
                                @endif
                            </td>

                            <!-- Pembimbing -->
                            <td style="padding: 0.875rem 1rem; text-align: center;">
                                @if($akses['pembimbing'])
                                    <span class="sa-check-icon" style="background: rgba(245, 158, 11, 0.18); color: #EAB308;">✓</span>
                                @else
                                    <span style="color: var(--neutral-400); font-weight: 700;">—</span>
                                @endif
                            </td>

                            <!-- Peserta -->
                            <td style="padding: 0.875rem 1rem; text-align: center;">
                                @if($akses['peserta'])
                                    <span class="sa-check-icon" style="background: rgba(34, 197, 94, 0.18); color: #22C55E;">✓</span>
                                @else
                                    <span style="color: var(--neutral-400); font-weight: 700;">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-gentelella.x-panel>

</div>
@endsection
