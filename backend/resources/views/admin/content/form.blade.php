<x-layouts.admin :title="$post ? 'Edit Post' : 'New Post'">
    <h1 class="fs-3 fw-semibold">{{ $post ? 'Edit "'.$post->title.'"' : 'New Post' }}</h1>

    <form method="POST"
          action="{{ $post ? route('admin.content.update', $post) : route('admin.content.store') }}"
          class="mt-4" style="max-width: 40rem;">
        @csrf
        @if ($post) @method('PATCH') @endif

        <div class="mb-3">
            <label class="form-label small fw-medium">Title</label>
            <input type="text" name="title" required class="form-control" value="{{ old('title', $post->title ?? '') }}" />
        </div>

        @include('admin.partials.image-upload', [
            'name' => 'cover_image', 'label' => 'Cover Image', 'context' => 'news',
            'value' => old('cover_image', $post->cover_image ?? null),
        ])

        <div class="mb-3">
            <label class="form-label small fw-medium">Body</label>
            <textarea name="body" rows="8" class="form-control">{{ old('body', $post->body ?? '') }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label small fw-medium">Status</label>
            <select name="status" class="form-select" style="max-width: 12rem;">
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected(old('status', $post->status ?? 'Draft') === $status)>{{ $status }}</option>
                @endforeach
            </select>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">{{ $post ? 'Save Changes' : 'Add Post' }}</button>
            <a href="{{ route('admin.content.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</x-layouts.admin>
