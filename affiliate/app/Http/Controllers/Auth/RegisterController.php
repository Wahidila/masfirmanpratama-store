<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Affiliator;
use App\Models\AffiliatorType;
use App\Models\ActivityLog;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function showRegistrationForm(): View
    {
        $types = AffiliatorType::where('is_active', true)->get();

        return view('auth.register', compact('types'));
    }

    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:affiliators'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'affiliator_type_id' => ['required', 'exists:affiliator_types,id'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'affiliator_type_id.required' => 'Tipe affiliator wajib dipilih.',
            'affiliator_type_id.exists' => 'Tipe affiliator tidak valid.',
        ]);

        $affiliator = Affiliator::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'phone' => $request->phone,
            'affiliator_type_id' => $request->affiliator_type_id,
            'status' => 'pending',
        ]);

        event(new Registered($affiliator));

        ActivityLog::create([
            'affiliator_id' => $affiliator->id,
            'action' => 'register',
            'description' => "Registrasi sebagai {$affiliator->type->name}",
            'ip_address' => $request->ip(),
        ]);

        Auth::guard('affiliator')->login($affiliator);

        return redirect()->route('verification.notice')
            ->with('success', 'Registrasi berhasil! Silakan verifikasi email Anda.');
    }
}
