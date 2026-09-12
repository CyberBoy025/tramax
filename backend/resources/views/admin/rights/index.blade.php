@php
    $canWrite = auth()->user()->hasRole(\App\Models\Role::SUPER_ADMIN, \App\Models\Role::AR_MANAGER);
    $canDelete = auth()->user()->hasRole(\App\Models\Role::SUPER_ADMIN);
@endphp

<x-layouts.admin title="Rights & Catalogue">
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="fs-3 fw-semibold mb-0">Rights & Catalogue</h1>
        @if ($canWrite)
            <a href="{{ route('admin.rights.create') }}" class="btn btn-primary">New Rights Record</a>
        @endif
    </div>

    @if (session('success'))
        <div class="alert alert-success py-2 small mt-3">{{ session('success') }}</div>
    @endif

    <div class="table-responsive mt-4 rounded-3" style="border: 1px solid var(--color-border-default);">
        <table class="data-table mb-0">
            <thead>
                <tr>
                    <th>Release</th>
                    <th>Master Owner</th>
                    <th>Publishing Owner</th>
                    <th>Copyright</th>
                    <th>Licensing</th>
                    @if ($canWrite)<th></th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse ($rights as $right)
                    <tr>
                        <td>{{ $right->release->title ?? '—' }}</td>
                        <td style="color: var(--color-text-secondary);">{{ $right->master_owner ?? '—' }}</td>
                        <td style="color: var(--color-text-secondary);">{{ $right->publishing_owner ?? '—' }}</td>
                        <td>
                            <span class="badge-status badge-status--{{ match($right->copyright_status) {
                                'Active' => 'success', 'Disputed' => 'error', default => 'info' } }}">
                                {{ $right->copyright_status }}
                            </span>
                        </td>
                        <td>
                            <span class="badge-status badge-status--{{ $right->licensing_status === 'Unlicensed' ? 'info' : 'success' }}">
                                {{ $right->licensing_status }}
                            </span>
                        </td>
                        @if ($canWrite)
                            <td class="d-flex gap-3">
                                <a href="{{ route('admin.rights.edit', $right) }}" class="small">Edit</a>
                                @if ($canDelete)
                                    <form method="POST" action="{{ route('admin.rights.destroy', $right) }}" data-confirm="Delete this rights record?">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-link btn-sm p-0 small" style="color: var(--color-state-error);">Delete</button>
                                    </form>
                                @endif
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-4" style="color: var(--color-text-muted);">No rights records yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.admin>
