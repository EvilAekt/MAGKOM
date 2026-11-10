<?php

declare(strict_types=1);

namespace App\Actions\Peserta\Profil;

use App\Enums\TipeAuditEnum;
use App\Models\PesertaProfil;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class UpdatePesertaProfilAction
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Menyimpan atau memperbarui biodata profil peserta secara transaksional dengan audit trail dan manajemen berkas aman.
     *
     * @param  array<string, mixed>  $validatedData
     *
     * @throws Throwable
     */
    public function execute(
        User $user,
        array $validatedData,
        ?UploadedFile $fotoFile = null,
        bool $hapusFoto = false,
        bool $persetujuanWali = false
    ): PesertaProfil {
        $storedNewPhotoPath = null;
        $oldPhotoToDelete = null;

        try {
            return DB::transaction(function () use (
                $user,
                $validatedData,
                $fotoFile,
                $hapusFoto,
                $persetujuanWali,
                &$storedNewPhotoPath,
                &$oldPhotoToDelete
            ) {
                // 1. Pessimistic lock pada baris pengguna induk
                /** @var User $lockedUser */
                $lockedUser = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
                $profil = $lockedUser->profil;

                $nilaiLama = $profil ? $profil->toArray() : null;
                $payload = $validatedData;

                // 2. Data registrasi awal peserta (Institusi, NIM/NISN, Jurusan, Jenjang) bersifat permanen / immutable
                if ($profil && $profil->exists && ! empty($profil->institusi)) {
                    unset($payload['institusi'], $payload['nim_nisn'], $payload['jurusan_id'], $payload['jenjang']);
                }

                // 3. Tangani unggah atau penghapusan berkas foto profil
                if ($hapusFoto) {
                    if ($profil?->foto) {
                        $oldPhotoToDelete = $profil->foto;
                    }
                    $payload['foto'] = null;
                } elseif ($fotoFile instanceof UploadedFile) {
                    if ($profil?->foto) {
                        $oldPhotoToDelete = $profil->foto;
                    }
                    $extension = $fotoFile->getClientOriginalExtension() ?: 'jpg';
                    $filename = 'peserta_'.$lockedUser->id.'_'.time().'_'.bin2hex(random_bytes(4)).'.'.$extension;
                    $storedNewPhotoPath = $fotoFile->storeAs('profil-peserta', $filename, 'public');
                    $payload['foto'] = $storedNewPhotoPath;
                } else {
                    // Hindari menimpa kolom foto jika tidak ada file baru yang diunggah
                    unset($payload['foto']);
                }

                unset($payload['hapus_foto']);

                // 4. Tangani persetujuan wali untuk jenjang SMK (UU PDP)
                if ($persetujuanWali) {
                    $payload['persetujuan_wali_at'] = now();
                }
                unset($payload['persetujuan_wali']);

                // 5. Sinkronisasi email pengguna jika diizinkan
                if (isset($payload['email'])) {
                    $newEmail = trim((string) $payload['email']);
                    if ($newEmail !== '' && $newEmail !== $lockedUser->email) {
                        $lockedUser->email = $newEmail;
                        $lockedUser->save();
                    }
                    unset($payload['email']);
                }

                // 6. Simpan atau perbarui entri profil peserta
                $updatedProfil = PesertaProfil::updateOrCreate(
                    ['user_id' => $lockedUser->id],
                    $payload
                );

                // 7. Catat jejak audit
                $this->auditService->catat(
                    tipe: TipeAuditEnum::Aksi,
                    tabel: 'peserta_profil',
                    idData: $updatedProfil->id,
                    nilaiLama: $nilaiLama,
                    nilaiBaru: $updatedProfil->toArray(),
                    detail: ['aksi' => 'Memperbarui biodata profil peserta']
                );

                // 8. Pasca-commit: hapus foto fisik lama secara aman jika diganti/dihapus
                if ($oldPhotoToDelete) {
                    $targetPath = $oldPhotoToDelete;
                    DB::afterCommit(function () use ($targetPath) {
                        if (Storage::disk('public')->exists($targetPath)) {
                            Storage::disk('public')->delete($targetPath);
                        }
                    });
                }

                return $updatedProfil;
            });
        } catch (Throwable $e) {
            // Bersihkan file foto baru jika transaksi database gagal
            if ($storedNewPhotoPath && Storage::disk('public')->exists($storedNewPhotoPath)) {
                Storage::disk('public')->delete($storedNewPhotoPath);
            }

            throw $e;
        }
    }
}

