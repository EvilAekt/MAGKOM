<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Logbook;
use App\Models\Pengaturan;
use App\Rules\SafeExternalUrlRule;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class LogbookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('link_laporan') && is_string($this->input('link_laporan'))) {
            $this->merge([
                'link_laporan' => trim($this->input('link_laporan')),
            ]);
        }
    }

    public function rules(): array
    {
        $maksMundurHari = Pengaturan::getInt('logbook_maks_mundur_hari', 3);
        $minDate = Carbon::today()->subDays($maksMundurHari)->toDateString();
        $today = Carbon::today()->toDateString();
        $maksBerkasMb = Pengaturan::getInt('berkas_maks_mb', 2);
        $maksBerkasKb = $maksBerkasMb * 1024;

        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        $logbook = $this->route('logbook');

        $tanggalRules = [
            'required',
            'date',
            "before_or_equal:{$today}",
        ];

        // Pada mode update jika tanggal sama dengan logbook lama, jangan kenakan batasan mundur hari ini
        $isExistingUnchangedDate = $isUpdate
            && $logbook instanceof Logbook
            && $logbook->tanggal
            && $this->input('tanggal') === $logbook->tanggal->toDateString();

        if (! $isExistingUnchangedDate) {
            $tanggalRules[] = "after_or_equal:{$minDate}";
        }

        return [
            'minggu_ke' => ['nullable', 'integer', 'min:1'],
            'tanggal' => $tanggalRules,
            'kegiatan' => ['required', 'string', 'min:10', 'max:5000'],
            'file_laporan' => ['nullable', 'file', 'mimes:pdf', "max:{$maksBerkasKb}"],
            'link_laporan' => ['nullable', 'string', 'max:2048', new SafeExternalUrlRule],
        ];
    }

    public function messages(): array
    {
        $maksBerkasMb = Pengaturan::getInt('berkas_maks_mb', 2);

        return [
            'tanggal.before_or_equal' => 'Tanggal logbook tidak boleh melebihi hari ini.',
            'tanggal.after_or_equal' => 'Tanggal kegiatan sudah melebihi batas toleransi pengisian logbook mundur (BR-09).',
            'file_laporan.mimes' => 'Berkas lampiran logbook harus berformat PDF.',
            'file_laporan.max' => "Ukuran berkas lampiran tidak boleh melebihi batas maksimal ({$maksBerkasMb} MB).",
            'link_laporan.regex' => 'Tautan lampiran harus menggunakan protokol aman HTTPS (https://).',
        ];
    }
}
