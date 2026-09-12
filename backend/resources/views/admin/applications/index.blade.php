@php
    $canWrite = auth()->user()->hasRole(\App\Models\Role::SUPER_ADMIN, \App\Models\Role::AR_MANAGER);
@endphp

<x-layouts.admin title="Applications">
    <h1 class="fs-3 fw-semibold">Applications</h1>
    <p class="mt-1 small" style="color: var(--color-text-secondary);">
        Artist Management module — {{ $canWrite ? 'you can update status.' : 'read-only for your role.' }}
    </p>

    @if (session('success'))
        <div class="alert alert-success py-2 small mt-3">{{ session('success') }}</div>
    @endif

    <div class="d-flex flex-wrap gap-2 mt-4">
        <a href="{{ route('admin.applications.index') }}"
           class="btn btn-sm rounded-pill {{ $activeStatus === '' ? 'btn-outline-light border-2' : 'btn-outline-secondary' }}">
            All
        </a>
        @foreach ($statuses as $status)
            <a href="{{ route('admin.applications.index', ['status' => $status]) }}"
               class="btn btn-sm rounded-pill {{ $activeStatus === $status ? 'btn-outline-light border-2' : 'btn-outline-secondary' }}">
                {{ $status }}
            </a>
        @endforeach
    </div>

    <div class="table-responsive mt-4 rounded-3" style="border: 1px solid var(--color-border-default);">
        <table class="data-table mb-0">
            <thead>
                <tr>
                    <th>Applicant</th>
                    <th>Artist Name</th>
                    <th>Genre</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($applications as $application)
                    <tr>
                        <td>{{ $application->full_name }}</td>
                        <td>{{ $application->artist_name }}</td>
                        <td style="color: var(--color-text-secondary);">{{ $application->genre ?? '—' }}</td>
                        <td>
                            <span class="badge-status badge-status--{{ match($application->status) {
                                'Accepted' => 'success', 'Shortlisted' => 'info',
                                'Under Review', 'Submitted' => 'warning', 'Rejected' => 'error', default => 'info' } }}">
                                {{ $application->status }}
                            </span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm p-0" data-bs-toggle="collapse"
                                    data-bs-target="#application-{{ $application->id }}">
                                Review
                            </button>
                        </td>
                    </tr>
                    <tr class="collapse" id="application-{{ $application->id }}">
                        <td colspan="5" style="background: var(--color-bg-raised);">
                            <div class="row g-3 small">
                                <div class="col-sm-6"><strong>Email:</strong> {{ $application->email }}</div>
                                <div class="col-sm-6"><strong>Phone:</strong> {{ $application->phone ?? '—' }}</div>
                                <div class="col-sm-6"><strong>Location:</strong> {{ $application->location ?? '—' }}</div>
                                <div class="col-sm-6"><strong>Years Active:</strong> {{ $application->years_active ?? '—' }}</div>
                                @if ($application->biography)
                                    <div class="col-12"><strong>Biography:</strong> {{ $application->biography }}</div>
                                @endif
                                @if ($application->demo_file_url)
                                    <div class="col-12">
                                        <strong>Demo:</strong>
                                        <a href="{{ $application->demo_file_url }}" target="_blank" rel="noopener">Listen</a>
                                    </div>
                                @endif
                            </div>

                            @if ($canWrite)
                                <form method="POST" action="{{ route('admin.applications.status', $application) }}" class="d-flex align-items-center gap-2 mt-3">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="form-select form-select-sm w-auto">
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status }}" @selected($status === $application->status)>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4" style="color: var(--color-text-muted);">
                            No applications{{ $activeStatus ? " with status \"$activeStatus\"" : '' }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.admin>
