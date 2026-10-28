@extends('layouts.dashboard')

@section('title', 'Data Modul: ' . $modul->nama_modul . ' - SuperAdmin MagKom')
@section('page_title', $modul->nama_modul)
@section('page_description', $modul->deskripsi ?: 'Modul kustom dinamis untuk pengelolaan data dan operasional mandiri MagKom.')

@section('breadcrumbs')
    <a href="{{ route('superadmin.dashboard') }}">SuperAdmin</a>
    <span class="breadcrumb-separator">/</span>
    <a href="{{ route('superadmin.modul-builder.index') }}">Pembuat Modul</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">{{ $modul->nama_modul }}</span>
@endsection

@section('page_actions')
    <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
        <a href="{{ route('superadmin.modul.export', ['slug' => $modul->slug]) }}" style="height: 38px; padding: 0 1rem; background: var(--green-700); color: #FFFFFF; font-weight: 700; font-size: 0.8125rem; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem; box-shadow: var(--shadow-subtle);">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            <span>Ekspor CSV</span>
        </a>

        <a href="{{ route('superadmin.modul.export-pdf', ['slug' => $modul->slug]) }}" target="_blank" style="height: 38px; padding: 0 1rem; background: #DC2626; color: #FFFFFF; font-weight: 700; font-size: 0.8125rem; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem; box-shadow: var(--shadow-subtle);">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            <span>Cetak PDF</span>
        </a>

        <a href="{{ route('superadmin.modul.print', ['slug' => $modul->slug]) }}" target="_blank" style="height: 38px; padding: 0 1rem; background: #475569; color: #FFFFFF; font-weight: 700; font-size: 0.8125rem; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem; box-shadow: var(--shadow-subtle);">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            <span>Print</span>
        </a>

        <button type="button" onclick="openCreateModal()" style="height: 38px; padding: 0 1.25rem; background: var(--blue-700); color: #FFFFFF; font-weight: 700; font-size: 0.8125rem; border: none; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem; box-shadow: var(--shadow-subtle);">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            <span>+ Tambah Data</span>
        </button>
    </div>
@endsection

