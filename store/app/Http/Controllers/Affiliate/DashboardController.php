<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $affiliator = $request->user('affiliator');

        // Total klik dari semua referral codes
        $totalClicks = $affiliator->referralCodes()->sum('clicks_count');

        // Total order ter-referral
        $totalOrders = $affiliator->commissions()->count();

        // Komisi per status
        $commissionPending = $affiliator->commissions()
            ->where('status', 'pending')
            ->sum('amount');
        $commissionApproved = $affiliator->commissions()
            ->where('status', 'approved')
            ->sum('amount');
        $commissionPaid = $affiliator->commissions()
            ->where('status', 'paid')
            ->sum('amount');

        // Saldo bisa ditarik = approved - sudah ditarik (withdrawal paid/approved/requested)
        $totalWithdrawn = $affiliator->withdrawals()
            ->whereIn('status', ['requested', 'approved', 'paid'])
            ->sum('amount');
        $saldoAvailable = max(0, (float) $commissionApproved - (float) $totalWithdrawn);

        // Kode referral utama (pertama dibuat)
        $primaryCode = $affiliator->referralCodes()->oldest()->first();

        return view('affiliate.dashboard.index', [
            'affiliator' => $affiliator,
            'totalClicks' => (int) $totalClicks,
            'totalOrders' => $totalOrders,
            'commissionPending' => (float) $commissionPending,
            'commissionApproved' => (float) $commissionApproved,
            'commissionPaid' => (float) $commissionPaid,
            'saldoAvailable' => $saldoAvailable,
            'primaryCode' => $primaryCode,
        ]);
    }
}
