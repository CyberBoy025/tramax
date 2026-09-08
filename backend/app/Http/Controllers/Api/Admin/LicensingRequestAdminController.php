<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\LicensingRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// Read gated to Super Administrator, Management, A&R, Finance; write
// (status decisions) gated to Super Administrator only — per discovery.md
// §3's Licensing Requests row, where no other role has "Manage", only Read.
class LicensingRequestAdminController extends Controller
{
    private const STATUSES = ['New', 'In Review', 'Approved', 'Declined'];

    public function index(Request $request): JsonResponse
    {
        $query = LicensingRequest::query()->with('release:id,title,slug')->latest();

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        return response()->json(['data' => $query->get()]);
    }

    public function show(LicensingRequest $licensingRequest): JsonResponse
    {
        return response()->json(['data' => $licensingRequest->load('release:id,title,slug')]);
    }

    public function updateStatus(Request $request, LicensingRequest $licensingRequest): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:'.implode(',', self::STATUSES)],
            'related_release_id' => ['nullable', 'exists:releases,id'],
        ]);

        $licensingRequest->update($data);

        return response()->json(['data' => $licensingRequest->fresh()->load('release:id,title,slug')]);
    }

    public function destroy(LicensingRequest $licensingRequest): JsonResponse
    {
        $licensingRequest->delete();

        return response()->json(['data' => ['message' => 'Deleted.']]);
    }
}
