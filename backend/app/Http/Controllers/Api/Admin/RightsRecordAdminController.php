<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\RightsRecord;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// Read gated to Super Administrator, Management, A&R, Finance; write gated to
// Super Administrator + A&R; delete further restricted to Super Administrator
// — per discovery.md §3's Rights Management row (Full / Read / Manage / Read).
class RightsRecordAdminController extends Controller
{
    private const STATUSES_COPYRIGHT = ['Active', 'Disputed', 'Expired'];

    private const STATUSES_LICENSING = ['Unlicensed', 'Licensed', 'Exclusive'];

    public function index(Request $request): JsonResponse
    {
        $query = RightsRecord::query()->with(['release:id,title,slug', 'track:id,title'])->latest();

        if ($releaseId = $request->query('release_id')) {
            $query->where('release_id', $releaseId);
        }

        return response()->json(['data' => $query->get()]);
    }

    public function show(RightsRecord $right): JsonResponse
    {
        return response()->json(['data' => $right->load(['release:id,title,slug', 'track:id,title'])]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);
        $right = RightsRecord::create($data);

        return response()->json(['data' => $right], 201);
    }

    public function update(Request $request, RightsRecord $right): JsonResponse
    {
        $data = $this->validated($request, sometimes: true);
        $right->update($data);

        return response()->json(['data' => $right->fresh()]);
    }

    public function destroy(Request $request, RightsRecord $right): JsonResponse
    {
        if (! $request->user()->hasRole(Role::SUPER_ADMIN)) {
            return response()->json(['message' => 'Only a Super Administrator can delete a rights record.'], 403);
        }

        $right->delete();

        return response()->json(['data' => ['message' => 'Deleted.']]);
    }

    private function validated(Request $request, bool $sometimes = false): array
    {
        $rule = fn (string $r) => $sometimes ? ['sometimes', $r] : ['nullable', $r];

        return $request->validate([
            'release_id' => ['nullable', 'exists:releases,id'],
            'track_id' => ['nullable', 'exists:tracks,id'],
            'master_owner' => [...$rule('string'), 'max:255'],
            'publishing_owner' => [...$rule('string'), 'max:255'],
            'songwriter' => [...$rule('string'), 'max:255'],
            'producer' => [...$rule('string'), 'max:255'],
            'copyright_status' => ['nullable', 'string', 'in:'.implode(',', self::STATUSES_COPYRIGHT)],
            'licensing_status' => ['nullable', 'string', 'in:'.implode(',', self::STATUSES_LICENSING)],
        ]);
    }
}
