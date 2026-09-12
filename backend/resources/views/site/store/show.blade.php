<x-layouts.public :title="$product->title">
    <article class="site-container py-6" style="padding-top: 5rem; padding-bottom: 5rem;">
        <x-kicker>Store</x-kicker>
        <h1 class="mt-3 fw-semibold display-5">{{ $product->title }}</h1>
        <p class="small mt-2" style="color: var(--color-text-secondary);">{{ $product->category ?? 'Merchandise' }}</p>
        @if ($product->price)
            <p class="font-data fs-2 fw-semibold mt-3 mb-0">₦{{ number_format($product->price) }}</p>
        @endif
        <p class="mt-4" style="max-width: 56ch; color: var(--color-text-secondary);">
            {{ $product->description ?: 'Catalogue preview — checkout is out of scope for the MVP.' }}
        </p>
    </article>
</x-layouts.public>
