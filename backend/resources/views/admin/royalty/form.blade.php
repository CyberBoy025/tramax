@php
    $existingLineItems = $statement?->lineItems ?? collect();
@endphp

<x-layouts.admin :title="$statement ? 'Edit Royalty Statement' : 'New Royalty Statement'">
    <h1 class="fs-3 fw-semibold">{{ $statement ? 'Edit Royalty Statement' : 'New Royalty Statement' }}</h1>

    <form method="POST"
          action="{{ $statement ? route('admin.royalty.update', $statement) : route('admin.royalty.store') }}"
          class="mt-4" style="max-width: 44rem;">
        @csrf
        @if ($statement) @method('PATCH') @endif

        <div class="mb-3">
            <label class="form-label small fw-medium">Artist</label>
            <select name="artist_profile_id" required class="form-select">
                <option value="">Select…</option>
                @foreach ($artists as $artist)
                    <option value="{{ $artist->id }}" @selected(old('artist_profile_id', $statement->artist_profile_id ?? null) == $artist->id)>{{ $artist->artist_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="row g-3">
            <div class="col-sm-6">
                <label class="form-label small fw-medium">Period Start</label>
                <input type="date" name="period_start" required class="form-control"
                       value="{{ old('period_start', $statement?->period_start?->format('Y-m-d')) }}" />
            </div>
            <div class="col-sm-6">
                <label class="form-label small fw-medium">Period End</label>
                <input type="date" name="period_end" required class="form-control"
                       value="{{ old('period_end', $statement?->period_end?->format('Y-m-d')) }}" />
            </div>
            <div class="col-sm-4">
                <label class="form-label small fw-medium">Total Revenue (₦)</label>
                <input type="number" step="0.01" name="total_revenue" class="form-control"
                       value="{{ old('total_revenue', $statement->total_revenue ?? '') }}" />
            </div>
            <div class="col-sm-4">
                <label class="form-label small fw-medium">Company Share (₦)</label>
                <input type="number" step="0.01" name="company_share" class="form-control"
                       value="{{ old('company_share', $statement->company_share ?? '') }}" />
            </div>
            <div class="col-sm-4">
                <label class="form-label small fw-medium">Artist Share (₦)</label>
                <input type="number" step="0.01" name="artist_share" class="form-control"
                       value="{{ old('artist_share', $statement->artist_share ?? '') }}" />
            </div>
            <div class="col-sm-4">
                <label class="form-label small fw-medium">Status</label>
                <select name="status" class="form-select">
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" @selected(old('status', $statement->status ?? 'Draft') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <hr class="my-4" style="border-color: var(--color-border-default);" />

        <div class="repeatable-rows" data-next-index="{{ $existingLineItems->count() }}">
            <div class="d-flex align-items-center justify-content-between">
                <p class="small fw-medium mb-2">Line Item Breakdown (optional)</p>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-add-row>+ Add Line Item</button>
            </div>

            <div class="repeatable-rows__items">
                @foreach ($existingLineItems as $i => $item)
                    <div class="repeatable-rows__row d-flex gap-2 align-items-center mb-2">
                        <select name="line_items[{{ $i }}][source]" class="form-select form-select-sm" style="max-width: 10rem;">
                            @foreach ($sources as $source)
                                <option value="{{ $source }}" @selected($item->source === $source)>{{ $source }}</option>
                            @endforeach
                        </select>
                        <input type="number" step="0.01" name="line_items[{{ $i }}][amount]" value="{{ $item->amount }}"
                               placeholder="Amount" class="form-control form-control-sm" style="max-width: 10rem;" />
                        <button type="button" class="btn btn-sm btn-link text-danger" data-remove-row>Remove</button>
                    </div>
                @endforeach
            </div>

            <template>
                <div class="repeatable-rows__row d-flex gap-2 align-items-center mb-2">
                    <select name="line_items[__INDEX__][source]" class="form-select form-select-sm" style="max-width: 10rem;">
                        @foreach ($sources as $source)
                            <option value="{{ $source }}">{{ $source }}</option>
                        @endforeach
                    </select>
                    <input type="number" step="0.01" name="line_items[__INDEX__][amount]"
                           placeholder="Amount" class="form-control form-control-sm" style="max-width: 10rem;" />
                    <button type="button" class="btn btn-sm btn-link text-danger" data-remove-row>Remove</button>
                </div>
            </template>
        </div>

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary">{{ $statement ? 'Save Changes' : 'Add Statement' }}</button>
            <a href="{{ route('admin.royalty.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</x-layouts.admin>
