<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminLoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('admin.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email:rfc,strict',
            'password' => 'required',
        ]);

        // Simple admin auth — config-based for M3
        // M4 will integrate with store admin
        $emailMatch = hash_equals((string) config('admin.email'), (string) $request->email);
        $passwordMatch = hash_equals((string) config('admin.password'), (string) $request->password);

        if ($emailMatch && $passwordMatch) {
            $request->session()->regenerate();
            session(['admin_authenticated' => true, 'admin_email' => $request->email]);

            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => 'Kredensial admin tidak valid.']);
    }

    public function logout(Request $request): RedirectResponse
    {
        session()->forget(['admin_authenticated', 'admin_email']);
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
