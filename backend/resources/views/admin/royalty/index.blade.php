@php
    $canWrite = auth()->user()->hasRole(\App\Models\Role::SUPER_ADMIN, \App\Models\Role::FINANCE);
    $canDelete = auth()->user()->hasRole(\App\Models\Role::SUPER_ADMIN);
@endphp

<x-layouts.admin title="Royalty & Revenue">
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="fs-3 fw-semibold mb-0">Royalty & Revenue</h1>
        @if ($canWrite)
            <a href="{{ route('admin.royalty.create') }}" class="btn btn-primary">New Statement</a>
        @endif
    </div>

    @if (session('success'))
        <div class="alert alert-success py-2 small mt-3">{{ session('success') }}</div>
    @endif

    <div class="table-responsive mt-4 rounded-3" style="border: 1px solid var(--color-border-default);">
        <table class="data-table mb-0">
            <thead>
                <tr>
                    <th>Artist</th>
                    <th>Period</th>
                    <th>Total Revenue</th>
                    <th>Artist Share</th>
                    <th>Status</th>
                    @if ($canWrite)<th></th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse ($statements as $statement)
                    <tr>
                        <td>{{ $statement->artist->artist_name ?? '—' }}</td>
                        <td style="color: var(--color-text-secondary);">
                            {{ $statement->period_start->format('M j') }} – {{ $statement->period_end->format('M j, Y') }}
                        </td>
                        <td class="font-data" style="color: var(--color-text-secondary);">₦{{ number_format($statement->total_revenue, 2) }}</td>
                        <td class="font-data">₦{{ number_format($statement->artist_share, 2) }}</td>
                        <td>
                            <span class="badge-status badge-status--{{ match($statement->status) {
                                'Paid' => 'success', 'Pending' => 'warning', default => 'info' } }}">
                                {{ $statement->status }}
                            </span>
                        </td>
                        @if ($canWrite)
                            <td class="d-flex gap-3">
                                <a href="{{ route('admin.royalty.edit', $statement) }}" class="small">Edit</a>
                                @if ($canDelete)
                                    <form method="POST" action="{{ route('admin.royalty.destroy', $statement) }}" data-confirm="Delete this royalty statement?">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-link btn-sm p-0 small" style="color: var(--color-state-error);">Delete</button>
                                    </form>
                                @endif
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-4" style="color: var(--color-text-muted);">No royalty statements yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.admin>
