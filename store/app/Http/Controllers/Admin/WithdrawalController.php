<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WithdrawalController extends Controller
{
    public function index(Request $request): View
    {
        $query = Withdrawal::with('affiliator');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('affiliator', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $stats = [
            'total' => Withdrawal::count(),
            'requested' => Withdrawal::where('status', 'requested')->count(),
            'approved' => Withdrawal::where('status', 'approved')->count(),
            'paid' => Withdrawal::where('status', 'paid')->count(),
            'rejected' => Withdrawal::where('status', 'rejected')->count(),
        ];

        $withdrawals = $query->latest()->paginate(20)->withQueryString();

        return view('admin.withdrawals.index', compact('withdrawals', 'stats'));
    }

    public function approve(Withdrawal $withdrawal): RedirectResponse
    {
        abort_if($withdrawal->status !== 'requested', 422, 'Penarikan ini tidak dalam status requested.');

        DB::transaction(function () use ($withdrawal) {
            $withdrawal->update(['status' => 'approved']);
        });

        return redirect()
            ->route('admin.withdrawals.index')
            ->with('status', 'Penarikan berhasil disetujui.');
    }

    public function markPaid(Withdrawal $withdrawal): RedirectResponse
    {
        abort_if($withdrawal->status !== 'approved', 422, 'Penarikan ini tidak dalam status approved.');

        DB::transaction(function () use ($withdrawal) {
            $withdrawal->update([
                'status' => 'paid',
                'processed_at' => now(),
            ]);
        });

        return redirect()
            ->route('admin.withdrawals.index')
            ->with('status', 'Penarikan berhasil ditandai lunas.');
    }

    public function reject(Withdrawal $withdrawal): RedirectResponse
    {
        abort_if(! in_array($withdrawal->status, ['requested', 'approved']), 422, 'Penarikan ini tidak dapat ditolak.');

        DB::transaction(function () use ($withdrawal) {
            $withdrawal->update(['status' => 'rejected']);
        });

        return redirect()
            ->route('admin.withdrawals.index')
            ->with('status', 'Penarikan berhasil ditolak.');
    }
}
