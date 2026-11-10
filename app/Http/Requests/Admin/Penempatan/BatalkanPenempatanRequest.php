<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Penempatan;

use App\Models\Penempatan;
use Illuminate\Foundation\Http\FormRequest;

class BatalkanPenempatanRequest extends FormRequest
{
    /**
     * Tentukan otorisasi permintaan: hanya Admin berhak membatalkan penempatan peserta (SEC-12).
     */
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('manage', Penempatan::class);
    }

    /**
     * Aturan validasi pembatalan / pengunduran diri peserta.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'alasan' => ['required', 'string', 'min:10', 'max:1000'],
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
            'alasan.required' => 'Alasan pembatalan atau pengunduran diri wajib diisi.',
            'alasan.min' => 'Alasan pembatalan/pengunduran diri minimal 10 karakter.',
            'alasan.max' => 'Alasan pembatalan/pengunduran diri maksimal 1000 karakter.',
        ];
    }
}
