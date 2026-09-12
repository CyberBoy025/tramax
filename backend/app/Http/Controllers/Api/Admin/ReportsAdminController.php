<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportsSummaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// Read-only aggregate summaries (README.md §4 "Analytics — release/artist/
// revenue/activity summaries for admin dashboard") — counts and sums over
// the existing module tables, not a new entity. discovery.md §3 gives A&R
// and Finance "Read (own scope)": since neither role is tied to a subset of
// records the way an Artist is to their own artist_profile, "own scope"
// here means their own functional domain — the modules they already have
// Manage/Full on elsewhere in the matrix. Super Admin and Management get
// everything.
//
// Aggregation logic lives in ReportsSummaryService so the Blade admin
// report (Web\ReportController) builds identical numbers from one place.
class ReportsAdminController extends Controller
{
    public function __construct(private ReportsSummaryService $reports) {}

    public function summary(Request $request): JsonResponse
    {
        return response()->json(['data' => $this->reports->buildFor($request->user())]);
    }
}
