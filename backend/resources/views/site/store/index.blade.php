<x-layouts.public title="Store">
    <section class="site-container py-6" style="padding-top: 5rem; padding-bottom: 5rem;">
        <x-kicker>Store</x-kicker>
        <h1 class="mt-3 fw-semibold display-5">Merchandise</h1>
        <p class="mt-3" style="max-width: 56ch; color: var(--color-text-secondary);">
            Catalogue preview — checkout is out of scope for the MVP unless confirmed separately.
        </p>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4 mt-2">
            @forelse ($products as $product)
                <div class="col">
                    <x-card :href="'/store/'.$product->slug" :title="$product->title" :meta="$product->category">
                        @if ($product->price)
                            <p class="font-data small mt-2 mb-0" style="color: var(--color-text-secondary);">₦{{ number_format($product->price) }}</p>
                        @endif
                    </x-card>
                </div>
            @empty
                <p class="small" style="color: var(--color-text-muted);">No products published yet.</p>
            @endforelse
        </div>
    </section>
</x-layouts.public>
