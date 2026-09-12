@php
    $linkedIds = $event?->artists->pluck('id')->all() ?? [];
@endphp

<x-layouts.admin :title="$event ? 'Edit Event' : 'New Event'">
    <h1 class="fs-3 fw-semibold">{{ $event ? 'Edit "'.$event->title.'"' : 'New Event' }}</h1>

    <form method="POST"
          action="{{ $event ? route('admin.events.update', $event) : route('admin.events.store') }}"
          class="mt-4" style="max-width: 40rem;">
        @csrf
        @if ($event) @method('PATCH') @endif

        <div class="mb-3">
            <label class="form-label small fw-medium">Title</label>
            <input type="text" name="title" required class="form-control" value="{{ old('title', $event->title ?? '') }}" />
        </div>

        <div class="row g-3">
            <div class="col-sm-6">
                <label class="form-label small fw-medium">Venue</label>
                <input type="text" name="venue" class="form-control" value="{{ old('venue', $event->venue ?? '') }}" />
            </div>
            <div class="col-sm-6">
                <label class="form-label small fw-medium">City</label>
                <input type="text" name="city" class="form-control" value="{{ old('city', $event->city ?? '') }}" />
            </div>
            <div class="col-sm-6">
                <label class="form-label small fw-medium">Event Date</label>
                <input type="datetime-local" name="event_date" class="form-control"
                       value="{{ old('event_date', $event?->event_date?->format('Y-m-d\TH:i')) }}" />
            </div>
            <div class="col-sm-6">
                <label class="form-label small fw-medium">Status</label>
                <select name="status" class="form-select">
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" @selected(old('status', $event->status ?? 'Upcoming') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-3 mt-3">
            <label class="form-label small fw-medium">Ticket Link</label>
            <input type="text" name="ticket_link" class="form-control" value="{{ old('ticket_link', $event->ticket_link ?? '') }}" />
        </div>

        <div class="mb-3">
            <label class="form-label small fw-medium">Description</label>
            <textarea name="description" rows="3" class="form-control">{{ old('description', $event->description ?? '') }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label small fw-medium">Artists</label>
            <select name="artist_profile_ids[]" multiple class="form-select" size="6">
                @foreach ($artists as $artist)
                    <option value="{{ $artist->id }}" @selected(in_array($artist->id, old('artist_profile_ids', $linkedIds)))>{{ $artist->artist_name }}</option>
                @endforeach
            </select>
            <p class="small mt-1" style="color: var(--color-text-muted);">Ctrl/Cmd-click to select multiple.</p>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">{{ $event ? 'Save Changes' : 'Add Event' }}</button>
            <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</x-layouts.admin>
