<x-layouts.admin :title="$artist ? 'Edit Artist' : 'New Artist'">
    <h1 class="fs-3 fw-semibold">{{ $artist ? 'Edit "'.$artist->artist_name.'"' : 'New Artist' }}</h1>

    <form method="POST"
          action="{{ $artist ? route('admin.artists.update', $artist) : route('admin.artists.store') }}"
          class="mt-4" style="max-width: 40rem;">
        @csrf
        @if ($artist) @method('PATCH') @endif

        <div class="mb-3">
            <label class="form-label small fw-medium">Artist Name</label>
            <input type="text" name="artist_name" required class="form-control"
                   value="{{ old('artist_name', $artist->artist_name ?? '') }}" />
            @error('artist_name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label small fw-medium">Genre</label>
            <input type="text" name="genre" class="form-control" value="{{ old('genre', $artist->genre ?? '') }}" />
        </div>

        <div class="mb-3">
            <label class="form-label small fw-medium">Biography</label>
            <textarea name="biography" rows="4" class="form-control">{{ old('biography', $artist->biography ?? '') }}</textarea>
        </div>

        @include('admin.partials.image-upload', [
            'name' => 'photo_url', 'label' => 'Photo', 'context' => 'artists',
            'value' => old('photo_url', $artist->photo_url ?? null),
        ])

        <div class="mb-3">
            <label class="form-label small fw-medium">Status</label>
            <select name="status" class="form-select" style="max-width: 12rem;">
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected(old('status', $artist->status ?? 'Active') === $status)>{{ $status }}</option>
                @endforeach
            </select>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">{{ $artist ? 'Save Changes' : 'Add Artist' }}</button>
            <a href="{{ route('admin.artists.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</x-layouts.admin>
