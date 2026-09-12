<x-layouts.public>
    <section class="site-container pt-5 pb-5 pt-sm-6 pb-sm-6" style="padding-top: 4rem; padding-bottom: 6rem;">
        <x-kicker>Tramax Entertainment</x-kicker>
        <h1 class="mt-3 fw-semibold display-4" style="max-width: 18ch;">
            Discover. <span class="font-accent">Develop.</span> Promote.
        </h1>
        <p class="mt-4 fs-5" style="max-width: 52ch; color: var(--color-text-secondary);">
            Tramax finds talent, builds careers, and takes music from the studio to the stage —
            artists, catalogue, rights, and revenue, in one place.
        </p>
        <div class="d-flex flex-wrap gap-3 mt-4">
            <a href="/artists" class="btn btn-primary rounded-pill">Explore Artists</a>
            <a href="/music" class="btn btn-outline-light rounded-pill">Listen to Music</a>
        </div>

        <div class="hero-placeholder mt-5" style="aspect-ratio: 21/9;" aria-hidden="true">
            Featured artist / release hero image
        </div>
    </section>

    <section class="py-5" style="border-top: 1px solid var(--color-border-default); border-bottom: 1px solid var(--color-border-default); background: var(--color-bg-raised);">
        <div class="site-container row row-cols-2 row-cols-sm-4 g-4">
            @foreach ($stats as $stat)
                <div class="col">
                    <p class="font-data fs-2 fw-semibold mb-0">{{ $stat['value'] }}</p>
                    <p class="small mt-1 mb-0" style="color: var(--color-text-secondary);">{{ $stat['label'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section class="site-container py-6" style="padding-top: 6rem; padding-bottom: 6rem;">
        <x-kicker>Featured</x-kicker>
        <h2 class="mt-3 fw-semibold display-6">Artists on Tramax</h2>
        <div class="row row-cols-1 row-cols-sm-3 g-4 mt-2">
            @forelse ($featuredArtists as $artist)
                <div class="col">
                    <x-card :href="'/artists/'.$artist->slug" :title="$artist->artist_name" :meta="$artist->genre" />
                </div>
            @empty
                <p class="small" style="color: var(--color-text-secondary);">No artists published yet.</p>
            @endforelse
        </div>
    </section>

    <section class="site-container pb-6" style="padding-bottom: 6rem;">
        <div class="rounded-4 p-5 p-sm-6" style="background: var(--color-bg-raised);">
            <x-kicker>Join Tramax</x-kicker>
            <h2 class="mt-3 fw-semibold display-6" style="max-width: 24ch;">Ready to take your music further?</h2>
            <p class="mt-3" style="max-width: 52ch; color: var(--color-text-secondary);">
                Submit your music and tell us about your project — our A&amp;R team reviews every
                application.
            </p>
            <a href="/artists#submit" class="btn btn-primary rounded-pill mt-3">Submit Your Music</a>
        </div>
    </section>
</x-layouts.public>
