<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CommissionController extends Controller
{
    public function index(Request $request): View
    {
        $query = Commission::with('affiliator');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('affiliator', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $stats = [
            'total' => Commission::count(),
            'pending' => Commission::where('status', 'pending')->count(),
            'approved' => Commission::where('status', 'approved')->count(),
            'rejected' => Commission::where('status', 'rejected')->count(),
            'total_approved_amount' => Commission::where('status', 'approved')->sum('amount'),
        ];

        $commissions = $query->latest()->paginate(20)->withQueryString();

        return view('admin.commissions.index', compact('commissions', 'stats'));
    }

    public function approve(Commission $commission): RedirectResponse
    {
        abort_if($commission->status !== 'pending', 422, 'Komisi ini tidak dalam status pending.');

        DB::transaction(function () use ($commission) {
            $commission->update([
                'status' => 'approved',
                'approved_at' => now(),
            ]);
        });

        return redirect()
            ->route('admin.commissions.index')
            ->with('status', 'Komisi berhasil disetujui.');
    }

    public function reject(Commission $commission): RedirectResponse
    {
        abort_if($commission->status !== 'pending', 422, 'Komisi ini tidak dalam status pending.');

        DB::transaction(function () use ($commission) {
            $commission->update([
                'status' => 'rejected',
                'approved_at' => now(),
            ]);
        });

        return redirect()
            ->route('admin.commissions.index')
            ->with('status', 'Komisi berhasil ditolak.');
    }
}
