<x-layouts.public :title="$release->title">
    <section class="site-container py-6" style="padding-top: 5rem; padding-bottom: 5rem;">
        <x-kicker>{{ $release->type }}</x-kicker>
        <h1 class="mt-3 fw-semibold display-5">{{ $release->title }}</h1>
        @if ($release->artist)
            <p class="mt-2" style="color: var(--color-text-secondary);">
                By <a href="{{ '/artists/'.$release->artist->slug }}" class="text-decoration-underline">{{ $release->artist->artist_name }}</a>
            </p>
        @endif
        @if ($release->release_date)
            <p class="small mt-1" style="color: var(--color-text-secondary);">Released {{ $release->release_date->format('M j, Y') }}</p>
        @endif

        @if ($release->tracks->count())
            <h2 class="mt-6 fs-4 fw-semibold" style="margin-top: 4rem;">Tracklist</h2>
            <ol class="list-unstyled d-flex flex-column gap-2 mt-3">
                @foreach ($release->tracks as $track)
                    <li class="d-flex align-items-center gap-3 small py-2" style="border-bottom: 1px solid var(--color-border-default);">
                        <span class="font-data" style="color: var(--color-text-muted);">{{ $track->track_number }}</span>
                        {{ $track->title }}
                    </li>
                @endforeach
            </ol>
        @endif
    </section>
</x-layouts.public>
