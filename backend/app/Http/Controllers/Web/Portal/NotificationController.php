<?php

namespace App\Http\Controllers\Web\Portal;

use App\Http\Controllers\Controller;
use App\Models\PortalNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Blade equivalent of Api\Portal\PortalNotificationController.
class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        return view('portal.notifications.index', [
            'notifications' => $request->user()->portalNotifications()->orderByDesc('created_at')->get(),
        ]);
    }

    public function markRead(Request $request, PortalNotification $notification): RedirectResponse
    {
        if ($notification->user_id !== $request->user()->id) {
            abort(404);
        }

        $notification->update(['read_at' => now()]);

        return back();
    }
}
