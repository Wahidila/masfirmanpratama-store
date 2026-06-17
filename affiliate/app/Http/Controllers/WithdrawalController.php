<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Withdrawal;
use App\Models\WithdrawalMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WithdrawalController extends Controller
{
    public function index(): View
    {
        $affiliator = Auth::guard('affiliator')->user();

        $withdrawals = $affiliator->withdrawals()
            ->with('method')
            ->latest()
            ->paginate(10);

        return view('withdrawals.index', compact('withdrawals'));
    }

    public function create(): View
    {
        $affiliator = Auth::guard('affiliator')->user();
        $availableBalance = $affiliator->availableBalance();
        $methods = WithdrawalMethod::where('is_active', true)->get();

        return view('withdrawals.create', compact('availableBalance', 'methods'));
    }

    public function store(Request $request): RedirectResponse
    {
        $affiliator = Auth::guard('affiliator')->user();
        $availableBalance = $affiliator->availableBalance();

        $request->validate([
            'withdrawal_method_id' => ['required', 'exists:withdrawal_methods,id'],
            'amount' => ['required', 'numeric', 'min:1', "max:{$availableBalance}"],
            'account_number' => ['required', 'string', 'max:50'],
            'account_name' => ['required', 'string', 'max:100'],
        ], [
            'amount.required' => 'Jumlah penarikan wajib diisi.',
            'amount.min' => 'Jumlah penarikan minimal Rp 1.',
            'amount.max' => 'Jumlah penarikan melebihi saldo tersedia.',
            'withdrawal_method_id.required' => 'Metode penarikan wajib dipilih.',
            'account_number.required' => 'Nomor rekening wajib diisi.',
            'account_name.required' => 'Nama pemilik rekening wajib diisi.',
        ]);

        $method = WithdrawalMethod::findOrFail($request->withdrawal_method_id);

        if ($request->amount < $method->min_withdrawal) {
            return back()->withErrors([
                'amount' => "Minimum penarikan untuk {$method->name} adalah Rp ".number_format($method->min_withdrawal, 0, ',', '.'),
            ])->withInput();
        }

        // Check no pending withdrawal
        $pendingExists = $affiliator->withdrawals()
            ->whereIn('status', ['pending', 'processing'])
            ->exists();

        if ($pendingExists) {
            return back()->withErrors([
                'amount' => 'Anda masih memiliki penarikan yang sedang diproses.',
            ])->withInput();
        }

        DB::transaction(function () use ($affiliator, $request, $method) {
            // Create withdrawal
            $withdrawal = Withdrawal::create([
                'affiliator_id' => $affiliator->id,
                'withdrawal_method_id' => $method->id,
                'amount' => $request->amount,
                'account_number' => $request->account_number,
                'account_name' => $request->account_name,
                'status' => 'pending',
            ]);

            // Mark commissions as withdrawn (FIFO)
            $remaining = $request->amount;
            $commissions = $affiliator->commissions()
                ->where('status', 'available')
                ->orderBy('available_at')
                ->get();

            foreach ($commissions as $commission) {
                if ($remaining <= 0) {
                    break;
                }

                $commission->update([
                    'status' => 'withdrawn',
                    'withdrawn_at' => now(),
                ]);
                $remaining -= $commission->amount;
            }

            // Activity log
            ActivityLog::create([
                'affiliator_id' => $affiliator->id,
                'action' => 'withdraw_request',
                'description' => 'Permintaan penarikan Rp '.number_format($request->amount, 0, ',', '.')." via {$method->name}",
                'ip_address' => request()->ip(),
            ]);
        });

        return redirect()->route('withdrawals.index')
            ->with('success', 'Permintaan penarikan berhasil diajukan. Mohon tunggu proses verifikasi admin.');
    }
}
