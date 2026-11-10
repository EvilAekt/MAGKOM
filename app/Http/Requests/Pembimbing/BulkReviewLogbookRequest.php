<?php

declare(strict_types=1);

namespace App\Http\Requests\Pembimbing;

use App\Models\Logbook;
use Illuminate\Foundation\Http\FormRequest;

class BulkReviewLogbookRequest extends FormRequest
{
    /**
     * Tentukan otorisasi permintaan bulk review logbook.
     * SuperAdmin strictly read-only (SEC-12), pembimbing dibatasi hanya mentee bimbingannya (Anti-IDOR).
     */
    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user) {
            return false;
        }

        // SuperAdmin dilarang keras melakukan perubahan status/review operasional
        if ($user->isSuperAdmin()) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if (! $user->isPembimbing()) {
            return false;
        }

        $ids = (array) $this->input('logbook_ids', []);
        if (! empty($ids)) {
            $hasUnauthorized = Logbook::whereIn('id', $ids)
                ->whereDoesntHave('penempatan', fn ($q) => $q->where('pembimbing_id', $user->id))
                ->exists();

            if ($hasUnauthorized) {
                return false;
            }
        }

        return true;
    }

    /**
     * Aturan validasi persetujuan massal logbook.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'logbook_ids' => ['required', 'array', 'min:1'],
            'logbook_ids.*' => ['required', 'integer', 'distinct', 'exists:logbook,id'],
            'catatan_pembimbing' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Pesan validasi bahasa Indonesia yang jelas dan informatif.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'logbook_ids.required' => 'Minimal satu entri logbook harus dipilih untuk disetujui.',
            'logbook_ids.min' => 'Pilih minimal satu entri logbook.',
            'logbook_ids.*.distinct' => 'Terdapat duplikasi ID logbook pada permintaan.',
            'logbook_ids.*.exists' => 'Satu atau lebih entri logbook yang dipilih tidak ditemukan dalam sistem.',
            'catatan_pembimbing.max' => 'Catatan pembimbing maksimal berisi 1000 karakter.',
        ];
    }
}
