{{--
    Ports components/ui/card.tsx — generic content card (artist / release /
    event / news / product). Required: $href, $title. Optional: $meta,
    plus a slot for extra content (e.g. the store card's price line).
--}}
@props(['href', 'title', 'meta' => null])

<a href="{{ $href }}" class="content-card">
    <h3 class="content-card__title mb-0">{{ $title }}</h3>
    @if ($meta)
        <p class="content-card__meta mb-0">{{ $meta }}</p>
    @endif
    {{ $slot }}
</a>
