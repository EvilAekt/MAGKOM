@extends('layouts.dashboard')

@section('title', 'Hak Akses dan Role - SuperAdmin MagKom')
@section('page_title', 'Hak Akses dan Role Pengguna')
@section('page_description', 'Matriks wewenang, batasan fungsional, dan cakupan hak akses seluruh peran sistem MagKom (PRD Bab 3.1 & 3.3).')

@section('breadcrumbs')
    <a href="{{ route('superadmin.dashboard') }}">SuperAdmin</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Role & Hak Akses</span>
@endsection

@push('styles')
<style>
    /* Dark Mode Overrides for SuperAdmin Roles & Permissions */
    [data-theme="dark"] div[style*="background: var(--neutral-0)"] {
        background-color: #182234 !important;
        border-color: #26354D !important;
    }

    [data-theme="dark"] div[style*="background: var(--blue-tint)"] {
        background-color: rgba(37, 99, 235, 0.15) !important;
        border-color: rgba(59, 130, 246, 0.3) !important;
    }

    [data-theme="dark"] div[style*="border: 1px solid var(--neutral-400)"],
    [data-theme="dark"] div[style*="border-bottom: 1px solid var(--neutral-400)"] {
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
<div style="display: flex; flex-direction: column; gap: 1.5rem;">

    <!-- 1. ALERT INFORMASI PRINSIP HAK AKSES FIGMA (SA-02) -->
    <div style="background: var(--blue-tint); border: 1px solid var(--blue-tint-line); border-left: 4px solid var(--blue-600); border-radius: 12px; padding: 1.25rem 1.5rem; display: flex; align-items: flex-start; gap: 1rem;">
        <div style="width: 36px; height: 36px; border-radius: 8px; background: var(--blue-600); color: #FFFFFF; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
            </svg>
        </div>
        <div>
            <h3 style="font-size: 0.9375rem; font-weight: 700; color: var(--blue-700); margin: 0 0 0.25rem 0;">
                SuperAdmin Mewarisi Seluruh Hak Akses Tiga Peran Lain
            </h3>
            <p style="font-size: 0.8125rem; color: var(--neutral-700); margin: 0; line-height: 1.5;">
                Sebagai pemegang wewenang tata kelola teknis tertinggi dinas (SuperAdmin), Anda memiliki akses pengawasan dan kontrol mutlak terhadap modul Administrasi Dinas, Pembimbing Lapangan, dan Peserta Magang (PRD Bab 3.1 & 3.3).
            </p>
        </div>
    </div>

    <!-- 2. KARTU RINGKASAN 4 ROLE UTAMA -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;">
        <!-- Card 1: SuperAdmin -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle); border-top: 4px solid #7E22CE;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #7E22CE; background: #F3E8FF; padding: 0.2rem 0.5rem; border-radius: 4px;">SuperAdmin</span>
                <span style="font-size: 1.5rem; font-weight: 800; color: var(--neutral-900);">{{ $roleCounts['superadmin'] ?? 0 }}</span>
            </div>
            <span style="font-weight: 700; font-size: 0.875rem; color: var(--neutral-800); display: block;">Tata Kelola Sistem & Eksekutif</span>
            <span style="font-size: 0.75rem; color: var(--neutral-600); display: block; margin-top: 0.25rem;">Kontrol penuh akun staf, audit log, kuota kemitraan, dan konfigurasi master.</span>
        </div>

        <!-- Card 2: Admin Dinas -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle); border-top: 4px solid var(--blue-600);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--blue-700); background: var(--blue-tint); padding: 0.2rem 0.5rem; border-radius: 4px;">Administrasi</span>
                <span style="font-size: 1.5rem; font-weight: 800; color: var(--neutral-900);">{{ $roleCounts['admin'] ?? 0 }}</span>
            </div>
            <span style="font-weight: 700; font-size: 0.875rem; color: var(--neutral-800); display: block;">Operasional Seleksi & Kuota</span>
            <span style="font-size: 0.75rem; color: var(--neutral-600); display: block; margin-top: 0.25rem;">Verifikasi berkas pendaftar, alokasi kuota jurusan, penempatan, dan laporan dinas.</span>
        </div>

        <!-- Card 3: Pembimbing Lapangan -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle); border-top: 4px solid var(--amber-500);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--amber-700); background: var(--amber-100); padding: 0.2rem 0.5rem; border-radius: 4px;">Pembimbing</span>
                <span style="font-size: 1.5rem; font-weight: 800; color: var(--neutral-900);">{{ $roleCounts['pembimbing'] ?? 0 }}</span>
            </div>
            <span style="font-weight: 700; font-size: 0.875rem; color: var(--neutral-800); display: block;">Mentoring & Evaluasi Kinerja</span>
            <span style="font-size: 0.75rem; color: var(--neutral-600); display: block; margin-top: 0.25rem;">Pemberian tugas teknis, reviu logbook harian, dan penilaian rubrik akhir mentee.</span>
        </div>

        <!-- Card 4: Peserta Magang -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle); border-top: 4px solid var(--green-600);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--green-700); background: var(--green-100); padding: 0.2rem 0.5rem; border-radius: 4px;">Peserta Magang</span>
                <span style="font-size: 1.5rem; font-weight: 800; color: var(--neutral-900);">{{ $roleCounts['peserta'] ?? 0 }}</span>
            </div>
            <span style="font-weight: 700; font-size: 0.875rem; color: var(--neutral-800); display: block;">Pelaksanaan Program Magang</span>
            <span style="font-size: 0.75rem; color: var(--neutral-600); display: block; margin-top: 0.25rem;">Pendaftaran mandiri, pelaporan logbook, penyelesaian tugas, dan unduh sertifikat.</span>
        </div>
    </div>

    <!-- 3. TABEL MATRIKS HAK AKSES MENYELURUH (PRD Bab 3.1 & 3.3) -->
    <x-gentelella.x-panel title="Matriks Wewenang & Hak Akses Modul" subtitle="Standar pembagian otorisasi RBAC (Role-Based Access Control) MagKom.">
        <x-slot:toolbox>
            <a href="{{ route('superadmin.staf.index') }}" style="font-size: 0.8125rem; font-weight: 700; color: var(--blue-600); text-decoration: none;">
                Kelola Akun Staf &rarr;
            </a>
        </x-slot:toolbox>

        <div class="table-responsive-gentelella" style="overflow-x: auto;">
            <table class="table jambo_table bulk_action" style="width: 100%; border-collapse: collapse; font-size: 0.8125rem; text-align: left;">
                <thead>
                    <tr class="headings" style="background: var(--neutral-100); border-bottom: 1px solid var(--neutral-400); color: var(--neutral-700); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                        <th style="padding: 0.875rem 1.25rem; font-weight: 700;">Modul & Cakupan Fitur</th>
                        <th style="padding: 0.875rem 1rem; font-weight: 700; text-align: center; width: 130px;">SuperAdmin</th>
                        <th style="padding: 0.875rem 1rem; font-weight: 700; text-align: center; width: 130px;">Administrasi</th>
                        <th style="padding: 0.875rem 1rem; font-weight: 700; text-align: center; width: 130px;">Pembimbing</th>
                        <th style="padding: 0.875rem 1rem; font-weight: 700; text-align: center; width: 130px;">Peserta</th>
                    </tr>
                </thead>
                <tbody style="divide-y: 1px solid var(--neutral-300);">
                    @foreach($matriksAkses as $akses)
                        <tr style="border-bottom: 1px solid var(--neutral-300); transition: background 0.15s;" onmouseover="this.style.background='var(--neutral-100)'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 0.875rem 1.25rem;">
                                <div style="font-weight: 700; color: var(--neutral-900); font-size: 0.875rem;">{{ $akses['modul'] }}</div>
                                <div style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.125rem;">{{ $akses['deskripsi'] }}</div>
                            </td>

                            <!-- SuperAdmin -->
                            <td style="padding: 0.875rem 1rem; text-align: center;">
                                @if($akses['superadmin'])
                                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 50%; background: #F3E8FF; color: #7E22CE; font-weight: 800;">✓</span>
                                @else
                                    <span style="color: var(--neutral-400); font-weight: 700;">—</span>
                                @endif
                            </td>

                            <!-- Admin -->
                            <td style="padding: 0.875rem 1rem; text-align: center;">
                                @if($akses['admin'])
                                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 50%; background: var(--blue-tint); color: var(--blue-700); font-weight: 800;">✓</span>
                                @else
                                    <span style="color: var(--neutral-400); font-weight: 700;">—</span>
                                @endif
                            </td>

                            <!-- Pembimbing -->
                            <td style="padding: 0.875rem 1rem; text-align: center;">
                                @if($akses['pembimbing'])
                                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 50%; background: var(--amber-100); color: var(--amber-700); font-weight: 800;">✓</span>
                                @else
                                    <span style="color: var(--neutral-400); font-weight: 700;">—</span>
                                @endif
                            </td>

                            <!-- Peserta -->
                            <td style="padding: 0.875rem 1rem; text-align: center;">
                                @if($akses['peserta'])
                                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 50%; background: var(--green-100); color: var(--green-700); font-weight: 800;">✓</span>
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
