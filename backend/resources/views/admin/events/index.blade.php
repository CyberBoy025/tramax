@php
    $canWrite = auth()->user()->hasRole(\App\Models\Role::SUPER_ADMIN, \App\Models\Role::AR_MANAGER);
    $canDelete = auth()->user()->hasRole(\App\Models\Role::SUPER_ADMIN);
@endphp

<x-layouts.admin title="Events">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="fs-3 fw-semibold mb-0">Events</h1>
            <p class="mt-1 small mb-0" style="color: var(--color-text-secondary);">Every status shown, including Cancelled.</p>
        </div>
        @if ($canWrite)
            <a href="{{ route('admin.events.create') }}" class="btn btn-primary">New Event</a>
        @endif
    </div>

    @if (session('success'))
        <div class="alert alert-success py-2 small mt-3">{{ session('success') }}</div>
    @endif

    <div class="table-responsive mt-4 rounded-3" style="border: 1px solid var(--color-border-default);">
        <table class="data-table mb-0">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Venue</th>
                    <th>Date</th>
                    <th>Artists</th>
                    <th>Status</th>
                    @if ($canWrite)<th></th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse ($events as $event)
                    <tr>
                        <td>{{ $event->title }}</td>
                        <td style="color: var(--color-text-secondary);">{{ $event->venue ?? '—' }}</td>
                        <td style="color: var(--color-text-secondary);">
                            {{ $event->event_date?->format('M j, Y') ?? '—' }}
                        </td>
                        <td style="color: var(--color-text-secondary);">
                            {{ $event->artists->pluck('artist_name')->join(', ') ?: '—' }}
                        </td>
                        <td>
                            <span class="badge-status badge-status--{{ match($event->status) {
                                'Upcoming' => 'info', 'Completed' => 'success', 'Cancelled' => 'error', default => 'info' } }}">
                                {{ $event->status }}
                            </span>
                        </td>
                        @if ($canWrite)
                            <td class="d-flex gap-3">
                                <a href="{{ route('admin.events.edit', $event) }}" class="small">Edit</a>
                                @if ($canDelete)
                                    <form method="POST" action="{{ route('admin.events.destroy', $event) }}" data-confirm="Delete {{ $event->title }}?">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-link btn-sm p-0 small" style="color: var(--color-state-error);">Delete</button>
                                    </form>
                                @endif
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-4" style="color: var(--color-text-muted);">No events yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.admin>
