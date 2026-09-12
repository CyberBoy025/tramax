<x-layouts.admin title="Users & Roles">
    <h1 class="fs-3 fw-semibold">Users &amp; Roles</h1>
    <p class="mt-1 small" style="color: var(--color-text-secondary);">
        Staff and artist accounts are provisioned here — there's no public registration flow.
        You can't change your own role, suspend yourself, or delete your own account.
    </p>

    @if (session('success'))
        <div class="alert alert-success py-2 small mt-3">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger py-2 small mt-3">{{ session('error') }}</div>
    @endif

    <div class="d-flex justify-content-end mt-3">
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">New User</a>
    </div>

    <div class="table-responsive mt-3 rounded-3" style="border: 1px solid var(--color-border-default);">
        <table class="data-table mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>
                            {{ $user->name }}
                            @if (auth()->id() === $user->id)
                                <span class="small" style="color: var(--color-text-muted);">(you)</span>
                            @endif
                        </td>
                        <td style="color: var(--color-text-secondary);">{{ $user->email }}</td>
                        <td style="color: var(--color-text-secondary);">{{ $user->role->name ?? '—' }}</td>
                        <td>
                            <span class="badge-status badge-status--{{ $user->status === 'Active' ? 'success' : 'warning' }}">{{ $user->status }}</span>
                        </td>
                        <td class="d-flex gap-3">
                            <a href="{{ route('admin.users.edit', $user) }}" class="small">Edit</a>
                            @if (auth()->id() !== $user->id)
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" data-confirm="Delete {{ $user->name }}?">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-link btn-sm p-0 small" style="color: var(--color-state-error);">Delete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center py-4" style="color: var(--color-text-muted);">No users yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.admin>
