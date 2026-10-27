@extends('layouts.peserta')

@section('title', 'Logbook & Jurnal Mingguan Peserta')
@section('page_title', 'Logbook Mingguan')

@push('styles')
<style>
    /* Layout Container Full-Width Bersih */
    .logbook-wrapper {
        max-width: 1040px;
        margin: 0 auto;
    }

    .card-modern {
        background: #ffffff;
        border: 1px solid var(--neutral-300);
        border-radius: var(--radius-lg, 12px);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        padding: 24px;
    }

    /* Banner Penempatan Magang */
    .banner-penempatan {
        background: linear-gradient(135deg, #1b3860 0%, #162e4f 100%);
        border-radius: 16px;
        padding: 32px 36px;
        color: #ffffff;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        box-shadow: 0 4px 20px -2px rgba(27, 56, 96, 0.18);
        border: 1px solid rgba(255, 255, 255, 0.12);
        position: relative;
        overflow: hidden;
        min-height: 185px;
    }

    .banner-penempatan::after {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 85% 50%, rgba(255, 255, 255, 0.08) 0%, transparent 60%);
        pointer-events: none;
    }

    .banner-content-wrap,
    .banner-side-stat {
        position: relative;
        z-index: 2;
    }

    .banner-title {
        font-size: var(--text-2xl);
        font-weight: var(--fw-bold);
        color: #ffffff;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
        line-height: 1.3;
    }

    .banner-meta {
        font-size: var(--text-base);
        color: rgba(255, 255, 255, 0.92);
        display: flex;
        flex-wrap: wrap;
        gap: 18px;
        line-height: 1.5;
    }

    .banner-meta-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* Weekly Progress Tracker Card */
    .progress-tracker-card {
        background: #ffffff;
        border: 1px solid var(--neutral-300);
        border-radius: var(--radius-lg, 12px);
        padding: 22px 24px;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    }

    .progress-tracker-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 12px;
    }

    .progress-tracker-title {
        font-size: var(--text-base);
        font-weight: var(--fw-bold);
        color: var(--neutral-900);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .progress-bar-wrap {
        height: 10px;
        background: var(--neutral-200);
        border-radius: 999px;
        overflow: hidden;
        margin-bottom: 16px;
    }

    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--blue-600) 0%, #3B82F6 100%);
        border-radius: 999px;
        transition: width 0.4s ease;
    }

    /* Weekly Stepper Scroll */
    .weekly-stepper-wrap {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding: 4px 2px 8px 2px;
        scrollbar-width: thin;
        scrollbar-color: var(--neutral-400) transparent;
    }

    .weekly-stepper-wrap::-webkit-scrollbar {
        height: 6px;
    }

    .weekly-stepper-wrap::-webkit-scrollbar-track {
        background: transparent;
    }

    .weekly-stepper-wrap::-webkit-scrollbar-thumb {
        background: var(--neutral-400);
        border-radius: 999px;
    }

    .weekly-stepper-wrap::-webkit-scrollbar-thumb:hover {
        background: var(--neutral-500);
    }

    .week-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: var(--text-xs);
        font-weight: var(--fw-semibold);
        white-space: nowrap;
        background: var(--neutral-100);
        color: var(--neutral-700);
        border: 1px solid var(--neutral-300);
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
    }

    .week-chip:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    }

    .week-chip.is-active-week {
        border-color: var(--blue-600);
        background: var(--blue-tint);
        color: var(--blue-700);
        font-weight: 700;
        box-shadow: 0 0 0 2px rgba(7, 81, 255, 0.2);
    }

    .week-chip.is-done {
        background: #ECFDF5;
        border-color: #A7F3D0;
        color: #065F46;
    }

    .week-chip.is-waiting {
        background: #EFF6FF;
        border-color: #BFDBFE;
        color: #1E40AF;
    }

    .week-chip.is-revisi {
        background: #FEF3C7;
        border-color: #FDE68A;
        color: #92400E;
    }

    .weekly-card.highlight-card {
        border-color: var(--blue-600) !important;
        box-shadow: 0 0 0 3px rgba(7, 81, 255, 0.25), 0 8px 24px rgba(7, 81, 255, 0.15) !important;
        transition: all 0.3s ease;
    }

    /* Smart Reminder Banner */
    .smart-reminder-banner {
        background: #EFF6FF;
        border: 1px solid #BFDBFE;
        border-left: 4px solid var(--blue-600);
        border-radius: var(--radius-lg, 12px);
        padding: 18px 24px;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }

    .reminder-content {
        display: flex;
        align-items: flex-start;
        gap: 14px;
    }

    .reminder-icon-box {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: var(--blue-tint);
        color: var(--blue-600);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Action Bar */
    .action-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 16px;
    }

    /* Search & Collapse Toolbar */
    .logbook-filter-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 20px;
        padding-bottom: 14px;
        border-bottom: 1px solid var(--neutral-200);
    }

    .logbook-search-box {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1;
        max-width: 440px;
    }

    .search-input-wrapper {
        position: relative;
        width: 100%;
        display: flex;
        align-items: center;
    }

    .search-input-wrapper .search-icon {
        position: absolute;
        left: 12px;
        color: var(--neutral-400);
        pointer-events: none;
    }

    .search-logbook-input {
        width: 100%;
        height: 38px;
        padding: 0 34px 0 36px;
        border: 1px solid var(--neutral-300);
        border-radius: 999px;
        font-size: var(--text-xs);
        color: var(--neutral-900);
        background: #ffffff;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
        outline: none;
        box-sizing: border-box;
    }

    .search-logbook-input:focus {
        border-color: var(--blue-600);
        box-shadow: 0 0 0 3px rgba(7, 81, 255, 0.12);
    }

    .search-logbook-input::placeholder {
        color: var(--neutral-400);
    }

    .btn-clear-search {
        position: absolute;
        right: 8px;
        background: none;
        border: none;
        font-size: 16px;
        line-height: 1;
        color: var(--neutral-400);
        cursor: pointer;
        padding: 4px 6px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-clear-search:hover {
        color: var(--neutral-700);
    }

    .search-result-count {
        font-size: 11px;
        font-weight: 600;
        color: var(--neutral-600);
        white-space: nowrap;
        background: var(--neutral-100);
        padding: 4px 10px;
        border-radius: 999px;
        border: 1px solid var(--neutral-300);
    }

    .collapse-toggle-group {
        display: flex;
        gap: 8px;
    }

    .btn-toggle-all {
        background: none;
        border: 1px solid var(--neutral-300);
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        color: var(--neutral-600);
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .btn-toggle-all:hover {
        background: var(--neutral-100);
        color: var(--neutral-900);
        border-color: var(--neutral-400);
    }

    /* Kartu Logbook Mingguan Accordion */
    .weekly-card {
        background: #ffffff;
        border: 1px solid var(--neutral-300);
        border-radius: var(--radius-lg, 12px);
        margin-bottom: 14px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
        overflow: hidden;
    }

    .weekly-card:hover {
        border-color: #BFDBFE;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    }

    .weekly-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        padding: 16px 20px;
        cursor: pointer;
        user-select: none;
        background: #ffffff;
        transition: background-color 0.15s ease;
    }

    .weekly-card-header:hover {
        background-color: var(--neutral-100);
    }

    .weekly-card.is-expanded .weekly-card-header {
        border-bottom: 1px solid var(--neutral-200);
    }

    .weekly-collapsed-preview {
        display: block;
        font-size: 13px;
        color: var(--neutral-500);
        margin-top: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 520px;
    }

    .weekly-card.is-expanded .weekly-collapsed-preview {
        display: none;
    }

    .btn-chevron-toggle {
        background: none;
        border: none;
        cursor: pointer;
        padding: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--neutral-400);
        transition: color 0.15s ease;
        border-radius: 6px;
    }

    .btn-chevron-toggle:hover {
        color: var(--neutral-800);
        background: var(--neutral-200);
    }

    .chevron-icon {
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .weekly-card.is-expanded .chevron-icon {
        transform: rotate(180deg);
    }

    .weekly-card-content {
        padding: 20px;
        display: none;
        background: #ffffff;
    }

    .weekly-card.is-expanded .weekly-card-content {
        display: block;
    }

    .weekly-badge-box {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .week-number-pill {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: var(--blue-tint);
        color: var(--blue-700);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        line-height: 1;
        flex-shrink: 0;
        border: 1px solid rgba(7, 81, 255, 0.2);
    }

    .weekly-card-title {
        font-size: var(--text-lg);
        font-weight: var(--fw-bold);
        color: var(--neutral-900);
        line-height: 1.35;
        margin-bottom: 4px;
    }

    .weekly-card-meta {
        font-size: var(--text-sm);
        color: var(--neutral-600);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .weekly-body {
        font-size: var(--text-base);
        line-height: 1.65;
        color: var(--neutral-800);
        white-space: pre-line;
    }

    .review-callout {
        margin-top: 16px;
        padding: 14px 18px;
        background: #FEF3C7;
        border-left: 4px solid #D97706;
        border-radius: 6px;
        font-size: var(--text-base);
        color: #92400E;
        line-height: 1.5;
    }

    .weekly-attachments {
        margin-top: 16px;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        padding-top: 14px;
        border-top: 1px dashed var(--neutral-200);
    }

    .attachment-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        background: var(--neutral-100);
        border: 1px solid var(--neutral-300);
        border-radius: 6px;
        font-size: var(--text-sm);
        font-weight: var(--fw-semibold);
        color: var(--neutral-800);
        text-decoration: none;
        transition: all 0.15s ease;
    }

    .attachment-pill:hover {
        background: var(--blue-tint);
        border-color: var(--blue-600);
        color: var(--blue-700);
    }

    /* Modal Form Styling */
    .modal-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(15, 23, 42, 0.65);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1050;
        padding: 20px;
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
    }

    .modal-backdrop.is-open {
        display: flex;
    }

    .modal-box {
        background: #ffffff;
        border-radius: 16px;
        width: 100%;
        max-width: 640px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 25px 60px -15px rgba(7, 81, 255, 0.2), 0 10px 25px -5px rgba(15, 23, 42, 0.12);
        border: 1px solid rgba(191, 219, 254, 0.8);
        animation: modalFadeIn 0.22s ease-out;
    }

    @keyframes modalFadeIn {
        from { opacity: 0; transform: translateY(16px) scale(0.97); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    .modal-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--neutral-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #ffffff;
        border-top-left-radius: 16px;
        border-top-right-radius: 16px;
    }

    .modal-close-btn {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: transparent;
        border: none;
        font-size: 20px;
        color: var(--neutral-500);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
        transition: background-color 0.15s ease, color 0.15s ease;
    }

    .modal-close-btn:hover {
        background: var(--neutral-150);
        color: var(--neutral-800);
    }

    .modal-body {
        padding: 24px;
    }

    .modal-footer {
        padding: 16px 24px;
        border-top: 1px solid var(--neutral-200);
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 12px;
        background: #FAFBFD;
        border-bottom-left-radius: 16px;
        border-bottom-right-radius: 16px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 18px;
    }

    .form-label {
        font-size: var(--text-sm);
        font-weight: var(--fw-semibold);
        color: var(--neutral-800);
        display: flex;
        align-items: center;
        justify-content: space-between;
        line-height: 1.3;
    }

    .form-control {
        height: 42px;
        padding: 0 14px;
        border: 1.5px solid #CBD5E1;
        border-radius: 8px;
        font-size: var(--text-sm);
        color: var(--neutral-900);
        background: #ffffff;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
        font-family: inherit;
        box-sizing: border-box;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--blue-600);
        box-shadow: 0 0 0 3px rgba(7, 81, 255, 0.14);
    }

    textarea.form-control {
        height: auto;
        padding: 12px 14px;
        line-height: 1.5;
        resize: vertical;
    }

    .logbook-select-placeholder {
        padding: 28px 20px;
        background: #F8FAFC;
        border: 1.5px dashed #CBD5E1;
        border-radius: 12px;
        text-align: center;
        margin-top: 4px;
    }

    .tips-box-bottom {
        background: #FAFBFD;
        border: 1px solid #DBEAFE;
        border-radius: 10px;
        padding: 14px 16px;
        margin-top: 20px;
        margin-bottom: 4px;
    }

    .char-counter {
        font-size: 0.75rem;
        color: var(--neutral-500);
        text-align: right;
        margin-top: 4px;
    }

    /* Dark Mode Harmonization ala Menu Tugas */
    [data-theme="dark"] .card-modern,
    [data-theme="dark"] .progress-tracker-card,
    [data-theme="dark"] .weekly-card {
        background: #182234 !important;
        border-color: #26354D !important;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.25) !important;
    }

    [data-theme="dark"] .weekly-card-header {
        background: #182234 !important;
        border-bottom-color: #26354D !important;
    }

    [data-theme="dark"] .weekly-card-content {
        background: #182234 !important;
        border-top-color: #26354D !important;
    }

    [data-theme="dark"] .weekly-card.is-expanded {
        border-color: #2563EB !important;
        box-shadow: 0 4px 20px rgba(37, 99, 235, 0.15) !important;
    }

    [data-theme="dark"] .weekly-card-title {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .weekly-card-meta {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .weekly-body {
        color: #CBD5E1 !important;
    }

    [data-theme="dark"] .review-callout {
        background: rgba(120, 53, 15, 0.35) !important;
        border-left-color: #F59E0B !important;
        color: #FEF3C7 !important;
    }

    [data-theme="dark"] .week-number-pill {
        background: rgba(6, 65, 204, 0.35) !important;
        border-color: rgba(7, 81, 255, 0.4) !important;
        color: #60A5FA !important;
    }

    /* ========================================================
       MODAL LOGBOOK DARK THEME HARMONIZATION
       ======================================================== */
    [data-theme="dark"] .modal-backdrop {
        background: rgba(0, 0, 0, 0.75);
    }

    [data-theme="dark"] .modal-box {
        background: #182234 !important;
        border: 1px solid #26354D !important;
        color: #F8FAFC !important;
        box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.7), 0 10px 25px -5px rgba(0, 0, 0, 0.5) !important;
    }

    [data-theme="dark"] .modal-header {
        background: #182234 !important;
        border-bottom-color: #26354D !important;
    }

    [data-theme="dark"] .modal-header h3 {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .modal-header p {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .modal-close-btn {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .modal-close-btn:hover {
        background: #1F2937 !important;
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .modal-body {
        background: #182234 !important;
    }

    [data-theme="dark"] .logbook-select-placeholder {
        background: #0F172A !important;
        border-color: #26354D !important;
    }

    [data-theme="dark"] .logbook-select-placeholder h4 {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .logbook-select-placeholder p {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] #alertMingguSudahDiisi {
        background: rgba(30, 58, 138, 0.3) !important;
        border-color: rgba(96, 165, 250, 0.4) !important;
    }

    [data-theme="dark"] #alertMingguSudahDiisi strong {
        color: #93C5FD !important;
    }

    [data-theme="dark"] #alertMingguSudahDiisi p {
        color: #CBD5E1 !important;
    }

    [data-theme="dark"] .tips-box-bottom {
        background: #0F172A !important;
        border-color: #26354D !important;
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .tips-box-bottom strong {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .tips-box-bottom p {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .char-counter {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .modal-footer {
        background: #0F172A !important;
        border-top-color: #26354D !important;
    }

    [data-theme="dark"] .btn-secondary {
        background: #1E293B !important;
        border: 1px solid #334155 !important;
        color: #CBD5E1 !important;
    }

    [data-theme="dark"] .btn-secondary:hover {
        background: #334155 !important;
        color: #FFFFFF !important;
    }

    [data-theme="dark"] .form-control {
        background: #0F172A !important;
        border-color: #3E516E !important;
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .form-control:focus {
        border-color: #2563EB !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.25) !important;
    }

    [data-theme="dark"] .form-label {
        color: #E2E8F0 !important;
    }

    [data-theme="dark"] .progress-tracker-title {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .smart-reminder-banner {
        background: rgba(30, 58, 138, 0.25) !important;
        border-color: rgba(59, 130, 246, 0.4) !important;
        border-left-color: #3B82F6 !important;
    }

    [data-theme="dark"] .smart-reminder-banner h4 {
        color: #93C5FD !important;
    }

    [data-theme="dark"] .smart-reminder-banner p {
        color: #CBD5E1 !important;
    }

    [data-theme="dark"] .search-logbook-input {
        background: #0F172A !important;
        border-color: #26354D !important;
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .search-logbook-input:focus {
        border-color: #3B82F6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important;
    }

    [data-theme="dark"] .btn-toggle-all {
        background: #0F172A !important;
        border-color: #26354D !important;
        color: #CBD5E1 !important;
    }

    [data-theme="dark"] .btn-toggle-all:hover {
        background: #182234 !important;
        color: #FFFFFF !important;
        border-color: #3B82F6 !important;
    }

    [data-theme="dark"] .week-chip {
        background: #0F172A !important;
        border-color: #26354D !important;
        color: #CBD5E1 !important;
    }

    [data-theme="dark"] .week-chip.is-done {
        background: rgba(6, 78, 59, 0.35) !important;
        border-color: rgba(52, 211, 153, 0.4) !important;
        color: #6EE7B7 !important;
    }

    [data-theme="dark"] .week-chip.is-waiting {
        background: rgba(30, 58, 138, 0.35) !important;
        border-color: rgba(96, 165, 250, 0.4) !important;
        color: #93C5FD !important;
    }

    [data-theme="dark"] .week-chip.is-revisi {
        background: rgba(120, 53, 15, 0.35) !important;
        border-color: rgba(245, 158, 11, 0.4) !important;
        color: #FCD34D !important;
    }

    [data-theme="dark"] .search-result-count {
        background: #0F172A !important;
        border-color: #26354D !important;
        color: #94A3B8 !important;
    }
</style>
@endpush

@section('content')
<div class="logbook-wrapper">
    <x-gentelella.page-title title="Logbook & Jurnal Mingguan Peserta" description="Catat ringkasan capaian aktivitas magang dan pantau evaluasi mingguan pembimbing lapangan.">
        <x-slot:actions>
            @if($penempatan)
                <button type="button" class="btn btn-primary" onclick="openTambahModal()" style="height: 40px; padding: 0 16px; display: inline-flex; align-items: center; gap: 8px;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>+ Isi Logbook</span>
                </button>
            @endif
        </x-slot:actions>
    </x-gentelella.page-title>

    @if(!$penempatan)
        <div class="card-modern" style="border-left: 4px solid var(--amber-700); background: var(--amber-100); margin-bottom: 24px;">
            <div style="display: flex; align-items: flex-start; gap: 16px;">
                <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--amber-700); flex-shrink: 0; margin-top: 2px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                <div>
                    <h3 style="font-size: 1.0625rem; font-weight: 700; color: var(--amber-700); margin-bottom: 4px;">
                        Menu Logbook Mingguan Terkunci
                    </h3>
                    <p style="font-size: 0.875rem; color: var(--neutral-700); line-height: 1.5;">
                        Anda belum memiliki penempatan magang aktif. Fitur pelaporan logbook mingguan ini akan terbuka secara otomatis setelah status pendaftaran magang Anda berstatus <strong>Diterima</strong> dan masa penempatan magang telah dimulai.
                    </p>
                </div>
            </div>
        </div>
    @else
        {{-- Banner Info Penempatan Magang Aktif --}}
        <div class="banner-penempatan">
            <div class="banner-content-wrap">
                <div style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(8px); border-radius: 20px; font-size: var(--text-2xs); font-weight: var(--fw-bold); text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 8px; border: 1px solid rgba(255,255,255,0.2);">
                    <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #4ADE80;"></span>
                    Penempatan Magang Aktif
                </div>
                <h1 class="banner-title" style="text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);">
                    {{ $penempatan->bidang?->nama_bidang ?? 'Dinas Komunikasi dan Informatika' }}
                </h1>
                <div class="banner-meta" style="text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);">
                    <span class="banner-meta-item">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Pembimbing: <strong>{{ $penempatan->pembimbing?->name ?? 'Belum Ditentukan' }}</strong>
                    </span>
                    <span class="banner-meta-item">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Periode: {{ \Carbon\Carbon::parse($penempatan->tgl_mulai)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($penempatan->tgl_selesai)->translatedFormat('d M Y') }}
                    </span>
                </div>
            </div>
            <div class="banner-side-stat" style="background: rgba(15, 23, 42, 0.45); backdrop-filter: blur(10px); padding: 14px 22px; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.28); text-align: right;">
                <div style="font-size: var(--text-2xs); text-transform: uppercase; font-weight: 600; opacity: 0.95; letter-spacing: 0.04em; color: rgba(255, 255, 255, 0.9);">Minggu Berjalan Saat Ini</div>
                <div style="font-size: var(--text-2xl); font-weight: var(--fw-bold); margin-top: 2px;">Minggu ke-{{ $mingguAktif }}</div>
                <div style="font-size: var(--text-xs); opacity: 0.85; margin-top: 2px;">dari total {{ $totalMinggu }} minggu</div>
            </div>
        </div>

        {{-- Weekly Progress Tracker (User-Friendly Component) --}}
        <div class="progress-tracker-card">
            <div class="progress-tracker-header">
                <div class="progress-tracker-title">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--blue-600);">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    Progres Pelaporan Logbook Mingguan
                </div>
                <div style="font-size: var(--text-sm); font-weight: 600; color: var(--neutral-700);">
                    <span style="color: var(--blue-700); font-size: var(--text-base); font-weight: 800;">{{ $totalTerisi }}</span> dari {{ $totalMinggu }} Minggu Terisi ({{ $persentaseTerisi }}%)
                </div>
            </div>

            <div class="progress-bar-wrap">
                <div class="progress-bar-fill" style="width: {{ max(4, $persentaseTerisi) }}%;"></div>
            </div>

            <div class="weekly-stepper-wrap">
                @foreach($daftarMinggu as $m)
                    @php
                        $chipClass = '';
                        $statusIcon = '🗓️';
                        $statusLabel = 'Belum Diisi';
                        if ($m['status'] === 'disetujui') {
                            $chipClass = 'is-done';
                            $statusIcon = '✓';
                            $statusLabel = 'Disetujui';
                        } elseif ($m['status'] === 'terkirim') {
                            $chipClass = 'is-waiting';
                            $statusIcon = '⏳';
                            $statusLabel = 'Terkirim';
                        } elseif ($m['status'] === 'perlu_revisi') {
                            $chipClass = 'is-revisi';
                            $statusIcon = '⚠️';
                            $statusLabel = 'Perlu Revisi';
                        } elseif ($m['is_aktif']) {
                            $chipClass = 'is-active-week';
                            $statusIcon = '📍';
                            $statusLabel = 'Minggu Berjalan';
                        }
                    @endphp
                    <button type="button" 
                            class="week-chip {{ $chipClass }}" 
                            onclick="handleWeekChipClick({{ $m['minggu_ke'] }}, '{{ $m['status'] }}', {{ $m['is_aktif'] ? 'true' : 'false' }})"
                            title="Minggu {{ $m['minggu_ke'] }}: {{ $m['label_periode'] }} ({{ $statusLabel }}) • Klik untuk lihat / isi">
                        <span>{{ $statusIcon }}</span>
                        <span>Minggu {{ $m['minggu_ke'] }}</span>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Smart Reminder Banner jika minggu aktif belum diisi --}}
        @if(!$mingguAktifSudahDiisi)
            @php
                $periodeAktif = $daftarMinggu[$mingguAktif - 1]['label_periode'] ?? '';
            @endphp
            <div class="smart-reminder-banner">
                <div class="reminder-content">
                    <div class="reminder-icon-box">
                        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <div style="font-size: var(--text-base); font-weight: 700; color: #1E3A8A; margin-bottom: 2px;">
                            Saatnya Mengisi Laporan Minggu ke-{{ $mingguAktif }} ({{ $periodeAktif }})
                        </div>
                        <div style="font-size: var(--text-sm); color: #3B82F6; line-height: 1.4;">
                            Catat capaian pekerjaan dan aktivitas magang Anda minggu ini sebelum akhir pekan agar pembimbing dapat memantau perkembangan Anda.
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-primary" onclick="openTambahModal({{ $mingguAktif }})" style="box-shadow: 0 4px 12px rgba(7, 81, 255, 0.25); white-space: nowrap;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                    </svg>
                    + Isi Logbook Minggu Ini
                </button>
            </div>
        @endif

        {{-- Action Bar --}}
        <div class="action-bar">
            <div>
                <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Riwayat Jurnal Mingguan</h2>
                <p style="font-size: 0.875rem; color: var(--neutral-600); margin: 4px 0 0 0;">Daftar aktivitas dan evaluasi mingguan yang telah Anda laporkan kepada pembimbing lapangan</p>
            </div>

            <div style="display: flex; align-items: center; gap: 12px;">
                <span class="badge badge-info" style="font-size: 0.8125rem; padding: 6px 12px;">
                    Total: {{ $logbooks->total() }} Laporan
                </span>
                <button type="button" class="btn btn-primary" onclick="openTambahModal()" style="height: 40px; padding: 0 16px;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                    </svg>
                    + Isi Logbook Mingguan
                </button>
            </div>
        </div>

        {{-- Search & Collapse Control Bar --}}
        @if($logbooks->isNotEmpty())
            <div class="logbook-filter-bar">
                <div class="logbook-search-box">
                    <div class="search-input-wrapper">
                        <svg class="search-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"></path>
                        </svg>
                        <input type="text" 
                               id="inputSearchLogbook" 
                               class="search-logbook-input" 
                               placeholder="Cari minggu ke- (contoh: 1, 2) atau kegiatan..." 
                               oninput="handleSearchLogbook(this.value)">
                        <button type="button" 
                                id="btnClearSearch" 
                                class="btn-clear-search" 
                                onclick="clearSearchLogbook()" 
                                style="display: none;" 
                                title="Hapus pencarian">
                            &times;
                        </button>
                    </div>
                    <span id="searchResultCount" class="search-result-count" style="display: none;"></span>
                </div>

                <div class="collapse-toggle-group">
                    <button type="button" class="btn-toggle-all" onclick="toggleAllCards(true)" title="Buka seluruh rincian kartu">
                        Buka Semua
                    </button>
                    <button type="button" class="btn-toggle-all" onclick="toggleAllCards(false)" title="Tutup seluruh rincian kartu">
                        Tutup Semua
                    </button>
                </div>
            </div>
        @endif

        {{-- Daftar Kartu Logbook Mingguan (Accordion) --}}
        @if($logbooks->isEmpty())
            <div class="card-modern" style="text-align: center; padding: 56px 24px;">
                <div style="width: 64px; height: 64px; border-radius: 50%; background: var(--blue-tint); color: var(--blue-600); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px;">
                    <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-900); margin-bottom: 6px;">Belum Ada Catatan Logbook Mingguan</h3>
                <p style="font-size: 0.875rem; color: var(--neutral-600); max-width: 440px; margin: 0 auto 20px auto; line-height: 1.5;">
                    Anda belum mengirimkan laporan mingguan. Klik tombol di bawah ini untuk mencatat ringkasan aktivitas magang Anda untuk minggu berjalan.
                </p>
                <button type="button" class="btn btn-primary" onclick="openTambahModal({{ $mingguAktif }})">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                    </svg>
                    + Isi Logbook Pertama Anda
                </button>
            </div>
        @else
            <div id="logbookCardsContainer">
                {{-- State Kosong Pencarian --}}
                <div id="searchEmptyState" class="card-modern" style="display: none; text-align: center; padding: 40px 20px; margin-bottom: 20px;">
                    <p style="font-size: 0.9375rem; color: var(--neutral-600); margin: 0;">
                        Tidak ada logbook yang cocok dengan pencarian "<span id="searchQueryText" style="font-weight: 700; color: var(--neutral-800);"></span>".
                    </p>
                    <button type="button" class="btn btn-secondary" onclick="clearSearchLogbook()" style="margin-top: 12px; font-size: 0.8125rem; height: 32px; padding: 0 14px;">
                        Reset Pencarian
                    </button>
                </div>

                @foreach($logbooks as $log)
                    @php
                        $statusValue = $log->status->value ?? (string)$log->status;
                        $isRevisi = in_array($statusValue, ['perlu_revisi', 'ditolak']);
                        $mingguKeTampil = $log->minggu_ke ?? 1;
                        $labelPeriodeTampil = $daftarMinggu[$mingguKeTampil - 1]['label_periode'] ?? $log->tanggal->translatedFormat('d M Y');
                        $isExpandedDefault = $loop->first;
                    @endphp
                    <div class="weekly-card {{ $isExpandedDefault ? 'is-expanded' : '' }}" 
                         id="logbook-minggu-{{ $mingguKeTampil }}"
                         data-minggu="{{ $mingguKeTampil }}"
                         data-status="{{ $statusValue }}">
                        
                        {{-- Header Accordion --}}
                        <div class="weekly-card-header" onclick="toggleCard({{ $log->id }})">
                            <div class="weekly-badge-box">
                                <div class="week-number-pill" style="{{ $isRevisi ? 'background: #FEF3C7; color: #D97706; border-color: #FDE68A;' : '' }}">
                                    <span style="font-size: 0.625rem; font-weight: 700; text-transform: uppercase;">MG</span>
                                    <span style="font-size: 1.125rem;">{{ $mingguKeTampil }}</span>
                                </div>
                                <div>
                                    <div class="weekly-card-title">Logbook Minggu ke-{{ $mingguKeTampil }}</div>
                                    <div class="weekly-card-meta">
                                        <span>📅 Periode: <strong>{{ $labelPeriodeTampil }}</strong></span>
                                        <span>•</span>
                                        <span>Dilaporkan: {{ $log->tanggal->translatedFormat('d M Y') }}</span>
                                    </div>
                                    {{-- Cuplikan saat ditutup --}}
                                    <div class="weekly-collapsed-preview">{{ Str::limit($log->kegiatan, 110) }}</div>
                                </div>
                            </div>

                            <div style="display: flex; align-items: center; gap: 10px;" onclick="event.stopPropagation();">
                                @if($isRevisi)
                                    <x-gentelella.badge type="danger">Perlu Revisi</x-gentelella.badge>
                                @elseif($statusValue === 'terkirim')
                                    <x-gentelella.badge type="info">Terkirim</x-gentelella.badge>
                                @elseif($statusValue === 'disetujui')
                                    <x-gentelella.badge type="success">Disetujui</x-gentelella.badge>
                                @else
                                    <x-gentelella.badge type="secondary">{{ ucfirst($statusValue) }}</x-gentelella.badge>
                                @endif

                                <button type="button" class="btn btn-secondary" style="height: 32px; padding: 0 12px; font-size: 0.75rem;"
                                        onclick="openEditModal({{ $log->id }}, {{ $mingguKeTampil }}, '{{ $log->tanggal->toDateString() }}', {{ json_encode($log->kegiatan) }}, '{{ $log->link_laporan ?? '' }}')">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                    Edit
                                </button>

                                <button type="button" class="btn-chevron-toggle" onclick="toggleCard({{ $log->id }})" aria-label="Buka tutup rincian">
                                    <svg class="chevron-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Konten Detail Accordion --}}
                        <div class="weekly-card-content" id="cardContent-{{ $log->id }}">
                            <div class="weekly-body">{{ $log->kegiatan }}</div>

                            @php
                                $catatanFeedback = $log->catatan_pembimbing ?? $log->catatan_review;
                            @endphp
                            @if($catatanFeedback)
                                <div style="margin-top: 14px;">
                                    <x-gentelella.callout type="warning" title="Catatan Review Pembimbing Lapangan">
                                        {{ $catatanFeedback }}
                                    </x-gentelella.callout>
                                </div>
                            @endif

                            @if($log->file_laporan || $log->link_laporan)
                                <div class="weekly-attachments">
                                    @if($log->file_laporan)
                                        <a href="{{ route('peserta.logbook.lampiran', $log) }}" target="_blank" class="attachment-pill">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--red-600);">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                            Berkas Laporan PDF ↗
                                        </a>
                                    @endif

                                    @if($log->link_laporan)
                                        <a href="{{ $log->link_laporan }}" target="_blank" rel="noopener noreferrer" class="attachment-pill">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--blue-600);">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                            </svg>
                                            Tautan Dokumen / Repositori ↗
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach

                @if($logbooks->hasPages())
                    <div style="margin-top: 24px;">
                        {{ $logbooks->links() }}
                    </div>
                @endif
            </div>
        @endif
    @endif
</div>

{{-- Modal Tambah Logbook Mingguan --}}
<div id="modalTambahLogbook" class="modal-backdrop">
    <div class="modal-box">
        <div class="modal-header">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: var(--blue-tint); color: var(--blue-600); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div>
                    <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0; line-height: 1.25;">Isi Logbook Mingguan</h3>
                    <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 2px 0 0 0;">Laporkan capaian dan aktivitas magang mingguan Anda</p>
                </div>
            </div>
            <button type="button" onclick="closeTambahModal()" class="modal-close-btn" title="Tutup">&times;</button>
        </div>

        <form id="formTambahLogbook" action="{{ route('peserta.logbook.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">

                {{-- Field Pemilihan Minggu Magang (Selalu Terlihat di Atas) --}}
                <div class="form-group" style="margin-bottom: 16px;">
                    <label for="tambah_minggu_ke" class="form-label">
                        <span>Pilih Minggu Magang <span style="color: var(--red-600);">*</span></span>
                        <span id="labelRentangMinggu" style="font-size: 0.75rem; color: var(--blue-600); font-weight: 600;"></span>
                    </label>
                    <select id="tambah_minggu_ke" name="minggu_ke" class="form-control" required onchange="onMingguChange(this.value)">
                        <option value="">-- Pilih Minggu Magang --</option>
                        @foreach($daftarMinggu as $m)
                            <option value="{{ $m['minggu_ke'] }}" 
                                    data-periode="{{ $m['label_periode'] }}"
                                    data-terisi="{{ $m['logbook'] ? '1' : '0' }}"
                                    {{ $m['logbook'] ? 'disabled' : '' }}>
                                Minggu ke-{{ $m['minggu_ke'] }} ({{ $m['label_periode'] }}) {{ $m['logbook'] ? '— Sudah Diisi' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Placeholder Saat Belum Ada Minggu yang Dipilih --}}
                <div id="placeholderPilihMinggu" class="logbook-select-placeholder">
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: var(--blue-tint); color: var(--blue-600); display: flex; align-items: center; justify-content: center; margin: 0 auto 10px auto;">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-800); margin: 0 0 4px 0;">Pilih Minggu Magang Terlebih Dahulu</h4>
                    <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0; line-height: 1.45;">
                        Silakan klik dan pilih minggu magang pada menu di atas untuk membuka formulir pelaporan logbook mingguan.
                    </p>
                </div>

                {{-- Notifikasi Jika Minggu Terpilih Sudah Pernah Diisi --}}
                <div id="alertMingguSudahDiisi" style="display: none; padding: 14px 16px; background: #EFF6FF; border: 1.5px solid #BFDBFE; border-radius: 10px; margin-top: 10px;">
                    <div style="display: flex; align-items: flex-start; gap: 10px;">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--blue-600); flex-shrink: 0; margin-top: 2px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <strong style="font-size: 0.875rem; color: var(--blue-800);">Minggu Ini Sudah Pernah Dilaporkan</strong>
                            <p style="font-size: 0.8125rem; color: var(--blue-700); margin: 2px 0 0 0; line-height: 1.4;">
                                Anda sudah mengisi logbook untuk minggu ini. Untuk mengubah data laporan, silakan tutup popup ini lalu gunakan tombol <strong>Edit</strong> pada kartu logbook yang bersangkutan di daftar bawah.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Container Formulir Isian (Hanya Muncul Setelah Minggu Dipilih) --}}
                <div id="formFieldsLogbook" style="display: none;">
                    <div class="form-group">
                        <label for="tambah_tanggal" class="form-label">
                            <span>Tanggal Pelaporan <span style="color: var(--red-600);">*</span></span>
                            <span style="font-size: 0.6875rem; color: var(--neutral-500); font-weight: 400;">Maks. hari ini</span>
                        </label>
                        <input type="date" id="tambah_tanggal" name="tanggal"
                               class="form-control"
                               value="{{ old('tanggal', now()->toDateString()) }}"
                               min="{{ now()->subDays($logbookMaksMundur)->toDateString() }}"
                               max="{{ now()->toDateString() }}"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="tambah_file_laporan" class="form-label">
                            <span>Unggah Berkas Laporan (PDF)</span>
                            <span style="font-size: 0.6875rem; color: var(--neutral-500); font-weight: 400;">Opsional, Maks. 2MB</span>
                        </label>
                        <input type="file" id="tambah_file_laporan" name="file_laporan" accept=".pdf" class="form-control" style="padding-top: 8px;">
                    </div>

                    <div class="form-group">
                        <label for="tambah_link_laporan" class="form-label">
                            <span>Tautan Berkas / Repositori</span>
                            <span style="font-size: 0.6875rem; color: var(--neutral-500); font-weight: 400;">Opsional (https://)</span>
                        </label>
                        <input type="url" id="tambah_link_laporan" name="link_laporan" class="form-control"
                               placeholder="https://drive.google.com/... atau https://github.com/..."
                               value="{{ old('link_laporan') }}">
                    </div>

                    <div class="form-group">
                        <label for="tambah_kegiatan" class="form-label">
                            <span>Ringkasan Kegiatan & Capaian <span style="color: var(--red-600);">*</span></span>
                            <span style="font-size: 0.6875rem; color: var(--neutral-500); font-weight: 400;">Min. 10 karakter</span>
                        </label>
                        <textarea id="tambah_kegiatan" name="kegiatan" rows="5" class="form-control"
                                  placeholder="Contoh: Pada minggu ini berhasil menyelesaikan slicing antarmuka dashboard, menghubungkan API autentikasi, serta melakukan perbaikan bug..."
                                  required oninput="updateCharCount(this, 'tambah_char_count')">{{ old('kegiatan') }}</textarea>
                        <div id="tambah_char_count" class="char-counter">0 karakter (minimal 10 karakter)</div>
                    </div>

                    {{-- Panduan Penulisan Logbook Mingguan (Diletakkan di Bagian Paling Bawah) --}}
                    <div class="tips-box-bottom">
                        <div style="display: flex; align-items: flex-start; gap: 10px;">
                            <span style="font-size: 18px; line-height: 1;">💡</span>
                            <div>
                                <strong style="color: var(--neutral-900); font-size: 0.8125rem;">Panduan Penulisan Logbook Mingguan:</strong>
                                <p style="font-size: 0.75rem; color: var(--neutral-600); margin: 3px 0 0 0; line-height: 1.45;">
                                    Uraikan pekerjaan utama yang diselesaikan, kendala yang dihadapi, solusi yang diterapkan, serta rencana minggu berikutnya secara jelas dan faktual.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" onclick="closeTambahModal()" class="btn btn-secondary">Batal</button>
                <button type="submit" id="btnSubmitTambah" class="btn btn-primary" disabled style="box-shadow: 0 4px 12px rgba(7, 81, 255, 0.25);">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Kirim Logbook Mingguan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit Logbook Mingguan --}}
<div id="modalEditLogbook" class="modal-backdrop">
    <div class="modal-box">
        <div class="modal-header">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #EFF6FF; color: var(--blue-700); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </div>
                <div>
                    <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0; line-height: 1.25;">Perbarui Logbook Mingguan</h3>
                    <p id="modalEditSubtitle" style="font-size: 0.8125rem; color: var(--neutral-600); margin: 2px 0 0 0;">Minggu ke-X</p>
                </div>
            </div>
            <button type="button" onclick="closeEditModal()" class="modal-close-btn" title="Tutup">&times;</button>
        </div>

        <form id="formEditLogbook" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <input type="hidden" id="edit_minggu_ke" name="minggu_ke">
            <input type="hidden" id="edit_tanggal" name="tanggal">

            <div class="modal-body">
                <div class="form-group">
                    <label for="edit_file_laporan" class="form-label">
                        <span>Perbarui Berkas Laporan (PDF)</span>
                        <span style="font-size: 0.6875rem; color: var(--neutral-500); font-weight: 400;">Opsional, Maks. 2MB</span>
                    </label>
                    <input type="file" id="edit_file_laporan" name="file_laporan" accept=".pdf" class="form-control" style="padding-top: 8px;">
                    <span style="font-size: 0.75rem; color: var(--neutral-500); margin-top: 2px;">Kosongkan jika tidak ingin mengubah berkas yang sudah ada.</span>
                </div>

                <div class="form-group">
                    <label for="edit_link_laporan" class="form-label">
                        <span>Perbarui Tautan Berkas / Repositori</span>
                        <span style="font-size: 0.6875rem; color: var(--neutral-500); font-weight: 400;">Opsional (https://)</span>
                    </label>
                    <input type="url" id="edit_link_laporan" name="link_laporan" class="form-control" placeholder="https://...">
                </div>

                <div class="form-group">
                    <label for="edit_kegiatan" class="form-label">
                        <span>Ringkasan Kegiatan & Capaian <span style="color: var(--red-600);">*</span></span>
                        <span style="font-size: 0.6875rem; color: var(--neutral-500); font-weight: 400;">Min. 10 karakter</span>
                    </label>
                    <textarea id="edit_kegiatan" name="kegiatan" rows="5" class="form-control" required oninput="updateCharCount(this, 'edit_char_count')"></textarea>
                    <div id="edit_char_count" class="char-counter">0 karakter</div>
                </div>

                <div class="tips-box-bottom">
                    <div style="display: flex; align-items: flex-start; gap: 10px;">
                        <span style="font-size: 18px; line-height: 1;">💡</span>
                        <div>
                            <strong style="color: var(--neutral-900); font-size: 0.8125rem;">Catatan Pembaruan:</strong>
                            <p style="font-size: 0.75rem; color: var(--neutral-600); margin: 3px 0 0 0; line-height: 1.45;">
                                Pastikan capaian yang diperbarui mencerminkan progres aktual sebelum diverifikasi dan dinilai oleh pembimbing lapangan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" onclick="closeEditModal()" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary" style="box-shadow: 0 4px 12px rgba(7, 81, 255, 0.25);">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function updateCharCount(textarea, counterId) {
        const counter = document.getElementById(counterId);
        if (!counter) return;
        const len = textarea.value.trim().length;
        if (len < 10) {
            counter.textContent = `${len} karakter (kurang ${10 - len} karakter)`;
            counter.style.color = 'var(--red-600)';
        } else {
            counter.textContent = `${len} karakter (memenuhi syarat min. 10)`;
            counter.style.color = '#10B981';
        }
    }

    function onMingguChange(mingguVal) {
        const select = document.getElementById('tambah_minggu_ke');
        const label = document.getElementById('labelRentangMinggu');
        const formFields = document.getElementById('formFieldsLogbook');
        const placeholder = document.getElementById('placeholderPilihMinggu');
        const alertSudahDiisi = document.getElementById('alertMingguSudahDiisi');
        const btnSubmit = document.getElementById('btnSubmitTambah');

        if (!mingguVal || mingguVal === "") {
            if (label) label.textContent = '';
            if (formFields) formFields.style.display = 'none';
            if (alertSudahDiisi) alertSudahDiisi.style.display = 'none';
            if (placeholder) placeholder.style.display = 'block';
            if (btnSubmit) btnSubmit.disabled = true;
            return;
        }

        const selectedOpt = select.options[select.selectedIndex];
        const periode = selectedOpt ? selectedOpt.getAttribute('data-periode') : '';
        const terisi = selectedOpt ? selectedOpt.getAttribute('data-terisi') === '1' : false;

        if (label) {
            label.textContent = periode ? `Periode: ${periode}` : '';
        }

        if (placeholder) placeholder.style.display = 'none';

        if (terisi) {
            if (formFields) formFields.style.display = 'none';
            if (alertSudahDiisi) alertSudahDiisi.style.display = 'block';
            if (btnSubmit) btnSubmit.disabled = true;
        } else {
            if (alertSudahDiisi) alertSudahDiisi.style.display = 'none';
            if (formFields) formFields.style.display = 'block';
            if (btnSubmit) btnSubmit.disabled = false;

            // Auto focus textarea kegiatan
            setTimeout(() => {
                document.getElementById('tambah_kegiatan')?.focus();
            }, 100);
        }
    }

    function openTambahModal(preSelectMinggu = null) {
        const modal = document.getElementById('modalTambahLogbook');
        const select = document.getElementById('tambah_minggu_ke');

        if (select) {
            if (preSelectMinggu !== null && preSelectMinggu !== undefined) {
                const targetOpt = Array.from(select.options).find(opt => opt.value == preSelectMinggu);
                if (targetOpt && targetOpt.disabled) {
                    select.value = "";
                } else {
                    select.value = preSelectMinggu;
                }
            } else {
                // Otomatis pilih minggu aktif atau minggu pertama yang belum diisi
                const availableOpt = Array.from(select.options).find(opt => opt.value !== "" && !opt.disabled);
                if (availableOpt) {
                    select.value = availableOpt.value;
                } else {
                    select.value = "";
                }
            }
        }

        onMingguChange(select?.value);
        modal?.classList.add('is-open');
    }

    function closeTambahModal() {
        document.getElementById('modalTambahLogbook')?.classList.remove('is-open');
    }

    function openEditModal(id, mingguKe, tanggal, kegiatan, link) {
        const modal = document.getElementById('modalEditLogbook');
        const form = document.getElementById('formEditLogbook');
        const subtitle = document.getElementById('modalEditSubtitle');
        const inputMinggu = document.getElementById('edit_minggu_ke');
        const inputTanggal = document.getElementById('edit_tanggal');
        const inputKegiatan = document.getElementById('edit_kegiatan');
        const inputLink = document.getElementById('edit_link_laporan');

        form.action = `/peserta/logbook/${id}`;
        subtitle.textContent = `Minggu ke-${mingguKe} • Dilaporkan: ${tanggal}`;
        inputMinggu.value = mingguKe;
        inputTanggal.value = tanggal;
        inputKegiatan.value = kegiatan;
        inputLink.value = link || '';

        updateCharCount(inputKegiatan, 'edit_char_count');

        modal?.classList.add('is-open');

        setTimeout(() => {
            inputKegiatan.focus();
        }, 150);
    }

    function closeEditModal() {
        document.getElementById('modalEditLogbook')?.classList.remove('is-open');
    }

    // Anti double-submit
    document.getElementById('formTambahLogbook')?.addEventListener('submit', function() {
        const btn = document.getElementById('btnSubmitTambah');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="animation: spin 1s linear infinite;">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" style="opacity: 0.25;"></circle>
                    <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" style="opacity: 0.75;"></path>
                </svg>
                Menyimpan...
            `;
        }
    });

    // Accordion Toggle Individual Card
    function toggleCard(cardId) {
        const content = document.getElementById(`cardContent-${cardId}`);
        if (!content) return;
        const card = content.closest('.weekly-card');
        if (card) {
            card.classList.toggle('is-expanded');
        }
    }

    // Toggle All Cards (Buka / Tutup Semua)
    function toggleAllCards(shouldExpand) {
        const cards = document.querySelectorAll('.weekly-card');
        cards.forEach(card => {
            if (shouldExpand) {
                card.classList.add('is-expanded');
            } else {
                card.classList.remove('is-expanded');
            }
        });
    }

    // Client-side Realtime Search Logbook
    function handleSearchLogbook(query) {
        const q = (query || '').trim().toLowerCase();
        const btnClear = document.getElementById('btnClearSearch');
        const resultCountEl = document.getElementById('searchResultCount');
        const emptyState = document.getElementById('searchEmptyState');
        const queryText = document.getElementById('searchQueryText');
        const cards = document.querySelectorAll('.weekly-card');

        if (btnClear) {
            btnClear.style.display = q.length > 0 ? 'inline-flex' : 'none';
        }

        let matchedCount = 0;

        cards.forEach(card => {
            const minggu = (card.getAttribute('data-minggu') || '').trim();
            const cardText = card.innerText.toLowerCase();

            // Cek apakah query cocok dengan minggu (angka '1', '2', 'minggu 2', dll.) atau kata kunci teks kegiatan
            const matchMingguExact = q === minggu || 
                                     q === `minggu ${minggu}` || 
                                     q === `minggu ke-${minggu}` || 
                                     q === `minggu ke ${minggu}` || 
                                     q === `mg ${minggu}`;
            const matchText = cardText.includes(q);

            if (!q || matchMingguExact || matchText) {
                card.style.display = 'block';
                matchedCount++;
                if (q) {
                    card.classList.add('is-expanded');
                }
            } else {
                card.style.display = 'none';
            }
        });

        if (emptyState && queryText) {
            if (q && matchedCount === 0) {
                emptyState.style.display = 'block';
                queryText.textContent = query;
            } else {
                emptyState.style.display = 'none';
            }
        }

        if (resultCountEl) {
            if (q) {
                resultCountEl.style.display = 'inline-block';
                resultCountEl.textContent = `${matchedCount} ditemukan`;
            } else {
                resultCountEl.style.display = 'none';
            }
        }
    }

    function clearSearchLogbook() {
        const input = document.getElementById('inputSearchLogbook');
        if (input) {
            input.value = '';
            handleSearchLogbook('');
            input.focus();
        }
    }

    function handleWeekChipClick(mingguKe, status, isAktif) {
        const card = document.getElementById(`logbook-minggu-${mingguKe}`);
        if (card) {
            // Bersihkan pencarian jika sedang aktif agar kartu target terlihat
            const input = document.getElementById('inputSearchLogbook');
            if (input && input.value) {
                clearSearchLogbook();
            }

            // Pastikan kartu terbuka jika tertutup
            card.classList.add('is-expanded');
            card.style.display = 'block';

            card.scrollIntoView({ behavior: 'smooth', block: 'center' });
            card.classList.add('highlight-card');
            setTimeout(() => {
                card.classList.remove('highlight-card');
            }, 2500);
            if (isAktif || status === 'belum') {
                openTambahModal(mingguKe);
            } else {
                const msg = `Laporan Minggu ke-${mingguKe} belum dimulai.`;
                if (window.MagKomToast) {
                    window.MagKomToast.info(msg, 'Informasi Jadwal');
                } else {
                    alert(msg);
                }
            }
        }
    }

    // Close on backdrop click & ESC key
    window.addEventListener('click', function(e) {
        const modalTambah = document.getElementById('modalTambahLogbook');
        const modalEdit = document.getElementById('modalEditLogbook');
        if (e.target === modalTambah) closeTambahModal();
        if (e.target === modalEdit) closeEditModal();
    });

    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeTambahModal();
            closeEditModal();
        }
    });
</script>
<style>
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>
@endpush
@endsection
