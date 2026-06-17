<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $affiliator = Auth::guard('affiliator')->user();

        $notifications = Notification::where('affiliator_id', $affiliator->id)
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(Notification $notification): RedirectResponse
    {
        $affiliator = Auth::guard('affiliator')->user();

        if ($notification->affiliator_id !== $affiliator->id) {
            abort(403);
        }

        $notification->update(['read_at' => now()]);

        return back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    public function markAllAsRead(): RedirectResponse
    {
        $affiliator = Auth::guard('affiliator')->user();

        Notification::where('affiliator_id', $affiliator->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
