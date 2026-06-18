<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use App\Models\AffiliateEvent;
use App\Models\Commission;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class EventController extends Controller
{
    public function index(Request $request): View|Response
    {
        $affiliator = $request->user('affiliator');

        // Gate: hanya alumni + peserta
        if (! in_array($affiliator->type, ['alumni', 'peserta'], true)) {
            abort(403, 'Fitur ini hanya tersedia untuk affiliator tipe alumni dan peserta.');
        }

        // Event aktif
        $events = AffiliateEvent::where('status', 'active')
            ->where('ends_at', '>=', now())
            ->orderBy('starts_at')
            ->get();

        // Leaderboard sederhana: top 10 affiliator by total komisi approved
        $leaderboard = Commission::where('status', 'approved')
            ->selectRaw('affiliator_id, SUM(amount) as total_komisi')
            ->groupBy('affiliator_id')
            ->orderByDesc('total_komisi')
            ->limit(10)
            ->with('affiliator:id,name,type')
            ->get();

        return view('affiliate.dashboard.events', [
            'events' => $events,
            'leaderboard' => $leaderboard,
            'affiliator' => $affiliator,
        ]);
    }
}
