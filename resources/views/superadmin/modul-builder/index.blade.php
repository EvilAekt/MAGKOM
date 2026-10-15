@extends('layouts.dashboard')

@section('title', 'Katalog Modul Kustom Dinamis - SuperAdmin MagKom')
@section('page_title', 'Pembuat Modul Kustom Dinamis (Low-Code Engine)')
@section('page_description', 'Rancang modul fitur baru apa pun secara mandiri tanpa coding dengan skema kolom fleksibel dan injeksi otomatis ke sidebar.')

@section('breadcrumbs')
    <a href="{{ route('superadmin.dashboard') }}">SuperAdmin</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Pembuat Modul</span>
@endsection

@section('page_actions')
    <a href="{{ route('superadmin.modul-builder.create') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; height: 42px; padding: 0 1.25rem; background: var(--blue-700); color: #FFFFFF; font-weight: 700; font-size: 0.875rem; border-radius: 8px; text-decoration: none; box-shadow: var(--shadow-subtle); transition: all 0.15s ease;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        <span>+ Buat Modul Baru</span>
    </a>
@endsection

@push('styles')
<style>
    .module-card {
        background: var(--neutral-0);
        border: 1px solid var(--neutral-300);
        border-radius: 14px;
        padding: 1.5rem;
        box-shadow: var(--shadow-subtle);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.2s ease;
    }

    .module-card:hover {
        border-color: var(--blue-400);
        box-shadow: 0 8px 24px rgba(7, 81, 255, 0.08);
        transform: translateY(-2px);
    }

    [data-theme="dark"] .module-card {
        background-color: #182234 !important;
        border-color: #26354D !important;
    }

    [data-theme="dark"] .module-card:hover {
        border-color: #3B82F6 !important;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4) !important;
    }

    [data-theme="dark"] div[style*="background: var(--neutral-0)"] {
        background-color: #182234 !important;
        border-color: #26354D !important;
    }

    [data-theme="dark"] div[style*="background: var(--neutral-100)"] {
        background-color: #111C2E !important;
        border-color: #26354D !important;
    }

    [data-theme="dark"] h1, [data-theme="dark"] h2, [data-theme="dark"] h3, [data-theme="dark"] strong {
        color: #F8FAFC !important;
    }
