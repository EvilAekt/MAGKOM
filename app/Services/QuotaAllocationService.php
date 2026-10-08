<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\StatusPendaftaranEnum;
use App\Exceptions\Domain\KuotaPenuhException;
use App\Exceptions\Domain\PendaftaranAktifGandaException;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\SlotKhusus;
use Illuminate\Support\Facades\DB;

class QuotaAllocationService
{
    /**
     * Mengambil 1 jatah kuota umum secara atomik dengan locking baris (AC-05-4).
     * Wajib dijalankan di dalam blok DB::transaction().
     *
     * @throws KuotaPenuhException
     */
    public function ambilKuotaUmum(int $kuotaId): Kuota
    {
        assert(DB::transactionLevel() > 0, 'Metode ambilKuotaUmum harus dipanggil dalam transaksi database.');

        $kuota = Kuota::whereKey($kuotaId)->lockForUpdate()->firstOrFail();

        if (! $kuota->aktif || $kuota->terisi >= $kuota->jumlah) {
            throw new KuotaPenuhException('Kuota umum untuk jurusan dan periode ini sudah terisi penuh.');
        }

        $kuota->increment('terisi');

        return $kuota->fresh();
    }

    /**
     * Mengambil 1 jatah slot khusus internal SuperAdmin secara atomik.
     *
     * @throws KuotaPenuhException
     */
    public function ambilKuotaKhusus(int $slotKhususId): SlotKhusus
    {
        assert(DB::transactionLevel() > 0, 'Metode ambilKuotaKhusus harus dipanggil dalam transaksi database.');

        $slot = SlotKhusus::whereKey($slotKhususId)->lockForUpdate()->firstOrFail();

        if (! $slot->aktif || $slot->terisi >= $slot->jumlah) {
            throw new KuotaPenuhException('Slot khusus internal untuk periode ini sudah terisi penuh.');
        }

        $slot->increment('terisi');

        return $slot->fresh();
    }

    /**
     * Mengembalikan 1 jatah kuota umum jika pendaftaran dibatalkan atau mengundurkan diri (BR-06).
     */
    public function lepasKuotaUmum(int $kuotaId): void
    {
        assert(DB::transactionLevel() > 0, 'Metode lepasKuotaUmum harus dipanggil dalam transaksi database.');

        $kuota = Kuota::whereKey($kuotaId)->lockForUpdate()->firstOrFail();

        if ($kuota->terisi > 0) {
            $kuota->decrement('terisi');
        }
    }

    /**
     * Mengembalikan 1 jatah slot khusus.
     */
    public function lepasKuotaKhusus(int $slotKhususId): void
    {
        assert(DB::transactionLevel() > 0, 'Metode lepasKuotaKhusus harus dipanggil dalam transaksi database.');

        $slot = SlotKhusus::whereKey($slotKhususId)->lockForUpdate()->firstOrFail();

        if ($slot->terisi > 0) {
            $slot->decrement('terisi');
        }
    }

    /**
     * Memeriksa dan menegakkan aturan BR-17 (hanya 1 pendaftaran aktif per peserta).
     *
     * @throws PendaftaranAktifGandaException
     */
    public function pastikanTidakAdaPendaftaranAktif(int $userId, ?int $abaikanPendaftaranId = null): void
    {
        $query = Pendaftaran::where('user_id', $userId)
            ->whereIn('status', [
                StatusPendaftaranEnum::BerkasBelumLengkap->value,
                StatusPendaftaranEnum::Diajukan->value,
                StatusPendaftaranEnum::PerluPerbaikan->value,
                StatusPendaftaranEnum::WaitingList->value,
                StatusPendaftaranEnum::Diterima->value,
            ]);

        if ($abaikanPendaftaranId) {
            $query->where('id', '!=', $abaikanPendaftaranId);
        }

        if ($query->exists()) {
            throw new PendaftaranAktifGandaException();
        }
    }
}
