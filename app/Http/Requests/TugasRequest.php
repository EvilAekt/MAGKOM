<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\KompleksitasEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusTugasEnum;
use App\Models\KelompokMagang;
use App\Models\Penempatan;
use App\Rules\SafeExternalUrlRule;
use App\Support\SafeHtmlSanitizer;
use Carbon\Carbon;
use Closure;
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
        if (! $this->has('tipe') || ! in_array($this->input('tipe'), ['individu', 'kelompok'], true)) {
            $this->merge([
                'tipe' => 'individu',
            ]);
        }

        if (! $this->has('kelompok_magang_ids') && $this->filled('kelompok_id')) {
            $this->merge([
                'kelompok_magang_ids' => [$this->integer('kelompok_id')],
            ]);
        }

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

        if ($this->has('link_referensi')) {
            $inputLink = $this->input('link_referensi');
            if (is_string($inputLink)) {
                $raw = trim($inputLink);
                if (str_starts_with($raw, '[') && str_ends_with($raw, ']')) {
                    $decoded = json_decode($raw, true);
                    if (is_array($decoded)) {
                        $normalized = [];
                        foreach ($decoded as $item) {
                            if (is_string($item)) {
                                $u = trim($item);
                                if ($u !== '' && str_contains($u, '.') && ! str_starts_with(strtolower($u), 'http://') && ! str_starts_with(strtolower($u), 'https://')) {
                                    $u = 'https://'.$u;
                                }
                                if ($u !== '') {
                                    $normalized[] = $u;
                                }
                            }
                        }
                        $this->merge([
                            'link_referensi' => empty($normalized) ? null : (count($normalized) === 1 ? $normalized[0] : json_encode($normalized, JSON_UNESCAPED_SLASHES)),
                        ]);
                    }
                } else {
                    $link = $raw;
                    if ($link !== '' && str_contains($link, '.') && ! str_starts_with(strtolower($link), 'http://') && ! str_starts_with(strtolower($link), 'https://')) {
                        $link = 'https://'.$link;
                    }
                    $this->merge([
                        'link_referensi' => $link === '' ? null : $link,
                    ]);
                }
            } elseif (is_array($inputLink)) {
                $normalized = [];
                foreach ($inputLink as $item) {
                    if (is_string($item)) {
                        $u = trim($item);
                        if ($u !== '' && str_contains($u, '.') && ! str_starts_with(strtolower($u), 'http://') && ! str_starts_with(strtolower($u), 'https://')) {
                            $u = 'https://'.$u;
                        }
                        if ($u !== '') {
                            $normalized[] = $u;
                        }
                    }
                }
                $this->merge([
                    'link_referensi' => empty($normalized) ? null : (count($normalized) === 1 ? $normalized[0] : json_encode($normalized, JSON_UNESCAPED_SLASHES)),
                ]);
            }
        }

        if ($this->has('deskripsi') && is_string($this->input('deskripsi'))) {
            $this->merge([
                'deskripsi' => SafeHtmlSanitizer::sanitize($this->input('deskripsi')),
            ]);
        }
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        $isKelompok = $this->input('tipe', 'individu') === 'kelompok';

        return [
            'tipe' => ['nullable', 'in:individu,kelompok'],
            'penempatan_ids' => [(! $isUpdate && ! $isKelompok) ? 'required' : 'nullable', 'array', 'min:1'],
            'penempatan_ids.*' => ['integer', 'distinct', 'exists:penempatan,id'],
            'kelompok_magang_ids' => [(! $isUpdate && $isKelompok) ? 'required' : 'nullable', 'array', 'min:1'],
            'kelompok_magang_ids.*' => ['integer', 'distinct', 'exists:kelompok_magang,id'],
            'penempatan_id' => [$isUpdate ? 'sometimes' : 'nullable', 'exists:penempatan,id'],
            'judul' => ['required', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string', 'max:10000'],
            'link_referensi' => [
                'nullable',
                'string',
                'max:2048',
                function (string $attribute, mixed $value, Closure $fail) {
                    if (! is_string($value) || trim($value) === '') {
                        return;
                    }

                    $raw = trim($value);
                    $urls = [];
                    if (str_starts_with($raw, '[') && str_ends_with($raw, ']')) {
                        $decoded = json_decode($raw, true);
                        if (! is_array($decoded)) {
                            $fail('Format daftar tautan referensi tidak valid.');

                            return;
                        }
                        $urls = $decoded;
                    } else {
                        $urls = [$raw];
                    }

                    $rule = new SafeExternalUrlRule;
                    foreach ($urls as $url) {
                        $rule->validate($attribute, $url, $fail);
                    }
                },
            ],
            'file_lampiran' => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,csv,ppt,pptx,zip,png,jpg,jpeg', 'max:10240'],
            'kategori' => ['nullable', 'string', 'max:50'],
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
            'kelompok_magang_ids.required' => 'Minimal satu tim magang harus dipilih.',
            'kelompok_magang_ids.min' => 'Minimal satu tim magang harus dipilih.',
            'kelompok_magang_ids.*.distinct' => 'Terdapat duplikasi tim yang dipilih.',
            'kelompok_magang_ids.*.exists' => 'Satu atau lebih tim bimbingan tidak ditemukan.',
            'judul.required' => 'Judul penugasan wajib diisi.',
            'deskripsi.required' => 'Petunjuk & instruksi pengerjaan wajib diisi.',
            'deskripsi.max' => 'Petunjuk & instruksi pengerjaan tidak boleh melebihi 10.000 karakter.',
            'deadline.required' => 'Batas waktu (deadline) tugas wajib ditentukan.',
            'deadline.after_or_equal' => 'Batas waktu (deadline) tugas tidak boleh di masa lampau.',
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

            // 4. Validasi Tugas Tim (tipe = kelompok)
            if ($this->input('tipe') === 'kelompok' && ! $this->isMethod('PUT') && ! $this->isMethod('PATCH')) {
                $kelompokIds = $this->input('kelompok_magang_ids') ?? [];
                if (is_array($kelompokIds) && ! empty($kelompokIds)) {
                    $uniqueKelompokIds = array_values(array_unique(array_filter(array_map('intval', $kelompokIds))));
                    $kelompokList = KelompokMagang::with(['anggota'])->whereIn('id', $uniqueKelompokIds)->get();

                    if ($kelompokList->count() !== count($uniqueKelompokIds)) {
                        $validator->errors()->add('kelompok_magang_ids', 'Satu atau lebih tim yang dipilih tidak ditemukan.');

                        return;
                    }

                    foreach ($kelompokList as $kelompok) {
                        $memberIds = collect([$kelompok->ketua_user_id])
                            ->merge($kelompok->anggota->pluck('user_id'))
                            ->filter()
                            ->unique()
                            ->values()
                            ->all();

                        $activePenempatans = Penempatan::with('peserta')
                            ->whereIn('peserta_id', $memberIds)
                            ->where('status', StatusPenempatanEnum::Aktif)
                            ->when($user && ! $user->isAdmin() && ! $user->isSuperAdmin(), fn ($q) => $q->where('pembimbing_id', $user->id))
                            ->get();

                        if ($activePenempatans->isEmpty()) {
                            $validator->errors()->add(
                                'kelompok_magang_ids',
                                "Tim {$kelompok->nama_kelompok} tidak memiliki peserta aktif di bawah bimbingan Anda."
                            );
                            break;
                        }

                        if ($deadlineInput) {
                            $deadline = Carbon::parse($deadlineInput)->startOfDay();
                            foreach ($activePenempatans as $pen) {
                                if ($pen->tgl_selesai && $deadline->gt(Carbon::parse($pen->tgl_selesai)->endOfDay())) {
                                    $validator->errors()->add(
                                        'deadline',
                                        "Deadline tugas tidak boleh melebihi batas akhir magang anggota tim {$kelompok->nama_kelompok} ({$pen->tgl_selesai->format('d/m/Y')})."
                                    );
                                    break 2;
                                }
                            }
                        }
                    }
                }
            }
        });
    }
}
