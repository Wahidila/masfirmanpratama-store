<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CommissionController extends Controller
{
    public function index(Request $request): View
    {
        $affiliator = Auth::guard('affiliator')->user();

        $query = $affiliator->commissions()->with('referralOrder');

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $commissions = $query->latest()->paginate(15);

        $summary = [
            'cooling' => $affiliator->commissions()->where('status', 'cooling')->sum('amount'),
            'available' => $affiliator->commissions()->where('status', 'available')->sum('amount'),
            'withdrawn' => $affiliator->commissions()->where('status', 'withdrawn')->sum('amount'),
            'total' => $affiliator->commissions()->whereIn('status', ['available', 'withdrawn'])->sum('amount'),
        ];

        return view('commissions.index', compact('commissions', 'summary'));
    }
}
