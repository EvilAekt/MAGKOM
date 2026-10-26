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

    /* Kartu & Item Tugas Terpadu */
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
    [data-theme="dark"] .micro-progress-bar {
        background: #26354D;
    }
</style>
@endpush

@section('content')
{{-- =========================================================================
   Gentelella Cockpit Dashboard - Pembimbing Lapangan Dinas Kominfo Sleman
   Standar Visual Gentelella Theme (Zero-Regression & Adaptive Responsive)
   ========================================================================= --}}
<div class="gentelella-dashboard-pembimbing cv-cockpit-layout" style="width: 100%;">

    {{-- =========================================================================
       KOLOM UTAMA: HERO, METRIK, TAB CONTROLLER & PANELS
       ========================================================================= --}}
    <div class="cv-cockpit-main" style="display: flex; flex-direction: column; gap: 1.5rem; min-width: 0;">

        <!-- 1. Header & Atmospheric Hero -->
        @include('pembimbing.dashboard.partials._header')

        <!-- 2. Gentelella Top Tile Count (4 Metrik Utama Pembimbing) & Komposisi Jenjang -->
        @include('pembimbing.dashboard.partials._metrics')

        <!-- 3. Segmented Multi-Tab Controller -->
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

        <!-- 4. Tab Panes -->
        <div class="tab-pane active" id="tab-ringkasan">
            @include('pembimbing.dashboard.partials._tab_ringkasan')
        </div>

        <div class="tab-pane" id="tab-antrean">
            @include('pembimbing.dashboard.partials._tab_antrean')
        </div>

        <div class="tab-pane" id="tab-mentee">
            @include('pembimbing.dashboard.partials._tab_mentee')
        </div>

        <div class="tab-pane" id="tab-evaluasi">
            @include('pembimbing.dashboard.partials._tab_evaluasi')
        </div>

        <div class="tab-pane" id="tab-agenda">
            @include('pembimbing.dashboard.partials._tab_agenda')
        </div>

    </div>

    {{-- =========================================================================
       KOLOM SAMPING: STICKY ASIDE 340PX (PERLU REVIU & AKSI CEPAT)
       ========================================================================= --}}
    <aside class="cv-aside-sticky">
        @include('pembimbing.dashboard.partials._aside')
    </aside>

</div>

{{-- Slide-Over Drawer: Reviu Logbook Mentee --}}
@include('pembimbing.dashboard.partials._modal_review_logbook')

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
