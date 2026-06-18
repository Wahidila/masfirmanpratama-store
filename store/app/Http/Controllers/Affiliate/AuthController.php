<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use App\Http\Requests\Affiliate\LoginAffiliatorRequest;
use App\Http\Requests\Affiliate\RegisterAffiliatorRequest;
use App\Models\Affiliator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Tampilkan form registrasi affiliator.
     */
    public function showRegister(): View
    {
        return view('affiliate.auth.register');
    }

    /**
     * Proses registrasi affiliator baru.
     */
    public function register(RegisterAffiliatorRequest $request): RedirectResponse
    {
        $affiliator = Affiliator::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
            'phone' => $request->validated('phone'),
            'type' => $request->validated('type'),
            'status' => 'pending',
        ]);

        $affiliator->sendEmailVerificationNotification();

        Auth::guard('affiliator')->login($affiliator);

        return redirect()->route('affiliate.verification.notice');
    }

    /**
     * Tampilkan form login affiliator.
     */
    public function showLogin(): View
    {
        return view('affiliate.auth.login');
    }

    /**
     * Authenticate affiliator & start session.
     */
    public function login(LoginAffiliatorRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        if (! Auth::guard('affiliator')->attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password tidak cocok.',
            ]);
        }

        /** @var Affiliator $affiliator */
        $affiliator = Auth::guard('affiliator')->user();

        if ($affiliator->status === 'suspended') {
            Auth::guard('affiliator')->logout();

            throw ValidationException::withMessages([
                'email' => 'Akun Anda telah ditangguhkan. Silakan hubungi admin.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()
            ->intended(route('affiliate.verification.notice'));
    }

    /**
     * Logout affiliator & invalidate session.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('affiliator')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('affiliate.login');
    }
}
