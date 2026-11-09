<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\JenjangEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PesertaProfil extends Model
{
    use HasFactory;

    protected $table = 'peserta_profil';

    protected $fillable = [
        'user_id',
        'nim_nisn',
        'institusi',
        'nama_pembimbing_sekolah',
        'jurusan_id',
        'jenjang',
        'no_hp',
        'alamat',
        'foto',
        'nama_kontak_darurat',
        'no_kontak_darurat',
        'hubungan_kontak_darurat',
        'persetujuan_wali_at',
    ];

    /**
     * Accessor untuk URL foto profil publik.
     */
    public function getFotoUrlAttribute(): ?string
    {
        if (! empty($this->foto)) {
            return asset('storage/'.$this->foto);
        }

        return null;
    }

    protected $casts = [
        'jenjang' => JenjangEnum::class,
        'persetujuan_wali_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }

    /**
     * Hitung kelengkapan profil peserta (0 - 100%) berdasarkan 5 pilar wajib.
     *
     * @return array{checklist: array<string, bool>, persentase: int, is_lengkap: bool}
     */
    public function hitungKelengkapan(): array
    {
        $user = $this->relationLoaded('user') ? $this->user : ($this->user()->first() ?? auth()->user());

        $akademikLengkap = ! empty($this->institusi) &&
            $this->institusi !== 'Institusi Belum Diatur' &&
            ! empty($this->nim_nisn) &&
            $this->nim_nisn !== '-' &&
            ! empty($this->jenjang) &&
            ! empty($this->jurusan_id);

        $kontakLengkap = ! empty($this->no_hp) && $this->no_hp !== '-';

        $checklist = [
            'data_diri' => ! empty($user?->nama ?? $user?->name) && ! empty($user?->email),
            'foto' => ! empty($this->foto),
            'akademik' => $akademikLengkap,
            'kontak' => $kontakLengkap,
            'darurat' => ! empty($this->nama_kontak_darurat) && ! empty($this->no_kontak_darurat),
        ];

        $totalWajib = count($checklist);
        $terisi = count(array_filter($checklist));
        $persentase = (int) round(($terisi / $totalWajib) * 100);

        return [
            'checklist' => $checklist,
            'persentase' => $persentase,
            'is_lengkap' => $persentase === 100,
        ];
    }

    /**
     * Cek apakah profil peserta sudah 100% lengkap.
     */
    public function isLengkap(): bool
    {
        return $this->hitungKelengkapan()['is_lengkap'];
    }
}
