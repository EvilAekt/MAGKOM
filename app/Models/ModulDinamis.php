<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModulDinamis extends Model
{
    use HasFactory;

    protected $table = 'modul_dinamis';

    public const STANDARD_ACTIONS = [
        'list'       => 'Daftar Data (Index)',
        'show'       => 'Detail Data (View)',
        'create'     => 'Tambah Data (Create)',
        'edit'       => 'Ubah Data (Update)',
        'delete'     => 'Hapus Data (Destroy)',
        'export_csv' => 'Ekspor Data CSV',
        'export_pdf' => 'Ekspor Dokumen PDF',
        'print'      => 'Cetak Pratinjau (Print)',
    ];

    protected $fillable = [
        'nama_modul',
        'slug',
        'tipe_sumber',
        'nama_tabel_fisik',
        'primary_key',
        'is_manual_pk',
        'deskripsi',
        'ikon',
        'target_roles',
        'izin_aksi',
        'log_aksi',
        'status_aktif',
        'is_public_read',
        'scope_mode',
        'urutan',
        'dibuat_oleh',
    ];

    protected $casts = [
        'target_roles'   => 'array',
        'izin_aksi'      => 'array',
        'log_aksi'       => 'array',
        'status_aktif'   => 'boolean',
        'is_manual_pk'   => 'boolean',
        'is_public_read' => 'boolean',
        'urutan'         => 'integer',
    ];

    public function fields(): HasMany
    {
        return $this->hasMany(ModulDinamisField::class, 'modul_id')->orderBy('urutan');
    }

    public function records(): HasMany
    {
        return $this->hasMany(ModulDinamisData::class, 'modul_id')->latest();
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    /**
     * Memeriksa apakah role pengguna memiliki izin untuk aksi spesifik.
     */
    public function canRolePerformAction(string $role, string $action): bool
    {
        $roleLower = strtolower($role);

        // SuperAdmin selalu diizinkan melakukan seluruh aksi
        if ($roleLower === 'superadmin') {
            return true;
        }

        // Cek izin aksi granular jika didefinisikan
        if (!empty($this->izin_aksi) && isset($this->izin_aksi[$action])) {
            $allowedRoles = array_map('strtolower', (array) $this->izin_aksi[$action]);
            return in_array($roleLower, $allowedRoles, true);
        }

        // Fallback: gunakan konfigurasi makro target_roles untuk read/list/show, dan batasi mutasi ke admin
        $targetRoles = array_map('strtolower', (array) ($this->target_roles ?? []));
        if (!in_array($roleLower, $targetRoles, true)) {
            return false;
        }

        if (in_array($action, ['create', 'edit', 'delete'], true)) {
            return in_array($roleLower, ['superadmin', 'admin'], true);
        }

        return true;
    }

    /**
     * Memeriksa apakah suatu aksi wajib dicatat ke dalam audit log.
     */
    public function shouldLogAction(string $action): bool
    {
        if (empty($this->log_aksi)) {
            // Default: catat seluruh mutasi data dan export dokumen
            return in_array($action, ['create', 'edit', 'delete', 'export_csv', 'export_pdf'], true);
        }

        return !empty($this->log_aksi[$action]);
    }
}
