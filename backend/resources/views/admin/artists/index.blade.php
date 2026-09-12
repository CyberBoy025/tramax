@php
    $canWrite = auth()->user()->hasRole(\App\Models\Role::SUPER_ADMIN, \App\Models\Role::AR_MANAGER);
    $canDelete = auth()->user()->hasRole(\App\Models\Role::SUPER_ADMIN);
@endphp

<x-layouts.admin title="Artists">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="fs-3 fw-semibold mb-0">Artists</h1>
            <p class="mt-1 small mb-0" style="color: var(--color-text-secondary);">
                Music Catalogue module — shows every status, unlike the public site.
            </p>
        </div>
        @if ($canWrite)
            <a href="{{ route('admin.artists.create') }}" class="btn btn-primary">New Artist</a>
        @endif
    </div>

    @if (session('success'))
        <div class="alert alert-success py-2 small mt-3">{{ session('success') }}</div>
    @endif

    <div class="table-responsive mt-4 rounded-3" style="border: 1px solid var(--color-border-default);">
        <table class="data-table mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Genre</th>
                    <th>Status</th>
                    @if ($canWrite)<th></th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse ($artists as $artist)
                    <tr>
                        <td>{{ $artist->artist_name }}</td>
                        <td style="color: var(--color-text-secondary);">{{ $artist->genre ?? '—' }}</td>
                        <td>
                            <span class="badge-status badge-status--{{ $artist->status === 'Active' ? 'success' : 'info' }}">
                                {{ $artist->status }}
                            </span>
                        </td>
                        @if ($canWrite)
                            <td class="d-flex gap-3">
                                <a href="{{ route('admin.artists.edit', $artist) }}" class="small">Edit</a>
                                @if ($canDelete)
                                    <form method="POST" action="{{ route('admin.artists.destroy', $artist) }}"
                                          data-confirm="Delete {{ $artist->artist_name }}? This cannot be undone.">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link btn-sm p-0 small" style="color: var(--color-state-error);">Delete</button>
                                    </form>
                                @endif
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center py-4" style="color: var(--color-text-muted);">No artists yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.admin>
