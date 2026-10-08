<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Pengaturan as PengaturanModel;

final class Pengaturan
{
    /**
     * Ambil nilai konfigurasi sistem (string).
     */
    public static function get(string $kunci, ?string $default = null): ?string
    {
        return PengaturanModel::getNilai($kunci, $default);
    }

    /**
     * Ambil nilai konfigurasi sebagai integer.
     */
    public static function int(string $kunci, int $default = 0): int
    {
        return PengaturanModel::getInt($kunci, $default);
    }

    /**
     * Ambil nilai konfigurasi sebagai boolean.
     */
    public static function bool(string $kunci, bool $default = false): bool
    {
        return PengaturanModel::getBool($kunci, $default);
    }

    /**
     * Simpan atau perbarui nilai konfigurasi.
     */
    public static function set(string $kunci, string $nilai): void
    {
        PengaturanModel::setNilai($kunci, $nilai);
    }
}
