@extends('layouts.dashboard')

@section('title', 'Dashboard Operasional Pembimbing - MagKom Sleman')
@section('page_title', 'Dashboard Pembimbing')
@section('page_description', 'Pusat komando pemantauan bimbingan magang, reviu tugas & logbook, serta evaluasi rubrik dinas.')

@section('breadcrumbs')
    <span class="breadcrumb-current">Dashboard Pembimbing</span>
@endsection

@push('styles')
<style>
    /* Tab Navigation Styles */
    .pembimbing-tab-bar {
        display: flex;
        gap: 0.5rem;
        background: #ffffff;
        padding: 0.5rem;
        border-radius: 12px;
        border: 1px solid var(--neutral-200, #E2E8F0);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        overflow-x: auto;
        scrollbar-width: none;
    }
    .pembimbing-tab-bar::-webkit-scrollbar {
        display: none;
    }
    .pembimbing-tab-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.65rem 1.25rem;
        border-radius: 8px;
        border: none;
        background: transparent;
        color: var(--neutral-600, #64748B);
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        white-space: nowrap;
    }
    .pembimbing-tab-btn:hover {
        background: rgba(6, 65, 204, 0.06);
        color: #0641CC;
    }
    .pembimbing-tab-btn.active {
        background: #0641CC;
        color: #ffffff;
        box-shadow: 0 3px 8px rgba(6, 65, 204, 0.25);
    }
    .pembimbing-tab-btn.active .tab-badge {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
    }
    .tab-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.15rem 0.5rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        background: var(--neutral-200, #E2E8F0);
        color: var(--neutral-700, #334155);
        transition: all 0.2s ease;
    }
    .tab-badge.badge-amber {
        background: #fef3c7;
        color: #92400e;
    }
    .tab-badge.badge-blue {
        background: #e0e7ff;
        color: #3730a3;
    }

    /* Tab Panes */
    .tab-pane {
        display: none;
        flex-direction: column;
        gap: 1.5rem;
        animation: fadeIn 0.25s ease-in-out;
    }
    .tab-pane.active {
        display: flex;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(4px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Custom Minimal Scrollbar */
    .clean-scrollbar {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 transparent;
    }
    .clean-scrollbar::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .clean-scrollbar::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 4px;
    }

    /* ========================================================================= */
    /* Komponen Kartu Tugas Terpadu Ala Dashboard Peserta Magang (DS-MagKom)     */
    /* ========================================================================= */
    .list-card {
        background: var(--neutral-0, #ffffff);
        border: 1px solid var(--neutral-200, #E2E8F0);
        border-radius: 12px;
        padding: 18px 22px;
        display: flex;
        flex-direction: column;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        transition: box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .list-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        padding-bottom: 14px;
        border-bottom: 1px solid var(--neutral-200, #E2E8F0);
        flex-wrap: wrap;
        gap: 12px;
    }

    .list-card-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--neutral-900, #0F172A);
        display: flex;
        align-items: center;
        gap: 8px;
        line-height: 1.3;
        margin: 0;
    }

    /* Filter Tabs Mini Cepat pada Header Kartu Tugas */
    .task-quick-filters {
        display: flex;
        gap: 6px;
        align-items: center;
        flex-wrap: wrap;
    }

    .task-filter-btn {
        background: var(--neutral-100, #F8FAFC);
        border: 1px solid var(--neutral-200, #E2E8F0);
        padding: 5px 12px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--neutral-600, #64748B);
        cursor: pointer;
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-family: inherit;
    }

    .task-filter-btn:hover {
        background: rgba(6, 65, 204, 0.08);
        color: #0641CC;
        border-color: #0641CC;
    }

    .task-filter-btn.active {
        background: #0641CC;
        color: #ffffff;
        border-color: #0641CC;
        box-shadow: 0 2px 6px rgba(6, 65, 204, 0.2);
    }

    .task-filter-btn .filter-badge {
        padding: 1px 6px;
        border-radius: 999px;
        font-size: 0.65rem;
        font-weight: 700;
        background: rgba(0, 0, 0, 0.08);
    }

    .task-filter-btn.active .filter-badge {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
    }

    /* Item Row Tugas */
    .task-item-row {
        padding: 14px 0;
        border-bottom: 1px solid var(--neutral-100, #F1F5F9);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        transition: background-color 0.15s ease;
    }

    .task-item-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .task-item-row:first-child {
        padding-top: 4px;
    }

    .task-item-left {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        flex: 1;
        min-width: 0;
    }

    /* Mentee Avatar Inisial Badge */
    .mentee-avatar-chip {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: linear-gradient(135deg, #0641CC 0%, #2563EB 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8125rem;
        font-weight: 700;
        flex-shrink: 0;
        box-shadow: 0 2px 6px rgba(6, 65, 204, 0.16);
    }

    .task-item-meta {
        flex: 1;
        min-width: 0;
    }

    .task-item-meta h4 {
        font-size: 0.9375rem;
        font-weight: 700;
        color: var(--neutral-900, #0F172A);
        margin: 0 0 4px 0;
        line-height: 1.35;
    }

    .task-item-meta h4 a {
        color: inherit;
        text-decoration: none;
        transition: color 0.15s ease;
    }

    .task-item-meta h4 a:hover {
        color: #0641CC;
    }

    .task-mentee-info {
        font-size: 0.775rem;
        color: var(--neutral-600, #475569);
        margin: 0 0 6px 0;
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    .task-badges-row {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    /* Deadline Pill - Standar Desain Dashboard Peserta Magang */
    .deadline-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 9px;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 700;
        background: var(--neutral-100, #F1F5F9);
        color: var(--neutral-600, #475569);
        border: 1px solid var(--neutral-300, #CBD5E1);
        white-space: nowrap;
    }

    .deadline-pill.is-urgent {
        background: #FEF2F2;
        color: #DC2626;
        border-color: #FECACA;
    }

    .deadline-pill.is-warning {
        background: #FFFBEB;
        color: #D97706;
        border-color: #FDE68A;
    }

    /* Category Chip */
    .task-category-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.7rem;
        font-weight: 600;
        background: #EFF6FF;
        color: #1D4ED8;
        padding: 3px 8px;
        border-radius: 6px;
        border: 1px solid #BFDBFE;
    }

    /* Action Buttons Compact */
    .btn-task-action {
        padding: 6px 14px;
        font-size: 0.775rem;
        font-weight: 600;
        border-radius: 8px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
        transition: all 0.15s ease;
        border: 1px solid transparent;
        cursor: pointer;
    }

    .btn-task-action-primary {
        background: #0641CC;
        color: #ffffff;
        box-shadow: 0 2px 6px rgba(6, 65, 204, 0.2);
    }

    .btn-task-action-primary:hover {
        background: #0535A6;
        color: #ffffff;
        transform: translateY(-1px);
    }

    .btn-task-action-secondary {
        background: var(--neutral-100, #F8FAFC);
        color: var(--neutral-700, #334155);
        border-color: var(--neutral-300, #CBD5E1);
    }

    .btn-task-action-secondary:hover {
        background: var(--neutral-200, #E2E8F0);
        color: #0641CC;
        border-color: #0641CC;
    }

    /* Micro Progress Bar untuk KPI Metrik Pembimbing */
    .micro-progress-bar {
        height: 6px;
        background: var(--neutral-200, #E2E8F0);
        border-radius: 999px;
        overflow: hidden;
        margin: 6px 0 4px 0;
        width: 100%;
    }

    .micro-progress-fill {
        height: 100%;
        border-radius: 999px;
        background: #10B981;
        transition: width 0.3s ease;
    }

    /* Dark Mode Harmonization */
    [data-theme="dark"] .pembimbing-tab-bar {
        background-color: #0F172A;
        border-color: #26354D;
    }

    [data-theme="dark"] .pembimbing-tab-btn {
        color: #94A3B8;
    }

    [data-theme="dark"] .pembimbing-tab-btn:hover {
        background-color: rgba(255, 255, 255, 0.05);
        color: #F8FAFC;
    }

    [data-theme="dark"] .pembimbing-tab-btn.active {
        background-color: #1E3A8A;
        color: #93C5FD;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }

    [data-theme="dark"] .tab-badge {
        background-color: #26354D;
        color: #E2E8F0;
    }

    [data-theme="dark"] .tab-badge.badge-amber {
        background-color: rgba(217, 119, 6, 0.25);
        color: #FCD34D;
    }

    [data-theme="dark"] .tab-badge.badge-blue {
        background-color: rgba(37, 99, 235, 0.25);
        color: #93C5FD;
    }

    [data-theme="dark"] .list-card {
        background: #182234;
        border-color: #26354D;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.25);
    }

    [data-theme="dark"] .list-card-header {
        border-bottom-color: #26354D;
    }

    [data-theme="dark"] .list-card-title {
        color: #F8FAFC;
    }

    [data-theme="dark"] .task-item-row {
        border-bottom-color: #26354D;
    }

    [data-theme="dark"] .task-item-meta h4 {
        color: #F8FAFC;
    }

    [data-theme="dark"] .task-mentee-info {
        color: #94A3B8;
    }

    [data-theme="dark"] .deadline-pill {
        background: #1E293B;
        border-color: #334155;
        color: #CBD5E1;
    }

    [data-theme="dark"] .deadline-pill.is-urgent {
        background: rgba(239, 68, 68, 0.2);
        color: #F87171;
        border-color: rgba(239, 68, 68, 0.4);
    }

    [data-theme="dark"] .deadline-pill.is-warning {
        background: rgba(245, 158, 11, 0.2);
        color: #FBBF24;
        border-color: rgba(245, 158, 11, 0.4);
    }

    [data-theme="dark"] .task-category-chip {
        background: rgba(30, 58, 138, 0.35);
        color: #93C5FD;
        border-color: rgba(59, 130, 246, 0.3);
    }

    [data-theme="dark"] .task-filter-btn {
        background: #0F172A;
        border-color: #26354D;
        color: #94A3B8;
    }

    [data-theme="dark"] .task-filter-btn.active {
        background: #0641CC;
        color: #ffffff;
        border-color: #0641CC;
    }

    [data-theme="dark"] .btn-task-action-secondary {
        background: #1E293B;
        border-color: #334155;
        color: #CBD5E1;
    }

    [data-theme="dark"] .btn-task-action-secondary:hover {
        background: #26354D;
        color: #60A5FA;
        border-color: #3B82F6;
    }

    [data-theme="dark"] .micro-progress-bar {
        background: #26354D;
    }
</style>
@endpush

@section('content')
<div class="dashboard-content" style="display: flex; flex-direction: column; gap: 1.5rem;">

    <!-- 1. Gentelella Page Title Bar -->
    <x-gentelella.page-title
        title="Dashboard Pembimbing"
        description="Pusat komando pemantauan bimbingan magang, reviu tugas & logbook, serta evaluasi rubrik dinas."
    >
        <x-slot:actions>
            <a href="{{ route('pembimbing.tugas.create') }}" class="btn btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem; background: #0641CC; color: #ffffff; border-radius: 8px; font-weight: 700; padding: 0.5rem 1rem; text-decoration: none; box-shadow: 0 2px 6px rgba(6, 65, 204, 0.2);">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                + Beri Tugas Baru
            </a>
            <a href="{{ route('pembimbing.agenda.index') }}" class="btn btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem; background: #ffffff; color: var(--neutral-700, #334155); border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; font-weight: 600; padding: 0.5rem 1rem; text-decoration: none;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                Agenda Mentoring
            </a>
        </x-slot:actions>
    </x-gentelella.page-title>

    <!-- 2. Hero Header Eksekutif Pembimbing Sleman Blue -->
    <div style="background: linear-gradient(135deg, #0641CC 0%, #1E40AF 60%, #1e3a8a 100%); border-radius: 14px; padding: 1.75rem 2rem; color: #ffffff; box-shadow: 0 10px 25px -5px rgba(6, 65, 204, 0.25); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem; position: relative; overflow: hidden;">
        <!-- Visual Accent Circles -->
        <div style="position: absolute; right: -40px; top: -40px; width: 220px; height: 220px; background: rgba(255, 255, 255, 0.06); border-radius: 50%; pointer-events: none;"></div>
        <div style="position: absolute; right: 120px; bottom: -60px; width: 160px; height: 160px; background: rgba(255, 255, 255, 0.04); border-radius: 50%; pointer-events: none;"></div>

        <div style="display: flex; flex-direction: column; gap: 0.5rem; z-index: 1; max-width: 650px;">
            <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                <span style="background: rgba(255, 255, 255, 0.18); backdrop-filter: blur(8px); padding: 0.3rem 0.85rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600; letter-spacing: 0.05em; text-transform: uppercase;">
                    {{ $pembimbingBidang }}
                </span>
                <span style="font-size: 0.8rem; opacity: 0.85;">Diskominfo Kabupaten Sleman</span>
            </div>
            <h1 style="font-size: 1.5rem; font-weight: 700; margin: 0; line-height: 1.25; color: #ffffff;">
                Selamat Datang, {{ auth()->user()->nama }}!
            </h1>
            <p style="font-size: 0.875rem; opacity: 0.9; margin: 0; line-height: 1.5;">
                Pusat kendali operasional bimbingan magang dinas. Pantau kemajuan peserta, evaluasi antrean tugas & logbook harian, serta tetapkan penilaian rubrik secara real-time.
            </p>
            <!-- Quick Action Shortcuts -->
            <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap; margin-top: 0.5rem;">
                <a href="{{ route('pembimbing.tugas.create') }}" style="display: inline-flex; align-items: center; gap: 0.45rem; background: #ffffff; color: #0641CC; padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.8125rem; font-weight: 700; border: none; text-decoration: none; cursor: pointer; box-shadow: 0 2px 6px rgba(0,0,0,0.1); transition: transform 0.2s ease;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    + Beri Tugas Baru
                </a>
                <button type="button" onclick="switchPembimbingTab('tab-antrean')" style="display: inline-flex; align-items: center; gap: 0.45rem; background: rgba(255, 255, 255, 0.18); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.3); color: #ffffff; padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.8125rem; font-weight: 600; cursor: pointer; transition: background 0.2s ease;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line></svg>
                    Reviu Antrean Hari Ini
                    @if($logbookPendingCount + $progresPendingCount > 0)
                        <span style="background: #f59e0b; color: #78350f; font-size: 0.7rem; font-weight: 800; padding: 0.1rem 0.45rem; border-radius: 999px;">
                            {{ $logbookPendingCount + $progresPendingCount }}
                        </span>
                    @endif
                </button>
                <button type="button" onclick="switchPembimbingTab('tab-evaluasi')" style="display: inline-flex; align-items: center; gap: 0.45rem; background: rgba(255, 255, 255, 0.12); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.2); color: #ffffff; padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.8125rem; font-weight: 600; cursor: pointer; transition: background 0.2s ease;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                    Kelola Nilai Magang
                </button>
                <a href="{{ route('pembimbing.agenda.index') }}" style="display: inline-flex; align-items: center; gap: 0.45rem; background: rgba(255, 255, 255, 0.18); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.3); color: #ffffff; padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.8125rem; font-weight: 600; text-decoration: none; cursor: pointer; transition: background 0.2s ease;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    Agenda Mentoring
                    @if($upcomingAgendaBimbingan->count() > 0)
                        <span style="background: #ffffff; color: #0641CC; font-size: 0.7rem; font-weight: 800; padding: 0.1rem 0.45rem; border-radius: 999px;">
                            {{ $upcomingAgendaBimbingan->count() }}
                        </span>
                    @endif
                </a>
            </div>
        </div>

        <!-- Indikator Beban Bimbingan (BR-13) -->
        <div style="background: rgba(255, 255, 255, 0.12); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 12px; padding: 1.25rem; min-width: 260px; z-index: 1;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <span style="font-size: 0.8rem; font-weight: 600; opacity: 0.9;">Beban Mentee Aktif</span>
                <span style="font-size: 0.8rem; font-weight: 700; background: rgba(255, 255, 255, 0.25); padding: 0.15rem 0.5rem; border-radius: 6px;">
                    {{ $totalMenteeAktif }} / {{ $maxBebanKapasitas }}
                </span>
            </div>
            <!-- Progress Bar Beban -->
            <div style="height: 8px; width: 100%; background: rgba(255, 255, 255, 0.2); border-radius: 999px; overflow: hidden; margin-bottom: 0.5rem;">
                <div style="height: 100%; width: {{ $persentaseKapasitas }}%; background: {{ $persentaseKapasitas >= 90 ? '#ef4444' : ($persentaseKapasitas >= 70 ? '#f59e0b' : '#10b981') }}; border-radius: 999px; transition: width 0.4s ease;"></div>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; opacity: 0.85;">
                <span>Kapasitas Terpakai: {{ $persentaseKapasitas }}%</span>
                <button type="button" onclick="switchPembimbingTab('tab-mentee')" style="background: transparent; border: none; padding: 0; color: #ffffff; text-decoration: underline; font-weight: 600; cursor: pointer;">
                    Lihat Mentee &rarr;
                </button>
            </div>
        </div>
    </div>

    <!-- 3. Gentelella Top Tile Count (4 Metrik Utama Pembimbing) -->
    <div class="row tile_count">
        <!-- Tile 1: Beban Mentee Aktif -->
        <x-gentelella.tile-stat
            title="Beban Mentee Aktif"
            value="{{ $totalMenteeAktif }} / {{ $maxBebanKapasitas }}"
            icon="users"
            trendText="{{ $persentaseKapasitas }}% Kapasitas"
            trendType="{{ $persentaseKapasitas >= 90 ? 'danger' : ($persentaseKapasitas >= 70 ? 'warning' : 'success') }}"
            subText="Beban aktif / batas kapasitas dinas"
            col="col-md-3 col-sm-6"
        />

        <!-- Tile 2: Reviu Logbook -->
        <x-gentelella.tile-stat
            title="Reviu Logbook"
            value="{{ $logbookPendingCount }}"
            icon="book-open"
            trendText="{{ $logbookPendingCount > 0 ? $logbookPendingCount . ' Perlu Tindak Lanjut' : 'Tuntas' }}"
            trendType="{{ $logbookPendingCount > 0 ? 'warning' : 'success' }}"
            subText="Logbook terkirim menunggu konfirmasi"
            col="col-md-3 col-sm-6"
        />

        <!-- Tile 3: Progres Tugas -->
        <x-gentelella.tile-stat
            title="Progres Tugas"
            value="{{ $progresPendingCount }}"
            icon="clipboard-check"
            trendText="{{ $progresPendingCount > 0 ? $progresPendingCount . ' Perlu Evaluasi' : 'Tuntas' }}"
            trendType="{{ $progresPendingCount > 0 ? 'warning' : 'success' }}"
            subText="Laporan progres menunggu evaluasi"
            col="col-md-3 col-sm-6"
        />

        <!-- Tile 4: Tugas Berjalan -->
        <x-gentelella.tile-stat
            title="Tugas Berjalan"
            value="{{ $tugasAktifCount }}"
            icon="activity"
            trendText="{{ $tugasOverdueCount > 0 ? $tugasOverdueCount . ' Overdue' : 'Jadwal terkendali' }}"
            trendType="{{ $tugasOverdueCount > 0 ? 'danger' : 'success' }}"
            subText="{{ $persenTugasKumulatif }}% tuntas &bull; {{ $tugasSelesaiKumulatif }}/{{ $totalTugasKumulatif }} tugas"
            col="col-md-3 col-sm-6"
        >
            <div class="micro-progress-bar" style="margin-top: 6px;">
                <div class="micro-progress-fill" style="width: {{ $persenTugasKumulatif }}%;"></div>
            </div>
        </x-gentelella.tile-stat>
    </div>

    <!-- 4. Komposisi Jenjang Pendidikan Mentee Aktif -->
    <div style="background: #ffffff; border-radius: 12px; border: 1px solid var(--neutral-200, #E2E8F0); padding: 0.85rem 1.25rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; box-shadow: 0 1px 2px rgba(0,0,0,0.03);">
        <div style="display: flex; align-items: center; gap: 0.6rem;">
            <span style="font-size: 0.8rem; font-weight: 700; color: var(--neutral-700, #334155); text-transform: uppercase; letter-spacing: 0.025em;">
                Komposisi Jenjang Pendidikan:
            </span>
            <span style="font-size: 0.8rem; color: var(--neutral-500, #64748B);">
                Total {{ $totalMenteeAktif }} peserta aktif bimbingan
            </span>
        </div>
        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            <div style="display: inline-flex; align-items: center; gap: 0.4rem; background: #eff6ff; color: #1e40af; padding: 0.3rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600;">
                <span style="width: 8px; height: 8px; border-radius: 50%; background: #2563eb;"></span>
                SMK: {{ $jenjangCounts['smk'] ?? 0 }} Siswa
            </div>
            <div style="display: inline-flex; align-items: center; gap: 0.4rem; background: #f5f3ff; color: #5b21b6; padding: 0.3rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600;">
                <span style="width: 8px; height: 8px; border-radius: 50%; background: #7c3aed;"></span>
                D3: {{ $jenjangCounts['d3'] ?? 0 }} Mahasiswa
            </div>
            <div style="display: inline-flex; align-items: center; gap: 0.4rem; background: #ecfdf5; color: #065f46; padding: 0.3rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600;">
                <span style="width: 8px; height: 8px; border-radius: 50%; background: #059669;"></span>
                S1: {{ $jenjangCounts['s1'] ?? 0 }} Mahasiswa
            </div>
        </div>
    </div>

    <!-- 5. Segmented Multi-Tab Controller -->
    <div class="pembimbing-tab-bar">
        <button type="button" class="pembimbing-tab-btn active" id="btn-tab-ringkasan" onclick="switchPembimbingTab('tab-ringkasan')">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7"></rect>
                <rect x="14" y="3" width="7" height="7"></rect>
                <rect x="14" y="14" width="7" height="7"></rect>
                <rect x="3" y="14" width="7" height="7"></rect>
            </svg>
            1. Ringkasan Utama
        </button>

        <button type="button" class="pembimbing-tab-btn" id="btn-tab-antrean" onclick="switchPembimbingTab('tab-antrean')">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
            </svg>
            2. Pusat Antrean Reviu
            @if($logbookPendingCount + $progresPendingCount > 0)
                <span class="tab-badge badge-amber">{{ $logbookPendingCount + $progresPendingCount }}</span>
            @endif
        </button>

        <button type="button" class="pembimbing-tab-btn" id="btn-tab-mentee" onclick="switchPembimbingTab('tab-mentee')">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
            3. Monitoring Mentee 360°
            <span class="tab-badge badge-blue">{{ $totalMenteeAktif }}</span>
        </button>

        <button type="button" class="pembimbing-tab-btn" id="btn-tab-evaluasi" onclick="switchPembimbingTab('tab-evaluasi')">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
            </svg>
            4. Evaluasi Rubrik & Kelulusan
        </button>

        <button type="button" class="pembimbing-tab-btn" id="btn-tab-agenda" onclick="switchPembimbingTab('tab-agenda')">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            5. Agenda & Radar Capaian
            @if($upcomingAgendaBimbingan->count() > 0)
                <span class="tab-badge badge-blue">{{ $upcomingAgendaBimbingan->count() }}</span>
            @endif
        </button>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 1: RINGKASAN UTAMA                                                    -->
    <!-- ========================================================================= -->
    <div class="tab-pane active" id="tab-ringkasan">

        <!-- Peringatan Kedisiplinan Logbook (BR-09 Compliance Alert) -->
        @if($inactiveLogbookMentees->count() > 0)
            <div style="background: #fff7ed; border: 1px solid #fed7aa; border-left: 4px solid #ea580c; border-radius: 12px; padding: 1.25rem 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ea580c" stroke-width="2">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                        <h3 style="font-size: 0.95rem; font-weight: 700; color: #9a3412; margin: 0;">
                            Peringatan: {{ $inactiveLogbookMentees->count() }} Mentee Belum Mengisi Logbook &ge; 2 Hari Kerja
                        </h3>
                    </div>
                    <span style="font-size: 0.75rem; color: #c2410c;">Maksimal pengisian mundur adalah 3 hari (BR-09). Segera ingatkan mentee sebelum entri logbook hangus.</span>
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 0.85rem;">
                    @foreach($inactiveLogbookMentees as $item)
                        @php
                            $m = $item['mentee'];
                        @endphp
                        <div style="background: #ffffff; border-radius: 10px; border: 1px solid #ffedd5; padding: 0.75rem 1rem; display: flex; justify-content: space-between; align-items: center; gap: 0.75rem;">
                            <div>
                                <div style="font-size: 0.875rem; font-weight: 600; color: var(--neutral-900, #0F172A);">
                                    {{ $m->peserta?->nama }}
                                </div>
                                <div style="font-size: 0.75rem; color: var(--neutral-600, #475569); margin-top: 0.1rem;">
                                    {{ $m->peserta?->profil?->institusi ?? '-' }}
                                </div>
                                <div style="font-size: 0.7rem; color: #ea580c; font-weight: 600; margin-top: 0.25rem;">
                                    Tidak mengisi: {{ $item['hari_pasif'] }} hari &bull; Terakhir: {{ $item['terakhir_isi'] }}
                                </div>
                            </div>
                            <a href="{{ route('pembimbing.peserta.show', $m->id) }}" style="padding: 0.35rem 0.65rem; background: #ea580c; color: #ffffff; font-size: 0.75rem; font-weight: 600; border-radius: 6px; text-decoration: none; white-space: nowrap;">
                                Cek Profil
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Panel 1: Komponen Kartu Tugas Terpadu dalam Gaya Gentelella x_panel -->
        <x-gentelella.x-panel
            title="Tenggat Waktu Tugas Mendekati Batas Akhir & Penugasan Mentee"
            subtitle="Pemantauan penugasan aktif, tenggat waktu (&le; {{ $deadlineMendekatiHari }} hari), dan antrean evaluasi progres mentee."
        >
            <x-slot:actions>
                <div class="task-quick-filters">
                    <button type="button" class="task-filter-btn active" onclick="filterDashboardTasks('semua', this)">
                        Semua <span class="filter-badge">{{ $tugasPrioritasPembimbing->count() }}</span>
                    </button>
                    <button type="button" class="task-filter-btn" onclick="filterDashboardTasks('mendesak', this)">
                        Mendesak <span class="filter-badge">{{ $tugasPrioritasPembimbing->filter(fn($t) => $t->is_overdue || $t->is_due_soon)->count() }}</span>
                    </button>
                    <button type="button" class="task-filter-btn" onclick="filterDashboardTasks('reviu', this)">
                        Perlu Reviu <span class="filter-badge">{{ $tugasPrioritasPembimbing->where('pending_progres_count', '>', 0)->count() }}</span>
                    </button>
                    <a href="{{ route('pembimbing.tugas.index') }}" style="font-size: 0.8125rem; font-weight: 600; color: #0641CC; text-decoration: none; margin-left: 8px;">
                        Lihat Seluruh Tugas &rarr;
                    </a>
                </div>
            </x-slot:actions>

            <!-- Daftar Item Tugas -->
            @if($tugasPrioritasPembimbing->isNotEmpty())
                <div id="dashboard-tasks-container">
                    @foreach($tugasPrioritasPembimbing as $tugas)
                        @php
                            $diff = $tugas->diff_days;
                            $hasPendingReview = $tugas->pending_progres_count > 0;
                            $isUrgent = $tugas->is_overdue || ($tugas->deadline && $diff <= 3);
                            $menteeName = $tugas->penempatan?->peserta?->nama ?? 'Peserta Magang';
                            $initials = strtoupper(substr($menteeName, 0, 2));
                        @endphp
                        <div class="task-item-row" data-is-urgent="{{ $isUrgent ? '1' : '0' }}" data-has-pending="{{ $hasPendingReview ? '1' : '0' }}">
                            <div class="task-item-left">
                                <div class="mentee-avatar-chip" title="Mentee: {{ $menteeName }}">
                                    {{ $initials }}
                                </div>
                                <div class="task-item-meta">
                                    <h4>
                                        <a href="{{ route('pembimbing.tugas.show', $tugas->id) }}">
                                            {{ $tugas->judul }}
                                        </a>
                                    </h4>
                                    <div class="task-mentee-info">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                        <span>Mentee: <strong>{{ $menteeName }}</strong></span>
                                        <span>&bull;</span>
                                        <span>{{ $tugas->penempatan?->peserta?->profil?->institusi ?? 'Institusi Belum Terisi' }}</span>
                                    </div>
                                    <div class="task-badges-row">
                                        <!-- Deadline Pill Standar Peserta Magang -->
                                        @if($tugas->is_overdue)
                                            <span class="deadline-pill is-urgent">
                                                <span>⚠️</span> Lewat Tenggat
                                            </span>
                                        @elseif($tugas->is_due_today)
                                            <span class="deadline-pill is-urgent">
                                                <span>⚡</span> Tenggat Hari Ini!
                                            </span>
                                        @elseif($tugas->is_due_tomorrow)
                                            <span class="deadline-pill is-urgent">
                                                <span>⚠️</span> Besok Jatuh Tempo
                                            </span>
                                        @elseif($tugas->is_due_soon)
                                            <span class="deadline-pill is-warning">
                                                <span>⏳</span> Sisa {{ $diff }} Hari
                                            </span>
                                        @elseif($tugas->deadline)
                                            <span class="deadline-pill">
                                                <span>📅</span> {{ \Carbon\Carbon::parse($tugas->deadline)->isoFormat('D MMM Y') }}
                                            </span>
                                        @else
                                            <span class="deadline-pill">Tanpa Tenggat</span>
                                        @endif

                                        <!-- Category Chip -->
                                        <span class="task-category-chip">
                                            {{ $tugas->kategori ?? 'Tugas Lapangan' }}
                                        </span>

                                        <!-- Pending Review Highlight Badge -->
                                        @if($hasPendingReview)
                                            <span style="font-size: 0.7rem; font-weight: 700; background: #EEF2FF; color: #4338CA; border: 1px solid #C7D2FE; padding: 2px 8px; border-radius: 999px; display: inline-flex; align-items: center; gap: 4px;">
                                                <span>📥</span> {{ $tugas->pending_progres_count }} Laporan Progres Menunggu Evaluasi
                                            </span>
                                        @endif

                                        <!-- Attachment / Reference Links -->
                                        @if($tugas->file_lampiran)
                                            @php $badge = $tugas->badge_color_lampiran; @endphp
                                            <a href="{{ route('pembimbing.tugas.lampiran', $tugas->id) }}" title="Unduh Berkas Panduan: {{ $tugas->nama_file_lampiran }}" style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.7rem; font-weight: 600; padding: 0.15rem 0.45rem; border-radius: 4px; background: {{ $badge['bg'] }}; color: {{ $badge['color'] }}; text-decoration: none; border: 1px solid {{ $badge['border'] }};">
                                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                                {{ $badge['label'] }} ({{ $tugas->ukuran_format }})
                                            </a>
                                        @endif
                                        @if($tugas->link_referensi)
                                            <a href="{{ $tugas->link_referensi }}" target="_blank" rel="noopener noreferrer" title="Buka Tautan Acuan: {{ $tugas->link_referensi }}" style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.7rem; font-weight: 600; padding: 0.15rem 0.45rem; border-radius: 4px; background: #eff6ff; color: #1d4ed8; text-decoration: none; border: 1px solid #bfdbfe;">
                                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                                                Tautan Acuan ↗
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Tombol Aksi Cepat Kontekstual -->
                            <div>
                                @if($hasPendingReview)
                                    <a href="{{ route('pembimbing.tugas.show', $tugas->id) }}" class="btn-task-action btn-task-action-primary" style="box-shadow: 0 4px 12px rgba(6, 65, 204, 0.22);">
                                        <span>Evaluasi Progres</span>
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                    </a>
                                @else
                                    <a href="{{ route('pembimbing.tugas.show', $tugas->id) }}" class="btn-task-action btn-task-action-secondary">
                                        <span>Reviu & Detail</span>
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Empty state untuk filter client-side jika tidak ada item yang cocok -->
                <div id="task-filter-empty-state" style="display: none; text-align: center; padding: 2rem 1rem; color: var(--neutral-500, #64748B);">
                    <p style="font-size: 0.875rem; font-weight: 600; color: var(--neutral-700, #334155); margin: 0 0 4px 0;">Tidak ada tugas pada kategori filter ini.</p>
                    <p style="font-size: 0.775rem; color: var(--neutral-500, #64748B); margin: 0;">Silakan pilih filter "Semua" untuk melihat seluruh penugasan mentee.</p>
                </div>
            @else
                <div style="text-align: center; padding: 3rem 1.5rem; color: var(--neutral-500, #64748B);">
                    <svg width="44" height="44" fill="none" stroke="#10b981" stroke-width="1.5" viewBox="0 0 24 24" style="margin: 0 auto 12px auto;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--neutral-900, #0F172A); margin: 0 0 4px 0;">Semua tugas mentee terkendali!</h4>
                    <p style="font-size: 0.8125rem; color: var(--neutral-500, #64748B); margin: 0 0 1rem 0;">Tidak ada penugasan yang terlambat atau laporan progres yang tertunda saat ini.</p>
                    <a href="{{ route('pembimbing.tugas.create') }}" style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.5rem 1.15rem; background: #0641CC; color: #ffffff; border-radius: 8px; font-size: 0.8125rem; font-weight: 600; text-decoration: none; box-shadow: 0 2px 6px rgba(6, 65, 204, 0.2);">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        + Berikan Tugas Baru
                    </a>
                </div>
            @endif
        </x-gentelella.x-panel>

        <!-- Panel 2: Aktivitas Mentoring Minggu Ini (B11) -->
        <x-gentelella.x-panel
            title="Aktivitas Mentoring Minggu Ini"
            subtitle="Logbook, progres tugas, dan sesi bimbingan lapangan terbaru"
        >
            @if(!empty($aktivitasMingguIni) && $aktivitasMingguIni->isNotEmpty())
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    @foreach($aktivitasMingguIni as $act)
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 1rem; border-radius: 8px; background: #F8FAFC; border: 1px solid var(--neutral-200, #E2E8F0); gap: 1rem; flex-wrap: wrap;">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <span style="font-size: 0.7rem; font-weight: 700; padding: 0.2rem 0.55rem; border-radius: 6px; background: {{ $act['badge_bg'] }}; color: {{ $act['badge_color'] }}; text-transform: uppercase;">
                                    {{ $act['badge'] }}
                                </span>
                                <div>
                                    <div style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-900, #0F172A);">
                                        {{ $act['judul'] }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--neutral-600, #475569); margin-top: 0.1rem;">
                                        {{ $act['keterangan'] }}
                                    </div>
                                </div>
                            </div>
                            <span style="font-size: 0.75rem; color: var(--neutral-500, #64748B); font-weight: 500;">
                                {{ \Carbon\Carbon::parse($act['waktu'])->diffForHumans() }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 1.5rem; color: var(--neutral-500, #64748B); font-size: 0.8125rem;">
                    Belum ada aktivitas baru dari peserta bimbingan minggu ini.
                </div>
            @endif
        </x-gentelella.x-panel>

        <!-- Pelacak Peserta Mendekati Batas Akhir Magang (Off-boarding Tracker <= 14 Hari) -->
        @if($offboardingMentees->count() > 0)
            <div style="background: #fffbeb; border: 1px solid #fef3c7; border-left: 4px solid #f59e0b; border-radius: 12px; padding: 1.25rem 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <h3 style="font-size: 0.95rem; font-weight: 700; color: #92400e; margin: 0;">
                            Perhatian: {{ $offboardingMentees->count() }} Mentee Mendekati Batas Selesai Magang (&le; 14 Hari)
                        </h3>
                    </div>
                    <span style="font-size: 0.75rem; color: #b45309;">Sesuai regulasi, segera susun penilaian rubrik final sebelum periode berakhir (BR-10, BR-11).</span>
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1rem;">
                    @foreach($offboardingMentees as $mentee)
                        @php
                            $sisaHari = \Carbon\Carbon::today()->diffInDays($mentee->tgl_selesai, false);
                            $statusNilai = $mentee->penilaian?->status->value ?? 'belum';
                        @endphp
                        <div style="background: #ffffff; border-radius: 10px; border: 1px solid #fde68a; padding: 0.875rem 1rem; display: flex; justify-content: space-between; align-items: center; gap: 0.75rem;">
                            <div>
                                <div style="font-size: 0.875rem; font-weight: 600; color: var(--neutral-900, #0F172A);">
                                    {{ $mentee->peserta?->nama }}
                                </div>
                                <div style="font-size: 0.75rem; color: var(--neutral-600, #475569); margin-top: 0.15rem;">
                                    {{ $mentee->peserta?->profil?->institusi ?? 'Institusi Belum Diisi' }}
                                </div>
                                <div style="display: flex; gap: 0.5rem; align-items: center; margin-top: 0.35rem;">
                                    <span style="font-size: 0.7rem; font-weight: 600; padding: 0.1rem 0.4rem; border-radius: 4px; background: {{ $sisaHari <= 3 ? '#fee2e2' : '#fef3c7' }}; color: {{ $sisaHari <= 3 ? '#b91c1c' : '#92400e' }};">
                                        {{ $sisaHari < 0 ? 'Berakhir ' . abs($sisaHari) . ' hari lalu' : ($sisaHari == 0 ? 'Berakhir Hari Ini' : 'Tersisa ' . $sisaHari . ' hari') }}
                                    </span>
                                    <span style="font-size: 0.7rem; color: var(--neutral-500, #64748B);">
                                        Nilai: <strong>{{ ucfirst($statusNilai) }}</strong>
                                    </span>
                                </div>
                            </div>
                            <a href="{{ route('pembimbing.penilaian.show', $mentee->id) }}" style="padding: 0.4rem 0.75rem; background: #0641CC; color: #ffffff; font-size: 0.75rem; font-weight: 600; border-radius: 6px; text-decoration: none; text-align: center; white-space: nowrap;">
                                Lembar Nilai
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Row 2 Kolom Antrean Mendesak -->
        <div class="row">
            <!-- Kolom Kiri: Antrean Progres Tugas -->
            <div class="col-md-6 col-sm-12">
                <x-gentelella.x-panel
                    title="Antrean Progres Tugas ({{ $pendingProgresLogs->count() }})"
                    subtitle="Laporan kemajuan tugas menunggu umpan balik pembimbing"
                >
                    <x-slot:actions>
                        <button type="button" onclick="switchPembimbingTab('tab-antrean')" style="background: transparent; border: none; font-size: 0.8rem; font-weight: 600; color: #0641CC; cursor: pointer;">
                            Buka Semua &rarr;
                        </button>
                    </x-slot:actions>

                    @forelse($pendingProgresLogs as $progres)
                        <div style="padding: 0.875rem 0; border-bottom: 1px solid var(--neutral-100, #F1F5F9); display: flex; justify-content: space-between; align-items: center; gap: 1rem;">
                            <div style="display: flex; flex-direction: column; gap: 0.2rem; flex: 1;">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <span style="font-size: 0.875rem; font-weight: 600; color: var(--neutral-900, #0F172A);">
                                        {{ $progres->tugas?->penempatan?->peserta?->nama ?? 'Peserta' }}
                                    </span>
                                    <span style="font-size: 0.7rem; background: #e0e7ff; color: #3730a3; padding: 0.1rem 0.45rem; border-radius: 4px; font-weight: 600;">
                                        {{ $progres->tugas?->kategori ?? 'Tugas' }}
                                    </span>
                                </div>
                                <span style="font-size: 0.8rem; color: var(--neutral-700, #334155); font-weight: 500;">
                                    {{ $progres->tugas?->judul }}
                                </span>
                                <span style="font-size: 0.75rem; color: var(--neutral-500, #64748B); line-height: 1.4;">
                                    {{ \Illuminate\Support\Str::limit($progres->catatan ?? $progres->deskripsi ?? 'Laporan progres kerja', 90) }}
                                </span>
                                <span style="font-size: 0.7rem; color: var(--neutral-400, #94A3B8);">
                                    Diajukan {{ $progres->created_at?->diffForHumans() }}
                                </span>
                            </div>
                            <a href="{{ route('pembimbing.tugas.show', $progres->tugas_id) }}" style="padding: 0.45rem 0.85rem; background: rgba(79, 70, 229, 0.1); color: #4f46e5; font-size: 0.75rem; font-weight: 600; border-radius: 6px; text-decoration: none; white-space: nowrap;">
                                Evaluasi
                            </a>
                        </div>
                    @empty
                        <div style="padding: 2.5rem 1rem; text-align: center; color: var(--neutral-500, #64748B); display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="1.5">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                            <span style="font-size: 0.875rem; font-weight: 600; color: var(--neutral-700, #334155);">Tidak Ada Antrean Progres</span>
                            <span style="font-size: 0.75rem;">Seluruh laporan progres tugas dari mentee telah dievaluasi.</span>
                        </div>
                    @endforelse
                </x-gentelella.x-panel>
            </div>

            <!-- Kolom Kanan: Antrean Reviu Logbook -->
            <div class="col-md-6 col-sm-12">
                <x-gentelella.x-panel
                    title="Antrean Reviu Logbook ({{ $pendingLogbooks->count() }})"
                    subtitle="Logbook harian menunggu validasi pembimbing"
                >
                    <x-slot:actions>
                        <button type="button" onclick="switchPembimbingTab('tab-antrean')" style="background: transparent; border: none; font-size: 0.8rem; font-weight: 600; color: #0641CC; cursor: pointer;">
                            Buka Semua &rarr;
                        </button>
                    </x-slot:actions>

                    @forelse($pendingLogbooks as $logbook)
                        <div style="padding: 0.875rem 0; border-bottom: 1px solid var(--neutral-100, #F1F5F9); display: flex; justify-content: space-between; align-items: center; gap: 1rem;">
                            <div style="display: flex; flex-direction: column; gap: 0.2rem; flex: 1;">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <span style="font-size: 0.875rem; font-weight: 600; color: var(--neutral-900, #0F172A);">
                                        {{ $logbook->penempatan?->peserta?->nama ?? 'Peserta' }}
                                    </span>
                                    <span style="font-size: 0.7rem; background: #fef3c7; color: #92400e; padding: 0.1rem 0.45rem; border-radius: 4px; font-weight: 600;">
                                        {{ $logbook->tanggal?->format('d M Y') }}
                                    </span>
                                </div>
                                <span style="font-size: 0.8rem; color: var(--neutral-700, #334155); line-height: 1.4;">
                                    {{ \Illuminate\Support\Str::limit($logbook->kegiatan, 90) }}
                                </span>
                                <div style="display: flex; align-items: center; gap: 0.75rem; margin-top: 0.15rem;">
                                    @if($logbook->link_laporan)
                                        <span style="font-size: 0.7rem; color: #0641CC; display: inline-flex; align-items: center; gap: 0.2rem;">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                                            Tautan Lampir
                                        </span>
                                    @endif
                                    @if($logbook->file_laporan)
                                        <span style="font-size: 0.7rem; color: #059669; display: inline-flex; align-items: center; gap: 0.2rem;">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                            Berkas PDF
                                        </span>
                                    @endif
                                    <span style="font-size: 0.7rem; color: var(--neutral-400, #94A3B8);">
                                        Masuk {{ $logbook->created_at?->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                            <a href="{{ route('pembimbing.logbook.index', ['penempatan_id' => $logbook->penempatan_id, 'tanggal' => $logbook->tanggal?->toDateString()]) }}" style="padding: 0.45rem 0.85rem; background: rgba(217, 119, 6, 0.1); color: #d97706; font-size: 0.75rem; font-weight: 600; border-radius: 6px; text-decoration: none; white-space: nowrap;">
                                Periksa
                            </a>
                        </div>
                    @empty
                        <div style="padding: 2.5rem 1rem; text-align: center; color: var(--neutral-500, #64748B); display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="1.5">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                            <span style="font-size: 0.875rem; font-weight: 600; color: var(--neutral-700, #334155);">Tidak Ada Antrean Logbook</span>
                            <span style="font-size: 0.75rem;">Semua entri logbook harian peserta bimbingan telah direviu.</span>
                        </div>
                    @endforelse
                </x-gentelella.x-panel>
            </div>
        </div>

        <!-- Panel 3: Ringkasan Cepat Mentee Bimbingan Aktif -->
        <x-gentelella.x-panel
            title="Daftar Mentee Bimbingan Aktif (Ringkasan)"
            subtitle="Total {{ $totalMenteeAktif }} peserta sedang aktif menjalani magang di bawah bimbingan Anda."
        >
            <x-slot:actions>
                <button type="button" onclick="switchPembimbingTab('tab-mentee')" style="background: transparent; border: none; font-size: 0.8rem; font-weight: 600; color: #0641CC; cursor: pointer;">
                    Buka Monitoring Lengkap 360° &rarr;
                </button>
            </x-slot:actions>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--neutral-200, #E2E8F0); background: #f8fafc; color: var(--neutral-600, #475569); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <th style="padding: 0.875rem 1.5rem;">Peserta Magang</th>
                            <th style="padding: 0.875rem 1rem;">Institusi & Jurusan</th>
                            <th style="padding: 0.875rem 1rem;">Periode Magang</th>
                            <th style="padding: 0.875rem 1rem;">Progres Durasi</th>
                            <th style="padding: 0.875rem 1rem;">Status Nilai</th>
                            <th style="padding: 0.875rem 1.5rem; text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($menteeAktifList as $mentee)
                            @php
                                $tglMulai = $mentee->tgl_mulai;
                                $tglSelesai = $mentee->tgl_selesai;
                                $totalHari = $tglMulai && $tglSelesai ? max(1, $tglMulai->diffInDays($tglSelesai)) : 1;
                                $hariBerjalan = $tglMulai ? max(0, \Carbon\Carbon::today()->diffInDays($tglMulai, false)) : 0;
                                $persenHari = min(100, (int) round(($hariBerjalan / $totalHari) * 100));
                                $statusNilai = $mentee->penilaian?->status->value ?? 'belum';
                            @endphp
                            <tr style="border-bottom: 1px solid var(--neutral-100, #F1F5F9); transition: background 0.15s ease;">
                                <td style="padding: 1rem 1.5rem;">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div style="width: 36px; height: 36px; border-radius: 50%; background: #e0e7ff; color: #3730a3; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.875rem;">
                                            {{ strtoupper(substr($mentee->peserta?->nama ?? 'P', 0, 2)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight: 600; color: var(--neutral-900, #0F172A);">
                                                {{ $mentee->peserta?->nama }}
                                            </div>
                                            <div style="font-size: 0.75rem; color: var(--neutral-500, #64748B);">
                                                {{ $mentee->peserta?->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 1rem 1rem;">
                                    <div style="font-weight: 500; color: var(--neutral-800, #1E293B);">
                                        {{ $mentee->peserta?->profil?->institusi ?? '-' }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--neutral-500, #64748B);">
                                        {{ $mentee->peserta?->profil?->jurusan?->nama_jurusan ?? '-' }}
                                    </div>
                                </td>
                                <td style="padding: 1rem 1rem; font-size: 0.8rem; color: var(--neutral-700, #334155); white-space: nowrap;">
                                    {{ $tglMulai?->format('d M Y') }} s/d {{ $tglSelesai?->format('d M Y') }}
                                </td>
                                <td style="padding: 1rem 1rem; min-width: 140px;">
                                    <div style="display: flex; justify-content: space-between; font-size: 0.7rem; color: var(--neutral-500, #64748B); margin-bottom: 0.25rem;">
                                        <span>Hari ke-{{ $hariBerjalan }}</span>
                                        <span>{{ $persenHari }}%</span>
                                    </div>
                                    <div style="height: 6px; width: 100%; background: var(--neutral-200, #E2E8F0); border-radius: 999px; overflow: hidden;">
                                        <div style="height: 100%; width: {{ $persenHari }}%; background: #0641CC; border-radius: 999px;"></div>
                                    </div>
                                </td>
                                <td style="padding: 1rem 1rem;">
                                    <span style="font-size: 0.75rem; font-weight: 600; padding: 0.2rem 0.6rem; border-radius: 999px; background: {{ $statusNilai === 'final' ? '#dcfce7' : ($statusNilai === 'draft' ? '#fef3c7' : '#f1f5f9') }}; color: {{ $statusNilai === 'final' ? '#166534' : ($statusNilai === 'draft' ? '#92400e' : '#475569') }};">
                                        {{ ucfirst($statusNilai) }}
                                    </span>
                                </td>
                                <td style="padding: 1rem 1.5rem; text-align: right; white-space: nowrap;">
                                    <div style="display: flex; gap: 0.4rem; justify-content: flex-end;">
                                        <a href="{{ route('pembimbing.peserta.show', $mentee->id) }}" style="padding: 0.35rem 0.65rem; background: rgba(6, 65, 204, 0.08); color: #0641CC; font-size: 0.75rem; font-weight: 600; border-radius: 6px; text-decoration: none;">
                                            Profil 360°
                                        </a>
                                        <a href="{{ route('pembimbing.penilaian.show', $mentee->id) }}" style="padding: 0.35rem 0.65rem; background: #0641CC; color: #ffffff; font-size: 0.75rem; font-weight: 600; border-radius: 6px; text-decoration: none;">
                                            Nilai
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding: 2rem; text-align: center; color: var(--neutral-500, #64748B);">
                                    Belum ada peserta magang yang aktif dibimbing saat ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-gentelella.x-panel>

    </div>

    <!-- ========================================================================= -->
    <!-- TAB 2: PUSAT ANTREAN REVIU CEPAT (Action Hub)                             -->
    <!-- ========================================================================= -->
    <div class="tab-pane" id="tab-antrean">

        <!-- Antrean Seluruh Logbook Harian Terkirim -->
        <x-gentelella.x-panel
            title="Seluruh Antrean Logbook Menunggu Reviu ({{ $allPendingLogbooks->count() }})"
            subtitle="Logbook harian terkirim dari mentee yang belum diberikan catatan/konfirmasi pembimbing."
        >
            <x-slot:actions>
                <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                    <input type="text" onkeyup="filterTableByInput(this, 'table-antrean-logbook')" placeholder="Cari nama atau kegiatan..." style="padding: 0.45rem 0.85rem; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; font-size: 0.8rem; outline: none; min-width: 200px;">
                    <a href="{{ route('pembimbing.logbook.index', ['status' => 'terkirim']) }}" style="font-size: 0.8rem; font-weight: 600; color: #0641CC; text-decoration: none;">
                        Kelola di Modul Logbook &rarr;
                    </a>
                </div>
            </x-slot:actions>

            <div style="overflow-x: auto;">
                <table id="table-antrean-logbook" style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--neutral-200, #E2E8F0); background: #f8fafc; color: var(--neutral-600, #475569); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <th style="padding: 0.875rem 1.5rem;">Mentee</th>
                            <th style="padding: 0.875rem 1rem;">Tanggal Logbook</th>
                            <th style="padding: 0.875rem 1rem;">Kegiatan / Aktivitas Kerja</th>
                            <th style="padding: 0.875rem 1rem;">Lampiran</th>
                            <th style="padding: 0.875rem 1rem;">Diajukan</th>
                            <th style="padding: 0.875rem 1.5rem; text-align: right;">Aksi Reviu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allPendingLogbooks as $logbook)
                            <tr style="border-bottom: 1px solid var(--neutral-100, #F1F5F9);">
                                <td style="padding: 1rem 1.5rem;">
                                    <div style="font-weight: 600; color: var(--neutral-900, #0F172A);">
                                        {{ $logbook->penempatan?->peserta?->nama }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--neutral-500, #64748B);">
                                        {{ $logbook->penempatan?->peserta?->profil?->institusi ?? '-' }}
                                    </div>
                                </td>
                                <td style="padding: 1rem 1rem; font-size: 0.8rem; font-weight: 600; color: #92400e; white-space: nowrap;">
                                    {{ $logbook->tanggal?->format('d M Y') }}
                                </td>
                                <td style="padding: 1rem 1rem; color: var(--neutral-700, #334155); max-width: 380px;">
                                    <div style="line-height: 1.45; font-size: 0.8125rem;">
                                        {{ $logbook->kegiatan }}
                                    </div>
                                </td>
                                <td style="padding: 1rem 1rem; white-space: nowrap;">
                                    @if($logbook->link_laporan)
                                        <a href="{{ $logbook->link_laporan }}" target="_blank" rel="noopener noreferrer" style="font-size: 0.75rem; color: #0641CC; text-decoration: underline; display: block;">
                                            Tautan Lampiran &nearr;
                                        </a>
                                    @endif
                                    @if($logbook->file_laporan)
                                        <span style="font-size: 0.75rem; color: #059669; font-weight: 600; display: block;">
                                            Berkas PDF Tersedia
                                        </span>
                                    @endif
                                    @if(!$logbook->link_laporan && !$logbook->file_laporan)
                                        <span style="font-size: 0.75rem; color: var(--neutral-400, #94A3B8);">-</span>
                                    @endif
                                </td>
                                <td style="padding: 1rem 1rem; font-size: 0.75rem; color: var(--neutral-500, #64748B); white-space: nowrap;">
                                    {{ $logbook->created_at?->diffForHumans() }}
                                </td>
                                <td style="padding: 1rem 1.5rem; text-align: right; white-space: nowrap;">
                                    <a href="{{ route('pembimbing.logbook.index', ['penempatan_id' => $logbook->penempatan_id, 'tanggal' => $logbook->tanggal?->toDateString()]) }}" style="padding: 0.4rem 0.85rem; background: #0641CC; color: #ffffff; font-size: 0.75rem; font-weight: 600; border-radius: 6px; text-decoration: none;">
                                        Tanggapi Logbook
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding: 2rem; text-align: center; color: var(--neutral-500, #64748B);">
                                    Tidak ada antrean logbook harian yang menunggu reviu.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-gentelella.x-panel>

        <!-- Antrean Seluruh Progres Tugas Menunggu Evaluasi -->
        <x-gentelella.x-panel
            title="Seluruh Antrean Progres Tugas Menunggu Evaluasi ({{ $allPendingProgresLogs->count() }})"
            subtitle="Laporan progres tugas dari mentee yang membutuhkan evaluasi dan umpan balik pembimbing."
        >
            <x-slot:actions>
                <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                    <input type="text" onkeyup="filterTableByInput(this, 'table-antrean-progres')" placeholder="Cari mentee atau judul tugas..." style="padding: 0.45rem 0.85rem; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; font-size: 0.8rem; outline: none; min-width: 200px;">
                    <a href="{{ route('pembimbing.tugas.index') }}" style="font-size: 0.8rem; font-weight: 600; color: #0641CC; text-decoration: none;">
                        Kelola di Modul Tugas &rarr;
                    </a>
                </div>
            </x-slot:actions>

            <div style="overflow-x: auto;">
                <table id="table-antrean-progres" style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--neutral-200, #E2E8F0); background: #f8fafc; color: var(--neutral-600, #475569); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <th style="padding: 0.875rem 1.5rem;">Mentee</th>
                            <th style="padding: 0.875rem 1rem;">Tugas & Kategori</th>
                            <th style="padding: 0.875rem 1rem;">Laporan Kemajuan</th>
                            <th style="padding: 0.875rem 1rem;">Deadline Tugas</th>
                            <th style="padding: 0.875rem 1.5rem; text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allPendingProgresLogs as $progres)
                            <tr style="border-bottom: 1px solid var(--neutral-100, #F1F5F9);">
                                <td style="padding: 1rem 1.5rem;">
                                    <div style="font-weight: 600; color: var(--neutral-900, #0F172A);">
                                        {{ $progres->tugas?->penempatan?->peserta?->nama }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--neutral-500, #64748B);">
                                        {{ $progres->tugas?->penempatan?->peserta?->profil?->institusi ?? '-' }}
                                    </div>
                                </td>
                                <td style="padding: 1rem 1rem;">
                                    <div style="font-weight: 600; color: var(--neutral-800, #1E293B);">
                                        {{ $progres->tugas?->judul }}
                                    </div>
                                    <span style="font-size: 0.7rem; background: #e0e7ff; color: #3730a3; padding: 0.1rem 0.45rem; border-radius: 4px; font-weight: 600;">
                                        {{ $progres->tugas?->kategori ?? 'Tugas' }}
                                    </span>
                                </td>
                                <td style="padding: 1rem 1rem; color: var(--neutral-700, #334155); max-width: 380px;">
                                    <div style="line-height: 1.45; font-size: 0.8125rem;">
                                        {{ $progres->catatan ?? $progres->deskripsi ?? '-' }}
                                    </div>
                                    <span style="font-size: 0.7rem; color: var(--neutral-400, #94A3B8); display: block; margin-top: 0.2rem;">
                                        Diajukan {{ $progres->created_at?->diffForHumans() }}
                                    </span>
                                </td>
                                <td style="padding: 1rem 1rem; font-size: 0.8rem; color: var(--neutral-700, #334155); white-space: nowrap;">
                                    {{ $progres->tugas?->deadline ? \Carbon\Carbon::parse($progres->tugas->deadline)->format('d M Y') : '-' }}
                                </td>
                                <td style="padding: 1rem 1.5rem; text-align: right; white-space: nowrap;">
                                    <a href="{{ route('pembimbing.tugas.show', $progres->tugas_id) }}" style="padding: 0.4rem 0.85rem; background: #4f46e5; color: #ffffff; font-size: 0.75rem; font-weight: 600; border-radius: 6px; text-decoration: none;">
                                        Evaluasi Tugas
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="padding: 2rem; text-align: center; color: var(--neutral-500, #64748B);">
                                    Seluruh laporan progres tugas telah dievaluasi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-gentelella.x-panel>

    </div>

    <!-- ========================================================================= -->
    <!-- TAB 3: MONITORING MENTEE 360° & PROGRES MAGANG                            -->
    <!-- ========================================================================= -->
    <div class="tab-pane" id="tab-mentee">

        <x-gentelella.x-panel
            title="Direktori Pemantauan Mentee 360° ({{ $totalMenteeAktif }} Peserta Aktif)"
            subtitle="Pantau riwayat penugasan, kedisiplinan logbook harian, dan masa dinas seluruh mentee bimbingan."
        >
            <x-slot:actions>
                <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                    <input type="text" onkeyup="filterTableByInput(this, 'table-monitoring-mentee')" placeholder="Cari nama, NIM, atau kampus..." style="padding: 0.45rem 0.85rem; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; font-size: 0.8rem; outline: none; min-width: 210px;">
                    <a href="{{ route('pembimbing.peserta.index') }}" style="font-size: 0.8rem; font-weight: 600; color: #0641CC; text-decoration: none;">
                        Buka Direktori Penuh &rarr;
                    </a>
                </div>
            </x-slot:actions>

            <div style="overflow-x: auto;">
                <table id="table-monitoring-mentee" style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--neutral-200, #E2E8F0); background: #f8fafc; color: var(--neutral-600, #475569); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <th style="padding: 0.875rem 1.5rem;">Peserta</th>
                            <th style="padding: 0.875rem 1rem;">Kontak & Kampus</th>
                            <th style="padding: 0.875rem 1rem;">Timeline Dinas</th>
                            <th style="padding: 0.875rem 1rem;">Ketuntasan Tugas</th>
                            <th style="padding: 0.875rem 1rem;">Status Nilai</th>
                            <th style="padding: 0.875rem 1.5rem; text-align: right;">Aksi Cepat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($menteeAktifList as $mentee)
                            @php
                                $totalTugas = $mentee->tugas->count();
                                $selesaiTugas = $mentee->tugas->where('status', \App\Enums\StatusTugasEnum::Selesai)->count();
                                $persenTugas = $totalTugas > 0 ? (int) round(($selesaiTugas / $totalTugas) * 100) : 0;
                                $statusNilai = $mentee->penilaian?->status->value ?? 'belum';
                            @endphp
                            <tr style="border-bottom: 1px solid var(--neutral-100, #F1F5F9);">
                                <td style="padding: 1rem 1.5rem;">
                                    <div style="font-weight: 600; color: var(--neutral-900, #0F172A);">
                                        {{ $mentee->peserta?->nama }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--neutral-500, #64748B);">
                                        NIM/NISN: {{ $mentee->peserta?->profil?->nim_nisn ?? '-' }}
                                    </div>
                                </td>
                                <td style="padding: 1rem 1rem;">
                                    <div style="font-size: 0.8rem; font-weight: 500; color: var(--neutral-800, #1E293B);">
                                        {{ $mentee->peserta?->profil?->institusi ?? '-' }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--neutral-500, #64748B);">
                                        {{ $mentee->peserta?->profil?->no_hp ?? $mentee->peserta?->email }}
                                    </div>
                                </td>
                                <td style="padding: 1rem 1rem; font-size: 0.8rem; color: var(--neutral-700, #334155); white-space: nowrap;">
                                    {{ $mentee->tgl_mulai?->format('d M Y') }} s/d {{ $mentee->tgl_selesai?->format('d M Y') }}
                                </td>
                                <td style="padding: 1rem 1rem;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <div style="flex: 1; height: 6px; background: var(--neutral-200, #E2E8F0); border-radius: 999px; overflow: hidden; min-width: 70px;">
                                            <div style="height: 100%; width: {{ $persenTugas }}%; background: #10b981; border-radius: 999px;"></div>
                                        </div>
                                        <span style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-700, #334155);">
                                            {{ $selesaiTugas }}/{{ $totalTugas }} ({{ $persenTugas }}%)
                                        </span>
                                    </div>
                                </td>
                                <td style="padding: 1rem 1rem;">
                                    <span style="font-size: 0.75rem; font-weight: 600; padding: 0.2rem 0.6rem; border-radius: 999px; background: {{ $statusNilai === 'final' ? '#dcfce7' : ($statusNilai === 'draft' ? '#fef3c7' : '#f1f5f9') }}; color: {{ $statusNilai === 'final' ? '#166534' : ($statusNilai === 'draft' ? '#92400e' : '#475569') }};">
                                        {{ ucfirst($statusNilai) }}
                                    </span>
                                </td>
                                <td style="padding: 1rem 1.5rem; text-align: right; white-space: nowrap;">
                                    <div style="display: flex; gap: 0.4rem; justify-content: flex-end;">
                                        <a href="{{ route('pembimbing.peserta.show', $mentee->id) }}" style="padding: 0.35rem 0.65rem; background: rgba(6, 65, 204, 0.08); color: #0641CC; font-size: 0.75rem; font-weight: 600; border-radius: 6px; text-decoration: none;">
                                            Profil 360°
                                        </a>
                                        <a href="{{ route('pembimbing.tugas.index') }}" style="padding: 0.35rem 0.65rem; background: rgba(16, 185, 129, 0.1); color: #10b981; font-size: 0.75rem; font-weight: 600; border-radius: 6px; text-decoration: none;">
                                            Beri Tugas
                                        </a>
                                        <a href="{{ route('pembimbing.penilaian.show', $mentee->id) }}" style="padding: 0.35rem 0.65rem; background: #0641CC; color: #ffffff; font-size: 0.75rem; font-weight: 600; border-radius: 6px; text-decoration: none;">
                                            Nilai
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding: 2rem; text-align: center; color: var(--neutral-500, #64748B);">
                                    Belum ada peserta magang aktif.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-gentelella.x-panel>

    </div>

    <!-- ========================================================================= -->
    <!-- TAB 4: EVALUASI RUBRIK & CAPAIAN MAGANG (Kelulusan)                       -->
    <!-- ========================================================================= -->
    <div class="tab-pane" id="tab-evaluasi">

        <!-- Matriks Status Penilaian Rubrik Seluruh Penempatan -->
        <x-gentelella.x-panel
            title="Matriks Evaluasi Rubrik & Penetapan Selesai ({{ $allPenempatanList->count() }} Mentee)"
            subtitle="Pemberian nilai rubrik kompetensi, predikat huruf (A–E), dan pemantauan masa tenggang akun (BR-10, BR-11)."
        >
            <x-slot:actions>
                <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                    <input type="text" onkeyup="filterTableByInput(this, 'table-evaluasi-penilaian')" placeholder="Cari nama mentee..." style="padding: 0.45rem 0.85rem; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; font-size: 0.8rem; outline: none; min-width: 190px;">
                    <a href="{{ route('pembimbing.penilaian.index') }}" style="font-size: 0.8rem; font-weight: 600; color: #0641CC; text-decoration: none;">
                        Buka Modul Penilaian Penuh &rarr;
                    </a>
                </div>
            </x-slot:actions>

            <div style="overflow-x: auto;">
                <table id="table-evaluasi-penilaian" style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--neutral-200, #E2E8F0); background: #f8fafc; color: var(--neutral-600, #475569); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <th style="padding: 0.875rem 1.5rem;">Peserta Magang</th>
                            <th style="padding: 0.875rem 1rem;">Status Magang</th>
                            <th style="padding: 0.875rem 1rem;">Status Nilai</th>
                            <th style="padding: 0.875rem 1rem;">Skor & Predikat</th>
                            <th style="padding: 0.875rem 1rem;">Masa Tenggang Akun</th>
                            <th style="padding: 0.875rem 1.5rem; text-align: right;">Lembar Nilai PDF</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allPenempatanList as $penempatan)
                            @php
                                $penilaian = $penempatan->penilaian;
                                $statusNilai = $penilaian?->status->value ?? 'belum';
                                $isSelesai = $penempatan->status === \App\Enums\StatusPenempatanEnum::Selesai;
                            @endphp
                            <tr style="border-bottom: 1px solid var(--neutral-100, #F1F5F9);">
                                <td style="padding: 1rem 1.5rem;">
                                    <div style="font-weight: 600; color: var(--neutral-900, #0F172A);">
                                        {{ $penempatan->peserta?->nama }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--neutral-500, #64748B);">
                                        {{ $penempatan->peserta?->profil?->institusi ?? '-' }}
                                    </div>
                                </td>
                                <td style="padding: 1rem 1rem;">
                                    <span style="font-size: 0.75rem; font-weight: 600; padding: 0.2rem 0.6rem; border-radius: 999px; background: {{ $isSelesai ? '#f1f5f9' : '#e0e7ff' }}; color: {{ $isSelesai ? '#475569' : '#3730a3' }};">
                                        {{ $isSelesai ? 'Selesai Magang' : 'Aktif Bimbingan' }}
                                    </span>
                                </td>
                                <td style="padding: 1rem 1rem;">
                                    <span style="font-size: 0.75rem; font-weight: 600; padding: 0.2rem 0.6rem; border-radius: 999px; background: {{ $statusNilai === 'final' ? '#dcfce7' : ($statusNilai === 'draft' ? '#fef3c7' : '#fee2e2') }}; color: {{ $statusNilai === 'final' ? '#166534' : ($statusNilai === 'draft' ? '#92400e' : '#b91c1c') }};">
                                        {{ ucfirst($statusNilai) }}
                                    </span>
                                </td>
                                <td style="padding: 1rem 1rem;">
                                    @if($penilaian && $penilaian->nilai_akhir !== null)
                                        <span style="font-weight: 700; color: var(--neutral-900, #0F172A); font-size: 0.9rem;">
                                            {{ number_format((float) $penilaian->nilai_akhir, 2) }}
                                        </span>
                                        <span style="font-weight: 700; color: #0641CC; margin-left: 0.25rem;">
                                            ({{ $penilaian->predikat ?? '-' }})
                                        </span>
                                    @else
                                        <span style="font-size: 0.75rem; color: var(--neutral-400, #94A3B8);">Belum Dinilai</span>
                                    @endif
                                </td>
                                <td style="padding: 1rem 1rem; font-size: 0.8rem; color: var(--neutral-600, #475569); white-space: nowrap;">
                                    @if($penempatan->tgl_nonaktif)
                                        <span style="color: #d97706; font-weight: 600;">
                                            Aktif s/d {{ \Carbon\Carbon::parse($penempatan->tgl_nonaktif)->format('d M Y') }}
                                        </span>
                                    @else
                                        <span style="color: var(--neutral-400, #94A3B8);">-</span>
                                    @endif
                                </td>
                                <td style="padding: 1rem 1.5rem; text-align: right; white-space: nowrap;">
                                    <div style="display: flex; gap: 0.4rem; justify-content: flex-end;">
                                        <a href="{{ route('pembimbing.penilaian.show', $penempatan->id) }}" style="padding: 0.35rem 0.65rem; background: #0641CC; color: #ffffff; font-size: 0.75rem; font-weight: 600; border-radius: 6px; text-decoration: none;">
                                            Form Nilai
                                        </a>
                                        @if($statusNilai === 'final')
                                            <a href="{{ route('pembimbing.penilaian.unduh', $penempatan->id) }}" style="padding: 0.35rem 0.65rem; background: rgba(6, 65, 204, 0.08); color: #0641CC; font-size: 0.75rem; font-weight: 600; border-radius: 6px; text-decoration: none;">
                                                Unduh PDF
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding: 2rem; text-align: center; color: var(--neutral-500, #64748B);">
                                    Belum ada data penempatan mentee.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-gentelella.x-panel>

        <!-- Riwayat Revisi Nilai Final (AC-10-3) -->
        @if($recentNilaiRevisions->count() > 0)
            <x-gentelella.x-panel
                title="Jejak Audit Revisi Nilai Final (AC-10-3)"
                subtitle="Setiap perubahan pada penilaian yang sudah berstatus Final tercatat dengan alasan resmi dan nilai sebelum vs sesudah."
            >
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.8125rem;">
                        <thead>
                            <tr style="border-bottom: 1px solid var(--neutral-200, #E2E8F0); background: #f8fafc; color: var(--neutral-600, #475569); font-size: 0.75rem; text-transform: uppercase;">
                                <th style="padding: 0.75rem 1.5rem;">Waktu</th>
                                <th style="padding: 0.75rem 1rem;">Peserta</th>
                                <th style="padding: 0.75rem 1rem;">Pengubah</th>
                                <th style="padding: 0.75rem 1rem;">Nilai Lama &rarr; Baru</th>
                                <th style="padding: 0.75rem 1.5rem;">Alasan Perubahan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentNilaiRevisions as $rev)
                                @php
                                    $lamaAkhir = is_array($rev->nilai_lama) ? ($rev->nilai_lama['nilai_akhir'] ?? '-') : $rev->nilai_lama;
                                    $baruAkhir = is_array($rev->nilai_baru) ? ($rev->nilai_baru['nilai_akhir'] ?? '-') : $rev->nilai_baru;
                                @endphp
                                <tr style="border-bottom: 1px solid var(--neutral-100, #F1F5F9);">
                                    <td style="padding: 0.85rem 1.5rem; color: var(--neutral-500, #64748B); white-space: nowrap;">
                                        {{ $rev->waktu?->format('d/m/Y H:i') ?? '-' }} WIB
                                    </td>
                                    <td style="padding: 0.85rem 1rem; font-weight: 600; color: var(--neutral-900, #0F172A);">
                                        {{ $rev->penilaian?->penempatan?->peserta?->nama ?? 'Peserta' }}
                                    </td>
                                    <td style="padding: 0.85rem 1rem; color: var(--neutral-700, #334155);">
                                        {{ $rev->pengubah?->nama ?? 'Pembimbing' }}
                                    </td>
                                    <td style="padding: 0.85rem 1rem; font-weight: 600; white-space: nowrap;">
                                        <span style="color: #ef4444;">{{ is_numeric($lamaAkhir) ? number_format((float) $lamaAkhir, 2) : $lamaAkhir }}</span>
                                        &rarr;
                                        <span style="color: #10b981;">{{ is_numeric($baruAkhir) ? number_format((float) $baruAkhir, 2) : $baruAkhir }}</span>
                                    </td>
                                    <td style="padding: 0.85rem 1.5rem; color: var(--neutral-700, #334155);">
                                        {{ $rev->alasan }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-gentelella.x-panel>
        @endif

    </div>

    <!-- ========================================================================= -->
    <!-- TAB 5: AGENDA MENTORING & RADAR CAPAIAN KOMPETENSI                        -->
    <!-- ========================================================================= -->
    <div class="tab-pane" id="tab-agenda">

        <!-- 1. RADAR CAPAIAN KOMPETENSI MENTEE BIDANG -->
        <x-gentelella.x-panel
            title="Radar Capaian Kompetensi Mentee Bidang"
            subtitle="Indeks performa dan penguasaan kompetensi rata-rata seluruh mentee berdasarkan rubrik penilaian dinas."
        >
            <x-slot:actions>
                <a href="{{ route('pembimbing.penilaian.index') }}" style="font-size: 0.825rem; font-weight: 700; color: #0641CC; text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;">
                    <span>Kelola Lembar Nilai</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </a>
            </x-slot:actions>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
                @foreach($competencyRadar as $radar)
                    @php
                        $skor = $radar['skor_rata'];
                        $color = $skor >= 85 ? '#10B981' : ($skor >= 75 ? '#0641CC' : '#F59E0B');
                    @endphp
                    <div style="background: #F8FAFC; border: 1px solid var(--neutral-200, #E2E8F0); border-radius: 10px; padding: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
                            <span style="font-size: 0.85rem; font-weight: 700; color: var(--neutral-800, #1E293B);">{{ $radar['kriteria'] }}</span>
                            <span style="font-size: 0.85rem; font-weight: 800; color: {{ $color }};">{{ number_format($skor, 1) }} / 100</span>
                        </div>
                        <div style="font-size: 0.72rem; color: var(--neutral-500, #64748B); margin-bottom: 0.5rem;">Bobot Rubrik: {{ $radar['bobot'] }}%</div>
                        <div style="width: 100%; height: 8px; background: #E2E8F0; border-radius: 9999px; overflow: hidden;">
                            <div style="height: 100%; width: {{ min(100, $skor) }}%; background: {{ $color }}; border-radius: 9999px; transition: width 0.4s ease;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-gentelella.x-panel>

        <!-- 2. EARLY WARNING: MENTEE BUTUH PERHATIAN KHUSUS (AT-RISK) -->
        <x-gentelella.x-panel
            title="Deteksi Dini Mentee Butuh Perhatian (Early Warning At-Risk)"
            subtitle="Mentee aktif dengan tugas tertunda (overdue) atau belum mengisi logbook kegiatan harian."
        >
            @if($menteeAtRiskList->isEmpty())
                <div style="padding: 2.5rem 1.5rem; text-align: center; color: var(--neutral-600, #475569);">
                    <div style="width: 44px; height: 44px; border-radius: 50%; background: #ECFDF5; color: #10B981; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem auto;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--neutral-900, #0F172A); margin: 0 0 0.25rem 0;">Seluruh Mentee Sesuai Target</h4>
                    <p style="font-size: 0.8rem; margin: 0;">Tidak ada mentee yang mengalami keterlambatan tugas kritis atau absen logbook.</p>
                </div>
            @else
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.85rem;">
                        <thead>
                            <tr style="border-bottom: 1px solid #E2E8F0; background: #F8FAFC;">
                                <th style="padding: 0.75rem 1rem; font-weight: 700; color: #475569;">Nama Mentee</th>
                                <th style="padding: 0.75rem 1rem; font-weight: 700; color: #475569;">Institusi</th>
                                <th style="padding: 0.75rem 1rem; font-weight: 700; color: #475569;">Tugas Overdue</th>
                                <th style="padding: 0.75rem 1rem; font-weight: 700; color: #475569;">Logbook Terhenti</th>
                                <th style="padding: 0.75rem 1rem; font-weight: 700; color: #475569;">Tingkat Risiko</th>
                                <th style="padding: 0.75rem 1rem; font-weight: 700; color: #475569; text-align: right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($menteeAtRiskList as $risk)
                                @php
                                    $m = $risk['mentee'];
                                    $badgeBg = $risk['risk_level'] === 'Tinggi' ? '#FEF2F2' : '#FFFBEB';
                                    $badgeColor = $risk['risk_level'] === 'Tinggi' ? '#DC2626' : '#D97706';
                                    $badgeBorder = $risk['risk_level'] === 'Tinggi' ? '#FECACA' : '#FDE68A';
                                @endphp
                                <tr style="border-bottom: 1px solid #E2E8F0;">
                                    <td style="padding: 0.75rem 1rem; font-weight: 700; color: #0F172A;">
                                        {{ $m->peserta?->nama }}
                                    </td>
                                    <td style="padding: 0.75rem 1rem; color: #475569;">
                                        {{ $m->peserta?->profil?->institusi ?? '-' }}
                                    </td>
                                    <td style="padding: 0.75rem 1rem;">
                                        @if($risk['overdue_count'] > 0)
                                            <span style="font-weight: 700; color: #DC2626;">{{ $risk['overdue_count'] }} Tugas Terlambat</span>
                                        @else
                                            <span style="color: #10B981;">0 Overdue</span>
                                        @endif
                                    </td>
                                    <td style="padding: 0.75rem 1rem;">
                                        @if($risk['days_no_logbook'] >= 2)
                                            <span style="font-weight: 700; color: #D97706;">{{ $risk['days_no_logbook'] }} Hari Belum Isi</span>
                                        @else
                                            <span style="color: #10B981;">Aktif Mengisi</span>
                                        @endif
                                    </td>
                                    <td style="padding: 0.75rem 1rem;">
                                        <span style="padding: 0.2rem 0.6rem; border-radius: 9999px; font-size: 0.72rem; font-weight: 700; background: {{ $badgeBg }}; color: {{ $badgeColor }}; border: 1px solid {{ $badgeBorder }};">
                                            Risiko {{ $risk['risk_level'] }}
                                        </span>
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: right;">
                                        <a href="{{ route('pembimbing.peserta.show', $m->id) }}" style="padding: 0.35rem 0.75rem; background: #ffffff; border: 1px solid #CBD5E1; color: #334155; border-radius: 6px; font-size: 0.75rem; font-weight: 600; text-decoration: none;">
                                            Buka Profil &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-gentelella.x-panel>

        <!-- 3. AGENDA MENTORING MENDATANG -->
        <x-gentelella.x-panel
            title="Sesi Mentoring & Notulensi Terjadwal"
            subtitle="Jadwal agenda pendampingan terdekat bersama mentee bidang."
        >
            <x-slot:actions>
                <a href="{{ route('pembimbing.agenda.index') }}" style="height: 36px; padding: 0 1rem; background: #0641CC; color: #ffffff; border-radius: 8px; font-size: 0.8125rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;">
                    <span>Kelola Semua Jadwal</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </a>
            </x-slot:actions>

            @if($upcomingAgendaBimbingan->isEmpty())
                <div style="padding: 2.5rem 1.5rem; text-align: center; color: var(--neutral-600, #475569);">
                    <p style="font-size: 0.85rem; margin: 0 0 0.75rem 0;">Belum ada agenda bimbingan yang dijadwalkan dalam waktu dekat.</p>
                    <a href="{{ route('pembimbing.agenda.index') }}" style="font-size: 0.825rem; color: #0641CC; font-weight: 700; text-decoration: underline;">
                        + Jadwalkan Sesi Mentoring Baru
                    </a>
                </div>
            @else
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
                    @foreach($upcomingAgendaBimbingan as $agenda)
                        <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; padding: 1.25rem;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                                <span style="font-size: 0.75rem; font-weight: 700; padding: 0.15rem 0.5rem; border-radius: 9999px; background: {{ $agenda->tipe === 'tatap_muka' ? '#F1F5F9' : '#EFF6FF' }}; color: {{ $agenda->tipe === 'tatap_muka' ? '#334155' : '#1E40AF' }};">
                                    {{ $agenda->tipe === 'tatap_muka' ? '🏢 Tatap Muka' : '💻 Daring' }}
                                </span>
                                <span style="font-size: 0.75rem; font-weight: 700; color: #0641CC;">
                                    {{ $agenda->jam_mulai }} WIB
                                </span>
                            </div>
                            <h4 style="font-size: 0.95rem; font-weight: 700; color: #0F172A; margin: 0 0 0.35rem 0;">{{ $agenda->judul_kegiatan }}</h4>
                            <div style="font-size: 0.8rem; color: #475569; margin-bottom: 0.25rem;">Mentee: <strong>{{ $agenda->penempatan?->peserta?->nama }}</strong></div>
                            <div style="font-size: 0.75rem; color: #64748B;">Tanggal: {{ $agenda->tgl_bimbingan?->translatedFormat('d M Y') }} &bull; {{ $agenda->lokasi_ruang ?? '-' }}</div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-gentelella.x-panel>

    </div>

</div>

@push('scripts')
<script>
    function switchPembimbingTab(tabId) {
        // Hide all tab panes
        document.querySelectorAll('.tab-pane').forEach(function(el) {
            el.classList.remove('active');
        });
        // Deactivate all tab buttons
        document.querySelectorAll('.pembimbing-tab-btn').forEach(function(el) {
            el.classList.remove('active');
        });

        // Activate selected tab pane and button
        var targetPane = document.getElementById(tabId);
        if (targetPane) {
            targetPane.classList.add('active');
        }

        var targetBtn = document.getElementById('btn-' + tabId);
        if (targetBtn) {
            targetBtn.classList.add('active');
        }

        // Store in localStorage for state persistence
        try {
            localStorage.setItem('magkom_pembimbing_active_tab', tabId);
        } catch(e) {}
    }

    // Client-side realtime table filter
    function filterTableByInput(inputEl, tableId) {
        var filter = inputEl.value.toLowerCase().trim();
        var table = document.getElementById(tableId);
        if (!table) return;
        var tbody = table.getElementsByTagName('tbody')[0];
        if (!tbody) return;
        var trs = tbody.getElementsByTagName('tr');
        for (var i = 0; i < trs.length; i++) {
            var rowText = trs[i].textContent.toLowerCase();
            if (rowText.indexOf(filter) > -1) {
                trs[i].style.display = "";
            } else {
                trs[i].style.display = "none";
            }
        }
    }

    // Client-side quick filter untuk Kartu Tugas Pembimbing
    function filterDashboardTasks(filterType, btnEl) {
        document.querySelectorAll('.task-filter-btn').forEach(function(b) {
            b.classList.remove('active');
        });
        if (btnEl) {
            btnEl.classList.add('active');
        }

        var rows = document.querySelectorAll('.task-item-row');
        var visibleCount = 0;

        rows.forEach(function(row) {
            var isUrgent = row.getAttribute('data-is-urgent') === '1';
            var hasPending = row.getAttribute('data-has-pending') === '1';

            if (filterType === 'semua') {
                row.style.display = 'flex';
                visibleCount++;
            } else if (filterType === 'mendesak') {
                if (isUrgent) {
                    row.style.display = 'flex';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            } else if (filterType === 'reviu') {
                if (hasPending) {
                    row.style.display = 'flex';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            }
        });

        var emptyBox = document.getElementById('task-filter-empty-state');
        if (emptyBox) {
            emptyBox.style.display = (visibleCount === 0) ? 'block' : 'none';
        }
    }

    // Auto-restore tab from localStorage or hash
    document.addEventListener('DOMContentLoaded', function() {
        var hash = window.location.hash.replace('#', '');
        var savedTab = null;
        try {
            savedTab = localStorage.getItem('magkom_pembimbing_active_tab');
        } catch(e) {}

        var tabToActivate = hash || savedTab;
        if (tabToActivate && document.getElementById(tabToActivate)) {
            switchPembimbingTab(tabToActivate);
        }
    });
</script>
@endpush
@endsection
