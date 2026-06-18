<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use App\Http\Requests\Affiliate\WithdrawalRequest;
use App\Models\CommissionSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WithdrawalController extends Controller
{
    public function index(Request $request): View
    {
        $affiliator = $request->user('affiliator');
        $withdrawals = $affiliator->withdrawals()->latest()->paginate(20);

        // Saldo available
        $commissionApproved = (float) $affiliator->commissions()
            ->where('status', 'approved')
            ->sum('amount');
        $totalWithdrawn = (float) $affiliator->withdrawals()
            ->whereIn('status', ['requested', 'approved', 'paid'])
            ->sum('amount');
        $saldoAvailable = max(0, $commissionApproved - $totalWithdrawn);

        $minPayout = (float) (CommissionSetting::where('scope', 'global')->value('min_payout') ?? 50000);

        return view('affiliate.dashboard.withdrawals', [
            'withdrawals' => $withdrawals,
            'saldoAvailable' => $saldoAvailable,
            'minPayout' => $minPayout,
            'affiliator' => $affiliator,
        ]);
    }

    public function store(WithdrawalRequest $request): RedirectResponse
    {
        $affiliator = $request->user('affiliator');

        // Cek bank info
        if (empty($affiliator->bank_name) || empty($affiliator->bank_account) || empty($affiliator->bank_holder)) {
            return redirect()
                ->route('affiliate.profile.edit')
                ->with('error', 'Lengkapi informasi bank di profil terlebih dahulu sebelum melakukan penarikan.');
        }

        // Hitung saldo available
        $commissionApproved = (float) $affiliator->commissions()
            ->where('status', 'approved')
            ->sum('amount');
        $totalWithdrawn = (float) $affiliator->withdrawals()
            ->whereIn('status', ['requested', 'approved', 'paid'])
            ->sum('amount');
        $saldoAvailable = max(0, $commissionApproved - $totalWithdrawn);

        $amount = (float) $request->validated()['amount'];

        if ($amount > $saldoAvailable) {
            return back()->withErrors(['amount' => 'Jumlah penarikan melebihi saldo tersedia.'])->withInput();
        }

        $affiliator->withdrawals()->create([
            'amount' => $amount,
            'status' => 'requested',
            'bank_name' => $affiliator->bank_name,
            'bank_account' => $affiliator->bank_account,
            'bank_holder' => $affiliator->bank_holder,
            'requested_at' => now(),
        ]);

        return redirect()
            ->route('affiliate.withdrawals.index')
            ->with('success', 'Permintaan penarikan berhasil diajukan.');
    }
}
