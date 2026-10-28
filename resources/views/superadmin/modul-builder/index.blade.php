@extends('layouts.dashboard')

@section('title', 'Katalog Modul Kustom - SuperAdmin MagKom')
@section('page_title', 'Katalog Modul Kustom (Low-Code Studio)')
@section('page_description', 'Kelola, konfigurasikan, dan pantau seluruh modul kustom yang aktif di portal MagKom Sleman.')

@section('breadcrumbs')
    <a href="{{ route('superadmin.dashboard') }}">SuperAdmin</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Pembuat Modul</span>
@endsection

@section('page_actions')
    <a href="{{ route('superadmin.modul-builder.create') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; height: 42px; padding: 0 1.25rem; background: var(--blue-600); color: #FFFFFF; font-weight: 700; font-size: 0.875rem; border-radius: var(--studio-radius-sm, 8px); text-decoration: none; box-shadow: 0 2px 8px rgba(1, 52, 124, 0.2); transition: all 0.15s ease;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        <span>+ Buat Modul Baru</span>
    </a>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modul-studio.css') }}">
    <style>
        .catalog-stats-bar {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card-widget {
            background: var(--studio-card-bg);
            border: 1px solid var(--studio-border);
            border-radius: var(--studio-radius-md);
            padding: 1.15rem 1.25rem;
            box-shadow: var(--studio-shadow);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stat-card-widget .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: var(--studio-radius-sm);
            background: var(--studio-primary-glow);
            color: var(--studio-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stat-card-widget .stat-info strong {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--neutral-900, #0F172A);
            display: block;
            line-height: 1.2;
        }

        .stat-card-widget .stat-info span {
            font-size: 0.75rem;
            color: var(--neutral-600, #64748B);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        [data-theme="dark"] .stat-card-widget .stat-info strong {
            color: #F8FAFC !important;
        }

        .catalog-filter-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }

        .catalog-tabs {
            display: flex;
            gap: 0.4rem;
            background: var(--studio-card-bg);
            border: 1px solid var(--studio-border);
            padding: 0.25rem;
            border-radius: var(--studio-radius-md);
        }

        .catalog-tab-btn {
            padding: 0.45rem 0.85rem;
            border: none;
            background: transparent;
            border-radius: var(--studio-radius-sm);
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--neutral-600, #64748B);
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .catalog-tab-btn.active, .catalog-tab-btn:hover {
            background: var(--studio-primary-glow);
            color: var(--studio-primary);
        }

        .catalog-search-box {
            position: relative;
            min-width: 280px;
        }

        .catalog-search-box input {
            width: 100%;
            height: 38px;
            background: var(--studio-card-bg);
            border: 1px solid var(--studio-border);
            border-radius: var(--studio-radius-sm);
            padding: 0 1rem 0 2.25rem;
            font-size: 0.8125rem;
            color: var(--neutral-900);
        }

        .catalog-search-box svg {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--neutral-400);
            pointer-events: none;
        }

        .module-grid-card {
            background: var(--studio-card-bg);
            border: 1px solid var(--studio-border);
            border-radius: var(--studio-radius-lg);
            padding: 1.35rem;
            box-shadow: var(--studio-shadow);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.2s ease;
        }

        .module-grid-card:hover {
            border-color: var(--studio-border-hover);
            box-shadow: var(--studio-shadow-hover);
            transform: translateY(-2px);
        }
    </style>
@endpush

@section('content')
<div class="studio-container">

    {{-- FEEDBACK ALERTS --}}
    @if(session('status_keberhasilan'))
        <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); color: #065F46; padding: 1rem 1.25rem; border-radius: var(--studio-radius-md); display: flex; align-items: center; gap: 0.75rem; font-size: 0.875rem; font-weight: 600;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            <span>{{ session('status_keberhasilan') }}</span>
        </div>
    @endif

    {{-- STATS METRICS BAR (Gentelella Tile Stats) --}}
    <div class="row tile_count" style="margin: 0 -10px 1.5rem -10px;">
        <x-gentelella.tile-stat
            col="col-md-4 col-sm-6 col-xs-12"
            title="Modul Aktif"
            :value="$modules->where('status_aktif', true)->count() . ' / ' . $modules->count()"
            subText="Modul siap operasional"
        />
        <x-gentelella.tile-stat
            col="col-md-4 col-sm-6 col-xs-12"
            title="Akses Publik Terbuka"
            :value="$modules->where('is_public_read', true)->count() . ' Modul'"
            subText="Dapat diakses portal publik"
        />
        <x-gentelella.tile-stat
            col="col-md-4 col-sm-6 col-xs-12"
            title="Total Data Tersimpan"
            :value="number_format($modules->sum('records_count')) . ' Baris'"
            subText="Tercatat di basis data"
        />
    </div>

    {{-- FILTER & SEARCH TOOLBAR --}}
    <div class="catalog-filter-bar">
        <div class="catalog-tabs">
            <button type="button" class="catalog-tab-btn active" onclick="filterModuleCards('all', this)">Semua ({{ (string) $modules->count() }})</button>
            <button type="button" class="catalog-tab-btn" onclick="filterModuleCards('public', this)">Akses Publik ({{ (string) $modules->where('is_public_read', true)->count() }})</button>
            <button type="button" class="catalog-tab-btn" onclick="filterModuleCards('private', this)">Internal ({{ (string) $modules->where('is_public_read', false)->count() }})</button>
            <button type="button" class="catalog-tab-btn" onclick="filterModuleCards('table', this)">Tabel DB ({{ (string) $modules->where('tipe_sumber', 'physical_table')->count() }})</button>
        </div>

        <div class="catalog-search-box">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            <input type="text" id="catalog-search" placeholder="Cari modul atau rute..." oninput="searchModuleCards(this.value)">
        </div>
    </div>

    {{-- DAFTAR MODUL KUSTOM GRID --}}
    @if($modules->isEmpty())
        <div style="background: var(--studio-card-bg); border: 1px dashed var(--studio-border); border-radius: var(--studio-radius-lg); padding: 3.5rem 1.5rem; text-align: center; box-shadow: var(--studio-shadow);">
            <div style="width: 56px; height: 56px; border-radius: 50%; background: var(--studio-primary-glow); color: var(--studio-primary); margin: 0 auto 1.25rem auto; display: flex; align-items: center; justify-content: center;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
            </div>
            <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0 0 0.5rem 0;">Belum Ada Modul Kustom</h3>
            <p style="font-size: 0.875rem; color: var(--neutral-600); max-width: 480px; margin: 0 auto 1.5rem auto;">
                Rancang modul pertama Anda (seperti Inventaris Perangkat, Log Kunjungan Tamu, atau Penugasan Khusus) dalam hitungan detik.
            </p>
            <a href="{{ route('superadmin.modul-builder.create') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; height: 42px; padding: 0 1.5rem; background: var(--blue-600); color: #FFFFFF; font-weight: 700; font-size: 0.875rem; border-radius: var(--studio-radius-sm); text-decoration: none;">
                + Buka Modul Studio Sekarang
            </a>
        </div>
    @else
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1.5rem;" id="modules-cards-container">
            @foreach($modules as $m)
                @php
                    $isPublic = $m->is_public_read ? 'true' : 'false';
                    $isTable = $m->tipe_sumber === 'physical_table' ? 'true' : 'false';
                @endphp
                <div class="module-grid-card" data-public="{{ $isPublic }}" data-table="{{ $isTable }}" data-title="{{ strtolower($m->nama_modul) }} {{ strtolower($m->slug) }}">
                    <div>
                        <!-- Header Kartu -->
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.875rem;">
                            <div style="display: flex; align-items: center; gap: 0.875rem;">
                                <div class="studio-avatar-badge" style="width: 44px; height: 44px;">
                                    {!! \App\Models\ModulDinamis::getSvgForIcon($m->ikon ?? 'box', 22) !!}
                                </div>
                                <div>
                                    <h4 style="font-size: 1.05rem; font-weight: 800; color: var(--neutral-900); margin: 0; line-height: 1.3;">
                                        {{ $m->nama_modul }}
                                    </h4>
                                    <span style="font-size: 0.72rem; font-family: monospace; color: var(--neutral-500);">
                                        /modul/{{ $m->slug }}
                                    </span>
                                </div>
                            </div>

                            <!-- Switch Status Aktif -->
                            <form action="{{ route('superadmin.modul-builder.toggle', $m) }}" method="POST" style="margin: 0;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" title="Klik untuk mengubah status aktif" style="background: none; border: none; cursor: pointer; padding: 0;">
                                    @if($m->status_aktif)
                                        <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.2rem 0.55rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 700; background: rgba(16, 185, 129, 0.12); color: #059669; border: 1px solid rgba(16, 185, 129, 0.25);">
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #10B981;"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.2rem 0.55rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 700; background: rgba(239, 68, 68, 0.12); color: #DC2626; border: 1px solid rgba(239, 68, 68, 0.25);">
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #DC2626;"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                </button>
                            </form>
                        </div>

                        <!-- Deskripsi -->
                        <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0 0 1rem 0; min-height: 38px; line-height: 1.45;">
                            {{ $m->deskripsi ?: 'Modul kustom terintegrasi dengan portal data MagKom Sleman.' }}
                        </p>

                        <!-- Telemetri Modul -->
                        <div style="background: var(--studio-row-bg); padding: 0.75rem 1rem; border-radius: var(--studio-radius-sm); border: 1px solid var(--studio-border); display: flex; justify-content: space-around; margin-bottom: 1rem;">
                            <div style="text-align: center;">
                                <div style="font-size: 0.6875rem; color: var(--neutral-500); text-transform: uppercase; font-weight: 700;">Skema Kolom</div>
                                <div style="font-size: 1.05rem; font-weight: 800; color: var(--neutral-900);">{{ $m->fields_count }} Kolom</div>
                            </div>
                            <div style="width: 1px; background: var(--studio-border);"></div>
                            <div style="text-align: center;">
                                <div style="font-size: 0.6875rem; color: var(--neutral-500); text-transform: uppercase; font-weight: 700;">Data Tercatat</div>
                                <div style="font-size: 1.05rem; font-weight: 800; color: var(--studio-primary);">{{ number_format($m->records_count) }} Baris</div>
                            </div>
                        </div>

                        <!-- Badge Metadata & Roles -->
                        <div style="display: flex; gap: 0.35rem; flex-wrap: wrap; margin-bottom: 1.25rem;">
                            @if($m->is_public_read)
                                <span style="font-size: 0.6875rem; background: rgba(16, 185, 129, 0.1); color: #059669; padding: 0.15rem 0.5rem; border-radius: 4px; font-weight: 700;">
                                    Akses Publik
                                </span>
                            @endif
                            @if($m->tipe_sumber === 'physical_table')
                                <span style="font-size: 0.6875rem; background: rgba(139, 92, 246, 0.1); color: #7C3AED; padding: 0.15rem 0.5rem; border-radius: 4px; font-weight: 700;">
                                    Tabel: {{ $m->nama_tabel_fisik }}
                                </span>
                            @endif
                            @foreach($m->target_roles ?? [] as $role)
                                <span style="font-size: 0.6875rem; background: var(--studio-row-bg); color: var(--neutral-600); border: 1px solid var(--studio-border); padding: 0.15rem 0.45rem; border-radius: 4px; text-transform: capitalize;">
                                    {{ $role }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div style="border-top: 1px solid var(--studio-border); padding-top: 1rem; display: flex; justify-content: space-between; align-items: center; gap: 0.5rem;">
                        <a href="{{ route('superadmin.modul.index', ['slug' => $m->slug]) }}" style="height: 36px; padding: 0 1rem; background: var(--studio-primary); color: #FFFFFF; font-weight: 700; font-size: 0.8125rem; border-radius: var(--studio-radius-sm); text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem; flex: 1; justify-content: center;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                            <span>Buka Data Modul</span>
                        </a>

                        <a href="{{ route('superadmin.modul-builder.edit', $m) }}" class="btn-icon-action" style="width: 36px; height: 36px;" title="Edit Skema Kolom">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                        </a>

                        <form action="{{ route('superadmin.modul-builder.destroy', $m) }}" method="POST" onsubmit="return confirm('PENTING: Anda yakin ingin menghapus modul {{ $m->nama_modul }}? Seluruh skema kolom dan {{ $m->records_count }} baris data di dalamnya akan terhapus permanen.');" style="margin: 0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-icon-action btn-icon-danger" style="width: 36px; height: 36px;" title="Hapus Modul">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function filterModuleCards(filter, btn) {
        document.querySelectorAll('.catalog-tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        document.querySelectorAll('.module-grid-card').forEach(card => {
            const isPub = card.dataset.public === 'true';
            const isTbl = card.dataset.table === 'true';

            let show = false;
            if (filter === 'all') {
                show = true;
            } else if (filter === 'public' && isPub) {
                show = true;
            } else if (filter === 'private' && !isPub) {
                show = true;
            } else if (filter === 'table' && isTbl) {
                show = true;
            }

            card.style.display = show ? 'flex' : 'none';
        });
    }

    function searchModuleCards(query) {
        const q = query.toLowerCase().trim();
        document.querySelectorAll('.module-grid-card').forEach(card => {
            const title = card.dataset.title;
            const match = title.includes(q);
            card.style.display = match ? 'flex' : 'none';
        });
    }
</script>
@endpush
