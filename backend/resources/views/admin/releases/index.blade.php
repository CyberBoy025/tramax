@php
    $canWrite = auth()->user()->hasRole(\App\Models\Role::SUPER_ADMIN, \App\Models\Role::AR_MANAGER);
    $canDelete = auth()->user()->hasRole(\App\Models\Role::SUPER_ADMIN);
@endphp

<x-layouts.admin title="Releases">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="fs-3 fw-semibold mb-0">Music / Releases</h1>
            <p class="mt-1 small mb-0" style="color: var(--color-text-secondary);">
                Every status shown, including Draft and Processing.
            </p>
        </div>
        @if ($canWrite)
            <a href="{{ route('admin.releases.create') }}" class="btn btn-primary">New Release</a>
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
                    <th>Artist</th>
                    <th>Type</th>
                    <th>Release Date</th>
                    <th>Status</th>
                    @if ($canWrite)<th></th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse ($releases as $release)
                    <tr>
                        <td>{{ $release->title }}</td>
                        <td style="color: var(--color-text-secondary);">{{ $release->artist->artist_name ?? '—' }}</td>
                        <td style="color: var(--color-text-secondary);">{{ $release->type }}</td>
                        <td style="color: var(--color-text-secondary);">
                            {{ $release->release_date?->format('M j, Y') ?? '—' }}
                        </td>
                        <td>
                            <span class="badge-status badge-status--{{ match($release->status) {
                                'Published' => 'success', 'Processing' => 'warning', default => 'info' } }}">
                                {{ $release->status }}
                            </span>
                        </td>
                        @if ($canWrite)
                            <td class="d-flex gap-3">
                                <a href="{{ route('admin.releases.edit', $release) }}" class="small">Edit</a>
                                @if ($canDelete)
                                    <form method="POST" action="{{ route('admin.releases.destroy', $release) }}"
                                          data-confirm="Delete {{ $release->title }}? This cannot be undone.">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link btn-sm p-0 small" style="color: var(--color-state-error);">Delete</button>
                                    </form>
                                @endif
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-4" style="color: var(--color-text-muted);">No releases yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.admin>
