<x-layouts.public title="News">
    <section class="site-container py-6" style="padding-top: 5rem; padding-bottom: 5rem;">
        <x-kicker>Newsroom</x-kicker>
        <h1 class="mt-3 fw-semibold display-5">News</h1>
        <div class="row row-cols-1 row-cols-sm-2 g-4 mt-2">
            @forelse ($posts as $post)
                <div class="col">
                    <x-card :href="'/news/'.$post->slug" :title="$post->title" :meta="$post->published_at?->format('M j, Y')" />
                </div>
            @empty
                <p class="small" style="color: var(--color-text-secondary);">No news posts published yet.</p>
            @endforelse
        </div>
    </section>
</x-layouts.public>
