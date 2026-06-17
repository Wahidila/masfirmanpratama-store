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
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Simple admin auth — config-based for M3
        // M4 will integrate with store admin
        if ($request->email === config('admin.email') && $request->password === config('admin.password')) {
            session(['admin_authenticated' => true, 'admin_email' => $request->email]);
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => 'Kredensial admin tidak valid.']);
    }

    public function logout(Request $request): RedirectResponse
    {
        session()->forget(['admin_authenticated', 'admin_email']);
        return redirect()->route('admin.login');
    }
}
