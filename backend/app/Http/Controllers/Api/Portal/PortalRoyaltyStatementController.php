<?php

namespace App\Http\Controllers\Api\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// Royalty Management (discovery.md §3) — Own (read) for the Artist role:
// their own statements and line items, never anyone else's, and no write
// access at all (Manage is Finance-only).
class PortalRoyaltyStatementController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $profile = $request->user()->artistProfile;
        if (! $profile) {
            return response()->json(['data' => []]);
        }

        $statements = $profile->royaltyStatements()
            ->with('lineItems')
            ->orderByDesc('period_end')
            ->get();

        return response()->json(['data' => $statements]);
    }
}
