@php
    $statusBadge = ['Published' => 'success', 'Processing' => 'warning', 'Draft' => 'info'];
@endphp

<x-layouts.portal title="My Releases">
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="fs-3 fw-semibold mb-0">My Releases</h1>
        @if ($profile)
            <button type="button" class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#new-release-form">Submit a Release</button>
        @endif
    </div>

    @if (! $profile)
        <div class="alert alert-danger small mt-4" style="max-width: 40rem;">
            Your account isn't linked to a public artist profile yet.
        </div>
    @else
        <p class="small mt-1" style="color: var(--color-text-secondary);">
            Submitted releases come in as Draft — an A&amp;R Manager or Super Admin reviews and
            publishes from there. You can't publish directly.
        </p>

        @if (session('success'))
            <div class="alert alert-success py-2 small">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger py-2 small">{{ session('error') }}</div>
        @endif

        <div class="collapse @if($errors->any()) show @endif" id="new-release-form">
            <form method="POST" action="{{ route('portal.releases.store') }}" class="row g-3 stat-card mt-2" style="max-width: 40rem;">
                @csrf
                <div class="col-sm-6">
                    <label class="form-label small fw-medium">Title</label>
                    <input type="text" name="title" required class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" />
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-sm-6">
                    <label class="form-label small fw-medium">Type</label>
                    <select name="type" required class="form-select">
                        @foreach ($types as $type)
                            <option value="{{ $type }}" @selected(old('type', 'Single') === $type)>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6">
                    <label class="form-label small fw-medium">Release Date</label>
                    <input type="date" name="release_date" class="form-control" value="{{ old('release_date') }}" />
                </div>
                <div class="col-12">
                    @include('portal.partials.image-upload', ['name' => 'cover_art_url', 'label' => 'Cover Art', 'context' => 'releases'])
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Submit for Review</button>
                </div>
            </form>
        </div>

        <div class="table-responsive mt-4 rounded-3" style="border: 1px solid var(--color-border-default);">
            <table class="data-table mb-0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Release Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($releases as $release)
                        <tr>
                            <td>{{ $release->title }}</td>
                            <td style="color: var(--color-text-secondary);">{{ $release->type }}</td>
                            <td style="color: var(--color-text-secondary);">{{ $release->release_date?->format('M j, Y') ?? '—' }}</td>
                            <td>
                                <span class="badge-status badge-status--{{ $statusBadge[$release->status] ?? 'info' }}">{{ $release->status }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-4" style="color: var(--color-text-muted);">No releases yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</x-layouts.portal>
