<?php

declare(strict_types=1);

namespace App\Http\Requests\SuperAdmin;

use App\Enums\RoleEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StorePermissionRequest extends FormRequest
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
        if (empty($this->input('slug')) && ! empty($this->input('nama_modul'))) {
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
        return [
            'nama_modul' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z0-9._-]+$/', 'unique:permissions,slug'],
            'kategori' => ['required', 'string', 'max:50'],
            'deskripsi' => ['nullable', 'string', 'max:500'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', 'in:superadmin,admin,pembimbing,peserta'],
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
            'roles' => 'Daftar Peran',
        ];
    }
}
