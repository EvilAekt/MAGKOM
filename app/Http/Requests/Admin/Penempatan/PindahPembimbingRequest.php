<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Penempatan;

use App\Enums\RoleEnum;
use App\Models\Penempatan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PindahPembimbingRequest extends FormRequest
{
    /**
     * Tentukan otorisasi permintaan: hanya Admin berhak mengelola pemindahan pembimbing (SEC-12).
     */
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('manage', Penempatan::class);
    }

    /**
     * Aturan validasi pemindahan pembimbing.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'pembimbing_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role', RoleEnum::Pembimbing->value)
                        ->where('status_aktif', true);
                }),
            ],
        ];
    }

    /**
     * Pesan validasi khusus.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'pembimbing_id.required' => 'Pembimbing baru tujuan wajib dipilih.',
            'pembimbing_id.exists' => 'Pembimbing baru yang dipilih tidak valid atau sedang tidak aktif.',
        ];
    }
}
