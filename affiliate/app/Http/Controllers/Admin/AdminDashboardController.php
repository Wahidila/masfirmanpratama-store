<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Affiliator;
use App\Models\Commission;
use App\Models\Withdrawal;
use App\Models\ReferralOrder;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_affiliators' => Affiliator::count(),
            'active_affiliators' => Affiliator::where('status', 'active')->count(),
            'pending_affiliators' => Affiliator::where('status', 'pending')->count(),
            'total_orders' => ReferralOrder::count(),
            'total_commissions' => Commission::sum('amount'),
            'pending_withdrawals' => Withdrawal::where('status', 'pending')->count(),
            'total_withdrawn' => Withdrawal::where('status', 'completed')->sum('amount'),
        ];

        $pendingAffiliators = Affiliator::where('status', 'pending')
            ->with('type')
            ->latest()
            ->take(5)
            ->get();

        $pendingWithdrawals = Withdrawal::where('status', 'pending')
            ->with(['affiliator', 'method'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'pendingAffiliators', 'pendingWithdrawals'));
    }
}
