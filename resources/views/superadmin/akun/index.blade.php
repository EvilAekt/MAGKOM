@extends('layouts.dashboard')

@section('title', 'Akun & Preferensi Saya - Super Administrator MagKom')

@section('content')
<div class="settings-page">





    <!-- Header Halaman -->
    <div class="settings-header">
        <div>
            <h1 class="settings-title">Akun & Preferensi Saya</h1>
            <p class="settings-subtitle">Kelola keamanan kredensial Super Administrator dan preferensi visual aplikasi.</p>
        </div>
        <div>
            <a href="{{ route('superadmin.profil.show') }}" class="btn-profile-shortcut">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span>Lihat Profil Lengkap</span>
            </a>
        </div>
    </div>

    <!-- Layout 2 Kolom Sesuai DS-MagKom -->
    <div class="settings-grid">
        
        <!-- Kolom Kiri: Informasi Akun -->
        <div class="settings-card">
            <h2 class="card-title">Informasi Akun</h2>
            <p class="card-subtitle">Ringkasan kredensial dan hak akses Super Administrator.</p>

            <div class="profile-mini-header">
                <div class="profile-mini-avatar">
                    @if($user->foto_url)
                        <img src="{{ $user->foto_url }}" alt="{{ $user->nama }}">
                    @else
                        {{ strtoupper(substr($user->nama ?? 'S', 0, 2)) }}
                    @endif
                </div>
                <div class="profile-mini-meta">
                    <span class="profile-mini-name">{{ $user->nama }}</span>
                    <span class="profile-role-chip">Super Administrator</span>
                </div>
            </div>

            <div class="info-list">
                <div class="info-row">
                    <span class="info-label">Nama Lengkap</span>
                    <span class="info-value">{{ $user->nama }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Alamat Email</span>
                    <span class="info-value">{{ $user->email }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Nomor Kontak</span>
                    <span class="info-value">{{ $user->no_telp ?? 'Belum diisi' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Hak Otoritas</span>
                    <span class="info-value">Pengelola Utama</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status Keamanan</span>
                    <span class="info-value">
                        <span class="status-badge-active">Aktif & Aman</span>
                    </span>
                </div>
            </div>

            <div class="callout-note">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink: 0; color: var(--blue-700, #0641CC);">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                <span>Foto pasfoto dan nomor telepon dinas dapat diperbarui di halaman <a href="{{ route('superadmin.profil.show') }}">Profil Saya</a>. Konfigurasi sistem global tersedia di <a href="{{ route('superadmin.pengaturan.index') }}">Pengaturan Sistem</a>.</span>
            </div>
        </div>

        <!-- Kolom Kanan: Ganti Kata Sandi -->
        <div class="settings-card">
            <h2 class="card-title">Ganti Kata Sandi</h2>
            <p class="card-subtitle">Gunakan kombinasi sandi tingkat tinggi untuk perlindungan akun Super Admin.</p>

            <form action="{{ route('superadmin.akun.password.update') }}" method="POST" id="passwordForm">
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

                <div class="form-hint">Minimal 8 karakter, kombinasi huruf besar/kecil, angka, dan simbol. Setiap pergantian kata sandi akan tercatat di Audit Log sistem.</div>

                <div class="form-actions-row">
                    <button type="submit" class="btn-primary-custom" id="btnSubmitPassword">Perbarui Kata Sandi</button>
                    <button type="button" class="btn-outline-custom" onclick="document.getElementById('passwordForm').reset()">Batal</button>
                </div>
            </form>
        </div>

        <!-- Kartu Penuh Bawah: Preferensi Tampilan & Bahasa -->
        <div class="settings-card settings-card-wide">
            <h2 class="card-title">Preferensi Antarmuka & Bahasa</h2>
            <p class="card-subtitle">Sesuaikan kenyamanan visual dan bahasa saat mengawasi seluruh sistem MagKom.</p>

            <form action="{{ route('superadmin.akun.preferensi.update') }}" method="POST">
                @csrf

                <div class="pref-horizontal-grid">
                    <!-- Pilihan Bahasa -->
                    <div class="pref-group">
                        <label class="pref-label" for="bahasaSelect">Bahasa Antarmuka</label>
                        <div class="language-select-wrapper">
                            <select name="bahasa" id="bahasaSelect" class="language-select-custom">
                                <option value="id" {{ ($bahasaAktif ?? 'id') === 'id' ? 'selected' : '' }}>Indonesia (Default)</option>
                                <option value="en" {{ ($bahasaAktif ?? '') === 'en' ? 'selected' : '' }}>English (US)</option>
                            </select>
                            <div class="language-chevron-icon" aria-hidden="true">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="form-hint" style="margin-bottom: 0;">Bahasa yang digunakan untuk tata kelola dan menu audit.</div>
                    </div>

                    <!-- Pilihan Tema -->
                    <div class="pref-group">
                        <label class="pref-label">Mode Tampilan Tema</label>
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
                                    <div class="theme-info-title">Terang</div>
                                    <div class="theme-info-desc">Mode default portal</div>
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
                                    <div class="theme-info-title">Gelap</div>
                                    <div class="theme-info-desc">Ramah mata untuk malam hari</div>
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
                                    <div class="theme-info-title">Ikuti Sistem</div>
                                    <div class="theme-info-desc">Menyesuaikan pengaturan OS</div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-actions-row" style="margin-top: 24px;">
                    <button type="submit" class="btn-primary-custom">Simpan Preferensi Tampilan</button>
                </div>
            </form>
        </div>

    </div>

</div>

@push('styles')
<style>
    .settings-page {
        max-width: 1240px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 24px;
        padding-bottom: 40px;
    }

    /* Alerts */
    .alert-banner {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 16px 20px;
        border-radius: 12px;
        font-size: 0.875rem;
    }
    .alert-success {
        background: #ECFDF5;
        border: 1px solid #A7F3D0;
        color: #065F46;
    }
    .alert-danger {
        background: #FEF2F2;
        border: 1px solid #FECACA;
        color: #991B1B;
    }
    .alert-title {
        font-weight: 700;
        margin-bottom: 2px;
    }
    .alert-desc {
        color: inherit;
    }
    .alert-list {
        margin: 4px 0 0 16px;
        padding: 0;
    }

    /* Header */
    .settings-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }
    .settings-title {
        font-size: 1.625rem;
        font-weight: 800;
        color: var(--neutral-900, #0F172A);
        margin: 0;
        line-height: 1.25;
    }
    .settings-subtitle {
        font-size: 0.9rem;
        color: var(--neutral-600, #475569);
        margin: 4px 0 0 0;
    }
    .btn-profile-shortcut {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 10px;
        background: #ffffff;
        border: 1px solid var(--neutral-300, #CBD5E1);
        color: var(--neutral-700, #334155);
        font-size: 0.875rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .btn-profile-shortcut:hover {
        background: var(--neutral-50, #F8FAFC);
        border-color: var(--blue-600, #0641CC);
        color: var(--blue-700, #0641CC);
    }

    /* Grid */
    .settings-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 24px;
        align-items: start;
    }
    .settings-card-wide {
        grid-column: 1 / -1;
    }
    @media (max-width: 960px) {
        .settings-grid {
            grid-template-columns: 1fr;
        }
        .settings-card-wide {
            grid-column: auto;
        }
    }

    /* Cards */
    .settings-card {
        background: #ffffff;
        border: 1px solid var(--neutral-200, #E2E8F0);
        border-radius: 16px;
        padding: 28px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
    }
    .card-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--neutral-900, #0F172A);
        margin: 0 0 4px 0;
    }
    .card-subtitle {
        font-size: 0.825rem;
        color: var(--neutral-500, #64748B);
        margin: 0 0 22px 0;
    }

    /* Profile Mini Header */
    .profile-mini-header {
        display: flex;
        align-items: center;
        gap: 16px;
        padding-bottom: 20px;
        margin-bottom: 20px;
        border-bottom: 1px solid #E2E8F0;
    }
    .profile-mini-avatar {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: #EFF6FF;
        color: var(--blue-700, #0641CC);
        font-size: 1.15rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        overflow: hidden;
        border: 2px solid #BFDBFE;
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
        font-size: 1.05rem;
        font-weight: 800;
        color: #0F172A;
    }
    .profile-role-chip {
        display: inline-flex;
        align-items: center;
        background: #EFF6FF;
        color: var(--blue-700, #0641CC);
        font-size: 0.725rem;
        font-weight: 700;
        padding: 3px 12px;
        border-radius: 999px;
        border: 1px solid #BFDBFE;
        width: fit-content;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    /* Info List */
    .info-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
        margin-bottom: 22px;
    }
    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.85rem;
    }
    .info-label {
        color: #64748B;
        font-weight: 500;
    }
    .info-value {
        color: #0F172A;
        font-weight: 600;
        text-align: right;
    }
    .status-badge-active {
        display: inline-flex;
        align-items: center;
        background: #ECFDF5;
        color: #065F46;
        padding: 2px 10px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
    }
    .callout-note {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 10px;
        padding: 12px 14px;
        font-size: 0.8rem;
        color: #475569;
        line-height: 1.45;
    }
    .callout-note a {
        color: var(--blue-700, #0641CC);
        font-weight: 700;
        text-decoration: underline;
    }

    /* Forms */
    .form-group-custom {
        margin-bottom: 16px;
    }
    .form-label-custom {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        color: #1E293B;
        margin-bottom: 6px;
    }
    .input-password-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }
    .input-custom {
        width: 100%;
        padding: 10px 42px 10px 14px;
        border: 1px solid var(--neutral-300, #CBD5E1);
        border-radius: 10px;
        font-size: 0.875rem;
        color: #0F172A;
        background: #ffffff;
        transition: all 0.2s ease;
    }
    .input-custom:focus {
        outline: none;
        border-color: var(--blue-600, #0641CC);
        box-shadow: 0 0 0 3px rgba(6, 65, 204, 0.12);
    }
    .input-custom.is-invalid {
        border-color: #DC2626;
        background: #FEF2F2;
    }
    .toggle-password-btn {
        position: absolute;
        right: 12px;
        background: none;
        border: none;
        color: #94A3B8;
        cursor: pointer;
        padding: 4px;
        display: flex;
        align-items: center;
    }
    .toggle-password-btn:hover {
        color: #475569;
    }
    .form-feedback-error {
        font-size: 0.775rem;
        color: #DC2626;
        margin-top: 4px;
        font-weight: 500;
    }
    .form-hint {
        font-size: 0.775rem;
        color: #64748B;
        margin-top: 6px;
        margin-bottom: 18px;
    }
    .form-actions-row {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    .btn-primary-custom {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 22px;
        border-radius: 10px;
        background: var(--blue-700, #0641CC);
        color: #ffffff;
        border: none;
        font-size: 0.875rem;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(6, 65, 204, 0.2);
        transition: all 0.2s ease;
    }
    .btn-primary-custom:hover {
        background: #0533A3;
        transform: translateY(-1px);
    }
    .btn-outline-custom {
        display: inline-flex;
        align-items: center;
        padding: 11px 18px;
        border-radius: 10px;
        background: #ffffff;
        border: 1px solid #CBD5E1;
        color: #475569;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-outline-custom:hover {
        background: #F8FAFC;
        color: #0F172A;
    }

    /* Preferences */
    .pref-horizontal-grid {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 32px;
        align-items: start;
    }
    @media (max-width: 840px) {
        .pref-horizontal-grid {
            grid-template-columns: 1fr;
        }
    }
    .pref-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .pref-label {
        font-size: 0.875rem;
        font-weight: 700;
        color: #1E293B;
    }
    .language-select-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }
    .language-select-custom {
        width: 100%;
        appearance: none;
        padding: 11px 40px 11px 14px;
        border: 1px solid #CBD5E1;
        border-radius: 10px;
        background: #ffffff;
        font-size: 0.875rem;
        font-weight: 600;
        color: #0F172A;
        cursor: pointer;
    }
    .language-chevron-icon {
        position: absolute;
        right: 14px;
        pointer-events: none;
        color: #64748B;
        transform: rotate(90deg);
        display: flex;
        align-items: center;
    }

    /* Theme Option Cards */
    .theme-options-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }
    @media (max-width: 640px) {
        .theme-options-grid {
            grid-template-columns: 1fr;
        }
    }
    .theme-card-option {
        border: 2px solid #E2E8F0;
        border-radius: 12px;
        padding: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
        gap: 10px;
        background: #F8FAFC;
        position: relative;
    }
    .theme-card-option input[type="radio"] {
        position: absolute;
        top: 10px;
        right: 10px;
        opacity: 0;
        pointer-events: none;
    }
    .theme-card-option:hover {
        border-color: #93C5FD;
        transform: translateY(-2px);
    }
    .theme-card-option.selected {
        border-color: var(--blue-600, #0641CC);
        background: #EFF6FF;
        box-shadow: 0 4px 12px rgba(6, 65, 204, 0.12);
    }
    .theme-preview-box {
        height: 52px;
        border-radius: 8px;
        padding: 8px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .preview-light {
        background: #FFFFFF;
        border: 1px solid #CBD5E1;
    }
    .preview-dark {
        background: #0F172A;
        border: 1px solid #334155;
    }
    .preview-system {
        background: linear-gradient(135deg, #FFFFFF 50%, #0F172A 50%);
        border: 1px solid #CBD5E1;
    }
    .preview-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }
    .preview-bar {
        height: 6px;
        border-radius: 3px;
        width: 60%;
    }
    .preview-light .preview-dot { background: var(--blue-600, #0641CC); }
    .preview-light .preview-bar { background: #CBD5E1; }
    .preview-dark .preview-dot { background: #60A5FA; }
    .preview-dark .preview-bar { background: #334155; }
    .preview-system .preview-dot { background: #94A3B8; }
    .preview-system .preview-bar { background: #64748B; }

    .theme-info-title {
        font-size: 0.85rem;
        font-weight: 700;
        color: #0F172A;
    }
    .theme-info-desc {
        font-size: 0.725rem;
        color: #64748B;
        line-height: 1.3;
    }

    /* Dark Mode Harmonization */
    [data-theme="dark"] .settings-page .settings-title,
    [data-theme="dark"] .settings-page .card-title,
    [data-theme="dark"] .settings-page .profile-mini-name,
    [data-theme="dark"] .settings-page .info-value,
    [data-theme="dark"] .settings-page .theme-info-title {
        color: #F8FAFC !important;
    }
    [data-theme="dark"] .settings-page .settings-subtitle,
    [data-theme="dark"] .settings-page .card-subtitle,
    [data-theme="dark"] .settings-page .info-label,
    [data-theme="dark"] .settings-page .theme-info-desc,
    [data-theme="dark"] .settings-page .form-hint {
        color: #94A3B8 !important;
    }
    [data-theme="dark"] .settings-page .settings-card {
        background: #182234 !important;
        border-color: #26354D !important;
    }
    [data-theme="dark"] .settings-page .profile-mini-header,
    [data-theme="dark"] .settings-page .callout-note {
        border-color: #26354D !important;
    }
    [data-theme="dark"] .settings-page .callout-note {
        background: #0F172A !important;
        color: #CBD5E1 !important;
    }
    [data-theme="dark"] .settings-page .btn-profile-shortcut,
    [data-theme="dark"] .settings-page .btn-outline-custom {
        background: #182234 !important;
        border-color: #3E516E !important;
        color: #E2E8F0 !important;
    }
    [data-theme="dark"] .settings-page .btn-profile-shortcut:hover,
    [data-theme="dark"] .settings-page .btn-outline-custom:hover {
        background: #1E293B !important;
        color: #60A5FA !important;
        border-color: #60A5FA !important;
    }
    [data-theme="dark"] .settings-page .input-custom,
    [data-theme="dark"] .settings-page .language-select-custom {
        background: #0F172A !important;
        border-color: #334155 !important;
        color: #F8FAFC !important;
    }
    [data-theme="dark"] .settings-page .form-label-custom,
    [data-theme="dark"] .settings-page .pref-label {
        color: #E2E8F0 !important;
    }
    [data-theme="dark"] .settings-page .theme-card-option {
        background: #0F172A !important;
        border-color: #26354D !important;
    }
    [data-theme="dark"] .settings-page .theme-card-option.selected {
        border-color: #60A5FA !important;
        background: rgba(6, 65, 204, 0.25) !important;
    }
</style>
@endpush

@push('scripts')
<script>
    function togglePasswordVisibility(fieldId, btn) {
        const input = document.getElementById(fieldId);
        if (!input) return;

        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';

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
@endpush
@endsection
