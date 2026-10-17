<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RoleEnum;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Kolom mass-assignable.
     * CATATAN: Kolom 'role' DILARANG masuk $fillable demi keamanan (NFR-S8, TDD 5.2).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'email',
        'no_telp',
        'foto',
        'password',
        'provider',
        'provider_id',
        'bidang_id',
        'status_aktif',
        'must_change_password',
        'dibuat_oleh',
    ];

    /**
     * Kolom yang disembunyikan dalam serialisasi JSON.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Nilai default atribut model.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status_aktif' => true,
        'must_change_password' => false,
    ];

    /**
     * Cast tipe data kolom.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => RoleEnum::class,
        'status_aktif' => 'boolean',
        'must_change_password' => 'boolean',
    ];

    /**
     * Accessor atribut 'name' agar kompatibel dengan konvensi Laravel dan template tampilan.
     */
    public function getNameAttribute(): ?string
    {
        return $this->nama;
    }

    /**
     * Accessor URL berkas foto profil pengguna / staf.
     */
    public function getFotoUrlAttribute(): ?string
    {
        if ($this->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->foto)) {
            return \Illuminate\Support\Facades\Storage::disk('public')->url($this->foto);
        }

        // Fallback untuk akun peserta yang menyimpan foto di tabel peserta_profil
        if ($this->isPeserta() && $this->profil?->foto_url) {
            return $this->profil->foto_url;
        }

        return null;
    }


    /**
     * Relasi ke unit kerja bidang (khusus staf).
     */
    public function bidang(): BelongsTo
    {
        return $this->belongsTo(Bidang::class, 'bidang_id');
    }

    /**
     * Relasi ke user staf yang membuat akun ini.
     */
    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(self::class, 'dibuat_oleh');
    }

    /**
     * Relasi ke profil peserta (khusus role peserta).
     */
    public function profil(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PesertaProfil::class, 'user_id');
    }

    /**
     * Relasi ke riwayat pengajuan pendaftaran magang.
     */
    public function pendaftaran(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Pendaftaran::class, 'user_id');
    }

    /**
     * Relasi ke catatan penempatan magang aktif (sebagai peserta).
     */
    public function penempatanPeserta(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Penempatan::class, 'peserta_id');
    }

    /**
     * Relasi ke peserta yang dibimbing (sebagai pembimbing).
     */
    public function penempatanBimbingan(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Penempatan::class, 'pembimbing_id');
    }

    /**
     * Helper cek peran pengguna.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === RoleEnum::SuperAdmin;
    }

    public function isAdmin(): bool
    {
        return $this->role === RoleEnum::Admin;
    }

    public function isPembimbing(): bool
    {
        return $this->role === RoleEnum::Pembimbing;
    }

    public function isPeserta(): bool
    {
        return $this->role === RoleEnum::Peserta;
    }

    public function isStaf(): bool
    {
        return in_array($this->role, [
            RoleEnum::SuperAdmin,
            RoleEnum::Admin,
            RoleEnum::Pembimbing,
        ], true);
    }

    /**
     * Memeriksa apakah peserta magang telah resmi diterima dan memiliki penempatan aktif / selesai.
     */
    public function hasPenempatanMagang(): bool
    {
        if ($this->relationLoaded('penempatanPeserta')) {
            return $this->penempatanPeserta->contains(function ($p) {
                return in_array($p->status, [\App\Enums\StatusPenempatanEnum::Aktif, \App\Enums\StatusPenempatanEnum::Selesai], true);
            });
        }

        return $this->penempatanPeserta()
            ->whereIn('status', [\App\Enums\StatusPenempatanEnum::Aktif, \App\Enums\StatusPenempatanEnum::Selesai])
            ->exists();
    }

    /**
     * Scope untuk pengguna aktif.
     */
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status_aktif', true);
    }

    /**
     * Scope untuk akun staf internal.
     */
    public function scopeStaf(Builder $query): Builder
    {
        return $query->whereIn('role', [
            RoleEnum::SuperAdmin->value,
            RoleEnum::Admin->value,
            RoleEnum::Pembimbing->value,
        ]);
    }
}
