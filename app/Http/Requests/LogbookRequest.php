<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\StatusPenempatanEnum;
use App\Models\Logbook;
use App\Models\Penempatan;
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

        // Cari penempatan aktif pengguna
        $user = $this->user();
        /** @var Penempatan|null $penempatan */
        $penempatan = $logbook instanceof Logbook && $logbook->penempatan
            ? $logbook->penempatan
            : ($user ? Penempatan::where('peserta_id', $user->id)->where('status', StatusPenempatanEnum::Aktif)->first() : null);

        $hasMingguKe = $this->filled('minggu_ke');
        $mingguAktif = $penempatan ? $penempatan->minggu_aktif : null;

        $mingguRules = ['nullable', 'integer', 'min:1'];
        if ($mingguAktif !== null) {
            // Minggu yang diisi tidak boleh melebihi minggu yang sedang aktif/berjalan saat ini
            $mingguRules[] = "max:{$mingguAktif}";
        }

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
            if ($hasMingguKe && $penempatan && $penempatan->tgl_mulai) {
                // Saat minggu_ke ditentukan, peserta dapat mengisi logbook minggu lampau (sejak tgl_mulai magang)
                $tglMulaiMagang = $penempatan->tgl_mulai->toDateString();
                $tanggalRules[] = "after_or_equal:{$tglMulaiMagang}";
            } else {
                // Fallback untuk mode tanpa minggu_ke: tetap berlaku batas toleransi mundur harian BR-09
                $tanggalRules[] = "after_or_equal:{$minDate}";
            }
        }

        return [
            'minggu_ke' => $mingguRules,
            'tanggal' => $tanggalRules,
            'kegiatan' => ['required', 'string', 'min:10', 'max:5000'],
            'file_laporan' => ['nullable', 'file', 'mimes:pdf', "max:{$maksBerkasKb}"],
            'link_laporan' => ['nullable', 'string', 'max:2048', new SafeExternalUrlRule],
        ];
    }

    public function messages(): array
    {
        $maksBerkasMb = Pengaturan::getInt('berkas_maks_mb', 2);
        $inputMinggu = $this->input('minggu_ke');

        return [
            'minggu_ke.max' => "Logbook untuk Minggu ke-{$inputMinggu} belum dapat diisi karena periode minggu tersebut belum berjalan.",
            'minggu_ke.min' => 'Nomor minggu magang tidak valid.',
            'tanggal.before_or_equal' => 'Tanggal logbook tidak boleh melebihi hari ini.',
            'tanggal.after_or_equal' => $this->filled('minggu_ke')
                ? 'Tanggal logbook tidak boleh mendahului tanggal mulai magang.'
                : 'Tanggal kegiatan sudah melebihi batas toleransi pengisian logbook mundur (BR-09).',
            'file_laporan.mimes' => 'Berkas lampiran logbook harus berformat PDF.',
            'file_laporan.max' => "Ukuran berkas lampiran tidak boleh melebihi batas maksimal ({$maksBerkasMb} MB).",
            'link_laporan.regex' => 'Tautan lampiran harus menggunakan protokol aman HTTPS (https://).',
        ];
    }
}
