<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateCommissionSettingRequest;
use App\Models\CommissionSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CommissionSettingController extends Controller
{
    public function index(): View
    {
        $settings = CommissionSetting::all();

        return view('admin.commission-settings.index', compact('settings'));
    }

    public function update(UpdateCommissionSettingRequest $request, CommissionSetting $commissionSetting): RedirectResponse
    {
        $commissionSetting->update($request->validated());

        return redirect()
            ->route('admin.commission-settings.index')
            ->with('status', 'Pengaturan komisi berhasil diperbarui.');
    }
}
