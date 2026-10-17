@extends('layouts.peserta')

@section('title', 'Akun Saya - Pengaturan')
@section('page_title', 'Akun Saya')

@push('styles')
<style>
    /* Settings Page Styles - Designed according to DS-MagKom & Reference */
    .settings-page {
        max-width: 1200px;
        margin: 0 auto;
        padding-bottom: 40px;
    }

    .settings-header {
        margin-bottom: 28px;
    }

    .settings-title {
        font-size: 24px;
        font-weight: 700;
        color: var(--neutral-800, #1D2530);
        margin-bottom: 6px;
        line-height: 1.3;
    }

    .settings-subtitle {
        font-size: 14px;
        color: var(--neutral-500, #7A8494);
        line-height: 1.5;
    }

    /* 2-Column Responsive Grid */
    .settings-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 24px;
        align-items: start;
    }

    @media (max-width: 960px) {
        .settings-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Card Box */
    .settings-card {
        background: #ffffff;
        border: 1px solid var(--neutral-200, #EEF0F5);
        border-radius: var(--radius-lg, 16px);
        padding: 28px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    }

    .card-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--neutral-800, #1D2530);
        margin-bottom: 6px;
    }

    .card-subtitle {
        font-size: 13px;
        color: var(--neutral-500, #7A8494);
        margin-bottom: 22px;
        line-height: 1.4;
    }

    /* Info Akun Styles */
    .profile-mini-header {
        display: flex;
        align-items: center;
        gap: 16px;
        padding-bottom: 22px;
        margin-bottom: 22px;
        border-bottom: 1px solid var(--neutral-200, #EEF0F5);
    }

    .profile-mini-avatar {
        width: 54px;
        height: 54px;
        border-radius: 50%;
        background: var(--blue-tint, #E6EEFF);
        color: var(--blue-700, #0641CC);
        font-size: 18px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        overflow: hidden;
        border: 1px solid var(--blue-tint-line, #B3C8FF);
    }

    .profile-mini-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-mini-meta {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .profile-mini-name {
        font-size: 16px;
        font-weight: 700;
        color: var(--neutral-800, #1D2530);
    }

    .profile-role-chip {
        display: inline-flex;
        align-items: center;
        background: var(--blue-tint, #E6EEFF);
        color: var(--blue-700, #0641CC);
        font-size: 12px;
        font-weight: 600;
        padding: 3px 12px;
        border-radius: 20px;
        width: fit-content;
    }

    .info-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
        margin-bottom: 24px;
    }

    .info-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 14px;
    }

    .info-label {
        color: var(--neutral-500, #7A8494);
        font-weight: 400;
    }

    .info-value {
        color: var(--neutral-800, #1D2530);
        font-weight: 600;
        text-align: right;
    }

    .status-badge-active {
        display: inline-flex;
        align-items: center;
        background: var(--green-100, #E8F7EE);
        color: var(--green-700, #166534);
        padding: 2px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .callout-note {
        background: var(--neutral-150, #F5F7FB);
        border: 1px solid var(--neutral-200, #EEF0F5);
        border-radius: var(--radius-md, 12px);
        padding: 14px 18px;
        font-size: 13px;
        color: var(--neutral-600, #666666);
        line-height: 1.5;
    }

    .callout-note a {
        color: var(--blue-700, #0641CC);
        font-weight: 600;
        text-decoration: none;
    }

    .callout-note a:hover {
        text-decoration: underline;
    }

    /* Form Ganti Password */
    .form-group-custom {
        margin-bottom: 18px;
    }

    .form-label-custom {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: var(--neutral-800, #1D2530);
        margin-bottom: 6px;
    }

    .input-password-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-custom {
        width: 100%;
        height: 42px;
        padding: 10px 42px 10px 14px;
        border: 1px solid var(--neutral-400, #D0D3D9);
        border-radius: var(--radius-default, 8px);
        font-size: 14px;
        color: var(--neutral-800, #1D2530);
        background: #ffffff;
        transition: border-color 0.2s, box-shadow 0.2s;
        font-family: inherit;
    }

    .input-custom:focus {
        outline: none;
        border-color: var(--blue-700, #0641CC);
        box-shadow: 0 0 0 3px rgba(6, 65, 204, 0.12);
    }

    .input-custom.is-invalid {
        border-color: var(--red-600, #DC2626);
    }

    .toggle-password-btn {
        position: absolute;
        right: 12px;
        background: none;
        border: none;
        padding: 4px;
        color: var(--neutral-500, #7A8494);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .toggle-password-btn:hover {
        color: var(--neutral-800, #1D2530);
    }

    .form-hint {
        font-size: 12px;
        color: var(--neutral-500, #7A8494);
        margin-top: 8px;
        margin-bottom: 22px;
    }

    .form-feedback-error {
        font-size: 12px;
        color: var(--red-600, #DC2626);
        margin-top: 4px;
    }

    .form-actions-row {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .btn-primary-custom {
        background: var(--blue-700, #0641CC);
        color: #ffffff;
        border: 1px solid var(--blue-700, #0641CC);
        border-radius: var(--radius-default, 8px);
        padding: 9px 20px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s, transform 0.1s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-primary-custom:hover {
        background: #0535A6;
        border-color: #0535A6;
    }

    .btn-primary-custom:active {
        transform: scale(0.99);
    }

    .btn-outline-custom {
        background: #ffffff;
        color: var(--neutral-700, #333333);
        border: 1px solid var(--neutral-400, #D0D3D9);
        border-radius: var(--radius-default, 8px);
        padding: 9px 20px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s, border-color 0.2s;
    }

    .btn-outline-custom:hover {
        background: var(--neutral-150, #F5F7FB);
        border-color: var(--neutral-500, #7A8494);
    }

    /* Preferensi Section */
    .pref-group {
        margin-bottom: 22px;
    }

    .pref-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--neutral-800, #1D2530);
        margin-bottom: 8px;
    }

    .language-select-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .language-select-custom {
        width: 100%;
        appearance: none;
        -webkit-appearance: none;
        padding: 10px 38px 10px 14px;
        border: 1px solid var(--neutral-400, #D0D3D9);
        border-radius: var(--radius-default, 8px);
        background: #ffffff;
        font-size: 14px;
        font-weight: 500;
        color: var(--neutral-800, #1D2530);
        cursor: pointer;
        transition: border-color 0.2s, box-shadow 0.2s;
        font-family: inherit;
    }

    .language-select-custom:focus {
        outline: none;
        border-color: var(--blue-700, #0641CC);
        box-shadow: 0 0 0 3px rgba(6, 65, 204, 0.12);
    }

    .language-chevron-icon {
        position: absolute;
        right: 14px;
        pointer-events: none;
        color: var(--neutral-500, #7A8494);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .theme-options-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-top: 8px;
    }

    @media (max-width: 640px) {
        .theme-options-grid {
            grid-template-columns: 1fr;
        }
    }

    .theme-card-option {
        position: relative;
        border: 1px solid var(--neutral-400, #D0D3D9);
        border-radius: var(--radius-md, 12px);
        padding: 14px 12px;
        cursor: pointer;
        transition: all 0.2s ease;
        background: #ffffff;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    [data-theme="dark"] .theme-card-option {
        background: #182234;
        border-color: #26354D;
    }

    .theme-card-option:hover {
        border-color: var(--blue-600, #0751FF);
    }

    .theme-card-option.selected {
        border: 2px solid var(--blue-700, #0641CC) !important;
        background: #ffffff;
        box-shadow: 0 0 0 1px var(--blue-700, #0641CC);
    }

    [data-theme="dark"] .theme-card-option.selected {
        border: 2px solid #3B82F6 !important;
        background: #182234;
        box-shadow: 0 0 0 1px #3B82F6;
    }

    .theme-card-option input[type="radio"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .theme-preview-box {
        height: 38px;
        border-radius: 6px;
        padding: 6px 10px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .preview-light {
        background: #F1F5F9;
        border: 1px solid #E2E8F0;
    }

    .preview-dark {
        background: #1E293B;
        border: 1px solid #0F172A;
    }

    .preview-system {
        background: linear-gradient(115deg, #F1F5F9 48%, #1E293B 48%);
        border: 1px solid #CBD5E1;
    }

    .preview-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #94A3B8;
    }

    .preview-bar {
        height: 4px;
        flex: 1;
        border-radius: 2px;
        background: #CBD5E1;
    }

    .preview-dark .preview-dot {
        background: #60A5FA;
    }

    .preview-dark .preview-bar {
        background: #334155;
    }

    .preview-system .preview-dot {
        background: #94A3B8;
    }

    .preview-system .preview-bar {
        background: #64748B;
    }

    .theme-info-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--neutral-800, #1D2530);
    }

    .theme-info-desc {
        font-size: 11px;
        color: var(--neutral-500, #7A8494);
        line-height: 1.3;
    }

    /* Dark Mode Harmonization ala Menu Tugas */
    [data-theme="dark"] .settings-title {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .settings-subtitle {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .card-title {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .card-subtitle {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .profile-mini-header {
        border-bottom-color: #26354D !important;
    }

    [data-theme="dark"] .profile-mini-name {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .profile-role-chip {
        background: rgba(6, 65, 204, 0.35) !important;
        border: 1px solid rgba(7, 81, 255, 0.5) !important;
        color: #60A5FA !important;
    }

    [data-theme="dark"] .info-label {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .info-value {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .status-badge-active {
        background: rgba(6, 78, 59, 0.5) !important;
        color: #6EE7B7 !important;
        border: 1px solid rgba(52, 211, 153, 0.4) !important;
    }

    [data-theme="dark"] .form-label-custom {
        color: #E2E8F0 !important;
    }

    [data-theme="dark"] .pref-label {
        color: #E2E8F0 !important;
    }

    [data-theme="dark"] .theme-info-title {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .theme-info-desc {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .btn-outline-custom {
        background: #182234 !important;
        border-color: #3E516E !important;
        color: #E2E8F0 !important;
    }

    [data-theme="dark"] .btn-outline-custom:hover {
        background: #1E293B !important;
        border-color: #60A5FA !important;
        color: #60A5FA !important;
    }
</style>
@endpush

@section('content')
<div class="settings-page">
    <!-- Header Halaman -->
    <div class="settings-header">
        <h1 class="settings-title">Akun saya</h1>
        <p class="settings-subtitle">Kelola informasi akun dan kata sandi kamu.</p>
    </div>

    <!-- Layout 2 Kolom Sesuai Referensi -->
    <div class="settings-grid">
        <!-- Kolom Kiri 1: Informasi Akun -->
        <div class="settings-card">
            <h2 class="card-title">Informasi akun</h2>

            <div class="profile-mini-header">
                <div class="profile-mini-avatar">
                    @if($user->profil?->foto_url)
                        <img src="{{ $user->profil->foto_url }}" alt="{{ $user->nama ?? $user->name }}">
                    @else
                        {{ strtoupper(substr($user->nama ?? $user->name ?? 'P', 0, 2)) }}
                    @endif
                </div>
                <div class="profile-mini-meta">
                    <span class="profile-mini-name">{{ $user->nama ?? $user->name }}</span>
                    <span class="profile-role-chip">Peserta Magang</span>
                </div>
            </div>

            <div class="info-list">
                <div class="info-row">
                    <span class="info-label">Nama lengkap</span>
                    <span class="info-value">{{ $user->nama ?? $user->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email</span>
                    <span class="info-value">{{ $user->email }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Peran</span>
                    <span class="info-value">Peserta Magang</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status akun</span>
                    <span class="info-value">
                        <span class="status-badge-active">Aktif</span>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Terakhir masuk</span>
                    <span class="info-value">Hari ini, {{ date('H.i') }}</span>
                </div>
            </div>

            <div class="callout-note">
                Nama, email, dan biodata akun dapat diubah lewat menu <a href="{{ route('peserta.profil.show') }}">Profil & Biodata</a>.
            </div>
        </div>

        <!-- Kolom Kanan 1: Ganti Kata Sandi -->
        <div class="settings-card">
            <h2 class="card-title">Ganti kata sandi</h2>
            <p class="card-subtitle">Gunakan kata sandi yang kuat dan belum pernah kamu pakai di tempat lain.</p>

            <form action="{{ route('peserta.pengaturan.password.update') }}" method="POST" id="passwordForm">
                @csrf
                @method('PUT')

                <!-- Kata Sandi Saat Ini -->
                <div class="form-group-custom">
                    <label for="current_password" class="form-label-custom">Kata sandi saat ini</label>
                    <div class="input-password-wrapper">
                        <input type="password" 
                               name="current_password" 
                               id="current_password" 
                               class="input-custom @error('current_password') is-invalid @enderror" 
                               placeholder="••••••••" 
                               required>
                        <button type="button" class="toggle-password-btn" onclick="togglePasswordVisibility('current_password', this)" aria-label="Lihat kata sandi">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                    @error('current_password')
                        <div class="form-feedback-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Kata Sandi Baru -->
                <div class="form-group-custom">
                    <label for="password" class="form-label-custom">Kata sandi baru</label>
                    <div class="input-password-wrapper">
                        <input type="password" 
                               name="password" 
                               id="password" 
                               class="input-custom @error('password') is-invalid @enderror" 
                               placeholder="••••••••" 
                               required>
                        <button type="button" class="toggle-password-btn" onclick="togglePasswordVisibility('password', this)" aria-label="Lihat kata sandi">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <div class="form-feedback-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Konfirmasi Kata Sandi Baru -->
                <div class="form-group-custom">
                    <label for="password_confirmation" class="form-label-custom">Konfirmasi kata sandi baru</label>
                    <div class="input-password-wrapper">
                        <input type="password" 
                               name="password_confirmation" 
                               id="password_confirmation" 
                               class="input-custom @error('password_confirmation') is-invalid @enderror" 
                               placeholder="••••••••" 
                               required>
                        <button type="button" class="toggle-password-btn" onclick="togglePasswordVisibility('password_confirmation', this)" aria-label="Lihat kata sandi">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <div class="form-feedback-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-hint">Minimal 8 karakter, kombinasi huruf dan angka.</div>

                <div class="form-actions-row">
                    <button type="submit" class="btn-primary-custom">Simpan kata sandi</button>
                    <button type="button" class="btn-outline-custom" onclick="document.getElementById('passwordForm').reset()">Batal</button>
                </div>
            </form>
        </div>

        <!-- Kolom Kiri 2: Preferensi -->
        <div class="settings-card">
            <h2 class="card-title">{{ __('Preferensi') }}</h2>

            <form action="{{ route('peserta.pengaturan.preferensi.update') }}" method="POST">
                @csrf

                <!-- Pilihan Bahasa -->
                <div class="pref-group">
                    <label class="pref-label" for="bahasaSelect">{{ __('Bahasa') }}</label>
                    <div class="language-select-wrapper">
                        <select name="bahasa" id="bahasaSelect" class="language-select-custom">
                            <option value="id" {{ ($bahasaAktif ?? 'id') === 'id' ? 'selected' : '' }}>{{ __('Indonesia') }}</option>
                            <option value="en" {{ ($bahasaAktif ?? '') === 'en' ? 'selected' : '' }}>{{ __('English (US)') }}</option>
                        </select>
                        <div class="language-chevron-icon" aria-hidden="true">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="form-hint" style="margin-bottom: 0;">{{ __('Berlaku untuk tampilan portal ini.') }}</div>
                </div>

                <!-- Pilihan Tampilan Tema -->
                <div class="pref-group">
                    <label class="pref-label">{{ __('Tampilan') }}</label>
                    <div class="theme-options-grid">
                        <!-- Pilihan Terang -->
                        <label class="theme-card-option {{ ($temaAktif ?? 'terang') === 'terang' ? 'selected' : '' }}" 
                               data-theme-val="terang" 
                               onclick="handleThemeSelection(this, 'terang')">
                            <input type="radio" name="tema" value="terang" {{ ($temaAktif ?? 'terang') === 'terang' ? 'checked' : '' }}>
                            <div class="theme-preview-box preview-light">
                                <span class="preview-dot"></span>
                                <span class="preview-bar"></span>
                            </div>
                            <div>
                                <div class="theme-info-title">{{ __('Terang') }}</div>
                                <div class="theme-info-desc">{{ __('Mode default portal') }}</div>
                            </div>
                        </label>

                        <!-- Pilihan Gelap -->
                        <label class="theme-card-option {{ ($temaAktif ?? '') === 'gelap' ? 'selected' : '' }}" 
                               data-theme-val="gelap" 
                               onclick="handleThemeSelection(this, 'gelap')">
                            <input type="radio" name="tema" value="gelap" {{ ($temaAktif ?? '') === 'gelap' ? 'checked' : '' }}>
                            <div class="theme-preview-box preview-dark">
                                <span class="preview-dot"></span>
                                <span class="preview-bar"></span>
                            </div>
                            <div>
                                <div class="theme-info-title">{{ __('Gelap') }}</div>
                                <div class="theme-info-desc">{{ __('Mode malam untuk penggunaan lama') }}</div>
                            </div>
                        </label>

                        <!-- Pilihan Ikuti Sistem -->
                        <label class="theme-card-option {{ ($temaAktif ?? '') === 'sistem' ? 'selected' : '' }}" 
                               data-theme-val="sistem" 
                               onclick="handleThemeSelection(this, 'sistem')">
                            <input type="radio" name="tema" value="sistem" {{ ($temaAktif ?? '') === 'sistem' ? 'checked' : '' }}>
                            <div class="theme-preview-box preview-system">
                                <span class="preview-dot"></span>
                                <span class="preview-bar"></span>
                            </div>
                            <div>
                                <div class="theme-info-title">{{ __('Ikuti sistem') }}</div>
                                <div class="theme-info-desc">{{ __('Sesuaikan tema dengan pengaturan OS') }}</div>
                            </div>
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn-primary-custom" style="margin-top: 8px;">{{ __('Simpan preferensi') }}</button>
            </form>
        </div>
    </div>
</div>

<script>
    function togglePasswordVisibility(fieldId, btn) {
        const input = document.getElementById(fieldId);
        if (!input) return;

        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';

        // Ganti ikon toggle
        if (isPassword) {
            btn.innerHTML = `
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path>
                </svg>
            `;
            btn.setAttribute('aria-label', 'Sembunyikan kata sandi');
        } else {
            btn.innerHTML = `
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
            `;
            btn.setAttribute('aria-label', 'Lihat kata sandi');
        }
    }

    function handleThemeSelection(card, themeValue) {
        document.querySelectorAll('.theme-card-option').forEach(el => el.classList.remove('selected'));
        card.classList.add('selected');
        const radio = card.querySelector('input[type="radio"]');
        if (radio) radio.checked = true;

        if (typeof window.applyPortalTheme === 'function') {
            window.applyPortalTheme(themeValue);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        try {
            const currentSavedTheme = localStorage.getItem('magkom_theme') || '{{ $temaAktif ?? 'terang' }}';
            const targetCard = document.querySelector(`.theme-card-option[data-theme-val="${currentSavedTheme}"]`);
            if (targetCard) {
                document.querySelectorAll('.theme-card-option').forEach(el => el.classList.remove('selected'));
                targetCard.classList.add('selected');
                const radio = targetCard.querySelector('input[type="radio"]');
                if (radio) radio.checked = true;
            }
        } catch (e) {}
    });
</script>
@endsection
