@php
    $meta = fn ($event) => collect([$event->city, $event->event_date?->format('M j, Y')])->filter()->implode(' · ') ?: 'Date TBA';
@endphp

<x-layouts.public title="Events">
    <section class="site-container py-6" style="padding-top: 5rem; padding-bottom: 5rem;">
        <x-kicker>Live</x-kicker>
        <h1 class="mt-3 fw-semibold display-5">Events &amp; Performances</h1>
        <div class="row row-cols-1 row-cols-sm-2 g-4 mt-2">
            @forelse ($events as $event)
                <div class="col">
                    <x-card :href="'/events/'.$event->slug" :title="$event->title" :meta="$meta($event)" />
                </div>
            @empty
                <p class="small" style="color: var(--color-text-secondary);">No events scheduled yet.</p>
            @endforelse
        </div>
    </section>
</x-layouts.public>
