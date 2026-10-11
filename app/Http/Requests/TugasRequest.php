<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\KompleksitasEnum;
use App\Enums\StatusTugasEnum;
use App\Models\Penempatan;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Validator;

class TugasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            'penempatan_id' => [$isUpdate ? 'sometimes' : 'required', 'exists:penempatan,id'],
            'judul' => ['required', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string'],
            'kategori' => ['required', 'string', 'max:50'],
            'kompleksitas' => ['required', new Enum(KompleksitasEnum::class)],
            'deadline' => ['required', 'date', $isUpdate ? 'nullable' : 'after_or_equal:today'],
            'status' => ['nullable', new Enum(StatusTugasEnum::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'deadline.after_or_equal' => 'Batas waktu (deadline) tugas tidak boleh di masa lampau (AC-08-1).',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $penempatanId = $this->input('penempatan_id') ?? $this->route('tugas')?->penempatan_id;
            $deadlineInput = $this->input('deadline');

            if ($penempatanId && $deadlineInput) {
                $penempatan = Penempatan::find($penempatanId);
                if ($penempatan && $penempatan->tgl_selesai) {
                    $deadline = Carbon::parse($deadlineInput)->startOfDay();
                    $tglSelesai = Carbon::parse($penempatan->tgl_selesai)->endOfDay();
                    if ($deadline->gt($tglSelesai)) {
                        $validator->errors()->add(
                            'deadline',
                            'Deadline tugas tidak boleh melebihi batas akhir magang peserta (' . $penempatan->tgl_selesai->format('d/m/Y') . ').'
                        );
                    }
                }
            }
        });
    }
}
