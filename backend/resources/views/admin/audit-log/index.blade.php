@php
    $actions = ['created', 'updated', 'deleted'];
    $actionBadge = ['created' => 'success', 'updated' => 'info', 'deleted' => 'error'];
    $entityLabels = [
        'ArtistProfile' => 'Artist', 'ArtistApplication' => 'Application', 'Release' => 'Release',
        'Event' => 'Event', 'NewsPost' => 'News Post', 'Product' => 'Product',
        'RightsRecord' => 'Rights Record', 'RoyaltyStatement' => 'Royalty Statement',
        'LicensingRequest' => 'Licensing Request', 'Partner' => 'Partner', 'User' => 'User',
    ];
    $activeAction = $filters['action'] ?? '';
    $activeEntity = $filters['entity_type'] ?? '';
@endphp

<x-layouts.admin title="Audit Log">
    <h1 class="fs-3 fw-semibold">Audit Log</h1>
    <p class="mt-1 small" style="color: var(--color-text-secondary);">
        Read-only trail of administrative writes — every create, edit, and delete an admin makes
        through this dashboard. Public-site submissions (applications, licensing requests, partner
        enquiries) aren't logged here since no admin performed them.
    </p>

    <div class="d-flex flex-wrap align-items-center gap-3 mt-3">
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.audit-log.index', ['entity_type' => $activeEntity]) }}"
               class="btn btn-sm rounded-pill {{ $activeAction === '' ? 'btn-outline-light border-2' : 'btn-outline-secondary' }}">All actions</a>
            @foreach ($actions as $action)
                <a href="{{ route('admin.audit-log.index', ['action' => $action, 'entity_type' => $activeEntity]) }}"
                   class="btn btn-sm rounded-pill text-capitalize {{ $activeAction === $action ? 'btn-outline-light border-2' : 'btn-outline-secondary' }}">{{ $action }}</a>
            @endforeach
        </div>

        <form method="GET" action="{{ route('admin.audit-log.index') }}">
            <input type="hidden" name="action" value="{{ $activeAction }}" />
            <select name="entity_type" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All entity types</option>
                @foreach ($entityLabels as $type => $label)
                    <option value="{{ $type }}" @selected($activeEntity === $type)>{{ $label }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="table-responsive mt-4 rounded-3" style="border: 1px solid var(--color-border-default);">
        <table class="data-table mb-0">
            <thead>
                <tr>
                    <th>When</th>
                    <th>Admin</th>
                    <th>Action</th>
                    <th>Entity</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($entries as $entry)
                    <tr>
                        <td style="color: var(--color-text-secondary);">{{ $entry->created_at->format('M j, Y g:i A') }}</td>
                        <td>{{ $entry->user->name ?? '—' }}</td>
                        <td>
                            <span class="badge-status badge-status--{{ $actionBadge[$entry->action] ?? 'info' }} text-capitalize">{{ $entry->action }}</span>
                        </td>
                        <td style="color: var(--color-text-secondary);">
                            {{ $entityLabels[$entry->entity_type] ?? $entry->entity_type }}
                            @if ($entry->entity_id !== null)
                                <span style="color: var(--color-text-muted);">#{{ $entry->entity_id }}</span>
                            @endif
                        </td>
                        <td>
                            @if ($entry->metadata && count($entry->metadata))
                                <button type="button" class="btn btn-link btn-sm p-0 small" data-bs-toggle="collapse" data-bs-target="#entry-{{ $entry->id }}">Details</button>
                            @endif
                        </td>
                    </tr>
                    @if ($entry->metadata && count($entry->metadata))
                        <tr class="collapse" id="entry-{{ $entry->id }}">
                            <td colspan="5" style="background: var(--color-bg-raised);">
                                <div class="row g-2 small">
                                    @foreach ($entry->metadata as $key => $value)
                                        <div class="col-sm-6">
                                            <strong>{{ $key }}:</strong>
                                            {{ is_null($value) ? '—' : (is_array($value) ? json_encode($value) : $value) }}
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr><td colspan="5" class="text-center py-4" style="color: var(--color-text-muted);">No matching audit entries.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.admin>
