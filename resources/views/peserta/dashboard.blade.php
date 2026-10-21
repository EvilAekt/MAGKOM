@extends('layouts.peserta')

@section('title', 'Dashboard Peserta')
@section('page_title', 'Dashboard Peserta')

@push('styles')
<style>
    /* Dashboard Hero Banner */
    .dashboard-hero {
        background: linear-gradient(95deg, rgba(6, 65, 204, 0.96) 0%, rgba(6, 65, 204, 0.88) 45%, rgba(6, 65, 204, 0.35) 100%),
                    url('{{ asset('images/dashboard_hero_bg.jpg') }}') right center / cover no-repeat;
        border-radius: var(--radius-lg);
        padding: 32px 36px;
        color: #ffffff;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 6px 20px rgba(6, 65, 204, 0.16);
        min-height: 180px;
    }

    .dashboard-hero::after {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 85% 50%, rgba(255, 255, 255, 0.08) 0%, transparent 60%);
        pointer-events: none;
    }

    .hero-content {
        position: relative;
        z-index: 2;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .hero-greeting h1 {
        font-size: var(--text-2xl);
        font-weight: var(--fw-bold);
        color: #ffffff;
        letter-spacing: -0.01em;
        margin-bottom: 6px;
        line-height: 1.25;
    }

    .hero-placement-title {
        font-size: var(--text-xl);
        font-weight: var(--fw-bold);
        color: #ffffff;
        margin: 4px 0 10px 0;
        line-height: 1.3;
    }

    .hero-greeting p {
        font-size: var(--text-base);
        color: rgba(255, 255, 255, 0.92);
        max-width: 600px;
        line-height: 1.5;
    }

    .hero-meta-badges {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
        margin-top: 10px;
    }

    .hero-meta-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255, 255, 255, 0.16);
        backdrop-filter: blur(6px);
        padding: 5px 12px;
        border-radius: var(--radius-full);
        font-size: var(--text-xs);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.22);
    }

    .hero-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        background: #10B981;
        color: #ffffff;
        border-radius: var(--radius-full);
        font-size: var(--text-2xs);
        font-weight: var(--fw-bold);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .hero-meta {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 10px;
    }

    .hero-date-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(8px);
        padding: 6px 14px;
        border-radius: var(--radius-full);
        font-size: var(--text-sm);
        font-weight: var(--fw-semibold);
        border: 1px solid rgba(255, 255, 255, 0.25);
    }

    .hero-glass-btn {
        background: rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.35);
        color: #ffffff;
        padding: 8px 16px;
        border-radius: var(--radius-full);
        font-size: var(--text-xs);
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }

    .hero-glass-btn:hover {
        background: rgba(255, 255, 255, 0.28);
        color: #ffffff;
        border-color: rgba(255, 255, 255, 0.6);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .hero-role-pill {
        display: inline-block;
        padding: 4px 12px;
        background: #ffffff;
        color: var(--blue-700);
        border-radius: var(--radius-full);
        font-size: var(--text-2xs);
        font-weight: var(--fw-bold);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* Attention Banner (Catatan Perhatian) */
    .attention-banner {
        background: #FFFBEB;
        border: 1px solid #FDE68A;
        border-left: 4px solid #D97706;
        border-radius: var(--radius-md);
        padding: 14px 20px;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        box-shadow: 0 1px 3px rgba(217, 119, 6, 0.08);
    }

    .attention-title {
        color: #92400E;
        font-size: 14px;
        font-weight: var(--fw-bold);
    }

    .attention-desc {
        color: #78350F;
        font-size: 13px;
        margin-left: 4px;
    }

    .btn-attention-action {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #D97706;
        color: #ffffff;
        padding: 6px 14px;
        border-radius: var(--radius-full);
        font-size: var(--text-xs);
        font-weight: 600;
        text-decoration: none;
        transition: all 0.15s ease;
    }

    .btn-attention-action:hover {
        background: #B45309;
        color: #ffffff;
    }

    /* Quick Action Buttons */
    .quick-actions-bar {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 12px;
        margin-bottom: 22px;
        flex-wrap: wrap;
    }

    /* KPI Unified Strip Card (Panel Metrik Terpadu) */
    .kpi-unified-card {
        background: var(--neutral-0);
        border: 1px solid var(--neutral-200);
        border-radius: var(--radius-md);
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        margin-bottom: 28px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }

    .kpi-unified-item {
        padding: 18px 20px;
        display: flex;
        align-items: flex-start;
        gap: 14px;
        position: relative;
        transition: background-color 0.15s ease;
    }

    .kpi-unified-item:hover {
        background-color: var(--neutral-100);
    }

    .kpi-unified-item:not(:last-child)::after {
        content: '';
        position: absolute;
        right: 0;
        top: 15%;
        height: 70%;
        width: 1px;
        background-color: var(--neutral-200);
    }

    .kpi-icon-box {
        width: 44px;
        height: 44px;
        border-radius: var(--radius-default);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .kpi-icon-blue { background: var(--blue-tint); color: var(--blue-600); }
    .kpi-icon-green { background: var(--green-100); color: var(--green-600); }
    .kpi-icon-amber { background: var(--amber-100); color: var(--amber-700); }
    .kpi-icon-purple { background: #F3E8FF; color: #7E22CE; }

    .kpi-details {
        display: flex;
        flex-direction: column;
        flex: 1;
        min-width: 0;
    }

    .kpi-label {
        font-size: var(--text-xs);
        font-weight: var(--fw-semibold);
        color: var(--neutral-600);
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 2px;
    }

    .kpi-value {
        font-size: var(--text-xl);
        font-weight: var(--fw-bold);
        color: var(--neutral-900);
        line-height: 1.25;
    }

    .micro-progress-bar {
        height: 5px;
        background: var(--neutral-200);
        border-radius: 999px;
        overflow: hidden;
        margin: 6px 0 4px 0;
    }

    .micro-progress-fill {
        height: 100%;
        border-radius: 999px;
        transition: width 0.3s ease;
    }

    .kpi-subtext {
        font-size: 11px;
        color: var(--neutral-500);
        margin-top: 1px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Onboarding Card */
    .onboarding-card {
        background: var(--neutral-0);
        border: 1px solid var(--blue-tint-line);
        border-radius: var(--radius-md);
        padding: 28px;
        margin-bottom: 28px;
        box-shadow: 0 2px 8px rgba(7, 81, 255, 0.06);
    }

    .stepper-list {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin: 24px 0 20px 0;
    }

    .step-item {
        background: var(--neutral-100);
        border: 1px solid var(--neutral-200);
        border-radius: var(--radius-default);
        padding: 18px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        position: relative;
    }

    .step-item.completed {
        background: #F0FDF4;
        border-color: #BBF7D0;
    }

    .step-item.active {
        background: #EFF6FF;
        border-color: #BFDBFE;
    }

    .step-badge {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: var(--text-xs);
        font-weight: var(--fw-bold);
        background: var(--neutral-400);
        color: #ffffff;
    }

    .step-item.completed .step-badge { background: var(--green-600); }
    .step-item.active .step-badge { background: var(--blue-600); }

    .step-title {
        font-size: var(--text-base);
        font-weight: var(--fw-semibold);
        color: var(--neutral-800);
        line-height: 1.3;
    }

    .step-desc {
        font-size: var(--text-xs);
        color: var(--neutral-600);
        line-height: 1.4;
    }

    /* Phase Selection Tracker */
    .status-tracker-card {
        background: var(--neutral-0);
        border: 1px solid var(--neutral-400);
        border-radius: var(--radius-md);
        padding: 24px;
        margin-bottom: 28px;
    }

    .tracker-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .info-strip {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        background: var(--neutral-100);
        padding: 16px;
        border-radius: var(--radius-default);
        margin-bottom: 16px;
    }

    .info-strip-item span {
        font-size: var(--text-xs);
        color: var(--neutral-500);
        display: block;
    }

    .info-strip-item strong {
        font-size: var(--text-base);
        color: var(--neutral-800);
        font-weight: var(--fw-semibold);
    }

    /* Two Column Content Grid */
    .dashboard-columns {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 28px;
    }

    .list-card {
        background: var(--neutral-0);
        border: 1px solid var(--neutral-300);
        border-radius: var(--radius-md);
        padding: 20px;
        display: flex;
        flex-direction: column;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        height: 100%;
    }

    .list-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 14px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--neutral-200);
    }

    .list-card-title {
        font-size: var(--text-base);
        font-weight: var(--fw-bold);
        color: var(--neutral-900);
        display: flex;
        align-items: center;
        gap: 8px;
        line-height: 1.3;
        margin: 0;
    }

    .item-row {
        padding: 12px 0;
        border-bottom: 1px solid var(--neutral-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        transition: background-color 0.15s ease;
    }

    .item-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .item-meta {
        flex: 1;
        min-width: 0;
    }

    .item-meta h4 {
        font-size: var(--text-sm);
        font-weight: var(--fw-semibold);
        color: var(--neutral-900);
        margin-bottom: 4px;
        line-height: 1.35;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .item-meta p {
        font-size: 12px;
        color: var(--neutral-500);
        line-height: 1.4;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .deadline-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 8px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        background: var(--neutral-100);
        color: var(--neutral-600);
        border: 1px solid var(--neutral-300);
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

    .week-mini-badge {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        background: var(--blue-tint);
        color: var(--blue-700);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        flex-shrink: 0;
        border: 1px solid rgba(7, 81, 255, 0.18);
    }

    /* Progress Fills Base */
    .progress-fill-blue { background: var(--blue-600); }
    .progress-fill-green { background: var(--green-600); }
    .progress-fill-amber { background: #D97706; }
    .progress-fill-purple { background: #7E22CE; }

    /* Completion Card (Fase 4 - Selesai Magang) */
    .completion-card {
        background: #FAFDFB;
        border: 2px solid var(--green-600);
        border-radius: var(--radius-md);
        padding: 32px;
        box-shadow: 0 4px 14px rgba(22, 163, 74, 0.08);
    }

    .completion-grade-box {
        text-align: right;
        background: var(--neutral-0);
        border: 1px solid var(--neutral-300);
        border-radius: var(--radius-md);
        padding: 16px 24px;
    }

    .completion-grade-score {
        font-size: 36px;
        font-weight: 800;
        color: var(--green-700);
        line-height: 1.1;
    }

    .completion-notes-box {
        margin-top: 20px;
        padding: 16px;
        background: var(--neutral-0);
        border: 1px solid var(--neutral-200);
        border-left: 4px solid var(--green-600);
        border-radius: var(--radius-default);
    }

    /* Empty State Base */
    .dashboard-empty-state {
        text-align: center;
        padding: 36px 16px;
        color: var(--neutral-500);
        margin: auto 0;
    }

    .dashboard-empty-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--neutral-800);
        margin: 0 0 4px 0;
    }

    .dashboard-empty-subtext {
        font-size: 12px;
        color: var(--neutral-500);
        margin: 0;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .kpi-unified-card { grid-template-columns: repeat(2, 1fr); }
        .kpi-unified-item:nth-child(2)::after { display: none; }
        .kpi-unified-item:nth-child(1),
        .kpi-unified-item:nth-child(2) { border-bottom: 1px solid var(--neutral-200); }
        .dashboard-columns { grid-template-columns: 1fr; }
        .stepper-list { grid-template-columns: 1fr; }
        .hero-meta { align-items: flex-start; }
    }

    @media (max-width: 640px) {
        .kpi-unified-card { grid-template-columns: 1fr; }
        .kpi-unified-item::after { display: none; }
        .kpi-unified-item:not(:last-child) { border-bottom: 1px solid var(--neutral-200); }
    }

    /* Dark Mode Harmonization */
    [data-theme="dark"] .dashboard-hero {
        background: linear-gradient(95deg, rgba(15, 23, 42, 0.94) 0%, rgba(30, 58, 138, 0.88) 50%, rgba(6, 65, 204, 0.45) 100%),
                    url('{{ asset('images/dashboard_hero_bg.jpg') }}') right center / cover no-repeat;
        box-shadow: 0 6px 24px rgba(0, 0, 0, 0.35);
        border: 1px solid rgba(59, 130, 246, 0.25);
    }

    [data-theme="dark"] .hero-role-pill {
        background: rgba(255, 255, 255, 0.18);
        border: 1px solid rgba(255, 255, 255, 0.35);
        color: #ffffff;
    }

    [data-theme="dark"] .kpi-unified-card {
        background: #182234;
        border-color: #26354D;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.25);
    }

    [data-theme="dark"] .kpi-unified-item:hover {
        background-color: #1E293B;
    }

    [data-theme="dark"] .kpi-unified-item:not(:last-child)::after {
        background-color: #26354D;
    }

    [data-theme="dark"] .kpi-label {
        color: #94A3B8;
    }

    [data-theme="dark"] .kpi-value {
        color: #F8FAFC;
    }

    [data-theme="dark"] .micro-progress-bar {
        background: #26354D;
    }

    [data-theme="dark"] .kpi-subtext {
        color: #94A3B8;
    }

    [data-theme="dark"] .kpi-icon-blue {
        background: rgba(6, 65, 204, 0.35);
        border: 1px solid rgba(7, 81, 255, 0.4);
        color: #60A5FA;
    }

    [data-theme="dark"] .kpi-icon-green {
        background: rgba(16, 185, 129, 0.25);
        border: 1px solid rgba(34, 197, 94, 0.4);
        color: #34D399;
    }

    [data-theme="dark"] .kpi-icon-amber {
        background: rgba(217, 119, 6, 0.25);
        border: 1px solid rgba(245, 158, 11, 0.4);
        color: #FBBF24;
    }

    [data-theme="dark"] .kpi-icon-purple {
        background: rgba(147, 51, 234, 0.25);
        border: 1px solid rgba(168, 85, 247, 0.4);
        color: #C084FC;
    }

    /* Progress Fills in Dark Mode */
    [data-theme="dark"] .progress-fill-blue {
        background: #3B82F6 !important;
    }

    [data-theme="dark"] .progress-fill-green {
        background: #10B981 !important;
    }

    [data-theme="dark"] .progress-fill-amber {
        background: #F59E0B !important;
    }

    [data-theme="dark"] .progress-fill-purple {
        background: #A855F7 !important;
    }

    [data-theme="dark"] .onboarding-card {
        background: #182234;
        border-color: #26354D;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.25);
    }

    [data-theme="dark"] .step-item {
        background: #0F172A;
        border-color: #26354D;
        color: #CBD5E1;
    }

    [data-theme="dark"] .step-item.completed {
        background: rgba(6, 78, 59, 0.25);
        border-color: rgba(52, 211, 153, 0.4);
    }

    [data-theme="dark"] .step-item.active {
        background: rgba(30, 58, 138, 0.25);
        border-color: rgba(96, 165, 250, 0.4);
    }

    [data-theme="dark"] .step-title {
        color: #F8FAFC;
    }

    [data-theme="dark"] .step-desc {
        color: #94A3B8;
    }

    [data-theme="dark"] .step-item.active .step-title {
        color: #93C5FD;
    }

    [data-theme="dark"] .step-item.active .step-desc {
        color: #CBD5E1;
    }

    [data-theme="dark"] .step-item.completed .step-title {
        color: #6EE7B7;
    }

    [data-theme="dark"] .step-item.completed .step-desc {
        color: #CBD5E1;
    }

    /* Phase Selection Tracker (Fase 2) */
    [data-theme="dark"] .status-tracker-card {
        background: #182234;
        border-color: #26354D;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.25);
    }

    [data-theme="dark"] .info-strip {
        background: #0F172A;
        border: 1px solid #26354D;
    }

    [data-theme="dark"] .info-strip-item span {
        color: #94A3B8;
    }

    [data-theme="dark"] .info-strip-item strong {
        color: #F8FAFC;
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

    [data-theme="dark"] .item-row {
        border-bottom-color: #26354D;
    }

    [data-theme="dark"] .item-row:hover {
        background-color: rgba(30, 41, 59, 0.4);
    }

    [data-theme="dark"] .item-meta h4 {
        color: #F8FAFC;
    }

    [data-theme="dark"] .item-meta p {
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

    [data-theme="dark"] .week-mini-badge {
        background: rgba(6, 65, 204, 0.35);
        border-color: rgba(7, 81, 255, 0.4);
        color: #60A5FA;
    }

    [data-theme="dark"] .attention-banner {
        background: rgba(120, 53, 15, 0.35);
        border-color: rgba(245, 158, 11, 0.4);
        border-left-color: #F59E0B;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25);
    }

    [data-theme="dark"] .attention-title {
        color: #FCD34D !important;
    }

    [data-theme="dark"] .attention-desc {
        color: #FEF3C7 !important;
    }

    [data-theme="dark"] .attention-desc strong {
        color: #FDE68A !important;
    }

    [data-theme="dark"] .btn-attention-action {
        background: #D97706;
        color: #ffffff;
        box-shadow: 0 2px 10px rgba(217, 119, 6, 0.35);
    }

    [data-theme="dark"] .btn-attention-action:hover {
        background: #B45309;
        color: #ffffff;
    }

    [data-theme="dark"] .quick-action-btn {
        background: #182234;
        border-color: #26354D;
        color: #E2E8F0;
    }

    [data-theme="dark"] .quick-action-btn:hover {
        background: #1E293B;
        border-color: #3B82F6;
        color: #60A5FA;
    }

    [data-theme="dark"] .dashboard-empty-title {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .dashboard-empty-subtext {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .completion-card {
        background: rgba(6, 78, 59, 0.2) !important;
        border-color: rgba(52, 211, 153, 0.45) !important;
        box-shadow: 0 6px 24px rgba(0, 0, 0, 0.35);
    }

    [data-theme="dark"] .completion-grade-box {
        background: #182234 !important;
        border-color: #26354D !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    [data-theme="dark"] .completion-grade-score {
        color: #34D399 !important;
    }

    [data-theme="dark"] .completion-notes-box {
        background: #0F172A !important;
        border-color: #26354D !important;
        border-left-color: #34D399 !important;
    }
</style>
@endpush

@section('content')

<!-- 1. Gentelella Page Title Bar -->
<x-gentelella.page-title
    title="Dashboard Peserta"
    description="Pantau progres magang, penugasan bimbingan, pengisian logbook harian, dan evaluasi hasil."
>
    @if($fase === 'aktif' && $penempatan)
        <x-slot:actions>
            <a href="{{ route('peserta.logbook.index') }}" class="btn btn-sm btn-primary" style="display: inline-flex; align-items: center; gap: 0.4rem; background: #0641CC; color: #ffffff; border-radius: 8px; font-weight: 700; padding: 0.5rem 1rem; text-decoration: none; box-shadow: 0 2px 6px rgba(6, 65, 204, 0.2);">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                + Isi Logbook Mingguan
            </a>
            <a href="{{ route('peserta.tugas.index') }}" class="btn btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem; background: #ffffff; color: var(--neutral-700, #334155); border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; font-weight: 600; padding: 0.5rem 1rem; text-decoration: none;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                Lihat Daftar Tugas
            </a>
        </x-slot:actions>
    @endif
</x-gentelella.page-title>

{{-- 2. Hero Greeting Banner --}}
@if($fase === 'aktif' && $penempatan)
    <div class="dashboard-hero">
        <div class="hero-content">
            <div class="hero-greeting">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                    <span class="hero-status-pill">● Status Penempatan: Magang Aktif</span>
                </div>
                <h1>Selamat Datang, {{ $user->nama ?? $user->name }}! 👋</h1>
                <h2 class="hero-placement-title">{{ $penempatan->bidang->nama_bidang ?? 'Dinas Komunikasi dan Informatika Kabupaten Sleman' }}</h2>
                
                <div class="hero-meta-badges">
                    <div class="hero-meta-chip">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>Pembimbing: <strong>{{ $penempatan->pembimbing->nama ?? $penempatan->pembimbing->name ?? 'Belum Ditugaskan' }}</strong></span>
                    </div>
                    <div class="hero-meta-chip">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Periode: {{ \Carbon\Carbon::parse($penempatan->tgl_mulai ?? $penempatan->tanggal_mulai)->isoFormat('D MMM Y') }} &ndash; {{ \Carbon\Carbon::parse($penempatan->tgl_selesai ?? $penempatan->tanggal_selesai)->isoFormat('D MMM Y') }}</span>
                    </div>
                </div>
            </div>

            <div class="hero-meta">
                <div class="hero-date-badge">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</span>
                </div>
                <a href="{{ route('peserta.penempatan.surat-diterima.pratinjau', $penempatan) }}" class="hero-glass-btn" target="_blank">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-1.943-9.542-7z"></path>
                    </svg>
                    Pratinjau SK Diterima
                </a>
            </div>
        </div>
    </div>

    {{-- Banner Alert Catatan Perhatian --}}
    @if($logbookPerluRevisi > 0)
        <div class="attention-banner">
            <div style="display: flex; align-items: center; gap: 12px;">
                <span style="font-size: 20px; line-height: 1;">⚠️</span>
                <div>
                    <strong class="attention-title">Tindak Lanjut Diperlukan:</strong>
                    <span class="attention-desc">
                        Terdapat <strong>{{ $logbookPerluRevisi }}</strong> laporan logbook mingguan yang memerlukan perbaikan dari pembimbing lapangan.
                    </span>
                </div>
            </div>
            <a href="{{ route('peserta.logbook.index') }}" class="btn-attention-action">
                Perbaiki Logbook Sekarang &rarr;
            </a>
        </div>
    @endif
@else
    <div class="dashboard-hero">
        <div class="hero-content">
            <div class="hero-greeting">
                <h1>Selamat Datang, {{ $user->nama ?? $user->name }}! 👋</h1>
                <p>
                    Selamat datang di portal terpadu magang Dinas Komunikasi dan Informatika Kabupaten Sleman. Pantau status pendaftaran, kegiatan harian, tugas, dan evaluasi hasil kerja Anda secara terpusat.
                </p>
            </div>
            <div class="hero-meta">
                <div class="hero-date-badge">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</span>
                </div>
                <span class="hero-role-pill">Peran: Peserta Magang</span>
            </div>
        </div>
    </div>
@endif

{{-- 2. Modul Berdasarkan Fase --}}

{{-- FASE 1: ONBOARDING (Belum Mendaftar / Profil Baru) --}}
@if($fase === 'onboarding')
    <x-gentelella.x-panel
        title="Panduan Alur Pendaftaran Magang"
        subtitle="Ikuti langkah-langkah di bawah ini untuk memulai pendaftaran magang di Dinas Kominfo Sleman."
    >
        <div class="stepper-list">
            <div class="step-item {{ $profil ? 'completed' : 'active' }}">
                <div class="step-badge">{{ $profil ? '✓' : '1' }}</div>
                <div class="step-title">1. Lengkapi Biodata & Profil</div>
                <div class="step-desc">
                    Isi asal institusi/sekolah, NISN/NIM, jurusan, jenjang, kontak WhatsApp, dan kontak darurat.
                </div>
            </div>

            <div class="step-item {{ $profil ? 'active' : '' }}">
                <div class="step-badge">2</div>
                <div class="step-title">2. Pilih Periode & Kuota</div>
                <div class="step-desc">
                    Pilih bulan magang aktif yang kuotanya masih tersedia sesuai jurusan Anda dan buat draf pengajuan.
                </div>
            </div>

            <div class="step-item">
                <div class="step-badge">3</div>
                <div class="step-title">3. Unggah Berkas & Ajukan</div>
                <div class="step-desc">
                    Unggah surat pengantar, proposal, transkrip nilai, lalu kirim pengajuan untuk diverifikasi admin.
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            @if(!$profil)
                <a href="{{ route('peserta.profil.show') }}" class="btn btn-primary">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Lengkapi Biodata Sekarang
                </a>
            @else
                <a href="{{ route('peserta.pendaftaran.index') }}" class="btn btn-primary">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Buat Pengajuan Pendaftaran Magang
                </a>
                <a href="{{ route('peserta.profil.show') }}" class="btn btn-secondary">
                    Periksa Biodata Saya
                </a>
            @endif
        </div>
    </x-gentelella.x-panel>
@endif

{{-- FASE 2: SELEKSI PENDAFTARAN (Pendaftaran Aktif Berjalan) --}}
@if($fase === 'seleksi' && $pendaftaran)
    @php
        $status = $pendaftaran->status;
        $badgeClass = match($status) {
            \App\Enums\StatusPendaftaranEnum::Diterima => 'badge-success',
            \App\Enums\StatusPendaftaranEnum::Diajukan, \App\Enums\StatusPendaftaranEnum::WaitingList => 'badge-warning',
            \App\Enums\StatusPendaftaranEnum::PerluPerbaikan, \App\Enums\StatusPendaftaranEnum::Ditolak, \App\Enums\StatusPendaftaranEnum::Dibatalkan => 'badge-danger',
            default => 'badge-neutral',
        };
    @endphp

    <x-gentelella.x-panel
        title="Status Pendaftaran Magang Aktif"
        subtitle="Pengajuan nomor #{{ $pendaftaran->id }} sedang diproses dalam tahapan seleksi."
    >
        <x-slot:actions>
            <span class="badge {{ $badgeClass }}" style="font-size: 13px; padding: 6px 14px;">
                ● {{ $status->label() }}
            </span>
        </x-slot:actions>

        <div class="info-strip">
            <div class="info-strip-item">
                <span>Periode Magang:</span>
                <strong>{{ $pendaftaran->bulanMagang ? \Carbon\Carbon::parse($pendaftaran->bulanMagang->bulan_mulai)->isoFormat('MMMM Y') : '-' }}</strong>
            </div>
            <div class="info-strip-item">
                <span>Jurusan Terdaftar:</span>
                <strong>{{ $pendaftaran->kuota->jurusan->nama_jurusan ?? '-' }}</strong>
            </div>
            <div class="info-strip-item">
                <span>Durasi:</span>
                <strong>{{ $pendaftaran->durasi_bulan }} Bulan</strong>
            </div>
            <div class="info-strip-item">
                <span>Kelengkapan Berkas:</span>
                <strong>{{ $pendaftaran->dokumen->count() }} Berkas Terunggah</strong>
            </div>
        </div>

        @if($pendaftaran->catatan_revisi)
            <div class="alert alert-warning" style="margin-bottom: 16px;">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div>
                    <strong>Catatan Admin Verifikator:</strong> {{ $pendaftaran->catatan_revisi }}
                </div>
            </div>
        @endif

        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="{{ route('peserta.pendaftaran.detail', $pendaftaran) }}" class="btn btn-primary">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-1.943-9.542-7z"></path>
                </svg>
                Buka Berkas & Detail Pendaftaran
            </a>

            @if($pendaftaran->penempatan)
                <a href="{{ route('peserta.penempatan.surat-diterima', $pendaftaran->penempatan) }}" class="btn btn-secondary" target="_blank">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Unduh Surat Keterangan Diterima (PDF)
                </a>
            @endif
        </div>
    </x-gentelella.x-panel>
@endif

{{-- FASE 3: AKTIF MAGANG (Sedang Berjalan) --}}
@if($fase === 'aktif' && $penempatan)
    @php
        $persenTugas = $totalTugas > 0 ? min(100, round(($tugasSelesai / $totalTugas) * 100)) : 0;
        
        $tglMulaiCarbon = $penempatan ? \Carbon\Carbon::parse($penempatan->tgl_mulai ?? $penempatan->tanggal_mulai)->startOfDay() : null;
        $tglSelesaiCarbon = $penempatan ? \Carbon\Carbon::parse($penempatan->tgl_selesai ?? $penempatan->tanggal_selesai)->startOfDay() : null;
        $totalHariMagang = ($tglMulaiCarbon && $tglSelesaiCarbon) ? max(1, $tglMulaiCarbon->diffInDays($tglSelesaiCarbon)) : 1;
        $hariBerjalan = ($tglMulaiCarbon) ? max(0, min($totalHariMagang, (int)$tglMulaiCarbon->diffInDays(now()->startOfDay()))) : 0;
        $persenHariMagang = min(100, round(($hariBerjalan / $totalHariMagang) * 100));
    @endphp

    {{-- Quick Action Bar --}}
    <div class="quick-actions-bar">
        <a href="{{ route('peserta.logbook.index') }}" class="btn btn-primary" style="box-shadow: 0 4px 12px rgba(7, 81, 255, 0.22);">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            + Isi Logbook Mingguan
        </a>

        <a href="{{ route('peserta.tugas.index') }}" class="btn btn-secondary">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
            </svg>
            Lihat Daftar Tugas
        </a>

        <a href="{{ route('peserta.nilai.show') }}" class="btn btn-secondary">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
            </svg>
            Cek Lembar Nilai
        </a>
    </div>

    {{-- Gentelella Tile Count 4 Metrik Peserta Magang --}}
    <div class="row tile_count" style="margin-bottom: 24px;">
        <!-- Tile 1: Tugas Selesai -->
        <x-gentelella.tile-stat
            title="Tugas Selesai"
            value="{{ $tugasSelesai }} / {{ $totalTugas }}"
            icon="clipboard-check"
            trendText="{{ $persenTugas }}% Tuntas"
            trendType="{{ $persenTugas >= 80 ? 'success' : ($persenTugas >= 40 ? 'warning' : 'info') }}"
            subText="{{ $tugasBelum }} tugas dalam proses"
            col="col-md-3 col-sm-6"
        >
            <div class="micro-progress-bar" style="margin-top: 6px;">
                <div class="micro-progress-fill progress-fill-blue" style="width: {{ $persenTugas }}%;"></div>
            </div>
        </x-gentelella.tile-stat>

        <!-- Tile 2: Logbook Mingguan -->
        <x-gentelella.tile-stat
            title="Logbook Mingguan"
            value="{{ $totalLogbook }} Laporan"
            icon="book-open"
            trendText="{{ $logbookPerluRevisi > 0 ? $logbookPerluRevisi . ' Perlu Revisi' : 'Tuntas' }}"
            trendType="{{ $logbookPerluRevisi > 0 ? 'danger' : 'success' }}"
            subText="{{ $logbookPerluRevisi > 0 ? 'Perlu perbaikan segera' : 'Aktivitas tercatat rapi' }}"
            col="col-md-3 col-sm-6"
        >
            <div class="micro-progress-bar" style="margin-top: 6px;">
                <div class="micro-progress-fill progress-fill-green" style="width: 100%;"></div>
            </div>
        </x-gentelella.tile-stat>

        <!-- Tile 3: Sisa Hari Magang -->
        <x-gentelella.tile-stat
            title="Sisa Hari Magang"
            value="{{ $sisaHari !== null ? $sisaHari . ' Hari' : '-' }}"
            icon="calendar"
            trendText="Hari ke-{{ $hariBerjalan }}"
            trendType="{{ $sisaHari !== null && $sisaHari <= 7 ? 'warning' : 'info' }}"
            subText="Dari total {{ $totalHariMagang }} hari ({{ $persenHariMagang }}%)"
            col="col-md-3 col-sm-6"
        >
            <div class="micro-progress-bar" style="margin-top: 6px;">
                <div class="micro-progress-fill progress-fill-amber" style="width: {{ $persenHariMagang }}%;"></div>
            </div>
        </x-gentelella.tile-stat>

        <!-- Tile 4: Status Evaluasi -->
        <x-gentelella.tile-stat
            title="Status Evaluasi"
            value="{{ $isFinalNilai ? ($penilaian->predikat ?? 'A') . ' (' . number_format($penilaian->nilai_akhir, 1) . ')' : 'Dalam Proses' }}"
            icon="award"
            trendText="{{ $isFinalNilai ? 'Nilai final resmi' : '5 rubrik dinas' }}"
            trendType="{{ $isFinalNilai ? 'success' : 'neutral' }}"
            subText="{{ $isFinalNilai ? 'Evaluasi tuntas' : 'Penilaian pembimbing' }}"
            col="col-md-3 col-sm-6"
        >
            <div class="micro-progress-bar" style="margin-top: 6px;">
                <div class="micro-progress-fill progress-fill-purple" style="width: {{ $isFinalNilai ? 100 : 50 }}%;"></div>
            </div>
        </x-gentelella.tile-stat>
    </div>

    {{-- 2 Kolom Gentelella: Tugas Mendesak & Logbook Terbaru --}}
    <div class="row">
        {{-- Kolom Tugas --}}
        <div class="col-md-6 col-sm-12">
            <x-gentelella.x-panel
                title="Tugas Magang Mendesak"
                subtitle="Daftar tugas bimbingan yang membutuhkan penyelesaian segera"
            >
                <x-slot:actions>
                    <a href="{{ route('peserta.tugas.index') }}" style="font-size: 13px; color: #0641CC; text-decoration: none; font-weight: 600;">Lihat Semua &rarr;</a>
                </x-slot:actions>

                @if($tugasMendesak->isNotEmpty())
                    @foreach($tugasMendesak as $t)
                        @php
                            $deadlineCarbon = \Carbon\Carbon::parse($t->deadline)->startOfDay();
                            $diffDays = now()->startOfDay()->diffInDays($deadlineCarbon, false);
                        @endphp
                        <div class="item-row">
                            <div class="item-meta">
                                <h4>{{ $t->judul }}</h4>
                                <p style="display: flex; align-items: center; gap: 8px; margin-top: 4px;">
                                    @if($diffDays < 0)
                                        <span class="deadline-pill is-urgent">Lewat Tenggat</span>
                                    @elseif($diffDays === 0)
                                        <span class="deadline-pill is-urgent">⚡ Hari Ini</span>
                                    @elseif($diffDays === 1)
                                        <span class="deadline-pill is-urgent">⚠️ Besok</span>
                                    @elseif($diffDays <= 3)
                                        <span class="deadline-pill is-warning">⏳ {{ $diffDays }} Hari Lagi</span>
                                    @else
                                        <span class="deadline-pill">📅 {{ $deadlineCarbon->isoFormat('D MMM') }}</span>
                                    @endif
                                    <span>&bull; {{ $t->kategori ?? 'Tugas Lapangan' }}</span>
                                </p>
                            </div>
                            <div>
                                <a href="{{ route('peserta.tugas.show', $t) }}" class="btn btn-secondary" style="height: 32px; padding: 0 12px; font-size: 12px; white-space: nowrap;">
                                    Kerjakan
                                </a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="dashboard-empty-state">
                        <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin: 0 auto 12px auto; color: var(--green-600);">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="dashboard-empty-title">Semua tugas selesai!</p>
                        <p class="dashboard-empty-subtext">Tidak ada tugas yang menunggu pengerjaan saat ini.</p>
                    </div>
                @endif
            </x-gentelella.x-panel>
        </div>

        {{-- Kolom Logbook --}}
        <div class="col-md-6 col-sm-12">
            <x-gentelella.x-panel
                title="Logbook Mingguan Terkini"
                subtitle="Catatan kemajuan dan laporan kegiatan mingguan"
            >
                <x-slot:actions>
                    <a href="{{ route('peserta.logbook.index') }}" style="font-size: 13px; color: #0641CC; text-decoration: none; font-weight: 600;">Lihat Semua &rarr;</a>
                </x-slot:actions>

                @if($logbookTerbaru->isNotEmpty())
                    @foreach($logbookTerbaru as $lb)
                        @php
                            $statusValue = $lb->status->value ?? (string)$lb->status;
                        @endphp
                        <div class="item-row">
                            <div style="display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0;">
                                <span class="week-mini-badge">
                                    MG{{ $lb->minggu_ke ?? $loop->iteration }}
                                </span>
                                <div class="item-meta">
                                    <h4>Minggu ke-{{ $lb->minggu_ke ?? ($loop->iteration) }} &bull; {{ \Carbon\Carbon::parse($lb->tanggal)->isoFormat('D MMM Y') }}</h4>
                                    <p>{{ \Illuminate\Support\Str::limit($lb->kegiatan, 65) }}</p>
                                </div>
                            </div>
                            <div>
                                @if($statusValue === 'perlu_revisi' || $statusValue === 'ditolak')
                                    <span class="badge badge-danger">Perlu Revisi</span>
                                @elseif($statusValue === 'disetujui')
                                    <span class="badge badge-success">Disetujui</span>
                                @else
                                    <span class="badge badge-info">Terkirim</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="dashboard-empty-state">
                        <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin: 0 auto 12px auto; color: var(--neutral-400);">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <p class="dashboard-empty-title">Belum ada catatan logbook mingguan</p>
                        <p class="dashboard-empty-subtext">Catat aktivitas mingguan magang Anda secara rutin.</p>
                    </div>
                @endif
            </x-gentelella.x-panel>
        </div>
    </div>
@endif

{{-- FASE 4: SELESAI MAGANG (Pasca Penilaian / Kelulusan) --}}
@if($fase === 'selesai' && $penempatan)
    <x-gentelella.x-panel
        title="Penyelesaian Program Magang Dinas Kominfo Sleman"
        subtitle="🎉 Selamat! Masa Magang Telah Selesai"
    >
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px;">
            <div>
                <span class="badge badge-success" style="font-size: 14px; padding: 6px 16px; margin-bottom: 12px;">
                    🎉 Selamat! Masa Magang Telah Selesai
                </span>
                <p style="font-size: 14px; color: var(--neutral-600); max-width: 650px; line-height: 1.5; margin-top: 8px;">
                    Anda telah menuntaskan seluruh kewajiban magang pada <strong>{{ $penempatan->bidang->nama_bidang ?? 'Dinas Kominfo Sleman' }}</strong> bersama pembimbing lapangan <strong>{{ $penempatan->pembimbing->nama ?? $penempatan->pembimbing->name ?? '-' }}</strong>.
                </p>
            </div>

            @if($isFinalNilai)
                <div class="completion-grade-box">
                    <span style="font-size: 12px; font-weight: 600; color: var(--neutral-500); text-transform: uppercase;">Predikat Kelulusan</span>
                    <div class="completion-grade-score">
                        {{ $penilaian->predikat ?? 'A' }}
                    </div>
                    <span style="font-size: 13px; color: var(--neutral-600);">Skor: {{ number_format($penilaian->nilai_akhir, 2) }} / 100</span>
                </div>
            @endif
        </div>

        @if($isFinalNilai && $penilaian->catatan)
            <div class="completion-notes-box">
                <strong style="font-size: 13px; color: var(--neutral-700); display: block; margin-bottom: 4px;">Catatan & Evaluasi Pembimbing:</strong>
                <p style="font-size: 14px; color: var(--neutral-800); font-style: italic;">"{{ $penilaian->catatan }}"</p>
            </div>
        @endif

        <div style="margin-top: 24px; display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="{{ route('peserta.nilai.show') }}" class="btn btn-primary">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Lihat Rincian Nilai 5 Rubrik
            </a>

            @if($isFinalNilai)
                <a href="{{ route('peserta.nilai.unduh') }}" class="btn btn-secondary">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Unduh Lembar Nilai Resmi (PDF)
                </a>
            @endif
        </div>
    </x-gentelella.x-panel>
@endif

@endsection

