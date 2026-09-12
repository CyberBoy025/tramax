@php
    $statusBadge = ['Upcoming' => 'info', 'Completed' => 'success', 'Cancelled' => 'error'];
@endphp

<x-layouts.portal title="Bookings">
    <h1 class="fs-3 fw-semibold">Bookings</h1>

    @if ($events === null)
        <div class="alert alert-danger small mt-4" style="max-width: 40rem;">
            Your account isn't linked to a public artist profile yet.
        </div>
    @else
        <p class="small mt-1" style="color: var(--color-text-secondary);">
            Events you're booked on. Read-only — an A&amp;R Manager or Super Admin manages event
            details and lineup.
        </p>

        <div class="table-responsive mt-4 rounded-3" style="border: 1px solid var(--color-border-default);">
            <table class="data-table mb-0">
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Venue</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($events as $event)
                        <tr>
                            <td>{{ $event->title }}</td>
                            <td style="color: var(--color-text-secondary);">{{ collect([$event->venue, $event->city])->filter()->implode(', ') ?: '—' }}</td>
                            <td style="color: var(--color-text-secondary);">{{ $event->event_date?->format('M j, Y') ?? '—' }}</td>
                            <td>
                                <span class="badge-status badge-status--{{ $statusBadge[$event->status] ?? 'info' }}">{{ $event->status }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-4" style="color: var(--color-text-muted);">No bookings yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</x-layouts.portal>
