<?php

declare(strict_types=1);

namespace App\Http\Requests\SuperAdmin;

use App\Enums\RoleEnum;
use App\Models\Permission;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdatePermissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role === RoleEnum::SuperAdmin;
    }

    /**
     * Prepare inputs before validation.
     */
    protected function prepareForValidation(): void
    {
        /** @var Permission|null $permission */
        $permission = $this->route('permission');

        if ($permission && ! $permission->is_system && empty($this->input('slug')) && ! empty($this->input('nama_modul'))) {
            $this->merge([
                'slug' => Str::slug((string) $this->input('nama_modul')),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Permission|null $permission */
        $permission = $this->route('permission');
        $permissionId = $permission?->id ?? 'NULL';

        return [
            'nama_modul' => ['required', 'string', 'max:150'],
            'slug' => ($permission && $permission->is_system)
                ? ['nullable', 'string', 'max:100']
                : ['required', 'string', 'max:100', 'regex:/^[a-zA-Z0-9._-]+$/', 'unique:permissions,slug,'.$permissionId],
            'kategori' => ['required', 'string', 'max:50'],
            'deskripsi' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Custom attribute names.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nama_modul' => 'Nama Modul / Hak Akses',
            'slug' => 'Kode Unik (Slug)',
            'kategori' => 'Kategori Modul',
            'deskripsi' => 'Deskripsi Cakupan',
        ];
    }
}
