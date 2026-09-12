<x-layouts.portal title="My Profile">
    <h1 class="fs-3 fw-semibold">My Profile</h1>

    @if (! $profile)
        <div class="alert alert-danger small mt-4" style="max-width: 40rem;">
            Your account isn't linked to a public artist profile yet — an admin links one when
            your application is accepted.
        </div>
    @else
        <div class="d-flex align-items-center justify-content-between" style="max-width: 40rem;">
            <p class="small mb-0" style="color: var(--color-text-secondary);">
                {{ $profile->artist_name }} · tramax.com/artists/{{ $profile->slug }}. Name, URL, and
                status are set by an admin — everything else here is yours to edit.
            </p>
            <span class="badge-status badge-status--{{ $profile->status === 'Active' ? 'success' : 'info' }}">{{ $profile->status }}</span>
        </div>

        @if (session('success'))
            <div class="alert alert-success py-2 small mt-3" style="max-width: 40rem;">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger py-2 small mt-3" style="max-width: 40rem;">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('portal.profile.update') }}" class="mt-4" style="max-width: 40rem;">
            @csrf @method('PATCH')

            <div class="mb-3">
                <label class="form-label small fw-medium">Genre</label>
                <input type="text" name="genre" class="form-control" value="{{ old('genre', $profile->genre ?? '') }}" />
            </div>

            @include('portal.partials.image-upload', [
                'name' => 'photo_url', 'label' => 'Photo', 'context' => 'artists',
                'value' => old('photo_url', $profile->photo_url ?? null),
            ])

            <div class="mb-3">
                <label class="form-label small fw-medium">Biography</label>
                <textarea name="biography" rows="5" class="form-control">{{ old('biography', $profile->biography ?? '') }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    @endif
</x-layouts.portal>
