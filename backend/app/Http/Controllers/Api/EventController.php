<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    public function index(): JsonResponse
    {
        $events = Event::query()
            ->where('status', '!=', 'Cancelled')
            ->orderBy('event_date')
            ->get(['id', 'title', 'slug', 'venue', 'city', 'event_date', 'status']);

        return response()->json(['data' => $events]);
    }

    public function show(string $slug): JsonResponse
    {
        $event = Event::query()
            ->where('slug', $slug)
            ->with('artists:id,artist_name,slug')
            ->firstOrFail();

        return response()->json(['data' => $event]);
    }
}
