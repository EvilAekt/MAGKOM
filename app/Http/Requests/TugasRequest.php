<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\KompleksitasEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusTugasEnum;
use App\Models\Penempatan;
use App\Rules\SafeExternalUrlRule;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Validator;

class TugasRequest extends FormRequest
{
    public function authorize(): bool
    {
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $tugas = $this->route('tugas');
            if ($tugas) {
                return $this->user()?->can('update', $tugas) ?? false;
            }
        }

        return true;
    }

    protected function prepareForValidation(): void
    {
        // Kompatibilitas mundur: jika dikirim penempatan_id tunggal, bungkus ke penempatan_ids
        if (! $this->has('penempatan_ids') && $this->filled('penempatan_id')) {
            $this->merge([
                'penempatan_ids' => [$this->integer('penempatan_id')],
            ]);
        } elseif ($this->has('penempatan_ids') && is_array($this->input('penempatan_ids')) && count($this->input('penempatan_ids')) > 0) {
            // Pasang penempatan_id pertama untuk kompatibilitas
            $this->merge([
                'penempatan_id' => (int) $this->input('penempatan_ids')[0],
            ]);
        }

        // Default kompleksitas jika form UI tidak mengirimkannya
        if (! $this->has('kompleksitas') || empty($this->input('kompleksitas'))) {
            $this->merge([
                'kompleksitas' => 'sedang',
            ]);
        }

        if ($this->has('link_referensi') && is_string($this->input('link_referensi'))) {
            $this->merge([
                'link_referensi' => trim($this->input('link_referensi')),
            ]);
        }
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            'penempatan_ids' => [$isUpdate ? 'nullable' : 'required', 'array', 'min:1'],
            'penempatan_ids.*' => ['integer', 'distinct', 'exists:penempatan,id'],
            'penempatan_id' => [$isUpdate ? 'sometimes' : 'nullable', 'exists:penempatan,id'],
            'judul' => ['required', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string'],
            'link_referensi' => ['nullable', 'string', 'max:2048', new SafeExternalUrlRule],
            'file_lampiran' => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,csv,ppt,pptx,zip,png,jpg,jpeg', 'max:10240'],
            'kategori' => ['required', 'string', 'max:50'],
            'kompleksitas' => ['required', new Enum(KompleksitasEnum::class)],
            'deadline' => ['required', 'date', $isUpdate ? 'nullable' : 'after_or_equal:today'],
            'status' => ['nullable', new Enum(StatusTugasEnum::class)],
            'aksi_publikasi' => ['nullable', 'in:sekarang,terjadwal,draft'],
            'tgl_publikasi' => ['nullable', 'required_if:aksi_publikasi,terjadwal', 'date'],
            'waktu_publikasi' => ['nullable', 'required_if:aksi_publikasi,terjadwal'],
        ];
    }

    public function messages(): array
    {
        return [
            'penempatan_ids.required' => 'Minimal satu peserta magang harus dipilih.',
            'penempatan_ids.min' => 'Minimal satu peserta magang harus dipilih.',
            'penempatan_ids.*.distinct' => 'Terdapat duplikasi peserta magang yang dipilih.',
            'penempatan_ids.*.exists' => 'Satu atau lebih peserta bimbingan tidak ditemukan.',
            'judul.required' => 'Judul penugasan wajib diisi.',
            'deskripsi.required' => 'Petunjuk & instruksi pengerjaan wajib diisi.',
            'deadline.required' => 'Batas waktu (deadline) tugas wajib ditentukan.',
            'deadline.after_or_equal' => 'Batas waktu (deadline) tugas tidak boleh di masa lampau (AC-08-1).',
            'link_referensi.url' => 'Format tautan referensi tidak valid. Harap gunakan URL lengkap diawali http:// atau https://.',
            'file_lampiran.mimes' => 'Format berkas lampiran tidak didukung. Harap unggah berkas berformat PDF, Word, Excel, PPT, ZIP, atau Gambar.',
            'file_lampiran.max' => 'Ukuran berkas lampiran tidak boleh melebihi 10 MB.',
            'tgl_publikasi.required_if' => 'Tanggal publikasi wajib diisi untuk penugasan terjadwal.',
            'waktu_publikasi.required_if' => 'Waktu publikasi wajib diisi untuk penugasan terjadwal.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $user = $this->user();
            $penempatanIds = $this->input('penempatan_ids') ?? [];
            if (! is_array($penempatanIds)) {
                $penempatanIds = array_filter([(int) $penempatanIds]);
            }

            // Fallback ke single penempatan jika update
            if (empty($penempatanIds) && $this->input('penempatan_id')) {
                $penempatanIds = [(int) $this->input('penempatan_id')];
            }

            $deadlineInput = $this->input('deadline');

            if (! empty($penempatanIds)) {
                $uniqueIds = array_values(array_unique(array_filter(array_map('intval', $penempatanIds))));
                $penempatans = Penempatan::with('peserta')->whereIn('id', $uniqueIds)->get();

                // Pastikan seluruh penempatan yang diminta benar-benar ditemukan di database
                if ($penempatans->count() !== count($uniqueIds)) {
                    $validator->errors()->add(
                        'penempatan_ids',
                        'Satu atau lebih peserta bimbingan yang dipilih tidak ditemukan dalam sistem.'
                    );
                }

                // 1. Validasi Kepemilikan & Kewenangan Pembimbing (Anti-IDOR) serta status aktif
                foreach ($penempatans as $p) {
                    if ($user && ! $user->isAdmin() && ! $user->isSuperAdmin() && $p->pembimbing_id !== $user->id) {
                        $validator->errors()->add(
                            'penempatan_ids',
                            'Anda tidak memiliki kewenangan untuk menugaskan peserta di luar bimbingan Anda.'
                        );
                        break;
                    }

                    if (! $this->isMethod('PUT') && ! $this->isMethod('PATCH') && $p->status !== StatusPenempatanEnum::Aktif) {
                        $menteeNama = $p->peserta?->nama ?? 'Peserta';
                        $validator->errors()->add(
                            'penempatan_ids',
                            "Peserta {$menteeNama} saat ini tidak berstatus aktif dalam bimbingan magang."
                        );
                        break;
                    }
                }

                // 2. Validasi Deadline tidak melewati batas selesai magang mentee
                if ($deadlineInput) {
                    $deadline = Carbon::parse($deadlineInput)->startOfDay();

                    foreach ($penempatans as $penempatan) {
                        if ($penempatan->tgl_selesai) {
                            $tglSelesai = Carbon::parse($penempatan->tgl_selesai)->endOfDay();
                            if ($deadline->gt($tglSelesai)) {
                                $menteeNama = $penempatan->peserta?->nama ?? 'Peserta';
                                $validator->errors()->add(
                                    'deadline',
                                    "Deadline tugas tidak boleh melebihi batas akhir magang {$menteeNama} ({$penempatan->tgl_selesai->format('d/m/Y')})."
                                );
                                break;
                            }
                        }
                    }
                }
            }

            // 3. Validasi Penjadwalan Publikasi
            if ($this->input('aksi_publikasi') === 'terjadwal') {
                $tglPub = $this->input('tgl_publikasi');
                $wktPub = $this->input('waktu_publikasi') ?: '08:00';

                if ($tglPub) {
                    $jadwalPublikasi = Carbon::parse("{$tglPub} {$wktPub}", 'Asia/Jakarta');
                    $sekarang = now('Asia/Jakarta');

                    if ($jadwalPublikasi->lte($sekarang)) {
                        $validator->errors()->add('tgl_publikasi', 'Waktu publikasi terjadwal harus berada di masa mendatang.');
                    }

                    if ($deadlineInput) {
                        $deadline = Carbon::parse($deadlineInput, 'Asia/Jakarta')->endOfDay();
                        if ($jadwalPublikasi->gte($deadline)) {
                            $validator->errors()->add('tgl_publikasi', 'Waktu publikasi terjadwal harus sebelum batas waktu (deadline) tugas.');
                        }
                    }
                }
            }
        });
    }
}
