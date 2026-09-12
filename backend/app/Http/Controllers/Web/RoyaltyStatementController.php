<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ArtistProfile;
use App\Models\Role;
use App\Models\RoyaltyStatement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

// Blade equivalent of Api\Admin\RoyaltyStatementAdminController — same
// transaction-wrapped create/update-replaces-line-items logic, same
// blank-money-field-becomes-0 coalescing (see the original's comment on
// ConvertEmptyStringsToNull).
class RoyaltyStatementController extends Controller
{
    private const STATUSES = ['Draft', 'Pending', 'Paid'];

    private const LINE_ITEM_SOURCES = ['Streaming', 'Publishing', 'Licensing', 'Other'];

    public function index(): View
    {
        return view('admin.royalty.index', [
            'statements' => RoyaltyStatement::query()->with('artist:id,artist_name')->latest('period_start')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.royalty.form', [
            'statement' => null,
            'artists' => ArtistProfile::query()->orderBy('artist_name')->get(['id', 'artist_name']),
            'statuses' => self::STATUSES,
            'sources' => self::LINE_ITEM_SOURCES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($data) {
            $statement = RoyaltyStatement::create($data['statement']);
            if (! empty($data['line_items'] ?? null)) {
                $statement->lineItems()->createMany($data['line_items']);
            }
        });

        return redirect()->route('admin.royalty.index')->with('success', 'Royalty statement created.');
    }

    public function edit(RoyaltyStatement $statement): View
    {
        return view('admin.royalty.form', [
            'statement' => $statement->load('lineItems'),
            'artists' => ArtistProfile::query()->orderBy('artist_name')->get(['id', 'artist_name']),
            'statuses' => self::STATUSES,
            'sources' => self::LINE_ITEM_SOURCES,
        ]);
    }

    public function update(Request $request, RoyaltyStatement $statement): RedirectResponse
    {
        $data = $this->validated($request, sometimes: true);

        DB::transaction(function () use ($statement, $data) {
            $statement->update($data['statement']);
            if (array_key_exists('line_items', $data)) {
                $statement->lineItems()->delete();
                $statement->lineItems()->createMany($data['line_items']);
            }
        });

        return redirect()->route('admin.royalty.index')->with('success', 'Royalty statement updated.');
    }

    public function destroy(Request $request, RoyaltyStatement $statement): RedirectResponse
    {
        if (! $request->user()->hasRole(Role::SUPER_ADMIN)) {
            abort(403, 'Only a Super Administrator can delete a royalty statement.');
        }

        $statement->delete();

        return redirect()->route('admin.royalty.index')->with('success', 'Royalty statement deleted.');
    }

    private function validated(Request $request, bool $sometimes = false): array
    {
        $rule = fn (string $r) => $sometimes ? ['sometimes', $r] : ['required', $r];

        // Drop blank rows (the JS "add a line item" template, or one added
        // then left empty) before validating — required_with would
        // otherwise reject the whole submission over an empty row nobody
        // meant to fill in.
        $request->merge([
            'line_items' => array_values(array_filter(
                $request->input('line_items', []),
                fn ($row) => filled($row['source'] ?? null)
            )),
        ]);

        $validated = $request->validate([
            'artist_profile_id' => [...$rule('integer'), 'exists:artist_profiles,id'],
            'period_start' => [...$rule('date')],
            'period_end' => [...$rule('date'), 'after_or_equal:period_start'],
            'total_revenue' => ['nullable', 'numeric', 'min:0'],
            'company_share' => ['nullable', 'numeric', 'min:0'],
            'artist_share' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'string', 'in:'.implode(',', self::STATUSES)],
            'line_items' => ['sometimes', 'array'],
            'line_items.*.source' => ['required_with:line_items', 'string', 'in:'.implode(',', self::LINE_ITEM_SOURCES)],
            'line_items.*.amount' => ['required_with:line_items', 'numeric', 'min:0'],
        ]);

        $lineItems = $validated['line_items'] ?? [];
        unset($validated['line_items']);

        foreach (['total_revenue', 'company_share', 'artist_share'] as $moneyField) {
            if (array_key_exists($moneyField, $validated) && $validated[$moneyField] === null) {
                $validated[$moneyField] = 0;
            }
        }

        return array_filter([
            'statement' => $validated,
            'line_items' => $lineItems,
        ], fn ($v) => $v !== null);
    }
}
