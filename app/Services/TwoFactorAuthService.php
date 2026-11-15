<?php

declare(strict_types=1);

namespace App\Services;

use PragmaRX\Google2FA\Google2FA;

class TwoFactorAuthService
{
    protected Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA;
    }

    /**
     * Menghasilkan kunci rahasia Base32 unik untuk TOTP (RFC 6238).
     */
    public function generateSecretKey(): string
    {
        return $this->google2fa->generateSecretKey(32);
    }

    /**
     * Menghasilkan URL otentikasi otpauth:// standard untuk aplikasi Authenticator.
     */
    public function getQRCodeUrl(string $company, string $holder, string $secret): string
    {
        return $this->google2fa->getQRCodeUrl($company, $holder, $secret);
    }

    /**
     * Memverifikasi kode numerik 6-digit TOTP dengan window toleransi 1 periode (±30 detik).
     */
    public function verifyKey(string $secret, string $code): bool
    {
        $cleanCode = preg_replace('/[^0-9]/', '', $code) ?? '';

        if (strlen($cleanCode) !== 6) {
            return false;
        }

        return (bool) $this->google2fa->verifyKey($secret, $cleanCode, 1);
    }

    /**
     * Mendapatkan kode OTP saat ini untuk keperluan pengujian dan verifikasi sinkronisasi.
     */
    public function getCurrentOtp(string $secret): string
    {
        return $this->google2fa->getCurrentOtp($secret);
    }
}