@push('styles')
<style>
    [data-theme="dark"] div[style*="background: var(--neutral-0)"] {
        background-color: #182234 !important;
        border-color: #26354D !important;
    }

    [data-theme="dark"] div[style*="background: var(--neutral-100)"] {
        background-color: #111C2E !important;
        border-color: #26354D !important;
    }

    [data-theme="dark"] input[type="text"],
    [data-theme="dark"] input[type="number"],
    [data-theme="dark"] input[type="date"],
    [data-theme="dark"] input[type="datetime-local"],
    [data-theme="dark"] textarea,
    [data-theme="dark"] select {
        background-color: #0F172A !important;
        color: #F8FAFC !important;
        border-color: #26354D !important;
        color-scheme: dark;
    }

    [data-theme="dark"] #modalDataForm > div {
        background-color: #182234 !important;
        border-color: #26354D !important;
        color: #F8FAFC !important;
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

    @if ($errors->any())
        <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #DC2626; padding: 1rem 1.25rem; border-radius: 10px; font-size: 0.875rem;">
            <strong style="display: block; margin-bottom: 0.35rem;">Gagal menyimpan data:</strong>
            <ul style="margin: 0; padding-left: 1.25rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- HEADER STATUS BADGES --}}
    <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
        @if($modul->tipe_sumber === 'physical_table')
            <span style="font-size: 0.75rem; background: rgba(16, 185, 129, 0.1); color: #059669; border: 1px solid rgba(16, 185, 129, 0.25); padding: 0.25rem 0.65rem; border-radius: 9999px; font-weight: 700;">
                Tabel Fisik: {{ $modul->nama_tabel_fisik }} (PK: {{ $modul->primary_key }})
            </span>
        @else
            <span style="font-size: 0.75rem; background: rgba(7, 81, 255, 0.1); color: var(--blue-700); border: 1px solid rgba(7, 81, 255, 0.25); padding: 0.25rem 0.65rem; border-radius: 9999px; font-weight: 700;">
                Skema Dinamis JSON
            </span>
        @endif

        @if($modul->is_public_read)
            <a href="{{ route('public.modul.index', ['slug' => $modul->slug]) }}" target="_blank" style="font-size: 0.75rem; background: rgba(168, 85, 247, 0.1); color: #9333EA; border: 1px solid rgba(168, 85, 247, 0.25); padding: 0.25rem 0.65rem; border-radius: 9999px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem;">
                <span>🌐 Akses Publik Aktif</span>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
            </a>
        @endif

        @if($modul->scope_mode === 'bidang_scope')
            <span style="font-size: 0.75rem; background: rgba(245, 158, 11, 0.1); color: #D97706; border: 1px solid rgba(245, 158, 11, 0.25); padding: 0.25rem 0.65rem; border-radius: 9999px; font-weight: 700;">
                🔒 Isolasi Bidang Dinas Aktif
            </span>
        @elseif($modul->scope_mode === 'creator_scope')
            <span style="font-size: 0.75rem; background: rgba(245, 158, 11, 0.1); color: #D97706; border: 1px solid rgba(245, 158, 11, 0.25); padding: 0.25rem 0.65rem; border-radius: 9999px; font-weight: 700;">
                🔒 Isolasi Pembuat Aktif
            </span>
        @endif
    </div>

    {{-- FILTER TOOLBAR & SEARCH --}}
    <x-gentelella.card style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <form method="GET" action="{{ route('superadmin.modul.index', ['slug' => $modul->slug]) }}" style="display: flex; gap: 0.75rem; flex: 1; max-width: 500px;">
            <div style="position: relative; width: 100%;">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari data dalam modul {{ $modul->nama_modul }}..." style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem 0 2.25rem; font-size: 0.8125rem; font-family: inherit;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position: absolute; left: 0.875rem; top: 12px; color: var(--neutral-500);"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </div>
            <button type="submit" style="height: 40px; padding: 0 1.25rem; background: var(--blue-700); color: #FFFFFF; font-weight: 700; font-size: 0.8125rem; border: none; border-radius: 8px; cursor: pointer;">
                Cari
            </button>
            @if(request('q'))
                <a href="{{ route('superadmin.modul.index', ['slug' => $modul->slug]) }}" style="height: 40px; padding: 0 0.875rem; background: var(--neutral-200); color: var(--neutral-700); font-size: 0.8125rem; font-weight: 600; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center;">Reset</a>
            @endif
        </form>

        <div style="font-size: 0.8125rem; color: var(--neutral-600);">
            Total Data: <strong>{{ $records->total() }}</strong> baris
        </div>
    </x-gentelella.card>

    {{-- TABEL DATA UNIVERSAL (Mendukung Dynamic JSON & Physical Table) --}}
    <x-gentelella.x-panel title="Data Modul: {{ $modul->nama_modul }}" subtitle="Pengelolaan data operasional mandiri (Universal CRUD Engine)">
        @if($records->isEmpty())
            <div style="padding: 3.5rem 1.5rem; text-align: center;">
                <div style="width: 52px; height: 52px; border-radius: 50%; background: var(--neutral-100); margin: 0 auto 1rem auto; display: flex; align-items: center; justify-content: center; color: var(--neutral-500);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                </div>
                <h4 style="font-size: 1rem; font-weight: 700; color: var(--neutral-800); margin: 0 0 0.35rem 0;">Belum Ada Baris Data</h4>
                <p style="font-size: 0.8125rem; color: var(--neutral-600); max-width: 440px; margin: 0 auto 1.25rem auto;">
                    Mulai dengan menginputkan baris data pertama Anda ke modul {{ $modul->nama_modul }}.
                </p>
                <button type="button" onclick="openCreateModal()" style="height: 38px; padding: 0 1.25rem; background: var(--blue-700); color: #FFFFFF; font-weight: 700; font-size: 0.8125rem; border: none; border-radius: 6px; cursor: pointer;">
                    + Tambah Data Pertama
                </button>
            </div>
        @else
            @php
                $tableFields = $modul->fields->where('tampil_di_tabel', true);
            @endphp
            <div class="table-responsive-gentelella" style="overflow-x: auto;">
                <table class="table jambo_table bulk_action" style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.8125rem;">
                    <thead>
                        <tr class="headings">
                            <th style="width: 70px;">
                                {{ $modul->tipe_sumber === 'physical_table' ? strtoupper($modul->primary_key) : '#ID' }}
                            </th>
                            @foreach($tableFields as $f)
                                <th>{{ $f->label }}</th>
                            @endforeach
                            <th>Dibuat</th>
                            <th style="text-align: right; width: 130px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($records as $row)
                            @php
                                $rowId = $modul->tipe_sumber === 'physical_table' ? ($row->{$modul->primary_key} ?? $row->id ?? '') : $row->id;
                                $rowData = $modul->tipe_sumber === 'physical_table' ? (array) $row : ($row->data ?? []);
                                $rowCreatedAt = $modul->tipe_sumber === 'physical_table' ? ($row->created_at ?? '-') : ($row->created_at?->format('d M Y H:i') ?? '-');
                                $rowCreator = $modul->tipe_sumber === 'physical_table' ? ($row->created_by ?? 'Sistem') : ($row->creator?->nama ?? 'Sistem');
                            @endphp
                            <tr style="border-bottom: 1px solid var(--neutral-200); transition: background 0.15s ease;">
                                <td style="padding: 0.875rem 1rem; font-family: monospace; font-weight: 700; color: var(--neutral-500);">
                                    #{{ $rowId }}
                                </td>

                                @foreach($tableFields as $f)
                                    @php
                                        $col = $f->nama_kolom;
                                        $val = $rowData[$col] ?? null;
                                    @endphp
                                    <td style="padding: 0.875rem 1rem; vertical-align: top;">
                                        @if($f->tipe_data === 'file')
                                            @if(!empty($val))
                                                @if($modul->tipe_sumber === 'dynamic' && !empty($row->files_metadata[$col]))
                                                    <a href="{{ route('superadmin.modul.download-file', ['slug' => $modul->slug, 'id' => $rowId, 'field' => $col]) }}" style="display: inline-flex; align-items: center; gap: 0.35rem; color: var(--blue-700); font-weight: 600; text-decoration: none;">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                                        <span>Unduh Berkas</span>
                                                    </a>
                                                @else
                                                    <a href="{{ $val }}" target="_blank" style="display: inline-flex; align-items: center; gap: 0.35rem; color: var(--blue-700); font-weight: 600; text-decoration: none;">
                                                        <span>Lihat Berkas</span>
                                                    </a>
                                                @endif
                                            @else
                                                <span style="color: var(--neutral-400);">-</span>
                                            @endif
                                        @elseif($f->tipe_data === 'boolean')
                                            @if($val)
                                                <span style="display: inline-block; padding: 0.15rem 0.5rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 700; background: rgba(16, 185, 129, 0.15); color: #059669;">Ya</span>
                                            @else
                                                <span style="display: inline-block; padding: 0.15rem 0.5rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 700; background: var(--neutral-200); color: var(--neutral-600);">Tidak</span>
                                            @endif
                                        @elseif($f->tipe_data === 'multiselect' && is_array($val))
                                            <div style="display: flex; gap: 0.25rem; flex-wrap: wrap;">
                                                @foreach($val as $item)
                                                    <span style="padding: 0.1rem 0.4rem; border-radius: 4px; background: var(--neutral-200); font-size: 0.6875rem; font-weight: 600;">{{ $item }}</span>
                                                @endforeach
                                            </div>
                                        @elseif($f->tipe_data === 'relation' && !empty($val))
                                            @php
                                                $relItems = $relationalOptions[$col] ?? [];
                                                $matched = collect($relItems)->firstWhere('id', (int) $val);
                                            @endphp
                                            <span style="font-weight: 600; color: var(--neutral-900);">
                                                {{ $matched['label'] ?? ('ID #' . $val) }}
                                            </span>
                                        @else
                                            <span style="color: var(--neutral-800);">
                                                {{ $val !== null && $val !== '' ? $val : '-' }}
                                            </span>
                                        @endif
                                    </td>
                                @endforeach

                                <td style="padding: 0.875rem 1rem; vertical-align: top; white-space: nowrap;">
                                    <div style="font-size: 0.75rem; color: var(--neutral-900); font-weight: 600;">{{ $rowCreatedAt }}</div>
                                    <div style="font-size: 0.6875rem; color: var(--neutral-500);">oleh: {{ $rowCreator }}</div>
                                </td>

                                <td style="padding: 0.875rem 1rem; vertical-align: top; text-align: right; white-space: nowrap;">
                                    <button type="button" onclick="openEditModal({{ (string) json_encode($rowId) }}, {{ (string) json_encode($rowData) }})" style="padding: 0.35rem 0.65rem; background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 6px; font-size: 0.75rem; font-weight: 600; color: var(--neutral-800); cursor: pointer; margin-right: 0.35rem;" title="Edit Baris">
                                        Edit
                                    </button>

                                    <form action="{{ route('superadmin.modul.destroy', ['slug' => $modul->slug, 'id' => $rowId]) }}" method="POST" onsubmit="return confirm('Hapus baris data #{{ $rowId }} ini?');" style="display: inline-block; margin: 0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="padding: 0.35rem 0.65rem; background: var(--neutral-0); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 6px; font-size: 0.75rem; font-weight: 600; color: #DC2626; cursor: pointer;" title="Hapus Baris">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($records->hasPages())
                <div style="padding: 1.25rem 1.5rem; border-top: 1px solid var(--neutral-200); background: var(--neutral-100);">
                    {{ $records->links() }}
                </div>
            @endif
        @endif
    </x-gentelella.x-panel>
