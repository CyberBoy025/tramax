@php
    $canWrite = auth()->user()->hasRole(\App\Models\Role::SUPER_ADMIN);
@endphp

<x-layouts.admin title="Licensing Requests">
    <h1 class="fs-3 fw-semibold">Licensing Requests</h1>
    <p class="mt-1 small" style="color: var(--color-text-secondary);">
        Inbound requests from filmmakers, advertisers, and media companies. Only Super
        Administrator can change status — everyone else here has Read only.
    </p>

    @if (session('success'))
        <div class="alert alert-success py-2 small mt-3">{{ session('success') }}</div>
    @endif

    <div class="d-flex flex-wrap gap-2 mt-3">
        <a href="{{ route('admin.licensing.index') }}"
           class="btn btn-sm rounded-pill {{ $activeStatus === '' ? 'btn-outline-light border-2' : 'btn-outline-secondary' }}">All</a>
        @foreach ($statuses as $status)
            <a href="{{ route('admin.licensing.index', ['status' => $status]) }}"
               class="btn btn-sm rounded-pill {{ $activeStatus === $status ? 'btn-outline-light border-2' : 'btn-outline-secondary' }}">{{ $status }}</a>
        @endforeach
    </div>

    <div class="table-responsive mt-4 rounded-3" style="border: 1px solid var(--color-border-default);">
        <table class="data-table mb-0">
            <thead>
                <tr>
                    <th>Company</th>
                    <th>Contact</th>
                    <th>Project Type</th>
                    <th>Received</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($requests as $req)
                    <tr>
                        <td>{{ $req->company_name }}</td>
                        <td style="color: var(--color-text-secondary);">{{ $req->contact_person }}</td>
                        <td style="color: var(--color-text-secondary);">{{ $req->project_type ?? '—' }}</td>
                        <td style="color: var(--color-text-secondary);">{{ $req->created_at->format('M j, Y') }}</td>
                        <td>
                            <span class="badge-status badge-status--{{ match($req->status) {
                                'Approved' => 'success', 'In Review' => 'warning', 'Declined' => 'error', default => 'info' } }}">
                                {{ $req->status }}
                            </span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm p-0" data-bs-toggle="collapse" data-bs-target="#licensing-{{ $req->id }}">Review</button>
                        </td>
                    </tr>
                    <tr class="collapse" id="licensing-{{ $req->id }}">
                        <td colspan="6" style="background: var(--color-bg-raised);">
                            <div class="row g-3 small">
                                <div class="col-sm-6"><strong>Email:</strong> {{ $req->email }}</div>
                                <div class="col-sm-6"><strong>Music Required:</strong> {{ $req->music_required ?? '—' }}</div>
                                <div class="col-sm-6"><strong>Usage:</strong> {{ $req->usage ?? '—' }}</div>
                                <div class="col-sm-6"><strong>Duration:</strong> {{ $req->duration ?? '—' }}</div>
                                <div class="col-sm-6"><strong>Territory:</strong> {{ $req->territory ?? '—' }}</div>
                                <div class="col-sm-6"><strong>Budget:</strong> {{ $req->budget ?? '—' }}</div>
                                @if ($req->release)
                                    <div class="col-12"><strong>Related Release:</strong> {{ $req->release->title }}</div>
                                @endif
                                @if ($req->message)
                                    <div class="col-12"><strong>Message:</strong> {{ $req->message }}</div>
                                @endif
                            </div>

                            @if ($canWrite)
                                <form method="POST" action="{{ route('admin.licensing.status', $req) }}" class="d-flex align-items-center gap-2 mt-3">
                                    @csrf @method('PATCH')
                                    <select name="status" class="form-select form-select-sm w-auto">
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status }}" @selected($status === $req->status)>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-4" style="color: var(--color-text-muted);">No licensing requests{{ $activeStatus ? " with status \"$activeStatus\"" : '' }}.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.admin>
