<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAffiliateEventRequest;
use App\Http\Requests\Admin\UpdateAffiliateEventRequest;
use App\Models\AffiliateEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AffiliateEventController extends Controller
{
    public function index(Request $request): View
    {
        $query = AffiliateEvent::query();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->input('search').'%');
        }

        $stats = [
            'total' => AffiliateEvent::count(),
            'draft' => AffiliateEvent::where('status', 'draft')->count(),
            'active' => AffiliateEvent::where('status', 'active')->count(),
            'ended' => AffiliateEvent::where('status', 'ended')->count(),
        ];

        $events = $query->latest()->paginate(20)->withQueryString();

        return view('admin.affiliate-events.index', compact('events', 'stats'));
    }

    public function create(): View
    {
        return view('admin.affiliate-events.create');
    }

    public function store(StoreAffiliateEventRequest $request): RedirectResponse
    {
        AffiliateEvent::create($request->validated());

        return redirect()
            ->route('admin.affiliate-events.index')
            ->with('status', 'Event affiliate berhasil dibuat.');
    }

    public function edit(AffiliateEvent $affiliateEvent): View
    {
        return view('admin.affiliate-events.edit', compact('affiliateEvent'));
    }

    public function update(UpdateAffiliateEventRequest $request, AffiliateEvent $affiliateEvent): RedirectResponse
    {
        $affiliateEvent->update($request->validated());

        return redirect()
            ->route('admin.affiliate-events.index')
            ->with('status', 'Event affiliate berhasil diperbarui.');
    }

    public function destroy(AffiliateEvent $affiliateEvent): RedirectResponse
    {
        $affiliateEvent->delete();

        return redirect()
            ->route('admin.affiliate-events.index')
            ->with('status', 'Event affiliate berhasil dihapus.');
    }
}
