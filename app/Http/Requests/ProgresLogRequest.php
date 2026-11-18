<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Rules\SafeExternalUrlRule;
use Closure;
use Illuminate\Foundation\Http\FormRequest;

class ProgresLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('link')) {
            $inputLink = $this->input('link');
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
                            'link' => empty($normalized) ? null : (count($normalized) === 1 ? $normalized[0] : json_encode($normalized, JSON_UNESCAPED_SLASHES)),
                        ]);
                    }
                } else {
                    $link = $raw;
                    if ($link !== '' && str_contains($link, '.') && ! str_starts_with(strtolower($link), 'http://') && ! str_starts_with(strtolower($link), 'https://')) {
                        $link = 'https://'.$link;
                    }
                    $this->merge([
                        'link' => $link === '' ? null : $link,
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
                    'link' => empty($normalized) ? null : (count($normalized) === 1 ? $normalized[0] : json_encode($normalized, JSON_UNESCAPED_SLASHES)),
                ]);
            }
        }

        $hasAttachment = $this->hasFile('file') || filled($this->input('link')) || filled($this->input('recent_file_path'));

        if ($hasAttachment && blank($this->input('catatan'))) {
            $this->merge([
                'catatan' => 'Mengumpulkan berkas/tautan pekerjaan tugas.',
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'catatan' => ['required', 'string', 'min:3', 'max:3000'],
            'file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,zip,rar,doc,docx', 'max:5120'],
            'link' => [
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
                            $fail('Format daftar tautan pengumpulan tidak valid.');

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
            'recent_file_path' => ['nullable', 'string', 'max:500'],
        ];
    }
}
