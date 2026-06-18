<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use App\Http\Requests\Affiliate\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $affiliator = $request->user('affiliator');

        return view('affiliate.dashboard.profile', [
            'affiliator' => $affiliator,
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $affiliator = $request->user('affiliator');
        $affiliator->update($request->validated());

        return redirect()
            ->route('affiliate.profile.edit')
            ->with('success', 'Profil berhasil diperbarui.');
    }
}
