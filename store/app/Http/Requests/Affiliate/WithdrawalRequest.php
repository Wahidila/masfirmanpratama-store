<?php

namespace App\Http\Requests\Affiliate;

use App\Models\CommissionSetting;
use Illuminate\Foundation\Http\FormRequest;

class WithdrawalRequest extends FormRequest
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
        $minPayout = (float) (CommissionSetting::where('scope', 'global')->value('min_payout') ?? 50000);

        return [
            'amount' => ['required', 'numeric', 'min:'.$minPayout],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        $minPayout = (float) (CommissionSetting::where('scope', 'global')->value('min_payout') ?? 50000);

        return [
            'amount.required' => 'Jumlah penarikan wajib diisi.',
            'amount.numeric' => 'Jumlah penarikan harus berupa angka.',
            'amount.min' => 'Jumlah penarikan minimal Rp '.number_format($minPayout, 0, ',', '.').'.',
        ];
    }
}
