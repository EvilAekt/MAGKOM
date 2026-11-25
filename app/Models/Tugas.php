<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\KompleksitasEnum;
use App\Enums\StatusTugasEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tugas extends Model
{
    use HasFactory;

    protected $table = 'tugas';

    protected $fillable = [
        'batch_id',
        'penempatan_id',
        'tipe',
        'kelompok_magang_id',
        'judul',
        'deskripsi',
        'link_referensi',
        'file_lampiran',
        'nama_file_lampiran',
        'ukuran_file_lampiran',
        'kategori',
        'kompleksitas',
        'deadline',
        'status',
        'status_publikasi',
        'jadwal_publikasi',
    ];

    protected $casts = [
        'kompleksitas' => KompleksitasEnum::class,
        'status' => StatusTugasEnum::class,
        'deadline' => 'date',
        'jadwal_publikasi' => 'datetime',
        'ukuran_file_lampiran' => 'integer',
    ];

    protected $appends = [
        'terlambat',
        'ekstensi_lampiran',
        'ukuran_format',
        'links',
    ];

    /**
     * Aksesor runtime untuk mendapatkan daftar tautan referensi acuan tugas.
     * Mendukung data warisan (URL tunggal), JSON array string, atau pemisah baris baru.
     *
     * @return array<int, string>
     */
    public function getLinksAttribute(): array
    {
        if (blank($this->link_referensi)) {
            return [];
        }

        $raw = trim((string) $this->link_referensi);
        if ($raw === '') {
            return [];
        }

        if (str_starts_with($raw, '[') && str_ends_with($raw, ']')) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                return array_values(array_filter($decoded, fn ($item) => is_string($item) && trim($item) !== ''));
            }
        }

        if (str_contains($raw, "\n")) {
            return array_values(array_filter(array_map('trim', explode("\n", $raw))));
        }

        return [$raw];
    }

    /**
     * BR-16: Aksesor runtime untuk tugas terlambat.
     * Tugas dianggap terlambat jika deadline < hari ini dan status belum selesai.
     * Tidak disimpan di basis data.
     */
    public function getTerlambatAttribute(): bool
    {
        if ($this->status === StatusTugasEnum::Selesai) {
            return false;
        }

        if (! $this->deadline) {
            return false;
        }

        return $this->deadline->lt(Carbon::today());
    }

    /**
     * Scope untuk memfilter tugas yang terlambat secara query SQL.
     */
    public function scopeTerlambat(Builder $query): Builder
    {
        return $query->where('status', '!=', StatusTugasEnum::Selesai->value)
            ->whereDate('deadline', '<', Carbon::today());
    }

    /**
     * Scope untuk tugas yang sudah aktif dipublikasikan ke peserta.
     */
    public function scopeDipublikasikan(Builder $query): Builder
    {
        return $query->where('status_publikasi', 'dipublikasikan');
    }

    /**
     * Scope untuk tugas terjadwal yang menunggu waktu publikasi.
     */
    public function scopeTerjadwal(Builder $query): Builder
    {
        return $query->where('status_publikasi', 'terjadwal');
    }

    /**
     * Scope untuk tugas yang masih berstatus draft.
     */
    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status_publikasi', 'draft');
    }

    public function isDipublikasikan(): bool
    {
        return $this->status_publikasi === 'dipublikasikan';
    }

    public function isTerjadwal(): bool
    {
        return $this->status_publikasi === 'terjadwal';
    }

    public function isDraft(): bool
    {
        return $this->status_publikasi === 'draft';
    }

    public function isKelompok(): bool
    {
        return $this->tipe === 'kelompok';
    }

    public function isIndividu(): bool
    {
        return $this->tipe !== 'kelompok';
    }

    public function penempatan(): BelongsTo
    {
        return $this->belongsTo(Penempatan::class, 'penempatan_id');
    }

    /**
     * Relasi ke kelompok magang jika tugas ini bertipe kelompok / project bersama.
     */
    public function kelompokMagang(): BelongsTo
    {
        return $this->belongsTo(KelompokMagang::class, 'kelompok_magang_id');
    }

    /**
     * Relasi ke record pivot penugasan spesifik peserta (TugasPeserta).
     *
     * @return HasMany<TugasPeserta>
     */
    public function tugasPeserta(): HasMany
    {
        return $this->hasMany(TugasPeserta::class, 'tugas_id');
    }

    /**
     * Relasi many-to-many ke peserta (User) yang ditugaskan dalam project bersama ini.
     *
     * @return BelongsToMany<User>
     */
    public function pesertaDitugaskan(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tugas_peserta', 'tugas_id', 'user_id')
            ->withTimestamps();
    }

    public function progresLogs(): HasMany
    {
        return $this->hasMany(ProgresLog::class, 'tugas_id')->latest();
    }

    /**
     * Mendapatkan ekstensi file lampiran (huruf kecil).
     */
    public function getEkstensiLampiranAttribute(): ?string
    {
        if (! $this->nama_file_lampiran && ! $this->file_lampiran) {
            return null;
        }

        $nama = $this->nama_file_lampiran ?: $this->file_lampiran;

        return strtolower(pathinfo($nama, PATHINFO_EXTENSION));
    }

    /**
     * Format ukuran file lampiran menjadi KB atau MB yang rapi.
     */
    public function getUkuranFormatAttribute(): ?string
    {
        if (! $this->ukuran_file_lampiran) {
            return null;
        }

        $bytes = (int) $this->ukuran_file_lampiran;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1).' MB';
        }

        return max(1, round($bytes / 1024)).' KB';
    }

    /**
     * Skema warna badge dan ikon berdasarkan tipe file dokumen.
     */
    public function getBadgeColorLampiranAttribute(): array
    {
        $ext = $this->ekstensi_lampiran;

        return match ($ext) {
            'pdf' => [
                'bg' => '#fee2e2',
                'color' => '#dc2626',
                'border' => '#fca5a5',
                'label' => 'PDF',
            ],
            'doc', 'docx' => [
                'bg' => '#dbeafe',
                'color' => '#2563eb',
                'border' => '#93c5fd',
                'label' => 'DOCX',
            ],
            'xls', 'xlsx', 'csv' => [
                'bg' => '#d1fae5',
                'color' => '#059669',
                'border' => '#6ee7b7',
                'label' => 'EXCEL',
            ],
            'ppt', 'pptx' => [
                'bg' => '#ffedd5',
                'color' => '#ea580c',
                'border' => '#fdba74',
                'label' => 'PPT',
            ],
            'zip', 'rar', '7z' => [
                'bg' => '#f3e8ff',
                'color' => '#7e22ce',
                'border' => '#d8b4fe',
                'label' => 'ZIP',
            ],
            'png', 'jpg', 'jpeg' => [
                'bg' => '#ecfdf5',
                'color' => '#047857',
                'border' => '#a7f3d0',
                'label' => 'IMG',
            ],
            default => [
                'bg' => '#f1f5f9',
                'color' => '#475569',
                'border' => '#cbd5e1',
                'label' => strtoupper($ext ?: 'FILE'),
            ],
        };
    }
}
