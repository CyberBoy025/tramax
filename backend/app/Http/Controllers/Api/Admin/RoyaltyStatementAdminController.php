<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\RoyaltyStatement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Read gated to Super Administrator, Management, A&R, Finance; write gated to
// Super Administrator + Finance; delete further restricted to Super
// Administrator — per discovery.md §3's Royalty Management row.
// total_revenue/company_share/artist_share are entered directly by Finance,
// not derived from line items — per README.md §14, this module is records
// and statements, not a calculation engine.
class RoyaltyStatementAdminController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = RoyaltyStatement::query()->with('artist:id,artist_name,slug')->latest('period_start');

        if ($artistId = $request->query('artist_profile_id')) {
            $query->where('artist_profile_id', $artistId);
        }

        return response()->json(['data' => $query->get()]);
    }

    public function show(RoyaltyStatement $statement): JsonResponse
    {
        return response()->json(['data' => $statement->load('artist:id,artist_name,slug', 'lineItems')]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);

        $statement = DB::transaction(function () use ($data) {
            $statement = RoyaltyStatement::create($data['statement']);
            if (! empty($data['line_items'] ?? null)) {
                $statement->lineItems()->createMany($data['line_items']);
            }

            return $statement;
        });

        return response()->json(['data' => $statement->load('lineItems')], 201);
    }

    public function update(Request $request, RoyaltyStatement $statement): JsonResponse
    {
        $data = $this->validated($request, sometimes: true);

        DB::transaction(function () use ($statement, $data) {
            $statement->update($data['statement']);
            // Line items are replaced wholesale when provided — simplest
            // correct behaviour for a statement that's still Draft/Pending;
            // fine-grained per-item edits aren't a requirement here.
            if (array_key_exists('line_items', $data)) {
                $statement->lineItems()->delete();
                $statement->lineItems()->createMany($data['line_items']);
            }
        });

        return response()->json(['data' => $statement->fresh()->load('lineItems')]);
    }

    public function destroy(Request $request, RoyaltyStatement $statement): JsonResponse
    {
        if (! $request->user()->hasRole(Role::SUPER_ADMIN)) {
            return response()->json(['message' => 'Only a Super Administrator can delete a royalty statement.'], 403);
        }

        $statement->delete();

        return response()->json(['data' => ['message' => 'Deleted.']]);
    }

    private function validated(Request $request, bool $sometimes = false): array
    {
        $rule = fn (string $r) => $sometimes ? ['sometimes', $r] : ['required', $r];

        $validated = $request->validate([
            'artist_profile_id' => [...$rule('integer'), 'exists:artist_profiles,id'],
            'period_start' => [...$rule('date')],
            'period_end' => [...$rule('date'), 'after_or_equal:period_start'],
            'total_revenue' => ['nullable', 'numeric', 'min:0'],
            'company_share' => ['nullable', 'numeric', 'min:0'],
            'artist_share' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'string', 'in:Draft,Pending,Paid'],
            'line_items' => ['sometimes', 'array'],
            'line_items.*.source' => ['required_with:line_items', 'string', 'in:Streaming,Publishing,Licensing,Other'],
            'line_items.*.amount' => ['required_with:line_items', 'numeric', 'min:0'],
        ]);

        $lineItems = $validated['line_items'] ?? null;
        unset($validated['line_items']);

        return array_filter([
            'statement' => $validated,
            'line_items' => $lineItems,
        ], fn ($v) => $v !== null);
    }
}
