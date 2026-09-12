<x-layouts.admin :title="$product ? 'Edit Product' : 'New Product'">
    <h1 class="fs-3 fw-semibold">{{ $product ? 'Edit "'.$product->title.'"' : 'New Product' }}</h1>

    <form method="POST"
          action="{{ $product ? route('admin.store.update', $product) : route('admin.store.store') }}"
          class="mt-4" style="max-width: 40rem;">
        @csrf
        @if ($product) @method('PATCH') @endif

        <div class="mb-3">
            <label class="form-label small fw-medium">Title</label>
            <input type="text" name="title" required class="form-control" value="{{ old('title', $product->title ?? '') }}" />
        </div>

        <div class="row g-3">
            <div class="col-sm-6">
                <label class="form-label small fw-medium">Category</label>
                <input type="text" name="category" class="form-control" value="{{ old('category', $product->category ?? '') }}" />
            </div>
            <div class="col-sm-6">
                <label class="form-label small fw-medium">Price (₦)</label>
                <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $product->price ?? '') }}" />
            </div>
        </div>

        <div class="mb-3 mt-3">
            <label class="form-label small fw-medium">Status</label>
            <select name="status" class="form-select" style="max-width: 12rem;">
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected(old('status', $product->status ?? 'Draft') === $status)>{{ $status }}</option>
                @endforeach
            </select>
        </div>

        @include('admin.partials.image-upload', [
            'name' => 'image_url', 'label' => 'Image', 'context' => 'products',
            'value' => old('image_url', $product->image_url ?? null),
        ])

        <div class="mb-3">
            <label class="form-label small fw-medium">Description</label>
            <textarea name="description" rows="3" class="form-control">{{ old('description', $product->description ?? '') }}</textarea>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">{{ $product ? 'Save Changes' : 'Add Product' }}</button>
            <a href="{{ route('admin.store.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</x-layouts.admin>
