<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Pengaturan as PengaturanModel;

final class Pengaturan
{
    /**
     * Menandakan apakah sistem sedang dalam mode pratinjau (preview).
     */
    private static bool $previewMode = false;

    /**
     * Aktifkan atau nonaktifkan mode pratinjau.
     */
    public static function setPreviewMode(bool $enabled): void
    {
        self::$previewMode = $enabled;
    }

    /**
     * Periksa apakah mode pratinjau sedang aktif.
     */
    public static function isPreviewMode(): bool
    {
        return self::$previewMode;
    }

    /**
     * Ambil nilai konfigurasi sistem (string).
     * Jika dalam mode pratinjau dan ada draf, kembalikan nilai draf.
     */
    public static function get(string $kunci, ?string $default = null): ?string
    {
        if (self::$previewMode) {
            $draftVal = PengaturanModel::getNilai('draft_'.$kunci);
            if ($draftVal !== null && $draftVal !== '') {
                return $draftVal;
            }
        }

        return PengaturanModel::getNilai($kunci, $default);
    }

    /**
     * Ambil nilai konfigurasi murni dari versi live (abaikan draf).
     */
    public static function getLive(string $kunci, ?string $default = null): ?string
    {
        return PengaturanModel::getNilai($kunci, $default);
    }

    /**
     * Ambil nilai draf untuk kunci tertentu jika ada.
     */
    public static function getDraft(string $kunci, ?string $default = null): ?string
    {
        return PengaturanModel::getNilai('draft_'.$kunci, $default);
    }

    /**
     * Cek apakah ada nilai draf untuk kunci tertentu.
     */
    public static function hasDraft(string $kunci): bool
    {
        $val = PengaturanModel::getNilai('draft_'.$kunci);

        return $val !== null && $val !== '';
    }

    /**
     * Ambil nilai konfigurasi sebagai integer.
     */
    public static function int(string $kunci, int $default = 0): int
    {
        if (self::$previewMode) {
            $draftVal = PengaturanModel::getNilai('draft_'.$kunci);
            if ($draftVal !== null && $draftVal !== '') {
                return (int) $draftVal;
            }
        }

        return PengaturanModel::getInt($kunci, $default);
    }

    /**
     * Ambil nilai konfigurasi sebagai boolean.
     */
    public static function bool(string $kunci, bool $default = false): bool
    {
        if (self::$previewMode) {
            $draftVal = PengaturanModel::getNilai('draft_'.$kunci);
            if ($draftVal !== null && $draftVal !== '') {
                return in_array(strtolower((string) $draftVal), ['1', 'true', 'on', 'yes'], true);
            }
        }

        return PengaturanModel::getBool($kunci, $default);
    }

    /**
     * Ambil nilai konfigurasi sebagai array dari JSON.
     * Jika dalam mode pratinjau dan ada draf, kembalikan data draf JSON.
     *
     * @param  array<mixed>  $default
     * @return array<mixed>
     */
    public static function json(string $kunci, array $default = []): array
    {
        if (self::$previewMode) {
            $draftVal = PengaturanModel::getNilai('draft_'.$kunci);
            if ($draftVal !== null && $draftVal !== '') {
                $decoded = json_decode($draftVal, true);
                if (is_array($decoded)) {
                    return $decoded;
                }
            }
        }

        return PengaturanModel::getJson($kunci, $default);
    }

    /**
     * Simpan nilai konfigurasi sebagai JSON string.
     *
     * @param  array<mixed>  $data
     */
    public static function setJson(string $kunci, array $data): void
    {
        PengaturanModel::setNilai($kunci, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    /**
     * Simpan atau perbarui nilai konfigurasi.
     */
    public static function set(string $kunci, string $nilai): void
    {
        PengaturanModel::setNilai($kunci, $nilai);
    }

    /**
     * Hapus konfigurasi berdasarkan kunci.
     */
    public static function delete(string $kunci): void
    {
        PengaturanModel::deleteKunci($kunci);
    }

    /**
     * Bersihkan cache konfigurasi sistem.
     */
    public static function clearCache(?string $kunci = null): void
    {
        PengaturanModel::clearCache($kunci);
    }
}
