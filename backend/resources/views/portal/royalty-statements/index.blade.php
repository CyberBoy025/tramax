@php
    $statusBadge = ['Paid' => 'success', 'Pending' => 'warning', 'Draft' => 'info'];
    $naira = fn ($value) => '₦'.number_format($value);
@endphp

<x-layouts.portal title="Royalty & Earnings">
    <h1 class="fs-3 fw-semibold">Royalty &amp; Earnings</h1>

    @if ($statements === null)
        <div class="alert alert-danger small mt-4" style="max-width: 40rem;">
            Your account isn't linked to a public artist profile yet.
        </div>
    @else
        <p class="small mt-1" style="color: var(--color-text-secondary);">
            Read-only — statements are entered by Finance.
        </p>

        @php
            $totalEarnings = $statements->sum('artist_share');
            $totalPaid = $statements->where('status', 'Paid')->sum('artist_share');
        @endphp

        <div class="row row-cols-2 row-cols-sm-3 g-3 mt-1">
            <div class="col">
                <div class="stat-card">
                    <p class="stat-card__value mb-0">{{ $naira($totalEarnings) }}</p>
                    <p class="stat-card__label mb-0">Total earnings (all statements)</p>
                </div>
            </div>
            <div class="col">
                <div class="stat-card">
                    <p class="stat-card__value mb-0">{{ $naira($totalPaid) }}</p>
                    <p class="stat-card__label mb-0">Paid out</p>
                </div>
            </div>
            <div class="col">
                <div class="stat-card">
                    <p class="stat-card__value mb-0">{{ $statements->count() }}</p>
                    <p class="stat-card__label mb-0">Statements</p>
                </div>
            </div>
        </div>

        <div class="table-responsive mt-4 rounded-3" style="border: 1px solid var(--color-border-default);">
            <table class="data-table mb-0">
                <thead>
                    <tr>
                        <th>Period</th>
                        <th>Total Revenue</th>
                        <th>Your Share</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($statements as $statement)
                        <tr>
                            <td>{{ $statement->period_start->format('M j, Y') }} – {{ $statement->period_end->format('M j, Y') }}</td>
                            <td style="color: var(--color-text-secondary);">{{ $naira($statement->total_revenue) }}</td>
                            <td>{{ $naira($statement->artist_share) }}</td>
                            <td>
                                <span class="badge-status badge-status--{{ $statusBadge[$statement->status] ?? 'info' }}">{{ $statement->status }}</span>
                            </td>
                            <td>
                                @if ($statement->lineItems->count())
                                    <button type="button" class="btn btn-link btn-sm p-0 small" data-bs-toggle="collapse" data-bs-target="#statement-{{ $statement->id }}">Breakdown</button>
                                @endif
                            </td>
                        </tr>
                        @if ($statement->lineItems->count())
                            <tr class="collapse" id="statement-{{ $statement->id }}">
                                <td colspan="5" style="background: var(--color-bg-raised);">
                                    <div class="row g-2 small">
                                        @foreach ($statement->lineItems as $item)
                                            <div class="col-sm-4"><strong>{{ $item->source }}:</strong> {{ $naira($item->amount) }}</div>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr><td colspan="5" class="text-center py-4" style="color: var(--color-text-muted);">No royalty statements yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</x-layouts.portal>
