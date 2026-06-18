<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'type' => ['required', 'in:banner,brosur,video,template_wa'],
            'file' => ['nullable', 'file', 'mimes:pdf,zip,png,jpg,jpeg,gif', 'max:10240'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul materi wajib diisi.',
            'title.max' => 'Judul materi maksimal 255 karakter.',
            'description.max' => 'Deskripsi maksimal 1000 karakter.',
            'type.required' => 'Tipe materi wajib dipilih.',
            'type.in' => 'Tipe materi tidak valid.',
            'file.file' => 'File tidak valid.',
            'file.mimes' => 'Format file harus: pdf, zip, png, jpg, jpeg, atau gif.',
            'file.max' => 'Ukuran file maksimal 10MB.',
        ];
    }
}
