<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAffiliatorRequest extends FormRequest
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
            'status' => ['required', 'in:pending,active,suspended'],
            'type' => ['required', 'in:alumni,non_alumni,peserta'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status tidak valid.',
            'type.required' => 'Tipe affiliator wajib diisi.',
            'type.in' => 'Tipe affiliator tidak valid.',
        ];
    }
}
