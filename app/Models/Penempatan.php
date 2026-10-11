<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\JalurEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\SumberPenempatanEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Penempatan extends Model
{
    use HasFactory;

    protected $table = 'penempatan';

    protected $fillable = [
        'pendaftaran_id',
        'peserta_id',
        'kuota_id',
        'slot_khusus_id',
        'bidang_id',
        'pembimbing_id',
        'jalur',
        'sumber',
        'dibuat_oleh',
        'tgl_mulai',
        'tgl_selesai',
        'tgl_ditetapkan_selesai',
        'tgl_nonaktif',
        'status',
    ];

    protected $casts = [
        'jalur' => JalurEnum::class,
        'sumber' => SumberPenempatanEnum::class,
        'status' => StatusPenempatanEnum::class,
        'tgl_mulai' => 'date',
        'tgl_selesai' => 'date',
        'tgl_ditetapkan_selesai' => 'date',
        'tgl_nonaktif' => 'date',
    ];

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class, 'pendaftaran_id');
    }

    public function peserta(): BelongsTo
    {
        return $this->belongsTo(User::class, 'peserta_id');
    }

    public function pembimbing(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pembimbing_id');
    }

    public function kuota(): BelongsTo
    {
        return $this->belongsTo(Kuota::class, 'kuota_id');
    }

    public function slotKhusus(): BelongsTo
    {
        return $this->belongsTo(SlotKhusus::class, 'slot_khusus_id');
    }

    public function bidang(): BelongsTo
    {
        return $this->belongsTo(Bidang::class, 'bidang_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function tugas(): HasMany
    {
        return $this->hasMany(Tugas::class, 'penempatan_id');
    }

    public function logbooks(): HasMany
    {
        return $this->hasMany(Logbook::class, 'penempatan_id');
    }

    public function penilaian(): HasOne
    {
        return $this->hasOne(Penilaian::class, 'penempatan_id');
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status', StatusPenempatanEnum::Aktif);
    }

    /**
     * Dapatkan nomor minggu magang yang sedang berjalan saat ini.
     */
    public function getMingguAktifAttribute(): int
    {
        if (! $this->tgl_mulai) {
            return 1;
        }

        $today = now()->startOfDay();
        $mulai = $this->tgl_mulai->copy()->startOfDay();

        if ($today->lt($mulai)) {
            return 1;
        }

        $diffDays = $mulai->diffInDays($today);
        $minggu = (int) floor($diffDays / 7) + 1;

        $totalMinggu = $this->total_minggu;
        return min(max(1, $minggu), $totalMinggu);
    }

    /**
     * Dapatkan total estimasi durasi magang dalam satuan minggu.
     */
    public function getTotalMingguAttribute(): int
    {
        if (! $this->tgl_mulai || ! $this->tgl_selesai) {
            return 12; // Default 12 minggu (~3 bulan)
        }

        $mulai = $this->tgl_mulai->copy()->startOfDay();
        $selesai = $this->tgl_selesai->copy()->endOfDay();
        $diffDays = $mulai->diffInDays($selesai) + 1;

        return max(1, (int) ceil($diffDays / 7));
    }

    /**
     * Menghasilkan struktur daftar seluruh minggu magang beserta status pengisian logbooknya.
     */
    public function getDaftarMingguMagang(): array
    {
        if (! $this->tgl_mulai) {
            return [];
        }

        $total = $this->total_minggu;
        $today = now()->startOfDay();
        $mulai = $this->tgl_mulai->copy()->startOfDay();

        // Load logbooks jika belum dimuat
        $logbookMap = $this->relationLoaded('logbooks')
            ? $this->logbooks->keyBy(function ($item) {
                return $item->minggu_ke ?? ($this->tgl_mulai->diffInWeeks($item->tanggal) + 1);
            })
            : $this->logbooks()->get()->keyBy(function ($item) {
                return $item->minggu_ke ?? ($this->tgl_mulai->diffInWeeks($item->tanggal) + 1);
            });

        $hasil = [];

        for ($i = 1; $i <= $total; $i++) {
            $tglAwal = $mulai->copy()->addDays(($i - 1) * 7);
            $tglAkhir = $tglAwal->copy()->addDays(6);

            // Batasi tanggal akhir agar tidak melebihi tgl_selesai jika sudah di ujung
            if ($this->tgl_selesai && $tglAkhir->gt($this->tgl_selesai)) {
                $tglAkhir = $this->tgl_selesai->copy();
            }

            $isAktif = $today->betweenIncluded($tglAwal, $tglAkhir);
            $isLampau = $today->gt($tglAkhir);
            $isMendatang = $today->lt($tglAwal);

            $logbook = $logbookMap->get($i);

            $statusPengisian = 'belum_diisi';
            if ($logbook) {
                $statusVal = $logbook->status->value ?? (string) $logbook->status;
                $statusPengisian = $statusVal;
            } elseif ($isMendatang) {
                $statusPengisian = 'mendatang';
            }

            $hasil[] = [
                'minggu_ke'       => $i,
                'tgl_awal'        => $tglAwal,
                'tgl_akhir'       => $tglAkhir,
                'label_periode'   => $tglAwal->translatedFormat('d M') . ' – ' . $tglAkhir->translatedFormat('d M Y'),
                'is_aktif'        => $isAktif,
                'is_lampau'       => $isLampau,
                'is_mendatang'    => $isMendatang,
                'logbook'         => $logbook,
                'status'          => $statusPengisian,
            ];
        }

        return $hasil;
    }
}
