<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommissionSettingRequest extends FormRequest
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
            'rate_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'min_payout' => ['required', 'numeric', 'min:0'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'rate_percent.required' => 'Persentase komisi wajib diisi.',
            'rate_percent.numeric' => 'Persentase komisi harus berupa angka.',
            'rate_percent.min' => 'Persentase komisi minimal 0.',
            'rate_percent.max' => 'Persentase komisi maksimal 100.',
            'min_payout.required' => 'Minimum pencairan wajib diisi.',
            'min_payout.numeric' => 'Minimum pencairan harus berupa angka.',
            'min_payout.min' => 'Minimum pencairan tidak boleh negatif.',
        ];
    }
}
