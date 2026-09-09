<?php

namespace App\Http\Controllers\Api\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// Events & Bookings (discovery.md §3) — Own (read/submit info) for the
// Artist role. Read-only for this MVP pass: "submit info" has no modeled
// field to submit (events carry no per-artist availability/notes column),
// so this stays a read-only view of events they're linked to — same MVP-
// depth judgment call as Distribution (status tracking, not a workflow
// engine).
class PortalEventController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $profile = $request->user()->artistProfile;
        if (! $profile) {
            return response()->json(['data' => []]);
        }

        $events = $profile->events()->orderBy('event_date')->get();

        return response()->json(['data' => $events]);
    }
}
