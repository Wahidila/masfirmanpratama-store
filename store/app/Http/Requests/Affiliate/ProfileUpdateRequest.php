<?php

namespace App\Http\Requests\Affiliate;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
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
            'phone' => ['nullable', 'string', 'max:20'],
            'bank_name' => ['required', 'string', 'max:50'],
            'bank_account' => ['required', 'string', 'max:30'],
            'bank_holder' => ['required', 'string', 'max:100'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'bank_name.required' => 'Nama bank wajib diisi.',
            'bank_name.max' => 'Nama bank maksimal 50 karakter.',
            'bank_account.required' => 'Nomor rekening wajib diisi.',
            'bank_account.max' => 'Nomor rekening maksimal 30 karakter.',
            'bank_holder.required' => 'Nama pemilik rekening wajib diisi.',
            'bank_holder.max' => 'Nama pemilik rekening maksimal 100 karakter.',
            'phone.max' => 'Nomor telepon maksimal 20 karakter.',
        ];
    }
}
