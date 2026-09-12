<x-layouts.public :title="$artist->artist_name">
    <section class="site-container py-6" style="padding-top: 5rem; padding-bottom: 5rem;">
        <x-kicker>Artist</x-kicker>
        <h1 class="mt-3 fw-semibold display-5">{{ $artist->artist_name }}</h1>
        @if ($artist->genre)
            <p class="mt-2" style="color: var(--color-text-secondary);">{{ $artist->genre }}</p>
        @endif
        @if ($artist->biography)
            <p class="mt-4" style="max-width: 56ch; color: var(--color-text-secondary);">{{ $artist->biography }}</p>
        @endif

        <h2 class="mt-6 fs-4 fw-semibold" style="margin-top: 4rem;">Releases</h2>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4 mt-2">
            @forelse ($artist->releases as $release)
                <div class="col">
                    <x-card :href="'/music/'.$release->slug" :title="$release->title" :meta="$release->type" />
                </div>
            @empty
                <p class="small" style="color: var(--color-text-secondary);">No releases published yet.</p>
            @endforelse
        </div>
    </section>
</x-layouts.public>
