<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ArtistProfile;
use App\Models\Event;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

// Blade equivalent of Api\Admin\EventAdminController — same artist-linking
// via sync() on the pivot. One HTML-forms limitation worth noting: a
// <select multiple> with nothing selected submits no field at all, so
// deselecting every artist on an existing event is indistinguishable from
// "didn't touch this field" and won't clear the links — selecting a
// different set, or removing some but not all, works normally.
class EventController extends Controller
{
    private const STATUSES = ['Upcoming', 'Completed', 'Cancelled'];

    public function index(): View
    {
        return view('admin.events.index', [
            'events' => Event::query()->with('artists:id,artist_name')->orderBy('event_date')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.events.form', [
            'event' => null,
            'artists' => ArtistProfile::query()->orderBy('artist_name')->get(['id', 'artist_name']),
            'statuses' => self::STATUSES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $artistIds = $data['artist_profile_ids'] ?? [];
        unset($data['artist_profile_ids']);

        $data['slug'] = Str::slug($data['title']).'-'.Str::random(4);
        $event = Event::create($data);

        if ($artistIds) {
            $event->artists()->sync($artistIds);
        }

        return redirect()->route('admin.events.index')->with('success', 'Event created.');
    }

    public function edit(Event $event): View
    {
        return view('admin.events.form', [
            'event' => $event->load('artists:id'),
            'artists' => ArtistProfile::query()->orderBy('artist_name')->get(['id', 'artist_name']),
            'statuses' => self::STATUSES,
        ]);
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $data = $this->validated($request, sometimes: true);
        $artistIds = $data['artist_profile_ids'] ?? null;
        unset($data['artist_profile_ids']);

        $event->update($data);

        if ($artistIds !== null) {
            $event->artists()->sync($artistIds);
        }

        return redirect()->route('admin.events.index')->with('success', 'Event updated.');
    }

    public function destroy(Request $request, Event $event): RedirectResponse
    {
        if (! $request->user()->hasRole(Role::SUPER_ADMIN)) {
            abort(403, 'Only a Super Administrator can delete an event.');
        }

        $event->delete();

        return redirect()->route('admin.events.index')->with('success', 'Event deleted.');
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
