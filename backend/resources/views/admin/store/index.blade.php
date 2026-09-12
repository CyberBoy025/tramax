@php
    $canWrite = auth()->user()->hasRole(\App\Models\Role::SUPER_ADMIN, \App\Models\Role::CONTENT_MANAGER);
    $canDelete = auth()->user()->hasRole(\App\Models\Role::SUPER_ADMIN);
@endphp

<x-layouts.admin title="Store">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="fs-3 fw-semibold mb-0">Store</h1>
            <p class="mt-1 small mb-0" style="color: var(--color-text-secondary);">Catalogue only — no checkout/orders.</p>
        </div>
        @if ($canWrite)
            <a href="{{ route('admin.store.create') }}" class="btn btn-primary">New Product</a>
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
                    <th>Category</th>
                    <th>Price</th>
                    <th>Status</th>
                    @if ($canWrite)<th></th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td>{{ $product->title }}</td>
                        <td style="color: var(--color-text-secondary);">{{ $product->category ?? '—' }}</td>
                        <td class="font-data" style="color: var(--color-text-secondary);">
                            {{ $product->price ? '₦'.number_format($product->price, 2) : '—' }}
                        </td>
                        <td>
                            <span class="badge-status badge-status--{{ $product->status === 'Published' ? 'success' : 'info' }}">{{ $product->status }}</span>
                        </td>
                        @if ($canWrite)
                            <td class="d-flex gap-3">
                                <a href="{{ route('admin.store.edit', $product) }}" class="small">Edit</a>
                                @if ($canDelete)
                                    <form method="POST" action="{{ route('admin.store.destroy', $product) }}" data-confirm="Delete {{ $product->title }}?">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-link btn-sm p-0 small" style="color: var(--color-state-error);">Delete</button>
                                    </form>
                                @endif
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center py-4" style="color: var(--color-text-muted);">No products yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.admin>
