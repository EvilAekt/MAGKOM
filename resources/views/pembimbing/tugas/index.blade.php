@extends('layouts.dashboard')

@section('title', 'Manajemen Penugasan Peserta - Portal MagKom')
@section('page_title', 'Tugas & Progres Magang')
@section('page_description', 'Manajemen penugasan terstruktur dan evaluasi kemajuan kerja mentee bimbingan.')

@push('styles')
<style>
    /* Google Classroom Theme untuk Dashboard Pembimbing */
    .classroom-container {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        max-width: 1400px;
        margin: 0 auto;
        width: 100%;
    }

    /* Classroom Hero Banner */
    .classroom-banner {
        background: linear-gradient(95deg, rgba(6, 65, 204, 0.96) 0%, rgba(6, 65, 204, 0.88) 45%, rgba(6, 65, 204, 0.35) 100%),
                    url('{{ asset('images/classroom_banner_bg.jpg') }}') right center / cover no-repeat;
        border-radius: 16px;
        padding: 30px 34px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 6px 20px rgba(6, 65, 204, 0.16);
        min-height: 185px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .classroom-banner::after {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 85% 50%, rgba(255, 255, 255, 0.08) 0%, transparent 60%);
        pointer-events: none;
    }

    .banner-top-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1.25rem;
        flex-wrap: wrap;
        position: relative;
        z-index: 2;
    }

    .banner-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #ffffff;
        margin: 0 0 6px 0;
        letter-spacing: -0.01em;
        line-height: 1.3;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }

    .banner-subtitle {
        font-size: 0.875rem;
        color: rgba(255, 255, 255, 0.92);
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        line-height: 1.5;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }

    .btn-create-task {
        background: #ffffff;
        color: #0641CC;
        text-decoration: none;
        padding: 0.65rem 1.35rem;
        border-radius: 10px;
        font-size: 0.875rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        white-space: nowrap;
    }

    .btn-create-task:hover {
        background: #f8fafc;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.22);
        color: #042d91;
    }

    .banner-stats {
        display: flex;
        gap: 10px;
        margin-top: 20px;
        flex-wrap: wrap;
        position: relative;
        z-index: 2;
    }

    .banner-stat-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(15, 23, 42, 0.45);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        border: 1px solid rgba(255, 255, 255, 0.28);
        color: #ffffff;
    }

    /* Tab Filter Navigasi Ala Classroom */
    .classroom-tabs-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        border-bottom: 1px solid var(--neutral-300, #E2E8F0);
        padding-bottom: 2px;
    }

    .classroom-tabs {
        display: flex;
        gap: 6px;
        overflow-x: auto;
        scrollbar-width: none;
    }
    .classroom-tabs::-webkit-scrollbar {
        display: none;
    }

    .tab-btn {
        background: transparent;
        border: none;
        padding: 10px 16px;
        font-size: 0.925rem;
        font-weight: 600;
        color: var(--neutral-600, #64748B);
        cursor: pointer;
        border-bottom: 3px solid transparent;
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
        text-decoration: none;
        font-family: inherit;
    }

    .tab-btn:hover {
        color: #0641CC;
        background: rgba(6, 65, 204, 0.04);
        border-radius: 8px 8px 0 0;
    }

    .tab-btn.active {
        color: #0641CC;
        border-bottom-color: #0641CC;
        font-weight: 700;
    }

    .tab-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 700;
        background: var(--neutral-200, #E2E8F0);
        color: var(--neutral-700, #334155);
        transition: all 0.2s ease;
    }

    .tab-btn.active .tab-count {
        background: #e0e7ff;
        color: #3730a3;
    }

    /* View Switcher */
    .view-switcher-group {
        display: flex;
        align-items: center;
        gap: 4px;
        background: var(--neutral-100, #F1F5F9);
        padding: 4px;
        border-radius: 10px;
        border: 1px solid var(--neutral-300, #E2E8F0);
    }

    .btn-toggle-view {
        background: transparent;
        border: none;
        padding: 6px 12px;
        border-radius: 7px;
        color: var(--neutral-600, #64748B);
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.15s ease;
    }

    .btn-toggle-view:hover {
        color: #0641CC;
    }

    .btn-toggle-view.active {
        background: #ffffff;
        color: #0641CC;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    }

    /* Filter Toolbar */
    .task-filter-toolbar {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid var(--neutral-200, #E2E8F0);
        padding: 1rem 1.25rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    }

    .toolbar-select,
    .toolbar-input {
        width: 100%;
        height: 40px;
        border-radius: 8px;
        border: 1px solid var(--neutral-300, #CBD5E1);
        padding: 0 0.85rem;
        font-size: 0.875rem;
        color: var(--neutral-900, #0F172A);
        background: #ffffff;
        outline: none;
        transition: border-color 0.2s ease;
        box-sizing: border-box;
    }

    .toolbar-select:focus,
    .toolbar-input:focus {
        border-color: #0641CC;
        box-shadow: 0 0 0 3px rgba(6, 65, 204, 0.12);
    }

    .toolbar-btn-search {
        height: 40px;
        padding: 0 1.25rem;
        background: #0641CC;
        color: #ffffff;
        font-weight: 600;
        font-size: 0.875rem;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: background 0.15s ease;
    }

    .toolbar-btn-search:hover {
        background: #042d91;
    }

    .toolbar-btn-reset {
        height: 40px;
        padding: 0 1rem;
        background: var(--neutral-150, #F1F5F9);
        color: var(--neutral-700, #334155);
        font-weight: 600;
        font-size: 0.875rem;
        text-decoration: none;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        border: 1px solid var(--neutral-300, #CBD5E1);
        transition: background 0.15s ease;
    }

    .toolbar-btn-reset:hover {
        background: var(--neutral-200, #E2E8F0);
    }

    /* Assignment Stream Cards */
    .assignment-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .assignment-card {
        background: #ffffff;
        border: 1px solid var(--neutral-200, #E2E8F0);
        border-radius: 14px;
        padding: 20px 24px;
        display: flex;
        align-items: flex-start;
        gap: 18px;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        position: relative;
    }

    .assignment-card:hover {
        border-color: #0641CC;
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(6, 65, 204, 0.10);
    }

    .assignment-icon-wrapper {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .assignment-icon-wrapper.belum {
        background: #e0e7ff;
        color: #0641CC;
    }

    .assignment-icon-wrapper.proses {
        background: #fef3c7;
        color: #92400e;
    }

    .assignment-icon-wrapper.selesai {
        background: #d1fae5;
        color: #065f46;
    }

    .assignment-icon-wrapper.overdue {
        background: #fee2e2;
        color: #dc2626;
    }

    .assignment-body {
        flex: 1;
        min-width: 0;
    }

    .assignment-header-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 6px;
    }

    .assignment-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--neutral-900, #0F172A);
        line-height: 1.35;
        margin: 0;
    }

    .assignment-title a {
        color: inherit;
        text-decoration: none;
        transition: color 0.15s ease;
    }

    .assignment-title a:hover {
        color: #0641CC;
    }

    .assignment-due {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--neutral-600, #64748B);
        white-space: nowrap;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .assignment-due.terlambat {
        color: #dc2626;
        font-weight: 700;
        background: #fee2e2;
        padding: 3px 8px;
        border-radius: 6px;
    }

    .assignment-due.mendekati {
        color: #92400e;
        font-weight: 700;
        background: #fef3c7;
        padding: 3px 8px;
        border-radius: 6px;
    }

    .assignment-desc {
        font-size: 0.875rem;
        color: var(--neutral-600, #475569);
        line-height: 1.5;
        margin: 4px 0 12px 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .assignment-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        padding-top: 12px;
        border-top: 1px solid var(--neutral-200, #F1F5F9);
    }

    .assignment-meta-tags {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        font-size: 0.75rem;
    }

    /* Mentee Chip - Identitas Mentee Bimbingan */
    .mentee-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        color: #1e293b;
    }

    .mentee-avatar-mini {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #0641CC;
        color: #ffffff;
        font-size: 0.65rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .mentee-inst {
        color: #64748b;
        font-weight: 500;
    }

    .assignment-tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: var(--neutral-150, #F1F5F9);
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--neutral-700, #334155);
    }

    .tag-complexity.rendah { background: #ecfdf5; color: #047857; }
    .tag-complexity.sedang { background: #fffbeb; color: #b45309; }
    .tag-complexity.tinggi { background: #eef2ff; color: #4338ca; }

    .tag-progres {
        background: #e0e7ff;
        color: #3730a3;
    }

    .tag-pending-review {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }

    .assignment-tag-link {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 9px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 700;
        text-decoration: none;
        transition: opacity 0.15s ease;
    }
    .assignment-tag-link:hover {
        opacity: 0.85;
    }

    .tag-link-external {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }

    /* Status Pills */
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .status-pill-success {
        background: #d1fae5;
        color: #065f46;
    }

    .status-pill-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .status-pill-info {
        background: #f1f5f9;
        color: #475569;
    }

    /* Action Buttons */
    .btn-action-primary {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 0.45rem 1rem;
        background: #0641CC;
        color: #ffffff;
        font-size: 0.8rem;
        font-weight: 700;
        border-radius: 8px;
        text-decoration: none;
        box-shadow: 0 2px 6px rgba(6, 65, 204, 0.2);
        transition: all 0.15s ease;
    }

    .btn-action-primary:hover {
        background: #042d91;
        transform: translateY(-1px);
    }

    .btn-action-secondary {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 0.45rem 0.95rem;
        background: rgba(6, 65, 204, 0.08);
        color: #0641CC;
        font-size: 0.8rem;
        font-weight: 700;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.15s ease;
    }

    .btn-action-secondary:hover {
        background: rgba(6, 65, 204, 0.16);
    }

    /* Empty State */
    .empty-state-card {
        background: #ffffff;
        border-radius: 14px;
        border: 1px dashed var(--neutral-300, #CBD5E1);
        padding: 56px 24px;
        text-align: center;
    }

    .empty-state-icon {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: #e0e7ff;
        color: #0641CC;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px auto;
    }

    /* Dark Mode Adjustments */
    [data-theme="dark"] .classroom-banner {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
    }
    [data-theme="dark"] .task-filter-toolbar,
    [data-theme="dark"] .assignment-card,
    [data-theme="dark"] .empty-state-card {
        background: var(--neutral-800, #1E293B);
        border-color: var(--neutral-700, #334155);
    }
    [data-theme="dark"] .toolbar-select,
    [data-theme="dark"] .toolbar-input {
        background: var(--neutral-900, #0F172A);
        border-color: var(--neutral-700, #334155);
        color: #F8FAFC;
    }
    [data-theme="dark"] .assignment-title,
    [data-theme="dark"] .assignment-title a {
        color: #F8FAFC;
    }
    [data-theme="dark"] .assignment-desc {
        color: #94A3B8;
    }
    [data-theme="dark"] .mentee-chip {
        background: var(--neutral-900, #0F172A);
        border-color: var(--neutral-700, #334155);
        color: #F8FAFC;
    }
    [data-theme="dark"] .assignment-tag {
        background: var(--neutral-900, #0F172A);
        color: #CBD5E1;
    }
    [data-theme="dark"] .view-switcher-group {
        background: var(--neutral-900, #0F172A);
        border-color: var(--neutral-700, #334155);
    }
    [data-theme="dark"] .btn-toggle-view.active {
        background: var(--neutral-800, #1E293B);
        color: #60a5fa;
    }

    @media (max-width: 768px) {
        .classroom-banner { padding: 22px 20px; }
        .banner-top-row { flex-direction: column; }
        .assignment-card { padding: 16px; gap: 14px; }
        .assignment-header-row { flex-direction: column; gap: 6px; }
    }
</style>
@endpush

@section('content')
<div class="classroom-container">

    {{-- Breadcrumb Navigasi --}}
    <nav style="font-size: 0.8rem; color: var(--neutral-500); display: flex; align-items: center; gap: 0.4rem;">
        <a href="{{ route('pembimbing.dashboard') }}" style="color: var(--neutral-600); text-decoration: none;">Dashboard</a>
        <span>/</span>
        <span style="color: var(--neutral-900); font-weight: 600;">Tugas Peserta</span>
    </nav>

    {{-- 1. Classroom Hero Banner Header (Foto 1) --}}
    @php
        $authUser = auth()->user();
        $bidangNama = $authUser->bidang?->nama_bidang ?? 'Dinas Komunikasi dan Informatika Sleman';
        $totalMentee = $penempatanList->count();
    @endphp

    <div class="classroom-banner">
        <div class="banner-top-row">
            <div>
                <h1 class="banner-title">Manajemen Penugasan & Progres (F-08)</h1>
                <div class="banner-subtitle">
                    <span>Bidang: <strong>{{ $bidangNama }}</strong></span>
                    <span>&bull;</span>
                    <span>Pembimbing: <strong>{{ $authUser->nama }}</strong></span>
                    <span>&bull;</span>
                    <span><strong>{{ $totalMentee }} Mentee Aktif Bimbingan</strong></span>
                </div>
            </div>
            <div>
                <a href="{{ route('pembimbing.tugas.create') }}" class="btn-create-task">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    + Buat Tugas Baru
                </a>
            </div>
        </div>

        <div class="banner-stats">
            <div class="banner-stat-chip">
                <span>Total: <strong>{{ $counts['total'] }} Tugas</strong></span>
            </div>
            <div class="banner-stat-chip">
                <span>Ditugaskan: <strong>{{ $counts['belum'] }}</strong></span>
            </div>
            <div class="banner-stat-chip">
                <span>Sedang Dikerjakan: <strong>{{ $counts['proses'] }}</strong></span>
            </div>
            <div class="banner-stat-chip" style="background: rgba(22, 163, 74, 0.25); border-color: rgba(34, 197, 94, 0.4);">
                <span>✓ Selesai: <strong>{{ $counts['selesai'] }}</strong></span>
            </div>
            @if($counts['perlu_review'] > 0)
                <div class="banner-stat-chip" style="background: rgba(245, 158, 11, 0.35); border-color: rgba(245, 158, 11, 0.55);">
                    <span>⚠️ <strong>{{ $counts['perlu_review'] }} Perlu Evaluasi</strong></span>
                </div>
            @endif
        </div>
    </div>

    {{-- 2. Tab Filter Navigasi Ala Google Classroom & View Switcher --}}
    <div class="classroom-tabs-wrapper">
        <div class="classroom-tabs" role="tablist">
            <a href="{{ route('pembimbing.tugas.index', array_merge(request()->except(['status', 'page']))) }}" 
               class="tab-btn {{ !request('status') ? 'active' : '' }}">
                Semua Tugas
                <span class="tab-count">{{ $counts['total'] }}</span>
            </a>
            <a href="{{ route('pembimbing.tugas.index', array_merge(request()->except('page'), ['status' => 'belum'])) }}" 
               class="tab-btn {{ request('status') === 'belum' ? 'active' : '' }}">
                Ditugaskan
                <span class="tab-count">{{ $counts['belum'] }}</span>
            </a>
            <a href="{{ route('pembimbing.tugas.index', array_merge(request()->except('page'), ['status' => 'proses'])) }}" 
               class="tab-btn {{ request('status') === 'proses' ? 'active' : '' }}">
                Sedang Dikerjakan
                <span class="tab-count">{{ $counts['proses'] }}</span>
            </a>
            <a href="{{ route('pembimbing.tugas.index', array_merge(request()->except('page'), ['status' => 'selesai'])) }}" 
               class="tab-btn {{ request('status') === 'selesai' ? 'active' : '' }}">
                Selesai
                <span class="tab-count">{{ $counts['selesai'] }}</span>
            </a>
            @if($counts['perlu_review'] > 0)
                <a href="{{ route('pembimbing.tugas.index', array_merge(request()->except('page'), ['status' => 'perlu_review'])) }}" 
                   class="tab-btn {{ request('status') === 'perlu_review' ? 'active' : '' }}">
                    ⚠️ Perlu Evaluasi
                    <span class="tab-count" style="background: #fef3c7; color: #92400e;">{{ $counts['perlu_review'] }}</span>
                </a>
            @endif
        </div>

        {{-- Switcher Mode Tampilan (Card vs Table) --}}
        <div class="view-switcher-group">
            <button type="button" id="btnViewCard" class="btn-toggle-view active" title="Tampilan Kartu Classroom" onclick="switchTaskView('card')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                <span>Kartu</span>
            </button>
            <button type="button" id="btnViewTable" class="btn-toggle-view" title="Tampilan Tabel Administratif" onclick="switchTaskView('table')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                <span>Tabel</span>
            </button>
        </div>
    </div>

    {{-- 3. Filter & Toolbar Pencarian Terpadu --}}
    <div class="task-filter-toolbar">
        <form method="GET" action="{{ route('pembimbing.tugas.index') }}" style="display: flex; gap: 0.85rem; flex-wrap: wrap; align-items: center; width: 100%;">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            
            <!-- Filter Mentee -->
            <div style="flex: 1; min-width: 220px;">
                <select name="penempatan_id" onchange="this.form.submit()" class="toolbar-select">
                    <option value="">Semua Mentee Bimbingan ({{ $totalMentee }})</option>
                    @foreach($penempatanList as $p)
                        <option value="{{ $p->id }}" {{ request('penempatan_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->peserta?->nama }} ({{ $p->peserta?->profil?->institusi ?? 'Institusi' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Pencarian Kata Kunci -->
            <div style="flex: 2; min-width: 240px; position: relative;">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul tugas, topik, atau nama mentee..." class="toolbar-input">
                @if(request('q'))
                    <a href="{{ route('pembimbing.tugas.index', request()->except('q')) }}" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: var(--neutral-400); text-decoration: none; font-size: 1.1rem; font-weight: bold;" title="Hapus pencarian">&times;</a>
                @endif
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="toolbar-btn-search">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    Filter
                </button>
                @if(request()->hasAny(['penempatan_id', 'q', 'status']))
                    <a href="{{ route('pembimbing.tugas.index') }}" class="toolbar-btn-reset">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- 4. Tampilan Kartu Assignment Stream Ala Classroom (Default Mode) --}}
    <div id="classroomCardContainer" class="assignment-list">
        @forelse($tugasList as $tugas)
            @php
                $statusVal = $tugas->status->value ?? (string)$tugas->status;
                $isSelesai = $statusVal === 'selesai';
                $isProses = $statusVal === 'proses';
                
                $deadline = $tugas->deadline;
                $now = \Carbon\Carbon::today();
                $isOverdue = $deadline && $deadline->isPast() && !$isSelesai;
                $daysLeft = $deadline ? $now->diffInDays($deadline, false) : 99;
                $isDueSoon = !$isOverdue && $daysLeft >= 0 && $daysLeft <= 3 && !$isSelesai;
                
                $hasPendingProgres = $tugas->progresLogs->contains(fn($log) => $log->status_review?->value === 'menunggu' || $log->status_review === 'menunggu');
                $mentee = $tugas->penempatan?->peserta;
                $menteeNama = $mentee?->nama ?? 'Mentee Belum Ditentukan';
                $institusi = $mentee?->profil?->institusi ?? 'Institusi';
                $initials = collect(explode(' ', $menteeNama))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
            @endphp

            <div class="assignment-card {{ $statusVal }} {{ $isOverdue ? 'overdue' : '' }}">
                <!-- Icon Lingkaran Penanda Status Ala Classroom -->
                <div class="assignment-icon-wrapper {{ $isOverdue ? 'overdue' : ($isSelesai ? 'selesai' : ($isProses ? 'proses' : 'belum')) }}">
                    @if($isOverdue)
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                    @elseif($isSelesai)
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    @elseif($isProses)
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    @else
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                    @endif
                </div>

                <!-- Konten Utama Tugas -->
                <div class="assignment-body">
                    <div class="assignment-header-row">
                        <h3 class="assignment-title">
                            <a href="{{ route('pembimbing.tugas.show', $tugas->id) }}">
                                {{ $tugas->judul }}
                            </a>
                        </h3>

                        <div class="assignment-due {{ $isOverdue ? 'terlambat' : ($isDueSoon ? 'mendekati' : '') }}">
                            @if($isOverdue)
                                <span>⚠️ Terlambat: {{ $deadline?->format('d M Y') }}</span>
                            @elseif($isDueSoon)
                                <span>⏳ Tenggat: {{ $deadline?->format('d M Y') }} (Sisa {{ $daysLeft }} Hari)</span>
                            @elseif($deadline)
                                <span>Tenggat: {{ $deadline->format('d M Y') }}</span>
                            @else
                                <span>Tanpa Tenggat Waktu</span>
                            @endif
                        </div>
                    </div>

                    @if($tugas->deskripsi)
                        <p class="assignment-desc">
                            {{ $tugas->deskripsi }}
                        </p>
                    @endif

                    <div class="assignment-footer">
                        <div class="assignment-meta-tags">
                            <!-- Chip Identitas Mentee (Konteks Khusus Pembimbing) -->
                            <div class="mentee-chip" title="Mentee Penerima Tugas: {{ $menteeNama }}">
                                <div class="mentee-avatar-mini">{{ $initials }}</div>
                                <span class="mentee-name">{{ $menteeNama }}</span>
                                <span class="mentee-inst">&bull; {{ $institusi }}</span>
                            </div>

                            <!-- Tag Kategori -->
                            <span class="assignment-tag">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
                                {{ $tugas->kategori ?? 'Tugas Lapangan' }}
                            </span>

                            <!-- Tag Kompleksitas -->
                            <span class="assignment-tag tag-complexity {{ strtolower($tugas->kompleksitas?->value ?? 'sedang') }}">
                                {{ ucfirst($tugas->kompleksitas?->value ?? 'Sedang') }}
                            </span>

                            <!-- Laporan Progres -->
                            @if($tugas->progresLogs->count() > 0)
                                <span class="assignment-tag {{ $hasPendingProgres ? 'tag-pending-review' : 'tag-progres' }}">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                                    {{ $tugas->progresLogs->count() }} Laporan Diserahkan
                                    @if($hasPendingProgres)
                                        <strong style="color: #b45309; font-weight: 700;">(Perlu Reviu)</strong>
                                    @endif
                                </span>
                            @endif

                            <!-- Dokumen Lampiran Acuan -->
                            @if($tugas->file_lampiran)
                                @php $badge = $tugas->badge_color_lampiran; @endphp
                                <a href="{{ route('pembimbing.tugas.lampiran', $tugas->id) }}" class="assignment-tag-link" title="{{ $tugas->nama_file_lampiran }}" style="background: {{ $badge['bg'] }}; color: {{ $badge['color'] }}; border: 1px solid {{ $badge['border'] }};">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                    {{ $badge['label'] }} ({{ $tugas->ukuran_format }})
                                </a>
                            @endif

                            <!-- Link Referensi -->
                            @if($tugas->link_referensi)
                                <a href="{{ $tugas->link_referensi }}" target="_blank" rel="noopener noreferrer" class="assignment-tag-link tag-link-external">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                                    Link Acuan ↗
                                </a>
                            @endif
                        </div>

                        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                            <!-- Status Badge Pill Ala Classroom -->
                            @if($isSelesai)
                                <span class="status-pill status-pill-success">✓ Selesai</span>
                            @elseif($isProses)
                                <span class="status-pill status-pill-warning">● Sedang Dikerjakan</span>
                            @else
                                <span class="status-pill status-pill-info">● Belum Dikerjakan</span>
                            @endif

                            <!-- Action Button Cerdas -->
                            @if($hasPendingProgres)
                                <a href="{{ route('pembimbing.tugas.show', $tugas->id) }}" class="btn-action-primary">
                                    Evaluasi Progres &rarr;
                                </a>
                            @else
                                <a href="{{ route('pembimbing.tugas.show', $tugas->id) }}" class="btn-action-secondary">
                                    Detail & Reviu &rarr;
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state-card">
                <div class="empty-state-icon">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"></path>
                        <rect x="9" y="3" width="6" height="4" rx="2"></rect>
                        <path d="M9 14l2 2 4-4"></path>
                    </svg>
                </div>
                <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--neutral-800, #1E293B); margin-bottom: 0.4rem;">
                    Belum Ada Penugasan Ditemukan
                </h3>
                <p style="font-size: 0.875rem; color: var(--neutral-600, #64748B); max-width: 460px; margin: 0 auto 1.25rem auto; line-height: 1.5;">
                    Belum ada penugasan yang sesuai dengan filter atau kata kunci saat ini. Anda dapat mereset filter atau memberikan penugasan baru kepada mentee bimbingan.
                </p>
                <a href="{{ route('pembimbing.tugas.create') }}" class="btn-action-primary" style="padding: 0.6rem 1.25rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Buat Tugas Baru Sekarang
                </a>
            </div>
        @endforelse
    </div>

    {{-- 5. Tampilan Tabel Alternatif (Table Mode) --}}
    <div id="classroomTableContainer" style="display: none; background: #ffffff; border-radius: 14px; border: 1px solid var(--neutral-200, #E2E8F0); box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden;">
        <div class="table-responsive-gentelella" style="overflow-x: auto;">
            <table class="table jambo_table bulk_action" style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                <thead>
                    <tr class="headings" style="border-bottom: 1px solid var(--neutral-200, #E2E8F0); background: #f8fafc; color: var(--neutral-600, #64748B); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                        <th style="padding: 1rem 1.5rem;">Tugas & Kategori</th>
                        <th style="padding: 1rem 1rem;">Peserta Magang</th>
                        <th style="padding: 1rem 1rem;">Kompleksitas</th>
                        <th style="padding: 1rem 1rem;">Tenggat Waktu</th>
                        <th style="padding: 1rem 1rem;">Status</th>
                        <th style="padding: 1rem 1.5rem; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tugasList as $tugas)
                        @php
                            $isOverdue = $tugas->deadline && $tugas->deadline->isPast() && $tugas->status->value !== 'selesai';
                            $daysLeft = $tugas->deadline ? \Carbon\Carbon::today()->diffInDays($tugas->deadline, false) : 99;
                            $isDueSoon = !$isOverdue && $daysLeft >= 0 && $daysLeft <= 3 && $tugas->status->value !== 'selesai';
                        @endphp
                        <tr style="border-bottom: 1px solid var(--neutral-100, #F1F5F9); transition: background 0.15s ease;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 1rem 1.5rem;">
                                <div style="font-weight: 600; color: var(--neutral-900, #0F172A); font-size: 0.925rem;">
                                    <a href="{{ route('pembimbing.tugas.show', $tugas->id) }}" style="color: inherit; text-decoration: none;">
                                        {{ $tugas->judul }}
                                    </a>
                                </div>
                                <div style="display: flex; gap: 0.4rem; align-items: center; margin-top: 0.35rem; flex-wrap: wrap;">
                                    <span style="font-size: 0.7rem; background: #e0e7ff; color: #3730a3; padding: 0.1rem 0.45rem; border-radius: 4px; font-weight: 600;">
                                        {{ $tugas->kategori }}
                                    </span>
                                    <span style="font-size: 0.75rem; color: var(--neutral-500, #64748B);">
                                        &bull; {{ $tugas->progresLogs->count() }} Laporan Progres
                                    </span>
                                    @if($tugas->file_lampiran)
                                        @php $badge = $tugas->badge_color_lampiran; @endphp
                                        <a href="{{ route('pembimbing.tugas.lampiran', $tugas->id) }}" title="{{ $tugas->nama_file_lampiran }}" style="display: inline-flex; align-items: center; gap: 0.2rem; font-size: 0.675rem; font-weight: 700; padding: 0.1rem 0.4rem; border-radius: 4px; background: {{ $badge['bg'] }}; color: {{ $badge['color'] }}; text-decoration: none; border: 1px solid {{ $badge['border'] }};">
                                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                            {{ $badge['label'] }} ({{ $tugas->ukuran_format }})
                                        </a>
                                    @endif
                                    @if($tugas->link_referensi)
                                        <a href="{{ $tugas->link_referensi }}" target="_blank" rel="noopener noreferrer" style="display: inline-flex; align-items: center; gap: 0.2rem; font-size: 0.675rem; font-weight: 700; padding: 0.1rem 0.4rem; border-radius: 4px; background: #eff6ff; color: #1d4ed8; text-decoration: none; border: 1px solid #bfdbfe;">
                                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                                            Link Acuan ↗
                                        </a>
                                    @endif
                                </div>
                            </td>
                            <td style="padding: 1rem 1rem;">
                                <div style="font-weight: 500; color: var(--neutral-800, #1E293B);">
                                    {{ $tugas->penempatan?->peserta?->nama ?? '-' }}
                                </div>
                                <div style="font-size: 0.75rem; color: var(--neutral-500, #64748B);">
                                    {{ $tugas->penempatan?->peserta?->profil?->institusi ?? '-' }}
                                </div>
                            </td>
                            <td style="padding: 1rem 1rem;">
                                <span style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-700, #334155); background: var(--neutral-100, #F1F5F9); padding: 0.2rem 0.5rem; border-radius: 4px;">
                                    {{ ucfirst($tugas->kompleksitas?->value ?? 'Sedang') }}
                                </span>
                            </td>
                            <td style="padding: 1rem 1rem;">
                                <div style="font-size: 0.825rem; font-weight: 500; color: {{ $isOverdue ? '#dc2626' : ($isDueSoon ? '#d97706' : 'var(--neutral-800, #1E293B)') }};">
                                    {{ $tugas->deadline?->format('d M Y') }}
                                </div>
                                @if($isOverdue)
                                    <span style="font-size: 0.7rem; font-weight: 700; color: #dc2626; background: #fee2e2; padding: 0.1rem 0.4rem; border-radius: 4px; display: inline-block; margin-top: 0.15rem;">
                                        Terlambat (Overdue)
                                    </span>
                                @elseif($isDueSoon)
                                    <span style="font-size: 0.7rem; font-weight: 600; color: #92400e; background: #fef3c7; padding: 0.1rem 0.4rem; border-radius: 4px; display: inline-block; margin-top: 0.15rem;">
                                        Sisa {{ $daysLeft }} Hari
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 1rem 1rem;">
                                @if($tugas->status->value === 'selesai')
                                    <span style="font-size: 0.75rem; font-weight: 600; background: #d1fae5; color: #065f46; padding: 0.2rem 0.55rem; border-radius: 999px;">
                                        Selesai
                                    </span>
                                @elseif($tugas->status->value === 'proses')
                                    <span style="font-size: 0.75rem; font-weight: 600; background: #fef3c7; color: #92400e; padding: 0.2rem 0.55rem; border-radius: 999px;">
                                        Sedang Dikerjakan
                                    </span>
                                @else
                                    <span style="font-size: 0.75rem; font-weight: 500; background: var(--neutral-100, #F1F5F9); color: var(--neutral-600, #64748B); padding: 0.2rem 0.55rem; border-radius: 999px;">
                                        Belum Dikerjakan
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 1rem 1.5rem; text-align: right;">
                                <a href="{{ route('pembimbing.tugas.show', $tugas->id) }}" style="padding: 0.4rem 0.85rem; background: rgba(6, 65, 204, 0.08); color: #0641CC; font-size: 0.75rem; font-weight: 600; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;">
                                    Detail & Reviu &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 3rem 1.5rem; text-align: center; color: var(--neutral-500, #64748B);">
                                Belum ada penugasan yang sesuai dengan kriteria filter saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- 6. Paginasi Halaman --}}
    @if($tugasList->hasPages())
        <div style="background: #ffffff; border-radius: 12px; border: 1px solid var(--neutral-200, #E2E8F0); padding: 0.85rem 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
            {{ $tugasList->links() }}
        </div>
    @endif

</div>

<script>
    function switchTaskView(mode) {
        const cardContainer = document.getElementById('classroomCardContainer');
        const tableContainer = document.getElementById('classroomTableContainer');
        const btnCard = document.getElementById('btnViewCard');
        const btnTable = document.getElementById('btnViewTable');

        if (mode === 'table') {
            if (cardContainer) cardContainer.style.display = 'none';
            if (tableContainer) tableContainer.style.display = 'block';
            if (btnCard) btnCard.classList.remove('active');
            if (btnTable) btnTable.classList.add('active');
            localStorage.setItem('pembimbing_task_view_mode', 'table');
        } else {
            if (cardContainer) cardContainer.style.display = 'flex';
            if (tableContainer) tableContainer.style.display = 'none';
            if (btnTable) btnTable.classList.remove('active');
            if (btnCard) btnCard.classList.add('active');
            localStorage.setItem('pembimbing_task_view_mode', 'card');
        }
    }

    // Pulihkan preferensi mode tampilan yang disimpan pengguna
    document.addEventListener('DOMContentLoaded', function() {
        const savedMode = localStorage.getItem('pembimbing_task_view_mode');
        if (savedMode === 'table') {
            switchTaskView('table');
        }
    });
</script>
@endsection
