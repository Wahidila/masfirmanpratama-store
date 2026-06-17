<?php

namespace App\Http\Controllers;

use App\Models\AffiliatorType;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function index(): View
    {
        $types = AffiliatorType::where('is_active', true)->get();

        $stats = [
            'total_affiliators' => \App\Models\Affiliator::where('status', 'active')->count(),
            'total_commissions_paid' => \App\Models\Commission::where('status', 'withdrawn')->sum('amount'),
        ];

        return view('landing', compact('types', 'stats'));
    }
}
