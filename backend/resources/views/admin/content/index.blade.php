@php
    $canWrite = auth()->user()->hasRole(\App\Models\Role::SUPER_ADMIN, \App\Models\Role::CONTENT_MANAGER);
    $canDelete = auth()->user()->hasRole(\App\Models\Role::SUPER_ADMIN);
@endphp

<x-layouts.admin title="Content">
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="fs-3 fw-semibold mb-0">News / Pages / Media</h1>
        @if ($canWrite)
            <a href="{{ route('admin.content.create') }}" class="btn btn-primary">New Post</a>
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
                    <th>Status</th>
                    <th>Published</th>
                    @if ($canWrite)<th></th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse ($posts as $post)
                    <tr>
                        <td>{{ $post->title }}</td>
                        <td>
                            <span class="badge-status badge-status--{{ $post->status === 'Published' ? 'success' : 'info' }}">{{ $post->status }}</span>
                        </td>
                        <td style="color: var(--color-text-secondary);">{{ $post->published_at?->format('M j, Y') ?? '—' }}</td>
                        @if ($canWrite)
                            <td class="d-flex gap-3">
                                <a href="{{ route('admin.content.edit', $post) }}" class="small">Edit</a>
                                @if ($canDelete)
                                    <form method="POST" action="{{ route('admin.content.destroy', $post) }}" data-confirm="Delete {{ $post->title }}?">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-link btn-sm p-0 small" style="color: var(--color-state-error);">Delete</button>
                                    </form>
                                @endif
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center py-4" style="color: var(--color-text-muted);">No posts yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.admin>
