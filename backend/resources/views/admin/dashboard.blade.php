<x-layouts.admin title="Dashboard">
    <h1 class="fs-3 fw-semibold">Operational Summary</h1>
    <p class="mt-1 small" style="color: var(--color-text-secondary);">{{ $note ?: "Your role doesn't have summary data configured for this dashboard yet." }}</p>

    @if (count($stats))
        <div class="row row-cols-2 row-cols-sm-4 g-3 mt-1">
            @foreach ($stats as $stat)
                <div class="col">
                    <div class="stat-card">
                        <p class="stat-card__value mb-0">{{ $stat['value'] }}</p>
                        <p class="stat-card__label mb-0">{{ $stat['label'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-layouts.admin>
