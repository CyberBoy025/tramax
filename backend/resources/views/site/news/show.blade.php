<x-layouts.public :title="$post->title">
    <article class="site-container py-6" style="padding-top: 5rem; padding-bottom: 5rem;">
        <x-kicker>News</x-kicker>
        <h1 class="mt-3 fw-semibold display-5" style="max-width: 28ch;">{{ $post->title }}</h1>
        @if ($post->published_at)
            <p class="small mt-2" style="color: var(--color-text-secondary);">{{ $post->published_at->format('M j, Y') }}</p>
        @endif
        <div class="mt-4" style="max-width: 64ch; white-space: pre-line; color: var(--color-text-secondary);">{{ $post->body ?: 'Full article content coming soon.' }}</div>
    </article>
</x-layouts.public>
