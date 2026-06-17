<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $affiliator = Auth::guard('affiliator')->user();
        $affiliator->load(['type', 'referralCodes', 'commissions']);

        $stats = [
            'total_earnings' => $affiliator->totalEarnings(),
            'available_balance' => $affiliator->availableBalance(),
            'total_referrals' => $affiliator->referralCodes()->count(),
            'total_clicks' => $affiliator->referralCodes()
                ->withCount('clicks')
                ->get()
                ->sum('clicks_count'),
            'total_orders' => $affiliator->referralOrders()->count(),
            'pending_commissions' => $affiliator->commissions()
                ->where('status', 'cooling')
                ->sum('amount'),
        ];

        $recentCommissions = $affiliator->commissions()
            ->with('referralOrder')
            ->latest()
            ->take(5)
            ->get();

        $recentOrders = $affiliator->referralOrders()
            ->with('referralCode')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('affiliator', 'stats', 'recentCommissions', 'recentOrders'));
    }
}