</div>

<!-- =====================================================================
     UNIVERSAL MODAL CRUD FORM (Create & Edit)
     ===================================================================== -->
<div id="modalDataForm" onclick="if(event.target === this) closeModalDataForm()" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
    <div style="background: var(--neutral-0); border-radius: 16px; width: 100%; max-width: 680px; padding: 2rem; box-shadow: var(--shadow-modal); position: relative; max-height: 90vh; display: flex; flex-direction: column; border: 1px solid var(--neutral-400);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.875rem;">
            <div>
                <h3 id="modalFormTitle" style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0;">
                    Tambah Data Baru
                </h3>
                <span style="font-size: 0.75rem; color: var(--neutral-500);">Modul: {{ $modul->nama_modul }} ({{ $modul->tipe_sumber === 'physical_table' ? 'Tabel: ' . $modul->nama_tabel_fisik : 'Dynamic Schema' }})</span>
            </div>
            <button type="button" onclick="closeModalDataForm()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--neutral-500); line-height: 1; padding: 0.25rem;">&times;</button>
        </div>

        <form id="dynamicDataForm" method="POST" enctype="multipart/form-data" style="overflow-y: auto; display: flex; flex-direction: column; gap: 1rem; padding-right: 0.25rem;">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            {{-- Kolom Primary Key Manual jika diaktifkan --}}
            @if($modul->is_manual_pk && $modul->primary_key)
                <div id="box-manual-pk">
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase; margin-bottom: 0.35rem;">
                        {{ strtoupper($modul->primary_key) }} (PRIMARY KEY) <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="text" name="{{ $modul->primary_key }}" id="input_{{ $modul->primary_key }}" placeholder="Masukkan nilai primary key unik..." required style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.8125rem; font-family: monospace;">
                </div>
            @endif

            @foreach($modul->fields as $f)
                @php
                    $col = $f->nama_kolom;
                @endphp
                {{-- Skip jika kolom adalah manual PK dan sudah ditampilkan di atas --}}
                @if($modul->is_manual_pk && $col === $modul->primary_key)
                    @continue
                @endif
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase; margin-bottom: 0.35rem;">
                        {{ $f->label }} {!! $f->wajib ? '<span style="color: #DC2626;">*</span>' : '' !!}
                    </label>

                    @if($f->tipe_data === 'textarea')
                        <textarea name="{{ $col }}" id="input_{{ $col }}" rows="3" {{ $f->wajib ? 'required' : '' }} style="width: 100%; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0.625rem 0.875rem; font-size: 0.8125rem; font-family: inherit;"></textarea>

                    @elseif($f->tipe_data === 'number')
                        <input type="number" step="any" name="{{ $col }}" id="input_{{ $col }}" {{ $f->wajib ? 'required' : '' }} style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.8125rem; font-family: inherit;">

                    @elseif($f->tipe_data === 'date')
                        <input type="date" name="{{ $col }}" id="input_{{ $col }}" {{ $f->wajib ? 'required' : '' }} style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.8125rem; font-family: inherit;">

                    @elseif($f->tipe_data === 'datetime')
                        <input type="datetime-local" name="{{ $col }}" id="input_{{ $col }}" {{ $f->wajib ? 'required' : '' }} style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.8125rem; font-family: inherit;">

                    @elseif($f->tipe_data === 'select')
                        <select name="{{ $col }}" id="input_{{ $col }}" {{ $f->wajib ? 'required' : '' }} style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.75rem; font-size: 0.8125rem; background: var(--neutral-0); font-family: inherit;">
                            <option value="">-- Pilih {{ $f->label }} --</option>
                            @foreach($f->opsi_pilihan ?? [] as $opt)
                                <option value="{{ $opt }}">{{ $opt }}</option>
                            @endforeach
                        </select>

                    @elseif($f->tipe_data === 'boolean')
                        <label style="display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; font-weight: 600; cursor: pointer;">
                            <input type="checkbox" name="{{ $col }}" id="input_{{ $col }}" value="1">
                            <span>Aktif / Terpenuhi (Ya)</span>
                        </label>

                    @elseif($f->tipe_data === 'file')
                        <input type="file" name="{{ $col }}" id="input_{{ $col }}" style="width: 100%; font-size: 0.8125rem;">
                        <span style="font-size: 0.6875rem; color: var(--neutral-500); display: block; margin-top: 0.2rem;">Format: PDF, JPG, PNG, WEBP, DOCX, XLSX (Maks. 5MB)</span>

                    @elseif($f->tipe_data === 'relation')
                        <select name="{{ $col }}" id="input_{{ $col }}" {{ $f->wajib ? 'required' : '' }} style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.75rem; font-size: 0.8125rem; background: var(--neutral-0); font-family: inherit;">
                            <option value="">-- Pilih Relasi {{ $f->label }} --</option>
                            @foreach($relationalOptions[$col] ?? [] as $rOpt)
                                <option value="{{ $rOpt['id'] }}">{{ $rOpt['label'] }} (ID: {{ $rOpt['id'] }})</option>
                            @endforeach
                        </select>

                    @else
                        {{-- text / default --}}
                        <input type="text" name="{{ $col }}" id="input_{{ $col }}" {{ $f->wajib ? 'required' : '' }} style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.8125rem; font-family: inherit;">
                    @endif
                </div>
            @endforeach

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--neutral-200); padding-top: 1rem; margin-top: 0.5rem;">
                <button type="button" onclick="closeModalDataForm()" style="height: 40px; padding: 0 1.25rem; background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 8px; font-weight: 700; font-size: 0.8125rem; color: var(--neutral-700); cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="height: 40px; padding: 0 1.75rem; background: var(--blue-700); color: #FFFFFF; font-weight: 700; font-size: 0.8125rem; border: none; border-radius: 8px; cursor: pointer; box-shadow: var(--shadow-subtle);">
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const baseUrl = "{{ (string) url('superadmin/modul/' . $modul->slug) }}";

    function openCreateModal() {
        document.getElementById('modalFormTitle').textContent = 'Tambah Data Baru';
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('dynamicDataForm').action = baseUrl;
        document.getElementById('dynamicDataForm').reset();
        document.getElementById('modalDataForm').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function openEditModal(recordId, data) {
        document.getElementById('modalFormTitle').textContent = 'Edit Data Baris #' + recordId;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('dynamicDataForm').action = baseUrl + '/' + recordId;
        document.getElementById('dynamicDataForm').reset();

        for (const [key, value] of Object.entries(data || {})) {
            const input = document.getElementById('input_' + key);
            if (input) {
                if (input.type === 'checkbox') {
                    input.checked = Boolean(value);
                } else if (input.type !== 'file') {
                    input.value = value;
                }
            }
        }

        document.getElementById('modalDataForm').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeModalDataForm() {
        document.getElementById('modalDataForm').style.display = 'none';
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModalDataForm();
        }
    });
</script>
@endsection
