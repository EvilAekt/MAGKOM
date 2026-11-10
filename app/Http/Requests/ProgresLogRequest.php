<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Rules\SafeExternalUrlRule;
use Illuminate\Foundation\Http\FormRequest;

class ProgresLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('link') && is_string($this->input('link'))) {
            $this->merge([
                'link' => trim($this->input('link')),
            ]);
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
            'link' => ['nullable', 'string', 'max:2048', new SafeExternalUrlRule],
            'recent_file_path' => ['nullable', 'string', 'max:500'],
        ];
    }
}
