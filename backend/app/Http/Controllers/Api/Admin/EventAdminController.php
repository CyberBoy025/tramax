<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

// Read gated to Super Administrator, Management, A&R, Content Manager; write
// gated to Super Administrator + A&R; delete further restricted to Super
// Administrator — per discovery.md §3's Events & Bookings row.
// Unlike the public GET /events, this returns every status, including
// Cancelled — an admin managing the calendar needs to see those too.
class EventAdminController extends Controller
{
    private const STATUSES = ['Upcoming', 'Completed', 'Cancelled'];

    public function index(): JsonResponse
    {
        $events = Event::query()
            ->with('artists:id,artist_name,slug')
            ->orderBy('event_date')
            ->get();

        return response()->json(['data' => $events]);
    }

    public function show(Event $event): JsonResponse
    {
        return response()->json(['data' => $event->load('artists:id,artist_name,slug')]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);
        $artistIds = $data['artist_profile_ids'] ?? [];
        unset($data['artist_profile_ids']);

        $data['slug'] = Str::slug($data['title']).'-'.Str::random(4);
        $event = Event::create($data);

        if ($artistIds) {
            $event->artists()->sync($artistIds);
        }

        return response()->json(['data' => $event->load('artists:id,artist_name,slug')], 201);
    }

    public function update(Request $request, Event $event): JsonResponse
    {
        $data = $this->validated($request, sometimes: true);
        $artistIds = $data['artist_profile_ids'] ?? null;
        unset($data['artist_profile_ids']);

        $event->update($data);

        if ($artistIds !== null) {
            $event->artists()->sync($artistIds);
        }

        return response()->json(['data' => $event->fresh()->load('artists:id,artist_name,slug')]);
    }

    public function destroy(Request $request, Event $event): JsonResponse
    {
        if (! $request->user()->hasRole(Role::SUPER_ADMIN)) {
            return response()->json(['message' => 'Only a Super Administrator can delete an event.'], 403);
        }

        $event->delete();

        return response()->json(['data' => ['message' => 'Deleted.']]);
    }

    private function validated(Request $request, bool $sometimes = false): array
    {
        $rule = fn (string $r) => $sometimes ? ['sometimes', $r] : ['required', $r];

        return $request->validate([
            'title' => $rule('string'),
            'venue' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'event_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
            'ticket_link' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'in:'.implode(',', self::STATUSES)],
            'artist_profile_ids' => ['sometimes', 'array'],
            'artist_profile_ids.*' => ['integer', 'exists:artist_profiles,id'],
        ]);
    }
}
