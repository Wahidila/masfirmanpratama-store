<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommissionController extends Controller
{
    public function index(Request $request): View
    {
        $affiliator = $request->user('affiliator');
        $status = $request->query('status');

        $query = $affiliator->commissions()->with('referralOrder.order')->latest();

        if ($status && in_array($status, ['pending', 'approved', 'paid', 'rejected'], true)) {
            $query->where('status', $status);
        }

        $commissions = $query->paginate(20)->withQueryString();

        return view('affiliate.dashboard.commissions', [
            'commissions' => $commissions,
            'currentStatus' => $status,
        ]);
    }
}
