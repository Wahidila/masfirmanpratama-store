<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAffiliatorRequest;
use App\Models\Affiliator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AffiliatorController extends Controller
{
    public function index(Request $request): View
    {
        $query = Affiliator::query();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $stats = [
            'total' => Affiliator::count(),
            'active' => Affiliator::where('status', 'active')->count(),
            'pending' => Affiliator::where('status', 'pending')->count(),
            'suspended' => Affiliator::where('status', 'suspended')->count(),
        ];

        $affiliators = $query->latest()->paginate(20)->withQueryString();

        return view('admin.affiliators.index', compact('affiliators', 'stats'));
    }

    public function show(Affiliator $affiliator): View
    {
        $affiliator->loadCount(['referralCodes', 'commissions', 'withdrawals']);
        $affiliator->load(['referralCodes', 'commissions' => function ($q) {
            $q->latest()->limit(10);
        }, 'withdrawals' => function ($q) {
            $q->latest()->limit(10);
        }]);

        $totalKomisi = $affiliator->commissions()->where('status', 'approved')->sum('amount');
        $totalWithdrawal = $affiliator->withdrawals()->where('status', 'paid')->sum('amount');

        return view('admin.affiliators.show', compact('affiliator', 'totalKomisi', 'totalWithdrawal'));
    }

    public function edit(Affiliator $affiliator): View
    {
        return view('admin.affiliators.edit', compact('affiliator'));
    }

    public function update(UpdateAffiliatorRequest $request, Affiliator $affiliator): RedirectResponse
    {
        $affiliator->update($request->validated());

        return redirect()
            ->route('admin.affiliators.index')
            ->with('status', 'Data affiliator berhasil diperbarui.');
    }

    public function destroy(Affiliator $affiliator): RedirectResponse
    {
        $affiliator->delete();

        return redirect()
            ->route('admin.affiliators.index')
            ->with('status', 'Affiliator berhasil dihapus.');
    }
}
