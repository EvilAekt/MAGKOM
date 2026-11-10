<?php

declare(strict_types=1);

namespace App\Http\Requests\Pembimbing;

use Illuminate\Foundation\Http\FormRequest;

class LaporanKinerjaFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:100'],
            'penempatan_id' => ['nullable', 'integer'],
            'status' => ['nullable', 'string', 'max:50'],
            'bidang_id' => ['nullable', 'integer'],
            'bulan' => ['nullable', 'string', 'max:20'],
            'tahun' => ['nullable', 'integer', 'min:2020', 'max:2099'],
            'per_page' => ['nullable', 'integer', 'in:5,10,25'],
            'format' => ['nullable', 'string', 'in:xlsx,csv,pdf'],
        ];
    }
}
