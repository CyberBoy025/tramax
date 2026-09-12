<x-layouts.public title="Artists">
    <section class="site-container py-6" style="padding-top: 5rem;">
        <x-kicker>Roster</x-kicker>
        <h1 class="mt-3 fw-semibold display-5">Artists</h1>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4 mt-2">
            @forelse ($artists as $artist)
                <div class="col">
                    <x-card :href="'/artists/'.$artist->slug" :title="$artist->artist_name" :meta="$artist->genre" />
                </div>
            @empty
                <p class="small" style="color: var(--color-text-secondary);">No artists published yet.</p>
            @endforelse
        </div>
    </section>

    <section id="submit" class="site-container" style="padding-bottom: 6rem;">
        <div class="rounded-4 p-5 p-sm-6" style="background: var(--color-bg-raised);">
            <x-kicker>Join the roster</x-kicker>
            <h2 class="mt-3 fw-semibold display-6">Submit your music</h2>
            <p class="mt-3" style="max-width: 56ch; color: var(--color-text-secondary);">
                Tell us about yourself and your music — our A&amp;R team reviews every application
                (Submitted &rarr; Under Review &rarr; Shortlisted &rarr; Accepted).
            </p>
            <div class="mt-4">
                @include('site.partials.enquiry-form', [
                    'action' => route('site.artists.apply'),
                    'submitLabel' => 'Submit Application',
                    'fields' => [
                        ['name' => 'full_name', 'label' => 'Full name'],
                        ['name' => 'artist_name', 'label' => 'Artist name'],
                        ['name' => 'email', 'label' => 'Email', 'type' => 'email'],
                        ['name' => 'genre', 'label' => 'Genre'],
                        ['name' => 'biography', 'label' => 'Tell us about you', 'type' => 'textarea'],
                    ],
                ])
            </div>
        </div>
    </section>
</x-layouts.public>
