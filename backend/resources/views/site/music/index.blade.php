<x-layouts.public title="Music">
    <section class="site-container py-6" style="padding-top: 5rem; padding-bottom: 5rem;">
        <x-kicker>Catalogue</x-kicker>
        <h1 class="mt-3 fw-semibold display-5">Music &amp; Releases</h1>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4 mt-2">
            @forelse ($releases as $release)
                <div class="col">
                    <x-card
                        :href="'/music/'.$release->slug"
                        :title="$release->title"
                        :meta="$release->artist ? $release->type.' · '.$release->artist->artist_name : $release->type"
                    />
                </div>
            @empty
                <p class="small" style="color: var(--color-text-secondary);">No releases published yet.</p>
            @endforelse
        </div>
    </section>
</x-layouts.public>
