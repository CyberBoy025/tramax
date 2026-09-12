<?php

namespace App\Http\Controllers\Web\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Blade equivalent of Api\Portal\PortalEventController — read-only list of
// events this artist is linked to.
class EventController extends Controller
{
    public function index(Request $request): View
    {
        $profile = $request->user()->artistProfile;

        $events = $profile
            ? $profile->events()->orderBy('event_date')->get()
            : null;

        return view('portal.bookings.index', ['events' => $events]);
    }
}
