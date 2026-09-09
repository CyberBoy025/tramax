<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// README.md §4 "Media upload pipeline — optimised storage, CDN-ready (§8)".
// Shared by both /admin/uploads and /portal/uploads (routes/api.php) — the
// operation (validate, store, return a URL) is identical either side; only
// who's allowed to call it and which context they may upload into differs,
// enforced here by role rather than duplicated per route.
//
// Stores on the "public" disk (config/filesystems.php) — local disk today,
// but swapping FILESYSTEM_DISK to "s3" in production (the config block
// already exists, unconfigured) moves every upload behind a CDN with no
// code change here, since nothing above this controller ever sees a raw
// filesystem path, only the URL this returns.
class MediaUploadController extends Controller
{
    private const CONTEXTS = ['artists', 'releases', 'products', 'news'];

    private const CONTEXTS_BY_ROLE = [
        Role::SUPER_ADMIN => self::CONTEXTS,
        Role::AR_MANAGER => ['artists', 'releases'],
        Role::CONTENT_MANAGER => ['products', 'news'],
        Role::ARTIST => ['artists', 'releases'],
    ];

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'file' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'context' => ['required', 'string', 'in:'.implode(',', self::CONTEXTS)],
        ]);

        $allowed = self::CONTEXTS_BY_ROLE[$request->user()->role->name] ?? [];
        if (! in_array($data['context'], $allowed, true)) {
            return response()->json(['message' => 'Your role can\'t upload media for this section.'], 403);
        }

        $path = $request->file('file')->store($data['context'], 'public');

        return response()->json(['data' => [
            'url' => Storage::disk('public')->url($path),
            'path' => $path,
        ]], 201);
    }
}
