<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// Read gated to Super Administrator + Management only; write (status
// decisions) gated to Super Administrator only — per discovery.md §3's
// Partnerships row, the narrowest of the modules built so far.
class PartnerAdminController extends Controller
{
    private const STATUSES = ['New', 'In Discussion', 'Active'];

    public function index(Request $request): JsonResponse
    {
        $query = Partner::query()->latest();

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        return response()->json(['data' => $query->get()]);
    }

    public function show(Partner $partner): JsonResponse
    {
        return response()->json(['data' => $partner]);
    }

    public function updateStatus(Request $request, Partner $partner): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:'.implode(',', self::STATUSES)],
        ]);

        $partner->update($data);

        return response()->json(['data' => $partner->fresh()]);
    }

    public function destroy(Partner $partner): JsonResponse
    {
        $partner->delete();

        return response()->json(['data' => ['message' => 'Deleted.']]);
    }
}
