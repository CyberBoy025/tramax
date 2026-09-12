<x-layouts.public :title="$event->title">
    <section class="site-container py-6" style="padding-top: 5rem; padding-bottom: 5rem;">
        <x-kicker>Event</x-kicker>
        <h1 class="mt-3 fw-semibold display-5">{{ $event->title }}</h1>
        <p class="mt-2" style="color: var(--color-text-secondary);">
            {{ collect([$event->venue, $event->city])->filter()->implode(', ') ?: 'Venue TBA' }}
            @if ($event->event_date)
                &middot; {{ $event->event_date->format('M j, Y') }}
            @endif
        </p>
        @if ($event->description)
            <p class="mt-4" style="max-width: 56ch; color: var(--color-text-secondary);">{{ $event->description }}</p>
        @endif
        @if ($event->artists->count())
            <p class="small mt-3" style="color: var(--color-text-secondary);">
                Featuring {{ $event->artists->pluck('artist_name')->implode(', ') }}
            </p>
        @endif
        <a href="/events" class="btn btn-outline-light rounded-pill mt-4">Back to Events</a>
    </section>
</x-layouts.public>
