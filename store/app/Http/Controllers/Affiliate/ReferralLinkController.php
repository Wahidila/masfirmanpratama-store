<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use App\Models\ReferralCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ReferralLinkController extends Controller
{
    public function index(Request $request): View
    {
        $affiliator = $request->user('affiliator');
        $codes = $affiliator->referralCodes()->latest()->get();

        return view('affiliate.dashboard.referral-links', [
            'codes' => $codes,
            'baseUrl' => url('/affiliate?ref='),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $affiliator = $request->user('affiliator');

        // Generate kode unik
        do {
            $code = strtoupper(Str::random(8));
        } while (ReferralCode::where('code', $code)->exists());

        $affiliator->referralCodes()->create([
            'code' => $code,
            'clicks_count' => 0,
        ]);

        return redirect()
            ->route('affiliate.referral-links.index')
            ->with('success', 'Kode referral baru berhasil dibuat.');
    }
}
