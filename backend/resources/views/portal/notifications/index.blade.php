<x-layouts.portal title="Notifications">
    <h1 class="fs-3 fw-semibold">Notifications</h1>

    <div class="d-flex flex-column gap-3 mt-4" style="max-width: 40rem;">
        @forelse ($notifications as $notification)
            <div class="stat-card @if(!$notification->read_at) stat-card--unread @endif">
                <div class="d-flex align-items-start justify-content-between gap-3">
                    <div>
                        <p class="fw-semibold mb-0">{{ $notification->title }}</p>
                        @if ($notification->body)
                            <p class="small mt-1 mb-0" style="color: var(--color-text-secondary);">{{ $notification->body }}</p>
                        @endif
                        <p class="small mt-2 mb-0" style="color: var(--color-text-muted);">{{ $notification->created_at->format('M j, Y g:i A') }}</p>
                    </div>
                    @if (! $notification->read_at)
                        <form method="POST" action="{{ route('portal.notifications.read', $notification) }}" class="flex-shrink-0">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-link btn-sm p-0 small">Mark as read</button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <p class="small" style="color: var(--color-text-muted);">No notifications yet.</p>
        @endforelse
    </div>
</x-layouts.portal>
