<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if (Auth::guard('affiliator')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $affiliator = Auth::guard('affiliator')->user();

            if (!$affiliator->isActive()) {
                Auth::guard('affiliator')->logout();
                return back()->withErrors([
                    'email' => 'Akun Anda belum aktif. Silakan tunggu persetujuan admin.',
                ]);
            }

            ActivityLog::create([
                'affiliator_id' => $affiliator->id,
                'action' => 'login',
                'description' => 'Login berhasil',
                'ip_address' => $request->ip(),
            ]);

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('affiliator')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
