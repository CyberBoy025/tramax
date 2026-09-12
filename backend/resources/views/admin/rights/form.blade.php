<x-layouts.admin :title="$right ? 'Edit Rights Record' : 'New Rights Record'">
    <h1 class="fs-3 fw-semibold">{{ $right ? 'Edit Rights Record' : 'New Rights Record' }}</h1>

    <form method="POST"
          action="{{ $right ? route('admin.rights.update', $right) : route('admin.rights.store') }}"
          class="mt-4" style="max-width: 40rem;">
        @csrf
        @if ($right) @method('PATCH') @endif

        <div class="mb-3">
            <label class="form-label small fw-medium">Release</label>
            <select name="release_id" class="form-select">
                <option value="">— None —</option>
                @foreach ($releases as $release)
                    <option value="{{ $release->id }}" @selected(old('release_id', $right->release_id ?? null) == $release->id)>{{ $release->title }}</option>
                @endforeach
            </select>
        </div>

        <div class="row g-3">
            <div class="col-sm-6">
                <label class="form-label small fw-medium">Master Owner</label>
                <input type="text" name="master_owner" class="form-control" value="{{ old('master_owner', $right->master_owner ?? '') }}" />
            </div>
            <div class="col-sm-6">
                <label class="form-label small fw-medium">Publishing Owner</label>
                <input type="text" name="publishing_owner" class="form-control" value="{{ old('publishing_owner', $right->publishing_owner ?? '') }}" />
            </div>
            <div class="col-sm-6">
                <label class="form-label small fw-medium">Songwriter</label>
                <input type="text" name="songwriter" class="form-control" value="{{ old('songwriter', $right->songwriter ?? '') }}" />
            </div>
            <div class="col-sm-6">
                <label class="form-label small fw-medium">Producer</label>
                <input type="text" name="producer" class="form-control" value="{{ old('producer', $right->producer ?? '') }}" />
            </div>
            <div class="col-sm-6">
                <label class="form-label small fw-medium">Copyright Status</label>
                <select name="copyright_status" class="form-select">
                    @foreach ($copyrightStatuses as $status)
                        <option value="{{ $status }}" @selected(old('copyright_status', $right->copyright_status ?? 'Active') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6">
                <label class="form-label small fw-medium">Licensing Status</label>
                <select name="licensing_status" class="form-select">
                    @foreach ($licensingStatuses as $status)
                        <option value="{{ $status }}" @selected(old('licensing_status', $right->licensing_status ?? 'Unlicensed') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary">{{ $right ? 'Save Changes' : 'Add Rights Record' }}</button>
            <a href="{{ route('admin.rights.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</x-layouts.admin>
