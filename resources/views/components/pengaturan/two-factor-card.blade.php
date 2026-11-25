@props([
    'user',
    'isSuperAdmin' => false,
])

@php
    $enabled = $user->hasEnabledTwoFactor();
    $roleName = $user->role?->label() ?? __('Akun');
@endphp

<div class="settings-white-card settings-2fa-card">
    <div class="settings-2fa-header-row">
        <div>
            <h2 class="card-heading-title">{{ __('Autentikasi Dua Faktor (2FA / TOTP)') }}</h2>
            <p class="card-heading-subtitle">{{ __('Lapisan perlindungan ekstra menggunakan aplikasi authenticator di smartphone Anda.') }}</p>
        </div>
        <div>
            @if($enabled)
                <span class="badge-status-pill badge-pill-success">
                    <span class="status-indicator-dot green"></span> {{ __('Aktif') }}
                </span>
            @else
                <span class="badge-status-pill badge-pill-muted">
                    <span class="status-indicator-dot gray"></span> {{ __('Belum Aktif') }}
                </span>
            @endif
        </div>
    </div>

    @if(! $enabled)
        {{-- Kondisi 2FA Belum Aktif (Opsional untuk semua role) --}}
        <div class="settings-2fa-info-box optional-box">
            <div class="info-box-icon">
                <i class="fas fa-mobile-alt" aria-hidden="true"></i>
            </div>
            <div class="info-box-text">
                <h3 class="info-box-title">{{ __('Lindungi Akun Anda dengan Aplikasi Authenticator') }}</h3>
                <p class="info-box-desc">
                    {{ __('Fitur ini bersifat pilihan (opsional). Jika diaktifkan, saat login Anda akan diminta memasukkan kode 6-digit dari Google Authenticator, Microsoft Authenticator, atau Authy, sehingga akun tetap terlindungi meskipun kata sandi diketahui pihak lain.') }}
                </p>
            </div>
        </div>

        <div class="settings-2fa-action-row">
            <form action="{{ route('2fa.enable') }}" method="POST">
                @csrf
                <button type="submit" class="btn-action-navy">
                    <i class="fas fa-shield-alt" aria-hidden="true"></i> {{ __('Aktifkan 2FA Sekarang') }}
                </button>
            </form>
        </div>
    @else
        {{-- Kondisi 2FA Sedang Aktif --}}
        <div class="settings-2fa-status-detail">
            <div class="settings-2fa-info-box active-box">
                <div class="info-box-icon">
                    <i class="fas fa-check-circle" aria-hidden="true"></i>
                </div>
                <div class="info-box-text">
                    <h3 class="info-box-title">{{ __('Perlindungan 2FA Sedang Aktif') }}</h3>
                    <p class="info-box-desc">
                        {{ __('Autentikasi dua faktor aktif sejak :waktu WIB. Setiap login akan diverifikasi menggunakan kode dari aplikasi authenticator Anda.', ['waktu' => $user->two_factor_confirmed_at?->translatedFormat('d F Y - H:i') ?? '-']) }}
                    </p>
                </div>
            </div>

            <div class="settings-2fa-action-row">
                <button type="button" class="btn-action-outline" data-action="open-modal-2fa-recovery">
                    <i class="fas fa-sync-alt" aria-hidden="true"></i> {{ __('Perbarui Recovery Codes') }}
                </button>
                <button type="button" class="btn-action-danger-outline" data-action="open-modal-2fa-disable">
                    <i class="fas fa-power-off" aria-hidden="true"></i> {{ __('Nonaktifkan 2FA') }}
                </button>
            </div>
        </div>
    @endif

    {{-- Modal Konfirmasi Nonaktifkan 2FA (Re-authentication Guard G-01) --}}
    @if($enabled)
        <div id="modal2faDisable" class="modal-2fa-backdrop" role="dialog" aria-modal="true" aria-labelledby="modal2faDisableTitle" hidden>
            <div class="modal-2fa-dialog">
                <div class="modal-2fa-header">
                    <h3 id="modal2faDisableTitle" class="modal-2fa-title">{{ __('Konfirmasi Nonaktifkan 2FA') }}</h3>
                    <button type="button" class="modal-2fa-close-btn" data-action="close-modal-2fa" aria-label="{{ __('Tutup') }}">&times;</button>
                </div>
                <form action="{{ route('2fa.disable') }}" method="POST">
                    @csrf
                    <div class="modal-2fa-body">
                        <p class="modal-2fa-desc">
                            {{ __('Menonaktifkan 2FA akan mengurangi lapisan perlindungan akun Anda. Masukkan kata sandi akun Anda saat ini untuk mengonfirmasi penonaktifan.') }}
                        </p>
                        <div class="modal-2fa-field">
                            <label for="current_password_disable" class="modal-2fa-label">{{ __('Kata Sandi Saat Ini') }}</label>
                            <input type="password" name="current_password" id="current_password_disable" class="modal-2fa-input" required placeholder="{{ __('Masukkan kata sandi akun') }}" autocomplete="current-password">
                        </div>
                    </div>
                    <div class="modal-2fa-footer">
                        <button type="button" class="btn-action-outline" data-action="close-modal-2fa">{{ __('Batal') }}</button>
                        <button type="submit" class="btn-action-danger">{{ __('Ya, Nonaktifkan 2FA') }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal Regenerasi Recovery Codes (Re-authentication Guard G-01) --}}
    @if($enabled)
        <div id="modal2faRecovery" class="modal-2fa-backdrop" role="dialog" aria-modal="true" aria-labelledby="modal2faRecoveryTitle" hidden>
            <div class="modal-2fa-dialog">
                <div class="modal-2fa-header">
                    <h3 id="modal2faRecoveryTitle" class="modal-2fa-title">{{ __('Perbarui Kode Pemulihan (Recovery Codes)') }}</h3>
                    <button type="button" class="modal-2fa-close-btn" data-action="close-modal-2fa" aria-label="{{ __('Tutup') }}">&times;</button>
                </div>
                <form action="{{ route('2fa.regenerate-recovery-codes') }}" method="POST">
                    @csrf
                    <div class="modal-2fa-body">
                        <p class="modal-2fa-desc">
                            {{ __('Memperbarui kode pemulihan akan membatalkan seluruh kode lama Anda. Masukkan kata sandi saat ini untuk menghasilkan 8 kode pemulihan darurat baru.') }}
                        </p>
                        <div class="modal-2fa-field">
                            <label for="current_password_recovery" class="modal-2fa-label">{{ __('Kata Sandi Saat Ini') }}</label>
                            <input type="password" name="current_password" id="current_password_recovery" class="modal-2fa-input" required placeholder="{{ __('Masukkan kata sandi akun') }}" autocomplete="current-password">
                        </div>
                    </div>
                    <div class="modal-2fa-footer">
                        <button type="button" class="btn-action-outline" data-action="close-modal-2fa">{{ __('Batal') }}</button>
                        <button type="submit" class="btn-action-navy">{{ __('Buat Kode Baru') }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
