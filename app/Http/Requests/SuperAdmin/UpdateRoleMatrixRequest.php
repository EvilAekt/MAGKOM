<?php

declare(strict_types=1);

namespace App\Http\Requests\SuperAdmin;

use App\Enums\RoleEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleMatrixRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role === RoleEnum::SuperAdmin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'matrix' => ['nullable', 'array'],
            'matrix.*' => ['nullable', 'array'],
            'matrix.*.*' => ['nullable', 'string', 'in:superadmin,admin,pembimbing,peserta,1,on'],
            'page_permission_ids' => ['nullable', 'array'],
            'page_permission_ids.*' => ['integer', 'exists:permissions,id'],
        ];
    }
}
