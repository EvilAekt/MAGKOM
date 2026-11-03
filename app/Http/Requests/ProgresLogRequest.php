<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProgresLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
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
            'link' => ['nullable', 'url', 'max:2048'],
            'recent_file_path' => ['nullable', 'string', 'max:500'],
        ];
    }
}
