<?php

namespace App\Http\Controllers\Web\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Blade port of frontend/src/app/portal/(protected)/dashboard/page.tsx.
class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $profile = $request->user()->artistProfile;

        $releases = $profile?->releases ?? collect();
        $royalty = $profile?->royaltyStatements ?? collect();
        $events = $profile?->events ?? collect();
        $notifications = $request->user()->portalNotifications;

        return view('portal.dashboard', [
            'profile' => $profile,
            'stats' => [
                ['label' => 'Releases', 'value' => $releases->count()],
                ['label' => 'Upcoming Bookings', 'value' => $events->where('status', 'Upcoming')->count()],
                ['label' => 'Total Earnings', 'value' => '₦'.number_format($royalty->sum('artist_share'))],
                ['label' => 'Unread Notifications', 'value' => $notifications->whereNull('read_at')->count()],
            ],
        ]);
    }
}
