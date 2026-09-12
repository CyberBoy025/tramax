@php
    $scopeNote = [
        'full' => 'Full summary across every module.',
        'artist_management' => 'Scoped to Artist Management, Catalogue, Rights, and Events — your functional domain.',
        'finance' => 'Scoped to Royalty and Licensing — your functional domain.',
    ];
    $naira = fn ($value) => '₦'.number_format($value);
@endphp

@php
    $breakdown = function (string $label, array $data) {
        if (empty($data)) {
            return '';
        }
        $chips = collect($data)->map(fn ($count, $key) => "<span class=\"rounded-pill border px-2 py-1\" style=\"border-color: var(--color-border-default); font-size: 0.75rem;\">".e($key).': <span class="font-data">'.e($count).'</span></span>')->implode(' ');

        return '<div class="mt-3"><p class="small fw-medium mb-1" style="color: var(--color-text-secondary);">'.e($label).'</p><div class="d-flex flex-wrap gap-2">'.$chips.'</div></div>';
    };
@endphp

<x-layouts.admin title="Reports & Analytics">
    <h1 class="fs-3 fw-semibold">Reports &amp; Analytics</h1>
    <p class="mt-1 small" style="color: var(--color-text-secondary);">
        {{ $scopeNote[$summary['scope']] ?? '' }} Live counts and sums over existing records — a
        summary view, not a separate reporting system.
    </p>
    <p class="small" style="color: var(--color-text-muted);">
        Generated {{ \Illuminate\Support\Carbon::parse($summary['generated_at'])->format('M j, Y g:i A') }}
    </p>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3 mt-1">
        @if (isset($summary['artists']))
            <div class="col">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-baseline">
                        <p class="fw-semibold mb-0">Artists</p>
                        <p class="stat-card__value mb-0">{{ $summary['artists']['total'] }}</p>
                    </div>
                    {!! $breakdown('By status', $summary['artists']['by_status']) !!}
                </div>
            </div>
        @endif

        @if (isset($summary['applications']))
            <div class="col">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-baseline">
                        <p class="fw-semibold mb-0">Applications</p>
                        <p class="stat-card__value mb-0">{{ $summary['applications']['total'] }}</p>
                    </div>
                    {!! $breakdown('By status', $summary['applications']['by_status']) !!}
                </div>
            </div>
        @endif

        @if (isset($summary['releases']))
            <div class="col">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-baseline">
                        <p class="fw-semibold mb-0">Releases</p>
                        <p class="stat-card__value mb-0">{{ $summary['releases']['total'] }}</p>
                    </div>
                    {!! $breakdown('By status', $summary['releases']['by_status']) !!}
                    {!! $breakdown('By type', $summary['releases']['by_type']) !!}
                </div>
            </div>
        @endif

        @if (isset($summary['rights_records']))
            <div class="col">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-baseline">
                        <p class="fw-semibold mb-0">Rights Records</p>
                        <p class="stat-card__value mb-0">{{ $summary['rights_records']['total'] }}</p>
                    </div>
                    {!! $breakdown('By copyright status', $summary['rights_records']['by_copyright_status']) !!}
                    {!! $breakdown('By licensing status', $summary['rights_records']['by_licensing_status']) !!}
                </div>
            </div>
        @endif

        @if (isset($summary['events']))
            <div class="col">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-baseline">
                        <p class="fw-semibold mb-0">Events</p>
                        <p class="stat-card__value mb-0">{{ $summary['events']['total'] }}</p>
                    </div>
                    {!! $breakdown('By status', $summary['events']['by_status']) !!}
                </div>
            </div>
        @endif

        @if (isset($summary['royalty']))
            <div class="col">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-baseline">
                        <p class="fw-semibold mb-0">Royalty Statements</p>
                        <p class="stat-card__value mb-0">{{ $summary['royalty']['total'] }}</p>
                    </div>
                    <div class="row g-2 text-center mt-2">
                        <div class="col-4">
                            <p class="font-data small fw-semibold mb-0">{{ $naira($summary['royalty']['total_revenue']) }}</p>
                            <p class="small mb-0" style="color: var(--color-text-muted);">Total revenue</p>
                        </div>
                        <div class="col-4">
                            <p class="font-data small fw-semibold mb-0">{{ $naira($summary['royalty']['company_share']) }}</p>
                            <p class="small mb-0" style="color: var(--color-text-muted);">Company share</p>
                        </div>
                        <div class="col-4">
                            <p class="font-data small fw-semibold mb-0">{{ $naira($summary['royalty']['artist_share']) }}</p>
                            <p class="small mb-0" style="color: var(--color-text-muted);">Artist share</p>
                        </div>
                    </div>
                    {!! $breakdown('By status', $summary['royalty']['by_status']) !!}
                </div>
            </div>
        @endif

        @if (isset($summary['licensing_requests']))
            <div class="col">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-baseline">
                        <p class="fw-semibold mb-0">Licensing Requests</p>
                        <p class="stat-card__value mb-0">{{ $summary['licensing_requests']['total'] }}</p>
                    </div>
                    {!! $breakdown('By status', $summary['licensing_requests']['by_status']) !!}
                </div>
            </div>
        @endif

        @if (isset($summary['partners']))
            <div class="col">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-baseline">
                        <p class="fw-semibold mb-0">Partners</p>
                        <p class="stat-card__value mb-0">{{ $summary['partners']['total'] }}</p>
                    </div>
                    {!! $breakdown('By status', $summary['partners']['by_status']) !!}
                </div>
            </div>
        @endif

        @if (isset($summary['products']))
            <div class="col">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-baseline">
                        <p class="fw-semibold mb-0">Store Products</p>
                        <p class="stat-card__value mb-0">{{ $summary['products']['total'] }}</p>
                    </div>
                    {!! $breakdown('By status', $summary['products']['by_status']) !!}
                </div>
            </div>
        @endif

        @if (isset($summary['news']))
            <div class="col">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-baseline">
                        <p class="fw-semibold mb-0">News Posts</p>
                        <p class="stat-card__value mb-0">{{ $summary['news']['total'] }}</p>
                    </div>
                    {!! $breakdown('By status', $summary['news']['by_status']) !!}
                </div>
            </div>
        @endif

        @if (isset($summary['users']))
            <div class="col">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-baseline">
                        <p class="fw-semibold mb-0">Users</p>
                        <p class="stat-card__value mb-0">{{ $summary['users']['total'] }}</p>
                    </div>
                    {!! $breakdown('By role', $summary['users']['by_role']) !!}
                </div>
            </div>
        @endif
    </div>
</x-layouts.admin>
