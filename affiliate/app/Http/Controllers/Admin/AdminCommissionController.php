<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AffiliatorType;
use App\Models\Commission;
use App\Models\CommissionSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCommissionController extends Controller
{
    public function index(Request $request): View
    {
        $query = Commission::with(['affiliator', 'referralOrder']);

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $commissions = $query->latest()->paginate(20);

        return view('admin.commissions.index', compact('commissions'));
    }

    public function settings(): View
    {
        $settings = CommissionSetting::with('affiliatorType')->get();
        $types = AffiliatorType::where('is_active', true)->get();

        return view('admin.commissions.settings', compact('settings', 'types'));
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*.rate' => 'required|numeric|min:0|max:100',
            'settings.*.cooling_days' => 'required|integer|min:0|max:90',
        ]);

        foreach ($request->settings as $id => $data) {
            CommissionSetting::where('id', $id)->update([
                'rate' => $data['rate'],
                'cooling_days' => $data['cooling_days'],
            ]);
        }

        return back()->with('success', 'Pengaturan komisi berhasil disimpan.');
    }
}
