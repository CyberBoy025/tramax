<x-layouts.admin :title="$release ? 'Edit Release' : 'New Release'">
    <h1 class="fs-3 fw-semibold">{{ $release ? 'Edit "'.$release->title.'"' : 'New Release' }}</h1>

    <form method="POST"
          action="{{ $release ? route('admin.releases.update', $release) : route('admin.releases.store') }}"
          class="mt-4" style="max-width: 40rem;">
        @csrf
        @if ($release) @method('PATCH') @endif

        @unless ($release)
            <div class="mb-3">
                <label class="form-label small fw-medium">Artist</label>
                <select name="artist_profile_id" required class="form-select">
                    <option value="">Select…</option>
                    @foreach ($artists as $artist)
                        <option value="{{ $artist->id }}" @selected(old('artist_profile_id') == $artist->id)>{{ $artist->artist_name }}</option>
                    @endforeach
                </select>
            </div>
        @endunless

        <div class="mb-3">
            <label class="form-label small fw-medium">Title</label>
            <input type="text" name="title" required class="form-control" value="{{ old('title', $release->title ?? '') }}" />
            @error('title') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label small fw-medium">Type</label>
            <select name="type" class="form-select" style="max-width: 12rem;">
                @foreach ($types as $type)
                    <option value="{{ $type }}" @selected(old('type', $release->type ?? 'Single') === $type)>{{ $type }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label small fw-medium">Release Date</label>
            <input type="date" name="release_date" class="form-control" style="max-width: 12rem;"
                   value="{{ old('release_date', $release?->release_date?->format('Y-m-d')) }}" />
        </div>

        <div class="mb-3">
            <label class="form-label small fw-medium">Status</label>
            <select name="status" class="form-select" style="max-width: 12rem;">
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected(old('status', $release->status ?? 'Draft') === $status)>{{ $status }}</option>
                @endforeach
            </select>
        </div>

        @include('admin.partials.image-upload', [
            'name' => 'cover_art_url', 'label' => 'Cover Art', 'context' => 'releases',
            'value' => old('cover_art_url', $release->cover_art_url ?? null),
        ])

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">{{ $release ? 'Save Changes' : 'Add Release' }}</button>
            <a href="{{ route('admin.releases.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</x-layouts.admin>