</style>
@endpush

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem; width: 100%;">

    {{-- FEEDBACK ALERTS --}}
    @if(session('status_keberhasilan'))
        <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); color: #065F46; padding: 1rem 1.25rem; border-radius: 10px; display: flex; align-items: center; gap: 0.75rem; font-size: 0.875rem; font-weight: 600;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            <span>{{ session('status_keberhasilan') }}</span>
        </div>
    @endif

    {{-- HIGHLIGHT BANNER LOW-CODE PLATFORM --}}
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-300); border-radius: 14px; padding: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; box-shadow: var(--shadow-subtle);">
        <div style="display: flex; align-items: center; gap: 1.25rem;">
            <div style="width: 50px; height: 50px; border-radius: 12px; background: rgba(7, 81, 255, 0.1); color: var(--blue-700); display: flex; align-items: center; justify-content: center;">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                    <polyline points="2 17 12 22 22 17"></polyline>
                    <polyline points="2 12 12 17 22 12"></polyline>
                </svg>
            </div>
            <div>
                <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--neutral-900); margin: 0;">
                    Portal MagKom Low-Code Architecture
                </h3>
                <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0.2rem 0 0 0;">
                    Setiap modul yang Anda ciptakan langsung memiliki tabel database tersendiri, form input modal otomatis, filter pencarian multi-kolom JSON, ekspor CSV, dan tautan navigasi di sidebar.
                </p>
            </div>
        </div>
        <div style="font-size: 0.8125rem; color: var(--neutral-600); font-weight: 700;">
            Total Modul Aktif: <span style="color: var(--blue-700); font-size: 1rem;">{{ $modules->where('status_aktif', true)->count() }}</span> dari {{ $modules->count() }} modul
        </div>
    </div>

    {{-- DAFTAR MODUL KUSTOM DALAM BENTUK GRID KARTU --}}
    @if($modules->isEmpty())
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-300); border-radius: 14px; padding: 3.5rem 1.5rem; text-align: center; box-shadow: var(--shadow-subtle);">
            <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(7, 81, 255, 0.1); color: var(--blue-700); margin: 0 auto 1.25rem auto; display: flex; align-items: center; justify-content: center;">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
            </div>
            <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0 0 0.5rem 0;">Belum Ada Modul Kustom Dibuat</h3>
            <p style="font-size: 0.875rem; color: var(--neutral-600); max-width: 480px; margin: 0 auto 1.5rem auto;">
                Tambahkan modul kustom pertama Anda (seperti Inventaris Perangkat, Log Kunjungan Tamu, atau Penugasan Khusus) hanya dalam beberapa klik.
            </p>
            <a href="{{ route('superadmin.modul-builder.create') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; height: 42px; padding: 0 1.5rem; background: var(--blue-700); color: #FFFFFF; font-weight: 700; font-size: 0.875rem; border-radius: 8px; text-decoration: none;">
                + Ciptakan Modul Baru Sekarang
            </a>
        </div>
    @else
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 1.5rem;">
            @foreach($modules as $m)
                <div class="module-card">
                    <div>
                        <!-- Header Kartu -->
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(7, 81, 255, 0.1); color: var(--blue-700); display: flex; align-items: center; justify-content: center; font-weight: 800;">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 style="font-size: 1.05rem; font-weight: 800; color: var(--neutral-900); margin: 0;">
                                        {{ $m->nama_modul }}
                                    </h4>
                                    <span style="font-size: 0.75rem; font-family: monospace; color: var(--neutral-500);">
                                        /superadmin/modul/{{ $m->slug }}
                                    </span>
                                </div>
                            </div>

                            <!-- Status Badge -->
                            <form action="{{ route('superadmin.modul-builder.toggle', $m) }}" method="POST" style="margin: 0;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" title="Klik untuk mengubah status aktif" style="background: none; border: none; cursor: pointer; padding: 0;">
                                    @if($m->status_aktif)
                                        <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.2rem 0.6rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 700; background: rgba(16, 185, 129, 0.15); color: #059669; border: 1px solid rgba(16, 185, 129, 0.3);">
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #059669;"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.2rem 0.6rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 700; background: rgba(239, 68, 68, 0.15); color: #DC2626; border: 1px solid rgba(239, 68, 68, 0.3);">
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #DC2626;"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                </button>
                            </form>
                        </div>

                        <!-- Deskripsi -->
                        <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0 0 1rem 0; min-height: 38px; line-height: 1.45;">
                            {{ $m->deskripsi ?: 'Tidak ada deskripsi modul.' }}
                        </p>

                        <!-- Telemetri Modul -->
                        <div style="background: var(--neutral-100); padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid var(--neutral-200); display: flex; justify-content: space-around; margin-bottom: 1.25rem;">
                            <div style="text-align: center;">
                                <div style="font-size: 0.6875rem; color: var(--neutral-500); text-transform: uppercase; font-weight: 700;">Kolom Schema</div>
                                <div style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900);">{{ $m->fields_count }} Field</div>
                            </div>
                            <div style="width: 1px; background: var(--neutral-300);"></div>
                            <div style="text-align: center;">
                                <div style="font-size: 0.6875rem; color: var(--neutral-500); text-transform: uppercase; font-weight: 700;">Data Tercatat</div>
                                <div style="font-size: 1.125rem; font-weight: 800; color: var(--blue-700);">{{ number_format($m->records_count) }} Baris</div>
                            </div>
                        </div>

                        <!-- Hak Akses Role -->
                        <div style="margin-bottom: 1.25rem; font-size: 0.75rem;">
                            <span style="color: var(--neutral-500); font-weight: 600;">Hak Akses Role:</span>
                            <div style="display: flex; gap: 0.35rem; flex-wrap: wrap; margin-top: 0.35rem;">
                                @foreach($m->target_roles ?? [] as $role)
                                    <span style="padding: 0.15rem 0.5rem; border-radius: 4px; background: var(--neutral-200); color: var(--neutral-800); font-weight: 600; font-size: 0.6875rem; text-transform: capitalize;">
                                        {{ $role }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div style="border-top: 1px solid var(--neutral-200); padding-top: 1rem; display: flex; justify-content: space-between; align-items: center; gap: 0.5rem;">
                        <a href="{{ route('superadmin.modul.index', ['slug' => $m->slug]) }}" style="height: 36px; padding: 0 1rem; background: var(--blue-700); color: #FFFFFF; font-weight: 700; font-size: 0.8125rem; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem; flex: 1; justify-content: center;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                            <span>Buka Modul</span>
                        </a>

                        <a href="{{ route('superadmin.modul-builder.edit', $m) }}" style="height: 36px; padding: 0 0.75rem; background: var(--neutral-0); border: 1px solid var(--neutral-300); color: var(--neutral-700); font-weight: 600; font-size: 0.8125rem; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem;" title="Edit Skema Kolom">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            Edit
                        </a>

                        <form action="{{ route('superadmin.modul-builder.destroy', $m) }}" method="POST" onsubmit="return confirm('PENTING: Anda yakin ingin menghapus modul {{ $m->nama_modul }}? Seluruh skema kolom dan {{ $m->records_count }} baris data di dalamnya akan terhapus permanen.');" style="margin: 0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="height: 36px; padding: 0 0.75rem; background: var(--neutral-0); border: 1px solid rgba(239, 68, 68, 0.3); color: #DC2626; font-weight: 600; font-size: 0.8125rem; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; gap: 0.25rem;" title="Hapus Modul">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
