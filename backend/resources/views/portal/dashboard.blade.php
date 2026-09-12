<x-layouts.portal title="Dashboard">
    <h1 class="fs-3 fw-semibold">Welcome back{{ $profile ? ', '.$profile->artist_name : '' }}</h1>

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

    <div class="stat-card mt-4">
        <div class="d-flex align-items-center justify-content-between">
            <p class="fw-semibold mb-0">Linked Artist Profile</p>
            @if ($profile)
                <span class="badge-status badge-status--success">{{ $profile->artist_name }}</span>
            @else
                <span class="badge-status badge-status--info">Not linked</span>
            @endif
        </div>
        <p class="small mt-2 mb-0" style="color: var(--color-text-secondary);">
            @if ($profile)
                Releases submitted from My Releases appear here as Draft until an admin publishes them.
            @else
                This account isn't linked to a public artist profile yet — an admin links one when your application is accepted.
            @endif
        </p>
    </div>
</x-layouts.portal>
