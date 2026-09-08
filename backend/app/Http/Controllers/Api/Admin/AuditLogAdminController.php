<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLogEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// Read-only (discovery.md §3/§4.4) — entries are written automatically by
// AuditLogObserver, never through this controller.
class AuditLogAdminController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = AuditLogEntry::query()->with('user:id,name,email')->latest('id');

        if ($request->filled('action')) {
            $query->where('action', $request->string('action'));
        }
        if ($request->filled('entity_type')) {
            $query->where('entity_type', $request->string('entity_type'));
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }

        return response()->json(['data' => $query->limit(200)->get()]);
    }
}
