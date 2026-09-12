<x-layouts.public title="Videos">
    <section class="site-container py-6" style="padding-top: 5rem; padding-bottom: 5rem;">
        <x-kicker>Media</x-kicker>
        <h1 class="mt-3 fw-semibold display-5">Videos &amp; Media</h1>

        <div class="d-flex flex-wrap gap-2 mt-3">
            @foreach ($categories as $category)
                <span class="rounded-pill small px-3 py-1" style="border: 1px solid var(--color-border-default); color: var(--color-text-secondary);">{{ $category }}</span>
            @endforeach
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4 mt-2">
            @for ($i = 0; $i < 3; $i++)
                <div class="col">
                    <div class="hero-placeholder" style="aspect-ratio: 16/9;" aria-hidden="true">Video embed placeholder</div>
                </div>
            @endfor
        </div>
    </section>
</x-layouts.public>
