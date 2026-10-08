<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Pengaturan;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class LogbookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $maksMundurHari = Pengaturan::getInt('logbook_maks_mundur_hari', 3);
        $minDate = Carbon::today()->subDays($maksMundurHari)->toDateString();
        $today = Carbon::today()->toDateString();
        $maksBerkasKb = Pengaturan::getInt('berkas_maks_mb', 2) * 1024;

        return [
            'tanggal' => [
                'required',
                'date',
                "before_or_equal:{$today}",
                "after_or_equal:{$minDate}",
            ],
            'kegiatan' => ['required', 'string', 'min:10', 'max:5000'],
            'file_laporan' => ['nullable', 'file', 'mimes:pdf', "max:{$maksBerkasKb}"],
            'link_laporan' => [
                'nullable',
                'url',
                'max:500',
                'regex:/^https:\/\//i',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal.before_or_equal' => 'Tanggal logbook tidak boleh melebihi hari ini.',
            'tanggal.after_or_equal' => 'Tanggal kegiatan sudah melebihi batas toleransi pengisian logbook mundur (BR-09).',
            'file_laporan.mimes' => 'Berkas lampiran logbook harus berformat PDF.',
            'file_laporan.max' => 'Ukuran berkas lampiran tidak boleh melebihi batas maksimal (2 MB).',
            'link_laporan.regex' => 'Tautan lampiran harus menggunakan protokol aman HTTPS (https://).',
        ];
    }
}
