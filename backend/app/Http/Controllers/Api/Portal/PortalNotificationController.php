<?php

namespace App\Http\Controllers\Api\Portal;

use App\Http\Controllers\Controller;
use App\Models\PortalNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PortalNotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()->portalNotifications()->orderByDesc('created_at')->get();

        return response()->json(['data' => $notifications]);
    }

    public function markRead(Request $request, PortalNotification $notification): JsonResponse
    {
        if ($notification->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        $notification->update(['read_at' => now()]);

        return response()->json(['data' => $notification->fresh()]);
    }
}
