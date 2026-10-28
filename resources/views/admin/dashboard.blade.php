@extends('layouts.dashboard')

@section('title', 'Dashboard Operasional Administrasi - MagKom Sleman')

@section('breadcrumbs')
    <span class="breadcrumb-current">Dashboard</span>
@endsection
@push('styles')
    <style>
        .adm-table-spacious {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.875rem;
        }
        .adm-table-spacious th {
            background-color: var(--neutral-150);
            color: var(--neutral-600);
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--neutral-300);
            text-align: left;
        }
        .adm-table-spacious td {
            padding: 0.875rem 1rem;
            border-bottom: 1px solid var(--neutral-200);
            vertical-align: middle;
            color: var(--neutral-800);
        }
        .adm-table-spacious tr:hover td {
            background-color: var(--neutral-100);
        }
        .adm-avatar-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--blue-tint);
            color: var(--blue-700);
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .adm-btn-action-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.35rem 0.75rem;
            background: var(--blue-600);
            color: #FFFFFF;
            font-size: 0.785rem;
            font-weight: 600;
            border-radius: 6px;
            text-decoration: none;
            transition: all 0.15s ease;
        }
        .adm-btn-action-primary:hover {
            background: var(--blue-700);
            color: #FFFFFF;
        }
        .adm-filter-pills {
            display: inline-flex;
            background: var(--neutral-150);
            padding: 3px;
            border-radius: 8px;
            border: 1px solid var(--neutral-300);
            gap: 2px;
        }
        .adm-pill-btn {
            background: transparent;
            border: none;
            padding: 0.3rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--neutral-600);
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .adm-pill-btn.active {
            background: var(--neutral-0);
            color: var(--blue-700);
            box-shadow: var(--shadow-subtle);
        }
        .adm-sla-pill-alert {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.2rem 0.5rem;
            background: rgba(239, 68, 68, 0.12);
            color: var(--red-600);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .adm-sla-pill-norm {
            display: inline-flex;
            align-items: center;
            padding: 0.2rem 0.5rem;
            background: var(--neutral-200);
            color: var(--neutral-700);
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        .adm-quota-row-item {
            padding: 0.75rem 1rem;
            background: var(--neutral-150);
            border: 1px solid var(--neutral-300);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 0.65rem;
        }
        .adm-mentor-clean-row {
            padding: 0.65rem 0.85rem;
            background: var(--neutral-150);
            border: 1px solid var(--neutral-300);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 0.5rem;
        }
    </style>
@endpush

@section('content')
{{-- =========================================================================
   Gentelella Cockpit Dashboard - Operasional Administrasi Dinas Kominfo Sleman
   Standar Visual Gentelella Theme (Zero-Regression & Adaptive Responsive)
   ========================================================================= --}}
<div class="gentelella-dashboard-admin adm-cockpit-wrapper" style="display: flex; flex-direction: column; gap: 1.5rem; width: 100%;">

    {{-- 1. Atmospheric Hero Card, 4-Metric Dock & Statusline Ribbon --}}
    @include('admin.dashboard.partials._header')

    {{-- 2. Asymmetrical Cockpit 2-Column Grid (Main 1fr + Sticky Aside 340px) --}}
    <div class="cv-cockpit-layout">
        {{-- Kolom Utama: Attention, Antrean Verifikasi & Kuota Monitoring --}}
        <div class="cv-cockpit-main" style="display: flex; flex-direction: column; gap: 1.5rem; min-width: 0;">
            @include('admin.dashboard.partials._attention')
            @include('admin.dashboard.partials._queue_table')
            @include('admin.dashboard.partials._quota_monitoring')
        </div>

        {{-- Kolom Samping: Sticky Aside --}}
        <aside class="cv-aside-sticky">
            @include('admin.dashboard.partials._aside')
        </aside>
    </div>

    {{-- Slide-Over Drawer: Pratinjau Cepat Berkas Pendaftaran --}}
    @include('admin.dashboard.partials._drawer_verifikasi')

</div>

<script>
/**
 * Instant filter untuk tabel antrean verifikasi tanpa reload halaman
 */
function filterQueueTable(filterType, btn) {
    document.querySelectorAll('.adm-pill-btn').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');

    const rows = document.querySelectorAll('.queue-row');
    let visibleCount = 0;
    rows.forEach(row => {
        let show = false;
        if (filterType === 'all') {
            show = true;
        } else if (filterType === 'diajukan') {
            show = row.classList.contains('status-diajukan');
        } else if (filterType === 'perlu_perbaikan') {
            show = row.classList.contains('status-perlu-perbaikan');
        }
        row.style.display = show ? '' : 'none';
        if (show) visibleCount++;
    });

    const emptyRow = document.getElementById('queueFilterEmptyRow');
    if (emptyRow) {
        emptyRow.style.display = (visibleCount === 0 && rows.length > 0) ? '' : 'none';
    }
}
</script>
@endsection
