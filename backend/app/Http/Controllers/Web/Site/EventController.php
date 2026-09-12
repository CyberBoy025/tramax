<?php

namespace App\Http\Controllers\Web\Site;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\View\View;

// Blade port of frontend/src/app/(marketing)/events/page.tsx and
// events/[slug]/page.tsx — same public-scoped queries as Api\EventController.
class EventController extends Controller
{
    public function index(): View
    {
        return view('site.events.index', [
            'events' => Event::query()->where('status', '!=', 'Cancelled')->orderBy('event_date')->get(),
        ]);
    }

    public function show(string $slug): View
    {
        $event = Event::query()->where('slug', $slug)->with('artists:id,artist_name,slug')->firstOrFail();

        return view('site.events.show', ['event' => $event]);
    }
}
