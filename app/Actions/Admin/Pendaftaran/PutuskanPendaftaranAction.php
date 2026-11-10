<?php

declare(strict_types=1);

namespace App\Actions\Admin\Pendaftaran;

use App\Models\Bidang;
use App\Models\Pendaftaran;
use App\Models\User;
use App\Services\PlacementService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;

class PutuskanPendaftaranAction
{
    public function __construct(
        protected PlacementService $placementService
    ) {}

    /**
     * Memproses keputusan verifikasi berkas pendaftaran (terima atau tolak) dan penempatan langsung jika ada.
     *
     * @return array{pendaftaran: Pendaftaran, message: string}
     */
    public function execute(
        User $admin,
        Pendaftaran $pendaftaran,
        string $keputusan,
        ?string $catatan = null,
        ?UploadedFile $fileSurat = null,
        ?int $pembimbingId = null,
        ?int $bidangId = null
    ): array {
        Gate::authorize('review', $pendaftaran);

        if ($keputusan === 'terima') {
            $p = $this->placementService->terimaBerkas(
                $pendaftaran,
                $admin,
                $catatan,
                $fileSurat
            );

            if ($pembimbingId) {
                $pembimbing = User::findOrFail($pembimbingId);
                $bidang = $bidangId ? Bidang::find($bidangId) : null;
                $this->placementService->tetapkanPenempatan($p, $admin, $pembimbing, $bidang);

                return [
                    'pendaftaran' => $p,
                    'message' => 'Pendaftaran berhasil diterima dan langsung ditempatkan kepada pembimbing.',
                ];
            }

            return [
                'pendaftaran' => $p,
                'message' => 'Berkas pendaftaran berhasil diterima. Silakan tetapkan pembimbing lapangan di menu Penempatan Peserta.',
            ];
        }

        $p = $this->placementService->tolak($pendaftaran, $admin, (string) $catatan);

        return [
            'pendaftaran' => $p,
            'message' => 'Pengajuan pendaftaran berhasil ditolak.',
        ];
    }
}
