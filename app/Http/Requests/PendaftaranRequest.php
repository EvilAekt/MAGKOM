<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\StatusKetersediaanEnum;
use App\Models\BulanMagang;
use App\Models\Kuota;
use App\Services\BulanStatusService;
use App\Services\QuotaAllocationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class PendaftaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isPeserta() ?? false;
    }

    public function rules(): array
    {
        return [
            'bulan_magang_id' => ['required', 'exists:bulan_magang,id'],
            'kuota_id'        => ['required', 'exists:kuota,id'],
            'durasi_bulan'    => ['required', 'integer', 'min:1', 'max:6'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $user = $this->user();
            if (! $user) {
                return;
            }

            // 1. Validasi aturan BR-17: hanya 1 pendaftaran aktif per peserta
            $quotaService = app(QuotaAllocationService::class);
            try {
                $quotaService->pastikanTidakAdaPendaftaranAktif($user->id);
            } catch (\Exception $e) {
                $validator->errors()->add(
                    'pendaftaran',
                    'Anda masih memiliki pendaftaran magang yang aktif. Berdasarkan ketentuan, peserta hanya boleh memiliki 1 pendaftaran aktif pada satu waktu.'
                );
            }

            // 2. Validasi status bulan magang (harus status Tersedia)
            $bulan = BulanMagang::find($this->input('bulan_magang_id'));
            $kuota = Kuota::find($this->input('kuota_id'));

            if ($bulan) {
                $statusService = app(BulanStatusService::class);
                $status = $statusService->status($bulan, $kuota);

                if ($status !== StatusKetersediaanEnum::Tersedia) {
                    $validator->errors()->add(
                        'bulan_magang_id',
                        'Periode bulan magang ini saat ini tidak dibuka untuk pendaftaran baru.'
                    );
                }
            }

            // 3. Pastikan kuota yang dipilih sesuai dengan bulan magang
            if ($bulan && $kuota && $kuota->bulan_magang_id !== $bulan->id) {
                $validator->errors()->add('kuota_id', 'Alokasi kuota tidak sesuai dengan periode bulan magang yang dipilih.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'bulan_magang_id.required' => 'Periode bulan magang wajib dipilih.',
            'kuota_id.required' => 'Jurusan kuota magang wajib dipilih.',
            'durasi_bulan.required' => 'Durasi magang wajib ditentukan.',
            'durasi_bulan.min' => 'Durasi magang minimal 1 bulan.',
            'durasi_bulan.max' => 'Durasi magang maksimal 6 bulan.',
        ];
    }
}
