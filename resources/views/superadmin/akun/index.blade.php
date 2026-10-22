@extends('layouts.dashboard')

@section('title', 'Akun & Preferensi Saya - Super Administrator MagKom')

@push('styles')
<style>
    /* ========================================================
       MAGKOM GENTELELLA DESIGN SYSTEM: SUPERADMIN AKUN
       ======================================================== */
    .superadmin-akun-wrapper {
        display: flex;
        flex-direction: column;
        gap: 24px;
        padding-bottom: 32px;
    }

    .superadmin-akun-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 24px;
        align-items: start;
    }

    @media (max-width: 960px) {
        .superadmin-akun-grid {
            grid-template-columns: 1fr;
        }
    }

    .profile-mini-header {
        display: flex;
        align-items: center;
        gap: 16px;
        padding-bottom: 20px;
        margin-bottom: 20px;
        border-bottom: 1px solid var(--neutral-200, #E2E8F0);
    }

    .profile-mini-avatar {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: #EFF6FF;
        color: #0751FF;
        font-size: 1.25rem;
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

    .info-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
        margin-bottom: 22px;
    }

    .info-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.875rem;
    }

    .info-label {
        color: var(--neutral-500, #64748B);
    }

    .info-value {
        color: var(--neutral-800, #1E293B);
        font-weight: 600;
        text-align: right;
    }

    .callout-note {
        background: #F8FAFC;
        border: 1px solid var(--neutral-200, #E2E8F0);
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 0.8125rem;
        color: var(--neutral-600, #475569);
        line-height: 1.5;
        display: flex;
        gap: 10px;
        align-items: flex-start;
    }

    .callout-note a {
        color: #0751FF;
        font-weight: 600;
        text-decoration: none;
    }

    .callout-note a:hover {
        text-decoration: underline;
    }

    /* Password Input */
    .input-password-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .toggle-password-btn {
        position: absolute;
        right: 12px;
        background: none;
        border: none;
        padding: 4px;
        color: var(--neutral-500, #64748B);
        cursor: pointer;
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
        border: 1.5px solid var(--neutral-300, #CBD5E1);
        border-radius: 10px;
        padding: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
        background: #ffffff;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .theme-card-option:hover {
        border-color: #0751FF;
    }

    .theme-card-option.selected {
        border-color: #0641CC !important;
        box-shadow: 0 0 0 2px rgba(7, 81, 255, 0.2);
    }

    .theme-card-option input[type="radio"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .theme-preview-box {
        height: 36px;
        border-radius: 6px;
        padding: 6px 10px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .preview-light { background: #F1F5F9; border: 1px solid #E2E8F0; }
    .preview-dark { background: #1E293B; border: 1px solid #0F172A; }
    .preview-system { background: linear-gradient(115deg, #F1F5F9 48%, #1E293B 48%); border: 1px solid #CBD5E1; }

    .preview-dot { width: 6px; height: 6px; border-radius: 50%; background: #94A3B8; }
    .preview-bar { height: 4px; flex: 1; border-radius: 2px; background: #CBD5E1; }
    .preview-dark .preview-dot { background: #60A5FA; }
    .preview-dark .preview-bar { background: #334155; }

    /* Dark Mode Harmonization */
    [data-theme="dark"] .profile-mini-header { border-bottom-color: #26354D !important; }
    [data-theme="dark"] .info-label { color: #94A3B8 !important; }
    [data-theme="dark"] .info-value { color: #F8FAFC !important; }
    [data-theme="dark"] .callout-note { background: #0F172A !important; border-color: #26354D !important; color: #94A3B8 !important; }
    [data-theme="dark"] .theme-card-option { background: #182234; border-color: #26354D; color: #F8FAFC; }
    [data-theme="dark"] .theme-card-option.selected { border-color: #3B82F6 !important; box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3); }
</style>
@endpush

@section('content')
<div class="superadmin-akun-wrapper">

    {{-- Header Halaman --}}
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0 0 4px 0;">
                Akun & Preferensi Saya
            </h1>
            <p style="font-size: 0.875rem; color: var(--neutral-500, #64748B); margin: 0;">
                Kelola keamanan kredensial Super Administrator dan preferensi visual aplikasi.
            </p>
        </div>
        <div>
            <a href="{{ route('superadmin.profil.show') }}" class="btn btn-secondary">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span>Lihat Profil Lengkap</span>
            </a>
        </div>
    </div>

    {{-- Layout 2 Kolom Gentelella --}}
    <div class="superadmin-akun-grid">

        {{-- Kolom Kiri: Informasi Akun --}}
        <x-gentelella.x-panel title="Informasi Akun" subtitle="Ringkasan kredensial dan hak akses Super Administrator.">
            <div class="profile-mini-header">
                <div class="profile-mini-avatar">
                    @if($user->foto_url)
                        <img src="{{ $user->foto_url }}" alt="{{ $user->nama }}">
                    @else
                        {{ strtoupper(substr($user->nama ?? 'S', 0, 2)) }}
                    @endif
                </div>
                <div>
                    <div style="font-size: 1.0625rem; font-weight: 700; color: var(--neutral-800, #1E293B);">
                        {{ $user->nama }}
                    </div>
                    <x-gentelella.badge variant="danger" style="margin-top: 4px;">
                        Super Administrator
                    </x-gentelella.badge>
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
                        <x-gentelella.badge variant="success">Aktif & Aman</x-gentelella.badge>
                    </span>
                </div>
            </div>

            <div class="callout-note">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink: 0; color: #0751FF;">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                <span>Foto pasfoto dan nomor telepon dinas dapat diperbarui di halaman <a href="{{ route('superadmin.profil.show') }}">Profil Saya</a>. Konfigurasi sistem global tersedia di <a href="{{ route('superadmin.pengaturan.index') }}">Pengaturan Sistem</a>.</span>
            </div>
        </x-gentelella.x-panel>

        {{-- Kolom Kanan: Ganti Kata Sandi --}}
        <x-gentelella.x-panel title="Ganti Kata Sandi" subtitle="Gunakan kombinasi sandi tingkat tinggi untuk perlindungan akun Super Admin.">
            <form action="{{ route('superadmin.akun.password.update') }}" method="POST" id="passwordForm">
                @csrf
                @method('PUT')

                {{-- Kata Sandi Saat Ini --}}
                <div class="form-group" style="margin-bottom: 16px;">
                    <label for="current_password" class="form-label" style="font-weight: 700;">Kata sandi saat ini</label>
                    <div class="input-password-wrapper">
                        <input type="password" 
                               name="current_password" 
                               id="current_password" 
                               class="form-control @error('current_password') is-invalid @enderror" 
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
                        <span style="font-size: 0.75rem; color: #DC2626; display: block; margin-top: 4px;">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Kata Sandi Baru --}}
                <div class="form-group" style="margin-bottom: 16px;">
                    <label for="password" class="form-label" style="font-weight: 700;">Kata sandi baru</label>
                    <div class="input-password-wrapper">
                        <input type="password" 
                               name="password" 
                               id="password" 
                               class="form-control @error('password') is-invalid @enderror" 
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
                        <span style="font-size: 0.75rem; color: #DC2626; display: block; margin-top: 4px;">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Konfirmasi Kata Sandi Baru --}}
                <div class="form-group" style="margin-bottom: 16px;">
                    <label for="password_confirmation" class="form-label" style="font-weight: 700;">Konfirmasi kata sandi baru</label>
                    <div class="input-password-wrapper">
                        <input type="password" 
                               name="password_confirmation" 
                               id="password_confirmation" 
                               class="form-control @error('password_confirmation') is-invalid @enderror" 
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
                        <span style="font-size: 0.75rem; color: #DC2626; display: block; margin-top: 4px;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="font-size: 0.75rem; color: var(--neutral-500, #64748B); margin-bottom: 20px;">
                    Minimal 8 karakter, kombinasi huruf besar/kecil, angka, dan simbol. Setiap pergantian kata sandi akan tercatat di Audit Log sistem.
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary" id="btnSubmitPassword">Perbarui Kata Sandi</button>
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('passwordForm').reset()">Batal</button>
                </div>
            </form>
        </x-gentelella.x-panel>

        {{-- Kolom Penuh: Preferensi Antarmuka & Bahasa --}}
        <div style="grid-column: 1 / -1;">
            <x-gentelella.x-panel title="{{ __('Preferensi Antarmuka & Bahasa') }}" subtitle="{{ __('Sesuaikan kenyamanan visual dan bahasa saat mengawasi seluruh sistem MagKom.') }}">
                <form action="{{ route('superadmin.akun.preferensi.update') }}" method="POST">
                    @csrf

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
                        {{-- Pilihan Bahasa --}}
                        <div class="form-group">
                            <label class="form-label" for="bahasaSelect" style="font-weight: 700;">{{ __('Bahasa Antarmuka') }}</label>
                            <select name="bahasa" id="bahasaSelect" class="form-control">
                                <option value="id" {{ ($bahasaAktif ?? 'id') === 'id' ? 'selected' : '' }}>{{ __('Indonesia (Default)') }}</option>
                                <option value="en" {{ ($bahasaAktif ?? '') === 'en' ? 'selected' : '' }}>{{ __('English (US)') }}</option>
                            </select>
                            <span style="font-size: 0.75rem; color: var(--neutral-500, #64748B); margin-top: 4px; display: block;">
                                {{ __('Bahasa yang digunakan untuk tata kelola dan menu audit.') }}
                            </span>
                        </div>

                        {{-- Pilihan Tema --}}
                        <div class="form-group">
                            <label class="form-label" style="font-weight: 700;">{{ __('Mode Tampilan Tema') }}</label>
                            <div class="theme-options-grid">
                                <label class="theme-card-option {{ ($temaAktif ?? 'terang') === 'terang' ? 'selected' : '' }}" 
                                       data-theme-val="terang" 
                                       onclick="handleThemeSelection(this, 'terang')">
                                    <input type="radio" name="tema" value="terang" {{ ($temaAktif ?? 'terang') === 'terang' ? 'checked' : '' }}>
                                    <div class="theme-preview-box preview-light">
                                        <span class="preview-dot"></span>
                                        <span class="preview-bar"></span>
                                    </div>
                                    <div>
                                        <div style="font-size: 0.8125rem; font-weight: 700;">{{ __('Terang') }}</div>
                                        <div style="font-size: 0.6875rem; color: var(--neutral-500, #64748B);">{{ __('Mode default portal') }}</div>
                                    </div>
                                </label>

                                <label class="theme-card-option {{ ($temaAktif ?? '') === 'gelap' ? 'selected' : '' }}" 
                                       data-theme-val="gelap" 
                                       onclick="handleThemeSelection(this, 'gelap')">
                                    <input type="radio" name="tema" value="gelap" {{ ($temaAktif ?? '') === 'gelap' ? 'checked' : '' }}>
                                    <div class="theme-preview-box preview-dark">
                                        <span class="preview-dot"></span>
                                        <span class="preview-bar"></span>
                                    </div>
                                    <div>
                                        <div style="font-size: 0.8125rem; font-weight: 700;">{{ __('Gelap') }}</div>
                                        <div style="font-size: 0.6875rem; color: var(--neutral-500, #64748B);">{{ __('Ramah mata untuk malam hari') }}</div>
                                    </div>
                                </label>

                                <label class="theme-card-option {{ ($temaAktif ?? '') === 'sistem' ? 'selected' : '' }}" 
                                       data-theme-val="sistem" 
                                       onclick="handleThemeSelection(this, 'sistem')">
                                    <input type="radio" name="tema" value="sistem" {{ ($temaAktif ?? '') === 'sistem' ? 'checked' : '' }}>
                                    <div class="theme-preview-box preview-system">
                                        <span class="preview-dot"></span>
                                        <span class="preview-bar"></span>
                                    </div>
                                    <div>
                                        <div style="font-size: 0.8125rem; font-weight: 700;">{{ __('Ikuti Sistem') }}</div>
                                        <div style="font-size: 0.6875rem; color: var(--neutral-500, #64748B);">{{ __('Menyesuaikan pengaturan OS') }}</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 20px;">
                        <button type="submit" class="btn btn-primary">{{ __('Simpan Preferensi Tampilan') }}</button>
                    </div>
                </form>
            </x-gentelella.x-panel>
        </div>

    </div>

</div>

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
