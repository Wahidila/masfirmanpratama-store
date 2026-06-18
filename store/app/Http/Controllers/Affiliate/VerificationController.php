<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use App\Models\Affiliator;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerificationController extends Controller
{
    /**
     * Tampilkan halaman pemberitahuan verifikasi email.
     */
    public function notice(): View
    {
        return view('affiliate.auth.verify-email');
    }

    /**
     * Verifikasi email affiliator via signed URL.
     */
    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        $request->fulfill();

        return redirect()
            ->route('affiliate.verification.notice')
            ->with('status', 'Email berhasil diverifikasi!');
    }

    /**
     * Kirim ulang email verifikasi.
     */
    public function resend(Request $request): RedirectResponse
    {
        /** @var Affiliator $user */
        $user = $request->user('affiliator');

        $user->sendEmailVerificationNotification();

        return back()->with('status', 'Link verifikasi telah dikirim ulang ke email Anda.');
    }
}
